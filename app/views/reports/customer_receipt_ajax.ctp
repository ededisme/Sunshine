<?php

// Function
include('includes/function.php');

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array("invoices.id",
    "receipts.created",
    "invoices.invoice_code",
    "receipts.receipt_code",
    "CONCAT_WS(' - ',patients.patient_code, patients.patient_name)",
    "(invoices.total_amount-invoices.total_discount)",
    "receipts.total_amount_paid",
    "receipts.balance",
    "CASE receipts.is_void WHEN 0 THEN 'Unvoid' WHEN 1 THEN 'Void' END");

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "receipts.id";

/* DB table to use */
$sTable = "  receipts INNER JOIN invoices ON receipts.invoice_id=invoices.id INNER JOIN queues ON queues.id = invoices.queue_id INNER JOIN patients ON patients.id=queues.patient_id";

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

    $sOrder = str_replace("receipts.created asc", "invoices.id asc", $sOrder);
    
    $sOrder .= ", receipts.created asc";        
}



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
$condition = 'patients.is_active=1 AND queues.status=3 AND invoices.is_void=0';
$conditionPos = 'SalesOrder.is_pos=1 AND SalesOrder.status=2 AND SaleOrderRecipt.is_void=0';
$conditionDep = 'patients.is_active=1 AND deposits.is_active!=0 ';
if ($data[1] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= '"' . dateConvert(str_replace("|||", "/", $data[1])) . '" <= DATE(receipts.created)';
    $conditionPos != '' ? $conditionPos .= ' AND ' : '';
    $conditionPos .= '"' . dateConvert(str_replace("|||", "/", $data[1])) . '" <= DATE(SaleOrderRecipt.created)';
    $conditionDep != '' ? $conditionDep .= ' AND ' : '';
    $conditionDep .= '"' . dateConvert(str_replace("|||", "/", $data[1])) . '" <= DATE(deposits.created)';
}
if ($data[2] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= '"' . dateConvert(str_replace("|||", "/", $data[2])) . '" >= DATE(receipts.created)';
    $conditionPos != '' ? $conditionPos .= ' AND ' : '';
    $conditionPos .= '"' . dateConvert(str_replace("|||", "/", $data[2])) . '" >= DATE(SaleOrderRecipt.created)';
    $conditionDep != '' ? $conditionDep .= ' AND ' : '';
    $conditionDep .= '"' . dateConvert(str_replace("|||", "/", $data[2])) . '" >= DATE(deposits.created)';
}
if ($data[3] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $conditionPos != '' ? $conditionPos .= ' AND ' : '';
    $condition .= 'receipts.is_void=' . $data[3];
    $conditionPos .= 'SaleOrderRecipt.is_void=' . $data[3];
    $conditionDep .= '';
} 
if ($data[4] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'company_id=' . $data[4];
    $conditionPos != '' ? $conditionPos .= ' AND ' : '';
    $conditionPos .= 'company_id=' . $data[4];
    $conditionDep != '' ? $conditionDep .= ' AND ' : '';
    $conditionDep .= 'company_id=' . $data[4];
}else{
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')';
    $conditionPos != '' ? $conditionPos .= ' AND ' : '';
    $conditionPos .= 'company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')';
    $conditionDep != '' ? $conditionDep .= ' AND ' : '';
    $conditionDep .= 'company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')';
}

if ($data[5] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'patients.id=' . $data[5];
    $conditionPos != '' ? $conditionPos .= ' AND ' : '';
    $conditionPos .= 'patient_id=' . $data[5];
    $conditionDep != '' ? $conditionDep .= ' AND ' : '';
    $conditionDep .= 'patient_id=' . $data[5];
}
if ($data[6] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'receipts.created_by=' . $data[6];
    $conditionPos != '' ? $conditionPos .= ' AND ' : '';
    $conditionPos .= 'SaleOrderRecipt.created_by=' . $data[6];
    $conditionDep != '' ? $conditionDep .= ' AND ' : '';
    $conditionDep .= 'deposits.created_by=' . $data[6];
}

