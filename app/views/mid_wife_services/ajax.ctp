<?php
// Authentication
include("includes/function.php");
$this->element('check_access');
$allowPrint = checkAccess($user['User']['id'], $this->params['controller'], 'printMidWifeService');
$allowView = checkAccess($user['User']['id'], $this->params['controller'], 'view');
$allowEdit = checkAccess($user['User']['id'], $this->params['controller'], 'edit');
$allowMidWifeServiceDossierMedical = checkAccess($user['User']['id'], $this->params['controller'], 'addMidWifeServiceDoctorDossierMedical');

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array('q.id', 'patient_code', 'patient_name', 'sex', 'CONCAT(telephone,".*",q.id)', 'DATE_FORMAT(osreq.created,"%d/%m/%Y %T")');

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "qd.id";

/* DB table to use */
$sTable = "mid_wife_service_requests as mwsreq INNER JOIN other_service_requests as osreq ON osreq.id=mwsreq.other_service_request_id INNER JOIN queued_doctors As qd ON osreq.queued_doctor_id=qd.id INNER JOIN queues as q On q.id=qd.queue_id INNER JOIN patients As p ON q.patient_id=p.id  ";

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
$conditions = "p.is_active=1 AND mwsreq.is_active=2 AND patient_code!='' ";
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
//debug($returnPatient);exit;
while ($aRow = mysql_fetch_array($rResult)) {
    $row = array();
    $explodeStr = explode(".*", $aRow[4]);
    for ($i = 0; $i < count($aColumns); $i++) {
        if ($aColumns[$i] == "q.id") {
            /* Special output formatting */
            $row[] = ++$index;
        } else if($i == 3){
            if ($aRow[$aColumns[$i]] == "F") {
                $row[] = GENERAL_FEMALE;
            } else {
                $row[] = GENERAL_MALE;
            }
        } else if($i == 4){
            $row[] = $explodeStr['0'];
        } else if ($aColumns[$i] != ' ') {
            /* General output */
            $row[] = $aRow[$aColumns[$i]];
        }
    } 
     
    $returns = '';      
                 
        $returns .= 
                ($allowEdit ? '<a href="" class="btnEditMidWife" rel="' . $aRow[0] . '" queueId="' . $aRow[0] . '" title="' . $aRow[$aColumns[1]] . '"><img alt="Mid Wife Story Patient" onmouseover="Tip(\''.'Mid Wife Story Patient'.'\')" src="' . $this->webroot . 'img/icon/mid-wife-story.png" /></a>&nbsp;&nbsp;&nbsp;&nbsp;' : '').
                ($allowMidWifeServiceDossierMedical ? '<a href="" class="btnMidWifeServiceDoctorDossierMedical" rel="' . $aRow[0] . '" queueId ="' . $aRow[0] . '" title="' . $aRow[$aColumns[1]] . '"><img alt="Mid Wife Dossier Medical" onmouseover="Tip(\'Mid Wife Dossier Medical\')"" src="' . $this->webroot . 'img/icon/dosier_medical.png" /></a>' : '');
//                ($allowPrint ? '<a href="" class="btnPrintMidWife" rel="' . $aRow[0] . '" title="' . $aRow[$aColumns[1]] . '"><img alt="Print" onmouseover="Tip(\'' . ACTION_PRINT . '\')" src="' . $this->webroot . 'img/action/printer.png" /></a>' : '');
    
    $row[] = $returns;
    $output['aaData'][] = $row;
}

echo json_encode($output);
?>