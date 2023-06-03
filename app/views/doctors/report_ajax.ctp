<?php

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array('p.id', 'DATE_FORMAT(q.created,"%d%m%Y")', 'cde', 'nme', 'sex', 'phn', 'pf.created_by');

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "p.id";

/* DB table to use */
$sTable = "queues q
                INNER JOIN patients p ON q.patient_id=p.id
                LEFT JOIN patient_histories ph ON q.id=ph.queue_id
                LEFT JOIN patient_followups pf ON q.id=pf.queue_id";

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
$condition = "p.sts=1 AND ph.is_active=1 AND (ph.created_by=" . $userId . " OR pf.created_by=" . $userId . ")";
/*
 * condition for due date
 */
switch ($datas[0]) {
    case '':
        $condition.='';
        break;
    case 'Today':
        $condition .= ' AND DATE(q.created)=CURDATE()';
        break;
    case 'This Week':
        $condition .= ' AND WEEK(q.created)=WEEK(CURDATE()) && YEAR(q.created)=YEAR(CURDATE())';
        break;
    case 'This Month':
        $condition .= ' AND MONTH(q.created)=MONTH(CURDATE()) && YEAR(q.created)=YEAR(CURDATE())';
        break;
}
/*
 * condition for date
 */
if ($datas[1] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= '"' . $datas[1] . '"<=DATE(q.created)';
}
if ($datas[2] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= '"' . $datas[2] . '">=DATE(q.created)';
}
/*
 * condition for patient type
 */
switch ($datas[3]) {
    case '':
        $condition.='';
        break;
    case 'new_patient':
        $condition .= ' AND is_new=1';
        break;
    case 'old_patient':
        $condition .= ' AND is_new=0';
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
        FROM $sTable
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
        if ($aColumns[$i] == "p.id") {
            /* Special output formatting */
            $row[] = ++$index;
        } else if ($aColumns[$i] == "pf.created_by") {
            if(is_null($aRow[$i])){
                $row[] = 'Consulation';
            }else{
                $row[] = 'Followup';
            }
        } else if ($aColumns[$i] != ' ') {
            /* General output */
            $row[] = $aRow[$i];
        }
    }
    $output['aaData'][] = $row;
}

echo json_encode($output);
?>