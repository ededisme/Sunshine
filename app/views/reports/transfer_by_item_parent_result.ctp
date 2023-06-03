<?php
include('includes/function.php');
$rnd = rand();
$tblName = "tbl" . rand();
$printArea = "printArea" . $rnd;
$btnPrint = "btnPrint" . $rnd;
$btnExport = "btnExport" . $rnd;
/**
 * export to excel
 */
$filename="public/report/transfer_by_item_parent_" . $this->Session->id(session_id()) . ".csv";
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
            window.open("<?php echo $this->webroot; ?>public/report/transfer_by_item_parent_<?php echo $this->Session->id(session_id()); ?>.csv", "_blank");
        });
    });
</script>
<div id="<?php echo $printArea; ?>">
    <?php
    $msg = '<b style="font-size: 18px;">' . MENU_REPORT_TRANSFER_ORDER_BY_ITEM . '</b><br /><br />';
    $excelContent .= MENU_REPORT_TRANSFER_ORDER_BY_ITEM."\n\n";
    if($_POST['date_from']!='') {
        $msg .= REPORT_FROM.': '.$_POST['date_from'];
        $excelContent .= REPORT_FROM.': '.$_POST['date_from'];
    }
    if($_POST['date_to']!='') {
        $msg .= ' '.REPORT_TO.': '.$_POST['date_to'];
        $excelContent .= ' '.REPORT_TO.': '.$_POST['date_to']."\n\n";
    }
    echo $this->element('/print/header-report',array('msg'=>$msg));
    $excelContent .= TABLE_NO."\t".TABLE_NAME."\t".TABLE_TO_NUMBER."\t".TABLE_CUSTOMER."\t".TABLE_LOCATION."\t".TABLE_TOTAL_QTY;
    ?>
    <table id="<?php echo $tblName; ?>" class="table_report">
        <tr>
            <th class="first"><?php echo TABLE_NO; ?></th>
            <th><?php echo TABLE_NAME; ?></th>
            <th><?php echo TABLE_TO_NUMBER; ?></th>
            <th style="width: 160px !important;"><?php echo TABLE_FROM_WAREHOUSE; ?></th>
            <th style="width: 160px !important;"><?php echo TABLE_TO_WAREHOUSE; ?></th>
            <th style="width: 120px !important;"><?php echo TABLE_TOTAL_QTY; ?></th>
        </tr>
        <?php
        // General Condition
        $col = implode(',', $_POST);
        $col = explode(",", $col);
        $condition = 'status!=-1';
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
            $condition .= 'company_id = ' . $col[4];
        } else {
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= 'company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')';
        }
        if ($col[5] != '') {
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= 'branch_id = ' . $col[5];
        }else{
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= 'branch_id IN (SELECT branch_id FROM user_branches WHERE user_id = '.$user['User']['id'].')';
        }
        if ($col[6] != '') {
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= 'from_location_group_id=' . $col[6];
        }else{
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= 'from_location_group_id IN (SELECT location_group_id FROM user_location_groups WHERE user_id = '.$user['User']['id'].')';
        }
        if ($col[7] != '') {
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= 'to_location_group_id=' . $col[7];
        } else {
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= 'to_location_group_id IN (SELECT location_group_id FROM user_location_groups WHERE user_id = '.$user['User']['id'].')';
        }
        if ($col[11] != '') {
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= 'created_by=' . $col[11];
        }
        $excelContent .= "\n".'Product'; ?>
        <tr style="font-weight: bold;"><td class="first" colspan="6" style="font-size: 14px;">Product</td></tr>
        <?php
        $index=1;
        $arrCode=array();
        $arrLocationFrom=array();
        $arrLocationTo=array();
        $oldParentId='';
        $oldParentName='';
        $oldProductId='';
        $oldProductName='';
        $subTotalParentQty=0;
        $query=mysql_query("SELECT
                                transfer_orders.id,
                                (SELECT parent_id FROM products WHERE id=product_id) AS parent_id,
                                (SELECT CONCAT_WS(' ',code,'-',name) FROM products WHERE id=(SELECT parent_id FROM products WHERE id=transfer_order_details.product_id)) AS parent_name,
                                order_date,
                                to_code AS code,
                                from_location_group_id AS location_from_name,
                                to_location_group_id AS location_to_name,
                                (qty * conversion) AS qty
                            FROM transfer_orders
                                INNER JOIN transfer_order_details ON transfer_orders.id=transfer_order_details.transfer_order_id
                            WHERE "
                                . $condition
                                . ($col[8] != ''?' AND product_id IN (SELECT product_id FROM product_pgroups WHERE pgroup_id=' . $col[8] . ')':'')
                                . ($col[9] != ''?' AND (SELECT parent_id FROM products WHERE id=product_id)=' . $col[9] :'')
                                . ($col[10] != ''?' AND product_id=' . $col[10] :'')
                                . "
                            ORDER BY parent_name,order_date");
        while($data=mysql_fetch_array($query)){
            $arrCode[] = $data['code'];
            $arrLocationFrom[] = $data['location_from_name'];
            $arrLocationTo[] = $data['location_to_name'];
            if($data['parent_id']!=$oldParentId){ 
                if($oldParentName!=''){ 
                    $excelContent .= "\n".$index."\t".$oldParentName."\t\t\t\t".number_format($subTotalParentQty,0); ?>
            <tr>
                <td class="first"><?php echo $index++; ?></td>
                <td colspan="4"><?php echo $oldParentName; ?></td>
                <td style="text-align: right;"><?php echo number_format($subTotalParentQty,0); ?></td>
            </tr>
        <?php
               }
            $subTotalParentQty=0;
            }
            $subTotalParentQty+=$data['qty'];
            $oldParentId=$data['parent_id'];
            $oldParentName=$data['parent_name'];
        }
        if(mysql_num_rows($query)){ 
            $excelContent .= "\n".$index."\t".$oldParentName."\t\t\t\t".number_format($subTotalParentQty,0); ?>
        <tr>
            <td class="first"><?php echo $index++; ?></td>
            <td colspan="4"><?php echo $oldParentName; ?></td>
            <td style="text-align: right;"><?php echo number_format($subTotalParentQty,0); ?></td>
        </tr>
        <?php 
        } 
        $excelContent .= "\n\n\t\t".sizeof(array_unique($arrCode))."\t".sizeof(array_unique($arrLocationFrom))."\t".sizeof(array_unique($arrLocationTo)); ?>
        <tr style="font-weight: bold;">
            <td class="first" colspan="2" style="font-size: 14px;"></td>
            <td style="text-align: center;font-size: 14px;text-decoration: underline;"><?php echo sizeof(array_unique($arrCode)); ?></td>
            <td style="text-align: center;font-size: 14px;text-decoration: underline;"><?php echo sizeof(array_unique($arrLocationFrom)); ?></td>
            <td style="text-align: center;font-size: 14px;text-decoration: underline;"><?php echo sizeof(array_unique($arrLocationTo)); ?></td>
            <td></td>
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