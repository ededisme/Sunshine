<?php

class SettingsController extends AppController {

    var $uses = 'User';
    var $components = array('Helper');

    function index() {
        $this->layout = 'ajax';
    }

    function retainedEarnings(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            if(preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/', $this->data['Setting']['date'])){
                $this->data['Setting']['date'] = $this->Helper->dateConvert($this->data['Setting']['date']);
            }

            $this->loadModel('GeneralLedger');

            $this->GeneralLedger->create();
            $this->data['Setting']['created_by'] = $user['User']['id'];
            $this->data['Setting']['is_retained_earnings'] = 1;
            $this->data['Setting']['is_active'] = 1;
            if ($this->GeneralLedger->save($this->data['Setting'])) {
                $generalLedgerId = $this->GeneralLedger->getLastInsertId();
                /**
                 * General Ledger Detail
                 */
                $this->loadModel('GeneralLedgerDetail');

                // retained earnings
                $this->GeneralLedgerDetail->create();
                $GeneralLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $generalLedgerId;
                $GeneralLedgerDetail['GeneralLedgerDetail']['company_id'] = $this->data['Setting']['company_id'];
                $GeneralLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $this->data['Setting']['retained_earnings'];
                $GeneralLedgerDetail['GeneralLedgerDetail']['type'] = 'Closing Entries';
                if($_POST['net_profit']>0){
                    $GeneralLedgerDetail['GeneralLedgerDetail']['debit'] = 0;
                    $GeneralLedgerDetail['GeneralLedgerDetail']['credit'] = abs($_POST['net_profit']);
                }else{
                    $GeneralLedgerDetail['GeneralLedgerDetail']['debit'] = abs($_POST['net_profit']);
                    $GeneralLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                }
                $GeneralLedgerDetail['GeneralLedgerDetail']['memo'] = $this->data['Setting']['retained_earnings_memo'];
                $GeneralLedgerDetail['GeneralLedgerDetail']['class_id'] = $this->data['Setting']['retained_earnings_class_id'];
                $this->GeneralLedgerDetail->save($GeneralLedgerDetail);

                // income summary
                $this->GeneralLedgerDetail->create();
                $GeneralLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $generalLedgerId;
                $GeneralLedgerDetail['GeneralLedgerDetail']['company_id'] = $this->data['Setting']['company_id'];
                $GeneralLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $this->data['Setting']['income_summary_account'];
                $GeneralLedgerDetail['GeneralLedgerDetail']['type'] = 'Closing Entries';
                if($_POST['net_profit']>0){
                    $GeneralLedgerDetail['GeneralLedgerDetail']['debit'] = abs($_POST['net_profit']);
                    $GeneralLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                }else{
                    $GeneralLedgerDetail['GeneralLedgerDetail']['debit'] = 0;
                    $GeneralLedgerDetail['GeneralLedgerDetail']['credit'] = abs($_POST['net_profit']);
                }
                $GeneralLedgerDetail['GeneralLedgerDetail']['memo'] = $this->data['Setting']['income_summary_memo'];
                $GeneralLedgerDetail['GeneralLedgerDetail']['class_id'] = $this->data['Setting']['income_summary_class_id'];
                $this->GeneralLedgerDetail->save($GeneralLedgerDetail);

                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                exit();
            }else{
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                exit();
            }
        }
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

