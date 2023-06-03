<?php

// Function
include('includes/function.php');
$this->element('check_access');
$allowVoidInvoice = checkAccess($user['User']['id'], 'dashboards', 'voidInvoice');
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
    "cin.name",
    "invoices.total_amount",
    "invoices.balance",
    "CASE invoices.is_void WHEN 0 THEN 'Unvoid' WHEN 1 THEN 'Void' END");

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "invoices.created";

/* DB table to use */
$sTable = " invoices INNER JOIN queues ON queues.id = invoices.queue_id INNER JOIN patients ON patients.id=queues.patient_id INNER JOIN company_insurances cin ON cin.id=patients.company_insurance_id ";

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
    $condition .= 'patients.id=' . $data[5];
}
if ($data[6] != 'all') {
    $condition != '' ? $condition .= ' AND ' : '';
    if($data[6]==1){
        // indebted
        $condition .= 'invoices.balance>0';
    }else if($data[6]==2){
        // paid already
        $condition .= 'invoices.balance<=0';
    }
}
if ($data[7] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'patients.company_insurance_id=' . $data[7];
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
    "iTotalDisplayRecords" => $iFilteredTotal,
    "aaData" => array()
);

$index = $_GET['iDisplayStart'];
while ($aRow = mysql_fetch_array($rResult)) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        if ($i == 0) {
            /* Special output formatting */
            $row[] = ++$index;
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
            $row[] = '<a href="" class="btnPrintCustomerInvoice" rel="' . $aRow[0] . '">' . $aRow[$i] . '</a>';
        } else if ($aColumns[$i] == "CASE invoices.is_void WHEN 0 THEN 'Unvoid' WHEN 1 THEN 'Void' END") {
            $row[] = $aRow[$i];
        } else if ($i == 5) {
            $row[] = number_format($aRow[$i], 2);
        } else if ($aColumns[$i] == "invoices.balance") {
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

echo json_encode($output);
?>