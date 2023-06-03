<?php

// Authentication
include("includes/function.php");
$this->element('check_access');
$allowClone = checkAccess($user['User']['id'], $this->params['controller'], 'add');
$allowView = checkAccess($user['User']['id'], $this->params['controller'], 'view');
$allowEdit = checkAccess($user['User']['id'], $this->params['controller'], 'edit');
$allowDelete = checkAccess($user['User']['id'], $this->params['controller'], 'delete');
$allowSetPrice = checkAccess($user['User']['id'], $this->params['controller'], 'productPrice');
$allowViewCost = checkAccess($user['User']['id'], $this->params['controller'], 'viewCost');
$allowPrintProduct = checkAccess($user['User']['id'], $this->params['controller'], 'printProduct');
$allowPrintByChecked = checkAccess($user['User']['id'], $this->params['controller'], 'printProductByCheck');
// Get Symbol Currency
$sqlSym = mysql_query("SELECT symbol FROM companies INNER JOIN currency_centers ON currency_centers.id = companies.currency_center_id WHERE companies.is_active = 1 LIMIT 1");
$rowSym = mysql_fetch_array($sqlSym);
// Get Decimal
$sqlOption = mysql_query("SELECT product_cost_decimal FROM setting_options");
$rowOption = mysql_fetch_array($sqlOption);

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
if($allowViewCost){
    $indexParent = 10;
    $aColumns = array('p.id',
    'p.name',
    '(SELECT GROUP_CONCAT(name) FROM pgroups WHERE id IN (SELECT pgroup_id FROM product_pgroups WHERE product_id = p.id))',
    'CONCAT_WS("|*|",IFNULL(p.barcode,""),IFNULL(p.period_from,""),IFNULL(p.period_to,""),IFNULL(p.created,""),IFNULL(p.price_uom_id,""),IFNULL(p.small_val_uom,1))',
    'uoms.name',
    'brands.name',
//    'IFNULL(SUM(product_inventories.total_qty), 0) AS total_qty',
    'IFNULL(SUM(inventories.qty), 0) AS total_qty',
    'p.unit_cost',
    'p.is_packet',
    'p.file_catalog',
    '(SELECT name FROM products WHERE id = p.parent_id)');
} else {
    $indexParent = 9;
    $aColumns = array('p.id',
    'p.name',
    '(SELECT GROUP_CONCAT(name) FROM pgroups WHERE id IN (SELECT pgroup_id FROM product_pgroups WHERE product_id = p.id))',
    'CONCAT_WS("|*|",IFNULL(p.barcode,""),IFNULL(p.period_from,""),IFNULL(p.period_to,""),IFNULL(p.created,""),IFNULL(p.price_uom_id,""),IFNULL(p.small_val_uom,1))',
    'uoms.name',
    'brands.name',
//    'IFNULL(SUM(product_inventories.total_qty), 0) AS total_qty',
    'IFNULL(SUM(inventories.qty), 0) AS total_qty',
    'p.is_packet',
    'p.file_catalog',
    '(SELECT name FROM products WHERE id = p.parent_id)');
}

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "p.id";

/* DB table to use */
if ($category != 'all') {
//    $sTable = "products p INNER JOIN product_branches ON product_branches.product_id = p.id LEFT JOIN brands ON brands.id = p.brand_id LEFT JOIN uoms ON uoms.id = p.price_uom_id LEFT JOIN product_inventories ON product_inventories.product_id = p.id AND product_inventories.location_group_id IN (SELECT location_group_id FROM user_location_groups WHERE user_id = ".$user['User']['id'].") INNER JOIN product_pgroups pg ON p.id = pg.product_id AND pg.pgroup_id = " . $category;
    $sTable = "products p INNER JOIN product_branches ON product_branches.product_id = p.id LEFT JOIN brands ON brands.id = p.brand_id LEFT JOIN uoms ON uoms.id = p.price_uom_id LEFT JOIN inventories ON inventories.product_id = p.id AND inventories.location_group_id IN (SELECT location_group_id FROM user_location_groups WHERE user_id = ".$user['User']['id'].") INNER JOIN product_pgroups pg ON p.id = pg.product_id AND pg.pgroup_id = " . $category;
} else {
//    $sTable = "products p INNER JOIN product_branches ON product_branches.product_id = p.id LEFT JOIN brands ON brands.id = p.brand_id LEFT JOIN uoms ON uoms.id = p.price_uom_id LEFT JOIN product_inventories ON product_inventories.product_id = p.id AND product_inventories.location_group_id IN (SELECT location_group_id FROM user_location_groups WHERE user_id = ".$user['User']['id'].")";
     $sTable = "products p INNER JOIN product_branches ON product_branches.product_id = p.id LEFT JOIN brands ON brands.id = p.brand_id LEFT JOIN uoms ON uoms.id = p.price_uom_id LEFT JOIN inventories ON inventories.product_id = p.id AND inventories.location_group_id IN (SELECT location_group_id FROM user_location_groups WHERE user_id = ".$user['User']['id'].")";
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
            if($aColumns[intval($_GET['iSortCol_' . $i])] == "p.id"){
                $sOrder .= "(SELECT name FROM products WHERE id = p.parent_id) ASC, p.created ". mysql_real_escape_string($_GET['sSortDir_' . $i]) . ", ";
            } else if($aColumns[intval($_GET['iSortCol_' . $i])] == 'CONCAT_WS("|*|",IFNULL(p.code,""),IFNULL(p.period_from,""),IFNULL(p.period_to,""),IFNULL(p.created,""))'){
                $sOrder .= "(SELECT name FROM products WHERE id = p.parent_id) ASC, p.code ". mysql_real_escape_string($_GET['sSortDir_' . $i]) . ", ";
            }else{
                $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . " " . mysql_real_escape_string($_GET['sSortDir_' . $i]) . ", ";
            }
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
        if($aColumns[$i] == 'p.barcode' || $aColumns[$i] == 'p.code' || $aColumns[$i] == 'p.name' || $aColumns[$i] == 'p.unit_cost' || $aColumns[$i] == 'uoms.name'){
            $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";
        }
    }
    $sWhere = substr_replace($sWhere, "", -3);
    $sWhere .= ')';
}

