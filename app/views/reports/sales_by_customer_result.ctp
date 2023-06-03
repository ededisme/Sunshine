<?php

$rnd = rand();
$tblName = "tbl" . rand();
$printArea = "printArea" . $rnd;
$btnPrint = "btnPrint" . $rnd;
$btnExport = "btnExport" . $rnd;

include('includes/function.php');

/**
 * export to excel
 */
$filename="public/report/sales_by_customer_" . $this->Session->id(session_id()) . ".csv";
$fp=fopen($filename,"wb");
$excelContent = '';

?>
<script type="text/javascript">
    $(document).ready(function(){
        $("#<?php echo $btnPrint; ?>").click(function(){
            w=window.open();
            w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
            w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
            w.document.write($("#<?php echo $printArea; ?>").html());
            w.document.close();
            w.print();
            w.close();
        });
        
        $("#<?php echo $btnExport; ?>").click(function(){
            window.open("<?php echo $this->webroot; ?>public/report/sales_by_customer_<?php echo $this->Session->id(session_id()); ?>.csv", "_blank");
        });
    });
</script>
<div id="<?php echo $printArea; ?>">
    <?php
    $msg = '<b style="font-size: 18px;">' . MENU_REPORT_SALES_ORDER_BY_CUSTOMER . '</b><br /><br />';
    $excelContent .= MENU_REPORT_SALES_ORDER_BY_CUSTOMER."\n\n";
    if($_POST['date_from']!='') {
        $msg .= REPORT_FROM.': '.$_POST['date_from'];
        $excelContent .= REPORT_FROM.': '.$_POST['date_from'];
    }
    if($_POST['date_to']!='') {
        $msg .= ' '.REPORT_TO.': '.$_POST['date_to'];
        $excelContent .= ' '.REPORT_TO.': '.$_POST['date_to']."\n\n";
    }
    echo $this->element('/print/header-report',array('msg'=>$msg));
    $excelContent .= TABLE_NO."\t".PATIENT_NAME."\t".TABLE_INVOICE_CODE."\t".TABLE_PRODUCT_NAME."\t".TABLE_LOCATION_GROUP."\t".GENERAL_AMOUNT;
    ?>
    <table id="<?php echo $tblName; ?>" class="table_report">
        <tr>
            <th class="first"><?php echo TABLE_NO; ?></th>
            <th><?php echo PATIENT_NAME; ?></th>
            <th style="width: 100px !important;"><?php echo TABLE_INVOICE_CODE; ?></th>
            <th><?php echo TABLE_PRODUCT_NAME; ?></th>
            <th style="width: 160px !important;"><?php echo TABLE_LOCATION_GROUP; ?></th>
            <th style="width: 120px !important;"><?php echo GENERAL_AMOUNT; ?></th>
        </tr>
        <?php
        // general condition
        $col = implode(',', $_POST);
        $col = explode(",", $col);
        $condition = '';
        if ($col[1] != '') {
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= '"' . dateConvert($col[1]) . '" <= DATE(order_date)';
        }
        if ($col[2] != '') {
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= '"' . dateConvert($col[2]) . '" >= DATE(order_date)';
        }
        $condition != '' ? $condition .= ' AND ' : '';
        if ($col[3] == '') {
            $condition .= 'status!=0';
        } else {
            $condition .= 'status=' . $col[3];
        }
        if ($col[4] != '') {
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= 'company_id=' . $col[4];
        }else{
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= 'company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')';
        }
        if ($col[5] != '') {
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= 'branch_id=' . $col[5];
        }else{
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= 'branch_id IN (SELECT branch_id FROM user_branches WHERE user_id = '.$user['User']['id'].')';
        }
        if ($col[6] != '') {
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= 'location_group_id=' . $col[6];
        }else{
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= 'location_group_id IN (SELECT location_group_id FROM user_location_groups WHERE user_id = '.$user['User']['id'].')';
        }
        if ($col[11] != '') {
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= 'created_by=' . $col[11];
        }
        if ($col[12] != '') {
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= 'patient_id=' . $col[12];
        }
        
        // declare
        $grandTotalAmount=0;
        $excelContent .= "\n".'Product'; ?>
        <tr style="font-weight: bold;"><td class="first" colspan="6" style="font-size: 14px;">Product</td></tr>
        <?php
        $index=1;
        $arrCode=array();
        $arrProduct=array();
        $arrLocation=array();
        $oldCustomerId='';
        $oldCustomerName='';
        $subTotalAmount=0;
        $totalAmount=0;
        $query=mysql_query("SELECT
                                'Invoice' AS trans_type,
                                patient_id,
                                (SELECT CONCAT_WS(' ',patient_code, patient_name) FROM patients WHERE id=patient_id) AS patient_name,
                                order_date,
                                so_code AS code,
                                (SELECT CONCAT_WS(' ',code,name) FROM products WHERE id=product_id) AS product_name,
                                (SELECT name FROM location_groups WHERE id=location_group_id) AS location_name,
                                ". ($col[10] != ''?($col[10] != '1'?'qty':'qty_free'):'qty')." AS qty,
                                (SELECT name FROM uoms WHERE id=qty_uom_id) AS qty_uom_name,
                                unit_price,
                                total_price
                            FROM sales_orders 
                                INNER JOIN sales_order_details ON sales_orders.id=sales_order_details.sales_order_id
                            WHERE "
                                . $condition
                                . ($col[7] != ''?' AND product_id IN (SELECT product_id FROM product_pgroups WHERE pgroup_id=' . $col[7] . ')':'')
                                . ($col[8] != ''?' AND (SELECT parent_id FROM products WHERE id=product_id)=' . $col[8] :'')
                                . ($col[9] != ''?' AND product_id=' . $col[9] :'')
                                . "
                            UNION ALL
                            SELECT
                                'Credit Memo' AS trans_type,
                                patient_id,
                                (SELECT CONCAT_WS(' ',patient_code, patient_name) FROM patients WHERE id=patient_id) AS patient_name,
                                order_date,
                                cm_code AS code,
                                (SELECT CONCAT_WS(' ',code,name) FROM products WHERE id=product_id) AS product_name,
                                (SELECT name FROM location_groups WHERE id=location_group_id) AS location_name,
                                ". ($col[10] != ''?($col[10] != '1'?'(qty*-1)':'(qty_free * -1)'):'((qty + qty_free) * -1)')." AS qty,
                                (SELECT name FROM uoms WHERE id=qty_uom_id) AS qty_uom_name,
                                unit_price,
                                total_price
                            FROM credit_memos
                                INNER JOIN credit_memo_details ON credit_memos.id=credit_memo_details.credit_memo_id
                            WHERE "
                                . str_replace('is_pos=0 AND','',$condition)
                                . ($col[7] != ''?' AND product_id IN (SELECT product_id FROM product_pgroups WHERE pgroup_id=' . $col[7] . ')':'')
                                . ($col[8] != ''?' AND (SELECT parent_id FROM products WHERE id=product_id)=' . $col[8] :'')
                                . ($col[9] != ''?' AND product_id=' . $col[9] :'')
                                . "
                            ORDER BY patient_name,order_date");
        while($data=mysql_fetch_array($query)){
            $arrCode[] = $data['code'];
            $arrProduct[] = $data['product_name'];
            $arrLocation[] = $data['location_name'];
            if($data['patient_id']!=$oldCustomerId){ 
                if($oldCustomerName!=''){ 
                    $excelContent .= "\n".$index."\t".$oldCustomerName."\t\t\t\t".number_format($subTotalAmount, 2); ?>
            <tr>
                <td class="first"><?php echo $index++; ?></td>
                <td colspan="4"><?php echo $oldCustomerName; ?></td>
                <td style="text-align: right;"><?php echo number_format($subTotalAmount, 2); ?></td>
            </tr>
            <?php
                }
                $subTotalAmount=0;
            }
            $subTotalAmount   += ($data['qty'] * $data['unit_price']);
            $totalAmount      += ($data['qty'] * $data['unit_price']);
            $grandTotalAmount += ($data['qty'] * $data['unit_price']);
            $oldCustomerId   = $data['patient_id'];
            $oldCustomerName = $data['patient_name'];
        }
        if(mysql_num_rows($query)){
            $excelContent .= "\n".$index."\t".$oldCustomerName."\t\t\t\t".number_format($subTotalAmount, 2); ?>
        <tr>
            <td class="first"><?php echo $index++; ?></td>
            <td colspan="4"><?php echo $oldCustomerName; ?></td>
            <td style="text-align: right;"><?php echo number_format($subTotalAmount, 2); ?></td>
        </tr>
        <?php $excelContent .= "\n".'Total Product'."\t\t\t\t\t".number_format($totalAmount, 2); ?>
        <tr style="font-weight: bold;">
            <td class="first" colspan="5" style="font-size: 14px;">Total Product</td>
            <td style="text-align: right;font-size: 14px;text-decoration: underline;"><?php echo number_format($totalAmount, 2); ?></td>
        </tr>
        <?php 
        } 
        if($col[7] == '' && $col[8] == '' && $col[9] == ''){ 
            $excelContent .= "\n\n".'Service'; ?>
            <tr><td colspan="6">&nbsp;</td></tr>
            <tr style="font-weight: bold;"><td class="first" colspan="6" style="font-size: 14px;">Service</td></tr>
            <?php
            $index=1;
            $oldCustomerId='';
            $oldCustomerName='';
            $subTotalAmount=0;
            $totalAmount=0;
            $query=mysql_query("SELECT
                                    'Invoice' AS trans_type,
                                    patient_id,
                                    (SELECT CONCAT_WS(' ',patient_code, patient_name) FROM patients WHERE id=patient_id) AS patient_name,
                                    order_date,
                                    so_code AS code,
                                    (SELECT name FROM services WHERE id=service_id) AS service_name,
                                    (SELECT name FROM location_groups WHERE id=location_group_id) AS location_name,
                                    ". ($col[10] != ''?($col[10] != '1'?'qty':'qty_free'):'qty')." AS qty,
                                    NULL AS qty_uom_name,
                                    unit_price,
                                    total_price
                                FROM sales_orders
                                    INNER JOIN sales_order_services ON sales_orders.id=sales_order_services.sales_order_id
                                WHERE " . $condition . "
                                UNION ALL
                                SELECT
                                    'Credit Memo' AS trans_type,
                                    patient_id,
                                    (SELECT CONCAT_WS(' ',patient_code, patient_name) FROM patients WHERE id=patient_id) AS patient_name,
                                    order_date,
                                    cm_code AS code,
                                    (SELECT name FROM services WHERE id=service_id) AS service_name,
                                    (SELECT name FROM location_groups WHERE id=location_group_id) AS location_name,
                                    ". ($col[10] != ''?($col[10] != '1'?'(qty*-1)':'(qty_free * -1)'):'((qty + qty_free) * -1)')." AS qty,
                                    NULL AS qty_uom_name,
                                    unit_price,
                                    total_price
                                FROM credit_memos
                                    INNER JOIN credit_memo_services ON credit_memos.id=credit_memo_services.credit_memo_id
                                WHERE " . str_replace('is_pos=0 AND','',$condition) . "
                                ORDER BY patient_name,order_date");
            while($data=mysql_fetch_array($query)){
                $arrCode[] = $data['code'];
                $arrProduct[] = $data['service_name'];
                $arrLocation[] = $data['location_name'];
                if($data['patient_id']!=$oldCustomerId){
                    if($oldCustomerName!=''){ 
                        $excelContent .= "\n".$index."\t".$oldCustomerName."\t\t\t\t".number_format($subTotalAmount, 2); ?>
                <tr>
                    <td class="first"><?php echo $index++; ?></td>
                    <td colspan="4"><?php echo $oldCustomerName; ?></td>
                    <td style="text-align: right;"><?php echo number_format($subTotalAmount, 2); ?></td>
                </tr>
                <?php
                    }
                    $subTotalAmount=0;
                }
                $subTotalAmount   += ($data['qty'] * $data['unit_price']);
                $totalAmount      += ($data['qty'] * $data['unit_price']);
                $grandTotalAmount += ($data['qty'] * $data['unit_price']);
                $oldCustomerId    = $data['patient_id'];
                $oldCustomerName  = $data['patient_name'];
            }
            if(mysql_num_rows($query)){ 
                $excelContent .= "\n".$index."\t".$oldCustomerName."\t\t\t\t".number_format($subTotalAmount, 2); ?>
            <tr>
                <td class="first"><?php echo $index++; ?></td>
                <td colspan="4"><?php echo $oldCustomerName; ?></td>
                <td style="text-align: right;"><?php echo number_format($subTotalAmount, 2); ?></td>
            </tr>
            <?php $excelContent .= "\n".'Total Service'."\t\t\t\t\t".number_format($totalAmount, 2); ?>
            <tr style="font-weight: bold;">
                <td class="first" colspan="5" style="font-size: 14px;">Total Service</td>
                <td style="text-align: right;font-size: 14px;text-decoration: underline;"><?php echo number_format($totalAmount, 2); ?></td>
            </tr>
            <?php 
            } 
            $excelContent .= "\n\n".'Miscellaneous'; ?>
            <tr><td colspan="6">&nbsp;</td></tr>
            <tr style="font-weight: bold;"><td class="first" colspan="6" style="font-size: 14px;">Miscellaneous</td></tr>
            <?php
            $index=1;
            $oldCustomerId='';
            $oldCustomerName='';
            $subTotalAmount=0;
            $totalAmount=0;
            $query=mysql_query("SELECT
                                    'Invoice' AS trans_type,
                                    patient_id,
                                    (SELECT CONCAT_WS(' ',patient_code, patient_name) FROM patients WHERE id=patient_id) AS patient_name,
                                    order_date,
                                    so_code AS code,
                                    description,
                                    (SELECT name FROM location_groups WHERE id=location_group_id) AS location_name,
                                    ". ($col[10] != ''?($col[10] != '1'?'qty':'qty_free'):'qty')." AS qty,
                                    (SELECT name FROM uoms WHERE id=qty_uom_id) AS qty_uom_name,
                                    unit_price,
                                    total_price
                                FROM sales_orders
                                    INNER JOIN sales_order_miscs ON sales_orders.id=sales_order_miscs.sales_order_id
                                WHERE " . $condition . "
                                UNION ALL
                                SELECT
                                    'Credit Memo' AS trans_type,
                                    patient_id,
                                    (SELECT CONCAT_WS(' ',patient_code, patient_name) FROM patients WHERE id=patient_id) AS patient_name,
                                    order_date,
                                    cm_code AS code,
                                    description,
                                    (SELECT name FROM location_groups WHERE id=location_group_id) AS location_name,
                                    ". ($col[10] != ''?($col[10] != '1'?'(qty*-1)':'(qty_free * -1)'):'((qty + qty_free) * -1)')." AS qty,
                                    (SELECT name FROM uoms WHERE id=qty_uom_id) AS qty_uom_name,
                                    unit_price,
                                    total_price
                                FROM credit_memos
                                    INNER JOIN credit_memo_miscs ON credit_memos.id=credit_memo_miscs.credit_memo_id
                                WHERE " . str_replace('is_pos=0 AND','',$condition) . "
                                ORDER BY patient_name,order_date");
            while($data=mysql_fetch_array($query)){
                $arrCode[] = $data['code'];
                $arrProduct[] = $data['description'];
                $arrLocation[] = $data['location_name'];
                if($data['patient_id']!=$oldCustomerId){ 
                    if($oldCustomerName!=''){ 
                        $excelContent .= "\n".$index."\t".$oldCustomerName."\t\t\t\t".number_format($subTotalAmount, 2); ?>
                <tr>
                    <td class="first"><?php echo $index++; ?></td>
                    <td colspan="4"><?php echo $oldCustomerName; ?></td>
                    <td style="text-align: right;"><?php echo number_format($subTotalAmount, 2); ?></td>
                </tr>
                <?php
                    }
                    $subTotalAmount=0;
                }
                $subTotalAmount   += ($data['qty'] * $data['unit_price']);
                $totalAmount      += ($data['qty'] * $data['unit_price']);
                $grandTotalAmount += ($data['qty'] * $data['unit_price']);
                $oldCustomerId    = $data['patient_id'];
                $oldCustomerName  = $data['patient_name'];
            }
            if(mysql_num_rows($query)){ 
                $excelContent .= "\n".$index."\t".$oldCustomerName."\t\t\t\t".number_format($subTotalAmount, 2); ?>
            <tr>
                <td class="first"><?php echo $index++; ?></td>
                <td colspan="4"><?php echo $oldCustomerName; ?></td>
                <td style="text-align: right;"><?php echo number_format($subTotalAmount, 2); ?></td>
            </tr>
            <?php $excelContent .= "\n".'Total Miscellaneous'."\t\t\t\t\t".number_format($totalAmount, 2); ?>
            <tr style="font-weight: bold;">
                <td class="first" colspan="5" style="font-size: 14px;">Total Miscellaneous</td>
                <td style="text-align: right;font-size: 14px;text-decoration: underline;"><?php echo number_format($totalAmount, 2); ?></td>
            </tr>
            <?php 
            } 
        } 
        $excelContent .= "\n\n".'Grand Total Amount'."\t\t".sizeof(array_unique($arrCode))."\t".sizeof(array_unique($arrProduct))."\t".sizeof(array_unique($arrLocation))."\t".number_format($grandTotalAmount, 2); ?>
        <tr><td colspan="6">&nbsp;</td></tr>
        <tr style="font-weight: bold;">
            <td class="first" colspan="2" style="font-size: 14px;">Grand Total Amount</td>
            <td style="text-align: center;font-size: 14px;text-decoration: underline;"><?php echo sizeof(array_unique($arrCode)); ?></td>
            <td style="text-align: center;font-size: 14px;text-decoration: underline;"><?php echo sizeof(array_unique($arrProduct)); ?></td>
            <td style="text-align: center;font-size: 14px;text-decoration: underline;"><?php echo sizeof(array_unique($arrLocation)); ?></td>
            <td style="text-align: right;font-size: 14px;text-decoration: underline;"><?php echo number_format($grandTotalAmount, 2); ?></td>
        </tr>
    </table>
</div>
<br />
<div class="buttons">
    <button type="button" id="<?php echo $btnPrint; ?>" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/>
        <?php echo ACTION_PRINT; ?>
    </button>
    <button type="button" id="<?php echo $btnExport; ?>" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/csv.png" alt=""/>
        <?php echo ACTION_EXPORT_TO_EXCEL; ?>
    </button>
</div>
<div style="clear: both;"></div>
<?php

$excelContent = chr(255).chr(254).@mb_convert_encoding($excelContent, 'UTF-16LE', 'UTF-8');
fwrite($fp,$excelContent);
fclose($fp);

?>