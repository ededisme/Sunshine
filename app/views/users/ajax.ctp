<?php

// Authentication
$this->element('check_access');
$allowView = checkAccess($user['User']['id'], $this->params['controller'], 'view');
$allowEdit = checkAccess($user['User']['id'], $this->params['controller'], 'edit');
$allowEditProfile = checkAccess($user['User']['id'], $this->params['controller'], 'editProfile');
$allowDelete = checkAccess($user['User']['id'], $this->params['controller'], 'delete');
$allowClearSession = checkAccess($user['User']['id'], $this->params['controller'], 'clearSession');

// Function
include('includes/function.php');

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array('id', 'username', 'first_name', 'last_name', 'sex', 'dob', 'telephone', 'expired');

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "id";

/* DB table to use */
$sTable = "users";

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
        } else if ($aColumns[$i] == 'dob' || $aColumns[$i] == 'expired') {
            if ($aRow[$i] != '0000-00-00') {
                $row[] = dateShort($aRow[$i]);
            } else {
                $row[] = '';
            }
        } else if ($aColumns[$i] != ' ') {
            /* General output */
            $row[] = $aRow[$i];
        }
    }
    
    $checkSession = mysql_query("SELECT id, session_active FROM users WHERE is_active = 1 AND session_id IS NOT NULL AND id = {$aRow[0]} AND id != '".$user['User']['id']."' ");
    $resultSession = mysql_fetch_array($checkSession);
    
    
    $row[] =
            ($allowView ? '<a href="" class="btnViewUser" rel="' . $aRow[0] . '" name="' . $aRow[2] . ' ' . $aRow[3] . '"><img alt="View" onmouseover="Tip(\'' . ACTION_VIEW . '\')" src="' . $this->webroot . 'img/button/view.png" /></a> ' : '') .
            ($allowEdit ? '<a href="" class="btnEditUser" rel="' . $aRow[0] . '" name="' . $aRow[2] . ' ' . $aRow[3] . '"><img alt="Edit" onmouseover="Tip(\'' . ACTION_EDIT . '\')" src="' . $this->webroot . 'img/button/edit.png" /></a> ' : '') .
            ($allowEditProfile ? '<a href="" class="btnEditProfileUser" rel="' . $aRow[0] . '" name="' . $aRow[2] . ' ' . $aRow[3] . '"><img alt="Edit Profile" onmouseover="Tip(\'' . USER_LOGIN_INFO . '\')" src="' . $this->webroot . 'img/button/key.png" /></a> ' : '').
            ($allowDelete ? '<a href="" class="btnDeleteUser" rel="' . $aRow[0] . '" name="' . $aRow[2] . ' ' . $aRow[3] . '"><img alt="Delete" onmouseover="Tip(\'' . ACTION_DELETE . '\')" src="' . $this->webroot . 'img/button/delete.png" /></a>' : '').
            ($allowClearSession && mysql_num_rows($checkSession) && $aRow[0]!=$user['User']['id'] ? '<a href="" class="btnClearSessionUser" sessionActive="' . date("d/m/Y H:i:s", strtotime($resultSession['session_active'])) . '" rel="' . $aRow[0] . '" name="' . $aRow[2] . ' ' . $aRow[3] . '"><img alt="Clear Session" onmouseover="Tip(\'' . ACTION_CLEAR_SESSION . '\')" src="' . $this->webroot . 'img/button/clear.png" /></a>' : '');
    $output['aaData'][] = $row;
}

echo json_encode($output);
?>