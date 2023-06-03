<?php

include('includes/function.php');

$this->element('check_access');
$allowVitalSign = checkAccess($user['User']['id'], $this->params['controller'], 'vitalSign');
$allowAddVitalSign = checkAccess($user['User']['id'], $this->params['controller'], 'addVitalSign');
$allowAddConsultation = checkAccess($user['User']['id'], 'doctors', 'consultationNurse');

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */

$aColumns = array('qd.id', 'patient_code', 'patient_name', 'sex', 'DATE_FORMAT(dob,"'.MYSQL_DATE.'")', 'CONCAT(telephone,".*",q.id,".*",qd.id,".*",p.id)', 'DATE_FORMAT(q.created,"%d/%m/%Y %H :%i :%s ")', '(SELECT emp.name FROM users As u INNER JOIN user_employees As useremployee ON useremployee.user_id = u.id INNER JOIN employees As emp ON emp.id = useremployee.employee_id WHERE (qd.status = 1 OR qd.status = 2) AND u.id = qd.doctor_id)');

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "qd.id";

/* DB table to use */
$sTable = "patients As p INNER JOIN queues As q ON p.id=q.patient_id INNER JOIN queued_doctors As qd ON qd.queue_id = q.id";

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
$condition = "p.is_active=1 AND q.status<=2 AND qd.status <= 2";

if($date!=""){
    $condition .= " AND DATE(q.created) = '" . $date."'";
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
    $explodeStr = explode(".*", $aRow[5]);
    for ($i = 0; $i < count($aColumns); $i++) {
        if ($aColumns[$i] == "qd.id") {
            /* Special output formatting */
            $row[] = ++$index;
        }  else if($i == 3){
            if ($aRow[$i] == "F") {
                $row[] = GENERAL_FEMALE;
            } else {
                $row[] = GENERAL_MALE;
            }            
        } else if($i == 5){
            $row[] = $explodeStr['0'];
        } else if ($aColumns[$i] != ' ') {
            /* General output */
           $row[] = $aRow[$i];
        }
    }
    
    /**
     * Action
     */   
    
    // check result in nurse
    $patientVitalSignId = "";
    $queryNurse = mysql_query("SELECT id FROM patient_vital_signs WHERE is_active = 1 AND queued_doctor_id = {$explodeStr[2]}");
    while ($resultResultNurse = mysql_fetch_array($queryNurse)) {
        $patientVitalSignId = $resultResultNurse['id'];
    }
    
    $row[] =
            '<a href="" class="btnCancelDoctor" queueId="' . $explodeStr[1] . '" queueDoctorId="' . $explodeStr[2] . '" patientId="' . $explodeStr[3] . '" title="' . $aRow[7] . '"><img alt="Cancel Doctor Consultation" onmouseover="Tip(\'Cancel Doctor Consultation\')"" style="width:24px; height: 24px;" src="' . $this->webroot . 'img/button/Actions-edit-delete-icon.png" /></a>&nbsp;&nbsp;&nbsp;'.
            ($allowAddConsultation ? '<a href="" class="btnConsultationNurse" rel="' . $explodeStr['1'] . '" queueDoctorId ="' . $aRow[0] . '" title="' . $aRow[$aColumns[1]] . '"><img alt="Consultation" onmouseover="Tip(\'Consultation\')"" src="' . $this->webroot . 'img/icon/nurse.png" /></a>&nbsp;&nbsp;&nbsp;' : '').
            ($allowAddVitalSign ? '<a href="" patientVitalSignId="'.$patientVitalSignId.'" class="btnVitalSign" rel="' . $explodeStr['1'] . '" queueDoctorId ="' . $aRow[0] . '" title="' . $aRow[$aColumns[1]] . '"><img alt="Add Vital Sign" onmouseover="Tip(\'Add Vital Sign\')"" src="' . $this->webroot . 'img/icon/doctor_2.png" /></a>' : '');
    
    
    $output['aaData'][] = $row;
}

echo json_encode($output);
?>