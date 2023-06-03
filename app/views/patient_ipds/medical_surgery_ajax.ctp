<?php
// Authentication
include("includes/function.php");
$this->element('check_access');
$allowPrint = checkAccess($user['User']['id'], $this->params['controller'], 'printPatientMedicalSurgery');
$allowView = checkAccess($user['User']['id'], $this->params['controller'], 'viewMedicalSurgery');
$allowEdit = checkAccess($user['User']['id'], $this->params['controller'], 'editMedicalSurgery');
$allowDelete = checkAccess($user['User']['id'], $this->params['controller'], 'deleteMedicalSurgery');
$allowAddService = checkAccess($user['User']['id'], $this->params['controller'], 'addServiceMedicalSurgery');

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array('id', 'ipd_code', '(SELECT patient_code FROM patients WHERE id=patient_id)', '(SELECT patient_name FROM patients WHERE id=patient_id)', '(SELECT sex FROM patients WHERE id=patient_id)', '(SELECT DATE_FORMAT(dob,"'.MYSQL_DATE.'") FROM patients WHERE id=patient_id)', 'CONCAT((SELECT telephone FROM patients WHERE id=patient_id), ".*", is_active, ".*", patient_id)');

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "id";

/* DB table to use */
$sTable = "patient_ipds";

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
$conditions = "is_active!=0 AND ipd_type = 2";
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
        if ($aColumns[$i] == "id") {
            /* Special output formatting */
            $row[] = ++$index;
        } else if($i == '4'){
            if ($aRow[$aColumns[$i]] == "F") {
                $row[] = GENERAL_FEMALE;
            } else {
                $row[] = GENERAL_MALE;
            }
            
        } else if($i == '6'){
            
            $row[] = $explodeStr[0];
            
        }else if ($aColumns[$i] != ' ') {
            /* General output */
            $row[] = $aRow[$aColumns[$i]];
        }
    }   
    
    $returns = '';      
    if($explodeStr[1]==2){
        $returns .= ($allowPrint ? '<a href="" class="btnPrintMedicalSurgery" rel="' . $aRow[$aColumns[0]] . '" title="' . $aRow[$aColumns[1]] . '"><img alt="Print" onmouseover="Tip(\'' . ACTION_PRINT . '\')" src="' . $this->webroot . 'img/action/printer1.png" /></a>&nbsp;&nbsp;' : '') .
                    ($allowView ? '<a href="" class="btnViewMedicalSurgery" rel="' . $aRow[$aColumns[0]] . '" title="' . $aRow[$aColumns[1]] . '"><img alt="View" onmouseover="Tip(\'' . ACTION_VIEW . '\')" src="' . $this->webroot . 'img/action/view.png" /></a>' : '');
    }else{
        $query = mysql_query("SELECT id FROM patient_ipd_service_details WHERE is_active !=0 AND patient_ipd_id = ".$aRow[$aColumns[0]]);        
        if(mysql_num_rows($query)){
            $returns .= ($allowPrint ? '<a href="" class="btnPrintMedicalSurgery" rel="' . $aRow[$aColumns[0]] . '" title="' . $aRow[$aColumns[1]] . '"><img alt="Print" onmouseover="Tip(\'' . ACTION_PRINT . '\')" src="' . $this->webroot . 'img/action/printer1.png" /></a>&nbsp;&nbsp;' : '') .
                        ($allowView ? '<a href="" class="btnViewMedicalSurgery" rel="' . $aRow[$aColumns[0]] . '" title="' . $aRow[$aColumns[1]] . '"><img alt="View" onmouseover="Tip(\'' . ACTION_VIEW . '\')" src="' . $this->webroot . 'img/action/view.png" /></a>&nbsp;&nbsp;' : '') .                                             
                        ($allowEdit ? '<a href="" class="btnEditMedicalSurgery" rel="' . $aRow[$aColumns[0]] . '" title="' . $aRow[$aColumns[1]] . '"><img alt="Edit" onmouseover="Tip(\'' . ACTION_EDIT . '\')" src="' . $this->webroot . 'img/action/edit.png" /></a>&nbsp;&nbsp;' : '') .                        
                        ($allowAddService ? '<a href="" class="btnAddMoreServiceMedicalSurgery" rel="' . $aRow[$aColumns[0]] . '" patientId="'.$explodeStr[2].'" ipdType="1" title="' . $aRow[$aColumns[1]] . '"><img alt="Add More Service" onmouseover="Tip(\'' . ACTION_ADD . '\')" src="' . $this->webroot . 'img/action/add.png" /></a>' : '');
        }else{            
            $returns .= ($allowPrint ? '<a href="" class="btnPrintMedicalSurgery" rel="' . $aRow[$aColumns[0]] . '" title="' . $aRow[$aColumns[1]] . '"><img alt="Print" onmouseover="Tip(\'' . ACTION_PRINT . '\')" src="' . $this->webroot . 'img/action/printer1.png" /></a>&nbsp;&nbsp;' : '') .
                        ($allowView ? '<a href="" class="btnViewMedicalSurgery" rel="' . $aRow[$aColumns[0]] . '" title="' . $aRow[$aColumns[1]] . '"><img alt="View" onmouseover="Tip(\'' . ACTION_VIEW . '\')" src="' . $this->webroot . 'img/action/view.png" /></a>&nbsp;&nbsp;' : '') .
                        ($allowEdit ? '<a href="" class="btnEditMedicalSurgery" rel="' . $aRow[$aColumns[0]] . '" title="' . $aRow[$aColumns[1]] . '"><img alt="Edit" onmouseover="Tip(\'' . ACTION_EDIT . '\')" src="' . $this->webroot . 'img/action/edit.png" /></a>&nbsp;&nbsp;' : '') .
                        ($allowAddService ? '<a href="" class="btnAddMoreServiceMedicalSurgery" rel="' . $aRow[$aColumns[0]] . '" patientId="'.$explodeStr[2].'" ipdType="1" title="' . $aRow[$aColumns[1]] . '"><img alt="Add More Service" onmouseover="Tip(\'' . ACTION_ADD . '\')" src="' . $this->webroot . 'img/action/add.png" /></a>&nbsp;&nbsp;' : '') .
                        ($allowDelete ? '<a href="" class="btnDeleteMedicalSurgery" rel="' . $aRow[$aColumns[0]] . '" title="' . $aRow[$aColumns[1]] . '"><img alt="Delete" onmouseover="Tip(\'' . ACTION_DELETE . '\')" src="' . $this->webroot . 'img/action/delete.png" /></a>' : '');
        }        
    }       
    
   
    $row[] = $returns;
    $output['aaData'][] = $row;
}

echo json_encode($output);
?>