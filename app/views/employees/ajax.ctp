<?php

// Authentication
$this->element('check_access');
$allowView = checkAccess($user['User']['id'], $this->params['controller'], 'view');
$allowEdit = checkAccess($user['User']['id'], $this->params['controller'], 'edit');
$allowDelete = checkAccess($user['User']['id'], $this->params['controller'], 'delete');
$allowStatus = checkAccess($user['User']['id'], $this->params['controller'], 'status');

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array('id', 'photo', 'employee_code', 'name', 'name_kh', 'sex', '(SELECT name FROm positions WHERE id = employees.position_id)', 'personal_number', 'is_active');

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "id";

/* DB table to use */
$sTable = "employees";

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
$conditionUser = " AND id IN (SELECT employee_id FROM employee_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))";

$condition = "(is_active=1 OR is_active = 3)".$conditionUser;
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
        } else if ($i == 1) {
            if($aRow[$i] != ''){
                $photo = "public/employee_photo/".$aRow[$i];
            }else{
                $photo = "img/button/no-images.png";
            }
            $row[] = '<img alt="" src="'.$this->webroot.$photo.'" style=" max-width: 50px; max-height: 50px;" />';
        } else if($i == 8){
            if($allowStatus){
                $row[] = '<a href="" class="btnChangeStatusEmployee" rel="' . $aRow[0] . '" name="' . $aRow[3] . '" status="' . $aRow[$i] . '"><img alt="' . ($aRow[$i] == 1 ? TABLE_ACTIVE : TABLE_INACTIVE) . '" onmouseover="Tip(\'' . ($aRow[$i] == 1 ? TABLE_ACTIVE : TABLE_INACTIVE) . '\')" src="' . $this->webroot . 'img/button/' . ($aRow[$i] == 1 ? 'active' : 'inactive') . '.png" /></a>';
            }else{
                $row[] = '<img alt="' . ($aRow[$i] == 1 ? TABLE_ACTIVE : TABLE_INACTIVE) . '" onmouseover="Tip(\'' . ($aRow[$i] == 1 ? TABLE_ACTIVE : TABLE_INACTIVE) . '\')" src="' . $this->webroot . 'img/button/' . ($aRow[$i] == 1 ? 'active' : 'inactive') . '.png" />';
            }
        } else if ($aColumns[$i] != ' ') {
            /* General output */
            $row[] = $aRow[$i];
        }
    }
    $row[] =
            ($allowView ? '<a href="" class="btnViewEmployee" rel="' . $aRow[0] . '" name="' . $aRow[3] . '"><img alt="View" onmouseover="Tip(\'' . ACTION_VIEW . '\')" src="' . $this->webroot . 'img/button/view.png" /></a> ' : '') .
            ($allowEdit ? '<a href="" class="btnEditEmployee" rel="' . $aRow[0] . '" name="' . $aRow[3] . '"><img alt="Edit" onmouseover="Tip(\'' . ACTION_EDIT . '\')" src="' . $this->webroot . 'img/button/edit.png" /></a> ' : '') .
            ($allowDelete ? '<a href="" class="btnDeleteEmployee" rel="' . $aRow[0] . '" name="' . $aRow[3] . '"><img alt="Delete" onmouseover="Tip(\'' . ACTION_DELETE . '\')" src="' . $this->webroot . 'img/button/delete.png" /></a>' : '');
    $output['aaData'][] = $row;
}

echo json_encode($output);
?>