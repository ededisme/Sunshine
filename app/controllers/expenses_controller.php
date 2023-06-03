<?php

class ExpensesController extends AppController {

    var $reference = 'Expenses';
    var $components = array('Helper');

    function index() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $this->Helper->saveUserActivity($user['User']['id'], 'Expense', 'Dashboard');
    }

    function ajax() {
        $this->layout = 'ajax';
    }

    function view($id = null) {
        $this->layout = 'ajax';
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        $this->Helper->saveUserActivity($user['User']['id'], 'Expense', 'View', $id);
        $this->data = $this->Expense->read(null, $id);
        $expenseDeatils = ClassRegistry::init('ExpenseDetail')->find('all', array('conditions' => array('ExpenseDetail.expense_id' => $id)));
        $this->set(compact("expenseDeatils"));
    }

    function add() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            if ($this->Helper->checkDouplicate('reference', 'expenses', $this->data['Expense']['reference'], 'status > 0')) {
                $this->Helper->saveUserActivity($user['User']['id'], 'Expense', 'Save Add New (Reference has existed)');
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $r = 0;
                $restCode = array();
                $dateNow  = date("Y-m-d H:i:s");
                $this->loadModel('GeneralLedger');
                $this->loadModel('GeneralLedgerDetail');
                $this->loadModel('ExpenseDetail');
                $this->loadModel('AccountType');
                // Chart Account
                $cashExpense = $this->AccountType->findById(19);
                
                $this->Expense->create();
                $this->data['Expense']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $this->data['Expense']['chart_account_id']  = $cashExpense['AccountType']['chart_account_id'];
                $this->data['Expense']['created']    = $dateNow;
                $this->data['Expense']['created_by'] = $user['User']['id'];
                $this->data['Expense']['status']     = 1;
                if ($this->Expense->save($this->data)) {
                    $expenseId = $this->Expense->id;
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($this->data['Expense'], 'expenses');
                    $restCode[$r]['modified'] = $dateNow;
                    $restCode[$r]['dbtodo']   = 'expenses';
                    $restCode[$r]['actodo']   = 'is';
                    $r++;
                    // GL
                    $this->GeneralLedger->create();
                    $this->data['GeneralLedger']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                    $this->data['GeneralLedger']['expense_id'] = $expenseId;
                    $this->data['GeneralLedger']['date']       = $this->data['Expense']['date'];
                    $this->data['GeneralLedger']['reference']  = $this->data['Expense']['reference'];
                    $this->data['GeneralLedger']['created']    = $dateNow;
                    $this->data['GeneralLedger']['created_by'] = $user['User']['id'];
                    $this->data['GeneralLedger']['is_approve'] = 1;
                    $this->data['GeneralLedger']['is_active']  = 1;
                    $this->data['GeneralLedger']['is_sys'] = 1;
                    $this->GeneralLedger->save($this->data);
                    $glId = $this->GeneralLedger->id;
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($this->data['GeneralLedger'], 'general_ledgers');
                    $restCode[$r]['modified'] = $dateNow;
                    $restCode[$r]['dbtodo']   = 'general_ledgers';
                    $restCode[$r]['actodo']   = 'is';
                    $r++;
                    // GL Detail
                    $GeneralLedgerDetail = array();
                    $this->GeneralLedgerDetail->create();
                    $GeneralLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $glId;
                    $GeneralLedgerDetail['GeneralLedgerDetail']['company_id']        = $this->data['Expense']['company_id'];
                    $GeneralLedgerDetail['GeneralLedgerDetail']['branch_id']         = $this->data['Expense']['branch_id'];
                    $GeneralLedgerDetail['GeneralLedgerDetail']['chart_account_id']  = $cashExpense['AccountType']['chart_account_id'];
                    $GeneralLedgerDetail['GeneralLedgerDetail']['type']        = 'Expense';
                    $GeneralLedgerDetail['GeneralLedgerDetail']['debit']       = 0;
                    $GeneralLedgerDetail['GeneralLedgerDetail']['credit']      = $this->data['Expense']['total_amount'];
                    $GeneralLedgerDetail['GeneralLedgerDetail']['memo']        = $this->data['Expense']['note'];
                    $GeneralLedgerDetail['GeneralLedgerDetail']['vendor_id']   = $this->data['Expense']['vendor_id'];
                    $this->GeneralLedgerDetail->save($GeneralLedgerDetail);
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($GeneralLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                    $restCode[$r]['dbtodo']   = 'general_ledger_details';
                    $restCode[$r]['actodo']   = 'is';
                    $r++;
                    for ($i = 0; $i < sizeof($_POST['chart_account_id']); $i++) {
                        // Expense Detail
                        $expenseDetail = array();
                        $this->ExpenseDetail->create();
                        $expenseDetail['ExpenseDetail']['expense_id'] = $expenseId;
                        $expenseDetail['ExpenseDetail']['chart_account_id'] = $_POST['chart_account_id'][$i];
                        $expenseDetail['ExpenseDetail']['amount'] = $_POST['amount'][$i];
                        $expenseDetail['ExpenseDetail']['note']   = $_POST['memo'][$i];
                        $this->ExpenseDetail->save($expenseDetail);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($expenseDetail['ExpenseDetail'], 'expense_details');
                        $restCode[$r]['dbtodo']   = 'expense_details';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                        // GL Detail
                        $GeneralLedgerDetail = array();
                        $this->GeneralLedgerDetail->create();
                        $GeneralLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $glId;
                        $GeneralLedgerDetail['GeneralLedgerDetail']['company_id']        = $this->data['Expense']['company_id'];
                        $GeneralLedgerDetail['GeneralLedgerDetail']['branch_id']         = $this->data['Expense']['branch_id'];
                        $GeneralLedgerDetail['GeneralLedgerDetail']['chart_account_id']  = $_POST['chart_account_id'][$i];
                        $GeneralLedgerDetail['GeneralLedgerDetail']['type']        = 'Expense';
                        $GeneralLedgerDetail['GeneralLedgerDetail']['debit']       = abs($_POST['amount'][$i]);
                        $GeneralLedgerDetail['GeneralLedgerDetail']['credit']      = 0;
                        $GeneralLedgerDetail['GeneralLedgerDetail']['memo']        = $_POST['memo'][$i];
                        $GeneralLedgerDetail['GeneralLedgerDetail']['vendor_id']   = $this->data['Expense']['vendor_id'];
                        $this->GeneralLedgerDetail->save($GeneralLedgerDetail);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($GeneralLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                        $restCode[$r]['dbtodo']   = 'general_ledger_details';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                    }
                    // Save File Send
                    $this->Helper->sendFileToSync($restCode, 0, 0);
                    // Save User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'Expense', 'Save Add New', $expenseId);
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'Expense', 'Save Add New (Error)');
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        $this->Helper->saveUserActivity($user['User']['id'], 'Expense', 'Add New');
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches = ClassRegistry::init('Branch')->find('all',
                        array(
                            'joins' => array(
                                array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))
                            ),
                            'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id'), 
                            'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $this->set(compact("companies", "branches"));
    }

    function edit($id = null) {
        $this->layout = 'ajax';
        if (!$id && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            if ($this->Helper->checkDouplicateEdit('reference', 'expenses', $id, $this->data['Expense']['reference'], 'status > 0')) {
                $this->Helper->saveUserActivity($user['User']['id'], 'Expense', 'Save Edit (Reference has existed)', $id);
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $r  = 0;
                $rb = 0;
                $restCode = array();
                $restBackCode  = array();
                $dateNow  = date("Y-m-d H:i:s");
                $this->loadModel('GeneralLedger');
                $this->loadModel('GeneralLedgerDetail');
                $this->loadModel('ExpenseDetail');
                $this->loadModel('AccountType');
                $expense = $this->Expense->read(null, $id);
                if($expense['Expense']['status'] == 1){
                    // Update Old
                    $this->Expense->updateAll(
                            array('Expense.status' => -1, 'Expense.modified_by' => $user['User']['id']),
                            array('Expense.id' => $id)
                    );
                    // Convert to REST
                    $restBackCode[$rb]['status']   = -1;
                    $restBackCode[$rb]['modified'] = $dateNow;
                    $restBackCode[$rb]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
                    $restBackCode[$rb]['dbtodo'] = 'expenses';
                    $restBackCode[$rb]['actodo'] = 'ut';
                    $restBackCode[$rb]['con']    = "sys_code = '".$expense['Expense']['sys_code']."'";
                    $rb++;
                    $this->GeneralLedger->updateAll(
                            array('GeneralLedger.is_active' => 2, 'GeneralLedger.modified_by' => $user['User']['id']),
                            array('GeneralLedger.expense_id' => $id)
                    );
                    // Convert to REST
                    $restBackCode[$rb]['is_active'] = 2;
                    $restBackCode[$rb]['modified']  = $dateNow;
                    $restBackCode[$rb]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
                    $restBackCode[$rb]['dbtodo'] = 'general_ledgers';
                    $restBackCode[$rb]['actodo'] = 'ut';
                    $restBackCode[$rb]['con']    = "expense_id = (SELECT id FROM expenses  WHERE sys_code = '".$expense['Expense']['sys_code']."' LIMIT 1)";
                    // Save File Send Delete
                    $this->Helper->sendFileToSync($restBackCode, 0, 0);
                    
                    // Chart Account
                    $cashExpense = $this->AccountType->findById(19);
                    
                    $this->Expense->create();
                    $this->data['Expense']['sys_code']    = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                    $this->data['Expense']['chart_account_id']  = $cashExpense['AccountType']['chart_account_id'];
                    $this->data['Expense']['status']      = 1;
                    $this->data['Expense']['created']     = $expense['Expense']['created'];
                    $this->data['Expense']['created_by']  = $expense['Expense']['created_by'];
                    $this->data['Expense']['modified']    = $dateNow;
                    $this->data['Expense']['modified_by'] = $user['User']['id'];
                    if ($this->Expense->save($this->data)) {
                        $expenseId = $this->Expense->id;
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($this->data['Expense'], 'expenses');
                        $restCode[$r]['dbtodo']   = 'expenses';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                        // GL
                        $this->GeneralLedger->create();
                        $this->data['GeneralLedger']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                        $this->data['GeneralLedger']['expense_id'] = $expenseId;
                        $this->data['GeneralLedger']['date']       = $this->data['Expense']['date'];
                        $this->data['GeneralLedger']['reference']  = $this->data['Expense']['reference'];
                        $this->data['GeneralLedger']['created']    = $dateNow;
                        $this->data['GeneralLedger']['created_by'] = $user['User']['id'];
                        $this->data['GeneralLedger']['is_approve'] = 1;
                        $this->data['GeneralLedger']['is_active']  = 1;
                        $this->data['GeneralLedger']['is_sys'] = 1;
                        $this->GeneralLedger->save($this->data);
                        $glId = $this->GeneralLedger->id;
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($this->data['GeneralLedger'], 'general_ledgers');
                        $restCode[$r]['modified'] = $dateNow;
                        $restCode[$r]['dbtodo']   = 'general_ledgers';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                        // GL Detail
                        $GeneralLedgerDetail = array();
                        $this->GeneralLedgerDetail->create();
                        $GeneralLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $glId;
                        $GeneralLedgerDetail['GeneralLedgerDetail']['company_id']        = $this->data['Expense']['company_id'];
                        $GeneralLedgerDetail['GeneralLedgerDetail']['branch_id']         = $this->data['Expense']['branch_id'];
                        $GeneralLedgerDetail['GeneralLedgerDetail']['chart_account_id']  = $cashExpense['AccountType']['chart_account_id'];
                        $GeneralLedgerDetail['GeneralLedgerDetail']['type']        = 'Expense';
                        $GeneralLedgerDetail['GeneralLedgerDetail']['debit']       = 0;
                        $GeneralLedgerDetail['GeneralLedgerDetail']['credit']      = $this->data['Expense']['total_amount'];
                        $GeneralLedgerDetail['GeneralLedgerDetail']['memo']        = $this->data['Expense']['note'];
                        $GeneralLedgerDetail['GeneralLedgerDetail']['vendor_id']   = $this->data['Expense']['vendor_id'];
                        $this->GeneralLedgerDetail->save($GeneralLedgerDetail);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($GeneralLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                        $restCode[$r]['dbtodo']   = 'general_ledger_details';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                        for ($i = 0; $i < sizeof($_POST['chart_account_id']); $i++) {
                            // Expense Detail
                            $expenseDetail = array();
                            $this->ExpenseDetail->create();
                            $expenseDetail['ExpenseDetail']['expense_id'] = $expenseId;
                            $expenseDetail['ExpenseDetail']['chart_account_id'] = $_POST['chart_account_id'][$i];
                            $expenseDetail['ExpenseDetail']['amount'] = $_POST['amount'][$i];
                            $expenseDetail['ExpenseDetail']['note']   = $_POST['memo'][$i];
                            $this->ExpenseDetail->save($expenseDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($expenseDetail['ExpenseDetail'], 'expense_details');
                            $restCode[$r]['dbtodo']   = 'expense_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                            // GL Detail
                            $GeneralLedgerDetail = array();
                            $this->GeneralLedgerDetail->create();
                            $GeneralLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $glId;
                            $GeneralLedgerDetail['GeneralLedgerDetail']['company_id']        = $this->data['Expense']['company_id'];
                            $GeneralLedgerDetail['GeneralLedgerDetail']['branch_id']         = $this->data['Expense']['branch_id'];
                            $GeneralLedgerDetail['GeneralLedgerDetail']['chart_account_id']  = $_POST['chart_account_id'][$i];
                            $GeneralLedgerDetail['GeneralLedgerDetail']['type']        = 'Expense';
                            $GeneralLedgerDetail['GeneralLedgerDetail']['debit']       = abs($_POST['amount'][$i]);
                            $GeneralLedgerDetail['GeneralLedgerDetail']['credit']      = 0;
                            $GeneralLedgerDetail['GeneralLedgerDetail']['memo']        = $_POST['memo'][$i];
                            $GeneralLedgerDetail['GeneralLedgerDetail']['vendor_id']   = $this->data['Expense']['vendor_id'];
                            $this->GeneralLedgerDetail->save($GeneralLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($GeneralLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                        }
                        // Save File Send
                        $this->Helper->sendFileToSync($restCode, 0, 0);
                        // Save User Activity
                        $this->Helper->saveUserActivity($user['User']['id'], 'Expense', 'Save Edit', $id);
                        echo MESSAGE_DATA_HAS_BEEN_SAVED;
                        exit;
                    } else {
                        $this->Helper->saveUserActivity($user['User']['id'], 'Expense', 'Save Edit (Error)', $id);
                        echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                        exit;
                    }
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'Expense', 'Save Edit (Error)', $id);
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        $this->Helper->saveUserActivity($user['User']['id'], 'Expense', 'Edit', $id);
        $this->data = $this->Expense->read(null, $id);
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches = ClassRegistry::init('Branch')->find('all',
                        array(
                            'joins' => array(
                                array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))
                            ),
                            'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id'), 
                            'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $expenseDeatils = ClassRegistry::init('ExpenseDetail')->find('all', array('conditions' => array('ExpenseDetail.expense_id' => $id)));
        $this->set(compact("companies", "branches", "expenseDeatils"));
    }

    function delete($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $r  = 0;
        $restCode = array();
        $user = $this->getCurrentUser();
        $dateNow = date("Y-m-d H:i:s");
        $this->loadModel('GeneralLedger');
        $expense = $this->Expense->read(null, $id);
        mysql_query("UPDATE `expenses` SET `status`=0, `modified`='".$dateNow."', `modified_by`=".$user['User']['id']." WHERE `id`=".$id.";");
        // Convert to REST
        $restCode[$r]['status']   = 0;
        $restCode[$r]['modified'] = $dateNow;
        $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
        $restCode[$r]['dbtodo'] = 'expenses';
        $restCode[$r]['actodo'] = 'ut';
        $restCode[$r]['con']    = "sys_code = '".$expense['Expense']['sys_code']."'";
        $r++;
        $this->GeneralLedger->updateAll(
                array('GeneralLedger.is_active' => 2, 'GeneralLedger.modified_by' => $user['User']['id']),
                array('GeneralLedger.expense_id' => $id)
        );
        // Convert to REST
        $restCode[$r]['is_active'] = 2;
        $restCode[$r]['modified']  = $dateNow;
        $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
        $restCode[$r]['dbtodo'] = 'general_ledgers';
        $restCode[$r]['actodo'] = 'ut';
        $restCode[$r]['con']    = "expense_id = (SELECT id FROM expenses  WHERE sys_code = '".$expense['Expense']['sys_code']."' LIMIT 1)";
        // Save File Send Delete
        $this->Helper->sendFileToSync($restCode, 0, 0);
        // Save User Activity
        $this->Helper->saveUserActivity($user['User']['id'], 'Expense', 'Delete', $id);
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }
    
    function customer($companyId = null) {
        $this->layout = 'ajax';
        $this->set('companyId', $companyId);
    }

    function customerAjax($companyId = null, $group = null) {
        $this->layout = 'ajax';
        $this->set('companyId', $companyId);
        $this->set('group', $group);
    }

    function vendor($companyId = null) {
        $this->layout = "ajax";
        $this->set('companyId', $companyId);
    }

    function vendorAjax($companyId = null) {
        $this->layout = "ajax";
        $this->set('companyId', $companyId);
    }

}

?>