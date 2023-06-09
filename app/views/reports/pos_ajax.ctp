<?php

$saleOrderDate = '';

// Function
include('includes/function.php');

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array("sales_orders.id",
    "sales_orders.order_date",
    "sales_orders.so_code",
    "sales_orders.total_amount-IFNULL(sales_orders.discount,0)+IFNULL(sales_orders.total_vat,0)",
    "sales_orders.`balance`",
    "sales_order_receipts.`amount_us`",
    "sales_order_receipts.`amount_other`",
    "IFNULL(sales_order_receipts.`change`,0)",
    "IFNULL(sales_order_receipts.`change_other`,0)",
    "CONCAT_WS(users.first_name, ' ',users.last_name)",
    "currency_centers.symbol",
    "(SELECT symbol FROM currency_centers WHERE id = sales_order_receipts.currency_center_id)");

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "sales_orders.order_date";

/* DB table to use */
$sTable = " sales_orders
            LEFT JOIN sales_order_receipts ON sales_order_receipts.sales_order_id = sales_orders.id
            INNER JOIN currency_centers ON currency_centers.id = sales_orders.currency_center_id
            INNER JOIN users ON users.id = sales_orders.created_by
            LEFT JOIN customers ON customers.id = sales_orders.customer_id ";

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

    $sOrder = str_replace("sales_orders.order_date asc", "sales_orders.order_date asc, sales_orders.id asc", $sOrder);
    $sOrder = str_replace("sales_orders.order_date desc", "sales_orders.order_date desc, sales_orders.id desc", $sOrder);
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
    for ($i = 0; $i < (count($aColumns) - 2); $i++) {
        $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";
    }
    $sWhere = substr_replace($sWhere, "", -3);
    $sWhere .= ')';
}

/* Individual column filtering */
for ($i = 0; $i < (count($aColumns) - 2); $i++) {
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
$condition = 'sales_orders.is_pos=1';
if ($data[1] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= '"' . dateConvert(str_replace("|||", "/", $data[1])) . '" <= DATE(sales_orders.order_date)';
}
if ($data[2] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= '"' . dateConvert(str_replace("|||", "/", $data[2])) . '" >= DATE(sales_orders.order_date)';
}
$condition != '' ? $condition .= ' AND ' : '';
if ($data[3] == '') {
    $condition .= 'sales_orders.status > -1';
} else {
    $condition .= 'sales_orders.status=' . $data[3];
}
if ($data[4] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'sales_orders.company_id=' . $data[4];
}else{
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'sales_orders.company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')';
}
if ($data[5] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'sales_orders.branch_id=' . $data[5];
}else{
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'sales_orders.branch_id IN (SELECT branch_id FROM user_branches WHERE user_id = '.$user['User']['id'].')';
}
if ($data[6] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'sales_orders.location_group_id=' . $data[6];
}else{
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'sales_orders.location_group_id IN (SELECT location_group_id FROM user_location_groups WHERE user_id = '.$user['User']['id'].')';
}
if ($data[7] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'sales_orders.created_by=' . $data[7];
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
        } else if ($aColumns[$i] == 'sales_orders.order_date') {
            if ($aRow[$i] != '0000-00-00') {
                $row[] = dateShort($aRow[$i]);
                $saleOrderDate = $aRow[$i];
            } else {
                $row[] = '';
            }
        } else if ($i == 2) {
            $row[] = '<a href="" class="btnPrintInvoicePos" rel="' . $aRow[0] . '">' . $aRow[$i] . '</a>';
        } else if ($i == 3 || $i == 4 || $i == 5 || $i == 7) {
            $row[] = number_format($aRow[$i], 2)." ".$aRow[10];
        } else if ($i == 6 || $i == 8) {
            $row[] = number_format($aRow[$i], 2)." ".$aRow[11];
        } else if ($i == 10 || $i == 11) {    
            
        } else if ($aColumns[$i] != '') {
            /* General output */
            $row[] = $aRow[$i];
        }
    }
    $output['aaData'][] = $row;
}

echo json_encode($output);
?>