<?php

// Authentication
$this->element('check_access');
$allowViewAll = checkAccess($user['User']['id'], 'general_ledgers', 'viewAll');

// Function
include('includes/function.php');

/**
 * export to excel
 */
$filename="public/report/ledger_" . $user['User']['id'] . ".csv";
$fp=fopen($filename,"wb");
$excelContent = MENU_GENERAL_LEDGER . "\n\n";
if($data[1]!='') {
    $excelContent .= REPORT_FROM . ': ' . str_replace('|||','/',$data[1]);
}
if($data[2]!='') {
    $excelContent .= ' '.REPORT_TO . ': ' . str_replace('|||','/',$data[2]);
}
$excelContent .= "\n\n".TABLE_NO."\t".TABLE_DATE."\t".TABLE_CREATED_BY."\t".TABLE_REFERENCE."\t".TABLE_ADJUST."\t".TABLE_TYPE."\tAccount Code\t".GENERAL_DESCRIPTION."\t".TABLE_CLASS."\t".GENERAL_DEBIT."\t".GENERAL_CREDIT."\t".GENERAL_BALANCE;

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array(
    'gl.id',
    '(SELECT CONCAT(account_codes," · ",account_description) FROM chart_accounts WHERE id=gld.chart_account_id)',
    'date',
    '(SELECT CONCAT(first_name," ",last_name) FROM users WHERE id=gl.created_by)',
    'reference',
    'is_adj',
    'type',
    '(SELECT account_codes FROM chart_accounts WHERE id=gld.chart_account_id)',
    'memo',
    '(SELECT name FROM classes WHERE id=gld.class_id)',
    'debit',
    'credit',
    'is_active',
    'gld.chart_account_id');

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

    $sOrder = str_replace('(SELECT CONCAT(account_codes," · ",account_description) FROM chart_accounts WHERE id=gld.chart_account_id) asc', '(SELECT CONCAT(account_codes," · ",account_description) FROM chart_accounts WHERE id=gld.chart_account_id) asc, gl.date asc, gl.id asc', $sOrder);
    $sOrder = str_replace('(SELECT CONCAT(account_codes," · ",account_description) FROM chart_accounts WHERE id=gld.chart_account_id) desc', '(SELECT CONCAT(account_codes," · ",account_description) FROM chart_accounts WHERE id=gld.chart_account_id) desc, gl.date desc, gl.id desc', $sOrder);
    $sOrder = str_replace('ORDER BY ','ORDER BY gld.company_id,',$sOrder);
    
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
for ($i = 0; $i < count($aColumns) - 2; $i++) {
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
$condition = "gl.is_approve=1 AND gl.is_active=1";
if ($data[1] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= '"' . dateConvert(str_replace("|||", "/", $data[1])) . '" <= gl.date';
}
if ($data[2] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= '"' . dateConvert(str_replace("|||", "/", $data[2])) . '" >= gl.date';
}
if ($data[3] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'gld.chart_account_id=' . $data[3];
}
if ($data[4] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    if ($data[4] != 0) {
        $condition .= 'gld.company_id=' . $data[4];
    } else {
        $condition .= 'gld.company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')';
    }
}else{
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'gld.company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')';
}
if ($data[5] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'gld.branch_id=' . $data[5];
}else{
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'gld.branch_id IN (SELECT branch_id FROM user_branches WHERE user_id = '.$user['User']['id'].')';
}
if ($data[6] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'gld.customer_id=' . $data[6];
}
if ($data[7] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'gld.vendor_id=' . $data[7];
}
if ($data[8] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'gld.other_id=' . $data[8];
}
if ($data[9] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'gld.class_id=' . $data[9];
}
if ($data[10] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'gl.is_adj=' . $data[10];
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

$index = 0;
$tmpId = '$';
$tmpName = '';
$bbi = 0;
$amountDr = 0;
$amountCr = 0;
$amountDrTotal = 0;
$amountCrTotal = 0;
while ($aRow = mysql_fetch_array($rResult)) {
    if ($index != 0 && $aRow[1] != $tmpId) {
        $index = 0;
        $rowTotal = array();
        $rowTotal[] = '<b class="colspanParent">Total ' . $tmpName . '</b>';
        $excelContent .= "\n" . $tmpName;
        for ($i = 0; $i < count($aColumns) - 6; $i++) {
            $rowTotal[] = '';
            $excelContent .= "\t";
        }
        $rowTotal[] = '<b>' . number_format($amountDr, 2) . '</b>';
        $excelContent .= "\t" . number_format($amountDr, 2);
        $rowTotal[] = '<b>' . number_format($amountCr, 2) . '</b>';
        $excelContent .= "\t" . number_format($amountCr, 2);
        $rowTotal[] = '<b>' . number_format($bbi + $amountDr - $amountCr, 2) . '</b>';
        $excelContent .= "\t" . number_format($bbi + $amountDr - $amountCr, 2);
        $output['aaData'][] = $rowTotal;
    }
    $chart_account_id = $aRow[13];
    $queryBBI = mysql_query("SELECT IFNULL((SELECT SUM(debit) FROM general_ledgers gl INNER JOIN general_ledger_details gld ON gl.id=gld.general_ledger_id WHERE gld.chart_account_id='" . $chart_account_id . "' AND date<'" . dateConvert(str_replace("|||", "/", $data[1])) . "' AND gl.is_approve=1 AND gl.is_active=1),0)-IFNULL((SELECT SUM(credit) FROM general_ledgers gl INNER JOIN general_ledger_details gld ON gl.id=gld.general_ledger_id WHERE gld.chart_account_id='" . $chart_account_id . "' AND date<'" . dateConvert(str_replace("|||", "/", $data[1])) . "' AND gl.is_approve=1 AND gl.is_active=1),0)") or die(mysql_error());
    $dataBBI = mysql_fetch_array($queryBBI);
    $bbi = $dataBBI[0];
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        if ($i == 0) {
            /* Special output formatting */
            if ($aRow[1] == $tmpId) {
                $row[] = '<b>' . ++$index . '</b><input type="hidden" value="' . $aRow[0] . '" class="link2glGeneralLedger" />';
                $excelContent .= "\n" . $index;
            } else {
                if (!is_null($aRow[1])) {
                    $tmpName = $aRow[1];
                } else {
                    $tmpName = '';
                }
                $row[] = '<b class="colspanParent">' . $tmpName . '</b>';
                $excelContent .= "\n" . $tmpName;
                for ($j = 0; $j < count($aColumns) - 4; $j++) {
                    $row[] = '';
                    $excelContent .= "\t";
                }
                $row[] = '<b>' . number_format($bbi, 2) . '</b>';
                $excelContent .= "\t" . number_format($bbi, 2);
                $output['aaData'][] = $row;
                $row = array();
                $row[] = '<b>' . ++$index . '</b><input type="hidden" value="' . $aRow[0] . '" class="link2glGeneralLedger" />';
                $excelContent .= "\n" . $index;
            }
        } else if ($i == 1) {
            
        } else if ($aColumns[$i] == 'date') {
            if ($aRow[0] == $tmpId) {
                $row[] = '';
                $excelContent .= "\t";
            } else {
                if ($aRow[$i] != '0000-00-00') {
                    $row[] = dateShort($aRow[$i]);
                    $excelContent .= "\t" . dateShort($aRow[$i]);
                } else {
                    $row[] = '';
                    $excelContent .= "\t";
                }
            }
        } else if ($aColumns[$i] == 'created_by') {
            if ($aRow[0] == $tmpId) {
                $row[] = '';
                $excelContent .= "\t";
            } else {
                $row[] = $aRow[$i];
                $excelContent .= "\t" . trim($aRow[$i]);
            }
        } else if ($aColumns[$i] == 'reference') {
            if ($aRow[0] == $tmpId) {
                $row[] = '';
                $excelContent .= "\t";
            } else {
                $row[] = trim($aRow[$i]);
                $excelContent .= "\t" . $aRow[$i];
            }
        } else if ($aColumns[$i] == 'is_adj') {
            if ($aRow[0] == $tmpId) {
                $row[] = '';
                $excelContent .= "\t";
            } else {
                if ($aRow[$i] == 1) {
                    $row[] = '<img alt="Edit" src="' . $this->webroot . 'img/button/tick.png" />';
                    $excelContent .= "\t" . "Adjust";
                } else {
                    $row[] = '';
                    $excelContent .= "\t";
                }
            }
        } else if ($aColumns[$i] == 'type') {
            if ($aRow[0] == $tmpId) {
                $row[] = '';
                $excelContent .= "\t";
            } else {
                $row[] = $aRow[$i];
                $excelContent .= "\t" . trim($aRow[$i]);
            }
        } else if ($aColumns[$i] == 'debit') {
            $row[] = $aRow[$i] != 0 ? number_format($aRow[$i], 2) : '';
            $excelContent .= "\t" . number_format($aRow[$i], 2);
            if ($aRow[1] == $tmpId) {
                $amountDr += $aRow[$i];
            } else {
                $amountDr = $aRow[$i];
            }
            $amountDrTotal += $aRow[$i];
        } else if ($aColumns[$i] == 'credit') {
            $row[] = $aRow[$i] != 0 ? number_format($aRow[$i], 2) : '';
            $excelContent .= "\t" . number_format($aRow[$i], 2);
            if ($aRow[1] == $tmpId) {
                $amountCr += $aRow[$i];
            } else {
                $amountCr = $aRow[$i];
            }
            $amountCrTotal += $aRow[$i];
        } else if ($aColumns[$i] == 'is_active') {
            $row[] = number_format($bbi + $amountDr - $amountCr, 2);
            $excelContent .= "\t" . number_format($bbi + $amountDr - $amountCr, 2);
        } else if ($aColumns[$i] == 'gld.chart_account_id') {

        } else if ($aColumns[$i] != ' ') {
            /* General output */
            $row[] = $aRow[$i];
            $excelContent .= "\t" . trim($aRow[$i]);
        }
    }
    $output['aaData'][] = $row;
    $tmpId = $aRow[1];
}
if (mysql_num_rows($rResult)) {
    $rowTotal = array();
    $rowTotal[] = '<b class="colspanParent">Total ' . $tmpName . '</b>';
    $excelContent .= "\n" . $tmpName;
    for ($i = 0; $i < count($aColumns) - 6; $i++) {
        $rowTotal[] = '';
        $excelContent .= "\t";
    }
    $rowTotal[] = '<b>' . number_format($amountDr, 2) . '</b>';
    $excelContent .= "\t" . number_format($amountDr, 2);
    $rowTotal[] = '<b>' . number_format($amountCr, 2) . '</b>';
    $excelContent .= "\t" . number_format($amountCr, 2);
    $rowTotal[] = '<b>' . number_format($bbi + $amountDr - $amountCr, 2) . '</b>';
    $excelContent .= "\t" . number_format($bbi + $amountDr - $amountCr, 2);
    $output['aaData'][] = $rowTotal;
}

echo json_encode($output);

$excelContent = chr(255).chr(254).@mb_convert_encoding($excelContent, 'UTF-16LE', 'UTF-8');
fwrite($fp,$excelContent);
fclose($fp);
?>