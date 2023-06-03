<?php

$isPos = false;
$status = 2;
$saleOrderDate = '';

// Function
include('includes/function.php');

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array("credit_memos.id",
    "credit_memos.order_date",
    "credit_memos.cm_code",
    "CONCAT_WS(' ', patients.patient_name)",
    "credit_memos.total_amount-IFNULL(credit_memos.discount,0)+IFNULL(credit_memos.mark_up,0)+IFNULL(credit_memos.total_vat,0)",
    "credit_memos.balance",
    "IF(balance>0 AND DATEDIFF(now(),due_date)>0,DATEDIFF(now(),due_date),'.')",
    "CASE credit_memos.status WHEN 0 THEN 'Void' WHEN 1 THEN 'Issued' WHEN 2 THEN 'Fulfilled' END",
    "currency_centers.symbol");

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "credit_memos.order_date";

/* DB table to use */
$sTable = " credit_memos
            LEFT JOIN patients ON patients.id = credit_memos.patient_id INNER JOIN currency_centers ON currency_centers.id = credit_memos.currency_center_id";

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

    $sOrder = str_replace("credit_memos.order_date asc", "credit_memos.order_date asc, credit_memos.id asc", $sOrder);
    $sOrder = str_replace("credit_memos.order_date desc", "credit_memos.order_date desc, credit_memos.id desc", $sOrder);
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
    for ($i = 0; $i < (count($aColumns) - 1); $i++) {
        $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";
    }
    $sWhere = substr_replace($sWhere, "", -3);
    $sWhere .= ')';
}

/* Individual column filtering */
for ($i = 0; $i < (count($aColumns) - 1); $i++) {
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
$condition = 'status!=-1';
if ($data[1] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= '"' . dateConvert(str_replace("|||", "/", $data[1])) . '" <= DATE(order_date)';
}
if ($data[2] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= '"' . dateConvert(str_replace("|||", "/", $data[2])) . '" >= DATE(order_date)';
}
$condition != '' ? $condition .= ' AND ' : '';
if ($data[3] == '') {
    $condition .= 'status!=0';
} else {
    $condition .= 'status=' . $data[3];
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
    $condition .= 'location_group_id=' . $data[6];
}else{
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'location_group_id IN (SELECT location_group_id FROM user_location_groups WHERE user_id = '.$user['User']['id'].')';
}
if ($data[7] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'patient_id IN (SELECT patient_id FROM customer_cgroups WHERE cgroup_id=' . $data[7] . ')';
}
if ($data[8] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'patient_id=' . $data[8];
}
if ($data[9] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'credit_memos.created_by=' . $data[9];
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
        } else if ($aColumns[$i] == 'credit_memos.order_date') {
            if ($aRow[$i] != '0000-00-00') {
                $row[] = dateShort($aRow[$i]);
                $saleOrderDate = $aRow[$i];
            } else {
                $row[] = '';
            }
        } else if ($aColumns[$i] == "CONCAT_WS(' ', patients.patient_name)") {
            if (trim($aRow[$i]) == '') {
                $row[] = 'General Customer';
            } else {
                $row[] = $aRow[$i];
            }
        } else if ($aColumns[$i] == 'credit_memos.cm_code') {
            $row[] = '<a href="' . $isPos . '" class="btnPrintCreditMemoInvoice" total="'.$aRow[4].'" balance="'.$aRow[5].'" rel="' . $aRow[0] . '">' . $aRow[$i] . '</a>';
        } else if ($aColumns[$i] == "CASE credit_memos.status WHEN 0 THEN 'Void' WHEN 1 THEN 'Issued' WHEN 2 THEN 'Fulfilled' END") {
            $row[] = $aRow[$i];
            if (trim($aRow[$i]) == "Void") {
                $status = 0;
            } else if (trim($aRow[$i]) == "Issued") {
                $status = 1;
            } else if (trim($aRow[$i]) == "Fulfilled") {
                $status = 2;
            }
        } else if ($i == 4 || $i == 5) {
            $row[] = number_format($aRow[$i], 2)." ".$aRow[8];
        } else if ($i == 8) {
        } else if ($aColumns[$i] != '') {
            /* General output */
            $row[] = $aRow[$i];
        }
    }
    $output['aaData'][] = $row;
}

echo json_encode($output);
?>