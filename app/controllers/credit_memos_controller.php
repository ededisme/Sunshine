<?php

class CreditMemosController extends AppController {

    var $name = 'CreditMemos';
    var $components = array('Helper', 'ProductCom', 'Inventory');

    function index() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $this->set('user', $user);
        $this->Helper->saveUserActivity($user['User']['id'], 'Sales Return', 'Dashboard');
    }

    function ajax($customer = 'all', $filterStatus = 'all', $balance = 'all', $date = '') {
        $this->layout = 'ajax';
        $this->set(compact('customer', 'filterStatus', 'balance', 'date'));
    }

    function view($id = null) {
        $this->layout = 'ajax';
        if (!empty($id)) {
            $user = $this->getCurrentUser();
            $this->Helper->saveUserActivity($user['User']['id'], 'Sales Return', 'View', $id);
            $this->data = $this->CreditMemo->read(null, $id);
            if (!empty($this->data)) {
                $creditMemoDetails = ClassRegistry::init('CreditMemoDetail')->find("all", array('conditions' => array('CreditMemoDetail.credit_memo_id' => $id)));
                $creditMemoMiscs = ClassRegistry::init('CreditMemoMisc')->find("all", array('conditions' => array('CreditMemoMisc.credit_memo_id' => $id)));
                $creditMemoServices = ClassRegistry::init('CreditMemoService')->find("all", array('conditions' => array('CreditMemoService.credit_memo_id' => $id)));
                $creditMemoReceipts = ClassRegistry::init('CreditMemoReceipt')->find("all", array('conditions' => array('CreditMemoReceipt.credit_memo_id' => $id, 'CreditMemoReceipt.is_void' => 0)));
                $cmWsales = ClassRegistry::init('CreditMemoWithSale')->find("all", array('conditions' => array('CreditMemoWithSale.credit_memo_id' => $id, 'CreditMemoWithSale.status>0')));
                $this->set(compact('creditMemo', 'creditMemoDetails', 'creditMemoReceipts', 'creditMemoMiscs', 'creditMemoServices', 'cmWsales'));
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
            $result = array();
            // Load Table
            $this->loadModel('CreditMemoDetail');
            $this->loadModel('CreditMemoMiscs');
            $this->loadModel('CreditMemoService');
            $this->loadModel('GeneralLedger');
            $this->loadModel('GeneralLedgerDetail');
            $this->loadModel('InventoryValuation');
            $this->loadModel('AccountType');
            $this->loadModel('Company');

            //  Find Chart Account
            $arAccount = $this->AccountType->findById(7);
            $salesMiscAccount = $this->AccountType->findById(10);
            $salesDiscAccount = $this->AccountType->findById(11);
            $salesMarkUpAccount = $this->AccountType->findById(17);
            $total_balance = ($this->data['CreditMemo']['total_amount'] + $this->data['CreditMemo']['mark_up'] + $this->data['CreditMemo']['total_vat']) - $this->data['CreditMemo']['discount'];
            // Sales Return
            $this->CreditMemo->create();
            $creditMemo = array();
            $creditMemo['CreditMemo']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
            $creditMemo['CreditMemo']['created']    = $dateNow;
            $creditMemo['CreditMemo']['created_by'] = $user['User']['id'];
            $creditMemo['CreditMemo']['company_id'] = $this->data['CreditMemo']['company_id'];
            $creditMemo['CreditMemo']['branch_id']  = $this->data['CreditMemo']['branch_id'];
            $creditMemo['CreditMemo']['location_group_id'] = $this->data['CreditMemo']['location_group_id'];
            $creditMemo['CreditMemo']['location_id'] = $this->data['CreditMemo']['location_id'];
            $creditMemo['CreditMemo']['customer_id'] = $this->data['CreditMemo']['customer_id'];
            $creditMemo['CreditMemo']['patient_id'] = $this->data['CreditMemo']['customer_id'];
            $creditMemo['CreditMemo']['currency_center_id'] = $this->data['CreditMemo']['currency_center_id'];
            $creditMemo['CreditMemo']['reason_id']      = $this->data['CreditMemo']['reason_id'];
            $creditMemo['CreditMemo']['sales_order_id'] = $this->data['CreditMemo']['sales_order_id'];
            $creditMemo['CreditMemo']['invoice_code']   = $this->data['CreditMemo']['invoice_code'];
            $creditMemo['CreditMemo']['invoice_date']   = ((empty($this->data['CreditMemo']['invoice_date']))?'0000-00-00':$this->data['CreditMemo']['invoice_date']);
            $creditMemo['CreditMemo']['note']    = $this->data['CreditMemo']['note'];
            $creditMemo['CreditMemo']['ar_id']   = $arAccount['AccountType']['chart_account_id'];
            $creditMemo['CreditMemo']['cm_code'] = $this->data['CreditMemo']['cm_code'];
            $creditMemo['CreditMemo']['balance'] = $total_balance;
            $creditMemo['CreditMemo']['total_amount'] = $this->data['CreditMemo']['total_amount'];
            $creditMemo['CreditMemo']['mark_up']      = $this->data['CreditMemo']['mark_up'];
            $creditMemo['CreditMemo']['discount']     = $this->data['CreditMemo']['discount'];
            $creditMemo['CreditMemo']['discount_percent'] = $this->data['CreditMemo']['discount_percent'];
            $creditMemo['CreditMemo']['order_date']  = $this->data['CreditMemo']['order_date'];
            $creditMemo['CreditMemo']['due_date']    = ((empty($this->data['CreditMemo']['due_date']))?'0000-00-00':$this->data['CreditMemo']['due_date']);
            $creditMemo['CreditMemo']['status']      = 2;
            $creditMemo['CreditMemo']['total_vat']   = $this->data['CreditMemo']['total_vat'];
            $creditMemo['CreditMemo']['vat_percent'] = $this->data['CreditMemo']['vat_percent'];
            $creditMemo['CreditMemo']['vat_setting_id'] = $this->data['CreditMemo']['vat_setting_id'];
            $creditMemo['CreditMemo']['vat_calculate']  = $this->data['CreditMemo']['vat_calculate'];
            $creditMemo['CreditMemo']['vat_chart_account_id'] = $this->data['CreditMemo']['vat_chart_account_id'];
            $creditMemo['CreditMemo']['price_type_id'] = $this->data['CreditMemo']['price_type_id'];
            if ($this->CreditMemo->save($creditMemo)) {
                $result['so_id'] = $creditMemoId = $this->CreditMemo->id;
                $company         = $this->Company->read(null, $this->data['CreditMemo']['company_id']);
                $classId         = $this->Helper->getClassId($company['Company']['id'], $company['Company']['classes'], $this->data['CreditMemo']['location_group_id']);
                // Get Module Code
                $modCode = $this->Helper->getModuleCode($this->data['CreditMemo']['cm_code'], $creditMemoId, 'cm_code', 'credit_memos', 'status != -1 AND branch_id = '.$this->data['CreditMemo']['branch_id']);
                // Updaet Module Code
                mysql_query("UPDATE credit_memos SET cm_code = '".$modCode."' WHERE id = ".$creditMemoId);
                // Convert to REST
                $restCode[$r] = $this->Helper->convertToDataSync($creditMemo['CreditMemo'], 'credit_memos');
                $restCode[$r]['cm_code']  = $modCode;
                $restCode[$r]['modified'] = $dateNow;
                $restCode[$r]['dbtodo']   = 'credit_memos';
                $restCode[$r]['actodo']   = 'is';
                $r++;
                // Create General Ledger
                $this->GeneralLedger->create();
                $generalLedger = array();
                $generalLedger['GeneralLedger']['sys_code']       = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $generalLedger['GeneralLedger']['credit_memo_id'] = $creditMemoId;
                $generalLedger['GeneralLedger']['date']       = $this->data['CreditMemo']['order_date'];
                $generalLedger['GeneralLedger']['reference']  = $modCode;
                $generalLedger['GeneralLedger']['created']    = $dateNow;
                $generalLedger['GeneralLedger']['created_by'] = $user['User']['id'];
                $generalLedger['GeneralLedger']['is_sys']     = 1;
                $generalLedger['GeneralLedger']['is_adj']     = 0;
                $generalLedger['GeneralLedger']['is_active']  = 1;
                $this->GeneralLedger->save($generalLedger);
                $generalLedgerId = $this->GeneralLedger->id;
                // Convert to REST
                $restCode[$r] = $this->Helper->convertToDataSync($generalLedger['GeneralLedger'], 'general_ledgers');
                $restCode[$r]['modified'] = $dateNow;
                $restCode[$r]['dbtodo']   = 'general_ledgers';
                $restCode[$r]['actodo']   = 'is';
                $r++;

                // General Ledger Detail (A/R)
                $generalLedgerDetail = array();
                $this->GeneralLedgerDetail->create();
                $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $generalLedgerId;
                $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id']  = $arAccount['AccountType']['chart_account_id'];
                $generalLedgerDetail['GeneralLedgerDetail']['company_id'] = $creditMemo['CreditMemo']['company_id'];
                $generalLedgerDetail['GeneralLedgerDetail']['branch_id']  = $creditMemo['CreditMemo']['branch_id'];
                $generalLedgerDetail['GeneralLedgerDetail']['location_group_id'] = $creditMemo['CreditMemo']['location_group_id'];
                $generalLedgerDetail['GeneralLedgerDetail']['location_id'] = $creditMemo['CreditMemo']['location_id'];
                $generalLedgerDetail['GeneralLedgerDetail']['type']   = 'Sales Return';
                $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $total_balance;
                $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Sales Return # ' . $modCode;
                $generalLedgerDetail['GeneralLedgerDetail']['customer_id'] = $creditMemo['CreditMemo']['customer_id'];
                $generalLedgerDetail['GeneralLedgerDetail']['class_id']    = $classId;
                $this->GeneralLedgerDetail->save($generalLedgerDetail);
                // Convert to REST
                $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                $restCode[$r]['dbtodo']   = 'general_ledger_details';
                $restCode[$r]['actodo']   = 'is';
                $r++;
                    
                // General Ledger Detail (Total Discount)
                if ($this->data['CreditMemo']['discount'] > 0) {
                    $this->GeneralLedgerDetail->create();
                    $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $salesDiscAccount['AccountType']['chart_account_id'];
                    $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                    $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $this->data['CreditMemo']['discount'];
                    $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Sales Return # ' . $modCode . ' Total Discount';
                    $this->GeneralLedgerDetail->save($generalLedgerDetail);
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                    $restCode[$r]['dbtodo']   = 'general_ledger_details';
                    $restCode[$r]['actodo']   = 'is';
                    $r++;
                }

                // General Ledger Detail (Total Mark Up)
                if ($this->data['CreditMemo']['mark_up'] > 0) {
                    $this->GeneralLedgerDetail->create();
                    $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $salesMarkUpAccount['AccountType']['chart_account_id'];
                    $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $this->data['CreditMemo']['mark_up'];
                    $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                    $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Sales Return # ' . $modCode . ' Markup';
                    $this->GeneralLedgerDetail->save($generalLedgerDetail);
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                    $restCode[$r]['dbtodo']   = 'general_ledger_details';
                    $restCode[$r]['actodo']   = 'is';
                    $r++;
                }

                // General Ledger Detail (Total VAT)
                if ($creditMemo['CreditMemo']['total_vat'] > 0) {
                    $this->GeneralLedgerDetail->create();
                    $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $this->data['CreditMemo']['vat_chart_account_id'];
                    $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $creditMemo['CreditMemo']['total_vat'];
                    $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                    $generalLedgerDetail['GeneralLedgerDetail']['memo']   = "ICS: Sales Return # " . $modCode . ' Total VAT';
                    $this->GeneralLedgerDetail->save($generalLedgerDetail);
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                    $restCode[$r]['dbtodo']   = 'general_ledger_details';
                    $restCode[$r]['actodo']   = 'is';
                    $r++;
                }

                for ($i = 0; $i < sizeof($_POST['product']); $i++) {
                    if (!empty($_POST['product_id'][$i])) {
                        $creditMemoDetail = array();
                        // Sales Order Detail
                        $this->CreditMemoDetail->create();
                        $creditMemoDetail['CreditMemoDetail']['credit_memo_id'] = $creditMemoId;
                        $creditMemoDetail['CreditMemoDetail']['discount_id'] = $_POST['discount_id'][$i];
                        $creditMemoDetail['CreditMemoDetail']['discount_amount']  = $_POST['discount'][$i];
                        $creditMemoDetail['CreditMemoDetail']['discount_percent'] = $_POST['discount_percent'][$i];
                        $creditMemoDetail['CreditMemoDetail']['product_id'] = $_POST['product_id'][$i];
                        $creditMemoDetail['CreditMemoDetail']['qty'] = $_POST['qty'][$i];
                        $creditMemoDetail['CreditMemoDetail']['qty_free'] = $_POST['qty_free'][$i];
                        $creditMemoDetail['CreditMemoDetail']['qty_uom_id'] = $_POST['qty_uom_id'][$i];
                        $creditMemoDetail['CreditMemoDetail']['unit_price'] = $_POST['unit_price'][$i];
                        $creditMemoDetail['CreditMemoDetail']['total_price'] = $_POST['h_total_price'][$i];
                        $creditMemoDetail['CreditMemoDetail']['lots_number'] = ($_POST['lots_number'][$i]!=""?$_POST['lots_number'][$i]:"0");
                        $creditMemoDetail['CreditMemoDetail']['expired_date'] = ($_POST['expired_date'][$i]!=""?$_POST['expired_date'][$i]:"0000-00-00");
                        $creditMemoDetail['CreditMemoDetail']['conversion'] = ($_POST['cm_conversion'][$i]);
                        $creditMemoDetail['CreditMemoDetail']['note'] = $_POST['note'][$i];
                        $this->CreditMemoDetail->save($creditMemoDetail);
                        $qtyOrder      = (($_POST['qty'][$i] + $_POST['qty_free'][$i]) / ($_POST['small_val_uom'][$i] / $_POST['cm_conversion'][$i]));
                        $qtyOrderSmall = ($_POST['qty'][$i] + $_POST['qty_free'][$i]) * $_POST['cm_conversion'][$i];
                        $priceSales    = $_POST['h_total_price'][$i] - $_POST['discount'][$i];
                        $queryProductCodeName = mysql_query("SELECT CONCAT(code,' - ',name) AS name, unit_cost AS unit_cost FROM products WHERE id=" . $_POST['product_id'][$i]);
                        $dataProductCodeName = mysql_fetch_array($queryProductCodeName);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($creditMemoDetail['CreditMemoDetail'], 'credit_memo_details');
                        $restCode[$r]['dbtodo']   = 'credit_memo_details';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                        
                        // Update Inventory (Sales Return)
                        $data = array();
                        $data['module_type']        = 11;
                        $data['credit_memo_id']     = $creditMemoId;
                        $data['product_id']         = $creditMemoDetail['CreditMemoDetail']['product_id'];
                        $data['location_id']        = $creditMemo['CreditMemo']['location_id'];
                        $data['location_group_id']  = $creditMemo['CreditMemo']['location_group_id'];
                        $data['lots_number']  = $creditMemoDetail['CreditMemoDetail']['lots_number'];
                        $data['expired_date'] = $creditMemoDetail['CreditMemoDetail']['expired_date'];
                        $data['date']         = $creditMemo['CreditMemo']['order_date'];
                        $data['total_qty']    = $qtyOrderSmall;
                        $data['total_order']  = $_POST['qty'][$i] * $_POST['cm_conversion'][$i];
                        $data['total_free']   = $_POST['qty_free'][$i] * $_POST['cm_conversion'][$i];
                        $data['user_id']      = $user['User']['id'];
                        $data['customer_id']  = $creditMemo['CreditMemo']['customer_id'];
                        $data['vendor_id']    = "";
                        $data['unit_cost']    = 0;
                        $data['unit_price']   = $priceSales;
                        // Update Invetory Location
                        $this->Inventory->saveInventory($data);
                        // Update Inventory Group
                        $this->Inventory->saveGroupTotalDetail($data);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($data, 'inventories');
                        $restCode[$r]['module_type']  = 11;
                        $restCode[$r]['total_qty']    = $qtyOrderSmall;
                        $restCode[$r]['total_order']  = $data['total_order'];
                        $restCode[$r]['total_free']   = $data['total_free'];
                        $restCode[$r]['expired_date'] = $data['expired_date'];
                        $restCode[$r]['vendor_id']    = "";
                        $restCode[$r]['unit_cost']    = 0;
                        $restCode[$r]['customer_id']       = $this->Helper->getSQLSyncCode("customers", $creditMemo['CreditMemo']['customer_id']);
                        $restCode[$r]['credit_memo_id']    = $this->Helper->getSQLSyncCode("credit_memos", $creditMemoId);
                        $restCode[$r]['product_id']        = $this->Helper->getSQLSyncCode("products", $creditMemoDetail['CreditMemoDetail']['product_id']);
                        $restCode[$r]['location_id']       = $this->Helper->getSQLSyncCode("locations", $creditMemo['CreditMemo']['location_id']);
                        $restCode[$r]['location_group_id'] = $this->Helper->getSQLSyncCode("location_groups", $creditMemo['CreditMemo']['location_group_id']);
                        $restCode[$r]['user_id']           = $this->Helper->getSQLSyncCode("users", $user['User']['id']);
                        $restCode[$r]['dbtype']  = 'saveInv,GroupDetail';
                        $restCode[$r]['actodo']  = 'inv';
                        $r++;
                        
                        if($this->data['calculate_cogs'] == 1){
                            // Inventory Valuation
                            $this->InventoryValuation->create();
                            $inv_valutaion = array();
                            $inv_valutaion['InventoryValuation']['sys_code']       = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                            $inv_valutaion['InventoryValuation']['credit_memo_id'] = $creditMemoId;
                            $inv_valutaion['InventoryValuation']['company_id'] = $this->data['CreditMemo']['company_id'];
                            $inv_valutaion['InventoryValuation']['branch_id']  = $this->data['CreditMemo']['branch_id'];
                            $inv_valutaion['InventoryValuation']['type'] = "Sales Return";
                            $inv_valutaion['InventoryValuation']['reference']   = $modCode;
                            $inv_valutaion['InventoryValuation']['customer_id'] = $creditMemo['CreditMemo']['customer_id'];
                            $inv_valutaion['InventoryValuation']['date'] = $this->data['CreditMemo']['order_date'];
                            $inv_valutaion['InventoryValuation']['pid'] = $_POST['product_id'][$i];
                            $inv_valutaion['InventoryValuation']['small_qty'] = $qtyOrderSmall;
                            $inv_valutaion['InventoryValuation']['qty'] = $this->Helper->replaceThousand(number_format($qtyOrder, 6));
                            $inv_valutaion['InventoryValuation']['cost'] = null;
                            $inv_valutaion['InventoryValuation']['is_var_cost'] = 1;
                            $inv_valutaion['InventoryValuation']['created'] = $dateNow;
                            $this->InventoryValuation->save($inv_valutaion);
                            $inv_valutation_id = $this->InventoryValuation->getLastInsertId();
                            $inventoryAsset = 0;
                            $cogs = 0;
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($inv_valutaion['InventoryValuation'], 'inventory_valuations');
                            $restCode[$r]['dbtodo']   = 'inventory_valuations';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                        }else{
                            $inv_valutation_id = NULL;
                            $inventoryAsset = $dataProductCodeName[1];
                            $cogs = $dataProductCodeName[1];
                        }

                        // General Ledger Detail (Product Income)
                        $this->GeneralLedgerDetail->create();
                        $queryIncAccount = mysql_query("SELECT IFNULL((IFNULL((SELECT chart_account_id FROM accounts WHERE product_id = " . $_POST['product_id'][$i] . " AND account_type_id=8),(SELECT chart_account_id FROM pgroup_accounts WHERE pgroup_id = (SELECT pgroup_id FROM product_pgroups WHERE product_id = " . $_POST['product_id'][$i] . " ORDER BY id  DESC LIMIT 1) AND account_type_id=8))),(SELECT chart_account_id FROM account_types WHERE id=8))");
                        $dataIncAccount = mysql_fetch_array($queryIncAccount);
                        $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $dataIncAccount[0];
                        $generalLedgerDetail['GeneralLedgerDetail']['product_id'] = $creditMemoDetail['CreditMemoDetail']['product_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['service_id'] = NULL;
                        $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_id'] = NULL;
                        $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_is_debit'] = NULL;
                        $generalLedgerDetail['GeneralLedgerDetail']['type']   = 'Sales Return';
                        $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $_POST['h_total_price'][$i];
                        $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                        $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Sales Return # ' . $modCode . ' Product # ' . $_POST['product'][$i];
                        $this->GeneralLedgerDetail->save($generalLedgerDetail);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                        $restCode[$r]['dbtodo']   = 'general_ledger_details';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;

                        // General Ledger Detail (Product Discount)
                        if ($_POST['discount'][$i] > 0) {
                            $this->GeneralLedgerDetail->create();
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $salesDiscAccount['AccountType']['chart_account_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $_POST['discount'][$i];
                            $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Sales Return # ' . $modCode . ' Product # ' . $_POST['product'][$i] . ' Discount';
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                        }

                        // Update GL for Inventory
                        $this->GeneralLedgerDetail->create();
                        $queryInvAccount = mysql_query("SELECT IFNULL((IFNULL((SELECT chart_account_id FROM accounts WHERE product_id = " . $_POST['product_id'][$i] . " AND account_type_id=1),(SELECT chart_account_id FROM pgroup_accounts WHERE pgroup_id = (SELECT pgroup_id FROM product_pgroups WHERE product_id = " . $_POST['product_id'][$i] . " ORDER BY id  DESC LIMIT 1) AND account_type_id=1))),(SELECT chart_account_id FROM account_types WHERE id=1))");
                        $dataInvAccount  = mysql_fetch_array($queryInvAccount);
                        $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $dataInvAccount[0];
                        $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_id'] = $inv_valutation_id;
                        $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_is_debit'] = 1;
                        $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $inventoryAsset;
                        $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                        $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Inventory for Sales Return # ' . $modCode . ' Product # ' . $dataProductCodeName[0];
                        $this->GeneralLedgerDetail->save($generalLedgerDetail);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                        $restCode[$r]['dbtodo']   = 'general_ledger_details';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;

                        // Update GL for COGS
                        $this->GeneralLedgerDetail->create();
                        $queryCogsAccount = mysql_query("SELECT IFNULL((IFNULL((SELECT chart_account_id FROM accounts WHERE product_id = " . $_POST['product_id'][$i] . " AND account_type_id=2),(SELECT chart_account_id FROM pgroup_accounts WHERE pgroup_id = (SELECT pgroup_id FROM product_pgroups WHERE product_id = " . $_POST['product_id'][$i] . " ORDER BY id  DESC LIMIT 1) AND account_type_id=2))),(SELECT chart_account_id FROM account_types WHERE id=2))");
                        $dataCogsAccount  = mysql_fetch_array($queryCogsAccount);
                        $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $dataCogsAccount[0];
                        $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_id'] = $inv_valutation_id;
                        $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_is_debit'] = 0;
                        $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                        $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $cogs;
                        $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: COGS for Sales Return # ' . $modCode . ' Product # ' . $dataProductCodeName[0];
                        $this->GeneralLedgerDetail->save($generalLedgerDetail);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                        $restCode[$r]['dbtodo']   = 'general_ledger_details';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;

                    } else if (!empty($_POST['service_id'][$i])) {
                        // Sales Return Service
                        $creditMemoService = array();
                        $this->CreditMemoService->create();
                        $creditMemoService['CreditMemoService']['credit_memo_id'] = $creditMemoId;
                        $creditMemoService['CreditMemoService']['discount_id'] = $_POST['discount_id'][$i];
                        $creditMemoService['CreditMemoService']['discount_amount']  = $_POST['discount'][$i];
                        $creditMemoService['CreditMemoService']['discount_percent'] = $_POST['discount_percent'][$i];
                        $creditMemoService['CreditMemoService']['service_id'] = $_POST['service_id'][$i];
                        $creditMemoService['CreditMemoService']['qty'] = $_POST['qty'][$i];
                        $creditMemoService['CreditMemoService']['qty_free'] = $_POST['qty_free'][$i];
                        $creditMemoService['CreditMemoService']['unit_price'] = $_POST['unit_price'][$i];
                        $creditMemoService['CreditMemoService']['total_price'] = $_POST['h_total_price'][$i];
                        $creditMemoService['CreditMemoService']['note'] = $_POST['note'][$i];
                        $this->CreditMemoService->save($creditMemoService);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($creditMemoService['CreditMemoService'], 'credit_memo_services');
                        $restCode[$r]['dbtodo']   = 'credit_memo_services';
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
                        $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $_POST['h_total_price'][$i];
                        $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                        $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Sales Return # ' . $modCode . ' Service # ' . $_POST['service_id'][$i];
                        $this->GeneralLedgerDetail->save($generalLedgerDetail);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                        $restCode[$r]['dbtodo']   = 'general_ledger_details';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;

                        // General Ledger Detail (Service Discount)
                        if ($_POST['discount'][$i] > 0) {
                            $this->GeneralLedgerDetail->create();
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $salesDiscAccount['AccountType']['chart_account_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $_POST['discount'][$i];
                            $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Sales Return # ' . $modCode . ' Service # ' . $_POST['service_id'][$i] . ' Discount';
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                        }

                    } else {
                        // Sales Return Miscellaneous
                        $creditMemoMiscs = array();
                        $this->CreditMemoMiscs->create();
                        $creditMemoMiscs['CreditMemoMiscs']['credit_memo_id'] = $creditMemoId;
                        $creditMemoMiscs['CreditMemoMiscs']['discount_id'] = $_POST['discount_id'][$i];
                        $creditMemoMiscs['CreditMemoMiscs']['discount_amount']  = $_POST['discount'][$i];
                        $creditMemoMiscs['CreditMemoMiscs']['discount_percent'] = $_POST['discount_percent'][$i];
                        $creditMemoMiscs['CreditMemoMiscs']['description'] = $_POST['product'][$i];
                        $creditMemoMiscs['CreditMemoMiscs']['qty_uom_id'] = $_POST['qty_uom_id'][$i];
                        $creditMemoMiscs['CreditMemoMiscs']['qty'] = $_POST['qty'][$i];
                        $creditMemoMiscs['CreditMemoMiscs']['qty_free'] = $_POST['qty_free'][$i];
                        $creditMemoMiscs['CreditMemoMiscs']['unit_price'] = $_POST['unit_price'][$i];
                        $creditMemoMiscs['CreditMemoMiscs']['total_price'] = $_POST['h_total_price'][$i];
                        $creditMemoMiscs['CreditMemoMiscs']['note'] = $_POST['note'][$i];
                        $this->CreditMemoMiscs->save($creditMemoMiscs);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($creditMemoMiscs['CreditMemoMiscs'], 'credit_memo_miscs');
                        $restCode[$r]['dbtodo']   = 'credit_memo_miscs';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;

                        // General Ledger Detail (Misc)
                        $this->GeneralLedgerDetail->create();
                        $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $salesMiscAccount['AccountType']['chart_account_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['service_id'] = NULL;
                        $generalLedgerDetail['GeneralLedgerDetail']['product_id'] = NULL;
                        $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_id'] = NULL;
                        $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_is_debit'] = NULL;
                        $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $_POST['h_total_price'][$i];
                        $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                        $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Sales Return # ' . $modCode . ' Misc # ' . $_POST['product'][$i];
                        $this->GeneralLedgerDetail->save($generalLedgerDetail);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                        $restCode[$r]['dbtodo']   = 'general_ledger_details';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;

                        // General Ledger Detail (Misc Discount)
                        if ($_POST['discount'][$i] > 0) {
                            $this->GeneralLedgerDetail->create();
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id']  = $salesDiscAccount['AccountType']['chart_account_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $_POST['discount'][$i];
                            $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Sales Return # ' . $modCode . ' Misc # ' . $_POST['product'][$i] . ' Discount';
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
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
                mysql_query("UPDATE tracks SET val='".$this->data['CreditMemo']['order_date']."', is_recalculate = 1 WHERE id=1");
                $this->Helper->saveUserActivity($user['User']['id'], 'Sales Return', 'Save Add New', $creditMemoId);
                echo json_encode($result);
                exit;
            } else {
                $this->Helper->saveUserActivity($user['User']['id'], 'Sales Return', 'Save Add New (Error)');
                $result['code'] = 2;
                echo json_encode($result);
                exit;
            }
        }
        $this->Helper->saveUserActivity($user['User']['id'], 'Sales Return', 'Add New');
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
                            'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id', 'ModuleCodeBranch.cm_code', 'Branch.currency_center_id', 'CurrencyCenter.symbol'),
                            'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])
                        ));
        // Get Loction Setting
        $locSetting = ClassRegistry::init('LocationSetting')->findById(5);
        $locCon     = '';
        if($locSetting['LocationSetting']['location_status'] == 1){
            $locCon = ' AND Location.is_for_sale = 0';
        }
        $joinUsers    = array('table' => 'user_location_groups', 'type' => 'INNER', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'));
        $joinLocation = array('table' => 'locations', 'type' => 'INNER', 'conditions' => array('locations.location_group_id=LocationGroup.id', $locCon));
        $locations    = ClassRegistry::init('Location')->find('all', array('joins' => array(array('table' => 'user_locations', 'type' => 'inner', 'conditions' => array('user_locations.location_id=Location.id'))), 'conditions' => array('user_locations.user_id=' . $user['User']['id'] . ' AND Location.is_active=1'.$locCon), 'order' => 'Location.name'));
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('fields' => array('LocationGroup.id', 'LocationGroup.name'),'joins' => array($joinUsers, $joinLocation),'conditions' => array('user_location_groups.user_id=' . $user['User']['id'], 'LocationGroup.is_active' => '1', 'LocationGroup.location_group_type_id != 1'), 'group' => 'LocationGroup.id'));
        $reasons = ClassRegistry::init('Reason')->find('list', array('conditions' => array('Reason.is_active' => '1'), 'order' => 'Reason.name'));
        $code = $this->Helper->getAutoGenerateCreditMemoCode();
        $this->set(compact("locations", "code", "locationGroups", "reasons", "companies", "branches"));
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
                            'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id', 'ModuleCodeBranch.cm_code', 'Branch.currency_center_id', 'CurrencyCenter.symbol'),
                            'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])
                        ));
        $uoms = ClassRegistry::init('Uom')->find('all', array('fields' => array('Uom.id', 'Uom.name'), 'conditions' => array('Uom.is_active' => 1)));
        $this->set(compact("branches", "uoms"));
    }

    function miscellaneous() {
        $this->layout = 'ajax';
    }

    function discount() {
        $this->layout = 'ajax';
        $discounts = ClassRegistry::init('Discount')->find("all", array('conditions' => array('Discount.is_active' => 1), 'order' => array('id DESC')));
        $this->set(compact('discounts'));
    }

    function product($companyId = null, $locationId = null, $branchId = null, $orderDate = null) {
        $this->layout = 'ajax';
        $this->set('companyId', $companyId);
        $this->set('locationId', $locationId);
        $this->set('orderDate', $orderDate);
        $this->set('branchId', $branchId);
    }

    function product_ajax($companyId = null, $locationId = null, $branchId = null, $orderDate = null, $category = null) {
        $this->layout = 'ajax';
        $this->set('companyId', $companyId);
        $this->set('category', $category);
        $this->set('locationId', $locationId);
        $this->set('orderDate', $orderDate);
        $this->set('branchId', $branchId);
    }

    function applyToInvoice($cmId, $cmBalance, $date) {
        $this->layout = 'ajax';
        $this->loadModel('CreditMemoWithSale');
        $this->loadModel('GeneralLedger');
        $this->loadModel('GeneralLedgerDetail');
        $user = $this->getCurrentUser();
        $creditMemo['CreditMemo']['id'] = $cmId;
        $credit_memo = $this->CreditMemo->read(null, $cmId);
        $total_amount_invoice = 0;
        if ($credit_memo['CreditMemo']['status'] > 0) {
            $r = 0;
            $restCode = array();
            $dateNow  = date("Y-m-d H:i:s");
            if (!empty($_POST['sales_order'])) {
                for ($i = 0; $i < sizeOf($_POST['sales_order']); $i++) {
                    if ($_POST['sales_order'][$i] != "" && $_POST['sales_order'][$i] > 0) {
                        $cmWSale = array();
                        $saleBalance = 0;
                        $queryInvoice = mysql_query("SELECT balance, sys_code, so_code, ar_id FROM sales_orders WHERE id=" . $_POST['sales_order'][$i]);
                        $dataInvoice = mysql_fetch_array($queryInvoice);
                        if ($cmBalance > 0) {
                            $saleBalance = $dataInvoice['balance'] - $_POST['invoice_price'][$i];
                            mysql_query("UPDATE sales_orders SET balance = " . $saleBalance . " WHERE id=" . $_POST['sales_order'][$i]);
                            // Convert to REST
                            $restCode[$r]['balance'] = $saleBalance;
                            $restCode[$r]['dbtodo']  = 'sales_orders';
                            $restCode[$r]['actodo']  = 'ut';
                            $restCode[$r]['con']     = "sys_code = '".$dataInvoice['sys_code']."'";
                            $r++;
                            $this->CreditMemoWithSale->create();
                            $cmWSale['CreditMemoWithSale']['sys_code']       = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                            $cmWSale['CreditMemoWithSale']['credit_memo_id'] = $cmId;
                            $cmWSale['CreditMemoWithSale']['sales_order_id'] = $_POST['sales_order'][$i];
                            $cmWSale['CreditMemoWithSale']['total_price'] = $_POST['invoice_price'][$i];
                            $cmWSale['CreditMemoWithSale']['status'] = 1;
                            $cmWSale['CreditMemoWithSale']['apply_date'] = $date;
                            $cmWSale['CreditMemoWithSale']['created']    = $dateNow;
                            $cmWSale['CreditMemoWithSale']['created_by'] = $user['User']['id'];
                            $this->CreditMemoWithSale->save($cmWSale);
                            $cmWSaleId = $this->CreditMemoWithSale->id;
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($cmWSale['CreditMemoWithSale'], 'credit_memo_with_sales');
                            $restCode[$r]['modified'] = $dateNow;
                            $restCode[$r]['dbtodo']   = 'credit_memo_with_sales';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                            // Save General Ledger Detail
                            $this->GeneralLedger->create();
                            $generalLedger = array();
                            $generalLedger['GeneralLedger']['sys_code']  = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                            $generalLedger['GeneralLedger']['credit_memo_with_sale_id']  = $cmWSaleId;
                            $generalLedger['GeneralLedger']['credit_memo_id']  = $cmId;
                            $generalLedger['GeneralLedger']['sales_order_id']  = NULL;
                            $generalLedger['GeneralLedger']['date']       = $date;
                            $generalLedger['GeneralLedger']['reference']  = $credit_memo['CreditMemo']['cm_code'];
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
                                $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id']  = $credit_memo['CreditMemo']['ar_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['company_id']        = $credit_memo['CreditMemo']['company_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['branch_id']         = $credit_memo['CreditMemo']['branch_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['type']   = 'Apply Invoice';
                                $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $_POST['invoice_price'][$i];
                                $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                                $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Apply CM # '.$credit_memo['CreditMemo']['cm_code'].' and INV # ' . $dataInvoice['so_code'];
                                $generalLedgerDetail['GeneralLedgerDetail']['customer_id'] = $credit_memo['CreditMemo']['customer_id'];
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
                            $generalLedger['GeneralLedger']['sales_order_id']  = $_POST['sales_order'][$i];
                            $generalLedger['GeneralLedger']['credit_memo_id']  = NULL;
                            $generalLedger['GeneralLedger']['reference']  = $dataInvoice['so_code'];
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
                                $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id']  = $dataInvoice['ar_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['company_id']        = $credit_memo['CreditMemo']['company_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['branch_id']         = $credit_memo['CreditMemo']['branch_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['type']   = 'Apply Sales Return';
                                $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                                $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $_POST['invoice_price'][$i];
                                $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Apply CM # '.$credit_memo['CreditMemo']['cm_code'].' and INV # ' . $dataInvoice['so_code'];
                                $generalLedgerDetail['GeneralLedgerDetail']['customer_id'] = $credit_memo['CreditMemo']['customer_id'];
                                $this->GeneralLedgerDetail->save($generalLedgerDetail);
                                // Convert to REST
                                $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                                $restCode[$r]['dbtodo']   = 'general_ledger_details';
                                $restCode[$r]['actodo']   = 'is';
                                $r++;
                            }
                            $this->Helper->saveUserActivity($user['User']['id'], 'Sales Return Receipt', 'Apply to Invoice', $this->CreditMemoWithSale->id);
                            $total_amount_invoice += $_POST['invoice_price'][$i];
                        }
                    }
                }
            }
            $creditMemo['CreditMemo']['balance'] = $cmBalance - $total_amount_invoice;
            $creditMemo['CreditMemo']['total_amount_invoice'] = ($credit_memo['CreditMemo']['total_amount_invoice'] < 0 ? 0 : $credit_memo['CreditMemo']['total_amount_invoice'] + $total_amount_invoice);
            $creditMemo['CreditMemo']['modified_by'] = $user['User']['id'];
            $this->CreditMemo->save($creditMemo);
            // Convert to REST
            $restCode[$r]['balance']  = $cmBalance - $total_amount_invoice;
            $restCode[$r]['total_amount_invoice'] = $creditMemo['CreditMemo']['total_amount_invoice'];
            $restCode[$r]['modified']    = $dateNow;
            $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
            $restCode[$r]['dbtodo'] = 'credit_memos';
            $restCode[$r]['actodo'] = 'ut';
            $restCode[$r]['con']    = "sys_code = '".$credit_memo['CreditMemo']['sys_code']."'";
            // Save File Send
            $this->Helper->sendFileToSync($restCode, 0, 0);
        }
        exit();
    }

    function aging($id = null) {
        $this->layout = 'ajax';
        $user         = $this->getCurrentUser();
        if (!empty($this->data)) {
            $this->loadModel('CreditMemoReceipt');
            // Find Chart Account Cash Default
            $cashBankAccount = ClassRegistry::init('AccountType')->findById(6);
            $cashBankAccountId = $cashBankAccount['AccountType']['chart_account_id'];
            $r = 0;
            $restCode = array();
            $dateNow  = date("Y-m-d H:i:s");
            $result      = array();
            $credit_memo = array();
            $credit_memo['CreditMemo']['id']          = $this->data['CreditMemo']['id'];
            $credit_memo['CreditMemo']['modified_by'] = $user['User']['id'];
            $credit_memo['CreditMemo']['balance']     = $this->data['CreditMemo']['balance_us'];
            if ($this->CreditMemo->save($credit_memo)) {
                $creditMemo = $this->CreditMemo->findById($this->data['CreditMemo']['id']);
                // Convert to REST
                $restCode[$r]['balance']     = $this->data['CreditMemo']['balance_us'];
                $restCode[$r]['modified']    = $dateNow;
                $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
                $restCode[$r]['dbtodo'] = 'credit_memos';
                $restCode[$r]['actodo'] = 'ut';
                $restCode[$r]['con']    = "sys_code = '".$creditMemo['CreditMemo']['sys_code']."'";
                $r++;
                $lastExchangeRate = ClassRegistry::init('ExchangeRate')->find("first", array("conditions" => array(
                                "ExchangeRate.branch_id" => $creditMemo['CreditMemo']['branch_id'],
                                "ExchangeRate.currency_center_id" => $this->data['CreditMemo']['currency_center_id']), "order" => array("ExchangeRate.created desc")));
                // Get Total Paid
                if(!empty($lastExchangeRate) && $lastExchangeRate['ExchangeRate']['rate_to_sell'] > 0){
                    $exchangeRateId = $lastExchangeRate['ExchangeRate']['id'];
                    $totalPaidOther = ($this->data['CreditMemo']['amount_other'] / $lastExchangeRate['ExchangeRate']['rate_to_sell']);
                } else {
                    $exchangeRateId = 0;
                    $totalPaidOther = 0;
                }
                $totalPaid = $this->data['CreditMemo']['amount_us'] + $totalPaidOther;
                // Load Model
                $this->loadModel('GeneralLedger');
                $this->loadModel('GeneralLedgerDetail');
                $this->loadModel('Company');
                // Sales Order Receipt
                $this->CreditMemoReceipt->create();
                $creditMemoReceipt = array();
                $creditMemoReceipt['CreditMemoReceipt']['sys_code']           = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $creditMemoReceipt['CreditMemoReceipt']['credit_memo_id']     = $this->data['CreditMemo']['id'];
                $creditMemoReceipt['CreditMemoReceipt']['branch_id']          = $creditMemo['CreditMemo']['branch_id'];
                $creditMemoReceipt['CreditMemoReceipt']['exchange_rate_id']   = $exchangeRateId;
                $creditMemoReceipt['CreditMemoReceipt']['currency_center_id'] = $this->data['CreditMemo']['currency_center_id'];
                $creditMemoReceipt['CreditMemoReceipt']['chart_account_id'] = $cashBankAccountId;
                $creditMemoReceipt['CreditMemoReceipt']['receipt_code']     = '';
                $creditMemoReceipt['CreditMemoReceipt']['amount_us']        = $this->data['CreditMemo']['amount_us'];
                $creditMemoReceipt['CreditMemoReceipt']['amount_other']     = $this->data['CreditMemo']['amount_other'];
                $creditMemoReceipt['CreditMemoReceipt']['total_amount']     = $this->data['CreditMemo']['total_amount'];
                $creditMemoReceipt['CreditMemoReceipt']['balance']          = $this->data['CreditMemo']['balance_us'];
                $creditMemoReceipt['CreditMemoReceipt']['balance_other']    = $this->data['CreditMemo']['balance_other'];
                $creditMemoReceipt['CreditMemoReceipt']['created_by']       = $user['User']['id'];
                $creditMemoReceipt['CreditMemoReceipt']['pay_date']         = $this->data['CreditMemo']['pay_date']!=''?$this->data['CreditMemo']['pay_date']:'0000-00-00';
                if ($this->data['CreditMemo']['balance_us'] > 0) {
                    $creditMemoReceipt['CreditMemoReceipt']['due_date'] = $this->data['CreditMemo']['aging']!=''?$this->data['CreditMemo']['aging']:'0000-00-00';
                }
                $this->CreditMemoReceipt->save($creditMemoReceipt);
                $result['sr_id'] = $this->CreditMemoReceipt->id;
                // Update Code & Change SO Generate Code
                $modComCode = ClassRegistry::init('ModuleCodeBranch')->find('first', array('conditions' => array("ModuleCodeBranch.branch_id" => $creditMemo['CreditMemo']['branch_id'])));
                $repCode    = date("y").$modComCode['ModuleCodeBranch']['cm_rep_code'];
                // Get Module Code
                $modCode    = $this->Helper->getModuleCode($repCode, $result['sr_id'], 'receipt_code', 'credit_memo_receipts', 'is_void = 0 AND branch_id = '.$creditMemo['CreditMemo']['branch_id']);
                // Updaet Module Code
                mysql_query("UPDATE credit_memo_receipts SET receipt_code = '".$modCode."' WHERE id = ".$result['sr_id']);
                // Convert to REST
                $restCode[$r] = $this->Helper->convertToDataSync($creditMemoReceipt['CreditMemoReceipt'], 'sales_order_receipts');
                $restCode[$r]['receipt_code'] = $modCode;
                $restCode[$r]['modified'] = $dateNow;
                $restCode[$r]['dbtodo']   = 'credit_memo_receipts';
                $restCode[$r]['actodo']   = 'is';
                $r++;
                $company = $this->Company->read(null, $creditMemo['CreditMemo']['location_group_id']);
                $classId = $this->Helper->getClassId($company['Company']['id'], $company['Company']['classes'], $creditMemo['CreditMemo']['location_group_id']);
                
                if (($this->data['CreditMemo']['total_amount'] - $this->data['CreditMemo']['balance_us']) > 0) {
                    // Save General Ledger Detail
                    $this->GeneralLedger->create();
                    $generalLedger = array();
                    $generalLedger['GeneralLedger']['sys_code']               = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                    $generalLedger['GeneralLedger']['credit_memo_id']         = $this->data['CreditMemo']['id'];
                    $generalLedger['GeneralLedger']['credit_memo_receipt_id'] = $result['sr_id'];
                    $generalLedger['GeneralLedger']['date']       = $this->data['CreditMemo']['pay_date']!=''?$this->data['CreditMemo']['pay_date']:'0000-00-00';
                    $generalLedger['GeneralLedger']['reference']  = $modCode;
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
                        $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id']  = $cashBankAccountId;
                        $generalLedgerDetail['GeneralLedgerDetail']['company_id']  = $creditMemo['CreditMemo']['company_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['branch_id']   = $creditMemo['CreditMemo']['branch_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['location_id'] = $creditMemo['CreditMemo']['location_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['location_group_id'] = $creditMemo['CreditMemo']['location_group_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['type']   = 'Sales Return Payment';
                        $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                        $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $totalPaid;
                        $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Sales Return # ' . $creditMemo['CreditMemo']['cm_code'];
                        $generalLedgerDetail['GeneralLedgerDetail']['customer_id'] = $creditMemo['CreditMemo']['customer_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['class_id']    = $classId;
                        $this->GeneralLedgerDetail->save($generalLedgerDetail);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                        $restCode[$r]['dbtodo']   = 'general_ledger_details';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;

                        $this->GeneralLedgerDetail->create();
                        $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $creditMemo['CreditMemo']['ar_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $totalPaid;
                        $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
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
                $this->Helper->saveUserActivity($user['User']['id'], 'Sales Return Receipt', 'Save Add New', $id, $result['sr_id']);
                echo json_encode($result);
                exit;
            }
        }
        if (!empty($id)) {
            $this->Helper->saveUserActivity($user['User']['id'], 'Sales Return Receipt', 'Add New', $id);
            $this->data = $this->CreditMemo->read(null, $id);
            $creditMemo = ClassRegistry::init('CreditMemo')->find("first", array('conditions' => array('CreditMemo.id' => $id)));
            if (!empty($creditMemo)) {
                $cmWsales = ClassRegistry::init('CreditMemoWithSale')->find("all", array('conditions' => array('CreditMemoWithSale.credit_memo_id' => $id, 'CreditMemoWithSale.status>0')));
                $creditMemoDetails = ClassRegistry::init('CreditMemoDetail')->find("all", array('conditions' => array('CreditMemoDetail.credit_memo_id' => $id)));
                $creditMemoServices = ClassRegistry::init('CreditMemoService')->find("all", array('conditions' => array('CreditMemoService.credit_memo_id' => $id)));
                $creditMemoMiscs = ClassRegistry::init('CreditMemoMisc')->find("all", array('conditions' => array('CreditMemoMisc.credit_memo_id' => $id)));
                $creditMemoReceipts = ClassRegistry::init('CreditMemoReceipt')->find("all", array('conditions' => array('CreditMemoReceipt.credit_memo_id' => $id, 'CreditMemoReceipt.is_void' => 0)));
                $this->set(compact('creditMemo', 'creditMemoDetails', 'creditMemoServices', 'creditMemoMiscs', 'creditMemoReceipts', 'cmWsales'));
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
            $creditMemo = ClassRegistry::init('CreditMemo')->find("first", array('conditions' => array('CreditMemo.id' => $id)));
            if (!empty($creditMemo)) {
                $cmWsales = ClassRegistry::init('CreditMemoWithSale')->find("all", array('conditions' => array('CreditMemoWithSale.credit_memo_id' => $id, 'CreditMemoWithSale.status>0'),
                    'group' => 'CreditMemoWithSale.sales_order_id',
                    'fields' => array('sum(CreditMemoWithSale.total_price) as total_price', 'CreditMemoWithSale.*', 'SalesOrder.*')
                        ));
                $creditMemoDetails = ClassRegistry::init('CreditMemoDetail')->find("all", array('conditions' => array('CreditMemoDetail.credit_memo_id' => $id)));
                $creditMemoMiscs = ClassRegistry::init('CreditMemoMisc')->find("all", array('conditions' => array('CreditMemoMisc.credit_memo_id' => $id)));
                $creditMemoServices = ClassRegistry::init('CreditMemoService')->find("all", array('conditions' => array('CreditMemoService.credit_memo_id' => $id)));

                $location = ClassRegistry::init('Location')->find("first", array("conditions" => array("Location.id" => $creditMemo['CreditMemo']['location_id'], "Location.is_active" => "1")));
                $this->set(compact('creditMemo', 'creditMemoDetails', 'creditMemoMiscs', 'creditMemoServices', "location", "cmWsales"));
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
            $sr = ClassRegistry::init('CreditMemoReceipt')->find("first", array('conditions' => array('CreditMemoReceipt.id' => $receiptId, 'CreditMemoReceipt.is_void' => 0)));

            $creditMemo = ClassRegistry::init('CreditMemo')->find("first", array('conditions' => array('CreditMemo.id' => $sr['CreditMemo']['id'])));
            if (!empty($creditMemo)) {
                $location = ClassRegistry::init('Location')->find("first", array("conditions" => array("Location.id" => $creditMemo['CreditMemo']['location_id'], "Location.is_active" => "1")));
                $creditMemoDetails = ClassRegistry::init('CreditMemoDetail')->find("all", array('conditions' => array('CreditMemoDetail.credit_memo_id' => $sr['CreditMemo']['id'])));
                $creditMemoMiscs = ClassRegistry::init('CreditMemoMisc')->find("all", array('conditions' => array('CreditMemoMisc.credit_memo_id' => $sr['CreditMemo']['id'])));
                $creditMemoServices = ClassRegistry::init('CreditMemoService')->find("all", array('conditions' => array('CreditMemoService.credit_memo_id' => $sr['CreditMemo']['id'])));
                $creditMemoReceipts = ClassRegistry::init('CreditMemoReceipt')->find("all", array('conditions' => array('CreditMemoReceipt.credit_memo_id' => $sr['CreditMemo']['id'], 'CreditMemoReceipt.is_void' => 0)));

                $this->set(compact('creditMemo', 'creditMemoDetails', 'creditMemoMiscs', 'creditMemoServices', 'creditMemoReceipts', 'sr', 'lastExchangeRate', 'location'));
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
            $sr = ClassRegistry::init('CreditMemoReceipt')->find("first", array('conditions' => array('CreditMemoReceipt.id' => $receiptId, 'CreditMemoReceipt.is_void' => 0)));

            $creditMemo = ClassRegistry::init('CreditMemo')->find("first", array('conditions' => array('CreditMemo.id' => $sr['CreditMemo']['id'])));
            if (!empty($creditMemo)) {
                $location = ClassRegistry::init('Location')->find("first", array("conditions" => array("Location.id" => $creditMemo['CreditMemo']['location_id'], "Location.is_active" => "1")));
                $creditMemoDetails = ClassRegistry::init('CreditMemoDetail')->find("all", array('conditions' => array('CreditMemoDetail.credit_memo_id' => $sr['CreditMemo']['id'])));
                $creditMemoMiscs = ClassRegistry::init('CreditMemoMisc')->find("all", array('conditions' => array('CreditMemoMisc.credit_memo_id' => $sr['CreditMemo']['id'])));
                $creditMemoServices = ClassRegistry::init('CreditMemoService')->find("all", array('conditions' => array('CreditMemoService.credit_memo_id' => $sr['CreditMemo']['id'])));
                $creditMemoReceipts = ClassRegistry::init('CreditMemoReceipt')->find("all", array('conditions' => array('CreditMemoReceipt.id <= ' . $receiptId, 'CreditMemoReceipt.credit_memo_id' => $sr['CreditMemo']['id'], 'CreditMemoReceipt.is_void' => 0)));
                $this->set(compact('creditMemo', 'creditMemoDetails', 'creditMemoMiscs', 'creditMemoServices', 'creditMemoReceipts', 'sr', 'lastExchangeRate', 'location'));
            } else {
                exit;
            }
        } else {
            exit;
        }
    }

    function customer($companyId) {
        $this->layout = 'ajax';
        if(!empty($companyId)){
            $this->set(compact('companyId'));
        }else{
            exit;
        }
    }

    function customer_ajax($companyId, $group = null) {
        $this->layout = 'ajax';
        if(!empty($companyId)){
            $this->set(compact('companyId', 'group'));
        }else{
            exit;
        }
    }
    
    function salesOrder($companyId = null, $branchId = null, $customerId = '') {
        $this->layout = 'ajax';
        $this->set(compact('companyId', 'customerId', 'branchId'));
    }

    function salesOrderAjax($companyId = null, $branchId = null, $customerId = '') {
        $this->layout = 'ajax';
        $this->set(compact('companyId', 'customerId', 'branchId'));
    }

    function invoice($chartAccountId, $companyId, $branchId, $customerId, $balance, $cmId) {
        $this->layout = 'ajax';
        $this->set('chartAccountId', $chartAccountId);
        $this->set('companyId', $companyId);
        $this->set('branchId', $branchId);
        $this->set('customerId', $customerId);
        $this->set('balance', $this->Helper->replaceThousand($balance));
        $this->data = $this->CreditMemo->read(null, $cmId);
    }

    function invoiceAjax($chartAccountId, $companyId, $branchId, $customerId, $cg = null) {
        $this->layout = 'ajax';
        $this->set('chartAccountId', $chartAccountId);
        $this->set('companyId', $companyId);
        $this->set('branchId', $branchId);
        $this->set('customerId', $customerId);
        $this->set('cg', $cg);
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
                             ));
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

    function searchProductByCode($company_id, $branchId, $customerId) {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $product_code = !empty($this->data['code']) ? $this->data['code'] : "";
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
                'Product.small_val_uom',
                'Product.price_uom_id',
                'Product.is_packet',
                'Product.is_expired_date',
                'Product.is_lots'
            ),
            'conditions' => array(
                array(
                    "OR" => array(
                        'trim(Product.code)' => trim($product_code),
                        'trim(Product.barcode)' => trim($product_code),
                        'trim(Product.chemical)' => trim($product_code)
                    )
                ),
                'Product.company_id' => $company_id,
                'Product.is_active' => 1,
                '((Product.price_uom_id IS NOT NULL AND Product.is_packet = 0) OR (Product.price_uom_id IS NULL AND Product.is_packet = 1))'
            ),
            'joins' => $joins,
            'group' => array(
                'Product.id',
                'Product.name',
                'Product.code',
                'Product.barcode',
                'Product.price_uom_id',
            )
        ));
        $this->set(compact('product', 'customerId'));
        $db = ConnectionManager::getDataSource('default');
        mysql_select_db($db->config['database']);
    }

    function edit($id = null) {
        $this->layout = 'ajax';
        if (!$id && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }

        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $this->loadModel('CreditMemoDetail');
            $this->loadModel('CreditMemoService');
            $this->loadModel('CreditMemoMiscs');
            $this->loadModel('GeneralLedger');
            $this->loadModel('GeneralLedgerDetail');
            $this->loadModel('InventoryValuation');
            $this->loadModel('AccountType');
            $this->loadModel('Company');
            $credit_memo = $this->CreditMemo->read(null, $this->data['id']);
            $queryHasReceipt = mysql_query("SELECT id FROM credit_memo_receipts WHERE credit_memo_id=" . $this->data['id'] . " AND is_void = 0");
            $queryHasApplyInv = mysql_query("SELECT id FROM credit_memo_with_sales WHERE credit_memo_id=" . $this->data['id'] . " AND status > 0");
            if ($credit_memo['CreditMemo']['status'] == 2 && !mysql_num_rows($queryHasReceipt) && !mysql_num_rows($queryHasApplyInv)) {
                $r  = 0;
                $rb = 0;
                $restCode = array();
                $restBackCode  = array();
                $dateNow  = date("Y-m-d H:i:s");
                $result = array();
                $statuEdit = "-1";
                if($this->data['CreditMemo']['company_id'] != $credit_memo['CreditMemo']['company_id']){
                    $statuEdit = 0;
                }
                // Update Status As Edit
                $this->CreditMemo->updateAll(
                        array('CreditMemo.status' => $statuEdit, 'CreditMemo.modified_by' => $user['User']['id']), array('CreditMemo.id' => $this->data['id'])
                );
                // Convert to REST
                $restBackCode[$rb]['status']   = $statuEdit;
                $restBackCode[$rb]['modified'] = $dateNow;
                $restBackCode[$rb]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
                $restBackCode[$rb]['dbtodo'] = 'credit_memos';
                $restBackCode[$rb]['actodo'] = 'ut';
                $restBackCode[$rb]['con']    = "sys_code = '".$credit_memo['CreditMemo']['sys_code']."'";
                $rb++;
                $this->GeneralLedger->updateAll(
                        array('GeneralLedger.is_active' => 2, 'GeneralLedger.modified_by' => $user['User']['id']), array('GeneralLedger.credit_memo_id' => $this->data['id'])
                );
                // Convert to REST
                $restBackCode[$rb]['is_active'] = 2;
                $restBackCode[$rb]['modified']  = $dateNow;
                $restBackCode[$rb]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
                $restBackCode[$rb]['dbtodo'] = 'general_ledgers';
                $restBackCode[$rb]['actodo'] = 'ut';
                $restBackCode[$rb]['con']    = "credit_memo_id = (SELECT id FROM credit_memos WHERE sys_code = '".$credit_memo['CreditMemo']['sys_code']."' LIMIT 1)";
                $rb++;
                $this->InventoryValuation->updateAll(
                        array('InventoryValuation.is_active' => 2), array('InventoryValuation.credit_memo_id' => $this->data['id'])
                );
                // Convert to REST
                $restBackCode[$rb]['is_active'] = 2;
                $restBackCode[$rb]['dbtodo'] = 'inventory_valuations';
                $restBackCode[$rb]['actodo'] = 'ut';
                $restBackCode[$rb]['con']    = "credit_memo_id = (SELECT id FROM credit_memos WHERE sys_code = '".$credit_memo['CreditMemo']['sys_code']."' LIMIT 1)";
                $rb++;
                $creditMemoDetails = ClassRegistry::init('CreditMemoDetail')->find("all", array('conditions' => array('CreditMemoDetail.credit_memo_id' => $this->data['id'])));
                foreach($creditMemoDetails AS $creditMemoDetail){
                    $totalQtyOrder = (($creditMemoDetail['CreditMemoDetail']['qty'] + $creditMemoDetail['CreditMemoDetail']['qty_free']) * $creditMemoDetail['CreditMemoDetail']['conversion']);
                    $qtyOrder      = ($creditMemoDetail['CreditMemoDetail']['qty'] * $creditMemoDetail['CreditMemoDetail']['conversion']);
                    $qtyFree       = ($creditMemoDetail['CreditMemoDetail']['qty_free'] * $creditMemoDetail['CreditMemoDetail']['conversion']);
                    // Update Inventory (Sales Return)
                    $data = array();
                    $data['module_type']        = 19;
                    $data['credit_memo_id']     = $credit_memo['CreditMemo']['id'];
                    $data['product_id']         = $creditMemoDetail['CreditMemoDetail']['product_id'];
                    $data['location_id']        = $credit_memo['CreditMemo']['location_id'];
                    $data['location_group_id']  = $credit_memo['CreditMemo']['location_group_id'];
                    $data['lots_number']  = $creditMemoDetail['CreditMemoDetail']['lots_number'];
                    $data['expired_date'] = $creditMemoDetail['CreditMemoDetail']['expired_date'];
                    $data['date']         = $credit_memo['CreditMemo']['order_date'];
                    $data['total_qty']    = $totalQtyOrder;
                    $data['total_order']  = $qtyOrder;
                    $data['total_free']   = $qtyFree;
                    $data['user_id']      = $user['User']['id'];
                    $data['customer_id']  = $credit_memo['CreditMemo']['customer_id'];
                    $data['vendor_id']    = "";
                    $data['unit_cost']    = 0;
                    $data['unit_price']   = $creditMemoDetail['CreditMemoDetail']['total_price'] - $creditMemoDetail['CreditMemoDetail']['discount_amount'];
                    // Update Invetory Location
                    $this->Inventory->saveInventory($data);
                    // Update Inventory Group
                    $this->Inventory->saveGroupTotalDetail($data);
                    // Convert to REST
                    $restBackCode[$rb] = $this->Helper->convertToDataSync($data, 'inventories');
                    $restBackCode[$rb]['module_type']  = 19;
                    $restBackCode[$rb]['total_qty']    = $totalQtyOrder;
                    $restBackCode[$rb]['total_order']  = $qtyOrder;
                    $restBackCode[$rb]['total_free']   = $qtyFree;
                    $restBackCode[$rb]['expired_date'] = $data['expired_date'];
                    $restBackCode[$rb]['vendor_id']    = "";
                    $restBackCode[$rb]['unit_cost']    = 0;
                    $restBackCode[$rb]['customer_id']       = $this->Helper->getSQLSyncCode("customers", $credit_memo['CreditMemo']['customer_id']);
                    $restBackCode[$rb]['credit_memo_id']    = $this->Helper->getSQLSyncCode("credit_memos", $credit_memo['CreditMemo']['id']);
                    $restBackCode[$rb]['product_id']        = $this->Helper->getSQLSyncCode("products", $creditMemoDetail['CreditMemoDetail']['product_id']);
                    $restBackCode[$rb]['location_id']       = $this->Helper->getSQLSyncCode("locations", $credit_memo['CreditMemo']['location_id']);
                    $restBackCode[$rb]['location_group_id'] = $this->Helper->getSQLSyncCode("location_groups", $credit_memo['CreditMemo']['location_group_id']);
                    $restBackCode[$rb]['user_id']           = $this->Helper->getSQLSyncCode("users", $user['User']['id']);
                    $restBackCode[$rb]['dbtype']  = 'saveInv,GroupDetail';
                    $restBackCode[$rb]['actodo']  = 'inv';
                    $rb++;
                }
                // Save File Send Delete
                $this->Helper->sendFileToSync($restBackCode, 0, 0);
                
                //  Find Chart Account
                $arAccount = $this->AccountType->findById(7);
                $salesMiscAccount = $this->AccountType->findById(10);
                $salesDiscAccount = $this->AccountType->findById(11);
                $salesMarkUpAccount = $this->AccountType->findById(17);
                $total_balance = ($this->data['CreditMemo']['total_amount'] + $this->data['CreditMemo']['mark_up'] + $this->data['CreditMemo']['total_vat']) - $this->data['CreditMemo']['discount'];
                // Sales Return
                $this->CreditMemo->create();
                $creditMemo = array();
                $creditMemo['CreditMemo']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $creditMemo['CreditMemo']['created']    = $dateNow;
                $creditMemo['CreditMemo']['created_by'] = $user['User']['id'];
                $creditMemo['CreditMemo']['company_id'] = $this->data['CreditMemo']['company_id'];
                $creditMemo['CreditMemo']['branch_id']  = $this->data['CreditMemo']['branch_id'];
                $creditMemo['CreditMemo']['location_group_id'] = $this->data['CreditMemo']['location_group_id'];
                $creditMemo['CreditMemo']['location_id'] = $this->data['CreditMemo']['location_id'];
                $creditMemo['CreditMemo']['customer_id'] = $this->data['CreditMemo']['customer_id'];
                $creditMemo['CreditMemo']['patient_id'] = $this->data['CreditMemo']['customer_id'];
                $creditMemo['CreditMemo']['currency_center_id'] = $this->data['CreditMemo']['currency_center_id'];
                $creditMemo['CreditMemo']['reason_id']      = $this->data['CreditMemo']['reason_id'];
                $creditMemo['CreditMemo']['sales_order_id'] = $this->data['CreditMemo']['sales_order_id'];
                $creditMemo['CreditMemo']['invoice_code']   = $this->data['CreditMemo']['invoice_code'];
                $creditMemo['CreditMemo']['invoice_date']   = ((empty($this->data['CreditMemo']['invoice_date']))?'0000-00-00':$this->data['CreditMemo']['invoice_date']);
                $creditMemo['CreditMemo']['note']    = $this->data['CreditMemo']['note'];
                $creditMemo['CreditMemo']['ar_id']   = $arAccount['AccountType']['chart_account_id'];
                $creditMemo['CreditMemo']['cm_code'] = $credit_memo['CreditMemo']['cm_code'];
                $creditMemo['CreditMemo']['balance'] = $total_balance;
                $creditMemo['CreditMemo']['total_amount'] = $this->data['CreditMemo']['total_amount'];
                $creditMemo['CreditMemo']['mark_up']      = $this->data['CreditMemo']['mark_up'];
                $creditMemo['CreditMemo']['discount']     = $this->data['CreditMemo']['discount'];
                $creditMemo['CreditMemo']['discount_percent'] = $this->data['CreditMemo']['discount_percent'];
                $creditMemo['CreditMemo']['order_date']  = $this->data['CreditMemo']['order_date'];
                $creditMemo['CreditMemo']['due_date']    = ((empty($this->data['CreditMemo']['due_date']))?'0000-00-00':$this->data['CreditMemo']['due_date']);
                $creditMemo['CreditMemo']['status']      = 2;
                $creditMemo['CreditMemo']['total_vat']   = $this->data['CreditMemo']['total_vat'];
                $creditMemo['CreditMemo']['vat_percent'] = $this->data['CreditMemo']['vat_percent'];
                $creditMemo['CreditMemo']['vat_setting_id'] = $this->data['CreditMemo']['vat_setting_id'];
                $creditMemo['CreditMemo']['vat_calculate']  = $this->data['CreditMemo']['vat_calculate'];
                $creditMemo['CreditMemo']['vat_chart_account_id'] = $this->data['CreditMemo']['vat_chart_account_id'];
                $creditMemo['CreditMemo']['price_type_id'] = $this->data['CreditMemo']['price_type_id'];
                if ($this->CreditMemo->save($creditMemo)) {
                    $result['so_id'] = $creditMemoId = $this->CreditMemo->id;
                    $company         = $this->Company->read(null, $this->data['CreditMemo']['company_id']);
                    $classId         = $this->Helper->getClassId($company['Company']['id'], $company['Company']['classes'], $this->data['CreditMemo']['location_group_id']);
                    $glReference     = $credit_memo['CreditMemo']['cm_code'];
                    if($this->data['CreditMemo']['company_id'] != $credit_memo['CreditMemo']['company_id']){
                        // Get Module Code
                        $modCode = $this->Helper->getModuleCode($this->data['CreditMemo']['cm_code'], $creditMemoId, 'cm_code', 'credit_memos', 'status != -1 AND branch_id = '.$this->data['CreditMemo']['branch_id']);
                        // Updaet Module Code
                        mysql_query("UPDATE credit_memos SET cm_code = '".$modCode."' WHERE id = ".$creditMemoId);
                        $glReference = $modCode;
                    }
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($creditMemo['CreditMemo'], 'credit_memos');
                    $restCode[$r]['cm_code']  = $glReference;
                    $restCode[$r]['modified'] = $dateNow;
                    $restCode[$r]['dbtodo']   = 'credit_memos';
                    $restCode[$r]['actodo']   = 'is';
                    $r++;
                    // Create General Ledger
                    $this->GeneralLedger->create();
                    $generalLedger = array();
                    $generalLedger['GeneralLedger']['sys_code']       = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                    $generalLedger['GeneralLedger']['credit_memo_id'] = $creditMemoId;
                    $generalLedger['GeneralLedger']['date']       = $this->data['CreditMemo']['order_date'];
                    $generalLedger['GeneralLedger']['reference']  = $glReference;
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

                    // General Ledger Detail (A/R)
                    $generalLedgerDetail = array();
                    $this->GeneralLedgerDetail->create();
                    $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $generalLedgerId;
                    $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id']  = $arAccount['AccountType']['chart_account_id'];
                    $generalLedgerDetail['GeneralLedgerDetail']['company_id']  = $creditMemo['CreditMemo']['company_id'];
                    $generalLedgerDetail['GeneralLedgerDetail']['branch_id']   = $creditMemo['CreditMemo']['branch_id'];
                    $generalLedgerDetail['GeneralLedgerDetail']['location_group_id'] = $creditMemo['CreditMemo']['location_group_id'];
                    $generalLedgerDetail['GeneralLedgerDetail']['location_id']       = $creditMemo['CreditMemo']['location_id'];
                    $generalLedgerDetail['GeneralLedgerDetail']['type']   = 'Sales Return';
                    $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                    $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $total_balance;
                    $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Sales Return # ' . $glReference;
                    $generalLedgerDetail['GeneralLedgerDetail']['customer_id'] = $creditMemo['CreditMemo']['customer_id'];
                    $generalLedgerDetail['GeneralLedgerDetail']['class_id']    = $classId;
                    $this->GeneralLedgerDetail->save($generalLedgerDetail);
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                    $restCode[$r]['dbtodo']   = 'general_ledger_details';
                    $restCode[$r]['actodo']   = 'is';
                    $r++;

                    // General Ledger Detail (Total Discount)
                    if ($this->data['CreditMemo']['discount'] > 0) {
                        $this->GeneralLedgerDetail->create();
                        $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $salesDiscAccount['AccountType']['chart_account_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                        $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $this->data['CreditMemo']['discount'];
                        $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Sales Return # ' . $glReference . ' Total Discount';
                        $this->GeneralLedgerDetail->save($generalLedgerDetail);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                        $restCode[$r]['dbtodo']   = 'general_ledger_details';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                    }

                    // General Ledger Detail (Total Mark Up)
                    if ($this->data['CreditMemo']['mark_up'] > 0) {
                        $this->GeneralLedgerDetail->create();
                        $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $salesMarkUpAccount['AccountType']['chart_account_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $this->data['CreditMemo']['mark_up'];
                        $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                        $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Sales Return # ' . $glReference . ' Markup';
                        $this->GeneralLedgerDetail->save($generalLedgerDetail);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                        $restCode[$r]['dbtodo']   = 'general_ledger_details';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                    }

                    // General Ledger Detail (Total VAT)
                    if ($creditMemo['CreditMemo']['total_vat'] > 0) {
                        $this->GeneralLedgerDetail->create();
                        $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $this->data['CreditMemo']['vat_chart_account_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $creditMemo['CreditMemo']['total_vat'];
                        $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                        $generalLedgerDetail['GeneralLedgerDetail']['memo']   = "ICS: Sales Return # " . $glReference . ' Total VAT';
                        $this->GeneralLedgerDetail->save($generalLedgerDetail);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                        $restCode[$r]['dbtodo']   = 'general_ledger_details';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                    }

                    for ($i = 0; $i < sizeof($_POST['product']); $i++) {
                        if (!empty($_POST['product_id'][$i])) {
                            $creditMemoDetail = array();
                            // Sales Order Detail
                            $this->CreditMemoDetail->create();
                            $creditMemoDetail['CreditMemoDetail']['credit_memo_id'] = $creditMemoId;
                            $creditMemoDetail['CreditMemoDetail']['discount_id'] = $_POST['discount_id'][$i];
                            $creditMemoDetail['CreditMemoDetail']['discount_amount']  = $_POST['discount'][$i];
                            $creditMemoDetail['CreditMemoDetail']['discount_percent'] = $_POST['discount_percent'][$i];
                            $creditMemoDetail['CreditMemoDetail']['product_id'] = $_POST['product_id'][$i];
                            $creditMemoDetail['CreditMemoDetail']['qty'] = $_POST['qty'][$i];
                            $creditMemoDetail['CreditMemoDetail']['qty_free'] = $_POST['qty_free'][$i];
                            $creditMemoDetail['CreditMemoDetail']['qty_uom_id'] = $_POST['qty_uom_id'][$i];
                            $creditMemoDetail['CreditMemoDetail']['unit_price'] = $_POST['unit_price'][$i];
                            $creditMemoDetail['CreditMemoDetail']['total_price'] = $_POST['h_total_price'][$i];
                            $creditMemoDetail['CreditMemoDetail']['lots_number'] = ($_POST['lots_number'][$i]!=""?$_POST['lots_number'][$i]:"0");
                            $creditMemoDetail['CreditMemoDetail']['expired_date'] = ($_POST['expired_date'][$i]!=""?$_POST['expired_date'][$i]:"0000-00-00");
                            $creditMemoDetail['CreditMemoDetail']['conversion'] = ($_POST['cm_conversion'][$i]);
                            $creditMemoDetail['CreditMemoDetail']['note'] = $_POST['note'][$i];
                            $this->CreditMemoDetail->save($creditMemoDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($creditMemoDetail['CreditMemoDetail'], 'credit_memo_details');
                            $restCode[$r]['dbtodo']   = 'credit_memo_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;

                            $qtyOrder      = (($_POST['qty'][$i] + $_POST['qty_free'][$i]) / ($_POST['small_val_uom'][$i] / $_POST['cm_conversion'][$i]));
                            $qtyOrderSmall = ($_POST['qty'][$i] + $_POST['qty_free'][$i]) * $_POST['cm_conversion'][$i];
                            $priceSales    = $_POST['h_total_price'][$i] - $_POST['discount'][$i];
                            $queryProductCodeName = mysql_query("SELECT CONCAT(code,' - ',name) AS name, unit_cost AS unit_cost FROM products WHERE id=" . $_POST['product_id'][$i]);
                            $dataProductCodeName = mysql_fetch_array($queryProductCodeName);
                            
                            // Update Inventory (Sales Return)
                            $data = array();
                            $data['module_type']        = 11;
                            $data['credit_memo_id']     = $creditMemoId;
                            $data['product_id']         = $creditMemoDetail['CreditMemoDetail']['product_id'];
                            $data['location_id']        = $creditMemo['CreditMemo']['location_id'];
                            $data['location_group_id']  = $creditMemo['CreditMemo']['location_group_id'];
                            $data['lots_number']  = $creditMemoDetail['CreditMemoDetail']['lots_number'];
                            $data['expired_date'] = $creditMemoDetail['CreditMemoDetail']['expired_date'];
                            $data['date']         = $creditMemo['CreditMemo']['order_date'];
                            $data['total_qty']    = $qtyOrderSmall;
                            $data['total_order']  = $_POST['qty'][$i] * $_POST['cm_conversion'][$i];
                            $data['total_free']   = $_POST['qty_free'][$i] * $_POST['cm_conversion'][$i];
                            $data['user_id']      = $user['User']['id'];
                            $data['customer_id']  = $creditMemo['CreditMemo']['customer_id'];
                            $data['vendor_id']    = "";
                            $data['unit_cost']    = 0;
                            $data['unit_price']   = $priceSales;
                            // Update Invetory Location
                            $this->Inventory->saveInventory($data);
                            // Update Inventory Group
                            $this->Inventory->saveGroupTotalDetail($data);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($data, 'inventories');
                            $restCode[$r]['module_type']  = 11;
                            $restCode[$r]['total_qty']    = $qtyOrderSmall;
                            $restCode[$r]['total_order']  = $data['total_order'];
                            $restCode[$r]['total_free']   = $data['total_free'];
                            $restCode[$r]['expired_date'] = $data['expired_date'];
                            $restCode[$r]['vendor_id']    = "";
                            $restCode[$r]['unit_cost']    = 0;
                            $restCode[$r]['customer_id']       = $this->Helper->getSQLSyncCode("customers", $creditMemo['CreditMemo']['customer_id']);
                            $restCode[$r]['credit_memo_id']    = $this->Helper->getSQLSyncCode("credit_memos", $creditMemoId);
                            $restCode[$r]['product_id']        = $this->Helper->getSQLSyncCode("products", $creditMemoDetail['CreditMemoDetail']['product_id']);
                            $restCode[$r]['location_id']       = $this->Helper->getSQLSyncCode("locations", $creditMemo['CreditMemo']['location_id']);
                            $restCode[$r]['location_group_id'] = $this->Helper->getSQLSyncCode("location_groups", $creditMemo['CreditMemo']['location_group_id']);
                            $restCode[$r]['user_id']           = $this->Helper->getSQLSyncCode("users", $user['User']['id']);
                            $restCode[$r]['dbtype']  = 'saveInv,GroupDetail';
                            $restCode[$r]['actodo']  = 'inv';
                            $r++;
                            
                            // Inventory Valuation
                            $invValOld = $this->InventoryValuation->find('first', array('conditions' => array('InventoryValuation.credit_memo_id' => $this->data['id'], 'InventoryValuation.pid' => $_POST['product_id'][$i], 'InventoryValuation.is_active = 2')));
                            $this->InventoryValuation->create();
                            $inv_valutaion = array();
                            $inv_valutaion['InventoryValuation']['sys_code']       = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                            $inv_valutaion['InventoryValuation']['credit_memo_id'] = $creditMemoId;
                            $inv_valutaion['InventoryValuation']['company_id'] = $this->data['CreditMemo']['company_id'];
                            $inv_valutaion['InventoryValuation']['branch_id']  = $this->data['CreditMemo']['branch_id'];
                            $inv_valutaion['InventoryValuation']['type'] = "Sales Return";
                            $inv_valutaion['InventoryValuation']['reference']   = $glReference;
                            $inv_valutaion['InventoryValuation']['customer_id'] = $creditMemo['CreditMemo']['customer_id'];
                            $inv_valutaion['InventoryValuation']['date'] = $this->data['CreditMemo']['order_date'];
                            $inv_valutaion['InventoryValuation']['pid'] = $_POST['product_id'][$i];
                            $inv_valutaion['InventoryValuation']['small_qty'] = $qtyOrderSmall;
                            $inv_valutaion['InventoryValuation']['qty'] = $this->Helper->replaceThousand(number_format($qtyOrder, 6));
                            $inv_valutaion['InventoryValuation']['cost'] = null;
                            $inv_valutaion['InventoryValuation']['created'] = $invValOld['InventoryValuation']['created'];
                            $inv_valutaion['InventoryValuation']['date_edited'] = date('Y-m-d H:i:s');
                            $inv_valutaion['InventoryValuation']['is_var_cost'] = 1;
                            $inv_valutaion['InventoryValuation']['created']     = $dateNow;
                            $this->InventoryValuation->save($inv_valutaion);
                            $inv_valutation_id = $this->InventoryValuation->getLastInsertId();
                            $inventoryAsset = 0;
                            $cogs = 0;
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($inv_valutaion['InventoryValuation'], 'inventory_valuations');
                            $restCode[$r]['dbtodo']   = 'inventory_valuations';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;

                            // General Ledger Detail (Product Income)
                            $this->GeneralLedgerDetail->create();
                            $queryIncAccount = mysql_query("SELECT IFNULL((IFNULL((SELECT chart_account_id FROM accounts WHERE product_id = " . $_POST['product_id'][$i] . " AND account_type_id=8),(SELECT chart_account_id FROM pgroup_accounts WHERE pgroup_id = (SELECT pgroup_id FROM product_pgroups WHERE product_id = " . $_POST['product_id'][$i] . " ORDER BY id  DESC LIMIT 1) AND account_type_id=8))),(SELECT chart_account_id FROM account_types WHERE id=8))");
                            $dataIncAccount  = mysql_fetch_array($queryIncAccount);
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $dataIncAccount[0];
                            $generalLedgerDetail['GeneralLedgerDetail']['product_id']  = $creditMemoDetail['CreditMemoDetail']['product_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['service_id'] = NULL;
                            $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_id'] = NULL;
                            $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_is_debit'] = NULL;
                            $generalLedgerDetail['GeneralLedgerDetail']['debit']       = $_POST['h_total_price'][$i];
                            $generalLedgerDetail['GeneralLedgerDetail']['credit']      = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['memo']        = 'ICS: Sales Return # ' . $glReference . ' Product # ' . $_POST['product'][$i];
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;

                            // General Ledger Detail (Product Discount)
                            if ($_POST['discount'][$i] > 0) {
                                $this->GeneralLedgerDetail->create();
                                $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $salesDiscAccount['AccountType']['chart_account_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                                $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $_POST['discount'][$i];
                                $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Sales Return # ' . $glReference . ' Product # ' . $_POST['product'][$i] . ' Discount';
                                $this->GeneralLedgerDetail->save($generalLedgerDetail);
                                // Convert to REST
                                $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                                $restCode[$r]['dbtodo']   = 'general_ledger_details';
                                $restCode[$r]['actodo']   = 'is';
                                $r++;
                            }

                            // Update GL for Inventory
                            $this->GeneralLedgerDetail->create();
                            $queryInvAccount = mysql_query("SELECT IFNULL((IFNULL((SELECT chart_account_id FROM accounts WHERE product_id = " . $_POST['product_id'][$i] . " AND account_type_id=1),(SELECT chart_account_id FROM pgroup_accounts WHERE pgroup_id = (SELECT id FROM product_pgroups WHERE product_id = " . $_POST['product_id'][$i] . " ORDER BY id  DESC LIMIT 1) AND account_type_id=1))),(SELECT chart_account_id FROM account_types WHERE id=1))");
                            $dataInvAccount  = mysql_fetch_array($queryInvAccount);
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $dataInvAccount[0];
                            $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_id'] = $inv_valutation_id;
                            $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_is_debit'] = 1;
                            $generalLedgerDetail['GeneralLedgerDetail']['type']   = 'Sales Return';
                            $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $inventoryAsset;
                            $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Inventory for Sales Return # ' . $glReference . ' Product # ' . $dataProductCodeName[0];
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;

                            // Update GL for COGS
                            $this->GeneralLedgerDetail->create();
                            $queryCogsAccount = mysql_query("SELECT IFNULL((IFNULL((SELECT chart_account_id FROM accounts WHERE product_id = " . $_POST['product_id'][$i] . " AND account_type_id=2),(SELECT chart_account_id FROM pgroup_accounts WHERE pgroup_id = (SELECT pgroup_id FROM product_pgroups WHERE product_id = " . $_POST['product_id'][$i] . " ORDER BY id  DESC LIMIT 1) AND account_type_id=2))),(SELECT chart_account_id FROM account_types WHERE id=2))");
                            $dataCogsAccount  = mysql_fetch_array($queryCogsAccount);
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $dataCogsAccount[0];
                            $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_id'] = $inv_valutation_id;
                            $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_is_debit'] = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $cogs;
                            $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: COGS for Sales Return # ' . $glReference . ' Product # ' . $dataProductCodeName[0];
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;

                        } else if (!empty($_POST['service_id'][$i])) {
                            // Sales Return Service
                            $creditMemoService = array();
                            $this->CreditMemoService->create();
                            $creditMemoService['CreditMemoService']['credit_memo_id'] = $creditMemoId;
                            $creditMemoService['CreditMemoService']['discount_id']    = $_POST['discount_id'][$i];
                            $creditMemoService['CreditMemoService']['discount_amount']  = $_POST['discount'][$i];
                            $creditMemoService['CreditMemoService']['discount_percent'] = $_POST['discount_percent'][$i];
                            $creditMemoService['CreditMemoService']['service_id']  = $_POST['service_id'][$i];
                            $creditMemoService['CreditMemoService']['qty']         = $_POST['qty'][$i];
                            $creditMemoService['CreditMemoService']['qty_free']    = $_POST['qty_free'][$i];
                            $creditMemoService['CreditMemoService']['unit_price']  = $_POST['unit_price'][$i];
                            $creditMemoService['CreditMemoService']['total_price'] = $_POST['h_total_price'][$i];
                            $creditMemoService['CreditMemoService']['note']        = $_POST['note'][$i];
                            $this->CreditMemoService->save($creditMemoService);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($creditMemoService['CreditMemoService'], 'credit_memo_services');
                            $restCode[$r]['dbtodo']   = 'credit_memo_services';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;

                            // General Ledger Detail (Service)
                            $this->GeneralLedgerDetail->create();
                            $queryServiceAccount = mysql_query("SELECT IFNULL((SELECT chart_account_id FROM services WHERE id=" . $_POST['service_id'][$i] . "),(SELECT chart_account_id FROM account_types WHERE id=9))");
                            $dataServiceAccount = mysql_fetch_array($queryServiceAccount);
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $dataServiceAccount[0];
                            $generalLedgerDetail['GeneralLedgerDetail']['service_id']  = $_POST['service_id'][$i];
                            $generalLedgerDetail['GeneralLedgerDetail']['product_id']  = NULL;
                            $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_id'] = NULL;
                            $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_is_debit'] = NULL;
                            $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $_POST['h_total_price'][$i];
                            $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Sales Return # ' . $glReference . ' Service # ' . $_POST['service_id'][$i];
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;

                            // General Ledger Detail (Service Discount)
                            if ($_POST['discount'][$i] > 0) {
                                $this->GeneralLedgerDetail->create();
                                $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $salesDiscAccount['AccountType']['chart_account_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                                $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $_POST['discount'][$i];
                                $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Sales Return # ' . $glReference . ' Service # ' . $_POST['service_id'][$i] . ' Discount';
                                $this->GeneralLedgerDetail->save($generalLedgerDetail);
                                // Convert to REST
                                $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                                $restCode[$r]['dbtodo']   = 'general_ledger_details';
                                $restCode[$r]['actodo']   = 'is';
                                $r++;
                            }

                        } else {
                            // Sales Return Miscellaneous
                            $creditMemoMiscs = array();
                            $this->CreditMemoMiscs->create();
                            $creditMemoMiscs['CreditMemoMiscs']['credit_memo_id'] = $creditMemoId;
                            $creditMemoMiscs['CreditMemoMiscs']['discount_id']    = $_POST['discount_id'][$i];
                            $creditMemoMiscs['CreditMemoMiscs']['discount_amount']  = $_POST['discount'][$i];
                            $creditMemoMiscs['CreditMemoMiscs']['discount_percent'] = $_POST['discount_percent'][$i];
                            $creditMemoMiscs['CreditMemoMiscs']['description'] = $_POST['product'][$i];
                            $creditMemoMiscs['CreditMemoMiscs']['qty_uom_id']  = $_POST['qty_uom_id'][$i];
                            $creditMemoMiscs['CreditMemoMiscs']['qty']         = $_POST['qty'][$i];
                            $creditMemoMiscs['CreditMemoMiscs']['qty_free']    = $_POST['qty_free'][$i];
                            $creditMemoMiscs['CreditMemoMiscs']['unit_price']  = $_POST['unit_price'][$i];
                            $creditMemoMiscs['CreditMemoMiscs']['total_price'] = $_POST['h_total_price'][$i];
                            $creditMemoMiscs['CreditMemoMiscs']['note']        = $_POST['note'][$i];
                            $this->CreditMemoMiscs->save($creditMemoMiscs);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($creditMemoMiscs['CreditMemoMiscs'], 'credit_memo_miscs');
                            $restCode[$r]['dbtodo']   = 'credit_memo_miscs';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;

                            // General Ledger Detail (Misc)
                            $this->GeneralLedgerDetail->create();
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $salesMiscAccount['AccountType']['chart_account_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['service_id'] = NULL;
                            $generalLedgerDetail['GeneralLedgerDetail']['product_id'] = NULL;
                            $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_id'] = NULL;
                            $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_is_debit'] = NULL;
                            $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $_POST['h_total_price'][$i];
                            $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Sales Return # ' . $glReference . ' Misc # ' . $_POST['product'][$i];
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;

                            // General Ledger Detail (Misc Discount)
                            if ($_POST['discount'][$i] > 0) {
                                $this->GeneralLedgerDetail->create();
                                $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id']  = $salesDiscAccount['AccountType']['chart_account_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                                $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $_POST['discount'][$i];
                                $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: Sales Return # ' . $glReference . ' Misc # ' . $_POST['product'][$i] . ' Discount';
                                $this->GeneralLedgerDetail->save($generalLedgerDetail);
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
                    mysql_query("UPDATE tracks SET val='".$this->data['CreditMemo']['order_date']."', is_recalculate = 1 WHERE id=1");
                    $this->Helper->saveUserActivity($user['User']['id'], 'Sales Return', 'Save Edit', $this->data['id'], $creditMemoId);
                    echo json_encode($result);
                    exit;
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'Sales Return', 'Save Edit (Error)', $this->data['id']);
                    $result['code'] = 2;
                    echo json_encode($result);
                    exit;
                }
            } else {
                $this->Helper->saveUserActivity($user['User']['id'], 'Sales Return', 'Save Edit (Error Status)', $this->data['id']);
                $result['code'] = 2;
                echo json_encode($result);
                exit;
            }
        }
        $this->Helper->saveUserActivity($user['User']['id'], 'Sales Return', 'Edit', $id);
        $this->data = ClassRegistry::init('CreditMemo')->find('first', array(
            'conditions' => array('CreditMemo.status = 2', 'CreditMemo.id' => $id)
                )
        );
        $queryHasReceipt = mysql_query("SELECT id FROM credit_memo_receipts WHERE credit_memo_id=" . $id . " AND is_void = 0");
        $queryHasApplyInv = mysql_query("SELECT id FROM credit_memo_with_sales WHERE credit_memo_id=" . $id . " AND status > 0");
        if ($this->data['CreditMemo']['status'] == 2 && !mysql_num_rows($queryHasReceipt) && !mysql_num_rows($queryHasApplyInv)) {
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
                                'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id', 'ModuleCodeBranch.cm_code', 'Branch.currency_center_id', 'CurrencyCenter.symbol'),
                                'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])
                            ));
            // Get Loction Setting
            $locSetting = ClassRegistry::init('LocationSetting')->findById(5);
            $locCon     = '';
            if($locSetting['LocationSetting']['location_status'] == 1){
                $locCon = ' AND Location.is_for_sale = 0';
            }
            $joinUsers    = array('table' => 'user_location_groups', 'type' => 'INNER', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'));
            $joinLocation = array('table' => 'locations', 'type' => 'INNER', 'conditions' => array('locations.location_group_id=LocationGroup.id', $locCon));
            $locations    = ClassRegistry::init('Location')->find('all', array('joins' => array(array('table' => 'user_locations', 'type' => 'inner', 'conditions' => array('user_locations.location_id=Location.id'))), 'conditions' => array('user_locations.user_id=' . $user['User']['id'] . ' AND Location.is_active=1'.$locCon), 'order' => 'Location.name'));
            $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('fields' => array('LocationGroup.id', 'LocationGroup.name'),'joins' => array($joinUsers, $joinLocation),'conditions' => array('user_location_groups.user_id=' . $user['User']['id'], 'LocationGroup.is_active' => '1', 'LocationGroup.location_group_type_id != 1'), 'group' => 'LocationGroup.id'));
            $reasons = ClassRegistry::init('Reason')->find('list', array('conditions' => array('Reason.is_active' => '1'), 'order' => 'Reason.name'));
            $arAccountId = $this->data['CreditMemo']['ar_id'];
            $this->set(compact("locations", "locationGroups", "reasons", "companies", "branches", "arAccountId"));
        } else {
            echo "Sorry Cannot Edit";
            exit;
        }
    }

    function editDetail($id = null) {
        $this->layout = 'ajax';
        if ($id >= 0) {
            $user = $this->getCurrentUser();
            $branches = ClassRegistry::init('Branch')->find('all',
                            array(
                                'joins' => array(
                                    array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id')),
                                    array('table' => 'module_code_branches AS ModuleCodeBranch', 'type' => 'left', 'conditions' => array('ModuleCodeBranch.branch_id=Branch.id'))
                                ),
                                'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id', 'ModuleCodeBranch.cm_code', 'Branch.currency_center_id', 'CurrencyCenter.symbol'),
                                'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])
                            ));
            $credit_memo = ClassRegistry::init('CreditMemo')->find('first', array('conditions' => array('CreditMemo.status = 2', 'CreditMemo.id' => $id)));
            $creditMemoDetails = ClassRegistry::init('CreditMemoDetail')->find('all', array('conditions' => array('CreditMemoDetail.credit_memo_id' => $id)));
            $creditMemoServices = ClassRegistry::init('CreditMemoService')->find('all', array('conditions' => array('CreditMemoService.credit_memo_id' => $id)));
            $creditMemoMiscs = ClassRegistry::init('CreditMemoMisc')->find('all', array('conditions' => array('CreditMemoMisc.credit_memo_id' => $id)));
            $uoms = ClassRegistry::init('Uom')->find('all', array('fields' => array('Uom.id', 'Uom.name'), 'conditions' => array('Uom.is_active' => 1)));
            $this->set(compact('branches', 'uoms', "creditMemoDetails", "credit_memo", "creditMemoServices", "creditMemoMiscs"));
        } else {
            exit;
        }
    }

    function receive($id = null) {
        $this->layout = 'ajax';
//        if (!$id && empty($this->data)) {
//            exit;
//        }
//        $user = $this->getCurrentUser();
//        if (!empty($this->data)) {
//            $db = ConnectionManager::getDataSource('default');
//            mysql_select_db($db->config['database']);
//            
//            $creditMemo = $this->CreditMemo->read(null, $this->data['memo_id']);
//            if ($creditMemo['CreditMemo']['status'] == 1) {
//                $r = 0;
//                $restCode = array();
//                $dateNow  = date("Y-m-d H:i:s");
//                $this->loadModel('CreditMemoDetail');
//                $creditMemo['CreditMemo']['id'] = $this->data['memo_id'];
//                $creditMemo['CreditMemo']['status'] = 2;
//                if ($this->CreditMemo->save($creditMemo)) {
//                    // Convert to REST
//                    $restCode[$r]['status']      = 2;
//                    $restCode[$r]['modified']    = $dateNow;
//                    $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
//                    $restCode[$r]['dbtodo'] = 'credit_memos';
//                    $restCode[$r]['actodo'] = 'ut';
//                    $restCode[$r]['con']    = "sys_code = '".$creditMemo['CreditMemo']['sys_code']."'";
//                    $r++;
//                    $credit_memo_id = $this->data['memo_id'];
//                    $creditMemoDetails = ClassRegistry::init('CreditMemoDetail')->find("all", array('conditions' => array('CreditMemoDetail.credit_memo_id' => $credit_memo_id)));
//                    $dateCM = $creditMemo['CreditMemo']['order_date'];
//                    foreach ($creditMemoDetails as $creditMemoDetail) {
//                        
//                        $totalQtyOrder = (($creditMemoDetail['CreditMemoDetail']['qty'] + $creditMemoDetail['CreditMemoDetail']['qty_free']) * $creditMemoDetail['CreditMemoDetail']['conversion']);
//                        $qtyOrder      = ($creditMemoDetail['CreditMemoDetail']['qty'] * $creditMemoDetail['CreditMemoDetail']['conversion']);
//                        $qtyFree       = ($creditMemoDetail['CreditMemoDetail']['qty_free'] * $creditMemoDetail['CreditMemoDetail']['conversion']);
//                        $totalAmountSales = ($creditMemoDetail['CreditMemoDetail']['total_price'] - $creditMemoDetail['CreditMemoDetail']['discount_amount']);
//                        if($totalAmountSales > 0){
//                            $unitPrice = $this->Helper->replaceThousand(number_format($totalAmountSales / $totalQtyOrder, 9));
//                        } else {
//                            $unitPrice = 0;
//                        }
//                        // Update Inventory (Sales Return)
//                        $data = array();
//                        $data['module_type']        = 11;
//                        $data['credit_memo_id']     = $creditMemoDetail['CreditMemoDetail']['credit_memo_id'];
//                        $data['product_id']         = $creditMemoDetail['CreditMemoDetail']['product_id'];
//                        $data['location_id']        = $creditMemo['CreditMemo']['location_id'];
//                        $data['location_group_id']  = $creditMemo['CreditMemo']['location_group_id'];
//                        $data['lots_number']  = $creditMemoDetail['CreditMemoDetail']['lots_number'];
//                        $data['expired_date'] = $creditMemoDetail['CreditMemoDetail']['expired_date'];
//                        $data['date']         = $dateCM;
//                        $data['total_qty']    = $totalQtyOrder;
//                        $data['total_order']  = $qtyOrder;
//                        $data['total_free']   = $qtyFree;
//                        $data['user_id']      = $user['User']['id'];
//                        $data['customer_id']  = $creditMemo['CreditMemo']['customer_id'];
//                        $data['vendor_id']    = "";
//                        $data['unit_cost']    = 0;
//                        $data['unit_price']   = $unitPrice;
//                        // Update Invetory Location
//                        $this->Inventory->saveInventory($data);
//                        // Update Inventory Group
//                        $this->Inventory->saveGroupTotalDetail($data);
//                        // Convert to REST
//                        $restCode[$r] = $this->Helper->convertToDataSync($data, 'inventories');
//                        $restCode[$r]['module_type']  = 11;
//                        $restCode[$r]['total_qty']    = $totalQtyOrder;
//                        $restCode[$r]['total_order']  = $qtyOrder;
//                        $restCode[$r]['total_free']   = $qtyFree;
//                        $restCode[$r]['expired_date'] = $data['expired_date'];
//                        $restCode[$r]['vendor_id']    = "";
//                        $restCode[$r]['unit_cost']    = 0;
//                        $restCode[$r]['customer_id']       = $this->Helper->getSQLSyncCode("customers", $creditMemo['CreditMemo']['customer_id']);
//                        $restCode[$r]['credit_memo_id']    = $this->Helper->getSQLSyncCode("credit_memos", $creditMemo['CreditMemo']['id']);
//                        $restCode[$r]['product_id']        = $this->Helper->getSQLSyncCode("products", $creditMemoDetail['CreditMemoDetail']['product_id']);
//                        $restCode[$r]['location_id']       = $this->Helper->getSQLSyncCode("locations", $creditMemo['CreditMemo']['location_id']);
//                        $restCode[$r]['location_group_id'] = $this->Helper->getSQLSyncCode("location_groups", $creditMemo['CreditMemo']['location_group_id']);
//                        $restCode[$r]['user_id']           = $this->Helper->getSQLSyncCode("users", $user['User']['id']);
//                        $restCode[$r]['dbtype']  = 'saveInv,GroupDetail';
//                        $restCode[$r]['actodo']  = 'inv';
//                        $r++;
//                        // Insert Inventory Unit Cost
//                        mysql_query("INSERT INTO `inventory_unit_costs` (`product_id`, `total_qty`, `unit_cost`, `created`)
//                                     VALUES (".$creditMemoDetail['CreditMemoDetail']['product_id'].", ".$totalQtyOrder.", ".$creditMemoDetail['Product']['unit_cost'].", '".date("Y-m-d H:i:s")."');");
//                        // Convert to REST
//                        $restCode[$r]['unit_cost']  = $creditMemoDetail['Product']['unit_cost'];
//                        $restCode[$r]['total_qty']  = $totalQtyOrder;
//                        $restCode[$r]['created']    = $dateNow;
//                        $restCode[$r]['product_id'] = $this->Helper->getSQLSysCode("products", $creditMemoDetail['CreditMemoDetail']['product_id']);
//                        $restCode[$r]['dbtodo'] = 'inventory_unit_costs';
//                        $restCode[$r]['actodo'] = 'is';
//                        $r++;
//                    }
//                    // Save File Send
//                    $this->Helper->sendFileToSync($restCode, 0, 0);
//                    $this->Helper->saveUserActivity($user['User']['id'], 'Sales Return', 'Save Receive', $this->data['memo_id']);
//                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
//                    exit;
//                } else {
//                    $this->Helper->saveUserActivity($user['User']['id'], 'Sales Return', 'Save Receive (Error)', $this->data['memo_id']);
//                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
//                    exit;
//                }
//            } else {
//                $this->Helper->saveUserActivity($user['User']['id'], 'Sales Return', 'Save Receive (Error Status)', $this->data['memo_id']);
//                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
//                exit;
//            }
//        }
//        $this->Helper->saveUserActivity($user['User']['id'], 'Sales Return', 'Receive', $id);
//        $credit_memo = ClassRegistry::init('CreditMemo')->find('first', array(
//            'conditions' => array('CreditMemo.status = 1', 'CreditMemo.id' => $id)
//                )
//        );
//        // Check Status
//        if($credit_memo['CreditMemo']['status'] == 1){
//            $creditMemoDetails = ClassRegistry::init('CreditMemoDetail')->find('all', array(
//                'conditions' => array('CreditMemoDetail.credit_memo_id' => $id)
//                    )
//            );
//            $this->set(compact("credit_memo", "creditMemoDetails", "id"));
//        }else{
//            exit;
//        }
        exit;
    }

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
        $this->loadModel('CreditMemoReceipt');
        $this->loadModel('CreditMemoDetail');
        $this->loadModel('InventoryValuation');
        $this->loadModel('CreditMemoWithSale');
        $creditMemo = ClassRegistry::init('CreditMemo')->find("first", array('conditions' => array('CreditMemo.id' => $id)));
        $creditMemoDetails = ClassRegistry::init('CreditMemoDetail')->find("all", array('conditions' => array('CreditMemoDetail.credit_memo_id' => $id)));
        $queryHasReceipt  = mysql_query("SELECT id FROM credit_memo_receipts WHERE credit_memo_id=" . $id . " AND is_void = 0");
        $queryHasApplyInv = mysql_query("SELECT id FROM credit_memo_with_sales WHERE credit_memo_id=" . $id . " AND status > 0");
        if(@mysql_num_rows($queryHasReceipt) && @mysql_num_rows($queryHasApplyInv)){
            $this->Helper->saveUserActivity($user['User']['id'], 'Sales Return', 'Void (Error has transaction with other modules)', $id);
            echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
            exit;
        }

        if ($creditMemo['CreditMemo']['status'] == 2) {
            $isLock = 0;
            foreach($creditMemoDetails AS $creditMemoDetail){
                // Inventory By Order Date
                $totalQtyByDate = 0;
                $sqlInv = mysql_query("SELECT SUM(qty) FROM inventories WHERE location_group_id = ".$creditMemo['CreditMemo']['location_group_id']." AND location_id = ".$creditMemo['CreditMemo']['location_id']." AND product_id = ".$creditMemoDetail['Product']['id']." AND date <= '".$creditMemo['CreditMemo']['order_date']."' AND date_expired = '".$creditMemoDetail['CreditMemoDetail']['date_expired']."'");
                if(mysql_num_rows($sqlInv)){
                    $rowInv = mysql_fetch_array($sqlInv);
                    $totalQtyByDate = $rowInv[0];
                }
                // Total Qty On Hand
                $totalQtyOnHand = 0;
                $sqlOnHand = mysql_query("SELECT SUM(total_qty - total_order) FROM ".$creditMemo['CreditMemo']['location_id']."_inventory_totals WHERE product_id = ".$creditMemoDetail['Product']['id']." AND expired_date = '".$creditMemoDetail['CreditMemoDetail']['date_expired']."'");
                if(mysql_num_rows($sqlOnHand)){
                    $rowOnHand = mysql_fetch_array($sqlOnHand);
                    $totalQtyOnHand = $rowOnHand[0];
                }
                $totalQtySalesReturn = ($creditMemoDetail['CreditMemoDetail']['qty'] + $creditMemoDetail['CreditMemoDetail']['qty_free']) * $creditMemoDetail['CreditMemoDetail']['conversion'];
                if($totalQtySalesReturn > $totalQtyByDate){
                    $isLock = 1;
                    break;
                } else {
                    if($totalQtySalesReturn > $totalQtyOnHand){
                        $isLock = 1;
                        break;
                    }
                }
            }
            if($isLock == 1){
                $this->Helper->saveUserActivity($user['User']['id'], 'Sales Return', 'Delete (Error with total qty)', $id);
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                exit;
            }
            // Update Inventory Valuation
            $this->InventoryValuation->updateAll(
                    array('InventoryValuation.is_active' => 2), array('InventoryValuation.credit_memo_id' => $id)
            );
            // Convert to REST
            $restCode[$r]['is_active']   = 2;
            $restCode[$r]['dbtodo'] = 'inventory_valuations';
            $restCode[$r]['actodo'] = 'ut';
            $restCode[$r]['con']    = "credit_memo_id = (SELECT id FROM credit_memos WHERE sys_code = '".$creditMemo['CreditMemo']['sys_code']."' LIMIT 1)";
            $r++;
            // General Ledger
            $this->GeneralLedger->updateAll(
                    array('GeneralLedger.is_active' => 2, 'GeneralLedger.modified_by' => $user['User']['id']), array('GeneralLedger.credit_memo_id' => $id)
            );
            // Convert to REST
            $restCode[$r]['is_active']   = 2;
            $restCode[$r]['modified']    = $dateNow;
            $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
            $restCode[$r]['dbtodo'] = 'general_ledgers';
            $restCode[$r]['actodo'] = 'ut';
            $restCode[$r]['con']    = "credit_memo_id = (SELECT id FROM credit_memos WHERE sys_code = '".$creditMemo['CreditMemo']['sys_code']."' LIMIT 1)";
            $r++;
            // Update Sales Return
            $this->CreditMemo->updateAll(
                    array('CreditMemo.status' => 0, 'CreditMemo.modified_by' => $user['User']['id']), array('CreditMemo.id' => $id)
            );
            // Convert to REST
            $restCode[$r]['status']      = 0;
            $restCode[$r]['modified']    = $dateNow;
            $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
            $restCode[$r]['dbtodo'] = 'credit_memos';
            $restCode[$r]['actodo'] = 'ut';
            $restCode[$r]['con']    = "sys_code = '".$creditMemo['CreditMemo']['sys_code']."'";
            $r++;
            $creditMemoDetails = ClassRegistry::init('CreditMemoDetail')->find("all", array('conditions' => array('CreditMemoDetail.credit_memo_id' => $id)));
            foreach($creditMemoDetails AS $creditMemoDetail){
                $totalQtyOrder = (($creditMemoDetail['CreditMemoDetail']['qty'] + $creditMemoDetail['CreditMemoDetail']['qty_free']) * $creditMemoDetail['CreditMemoDetail']['conversion']);
                $qtyOrder      = ($creditMemoDetail['CreditMemoDetail']['qty'] * $creditMemoDetail['CreditMemoDetail']['conversion']);
                $qtyFree       = ($creditMemoDetail['CreditMemoDetail']['qty_free'] * $creditMemoDetail['CreditMemoDetail']['conversion']);
                // Update Inventory (Sales Return)
                $data = array();
                $data['module_type']        = 19;
                $data['credit_memo_id']     = $creditMemo['CreditMemo']['id'];
                $data['product_id']         = $creditMemoDetail['CreditMemoDetail']['product_id'];
                $data['location_id']        = $creditMemo['CreditMemo']['location_id'];
                $data['location_group_id']  = $creditMemo['CreditMemo']['location_group_id'];
                $data['lots_number']  = $creditMemoDetail['CreditMemoDetail']['lots_number'];
                $data['expired_date'] = $creditMemoDetail['CreditMemoDetail']['expired_date'];
                $data['date']         = $creditMemo['CreditMemo']['order_date'];
                $data['total_qty']    = $totalQtyOrder;
                $data['total_order']  = $qtyOrder;
                $data['total_free']   = $qtyFree;
                $data['user_id']      = $user['User']['id'];
                $data['customer_id']  = $creditMemo['CreditMemo']['customer_id'];
                $data['vendor_id']    = "";
                $data['unit_cost']    = 0;
                $data['unit_price']   = $creditMemoDetail['CreditMemoDetail']['total_price'] - $creditMemoDetail['CreditMemoDetail']['discount_amount'];
                // Update Invetory Location
                $this->Inventory->saveInventory($data);
                // Update Inventory Group
                $this->Inventory->saveGroupTotalDetail($data);
                // Convert to REST
                $restCode[$r] = $this->Helper->convertToDataSync($data, 'inventories');
                $restCode[$r]['module_type']  = 19;
                $restCode[$r]['total_qty']    = $totalQtyOrder;
                $restCode[$r]['total_order']  = $qtyOrder;
                $restCode[$r]['total_free']   = $qtyFree;
                $restCode[$r]['expired_date'] = $data['expired_date'];
                $restCode[$r]['vendor_id']    = "";
                $restCode[$r]['unit_cost']    = 0;
                $restCode[$r]['customer_id']       = $this->Helper->getSQLSyncCode("customers", $creditMemo['CreditMemo']['customer_id']);
                $restCode[$r]['credit_memo_id']    = $this->Helper->getSQLSyncCode("credit_memos", $creditMemo['CreditMemo']['id']);
                $restCode[$r]['product_id']        = $this->Helper->getSQLSyncCode("products", $creditMemoDetail['CreditMemoDetail']['product_id']);
                $restCode[$r]['location_id']       = $this->Helper->getSQLSyncCode("locations", $creditMemo['CreditMemo']['location_id']);
                $restCode[$r]['location_group_id'] = $this->Helper->getSQLSyncCode("location_groups", $creditMemo['CreditMemo']['location_group_id']);
                $restCode[$r]['user_id']           = $this->Helper->getSQLSyncCode("users", $user['User']['id']);
                $restCode[$r]['dbtype']  = 'saveInv,GroupDetail';
                $restCode[$r]['actodo']  = 'inv';
                $r++;
            }
            // Save File Send
            $this->Helper->sendFileToSync($restCode, 0, 0);
            // Recalculate Average Cost
            $sqlTrack = mysql_query("SELECT val FROM tracks WHERE id = 1");
            $track    = mysql_fetch_array($sqlTrack);
            $dateReca = $creditMemo['CreditMemo']['order_date'];
            $dateReca = date("Y-m-d", strtotime(date("Y-m-d", strtotime($dateReca)) . " -1 day"));
            if($track[0] == "0000-00-00" || (strtotime($track[0]) >= strtotime($dateReca))){
                mysql_query("UPDATE tracks SET val='".$dateReca."', is_recalculate = 1 WHERE id=1");
            }
            $this->Helper->saveUserActivity($user['User']['id'], 'Sales Return', 'Void', $id);
            echo MESSAGE_DATA_HAS_BEEN_DELETED;
            exit;
        }else{
            $this->Helper->saveUserActivity($user['User']['id'], 'Sales Return', 'Void (Error Status)', $id);
            echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
            exit;
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
        $this->loadModel('CreditMemoReceipt');
        $receipt = ClassRegistry::init('CreditMemoReceipt')->find("first", array('conditions' => array('CreditMemoReceipt.id' => $id)));
        $this->CreditMemoReceipt->updateAll(
                array('CreditMemoReceipt.is_void' => 1, 'CreditMemoReceipt.modified_by' => $user['User']['id']), array('CreditMemoReceipt.id' => $id)
        );
        // Convert to REST
        $restCode[$r]['is_void']     = 1;
        $restCode[$r]['modified']    = $dateNow;
        $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
        $restCode[$r]['dbtodo'] = 'credit_memo_receipts';
        $restCode[$r]['actodo'] = 'ut';
        $restCode[$r]['con']    = "sys_code = '".$receipt['CreditMemoReceipt']['sys_code']."'";
        $r++;
        $exchangeRate = ClassRegistry::init('ExchangeRate')->find("first", array("conditions" => array("ExchangeRate.id" => $receipt['CreditMemoReceipt']['exchange_rate_id'])));
        if(!empty($exchangeRate) && $exchangeRate['ExchangeRate']['rate_to_sell'] > 0){
            $totalPaidOther = $receipt['CreditMemoReceipt']['amount_other'] / $exchangeRate['ExchangeRate']['rate_to_sell'];
        } else {
            $totalPaidOther = 0;
        }
        $total_amount = $receipt['CreditMemoReceipt']['amount_us'] + $totalPaidOther;

        mysql_query("UPDATE credit_memos SET balance = balance+" . $total_amount . " WHERE id=" . $receipt['CreditMemoReceipt']['credit_memo_id']);
        // Convert to REST
        $restCode[$r]['balance']     = '(balance+'.$total_amount.')';
        $restCode[$r]['modified']    = $dateNow;
        $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
        $restCode[$r]['dbtodo'] = 'credit_memos';
        $restCode[$r]['actodo'] = 'ut';
        $restCode[$r]['con']    = "sys_code = '".$receipt['CreditMemo']['sys_code']."'";
        $r++;
        $this->GeneralLedger->updateAll(
                array('GeneralLedger.is_active' => 2, 'GeneralLedger.modified_by' => $user['User']['id']), array('GeneralLedger.credit_memo_receipt_id' => $id)
        );
        // Convert to REST
        $restCode[$r]['is_active']   = 2;
        $restCode[$r]['modified']    = $dateNow;
        $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
        $restCode[$r]['dbtodo'] = 'general_ledgers';
        $restCode[$r]['actodo'] = 'ut';
        $restCode[$r]['con']    = "credit_memo_receipt_id = (SELECT id FROM credit_memo_receipts WHERE sys_code = '".$receipt['CreditMemoReceipt']['sys_code']."' LIMIT 1)";
        // Save File Send
        $this->Helper->sendFileToSync($restCode, 0, 0);
        $this->Helper->saveUserActivity($user['User']['id'], 'Sales Return Receipt', 'Void', $id);
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }

    function deleteCmWSlae($id) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $r = 0;
        $restCode = array();
        $dateNow  = date("Y-m-d H:i:s");
        $user = $this->getCurrentUser();
        $result = array();
        $this->loadModel('CreditMemoWithSale');
        $this->loadModel('GeneralLedger');
        $cmWsale = $this->CreditMemoWithSale->read(null, $id);
        if ($cmWsale['CreditMemoWithSale']['status'] == 1) {
            mysql_query("UPDATE sales_orders SET balance = balance + " . $cmWsale['CreditMemoWithSale']['total_price'] . " WHERE id=" . $cmWsale['CreditMemoWithSale']['sales_order_id']);
            // Convert to REST
            $restCode[$r]['balance']     = '(balance+'.$cmWsale['CreditMemoWithSale']['total_price'].')';
            $restCode[$r]['modified']    = $dateNow;
            $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
            $restCode[$r]['dbtodo'] = 'sales_orders';
            $restCode[$r]['actodo'] = 'ut';
            $restCode[$r]['con']    = "sys_code = '".$cmWsale['SalesOrder']['sys_code']."'";
            $r++;
            mysql_query("UPDATE credit_memos SET balance = balance + " . $cmWsale['CreditMemoWithSale']['total_price'] . ", total_amount_invoice = (IF((total_amount_invoice - " . $cmWsale['CreditMemoWithSale']['total_price'] . ") < 0,0,(total_amount_invoice - " . $cmWsale['CreditMemoWithSale']['total_price'] . "))) WHERE id=" . $cmWsale['CreditMemoWithSale']['credit_memo_id']);
            // Convert to REST
            $restCode[$r]['balance']     = '(balance+'.$cmWsale['CreditMemoWithSale']['total_price'].')';
            $restCode[$r]['modified']    = $dateNow;
            $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
            $restCode[$r]['dbtodo'] = 'credit_memos';
            $restCode[$r]['actodo'] = 'ut';
            $restCode[$r]['con']    = "sys_code = '".$cmWsale['CreditMemo']['sys_code']."'";
            $r++;
            $this->CreditMemoWithSale->updateAll(
                    array('CreditMemoWithSale.status' => 0), array('CreditMemoWithSale.id' => $id)
            );
            // Convert to REST
            $restCode[$r]['status']      = 0;
            $restCode[$r]['modified']    = $dateNow;
            $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
            $restCode[$r]['dbtodo'] = 'credit_memo_with_sales';
            $restCode[$r]['actodo'] = 'ut';
            $restCode[$r]['con']    = "sys_code = '".$cmWsale['CreditMemoWithSale']['sys_code']."'";
            $r++;
            $this->GeneralLedger->updateAll(
                    array('GeneralLedger.is_active' => 2, 'GeneralLedger.modified_by' => $user['User']['id']),
                    array('GeneralLedger.credit_memo_with_sale_id' => $id)
            );
            // Convert to REST
            $restCode[$r]['is_active']   = 2;
            $restCode[$r]['modified']    = $dateNow;
            $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
            $restCode[$r]['dbtodo'] = 'general_ledgers';
            $restCode[$r]['actodo'] = 'ut';
            $restCode[$r]['con']    = "credit_memo_with_sale_id = (SELECT id FROM credit_memo_with_sales WHERE sys_code = '".$cmWsale['CreditMemoWithSale']['sys_code']."' LIMIT 1)";
            // Save File Send
            $this->Helper->sendFileToSync($restCode, 0, 0);
            $this->Helper->saveUserActivity($user['User']['id'], 'Sales Return Receipt', 'Void Apply to Invoice', $id);
            $result['result']   = 1;
        } else {
            $this->Helper->saveUserActivity($user['User']['id'], 'Sales Return Receipt', 'Void Apply to Invoice (Error Status)', $id);
            $result['result']   = 2;
        }
        echo json_encode($result);
        exit;
    }
    
    function editUnitPrice(){
        $this->layout = 'ajax';
    }
    
    function getProductFromSales($id = null){
        $this->layout = 'ajax';
        $result = array();
        if (empty($id)) {
            $result['error'] = 1;
            echo json_encode($result);
            exit;
        }
        $sqlSettingUomDeatil = mysql_query("SELECT uom_detail_option FROM setting_options");
        $rowSettingUomDetail = mysql_fetch_array($sqlSettingUomDeatil);
        $user = $this->getCurrentUser();
        $allowProductDiscount = $this->Helper->checkAccess($user['User']['id'], $this->params['controller'], 'discount');
        $allowEditPrice = $this->Helper->checkAccess($user['User']['id'], $this->params['controller'], 'editUnitPrice');
        // Check Permission Edit Price
        if($allowEditPrice){
            $readonly = '';
        }else{
            $readonly = 'readonly="readonly"';
        }
        $rowList = array();
        $rowLbl  = "";
        $index   = '';
        // Get Product
        $sqlSalesDetail  = mysql_query("SELECT products.is_expired_date AS is_expired_date, products.id AS product_id, products.code AS code, products.barcode AS barcode, products.name AS name, products.small_val_uom AS small_val_uom, products.price_uom_id AS price_uom_id, sd.qty AS qty, sd.qty_free AS qty_free, sd.qty_uom_id AS qty_uom_id, sd.conversion AS conversion, sd.discount_id AS discount_id, sd.discount_amount AS discount_amount, sd.discount_percent AS discount_percent, sd.unit_price AS unit_price, sd.total_price AS total_price, sd.note AS note, so.customer_id AS customer_id FROM sales_order_details AS sd INNER JOIN sales_orders AS so ON so.id = sd.sales_order_id INNER JOIN products ON products.id = sd.product_id WHERE sd.sales_order_id = ".$id.";");
        while($rowDetail = mysql_fetch_array($sqlSalesDetail)){
            $index     = rand();
            $productName = str_replace('"', '&quot;', $rowDetail['name']);
            $sqlProCus = mysql_query("SELECT name FROM product_with_customers WHERE product_id = ".$rowDetail['product_id']." AND customer_id = ".$rowDetail['customer_id']." ORDER BY created DESC LIMIT 1");
            if(mysql_num_rows($sqlProCus)){
                $rowProCus   = mysql_fetch_array($sqlProCus);
                $productName = str_replace('"', '&quot;', $rowProCus['name']);
            }
            // Caculate Discount
            $discountAmt = $rowDetail['discount_amount'];
            // Open Tr
            $rowLbl .= '<tr class="tblCMList">';
            // Index
            $rowLbl .= '<td class="first" style="width:4%; text-align: center;padding: 0px; height: 30px;">'.++$index.'</td>';
            // UPC
            $rowLbl .= '<td style="width:7%; text-align: left; padding: 5px;"><span class="lblUPC">'.$rowDetail['barcode'].'</span></td>';
            // SKU
            $rowLbl .= '<td style="width:7%; text-align: left; padding: 5px;"><span class="lblSKU">'.$rowDetail['code'].'</span></td>';
            // Product
            $rowLbl .= '<td style="width:14%; text-align: left; padding: 5px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="hidden" id="product_id_'.$index.'" class="product_id" value="'.$rowDetail['product_id'].'" name="product_id[]" />';
            $rowLbl .= '<input type="hidden" id="service_id_'.$index.'" value="" name="service_id[]" />';
            $rowLbl .= '<input type="hidden" value="'.$rowDetail['discount_id'].'" name="discount_id[]" />';
            $rowLbl .= '<input type="hidden" value="'.$discountAmt.'" name="discount_amount[]" />';
            $rowLbl .= '<input type="hidden" value="'.$rowDetail['discount_percent'].'" name="discount_percent[]" />';
            $rowLbl .= '<input type="hidden" value="'.$rowDetail['conversion'].'" name="cm_conversion[]" class="cm_conversion" />';
            $rowLbl .= '<input type="hidden" value="'.$rowDetail['small_val_uom'].'" name="small_val_uom[]" class="small_val_uom" />';
            $rowLbl .= '<input type="hidden" value="'.$rowDetail['note'].'" name="note[]" id="note" readonly="readonly" class="note" />';
            $rowLbl .= '<input type="hidden" class="orgProName" value="PUC: '.$rowDetail['barcode'].'<br/><br/>SKU: '.$rowDetail['code'].'<br/><br/>Name: '.str_replace('"', '&quot;', $rowDetail['name']).'" />';
            $rowLbl .= '<input type="text" id="productName_'.$index.'" value="'.$rowDetail['name'].'" id="product" name="product[]" class="product validate[required]" style="width: 75%;" />';
            $rowLbl .= '<img alt="Note" src="'.$this->webroot.'img/button/note.png" class="noteAddCM" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Note\')" />';
            $rowLbl .= '<img alt="Information" src="'.$this->webroot.'img/button/view.png" class="btnProductCMInfo" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Information\')" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Lot Number
            $lotDispaly = '';
            if($rowSettingUomDetail[0] == 0){
                $lotDispaly = 'display: none;';
            }
            $rowLbl .= '<td style="width:7%; text-align: center;padding: 0px;'.$lotDispaly.'">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="text" id="lots_number_'.$index.'" name="lots_number[]" value="" style="width:90%;" class="lots_number" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Expired Date
            if($rowDetail['is_expired_date'] == 1){
                $classExp = 'class="expired_date validate[required]"';
                $disExp   = '';
            }else{
                $classExp = '';
                $disExp   = 'visibility: hidden;';
            }
            $dateExp = '';
            $rowLbl .= '<td style="width:10%; text-align: center;padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="text" id="expired_date_'.$index.'" name="expired_date[]" value="'.$dateExp.'" readonly="readonly" style="width:90%;'.$disExp.'" '.$classExp.' />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Qty
            $rowLbl .= '<td style="width:6%; text-align: center;padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="text" value="'.$rowDetail['qty'].'" id="qty_'.$index.'" name="qty[]" style="width:70%;" class="qty interger" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Qty Free
            $rowLbl .= '<td style="width:6%; text-align: center;padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="text" value="'.$rowDetail['qty_free'].'" id="qty_free_'.$index.'" name="qty_free[]" style="width:70%;" class="qty_free interger" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // UOM
            $query=mysql_query("SELECT id,name,abbr,1 AS conversion FROM uoms WHERE id=".$rowDetail['price_uom_id']."
                                UNION
                                SELECT id,name,abbr,(SELECT value FROM uom_conversions WHERE is_active=1 AND from_uom_id=".$rowDetail['price_uom_id']." AND to_uom_id=uoms.id) AS conversion FROM uoms WHERE id IN (SELECT to_uom_id FROM uom_conversions WHERE is_active=1 AND from_uom_id=".$rowDetail['price_uom_id'].")
                                ORDER BY conversion ASC");
            $i = 1;
            $length = mysql_num_rows($query);
            $optionUom = "";
            while($data=mysql_fetch_array($query)){
                $priceLbl   = "";
                $selected   = "";
                $isMain     = "other";
                $isSmall    = 0;
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
                    }
                }else{
                    $priceLbl .= 'price-uom-1="0" price-uom-2="0"';
                }
                $optionUom .= '<option '.$priceLbl.' '.$selected.' data-sm="'.$isSmall.'" data-item="'.$isMain.'" value="'.$data['id'].'" conversion="'.$data['conversion'].'">'.$data['name'].'</option>';
                $i++;
            }
            $rowLbl .= '<td style="width:9%; padding: 0px; text-align: center">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<select id="qty_uom_id_'.$index.'" style="width:80%; height: 20px;" name="qty_uom_id[]" class="qty_uom_id validate[required]">'.$optionUom.'</select>';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Unit Price
            $rowLbl .= '<td style="width:9%; padding: 0px; text-align: center">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="text" '.$readonly.' value="'.number_format($rowDetail['unit_price'], 2).'" id="unit_price_'.$index.'" name="unit_price[]" style="width:70%;" class="float unit_price" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Discount
            // Check Permission Discount
            if($allowProductDiscount){
                $btnDel = 'display: none;';
                if($discountAmt > 0){
                    $btnDel = '';
                }
                $disDisplay  = '<input type="text" value="'.number_format($discountAmt, 2).'" id="discount_'.$index.'" name="discount[]" class="discount btnDiscountCM float" style="width: 60%;" readonly="readonly" />';
                $disDisplay .= '<img alt="Remove" src="'.$this->webroot.'img/button/cross.png" class="btnRemoveDiscount" align="absmiddle" style="cursor: pointer; '.$btnDel.'" onmouseover="Tip(\'Remove\')" />';
            }else{
                $disDisplay = '<input type="hidden" value="'.number_format($discountAmt, 2).'" id="discount_'.$index.'" name="discount[]" class="discount btnDiscountCM float" value="0" style="width: 60%;" readonly="readonly" />';
            }
            $rowLbl .= '<td style="width:8%; padding: 0px; text-align: center">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= $disDisplay;
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Total Price
            $rowLbl .= '<td style="width:9%; text-align: center; padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="hidden" value="'.number_format($rowDetail['total_price'], 2).'" id="h_total_price_'.$index.'" class="h_total_price float" name="h_total_price[]" />';
            $rowLbl .= '<input type="text" '.$readonly.' value="'.number_format(($rowDetail['total_price'] - $discountAmt), 2).'" id="total_price_'.$index.'" name="total_price[]" style="width:84%" class="float total_price" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Button Remove
            $rowLbl .= '<td style="width:4%">';
            $rowLbl .= '<img alt="Remove" src="'.$this->webroot.'img/button/cross.png" class="btnRemoveCM" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Remove\')" />';
            $rowLbl .= '</td>';
            // Close Tr
            $rowLbl .= '</tr>';
        }
        // Get Service
        $sqlSalesService  = mysql_query("SELECT services.code AS code, services.name AS name, uoms.abbr AS uom, uoms.id AS uom_id, sd.service_id AS service_id, sd.qty AS qty, sd.qty_free AS qty_free, sd.discount_id AS discount_id, sd.discount_amount AS discount_amount, sd.discount_percent AS discount_percent, sd.unit_price AS unit_price, sd.total_price AS total_price, sd.note AS note FROM sales_order_services AS sd INNER JOIN services ON services.id = sd.service_id INNER JOIN uoms ON uoms.id = services.uom_id WHERE sd.sales_order_id = ".$id.";");
        while($rowService = mysql_fetch_array($sqlSalesService)){
            $index   = rand();
            // Open Tr
            $rowLbl .= '<tr class="tblCMList">';
            // Index
            $rowLbl .= '<td class="first" style="width:4%; text-align: center;padding: 0px; height: 30px;">'.++$index.'</td>';
            // UPC
            $rowLbl .= '<td style="width:7%; text-align: left; padding: 5px;"><span class="lblUPC"></span></td>';
            // SKU
            $rowLbl .= '<td style="width:7%; text-align: left; padding: 5px;"><span class="lblSKU">'.$rowService['code'].'</span></td>';
            // Product
            $rowLbl .= '<td style="width:14%; text-align: left; padding: 5px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="hidden" id="product_id_'.$index.'" class="product_id" value="" name="product_id[]" />';
            $rowLbl .= '<input type="hidden" id="service_id_'.$index.'" value="'.$rowService['service_id'].'" name="service_id[]" />';
            $rowLbl .= '<input type="hidden" value="'.$rowService['discount_id'].'" name="discount_id[]" />';
            $rowLbl .= '<input type="hidden" value="'.$rowService['discount_amount'].'" name="discount_amount[]" />';
            $rowLbl .= '<input type="hidden" value="'.$rowService['discount_percent'].'" name="discount_percent[]" />';
            $rowLbl .= '<input type="hidden" value="1" name="cm_conversion[]" class="cm_conversion" />';
            $rowLbl .= '<input type="hidden" value="1" name="small_val_uom[]" class="small_val_uom" />';
            $rowLbl .= '<input type="hidden" value="'.$rowService['note'].'" name="note[]" id="note" readonly="readonly" class="note" />';
            $rowLbl .= '<input type="text" id="productName_'.$index.'" value="'.$rowService['name'].'" id="product" readonly="readonly" name="product[]" class="product validate[required]" style="width: 75%;" />';
            $rowLbl .= '<img alt="Note" src="'.$this->webroot.'img/button/note.png" class="noteAddCM" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Note\')" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Lot Number
            $lotDispaly = '';
            if($rowSettingUomDetail[0] == 0){
                $lotDispaly = 'display: none;';
            }
            $rowLbl .= '<td style="width:7%; text-align: center;padding: 0px;'.$lotDispaly.'">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="text" id="lots_number_'.$index.'" name="lots_number[]" value="" style="width:90%; display: none;" class="lots_number" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Expired Date
            $rowLbl .= '<td style="width:10%; text-align: center;padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="text" id="expired_date_'.$index.'" name="expired_date[]" value="" readonly="readonly" style="width:90%; display: none;" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Qty
            $rowLbl .= '<td style="width:6%; text-align: center;padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="text" value="'.$rowService['qty'].'" id="qty_'.$index.'" name="qty[]" style="width:70%;" class="qty interger" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Qty Free
            $rowLbl .= '<td style="width:6%; text-align: center;padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="text" value="'.$rowService['qty_free'].'" id="qty_free_'.$index.'" name="qty_free[]" style="width:70%;" class="qty_free interger" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // UOM
            $optionUom = '<option value="'.$rowService['uom_id'].'" conversion="1" selected="selected">'.$rowService['uom'].'</option>';
            $rowLbl .= '<td style="width:9%; padding: 0px; text-align: center">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<select id="qty_uom_id_'.$index.'" style="width:80%; height: 20px;" name="qty_uom_id[]" class="qty_uom_id">'.$optionUom.'</select>';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Unit Price
            $rowLbl .= '<td style="width:9%; padding: 0px; text-align: center">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="text" '.$readonly.' value="'.number_format($rowService['unit_price'], 2).'" id="unit_price_'.$index.'" name="unit_price[]" style="width:70%;" class="float unit_price" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Discount
            // Check Permission Discount
            if($allowProductDiscount){
                $btnDel = 'display: none;';
                if($rowService['discount_amount'] > 0){
                    $btnDel = '';
                }
                $disDisplay  = '<input type="text" value="'.number_format($rowService['discount_amount'], 2).'" id="discount_'.$index.'" name="discount[]" class="discount btnDiscountCM float" style="width: 60%;" readonly="readonly" />';
                $disDisplay .= '<img alt="Remove" src="'.$this->webroot.'img/button/cross.png" class="btnRemoveDiscount" align="absmiddle" style="cursor: pointer; '.$btnDel.'" onmouseover="Tip(\'Remove\')" />';
            }else{
                $disDisplay = '<input type="hidden" value="'.number_format($rowService['discount_amount'], 2).'" id="discount_'.$index.'" name="discount[]" class="discount btnDiscountCM float" value="0" style="width: 60%;" readonly="readonly" />';
            }
            $rowLbl .= '<td style="width:8%; padding: 0px; text-align: center">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= $disDisplay;
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Total Price
            $rowLbl .= '<td style="width:9%; text-align: center; padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="hidden" value="'.number_format($rowService['total_price'], 2).'" id="h_total_price_'.$index.'" class="h_total_price float" name="h_total_price[]" />';
            $rowLbl .= '<input type="text" '.$readonly.' value="'.number_format(($rowService['total_price'] - $rowService['discount_amount']), 2).'" id="total_price_'.$index.'" name="total_price[]" style="width:84%" class="float total_price" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Button Remove
            $rowLbl .= '<td style="width:4%">';
            $rowLbl .= '<img alt="Remove" src="'.$this->webroot.'img/button/cross.png" class="btnRemoveCM" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Remove\')" />';
            $rowLbl .= '</td>';
            // Close Tr
            $rowLbl .= '</tr>';
        }
        // Get Miscs
        $sqlSalesMisc  = mysql_query("SELECT sd.description AS name, sd.qty AS qty, sd.qty_uom_id AS qty_uom_id, sd.qty_free AS qty_free, sd.discount_id AS discount_id, sd.discount_amount AS discount_amount, sd.discount_percent AS discount_percent, sd.unit_price AS unit_price, sd.total_price AS total_price, sd.note AS note FROM sales_order_miscs AS sd WHERE sd.sales_order_id = ".$id.";");
        while($rowMisc = mysql_fetch_array($sqlSalesMisc)){
            $index   = rand();
            // Open Tr
            $rowLbl .= '<tr class="tblCMList">';
            // Index
            $rowLbl .= '<td class="first" style="width:4%; text-align: center;padding: 0px; height: 30px;">'.++$index.'</td>';
            // UPC
            $rowLbl .= '<td style="width:7%; text-align: left; padding: 5px;"><span class="lblUPC"></span></td>';
            // SKU
            $rowLbl .= '<td style="width:7%; text-align: left; padding: 5px;"><span class="lblSKU"></span></td>';
            // Product
            $rowLbl .= '<td style="width:14%; text-align: left; padding: 5px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="hidden" id="product_id_'.$index.'" class="product_id" value="" name="product_id[]" />';
            $rowLbl .= '<input type="hidden" id="service_id_'.$index.'" value="" name="service_id[]" />';
            $rowLbl .= '<input type="hidden" value="'.$rowMisc['discount_id'].'" name="discount_id[]" />';
            $rowLbl .= '<input type="hidden" value="'.$rowMisc['discount_amount'].'" name="discount_amount[]" />';
            $rowLbl .= '<input type="hidden" value="'.$rowMisc['discount_percent'].'" name="discount_percent[]" />';
            $rowLbl .= '<input type="hidden" value="1" name="cm_conversion[]" class="cm_conversion" />';
            $rowLbl .= '<input type="hidden" value="1" name="small_val_uom[]" class="small_val_uom" />';
            $rowLbl .= '<input type="hidden" value="'.$rowMisc['note'].'" name="note[]" id="note" readonly="readonly" class="note" />';
            $rowLbl .= '<input type="text" id="productName_'.$index.'" value="'.$rowMisc['name'].'" id="product" readonly="readonly" name="product[]" class="product validate[required]" style="width: 75%;" />';
            $rowLbl .= '<img alt="Note" src="'.$this->webroot.'img/button/note.png" class="noteAddCM" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Note\')" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Lot Number
            $lotDispaly = '';
            if($rowSettingUomDetail[0] == 0){
                $lotDispaly = 'display: none;';
            }
            $rowLbl .= '<td style="width:7%; text-align: center;padding: 0px;'.$lotDispaly.'">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="text" id="lots_number_'.$index.'" name="lots_number[]" value="" style="width:90%; display: none;" class="lots_number" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Expired Date
            $rowLbl .= '<td style="width:10%; text-align: center;padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="text" id="expired_date_'.$index.'" name="expired_date[]" value="" readonly="readonly" style="width:90%; display: none;" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Qty
            $rowLbl .= '<td style="width:6%; text-align: center;padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="text" value="'.$rowMisc['qty'].'" id="qty_'.$index.'" name="qty[]" style="width:70%;" class="qty interger" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Qty Free
            $rowLbl .= '<td style="width:6%; text-align: center;padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="text" value="'.$rowMisc['qty_free'].'" id="qty_free_'.$index.'" name="qty_free[]" style="width:70%;" class="qty_free interger" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // UOM
            $optionUom = '';
            $uoms = ClassRegistry::init('Uom')->find('all', array('fields' => array('Uom.id', 'Uom.name'), 'conditions' => array('Uom.is_active' => 1)));
            foreach($uoms AS $uom){
                $selected = '';
                if($uom['Uom']['id'] == $rowMisc['qty_uom_id']){
                    $selected = 'selected="selected"';
                }
                $optionUom .= '<option conversion="1" value="'.$uom['Uom']['id'].'" '.$selected.' conversion="1">'.$uom['Uom']['name'].'</option>';
            }
            $rowLbl .= '<td style="width:9%; padding: 0px; text-align: center">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<select id="qty_uom_id_'.$index.'" style="width:80%; height: 20px;" name="qty_uom_id[]" class="qty_uom_id">'.$optionUom.'</select>';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Unit Price
            $rowLbl .= '<td style="width:9%; padding: 0px; text-align: center">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="text" '.$readonly.' value="'.number_format($rowMisc['unit_price'], 2).'" id="unit_price_'.$index.'" name="unit_price[]" style="width:70%;" class="float unit_price" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Discount
            // Check Permission Discount
            if($allowProductDiscount){
                $btnDel = 'display: none;';
                if($rowMisc['discount_amount'] > 0){
                    $btnDel = '';
                }
                $disDisplay  = '<input type="text" value="'.number_format($rowMisc['discount_amount'], 2).'" id="discount_'.$index.'" name="discount[]" class="discount btnDiscountCM float" style="width: 60%;" readonly="readonly" />';
                $disDisplay .= '<img alt="Remove" src="'.$this->webroot.'img/button/cross.png" class="btnRemoveDiscount" align="absmiddle" style="cursor: pointer; '.$btnDel.'" onmouseover="Tip(\'Remove\')" />';
            }else{
                $disDisplay = '<input type="hidden" value="'.number_format($rowMisc['discount_amount'], 2).'" id="discount_'.$index.'" name="discount[]" class="discount btnDiscountCM float" value="0" style="width: 60%;" readonly="readonly" />';
            }
            $rowLbl .= '<td style="width:8%; padding: 0px; text-align: center">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= $disDisplay;
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Total Price
            $rowLbl .= '<td style="width:9%; text-align: center; padding: 0px;">';
            $rowLbl .= '<div class="inputContainer" style="width:100%">';
            $rowLbl .= '<input type="hidden" value="'.number_format($rowMisc['total_price'], 2).'" id="h_total_price_'.$index.'" class="h_total_price float" name="h_total_price[]" />';
            $rowLbl .= '<input type="text" '.$readonly.' value="'.number_format(($rowMisc['total_price'] - $rowMisc['discount_amount']), 2).'" id="total_price_'.$index.'" name="total_price[]" style="width:84%" class="float total_price" />';
            $rowLbl .= '</div>';
            $rowLbl .= '</td>';
            // Button Remove
            $rowLbl .= '<td style="width:4%">';
            $rowLbl .= '<img alt="Remove" src="'.$this->webroot.'img/button/cross.png" class="btnRemoveCM" align="absmiddle" style="cursor: pointer;" onmouseover="Tip(\'Remove\')" />';
            $rowLbl .= '</td>';
            // Close Tr
            $rowLbl .= '</tr>';
        }
        $rowList['error']  = 0;
        $rowList['result'] = $rowLbl;
        echo json_encode($rowList);
        exit;
    }
    
    function searchSalesInvoice(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $this->loadModel('SalesOrder');
        $userPermission = 'SalesOrder.company_id IN (SELECT company_id FROM user_companies WHERE user_id ='.$user['User']['id'].') AND SalesOrder.branch_id IN (SELECT branch_id FROM user_branches WHERE user_id ='.$user['User']['id'].')';
        $salesOrders = $this->SalesOrder->find('all', array(
                    'conditions' => array('OR' => array(
                            'SalesOrder.so_code LIKE' => '%' . $this->params['url']['q'] . '%'
                        ), 
                        'SalesOrder.status' => 2,
                        $userPermission
                    ),
                    'limit' => $this->params['url']['limit']
                ));

        $this->set(compact('salesOrders'));
    }
    
    function invoiceDiscount(){
        $this->layout = 'ajax';
    }
    
    function viewCreditMemoIssued(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        // Check Module Exist
        $sqlDash = mysql_query("SELECT id FROM user_dashboards WHERE module_id = 504 AND user_id = {$user['User']['id']} LIMIT 1");
        if(!mysql_num_rows($sqlDash)){
            $this->loadModel('UserDashboard');
            $userDash = array();
            $userDash['UserDashboard']['user_id']      = $user['User']['id'];
            $userDash['UserDashboard']['module_id']    = 504;
            $userDash['UserDashboard']['display']      = 1;
            $userDash['UserDashboard']['auto_refresh'] = 1;
            $userDash['UserDashboard']['time_refresh'] = 5;
            $this->UserDashboard->save($userDash);
        }
    }
    
    function addReason(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $this->loadModel('Reason');
            $result = array();
            if ($this->Helper->checkDouplicate('name', 'reasons', $this->data['Reason']['name'])) {
                $this->Helper->saveUserActivity($user['User']['id'], 'CM Reason', 'Save Quick Add New (Name ready existed)');
                $result['error'] = 2;
                echo json_encode($result);
                exit;
            } else {
                $r = 0;
                $restCode  = array();
                $dateNow   = date("Y-m-d H:i:s");
                $this->Reason->create();
                $this->data['Reason']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $this->data['Reason']['created']    = $dateNow;
                $this->data['Reason']['created_by'] = $user['User']['id'];
                $this->data['Reason']['is_active'] = 1;
                if ($this->Reason->save($this->data)) {
                    $reasonId = $this->Reason->id;
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($this->data['Reason'], 'reasons');
                    $restCode[$r]['modified']   = $dateNow;
                    $restCode[$r]['dbtodo']     = 'reasons';
                    $restCode[$r]['actodo']     = 'is';
                    // Save File Send
                    $this->Helper->sendFileToSync($restCode, 0, 0);
                    // Save User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'CM Reason', 'Save Quick Add New', $reasonId);
                    $result['error']  = 0;
                    $result['option'] = '<option value="">'.INPUT_SELECT.'</option>';
                    $reasons = ClassRegistry::init('Reason')->find('all', array('order' => 'name', 'conditions' => array('is_active' => 1)));
                    foreach($reasons AS $reason){
                        $selected = '';
                        if($reason['Reason']['id'] == $reasonId){
                            $selected = 'selected="selected"';
                        }
                        $result['option'] .= '<option value="'.$reason['Reason']['id'].'" '.$selected.'>'.$reason['Reason']['name'].'</option>';
                    }
                    echo json_encode($result);
                    exit;
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'CM Reason', 'Save Quick Add New (Error)');
                    $result['error'] = 1;
                    echo json_encode($result);
                    exit;
                }
            }
        }
        $this->Helper->saveUserActivity($user['User']['id'], 'CM Reason', 'Quick Add New');
    }

}

?>