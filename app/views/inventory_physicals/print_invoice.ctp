<?php
    include("includes/function.php");
    $sqlSettingUomDeatil = mysql_query("SELECT uom_detail_option FROM setting_options");
    $rowSettingUomDetail = mysql_fetch_array($sqlSettingUomDeatil);
?>
<style type="text/css" media="screen">
    div.print-footer {display: none;}
</style>
<style type="text/css" media="print">
    div.print_doc { width:100%;}
    #btnDisappearPrint { display: none;}
    div.print-footer {display: block; width:100%;} 
</style>
<div class="print_doc">
    <?php
    $queryCycleProduct = mysql_query("  SELECT *,
                                        (SELECT photo FROM companies WHERE id = inventory_physicals.company_id) AS company_name,
                                        (SELECT name FROM location_groups WHERE id=inventory_physicals.location_group_id) AS location_group_name
                                        FROM inventory_physicals WHERE id=" . $id);
    $dataCycleProduct = mysql_fetch_array($queryCycleProduct);
    $msg = MENU_SALES_MIX;
    echo $this->element('/print/header', array('msg' => $msg, 'barcode' => $dataCycleProduct['code'], 'address' => '', 'counterNumber' => '', 'logo' => $dataCycleProduct['company_name']));
    ?>
    <div style="height: 30px"></div>
    <table width="100%">
        <tr>
            <td style="width: 600px; vertical-align: top; font-size: 10px;">
                <?php echo TABLE_LOCATION_GROUP; ?>
                <b style="font-size: 10px;"><?php echo $dataCycleProduct['location_group_name']; ?><b/>
            </td>
            <td align="right" style="vertical-align: top; font-size: 10px;">
                <?php echo TABLE_CODE ?>: <b><?php echo $dataCycleProduct['code']; ?></b>
            </td>
        </tr>
        <tr>
            <td style="width: 600px; vertical-align: top; font-size: 10px;"></td>
            <td align="right" style="vertical-align: top; font-size: 10px;">
                <?php echo TABLE_DATE ?>: <b><?php echo dateShort($dataCycleProduct['date'], "d/M/Y"); ?></b>
            </td>
        </tr>
    </table>
    <br />
    <div>
        <div>
            <table class="table_print">
                <tr>
                    <th class="first" style="font-size: 10px;"><?php echo TABLE_NO; ?></th>
                    <th style="width: 80px !important; font-size: 10px;"><?php echo TABLE_CODE; ?></th>
                    <th style="width: 80px !important; font-size: 10px;"><?php echo TABLE_BARCODE; ?></th>
                    <th style="font-size: 10px;"><?php echo TABLE_NAME; ?></th>
                    <th style="width: 120px !important; font-size: 10px;"><?php echo TABLE_QTY_DIFFERENCE; ?></th>
                    <th style="width: 80px !important; font-size: 10px;"><?php echo TABLE_UOM; ?></th>
                    <th style="width: 100px !important; font-size: 10px; <?php if($rowSettingUomDetail[0] == 0){ ?>display: none;<?php } ?>"><?php echo TABLE_LOTS_NO; ?></th>
                    <th style="width: 100px !important; font-size: 10px;"><?php echo TABLE_LOCATION; ?></th>
                </tr>
                <?php
                $index=1;
                $queryDetail=mysql_query("  SELECT
                                                product_id,
                                                (SELECT code FROM products WHERE id=product_id) AS code,
                                                (SELECT barcode FROM products WHERE id=product_id) AS barcode,
                                                (SELECT name FROM products WHERE id=product_id) AS name,
                                                (SELECT abbr FROM uoms WHERE id=(SELECT price_uom_id FROM products WHERE id=product_id)) AS uom,
                                                location_id,
                                                lots_number,
                                                qty_diff
                                            FROM inventory_physical_details WHERE inventory_physical_id=".$dataCycleProduct['id']);
                while($dataDetail=mysql_fetch_array($queryDetail)){
                ?>
                <tr>
                    <td class="first" style="text-align: right; font-size: 10px;"><?php echo $index++; ?></td>
                    <td style="font-size: 10px;"><?php echo $dataDetail['code']; ?></td>
                    <td style="font-size: 10px;"><?php echo $dataDetail['barcode']; ?></td>
                    <td style="font-size: 10px;"><?php echo $dataDetail['name']; ?></td>
                    <?php
                        $value = $dataDetail['qty_diff'];
                        $product = mysql_query("SELECT price_uom_id, (SELECT abbr FROM uoms WHERE id = products.price_uom_id) AS uom_name FROM products WHERE id=" . $dataDetail['product_id']);
                        $row = mysql_fetch_array($product);
                        $mainUom  = $row['uom_name'];
                        $smallUom = 1;
                        $smallUomLabel = "";
                        $sqlSmUom = mysql_query("SELECT value, (SELECT abbr FROM uoms WHERE id = uom_conversions.to_uom_id) as abbr FROM uom_conversions WHERE from_uom_id = " . $row['price_uom_id'] . " AND is_small_uom = 1 AND is_active = 1");
                        while (@$d = mysql_fetch_array($sqlSmUom)) {
                            $smallUom = $d['value'];
                            $smallUomLabel = $d['abbr'];
                        }
                     ?>
                    <td style="text-align: center; font-size: 10px;">
                        <?php
                            echo $value; 
                        ?>
                    </td>
                    <td style="text-align: center; font-size: 10px;"><?php echo $smallUomLabel!=''?$smallUomLabel:$mainUom; ?></td>
                    <td style="text-align: center; font-size: 10px; <?php if($rowSettingUomDetail[0] == 0){ ?>display: none;<?php } ?>"><?php echo $dataDetail['lots_number']; ?></td>
                    <td style="text-align: center; font-size: 10px;">
                        <?php
                            $sqlLocation = mysql_query("SELECT name FROM locations WHERE id = ".$dataDetail['location_id']);
                            $rowLocation = mysql_fetch_array($sqlLocation);
                            echo $rowLocation[0];
                        ?>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>
        <br />
        <table style="width: 100%">
            <tr>
                <td style="font-size: 10px; font-weight: bold; height: 50px; vertical-align: top; width: 7%; text-decoration: underline">Memo: </td>
                <td style="width: 55%; vertical-align: top; font-size: 10px;">
                    <?php echo $dataCycleProduct['note']; ?>
                </td>
                <td></td>
            </tr>
        </table>
        <div style="clear:both"></div>
        <br />
        <div style="float:left;width: 450px">
            <div>
                <input type="button" value="<?php echo ACTION_PRINT; ?>" id='btnDisappearPrint' onClick='window.print();window.close();' class='noprint'>
            </div>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="printable" style="position: fixed; bottom: 40px; width: 100%;" class="print-footer">
        <table style="width: 100%;">
            <tr>
                <td style="font-size: 10px;">Issued By..............................</td>
                <td style="text-align: center; font-size: 10px;">Stock Issued By..............................</td>
                <td style="text-align: right; font-size: 10px;">Checked & Received By..............................</td>
            </tr>
        </table>
    </div>
</div>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(document).dblclick(function(){
            window.close();
        });
    });
</script>
