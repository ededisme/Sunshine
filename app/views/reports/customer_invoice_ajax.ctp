<?php

// Function
include('includes/function.php');
$this->element('check_access');
$allowVoidInvoice = checkAccess($user['User']['id'], 'dashboards', 'voidInvoice');
$allowConvertSI = checkAccess($user['User']['id'], $this->params['controller'], 'convertInvoice');
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array("invoices.id",
    "invoices.created",
    "invoices.invoice_code",
    "CONCAT_WS(' - ',patients.patient_code, patients.patient_name)",
    "invoices.total_amount",
    "invoices.balance",
    "CASE invoices.is_void WHEN 0 THEN 'Unvoid' WHEN 1 THEN 'Void' END",
    "CONCAT_WS('||', invoices.is_convert, queues.id)");

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "invoices.created";

/* DB table to use */
$sTable = " invoices INNER JOIN queues ON queues.id = invoices.queue_id INNER JOIN patients ON patients.id=queues.patient_id INNER JOIN invoice_details AS InvoiceDetail ON InvoiceDetail.invoice_id = invoices.id";

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
 * no need to aging below this line
 */

/*
 * Paging
 */
$sLimit = "";
if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
    $sLimit = "LIMIT " . mysql_real_escape_string($_GET['iDisplayStart']) . ", " .
            mysql_real_escape_string($_GET['iDisplayLength']);
}


/*
 * Ordering
 */
if (isset($_GET['iSortCol_0'])) {
    $sOrder = "ORDER BY  ";
    for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
        if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
            $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . " " . mysql_real_escape_string($_GET['sSortDir_' . $i]) . ", ";
        }
    }

    $sOrder = substr_replace($sOrder, "", -2);
    if ($sOrder == "ORDER BY") {
        $sOrder = "";
    }
  
    $sOrder = str_replace("invoices.created asc", "invoices.created asc, invoices.id asc", $sOrder);
    $sOrder = str_replace("invoices.created desc", "invoices.created desc, invoices.id desc", $sOrder);
}
$sGroup = " GROUP BY invoices.invoice_code";


/*
 * Filtering
 * NOTE this does not match the built-in DataTables filtering which does it
 * word by word on any field. It's possible to do here, but concerned about efficiency
 * on very large tables, and MySQL's regex functionality is very limited
 */
$sWhere = "";
if ($_GET['sSearch'] != "") {
    $sWhere = "WHERE (";
    for ($i = 0; $i < count($aColumns); $i++) {
        $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";
    }
    $sWhere = substr_replace($sWhere, "", -3);
    $sWhere .= ')';
}

