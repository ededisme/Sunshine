<?php
// Authentication
include("includes/function.php");
$this->element('check_access');
$allowPrint = checkAccess($user['User']['id'], $this->params['controller'], 'printPatientIpd');
$allowView = checkAccess($user['User']['id'], $this->params['controller'], 'view');
$allowEdit = checkAccess($user['User']['id'], $this->params['controller'], 'edit');
$allowDelete = checkAccess($user['User']['id'], $this->params['controller'], 'delete');
$allowAddService = checkAccess($user['User']['id'], $this->params['controller'], 'addServices');

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array('patient_ipds.id', 'ipd_code', 'patient_code', 'patient_name', 'sex', 'DATE_FORMAT(patients.dob,"'.MYSQL_DATE.'")', 'CONCAT(telephone, ".*", patient_ipds.is_active, ".*", patients.id)', 'date_ipd', 'CONCAT(DATEDIFF(date_ipd,CURDATE())," ","day(s)")', '(SELECT room_name FROM rooms WHERE id IN (SELECT room_id FROM patient_stay_in_rooms WHERE status >= 1 AND patient_ipd_id = patient_ipds.id GROUP BY patient_ipd_id) LIMIT 1)', 'patient_ipds.doctor_id', 'IFNULL(cin.name, "Walk-in")');

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "patient_ipds.id";

/* DB table to use */
$sTable = "patient_ipds INNER JOIN patients ON patients.id = patient_ipds.patient_id LEFT JOIN company_insurances cin ON cin.id = patients.company_insurance_id";

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
$conditions = "patient_ipds.is_active!=0 AND patients.is_active = 1 AND patient_ipds.ipd_type = 1";

if($status!="all"){
    $conditions .= " AND patient_ipds.is_active = {$status}";
}

if($companyInsurance!="all"){
    if($companyInsurance!="0"){
        $conditions .= " AND patients.company_insurance_id = {$companyInsurance} ";
    }else{
        $conditions .= " AND patients.company_insurance_id IS NULL ";
    }
}

