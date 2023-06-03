<?php

class PurchaseReturnsController extends AppController {

    var $name = 'PurchaseReturns';
    var $components = array('Helper', 'Inventory');

    function index() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $this->Helper->saveUserActivity($user['User']['id'], 'Bill Return', 'Dashboard');
        $locationGroups = ClassRegistry::init('LocationGroup')->find('all', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))),'conditions' => array('user_location_groups.user_id=' . $user['User']['id'], 'LocationGroup.is_active' => '1', 'LocationGroup.location_group_type_id != 1')));
        $locations = ClassRegistry::init('Location')->find('all', array('joins' => array(array('table' => 'user_locations', 'type' => 'inner', 'conditions' => array('user_locations.location_id=Location.id'))), 'conditions' => array('user_locations.user_id=' . $user['User']['id'], 'Location.is_active=1'), 'order' => 'Location.name'));
        $this->set(compact('locationGroups', 'locations'));
    }

    function ajax($filterStatus = 'all', $balance = 'all', $vendor = "all", $date = '') {
        $this->layout = 'ajax';
        $this->set(compact('filterStatus', 'balance', 'vendor',  'date'));
    }

    function view($id = null) {
        $this->layout = 'ajax';
        if (!empty($id)) {
            $user = $this->getCurrentUser();
            $this->Helper->saveUserActivity($user['User']['id'], 'Bill Return', 'View', $id);
            $this->data = $this->PurchaseReturn->read(null, $id);
            $purchaseReturn = ClassRegistry::init('PurchaseReturn')->find("first", array('conditions' => array('PurchaseReturn.id' => $id)));
            if (!empty($purchaseReturn)) {
                $purchaseReturnDetails = ClassRegistry::init('PurchaseReturnDetail')->find("all", array('conditions' => array('PurchaseReturnDetail.purchase_return_id' => $id)));
                $purchaseReturnReceipts = ClassRegistry::init('PurchaseReturnReceipt')->find("all", array('conditions' => array('PurchaseReturnReceipt.purchase_return_id' => $id, 'PurchaseReturnReceipt.is_void' => 0)));
                $purchaseReturnServices = ClassRegistry::init('PurchaseReturnService')->find('all', array(
                    'conditions' => array('PurchaseReturnService.purchase_return_id' => $id)
                        )
                );
                $purchaseReturnMiscs = ClassRegistry::init('PurchaseReturnMisc')->find('all', array(
                    'conditions' => array('PurchaseReturnMisc.purchase_return_id' => $id)
                        )
                );
                $this->set(compact('purchaseReturn', 'purchaseReturnDetails', 'purchaseReturnServices', 'purchaseReturnMiscs', 'purchaseReturnReceipts'));
            } else {
                exit;
            }
        } else {
            exit;
        }
    }

    function add() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $r = 0;
            $restCode = array();
            $dateNow  = date("Y-m-d H:i:s");
            $this->loadModel('PurchaseReturnDetail');
            $this->loadModel('PurchaseReturnMiscs');
            $this->loadModel('PurchaseReturnService');
            $this->loadModel('PurchaseReturnReceipt');
            $this->loadModel('GeneralLedger');
            $this->loadModel('GeneralLedgerDetail');
            $this->loadModel('InventoryValuation');
            $this->loadModel('AccountType');
            $this->loadModel('Company');
            $this->loadModel('PurchaseReturnReceive');

            $result = array();
            $this->PurchaseReturn->create();
            $this->GeneralLedger->create();

            //  Find Chart Account
            $apAccount = $this->AccountType->findById(14);
            $purchaseMiscAccount = $this->AccountType->findById(10);
            $purchaseReturn = array();
            $purchaseReturn['PurchaseReturn']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
            $purchaseReturn['PurchaseReturn']['created']    = $dateNow;
            $purchaseReturn['PurchaseReturn']['created_by'] = $user['User']['id'];
            $purchaseReturn['PurchaseReturn']['company_id'] = $this->data['PurchaseReturn']['company_id'];
            $purchaseReturn['PurchaseReturn']['branch_id']  = $this->data['PurchaseReturn']['branch_id'];
            $purchaseReturn['PurchaseReturn']['location_group_id'] = $this->data['PurchaseReturn']['location_group_id'];
            $purchaseReturn['PurchaseReturn']['location_id'] = $this->data['PurchaseReturn']['location_id'];
            $purchaseReturn['PurchaseReturn']['vendor_id']   = $this->data['PurchaseReturn']['vendor_id'];
            $purchaseReturn['PurchaseReturn']['currency_center_id'] = $this->data['PurchaseReturn']['currency_center_id'];
            $purchaseReturn['PurchaseReturn']['note']    = $this->data['PurchaseReturn']['note'];
            $purchaseReturn['PurchaseReturn']['ap_id']   = $apAccount['AccountType']['chart_account_id'];
            $purchaseReturn['PurchaseReturn']['pr_code'] = $this->data['PurchaseReturn']['pr_code'];
            $purchaseReturn['PurchaseReturn']['balance'] = $this->data['PurchaseReturn']['total_amount'] + $this->data['PurchaseReturn']['total_vat'];
            $purchaseReturn['PurchaseReturn']['total_amount'] = $this->data['PurchaseReturn']['total_amount'];
            $purchaseReturn['PurchaseReturn']['order_date']  = $this->data['PurchaseReturn']['order_date'];
            $purchaseReturn['PurchaseReturn']['vat_percent'] = $this->data['PurchaseReturn']['vat_percent'];
            $purchaseReturn['PurchaseReturn']['total_vat']   = $this->data['PurchaseReturn']['total_vat'];
            $purchaseReturn['PurchaseReturn']['vat_setting_id']  = $this->data['PurchaseReturn']['vat_setting_id'];
            $purchaseReturn['PurchaseReturn']['vat_calculate']   = $this->data['PurchaseReturn']['vat_calculate'];
            $purchaseReturn['PurchaseReturn']['vat_chart_account_id'] = $this->data['PurchaseReturn']['vat_chart_account_id'];
            $purchaseReturn['PurchaseReturn']['status'] = 2;

            if ($this->PurchaseReturn->save($purchaseReturn)) {
                $result['br_id'] = $purchaseReturnId = $this->PurchaseReturn->id;
                $company         = $this->Company->read(null, $this->data['PurchaseReturn']['company_id']);
                $classId         = $this->Helper->getClassId($company['Company']['id'], $company['Company']['classes'], $this->data['PurchaseReturn']['location_group_id']);
                if($this->data['PurchaseReturn']['pr_code'] == ''){
                    $branchCode  = ClassRegistry::init('ModuleCodeBranch')->find('first', array('conditions' => array('ModuleCodeBranch.branch_id' => $this->data['PurchaseReturn']['branch_id'])));
                    $this->data['PurchaseReturn']['pr_code'] = date("y").$branchCode['ModuleCodeBranch']['br_code'];
                    // Get Module Code
                    $modCode = $this->Helper->getModuleCode($this->data['PurchaseReturn']['pr_code'], $purchaseReturnId, 'pr_code', 'purchase_returns', 'status != -1 AND branch_id = '.$this->data['PurchaseReturn']['branch_id']);
                    // Updaet Module Code
                    mysql_query("UPDATE purchase_returns SET pr_code = '".$modCode."' WHERE id = ".$purchaseReturnId);
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($purchaseReturn['PurchaseReturn'], 'purchase_returns');
                    $restCode[$r]['pr_code']  = $modCode;
                    $restCode[$r]['modified'] = $dateNow;
                    $restCode[$r]['dbtodo']   = 'purchase_returns';
                    $restCode[$r]['actodo']   = 'is';
                    $r++;
                } else {
                    $modCode = $this->data['PurchaseReturn']['pr_code'];
                }
                // Create General Ledger
                $generalLedger = array();
                $this->GeneralLedger->create();
                $generalLedger['GeneralLedger']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $generalLedger['GeneralLedger']['purchase_return_id'] = $purchaseReturnId;
                $generalLedger['GeneralLedger']['date'] = $this->data['PurchaseReturn']['order_date'];
                $generalLedger['GeneralLedger']['reference']  = $modCode;
                $generalLedger['GeneralLedger']['created']    = $dateNow;
                $generalLedger['GeneralLedger']['created_by'] = $user['User']['id'];
                $generalLedger['GeneralLedger']['is_sys'] = 1;
                $generalLedger['GeneralLedger']['is_adj'] = 0;
                $generalLedger['GeneralLedger']['is_active'] = 1;

                $this->GeneralLedger->save($generalLedger);
                $generalLedgerId = $this->GeneralLedger->id;
                // Convert to REST
                $restCode[$r] = $this->Helper->convertToDataSync($generalLedger['GeneralLedger'], 'general_ledgers');
                $restCode[$r]['modified'] = $dateNow;
                $restCode[$r]['dbtodo']   = 'general_ledgers';
                $restCode[$r]['actodo']   = 'is';
                $r++;
                // General Ledger Detail (A/P)
                $this->GeneralLedgerDetail->create();
                $generalLedgerDetail = array();
                $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $generalLedgerId;
                $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id']  = $purchaseReturn['PurchaseReturn']['ap_id'];
                $generalLedgerDetail['GeneralLedgerDetail']['location_group_id'] = $purchaseReturn['PurchaseReturn']['location_group_id'];
                $generalLedgerDetail['GeneralLedgerDetail']['company_id']  = $purchaseReturn['PurchaseReturn']['company_id'];
                $generalLedgerDetail['GeneralLedgerDetail']['branch_id']   = $purchaseReturn['PurchaseReturn']['branch_id'];
                $generalLedgerDetail['GeneralLedgerDetail']['location_id'] = $purchaseReturn['PurchaseReturn']['location_id'];
                $generalLedgerDetail['GeneralLedgerDetail']['type']   = 'Purchase Return';
                $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $purchaseReturn['PurchaseReturn']['balance'];
                $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Purchase Return # ' . $modCode;
                $generalLedgerDetail['GeneralLedgerDetail']['vendor_id'] = $purchaseReturn['PurchaseReturn']['vendor_id'];
                $generalLedgerDetail['GeneralLedgerDetail']['class_id']  = $classId;
                $this->GeneralLedgerDetail->save($generalLedgerDetail);
                // Convert to REST
                $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                $restCode[$r]['dbtodo']   = 'general_ledger_details';
                $restCode[$r]['actodo']   = 'is';
                $r++;

                if (($purchaseReturn['PurchaseReturn']['total_vat']) > 0) {
                    $this->GeneralLedgerDetail->create();
                    $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $purchaseReturn['PurchaseReturn']['vat_chart_account_id'];
                    $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                    $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $purchaseReturn['PurchaseReturn']['total_vat'];
                    $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Purchase Return # ' . $modCode . ' Total VAT';
                    $this->GeneralLedgerDetail->save($generalLedgerDetail);
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                    $restCode[$r]['dbtodo']   = 'general_ledger_details';
                    $restCode[$r]['actodo']   = 'is';
                    $r++;
                }

                for ($i = 0; $i < sizeof($_POST['product']); $i++) {
                    if (!empty($_POST['product_id'][$i])) {
                        $purchaseReturnDetail = array();
                        // Purchase Return Detail
                        $this->PurchaseReturnDetail->create();
                        $purchaseReturnDetail['PurchaseReturnDetail']['sys_code'] = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                        $purchaseReturnDetail['PurchaseReturnDetail']['purchase_return_id'] = $purchaseReturnId;
                        $purchaseReturnDetail['PurchaseReturnDetail']['product_id']   = $_POST['product_id'][$i];
                        $purchaseReturnDetail['PurchaseReturnDetail']['qty']          = $_POST['qty'][$i];
                        $purchaseReturnDetail['PurchaseReturnDetail']['qty_uom_id']   = $_POST['qty_uom_id'][$i];
                        $purchaseReturnDetail['PurchaseReturnDetail']['conversion']   = $_POST['conversion'][$i];
                        $purchaseReturnDetail['PurchaseReturnDetail']['unit_price']   = $_POST['unit_price'][$i];
                        $purchaseReturnDetail['PurchaseReturnDetail']['total_price']  = $_POST['total_price'][$i];
                        $purchaseReturnDetail['PurchaseReturnDetail']['note']         = $_POST['note'][$i];
                        if($_POST['expired_date'][$i] != '' && $_POST['expired_date'][$i] != '0000-00-00'){
                            $dateExp = $this->Helper->dateConvert($_POST['expired_date'][$i]);
                        } else {
                            $dateExp = '0000-00-00';
                        }
                        $purchaseReturnDetail['PurchaseReturnDetail']['expired_date'] = $dateExp;
                        $this->PurchaseReturnDetail->save($purchaseReturnDetail);
                        $purchaseReturnDetailId = $this->PurchaseReturnDetail->id;
                        $qtyOrder      = $_POST['qty'][$i] / ($_POST['small_val_uom'][$i] / $_POST['conversion'][$i]);
                        $qtyOrderSmall = $_POST['qty'][$i] * $_POST['conversion'][$i];
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($purchaseReturnDetail['PurchaseReturnDetail'], 'purchase_return_details');
                        $restCode[$r]['dbtodo']   = 'purchase_return_details';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                        
                        // Update Inventory (Purchase Return)
                        $data = array();
                        $data['module_type']        = 7;
                        $data['purchase_return_id'] = $purchaseReturnId;
                        $data['product_id']         = $_POST['product_id'][$i];
                        $data['location_id']        = $purchaseReturn['PurchaseReturn']['location_id'];
                        $data['location_group_id']  = $purchaseReturn['PurchaseReturn']['location_group_id'];
                        $data['lots_number']  = '0';
                        $data['expired_date'] = $purchaseReturnDetail['PurchaseReturnDetail']['expired_date'];
                        $data['date']         = $purchaseReturn['PurchaseReturn']['order_date'];
                        $data['total_qty']    = $qtyOrderSmall;
                        $data['total_order']  = $qtyOrderSmall;
                        $data['total_free']   = 0;
                        $data['user_id']      = $user['User']['id'];
                        $data['customer_id']  = "";
                        $data['vendor_id']    = $purchaseReturn['PurchaseReturn']['vendor_id'];
                        $data['unit_cost']    = 0;
                        $data['unit_price']   = 0;
                        // Update Invetory Location
                        $this->Inventory->saveInventory($data);
                        // Update Inventory Group
                        $this->Inventory->saveGroupTotalDetail($data);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($data, 'inventories');
                        $restCode[$r]['module_type']  = 7;
                        $restCode[$r]['total_qty']    = $qtyOrderSmall;
                        $restCode[$r]['total_order']  = $qtyOrderSmall;
                        $restCode[$r]['total_free']   = 0;
                        $restCode[$r]['lots_number']  = '0';
                        $restCode[$r]['expired_date'] = $data['expired_date'];
                        $restCode[$r]['customer_id']  = "";
                        $restCode[$r]['unit_cost']    = 0;
                        $restCode[$r]['unit_price']   = 0;
                        $restCode[$r]['vendor_id']    = $this->Helper->getSQLSyncCode("vendors", $purchaseReturn['PurchaseReturn']['vendor_id']);
                        $restCode[$r]['purchase_return_id'] = $this->Helper->getSQLSyncCode("purchase_returns", $purchaseReturnId);
                        $restCode[$r]['product_id']         = $this->Helper->getSQLSyncCode("products", $_POST['product_id'][$i]);
                        $restCode[$r]['location_id']        = $this->Helper->getSQLSyncCode("locations", $purchaseReturn['PurchaseReturn']['location_id']);
                        $restCode[$r]['location_group_id']  = $this->Helper->getSQLSyncCode("location_groups", $purchaseReturn['PurchaseReturn']['location_group_id']);
                        $restCode[$r]['user_id']            = $this->Helper->getSQLSyncCode("users", $user['User']['id']);
                        $restCode[$r]['dbtype']  = 'saveInv,GroupDetail';
                        $restCode[$r]['actodo']  = 'inv';
                        $r++;

                        // Insert Into Receive
                        $billReturnReceive = array();
                        $this->PurchaseReturnReceive->create();
                        $billReturnReceive['PurchaseReturnReceive']['purchase_return_id'] = $purchaseReturnId;
                        $billReturnReceive['PurchaseReturnReceive']['purchase_return_detail_id'] = $purchaseReturnDetailId;
                        $billReturnReceive['PurchaseReturnReceive']['product_id']    = $_POST['product_id'][$i];
                        $billReturnReceive['PurchaseReturnReceive']['qty']           = $_POST['qty'][$i];
                        $billReturnReceive['PurchaseReturnReceive']['qty_uom_id']    = $_POST['qty_uom_id'][$i];
                        $billReturnReceive['PurchaseReturnReceive']['conversion']    = $_POST['conversion'][$i];
                        $billReturnReceive['PurchaseReturnReceive']['lots_number']   = 0;
                        $billReturnReceive['PurchaseReturnReceive']['expired_date']  = $data['expired_date'];
                        $this->PurchaseReturnReceive->save($billReturnReceive);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($billReturnReceive['PurchaseReturnReceive'], 'purchase_return_receives');
                        $restCode[$r]['dbtodo'] = 'purchase_return_receives';
                        $restCode[$r]['actodo'] = 'is';
                        $r++;

                        // Inventory Valuation
                        $inv_valutaion = array();
                        $this->InventoryValuation->create();
                        $inv_valutaion['InventoryValuation']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                        $inv_valutaion['InventoryValuation']['purchase_return_id'] = $purchaseReturnId;
                        $inv_valutaion['InventoryValuation']['company_id'] = $this->data['PurchaseReturn']['company_id'];
                        $inv_valutaion['InventoryValuation']['branch_id']  = $this->data['PurchaseReturn']['branch_id'];
                        $inv_valutaion['InventoryValuation']['type']       = "Purchase Return";
                        $inv_valutaion['InventoryValuation']['date']       = $purchaseReturn['PurchaseReturn']['order_date'];
                        $inv_valutaion['InventoryValuation']['created']    = $dateNow;
                        $inv_valutaion['InventoryValuation']['pid']        = $_POST['product_id'][$i];
                        $inv_valutaion['InventoryValuation']['small_qty']  = "-" . $qtyOrderSmall;
                        $inv_valutaion['InventoryValuation']['qty']   = "-" . $this->Helper->replaceThousand(number_format($qtyOrder, 6));
                        $inv_valutaion['InventoryValuation']['cost']  = null;
                        $inv_valutaion['InventoryValuation']['price'] = $_POST['unit_price'][$i] * ($_POST['small_val_uom'][$i] / $_POST['conversion'][$i]);
                        $inv_valutaion['InventoryValuation']['is_var_cost'] = 1;
                        $this->InventoryValuation->saveAll($inv_valutaion);
                        $inv_valutation_id = $this->InventoryValuation->getLastInsertId();
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($inv_valutaion['InventoryValuation'], 'inventory_valuations');
                        $restCode[$r]['dbtodo']   = 'inventory_valuations';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                        
                        // Update GL for Inventory
                        $this->GeneralLedgerDetail->create();
                        $queryInvAccount = mysql_query("SELECT IFNULL((IFNULL((SELECT chart_account_id FROM accounts WHERE product_id = " . $_POST['product_id'][$i] . " AND account_type_id=1),(SELECT chart_account_id FROM pgroup_accounts WHERE pgroup_id = (SELECT pgroup_id FROM product_pgroups WHERE product_id = " . $_POST['product_id'][$i] . " ORDER BY id  DESC LIMIT 1) AND account_type_id=1))),(SELECT chart_account_id FROM account_types WHERE id=1))");
                        $dataInvAccount = mysql_fetch_array($queryInvAccount);
                        $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $dataInvAccount[0];
                        $generalLedgerDetail['GeneralLedgerDetail']['service_id'] = NULL;
                        $generalLedgerDetail['GeneralLedgerDetail']['product_id'] = $_POST['product_id'][$i];
                        $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_id'] = $inv_valutation_id;
                        $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_is_debit'] = 0;
                        $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                        $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $_POST['total_price'][$i];
                        $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Inventory Purchase Return # ' . $modCode . ' Product # ' . $_POST['product'][$i];
                        $this->GeneralLedgerDetail->save($generalLedgerDetail);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                        $restCode[$r]['dbtodo']   = 'general_ledger_details';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;

                        // Update GL COGS
                        $this->GeneralLedgerDetail->create();
                        $queryCogsAccount = mysql_query("SELECT IFNULL((IFNULL((SELECT chart_account_id FROM accounts WHERE product_id = " . $_POST['product_id'][$i] . " AND account_type_id=2),(SELECT chart_account_id FROM pgroup_accounts WHERE pgroup_id = (SELECT pgroup_id FROM product_pgroups WHERE product_id = " . $_POST['product_id'][$i] . " ORDER BY id  DESC LIMIT 1) AND account_type_id=2))),(SELECT chart_account_id FROM account_types WHERE id=2))");
                        $dataCogsAccount  = mysql_fetch_array($queryCogsAccount);
                        $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $dataCogsAccount[0];
                        $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_id'] = $inv_valutation_id;
                        $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_is_debit'] = 1;
                        $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                        $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                        $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: COGS Purchase Return # ' . $modCode . ' Product # ' . $_POST['product'][$i];
                        $this->GeneralLedgerDetail->save($generalLedgerDetail);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                        $restCode[$r]['dbtodo']   = 'general_ledger_details';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                    } else if (!empty($_POST['service_id'][$i])) {
                        // Sales Order Service
                        $purchaseReturnService = array();
                        $this->PurchaseReturnService->create();
                        $purchaseReturnService['PurchaseReturnService']['purchase_return_id'] = $purchaseReturnId;
                        $purchaseReturnService['PurchaseReturnService']['service_id']  = $_POST['service_id'][$i];
                        $purchaseReturnService['PurchaseReturnService']['qty']         = $_POST['qty'][$i];
                        $purchaseReturnService['PurchaseReturnService']['unit_price']  = $_POST['unit_price'][$i];
                        $purchaseReturnService['PurchaseReturnService']['total_price'] = $_POST['total_price'][$i];
                        $purchaseReturnService['PurchaseReturnService']['note']        = $_POST['note'][$i];
                        $this->PurchaseReturnService->save($purchaseReturnService);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($purchaseReturnService['PurchaseReturnService'], 'purchase_return_services');
                        $restCode[$r]['dbtodo']   = 'purchase_return_services';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;

                        // General Ledger Detail (Service)
                        $this->GeneralLedgerDetail->create();
                        $queryServiceAccount = mysql_query("SELECT IFNULL((SELECT chart_account_id FROM services WHERE id=" . $_POST['service_id'][$i] . "),(SELECT chart_account_id FROM account_types WHERE id=9))");
                        $dataServiceAccount = mysql_fetch_array($queryServiceAccount);
                        $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $dataServiceAccount[0];
                        $generalLedgerDetail['GeneralLedgerDetail']['service_id'] = $_POST['service_id'][$i];
                        $generalLedgerDetail['GeneralLedgerDetail']['product_id'] = NULL;
                        $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_id'] = NULL;
                        $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_is_debit'] = NULL;
                        $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                        $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $_POST['total_price'][$i];
                        $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Purchase Return  # ' . $modCode . ' Service # ' . $_POST['product'][$i];
                        $this->GeneralLedgerDetail->save($generalLedgerDetail);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                        $restCode[$r]['dbtodo']   = 'general_ledger_details';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                    } else {
                        // Sales Order Miscellaneous
                        $purchaseReturnMiscs = array();
                        $this->PurchaseReturnMiscs->create();
                        $purchaseReturnMiscs['PurchaseReturnMiscs']['purchase_return_id'] = $purchaseReturnId;
                        $purchaseReturnMiscs['PurchaseReturnMiscs']['description']  = $_POST['product'][$i];
                        $purchaseReturnMiscs['PurchaseReturnMiscs']['qty_uom_id']   = $_POST['qty_uom_id'][$i];
                        $purchaseReturnMiscs['PurchaseReturnMiscs']['qty']          = $_POST['qty'][$i];
                        $purchaseReturnMiscs['PurchaseReturnMiscs']['unit_price']   = $_POST['unit_price'][$i];
                        $purchaseReturnMiscs['PurchaseReturnMiscs']['total_price']  = $_POST['total_price'][$i];
                        $purchaseReturnMiscs['PurchaseReturnMiscs']['note']         = $_POST['note'][$i];
                        $this->PurchaseReturnMiscs->save($purchaseReturnMiscs);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($purchaseReturnMiscs['PurchaseReturnMiscs'], 'purchase_return_miscs');
                        $restCode[$r]['dbtodo']   = 'purchase_return_miscs';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;

                        // General Ledger Detail (Misc)
                        $this->GeneralLedgerDetail->create();
                        $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id']  = $purchaseMiscAccount['AccountType']['chart_account_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['service_id'] = NULL;
                        $generalLedgerDetail['GeneralLedgerDetail']['product_id'] = NULL;
                        $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_id'] = NULL;
                        $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_is_debit'] = NULL;
                        $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                        $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $_POST['total_price'][$i];
                        $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Purchase Return  # ' . $modCode . ' Misc # ' . $_POST['product'][$i];
                        $this->GeneralLedgerDetail->save($generalLedgerDetail);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                        $restCode[$r]['dbtodo']   = 'general_ledger_details';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                    }
                }
                // Save File Send
                $this->Helper->sendFileToSync($restCode, 0, 0);
                // Recalculate Average Cost
                mysql_query("UPDATE tracks SET val='".$this->data['PurchaseReturn']['order_date']."', is_recalculate = 1 WHERE id=1");
                $this->Helper->saveUserActivity($user['User']['id'], 'Bill Return', 'Save Add New', $purchaseReturnId);
                echo json_encode($result);
                exit;
            } else{
                $this->Helper->saveUserActivity($user['User']['id'], 'Bill Return', 'Save Add New (Error)');
                $result['code'] = 2;
                echo json_encode($result);
                exit;
            }
        }
        $this->Helper->saveUserActivity($user['User']['id'], 'Bill Return', 'Add New');
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
                            'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id', 'ModuleCodeBranch.br_code', 'Branch.currency_center_id', 'CurrencyCenter.symbol'),
                            'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])
                        ));
        // Get Loction Setting
        $locSetting = ClassRegistry::init('LocationSetting')->findById(2);
        $locCon     = '';
        if($locSetting['LocationSetting']['location_status'] == 1){
            $locCon = ' AND Location.is_for_sale = 0';
        }
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))),'conditions' => array('user_location_groups.user_id=' . $user['User']['id'], 'LocationGroup.is_active' => '1', 'LocationGroup.location_group_type_id != 1')));
        $locations = ClassRegistry::init('Location')->find('all', array('joins' => array(array('table' => 'user_locations', 'type' => 'inner', 'conditions' => array('user_locations.location_id=Location.id'))), 'conditions' => array('user_locations.user_id=' . $user['User']['id'] . ' AND Location.is_active=1'.$locCon), 'order' => 'Location.name'));
        $this->set(compact("locationGroups", "locations", "apAccountId", "companies", "branches"));
    }

    function orderDetails() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $branches = ClassRegistry::init('Branch')->find('all',
                        array(
                            'joins' => array(
                                array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id')),
                                array('table' => 'module_code_branches AS ModuleCodeBranch', 'type' => 'left', 'conditions' => array('ModuleCodeBranch.branch_id=Branch.id'))
                            ),
                            'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id', 'ModuleCodeBranch.br_code', 'Branch.currency_center_id', 'CurrencyCenter.symbol'),
                            'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])
                        ));
        $uoms = ClassRegistry::init('Uom')->find('all', array('fields' => array('Uom.id', 'Uom.name'), 'conditions' => array('Uom.is_active' => 1)));
        $this->set(compact('branches', 'uoms'));
    }

    function miscellaneous() {
        $this->layout = 'ajax';
    }

    function discount() {
        $this->layout = 'ajax';
        $discounts = ClassRegistry::init('Discount')->find("all", array('conditions' => array('Discount.is_active' => 1), 'order' => array('id DESC')));
        $this->set(compact('discounts'));
    }

    function product($companyId, $branchId = null, $locationId = null, $orderDate = null, $brId = null) {
        $this->layout = 'ajax';
        $this->set(compact('companyId', 'branchId', 'locationId', 'orderDate', 'brId'));
    }

    function product_ajax($companyId, $branchId = null, $locationId = null, $orderDate = null, $category = null) {
        $this->layout = 'ajax';
        $brId = $_GET['br_id'];
        $this->set(compact('companyId', 'branchId', 'locationId', 'orderDate', 'brId', 'category'));
    }

    function aging($id = null) {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $r = 0;
            $restCode = array();
            $dateNow  = date("Y-m-d H:i:s");
            $this->loadModel('PurchaseReturnReceipt');
            $cashBankAccount = ClassRegistry::init('AccountType')->findById(6);
            $cashBankAccountId = $cashBankAccount['AccountType']['chart_account_id'];
            $result = array();
            $purchaseR = array();
            $purchaseR['PurchaseReturn']['id'] = $this->data['PurchaseReturn']['id'];
            $purchaseR['PurchaseReturn']['modified']    = $dateNow;
            $purchaseR['PurchaseReturn']['modified_by'] = $user['User']['id'];
            $purchaseR['PurchaseReturn']['balance'] = $this->data['PurchaseReturn']['balance_us'];
            if ($this->PurchaseReturn->save($purchaseR)) {
                $purchaseReturn  = $this->PurchaseReturn->findById($this->data['PurchaseReturn']['id']);
                // Convert to REST
                $restCode[$r]['balance']     = $this->data['PurchaseReturn']['balance_us'];
                $restCode[$r]['modified']    = $dateNow;
                $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
                $restCode[$r]['dbtodo'] = 'purchase_returns';
                $restCode[$r]['actodo'] = 'ut';
                $restCode[$r]['con']    = "sys_code = '".$purchaseReturn['PurchaseReturn']['sys_code']."'";
                $r++;
                $lastExchangeRate = ClassRegistry::init('ExchangeRate')->find("first", array("conditions" => array(
                                "ExchangeRate.branch_id" => $purchaseReturn['PurchaseReturn']['branch_id'],
                                "ExchangeRate.currency_center_id" => $this->data['PurchaseReturn']['currency_center_id']), "order" => array("ExchangeRate.created desc")));
                if(!empty($lastExchangeRate) && $lastExchangeRate['ExchangeRate']['rate_to_sell'] > 0){
                    $exchangeRateId = $lastExchangeRate['ExchangeRate']['id'];
                    $totalPaidOther = ($this->data['PurchaseReturn']['amount_other'] / $lastExchangeRate['ExchangeRate']['rate_to_sell']);
                } else {
                    $exchangeRateId = 0;
                    $totalPaidOther = 0;
                }
                // Purchase Return Receipt
                $purchaseReturnReceipt = array();
                $this->PurchaseReturnReceipt->create();
                $purchaseReturnReceipt['PurchaseReturnReceipt']['sys_code'] = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $purchaseReturnReceipt['PurchaseReturnReceipt']['purchase_return_id'] = $this->data['PurchaseReturn']['id'];
                $purchaseReturnReceipt['PurchaseReturnReceipt']['branch_id']          = $purchaseReturn['PurchaseReturn']['branch_id'];
                $purchaseReturnReceipt['PurchaseReturnReceipt']['exchange_rate_id']   = $exchangeRateId;
                $purchaseReturnReceipt['PurchaseReturnReceipt']['currency_center_id'] = $this->data['PurchaseReturn']['currency_center_id'];
                $purchaseReturnReceipt['PurchaseReturnReceipt']['chart_account_id']   = $cashBankAccountId;
                $purchaseReturnReceipt['PurchaseReturnReceipt']['receipt_code'] = '';
                $purchaseReturnReceipt['PurchaseReturnReceipt']['amount_us'] = $this->data['PurchaseReturn']['amount_us'];
                $purchaseReturnReceipt['PurchaseReturnReceipt']['amount_other'] = $this->data['PurchaseReturn']['amount_other'];
                $purchaseReturnReceipt['PurchaseReturnReceipt']['total_amount'] = $this->data['PurchaseReturn']['total_amount'];
                $purchaseReturnReceipt['PurchaseReturnReceipt']['balance'] = $this->data['PurchaseReturn']['balance_us'];
                $purchaseReturnReceipt['PurchaseReturnReceipt']['balance_other'] = $this->data['PurchaseReturn']['balance_other'];
                $purchaseReturnReceipt['PurchaseReturnReceipt']['created_by'] = $user['User']['id'];
                $purchaseReturnReceipt['PurchaseReturnReceipt']['pay_date'] = $this->data['PurchaseReturn']['pay_date']!=''?$this->data['PurchaseReturn']['pay_date']:'0000-00-00';
                
                $this->loadModel('GeneralLedger');
                $this->loadModel('GeneralLedgerDetail');
                $this->loadModel('Company');
                $generalLedger   = $this->GeneralLedger->find("first", array("conditions" => array("GeneralLedger.purchase_return_id" => $purchaseReturn['PurchaseReturn']['id'])));
                $company         = $this->Company->read(null, $purchaseReturn['PurchaseReturn']['company_id']);
                $classId         = $this->Helper->getClassId($company['Company']['id'], $company['Company']['classes'], $purchaseReturn['PurchaseReturn']['location_group_id']);
                $totalPaid = $this->data['PurchaseReturn']['amount_us'] + $totalPaidOther;
                if ($this->data['PurchaseReturn']['balance_us'] > 0) {
                    $purchaseReturnReceipt['PurchaseReturnReceipt']['due_date'] = $this->data['PurchaseReturn']['aging']!=''?$this->data['PurchaseReturn']['aging']:'0000-00-00';
                }
                $this->PurchaseReturnReceipt->save($purchaseReturnReceipt);
                $result['sr_id'] = $this->PurchaseReturnReceipt->id;
                // Update Code & Change SO Generate Code
                $modComCode = ClassRegistry::init('ModuleCodeBranch')->find('first', array('conditions' => array("ModuleCodeBranch.branch_id" => $purchaseReturn['PurchaseReturn']['branch_id'])));
                $repCode    = date("y").$modComCode['ModuleCodeBranch']['br_rep_code'];
                // Get Module Code
                $modCode    = $this->Helper->getModuleCode($repCode, $result['sr_id'], 'receipt_code', 'purchase_return_receipts', 'is_void = 0 AND branch_id = '.$purchaseReturn['PurchaseReturn']['branch_id']);
                // Updaet Module Code
                mysql_query("UPDATE purchase_return_receipts SET receipt_code = '".$modCode."' WHERE id = ".$result['sr_id']);
                // Convert to REST
                $restCode[$r] = $this->Helper->convertToDataSync($purchaseReturnReceipt['PurchaseReturnReceipt'], 'purchase_return_receipts');
                $restCode[$r]['receipt_code']  = $modCode;
                $restCode[$r]['modified'] = $dateNow;
                $restCode[$r]['dbtodo']   = 'purchase_return_receipts';
                $restCode[$r]['actodo']   = 'is';
                $r++;
                if (($this->data['PurchaseReturn']['total_amount'] - $this->data['PurchaseReturn']['balance_us']) > 0) {
                    // Save General Ledger Detail
                    $this->GeneralLedger->create();
                    $generalLedger = array();
                    $generalLedger['GeneralLedger']['sys_code']           = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                    $generalLedger['GeneralLedger']['purchase_return_id'] = $this->data['PurchaseReturn']['id'];
                    $generalLedger['GeneralLedger']['purchase_return_receipt_id'] = $result['sr_id'];
                    $generalLedger['GeneralLedger']['date'] = $this->data['PurchaseReturn']['pay_date']!=''?$this->data['PurchaseReturn']['pay_date']:'0000-00-00';
                    $generalLedger['GeneralLedger']['reference'] = $purchaseReturn['PurchaseReturn']['pr_code'];
                    $generalLedger['GeneralLedger']['created_by'] = $user['User']['id'];
                    $generalLedger['GeneralLedger']['is_sys'] = 1;
                    $generalLedger['GeneralLedger']['is_adj'] = 0;
                    $generalLedger['GeneralLedger']['is_active'] = 1;
                    if ($this->GeneralLedger->save($generalLedger)) {
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($generalLedger['GeneralLedger'], 'general_ledgers');
                        $restCode[$r]['modified'] = $dateNow;
                        $restCode[$r]['dbtodo']   = 'general_ledgers';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                        $this->GeneralLedgerDetail->create();
                        $generalLedgerDetail = array();
                        $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $this->GeneralLedger->id;
                        $generalLedgerDetail['GeneralLedgerDetail']['company_id'] = $purchaseReturn['PurchaseReturn']['company_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['branch_id']  = $purchaseReturn['PurchaseReturn']['branch_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $cashBankAccountId;
                        $generalLedgerDetail['GeneralLedgerDetail']['location_group_id'] = $purchaseReturn['PurchaseReturn']['location_group_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['location_id'] = $purchaseReturn['PurchaseReturn']['location_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['type'] = 'Bill Return Payment';
                        $generalLedgerDetail['GeneralLedgerDetail']['debit'] = $totalPaid;
                        $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                        $generalLedgerDetail['GeneralLedgerDetail']['memo'] = 'ICS: Bill Return # ' . $purchaseReturn['PurchaseReturn']['pr_code'];
                        $generalLedgerDetail['GeneralLedgerDetail']['vendor_id'] = $purchaseReturn['PurchaseReturn']['vendor_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['class_id'] = $classId;
                        $this->GeneralLedgerDetail->save($generalLedgerDetail);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                        $restCode[$r]['dbtodo']   = 'general_ledger_details';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;

                        $this->GeneralLedgerDetail->create();
                        $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $purchaseReturn['PurchaseReturn']['ap_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['debit'] = 0;
                        $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $totalPaid;
                        $this->GeneralLedgerDetail->save($generalLedgerDetail);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                        $restCode[$r]['dbtodo']   = 'general_ledger_details';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                    }
                }
                // Save File Send
                $this->Helper->sendFileToSync($restCode, 0, 0);
                $this->Helper->saveUserActivity($user['User']['id'], 'Bill Return Payment', 'Save Add New', $result['sr_id']);
                echo json_encode($result);
                exit;
            } else {
                $this->Helper->saveUserActivity($user['User']['id'], 'Bill Return Payment', 'Save Add New (Error)');
                $result['sr_id'] = 0;
                echo json_encode($result);
                exit;
            }
        }
        if (!empty($id)) {
            $this->data = $this->PurchaseReturn->read(null, $id);
            $purchaseReturn = ClassRegistry::init('PurchaseReturn')->find("first", array('conditions' => array('PurchaseReturn.id' => $id)));
            if (!empty($purchaseReturn)) {
                $this->Helper->saveUserActivity($user['User']['id'], 'Bill Return Payment', 'Add New');
                $pbcWPos = ClassRegistry::init('InvoicePbcWithPb')->find("all", array('conditions' => array('InvoicePbcWithPb.purchase_return_id' => $id, 'InvoicePbcWithPb.status>0')));
                $purchaseReturnDetails = ClassRegistry::init('PurchaseReturnDetail')->find("all", array('conditions' => array('PurchaseReturnDetail.purchase_return_id' => $id)));
                $purchaseReturnServices = ClassRegistry::init('PurchaseReturnService')->find("all", array('conditions' => array('PurchaseReturnService.purchase_return_id' => $id)));
                $purchaseReturnMiscs = ClassRegistry::init('PurchaseReturnMisc')->find("all", array('conditions' => array('PurchaseReturnMisc.purchase_return_id' => $id)));
                $purchaseReturnReceipts = ClassRegistry::init('PurchaseReturnReceipt')->find("all", array('conditions' => array('PurchaseReturnReceipt.purchase_return_id' => $id, 'PurchaseReturnReceipt.is_void' => 0)));
                $this->set(compact('purchaseReturn', 'purchaseReturnDetails', 'purchaseReturnServices', 'purchaseReturnMiscs', 'purchaseReturnReceipts', 'pbcWPos'));
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
            $purchaseReturn = ClassRegistry::init('PurchaseReturn')->find("first", array('conditions' => array('PurchaseReturn.id' => $id)));
            if (!empty($purchaseReturn)) {
                $pbcPbs = ClassRegistry::init('InvoicePbcWithPb')->find("all", array('conditions' => array('InvoicePbcWithPb.purchase_return_id' => $id, 'InvoicePbcWithPb.status>0'),
                    'group' => 'InvoicePbcWithPb.purchase_order_id',
                    'fields' => array('sum(InvoicePbcWithPb.total_cost) as total_cost', 'InvoicePbcWithPb.*', 'PurchaseOrder.*')
                ));
                $purchaseReturnDetails = ClassRegistry::init('PurchaseReturnDetail')->find("all", array('conditions' => array('PurchaseReturnDetail.purchase_return_id' => $id)));
                $purchaseReturnMiscs = ClassRegistry::init('PurchaseReturnMisc')->find("all", array('conditions' => array('PurchaseReturnMisc.purchase_return_id' => $id)));
                $purchaseReturnServices = ClassRegistry::init('PurchaseReturnService')->find("all", array('conditions' => array('PurchaseReturnService.purchase_return_id' => $id)));

                $location = ClassRegistry::init('Location')->find("first", array("conditions" => array("Location.id" => $purchaseReturn['PurchaseReturn']['location_id'], "Location.is_active" => "1")));
                $this->set(compact('purchaseReturn', 'purchaseReturnDetails', 'purchaseReturnMiscs', 'purchaseReturnServices', "location", "pbcPbs"));
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
            $sr = ClassRegistry::init('PurchaseReturnReceipt')->find("first", array('conditions' => array('PurchaseReturnReceipt.id' => $receiptId, 'PurchaseReturnReceipt.is_void' => 0)));

            $purchaseReturn = ClassRegistry::init('PurchaseReturn')->find("first", array('conditions' => array('PurchaseReturn.id' => $sr['PurchaseReturn']['id'])));
            if (!empty($purchaseReturn)) {
                $lastExchangeRate = ClassRegistry::init('ExchangeRate')->find("first", array(
                    "conditions" => array("ExchangeRate.is_active" => 1),
                    "order" => array("ExchangeRate.created desc")
                        )
                );
                $location = ClassRegistry::init('Location')->find("first", array("conditions" => array("Location.id" => $purchaseReturn['PurchaseReturn']['location_id'], "Location.is_active" => "1")));
                $purchaseReturnDetails = ClassRegistry::init('PurchaseReturnDetail')->find("all", array('conditions' => array('PurchaseReturnDetail.purchase_return_id' => $sr['PurchaseReturn']['id'])));
                $purchaseReturnServices = ClassRegistry::init('PurchaseReturnService')->find("all", array('conditions' => array('PurchaseReturnService.purchase_return_id' => $sr['PurchaseReturn']['id'])));
                $purchaseReturnMiscs = ClassRegistry::init('PurchaseReturnMisc')->find("all", array('conditions' => array('PurchaseReturnMisc.purchase_return_id' => $sr['PurchaseReturn']['id'])));
                $purchaseReturnReceipts = ClassRegistry::init('PurchaseReturnReceipt')->find("all", array('conditions' => array('PurchaseReturnReceipt.purchase_return_id' => $sr['PurchaseReturn']['id'], 'PurchaseReturnReceipt.is_void' => 0)));

                $this->set(compact('purchaseReturn', 'purchaseReturnDetails', 'purchaseReturnMiscs', 'purchaseReturnServices', 'purchaseReturnReceipts', 'sr', 'lastExchangeRate', 'location'));
            } else {
                exit;
            }
        } else {
            exit;
        }
    }

    function printReceiptCurrent($receiptId = null) {
        if (!empty($receiptId)) {
            $this->layout = 'ajax';
            $sr = ClassRegistry::init('PurchaseReturnReceipt')->find("first", array('conditions' => array('PurchaseReturnReceipt.id' => $receiptId, 'PurchaseReturnReceipt.is_void' => 0)));

            $purchaseReturn = ClassRegistry::init('PurchaseReturn')->find("first", array('conditions' => array('PurchaseReturn.id' => $sr['PurchaseReturn']['id'])));
            if (!empty($purchaseReturn)) {
                $lastExchangeRate = ClassRegistry::init('ExchangeRate')->find("first", array(
                    "conditions" => array("ExchangeRate.is_active" => 1),
                    "order" => array("ExchangeRate.created desc")
                        )
                );
                $location = ClassRegistry::init('Location')->find("first", array("conditions" => array("Location.id" => $purchaseReturn['PurchaseReturn']['location_id'], "Location.is_active" => "1")));
                $purchaseReturnDetails = ClassRegistry::init('PurchaseReturnDetail')->find("all", array('conditions' => array('PurchaseReturnDetail.purchase_return_id' => $sr['PurchaseReturn']['id'])));
                $purchaseReturnServices = ClassRegistry::init('PurchaseReturnService')->find("all", array('conditions' => array('PurchaseReturnService.purchase_return_id' => $sr['PurchaseReturn']['id'])));
                $purchaseReturnMiscs = ClassRegistry::init('PurchaseReturnMisc')->find("all", array('conditions' => array('PurchaseReturnMisc.purchase_return_id' => $sr['PurchaseReturn']['id'])));
                $purchaseReturnReceipts = ClassRegistry::init('PurchaseReturnReceipt')->find("all", array('conditions' => array('PurchaseReturnReceipt.id <= ' . $receiptId, 'PurchaseReturnReceipt.purchase_return_id' => $sr['PurchaseReturn']['id'], 'PurchaseReturnReceipt.is_void' => 0)));

                $this->set(compact('purchaseReturn', 'purchaseReturnDetails', 'purchaseReturnMiscs', 'purchaseReturnServices', 'purchaseReturnReceipts', 'sr', 'lastExchangeRate', 'location'));
            } else {
                exit;
            }
        } else {
            exit;
        }
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

    function invoice($companyId, $branchId, $chartAccountId, $vendorId, $balance, $prId) {
        $this->layout = 'ajax';
        $this->set('companyId', $companyId);
        $this->set('branchId', $branchId);
        $this->set('chartAccountId', $chartAccountId);
        $this->set('vendorId', $vendorId);
        $this->set('balance', $this->Helper->replaceThousand($balance));
        $this->data = $this->PurchaseReturn->read(null, $prId);
    }

    function invoiceAjax($companyId, $branchId, $chartAccountId, $vendorId, $cg = null) {
        $this->layout = 'ajax';
        $this->set('companyId', $companyId);
        $this->set('branchId', $branchId);
        $this->set('chartAccountId', $chartAccountId);
        $this->set('vendorId', $vendorId);
        $this->set('cg', $cg);
    }

    function applyToInvoice($prId, $prBalance, $date) {
        $this->layout = 'ajax';
        $this->loadModel('InvoicePbcWithPb');
        $this->loadModel('GeneralLedger');
        $this->loadModel('GeneralLedgerDetail');
        $user = $this->getCurrentUser();
        $purchase_return = $this->PurchaseReturn->read(null, $prId);
        $PurchaseReturn = array();
        $PurchaseReturn['PurchaseReturn']['id'] = $prId;
        $total_amount_invoice = 0;
        if (!empty($_POST['purchase_order'])) {
            $r = 0;
            $restCode = array();
            $dateNow  = date("Y-m-d H:i:s");
            for ($i = 0; $i < sizeOf($_POST['purchase_order']); $i++) {
                if ($_POST['purchase_order'][$i] != "" && $_POST['purchase_order'][$i] > 0) {                    
                    $prWPo = array();
                    $saleBalance = 0;
                    $queryInvoice = mysql_query("SELECT balance, sys_code, po_code, ap_id FROM purchase_orders WHERE id=" . $_POST['purchase_order'][$i]);
                    $dataInvoice = mysql_fetch_array($queryInvoice);
                    if ($prBalance > 0) {
                        $saleBalance = $dataInvoice['balance'] - $_POST['invoice_price_pbc'][$i];
                        mysql_query("UPDATE purchase_orders SET balance = " . $saleBalance . " WHERE id=" . $_POST['purchase_order'][$i]);
                        // Convert to REST
                        $restCode[$r]['balance'] = $saleBalance;
                        $restCode[$r]['dbtodo']  = 'purchase_orders';
                        $restCode[$r]['actodo']  = 'ut';
                        $restCode[$r]['con']     = "sys_code = '".$dataInvoice['sys_code']."'";
                        $r++;
                        $this->InvoicePbcWithPb->create();
                        $prWPo['InvoicePbcWithPb']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                        $prWPo['InvoicePbcWithPb']['purchase_return_id'] = $prId;
                        $prWPo['InvoicePbcWithPb']['purchase_order_id'] = $_POST['purchase_order'][$i];
                        $prWPo['InvoicePbcWithPb']['total_cost'] = $_POST['invoice_price_pbc'][$i];
                        $prWPo['InvoicePbcWithPb']['apply_date'] = $date;
                        $prWPo['InvoicePbcWithPb']['created']    = $dateNow;
                        $prWPo['InvoicePbcWithPb']['created_by'] = $user['User']['id'];
                        $prWPo['InvoicePbcWithPb']['status'] = 1;
                        $this->InvoicePbcWithPb->save($prWPo);
                        $brPbId = $this->InvoicePbcWithPb->id;
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($prWPo['InvoicePbcWithPb'], 'invoice_pbc_with_pbs');
                        $restCode[$r]['modified'] = $dateNow;
                        $restCode[$r]['dbtodo']   = 'invoice_pbc_with_pbs';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                        // Save General Ledger Detail
                        $this->GeneralLedger->create();
                        $generalLedger = array();
                        $generalLedger['GeneralLedger']['sys_code']  = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                        $generalLedger['GeneralLedger']['invoice_pbc_with_pbs_id'] = $brPbId;
                        $generalLedger['GeneralLedger']['purchase_return_id'] = $prId;
                        $generalLedger['GeneralLedger']['purchase_order_id']  = NULL;
                        $generalLedger['GeneralLedger']['date']       = $date;
                        $generalLedger['GeneralLedger']['reference']  = $purchase_return['PurchaseReturn']['pr_code'];
                        $generalLedger['GeneralLedger']['created_by'] = $user['User']['id'];
                        $generalLedger['GeneralLedger']['is_sys'] = 1;
                        $generalLedger['GeneralLedger']['is_adj'] = 0;
                        $generalLedger['GeneralLedger']['is_active'] = 1;
                        if ($this->GeneralLedger->save($generalLedger)) {
                            $glId = $this->GeneralLedger->id;
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedger['GeneralLedger'], 'general_ledgers');
                            $restCode[$r]['modified'] = $dateNow;
                            $restCode[$r]['dbtodo']   = 'general_ledgers';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                            $this->GeneralLedgerDetail->create();
                            $generalLedgerDetail = array();
                            $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $glId;
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id']  = $purchase_return['PurchaseReturn']['ap_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['company_id']        = $purchase_return['PurchaseReturn']['company_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['branch_id']         = $purchase_return['PurchaseReturn']['branch_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['type']   = 'Apply Purchase Bill';
                            $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $_POST['invoice_price_pbc'][$i];
                            $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Apply PR # '.$purchase_return['PurchaseReturn']['pr_code'].' and PB # ' . $dataInvoice['po_code'];
                            $generalLedgerDetail['GeneralLedgerDetail']['vendor_id'] = $purchase_return['PurchaseReturn']['vendor_id'];
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                        }
                        // Save General Ledger Detail
                        $this->GeneralLedger->create();
                        $generalLedger['GeneralLedger']['sys_code']  = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                        $generalLedger['GeneralLedger']['purchase_order_id']  = $_POST['purchase_order'][$i];
                        $generalLedger['GeneralLedger']['purchase_return_id'] = NULL;
                        $generalLedger['GeneralLedger']['reference']  = $dataInvoice['po_code'];
                        if ($this->GeneralLedger->save($generalLedger)) {
                            $glId = $this->GeneralLedger->id;
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedger['GeneralLedger'], 'general_ledgers');
                            $restCode[$r]['modified'] = $dateNow;
                            $restCode[$r]['dbtodo']   = 'general_ledgers';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                            $this->GeneralLedgerDetail->create();
                            $generalLedgerDetail = array();
                            $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $glId;
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id']  = $dataInvoice['ap_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['company_id']        = $purchase_return['PurchaseReturn']['company_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['branch_id']         = $purchase_return['PurchaseReturn']['branch_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['type']   = 'Apply Purchase Return';
                            $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $_POST['invoice_price_pbc'][$i];
                            $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Apply PR # '.$purchase_return['PurchaseReturn']['pr_code'].' and PB # ' . $dataInvoice['po_code'];
                            $generalLedgerDetail['GeneralLedgerDetail']['vendor_id'] = $purchase_return['PurchaseReturn']['vendor_id'];
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                        }
                        $this->Helper->saveUserActivity($user['User']['id'], 'Bill Return Receipt', 'Save Apply to PB', $this->InvoicePbcWithPb->id);
                        $total_amount_invoice += $_POST['invoice_price_pbc'][$i];
                    }
                }
            }
        }
        $PurchaseReturn['PurchaseReturn']['balance'] = $prBalance - $total_amount_invoice;
        $PurchaseReturn['PurchaseReturn']['total_amount_po'] = ($purchase_return['PurchaseReturn']['total_amount_po'] < 0 ? 0 : $purchase_return['PurchaseReturn']['total_amount_po'] + $total_amount_invoice);
        $PurchaseReturn['PurchaseReturn']['modified']    = $dateNow;
        $PurchaseReturn['PurchaseReturn']['modified_by'] = $user['User']['id'];
        $this->PurchaseReturn->save($PurchaseReturn);
        // Convert to REST
        $restCode[$r]['balance']  = $prBalance - $total_amount_invoice;
        $restCode[$r]['total_amount_invoice'] = $PurchaseReturn['PurchaseReturn']['total_amount_po'];
        $restCode[$r]['modified']    = $dateNow;
        $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
        $restCode[$r]['dbtodo'] = 'purchase_returns';
        $restCode[$r]['actodo'] = 'ut';
        $restCode[$r]['con']    = "sys_code = '".$purchase_return['PurchaseReturn']['sys_code']."'";
        // Save File Send
        $this->Helper->sendFileToSync($restCode, 0, 0);
        exit();
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

    function searchProductByCode($company_id, $branch_id, $brId = null) {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $product_code = !empty($this->data['code']) ? $this->data['code'] : "";
        $order_date   = $this->data['order_date'];
        $location_id  = $this->data['location_id'];
        $expiryDate   = $this->data['expiry_date']!=''?$this->data['expiry_date']:'0000-00-00';
        $dateNow   = date("Y-m-d");
        $tableName = $location_id."_inventory_totals";
        if(strtotime($order_date) < strtotime($dateNow) ){
            $sumEnding = "(StockDaily.total_cycle + StockDaily.total_cm + StockDaily.total_pb + StockDaily.total_to_in) - (StockDaily.total_so + StockDaily.total_pos + StockDaily.total_pbc + StockDaily.total_to_out)";
            $joinStockDaily = array(
                                'table' => $location_id."_inventory_total_details",
                                'type' => 'INNER',
                                'alias' => 'StockDaily',
                                'conditions' => array(
                                    'StockDaily.product_id = Product.id',
                                    "StockDaily.date <= '".$order_date."'",
                                    "StockDaily.expired_date = '".$expiryDate."'"
                                ));
            $joinInventory  = "";
            $inventoryField = "SUM(IFNULL(".$sumEnding.",0)) AS total_qty";
            $groupBy        = "Product.id";
        }else{
            $joinInventory  = array(
                                 'table' => $tableName,
                                 'type' => 'INNER',
                                 'alias' => 'InventoryTotal',
                                 'conditions' => array(
                                     'InventoryTotal.product_id = Product.id',
                                     "InventoryTotal.expired_date = '".$expiryDate."'"
                                 ));
            $joinStockDaily = "";
            $inventoryField = "SUM(InventoryTotal.total_qty - InventoryTotal.total_order) AS total_qty";
            $groupBy        = "Product.id";
        }
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
                                 'ProductBranch.branch_id = '.$branch_id
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
            $joinInventory,
            $joinStockDaily,
            $joinProductgroup,
            $joinPgroup,
            $joinProductBranch
        );

        $product = ClassRegistry::init('Product')->find('first', array(
                    'fields' => array(
                        'Product.id',
                        'Product.name',
                        'Product.code',
                        'Product.description',
                        'Product.small_val_uom',
                        'Product.default_cost',
                        'Product.price_uom_id',
                        'Product.unit_cost',
                        'Product.is_expired_date',
                        $inventoryField,
                    ),
                    'conditions' => array(
                        array(
                            "OR" => array(
                                'trim(Product.code)' => trim($product_code),
                                'trim(Product.barcode)' => trim($product_code)
                            )
                        ),
                        'Product.company_id' => $company_id,
                        'Product.is_active' => 1,
                        'Product.is_packet' => 0,
                        'Product.price_uom_id > 0',
                        'Product.small_val_uom > 0'
                    ),
                    'joins' => $joins,
                    'group' => $groupBy
                ));
        $this->set(compact('product', 'order_date', 'location_id', 'brId', 'expiryDate'));
    }

    function edit($id = null) {
        $this->layout = 'ajax';
        if (!$id && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }

        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $product_return = $this->PurchaseReturn->read(null, $this->data['id']);
            if ($product_return['PurchaseReturn']['status'] == 2) {
                $r  = 0;
                $rb = 0;
                $restCode = array();
                $restBackCode  = array();
                $dateNow  = date("Y-m-d H:i:s");
                $this->loadModel('PurchaseReturnDetail');
                $this->loadModel('PurchaseReturnMiscs');
                $this->loadModel('PurchaseReturnService');
                $this->loadModel('PurchaseReturnReceipt');
                $this->loadModel('GeneralLedger');
                $this->loadModel('GeneralLedgerDetail');
                $this->loadModel('AccountType');
                $this->loadModel('InventoryValuation');
                $this->loadModel('Company');
                $this->loadModel('PurchaseReturnReceive');
                
                $statuEdit = "-1";
                $result = array();
                if($this->data['PurchaseReturn']['company_id'] != $product_return['PurchaseReturn']['company_id']){
                    $statuEdit = 0;
                }
                $this->PurchaseReturn->updateAll(
                        array('PurchaseReturn.status' => $statuEdit, 'PurchaseReturn.modified_by' => $user['User']['id']), array('PurchaseReturn.id' => $this->data['id'])
                );
                // Convert to REST
                $restBackCode[$rb]['status']   = $statuEdit;
                $restBackCode[$rb]['modified'] = $dateNow;
                $restBackCode[$rb]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
                $restBackCode[$rb]['dbtodo'] = 'purchase_returns';
                $restBackCode[$rb]['actodo'] = 'ut';
                $restBackCode[$rb]['con']    = "sys_code = '".$product_return['PurchaseReturn']['sys_code']."'";
                $rb++;
                $this->GeneralLedger->updateAll(
                        array('GeneralLedger.is_active' => 2, 'GeneralLedger.modified_by' => $user['User']['id']), array('GeneralLedger.purchase_return_id' => $this->data['id'])
                );
                // Convert to REST
                $restBackCode[$rb]['is_active'] = 2;
                $restBackCode[$rb]['modified']  = $dateNow;
                $restBackCode[$rb]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
                $restBackCode[$rb]['dbtodo'] = 'general_ledgers';
                $restBackCode[$rb]['actodo'] = 'ut';
                $restBackCode[$rb]['con']    = "purchase_return_id = (SELECT id FROM purchase_returns WHERE sys_code = '".$product_return['PurchaseReturn']['sys_code']."' LIMIT 1)";
                $rb++;
                $this->InventoryValuation->updateAll(
                        array('InventoryValuation.is_active' => "2"), array('InventoryValuation.purchase_return_id' => $this->data['id'])
                );
                // Convert to REST
                $restBackCode[$rb]['is_active'] = 2;
                $restBackCode[$rb]['dbtodo'] = 'inventory_valuations';
                $restBackCode[$rb]['actodo'] = 'ut';
                $restBackCode[$rb]['con']    = "purchase_return_id = (SELECT id FROM purchase_returns WHERE sys_code = '".$product_return['PurchaseReturn']['sys_code']."' LIMIT 1)";
                $rb++;
                
                $purchaseReturnDetails = ClassRegistry::init('PurchaseReturnDetail')->find("all", array('conditions' => array('PurchaseReturnDetail.purchase_return_id' => $this->data['id'])));
                foreach($purchaseReturnDetails AS $purchaseReturnDetail){
                    $qtyOrderSmall = ($purchaseReturnDetail['PurchaseReturnDetail']['qty'] * $purchaseReturnDetail['PurchaseReturnDetail']['conversion']);
                    // Update Inventory (Purchase Return)
                    $data = array();
                    $data['module_type']        = 20;
                    $data['purchase_return_id'] = $this->data['id'];
                    $data['product_id']         = $purchaseReturnDetail['PurchaseReturnDetail']['product_id'];
                    $data['location_id']        = $product_return['PurchaseReturn']['location_id'];
                    $data['location_group_id']  = $product_return['PurchaseReturn']['location_group_id'];
                    $data['lots_number']  = '0';
                    $data['expired_date'] = $purchaseReturnDetail['PurchaseReturnDetail']['expired_date'];
                    $data['date']         = $product_return['PurchaseReturn']['order_date'];
                    $data['total_qty']    = $qtyOrderSmall;
                    $data['total_order']  = $qtyOrderSmall;
                    $data['total_free']   = 0;
                    $data['user_id']      = $user['User']['id'];
                    $data['customer_id']  = "";
                    $data['vendor_id']    = $product_return['PurchaseReturn']['vendor_id'];
                    $data['unit_cost']    = 0;
                    $data['unit_price']   = 0;
                    // Update Invetory Location
                    $this->Inventory->saveInventory($data);
                    // Update Inventory Group
                    $this->Inventory->saveGroupTotalDetail($data);
                    // Convert to REST
                    $restBackCode[$rb] = $this->Helper->convertToDataSync($data, 'inventories');
                    $restBackCode[$rb]['module_type']  = 20;
                    $restBackCode[$rb]['total_qty']    = $qtyOrderSmall;
                    $restBackCode[$rb]['total_order']  = $qtyOrderSmall;
                    $restBackCode[$rb]['total_free']   = 0;
                    $restBackCode[$rb]['lots_number']  = '0';
                    $restBackCode[$rb]['expired_date'] = $data['expired_date'];
                    $restBackCode[$rb]['customer_id']  = "";
                    $restBackCode[$rb]['unit_cost']    = 0;
                    $restBackCode[$rb]['unit_price']   = 0;
                    $restBackCode[$rb]['vendor_id']    = $this->Helper->getSQLSyncCode("vendors", $product_return['PurchaseReturn']['vendor_id']);
                    $restBackCode[$rb]['purchase_return_id'] = $this->Helper->getSQLSyncCode("purchase_returns", $this->data['id']);
                    $restBackCode[$rb]['product_id']         = $this->Helper->getSQLSyncCode("products", $purchaseReturnDetail['PurchaseReturnDetail']['product_id']);
                    $restBackCode[$rb]['location_id']        = $this->Helper->getSQLSyncCode("locations", $product_return['PurchaseReturn']['location_id']);
                    $restBackCode[$rb]['location_group_id']  = $this->Helper->getSQLSyncCode("location_groups", $product_return['PurchaseReturn']['location_group_id']);
                    $restBackCode[$rb]['user_id']            = $this->Helper->getSQLSyncCode("users", $user['User']['id']);
                    $restBackCode[$rb]['dbtype']  = 'saveInv,GroupDetail';
                    $restBackCode[$rb]['actodo']  = 'inv';
                    $rb++;
                }
                // Save File Send Delete
                $this->Helper->sendFileToSync($restBackCode, 0, 0);

                $this->PurchaseReturn->create();
                $this->GeneralLedger->create();

                //  Find Chart Account
                $apAccount = $this->AccountType->findById(14);
                $purchaseMiscAccount = $this->AccountType->findById(10);
                $purchaseReturn = array();
                $purchaseReturn['PurchaseReturn']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $purchaseReturn['PurchaseReturn']['created']    = $dateNow;
                $purchaseReturn['PurchaseReturn']['created_by'] = $user['User']['id'];
                $purchaseReturn['PurchaseReturn']['company_id'] = $this->data['PurchaseReturn']['company_id'];
                $purchaseReturn['PurchaseReturn']['branch_id']  = $this->data['PurchaseReturn']['branch_id'];
                $purchaseReturn['PurchaseReturn']['location_group_id'] = $this->data['PurchaseReturn']['location_group_id'];
                $purchaseReturn['PurchaseReturn']['location_id'] = $this->data['PurchaseReturn']['location_id'];
                $purchaseReturn['PurchaseReturn']['vendor_id']   = $this->data['PurchaseReturn']['vendor_id'];
                $purchaseReturn['PurchaseReturn']['currency_center_id'] = $this->data['PurchaseReturn']['currency_center_id'];
                $purchaseReturn['PurchaseReturn']['note']    = $this->data['PurchaseReturn']['note'];
                $purchaseReturn['PurchaseReturn']['ap_id']   = $apAccount['AccountType']['chart_account_id'];
                $purchaseReturn['PurchaseReturn']['pr_code'] = $product_return['PurchaseReturn']['pr_code'];
                $purchaseReturn['PurchaseReturn']['total_amount'] = $this->data['PurchaseReturn']['total_amount'];
                $purchaseReturn['PurchaseReturn']['balance']      = $this->data['PurchaseReturn']['total_amount'] + $this->data['PurchaseReturn']['total_vat'];
                $purchaseReturn['PurchaseReturn']['total_amount'] = $this->data['PurchaseReturn']['total_amount'];
                $purchaseReturn['PurchaseReturn']['order_date']   = $this->data['PurchaseReturn']['order_date'];
                $purchaseReturn['PurchaseReturn']['vat_percent']  = $this->data['PurchaseReturn']['vat_percent'];
                $purchaseReturn['PurchaseReturn']['total_vat']    = $this->data['PurchaseReturn']['total_vat'];
                $purchaseReturn['PurchaseReturn']['vat_setting_id']  = $this->data['PurchaseReturn']['vat_setting_id'];
                $purchaseReturn['PurchaseReturn']['vat_calculate']   = $this->data['PurchaseReturn']['vat_calculate'];
                $purchaseReturn['PurchaseReturn']['vat_chart_account_id'] = $this->data['PurchaseReturn']['vat_chart_account_id'];
                $purchaseReturn['PurchaseReturn']['status'] = 2;

                if ($this->PurchaseReturn->save($purchaseReturn)) {
                    $result['br_id']  = $purchaseReturnId = $this->PurchaseReturn->id;
                    $company         = $this->Company->read(null, $this->data['PurchaseReturn']['company_id']);
                    $classId         = $this->Helper->getClassId($company['Company']['id'], $company['Company']['classes'], $this->data['PurchaseReturn']['location_group_id']);
                    $BRCode           = $product_return['PurchaseReturn']['pr_code'];
                    if($this->data['PurchaseReturn']['company_id'] != $product_return['PurchaseReturn']['company_id']){
                        // Get Module Code
                        $modCode = $this->Helper->getModuleCode($this->data['PurchaseReturn']['pr_code'], $purchaseReturnId, 'pr_code', 'purchase_returns', 'status != -1 AND branch_id = '.$this->data['PurchaseReturn']['branch_id']);
                        // Updaet Module Code
                        mysql_query("UPDATE purchase_returns SET pr_code = '".$modCode."' WHERE id = ".$purchaseReturnId);
                        $BRCode = $modCode;
                    }
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($purchaseReturn['PurchaseReturn'], 'purchase_returns');
                    $restCode[$r]['pr_code']  = $BRCode;
                    $restCode[$r]['modified'] = $dateNow;
                    $restCode[$r]['dbtodo']   = 'purchase_returns';
                    $restCode[$r]['actodo']   = 'is';
                    $r++;
                    // Create General Ledger
                    $generalLedger = array();
                    $this->GeneralLedger->create();
                    $generalLedger['GeneralLedger']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                    $generalLedger['GeneralLedger']['purchase_return_id'] = $purchaseReturnId;
                    $generalLedger['GeneralLedger']['date'] = $this->data['PurchaseReturn']['order_date'];
                    $generalLedger['GeneralLedger']['reference']  = $BRCode;
                    $generalLedger['GeneralLedger']['created']    = $dateNow;
                    $generalLedger['GeneralLedger']['created_by'] = $user['User']['id'];
                    $generalLedger['GeneralLedger']['is_sys'] = 1;
                    $generalLedger['GeneralLedger']['is_adj'] = 0;
                    $generalLedger['GeneralLedger']['is_active'] = 1;

                    $this->GeneralLedger->save($generalLedger);
                    $generalLedgerId = $this->GeneralLedger->id;
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($generalLedger['GeneralLedger'], 'general_ledgers');
                    $restCode[$r]['modified'] = $dateNow;
                    $restCode[$r]['dbtodo']   = 'general_ledgers';
                    $restCode[$r]['actodo']   = 'is';
                    $r++;
                    // General Ledger Detail (A/P)
                    $this->GeneralLedgerDetail->create();
                    $generalLedgerDetail = array();
                    $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $generalLedgerId;
                    $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id']  = $purchaseReturn['PurchaseReturn']['ap_id'];
                    $generalLedgerDetail['GeneralLedgerDetail']['location_group_id'] = $purchaseReturn['PurchaseReturn']['location_group_id'];
                    $generalLedgerDetail['GeneralLedgerDetail']['company_id']  = $purchaseReturn['PurchaseReturn']['company_id'];
                    $generalLedgerDetail['GeneralLedgerDetail']['branch_id']   = $purchaseReturn['PurchaseReturn']['branch_id'];
                    $generalLedgerDetail['GeneralLedgerDetail']['location_id'] = $purchaseReturn['PurchaseReturn']['location_id'];
                    $generalLedgerDetail['GeneralLedgerDetail']['type']   = 'Purchase Return';
                    $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $purchaseReturn['PurchaseReturn']['balance'];
                    $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                    $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Purchase Return # ' . $BRCode;
                    $generalLedgerDetail['GeneralLedgerDetail']['vendor_id'] = $purchaseReturn['PurchaseReturn']['vendor_id'];
                    $generalLedgerDetail['GeneralLedgerDetail']['class_id']  = $classId;
                    $this->GeneralLedgerDetail->save($generalLedgerDetail);
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                    $restCode[$r]['dbtodo']   = 'general_ledger_details';
                    $restCode[$r]['actodo']   = 'is';
                    $r++;

                    if (($purchaseReturn['PurchaseReturn']['total_vat']) > 0) {
                        $this->GeneralLedgerDetail->create();
                        $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $purchaseReturn['PurchaseReturn']['vat_chart_account_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                        $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $purchaseReturn['PurchaseReturn']['total_vat'];
                        $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Purchase Return # ' . $BRCode . ' Total VAT';
                        $this->GeneralLedgerDetail->save($generalLedgerDetail);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                        $restCode[$r]['dbtodo']   = 'general_ledger_details';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                    }

                    for ($i = 0; $i < sizeof($_POST['product']); $i++) {
                        if (!empty($_POST['product_id'][$i])) {
                            $purchaseReturnDetail = array();
                            // Purchase Return Detail
                            $this->PurchaseReturnDetail->create();
                            $purchaseReturnDetail['PurchaseReturnDetail']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                            $purchaseReturnDetail['PurchaseReturnDetail']['purchase_return_id'] = $purchaseReturnId;
                            $purchaseReturnDetail['PurchaseReturnDetail']['product_id']   = $_POST['product_id'][$i];
                            $purchaseReturnDetail['PurchaseReturnDetail']['qty']          = $_POST['qty'][$i];
                            $purchaseReturnDetail['PurchaseReturnDetail']['qty_uom_id']   = $_POST['qty_uom_id'][$i];
                            $purchaseReturnDetail['PurchaseReturnDetail']['conversion']   = $_POST['conversion'][$i];
                            $purchaseReturnDetail['PurchaseReturnDetail']['unit_price']   = $_POST['unit_price'][$i];
                            $purchaseReturnDetail['PurchaseReturnDetail']['total_price']  = $_POST['total_price'][$i];
                            $purchaseReturnDetail['PurchaseReturnDetail']['note']         = $_POST['note'][$i];
                            if($_POST['expired_date'][$i] != '' && $_POST['expired_date'][$i] != '0000-00-00'){
                                $dateExp = $this->Helper->dateConvert($_POST['expired_date'][$i]);
                            } else {
                                $dateExp = '0000-00-00';
                            }
                            $purchaseReturnDetail['PurchaseReturnDetail']['expired_date'] = $dateExp;
                            $this->PurchaseReturnDetail->save($purchaseReturnDetail);
                            $purchaseReturnDetailId = $this->PurchaseReturnDetail->id;
                            $qtyOrder      = $_POST['qty'][$i] / ($_POST['small_val_uom'][$i] / $_POST['conversion'][$i]);
                            $qtyOrderSmall = $_POST['qty'][$i] * $_POST['conversion'][$i];
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($purchaseReturnDetail['PurchaseReturnDetail'], 'purchase_return_details');
                            $restCode[$r]['dbtodo']   = 'purchase_return_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                            
                            // Update Inventory (Purchase Return)
                            $data = array();
                            $data['module_type']        = 7;
                            $data['purchase_return_id'] = $purchaseReturnId;
                            $data['product_id']         = $_POST['product_id'][$i];
                            $data['location_id']        = $purchaseReturn['PurchaseReturn']['location_id'];
                            $data['location_group_id']  = $purchaseReturn['PurchaseReturn']['location_group_id'];
                            $data['lots_number']  = '0';
                            $data['expired_date'] = $purchaseReturnDetail['PurchaseReturnDetail']['expired_date'];
                            $data['date']         = $purchaseReturn['PurchaseReturn']['order_date'];
                            $data['total_qty']    = $qtyOrderSmall;
                            $data['total_order']  = $qtyOrderSmall;
                            $data['total_free']   = 0;
                            $data['user_id']      = $user['User']['id'];
                            $data['customer_id']  = "";
                            $data['vendor_id']    = $purchaseReturn['PurchaseReturn']['vendor_id'];
                            $data['unit_cost']    = 0;
                            $data['unit_price']   = 0;
                            // Update Invetory Location
                            $this->Inventory->saveInventory($data);
                            // Update Inventory Group
                            $this->Inventory->saveGroupTotalDetail($data);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($data, 'inventories');
                            $restCode[$r]['module_type']  = 7;
                            $restCode[$r]['total_qty']    = $qtyOrderSmall;
                            $restCode[$r]['total_order']  = $qtyOrderSmall;
                            $restCode[$r]['total_free']   = 0;
                            $restCode[$r]['lots_number']  = '0';
                            $restCode[$r]['expired_date'] = $data['expired_date'];
                            $restCode[$r]['customer_id']  = "";
                            $restCode[$r]['unit_cost']    = 0;
                            $restCode[$r]['unit_price']   = 0;
                            $restCode[$r]['vendor_id']    = $this->Helper->getSQLSyncCode("vendors", $purchaseReturn['PurchaseReturn']['vendor_id']);
                            $restCode[$r]['purchase_return_id'] = $this->Helper->getSQLSyncCode("purchase_returns", $purchaseReturnId);
                            $restCode[$r]['product_id']         = $this->Helper->getSQLSyncCode("products", $_POST['product_id'][$i]);
                            $restCode[$r]['location_id']        = $this->Helper->getSQLSyncCode("locations", $purchaseReturn['PurchaseReturn']['location_id']);
                            $restCode[$r]['location_group_id']  = $this->Helper->getSQLSyncCode("location_groups", $purchaseReturn['PurchaseReturn']['location_group_id']);
                            $restCode[$r]['user_id']            = $this->Helper->getSQLSyncCode("users", $user['User']['id']);
                            $restCode[$r]['dbtype']  = 'saveInv,GroupDetail';
                            $restCode[$r]['actodo']  = 'inv';
                            $r++;
                            
                            //Insert Into Receive
                            $billReturnReceive = array();
                            $this->PurchaseReturnReceive->create();
                            $billReturnReceive['PurchaseReturnReceive']['purchase_return_id'] = $purchaseReturnId;
                            $billReturnReceive['PurchaseReturnReceive']['purchase_return_detail_id'] = $purchaseReturnDetailId;
                            $billReturnReceive['PurchaseReturnReceive']['product_id']    = $_POST['product_id'][$i];
                            $billReturnReceive['PurchaseReturnReceive']['qty']           = $_POST['qty'][$i];
                            $billReturnReceive['PurchaseReturnReceive']['qty_uom_id']    = $_POST['qty_uom_id'][$i];
                            $billReturnReceive['PurchaseReturnReceive']['conversion']    = $_POST['conversion'][$i];
                            $billReturnReceive['PurchaseReturnReceive']['lots_number']   = 0;
                            $billReturnReceive['PurchaseReturnReceive']['expired_date']  = $data['expired_date'];
                            $this->PurchaseReturnReceive->save($billReturnReceive);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($billReturnReceive['PurchaseReturnReceive'], 'purchase_return_receives');
                            $restCode[$r]['dbtodo'] = 'purchase_return_receives';
                            $restCode[$r]['actodo'] = 'is';
                            $r++;

                            // Inventory Valuation
                            $inv_valutaion = array();
                            $this->InventoryValuation->create();
                            $inv_valutaion['InventoryValuation']['sys_code']  = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                            $inv_valutaion['InventoryValuation']['purchase_return_id'] = $purchaseReturnId;
                            $inv_valutaion['InventoryValuation']['company_id'] = $this->data['PurchaseReturn']['company_id'];
                            $inv_valutaion['InventoryValuation']['branch_id']  = $this->data['PurchaseReturn']['branch_id'];
                            $inv_valutaion['InventoryValuation']['type']      = "Purchase Return";
                            $inv_valutaion['InventoryValuation']['date']      = $purchaseReturn['PurchaseReturn']['order_date'];
                            $inv_valutaion['InventoryValuation']['created']   = $dateNow;
                            $inv_valutaion['InventoryValuation']['pid']       = $_POST['product_id'][$i];
                            $inv_valutaion['InventoryValuation']['small_qty'] = "-" . $qtyOrderSmall;
                            $inv_valutaion['InventoryValuation']['qty']   = "-" . $this->Helper->replaceThousand(number_format($qtyOrder, 6));
                            $inv_valutaion['InventoryValuation']['cost']  = null;
                            $inv_valutaion['InventoryValuation']['price'] = $_POST['unit_price'][$i] * ($_POST['small_val_uom'][$i] / $_POST['conversion'][$i]);
                            $inv_valutaion['InventoryValuation']['is_var_cost'] = 1;
                            $this->InventoryValuation->saveAll($inv_valutaion);
                            $inv_valutation_id = $this->InventoryValuation->getLastInsertId();
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($inv_valutaion['InventoryValuation'], 'inventory_valuations');
                            $restCode[$r]['dbtodo']   = 'inventory_valuations';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                            
                            // Update GL for Inventory
                            $this->GeneralLedgerDetail->create();
                            $queryInvAccount = mysql_query("SELECT IFNULL((IFNULL((SELECT chart_account_id FROM accounts WHERE product_id = " . $_POST['product_id'][$i] . " AND account_type_id=1),(SELECT chart_account_id FROM pgroup_accounts WHERE pgroup_id = (SELECT pgroup_id FROM product_pgroups WHERE product_id = " . $_POST['product_id'][$i] . " ORDER BY id  DESC LIMIT 1) AND account_type_id=1))),(SELECT chart_account_id FROM account_types WHERE id=1))");
                            $dataInvAccount = mysql_fetch_array($queryInvAccount);
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $dataInvAccount[0];
                            $generalLedgerDetail['GeneralLedgerDetail']['service_id'] = NULL;
                            $generalLedgerDetail['GeneralLedgerDetail']['product_id'] = $_POST['product_id'][$i];
                            $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_id'] = $inv_valutation_id;
                            $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_is_debit'] = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $_POST['total_price'][$i];
                            $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Inventory Purchase Return # ' . $BRCode . ' Product # ' . $_POST['product'][$i];
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;

                            // Update GL COGS
                            $this->GeneralLedgerDetail->create();
                            $queryCogsAccount = mysql_query("SELECT IFNULL((IFNULL((SELECT chart_account_id FROM accounts WHERE product_id = " . $_POST['product_id'][$i] . " AND account_type_id=2),(SELECT chart_account_id FROM pgroup_accounts WHERE pgroup_id = (SELECT pgroup_id FROM product_pgroups WHERE product_id = " . $_POST['product_id'][$i] . " ORDER BY id  DESC LIMIT 1) AND account_type_id=2))),(SELECT chart_account_id FROM account_types WHERE id=2))");
                            $dataCogsAccount  = mysql_fetch_array($queryCogsAccount);
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $dataCogsAccount[0];
                            $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_id'] = $inv_valutation_id;
                            $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_is_debit'] = 1;
                            $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: COGS Purchase Return # ' . $BRCode . ' Product # ' . $_POST['product'][$i];
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                        } else if (!empty($_POST['service_id'][$i])) {
                            // Sales Order Service
                            $purchaseReturnService = array();
                            $this->PurchaseReturnService->create();
                            $purchaseReturnService['PurchaseReturnService']['purchase_return_id'] = $purchaseReturnId;
                            $purchaseReturnService['PurchaseReturnService']['service_id']  = $_POST['service_id'][$i];
                            $purchaseReturnService['PurchaseReturnService']['qty']         = $_POST['qty'][$i];
                            $purchaseReturnService['PurchaseReturnService']['unit_price']  = $_POST['unit_price'][$i];
                            $purchaseReturnService['PurchaseReturnService']['total_price'] = $_POST['total_price'][$i];
                            $purchaseReturnService['PurchaseReturnService']['note']        = $_POST['note'][$i];
                            $this->PurchaseReturnService->save($purchaseReturnService);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($purchaseReturnService['PurchaseReturnService'], 'purchase_return_services');
                            $restCode[$r]['dbtodo']   = 'purchase_return_services';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;

                            // General Ledger Detail (Service)
                            $this->GeneralLedgerDetail->create();
                            $queryServiceAccount = mysql_query("SELECT IFNULL((SELECT chart_account_id FROM services WHERE id=" . $_POST['service_id'][$i] . "),(SELECT chart_account_id FROM account_types WHERE id=9))");
                            $dataServiceAccount = mysql_fetch_array($queryServiceAccount);
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $dataServiceAccount[0];
                            $generalLedgerDetail['GeneralLedgerDetail']['service_id'] = $_POST['service_id'][$i];
                            $generalLedgerDetail['GeneralLedgerDetail']['product_id'] = NULL;
                            $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_id'] = NULL;
                            $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_is_debit'] = NULL;
                            $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $_POST['total_price'][$i];
                            $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Purchase Return  # ' . $BRCode . ' Service # ' . $_POST['product'][$i];
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                        } else {
                            // Sales Order Miscellaneous
                            $purchaseReturnMiscs = array();
                            $this->PurchaseReturnMiscs->create();
                            $purchaseReturnMiscs['PurchaseReturnMiscs']['purchase_return_id'] = $purchaseReturnId;
                            $purchaseReturnMiscs['PurchaseReturnMiscs']['description']  = $_POST['product'][$i];
                            $purchaseReturnMiscs['PurchaseReturnMiscs']['qty_uom_id']   = $_POST['qty_uom_id'][$i];
                            $purchaseReturnMiscs['PurchaseReturnMiscs']['qty']          = $_POST['qty'][$i];
                            $purchaseReturnMiscs['PurchaseReturnMiscs']['unit_price']   = $_POST['unit_price'][$i];
                            $purchaseReturnMiscs['PurchaseReturnMiscs']['total_price']  = $_POST['total_price'][$i];
                            $purchaseReturnMiscs['PurchaseReturnMiscs']['note']         = $_POST['note'][$i];
                            $this->PurchaseReturnMiscs->save($purchaseReturnMiscs);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($purchaseReturnMiscs['PurchaseReturnMiscs'], 'purchase_return_miscs');
                            $restCode[$r]['dbtodo']   = 'purchase_return_miscs';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;

                            // General Ledger Detail (Misc)
                            $this->GeneralLedgerDetail->create();
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id']  = $purchaseMiscAccount['AccountType']['chart_account_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['service_id'] = NULL;
                            $generalLedgerDetail['GeneralLedgerDetail']['product_id'] = NULL;
                            $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_id'] = NULL;
                            $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_is_debit'] = NULL;
                            $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $_POST['total_price'][$i];
                            $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Purchase Return  # ' . $BRCode . ' Misc # ' . $_POST['product'][$i];
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                        }
                    }
                    // Save File Send
                    $this->Helper->sendFileToSync($restCode, 0, 0);
                    // Recalculate Average Cost
                    mysql_query("UPDATE tracks SET val='".$purchaseReturn['PurchaseReturn']['order_date']."', is_recalculate = 1 WHERE id=1");
                    $this->Helper->saveUserActivity($user['User']['id'], 'Bill Return', 'Save Edit', $this->data['id'], $purchaseReturnId);
                    echo json_encode($result);
                    exit;
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'Bill Return', 'Save Edit (Error)', $this->data['id']);
                    $result['code'] = 2;
                    echo json_encode($result);
                    exit;
                }
            } else {
                $this->Helper->saveUserActivity($user['User']['id'], 'Bill Return', 'Save Edit (Error Status)', $this->data['id']);
                $result['code'] = 2;
                echo json_encode($result);
                exit;
            }
        }
        $this->Helper->saveUserActivity($user['User']['id'], 'Bill Return', 'Edit', $id);
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
                            'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id', 'ModuleCodeBranch.br_code', 'Branch.currency_center_id', 'CurrencyCenter.symbol'),
                            'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])
                        ));
        // Get Loction Setting
        $locSetting = ClassRegistry::init('LocationSetting')->findById(2);
        $locCon     = '';
        if($locSetting['LocationSetting']['location_status'] == 1){
            $locCon = ' AND Location.is_for_sale = 0';
        }
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('joins' => array(array('table' => 'user_location_groups', 'type' => 'inner', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'))),'conditions' => array('user_location_groups.user_id=' . $user['User']['id'], 'LocationGroup.is_active' => '1', 'LocationGroup.location_group_type_id != 1')));
        $locations = ClassRegistry::init('Location')->find('all', array('joins' => array(array('table' => 'user_locations', 'type' => 'inner', 'conditions' => array('user_locations.location_id=Location.id'))), 'conditions' => array('user_locations.user_id=' . $user['User']['id'] . ' AND Location.is_active=1'.$locCon), 'order' => 'Location.name'));
        $purchase_returns = ClassRegistry::init('PurchaseReturn')->find('first', array('conditions' => array('PurchaseReturn.status = 2', 'PurchaseReturn.id' => $id)));
        $this->set(compact("purchase_returns", "locationGroups", "locations", "apAccountId", "id", "companies", "branches"));
    }

    function editDetail($id = null) {
        $this->layout = 'ajax';
        $uoms = ClassRegistry::init('Uom')->find('all', array('fields' => array('Uom.id', 'Uom.name'), 'conditions' => array('Uom.is_active' => 1)));
        if (!empty($id)) {
            $user = $this->getCurrentUser();
            $branches = ClassRegistry::init('Branch')->find('all',
                            array(
                                'joins' => array(
                                    array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id')),
                                    array('table' => 'module_code_branches AS ModuleCodeBranch', 'type' => 'left', 'conditions' => array('ModuleCodeBranch.branch_id=Branch.id'))
                                ),
                                'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id', 'ModuleCodeBranch.br_code', 'Branch.currency_center_id', 'CurrencyCenter.symbol'),
                                'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])
                            ));
            $purchase_returns = ClassRegistry::init('PurchaseReturn')->find('first', array('conditions' => array('PurchaseReturn.status = 2', 'PurchaseReturn.id' => $id)));
            $purchaseReturnDetails = ClassRegistry::init('PurchaseReturnDetail')->find('all', array('conditions' => array('PurchaseReturnDetail.purchase_return_id' => $id)));
            $purchaseReturnServices = ClassRegistry::init('PurchaseReturnService')->find('all', array('conditions' => array('PurchaseReturnService.purchase_return_id' => $id)));
            $purchaseReturnMiscs = ClassRegistry::init('PurchaseReturnMisc')->find('all', array('conditions' => array('PurchaseReturnMisc.purchase_return_id' => $id)));
            $this->set(compact('branches', 'uoms', "purchaseReturnDetails", "purchase_returns", "purchaseReturnServices", "purchaseReturnMiscs"));
        } else {
            $this->set(compact('uoms'));
        }
    }

//    function receive($id = null) {
//        $this->layout = 'ajax';
//        if (!$id && empty($this->data)) {
//            exit;
//        }
//        $user = $this->getCurrentUser();
//        if (!empty($this->data)) {            
//            $purcahse_return = $this->PurchaseReturn->read(null, $this->data['pr_id']);
//            if ($purcahse_return['PurchaseReturn']['status'] == 1 || $purcahse_return['PurchaseReturn']['status'] == 3) {
//                $access = true;
//                $productOrder = array();
//                $sqlDetail = mysql_query("SELECT id, product_id, SUM(IFNULL(qty,0) * conversion) AS qty FROM purchase_return_details WHERE purchase_return_id =".$this->data['pr_id']." AND id NOT IN (SELECT purchase_return_detail_id FROM purchase_return_receives GROUP BY purchase_return_detail_id) GROUP BY product_id");
//                if(@mysql_num_rows($sqlDetail)) {
//                    // Check With Current Stock
//                    while($rowDetail = mysql_fetch_array($sqlDetail)){
//                        if (array_key_exists($rowDetail['product_id'], $productOrder)){
//                            $productOrder[$rowDetail['product_id']]['qty'] += $rowDetail['qty'];
//                        } else {
//                            $productOrder[$rowDetail['product_id']]['qty'] = $rowDetail['qty'];
//                        }
//                    }
//                }
//                // Check Qty in Stock Before Save
//                foreach($productOrder AS $key => $order){
//                    $sqlTotal = mysql_query("SELECT (SUM(IFNULL(total_qty,0) - IFNULL(total_order,0)) + (SELECT IFNULL(SUM(qty),0) FROM stock_orders WHERE product_id = ".$key." AND purchase_return_id = ".$this->data['pr_id'].")) AS total_qty FROM ".$purcahse_return['PurchaseReturn']['location_group_id']."_group_totals WHERE product_id = ".$key." AND location_group_id = ".$purcahse_return['PurchaseReturn']['location_group_id']." AND location_id = ".$purcahse_return['PurchaseReturn']['location_id']." GROUP BY product_id;");
//                    $rowTotal = mysql_fetch_array($sqlTotal);
//                    if($rowTotal['total_qty'] < $order['qty']){
//                        $access = false;
//                    }
//                }
//                if($access == true) {
//                    $r = 0;
//                    $restCode = array();
//                    $dateNow  = date("Y-m-d H:i:s");
//                    $this->loadModel('PurchaseReturnReceive');
//                    $datePbc  = $purcahse_return['PurchaseReturn']['order_date'];
//                    // Calculate Location, Lot, Expired Date
//                    $sqlOrder = mysql_query("SELECT stock_orders.*, products.price_uom_id FROM stock_orders INNER JOIN products ON products.id = stock_orders.product_id WHERE stock_orders.purchase_return_id = ".$this->data['pr_id']);
//                    while($rowOrder = mysql_fetch_array($sqlOrder)){
//                        // Reset Stock Order
//                        $this->Inventory->saveGroupQtyOrder($rowOrder['location_group_id'], $rowOrder['location_id'], $rowOrder['product_id'], $rowOrder['lots_number'], $rowOrder['expired_date'], $rowOrder['qty'], $datePbc, '-');
//                        $sqlUom = mysql_query("SELECT IFNULL((SELECT to_uom_id FROM uom_conversions WHERE from_uom_id = ".$rowOrder['price_uom_id']." AND is_small_uom = 1 LIMIT 1), ".$rowOrder['price_uom_id'].")");
//                        $rowUom = mysql_fetch_array($sqlUom);
//                        // Get Lots, Expired, Total Qty
//                        $invInfos   = array();
//                        $index      = 0;
//                        $totalOrder = $rowOrder['qty'];
//                        // Calculate Location, Lot, Expired Date
//                        $sqlInventory = mysql_query("SELECT SUM(IFNULL(group_totals.total_qty,0)) AS total_qty, group_totals.location_id AS location_id, group_totals.lots_number AS lots_number, group_totals.expired_date AS expired_date FROM ".$rowOrder['location_group_id']."_group_totals AS group_totals WHERE group_totals.location_id = ".$rowOrder['location_id']." AND group_totals.product_id = ".$rowOrder['product_id']." GROUP BY group_totals.location_id, group_totals.product_id, group_totals.lots_number, group_totals.expired_date HAVING total_qty > 0 ORDER BY group_totals.lots_number, group_totals.expired_date, group_totals.location_id ASC");
//                        while($rowInventory = mysql_fetch_array($sqlInventory)) {
//                            // Check Order
//                            $stockOrder = 0;
//                            $sqlStock = mysql_query("SELECT SUM(qty) FROM stock_orders WHERE purchase_return_id IS NULL AND product_id = ".$rowOrder['product_id']." AND location_group_id = ".$rowOrder['location_group_id']." AND location_id = ".$rowInventory['location_id']." AND lots_number = '".$rowInventory['lots_number']."' AND expired_date = '".$rowInventory['expired_date']."'");
//                            if(mysql_num_rows($sqlStock)){
//                                $rowStock = mysql_fetch_array($sqlStock);
//                                $stockOrder = $rowStock[0];
//                            }
//                            $totalStock = $rowInventory['total_qty'] - $stockOrder;
//                            if($totalOrder > 0 && $totalStock > 0){
//                                if($totalStock >= $totalOrder) {
//                                    $invInfos[$index]['total_qty']    = $totalOrder;
//                                    $invInfos[$index]['location_id']  = $rowInventory['location_id'];
//                                    $invInfos[$index]['lots_number']  = $rowInventory['lots_number'];
//                                    $invInfos[$index]['expired_date'] = $rowInventory['expired_date'];
//                                    $totalOrder = 0;
//                                    ++$index;
//                                } else if($totalStock < $totalOrder) {
//                                    $invInfos[$index]['total_qty']    = $rowInventory['total_qty'];
//                                    $invInfos[$index]['location_id']  = $rowInventory['location_id'];
//                                    $invInfos[$index]['lots_number']  = $rowInventory['lots_number'];
//                                    $invInfos[$index]['expired_date'] = $rowInventory['expired_date'];
//                                    $totalOrder = $totalOrder - $totalStock;
//                                    ++$index;
//                                }
//                            }
//                        }
//                        // Cut Stock
//                        foreach($invInfos AS $invInfo){
//                            // Update Inventory (Bill Return)
//                            $data = array();
//                            $data['module_type']        = 7;
//                            $data['purchase_return_id'] = $rowOrder['purchase_return_id'];
//                            $data['product_id']         = $rowOrder['product_id'];
//                            $data['location_id']        = $invInfo['location_id'];
//                            $data['location_group_id']  = $rowOrder['location_group_id'];
//                            $data['lots_number']  = '0';
//                            $data['expired_date'] = '0000-00-00';
//                            $data['date']         = $datePbc;
//                            $data['total_qty']    = $invInfo['total_qty'];
//                            $data['total_order']  = $invInfo['total_qty'];
//                            $data['total_free']   = 0;
//                            $data['user_id']      = $user['User']['id'];
//                            $data['customer_id']  = "";
//                            $data['vendor_id']    = $purcahse_return['PurchaseReturn']['vendor_id'];
//                            $data['unit_cost']    = 0;
//                            $data['unit_price']   = 0;
//                            // Update Invetory Location
//                            $this->Inventory->saveInventory($data);
//                            // Update Inventory Group
//                            $this->Inventory->saveGroupTotalDetail($data);
//                            // Convert to REST
//                            $restCode[$r] = $this->Helper->convertToDataSync($data, 'inventories');
//                            $restCode[$r]['module_type']  = 7;
//                            $restCode[$r]['total_qty']    = $invInfo['total_qty'];
//                            $restCode[$r]['total_order']  = $invInfo['total_qty'];
//                            $restCode[$r]['total_free']   = 0;
//                            $restCode[$r]['expired_date'] = $data['expired_date'];
//                            $restCode[$r]['customer_id']  = "";
//                            $restCode[$r]['unit_cost']    = 0;
//                            $restCode[$r]['unit_price']   = 0;
//                            $restCode[$r]['vendor_id']    = $this->Helper->getSQLSyncCode("vendors", $purcahse_return['PurchaseReturn']['vendor_id']);
//                            $restCode[$r]['purchase_return_id'] = $this->Helper->getSQLSyncCode("purchase_returns", $rowOrder['purchase_return_id']);
//                            $restCode[$r]['product_id']        = $this->Helper->getSQLSyncCode("products", $rowOrder['product_id']);
//                            $restCode[$r]['location_id']       = $this->Helper->getSQLSyncCode("locations", $invInfo['location_id']);
//                            $restCode[$r]['location_group_id'] = $this->Helper->getSQLSyncCode("location_groups", $rowOrder['location_group_id']);
//                            $restCode[$r]['user_id']           = $this->Helper->getSQLSyncCode("users", $user['User']['id']);
//                            $restCode[$r]['dbtype']  = 'saveInv,GroupDetail';
//                            $restCode[$r]['actodo']  = 'inv';
//                            $r++;
//                            
//                            //Insert Into Delivery Detail
//                            $billReturnReceive = array();
//                            $this->PurchaseReturnReceive->create();
//                            $billReturnReceive['PurchaseReturnReceive']['purchase_return_id'] = $rowOrder['purchase_return_id'];
//                            $billReturnReceive['PurchaseReturnReceive']['purchase_return_detail_id'] = $rowOrder['purchase_return_detail_id'];
//                            $billReturnReceive['PurchaseReturnReceive']['product_id']    = $rowOrder['product_id'];
//                            $billReturnReceive['PurchaseReturnReceive']['qty_uom_id']    = $rowUom[0];
//                            $billReturnReceive['PurchaseReturnReceive']['lots_number']   = $invInfo['lots_number']!=''?$invInfo['lots_number']:0;
//                            $billReturnReceive['PurchaseReturnReceive']['expired_date']  = $invInfo['expired_date']!='0000-00-00'?$invInfo['expired_date']:'0000-00-00';
//                            $billReturnReceive['PurchaseReturnReceive']['qty'] = $invInfo['total_qty'];
//                            $this->PurchaseReturnReceive->save($billReturnReceive);
//                            // Convert to REST
//                            $restCode[$r] = $this->Helper->convertToDataSync($billReturnReceive['PurchaseReturnReceive'], 'purchase_return_receives');
//                            $restCode[$r]['dbtodo'] = 'purchase_return_receives';
//                            $restCode[$r]['actodo'] = 'is';
//                            $r++;
//                        }
//                    }
//                    // Update Sales Order
//                    mysql_query("UPDATE purchase_returns SET status = 2, `modified` = '".$dateNow."', `modified_by` = ".$user['User']['id']." WHERE  `id`= " . $this->data['pr_id']);
//                    // Convert to REST
//                    $restCode[$r]['status']      = 2;
//                    $restCode[$r]['modified']    = $dateNow;
//                    $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
//                    $restCode[$r]['dbtodo'] = 'purchase_returns';
//                    $restCode[$r]['actodo'] = 'ut';
//                    $restCode[$r]['con']    = "sys_code = '".$purcahse_return['PurchaseReturn']['sys_code']."'";
//                    // Detele Tmp Stock Order
//                    mysql_query("DELETE FROM `stock_orders` WHERE  `purchase_return_id`=".$this->data['pr_id'].";");
//                    // Save File Send
//                    $this->Helper->sendFileToSync($restCode, 0, 0);
//                    $this->Helper->saveUserActivity($user['User']['id'], 'Bill Return', 'Save Receive', $this->data['pr_id']);
//                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
//                    exit;
//                }
//            } else {
//                $this->Helper->saveUserActivity($user['User']['id'], 'Bill Return', 'Save Receive (Error Status)', $this->data['pr_id']);
//                echo 'error';
//                exit;
//            }
//        }
//        $this->Helper->saveUserActivity($user['User']['id'], 'Bill Return', 'Receive', $id);
//        $purchase_returns = ClassRegistry::init('PurchaseReturn')->find('first', array('conditions' => array('PurchaseReturn.id' => $id)));
//        $purchaseReturnDetails = ClassRegistry::init('PurchaseReturnDetail')->find('all', array('conditions' => array('PurchaseReturnDetail.purchase_return_id' => $id)));
//        $this->set(compact("purchase_returns", "purchaseReturnDetails"));
//    }

    function void($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $r = 0;
        $restCode = array();
        $dateNow  = date("Y-m-d H:i:s");
        $user = $this->getCurrentUser();
        $this->loadModel('GeneralLedger');
        $this->loadModel('InventoryValuation');
        $this->loadModel('PurchaseReturnDetail');
        $queryHasReceipt = mysql_query("SELECT id FROM purchase_return_receipts WHERE purchase_return_id=" . $id . " AND is_void = 0");
        $queryHasApplyInv = mysql_query("SELECT id FROM invoice_pbc_with_pbs WHERE purchase_return_id=" . $id . " AND status > 0");
        if (@mysql_num_rows($queryHasReceipt) || @mysql_num_rows($queryHasApplyInv)) {
            $this->Helper->saveUserActivity($user['User']['id'], 'Bill Return', 'Void (Transaction with other modules)', $id);
            echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
            exit;
        }
        $product_return = $this->PurchaseReturn->read(null, $id);
        if (!isset($product_return['PurchaseReturn']['order_date']) || is_null($product_return['PurchaseReturn']['order_date']) || $product_return['PurchaseReturn']['order_date'] == '0000-00-00' || $product_return['PurchaseReturn']['order_date'] == '') {
            $this->Helper->saveUserActivity($user['User']['id'], 'Bill Return', 'Void (Error Order Date)', $id);
            echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
            exit;
        }
        if($product_return['PurchaseReturn']['order_date'] == 1){
            $this->Helper->saveUserActivity($user['User']['id'], 'Bill Return', 'Void (Error Status)', $id);
            echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
            exit;
        }
        $this->PurchaseReturn->updateAll(
                array('PurchaseReturn.status' => 0, 'PurchaseReturn.modified_by' => $user['User']['id']), array('PurchaseReturn.id' => $id)
        );
        // Convert to REST
        $restCode[$r]['status']      = 0;
        $restCode[$r]['modified']    = $dateNow;
        $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
        $restCode[$r]['dbtodo'] = 'purchase_returns';
        $restCode[$r]['actodo'] = 'ut';
        $restCode[$r]['con']    = "sys_code = '".$product_return['PurchaseReturn']['sys_code']."'";
        $r++;
        $this->InventoryValuation->updateAll(
                array('InventoryValuation.is_active' => "2"), array('InventoryValuation.purchase_return_id' => $id)
        );
        // Convert to REST
        $restCode[$r]['is_active']   = 2;
        $restCode[$r]['dbtodo'] = 'inventory_valuations';
        $restCode[$r]['actodo'] = 'ut';
        $restCode[$r]['con']    = "purchase_return_id = (SELECT id FROM purchase_returns WHERE sys_code = '".$product_return['PurchaseReturn']['sys_code']."' LIMIT 1)";
        $r++;
        $this->GeneralLedger->updateAll(
                array('GeneralLedger.is_active' => 2, 'GeneralLedger.modified_by' => $user['User']['id']), array('GeneralLedger.purchase_return_id' => $id)
        );
        // Convert to REST
        $restCode[$r]['is_active']   = 2;
        $restCode[$r]['modified']    = $dateNow;
        $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
        $restCode[$r]['dbtodo'] = 'general_ledgers';
        $restCode[$r]['actodo'] = 'ut';
        $restCode[$r]['con']    = "purchase_return_id = (SELECT id FROM purchase_returns WHERE sys_code = '".$product_return['PurchaseReturn']['sys_code']."' LIMIT 1)";
        $r++;
        $purchaseReturnDetails = ClassRegistry::init('PurchaseReturnDetail')->find("all", array('conditions' => array('PurchaseReturnDetail.purchase_return_id' => $this->data['id'])));
        foreach($purchaseReturnDetails AS $purchaseReturnDetail){
            $qtyOrderSmall = ($product_return['PurchaseReturn']['qty'] * $product_return['PurchaseReturn']['conversion']);
            // Update Inventory (Purchase Return)
            $data = array();
            $data['module_type']        = 20;
            $data['purchase_return_id'] = $this->data['id'];
            $data['product_id']         = $purchaseReturnDetail['PurchaseReturnDetail']['product_id'];
            $data['location_id']        = $product_return['PurchaseReturn']['location_id'];
            $data['location_group_id']  = $product_return['PurchaseReturn']['location_group_id'];
            $data['lots_number']  = '0';
            $data['expired_date'] = $purchaseReturnDetail['PurchaseReturnDetail']['expired_date'];
            $data['date']         = $product_return['PurchaseReturn']['order_date'];
            $data['total_qty']    = $qtyOrderSmall;
            $data['total_order']  = $qtyOrderSmall;
            $data['total_free']   = 0;
            $data['user_id']      = $user['User']['id'];
            $data['customer_id']  = "";
            $data['vendor_id']    = $product_return['PurchaseReturn']['vendor_id'];
            $data['unit_cost']    = 0;
            $data['unit_price']   = 0;
            // Update Invetory Location
            $this->Inventory->saveInventory($data);
            // Update Inventory Group
            $this->Inventory->saveGroupTotalDetail($data);
            // Convert to REST
            $restCode[$r] = $this->Helper->convertToDataSync($data, 'inventories');
            $restCode[$r]['module_type']  = 20;
            $restCode[$r]['total_qty']    = $qtyOrderSmall;
            $restCode[$r]['total_order']  = $qtyOrderSmall;
            $restCode[$r]['total_free']   = 0;
            $restCode[$r]['lots_number']  = '0';
            $restCode[$r]['expired_date'] = $data['expired_date'];
            $restCode[$r]['customer_id']  = "";
            $restCode[$r]['unit_cost']    = 0;
            $restCode[$r]['unit_price']   = 0;
            $restCode[$r]['vendor_id']    = $this->Helper->getSQLSyncCode("vendors", $product_return['PurchaseReturn']['vendor_id']);
            $restCode[$r]['purchase_return_id'] = $this->Helper->getSQLSyncCode("purchase_returns", $this->data['id']);
            $restCode[$r]['product_id']         = $this->Helper->getSQLSyncCode("products", $purchaseReturnDetail['PurchaseReturnDetail']['product_id']);
            $restCode[$r]['location_id']        = $this->Helper->getSQLSyncCode("locations", $product_return['PurchaseReturn']['location_id']);
            $restCode[$r]['location_group_id']  = $this->Helper->getSQLSyncCode("location_groups", $product_return['PurchaseReturn']['location_group_id']);
            $restCode[$r]['user_id']            = $this->Helper->getSQLSyncCode("users", $user['User']['id']);
            $restCode[$r]['dbtype']  = 'saveInv,GroupDetail';
            $restCode[$r]['actodo']  = 'inv';
            $r++;
        }
        // Save File Send
        $this->Helper->sendFileToSync($restCode, 0, 0);
        // Recalculate Average Cost
        mysql_query("UPDATE tracks SET val='".$dateReca."', is_recalculate = 1 WHERE id=1");
        $this->Helper->saveUserActivity($user['User']['id'], 'Bill Return', 'Void', $id);
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
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
        $this->loadModel('PurchaseReturnReceipt');
        $receipt = ClassRegistry::init('PurchaseReturnReceipt')->find("first", array('conditions' => array('PurchaseReturnReceipt.id' => $id)));
        $this->PurchaseReturnReceipt->updateAll(
                array('PurchaseReturnReceipt.is_void' => 1, 'PurchaseReturnReceipt.modified_by' => $user['User']['id']), array('PurchaseReturnReceipt.id' => $id)
        );
        // Convert to REST
        $restCode[$r]['is_void']     = 1;
        $restCode[$r]['modified']    = $dateNow;
        $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
        $restCode[$r]['dbtodo'] = 'purchase_return_receipts';
        $restCode[$r]['actodo'] = 'ut';
        $restCode[$r]['con']    = "sys_code = '".$receipt['PurchaseReturnReceipt']['sys_code']."'";
        $r++;
        $exchangeRate = ClassRegistry::init('ExchangeRate')->find("first", array("conditions" => array("ExchangeRate.id" => $receipt['PurchaseReturnReceipt']['exchange_rate_id'])));
        if(!empty($exchangeRate) && $exchangeRate['ExchangeRate']['rate_to_sell'] > 0){
            $totalPaidOther = $receipt['PurchaseReturnReceipt']['amount_other'] / $exchangeRate['ExchangeRate']['rate_to_sell'];
        } else {
            $totalPaidOther = 0;
        }
        $total_amount = $receipt['PurchaseReturnReceipt']['amount_us'] + $totalPaidOther;

        mysql_query("UPDATE purchase_returns SET balance = balance+" . $total_amount . " WHERE id=" . $receipt['PurchaseReturnReceipt']['purchase_return_id']);
        // Convert to REST
        $restCode[$r]['balance']     = '(balance+'.$total_amount.')';
        $restCode[$r]['modified']    = $dateNow;
        $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
        $restCode[$r]['dbtodo'] = 'purchase_returns';
        $restCode[$r]['actodo'] = 'ut';
        $restCode[$r]['con']    = "sys_code = '".$receipt['PurchaseReturn']['sys_code']."'";
        $r++;
        $this->GeneralLedger->updateAll(
                array('GeneralLedger.is_active' => 2, 'GeneralLedger.modified_by' => $user['User']['id']), array('GeneralLedger.purchase_return_receipt_id' => $id)
        );
        // Convert to REST
        $restCode[$r]['is_active']   = 2;
        $restCode[$r]['modified']    = $dateNow;
        $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
        $restCode[$r]['dbtodo'] = 'general_ledgers';
        $restCode[$r]['actodo'] = 'ut';
        $restCode[$r]['con']    = "purchase_return_receipt_id = (SELECT id FROM purchase_return_receipts WHERE sys_code = '".$receipt['PurchaseReturnReceipt']['sys_code']."' LIMIT 1)";

        // Save File Send
        $this->Helper->sendFileToSync($restCode, 0, 0);
        $this->Helper->saveUserActivity($user['User']['id'], 'Bill Return Receipt', 'Void', $id);
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }

    function deletePbcWPo($id) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $r = 0;
        $restCode = array();
        $dateNow  = date("Y-m-d H:i:s");
        $result = array();
        $user = $this->getCurrentUser();
        $this->loadModel('InvoicePbcWithPb');
        $this->loadModel('GeneralLedger');
        $pbcWPo = $this->InvoicePbcWithPb->read(null, $id);
        if ($pbcWPo['InvoicePbcWithPb']['status'] == 1) {
            mysql_query("UPDATE purchase_orders SET balance = balance + " . $pbcWPo['InvoicePbcWithPb']['total_cost'] . " WHERE id=" . $pbcWPo['InvoicePbcWithPb']['purchase_order_id']);
            // Convert to REST
            $restCode[$r]['balance']     = '(balance+'.$pbcWPo['InvoicePbcWithPb']['total_cost'].')';
            $restCode[$r]['modified']    = $dateNow;
            $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
            $restCode[$r]['dbtodo'] = 'purchase_orders';
            $restCode[$r]['actodo'] = 'ut';
            $restCode[$r]['con']    = "sys_code = '".$pbcWPo['PurchaseOrder']['sys_code']."'";
            $r++;
            mysql_query("UPDATE purchase_returns SET balance = balance + " . $pbcWPo['InvoicePbcWithPb']['total_cost'] . ", total_amount_po =total_amount_po - " . $pbcWPo['InvoicePbcWithPb']['total_cost'] . " WHERE id=" . $pbcWPo['InvoicePbcWithPb']['purchase_return_id']);
            // Convert to REST
            $restCode[$r]['balance']     = '(balance+'.$pbcWPo['InvoicePbcWithPb']['total_cost'].')';
            $restCode[$r]['modified']    = $dateNow;
            $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
            $restCode[$r]['dbtodo'] = 'purchase_returns';
            $restCode[$r]['actodo'] = 'ut';
            $restCode[$r]['con']    = "sys_code = '".$pbcWPo['PurchaseReturn']['sys_code']."'";
            $r++;
            $this->data['InvoicePbcWithPb']['id'] = $id;
            $this->data['InvoicePbcWithPb']['modified']    = $dateNow;
            $this->data['InvoicePbcWithPb']['modified_by'] = $user['User']['id'];
            $this->data['InvoicePbcWithPb']['status'] = 0;
            $this->InvoicePbcWithPb->save($this->data);
            // Convert to REST
            $restCode[$r]['status']      = 0;
            $restCode[$r]['modified']    = $dateNow;
            $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
            $restCode[$r]['dbtodo'] = 'invoice_pbc_with_pbs';
            $restCode[$r]['actodo'] = 'ut';
            $restCode[$r]['con']    = "sys_code = '".$pbcWPo['InvoicePbcWithPb']['sys_code']."'";
            $r++;
            $this->GeneralLedger->updateAll(
                    array('GeneralLedger.is_active' => 2, 'GeneralLedger.modified_by' => $user['User']['id']),
                    array('GeneralLedger.invoice_pbc_with_pbs_id' => $id)
            );
            // Convert to REST
            $restCode[$r]['is_active']   = 2;
            $restCode[$r]['modified']    = $dateNow;
            $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
            $restCode[$r]['dbtodo'] = 'general_ledgers';
            $restCode[$r]['actodo'] = 'ut';
            $restCode[$r]['con']    = "invoice_pbc_with_pbs_id = (SELECT id FROM invoice_pbc_with_pbs WHERE sys_code = '".$pbcWPo['InvoicePbcWithPb']['sys_code']."' LIMIT 1)";
            // Save File Send
            $this->Helper->sendFileToSync($restCode, 0, 0);
            $this->Helper->saveUserActivity($user['User']['id'], 'Bill Return Receipt', 'Void Apply to PB', $id);
            $result['result']   = 1;
        } else {
            $this->Helper->saveUserActivity($user['User']['id'], 'Bill Return Receipt', 'Void Apply to PB (Error)', $id);
            $result['result']   = 2;
        }
        echo json_encode($result);
        exit;
    }

    
    function pickProduct($billReturnDetailId = null, $locationGroupId = null){        
        $this->layout = 'ajax';        
        if(empty($billReturnDetailId) || empty($locationGroupId)){
            echo MESSAGE_DATA_INVALID;
            exit;
        }      
        $purchaseReturnDetail = ClassRegistry::init('PurchaseReturnDetail')->find("first", array('conditions' => array('PurchaseReturnDetail.id' => $billReturnDetailId)));
        $this->set(compact("billReturnDetailId", "locationGroupId", "purchaseReturnDetail"));        
    }
    
    function pickProductAjax($productId = null, $locationGroupId = null, $smallUomLabel = null, $smallUomId = null){
        $this->layout = 'ajax';
        $this->set(compact('productId', 'locationGroupId', 'smallUomLabel', 'smallUomId'));
    }
    
    function pickProductSave(){
        $this->layout = 'ajax';
        if(!empty($this->data)){
            $user = $this->getCurrentUser();
            $sql = mysql_query("SELECT id FROM purchase_return_receives WHERE purchase_return_detail_id = ".$this->data['bill_return_detail_id']);
            if(!mysql_num_rows($sql)){
                $r = 0;
                $restCode = array();
                $dateNow  = date("Y-m-d H:i:s");
                $this->loadModel('PurchaseReturnReceive');
                $billReturn = ClassRegistry::init('PurchaseReturn')->find("first", array('conditions' => array('PurchaseReturn.id' => $this->data['bill_return_id'])));
                // Reset Stock Order
                $sqlResetOrder = mysql_query("SELECT * FROM stock_orders WHERE `purchase_return_id`=".$this->data['bill_return_id']." AND purchase_return_detail_id = ".$this->data['bill_return_detail_id'].";");
                while($rowResetOrder = mysql_fetch_array($sqlResetOrder)){
                    $this->Inventory->saveGroupQtyOrder($rowResetOrder['location_group_id'], $rowResetOrder['location_id'], $rowResetOrder['product_id'], $rowResetOrder['lots_number'], $rowResetOrder['expired_date'], $rowResetOrder['qty'], $rowResetOrder['date'], '-');
                }
                // Detele Tmp Stock Order
                mysql_query("DELETE FROM `stock_orders` WHERE  `purchase_return_id`=".$this->data['bill_return_id']." AND purchase_return_detail_id = ".$this->data['bill_return_detail_id'].";");   
                for($i = 0; $i < sizeof($_POST['qty_pick']); $i++){
                    // Update Inventory (Bill Return)
                    $data = array();
                    $data['module_type']        = 7;
                    $data['purchase_return_id'] = $this->data['bill_return_id'];
                    $data['product_id']         = $this->data['product_id'];
                    $data['location_id']        = $_POST['location_id'][$i];
                    $data['location_group_id']  = $billReturn['PurchaseReturn']['location_group_id'];
                    $data['lots_number']  = $_POST['lots_number'][$i]!=''?$_POST['lots_number'][$i]:0;
                    $data['expired_date'] = $_POST['expired_date'][$i]!=''?$_POST['expired_date'][$i]:'0000-00-00';
                    $data['date']         = $billReturn['PurchaseReturn']['order_date'];
                    $data['total_qty']    = $_POST['qty_pick'][$i];
                    $data['total_order']  = $_POST['qty_pick'][$i];
                    $data['total_free']   = 0;
                    $data['user_id']      = $user['User']['id'];
                    $data['customer_id']  = "";
                    $data['vendor_id']    = $billReturn['PurchaseReturn']['vendor_id'];
                    $data['unit_cost']    = 0;
                    // Update Invetory Location
                    $this->Inventory->saveInventory($data);
                    // Update Inventory Group
                    $this->Inventory->saveGroupTotalDetail($data);
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($data, 'inventories');
                    $restCode[$r]['module_type']  = 7;
                    $restCode[$r]['total_qty']    = $_POST['qty_pick'][$i];
                    $restCode[$r]['total_order']  = $_POST['qty_pick'][$i];
                    $restCode[$r]['total_free']   = 0;
                    $restCode[$r]['expired_date'] = $data['expired_date'];
                    $restCode[$r]['customer_id']  = "";
                    $restCode[$r]['vendor_id']    = $billReturn['PurchaseReturn']['vendor_id'];
                    $restCode[$r]['unit_cost']    = 0;
                    $restCode[$r]['unit_price']   = 0;
                    $restCode[$r]['purchase_return_id'] = $this->Helper->getSQLSyncCode("purchase_returns", $this->data['bill_return_id']);
                    $restCode[$r]['product_id']        = $this->Helper->getSQLSyncCode("products", $this->data['product_id']);
                    $restCode[$r]['location_id']       = $this->Helper->getSQLSyncCode("locations", $_POST['location_id'][$i]);
                    $restCode[$r]['location_group_id'] = $this->Helper->getSQLSyncCode("location_groups", $billReturn['PurchaseReturn']['location_group_id']);
                    $restCode[$r]['user_id']           = $this->Helper->getSQLSyncCode("users", $user['User']['id']);
                    $restCode[$r]['dbtype']  = 'saveInv,GroupDetail';
                    $restCode[$r]['actodo']  = 'inv';
                    $r++;
                    
                    $this->PurchaseReturnReceive->create();
                    $dnExpried = array();
                    $dnExpried['PurchaseReturnReceive']['purchase_return_id'] = $this->data['bill_return_id'];
                    $dnExpried['PurchaseReturnReceive']['purchase_return_detail_id'] = $this->data['bill_return_detail_id'];
                    $dnExpried['PurchaseReturnReceive']['product_id']   = $this->data['product_id'];
                    $dnExpried['PurchaseReturnReceive']['qty']          = $_POST['qty_pick'][$i];
                    $dnExpried['PurchaseReturnReceive']['qty_uom_id']   = $_POST['uom'][$i];
                    $dnExpried['PurchaseReturnReceive']['lots_number']  = $_POST['lots_number'][$i]!=''?$_POST['lots_number'][$i]:0;
                    $dnExpried['PurchaseReturnReceive']['expired_date'] = $_POST['expired_date'][$i]!=''?$_POST['expired_date'][$i]:'0000-00-00';
                    $this->PurchaseReturnReceive->save($dnExpried);
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($dnExpried['PurchaseReturnReceive'], 'purchase_return_receives');
                    $restCode[$r]['dbtodo']   = 'purchase_return_receives';
                    $restCode[$r]['actodo']   = 'is';
                    $r++;
                }   
                // Save File Send
                $this->Helper->sendFileToSync($restCode, 0, 0);
                $this->Helper->saveUserActivity($user['User']['id'], 'Bill Return', 'Save Product Pick One', $this->data['bill_return_id']);
                $invalid['success'] = 1;
            }else{
                $this->Helper->saveUserActivity($user['User']['id'], 'Bill Return', 'Save Product Pick One (Existed)', $this->data['bill_return_id']);
                $invalid['ready'] = 1;
            }
            echo json_encode($invalid);
            exit();
        }
    }
    
    function searchVendor() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $userPermission = 'Vendor.id IN (SELECT vendor_id FROM vendor_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id ='.$user['User']['id'].'))';
        $vendors = ClassRegistry::init('Vendor')->find('all', array(
                    'conditions' => array('OR' => array(
                            'Vendor.name LIKE' => '%'.$this->params['url']['q'].'%',
                            'Vendor.vendor_code LIKE' => '%'.$this->params['url']['q'].'%',
                        ), 'Vendor.is_active' => 1, $userPermission
                    ),
                ));
        if (!empty($vendors)) {
            foreach ($vendors as $vendor) {
                $queryNetDays = mysql_query('SELECT (SELECT net_days FROM payment_terms WHERE id=payment_term_id) FROM vendors WHERE id=' . $vendor['Vendor']['id']);
                $dataNetDays  = mysql_fetch_array($queryNetDays);
                $sqlCompany   = mysql_query("SELECT GROUP_CONCAT(company_id) AS company_id FROM vendor_companies WHERE vendor_id = ".$vendor['Vendor']['id']);
                $rowCompany   = mysql_fetch_array($sqlCompany);
                echo "{$vendor['Vendor']['id']}.*{$vendor['Vendor']['name']}.*{$vendor['Vendor']['vendor_code']}.*{$dataNetDays[0]}.*{$rowCompany[0]}\n";
            }
        }
        exit;
    }
    
    function purchaseBill($companyId = null, $branchId = null, $vendorId = ''){
        $this->layout = 'ajax';
        $this->set(compact('companyId', 'branchId', 'vendorId'));
    }
    
    function purchaseBillAjax($companyId, $branchId, $vendorId = ''){
        $this->layout = 'ajax';
        $this->set(compact('companyId', 'branchId', 'vendorId'));
    }
    
    function getProductFromBill($id = null){
        $this->layout = 'ajax';
        $result = array();
        if (empty($id)) {
            $result['error'] = 1;
            echo json_encode($result);
            exit;
        }
        $purchaseBill = ClassRegistry::init('PurchaseOrder')->read(null, $id);
        $user  = $this->getCurrentUser();
        $rowList = array();
        $rowLbl  = "";
        // Get Product
        $sqlQuoteDetail  = mysql_query("SELECT products.code AS code, products.barcode AS barcode, products.name AS name, products.price_uom_id AS price_uom_id, products.small_val_uom AS small_val_uom, purchase_orders_details.product_id AS product_id, purchase_orders_details.unit_price AS unit_price, purchase_orders_details.total_price AS total_price, purchase_orders_details.qty AS qty, purchase_orders_details.qty_uom_id AS qty_uom_id, purchase_orders_details.conversion AS conversion, purchase_orderss.customer_id AS customer_id FROM purchase_orders_details INNER JOIN purchase_orderss ON purchase_orderss.id = purchase_orders_details.purchase_orders_id INNER JOIN products ON products.id = purchase_orders_details.product_id WHERE purchase_orders_details.purchase_orders_id = ".$id.";");
        while($rowDetail = mysql_fetch_array($sqlQuoteDetail)){
            $index   = rand();
            $productName = str_replace('"', '&quot;', $rowDetail['name']);
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
            while($data=mysql_fetch_array($query)){
                $selected   = "";
                $isMain     = "other";
                $isSmall    = 0;
                $conversion = ($rowDetail['small_val_uom'] / $data['conversion']);
                // Check With Qty UOM Id
                if($data['id'] == $rowDetail['qty_uom_id']){   
                    $selected = ' selected="selected" ';
                }
                // Check With Product UOM Id
                if($data['id'] == $rowDetail['price_uom_id']){
                    $isMain = "first";
                }
                // Check Is Small UOM
                if($length == $i){
                    $isSmall = 1;
                }
                $optionUom .= '<option '.$selected.' data-sm="'.$isSmall.'" data-item="'.$isMain.'" value="'.$data['id'].'" conversion="'.$data['conversion'].'">'.$data['name'].'</option>';
                $i++;
            }
            // Open Tr
            $rowLbl .= '<tr class="tblPurchaseReturnList">';
            // Index
            $rowLbl .= '<td class="first" style="width:7%; text-align: center; padding: 0px; height: 30px;">'.++$index.'</td>';
            // SKU
            $rowLbl .= '<td style="width:10%; text-align: left; padding: 5px;"><input type="text" class="lblSKU" readonly="readonly" style="width: 90%;" value="'.$rowDetail['code'].'" /></td>';
            // Product
            $rowLbl .= '<td style="width:24%; text-align: center; padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="hidden" name="inv_qty[]" />';
            $rowLbl .= '<input type="hidden" name="total_qty[]" class="total_qty" />';
            $rowLbl .= '<input type="hidden" name="product_id[]" class="product_id" />';
            $rowLbl .= '<input type="hidden" name="service_id[]" />';
            $rowLbl .= '<input type="hidden" value="1" name="small_val_uom[]" class="small_val_uom" />';
            $rowLbl .= '<input type="hidden" value="1" name="conversion[]" class="conversion" />';
            $rowLbl .= '<input type="hidden" name="note[]" class="note" />';
            $rowLbl .= '<input type="text" id="product" readonly="readonly" name="product[]" class="product validate[required]" style="width: 80%;" value="'.$productName.'" />';
            $rowLbl .= '<img alt="Note" src="'.$this->webroot.'img/button/note.png" class="notePR" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Note\')" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Qty Input
            $rowLbl .= '<td style="width:10%; text-align: center;padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="text" id="qty" name="qty[]" style="width:80%;" class="qty" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // UOM
            $rowLbl .= '<td style="width:15%; padding: 0px; text-align: center">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<select id="qty_uom_id" style="width:80%; height: 20px;" name="qty_uom_id[]" class="qty_uom_id validate[required]" >'.$optionUom.'</select>';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Unit Cost
            $rowLbl .= '<td style="width: 12%; padding: 0px; text-align: center">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="hidden" class="org_price" />';
            $rowLbl .= '<input type="text" id="unit_price" name="unit_price[]" style="width:80%;" class="unit_price float validate[required]" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Total Cost
            $rowLbl .= '<td style="width:12%; text-align: center; padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="text" name="total_price[]" style="width:80%" class="total_price float" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Button Remove
            $rowLbl .= '<td style="width:10%; text-align: center; padding: 0px;">';
            $rowLbl .= '<img alt="Remove" src="'.$this->webroot.'img/button/cross.png" class="btnRemoveSO" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Remove\')" />';
            $rowLbl .= '&nbsp; <img alt="Up" src="'.$this->webroot .'img/button/move_up.png" class="btnMoveUpBR" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Up\')" />';
            $rowLbl .= '&nbsp; <img alt="Down" src="'.$this->webroot .'img/button/move_down.png" class="btnMoveDownBR" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Down\')" />';
            $rowLbl .= '</td>';
            // Close Tr
            $rowLbl .= '</tr>';
        }
        // Get Service
        $sqlQuoteService  = mysql_query("SELECT services.code AS code, services.name AS name, uoms.abbr AS uom, uoms.id AS uom_id, purchase_orders_services.service_id AS service_id, purchase_orders_services.unit_price AS unit_price, purchase_orders_services.total_price AS total_price, purchase_orders_services.qty AS qty, purchase_orders_services.conversion AS conversion, purchase_orders_services.discount_id AS discount_id, purchase_orders_services.discount_amount AS discount_amount, purchase_orders_services.discount_percent AS discount_percent FROM purchase_orders_services INNER JOIN services ON services.id = purchase_orders_services.service_id INNER JOIN uoms ON uoms.id = services.uom_id WHERE purchase_orders_services.purchase_orders_id = ".$id.";");
        while($rowService = mysql_fetch_array($sqlQuoteService)){
            $index   = rand();
            // Qty
            $qty = $rowService['qty'];
            // Get UOM
            $optionUom = '<option value="'.$rowService['uom_id'].'" conversion="1" selected="selected">'.$rowService['uom'].'</option>';
            // Open Tr
            $rowLbl .= '<tr class="tblPurchaseReturnList">';
            // Index
            $rowLbl .= '<td class="first" style="width:7%; text-align: center; padding: 0px; height: 30px;">'.++$index.'</td>';
            // SKU
            $rowLbl .= '<td style="width:10%; text-align: left; padding: 5px;"><input type="text" class="lblSKU" readonly="readonly" style="width: 90%;" value="'.$rowDetail['code'].'" /></td>';
            // Product
            $rowLbl .= '<td style="width:24%; text-align: center; padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="hidden" name="inv_qty[]" />';
            $rowLbl .= '<input type="hidden" name="total_qty[]" class="total_qty" />';
            $rowLbl .= '<input type="hidden" name="product_id[]" class="product_id" />';
            $rowLbl .= '<input type="hidden" name="service_id[]" />';
            $rowLbl .= '<input type="hidden" value="1" name="small_val_uom[]" class="small_val_uom" />';
            $rowLbl .= '<input type="hidden" value="1" name="conversion[]" class="conversion" />';
            $rowLbl .= '<input type="hidden" name="note[]" class="note" />';
            $rowLbl .= '<input type="text" id="product" readonly="readonly" name="product[]" class="product validate[required]" style="width: 80%;" />';
            $rowLbl .= '<img alt="Note" src="'.$this->webroot.'img/button/note.png" class="notePR" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Note\')" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Qty Input
            $rowLbl .= '<td style="width:10%; text-align: center;padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="text" id="qty" name="qty[]" style="width:80%;" class="qty" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // UOM
            $rowLbl .= '<td style="width:15%; padding: 0px; text-align: center">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<select id="qty_uom_id" style="width:80%; height: 20px;" name="qty_uom_id[]" class="qty_uom_id validate[required]" >'.$optionUom.'</select>';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Unit Cost
            $rowLbl .= '<td style="width: 12%; padding: 0px; text-align: center">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="hidden" class="org_price" />';
            $rowLbl .= '<input type="text" id="unit_price" name="unit_price[]" style="width:80%;" class="unit_price float validate[required]" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Total Cost
            $rowLbl .= '<td style="width:12%; text-align: center; padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="text" name="total_price[]" style="width:80%" class="total_price float" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Button Remove
            $rowLbl .= '<td style="width:10%; text-align: center; padding: 0px;">';
            $rowLbl .= '<img alt="Remove" src="'.$this->webroot.'img/button/cross.png" class="btnRemoveSO" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Remove\')" />';
            $rowLbl .= '&nbsp; <img alt="Up" src="'.$this->webroot .'img/button/move_up.png" class="btnMoveUpBR" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Up\')" />';
            $rowLbl .= '&nbsp; <img alt="Down" src="'.$this->webroot .'img/button/move_down.png" class="btnMoveDownBR" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Down\')" />';
            $rowLbl .= '</td>';
            // Close Tr
            $rowLbl .= '</tr>';
        }
        // Get Miscs
        $sqlQuoteMisc  = mysql_query("SELECT purchase_orders_miscs.description AS description, purchase_orders_miscs.unit_price AS unit_price, purchase_orders_miscs.total_price AS total_price, purchase_orders_miscs.qty AS qty, purchase_orders_miscs.qty_uom_id AS qty_uom_id, purchase_orders_miscs.conversion AS conversion, purchase_orders_miscs.discount_id AS discount_id, purchase_orders_miscs.discount_amount AS discount_amount, purchase_orders_miscs.discount_percent AS discount_percent FROM purchase_orders_miscs WHERE purchase_orders_miscs.purchase_orders_id = ".$id.";");
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
            $rowLbl .= '<tr class="tblPurchaseReturnList">';
            // Index
            $rowLbl .= '<td class="first" style="width:7%; text-align: center; padding: 0px; height: 30px;">'.++$index.'</td>';
            // SKU
            $rowLbl .= '<td style="width:10%; text-align: left; padding: 5px;"><input type="text" class="lblSKU" readonly="readonly" style="width: 90%;" value="'.$rowDetail['code'].'" /></td>';
            // Product
            $rowLbl .= '<td style="width:24%; text-align: center; padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="hidden" name="inv_qty[]" />';
            $rowLbl .= '<input type="hidden" name="total_qty[]" class="total_qty" />';
            $rowLbl .= '<input type="hidden" name="product_id[]" class="product_id" />';
            $rowLbl .= '<input type="hidden" name="service_id[]" />';
            $rowLbl .= '<input type="hidden" value="1" name="small_val_uom[]" class="small_val_uom" />';
            $rowLbl .= '<input type="hidden" value="1" name="conversion[]" class="conversion" />';
            $rowLbl .= '<input type="hidden" name="note[]" class="note" />';
            $rowLbl .= '<input type="text" id="product" readonly="readonly" name="product[]" class="product validate[required]" style="width: 80%;" />';
            $rowLbl .= '<img alt="Note" src="'.$this->webroot.'img/button/note.png" class="notePR" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Note\')" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Qty Input
            $rowLbl .= '<td style="width:10%; text-align: center;padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="text" id="qty" name="qty[]" style="width:80%;" class="qty" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // UOM
            $rowLbl .= '<td style="width:15%; padding: 0px; text-align: center">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<select id="qty_uom_id" style="width:80%; height: 20px;" name="qty_uom_id[]" class="qty_uom_id validate[required]" >'.$optionUom.'</select>';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Unit Cost
            $rowLbl .= '<td style="width: 12%; padding: 0px; text-align: center">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="hidden" class="org_price" />';
            $rowLbl .= '<input type="text" id="unit_price" name="unit_price[]" style="width:80%;" class="unit_price float validate[required]" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Total Cost
            $rowLbl .= '<td style="width:12%; text-align: center; padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="text" name="total_price[]" style="width:80%" class="total_price float" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Button Remove
            $rowLbl .= '<td style="width:10%; text-align: center; padding: 0px;">';
            $rowLbl .= '<img alt="Remove" src="'.$this->webroot.'img/button/cross.png" class="btnRemoveSO" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Remove\')" />';
            $rowLbl .= '&nbsp; <img alt="Up" src="'.$this->webroot .'img/button/move_up.png" class="btnMoveUpBR" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Up\')" />';
            $rowLbl .= '&nbsp; <img alt="Down" src="'.$this->webroot .'img/button/move_down.png" class="btnMoveDownBR" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Down\')" />';
            $rowLbl .= '</td>';
            // Close Tr
            $rowLbl .= '</tr>';
        }
        $rowList['error']  = 0;
        $rowList['result'] = $rowLbl;
        echo json_encode($rowList);
        exit;
    }
    
    function getProductByExp($productId, $locationId, $orderDate = 0){
        $this->layout = 'ajax';
        if(empty($productId) || empty($locationId) || empty($orderDate)){
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $this->set(compact('productId', 'orderDate', 'locationId'));
    }
    
}

?>
