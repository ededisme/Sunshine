<?php

class ReportsController extends AppController {

    var $uses = array('ChartAccount','Patient');
    var $components = array('Helper', 'Address', 'ProductCom');

    /**
     * Global Inventory
     */
    function product() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id')),array('table' => 'locations', 'type' => 'inner', 'conditions' => array('locations.location_group_id=LocationGroup.id'))), 'conditions' => array('LocationGroup.is_active = 1', 'user_location_groups.user_id=' . $user['User']['id'])));
        $locations = ClassRegistry::init('Location')->find('all', array('conditions' => array('Location.is_active = 1 AND Location.location_group_id IN (SELECT location_group_id FROM user_location_groups WHERE user_id = '.$user['User']['id'].')'), 'order' => 'Location.name'));
        $this->set(compact('locationGroups', 'locations'));
    }

    function productResult() {
        $this->layout = 'ajax';
    }

    function productAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }
    
    /**
     * Inventory Activity
     */
    function inventoryActivity() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id')),array('table' => 'locations', 'type' => 'inner', 'conditions' => array('locations.location_group_id=LocationGroup.id'))),'conditions' => array('LocationGroup.is_active = 1', 'LocationGroup.location_group_type_id != 1', 'user_location_groups.user_id=' . $user['User']['id'])));
        $locations = ClassRegistry::init('Location')->find('all', array('conditions' => array('Location.is_active = 1 AND Location.location_group_id IN (SELECT location_group_id FROM user_location_groups WHERE user_id = '.$user['User']['id'].')'), 'order' => 'Location.name'));
        $this->set(compact('locationGroups', 'locations'));
    }

    function inventoryActivityParentResult() {
        $this->layout = 'ajax';
    }

    function inventoryActivityResult() {
        $this->layout = 'ajax';
    }
    
    function inventoryActivityWithGlobalResult() {
        $this->layout = 'ajax';
    }

    function inventoryActivityWithGlobalDetailResult() {
        $this->layout = 'ajax';
    }

    function inventoryActivityDetailResult() {
        $this->layout = 'ajax';
    }
    
    /**
     * Inventory Activity (Customer Consignment)
     */
    function inventoryConsignment() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $this->set('dateRange', $this->dateRange());
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('fields' => array('LocationGroup.id', 'customers.name'), 'joins' => array(array('table' => 'customers', 'type' => 'inner', 'conditions' => array('customers.id=LocationGroup.customer_id')), array('table' => 'customer_companies', 'type' => 'inner', 'conditions' => array('customer_companies.customer_id=customers.id', 'customer_companies.company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')'))), 'conditions' => array('LocationGroup.is_active = 1', 'LocationGroup.customer_id > 0')));
        $this->set(compact('locationGroups'));
    }

    function inventoryConsignmentParentResult() {
        $this->layout = 'ajax';
    }

    function inventoryConsignmentResult() {
        $this->layout = 'ajax';
    }
    
    function inventoryConsignmentWithGlobalResult() {
        $this->layout = 'ajax';
    }

    function inventoryConsignmentWithGlobalDetailResult() {
        $this->layout = 'ajax';
    }

    function inventoryConsignmentDetailResult() {
        $this->layout = 'ajax';
    }

    function productByTypeResult() {
        $this->layout = 'ajax';
    }

    function productByTypeAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }

    function productViewQtyDetail($status = null ,$id = null, $date = null, $location_id = null) {
        $user = $this->getCurrentUser();
        $conditionLocation = "";
        $conditionDate     = "date < '".$date."'";
        if(!empty($location_id)){
            $conditionLocation = "user_locations.location_id = ".$location_id;
        }
        if($status == 2){
            $conditionDate     = "date <= '".$date."'";
        }
        $product = ClassRegistry::init('Product')->find('first',array('conditions'=>array('Product.id'=>$id)));
        $uom     = ClassRegistry::init('Uom')->find('first',array('conditions'=>array('Uom.id'=>$product['Product']['price_uom_id'])));
        // Smallest Uom
        $query=mysql_query("SELECT id,name,abbr,1 AS conversion FROM uoms WHERE id=".$product['Product']['price_uom_id']."
                            UNION
                            SELECT id,name,abbr,(SELECT value FROM uom_conversions WHERE is_active=1 AND from_uom_id=".$product['Product']['price_uom_id']." AND to_uom_id=uoms.id) AS conversion FROM uoms WHERE id IN (SELECT to_uom_id FROM uom_conversions WHERE is_active=1 AND from_uom_id=".$product['Product']['price_uom_id'].")
                            ORDER BY conversion ASC");
        $small_label = "";
        $small_uom = 1;
        while($r=mysql_fetch_array($query)){
            $small_label = $r['abbr'];
            $small_uom = floatval($r['conversion']);
        }
        $locations = ClassRegistry::init('Location')->find('list', array('joins' => array(array('table' => 'user_locations', 'type' => 'inner', 'conditions' => array('user_locations.location_id=Location.id', $conditionLocation))), 'conditions' => array('user_locations.user_id=' . $user['User']['id'] . ' AND Location.is_active=1'), 'order' => 'Location.name'));
        $html = "<table class='table'><tr><th class='first'>" . TABLE_LOCATION . "</th><th>" . TABLE_QTY . "</th></tr>";
        foreach ($locations as $key => $value) {
            if ($date != date("Y-m-d")) {
                $query = mysql_query('  SELECT SUM((total_pb + total_cm + total_to_in + total_cycle) - (total_so + total_pbc + total_pos + total_to_out)) AS total_qty
                                        FROM '.$key.'_inventory_total_details
                                        WHERE product_id="' . $id . '"
                                            AND location_id="' . $key . '"
                                            AND '.$conditionDate);
            } else {
                $query = mysql_query('  SELECT SUM(total_qty)
                                        FROM '.$key.'_inventory_totals
                                        WHERE product_id="' . $id . '"');
            }
            $data = mysql_fetch_array($query);
            $html .= "<tr><td class='first' style='white-space: nowrap;width: 80%;'>" . $value . "</td><td style='text-align: right;'>" . ($data[0] != 0 ? $this->Helper->showTotalQty($data[0], $uom['Uom']['name'], $small_uom, $small_label) : '-') . "</td></tr>";
        }
        $html .= "</table>";
        echo $html;
        exit();
    }

    /**
     * Inventory Valuation
     */
    function inventoryValuation() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $pro = ClassRegistry::init('Product')->find('all', array('fields' => array('Product.code', 'Product.name', 'Product.id'), 'conditions' => array('Product.is_active = 1' , 'Product.company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')'), 'order' => 'Product.code'));
        $products = Set::combine($pro, '{n}.Product.id', array('{0} - {1}', '{n}.Product.code', '{n}.Product.name'));
        $this->set(compact('companies', 'products'));
    }

    function inventoryValuationResult() {
        $this->layout = 'ajax';
    }

    function inventoryValuationDetailResult() {
        $this->layout = 'ajax';
    }

    /**
     * Inventory Adjustment
     */
    function inventoryAdjustment() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))),'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('companies', 'branches', 'locationGroups', 'users'));
    }

    function inventoryAdjustmentResult() {
        $this->layout = 'ajax';
    }

    function inventoryAdjustmentAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }

    /**
     * Inventory Adjustment By Item
     */
    function inventoryAdjustmentByItem() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list',array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))),'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('companies', 'branches', 'locationGroups', 'users'));
    }

    function inventoryAdjustmentByItemParentResult() {
        $this->layout = 'ajax';
    }

    function inventoryAdjustmentByItemResult() {
        $this->layout = 'ajax';
    }

    function inventoryAdjustmentByItemDetailResult() {
        $this->layout = 'ajax';
    }

    /**
     * Product Aging
     */
    function productAging() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        $pgroups = ClassRegistry::init('Pgroup')->find('list', array('order' => 'id', 'conditions' => array('Pgroup.is_active' => 1, 'Pgroup.id IN (SELECT pgroup_id FROM pgroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].'))')));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1', 'id IN (SELECT created_by FROM products WHERE is_active = 1)'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('companies', 'locations', 'pgroups', 'users'));
    }

    function productAgingResult() {
        $this->layout = 'ajax';
    }

    function productAgingAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }

    /**
     * Product Average Cost
     */
    function productAverageCost() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        $pgroups = ClassRegistry::init('Pgroup')->find('list', array('order' => 'id', 'conditions' => array('Pgroup.is_active' => 1, 'Pgroup.id IN (SELECT pgroup_id FROM pgroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].'))')));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1', 'id IN (SELECT created_by FROM products WHERE is_active = 1)'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('companies', 'locations', 'pgroups', 'users'));
    }

    function productAverageCostResult() {
        $this->layout = 'ajax';
    }

    function productAverageCostAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }

    /**
     * Product Price List
     */
    function productPrice() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))),'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $pgroups = ClassRegistry::init('Pgroup')->find('list', array('order' => 'id', 'conditions' => array('Pgroup.is_active' => 1, 'Pgroup.id IN (SELECT pgroup_id FROM pgroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].'))')));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1', 'id IN (SELECT created_by FROM products WHERE is_active = 1)'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('companies', 'branches', 'locations', 'pgroups', 'users'));
    }

    function productPriceResult() {
        $this->layout = 'ajax';
    }

    function productPriceAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }

    /**
     * Transfer Order
     */
    function transferOrder() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))),'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('locationGroups', 'companies', 'branches', 'users'));
    }

    function transferOrderResult() {
        $this->layout = 'ajax';
    }

    function transferOrderAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }

    /**
     * Transfer By Item
     */
    function transferByItem() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))),'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('companies', 'branches', 'locationGroups', 'users'));
    }

    function transferByItemParentResult() {
        $this->layout = 'ajax';
    }

    function transferByItemResult() {
        $this->layout = 'ajax';
    }

    function transferByItemDetailResult() {
        $this->layout = 'ajax';
    }
    
    /**
     * Request Stock
     */
    function requestStock() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))),'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('locationGroups', 'companies', 'branches', 'users'));
    }

    function requestStockResult() {
        $this->layout = 'ajax';
    }

    function requestStockAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }
    
    /**
     * Request By Item
     */
    function requestByItem() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))),'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('companies', 'branches', 'locationGroups', 'users'));
    }

    function requestByItemParentResult() {
        $this->layout = 'ajax';
    }

    function requestByItemResult() {
        $this->layout = 'ajax';
    }

    function requestByItemDetailResult() {
        $this->layout = 'ajax';
    }

    /**
     * Sales By Item (POS)
     */
    function posByItem() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('companies', 'locationGroups', 'users'));
    }

    function posByItemParentResult() {
        $this->layout = 'ajax';
    }

    function posByItemResult() {
        $this->layout = 'ajax';
    }

    function posByItemDetailResult() {
        $this->layout = 'ajax';
    }
    
    /**
     * Customer Quotation
     */ 
    function customerQuotation() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $users = ClassRegistry::init('User')->find('list',array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('companies', 'branches', 'locationGroups', 'users'));
    }

    function customerQuotationResult() {
        $this->layout = 'ajax';
    }

    function customerQuotationAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }
    
    function customerQuotationProductResult() {
        $this->layout = 'ajax';
    }

    function customerQuotationProductAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }
    
    function customerQuotationProductSummaryResult() {
        $this->layout = 'ajax';
    }

    function customerQuotationProductSummaryAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }
    
    /**
     * Customer Sales Order
     */   
    function customerSaleOrder() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $users = ClassRegistry::init('User')->find('list',array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('companies', 'branches', 'locationGroups', 'users'));
    }

    function customerSaleOrderResult() {
        $this->layout = 'ajax';
    }

    function customerSaleOrderAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }
    
    function customerSaleOrderProductResult() {
        $this->layout = 'ajax';
    }

    function customerSaleOrderProductAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }
    
    function customerSaleOrderProductSummaryResult() {
        $this->layout = 'ajax';
    }

    function customerSaleOrderProductSummaryAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }
    
    /**
     * Sales By Item Type (POS)
     */
    function posByType() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('companies', 'locationGroups', 'users'));
    }

    function posByTypeParentResult() {
        $this->layout = 'ajax';
    }

    function posByTypeResult() {
        $this->layout = 'ajax';
    }

    function posByTypeDetailResult() {
        $this->layout = 'ajax';
    }

    /**
     * Sales By Item
     */
    function salesByItem() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('companies', 'branches', 'locationGroups', 'users'));
    }

    function salesByItemParentResult() {
        $this->layout = 'ajax';
    }

    function salesByItemResult() {
        $this->layout = 'ajax';
    }

    function salesByItemDetailResult() {
        $this->layout = 'ajax';
    }

    /**
     * Sales By Item Type
     */
    function salesByType() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('companies', 'locationGroups', 'users'));
    }

    function salesByTypeParentResult() {
        $this->layout = 'ajax';
    }

    function salesByTypeResult() {
        $this->layout = 'ajax';
    }

    function salesByTypeDetailResult() {
        $this->layout = 'ajax';
    }

    /**
     * Sales By Customer
     */
    function salesByCustomer() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('companies', 'branches', 'locationGroups', 'users'));
    }

    function salesByCustomerResult() {
        $this->layout = 'ajax';
    }

    function salesByCustomerDetailResult() {
        $this->layout = 'ajax';
    }

    /**
     * Sales By Rep
     */
    function salesByRep() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('companies', 'branches', 'locationGroups', 'users'));
    }

    function salesByRepResult() {
        $this->layout = 'ajax';
    }

    function salesByRepDetailResult() {
        $this->layout = 'ajax';
    }

    /**
     * Invoice (SO+POS)
     */
    function invoice() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('companies', 'branches', 'locationGroups', 'users'));
    }

    function invoiceResult() {
        $this->layout = 'ajax';
    }

    function invoiceAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }

    /**
     * Invoice (POS)
     */
    function pos() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))),'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('companies', 'branches', 'locationGroups', 'users'));
    }

    function posResult() {
        $this->layout = 'ajax';
    }

    function posAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }

    /**
     * Invoice (Sales Order)
     */
    function customerInvoice() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('companies', 'branches', 'locationGroups', 'users'));
    }

    function customerInvoiceResult() {
        $this->layout = 'ajax';
    }

    function customerInvoiceAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }

    /**
     * Invoice By Rep (Sales Order)
     */
    function customerInvoiceByRep($data = null) {
        $this->layout = 'ajax';
        if (isset($_POST['action']) && $_POST['action'] == 'export') {
            $user = $this->getCurrentUser();
            $data = explode(",", $data);

            // Function
            include('includes/function.php');

            /* Customize condition */
            $condition = 'status!=-1 AND is_pos=0';
            if ($data[1] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= '"' . dateConvert(str_replace("|||", "/", $data[1])) . '" <= DATE(order_date)';
            }
            if ($data[2] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= '"' . dateConvert(str_replace("|||", "/", $data[2])) . '" >= DATE(order_date)';
            }
            $condition != '' ? $condition .= ' AND ' : '';
            if ($data[3] == '') {
                $condition .= 'status!=0';
            } else {
                $condition .= 'status=' . $data[3];
            }
            if ($data[4] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= 'company_id=' . $data[4];
            } else {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= 'company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')';
            }
            if ($data[5] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= 'branch_id=' . $data[5];
            }else{
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= 'branch_id IN (SELECT branch_id FROM user_branches WHERE user_id = '.$user['User']['id'].')';
            }
            if ($data[6] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= 'location_group_id=' . $data[6];
            }
            if ($data[7] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= 'customer_id IN (SELECT customer_id FROM customer_cgroups WHERE cgroup_id=' . $data[7] . ')';
            }
            if ($data[8] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= 'customer_id=' . $data[8];
            }
            if ($data[9] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= 'sales_orders.created_by=' . $data[9];
            }

            $filename = "public/report/customer_invoice_by_rep.csv";
            $fp = fopen($filename, "wb");
            $excelContent = MENU_REPORT_SALES_ORDER_BY_REP_INVOICE . "\n\n";
            $excelContent .= TABLE_NO . "\t" . SALES_ORDER_DATE . "\t" . TABLE_INVOICE_CODE . "\t" . TABLE_MEMO . "\t" . PRICING_RULE_CUSTOMER . "\t" . GENERAL_AGING . "\t" . TABLE_TOTAL_AMOUNT . "\t" . GENERAL_BALANCE . "\t" . TABLE_STATUS;
            $query = mysql_query('  SELECT
                                        (SELECT name FROM cgroups WHERE id=(SELECT cgroup_id FROM customer_cgroups WHERE customer_id=sales_orders.customer_id AND cgroup_id IN (SELECT id FROM cgroups WHERE is_active=1) LIMIT 1)) AS rep_name,
                                        sales_orders.order_date,
                                        sales_orders.so_code,
                                        sales_orders.memo,
                                        CONCAT_WS(" ", customers.customer_code, " - ", customers.firstname, customers.lastname),
                                        IF(balance>0 AND DATEDIFF(now(),due_date)>0,DATEDIFF(now(),due_date),"."),
                                        sales_orders.total_amount-IFNULL(sales_orders.discount,0)+IFNULL(sales_orders.mark_up,0),
                                        sales_orders.balance,
                                        CASE sales_orders.status WHEN 0 THEN "Void" WHEN 1 THEN "Issued" WHEN 2 THEN "Fulfilled" END
                                    FROM sales_orders INNER JOIN customers ON customers.id = sales_orders.customer_id
                                    WHERE ' . $condition . '
                                    ORDER BY rep_name,sales_orders.order_date') or die(mysql_error());
            $index = 1;
            $tmpName = '';
            $amount = 0;
            $balance = 0;
            $amountTotal = 0;
            $balanceTotal = 0;
            while ($data = mysql_fetch_array($query)) {
                if ($index != 1 && $data[0] != $tmpName) {
                    $index = 1;
                    $excelContent .= "\n" . 'Total ' . $tmpName . "\t\t\t\t\t\t" . number_format($amount, 2) . "\t" . number_format($balance, 2);
                }
                if ($data[0] == $tmpName) {
                    $amount += $data[6];
                    $balance += $data[7];
                } else {
                    $amount = $data[6];
                    $balance = $data[7];
                }
                if ($tmpName != $data[0]) {
                    $tmpName = $data[0];
                    $excelContent .= "\n" . $tmpName;
                    $index = 1;
                }
                $amountTotal += $data[6];
                $balanceTotal += $data[7];
                $excelContent .= "\n" . $index++ . "\t" . trim($data[1]) . "\t" . trim($data[2]) . "\t" . trim($data[3]) . "\t" . trim($data[4]) . "\t" . trim($data[5]) . "\t" . number_format($data[6], 2) . "\t" . number_format($data[7], 2) . "\t" . trim($data[8]);
            }
            if (mysql_num_rows($query)) {
                $excelContent .= "\n" . 'Total ' . $tmpName . "\t\t\t\t\t\t" . number_format($amount, 2) . "\t" . number_format($balance, 2);
                $excelContent .= "\n" . 'GRAND TOTAL' . "\t\t\t\t\t\t" . number_format($amountTotal, 2) . "\t" . number_format($balanceTotal, 2);
            }
            $excelContent = chr(255) . chr(254) . @mb_convert_encoding($excelContent, 'UTF-16LE', 'UTF-8');
            fwrite($fp, $excelContent);
            fclose($fp);
            exit();
        } else {
            $this->set('dateRange', $this->dateRange());
            $user = $this->getCurrentUser();
            $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
            $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
            $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
            $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
            $this->set(compact('companies', 'branches', 'locationGroups', 'users'));
        }
    }

    function customerInvoiceByRepResult() {
        $this->layout = 'ajax';
    }

    function customerInvoiceByRepAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }

    /**
     * Invoice (Credit Memo)
     */
    function customerInvoiceCredit() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('companies', 'locationGroups', 'users', 'branches'));
    }

    function customerInvoiceCreditResult() {
        $this->layout = 'ajax';
    }

    function customerInvoiceCreditAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }

    /**
     * Open Invoice By Rep (SO+CM)
     */
    function openInvoiceByRep() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('companies', 'branches', 'locationGroups', 'users'));
    }

    function openInvoiceByRepResult() {
        $this->layout = 'ajax';
        $data = str_replace("/", "|||", implode(',', $_POST));
        $data = explode(",", $data);
        $this->set("data", $data);
    }

    /**
     * Discount Summary
     */
    function customerDiscount() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $this->set(compact('companies', 'branches'));
    }

    function customerDiscountResult() {
        $this->layout = 'ajax';
    }

    function customerDiscountAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }

    /**
     * Delivery
     */
    function delivery() {
        $this->layout = 'ajax';
        if (isset($_POST['action']) && $_POST['action'] == 'export') {
            $user = $this->getCurrentUser();
            $data = explode(",", $data);

            // Function
            include('includes/function.php');

            /* Customize condition */
            $condition = "is_active=1";
            if ($data[1] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= '"' . dateConvert(str_replace("|||", "/", $data[1])) . '" <= DATE(date)';
            }
            if ($data[2] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= '"' . dateConvert(str_replace("|||", "/", $data[2])) . '" >= DATE(date)';
            }
            $condition != '' ? $condition .= ' AND ' : '';
            if ($data[3] == '') {
                $condition .= 'status!=0';
            } else {
                $condition .= 'status=' . $data[3];
            }
            if ($data[4] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= 'company_id = '.$data[4];
            } else {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= 'company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')';
            }
            if ($data[5] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= 'branch_id = '.$data[5];
            } else {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= 'branch_id IN (SELECT branch_id FROM user_branches WHERE user_id = '.$user['User']['id'].')';
            }
            if ($data[6] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= 'warehouse_id = '. $data[6];
            }
            if ($data[7] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= '(SELECT customer_id FROM sales_orders WHERE delivery_id=deliveries.id) IN (SELECT customer_id FROM customer_cgroups WHERE cgroup_id=' . $data[7] . ')';
            }
            if ($data[8] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= $data[8] . ' IN (SELECT customer_id FROM sales_orders WHERE delivery_id=deliveries.id)';
            }
            if ($data[9] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= 'created_by=' . $data[9];
            }

            $filename = "public/report/delivery.csv";
            $fp = fopen($filename, "wb");
            $excelContent = MENU_DELIVERY_MANAGEMENT . "\n\n";
            $excelContent .= TABLE_NO . "\t" . TABLE_CODE . "\t" . TABLE_CUSTOMER_GROUP . "\t" . TABLE_INVOICE_CODE . "\t" . TABLE_DN_DATE . "\t" . TABLE_CREATED . "\t" . TABLE_STATUS;
            $query = mysql_query('  SELECT
                                        id,
                                        code,
                                        note,
                                        (SELECT CONCAT_WS(" ",first_name,last_name) FROM users WHERE id=deliveries.created_by),
                                        date,
                                        created,
                                        status
                                    FROM deliveries
                                    WHERE ' . $condition) or die(mysql_error());
            $index = 1;
            while ($data = mysql_fetch_array($query)) {
                // customer group
                $customerGp = "";
                $j = 1;
                $sql = mysql_query("SELECT so.customer_id as customer_id FROM sales_orders as so INNER JOIN customer_cgroups as cc ON cc.customer_id = so.customer_id INNER JOIN cgroups as cg ON cg.id = cc.cgroup_id WHERE so.delivery_id = " . $data[0] . " AND so.status > 0 GROUP BY cc.cgroup_id");
                $sizeOfSale = mysql_num_rows($sql);
                while (@$r = mysql_fetch_array($sql)) {
                    $j++;
                    if ($j > 2 && $j > $sizeOfSale) {
                        $customerGp .= ", ";
                    }
                    $s = mysql_query("SELECT cg.name as name FROM customer_cgroups as ccg INNER JOIN cgroups as cg ON cg.id = ccg.cgroup_id WHERE ccg.customer_id = " . $r['customer_id'] . " LIMIT 1");
                    while (@$rp = mysql_fetch_array($s)) {
                        $customerGp .= $rp['name'];
                    }
                }

                // so code
                $saleOrderCodeArr = array();
                $sql = mysql_query("SELECT so_code FROM sales_orders WHERE delivery_id =" . $data[0] . " AND status > 0 GROUP BY so_code");
                while (@$r = mysql_fetch_array($sql)) {
                    $saleOrderCodeArr[] = $r['so_code'];
                }
                $saleOrderCode = implode(", ", $saleOrderCodeArr);

                $excelContent .= "\n" . $index++ . "\t" . trim($data[1]) . "\t" . $customerGp . "\t" . $saleOrderCode . "\t" . trim($data[4]) . "\t" . trim($data[5]) . "\t" . ($data[6] == 1 ? 'Issued' : 'Fulfilled');
            }
            $excelContent = chr(255) . chr(254) . @mb_convert_encoding($excelContent, 'UTF-16LE', 'UTF-8');
            fwrite($fp, $excelContent);
            fclose($fp);
            exit();
        } else {
            $this->set('dateRange', $this->dateRange());
            $user = $this->getCurrentUser();
            $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
            $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
            $locations = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
            $users = ClassRegistry::init('User')->find('all', array('conditions' => array('is_active = 1'), 'order' => 'username'));
            $this->set(compact('companies', 'branches', 'locations', 'users'));
        }
    }

    function deliveryResult() {
        $this->layout = 'ajax';
    }

    function deliveryAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }

    /**
     * Receive Payments Report
     */
    function customerReceivePayment() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $this->set(compact("companies", "branches"));
    }

    function customerReceivePaymentResult() {
        $this->layout = 'ajax';
    }

    function customerReceivePaymentAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);

        $user = $this->getCurrentUser();
        $this->set('userId', $user['User']['id']);
        $companies = ClassRegistry::init('Company')->find('list',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        $this->set(compact("companies"));
    }

    /**
     * Receive Payments By Rep Report
     */
    function customerReceivePaymentByRep() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $customerGroups = ClassRegistry::init('Cgroup')->find("list", array("conditions" => array("Cgroup.is_active = 1", "Cgroup.id IN (SELECT cgroup_id FROM cgroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))")));
        $cus = ClassRegistry::init('Customer')->find('all', array('fields' => array('customer_code', 'name', 'id'), 'conditions' => array('Customer.is_active = 1', "Customer.id IN (SELECT customer_id FROM customer_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))"), 'order' => 'customer_code'));
        $customers = Set::combine($cus, '{n}.Customer.id', array('{0} - {1}', '{n}.Customer.customer_code', '{n}.Customer.name'));
        $this->set(compact("companies", "branches", "customerGroups", "customers"));
    }

    function customerReceivePaymentByRepResult() {
        $this->layout = 'ajax';
    }

    function customerReceivePaymentByRepAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
        $user = $this->getCurrentUser();
        $this->set('userId', $user['User']['id']);
        $companies = ClassRegistry::init('Company')->find('list',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        $this->set(compact("companies"));
    }

    /**
     * A/R Aging
     */
    function accountReceivable() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $classes   = ClassRegistry::init('Class')->find("list", array("conditions" => array("Class.is_active = 1", "Class.id IN (SELECT class_id FROM class_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))")));
        $this->set(compact("companies", "classes"));
    }

    function accountReceivableResult() {
        $this->layout = 'ajax';
    }

    /**
     * Customer Balance
     */
    function customerBalance() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        $this->set(compact("companies"));
    }

    function customerBalanceResult() {
        $this->layout = 'ajax';
    }

    function customerBalanceAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);

        $user = $this->getCurrentUser();
        $this->set('userId', $user['User']['id']);
        $companies = ClassRegistry::init('Company')->find('list',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        $this->set(compact("companies"));
    }

    /**
     * Customer Address
     */
    function customerAddress($data = null) {
        if (isset($_POST['action']) && $_POST['action'] == 'export') {
            $user = $this->getCurrentUser();
            $data = explode(",", $data);

            // Function
            include('includes/function.php');

            /* Customize condition */
            $condition = "is_active=1";
            if ($data[0] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= 'province_id=' . $data[0];
            }
            if ($data[1] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= 'district_id=' . $data[1];
            }
            if ($data[2] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= 'commune_id=' . $data[2];
            }
            if ($data[3] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= 'village_id=' . $data[3];
            }
            if ($data[4] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= 'id IN (SELECT customer_id FROM customer_cgroups WHERE cgroup_id=' . $data[4] . ')';
            }
            if ($data[5] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= 'created_by=' . $data[5];
            }

            $filename = "public/report/customer_address.csv";
            $fp = fopen($filename, "wb");
            $excelContent = MENU_REPORT_CUSTOMER_ADDRESS . "\n\n";
            $excelContent .= TABLE_NO . "\t" . TABLE_CODE . "\t" . TABLE_CUSTOMER . "\t" . TABLE_SEX . "\t" . TABLE_ADDRESS;
            $query = mysql_query('  SELECT
                                        id,
                                        (SELECT name FROM cgroups WHERE id=(SELECT cgroup_id FROM customer_cgroups WHERE customer_id=customers.id LIMIT 1)) AS customer_group_name,
                                        customer_code,
                                        CONCAT_WS(" ",firstname,lastname),
                                        sex,
                                        address_alt
                                    FROM customers
                                    WHERE ' . $condition . '
                                    ORDER BY customer_group_name');
            $index = 1;
            $tmpName = '$';
            while ($data = mysql_fetch_array($query)) {
                if (is_null($data[1])) {
                    $data[1] = 'No Rep';
                }
                if ($tmpName != $data[1]) {
                    $tmpName = $data[1];
                    $excelContent .= "\n" . $tmpName;
                    $index = 1;
                }
                $excelContent .= "\n" . $index++ . "\t" . trim($data[2]) . "\t" . trim($data[3]) . "\t" . trim($data[4]) . "\t" . trim($data[5]);
            }
            $excelContent = chr(255) . chr(254) . @mb_convert_encoding($excelContent, 'UTF-16LE', 'UTF-8');
            fwrite($fp, $excelContent);
            fclose($fp);
            exit();
        } else {
            $this->layout = 'ajax';
            $this->set('dateRange', $this->dateRange());
            $user = $this->getCurrentUser();
            $provinces = ClassRegistry::init('Province')->find('list', array('conditions' => array('is_active = 1')));
            $districts = $this->Address->districtList();
            $communes = $this->Address->communeList();
            $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
            $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
            $this->set(compact('provinces', 'districts', 'communes', 'users', 'companies'));
        }
    }

    function customerAddressResult() {
        $this->layout = 'ajax';
    }

    function customerAddressAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }

    /**
     * Customer Address List
     */
    function customerAddressList() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $provinces = ClassRegistry::init('Province')->find('list', array('conditions' => array('is_active = 1')));
        $districts = $this->Address->districtList();
        $communes = $this->Address->communeList();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('provinces', 'districts', 'communes', 'users', 'companies'));
    }

    function customerAddressListResult() {
        $this->layout = 'ajax';
    }

    /**
     * Customer Address Detail
     */
    function customerAddressDetail() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $provinces = ClassRegistry::init('Province')->find('list', array('conditions' => array('is_active = 1')));
        $districts = $this->Address->districtList();
        $communes = $this->Address->communeList();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('provinces', 'districts', 'communes', 'users', 'companies'));
    }

    function customerAddressDetailResult() {
        $this->layout = 'ajax';
    }

    /**
     * Dormant Customer
     */
    function customerDormant() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $this->set('dateRange', $this->dateRange());
        $this->loadModel('Cgroup');
        $customerGroups = ClassRegistry::init('Cgroup')->find("list", array("conditions" => array("Cgroup.is_active = 1", "Cgroup.id IN (SELECT cgroup_id FROM cgroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))")));
        $this->set(compact("customerGroups"));
    }

    function customerDormantResult() {
        $this->layout = 'ajax';
    }

    function customerDormantAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }

    /**
     * A/R Aging
     */
    function accountReceivableEmployee() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $classes = ClassRegistry::init('Class')->find("list", array("conditions" => array("Class.is_active = 1", "Class.id IN (SELECT class_id FROM class_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))")));
        $this->set(compact("classes", "companies"));
    }

    function accountReceivableEmployeeResult() {
        $this->layout = 'ajax';
    }

    /**
     * Employee Balance
     */
    function employeeBalance() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        $this->set(compact("companies"));
    }

    function employeeBalanceResult() {
        $this->layout = 'ajax';
    }

    function employeeBalanceAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);

        $user = $this->getCurrentUser();
        $this->set('userId', $user['User']['id']);
        $companies = ClassRegistry::init('Company')->find('list',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        $this->set(compact("companies"));
    }

    /**
     * Purchase Order Barcode
     */
    function purchaseOrderBarcode() {
        $this->layout = "ajax";
    }

    function purchaseOrderBarcodeAjax() {
        $this->layout = "ajax";
    }

    /**
     * Purchase By Item
     */
    function purchaseByItem() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $warehouses = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('companies', 'warehouses', 'users', 'branches'));
    }

    function purchaseByItemParentResult() {
        $this->layout = 'ajax';
    }

    function purchaseByItemResult() {
        $this->layout = 'ajax';
    }

    function purchaseByItemDetailResult() {
        $this->layout = 'ajax';
    }

    /**
     * Invoice Purchase Bill
     */
    function purchaseInvoice() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))),'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $warehouses = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('companies', 'warehouses', 'users', 'branches'));
    }

    function purchaseInvoiceResult() {
        $this->layout = 'ajax';
    }

    function purchaseInvoiceAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }

    /**
     * Invoice Purchase Bill Credit
     */
    function purchaseInvoiceCredit() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $vendors = ClassRegistry::init('Vendor')->find("list", array("conditions" => array("Vendor.is_active = 1", "Vendor.id IN (SELECT vendor_id FROM vendor_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))")));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('companies', 'locationGroups', 'vendors', 'users', 'branches'));
    }

    function purchaseInvoiceCreditResult() {
        $this->layout = 'ajax';
    }

    function purchaseInvoiceCreditAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }

    /**
     * Pay Bills Report
     */
    function vendorPayBill() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $this->set(compact("companies", "branches"));
    }

    function vendorPayBillResult() {
        $this->layout = 'ajax';
    }

    function vendorPayBillAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);

        $user = $this->getCurrentUser();
        $this->set('userId', $user['User']['id']);
        $companies = ClassRegistry::init('Company')->find('list',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        $this->set(compact("companies"));
    }

    /**
     * A/P Aging
     */
    function accountPayable() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $vendorGroups = ClassRegistry::init('Vgroup')->find("list", array("conditions" => array("Vgroup.is_active = 1", "Vgroup.id IN (SELECT vgroup_id FROM vgroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))")));
        $this->set(compact("companies", "vendorGroups"));
    }

    function accountPayableResult() {
        $this->layout = 'ajax';
    }

    /**
     * Vendor Balance
     */
    function vendorBalance() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        $this->set(compact("companies"));
    }

    function vendorBalanceResult() {
        $this->layout = 'ajax';
    }

    function vendorBalanceAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);

        $user = $this->getCurrentUser();
        $this->set('userId', $user['User']['id']);
        $companies = ClassRegistry::init('Company')->find('list',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        $this->set(compact("companies"));
    }

    /**
     * Vendor Product List
     */
    function vendorProductList() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $this->set('dateRange', $this->dateRange());
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $this->set(compact("companies"));
    }

    function vendorProductListResult() {
        $this->layout = 'ajax';
    }

    function vendorProductListAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }

    /**
     * Vendor Address
     */
    function vendorAddress() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $this->set('dateRange', $this->dateRange());
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $provinces = ClassRegistry::init('Province')->find('list', array('conditions' => array('is_active = 1')));
        $districts = $this->Address->districtList();
        $communes = $this->Address->communeList();
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('provinces', 'districts', 'communes', 'users', 'companies'));
    }

    function vendorAddressResult() {
        $this->layout = 'ajax';
    }

    function vendorAddressAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }

    /**
     * Vendor Address List
     */
    function vendorAddressList() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $this->set('dateRange', $this->dateRange());
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $provinces = ClassRegistry::init('Province')->find('list', array('conditions' => array('is_active = 1')));
        $districts = $this->Address->districtList();
        $communes = $this->Address->communeList();
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('provinces', 'districts', 'communes', 'users', 'companies'));
    }

    function vendorAddressListResult() {
        $this->layout = 'ajax';
    }

    /**
     * General Ledger
     */
    function ledger() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))),'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $others = ClassRegistry::init('Other')->find("list", array("conditions" => array("Other.is_active = 1", "Other.id IN (SELECT other_id FROM other_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))")));
        $this->set(compact("companies", "others", "branches"));
    }

    function ledgerResult() {
        $this->layout = 'ajax';
    }

    function ledgerAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }

    /**
     * Journal
     */
    function generalLedger() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))),'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $others = ClassRegistry::init('Other')->find("list", array("conditions" => array("Other.is_active = 1", "Other.id IN (SELECT other_id FROM other_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))")));
        $this->set(compact("companies", "others", "branches"));
    }

    function generalLedgerResult() {
        $this->layout = 'ajax';
    }

    function generalLedgerAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }

    /**
     * Trial Balance
     */
    function trialBalance() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $reportTypes = array('as_of' => 'As Of', 'period' => 'Period');
        $this->set(compact("reportTypes"));
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))),'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $others    = ClassRegistry::init('Other')->find("list", array("conditions" => array("Other.is_active = 1", "Other.id IN (SELECT other_id FROM other_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))")));
        $this->set(compact("companies", "others", "branches"));
    }

    function trialBalanceResult() {
        $this->layout = 'ajax';
    }

    function trialBalanceResultByMonth() {
        $this->layout = 'ajax';
    }

    function trialBalanceResultPeriod() {
        $this->layout = 'ajax';
    }

    function trialBalanceResultPeriodByMonth() {
        $this->layout = 'ajax';
    }

    /**
     * Profit & Loss
     */
    function profitLoss() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $others = ClassRegistry::init('Other')->find("list", array("conditions" => array("Other.is_active = 1", "Other.id IN (SELECT other_id FROM other_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))")));
        $this->set(compact("companies", "others", "branches"));
    }

    function profitLossResult() {
        $this->layout = 'ajax';
    }

    function profitLossResultByMonth() {
        $this->layout = 'ajax';
    }

    /**
     * Balance Sheet
     */
    function balanceSheet() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))),'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $others = ClassRegistry::init('Other')->find("list", array("conditions" => array("Other.is_active = 1", "Other.id IN (SELECT other_id FROM other_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))")));
        $this->set(compact("companies", "others", "branches"));
    }

    function balanceSheetResult() {
        $this->layout = 'ajax';
    }

    function balanceSheetResultByMonth() {
        $this->layout = 'ajax';
    }

    /**
     * Statement of Cash Flow
     */
    function cashFlow() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))),'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $others = ClassRegistry::init('Other')->find("list", array("conditions" => array("Other.is_active = 1", "Other.id IN (SELECT other_id FROM other_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))")));
        $this->set(compact("companies", "others", "branches"));
    }

    function cashFlowResult() {
        $this->layout = 'ajax';
    }

    function cashFlowResultByMonth() {
        $this->layout = 'ajax';
    }

    /**
     * Check
     */
    function checkDetail() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))),'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $others = ClassRegistry::init('Other')->find("list", array("conditions" => array("Other.is_active = 1", "Other.id IN (SELECT other_id FROM other_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))")));
        $this->set(compact("companies", "others", "branches"));
    }

    function checkDetailResult() {
        $this->layout = 'ajax';
    }

    function checkDetailAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }

    /**
     * Deposit
     */
    function depositDetail() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $others = ClassRegistry::init('Other')->find("list", array("conditions" => array("Other.is_active = 1", "Other.id IN (SELECT other_id FROM other_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))")));
        $this->set(compact("companies", "others", "branches"));
    }

    function depositDetailResult() {
        $this->layout = 'ajax';
    }

    function depositDetailAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }

    /**
     * Reconcile
     */
    function reconcile() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))),'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $this->set(compact("companies", "branches"));
    }

    function reconcileResult() {
        $this->layout = 'ajax';
    }

    function getStatementEndingDate($companyId, $coaId) {
        $this->layout = 'ajax';
        $this->set("companyId", $companyId);
        $this->set("coaId", $coaId);
    }

    /**
     * User Rights
     */
    function userRights() {
        $this->layout = 'ajax';
        $user = ClassRegistry::init('User')->find('all', array('fields' => array('first_name', 'last_name', 'id')));
        $users = Set::combine($user, '{n}.User.id', array('{0} {1}', '{n}.User.first_name', '{n}.User.last_name'));
        $this->set(compact("users"));
    }

    function userRightsResult() {
        $this->layout = 'ajax';
    }

    /**
     * User Log
     */
    function userLog() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = ClassRegistry::init('User')->find('all', array('fields' => array('first_name', 'last_name', 'id')));
        $users = Set::combine($user, '{n}.User.id', array('{0} {1}', '{n}.User.first_name', '{n}.User.last_name'));
        $this->set(compact("users"));
    }

    function userLogResult() {
        $this->layout = 'ajax';
    }

    function userLogAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }

    function dateRange() {
        return array(
            'Today' => 'Today',
            'Yesterday' => 'Yesterday',
            'This Week' => 'This Week',
            'This Week-to-date' => 'This Week-to-date',
            'This Month' => 'This Month',
            'This Month-to-date' => 'This Month-to-date',
            'This Quarter' => 'This Quarter',
            'This Quarter-to-date' => 'This Quarter-to-date',
            'This Year' => 'This Year',
            'This Year-to-date' => 'This Year-to-date',
            'Last 30 days' => 'Last 30 days',
            'Last 365 days' => 'Last 365 days',
            'Last Week' => 'Last Week',
            'Last Week-to-date' => 'Last Week-to-date',
            'Last Month' => 'Last Month',
            'Last Month-to-date' => 'Last Month-to-date',
            'Last Quarter' => 'Last Quarter',
            'Last Quarter-to-date' => 'Last Quarter-to-date',
            'Last Year' => 'Last Year',
            'Last Year-to-date' => 'Last Year-to-date',
            'Next 30 days' => 'Next 30 days',
            'Next 365 days' => 'Next 365 days',
            'Next Week' => 'Next Week',
            'Next 4 Weeks' => 'Next 4 Weeks',
            'Next Month' => 'Next Month',
            'Next Quarter' => 'Next Quarter',
            'Next Year' => 'Next Year'
        );
    }
    
    /**
     * Search Customer Group
     */
    function searchCgroup(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $userPermission = 'Cgroup.id IN (SELECT cgroup_id FROM cgroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id ='.$user['User']['id'].'))';
        $cgroups = ClassRegistry::init('Cgroup')->find('all', array(
                        'conditions' => array('OR' => array(
                                'Cgroup.name LIKE' => '%' . $this->params['url']['q'] . '%'
                            ), 'Cgroup.is_active' => 1, $userPermission
                        ),
                        'limit' => $this->params['url']['limit']
                    ));
        if (!empty($cgroups)) {
            foreach ($cgroups as $cgroup) {
                $name = $cgroup['Cgroup']['name'];
                echo "{$cgroup['Cgroup']['id']}.*{$name}\n";
            }
        }else{
            echo '';
        }
        exit;
    }
    
    /**
     * Search Customer
     */
    function searchCustomer(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $customers = ClassRegistry::init('Patient')->find('all', array(
                        'conditions' => array('OR' => array(
                                'Patient.patient_name LIKE' => '%' . $this->params['url']['q'] . '%',
                                'Patient.patient_code LIKE' => '%' . $this->params['url']['q'] . '%',
                                'Patient.telephone LIKE' => '%' . $this->params['url']['q'] . '%',
                                'Patient.email LIKE' => '%' . $this->params['url']['q'] . '%'
                            ), 'Patient.is_active' => 1
                        ),
                        'limit' => $this->params['url']['limit']
                    ));
        if (!empty($customers)) {
            foreach ($customers as $customer) {
                $name = $customer['Patient']['patient_name'];
                echo "{$customer['Patient']['id']}.*{$customer['Patient']['patient_code']} - {$name}.*{$customer['Patient']['patient_code']}\n";
            }
        }else{
            echo '';
        }
        exit;
    }
    
    /**
     * Search Product Group
     */
    function searchPgroup(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $userPermission = 'Pgroup.id IN (SELECT pgroup_id FROM pgroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id ='.$user['User']['id'].'))';
        $pgroups = ClassRegistry::init('Pgroup')->find('all', array(
                        'conditions' => array('OR' => array(
                                'Pgroup.name LIKE' => '%' . $this->params['url']['q'] . '%'
                            ), 'Pgroup.is_active' => 1, $userPermission
                        ),
                        'limit' => $this->params['url']['limit']
                    ));
        if (!empty($pgroups)) {
            foreach ($pgroups as $pgroup) {
                $name = $pgroup['Pgroup']['name'];
                echo "{$pgroup['Pgroup']['id']}.*{$name}\n";
            }
        }else{
            echo '';
        }
        exit;
    }
    
    /**
     * Search Product
     */
    function searchProduct(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $userPermission = 'Product.company_id IN (SELECT company_id FROM user_companies WHERE user_id ='.$user['User']['id'].') AND Product.id IN (SELECT product_id FROM product_branches WHERE branch_id IN (SELECT branch_id FROM user_branches WHERE user_id = '.$user['User']['id'].'))';
        $products = ClassRegistry::init('Product')->find('all', array(
                        'conditions' => array('OR' => array(
                                'Product.code LIKE' => '%' . $this->params['url']['q'] . '%',
                                'Product.barcode LIKE' => '%' . $this->params['url']['q'] . '%',
                                'Product.name LIKE' => '%' . $this->params['url']['q'] . '%',
                                'Product.name_kh LIKE' => '%' . $this->params['url']['q'] . '%'
                            ), 'Product.is_active' => 1, $userPermission
                        ),
                        'limit' => $this->params['url']['limit']
                    ));
        if (!empty($products)) {
            foreach ($products as $product) {
                $name = $product['Product']['code']." - ".$product['Product']['name'];
                echo "{$product['Product']['id']}.*{$name}\n";
            }
        }else{
            echo '';
        }
        exit;
    }
    
    /**
     * Search Employee
     */
    
    function searchEmployee(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $userPermission = 'Employee.id IN (SELECT employee_id FROM employee_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id ='.$user['User']['id'].'))';
        $employees = ClassRegistry::init('Employee')->find('all', array(
                        'conditions' => array('OR' => array(
                                'Employee.name LIKE' => '%' . $this->params['url']['q'] . '%',
                                'Employee.name_kh LIKE' => '%' . $this->params['url']['q'] . '%',
                                'Employee.employee_code LIKE' => '%' . $this->params['url']['q'] . '%'
                            ), 'Employee.is_active' => 1, $userPermission
                        ),
                        'limit' => $this->params['url']['limit']
                    ));
        if (!empty($employees)) {
            foreach ($employees as $employee) {
                echo "{$employee['Employee']['id']}.*{$employee['Employee']['name']}.*{$employee['Employee']['employee_code']}\n";
            }
        }else{
            echo '';
        }
        exit;
    }
    
    /**
     * Search Employee Group
     */
    
    function searchEgroup(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $userPermission = 'Egroup.id IN (SELECT egroup_id FROM egroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id ='.$user['User']['id'].'))';
        $egroups = ClassRegistry::init('Egroup')->find('all', array(
                        'conditions' => array('OR' => array(
                                'Egroup.name LIKE' => '%' . $this->params['url']['q'] . '%'
                            ), 'Egroup.is_active' => 1, $userPermission
                        ),
                        'limit' => $this->params['url']['limit']
                    ));
        if (!empty($egroups)) {
            foreach ($egroups as $egroup) {
                echo "{$egroup['Egroup']['id']}.*{$egroup['Egroup']['name']}\n";
            }
        }else{
            echo '';
        }
        exit;
    }
    
    /**
     * Search Vendor
     */
    function searchVendor(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $userPermission = 'Vendor.id IN (SELECT vendor_id FROM vendor_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id ='.$user['User']['id'].'))';
        $vendors = ClassRegistry::init('Vendor')->find('all', array(
                        'conditions' => array('OR' => array(
                                'Vendor.name LIKE' => '%' . $this->params['url']['q'] . '%',
                                'Vendor.vendor_code LIKE' => '%' . $this->params['url']['q'] . '%',
                                'Vendor.work_telephone LIKE' => '%' . $this->params['url']['q'] . '%',
                                'Vendor.fax_number LIKE' => '%' . $this->params['url']['q'] . '%',
                                'Vendor.email_address LIKE' => '%' . $this->params['url']['q'] . '%',
                            ), 'Vendor.is_active' => 1, $userPermission
                        ),
                        'limit' => $this->params['url']['limit']
                    ));
        if (!empty($vendors)) {
            foreach ($vendors as $vendor) {
                echo "{$vendor['Vendor']['id']}.*{$vendor['Vendor']['name']}.*{$vendor['Vendor']['vendor_code']}\n";
            }
        }else{
            echo '';
        }
        exit;
    }
    
    // Sales Top Item
    function salesTopItem(){
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $this->set(compact('companies', 'branches'));
    }
    
    function salesTopItemResult(){
        $this->layout = 'ajax';
    }
    
    function salesTopItemGraph(){
        $this->layout = 'ajax';
    }
    
    // Sales Top Customer
    function salesTopCustomer(){
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $this->set(compact('companies', 'branches'));
    }
    
    function salesTopCustomerResult(){
        $this->layout = 'ajax';
    }
    
    function salesTopCustomerGraph(){
        $this->layout = 'ajax';
    }
    
    function salesBySalesman(){
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id')), array('table' => 'locations', 'type' => 'inner', 'conditions' => array('locations.location_group_id=LocationGroup.id'))), 'conditions' => array('LocationGroup.is_active = 1', 'user_location_groups.user_id=' . $user['User']['id'])));
        $salesmans = ClassRegistry::init('Employee')->find('list', array('joins' => array(array('table' => 'employee_companies', 'type' => 'inner', 'conditions' => array('employee_companies.employee_id=Employee.id'))), 'conditions' => array('Employee.is_active = 1', 'Employee.is_show_in_sales = 1', 'employee_companies.company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')')));
        $users = ClassRegistry::init('User')->find('all', array('conditions' => array('is_active = 1'), 'order' => 'username'));
        $this->set(compact('companies', 'branches', 'locationGroups', 'salesmans', 'users'));
    }
    
    function salesBySalesmanDetailResult(){
        $this->layout = 'ajax';
    }
    
    /**
     * Invoice By Rep
     */
    function invoiceByRep($data = null) {
        $this->layout = 'ajax';
        if (isset($_POST['action']) && $_POST['action'] == 'export') {
            $user = $this->getCurrentUser();
            $data = explode(",", $data);

            // Function
            include('includes/function.php');

            /* Customize condition */
            $condition = 'sales_orders.is_pos=0';
            if ($data[1] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= '"' . dateConvert(str_replace("|||", "/", $data[1])) . '" <= DATE(sales_orders.order_date)';
            }
            if ($data[2] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= '"' . dateConvert(str_replace("|||", "/", $data[2])) . '" >= DATE(sales_orders.order_date)';
            }
            $condition != '' ? $condition .= ' AND ' : '';
            if ($data[3] == '') {
                $condition .= 'sales_orders.status!=0';
            } else {
                $condition .= 'sales_orders.status=' . $data[3];
            }
            if ($data[4] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= 'sales_orders.company_id = ' . $data[4];
            } else {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= 'sales_orders.company_id IN (SELECT company_id FROM user_companies WHERE user_id = ' . $user['User']['id'].')';
            }
            if ($data[5] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= 'sales_orders.location_group_id =' . $data[5];
            }
            if ($data[6] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= 'sales_orders.customer_id IN (SELECT customer_id FROM customer_cgroups WHERE cgroup_id=' . $data[6] . ')';
            }
            if ($data[7] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= 'sales_orders.customer_id=' . $data[7];
            }
            if ($data[8] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= 'sales_orders.sales_rep_id = ' . $data[8];
            }
            if ($data[9] != '') {
                $condition != '' ? $condition .= ' AND ' : '';
                $condition .= 'sales_orders.created_by = ' . $data[9];
            }

            $filename = "public/report/invoice_by_rep.csv";
            $fp = fopen($filename, "wb");
            $excelContent = MENU_REPORT_SALES_ORDER_BY_REP_INVOICE . "\n\n";
            $excelContent .= TABLE_NO . "\t" . TABLE_INVOICE_DATE . "\t" . TABLE_INVOICE_CODE . "\t" . TABLE_MEMO . "\t" . PRICING_RULE_CUSTOMER . "\t" . GENERAL_AGING . "\t" . TABLE_TOTAL_AMOUNT . "\t" . GENERAL_BALANCE . "\t" . TABLE_STATUS;
            $query = mysql_query('  SELECT
                                        CONCAT_WS(" ", employees.employee_code, " - " , employees.name) AS rep_name,
                                        sales_orders.order_date,
                                        sales_orders.so_code,
                                        sales_orders.memo,
                                        CONCAT_WS(" ", customers.customer_code, " - ", customers.name),
                                        IF(balance>0 AND DATEDIFF(now(),due_date)>0,DATEDIFF(now(),due_date),"."),
                                        sales_orders.total_amount-IFNULL(sales_orders.discount,0)+IFNULL(sales_orders.total_vat,0),
                                        sales_orders.balance,
                                        CASE sales_orders.status WHEN 0 THEN "Void" WHEN 1 THEN "Issued" WHEN 2 THEN "Fulfilled" END
                                    FROM sales_orders INNER JOIN customers ON customers.id = sales_orders.customer_id INNER JOIN employees ON employees.id = sales_orders.sales_rep_id
                                    WHERE ' . $condition . '
                                    ORDER BY rep_name,sales_orders.order_date') or die(mysql_error());
            $index = 1;
            $tmpName = '';
            $amount = 0;
            $balance = 0;
            $amountTotal = 0;
            $balanceTotal = 0;
            while ($data = mysql_fetch_array($query)) {
                if ($index != 1 && $data[0] != $tmpName) {
                    $index = 1;
                    $excelContent .= "\n" . 'Total ' . $tmpName . "\t\t\t\t\t\t" . number_format($amount, 2) . "\t" . number_format($balance, 2);
                }
                if ($data[0] == $tmpName) {
                    $amount += $data[6];
                    $balance += $data[7];
                } else {
                    $amount = $data[6];
                    $balance = $data[7];
                }
                if ($tmpName != $data[0]) {
                    $tmpName = $data[0];
                    $excelContent .= "\n" . $tmpName;
                    $index = 1;
                }
                $amountTotal += $data[6];
                $balanceTotal += $data[7];
                $excelContent .= "\n" . $index++ . "\t" . trim($data[1]) . "\t" . trim($data[2]) . "\t" . trim($data[3]) . "\t" . trim($data[4]) . "\t" . trim($data[5]) . "\t" . number_format($data[6], 2) . "\t" . number_format($data[7], 2) . "\t" . trim($data[8]);
            }
            if (mysql_num_rows($query)) {
                $excelContent .= "\n" . 'Total ' . $tmpName . "\t\t\t\t\t\t" . number_format($amount, 2) . "\t" . number_format($balance, 2);
                $excelContent .= "\n" . 'GRAND TOTAL' . "\t\t\t\t\t\t" . number_format($amountTotal, 2) . "\t" . number_format($balanceTotal, 2);
            }
            $excelContent = chr(255) . chr(254) . @mb_convert_encoding($excelContent, 'UTF-16LE', 'UTF-8');
            fwrite($fp, $excelContent);
            fclose($fp);
            exit();
        } else {
            $this->set('dateRange', $this->dateRange());
            $user = $this->getCurrentUser();
            $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
            $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
            $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id')), array('table' => 'locations', 'type' => 'inner', 'conditions' => array('locations.location_group_id=LocationGroup.id'))), 'conditions' => array('LocationGroup.is_active = 1', 'user_location_groups.user_id=' . $user['User']['id'])));
            $salesmans = ClassRegistry::init('Employee')->find('list', array('joins' => array(array('table' => 'employee_companies', 'type' => 'inner', 'conditions' => array('employee_companies.employee_id=Employee.id'))), 'conditions' => array('Employee.is_active = 1', 'Employee.is_show_in_sales = 1', 'employee_companies.company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')')));
            $users = ClassRegistry::init('User')->find('all', array('conditions' => array('is_active = 1'), 'order' => 'username'));
            $this->set(compact('locationGroups', 'salesmans', 'companies', 'branches', 'users'));
        }
    }

    function invoiceByRepResult() {
        $this->layout = 'ajax';
    }

    function invoiceByRepAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }
    
    /**
     * Statement
     */
    function statement() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
    }

    function statementResult() {
        $this->layout = 'ajax';
    }

    /**
     * Statement By Rep
     */
    function statementByRep() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $salesmans = ClassRegistry::init('Employee')->find('list',
                                            array(
                                                'joins' => array(
                                                    array('table' => 'employee_companies', 'type' => 'inner', 'conditions' => array('employee_companies.employee_id=Employee.id')
                                                    )
                                                ),
                                                'conditions' => array('Employee.is_active = 1', 'Employee.is_show_in_sales = 1', 'employee_companies.company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')')
                                            )
                         );
        $this->set(compact("salesmans"));
    }

    function statementByRepResult() {
        $this->layout = 'ajax';
    }
    
    /**
     * Customer Balance By Invoice
     */
    function customerBalanceByInvoice() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $customerGroups = ClassRegistry::init('Cgroup')->find("list", array("conditions" => array("Cgroup.is_active = 1")));
        $this->set(compact("customerGroups", "companies"));
    }

    function customerBalanceByInvoiceResult() {
        $this->layout = 'ajax';
    }

    function customerBalanceByInvoiceAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);

        $user = $this->getCurrentUser();
        $this->set('userId', $user['User']['id']);
    }
    
    /**
     * Vendor Balance By Invoice
     */
    function vendorBalanceByInvoice() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))),'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $this->set(compact("companies"));
    }

    function vendorBalanceByInvoiceResult() {
        $this->layout = 'ajax';
    }

    function vendorBalanceByInvoiceAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);

        $user = $this->getCurrentUser();
        $this->set('userId', $user['User']['id']);
    }
    
    /**
     * Audi Trail
     */
    function auditTrail(){
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))),'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $user = ClassRegistry::init('User')->find('all', array('fields' => array('first_name', 'last_name', 'id'), 'conditions' => array("is_active=1")));
        $users = Set::combine($user, '{n}.User.id', array('{0} {1}', '{n}.User.first_name', '{n}.User.last_name'));
        $this->set(compact('users', 'companies', 'branches'));
    }
    
    function auditTrailResult(){        
        $this->layout = 'ajax';
    }
    
    /**
     * Transfer Order
     */
