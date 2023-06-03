<?php

class PurchaseOrdersController extends AppController {

    var $name = 'PurchaseOrders';
    var $components = array('Helper', 'Inventory');

    function index() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Bill', 'Dashboard');
        // Get Loction Setting
        $locSetting = ClassRegistry::init('LocationSetting')->findById(1);
        $locCon     = '';
        if($locSetting['LocationSetting']['location_status'] == 1){
            $locCon = ' AND Location.is_for_sale = 0';
        }
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))),'conditions' => array('user_location_groups.user_id=' . $user['User']['id'], 'LocationGroup.is_active' => '1', 'LocationGroup.location_group_type_id != 1')));
        $locations  = ClassRegistry::init('Location')->find('all', array('joins' => array(array('table' => 'user_locations', 'type' => 'inner', 'conditions' => array('user_locations.location_id=Location.id'))), 'conditions' => array('user_locations.user_id=' . $user['User']['id'] . ' AND Location.is_active=1'.$locCon), 'order' => 'Location.name'));
        $this->set(compact('locationGroups', 'locations'));
    }

    function ajax($filterStatus = 'all', $balance = 'all', $vendor = 'all', $date = '') {
        $this->layout = 'ajax';
        $this->set(compact('filterStatus', 'balance', 'vendor', 'date'));
    }

    function view($id = null) {
        $this->layout = 'ajax';
        if (!empty($id)) {
            $user = $this->getCurrentUser();
            $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Bill', 'View', $id);
            $purchaseOrder = $this->PurchaseOrder->find("first", array('conditions' => array('PurchaseOrder.id' => $id)));
            if (!empty($purchaseOrder)) {
                $vendor = ClassRegistry::init('Vendor')->find("first", array('conditions' => array('Vendor.id' => $purchaseOrder['PurchaseOrder']['vendor_id'])));
                $purchaseOrderDetails = ClassRegistry::init('PurchaseOrderDetail')->find("all", array('conditions' => array('PurchaseOrderDetail.purchase_order_id' => $id)));
                $purchaseOrderServices = ClassRegistry::init('PurchaseOrderService')->find("all", array('conditions' => array('PurchaseOrderService.purchase_order_id' => $id)));
                $purchaseOrderMiscs    = ClassRegistry::init('PurchaseOrderMisc')->find("all", array('conditions' => array('PurchaseOrderMisc.purchase_order_id' => $id)));
                $purchaseOrderReceipts = ClassRegistry::init('Pv')->find("all", array('conditions' => array('Pv.purchase_order_id' => $id, 'Pv.is_void' => 0)));
                $this->set(compact('purchaseOrder', 'purchaseOrderDetails', 'purchaseOrderServices', 'purchaseOrderMiscs', 'purchaseOrderMiscs', 'purchaseOrderReceipts', 'vendor'));
            }
        }
    }

    function add() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {                            
            if ($this->data['PurchaseOrder']['total_amount'] != "") {
                $result = array();
                $totalAmount = $this->data['PurchaseOrder']['total_amount'] - $this->data['PurchaseOrder']['discount_amount'] + $this->data['PurchaseOrder']['total_vat'];
                if($totalAmount < $this->data['total_deposit']){
                    $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Bill', 'Save Add New (Error Deposit > Amount)');
                    $result['code'] = 2;
                    echo json_encode($result);
                    exit;
                }
                $r = 0;
                $restCode = array();
                $dateNow  = date("Y-m-d H:i:s");
                $this->loadModel('PurchaseOrderDetail');
                $this->loadModel('PurchaseOrderMisc');
                $this->loadModel('PurchaseOrderService');
                $this->loadModel('GeneralLedger');
                $this->loadModel('GeneralLedgerDetail');
                $this->loadModel('InventoryValuation');
                $this->loadModel('Company');
                $this->loadModel('AccountType');
                // Chart Account
                $salesMiscAccount = $this->AccountType->findById(10);
                $purchaseDiscAccount = $this->AccountType->findById(15);
                $apAccount = ClassRegistry::init('AccountType')->findById(14);
                
                $this->PurchaseOrder->create();
                $this->data['PurchaseOrder']['sys_code']     = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $this->data['PurchaseOrder']['invoice_date'] = ((empty($this->data['PurchaseOrder']['invoice_date']))?'0000-00-00':$this->data['PurchaseOrder']['invoice_date']);
                $this->data['PurchaseOrder']['ap_id']      = $apAccount['AccountType']['chart_account_id'];
                $this->data['PurchaseOrder']['created']    = $dateNow;
                $this->data['PurchaseOrder']['created_by'] = $user['User']['id'];
                $this->data['PurchaseOrder']['status']     = 3;
                $this->data['PurchaseOrder']['balance']    = $totalAmount;
                $this->data['PurchaseOrder']['is_deposit_reference'] = 0;
                $totalDisByItem = 0;
                if($this->data['PurchaseOrder']['discount_amount'] > 0){
                    if($this->data['PurchaseOrder']['discount_percent'] > 0){
                        $totalDisByItem = $this->data['PurchaseOrder']['discount_percent'];
                    } else {
                        $totalDisByItem = $this->Helper->replaceThousand(number_format($this->data['PurchaseOrder']['discount_amount'] / $this->data['PurchaseOrder']['total_amount'], 9));
                    }
                }
                if ($this->PurchaseOrder->save($this->data)) {
                    $result['po_id'] = $purchaseOrderId = $this->PurchaseOrder->id;
                    $company         = $this->Company->read(null, $this->data['PurchaseOrder']['company_id']);
                    $classId         = $this->Helper->getClassId($company['Company']['id'], $company['Company']['classes'], $this->data['PurchaseOrder']['location_group_id']);
                    if($this->data['PurchaseOrder']['po_code'] == ''){
                        $branchCode  = ClassRegistry::init('ModuleCodeBranch')->find('first', array('conditions' => array('ModuleCodeBranch.branch_id' => $this->data['PurchaseOrder']['branch_id'])));
                        $this->data['PurchaseOrder']['po_code'] = date("y").$branchCode['ModuleCodeBranch']['pb_code'];
                        // Get Module Code
                        $modCode = $this->Helper->getModuleCode($this->data['PurchaseOrder']['po_code'], $purchaseOrderId, 'po_code', 'purchase_orders', 'status != -1 AND branch_id = '.$this->data['PurchaseOrder']['branch_id']);
                        // Updaet Module Code
                        mysql_query("UPDATE purchase_orders SET po_code = '".$modCode."' WHERE id = ".$purchaseOrderId);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($this->data['PurchaseOrder'], 'purchase_orders');
                        $restCode[$r]['po_code']  = $modCode;
                        $restCode[$r]['modified'] = $dateNow;
                        $restCode[$r]['dbtodo']   = 'purchase_orders';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                    } else {
                        $modCode = $this->data['PurchaseOrder']['po_code'];
                    }
                    // ACCOUNT Statement GL
                    $this->GeneralLedger->create();
                    $this->data['GeneralLedger']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                    $this->data['GeneralLedger']['purchase_order_id'] = $purchaseOrderId;
                    $this->data['GeneralLedger']['date']       = $this->data['PurchaseOrder']['order_date'];
                    $this->data['GeneralLedger']['reference']  = $this->data['PurchaseOrder']['po_code'];
                    $this->data['GeneralLedger']['created']    = $dateNow;
                    $this->data['GeneralLedger']['created_by'] = $user['User']['id'];
                    $this->data['GeneralLedger']['is_sys'] = 1;
                    $this->GeneralLedger->save($this->data);
                    $gleaderId = $this->GeneralLedger->getLastInsertId();
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($this->data['GeneralLedger'], 'general_ledgers');
                    $restCode[$r]['modified'] = $dateNow;
                    $restCode[$r]['dbtodo']   = 'general_ledgers';
                    $restCode[$r]['actodo']   = 'is';
                    $r++;
                    /**
                     * ACCOUNT Statement GL Detail A/P
                     */
                    $this->GeneralLedgerDetail->create();
                    $generalLedgerDetail = array();
                    $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $gleaderId;
                    $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id']  = $apAccount['AccountType']['chart_account_id'];
                    $generalLedgerDetail['GeneralLedgerDetail']['vendor_id']   = $this->data['PurchaseOrder']['vendor_id'];
                    $generalLedgerDetail['GeneralLedgerDetail']['company_id']  = $this->data['PurchaseOrder']['company_id'];
                    $generalLedgerDetail['GeneralLedgerDetail']['branch_id']   = $this->data['PurchaseOrder']['branch_id'];
                    $generalLedgerDetail['GeneralLedgerDetail']['location_group_id'] = $this->data['PurchaseOrder']['location_group_id'];
                    $generalLedgerDetail['GeneralLedgerDetail']['location_id'] = $this->data['PurchaseOrder']['location_id'];
                    $generalLedgerDetail['GeneralLedgerDetail']['type']   = "Bill";
                    $generalLedgerDetail['GeneralLedgerDetail']['memo']   = "ICS: PB # " . $this->data['PurchaseOrder']['po_code'];
                    $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                    $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $totalAmount;
                    $generalLedgerDetail['GeneralLedgerDetail']['class_id'] = $classId;
                    $this->GeneralLedgerDetail->save($generalLedgerDetail);
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                    $restCode[$r]['dbtodo']   = 'general_ledger_details';
                    $restCode[$r]['actodo']   = 'is';
                    $r++;
                    /* Purchase Order Total Discount */
                    if ($this->data['PurchaseOrder']['discount_amount'] > 0) {
                        $this->GeneralLedgerDetail->create();
                        $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id']  = $purchaseDiscAccount['AccountType']['chart_account_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                        $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $this->data['PurchaseOrder']['discount_amount'];
                        $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: PB # ' . $this->data['PurchaseOrder']['po_code'] . ' Total Discount';
                        $this->GeneralLedgerDetail->save($generalLedgerDetail);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                        $restCode[$r]['dbtodo']   = 'general_ledger_details';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                    }
                    /**
                     * Purchase Order Total VAT
                     */
                    if (($this->data['PurchaseOrder']['total_vat']) > 0) {
                        $this->GeneralLedgerDetail->create();
                        $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id']  = $this->data['PurchaseOrder']['vat_chart_account_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $this->data['PurchaseOrder']['total_vat'];
                        $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                        $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: PB # ' . $this->data['PurchaseOrder']['po_code'] . ' Total VAT';
                        $this->GeneralLedgerDetail->save($generalLedgerDetail);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                        $restCode[$r]['dbtodo']   = 'general_ledger_details';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                    }
                    $this->loadModel('PurchaseReceiveResult');
                    $this->PurchaseReceiveResult->create();
                    $purchaseRecResult = array();
                    $purchaseRecResult['PurchaseReceiveResult']['sys_code'] = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                    $purchaseRecResult['PurchaseReceiveResult']['purchase_order_id'] = $purchaseOrderId;
                    $purchaseRecResult['PurchaseReceiveResult']['date']       = $this->data['PurchaseOrder']['order_date'];
                    $purchaseRecResult['PurchaseReceiveResult']['created']    = $dateNow;
                    $purchaseRecResult['PurchaseReceiveResult']['created_by'] = $user['User']['id'];
                    $this->PurchaseReceiveResult->save($purchaseRecResult);
                    $purchaseRecResultId = $this->PurchaseReceiveResult->id;
                    // Update Code Receive Result Code
                    $sqlRecCode = mysql_query("SELECT CONCAT('".date("y")."GRR','',LPAD(((SELECT count(tmp.id) FROM `purchase_receive_results` as tmp WHERE tmp.code LIKE '".date("y")."GRR%' AND tmp.id < ".$purchaseRecResultId.") + 1),7,'0')) AS code");
                    $rowRecCode = mysql_fetch_array($sqlRecCode);
                    mysql_query("UPDATE purchase_receive_results SET code = '".$rowRecCode['code']."' WHERE id = ".$purchaseRecResultId);
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($purchaseRecResult['PurchaseReceiveResult'], 'purchase_receive_results');
                    $restCode[$r]['code']   = $rowRecCode['code'];
                    $restCode[$r]['dbtodo'] = 'purchase_receive_results';
                    $restCode[$r]['actodo'] = 'is';
                    $r++;
                    // Get Decimal
                    $sqlOption = mysql_query("SELECT product_cost_decimal FROM setting_options");
                    $rowOption = mysql_fetch_array($sqlOption);
                    for ($i = 0; $i < sizeof($_POST['product_id']); $i++) {
                        if ($_POST['product_id'][$i] != '' && $_POST['qty_uom_id'][$i] != '' && $_POST['qty'][$i] != '' && $_POST['qty'][$i] != null && $_POST['qty_free'][$i] != '' && $_POST['qty_free'][$i] != null && ($_POST['qty'][$i] + $_POST['qty_free'][$i]) > 0) {
                            // Account General Legder Detail
                            $qtyOrderSmall = ($_POST['qty'][$i] + $_POST['qty_free'][$i]) * $_POST['pb_conversion'][$i];
                            $qtyOrder      = ($_POST['qty'][$i] + $_POST['qty_free'][$i]) / ($_POST['small_uom_val_pb'][$i] / $_POST['pb_conversion'][$i]);
                            if($_POST['total_cost'][$i] > 0) {
                                $costDisByTotal = 0;
                                if($totalDisByItem > 0) {
                                    $costDisByTotal = $this->Helper->replaceThousand(number_format(($_POST['total_cost'][$i] * $totalDisByItem) / 100, 9));
                                }
                                $purchaseCost = ($_POST['total_cost'][$i] - $costDisByTotal);
                                $costOrder    = ($purchaseCost / ($_POST['qty'][$i] + $_POST['qty_free'][$i])) * ($_POST['small_uom_val_pb'][$i] / $_POST['pb_conversion'][$i]);
                            } else {
                                $costOrder = 0;
                            }
                            $total_amount  = ($_POST['total_cost'][$i] + $_POST['discount'][$i]);
                            
                            // Save Product in pruchase order detail
                            $this->PurchaseOrderDetail->create();
                            $PurchaseOrderDetail = array();
                            $PurchaseOrderDetail['PurchaseOrderDetail']['sys_code']          = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                            $PurchaseOrderDetail['PurchaseOrderDetail']['purchase_order_id'] = $purchaseOrderId;
                            $PurchaseOrderDetail['PurchaseOrderDetail']['discount_id']       = $_POST['discount_id'][$i];
                            $PurchaseOrderDetail['PurchaseOrderDetail']['discount_amount']   = $_POST['discount'][$i];
                            $PurchaseOrderDetail['PurchaseOrderDetail']['discount_percent']  = $_POST['discount_percent'][$i];
                            $PurchaseOrderDetail['PurchaseOrderDetail']['product_id'] = $_POST['product_id'][$i];
                            $PurchaseOrderDetail['PurchaseOrderDetail']['max_order']  = $_POST['max_order'][$i];
                            $PurchaseOrderDetail['PurchaseOrderDetail']['qty']        = $_POST['qty'][$i];
                            $PurchaseOrderDetail['PurchaseOrderDetail']['qty_free']   = $_POST['qty_free'][$i];
                            $PurchaseOrderDetail['PurchaseOrderDetail']['qty_uom_id'] = $_POST['qty_uom_id'][$i];
                            $PurchaseOrderDetail['PurchaseOrderDetail']['default_cost'] = ($_POST['unit_cost'][$i] * ($_POST['small_uom_val_pb'][$i] / $_POST['pb_conversion'][$i]));
                            $PurchaseOrderDetail['PurchaseOrderDetail']['unit_cost']    = $_POST['unit_cost'][$i];
                            $PurchaseOrderDetail['PurchaseOrderDetail']['total_cost']   = $_POST['h_total_cost'][$i];
                            $PurchaseOrderDetail['PurchaseOrderDetail']['conversion']   = $_POST['pb_conversion'][$i];
                            $PurchaseOrderDetail['PurchaseOrderDetail']['lots_number']  = $_POST['lots_number'][$i]!=""?$_POST['lots_number'][$i]:0;
                            $PurchaseOrderDetail['PurchaseOrderDetail']['date_expired'] = ((empty($_POST['date_expired'][$i]))?'0000-00-00':$_POST['date_expired'][$i]);
                            $PurchaseOrderDetail['PurchaseOrderDetail']['note']         = $_POST['note'][$i];
                            $PurchaseOrderDetail['PurchaseOrderDetail']['new_unit_cost']  = $this->Helper->replaceThousand(number_format($costOrder, $rowOption[0]));
                            $this->PurchaseOrderDetail->save($PurchaseOrderDetail);
                            $PurchaseOrderDetailId = $this->PurchaseOrderDetail->id;
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($PurchaseOrderDetail['PurchaseOrderDetail'], 'purchase_order_details');
                            $restCode[$r]['dbtodo']   = 'purchase_order_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                            
                            $sqlPro  = mysql_query("SELECT small_val_uom, unit_cost, sys_code FROM products WHERE id = ".$_POST['product_id'][$i]);
                            $rowPro  = mysql_fetch_array($sqlPro);
                            // Update unit cost for product                             
                            mysql_query("UPDATE products SET unit_cost = '".$PurchaseOrderDetail['PurchaseOrderDetail']['new_unit_cost']."' WHERE id=".$_POST['product_id'][$i]);
                            // Convert to REST
                            $restCode[$r]['unit_cost'] = $PurchaseOrderDetail['PurchaseOrderDetail']['new_unit_cost'];
                            $restCode[$r]['dbtodo'] = 'products';
                            $restCode[$r]['actodo'] = 'ut';
                            $restCode[$r]['con']    = "sys_code = '".$rowPro['sys_code']."'";
                            $r++;
                            if ($rowPro['unit_cost'] != $PurchaseOrderDetail['PurchaseOrderDetail']['new_unit_cost']) {
                                mysql_query("INSERT INTO `product_unit_cost_histories` (`product_id`, `purchase_order_id`, `old_cost`, `new_cost`, `type`, `created`, `created_by`) 
                                             VALUES (".$_POST['product_id'][$i].", ".$purchaseOrderId.", ".$rowPro['unit_cost'].", ".$PurchaseOrderDetail['PurchaseOrderDetail']['new_unit_cost'].", 'PB', '".$dateNow."', ".$user['User']['id'].");");
                                // Convert to REST
                                $restCode[$r]['old_cost']   = $rowPro['unit_cost'];
                                $restCode[$r]['new_cost']   = $PurchaseOrderDetail['PurchaseOrderDetail']['new_unit_cost'];
                                $restCode[$r]['created']    = $dateNow;
                                $restCode[$r]['created_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
                                $restCode[$r]['product_id'] = $this->Helper->getSQLSysCode("products", $_POST['product_id'][$i]);
                                $restCode[$r]['purchase_order_id'] = $this->Helper->getSQLSysCode("purchase_orders", $purchaseOrderId);
                                $restCode[$r]['dbtodo'] = 'product_unit_cost_histories';
                                $restCode[$r]['actodo'] = 'is';
                                $r++;
                            }
                            
                            // Inventory
                            // Update Inventory (Purchase)
                            $data = array();
                            $data['module_type']       = 6;
                            $data['purchase_order_id'] = $purchaseOrderId;
                            $data['product_id']        = $_POST['product_id'][$i];
                            $data['location_id']       = $this->data['PurchaseOrder']['location_id'];
                            $data['location_group_id'] = $this->data['PurchaseOrder']['location_group_id'];
                            $data['lots_number']  = $PurchaseOrderDetail['PurchaseOrderDetail']['lots_number'];
                            $data['expired_date'] = $PurchaseOrderDetail['PurchaseOrderDetail']['date_expired'];
                            $data['date']         = $this->data['PurchaseOrder']['order_date'];
                            $data['total_qty']    = $qtyOrderSmall;
                            $data['total_order']  = $qtyOrderSmall;
                            $data['total_free']   = 0;
                            $data['user_id']      = $user['User']['id'];
                            $data['customer_id']  = "";
                            $data['vendor_id']    = $this->data['PurchaseOrder']['vendor_id'];
                            $data['unit_cost']    = $_POST['unit_cost'][$i];
                            $data['unit_price']   = 0;
                            // Update Invetory Location
                            $this->Inventory->saveInventory($data);
                            // Update Inventory Group
                            $this->Inventory->saveGroupTotalDetail($data);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($data, 'inventories');
                            $restCode[$r]['module_type']  = 6;
                            $restCode[$r]['total_qty']    = $qtyOrderSmall;
                            $restCode[$r]['total_order']  = $qtyOrderSmall;
                            $restCode[$r]['total_free']   = 0;
                            $restCode[$r]['expired_date'] = $data['expired_date'];
                            $restCode[$r]['unit_cost']    = $_POST['unit_cost'][$i];
                            $restCode[$r]['unit_price']   = 0;
                            $restCode[$r]['customer_id']  = "";
                            $restCode[$r]['vendor_id']    = $this->Helper->getSQLSyncCode("vendors", $this->data['PurchaseOrder']['vendor_id']);
                            $restCode[$r]['purchase_order_id'] = $this->Helper->getSQLSyncCode("purchase_orders", $purchaseOrderId);
                            $restCode[$r]['product_id']        = $this->Helper->getSQLSyncCode("products", $_POST['product_id'][$i]);
                            $restCode[$r]['location_id']       = $this->Helper->getSQLSyncCode("locations", $this->data['PurchaseOrder']['location_id']);
                            $restCode[$r]['location_group_id'] = $this->Helper->getSQLSyncCode("location_groups", $this->data['PurchaseOrder']['location_group_id']);
                            $restCode[$r]['user_id']           = $this->Helper->getSQLSyncCode("users", $user['User']['id']);
                            $restCode[$r]['dbtype']  = 'saveInv,GroupDetail';
                            $restCode[$r]['actodo']  = 'inv';
                            $r++;
                            // Purchase Receive
                            ClassRegistry::init('PurchaseReceive')->create();
                            $this->data['PurchaseReceive']['purchase_receive_result_id'] = $purchaseRecResultId;
                            $this->data['PurchaseReceive']['purchase_order_id']          = $purchaseOrderId;
                            $this->data['PurchaseReceive']['purchase_order_detail_id']   = $PurchaseOrderDetailId;
                            $this->data['PurchaseReceive']['product_id'] = $_POST['product_id'][$i];
                            $this->data['PurchaseReceive']['qty']        = ($_POST['qty'][$i] + $_POST['qty_free'][$i]);
                            $this->data['PurchaseReceive']['qty_uom_id'] = $_POST['qty_uom_id'][$i];
                            $this->data['PurchaseReceive']['conversion'] = $_POST['pb_conversion'][$i];
                            $this->data['PurchaseReceive']['received_date'] = $this->data['PurchaseOrder']['order_date'];
                            $this->data['PurchaseReceive']['lots_number']   = $data['lots_number'];
                            $this->data['PurchaseReceive']['date_expired']  = $data['expired_date'];
                            $this->data['PurchaseReceive']['created']    = $dateNow;
                            $this->data['PurchaseReceive']['created_by'] = $user['User']['id'];
                            $this->data['PurchaseReceive']['status']     = 1;
                            ClassRegistry::init('PurchaseReceive')->save($this->data);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($this->data['PurchaseReceive'], 'purchase_receives');
                            $restCode[$r]['dbtodo'] = 'purchase_receives';
                            $restCode[$r]['actodo'] = 'is';
                            $r++;
                            if($this->data['calculate_cogs'] == 1){
                                // Inventory Valuation
                                $inv_valutaion = array();
                                $this->InventoryValuation->create();
                                $inv_valutaion['InventoryValuation']['sys_code']  = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                                $inv_valutaion['InventoryValuation']['purchase_order_id'] = $purchaseOrderId;
                                $inv_valutaion['InventoryValuation']['purchase_order_detail_id'] = $PurchaseOrderDetailId;
                                $inv_valutaion['InventoryValuation']['company_id'] = $this->data['PurchaseOrder']['company_id'];
                                $inv_valutaion['InventoryValuation']['branch_id']  = $this->data['PurchaseOrder']['branch_id'];
                                $inv_valutaion['InventoryValuation']['type']       = "Bill";
                                $inv_valutaion['InventoryValuation']['reference']  = $this->data['PurchaseOrder']['po_code'];
                                $inv_valutaion['InventoryValuation']['vendor_id']  = $this->data['PurchaseOrder']['vendor_id'];
                                $inv_valutaion['InventoryValuation']['date']       = $this->data['PurchaseOrder']['order_date'];
                                $inv_valutaion['InventoryValuation']['created']    = $this->data['PurchaseOrder']['order_date']." 00:00:00";
                                $inv_valutaion['InventoryValuation']['pid']        = $_POST['product_id'][$i];
                                $inv_valutaion['InventoryValuation']['small_qty']  = $qtyOrderSmall;
                                $inv_valutaion['InventoryValuation']['qty']  = $this->Helper->replaceThousand(number_format($qtyOrder, 6));
                                $inv_valutaion['InventoryValuation']['cost'] = $this->Helper->replaceThousand(number_format($costOrder, $rowOption[0]));
                                $this->InventoryValuation->saveAll($inv_valutaion);
                                // Convert to REST
                                $restCode[$r] = $this->Helper->convertToDataSync($inv_valutaion['InventoryValuation'], 'inventory_valuations');
                                $restCode[$r]['dbtodo']   = 'inventory_valuations';
                                $restCode[$r]['actodo']   = 'is';
                                $r++;
                            }
                            // General Ledger Detail (Product Income)
                            $this->GeneralLedgerDetail->create();
                            $queryInvAccount = mysql_query("SELECT IFNULL((IFNULL((SELECT chart_account_id FROM accounts WHERE product_id = " . $_POST['product_id'][$i] . " AND account_type_id=1),(SELECT chart_account_id FROM pgroup_accounts WHERE pgroup_id = (SELECT pgroup_id FROM product_pgroups WHERE product_id = " . $_POST['product_id'][$i] . " ORDER BY id  DESC LIMIT 1) AND account_type_id=1))),(SELECT chart_account_id FROM account_types WHERE id=1))");
                            $dataInvAccount  = mysql_fetch_array($queryInvAccount);
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $dataInvAccount[0];
                            $generalLedgerDetail['GeneralLedgerDetail']['product_id'] = $_POST['product_id'][$i];
                            $generalLedgerDetail['GeneralLedgerDetail']['service_id'] = NULL;
                            $generalLedgerDetail['GeneralLedgerDetail']['memo']   = "ICS: PB # " . $this->data['PurchaseOrder']['po_code'] . " Product # " . $_POST['product_name'][$i];
                            $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $total_amount;
                            $this->GeneralLedgerDetail->saveAll($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo'] = 'general_ledger_details';
                            $restCode[$r]['actodo'] = 'is';
                            $r++;
                            // General Ledger Detail (Product Discount)
                            if ($_POST['discount'][$i] > 0) {
                                $this->GeneralLedgerDetail->create();
                                $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $purchaseDiscAccount['AccountType']['chart_account_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['memo']   = "ICS: PB # " . $this->data['PurchaseOrder']['po_code'] . " Product # " . $_POST['product_name'][$i] . ' Discount';
                                $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                                $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $_POST['discount'][$i];
                                $this->GeneralLedgerDetail->saveAll($generalLedgerDetail);
                                // Convert to REST
                                $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                                $restCode[$r]['dbtodo'] = 'general_ledger_details';
                                $restCode[$r]['actodo'] = 'is';
                                $r++;
                            }
                            // End Account General Legder Detail
                        } else if (!empty($_POST['service_id'][$i])) {
                            // Purchase Order Service
                            $this->PurchaseOrderService->create();
                            $PurchaseOrderService = array();
                            $PurchaseOrderService['PurchaseOrderService']['purchase_order_id'] = $purchaseOrderId;
                            $PurchaseOrderService['PurchaseOrderService']['discount_id']       = $_POST['discount_id'][$i];
                            $PurchaseOrderService['PurchaseOrderService']['discount_amount']   = $_POST['discount'][$i];
                            $PurchaseOrderService['PurchaseOrderService']['discount_percent']  = $_POST['discount_percent'][$i];
                            $PurchaseOrderService['PurchaseOrderService']['service_id'] = $_POST['service_id'][$i];
                            $PurchaseOrderService['PurchaseOrderService']['qty']        = $_POST['qty'][$i];
                            $PurchaseOrderService['PurchaseOrderService']['qty_free']   = $_POST['qty_free'][$i];
                            $PurchaseOrderService['PurchaseOrderService']['unit_cost']  = $_POST['unit_cost'][$i];
                            $PurchaseOrderService['PurchaseOrderService']['total_cost'] = $_POST['h_total_cost'][$i];
                            $PurchaseOrderService['PurchaseOrderService']['note']       = $_POST['note'][$i];
                            $this->PurchaseOrderService->save($PurchaseOrderService);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($PurchaseOrderService['PurchaseOrderService'], 'purchase_order_services');
                            $restCode[$r]['dbtodo'] = 'purchase_order_services';
                            $restCode[$r]['actodo'] = 'is';
                            $r++;
                            // General Ledger Detail (Service)
                            $this->GeneralLedgerDetail->create();
                            $queryServiceAccount = mysql_query("SELECT chart_account_id FROM account_types WHERE id=9");
                            $dataServiceAccount  = mysql_fetch_array($queryServiceAccount);
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $dataServiceAccount[0];
                            $generalLedgerDetail['GeneralLedgerDetail']['service_id'] = $_POST['service_id'][$i];
                            $generalLedgerDetail['GeneralLedgerDetail']['product_id'] = NULL;
                            $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $_POST['h_total_cost'][$i];
                            $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: PB # ' . $this->data['PurchaseOrder']['po_code'] . ' Service # ' . $_POST['product_name'][$i];
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo'] = 'general_ledger_details';
                            $restCode[$r]['actodo'] = 'is';
                            $r++;
                            // General Ledger Detail (Service Discount)
                            if (abs($_POST['discount'][$i]) > 0) {
                                $this->GeneralLedgerDetail->create();
                                $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $purchaseDiscAccount['AccountType']['chart_account_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['memo']   = "ICS: PB # " . $this->data['PurchaseOrder']['po_code'] . " Service # " . $_POST['product_name'][$i] . ' Discount';
                                $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                                $generalLedgerDetail['GeneralLedgerDetail']['credit'] = abs($_POST['discount'][$i]);
                                $this->GeneralLedgerDetail->saveAll($generalLedgerDetail);
                                // Convert to REST
                                $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                                $restCode[$r]['dbtodo'] = 'general_ledger_details';
                                $restCode[$r]['actodo'] = 'is';
                                $r++;
                            }
                        } else{
                            // Purchase Miscellaneous
                            $this->PurchaseOrderMisc->create();
                            $PurchaseOrderMiscs = array();
                            $PurchaseOrderMiscs['PurchaseOrderMisc']['purchase_order_id'] = $purchaseOrderId;
                            $PurchaseOrderMiscs['PurchaseOrderMisc']['discount_id']       = $_POST['discount_id'][$i];
                            $PurchaseOrderMiscs['PurchaseOrderMisc']['discount_amount']   = $_POST['discount'][$i];
                            $PurchaseOrderMiscs['PurchaseOrderMisc']['discount_percent']  = $_POST['discount_percent'][$i];
                            $PurchaseOrderMiscs['PurchaseOrderMisc']['description'] = $_POST['product_name'][$i];
                            $PurchaseOrderMiscs['PurchaseOrderMisc']['qty_uom_id']  = $_POST['qty_uom_id'][$i];
                            $PurchaseOrderMiscs['PurchaseOrderMisc']['qty']         = $_POST['qty'][$i];
                            $PurchaseOrderMiscs['PurchaseOrderMisc']['qty_free']    = $_POST['qty_free'][$i];
                            $PurchaseOrderMiscs['PurchaseOrderMisc']['unit_cost']   = $_POST['unit_cost'][$i];
                            $PurchaseOrderMiscs['PurchaseOrderMisc']['total_cost']  = $_POST['h_total_cost'][$i];
                            $PurchaseOrderMiscs['PurchaseOrderMisc']['note']        = $_POST['note'][$i];
                            $this->PurchaseOrderMisc->save($PurchaseOrderMiscs);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($PurchaseOrderMiscs['PurchaseOrderMisc'], 'purchase_order_miscs');
                            $restCode[$r]['dbtodo'] = 'purchase_order_miscs';
                            $restCode[$r]['actodo'] = 'is';
                            $r++;
                            // General Ledger Detail (Misc)
                            $this->GeneralLedgerDetail->create();
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $salesMiscAccount['AccountType']['chart_account_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['service_id'] = NULL;
                            $generalLedgerDetail['GeneralLedgerDetail']['product_id'] = NULL;
                            $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $_POST['h_total_cost'][$i];
                            $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: PB # ' . $this->data['PurchaseOrder']['po_code'] . ' Misc # ' . $_POST['product_name'][$i];
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo'] = 'general_ledger_details';
                            $restCode[$r]['actodo'] = 'is';
                            $r++;
                            // General Ledger Detail (Misc Discount)
                            if (abs($_POST['discount'][$i]) > 0) {
                                $this->GeneralLedgerDetail->create();
                                $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $purchaseDiscAccount['AccountType']['chart_account_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                                $generalLedgerDetail['GeneralLedgerDetail']['credit'] = abs($_POST['discount'][$i]);
                                $generalLedgerDetail['GeneralLedgerDetail']['memo']   = "ICS: PB # " . $this->data['PurchaseOrder']['po_code'] . " Misc # " . $_POST['product_name'][$i] . ' Discount';
                                $this->GeneralLedgerDetail->saveAll($generalLedgerDetail);
                                // Convert to REST
                                $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                                $restCode[$r]['dbtodo'] = 'general_ledger_details';
                                $restCode[$r]['actodo'] = 'is';
                                $r++;
                            }
                        }
                    }
                    // Save File Send
                    $this->Helper->sendFileToSync($restCode, 0, 0);
                    // Recalculate Average Cost
                    $dateReca = date("Y-m-d", strtotime(date("Y-m-d", strtotime($this->data['PurchaseOrder']['order_date'])) . " -1 day"));
                    mysql_query("UPDATE tracks SET val='".$dateReca."', is_recalculate = 1 WHERE id=1");
                    // Return Purchase Bill Id
                    $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Bill', 'Save Add New', $purchaseOrderId);
                    $result['code']  = 3;
                    $result['po_id'] = $purchaseOrderId;
                    echo json_encode($result);
                    exit;
                } else {      
                    $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Bill', 'Save Add New (Error)');
                    $result['code'] = 2;
                    echo json_encode($result);
                    exit;
                }
            } else {
                $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Bill', 'Save Add New (Error Total Amount)');
                $result['code'] = 2;
                echo json_encode($result);
                exit;
            }
        }
        $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Bill', 'Add New');
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
                            'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id', 'ModuleCodeBranch.pb_code', 'Branch.currency_center_id', 'CurrencyCenter.symbol'),
                            'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])
                        ));
        // Get Loction Setting
        $locSetting = ClassRegistry::init('LocationSetting')->findById(1);
        $locCon     = '';
        if($locSetting['LocationSetting']['location_status'] == 1){
            $locCon = ' AND Location.is_for_sale = 0';
        }
        $locations  = ClassRegistry::init('Location')->find('all', array('joins' => array(array('table' => 'user_locations', 'type' => 'inner', 'conditions' => array('user_locations.location_id=Location.id'))), 'conditions' => array('user_locations.user_id=' . $user['User']['id'] . ' AND Location.is_active=1'.$locCon), 'order' => 'Location.name'));
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))),'conditions' => array('user_location_groups.user_id=' . $user['User']['id'], 'LocationGroup.is_active' => '1', 'LocationGroup.location_group_type_id != 1')));
        $uoms = ClassRegistry::init('Uom')->find('all', array('fields' => array('Uom.id', 'Uom.name'), 'conditions' => array('Uom.is_active' => 1)));
        $shipments = ClassRegistry::init('Shipment')->find('list', array('conditions' => array('Shipment.is_active' => 1)));
        $this->set(compact("locations", "uoms", 'locationGroups', 'shipments', 'apAccountId', 'companies', 'branches'));
    }

    function delete($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $r = 0;
        $restCode = array();
        $dateNow  = date("Y-m-d H:i:s");
        $this->loadModel('PurchaseOrderDetail');
        $this->loadModel('InventoryValuation');
        $this->loadModel('GeneralLedger');
        $user = $this->getCurrentUser();
        $purchase = $this->PurchaseOrder->read(null, $id);
        $purchaseOrderDetails = ClassRegistry::init('PurchaseOrderDetail')->find("all", array('conditions' => array('PurchaseOrderDetail.purchase_order_id' => $id)));
        $queryHasReceipt = mysql_query("SELECT id FROM pvs WHERE purchase_order_id=" . $id . " AND is_void = 0");
        $queryHasReturn = mysql_query("SELECT id FROM purchase_returns WHERE status>0 AND purchase_order_id=" . $id);
        $queryHasReturnNew = mysql_query("SELECT id FROM invoice_pbc_with_pbs WHERE status > 0 AND purchase_order_id=" . $id);
        if(@mysql_num_rows($queryHasReceipt) || @mysql_num_rows($queryHasReturn) || @mysql_num_rows($queryHasReturnNew) || $purchase['PurchaseOrder']['status'] != 3){
            $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Bill', 'Delete (Error has transaction with other modules)', $id);
            echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
            exit;
        }
        $isLock = 0;
        foreach($purchaseOrderDetails AS $purchaseOrderDetail){
            // Inventory By Order Date
            $totalQtyByDate = 0;
            $sqlInv = mysql_query("SELECT SUM(qty) FROM inventories WHERE location_group_id = ".$purchase['PurchaseOrder']['location_group_id']." AND location_id = ".$purchase['PurchaseOrder']['location_id']." AND product_id = ".$purchaseOrderDetail['Product']['id']." AND date <= '".$purchase['PurchaseOrder']['order_date']."' AND date_expired = '".$purchaseOrderDetail['PurchaseOrderDetail']['date_expired']."'");
            if(mysql_num_rows($sqlInv)){
                $rowInv = mysql_fetch_array($sqlInv);
                $totalQtyByDate = $rowInv[0];
            }
            // Total Qty On Hand
            $totalQtyOnHand = 0;
            $sqlOnHand = mysql_query("SELECT SUM(total_qty - total_order) FROM ".$purchase['PurchaseOrder']['location_id']."_inventory_totals WHERE product_id = ".$purchaseOrderDetail['Product']['id']." AND expired_date = '".$purchaseOrderDetail['PurchaseOrderDetail']['date_expired']."'");
            if(mysql_num_rows($sqlOnHand)){
                $rowOnHand = mysql_fetch_array($sqlOnHand);
                $totalQtyOnHand = $rowOnHand[0];
            }
            $totalQtyPurchase = ($purchaseOrderDetail['PurchaseOrderDetail']['qty'] + $purchaseOrderDetail['PurchaseOrderDetail']['qty_free']) * $purchaseOrderDetail['PurchaseOrderDetail']['conversion'];
            if($totalQtyPurchase > $totalQtyByDate){
                $isLock = 1;
                break;
            } else {
                if($totalQtyPurchase > $totalQtyOnHand){
                    $isLock = 1;
                    break;
                }
            }
        }
        if($isLock == 1){
            $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Bill', 'Delete (Error with total qty)', $id);
            echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
            exit;
        }
        $this->PurchaseOrder->updateAll(
                array('PurchaseOrder.status' => "0", 'PurchaseOrder.modified_by' => $user['User']['id']), array('PurchaseOrder.id' => $id)
        );
        // Convert to REST
        $restCode[$r]['status']      = 0;
        $restCode[$r]['modified']    = $dateNow;
        $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
        $restCode[$r]['dbtodo'] = 'purchase_orders';
        $restCode[$r]['actodo'] = 'ut';
        $restCode[$r]['con']    = "sys_code = '".$purchase['PurchaseOrder']['sys_code']."'";
        $r++;
        $this->GeneralLedger->updateAll(
                array('GeneralLedger.is_active' => "2", 'GeneralLedger.modified_by' => $user['User']['id']), array('GeneralLedger.purchase_order_id' => $id)
        );
        // Convert to REST
        $restCode[$r]['is_active']   = 2;
        $restCode[$r]['modified']    = $dateNow;
        $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
        $restCode[$r]['dbtodo'] = 'general_ledgers';
        $restCode[$r]['actodo'] = 'ut';
        $restCode[$r]['con']    = "purchase_order_id = (SELECT id FROM purchase_orders WHERE sys_code = '".$purchase['PurchaseOrder']['sys_code']."' LIMIT 1)";
        $r++;
        $this->InventoryValuation->updateAll(
                array('InventoryValuation.is_active' => "2"), array('InventoryValuation.purchase_order_id' => $id)
        );
        // Convert to REST
        $restCode[$r]['is_active']   = 2;
        $restCode[$r]['dbtodo'] = 'inventory_valuations';
        $restCode[$r]['actodo'] = 'ut';
        $restCode[$r]['con']    = "purchase_order_id = (SELECT id FROM purchase_orders WHERE sys_code = '".$purchase['PurchaseOrder']['sys_code']."' LIMIT 1)";
        $r++;
        // Reset Stock
        foreach($purchaseOrderDetails AS $purchaseOrderDetail){
            // Inventory
            $qtyOrderSmall = ($purchaseOrderDetail['PurchaseOrderDetail']['qty'] + $purchaseOrderDetail['PurchaseOrderDetail']['qty_free']) * $purchaseOrderDetail['PurchaseOrderDetail']['conversion'];
            // Update Inventory (Purchase)
            $data = array();
            $data['module_type']       = 18;
            $data['purchase_order_id'] = $id;
            $data['product_id']        = $purchaseOrderDetail['PurchaseOrderDetail']['product_id'];
            $data['location_id']       = $purchase['PurchaseOrder']['location_id'];
            $data['location_group_id'] = $purchase['PurchaseOrder']['location_group_id'];
            $data['lots_number']  = $purchaseOrderDetail['PurchaseOrderDetail']['lots_number'];
            $data['expired_date'] = $purchaseOrderDetail['PurchaseOrderDetail']['date_expired'];
            $data['date']         = $purchase['PurchaseOrder']['order_date'];
            $data['total_qty']    = $qtyOrderSmall;
            $data['total_order']  = $qtyOrderSmall;
            $data['total_free']   = 0;
            $data['user_id']      = $user['User']['id'];
            $data['customer_id']  = "";
            $data['vendor_id']    = $purchase['PurchaseOrder']['vendor_id'];
            $data['unit_cost']    = $purchaseOrderDetail['PurchaseOrderDetail']['unit_cost'];
            $data['unit_price']   = 0;
            // Update Invetory Location
            $this->Inventory->saveInventory($data);
            // Update Inventory Group
            $this->Inventory->saveGroupTotalDetail($data);
            // Convert to REST
            $restCode[$r] = $this->Helper->convertToDataSync($data, 'inventories');
            $restCode[$r]['module_type']  = 18;
            $restCode[$r]['total_qty']    = $qtyOrderSmall;
            $restCode[$r]['total_order']  = $qtyOrderSmall;
            $restCode[$r]['total_free']   = 0;
            $restCode[$r]['expired_date'] = $data['expired_date'];
            $restCode[$r]['unit_cost']    = $purchaseOrderDetail['PurchaseOrderDetail']['unit_cost'];
            $restCode[$r]['unit_price']   = 0;
            $restCode[$r]['customer_id']  = "";
            $restCode[$r]['vendor_id']    = $this->Helper->getSQLSyncCode("vendors", $purchase['PurchaseOrder']['vendor_id']);
            $restCode[$r]['purchase_order_id'] = $this->Helper->getSQLSyncCode("purchase_orders", $id);
            $restCode[$r]['product_id']        = $this->Helper->getSQLSyncCode("products", $purchaseOrderDetail['PurchaseOrderDetail']['product_id']);
            $restCode[$r]['location_id']       = $this->Helper->getSQLSyncCode("locations", $purchase['PurchaseOrder']['location_id']);
            $restCode[$r]['location_group_id'] = $this->Helper->getSQLSyncCode("location_groups", $purchase['PurchaseOrder']['location_group_id']);
            $restCode[$r]['user_id']           = $this->Helper->getSQLSyncCode("users", $user['User']['id']);
            $restCode[$r]['dbtype']  = 'saveInv,GroupDetail';
            $restCode[$r]['actodo']  = 'inv';
            $r++;
        }
        // Save File Send
        $this->Helper->sendFileToSync($restCode, 0, 0);
        // Recalculate Average Cost
        $dateReca = date("Y-m-d", strtotime(date("Y-m-d", strtotime($purchase['PurchaseOrder']['order_date'])) . " -1 day"));
        mysql_query("UPDATE tracks SET val='".$dateReca."', is_recalculate = 1 WHERE id=1");
        $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Bill', 'Delete', $id);
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }

    function edit($id=null) {
        $this->layout = 'ajax';
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $purchase          = $this->PurchaseOrder->read(null, $id);
            $queryHasReceipt   = mysql_query("SELECT id FROM pvs WHERE purchase_order_id=" . $id . " AND is_void = 0");
            $queryHasReturn    = mysql_query("SELECT id FROM purchase_returns WHERE status>0 AND purchase_order_id=" . $id);
            $queryHasReturnNew = mysql_query("SELECT id FROM invoice_pbc_with_pbs WHERE status > 0 AND purchase_order_id=" . $id);
            if ($purchase['PurchaseOrder']['status'] == 3 && !@mysql_num_rows($queryHasReceipt) && !@mysql_num_rows($queryHasReturn) && !@mysql_num_rows($queryHasReturnNew)) {
                if ($this->data['PurchaseOrder']['total_amount'] != "") {
                    $r  = 0;
                    $rb = 0;
                    $restCode = array();
                    $restBackCode  = array();
                    $dateNow  = date("Y-m-d H:i:s");
                    $result = array();
                    $totalAmount = $this->data['PurchaseOrder']['total_amount'] - $this->data['PurchaseOrder']['discount_amount'] + $this->data['PurchaseOrder']['total_vat'];
                    $totalDept   = 0;
                    $statuEdit = "-1";
                    if($this->data['PurchaseOrder']['company_id'] != $purchase['PurchaseOrder']['company_id']){
                        $statuEdit = 0;
                        $totalDept = $this->data['total_deposit'];
                    }
                    
                    // Load Model
                    $this->loadModel('PurchaseOrderDetail');
                    $this->loadModel('PurchaseOrderMisc');
                    $this->loadModel('PurchaseOrderService');
                    $this->loadModel('GeneralLedger');
                    $this->loadModel('GeneralLedgerDetail');
                    $this->loadModel('InventoryValuation');
                    $this->loadModel('Company');
                    $this->loadModel('AccountType');

                    // Chart Account
                    $salesMiscAccount = $this->AccountType->findById(10);
                    $purchaseDiscAccount = $this->AccountType->findById(15);
                    $apAccount = ClassRegistry::init('AccountType')->findById(14);
                    
                    $this->PurchaseOrder->updateAll(
                            array('PurchaseOrder.status' => $statuEdit, 'PurchaseOrder.modified_by' => $user['User']['id']), array('PurchaseOrder.id' => $id)
                    );
                    // Convert to REST
                    $restBackCode[$rb]['status']   = $statuEdit;
                    $restBackCode[$rb]['modified'] = $dateNow;
                    $restBackCode[$rb]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
                    $restBackCode[$rb]['dbtodo'] = 'purchase_orders';
                    $restBackCode[$rb]['actodo'] = 'ut';
                    $restBackCode[$rb]['con']    = "sys_code = '".$purchase['PurchaseOrder']['sys_code']."'";
                    $rb++;
                    $this->InventoryValuation->updateAll(
                            array('InventoryValuation.is_active' => "2"), array('InventoryValuation.purchase_order_id' => $id)
                    );
                    // Convert to REST
                    $restBackCode[$rb]['is_active'] = 2;
                    $restBackCode[$rb]['dbtodo'] = 'inventory_valuations';
                    $restBackCode[$rb]['actodo'] = 'ut';
                    $restBackCode[$rb]['con']    = "purchase_order_id = (SELECT id FROM purchase_orders WHERE sys_code = '".$purchase['PurchaseOrder']['sys_code']."' LIMIT 1)";
                    $rb++;
                    $this->GeneralLedger->updateAll(
                            array('GeneralLedger.is_active' => "2", 'GeneralLedger.modified_by' => $user['User']['id']), array('GeneralLedger.purchase_order_id' => $id)
                    );
                    // Convert to REST
                    $restBackCode[$rb]['is_active'] = 2;
                    $restBackCode[$rb]['modified']  = $dateNow;
                    $restBackCode[$rb]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
                    $restBackCode[$rb]['dbtodo'] = 'general_ledgers';
                    $restBackCode[$rb]['actodo'] = 'ut';
                    $restBackCode[$rb]['con']    = "purchase_order_id = (SELECT id FROM purchase_orders WHERE sys_code = '".$purchase['PurchaseOrder']['sys_code']."' LIMIT 1)";
                    $rb++;
                    // Reset Stock
                    $purchaseOrderDetails = ClassRegistry::init('PurchaseOrderDetail')->find("all", array('conditions' => array('PurchaseOrderDetail.purchase_order_id' => $id)));
                    foreach($purchaseOrderDetails AS $purchaseOrderDetail){
                        // Inventory
                        $qtyOrderSmall = ($purchaseOrderDetail['PurchaseOrderDetail']['qty'] + $purchaseOrderDetail['PurchaseOrderDetail']['qty_free']) * $purchaseOrderDetail['PurchaseOrderDetail']['conversion'];
                        // Update Inventory (Purchase)
                        $data = array();
                        $data['module_type']       = 18;
                        $data['purchase_order_id'] = $id;
                        $data['product_id']        = $purchaseOrderDetail['PurchaseOrderDetail']['product_id'];
                        $data['location_id']       = $purchase['PurchaseOrder']['location_id'];
                        $data['location_group_id'] = $purchase['PurchaseOrder']['location_group_id'];
                        $data['lots_number']  = $purchaseOrderDetail['PurchaseOrderDetail']['lots_number'];
                        $data['expired_date'] = $purchaseOrderDetail['PurchaseOrderDetail']['date_expired'];
                        $data['date']         = $purchase['PurchaseOrder']['order_date'];
                        $data['total_qty']    = $qtyOrderSmall;
                        $data['total_order']  = $qtyOrderSmall;
                        $data['total_free']   = 0;
                        $data['user_id']      = $user['User']['id'];
                        $data['customer_id']  = "";
                        $data['vendor_id']    = $purchase['PurchaseOrder']['vendor_id'];
                        $data['unit_cost']    = $purchaseOrderDetail['PurchaseOrderDetail']['unit_cost'];
                        $data['unit_price']   = 0;
                        // Update Invetory Location
                        $this->Inventory->saveInventory($data);
                        // Update Inventory Group
                        $this->Inventory->saveGroupTotalDetail($data);
                        // Convert to REST
                        $restBackCode[$rb] = $this->Helper->convertToDataSync($data, 'inventories');
                        $restBackCode[$rb]['module_type']  = 18;
                        $restBackCode[$rb]['total_qty']    = $qtyOrderSmall;
                        $restBackCode[$rb]['total_order']  = $qtyOrderSmall;
                        $restBackCode[$rb]['total_free']   = 0;
                        $restBackCode[$rb]['expired_date'] = $data['expired_date'];
                        $restBackCode[$rb]['unit_cost']    = $purchaseOrderDetail['PurchaseOrderDetail']['unit_cost'];
                        $restBackCode[$rb]['unit_price']   = 0;
                        $restBackCode[$rb]['customer_id']  = "";
                        $restBackCode[$rb]['vendor_id']    = $this->Helper->getSQLSyncCode("vendors", $purchase['PurchaseOrder']['vendor_id']);
                        $restBackCode[$rb]['purchase_order_id'] = $this->Helper->getSQLSyncCode("purchase_orders", $id);
                        $restBackCode[$rb]['product_id']        = $this->Helper->getSQLSyncCode("products", $purchaseOrderDetail['PurchaseOrderDetail']['product_id']);
                        $restBackCode[$rb]['location_id']       = $this->Helper->getSQLSyncCode("locations", $purchase['PurchaseOrder']['location_id']);
                        $restBackCode[$rb]['location_group_id'] = $this->Helper->getSQLSyncCode("location_groups", $purchase['PurchaseOrder']['location_group_id']);
                        $restBackCode[$rb]['user_id']           = $this->Helper->getSQLSyncCode("users", $user['User']['id']);
                        $restBackCode[$rb]['dbtype']  = 'saveInv,GroupDetail';
                        $restBackCode[$rb]['actodo']  = 'inv';
                        $rb++;
                    }
                    // Save File Send Delete
                    $this->Helper->sendFileToSync($restBackCode, 0, 0);
                    
                    $pbCode = $this->data['PurchaseOrder']['po_code'];
                    $this->PurchaseOrder->create();
                    $this->data['PurchaseOrder']['sys_code']     = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                    $this->data['PurchaseOrder']['invoice_date'] = ((empty($this->data['PurchaseOrder']['invoice_date']))?'0000-00-00':$this->data['PurchaseOrder']['invoice_date']);
                    $this->data['PurchaseOrder']['po_code']    = $purchase['PurchaseOrder']['po_code'];
                    $this->data['PurchaseOrder']['ap_id']      = $apAccount['AccountType']['chart_account_id'];
                    $this->data['PurchaseOrder']['created']    = $dateNow;
                    $this->data['PurchaseOrder']['created_by'] = $user['User']['id'];
                    $this->data['PurchaseOrder']['status']     = 3;
                    $this->data['PurchaseOrder']['total_deposit'] = $totalDept;
                    $this->data['PurchaseOrder']['balance']       = $totalAmount - $totalDept;
                    $this->data['PurchaseOrder']['is_deposit_reference'] = 0;
                    $totalDisByItem = 0;
                    if($this->data['PurchaseOrder']['discount_amount'] > 0){
                        if($this->data['PurchaseOrder']['discount_percent'] > 0){
                            $totalDisByItem = $this->data['PurchaseOrder']['discount_percent'];
                        } else {
                            $totalDisByItem = $this->Helper->replaceThousand(number_format($this->data['PurchaseOrder']['discount_amount'] / $this->data['PurchaseOrder']['total_amount'], 9));
                        }
                    }
                    if ($this->PurchaseOrder->save($this->data)) {
                        $result['po_id'] = $purchaseOrderId = $this->PurchaseOrder->id;
                        $company         = $this->Company->read(null, $this->data['PurchaseOrder']['company_id']);
                        $classId         = $this->Helper->getClassId($company['Company']['id'], $company['Company']['classes'], $this->data['PurchaseOrder']['location_group_id']);
                        $pbModCode       = $purchase['PurchaseOrder']['po_code'];
                        if($this->data['PurchaseOrder']['company_id'] != $purchase['PurchaseOrder']['company_id']){
                            // Get Module Code
//                            $modCode = $this->Helper->getModuleCode($pbCode, $purchaseOrderId, 'po_code', 'purchase_orders', 'status != -1 AND branch_id = '.$this->data['PurchaseOrder']['branch_id']);
                            // Updaet Module Code
//                            mysql_query("UPDATE purchase_orders SET po_code = '".$modCode."' WHERE id = ".$purchaseOrderId);
//                            $pbModCode = $modCode;
                        }
                        // Check Update Deposit PO History
                        if($purchase['PurchaseOrder']['is_deposit_reference'] == 1){
                            // Update PO Applied is closed
                            mysql_query("UPDATE purchase_requests SET is_close = 0 WHERE id = ".$purchase['PurchaseOrder']['purchase_request_id']);
                        }
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($this->data['PurchaseOrder'], 'purchase_orders');
//                        $restCode[$r]['po_code']  = $pbModCode;
                        $restCode[$r]['modified'] = $dateNow;
                        $restCode[$r]['dbtodo']   = 'purchase_orders';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                        // ACCOUNT Statement GL
                        $this->GeneralLedger->create();
                        $this->data['GeneralLedger']['sys_code']       = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                        $this->data['GeneralLedger']['purchase_order_id'] = $purchaseOrderId;
                        $this->data['GeneralLedger']['date']       = $this->data['PurchaseOrder']['order_date'];
                        $this->data['GeneralLedger']['reference']  = $pbModCode;
                        $this->data['GeneralLedger']['created_by'] = $user['User']['id'];
                        $this->data['GeneralLedger']['is_sys'] = 1;
                        $this->GeneralLedger->save($this->data);
                        $gleaderId = $this->GeneralLedger->getLastInsertId();
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($this->data['GeneralLedger'], 'general_ledgers');
                        $restCode[$r]['modified'] = $dateNow;
                        $restCode[$r]['dbtodo']   = 'general_ledgers';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;

                        /**
                         * Purchase Order Detail A/P
                         */
                        $this->GeneralLedgerDetail->create();
                        $generalLedgerDetail = array();
                        $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $gleaderId;
                        $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id']  = $apAccount['AccountType']['chart_account_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['type']        = "Bill";
                        $generalLedgerDetail['GeneralLedgerDetail']['vendor_id']   = $this->data['PurchaseOrder']['vendor_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['company_id']  = $this->data['PurchaseOrder']['company_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['branch_id']   = $this->data['PurchaseOrder']['branch_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['location_group_id'] = $this->data['PurchaseOrder']['location_group_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['location_id'] = $this->data['PurchaseOrder']['location_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['memo']     = "ICS: PB # " . $pbModCode;
                        $generalLedgerDetail['GeneralLedgerDetail']['debit']    = 0;
                        $generalLedgerDetail['GeneralLedgerDetail']['credit']   = $totalAmount;
                        $generalLedgerDetail['GeneralLedgerDetail']['class_id'] = $classId;
                        $this->GeneralLedgerDetail->save($generalLedgerDetail);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                        $restCode[$r]['dbtodo']   = 'general_ledger_details';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                        
                        /* Purchase Order Total Discount */
                        if ($this->data['PurchaseOrder']['discount_amount'] > 0) {
                            $this->GeneralLedgerDetail->create();
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id']  = $purchaseDiscAccount['AccountType']['chart_account_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $this->data['PurchaseOrder']['discount_amount'];
                            $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: PB # ' . $pbModCode . ' Total Discount';
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                        }

                        /**
                         * Purchase Order Total VAT
                         */
                        if (($this->data['PurchaseOrder']['total_vat']) > 0) {
                            $this->GeneralLedgerDetail->create();
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id']  = $this->data['PurchaseOrder']['vat_chart_account_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $this->data['PurchaseOrder']['total_vat'];
                            $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: PB # ' . $pbModCode.' Total VAT';
                            $this->GeneralLedgerDetail->save($generalLedgerDetail); 
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                        }
                        
                        $this->loadModel('PurchaseReceiveResult');
                        $this->PurchaseReceiveResult->create();
                        $purchaseRecResult = array();
                        $purchaseRecResult['PurchaseReceiveResult']['sys_code'] = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                        $purchaseRecResult['PurchaseReceiveResult']['purchase_order_id'] = $purchaseOrderId;
                        $purchaseRecResult['PurchaseReceiveResult']['date']       = $this->data['PurchaseOrder']['order_date'];
                        $purchaseRecResult['PurchaseReceiveResult']['created']    = $dateNow;
                        $purchaseRecResult['PurchaseReceiveResult']['created_by'] = $user['User']['id'];
                        $this->PurchaseReceiveResult->save($purchaseRecResult);
                        $purchaseRecResultId = $this->PurchaseReceiveResult->id;
                        // Update Code Receive Result Code
                        $sqlRecCode = mysql_query("SELECT CONCAT('".date("y")."GRR','',LPAD(((SELECT count(tmp.id) FROM `purchase_receive_results` as tmp WHERE tmp.code LIKE '".date("y")."GRR%' AND tmp.id < ".$purchaseRecResultId.") + 1),7,'0')) AS code");
                        $rowRecCode = mysql_fetch_array($sqlRecCode);
                        mysql_query("UPDATE purchase_receive_results SET code = '".$rowRecCode['code']."' WHERE id = ".$purchaseRecResultId);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($purchaseRecResult['PurchaseReceiveResult'], 'purchase_receive_results');
                        $restCode[$r]['code']   = $rowRecCode['code'];
                        $restCode[$r]['dbtodo'] = 'purchase_receive_results';
                        $restCode[$r]['actodo'] = 'is';
                        $r++;
                        
                        // Get Decimal
                        $sqlOption = mysql_query("SELECT product_cost_decimal FROM setting_options");
                        $rowOption = mysql_fetch_array($sqlOption);
                        
                        for ($i = 0; $i < sizeof($_POST['product_id']); $i++) {
                            if ($_POST['product_id'][$i] != '' && $_POST['qty_uom_id'][$i] != '' && $_POST['qty'][$i] != '' && $_POST['qty'][$i] != null && $_POST['qty_free'][$i] != '' && $_POST['qty_free'][$i] != null && ($_POST['qty'][$i] + $_POST['qty_free'][$i]) > 0) {
                                // Account General Legder Detail
                                $qtyOrderSmall = ($_POST['qty'][$i] + $_POST['qty_free'][$i]) * $_POST['pb_conversion'][$i];
                                $qtyOrder      = ($_POST['qty'][$i] + $_POST['qty_free'][$i]) / ($_POST['small_uom_val_pb'][$i] / $_POST['pb_conversion'][$i]);
                                if($_POST['total_cost'][$i] > 0){
                                    $costDisByTotal = 0;
                                    if($totalDisByItem > 0){
                                        $costDisByTotal = $this->Helper->replaceThousand(number_format(($_POST['total_cost'][$i] * $totalDisByItem) / 100, 9));
                                    }
                                    $purchaseCost = ($_POST['total_cost'][$i] - $costDisByTotal);
                                    $costOrder    = ($purchaseCost / ($_POST['qty'][$i] + $_POST['qty_free'][$i])) * ($_POST['small_uom_val_pb'][$i] / $_POST['pb_conversion'][$i]);
                                } else {
                                    $costOrder = 0;
                                }
                                $total_amount  = ($_POST['total_cost'][$i] + $_POST['discount'][$i]);
                                
                                // Save Product in pruchase order detail
                                $this->PurchaseOrderDetail->create();
                                $PurchaseOrderDetail = array();
                                $PurchaseOrderDetail['PurchaseOrderDetail']['sys_code']          = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                                $PurchaseOrderDetail['PurchaseOrderDetail']['purchase_order_id'] = $purchaseOrderId;
                                $PurchaseOrderDetail['PurchaseOrderDetail']['discount_id']       = $_POST['discount_id'][$i];
                                $PurchaseOrderDetail['PurchaseOrderDetail']['discount_amount']   = $_POST['discount'][$i];
                                $PurchaseOrderDetail['PurchaseOrderDetail']['discount_percent']  = $_POST['discount_percent'][$i];
                                $PurchaseOrderDetail['PurchaseOrderDetail']['product_id'] = $_POST['product_id'][$i];
                                $PurchaseOrderDetail['PurchaseOrderDetail']['max_order']  = $_POST['max_order'][$i];
                                $PurchaseOrderDetail['PurchaseOrderDetail']['qty']        = $_POST['qty'][$i];
                                $PurchaseOrderDetail['PurchaseOrderDetail']['qty_free']   = $_POST['qty_free'][$i];
                                $PurchaseOrderDetail['PurchaseOrderDetail']['qty_uom_id'] = $_POST['qty_uom_id'][$i];
                                $PurchaseOrderDetail['PurchaseOrderDetail']['default_cost'] = ($_POST['unit_cost'][$i] * ($_POST['small_uom_val_pb'][$i] / $_POST['pb_conversion'][$i]));
                                $PurchaseOrderDetail['PurchaseOrderDetail']['unit_cost']    = $_POST['unit_cost'][$i];
                                $PurchaseOrderDetail['PurchaseOrderDetail']['total_cost']   = $_POST['h_total_cost'][$i];
                                $PurchaseOrderDetail['PurchaseOrderDetail']['conversion']   = $_POST['pb_conversion'][$i];
                                $PurchaseOrderDetail['PurchaseOrderDetail']['lots_number']  = $_POST['lots_number'][$i]!=""?$_POST['lots_number'][$i]:0;
                                $PurchaseOrderDetail['PurchaseOrderDetail']['date_expired'] = ((empty($_POST['date_expired'][$i]))?'0000-00-00':$_POST['date_expired'][$i]);
                                $PurchaseOrderDetail['PurchaseOrderDetail']['note']         = $_POST['note'][$i];
                                $PurchaseOrderDetail['PurchaseOrderDetail']['new_unit_cost']  = $this->Helper->replaceThousand(number_format($costOrder, $rowOption[0]));
                                $this->PurchaseOrderDetail->save($PurchaseOrderDetail);
                                $PurchaseOrderDetailId = $this->PurchaseOrderDetail->id;
                                // Convert to REST
                                $restCode[$r] = $this->Helper->convertToDataSync($PurchaseOrderDetail['PurchaseOrderDetail'], 'purchase_order_details');
                                $restCode[$r]['new_unit_cost'] = $this->Helper->replaceThousand(number_format($costOrder, $rowOption[0]));
                                $restCode[$r]['dbtodo']   = 'purchase_order_details';
                                $restCode[$r]['actodo']   = 'is';
                                $r++;
                                $sqlPro  = mysql_query("SELECT small_val_uom, unit_cost, sys_code FROM products WHERE id = ".$_POST['product_id'][$i]);
                                $rowPro  = mysql_fetch_array($sqlPro);
                                // Update unit cost for product                             
                                mysql_query("UPDATE products SET unit_cost = '".$PurchaseOrderDetail['PurchaseOrderDetail']['new_unit_cost']."' WHERE id=".$_POST['product_id'][$i]);
                                // Convert to REST
                                $restCode[$r]['unit_cost'] = $PurchaseOrderDetail['PurchaseOrderDetail']['new_unit_cost'];
                                $restCode[$r]['dbtodo'] = 'products';
                                $restCode[$r]['actodo'] = 'ut';
                                $restCode[$r]['con']    = "sys_code = '".$rowPro['sys_code']."'";
                                $r++;
                                if ($rowPro['unit_cost'] != $PurchaseOrderDetail['PurchaseOrderDetail']['new_unit_cost']) {
                                    mysql_query("INSERT INTO `product_unit_cost_histories` (`product_id`, `purchase_order_id`, `old_cost`, `new_cost`, `type`, `created`, `created_by`) 
                                                 VALUES (".$_POST['product_id'][$i].", ".$purchaseOrderId.", ".$rowPro['unit_cost'].", ".$PurchaseOrderDetail['PurchaseOrderDetail']['new_unit_cost'].", 'PB', '".$dateNow."', ".$user['User']['id'].");");
                                    // Convert to REST
                                    $restCode[$r]['old_cost']   = $rowPro['unit_cost'];
                                    $restCode[$r]['new_cost']   = $PurchaseOrderDetail['PurchaseOrderDetail']['new_unit_cost'];
                                    $restCode[$r]['created']    = $dateNow;
                                    $restCode[$r]['created_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
                                    $restCode[$r]['product_id'] = $this->Helper->getSQLSysCode("products", $_POST['product_id'][$i]);
                                    $restCode[$r]['purchase_order_id'] = $this->Helper->getSQLSysCode("purchase_orders", $purchaseOrderId);
                                    $restCode[$r]['dbtodo'] = 'product_unit_cost_histories';
                                    $restCode[$r]['actodo'] = 'is';
                                    $r++;
                                }

                                // Inventory
                                // Update Inventory (Purchase)
                                $data = array();
                                $data['module_type']       = 6;
                                $data['purchase_order_id'] = $purchaseOrderId;
                                $data['product_id']        = $_POST['product_id'][$i];
                                $data['location_id']       = $this->data['PurchaseOrder']['location_id'];
                                $data['location_group_id'] = $this->data['PurchaseOrder']['location_group_id'];
                                $data['lots_number']  = $PurchaseOrderDetail['PurchaseOrderDetail']['lots_number'];
                                $data['expired_date'] = $PurchaseOrderDetail['PurchaseOrderDetail']['date_expired'];
                                $data['date']         = $this->data['PurchaseOrder']['order_date'];
                                $data['total_qty']    = $qtyOrderSmall;
                                $data['total_order']  = $qtyOrderSmall;
                                $data['total_free']   = 0;
                                $data['user_id']      = $user['User']['id'];
                                $data['customer_id']  = "";
                                $data['vendor_id']    = $this->data['PurchaseOrder']['vendor_id'];
                                $data['unit_cost']    = $_POST['unit_cost'][$i];
                                $data['unit_price']   = 0;
                                // Update Invetory Location
                                $this->Inventory->saveInventory($data);
                                // Update Inventory Group
                                $this->Inventory->saveGroupTotalDetail($data);
                                // Convert to REST
                                $restCode[$r] = $this->Helper->convertToDataSync($data, 'inventories');
                                $restCode[$r]['module_type']  = 6;
                                $restCode[$r]['total_qty']    = $qtyOrderSmall;
                                $restCode[$r]['total_order']  = $qtyOrderSmall;
                                $restCode[$r]['total_free']   = 0;
                                $restCode[$r]['expired_date'] = $data['expired_date'];
                                $restCode[$r]['unit_cost']    = $_POST['unit_cost'][$i];
                                $restCode[$r]['unit_price']   = 0;
                                $restCode[$r]['customer_id']  = "";
                                $restCode[$r]['vendor_id']    = $this->Helper->getSQLSyncCode("vendors", $this->data['PurchaseOrder']['vendor_id']);
                                $restCode[$r]['purchase_order_id'] = $this->Helper->getSQLSyncCode("purchase_orders", $purchaseOrderId);
                                $restCode[$r]['product_id']        = $this->Helper->getSQLSyncCode("products", $_POST['product_id'][$i]);
                                $restCode[$r]['location_id']       = $this->Helper->getSQLSyncCode("locations", $this->data['PurchaseOrder']['location_id']);
                                $restCode[$r]['location_group_id'] = $this->Helper->getSQLSyncCode("location_groups", $this->data['PurchaseOrder']['location_group_id']);
                                $restCode[$r]['user_id']           = $this->Helper->getSQLSyncCode("users", $user['User']['id']);
                                $restCode[$r]['dbtype']  = 'saveInv,GroupDetail';
                                $restCode[$r]['actodo']  = 'inv';
                                $r++;
                                // Purchase Receive
                                ClassRegistry::init('PurchaseReceive')->create();
                                $this->data['PurchaseReceive']['purchase_receive_result_id'] = $purchaseRecResultId;
                                $this->data['PurchaseReceive']['purchase_order_id']          = $purchaseOrderId;
                                $this->data['PurchaseReceive']['purchase_order_detail_id']   = $PurchaseOrderDetailId;
                                $this->data['PurchaseReceive']['product_id'] = $_POST['product_id'][$i];
                                $this->data['PurchaseReceive']['qty']        = ($_POST['qty'][$i] + $_POST['qty_free'][$i]);
                                $this->data['PurchaseReceive']['qty_uom_id'] = $_POST['qty_uom_id'][$i];
                                $this->data['PurchaseReceive']['conversion'] = $_POST['pb_conversion'][$i];
                                $this->data['PurchaseReceive']['received_date'] = $this->data['PurchaseOrder']['order_date'];
                                $this->data['PurchaseReceive']['lots_number']   = $data['lots_number'];
                                $this->data['PurchaseReceive']['date_expired']  = $data['expired_date'];
                                $this->data['PurchaseReceive']['created']    = $dateNow;
                                $this->data['PurchaseReceive']['created_by'] = $user['User']['id'];
                                $this->data['PurchaseReceive']['status']     = 1;
                                ClassRegistry::init('PurchaseReceive')->save($this->data);
                                // Convert to REST
                                $restCode[$r] = $this->Helper->convertToDataSync($this->data['PurchaseReceive'], 'purchase_receives');
                                $restCode[$r]['dbtodo'] = 'purchase_receives';
                                $restCode[$r]['actodo'] = 'is';
                                $r++;

                                if($this->data['calculate_cogs'] == 1){
                                    // Inventory Valuation
                                    $inv_valutaion = array();
                                    $this->InventoryValuation->create();
                                    $inv_valutaion['InventoryValuation']['sys_code']  = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                                    $inv_valutaion['InventoryValuation']['purchase_order_id'] = $purchaseOrderId;
                                    $inv_valutaion['InventoryValuation']['purchase_order_detail_id'] = $PurchaseOrderDetailId;
                                    $inv_valutaion['InventoryValuation']['company_id'] = $this->data['PurchaseOrder']['company_id'];
                                    $inv_valutaion['InventoryValuation']['branch_id']  = $this->data['PurchaseOrder']['branch_id'];
                                    $inv_valutaion['InventoryValuation']['type']       = "Bill";
                                    $inv_valutaion['InventoryValuation']['reference']  = $pbModCode;
                                    $inv_valutaion['InventoryValuation']['vendor_id']  = $this->data['PurchaseOrder']['vendor_id'];
                                    $inv_valutaion['InventoryValuation']['date']       = $this->data['PurchaseOrder']['order_date'];
                                    $inv_valutaion['InventoryValuation']['created']    = $this->data['PurchaseOrder']['order_date']." 00:00:00";
                                    $inv_valutaion['InventoryValuation']['pid']        = $_POST['product_id'][$i];
                                    $inv_valutaion['InventoryValuation']['small_qty']  = $qtyOrderSmall;
                                    $inv_valutaion['InventoryValuation']['qty']  = $this->Helper->replaceThousand(number_format($qtyOrder, 6));
                                    $inv_valutaion['InventoryValuation']['cost'] = $this->Helper->replaceThousand(number_format($costOrder, $rowOption[0]));
                                    $this->InventoryValuation->saveAll($inv_valutaion);
                                    // Convert to REST
                                    $restCode[$r] = $this->Helper->convertToDataSync($inv_valutaion['InventoryValuation'], 'inventory_valuations');
                                    $restCode[$r]['dbtodo']   = 'inventory_valuations';
                                    $restCode[$r]['actodo']   = 'is';
                                    $r++;
                                }
                                // General Ledger Detail (Product)
                                $this->GeneralLedgerDetail->create();
                                $queryInvAccount = mysql_query("SELECT IFNULL((IFNULL((SELECT chart_account_id FROM accounts WHERE product_id = " . $_POST['product_id'][$i] . " AND account_type_id=1),(SELECT chart_account_id FROM pgroup_accounts WHERE pgroup_id = (SELECT pgroup_id FROM product_pgroups WHERE product_id = " . $_POST['product_id'][$i] . " ORDER BY id  DESC LIMIT 1) AND account_type_id=1))),(SELECT chart_account_id FROM account_types WHERE id=1))");
                                $dataInvAccount  = mysql_fetch_array($queryInvAccount);
                                $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $dataInvAccount[0];
                                $generalLedgerDetail['GeneralLedgerDetail']['product_id']  = $_POST['product_id'][$i];
                                $generalLedgerDetail['GeneralLedgerDetail']['service_id']  = NULL;
                                $generalLedgerDetail['GeneralLedgerDetail']['memo']   = "ICS: PB # " . $pbModCode . " Product # " . $_POST['product_name'][$i];
                                $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $total_amount;
                                $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                                $this->GeneralLedgerDetail->saveAll($generalLedgerDetail);
                                // Convert to REST
                                $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                                $restCode[$r]['dbtodo']   = 'general_ledger_details';
                                $restCode[$r]['actodo']   = 'is';
                                $r++;

                                // General Ledger Detail (Product Discount)
                                if (abs($_POST['discount'][$i]) > 0) {
                                    $this->GeneralLedgerDetail->create();
                                    $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $purchaseDiscAccount['AccountType']['chart_account_id'];
                                    $generalLedgerDetail['GeneralLedgerDetail']['memo']   = "ICS: PB # " . $pbModCode . " Product # " . $_POST['product_name'][$i] . ' Discount';
                                    $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                                    $generalLedgerDetail['GeneralLedgerDetail']['credit'] = abs($_POST['discount'][$i]);
                                    $this->GeneralLedgerDetail->saveAll($generalLedgerDetail);
                                    // Convert to REST
                                    $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                                    $restCode[$r]['dbtodo']   = 'general_ledger_details';
                                    $restCode[$r]['actodo']   = 'is';
                                    $r++;
                                }
                                // End Account General Legder Detail         

                            } else if (!empty($_POST['service_id'][$i])) {
                                // Purchase Order Service
                                $this->PurchaseOrderService->create();
                                $PurchaseOrderService = array();
                                $PurchaseOrderService['PurchaseOrderService']['purchase_order_id'] = $purchaseOrderId;
                                $PurchaseOrderService['PurchaseOrderService']['discount_id']      = $_POST['discount_id'][$i];
                                $PurchaseOrderService['PurchaseOrderService']['discount_amount']  = $_POST['discount'][$i];
                                $PurchaseOrderService['PurchaseOrderService']['discount_percent'] = $_POST['discount_percent'][$i];
                                $PurchaseOrderService['PurchaseOrderService']['service_id'] = $_POST['service_id'][$i];
                                $PurchaseOrderService['PurchaseOrderService']['qty']        = $_POST['qty'][$i];
                                $PurchaseOrderService['PurchaseOrderService']['qty_free']   = $_POST['qty_free'][$i];
                                $PurchaseOrderService['PurchaseOrderService']['unit_cost']  = $_POST['unit_cost'][$i];
                                $PurchaseOrderService['PurchaseOrderService']['total_cost'] = $_POST['h_total_cost'][$i];
                                $PurchaseOrderService['PurchaseOrderService']['note']       = $_POST['note'][$i];
                                $this->PurchaseOrderService->save($PurchaseOrderService);
                                // Convert to REST
                                $restCode[$r] = $this->Helper->convertToDataSync($PurchaseOrderService['PurchaseOrderService'], 'purchase_order_services');
                                $restCode[$r]['dbtodo']   = 'purchase_order_services';
                                $restCode[$r]['actodo']   = 'is';
                                $r++;
                                
                                // General Ledger Detail (Service)
                                $this->GeneralLedgerDetail->create();
                                $queryServiceAccount = mysql_query("SELECT chart_account_id FROM account_types WHERE id=9");
                                $dataServiceAccount  = mysql_fetch_array($queryServiceAccount);
                                $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $dataServiceAccount[0];
                                $generalLedgerDetail['GeneralLedgerDetail']['service_id']  = $_POST['service_id'][$i];
                                $generalLedgerDetail['GeneralLedgerDetail']['product_id']  = NULL;
                                $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $_POST['h_total_cost'][$i];
                                $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                                $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: PB # ' . $pbModCode . ' Service # ' . $_POST['product_name'][$i];
                                $this->GeneralLedgerDetail->save($generalLedgerDetail);
                                // Convert to REST
                                $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                                $restCode[$r]['dbtodo']   = 'general_ledger_details';
                                $restCode[$r]['actodo']   = 'is';
                                $r++;

                                // General Ledger Detail (Service Discount)
                                if (abs($_POST['discount'][$i]) > 0) {
                                    $this->GeneralLedgerDetail->create();
                                    $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $purchaseDiscAccount['AccountType']['chart_account_id'];
                                    $generalLedgerDetail['GeneralLedgerDetail']['memo']   = "ICS: PB # " . $pbModCode . " Service # " . $_POST['product_name'][$i] . ' Discount';
                                    $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                                    $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $_POST['discount'][$i];
                                    $this->GeneralLedgerDetail->saveAll($generalLedgerDetail);
                                    // Convert to REST
                                    $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                                    $restCode[$r]['dbtodo']   = 'general_ledger_details';
                                    $restCode[$r]['actodo']   = 'is';
                                    $r++;
                                }
                            } else{
                                // Purchase Order Miscellaneous
                                $this->PurchaseOrderMisc->create();
                                $PurchaseOrderMiscs = array();
                                $PurchaseOrderMiscs['PurchaseOrderMisc']['purchase_order_id'] = $purchaseOrderId;
                                $PurchaseOrderMiscs['PurchaseOrderMisc']['discount_id']      = $_POST['discount_id'][$i];
                                $PurchaseOrderMiscs['PurchaseOrderMisc']['discount_amount']  = $_POST['discount'][$i];
                                $PurchaseOrderMiscs['PurchaseOrderMisc']['discount_percent'] = $_POST['discount_percent'][$i];
                                $PurchaseOrderMiscs['PurchaseOrderMisc']['description'] = $_POST['product_name'][$i];
                                $PurchaseOrderMiscs['PurchaseOrderMisc']['qty_uom_id']  = $_POST['qty_uom_id'][$i];
                                $PurchaseOrderMiscs['PurchaseOrderMisc']['qty']         = $_POST['qty'][$i];
                                $PurchaseOrderMiscs['PurchaseOrderMisc']['qty_free']    = $_POST['qty_free'][$i];
                                $PurchaseOrderMiscs['PurchaseOrderMisc']['unit_cost']   = $_POST['unit_cost'][$i];
                                $PurchaseOrderMiscs['PurchaseOrderMisc']['total_cost']  = $_POST['h_total_cost'][$i];
                                $PurchaseOrderMiscs['PurchaseOrderMisc']['note'] = $_POST['note'][$i];
                                $this->PurchaseOrderMisc->save($PurchaseOrderMiscs);
                                // Convert to REST
                                $restCode[$r] = $this->Helper->convertToDataSync($PurchaseOrderMiscs['PurchaseOrderMisc'], 'purchase_order_miscs');
                                $restCode[$r]['dbtodo']   = 'purchase_order_miscs';
                                $restCode[$r]['actodo']   = 'is';
                                $r++;

                                // General Ledger Detail (Misc)
                                $this->GeneralLedgerDetail->create();
                                $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id']  = $salesMiscAccount['AccountType']['chart_account_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['service_id']  = NULL;
                                $generalLedgerDetail['GeneralLedgerDetail']['product_id']  = NULL;
                                $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $_POST['h_total_cost'][$i];
                                $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                                $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: PB # ' . $pbModCode . ' Misc # ' . $_POST['product_name'][$i];
                                $this->GeneralLedgerDetail->save($generalLedgerDetail);
                                // Convert to REST
                                $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                                $restCode[$r]['dbtodo']   = 'general_ledger_details';
                                $restCode[$r]['actodo']   = 'is';
                                $r++;

                                // General Ledger Detail (Misc Discount)
                                if (abs($_POST['discount'][$i]) > 0) {
                                    $this->GeneralLedgerDetail->create();
                                    $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $purchaseDiscAccount['AccountType']['chart_account_id'];
                                    $generalLedgerDetail['GeneralLedgerDetail']['memo']   = "ICS: PB # " . $pbModCode . " Misc # " . $_POST['product_name'][$i] . ' Discount';
                                    $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                                    $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $_POST['discount'][$i];
                                    $this->GeneralLedgerDetail->saveAll($generalLedgerDetail);
                                    // Convert to REST
                                    $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                                    $restCode[$r]['dbtodo']   = 'general_ledger_details';
                                    $restCode[$r]['actodo']   = 'is';
                                    $r++;
                                }
                            }
                        }
                        // Save File Send
                        $this->Helper->sendFileToSync($restCode, 0, 0);
                        // Recalculate Average Cost
                        $dateReca = date("Y-m-d", strtotime(date("Y-m-d", strtotime($this->data['PurchaseOrder']['order_date'])) . " -1 day"));
                        mysql_query("UPDATE tracks SET val='".$dateReca."', is_recalculate = 1 WHERE id=1");
                        // Return Purchase Bill Id
                        $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Bill', 'Save Edit', $id, $purchaseOrderId);
                        $result['code']  = 3;
                        $result['po_id'] = $purchaseOrderId;
                        echo json_encode($result);
                        exit;
                    } else {         
                        $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Bill', 'Save Edit (Error)', $id);
                        $result['code'] = 2;
                        echo json_encode($result);
                        exit;
                    }

                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Bill', 'Save Edit (Error Status)', $id);
                    $result['code'] = 2;
                    echo json_encode($result);
                    exit;
                }
            } else {
                $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Bill', 'Save Edit (Error has transaction with other modules)', $id);
                $result['code'] = 2;
                echo json_encode($result);
                exit;
            }
        }
        if (!empty($id)) {
            $this->data = $this->PurchaseOrder->read(null, $id);
            if ($this->data['PurchaseOrder']['status'] == 3) {
                $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Bill', 'Edit', $id);
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
                                    'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id', 'ModuleCodeBranch.pb_code', 'Branch.currency_center_id', 'CurrencyCenter.symbol'),
                                    'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])
                                ));
                // Get Loction Setting
                $locSetting = ClassRegistry::init('LocationSetting')->findById(1);
                $locCon     = '';
                if($locSetting['LocationSetting']['location_status'] == 1){
                    $locCon = ' AND Location.is_for_sale = 0';
                }
                $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))),'conditions' => array('user_location_groups.user_id=' . $user['User']['id'], 'LocationGroup.is_active' => '1', 'LocationGroup.location_group_type_id != 1')));
                $locations = ClassRegistry::init('Location')->find('all', array('joins' => array(array('table' => 'user_locations', 'type' => 'inner', 'conditions' => array('user_locations.location_id=Location.id'))), 'conditions' => array('user_locations.user_id=' . $user['User']['id'] . ' AND Location.is_active=1'.$locCon), 'order' => 'Location.name'));
                $uoms = ClassRegistry::init('Uom')->find('all', array('fields' => array('Uom.id', 'Uom.name'), 'conditions' => array('Uom.is_active' => 1)));
                $shipments = ClassRegistry::init('Shipment')->find('list', array('conditions' => array('Shipment.is_active' => 1)));
                $purchaseOrderDetails = ClassRegistry::init('PurchaseOrderDetail')->find("all", array('conditions' => array('PurchaseOrderDetail.purchase_order_id' => $id)));
                $purchaseOrderServices = ClassRegistry::init('PurchaseOrderService')->find("all", array('conditions' => array('PurchaseOrderService.purchase_order_id' => $id)));
                $purchaseOrderMiscs = ClassRegistry::init('PurchaseOrderMisc')->find("all", array('conditions' => array('PurchaseOrderMisc.purchase_order_id' => $id)));
                $apAccountId = $this->data['PurchaseOrder']['ap_id'];
                $this->set(compact('purchaseOrder', 'purchaseOrderDetails', 'purchaseOrderServices', 'purchaseOrderMiscs', 'uoms', 'locations', 'locationGroups', 'shipments', 'id', 'apAccountId', 'companies', 'branches'));
            } else {
                $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Bill', 'Edit (Error ID)', $id);
                echo MESSAGE_DATA_INVALID;
                exit;
            }
        }
    }

    function voidReceipt($id) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $r = 0;
        $restCode = array();
        $dateNow  = date("Y-m-d H:i:s");
        $user = $this->getCurrentUser();
        $this->loadModel('GeneralLedger');
        $this->loadModel('Pv');
        $receipt = ClassRegistry::init('Pv')->find("first",
                        array('conditions' => array('Pv.id' => $id)));
        if(!empty($receipt) && $receipt['Pv']['is_void'] == 0){
            $this->Pv->updateAll(
                    array('Pv.is_void' => 1, 'Pv.modified_by' => $user['User']['id']),
                    array('Pv.id' => $id)
            );
            // Convert to REST
            $restCode[$r]['is_void']     = 1;
            $restCode[$r]['modified']    = $dateNow;
            $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
            $restCode[$r]['dbtodo'] = 'pvs';
            $restCode[$r]['actodo'] = 'ut';
            $restCode[$r]['con']    = "sys_code = '".$receipt['Pv']['sys_code']."'";
            $r++;
            $exchangeRate = ClassRegistry::init('ExchangeRate')->find("first", array("conditions" => array("ExchangeRate.id" => $receipt['Pv']['exchange_rate_id'])));
            if(!empty($exchangeRate) && $exchangeRate['ExchangeRate']['rate_purchase'] > 0){
                $totalPaidOther = $receipt['Pv']['amount_other'] / $exchangeRate['ExchangeRate']['rate_purchase'];
                $totalDiscOther = $receipt['Pv']['discount_other'] / $exchangeRate['ExchangeRate']['rate_purchase'];
            } else {
                $totalPaidOther = 0;
                $totalDiscOther = 0;
            }
            $total_amount = $receipt['Pv']['amount_us'] + $totalPaidOther + $receipt['Pv']['discount'] + $totalDiscOther;

            mysql_query("UPDATE purchase_orders SET balance = balance+" . $total_amount . " WHERE id=" . $receipt['Pv']['purchase_order_id']);
            // Convert to REST
            $restCode[$r]['balance']     = '(balance+'.$total_amount.')';
            $restCode[$r]['modified']    = $dateNow;
            $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
            $restCode[$r]['dbtodo'] = 'purchase_orders';
            $restCode[$r]['actodo'] = 'ut';
            $restCode[$r]['con']    = "sys_code = '".$receipt['PurchaseOrder']['sys_code']."'";
            $r++;
            $this->GeneralLedger->updateAll(
                    array('GeneralLedger.is_active' => 2, 'GeneralLedger.modified_by' => $user['User']['id']),
                    array('GeneralLedger.pv_id' => $id)
            );
            // Convert to REST
            $restCode[$r]['is_active']   = 2;
            $restCode[$r]['modified']    = $dateNow;
            $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
            $restCode[$r]['dbtodo'] = 'general_ledgers';
            $restCode[$r]['actodo'] = 'ut';
            $restCode[$r]['con']    = "pv_id = (SELECT id FROM pvs WHERE sys_code = '".$receipt['Pv']['sys_code']."' LIMIT 1)";
            
            // Save File Send
            $this->Helper->sendFileToSync($restCode, 0, 0);
            $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Bill Receipt', 'Void', $id);
            echo MESSAGE_DATA_HAS_BEEN_DELETED;
            exit;
        }else{
            $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Bill Receipt', 'Void (Error)', $id);
            echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
            exit;
        }
    }

    function product($companyId = null, $branchId = null, $locationId = null) {
        $this->layout = "ajax";
        $this->set(compact('companyId', 'branchId', 'locationId'));
    }

    function productAjax($companyId = null, $branchId = null, $locationId = null, $category = null) {
        $this->layout = "ajax";
        $this->set(compact('companyId', 'branchId', 'locationId', 'category'));
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

    function aging($id = null) {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $r = 0;
            $restCode = array();
            $dateNow  = date("Y-m-d H:i:s");
            $this->loadModel('Pv');
            $cashBankAccount    = ClassRegistry::init('AccountType')->findById(13);
            $cashBankAccountId  = $cashBankAccount['AccountType']['chart_account_id'];
            $result = array();
            $purchaseOrder = $this->PurchaseOrder->find("first", array('conditions' => array('PurchaseOrder.id' => $this->data['PurchaseOrder']['id'])));
            if($purchaseOrder['PurchaseOrder']['balance'] >= 0 && $purchaseOrder['PurchaseOrder']['status'] > 0){
                $lastExchangeRate = ClassRegistry::init('ExchangeRate')->find("first", array("conditions" => array(
                                "ExchangeRate.branch_id" => $purchaseOrder['PurchaseOrder']['branch_id'],
                                "ExchangeRate.currency_center_id" => $this->data['PurchaseOrder']['currency_center_id']), "order" => array("ExchangeRate.created desc")));
                if(!empty($lastExchangeRate) && $lastExchangeRate['ExchangeRate']['rate_purchase'] > 0){
                    $exchangeRateId = $lastExchangeRate['ExchangeRate']['id'];
                    $totalPaidOther = ($this->data['PurchaseOrder']['amount_other'] / $lastExchangeRate['ExchangeRate']['rate_purchase']);
                } else {
                    $exchangeRateId = 0;
                    $totalPaidOther = 0;
                }
                $totalPaid = $this->data['PurchaseOrder']['amount_us'] + $totalPaidOther;
                if($totalPaid <= $purchaseOrder['PurchaseOrder']['balance']){
                    $purchase = array();
                    $purchase['PurchaseOrder']['id'] = $this->data['PurchaseOrder']['id'];
                    $purchase['PurchaseOrder']['modified']    = $dateNow;
                    $purchase['PurchaseOrder']['modified_by'] = $user['User']['id'];
                    $purchase['PurchaseOrder']['balance'] = $this->data['PurchaseOrder']['balance_us'];
                    if ($this->PurchaseOrder->save($purchase)) {
                        // Convert to REST
                        $restCode[$r]['balance']     = $this->data['PurchaseOrder']['balance_us'];
                        $restCode[$r]['modified']    = $dateNow;
                        $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
                        $restCode[$r]['dbtodo'] = 'purchase_orders';
                        $restCode[$r]['actodo'] = 'ut';
                        $restCode[$r]['con']    = "sys_code = '".$purchaseOrder['PurchaseOrder']['sys_code']."'";
                        $r++;
                        // Sales Order Receipt
                        $purchaseOrderReceipt = array();
                        $this->Pv->create();
                        $purchaseOrderReceipt['Pv']['sys_code']           = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                        $purchaseOrderReceipt['Pv']['purchase_order_id']  = $this->data['PurchaseOrder']['id'];
                        $purchaseOrderReceipt['Pv']['branch_id']          = $purchaseOrder['PurchaseOrder']['branch_id'];
                        $purchaseOrderReceipt['Pv']['exchange_rate_id']   = $exchangeRateId;
                        $purchaseOrderReceipt['Pv']['currency_center_id'] = $this->data['PurchaseOrder']['currency_center_id'];
                        $purchaseOrderReceipt['Pv']['chart_account_id']   = $cashBankAccountId;
                        $purchaseOrderReceipt['Pv']['pv_code']            = '';
                        $purchaseOrderReceipt['Pv']['amount_us']          = $this->data['PurchaseOrder']['amount_us'];
                        $purchaseOrderReceipt['Pv']['amount_other']       = $this->data['PurchaseOrder']['amount_other'];
                        $purchaseOrderReceipt['Pv']['total_amount']       = $this->data['PurchaseOrder']['total_amount'];
                        $purchaseOrderReceipt['Pv']['balance']            = $this->data['PurchaseOrder']['balance_us'];
                        $purchaseOrderReceipt['Pv']['balance_other']      = $this->data['PurchaseOrder']['balance_other'];
                        $purchaseOrderReceipt['Pv']['created_by']         = $user['User']['id'];
                        $purchaseOrderReceipt['Pv']['pay_date']           = $this->data['PurchaseOrder']['pay_date']!=''?$this->data['PurchaseOrder']['pay_date']:'0000-00-00';
                        if ($this->data['PurchaseOrder']['balance_us'] > 0) {
                            $purchaseOrderReceipt['Pv']['due_date'] = $this->data['PurchaseOrder']['aging']!=''?$this->data['PurchaseOrder']['aging']:'0000-00-00';
                        }
                        $this->Pv->save($purchaseOrderReceipt);
                        $result['sr_id'] = $this->Pv->id;
                        // Update Code & Change Receipt Generate Code
                        $modComCode = ClassRegistry::init('ModuleCodeBranch')->find('first', array('conditions' => array("ModuleCodeBranch.branch_id" => $purchaseOrder['PurchaseOrder']['branch_id'])));
                        $repCode    = date("y").$modComCode['ModuleCodeBranch']['pb_rep_code'];
                        // Get Module Code
                        $modCode    = $this->Helper->getModuleCode($repCode, $result['sr_id'], 'pv_code', 'pvs', 'is_void = 0 AND branch_id = '.$purchaseOrder['PurchaseOrder']['branch_id']);
                        // Updaet Module Code
                        mysql_query("UPDATE pvs SET pv_code = '".$modCode."' WHERE id = ".$result['sr_id']);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($purchaseOrderReceipt['Pv'], 'pvs');
                        $restCode[$r]['pv_code']  = $modCode;
                        $restCode[$r]['modified'] = $dateNow;
                        $restCode[$r]['dbtodo']   = 'pvs';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                        // Save GL
                        $this->loadModel('GeneralLedger');
                        $this->data['GeneralLedger']['sys_code']          = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                        $this->data['GeneralLedger']['purchase_order_id'] = $this->data['PurchaseOrder']['id'];
                        $this->data['GeneralLedger']['pv_id'] = $result['sr_id'];
                        $this->data['GeneralLedger']['date'] = $this->data['PurchaseOrder']['pay_date']!=''?$this->data['PurchaseOrder']['pay_date']:'0000-00-00';
                        $this->data['GeneralLedger']['reference'] = $purchaseOrder['PurchaseOrder']['po_code'];
                        $this->data['GeneralLedger']['is_sys'] = 1;
                        $this->data['GeneralLedger']['created_by'] = $user['User']['id'];
                        $this->GeneralLedger->saveAll($this->data);
                        $gleaderId = $this->GeneralLedger->getLastInsertId();
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($this->data['GeneralLedger'], 'general_ledgers');
                        $restCode[$r]['modified'] = $dateNow;
                        $restCode[$r]['dbtodo']   = 'general_ledgers';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                        // Load GL Detail
                        $this->loadModel('GeneralLedgerDetail');
                        $this->loadModel('LocationGroup');
                        $this->loadModel('Company');
                        $company  = $this->Company->read(null, $purchaseOrder['PurchaseOrder']['company_id']);
                        $classId  = $this->Helper->getClassId($company['Company']['id'], $company['Company']['classes'], $purchaseOrder['PurchaseOrder']['location_group_id']);
                       
                        // Save GL Detail
                        $this->data['GeneralLedgerDetail']['general_ledger_id'] = $gleaderId;
                        $this->data['GeneralLedgerDetail']['type'] = "Purchase Bill Payment";
                        $this->data['GeneralLedgerDetail']['vendor_id']   = $purchaseOrder['PurchaseOrder']['vendor_id'];
                        $this->data['GeneralLedgerDetail']['company_id']  = $purchaseOrder['PurchaseOrder']['company_id'];
                        $this->data['GeneralLedgerDetail']['branch_id']   = $purchaseOrder['PurchaseOrder']['branch_id'];
                        $this->data['GeneralLedgerDetail']['location_group_id'] = $purchaseOrder['PurchaseOrder']['location_group_id'];
                        $this->data['GeneralLedgerDetail']['location_id'] = $purchaseOrder['PurchaseOrder']['location_id'];
                        $this->data['GeneralLedgerDetail']['memo'] = "ICS: Purchase Bill # " . $purchaseOrder['PurchaseOrder']['po_code'];
                        $this->data['GeneralLedgerDetail']['class_id'] = $classId;
                        for ($j = 0; $j < 2; $j++) {
                            if ($j == 0) {
                                $this->data['GeneralLedgerDetail']['chart_account_id'] = $cashBankAccountId;
                                $this->data['GeneralLedgerDetail']['debit']  = 0;
                                $this->data['GeneralLedgerDetail']['credit'] = $totalPaid;
                                $this->GeneralLedgerDetail->saveAll($this->data);
                            } else {
                                $this->data['GeneralLedgerDetail']['chart_account_id'] = $purchaseOrder['PurchaseOrder']['ap_id'];
                                $this->data['GeneralLedgerDetail']['debit']  = $totalPaid;
                                $this->data['GeneralLedgerDetail']['credit'] = 0;
                                $this->GeneralLedgerDetail->saveAll($this->data);
                            }
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($this->data['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                        }
                        // Save File Send
                        $this->Helper->sendFileToSync($restCode, 0, 0);
                        $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Bill Payment', 'Save Add New', $result['sr_id']);
                        echo json_encode($result);
                        exit;
                    }
                }else{
                    $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Bill Payment', 'Save Add New (Error)');
                    $result['sr_id'] = 0;
                    echo json_encode($result);
                    exit;
                }
            }else{
                $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Bill Payment', 'Save Add New (Error PB Status)');
                $result['sr_id'] = 0;
                echo json_encode($result);
                exit;
            }
        }
        if (!empty($id)) {
            $this->data = $this->PurchaseOrder->read(null, $id);
            $purchaseOrder = $this->PurchaseOrder->find("first", array('conditions' => array('PurchaseOrder.id' => $id)));
            if (!empty($purchaseOrder)) {
                $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Bill Payment', 'Add New');
                $vendor = ClassRegistry::init('Vendor')->find("first", array('conditions' => array('Vendor.id' => $purchaseOrder['PurchaseOrder']['vendor_id'])));
                $purchaseOrderDetails  = ClassRegistry::init('PurchaseOrderDetail')->find("all", array('conditions' => array('PurchaseOrderDetail.purchase_order_id' => $id)));
                $purchaseOrderServices = ClassRegistry::init('PurchaseOrderService')->find("all", array('conditions' => array('PurchaseOrderService.purchase_order_id' => $id)));
                $purchaseOrderMiscs    = ClassRegistry::init('PurchaseOrderMisc')->find("all", array('conditions' => array('PurchaseOrderMisc.purchase_order_id' => $id)));
                $purchaseOrderReceipts = ClassRegistry::init('Pv')->find("all", array('conditions' => array('Pv.purchase_order_id' => $id, 'Pv.is_void' => 0)));
                $this->set(compact('purchaseOrder', 'purchaseOrderDetails', 'purchaseOrderServices', 'purchaseOrderMiscs', 'purchaseOrderReceipts', 'vendor'));
            } else {
                exit;
            }
        } else {
            exit;
        }
    }

    function printInvoice($id = null) {
        if (!empty($id)) {
            $this->layout = 'ajax';
            $purchaseOrder = ClassRegistry::init('PurchaseOrder')->find("first", array('conditions' => array('PurchaseOrder.id' => $id)));
            if (!empty($purchaseOrder)) {
                $purchaseOrderDetails = ClassRegistry::init('PurchaseOrderDetail')->find("all", array('conditions' => array('PurchaseOrderDetail.purchase_order_id' => $id)));
                $purchaseOrderServices = ClassRegistry::init('PurchaseOrderService')->find("all", array('conditions' => array('PurchaseOrderService.purchase_order_id' => $id)));
                $purchaseOrderMiscs = ClassRegistry::init('PurchaseOrderMisc')->find("all", array('conditions' => array('PurchaseOrderMisc.purchase_order_id' => $id)));
                $location = ClassRegistry::init('Location')->find("first", array('conditions' => array("Location.id" => $purchaseOrder['PurchaseOrder']['location_id'], "Location.is_active" => "1")));
                $this->set(compact('purchaseOrder', 'purchaseOrderDetails', 'location', 'purchaseOrderServices', 'purchaseOrderMiscs'));
            } else {
                exit;
            }
        } else {
            exit;
        }
    }

    function printInvoiceProduct($id = null) {
        if (!empty($id)) {
            $this->layout = 'ajax';
            $purchaseOrder = ClassRegistry::init('PurchaseOrder')->find("first", array('conditions' => array('PurchaseOrder.id' => $id)));
            if (!empty($purchaseOrder)) {
                $purchaseOrderDetails = ClassRegistry::init('PurchaseOrderDetail')->find("all", array('conditions' => array('PurchaseOrderDetail.purchase_order_id' => $id)));
                $purchaseOrderServices = ClassRegistry::init('PurchaseOrderService')->find("all", array('conditions' => array('PurchaseOrderService.purchase_order_id' => $id)));
                $purchaseOrderMiscs = ClassRegistry::init('PurchaseOrderMisc')->find("all", array('conditions' => array('PurchaseOrderMisc.purchase_order_id' => $id)));
                $location = ClassRegistry::init('Location')->find("first", array('conditions' => array("Location.id" => $purchaseOrder['PurchaseOrder']['location_id'], "Location.is_active" => "1")));
                $this->set(compact('purchaseOrder', 'purchaseOrderDetails', 'location', 'purchaseOrderServices', 'purchaseOrderMiscs'));
            } else {
                exit;
            }
        } else {
            exit;
        }
    }

    function printReceipt($receiptId = null) {
        if (!empty($receiptId)) {            
            $this->layout = 'ajax';
            $sr = ClassRegistry::init('Pv')->find("first", array('conditions' => array('Pv.purchase_order_id' => $receiptId, 'Pv.is_void' => 0), 'order' => array('Pv.id DESC')));            
            $purchaseOrder = ClassRegistry::init('PurchaseOrder')->find("first", array('conditions' => array('PurchaseOrder.id' => $sr['PurchaseOrder']['id'])));                                
            if (!empty($purchaseOrder)) {                
                $lastExchangeRate = ClassRegistry::init('ExchangeRate')->find("first", array(
                            "conditions" => array("ExchangeRate.is_active" => 1),
                            "order" => array("ExchangeRate.created desc")
                                )
                );
                $purchaseOrderDetails = ClassRegistry::init('PurchaseOrderDetail')->find("all", array('conditions' => array('PurchaseOrderDetail.purchase_order_id' => $sr['PurchaseOrder']['id'])));
                $purchaseOrderServices = ClassRegistry::init('PurchaseOrderService')->find("all", array('conditions' => array('PurchaseOrderService.purchase_order_id' => $receiptId)));
                $purchaseOrderMiscs = ClassRegistry::init('PurchaseOrderMisc')->find("all", array('conditions' => array('PurchaseOrderMisc.purchase_order_id' => $receiptId)));
                $purchaseOrderReceipts = ClassRegistry::init('Pv')->find("all", array('conditions' => array('Pv.purchase_order_id' => $sr['PurchaseOrder']['id'], 'Pv.is_void' => 0)));
                $location = ClassRegistry::init('Location')->find("first", array('conditions' => array("Location.id" => $purchaseOrder['PurchaseOrder']['location_id'], "Location.is_active" => "1")));             
                $this->set(compact('purchaseOrder', 'purchaseOrderDetails', 'location', 'purchaseOrderReceipts', 'sr', 'lastExchangeRate', 'purchaseOrderMiscs', 'purchaseOrderServices'));
            } else {
                exit;
            }
        } else {
            exit;
        }
    }

    function printReceiptOne($receiptId = null) {
        if (!empty($receiptId)) {
            $this->layout = 'ajax';
            $sr = ClassRegistry::init('Pv')->find("first", array('conditions' => array('Pv.id' => $receiptId, 'Pv.is_void' => 0)));
            $purchaseOrder = ClassRegistry::init('PurchaseOrder')->find("first", array('conditions' => array('PurchaseOrder.id' => $sr['PurchaseOrder']['id'])));

            if (!empty($purchaseOrder)) {
                $lastExchangeRate = ClassRegistry::init('ExchangeRate')->find("first", array(
                            "conditions" => array("ExchangeRate.is_active" => 1),
                            "order" => array("ExchangeRate.created desc")
                                )
                );
                $purchaseOrderDetails = ClassRegistry::init('PurchaseOrderDetail')->find("all", array('conditions' => array('PurchaseOrderDetail.purchase_order_id' => $sr['PurchaseOrder']['id'])));
                $purchaseOrderServices = ClassRegistry::init('PurchaseOrderService')->find("all", array('conditions' => array('PurchaseOrderService.purchase_order_id' => $sr['PurchaseOrder']['id'])));
                $purchaseOrderMiscs = ClassRegistry::init('PurchaseOrderMisc')->find("all", array('conditions' => array('PurchaseOrderMisc.purchase_order_id' => $sr['PurchaseOrder']['id'])));
                $purchaseOrderReceipts = ClassRegistry::init('Pv')->find("all", array('conditions' => array('Pv.purchase_order_id' => $sr['PurchaseOrder']['id'], 'Pv.is_void' => 0)));
                $location = ClassRegistry::init('Location')->find("first", array('conditions' => array("Location.id" => $purchaseOrder['PurchaseOrder']['location_id'], "Location.is_active" => "1")));

                $this->set(compact('purchaseOrder', 'purchaseOrderDetails', 'location', 'purchaseOrderReceipts', 'sr', 'lastExchangeRate', 'purchaseOrderMiscs', 'purchaseOrderServices'));
            } else {
                exit;
            }
        } else {
            exit;
        }
    }

    function searchProductCode($companyId = null, $branchId = null, $code = null, $field = null) {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $searchField = "";
        if($field == 1){
            $searchField = "trim(p.code) = '" . mysql_real_escape_string(trim($code)) . "' ";
        } else if($field == 2){
            $searchField = "trim(p.barcode) = '" . mysql_real_escape_string(trim($code)) . "'";
        }
        $cmt = "SELECT p.id,                                 
                p.code,
                p.barcode,
                p.name,
                p.unit_cost,  
                p.small_val_uom,
                p.is_expired_date,
                p.is_lots,
                p.is_packet,
                p.price_uom_id,
                IF((SELECT count(*) FROM inventories WHERE product_id = p.id AND unit_cost > 0) > 0,(SELECT unit_cost FROM inventories WHERE product_id = p.id AND unit_cost > 0 ORDER BY id DESC LIMIT 1), p.default_cost) AS unitCost                             
                FROM  products as p
                INNER JOIN product_branches ON product_branches.product_id = p.id AND product_branches.branch_id = ".$branchId."
                INNER JOIN uoms as u ON u.id = p.price_uom_id
                INNER JOIN product_pgroups ON product_pgroups.product_id = p.id
                INNER JOIN pgroups ON pgroups.id = product_pgroups.pgroup_id AND (pgroups.user_apply = 0 OR (pgroups.user_apply = 1 AND pgroups.id IN (SELECT pgroup_id FROM user_pgroups WHERE user_id = ".$user['User']['id'].")))
                WHERE p.is_active = 1 AND ".$searchField." AND ((p.price_uom_id IS NOT NULL AND p.is_packet = 0) OR (p.price_uom_id IS NULL AND p.is_packet = 1)) AND p.company_id = ".$companyId."
                GROUP BY p.code, p.name
                ORDER BY p.code";
        $product = mysql_query($cmt);
        if (@$num = mysql_num_rows($product)) {
            while ($aRow = mysql_fetch_array($product)) {
                   $packetList = '';
                   $productId = $aRow['id'];
                   $productSku = $aRow['code'];
                   $productPUC = $aRow['barcode'];
                   $productName = $aRow['name'];
                   $isExpired = $aRow['is_expired_date'];
                   $isLots    = $aRow['is_lots'];
                   $smallUomVal = $aRow['small_val_uom'];
                   $unitCost = $aRow['unitCost'];
                   $mainUomId = $aRow['price_uom_id'];
                   if($aRow['is_packet'] == 1){
                        $index = 1;
                        $sqlPacket = mysql_query("SELECT products.code, product_with_packets.qty_uom_id, product_with_packets.qty, product_with_packets.conversion FROM product_with_packets INNER JOIN products ON products.id = product_with_packets.packet_product_id WHERE product_with_packets.main_product_id = ".$productId);
                        while($rowPacket = mysql_fetch_array($sqlPacket)){
                            if($index > 1){
                                $packetList .= "**";
                            }
                            $qtyOrder = $rowPacket['qty'];
                            $packetList .= trim($rowPacket['code'])."||".trim($rowPacket['qty_uom_id'])."||".$qtyOrder;
                            $index++;
                        }
                   }
                   $data = array();
                   $data[] = $productId;
                   $data[] = htmlspecialchars(trim($productSku), ENT_QUOTES, 'UTF-8');
                   $data[] = htmlspecialchars(trim($productPUC), ENT_QUOTES, 'UTF-8');
                   $data[] = htmlspecialchars(trim($productName), ENT_QUOTES, 'UTF-8');
                   $data[] = $isExpired;
                   $data[] = $unitCost;
                   $data[] = $smallUomVal;
                   $data[] = $mainUomId;
                   $data[] = trim($packetList);
                   $data[] = $isLots;
                   echo json_encode($data);
            }
        } else {
            echo TABLE_NO_PRODUCT;                       
        }
        exit;
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
                            , '((Product.price_uom_id IS NOT NULL AND Product.is_packet = 0) OR (Product.price_uom_id IS NULL AND Product.is_packet = 1))'
                        ),
                        'joins' => $joins,
                        'group' => array(
                            'Product.id'
                        )
                    ));
        $this->set(compact('products'));
    }
    
    function service($companyId, $branchId) {
        $this->layout = 'ajax';
        $sections = ClassRegistry::init('Section')->find("list", array("conditions" => array("Section.is_active = 1", "Section.id IN (SELECT section_id FROM section_companies WHERE company_id = ".$companyId.")")));
        $services = $this->serviceCombo($companyId, $branchId);
        $this->set(compact('sections', 'services'));
    }

    function serviceCombo($companyId, $branchId) {
        $array = array();
        $services = ClassRegistry::init('Service')->find("all", array("conditions" => array("Service.company_id=" . $companyId. " AND Service.is_active = 1", "Service.id IN (SELECT service_id FROM service_branches WHERE branch_id = ".$branchId.")")));
        foreach ($services as $service) {
            $uomId = $service['Service']['uom_id']!=''?$service['Service']['uom_id']:'';
            array_push($array, array('value' => $service['Service']['id'], 'name' => $service['Service']['code']." - ".$service['Service']['name'], 'class' => $service['Section']['id'], 'abbr' => $service['Service']['name'], 'price' => $service['Service']['unit_price'], 'scode' => $service['Service']['code'], 'suom' => $uomId));
        }
        return $array;
    }
    
    function miscellaneous() {
        $this->layout = 'ajax';
    }
    
    function purchaseRequest($companyId, $branchId){
        $this->layout = "ajax";
        $this->set(compact('companyId', 'branchId'));
    }
    
    function purchaseRequestAjax($companyId, $branchId){
        $this->layout = "ajax";
        $this->set(compact('companyId', 'branchId'));
    }
    
    function checkPurchaseOrder($vendor = null, $location= null, $company = null){
        $this->layout = "ajax";
        $this->loadModel('PurchaseRequest');
        $check = $this->PurchaseRequest->find("first", array('conditions' => array('PurchaseRequest.vendor_id' => $vendor, 'PurchaseRequest.location_id' => $location, 'PurchaseRequest.company_id' => $company, 'PurchaseRequest.is_close = 0', 'PurchaseRequest.status between 1 AND 2')));
        if($check){
            echo 1;
        }else{
            echo 0;
        }
        exit;
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
    
    function getProductsFromPO($id = null){
        $this->layout = 'ajax';
        $result = array();
        if (!$id) {
            $result['result'] = 0;
            echo json_encode($result);
            exit;
        }
        $sqlSettingUomDeatil = mysql_query("SELECT uom_detail_option, product_cost_decimal FROM setting_options");
        $rowSettingUomDetail = mysql_fetch_array($sqlSettingUomDeatil);
        $user = $this->getCurrentUser();
        $allowProductDiscount = $this->Helper->checkAccess($user['User']['id'], 'purchase_orders', 'discount');
        $sqlPO = mysql_query("SELECT p.id AS id, p.code AS code, p.barcode AS barcode, p.name AS name, p.price_uom_id AS main_uom_id, p.small_val_uom AS small_uom, p.is_expired_date AS is_expired_date, prd.qty AS qty, prd.qty_uom_id AS qty_uom_id, prd.conversion AS conversion, prd.unit_cost AS unit_cost, prd.total_cost AS total_cost, prd.note AS note, purchase_requests.company_id AS company_id, purchase_requests.branch_id AS branch_id, purchase_requests.currency_center_id AS currency_id FROM purchase_request_details AS prd INNER JOIN purchase_requests ON purchase_requests.id = prd.purchase_request_id INNER JOIN products AS p ON p.id = prd.product_id WHERE prd.purchase_request_id =".$id);
        $rowList = "";
        $currencyRate = 1;
        if(@mysql_num_rows($sqlPO)){
            while($rowPO=mysql_fetch_array($sqlPO)){
                $sqlCurrency = mysql_query("SELECT rate_purchase FROM branch_currencies WHERE branch_id = ".$rowPO['branch_id']." AND currency_center_id = ".$rowPO['currency_id']." AND is_active = 1");
                if(mysql_num_rows($sqlCurrency)){
                    $rowCurrency = mysql_fetch_array($sqlCurrency);
                    $currencyRate = $rowCurrency[0];
                }
                // Check Discount Permission
                $disDiv = '';
                if($allowProductDiscount){
                    $disDiv .= '<input type="text" name="discount[]" value="0" class="discountPB btnDiscountPB" readonly="readonly" id="discountPB'.rand().'" style="width: 70%;" />';
                    $disDiv .= '<img alt="Remove" src="'.$this->webroot . 'img/button/cross.png" class="btnRemoveDiscountPB" align="absmiddle" style="cursor: pointer; display: none;" onmouseover="Tip(\'Remove\')" />';
                }else{
                    $disDiv .= '<input type="hidden" name="discount[]" value="0" class="discountPB btnDiscountPB" readonly="readonly" id="discountPB'.rand().'" style="width: 70%;" />';
                }
                // Check Product Exp
                if($rowPO['is_expired_date'] == 1){
                    $expType = "text";
                    $requiredExp = "date_expired validate[required]";
                } else {
                    $expType = "hidden";
                    $requiredExp = "";
                }
                // Get Uom Of Product
                $queryUom=mysql_query("SELECT id,name,abbr,1 AS conversion FROM uoms WHERE id=".$rowPO['main_uom_id']."
                                        UNION
                                        SELECT id,name,abbr,(SELECT value FROM uom_conversions WHERE is_active=1 AND from_uom_id=".$rowPO['main_uom_id']." AND to_uom_id=uoms.id) AS conversion FROM uoms WHERE id IN (SELECT to_uom_id FROM uom_conversions WHERE is_active=1 AND from_uom_id=".$rowPO['main_uom_id'].")
                                        ORDER BY conversion ASC");
                $k = 1;
                $options = "";
                $length = mysql_num_rows($queryUom);
                while($dataUom=mysql_fetch_array($queryUom)){
                    if($length == $k){
                        $dataSm = 1;
                    }else{
                        $dataSm = 0;
                    }
                    if($dataUom['id'] == $rowPO['main_uom_id']){
                        $dataItem = "first";
                    }else{
                        $dataItem = "other";
                    }
                    if($dataUom['id'] == $rowPO['qty_uom_id']){
                        $selected = 'selected="selected"';
                    }else{
                        $selected = '';
                    }
                    $options .='<option data-sm="'.$dataSm.'" data-item="'.$dataItem.'" value="'.$dataUom['id'].'" '.$selected.' conversion="'.$dataUom['conversion'].'">'.$dataUom['name'].'</option>';
                
                $k++;
                }
                $rowList .= '<tr class="listBodyPB">';
                // Index
                $rowList .= '<td class="first" style="width:4%"></td>';
                // UPC
                $rowList .= '<td style="width:8%">'
                         .  '<span class="purchasePUC">'.$rowPO['barcode'].'</span>'
                         .  '</td>';
                
                // SKU
                $rowList .= '<td style="width:8%">'
                         .  '<span class="purchaseSKU">'.$rowPO['code'].'</span>'
                         .  '</td>';
                // Product
                $rowList .= '<td style="width:13%">'
                         .  '<div class="inputContainer" style="width:100%">'
                         .  '<input type="hidden" name="product_id[]" value="'.$rowPO['id'].'" class="product_id" id="product_id" />'
                         .  '<input type="hidden" name="service_id[]" class="service_id" id="service_id" />'
                         .  '<input type="hidden" name="note[]" value="'.$rowPO['note'].'" class="note" id="note" />'
                         .  '<input type="hidden" name="max_order[]" value="0" class="max_order" />'
                         .  '<input type="text" name="product_name[]" value="'.$rowPO['name'].'" class="product_name validate[required]" id="product_name" readonly="readonly" style="width: 75%" />'
                         .  '<img alt="Note" src="'.$this->webroot . 'img/button/note.png" class="noteAddPB" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Note\')" />'
                         .  '</div>'
                         .  '</td>';
                // Lot Number
                $lotDispaly = '';
                if($rowSettingUomDetail[0] == 0){
                    $lotDispaly = 'display: none;';
                }
                $rowList .= '<td style="padding:0px; text-align: center; width:8%;'.$lotDispaly.'">'
                         .  '<div class="inputContainer" style="width:100%">'
                         .  '<input type="text" name="lots_number[]" id="lots_number'.rand().'" style="width:80%" class="lots_number" />'
                         .  '</div>'
                         .  '</td>';
                // Expired Date
                $rowList .= '<td style="padding:0px; text-align: center; width:8%">'
                         .  '<div class="inputContainer" style="width:100%">'
                         .  '<input type="hidden" value="'.$rowPO['is_expired_date'].'" class="is_expired" />'
                         .  '<input type="'.$expType.'" name="date_expired[]" id="date_expired'.rand().'" style="width:80%" class="'.$requiredExp.'" />'
                         .  '</div>'
                         .  '</td>';
                // QTY
                $rowList .= '<td style="padding:0px; text-align: center; width:5%">'
                         .  '<div class="inputContainer" style="width:100%">'
                         .  '<input type="text" id="qty'.rand().'" value="'.number_format($rowPO['qty'], 0).'" name="qty[]" style="width:80%;" class="qty" />'
                         .  '</div>'
                         .  '</td>';
                // QTY Free
                $rowList .= '<td style="padding:0px; text-align: center; width:5%">'
                         .  '<div class="inputContainer" style="width:100%">'
                         .  '<input type="text" id="qty_free'.rand().'" value="0" name="qty_free[]" style="width:80%;" class="qty_free" />'
                         .  '</div>'
                         .  '</td>';
                // UOM
                $rowList .= '<td style="padding:0px; text-align: center; width:9%">'
                         .  '<div class="inputContainer" style="width:100%">'
                         .  '<input type="hidden" class="small_uom_val_pb" value="'.$rowPO['small_uom'].'" name="small_uom_val_pb[]"/>'
                         .  '<input type="hidden" class="pb_conversion" value="'.$rowPO['conversion'].'" name="pb_conversion[]"/>'
                         .  '<select id="qty_uom_id'.rand().'" name="qty_uom_id[]" style="width:80%; height: 20px;" class="qty_uom_id validate[required]">'
                         .  $options
                         .  '</select>'
                         .  '</div>'
                         .  '</td>';
                // Unit Cost
                $defaultCost = number_format(($rowPO['unit_cost'] * ($rowPO['small_uom'] / $rowPO['conversion'])) / $currencyRate, 2);
                $unitCost    = $rowPO['unit_cost'] / $currencyRate;
                $totalCost   = $unitCost * $rowPO['qty'];
                $rowList .= '<td style="padding:0px; text-align: center; width:8%">'
                         .  '<div class="inputContainer" style="width:100%">'
                         .  '<input type="hidden" class="defaltCost" value="'.$defaultCost.'" />'
                         .  '<input type="text" id="unit_cost'.rand().'" value="'.number_format($unitCost, $rowSettingUomDetail[1]).'" name="unit_cost[]" class="unit_cost validate[required] float" style="width:80%" />'
                         .  '</div>'
                         .  '</td>';
                // Discount
                $rowList .= '<td style="padding:0px; text-align: center; width:7%">'
                         .  '<div class="inputContainer" style="width:100%">'
                         .  '<div style="white-space: nowrap; margin-top: 3px; width: 100%">'
                         .  '<input type="hidden" name="discount_id[]" />'
                         .  '<input type="hidden" name="discount_amount[]" />'
                         .  '<input type="hidden" name="discount_percent[]" />'
                         .  $disDiv
                         .  '</div>'
                         .  '</div>'
                         . '</td>';
                // Total Cost
                $rowList .= '<td style="padding:0px; text-align: center; width:7%">'
                         .  '<input type="hidden" id="h_total_cost'.rand().'" value="'.$totalCost.'" class="h_total_cost float" name="h_total_cost[]" />'
                         .  '<input type="text" name="total_cost[]" value="'.number_format($totalCost, $rowSettingUomDetail[1]).'" id="total_cost'.rand().'" style="width:80%" class="total_cost float" />'
                         . '</td>';
                // Buttom Remove
                $rowList .= '<td style="white-space:nowrap; padding:0px; text-align:center; width:3%"><img alt="" src="'.$this->webroot.'img/button/cross.png" class="btnRemovePB" style="cursor: pointer;" onmouseover="Tip(\'Remove\')" /></td>';
                $rowList .= '</tr>';
            }
        }
        $sqlServicePO = mysql_query("SELECT p.id AS id, p.code AS code, p.name AS name, uoms.abbr AS uom, uoms.id AS uom_id, prd.qty AS qty, prd.unit_cost AS unit_cost, prd.total_cost AS total_cost, prd.note AS note, purchase_requests.company_id AS company_id, purchase_requests.branch_id AS branch_id, purchase_requests.currency_center_id AS currency_id FROM purchase_request_services AS prd INNER JOIN purchase_requests ON purchase_requests.id = prd.purchase_request_id INNER JOIN services AS p ON p.id = prd.service_id LEFT JOIN uoms ON uoms.id = p.uom_id WHERE prd.purchase_request_id =".$id);
        if(mysql_num_rows($sqlServicePO)){
            while($rowPO = mysql_fetch_array($sqlServicePO)){
                $sqlCurrency = mysql_query("SELECT rate_purchase FROM branch_currencies WHERE branch_id = ".$rowPO['branch_id']." AND currency_center_id = ".$rowPO['currency_id']." AND is_active = 1");
                if(mysql_num_rows($sqlCurrency)){
                    $rowCurrency = mysql_fetch_array($sqlCurrency);
                    $currencyRate = $rowCurrency[0];
                }
                // Check Discount Permission
                $disDiv = '';
                if($allowProductDiscount){
                    $disDiv .= '<input type="text" name="discount[]" value="0" class="discountPB btnDiscountPB" readonly="readonly" id="discountPB'.rand().'" style="width: 70%;" />';
                    $disDiv .= '<img alt="Remove" src="'.$this->webroot . 'img/button/cross.png" class="btnRemoveDiscountPB" align="absmiddle" style="cursor: pointer; display: none;" onmouseover="Tip(\'Remove\')" />';
                }else{
                    $disDiv .= '<input type="hidden" name="discount[]" value="0" class="discountPB btnDiscountPB" readonly="readonly" id="discountPB'.rand().'" style="width: 70%;" />';
                }
                // Check Product Exp
                $requiredExp = "";
                // Get UOM
                $optionUom = '<option value="'.$rowPO['uom_id'].'" conversion="1" selected="selected">'.$rowPO['uom'].'</option>';
                $rowList .= '<tr class="listBodyPB">';
                // Index
                $rowList .= '<td class="first" style="width:4%"></td>';
                // UPC
                $rowList .= '<td style="width:8%">'
                         .  '<span class="purchasePUC"></span>'
                         .  '</td>';
                
                // SKU
                $rowList .= '<td style="width:8%">'
                         .  '<span class="purchaseSKU">'.$rowPO['code'].'</span>'
                         .  '</td>';
                // Service
                $rowList .= '<td style="width:13%">'
                         .  '<div class="inputContainer" style="width:100%">'
                         .  '<input type="hidden" name="product_id[]" value="" class="product_id" id="product_id" />'
                         .  '<input type="hidden" name="service_id[]" value="'.$rowPO['id'].'" class="service_id" id="service_id" />'
                         .  '<input type="hidden" name="note[]" value="'.$rowPO['note'].'" class="note" id="note" />'
                         .  '<input type="hidden" name="max_order[]" value="0" class="max_order" />'
                         .  '<input type="text" name="product_name[]" value="'.$rowPO['name'].'" class="product_name validate[required]" id="product_name" readonly="readonly" style="width: 75%" />'
                         .  '<img alt="Note" src="'.$this->webroot . 'img/button/note.png" class="noteAddPB" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Note\')" />'
                         .  '</div>'
                         .  '</td>';
                // Lot Number
                $lotDispaly = '';
                if($rowSettingUomDetail[0] == 0){
                    $lotDispaly = 'display: none;';
                }
                $rowList .= '<td style="padding:0px; text-align: center; width:8%;'.$lotDispaly.'">'
                         .  '<div class="inputContainer" style="width:100%">'
                         .  '<input type="hidden" name="lots_number[]" id="lots_number'.rand().'" style="width:80%" class="lots_number" readonly="readonly" />'
                         .  '</div>'
                         .  '</td>';
                // Expired Date
                $rowList .= '<td style="padding:0px; text-align: center; width:8%">'
                         .  '<div class="inputContainer" style="width:100%">'
                         .  '<input type="hidden" value="0" class="is_expired" />'
                         .  '<input type="hidden" name="date_expired[]" id="date_expired'.rand().'" style="width:80%;" class="'.$requiredExp.'" readonly="readonly" />'
                         .  '</div>'
                         .  '</td>';
                // QTY
                $rowList .= '<td style="padding:0px; text-align: center; width:5%">'
                         .  '<div class="inputContainer" style="width:100%">'
                         .  '<input type="text" id="qty'.rand().'" value="'.number_format($rowPO['qty'], 0).'" name="qty[]" style="width:80%;" class="qty" />'
                         .  '</div>'
                         .  '</td>';
                // QTY Free
                $rowList .= '<td style="padding:0px; text-align: center; width:5%">'
                         .  '<div class="inputContainer" style="width:100%">'
                         .  '<input type="text" id="qty_free'.rand().'" value="0" name="qty_free[]" style="width:80%;" class="qty_free" />'
                         .  '</div>'
                         .  '</td>';
                // UOM
                $rowList .= '<td style="padding:0px; text-align: center; width:9%">'
                         .  '<div class="inputContainer" style="width:100%">'
                         .  '<input type="hidden" class="small_uom_val_pb" value="1" name="small_uom_val_pb[]"/>'
                         .  '<input type="hidden" class="pb_conversion" value="1" name="pb_conversion[]"/>'
                         .  '<select id="qty_uom_id'.rand().'" name="qty_uom_id[]" style="width:80%; height: 20px; display: none;" class="qty_uom_id">'
                         .  $optionUom
                         .  '</select>'
                         .  '</div>'
                         .  '</td>';
                // Unit Cost
                $defaultCost = number_format($rowPO['unit_cost'] / $currencyRate, 2);
                $unitCost    = $rowPO['unit_cost'] / $currencyRate;
                $totalCost   = $unitCost * $rowPO['qty'];
                $rowList .= '<td style="padding:0px; text-align: center; width:8%">'
                         .  '<div class="inputContainer" style="width:100%">'
                         .  '<input type="hidden" class="defaltCost" value="'.$defaultCost.'" />'
                         .  '<input type="text" id="unit_cost'.rand().'" value="'.number_format($unitCost, $rowSettingUomDetail[1]).'" name="unit_cost[]" class="unit_cost validate[required] float" style="width:80%" />'
                         .  '</div>'
                         .  '</td>';
                // Discount
                $rowList .= '<td style="padding:0px; text-align: center; width:7%">'
                         .  '<div class="inputContainer" style="width:100%">'
                         .  '<div style="white-space: nowrap; margin-top: 3px; width: 100%">'
                         .  '<input type="hidden" name="discount_id[]" />'
                         .  '<input type="hidden" name="discount_amount[]" />'
                         .  '<input type="hidden" name="discount_percent[]" />'
                         .  $disDiv
                         .  '</div>'
                         .  '</div>'
                         . '</td>';
                // Total Cost
                $rowList .= '<td style="padding:0px; text-align: center; width:7%">'
                         .  '<input type="hidden" id="h_total_cost'.rand().'" value="'.$totalCost.'" class="h_total_cost float" name="h_total_cost[]" />'
                         .  '<input type="text" name="total_cost[]" value="'.number_format($totalCost, $rowSettingUomDetail[1]).'" id="total_cost'.rand().'" style="width:80%" class="total_cost float" />'
                         . '</td>';
                // Buttom Remove
                $rowList .= '<td style="white-space:nowrap; padding:0px; text-align:center; width:3%"><img alt="" src="'.$this->webroot.'img/button/cross.png" class="btnRemovePB" style="cursor: pointer;" onmouseover="Tip(\'Remove\')" /></td>';
                $rowList .= '</tr>';
            }
        }
        $result['result'] = $rowList;
        echo json_encode($result);
        exit;
    }
    
    function close($id = null){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (empty($id)) {
            $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Bill', 'Close (Error Id)', $id);
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $purchaseOrder = $this->PurchaseOrder->find("first", array('conditions' => array('PurchaseOrder.id' => $id)));
        if($purchaseOrder['PurchaseOrder']['status'] == 1){
            if($purchaseOrder['PurchaseOrder']['vendor_consignment_id'] != ''){
                // Load Model
                $this->loadModel('PurchaseReceiveResult');
                $this->PurchaseReceiveResult->create();
                $purchaseRecResult = array();
                $purchaseRecResult['PurchaseReceiveResult']['purchase_order_id'] = $id;
                $purchaseRecResult['PurchaseReceiveResult']['date'] = date("Y-m-d");
                $purchaseRecResult['PurchaseReceiveResult']['created_by'] = $user['User']['id']; 
                if($this->PurchaseReceiveResult->save($purchaseRecResult)){
                    $purchaseRecResultId = $this->PurchaseReceiveResult->id;
                    // Update Code Receive Result Code
                    $sqlRecCode = mysql_query("SELECT CONCAT('".date("y")."GRR','',LPAD(((SELECT count(tmp.id) FROM `purchase_receive_results` as tmp WHERE tmp.code LIKE '".date("y")."GRR%' AND tmp.id < ".$purchaseRecResultId.") + 1),7,'0')) AS code");
                    $rowRecCode = mysql_fetch_array($sqlRecCode);
                    mysql_query("UPDATE purchase_receive_results SET code = '".$rowRecCode['code']."' WHERE id = ".$purchaseRecResultId);
                    // Load Model
                    $this->loadModel('PurchaseReceiveResult');
                    $purchaseOrderDetails = ClassRegistry::init('PurchaseOrderDetail')->find("all", array('conditions' => array('PurchaseOrderDetail.purchase_order_id' => $id)));
                    foreach($purchaseOrderDetails AS $purchaseOrderDetail){
                        // Update unit cost for product                             
                        mysql_query("UPDATE products SET unit_cost = '".$purchaseOrderDetail['PurchaseOrderDetail']['new_unit_cost']."' WHERE id=".$purchaseOrderDetail['PurchaseOrderDetail']['product_id']);
                        // Purchase Receive
                        ClassRegistry::init('PurchaseReceive')->create();
                        $this->data['PurchaseReceive']['purchase_receive_result_id'] = $purchaseRecResultId;
                        $this->data['PurchaseReceive']['purchase_order_id'] = $id;
                        $this->data['PurchaseReceive']['purchase_order_detail_id'] = $purchaseOrderDetail['PurchaseOrderDetail']['id'];
                        $this->data['PurchaseReceive']['product_id'] = $purchaseOrderDetail['PurchaseOrderDetail']['product_id'];
                        $this->data['PurchaseReceive']['qty'] = $purchaseOrderDetail['PurchaseOrderDetail']['qty'];
                        $this->data['PurchaseReceive']['qty_uom_id'] = $purchaseOrderDetail['PurchaseOrderDetail']['qty_uom_id'];
                        $this->data['PurchaseReceive']['conversion'] = $purchaseOrderDetail['PurchaseOrderDetail']['conversion'];
                        $this->data['PurchaseReceive']['received_date'] = date("Y-m-d");
                        $this->data['PurchaseReceive']['lots_number']   = $purchaseOrderDetail['PurchaseOrderDetail']['lots_number'];
                        $this->data['PurchaseReceive']['date_expired']  = $purchaseOrderDetail['PurchaseOrderDetail']['date_expired'];
                        $this->data['PurchaseReceive']['created_by'] = $user['User']['id'];
                        $this->data['PurchaseReceive']['status'] = 1;
                        ClassRegistry::init('PurchaseReceive')->save($this->data);
                    }
                    mysql_query("UPDATE `purchase_orders` SET `modified_by`=".$user['User']['id'].", `status`=3 WHERE `id` = ".$id.";");
                    $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Bill', 'Close With Consignment', $id);
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                }
            } else {
                $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Bill', 'Close', $id);
                mysql_query("UPDATE `purchase_orders` SET `modified_by`=".$user['User']['id'].", `status`=3 WHERE `id` = ".$id.";");
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                exit;
            }
        } else {
            $this->Helper->saveUserActivity($user['User']['id'], 'Purchase Bill', 'Close (Error Status)', $id);
            echo MESSAGE_DATA_INVALID;
            exit;
        }
    }
    
    function searchPurchaseOrder($poNum = null){
        $this->layout = 'ajax';
        if (empty($poNum)) {
            $result['error'] = 1;
            echo json_encode($result);
            exit;
        }
        $user = $this->getCurrentUser();
        $purchaseRequest = ClassRegistry::init('PurchaseRequest')->find('first', array(
                            'conditions' => array(
                                'PurchaseRequest.pr_code' => $poNum,
                                'PurchaseRequest.status' => 1,
                                'PurchaseRequest.company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')',
                                'PurchaseRequest.branch_id IN (SELECT branch_id FROM user_branches WHERE user_id = '.$user['User']['id'].')',
                                'PurchaseRequest.is_close' => 0,
                            )
                          ));
        if(!empty($purchaseRequest)){
            $purchaseRequest['PurchaseRequest']['order_date'] = $this->Helper->dateShort($purchaseRequest['PurchaseRequest']['order_date']);
        }else{
            $purchaseRequest['error'] = 1;
        }
        echo json_encode($purchaseRequest);
        exit;
    }
    
    function invoiceDiscount(){
        $this->layout = 'ajax';
    }
    
    function viewPurchaseIssued(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        // Check Module Exist
        $sqlDash = mysql_query("SELECT id FROM user_dashboards WHERE module_id = 505 AND user_id = {$user['User']['id']} LIMIT 1");
        if(!mysql_num_rows($sqlDash)){
            $this->loadModel('UserDashboard');
            $userDash = array();
            $userDash['UserDashboard']['user_id']      = $user['User']['id'];
            $userDash['UserDashboard']['module_id']    = 505;
            $userDash['UserDashboard']['display']      = 1;
            $userDash['UserDashboard']['auto_refresh'] = 1;
            $userDash['UserDashboard']['time_refresh'] = 5;
            $this->UserDashboard->save($userDash);
        }
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
    
    function vendorConsignment($companyId = null, $branchId = null, $locationGroupId = null, $locationId = null, $vendorId = '') {
        $this->layout = 'ajax';
        $this->set(compact('companyId', 'vendorId', 'branchId', 'locationGroupId', 'locationId'));
    }

    function vendorConsignmentAjax($companyId = null, $branchId = null, $locationGroupId = null, $locationId = null, $vendorId = '') {
        $this->layout = 'ajax';
        $this->set(compact('companyId', 'vendorId', 'branchId', 'locationGroupId', 'locationId'));
    }
    
    function getVendorConsignment($id = null, $pbId = 0){
        $this->layout = 'ajax';
        $result = array();
        if (!$id) {
            $result['result'] = 0;
            echo json_encode($result);
            exit;
        }
        include("includes/function.php");
        $sqlSettingUomDeatil = mysql_query("SELECT uom_detail_option FROM setting_options");
        $rowSettingUomDetail = mysql_fetch_array($sqlSettingUomDeatil);
        $user = $this->getCurrentUser();
        $allowProductDiscount = $this->Helper->checkAccess($user['User']['id'], 'purchase_orders', 'discount');
        $sqlPO = mysql_query("SELECT p.id AS id, p.code AS code, p.barcode AS barcode, p.name AS name, p.price_uom_id AS main_uom_id, p.small_val_uom AS small_uom, p.is_expired_date AS is_expired_date, prd.qty AS qty, prd.qty_uom_id AS qty_uom_id, prd.conversion AS conversion, prd.unit_cost AS unit_cost, prd.total_cost AS total_cost, prd.note AS note, prd.lots_number AS lots_number, prd.date_expired AS date_expired, vendor_consignments.company_id AS company_id, vendor_consignments.branch_id AS branch_id, vendor_consignments.currency_center_id AS currency_id, vendor_consignments.location_group_id AS location_group_id, vendor_consignments.location_id AS location_id FROM vendor_consignment_details AS prd INNER JOIN vendor_consignments ON vendor_consignments.id = prd.vendor_consignment_id INNER JOIN products AS p ON p.id = prd.product_id WHERE prd.vendor_consignment_id =".$id);
        $rowList = "";
        $currencyRate = 1;
        if(@mysql_num_rows($sqlPO)){
            while($rowPO=mysql_fetch_array($sqlPO)){
                // Total PB Receive
                $totalPB = 0;
                $sqlPB = mysql_query("SELECT SUM((purchase_order_details.qty + purchase_order_details.qty_free) * purchase_order_details.conversion) AS total_pb FROM purchase_order_details INNER JOIN purchase_orders ON purchase_orders.id = purchase_order_details.purchase_order_id AND purchase_orders.vendor_consignment_id = {$id} AND purchase_orders.id != {$pbId} AND purchase_orders.status > 0 WHERE purchase_orders.location_group_id = ".$rowPO['location_group_id']." AND purchase_orders.location_id = ".$rowPO['location_id']." AND purchase_order_details.product_id = {$rowPO['id']} AND purchase_order_details.lots_number = '".$rowPO['lots_number']."' AND purchase_order_details.date_expired = '".$rowPO['date_expired']."'");
                if(mysql_num_rows($sqlPB)){
                    $rowPB = mysql_fetch_array($sqlPB);
                    $totalPB = $rowPB[0];
                }
                // Total Vendor Return Consignment
                $totalReturn = 0;
                $sqlReturn = mysql_query("SELECT SUM(qty * conversion) AS total_return FROM vendor_consignment_return_details INNER JOIN vendor_consignment_returns ON vendor_consignment_returns.id = vendor_consignment_return_details.vendor_consignment_return_id AND vendor_consignment_returns.status > 0 AND vendor_consignment_returns.location_group_id = ".$rowPO['location_group_id']." AND vendor_consignment_returns.location_id = ".$rowPO['location_id']." WHERE product_id = {$rowPO['id']} AND lots_number = '".$rowPO['lots_number']."' AND date_expired = '".$rowPO['date_expired']."'");
                if(mysql_num_rows($sqlReturn)){
                    $rowReturn = mysql_fetch_array($sqlReturn);
                    $totalReturn = $rowReturn[0];
                }
                $inputQty = $rowPO['qty'];
                $consignmentOrder = ($rowPO['qty'] * $rowPO['conversion']);
                $maxOrder = ($consignmentOrder - $totalPB - $totalReturn)>0?($consignmentOrder - $totalPB - $totalReturn):0;
                $isSmallSelected = 0;
                if($maxOrder < $consignmentOrder){
                    $inputQty = $maxOrder;
                    $isSmallSelected = 1;
                }
                $sqlCurrency = mysql_query("SELECT rate_purchase FROM branch_currencies WHERE branch_id = ".$rowPO['branch_id']." AND currency_center_id = ".$rowPO['currency_id']." AND is_active = 1");
                if(mysql_num_rows($sqlCurrency)){
                    $rowCurrency = mysql_fetch_array($sqlCurrency);
                    $currencyRate = $rowCurrency[0];
                }
                // Check Discount Permission
                $disDiv = '';
                if($allowProductDiscount){
                    $disDiv .= '<input type="text" name="discount[]" value="0" class="discountPB btnDiscountPB" readonly="readonly" id="discountPB'.rand().'" style="width: 70%;" />';
                    $disDiv .= '<img alt="Remove" src="'.$this->webroot . 'img/button/cross.png" class="btnRemoveDiscountPB" align="absmiddle" style="cursor: pointer; display: none;" onmouseover="Tip(\'Remove\')" />';
                }else{
                    $disDiv .= '<input type="hidden" name="discount[]" value="0" class="discountPB btnDiscountPB" readonly="readonly" id="discountPB'.rand().'" style="width: 70%;" />';
                }
                // Check Product Exp
                if($rowPO['is_expired_date'] == 1){
                    $expType = "text";
                    $requiredExp = "date_expired validate[required]";
                } else {
                    $expType = "hidden";
                    $requiredExp = "";
                }
                // Get Uom Of Product
                $queryUom=mysql_query("SELECT id,name,abbr,1 AS conversion FROM uoms WHERE id=".$rowPO['main_uom_id']."
                                        UNION
                                        SELECT id,name,abbr,(SELECT value FROM uom_conversions WHERE is_active=1 AND from_uom_id=".$rowPO['main_uom_id']." AND to_uom_id=uoms.id) AS conversion FROM uoms WHERE id IN (SELECT to_uom_id FROM uom_conversions WHERE is_active=1 AND from_uom_id=".$rowPO['main_uom_id'].")
                                        ORDER BY conversion ASC");
                $k = 1;
                $options = "";
                $length = mysql_num_rows($queryUom);
                while($dataUom=mysql_fetch_array($queryUom)){
                    if($length == $k && $isSmallSelected == 1){
                        $selected = 'selected="selected"';
                        $dataSm = 1;
                    }else{
                        $dataSm = 0;
                    }
                    if($dataUom['id'] == $rowPO['main_uom_id']){
                        $dataItem = "first";
                    }else{
                        $dataItem = "other";
                    }
                    if($dataUom['id'] == $rowPO['qty_uom_id'] && $isSmallSelected == 0){
                        $selected = 'selected="selected"';
                    }else{
                        $selected = '';
                    }
                    $options .='<option data-sm="'.$dataSm.'" data-item="'.$dataItem.'" value="'.$dataUom['id'].'" '.$selected.' conversion="'.$dataUom['conversion'].'">'.$dataUom['name'].'</option>';
                    $k++;
                }
                $rowList .= '<tr class="listBodyPB">';
                // Index
                $rowList .= '<td class="first" style="width:4%"></td>';
                // UPC
                $rowList .= '<td style="width:8%">'
                         .  '<span class="purchasePUC">'.$rowPO['barcode'].'</span>'
                         .  '</td>';
                
                // SKU
                $rowList .= '<td style="width:8%">'
                         .  '<span class="purchaseSKU">'.$rowPO['code'].'</span>'
                         .  '</td>';
                // Product
                $rowList .= '<td style="width:13%">'
                         .  '<div class="inputContainer" style="width:100%">'
                         .  '<input type="hidden" name="product_id[]" value="'.$rowPO['id'].'" class="product_id" id="product_id" />'
                         .  '<input type="hidden" name="service_id[]" class="service_id" id="service_id" />'
                         .  '<input type="hidden" name="max_order[]" value="'.$maxOrder.'" class="max_order" />'
                         .  '<input type="hidden" name="note[]" value="'.$rowPO['note'].'" class="note" id="note" />'
                         .  '<input type="text" name="product_name[]" value="'.$rowPO['name'].'" class="product_name validate[required]" id="product_name" readonly="readonly" style="width: 75%" />'
                         .  '<img alt="Note" src="'.$this->webroot . 'img/button/note.png" class="noteAddPB" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Note\')" />'
                         .  '</div>'
                         .  '</td>';
                // Lot Number
                $lotDispaly = '';
                if($rowSettingUomDetail[0] == 0){
                    $lotDispaly = 'display: none;';
                }
                $rowList .= '<td style="padding:0px; text-align: center; width:8%;'.$lotDispaly.'">'
                         .  '<div class="inputContainer" style="width:100%">'
                         .  '<input type="text" name="lots_number[]" id="lots_number'.rand().'" style="width:80%" class="lots_number" value="'.$rowPO['lots_number'].'" />'
                         .  '</div>'
                         .  '</td>';
                // Expired Date
                $expDisplay = '';
                $class = '';
                if($rowPO['is_expired_date'] == 0){
                    $expDisplay = 'visibility: hidden;';
                    $dateExp = '0000-00-00';
                } else {
                    $dateExp = '';
                    $class = 'class="date_expired validate[required]"';
                    if($rowPO['date_expired'] != "" && $rowPO['date_expired'] != "0000-00-00"){
                        $dateExp = dateShort($rowPO['date_expired']);
                    }
                }
                $rowList .= '<td style="padding:0px; text-align: center; width:8%">'
                         .  '<div class="inputContainer" style="width:100%">'
                         .  '<input type="hidden" value="'.$rowPO['is_expired_date'].'" class="is_expired" />'
                         .  '<input type="text" name="date_expired[]" id="date_expired'.rand().'" style="width:80%;'.$expDisplay.'" class="'.$class.'" value="'.$dateExp.'" />'
                         .  '</div>'
                         .  '</td>';
                // QTY
                $rowList .= '<td style="padding:0px; text-align: center; width:5%">'
                         .  '<div class="inputContainer" style="width:100%">'
                         .  '<input type="text" id="qty'.rand().'" value="'.$inputQty.'" name="qty[]" style="width:80%;" class="qty" />'
                         .  '</div>'
                         .  '</td>';
                // QTY Free
                $rowList .= '<td style="padding:0px; text-align: center; width:5%">'
                         .  '<div class="inputContainer" style="width:100%">'
                         .  '<input type="text" id="qty_free'.rand().'" value="0" name="qty_free[]" style="width:80%;" class="qty_free" />'
                         .  '</div>'
                         .  '</td>';
                // UOM
                $rowList .= '<td style="padding:0px; text-align: center; width:9%">'
                         .  '<div class="inputContainer" style="width:100%">'
                         .  '<input type="hidden" class="small_uom_val_pb" value="'.$rowPO['small_uom'].'" name="small_uom_val_pb[]"/>'
                         .  '<input type="hidden" class="pb_conversion" value="'.$rowPO['conversion'].'" name="pb_conversion[]"/>'
                         .  '<select id="qty_uom_id'.rand().'" name="qty_uom_id[]" style="width:80%; height: 20px;" class="qty_uom_id validate[required]">'
                         .  $options
                         .  '</select>'
                         .  '</div>'
                         .  '</td>';
                // Unit Cost
                $defaultCost = number_format(($rowPO['unit_cost'] * ($rowPO['small_uom'] / $rowPO['conversion'])) / $currencyRate, 2);
                $unitCost    = $rowPO['unit_cost'] / $currencyRate;
                $totalCost   = $unitCost * $rowPO['qty'];
                $rowList .= '<td style="padding:0px; text-align: center; width:8%">'
                         .  '<div class="inputContainer" style="width:100%">'
                         .  '<input type="hidden" class="defaltCost" value="'.$defaultCost.'" />'
                         .  '<input type="text" id="unit_cost'.rand().'" value="'.number_format($unitCost, 2).'" name="unit_cost[]" class="unit_cost validate[required] float" style="width:80%" />'
                         .  '</div>'
                         .  '</td>';
                // Discount
                $rowList .= '<td style="padding:0px; text-align: center; width:7%">'
                         .  '<div class="inputContainer" style="width:100%">'
                         .  '<div style="white-space: nowrap; margin-top: 3px; width: 100%">'
                         .  '<input type="hidden" name="discount_id[]" />'
                         .  '<input type="hidden" name="discount_amount[]" />'
                         .  '<input type="hidden" name="discount_percent[]" />'
                         .  $disDiv
                         .  '</div>'
                         .  '</div>'
                         . '</td>';
                // Total Cost
                $rowList .= '<td style="padding:0px; text-align: center; width:7%">'
                         .  '<input type="hidden" id="h_total_cost'.rand().'" value="'.number_format($totalCost, 2).'" class="h_total_cost float" name="h_total_cost[]" />'
                         .  '<input type="text" name="total_cost[]" value="'.number_format($totalCost, 2).'" id="total_cost'.rand().'" style="width:80%" class="total_cost float" />'
                         . '</td>';
                // Buttom Remove
                $rowList .= '<td style="white-space:nowrap; padding:0px; text-align:center; width:3%"><img alt="" src="'.$this->webroot.'img/button/cross.png" class="btnRemovePB" style="cursor: pointer;" onmouseover="Tip(\'Remove\')" /></td>';
                $rowList .= '</tr>';
            }
        }
        $result['result'] = $rowList;
        echo json_encode($result);
        exit;
    }

}

?>