<?php
// Authentication
include("includes/function.php");
$this->element('check_access');
$allowPrint = checkAccess($user['User']['id'], $this->params['controller'], 'printPatientForm');
$allowReturnPatient=checkAccess($user['User']['id'], $this->params['controller'], 'returnPatient');
$allowView = checkAccess($user['User']['id'], $this->params['controller'], 'view');
$allowEdit = checkAccess($user['User']['id'], $this->params['controller'], 'edit');
$allowDelete = checkAccess($user['User']['id'], $this->params['controller'], 'delete');

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array('Patient.id', 'patient_code', 'patient_name', 'Patient.sex', 'DATE_FORMAT(Patient.dob,"'.MYSQL_DATE.'")', 'CONCAT_WS(".*",Patient.id,patient_name,patient_code,Patient.sex,Patient.dob,patient_group_id,Patient.email,occupation,Patient.telephone,Patient.address,location_id,Patient.nationality,PatientIpd.id,ipd_code,Employee.name,Groups.name,allergies,date_ipd)');

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "Patient.id";

/* DB table to use */
$sTable = " patients AS Patient INNER JOIN patient_ipds AS PatientIpd ON Patient.id = PatientIpd.patient_id
            INNER JOIN users AS User ON User.id = PatientIpd.doctor_id
            INNER JOIN user_employees AS UserEmployee ON User.id = UserEmployee.user_id
            INNER JOIN employees AS Employee ON Employee.id = UserEmployee.employee_id
            INNER JOIN groups AS Groups ON Groups.id = PatientIpd.group_id ";

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
$conditions = "PatientIpd.is_active=1 AND patient_code!=''";
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
while ($aRow = mysql_fetch_array($rResult)) {
    $explodeStr = explode(".*", $aRow[5]);
    
    $row = array();    
    for ($i = 0; $i < count($aColumns); $i++) {
        
        if ($aColumns[$i] == "Patient.id") {
            /* Special output formatting */
            ++$index;
            $row[] = '<input type="radio" value="'.$aRow[0].'" name="chkPatient" rel="'.$aRow[5].'" />' ;
        } else if($i == 3){
            if ($aRow[$i] == "F") {
                $row[] = GENERAL_FEMALE;
            } else {
                $row[] = GENERAL_MALE;
            }
        } else if($i == 5){
            $row[] = $explodeStr[8];
        } else if ($aColumns[$i] != ' ') {
            /* General output */
            $row[] = $aRow[$aColumns[$i]];
        }
    }   
    
    $output['aaData'][] = $row;
}

echo json_encode($output);
?>