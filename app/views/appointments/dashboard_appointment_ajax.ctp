<?php
// Authentication
include("includes/function.php");
$this->element('check_access');
$allowEdit = checkAccess($user['User']['id'], $this->params['controller'], 'edit');
$allowDelete = checkAccess($user['User']['id'], $this->params['controller'], 'delete');
$allowReturnPatient=checkAccess($user['User']['id'], 'patients', 'returnPatient');

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array('a.id', 'patient_code', 'p.patient_name', 'CONCAT_WS(".*",p.sex,p.id)', 'p.telephone', 'a.app_date', 'CONCAT(DATEDIFF(a.app_date,CURDATE())," ","day(s)")', 'a.doctor_id',  'a.description');

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "a.id";

/* DB table to use */
$sTable = "appointments a INNER JOIN patients p ON a.patient_id=p.id ";

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
// show by specific doctor 
//$condition = "p.is_active=1 AND a.is_close=0 AND DATE(a.app_date) >= CURDATE() AND DATEDIFF(a.app_date,CURDATE()) <= 2  AND doctor_id=" . $userId;
// show all every doctor can see 
$condition = "p.is_active=1 AND a.is_close=0";

if ($date != "") {
    $condition .= " AND DATE( a.app_date )='" . $date . "'";
}else{
    $condition .= " AND DATE(a.app_date) >= CURDATE()";
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
        GROUP BY a.patient_id, a.app_date 
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
    $explodeStr = explode(".*", $aRow[3]);
    for ($i = 0; $i < count($aColumns); $i++) {
        if ($aColumns[$i] == "a.id") {
            /* Special output formatting */
            $row[] = ++$index;
        } else if($i == 3){
            if ($explodeStr[0] == "F") {
                $row[] = GENERAL_FEMALE;
            } else {
                $row[] = GENERAL_MALE;
            }
            
        } else if($i == 5){
            $currentDate = date('d/m/Y');
            if (date("d/m/Y", strtotime($aRow[$i])) == $currentDate) {
                $row[] = date("d/m/Y", strtotime($aRow[$i])). '<img style="padding-left: 10px;" src="'.$this->webroot.'img/button/today.png" />';
            } else {
                $row[] = date("d/m/Y", strtotime($aRow[$i]));
            }
        } else if($i == 6){
            if ($aRow[$i] < 0) {
                $row[] = $aRow[$i]. '<img style="padding-left: 10px;" src="'.$this->webroot.'img/button/back.png" />';
            } else {
                $row[] = $aRow[$i];
            }
        } else if($i == 7){            
            $row[] = getDoctor($aRow[$i]);
        }  else if ($aColumns[$i] != ' ') {
            /* General output */
            $row[] = $aRow[$i];
        }
    }
    
    $returns = '';      
    $query_consult = mysql_query('SELECT patient_id FROM queues WHERE status<3 AND created>DATE_SUB(date(NOW()),INTERVAL 1 DAY) AND patient_id=' . $explodeStr[1]);
    if (!mysql_num_rows($query_consult)) {  
        $returns = ($allowReturnPatient ? '<a href="" class="btnReturnAppoDashboard" doctor-id="'.$aRow[7].'" rel="' . $explodeStr[1] . '" title="' . PATIENT_ADD_TO_QUEUE .': '. $aRow[1] .' - '. $aRow[2] . '"><img style="width:16px;" alt="Return" onmouseover="Tip(\'' . ACTION_RETURN . '\')" src="' . $this->webroot . 'img/action/return.png" /></a>&nbsp;&nbsp;' : '');
    } 
    
    
    $row[] =        
            $returns.
            ($allowEdit ? '<a href="" class="btnEditAppointment" rel="' . $aRow[0] . '" title="' . $aRow[1] . '"><img alt="Cancel" onmouseover="Tip(\'Edit\')" src="' . $this->webroot . 'img/action/edit.png" /></a>&nbsp;&nbsp;&nbsp;' : '').
            ($allowDelete ? '<a href="" class="btnCancelAppointment" rel="' . $aRow[0] . '" title="' . $aRow[1] . '"><img alt="Cancel" onmouseover="Tip(\'Cancel\')" src="' . $this->webroot . 'img/action/delete.png" /></a>' : '');
    $output['aaData'][] = $row;
}

echo json_encode($output);
?>