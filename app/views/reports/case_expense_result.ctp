<?php
include("includes/function.php");
$rnd = rand();
$oTable = "oTable" . $rnd;
$printArea = "printArea" . $rnd;
$btnPrint = "btnPrint" . $rnd;
$btnExport = "btnExport" . $rnd;
$tblName = "tbl" . rand(); ?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<script type="text/javascript">
    var <?php echo $oTable; ?>;
    $(document).ready(function(){
        $(".btnExportCaseExpense").click(function(){
            window.open("<?php echo $this->webroot; ?>public/report/case_expence_<?php echo $user['User']['id']; ?>.csv", "_blank");
        });
        
        $("#<?php echo $btnPrint; ?>").click(function(){
            $(".dataTables_length").hide();
            $(".dataTables_filter").hide();
            $(".dataTables_paginate").hide();
            w=window.open();
            w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
            w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
            w.document.write($("#<?php echo $printArea; ?>").html());
            w.document.close();
            w.print();
            w.close();
            $(".dataTables_length").show();
            $(".dataTables_filter").show();
            $(".dataTables_paginate").show();
        });
    });
</script>
<div id="<?php echo $printArea; ?>">
    <?php
    $msg = '<b style="font-size: 18px;">' . MENU_EXPENSE . '</b><br /><br />';
    if($_POST['date_from']!='') {
        $msg .= REPORT_FROM.': '.$_POST['date_from'];
    }
    if($_POST['date_to']!='') {
        $msg .= ' '.REPORT_TO.': '.$_POST['date_to'];
    }
    
    /**
    * export to excel
    */
   $filename = "public/report/case_expence_" . $user['User']['id'] . ".csv";
   $fp = fopen($filename, "wb");
   $excelContent  = MENU_EXPENSE . "\n\n";    
   $excelContent .= TABLE_NO."\t".TABLE_DATE."\t".TABLE_REFERENCE."\t".MENU_CHART_OF_ACCOUNT_MANAGEMENT."\t".TABLE_EMPLOYEE."\t".TABLE_VENDOR."\t".TABLE_MEMO."\t".TABLE_TOTAL_AMOUNT."\t".TABLE_CREATED_BY."\n";    
    
    $col  = implode(',', $_POST);
    $data = explode(",", $col);
    $condition = "expenses.status > 0";
    if ($data[1] != '') {
        $condition != '' ? $condition .= ' AND ' : '';
        $condition .= '"' . dateConvert(str_replace("|||", "/", $data[1])) . '" <= DATE(expenses.date)';
    }
    if ($data[2] != '') {
        $condition != '' ? $condition .= ' AND ' : '';
        $condition .= '"' . dateConvert(str_replace("|||", "/", $data[2])) . '" >= DATE(expenses.date)';
    }
    if ($data[3] != '') {
        $condition != '' ? $condition .= ' AND ' : '';
        $condition .= 'expenses.status=' . $data[3];
    }
    if ($data[4] != '') {
        $condition != '' ? $condition .= ' AND ' : '';
        $condition .= 'expenses.company_id=' . $data[4];
    }else{
        $condition != '' ? $condition .= ' AND ' : '';
        $condition .= 'expenses.company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')';
    }
    if ($data[5] != '') {
        $condition != '' ? $condition .= ' AND ' : '';
        $condition .= 'expenses.branch_id=' . $data[5];
    }else{
        $condition != '' ? $condition .= ' AND ' : '';
        $condition .= 'expenses.branch_id IN (SELECT branch_id FROM user_branches WHERE user_id = '.$user['User']['id'].')';
    }
    if ($data[6] != '') {
        $condition != '' ? $condition .= ' AND ' : '';
        $condition .= 'expense_details.chart_account_id=' . $data[6];
    }
    if ($data[7] != '') {
        $condition != '' ? $condition .= ' AND ' : '';
        $condition .= 'expense_details.employee_id=' . $data[7];
    }
    if ($data[8] != '') {
        $condition != '' ? $condition .= ' AND ' : '';
        $condition .= 'expenses.created_by=' . $data[8];
    }
    
    echo $this->element('/print/header-report',array('msg'=>$msg));
    ?>
    <div id="dynamic">
        <table id="<?php echo $tblName; ?>" class="table">
            <thead>
                <tr>
                    <th class="first"><?php echo TABLE_NO; ?></th>
                    <th style="width: 120px !important;"><?php echo TABLE_DATE; ?></th>
                    <th><?php echo TABLE_REFERENCE; ?></th>
                    <th><?php echo MENU_CHART_OF_ACCOUNT_MANAGEMENT; ?></th>
                    <th><?php echo TABLE_EMPLOYEE; ?></th>
                    <th><?php echo TABLE_VENDOR; ?></th>
                    <th><?php echo TABLE_MEMO; ?></th>
                    <th style="width: 140px !important;"><?php echo TABLE_TOTAL_AMOUNT; ?></th>
                    <th style="width: 180px !important;"><?php echo TABLE_CREATED_BY; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $index = 1;
                    $totalAmount = 0;
                    $otherIncomeOldId = "$";
                    $queryOtherExpense = mysql_query("SELECT 
                                                        expenses.id,
                                                        expenses.date AS date,
                                                        expenses.reference AS reference,
                                                        (SELECT name FROM vendors WHERE vendors.id = expenses.vendor_id) AS vendor_name,
                                                        expenses.note,
                                                        FORMAT(expenses.total_amount, 2) AS total_amount,
                                                        (SELECT CONCAT(first_name,' ', last_name) FROM users WHERE users.id = expenses.created_by) AS created_by,
                                                        (SELECT CONCAT(account_codes,' - ', account_description) FROM chart_accounts WHERE chart_accounts.id = expense_details.chart_account_id) AS chart_account,
                                                        expense_details.note AS note_detail,
                                                        (SELECT CONCAT(employee_code,' · ',name) AS name FROM employees WHERE id = expenses.employee_id) AS employee_name,
                                                        FORMAT(expense_details.amount, 2) AS amount
                                                    FROM  
                                                        expenses 
                                                    INNER JOIN 
                                                        expense_details ON expenses.id = expense_details.expense_id 
                                                    WHERE 
                                                        ".$condition."");
                    if(mysql_num_rows($queryOtherExpense)){
                        while($dataOtherExpense = mysql_fetch_array($queryOtherExpense)){
                            $totalAmount += replaceThousand($dataOtherExpense["amount"]);
                            $excelContent .= $index."\t".date("d/m/Y", strtotime($dataOtherExpense["date"]))."\t".$dataOtherExpense["reference"]."\t".$dataOtherExpense["chart_account"]."\t".$dataOtherExpense["employee_name"]."\t".$dataOtherExpense["vendor_name"]."\t".nl2br($dataOtherExpense["note_detail"])."\t".$dataOtherExpense["amount"]."\t".$dataOtherExpense["created_by"]."\n";    
                ?>
                <tr>
                    <td class="first" style="text-align: center;"><?php echo $index++; ?></td>
                    <td><?php echo date("d/m/Y", strtotime($dataOtherExpense["date"])); ?></td>
                    <td><?php echo $dataOtherExpense["reference"]; ?></td>
                    <td><?php echo $dataOtherExpense["chart_account"]; ?></td>
                    <td><?php echo $dataOtherExpense["employee_name"]; ?></td>
                    <td><?php echo $dataOtherExpense["vendor_name"]; ?></td>
                    <td><?php echo nl2br($dataOtherExpense["note_detail"]); ?></td>
                    <td style="text-align: right;"><?php echo $dataOtherExpense["amount"]; ?></td>
                    <td><?php echo $dataOtherExpense["created_by"]; ?></td>
                </tr>
                <?php
                        }
                        $excelContent .= "\t\t\t\t\t\t Total: \t".number_format($totalAmount, 2);    
                ?>
                <tr>
                    <td class="first" colspan="7" style="text-align: right;">Total: </td>
                    <td style="text-align: right;"><?php echo number_format($totalAmount, 2); ?></td>
                    <td></td>
                </tr>
                <?php
                    }else{
                ?>
                <tr>
                    <td colspan="9" class="dataTables_empty first"><?php echo GENERAL_NO_RECORD; ?></td>
                </tr>
                <?php
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>
<div style="clear: both;"></div>
<br />
<div class="buttons">
    <button type="button" id="<?php echo $btnPrint; ?>" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/>
        <?php echo ACTION_PRINT; ?>
    </button>
    <button type="button" class="positive btnExportCaseExpense">
        <img src="<?php echo $this->webroot; ?>img/button/csv.png" alt=""/>
        <?php echo ACTION_EXPORT_TO_EXCEL; ?>
    </button>
</div>
<div style="clear: both;"></div>
<?php 
$excelContent = chr(255) . chr(254) . @mb_convert_encoding($excelContent, 'UTF-16LE', 'UTF-8');
fwrite($fp, $excelContent);
fclose($fp);
?>