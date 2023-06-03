<?php
// Get Decimal
$sqlOption = mysql_query("SELECT product_cost_decimal FROM setting_options");
$rowOption = mysql_fetch_array($sqlOption);
// Authentication
$this->element('check_access');
$allowView   = checkAccess($user['User']['id'], $this->params['controller'], 'view');
$allowPrint  = checkAccess($user['User']['id'], $this->params['controller'], 'printInvoice');
$allowEdit   = checkAccess($user['User']['id'], $this->params['controller'], 'edit');
$allowAging  = checkAccess($user['User']['id'], $this->params['controller'], 'aging');
$allowDelete = checkAccess($user['User']['id'], $this->params['controller'], 'delete');
$allowViewByUser = checkAccess($user['User']['id'], $this->params['controller'], 'viewByUser');
$allowClose = checkAccess($user['User']['id'], $this->params['controller'], 'close');

// Function
include('includes/function.php');

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array('pr.id',
    'pr.order_date',
    "CONCAT(pr.po_code,'|!|',pr.balance)",
    'loc.name',
    'v.name',
    '(pr.total_amount - IFNULL(pr.discount_amount,0) + IFNULL(pr.total_vat,0))',
    'pr.balance',
    'pr.status',
    "currency_centers.symbol",
    "pr.vendor_consignment_id");

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "pr.id";

/* DB table to use */
$sTable = "purchase_orders as pr INNER JOIN `location_groups` as loc ON pr.location_group_id = loc.id INNER JOIN `vendors` as v ON pr.vendor_id = v.id INNER JOIN currency_centers ON currency_centers.id = pr.currency_center_id";

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
 * no need to edit below this line
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

    $sOrder = str_replace("pr.order_date asc", "pr.order_date asc, pr.id asc", $sOrder);
    $sOrder = str_replace("pr.order_date desc", "pr.order_date desc, pr.id desc", $sOrder);
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
$condition = "pr.status >= 0 AND pr.company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id'].") AND pr.branch_id IN (SELECT branch_id FROM user_branches WHERE user_id = ".$user['User']['id'].") AND pr.location_group_id IN (SELECT location_group_id FROM user_location_groups WHERE user_id='" . $user['User']['id'] . "' GROUP BY location_group_id)";
if($allowViewByUser){
    $condition .= " AND pr.created_by =".$user['User']['id'];
}
if ($balance == 1) {
    $condition .= " AND pr.balance > 0";
} else if ($balance == 2) {
    $condition .= " AND pr.balance <= 0";
}

if ($date != "") {
    $condition .= " AND pr.order_date='" . $date . "'";
}

if ($vendor != "all") {
    $condition .= " AND pr.vendor_id='" . $vendor . "'";
}

if ($filterStatus != "all") {
    $condition .= " AND pr.status='" . $filterStatus . "'";
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
    $record = explode("|!|", $aRow[2]);
    for ($i = 0; $i < count($aColumns); $i++) {
        if ($i == 0) {
            /* Special output formatting */
            $row[] = ++$index;
        } else if ($i == 2) {
            $row[] = $record[0];
        } else if ($aColumns[$i] == 'pr.order_date') {
            if ($aRow[$i] != '0000-00-00') {
                $row[] = dateShort($aRow[$i]);
            } else {
                $row[] = '';
            }
        } else if ($aColumns[$i] == 'pr.fulfillment_date') {
            if ($aRow[$i] != '0000-00-00') {
                $row[] = dateShort($aRow[$i]);
            } else {
                $row[] = '';
            }
        } else if ($aColumns[$i] == 'pr.status') {
            switch ($aRow[$i]) {
                case 0:
                    $row[] = 'Void';
                    break;
                case 1:
                    $row[] = 'Issued';
                    break;
                case 2:
                    $row[] = 'Partial';
                    break;
                case 3:
                    $row[] = 'Fulfilled';
                    break;
            }
        } else if ($aColumns[$i] == '(pr.total_amount - IFNULL(pr.discount_amount,0) + IFNULL(pr.total_vat,0))') {
            $row[] = $aRow[8]." ".number_format($aRow[$i], $rowOption[0]);
        } else if ($aColumns[$i] == 'pr.balance') {
            $row[] = $aRow[8]." ".number_format($aRow[$i], $rowOption[0]);
        } else if ($aColumns[$i] == 'currency_centers.symbol' || $aColumns[$i] == 'pr.vendor_consignment_id') {
        } else if ($aColumns[$i] != ' ') {
            /* General output */
            $row[] = $aRow[$i];
        }
    }
    $queryHasReceipt  = mysql_query("SELECT id FROM pvs WHERE purchase_order_id=" . $aRow[0] . " AND is_void = 0");
    $queryHasReturn   = mysql_query("SELECT id FROM invoice_pbc_with_pbs WHERE status > 0 AND purchase_order_id=" . $aRow[0]);
    $close = false;
    if($aRow[9] != ''){
        $close = true;
    } else {
        $sqlProduct = mysql_query("SELECT id FROM purchase_order_details WHERE purchase_order_id = " . $aRow[0]);
        if(!mysql_num_rows($sqlProduct)){
            $close = true;
        }
    }
    $row[] =
            ($allowView ? '<a href="" class="btnViewPurchaseOrder" rel="' . $aRow[0] . '" name="' . $record[0] . '"><img alt="View" onmouseover="Tip(\'' . ACTION_VIEW . '\')" src="' . $this->webroot . 'img/button/view.png" /></a> ' : '') .
            ($allowPrint && $aRow[7] > 0 ? '<a href="" class="btnPrintPurchaseOrder" rel="' . $aRow[0] . '"><img alt="Print" onmouseover="Tip(\'' . ACTION_PRINT_PURCHASE_BILL . '\')" src="' . $this->webroot . 'img/button/printer.png" /></a> ' : '') .
            ($allowEdit && $aRow[7] == 3 && !mysql_num_rows($queryHasReceipt) && !mysql_num_rows($queryHasReturn) ? '<a href="" class="btnEditPurchaseOrder" rel="' . $aRow[0] . '" name="' . $record[0] . '"><img alt="Edit" onmouseover="Tip(\'' . ACTION_EDIT . '\')" src="' . $this->webroot . 'img/button/edit.png" /></a> ' : '') .
            ($allowAging && $aRow[7] > 0 ? '<a href="" class="btnAgingPurchaseOrder" rel="' . $aRow[0] . '" name="' . $record[0] . '"><img alt="Aging" onmouseover="Tip(\'' . TABLE_PAY . '\')" src="' . $this->webroot . 'img/button/aging.png" /></a> ' : '').
//            ($allowClose && $aRow[7] == 1 && $close == true ? '<a href="" class="btnClosePurchaseOrder" rel="' . $aRow[0] . '" name="' . $record[0] . '"><img alt="Close" onmouseover="Tip(\'' . TABLE_CLOSE . '\')" src="' . $this->webroot . 'img/button/close.png" /></a> ' : '').
            ($allowDelete && $aRow[7] == 3 && !mysql_num_rows($queryHasReceipt) && !mysql_num_rows($queryHasReturn) ? '<a href="" class="btnDeletePurchaseOrder" rel="' . $aRow[0] . '" name="' . $record[0] . '"><img alt="Void" onmouseover="Tip(\'' . ACTION_VOID . '\')" src="' . $this->webroot . 'img/button/delete.png" /></a>' : '');
    $output['aaData'][] = $row;
}

echo json_encode($output);
?>