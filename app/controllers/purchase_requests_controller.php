<?php

class PurchaseRequestsController extends AppController {

    var $name = 'PurchaseRequests';
    var $components = array('Helper');

    function index() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Order', 'Dashboard');
        $companies = ClassRegistry::init('Company')->find('list',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        $locations = ClassRegistry::init('Location')->find('list', array('joins' => array(array('table' => 'user_locations', 'type' => 'inner', 'conditions' => array('user_locations.location_id=Location.id'))), 'conditions' => array('user_locations.user_id=' . $user['User']['id'], 'Location.is_active=1'), 'order' => 'Location.name'));
        $this->set(compact('companies', 'locations'));
    }

    function ajax($vendor = 'all', $filterStatus = 'all', $filterClose = 'all', $date = '') {
        $this->layout = 'ajax';
        $this->set(compact('vendor', 'filterStatus', 'filterClose', 'date'));   
    }

    function add() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            if ($this->data['PurchaseRequest']['total_amount'] != "") {
                Configure::write('debug', 0);
                $r = 0;
                $restCode = array();
                $dateNow  = date("Y-m-d H:i:s");
                // Load Model
                $this->loadModel('PurchaseRequestTermCondition');
                $this->loadModel('PurchaseRequestDetail');
                $this->loadModel('PurchaseRequestService');
                $this->PurchaseRequest->create();
                $this->data['PurchaseRequest']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $this->data['PurchaseRequest']['created']    = $dateNow;
                $this->data['PurchaseRequest']['created_by'] = $user['User']['id'];
                $this->data['PurchaseRequest']['status'] = 1; 
                if ($this->PurchaseRequest->save($this->data)) {
                    $error = mysql_error();
                    if($error != 'Invalid Data'){
                        $result['po_id'] = $this->PurchaseRequest->id;
                        $purchaseOrderId = $this->PurchaseRequest->getLastInsertId();
                        // Get Module Code
                        $modCode = $this->Helper->getModuleCode($this->data['PurchaseRequest']['pr_code'], $purchaseOrderId, 'pr_code', 'purchase_requests', 'status != -1 AND branch_id = '.$this->data['PurchaseRequest']['branch_id']);
                        // Updaet Module Code
                        mysql_query("UPDATE purchase_requests SET pr_code = '".$modCode."' WHERE id = ".$purchaseOrderId);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($this->data['PurchaseRequest'], 'purchase_requests');
                        $restCode[$r]['pr_code']  = $modCode;
                        $restCode[$r]['modified'] = $dateNow;
                        $restCode[$r]['dbtodo']   = 'purchase_requests';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                        // Insert Term & Condition
                        if(!empty($_POST['term_condition_type_id'])){
                            for ($i = 0; $i < sizeof($_POST['term_condition_type_id']); $i++) {
                                if(!empty($_POST['term_condition_id'][$i])){
                                    $termCondition = array();
                                    // Term Condition
                                    $this->PurchaseRequestTermCondition->create();
                                    $termCondition['PurchaseRequestTermCondition']['purchase_request_id'] = $purchaseOrderId;
                                    $termCondition['PurchaseRequestTermCondition']['term_condition_type_id'] = $_POST['term_condition_type_id'][$i];
                                    $termCondition['PurchaseRequestTermCondition']['term_condition_id'] = $_POST['term_condition_id'][$i];
                                    $this->PurchaseRequestTermCondition->save($termCondition);
                                    // Convert to REST
                                    $restCode[$r] = $this->Helper->convertToDataSync($termCondition['PurchaseRequestTermCondition'], 'purchase_request_term_conditions');
                                    $restCode[$r]['dbtodo']   = 'purchase_request_term_conditions';
                                    $restCode[$r]['actodo']   = 'is';
                                    $r++;
                                }
                            }
                        }
                        for ($i = 0; $i < sizeof($_POST['product_id']); $i++) {
                            if ($_POST['product_id'][$i] != '') {
                                // Save Product in pruchase order detail
                                $PurchaseRequestDetail = array();
                                $this->PurchaseRequestDetail->create();
                                $PurchaseRequestDetail['PurchaseRequestDetail']['purchase_request_id'] = $purchaseOrderId;                              
                                $PurchaseRequestDetail['PurchaseRequestDetail']['product_id'] = $_POST['product_id'][$i];
                                $PurchaseRequestDetail['PurchaseRequestDetail']['qty'] = $_POST['qty'][$i];
                                $PurchaseRequestDetail['PurchaseRequestDetail']['qty_uom_id'] = $_POST['qty_uom_id'][$i];
                                $PurchaseRequestDetail['PurchaseRequestDetail']['unit_cost'] = $_POST['unit_cost'][$i];
                                $PurchaseRequestDetail['PurchaseRequestDetail']['total_cost'] = $_POST['h_total_cost'][$i];
                                $PurchaseRequestDetail['PurchaseRequestDetail']['conversion'] = $_POST['prr_conversion'][$i];
                                $PurchaseRequestDetail['PurchaseRequestDetail']['note'] = $_POST['note'][$i];
                                $this->PurchaseRequestDetail->save($PurchaseRequestDetail);
                                // Convert to REST
                                $restCode[$r] = $this->Helper->convertToDataSync($PurchaseRequestDetail['PurchaseRequestDetail'], 'purchase_request_details');
                                $restCode[$r]['dbtodo']   = 'purchase_request_details';
                                $restCode[$r]['actodo']   = 'is';
                                $r++;
                            } else if($_POST['service_id'][$i] != '') {
                                // Save Product in pruchase order detail
                                $PurchaseRequestService = array();
                                $this->PurchaseRequestService->create();
                                $PurchaseRequestService['PurchaseRequestService']['purchase_request_id'] = $purchaseOrderId;                              
                                $PurchaseRequestService['PurchaseRequestService']['service_id'] = $_POST['service_id'][$i];
                                $PurchaseRequestService['PurchaseRequestService']['qty'] = $_POST['qty'][$i];
                                $PurchaseRequestService['PurchaseRequestService']['unit_cost'] = $_POST['unit_cost'][$i];
                                $PurchaseRequestService['PurchaseRequestService']['total_cost'] = $_POST['h_total_cost'][$i];
                                $PurchaseRequestService['PurchaseRequestService']['note'] = $_POST['note'][$i];
                                $this->PurchaseRequestService->save($PurchaseRequestService);
                                // Convert to REST
                                $restCode[$r] = $this->Helper->convertToDataSync($PurchaseRequestService['PurchaseRequestService'], 'purchase_request_services');
                                $restCode[$r]['dbtodo']   = 'purchase_request_services';
                                $restCode[$r]['actodo']   = 'is';
                                $r++;
                            }
                        }  
                        // Save File Send
                        $this->Helper->sendFileToSync($restCode, 0, 0);
                        $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Order', 'Save Add New', $purchaseOrderId);
                        $result['ppr_id'] = $purchaseOrderId;
                        echo json_encode($result);
                        exit;
                    } else {
                        $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Order', 'Save Add New (Error '.$error.')');
                        $result['error'] = 2;
                        echo json_encode($result);
                        exit;
                    }
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Order', 'Save Add New (Error)');
                    $result['error'] = 2;
                    echo json_encode($result);
                    exit;
                }
            } else {
                $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Order', 'Save Add New (Error Total Amount)');
                $result['error'] = 2;
                echo json_encode($result);
                exit;
            }
        }
        $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Order', 'Add New');
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
                            'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id', 'ModuleCodeBranch.po_code', 'Branch.currency_center_id', 'CurrencyCenter.symbol'),
                            'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])
                        ));
        $currencyCenters = ClassRegistry::init('CurrencyCenter')->find('all', array('conditions' => array('CurrencyCenter.is_active' => 1)));
        $uoms = ClassRegistry::init('Uom')->find('all', array('fields' => array('Uom.id', 'Uom.name'), 'conditions' => array('Uom.is_active' => 1)));
        $shipments = ClassRegistry::init('Shipment')->find('list', array('conditions' => array('Shipment.is_active' => 1)));
        $places    = ClassRegistry::init('Place')->find('list', array('conditions' => array('Place.is_active' => 1)));
        $portOfDischanges = $places;
        $finalPlaceOfDeliveries = $places;
        $this->set(compact("uoms", "companies", "branches", "shipments", "portOfDischanges", "finalPlaceOfDeliveries", "currencyCenters"));
    }
    
    function product($companyId = null, $branchId = null) {
        $this->layout = "ajax";
        $this->set(compact('companyId', 'branchId'));
    }

    function productAjax($companyId = null, $branchId = null, $category = null) {
        $this->layout = "ajax";
        $this->set(compact('companyId', 'branchId', 'category'));
    }
    
    function vendor($companyId) {
        $this->layout = "ajax";
        if(!empty($companyId)){
            $this->set('companyId', $companyId);
        }else{
            exit;
        }
    }

    function vendorAjax($companyId) {
        $this->layout = "ajax";
        if(!empty($companyId)){
            $this->set('companyId', $companyId);
        }else{
            exit;
        }
    }

    function edit($id=null, $action=null, $time = null) {
        $this->layout = 'ajax';
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $this->loadModel('PurchaseRequestTermCondition');
            $this->loadModel('PurchaseRequestDetail');
            $this->loadModel('PurchaseRequestService');
            $purchase = $this->PurchaseRequest->read(null, $this->data['PurchaseRequest']['purchase_request_id']);               
            if ($purchase['PurchaseRequest']['status'] == 1) {
                if ($this->data['PurchaseRequest']['total_amount'] != "") {    
                    Configure::write('debug', 0);
                    $r  = 0;
                    $rb = 0;
                    $restCode = array();
                    $restBackCode  = array();
                    $dateNow  = date("Y-m-d H:i:s");
                    $user = $this->getCurrentUser();
                    $modified = date("Y-m-d H:i:s");
                    $statuEdit = "-1";
                    // Check Old Company & New Company
                    if($this->data['PurchaseRequest']['company_id'] != $purchase['PurchaseRequest']['company_id']){
                        $statuEdit = 0;
                    }
                    // Update old PO as history
                    $this->PurchaseRequest->updateAll(
                            array('PurchaseRequest.status' => $statuEdit,'PurchaseRequest.modified_by' => $user['User']['id'], 'PurchaseRequest.modified' => "'$modified'"), array('PurchaseRequest.id' => $this->data['PurchaseRequest']['purchase_request_id'])
                    );
                    // Convert to REST
                    $restBackCode[$rb]['status']   = $statuEdit;
                    $restBackCode[$rb]['modified'] = $dateNow;
                    $restBackCode[$rb]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
                    $restBackCode[$rb]['dbtodo'] = 'purchase_requests';
                    $restBackCode[$rb]['actodo'] = 'ut';
                    $restBackCode[$rb]['con']    = "sys_code = '".$purchase['PurchaseRequest']['sys_code']."'";
                    // Save File Send Delete
                    $this->Helper->sendFileToSync($restBackCode, 0, 0);
                    // Create New PO
                    $prCode = $this->data['PurchaseRequest']['pr_code'];
                    $this->PurchaseRequest->create();
                    $this->data['PurchaseRequest']['sys_code'] = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                    $this->data['PurchaseRequest']['pr_code']  = $purchase['PurchaseRequest']['pr_code'];
                    $this->data['PurchaseRequest']['created']  = $dateNow;
                    $this->data['PurchaseRequest']['created_by'] = $user['User']['id'];
                    $this->data['PurchaseRequest']['status'] = 1;                       
                    if ($this->PurchaseRequest->save($this->data)) {
                        $error = mysql_error();
                        if($error != 'Invalid Data'){
                            $result['po_id'] = $this->PurchaseRequest->id;
                            $purchaseOrderId = $this->PurchaseRequest->getLastInsertId();
                            $prCode = $this->data['PurchaseRequest']['pr_code'];
                            if($this->data['PurchaseRequest']['branch_id'] != $purchase['PurchaseRequest']['branch_id']){
                                // Get Module Code
                                $modCode = $this->Helper->getModuleCode($prCode, $purchaseOrderId, 'pr_code', 'purchase_requests', 'status != -1 AND branch_id = '.$this->data['PurchaseRequest']['branch_id']);
                                // Updaet Module Code
                                mysql_query("UPDATE purchase_requests SET pr_code = '".$modCode."' WHERE id = ".$purchaseOrderId);
                                $prCode = $modCode;
                            }
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($this->data['PurchaseRequest'], 'purchase_requests');
                            $restCode[$r]['pr_code']  = $prCode;
                            $restCode[$r]['modified'] = $dateNow;
                            $restCode[$r]['dbtodo']   = 'purchase_requests';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                            // Insert Term & Condition
                            if(!empty($_POST['term_condition_type_id'])){
                                for ($i = 0; $i < sizeof($_POST['term_condition_type_id']); $i++) {
                                    if(!empty($_POST['term_condition_id'][$i])){
                                        $termCondition = array();
                                        // Term Condition
                                        $this->PurchaseRequestTermCondition->create();
                                        $termCondition['PurchaseRequestTermCondition']['purchase_request_id'] = $purchaseOrderId;
                                        $termCondition['PurchaseRequestTermCondition']['term_condition_type_id'] = $_POST['term_condition_type_id'][$i];
                                        $termCondition['PurchaseRequestTermCondition']['term_condition_id'] = $_POST['term_condition_id'][$i];
                                        $this->PurchaseRequestTermCondition->save($termCondition);
                                        // Convert to REST
                                        $restCode[$r] = $this->Helper->convertToDataSync($termCondition['PurchaseRequestTermCondition'], 'purchase_request_term_conditions');
                                        $restCode[$r]['dbtodo']   = 'purchase_request_term_conditions';
                                        $restCode[$r]['actodo']   = 'is';
                                        $r++;
                                    }
                                }
                            }
                            /**
                             * Purchase Order Detail
                             */
                            for ($i = 0; $i < sizeof($_POST['product_id']); $i++) {
                                if ($_POST['product_id'][$i] != '') {
                                    // Save Product in pruchase order detail
                                    $PurchaseRequestDetail = array();
                                    $this->PurchaseRequestDetail->create();
                                    $PurchaseRequestDetail['PurchaseRequestDetail']['purchase_request_id'] = $purchaseOrderId;                              
                                    $PurchaseRequestDetail['PurchaseRequestDetail']['product_id'] = $_POST['product_id'][$i];
                                    $PurchaseRequestDetail['PurchaseRequestDetail']['qty'] = $_POST['qty'][$i];
                                    $PurchaseRequestDetail['PurchaseRequestDetail']['qty_uom_id'] = $_POST['qty_uom_id'][$i];
                                    $PurchaseRequestDetail['PurchaseRequestDetail']['unit_cost'] = $_POST['unit_cost'][$i];
                                    $PurchaseRequestDetail['PurchaseRequestDetail']['total_cost'] = $_POST['h_total_cost'][$i];
                                    $PurchaseRequestDetail['PurchaseRequestDetail']['conversion'] = $_POST['prr_conversion'][$i];
                                    $PurchaseRequestDetail['PurchaseRequestDetail']['note'] = $_POST['note'][$i];
                                    $this->PurchaseRequestDetail->save($PurchaseRequestDetail);
                                    // Convert to REST
                                    $restCode[$r] = $this->Helper->convertToDataSync($PurchaseRequestDetail['PurchaseRequestDetail'], 'purchase_request_details');
                                    $restCode[$r]['dbtodo']   = 'purchase_request_details';
                                    $restCode[$r]['actodo']   = 'is';
                                    $r++;
                               } else if($_POST['service_id'][$i] != '') {
                                    // Save Product in pruchase order detail
                                    $PurchaseRequestService = array();
                                    $this->PurchaseRequestService->create();
                                    $PurchaseRequestService['PurchaseRequestService']['purchase_request_id'] = $purchaseOrderId;                              
                                    $PurchaseRequestService['PurchaseRequestService']['service_id'] = $_POST['service_id'][$i];
                                    $PurchaseRequestService['PurchaseRequestService']['qty'] = $_POST['qty'][$i];
                                    $PurchaseRequestService['PurchaseRequestService']['unit_cost'] = $_POST['unit_cost'][$i];
                                    $PurchaseRequestService['PurchaseRequestService']['total_cost'] = $_POST['h_total_cost'][$i];
                                    $PurchaseRequestService['PurchaseRequestService']['note'] = $_POST['note'][$i];
                                    $this->PurchaseRequestService->save($PurchaseRequestService);
                                    // Convert to REST
                                    $restCode[$r] = $this->Helper->convertToDataSync($PurchaseRequestService['PurchaseRequestService'], 'purchase_request_services');
                                    $restCode[$r]['dbtodo']   = 'purchase_request_services';
                                    $restCode[$r]['actodo']   = 'is';
                                    $r++;
                                }
                            }
                            // Save File Send
                            $this->Helper->sendFileToSync($restCode, 0, 0);
                            $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Order', 'Save Edit', $this->data['PurchaseRequest']['purchase_request_id'], $purchaseOrderId);
                            $result['ppr_id'] = $purchaseOrderId;
                            echo json_encode($result);
                            exit;
                        } else {
                            // Update old PO as history
                            $this->PurchaseRequest->updateAll(
                                    array('PurchaseRequest.status' => 1,'PurchaseRequest.modified_by' => $user['User']['id'], 'PurchaseRequest.modified' => "'$modified'"), array('PurchaseRequest.id' => $this->data['PurchaseRequest']['purchase_request_id'])
                            );
                            $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Order', 'Save Edit (Error '.$error.')', $this->data['PurchaseRequest']['purchase_request_id']);
                            $result['error'] = 1;
                            echo json_encode($result);
                            exit;
                        }
                    } else {
                        $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Order', 'Save Edit (Error)', $this->data['PurchaseRequest']['purchase_request_id']);
                        $result['error'] = 1;
                        echo json_encode($result);
                        exit;
                    }
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Order', 'Save Edit (Error Total Amount)', $this->data['PurchaseRequest']['purchase_request_id']);
                    $result['error'] = 2;
                    echo json_encode($result);
                    exit;
                }
            } else {
                $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Order', 'Save Edit (Error Status)', $this->data['PurchaseRequest']['purchase_request_id']);
                $result['error'] = 1;
                echo json_encode($result);
                exit;
            }
        }
        if (!empty($id)) {
            $purchase = $this->PurchaseRequest->read(null, $id);
            if ($purchase['PurchaseRequest']['status'] == 1) {
                $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Order', 'Edit', $id);
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
                                    'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id', 'ModuleCodeBranch.po_code', 'Branch.currency_center_id', 'CurrencyCenter.symbol'),
                                    'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])
                                ));
                $currencyCenters = ClassRegistry::init('CurrencyCenter')->find('all', array('conditions' => array('CurrencyCenter.is_active' => 1)));
                $uoms = ClassRegistry::init('Uom')->find('all', array('fields' => array('Uom.id', 'Uom.name'), 'conditions' => array('Uom.is_active' => 1)));
                $shipments = ClassRegistry::init('Shipment')->find('list', array('conditions' => array('Shipment.is_active' => 1)));
                $places    = ClassRegistry::init('Place')->find('list', array('conditions' => array('Place.is_active' => 1)));
                $portOfDischanges = $places;
                $finalPlaceOfDeliveries = $places;
                $this->data = $this->PurchaseRequest->find("first", array('conditions' => array('PurchaseRequest.id' => $id)));
                if (!empty($this->data)) {
                    $purchaseRequestDetails = ClassRegistry::init('PurchaseRequestDetail')->find("all", array('conditions' => array('PurchaseRequestDetail.purchase_request_id' => $id)));
                    $purchaseRequestServices = ClassRegistry::init('PurchaseRequestService')->find("all", array('conditions' => array('PurchaseRequestService.purchase_request_id' => $id)));
                    $this->set(compact('purchaseRequestDetails', 'purchaseRequestServices', 'uoms', 'companies', 'branches', 'portOfDischanges', 'finalPlaceOfDeliveries', 'shipments', 'id', 'time', 'action', 'currencyCenters'));
                    $db = ConnectionManager::getDataSource('default');
                    mysql_select_db($db->config['database']);
                }
            } else {
                $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Order', 'Edit (Error ID)', $id);
                echo MESSAGE_DATA_INVALID;
                exit;
            }
        }
    }
    
    function view($id = null) {
        $this->layout = 'ajax';
        if (!empty($id)) {
            $user = $this->getCurrentUser();
            $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Order', 'View', $id);
            $purchaseRequest = $this->PurchaseRequest->find("first", array('conditions' => array('PurchaseRequest.id' => $id)));
            if (!empty($purchaseRequest)) {
                $purchaseRequestDetails = ClassRegistry::init('PurchaseRequestDetail')->find("all", array('conditions' => array('PurchaseRequestDetail.purchase_request_id' => $id)));
                $purchaseRequestServices = ClassRegistry::init('PurchaseRequestService')->find("all", array('conditions' => array('PurchaseRequestService.purchase_request_id' => $id)));
                $this->set(compact('purchaseRequest', 'purchaseRequestDetails', 'purchaseRequestServices'));
                $db = ConnectionManager::getDataSource('default');
                mysql_select_db($db->config['database']);
            }
        }
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
        $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Order', 'Delete', $id);
        $modified = date("Y-m-d H:i:s");      
        $purchaseRequest = $this->PurchaseRequest->find("first", array('conditions' => array('PurchaseRequest.id' => $id)));
        // Check Status == 1 && Total Deposit == 0
        if($purchaseRequest['PurchaseRequest']['status'] == 1){
            $this->PurchaseRequest->updateAll(
                    array('PurchaseRequest.status' => 0, "PurchaseRequest.modified_by" => $user['User']['id'],'PurchaseRequest.modified' => "'$modified'"),
                    array('PurchaseRequest.id' => $id)
            );
            // Convert to REST
            $restCode[$r]['status']      = 0;
            $restCode[$r]['modified']    = $dateNow;
            $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
            $restCode[$r]['dbtodo'] = 'purchase_requests';
            $restCode[$r]['actodo'] = 'ut';
            $restCode[$r]['con']    = "sys_code = '".$purchaseRequest['PurchaseRequest']['sys_code']."'";
            // Save File Send
            $this->Helper->sendFileToSync($restCode, 0, 0);
            echo MESSAGE_DATA_HAS_BEEN_DELETED;
        } else {
            echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
        }
        exit;
    }
    
    function printInvoice($id = null) {
        if (!empty($id)) {
            $this->layout = 'ajax';
            $purchaseRequest = ClassRegistry::init('PurchaseRequest')->find("first", array('conditions' => array('PurchaseRequest.id' => $id)));
            if (!empty($purchaseRequest)) {
                $purchaseRequestDetails = ClassRegistry::init('PurchaseRequestDetail')->find("all", array('conditions' => array('PurchaseRequestDetail.purchase_request_id' => $id)));
                $purchaseRequestServices = ClassRegistry::init('PurchaseRequestService')->find("all", array('conditions' => array('PurchaseRequestService.purchase_request_id' => $id)));
                $this->set(compact('purchaseRequest', 'purchaseRequestDetails', 'purchaseRequestServices'));
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
        $r = 0;
        $restCode = array();
        $dateNow  = date("Y-m-d H:i:s");
        $user = $this->getCurrentUser();    
        $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Order', 'Close', $id);
        $modified = date("Y-m-d H:i:s");                    
        $this->PurchaseRequest->updateAll(
                array('PurchaseRequest.is_close' => "1", "PurchaseRequest.modified_by" => $user['User']['id'], 'PurchaseRequest.modified' => "'$modified'"),
                array('PurchaseRequest.id' => $id)
        );
        $purchaseRequest = $this->PurchaseRequest->find("first", array('conditions' => array('PurchaseRequest.id' => $id)));
        // Convert to REST
        $restCode[$r]['is_close']    = 1;
        $restCode[$r]['modified']    = $dateNow;
        $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
        $restCode[$r]['dbtodo'] = 'purchase_requests';
        $restCode[$r]['actodo'] = 'ut';
        $restCode[$r]['con']    = "sys_code = '".$purchaseRequest['PurchaseRequest']['sys_code']."'";
        // Save File Send
        $this->Helper->sendFileToSync($restCode, 0, 0);
        echo MESSAGE_DATA_HAS_BEEN_CLOSED;
        exit;
    }
    
    function open($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $r = 0;
        $restCode = array();
        $dateNow  = date("Y-m-d H:i:s");
        $user = $this->getCurrentUser();
        $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Order', 'Open', $id);
        $modified = date("Y-m-d H:i:s"); 
        $this->PurchaseRequest->updateAll(
                array('PurchaseRequest.is_close' => "0", "PurchaseRequest.modified_by" => $user['User']['id'], 'PurchaseRequest.modified' => "'$modified'"),
                array('PurchaseRequest.id' => $id)
        );
        $purchaseRequest = $this->PurchaseRequest->find("first", array('conditions' => array('PurchaseRequest.id' => $id)));
        // Convert to REST
        $restCode[$r]['is_close']    = 0;
        $restCode[$r]['modified']    = $dateNow;
        $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
        $restCode[$r]['dbtodo'] = 'purchase_requests';
        $restCode[$r]['actodo'] = 'ut';
        $restCode[$r]['con']    = "sys_code = '".$purchaseRequest['PurchaseRequest']['sys_code']."'";
        // Save File Send
        $this->Helper->sendFileToSync($restCode, 0, 0);
        echo MESSAGE_DATA_HAS_BEEN_SAVED;
        exit;
    }
    
    function purchaseRequestView($id = null, $editId = null){
        $this->layout = 'ajax';
        if(!empty($this->data)){
            $user = $this->getCurrentUser();
            for ($i = 0; $i < sizeof($this->data['get_id']); $i++) {
                if($this->data['get_id'][$i] != ""){
                    if($this->data['is_close'][$i] == "1"){
                        $this->data['PurchaseRequestDetail']['is_close'] = 1;
                    }else{
                        $this->data['PurchaseRequestDetail']['is_close'] = 0;
                    }
                    $this->data['PurchaseRequestDetail']['id'] = $this->data['get_id'][$i];
                    $this->data['PurchaseRequestDetail']['modified_by'] = $user['User']['id'];
                    ClassRegistry::init('PurchaseRequestDetail')->save($this->data);
                }
            }
            if($this->data['closeAll'] == 1){
                $this->data['PurchaseRequest']['id'] = $this->data['id'];
                $this->data['PurchaseRequest']['is_close'] = 1;
                $this->data['PurchaseRequest']['modified_by'] = $user['User']['id'];
                ClassRegistry::init('PurchaseRequest')->save($this->data);
            }
            echo MESSAGE_DATA_HAS_BEEN_SAVED;
            exit;
        }
        if (!empty($id)) {
            $purchaseRequest = $this->PurchaseRequest->find("first", array('conditions' => array('PurchaseRequest.id' => $id)));
            if (!empty($purchaseRequest)) {
                $vendor = ClassRegistry::init('Vendor')->find("first", array('conditions' => array('Vendor.id' => $purchaseRequest['PurchaseRequest']['vendor_id'])));
                $purchaseRequestDetails = ClassRegistry::init('PurchaseRequestDetail')->find("all", array('conditions' => array('PurchaseRequestDetail.purchase_request_id' => $id)));
                $this->set(compact('purchaseRequest', 'purchaseRequestDetails', 'vendor', 'editId'));
            }
        }
        
        if(empty($id) && empty($this->data)){
            echo MESSAGE_DATA_HAS_BEEN_CLOSED;
            exit;
        }
    }
    function searchVendor() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $vendors = ClassRegistry::init('Vendor')->find('all', array(
                    'conditions' => array('OR' => array(
                            'Vendor.name LIKE' => '%' . $this->params['url']['q'] . '%',
                            'Vendor.vendor_code LIKE' => '%' . $this->params['url']['q'] . '%',
                        ), 'Vendor.id IN (SELECT vendor_id FROM vendor_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].'))'
                        , 'Vendor.is_active' => 1
                    ),
                ));
        $this->set(compact('vendors'));
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
                            ), 'Product.company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')'
                            , 'Product.is_active' => 1
                            , 'Product.is_packet' => 0
                            , 'Product.price_uom_id > 0'
                            , 'Product.small_val_uom > 0'
                        ),
                        'joins' => $joins,
                        'group' => array(
                            'Product.id'
                        )
                    ));
        $this->set(compact('products'));
    }
    
    function searchProductCode($companyId = null, $branchId = null, $code = null, $field = null) {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $searchField = "";
        if($field == 1){
            $searchField = "(trim(p.code) = '" . mysql_real_escape_string(trim($code)) . "')";
        } else if($field == 2){
            $searchField = "(trim(p.barcode) = '" . mysql_real_escape_string(trim($code)) . "' OR trim(p.code) = '" . mysql_real_escape_string(trim($code)) . "')";
        }
        $product = mysql_query("SELECT CONCAT(p.id,'!-',p.price_uom_id), 
                                p.code, 
                                p.name, 
                                (SELECT SUM(qty) FROM inventories WHERE product_id = p.id), 
                                (SELECT name FROM uoms WHERE id = p.price_uom_id), 
                                IF((SELECT count(*) FROM `inventories` WHERE product_id = p.id AND unit_cost > 0) > 0, 
                                   (SELECT unit_cost FROM `inventories` WHERE product_id = p.id AND unit_cost > 0 ORDER BY `id` DESC LIMIT 1), 
                                   p.default_cost), 
                                p.small_val_uom
                                FROM  products as p 
                                INNER JOIN product_branches ON product_branches.product_id = p.id AND product_branches.branch_id = ".$branchId."
                                INNER JOIN product_pgroups ON product_pgroups.product_id = p.id
                                INNER JOIN pgroups ON pgroups.id = product_pgroups.pgroup_id AND (pgroups.user_apply = 0 OR (pgroups.user_apply = 1 AND pgroups.id IN (SELECT pgroup_id FROM user_pgroups WHERE user_id = ".$user['User']['id'].")))
                                WHERE p.is_active = 1 AND p.is_packet = 0 AND p.price_uom_id > 0 AND p.small_val_uom > 0 AND p.company_id = " . $companyId . " AND ".$searchField."
                                GROUP BY p.id, p.code, p.name 
                                ORDER BY p.code");
        if (@$num = mysql_num_rows($product)) {
            while ($aRow = mysql_fetch_array($product)) {
                $array = explode('!-', $aRow[0]);
                // Check uom last purchase order
                // array[1] uom product               
                $unit_cost = round($aRow[5], 5);
                $data = array();
                $data[] = trim($array[0]);
                $data[] = htmlspecialchars(trim($aRow[1]), ENT_QUOTES, 'UTF-8');
                $data[] = trim($array[1]);
                $data[] = $unit_cost;
                $data[] = htmlspecialchars(trim($aRow[2]), ENT_QUOTES, 'UTF-8');
                $data[] = trim($aRow[6]);
                echo json_encode($data);
            }
        } else {
            echo TABLE_NO_PRODUCT;
        }
        exit;
    }
    
    function editCost(){
        $this->layout = 'ajax';
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
    
    function productHistory($productId = null, $vendorId = null) {
        $this->layout = 'ajax';
        if (!empty($productId)) {
            $user = $this->getCurrentUser();
            $this->Helper->saveUserActivity($user['User']['id'], 'Order', 'View Product History', '');
            $product = ClassRegistry::init('Product')->find("first", array('conditions' => array('Product.id' => $productId)));
            $this->set(compact('product', 'vendorId'));
        } else {
            exit;
        }
    }
    
    function productHistoryAjax($productId = null, $vendorId = null) {
        $this->layout = 'ajax';
        $this->set(compact('productId', 'vendorId'));
    }
}
?>