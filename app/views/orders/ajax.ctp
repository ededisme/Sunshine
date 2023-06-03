<?php
// Authentication
$this->element('check_access');
$allowView = checkAccess($user['User']['id'], $this->params['controller'], 'view');
$allowEdit = checkAccess($user['User']['id'], $this->params['controller'], 'edit');
$allowPrint = checkAccess($user['User']['id'], $this->params['controller'], 'printInvoice');
$allowVoid = checkAccess($user['User']['id'], $this->params['controller'], 'delete');
$allowViewByUser = checkAccess($user['User']['id'], $this->params['controller'], 'viewByUser');
$allowApprove = checkAccess($user['User']['id'], $this->params['controller'], 'approve');
$allowClose = checkAccess($user['User']['id'], $this->params['controller'], 'close');
$allowOpen = checkAccess($user['User']['id'], $this->params['controller'], 'open');
// Function
include('includes/function.php');

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array("orders.id", 
    "orders.order_date",
    "orders.order_code",
    "CONCAT_WS(' - ',patients.patient_code, patients.patient_name)",    
    "CASE patients.sex WHEN 'F' THEN '".GENERAL_FEMALE."' WHEN 'M' THEN '".GENERAL_MALE."' END",
    'DATE_FORMAT(patients.dob,"'.MYSQL_DATE.'")',
    "patients.telephone",
    "CASE orders.prescription_type WHEN 0 THEN '".TABLE_HOME_MEDICAL."' WHEN 1 THEN '".TABLE_HOSPITAL_MEDICAL."' END",
    "orders.status");

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "orders.id";

/* DB table to use */
//$sTable = "orders INNER JOIN customers ON customers.id = orders.customer_id INNER JOIN currency_centers ON currency_centers.id = orders.currency_center_id";
$sTable = "orders INNER JOIN queues ON queues.id = orders.queue_id INNER JOIN patients ON patients.id = queues.patient_id INNER JOIN currency_centers ON currency_centers.id = orders.currency_center_id";

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
            if($aColumns[intval($_GET['iSortCol_' . $i])] == "orders.id"){
                $sOrder .= "orders.created " . mysql_real_escape_string($_GET['sSortDir_' . $i]) . ", ";
            } else {
                $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . " " . mysql_real_escape_string($_GET['sSortDir_' . $i]) . ", ";
            }
        }
    }

    $sOrder = substr_replace($sOrder, "", -2);
    if ($sOrder == "ORDER BY") {
        $sOrder = "";
    }
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
    for ($i = 0; $i < (count($aColumns) - 5); $i++) {
        $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";
    }
    $sWhere = substr_replace($sWhere, "", -3);
    $sWhere .= ')';
}

/* Individual column filtering */
for ($i = 0; $i < (count($aColumns) - 5); $i++) {
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
$condition = "orders.status >= 0 AND orders.company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id'].") AND orders.branch_id IN (SELECT branch_id FROM user_branches WHERE user_id = ".$user['User']['id'].")";
if($allowViewByUser){
    $condition .= " AND orders.created_by =".$user['User']['id'];
}

if($customer != 'all'){
    $condition .= " AND orders.patient_id =".$customer;
}

//if($status != 'all'){
//    $condition .= " AND orders.is_close = ".$status;
//}

//if($approve != 'all'){
//    $condition .= " AND orders.is_approve = ".$approve;
//}


if($date != ''){
    $condition .= " AND orders.order_date = '".$date."'";
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
//    print_r($aRow);
//    exit();
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        if ($i == 0) {
            /* Special output formatting */
            $row[] = ++$index;
        } else if ($aColumns[$i] == "orders.order_date") {
            if ($aRow[$i] != '0000-00-00') {
                $row[] = dateShort($aRow[$i]);
            } else {
                $row[] = '';
            }
        } else if ($i == 8) {
        } else if ($aColumns[$i] != '') {
            /* General output */
            $row[] = $aRow[$i];
        }
    }
    
    $row[] =
            ($allowView ? '<a href="#" class="btnViewOrder" rel="' . $aRow[0] . '" name="' . $aRow[2] . '"><img alt="View" onmouseover="Tip(\'' . ACTION_VIEW . '\')" src="' . $this->webroot . 'img/button/view.png" /></a> &nbsp;' : '') .
            ($allowPrint && $aRow[8] > 0 ? '<a href="#" class="btnPrintInvoiceOrder" rel="' . $aRow[0] . '"><img alt="Print" onmouseover="Tip(\'' . ACTION_PRINT_PRESCRIPTION . '\')" src="' . $this->webroot . 'img/button/printer.png" /></a> &nbsp;' : '') .
            ($allowEdit && $aRow[8] == 1 ? '<a href="#" class="btnEditOrder" rel="' . $aRow[0] . '"><img alt="Edit" onmouseover="Tip(\'' . ACTION_EDIT . '\')" src="' . $this->webroot . 'img/action/edit.png" /></a> &nbsp;' : '') .
            ($allowVoid && $aRow[8] == 1 ? '<a href="#" class="btnVoidOrder" rel="' . $aRow[0] . '" name="' . $aRow[2] . '"><img alt="Void" onmouseover="Tip(\'Void\')" src="' . $this->webroot . 'img/button/delete.png" /></a>' : '');
    $output['aaData'][] = $row;
}

echo json_encode($output);
?>