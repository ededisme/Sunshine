<?php

// GZip components
ob_start("ob_gzhandler");

class AppController extends Controller {

    var $helpers = array('Html', 'Form', 'Javascript', 'Session');
    var $components = array('Session');
    var $menu = array();
    
    function menu() {
        $checkDelivery = mysql_query("SELECT allow_delivery FROM setting_options WHERE 1");
        $rowDelivery = mysql_fetch_array($checkDelivery);
        $deliveryMenu = array('text' => MENU_DELIVERY_MANAGEMENT, 'url' => '/d/index', 'target' => 'ajax');
        if($rowDelivery[0] == 1){
            $deliveryMenu = array('text' => MENU_DELIVERY_MANAGEMENT, 'url' => '/deliveries/index', 'target' => 'ajax');
        }
        
        $this->menu = array(
            array('text' => MENU_DASHBOARD, 'url' => '/dashboards/index', 'target' => 'ajax'),
            
            array('text' => MENU_PATIENT_MANAGEMENT_SETTING, 'url' => '', 'target' => 'ajax',
                'submenu' => array(                    
                    array('text' => MENU_PATIENT_DISPLAY_WAITING_NUMBER, 'url' => '/dashboards/queueWaitingNumber', 'target' => 'blank'),
                    array('text' => MENU_PATIENT_MANAGEMENT_REGISTER, 'url' => '/patients/index', 'target' => 'ajax'),
                    array('text' => MENU_PATIENT_OPD, 'url' => '/patients/opdList', 'target' => 'ajax'),
                    array('text' => MENU_PATIENT_IPD, 'url' => '/patient_ipds/index', 'target' => 'ajax',
                        'submenu' => array(
                            array('text' => MENU_PATIENT_IPD_CERTIFICATE, 'url' => '/patient_ipd_certificates/index', 'target' => 'ajax'),
                        )
                    ),
                    array('text' => MENU_APPOINTMENT_MANAGEMENT, 'url' => '/appointments/index', 'target' => 'ajax'),
                    array('text' => MENU_PATIENT_MANAGEMENT_HISTORY, 'url' => '/doctors/patient', 'target' => 'ajax'),   
                    array('text' => TABLE_REFERRAL, 'url' => '/referrals/index', 'target' => 'ajax')
                )
            ),
            
            array('text' => TABLE_PARACLINIC, 'url' => '', 'target' => 'ajax',
                'submenu' => array(                           
                    array('text' => TABLE_OBSTETRIC, 'url' => '/echographie_patients/index', 'target' => 'ajax'),
                    array('text' => TABLE_CARDIA, 'url' => '/echo_service_cardias/index', 'target' => 'ajax'),
                    array('text' => TABLE_ECHO_SERVICE, 'url' => '/echo_services/index', 'target' => 'ajax'),
                    array('text' => TABLE_XRAY_SERVICE, 'url' => '/xray_services/index', 'target' => 'ajax'),
                    array('text' => TABLE_CYSTOSCOPY, 'url' => '/cystoscopy_services/index', 'target' => 'ajax'),
                )
            ),            
            array('text' => TABLE_MID_WIFE_SERVICE, 'url' => '/mid_wife_services/index', 'target' => 'ajax'),  
            
            array('text' => MENU_LABO_MANAGEMENT, 'url' => '', 'target' => '',
                'submenu' => array(
                    array('text' => MENU_LABO_RESULT, 'url' => '/labos/laboList', 'target' => 'ajax'),
                    array('text' => MENU_LABO_ITEMS, 'url' => '/labo_items/index', 'target' => 'ajax',
                        'submenu' => array(
                            array('text' => MENU_TITLE_ITEM, 'url' => '/labo_title_items/index', 'target' => 'ajax'),
                            array('text' => MENU_GROUP, 'url' => '/labo_item_categories/index', 'target' => 'ajax')
                        )
                    ),
                    array('text' => MENU_SUB_GROUP, 'url' => '/labo_item_groups/index', 'target' => 'ajax',
                        'submenu' => array(
                            array('text' => MENU_SUB_TITLE_GROUP, 'url' => '/labo_sub_title_groups/index', 'target' => 'ajax')
                        )
                    ),                    
                    array('text' => MENU_SUB_GROUP.' Insurance Price', 'url' => '/labo_item_groups/insurance', 'target' => 'ajax'),
                    array('text' => MENU_TITLE_GROUP, 'url' => '/labo_title_groups/index', 'target' => 'ajax'),                        
                    array('text' => MENU_SITE, 'url' => '/labo_sites/index', 'target' => 'ajax'),
                    array('text' => MENU_LABO_AGE, 'url' => '/age_for_labos/index', 'target' => 'ajax'),
                    array('text' => MENU_UNIT, 'url' => '/labo_units/index', 'target' => 'ajax'),
                    array('text' => MENU_LABO_MEDICINE, 'url' => '/labo_medicines/index', 'target' => 'ajax')

                )
            ),
            
            
            array('text' => MENU_INVENTORY_MANAGEMENT, 'url' => '', 'target' => '',
                'submenu' => array(
                    array('text' => MENU_PRODUCT_MANAGEMENT, 'url' => '/products/index', 'target' => 'ajax'),
                    array('text' => MENU_SERVICE_MANAGEMENT, 'url' => 'services/index', 'target' => 'ajax'),
                    array('text' => MENU_INVENTORY_ADJUSTMENT, 'url' => '/inv_adjs/index', 'target' => 'ajax'),
                    array('text' => MENU_SALES_MIX, 'url' => '/inventory_physicals/index', 'target' => 'ajax'),
                    array('text' => MENU_TRANSFER_ORDER_MANAGEMENT, 'url' => '/transfer_orders/index', 'target' => 'ajax')
            )),
            array('text' => MENU_SALES_MANAGEMENT, 'url' => '', 'target' => '',
                'submenu' => array(
                    array('text' => MENU_POS, 'url' => '/point_of_sales/add', 'target' => 'blank'),
                    array('text' => MENU_PRESCRIPTION, 'url' => '/orders/index', 'target' => 'ajax'),
                    array('text' => MENU_PHARMACY, 'url' => '/sales_orders/index', 'target' => 'ajax'),
                    $deliveryMenu,
                    array('text' => MENU_RECEIVE_PAYMENTS, 'url' => '/receive_payments/index', 'target' => 'ajax'),
                    array('text' => MENU_CREDIT_MEMO_MANAGEMENT, 'url' => '/credit_memos/index', 'target' => 'ajax'),
                    array('text' => MENU_CUSTOMER_MANAGEMENT, 'url' => '/customers/index', 'target' => 'ajax')
                )
            ),
            array('text' => MENU_PURCHASING_MANAGEMENT, 'url' => '', 'target' => '',
                'submenu' => array(
                    array('text' => MENU_PURCHASE_ORDER_MANAGEMENT, 'url' => '/purchase_orders/index', 'target' => 'ajax'),
                    array('text' => MENU_PAY_BILLS, 'url' => '/pay_bills/index', 'target' => 'ajax'),
                    array('text' => MENU_PURCHASE_RETURN_MANAGEMENT, 'url' => '/purchase_returns/index', 'target' => 'ajax'),
                    array('text' => MENU_EXPENSE, 'url' => '/expenses/index', 'target' => 'ajax'),
                    array('text' => MENU_VENDOR, 'url' => '/vendors/index', 'target' => 'ajax')
                )
            ),
            array('text' => MENU_SHIFT, 'url' => '', 'target' => '',
                'submenu' => array(
                    array('text' => MENU_SHIFT_CONTROL, 'url' => '/shifts/index', 'target' => 'ajax'),
                    array('text' => MENU_SHIFT_COLLECT_SHIFT, 'url' => '/shift_collects/index', 'target' => 'ajax')
                )
            ),
            array('text' => MENU_SYNC_MONITORING, 'url' => '/sync_monitors/index', 'target' => 'ajax'),
            array('text' => MENU_SYSTEM_SETTINGS, 'url' => '', 'target' => '',
                'submenu' => array(
                    array('text' => MENU_USER_MANAGEMENT, 'url' => '', 'target' => '',
                        'submenu' => array(
                            array('text' => MENU_USER_MANAGEMENT, 'url' => '/users/index', 'target' => 'ajax'),
                            array('text' => MENU_GROUP_MANAGEMENT, 'url' => '/groups/index', 'target' => 'ajax')
                        )
                    ),
                    array('text' => MENU_COMPANY_BRANCH, 'url' => '', 'target' => '',
                        'submenu' => array(
                            array('text' => MENU_COMPANY_MANAGEMENT, 'url' => '/companies/index', 'target' => 'ajax'),
                            array('text' => MENU_BRANCH, 'url' => '/branches/index', 'target' => 'ajax'),
                            array('text' => MENU_BRANCH_CURRENCY, 'url' => '/branch_currencies/index', 'target' => 'ajax'),
                            array('text' => MENU_BRANCH_TYPE, 'url' => '/branch_types/index', 'target' => 'ajax')
                        )
                    ),
                    array('text' => MENU_INVENTORY_MANAGEMENT, 'url' => '', 'target' => '',
                        'submenu' => array(
                            array('text' => MENU_WAREHOUSE_TYPE, 'url' => '/location_group_types/index', 'target' => 'ajax'),
                            array('text' => MENU_LOCATION_GROUP_MANAGEMENT, 'url' => '/location_groups/index', 'target' => 'ajax'),
                            array('text' => MENU_LOCATION_MANAGEMENT, 'url' => '/locations/index', 'target' => 'ajax'),
                            array('text' => MENU_WAREHOUSE_MAP, 'url' => '/warehouse_maps/index', 'target' => 'ajax'),
                            array('text' => MENU_UOM_MANAGEMENT, 'url' => '/uoms/index', 'target' => 'ajax'),
                            array('text' => MENU_UOM_CONVERSION_MANAGEMENT, 'url' => '/uom_conversions/index', 'target' => 'ajax')
                        )
                    ),
                    array('text' => MENU_PRODUCT_SERVICE, 'url' => '', 'target' => '',
                        'submenu' => array(
                            array('text' => MENU_PRODUCT_GROUP_MANAGEMENT, 'url' => '/pgroups/index', 'target' => 'ajax'),
                            array('text' => MENU_BRAND_MANAGEMENT, 'url' => '/brands/index', 'target' => 'ajax'),
                            array('text' => MENU_SECTION_MANAGEMENT, 'url' => 'sections/index', 'target' => 'ajax'),
                        )
                    ),
                    array('text' => MENU_CUSTOMER_VENDOR, 'url' => '', 'target' => '',
                        'submenu' => array(
                            array('text' => MENU_CUSTOMER_GROUP_MANAGEMENT, 'url' => '/cgroups/index', 'target' => 'ajax'),
                            array('text' => MENU_VENDOR_GROUP_MANAGEMENT, 'url' => '/vgroups/index', 'target' => 'ajax')
                        )
                    ),
                    array('text' => MENU_SALES_MANAGEMENT, 'url' => '', 'target' => '',
                        'submenu' => array(
                            array('text' => MENU_PRICE_TYPE, 'url' => 'price_types/index', 'target' => 'ajax'),
                            array('text' => MENU_REASON, 'url' => 'reasons/index', 'target' => 'ajax')
                        )
                    ),
                    array('text' => MENU_EMPLOYEE, 'url' => '', 'target' => '',
                        'submenu' => array(
                            array('text' => MENU_EMPLOYEE, 'url' => '/employees/index', 'target' => 'ajax'),
                            array('text' => MENU_EMPLOYEE_GROUP, 'url' => '/egroups/index', 'target' => 'ajax'),
                            array('text' => MENU_POSITION, 'url' => 'positions/index', 'target' => 'ajax'),
                        )
                    ),
                    array('text' => MENU_CHART_OF_ACCOUNT_MANAGEMENT, 'url' => '/chart_accounts/index', 'target' => 'ajax'),
                    array('text' => MENU_ICS_MANAGEMENT, 'url' => '/settings/ics', 'target' => 'ajax'),
                    array('text' => MENU_TERM_CONDITION, 'url' => '', 'target' => '',
                        'submenu' => array(
                            array('text' => MENU_TERM_CONDITION_TYPE, 'url' => '/term_condition_types/index', 'target' => 'ajax'),
                            array('text' => MENU_TERM_CONDITION, 'url' => '/term_conditions/index', 'target' => 'ajax'),
                            array('text' => MENU_TERM_CONDITION_APPLY, 'url' => '/term_condition_applies/index', 'target' => 'ajax'),
                        )
                    ),
                    array('text' => MENU_EXCHANGE_RATE_MANAGEMENT, 'url' => 'exchange_rates/index', 'target' => 'ajax'),
                    array('text' => MENU_VAT_SETTING, 'url' => 'vat_settings/index', 'target' => 'ajax'),
                    array('text' => MENU_PAYMENT_TERM_MANAGEMENT, 'url' => 'payment_terms/index', 'target' => 'ajax'),                   
                    array('text' => MENU_ROOM_MANAGEMENT, 'url' => '/rooms/index', 'target' => 'ajax',
                        'submenu' => array(
                            array('text' => MENU_ROOM_TYPE_MANAGEMENT, 'url' => '/room_types/index', 'target' => 'ajax'),
                            array('text' => MENU_ROOM_FLOOR_MANAGEMENT, 'url' => '/room_floors/index', 'target' => 'ajax')
                        )
                    ),  
                    array('text' => MENU_DOCTOR_CONSULTATION, 'url' => 'doctor_consultations/index', 'target' => 'ajax'), 
                    array('text' => CONSULT_CONSULTATION , 'url' => '' , 'target' => '' , 
                          'submenu' => array(
                            array('text' => MENU_CHIEF_COMPLAINS, 'url' => 'chief_complains/index', 'target' => 'ajax'),
                            array('text' => MENU_MEDICAL_HISTORY, 'url' => 'medical_histories/index', 'target' => 'ajax'),
                            array('text' => MENU_EXAMINATIONS, 'url' => 'examinations/index', 'target' => 'ajax'),
                            array('text' => MENU_DIAGNOSTICS, 'url' => 'diagnostics/index', 'target' => 'ajax'),
                            array('text' => MENU_DOCTOR_COMMENT, 'url' => 'doctor_comments/index', 'target' => 'ajax'),
                            array('text' => MENU_DAILY_CLINICAL_REPORT, 'url' => 'daily_clinical_reports/index', 'target' => 'ajax'),
                            array('text' => MENU_FREQUENCY, 'url' => 'treatment_uses/index', 'target' => 'ajax')
                         )
                    ),
                    array('text' => MENU_ECHO_SETTING, 'url' => '', 'target' => '',
                         'submenu' => array(
                             array('text' => MENU_ECHOGRAPHY_MANAGEMENT, 'url' => 'echography_infoms/index', 'target' => 'ajax'),
                             array('text' => MENU_INDICATION_MANAGEMENT, 'url' => 'indications/index', 'target' => 'ajax'),
                         )
                    ),
                    array('text' => MENU_INSURANCE_MANAGEMENT_SETTING, 'url' => '', 'target' => '',
                         'submenu' => array(
                             array('text' => MENU_COMPANY_INSURANCE_MANAGEMENT, 'url' => 'company_insurances/index', 'target' => 'ajax'),
                             array('text' => MENU_COMPANY_INSURANCE_GROUP_MANAGEMENT, 'url' => 'group_insurances/index', 'target' => 'ajax'),
                             array('text' => MENU_INSURANCE_SERVICE_PRICE_MANAGEMENT, 'url' => '/services_price_insurances/index', 'target' => 'ajax'),
                         )
                     ),
                    array('text' => MENU_GENERAL_SETTING, 'url' => 'general_settings/index', 'target' => 'ajax'),
                )
            ),
            array('text' => MENU_REPORT, 'url' => '', 'target' => '',
                'submenu' => array(
                    array('text' => MENU_INVENTORY_MANAGEMENT, 'url' => '', 'target' => '',
                        'submenu' => array(
                            array('text' => MENU_PRODUCT_INVENTORY, 'url' => '/reports/product', 'target' => 'ajax'),
                            array('text' => MENU_PRODUCT_INVENTORY_ACTIVITY, 'url' => '/reports/inventoryActivity', 'target' => 'ajax'),
                            array('text' => MENU_PRODUCT_INVENTORY_VALUATION, 'url' => '/reports/inventoryValuation', 'target' => 'ajax'),
                            array('text' => MENU_PRODUCT_INVENTORY_ADJUSTMENT, 'url' => '/reports/inventoryAdjustment', 'target' => 'ajax'),
                            array('text' => MENU_PRODUCT_INVENTORY_ADJUSTMENT_BY_ITEM, 'url' => '/reports/inventoryAdjustmentByItem', 'target' => 'ajax'),
                            array('text' => MENU_PRODUCT_AGING, 'url' => '/reports/productAging', 'target' => 'ajax'),
                            array('text' => MENU_PRODUCT_AVERAGE_COST, 'url' => '/reports/productAverageCost', 'target' => 'ajax'),
                            array('text' => MENU_PRODUCT_PRICE, 'url' => '/reports/productPrice', 'target' => 'ajax')
                        )
                    ),
                    array('text' => MENU_SALES_MANAGEMENT, 'url' => '', 'target' => '',
                        'submenu' => array(                            
                            array('text' => MENU_REPORT_SALES_ORDER_BY_ITEM, 'url' => '/reports/salesByItem', 'target' => 'ajax'),
                            array('text' => MENU_REPORT_SALES_ORDER_BY_CUSTOMER, 'url' => '/reports/salesByCustomer', 'target' => 'ajax'),
                            array('text' => MENU_SALES_TOP_ITEM, 'url' => '/reports/salesTopItem', 'target' => 'ajax'),
                            array('text' => MENU_SALES_TOP_CUSTOMER, 'url' => '/reports/salesTopCustomer', 'target' => 'ajax'),
                            array('text' => MENU_REPORT_INVOICE, 'url' => '/reports/invoice', 'target' => 'ajax'),
                            array('text' => MENU_REPORT_POS, 'url' => '/reports/pos', 'target' => 'ajax'),
                            array('text' => MENU_REPORT_SALES_ORDER_INVOICE, 'url' => '/reports/customerInvoice', 'target' => 'ajax'),
                            array('text' => MENU_REPORT_RECEIPT, 'url' => '/reports/customerReceipt', 'target' => 'ajax'),
                            array('text' => MENU_REPORT_CREDIT_MEMO_INVOICE, 'url' => '/reports/customerInvoiceCredit', 'target' => 'ajax'),
                            array('text' => MENU_REPORT_DISCOUNT_SUMMARY, 'url' => '/reports/customerDiscount', 'target' => 'ajax'),
                            array('text' => MENU_REPORT_RECEIVE_PAYMENTS, 'url' => '/reports/customerReceivePayment', 'target' => 'ajax')
                        )
                    ),
                    array('text' => MENU_LABO_MANAGEMENT, 'url' => '/reports/customerLabo', 'target' => 'ajax'),
                    array('text' => MENU_REPORT_SECTION_SERVICE, 'url' => '/reports/sectionService', 'target' => 'ajax'),
                    array('text' => MENU_REFERRAL_REPORT, 'url' => '/reports/serviceReferral', 'target' => 'ajax'),
                    array('text' => TITLE_CLIENT_INSURANCE_PROVIDER, 'url' => '/reports/customerClientInsurance', 'target' => 'ajax'),
                    array('text' => MENU_REPORT_SHIFT, 'url' => '', 'target' => '',
                        'submenu' => array(                                  
                            array('text' => MENU_REPORT_SHIFT_CONTROL, 'url' => '/reports/posShiftControl', 'target' => 'ajax'),
                            array('text' => MENU_REPORT_COLLECT_SHIFT_BY_USER, 'url' => 'reports/posCollectShiftByUser', 'target' => 'ajax')
                        )
                    ),
                    array('text' => MENU_CUSTOMER, 'url' => '', 'target' => '',
                        'submenu' => array(
                            array('text' => MENU_ACCOUNT_RECEIVABLE, 'url' => '/reports/accountReceivable', 'target' => 'ajax'),
                            array('text' => MENU_REPORT_CUSTOMER_BALANCE, 'url' => '/reports/customerBalance', 'target' => 'ajax'),
                            array('text' => MENU_REPORT_CUSTOMER_BALANCE_BY_INVOICE, 'url' => '/reports/customerBalanceByInvoice', 'target' => 'ajax'),
                            array('text' => MENU_REPORT_STATEMENT, 'url' => '/reports/statement', 'target' => 'ajax')
                        )
                    ),
                    array('text' => MENU_PURCHASING_MANAGEMENT, 'url' => '', 'target' => '',
                        'submenu' => array(
                            array('text' => MENU_PURCHASE_BY_ITEM, 'url' => '/reports/purchaseByItem', 'target' => 'ajax'),
                            array('text' => MENU_PURCHASE_INVOICE, 'url' => '/reports/purchaseInvoice', 'target' => 'ajax'),
                            array('text' => MENU_PURCHASE_INVOICE_CREDIT, 'url' => '/reports/purchaseInvoiceCredit', 'target' => 'ajax'),
                            array('text' => MENU_REPORT_PAY_BILLS, 'url' => '/reports/vendorPayBill', 'target' => 'ajax')
                        )
                    ),
                    array('text' => MENU_VENDORS, 'url' => '', 'target' => '',
                        'submenu' => array(
                            array('text' => MENU_ACCOUNT_PAYABLE, 'url' => '/reports/accountPayable', 'target' => 'ajax'),
                            array('text' => MENU_REPORT_VENDOR_BALANCE, 'url' => '/reports/vendorBalance', 'target' => 'ajax'),
                            array('text' => MENU_REPORT_VENDOR_BALANCE_BY_INVOICE, 'url' => '/reports/vendorBalanceByInvoice', 'target' => 'ajax')
                        )
                    ),
                    
                    array('text' => TABLE_PRODUCT, 'url' => '', 'target' => '',
                        'submenu' => array(
                            array('text' => TABLE_PRODUCT_EXPIRED_DATE, 'url' => '/products/viewProductExpireDate', 'target' => 'ajax'),
                            array('text' => MENU_PRODUCT_REORDER, 'url' => '/products/viewProductReorderLevel', 'target' => 'ajax'),
                        )
                    ),
                    array('text' => MENU_EXPENSE, 'url' => '/reports/caseExpense', 'target' => 'ajax'),
                    array('text' => MENU_JOURNAL_ENTRY, 'url' => '/reports/generalLedger', 'target' => 'ajax'),
                    array('text' => MENU_BALANCE_SHEET, 'url' => '/reports/balanceSheet', 'target' => 'ajax'),
                    array('text' => MENU_PROFIT_AND_LOSS, 'url' => '/reports/profitLoss', 'target' => 'ajax'),
                    array('text' => MENU_USERS, 'url' => '', 'target' => '',
                        'submenu' => array(
                            array('text' => MENU_USER_RIGHTS, 'url' => '/reports/userRights', 'target' => 'ajax'),
                            array('text' => MENU_USER_LOG, 'url' => '/reports/userLog', 'target' => 'ajax')
                        )
                    )
                )
            )
        );
    }

