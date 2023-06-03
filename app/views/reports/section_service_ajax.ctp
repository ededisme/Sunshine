<?php

// Function
include('includes/function.php');
/**
 * export to excel
 */
$filename="public/report/report_section_service_" . $user['User']['id'] . ".csv";
$fp=fopen($filename,"wb");
$excelContent = MENU_REPORT_SECTION_SERVICE . "\n\n";

if($data[1]!='') {
    $excelContent .= REPORT_FROM . ': ' . str_replace('|||','/',$data[1]);
}
if($data[2]!='') {
    $excelContent .= ' '.REPORT_TO . ': ' . str_replace('|||','/',$data[2]);
}

$excelContent .= "\n\n".TABLE_NO."\t".TABLE_DATE."\t".TABLE_INVOICE_CODE."\t".TABLE_CUSTOMER_NUMBER."\t".TABLE_CUSTOMER_NAME."\t".DOCTOR_DOCTOR."\t".SECTION_SECTION."\t".SERVICE_SERVICE."\t".TABLE_QTY."\t".GENERAL_UNIT_PRICE."\t".GENERAL_DISCOUNT."($)\t".TABLE_TOTAL_AMOUNT."($)";
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array("invoice_details.id",
    "invoices.created",
    "invoices.invoice_code",
    "patients.patient_code" , 
    "patients.patient_name" , 
    "(SELECT e.name FROM users u INNER JOIN user_employees ue ON u.id = ue.user_id INNER JOIN employees e ON e.id = ue.employee_id WHERE u.id = invoice_details.doctor_id )",
    "sections.name",
    "services.name",
    "invoice_details.qty",
    "invoice_details.unit_price",
    "invoice_details.discount",
    "invoice_details.total_price");

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "invoice_details.created";

/* DB table to use */
$sTable = "invoice_details INNER JOIN invoices ON invoices.id = invoice_details.invoice_id INNER JOIN services ON services.id = invoice_details.service_id INNER JOIN sections on sections.id = services.section_id INNER JOIN queues ON queues.id = invoices.queue_id INNER JOIN patients ON patients.id=queues.patient_id";


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
    $sOrder = "ORDER BY ";
    for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
        if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
            $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . " " . mysql_real_escape_string($_GET['sSortDir_' . $i]) . ", ";
        }
    }

    $sOrder = substr_replace($sOrder, "", -2);
    if ($sOrder == "ORDER BY") {
        $sOrder = 'ORDER BY services.section_id , services.id, invoice_details.created ASC';
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
$condition = 'invoice_details.is_active = 1 AND sections.is_active = 1 AND services.is_active = 1 AND invoice_details.type = 1';
if ($data[1] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= '"' . dateConvert(str_replace("|||", "/", $data[1])) . '" <= DATE(invoice_details.created)';
}
if ($data[2] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= '"' . dateConvert(str_replace("|||", "/", $data[2])) . '" >= DATE(invoice_details.created)';
}
$condition != '' ? $condition .= ' AND ' : '';
if ($data[3] != '') {
    $condition .= 'invoices.is_void=' . $data[3];
} 
if ($data[4] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'company_id=' . $data[4];
}
if ($data[5] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'sections.id=' . $data[5];
}
if ($data[6] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'services.id=' . $data[6];
}
if ($data[7] != '') {
    $condition != '' ? $condition .= ' AND ' : '';
    $condition .= 'invoice_details.doctor_id=' . $data[7];
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
$groupBy = "";

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
$qty = 0;
$unitPrice = 0;
$discount = 0;
$totalPrice = 0;
while ($aRow = mysql_fetch_array($rResult)) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        if ($i == 0) {
            /* Special output formatting */
            $row[] = ++$index;
            $excelContent .= "\n" . $index ."\t";
        } else if ($i == 8){
            $row[] = number_format($aRow[$i], 0);
            $excelContent .= number_format($aRow[$i], 0)."\t";
            $qty += number_format($aRow[$i], 0);
        } else if ($i == 9){
            $row[] = number_format($aRow[$i], 2);
            $excelContent .= number_format($aRow[$i], 2)."\t";
            $unitPrice += number_format($aRow[$i], 2);
        } else if($i == 10){
            $row[] = number_format($aRow[$i], 2);
            $excelContent .= number_format($aRow[$i], 2)."\t";
            $discount += number_format($aRow[$i], 2);
        } else if ($i == 11){
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
    for ($i = 0; $i < count($aColumns) - 5; $i++) {        
        $excelContent .= "\t";
    }    
    $excelContent .= "\t" . number_format($qty, 0);  
    $excelContent .= "\t" . number_format($unitPrice, 2);   
    $excelContent .= "\t" . number_format($discount, 2);    
    $excelContent .= "\t" . number_format($totalPrice, 2);    
}

echo json_encode($output);


$excelContent = chr(255).chr(254).@mb_convert_encoding($excelContent, 'UTF-16LE', 'UTF-8');
fwrite($fp,$excelContent);
fclose($fp);

?>