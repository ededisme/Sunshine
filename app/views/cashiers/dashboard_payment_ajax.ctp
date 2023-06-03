<?php

$this->element('check_access');
$allowCheckout = checkAccess($user['User']['id'], $this->params['controller'], 'checkout');

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array('q.id', 'patient_code', 'patient_name', 'sex', 'DATE_FORMAT(dob,"'.MYSQL_DATE.'")', 'telephone', "IFNULL((SELECT CASE consult_status WHEN 1 THEN 'OPD' WHEN 2 THEN 'IPD' END FROM patient_consultations WHERE queued_doctor_id = qd.id AND is_active = 1 ORDER BY patient_consultations.id DESC LIMIT 1), 'OPD')", '(SELECT rooms.room_name FROM rooms INNER JOIN patient_consultations ON patient_consultations.room_id = rooms.id WHERE patient_consultations.is_active = 1 AND patient_consultations.queued_doctor_id = qd.id ORDER BY patient_consultations.id DESC LIMIT 1)', 'q.created');


/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "p.id";

/* DB table to use */

$sTable = "patients p INNER JOIN queues as q ON q.patient_id=p.id INNER JOIN queued_doctors As qd ON qd.queue_id = q.id ";

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

/* Customize order */
$order = "q.id DESC";
if (!eregi("ORDER BY", $sOrder)) {
    $sOrder .= "ORDER BY " . $order;
} else {
    $sOrder .= ", " . $order;
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
$conditions = "p.is_active=1 AND q.status<=2 AND qd.id NOT IN (SELECT queued_doctor_id FROM patient_ipds INNER JOIN patient_stay_in_rooms ON patient_ipds.id = patient_stay_in_rooms.patient_ipd_id WHERE patient_ipds.is_active >= 1 AND queued_doctor_id IS NOT NULL AND patient_stay_in_rooms.room_id IS NOT NULL)";
if (!eregi("WHERE", $sWhere)) {
    $sWhere .= "WHERE " . $conditions;
} else {
    $sWhere .= "AND " . $conditions;
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
//echo $sQuery;exit;
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
        } else if($i == 3){
            if ($aRow[$aColumns[$i]] == "F") {
                $row[] = GENERAL_FEMALE;
            } else {
                $row[] = GENERAL_MALE;
            }
        } else if($i == 8){
            $row[] = date('d/m/Y H:i:s', strtotime($aRow[$i]));
        } else if ($aColumns[$i] != ' ') {
            /* General output */
            $row[] = $aRow[$i];
        }
    }
    
    $row [] = ($allowCheckout ? '<a href="" class="btnCancelPayment" rel="' . $aRow[0] . '" title="' . $aRow[$aColumns[1]] . '"><img alt="Void" style="width:24px; height: 24px; padding-right: 10px;" onmouseover="Tip(\'Cancel\')" src="' . $this->webroot . 'img/button/Actions-edit-delete-icon.png" /></a>' : '').
              ($allowCheckout ? '<a href="" class="btnCheckOut" rel="' . $aRow[0] . '" title="' . $aRow[$aColumns[1]] . '"><img alt="Aging" onmouseover="Tip(\'' . TABLE_PAY . '\')" src="' . $this->webroot . 'img/button/pay_24.png" /></a>' : '');       

    $output['aaData'][] = $row;
}

echo json_encode($output);
?>