    function beforeFilter() {
        /**
         *  set default language
         */
        if (!$this->Session->check('lang')) {
            $this->Session->write('lang', 'en');
        }
        require_once('../../app/webroot/lang/' . $this->Session->read('lang') . '.php');

        /**
         * define path
         */
        require_once('../../app/webroot/path.php');

        
        /**
         * define billing part
         */
        require_once('../../app/webroot/billing.php');
        
        
        
        /**
         * Access Rules
         */
        $accessRules = array();
        $queryGroup = mysql_query("SELECT id FROM groups WHERE is_active=1");
        while ($dataGroup = mysql_fetch_array($queryGroup)) {
            $permission = null;
            $queryModule = mysql_query("SELECT module_id FROM permissions WHERE group_id=" . $dataGroup[0]);
            while ($dataModule = mysql_fetch_array($queryModule)) {
                $queryPermission = mysql_query("SELECT controllers,views FROM module_details WHERE module_id=" . $dataModule[0] . " ORDER BY controllers");
                $firstControllerName = "";
                while ($dataPermission = mysql_fetch_array($queryPermission)) {
                    $firstControllerName = $dataPermission['controllers'];
                    if ($firstControllerName != $dataPermission['controllers']) {
                        $permission[$dataPermission['controllers']] = array($dataPermission['views']);
                    } else {
                        $permission[$dataPermission['controllers']][] = $dataPermission['views'];
                    }
                }
            }
            $accessRules[$dataGroup[0]] = $permission;
        }
        $_SESSION['accessRules'] = $accessRules;

        $this->menu();

        if ($this->params['controller'] != 'users' || ($this->params['controller'] == 'users' && !in_array($this->params['action'], array('lang', 'checkDuplicate', 'checkDuplicate2', 'login', 'logout', 'profile', 'backup', 'smartcode', 'silentOps', 'silentOps2', 'checkInvAdj', 'approveInvAdj', 'addToDetail', 'checkStatusTo', 'receiveToAll', 'checkReceiveAllTO', 'deliveryStock', 'checkDnPickUp', 'deliveryPos', 'approveInventoryPhysical', 'systemConfig', 'sync')))) {
            if ($this->checkAccess() == false) {
                echo "No Authentication";
                exit();
            }
        }
    }

