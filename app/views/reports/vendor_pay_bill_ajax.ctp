<?php

// Authentication
$this->element('check_access');
$allowViewAll = checkAccess($user['User']['id'], 'general_ledgers', 'viewAll');

// Function
include('includes/function.php');

/**
 * export to excel
 */
$filename="public/report/vendor_pay_bill_" . $user['User']['id'] . ".csv";
$fp=fopen($filename,"wb");
$excelContent = MENU_REPORT_PAY_BILLS . "\n\n";
if($data[1]!='') {
    $excelContent .= REPORT_FROM . ': ' . str_replace('|||','/',$data[1]);
}
if($data[2]!='') {
    $excelContent .= ' '.REPORT_TO . ': ' . str_replace('|||','/',$data[2]);
}
$excelContent .= "\n\n".TABLE_NO."\t".TABLE_TYPE."\t".TABLE_DATE."\t".TABLE_COMPANY."\t".TABLE_REFERENCE."\t".TABLE_ACCOUNT."\t".GENERAL_AMOUNT;

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array(
    'gl.id',
    'vendors.name',
    'type',
    'date',
    'companies.name',
    'reference',
    'IF(debit>0,debit,credit*-1)',
    '(SELECT symbol FROM currency_centers WHERE id = (SELECT currency_center_id FROM companies WHERE id = companies.id))');

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "gl.id";

/* DB table to use */
$sTable = "general_ledgers gl INNER JOIN general_ledger_details gld ON gl.id=gld.general_ledger_id INNER JOIN companies ON companies.id = gld.company_id INNER JOIN vendors ON vendors.id = gld.vendor_id";

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
            $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . "
                                " . mysql_real_escape_string($_GET['sSortDir_' . $i]) . ", ";
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
    for ($i = 0; $i < count($aColumns) - 2; $i++) {
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
$condition .= " AND gld.chart_account_id IN (SELECT id FROM chart_accounts WHERE chart_account_type_id=6)";
$condition .= " AND type='Pay Bill'";
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
    if ($data[3] != 0) {
        $condition .= 'gld.company_id=' . $data[3];
    } else {
        $condition .= 'gld.company_id IS NULL';
    }
}else{
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'gld.company_id IN (SELECT company_id FROM user_companies WHERE user_id ='.$user['User']['id'].')';
}
if ($data[4] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'gld.branch_id=' . $data[4];
}else{
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'gld.branch_id IN (SELECT branch_id FROM user_branches WHERE user_id ='.$user['User']['id'].')';
}
if ($data[5] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'gld.vendor_id=' . $data[5];
}
if ($data[6] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'gld.class_id=' . $data[6];
}
if ($data[7] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'gl.is_adj=' . $data[7];
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
$amount = 0;
$amountTotal = 0;
$symbol = "";
while ($aRow = mysql_fetch_array($rResult)) {
    $symbol = $aRow[7];
    if ($index != 0 && $aRow[1] != $tmpId) {
        $index = 0;
        $rowTotal = array();
        $rowTotal[] = '<b class="colspanParent">Total ' . $tmpName . '</b>';
        $excelContent .= "\n" . "Total " . $tmpName;
        for ($i = 0; $i < count($aColumns) - 4; $i++) {
            $rowTotal[] = '';
            $excelContent .= "\t";
        }
        $rowTotal[] = '<b>' . number_format($amount, 2) ." ".$symbol. '</b>';
        $excelContent .= "\t" . number_format($amount, 2) ." ".$symbol;
        $output['aaData'][] = $rowTotal;
    }
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        if ($i == 0) {
            /* Special output formatting */
            if ($aRow[1] == $tmpId) {
                $row[] = '<input type="hidden" value="' . $aRow[0] . '" class="link2gl" /><b>' . ++$index . '</b>';
                $excelContent .= "\n" . $index;
            } else {
                if (!is_null($aRow[1])) {
                    $tmpName = $aRow[1];
                } else {
                    $tmpName = 'General Vendor';
                }
                $row[] = '<b class="colspanParent">' . $tmpName . '</b>';
                $excelContent .= "\n" . $tmpName;
                for ($j = 0; $j < count($aColumns) - 2; $j++) {
                    $row[] = '';
                }
                $output['aaData'][] = $row;
                $row = array();
                $row[] = '<input type="hidden" value="' . $aRow[0] . '" class="link2gl" /><b>' . ++$index . '</b>';
                $excelContent .= "\n" . $index;
            }
        } else if ($i == 1) {
            
        } else if ($aColumns[$i] == 'date') {
            if ($aRow[$i] != '0000-00-00') {
                $row[] = dateShort($aRow[$i]);
                $excelContent .= "\t" . dateShort($aRow[$i]);
            } else {
                $row[] = '';
                $excelContent .= "\t";
            }
        } else if ($aColumns[$i] == 'IF(debit>0,debit,credit*-1)') {
            $row[] = number_format($aRow[$i], 2)." ".$symbol;
            $excelContent .= "\t" . number_format($aRow[$i], 2)." ".$symbol;
            if ($aRow[1] == $tmpId) {
                $amount += $aRow[$i];
            } else {
                $amount = $aRow[$i];
            }
            $amountTotal += $aRow[$i];
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
    $excelContent .= "\n" . "Total " . $tmpName;
    for ($i = 0; $i < count($aColumns) - 4; $i++) {
        $rowTotal[] = '';
        $excelContent .= "\t";
    }
    $rowTotal[] = '<b>' . number_format($amount, 2) ." ".$symbol. '</b>';
    $excelContent .= "\t" . number_format($amount, 2)." ".$symbol;
    $output['aaData'][] = $rowTotal;

    $rowTotal = array();
    $rowTotal[] = '<b class="colspanParent">GRAND TOTAL</b>';
    $excelContent .= "\n" . "GRAND TOTAL";
    for ($i = 0; $i < count($aColumns) - 4; $i++) {
        $rowTotal[] = '';
        $excelContent .= "\t";
    }
    $rowTotal[] = '<b>' . number_format($amountTotal, 2) ." ".$symbol. '</b>';
    $excelContent .= "\t" . number_format($amountTotal, 2) ." ".$symbol;
    $output['aaData'][] = $rowTotal;
}

echo json_encode($output);

$excelContent = chr(255).chr(254).@mb_convert_encoding($excelContent, 'UTF-16LE', 'UTF-8');
fwrite($fp,$excelContent);
fclose($fp);
?>