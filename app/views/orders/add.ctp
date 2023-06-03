<?php
include("includes/function.php");
echo $this->element('prevent_multiple_submit');
$queryClosingDate=mysql_query("SELECT DATE_FORMAT(date,'%d/%m/%Y') FROM account_closing_dates ORDER BY id DESC LIMIT 1");
$dataClosingDate=mysql_fetch_array($queryClosingDate);
// Authentication
$this->element('check_access');
$allowEditTerm   = checkAccess($user['User']['id'], $this->params['controller'], 'editTermsCondition');
$allowEditInvDis = checkAccess($user['User']['id'], $this->params['controller'], 'invoiceDiscount');
?>
<script type="text/javascript">
    var timeSearchOrder = 1;
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        clearOrderDetailOrder();
        loadOrderDetailOrder();
        loadAutoCompleteOff();
        // Hide Branch
        $("#OrderBranchId").filterOptions('com', '0', '');
        $("#OrderAddForm").validationEngine();
        $(".saveOrder").click(function(){
            if(checkBfSaveOrder() == true){
                return true;
            }else{
                return false;
            }
        });

        $("#OrderAddForm").ajaxForm({
            dataType: 'json',
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveOrder").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            beforeSerialize: function($form, options) {
                $("#OrderOrderDate, .expired_date").datepicker("option", "dateFormat", "yy-mm-dd");
                $(".float, .interger").each(function(){
                    $(this).val($(this).val().replace(/,/g,""));
                });
            },
            error: function (result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                createSysAct('Order', 'Add', 2, result.responseText);
                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                $("#dialog").dialog({
                    title: '<?php echo DIALOG_INFORMATION; ?>',
                    resizable: false,
                    modal: true,
                    width: 'auto',
                    height: 'auto',
                    position:'center',
                    closeOnEscape: true,
                    open: function(event, ui){
                        $(".ui-dialog-buttonpane").show(); $(".ui-dialog-titlebar-close").show();
                    },
                    close: function(){
                        $(this).dialog({close: function(){}});
                        $(this).dialog("close");
                        backOrder();
                    },
                    buttons: {
                        '<?php echo ACTION_CLOSE; ?>': function() {
                            $("meta[http-equiv='refresh']").attr('content','0');
                            $(this).dialog("close");
                        }
                    }
                });
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                if(result.code == "1"){
                    codeDialogOrder();
                }else if(result.code == "2"){
                    errorSaveOrder();
                }else{
                    createSysAct('Order', 'Add', 1, '');
                    $("#dialog").html('<div class="buttons"><button type="submit" class="positive printInvoiceOrder" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span><?php echo ACTION_PRINT_SALES_ORDER; ?></span></button><button type="submit" class="positive printInvoiceOrderNoHead" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span><?php echo ACTION_PRINT_SALES_ORDER; ?> No Header</span></button></div>');
                    $(".printInvoiceOrder").click(function(){
                        $.ajax({
                            type: "POST",
                            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printInvoice/"+result.id,
                            beforeSend: function(){
                                $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                            },
                            success: function(printInvoiceResult){
                                w=window.open();
                                w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                                w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                                w.document.write(printInvoiceResult);
                                w.document.close();
                                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                            }
                        });
                    });
                    $(".printInvoiceOrderNoHead").click(function(){
                        $.ajax({
                            type: "POST",
                            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printInvoice/"+result.id+"/1",
                            beforeSend: function(){
                                $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                            },
                            success: function(printInvoiceResult){
                                w=window.open();
                                w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                                w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                                w.document.write(printInvoiceResult);
                                w.document.close();
                                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                            }
                        });
                    });
                    $("#dialog").dialog({
                        title: '<?php echo DIALOG_INFORMATION; ?>',
                        resizable: false,
                        modal: true,
                        width: 'auto',
                        height: 'auto',
                        position:'center',
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show();
                        },
                        close: function(){
                            $(this).dialog({close: function(){}});
                            $(this).dialog("close");
                            backOrder();
                        },
                        buttons: {
                            '<?php echo ACTION_CLOSE; ?>': function() {
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            }
        });
        
        $("#OrderCustomerName").focus(function(){
            checkOrderDate();
        });
        
        $(".searchCustomerOrder").click(function(){
            if(checkOrderDate() == true && $("#OrderCompanyId").val() != ''){
                searchAllCustomerOrder();
            }
        });

        $("#OrderCustomerName").autocomplete("<?php echo $this->base ."/".$this->params['controller']. "/searchCustomer"; ?>", {
            width: 410,
            max: 10,
            scroll: true,
            scrollHeight: 500,
            formatItem: function(data, i, n, value) {
                if(checkCompanyOrder(value.toString().split(".*")[3])){
                    return value.split(".*")[1] + " - " + value.split(".*")[2];
                }
            },
            formatResult: function(data, value) {
                if(checkCompanyOrder(value.toString().split(".*")[3])){
                    return value.split(".*")[1] + " - " + value.split(".*")[2];
                }
            }
        }).result(function(event, value){
            $("#OrderProduct").attr("disabled", false);
            $("#OrderCustomerId").val(value.toString().split(".*")[0]);
            $("#OrderCustomerName").val(value.toString().split(".*")[1]+" - "+value.toString().split(".*")[2]);
            $("#OrderCustomerName").attr("readonly","readonly");
            $(".searchCustomerOrder").hide();
            $(".deleteCustomerOrder").show();
            getCustomerContactOrder(value.toString().split(".*")[0], '');
            if(value.toString().split(".*")[4] != ''){
                // Check Price Type Customer
                customerPriceTypeOrder(value.toString().split(".*")[4], 0);
            }
        });

        $(".deleteCustomerOrder").click(function(){
            if($(".tblOrderList").find(".product_id").val() == undefined && $("#OrderQuotationNumber").val() == ""){
                removeCustomerOrder();
            }else{
                var question = "<?php echo MESSAGE_CONFIRM_REMOVE_CUSTOMER_ON_ORDER; ?>";
                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+question+'</p>');
                $("#dialog").dialog({
                    title: '<?php echo DIALOG_CONFIRMATION; ?>',
                    resizable: false,
                    modal: true,
                    width: 'auto',
                    height: 'auto',
                    position:'center',
                    closeOnEscape: true,
                    open: function(event, ui){
                        $(".ui-dialog-buttonpane").show(); 
                        $(".ui-dialog-titlebar-close").show();
                    },
                    buttons: {
                        '<?php echo ACTION_OK; ?>': function() {
                            removeCustomerOrder();
                            $(this).dialog("close");
                        },
                        '<?php echo ACTION_CANCEL; ?>': function() {
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });

        $('#OrderCustomerName').keypress(function(e){
            if(e.keyCode == 13){
                return false;
            }
        });
        
        $('#OrderOrderDate').datepicker({
            dateFormat:'dd/mm/yy',
            changeMonth: true,
            changeYear: true
        }).unbind("blur");
        
        $("#OrderOrderDate").datepicker("option", "minDate", "<?php echo $dataClosingDate[0]; ?>");
        $("#OrderOrderDate").datepicker("option", "maxDate", 0);
        
        $(".btnBackOrder").click(function(event){
            event.preventDefault();
            $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_DO_YOU_WANT_TO_BACK; ?></p>');
            $("#dialog").dialog({
                title: '<?php echo DIALOG_CONFIRMATION; ?>',
                resizable: false,
                modal: true,
                width: 'auto',
                height: 'auto',
                open: function(event, ui){
                    $(".ui-dialog-buttonpane").show();
                },
                buttons: {
                    '<?php echo ACTION_NO; ?>': function() {
                        $(this).dialog("close");
                    },
                    '<?php echo ACTION_YES; ?>': function() {
                        $(this).dialog("close");
                        backOrder();
                    }
                }
            });
        });
        
        $("#OrderCompanyId").change(function(){
            var obj   = $(this);
            var vatCal = $(this).find("option:selected").attr("vat-opt");
            if($(".tblOrderList").find(".product_id").val() == undefined){
                $.cookie('companyIdOrder', obj.val(), { expires: 7, path: "/" });
                $("#OrderVatCalculate").val(vatCal);
                $("#OrderBranchId").filterOptions('com', obj.val(), '');
                $("#OrderBranchId").change();
                resetFormOrder();
                checkVatCompanyOrder();
                changeInputCSSOrder();
            }else{
                var question = "<?php echo SALES_ORDER_CONFIRM_CHANGE_COMPANY; ?>";
                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+question+'</p>');
                $("#dialog").dialog({
                    title: '<?php echo DIALOG_CONFIRMATION; ?>',
                    resizable: false,
                    modal: true,
                    width: 'auto',
                    height: 'auto',
                    position:'center',
                    closeOnEscape: true,
                    open: function(event, ui){
                        $(".ui-dialog-buttonpane").show(); 
                        $(".ui-dialog-titlebar-close").show();
                    },
                    buttons: {
                        '<?php echo ACTION_OK; ?>': function() {
                            $.cookie('companyIdOrder', obj.val(), { expires: 7, path: "/" });
                            $("#OrderVatCalculate").val(vatCal);
                            $("#OrderBranchId").filterOptions('com', obj.val(), '');
                            $("#OrderBranchId").change();
                            $("#tblOrder").html('');
                            getTotalAmountOrder();
                            resetFormOrder();
                            checkVatCompanyOrder();
                            changeInputCSSOrder();
                            $(this).dialog("close");
                        },
                        '<?php echo ACTION_CANCEL; ?>': function() {
                            $("#OrderCompanyId").val($.cookie("companyIdOrder"));
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
        // Action Branch
        $("#OrderBranchId").change(function(){
            var obj = $(this);
            if($(".tblOrderList").find(".product_id").val() == undefined){
                $.cookie('branchIdOrder', obj.val(), { expires: 7, path: "/" });
                branchChangeOrder(obj);
            } else {
                var question = "<?php echo MESSAGE_CONFIRM_CHANGE_BRANCH; ?>";
                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+question+'</p>');
                $("#dialog").dialog({
                    title: '<?php echo DIALOG_CONFIRMATION; ?>',
                    resizable: false,
                    modal: true,
                    width: 'auto',
                    height: 'auto',
                    position:'center',
                    closeOnEscape: true,
                    open: function(event, ui){
                        $(".ui-dialog-buttonpane").show(); 
                        $(".ui-dialog-titlebar-close").show();
                    },
                    buttons: {
                        '<?php echo ACTION_OK; ?>': function() {
                            $.cookie('branchIdOrder', obj.val(), { expires: 7, path: "/" });
                            branchChangeOrder(obj);
                            $("#tblOrder").html('');
                            // Total Discount
                            $("#btnRemoveOrderTotalDiscount").click();
                            getTotalAmountOrder();
                            $(this).dialog("close");
                        },
                        '<?php echo ACTION_CANCEL; ?>': function() {
                            $("#OrderBranchId").val($.cookie("branchIdOrder"));
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
        // Company Action
        if($.cookie('companyIdOrder') != null || $("#OrderCompanyId").find("option:selected").val() != ''){
            if($.cookie('companyIdOrder') != null){
                $("#OrderCompanyId").val($.cookie('companyIdOrder'));
            }
            var vatCal = $("#OrderCompanyId").find("option:selected").attr("vat-opt");
            $("#OrderVatCalculate").val(vatCal);
            $("#OrderBranchId").filterOptions('com', $("#OrderCompanyId").val(), '');
            $("#OrderBranchId").change();
        }
        // Action Search Order
        $(".searchQuotationOrder").click(function(){
            if($("#OrderCompanyId").val() != "" && $("#OrderBranchId").val() != ""){
                searchQuotationOrder();
            }
        });
        
        // Action Delete Order
        $(".deleteQuotationOrder").click(function(){
            $("#OrderQuotationId").val('');
            $("#OrderQuotationNumber").val('');
            $("#OrderQuotationNumber").removeAttr('readonly');
            $(".searchQuotationOrder").show();
            $(".deleteQuotationOrder").hide();
        });
        
        // Reset Form Order
        resetFormOrder();
        // VAT Filter
        checkVatCompanyOrder();
        
    }); // End Document Ready
    
    function removeCustomerOrder(){
        $("#OrderCustomerId").val("");
        $("#OrderCustomerName").val("");
        $("#OrderCustomerName").removeAttr("readonly");
        $(".searchCustomerOrder").show();
        $(".deleteCustomerOrder").hide();
        $("#OrderCustomerContactId").html('<option value=""><?php echo INPUT_SELECT; ?></option>');
        $("#typeOfPriceOrder").filterOptions("comp", $("#OrderCompanyId").val(), "0");
        $(".deleteQuotationOrder").click();
    }
    
    function branchChangeOrder(obj){
        var mCode = obj.find("option:selected").attr("mcode");
        var currency = obj.find("option:selected").attr("currency");
        var currencySymbol = obj.find("option:selected").attr("symbol");
        $("#OrderOrderCode").val('<?php echo date("y"); ?>'+mCode);
        $("#OrderCurrencyCenterId").val(currency);
        $(".lblSymbolOrder").html(currencySymbol);
    }
    
    function changeLblVatCalOrder(){
        var vatCal = $("#OrderVatCalculate").val();
        $("#lblOrderVatSettingId").unbind("mouseover");
        if(vatCal != ''){
            if(vatCal == 1){
                $("#lblOrderVatSettingId").mouseover(function(){
                    Tip('<?php echo TABLE_VAT_BEFORE_DISCOUNT; ?>');
                });
            } else {
                $("#lblOrderVatSettingId").mouseover(function(){
                    Tip('<?php echo TABLE_VAT_AFTER_DISCOUNT; ?>');
                });
            }
        }
    }
    
    function checkVatSelectedOrder(){
        var vatPercent = replaceNum($("#OrderVatSettingId").find("option:selected").attr("rate"));
        $("#OrderVatPercent").val((vatPercent).toFixed(2));
    }
    
    function checkVatCompanyOrder(){
        // VAT Filter
        $("#OrderVatSettingId").filterOptions('com-id', $("#OrderCompanyId").val(), '');
    }
    
    function backOrder(){
        $("#OrderAddForm").validationEngine("hideAll");
        oCache.iCacheLower = -1;
        oTableOrder.fnDraw(false);
        var rightPanel = $(".btnBackOrder").parent().parent().parent().parent().parent();
        var leftPanel  = rightPanel.parent().find(".leftPanel");
        rightPanel.hide();rightPanel.html("");
        leftPanel.show("slide", { direction: "left" }, 500);
    }
    
    function searchQuotationOrder(){
        var companyId  = $("#OrderCompanyId").val();
        var branchId   = $("#OrderBranchId").val();
        var customerId = $("#OrderCustomerId").val();
        if(companyId != '' && branchId != ''){
            $.ajax({
                type:   "POST",
                url:    "<?php echo $this->base . '/' . $this->params['controller']; ?>/quotation/"+companyId+"/"+branchId+"/"+customerId,
                data:   "sale_id=0",
                beforeSend: function(){
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(msg){
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $("#dialog").html(msg).dialog({
                        title: '<?php echo MENU_QUOTATION_INFO; ?>',
                        resizable: false,
                        modal: true,
                        width: 900,
                        height: 600,
                        position:'center',
                        closeOnEscape: true,
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show(); $(".ui-dialog-titlebar-close").show();
                        },
                        buttons: {
                            '<?php echo ACTION_OK; ?>': function() {
                                if($("input[name='chkQuotation']:checked").val()){
                                    $("#OrderQuotationId").val($("input[name='chkQuotation']:checked").val());
                                    $("#OrderQuotationNumber").val($("input[name='chkQuotation']:checked").attr("rel"));
                                    $("#OrderQuotationNumber").attr('readonly', 'readonly');
                                    $(".searchQuotationOrder").hide();
                                    $(".deleteQuotationOrder").show();
                                    var quoteId = $("input[name='chkQuotation']:checked").val();
                                    // Customer
                                    var customerId     = $("input[name='chkQuotation']:checked").attr('cus-id');
                                    var customerCode   = $("input[name='chkQuotation']:checked").attr("cus-code");
                                    var customerNameEn = $("input[name='chkQuotation']:checked").attr("name-us");
                                    var customerCon    = $("input[name='chkQuotation']:checked").attr("cus-con");
                                    // Set Customer
                                    $("#OrderProduct").attr("disabled", false);
                                    $("#OrderCustomerId").val(customerId);
                                    $("#OrderCustomerName").val(customerCode+" - "+customerNameEn);
                                    $("#OrderCustomerName").attr('readonly','readonly');
                                    $(".searchCustomerOrder").hide();
                                    $(".deleteCustomerOrder").show();
                                    getCustomerContactOrder(customerId, customerCon);
                                    // Check Price Type
                                    var priceTypeList = $("input[name='chkQuotation']:checked").attr("ptype");
                                    var priceTypeSelected = $("input[name='chkQuotation']:checked").attr("ptype-id");
                                    customerPriceTypeOrder(priceTypeList, priceTypeSelected);
                                    // Discount
                                    var discountAmt = replaceNum($("input[name='chkQuotation']:checked").attr("dis"));
                                    var discountPer = replaceNum($("input[name='chkQuotation']:checked").attr("disp"));
                                    $("#OrderDiscount").val(discountAmt);
                                    $("#OrderDiscountPercent").val(discountPer);
                                    if(discountPer > 0){
                                        $("#quoteLabelDisPercent").html('('+discountPer+'%)');
                                    } else {
                                        $("#quoteLabelDisPercent").html('');
                                    }
                                    if(discountAmt > 0 || discountPer > 0){
                                        $("#btnRemoveOrderTotalDiscount").show();
                                    }
                                    // VAT 
                                    var vatSettingId = $("input[name='chkQuotation']:checked").attr("vid");
                                    var vatCalculate = $("input[name='chkQuotation']:checked").attr("vcal");
                                    var varPercent   = $("input[name='chkQuotation']:checked").attr("vper");
                                    $("#OrderVatCalculate").val(vatCalculate);
                                    $("#OrderVatSettingId").find("option[value='"+vatSettingId+"']").attr("selected", true);
                                    $("#OrderVatPercent").val(varPercent);
                                    changeLblVatCalOrder();
                                    // Get Product From Request Stock
                                    $.ajax({
                                        dataType: "json",
                                        type:   "POST",
                                        url:    "<?php echo $this->base . '/' . $this->params['controller']; ?>/getProductFromQuote/"+quoteId,
                                        beforeSend: function(){
                                            $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                                        },
                                        success: function(msg){
                                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                                            if(msg.error == 0){
                                                var tr = msg.result;
                                                // Empty Row List
                                                $("#tblOrder").html('');
                                                // Insert Row List
                                                $("#tblOrder").append(tr);
                                                // Event Key Table List
                                                checkEventOrder();
                                                sortNuTableOrder();
                                                // Calculate Total Amount
                                                getTotalAmountOrder();
                                            }
                                        }
                                    });
                                }
                                $(this).dialog("close");
                            },
                            '<?php echo ACTION_CANCEL; ?>': function() {
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            });
        }
    }
    
    function getTotalDiscountOrder(){
        $.ajax({
            type:   "POST",
            url:    "<?php echo $this->base . "/orders/invoiceDiscount"; ?>",
            beforeSend: function(){
                $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
            },
            success: function(msg){
                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                $("#dialog").html(msg).dialog({
                    title: '<?php echo GENERAL_DISCOUNT; ?>',
                    resizable: false,
                    modal: true,
                    width: 350,
                    height: 180,
                    position:'center',
                    closeOnEscape: true,
                    open: function(event, ui){
                        $(".ui-dialog-buttonpane").show(); 
                        $(".ui-dialog-titlebar-close").show();
                    },
                    buttons: {
                        '<?php echo ACTION_OK; ?>': function() {
                            var totalDisAmt     = replaceNum($("#inputInvoiceDisAmt").val());
                            var totalDisPercent = replaceNum($("#inputInvoiceDisPer").val());
                            $("#OrderDiscount").val(totalDisAmt);
                            $("#OrderDiscountPercent").val(totalDisPercent);
                            getTotalAmountOrder();
                            if(totalDisPercent > 0){
                                $("#quoteLabelDisPercent").html('('+totalDisPercent+'%)');
                            } else {
                                $("#quoteLabelDisPercent").html('');
                            }
                            if(totalDisAmt > 0 || totalDisPercent > 0){
                                $("#btnRemoveOrderTotalDiscount").show();
                            }
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
    }
    
    function getCustomerContactOrder(customerId, selected){
        $.ajax({
            type: "POST",
            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/getCustomerContact/"+customerId,
            beforeSend: function(){
                $("#OrderCustomerContactId").html('<option value=""><?php echo INPUT_SELECT; ?></option>');
            },
            success: function(msg){
                $("#OrderCustomerContactId").html(msg);
                $("#OrderCustomerContactId").find("option[value='"+selected+"']").attr("selected", true);
            }
        });
    }
    
    function checkCompanyOrder(companyId){
        var companyReturn = false;
        var companyPut    = companyId.split(",");
        var companySelect = $("#OrderCompanyId").val();
        if(companyPut.indexOf(companySelect) != -1){
            companyReturn = true;
        }
        return companyReturn;
    }
    
    function resetFormOrder(){
        // Customer
        $(".deleteCustomerOrder").click();
        // Total Discount
        $("#btnRemoveOrderTotalDiscount").click();
        // Quotation
        $(".deleteQuotationOrder").click();
        // Note
        $("#OrderNote").val('');
    }
    
    function checkOrderDate(){
        if($("#OrderOrderDate").val() == ''){
            $("#OrderOrderDate").focus();
            return false;
        }else{
            return true;
        }
    }

    function checkCustomerOrder(field, rules, i, options){
        if($("#OrderCustomerId").val() == "" || $("#OrderCustomerName").val() == ""){
            return "* Invalid Customer";
        }
    }
    
    function searchAllCustomerOrder(){
        var companyId = $("#OrderCompanyId").val();
        $.ajax({
            type:   "POST",
            url:    "<?php echo $this->base . '/' . $this->params['controller']; ?>/customer/"+companyId,
            beforeSend: function(){
                $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
            },
            success: function(msg){
                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                $("#dialog").html(msg).dialog({
                    title: '<?php echo MENU_CUSTOMER_MANAGEMENT_INFO; ?>',
                    resizable: false,
                    modal: true,
                    width: 850,
                    height: 500,
                    position:'center',
                    open: function(event, ui){
                        $(".ui-dialog-buttonpane").show();
                    },
                    buttons: {
                        '<?php echo ACTION_OK; ?>': function() {
                            if($("input[name='chkCMCustomer']:checked").val()){
                                $("#OrderProduct").attr("disabled", false);
                                $("#OrderCustomerId").val($("input[name='chkCMCustomer']:checked").val());
                                $("#OrderCustomerName").val($("input[name='chkCMCustomer']:checked").attr("code")+" - "+$("input[name='chkCMCustomer']:checked").attr("rel"));
                                $("#OrderCustomerName").attr('readonly','readonly');
                                $(".searchCustomerOrder").hide();
                                $(".deleteCustomerOrder").show();
                                getCustomerContactOrder($("input[name='chkCMCustomer']:checked").val(), '');
                                // Check Price Type
//                                var priceTypeList = $("input[name='chkCMCustomer']:checked").attr("ptype");
//                                if(priceTypeList != ''){
//                                    customerPriceTypeOrder(priceTypeList, 0);
//                                }
                            }
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
    }

    function loadOrderDetailOrder(){
        $.ajax({
            type: "POST",
            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/orderDetails/",
            beforeSend: function(){
                $(".orderDetailOrder").html('<img alt="Loading" src="<?php echo $this->webroot; ?>img/ajax-loader.gif" />');
                $("#tblOrder").html("");
                $("#OrderTotalAmount").val('0.00');
                $("#OrderDiscountPercent").val('0');
                $("#OrderDiscount").val('0');
                $("#OrderTotalAmountSummary").val('0.00');
            },
            success: function(msg){
                $(".orderDetailOrder").html(msg);
                $(".footerSaveOrder").show();
                <?php if($allowEditInvDis){ ?>
                // Action Total Discount Amount
                $("#OrderDiscount").click(function(){
                    if($("#OrderCompanyId").val() != ''){
                        getTotalDiscountOrder();
                    }
                });
                $("#btnRemoveOrderTotalDiscount").click(function(){
                    $("#OrderDiscount").val(0);
                    $("#OrderDiscountPercent").val(0);
                    $(this).hide();
                    $("#quoteLabelDisPercent").html('');
                    getTotalAmountOrder();
                });
                <?php } ?>
                // Action VAT Status
                $("#OrderVatSettingId").change(function(){
                    checkVatSelectedOrder();
                    getTotalAmountOrder();
                });
            }
        });
    }

    function clearOrderDetailOrder(){
        $(".orderDetailOrder").html("");
        $(".footerSaveOrder").hide();
        $(".deleteCustomerOrder").hide();
    }

    function checkBfSaveOrder(){
        var formName = "#OrderAddForm";
        var validateBack =$(formName).validationEngine("validate");
        if(!validateBack){
            return false;
        }else{
            if($(".tblOrderList").find(".product").val() == undefined){
                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>Please make an order first.</p>');
                $("#dialog").dialog({
                    title: '<?php echo DIALOG_INFORMATION; ?>',
                    resizable: false,
                    modal: true,
                    width: 'auto',
                    height: 'auto',
                    position:'center',
                    open: function(event, ui){
                        $(".ui-dialog-buttonpane").show();
                    },
                    buttons: {
                        '<?php echo ACTION_CLOSE; ?>': function() {
                            $(this).dialog("close");
                        }
                    }
                });
                return false;
            }else{
                return true;
            }
        }
    }
    
    function errorSaveOrder(){
        $("#dialog").html('<p style="color:red; font-size:14px;"><?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?></p>');
        $("#dialog").dialog({
            title: '<?php echo DIALOG_INFORMATION; ?>',
            resizable: false,
            modal: true,
            width: 'auto',
            height: 'auto',
            position:'center',
            open: function(event, ui){
                $(".ui-dialog-buttonpane").show();
            },
            close: function(){
                $(this).dialog({close: function(){}});
                $(this).dialog("close");
                var rightPanel=$("#SalesOrderAddForm").parent();
                var leftPanel=rightPanel.parent().find(".leftPanel");
                rightPanel.hide();rightPanel.html("");
                leftPanel.show("slide", { direction: "left" }, 500);
                oCache.iCacheLower = -1;
                oTableOrder.fnDraw(false);
            },
            buttons: {
                '<?php echo ACTION_CLOSE; ?>': function() {
                    $(this).dialog("close");
                }
            }
        });
    }
    
    function codeDialogOrder(){
        $("#dialog").html('<p style="color:red; font-size:14px;"><?php echo MESSAGE_CODE_ALREADY_EXISTS_IN_THE_SYSTEM; ?></p>');
        $("#dialog").dialog({
            title: '<?php echo DIALOG_INFORMATION; ?>',
            resizable: false,
            modal: true,
            width: 'auto',
            height: 'auto',
            position:'center',
            open: function(event, ui){
                $(".ui-dialog-buttonpane").show();
            },
            close: function(){
                $(this).dialog({close: function(){}});
                $(this).dialog("close");
                $(".saveOrder").removeAttr("disabled");
                $(".txtSaveOrder").html("<?php echo ACTION_SAVE; ?>");
            },
            buttons: {
                '<?php echo ACTION_CLOSE; ?>': function() {
                    $(this).dialog("close");
                    $(".saveOrder").removeAttr("disabled");
                    $(".txtSaveOrder").html("<?php echo ACTION_SAVE; ?>");
                }
            }
        });
    }
    
    function changeInputCSSOrder(){
        var cssStyle  = 'inputDisable';
        var cssRemove = 'inputEnable';
        var readonly  = true;
        var disabled  = true;
        $(".searchCustomerOrder").hide();
        $(".searchQuotationOrder").hide();
        $("#divSearchOrder").css("visibility", "hidden");
        if($("#OrderCompanyId").val() != ''){
            cssStyle  = 'inputEnable';
            cssRemove = 'inputDisable';
            readonly  = false;
            disabled  = false;
            if($("#OrderCustomerName").val() == ''){
                $(".searchCustomerOrder").show();
            }
            if($("#OrderQuotationNumber").val() == ''){
                $(".searchQuotationOrder").show();
            }
            $("#divSearchOrder").css("visibility", "visible");
        } else {
            $(".lblSymbolOrder").html('');
        }  
        // Label
        $("#OrderAddForm").find("label").removeAttr("class");
        $("#OrderAddForm").find("label").each(function(){
            var label = $(this).attr("for");
            if(label != 'OrderCompanyId'){
                $(this).addClass(cssStyle);
            }
        });
        // Input & Select
        $("#OrderAddForm").find("input").each(function(){
            $(this).removeClass(cssRemove);
            $(this).addClass(cssStyle);
        });
        $("#OrderAddForm").find("select").each(function(){
            var selectId = $(this).attr("id");
            if(selectId != 'OrderCompanyId'){
                $(this).removeClass(cssRemove);
                $(this).addClass(cssStyle);
                $(this).attr("disabled", disabled);
            }
        });
        $(".lblSymbolOrder").removeClass(cssRemove);
        $(".lblSymbolOrder").addClass(cssStyle);
        $(".lblSymbolOrderPercent").removeClass(cssRemove);
        $(".lblSymbolOrderPercent").addClass(cssStyle);
        // Input Readonly
        $("#OrderCustomerName").attr("readonly", readonly);
        $("#OrderNote").attr("readonly", readonly);
        $("#SearchProductPucOrder").attr("readonly", readonly);
        $("#SearchProductSkuOrder").attr("readonly", readonly);
        // Check Price Type With Company
        checkPriceTypeOrder();
        // Put label VAT Calculate
        changeLblVatCalOrder();
        // Check VAT Default
        getDefaultVatOrder();
    }
    
    function checkPriceTypeOrder(){
        // Price Type Filter
        $("#typeOfPriceOrder").filterOptions('comp', $("#OrderCompanyId").val(), '');
        if($("#OrderCompanyId").val() == ''){
            $("#typeOfPriceOrder").prepend('<option value="" comp=""><?php echo INPUT_SELECT;; ?></option>');
            $("#typeOfPriceOrder option[value='']").attr("selected", true);
        } else {
            $("#typeOfPriceOrder option[value='']").remove();
        }
    }
    
    function customerPriceTypeOrder(priceTypeList, priceTypeSelected){
        var priceTypeShow = '';
        var priceType = "";
        if(priceTypeList != ''){
            var selected  = 0;
            priceType = priceTypeList.toString().split(",");
            $("#typeOfPriceOrder option").each(function(){
                var hide = true;
                var id   = $(this).val();
                var objType = $(this);
                $.each(priceType,function(key, item){
                    var typeId = item.toString();
                    if(id == typeId){
                        hide = false;
                    }
                });
                if(hide == true){
                    objType.hide();
                } else {
                    if(selected == 0){
                        objType.attr("selected", true);
                        selected = 1;
                    }
                }
            });
        }
        
        if(priceTypeSelected != ""){
            priceTypeShow = priceTypeSelected;
        } else {
            priceTypeShow = priceType[0];
        }
        
        $("#typeOfPriceOrder option").removeAttr("selected");
        $("#typeOfPriceOrder option[value='"+priceTypeShow+"']").attr("selected", true);
        $.cookie("typePriceOrder", $("#typeOfPriceOrder").val(), {expires : 7,path    : '/'});
        changePriceTypeOrder();
    }
    
    function getDefaultVatOrder(){
        var vatDefault = $("#OrderCompanyId option:selected").attr("vat-d");
        $("#OrderVatSettingId option[value='"+vatDefault+"']").attr("selected", true);
        checkVatSelectedOrder();
    }
</script>
<?php echo $this->Form->create('Order', array('inputDefaults' => array('div' => false, 'label' => false))); ?>
<input type="hidden" value="" name="data[Order][vat_calculate]" id="OrderVatCalculate" />
<input type="hidden" value="" name="data[Order][currency_center_id]" id="OrderCurrencyCenterId" />
<div style="float: right; width: 165px; text-align: right; cursor: pointer;" id="btnHideShowHeaderOrder">
    [<span>Hide</span> Header Information <img alt="" align="absmiddle" style="width: 16px; height: 16px;" src="<?php echo $this->webroot . 'img/button/arrow-up.png'; ?>" />]
</div>
<div style="clear: both;"></div>
<table cellpadding="0" cellspacing="0" style="width: 100%;" id="orderHeaderForm">
    <tr>
        <td style="width: 50%;">
            <fieldset id="OrderInformation">
                <legend><?php __(MENU_ORDER_INFO); ?></legend>
                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                    <tr>
                        <td style="vertical-align: top;">
                            <table cellpadding="0" style="width: 100%;">
                                <tr>
                                    <td style="width: 36%;"><label for="OrderCompanyId"><?php echo TABLE_COMPANY; ?> <span class="red">*</span> </label></td>
                                    <td style="width: 32%;"><label for="OrderBranchId"><?php echo MENU_BRANCH; ?> <span class="red">*</span></label></td>
                                    <td style="width: 32%;"><label for="OrderOrderCode"><?php echo TABLE_SALES_ORDER_NUMBER; ?> <span class="red">*</span></label></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="inputContainer" style="width:100%">
                                            <select name="data[Order][company_id]" id="OrderCompanyId" class="validate[required]" style="width: 80%;">
                                                <?php
                                                if(count($companies) != 1){
                                                ?>
                                                <option vat-d="" value="" vat-opt=""><?php echo INPUT_SELECT; ?></option>
                                                <?php
                                                }
                                                foreach($companies AS $company){
                                                    $sqlVATDefault = mysql_query("SELECT vat_modules.vat_setting_id FROM vat_modules INNER JOIN vat_settings ON vat_settings.company_id = ".$company['Company']['id']." AND vat_settings.is_active = 1 AND vat_settings.id = vat_modules.vat_setting_id WHERE vat_modules.is_active = 1 AND vat_modules.apply_to = 69 GROUP BY vat_modules.vat_setting_id LIMIT 1");
                                                    $rowVATDefault = mysql_fetch_array($sqlVATDefault);
                                                ?>
                                                <option vat-d="<?php echo $rowVATDefault[0]; ?>" value="<?php echo $company['Company']['id']; ?>" vat-opt="<?php echo $company['Company']['vat_calculate']; ?>"><?php echo $company['Company']['name']; ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inputContainer" style="width:100%">
                                            <select name="data[Order][branch_id]" id="OrderBranchId" class="validate[required]" style="width: 90%;">
                                                <?php
                                                if(count($branches) != 1){
                                                ?>
                                                <option value="" com="" mcode="" currency="" symbol=""><?php echo INPUT_SELECT; ?></option>
                                                <?php
                                                }
                                                foreach($branches AS $branch){
                                                ?>
                                                <option value="<?php echo $branch['Branch']['id']; ?>" com="<?php echo $branch['Branch']['company_id']; ?>" mcode="<?php echo $branch['ModuleCodeBranch']['so_code']; ?>" currency="<?php echo $branch['Branch']['currency_center_id']; ?>" symbol="<?php echo $branch['CurrencyCenter']['symbol']; ?>"><?php echo $branch['Branch']['name']; ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inputContainer" style="width:100%">
                                            <?php echo $this->Form->text('order_code', array('class' => 'validate[required]', 'style' => 'width:85%', 'readonly' => true)); ?>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <?php if(!$allowEditTerm){ ?>
                        <td rowspan="2" style="width: 50%;">
                            <table cellpadding="0" style="width: 100%;">
                                <tr>
                                    <td style="width: 33%;"><label for="OrderQuotationNumber"><?php echo TABLE_QUOTATION_NUMBER; ?></label></td>
                                    <td><label for="OrderNote"><?php echo TABLE_NOTE; ?></label></td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top;">
                                        <div class="inputContainer" style="width:100%">
                                            <?php 
                                            echo $this->Form->hidden('quotation_id'); 
                                            echo $this->Form->text('quotation_number', array('style' => 'width:75%')); ?>
                                            <img alt="Search" align="absmiddle" style="cursor: pointer; width: 22px; height: 22px;" class="searchQuotationOrder" onmouseover="Tip('<?php echo GENERAL_SEARCH; ?>')" src="<?php echo $this->webroot . 'img/button/search.png'; ?>" />
                                            <img alt="Delete" align="absmiddle" style="display: none; cursor: pointer;" class="deleteQuotationOrder" onmouseover="Tip('<?php echo ACTION_DELETE; ?>')" src="<?php echo $this->webroot . 'img/button/delete.png'; ?>" />
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inputContainer" style="width:100%">
                                            <?php echo $this->Form->input('note', array('label' => false, 'style' => 'width:95%; height: 65px;')); ?>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <?php
                        }
                        ?>
                    </tr> 
                    <tr>
                        <td style="vertical-align: top;">
                            <table cellpadding="0" style="width: 100%;">
                                <tr>
                                    <td style="width: 36%"><label for="OrderCustomerName"><?php echo PATIENT_NAME; ?> <span class="red">*</span></label></td>
                                    <td style="width: 32%"><label for="OrderCustomerContactId"><?php echo TABLE_CONTACT_NAME; ?></label></td>
                                    <td style="width: 32%"><label for="OrderOrderDate"><?php echo TABLE_SALE_ORDER_DATE; ?></label></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="inputContainer" style="width:100%">
                                            <?php echo $this->Form->hidden('customer_id'); ?>
                                            <?php echo $this->Form->text('customer_name', array('label' => false, 'class' => 'validate[required,funcCall[checkCustomerOrder]]', 'style' => 'width:75%')); ?>
                                            <img alt="Search" align="absmiddle" style="cursor: pointer; width: 22px; height: 22px;" class="searchCustomerOrder" onmouseover="Tip('<?php echo GENERAL_SEARCH; ?>')" src="<?php echo $this->webroot . 'img/button/search.png'; ?>" />
                                            <img alt="Delete" align="absmiddle" style="cursor: pointer; display: none;" class="deleteCustomerOrder" onmouseover="Tip('<?php echo ACTION_DELETE; ?>')" src="<?php echo $this->webroot . 'img/button/delete.png'; ?>" />
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inputContainer" style="width:100%">
                                            <select name="data[Order][customer_contact_id]" id="OrderCustomerContactId" style="width: 90%;">
                                                <option value=""><?php echo INPUT_SELECT; ?></option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inputContainer" style="width:100%">
                                            <?php echo $this->Form->text('order_date', array('value' => date("d/m/Y"),'readonly' => 'readonly', 'class' => 'validate[required]', 'style' => 'width:85%')); ?>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <?php if($allowEditTerm){ ?>
                    <tr>
                        <td style="vertical-align: top;">
                            <table cellpadding="0" style="width: 100%;">
                                <tr>
                                    <td style="width: 36%; display: none;"><label for="OrderQuotationNumber"><?php echo TABLE_QUOTATION_NUMBER; ?></label></td>
                                    <td><label for="OrderNote"><?php echo TABLE_NOTE; ?></label></td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top; display: none;">
                                        <div class="inputContainer" style="width:100%">
                                            <?php echo $this->Form->hidden('quotation_id'); ?>
                                            <?php echo $this->Form->text('quotation_number', array('style' => 'width:75%')); ?>
                                            <img alt="Search" align="absmiddle" style="cursor: pointer; width: 22px; height: 22px;" class="searchQuotationOrder" onmouseover="Tip('<?php echo GENERAL_SEARCH; ?>')" src="<?php echo $this->webroot . 'img/button/search.png'; ?>" />
                                            <img alt="Delete" align="absmiddle" style="display: none; cursor: pointer;" class="deleteQuotationOrder" onmouseover="Tip('<?php echo ACTION_DELETE; ?>')" src="<?php echo $this->webroot . 'img/button/delete.png'; ?>" />
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inputContainer" style="width:100%">
                                            <?php echo $this->Form->input('note', array('label' => false, 'style' => 'width:95%; height: 40px;')); ?>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                </table>
            </fieldset>
        </td>
        <td style="width: 50%; vertical-align: top; <?php if(!$allowEditTerm){ ?>display: none;<?php } ?>">
            <fieldset id="OrderInformation">
                <legend><?php __(MENU_TERM_CONDITION); ?></legend>
                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                    <tr>
                        <td style=" vertical-align: top; height: 174px;">
                            <table cellpadding="0" cellspacing="0" style="width: 100%;">
                                <?php
                                $termForms = array();
                                $sqlTermApply = mysql_query("SELECT * FROM term_condition_applies WHERE is_active = 1 AND module_type_id = 69 GROUP BY module_type_id, term_condition_type_id ORDER BY id");
                                $i=0;
                                $j=0;
                                if(mysql_num_rows($sqlTermApply)){
                                    while($rowTermApply = mysql_fetch_array($sqlTermApply)){
                                        $termForms[$j][$i] = $rowTermApply['term_condition_type_id']."||".$rowTermApply['term_condition_default_id'];
                                        $i++;
                                        if($i==3){
                                            $i=0;
                                            $j++;
                                        }
                                    }
                                }
                                foreach($termForms AS $termForm){
                                    @$termData1 = explode("||", $termForm[0]);
                                    @$termData2 = explode("||", $termForm[1]);
                                    @$termData3 = explode("||", $termForm[2]);
                                    $termTypeId1 = '';
                                    $termTypeId2 = '';
                                    $termTypeId3 = '';
                                    $title1 = '';
                                    $title2 = '';
                                    $title3 = '';
                                    $input1 = '';
                                    $input2 = '';
                                    $input3 = '';
                                    if(!empty($termForm[0])){
                                        $termTypeId1 = $termData1[0];
                                        $title1  = getTermType($termData1[0]);
                                        $input1  = getTermOption($termData1[0], $termData1[1]);
                                    }
                                    if(!empty($termForm[1])){
                                        $termTypeId2 = $termData2[0];
                                        $title2  = getTermType($termData2[0]);
                                        $input2  = getTermOption($termData2[0], $termData2[1]);
                                    }
                                    if(!empty($termForm[2])){
                                        $termTypeId3 = $termData3[0];
                                        $title3  = getTermType($termData3[0]);
                                        $input3  = getTermOption($termData3[0], $termData3[1]);
                                    }
                                ?>
                                <tr>
                                    <td style="width: 33%"><label for="TermCondition<?php echo $termTypeId1; ?>"><?php echo $title1; ?></label></td>
                                    <td style="width: 34%"><label for="TermCondition<?php echo $termTypeId2; ?>"><?php echo $title2; ?></label></td>
                                    <td style="width: 33%"><label for="TermCondition<?php echo $termTypeId3; ?>"><?php echo $title3; ?></label></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="inputContainer" style="width:100%">
                                            <?php
                                            if(!empty($termTypeId1)){
                                            ?>
                                            <input type="hidden" name="term_condition_type_id[]" value="<?php echo $termTypeId1; ?>" />
                                            <select name="term_condition_id[]" id="TermCondition<?php echo $termTypeId1; ?>" style="width: 90%;">
                                                <option value=""><?php echo INPUT_SELECT; ?></option>
                                                <?php echo $input1; ?>
                                            </select>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inputContainer" style="width:100%">
                                            <?php
                                            if(!empty($termTypeId2)){
                                            ?>
                                            <input type="hidden" name="term_condition_type_id[]" value="<?php echo $termTypeId2; ?>" />
                                            <select name="term_condition_id[]" id="TermCondition<?php echo $termTypeId2; ?>" style="width: 90%;">
                                                <option value=""><?php echo INPUT_SELECT; ?></option>
                                                <?php echo $input2; ?>
                                            </select>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inputContainer" style="width:100%">
                                            <?php
                                            if(!empty($termTypeId3)){
                                            ?>
                                            <input type="hidden" name="term_condition_type_id[]" value="<?php echo $termTypeId3; ?>" />
                                            <select name="term_condition_id[]" id="TermCondition<?php echo $termTypeId3; ?>" style="width: 90%;">
                                                <option value=""><?php echo INPUT_SELECT; ?></option>
                                                <?php echo $input3; ?>
                                            </select>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                }
                                ?>
                            </table>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </td>
    </tr>
</table>
<div class="orderDetailOrder" style=" margin-top: 5px; text-align: center;"></div>
<div class="footerSaveOrder" style="">
    <div style="float: left; width: 21%;">
        <div class="buttons">
            <a href="#" class="positive btnBackOrder">
                <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
                <?php echo ACTION_BACK; ?>
            </a>
        </div>
        <div class="buttons">
            <button type="submit" class="positive saveOrder" >
                <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
                <span class="txtSaveOrder"><?php echo ACTION_SAVE; ?></span>
            </button>
        </div>
        <div style="clear: both;"></div>
    </div>
    <div style="float: right; width:78%;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 10%; text-align: right;"><label for="OrderTotalAmount"><?php echo TABLE_SUB_TOTAL; ?>:</label></td>
                <td style="width: 13%;">
                    <div class="inputContainer" style="width: 100%">
                        <?php echo $this->Form->text('total_amount', array('readonly' => true, 'class' => 'float validate[required]', 'style' => 'width: 80%; font-size:12px; font-weight: bold', 'value'=>'0')); ?> <span class="lblSymbolOrder"></span>
                    </div>
                </td>
                <td style="width: 8%; text-align: right;"><label for="OrderDiscount"><?php echo GENERAL_DISCOUNT; ?>:</label></td>
                <td style="width: 20%;">
                    <div class="inputContainer" style="width:100%">
                        <?php echo $this->Form->hidden('discount_percent', array('class' => 'float', 'value'=>'0')); ?>
                        <?php echo $this->Form->text('discount', array('style' => 'width: 55%; height:15px; font-size:12px; font-weight: bold', 'class' => 'float', 'value'=>'0', 'readonly' => true)); ?> <span class="lblSymbolOrder"></span>
                        <span id="quoteLabelDisPercent"></span>
                        <?php if($allowEditInvDis){ ?><img alt="Remove" src="<?php echo $this->webroot . 'img/button/cross.png'; ?>" id="btnRemoveOrderTotalDiscount" align="absmiddle" style="cursor: pointer; display: none;" onmouseover="Tip('Remove Discount')" /><?php } ?>
                    </div>
                </td>
                <td style="width: 19%; text-align: right;">
                    <label for="OrderVatSettingId" id="lblOrderVatSettingId"><?php echo TABLE_VAT; ?> <span class="red">*</span>:</label>
                    <select id="OrderVatSettingId" name="data[Order][vat_setting_id]" style="width: 75%;" class="validate[required]">
                        <option com-id="" value="" rate="0.00"><?php echo INPUT_SELECT; ?></option>
                        <?php
                        // VAT
                        $sqlVat = mysql_query("SELECT id, name, vat_percent, company_id FROM vat_settings WHERE is_active = 1 AND type = 1 AND company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id'].");");
                        while($rowVat = mysql_fetch_array($sqlVat)){
                        ?>
                        <option com-id="<?php echo $rowVat['company_id']; ?>" value="<?php echo $rowVat['id']; ?>" rate="<?php echo $rowVat['vat_percent']; ?>"><?php echo $rowVat['name']; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </td>
                <td style="width: 9%;">
                    <div class="inputContainer" style="width: 100%">
                        <input type="hidden" value="0" name="data[Order][total_vat]" id="OrderTotalVat" class="float" />
                        <?php echo $this->Form->text('vat_percent', array('readonly' => true, 'class' => 'float validate[required]', 'style' => 'width: 50%; font-size:12px; font-weight: bold', 'value' => 0)); ?> <span class="lblSymbolOrderPercent">(%)</span>
                    </div>
                </td>
                <td style="width: 6%; text-align: right;"><label for="OrderTotalAmountSummary"><?php echo TABLE_TOTAL; ?>:</label></td>
                <td style="width: 14%;">
                    <div class="inputContainer" style="width: 100%">
                        <?php echo $this->Form->text('total_amount_summary', array('readonly' => true, 'class' => 'float validate[required]', 'style' => 'width: 80%; font-size:12px; font-weight: bold', 'value'=>'0')); ?> <span class="lblSymbolOrder"></span>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div style="clear: both;"></div>
</div>
<?php echo $this->Form->end(); ?>