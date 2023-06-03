<?php
header("Content-type: text/plain");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variablestom
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
//$aColumns = array('CONCAT(cus.id,"||",IFNULL(cus.limit_balance,0),"||",IFNULL(cus.limit_total_invoice,0),"||",IFNULL(cus.payment_term_id, 0))', 'cus.customer_code', 'cus.name_kh', 'cus.name', 'cus.sex', 'cus.main_number', 'photo');
$aColumns = array('CONCAT(p.id,"||",0,"||",0,"||",0,"||",q.id,"||",p.patient_group_id)', 'p.patient_code', 'p.patient_name', 'p.sex', 'p.telephone');

/* Indexed column (used for fast and accurate table cardinality) */
//$sIndexColumn = "cus.id";
$sIndexColumn = "p.id";

/* DB table to use */
//if(@$group){
//    $sTable = "customers as cus INNER JOIN `customer_companies` AS ccp ON ccp.customer_id = cus.id AND ccp.company_id = ".$companyId." INNER JOIN `customer_cgroups` as gr ON cus.id = gr.customer_id AND gr.cgroup_id =".$group." INNER JOIN `cgroups` ON cgroups.id = gr.cgroup_id AND (cgroups.user_apply = 0 OR {$user['User']['id']} IN (SELECT user_id FROM user_cgroups WHERE cgroup_id = cgroups.id AND user_cgroups.user_id))";
//}else{
//    $sTable = "customers as cus INNER JOIN `customer_companies` AS ccp ON ccp.customer_id = cus.id AND ccp.company_id = ".$companyId." INNER JOIN `customer_cgroups` as gr ON cus.id = gr.customer_id INNER JOIN `cgroups` ON cgroups.id = gr.cgroup_id AND (cgroups.user_apply = 0 OR {$user['User']['id']} IN (SELECT user_id FROM user_cgroups WHERE cgroup_id = cgroups.id AND user_cgroups.user_id))";
//}
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
for ($i = 0; $i < count($aColumns)-1; $i++) {
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
//$condition = "cus.is_active=1";
$condition = "(p.is_active=1 AND q.status!=3 AND qd.status <= 2) OR (qd.doctor_id IS NULL)";
if (!eregi("WHERE", $sWhere)) {
    $sWhere .= "WHERE " . $condition;
} else {
    $sWhere .= "AND " . $condition;
}

/*
 * SQL queries
 * Get data to display
 */
//$groupBy = "GROUP BY cus.id";
$groupBy = "GROUP BY p.id";
$sQuery = "
        SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
        FROM   $sTable
        $sWhere
        $groupBy
        $sOrder
        $sLimit
";
//echo $sQuery;
//exit();
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
$saleCon = "";
if($saleId > 0){
    $saleCon = " AND id != ".$saleId;
}
while ($aRow = mysql_fetch_array($rResult)) {
    $row = array();
    $value = explode("||", $aRow[0]);
    $limitBalance = $value[1];
    $limitInvoice = $value[2];
    $totalInvUsed = 0;
    $totalAmoUsed = 0;
    if($limitBalance > 0 || $limitInvoice > 0){
        // Query Total Invoice & Total Amount Not Pay
        $queryCon = mysql_query("SELECT COUNT(id) AS total_invoice, SUM(total_amount) AS total_amount FROM sales_orders WHERE customer_id = ".$value[0]." AND status > 0 AND balance > 0".$saleCon." GROUP BY customer_id");
        $dataCon = mysql_fetch_array($queryCon);
        // Check Limit Invoice
        if(@mysql_num_rows($queryCon)){
            $totalInvUsed = $dataCon['total_invoice'];
            $totalAmoUsed = $dataCon['total_amount'];
        }
    }
//    $pType = "";
//    $sqlPriceType = mysql_query("SELECT GROUP_CONCAT(price_type_id) FROM cgroup_price_types WHERE cgroup_id IN (SELECT cgroup_id FROM customer_cgroups WHERE customer_id = ".$value[0]." GROUP BY cgroup_id)");
//    if(mysql_num_rows($sqlPriceType)){
//        $rowPriceType = mysql_fetch_array($sqlPriceType);
//        $pType = $rowPriceType[0];
//    }
    for ($i = 0; $i < count($aColumns); $i++) {
        if ($i == 0) {
            /* Special output formatting */
            $index ++;
            //$row[] = '<input type="radio" limit-balance="'.$limitBalance.'" limit-invoice="'.$limitInvoice.'" inv-used="'.($totalInvUsed + 1).'" bal-used="'.$totalAmoUsed.'" value="'.$value[0].'" name="chkCustomer" photo="'.$aRow[6].'" name-kh="'.$aRow[2].'" name-us="'.$aRow[3].'" rel="'.$aRow[1].'" term-id="'.$value[3].'" net_days="'.$aRow[6].'" ptype="'.$pType.'" />' ;
            $row[] = '<input type="radio" name="chkCustomer" name-us="'.$aRow[2].'"  value="'.$value[0].'" patient-group-id="'.$value[5].'" rel="'.$aRow[1].'" queue-id="'.$value[4].'">' ;
        } else if ($i == 3) {
            if ($aRow[$i] == "F") {
                $row[] = GENERAL_FEMALE;
            } else {
                $row[] = GENERAL_MALE;
            }
        } else if ($aColumns[$i] != ' ') {
            /* General output */
            $row[] = $aRow[$i];
        }
    }
    $output['aaData'][] = $row;
}

echo json_encode($output);
?>