if($dateFrom!=""){
    $conditions .= " AND DATE(patient_ipds.date_ipd) >= '".$dateFrom."' "; 
}
if($dateTo!=""){
    $conditions .= " AND DATE(patient_ipds.date_ipd) <= '".$dateTo."' "; 
}

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
    $explodeStr = explode(".*", $aRow[6]);
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        if ($aColumns[$i] == "patient_ipds.id") {
            /* Special output formatting */
            $row[] = ++$index;
        } else if($i == '4'){
            if ($aRow[$i] == "F") {
                $row[] = GENERAL_FEMALE;
            } else {
                $row[] = GENERAL_MALE;
            }
        } else if($i == '7'){
            $row[] = dateShort($aRow[$i]);
        } else if($i == '6'){
            $row[] = $explodeStr[0];
        } else if($i == '10'){
            $row[] = getDoctor($aRow[$i]);
        } else if ($aColumns[$i] != ' ') {
            /* General output */
            $row[] = $aRow[$aColumns[$i]];
        }
    }   
    
    $returns = '';      
    if($explodeStr[1]==2){
        $returns .= ($allowPrint ? '<a href="" class="btnPrintPatientIPD" rel="' . $aRow[0] . '" title="' . $aRow[1] . '"><img alt="Print" style="" onmouseover="Tip(\'' . ACTION_PRINT . '\')" src="' . $this->webroot . 'img/action/printer1.png" /></a>&nbsp;&nbsp;' : '') .
                    ($allowView ? '<a href="" class="btnViewPatientIPD" rel="' . $aRow[0] . '" title="' . $aRow[1] . '"><img alt="View" onmouseover="Tip(\'' . ACTION_VIEW . '\')" src="' . $this->webroot . 'img/action/view.png" /></a>&nbsp;&nbsp;' : '');
    }else{
        $query = mysql_query("SELECT id FROM patient_ipd_service_details WHERE is_active !=0 AND patient_ipd_id = ".$aRow[0]);        
        if(mysql_num_rows($query)){
            $returns .= ($allowPrint ? '<a href="" class="btnPrintPatientIPD" rel="' . $aRow[0] . '" title="' . $aRow[1] . '"><img alt="Print" onmouseover="Tip(\'' . ACTION_PRINT . '\')" src="' . $this->webroot . 'img/action/printer1.png" /></a>&nbsp;&nbsp;' : '') .                     
                        ($allowEdit ? '<a href="" class="btnEditPatientIPD" rel="' . $aRow[0] . '" title="' . $aRow[1] . '"><img alt="Edit" onmouseover="Tip(\'' . ACTION_EDIT . '\')" src="' . $this->webroot . 'img/action/edit.png" /></a>&nbsp;&nbsp;' : '') . 
                        ($allowView ? '<a href="" class="btnViewPatientIPD" rel="' . $aRow[0] . '" title="' . $aRow[1] . '"><img alt="View" onmouseover="Tip(\'' . ACTION_VIEW . '\')" src="' . $this->webroot . 'img/action/add.png" /></a>&nbsp;&nbsp;' : '') .                          
                        ($allowAddService ? '<a href="" class="btnAddMoreService" rel="' . $aRow[0] . '" patientId="'.$explodeStr[2].'" ipdType="1" title="' . $aRow[1] . '"><img alt="Add More Service" onmouseover="Tip(\'' . ACTION_ADD . '\')" src="' . $this->webroot . 'img/action/add.png" /></a>&nbsp;&nbsp;' : '').
                        ($allowDelete ? '<a href="" class="btnLeavePatientIPD" rel="' . $aRow[0] . '" name="' . $aRow[1] . '"><img alt="Patient Leave" onmouseover="Tip(\'' . 'Patient Leave' . '\')" src="' . $this->webroot . 'img/button/approved.png" style="padding-right:5px;" /></a>' : '');
        }else{
            $returns .= ($allowPrint ? '<a href="" class="btnPrintPatientIPD" rel="' . $aRow[0] . '" title="' . $aRow[1] . '"><img alt="Print" onmouseover="Tip(\'' . ACTION_PRINT . '\')" src="' . $this->webroot . 'img/action/printer1.png" /></a>&nbsp;&nbsp;' : '') .
                        ($allowEdit ? '<a href="" class="btnEditPatientIPD" rel="' . $aRow[0] . '" title="' . $aRow[1] . '"><img alt="Edit" onmouseover="Tip(\'' . ACTION_EDIT . '\')" src="' . $this->webroot . 'img/action/edit.png" /></a>&nbsp;&nbsp;' : '') .
                        ($allowView ? '<a href="" class="btnViewPatientIPD" rel="' . $aRow[0] . '" title="' . $aRow[1] . '"><img alt="View" onmouseover="Tip(\'' . ACTION_VIEW . '\')" src="' . $this->webroot . 'img/action/add.png" /></a>&nbsp;&nbsp;' : '') .
                        ($allowAddService ? '<a href="" class="btnAddMoreService" rel="' . $aRow[0] . '" patientId="'.$explodeStr[2].'" ipdType="1" title="' . $aRow[1] . '"><img alt="Add More Service" onmouseover="Tip(\'' . ACTION_ADD . '\')" src="' . $this->webroot . 'img/action/add.png" /></a>&nbsp;&nbsp;' : '') .                        
                        ($allowDelete ? '<a href="" class="btnDeletePatientIPD" rel="' . $aRow[0] . '" title="' . $aRow[1] . '"><img alt="Delete" onmouseover="Tip(\'' . ACTION_DELETE . '\')" src="' . $this->webroot . 'img/action/delete.png" /></a>' : '');
        }
    }
    $row[] = $returns;
    $output['aaData'][] = $row;
}

echo json_encode($output);
?>