/* Individual column filtering */
for ($i = 0; $i < count($aColumns) - 1; $i++) {
    if ($_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {
        if ($sWhere == "") {
            $sWhere = "WHERE ";
        } else {
            $sWhere .= " AND ";
        }
        if($aColumns[$i] == 'p.barcode' || $aColumns[$i] == 'p.code' || $aColumns[$i] == 'p.name' || $aColumns[$i] == 'p.unit_cost' || $aColumns[$i] == 'uoms.name'){
            $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch_' . $i]) . "%' ";
        }
    }
}

/* Customize condition */

$condition = "p.is_active=1 AND p.id IN (SELECT product_id FROM product_branches WHERE branch_id IN (SELECT branch_id FROM user_branches WHERE user_id = " . $user['User']['id'] . "))";
if($branchId != 'all'){
    $condition .= " AND product_branches.branch_id = " . $branchId;
}else{
    $condition .= " AND product_branches.branch_id IN (SELECT branch_id FROM user_branches WHERE user_id = " . $user['User']['id'] . ")";
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
$groupBy = "GROUP BY p.id";
if($qty == 1){
    $groupBy .= " HAVING total_qty > 0";
} else if($qty == 2){
    $groupBy .= " HAVING total_qty = 0";
}
$sQuery = "
        SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
        FROM   $sTable
        $sWhere
        $groupBy
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
$lenght     = 0;
$index      = $_GET['iDisplayStart'];
$unit_cost  = 0;

if($displayPro == 2){
    $parentId   = '$';
    $parentName = "";
}
$productChecked = "";
while ($aRow = mysql_fetch_array($rResult)) {
    $row = array();
    $parcket = 0;
    $fileCatalog = '';
    $explodeStr = explode("|*|", $aRow[3]);
    for ($i = 0; $i < count($aColumns) - 1; $i++) {
        if ($i == 0) {
            /* Special output formatting */             
            if($displayPro == 2){
                if ($aRow[$indexParent]  == $parentId) {
                    $row[] = ++$index;
                }else{
                    $index = 0;
                    if (!is_null($aRow[$indexParent])) {
                        $parentName = $aRow[$indexParent];
                    } else {
                        $parentName = 'No Parent';
                    }

                    $row[] = '<b class="colspanParent" style="font-size: 16px;">' . $parentName . '</b>';
                    for ($j = 0; $j < count($aColumns) - 1; $j++) {
                        $row[] = '<b class="colspanParentHidden"></b>';
                    } 
                    $output['aaData'][] = $row;
                    $row = array();                
                    $row[] = ++$index; 
                } 
            }else{
                $row[] = ++$index;
            }
        } else if ($i == 3) {
            $row[] = $explodeStr[0];
        } else if ($aColumns[$i] == 'p.unit_cost') {
            $row[] = number_format($aRow[$i], $rowOption[0])." ".$rowSym[0];
        } else if ($aColumns[$i] == 'p.is_packet') {
            $parcket = $aRow[$i];
            if($aRow[$i] == 0){
                $price = 0;
                $sqlPrice = mysql_query("SELECT products.unit_cost, product_prices.price_type_id, product_prices.amount, product_prices.percent, product_prices.add_on, product_prices.set_type FROM product_prices INNER JOIN products ON products.id = product_prices.product_id WHERE product_prices.product_id =".$aRow[0]." AND price_type_id = ".$priceType." AND product_prices.branch_id = ".$branchId." AND product_prices.uom_id =".$explodeStr[4]);
                if(@mysql_num_rows($sqlPrice)){
                    while($rowPrice = mysql_fetch_array($sqlPrice)){
                        $unitCost = replaceThousand(number_format($rowPrice['unit_cost'] /  1, 2));
                        if($rowPrice['set_type'] == 1){
                            $price = $rowPrice['amount'];
                        }else if($rowPrice['set_type'] == 2){
                            $percent = ($unitCost * $rowPrice['percent']) / 100;
                            $price = $unitCost + $percent;
                        }else if($rowPrice['set_type'] == 3){
                            $price = $unitCost + $rowPrice['add_on'];
                        }
                    }
                }
                $labelPrice = number_format($price, 2).' '.$rowSym[0];
                if($allowSetPrice){
                    $labelPrice .= ' <a href="#" class="setProductPrice" data="'.$aRow[0].'">['.ACTION_SET_PRICE.']</a>';
                }
                $row[] = $labelPrice;
            } else {
                $row[] = '';
            }
//        } else if ($aColumns[$i] == 'p.is_not_for_sale') {
//            $peroid     = '';
//            $peroidFrom = $explodeStr[1];
//            $peroidTo   = $explodeStr[2];
//            $created    = $explodeStr[3];
//            if($peroidFrom != ''){
//                $peroid = dateShort($peroidFrom)." - ".dateShort($peroidTo);
//            } else {
//                $peroid = dateShort($created, "d/m/Y");
//            }
//            if($aRow[$i] == 0){
//                $action = '<img src="' . $this->webroot . 'img/button/active.png" onmouseover="Tip(\'' . TABLE_ACTIVE . '\')" alt="Active" />';
//            }else{
//                $action = '<img src="' . $this->webroot . 'img/button/inactive.png" onmouseover="Tip(\'' . TABLE_INACTIVE . '\')" alt="Inactive" />';
//            }
//            $row[] = $action." ".$peroid;
//        } else if ($aColumns[$i] == 'IFNULL(SUM(product_inventories.total_qty), 0) AS total_qty') {
        } else if ($aColumns[$i] == 'IFNULL(SUM(inventories.qty), 0) AS total_qty') {
            $row[] = '<a href="#" class="viewInventoryProduct" data="'.$aRow[0].'">'.displayQtyByUoM($aRow[$i], $explodeStr[4], $explodeStr[5], $aRow[4]).'</a>';
        } else if ($aColumns[$i] == 'p.file_catalog') {
            $fileCatalog = $aRow[$i];
        } else if ($aColumns[$i] != ' ') {
            /* General output */
            $row[] = $aRow[$i];
        }
    }
    
    if($displayPro == 2){
        $parentId = $aRow[$indexParent]; 
    }
    $queryUserPrintProduct = mysql_query("SELECT product_id FROM `user_print_product` WHERE `user_id` = ".$user['User']['id']." AND product_id = ".$aRow[0]."");
    if(mysql_num_rows($queryUserPrintProduct) > 0){
        $productChecked = 'checked="checked"';
    }
    // Check Stock
    $totalStock = 0;
//    $sqlStock = mysql_query("SELECT SUM(total_qty) FROM product_inventories WHERE product_id = ".$aRow[0]." GROUP BY product_id");
     $sqlStock = mysql_query("SELECT SUM(qty) FROM inventories WHERE product_id = ".$aRow[0]." GROUP BY product_id");
    if(mysql_num_rows($sqlStock)){
        $rowStock = mysql_fetch_array($sqlStock);
        $totalStock = $rowStock[0];
    }
    $row[] =
            ($allowPrintByChecked && $parcket == 0 ? '<input type="checkbox" rel="' . $aRow[0] . '" class="btnCheckProduct" '.$productChecked.' />' : '') .
            ($allowView && $fileCatalog != '' ? ' <a href="'.$this->webroot.'public/product_catalog/'.$fileCatalog.'" class="btnViewProductCatalog" onClick="return popUpProductCatalog(this, \'Product Catalog\')"><img alt="Edit" onmouseover="Tip(\'' . ACTION_VIEW_CATALOG . '\')" src="' . $this->webroot . 'img/button/catalog.png" style="width: 16px; height: 16px;" /></a> ' : '') .
            ($allowView ? ' <a href="" class="btnViewProductView" rel="' . $aRow[0] . '" name="' . $explodeStr[0] . ' - ' . $aRow[1] . '"><img alt="View" onmouseover="Tip(\'' . ACTION_VIEW . '\')" src="' . $this->webroot . 'img/button/view.png" /></a> ' : '') .
            ($allowPrintProduct ? ' <a href="" class="btnPrintProduct" rel="' . $aRow[0].'"><img alt="Print Product" onmouseover="Tip(\'' . ACTION_PRINT_PRODUCT . '\')" src="' . $this->webroot . 'img/button/printer.png" /></a>' : '').
            ($allowEdit ? ' <a href="" class="btnEditProductView" rel="' . $aRow[0] . '" name="' . $explodeStr[0] . ' - ' . $aRow[1] . '"><img alt="Edit" onmouseover="Tip(\'' . ACTION_EDIT . '\')" src="' . $this->webroot . 'img/button/edit.png" /></a> ' : '') .
            ($allowDelete && $totalStock == 0 ? ' <a href="" class="btnDeleteProductView" rel="' . $aRow[0] . '" name="' . $explodeStr[0] . ' - ' . $aRow[1] . '"><img alt="Delete" onmouseover="Tip(\'' . ACTION_DELETE . '\')" src="' . $this->webroot . 'img/button/delete.png" /></a>' : '');
    $output['aaData'][] = $row;
    $lenght++;
}

echo json_encode($output);
?>