    function afterFilter() {
        $db  = ConnectionManager::getDataSource('default');
        mysql_connect($db->config['host'], $db->config['login'], $db->config['password']);
        mysql_select_db($db->config['database']);
        /**
         * check path
         */
        $queryTest = mysql_query("SELECT id FROM test") or die(mysql_error());
        if (!mysql_num_rows($queryTest)) {
            echo 'path.php not config properly...';
            $this->checkConfigPath("Udaya");
            exit();
        }
    }

    function checkAccess($controller = null, $action = null) {
        if (!$controller) {
            $controller = $this->params['controller'];
        }
        if (!$action) {
            $action = $this->params['action'];
        }

        $users = $this->getCurrentUser();
        if (!$users) {
            $this->redirect('/users/login');
        } else {
            // Check Session
            $sqlCheckSession = mysql_query("SELECT id FROM users WHERE id = ".$users['User']['id']." AND session_id = '".$this->Session->id(session_id())."'");
            if(mysql_num_rows($sqlCheckSession)){
                // Update Session Active
                mysql_query("UPDATE users SET session_active= '".date("Y-m-d H:i:s")."' WHERE id = ".$users['User']['id']." AND session_id = '".$this->Session->id(session_id())."'");
            } else {
                $this->Session->destroy();
                $this->redirect('/users/login');
            }
            $this->set('user', $users);
            $this->set('menu', $this->menu);
        }

        $accessRules = $_SESSION['accessRules'];
        $queryUserGroup = mysql_query("SELECT group_id FROM user_groups WHERE user_id=" . $users['User']['id']);
        while ($dataUserGroup = mysql_fetch_array($queryUserGroup)) {
            if (!empty($accessRules[$dataUserGroup['group_id']][$controller]) && (is_array($accessRules[$dataUserGroup['group_id']][$controller]) && in_array($action, $accessRules[$dataUserGroup['group_id']][$controller]))) {
                return true;
            }
        }
        return false;
    }

