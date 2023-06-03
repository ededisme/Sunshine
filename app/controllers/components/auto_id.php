<?php

/**
 * Description of Helper
 *
 * @author UDAYA
 */
App::import('model', 'ExtendAppModel');

class AutoIdComponent extends Object {
    
    function generateAutoLaboCode($table, $field, $len, $char, $year = 1) {
        $db = ConnectionManager::getDataSource('default');
        mysql_select_db($db->config['database']);
        $con = ' AND status>0';
        if($year == 1){
            $year =  date('y');
        }else{
            $year = "";
        }        
        $queryAutoId = mysql_query("SELECT COUNT(" . $field . ") FROM " . $table . " WHERE " . $field . " LIKE '" . $char . $year . "-%'{$con}");
        $dataAutoId = mysql_fetch_array($queryAutoId);
        return $char . $year . '-' . str_pad($dataAutoId[0] + 1, $len, '0', STR_PAD_LEFT);
    }
    
    
    function generateAutoCodePatientCondition($table, $field, $len, $char, $year = 1) {
        $db = ConnectionManager::getDataSource('default');
        mysql_select_db($db->config['database']);
        $con = '';
        if($year == 1){
            $year =  date('y');
        }else{
            $year = "";
        }        
        $queryAutoId = mysql_query("SELECT COUNT(" . $field . ") FROM " . $table . " WHERE " . $field . " LIKE '" . $year . $char . "%'{$con}");
        $dataAutoId = mysql_fetch_array($queryAutoId);
        return $year . $char . str_pad($dataAutoId[0] + 1, $len, '0', STR_PAD_LEFT);
    }
    
    function generateAutoCode($table, $field, $len, $char, $year = 1, $status = 'is_active = 1') {
        $db = ConnectionManager::getDataSource('default');
        mysql_select_db($db->config['database']);
        $con = '';
        if($year == 1){
            $year =  date('y');
        }else{
            $year = "";
        }
        if($status != ''){
            if($table == 'shifts'){
                $status = "status > 0";
            }
            $con = ' AND '.$status;
        }
        $queryAutoId = mysql_query("SELECT COUNT(" . $field . ") FROM " . $table . " WHERE " . $field . " LIKE '" . $year . $char . "%'{$con}");
        $dataAutoId = mysql_fetch_array($queryAutoId);
        return $year . $char . str_pad($dataAutoId[0] + 1, $len, '0', STR_PAD_LEFT);
    }
    
    function generateAutoCodeOrder($table, $field, $len, $char, $year = 1, $status = 'is_active = 1') {
        $db = ConnectionManager::getDataSource('default');
        mysql_select_db($db->config['database']);
        $con = '';
        if($year == 1){
            $year =  date('y');
        }else{
            $year = "";
        }
        if($status != ''){
            if($table == 'shifts'){
                $status = "status > 0";
            }
            $con = ' AND '.$status;
        }
        $queryAutoId = mysql_query("SELECT COUNT(" . $field . ") FROM " . $table . " WHERE " . $field . " LIKE '" . $year . $char . "%'{$con}");
        $dataAutoId = mysql_fetch_array($queryAutoId);
        return $year . $char . str_pad($dataAutoId[0] + 1, $len, '0', STR_PAD_LEFT);
    }
    
    
    function generateAutoCodeReceipt($table, $field, $len, $char, $year = 1) {
        $db = ConnectionManager::getDataSource('default');
        mysql_select_db($db->config['database']);
        if($year == 1){
            $year =  date('y');
        }else{
            $year = "";
        }
        $queryAutoId = mysql_query("SELECT COUNT(" . $field . ") FROM " . $table . " WHERE " . $field . " LIKE '" . $year . $char . "%'");
        $dataAutoId = mysql_fetch_array($queryAutoId);
        return $year . $char . str_pad($dataAutoId[0] + 1, $len, '0', STR_PAD_LEFT);
    }
    
    
    function generateAutoCodeInvoice($table, $field, $len, $char, $year = 1) {
        $db = ConnectionManager::getDataSource('default');
        mysql_select_db($db->config['database']);
        if($year == 1){
            $year =  date('y');
        }else{
            $year = "";
        }
        $queryAutoId = mysql_query("SELECT COUNT(" . $field . ") FROM " . $table . " WHERE " . $field . " LIKE '" . $year . $char . "%'");
        $dataAutoId = mysql_fetch_array($queryAutoId);
        return $year . $char . str_pad($dataAutoId[0] + 1, $len, '0', STR_PAD_LEFT);
    }
    
    function moduleGenerateCode($modCode, $modId, $field, $table, $con){
        $db = ConnectionManager::getDataSource('default');
        mysql_select_db($db->config['database']);
        $sqCode = mysql_query("SELECT CONCAT('".$modCode."','',LPAD(((SELECT count(tmp.".$field.") FROM `".$table."` as tmp WHERE tmp.id < ".$modId." AND ".$con." AND tmp.".$field." LIKE '".$modCode."%') + 1),7,'0'));");
        $code   = mysql_fetch_array($sqCode);
        return $code[0];
    }

}

?>