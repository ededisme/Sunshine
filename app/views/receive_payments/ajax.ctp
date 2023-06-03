<?php 
include("includes/function.php");
echo $this->element('prevent_multiple_submit'); 
?>
<script type="text/javascript">
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        // Remove Disabed Submit Button
        $(".btnSaveReceivePayment").removeAttr('disabled');
        // Form Validation
        $("#CustomerPaymentForm").validationEngine('detach');
        $("#CustomerPaymentForm").validationEngine('attach');
        // Check Bf Save
        $(".btnSaveReceivePayment").unbind("click");
        $(".btnSaveReceivePayment").click(function(){
            if(checkBfSaveReceivePayment() == true){
                return true;
            }else{
                confirmCheckPaidReceivePayment();
                return false;
            }
        });
        $("#CustomerPaymentForm").ajaxForm({
            beforeSerialize: function($form, options) {
                $("#CustomerPaymentDate").datepicker("option", "dateFormat", "yy-mm-dd");
                $(".CustomerPaymentDueDate").datepicker("option", "dateFormat", "yy-mm-dd");
            },
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveCustomerPayment").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $("#CustomerPaymentDate").datepicker("option", "dateFormat", "dd/mm/yy");
                $("#CustomerPaymentReference").val("");
                $("#CustomerPaymentNote").val("");
                $(".txtSaveCustomerPayment").html("<?php echo ACTION_SAVE; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                loadTablePaymentCustomer();
                $("#CustomerPaymentBranchId").change();
                $("button[type=submit]", $("#CustomerPaymentForm")).removeAttr('disabled');
                // alert message
                if(result != '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>' && result != '<?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?>'){
                    createSysAct('Receive Payment', 'Add', 2, result);
                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                }else {
                    createSysAct('Receive Payment', 'Add', 1, '');
                    // alert message
                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result+'</p>');
                }
                $("#dialog").dialog({
                    title: '<?php echo DIALOG_INFORMATION; ?>',
                    resizable: false,
                    modal: true,
                    width: 'auto',
                    height: 'auto',
                    open: function(event, ui){
                        $(".ui-dialog-buttonpane").show();
                    },
                    buttons: {
                        '<?php echo ACTION_CLOSE; ?>': function() {
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
        $(".CustomerPaymentDueDate").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd/mm/yy',
            minDate: 0
        }).unbind("blur");
        
        $(".CustomerPaymentAmountUs, .CustomerPaymentAmountOther, .CustomerPaymentDiscountUs, .CustomerPaymentDiscountOther").focus(function(){
            if($(this).val() == 0 || $(this).val() == '0.00'){
                $(this).val("");
            }
        });
        
        $(".CustomerPaymentAmountUs, .CustomerPaymentDiscountUs").keyup(function(){
            if($(this).attr("class") == "CustomerPaymentDiscountUs"){
                $(this).closest("tr").find(".customer_payment_is_paid").removeAttr('checked');
                $(".CustomerPaymentAmountUs, .CustomerPaymentAmountOther").val(0);
                // Check Discount Amount
                var amountPaid    = replaceNum($(this).closest("tr").find(".txtAmountPaidCustomerPayment").text());
                var DiscountAmt   = replaceNum($(this).val());
                var DiscountOther = convertToMainCurrencyReceivePayment(replaceNum($(this).closest("tr").find(".CustomerPaymentDiscountOther").val()));
                if(amountPaid < (DiscountAmt + DiscountOther)){
                    var disBalance = amountPaid - DiscountOther;
                    $(this).val(disBalance);
                }
            }
            calcCustomerPayment();
        });
        
        $(".CustomerPaymentAmountOther, .CustomerPaymentDiscountOther").keyup(function(){
            if($("#exchangeRateSalesReceive").find("option:selected").val() != ''){
                if($(this).attr("class") == "CustomerPaymentDiscountOther"){
                    $(this).closest("tr").find(".customer_payment_is_paid").removeAttr('checked');
                    $(".CustomerPaymentAmountUs, .CustomerPaymentAmountOther").val(0);
                    // Check Discount Amount
                    var amountPaid    = replaceNum($(this).closest("tr").find(".txtAmountPaidCustomerPayment").text());
                    var DiscountAmt   = replaceNum($(this).closest("tr").find(".CustomerPaymentDiscountUs").val());
                    var DiscountOther = convertToMainCurrencyReceivePayment(replaceNum($(this).val()));
                    if(amountPaid < (DiscountAmt + DiscountOther)){
                        var disBalance = convertToOtherCurrencyReceivePayment(amountPaid - DiscountAmt);
                        $(this).val(disBalance);
                    }
                }
                calcCustomerPayment();
            } else {
                $(this).val("");
            }
        });
        
        $(".CustomerPaymentAmountUs, .CustomerPaymentAmountOther, .CustomerPaymentDiscountUs, .CustomerPaymentDiscountOther").blur(function(){
            if($(this).closest("tr").find(".CustomerPaymentAmountUs").val() > 0 || $(this).closest("tr").find(".CustomerPaymentAmountOther").val() > 0){
                $(this).closest("tr").find(".customer_payment_is_paid").attr('checked','checked');
            } else {
                $(this).closest("tr").find(".customer_payment_is_paid").removeAttr('checked');
            }
            if($(this).val() == ''){
                $(this).val('0');
            }
            calcCustomerPayment();
        });
        
        $(".customer_payment_is_paid").change(function(){
            if($(this).is(':checked')){
                var totalBalance = replaceNum($(this).closest("tr").find(".txtAmountPaidCustomerPayment").text()) - replaceNum($(this).closest("tr").find(".CustomerPaymentDiscountUs").val());
                $(this).closest("tr").find(".CustomerPaymentAmountUs").val(totalBalance);
            }else{
                $(this).closest("tr").find(".CustomerPaymentAmountUs").val(0);
            }
            calcCustomerPayment();
        });
        // prevent enter key
        $(".CustomerPaymentAmountUs").keypress(function(e){
            if((e.which && e.which == 13) || e.keyCode == 13){
                return false;
            }
        });
        $(".CustomerPaymentBalance").keypress(function(e){
            if((e.which && e.which == 13) || e.keyCode == 13){
                return false;
            }
        });
        $(".CustomerPaymentDueDate").keypress(function(e){
            if((e.which && e.which == 13) || e.keyCode == 13){
                return false;
            }
        });
        // Get Reference Code
        var mCode = $("#CustomerPaymentBranchId").find("option:selected").attr("mcode");
        $("#CustomerPaymentReference").val("<?php echo date("y"); ?>"+mCode);
        // Currency Payment Select
        $("#exchangeRateSalesReceive").change(function(){
            var symbol   = $(this).find("option:selected").attr("symbol");
            var exRateId = $(this).find("option:selected").attr("exrate");
            if(symbol != ''){
                $("#CustomerPaymentAmountOther").removeAttr("readonly");
                $("#CustomerPaymentDiscountOther").removeAttr("readonly");
            } else {
                $("#CustomerPaymentAmountOther").val(0);
                $("#CustomerPaymentAmountOther").attr("readonly", true);
                $("#CustomerPaymentDiscountOther").val(0);
                $("#CustomerPaymentDiscountOther").attr("readonly", true);
            }
            $(".paidOtherCurrencySymbolReceive").html(symbol);
            $("#exchangeRateIdReceive").val(exRateId);  
            $(".CustomerPaymentBalance").each(function(){
                if($("#exchangeRateSalesReceive").find("option:selected").val() != ''){
                    var balance = replaceNum($(this).val());
                    var balanceOther = convertToOtherCurrencyReceivePayment(balance);
                    $(this).closest("tr").find(".CustomerPaymentBalanceOther").val(converDicemalJS(balanceOther).toFixed(2));
                } else {
                    $(this).closest("tr").find(".CustomerPaymentBalanceOther").val(0);
                }
            });
        });
    });
    function confirmCheckPaidReceivePayment(){
        var question = "<?php echo MESSAGE_CONFIRM_PAID_BEFORE_SAVE; ?>";
        $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+question+'</p>');
        $("#dialog").dialog({
            title: '<?php echo DIALOG_CONFIRMATION; ?>',
            resizable: false,
            modal: true,
            width: 'auto',
            height: 'auto',
            position:'center',
            closeOnEscape: false,
            open: function(event, ui){
                $(".ui-dialog-buttonpane").show(); 
                $(".ui-dialog-titlebar-close").hide();
            },
            buttons: {
                '<?php echo ACTION_CLOSE; ?>': function() {
                    $(this).dialog("close");
                }
            }
        });
    }
    
    function calcCustomerPayment(){        
        var totalPaid     = 0;  
        var totalPayUs    = 0;
        var totalPayOther = 0;
        var totalDisOther = 0;
        var totalDisUs    = 0;
        var totalBalance  = 0;
        var totalBalanceOther = 0;
        $(".CustomerPaymentAmountUs").delay(10).each(function(){
            var amountPaid    = replaceNum($(this).closest("tr").find(".txtAmountPaidCustomerPayment").text());
            var paid          = replaceNum($(this).val());
            var amountOther   = replaceNum($(this).closest("tr").find(".CustomerPaymentAmountOther").val());
            var Discount      = replaceNum($(this).closest("tr").find(".CustomerPaymentDiscountUs").val());
            var DiscountOther = replaceNum($(this).closest("tr").find(".CustomerPaymentDiscountOther").val());
            var balance       = 0;
            var balanceOther  = 0;
            totalPaid         = converDicemalJS(paid + convertToMainCurrencyReceivePayment(amountOther) + Discount + convertToMainCurrencyReceivePayment(DiscountOther));            
            if(amountPaid > totalPaid){
                balance = converDicemalJS(amountPaid - totalPaid);
                if($("#exchangeRateSalesReceive").find("option:selected").val() != ''){
                    balanceOther = convertToOtherCurrencyReceivePayment(balance);
                }
                $(this).closest("tr").find(".CustomerPaymentBalance").val(converDicemalJS(balance).toFixed(2));
                $(this).closest("tr").find(".CustomerPaymentBalanceOther").val(converDicemalJS(balanceOther).toFixed(2));
                $(this).closest("tr").find(".CustomerPaymentDueDate").show();
            }else{
                balance = converDicemalJS(amountPaid - (Discount + convertToMainCurrencyReceivePayment(DiscountOther)));
                $(this).val((balance).toFixed(3));
                $(this).closest("tr").find(".CustomerPaymentAmountOther").val(0);
                $(this).closest("tr").find(".CustomerPaymentBalance").val(0);
                $(this).closest("tr").find(".CustomerPaymentBalanceOther").val(0);
                $(this).closest("tr").find(".CustomerPaymentDueDate").hide();
            }
            if(totalPaid==0){
                $(this).closest("tr").find(".CustomerPaymentDueDate").hide();
            }
            totalPayUs        += replaceNum($(this).val());
            totalPayOther     += replaceNum($(this).closest("tr").find(".CustomerPaymentAmountOther").val());
            totalDisUs        += replaceNum($(this).closest("tr").find(".CustomerPaymentDiscountUs").val());
            totalDisOther     += replaceNum($(this).closest("tr").find(".CustomerPaymentDiscountOther").val());
            totalBalance      += balance;
            totalBalanceOther += balanceOther;
        });
        $("#totalPayCustomerPayment").text(converDicemalJS(totalPayUs).toFixed(2));
        $("#totalPayOtherCustomerPayment").text(converDicemalJS(totalPayOther).toFixed(2));
        $("#totalDiscountCustomerPayment").text(converDicemalJS(totalDisUs).toFixed(2));
        $("#totalDiscountOtherCustomerPayment").text(converDicemalJS(totalDisOther).toFixed(2));
        $("#totalBalanceCustomerPayment").text(converDicemalJS(totalBalance).toFixed(2));
        $("#totalBalanceCustomerPaymentOther").text(converDicemalJS(totalBalanceOther).toFixed(2));
    }
    
    function checkBfSaveReceivePayment(){
        var formName     = "#CustomerPaymentForm";
        var validateBack = $(formName).validationEngine("validate");
        if(!validateBack){
            return false;
        }else{
            if($(".CustomerPaymentAmountUs").val() == undefined){
                return false;
            }else{
                var result = false;
                $(".customer_payment_is_paid").each(function(){
                    if($(this).is(':checked')){
                        result = true;
                    }
                });
                return result;
            }
        }
    }
    
    function convertToMainCurrencyReceivePayment(val){
        var exchangeRate  = replaceNum($("#exchangeRateSalesReceive").find("option:selected").attr("ratesale"));
        var amountConvert = 0;
        if(exchangeRate > 0){
            amountConvert = converDicemalJS(replaceNum(val) / exchangeRate);
        }
        return amountConvert;
    }
    
    function convertToOtherCurrencyReceivePayment(val){
        var exchangeRate  = replaceNum($("#exchangeRateSalesReceive").find("option:selected").attr("ratesale"));
        var amountConvert = 0;
        if(exchangeRate > 0){
            amountConvert = converDicemalJS(replaceNum(val) * exchangeRate);
        }
        return amountConvert;
    }
</script>
<table style="width: 300px; float: left;">
    <tr>
        <td colspan="2">
            <input type="hidden" name="data[CustomerPayment][exchange_rate_id]" id="exchangeRateIdReceive" />
            <?php
            $sqlMainCurrency = mysql_query("SELECT symbol FROM currency_centers WHERE id = (SELECT currency_center_id FROM companies WHERE id = ".$companyId.")");
            $rowMainCurrency = mysql_fetch_array($sqlMainCurrency);
            $sqlCurrency = mysql_query("SELECT currency_centers.name, currency_centers.symbol, branch_currencies.rate_to_sell FROM branch_currencies INNER JOIN currency_centers ON currency_centers.id = branch_currencies.currency_center_id WHERE branch_currencies.branch_id = ".$branchId);
            if(mysql_num_rows($sqlCurrency)){
            ?>
            <table class="table" cellspacing="0" >
                <thead>
                    <tr>
                        <th class="first" style="width:100%;" colspan="2"><?php echo MENU_EXCHANGE_RATE_LIST; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while($rowCurrency = mysql_fetch_array($sqlCurrency)){
                    ?>
                    <tr>
                        <td class="first" style="text-align:center; font-size: 12px; width: 25%;">1 <?php echo $rowMainCurrency[0]; ?> =</td>
                        <td style="font-size: 12px;"><?php echo number_format($rowCurrency['rate_to_sell'], 9); ?> <?php echo $rowCurrency['symbol']; ?></td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php
            }
            ?>
        </td>
    </tr>
</table>
<table style="width: 400px; float: right;">
    <tr>
        <td>
            <label for="exchangeRateSalesReceive"><?php echo TABLE_PAID_WITH_OTHER_CURRENCY; ?></label> :
        </td>
        <td>
            <select name="data[CustomerPayment][currency_center_id]" id="exchangeRateSalesReceive" style="width: 200px;">
                <option value="" symbol="" exrate="" ratesale=""><?php echo INPUT_SELECT; ?></option>
                <?php 
                $sqlCurSelect = mysql_query("SELECT currency_centers.id, currency_centers.name, currency_centers.symbol, branch_currencies.exchange_rate_id, branch_currencies.rate_to_sell FROM branch_currencies INNER JOIN currency_centers ON currency_centers.id = branch_currencies.currency_center_id WHERE branch_currencies.branch_id = ".$branchId);
                while($rowCurSelect = mysql_fetch_array($sqlCurSelect)){
                ?>
                <option value="<?php echo $rowCurSelect['id']; ?>" symbol="<?php echo $rowCurSelect['symbol']; ?>" exrate="<?php echo $rowCurSelect['exchange_rate_id']; ?>" ratesale="<?php echo $rowCurSelect['rate_to_sell']; ?>"><?php echo $rowCurSelect['name']; ?></option>
                <?php
                }
                ?>
            </select>
        </td>
    </tr>
</table>
<div style="clear: both;"></div>
<table class="table" cellspacing="0">
    <thead>
        <tr>
            <th class="first"><?php echo TABLE_NO; ?></th>
            <th style="width: 90px !important;"><?php echo TABLE_DATE; ?></th>
            <th style="width: 130px !important;"><?php echo TABLE_INVOICE_CODE; ?></th>
            <th><?php echo TABLE_CUSTOMER; ?></th>
            <th style="width: 100px !important;"><?php echo GENERAL_AMOUNT; ?></th>
            <th style="width: 100px !important;"><?php echo GENERAL_PAID; ?></th>
            <th style="width: 100px !important;"><?php echo GENERAL_PAID; ?></th>
            <th style="width: 100px !important;"><?php echo POS_DISCOUNTS; ?></th>
            <th style="width: 100px !important;"><?php echo POS_DISCOUNTS; ?></th>
            <th style="width: 100px !important;"><?php echo GENERAL_BALANCE; ?></th>
            <th style="width: 100px !important;"><?php echo GENERAL_BALANCE; ?></th>
            <th style="width: 100px !important;"><?php echo GENERAL_AGING; ?></th>
            <th style="width: 50px !important;"></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $index=1;
        $totalAmount=0;
        $balance=0;
        $query=mysql_query("SELECT sales_orders.id,
                                sales_orders.order_date,
                                sales_orders.so_code,
                                currency_centers.symbol,
                                CONCAT_WS(' ', customers.customer_code, '-', customers.name) AS customer_name,
                                sales_orders.balance
                            FROM sales_orders LEFT JOIN customers ON customers.id = sales_orders.customer_id
                            INNER JOIN currency_centers ON currency_centers.id = sales_orders.currency_center_id
                            WHERE status>0 AND balance>0
                                AND company_id=".$companyId." AND branch_id=".$branchId."
                                ".($customerId!=""?' AND customer_id='.$customerId:'')."
                            ORDER BY order_date");
        if(mysql_num_rows($query)){
            while($data=mysql_fetch_array($query)){
                $rnd = rand();
                $balance+=$data['balance'];
        ?>
        <tr>
            <td class="first">
                <?php echo $index++; ?>
                <input type="hidden" name="id[]" value="<?php echo $data['id']; ?>" />
            </td>
            <td><?php echo dateShort($data['order_date']); ?></td>
            <td><?php echo $data['so_code']; ?></td>
            <td><input type="text" value="<?php echo $data['customer_name']; ?>" style="width: 99%;" /></td>
            <td class="txtAmountPaidCustomerPayment">
                <input type="hidden" name="amount_due[]" value="<?php echo number_format($data['balance'],2); ?>" />
                <?php echo number_format($data['balance'],2)." ".$data['symbol']; ?>
            </td>
            <td>
                <div class="inputContainer">
                    <input type="text" id="CustomerPaymentAmountUs<?php echo $rnd; ?>" name="amount_us[]" class="CustomerPaymentAmountUs validate[required,custom[number]]" value="0" style="width: 60px;" /> <?php echo $data['symbol']; ?>
                </div>
            </td>
            <td>
                <div class="inputContainer">
                    <input type="text" id="CustomerPaymentAmountOther<?php echo $rnd; ?>" name="amount_other[]" class="CustomerPaymentAmountOther validate[custom[number]]" value="0" style="width: 60px;" /> <span class="paidOtherCurrencySymbolReceive"></span>
                </div>
            </td>
            <td>
                <div class="inputContainer">
                    <input type="text" id="CustomerPaymentDiscountUs<?php echo $rnd; ?>" name="discount_us[]" class="CustomerPaymentDiscountUs" value="0" style="width: 60px;" /> <?php echo $data['symbol']; ?>
                </div>
            </td>
            <td>
                <div class="inputContainer">
                    <input type="text" id="CustomerPaymentDiscountOther<?php echo $rnd; ?>" name="discount_other[]" class="CustomerPaymentDiscountOther" value="0" style="width: 60px;" /> <span class="paidOtherCurrencySymbolReceive"></span>
                </div>
            </td>
            <td><input type="text" id="CustomerPaymentBalance<?php echo $rnd; ?>" name="balance_us[]" class="CustomerPaymentBalance" value="<?php echo number_format($data['balance'],2); ?>" style="width: 60px;" readonly="readonly" /> <?php echo $data['symbol']; ?></td>
            <td><input type="text" id="CustomerPaymentBalanceOther<?php echo $rnd; ?>" class="CustomerPaymentBalanceOther" value="0" style="width: 60px;" readonly="readonly" /> <span class="paidOtherCurrencySymbolReceive"></td>
            <td>
                <div class="inputContainer">
                    <input type="text" id="CustomerPaymentDueDate<?php echo $rnd; ?>" name="due_date[]" class="CustomerPaymentDueDate" value="" style="width: 60px;display: none;" readonly="readonly" />
                </div>
            </td>
            <td>
                <input type="checkbox" class="customer_payment_is_paid" />
            </td>
        </tr>
            <?php } ?>
        <tr>
            <td class="first" colspan="4" style="text-align: right;font-weight: bold;"><?php echo TABLE_TOTAL; ?></td>
            <td><?php echo number_format($balance,2); ?></td>
            <td id="totalPayCustomerPayment"></td>
            <td id="totalPayOtherCustomerPayment"></td>
            <td id="totalDiscountCustomerPayment"></td>
            <td id="totalDiscountOtherCustomerPayment"></td>
            <td id="totalBalanceCustomerPayment"></td>
            <td id="totalBalanceCustomerPaymentOther"></td>
            <td></td>
            <td></td>
        </tr>
        <?php }else{ ?>
        <tr>
            <td colspan="14" class="dataTables_empty first"><?php echo TABLE_NO_MATCHING_RECORD; ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>