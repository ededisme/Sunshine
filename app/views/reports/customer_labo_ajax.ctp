<?php

// Function
include('includes/function.php');
$this->element('check_access');
$allowVoidInvoice = checkAccess($user['User']['id'], 'dashboards', 'voidInvoice');

/**
 * export to excel
 */
$filename="public/report/report_labo_result_" . $user['User']['id'] . ".csv";
$fp=fopen($filename,"wb");
$excelContent = MENU_REPORT_SECTION_SERVICE . "\n\n";

if($data[1]!='') {
    $excelContent .= REPORT_FROM . ': ' . str_replace('|||','/',$data[1]);
}
if($data[2]!='') {
    $excelContent .= ' '.REPORT_TO . ': ' . str_replace('|||','/',$data[2]);
}

$excelContent .= "\n\n".TABLE_NO."\t".TABLE_DATE."\t".TABLE_INVOICE_CODE."\t".TABLE_CUSTOMER_NUMBER."\t".TABLE_CUSTOMER_NAME."\t".DOCTOR_DOCTOR."\t".MENU_SUB_GROUP."\t".TABLE_QTY."\t".'Hospital Price'."\t".'Patient Price'."($)"."\t".TABLE_SEX."\t".TABLE_DATE_OF_BIRTH;
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array("lig.id",
    "invoices.created" ,
    "invoices.invoice_code" ,
    "patients.patient_code" , 
    "patients.patient_name" , 
    "(SELECT e.name FROM users u INNER JOIN user_employees ue ON u.id = ue.user_id INNER JOIN employees e ON e.id = ue.employee_id WHERE u.id = invd.doctor_id )",
    "lig.name",
    "invd.qty",
    "invd.qty*invd.hospital_price",
    "invd.total_price", 
    'patients.sex',
    'DATE_FORMAT(dob,"'.MYSQL_DATE.'")');

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "lig.id";

/* DB table to use */
$sTable = " labo_item_groups lig INNER JOIN invoice_details invd ON invd.service_id= lig.id INNER JOIN invoices ON invoices.id=invd.invoice_id INNER JOIN queues ON queues.id = invoices.queue_id INNER JOIN patients ON patients.id=queues.patient_id ";

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
 * no need to aging below this line
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
            $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . " " . mysql_real_escape_string($_GET['sSortDir_' . $i]) . ", ";
        }
    }

    $sOrder = substr_replace($sOrder, "", -2);
    if ($sOrder == "ORDER BY") {
        $sOrder = "";
    }

    $sOrder = str_replace("invoices.created asc", "invoices.created asc, invoices.id asc", $sOrder);
    $sOrder = str_replace("invoices.created desc", "invoices.created desc, invoices.id desc", $sOrder);
}
$sGroup = "";

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
$condition = 'invoices.is_void=0 AND invd.is_active=1 AND invd.type=2 ';
if ($data[1] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= '"' . dateConvert(str_replace("|||", "/", $data[1])) . '" <= DATE(invoices.created)';
}
if ($data[2] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= '"' . dateConvert(str_replace("|||", "/", $data[2])) . '" >= DATE(invoices.created)';
}
if ($data[3] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'lig.id=' . $data[3];
}
if($data[4] != ''){
      $condition != '' ? $condition .= ' AND ' : '';
      $condition .= 'invd.doctor_id=' . $data[4];
}
if($data[5] != ''){
      $condition != '' ? $condition .= ' AND ' : '';
      $condition .= 'patients.id=' . $data[5];
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
$qty = 0;
$totalPrice = 0;
$totalHospitalPrice = 0;
while ($aRow = mysql_fetch_array($rResult)) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        if ($i == 0) {
            /* Special output formatting */
            $row[] = ++$index;
            $excelContent .= "\n" . $index ."\t";
        } else if ($i == 7){
            $row[] = number_format($aRow[$i], 0);
            $excelContent .= number_format($aRow[$i], 0)."\t";
            $qty += number_format($aRow[$i], 0);
        } else if ($i == 8){
            $row[] = number_format($aRow[$i], 2);
            $excelContent .= number_format($aRow[$i], 2)."\t";
            $totalHospitalPrice += number_format($aRow[$i], 2);
        } else if ($i == 9){
            $row[] = number_format($aRow[$i], 2);
            $excelContent .= number_format($aRow[$i], 2)."\t";
            $totalPrice += number_format($aRow[$i], 2);
        } else if ($aColumns[$i] != '') {
            /* General output */
            $row[] = $aRow[$i];
            $excelContent .= $aRow[$i]."\t";
        }
    }
    $output['aaData'][] = $row;
}

if (mysql_num_rows($rResult)) {        
    $excelContent .= "\n" . "TOTAL";
    for ($i = 0; $i < count($aColumns) - 6; $i++) {        
        $excelContent .= "\t";
    }    
    $excelContent .= "\t" . number_format($qty, 0);  
    $excelContent .= "\t" . number_format($totalHospitalPrice, 2);
    $excelContent .= "\t" . number_format($totalPrice, 2);   
}

echo json_encode($output);


$excelContent = chr(255).chr(254).@mb_convert_encoding($excelContent, 'UTF-16LE', 'UTF-8');
fwrite($fp,$excelContent);
fclose($fp);
?>