<?php

include('includes/function.php');
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$dateNow    = date("Y-m-d");
$tableName  = $locationId."_inventory_totals AS inventory";
$columsName = "SUM(inventory.total_qty - inventory.total_order)";
$sGroup     = " GROUP BY products.id, inventory.expired_date";

if(strtotime($orderDate) < strtotime($dateNow) ){
    $tableName = $locationId."_inventory_total_details AS inventory";
    $sumEnding = "SUM((inventory.total_cycle + inventory.total_cm + inventory.total_pb + inventory.total_to_in) - (inventory.total_so + inventory.total_pos + inventory.total_pbc + inventory.total_to_out))";
    $columsName = "IFNULL(".$sumEnding.",0)";
    $sGroup = " GROUP BY products.id, inventory.expired_date";
}

$aColumns = array('CONCAT_WS("|",products.id,products.price_uom_id,u.abbr)', 
                  'products.code', 
                  'products.name', 
                  'inventory.expired_date', 
                  'IFNULL('.$columsName.',0)');

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "products.id";

/* DB table to use */
if ($category) {
    if(strtotime($orderDate) < strtotime($dateNow) ){
        $sTable = "products INNER JOIN product_branches ON product_branches.product_id = products.id AND product_branches.branch_id = ".$branchId." INNER JOIN ".$tableName." ON products.id = inventory.product_id AND inventory.date <= '".$orderDate."' INNER JOIN product_pgroups ON product_pgroups.product_id = products.id AND product_pgroups.pgroup_id = ".$category. " INNER JOIN pgroups ON pgroups.id = product_pgroups.pgroup_id AND (pgroups.user_apply = 0 OR (pgroups.user_apply = 1 AND pgroups.id IN (SELECT pgroup_id FROM user_pgroups WHERE user_id = ".$user['User']['id']."))) INNER JOIN uoms AS u ON u.id = products.price_uom_id"; 
    }else{
        $sTable = "products INNER JOIN product_branches ON product_branches.product_id = products.id AND product_branches.branch_id = ".$branchId." INNER JOIN ".$tableName." ON products.id = inventory.product_id INNER JOIN product_pgroups ON product_pgroups.product_id = products.id AND product_pgroups.pgroup_id = ".$category. " INNER JOIN pgroups ON pgroups.id = product_pgroups.pgroup_id AND (pgroups.user_apply = 0 OR (pgroups.user_apply = 1 AND pgroups.id IN (SELECT pgroup_id FROM user_pgroups WHERE user_id = ".$user['User']['id']."))) INNER JOIN uoms AS u ON u.id = products.price_uom_id";
    }
} else {
    if(strtotime($orderDate) < strtotime($dateNow) ){
        $sTable = "products INNER JOIN product_branches ON product_branches.product_id = products.id AND product_branches.branch_id = ".$branchId." INNER JOIN ".$tableName." ON products.id = inventory.product_id AND inventory.date <= '".$orderDate."' INNER JOIN uoms AS u ON u.id = products.price_uom_id INNER JOIN product_pgroups ON product_pgroups.product_id = products.id INNER JOIN pgroups ON pgroups.id = product_pgroups.pgroup_id AND (pgroups.user_apply = 0 OR (pgroups.user_apply = 1 AND pgroups.id IN (SELECT pgroup_id FROM user_pgroups WHERE user_id = ".$user['User']['id'].")))";
    }else{
        $sTable = "products INNER JOIN product_branches ON product_branches.product_id = products.id AND product_branches.branch_id = ".$branchId." INNER JOIN ".$tableName." ON products.id = inventory.product_id INNER JOIN uoms AS u ON u.id = products.price_uom_id INNER JOIN product_pgroups ON product_pgroups.product_id = products.id INNER JOIN pgroups ON pgroups.id = product_pgroups.pgroup_id AND (pgroups.user_apply = 0 OR (pgroups.user_apply = 1 AND pgroups.id IN (SELECT pgroup_id FROM user_pgroups WHERE user_id = ".$user['User']['id'].")))";
    }
}
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
        if($aColumns[$i] != 'IFNULL('.$columsName.',0)'){
            $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR "; 
        }
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
$condition = "products.is_active = 1 AND products.price_uom_id > 0 AND products.is_packet = 0";
if ($companyId) {
    $condition .= " AND products.company_id=" . $companyId;
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
        $sGroup
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
    $totalStock = $aRow[4];
    $totalOrder = 0;
    $record     = explode("|",$aRow[0]);
    $query = mysql_query("SELECT id,name,abbr,1 AS conversion FROM uoms WHERE id=".$record[1]."
                        UNION
                        SELECT id,name,abbr,(SELECT value FROM uom_conversions WHERE is_active=1 AND from_uom_id=".$record[1]." AND to_uom_id=uoms.id) AS conversion FROM uoms WHERE id IN (SELECT to_uom_id FROM uom_conversions WHERE is_active=1 AND from_uom_id=".$record[1].")
                        ORDER BY conversion ASC");
    $smallLabel = "";
    $smallUom   = 1;
    while($data=mysql_fetch_array($query)){
        $smallLabel = $data['abbr'];
        $smallUom   = $data['conversion'];
    }
    if(strtotime($orderDate) < strtotime($dateNow) ){
        $sqlCurrentStock = mysql_query("SELECT SUM(total_qty - total_order) AS total_qty FROM ".$locationId."_inventory_totals WHERE product_id = ".$record[0]." AND expired_date = '".$aRow[3]."';");
        if(mysql_num_rows($sqlCurrentStock)){
            $rowCurrentStock = mysql_fetch_array($sqlCurrentStock);
            if($rowCurrentStock[0] < $totalStock){
                $totalStock = $rowCurrentStock[0];
            }
        } else {
            $totalStock = 0;
        }
    }
    if(!empty($brId)){
        $sql = mysql_query("SELECT sum(sor.qty) as total_order FROM `stock_orders` as sor WHERE sor.product_id = ".$record[0]." AND sor.purchase_return_id = ".$brId." AND sor.location_id = ".$locationId." AND sor.expired_date = '".$aRow[3]."' AND sor.date = '".$orderDate."' GROUP BY sor.product_id");
        if(mysql_num_rows($sql)){
            $rowOrder   = mysql_fetch_array($sql);
            $totalOrder = $rowOrder['total_order'];
        }
    }
    for ($i = 0; $i < count($aColumns); $i++) {
        if ($i == 0) {
            /* Special output formatting */
            $index++;
            if(($totalStock + $totalOrder) > 0){
                $row[] = '<input type="radio" value="'.htmlspecialchars($aRow[1], ENT_QUOTES, 'UTF-8').'" exp="'.$aRow[3].'" class="'.($totalStock + $totalOrder).'" name="chkProductPR" />';
            } else {
                $row[] = '';
            }
        } else if ($aColumns[$i] == 'products.code') {
            $row[] = $aRow[$i];
        } else if ($aColumns[$i] == 'inventory.expired_date') {
            if($aRow[$i] != '' && $aRow[$i] != '0000-00-00'){
                $row[] = dateShort($aRow[$i]);
            } else {
                $row[] = '';
            }
        } else if ($aColumns[$i] == 'IFNULL('.$columsName.',0)') {
            $row[] = showTotalQty(($totalStock + $totalOrder), $record[2], $smallUom, $smallLabel);
        } else if ($aColumns[$i] != ' ') {
            /* General output */
            $row[] = $aRow[$i];
        }
    }
    $output['aaData'][] = $row;
}

echo json_encode($output);
?>