<?php
// Authentication
include("includes/function.php");
$this->element('check_access');
$allowView = checkAccess($user['User']['id'], $this->params['controller'], 'view');
$allowEdit = checkAccess($user['User']['id'], $this->params['controller'], 'edit');
$allowDelete = checkAccess($user['User']['id'], $this->params['controller'], 'delete');

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */

$aColumns = array('laboItemGroup.id', '(SELECT name FROM companies WHERE id = laboItemGroup.company_id)', 'laboItemGroup.code', 'laboItemGroup.name', 'laboItemGroup.name', 'laboItemGroup.name');

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "laboItemGroup.id";

/* DB table to use */
$sTable = "labo_item_groups laboItemGroup";

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


$condition = "is_active=1";

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
    for ($i = 0; $i < count($aColumns); $i++) {
        if ($i == 0) {
            /* Special output formatting */
            $row[] = ++$index;
        } else if ($i == 4) {
            $result = "";
            $unitPrice = 0;
            $patientGroup = "";    
            $queryPrice = mysql_query("SELECT name,unit_price FROM labo_item_patient_groups
                                    INNER JOIN patient_groups ON patient_groups.id = labo_item_patient_groups.patient_group_id
                                    WHERE labo_item_patient_groups.is_active = 1 AND labo_item_patient_groups.labo_item_group_id=".$aRow[0]);
            while ($rowPrice = mysql_fetch_row($queryPrice)) {
                $unitPrice = $rowPrice[1];
                $patientGroup = $rowPrice[0];  
                $result .= $patientGroup.' = '. number_format($unitPrice, 2).'<br/>';
            }
            $row[] = $result;
        }  else if ($i == 5) {
            $result = "";
            $unitPrice = 0;
            $patientGroup = "";    
            $queryPrice = mysql_query("SELECT name,hospital_price FROM labo_item_patient_groups
                                    INNER JOIN patient_groups ON patient_groups.id = labo_item_patient_groups.patient_group_id
                                    WHERE labo_item_patient_groups.is_active = 1 AND labo_item_patient_groups.labo_item_group_id=".$aRow[0]);
            while ($rowPrice = mysql_fetch_row($queryPrice)) {
                $unitPrice = $rowPrice[1];
                $patientGroup = $rowPrice[0];  
                $result .= $patientGroup.' = '. number_format($unitPrice, 2).'<br/>';
            }
            $row[] = $result;
        } else if ($aColumns[$i] != ' ') {
            /* General output */
            $row[] = $aRow[$i];
        }
    }
    $row[] =            
            ($allowView ? '<a href="" class="btnViewLaboItemGroup" rel="' . $aRow[0] . '" title="' . $aRow[1] . '"><img alt="View" onmouseover="Tip(\''.ACTION_VIEW.'\')" src="' . $this->webroot . 'img/action/view.png" /></a>&nbsp;&nbsp;' : '') .            
            ($allowEdit ? '<a href="" class="btnEditLaboItemGroup" rel="' . $aRow[0] . '" title="' . $aRow[1] . '"><img alt="View" onmouseover="Tip(\''.ACTION_EDIT.'\')" src="' . $this->webroot . 'img/action/edit.png" /></a>&nbsp;&nbsp;' : '') .            
            ($allowDelete ? '<a href="" class="btnDeleteLaboItemGroup" rel="' . $aRow[0] . '" title="' . $aRow[1] . '"><img alt="Delete" onmouseover="Tip(\''.ACTION_DELETE.'\')" src="' . $this->webroot . 'img/action/delete.png" /></a>'  : '');
    $output['aaData'][] = $row;
}

echo json_encode($output);
?>