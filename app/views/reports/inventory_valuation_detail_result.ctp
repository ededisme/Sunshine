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
$filename="public/report/inventory_valuation_detail.csv";
$fp=fopen($filename,"wb");
$excelContent = '';

?>
<script type="text/javascript">
    $(document).ready(function(){
        $(".btnPrintSalesOrderInvoice").click(function(event){
            event.preventDefault();
            url = "<?php echo $this->base . '/point_of_sales'; ?>/printReceipt/"+$(this).attr("rel");
            $.ajax({
                type: "POST",
                url: url,
                beforeSend: function(){
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(printResult){
                    w=window.open();
                    w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                    w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                    w.document.write(printResult);
                    w.document.close();
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                }
            });
        });

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
            window.open("<?php echo $this->webroot; ?>public/report/inventory_valuation_detail.csv", "_blank");
        });
    });
</script>
<div id="<?php echo $printArea; ?>">
    <?php
    $msg = '<b style="font-size: 18px;">' . MENU_PRODUCT_INVENTORY_VALUATION_DETAIL . '</b><br /><br />';
    $excelContent .= MENU_PRODUCT_INVENTORY_VALUATION_DETAIL."\n\n";
    if($_POST['date_from']!='') {
        $msg .= REPORT_FROM.': '.$_POST['date_from'];
        $excelContent .= REPORT_FROM.': '.$_POST['date_from'];
    }
    if($_POST['date_to']!='') {
        $msg .= ' '.REPORT_TO.': '.$_POST['date_to'];
        $excelContent .= ' '.REPORT_TO.': '.$_POST['date_to']."\n\n";
    }
    echo $this->element('/print/header-report',array('msg'=>$msg));
    $excelContent .= TABLE_NO."\t".GENERAL_TYPE."\t".TABLE_DATE."\t".TABLE_NAME."\t".TABLE_QTY."\t".'Cost'."\t".'On Hand'."\t".'Avg Cost'."\t".'Asset Value';
    ?>
    <table id="<?php echo $tblName; ?>" class="table_report">
        <tr>
            <th class="first"><?php echo TABLE_NO; ?></th>
            <th style="width: 140px !important;"><?php echo GENERAL_TYPE; ?></th>
            <th style="width: 80px !important;"><?php echo TABLE_DATE; ?></th>
            <th><?php echo TABLE_NAME; ?></th>
            <th style="width: 110px !important;"><?php echo TABLE_UOM; ?></th>
            <th style="width: 80px !important;"><?php echo TABLE_QTY; ?></th>
            <th style="width: 120px !important;">Cost</th>
            <th style="width: 120px !important;">On Hand</th>
            <th style="width: 120px !important;">Avg Cost</th>
            <th style="width: 120px !important;">Asset Value</th>
        </tr>
        <?php
        // general condition
        $col = implode(',', $_POST);
        $col = explode(",", $col);
        $condition = 'inventory_valuations.is_active=1';
        if ($col[1] != '') {
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= '"' . dateConvert($col[1]) . '" <= DATE(inventory_valuations.date)';
        }
        if ($col[2] != '') {
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= '"' . dateConvert($col[2]) . '" >= DATE(inventory_valuations.date)';
        }
        if ($col[3] != '') {
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= 'inventory_valuations.company_id =' . $col[3];
        }else{
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= 'inventory_valuations.company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')';
        }
        if ($col[4] != '') {
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= 'inventory_valuations.pid=' . $col[4];
        }
        ?>
        
        <?php $excelContent .= "\n".'Inventory'; ?>
        <tr style="font-weight: bold;"><td class="first" colspan="9" style="font-size: 14px;">Inventory</td></tr>
        <?php
        $index=1;
        $oldProductId='';
        $oldProductName='';
        $onHand=0;
        $onHandSmall=0;
        $totalOnHand=0;
        $assetValue=0;
        $totalAssetValue=0;
        $query=mysql_query("SELECT
                                inventory_valuations.type,
                                inventory_valuations.date,
                                inventory_valuations.pid,
                                inventory_valuations.small_qty,
                                inventory_valuations.qty,
                                inventory_valuations.cost,
                                inventory_valuations.price,
                                inventory_valuations.on_hand,
                                inventory_valuations.on_hand_small,
                                inventory_valuations.avg_cost,
                                inventory_valuations.asset_value,
                                inventory_valuations.is_var_cost,
                                inventory_valuations.is_adjust_value,
                                CONCAT_WS(' ',products.code,'-',products.name) AS product_name,
                                uoms.name AS uom_name
                            FROM inventory_valuations 
                            INNER JOIN products ON products.id = inventory_valuations.pid
                            INNER JOIN uoms ON uoms.id = products.price_uom_id
                            WHERE " . $condition . "
                            ORDER BY product_name,inventory_valuations.date,inventory_valuations.created,inventory_valuations.id");
        while($data=mysql_fetch_array($query)){
            if($data['pid']!=$oldProductId){ 
                if($oldProductName!=''){ 
                    $excelContent .= "\n".'Total '.$oldProductName."\t\t\t\t\t\t\t".$onHandSmall."\t\t".number_format($assetValue,2); ?>
            <tr style="font-weight: bold;">
                <td class="first" colspan="7">Total <?php echo $oldProductName; ?></td>
                <td style="text-align: right;border-top: 1px solid #000;"><?php echo $onHandSmall; ?></td>
                <td></td>
                <td style="text-align: right;border-top: 1px solid #000;"><?php echo number_format($assetValue,2); ?></td>
            </tr>
            <?php
                    $totalOnHand+=$onHandSmall;
                    $totalAssetValue+=$assetValue;
                    $index=1;
                }
                $excelContent .= "\n".$data['product_name']; ?>
            <tr>
                <td class="first" colspan="10" style="font-weight: bold;"><?php echo $data['product_name']; ?></td>
            </tr>
            <?php 
            } 
            $excelContent .= "\n".$index."\t".$data['type']."\t".dateShort($data['date'])."\t\t".$data['uom_name']."\t".number_format($data['qty'], 5)."\t".($data['type']=='Credit'?number_format($data['qty']*$data['price'],2):($data['is_var_cost']!=1 && $data['is_adjust_value']!=1?number_format($data['qty']*$data['cost'],2):''))."\t".number_format($data['on_hand'], 5)."\t".number_format($data['avg_cost'],2)."\t".number_format($data['asset_value'],2); ?>
            <tr>
                <td class="first"><?php echo $index++; ?></td>
                <td><?php echo $data['type']; ?></td>
                <td><?php echo dateShort($data['date']); ?></td>
                <td></td>
                <td style="text-align: center;"><?php echo $data['uom_name']; ?></td>
                <td style="text-align: right;<?php echo $data['qty']<0?'color: #f00;':''; ?>"><?php echo number_format($data['qty'], 5); ?></td>
                <?php $cost=$data['type']=='Credit'?number_format($data['qty']*$data['price'],2):($data['is_var_cost']!=1 && $data['is_adjust_value']!=1?number_format($data['qty']*$data['cost'],2):''); ?>
                <td style="text-align: right;<?php echo $cost<0?'color: #f00;':''; ?>"><?php echo $cost; ?></td>
                <td style="text-align: right;"><?php echo number_format($data['on_hand'], 5); ?></td>
                <td style="text-align: right;"><?php echo number_format($data['avg_cost'], 6); ?></td>
                <td style="text-align: right;"><?php echo number_format($data['asset_value'], 2); ?></td>
            </tr>
            <?php
            $onHand=$data['on_hand'];
            $onHandSmall = $data['on_hand_small'];
            $assetValue=$data['asset_value'];
            $oldProductId=$data['pid'];
            $oldProductName=$data['product_name'];
        } 
        $totalOnHand+=$onHandSmall;
        $totalAssetValue+=$assetValue;
        if(mysql_num_rows($query)){ 
            $excelContent .= "\n".'Total '.$oldProductName."\t\t\t\t\t\t\t".number_format($onHand, 2)."\t\t".number_format($assetValue,2); ?>
        <tr style="font-weight: bold;">
            <td class="first" colspan="7">Total <?php echo $oldProductName; ?></td>
            <td style="text-align: right;border-top: 1px solid #000;"><?php echo number_format($onHand, 2); ?></td>
            <td></td>
            <td style="text-align: right;border-top: 1px solid #000;"><?php echo number_format($assetValue, 2); ?></td>
        </tr>
        <tr><td colspan="9">&nbsp;</td></tr>
        <?php $excelContent .= "\n\n".'Total Inventory'."\t\t\t\t\t\t\t".number_format($totalOnHand, 2)."\t\t".number_format($totalAssetValue,2); ?>
        <tr style="font-weight: bold;">
            <td class="first" colspan="7" style="font-size: 14px;">Total Inventory</td>
            <td style="text-align: right;font-size: 14px;text-decoration: underline;"><?php echo number_format($totalOnHand, 2); ?></td>
            <td></td>
            <td style="text-align: right;font-size: 14px;text-decoration: underline;"><?php echo number_format($totalAssetValue,2); ?></td>
        </tr>
        <?php 
        } 
        $excelContent .= "\n\n".'TOTAL'."\t\t\t\t\t\t\t".number_format($totalOnHand, 2)."\t\t".number_format($totalAssetValue,2); ?>
        <tr><td colspan="10">&nbsp;</td></tr>
        <tr style="font-weight: bold;">
            <td class="first" colspan="7" style="font-size: 14px;">TOTAL</td>
            <td style="text-align: right;font-size: 14px;text-decoration: underline;"><?php echo number_format($totalOnHand, 2); ?></td>
            <td></td>
            <td style="text-align: right;font-size: 14px;text-decoration: underline;"><?php echo number_format($totalAssetValue,2); ?></td>
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