/* Individual column filtering */
for ($i = 0; $i < count($aColumns); $i++) {
    if ($_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {
        if ($sWhere == "") {
            $sWhere = "WHERE ";
        } else {
            $sWhere .= " AND ";
        }
        $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch_' . $i]) . "%' ";
    }
}

/* Customize condition */
$condition = 'queues.status=3';
if ($data[1] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= '"' . dateConvert(str_replace("|||", "/", $data[1])) . '" <= DATE(invoices.created)';
}
if ($data[2] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= '"' . dateConvert(str_replace("|||", "/", $data[2])) . '" >= DATE(invoices.created)';
}
$condition != '' ? $condition .= ' AND ' : '';

if ($data[3] != '') {
    $condition .= 'invoices.is_void=' . $data[3];
} 

if ($data[4] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'company_id=' . $data[4];
}else{
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')';
}

if ($data[5] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'branch_id=' . $data[5];
}else{
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'branch_id IN (SELECT branch_id FROM user_branches WHERE user_id = '.$user['User']['id'].')';
}

if ($data[6] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'invoices.created_by=' . $data[6];
}

if ($data[7] != 'all') {
    $condition != '' ? $condition .= ' AND ' : '';
    if($data[7]==1){
        // indebted
        $condition .= 'invoices.balance>0';
    }else if($data[7]==2){
        // paid already
        $condition .= 'invoices.balance<=0';
    }
}

if ($data[8] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'patients.id=' . $data[8];
}

if ($data[9] != 'all') {
    $condition != '' ? $condition .= ' AND ' : '';
    if($data[9]==1){
        // Serivce
        $condition .= 'InvoiceDetail.type=1';
    }else if($data[9]==2){
        // Labo
        $condition .= 'InvoiceDetail.type=2';
    }else if($data[9]==3){
        // Labo
        $condition .= 'InvoiceDetail.type=3';
    }
}

if (!eregi("WHERE", $sWhere)) {
    $sWhere .= "WHERE " . $condition;
} else {
    $sWhere .= "AND " . $condition;
}
/*
 * SQL queries
 * Get data to display
 */
$sQuery = "
        SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
        FROM   $sTable
        $sWhere
        $sGroup
        $sOrder
        $sLimit
";
//echo $sQuery;

$rResult = mysql_query($sQuery) or die(mysql_error());

/* Data set length after filtering */
$sQuery = "
        SELECT FOUND_ROWS()
";
$rResultFilterTotal = mysql_query($sQuery) or die(mysql_error());
$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
$iFilteredTotal = $aResultFilterTotal[0];

/* Total data set length */
$sQuery = "
        SELECT COUNT(" . $sIndexColumn . ")
        FROM   $sTable
";
$rResultTotal = mysql_query($sQuery) or die(mysql_error());
$aResultTotal = mysql_fetch_array($rResultTotal);
$iTotal = $aResultTotal[0];


/*
 * Output
 */
$output = array(
    "sEcho" => intval($_GET['sEcho']),
    "iTotalRecords" => $iTotal,
    "iTotalDisplayRecords" => $iFilteredTotal,
    "aaData" => array()
);

$index = $_GET['iDisplayStart'];
while ($aRow = mysql_fetch_array($rResult)) {
    $row = array();
	$resultConvert = explode("||", $aRow[7]);
    for ($i = 0; $i < count($aColumns); $i++) {
        if ($i == 0) {
			 $colorCode = "#000";
            /* Special output formatting */
            if($allowConvertSI){                
                if($resultConvert[0]==1){                    
                    $colorCode = "green";
                }
                $row[] = '<input type="checkbox" name="invoice_check[]" invoice-id="'.$aRow[0].'" class="invoice_check" />';
            }else{ 
                $row[] = ++$index;
            }
        } else if ($aColumns[$i] == 'invoices.created') {
            if ($aRow[$i] != '0000-00-00') {
                $row[] = dateShort($aRow[$i]);
            } else {
                $row[] = '';
            }
        } else if ($aColumns[$i] == "CONCAT_WS(' - ',patients.patient_code, patients.patient_name)") {
            if (trim($aRow[$i]) == '') {
                $row[] = 'General Customer';
            } else {
                $row[] = $aRow[$i];
            }
        } else if ($aColumns[$i] == 'invoices.invoice_code') {
            $row[] = '<a href="" style="color:'.$colorCode.'"  class="btnPrintCustomerInvoice" rel="' . $aRow[0] . '">' . $aRow[$i] . '</a>';
        } else if ($aColumns[$i] == "CASE invoices.is_void WHEN 0 THEN 'Unvoid' WHEN 1 THEN 'Void' END") {
            $row[] = $aRow[$i];
        } else if ($i == 7) {
            
        } else if ($i == 4) {
            $row[] = number_format($aRow[$i], 2);
        } 
        else if ($aColumns[$i] == "invoices.balance") {
            if($aRow[$i]<0){
                $row[] = '0.00';
            }else{
                $row[] = number_format($aRow[$i], 2);
            }
        } 
        else if ($aColumns[$i] != '') {
            /* General output */
            $row[] = $aRow[$i];
        }
    }
    $sts = 0;
    $querySale = mysql_query("SELECT sales_orders.id FROM sales_orders WHERE sales_orders.queue_id=".$resultConvert[1]);
    if(@mysql_num_rows($querySale)){
       $resStsSale = mysql_fetch_array($querySale);
       $queryCredit = mysql_query("SELECT credit_memos.id FROM credit_memos INNER JOIN credit_memo_with_invoices cminv ON credit_memos.id=cminv.credit_memo_id WHERE cminv.status=1 AND sales_order_id=".$resStsSale[0]);
       if(@mysql_num_rows($queryCredit)){
           $sts = 1;
       }else{
           $sts = 0;
       }
    }else{
        $sts = 1;
    }
    
    $void = '';
    if ($allowVoidInvoice) { 
        $void = '<a href="" class="btnVoidRep" rel="' . $aRow[0] . '" title="' . $aRow[2] . '" sts="' . $sts . '"><img alt="Void" onmouseover="Tip(\''.ACTION_VOID.'\')" src="' . $this->webroot . 'img/action/delete.png" /></a>';
    }
    $row[] = $void ;
    $output['aaData'][] = $row;
}

echo json_encode($output);
?>