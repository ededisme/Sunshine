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
$filename="public/report/inventory_valuation.csv";
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
            window.open("<?php echo $this->webroot; ?>public/report/inventory_valuation.csv", "_blank");
        });
    });
</script>
<div id="<?php echo $printArea; ?>">
    <?php
    $msg = '<b style="font-size: 18px;">' . MENU_PRODUCT_INVENTORY_VALUATION_SUMMARY . '</b><br /><br />';
    $excelContent .= MENU_PRODUCT_INVENTORY_VALUATION_SUMMARY."\n\n";
    if($_POST['date_from']!='') {
        $msg .= REPORT_FROM.': '.$_POST['date_from'];
        $excelContent .= REPORT_FROM.': '.$_POST['date_from'];
    }
    if($_POST['date_to']!='') {
        $msg .= ' '.REPORT_TO.': '.$_POST['date_to'];
        $excelContent .= ' '.REPORT_TO.': '.$_POST['date_to']."\n\n";
    }
    echo $this->element('/print/header-report',array('msg'=>$msg));
    $excelContent .= TABLE_NO."\t".TABLE_NAME."\t".'On Hand'."\t".'Avg Cost'."\t".'Asset Value'."\t".'% of Tot Asset'."\t".'Sales Price'."\t".'Retail Value'."\t".'% of Tot Retail';
    ?>
    <table id="<?php echo $tblName; ?>" class="table_report">
        <tr>
            <th class="first"><?php echo TABLE_NO; ?></th>
            <th><?php echo TABLE_NAME; ?></th>
            <th><?php echo TABLE_UOM; ?></th>
            <th>On Hand</th>
            <th>Avg Cost</th>
            <th>Asset Value</th>
            <th>% of Tot Asset</th>
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
        // query total
        $oldProductId='';
        $oldProductName='';
        $onHand=0;
        $onHandSmall=0;
        $totalOnHand=0;
        $avgCost=0;
        $assetValue=0;
        $totalAssetValue=0;
        $query=mysql_query("SELECT
                                pid,
                                on_hand,
                                on_hand_small,
                                avg_cost,
                                asset_value,
                                (SELECT CONCAT_WS(' ',code,'-',name) FROM products WHERE id=pid) AS product_name
                            FROM inventory_valuations
                            WHERE " . $condition . "
                            ORDER BY product_name,date,created,id");
        while($data=mysql_fetch_array($query)){
            if($data['pid']!=$oldProductId){
                if($oldProductName!=''){
                    $totalOnHand+=$onHandSmall;
                    $totalAssetValue += $assetValue;
                }
            }
            $onHand=$data['on_hand'];
            $onHandSmall = $data['on_hand_small'];
            $avgCost=$data['avg_cost'];
            $assetValue=$data['asset_value'];
            $oldProductId=$data['pid'];
            $oldProductName=$data['product_name'];
        }
        $totalOnHand+=$onHandSmall;
        $totalAssetValue+=$assetValue;
        // result
        $asset=$totalAssetValue;
        $index=1;
        $oldProductId='';
        $oldProductName='';
        $oldUomName='';
        $onHand=0;
        $onHandSmall=0;
        $totalOnHand=0;
        $avgCost=0;
        $assetValue=0;
        $totalAssetValue=0;
        $query=mysql_query("SELECT
                                inventory_valuations.pid,
                                inventory_valuations.on_hand,
                                inventory_valuations.on_hand_small,
                                inventory_valuations.avg_cost,
                                inventory_valuations.asset_value,
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
                    $excelContent .= "\n".$index."\t".$oldProductName."\t".$oldUomName."\t".($onHand>0?$onHand:0)."\t".$avgCost."\t".($assetValue>0?$assetValue:0)."\t".((($assetValue>0?$assetValue:0)/($asset>0?$asset:1))*100).'%'; ?>
            <tr>
                <td class="first"><?php echo $index++; ?></td>
                <td><?php echo $oldProductName; ?></td>
                <td style="text-align: center;"><?php echo $oldUomName; ?></td>
                <td style="text-align: right;"><?php echo number_format($onHand, 5); ?></td>
                <td style="text-align: right;"><?php echo number_format($avgCost, 6); ?></td>
                <td style="text-align: right;"><?php echo number_format(($assetValue>0?$assetValue:0),2); ?></td>
                <td style="text-align: right;"><?php echo number_format((($assetValue>0?$assetValue:0)/($asset>0?$asset:1))*100,2).'%'; ?></td>
            </tr>
            <?php
                    $totalOnHand+=($onHandSmall>0?$onHandSmall:0);
                    $totalAssetValue+=$assetValue;
                }
            }
            $onHand=$data['on_hand'];
            $onHandSmall = $data['on_hand_small'];
            $avgCost=$data['avg_cost'];
            $assetValue=$data['asset_value'];
            $oldProductId=$data['pid'];
            $oldProductName=$data['product_name'];
            $oldUomName=$data['uom_name'];
        } 
        $totalOnHand+=$onHandSmall;
        $totalAssetValue+=$assetValue;
        if(mysql_num_rows($query)){ 
            $excelContent .= "\n".$index."\t".$oldProductName."\t".$oldUomName."\t".number_format($onHand, 5)."\t".$avgCost."\t".$assetValue."\t".(($assetValue/($asset>0?$asset:1))*100).'%'; ?>
        <tr>
            <td class="first"><?php echo $index++; ?></td>
            <td><?php echo $oldProductName; ?></td>
            <td style="text-align: center;"><?php echo $oldUomName; ?></td>
            <td style="text-align: right;"><?php echo number_format($onHand, 5); ?></td>
            <td style="text-align: right;"><?php echo number_format($avgCost, 6); ?></td>
            <td style="text-align: right;"><?php echo number_format($assetValue,2); ?></td>
            <td style="text-align: right;"><?php echo number_format(($assetValue/($asset>0?$asset:1))*100,2).'%'; ?></td>
        </tr>
        <?php $excelContent .= "\n".'Total Inventory'."\t\t\t".number_format($totalOnHand, 2)."\t\t".number_format($totalAssetValue,2)."\t"; ?>
        <tr style="font-weight: bold;">
            <td class="first" colspan="3" style="font-size: 14px;">Total Inventory</td>
            <td style="text-align: right;font-size: 14px;text-decoration: underline;"><?php echo number_format($totalOnHand, 2); ?></td>
            <td style="text-align: right;font-size: 14px;text-decoration: underline;"></td>
            <td style="text-align: right;font-size: 14px;text-decoration: underline;"><?php echo number_format($totalAssetValue,2); ?></td>
            <td style="text-align: right;font-size: 14px;text-decoration: underline;"></td>
        </tr>
        <?php } ?>

        <?php $excelContent .= "\n\n".'TOTAL'."\t\t\t".number_format($totalOnHand, 2)."\t\t".number_format($totalAssetValue,2)."\t"; ?>
        <tr><td colspan="9">&nbsp;</td></tr>
        <tr style="font-weight: bold;">
            <td class="first" colspan="3" style="font-size: 14px;">TOTAL</td>
            <td style="text-align: right;font-size: 14px;text-decoration: underline;"><?php echo number_format($totalOnHand, 2); ?></td>
            <td style="text-align: right;font-size: 14px;text-decoration: underline;"></td>
            <td style="text-align: right;font-size: 14px;text-decoration: underline;"><?php echo number_format($totalAssetValue,2); ?></td>
            <td style="text-align: right;font-size: 14px;text-decoration: underline;"></td>
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