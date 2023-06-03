<?php
// Authentication
include("includes/function.php");
$this->element('check_access');
$allowPrint = checkAccess($user['User']['id'], $this->params['controller'], 'printLabo');
$allowView = checkAccess($user['User']['id'], $this->params['controller'], 'view');
$allowEdit = checkAccess($user['User']['id'], $this->params['controller'], 'edit');
$allowApprove = checkAccess($user['User']['id'], $this->params['controller'], 'approve');
$allowDisapprove = checkAccess($user['User']['id'], $this->params['controller'], 'disapprove');

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array('Labo.queued_id', 'number_lab', 'CONCAT(patient_code, " - ", patient_name)', 'sex', 'telephone', 'Labo.created', 'CONCAT(Labo.modified,".*",Labo.status, ".*",Labo.id, ".*", IFNULL( Labo.validate, "" ) )', 'Labo.is_validate');

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "Labo.queued_id";

/* DB table to use */
$sTable = "labos As Labo INNER JOIN queued_labos As QueuedLabo ON Labo.queued_id = QueuedLabo.id INNER JOIN queues As Queue ON Queue.id = QueuedLabo.queue_id INNER JOIN patients As Patient ON Patient.id = Queue.patient_id";

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
$conditions = "Labo.status>=2 AND Labo.status<=3 AND patient_code!=''";

if($date != ""){
    $conditions .= " AND DATE(Labo.created) = '".$date."' ";
}

if($validate != "all"){
    $conditions .= " AND Labo.is_validate = '".$validate."' ";
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
//debug($returnPatient);exit;
while ($aRow = mysql_fetch_array($rResult)) {
    $row = array();
    $explodeStr = explode(".*", $aRow[6]);
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
        } else if($i == 6){
            $row[] = $explodeStr[0];           
        } else if($i == 7){
            $actionApprove = "";
            $actionDisApprove = "";
            if($allowApprove){                
                $actionApprove = "btnApproveLabo"; 
            }
            if($allowDisapprove){                
                $actionDisApprove  = "btnDisApproveLabo";               
            }   
            // check modify by
            $resultModify = "";
            $validate = "";
            if($aRow[$i] == 1){
                $queryModify = mysql_query("SELECT CONCAT(first_name, ' ', last_name) FROM users WHERE id IN (SELECT modified_by FROM labos WHERE status>=2 AND status<=3 AND id = '".$aRow[0]."') ");
                while ($rowModify = mysql_fetch_array($queryModify)) {                    
                    if($explodeStr[3]!="0000-00-00"){
                        $validate = $explodeStr[3];
                    }
                    $resultModify = $rowModify[0];
                }
            }
            $row[] = '<img alt="' . ($aRow[$i] == 1 ? ACTION_DISAPPROVE : ACTION_APPROVE) . '" class="'.($aRow[$i] == 0 && $allowApprove ? $actionApprove : $actionDisApprove).'" onmouseover="Tip(\'' . ($aRow[$i] == 1 ? ACTION_DISAPPROVE : ACTION_APPROVE) . '\')" rel="' . $aRow[0] . '" name="' . $aRow[2] . '" style="cursor: pointer;" src="' . $this->webroot . 'img/button/' . ($aRow[$i] == 1 ? 'active' : 'inactive') . '.png" /> '.$validate.'<br/>'.$resultModify;
            
        } else if ($aColumns[$i] != ' ') {
            /* General output */
            $row[] = $aRow[$i];
        }
    }   
    
    $returns = '';      
                         
    if($explodeStr[1]==2){
        $returns .=                 
                ($allowPrint && $aRow[7] == 0 ? '<a href="" class="btnPrintLabo" rel="' . $aRow[0] . '" title="' . $aRow[$aColumns[1]] . '"><img alt="Print" onmouseover="Tip(\'' . ACTION_PRINT . '\')" src="' . $this->webroot . 'img/action/printer1.png" /></a>&nbsp;&nbsp;' : '') .
                ($allowView ? '<a href="" class="btnViewLaboList" rel="' . $aRow[0] . '" title="' . $aRow[$aColumns[1]] . '"><img alt="View" onmouseover="Tip(\'' . ACTION_VIEW . '\')" src="' . $this->webroot . 'img/action/view.png" /></a>&nbsp;&nbsp;' : '') .                
                ($allowEdit && $aRow[7] == 0 ? '<a href="" class="btnEditLaboList" rel="' . $aRow[0] . '" title="' . $aRow[$aColumns[1]] . '"><img alt="View" onmouseover="Tip(\''.ACTION_EDIT.'\')" src="' . $this->webroot . 'img/action/edit.png" /></a>' : '');
    }else{
        $returns .=                
                ($allowPrint && $aRow[7] == 0 ? '<a href="" class="btnPrintLaboAfterPrint" rel="' . $aRow[0] . '" lab="'.$explodeStr[2].'" title="' . $aRow[$aColumns[1]] . '"><img alt="Print" onmouseover="Tip(\'' . ACTION_PRINT . '\')" src="' . $this->webroot . 'img/action/printer1.png" /></a>&nbsp;&nbsp;' : '') .                
                ($allowView ? '<a href="" class="btnViewAfterPrint" rel="' . $aRow[0] . '" lab="'.$explodeStr[2].'" title="' . $aRow[$aColumns[1]] . '"><img alt="View" onmouseover="Tip(\'' . ACTION_VIEW . '\')" src="' . $this->webroot . 'img/action/view.png" /></a>&nbsp;&nbsp;' : '') .                
                ($allowEdit && $aRow[7] == 0 ? '<a href="" class="btnEditLaboListAfterPrint" lab="'.$explodeStr[2].'" rel="' . $aRow[0] . '" title="' . $aRow[$aColumns[1]] . '"><img alt="View" onmouseover="Tip(\''.ACTION_EDIT.'\')" src="' . $this->webroot . 'img/action/edit.png" /></a>' : '');                
    }    
   
    $row[] = $returns;
    $output['aaData'][] = $row;
}

echo json_encode($output);
?>