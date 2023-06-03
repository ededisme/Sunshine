<?php

class InventoryPhysicalsController extends AppController {

    var $name = 'InventoryPhysicals';
    var $components = array('Helper', 'Inventory');

    function index() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))),'conditions' => array('user_location_groups.user_id=' . $user['User']['id'], 'LocationGroup.is_active' => '1', 'LocationGroup.location_group_type_id != 1')));
        $this->set(compact('locationGroups'));
        $this->Helper->saveUserActivity($user['User']['id'], 'Inventory Physical', 'Dashboard');
    }

    function ajax($warehouse = "all", $status = "all", $date = "") {
        $this->layout = 'ajax';
        $this->set(compact('warehouse', 'status', 'date'));
    }

    function add() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', 
                          array('joins' => array(
                              array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id')), 
                              array('table' => 'locations', 'type' => 'inner', 'conditions' => array('locations.location_group_id=LocationGroup.id'))), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'], 'LocationGroup.is_active' => '1', 'LocationGroup.location_group_type_id != 1'), 'group' => 'LocationGroup.id'));
        $companies = ClassRegistry::init('Company')->find('all', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'fields' => array('Company.id', 'Company.name', 'Company.vat_calculate'), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('all', array('joins' => array(
                                array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id')),
                                array('table' => 'module_code_branches AS ModuleCodeBranch', 'type' => 'left', 'conditions' => array('ModuleCodeBranch.branch_id=Branch.id'))),'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id', 'Branch.currency_center_id', 'CurrencyCenter.symbol'), 'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $this->Helper->saveUserActivity($user['User']['id'], 'Inventory Physical', 'Add New');
        $this->set(compact('locationGroups', 'companies', 'branches'));
    }
    
    function addDetail() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $branches  = ClassRegistry::init('Branch')->find('all', array('joins' => array(
                                array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id')),
                                array('table' => 'module_code_branches AS ModuleCodeBranch', 'type' => 'left', 'conditions' => array('ModuleCodeBranch.branch_id=Branch.id'))),'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id', 'Branch.currency_center_id', 'CurrencyCenter.symbol'), 'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $this->set(compact('branches'));
    }
    
    function edit($id) {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $this->data = $this->InventoryPhysical->read(null, $id);
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', 
                          array('joins' => array(
                              array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id')), 
                              array('table' => 'locations', 'type' => 'inner', 'conditions' => array('locations.location_group_id=LocationGroup.id'))),
                          'conditions' => array('user_location_groups.user_id=' . $user['User']['id'], 'LocationGroup.is_active' => '1', 'LocationGroup.location_group_type_id != 1'),
                          'group' => 'LocationGroup.id'
                          ));
        $pgroups   = ClassRegistry::init('Pgroup')->find('list', array('order' => 'id', 'conditions' => array('is_active' => 1, 'id IN (SELECT pgroup_id FROM pgroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].'))')));
        $companies = ClassRegistry::init('Company')->find('all',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))
                            ),
                            'fields' => array('Company.id', 'Company.name', 'Company.vat_calculate'),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        ));
        $branches = ClassRegistry::init('Branch')->find('all',
                        array(
                            'joins' => array(
                                array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id')),
                                array('table' => 'module_code_branches AS ModuleCodeBranch', 'type' => 'left', 'conditions' => array('ModuleCodeBranch.branch_id=Branch.id'))
                            ),
                            'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id', 'ModuleCodeBranch.adj_code', 'Branch.currency_center_id', 'CurrencyCenter.symbol'),
                            'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])
                        ));
        $this->Helper->saveUserActivity($user['User']['id'], 'Inventory Physical', 'Edit', $id);
        $this->set(compact('id', 'pgroups', 'locationGroups', 'companies', 'branches'));
    }

    function editDetail($InventoryPhysicalId = null, $locationGroup) {
        $this->layout = 'ajax';
        $InventoryPhysical = $this->InventoryPhysical->read(null, $InventoryPhysicalId);
        $user = $this->getCurrentUser();
        $branches  = ClassRegistry::init('Branch')->find('all', array('joins' => array(
                                array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id')),
                                array('table' => 'module_code_branches AS ModuleCodeBranch', 'type' => 'left', 'conditions' => array('ModuleCodeBranch.branch_id=Branch.id'))),'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id', 'Branch.currency_center_id', 'CurrencyCenter.symbol'), 'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $this->set(compact('branches', 'InventoryPhysicalId', 'InventoryPhysical', 'locationGroup'));
    }

    function uom() {
        $this->layout = 'ajax';
    }

    function save() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if ($this->Helper->checkDouplicate('code', 'inventory_physicals', $this->data['InventoryPhysical']['code'], 'status > 0')) {
            $this->Helper->saveUserActivity($user['User']['id'], 'Inventory Physical', 'Save Add New (Reference ready existed)');
            echo 'duplicate';
            exit();
        }
        if (preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/', $this->data['InventoryPhysical']['date'])) {
            $this->data['InventoryPhysical']['date'] = $this->Helper->dateConvert($this->data['InventoryPhysical']['date']);
        }
        if (!empty($_POST['qty_diff']) && sizeof($_POST['qty_diff']) > 0) {
            $r = 0;
            $restCode = array();
            $dateNow  = date("Y-m-d H:i:s");
            $adjAccount = ClassRegistry::init('AccountType')->findById(3);
            $adjAccountId = $adjAccount['AccountType']['chart_account_id'];
            // Insert Into Cycle Products
            $this->InventoryPhysical->create();
            $this->data['InventoryPhysical']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
            $this->data['InventoryPhysical']['created']    = $dateNow;
            $this->data['InventoryPhysical']['date']               = $this->data['InventoryPhysical']['date'];
            $this->data['InventoryPhysical']['location_group_id']  = $this->data['InventoryPhysical']['location_group_id'];
            $this->data['InventoryPhysical']['company_id']         = $this->data['InventoryPhysical']['company_id'];
            $this->data['InventoryPhysical']['branch_id']          = $this->data['InventoryPhysical']['branch_id'];
            $this->data['InventoryPhysical']['deposit_to']         = $adjAccountId;
            $this->data['InventoryPhysical']['note']               = $this->data['InventoryPhysical']['note'];
            $this->data['InventoryPhysical']['created_by']         = $user['User']['id'];
            $this->InventoryPhysical->save($this->data);
            $InventoryPhysicalId = $this->InventoryPhysical->id;
            // Get Module Code
            $modCode = $this->Helper->getModuleCode($this->data['InventoryPhysical']['code'], $InventoryPhysicalId, 'code', 'inventory_physicals', 'status >= 0 AND branch_id = '.$this->data['InventoryPhysical']['branch_id']);
            // Updaet Module Code
            $this->data['InventoryPhysical']['code'] = $modCode;
            mysql_query("UPDATE inventory_physicals SET code = '".$modCode."' WHERE id = ".$InventoryPhysicalId);
            // Convert to REST
            $restCode[$r] = $this->Helper->convertToDataSync($this->data['InventoryPhysical'], 'inventory_physicals');
            $restCode[$r]['modified'] = $dateNow;
            $restCode[$r]['dbtodo']   = 'inventory_physicals';
            $restCode[$r]['actodo']   = 'is';
            $r++;
            // Insert Into Cycle Product Details
            $this->loadModel('InventoryPhysicalDetail');
            $this->loadModel('StockOrder');
            for ($i = 0; $i < sizeof($_POST['qty_diff']); $i++) {
                if ($this->Helper->preventInput($_POST['qty_diff'][$i]) != '') {
                    $lotNum = $this->Helper->preventInput($_POST['lots_number'][$i])!=''?$this->Helper->preventInput($_POST['lots_number'][$i]):0;
                    $expDate = $this->Helper->preventInput($_POST['expired_date'][$i])!=''?$this->Helper->preventInput($_POST['expired_date'][$i]):'0000-00-00';
                    $this->InventoryPhysicalDetail->create();
                    $InventoryPhysicalDetail = array();
                    $InventoryPhysicalDetail['InventoryPhysicalDetail']['inventory_physical_id'] = $InventoryPhysicalId;
                    $InventoryPhysicalDetail['InventoryPhysicalDetail']['product_id']   = $this->Helper->preventInput($_POST['product_id'][$i]);
                    $InventoryPhysicalDetail['InventoryPhysicalDetail']['location_id']  = $this->Helper->preventInput($_POST['location_id'][$i]);
                    $InventoryPhysicalDetail['InventoryPhysicalDetail']['lots_number']  = $lotNum;
                    $InventoryPhysicalDetail['InventoryPhysicalDetail']['expired_date'] = $expDate;
                    $InventoryPhysicalDetail['InventoryPhysicalDetail']['qty_diff']     = $this->Helper->preventInput($_POST['qty_diff'][$i]);
                    $this->InventoryPhysicalDetail->save($InventoryPhysicalDetail);
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($InventoryPhysicalDetail['InventoryPhysicalDetail'], 'inventory_physical_details');
                    $restCode[$r]['dbtodo'] = 'inventory_physical_details';
                    $restCode[$r]['actodo'] = 'is';
                    $r++;
                    if($this->Helper->preventInput($_POST['qty_diff'][$i]) < 0){
                        $this->StockOrder->create();
                        $tmpInventoryPhysical = array();
                        $tmpInventoryPhysical['StockOrder']['sys_code'] = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                        $tmpInventoryPhysical['StockOrder']['inventory_physical_id'] = $InventoryPhysicalId;
                        $tmpInventoryPhysical['StockOrder']['product_id']    = $this->Helper->preventInput($_POST['product_id'][$i]);
                        $tmpInventoryPhysical['StockOrder']['location_group_id']   = $this->data['InventoryPhysical']['location_group_id'];
                        $tmpInventoryPhysical['StockOrder']['location_id']   = $this->Helper->preventInput($_POST['location_id'][$i]);
                        $tmpInventoryPhysical['StockOrder']['lots_number']   = $lotNum;
                        $tmpInventoryPhysical['StockOrder']['expired_date']  = $expDate;
                        $tmpInventoryPhysical['StockOrder']['date'] = $this->data['InventoryPhysical']['date'];
                        $tmpInventoryPhysical['StockOrder']['qty']  = $this->Helper->preventInput($_POST['qty_diff'][$i]) * -1;
                        $this->StockOrder->save($tmpInventoryPhysical);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($tmpInventoryPhysical['StockOrder'], 'stock_orders');
                        $restCode[$r]['dbtodo'] = 'stock_orders';
                        $restCode[$r]['actodo'] = 'is';
                        $r++;
                        $this->Inventory->saveGroupQtyOrder($this->data['InventoryPhysical']['location_group_id'], $this->Helper->preventInput($_POST['location_id'][$i]), $_POST['product_id'][$i], $lotNum, $expDate, $this->Helper->preventInput($_POST['qty_diff'][$i] * -1), $this->data['InventoryPhysical']['date'], '+');
                        // Convert to REST
                        $restCode[$r]['group']    = $this->Helper->getSQLSyncCode("location_groups", $this->data['InventoryPhysical']['location_group_id']);
                        $restCode[$r]['location'] = $this->Helper->getSQLSyncCode("locations", $_POST['location_id'][$i]);
                        $restCode[$r]['product']  = $this->Helper->getSQLSyncCode("products", $_POST['product_id'][$i]);
                        $restCode[$r]['lots']   = $lotNum;
                        $restCode[$r]['expd']   = $expDate;
                        $restCode[$r]['qty']    = $this->Helper->preventInput($_POST['qty_diff'][$i] * -1);
                        $restCode[$r]['date']   = $this->data['InventoryPhysical']['date'];
                        $restCode[$r]['syml']   = '+';
                        $restCode[$r]['dbtype'] = 'saveOrder';
                        $restCode[$r]['actodo'] = 'inv';
                        $r++;
                    }
                }
            }
            // Save File Send
            $this->Helper->sendFileToSync($restCode, 0, 0);
            // Save User Activity
            $this->Helper->saveUserActivity($user['User']['id'], 'Inventory Physical', 'Save Add New', $InventoryPhysicalId);
            echo $InventoryPhysicalId;
        } else {
            $this->Helper->saveUserActivity($user['User']['id'], 'Inventory Physical', 'Save Add New (Error)');
            echo 'error';
        }
        exit;
    }

    function saveEdit() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $invAdj = $this->InventoryPhysical->read(null, $this->data['InventoryPhysical']['id']);
        if ($invAdj['InventoryPhysical']['status'] != 1) {
            $this->Helper->saveUserActivity($user['User']['id'], 'Inventory Physical', 'Save Edit (Error Status)', $this->data['InventoryPhysical']['id']);
            echo 'error';
            exit;
        }
        if (!empty($_POST['qty_diff']) && sizeof($_POST['qty_diff']) > 0) {
            $this->loadModel('StockOrder');
            $statuEdit = "-1";
            $dateNow   = date("Y-m-d H:i:s");
            $rb = 0;
            $restBackCode  = array();
            $adjAccount = ClassRegistry::init('AccountType')->findById(3);
            $adjAccountId = $adjAccount['AccountType']['chart_account_id'];
            // Reset Stock Order
            $sqlResetOrder = mysql_query("SELECT * FROM stock_orders WHERE `inventory_physical_id`=".$this->data['InventoryPhysical']['id'].";");
            while($rowResetOrder = mysql_fetch_array($sqlResetOrder)){
                $this->Inventory->saveGroupQtyOrder($rowResetOrder['location_group_id'], $rowResetOrder['location_id'], $rowResetOrder['product_id'], $rowResetOrder['lots_number'], $rowResetOrder['expired_date'], $rowResetOrder['qty'], $rowResetOrder['date'], '-');
                // Convert to REST
                $restBackCode[$rb]['group']    = $this->Helper->getSQLSyncCode("location_groups", $rowResetOrder['location_group_id']);
                $restBackCode[$rb]['location'] = $this->Helper->getSQLSyncCode("locations", $rowResetOrder['location_id']);
                $restBackCode[$rb]['product']  = $this->Helper->getSQLSyncCode("products", $rowResetOrder['product_id']);
                $restBackCode[$rb]['lots']   = $rowResetOrder['lots_number'];
                $restBackCode[$rb]['expd']   = $rowResetOrder['expired_date'];
                $restBackCode[$rb]['qty']    = $rowResetOrder['qty'];
                $restBackCode[$rb]['date']   = $rowResetOrder['date'];
                $restBackCode[$rb]['syml']   = '-';
                $restBackCode[$rb]['dbtype'] = 'saveOrder';
                $restBackCode[$rb]['actodo'] = 'inv';
                $rb++;
            }
            // Detele Tmp Stock Order
            mysql_query("DELETE FROM `stock_orders` WHERE  `inventory_physical_id`=".$this->data['InventoryPhysical']['id'].";");
            // Convert to REST
            $restBackCode[$rb]['dbtodo'] = 'stock_orders';
            $restBackCode[$rb]['actodo'] = 'dt';
            $restBackCode[$rb]['con']    = "inventory_physical_id = ".$invAdj['InventoryPhysical']['sys_code'];
            $rb++;
            if($this->data['InventoryPhysical']['company_id'] != $invAdj['InventoryPhysical']['company_id']){
                $statuEdit = 0;
            }
            // Update Status Request Stock Edit
            $this->InventoryPhysical->updateAll(
                    array('InventoryPhysical.status' => $statuEdit, "modified_by"=>$user['User']['id']), array('InventoryPhysical.id' => $this->data['InventoryPhysical']['id'])
            );
            // Convert to REST
            $restBackCode[$rb]['status']   = $statuEdit;
            $restBackCode[$rb]['modified'] = $dateNow;
            $restBackCode[$rb]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
            $restBackCode[$rb]['dbtodo'] = 'inventory_physicals';
            $restBackCode[$rb]['actodo'] = 'ut';
            $restBackCode[$rb]['con']    = "sys_code = '".$invAdj['InventoryPhysical']['sys_code']."'";
            $rb++;
            // Save File Send Delete
            $this->Helper->sendFileToSync($restBackCode, 0, 0);
            // Insert New Request Stock
            $r = 0;
            $restCode  = array();
            $this->InventoryPhysical->create();
            $this->data['InventoryPhysical']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
            $this->data['InventoryPhysical']['created']    = $dateNow;
            $this->data['InventoryPhysical']['code']  = $invAdj['InventoryPhysical']['code'];
            $this->data['InventoryPhysical']['date']       = $this->data['InventoryPhysical']['date'];
            $this->data['InventoryPhysical']['location_group_id'] = $this->data['InventoryPhysical']['location_group_id'];
            $this->data['InventoryPhysical']['company_id'] = $this->data['InventoryPhysical']['company_id'];
            $this->data['InventoryPhysical']['branch_id']  = $this->data['InventoryPhysical']['branch_id'];
            $this->data['InventoryPhysical']['deposit_to'] = $adjAccountId;
            $this->data['InventoryPhysical']['note']       = $this->data['InventoryPhysical']['note'];
            $this->data['InventoryPhysical']['created_by'] = $user['User']['id'];
            $this->data['InventoryPhysical']['status']     = 1;
            $this->InventoryPhysical->save($this->data);
            $InventoryPhysicalId = $this->InventoryPhysical->id;
            if($this->data['InventoryPhysical']['company_id'] != $invAdj['InventoryPhysical']['company_id']){
                // Get Module Code
                $modCode = $this->Helper->getModuleCode($this->data['InventoryPhysical']['code'], $InventoryPhysicalId, 'code', 'inventory_physicals', 'status >= 0 AND branch_id = '.$this->data['InventoryPhysical']['branch_id']);
                // Updaet Module Code
                $this->data['InventoryPhysical']['code'] = $modCode;
                mysql_query("UPDATE inventory_physicals SET code = '".$modCode."' WHERE id = ".$InventoryPhysicalId);
            }
            // Convert to REST
            $restCode[$r] = $this->Helper->convertToDataSync($this->data['InventoryPhysical'], 'inventory_physicals');
            $restCode[$r]['modified'] = $dateNow;
            $restCode[$r]['dbtodo']   = 'inventory_physicals';
            $restCode[$r]['actodo']   = 'is';
            $r++;
            // Insert Into Cycle Product Details
            $this->loadModel('InventoryPhysicalDetail');
            $this->loadModel('StockOrder');
            for ($i = 0; $i < sizeof($_POST['qty_diff']); $i++) {
                if ($this->Helper->preventInput($_POST['qty_diff'][$i]) != '') {
                    $lotNum = $this->Helper->preventInput($_POST['lots_number'][$i])!=''?$this->Helper->preventInput($_POST['lots_number'][$i]):0;
                    $expDate = $this->Helper->preventInput($_POST['expired_date'][$i])!=''?$this->Helper->preventInput($_POST['expired_date'][$i]):'0000-00-00';
                    $this->InventoryPhysicalDetail->create();
                    $InventoryPhysicalDetail = array();
                    $InventoryPhysicalDetail['InventoryPhysicalDetail']['inventory_physical_id'] = $InventoryPhysicalId;
                    $InventoryPhysicalDetail['InventoryPhysicalDetail']['product_id']   = $this->Helper->preventInput($_POST['product_id'][$i]);
                    $InventoryPhysicalDetail['InventoryPhysicalDetail']['location_id']  = $this->Helper->preventInput($_POST['location_id'][$i]);
                    $InventoryPhysicalDetail['InventoryPhysicalDetail']['lots_number']  = $lotNum;
                    $InventoryPhysicalDetail['InventoryPhysicalDetail']['expired_date'] = $expDate;
                    $InventoryPhysicalDetail['InventoryPhysicalDetail']['qty_diff']     = $this->Helper->preventInput($_POST['qty_diff'][$i]);
                    $this->InventoryPhysicalDetail->save($InventoryPhysicalDetail);
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($InventoryPhysicalDetail['InventoryPhysicalDetail'], 'inventory_physical_details');
                    $restCode[$r]['dbtodo'] = 'inventory_physical_details';
                    $restCode[$r]['actodo'] = 'is';
                    $r++;
                    if($this->Helper->preventInput($_POST['qty_diff'][$i]) < 0){
                        $this->StockOrder->create();
                        $tmpInventoryPhysical = array();
                        $tmpInventoryPhysical['StockOrder']['inventory_physical_id'] = $InventoryPhysicalId;
                        $tmpInventoryPhysical['StockOrder']['product_id']    = $this->Helper->preventInput($_POST['product_id'][$i]);
                        $tmpInventoryPhysical['StockOrder']['location_group_id']   = $this->data['InventoryPhysical']['location_group_id'];
                        $tmpInventoryPhysical['StockOrder']['location_id']   = $this->Helper->preventInput($_POST['location_id'][$i]);
                        $tmpInventoryPhysical['StockOrder']['lots_number']   = $lotNum;
                        $tmpInventoryPhysical['StockOrder']['expired_date']  = $expDate;
                        $tmpInventoryPhysical['StockOrder']['date'] = $this->data['InventoryPhysical']['date'];
                        $tmpInventoryPhysical['StockOrder']['qty']  = $this->Helper->preventInput($_POST['qty_diff'][$i]) * -1;
                        $this->StockOrder->save($tmpInventoryPhysical);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($tmpInventoryPhysical['StockOrder'], 'stock_orders');
                        $restCode[$r]['dbtodo'] = 'stock_orders';
                        $restCode[$r]['actodo'] = 'is';
                        $r++;
                        $this->Inventory->saveGroupQtyOrder($this->data['InventoryPhysical']['location_group_id'], $this->Helper->preventInput($_POST['location_id'][$i]), $_POST['product_id'][$i], $lotNum, $expDate, $this->Helper->preventInput($_POST['qty_diff'][$i] * -1), $this->data['InventoryPhysical']['date'], '+');
                        // Convert to REST
                        $restCode[$r]['group']    = $this->Helper->getSQLSyncCode("location_groups", $this->data['InventoryPhysical']['location_group_id']);
                        $restCode[$r]['location'] = $this->Helper->getSQLSyncCode("locations", $_POST['location_id'][$i]);
                        $restCode[$r]['product']  = $this->Helper->getSQLSyncCode("products", $_POST['product_id'][$i]);
                        $restCode[$r]['lots']   = $lotNum;
                        $restCode[$r]['expd']   = $expDate;
                        $restCode[$r]['qty']    = $this->Helper->preventInput($_POST['qty_diff'][$i] * -1);
                        $restCode[$r]['date']   = $this->data['InventoryPhysical']['date'];
                        $restCode[$r]['syml']   = '+';
                        $restCode[$r]['dbtype'] = 'saveOrder';
                        $restCode[$r]['actodo'] = 'inv';
                        $r++;
                    }
                }
            }
            // Save File Send
            $this->Helper->sendFileToSync($restCode, 0, 0);
            // Save User Activity
            $this->Helper->saveUserActivity($user['User']['id'], 'Inventory Physical', 'Save Edit', $this->data['InventoryPhysical']['id'], $InventoryPhysicalId);
            echo $InventoryPhysicalId;
        } else {
            $this->Helper->saveUserActivity($user['User']['id'], 'Inventory Physical', 'Save Edit (Error)', $this->data['InventoryPhysical']['id']);
            echo 'error';
        }
        exit;
    }
    
    function approve() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $queryAdj = mysql_query("SELECT inventory_physical_id FROM inventory_physical_crontabs WHERE inventory_physical_id=" . $this->Helper->preventInput($_POST['inventory_physical_id']));
        if(!mysql_num_rows($queryAdj)){
            mysql_query("INSERT INTO inventory_physical_crontabs (inventory_physical_id,status,created,created_by) VALUES ('" . $this->Helper->preventInput($_POST['inventory_physical_id']) . "',1,now(),'" . $user['User']['id'] . "')");
        }
        shell_exec("wget -b -q -O public/logs/approveInventoryPhysical?id=".$this->Helper->preventInput($_POST['inventory_physical_id'])." '".LINK_URL."approveInventoryPhysical/" . $this->Helper->preventInput($_POST['inventory_physical_id']) . "?user=".$user['User']['id']."' ".LINK_URL_SSL);
        $this->Helper->saveUserActivity($user['User']['id'], 'Inventory Physical', 'Click Approve', $_POST['inventory_physical_id']);
        echo $_POST['inventory_physical_id'];
        exit();
    }

    function printInvoice($id = null) {
        $this->layout = 'ajax';
        $this->set('id', $this->Helper->preventInput($id));
    }
    
    function delete($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $r = 0;
        $restCode = array();
        $dateNow  = date("Y-m-d H:i:s");
        $user = $this->getCurrentUser();
        $this->data = $this->InventoryPhysical->read(null, $id);
        mysql_query("UPDATE `inventory_physicals` SET `status`=0, `modified`='".$dateNow."', `modified_by`=".$user['User']['id']." WHERE `id`=".$id.";");
        // Convert to REST
        $restCode[$r]['status']      = 0;
        $restCode[$r]['modified']    = $dateNow;
        $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
        $restCode[$r]['dbtodo'] = 'inventory_physicals';
        $restCode[$r]['actodo'] = 'ut';
        $restCode[$r]['con']    = "sys_code = '".$this->data['InventoryPhysical']['sys_code']."'";
        $r++;
        // Reset Stock Order
        $sqlResetOrder = mysql_query("SELECT * FROM stock_orders WHERE `inventory_physical_id`=".$id.";");
        while($rowResetOrder = mysql_fetch_array($sqlResetOrder)){
            $this->Inventory->saveGroupQtyOrder($rowResetOrder['location_group_id'], $rowResetOrder['location_id'], $rowResetOrder['product_id'], $rowResetOrder['lots_number'], $rowResetOrder['expired_date'], $rowResetOrder['qty'], $rowResetOrder['date'], '-');
            // Convert to REST
            $restCode[$r]['group']    = $this->Helper->getSQLSyncCode("location_groups", $rowResetOrder['location_group_id']);
            $restCode[$r]['location'] = $this->Helper->getSQLSyncCode("locations", $rowResetOrder['location_id']);
            $restCode[$r]['product']  = $this->Helper->getSQLSyncCode("products", $rowResetOrder['product_id']);
            $restCode[$r]['lots']   = $rowResetOrder['lots_number'];
            $restCode[$r]['expd']   = $rowResetOrder['expired_date'];
            $restCode[$r]['qty']    = $rowResetOrder['qty'];
            $restCode[$r]['date']   = $rowResetOrder['date'];
            $restCode[$r]['syml']   = '-';
            $restCode[$r]['dbtype'] = 'saveOrder';
            $restCode[$r]['actodo'] = 'inv';
            $r++;
        }
        // Detele Tmp Stock Order
        mysql_query("DELETE FROM `stock_orders` WHERE `inventory_physical_id`=".$id.";");
        // Convert to REST
        $restCode[$r]['dbtodo'] = 'stock_orders';
        $restCode[$r]['actodo'] = 'dt';
        $restCode[$r]['con']    = "inventory_physical_id = ".$this->data['InventoryPhysical']['sys_code'];
        // Save File Send
        $this->Helper->sendFileToSync($restCode, 0, 0);
        // Save User Activity
        $this->Helper->saveUserActivity($user['User']['id'], 'Inventory Physical', 'Delete', $id);
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }
    
    function searchProductSku($companyId, $branchId, $code){
        if (!$code) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        $locationGroup = $this->Helper->preventInput($_POST['location_group_id']);
        $product = ClassRegistry::init('Product')->find('first', array(
            'fields' => array(
                'Product.id',
                'Product.name',
                'Product.barcode',
                'Product.code',
                'Product.small_val_uom',
                'Product.price_uom_id',
                'Product.is_expired_date',
                'Product.is_lots',
                'Uom.name',
                'Uom.abbr'
            ),
            'conditions' => array(
                'Product.company_id' => $companyId,
                "OR" => array(
                        'trim(Product.code) = ("' . mysql_real_escape_string(trim($code)) . '")',
                        'trim(Product.barcode) = ("' . mysql_real_escape_string(trim($code)) . '")',
                        'trim(Product.name) = ("' . mysql_real_escape_string(trim($code)) . '")',
                        'trim(ProductWithSku.sku) = ("' . mysql_real_escape_string(trim($code)) . '")'
                    ),
                'Product.is_active' => 1,
                'Product.is_packet' => 0
            ),
            'joins' => array(
                            array(
                                'table' => 'uoms',
                                'type' => 'INNER',
                                'alias' => 'Uom',
                                'conditions' => array(
                                    'Uom.id = Product.price_uom_id'
                                )
                            ),
                            array(
                                'table' => 'product_branches',
                                'type' => 'INNER',
                                'alias' => 'ProductBranch',
                                'conditions' => array(
                                    'ProductBranch.product_id = Product.id',
                                    'ProductBranch.branch_id = '.$branchId
                            )),
                            array(
                                'table' => 'product_pgroups',
                                'type' => 'INNER',
                                'alias' => 'ProductPgroup',
                                'conditions' => array('ProductPgroup.product_id = Product.id')
                            ),
                            array(
                                'table' => 'pgroups',
                                'type' => 'INNER',
                                'alias' => 'Pgroup',
                                'conditions' => array(
                                    'Pgroup.id = ProductPgroup.pgroup_id',
                                    '(Pgroup.user_apply = 0 OR (Pgroup.user_apply = 1 AND Pgroup.id IN (SELECT pgroup_id FROM user_pgroups WHERE user_id = '.$user['User']['id'].')))'
                                )
                            ),
                            array(
                                'table' => 'product_with_skus',
                                'type' => 'LEFT',
                                'alias' => 'ProductWithSku',
                                'conditions' => array(
                                    'ProductWithSku.product_id = Product.id'
                                )
                            )
                      ),
            'group' => array(
                'Product.id',
                'Product.code',
                'Product.name',
                'Product.price_uom_id',
            )
        ));
        if(!empty($product)){
            $sqlLoc = mysql_query("SELECT id, name FROM locations WHERE is_active = 1 AND location_group_id = {$locationGroup}");
            while($rowLoc = mysql_fetch_array($sqlLoc)){
                $product['Location'][] = $rowLoc['id']."--".$rowLoc['name'];
            }
            $smallUomLabel = "";
            $qry = mysql_query("SELECT (SELECT abbr FROM uoms WHERE id = uom_conversions.to_uom_id) as abbr FROM uom_conversions WHERE from_uom_id = " . $product['Product']['price_uom_id'] . " AND is_small_uom = 1 AND is_active = 1");
            while (@$d = mysql_fetch_array($qry)) {
                $smallUomLabel = $d['abbr'];
            }
            $product['Product']['code']    = $product['Product']['code'];
            $product['Product']['barcode'] = $product['Product']['barcode'];
            $product['Product']['name']    = $product['Product']['name'];
            $product['Product']['small_uom_label'] = $smallUomLabel;
        }
        echo json_encode($product);
        exit;
    }
    
    function searchProductPuc($companyId, $branchId, $code){
        if (!$code) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        $locationGroup = $this->Helper->preventInput($_POST['location_group_id']);
        $product = ClassRegistry::init('Product')->find('first', array(
            'fields' => array(
                'Product.id',
                'Product.name',
                'Product.barcode',
                'Product.code',
                'Product.small_val_uom',
                'Product.price_uom_id',
                'Product.is_expired_date',
                'Product.is_lots',
                'Uom.name',
                'Uom.abbr'
            ),
            'conditions' => array(
                'Product.company_id' => $companyId,
                "OR" => array(
                        'trim(Product.code) = ("' . mysql_real_escape_string(trim($code)) . '")',
                        'trim(Product.barcode) = ("' . mysql_real_escape_string(trim($code)) . '")',
                        'trim(Product.name) = ("' . mysql_real_escape_string(trim($code)) . '")',
                        'trim(ProductWithSku.sku) = ("' . mysql_real_escape_string(trim($code)) . '")'
                    ),
                'Product.is_active' => 1,
                'Product.is_packet' => 0
            ),
            'joins' => array(
                            array(
                                'table' => 'uoms',
                                'type' => 'INNER',
                                'alias' => 'Uom',
                                'conditions' => array(
                                    'Uom.id = Product.price_uom_id'
                                )
                            ),
                            array(
                                'table' => 'product_pgroups',
                                'type' => 'INNER',
                                'alias' => 'ProductPgroup',
                                'conditions' => array('ProductPgroup.product_id = Product.id')
                            ),
                            array(
                                'table' => 'product_branches',
                                'type' => 'INNER',
                                'alias' => 'ProductBranch',
                                'conditions' => array(
                                    'ProductBranch.product_id = Product.id',
                                    'ProductBranch.branch_id = '.$branchId
                            )),
                            array(
                                'table' => 'pgroups',
                                'type' => 'INNER',
                                'alias' => 'Pgroup',
                                'conditions' => array(
                                    'Pgroup.id = ProductPgroup.pgroup_id',
                                    '(Pgroup.user_apply = 0 OR (Pgroup.user_apply = 1 AND Pgroup.id IN (SELECT pgroup_id FROM user_pgroups WHERE user_id = '.$user['User']['id'].')))'
                                )
                            ),
                            array(
                                'table' => 'product_with_skus',
                                'type' => 'LEFT',
                                'alias' => 'ProductWithSku',
                                'conditions' => array(
                                    'ProductWithSku.product_id = Product.id'
                                )
                            )
                      ),
            'group' => array(
                'Product.id',
                'Product.code',
                'Product.name',
                'Product.price_uom_id',
            )
        ));
        if(!empty($product)){
            $sqlLoc = mysql_query("SELECT id, name FROM locations WHERE is_active = 1 AND location_group_id = {$locationGroup}");
            while($rowLoc = mysql_fetch_array($sqlLoc)){
                $product['Location'][] = $rowLoc['id']."--".$rowLoc['name'];
            }
            $smallUomLabel = "";
            $qry = mysql_query("SELECT (SELECT abbr FROM uoms WHERE id = uom_conversions.to_uom_id) as abbr FROM uom_conversions WHERE from_uom_id = " . $product['Product']['price_uom_id'] . " AND is_small_uom = 1 AND is_active = 1");
            while (@$d = mysql_fetch_array($qry)) {
                $smallUomLabel = $d['abbr'];
            }
            $product['Product']['code']    = $product['Product']['code'];
            $product['Product']['barcode'] = $product['Product']['barcode'];
            $product['Product']['name']    = $product['Product']['name'];
            $product['Product']['small_uom_label'] = $smallUomLabel;
        }
        echo json_encode($product);
        exit;
    }
    
    function searchProduct() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $joinProductgroup  = array(
                             'table' => 'product_pgroups',
                             'type' => 'INNER',
                             'alias' => 'ProductPgroup',
                             'conditions' => array('ProductPgroup.product_id = Product.id')
                             );
        $joinProductBranch  = array(
                             'table' => 'product_branches',
                             'type' => 'INNER',
                             'alias' => 'ProductBranch',
                             'conditions' => array(
                                 'ProductBranch.product_id = Product.id',
                                 'ProductBranch.branch_id IN (SELECT branch_id FROM user_branches WHERE user_id = '.$user['User']['id'].')'
                             ));
        $joinPgroup  = array(
                             'table' => 'pgroups',
                             'type' => 'INNER',
                             'alias' => 'Pgroup',
                             'conditions' => array(
                                 'Pgroup.id = ProductPgroup.pgroup_id',
                                 '(Pgroup.user_apply = 0 OR (Pgroup.user_apply = 1 AND Pgroup.id IN (SELECT pgroup_id FROM user_pgroups WHERE user_id = '.$user['User']['id'].')))'
                             )
                          );
        $joins = array(
            $joinProductgroup,
            $joinPgroup,
            $joinProductBranch
        );
        $products = ClassRegistry::init('Product')->find('all', array(
                        'conditions' => array('OR' => array(
                                'Product.code LIKE' => '%' . trim($this->params['url']['q']) . '%',
                                'Product.barcode LIKE' => '%' . trim($this->params['url']['q']) . '%',
                                'Product.name LIKE' => '%' . trim($this->params['url']['q']) . '%',
                            ), 'Product.company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')'
                            , 'Product.is_active' => 1
                            , 'Product.is_packet' => 0
                        ),
                        'joins' => $joins,
                        'group' => array(
                            'Product.id'
                        )
                    ));
        $this->set(compact('products'));
    }
    
    function product($companyId = null, $branchId = null, $locationGroup, $category = null){
        $this->layout = 'ajax';
        $this->set(compact("companyId", "branchId", "locationGroup", "category"));
    }
    
    function productAjax($companyId = null, $branchId = null, $locationGroup, $category = null){
        $this->layout = 'ajax';
        $this->set(compact("companyId", "branchId", "locationGroup", "category"));
    }
    
    function getTotalQtyOnHand($location, $adjDate, $lotsNumber, $expired = null){
        $this->layout = 'ajax';
        if(!empty($location) && !empty($adjDate) && !empty($expired) && !empty($_POST['product_id'])){
            $invAdjId = $_POST['inv_adj_id'];
            $dateNow  = date("Y-m-d");
            $productId = $this->Helper->preventInput($_POST['product_id']);
            if(empty($expired) || $expired == 'none'){
                $expired = '0000-00-00';
            }
            if($adjDate < $dateNow){
                $sqlTotal = mysql_query("SELECT SUM((total_cycle + total_to_in + total_cm + total_pb + total_cus_consign_in) - (total_so + total_pos + total_pbc + total_to_out + total_cus_consign_out + total_order)) AS total_qty FROM `".$this->Helper->preventInput($location)."_inventory_total_details` WHERE product_id = {$productId} AND lots_number = '{$this->Helper->preventInput($lotsNumber)}' AND expired_date = '{$this->Helper->preventInput($expired)}' AND date <= '".$adjDate."' GROUP BY product_id, expired_date");
            }else{
                $sqlTotal = mysql_query("SELECT SUM(total_qty - total_order) AS total_qty FROM `".$this->Helper->preventInput($location)."_inventory_totals` WHERE product_id = {$productId} AND lots_number = '{$this->Helper->preventInput($lotsNumber)}' AND expired_date = '{$this->Helper->preventInput($expired)}' GROUP BY product_id, expired_date");
            }
            // Get Total Qty in Order
            $totalOrder = 0;
            if(!empty($invAdjId)){
                $InventoryPhysical = $this->InventoryPhysical->read(null, $invAdjId);
                $sqlOrder = mysql_query("SELECT sum(sor.qty) as total_order FROM `stock_orders` as sor WHERE sor.inventory_physical_id = ".$invAdjId." AND sor.product_id = ".$productId." AND sor.location_group_id = ".$InventoryPhysical['InventoryPhysical']['location_group_id']." AND location_id = ".$this->Helper->preventInput($location)." AND lots_number = '{$this->Helper->preventInput($lotsNumber)}' AND expired_date = '{$this->Helper->preventInput($expired)}' AND date <= '".$adjDate."' GROUP BY sor.product_id");
                while($rOrder=mysql_fetch_array($sqlOrder)){
                    $totalOrder = $rOrder['total_order'];
                }
            }
            if(mysql_num_rows($sqlTotal)){
                $rowTotal = mysql_fetch_array($sqlTotal);
                $totalQty = ($rowTotal[0] + $totalOrder)>0?($rowTotal[0] + $totalOrder):0;
            }else{
                $totalQty = 0;
            }
            echo $totalQty;
        }
        exit;
    }
}

?>