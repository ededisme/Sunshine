<?php
    // Get Decimal
    $sqlOption = mysql_query("SELECT product_cost_decimal FROM setting_options");
    $rowOption = mysql_fetch_array($sqlOption);
    include("includes/function.php");
?>
<style>
    .bold{
        font-weight: bold;
    }
</style>
<style type="text/css" media="print">
    div.print_doc { width:100%;}
    #btnDisappearPrint { display: none;}
</style>
<div class="print_doc">
    <?php
    $msg = 'PURCHASE ORDER';
    echo $this->element('/print/header-po', array('msg' => $msg, 'title' => $purchaseRequest['Branch']['name'], 'address' => $purchaseRequest['Branch']['address'], 'telephone' => $purchaseRequest['Branch']['telephone'], 'logo' => $purchaseRequest['Company']['photo']));
    ?>
    <div style="height: 12px"></div>
    <table width="100%">
        <tr>
            <td style="width: 7%; font-size: 12px;">From:</td>
            <td style="width: 65%; font-size: 12px;"><?php echo $purchaseRequest['Vendor']['name']; ?></td>
            <td style="width: 11%; font-size: 12px;">PO No:</td>
            <td style="font-size: 12px;"><?php echo $purchaseRequest['PurchaseRequest']['pr_code']; ?></td>
        </tr>
        <tr>
            <td style="font-size: 12px;">Address:</td>
            <td style="font-size: 12px;" rowspan="2">
                <?php echo nl2br($purchaseRequest['Vendor']['address']) ?>
            </td>
            <td style="font-size: 12px;">PO Date:</td>
            <td style="font-size: 12px;"><?php echo dateShort($purchaseRequest['PurchaseRequest']['order_date'], "M-d-Y"); ?></td>
        </tr>
        <tr>
            <td style="font-size: 12px;" colspan="2">
            Port Of Discharge: 
            <?php
            if($purchaseRequest['PurchaseRequest']['port_of_dischange_id'] != ''){
                $sqlShip = mysql_query("SELECT name FROM places WHERE id = ".$purchaseRequest['PurchaseRequest']['port_of_dischange_id']);
                $rowShip = mysql_fetch_array($sqlShip);
                echo $rowShip['name'];
            }
            ?>
            </td>
            <td style="font-size: 12px;">Ref Quotation:</td>
            <td style="font-size: 12px;"><?php echo $purchaseRequest['PurchaseRequest']['ref_quotation']; ?></td>
        </tr>
        <tr>
            <td style="font-size: 12px;" colspan="2">
            Final Place of Delivery: 
            <?php
            if($purchaseRequest['PurchaseRequest']['final_place_of_delivery_id'] != ''){
                $sqlShip = mysql_query("SELECT name FROM places WHERE id = ".$purchaseRequest['PurchaseRequest']['final_place_of_delivery_id']);
                $rowShip = mysql_fetch_array($sqlShip);
                echo $rowShip['name'];
            }
            ?>
            </td>
            <td style="font-size: 12px;">Shipment By:</td>
            <td style="font-size: 12px;">
                <?php 
                if($purchaseRequest['PurchaseRequest']['shipment_id'] != ''){
                    $sqlShip = mysql_query("SELECT name FROM shipments WHERE id = ".$purchaseRequest['PurchaseRequest']['shipment_id']);
                    $rowShip = mysql_fetch_array($sqlShip);
                    echo $rowShip['name'];
                }
                ?>
            </td>
        </tr>
    </table>
    <br />
    <div>
        <div>
            <table class="table_print" style="width: 100%;">
                <tr>
                    <th class="first" style="font-size: 12px; padding: 0px; height: 20px;"><?php echo TABLE_NO; ?></th>
                    <th style="text-transform: uppercase; font-size: 12px; padding: 0px;"><?php echo TABLE_SKU; ?></th>
                    <th style="width: 35%; text-transform: uppercase; font-size: 12px; padding: 0px;"><?php echo GENERAL_DESCRIPTION; ?></th>
                    <th style="white-space: nowrap; text-transform: uppercase; font-size: 12px; padding: 0px;"><?php echo TABLE_QTY; ?></th>
                    <th style="white-space: nowrap; text-transform: uppercase; font-size: 12px; padding: 0px;"><?php echo TABLE_UOM; ?></th>
                    <th style="text-transform: uppercase; font-size: 12px; padding: 0px;"><?php echo SALES_ORDER_UNIT_PRICE; ?></th>
                    <th style="text-transform: uppercase; font-size: 12px; padding: 0px;"><?php echo TABLE_TOTAL_PRICE_SHORT; ?></th>
                </tr>
                <?php
                $index = 0;
                if (!empty($purchaseRequestDetails)) {
                    foreach ($purchaseRequestDetails as $purchaseRequestDetail) {
                ?>
                        <tr>
                            <td class="first" style="text-align: center; font-size: 12px; padding-top: 0px; padding-bottom: 0px; height: 20px;"><?php echo++$index; ?></td>
                            <td style="font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo $purchaseRequestDetail['Product']['code']; ?></td>
                            <td style="width: 35%; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo $purchaseRequestDetail['Product']['name']; ?></td>
                            <td style="text-align: center; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo number_format($purchaseRequestDetail['PurchaseRequestDetail']['qty'], 0); ?> </td>
                            <td style="text-align: center; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo $purchaseRequestDetail['Uom']['abbr']; ?> </td>
                            <td style="text-align: right; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo number_format($purchaseRequestDetail['PurchaseRequestDetail']['unit_cost'], $rowOption[0]); ?></td>
                            <td style="text-align: right; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo number_format(($purchaseRequestDetail['PurchaseRequestDetail']['total_cost']), $rowOption[0]); ?></td>
                        </tr>
                <?php
                        
                    }
                }
                if (!empty($purchaseRequestServices)) {
                    foreach ($purchaseRequestServices as $purchaseRequestService) {
                        $uomName = '';
                        if($purchaseRequestService['Service']['uom_id'] != ''){
                            $sqlUom = mysql_query("SELECT abbr FROM uoms WHERE id = ".$purchaseRequestService['Service']['uom_id']);
                            $rowUom = mysql_fetch_array($sqlUom);
                            $uomName = $rowUom[0];
                        }
                ?>
                        <tr>
                            <td class="first" style="text-align: center; font-size: 12px; padding-top: 0px; padding-bottom: 0px; height: 20px;"><?php echo++$index; ?></td>
                            <td style="font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo $purchaseRequestService['Service']['code']; ?></td>
                            <td style="width: 35%; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo $purchaseRequestService['Service']['name']; ?></td>
                            <td style="text-align: center; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo number_format($purchaseRequestService['PurchaseRequestService']['qty'], 0); ?> </td>
                            <td style="text-align: center; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo $uomName; ?> </td>
                            <td style="text-align: right; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo number_format($purchaseRequestService['PurchaseRequestService']['unit_cost'], $rowOption[0]); ?></td>
                            <td style="text-align: right; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo number_format(($purchaseRequestService['PurchaseRequestService']['total_cost']), $rowOption[0]); ?></td>
                        </tr>
                <?php
                        
                    }
                }
                ?>
                <tr>
                    <td class="first" style="border-bottom: none; border-left: none;text-align: right; font-size: 12px; padding-top: 0px; padding-bottom: 0px; height: 20px;" colspan="6"><b><?php echo TABLE_SUB_TOTAL; ?></b></td>
                    <td style="text-align: right; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo number_format($purchaseRequest['PurchaseRequest']['total_amount'], $rowOption[0]); ?> <?php echo $purchaseRequest['CurrencyCenter']['symbol']; ?></td>
                </tr>
                <tr>
                    <td class="first" style="border-bottom: none; border-left: none;text-align: right; font-size: 12px; padding-top: 0px; padding-bottom: 0px; height: 20px;" colspan="6"><b><?php echo TABLE_VAT; ?> (<?php echo number_format($purchaseRequest['PurchaseRequest']['vat_percent'], 2); ?>%)</b></td>
                    <td style="text-align: right; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo number_format($purchaseRequest['PurchaseRequest']['total_vat'], $rowOption[0]); ?> <?php echo $purchaseRequest['CurrencyCenter']['symbol']; ?></td>
                </tr>
                <tr>
                    <td class="first" style="border-bottom: none; border-left: none;text-align: right; font-size: 12px; padding-top: 0px; padding-bottom: 0px; height: 20px;" colspan="6"><b><?php echo TABLE_TOTAL; ?></b></td>
                    <td style="text-align: right; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo number_format($purchaseRequest['PurchaseRequest']['total_amount'] + $purchaseRequest['PurchaseRequest']['total_vat'], $rowOption[0]); ?> <?php echo $purchaseRequest['CurrencyCenter']['symbol']; ?></td>
                </tr>
            </table>
        </div>
        <?php
        $sqlTerm = mysql_query("SELECT term_conditions.name AS name FROM purchase_request_term_conditions INNER JOIN term_conditions ON term_conditions.id = purchase_request_term_conditions.term_condition_id WHERE purchase_request_term_conditions.purchase_request_id = ".$purchaseRequest['PurchaseRequest']['id']);                                    
        if(mysql_num_rows($sqlTerm)){
        ?>
        <!-- Term & Condition -->
        <table cellpadding="0" cellspacing="0" style="width: 100%; margin-top: 5px;">
            <tr>
                <td style="font-size: 10px; font-weight: bold; vertical-align: top; border: none; padding: 0px; height: 15px;">Terms and Condition:</td>
            </tr>
            <?php
            while($rowTerm = mysql_fetch_array($sqlTerm)){
            ?>
            <tr>
                <td style="font-size: 10px; vertical-align: top; border: none; padding: 0px;">- <?php echo $rowTerm['name']; ?></td>
            </tr>
            <?php
            }
            ?>
        </table>
        <!-- Note -->
        <?php
        }
        ?>
        <br />
        <b style="font-size: 12px;">Authorize Signature:</b> <br />
        <div style="font-size: 12px;"></div>Sincerely yours,
        <div style="margin-top: 50px;">
            <table style="width: 100%;" cellpadding="3" cellspacing="0">
                <tr>
                    <td><b style="font-size: 12px;"></b></td>
                </tr>
                <tr>
                    <td style="font-size: 12px;"></td>
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