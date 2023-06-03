<?php
    include("includes/function.php");
    $sqlSettingUomDeatil  = mysql_query("SELECT uom_detail_option FROM setting_options");
    $rowSettingUomDetail  = mysql_fetch_array($sqlSettingUomDeatil);
?>
<style type="text/css" media="print">
    div.print_doc { width:100%;}
    #btnDisappearPrint { display: none;}
</style>
<div class="print_doc">
    <?php
    $msg = MENU_PURCHASE_ORDER_MANAGEMENT;
    echo $this->element('/print/header', array('msg' => $msg, 'barcode' => $purchaseOrder['PurchaseOrder']['po_code'], 'logo' => $purchaseOrder['Company']['photo']));
    ?>
    <div style="height: 12px"></div>
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
                <?php echo dateShort($purchaseOrder['PurchaseOrder']['invoice_date'], 'd/M/Y'); ?>
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
                <?php echo $purchaseOrder['PurchaseRequest']['pr_code'] != '0000-00-00'?dateShort($purchaseOrder['PurchaseRequest']['order_date'], 'd/M/Y'):""; ?>
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
    <br />
    <div>
        <div>
            <table class="table_print">
                <tr>
                    <th class="first" style="text-transform: uppercase; font-size: 12px;"><?php echo TABLE_NO; ?></th>
                    <th style="text-transform: uppercase; font-size: 12px; width: 18%;"><?php echo TABLE_SKU; ?></th>
                    <th style="text-transform: uppercase; font-size: 12px; width: 35%;"><?php echo TABLE_PRODUCT_NAME; ?></th>
                    <th style="text-transform: uppercase; font-size: 12px; width: 10%;"><?php echo TABLE_QTY; ?></th>
                    <th style="width: 10%; text-transform: uppercase; font-size: 12px;"><?php echo TABLE_F_O_C; ?></th>
                    <th style="text-transform: uppercase; font-size: 12px; width: 10%; <?php if($rowSettingUomDetail[0] == 0){ ?>display: none;<?php } ?>"><?php echo TABLE_LOTS_NO; ?></th>
                    <th style="width: 15%; white-space: nowrap; text-transform: uppercase; font-size: 12px;"><?php echo TABLE_UOM; ?></th>
                </tr>
                <?php
                $index = 0;
                if (!empty($purchaseOrderDetails)) {
                    foreach ($purchaseOrderDetails as $purchaseOrderDetail) {
                        
                ?>
                        <tr>
                            <td class="first" style="text-align: center; font-size: 12px;"><?php echo++$index; ?></td>
                            <td style="text-align: center; font-size: 12px;"><?php echo $purchaseOrderDetail['Product']['code']; ?> </td>
                            <td style="font-size: 12px;"><?php echo $purchaseOrderDetail['Product']['name']; ?></td>
                            <td style="text-align: center; font-size: 12px;"><?php echo number_format($purchaseOrderDetail['PurchaseOrderDetail']['qty'], 0); ?> </td>
                            <td style="text-align: center; font-size: 12px;"><?php echo number_format($purchaseOrderDetail['PurchaseOrderDetail']['qty_free'], 0); ?> </td>
                            <td style="font-size: 12px; <?php if($rowSettingUomDetail[0] == 0){ ?>display: none;<?php } ?>"><?php echo $purchaseOrderDetail['PurchaseOrderDetail']['lots_number']; ?></td>
                            <td style="text-align: center; font-size: 12px;"><?php echo $purchaseOrderDetail['Uom']['abbr']; ?></td>
                        </tr>
                <?php
                    }
                }               
                ?>
                
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
    $(document).ready(function(){
        $(document).dblclick(function(){
            window.close();
        });
    });
</script>