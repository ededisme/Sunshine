<?php
// Authentication
$this->element('check_access');
$allowVoidInvoice = checkAccess($user['User']['id'], 'dashboards', 'voidInvoice');
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array('i.id', 'i.invoice_code', 'patient_name', 'sex', 'i.total_amount', 'i.total_discount','q.id');

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "i.id";

/* DB table to use */
$sTable = "patients p INNER JOIN queues as q ON q.patient_id=p.id INNER JOIN invoices i ON i.queue_id=q.id";

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
$conditions = "i.is_void=0 AND DATE(i.created) >= CURDATE()";
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
    $row = array();
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
        } else if ($i == 4 || $i == 5) {
            $row[] = number_format($aRow[$i], 2);
            
        } else if ($aColumns[$i] == 'q.id') {
           
        } else if ($aColumns[$i] != ' ') {
            /* General output */
            $row[] = $aRow[$i];
        }
    }
    $sts = 0;
    $stsRec = 0;
    $querySale = mysql_query("SELECT sales_orders.id FROM sales_orders WHERE sales_orders.queue_id=".$aRow[6]);
    if(@mysql_num_rows($querySale)){
       $resStsSale = mysql_fetch_array($querySale);
       $queryCredit = mysql_query("SELECT credit_memos.id FROM credit_memos INNER JOIN credit_memo_with_invoices cminv ON credit_memos.id=cminv.credit_memo_id WHERE cminv.status=1 AND sales_order_id=".$resStsSale[0]);
       if(@mysql_num_rows($queryCredit)){
           $sts = 1;
       }else{
           $sts = 0;
       }
    }else{
        $sts = 1;
    }
    
    $queryRec = mysql_query("SELECT id FROM receipts WHERE is_void=0 AND invoice_id =".$aRow[0]);
    if(@mysql_num_rows($queryRec)){
        $stsRec = 0;
    }else{
        $stsRec = 1;
    }
    
    $void = '';
    if ($allowVoidInvoice) { 
        $void = '<a href="" class="btnVoid" rel="' . $aRow[0] . '" title="' . $aRow[1] . '" sts="' . $sts . '" stsRec="' . $stsRec . '" ><img alt="Void" onmouseover="Tip(\''.ACTION_VOID.'\')" src="' . $this->webroot . 'img/action/delete.png" /></a>';
    }
    $row[] ='<a href="" class="btnView" rel="' . $aRow[0] . '" title="' . $aRow[1] . '"><img alt="View" onmouseover="Tip(\''.ACTION_VIEW.'\')" src="' . $this->webroot . 'img/action/view.png" /></a>'.
           $void ;
    $output['aaData'][] = $row;
}

echo json_encode($output);
?>