//    function quotation() {
//        $this->layout = 'ajax';
//        $this->set('dateRange', $this->dateRange());
//        $user = $this->getCurrentUser();
//        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))),'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
//        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
//        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
//        $this->set(compact('companies', 'branches', 'users'));
//    }
//
//    function quotationResult() {
//        $this->layout = 'ajax';
//    }
//
//    function quotationAjax($data = null) {
//        $this->layout = 'ajax';
//        $data = explode(",", $data);
//        $this->set("data", $data);
//    }
    
    /**
     * Transfer Order
     */
    function order() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('companies', 'users'));
    }

    function orderResult() {
        $this->layout = 'ajax';
    }

    function orderAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }
    
    /**
     * Consignment Customer
     */
    function consignmentCustomer() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))),'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('companies', 'branches', 'locationGroups', 'users'));
    }

    function consignmentCustomerResult() {
        $this->layout = 'ajax';
    }

    function consignmentCustomerAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }
    
    /**
     * POS (Shift Control)
     */
    function posShiftControl() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $users = ClassRegistry::init('User')->find('all', array('conditions' => array('is_active = 1'), 'order' => 'username'));
        $this->set(compact('companies', 'branches', 'locationGroups', 'users'));
    }

    function posShiftControlResult() {
        $this->layout = 'ajax';
    }

    function posShiftControlAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }
    
    /**
     * POS (Shift Collect By User)
     */
    function posCollectShiftByUser() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $users = ClassRegistry::init('User')->find('all', array('conditions' => array('is_active = 1'), 'order' => 'username'));
        $this->set(compact('companies', 'branches', 'locationGroups', 'users'));
    }

    function posCollectShiftByUserResult() {
        $this->layout = 'ajax';
    }

    function posCollectShiftByUserAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }
    
    /**
     * Report Receipt
     */
    function customerReceipt() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $customerGroups = ClassRegistry::init('Cgroup')->find("list", array("conditions" => array("Cgroup.is_active = 1", "Cgroup.id IN (SELECT cgroup_id FROM cgroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))")));
        $cus = ClassRegistry::init('Customer')->find('all', array('fields' => array('customer_code', 'name', 'id'), 'conditions' => array('Customer.is_active = 1', "Customer.id IN (SELECT customer_id FROM customer_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))"), 'order' => 'customer_code'));
        $customers = Set::combine($cus, '{n}.Customer.id', array('{0} - {1}', '{n}.Customer.customer_code', '{n}.Customer.name'));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1','id IN ((SELECT created_by FROM receipts WHERE is_void=0 GROUP BY created_by))'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('companies', 'locationGroups', 'customerGroups', 'customers', 'users', 'patients'));
    }

    function customerReceiptResult() {
        $this->layout = 'ajax';
    }

    function customerReceiptAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }
    
    /**
     * Search Patient
     */
    function searchPatient(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $customers = ClassRegistry::init('Patient')->find('all', array(
                        'conditions' => array('OR' => array(
                                'Patient.patient_name LIKE' => '%' . $this->params['url']['q'] . '%',
                                'Patient.patient_code LIKE' => '%' . $this->params['url']['q'] . '%',
                                'Patient.telephone LIKE' => '%' . $this->params['url']['q'] . '%',
                                'Patient.email LIKE' => '%' . $this->params['url']['q'] . '%'
                            ), 'Patient.is_active' => 1
                        ),
                        'limit' => $this->params['url']['limit']
                    ));
        if (!empty($customers)) {
            foreach ($customers as $customer) {
                $name = $customer['Patient']['patient_name'];
                $patientInfo = $customer['Patient']['patient_code'].' - '.$name;
                echo "{$customer['Patient']['id']}.*{$patientInfo}.*{$name}\n";
            }
        }else{
            echo '';
        }
        exit;
    }
    
    
    //{$customer['Patient']['patient_code']}
    /*
     * Report Laboratory
     */
    function customerLabo() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        $patients = $this->Patient->find('list', array('order'=>array('Patient.id ASC'),'group' => 'Patient.id',
                'fields' => array('Patient.id' , 'Patient.patient_name'),
                'joins' => array(
                    array('table' => 'queues',
                        'alias' => 'Queue',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Patient.id = Queue.patient_id'
                        )
                    ) , 
                    array('table' => 'invoices',
                        'alias' => 'Invoice',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Queue.id = Invoice.queue_id'
                        )
                    ) , 
                    array('table' => 'invoice_details',
                        'alias' => 'InvoiceDetail',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Invoice.id = InvoiceDetail.invoice_id'
                        )
                    )
            )));   
                       
        $doctors = ClassRegistry::init('User')->find('list',
                array(
                    'joins' => array(array(
                        'table' => 'user_employees', 'type' => 'INNER', 'conditions' => array('User.id = user_employees.user_id')),
                        array('table' => 'employees', 'type' => 'INNER', 'conditions' => array('employees.id = user_employees.employee_id')),) , 
                        array('table' => 'invoice_details', 'type' => 'INNER', 'conditions' => array('User.id = invoice_details.doctor_id')) ,
                        'conditions' => array('User.is_active=1 '), 'fields' => array('User.id', 'employees.name'),  'order' => 'employees.name ASC'));
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $customerGroups = ClassRegistry::init('Cgroup')->find("list", array("conditions" => array("Cgroup.is_active = 1", "Cgroup.id IN (SELECT cgroup_id FROM cgroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))")));
        $cus = ClassRegistry::init('Customer')->find('all', array('fields' => array('customer_code', 'name', 'id'), 'conditions' => array('Customer.is_active = 1', "Customer.id IN (SELECT customer_id FROM customer_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))"), 'order' => 'customer_code'));
        $customers = Set::combine($cus, '{n}.Customer.id', array('{0} - {1}', '{n}.Customer.customer_code', '{n}.Customer.name'));
        $laboSubGroups = ClassRegistry::init('LaboItemGroup')->find('list', array('conditions' => array('is_active = 1', 'id IN (SELECT labo_item_group_id FROM labo_requests WHERE is_active = 1)'), 'order' => 'name', 'fields' => array('LaboItemGroup.id', 'LaboItemGroup.name')));
        $companyInsurances = ClassRegistry::init('CompanyInsurance')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'name', 'fields' => array('CompanyInsurance.id', 'CompanyInsurance.name')));
        $this->set(compact('patients' , 'doctors' , 'companies', 'locationGroups', 'customerGroups', 'customers', 'companyInsurances', 'laboSubGroups'));
    }

    function customerLaboResult() {
        $this->layout = 'ajax';
    }

    function customerLaboAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }
    
    /*
     * Section/Service report
     */
    function sectionService() {
        $this->layout = 'ajax';
        $this->loadModel('User');
        $this->loadModel('Section');
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $sections = ClassRegistry::init('Section')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'name', 'fields' => array('Section.id', 'Section.name')));
        $services = ClassRegistry::init('Service')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'name', 'fields' => array('Service.id', 'Service.name')));
        $doctors = ClassRegistry::init('User')->find('list',
                array(
                    'joins' => array(array(
                        'table' => 'user_employees', 'type' => 'INNER', 'conditions' => array('User.id = user_employees.user_id')),
                        array('table' => 'employees', 'type' => 'INNER', 'conditions' => array('employees.id = user_employees.employee_id')),) , 
                        array('table' => 'invoice_details', 'type' => 'INNER', 'conditions' => array('User.id = invoice_details.doctor_id')) ,
                        'conditions' => array('User.is_active=1 '), 'fields' => array('User.id', 'employees.name'),  'order' => 'employees.name ASC'));
        $this->set(compact('companies', 'locationGroups', 'customerGroups', 'customers', 'users', 'patients', 'doctors', 'services', 'sections'));
    }

    function sectionServiceResult() {
        $this->layout = 'ajax';
    }

    function sectionServiceAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }
    

    /*
     * Report Client Insurance Provider.
     */
    function customerClientInsurance() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
