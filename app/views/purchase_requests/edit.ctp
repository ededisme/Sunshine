<?php 
// Get Decimal
$sqlOption = mysql_query("SELECT product_cost_decimal FROM setting_options");
$rowOption = mysql_fetch_array($sqlOption);

include("includes/function.php");
$this->element('check_access');
$allowEditCost   = checkAccess($user['User']['id'], $this->params['controller'], 'editCost');
$allowAddService = checkAccess($user['User']['id'], $this->params['controller'], 'service');
$allowEditTerm   = checkAccess($user['User']['id'], $this->params['controller'], 'editTermsCondition');
// Prevent Button Submit
echo $this->element('prevent_multiple_submit'); 
$queryClosingDate=mysql_query("SELECT DATE_FORMAT(date,'%d/%m/%Y') FROM account_closing_dates ORDER BY id DESC LIMIT 1");
$dataClosingDate=mysql_fetch_array($queryClosingDate);
?>
<script type="text/javascript">
    var indexRowPO  = 0;
    var rowClonePO  = $("#detailPO");
    var pprTimeCode = 1;
    var timestamp   = new Date().getTime();
    
    function resizeFormTitlePO(){
        var screen = 16;
        var widthList = $("#bodyListPO").width();
        $("#tblHeaderPO").css('width',widthList);
        var widthTitle = widthList - screen;
        $("#tblHeaderPO").css('padding','0px');
        $("#tblHeaderPO").css('margin-top','5px');
        $("#tblHeaderPO").css('width',widthTitle);
    }
    
    function resizeFornScrollPO(){
        var windowHeight = $(tabPRId).height();
        var formHeader = 0;
        if ($('#divPOTop').is(':hidden')) {
            formHeader = 0;
        } else {
            formHeader = $("#divPOTop").height();
        }
        var btnHeader    = $("#btnHideShowHeaderPurchaseRequest").height();
        var formFooter   = $("#tblFooterPO").height();
        var formSearch   = $("#searchFormPO").height();
        var tableHeader  = $("#tblHeaderPO").height();
        var getHeight = windowHeight - (formHeader + btnHeader + tableHeader + formSearch + formFooter);
        $("#bodyListPO").css('height',getHeight);
        $("#bodyListPO").css('padding','0px');
        $("#bodyListPO").css('width','100%');
        $("#bodyListPO").css('overflow-x','hidden');
        $("#bodyListPO").css('overflow-y','scroll');
    }
    
    function refreshScreenPO(){
        $("#tblHeaderPO").removeAttr('style');
    }
    
    function calcTotalPO(){
        var totalAmount = 0;
        var totalVat    = 0;
        var vatPercent  = replaceNum($("#PurchaseRequestVatPercent").val());
        var total       = 0;
        var vatCal           = $("#PurchaseRequestVatCalculate").val();
        var totalBfDis       = 0;
        var totalAmtCalVat   = 0;
        $(".listBodyPO").find(".total_cost").each(function(){
            totalAmount += replaceNum($(this).val());
        });
        totalAmtCalVat = totalAmount;
        if(vatCal == 1){
            $(".listBodyPO").each(function(){
                var qty   = replaceNum($(this).find(".qty").val());
                var price = replaceNum($(this).find(".unit_cost").val());
                totalBfDis += replaceNum(converDicemalJS(qty * price));
            });
            totalAmtCalVat = totalBfDis;
        }
        totalVat = replaceNum(converDicemalJS((totalAmtCalVat * vatPercent) / 100).toFixed(<?php echo $rowOption[0]; ?>));
        total    = converDicemalJS(totalAmount + totalVat);
        $("#PurchaseRequestTotalAmount").val((parseFloat(totalAmount)).toFixed(<?php echo $rowOption[0]; ?>));
        $("#PurchaseRequestTotalVat").val((parseFloat(totalVat)).toFixed(<?php echo $rowOption[0]; ?>));
        $("#PurchaseRequestTotalAmountAll").val((parseFloat(total)).toFixed(<?php echo $rowOption[0]; ?>));
    }
    
    function addProductPO(name, uom, product_id, unit_cost, smallUomValPro){
        var productId           = product_id;
        var productName         = name;
        var smallUomPro         = smallUomValPro;  
        indexRowPO = Math.floor((Math.random() * 100000) + 1);
        
        var tr = rowClonePO.clone(true);
        tr.removeAttr("style").removeAttr("id");
        
        tr.find("td:eq(0)").html(indexRowPO);
        tr.find("td .product_id").attr("id", "product_id"+indexRowPO).val(productId);
        tr.find("td .product_name").attr("id", "product_name"+indexRowPO).val(productName);
        tr.find("td .smallUomValPro").attr("id", "smallUomValPro").val(smallUomPro);
        tr.find("td .qty_uom_id").attr("id", "qty_uom_id"+indexRowPO).html(uom);
        tr.find("td .qty").attr("id", "qty_"+indexRowPO).val(1);
        tr.find("td .unit_cost").attr("id", "unit_cost"+indexRowPO).val(Number(unit_cost).toFixed(<?php echo $rowOption[0]; ?>));
        tr.find("td .defaltCost").val(unit_cost);
        tr.find("td .tmp_unit_cost").val(unit_cost);
        tr.find("td .total_cost").attr("id", "total_cost"+indexRowPO).val(unit_cost);
        tr.find("td .h_total_cost").attr("id", "h_total_cost"+indexRowPO).val(unit_cost);
        tr.find("td .note").attr("id", "note"+indexRowPO).val("");
        tr.find("td .btnRemoveDiscountPO").hide();
        tr.find("td .btnRemovePO").show();
        var conversion = parseInt(tr.find("td .qty_uom_id").find("option:selected").attr('conversion'));
        tr.find("td .prr_conversion").val(parseInt(smallUomPro / conversion));
        $("#tblPO").append(tr);
        
        $("#tblPO").find("tr:last").find("td .qty").select().focus();
        pprTimeCode = 1;
        checkEventPO();
        sortNuTablePO();
        calcTotalPO();
    }
    
    function addServicePO(service_id, name, unit_cost, serviceCode, uomId){
        indexRowPO       = Math.floor((Math.random() * 100000) + 1);
        var serviceID    = service_id;
        var productName  = name;
        var tr           = rowClonePO.clone(true);
        
        tr.removeAttr("style").removeAttr("id");
        tr.find("td:eq(0)").html(indexRowPO);
        tr.find("td .product_id").attr("id", "product_id"+indexRowPO).val('');
        tr.find("td .btnProductInfo").remove();
        tr.find("td .service_id").attr("id", "service_id"+indexRowPO).val(serviceID);
        tr.find("td .product_name").attr("id", "product_name"+indexRowPO).val(productName);
        tr.find("td .smallUomValPro").attr("id", "smallUomValPro").val(1);
        tr.find("td .prr_conversion").val(1);
        tr.find("td .qty").attr("id", "qty_"+indexRowPO).val(1);
        tr.find("td .unit_cost").attr("id", "unit_cost"+indexRowPO).val(unit_cost);
        tr.find("td .defaltCost").val(unit_cost);
        tr.find("td .tmp_unit_cost").val(unit_cost);
        tr.find("td .total_cost").attr("id", "total_cost"+indexRowPO).val(unit_cost);
        tr.find("td .h_total_cost").attr("id", "h_total_cost"+indexRowPO).val(unit_cost);
        tr.find("td .note").attr("id", "note"+indexRowPO).val("");
        tr.find("td .btnRemovePO").show();
        if(uomId == ''){
            tr.find("select[name='qty_uom_id[]']").attr("id", "qty_uom_id_"+indexRowPO).html('<option value="1" conversion="1" selected="selected">None</option>').css('visibility', 'hidden');
        } else {
            tr.find("select[name='qty_uom_id[]']").attr("id", "qty_uom_id_"+indexRowPO).find("option[value='"+uomId+"']").attr("selected", true);
            tr.find("select[name='qty_uom_id[]']").find("option[value!='"+uomId+"']").hide();
        }
        $("#tblPO").append(tr);
        tr.find("td .qty").select().focus();
        
        pprTimeCode = 1;
        checkEventPO();
        sortNuTablePO();
        calcTotalPO();
    }
    
    function clearFormPO(){
        $("#tblPO tr#detailPO").each(function(i){
            if($("#tblPO tr#detailPO").length == 1){
                $(this).find("td .product_id").val('');
                $(this).find("td .product_name").val('');
                $(this).find("td .qty").val('');
                $(this).find("td .unit_cost").val('');
                $(this).find("td .total_cost").val('0');
                $("#tblPO tr#detailPO").hide();
            }else{
                $(this).remove();
            }
        });
        $("#PurchaseRequestTotalAmount").val('0');
        calcTotalPO();
        pprTimeCode = 1;
    }
    
    function sortNuTablePO(){
        var sort = 1;
        $(".listBodyPO").each(function(){
            $(this).find("td:eq(0)").html(sort);
            sort++;
        });
    }
    
    function checkVendorPO(field, rules, i, options){
        if($("#PurchaseRequestVendorId").val() == "" || $("#PurchaseRequestVendorName").val() == ""){
            return "* Invalid Vendor";
        }
    }
    
    function serachProCodePO(code, field){
        if($("#PurchaseRequestCompanyId").val() == "" || $("#PurchaseRequestBranchId").val() == ""){
            pprTimeCode = 1;
            $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_SELECT_FIELD_REQURIED; ?></p>');
            $("#dialog").dialog({
                title: '<?php echo DIALOG_WARNING; ?>',
                resizable: false,
                modal: true,
                width: 'auto',
                height: 'auto',
                open: function(event, ui){
                    $(".ui-dialog-buttonpane").show();
                },
                close: function(event, ui){
                    
                },
                buttons: {
                    '<?php echo ACTION_CLOSE; ?>': function() {
                        $(this).dialog("close");
                    }
                }
            });
        }else {
            $.ajax({
                type:   "POST",
                url:    "<?php echo $this->base . "/purchase_requests/searchProductCode/"; ?>" +$("#PurchaseRequestCompanyId").val()+"/"+$("#PurchaseRequestBranchId").val()+"/"+code+"/2",
                beforeSend: function(){
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(msg){
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $(field).val('');
                    if(msg == '<?php echo TABLE_NO_PRODUCT; ?>'){
                        $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo TABLE_NO_PRODUCT; ?></p>');
                        $("#dialog").dialog({
                            title: '<?php echo DIALOG_WARNING; ?>',
                            resizable: false,
                            modal: true,
                            width: 'auto',
                            height: 'auto',
                            open: function(event, ui){
                                $(".ui-dialog-buttonpane").show();
                            },
                            buttons: {
                                '<?php echo ACTION_CLOSE; ?>': function() {
                                    $(field).focus();
                                    $(this).dialog("close");
                                }
                            }
                        });
                        pprTimeCode = 1;
                    }else{
                        var data = msg;
                        var record = $.parseJSON(data);
                        if(data){
                            $.ajax({
                                type: "GET",
                                url: "<?php echo $this->base; ?>/uoms/getRelativeUom/"+record[2],
                                data: "",
                                beforeSend: function(){
                                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                                },
                                success: function(msg){
                                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                                    addProductPO(record[1]+"-"+record[4], msg, record[0], record[3], record[5]);
                                }
                            });
                        }
                    }
                }
            });
        }
    }
    
    function searchAllServicePO(){
        if($("#PurchaseRequestCompanyId").val()=="" || $("#PurchaseRequestBranchId").val() == ""){
            pprTimeCode == 1;
            $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_SELECT_COMPANY_FIRST; ?></p>');
            $("#dialog").dialog({
                title: '<?php echo DIALOG_WARNING; ?>',
                resizable: false,
                modal: true,
                width: 'auto',
                height: 'auto',
                open: function(event, ui){
                    $(".ui-dialog-buttonpane").show();
                },
                close: function(event, ui){

                },
                buttons: {
                    '<?php echo ACTION_CLOSE; ?>': function() {
                        $(this).dialog("close");
                    }
                }
            });
        }else{
            $.ajax({
                type:   "POST",
                url:    "<?php echo $this->base . "/purchase_requests/service"; ?>/" + $("#PurchaseRequestCompanyId").val()+"/"+$("#PurchaseRequestBranchId").val(),
                beforeSend: function(){
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(msg){
                    pprTimeCode == 1;
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $("#dialog").html(msg).dialog({
                        title: '<?php echo SALES_ORDER_ADD_SERVICE; ?>',
                        resizable: false,
                        modal: true,
                        width: 'auto',
                        height: 'auto',
                        position:'center',
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show();
                        },
                        buttons: {
                            '<?php echo ACTION_OK; ?>': function() {
                                var formName = "#ServiceServiceForm";
                                var validateBack =$(formName).validationEngine("validate");
                                if(!validateBack){
                                    return false;
                                }else{
                                    addServicePO($("#ServiceServiceId").val(),$("#ServiceServiceId").find("option:selected").html(),$("#ServiceUnitPrice").val(),$("#ServiceServiceId").find("option:selected").attr("scode"),$("#ServiceServiceId").find("option:selected").attr("suom"));
                                }
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            });
        }
    }
    
    function deleteVendorPO(){
        $("#PurchaseRequestVendorId").val("");
        $("#PurchaseRequestVendorName").val("");
        $("#PurchaseRequestVendorName").removeAttr("readonly");
        $("#deleteSearchVendorPO").hide();
        $("#searchVendorPO").show();
    }
    
    function checkBfSavePO(){
        var formName = "#PurchaseRequestEditForm";
        var validateBack =$(formName).validationEngine("validate");
        if(!validateBack){
            return false;
        }else{
            if(($("#PurchaseRequestTotalAmount").val() == undefined && $("#PurchaseRequestTotalAmount").val() == "") || $(".listBodyPO").find(".product_id").val() == undefined){
                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>Please make an order first.</p>');
                $("#dialog").dialog({
                    title: '<?php echo DIALOG_INFORMATION; ?>',
                    resizable: false,
                    modal: true,
                    width: 'auto',
                    height: 'auto',
                    pprsition:'center',
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
    
    function searchProductPO(){
        if($("#PurchaseRequestCompanyId").val() == "" || $("#PurchaseRequestBranchId").val() == ""){
            pprTimeCode = 1;
            $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_SELECT_COMPANY_LOCATION; ?></p>');
            $("#dialog").dialog({
                title: '<?php echo DIALOG_WARNING; ?>',
                resizable: false,
                modal: true,
                width: 'auto',
                height: 'auto',
                open: function(event, ui){
                    $(".ui-dialog-buttonpane").show();
                },
                close: function(event, ui){
                    
                },
                buttons: {
                    '<?php echo ACTION_CLOSE; ?>': function() {
                        $(this).dialog("close");
                    }
                }
            });
        }else{
            $.ajax({
                type:   "POST",
                url:    "<?php echo $this->base . "/purchase_requests/product/"; ?>"+$("#PurchaseRequestCompanyId").val()+"/"+$("#PurchaseRequestBranchId").val(),
                beforeSend: function(){
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(msg){
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $("#dialog").html(msg).dialog({
                        title: '<?php echo MENU_PRODUCT_MANAGEMENT_INFO; ?>',
                        resizable: false,
                        modal: true,
                        width: 800,
                        height: 550,
                        pprsition:'center',
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show();
                        },
                        buttons: {
                            '<?php echo ACTION_OK; ?>': function() {
                                var data = $("input[name='chkProduct']:checked").val();
                                var record = $.parseJSON(data);
                                if(data){
                                    $.ajax({
                                        type: "GET",
                                        url: "<?php echo $this->base; ?>/uoms/getRelativeUom/"+record[1],
                                        data: "",
                                        beforeSend: function(){
                                            $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                                        },
                                        success: function(msg){
                                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                                            addProductPO($("input[name='chkProduct']:checked").attr('class')+"-"+$("input[name='chkProduct']:checked").attr('id'), msg, record[0], record[2], record[5]);
                                        }
                                    });
                                }
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            });
        }
    }
    
    function moveRowPO(){
        $(".btnMoveDownPO, .btnMoveUpPO").unbind('click');
        $(".btnMoveDownPO").click(function () {
            var rowToMove = $(this).parents('tr.listBodyPO:first');
            var next = rowToMove.next('tr.listBodyPO');
            if (next.length == 1) { next.after(rowToMove); }
            sortNuTablePO();
        });

        $(".btnMoveUpPO").click(function () {
            var rowToMove = $(this).parents('tr.listBodyPO:first');
            var prev = rowToMove.prev('tr.listBodyPO');
            if (prev.length == 1) { prev.before(rowToMove); }
            sortNuTablePO();
        });
    }
    
    function checkEventPO(){
        eventKeyRowPO();
        $(".listBodyPO").unbind("click");
        $(".listBodyPO").click(function(){
            eventKeyRowPO();
        });
    }
    
    function eventKeyRowPO(){
        loadAutoCompleteOff();
        $(".qty, .unit_cost, .qty_uom_id, .total_cost, .btnRemovePO, .noteAddPO, .btnDiscountPO, .btnRemoveDiscountPO, .btnProductInfo").unbind('keypress').unbind('keyup').unbind('change').unbind('click');
        $(".float").autoNumeric({mDec: <?php echo $rowOption[0]; ?>, aSep: ','});
        $(".qty").autoNumeric({mDec: 2, aSep: ','});
        $(".total_cost").keyup(function(){
            var value     = replaceNum($(this).val());
            var unitPrice = 0;
            var qty   = replaceNum($(this).closest("tr").find(".qty").val());
            unitPrice = parseFloat(converDicemalJS(value / qty));
            $(this).closest("tr").find(".h_total_cost").val(parseFloat(converDicemalJS(unitPrice * qty)).toFixed(<?php echo $rowOption[0]; ?>));
            $(this).closest("tr").find(".unit_cost").val(unitPrice.toFixed(<?php echo $rowOption[0]; ?>));
            calcTotalPO();
        });
        
        $(".qty, .unit_cost").keyup(function(){
            var qty = "";
            var conversion = replaceNum($(this).closest("tr").find(".qty_uom_id").find("option:selected").attr("conversion"));
            var unitCost   = replaceNum($(this).closest("tr").find("td .unit_cost").val());
            var tmpCost    = replaceNum($(this).closest("tr").find("td .tmp_unit_cost").val());
            if($(this).attr("class") == 'unit_cost validate[required] float'){
                if(unitCost != tmpCost){
                    $(this).closest("tr").find(".defaltCost").val(converDicemalJS(unitCost * conversion).toFixed(<?php echo $rowOption[0]; ?>));
                    $(this).closest("tr").find(".tmp_unit_cost").val(unitCost);
                }
            }
            if(replaceNum($(this).closest("tr").find("td .qty").val()) != ""){
                qty = replaceNum($(this).closest("tr").find("td .qty").val());
            }else{
                qty = 1;
            }
            var totalAmount = converDicemalJS(parseFloat(replaceNum(qty)) * replaceNum($(this).closest("tr").find("td .unit_cost").val()));
            $(this).closest("tr").find("td .h_total_cost").val(totalAmount.toFixed(<?php echo $rowOption[0]; ?>));
            $(this).closest("tr").find("td .total_cost").val(converDicemalJS(parseFloat(totalAmount)).toFixed(<?php echo $rowOption[0]; ?>));
            calcTotalPO();
        });
        
        $(".qty").keypress(function(e){
            if((e.which && e.which == 13) || e.keyCode == 13){
                if(replaceNum($(this).val()) != "" && replaceNum($(this).val()) > 0){
                    $(this).closest("tr").find(".unit_cost").select().focus();
                }
                return false;
            }
        });
        
        $(".unit_cost").keypress(function(e){
            if((e.which && e.which == 13) || e.keyCode == 13){
                if(replaceNum($(this).val()) != "" && replaceNum($(this).val()) > 0){
                    $(this).closest("tr").find(".qty_uom_id").select().focus();
                }
                return false;
            }
        });
        
        $(".qty_uom_id").keypress(function(e){
            if((e.which && e.which == 13) || e.keyCode == 13){
                if(replaceNum($(this).val()) != "" && replaceNum($(this).val()) > 0){
                    $(".productPO").select().focus();
                }
                return false;
            }
        });
        
        $(".qty_uom_id").change(function(){            
            var value = replaceNum($(this).val());
            var uomConversion = replaceNum($(this).find("option[value='"+value+"']").attr('conversion'));            
            var smUomVal = replaceNum($(this).closest("tr").find(".smallUomValPro").val());
            var uomCon   = converDicemalJS(smUomVal / uomConversion);   
            
            var unit_price = (parseFloat(converDicemalJS(replaceNum($(this).closest("tr").find(".defaltCost").val())/uomConversion))).toFixed(<?php echo $rowOption[0]; ?>);
            if($(this).closest("tr").find(".product_id").val() != ""){
                $(this).closest("tr").find(".unit_cost").val(unit_price);
                $(this).closest("tr").find(".tmp_unit_cost").val(unit_price);
                var totalAmount = parseFloat( converDicemalJS(unit_price * replaceNum($(this).closest("tr").find(".qty").val())) );
                $(this).closest("tr").find(".prr_conversion").val(uomCon);
                
                $(this).closest("tr").find(".h_total_cost").val(totalAmount.toFixed(<?php echo $rowOption[0]; ?>));
                $(this).closest("tr").find(".total_cost").val( (converDicemalJS(totalAmount)).toFixed(<?php echo $rowOption[0]; ?>) );
                calcTotalPO();
            }
        });
        
        $(".btnRemovePO").click(function(){
            var currentTr = $(this).closest("tr");
            $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>Are you sure to remove this order?</p>');
            $("#dialog").dialog({
                title: '<?php echo DIALOG_INFORMATION; ?>',
                resizable: false,
                pprsition:'center',
                modal: true,
                width: '300',
                height: 'auto',
                open: function(event, ui){
                    $(".ui-dialog-buttonpane").show();
                },
                buttons: {
                    '<?php echo ACTION_CANCEL; ?>': function() {
                        $(this).dialog("close");
                    },
                    '<?php echo ACTION_OK; ?>': function() {
                        currentTr.remove();
                        calcTotalPO();
                        sortNuTablePO();
                        $(this).dialog("close");
                    }
                }
            });
        });
        
        // Button Show Information
        $(".btnProductInfo").click(function(){
            showProductInfoPO($(this));
        });
        
        $(".noteAddPO").click(function(){
            addNotePO($(this));
        });
        
        moveRowPO();
    }
    
    function searchVendorPO(){
        var companyId = $("#PurchaseRequestCompanyId").val();
        if($("#PurchaseRequestCompanyId").val() ==""){
            $(this).val('');
            $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_SELECT_COMPANY_FIRST; ?></p>');
            $("#dialog").dialog({
                title: '<?php echo DIALOG_WARNING; ?>',
                resizable: false,
                modal: true,
                width: 'auto',
                height: 'auto',
                open: function(event, ui){
                    $(".ui-dialog-buttonpane").show();
                },
                close: function(event, ui){

                },
                buttons: {
                    '<?php echo ACTION_CLOSE; ?>': function() {
                        $(this).dialog("close");
                    }
                }
            });
        }else{
            $.ajax({
                type:   "POST",
                url:    "<?php echo $this->base . "/purchase_requests/vendor/"; ?>"+companyId,
                beforeSend: function(){
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(msg){
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $("#dialog").html(msg).dialog({
                        title: '<?php echo MENU_VENDOR; ?>',
                        resizable: false,
                        modal: true,
                        width: 850,
                        height: 500,
                        pprsition:'center',
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show();
                        },
                        close: function(){
                            pprTimeCode = 1;
                        },
                        buttons: {
                            '<?php echo ACTION_OK; ?>': function() {
                                // calculate due_date
                                var data = $("input[name='chkVendor']:checked").val();
                                if(data){
                                    $("#PurchaseRequestVendorId").val(data.split('-')[0]);
                                    $("#PurchaseRequestVendorName").val(data.split('-')[2]);
                                    $("#PurchaseRequestVendorName").attr('readonly', true);
                                    $("#searchVendorPO").hide();
                                    $("#deleteSearchVendorPO").show();
                                }
                                pprTimeCode = 1;
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            });
        }
    }
    
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        loadAutoCompleteOff();
        // Hide Branch
        $("#PurchaseRequestBranchId").filterOptions('com', '<?php echo $this->data['PurchaseRequest']['company_id']; ?>', '<?php echo $this->data['PurchaseRequest']['branch_id']; ?>');
        $("#detailPO").remove();
        // Add EventKey List
        checkEventPO();
        var waitForFinalEventPO = (function () {
          var timers = {};
          return function (callback, ms, uniqueId) {
            if (!uniqueId) {
              uniqueId = "Don't call this twice without a uniqueId";
            }
            if (timers[uniqueId]) {
              clearTimeout (timers[uniqueId]);
            }
            timers[uniqueId] = setTimeout(callback, ms);
          };
        })();
        
        // Click Tab Refresh Form List: Screen, Title, Scroll
        if(tabPRReg != tabPRId){
            $("a[href='"+tabPRId+"']").click(function(){
                if($("#bodyListPO").html() != '' && $("#bodyListPO").html() != null){
                    waitForFinalEventPO(function(){
                        refreshScreenPO();
                        resizeFormTitlePO();
                        resizeFornScrollPO();  
                    }, 500, "Finish");
                }
            });
            tabPRReg = tabPRId;
        }

        waitForFinalEventPO(function(){
              refreshScreenPO();
              resizeFormTitlePO();
              resizeFornScrollPO();  
            }, 500, "Finish");
        
        $(window).resize(function(){
            if(tabPRReg == $(".ui-tabs-selected a").attr("href")){
                waitForFinalEventPO(function(){
                    refreshScreenPO();
                    resizeFormTitlePO();
                    resizeFornScrollPO();  
                  }, 500, "Finish");
            }
        });
        
        // Hide / Show Header
        $("#btnHideShowHeaderPurchaseRequest").click(function(){
            var PurchaseRequestCompanyId = $("#PurchaseRequestCompanyId").val();
            var PurchaseRequestBranchId  = $("#PurchaseRequestBranchId").val();
            var PurchaseRequestOrderDate = $("#PurchaseRequestOrderDate").val();
            var PurchaseRequestVendorId  = $("#PurchaseRequestVendorId").val();
            
            if(PurchaseRequestCompanyId == "" || PurchaseRequestBranchId == "" || PurchaseRequestOrderDate == "" || PurchaseRequestVendorId == ""){
                $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_SELECT_FIELD_REQURIED; ?></p>');
                $("#dialog").dialog({
                    title: '<?php echo DIALOG_WARNING; ?>',
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
            }else{            
                var label  = $(this).find("span").text();
                var action = '';
                var img    = '<?php echo $this->webroot . 'img/button/'; ?>';
                if(label == 'Hide'){
                    action = 'Show';
                    $("#divPOTop").hide();
                    img += 'arrow-down.png';
                } else {
                    action = 'Hide';
                    $("#divPOTop").show();
                    img += 'arrow-up.png';
                }
                $(this).find("span").text(action);
                $(this).find("img").attr("src", img);
                if(tabPRReg == $(".ui-tabs-selected a").attr("href")){
                    waitForFinalEventPO(function(){
                        resizeFornScrollPO();
                    }, 300, "Finish");
                }
            }
        });
        
        // Form Validate
        $("#PurchaseRequestEditForm").validationEngine('detach');
        $("#PurchaseRequestEditForm").validationEngine('attach');
        
        $(".btnSavePurchaseOrder").click(function(){
            if(checkBfSavePO() == true){
                return true;
            }else{
                return false;
            }
        });
        
        $("#PurchaseRequestEditForm").ajaxForm({
            dataType: 'json',
            beforeSubmit: function(arr, $form, options) {
                $(".txtSavePO").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            beforeSerialize: function($form, options) {
                $("#PurchaseRequestOrderDate, #PurchaseRequestExpectedDeliveryDate").datepicker("option", "dateFormat", "yy-mm-dd");
                $(".float, .qty").each(function(){
                    $(this).val($(this).val().replace(/,/g,""));
                });
                $("#PurchaseRequestTotalAmount").val($("#PurchaseRequestTotalAmount").val().replace(/,/g,""));
                var totalAmt = replaceNum($("#PurchaseRequestTotalAmount").val());
                var totalDps = replaceNum($("#PurchaseRequestOldTotalDeposit").val());
                // Check total amount < total deposit
                if(totalAmt < totalDps && totalDps > 0){
                    errorSaveDepositPO();
                    return false;
                }
            },
            error: function (result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                createSysAct('Purchase Order', 'Edit', 2, result.responseText);
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
                        backPruchaseOrder();
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
                    codeDialogPO();
                }else if(result.code == "2"){
                    errorSavePO();
                }else if(result.code == "3"){
                    errorSaveDepositPO();
                }else{
                    createSysAct('Purchase Order', 'Edit', 1, '');
                    $("#dialog").html('<div class="buttons"><button type="submit" class="positive printInvoicePO" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span><?php echo ACTION_PRINT_PURCHASE_ORDER; ?></span></button></div> ');
                    $(".printInvoicePO").click(function(){
                        $.ajax({
                            type: "POST",
                            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printInvoice/"+result.ppr_id,
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
                        pprsition:'center',
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show();
                        },
                        close: function(){
                            $(this).dialog({close: function(){
                            }});
                            $(this).dialog("close");
                            backPruchaseOrder();
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
        
        $(".productPO").autocomplete("<?php echo $this->base . "/purchase_requests/searchProduct"; ?>", {
            width: 410,
            max: 10,
            scroll: true,
            scrollHeight: 500,
            formatItem: function(data, i, n, value) {
                return value.split(".*")[0]+"-"+value.split(".*")[1];
            },
            formatResult: function(data, value) {
                return value.split(".*")[0]+"-"+value.split(".*")[1];
            }
        }).result(function(event, value){
            var code = value.toString().split(".*")[0];
            $(".productPO").val(code);
            if(pprTimeCode == 1){
                pprTimeCode = 2;
                serachProCodePO(code,'.productPO');
            }
        });
        
        $("#searchVendorPO").click(function(){
            if(checkOrderDatePO() == true){
                searchVendorPO();
            }
        });
        
        $("#deleteSearchVendorPO").click(function(){
            deleteVendorPO();
        });
        
        $("#PurchaseRequestVendorName").focus(function(){
            checkOrderDatePO();
        });
        
        $("#PurchaseRequestVendorName").keypress(function(e){
            if((e.which && e.which != 13) || e.keyCode != 13){
                $("#PurchaseRequestVendorId").val("");
            }
        });
        
        $("#PurchaseRequestVendorName").autocomplete("<?php echo $this->base . "/purchase_requests/searchVendor"; ?>", {
            width: 410,
            max: 10,
            scroll: true,
            scrollHeight: 500,
            formatItem: function(data, i, n, value) {
                if(checkCompanyPO(value.split(".*")[4])){
                    return value.split(".*")[2] + " - " + value.split(".*")[1];
                }
            },
            formatResult: function(data, value) {
                if(checkCompanyPO(value.split(".*")[4])){
                    return value.split(".*")[2] + " - " + value.split(".*")[1];
                }
            }
        }).result(function(event, value){
            $("#PurchaseRequestVendorId").val(value.toString().split(".*")[0]);
            $("#PurchaseRequestVendorName").val(value.toString().split(".*")[1]);
            $("#PurchaseRequestVendorName").attr('readonly', true);
            $("#searchVendorPO").hide();
            $("#deleteSearchVendorPO").show();
        });
        
        $("#PurchaseRequestOrderDate, #PurchaseRequestExpectedDeliveryDate").datepicker({
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true
        }).unbind("blur");
        
        $("#PurchaseRequestOrderDate").datepicker("option", "minDate", "<?php echo $dataClosingDate[0]; ?>");
        $("#PurchaseRequestOrderDate").datepicker("option", "maxDate", 0);
        
        $("#searchProductPO").click(function(){
            searchProductPO();
        });
        
        $(".addServicePO").click(function(){
            searchAllServicePO();
        });
        
        $(".productCodePO").focus(function(){
            $(".btnSavePurchaseOrder").attr('disabled','disabled');
        });
        
        $(".productCodePO").blur(function(){
            $(".btnSavePurchaseOrder").removeAttr('disabled');
        });
        
        
        $(".productCodePO").keyup(function(e){
            var currentTimestamp = new Date().getTime();
            var obj = $(this);
            if(currentTimestamp - timestamp < 50){
                if($(this).val().length >= 4 ){
                    if($("#PurchaseRequestCompanyId").val() ==""){
                        $(this).val('');
                        $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_SELECT_COMPANY_FIRST; ?></p>');
                        $("#dialog").dialog({
                            title: '<?php echo DIALOG_WARNING; ?>',
                            resizable: false,
                            modal: true,
                            width: 'auto',
                            height: 'auto',
                            open: function(event, ui){
                                $(".ui-dialog-buttonpane").show();
                            },
                            close: function(event, ui){

                            },
                            buttons: {
                                '<?php echo ACTION_CLOSE; ?>': function() {
                                    $(this).dialog("close");
                                }
                            }
                        });
                    }else{
                        if($(this).val() != ""){
                            if(pprTimeCode == 1){
                               pprTimeCode = 2;
                               serachProCodePO($(this).val(),'.productCodePO')
                            }
                        }
                    }
                }
            }
            timestamp = currentTimestamp;
        });
        
        $(".productCodePO").keypress(function(e){
            if((e.which && e.which == 13) || e.keyCode == 13){
                if(pprTimeCode == 1){
                    pprTimeCode = 2;
                    serachProCodePO($(this).val(),'.productCodePO');
                }
                return false;
            }
        });

        $(".productPO").keypress(function(e){
            var code =null;
            var obj = $(this);
            code = (e.keyCode ? e.keyCode : e.which);
            if (code == 13){
                if($("#PurchaseRequestCompanyId").val() ==""){
                    $(this).val('');
                    $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_SELECT_COMPANY_FIRST; ?></p>');
                    $("#dialog").dialog({
                        title: '<?php echo DIALOG_WARNING; ?>',
                        resizable: false,
                        modal: true,
                        width: 'auto',
                        height: 'auto',
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show();
                        },
                        close: function(event, ui){

                        },
                        buttons: {
                            '<?php echo ACTION_CLOSE; ?>': function() {
                                $(this).dialog("close");
                            }
                        }
                    });
                } else{
                    if($(this).val() != ""){
                        if(pprTimeCode == 1){
                            pprTimeCode = 2;
                            serachProCodePO($(this).val(),'.productPO');
                        }
                    }
                }
                return false;
            }
        });
        
        $(".btnBackPurchaseRequest").click(function(event){
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
                        backPruchaseOrder();
                    }
                }
            });
        });
        
        // Company Action
        $("#PurchaseRequestCompanyId").change(function(){
            var obj    = $(this);
            var vatCal = $(this).find("option:selected").attr("vat-opt");
            if($(".listBodyPO").find(".product_id").val() == undefined){
                $.cookie('companyIdPurchaseRequest', obj.val(), { expires: 7, path: "/" });
                $("#PurchaseRequestVatCalculate").val(vatCal);
                $("#PurchaseRequestBranchId").filterOptions('com', obj.val(), '');
                $("#PurchaseRequestBranchId").change();
                checkVatCompanyPO();
                resetFormPO();
                changeInputCSSPO();
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
                        $(".ui-dialog-buttonpane").show(); $(".ui-dialog-titlebar-close").show();
                    },
                    buttons: {
                        '<?php echo ACTION_OK; ?>': function() {
                            $.cookie('companyIdPurchaseRequest', obj.val(), { expires: 7, path: "/" });
                            $("#tblPO").html('');
                            $("#PurchaseRequestVatCalculate").val(vatCal);
                            $("#PurchaseRequestBranchId").filterOptions('com', obj.val(), '');
                            $("#PurchaseRequestBranchId").change();
                            checkVatCompanyPO();
                            resetFormPO();
                            changeInputCSSPO();
                            $(this).dialog("close");
                        },
                        '<?php echo ACTION_CANCEL; ?>': function() {
                            $("#PurchaseRequestCompanyId").val($.cookie("companyIdPurchaseRequest"));
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
        // Action Branch
        $("#PurchaseRequestBranchId").change(function(){
            var obj = $(this);
            if($(".listBodyPO").find(".product_id").val() == undefined){
                $.cookie('branchIdPurchaseRequest', obj.val(), { expires: 7, path: "/" });
                branchChangePurchaseRequest(obj);
                checkCurrencyWithBranchPO('');
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
                            $.cookie('branchIdPurchaseRequest', obj.val(), { expires: 7, path: "/" });
                            branchChangePurchaseRequest(obj);
                            checkCurrencyWithBranchPO('');
                            $("#tblPO").html('');
                            calcTotalPO();
                            $(this).dialog("close");
                        },
                        '<?php echo ACTION_CANCEL; ?>': function() {
                            $("#PurchaseRequestBranchId").val($.cookie("branchIdPurchaseRequest"));
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
        
        $("#PurchaseRequestCurrencyCenterId").change(function(){
            var currencySymbol = $(this).find("option:selected").attr("symbol");
            $(".lblSymbolPO").html(currencySymbol);
        });
        
        // Action VAT Status
        $("#PurchaseRequestVatSettingId").change(function(){
            checkVatSelectedPO();
            calcTotalPO();
        });
        
        // Button Change Info & Term
        <?php
        if($allowEditTerm){
        ?>
        $("#btnPOTermCon").click(function(){
            $("#POInformation").hide();
            $("#POTermCondition").show();
            $("#btnPOTermCon, #btnPOInfo").removeAttr('style');
            $("#btnPOTermCon").attr("style", "padding: 3px; background: #CCCCCC; font-weight: bold;");
            $("#btnPOInfo").attr("style", "padding: 3px; background: #CCCCCC;");
        });
        
        $("#btnPOInfo").click(function(){
            $("#POInformation").show();
            $("#POTermCondition").hide();
            $("#btnPOTermCon, #btnPOInfo").removeAttr('style');
            $("#btnPOTermCon").attr("style", "padding: 3px; background: #CCCCCC;");
            $("#btnPOInfo").attr("style", "padding: 3px; background: #CCCCCC; font-weight: bold;");
        });
        <?php
        }
        ?>
        checkVatCompanyPO('<?php echo $this->data['PurchaseRequest']['vat_setting_id']; ?>');
        checkCurrencyWithBranchPO('<?php echo $this->data['PurchaseRequest']['currency_center_id']; ?>');
        // Put label VAT Calculate
        changeLblVatCalPO();
    });
    
    function branchChangePurchaseRequest(obj){
        var mCode = obj.find("option:selected").attr("mcode");
        $("#PurchaseRequestPrCode").val('<?php echo date("y"); ?>'+mCode);
    }
    
    function changeLblVatCalPO(){
        var vatCal = $("#PurchaseRequestVatCalculate").val();
        $("#lblPurchaseRequestVatSettingId").unbind("mouseover");
        if(vatCal != ''){
            if(vatCal == 1){
                $("#lblPurchaseRequestVatSettingId").mouseover(function(){
                    Tip('<?php echo TABLE_VAT_BEFORE_DISCOUNT; ?>');
                });
            } else {
                $("#lblPurchaseRequestVatSettingId").mouseover(function(){
                    Tip('<?php echo TABLE_VAT_AFTER_DISCOUNT; ?>');
                });
            }
        }
    }
    
    function checkVatSelectedPO(){
        var vatPercent = replaceNum($("#PurchaseRequestVatSettingId").find("option:selected").attr("rate"));
        $("#PurchaseRequestVatPercent").val((vatPercent).toFixed(2));
    }
    
    function checkVatCompanyPO(selected){
        // VAT Filter
        $("#PurchaseRequestVatSettingId").filterOptions('com-id', $("#PurchaseRequestCompanyId").val(), selected);
    }
    
    function checkCompanyPO(companyId){
        var companyReturn = false;
        var companyPut    = companyId.split(",");
        var companySelect = $("#PurchaseRequestCompanyId").val();
        if(companyPut.indexOf(companySelect) != -1){
            companyReturn = true;
        }
        return companyReturn;
    }
    
    function checkCurrencyWithBranchPO(selected){
        var currency = $("#PurchaseRequestBranchId").find("option:selected").attr("currency");
        // Currency Filter
        $("#PurchaseRequestCurrencyCenterId").filterOptions('branch', $("#PurchaseRequestBranchId").val(), selected);
        $("#PurchaseRequestCurrencyCenterId").showHideDropdownOptions(currency, true);
    }
    
    function resetFormPO(){
        // Vendor
        $("#deleteSearchVendorPO").click();
    }
    
    function checkOrderDatePO(){
        if($("#PurchaseRequestOrderDate").val() == ""){
            $("#PurchaseRequestOrderDate").focus();
            return false;
        }else{
            return true;
        }
    }
    
    function codeDialogPO(){
        $("#dialog").html('<p style="color:red; font-size:14px;"><?php echo MESSAGE_CODE_ALREADY_EXISTS_IN_THE_SYSTEM; ?></p>');
        $("#dialog").dialog({
            title: '<?php echo DIALOG_INFORMATION; ?>',
            resizable: false,
            modal: true,
            width: 'auto',
            height: 'auto',
            pprsition:'center',
            open: function(event, ui){
                $(".ui-dialog-buttonpane").show();
            },
            close: function(){
                $(this).dialog({close: function(){}});
                $(this).dialog("close");
                $(".btnSavePurchaseOrder").removeAttr("disabled");
                $(".txtSavePO").html("<?php echo ACTION_SAVE; ?>");
            },
            buttons: {
                '<?php echo ACTION_CLOSE; ?>': function() {
                    $(this).dialog("close");
                    $(".btnSavePurchaseOrder").removeAttr("disabled");
                    $(".txtSavePO").html("<?php echo ACTION_SAVE; ?>");
                }
            }
        });
    }
    
    function errorSavePO(){
        $("#dialog").html('<p style="color:red; font-size:14px;"><?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?></p>');
        $("#dialog").dialog({
            title: '<?php echo DIALOG_INFORMATION; ?>',
            resizable: false,
            modal: true,
            width: 'auto',
            height: 'auto',
            pprsition:'center',
            open: function(event, ui){
                $(".ui-dialog-buttonpane").show();
            },
            close: function(){
                $(this).dialog({close: function(){}});
                $(this).dialog("close");
                backPruchaseOrder();
            },
            buttons: {
                '<?php echo ACTION_CLOSE; ?>': function() {
                    $(this).dialog("close");
                }
            }
        });
    }
    
    function errorSaveDepositPO(){
        $(".txtSavePO").html("<?php echo ACTION_SAVE; ?>");
        $(".btnSavePurchaseOrder").removeAttr('disabled');
        $("#dialog").html('<p style="color:red; font-size:14px;"><?php echo MESSAGE_TOTAL_AMOUNT_LESS_THAN_TOTAL_DEPOSIT; ?></p>');
        $("#dialog").dialog({
            title: '<?php echo DIALOG_INFORMATION; ?>',
            resizable: false,
            modal: true,
            width: 'auto',
            height: 'auto',
            pprsition:'center',
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
    
    function addNotePO(currentTr){
        var note = currentTr.closest("tr").find(".note");
        $("#dialog").html("<textarea style='width:350px; height: 200px;' id='noteCommentPO'>"+note.val()+"</textarea>").dialog({
            title: '<?php echo TABLE_NOTE; ?>',
            resizable: false,
            modal: true,
            width: 'auto',
            height: 'auto',
            pprsition:'center',
            open: function(event, ui){
                $(".ui-dialog-buttonpane").show();
            },
            buttons: {
                '<?php echo ACTION_OK; ?>': function() {
                    note.val($("#noteCommentPO").val());
                    $(this).dialog("close");
                }
            }
        });
    }
    
    function backPruchaseOrder(){
        $("#PurchaseRequestEditForm").validationEngine("hideAll");
        var rightPanel = $(".btnBackPurchaseRequest").parent().parent().parent().parent().parent();
        var leftPanel  = rightPanel.parent().find(".leftPanel");
        rightPanel.hide("slide", { direction: "right" }, 500, function(){
            leftPanel.show();
            rightPanel.html("");
        });
        leftPanel.html("<?php echo ACTION_LOADING; ?>");
        leftPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/index/");
    }
    
    function changeInputCSSPO(){
        var cssStyle  = 'inputDisable';
        var cssRemove = 'inputEnable';
        var readonly  = true;
        var disabled  = true;
        $(".searchVendorPO").hide();
        $("#divSearchPO").css("visibility", "hidden");
        var currencySymbol = $("#PurchaseRequestCurrencyCenterId").find("option:selected").attr("symbol");
        $(".lblSymbolPO").html(currencySymbol);
        if($("#PurchaseRequestCompanyId").val() != ''){
            cssStyle  = 'inputEnable';
            cssRemove = 'inputDisable';
            readonly  = false;
            disabled  = false;
            if($("#PurchaseRequestVendorName").val() == ''){
                $(".searchVendorPO").show();
            }
            $("#divSearchPO").css("visibility", "visible");
        }   
        // Label
        $("#PurchaseRequestEditForm").find("label").removeAttr("class");
        $("#PurchaseRequestEditForm").find("label").each(function(){
            var label = $(this).attr("for");
            if(label != 'PurchaseRequestCompanyId'){
                $(this).addClass(cssStyle);
            }
        });
        // Input & Select
        $("#PurchaseRequestEditForm").find("input").each(function(){
            $(this).removeClass(cssRemove);
            $(this).addClass(cssStyle);
        });
        $("#PurchaseRequestEditForm").find("select").each(function(){
            var selectId = $(this).attr("id");
            if(selectId != 'PurchaseRequestCompanyId'){
                $(this).removeClass(cssRemove);
                $(this).addClass(cssStyle);
                $(this).attr("disabled", disabled);
            }
        });
        $(".lblSymbolPO").removeClass(cssRemove);
        $(".lblSymbolPO").addClass(cssStyle);
        $(".lblSymbolPOPercent").removeClass(cssRemove);
        $(".lblSymbolPOPercent").addClass(cssStyle);
        // Input Readonly
        $("#PurchaseRequestVendorName").attr("readonly", readonly);
        $("#PurchaseRequestCurrency").attr("readonly", readonly);
        $("#PurchaseRequestRefPurchaseRequest").attr("readonly", readonly);
        $("#PurchaseRequestNote").attr("readonly", readonly);
        $("#productPurchaseCode").attr("readonly", readonly);
        $("#productPurchase").attr("readonly", readonly);
        // Put label VAT Calculate
        changeLblVatCalPO();
        // Check VAT Default
        getDefaultVatPO();
    }
    
    function getDefaultVatPO(){
        var vatDefault = $("#PurchaseRequestCompanyId option:selected").attr("vat-d");
        $("#PurchaseRequestVatSettingId option[value='"+vatDefault+"']").attr("selected", true);
        checkVatSelectedPO();
    }
    
    function showProductInfoPO(currentTr){
        var vendorId  = $("#PurchaseRequestVendorId").val();
        var productId = currentTr.closest("tr").find(".product_id").val();
        if(productId != ''){
            $.ajax({
                type:   "POST",
                url:    "<?php echo $this->base . "/purchase_requests/productHistory"; ?>/"+productId+"/"+vendorId,
                beforeSend: function(){
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(msg){
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $("#dialog").html(msg).dialog({
                        title: '<?php echo MENU_PRODUCT_MANAGEMENT_INFO; ?>',
                        resizable: false,
                        modal: true,
                        width: 1200,
                        height: 550,
                        position:'center',
                        closeOnEscape: true,
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show(); 
                            $(".ui-dialog-titlebar-close").show();
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
</script>
<?php echo $this->Form->create('PurchaseRequest'); ?>
<?php echo $this->Form->hidden('purchase_request_id', array('value'=>$id)); ?>
<?php echo $this->Form->hidden('old_total_deposit', array('value'=>$this->data['PurchaseRequest']['total_deposit'])); ?>
<input type="hidden" value="<?php echo $this->data['PurchaseRequest']['vat_calculate']; ?>" name="data[PurchaseRequest][vat_calculate]" id="PurchaseRequestVatCalculate" />
<div style="float: right; width: 165px; text-align: right; cursor: pointer;" id="btnHideShowHeaderPurchaseRequest">
    [<span>Hide</span> Header Information <img alt="" align="absmiddle" style="width: 16px; height: 16px;" src="<?php echo $this->webroot . 'img/button/arrow-up.png'; ?>" />]
</div>
<div style="clear: both;"></div>
<div id="divPOTop">
    <fieldset>
        <legend><a href="#" id="btnPOInfo" style="padding: 3px; background: #CCCCCC; font-weight: bold;"><?php __(MENU_PURCHASE_REQUEST_INFO); ?></a> <?php if($allowEditTerm){ ?>| <a href="#" id="btnPOTermCon" style="padding: 3px; background: #CCCCCC;"><?php __(TABLE_TERM_AND_CONDITION); ?></a><?php } ?></legend>
        <table cellpadding="3" cellspacing="0" style="width: 100%;" id="POInformation">
            <tr>
                <td style="width: 50%">
                    <table cellpadding="0" style="width: 100%">
                        <tr>
                            <td style="width: 34%"><label for="PurchaseRequestCompanyId"><?php echo TABLE_COMPANY; ?></label> <span class="red">*</span></td>
                            <td style="width: 33%"><label for="PurchaseRequestBranchId"><?php echo MENU_BRANCH; ?> <span class="red">*</span></label></td>
                            <td style="width: 33%"><label for="PurchaseRequestPrCode"><?php echo TABLE_PO_NUMBER; ?></label> <span class="red">*</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="inputContainer" style="width:100%">
                                    <?php 
                                    $btnDisabled = '';
                                    $venReadonly = true;
                                    if($this->data['PurchaseRequest']['total_deposit'] > 0){
                                        echo $this->Form->hidden('company_id');
                                        echo $this->data['Company']['name'];
                                        $btnDisabled = 'display: none;';
                                    } else {
                                    ?>
                                        <select name="data[PurchaseRequest][company_id]" id="PurchaseRequestCompanyId" class="validate[required]" style="width: 75%;">
                                            <?php
                                            if(count($companies) != 1){
                                            ?>
                                            <option vat-d="" value="" vat-opt=""><?php echo INPUT_SELECT; ?></option>
                                            <?php
                                            }
                                            foreach($companies AS $company){
                                                $sqlVATDefault = mysql_query("SELECT vat_modules.vat_setting_id FROM vat_modules INNER JOIN vat_settings ON vat_settings.company_id = ".$company['Company']['id']." AND vat_settings.is_active = 1 AND vat_settings.id = vat_modules.vat_setting_id WHERE vat_modules.is_active = 1 AND vat_modules.apply_to = 48 GROUP BY vat_modules.vat_setting_id LIMIT 1");
                                                $rowVATDefault = mysql_fetch_array($sqlVATDefault);
                                            ?>
                                            <option vat-d="<?php echo $rowVATDefault[0]; ?>" <?php if($company['Company']['id'] == $this->data['PurchaseRequest']['company_id']){ ?>selected="selected"<?php } ?> value="<?php echo $company['Company']['id']; ?>" vat-opt="<?php echo $company['Company']['vat_calculate']; ?>"><?php echo $company['Company']['name']; ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </td>
                            <td>
                                <div class="inputContainer" style="width:100%">
                                    <select name="data[PurchaseRequest][branch_id]" id="PurchaseRequestBranchId" class="validate[required]" style="width: 75%;">
                                        <?php
                                        if(count($branches) != 1){
                                        ?>
                                        <option value="" com="" mcode="" currency="" symbol=""><?php echo INPUT_SELECT; ?></option>
                                        <?php
                                        }
                                        foreach($branches AS $branch){
                                        ?>
                                        <option <?php if($branch['Branch']['id'] == $this->data['PurchaseRequest']['branch_id']){ ?>selected="selected"<?php } ?> value="<?php echo $branch['Branch']['id']; ?>" com="<?php echo $branch['Branch']['company_id']; ?>" mcode="<?php echo $branch['ModuleCodeBranch']['po_code']; ?>" currency="<?php echo $branch['Branch']['currency_center_id']; ?>" symbol="<?php echo $branch['CurrencyCenter']['symbol']; ?>"><?php echo $branch['Branch']['name']; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div> 
                            </td>
                            <td>
                                <div class="inputContainer" style="width:100%">
                                    <?php echo $this->Form->text('pr_code', array('class' => 'validate[required]', 'value' => $this->data['PurchaseRequest']['pr_code'], 'style' => 'width:70%', 'readonly' => $venReadonly)); ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td rowspan="2">
                    <table cellpadding="0" cellspacing="0" style="width: 100%">
                        <tr>
                            <td style="width: 34%;"><label for="PurchaseRequestOrderDate"><?php echo TABLE_PO_DATE; ?></label> <span class="red">*</span></td>
                            <td style="width: 33%;"></td>
                            <td style="width: 33%;"><label for="PurchaseRequestNote"><?php echo TABLE_NOTE; ?></label></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">
                                <div class="inputContainer" style="width:100%">
                                    <?php echo $this->Form->text('order_date', array('class' => 'validate[required]', 'readonly' => 'readonly', 'style' => 'width:70%', 'value' => dateShort($this->data['PurchaseRequest']['order_date']))); ?>
                                </div>
                            </td>
                            <td></td>
                            <td rowspan="3" style=" vertical-align: top;">
                                <?php echo $this->Form->input('note', array('style' => 'width:90%; height: 70px;', 'label' => false)); ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 34%; padding-top: 14px;"></td>
                            <td style="width: 33%;"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                </td>
            </tr> 
            <tr>
                <td>
                    <table cellpadding="0" style="width: 100%">
                        <tr>
                            <td style="width: 34%"><label for="PurchaseRequestVendorName"><?php echo TABLE_VENDOR; ?> <span class="red">*</span></label></td>
                            <td style="width: 33%"><label for="PurchaseRequestCurrencyCenterId"><?php echo TABLE_CURRENCY; ?> <span class="red">*</span></label></td>
                            <td style="width: 33%"><label for="PurchaseRequestRefPurchaseRequest"><?php echo TABLE_REF_QUOTATION; ?></label></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="inputContainer" style="width:100%">
                                    <?php echo $this->Form->hidden('vendor_id', array('value' => $this->data['Vendor']['id'])); ?>
                                    <?php echo $this->Form->text('vendor_name', array('class' => 'validate[required,funcCall[checkVendorPO]]', 'style' => 'width:70%', 'value' => $this->data['Vendor']['name'], 'readonly' => $venReadonly)); ?>
                                    &nbsp;&nbsp; <img alt="Search" align="absmiddle" style="cursor: pointer; width:22px; height: 22px; display: none;" id="searchVendorPO" onmouseover="Tip('<?php echo GENERAL_SEARCH; ?>')" src="<?php echo $this->webroot . 'img/button/search.png'; ?>" />
                                    <img alt="Delete" align="absmiddle" id="deleteSearchVendorPO" onmouseover="Tip('<?php echo ACTION_DELETE; ?>')" src="<?php echo $this->webroot . 'img/button/delete.png'; ?>" style="cursor: pointer; <?php echo $btnDisabled; ?>" />
                                </div>
                            </td>
                            <td>
                                <div class="inputContainer" style="width:100%">
                                    <select name="data[PurchaseRequest][currency_center_id]" id="PurchaseRequestCurrencyCenterId" class="validate[required]" style="width: 75%;">
                                        <option company="" value="" symbol=""><?php echo INPUT_SELECT; ?></option>
                                        <?php
                                        foreach($currencyCenters AS $currencyCenter){
                                            $sqlCurrencyCom = mysql_query("SELECT GROUP_CONCAT(branch_id) FROM branch_currencies WHERE branch_currencies.is_active = 1 AND branch_currencies.currency_center_id = ".$currencyCenter['CurrencyCenter']['id']);
                                            $rowCurrencyCom = mysql_fetch_array($sqlCurrencyCom);
                                        ?>
                                        <option <?php if($this->data['PurchaseRequest']['currency_center_id'] == $currencyCenter['CurrencyCenter']['id']){ ?>selected="selected"<?php } ?> branch="<?php echo $rowCurrencyCom[0]; ?>" value="<?php echo $currencyCenter['CurrencyCenter']['id']; ?>" symbol="<?php echo $currencyCenter['CurrencyCenter']['symbol']; ?>"><?php echo $currencyCenter['CurrencyCenter']['name']; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="inputContainer" style="width:100%">
                                    <?php 
                                    echo $this->Form->text('ref_quotation', array('style' => 'width:70%')); 
                                    ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table id="POTermCondition" cellpadding="0" cellspacing="0" style="width: 100%; display: none;">
            <tr>
                <td>
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <tr>
                            <td style=" vertical-align: top; height: 110px;">
                                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                                    <?php
                                    // Copy Must Change module type id in SQL Comment
                                    $termForms = array();
                                    $sqlTermApply = mysql_query("SELECT * FROM term_condition_applies WHERE is_active = 1 AND module_type_id = 48 GROUP BY module_type_id, term_condition_type_id ORDER BY id");
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
                                            $sqlQTerm1 = mysql_query("SELECT term_condition_id FROM purchase_request_term_conditions WHERE purchase_request_id = ".$this->data['PurchaseRequest']['id']." AND term_condition_type_id =".$termData1[0]." LIMIT 1");
                                            $rowQTerm1 = mysql_fetch_array($sqlQTerm1);
                                            $termTypeId1 = $termData1[0];
                                            $title1  = getTermType($termData1[0]);
                                            $input1  = getTermOption($termData1[0], $rowQTerm1[0]);
                                        }
                                        if(!empty($termForm[1])){
                                            $sqlQTerm2 = mysql_query("SELECT term_condition_id FROM purchase_request_term_conditions WHERE purchase_request_id = ".$this->data['PurchaseRequest']['id']." AND term_condition_type_id =".$termData2[0]." LIMIT 1");
                                            $rowQTerm2 = mysql_fetch_array($sqlQTerm2);
                                            $termTypeId2 = $termData2[0];
                                            $title2  = getTermType($termData2[0]);
                                            $input2  = getTermOption($termData2[0], $rowQTerm2[0]);
                                        }
                                        if(!empty($termForm[2])){
                                            $sqlQTerm3 = mysql_query("SELECT term_condition_id FROM purchase_request_term_conditions WHERE purchase_request_id = ".$this->data['PurchaseRequest']['id']." AND term_condition_type_id =".$termData3[0]." LIMIT 1");
                                            $rowQTerm3 = mysql_fetch_array($sqlQTerm3);
                                            $termTypeId3 = $termData3[0];
                                            $title3  = getTermType($termData3[0]);
                                            $input3  = getTermOption($termData3[0], $rowQTerm3[0]);
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
</div>
<div class="inputContainer" style="width:100%" id="searchFormPO">
    <table width="100%">
        <tr>
            <td style="width: 300px;">
                <input type="text" id="productPurchaseCode" class="productCodePO" style="width: 90%; height:15px;" placeholder="<?php echo TABLE_SCAN_ENTER_UPC; ?>" />
            </td>
            <td style="width: 300px">
                <input type="text" id="productPurchase" class="productPO" style="width: 90%; height: 15px;" placeholder="<?php echo TABLE_SEARCH_SKU_NAME; ?>" />
            </td>
            <td id="divSearchPO">
                <img alt="Search" align="absmiddle" style="cursor: pointer;" id="searchProductPO" onmouseover="Tip('<?php echo GENERAL_SEARCH; ?>')" src="<?php echo $this->webroot . 'img/button/search.png'; ?>" />
                <?php
                if ($allowAddService) {
                ?>
                <img alt="<?php echo SALES_ORDER_ADD_SERVICE; ?>" style="cursor: pointer;" align="absmiddle" class="addServicePO" onmouseover="Tip('<?php echo SALES_ORDER_ADD_SERVICE; ?>')" src="<?php echo $this->webroot . 'img/button/service.png'; ?>" /> 
                <?php
                }
                ?>
            </td>
        </tr>
    </table>
</div>
<table id="tblHeaderPO" class="table" cellspacing="0" style="padding:0px; width:99%;">
    <tr>
        <th class="first" style="width:5%"><?php echo TABLE_NO; ?></th>
        <th style="width:24%"><?php echo TABLE_PRODUCT_NAME; ?></th>
        <th style="width:10%"><?php echo TABLE_QTY; ?></th>
        <th style="width:13%"><?php echo TABLE_UOM; ?></th>
        <th style="width:13%"><?php echo TABLE_UNIT_COST . TABLE_CURRENCY_DEFAULT ?></th>
        <th style="width:14%"><?php echo TABLE_TOTAL_COST . TABLE_CURRENCY_DEFAULT ?></th>
        <th style="width:10%"></th>
    </tr>
</table>
<div id="bodyListPO">
    <table id="tblPO" class="table" cellspacing="0" style="padding:0px;">
        <tr id="detailPO" class="listBodyPO" style="visibility: hidden;">
            <td class="first" style="width:5%"></td>
            <td style="width:24%">
                <div class="inputContainer" style="width:100%">
                    <input type="hidden" name="product_id[]" class="product_id" id="product_id" />
                    <input type="hidden" name="service_id[]" class="service_id" id="service_id" />
                    <input type="hidden" id="note" name="note[]" class="note" />
                    <input type="text" id="product_name" name="product_name[]" readonly="readonly" class="product_name validate[required]" style="width: 75%" />
                    <img alt="Note" src="<?php echo $this->webroot . 'img/button/note.png'; ?>" class="noteAddPO" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Note')" />
                    <img alt="Information" src="<?php echo $this->webroot . 'img/button/view.png'; ?>" class="btnProductInfo" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Information')" />
                </div>
            </td>
            <td style="padding:0px; text-align: center; width:10%">
                <div class="inputContainer" style="width:100%">
                    <input type="text" id="qty" name="qty[]" style="width:75%;"  class="qty validate[required,min[1]]" />
                </div>
            </td>
            <td style="padding:0px; text-align: center; width:13%">
                <div class="inputContainer" style="width:100%">
                    <input type="hidden" class="smallUomValPro" />
                    <input type="hidden" name="prr_conversion[]" class="prr_conversion" />                                       
                    <select id="qty_uom_id" name="qty_uom_id[]" style="width:90%; height: 20px;" class="qty_uom_id validate[required]">
                        <?php
                        foreach ($uoms as $uom) {
                            echo "<option value='{$uom['Uom']['id']}' conversion='1'>{$uom['Uom']['name']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </td>
            <td style="padding:0px; text-align: center; width:13%">
                <div class="inputContainer" style="width:100%">
                    <input type="hidden" class="defaltCost" name="default_cost[]" />
                    <input type="hidden" class="tmp_unit_cost" value="0" />
                    <input type="text" id="unit_cost" name="unit_cost[]" <?php if(!$allowEditCost){ ?>readonly=""<?php } ?> class="unit_cost validate[required] float" style="width:80%" />
                </div>
            </td>
            <td style="padding:0px; text-align: center; width:14%">
                <input type="hidden" id="h_total_cost" class="h_total_cost float" name="h_total_cost[]" />
                <input type="text" name="total_cost[]" <?php if(!$allowEditCost){ ?>readonly=""<?php } ?> id="total_cost" style="width:80%" class="total_cost float" />
                
            </td>
            <td style="white-space: nowrap; padding:0px; text-align: center; width:10%">
                <img alt="" src="<?php echo $this->webroot . 'img/button/cross.png'; ?>" class="btnRemovePO" style="cursor: pointer;" onmouseover="Tip('Remove')" />
                &nbsp; <img alt="Up" src="<?php echo $this->webroot . 'img/button/move_up.png'; ?>" class="btnMoveUpPO" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Up')" />
                &nbsp; <img alt="Down" src="<?php echo $this->webroot . 'img/button/move_down.png'; ?>" class="btnMoveDownPO" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Down')" />
            </td>
        </tr>
        <?php
        if($time != 2){
            $i=0; 
            foreach ($purchaseRequestDetails as $orderDetail){
            $i++; 
        ?>
        <tr class="listBodyPO">
            <td class="first" style="width:5%"><?php echo $i; ?></td>
            <td style="width:24%">
                <div class="inputContainer" style="width:100%">
                    <input type="hidden" name="product_id[]" value="<?php echo $orderDetail['Product']['id'];?>" class="product_id" />
                    <input type="hidden" name="service_id[]" value="" class="service_id" />
                    <input type="hidden" id="note" name="note[]" class="note" value="<?php echo $orderDetail['PurchaseRequestDetail']['note']; ?>" />
                    <?php echo $this->Form->input('product_name', array('name'=>'product_name[]','id'=>'product_name'.$i, 'label' => false, 'value' => $orderDetail['Product']['code']." - ".$orderDetail['Product']['name'], 'class'=>'product_name validate[required]', 'style'=>'width:75%' , 'div'=>false));?>
                    <img alt="Note" id="noteAddPO<?php echo $i;?>" src="<?php echo $this->webroot . 'img/button/note.png'; ?>" class="noteAddPO" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Note')" />
                    <img alt="Information" src="<?php echo $this->webroot . 'img/button/view.png'; ?>" class="btnProductInfo" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Information')" />
                </div>
            </td>
            <td style="padding:0px; text-align: center; width:10%">
                <?php echo $this->Form->input('qty', array('id'=>'qty_'.$i,'name'=>'qty[]', 'label' => false, 'value' => number_format($orderDetail['PurchaseRequestDetail']['qty'], 0), 'class'=>'qty float validate[required,min[1]]', 'style'=>'width:75%'));?>
            </td>
            <td style="padding:0px; text-align: center; width:13%">
                <div class="input text">                                  
                    <input type="hidden" value="<?php echo $orderDetail['Product']['small_val_uom'];?>" class="smallUomValPro" />                                                         
                    <input type="hidden" value="<?php echo $orderDetail['PurchaseRequestDetail']['conversion'];?>" name="prr_conversion[]" class="prr_conversion" />
                    <select name="qty_uom_id[]" class="qty_uom_id validate[required]" id="qty_uom_id<?php echo $i;?>" style="width:90%; height: 20px;" base="1">
                    <?php
                            $queryUom=mysql_query("SELECT id,name,abbr,1 AS conversion FROM uoms WHERE id=".$orderDetail['Product']['price_uom_id']."
                                                   UNION
                                                   SELECT id,name,abbr,(SELECT value FROM uom_conversions WHERE is_active=1 AND from_uom_id=".$orderDetail['Product']['price_uom_id']." AND to_uom_id=uoms.id) AS conversion FROM uoms WHERE id IN (SELECT to_uom_id FROM uom_conversions WHERE is_active=1 AND from_uom_id=".$orderDetail['Product']['price_uom_id'].")
                                                   ORDER BY conversion ASC");
                            $k = 1;
                            $length = mysql_num_rows($queryUom);
                            while($dataUom=mysql_fetch_array($queryUom)){?>
                            <option data-sm="<?php if($length == $k){ ?>1<?php }else{ ?>0<?php } ?>" data-item="<?php if($dataUom['id'] == $orderDetail['Product']['price_uom_id']){ echo "first"; }else{ echo "other";} ?>" value="<?php echo $dataUom['id']; ?>" <?php if($dataUom['id']==$orderDetail['PurchaseRequestDetail']['qty_uom_id']){ echo 'selected'; }else{ echo ''; };?> conversion="<?php echo $dataUom['conversion']; ?>"><?php echo $dataUom['name']; ?></option>
                    <?php 
                            $k++;
                            } 
                    ?>
                    </select>
                </div>
            </td>
            <td style="padding:0px; text-align: center; width:13%">
                <input type="hidden" class="defaltCost" value="<?php echo $orderDetail['PurchaseRequestDetail']['unit_cost'];?>" name="default_cost[]" />
                <input type="hidden" class="tmp_unit_cost" value="<?php echo $orderDetail['PurchaseRequestDetail']['unit_cost']; ?>" />
                <?php
                $readonly = false;
                if(!$allowEditCost){
                    $readonly = true;
                }
                echo $this->Form->input('unit_cost', array('id'=>'unit_cost'.$i, 'name'=>'unit_cost[]', 'readonly' => $readonly, 'label' => false, 'value'=>number_format($orderDetail['PurchaseRequestDetail']['unit_cost'], $rowOption[0]), 'class'=>'unit_cost validate[required] float', 'style'=>'width:80%;'));?>
            </td>
            <td style="padding:0px; text-align: center; width:14%">
                <input type="hidden" name="h_total_cost[]" id="h_total_cost<?php echo $i; ?>" style="width:80%" class="h_total_cost" value="<?php echo $orderDetail['PurchaseRequestDetail']['total_cost']; ?>" />
                <?php echo $this->Form->input('total_cost', array('id'=>'total_cost'.$i, 'readonly' => $readonly, 'label' => false, 'name'=>'total_cost[]', 'value'=>number_format($orderDetail['PurchaseRequestDetail']['total_cost'], $rowOption[0]), 'class'=>'total_cost float', 'style'=>'width:80%'));?>
            </td>
            <td style="white-space: nowrap; padding:0px; text-align: center; width:10%">
                <img alt="" src="<?php echo $this->webroot . 'img/button/cross.png'; ?>" class="btnRemovePO" style="cursor: pointer;" onmouseover="Tip('Remove')" />
                &nbsp; <img alt="Up" src="<?php echo $this->webroot . 'img/button/move_up.png'; ?>" class="btnMoveUpPO" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Up')" />
                &nbsp; <img alt="Down" src="<?php echo $this->webroot . 'img/button/move_down.png'; ?>" class="btnMoveDownPO" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Down')" />
            </td>
        </tr>
        <?php 
            }
            foreach ($purchaseRequestServices as $purchaseRequestService){
                $uomName = 'None';
                $uomVal  = 1;
                if($purchaseRequestService['Service']['uom_id'] != ''){
                    $sqlUom = mysql_query("SELECT abbr FROM uoms WHERE id = ".$purchaseRequestService['Service']['uom_id']);
                    $rowUom = mysql_fetch_array($sqlUom);
                    $uomName = $rowUom[0];
                    $uomVal  = $purchaseRequestService['Service']['uom_id'];
                }
                $i++; 
        ?>
        <tr class="listBodyPO">
            <td class="first" style="width:5%"><?php echo $i; ?></td>
            <td style="width:24%">
                <div class="inputContainer" style="width:100%">
                    <input type="hidden" name="product_id[]" value="" class="product_id" />
                    <input type="hidden" name="service_id[]" value="<?php echo $purchaseRequestService['Service']['id'];?>" class="service_id" />
                    <input type="hidden" id="note" name="note[]" class="note" value="<?php echo $purchaseRequestService['PurchaseRequestService']['note']; ?>" />
                    <?php echo $this->Form->input('product_name', array('name'=>'product_name[]','id'=>'product_name'.$i, 'label' => false, 'value' => $purchaseRequestService['Service']['code']." - ".$purchaseRequestService['Service']['name'], 'class'=>'product_name validate[required]', 'style'=>'width:75%' , 'div'=>false));?>
                    <img alt="Note" id="noteAddPO<?php echo $i;?>" src="<?php echo $this->webroot . 'img/button/note.png'; ?>" class="noteAddPO" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Note')" />
                </div>
            </td>
            <td style="padding:0px; text-align: center; width:10%">
                <?php echo $this->Form->input('qty', array('id'=>'qty_'.$i,'name'=>'qty[]', 'label' => false, 'value' => number_format($purchaseRequestService['PurchaseRequestService']['qty'], 0), 'class'=>'qty float validate[required,min[1]]', 'style'=>'width:75%'));?>
            </td>
            <td style="padding:0px; text-align: center; width:13%">
                <div class="input text">                                  
                    <input type="hidden" value="1" class="smallUomValPro" />                                                         
                    <input type="hidden" value="1" name="prr_conversion[]" class="prr_conversion" />
                    <select name="qty_uom_id[]" class="qty_uom_id" id="qty_uom_id<?php echo $i;?>" style="width:90%; height: 20px; <?php if($uomName == 'None'){ ?>visibility: hidden;<?php } ?>">
                        <option value="<?php echo $uomVal; ?>" conversion="1" selected="selected"><?php echo $uomName;?></option>
                    </select>
                </div>
            </td>
            <td style="padding:0px; text-align: center; width:13%">
                <input type="hidden" class="defaltCost" value="<?php echo $purchaseRequestService['PurchaseRequestService']['unit_cost'];?>" name="default_cost[]" />
                <input type="hidden" class="tmp_unit_cost" value="<?php echo $purchaseRequestService['PurchaseRequestService']['unit_cost']; ?>" />
                <?php
                $readonly = false;
                if(!$allowEditCost){
                    $readonly = true;
                }
                echo $this->Form->input('unit_cost', array('id'=>'unit_cost'.$i, 'name'=>'unit_cost[]', 'readonly' => $readonly, 'label' => false, 'value'=>number_format($purchaseRequestService['PurchaseRequestService']['unit_cost'], $rowOption[0]), 'class'=>'unit_cost validate[required] float', 'style'=>'width:80%;'));?>
            </td>
            <td style="padding:0px; text-align: center; width:14%">
                <input type="hidden" name="h_total_cost[]" id="h_total_cost<?php echo $i; ?>" style="width:80%" class="h_total_cost" value="<?php echo $purchaseRequestService['PurchaseRequestService']['total_cost']; ?>" />
                <?php echo $this->Form->input('total_cost', array('id'=>'total_cost'.$i, 'readonly' => $readonly, 'label' => false, 'name'=>'total_cost[]', 'value'=>number_format($purchaseRequestService['PurchaseRequestService']['total_cost'], $rowOption[0]), 'class'=>'total_cost float', 'style'=>'width:80%'));?>
            </td>
            <td style="white-space: nowrap; padding:0px; text-align: center; width:10%">
                <img alt="" src="<?php echo $this->webroot . 'img/button/cross.png'; ?>" class="btnRemovePO" style="cursor: pointer;" onmouseover="Tip('Remove')" />
                &nbsp; <img alt="Up" src="<?php echo $this->webroot . 'img/button/move_up.png'; ?>" class="btnMoveUpPO" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Up')" />
                &nbsp; <img alt="Down" src="<?php echo $this->webroot . 'img/button/move_down.png'; ?>" class="btnMoveDownPO" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Down')" />
            </td>
        </tr>
        <?php
            }
        }
        ?>
    </table>
</div>
<div id="tblFooterPO">
    <div style="float: left; width: 15%;">
        <div class="buttons">
            <a href="#" class="positive btnBackPurchaseRequest">
                <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
                <?php echo ACTION_BACK; ?>
            </a>
        </div>
        <div class="buttons">
            <button type="submit" class="positive btnSavePurchaseOrder">
                <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
                <span class="txtSavePO"><?php echo ACTION_SAVE; ?></span>
            </button>
        </div>
    </div>
    <div style="float: right; width:83%; vertical-align: bottom;" id="amountPaid">
        <table cellpadding="0" style="width:100%; padding: 0px; margin: 0px;">
            <tr>
                <td style="width:15%; text-align: right;"><label for="PurchaseRequestTotalAmount"><?php echo TABLE_SUB_TOTAL; ?>:</label></td>
                <td style="width:18%">
                    <?php echo $this->Form->text('total_amount', array('readonly' => true, 'style' => 'width: 80%; height:15px; font-size:12px; font-weight: bold', 'value' => number_format($this->data['PurchaseRequest']['total_amount'], $rowOption[0]))); ?> <span class="lblSymbolPO"><?php echo $this->data['CurrencyCenter']['symbol']; ?></span>
                </td>
                <td style="width:23%; text-align: right;">
                    <label for="PurchaseRequestVatSettingId" id="lblPurchaseRequestVatSettingId"><?php echo TABLE_VAT; ?> <span class="red">*</span>:</label>
                    <select id="PurchaseRequestVatSettingId" name="data[PurchaseRequest][vat_setting_id]" style="width: 75%;" class="validate[required]">
                        <option com-id="" value="" rate="0.00"><?php echo INPUT_SELECT; ?></option>
                        <?php
                        // VAT
                        $sqlVat = mysql_query("SELECT id, name, vat_percent, company_id FROM vat_settings WHERE is_active = 1 AND type = 2 AND company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id'].");");
                        while($rowVat = mysql_fetch_array($sqlVat)){
                        ?>
                        <option com-id="<?php echo $rowVat['company_id']; ?>" <?php if($this->data['PurchaseRequest']['vat_setting_id'] == $rowVat['id']){ ?>selected="selected"<?php } ?> value="<?php echo $rowVat['id']; ?>" rate="<?php echo $rowVat['vat_percent']; ?>"><?php echo $rowVat['name']; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </td>
                <td style="width:10%">
                    <input type="hidden" id="PurchaseRequestTotalVat" name="data[PurchaseRequest][total_vat]" value="<?php echo $this->data['PurchaseRequest']['total_vat']; ?>" />
                    <?php echo $this->Form->text('vat_percent', array('readonly' => true, 'style' => 'width: 50%; height:15px; font-size:12px; font-weight: bold', 'value' => number_format($this->data['PurchaseRequest']['vat_percent'], 2))); ?> <span class="lblSymbolPOPercent">(%)</span>
                </td>
                <td style="width:15%; text-align: right;"><label for="PurchaseRequestTotalAmountAll"><?php echo TABLE_TOTAL; ?>:</label></td>
                <td style="width:19%">
                    <?php echo $this->Form->text('total_amount_all', array('readonly' => true, 'style' => 'width: 80%; height:15px; font-size:12px; font-weight: bold' , 'value' => number_format(($this->data['PurchaseRequest']['total_amount'] + $this->data['PurchaseRequest']['total_vat']), $rowOption[0]))); ?> <span class="lblSymbolPO"><?php echo $this->data['CurrencyCenter']['symbol']; ?></span>
                </td>
            </tr>
        </table>
    </div>
    <div style="clear: both;"></div>
</div>
<?php echo $this->Form->end(); ?>