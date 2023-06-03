<div class="print_doc">
    <?php
    include("includes/function.php");
    $msg = ACTION_INVOICE . ' <br /> ' . MENU_PURCHASE_RETURN_MANAGEMENT;
    echo $this->element('/print/header', array('msg' => $msg, 'barcode' => $purchaseReturn['PurchaseReturn']['pr_code'], 'logo' => $purchaseReturn['Company']['photo']));
    ?>
    <div style="height: 30px"></div>
    <table style="width: 100%;" cellpadding="5">
        <tr>
            <td style="width: 10%;"></td>
            <td style="width: 25%;"></td>
            <td style="width: 15%;"><?php echo PURCHASE_RETURN_CODE; ?> :</td>
            <td style="width: 25%;"><?php echo $purchaseReturn['PurchaseReturn']['pr_code']; ?></td>
            <td style="width: 10%;"><?php echo TABLE_DATE; ?> :</td>
            <td><?php echo dateShort($purchaseReturn['PurchaseReturn']['order_date']); ?></td>
        </tr>
        <tr>
            <td><?php echo TABLE_VENDOR; ?> :</td>
            <td><?php echo $purchaseReturn['Vendor']['vendor_code']." - ".$purchaseReturn['Vendor']['name']; ?></td>
            <td><?php echo TABLE_LOCATION_GROUP; ?> :</td>
            <td><?php echo $purchaseReturn['LocationGroup']['name']; ?></td>
            <td><?php echo TABLE_LOCATION; ?> :</td>
            <td><?php echo $purchaseReturn['Location']['name']; ?></td>
        </tr>
        <tr>
            <td style="vertical-align: top;"><?php echo TABLE_NOTE; ?> :</td>
            <td style="vertical-align: top;" cospan="5"><?php echo nl2br($purchaseReturn['Vendor']['note']); ?></td>
        </tr>
    </table>
    <br />
    <div>
        <div>
            <table class="table_print">
                <tr>
                    <th class="first"><?php echo TABLE_NO; ?></th>
                    <th><?php echo TABLE_NAME . "/" . GENERAL_DESCRIPTION ?></th>
                    <th style="width: 160px !important;"><?php echo TABLE_QTY ?></th>
                    <th style="width: 140px !important;"><?php echo SALES_ORDER_UNIT_PRICE ?></th>
                    <th style="width: 140px !important;"><?php echo SALES_ORDER_TOTAL_PRICE; ?></th>
                </tr>
                <?php
                $index = 0;
                if (!empty($purchaseReturnDetails)) {
                    foreach ($purchaseReturnDetails as $purchaseReturnDetail) {//$discount = $purchaseReturnDetail['Discount']['amount'] + ($purchaseReturnDetail['Discount']['percent'] * $purchaseReturnDetail['PurchaseReturnDetail']['total_price']) / 100;
                ?>
                        <tr><td class="first" style="text-align: right;"><?php echo++$index; ?></td>
                            <td><?php echo $purchaseReturnDetail['Product']['code'] . ' - ' . $purchaseReturnDetail['Product']['name']; ?></td>
                            <td style="text-align: center"><?php echo number_format($purchaseReturnDetail['PurchaseReturnDetail']['qty'], 0) . ' ' . $purchaseReturnDetail['Uom']['abbr']; ?> </td>
                            <td style="text-align: right"><div style="width:10px; float: left; font-size:14px; margin-left: 5px;">$</div><?php echo number_format($purchaseReturnDetail['PurchaseReturnDetail']['unit_price'], 2); ?></td>
                            <td style="text-align: right"><div style="width:10px; float: left; font-size:14px; margin-left: 5px;">$</div><?php echo number_format(($purchaseReturnDetail['PurchaseReturnDetail']['total_price']), 2); ?></td>
                        </tr>
                <?php
                    }
                }
                ?>
                <?php
                if (!empty($purchaseReturnServices)) {
                    foreach ($purchaseReturnServices as $purchaseReturnService) {
                        $uomName = '';
                        if($purchaseReturnService['Service']['uom_id'] != ''){
                            $sqlUom = mysql_query("SELECT abbr FROM uoms WHERE id = ".$purchaseReturnService['Service']['uom_id']);
                            $rowUom = mysql_fetch_array($sqlUom);
                            $uomName = $rowUom[0];
                        }
                ?>
                        <tr><td class="first" style="text-align: right;"><?php echo++$index; ?></td>
                            <td><?php echo $purchaseReturnService['Service']['code'] . ' - ' .$purchaseReturnService['Service']['name']; ?></td>
                            <td style="text-align: center"><?php echo number_format($purchaseReturnService['PurchaseReturnService']['qty'], 0). ' '.$uomName; ?></td>
                            <td style="text-align: right">
                                <div style="width:10px; float: left; font-size:14px; margin-left: 5px;">$</div> <?php echo number_format($purchaseReturnService['PurchaseReturnService']['unit_price'], 2); ?>
                            </td>
                            <td style="text-align: right">
                                <div style="width:10px; float: left; font-size:14px; margin-left: 5px;">$</div> <?php echo number_format($purchaseReturnService['PurchaseReturnService']['total_price'], 2); ?>
                            </td>
                        </tr>
                <?php
                    }
                }
                ?>
                <?php
                if (!empty($purchaseReturnMiscs)) {
                    foreach ($purchaseReturnMiscs as $purchaseReturnMisc) {
                ?>
                        <tr><td class="first" style="text-align: right;"><?php echo++$index; ?></td>
                            <td><?php echo $purchaseReturnMisc['PurchaseReturnMisc']['description']; ?></td>
                            <td style="text-align: center;white-space: nowrap;"><?php echo number_format($purchaseReturnMisc['PurchaseReturnMisc']['qty'], 0) . ' ' . $purchaseReturnMisc['Uom']['abbr']; ?> </td>
                            <td style="text-align: right">
                                <div style="width:10px; float: left; font-size:14px; margin-left: 5px;">$</div> <?php echo number_format($purchaseReturnMisc['PurchaseReturnMisc']['unit_price'], 2); ?>
                            </td>
                            <td style="text-align: right">
                                <div style="width:10px; float: left; font-size:14px; margin-left: 5px;">$</div> <?php echo number_format($purchaseReturnMisc['PurchaseReturnMisc']['total_price'], 2); ?>
                            </td>
                        </tr>
                <?php
                    }
                }
                ?>
                <tr>
                    <td class="first" style="border-bottom: none; border-left: none;text-align: right" colspan="4"><b><?php echo TABLE_SUB_TOTAL; ?></b></td>
                    <td style="text-align: right"><div style="width:10px; float: left; font-size:14px; margin-left: 5px;">$</div><?php echo number_format($purchaseReturn['PurchaseReturn']['total_amount'], 2); ?></td>
                </tr>
                <tr>
                    <td class="first" style="border-bottom: none; border-left: none;text-align: right" colspan="4"><b><?php echo TABLE_VAT; ?> (<?php echo number_format($purchaseReturn['PurchaseReturn']['vat_percent'], 2); ?>%)</b></td>
                    <td style="text-align: right"><div style="width:10px; float: left; font-size:14px; margin-left: 5px;">$</div><?php echo number_format($purchaseReturn['PurchaseReturn']['total_vat'], 2); ?></td>
                </tr>
                <tr>
                    <td class="first" style="border-bottom: none; border-left: none;text-align: right;" colspan=4"><b><?php echo TABLE_TOTAL_AMOUNT; ?></b></td>
                    <td style="text-align: right"><div style="width:10px; float: left; font-size:14px; margin-left: 5px;">$</div><?php echo number_format($purchaseReturn['PurchaseReturn']['total_amount'] + $purchaseReturn['PurchaseReturn']['total_vat'], 2); ?></td>
                </tr>
            </table>
        </div>
        <br />
        <div>
            <table style="width: 100%">
                <?php
                if(!empty($pbcPbs)){
                    $i = 1;
                    $invoices = "";
                    foreach($pbcPbs as $pbcPb){
                        if($i == 1){
                            $invoices .= $pbcPb['PurchaseOrder']['po_code']." (".$pbcPb[0]['total_cost']." $)";
                        }else{
                            $invoices .= " ,".$pbcPb['PurchaseOrder']['po_code']." (".$pbcPb[0]['total_cost']." $)";
                        }
                        $i++;
                    }
                ?>
                <tr>
                    <td style="font-size: 14px; font-weight: bold; vertical-align: top; width: 15%; text-decoration: underline">Apply To PB: </td>
                    <td style="width: 55%; vertical-align: top;font-size: 15px;">
                        <?php echo $invoices; ?>
                    </td>
                    <td></td>
                </tr>
                <?php
                }
                ?>
           </table>
            <table style="width: 100%;">
                <tr>
                    <td><?php echo TABLE_ISSUED_BY;?>..............................</td>
                    <td style="text-align: center;"><?php echo TABLE_STOCK_ISSUED_BY;?>..............................</td>
                    <td style="text-align: right;"><?php echo TABLE_CHECKED_RECEIVE_BY;?>..............................</td>
                </tr>
            </table>
        </div>
        <div style="clear:both"></div>
        <br />
        <div style="float:left;width: 450px">
            <div>
                <input type="button" value="<?php echo ACTION_PRINT; ?>" id='btnDisappearPrint' onClick='window.print();window.close();' class='noprint'>
            </div>
        </div>
        <div style="clear:both"></div>
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