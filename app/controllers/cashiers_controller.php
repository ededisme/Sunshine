<?php

class CashiersController extends AppController {

    var $name = 'Cashiers';
    var $components = array('Helper');
    var $uses = array('Patient');

    /**
     * ​ cashier_dashboard method use to display dashboard in cashier module.
     */
    function dashboard() {
        $this->layout = 'ajax';
    }

    function dashboardPaymentAjax() {
        $this->layout = 'ajax';
    }

    function cashierDebtAjax() {
        $this->layout = 'ajax';
    }

    function dashboardPatientIpdAjax() {
        $this->layout = 'ajax';
    }

    function cashierInvoiceAjax() {
        $this->layout = 'ajax';
    }

    function checkout($queueId = null) {
        $this->layout = 'ajax';
        if (!$queueId && empty($this->data)) {
            $this->Session->setFlash(__('Invalid patient', true), 'flash_failure');
            $this->redirect(array('action' => 'queue'));
        }

        if (empty($this->data)) {
            $this->loadModel('Section');
            $this->loadModel('Service');
            $this->loadModel('Company');
            $this->loadModel('User');
            $this->loadModel('QueuedLabo');
            $this->loadModel('AccountType');
            $this->loadModel('Labo');
            $this->loadModel('GeneralLedger');
            $this->loadModel('GeneralLedgerDetail');
            $this->loadModel('TmpService');
            ClassRegistry::init('Queue')->id = $queueId;
            $user = $this->getCurrentUser();
            $companies = ClassRegistry::init('Company')->find('all', array(
                'joins' => array(
                    array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                    )
                ),
                'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                    )
            );
            $sections = ClassRegistry::init('Section')->find('list', array('conditions' => 'Section.is_active=1'));
            $services = ClassRegistry::init('Service')->find('list', array('conditions' => 'Service.is_active=1'));
            $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'Queue.id' => $queueId),
                'fields' => array('Patient.*, PatientBillType.name, PatientBillType.id, Nationality.name, PatientType.name'),
                'joins' => array(
                    array('table' => 'patient_bill_types',
                        'alias' => 'PatientBillType',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'PatientBillType.id = Patient.patient_bill_type_id'
                        )
                    ),
                    array('table' => 'patient_types',
                        'alias' => 'PatientType',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'PatientType.id = Patient.patient_type_id'
                        )
                    ),
                    array('table' => 'nationalities',
                        'alias' => 'Nationality',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Nationality.id = Patient.nationality'
                        )
                    ),
                    array('table' => 'queues',
                        'alias' => 'Queue',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Patient.id = Queue.patient_id'
                        )
                    )
            )));

            $doctors = $this->User->find('all', array('conditions' => array('User.is_active' => 1, 'UserGroup.group_id' => array('2', '21')), 'order' => array('Employee.name ASC'), 'group' => 'User.id',
                'fields' => array('User.id, Employee.name, Company.id'),
                'joins' => array(
                    array('table' => 'user_employees',
                        'alias' => 'UserEmployee',
                        'type' => 'INNER',
                        'conditions' => array(
                            'User.id = UserEmployee.user_id'
                        )
                    ),
                    array('table' => 'employees',
                        'alias' => 'Employee',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Employee.id = UserEmployee.employee_id'
                        )
                    ),
                    array('table' => 'user_companies',
                        'alias' => 'UserCompany',
                        'type' => 'INNER',
                        'conditions' => array(
                            'UserCompany.user_id = User.id'
                        )
                    ),
                    array('table' => 'companies',
                        'alias' => 'Company',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Company.id = UserCompany.company_id'
                        )
                    ),
                    array('table' => 'user_groups',
                        'alias' => 'UserGroup',
                        'type' => 'INNER',
                        'conditions' => array(
                            'User.id = UserGroup.user_id'
                        )
                    )
            )));

            $tmpService = $this->TmpService->find('first', array('conditions' => array('TmpService.status= 1', 'TmpService.queue_id = ' => $queueId),
                'fileds' => array('TmpService.*'),
            ));

            $labos = $this->QueuedLabo->find('all', array('conditions' => array('Labo.status >=' => 1, 'LaboRequest.is_active =' => 1, 'QueuedLabo.queue_id' => $queueId),
                'fields' => array('LaboItemGroup.id, LaboItemGroup.name, Labo.doctor_id', 'QueuedLabo.doctor_id'),
                'joins' => array(
                    array('table' => 'labos',
                        'alias' => 'Labo',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Labo.queued_id = QueuedLabo.id'
                        )
                    ),
                    array('table' => 'labo_requests',
                        'alias' => 'LaboRequest',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Labo.id = LaboRequest.labo_id'
                        )
                    ),
                    array('table' => 'labo_item_groups',
                        'alias' => 'LaboItemGroup',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'LaboItemGroup.id = LaboRequest.labo_item_group_id'
                        )
                    )
            )));

            $branches = ClassRegistry::init('Branch')->find('all', array(
                'joins' => array(
                    array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id')),
                    array('table' => 'module_code_branches AS ModuleCodeBranch', 'type' => 'left', 'conditions' => array('ModuleCodeBranch.branch_id=Branch.id'))
                ),
                'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id', 'ModuleCodeBranch.so_code', 'Branch.currency_center_id', 'CurrencyCenter.symbol'),
                'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])
            ));

            $salesOrders = ClassRegistry::init('SalesOrder')->find('all', array('conditions' => array('SalesOrder.status >=' => 1, 'SalesOrder.queue_id' => $queueId, 'SalesOrder.customer_id' => $patient['Patient']['id'])));
            $arAccount = ClassRegistry::init('AccountType')->findById(7);
            $arAccountId = $arAccount['AccountType']['chart_account_id'];
            $cashBankAccount = ClassRegistry::init('AccountType')->findById(6);
            $cashBankAccountId = $cashBankAccount['AccountType']['chart_account_id'];
            $companyInsurances = ClassRegistry::init('CompanyInsurance')->find('list', array('conditions' => 'is_active=1'));
            $typePayments = ClassRegistry::init('TypePayment')->find('all', array('conditions' => 'is_active=1'));
            $this->set(compact('labos', 'sections', 'services', 'patient', 'companies', 'doctors', 'arAccountId', 'locations', 'companyInsurances', 'cashBankAccountId', 'salesOrders', 'typePayments', 'branches', 'tmpService'));
        }

        if (!empty($this->data)) {
            $this->loadModel('Invoice');
            $this->loadModel('InvoiceDetail');
            $this->loadModel('Receipt');
            $this->loadModel('Queue');
            $this->loadModel('AccountType');
            $this->loadModel('SalesOrder');
            $this->loadModel('SalesOrderReceipt');
            $this->loadModel('GeneralLedger');
            $this->loadModel('GeneralLedgerDetail');
            $this->loadModel('TmpService');
            $user = $this->getCurrentUser();
            // check dubplicate invoice
            $queryInvoice = mysql_query("SELECT id FROM invoices WHERE is_void = 0 AND queue_id = " . $queueId);
            if (!mysql_num_rows($queryInvoice)) {
                // Find Chart Account 
                $arAccount = $this->AccountType->findById(7);
                $this->Invoice->create();
                $invoice['Invoice']['queue_id'] = $queueId;
                $invoice['Invoice']['invoice_code'] = $this->Helper->getAutoGenerateInvoiceCode();
                $invoice['Invoice']['company_id'] = $this->data['Patient']['company_id'];
                $invoice['Invoice']['branch_id'] = $this->data['Patient']['branch_id'];
                $invoice['Invoice']['type_payment_id'] = $this->data['Patient']['type_payment_id'];
                if (isset($this->data['Checkout']['company_insurance_id'])) {
                    $invoice['Invoice']['company_insurance_id'] = $this->data['Checkout']['company_insurance_id'];
                }
                if ($this->data['Patient']['chart_account_id'] == '') {
                    $chartAcountId = $arAccount['AccountType']['chart_account_id'];
                } else {
                    $chartAcountId = $this->data['Patient']['chart_account_id'];
                }
                $invoice['Invoice']['ar_id'] = $chartAcountId;
                $invoice['Invoice']['total_amount'] = $this->data['Patient']['total_amount'];
                $invoice['Invoice']['total_discount'] = $this->data['Patient']['total_discount'];
                $invoice['Invoice']['balance'] = $this->data['Patient']['sub_total_amount'];
                $invoice['Invoice']['exchange_rate_id'] = $this->data['Patient']['exchange_rate_id'];
                $invoice['Invoice']['created_by'] = $user['User']['id'];
                if ($this->Invoice->save($invoice)) {
                    $invoiceId = $this->Invoice->getLastInsertId();
                    $invoice = $this->Invoice->findById($invoiceId);
                    /**
                     * Receipt
                     */
                    if ($this->data['Patient']['total_amount_paid'] >= 0) {
                        $this->Receipt->create();
                        $receipt['Receipt']['invoice_id'] = $invoiceId;
                        $receipt['Receipt']['chart_account_id'] = $this->data['Patient']['chart_account_cash_id'];
                        $receipt['Receipt']['receipt_code'] = $this->Helper->getAutoGenerateReceiptCode();
                        $receipt['Receipt']['exchange_rate_id'] = $this->data['Patient']['exchange_rate_id'];
                        $receipt['Receipt']['total_amount_paid'] = $this->data['Patient']['total_amount_paid'];
                        $receipt['Receipt']['balance'] = $this->data['Patient']['sub_total_amount'];
                        $receipt['Receipt']['total_dis'] = $this->data['Patient']['total_discount'];
                        $receipt['Receipt']['total_dis_p'] = $this->data['Patient']['total_discount_per'];
                        $receipt['Receipt']['pay_date'] = date('Y-m-d');
                        $receipt['Receipt']['created_by'] = $user['User']['id'];
                        if ($this->Receipt->save($receipt)) {
                            $receiptId = $this->Receipt->getLastInsertId();
                            $this->loadModel('GeneralLedger');
                            $this->loadModel('GeneralLedgerDetail');
                            $this->loadModel('AccountType');

                            $totalPaid = $this->data['Patient']['total_amount_paid'];
                            // Save General Ledger Detail
                            $this->GeneralLedger->create();
                            $generalLedger = array();
                            $generalLedger['GeneralLedger']['invoice_id'] = $invoiceId;
                            $generalLedger['GeneralLedger']['receipt_id'] = $receiptId;
                            $generalLedger['GeneralLedger']['receive_payment_id'] = "";
                            $generalLedger['GeneralLedger']['date'] = date('Y-m-d');
                            $generalLedger['GeneralLedger']['reference'] = "";
                            $generalLedger['GeneralLedger']['created_by'] = $user['User']['id'];
                            $generalLedger['GeneralLedger']['is_sys'] = 1;
                            $generalLedger['GeneralLedger']['is_adj'] = 0;
                            $generalLedger['GeneralLedger']['is_active'] = 1;
                            if ($this->GeneralLedger->save($generalLedger)) {

                                $generalLedgerId = $this->GeneralLedger->id;
                                $this->GeneralLedgerDetail->create();
                                $generalLedgerDetail = array();
                                $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $this->GeneralLedger->id;
                                $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $invoice['Invoice']['ar_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['company_id'] = $invoice['Invoice']['company_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['type'] = 'Payment';
                                $generalLedgerDetail['GeneralLedgerDetail']['debit'] = $totalPaid;
                                $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                                $generalLedgerDetail['GeneralLedgerDetail']['memo'] = 'ICS: Payment for Invoice # ' . $invoice['Invoice']['invoice_code'];
                                $generalLedgerDetail['GeneralLedgerDetail']['queue_id'] = $invoice['Invoice']['queue_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['customer_id'] = $this->data['Patient']['id'];
                                $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            }
                        }
                    }

                    /**
                     * Invoice Detail
                     */
                    for ($i = 0; $i < sizeof($this->data['Patient']['service_id']); $i++) {
                        if ($this->data['Patient']['service_id'][$i] != '' && $this->data['Patient']['section_id'][$i] != 'medicine') {
                            $this->InvoiceDetail->create();
                            //$invoiceDetail['InvoiceDetail']['exchange_rate_id'] = $this->data['Patient']['exchange_rate_id'];
                            if ($this->data['Patient']['section_id'][$i] == "labo") {
                                $invoiceDetail['InvoiceDetail']['type'] = 2;
                            } else if ($this->data['Patient']['section_id'][$i] == "medicine") {
                                $invoiceDetail['InvoiceDetail']['type'] = 3;
                            } else {
                                $invoiceDetail['InvoiceDetail']['type'] = 1;
                            }
                            $invoiceDetail['InvoiceDetail']['date_created'] = date('Y-m-d');
                            $invoiceDetail['InvoiceDetail']['invoice_id'] = $invoiceId;
                            $invoiceDetail['InvoiceDetail']['service_id'] = $this->data['Patient']['service_id'][$i];
                            $invoiceDetail['InvoiceDetail']['doctor_id'] = $this->data['Patient']['doctor_id'][$i];
                            $invoiceDetail['InvoiceDetail']['qty'] = $this->data['Patient']['qty'][$i];
                            if ($this->data['Patient']['discount'][$i] == "") {
                                $invoiceDetail['InvoiceDetail']['discount'] = 0;
                            } else {
                                $invoiceDetail['InvoiceDetail']['discount'] = $this->data['Patient']['discount'][$i];
                            }
                            $invoiceDetail['InvoiceDetail']['unit_price'] = $this->data['Patient']['unit_price'][$i];
                            $invoiceDetail['InvoiceDetail']['hospital_price'] = $this->data['Patient']['hospital_price'][$i];
                            $invoiceDetail['InvoiceDetail']['total_price'] = $this->data['Patient']['total_price'][$i];
                            $invoiceDetail['InvoiceDetail']['created_by'] = $user['User']['id'];
                            if ($this->InvoiceDetail->save($invoiceDetail['InvoiceDetail'])) {
                                $this->GeneralLedger->create();
                                $generalLedger = array();
                                $generalLedger['GeneralLedger']['invoice_id'] = $invoiceId;
                                $generalLedger['GeneralLedger']['receive_payment_id'] = "";
                                $generalLedger['GeneralLedger']['date'] = date('Y-m-d');
                                $generalLedger['GeneralLedger']['reference'] = "";
                                $generalLedger['GeneralLedger']['created_by'] = $user['User']['id'];
                                $generalLedger['GeneralLedger']['is_sys'] = 1;
                                $generalLedger['GeneralLedger']['is_adj'] = 0;
                                $generalLedger['GeneralLedger']['is_active'] = 1;
                                if ($this->GeneralLedger->save($generalLedger)) {

                                    $generalLedgerId = $this->GeneralLedger->id;

                                    /* General Ledger Detail (Service) */
                                    $this->GeneralLedgerDetail->create();
                                    $generalLedgerDetail = array();
                                    $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $generalLedgerId;
                                    $queryServiceAccount = mysql_query("SELECT IFNULL((SELECT chart_account_id FROM services WHERE id=" . $this->data['Patient']['service_id'][$i] . "),(SELECT chart_account_id FROM account_types WHERE id=9))");
                                    $dataServiceAccount = mysql_fetch_array($queryServiceAccount);
                                    $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $dataServiceAccount[0];
                                    $generalLedgerDetail['GeneralLedgerDetail']['company_id'] = $invoice['Invoice']['company_id'];
                                    $generalLedgerDetail['GeneralLedgerDetail']['service_id'] = $this->data['Patient']['service_id'][$i];
                                    if ($this->data['Patient']['section_id'][$i] == "labo") {
                                        $generalLedgerDetail['GeneralLedgerDetail']['type'] = 'Invoice Service Labo';
                                    } else if ($this->data['Patient']['section_id'][$i] == "medicine") {
                                        $generalLedgerDetail['GeneralLedgerDetail']['type'] = 'Invoice Service Medicine';
                                    } else {
                                        $generalLedgerDetail['GeneralLedgerDetail']['type'] = 'Invoice Service';
                                    }
                                    $generalLedgerDetail['GeneralLedgerDetail']['debit'] = 0;
                                    $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $this->data['Patient']['total_price'][$i];
                                    $generalLedgerDetail['GeneralLedgerDetail']['memo'] = 'ICS: Invoice Service # ' . $invoice['Invoice']['invoice_code'] . ' ' . $this->data['Patient']['service_id'][$i];
                                    $generalLedgerDetail['GeneralLedgerDetail']['queue_id'] = $invoice['Invoice']['queue_id'];
                                    $generalLedgerDetail['GeneralLedgerDetail']['customer_id'] = $this->data['Patient']['id'];
                                    $this->GeneralLedgerDetail->save($generalLedgerDetail);

                                    /* General Ledger Detail (Service Discount) */
                                    if ($this->data['Patient']['discount'][$i] > 0) {
                                        $this->GeneralLedgerDetail->create();
                                        $generalLedgerDetail = array();
                                        $salesDiscAccount = $this->AccountType->findById(11);
                                        $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $generalLedgerId;
                                        $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $salesDiscAccount['AccountType']['chart_account_id'];
                                        $generalLedgerDetail['GeneralLedgerDetail']['company_id'] = $invoice['Invoice']['company_id'];
                                        $generalLedgerDetail['GeneralLedgerDetail']['service_id'] = $this->data['Patient']['service_id'][$i];
                                        if ($this->data['Patient']['section_id'][$i] == "labo") {
                                            $generalLedgerDetail['GeneralLedgerDetail']['type'] = 'Invoice Service Labo';
                                        } else if ($this->data['Patient']['section_id'][$i] == "medicine") {
                                            $generalLedgerDetail['GeneralLedgerDetail']['type'] = 'Invoice Service Medicine';
                                        } else {
                                            $generalLedgerDetail['GeneralLedgerDetail']['type'] = 'Invoice Service';
                                        }
                                        $generalLedgerDetail['GeneralLedgerDetail']['debit'] = $this->data['Patient']['discount'][$i];
                                        $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                                        $generalLedgerDetail['GeneralLedgerDetail']['memo'] = 'ICS: Invoice Service # ' . $invoice['Invoice']['invoice_code'] . ' ' . $this->data['Patient']['service_id'][$i] . ' Discount';
                                        $generalLedgerDetail['GeneralLedgerDetail']['queue_id'] = $invoice['Invoice']['queue_id'];
                                        $generalLedgerDetail['GeneralLedgerDetail']['customer_id'] = $this->data['Patient']['id'];
                                        $this->GeneralLedgerDetail->save($generalLedgerDetail);
                                    }
                                }
                            }
                        }
                    }

                    if ($this->data['SalesOrder']['id'] != "") {
                        $salesOrder = array();
                        $salesOrder['SalesOrder']['id'] = $this->data['SalesOrder']['id'];
                        $salesOrder['SalesOrder']['modified_by'] = $user['User']['id'];
                        $salesOrder['SalesOrder']['balance'] = $this->data['SalesOrder']['balance_us'];
                        if ($this->SalesOrder->save($salesOrder)) {
                            $lastExchangeRate = ClassRegistry::init('ExchangeRate')->find("first", array("conditions" => array(
                                    "ExchangeRate.branch_id" => $this->data['Patient']['branch_id'],
                                    "ExchangeRate.currency_center_id" => $this->data['SalesOrder']['currency_center_id']), "order" => array("ExchangeRate.created desc")));
                            if (!empty($lastExchangeRate) && $lastExchangeRate['ExchangeRate']['rate_to_sell'] > 0) {
                                $exchangeRateId = $lastExchangeRate['ExchangeRate']['id'];
                            } else {
                                $exchangeRateId = 0;
                            }
                            $dateNow = date("Y-m-d H:i:s");

                            // Sales Order Receipt
                            $this->SalesOrderReceipt->create();
                            $salesOrderReceipt = array();
                            $salesOrderReceipt['SalesOrderReceipt']['sales_order_id'] = $this->data['SalesOrder']['id'];
                            $salesOrderReceipt['SalesOrderReceipt']['sys_code'] = md5(rand() . strtotime(date("Y-m-d H:i:s")) . $user['User']['id']);
                            $salesOrderReceipt['SalesOrderReceipt']['branch_id'] = $this->data['Patient']['branch_id'];
                            $salesOrderReceipt['SalesOrderReceipt']['exchange_rate_id'] = $exchangeRateId;
                            $salesOrderReceipt['SalesOrderReceipt']['currency_center_id'] = $this->data['SalesOrder']['currency_center_id'];
                            $salesOrderReceipt['SalesOrderReceipt']['chart_account_id'] = $this->data['Patient']['chart_account_cash_id'];
                            $salesOrderReceipt['SalesOrderReceipt']['receipt_code'] = $this->Helper->getAutoGenerateSalesOrderReceiptCode();
                            $salesOrderReceipt['SalesOrderReceipt']['amount_us'] = $this->data['SalesOrder']['total_amount'];
                            $salesOrderReceipt['SalesOrderReceipt']['amount_other'] = $this->data['SalesOrder']['total_amount'];
                            $salesOrderReceipt['SalesOrderReceipt']['discount_us'] = 0;
                            $salesOrderReceipt['SalesOrderReceipt']['discount_other'] = 0;
                            $salesOrderReceipt['SalesOrderReceipt']['total_amount'] = $this->data['SalesOrder']['total_amount'];
                            $salesOrderReceipt['SalesOrderReceipt']['balance'] = 0;
                            $salesOrderReceipt['SalesOrderReceipt']['balance_other'] = 0;
                            $salesOrderReceipt['SalesOrderReceipt']['created'] = $dateNow;
                            $salesOrderReceipt['SalesOrderReceipt']['created_by'] = $user['User']['id'];
                            $salesOrderReceipt['SalesOrderReceipt']['pay_date'] = date('Y-m-d');
                            $salesOrderReceipt['SalesOrderReceipt']['due_date'] = date('Y-m-d');
                            $this->SalesOrderReceipt->save($salesOrderReceipt);
                            $result['sr_id'] = $this->SalesOrderReceipt->id;
                            // Load Model
                            $this->loadModel('GeneralLedger');
                            $this->loadModel('GeneralLedgerDetail');
                            $this->loadModel('LocationGroup');
                            $this->loadModel('AccountType');
                            // Get Default Chart Account Paid
                            $cashBankAccount = $this->AccountType->findById(6);
                            $salesOrder = $this->SalesOrder->findById($this->data['SalesOrder']['id']);
                            $generalLedger = $this->GeneralLedger->find("first", array("conditions" => array("GeneralLedger.sales_order_id" => $salesOrder['SalesOrder']['id'])));
                            // Get Total Paid
                            $totalPaid = $this->data['SalesOrder']['total_amount'];
                            if ($this->data['SalesOrder']['balance_us'] > 0) {
                                $salesOrderReceipt['SalesOrderReceipt']['due_date'] = date('Y-m-d');
                            }
                            $locationGroup = $this->LocationGroup->read(null, $salesOrder['SalesOrder']['location_group_id']);
                            //$classId         = $locationGroup['LocationGroup']['class_id'];
                            if (($this->data['SalesOrder']['total_amount'] - $this->data['SalesOrder']['balance_us']) > 0) {
                                // Save General Ledger Detail
                                $this->GeneralLedger->create();
                                $generalLedger = array();
                                $generalLedger['GeneralLedger']['sales_order_id'] = $this->data['SalesOrder']['id'];
                                $generalLedger['GeneralLedger']['sales_order_receipt_id'] = $result['sr_id'];
                                $generalLedger['GeneralLedger']['date'] = date('Y-m-d');
                                $generalLedger['GeneralLedger']['reference'] = $salesOrder['SalesOrder']['so_code'];
                                $generalLedger['GeneralLedger']['created_by'] = $user['User']['id'];
                                $generalLedger['GeneralLedger']['is_sys'] = 1;
                                $generalLedger['GeneralLedger']['is_adj'] = 0;
                                $generalLedger['GeneralLedger']['is_active'] = 1;
                                if ($this->GeneralLedger->save($generalLedger)) {
                                    $this->GeneralLedgerDetail->create();
                                    $generalLedgerDetail = array();
                                    $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $this->GeneralLedger->id;
                                    $queryAr = mysql_query("SELECT ar_id FROM sales_orders WHERE id=" . $this->data['SalesOrder']['id']);
                                    $dataAr = mysql_fetch_array($queryAr);
                                    $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $dataAr[0];
                                    $generalLedgerDetail['GeneralLedgerDetail']['company_id'] = $salesOrder['SalesOrder']['company_id'];
                                    $generalLedgerDetail['GeneralLedgerDetail']['location_id'] = $salesOrder['SalesOrder']['location_id'];
                                    $generalLedgerDetail['GeneralLedgerDetail']['type'] = 'Payment';
                                    $generalLedgerDetail['GeneralLedgerDetail']['debit'] = 0;
                                    $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $totalPaid;
                                    $generalLedgerDetail['GeneralLedgerDetail']['memo'] = 'ICS: Payment for SO # ' . $salesOrder['SalesOrder']['so_code'];
                                    $generalLedgerDetail['GeneralLedgerDetail']['customer_id'] = $salesOrder['SalesOrder']['customer_id'];
                                    //$generalLedgerDetail['GeneralLedgerDetail']['class_id'] = $classId;
                                    $this->GeneralLedgerDetail->save($generalLedgerDetail);
                                }
                            }
                        }
                    }
                    /**
                     * Update Queue
                     */
                    if ($queueId != '') {
                        $Queue['Queue']['id'] = $queueId;
                        $Queue['Queue']['modified_by'] = $user['User']['id'];
                        $Queue['Queue']['status'] = 3;
                        $this->Queue->save($Queue);
                    }
                    /**
                     * Update TmpService
                     */
                    if ($this->data['TmpService']['id'] != '') {
                        $TmpService['TmpService']['id'] = $this->data['TmpService']['id'];
                        $TmpService['TmpService']['modified_by'] = $user['User']['id'];
                        $TmpService['TmpService']['status'] = 2;
                        $this->TmpService->save($TmpService);
                    }


                    /**
                     * Result
                     */
                    echo $invoiceId;
                    exit;
                } else {
                    $this->Session->setFlash(__('The invoice could not be saved. Please, try again.', true), 'flash_failure');
                }
            }
        }
    }

    function getService($sectionId = null) {
        $this->layout = 'ajax';
        $this->loadModel('Service');
        $services = ClassRegistry::init('Service')->find('all', array('fields' => 'Service.id, Service.name', 'conditions' => array('Service.is_active=1', 'Service.section_id' => $sectionId), 'order' => 'Service.name', 'recursive' => 0));
        $this->set(compact('services'));
    }

    function getServicePrice($id = null, $pateintGroup = null, $companyInsuranceId = null) {
        $this->layout = 'ajax';
        $this->loadModel('ServicesPatientGroupDetail');
        $this->loadModel('ServicesPriceInsurancePatientGroupDetail');
        $this->loadModel('ServicesPriceInsurance');
        if ($companyInsuranceId != "") {
            $services = $this->ServicesPriceInsurance->find('first', array('conditions' => array('ServicesPriceInsurancePatientGroupDetail.is_active' => 1, 'ServicesPriceInsurance.service_id' => $id, 'ServicesPriceInsurance.company_insurance_id' => $companyInsuranceId, 'ServicesPriceInsurancePatientGroupDetail.patient_group_id' => $pateintGroup),
                'fields' => array('ServicesPriceInsurancePatientGroupDetail.unit_price'),
                'joins' => array(
                    array('table' => 'services_price_insurance_patient_group_details',
                        'alias' => 'ServicesPriceInsurancePatientGroupDetail',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'ServicesPriceInsurance.id = ServicesPriceInsurancePatientGroupDetail.services_price_insurance_id'
                        )
                    )
            )));
            echo $services['ServicesPriceInsurancePatientGroupDetail']['unit_price'];
        } else {
            $services = $this->ServicesPatientGroupDetail->find('first', array('fields' => array('ServicesPatientGroupDetail.unit_price'), 'conditions' => array('ServicesPatientGroupDetail.service_id' => $id, 'ServicesPatientGroupDetail.patient_group_id' => $pateintGroup, 'ServicesPatientGroupDetail.is_active' => 1)));
            echo $services['ServicesPatientGroupDetail']['unit_price'];
        }

        exit();
    }

    function patientPayment($patientIpdId = null) {
        $this->layout = 'ajax';
        if (empty($this->data)) {
            $this->loadModel('Section');
            $this->loadModel('Service');
            $this->loadModel('Company');
            $this->loadModel('User');
            $this->loadModel('AccountType');
            $this->loadModel('Patient');
            $this->loadModel('PatientIpd');
            $this->loadModel('PatientIpdServiceDetail');
            $this->loadModel('Queue');
            $this->loadModel('DoctorConsultation');
            $this->loadModel('SalesOrder');
            $this->loadModel('SalesOrderReceipt');
            $this->loadModel('ExchangeRate');

            $user = $this->getCurrentUser();
            $companies = ClassRegistry::init('Company')->find('all', array(
                'joins' => array(
                    array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                    )
                ),
                'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                    )
            );
            $sections = ClassRegistry::init('Section')->find('list', array('conditions' => 'Section.is_active=1'));
            $services = ClassRegistry::init('Service')->find('list', array('conditions' => 'Service.is_active=1'));

            $patientIpd = ClassRegistry::init('PatientIpd')->find('first', array('conditions' => array('PatientIpd.is_active >= 1', 'PatientIpd.id' => $patientIpdId)));
            $dataServiceLabo = $this->PatientIpdServiceDetail->find('all', array('conditions' => array('PatientIpdServiceDetail.is_active' => 1, 'PatientIpdServiceDetail.type' => 2, 'PatientIpdServiceDetail.patient_ipd_id' => $patientIpdId),
                'fields' => array('PatientIpdServiceDetail.*, LaboItemGroup.name'),
                'joins' => array(
                    array('table' => 'labo_item_groups',
                        'alias' => 'LaboItemGroup',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'LaboItemGroup.id = PatientIpdServiceDetail.service_id'
                        )
                    )
            )));
            $dataServiceDetail = $this->PatientIpdServiceDetail->find('all', array('conditions' => array('PatientIpdServiceDetail.is_active' => 1, 'PatientIpdServiceDetail.type' => 1, 'PatientIpdServiceDetail.patient_ipd_id' => $patientIpdId),
                'fields' => array('PatientIpdServiceDetail.*, Section.id'),
                'joins' => array(
                    array('table' => 'services',
                        'alias' => 'Service',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Service.id = PatientIpdServiceDetail.service_id'
                        )
                    ),
                    array('table' => 'sections',
                        'alias' => 'Section',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Section.id = Service.section_id'
                        )
                    ),
                    array('table' => 'patient_ipds',
                        'alias' => 'PatientIpd',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'PatientIpd.id = PatientIpdServiceDetail.patient_ipd_id'
                        )
                    )
            )));

            $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'Patient.id' => $patientIpd['PatientIpd']['patient_id']),
                'fields' => array('Patient.*, PatientBillType.name, PatientBillType.id, Nationality.name, PatientType.name'),
                'joins' => array(
                    array('table' => 'patient_bill_types',
                        'alias' => 'PatientBillType',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'PatientBillType.id = Patient.patient_bill_type_id'
                        )
                    ),
                    array('table' => 'patient_types',
                        'alias' => 'PatientType',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'PatientType.id = Patient.patient_type_id'
                        )
                    ),
                    array('table' => 'nationalities',
                        'alias' => 'Nationality',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Nationality.id = Patient.nationality'
                        )
                    )
            )));
            $doctors = $this->User->find('all', array('conditions' => array('User.is_active' => 1, 'UserGroup.group_id' => array('2', '21')), 'order' => array('Employee.name ASC'), 'group' => 'User.id',
                'fields' => array('User.id, Employee.name, Company.id'),
                'joins' => array(
                    array('table' => 'user_employees',
                        'alias' => 'UserEmployee',
                        'type' => 'INNER',
                        'conditions' => array(
                            'User.id = UserEmployee.user_id'
                        )
                    ),
                    array('table' => 'employees',
                        'alias' => 'Employee',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Employee.id = UserEmployee.employee_id'
                        )
                    ),
                    array('table' => 'user_companies',
                        'alias' => 'UserCompany',
                        'type' => 'INNER',
                        'conditions' => array(
                            'UserCompany.user_id = User.id'
                        )
                    ),
                    array('table' => 'companies',
                        'alias' => 'Company',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Company.id = UserCompany.company_id'
                        )
                    ),
                    array('table' => 'user_groups',
                        'alias' => 'UserGroup',
                        'type' => 'INNER',
                        'conditions' => array(
                            'User.id = UserGroup.user_id'
                        )
                    )
            )));
            $branches = ClassRegistry::init('Branch')->find('all', array(
                'joins' => array(
                    array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id')),
                    array('table' => 'module_code_branches AS ModuleCodeBranch', 'type' => 'left', 'conditions' => array('ModuleCodeBranch.branch_id=Branch.id'))
                ),
                'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id', 'ModuleCodeBranch.so_code', 'Branch.currency_center_id', 'CurrencyCenter.symbol'),
                'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])
            ));
            $salesOrders = ClassRegistry::init('SalesOrder')->find('all', array('conditions' => array('SalesOrder.status >=' => 1, 'SalesOrder.balance >' => 0, 'SalesOrder.queue_doctor_id' => $patientIpd['PatientIpd']['queued_doctor_id'], 'SalesOrder.patient_id' => $patientIpd['PatientIpd']['patient_id'])));
            $arAccount = ClassRegistry::init('AccountType')->findById(7);
            $arAccountId = $arAccount['AccountType']['chart_account_id'];
            $cashBankAccount = ClassRegistry::init('AccountType')->findById(6);
            $cashBankAccountId = $cashBankAccount['AccountType']['chart_account_id'];
            $companyInsurances = ClassRegistry::init('CompanyInsurance')->find('list', array('conditions' => 'is_active=1'));
            $this->set(compact('sections', 'services', 'patient', 'branches', 'companies', 'doctors', 'arAccountId', 'locations', 'companyInsurances', 'patientIpdId', 'dataServiceDetail', 'patientIpd', 'cashBankAccountId', 'salesOrders', 'dataServiceLabo'));
        }
        if (!empty($this->data)) {
            $user = $this->getCurrentUser();
            $date = date('Y-m-d H:i:s');
            $this->loadModel('PatientIpd');
            $this->loadModel('PatientIpdServiceDetail');
            $this->loadModel('Queue');
            $this->loadModel('Invoice');
            $this->loadModel('InvoiceDetail');
            $this->loadModel('Receipt');
            $this->loadModel('SalesOrder');
            $this->loadModel('SalesOrderReceipt');

            // create new queue for patient in hospital
            $this->Queue->create();
            $data['Queue']['patient_id'] = $this->data['Patient']['id'];
            $data['Queue']['patient_type_id'] = 2;
            $data['Queue']['created_by'] = $user['User']['id'];
            $data['Queue']['status'] = 3;
            if ($this->Queue->save($data['Queue'])) {
                $queueId = $this->Queue->getLastInsertId();
            }
            if (isset($this->data['PatientIpd']['company_insurance_id']) && $this->data['PatientIpd']['company_insurance_id'] != "") {
                $this->PatientIpd->updateAll(
                        array('PatientIpd.company_insurance_id' => $this->data['PatientIpd']['company_insurance_id'], 'PatientIpd.modified' => "'$date'", 'PatientIpd.modified_by' => $user['User']['id']), array('PatientIpd.id' => $this->data['PatientIpd']['id'])
                );
            }
            $this->PatientIpdServiceDetail->updateAll(
                    array('PatientIpdServiceDetail.is_active' => "0", 'PatientIpdServiceDetail.modified' => "'$date'", 'PatientIpdServiceDetail.modified_by' => $user['User']['id']), array('PatientIpdServiceDetail.patient_ipd_id' => $this->data['PatientIpd']['id'], 'PatientIpdServiceDetail.is_active' => 1)
            );
            for ($i = 0; $i < sizeof($this->data['Patient']['section_id']); $i++) {
                if ($this->data['Patient']['section_id'][$i] != '') {
                    $this->PatientIpdServiceDetail->create();
                    if ($this->data['Patient']['section_id'][$i] == "labo") {
                        $PatientIpdServiceDetail['PatientIpdServiceDetail']['type'] = 2;
                    } else if ($this->data['Patient']['section_id'][$i] == "medicine") {
                        $PatientIpdServiceDetail['PatientIpdServiceDetail']['type'] = 3;
                    } else {
                        $PatientIpdServiceDetail['PatientIpdServiceDetail']['type'] = 1;
                    }
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['patient_ipd_id'] = $this->data['PatientIpd']['id'];
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['exchange_rate_id'] = $this->data['Patient']['exchange_rate_id'];
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['service_id'] = $this->data['Patient']['service_id'][$i];
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['doctor_id'] = $this->data['Patient']['doctor_id'][$i];
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['qty'] = $this->data['Patient']['qty'][$i];
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['date_created'] = $this->data['Patient']['date_created'][$i];
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['unit_price'] = $this->data['Patient']['unit_price'][$i];
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['total_price'] = $this->data['Patient']['total_price'][$i];
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['created_by'] = $user['User']['id'];
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['is_active'] = 2;
                    $this->PatientIpdServiceDetail->save($PatientIpdServiceDetail['PatientIpdServiceDetail']);
                }
            }
            // create new invoice for patient
            // Find Chart Account 
            $this->loadModel('AccountType');
            $arAccount = $this->AccountType->findById(7);
            $this->Invoice->create();
            if ($this->data['Patient']['chart_account_id'] == '') {
                $invoice['Invoice']['ar_id'] = $arAccount['AccountType']['chart_account_id'];
            } else {
                $invoice['Invoice']['ar_id'] = $this->data['Patient']['chart_account_id'];
            }
            $invoice['Invoice']['queue_id'] = $queueId;
            $invoice['Invoice']['invoice_code'] = $this->Helper->getAutoGenerateInvoiceCode();
            $invoice['Invoice']['company_id'] = $this->data['Patient']['company_id'];
            if (isset($this->data['Patient']['company_insurance_id'])) {
                $invoice['Invoice']['company_insurance_id'] = $this->data['Patient']['company_insurance_id'];
            }
            $invoice['Invoice']['total_amount'] = $this->data['Patient']['total_amount'];
            $invoice['Invoice']['total_discount'] = $this->data['Patient']['total_discount'];
            $invoice['Invoice']['balance'] = $this->data['Patient']['sub_total_amount'];
            $invoice['Invoice']['created_by'] = $user['User']['id'];
            if ($this->Invoice->save($invoice)) {
                $invoiceId = $this->Invoice->getLastInsertId();
                /**
                 * Invoice Detail
                 */
                for ($i = 0; $i < sizeof($this->data['Patient']['section_id']); $i++) {
                    if ($this->data['Patient']['section_id'][$i] != '') {
                        $this->InvoiceDetail->create();
                        if ($this->data['Patient']['section_id'][$i] == "labo") {
                            $invoiceDetail['InvoiceDetail']['type'] = 2;
                        } else if ($this->data['Patient']['section_id'][$i] == "medicine") {
                            $invoiceDetail['InvoiceDetail']['type'] = 3;
                        } else {
                            $invoiceDetail['InvoiceDetail']['type'] = 1;
                        }
                        $invoiceDetail['InvoiceDetail']['exchange_rate_id'] = $this->data['Patient']['exchange_rate_id'];
                        $invoiceDetail['InvoiceDetail']['invoice_id'] = $invoiceId;
                        $invoiceDetail['InvoiceDetail']['date_created'] = $this->data['Patient']['date_created'][$i];
                        $invoiceDetail['InvoiceDetail']['service_id'] = $this->data['Patient']['service_id'][$i];
                        $invoiceDetail['InvoiceDetail']['doctor_id'] = $this->data['Patient']['doctor_id'][$i];
                        $invoiceDetail['InvoiceDetail']['qty'] = $this->data['Patient']['qty'][$i];
                        if (!isset($this->data['Patient']['discount'][$i])) {
                            $invoiceDetail['InvoiceDetail']['discount'] = 0;
                        } else {
                            $invoiceDetail['InvoiceDetail']['discount'] = $this->data['Patient']['discount'][$i];
                        }
                        $invoiceDetail['InvoiceDetail']['unit_price'] = $this->data['Patient']['unit_price'][$i];
                        $invoiceDetail['InvoiceDetail']['total_price'] = $this->data['Patient']['total_price'][$i];
                        $invoiceDetail['InvoiceDetail']['created_by'] = $user['User']['id'];
                        $this->InvoiceDetail->save($invoiceDetail['InvoiceDetail']);
                    }
                }
                /**
                 * Receipt
                 */
                if ($this->data['Patient']['total_amount_paid'] > 0) {
                    $this->Receipt->create();
                    $receipt['Receipt']['invoice_id'] = $invoiceId;
                    $receipt['Receipt']['receipt_code'] = $this->Helper->getAutoGenerateReceiptCode();
                    if (isset($this->data['Patient']['chart_account_id_cash'])) {
                        $arAccount = $this->AccountType->findById(6);
                        $receipt['Receipt']['chart_account_id'] = $arAccount['AccountType']['chart_account_id'];
                    } else {
                        $receipt['Receipt']['chart_account_id'] = $this->data['Patient']['chart_account_id_cash'];
                    }
                    $receipt['Receipt']['pay_date'] = date('Y-m-d');
                    $receipt['Receipt']['exchange_rate_id'] = $this->data['Patient']['exchange_rate_id'];
                    $receipt['Receipt']['total_amount_paid'] = $this->data['Patient']['total_amount_paid'];
                    $receipt['Receipt']['balance'] = $this->data['Patient']['sub_total_amount'];
                    $receipt['Receipt']['created_by'] = $user['User']['id'];
                    if ($this->Receipt->save($receipt)) {
                        $receiptId = $this->Receipt->getLastInsertId();
                        $this->loadModel('GeneralLedger');
                        $this->loadModel('GeneralLedgerDetail');

                        $invoice = $this->Invoice->findById($invoiceId);
                        $totalPaid = $this->data['Patient']['total_amount_paid'];
                        // Save General Ledger Detail
                        $this->GeneralLedger->create();
                        $generalLedger = array();
                        $generalLedger['GeneralLedger']['invoice_id'] = $invoiceId;
                        $generalLedger['GeneralLedger']['receipt_id'] = $receiptId;
                        $generalLedger['GeneralLedger']['receive_payment_id'] = "";
                        $generalLedger['GeneralLedger']['date'] = date('Y-m-d');
                        $generalLedger['GeneralLedger']['reference'] = "";
                        $generalLedger['GeneralLedger']['created_by'] = $user['User']['id'];
                        $generalLedger['GeneralLedger']['is_sys'] = 1;
                        $generalLedger['GeneralLedger']['is_adj'] = 0;
                        $generalLedger['GeneralLedger']['is_active'] = 1;
                        if ($this->GeneralLedger->save($generalLedger)) {
                            $this->GeneralLedgerDetail->create();
                            $generalLedgerDetail = array();
                            $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $this->GeneralLedger->id;
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $invoice['Invoice']['ar_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['company_id'] = $invoice['Invoice']['company_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['type'] = 'Payment';
                            $generalLedgerDetail['GeneralLedgerDetail']['debit'] = $totalPaid;
                            $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['memo'] = 'ICS: Payment for Invoice # ' . $invoice['Invoice']['invoice_code'];
                            $generalLedgerDetail['GeneralLedgerDetail']['queue_id'] = $invoice['Invoice']['queue_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['customer_id'] = $this->data['Patient']['id'];
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            $this->GeneralLedgerDetail->create();
                            $generalLedgerDetail = array();
                            $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $this->GeneralLedger->id;
                            $queryAr = mysql_query("SELECT ar_id FROM invoices WHERE id=" . $invoiceId);
                            $dataAr = mysql_fetch_array($queryAr);
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $dataAr[0];
                            $generalLedgerDetail['GeneralLedgerDetail']['company_id'] = $invoice['Invoice']['company_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['type'] = 'Payment';
                            $generalLedgerDetail['GeneralLedgerDetail']['debit'] = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $totalPaid;
                            $generalLedgerDetail['GeneralLedgerDetail']['memo'] = 'ICS: Payment for Invoice # ' . $invoice['Invoice']['invoice_code'];
                            $generalLedgerDetail['GeneralLedgerDetail']['queue_id'] = $invoice['Invoice']['queue_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['customer_id'] = $this->data['Patient']['id'];
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                        }
                    }
                }

                if ($this->data['SalesOrder']['id'] != "") {
                    $salesOrder = array();
                    $salesOrder['SalesOrder']['id'] = $this->data['SalesOrder']['id'];
                    $salesOrder['SalesOrder']['modified_by'] = $user['User']['id'];
                    $salesOrder['SalesOrder']['balance'] = $this->data['SalesOrder']['balance_us'];
                    if ($this->SalesOrder->save($salesOrder)) {
                        $lastExchangeRate = ClassRegistry::init('ExchangeRate')->find("first", array("conditions" => array(
                                "ExchangeRate.branch_id" => $this->data['Patient']['branch_id'],
                                "ExchangeRate.currency_center_id" => $this->data['SalesOrder']['currency_center_id']), "order" => array("ExchangeRate.created desc")));
                        if (!empty($lastExchangeRate) && $lastExchangeRate['ExchangeRate']['rate_to_sell'] > 0) {
                            $exchangeRateId = $lastExchangeRate['ExchangeRate']['id'];
                        } else {
                            $exchangeRateId = 0;
                        }
                        $dateNow = date("Y-m-d H:i:s");

                        // Sales Order Receipt
                        $this->SalesOrderReceipt->create();
                        $salesOrderReceipt = array();
                        $salesOrderReceipt['SalesOrderReceipt']['sales_order_id'] = $this->data['SalesOrder']['id'];
                        $salesOrderReceipt['SalesOrderReceipt']['sys_code'] = md5(rand() . strtotime(date("Y-m-d H:i:s")) . $user['User']['id']);
                        $salesOrderReceipt['SalesOrderReceipt']['branch_id'] = $this->data['Patient']['branch_id'];
                        $salesOrderReceipt['SalesOrderReceipt']['exchange_rate_id'] = $exchangeRateId;
                        $salesOrderReceipt['SalesOrderReceipt']['currency_center_id'] = $this->data['SalesOrder']['currency_center_id'];
                        $salesOrderReceipt['SalesOrderReceipt']['chart_account_id'] = $this->data['Patient']['chart_account_id_cash'];
                        $salesOrderReceipt['SalesOrderReceipt']['receipt_code'] = $this->Helper->getAutoGenerateSalesOrderReceiptCode();
                        $salesOrderReceipt['SalesOrderReceipt']['amount_us'] = $this->data['SalesOrder']['total_amount'];
                        $salesOrderReceipt['SalesOrderReceipt']['amount_other'] = $this->data['SalesOrder']['total_amount'];
                        $salesOrderReceipt['SalesOrderReceipt']['discount_us'] = 0;
                        $salesOrderReceipt['SalesOrderReceipt']['discount_other'] = 0;
                        $salesOrderReceipt['SalesOrderReceipt']['total_amount'] = $this->data['SalesOrder']['total_amount'];
                        $salesOrderReceipt['SalesOrderReceipt']['balance'] = 0;
                        $salesOrderReceipt['SalesOrderReceipt']['balance_other'] = 0;
                        $salesOrderReceipt['SalesOrderReceipt']['created'] = $dateNow;
                        $salesOrderReceipt['SalesOrderReceipt']['created_by'] = $user['User']['id'];
                        $salesOrderReceipt['SalesOrderReceipt']['pay_date'] = date('Y-m-d');
                        $salesOrderReceipt['SalesOrderReceipt']['due_date'] = date('Y-m-d');
                        $this->SalesOrderReceipt->save($salesOrderReceipt);
                        $result['sr_id'] = $this->SalesOrderReceipt->id;
                        // Load Model
                        $this->loadModel('GeneralLedger');
                        $this->loadModel('GeneralLedgerDetail');
                        $this->loadModel('LocationGroup');
                        $this->loadModel('AccountType');
                        // Get Default Chart Account Paid
                        $cashBankAccount = $this->AccountType->findById(6);
                        $salesOrder = $this->SalesOrder->findById($this->data['SalesOrder']['id']);
                        $generalLedger = $this->GeneralLedger->find("first", array("conditions" => array("GeneralLedger.sales_order_id" => $salesOrder['SalesOrder']['id'])));
                        // Get Total Paid
                        $totalPaid = $this->data['SalesOrder']['total_amount'];
                        if ($this->data['SalesOrder']['balance_us'] > 0) {
                            $salesOrderReceipt['SalesOrderReceipt']['due_date'] = date('Y-m-d');
                        }
                        $locationGroup = $this->LocationGroup->read(null, $salesOrder['SalesOrder']['location_group_id']);
                        //$classId         = $locationGroup['LocationGroup']['class_id'];
                        if (($this->data['SalesOrder']['total_amount'] - $this->data['SalesOrder']['balance_us']) > 0) {
                            // Save General Ledger Detail
                            $this->GeneralLedger->create();
                            $generalLedger = array();
                            $generalLedger['GeneralLedger']['sales_order_id'] = $this->data['SalesOrder']['id'];
                            $generalLedger['GeneralLedger']['sales_order_receipt_id'] = $result['sr_id'];
                            $generalLedger['GeneralLedger']['date'] = date('Y-m-d');
                            $generalLedger['GeneralLedger']['reference'] = $salesOrder['SalesOrder']['so_code'];
                            $generalLedger['GeneralLedger']['created_by'] = $user['User']['id'];
                            $generalLedger['GeneralLedger']['is_sys'] = 1;
                            $generalLedger['GeneralLedger']['is_adj'] = 0;
                            $generalLedger['GeneralLedger']['is_active'] = 1;
                            if ($this->GeneralLedger->save($generalLedger)) {
                                $this->GeneralLedgerDetail->create();
                                $generalLedgerDetail = array();
                                $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $this->GeneralLedger->id;
                                $queryAr = mysql_query("SELECT ar_id FROM sales_orders WHERE id=" . $this->data['SalesOrder']['id']);
                                $dataAr = mysql_fetch_array($queryAr);
                                $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $dataAr[0];
                                $generalLedgerDetail['GeneralLedgerDetail']['company_id'] = $salesOrder['SalesOrder']['company_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['location_id'] = $salesOrder['SalesOrder']['location_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['type'] = 'Payment';
                                $generalLedgerDetail['GeneralLedgerDetail']['debit'] = 0;
                                $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $totalPaid;
                                $generalLedgerDetail['GeneralLedgerDetail']['memo'] = 'ICS: Payment for SO # ' . $salesOrder['SalesOrder']['so_code'];
                                $generalLedgerDetail['GeneralLedgerDetail']['customer_id'] = $salesOrder['SalesOrder']['customer_id'];
                                //$generalLedgerDetail['GeneralLedgerDetail']['class_id'] = $classId;
                                $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            }
                        }
                    }
                }


                /**
                 * Result
                 */
                echo $invoiceId;
                exit;
            } else {
                $this->Session->setFlash(__('The invoice could not be saved. Please, try again.', true), 'flash_failure');
            }
        }
    }

    function discount1() {
        $this->layout = 'ajax';
        $discounts = ClassRegistry::init('Discount')->find("all", array('conditions' => array('Discount.is_active' => 1), 'order' => array('id DESC')));
        $this->set(compact('discounts'));
    }

    function discount() {
        $this->layout = 'ajax';
    }

    function checkoutDebt($invoiceId = null) {
        $this->layout = 'ajax';
        if (!$invoiceId && empty($this->data)) {
            echo 'Invalid patient';
            exit;
        }
        if (empty($this->data)) {
            ClassRegistry::init('Invoice')->id = $invoiceId;
            ClassRegistry::init('Queue')->id = ClassRegistry::init('Invoice')->field('queue_id');
            $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'Invoice.id' => $invoiceId),
                'fields' => array('Patient.*, PatientBillType.name, PatientBillType.id, Nationality.name, PatientType.name, CompanyInsurance.name, Company.name, Invoice.*'),
                'joins' => array(
                    array('table' => 'patient_bill_types',
                        'alias' => 'PatientBillType',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'PatientBillType.id = Patient.patient_bill_type_id'
                        )
                    ),
                    array('table' => 'patient_types',
                        'alias' => 'PatientType',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'PatientType.id = Patient.patient_type_id'
                        )
                    ),
                    array('table' => 'nationalities',
                        'alias' => 'Nationality',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Nationality.id = Patient.nationality'
                        )
                    ),
                    array('table' => 'queues',
                        'alias' => 'Queue',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Patient.id = Queue.patient_id'
                        )
                    ),
                    array('table' => 'invoices',
                        'alias' => 'Invoice',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Queue.id = Invoice.queue_id'
                        )
                    ),
                    array('table' => 'invoice_details',
                        'alias' => 'InvoiceDetail',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Invoice.id = InvoiceDetail.invoice_id'
                        )
                    ),
                    array('table' => 'company_insurances',
                        'alias' => 'CompanyInsurance',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'CompanyInsurance.id = Invoice.company_insurance_id'
                        )
                    ),
                    array('table' => 'companies',
                        'alias' => 'Company',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Company.id = Invoice.company_id'
                        )
                    ),
                    array('table' => 'user_employees',
                        'alias' => 'UserEmployee',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'InvoiceDetail.doctor_id = UserEmployee.user_id'
                        )
                    ),
                    array('table' => 'employees',
                        'alias' => 'Employee',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Employee.id = UserEmployee.employee_id'
                        )
                    )
            )));
            $this->loadModel('AccountType');
            $cashBankAccount = ClassRegistry::init('AccountType')->findById(6);
            $cashBankAccountId = $cashBankAccount['AccountType']['chart_account_id'];
            $this->set(compact('patient', 'cashBankAccountId'));
        }
        if (isset($_POST['action']) && $_POST['action'] == 'checkout') {
            $this->loadModel('Invoice');
            $this->loadModel('Receipt');
            $user = $this->getCurrentUser();
            $invoice['Invoice']['id'] = $_POST['invoice_id'];
            $invoice['Invoice']['balance'] = $_POST['balance'];
            $invoice['Invoice']['modified_by'] = $user['User']['id'];
            if ($this->Invoice->save($invoice)) {
                $this->Receipt->create();
                $receipt['Receipt']['invoice_id'] = $_POST['invoice_id'];
                $receipt['Receipt']['receipt_code'] = $this->Helper->getAutoGenerateReceiptCode();
                $receipt['Receipt']['chart_account_id'] = $this->data['Patient']['chart_account_id'];
                $receipt['Receipt']['exchange_rate_id'] = $_POST['exchange_rate_id'];
                if ($_POST['total_amount_r'] > 0) {
                    $receipt['Receipt']['total_amount_paid'] = $_POST['total_amount_d'] + ($_POST['total_amount_r'] / $_POST['exchange_rate']);
                } else {
                    $receipt['Receipt']['total_amount_paid'] = $_POST['total_amount_d'];
                }
                $receipt['Receipt']['total_dis'] = $this->data['Patient']['total_discount'];
                $receipt['Receipt']['total_dis_p'] = $this->data['Patient']['total_discount_per'];
                $receipt['Receipt']['balance'] = $_POST['balance'];
                $receipt['Receipt']['pay_date'] = date('Y-m-d');
                $receipt['Receipt']['created_by'] = $user['User']['id'];
                if ($this->Receipt->save($receipt)) {

                    $receiptId = $this->Receipt->getLastInsertId();
                    $this->loadModel('GeneralLedger');
                    $this->loadModel('GeneralLedgerDetail');
                    $this->loadModel('AccountType');

                    $invoice = $this->Invoice->findById($_POST['invoice_id']);
                    if ($_POST['total_amount_r'] > 0) {
                        $totalPaid = $_POST['total_amount_d'] + ($_POST['total_amount_r'] / $_POST['exchange_rate']);
                    } else {
                        $totalPaid = $_POST['total_amount_d'];
                    }
                    // Save General Ledger Detail
                    $this->GeneralLedger->create();
                    $generalLedger = array();
                    $generalLedger['GeneralLedger']['invoice_id'] = $_POST['invoice_id'];
                    $generalLedger['GeneralLedger']['receipt_id'] = $receiptId;
                    $generalLedger['GeneralLedger']['receive_payment_id'] = "";
                    $generalLedger['GeneralLedger']['date'] = date('Y-m-d');
                    $generalLedger['GeneralLedger']['reference'] = "";
                    $generalLedger['GeneralLedger']['created_by'] = $user['User']['id'];
                    $generalLedger['GeneralLedger']['is_sys'] = 1;
                    $generalLedger['GeneralLedger']['is_adj'] = 0;
                    $generalLedger['GeneralLedger']['is_active'] = 1;
                    if ($this->GeneralLedger->save($generalLedger)) {
                        $this->GeneralLedgerDetail->create();
                        $generalLedgerDetail = array();
                        $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $this->GeneralLedger->id;
                        $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $invoice['Invoice']['ar_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['company_id'] = $invoice['Invoice']['company_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['type'] = 'Payment';
                        $generalLedgerDetail['GeneralLedgerDetail']['debit'] = $totalPaid;
                        $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                        $generalLedgerDetail['GeneralLedgerDetail']['memo'] = 'ICS: Payment for Invoice # ' . $invoice['Invoice']['invoice_code'];
                        $generalLedgerDetail['GeneralLedgerDetail']['queue_id'] = $invoice['Invoice']['queue_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['customer_id'] = $this->data['Patient']['id'];
                        $this->GeneralLedgerDetail->save($generalLedgerDetail);
                        $this->GeneralLedgerDetail->create();
                        $generalLedgerDetail = array();
                        $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $this->GeneralLedger->id;
                        $queryAr = mysql_query("SELECT ar_id FROM invoices WHERE id=" . $_POST['invoice_id']);
                        $dataAr = mysql_fetch_array($queryAr);
                        $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $dataAr[0];
                        $generalLedgerDetail['GeneralLedgerDetail']['company_id'] = $invoice['Invoice']['company_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['type'] = 'Payment';
                        $generalLedgerDetail['GeneralLedgerDetail']['debit'] = 0;
                        $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $totalPaid;
                        $generalLedgerDetail['GeneralLedgerDetail']['memo'] = 'ICS: Payment for Invoice # ' . $invoice['Invoice']['invoice_code'];
                        $generalLedgerDetail['GeneralLedgerDetail']['queue_id'] = $invoice['Invoice']['queue_id'];
                        $generalLedgerDetail['GeneralLedgerDetail']['customer_id'] = $this->data['Patient']['id'];
                        $this->GeneralLedgerDetail->save($generalLedgerDetail);
                    }
                    /**
                     * result
                     */
                    echo $_POST['invoice_id'];
                    exit;
                } else {
                    echo 'The receipt could not be saved. Please, try again.';
                }
            }
        }
    }

    function creditMemo($patientId = null) {
        $this->layout = 'ajax';
        if (!$patientId && empty($this->data)) {
            echo 'Invalid patient';
            exit;
        }
        if (empty($this->data)) {
            $this->set('patient', $this->Patient->read(null, $patientId));
            ClassRegistry::init('Country')->id = $this->Patient->field('nationality');
            $this->set('nationality', ClassRegistry::init('Country')->field('name'));
        }
        if (isset($_POST['action']) && $_POST['action'] == 'checkout') {
            $this->loadModel('CreditMemo');
            $this->loadModel('CreditMemoDetail');
            $user = $this->getCurrentUser();
            $this->CreditMemo->create();
            $CreditMemo['CreditMemo']['patient_id'] = $patientId;
            $CreditMemo['CreditMemo']['credit_memo_code'] = $this->Helper->getAutoGenerateCreditMemoCode();
            $CreditMemo['CreditMemo']['total_amount'] = $_POST['amount'];
            $CreditMemo['CreditMemo']['created_by'] = $user['User']['id'];
            if ($this->CreditMemo->save($CreditMemo)) {
                $credit_memo_id = $this->CreditMemo->getLastInsertId();
                /**
                 * Credit Memo Detail
                 */
                for ($i = 0; $i < sizeof($_POST['section_id']); $i++) {
                    if ($_POST['service_id'][$i] != '') {
                        $this->CreditMemoDetail->create();
                        $CreditMemoDetail['CreditMemoDetail']['credit_memo_id'] = $credit_memo_id;
                        $CreditMemoDetail['CreditMemoDetail']['service_id'] = $_POST['service_id'][$i];
                        $CreditMemoDetail['CreditMemoDetail']['qty'] = $_POST['qty'][$i];
                        $CreditMemoDetail['CreditMemoDetail']['unit_price'] = $_POST['unit_price_d'][$i];
                        $CreditMemoDetail['CreditMemoDetail']['total_price'] = $_POST['total_price_d'][$i];
                        $this->CreditMemoDetail->save($CreditMemoDetail);
                    }
                }
                /**
                 * Result
                 */
                echo $credit_memo_id;
                exit;
            } else {
                echo 'The credit memo could not be saved. Please, try again.';
            }
        }
    }

    function printInvoice($invoiceId = null) {
        $this->layout = 'ajax';
        ClassRegistry::init('Queue')->id = ClassRegistry::init('Invoice')->field('queue_id');
        $patient = $this->Patient->find('first', array('conditions' => array('Invoice.is_void' => 0, 'Invoice.id' => $invoiceId),
            'fields' => array('Patient.*, PatientBillType.name, PatientBillType.id, Nationality.name, PatientType.name, CompanyInsurance.name, Company.name, Company.photo, Branch.*, Invoice.*'),
            'joins' => array(
                array('table' => 'patient_bill_types',
                    'alias' => 'PatientBillType',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientBillType.id = Patient.patient_bill_type_id'
                    )
                ),
                array('table' => 'patient_types',
                    'alias' => 'PatientType',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientType.id = Patient.patient_type_id'
                    )
                ),
                array('table' => 'nationalities',
                    'alias' => 'Nationality',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Nationality.id = Patient.nationality'
                    )
                ),
                array('table' => 'queues',
                    'alias' => 'Queue',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Patient.id = Queue.patient_id'
                    )
                ),
                array('table' => 'invoices',
                    'alias' => 'Invoice',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Queue.id = Invoice.queue_id'
                    )
                ),
                array('table' => 'invoice_details',
                    'alias' => 'InvoiceDetail',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Invoice.id = InvoiceDetail.invoice_id'
                    )
                ),
                array('table' => 'company_insurances',
                    'alias' => 'CompanyInsurance',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'CompanyInsurance.id = Invoice.company_insurance_id'
                    )
                ),
                array('table' => 'companies',
                    'alias' => 'Company',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Company.id = Invoice.company_id'
                    )
                ),
                array('table' => 'branches',
                    'alias' => 'Branch',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Branch.id = Invoice.branch_id'
                    )
                ),
                array('table' => 'user_employees',
                    'alias' => 'UserEmployee',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'InvoiceDetail.doctor_id = UserEmployee.user_id'
                    )
                ),
                array('table' => 'employees',
                    'alias' => 'Employee',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Employee.id = UserEmployee.employee_id'
                    )
                )

        )));
        $this->set(compact('patient'));
    }

    function printInvoiceDetail($invoiceId = null) {
        $this->layout = 'ajax';
        ClassRegistry::init('Queue')->id = ClassRegistry::init('Invoice')->field('queue_id');
        $patient = $this->Patient->find('first', array('conditions' => array('Invoice.is_void' => 0, 'Invoice.id' => $invoiceId),
            'fields' => array('Patient.*, PatientBillType.name, PatientBillType.id, Nationality.name, PatientType.name, CompanyInsurance.name, Company.name, Company.photo, Branch.*, Invoice.*'),
            'joins' => array(
                array('table' => 'patient_bill_types',
                    'alias' => 'PatientBillType',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientBillType.id = Patient.patient_bill_type_id'
                    )
                ),
                array('table' => 'patient_types',
                    'alias' => 'PatientType',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientType.id = Patient.patient_type_id'
                    )
                ),
                array('table' => 'nationalities',
                    'alias' => 'Nationality',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Nationality.id = Patient.nationality'
                    )
                ),
                array('table' => 'queues',
                    'alias' => 'Queue',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Patient.id = Queue.patient_id'
                    )
                ),
                array('table' => 'invoices',
                    'alias' => 'Invoice',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Queue.id = Invoice.queue_id'
                    )
                ),
                array('table' => 'invoice_details',
                    'alias' => 'InvoiceDetail',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Invoice.id = InvoiceDetail.invoice_id'
                    )
                ),
                array('table' => 'company_insurances',
                    'alias' => 'CompanyInsurance',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'CompanyInsurance.id = Invoice.company_insurance_id'
                    )
                ),
                array('table' => 'companies',
                    'alias' => 'Company',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Company.id = Invoice.company_id'
                    )
                ),
                array('table' => 'branches',
                    'alias' => 'Branch',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Branch.id = Invoice.branch_id'
                    )
                ),
                array('table' => 'user_employees',
                    'alias' => 'UserEmployee',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'InvoiceDetail.doctor_id = UserEmployee.user_id'
                    )
                ),
                array('table' => 'employees',
                    'alias' => 'Employee',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Employee.id = UserEmployee.employee_id'
                    )
                )
        )));
        $this->set(compact('patient'));
    }

    function printInvoiceVat($invoiceId = null) {
        $this->layout = 'ajax';
        ClassRegistry::init('Queue')->id = ClassRegistry::init('Invoice')->field('queue_id');
        $patient = $this->Patient->find('first', array('conditions' => array('Invoice.is_void' => 0, 'Invoice.id' => $invoiceId),
            'fields' => array('Patient.*, PatientBillType.name, PatientBillType.id, Nationality.name, PatientType.name, CompanyInsurance.name, Company.name, Invoice.*'),
            'joins' => array(
                array('table' => 'patient_bill_types',
                    'alias' => 'PatientBillType',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientBillType.id = Patient.patient_bill_type_id'
                    )
                ),
                array('table' => 'patient_types',
                    'alias' => 'PatientType',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientType.id = Patient.patient_type_id'
                    )
                ),
                array('table' => 'nationalities',
                    'alias' => 'Nationality',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Nationality.id = Patient.nationality'
                    )
                ),
                array('table' => 'queues',
                    'alias' => 'Queue',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Patient.id = Queue.patient_id'
                    )
                ),
                array('table' => 'invoices',
                    'alias' => 'Invoice',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Queue.id = Invoice.queue_id'
                    )
                ),
                array('table' => 'invoice_details',
                    'alias' => 'InvoiceDetail',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Invoice.id = InvoiceDetail.invoice_id'
                    )
                ),
                array('table' => 'company_insurances',
                    'alias' => 'CompanyInsurance',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'CompanyInsurance.id = Invoice.company_insurance_id'
                    )
                ),
                array('table' => 'companies',
                    'alias' => 'Company',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Company.id = Invoice.company_id'
                    )
                ),
                array('table' => 'user_employees',
                    'alias' => 'UserEmployee',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'InvoiceDetail.doctor_id = UserEmployee.user_id'
                    )
                ),
                array('table' => 'employees',
                    'alias' => 'Employee',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Employee.id = UserEmployee.employee_id'
                    )
                )
        )));
        $this->set(compact('patient'));
    }

    function printInvoiceIpd($invoiceId = null) {
        $this->layout = 'ajax';
        ClassRegistry::init('Queue')->id = ClassRegistry::init('Invoice')->field('queue_id');
        $patient = $this->Patient->find('first', array('conditions' => array('Invoice.is_void' => 0, 'Invoice.id' => $invoiceId),
            'fields' => array('Patient.*, PatientBillType.name, PatientBillType.id, Nationality.name, PatientType.name, CompanyInsurance.name, Company.name, Company.photo, Branch.*, Invoice.*'),
            'joins' => array(
                array('table' => 'patient_bill_types',
                    'alias' => 'PatientBillType',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientBillType.id = Patient.patient_bill_type_id'
                    )
                ),
                array('table' => 'patient_types',
                    'alias' => 'PatientType',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientType.id = Patient.patient_type_id'
                    )
                ),
                array('table' => 'nationalities',
                    'alias' => 'Nationality',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Nationality.id = Patient.nationality'
                    )
                ),
                array('table' => 'queues',
                    'alias' => 'Queue',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Patient.id = Queue.patient_id'
                    )
                ),
                array('table' => 'invoices',
                    'alias' => 'Invoice',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Queue.id = Invoice.queue_id'
                    )
                ),
                array('table' => 'invoice_details',
                    'alias' => 'InvoiceDetail',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Invoice.id = InvoiceDetail.invoice_id'
                    )
                ),
                array('table' => 'company_insurances',
                    'alias' => 'CompanyInsurance',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'CompanyInsurance.id = Invoice.company_insurance_id'
                    )
                ),
                array('table' => 'companies',
                    'alias' => 'Company',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Company.id = Invoice.company_id'
                    )
                ),
                array('table' => 'branches',
                    'alias' => 'Branch',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Branch.id = Invoice.branch_id'
                    )
                ),
                array('table' => 'user_employees',
                    'alias' => 'UserEmployee',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'InvoiceDetail.doctor_id = UserEmployee.user_id'
                    )
                ),
                array('table' => 'employees',
                    'alias' => 'Employee',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Employee.id = UserEmployee.employee_id'
                    )
                )
        )));
        $this->set(compact('patient'));
    }

    function printInvoiceReceipt($invoiceId = null) {
        $this->layout = 'ajax';
        ClassRegistry::init('Queue')->id = ClassRegistry::init('Invoice')->field('queue_id');
        $patient = $this->Patient->find('first', array('conditions' => array('Invoice.is_void' => 0, 'Invoice.id' => $invoiceId),
            'fields' => array('Patient.*, PatientBillType.name, PatientBillType.id, Nationality.name, PatientType.name, CompanyInsurance.name, Company.name, Company.photo, Branch.*, Invoice.*'),
            'joins' => array(
                array('table' => 'patient_bill_types',
                    'alias' => 'PatientBillType',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientBillType.id = Patient.patient_bill_type_id'
                    )
                ),
                array('table' => 'patient_types',
                    'alias' => 'PatientType',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientType.id = Patient.patient_type_id'
                    )
                ),
                array('table' => 'nationalities',
                    'alias' => 'Nationality',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Nationality.id = Patient.nationality'
                    )
                ),
                array('table' => 'queues',
                    'alias' => 'Queue',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Patient.id = Queue.patient_id'
                    )
                ),
                array('table' => 'invoices',
                    'alias' => 'Invoice',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Queue.id = Invoice.queue_id'
                    )
                ),
                array('table' => 'invoice_details',
                    'alias' => 'InvoiceDetail',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Invoice.id = InvoiceDetail.invoice_id'
                    )
                ),
                array('table' => 'company_insurances',
                    'alias' => 'CompanyInsurance',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'CompanyInsurance.id = Invoice.company_insurance_id'
                    )
                ),
                array('table' => 'companies',
                    'alias' => 'Company',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Company.id = Invoice.company_id'
                    )
                ),
                array('table' => 'branches',
                    'alias' => 'Branch',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Branch.id = Invoice.branch_id'
                    )
                ),
                array('table' => 'user_employees',
                    'alias' => 'UserEmployee',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'InvoiceDetail.doctor_id = UserEmployee.user_id'
                    )
                ),
                array('table' => 'employees',
                    'alias' => 'Employee',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Employee.id = UserEmployee.employee_id'
                    )
                )
        )));

        $this->set(compact('patient'));
    }

    function printInvoiceReceiptIpd($invoiceId = null) {
        $this->layout = 'ajax';
        ClassRegistry::init('Queue')->id = ClassRegistry::init('Invoice')->field('queue_id');
        $patient = $this->Patient->find('first', array('conditions' => array('Invoice.is_void' => 0, 'Invoice.id' => $invoiceId),
            'fields' => array('Patient.*, PatientBillType.name, PatientBillType.id, Nationality.name, PatientType.name, CompanyInsurance.name, Company.name, Company.photo, Branch.*, Invoice.*'),
            'joins' => array(
                array('table' => 'patient_bill_types',
                    'alias' => 'PatientBillType',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientBillType.id = Patient.patient_bill_type_id'
                    )
                ),
                array('table' => 'patient_types',
                    'alias' => 'PatientType',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientType.id = Patient.patient_type_id'
                    )
                ),
                array('table' => 'nationalities',
                    'alias' => 'Nationality',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Nationality.id = Patient.nationality'
                    )
                ),
                array('table' => 'queues',
                    'alias' => 'Queue',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Patient.id = Queue.patient_id'
                    )
                ),
                array('table' => 'invoices',
                    'alias' => 'Invoice',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Queue.id = Invoice.queue_id'
                    )
                ),
                array('table' => 'invoice_details',
                    'alias' => 'InvoiceDetail',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Invoice.id = InvoiceDetail.invoice_id'
                    )
                ),
                array('table' => 'company_insurances',
                    'alias' => 'CompanyInsurance',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'CompanyInsurance.id = Invoice.company_insurance_id'
                    )
                ),
                array('table' => 'companies',
                    'alias' => 'Company',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Company.id = Invoice.company_id'
                    )
                ),
                array('table' => 'branches',
                    'alias' => 'Branch',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Branch.id = Invoice.branch_id'
                    )
                ),
                array('table' => 'user_employees',
                    'alias' => 'UserEmployee',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'InvoiceDetail.doctor_id = UserEmployee.user_id'
                    )
                ),
                array('table' => 'employees',
                    'alias' => 'Employee',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Employee.id = UserEmployee.employee_id'
                    )
                )
        )));
        $this->set(compact('patient'));
    }

    function printCreditMemo($creditMemoId = null) {
        $this->layout = 'ajax';
        ClassRegistry::init('CreditMemo')->id = $creditMemoId;
        $this->set('patient', $this->Patient->read(null, ClassRegistry::init('CreditMemo')->field('patient_id')));
        ClassRegistry::init('Country')->id = $this->Patient->field('nationality');
        $this->set('nationality', ClassRegistry::init('Country')->field('name'));
    }

    function reportInvoice() {
        
    }

    function reportInvoiceResult() {
        $this->layout = 'ajax';
    }

    function reportInvoiceAjax($datas = null) {
        $this->layout = 'ajax';
        $datas = explode(",", $datas);
        $this->set("datas", $datas);
        $user = $this->getCurrentUser();
        $this->set('userId', $user['User']['id']);
    }

    function reportReceipt() {
        
    }

    function reportReceiptResult() {
        $this->layout = 'ajax';
    }

    function reportReceiptAjax($datas = null) {
        $this->layout = 'ajax';
        $datas = explode(",", $datas);
        $this->set("datas", $datas);
        $user = $this->getCurrentUser();
        $this->set('userId', $user['User']['id']);
    }

    function reportCreditMemo() {
        
    }

    function reportCreditMemoResult() {
        $this->layout = 'ajax';
    }

    function reportCreditMemoAjax($datas = null) {
        $this->layout = 'ajax';
        $datas = explode(",", $datas);
        $this->set("datas", $datas);
        $user = $this->getCurrentUser();
        $this->set('userId', $user['User']['id']);
    }

    function index() {
        
    }

    function ajax() {
        $this->layout = 'ajax';
    }

    function voidPayment($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        $this->Helper->saveUserActivity($user['User']['id'], 'Company', 'Delete');
        mysql_query("UPDATE `queues` SET `status`=4, `modified`='" . date("Y-m-d H:i:s") . "', `modified_by`=" . $user['User']['id'] . " WHERE `id`=" . $id . ";");
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }

}

?>