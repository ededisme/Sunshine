<?php
include("includes/function.php");

mysql_query("UPDATE `sales_orders` SET `is_print`= 0 WHERE  `id`=" . $salesOrder['SalesOrder']['id'] . " LIMIT 1;");
?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/print_setup.js"></script>
<style type="text/css" media="screen">
    div#header_waiting { display: none;}
    div.wrap-print-slip { width:310px;}
    div#print-footer {display: none;} 
    b{font-size:12px;}
</style> 
<style type="text/css" media="print">
    div#header_waiting { width:100%; text-align: center; margin: 0px auto; display: block; padding-bottom: 20px; padding-top: 0px; page-break-after: always}
    div.wrap-print-slip { width:100%;}
    #btnDisappearPrint { display: none;}
    div#print-footer {display: block; margin-top: 10px; width:100%} 
</style>
<div class="wrap-print-slip" style="font-size: 14px; padding: 0px;">
    <?php
    $msg = ACTION_RECEIPT;
    echo $this->element('/print/header_small', array('msg' => $msg, 'barcode' => $salesOrderReceipt['SalesOrderReceipt']['receipt_code'], 'address' => $salesOrder['Location']['address'], 'counterNumber' => $salesOrder['Location']['counter_number'], 'location' => $salesOrder['Location']['id'], 'waiting_number' => $salesOrderReceipt['SalesOrder']['waiting_number']));
    ?>
    <table cellpadding="0" cellspacing="0" width="100%">
        <tr>

            <td style="font-size: 10px;">
                វិក្ក័យប័ត្រ : <?php echo $salesOrderReceipt['SalesOrderReceipt']['receipt_code']; ?>
            </td>
            <td style="font-size: 10px;text-align: right;">
                អ្នកលក់ : <?php echo "{$salesOrderReceipt['User']['first_name']} {$salesOrderReceipt['User']['last_name']}"; ?>
            </td>
        </tr>
    </table>
    <div>
        <div style="margin-top:2px; ">
            <table class="table_pos" cellpadding="0" cellspacing="0" style="width:100%">
                <tr>
                    <th class="first" style="padding:1px; font-size: 9px; width:45%; text-align: left; font-weight: bold; border-right: none;">បរិយាយ</th>
                    <th style="padding: 1px; font-size: 9px; text-align: center; width: 17%; font-weight: bold; border-right: none;" >ចំនួន</th>
                    <th style="padding: 1px; font-size: 9px; text-align: center; width: 12%; font-weight: bold; border-right: none;" >តំលៃ</th>
                    <th style="padding: 1px; font-size: 9px; text-align: center; width: 12%; font-weight: bold; border-right: none;" >ចុះថ្លៃ</th>
                    <th style="padding: 1px; font-size: 9px; text-align: center; width: 14%; font-weight: bold;" >សរុប</th>
                </tr>
                <?php
                $index = 1;
                if (!empty($salesOrderDetails)) {
                    foreach ($salesOrderDetails as $salesOrderDetail) {
                        $discount = $salesOrderDetail['SalesOrderDetail']['discount_amount'];
                        ?>
                        <tr>
                            <td class="first" style="padding: 1px; font-size: 10px; text-align: left; color: #000; border-right: none;font-weight: bold; border-left: none;"><?php echo strlen($salesOrderDetail['Product']['name']) > 20 ? substr($salesOrderDetail['Product']['name'], 0, 20) : $salesOrderDetail['Product']['name']; ?></td>
                            <td style="padding: 1px; text-align: center; font-size: 9px;text-align: center; color: #000; border-right: none;font-weight: bold;"><?php echo $salesOrderDetail['SalesOrderDetail']['qty'] . '<span style="font-size:7px">' . $salesOrderDetail['Uom']['abbr'] . '</span>'; ?></td>
                            <td style="padding: 1px; text-align: right; font-size: 9px;text-align: center; color: #000; border-right: none;font-weight: bold;"><?php echo number_format($salesOrderDetail['SalesOrderDetail']['unit_price'], 2); ?></td>
                            <td style="padding: 1px; text-align: right; font-size: 9px;text-align: center; color: #000; border-right: none;font-weight: bold;"><?php echo number_format(($discount), 2); ?></td>
                            <td style="padding: 1px; text-align: right; font-size: 9px;text-align: right; color: #000; border-right: none;font-weight: bold;"><?php echo number_format(($salesOrderDetail['SalesOrderDetail']['total_price']) - $discount, 2); ?></td>
                        </tr>
                        <?php
                        $index++;
                    }
                }

                if (!empty($salesOrderServices)) {
                    foreach ($salesOrderServices as $salesOrderService) {
                        $discount = $salesOrderService['SalesOrderService']['discount_amount'];
                        ?>
                        <tr>
                            <td class="first" style="padding: 1px; font-size: 10px; text-align: left; color: #000; border-right: none;font-weight: bold; border-left: none;"><?php echo strlen($salesOrderService['Service']['name']) > 20 ? substr($salesOrderService['Service']['name'], 0, 20) : $salesOrderService['Service']['name']; ?></td>
                            <td style="padding: 1px; text-align: center; font-size: 9px;text-align: center; color: #000; border-right: none;font-weight: bold;"><?php echo $salesOrderService['SalesOrderService']['qty']; ?></td>
                            <td style="padding: 1px; text-align: right; font-size: 9px;text-align: center; color: #000; border-right: none;font-weight: bold;"><?php echo number_format($salesOrderService['SalesOrderService']['unit_price'], 2); ?></td>
                            <td style="padding: 1px; text-align: right; font-size: 9px;text-align: center; color: #000; border-right: none;font-weight: bold;"><?php echo number_format(($discount), 2); ?></td>
                            <td style="padding: 1px; text-align: right; font-size: 9px;text-align: right; color: #000; border-right: none;font-weight: bold;"><?php echo number_format(($salesOrderService['SalesOrderService']['total_price']) - $discount, 2); ?></td>
                        </tr>
                        <?php
                        $index++;
                    }
                }
                ?>
            </table>
            <table style="width:100%;" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="first" style="border-bottom: none; border-left: none; text-align: left; width:30%; font-size: 9px;font-weight: bold;">សរុប</td>
                    <td style="text-align: left; font-size: 9px; width:35%;font-weight: bold;">SUB TOTAL(USD)</td>
                    <td style="text-align: right; font-size: 11px; font-weight: bold;"><div style="width:10px; float: left; font-size: 11px;">$</div><?php echo number_format($salesOrder['SalesOrder']['total_amount'], 2); ?></td>
                </tr>
                <tr>
                    <td class="first" style="border-bottom: none; border-left: none; text-align: left; font-size: 9px;font-weight: bold;">បញ្ចុះតំលៃ</td>
                    <td style="text-align: left; font-size: 9px; font-weight: bold;">DISCOUNT(USD)</td>
                    <td style="text-align: right; font-size: 11px; font-weight: bold; width:20%;"><div style="width:10px; float: left; font-size: 11px;">$</div><?php echo number_format($salesOrder['SalesOrder']['discount'], 2); ?></td>
                </tr>
                <tr>
                    <td class="first" style="border-bottom: none; border-left: none; text-align: left; font-size: 9px;font-weight: bold;">សរុបចុងក្រោយ</td>
                    <td style="text-align: left; font-size: 9px; font-weight: bold;">GRAND TOTAL(USD)</td>
                    <td style="text-align: right; font-size: 11px; font-weight: bold; width:20%;"><div style="width:10px; float: left; font-size: 11px;">$</div><?php echo number_format($salesOrder['SalesOrder']['total_amount'] - $salesOrder['SalesOrder']['discount'], 2); ?></td>
                </tr>
                <tr>
                    <td class="first" style="border-bottom: none; border-left: 1px solid #000; text-align: left;font-size: 9px; border-top: 1px solid #000;font-weight: bold;">ប្រាក់ទទួល</td>
                    <td style="text-align: left; font-size: 9px; border-top: 1px solid #000;font-weight: bold;" >Received(USD)</td>
                    <td style="text-align: right;font-size: 11px; font-weight: bold; width:30%; border-top: 1px solid #000; border-right: 1px solid #000;"><div style="width:10px; float: left; font-size: 11px;">$</div><?php echo number_format($salesOrderReceipt['SalesOrderReceipt']['amount_us'], 2); ?></td>
                </tr>
                <tr>
                    <td class="first" style="border-bottom: 1px solid #000; border-left: 1px solid #000; text-align: left;font-size: 9px;font-weight: bold;">ប្រាក់ទទួល</td>
                    <td style="text-align: left; font-size: 9px; border-bottom: 1px solid #000;font-weight: bold;" >Received(RIEL)</td>
                    <td style="text-align: right;font-size: 11px; font-weight: bold; width:30%; border-bottom: 1px solid #000;  border-right: 1px solid #000;"><div style="width:10px; float: left; font-size: 11px;">R</div><?php echo number_format($salesOrderReceipt['SalesOrderReceipt']['amount_kh'], 0); ?></td>
                </tr>
                <tr>
                    <?php
                    if ($salesOrderReceipt['SalesOrderReceipt']['change'] > 0) {
                        ?>
                        <td class="first" style="border-bottom: none; border-left: none; text-align: left;font-size: 9px;font-weight: bold;">ប្រាក់អាប់</td>
                        <td style="text-align: left;font-size: 9px;font-weight: bold;" >CHANGE(USD)</td>
                        <td style="text-align: right;font-size: 11px; font-weight: bold" ><div style="width:10px; float: left; font-size: 11px;">$</div><?php echo number_format($salesOrderReceipt['SalesOrderReceipt']['change'], 2); ?></td>
                        <?php
                    } else {
                        ?>
                        <td class="first" style="border-bottom: none; border-left: none; text-align: left;font-size: 9px;font-weight: bold;">ប្រាក់អាប់</td>
                        <td style="text-align: left;font-size: 9px;font-weight: bold;" >CHANGE(Riel)</td>
                        <td style="text-align: right;font-size: 11px; font-weight: bold" ><div style="width:10px; float: left; font-size: 11px;">R</div><?php echo number_format($salesOrderReceipt['SalesOrderReceipt']['change_kh'], 0); ?></td>
                        <?php
                    }
                    ?>
                </tr>
            </table>
        </div>
        <div style="clear:both;"></div>
        <div id="print-footer" style="margin-top: 15px;">
            1 $ = <?php echo $lastExchangeRate['ExchangeRate']['riel']; ?> Riel
            <div style="font-size:8px; text-align: center">
                <span style="margin-bottom: 0px; font-size:8px; display: block;">ទំនិញទិញហើយមិនអាចប្តូរវិញបានទេ Purchased not returnable.</span>
                Thank you for shopping. Please come again.
            </div>
            <span style="font-size: 6px; display: block; margin-top: 0px;">Print: <?php echo date("d/m/Y H:i:s") ?></span>
        </div>
        <div>
            <input type="button" value="<?php echo ACTION_PRINT; ?>" id='btnDisappearPrint' onClick='printNow();' class='noprint'>
        </div>
        <div style="clear:both"></div>
    </div>
</div>
<script type="text/javascript">
    function printNow(){
        var footerLeft = "";
        var footerRight = "";
        var footerCenter = "";
        var headerRight = "";
        var w = window;
        try {
            var printerPOS = getPrinterSm(<?php echo $salesOrder['Location']['id']; ?>).toString().split("|**|")[0];
            var silent = parseFloat(getPrinterSm(<?php echo $salesOrder['Location']['id']; ?>).toString().split("|**|")[1]);
            var scale = 100;
            printJs(printerPOS,w,footerLeft,footerRight,footerCenter,1,scale,headerRight,silent); 
        } catch (e) {
            w.print();
        }
        w.close();
		
    }
</script>