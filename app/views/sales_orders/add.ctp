<?php
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include('includes/function.php');
$this->element('check_access');
echo $this->element('prevent_multiple_submit');
$queryClosingDate = mysql_query("SELECT DATE_FORMAT(date,'%d/%m/%Y') FROM account_closing_dates ORDER BY id DESC LIMIT 1");
$dataClosingDate  = mysql_fetch_array($queryClosingDate);
$sqlSettingUomDeatil = mysql_query("SELECT uom_detail_option, calculate_cogs FROM setting_options");
$rowSettingUomDetail = mysql_fetch_array($sqlSettingUomDeatil);
// Authentication
$allowEditTerm     = checkAccess($user['User']['id'], $this->params['controller'], 'editTermsCondition');
$allowEditInvDis   = checkAccess($user['User']['id'], $this->params['controller'], 'invoiceDiscount');
$allowAddCustomer  = checkAccess($user['User']['id'], 'customers', 'quickAdd');
?>
<script type="text/javascript">
    var fieldRequireInvoice = ['SalesOrderLocationGroupId'];
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        $('#dialog').dialog('destroy');
        clearOrderDetailSo();
        // Hide Branch
        $("#SalesOrderBranchId").filterOptions('com', '0', '');
        $("#SalesOrderLocationGroupId").chosen({ width: 200});
        $("#SalesOrderAddForm").validationEngine();
        $(".saveSales").click(function(){
            if(checkBfSaveSo() == true){
                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_DO_YOU_WANT_TO_SAVE; ?></p>');
                $("#dialog").dialog({
                    title: '<?php echo DIALOG_CONFIRMATION; ?>',
                    resizable: false,
                    modal: true,
                    width: 300,
                    height: 'auto',
                    position:'center',
                    closeOnEscape: false,
                    open: function(event, ui){
                        $(".ui-dialog-buttonpane").show(); 
                        $(".ui-dialog-titlebar-close").hide();
                    },
                    buttons: {
//                        '<?php echo ACTION_SAVE; ?>': function() {
//                            // Set Save Status
//                            $("#saveExitSales").val(0);
//                            // Action Click Save
//                            $("#SalesOrderAddForm").submit();
//                            $(this).dialog("close");
//                        },
                        '<?php echo ACTION_SAVE_EXIT; ?>': function() {
                            // Set Save Status
                            $("#saveExitSales").val(1);
                            // Action Click Save
                            $("#SalesOrderAddForm").submit();
                            $(this).dialog("close");
                        },
                        '<?php echo ACTION_CANCEL; ?>': function() {
                            $(this).dialog("close");
                        }
                    }
                });
                return false;
            }else{
                return false;
            }
        });

        $(".float").autoNumeric({mDec: 3});

        $("#SalesOrderAddForm").ajaxForm({
            dataType: 'json',
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveSales").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            beforeSerialize: function($form, options) {
                if(checkRequireField(fieldRequireInvoice) == false){
                    alertSelectRequireField();
                    $(".saveSales").removeAttr('disabled');
                    return false;
                }
                var access  = true;
                var confirm = false;
                if(timeBarcodeSO == 2){
                    access = false;
                }else{
                    var totalAmount  = replaceNum($("#SalesOrderTotalAmount").val());
                    var cusLimitBal  = replaceNum($("#limitBalance").val());
                    var cusLimitInv  = replaceNum($("#limitInvoice").val());
                    var cusBalUsed   = replaceNum($("#totalBalanceUsed").val());
                    var cusInvUsed   = replaceNum($("#totalInvoiceUsed").val());
                    var cusTotalBal  = (totalAmount + cusBalUsed);
                    if((cusInvUsed > cusLimitInv && cusLimitInv > 0) || (cusTotalBal > cusLimitBal && cusLimitBal > 0)){
                        confirm = true;
                    }
                }
                if(confirm == true && $("#SalesOrderIsApprove").val() == 1){
                    // Set Approve Confirm
                    confirmConditionCustomer();
                    access  = false;
                }
                // Check Access Condition
                if(access == true){
                    $("#SalesOrderOrderDate").datepicker("option", "dateFormat", "yy-mm-dd");
                    $(".float").each(function(){
                        $(this).val($(this).val().replace(/,/g,""));
                    });
                    $(".floatQty").each(function(){
                        $(this).val($(this).val().replace(/,/g,""));
                    });
                }else{
                    $(".saveSales").removeAttr('disabled');
                    return false;
                }
            },
            error: function (result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                createSysAct('Sales Invoice', 'Add', 2, result.responseText);
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
                        var saveStatus = $("#saveExitSales").val();
                        if(saveStatus == 1){
                            backSalesOrder();
                        } else {
                            saveContiuneSales();
                        }
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
                    codeDialogSO();
                }else if(result.code == "2"){
                    errorSaveSO();
                }else if(result.code == "3"){
                    errorOutStock();
                    var listOutStock = result.listOutStock.split("-");
                    var obj = "";
                    $(".tblSOList").each(function(){
                        if($(this).find("input[name='product_id[]']").val() != ""){
                            obj = $(this);
                            $.each(listOutStock, function(i, val){
                                if(val != ""){
                                    if(obj.find("input[name='product_id[]']").val() == val.split("|")[0]){
                                        obj.css("background","#fc8b8b");
                                        obj.attr('data-stock',"Total qty can sale is "+val.split("|")[1]+" "+val.split("|")[2]+"");
                                        obj.find("input[name='inv_qtySO[]']").val(val.split("|")[1]);
                                    }
                                }
                            });
                        }
                    });
                    $(".tblSOList").mouseover(function(){
                        var text = $(this).attr('data-stock');
                        Tip(text);
                    });
                }else{
                    createSysAct('Sales Invoice', 'Add', 1, '');
                    $("#dialog").html('<div class="buttons"><button type="submit" class="positive printInvoice" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="txtPrintInvoiceNoHead"><?php echo ACTION_INVOICE; ?></span></button></div>');
                    $(".printInvoice").click(function(){
                        $.ajax({
                            type: "POST",
                            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printInvoice/"+result.so_id,
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
                        closeOnEscape: true,
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show(); $(".ui-dialog-titlebar-close").show();
                        },
                        close: function(){
                            $(this).dialog({close: function(){}});
                            $(this).dialog("close");
                            var saveStatus = $("#saveExitSales").val();
                            if(saveStatus == 1){
                                backSalesOrder();
                            } else {
                                saveContiuneSales();
                            }
                        },
                        buttons: {
                            '<?php echo ACTION_CLOSE; ?>': function() {
                                $("meta[http-equiv='refresh']").attr('content','0');
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            }
        });
        
        $(".searchCustomerSales").click(function(){
            if(checkOrderDate() == true && $("#SalesOrderCompanyId").val() != '' && $("#SalesOrderBranchId").val() != ''){
                searchAllCustomerSales();
            }
        });

        $(".deleteCustomerSales").click(function(){
            if($(".tblSOList").find(".product_id").val() == undefined){
                removeCustomerSales();
            } else {
                var question = "<?php echo MESSAGE_CONFIRM_REMOVE_CUSTOMER_ON_SALES; ?>";
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
                            removeCustomerSales();
                            $(this).dialog("close");
                        },
                        '<?php echo ACTION_CANCEL; ?>': function() {
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
        
        $("#SalesOrderCustomerName").focus(function(){
            checkOrderDate();
        });
        
        $('#SalesOrderCustomerName').keypress(function(e){
            if(e.keyCode == 13){
                return false;
            }
        });
        
        $("#SalesOrderCustomerName").autocomplete("<?php echo $this->base . "/sales_orders/searchCustomer"; ?>", {
            width: 410,
            max: 10,
            scroll: true,
            scrollHeight: 500,
            formatItem: function(data, i, n, value) {
                return value.split(".*")[2] + " - " + value.split(".*")[1];
            },
            formatResult: function(data, value) {
                return value.split(".*")[2] + " - " + value.split(".*")[1];
            }
        }).result(function(event, value){
            var strName = value.toString().split(".*")[1];
            var customerId = value.toString().split(".*")[0];
            var customerCode = value.toString().split(".*")[2];
            var queueId = value.toString().split(".*")[3];
            var pateintGroupId = value.toString().split(".*")[4];
            var queueDoctorId = value.toString().split(".*")[5];
            var paymentTermId = "";
            $("#orderQueueId").val(queueId);
            $("#orderQueueDoctorId").val(queueDoctorId);
            $("#SalesOrderPatientGroupId").val(pateintGroupId);
            $("#SalesOrderProduct").attr("disabled", false);
            $("#SalesOrderCustomerId").val(customerId);
            $("#SalesOrderCustomerName").val(customerCode+" - "+strName);
            $("#SalesOrderCustomerName").attr("readonly","readonly");
            $("#SalesOrderPaymentTermId").find("option[value='"+paymentTermId+"']").attr("selected", true);
            $(".searchCustomerSales").hide();
            $(".deleteCustomerSales").show();
        });
        
        // Action Location Group
        if($.cookie('SalesOrderLocationGroupId')!=null){
            $("#SalesOrderLocationGroupId").val($.cookie('SalesOrderLocationGroupId'));
        }
        
        $("#SalesOrderLocationGroupId").change(function(){
            var obj = $(this);
            if($(".tblSOList").find(".product_id").val() == undefined){
                $.cookie("SalesOrderLocationGroupId", obj.val(), {expires : 7,path    : '/'});
            }else{
                var question = "<?php echo MESSAGE_CONFRIM_CHANGE_LOCATION_GROUP; ?>";
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
                        $(".ui-dialog-buttonpane").show(); $(".ui-dialog-titlebar-close").show();
                    },
                    buttons: {
                        '<?php echo ACTION_OK; ?>': function() {
                            $.cookie("SalesOrderLocationGroupId", obj.val(), {expires : 7,path    : '/'});
                            $("#tblSO").html('');
                            // Total Discount
                            $("#btnRemoveSalesTotalDiscount").click();
                            calcTotalAmountSales();
                            $(this).dialog("close");
                        },
                        '<?php echo ACTION_CANCEL; ?>': function() {
                            $("#SalesOrderLocationGroupId").val($.cookie("SalesOrderLocationGroupId"));
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
        
        
        
        
        
        // Action Search Order
        $(".searchOrderSales").click(function(){
            var locationGroup = $("#SalesOrderLocationGroupId").val();
            if(locationGroup != ""){
                searchOrderSales();
            }
        });
        
        // Action Delete Order
        $(".deleteOrderSales").click(function(){
            $("#SalesOrderOrderId").val('');
            $("#SalesOrderOrderNumber").val('');
             resetFormSales();
            
//            $("#SalesOrderCustomerId").val("");
//            $("#SalesOrderCustomerName").val("");
            $("#SalesOrderOrderNumber").removeAttr('readonly');
            $(".searchOrderSales").show();
            $(".deleteOrderSales").hide();
        });
        
        
        
        
        
        // Action Order Date
        $('#SalesOrderOrderDate').datepicker({
            dateFormat:'dd/mm/yy',
            changeMonth: true,
            changeYear: true
        }).unbind("blur");
        $("#SalesOrderOrderDate").datepicker("option", "minDate", "<?php echo $dataClosingDate[0]; ?>");
        $("#SalesOrderOrderDate").datepicker("option", "maxDate", 0);
        
//        if($.cookie('SalesOrderOrderDate')!=null){
//            $("#SalesOrderOrderDate").val($.cookie('SalesOrderOrderDate'));
//        }
        
        $("#SalesOrderOrderDate").change(function(){
            if($(".tblSOList").find(".product_id").val() == undefined){
                $.cookie("SalesOrderOrderDate", $("#SalesOrderOrderDate").val(), {
                    expires : 7,
                    path    : '/'
                });
            }else{
                var question = "<?php echo MESSAGE_CONFIRM_CHANGE_DATE; ?>";
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
                        $(".ui-dialog-buttonpane").show(); $(".ui-dialog-titlebar-close").show();
                    },
                    buttons: {
                        '<?php echo ACTION_OK; ?>': function() {
                            $.cookie("SalesOrderOrderDate", $("#SalesOrderOrderDate").val(), {expires : 7,path    : '/'});
                            $("#tblSO").html('');
                            // Total Discount
                            $("#btnRemoveSalesTotalDiscount").click();
                            calcTotalAmountSales();
                            $(this).dialog("close");
                        },
                        '<?php echo ACTION_CANCEL; ?>': function() {
                            $("#SalesOrderOrderDate").val($.cookie("SalesOrderOrderDate"));
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
        
        $(".btnBackSalesOrder").click(function(event){
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
                        backSalesOrder();
                    }
                }
            });
        });
        
        $("#SalesOrderCompanyId").change(function(){
            var obj    = $(this);
            var vatCal = $(this).find("option:selected").attr("vat-opt");
            if($(".tblSOList").find(".product_id").val() == undefined){
                $.cookie('companyIdSales', obj.val(), { expires: 7, path: "/" });
                $("#SalesOrderVatCalculate").val(vatCal);
                $("#SalesOrderBranchId").filterOptions('com', obj.val(), '');
                $("#SalesOrderBranchId").change();
                resetFormSales();
                checkVatCompanySales();
                checkChartAccountSales();
                changeInputCSSSales();
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
                            $.cookie('companyIdSales', obj.val(), { expires: 7, path: "/" });
                            $("#SalesOrderVatCalculate").val(vatCal);
                            $("#SalesOrderBranchId").filterOptions('com', obj.val(), '');
                            $("#SalesOrderBranchId").change();
                            resetFormSales();
                            checkVatCompanySales();
                            checkChartAccountSales();
                            $("#tblSO").html('');
                            // Total Discount
                            $("#btnRemoveSalesTotalDiscount").click();
                            calcTotalAmountSales();
                            $(this).dialog("close");
                        },
                        '<?php echo ACTION_CANCEL; ?>': function() {
                            $("#SalesOrderCompanyId").val($.cookie("companyIdSales"));
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
        // Action Branch
        $("#SalesOrderBranchId").change(function(){
            var obj = $(this);
            if($(".tblSOList").find(".product_id").val() == undefined){
                $.cookie('branchIdSalesOrder', obj.val(), { expires: 7, path: "/" });
                branchChangeSalesOrder(obj);
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
                            $.cookie('branchIdSalesOrder', obj.val(), { expires: 7, path: "/" });
                            branchChangeSalesOrder(obj);
                            $("#tblSO").html('');
                            // Total Discount
                            $("#btnRemoveSalesTotalDiscount").click();
                            calcTotalAmountSales();
                            $(this).dialog("close");
                        },
                        '<?php echo ACTION_CANCEL; ?>': function() {
                            $("#SalesOrderBranchId").val($.cookie("branchIdSalesOrder"));
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
        // Company Action
        if($.cookie('companyIdSales') != null || $("#SalesOrderCompanyId").find("option:selected").val() != ''){
            if($.cookie('companyIdSales') != null){
                $("#SalesOrderCompanyId").val($.cookie('companyIdSales'));
            }
            var vatCal = $("#SalesOrderCompanyId").find("option:selected").attr("vat-opt");
            $("#SalesOrderVatCalculate").val(vatCal);
            $("#SalesOrderBranchId").filterOptions('com', $("#SalesOrderCompanyId").val(), '');
            $("#SalesOrderBranchId").change();
        }
        // Button Change Info & Term
        <?php
        if($allowEditTerm){
        ?>
        $("#btnSalesInvoiceTermCon").click(function(){
            $("#saleInvoiceInformation").hide();
            $("#salesTermCondition").show();
            $("#btnSalesInvoiceTermCon, #btnSalesInvoiceInfo").removeAttr('style');
            $("#btnSalesInvoiceTermCon").attr("style", "padding: 3px; background: #CCCCCC; font-weight: bold;");
            $("#btnSalesInvoiceInfo").attr("style", "padding: 3px; background: #CCCCCC;");
        });
        
        $("#btnSalesInvoiceInfo").click(function(){
            $("#saleInvoiceInformation").show();
            $("#salesTermCondition").hide();
            $("#btnSalesInvoiceTermCon, #btnSalesInvoiceInfo").removeAttr('style');
            $("#btnSalesInvoiceTermCon").attr("style", "padding: 3px; background: #CCCCCC;");
            $("#btnSalesInvoiceInfo").attr("style", "padding: 3px; background: #CCCCCC; font-weight: bold;");
        });
        <?php
        }
        if($allowAddCustomer){
        ?>
        $("#addCustomerSales").click(function(){
            $.ajax({
                type:   "GET",
                url:    "<?php echo $this->base . "/customers/quickAdd/"; ?>",
                beforeSend: function(){
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(msg){
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $("#dialog3").html(msg);
                    $("#dialog3").dialog({
                        title: '<?php echo MENU_CUSTOMER_MANAGEMENT_ADD; ?>',
                        resizable: false,
                        modal: true,
                        width: '550',
                        height: '600',
                        position:'center',
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show();
                            $(".ui-dialog-titlebar-close").show();
                        },
                        buttons: {
                            '<?php echo ACTION_CLOSE; ?>': function() {
                                $(this).dialog("close");
                            },
                            '<?php echo ACTION_SAVE; ?>': function() {
                                var formName = "#CustomerQuickAddForm";
                                var validateBack =$(formName).validationEngine("validate");
                                if(!validateBack){
                                    return false;
                                }else{
                                    if($("#CustomerCgroupId").val() == null || $("#CustomerCgroupId").val() == ''){
                                        alertSelectRequireField();
                                    } else {
                                        $(this).dialog("close");
                                        $.ajax({
                                            dataType: 'json',
                                            type: "POST",
                                            url: "<?php echo $this->base; ?>/customers/quickAdd",
                                            data: $("#CustomerQuickAddForm").serialize(),
                                            beforeSend: function(){
                                                $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                                            },
                                            error: function (result) {
                                                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                                createSysAct('Sales Invoice', 'Quick Add Customer', 2, result);
                                                $("#dialog1").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                                                $("#dialog1").dialog({
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
                                                    buttons: {
                                                        '<?php echo ACTION_CLOSE; ?>': function() {
                                                            $(this).dialog("close");
                                                        }
                                                    }
                                                });
                                            },
                                            success: function(result){
                                                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                                                createSysAct('Sales Invoice', 'Quick Add Customer', 1, '');
                                                var msg = '';
                                                if(result.error == 0){
                                                    msg = '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>';
                                                    // Set Customer
                                                    $("#SalesOrderCustomerId").val(result.id);
                                                    $("#SalesOrderCustomerName").val(result.name);
                                                    $("#SalesOrderCustomerName").attr("readonly","readonly");
                                                    $("#SalesOrderPaymentTermId").find("option[value='"+result.term+"']").attr("selected", true);
                                                    $(".searchCustomerSales").hide();
                                                    $(".deleteCustomerSales").show();
                                                    if(result.price != ''){
                                                        // Check Price Type Customer
                                                        customerPriceTypeSales(result.price, 0);
                                                    }
                                                    $.ajax({
                                                        dataType: 'json',
                                                        type: "POST",
                                                        url: "<?php echo $this->base . '/sales_orders'; ?>/customerCondition/"+result.id+"/0",
                                                        beforeSend: function(){
                                                        },
                                                        success: function(msg){
                                                            if(msg.error == 0){
                                                                // Condition
                                                                var limitBalance = msg.limit_balance;
                                                                var limitInvoice = msg.limit_invoice;
                                                                var totalBalanceUsed = msg.balance_used;
                                                                var totalInvoiceUsed = msg.invoice_used;
                                                                // Set Condition
                                                                $("#limitBalance").val(limitBalance);
                                                                $("#limitInvoice").val(limitInvoice);
                                                                $("#totalBalanceUsed").val(totalBalanceUsed);
                                                                $("#totalInvoiceUsed").val(totalInvoiceUsed);
                                                            }else{
                                                                $(".deleteCustomerSales").click();
                                                            }
                                                        }
                                                    });
                                                } else  if (result.error == 1){
                                                    msg = '<?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?>'; 
                                                } else  if (result.error == 2){
                                                    msg = '<?php echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM; ?>';
                                                }
                                                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+msg+'</p>');
                                                $("#dialog").dialog({
                                                    title: '<?php echo DIALOG_INFORMATION; ?>',
                                                    resizable: false,
                                                    modal: true,
                                                    width: 'auto',
                                                    height: 'auto',
                                                    position: 'center',
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
                                    }
                                }  
                            }
                        }
                    });
                }
            });
        });
        <?php
        }
        ?>
        // Reset Form
        resetFormSales();
        // VAT Filter
        checkVatCompanySales();
        // A/R Filter
        checkChartAccountSales();
        // Load Detail
        loadOrderDetailSO();
        // Protect Browser Auto Complete QTY Input
        loadAutoCompleteOff();
    });
    
    
    //searchOrderSales
    function searchOrderSales(){
        var companyId  = $("#SalesOrderCompanyId").val();
        var branchId   = $("#SalesOrderBranchId").val();
        var customerId = $("#SalesOrderCustomerId").val();
        if(companyId != '' && branchId != ''){
            $.ajax({
                type:   "POST",
                url:    "<?php echo $this->base . '/' . $this->params['controller']; ?>/order/"+companyId+"/"+branchId+"/"+customerId,
                data:   "sale_id=0",
                beforeSend: function(){
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(msg){
                    timeBarcodeSO == 1;
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $("#dialog").html(msg).dialog({
                        title: '<?php echo MENU_ORDER_INFO; ?>',
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
                                if($("input[name='chkOrder']:checked").val()){
                                    $("#SalesOrderOrderId").val($("input[name='chkOrder']:checked").val());
                                    $("#SalesOrderOrderNumber").val($("input[name='chkOrder']:checked").attr("rel"));
                                    $("#SalesOrderOrderNumber").attr('readonly', 'readonly');
                                    $(".searchOrderSales").hide();
                                    $(".deleteOrderSales").show();
                                    var orderId = $("input[name='chkOrder']:checked").val();
                                    var locationGroup = $("#SalesOrderLocationGroupId").val();
                                    var salesId    = 0;
                                    var discount   = $("input[name='chkOrder']:checked").attr("dis");
                                    var disPercent = $("input[name='chkOrder']:checked").attr("disp");
                                    // Insert Customer
                                    // Condition
                                    var limitBalance = $("input[name='chkOrder']:checked").attr("limit-balance");
                                    var limitInvoice = $("input[name='chkOrder']:checked").attr("limit-invoice");
                                    var totalBalanceUsed = $("input[name='chkOrder']:checked").attr("bal-used");
                                    var totalInvoiceUsed = $("input[name='chkOrder']:checked").attr("inv-used");
                                    
                                    // Queue Id
                                    var queueId = $("input[name='chkOrder']:checked").attr("queue-id");
                                    var queueDoctorId = $("input[name='chkOrder']:checked").attr("queue-doctor-id");
                                    // Customer
                                    var customerId     = $("input[name='chkOrder']:checked").attr('cus-id');
                                    var customerCode   = $("input[name='chkOrder']:checked").attr("cus-code");
                                    var customerNameEn = $("input[name='chkOrder']:checked").attr("cus-name");
                                    //var paymentTermId  = $("input[name='chkOrder']:checked").attr("term-id");
                                    //var customerCont   = $("input[name='chkOrder']:checked").attr("cus-con");
                                    // Set Customer
                                    $("#SalesOrderProduct").attr("disabled", false);                                    
                                    // Queue Id                                    
                                    $("#orderQueueId").val(queueId);
                                    $("#orderQueueDoctorId").val(queueDoctorId);                                    
                                    $("#SalesOrderCustomerId").val(customerId);
                                    $("#SalesOrderCustomerName").val(customerCode+" - "+customerNameEn);
                                    $("#SalesOrderCustomerName").attr('readonly','readonly');
                                    //$("#SalesOrderPaymentTermId").find("option[value='"+paymentTermId+"']").attr("selected", true);
                                    $(".searchCustomerSales").hide();
                                    $(".deleteCustomerSales").show();
                                    //getCustomerContactSales(customerId, customerCont);
                                 
                                    // Set Condition
                                    $("#limitBalance").val(limitBalance);
                                    $("#limitInvoice").val(limitInvoice);
                                    $("#totalBalanceUsed").val(totalBalanceUsed);
                                    $("#totalInvoiceUsed").val(totalInvoiceUsed);

                                 
                                    // Get Product From Request Stock
                                    $.ajax({
                                        dataType: "json",
                                        type:   "POST",
                                        url:    "<?php echo $this->base . '/' . $this->params['controller']; ?>/getProductFromOrder/"+orderId+"/"+locationGroup+"/"+salesId,
                                        beforeSend: function(){
                                            $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                                        },
                                        success: function(msg){
                                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                                            if(msg.error == 0){
                                                var tr = msg.result;
                                                // Empty Row List
                                                $("#tblSO").html('');
                                                // Insert Row List
                                                $("#tblSO").append(tr);
                                                // Event Key Table List
                                                checkEventSO();
                                                // Calculate Total Amount
                                                calcTotalAmountSales();
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
    
    
    function removeCustomerSales(){
        $("#orderQueueId").val("");
        $("#orderQueueDoctorId").val("");        
        $("#SalesOrderPatientGroupId").val("");
        $("#SalesOrderCustomerId").val("");
        $("#SalesOrderCustomerName").val("");
        $("#SalesOrderCustomerName").removeAttr("readonly");
        $(".searchCustomerSales").show();
        $(".deleteCustomerSales").hide();
        $("#typeOfPriceSO").filterOptions("comp", $("#SalesOrderCompanyId").val(), "0");
        $("#SalesOrderPaymentTermId").find("option[value='']").attr("selected", true);
    } 
    
    function branchChangeSalesOrder(obj){
        var mCode  = obj.find("option:selected").attr("mcode");
        var currency = obj.find("option:selected").attr("currency");
        var currencySymbol = obj.find("option:selected").attr("symbol");
        $("#SalesOrderSoCode").val("<?php echo date("y"); ?>"+mCode);
        $("#SalesOrderCurrencyCenterId").val(currency);
        $(".lblSymbolSales").html(currencySymbol);
    }
    
    function getTotalDiscountSales(){
        $.ajax({
            type:   "POST",
            url:    "<?php echo $this->base . "/sales_orders/invoiceDiscount"; ?>",
            beforeSend: function(){
                $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
            },
            success: function(msg){
                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                $("#dialog").html(msg).dialog({
                    title: '<?php echo GENERAL_DISCOUNT; ?>',
                    resizable: false,
                    modal: true,
                    width: 450,
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
                            $("#SalesOrderDiscountUs").val(totalDisAmt);
                            $("#SalesOrderDiscountPercent").val(totalDisPercent);
                            calcTotalAmountSales();
                            if(totalDisPercent > 0){
                                $("#salesLabelDisPercent").html('('+totalDisPercent+'%)');
                            } else {
                                $("#salesLabelDisPercent").html('');
                            }
                            if(totalDisAmt > 0 || totalDisPercent > 0){
                                $("#btnRemoveSalesTotalDiscount").show();
                            }
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
    }
    
    function changeLblVatCalSales(){
        var vatCal = $("#SalesOrderVatCalculate").val();
        $("#lblSalesOrderVatSettingId").unbind("mouseover");
        if(vatCal != ''){
            if(vatCal == 1){
                $("#lblSalesOrderVatSettingId").mouseover(function(){
                    Tip('<?php echo TABLE_VAT_BEFORE_DISCOUNT; ?>');
                });
            } else {
                $("#lblSalesOrderVatSettingId").mouseover(function(){
                    Tip('<?php echo TABLE_VAT_AFTER_DISCOUNT; ?>');
                });
            }
        }
    }
    
    function checkVatSelectedSales(){
        var vatPercent = replaceNum($("#SalesOrderVatSettingId").find("option:selected").attr("rate"));
        var vatAccId   = replaceNum($("#SalesOrderVatSettingId").find("option:selected").attr("acc"));
        $("#SalesOrderVatPercent").val((vatPercent).toFixed(2));
        $("#SalesOrderVatChartAccountId").val(vatAccId);
    }
    
    function checkVatCompanySales(){
        // VAT Filter
        $("#SalesOrderVatSettingId").filterOptions('com-id', $("#SalesOrderCompanyId").val(), '');
    }
    
    function backSalesOrder(){
        oCache.iCacheLower = -1;
        oTableSalesOrder.fnDraw(false);
        $("#SalesOrderAddForm").validationEngine("hideAll");
        var rightPanel = $("#SalesOrderAddForm").parent();
        var leftPanel  = rightPanel.parent().find(".leftPanel");
        rightPanel.hide();rightPanel.html("");
        leftPanel.show("slide", { direction: "left" }, 500);
    }
    
    function saveContiuneSales(){
        $("#SalesOrderAddForm").validationEngine("hideAll");
        var rightPanel = $("#SalesOrderAddForm").parent();
        rightPanel.html("<?php echo ACTION_LOADING; ?>");
        rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/add/");
    }
    
    function checkCompanySales(companyId){
        var companyReturn = false;
        var companyPut    = companyId.split(",");
        var companySelect = $("#SalesOrderCompanyId").val();
        if(companyPut.indexOf(companySelect) != -1){
            companyReturn = true;
        }
        return companyReturn;
    }
    
    function resetFormSales(){
        // Customer
        $(".deleteCustomerSales").click();
    }
    
    function checkChartAccountSales(){
        // A/R Filter
        $("#SalesOrderChartAccountId").filterOptions('company_id', $("#SalesOrderCompanyId").val(), '');
        
        if($("#SalesOrderCompanyId").val() != ''){
            <?php
            if(!empty($arAccountId)){
            ?>
            $("#SalesOrderChartAccountId option[value='<?php echo $arAccountId; ?>']").attr('selected', true);
            <?php
            }
            ?>
        }
    }
    
    function confirmConditionCustomer(){
        var question = "<?php echo MESSAGE_CUSTOMER_INVOICE_HAVE_LIMIT_CONDITON." ".MESSAGE_CONFRIM_CUSTOMER_CONTINUE_SALE; ?>";
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
                '<?php echo ACTION_OK; ?>': function() {
                    // Set Approve Confirm
                    $("#SalesOrderIsApprove").val(0);
                    // Action Click Save
                    $("#SalesOrderAddForm").submit();
                    $(this).dialog("close");
                },
                '<?php echo ACTION_CANCEL; ?>': function() {
                    $(this).dialog("close");
                }
            }
        });
    }
    
    function checkOrderDate(){
        if($("#SalesOrderOrderDate").val() == ''){
            $("#SalesOrderOrderDate").focus();
            return false;
        }else{
            return true;
        }
    }

    function checkCustomerSales(field, rules, i, options){
        if($("#SalesOrderCustomerId").val() == "" || $("#SalesOrderCustomerName").val() == ""){
            return "* Invalid Customer";
        }
    }
    
    function checkCollectorSO(field, rules, i, options){
        if($("#SalesOrderCollectorId").val() == "" || $("#SalesOrderCollectorName").val() == ""){
            return "* Invalid Collector";
        }
    }

    function loadOrderDetailSO(){
        if($("#SalesOrderOrderDate").val() != ''){
            $.ajax({
                type: "POST",
                url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/orderDetails/",
                beforeSend: function(){
                    $(".orderDetailSales").html('<img alt="Loading" src="<?php echo $this->webroot; ?>img/ajax-loader.gif" />');
                    $("#tblSO").html('');
                    $("#SalesOrderTotalAmount").val("0.000");
                    $("#SalesOrderDiscountPercent").val("0");
                    $("#SalesOrderSubTotalAmount").val("0.000");
                    $("#SalesOrderStatus").removeAttr("disabled");
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(msg){
                    $(".orderDetailSales").html(msg);
                    $(".footerFormSales").show();
                    <?php if($allowEditInvDis){ ?>
                    // Action Total Discount Amount
                    $("#SalesOrderDiscountUs").click(function(){
                        getTotalDiscountSales();
                    });
                    <?php } ?>

                    $("#btnRemoveSalesTotalDiscount").click(function(){
                        $("#SalesOrderDiscountUs").val(0);
                        $("#SalesOrderDiscountPercent").val(0);
                        $(this).hide();
                        $("#salesLabelDisPercent").html('');
                        calcTotalAmountSales();
                    });
                    
                    // Action VAT Status
                    $("#SalesOrderVatSettingId").change(function(){
                        checkVatSelectedSales();
                        calcTotalAmountSales();
                    });
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                }
            });
        } else {
            clearOrderDetailSo();
        }
    }

    function clearOrderDetailSo(){
        $(".orderDetailSales").html("");
        $(".footerFormSales").hide();
    }
    
    function searchAllCustomerSales(){
        var companyId = $("#SalesOrderCompanyId").val();
        if(companyId != '' && $("#SalesOrderBranchId").val() != ''){
            $.ajax({
                type:   "POST",
                url:    "<?php echo $this->base . '/' . $this->params['controller']; ?>/customer/"+companyId,
                data:   "sale_id=0",
                beforeSend: function(){
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(msg){
                    timeBarcodeSO == 1;
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $("#dialog").html(msg).dialog({
                        title: '<?php echo MENU_CUSTOMER_MANAGEMENT_INFO; ?>',
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
                                if($("input[name='chkCustomer']:checked").val()){                                    
                                    // Customer
                                    var customerId     = $("input[name='chkCustomer']:checked").val();
                                    var customerCode   = $("input[name='chkCustomer']:checked").attr("rel");
                                    var customerNameEn = $("input[name='chkCustomer']:checked").attr("name-us");
                                    var queueId = $("input[name='chkCustomer']:checked").attr("queue-id");
                                    var queueDoctorId = $("input[name='chkCustomer']:checked").attr("queue-doctor-id");
                                    var patientGroupId = $("input[name='chkCustomer']:checked").attr("patient-group-id");
                                    $("#orderQueueId").val(queueId);
                                    $("#orderQueueDoctorId").val(queueDoctorId);
                                    $("#SalesOrderProduct").attr("disabled", false);
                                    $("#SalesOrderCustomerId").val(customerId);
                                    $("#SalesOrderPatientGroupId").val(patientGroupId);
                                    $("#SalesOrderCustomerName").val(customerCode+" - "+customerNameEn);
                                    $("#SalesOrderCustomerName").attr('readonly','readonly');
                                    $(".searchCustomerSales").hide();
                                    $(".deleteCustomerSales").show();                                    
                                }
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            });
        }
    }
    
    function checkBfSaveSo(){
        $("#SalesOrderCustomerName").removeClass("validate[required]");
        $("#SalesOrderCustomerName").addClass("validate[required,funcCall[checkCustomerSales]]");
        var formName = "#SalesOrderAddForm";
        var validateBack =$(formName).validationEngine("validate");
        if(!validateBack){
            $("#SalesOrderCustomerName").removeClass("validate[required,funcCall[checkCustomerSales]]");
            $("#SalesOrderCustomerName").addClass("validate[required]");
            return false;
        }else{
            if($(".tblSOList").find(".product").val() == undefined){
                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>Please make an order first.</p>');
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
                    buttons: {
                        '<?php echo ACTION_CLOSE; ?>': function() {
                            $(this).dialog("close");
                        }
                    }
                });
                $("#SalesOrderCustomerName").removeClass("validate[required,funcCall[checkCustomerSales]]");
                $("#SalesOrderCustomerName").addClass("validate[required]");
                return false;
            }else{
                return true;
            }
        }
    }
    
    function codeDialogSO(){
        $("#dialog").html('<p style="color:red; font-size:14px;"><?php echo MESSAGE_CODE_ALREADY_EXISTS_IN_THE_SYSTEM; ?></p>');
        $("#dialog").dialog({
            title: '<?php echo DIALOG_INFORMATION; ?>',
            resizable: false,
            modal: true,
            width: 'auto',
            height: 'auto',
            position:'center',
            closeOnEscape: false,
            open: function(event, ui){
                $(".ui-dialog-buttonpane").show(); $(".ui-dialog-titlebar-close").hide();
            },
            buttons: {
                '<?php echo ACTION_CLOSE; ?>': function() {
                    $(this).dialog("close");
                    $(".saveSales").removeAttr("disabled");
                    $(".txtSaveSales").html("<?php echo ACTION_SAVE; ?>");
                }
            }
        });
    }
    
    function errorSaveSO(){
        $("#dialog").html('<p style="color:red; font-size:14px;"><?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?></p>');
        $("#dialog").dialog({
            title: '<?php echo DIALOG_INFORMATION; ?>',
            resizable: false,
            modal: true,
            width: 'auto',
            height: 'auto',
            position:'center',
            closeOnEscape: false,
            open: function(event, ui){
                $(".ui-dialog-buttonpane").show(); $(".ui-dialog-titlebar-close").hide();
            },
            buttons: {
                '<?php echo ACTION_CLOSE; ?>': function() {
                    $(this).dialog("close");
                    var rightPanel=$("#SalesOrderAddForm").parent();
                    var leftPanel=rightPanel.parent().find(".leftPanel");
                    rightPanel.hide();rightPanel.html("");
                    leftPanel.show("slide", { direction: "left" }, 500);
                    oCache.iCacheLower = -1;
                    oTableSalesOrder.fnDraw(false);
                }
            }
        });
    }
    
    function errorOutStock(){
        $("#dialog").html('<p style="color:red; font-size:14px;"><?php echo MESSAGE_SOME_PRODUCT_OUT_OF_STOCK; ?></p>');
        $("#dialog").dialog({
            title: '<?php echo DIALOG_INFORMATION; ?>',
            resizable: false,
            modal: true,
            width: 'auto',
            height: 'auto',
            position:'center',
            closeOnEscape: false,
            open: function(event, ui){
                $(".ui-dialog-buttonpane").show(); $(".ui-dialog-titlebar-close").hide();
            },
            buttons: {
                '<?php echo ACTION_CLOSE; ?>': function() {
                    $(this).dialog("close");
                    $(".saveSales").removeAttr("disabled");
                    $(".txtSaveSales").html("<?php echo ACTION_SAVE; ?>");
                }
            }
        });
    }
    
    function changeInputCSSSales(){
        var cssStyle  = 'inputDisable';
        var cssRemove = 'inputEnable';
        var readonly  = true;
        var disabled  = true;
        // Button Search
        $(".searchCustomerSales").hide();
        // Div for Search Product, Service, Misc
        $("#divSearchSales").css("visibility", "hidden");
        if($("#SalesOrderCompanyId").val() != ''){
            cssStyle  = 'inputEnable';
            cssRemove = 'inputDisable';
            readonly  = false;
            disabled  = false;
            // Button Search
            if($("#SalesOrderCustomerName").val() == ''){
                $(".searchCustomerSales").show();
            }
            // Div for Search Product, Service, Misc
            $("#divSearchSales").css("visibility", "visible");
        } else {
            $(".lblSymbolSales").html('');
        } 
        // Label
        $("#SalesOrderAddForm").find("label").removeAttr("class");
        $("#SalesOrderAddForm").find("label").each(function(){
            var label = $(this).attr("for");
            if(label != 'SalesOrderCompanyId'){
                $(this).addClass(cssStyle);
            }
        });
        // Input & Select
        $("#SalesOrderAddForm").find("input").each(function(){
            $(this).removeClass(cssRemove);
            $(this).addClass(cssStyle);
        });
        $("#SalesOrderAddForm").find("select").each(function(){
            var selectId = $(this).attr("id");
            if(selectId != 'SalesOrderCompanyId'){
                $(this).removeClass(cssRemove);
                $(this).addClass(cssStyle);
                $(this).attr("disabled", disabled);
            }
        });
        $(".lblSymbolSales").removeClass(cssRemove);
        $(".lblSymbolSales").addClass(cssStyle);
        $(".lblSymbolSalesPercent").removeClass(cssRemove);
        $(".lblSymbolSalesPercent").addClass(cssStyle);
        // Input Readonly
        $("#SalesOrderCustomerName").attr("readonly", readonly);
        $("#SalesOrderCustomerPoNumber").attr("readonly", readonly);
        $("#SalesOrderMemo").attr("readonly", readonly);
        $("#searchProductUpcSales").attr("readonly", readonly);
        $("#searchProductSkuSales").attr("readonly", readonly);
        // Check Price Type With Company
        checkPriceTypeSales();
        // Put label VAT Calculate
        changeLblVatCalSales();
        // Check VAT Default
        getDefaultVatSales();
    }
    
    function checkPriceTypeSales(){
        // Price Type Filter
        $("#typeOfPriceSO").filterOptions('comp', $("#SalesOrderCompanyId").val(), '');
        if($("#SalesOrderCompanyId").val() == ''){
            $("#typeOfPriceSO").prepend('<option value="" comp=""><?php echo INPUT_SELECT; ?></option>');
            $("#typeOfPriceSO option[value='']").attr("selected", true);
        } else {
            $("#typeOfPriceSO option[value='']").remove();
        }
    }
    
    function customerPriceTypeSales(priceTypeList, priceTypeSelected){
        var priceTypeShow = '';
        var priceType = "";
        if(priceTypeList != ''){
            var selected  = 0;
            priceType = priceTypeList.toString().split(",");
            $("#typeOfPriceSO option").each(function(){
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
        
        $("#typeOfPriceSO option").removeAttr("selected");
        $("#typeOfPriceSO option[value='"+priceTypeShow+"']").attr("selected", true);
        $.cookie("typePriceSO", $("#typeOfPriceSO").val(), {expires : 7, path : '/'});
        changePriceTypeSO();
    }
    
    function getDefaultVatSales(){
        var vatDefault = $("#SalesOrderCompanyId option:selected").attr("vat-d");
        $("#SalesOrderVatSettingId option[value='"+vatDefault+"']").attr("selected", true);
        checkVatSelectedSales();
    }
</script>
<?php echo $this->Form->create('SalesOrder', array('inputDefaults' => array('div' => false, 'label' => false))); ?>
<input type="hidden" value="<?php echo $rowSettingUomDetail[1]; ?>" name="data[calculate_cogs]" />
<input type="hidden" name="data[total_deposit]" id="SalesOrderTotalDeposit" />
<input type="hidden" value="1" id="SalesOrderIsApprove" name="data[SalesOrder][is_approve]" />
<input type="hidden" value="0" id="SalesOrderCurrencyCenterId" name="data[SalesOrder][currency_center_id]" />
<input type="hidden" id="limitBalance" />
<input type="hidden" id="limitInvoice" />
<input type="hidden" id="totalBalanceUsed" />
<input type="hidden" id="totalInvoiceUsed" />
<input type="hidden" id="saveExitSales" value="1" />
<input type="hidden" value="" name="data[SalesOrder][vat_calculate]" id="SalesOrderVatCalculate" />
<input type="hidden" id="orderQueueId" value="" name="data[SalesOrder][queue_id]"/>
<input type="hidden" id="orderQueueDoctorId" value="" name="data[SalesOrder][queue_doctor_id]"/>
<div style="float: right; width: 165px; text-align: right; cursor: pointer;" id="btnHideShowHeaderSalesOrder">
    [<span>Hide</span> Header Information <img alt="" align="absmiddle" style="width: 16px; height: 16px;" src="<?php echo $this->webroot . 'img/button/arrow-up.png'; ?>" />]
</div>
<div style="clear: both;"></div>
<fieldset id="SOTop">
    <legend><a href="#" id="btnSalesInvoiceInfo" style="padding: 3px; background: #CCCCCC; font-weight: bold;"><?php __(MENU_SALES_ORDER_MANAGEMENT_INFO); ?></a> <?php if($allowEditTerm){ ?>| <a href="#" id="btnSalesInvoiceTermCon" style="padding: 3px; background: #CCCCCC;"><?php __(TABLE_TERM_AND_CONDITION); ?></a><?php } ?></legend>
    <table cellpadding="0" cellspacing="0" style="width: 100%;" id="saleInvoiceInformation">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <table cellpadding="0" style="width: 100%">
                    <tr>
                        <td style="width: 34%"><label for="SalesOrderOrderDate"><?php echo TABLE_INVOICE_DATE; ?> <span class="red">*</span></label></td>
                        <td style="width: 33%"><label for="SalesOrderSoCode"><?php echo TABLE_INVOICE_CODE; ?> <span class="red">*</span></label></td>
                        <td style="width: 33%"><label for="SalesOrderCustomerPoNumber"><?php echo TABLE_CUSTOMER_PO; ?></label></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="inputContainer" style="width:100%">
                                <?php echo $this->Form->text('order_date', array('value' => date("d/m/Y"),'class' => 'validate[required]', 'readonly' => 'readonly', 'style' => 'width:85%')); ?>
                            </div>
                        </td>
                        <td>
                            <div class="inputContainer" style="width:100%">
                                <?php echo $this->Form->text('so_code', array('class' => 'validate[required]', 'style' => 'width:85%', 'readonly' => true)); ?>
                            </div>
                        </td>
                        <td>
                            <div class="inputContainer" style="width:100%">
                                <?php echo $this->Form->text('customer_po_number', array('style' => 'width:85%')); ?>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 17%; vertical-align: top;">
                <table cellpadding="0" style="width: 100%">
                    <tr>
                        <td><?php if(count($companies) > 1){ ?><label for="SalesOrderCompanyId"><?php echo TABLE_COMPANY; ?> <span class="red">*</span></label><?php } ?></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="inputContainer" style="width:100%; <?php if(count($companies) == 1){ ?>display: none;<?php } ?>">
                                <select name="data[SalesOrder][company_id]" id="SalesOrderCompanyId" class="validate[required]" style="width: 90%;">
                                    <?php
                                    if(count($companies) != 1){
                                    ?>
                                    <option vat-d="" value="" vat-opt=""><?php echo INPUT_SELECT; ?></option>
                                    <?php
                                    }
                                    foreach($companies AS $company){
                                        $sqlVATDefault = mysql_query("SELECT vat_modules.vat_setting_id FROM vat_modules INNER JOIN vat_settings ON vat_settings.company_id = ".$company['Company']['id']." AND vat_settings.is_active = 1 AND vat_settings.id = vat_modules.vat_setting_id WHERE vat_modules.is_active = 1 AND vat_modules.apply_to = 25 GROUP BY vat_modules.vat_setting_id LIMIT 1");
                                        $rowVATDefault = mysql_fetch_array($sqlVATDefault);
                                    ?>
                                    <option vat-d="<?php echo $rowVATDefault[0]; ?>" value="<?php echo $company['Company']['id']; ?>" vat-opt="<?php echo $company['Company']['vat_calculate']; ?>"><?php echo $company['Company']['name']; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 16%; vertical-align: top;">
                <table cellpadding="0" style="width: 100%">
                    <tr>
                        <td><?php if(count($branches) > 1){ ?><label for="SalesOrderBranchId"><?php echo MENU_BRANCH; ?> <span class="red">*</span></label><?php } ?></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="inputContainer" style="width:100%; <?php if(count($branches) == 1){ ?>display: none;<?php } ?>">
                                <select name="data[SalesOrder][branch_id]" id="SalesOrderBranchId" class="validate[required]" style="width: 90%;">
                                    <?php
                                    if(count($branches) != 1){
                                    ?>
                                    <option value="" com="" mcode="" currency="" symbol=""><?php echo INPUT_SELECT; ?></option>
                                    <?php
                                    }
                                    foreach($branches AS $branch){
                                    ?>
                                    <option value="<?php echo $branch['Branch']['id']; ?>" com="<?php echo $branch['Branch']['company_id']; ?>" mcode="<?php echo $branch['ModuleCodeBranch']['inv_code']; ?>" currency="<?php echo $branch['Branch']['currency_center_id']; ?>" symbol="<?php echo $branch['CurrencyCenter']['symbol']; ?>"><?php echo $branch['Branch']['name']; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 16%; vertical-align: top;" rowspan="2">
                <table cellpadding="0" style="width: 100%">
                    <tr>
                        <td><label for="SalesOrderMemo"><?php echo TABLE_MEMO; ?></label></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="inputContainer" style="width:100%">
                                <?php echo $this->Form->input('memo', array('style' => 'width:90%; height: 70px;')); ?>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr> 
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <table cellpadding="0" style="width: 100%">
                    <tr>
                        <td colspan="2"><label for="SalesOrderCustomerName"><?php echo PATIENT_NAME; ?> <span class="red">*</span></label></td>
                        <td style="width: 33%;"><label for="SalesOrderPaymentTermId"><?php echo TABLE_PAYMENT_TERMS; ?></label></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="inputContainer" style="width:100%">
                                <?php
                                echo $this->Form->hidden('patient_group_id');
                                echo $this->Form->hidden('customer_id');
                                if($allowAddCustomer){
                                ?>
                                <div class="addnewSmall" style="float: left;">
                                    <?php echo $this->Form->text('customer_name', array('class' => 'validate[required]', 'style' => 'width: 285px; border: none;')); ?>
                                    <img alt="<?php echo MENU_CUSTOMER_MANAGEMENT_ADD; ?>" align="absmiddle" style="display: none; cursor: pointer; width: 16px;" id="addCustomerSales" onmouseover="Tip('<?php echo MENU_CUSTOMER_MANAGEMENT_ADD; ?>')" src="<?php echo $this->webroot . 'img/button/plus.png'; ?>" />
                                </div>
                                <?php
                                } else {
                                    echo $this->Form->text('customer_name', array('class' => 'validate[required]', 'style' => 'width:320px'));
                                }
                                ?>
                                &nbsp;&nbsp;<img alt="<?php echo TABLE_SHOW_CUSTOMER_LIST; ?>" align="absmiddle" style="cursor: pointer; width: 22px; height: 22px;" class="searchCustomerSales" onmouseover="Tip('<?php echo TABLE_SHOW_CUSTOMER_LIST; ?>')" src="<?php echo $this->webroot . 'img/button/search.png'; ?>" />
                                <img alt="<?php echo ACTION_REMOVE; ?>" align="absmiddle" style="display: none; cursor: pointer; height: 22px;" class="deleteCustomerSales" onmouseover="Tip('<?php echo ACTION_REMOVE; ?>')" src="<?php echo $this->webroot . 'img/button/pos/remove-icon-png-25.png'; ?>" />
                            </div>
                        </td>
                        <td>
                            <div class="inputContainer" style="width:100%">
                                <?php echo $this->Form->input('payment_term_id', array('style' => 'width:90%;', 'selected'=> 1 ,'label' => false,'div' => false)); ?>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 17%; vertical-align: top;">
                <table cellpadding="0" style="width: 100%">
                    <tr>
                        <td><label for="SalesOrderOrderNumber"><?php echo MENU_PRESCRIPTION_CODE; ?></label></label></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="inputContainer" style="width:100%">
                                <?php echo $this->Form->hidden('order_id'); ?>
                                <?php echo $this->Form->text('order_number', array('style' => 'width:70%', 'readonly' => TRUE)); ?>
                                <img alt="Search" align="absmiddle" style="cursor: pointer; width: 22px; height: 22px;" class="searchOrderSales" onmouseover="Tip('<?php echo GENERAL_SEARCH; ?>')" src="<?php echo $this->webroot . 'img/button/search.png'; ?>" />
                                <img alt="Delete" align="absmiddle" style="display: none; cursor: pointer;" class="deleteOrderSales" onmouseover="Tip('<?php echo ACTION_DELETE; ?>')" src="<?php echo $this->webroot . 'img/button/delete.png'; ?>" />
                            </div>
                       </td>
                    </tr>
                </table>
            </td>
            <td style="width: 17%; vertical-align: top;">
                <table cellpadding="0" style="width: 100%">
                    <tr>
                        <td><?php if(count($locationGroups) > 1){ ?><label for="SalesOrderLocationGroupId"><?php echo TABLE_LOCATION_GROUP; ?> <span class="red">*</span><?php } ?></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="inputContainer" style="width:100%; <?php if(count($locationGroups) == 1){ ?>display: none;<?php } ?>">
                                <?php
                                $emptyWare = INPUT_SELECT;
                                if(count($locationGroups) == 1){
                                    $emptyWare = false;
                                }
                                echo $this->Form->input('location_group_id', array('empty' => $emptyWare, 'label' => false)); 
                                ?>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table id="salesTermCondition" cellpadding="0" cellspacing="0" style="width: 100%; display: none;">
        <tr>
            <td>
                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                    <tr>
                        <td style=" vertical-align: top; height: 105px;">
                            <table cellpadding="0" cellspacing="0" style="width: 100%;">
                                <?php
                                $termForms = array();
                                $sqlTermApply = mysql_query("SELECT * FROM term_condition_applies WHERE is_active = 1 AND module_type_id = 25 GROUP BY module_type_id, term_condition_type_id ORDER BY id");
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
            </td>
        </tr>
    </table>
</fieldset>
<div class="orderDetailSales" style=" margin-top: 5px; text-align: center;">
</div>
<div class="footerFormSales">
    <div style="float: left; width: 19%;">
        <div class="buttons">
            <a href="#" class="positive btnBackSalesOrder">
                <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
                <?php echo ACTION_BACK; ?>
            </a>
        </div>
        <div class="buttons">
            <button type="submit" class="positive saveSales" >
                <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
                <span class="txtSaveSales"><?php echo ACTION_SAVE; ?></span>
            </button>
        </div>
        <div style="clear: both;"></div>
    </div>
    <div style="float: right; width: 80%;">
        <table style="width:100%">
            <tr>
                <td style="width:10%; text-align: right;"><label for="SalesOrderTotalAmount"><?php echo TABLE_SUB_TOTAL; ?>:</label></td>
                <td style="width:15%;">
                    <div class="inputContainer" style="width:100%">
                        <?php echo $this->Form->text('total_amount', array('readonly' => true, 'class' => 'float validate[required]', 'style' => 'width: 75%; height:15px; font-size:12px; font-weight: bold', 'value' => "0.000")); ?> <span class="lblSymbolSales"></span>
                    </div>
                </td>
                <td style="width:8%; text-align: right;"><label for="SalesOrderDiscountUs"><?php echo GENERAL_DISCOUNT; ?>:</label></td>
                <td style="width:18%;">
                    <div class="inputContainer" style="width:100%">
                        <?php echo $this->Form->hidden('discount_percent', array('class' => 'float', 'value'=>'0')); ?>
                        <?php echo $this->Form->text('discount_us', array('style' => 'width: 45%; height:15px; font-size:12px; font-weight: bold', 'class' => 'float', 'value'=>'0', 'readonly' => true)); ?> <span class="lblSymbolSales"></span>
                        <span id="salesLabelDisPercent"></span>
                        <?php if($allowEditInvDis){ ?><img alt="Remove" src="<?php echo $this->webroot . 'img/button/cross.png'; ?>" id="btnRemoveSalesTotalDiscount" align="absmiddle" style="cursor: pointer; display: none;" onmouseover="Tip('Remove Discount')" /><?php } ?>
                    </div>
                </td>
                <td style="width:17%; text-align: right;">
                    <label for="SalesOrderVatSettingId" id="lblSalesOrderVatSettingId"><?php echo TABLE_VAT; ?> <span class="red">*</span>:</label>
                    <select id="SalesOrderVatSettingId" name="data[SalesOrder][vat_setting_id]" style="width: 75%;" class="validate[required]">
                        <option com-id="" value="" rate="0.000" acc=""><?php echo INPUT_SELECT; ?></option>
                        <?php
                        // VAT
                        $sqlVat = mysql_query("SELECT id, name, vat_percent, company_id, chart_account_id FROM vat_settings WHERE is_active = 1 AND type = 1 AND company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id'].");");
                        while($rowVat = mysql_fetch_array($sqlVat)){
                        ?>
                        <option com-id="<?php echo $rowVat['company_id']; ?>" value="<?php echo $rowVat['id']; ?>" rate="<?php echo $rowVat['vat_percent']; ?>" acc="<?php echo $rowVat['chart_account_id']; ?>"><?php echo $rowVat['name']; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </td>
                <td style="width:9%;">
                    <div class="inputContainer" style="width:100%">
                        <?php echo $this->Form->hidden('vat_chart_account_id', array('value' => '')); ?>
                        <?php echo $this->Form->hidden('total_vat', array('class' => 'float', 'value' => 0)); ?>
                        <?php echo $this->Form->text('vat_percent', array('value' => 0, 'readonly' => true, 'style' => 'width: 50%; height:15px; font-size:12px; font-weight: bold', 'class' => 'float')); ?> <span class="lblSymbolSalesPercent">(%)</span>
                    </div>
                </td>
                <td style="width:5%; text-align: right;"><label for="SalesOrderSubTotalAmount"><?php echo TABLE_TOTAL; ?>:</label></td>
                <td style="width:15%;">
                    <div class="inputContainer" style="width:100%">
                        <?php echo $this->Form->text('sub_total_amount', array('readonly' => true, 'class' => 'float validate[required]', 'style' => 'width: 75%; height:15px; font-size:12px; font-weight: bold', 'value'=>'0')); ?> <span class="lblSymbolSales"></span>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div style="clear: both;"></div>
</div>
<?php echo $this->Form->end(); ?>