if ($data[7] != 'all') {
    $condition != '' ? $condition .= ' AND ' : '';
    if($data[7]==1){
        // indebted
        $condition .= 'receipts.balance>0 AND invoices.balance>0';
    }else if($data[7]==2){
        // paid already
        $condition .= 'receipts.balance<=0 AND invoices.balance<=0';
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
        $sOrder
        $sLimit
";

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
//    "iTotalDisplayRecords" => $iFilteredTotal,
    "aaData" => array()
);

$index = $_GET['iDisplayStart'];
$iFilteredTotal = 0;
while ($aRow = mysql_fetch_array($rResult)) {
    $iFilteredTotal++;
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        if ($i == 0) {
            /* Special output formatting */
            $row[] = ++$index;
        } else if ($aColumns[$i] == 'receipts.created') {
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
            $row[] = '<a href="" class="btnPrintCustomerInvoice" rel="' . $aRow[0] . '">' . $aRow[$i] . '</a>';
        } else if ($aColumns[$i] == 'receipts.receipt_code') {
            $row[] = '<a href="" class="btnPrintCustomerReceipt" rel="' . $aRow[0] . '">' . $aRow[$i] . '</a>';
        } else if ($aColumns[$i] == "CASE receipts.is_void WHEN 0 THEN 'Unvoid' WHEN 1 THEN 'Void' END") {
            $row[] = $aRow[$i];
        } else if ($i == 5 || $i == 6) {
            $row[] = number_format($aRow[$i], 2);
        } else if ($aColumns[$i] == "receipts.balance") {
            if($aRow[$i]<0){
                $row[] = '0.00';
            }else{
                $row[] = number_format($aRow[$i], 2);
            }
        } else if ($aColumns[$i] != '') {
            /* General output */
            $row[] = $aRow[$i];
        }
    }
    $output['aaData'][] = $row;
}
// receipt pos
$queryData = mysql_query("SELECT SalesOrder.id as salesId,SaleOrderRecipt.created as Created, SaleOrderRecipt.receipt_code,SaleOrderRecipt.amount_us as Paid,SaleOrderRecipt.balance as Balance,SalesOrder.so_code,SalesOrder.total_amount as SaleTotal,patients.patient_name,patients.patient_code,(CASE SaleOrderRecipt.is_void WHEN 0 THEN 'Unvoid' WHEN 1 THEN 'Void' END ) as stat 
        FROM sales_order_receipts SaleOrderRecipt 
        INNER JOIN sales_orders SalesOrder ON SalesOrder.id=SaleOrderRecipt.sales_order_id 
        INNER JOIN patients ON patients.id = SalesOrder.patient_id WHERE ".$conditionPos);
if(@mysql_num_rows($queryData)){
    while ($resData = mysql_fetch_array($queryData)){
        $iFilteredTotal++;
        $datePay = '';
        if ($resData['Created'] != '0000-00-00') {
            $datePay = dateShort($resData['Created']);
        } 
        $row1 = array();
        $row1[] = ++$index;
        $row1[] = $datePay;
        $row1[] = $resData['so_code'];
        $row1[] = '<a href="" class="btnPrintInvoiceReportPos" rel="' . $resData['salesId'] . '">' .$resData['receipt_code']. '</a>';
        $row1[] = $resData['patient_code'].' - '.$resData['patient_name'];
        $row1[] = $resData['SaleTotal'];
        $row1[] = $resData['Paid'];
        $row1[] = number_format($resData['Balance'],2);
        $row1[] = $resData['stat'];
        $output['aaData'][] = $row1;
    }
}
// deposits
$queryDep = mysql_query("SELECT deposits.id as deposId,deposits.deposit_code,deposits.total_amount,deposits.amount_received,patients.patient_name,patients.patient_code,deposits.created as CreateDep FROM deposits INNER JOIN patients ON patients.id=deposits.patient_id WHERE ".$conditionDep);
if(@mysql_num_rows($queryDep)){
    while ($resDep = mysql_fetch_array($queryDep)){
        $iFilteredTotal++;
        $dateDep = '';
        if ($resDep['CreateDep'] != '0000-00-00') {
            $dateDep = dateShort($resDep['CreateDep']);
        } 
        $row2 = array();
        $row2[] = ++$index;
        $row2[] = $dateDep;
        $row2[] = '';
        $row2[] = '<a href="" class="btnPrintInvoiceReportDeposite" rel="' . $resDep['deposId'] . '">' .$resDep['deposit_code']. '</a>';
        $row2[] = $resDep['patient_code'].' - '.$resDep['patient_name'];
        $row2[] = number_format($resDep['total_amount'],2);
        $row2[] = number_format($resDep['amount_received'],2);
        $row2[] = '0.00';
        $row2[] = '';
        $output['aaData'][] = $row2;
    }
}
$output["iTotalDisplayRecords"] = $iFilteredTotal;
echo json_encode($output);
?>