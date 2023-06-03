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
    include("includes/function.php");
    $msg = 'CREDIT MEMO';
    echo $this->element('/print/header-invoice', array('msg' => $msg, 'telephone' => $creditMemo['Branch']['telephone'], 'vat' => '', 'address' => $creditMemo['Branch']['address'], 'logo' => $creditMemo['Company']['photo'], 'title' => $creditMemo['Branch']['name']));
    ?>
    <div style="height: 20px"></div>
    <table width="100%">
        <tr>
            <td style="width: 15%; text-transform: uppercase; font-size: 12px;"><?php echo TABLE_CREDIT_MEMO_DATE; ?> :</td>
            <td style="font-size: 12px;"><?php echo dateShort($creditMemo['CreditMemo']['order_date']); ?></td>
            <td style="width: 15%; text-transform: uppercase; font-size: 12px;"><?php echo TABLE_CREDIT_MEMO_NUMBER; ?> :</td>
            <td style="font-size: 12px;"><?php echo $creditMemo['CreditMemo']['cm_code']; ?></td>
            <td style="width: 15%; text-transform: uppercase; font-size: 12px;"><?php echo TABLE_REASON; ?> :</td>
            <td style="font-size: 12px;"><?php echo $creditMemo['Reason']['name']; ?></td>
        </tr>
        <tr>
            <td style="width: 15%; text-transform: uppercase; font-size: 12px;"><?php echo TABLE_INVOICE_DATE; ?> :</td>
            <td style="font-size: 12px;"><?php if($creditMemo['CreditMemo']['invoice_date'] != ''){ echo dateShort($creditMemo['CreditMemo']['invoice_date']); } ?></td>
            <td style="width: 15%; text-transform: uppercase; font-size: 12px;"><?php echo TABLE_INVOICE_NO; ?> :</td>
            <td style="font-size: 12px;" colspan="3"><?php echo $creditMemo['CreditMemo']['invoice_code']; ?></td>
        </tr>
        <tr>
            <td style="width: 15%; text-transform: uppercase; font-size: 12px;"><?php echo TABLE_NOTE; ?> :</td>
            <td style="font-size: 12px;" colspan="5"><?php echo $creditMemo['CreditMemo']['note']; ?></td>
        </tr>
    </table>
    <br />
    <div>
        <div>
            <table class="table_print">
                <tr>
                    <th class="first" style="width: 6%; font-size: 12px; height: 30px; padding-top: 0px; padding-bottom: 0px;"><?php echo TABLE_NO; ?></th>
                    <th style="font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo TABLE_SKU; ?></th>
                    <th style="width: 33%; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo TABLE_PRODUCT_NAME; ?></th>
                    <th style="width: 7%; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo TABLE_QTY; ?></th>
                    <th style="width: 7%; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo TABLE_F_O_C; ?></th>
                    <th style="width: 7%; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo TABLE_UOM; ?></th>
                    <th style="width: 11%; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo SALES_ORDER_UNIT_PRICE; ?></th>
                    <th style="width: 10%; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo GENERAL_DISCOUNT; ?></th>
                    <th style="width: 11%; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo SALES_ORDER_TOTAL_PRICE; ?></th>
                </tr>
                <?php
                $index = 0;
                if (!empty($creditMemoDetails)) {
                    foreach ($creditMemoDetails as $creditMemoDetail) {
                        // Check Name With Customer
                        $productName = $creditMemoDetail['Product']['name'];
                        $sqlProCus   = mysql_query("SELECT name FROM product_with_customers WHERE product_id = ".$creditMemoDetail['Product']['id']." AND customer_id = ".$creditMemo['Customer']['id']." ORDER BY created DESC LIMIT 1");
                        if(@mysql_num_rows($sqlProCus)){
                            $rowProCus = mysql_fetch_array($sqlProCus);
                            $productName = $rowProCus['name'];
                        }
                        $discount = $creditMemoDetail['CreditMemoDetail']['discount_amount'];
                ?>
                        <tr>
                            <td class="first" style="text-align: center; font-size: 12px; height: 30px; padding-top: 0px; padding-bottom: 0px;"><?php echo ++$index; ?></td>
                            <td style="font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo $creditMemoDetail['Product']['code']; ?></td>
                            <td style="font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo $productName; ?></td>
                            <td style="text-align: center;white-space: nowrap; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo number_format($creditMemoDetail['CreditMemoDetail']['qty'], 0); ?> </td>
                            <td style="text-align: center;white-space: nowrap; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo number_format($creditMemoDetail['CreditMemoDetail']['qty_free'], 0); ?> </td>
                            <td style="text-align: center;white-space: nowrap; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo $creditMemoDetail['Uom']['abbr']; ?> </td>
                            <td style="text-align: right; font-size: 12px; padding-top: 0px; padding-bottom: 0px;">
                                <span style="float: left; width: 12px; font-size: 11px;"><?php echo $creditMemo['CurrencyCenter']['symbol']; ?></span><?php echo number_format($creditMemoDetail['CreditMemoDetail']['unit_price'], 3); ?>
                            </td>
                            <td style="text-align: right; font-size: 12px; padding-top: 0px; padding-bottom: 0px;">
                                <span style="float: left; width: 12px; font-size: 11px;"><?php echo $creditMemo['CurrencyCenter']['symbol']; ?></span><?php echo number_format($discount, 3); ?>
                            </td>
                            <td style="text-align: right; font-size: 12px; padding-top: 0px; padding-bottom: 0px;">
                                <span style="float: left; width: 12px; font-size: 11px;"><?php echo $creditMemo['CurrencyCenter']['symbol']; ?></span><?php echo number_format(($creditMemoDetail['CreditMemoDetail']['total_price'] - $discount), 3); ?>
                            </td>
                        </tr>
                <?php
                    }
                }
                if (!empty($creditMemoServices)) {
                    foreach ($creditMemoServices as $creditMemoService) {
                        $uomName = '';
                        if($creditMemoService['Service']['uom_id'] != ''){
                            $sqlUom = mysql_query("SELECT abbr FROM uoms WHERE id = ".$creditMemoService['Service']['uom_id']);
                            $rowUom = mysql_fetch_array($sqlUom);
                            $uomName = $rowUom[0];
                        }
                        $discount = $creditMemoService['CreditMemoService']['discount_amount'];
                ?>
                        <tr>
                            <td class="first" style="text-align: center; font-size: 12px; height: 30px; padding-top: 0px; padding-bottom: 0px;"><?php echo++$index; ?></td>
                            <td style="font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo $creditMemoService['Service']['code']; ?></td>
                            <td style="font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo $creditMemoService['Service']['name']; ?></td>
                            <td style="text-align: center; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo number_format($creditMemoService['CreditMemoService']['qty'], 0); ?></td>
                            <td style="text-align: center; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo number_format($creditMemoService['CreditMemoService']['qty_free'], 0); ?></td>
                            <td style="text-align: center; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo $uomName; ?></td>
                            <td style="text-align: right; font-size: 12px; padding-top: 0px; padding-bottom: 0px;">
                                <span style="float: left; width: 12px; font-size: 11px;"><?php echo $creditMemo['CurrencyCenter']['symbol']; ?></span><?php echo number_format($creditMemoService['CreditMemoService']['unit_price'], 3); ?>
                            </td>
                            <td style="text-align: right; font-size: 12px; padding-top: 0px; padding-bottom: 0px;">
                                <span style="float: left; width: 12px; font-size: 11px;"><?php echo $creditMemo['CurrencyCenter']['symbol']; ?></span><?php echo number_format($discount, 3); ?>
                            </td>
                            <td style="text-align: right; font-size: 12px; padding-top: 0px; padding-bottom: 0px;">
                                <span style="float: left; width: 12px; font-size: 11px;"><?php echo $creditMemo['CurrencyCenter']['symbol']; ?></span><?php echo number_format(($creditMemoService['CreditMemoService']['total_price'] - $discount), 3); ?>
                            </td>
                        </tr>
                <?php
                    }
                }
                if (!empty($creditMemoMiscs)) {
                    foreach ($creditMemoMiscs as $creditMemoMisc) {
                        $discount = $creditMemoMisc['CreditMemoMisc']['discount_amount'];
                ?>
                        <tr>
                            <td class="first" style="text-align: center; font-size: 12px; height: 30px; padding-top: 0px; padding-bottom: 0px;"><?php echo++$index; ?></td>
                            <td style="font-size: 12px; padding-top: 0px; padding-bottom: 0px;"></td>
                            <td style="font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo $creditMemoMisc['CreditMemoMisc']['description']; ?></td>
                            <td style="text-align: center;font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo number_format($creditMemoMisc['CreditMemoMisc']['qty'], 0); ?> </td>
                            <td style="text-align: center;font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo number_format($creditMemoMisc['CreditMemoMisc']['qty_free'], 0); ?> </td>
                            <td style="text-align: center;font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><?php echo $creditMemoMisc['Uom']['abbr']; ?></td>
                            <td style="text-align: right; font-size: 12px; padding-top: 0px; padding-bottom: 0px;">
                                <span style="float: left; width: 12px; font-size: 11px;"><?php echo $creditMemo['CurrencyCenter']['symbol']; ?></span><?php echo number_format($creditMemoMisc['CreditMemoMisc']['unit_price'], 3); ?>
                            </td>
                            <td style="text-align: right; font-size: 12px; padding-top: 0px; padding-bottom: 0px;">
                                <span style="float: left; width: 12px; font-size: 11px;"><?php echo $creditMemo['CurrencyCenter']['symbol']; ?></span><?php echo number_format($discount, 3); ?>
                            </td>
                            <td style="text-align: right; font-size: 12px; padding-top: 0px; padding-bottom: 0px;">
                                <span style="float: left; width: 12px; font-size: 11px;"><?php echo $creditMemo['CurrencyCenter']['symbol']; ?></span><?php echo number_format(($creditMemoMisc['CreditMemoMisc']['total_price'] - $discount), 3); ?>
                            </td>
                        </tr>
                <?php
                    }
                }
                ?>
                <tr>
                    <td class="first" style="border-bottom: none; border-left: none;text-align: right; font-size: 12px; height: 30px; padding-top: 0px; padding-bottom: 0px;" colspan="8"><b style="font-size: 12px;"><?php echo TABLE_SUB_TOTAL; ?></b></td>
                    <td style="text-align: right; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><span style="float: left; width: 12px; font-size: 11px;"><?php echo $creditMemo['CurrencyCenter']['symbol']; ?></span><?php echo number_format($creditMemo['CreditMemo']['total_amount'], 3); ?></td>
                </tr>
                <?php
                if($creditMemo['CreditMemo']['total_vat'] > 0){
                ?>
                <tr>
                    <td class="first" style="border-bottom: none; border-left: none;text-align: right; font-size: 12px; height: 30px; padding-top: 0px; padding-bottom: 0px;" colspan="8"><b style="font-size: 12px;"><?php echo TABLE_VAT ?> (<?php echo number_format($creditMemo['CreditMemo']['vat_percent'], 3); ?>%)</b></td>
                    <td style="text-align: right; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><span style="float: left; width: 12px; font-size: 11px;"><?php echo $creditMemo['CurrencyCenter']['symbol']; ?></span><?php echo number_format($creditMemo['CreditMemo']['total_vat'], 3); ?></td>
                </tr>
                <?php
                }
                if($creditMemo['CreditMemo']['discount'] > 0){
                ?>
                <tr>
                    <td class="first" style="border-bottom: none; border-left: none;text-align: right; font-size: 12px; height: 30px; padding-top: 0px; padding-bottom: 0px;" colspan="8"><b style="font-size: 12px;"><?php echo GENERAL_DISCOUNT; ?></b></td>
                    <td style="text-align: right; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><span style="float: left; width: 12px; font-size: 11px;"><?php echo $creditMemo['CurrencyCenter']['symbol']; ?></span><?php echo number_format($creditMemo['CreditMemo']['discount'], 3); ?></td>
                </tr>
                <?php
                }
                ?>
                <?php
                if($creditMemo['CreditMemo']['mark_up'] > 0){
                ?>
                <tr>
                    <td class="first" style="border-bottom: none; border-left: none;text-align: right; font-size: 12px; height: 30px; padding-top: 0px; padding-bottom: 0px;" colspan="8"><b style="font-size: 12px;"><?php echo TABLE_MARK_UP; ?></b></td>
                    <td style="text-align: right; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><span style="float: left; width: 12px; font-size: 11px;"><?php echo $creditMemo['CurrencyCenter']['symbol']; ?></span><?php echo number_format($creditMemo['CreditMemo']['mark_up'], 3); ?></td>
                </tr>
                <?php
                }
                ?>
                <tr>
                    <td class="first" style="border-bottom: none; border-left: none;text-align: right; font-size: 12px; height: 30px; padding-top: 0px; padding-bottom: 0px;" colspan="8"><b style="font-size: 12px;"><?php echo TABLE_TOTAL_AMOUNT; ?></b></td>
                    <td style="text-align: right; font-size: 12px; padding-top: 0px; padding-bottom: 0px;"><span style="float: left; width: 12px; font-size: 11px;"><?php echo $creditMemo['CurrencyCenter']['symbol']; ?></span><?php echo number_format($creditMemo['CreditMemo']['total_amount']+$creditMemo['CreditMemo']['total_vat']+$creditMemo['CreditMemo']['mark_up']-$creditMemo['CreditMemo']['discount'], 3); ?></td>
                </tr>
            </table>
        </div>
        <br />
        <div>
            <table style="width: 100%;">
                <?php
                if(!empty($cmWsales)){
                    $i = 1;
                    $invoices = "";
                    foreach($cmWsales as $cmWsale){
                        if($i == 1){
                            $invoices .= $cmWsale['SalesOrder']['so_code']." (".$cmWsale[0]['total_price']." $)";
                        }else{
                            $invoices .= " ,".$cmWsale['SalesOrder']['so_code']." (".$cmWsale[0]['total_price']." $)";
                        }
                        $i++;
                    }
                ?>
                <tr>
                    <td style="font-size: 12px; font-weight: bold; vertical-align: top; width: 15%; text-decoration: underline;"><?php echo APPLY_TO_INVOICE;?>: </td>
                    <td style="width: 55%; vertical-align: top;font-size: 12px;">
                        <?php echo $invoices; ?>
                    </td>
                    <td></td>
                </tr>
                <?php
                }?>
           </table>
        </div>
        <div style="clear:both"></div>
        <br />
        <div style="float:left;width: 450px">
            <div>
                <input type="button" value="<?php echo ACTION_PRINT; ?>" id='btnDisappearPrint' class='noprint' />
            </div>
        </div>
        <div style="clear:both"></div>
    </div>
    <div style="width: 100%; position: fixed; bottom: 40px;" class="print-footer">
        <table style="width: 100%;">
                <tr>
                    <td style="font-size: 12px;"><?php echo TABLE_ISSUED_BY;?>..............................</td>
                    <td style="text-align: center; font-size: 12px;"><?php echo TABLE_STOCK_ISSUED_BY;?>..............................</td>
                    <td style="text-align: right; font-size: 12px;"><?php echo TABLE_CHECKED_RECEIVE_BY;?>..............................</td>
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
        $("#btnDisappearPrint").click(function(){
            $("#footerPrint").show();
            window.print();
            window.close();
        });
    });
</script>