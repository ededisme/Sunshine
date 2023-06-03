<?php

// Authentication
$this->element('check_access');
$allowView = checkAccess($user['User']['id'], $this->params['controller'], 'view');
$allowEdit = checkAccess($user['User']['id'], $this->params['controller'], 'edit');
$allowDelete = checkAccess($user['User']['id'], $this->params['controller'], 'delete');
$allowViewAll = checkAccess($user['User']['id'], $this->params['controller'], 'viewAll');
$allowEditAll = checkAccess($user['User']['id'], $this->params['controller'], 'editAll');
$allowDeleteAll = checkAccess($user['User']['id'], $this->params['controller'], 'deleteAll');

// Function
include('includes/function.php');

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array(
    'gl.id',
    'date',
    'created_by',
    'reference',
    'is_adj',
    'type',
    '(SELECT CONCAT(account_codes," · ",account_description) FROM chart_accounts WHERE id=gld.chart_account_id)',
    'memo',
    '(SELECT name FROM classes WHERE id=gld.class_id)',
    'IF(debit>0,debit,credit*-1)',
    'is_sys');

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "gl.id";

/* DB table to use */
$sTable = "general_ledgers gl INNER JOIN general_ledger_details gld ON gl.id=gld.general_ledger_id";

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

    $sOrder = str_replace("date asc", "date asc, gl.id asc", $sOrder);
    $sOrder = str_replace("date desc", "date desc, gl.id desc", $sOrder);
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
$condition = "gl.is_approve=1 AND gl.is_active=1 AND gld.company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id'].") AND gld.branch_id IN (SELECT branch_id FROM user_branches WHERE user_id = ".$user['User']['id'].")";
if(!is_null($filterDateFrom) && $filterDateFrom != ""){
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= '"' . $filterDateFrom . '" <= gl.date';
}
if(!is_null($filterDateTo) && $filterDateTo != ""){
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= '"' . $filterDateTo . '" >= gl.date';
}
if(!is_null($filterStatus) && $filterStatus != 'all'){
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'gl.is_approve=' . $filterStatus;
}
if(!is_null($filterCreatedBy) && $filterCreatedBy != 'all'){
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'gl.created_by=' . $filterCreatedBy;
}
switch ($type) {
    case 'customer':
        $arrCoAIdList = array();
        $queryCoAIdList = mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND chart_account_type_id IN (SELECT id FROM chart_account_types WHERE name='Accounts Receivable')");
        while ($dataCoAIdList = mysql_fetch_array($queryCoAIdList)) {
            $arrCoAIdList[] = $dataCoAIdList['id'];
        }
        if (sizeof($arrCoAIdList) != 0) {
            $condition .= " AND gld.chart_account_id IN (" . implode(",", $arrCoAIdList) . ")";
        }
        if ($typeId != "all") {
            $condition .= " AND customer_id=" . $typeId;
        } else {
            $condition .= " AND customer_id!=''";
        }
        if ($dateType == "current") {
            //$condition .= " AND date='" . $date . "'";
            $glIdList = substr($glIdList, 0, -1);
            $arrGlIdList = str_replace("-", ",", $glIdList);
            $condition .= " AND gl.id IN (" . $arrGlIdList . ")";
        } else if ($dateType == "between") {
            //$condition .= " AND DATEDIFF('" . $date . "',date) BETWEEN " . $from . " AND " . $to;
            $glIdList = substr($glIdList, 0, -1);
            $arrGlIdList = str_replace("-", ",", $glIdList);
            $condition .= " AND gl.id IN (" . $arrGlIdList . ")";
        } else if ($dateType == "through") {
            //$condition .= " AND DATEDIFF('" . $date . "',date) > " . $through;
            $glIdList = substr($glIdList, 0, -1);
            $arrGlIdList = str_replace("-", ",", $glIdList);
            $condition .= " AND gl.id IN (" . $arrGlIdList . ")";
        } else {
            $condition .= " AND DATEDIFF('" . $date . "',date) >= 0";
        }
        break;
    case 'employee':
        $arrCoAIdList = array();
        $queryCoAIdList = mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND chart_account_type_id IN (SELECT id FROM chart_account_types WHERE name='Accounts Receivable')");
        while ($dataCoAIdList = mysql_fetch_array($queryCoAIdList)) {
            $arrCoAIdList[] = $dataCoAIdList['id'];
        }
        if (sizeof($arrCoAIdList) != 0) {
            $condition .= " AND gld.chart_account_id IN (" . implode(",", $arrCoAIdList) . ")";
        }
        if ($typeId != "all") {
            $condition .= " AND employee_id=" . $typeId;
        } else {
            $condition .= " AND employee_id!=''";
        }
        if ($dateType == "current") {
            //$condition .= " AND date='" . $date . "'";
            $glIdList = substr($glIdList, 0, -1);
            $arrGlIdList = str_replace("-", ",", $glIdList);
            $condition .= " AND gl.id IN (" . $arrGlIdList . ")";
        } else if ($dateType == "between") {
            //$condition .= " AND DATEDIFF('" . $date . "',date) BETWEEN " . $from . " AND " . $to;
            $glIdList = substr($glIdList, 0, -1);
            $arrGlIdList = str_replace("-", ",", $glIdList);
            $condition .= " AND gl.id IN (" . $arrGlIdList . ")";
        } else if ($dateType == "through") {
            //$condition .= " AND DATEDIFF('" . $date . "',date) > " . $through;
            $glIdList = substr($glIdList, 0, -1);
            $arrGlIdList = str_replace("-", ",", $glIdList);
            $condition .= " AND gl.id IN (" . $arrGlIdList . ")";
        } else {
            $condition .= " AND DATEDIFF('" . $date . "',date) >= 0";
        }
        break;
    case 'vendor':
        $arrCoAIdList = array();
        $queryCoAIdList = mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND chart_account_type_id IN (SELECT id FROM chart_account_types WHERE name='Accounts Payable')");
        while ($dataCoAIdList = mysql_fetch_array($queryCoAIdList)) {
            $arrCoAIdList[] = $dataCoAIdList['id'];
        }
        if (sizeof($arrCoAIdList) != 0) {
            $condition .= " AND gld.chart_account_id IN (" . implode(",", $arrCoAIdList) . ")";
        }
        if ($typeId != "all") {
            $condition .= " AND vendor_id=" . $typeId;
        } else {
            $condition .= " AND vendor_id!=''";
        }
        if ($dateType == "current") {
            //$condition .= " AND date='" . $date . "'";
            $glIdList = substr($glIdList, 0, -1);
            $arrGlIdList = str_replace("-", ",", $glIdList);
            $condition .= " AND gl.id IN (" . $arrGlIdList . ")";
        } else if ($dateType == "between") {
            //$condition .= " AND DATEDIFF('" . $date . "',date) BETWEEN " . $from . " AND " . $to;
            $glIdList = substr($glIdList, 0, -1);
            $arrGlIdList = str_replace("-", ",", $glIdList);
            $condition .= " AND gl.id IN (" . $arrGlIdList . ")";
        } else if ($dateType == "through") {
            //$condition .= " AND DATEDIFF('" . $date . "',date) > " . $through;
            $glIdList = substr($glIdList, 0, -1);
            $arrGlIdList = str_replace("-", ",", $glIdList);
            $condition .= " AND gl.id IN (" . $arrGlIdList . ")";
        } else {
            $condition .= " AND DATEDIFF('" . $date . "',date) >= 0";
        }
        break;
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
$tmpId = '';
$amount = 0;
while ($aRow = mysql_fetch_array($rResult)) {
    $isSys = 0;
    $isOwner = 0;
    $isCompany = 0;
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        if ($i == 0) {
            /* Special output formatting */
            if ($aRow[0] == $tmpId) {
                $row[] = '';
            } else {
                $row[] = ++$index;
            }
        } else if ($aColumns[$i] == 'date') {
            if ($aRow[0] == $tmpId) {
                $row[] = '';
            } else {
                if ($aRow[$i] != '0000-00-00') {
                    $row[] = dateShort($aRow[$i]);
                } else {
                    $row[] = '';
                }
            }
        } else if ($aColumns[$i] == 'created_by') {
            if ($aRow[$i] == $userId) {
                $isOwner = 1;
            }
            if ($aRow[0] == $tmpId) {
                $row[] = '';
            } else {
                $queryCreator = mysql_query("SELECT CONCAT(first_name,' ',last_name) FROM users WHERE id=" . $aRow[$i]);
                $dataCreator = mysql_fetch_array($queryCreator);
                $row[] = $dataCreator[0];
            }
        } else if ($aColumns[$i] == 'reference') {
            if ($aRow[0] == $tmpId) {
                $row[] = '';
            } else {
                $row[] = $aRow[$i];
            }
        } else if ($aColumns[$i] == 'is_adj') {
            if ($aRow[0] == $tmpId) {
                $row[] = '';
            } else {
                if ($aRow[$i] == 1) {
                    $row[] = '<img alt="Edit" src="' . $this->webroot . 'img/button/tick.png" />';
                } else {
                    $row[] = '';
                }
            }
        } else if ($aColumns[$i] == 'type') {
            if ($aRow[0] == $tmpId) {
                $row[] = '';
            } else {
                $row[] = $aRow[$i];
            }
        } else if ($aColumns[$i] == 'memo') {
            $row[] = mb_strlen($aRow[$i]) > 50 ? mb_substr($aRow[$i], 0, 50) . "..." : $aRow[$i];
        } else if ($aColumns[$i] == 'debit' || $aColumns[$i] == 'credit') {
            $row[] = number_format($aRow[$i], 2);
        } else if ($aColumns[$i] == 'IF(debit>0,debit,credit*-1)') {
            if ($type == 'customer' || $type == 'employee') {
                $row[] = number_format($aRow[$i], 2);
                $amount += $aRow[$i];
            } else {
                $row[] = number_format($aRow[$i] * -1, 2);
                $amount += $aRow[$i] * -1;
            }
        } else if ($aColumns[$i] == 'is_sys') {
            $isSys = $aRow[$i];
        } else if ($aColumns[$i] != ' ') {
            /* General output */
            $row[] = $aRow[$i];
        }
    }
    $row[] = number_format($amount, 2);
    if ($aRow[0] == $tmpId) {
        $row[] = '';
    } else {
        $row[] =
                ($allowView || $allowViewAll ? '<a href="" class="btnViewJournalEntry" rel="' . $aRow[0] . '" name="' . $aRow[1] . '"><img alt="View" onmouseover="Tip(\'' . ACTION_VIEW . '\')" src="' . $this->webroot . 'img/button/view.png" /></a>' : '') .
                ($allowView ? '<a href="" class="btnPrintJournalEntry" rel="' . $aRow[0] . '" type="' . $aRow[6] . '"><img alt="Print" onmouseover="Tip(\'' . ACTION_PRINT . '\')" src="' . $this->webroot . 'img/button/printer.png" /></a>' : '') .
                (($allowEdit || $allowEditAll) && ($isOwner || $allowEditAll) && $isCompany && !$isSys ? '<a href="" class="btnEditJournalEntry" rel="' . $aRow[0] . '" name="' . $aRow[1] . '"><img alt="Edit" onmouseover="Tip(\'' . ACTION_EDIT . '\')" src="' . $this->webroot . 'img/button/edit.png" /></a>' : '') .
                (($allowDelete || $allowDeleteAll) && ($isOwner || $allowDeleteAll) && $isCompany && !$isSys ? '<a href="" class="btnDeleteJournalEntry" rel="' . $aRow[0] . '" name="' . $aRow[1] . '"><img alt="Delete" onmouseover="Tip(\'' . ACTION_DELETE . '\')" src="' . $this->webroot . 'img/button/delete.png" /></a>' : '');
    }
    $output['aaData'][] = $row;
    $tmpId = $aRow[0];
}

echo json_encode($output);
?>