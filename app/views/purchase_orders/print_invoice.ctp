<?php
// Get Decimal
$sqlOption = mysql_query("SELECT product_cost_decimal FROM setting_options");
$rowOption = mysql_fetch_array($sqlOption);
include("includes/function.php");
$sqlSettingUomDeatil  = mysql_query("SELECT uom_detail_option FROM setting_options");
$rowSettingUomDetail  = mysql_fetch_array($sqlSettingUomDeatil);
// Check Permission
$this->element('check_access');
$allowShowUnitCost  = checkAccess($user['User']['id'], $this->params['controller'], 'showUnitCost');
$allowDiscount    = checkAccess($user['User']['id'], $this->params['controller'], 'discount');
$allowEditInvDis  = checkAccess($user['User']['id'], $this->params['controller'], 'invoiceDiscount');
?>
<style type="text/css" media="print">
    div.print_doc {
        width: 100%;
    }

    #btnDisappearPrint {
        display: none;
    }
</style>
<div class="print_doc">
    <?php
    $msg = MENU_PURCHASE_ORDER_MANAGEMENT;
    echo $this->element('/print/header', array('msg' => $msg, 'barcode' => $purchaseOrder['PurchaseOrder']['po_code'], 'logo' => $purchaseOrder['Company']['photo']));
    ?>
    <div style="height: 20px"></div>
    <table style="width: 100%;" cellpadding="5" cellspacing="0">
        <tr>
            <td style="width: 20%; text-transform: uppercase; font-size: 12px;"><?php echo TABLE_PB_NUMBER; ?> :</td>
            <td style="width: 15%; text-transform: uppercase; font-size: 12px;">
                <?php echo $purchaseOrder['PurchaseOrder']['po_code']; ?>
            </td>
            <td style="width: 15%; text-transform: uppercase; font-size: 12px;"><?php echo TABLE_INVOICE_CODE; ?> :</td>
            <td style="width: 20%; font-size: 12px;">
                <?php echo $purchaseOrder['PurchaseOrder']['invoice_code']; ?>
            </td>
            <td style="width: 15%; text-transform: uppercase; font-size: 12px;"><?php echo TABLE_LOCATION_GROUP; ?> :</td>
            <td style="width: 28%; font-size: 12px;">
                <?php echo $purchaseOrder['LocationGroup']['name']; ?>
            </td>
        </tr>
        <tr>
            <td style="text-transform: uppercase; font-size: 12px;"><?php echo TABLE_PB_DATE; ?> :</td>
            <td style="font-size: 12px;">
                <?php echo dateShort($purchaseOrder['PurchaseOrder']['order_date'], 'd/M/Y'); ?>
            </td>
            <td style="text-transform: uppercase; font-size: 12px;"><?php echo TABLE_INVOICE_DATE; ?> :</td>
            <td style="font-size: 12px;">
                <?php if ($purchaseOrder['PurchaseOrder']['invoice_date'] != '0000-00-00' && $purchaseOrder['PurchaseOrder']['invoice_date'] != '') {
                    echo dateShort($purchaseOrder['PurchaseOrder']['invoice_date'], 'd/M/Y');
                } ?>
            </td>
            <td style="text-transform: uppercase; font-size: 12px;"><?php echo TABLE_LOCATION; ?> :</td>
            <td style="font-size: 12px;">
                <?php echo $purchaseOrder['Location']['name']; ?>
            </td>
        </tr>
        <tr>
            <td style="text-transform: uppercase; font-size: 12px;"><?php echo TABLE_PO_NUMBER; ?> :</td>
            <td style="font-size: 12px;">
                <?php echo $purchaseOrder['PurchaseRequest']['pr_code']; ?>
            </td>
            <td style="text-transform: uppercase; font-size: 12px;"><?php echo TABLE_PO_DATE; ?> :</td>
            <td style="font-size: 12px;">
                <?php echo $purchaseOrder['PurchaseRequest']['pr_code'] != '0000-00-00' ? dateShort($purchaseOrder['PurchaseRequest']['order_date'], 'd/M/Y') : ""; ?>
            </td>
            <td style="text-transform: uppercase; font-size: 12px;"><?php echo TABLE_VENDOR; ?> :</td>
            <td style="font-size: 12px;">
                <?php echo $purchaseOrder['Vendor']['name']; ?>
            </td>
        </tr>
        <tr>
            <td style="text-transform: uppercase; font-size: 12px;"><?php echo TABLE_SHIPMENT_BY; ?> :</td>
            <td style="font-size: 12px;">
                <?php echo $purchaseOrder['Shipment']['name']; ?>
            </td>
            <td style="text-transform: uppercase; font-size: 12px;"><?php echo TABLE_NOTE; ?> :</td>
            <td colspan="3" style="font-size: 12px;">
                <?php echo $purchaseOrder['PurchaseOrder']['note']; ?>
            </td>
        </tr>
    </table>
    <?php 
        $display ='display:none;';
        if($allowShowUnitCost){
            $display = '';
        }
    ?>
    <br />
    <div>
        <div>
            <table class="table_print">
                <tr>
                    <th class="first" style="text-transform: uppercase; font-size: 12px; height: 20px; padding-bottom: 0px; padding-top: 0px;"><?php echo TABLE_NO; ?></th>
                    <th style="width: 10%; text-transform: uppercase; font-size: 12px; padding-bottom: 0px; padding-top: 0px;"><?php echo TABLE_SKU; ?></th>
                    <th style="width: 35%; text-transform: uppercase; font-size: 12px; padding-bottom: 0px; padding-top: 0px;"><?php echo GENERAL_DESCRIPTION; ?></th>
                    <th style="text-transform: uppercase; font-size: 12px; padding-bottom: 0px; padding-top: 0px;"><?php echo TABLE_QTY; ?></th>
                    <th style="text-transform: uppercase; font-size: 12px; padding-bottom: 0px; padding-top: 0px;"><?php echo TABLE_F_O_C; ?></th>
                    <th style="text-transform: uppercase; font-size: 12px; padding-bottom: 0px; padding-top: 0px;"><?php echo TABLE_UOM; ?></th>
                    <th style="width: 15%; text-transform: uppercase; font-size: 12px; padding-bottom: 0px; padding-top: 0px; <?php echo $display;?>"><?php echo TABLE_UNIT_COST; ?></th>
                    <th style="width: 13%; text-transform: uppercase; font-size: 12px; padding-bottom: 0px; padding-top: 0px; <?php echo $display;?>"><?php echo GENERAL_DISCOUNT; ?></th>
                    <th style="width: 15%; text-transform: uppercase; font-size: 12px; padding-bottom: 0px; padding-top: 0px; <?php echo $display;?>"><?php echo GENERAL_AMOUNT; ?></th>
                </tr>
                <?php
                $index = 0;
                if (!empty($purchaseOrderDetails)) {
                    foreach ($purchaseOrderDetails as $purchaseOrderDetail) {
                        $discount = $purchaseOrderDetail['PurchaseOrderDetail']['discount_amount'];
                ?>
                        <tr>
                            <td class="first" style="text-align: center; font-size: 12px; height: 20px; padding-bottom: 0px; padding-top: 0px;"><?php echo ++$index; ?></td>
                            <td style="font-size: 12px; padding-bottom: 0px; padding-top: 0px;"><?php echo $purchaseOrderDetail['Product']['code']; ?></td>
                            <td style="font-size: 12px; padding-bottom: 0px; padding-top: 0px;"><?php echo $purchaseOrderDetail['Product']['name']; ?></td>
                            <td style="text-align: center; font-size: 12px; padding-bottom: 0px; padding-top: 0px;"><?php echo number_format($purchaseOrderDetail['PurchaseOrderDetail']['qty'], 0); ?> </td>
                            <td style="text-align: center; font-size: 12px; padding-bottom: 0px; padding-top: 0px;"><?php echo number_format($purchaseOrderDetail['PurchaseOrderDetail']['qty_free'], 0); ?> </td>
                            <td style="text-align: center; font-size: 12px; padding-bottom: 0px; padding-top: 0px;"><?php echo $purchaseOrderDetail['Uom']['abbr']; ?> </td>
                            <td style="text-align: right; font-size: 12px; padding-bottom: 0px; padding-top: 0px; <?php echo $display;?>"><?php echo number_format($purchaseOrderDetail['PurchaseOrderDetail']['unit_cost'],  $rowOption[0]); ?></td>
                            <td style="text-align: right; font-size: 12px; padding-bottom: 0px; padding-top: 0px; <?php echo $display;?>"><?php echo number_format($discount,  $rowOption[0]); ?></td>
                            <td style="text-align: right; font-size: 12px; padding-bottom: 0px; padding-top: 0px; <?php echo $display;?>"><?php echo number_format(($purchaseOrderDetail['PurchaseOrderDetail']['total_cost'] - $discount),  $rowOption[0]); ?></td>
                        </tr>
                    <?php

                    }
                }
                if (!empty($purchaseOrderServices)) {
                    foreach ($purchaseOrderServices as $purchaseOrderService) {
                        $uomName = '';
                        if ($purchaseOrderService['Service']['uom_id'] != '') {
                            $sqlUom = mysql_query("SELECT abbr FROM uoms WHERE id = " . $purchaseOrderService['Service']['uom_id']);
                            $rowUom = mysql_fetch_array($sqlUom);
                            $uomName = $rowUom[0];
                        }
                        $discount = $purchaseOrderService['PurchaseOrderService']['discount_amount'];
                    ?>
                        <tr>
                            <td class="first" style="text-align: center; font-size: 12px; height: 20px; padding-bottom: 0px; padding-top: 0px;"><?php echo ++$index; ?></td>
                            <td style="font-size: 12px; padding-bottom: 0px; padding-top: 0px;"><?php echo $purchaseOrderService['Service']['code']; ?></td>
                            <td style="font-size: 12px; padding-bottom: 0px; padding-top: 0px;"><?php echo $purchaseOrderService['Service']['name']; ?></td>
                            <td style="text-align: center; font-size: 12px; padding-bottom: 0px; padding-top: 0px;"><?php echo number_format($purchaseOrderService['PurchaseOrderService']['qty'], 0); ?> </td>
                            <td style="text-align: center; font-size: 12px; padding-bottom: 0px; padding-top: 0px;"><?php echo number_format($purchaseOrderService['PurchaseOrderService']['qty_free'], 0); ?> </td>
                            <td style="text-align: center; font-size: 12px; padding-bottom: 0px; padding-top: 0px;"><?php echo $uomName; ?></td>
                            <td style="text-align: right; font-size: 12px; padding-bottom: 0px; padding-top: 0px; <?php echo $display;?>"><?php echo number_format($purchaseOrderService['PurchaseOrderService']['unit_cost'],  $rowOption[0]); ?></td>
                            <td style="text-align: right; font-size: 12px; padding-bottom: 0px; padding-top: 0px; <?php echo $display;?>"><?php echo number_format($discount,  $rowOption[0]); ?></td>
                            <td style="text-align: right; font-size: 12px; padding-bottom: 0px; padding-top: 0px; <?php echo $display;?>"><?php echo number_format(($purchaseOrderService['PurchaseOrderService']['total_cost'] - $discount),  $rowOption[0]); ?></td>
                        </tr>
                    <?php

                    }
                }
                if (!empty($purchaseOrderMiscs)) {
                    foreach ($purchaseOrderMiscs as $purchaseOrderMisc) {
                        $discount = $purchaseOrderMisc['PurchaseOrderMisc']['discount_amount'];
                    ?>
                        <tr>
                            <td class="first" style="text-align: center; font-size: 12px; height: 20px; padding-bottom: 0px; padding-top: 0px;"><?php echo ++$index; ?></td>
                            <td style="font-size: 12px; padding-bottom: 0px; padding-top: 0px;"></td>
                            <td style="font-size: 12px; padding-bottom: 0px; padding-top: 0px;"><?php echo $purchaseOrderMisc['PurchaseOrderMisc']['description']; ?></td>
                            <td style="text-align: center; font-size: 12px; padding-bottom: 0px; padding-top: 0px;"><?php echo number_format($purchaseOrderMisc['PurchaseOrderMisc']['qty'], 0); ?> </td>
                            <td style="text-align: center; font-size: 12px; padding-bottom: 0px; padding-top: 0px;"><?php echo number_format($purchaseOrderMisc['PurchaseOrderMisc']['qty_free'], 0); ?> </td>
                            <td style="text-align: center; font-size: 12px; padding-bottom: 0px; padding-top: 0px;"><?php echo $purchaseOrderMisc['Uom']['abbr']; ?> </td>
                            <td style="text-align: right; font-size: 12px; padding-bottom: 0px; padding-top: 0px; <?php echo $display;?>"><?php echo number_format($purchaseOrderMisc['PurchaseOrderMisc']['unit_cost'],  $rowOption[0]); ?></td>
                            <td style="text-align: right; font-size: 12px; padding-bottom: 0px; padding-top: 0px; <?php echo $display;?>"><?php echo number_format($discount,  $rowOption[0]); ?></td>
                            <td style="text-align: right; font-size: 12px; padding-bottom: 0px; padding-top: 0px; <?php echo $display;?>"><?php echo number_format(($purchaseOrderMisc['PurchaseOrderMisc']['total_cost'] - $discount),  $rowOption[0]); ?></td>
                        </tr>
                <?php
                    }
                }
                ?>
                <tr>
                    <td class="first" style="<?php echo $display;?> border-bottom: none; border-left: none;text-align: right; font-size: 12px; height: 20px; padding-bottom: 0px; padding-top: 0px;" colspan="8"><b><?php echo TABLE_TOTAL_AMOUNT; ?></b></td>
                    <td style="<?php echo $display;?> text-align: right; font-size: 12px; padding-bottom: 0px; padding-top: 0px;"><?php echo number_format(($purchaseOrder['PurchaseOrder']['total_amount']),  $rowOption[0]); ?> <?php echo $purchaseOrder['CurrencyCenter']['symbol']; ?></td>
                </tr>
                <tr>
                    <td class="first" style="<?php echo $display;?>border-bottom: none; border-left: none;text-align: right; font-size: 12px; height: 20px; padding-bottom: 0px; padding-top: 0px;" colspan="8"><b><?php echo GENERAL_DISCOUNT; ?> <?php if ($purchaseOrder['PurchaseOrder']['discount_percent'] > 0) { ?>(<?php echo number_format($purchaseOrder['PurchaseOrder']['discount_percent'], 2); ?>%)<?php } ?></b></td>
                    <td style=" <?php echo $display;?> text-align: right; font-size: 12px; padding-bottom: 0px; padding-top: 0px;"><?php echo number_format(($purchaseOrder['PurchaseOrder']['discount_amount']),  $rowOption[0]); ?> <?php echo $purchaseOrder['CurrencyCenter']['symbol']; ?></td>
                </tr>
                <tr>
                    <td class="first" style="<?php echo $display;?>border-bottom: none; border-left: none;text-align: right; font-size: 12px; height: 20px; padding-bottom: 0px; padding-top: 0px;" colspan="8"><b><?php echo TABLE_VAT; ?> (<?php echo number_format($purchaseOrder['PurchaseOrder']['vat_percent'], 2); ?>%)</b></td>
                    <td style=" <?php echo $display;?>text-align: right; font-size: 12px; padding-bottom: 0px; padding-top: 0px;"><?php echo number_format(($purchaseOrder['PurchaseOrder']['total_vat']),  $rowOption[0]); ?> <?php echo $purchaseOrder['CurrencyCenter']['symbol']; ?></td>
                </tr>
                <tr>
                    <td class="first" style="<?php echo $display;?> border-bottom: none; border-left: none;text-align: right; font-size: 12px; height: 20px; padding-bottom: 0px; padding-top: 0px;" colspan="8"><b><?php echo TABLE_TOTAL; ?></b></td>
                    <td style="<?php echo $display;?> text-align: right; font-size: 12px; padding-bottom: 0px; padding-top: 0px;"><?php echo number_format(($purchaseOrder['PurchaseOrder']['total_amount'] - $purchaseOrder['PurchaseOrder']['discount_amount'] + $purchaseOrder['PurchaseOrder']['total_vat']),  $rowOption[0]); ?> <?php echo $purchaseOrder['CurrencyCenter']['symbol']; ?></td>
                </tr>
            </table>
        </div>
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
    $(document).ready(function() {
        $(document).dblclick(function() {
            window.close();
        });
    });
</script>