//        $customerGroups = ClassRegistry::init('Cgroup')->find("list", array("conditions" => array("Cgroup.is_active = 1", "Cgroup.id IN (SELECT cgroup_id FROM cgroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))")));
//        $cus = ClassRegistry::init('Customer')->find('all', array('fields' => array('customer_code', 'name', 'id'), 'conditions' => array('Customer.is_active = 1', "Customer.id IN (SELECT customer_id FROM customer_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))"), 'order' => 'customer_code'));
//        $customers = Set::combine($cus, '{n}.Customer.id', array('{0} - {1}', '{n}.Customer.customer_code', '{n}.Customer.name'));
//        $patient = ClassRegistry::init('Patient')->find('all', array('fields' => array('patient_code', 'patient_name', 'id'), 'conditions' => array('Patient.is_active = 1'), 'order' => 'patient_code'));
//        $patients = Set::combine($patient, '{n}.Patient.id', array('{0} - {1}', '{n}.Patient.patient_code', '{n}.Patient.patient_name'));
        $companyInsurances = ClassRegistry::init('CompanyInsurance')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'name', 'fields' => array('CompanyInsurance.id', 'CompanyInsurance.name')));
        $this->set(compact('companies', 'locationGroups', 'customerGroups', 'customers', 'companyInsurances', 'patients'));
    }

    function customerClientInsuranceResult() {
        $this->layout = 'ajax';
    }

    function customerClientInsuranceAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }
    
    /**
     * Case Expense
     */
    function caseExpense() {
        $this->layout = 'ajax';
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))),'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'] . ' AND LocationGroup.is_active=1'), 'order' => 'LocationGroup.name'));
        $users = ClassRegistry::init('User')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'username', 'fields' => array('User.id', 'User.username')));
        $this->set(compact('companies', 'branches', 'locationGroups', 'users'));
    }

    function caseExpenseResult() {
        $this->layout = 'ajax';
    }

	function convertInvoice() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if(!empty($_POST['soId'])){
            $this->loadModel('Invoice');
            $this->loadModel('InvoiceDetail');                    
            $modified = date("Y-m-d H:i:s"); 
            $dateTime = date("Y-m-d H:i:s");
            $dataSelected = $_POST['soId'];            
            $amount = 0;
            $balance = 0;
            $grandTotal = 0;
            $index = 0;
            asort($dataSelected);    
            foreach ($dataSelected as $invId) {   
				$sqlCheckQueue = mysql_query("SELECT invoices.queue_id FROM invoices WHERE invoices.id = {$invId} LIMIT 1;");
				if(mysql_num_rows($sqlCheckQueue)){
					while($rowCheckQueue = mysql_fetch_array($sqlCheckQueue)){
						$insertSales = mysql_query("INSERT INTO ".DB_SS_MONY_KID."queues (`id`, `patient_id`, `patient_type_id`, `created`, `created_by`, `modified`, `modified_by`, `status`)
                                        SELECT `id`, `patient_id`, `patient_type_id`, `created`, `created_by`, `modified`, `modified_by`, `status` FROM queues WHERE  status > 0 AND id = " . $rowCheckQueue['queue_id'] . ";");

						$insertSales = mysql_query("INSERT INTO ".DB_SS_MONY_KID."queued_doctors (`id`, `queue_id`, `doctor_id`, `created`, `created_by`, `modified`, `modified_by`, `status`)
                                        SELECT `id`, `queue_id`, `doctor_id`, `created`, `created_by`, `modified`, `modified_by`, `status` FROM queued_doctors WHERE status > 0 AND queue_id = " . $rowCheckQueue['queue_id'] . ";");
					}
				}

                // insert invoice to Mony-kid SS

                $newInvoiceCode = $this->Helper->generateAutoINVCode('invoices', 'invoice_code', 6, 'I', 'is_void = 0'); 
                
                $insertSales = mysql_query("INSERT INTO ".DB_SS_MONY_KID."invoices (`branch_id`, `invoice_code`, `company_id`, `company_insurance_id`, `queue_id`, `ar_id`, `total_amount`, `total_discount`, `created`, `created_by`, `modified`, `modified_by`, `type_payment_id`)
                                            SELECT `branch_id`,'" . $newInvoiceCode . "',`company_id`, `company_insurance_id`, `queue_id`, `ar_id`, `total_amount`, `total_discount`, `created`,  `created_by`, `modified`, `modified_by`, `type_payment_id` FROM invoices WHERE id = " . $invId . ";");
                $invoiceId = mysql_insert_id();
                
                // insert receipt to Mony-kid SS
                $receiptCode = $this->Helper->generateAutoReceiptCode('receipts', 'receipt_code', 6, 'RE', 'is_void = 0'); 
                $insertReceipt = mysql_query("INSERT INTO ".DB_SS_MONY_KID."receipts (`invoice_id`, `chart_account_id`, `receipt_code`, `exchange_rate_id`, `total_amount_paid`, `balance`, `total_dis`, `total_dis_p`, `pay_date`, `due_date`, `created`, `created_by`)
                                            SELECT '".$invoiceId."', `chart_account_id`, '" . $receiptCode . "', `exchange_rate_id`, `total_amount_paid`, `balance`, `total_dis`, `total_dis_p`, `pay_date`, `due_date`, `created`, `created_by` FROM receipts WHERE invoice_id = " . $invId . " AND is_void = 0;");
                // product detail 
                $invoiceDetails = ClassRegistry::init('InvoiceDetail')->find("all", array(
                    'conditions' => array('InvoiceDetail.invoice_id' => $invId),
                    'group' => array(
                        "InvoiceDetail.id"
                    ),
                    'order' => 'InvoiceDetail.id'
                ));           
                $pNumber = 0;
				$totalAmount = 0;
                foreach ($invoiceDetails as $invoiceDetail) {
					// debug($invoiceDetail);
					// exit;
					$unitPrice = 0;
					$totalPrice = 0;
					if($invoiceDetail['InvoiceDetail']['type'] == 1){
						// insert invoice details
						$sqlServiceId = mysql_query("SELECT sys_code, id FROM services WHERE is_active = 1 AND id={$invoiceDetail['InvoiceDetail']['service_id'] }");
						if(mysql_num_rows($sqlServiceId)){
							while($rowServiceId = mysql_fetch_array($sqlServiceId)){
								$sqlServiceSeccondaryId = mysql_query("SELECT id FROM ".DB_SS_MONY_KID."services WHERE is_active = 1 AND sys_code = '".$rowServiceId['sys_code']."'");
								if(mysql_num_rows($sqlServiceSeccondaryId)){
									while($rowServiceSeccondaryId = mysql_fetch_array($sqlServiceSeccondaryId)){
										$sqlSelectPrice = mysql_query("SELECT IFNULL(unit_price,0) AS unit_price FROM ".DB_SS_MONY_KID."services_patient_group_details WHERE is_active = 1 AND patient_group_id = 1 AND service_id = {$invoiceDetail['InvoiceDetail']['service_id']}");
										while($rowSelectPrice = mysql_fetch_array($sqlSelectPrice)){
										$unitPrice = $rowSelectPrice['unit_price'];
										$totalPrice = ($unitPrice * $invoiceDetail['InvoiceDetail']['qty']) - $invoiceDetail['InvoiceDetail']['discount'];
										$totalAmount +=$totalPrice;
										mysql_query("INSERT INTO ".DB_SS_MONY_KID."`invoice_details` 
													(`exchange_rate_id`, 
													`invoice_id`, 
													`type`, 
													`date_created`, 
													`service_id`, 
													`doctor_id`, 
													`qty`, 
													`discount`, 
													`unit_price`, 
													`hospital_price`, 
													`total_price`, 
													`created`,
													`created_by`, 
													`modified`, 
													`modified_by`) "
												. "VALUES (
														'" . $invoiceDetail['InvoiceDetail']['exchange_rate_id'] . "', 
														'" . $invoiceId . "', 
														'" . $invoiceDetail['InvoiceDetail']['type'] . "', 
														'" . $invoiceDetail['InvoiceDetail']['date_created'] . "', 
														'" . $rowServiceSeccondaryId['id'] . "', 
														'" . $invoiceDetail['InvoiceDetail']['doctor_id'] . "', 
														'" . $invoiceDetail['InvoiceDetail']['qty'] . "', 
														'" . $invoiceDetail['InvoiceDetail']['discount'] . "', 
														'" . $unitPrice . "',
														'" . $unitPrice . "', 
														'" . $totalPrice . "', 
														'" . $invoiceDetail['InvoiceDetail']['created'] . "', 
														'" . $invoiceDetail['InvoiceDetail']['created'] . "', 
														'".$invoiceDetail['InvoiceDetail']['modified']."', 
														'".$invoiceDetail['InvoiceDetail']['modified_by']."' ) ");  
										}
									}
								}else{
									//invalid on service
									echo MESSAGE_DATA_INVALID;
									exit;
								}            
							}
						}else{
							//invalid on service
							echo MESSAGE_DATA_INVALID;
            				exit;
						}
					}else if($invoiceDetail['InvoiceDetail']['type'] == 2){
						$sqlLaboId = mysql_query("SELECT id, code FROM labo_item_groups WHERE labo_item_groups.id = '".$invoiceDetail['InvoiceDetail']['service_id']."' AND is_active = 1");
                    	if(mysql_num_rows($sqlLaboId)){
							while($rowLaboId = mysql_fetch_array($sqlLaboId)){
								$sqlLaboSeccondaryId = mysql_query("SELECT id FROM ".DB_SS_MONY_KID."labo_item_groups WHERE is_active = 1 AND code = '".$rowLaboId['code']."'");
								if(mysql_num_rows($sqlLaboSeccondaryId)){
									while($rowLaboSeccondaryId = mysql_fetch_array($sqlLaboSeccondaryId)){
										$sqlSelectPrice = mysql_query("SELECT IFNULL(unit_price,0) AS unit_price FROM ".DB_SS_MONY_KID."labo_item_patient_groups WHERE is_active = 1 AND patient_group_id = 1 AND labo_item_group_id = {$rowLaboSeccondaryId['id']}");
										while($rowSelectPrice = mysql_fetch_array($sqlSelectPrice)){
											$unitPrice = $rowSelectPrice['unit_price'];
											$totalPrice = ($unitPrice * $invoiceDetail['InvoiceDetail']['qty']) - $invoiceDetail['InvoiceDetail']['discount'];
											$totalAmount +=$totalPrice;
											mysql_query("INSERT INTO ".DB_SS_MONY_KID."`invoice_details` 
														(`exchange_rate_id`, 
														`invoice_id`, 
														`type`, 
														`date_created`, 
														`service_id`, 
														`doctor_id`, 
														`qty`, 
														`discount`, 
														`unit_price`, 
														`hospital_price`, 
														`total_price`, 
														`created`,
														`created_by`, 
														`modified`, 
														`modified_by`) "
													. "VALUES (
															'" . $invoiceDetail['InvoiceDetail']['exchange_rate_id'] . "', 
															'" . $invoiceId . "', 
															'" . $invoiceDetail['InvoiceDetail']['type'] . "', 
															'" . $invoiceDetail['InvoiceDetail']['date_created'] . "', 
															'" . $rowLaboSeccondaryId['id'] . "', 
															'" . $invoiceDetail['InvoiceDetail']['doctor_id'] . "', 
															'" . $invoiceDetail['InvoiceDetail']['qty'] . "', 
															'" . $invoiceDetail['InvoiceDetail']['discount'] . "', 
															'" . $unitPrice . "',
															'" . $unitPrice . "', 
															'" . $totalPrice . "', 
															'" . $invoiceDetail['InvoiceDetail']['created'] . "', 
															'" . $invoiceDetail['InvoiceDetail']['created'] . "', 
															'".$invoiceDetail['InvoiceDetail']['modified']."', 
															'".$invoiceDetail['InvoiceDetail']['modified_by']."' ) ");  
										}
									}
								}else{
									//invalid on service
									echo MESSAGE_DATA_INVALID;
									exit;
								}
							}
						}else{
							//invalid on labo
							echo MESSAGE_DATA_INVALID;
            				exit;
						}
					}

					

					// else{
					// 	$queryProductId = mysql_query("SELECT id,sys_code FROM products WHERE products.id = '".$invoiceDetail['InvoiceDetail']['service_id']."' AND is_active = 1");
                    // 	if(mysql_num_rows($queryProductId)){
					// 		while($rowProductId = mysql_fetch_array($queryProductId)){
					// 			$sqlProductSeccondaryId = mysql_query("SELECT id FROM ".DB_SS_MONY_KID."products WHERE is_active = 1 AND sys_code = '".$rowProductId['sys_code']."'");
					// 			if(mysql_num_rows($sqlProductSeccondaryId)){
					// 				while($rowProductSeccondaryId = mysql_fetch_array($sqlProductSeccondaryId)){
					// 					$sqlSelectPrice = mysql_query("SELECT IFNULL(amount,0) FROM product_price WHERE product_id = {$rowProductSeccondaryId['id']}");
					// 					while($rowSelectPrice = mysql_fetch_array($sqlSelectPrice)){
					// 					$unitPrice = $rowSelectPrice['unit_price'];
					// 						$totalPrice = ($unitPrice * $invoiceDetail['InvoiceDetail']['qty']) - $invoiceDetail['InvoiceDetail']['discount'];
					// 						$totalAmount +=$totalPrice;
					// 						mysql_query("INSERT INTO ".DB_SS_MONY_KID."`invoice_details` 
					// 									(`exchange_rate_id`, 
					// 									`invoice_id`, 
					// 									`type`, 
					// 									`date_created`, 
					// 									`service_id`, 
					// 									`doctor_id`, 
					// 									`qty`, 
					// 									`discount`, 
					// 									`unit_price`, 
					// 									`hospital_price`, 
					// 									`total_price`, 
					// 									`created`,
					// 									`created_by`, 
					// 									`modified`, 
					// 									`modified_by`) "
					// 								. "VALUES (
					// 										'" . $invoiceDetail['InvoiceDetail']['exchange_rate_id'] . "', 
					// 										'" . $invoiceId . "', 
					// 										'" . $invoiceDetail['InvoiceDetail']['type'] . "', 
					// 										'" . $invoiceDetail['InvoiceDetail']['date_created'] . "', 
					// 										'" . $rowProductSeccondaryId['id'] . "', 
					// 										'" . $invoiceDetail['InvoiceDetail']['doctor_id'] . "', 
					// 										'" . $invoiceDetail['InvoiceDetail']['qty'] . "', 
					// 										'" . $invoiceDetail['InvoiceDetail']['discount'] . "', 
					// 										'" . $unitPrice . "',
					// 										'" . $unitPrice . "', 
					// 										'" . $totalPrice . "', 
					// 										'" . $invoiceDetail['InvoiceDetail']['created'] . "', 
					// 										'" . $invoiceDetail['InvoiceDetail']['created'] . "', 
					// 										'".$invoiceDetail['InvoiceDetail']['modified']."', 
					// 										'".$invoiceDetail['InvoiceDetail']['modified_by']."' ) ");  
					// 					}
					// 				}
					// 			}else{
					// 				//invalid on product
					// 				echo MESSAGE_DATA_INVALID;
					// 				exit;
					// 			}
					// 		}
					// 	}else{
					// 		//invalid on product
					// 		echo MESSAGE_DATA_INVALID;
            		// 		exit;
					// 	}
					// }
                }
				$totalAmountMedicine = 0;
				$unitPrice = 0;
				$sqlCheckQueue = mysql_query("SELECT sales_orders.id, (sales_orders.total_amount - IFNULL(sales_orders.discount,0)) AS totalAmount FROM sales_orders INNER JOIN invoices ON invoices.queue_id = sales_orders.queue_id WHERE sales_orders.status >0 AND invoices.is_void = 0 AND invoices.id = {$invId} LIMIT 1;");
				if(mysql_num_rows($sqlCheckQueue)){
					$newSalesOrderCode = $this->Helper->generateAutoSalesOrderCode('sales_orders', 'so_code', 7, 'INV', 'status > 0'); 
					while($rowCheckSalesOrder = mysql_fetch_array($sqlCheckQueue)){
						$insertSales = mysql_query("INSERT INTO ".DB_SS_MONY_KID."sales_orders (`sys_code`, `delivery_id`, `company_id`, `branch_id`, `location_group_id`, `location_id`, `customer_id`, `patient_id`, `queue_id`, `queue_doctor_id`, `customer_contact_id`, `currency_center_id`, `ar_id`, `payment_term_id`, `price_type_id`, `sales_rep_id`, `deliver_id`, `collector_id`, `consignment_id`, `consignment_code`, `quotation_id`, `quotation_number`, `order_id`, `order_number`, `customer_po_number`, `project`, `so_code`, `total_amount`, `total_amount_kh`, `total_amount_return`, `total_deposit`, `balance`, `discount`, `discount_percent`, `vat_chart_account_id`, `total_vat`, `vat_percent`, `vat_setting_id`, `vat_calculate`, `order_date`, `due_date`, `memo`, `shift_id`, `created`, `created_by`, `edited`, `edited_by`, `modified`, `modified_by`, `approved`, `approved_by`, `status`, `is_deposit_reference`, `is_approve`, `is_print`, `is_reprint`, `is_pos`)
                                            SELECT `sys_code`, `delivery_id`, `company_id`, `branch_id`, `location_group_id`, `location_id`, `customer_id`, `patient_id`, `queue_id`, `queue_doctor_id`, `customer_contact_id`, `currency_center_id`, `ar_id`, `payment_term_id`, `price_type_id`, `sales_rep_id`, `deliver_id`, `collector_id`, `consignment_id`, `consignment_code`, `quotation_id`, `quotation_number`, `order_id`, `order_number`, `customer_po_number`, `project`, '".$newSalesOrderCode."', `total_amount`, `total_amount_kh`, `total_amount_return`, `total_deposit`, `balance`, `discount`, `discount_percent`, `vat_chart_account_id`, `total_vat`, `vat_percent`, `vat_setting_id`, `vat_calculate`, `order_date`, `due_date`, `memo`, `shift_id`, `created`, `created_by`, `edited`, `edited_by`, `modified`, `modified_by`, `approved`, `approved_by`, `status`, `is_deposit_reference`, `is_approve`, `is_print`, `is_reprint`, `is_pos` FROM sales_orders WHERE id = " . $rowCheckSalesOrder['id'] . ";");
						$salesOrderId = mysql_insert_id();

						$sqlSalesOrderDetailOP = mysql_query("SELECT id,qty,total_price, discount_amount, product_id FROM sales_order_details WHERE sales_order_id = {$rowCheckSalesOrder['id']}");
						while($rowSalesOrderDetailOP = mysql_fetch_array($sqlSalesOrderDetailOP)){
							$queryProductId = mysql_query("SELECT id,sys_code FROM products WHERE products.id = '".$rowSalesOrderDetailOP['product_id']."' AND is_active = 1");
								if(mysql_num_rows($queryProductId)){
									while($rowProductId = mysql_fetch_array($queryProductId)){
									$sqlProductSeccondaryId = mysql_query("SELECT id FROM ".DB_SS_MONY_KID."products WHERE is_active = 1 AND sys_code = '".$rowProductId['sys_code']."'");
									if(mysql_num_rows($sqlProductSeccondaryId)){
										while($rowProductSeccondaryId = mysql_fetch_array($sqlProductSeccondaryId)){
											$sqlSelectPrice = mysql_query("SELECT IFNULL(amount,0) AS amount FROM ".DB_SS_MONY_KID."product_prices WHERE product_id = {$rowProductSeccondaryId['id']} ORDER BY amount desc LIMIT 1");
											if(mysql_num_rows($sqlSelectPrice)){
												while($rowSelectPrice = mysql_fetch_array($sqlSelectPrice)){
													$unitPrice = $rowSelectPrice['amount'];
													$totalPrice = ($unitPrice * $invoiceDetail['InvoiceDetail']['qty']) - $invoiceDetail['InvoiceDetail']['discount'];
													$totalAmountMedicine +=$totalPrice;
													$totalAmount +=$totalPrice;

													$insertSales = mysql_query("INSERT INTO ".DB_SS_MONY_KID."sales_order_details (`sys_code`, `sales_order_id`, `product_id`, `qty`, `qty_free`, `qty_uom_id`, `conversion`, `discount_id`, `discount_amount`, `discount_percent`, `unit_cost`, `unit_price`, `total_price`, `note`, `qty_remianing`, `lots_number`, `expired_date`)
													SELECT `sys_code`, '".$salesOrderId."', `product_id`, `qty`, `qty_free`, `qty_uom_id`, `conversion`, `discount_id`, `discount_amount`, `discount_percent`, `unit_cost`, '".$unitPrice."', '".$totalPrice."', `note`, `qty_remianing`, `lots_number`, `expired_date` FROM sales_order_details WHERE id = " . $rowSalesOrderDetailOP['id'] . ";");
												}
											}else{
												$totalAmountMedicine += $rowSalesOrderDetailOP['total_price'];
												$totalAmount += $rowSalesOrderDetailOP['total_price'];
												$insertSales = mysql_query("INSERT INTO ".DB_SS_MONY_KID."sales_order_details (`sys_code`, `sales_order_id`, `product_id`, `qty`, `qty_free`, `qty_uom_id`, `conversion`, `discount_id`, `discount_amount`, `discount_percent`, `unit_cost`, `unit_price`, `total_price`, `note`, `qty_remianing`, `lots_number`, `expired_date`)
												SELECT `sys_code`, '".$salesOrderId."', `product_id`, `qty`, `qty_free`, `qty_uom_id`, `conversion`, `discount_id`, `discount_amount`, `discount_percent`, `unit_cost`, `unit_price`, `total_price`, `note`, `qty_remianing`, `lots_number`, `expired_date` FROM sales_order_details WHERE id = " . $rowSalesOrderDetailOP['id'] . ";");
											}
										}
									}
								}
							}
						}
						// update is convert Secondary
						mysql_query("UPDATE ".DB_SS_MONY_KID."`sales_orders` SET total_amount ='".$totalAmountMedicine."' WHERE id=" . $salesOrderId . ";");
					}
				}
				
				// update is convert OP
                 mysql_query("UPDATE `invoices` SET is_convert = 1 WHERE id=" . $invId . ";");

                // update is convert Secondary
                 mysql_query("UPDATE ".DB_SS_MONY_KID."`invoices` SET is_convert = 1 , total_amount ='".$totalAmount."' WHERE id=" . $invoiceId . ";");
           
            }        
            $this->Helper->saveUserActivity($user['User']['id'], 'Reports', 'Convert Invoice', '1');
            echo MESSAGE_DATA_HAS_BEEN_SAVED;
            exit;
        }else{
            echo MESSAGE_DATA_INVALID;
            exit;
        }
    }

    /*
     * Service Referral report
     */
    function serviceReferral() {
        $this->layout = 'ajax';
        $this->loadModel('User');
        $this->loadModel('Section');
        $this->set('dateRange', $this->dateRange());
        $user = $this->getCurrentUser();
        $services = ClassRegistry::init('Service')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'name', 'fields' => array('Service.id', 'Service.name')));
        $referrals = ClassRegistry::init('Referral')->find('list', array('conditions' => 'is_active=1'));
        $this->set(compact('referrals', 'services'));
    }

    function serviceReferralResult() {
        $this->layout = 'ajax';
    }

    function serviceReferralAjax($data = null) {
        $this->layout = 'ajax';
        $data = explode(",", $data);
        $this->set("data", $data);
    }
    
}

?>