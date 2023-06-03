<?php

class OrdersController extends AppController {

    var $name = 'Orders';
    var $components = array('Helper', 'ProductCom');

    function index() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $this->set('user', $user);
        $this->Helper->saveUserActivity($user['User']['id'], 'Sales Order', 'Dashboard');
    }

    function ajax($customer = 'all', $status = 'all', $approve = 'all', $date = '') {
        $this->layout = 'ajax';
        $this->set(compact('customer', 'status', 'approve', 'date'));
    }

    function add() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $result = array();
            // Load Table
            $this->loadModel('OrderTermCondition');
            $this->loadModel('OrderDetail');
            $this->loadModel('OrderService');
            $this->loadModel('OrderMisc');
            // Order
            $this->Order->create();
            $order = array();
            $order['Order']['company_id'] = $this->data['Order']['company_id'];
            $order['Order']['branch_id']  = $this->data['Order']['branch_id'];
            $order['Order']['customer_id'] = $this->data['Order']['customer_id'];
            $order['Order']['customer_contact_id'] = $this->data['Order']['customer_contact_id'];
            $order['Order']['currency_center_id'] = $this->data['Order']['currency_center_id'];
            $order['Order']['quotation_id'] = $this->data['Order']['quotation_id'];
            $order['Order']['quotation_number'] = $this->data['Order']['quotation_number'];
            $order['Order']['order_code'] = $this->data['Order']['order_code'];
            $order['Order']['order_date'] = $this->data['Order']['order_date'];
            $order['Order']['price_type_id']  = $this->data['Order']['price_type_id'];
            $order['Order']['total_amount'] = $this->data['Order']['total_amount'];
            $order['Order']['discount']     = $this->data['Order']['discount'];
            $order['Order']['discount_percent'] = $this->data['Order']['discount_percent'];
            $order['Order']['total_vat'] = $this->data['Order']['total_vat'];
            $order['Order']['vat_percent'] = $this->data['Order']['vat_percent'];
            $order['Order']['vat_setting_id'] = $this->data['Order']['vat_setting_id'];
            $order['Order']['vat_calculate']  = $this->data['Order']['vat_calculate'];
            $order['Order']['note'] = $this->data['Order']['note'];
            $order['Order']['created_by']  = $user['User']['id'];
            $order['Order']['status'] = 1;
            if ($this->Order->save($order)) {
                $result['id'] = $orderId = $this->Order->id;
                // Get Module Code
                $modCode = $this->Helper->getModuleCode($this->data['Order']['order_code'], $orderId, 'order_code', 'orders', 'status >= 0 AND branch_id = '.$this->data['Order']['branch_id']);
                // Updaet Module Code
                mysql_query("UPDATE orders SET order_code = '".$modCode."' WHERE id = ".$orderId);
                // Insert Term & Condition
                if(!empty($_POST['term_condition_type_id'])){
                    for ($i = 0; $i < sizeof($_POST['term_condition_type_id']); $i++) {
                        if(!empty($_POST['term_condition_id'][$i])){
                            $termCondition = array();
                            // Term Condition
                            $this->OrderTermCondition->create();
                            $termCondition['OrderTermCondition']['order_id'] = $orderId;
                            $termCondition['OrderTermCondition']['term_condition_type_id'] = $_POST['term_condition_type_id'][$i];
                            $termCondition['OrderTermCondition']['term_condition_id'] = $_POST['term_condition_id'][$i];
                            $this->OrderTermCondition->save($termCondition);
                        }
                    }
                }
                for ($i = 0; $i < sizeof($_POST['product']); $i++) {
                    if (!empty($_POST['product_id'][$i])) {
                        $orderDetail = array();
                        // Order Detail
                        $this->OrderDetail->create();
                        $orderDetail['OrderDetail']['order_id'] = $orderId;
                        $orderDetail['OrderDetail']['product_id']   = $_POST['product_id'][$i];
                        $orderDetail['OrderDetail']['qty']          = $_POST['qty'][$i];
                        $orderDetail['OrderDetail']['qty_free']     = $_POST['qty_free'][$i];
                        $orderDetail['OrderDetail']['qty_uom_id']   = $_POST['qty_uom_id'][$i];
                        $orderDetail['OrderDetail']['conversion']   = $_POST['conversion'][$i];
                        $orderDetail['OrderDetail']['discount_id']  = $_POST['discount_id'][$i];
                        $orderDetail['OrderDetail']['discount_amount']  = $_POST['discount'][$i];
                        $orderDetail['OrderDetail']['discount_percent'] = $_POST['discount_percent'][$i];
                        $orderDetail['OrderDetail']['unit_cost']    = $_POST['unit_cost'][$i];
                        $orderDetail['OrderDetail']['unit_price']   = $_POST['unit_price'][$i];
                        $orderDetail['OrderDetail']['total_price']  = $_POST['total_price_bf_dis'][$i];
                        $this->OrderDetail->save($orderDetail);
                    } else if(!empty($_POST['service_id'][$i])){
                        $orderService = array();
                        // Quotation Detail
                        $this->OrderService->create();
                        $orderService['OrderService']['order_id'] = $orderId;
                        $orderService['OrderService']['service_id']   = $_POST['service_id'][$i];
                        $orderService['OrderService']['qty']          = $_POST['qty'][$i];
                        $orderService['OrderService']['qty_free']     = $_POST['qty_free'][$i];
                        $orderService['OrderService']['conversion']   = $_POST['conversion'][$i];
                        $orderService['OrderService']['discount_id']  = $_POST['discount_id'][$i];
                        $orderService['OrderService']['discount_amount']  = $_POST['discount'][$i];
                        $orderService['OrderService']['discount_percent'] = $_POST['discount_percent'][$i];
                        $orderService['OrderService']['unit_price']   = $_POST['unit_price'][$i];
                        $orderService['OrderService']['total_price']  = $_POST['total_price_bf_dis'][$i];
                        $this->OrderService->save($orderService);
                    } else{
                        $orderMisc = array();
                        // Quotation Detail
                        $this->OrderMisc->create();
                        $orderMisc['OrderMisc']['order_id'] = $orderId;
                        $orderMisc['OrderMisc']['description']  = $_POST['product'][$i];
                        $orderMisc['OrderMisc']['qty']          = $_POST['qty'][$i];
                        $orderMisc['OrderMisc']['qty_free']     = $_POST['qty_free'][$i];
                        $orderMisc['OrderMisc']['qty_uom_id']   = $_POST['qty_uom_id'][$i];
                        $orderMisc['OrderMisc']['conversion']   = $_POST['conversion'][$i];
                        $orderMisc['OrderMisc']['discount_id']  = $_POST['discount_id'][$i];
                        $orderMisc['OrderMisc']['discount_amount']  = $_POST['discount'][$i];
                        $orderMisc['OrderMisc']['discount_percent'] = $_POST['discount_percent'][$i];
                        $orderMisc['OrderMisc']['unit_price']   = $_POST['unit_price'][$i];
                        $orderMisc['OrderMisc']['total_price']  = $_POST['total_price_bf_dis'][$i];
                        $this->OrderMisc->save($orderMisc);
                    }
                }
                $this->Helper->saveUserActivity($user['User']['id'], 'Sales Order', 'Save Add New', $orderId);
                echo json_encode($result);
                exit;
            } else {
                $this->Helper->saveUserActivity($user['User']['id'], 'Sales Order', 'Save Add New (Error)');
                $result['code'] = 2;
                echo json_encode($result);
                exit;
            }
        }
        $this->Helper->saveUserActivity($user['User']['id'], 'Sales Order', 'Add New');
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
                            'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id', 'ModuleCodeBranch.so_code', 'Branch.currency_center_id', 'CurrencyCenter.symbol'),
                            'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])
                        ));
        $this->set(compact("companies", "branches"));
    }

    function orderDetails() {
        $this->layout = 'ajax';
        $uoms = ClassRegistry::init('Uom')->find('all', array('fields' => array('Uom.id', 'Uom.name'), 'conditions' => array('Uom.is_active' => 1)));
        $this->set(compact('uoms'));
    }
    
    function customer($companyId = null) {
        $this->layout = 'ajax';
        $this->set(compact('companyId'));
    }

    function customerAjax($companyId = null, $group = null) {
        $this->layout = 'ajax';
        $this->set(compact('companyId', 'group'));
    }
    
    function product($companyId = null, $branchId = null) {
        $this->layout = 'ajax';
        $orderDate    = $_POST['order_date'];
        $this->set(compact('companyId', 'branchId', 'orderDate'));
    }

    function productAjax($companyId = null, $branchId = null, $category = null) {
        $this->layout = 'ajax';
        $orderDate    = $_GET['order_date'];
        $this->set(compact('companyId', 'branchId', 'category', 'orderDate'));
    }
    
    function searchProduct() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $joinProductBranch  = array(
                             'table' => 'product_branches',
                             'type' => 'INNER',
                             'alias' => 'ProductBranch',
                             'conditions' => array(
                                 'ProductBranch.product_id = Product.id',
                                 'ProductBranch.branch_id IN (SELECT branch_id FROM user_branches WHERE user_id = '.$user['User']['id'].')'
                             ));
        $joinProductgroup  = array(
                             'table' => 'product_pgroups',
                             'type' => 'INNER',
                             'alias' => 'ProductPgroup',
                             'conditions' => array('ProductPgroup.product_id = Product.id')
                             );
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
                                'Product.chemical LIKE' => '%' . trim($this->params['url']['q']) . '%',
                            ), 'Product.company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')'
                            , 'Product.is_active' => 1
                            , '((Product.price_uom_id IS NOT NULL AND Product.is_packet = 0) OR (Product.price_uom_id IS NULL AND Product.is_packet = 1))'
                        ),
                        'joins' => $joins,
                        'group' => array(
                            'Product.id'
                        )
                ));
        $this->set(compact('products'));
    }

    function searchProductByCode($companyId = null, $customerId = 0, $branchId = null) {
        $this->layout = 'ajax';
        $order_date   = !empty($_POST['order_date']) ? $_POST['order_date'] : "0000-00-00";
        $product_code = !empty($this->data['code']) ? $this->data['code'] : "";
        $user = $this->getCurrentUser();
        $joinProductBranch  = array(
                             'table' => 'product_branches',
                             'type' => 'INNER',
                             'alias' => 'ProductBranch',
                             'conditions' => array(
                                 'ProductBranch.product_id = Product.id',
                                 'ProductBranch.branch_id' => $branchId
                             ));
        $joinProductgroup  = array(
                             'table' => 'product_pgroups',
                             'type' => 'INNER',
                             'alias' => 'ProductPgroup',
                             'conditions' => array('ProductPgroup.product_id = Product.id')
                             );
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
        $product = ClassRegistry::init('Product')->find('first', array(
            'fields' => array(
                'Product.id',
                'Product.name',
                'Product.code',
                'Product.barcode',
                'Product.price_uom_id',
                'Product.is_packet',
                'Product.small_val_uom'
            ),
            'conditions' => array(
                array(
                    "OR" => array(
                        'trim(Product.code)' => trim($product_code),
                        'trim(Product.barcode)' => trim($product_code),
                        'trim(Product.chemical)' => trim($product_code)
                    )
                ), 'Product.is_active' => 1
                , 'Product.company_id' => $companyId
                , '((Product.price_uom_id IS NOT NULL AND Product.is_packet = 0) OR (Product.price_uom_id IS NULL AND Product.is_packet = 1))'
                , "((is_not_for_sale = 0 AND period_from IS NULL AND period_to IS NULL) OR (is_not_for_sale = 0 AND period_from <= '".$order_date."' AND period_to >= '".$order_date."') OR (is_not_for_sale = 1 AND period_from IS NOT NULL AND period_to IS NOT NULL AND '".$order_date."' NOT BETWEEN period_from AND period_to))"
            ),
            'joins' => $joins,
            'group' => array(
                'Product.id'
            )
        ));
        $this->set(compact('product', 'customerId'));

        $db = ConnectionManager::getDataSource('default');
        mysql_select_db($db->config['database']);
    }
    
    function edit($id = null, $editType = null) {
        $this->layout = 'ajax';
        if (!$id && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }

        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $Order = $this->Order->read(null, $this->data['id']);
            if ($Order['Order']['status'] == 1) {
                $result = array();
                $statuEdit = "-1";
                if($this->data['Order']['company_id'] != $Order['Order']['company_id']){
                    $statuEdit = 0;
                }
                // Update Status As Edit
                $this->Order->updateAll(
                        array('Order.status' => $statuEdit, 'Order.modified_by' => $user['User']['id']), array('Order.id' => $this->data['id'])
                );
                // Load Table
                $this->loadModel('OrderTermCondition');
                $this->loadModel('OrderDetail');
                $this->loadModel('OrderService');
                $this->loadModel('OrderMisc');
                // Order
                $orderCode = $Order['Order']['order_code'];
                $this->Order->create();
                $order = array();
                $order['Order']['order_code'] = $orderCode;
                $order['Order']['company_id'] = $this->data['Order']['company_id'];
                $order['Order']['branch_id']  = $this->data['Order']['branch_id'];
                $order['Order']['quotation_id'] = $this->data['Order']['quotation_id'];
                $order['Order']['quotation_number'] = $this->data['Order']['quotation_number'];
                $order['Order']['customer_id'] = $this->data['Order']['customer_id'];
                $order['Order']['customer_contact_id'] = isset($this->data['Order']['customer_contact_id']) ? $this->data['Order']['customer_contact_id'] : '';
                $order['Order']['currency_center_id'] = $this->data['Order']['currency_center_id'];
                $order['Order']['order_date'] = $this->data['Order']['order_date'];
                $order['Order']['price_type_id']  = $this->data['Order']['price_type_id'];
                $order['Order']['total_amount'] = $this->data['Order']['total_amount'];
                $order['Order']['discount']     = $this->data['Order']['discount'];
                $order['Order']['discount_percent'] = $this->data['Order']['discount_percent'];
                $order['Order']['total_vat']    = $this->data['Order']['total_vat'];
                $order['Order']['vat_percent']  = $this->data['Order']['vat_percent'];
                $order['Order']['vat_setting_id'] = $this->data['Order']['vat_setting_id'];
                $order['Order']['vat_calculate']  = $this->data['Order']['vat_calculate'];
                $order['Order']['note'] = $this->data['Order']['note'];
                $order['Order']['edited']      = date("Y-m-d H:i:s");
                $order['Order']['edited_by']   = $user['User']['id'];
                $order['Order']['created']     = $Order['Order']['created'];
                $order['Order']['created_by']  = $Order['Order']['created_by'];
                $order['Order']['patient_id'] = $Order['Order']['patient_id']; 
                $order['Order']['queue_id'] = $Order['Order']['queue_id']; 
                $order['Order']['queue_doctor_id'] = $Order['Order']['queue_doctor_id']; 
                $order['Order']['prescription_type']  = $this->data['Order']['prescription_type'];
                if ($this->Order->save($order)) {
                    $result['id'] = $orderId = $this->Order->id;
                    if($this->data['Order']['branch_id'] != $Order['Order']['branch_id']){
                        // Get Module Code
                        $modCode = $this->Helper->getModuleCode($this->data['Order']['order_code'], $orderId, 'order_code', 'orders', 'status >= 0 AND branch_id = '.$this->data['Order']['branch_id']);
                        // Updaet Module Code
                        mysql_query("UPDATE orders SET order_code = '".$modCode."' WHERE id = ".$orderId);
                    }
                    
                    // Update Appointment 
                    if($this->data['Appointment']['id'] != ''){
                        mysql_query("UPDATE appointments SET order_id = '".$orderId."' WHERE id = ".$this->data['Appointment']['id']);
                    }
                    // Insert Term & Condition
                    if(!empty($_POST['term_condition_type_id'])){
                        for ($i = 0; $i < sizeof($_POST['term_condition_type_id']); $i++) {
                            if(!empty($_POST['term_condition_id'][$i])){
                                $termCondition = array();
                                // Term Condition
                                $this->OrderTermCondition->create();
                                $termCondition['OrderTermCondition']['order_id'] = $orderId;
                                $termCondition['OrderTermCondition']['term_condition_type_id'] = $_POST['term_condition_type_id'][$i];
                                $termCondition['OrderTermCondition']['term_condition_id'] = $_POST['term_condition_id'][$i];
                                $this->OrderTermCondition->save($termCondition);
                            }
                        }
                    }
                    for ($i = 0; $i < sizeof($_POST['product']); $i++) {
                        if (!empty($_POST['product_id'][$i])) {
                            $orderDetail = array();
                            // Order Detail
                            $this->OrderDetail->create();
                            $orderDetail['OrderDetail']['order_id'] = $orderId;
                            $orderDetail['OrderDetail']['product_id']   = $_POST['product_id'][$i];
                            $orderDetail['OrderDetail']['qty']          = $_POST['qty'][$i];
                            $orderDetail['OrderDetail']['qty_free']     = isset($_POST['qty_free'][$i]) ? $_POST['qty_free'][$i] : 0;
                            $orderDetail['OrderDetail']['qty_uom_id']   = $_POST['qty_uom_id'][$i];
                            $orderDetail['OrderDetail']['conversion']   = $_POST['conversion'][$i];
                            $orderDetail['OrderDetail']['discount_id']  = isset($_POST['discount_id'][$i]) ? $_POST['discount_id'][$i] : 0 ;
                            $orderDetail['OrderDetail']['discount_amount']  = isset($_POST['discount'][$i]) ? $_POST['discount'][$i] : 0;
                            $orderDetail['OrderDetail']['discount_percent'] = isset($_POST['discount_percent'][$i]) ? $_POST['discount_percent'][$i] : 0 ;
                            $orderDetail['OrderDetail']['unit_cost']    = isset($_POST['unit_cost'][$i]) ? $_POST['unit_cost'][$i] : 0 ;
                            $orderDetail['OrderDetail']['unit_price']   = isset($_POST['unit_price'][$i]) ? $_POST['unit_price'][$i] : 0;
                            $orderDetail['OrderDetail']['total_price']  = isset($_POST['total_price_bf_dis'][$i]) ? $_POST['total_price_bf_dis'][$i] : 0;
                            $orderDetail['OrderDetail']['num_days']    = $_POST['num_days'][$i];
                            $orderDetail['OrderDetail']['morning']     = $_POST['morning'][$i];
                            $orderDetail['OrderDetail']['afternoon']   = $_POST['afternoon'][$i];
                            $orderDetail['OrderDetail']['evening']     = $_POST['evening'][$i];
                            $orderDetail['OrderDetail']['night']       = $_POST['night'][$i];
                            $orderDetail['OrderDetail']['note']        = isset($_POST['note'][$i]) ? $_POST['note'][$i] : '' ;
                            $orderDetail['OrderDetail']['morning_use_id']   = ((empty($_POST['morning_use_id'][$i]))?0:$_POST['morning_use_id'][$i]);
                            $orderDetail['OrderDetail']['afternoon_use_id'] = ((empty($_POST['afternoon_use_id'][$i]))?0:$_POST['afternoon_use_id'][$i]);
                            $orderDetail['OrderDetail']['evening_use_id']   = ((empty($_POST['evening_use_id'][$i]))?0:$_POST['evening_use_id'][$i]); 
                            $orderDetail['OrderDetail']['night_use_id']     = ((empty($_POST['night_use_id'][$i]))?0:$_POST['night_use_id'][$i]);
                            $this->OrderDetail->save($orderDetail);

                        } else if(!empty($_POST['service_id'][$i])){
                            $orderService = array();
                            // Quotation Detail
                            $this->OrderService->create();
                            $orderService['OrderService']['order_id'] = $orderId;
                            $orderService['OrderService']['service_id']   = $_POST['service_id'][$i];
                            $orderService['OrderService']['qty']          = $_POST['qty'][$i];
                            $orderService['OrderService']['qty_free']     = $_POST['qty_free'][$i];
                            $orderService['OrderService']['conversion']   = $_POST['conversion'][$i];
                            $orderService['OrderService']['discount_id']  = $_POST['discount_id'][$i];
                            $orderService['OrderService']['discount_amount']  = $_POST['discount'][$i];
                            $orderService['OrderService']['discount_percent'] = $_POST['discount_percent'][$i];
                            $orderService['OrderService']['unit_price']   = $_POST['unit_price'][$i];
                            $orderService['OrderService']['total_price']  = $_POST['total_price_bf_dis'][$i];
                            $this->OrderService->save($orderService);
                        } else{
                            $orderMisc = array();
                            // Quotation Detail
                            $this->OrderMisc->create();
                            $orderMisc['OrderMisc']['order_id'] = $orderId;
                            $orderMisc['OrderMisc']['description']  = $_POST['product'][$i];
                            $orderMisc['OrderMisc']['qty']          = $_POST['qty'][$i];
                            $orderMisc['OrderMisc']['qty_free']     = isset($_POST['qty_free'][$i]) ? $_POST['qty_free'][$i] : 0;
                            $orderMisc['OrderMisc']['qty_uom_id']   = $_POST['qty_uom_id'][$i];
                            $orderMisc['OrderMisc']['conversion']   = $_POST['conversion'][$i];
                            $orderMisc['OrderMisc']['discount_id']  = isset($_POST['discount_id'][$i]) ? $_POST['discount_id'][$i] : 0 ;
                            $orderMisc['OrderMisc']['discount_amount']  = isset($_POST['discount'][$i]) ? $_POST['discount'][$i] : 0;
                            $orderMisc['OrderMisc']['discount_percent'] = isset($_POST['discount_percent'][$i]) ? $_POST['discount_percent'][$i] : 0;
                            $orderMisc['OrderMisc']['unit_price']   = isset($_POST['unit_price'][$i]) ? $_POST['unit_price'][$i] : 0;
                            $orderMisc['OrderMisc']['total_price']  = isset($_POST['total_price_bf_dis'][$i]) ? $_POST['total_price_bf_dis'][$i] : 0;
                            $orderMisc['OrderMisc']['num_days']    = $_POST['num_days'][$i];
                            $orderMisc['OrderMisc']['morning']     = $_POST['morning'][$i];
                            $orderMisc['OrderMisc']['afternoon']   = $_POST['afternoon'][$i];
                            $orderMisc['OrderMisc']['evening']     = $_POST['evening'][$i];
                            $orderMisc['OrderMisc']['night']       = $_POST['night'][$i];
                            $orderMisc['OrderMisc']['note']        = isset($_POST['note'][$i]) ? $_POST['note'][$i] : '';
                            $orderMisc['OrderMisc']['morning_use_id']   = ((empty($_POST['morning_use_id'][$i]))?0:$_POST['morning_use_id'][$i]);
                            $orderMisc['OrderMisc']['afternoon_use_id'] = ((empty($_POST['afternoon_use_id'][$i]))?0:$_POST['afternoon_use_id'][$i]);
                            $orderMisc['OrderMisc']['evening_use_id']   = ((empty($_POST['evening_use_id'][$i]))?0:$_POST['evening_use_id'][$i]); 
                            $orderMisc['OrderMisc']['night_use_id']     = ((empty($_POST['night_use_id'][$i]))?0:$_POST['night_use_id'][$i]);
                            $this->OrderMisc->save($orderMisc);
                        }
                    }
                    $this->Helper->saveUserActivity($user['User']['id'], 'Sales Order', 'Save Edit', $this->data['id'], $orderId);
                    echo json_encode($result);
                    exit;
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'Sales Order', 'Save Edit (Error)', $this->data['id']);
                    $result['code'] = 2;
                    echo json_encode($result);
                    exit;
                }
            }else{
                $this->Helper->saveUserActivity($user['User']['id'], 'Sales Order', 'Save Edit (Error Status)', $this->data['id']);
                $result['code'] = 2;
                echo json_encode($result);
                exit;
            }
        }
        $this->Helper->saveUserActivity($user['User']['id'], 'Sales Order', 'Edit', $id);
        $this->data = ClassRegistry::init('Order')->find('first', array(
            'conditions' => array('Order.status = 1', 'Order.id' => $id)
                )
        );
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
                            'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id', 'ModuleCodeBranch.so_code', 'Branch.currency_center_id', 'CurrencyCenter.symbol'),
                            'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])
                        ));
        $appointments =  ClassRegistry::init('Appointment')->find('first', array(
                        'conditions' => array('Appointment.order_id' => $id))) ;
        $locationBacks = $editType;
        $this->set(compact("companies", "branches" , "appointments", "locationBacks"));
        if ($this->data['Order']['status'] != 1) {
            echo "Sorry Cannot Edit";
            exit;
        }
    }
    
    function editDetails($id = null) {
        $this->layout = 'ajax';
        if($id >= 0){
            $this->loadModel('TreatmentUse');
            $treatmentUses = ClassRegistry::init('TreatmentUse')->find("all", array('conditions' => array('TreatmentUse.is_active' => 1)));
            $order = ClassRegistry::init('Order')->find("first", array('conditions' => array('Order.id' => $id)));
            $orderDetails = ClassRegistry::init('OrderDetail')->find('all', array('conditions' => array('OrderDetail.order_id' => $id)));
            $orderServices = ClassRegistry::init('OrderService')->find("all", array('conditions' => array('OrderService.order_id' => $id)));
            $orderMiscs    = ClassRegistry::init('OrderMisc')->find("all", array('conditions' => array('OrderMisc.order_id' => $id)));
            $uoms = ClassRegistry::init('Uom')->find('all', array('fields' => array('Uom.id', 'Uom.name'), 'conditions' => array('Uom.is_active' => 1)));
            $this->set(compact("orderDetails", "orderServices", "orderMiscs", "uoms", "order", "treatmentUses"));
        } else {
            exit;
        }
    }
    
    function delete($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        $order = ClassRegistry::init('Order')->find("first", array('conditions' => array('Order.id' => $id)));

        if ($order['Order']['status'] > 0) {
            // Update Credit Memo
            $this->Order->updateAll(
                    array('Order.status' => 0, 'Order.modified_by' => $user['User']['id']), array('Order.id' => $id)
            );
            $this->Helper->saveUserActivity($user['User']['id'], 'Sales Order', 'Delete', $id);
            echo MESSAGE_DATA_HAS_BEEN_DELETED;
            exit;
        }else{
            $this->Helper->saveUserActivity($user['User']['id'], 'Sales Order', 'Delete (Error)', $id);
            echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
            exit;
        }
    }
    
    function view($id = null) {
        $this->layout = 'ajax';
        if (!empty($id)) {
            $user = $this->getCurrentUser();
            $this->data = $this->Order->read(null, $id);
            if (!empty($this->data)) {
                $this->Helper->saveUserActivity($user['User']['id'], 'Sales Order', 'View', $id);
                $orderDetails = ClassRegistry::init('OrderDetail')->find("all", array('conditions' => array('OrderDetail.order_id' => $id)));
                $orderServices = ClassRegistry::init('OrderService')->find("all", array('conditions' => array('OrderService.order_id' => $id)));
                $orderMiscs    = ClassRegistry::init('OrderMisc')->find("all", array('conditions' => array('OrderMisc.order_id' => $id)));
                $this->set(compact('orderDetails', 'orderServices', 'orderMiscs'));
            } else {
                exit;
            }
        } else {
            exit;
        }
    }
    
    function printInvoice($id = null, $head = 0) {
        $this->layout = 'ajax';
        if (!empty($id)) {
            $this->data = $this->Order->read(null, $id);
            if (!empty($this->data)) {
                $orderDetails  = ClassRegistry::init('OrderDetail')->find("all", array('conditions' => array('OrderDetail.order_id' => $id)));
                $orderServices = ClassRegistry::init('OrderService')->find("all", array('conditions' => array('OrderService.order_id' => $id)));
                $orderMiscs    = ClassRegistry::init('OrderMisc')->find("all", array('conditions' => array('OrderMisc.order_id' => $id)));
                  $appointment    = ClassRegistry::init('Appointment')->find("all", array('conditions' => array('Appointment.order_id' => $id)));
                $this->set(compact('orderDetails', 'orderServices', 'orderMiscs', 'head' , 'appointment'));
            } else {
                exit;
            }
        } else {
            exit;
        }
    }
    
    function close($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        $modified = date("Y-m-d H:i:s");                    
        $this->Order->updateAll(
                array('Order.is_close' => "1", "Order.modified_by" => $user['User']['id'], 'Order.modified' => "'$modified'"),
                array('Order.id' => $id)
        );
        $this->Helper->saveUserActivity($user['User']['id'], 'Sales Order', 'Close', $id);
        echo MESSAGE_DATA_HAS_BEEN_CLOSED;
        exit;
    }
    
    function open($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        $modified = date("Y-m-d H:i:s"); 
        $this->Order->updateAll(
                array('Order.is_close' => "0", "Order.modified_by" => $user['User']['id'], 'Order.modified' => "'$modified'"),
                array('Order.id' => $id)
        );
        $this->Helper->saveUserActivity($user['User']['id'], 'Sales Order', 'Open', $id);
        echo MESSAGE_DATA_HAS_BEEN_SAVED;
        exit;
    }
    
    function quotation($companyId = null, $branchId = null, $customerId = ''){
        $this->layout = 'ajax';
        $this->set('saleId', $_POST['sale_id']);
        $this->set(compact('companyId', 'branchId', 'customerId'));
    }
    
    function quotationAjax($companyId, $branchId, $customerId = ''){
        $this->layout = 'ajax';
        $this->set('saleId', $_GET['sale_id']);
        $this->set(compact('companyId', 'branchId', 'customerId'));
    }
    
    function getProductFromQuote($id = null){
        $this->layout = 'ajax';
        $result = array();
        if (empty($id)) {
            $result['error'] = 1;
            echo json_encode($result);
            exit;
        }
        $quotation = ClassRegistry::init('Quotation')->read(null, $id);
        $user  = $this->getCurrentUser();
        $allowProductDiscount = $this->Helper->checkAccess($user['User']['id'], $this->params['controller'], 'discount');
        $allowEditPrice = $this->Helper->checkAccess($user['User']['id'], $this->params['controller'], 'editPrice');
        $priceStatus = '';
        if(!$allowEditPrice){
            $priceStatus = 'readonly="readonly"';
        }
        $rowList = array();
        $rowLbl  = "";
        // Get Product
        $sqlQuoteDetail  = mysql_query("SELECT products.code AS code, products.barcode AS barcode, products.name AS name, products.price_uom_id AS price_uom_id, products.small_val_uom AS small_val_uom, quotation_details.product_id AS product_id, quotation_details.unit_price AS unit_price, quotation_details.total_price AS total_price, quotation_details.qty AS qty, quotation_details.qty_uom_id AS qty_uom_id, quotation_details.conversion AS conversion, quotation_details.discount_id AS discount_id, quotation_details.discount_amount AS discount_amount, quotation_details.discount_percent AS discount_percent, quotations.customer_id AS customer_id FROM quotation_details INNER JOIN quotations ON quotations.id = quotation_details.quotation_id INNER JOIN products ON products.id = quotation_details.product_id WHERE quotation_details.quotation_id = ".$id.";");
        while($rowDetail = mysql_fetch_array($sqlQuoteDetail)){
            $index   = rand();
            $productName = str_replace('"', '&quot;', $rowDetail['name']);
            $sqlProCus = mysql_query("SELECT name FROM product_with_customers WHERE product_id = ".$rowDetail['product_id']." AND customer_id = ".$rowDetail['customer_id']." ORDER BY created DESC LIMIT 1");
            if(mysql_num_rows($sqlProCus)){
                $rowProCus   = mysql_fetch_array($sqlProCus);
                $productName = str_replace('"', '&quot;', $rowProCus['name']);
            }
            // Qty
            $qty = $rowDetail['qty'];
            // Get UOM
            $query=mysql_query("SELECT id,name,abbr,1 AS conversion FROM uoms WHERE id=".$rowDetail['price_uom_id']."
                                UNION
                                SELECT id,name,abbr,(SELECT value FROM uom_conversions WHERE is_active=1 AND from_uom_id=".$rowDetail['price_uom_id']." AND to_uom_id=uoms.id) AS conversion FROM uoms WHERE id IN (SELECT to_uom_id FROM uom_conversions WHERE is_active=1 AND from_uom_id=".$rowDetail['price_uom_id'].")
                                ORDER BY conversion ASC");
            $i = 1;
            $length = mysql_num_rows($query);
            $optionUom = "";
            $conversionUsed = 1;
            $costSelected   = 0;
            while($data=mysql_fetch_array($query)){
                $priceLbl   = "";
                $costLbl    = "";
                $selected   = "";
                $isMain     = "other";
                $isSmall    = 0;
                $conversion = ($rowDetail['small_val_uom'] / $data['conversion']);
                // Check With Qty UOM Id
                if($data['id'] == $rowDetail['qty_uom_id']){   
                    $selected = ' selected="selected" ';
                    $conversionUsed = $conversion;
                }
                // Check With Product UOM Id
                if($data['id'] == $rowDetail['price_uom_id']){
                    $isMain = "first";
                }
                // Check Is Small UOM
                if($length == $i){
                    $isSmall = 1;
                }
                // Get Price
                $sqlPrice = mysql_query("SELECT products.unit_cost, product_prices.price_type_id, product_prices.amount, product_prices.percent, product_prices.add_on, product_prices.set_type FROM product_prices INNER JOIN products ON products.id = product_prices.product_id WHERE product_prices.product_id =".$rowDetail['product_id']." AND product_prices.uom_id =".$data['id']);
                if(@mysql_num_rows($sqlPrice)){
                    $price = 0;
                    while($rowPrice = mysql_fetch_array($sqlPrice)){
                        $unitCost = $rowPrice['unit_cost'] /  $data['conversion'];
                        if($rowPrice['set_type'] == 1){
                            $price = $rowPrice['amount'];
                        }else if($rowPrice['set_type'] == 2){
                            $percent = ($unitCost * $rowPrice['percent']) / 100;
                            $price = $unitCost + $percent;
                        }else if($rowPrice['set_type'] == 3){
                            $price = $unitCost + $rowPrice['add_on'];
                        }
                        $priceLbl .= 'price-uom-'.$rowPrice['price_type_id'].'="'.$price.'" ';
                        $costLbl  .= 'cost-uom-'.$rowPrice['price_type_id'].'="'.$unitCost.'" ';
                        if($data['id'] == $rowDetail['qty_uom_id'] && $rowPrice['price_type_id'] == $quotation['Quotation']['price_type_id']){
                            $costSelected = $unitCost;
                        }
                    }
                }else{
                    $priceLbl .= 'price-uom-1="0" price-uom-2="0"';
                    $costLbl  .= 'cost-uom-1="0" cost-uom-2="0"';
                }
                $optionUom .= '<option '.$costLbl.' '.$priceLbl.' '.$selected.' data-sm="'.$isSmall.'" data-item="'.$isMain.'" value="'.$data['id'].'" conversion="'.$data['conversion'].'">'.$data['name'].'</option>';
                $i++;
            }
            // Open Tr
            $rowLbl .= '<tr class="tblOrderList">';
            // Index
            $rowLbl .= '<td class="first" style="width:5%; text-align: center;padding: 0px; height: 30px;">'.++$index.'</td>';
            // SKU
            $rowLbl .= '<td style="width:11%; text-align: left; padding: 5px;"><span class="lblSKU">'.$rowDetail['code'].'</span></td>';
            // Product
            $rowLbl .= '<td style="width:20%; text-align: left; padding: 5px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%; padding: 0px; margin: 0px;">';
            $rowLbl .= '<input type="hidden" id="product_id_'.$index.'" class="product_id" name="product_id[]" value="'.$rowDetail['product_id'].'" />';
            $rowLbl .= '<input type="hidden" id="service_id_'.$index.'" name="service_id[]" value="" />';
            $rowLbl .= '<input type="hidden" class="orgProName" value="PUC: '.$rowDetail['barcode'].'<br/><br/>SKU: '.$rowDetail['code'].'<br/><br/>Name: '.str_replace('"', '&quot;', $rowDetail['name']).'" />';
            $rowLbl .= '<input type="text" id="product_'.$index.'" value="'.$productName.'" name="product[]" class="product validate[required]" style="width: 85%;" />';
            $rowLbl .= '<img alt="Information" src="'.$this->webroot.'img/button/view.png" class="btnProductSaleInfo" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Information\')" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Qty Input
            $rowLbl .= '<td style="width:8%; text-align: center;padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="text" id="qty_'.$index.'" name="qty[]" value="'.$qty.'" style="width:60%;" class="qty interger" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Qty Free Input
            $rowLbl .= '<td style="width:8%; text-align: center;padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="text" id="qty_free_'.$index.'" name="qty_free[]" value="0" style="width:60%;" class="qty_free interger" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // UOM
            $rowLbl .= '<td style="width:13%; padding: 0px; text-align: center">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="hidden" name="conversion[]" class="conversion" value="'.$rowDetail['conversion'].'" />';
            $rowLbl .= '<select id="qty_uom_id_'.$index.'" style="width:80%; height: 20px;" name="qty_uom_id[]" class="qty_uom_id validate[required]">'.$optionUom.'</select>';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Unit Price
            $priceColor = 'color: red;';
            $msgStyle   = '';
            $unitPrice  = $rowDetail['unit_price'];
            if($unitPrice >= $costSelected){
                $priceColor = '';
                $msgStyle   = 'style="display: none;"';
            }
            $rowLbl .= '<td style="width:9%; padding: 0px; text-align: center;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="hidden" class="float unit_cost" name="unit_cost[]" value="'.number_format($costSelected, 2).'" />';
            $rowLbl .= '<input type="text" id="unit_price_'.$index.'" '.$priceStatus.' name="unit_price[]" value="'.number_format($unitPrice, 2).'" style="width:70%; '.$priceColor.'" class="float unit_price" />';
            $rowLbl .= '<img alt="'.MESSAGE_UNIT_PRICE_LESS_THAN_UNIT_COST.'" src="'.$this->webroot.'img/button/down.png" '.$msgStyle.' class="priceDownOrder" align="absmiddle" onmouseover="Tip(\''.MESSAGE_UNIT_PRICE_LESS_THAN_UNIT_COST.'\')" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Discount
            // Check Permission Discount
            if($allowProductDiscount){
                if($rowDetail['discount_id'] > 0){
                    $disPlay = '';
                }else{
                    $disPlay = 'display: none;';
                }
                $disDisplay  = '<input type="text" id="discount_'.$index.'" name="discount[]" class="discount float" value="'.number_format($rowDetail['discount_amount'], 2).'" style="width: 60%;" readonly="readonly" />';
                $disDisplay .= '<img alt="Remove" src="'.$this->webroot.'img/button/cross.png" class="btnRemoveDiscountOrder" align="absmiddle" style="cursor: pointer; '.$disPlay.'" onmouseover="Tip(\'Remove\')" />';
            }else{
                $disDisplay = '<input type="hidden" id="discount_'.$index.'" name="discount[]" class="discount float" value="'.$rowDetail['discount_amount'].'" style="width: 60%;" readonly="readonly" />';
                $disDisplay .= number_format($rowDetail['discount_amount'], 2);
            }
            $rowLbl .= '<td style="width:9%; padding: 0px; text-align: center">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="hidden" name="discount_id[]" value="'.$rowDetail['discount_id'].'" />';
            $rowLbl .= '<input type="hidden" name="discount_amount[]" value="'.$rowDetail['discount_amount'].'" />';
            $rowLbl .= '<input type="hidden" name="discount_percent[]" value="'.$rowDetail['discount_percent'].'" />';
            $rowLbl .= $disDisplay;
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Total Price
            $rowLbl .= '<td style="width:9%; text-align: center; padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="hidden" name="total_price_bf_dis[]" value="'.number_format($rowDetail['total_price'], 2).'" class="total_price_bf_dis float" />';
            $rowLbl .= '<input type="text" id="total_price_'.$index.'" '.$priceStatus.' name="total_price[]" value="'.number_format($rowDetail['total_price'] - $rowDetail['discount_amount'], 2).'" style="width:80%" class="total_price float" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Button Remove
            $rowLbl .= '<td style="width:8%">';
            $rowLbl .= '<img alt="Remove" src="'.$this->webroot.'img/button/cross.png" class="btnRemoveOrderList" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Remove\')" />';
            $rowLbl .= '&nbsp; <img alt="Up" src="'.$this->webroot .'img/button/move_up.png" class="btnMoveUpOrderList" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Up\')" />';
            $rowLbl .= '&nbsp; <img alt="Down" src="'.$this->webroot .'img/button/move_down.png" class="btnMoveDownOrderList" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Down\')" />';
            $rowLbl .= '</td>';
            // Close Tr
            $rowLbl .= '</tr>';
        }
        // Get Service
        $sqlQuoteService  = mysql_query("SELECT services.code AS code, services.name AS name, uoms.abbr AS uom, uoms.id AS uom_id, quotation_services.service_id AS service_id, quotation_services.unit_price AS unit_price, quotation_services.total_price AS total_price, quotation_services.qty AS qty, quotation_services.conversion AS conversion, quotation_services.discount_id AS discount_id, quotation_services.discount_amount AS discount_amount, quotation_services.discount_percent AS discount_percent FROM quotation_services INNER JOIN services ON services.id = quotation_services.service_id INNER JOIN uoms ON uoms.id = services.uom_id WHERE quotation_services.quotation_id = ".$id.";");
        while($rowService = mysql_fetch_array($sqlQuoteService)){
            $index   = rand();
            // Qty
            $qty = $rowService['qty'];
            // Get UOM
            $optionUom = '<option value="'.$rowService['uom_id'].'" conversion="1" selected="selected">'.$rowService['uom'].'</option>';
            // Open Tr
            $rowLbl .= '<tr class="tblOrderList">';
            // Index
            $rowLbl .= '<td class="first" style="width:5%; text-align: center;padding: 0px; height: 30px;">'.++$index.'</td>';
            // SKU
            $rowLbl .= '<td style="width:11%; text-align: left; padding: 5px;"><span class="lblSKU">'.$rowService['code'].'</span></td>';
            // Product
            $rowLbl .= '<td style="width:20%; text-align: left; padding: 5px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%; padding: 0px; margin: 0px;">';
            $rowLbl .= '<input type="hidden" id="product_id_'.$index.'" class="product_id" name="product_id[]" value="" />';
            $rowLbl .= '<input type="hidden" id="service_id_'.$index.'" name="service_id[]" value="'.$rowService['service_id'].'" />';
            $rowLbl .= '<input type="text" id="product_'.$index.'" readonly="readonly" value="'.$rowService['name'].'" name="product[]" class="product validate[required]" style="width: 85%;" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Qty Input
            $rowLbl .= '<td style="width:8%; text-align: center;padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="text" id="qty_'.$index.'" name="qty[]" value="'.$qty.'" style="width:60%;" class="qty interger" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Qty Free Input
            $rowLbl .= '<td style="width:8%; text-align: center;padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="text" id="qty_free_'.$index.'" name="qty_free[]" value="0" style="width:60%;" class="qty_free interger" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // UOM
            $rowLbl .= '<td style="width:13%; padding: 0px; text-align: center">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="hidden" name="conversion[]" class="conversion" value="'.$rowService['conversion'].'" />';
            $rowLbl .= '<select id="qty_uom_id_'.$index.'" style="width:80%; height: 20px;" name="qty_uom_id[]" class="qty_uom_id">'.$optionUom.'</select>';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Unit Price
            $unitPrice = $rowService['unit_price'];
            $rowLbl .= '<td style="width:9%; padding: 0px; text-align: center;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="hidden" class="float unit_cost" name="unit_cost[]" value="0" />';
            $rowLbl .= '<input type="text" id="unit_price_'.$index.'" '.$priceStatus.' name="unit_price[]" value="'.number_format($unitPrice, 2).'" style="width:70%;" class="float unit_price" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Discount
            // Check Permission Discount
            if($allowProductDiscount){
                if($rowService['discount_id'] > 0){
                    $disPlay = '';
                }else{
                    $disPlay = 'display: none;';
                }
                $disDisplay  = '<input type="text" id="discount_'.$index.'" name="discount[]" class="discount float" value="'.number_format($rowService['discount_amount'], 2).'" style="width: 60%;" readonly="readonly" />';
                $disDisplay .= '<img alt="Remove" src="'.$this->webroot.'img/button/cross.png" class="btnRemoveDiscountOrder" align="absmiddle" style="cursor: pointer; '.$disPlay.'" onmouseover="Tip(\'Remove\')" />';
            }else{
                $disDisplay = '<input type="hidden" id="discount_'.$index.'" name="discount[]" class="discount float" value="'.$rowService['discount_amount'].'" style="width: 60%;" readonly="readonly" />';
                $disDisplay .= number_format($rowService['discount_amount'], 2);
            }
            $rowLbl .= '<td style="width:9%; padding: 0px; text-align: center">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="hidden" name="discount_id[]" value="'.$rowService['discount_id'].'" />';
            $rowLbl .= '<input type="hidden" name="discount_amount[]" value="'.$rowService['discount_amount'].'" />';
            $rowLbl .= '<input type="hidden" name="discount_percent[]" value="'.$rowService['discount_percent'].'" />';
            $rowLbl .= $disDisplay;
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Total Price
            $rowLbl .= '<td style="width:9%; text-align: center; padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="hidden" name="total_price_bf_dis[]" value="'.number_format($rowService['total_price'], 2).'" class="total_price_bf_dis float" />';
            $rowLbl .= '<input type="text" id="total_price_'.$index.'" '.$priceStatus.' name="total_price[]" value="'.number_format($rowService['total_price'] - $rowService['discount_amount'], 2).'" style="width:80%" class="total_price float" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Button Remove
            $rowLbl .= '<td style="width:8%">';
            $rowLbl .= '<img alt="Remove" src="'.$this->webroot.'img/button/cross.png" class="btnRemoveOrderList" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Remove\')" />';
            $rowLbl .= '&nbsp; <img alt="Up" src="'.$this->webroot .'img/button/move_up.png" class="btnMoveUpOrderList" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Up\')" />';
            $rowLbl .= '&nbsp; <img alt="Down" src="'.$this->webroot .'img/button/move_down.png" class="btnMoveDownOrderList" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Down\')" />';
            $rowLbl .= '</td>';
            // Close Tr
            $rowLbl .= '</tr>';
        }
        // Get Miscs
        $sqlQuoteMisc  = mysql_query("SELECT quotation_miscs.description AS description, quotation_miscs.unit_price AS unit_price, quotation_miscs.total_price AS total_price, quotation_miscs.qty AS qty, quotation_miscs.qty_uom_id AS qty_uom_id, quotation_miscs.conversion AS conversion, quotation_miscs.discount_id AS discount_id, quotation_miscs.discount_amount AS discount_amount, quotation_miscs.discount_percent AS discount_percent FROM quotation_miscs WHERE quotation_miscs.quotation_id = ".$id.";");
        while($rowMisc = mysql_fetch_array($sqlQuoteMisc)){
            $index   = rand();
            // Qty
            $qty = $rowMisc['qty'];
            // Get UOM
            $optionUom = '';
            $uoms = ClassRegistry::init('Uom')->find('all', array('fields' => array('Uom.id', 'Uom.name'), 'conditions' => array('Uom.is_active' => 1)));
            foreach($uoms AS $uom){
                $selected = '';
                if($uom['Uom']['id'] == $rowMisc['qty_uom_id']){
                    $selected = 'selected="selected"';
                }
                $optionUom .= '<option conversion="1" value="'.$uom['Uom']['id'].'" '.$selected.' conversion="1">'.$uom['Uom']['name'].'</option>';
            }
            // Open Tr
            $rowLbl .= '<tr class="tblOrderList">';
            // Index
            $rowLbl .= '<td class="first" style="width:5%; text-align: center;padding: 0px; height: 30px;">'.++$index.'</td>';
            // SKU
            $rowLbl .= '<td style="width:11%; text-align: left; padding: 5px;"><span class="lblSKU"></span></td>';
            // Product
            $rowLbl .= '<td style="width:20%; text-align: left; padding: 5px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%; padding: 0px; margin: 0px;">';
            $rowLbl .= '<input type="hidden" id="product_id_'.$index.'" class="product_id" name="product_id[]" value="" />';
            $rowLbl .= '<input type="hidden" id="service_id_'.$index.'" name="service_id[]" value="" />';
            $rowLbl .= '<input type="text" id="product_'.$index.'" readonly="readonly" value="'.$rowMisc['description'].'" name="product[]" class="product validate[required]" style="width: 85%;" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Qty Input
            $rowLbl .= '<td style="width:8%; text-align: center;padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="text" id="qty_'.$index.'" name="qty[]" value="'.$qty.'" style="width:60%;" class="qty interger" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Qty Free Input
            $rowLbl .= '<td style="width:8%; text-align: center;padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="text" id="qty_free_'.$index.'" name="qty_free[]" value="0" style="width:60%;" class="qty_free interger" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // UOM
            $rowLbl .= '<td style="width:13%; padding: 0px; text-align: center">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="hidden" name="conversion[]" class="conversion" value="'.$rowMisc['conversion'].'" />';
            $rowLbl .= '<select id="qty_uom_id_'.$index.'" style="width:80%; height: 20px;" name="qty_uom_id[]" class="qty_uom_id validate[required]">'.$optionUom.'</select>';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Unit Price
            $unitPrice = $rowMisc['unit_price'];
            $rowLbl .= '<td style="width:9%; padding: 0px; text-align: center;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="hidden" class="float unit_cost" name="unit_cost[]" value="0" />';
            $rowLbl .= '<input type="text" id="unit_price_'.$index.'" '.$priceStatus.' name="unit_price[]" value="'.number_format($unitPrice, 2).'" style="width:70%;" class="float unit_price" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Discount
            // Check Permission Discount
            if($allowProductDiscount){
                if($rowMisc['discount_id'] > 0){
                    $disPlay = '';
                }else{
                    $disPlay = 'display: none;';
                }
                $disDisplay  = '<input type="text" id="discount_'.$index.'" name="discount[]" class="discount float" value="'.number_format($rowMisc['discount_amount'], 2).'" style="width: 60%;" readonly="readonly" />';
                $disDisplay .= '<img alt="Remove" src="'.$this->webroot.'img/button/cross.png" class="btnRemoveDiscountOrder" align="absmiddle" style="cursor: pointer; '.$disPlay.'" onmouseover="Tip(\'Remove\')" />';
            }else{
                $disDisplay = '<input type="hidden" id="discount_'.$index.'" name="discount[]" class="discount float" value="'.$rowMisc['discount_amount'].'" style="width: 60%;" readonly="readonly" />';
                $disDisplay .= number_format($rowMisc['discount_amount'], 2);
            }
            $rowLbl .= '<td style="width:9%; padding: 0px; text-align: center">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="hidden" name="discount_id[]" value="'.$rowMisc['discount_id'].'" />';
            $rowLbl .= '<input type="hidden" name="discount_amount[]" value="'.$rowMisc['discount_amount'].'" />';
            $rowLbl .= '<input type="hidden" name="discount_percent[]" value="'.$rowMisc['discount_percent'].'" />';
            $rowLbl .= $disDisplay;
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Total Price
            $rowLbl .= '<td style="width:9%; text-align: center; padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="hidden" name="total_price_bf_dis[]" value="'.number_format($rowMisc['total_price'], 2).'" class="total_price_bf_dis float" />';
            $rowLbl .= '<input type="text" id="total_price_'.$index.'" '.$priceStatus.' name="total_price[]" value="'.number_format($rowMisc['total_price'] - $rowMisc['discount_amount'], 2).'" style="width:80%" class="total_price float" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Button Remove
            $rowLbl .= '<td style="width:8%">';
            $rowLbl .= '<img alt="Remove" src="'.$this->webroot.'img/button/cross.png" class="btnRemoveOrderList" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Remove\')" />';
            $rowLbl .= '&nbsp; <img alt="Up" src="'.$this->webroot .'img/button/move_up.png" class="btnMoveUpOrderList" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Up\')" />';
            $rowLbl .= '&nbsp; <img alt="Down" src="'.$this->webroot .'img/button/move_down.png" class="btnMoveDownOrderList" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Down\')" />';
            $rowLbl .= '</td>';
            // Close Tr
            $rowLbl .= '</tr>';
        }
        $rowList['error']  = 0;
        $rowList['result'] = $rowLbl;
        echo json_encode($rowList);
        exit;
    }
    
    function service($companyId, $branchId) {
        $this->layout = 'ajax';
        $sections = ClassRegistry::init('Section')->find("list", array("conditions" => array("Section.is_active = 1")));
        $services = $this->serviceCombo($companyId, $branchId);
        $this->set(compact('sections', 'services'));
    }

    function serviceCombo($companyId, $branchId) {
        $array = array();
        $services = ClassRegistry::init('Service')->find("all", array("conditions" => array("Service.company_id=" . $companyId . " AND Service.branch_id=" . $branchId . " AND Service.is_active = 1")));
        foreach ($services as $service) {
            $uomId = $service['Service']['uom_id']!=''?$service['Service']['uom_id']:'';
            array_push($array, array('value' => $service['Service']['id'], 'name' => $service['Service']['code']." - ".$service['Service']['name'], 'class' => $service['Section']['id'], 'abbr' => $service['Service']['name'], 'price' => $service['Service']['unit_price'], 'scode' => $service['Service']['code'], 'suom' => $uomId));
        }
        return $array;
    }
    
    function miscellaneous() {
        $this->layout = 'ajax';
    }
    
    function discount($companyId = null) {
        $this->layout = 'ajax';
        if (!$companyId) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $discounts = ClassRegistry::init('Discount')->find("all", array('conditions' => array('Discount.is_active' => 1, 'Discount.company_id' => $companyId), 'order' => array('id DESC')));
        $this->set(compact('discounts'));
    }
    
    function getCustomerContact($customerId){
        $this->layout = 'ajax';
        $result = '<option value="">'.INPUT_SELECT.'</option>';
        if (!empty($customerId)) {
            $customerContacts  = ClassRegistry::init('CustomerContact')->find("all", array('conditions' => array('CustomerContact.customer_id' => $customerId)));
            foreach($customerContacts AS $customerContact){
                $result .= '<option value="'.$customerContact['CustomerContact']['id'].'">'.$customerContact['CustomerContact']['contact_name'].'</option>';
            }
        }
        echo $result;
        exit;
    }
    
    function invoiceDiscount(){
        $this->layout = 'ajax';
    }
    
    function searchCustomer(){
        $this->layout = 'ajax';
        $search = $this->params['url']['q'];
        $this->set(compact('search'));
    }
    
    function approve($id){
        $this->layout = 'ajax';
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        $modified = date("Y-m-d H:i:s");                    
        $this->Order->updateAll(
                array('Order.is_approve' => "1", "Order.approved_by" => $user['User']['id'], 'Order.approved' => "'$modified'"),
                array('Order.id' => $id)
        );
        $this->Helper->saveUserActivity($user['User']['id'], 'Order', 'Save Approve', $id);
        echo MESSAGE_DATA_HAS_BEEN_SAVED;
        exit;
    }
    
    function viewOrderNoApprove(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        // Check Module Exist
        $sqlDash = mysql_query("SELECT id FROM user_dashboards WHERE module_id = 502 AND user_id = {$user['User']['id']} LIMIT 1");
        if(!mysql_num_rows($sqlDash)){
            $this->loadModel('UserDashboard');
            $userDash = array();
            $userDash['UserDashboard']['user_id']      = $user['User']['id'];
            $userDash['UserDashboard']['module_id']    = 502;
            $userDash['UserDashboard']['display']      = 1;
            $userDash['UserDashboard']['auto_refresh'] = 1;
            $userDash['UserDashboard']['time_refresh'] = 5;
            $this->UserDashboard->save($userDash);
        }
    }
    
    function productHistory($productId = null, $customerId = null) {
        $this->layout = 'ajax';
        if (!empty($productId)) {
            $user = $this->getCurrentUser();
            $this->Helper->saveUserActivity($user['User']['id'], 'Order', 'View Product History', '');
            $product = ClassRegistry::init('Product')->find("first", array('conditions' => array('Product.id' => $productId)));
            $this->set(compact('product', 'customerId'));
        } else {
            exit;
        }
    }
    
    function productHistoryAjax($productId = null, $customerId = null) {
        $this->layout = 'ajax';
        $this->set(compact('productId', 'customerId'));
    }

}

?>