    function getNetIncome($date, $comapny_id){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();

        // Income CoA List
        $coAIdList = array();
        $queryCoAIdList=mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND chart_account_type_id IN (11,14)");
        while($dataCoAIdList=mysql_fetch_array($queryCoAIdList)){
            $coAIdList[]=$dataCoAIdList['id'];
        }
        $queryIncome=mysql_query("  SELECT SUM(credit)-SUM(debit)
                                    FROM general_ledgers gl INNER JOIN general_ledger_details gld ON gl.id=gld.general_ledger_id
                                    WHERE gl.is_approve=1 AND gl.is_approve=1 AND gl.is_active=1 AND company_id='" . $comapny_id . "' AND date <= '" . $date . "' AND gld.chart_account_id IN (" . implode(",", $coAIdList) . ")");
        $dataIncome=mysql_fetch_array($queryIncome);

        // COGS CoA List
        $coAIdList = array();
        $queryCoAIdList=mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND chart_account_type_id IN (12)");
        while($dataCoAIdList=mysql_fetch_array($queryCoAIdList)){
            $coAIdList[]=$dataCoAIdList['id'];
        }
        $queryCogs=mysql_query("  SELECT SUM(debit)-SUM(credit)
                                    FROM general_ledgers gl INNER JOIN general_ledger_details gld ON gl.id=gld.general_ledger_id
                                    WHERE gl.is_approve=1 AND gl.is_approve=1 AND gl.is_active=1 AND company_id='" . $comapny_id . "' AND date <= '" . $date . "' AND gld.chart_account_id IN (" . implode(",", $coAIdList) . ")");
        $dataCogs=mysql_fetch_array($queryCogs);

        // Expense CoA List
        $coAIdList = array();
        $queryCoAIdList=mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND chart_account_type_id IN (13,15)");
        while($dataCoAIdList=mysql_fetch_array($queryCoAIdList)){
            $coAIdList[]=$dataCoAIdList['id'];
        }
        $queryExpense=mysql_query(" SELECT SUM(debit)-SUM(credit)
                                    FROM general_ledgers gl INNER JOIN general_ledger_details gld ON gl.id=gld.general_ledger_id
                                    WHERE gl.is_approve=1 AND gl.is_approve=1 AND gl.is_active=1 AND company_id='" . $comapny_id . "' AND date <= '" . $date . "' AND gld.chart_account_id IN (" . implode(",", $coAIdList) . ")");
        $dataExpense=mysql_fetch_array($queryExpense);

        echo $dataIncome[0]-$dataCogs[0]-$dataExpense[0];
        exit();
    }

    function ics($productId = null) {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($_POST)) {
            $r = 0;
            $restCode  = array();
            $queryAccountType = mysql_query("SELECT id,name FROM account_types WHERE status = 1 ORDER BY ordering");
            while ($dataAccountType = mysql_fetch_array($queryAccountType)) {
                $name = "t" . $dataAccountType['id'];
                mysql_query("UPDATE account_types SET chart_account_id=" . $_POST[$name] . " WHERE id=" . $dataAccountType['id']);
                // Convert to REST
                $restCode[$r]['chart_account_id'] = $this->Helper->getSQLSyncCode("chart_accounts", $_POST[$name]);
                $restCode[$r]['dbtodo'] = 'account_types';
                $restCode[$r]['actodo'] = 'ut';
                $restCode[$r]['con']    = "id = '".$dataAccountType['id']."'";
                $r++;
            }
            // Save File Send
            $this->Helper->sendFileToSync($restCode, 0, 0);
            // Save User Activity
            $this->Helper->saveUserActivity($user['User']['id'], 'ICS', 'Save Edit');
            echo MESSAGE_DATA_HAS_BEEN_SAVED;
            exit();
        }
        // User Activity
        $this->Helper->saveUserActivity($user['User']['id'], 'ICS', 'Edit');
        $this->set('productId', $productId);
    }

    function accountClosingDate(){
        $this->layout = 'ajax';
        if (!empty($this->data)) {
            $r = 0;
            $restCode = array();
            $dateNow  = date("Y-m-d H:i:s");
            $user = $this->getCurrentUser();
            mysql_query("INSERT INTO account_closing_dates (date,created,created_by) VALUES ('" . $this->data['Setting']['date'] . "', now(), " . $user['User']['id'] . ")");
            // Convert to REST
            $restCode[$r]['date']       = $this->data['Setting']['date'];
            $restCode[$r]['created']    = $dateNow;
            $restCode[$r]['created_by'] = $this->Helper->getSQLSyncCode("users", $user['User']['id']);
            $restCode[$r]['dbtodo']     = 'account_closing_dates';
            $restCode[$r]['actodo']     = 'is';
            // Save File Send
            $this->Helper->sendFileToSync($restCode, 0, 0);
            // Save User Activity
            $this->Helper->saveUserActivity($user['User']['id'], 'Account Closing Date', 'Save Change');
            echo MESSAGE_DATA_HAS_BEEN_SAVED;
            exit();
        }
    }

}

?>