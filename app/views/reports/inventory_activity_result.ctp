<?php

$rnd = rand();
$tblName = "tbl" . rand();
$printArea = "printArea" . $rnd;
$btnPrint = "btnPrint" . $rnd;
$btnExport = "btnExport" . $rnd;

include('includes/function.php');
$sqlSettingUomDeatil = mysql_query("SELECT uom_detail_option, calculate_cogs FROM setting_options");
$rowSettingUomDetail = mysql_fetch_array($sqlSettingUomDeatil);
/**
 * export to excel
 */
$filename="public/report/inventory_activity_with_global_detail_" . $this->Session->id(session_id()) . ".csv";
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
            window.open("<?php echo $this->webroot; ?>public/report/inventory_activity_with_global_detail_<?php echo $this->Session->id(session_id()); ?>.csv", "_blank");
        });
    });
</script>
<div id="<?php echo $printArea; ?>">
    <?php
    $msg = '<b style="font-size: 18px;">' . MENU_PRODUCT_INVENTORY_ACTIVITY . '</b><br /><br />';
    $excelContent .= MENU_PRODUCT_INVENTORY_ACTIVITY."\n\n";
    if($_POST['date_from']!='') {
        $msg .= REPORT_FROM.': '.$_POST['date_from'];
        $excelContent .= REPORT_FROM.': '.$_POST['date_from'];
    }
    if($_POST['date_to']!='') {
        $msg .= ' '.REPORT_TO.': '.$_POST['date_to'];
        $excelContent .= ' '.REPORT_TO.': '.$_POST['date_to'];
    }
    if($_POST['location_group_id']!='' || $_POST['location_id']!='' || $_POST['pgroup_id']!='') {
        $msg .= '<br /><br />';
        $excelContent .= "\n\n";
    }
    if($_POST['location_group_id']!='') {
        $query=mysql_query("SELECT name FROM location_groups WHERE id=".$_POST['location_group_id']);
        $data=mysql_fetch_array($query);
        $msg .= '<b>'.TABLE_LOCATION_GROUP.'</b>: '.$data[0];
        $excelContent .= TABLE_LOCATION_GROUP.': '.$data[0];
    }
    if($_POST['location_id']!='') {
        $query=mysql_query("SELECT name FROM locations WHERE id=".$_POST['location_id']);
        $data=mysql_fetch_array($query);
        $msg .= ' <br/><b>'.TABLE_LOCATION.'</b>: '.$data[0];
        $excelContent .= TABLE_LOCATION.': '.$data[0];
    }
    if($_POST['pgroup_id']!='') {
        $query=mysql_query("SELECT name FROM pgroups WHERE id=".$_POST['pgroup_id']);
        $data=mysql_fetch_array($query);
        $msg .= ' <br/><b>'.GENERAL_TYPE.'</b>: '.$data[0];
        $excelContent .= GENERAL_TYPE.': '.$data[0];
    }
    $msg .= '<br /><br />';
    $excelContent .= "\n\n";
    echo $this->element('/print/header-report',array('msg'=>$msg));
    $excelColLot = "";
    if($rowSettingUomDetail[0] == 1){
        $excelColLot = "\t".TABLE_LOTS_NO;
    }
    $excelContent .= TABLE_NO."\t".TABLE_SKU."\t".TABLE_LOCATION.$excelColLot."\t".TABLE_EXPIRED_DATE."\t".TABLE_TOTAL_QTY."\t".TABLE_UOM;
    ?>
    <table id="<?php echo $tblName; ?>" class="table_report">
        <tr>
            <th class="first"><?php echo TABLE_NO; ?></th>
            <th style="width: 70px !important;"><?php echo TABLE_SKU; ?></th>
            <th><?php echo TABLE_LOCATION; ?></th>
            <th style="<?php if($rowSettingUomDetail[0] == 0){ ?>display: none;<?php } ?>"><?php echo TABLE_LOTS_NO; ?></th>
            <th style="width: 120px !important;"><?php echo TABLE_EXPIRED_DATE; ?></th>
            <th style="width: 80px !important;"><?php echo TABLE_TOTAL_QTY; ?></th>
            <th style="width: 60px !important;"><?php echo TABLE_UOM; ?></th>
        </tr>
        <?php
        // general condition
        $col = implode(',', $_POST);
        $col = explode(",", $col);
        $conPGroup  = "";
        $conParent  = "";
        $conVendor  = "";
        $conCompany = "";
        $conProduct = "";
        $conditionGetLocation = "";
        // Get Location Group
        if ($col[3] != '') {
            $conditionGetProLoc    = $col[7] != ""?" WHERE product_id = ".$col[7]:"";
            $conditionGetLocation .= " id IN (SELECT location_id FROM ".$col[3]."_group_totals ".$conditionGetProLoc." GROUP BY location_id)";
        }else{
            $conditionGetLocation .= " location_group_id IN (SELECT user_location_groups.location_group_id FROM user_location_groups INNER JOIN location_groups ON location_groups.id = user_location_groups.location_group_id AND location_groups.location_group_type_id != 1 WHERE user_location_groups.user_id = ".$user['User']['id']." GROUP BY user_location_groups.location_group_id)";
        }
        
        // Get Location
        if ($col[4] != '') {
            $conditionGetLocation != '' ? $conditionGetLocation .= ' AND ' : '';
            $conditionGetLocation = "id = ".$col[4];
        }
        
        // Get Product Group
        if($col[5] != ""){
            // Get Product
            if($col[7] != ""){
                $conProduct = " AND product_id = ".$col[7];
            }
            $conPGroup = " AND products.id IN (SELECT product_id FROM product_pgroups WHERE pgroup_id = ".$col[5].$conProduct.")";
        }
        
        // Get Product Parent
        if($col[6] != ""){
            $conParent = " AND products.parent_id =".$col[6];
        }
        
        // Get Product
        if($col[7] != ""){
            if($conPGroup == ""){
                $conPGroup = " AND products.id IN (".$col[7].")";
            }
        }
        
        $joinProducts = " INNER JOIN products ON";
        $joinUom      = " INNER JOIN uoms ON uoms.id = products.price_uom_id";
        $tableDaily = "";
        $tableDailyBbi = "";
        $filedDailyBbi = "SUM((total_pb + total_cm + total_to_in + total_cycle + total_cus_consign_in + total_ven_consign_in) - (total_so + total_pbc + total_pos + total_to_out + total_cus_consign_out + total_ven_consign_out)) AS total_qty, SUM(total_pb) AS total_pb, SUM(total_cm) AS total_cm, SUM(total_to_in) AS total_to_in, SUM(total_cycle) AS total_cycle, SUM(total_so) AS total_so, SUM(total_pbc) AS total_pbc, SUM(total_pos) AS total_pos, SUM(total_to_out) AS total_to_out, SUM(total_cus_consign_in) AS total_cus_consign_in, SUM(total_cus_consign_out) AS total_cus_consign_out, SUM(total_ven_consign_in) AS total_ven_consign_in, SUM(total_ven_consign_out) AS total_ven_consign_out, product_id, location_id, lots_number, expired_date";
        $filedDaily = "SUM(total_pb) AS total_pb, SUM(total_cm) AS total_cm, SUM(total_to_in) AS total_to_in, SUM(total_cycle) AS total_cycle, SUM(total_so) AS total_so, SUM(total_pbc) AS total_pbc, SUM(total_pos) AS total_pos, SUM(total_to_out) AS total_to_out, SUM(total_cus_consign_in) AS total_cus_consign_in, SUM(total_cus_consign_out) AS total_cus_consign_out, SUM(total_ven_consign_in) AS total_ven_consign_in, SUM(total_ven_consign_out) AS total_ven_consign_out, uoms.name AS u_name, products.price_uom_id AS qty_uom_id, products.code AS product_code, products.name AS product_name, products.id AS product_id, location_id, (SELECT name FROM locations WHERE id = location_id) AS location_name, lots_number, expired_date";
        $conditionDaily = " products.is_active = 1 AND DayFilter ".$conPGroup.$conVendor.$conParent.$conCompany;
        $conditionDailyBbi = " products.is_active = 1 AND DayFilter AND changeId AND locationId AND lotsNumber AND expDate ".$conParent.$conCompany;
        // List Location 
        $queryLocationList = mysql_query('SELECT id AS location_id FROM locations WHERE ' . $conditionGetLocation . ' GROUP BY id');
        if(@mysql_num_rows($queryLocationList)){
            if(mysql_num_rows($queryLocationList) == 1){
                while($dataLocationList=mysql_fetch_array($queryLocationList)){
                    // Stock Daily Bigging
                    $tableDailyBbi .= "SELECT ".$filedDailyBbi." FROM ".$dataLocationList['location_id']."_inventory_total_details".$joinProducts." products.id = ".$dataLocationList['location_id']."_inventory_total_details.product_id WHERE".$conditionDailyBbi." GROUP BY ".$dataLocationList['location_id']."_inventory_total_details.location_id, ".$dataLocationList['location_id']."_inventory_total_details.lots_number, ".$dataLocationList['location_id']."_inventory_total_details.expired_date, ".$dataLocationList['location_id']."_inventory_total_details.product_id";
                    
                    // Stock Daily Ending
                    $tableDaily .= "SELECT ".$filedDaily." FROM ".$dataLocationList['location_id']."_inventory_total_details".$joinProducts." products.id = ".$dataLocationList['location_id']."_inventory_total_details.product_id".$joinUom." WHERE".$conditionDaily." GROUP BY ".$dataLocationList['location_id']."_inventory_total_details.location_id, ".$dataLocationList['location_id']."_inventory_total_details.lots_number, ".$dataLocationList['location_id']."_inventory_total_details.expired_date, ".$dataLocationList['location_id']."_inventory_total_details.product_id";
                }
            }else{
                $locId = 1;
                $tmpLocationId = 0;
                while($dataLocationList=mysql_fetch_array($queryLocationList)){
                    if($locId == 1){
                        // Stock Daily Bigging
                        $tableDailyBbi .= "SELECT ".$filedDailyBbi." FROM ".$dataLocationList['location_id']."_inventory_total_details".$joinProducts." products.id = ".$dataLocationList['location_id']."_inventory_total_details.product_id WHERE".$conditionDailyBbi." GROUP BY ".$dataLocationList['location_id']."_inventory_total_details.location_id, ".$dataLocationList['location_id']."_inventory_total_details.lots_number, ".$dataLocationList['location_id']."_inventory_total_details.expired_date, ".$dataLocationList['location_id']."_inventory_total_details.product_id";
                        
                        // Stock Daily Ending
                        $tableDaily .= "SELECT ".$filedDaily." FROM ".$dataLocationList['location_id']."_inventory_total_details".$joinProducts." products.id = ".$dataLocationList['location_id']."_inventory_total_details.product_id".$joinUom." WHERE ".$conditionDaily." GROUP BY ".$dataLocationList['location_id']."_inventory_total_details.location_id, ".$dataLocationList['location_id']."_inventory_total_details.lots_number, ".$dataLocationList['location_id']."_inventory_total_details.expired_date, ".$dataLocationList['location_id']."_inventory_total_details.product_id";
                    }else{
                        // Stock Daily Ending
                        $tableDailyBbi .= " UNION ALL SELECT ".$filedDailyBbi." FROM ".$dataLocationList['location_id']."_inventory_total_details".$joinProducts." products.id = ".$dataLocationList['location_id']."_inventory_total_details.product_id WHERE ".$conditionDailyBbi." GROUP BY ".$dataLocationList['location_id']."_inventory_total_details.location_id, ".$dataLocationList['location_id']."_inventory_total_details.lots_number, ".$dataLocationList['location_id']."_inventory_total_details.expired_date, ".$dataLocationList['location_id']."_inventory_total_details.product_id";
                        
                        // Stock Daily Ending
                        $tableDaily .= " UNION ALL SELECT ".$filedDaily." FROM ".$dataLocationList['location_id']."_inventory_total_details".$joinProducts." products.id = ".$dataLocationList['location_id']."_inventory_total_details.product_id".$joinUom." WHERE ".$conditionDaily." GROUP BY ".$dataLocationList['location_id']."_inventory_total_details.location_id, ".$dataLocationList['location_id']."_inventory_total_details.lots_number, ".$dataLocationList['location_id']."_inventory_total_details.expired_date, ".$dataLocationList['location_id']."_inventory_total_details.product_id";
                    }
                    $tmpLocationId = $dataLocationList['location_id'];
                    $locId++;
                }
            }
            $dateFrom = "date<'".dateConvert($col[1])."'";
            $dateTo   = "DATE(date)<='".dateConvert($col[2])."'";
            $tableDailyBeginning = str_replace("DayFilter", $dateFrom, $tableDailyBbi);
            $tableDailyEnding    = str_replace("DayFilter", $dateTo, $tableDaily);

            $sqlCmtDailyBiginning  = "SELECT SUM(total_qty) AS qty, SUM(total_pb) AS total_pb, SUM(total_cm) AS total_cm, SUM(total_to_in) AS total_to_in, SUM(total_cycle) AS total_cycle, SUM(total_so) AS total_so, SUM(total_pbc) AS total_pbc, SUM(total_pos) AS total_pos, SUM(total_to_out) AS total_to_out, SUM(total_cus_consign_in) AS total_cus_consign_in, SUM(total_cus_consign_out) AS total_cus_consign_out, SUM(total_ven_consign_in) AS total_ven_consign_in, SUM(total_ven_consign_out) AS total_ven_consign_out FROM (".$tableDailyBeginning.") AS stockDaily GROUP BY location_id, lots_number, expired_date, product_id ORDER BY product_id";
            $sqlCmtDailyEnding     = "SELECT SUM(total_pb) AS total_pb, SUM(total_cm) AS total_cm, SUM(total_to_in) AS total_to_in, SUM(total_cycle) AS total_cycle, SUM(total_so) AS total_so, SUM(total_pbc) AS total_pbc, SUM(total_pos) AS total_pos, SUM(total_to_out) AS total_to_out, SUM(total_cus_consign_in) AS total_cus_consign_in, SUM(total_cus_consign_out) AS total_cus_consign_out, SUM(total_ven_consign_in) AS total_ven_consign_in, SUM(total_ven_consign_out) AS total_ven_consign_out, u_name, qty_uom_id, product_id, product_code, product_name, location_id, (SELECT name FROM locations WHERE id = location_id) AS location_name, lots_number, expired_date FROM (".$tableDailyEnding.") AS stockDaily GROUP BY location_id, lots_number, expired_date, product_id ORDER BY product_id";
        } else {
            $sqlCmtDailyBiginning = '';
            $sqlCmtDailyEnding = '';
        }
        ?>

        <?php $excelContent .= "\n".'Product'; ?>
        <tr style="font-weight: bold;">
            <td class="first" colspan="7" style="font-size: 14px;">Product</td>
        </tr>
        <?php
        $index=1;
        $oldProductId='';
        $oldProductCode='';
        $oldProductName='';
        $subTotalBBI=0;
        $subTotalEBI=0;
        $subTotalParentQty=0;
        $subTotalParentBBI=0;
        $subTotalParentEBI=0;
        $totalBBI=0;
        $totalEBI=0;
        if($sqlCmtDailyEnding != ''){
            $query=mysql_query($sqlCmtDailyEnding);
            while($data=mysql_fetch_array($query)){
                if($data['product_id'] != $oldProductId){  
                    if($oldProductName != ''){
                        $excelContent .= "\n".'Total '.$oldProductName."\t\t\t\t".$subTotalParentEBI."\t\t"; 
            ?>
            <tr style="font-weight: bold;">
                <td class="first" colspan="4">Total <?php echo $oldProductName; ?></td>
                <td style="text-align: center;"><?php echo number_format($subTotalParentEBI, 0); ?></td>
                <td></td>
            </tr>
            <?php
                }
                $index=0;
                $subTotalParentQty=0;
                $subTotalParentBBI=0;
                $subTotalParentEBI=0;
            ?>
            <?php $excelContent .= "\n".$data['product_name']; ?>
            <tr>
                <td class="first" colspan="7" style="font-weight: bold;"><?php echo $data['product_name']; ?></td>
            </tr>
            <?php 
            } 
            // Smallest Uom
            $sqlUomSm = mysql_query("SELECT abbr FROM uoms WHERE id = IFNULL((SELECT to_uom_id FROM uom_conversions WHERE from_uom_id = ".$data['qty_uom_id']." AND is_small_uom = 1 AND is_active = 1 ORDER BY id DESC LIMIT 1), ".$data['qty_uom_id'].")");
            $rowUomSm = mysql_fetch_array($sqlUomSm);
            $small_label = $rowUomSm['abbr'];
            // Global Inventory
            $product_id = "products.id = ".$data['product_id'];
            $location_id = "location_id = ".$data['location_id'];
            $lots_id = "lots_number = '".$data['lots_number']."'";
            $exp = "expired_date = '".$data['expired_date']."'";
            $sqlCmtBiginning = str_replace("changeId",$product_id ,$sqlCmtDailyBiginning);
            $sqlCmtBiginning = str_replace("locationId",$location_id ,$sqlCmtBiginning);
            $sqlCmtBiginning = str_replace("lotsNumber",$lots_id ,$sqlCmtBiginning);
            $sqlCmtBiginning = str_replace("expDate",$exp ,$sqlCmtBiginning);
            $queryGlobal = mysql_query($sqlCmtBiginning);
            $dataGlobal  = mysql_fetch_array($queryGlobal);
            // Total BBI & EBI
            $subTotalBBI = $dataGlobal['qty']>0?$dataGlobal['qty']:0;
            $subTotalEBI = ($data['total_pb'] + $data['total_cm'] + $data['total_to_in'] + $data['total_cycle'] + $data['total_cus_consign_in'] + $data['total_ven_consign_in']) - ($data['total_so'] + $data['total_pbc'] + $data['total_pos'] + $data['total_to_out'] + $data['total_cus_consign_out'] + $data['total_ven_consign_out']);
            // Total EBI & Total
                $totalBBI                  += $subTotalBBI;
                $totalEBI                  += $subTotalEBI;
                $subTotalParentBBI         += $subTotalBBI;
                $subTotalParentEBI         += $subTotalEBI;
            $oldProductId=$data['product_id'];
            $oldProductCode=$data['product_code'];
            $oldProductName=$data['product_name'];
            $excelListLot = "";
            if($rowSettingUomDetail[0] == 1){
                $excelListLot = "\t".$data['lots_number'];
            }
            $expiredDate = "";
            if($data['expired_date'] != '' && $data['expired_date'] != '0000-00-00'){
                $expiredDate = dateShort($data['expired_date']); 
            }
            $excelContent .= "\n".$index++."\t".$oldProductCode."\t".$data['location_name'].$excelListLot."\t".$expiredDate."\t".$subTotalEBI."\t".$small_label; ?>
            <tr>
                <td class="first"><?php echo $index; ?></td>
                <td style="text-align: center;"><?php echo $oldProductCode; ?></td>
                <td style="text-align: center;"><?php echo $data['location_name']; ?></td>
                <td style="text-align: center; <?php if($rowSettingUomDetail[0] == 0){ ?>display: none;<?php } ?>"><?php echo $data['lots_number']; ?></td>
                <td style="text-align: center;"><?php echo $expiredDate; ?></td>
                <td style="text-align: center;"><?php echo number_format($subTotalEBI, 0); ?></td>
                <td style="text-align: center;"><?php echo $small_label; ?></td>
            </tr>
        <?php
            } 
            if(mysql_num_rows($query)){ 
                $excelContent .= "\n".'Total '.$oldProductName."\t\t\t\t".$subTotalParentEBI."\t\t";
        ?>
        <tr style="font-weight: bold;">
            <td class="first" colspan="4">Total <?php echo $oldProductName; ?></td>
            <td style="text-align: center;"><?php echo number_format($subTotalParentEBI, 0); ?></td>
            <td></td>
        </tr>
        <?php
                $excelContent .= "\n".'Total '."\t\t\t\t".$totalEBI."\t"; 
        ?>
        <tr style="font-weight: bold;">
            <td class="first" colspan="4" style="font-size: 14px;">Total</td>
            <td style="text-align: center;font-size: 14px;text-decoration: underline;"><?php echo number_format($totalEBI, 0); ?></td>
            <td></td>
        </tr>
        <?php 
            } 
        }
        ?>
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