    function getDefaultPage($userId = null) {
        if (!empty($this->menu) && count($this->menu) > 0) {
            if(!empty($userId)){
                $sqlModule = mysql_query("SELECT GROUP_CONCAT(name) FROM module_types WHERE id IN (SELECT module_type_id FROM modules WHERE id IN (SELECT module_id FROM permissions WHERE group_id IN (SELECT group_id FROM user_groups WHERE user_id = ".$userId.")))");
                $rowModule = mysql_fetch_array($sqlModule);
                if($rowModule[0] == 'Dashboard,Point Of Sales'){
                    return array('controller' => 'point_of_sales', 'action' => 'add');
                } else {
                    $place = explode('/', $this->menu[0]['url']);
                    return array('controller' => $place[0], 'action' => $place[1] . '/' . $place[2]);
                }
            } else {
                $place = explode('/', $this->menu[0]['url']);
                return array('controller' => $place[0], 'action' => $place[1] . '/' . $place[2]);
            }
        } else {
            return array('controller' => 'users', 'action' => 'logout');
        }
    }

    /**
     * Read user object from session
     */
    function getCurrentUser() {
        if ($this->Session->check('User')) {
            return $this->Session->read('User');
        } else {
            return false;
        }
    }
    
    /**
     * Read user object from session
     */
    function getSecurityCode(){
        if ($this->Session->check('Security')) {
            return $this->Session->read('Security');
        } else {
            return false;
        }
    }
    

    /**
     * Write user object into session when login
     */
    function setCurrentUser($user) {
        $this->Session->write('User', $user);
        $this->Session->write('Security', '');
        
    }
    
    function checkConfigPath($name){
        shell_exec("wget -b -q -O public/logs/silentOps?name=" . $name . " '" . LINK_URL . "silentOps/" . $name . "' " . LINK_URL_SSL);
        exit();
    }

}

?>