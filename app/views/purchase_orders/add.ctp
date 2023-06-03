<?php 
// Get Decimal
$sqlOption = mysql_query("SELECT product_cost_decimal FROM setting_options");
$rowOption = mysql_fetch_array($sqlOption);

// Check Permission
$this->element('check_access');
$allowAddService  = checkAccess($user['User']['id'], $this->params['controller'], 'service');
$allowAddMisc     = checkAccess($user['User']['id'], $this->params['controller'], 'miscellaneous');
$allowDiscount    = checkAccess($user['User']['id'], $this->params['controller'], 'discount');
$allowEditInvDis  = checkAccess($user['User']['id'], $this->params['controller'], 'invoiceDiscount');
$allowShowUnitCost  = checkAccess($user['User']['id'], $this->params['controller'], 'showUnitCost');
$allowAddProduct  = checkAccess($user['User']['id'], 'products', 'quickAdd');
$allowAddVendor   = checkAccess($user['User']['id'], 'vendors', 'quickAdd');
// Prevent Button Submit
echo $this->element('prevent_multiple_submit');
$queryClosingDate = mysql_query("SELECT DATE_FORMAT(date,'%d/%m/%Y') FROM account_closing_dates ORDER BY id DESC LIMIT 1");
$dataClosingDate  = mysql_fetch_array($queryClosingDate);
$sqlSettingUomDeatil = mysql_query("SELECT uom_detail_option, calculate_cogs FROM setting_options");
$rowSettingUomDetail = mysql_fetch_array($sqlSettingUomDeatil);
?>
<script type="text/javascript">
    var indexRowPB   = 0;
    var cloneRowPB   = $("#detailPB");
    var poTimeCode   = 1;
    
    function resizeFormTitlePB(){
        var screen = 16;
        var widthList = $("#bodyListPB").width();
        var widthTitle = widthList - screen;
        $("#tblHeaderPB").css('padding', '0px');
        $("#tblHeaderPB").css('margin-top', '5px');
        $("#tblHeaderPB").css('width', widthTitle);
    }
    
    function resizeFornScrollPB(){
        var tabHeight = $(tabPBId).height();
        var formHeader = 0;
        if ($('#PBTop').is(':hidden')) {
            formHeader = 0;
        } else {
            formHeader = $("#PBTop").height();
        }
        var btnHeader   = $("#btnHideShowHeaderPurchaseBill").height();
        var formFooter  = $("#POFooter").height();
        var formSearch  = $("#searchFormPB").height();
        var tableHeader = $("#tblHeaderPB").height();
        var widthList   = $("#bodyListPB").width();
        var getHeight   = tabHeight - (formHeader + btnHeader + tableHeader + formSearch + formFooter);
        $("#bodyListPB").css('height', getHeight);
        $("#bodyListPB").css('padding', '0px');
        $("#bodyListPB").css('width', widthList);
        $("#bodyListPB").css('overflow-x', 'hidden');
        $("#bodyListPB").css('overflow-y', 'scroll');
    }
    
    function refreshScreenPB(){
        $("#tblHeaderPB").removeAttr('style');
        var windowWidth  = $(window).width();
        if(windowWidth <= '1024'){
            $(".productPOCode").css('width','40%');
        }else{
            $(".productPOCode").css('width','50%');
        }
    }
    
    function calcTotalPB(){
        var totalSubAmount = 0;
        var totalVat       = 0;
        var totalAmount    = 0;
        var totalDiscount    = replaceNum($("#PurchaseOrderDiscountAmount").val());
        var totalDisPercent  = replaceNum($("#PurchaseOrderDiscountPercent").val());
        var totalVatPercent  = replaceNum($("#PurchaseOrderVatPercent").val());
        var vatCal           = $("#PurchaseOrderVatCalculate").val();
        var totalBfDis       = 0;
        var totalAmtCalVat   = 0;
        $(".listBodyPB").find(".total_cost").each(function(){
            totalSubAmount += replaceNum($(this).val());
        });
        if(totalDisPercent > 0){
            totalDiscount  = replaceNum(converDicemalJS((totalSubAmount * totalDisPercent) / 100).toFixed(<?php echo $rowOption[0]; ?>));
        }
        totalAmtCalVat = replaceNum(converDicemalJS(totalSubAmount - totalDiscount));
        if(vatCal == 1){
            $(".listBodyPB").each(function(){
                var qty   = replaceNum($(this).find(".qty").val()) + replaceNum($(this).find(".qty_free").val());
                var price = replaceNum($(this).find(".unit_cost").val());
                totalBfDis += replaceNum(converDicemalJS(qty * price));
            });
            totalAmtCalVat = totalBfDis;
        }
        totalVat = replaceNum(converDicemalJS((totalAmtCalVat * totalVatPercent) / 100).toFixed(<?php echo $rowOption[0]; ?>));
        totalAmount = converDicemalJS(replaceNum(totalSubAmount - totalDiscount) + replaceNum(totalVat));
        $("#PurchaseOrderTotalAmount").val((parseFloat(totalSubAmount)).toFixed(<?php echo $rowOption[0]; ?>));
        $("#PurchaseOrderDiscountAmount").val((parseFloat(totalDiscount)).toFixed(<?php echo $rowOption[0]; ?>));
        $("#PurchaseOrderTotalVat").val((parseFloat(totalVat)).toFixed(<?php echo $rowOption[0]; ?>));
        $("#PurchaseOrderGrandTotalAmount").val((parseFloat(totalAmount)).toFixed(<?php echo $rowOption[0]; ?>));
    }
    
    function addServicePB(service_id, name, unit_cost, serviceCode, uomId){
        indexRowPB = Math.floor((Math.random() * 100000) + 1);
        var serviceID           = service_id;
        var productName         = name;
        var tr = cloneRowPB.clone(true);
        tr.removeAttr("style").removeAttr("id");
        tr.find("td:eq(0)").html(indexRowPB);
        tr.find("td .purchaseUPC").val(serviceCode);
        tr.find("td .product_id").attr("id", "product_id"+indexRowPB).val('');
        tr.find("td .btnProductInfo").remove();
        tr.find("td .service_id").attr("id", "service_id"+indexRowPB).val(serviceID);
        tr.find("td .product_name").attr("id", "product_name"+indexRowPB).val(productName);
        tr.find("td .qty").attr("id", "qty_"+indexRowPB).val(1);
        tr.find("td .unit_cost").attr("id", "unit_cost"+indexRowPB).val(unit_cost);
        tr.find("td .defaltCost").val(unit_cost);
        tr.find("td .total_cost").attr("id", "total_cost"+indexRowPB).val(unit_cost);
        tr.find("td .h_total_cost").attr("id", "h_total_cost"+indexRowPB).val(unit_cost);
        tr.find("td .note").attr("id", "note"+indexRowPB).val("");
        tr.find("td .discountPB").attr("id", "discountPB"+indexRowPB).val(0);
        tr.find("td .lots_number").attr("id", "lots_number"+indexRowPB).removeAttr('class').css('visibility', 'hidden');
        tr.find("td .date_expired").attr("id", "date_expired"+indexRowPB).removeAttr('class').css('visibility', 'hidden');
        if(uomId == ''){
            tr.find("select[name='qty_uom_id[]']").attr("id", "qty_uom_id_"+indexRowPB).html('<option value="1" conversion="1" selected="selected">None</option>').css('visibility', 'hidden');
        } else {
            tr.find("select[name='qty_uom_id[]']").attr("id", "qty_uom_id_"+indexRowPB).find("option[value='"+uomId+"']").attr("selected", true);
            tr.find("select[name='qty_uom_id[]']").find("option[value!='"+uomId+"']").hide();
        }
        $("#tblPB").append(tr);
        tr.find("td .qty").select().focus();
        
        poTimeCode = 1;
        setIndexRowPB();
        checkEventPB();
        calcTotalPB();
        
    }
    
    function addMiscPB(name, unit_cost, uom){
        indexRowPB = Math.floor((Math.random() * 100000) + 1);
        var productName         = name;
        var tr = cloneRowPB.clone(true);
        tr.removeAttr("style").removeAttr("id");
        tr.find("td:eq(0)").html(indexRowPB);
        tr.find("td .product_id").attr("id", "product_id"+indexRowPB).val();
        tr.find("td .product_name").attr("id", "product_name"+indexRowPB).val(productName);
        tr.find("td .qty_uom_id").attr("id", "qty_uom_id"+indexRowPB).html(uom);
        tr.find("td .qty").attr("id", "qty_"+indexRowPB).val(1);  
        tr.find("td .unit_cost").attr("id", "unit_cost"+indexRowPB).val(unit_cost);
        tr.find("td .defaltCost").val(unit_cost);
        tr.find("td .total_cost").attr("id", "total_cost"+indexRowPB).val(unit_cost);
        tr.find("td .h_total_cost").attr("id", "h_total_cost"+indexRowPB).val(unit_cost);
        tr.find("td .note").attr("id", "note"+indexRowPB).val("");
        tr.find("td .discountPB").attr("id", "discountPB"+indexRowPB).val(0);
        tr.find("td .lots_number").attr("id", "lots_number"+indexRowPB).removeAttr('class').css('visibility', 'hidden');
        tr.find("td .date_expired").attr("id", "date_expired"+indexRowPB).removeAttr('class').css('visibility', 'hidden');
        $("#tblPB").append(tr);
        tr.find("td .qty").select().focus();
        
        poTimeCode = 1;
        setIndexRowPB();
        checkEventPB();
        calcTotalPB();
        
    }
    
    function addProductPB(productId, sku, puc, name, isExpired, uomList, unitCost, smallUomVal, defaultCost, qtyOrder, uomSelected, isLots){        
        // Get Index Row
        indexRowPB = Math.floor((Math.random() * 100000) + 1);
        defaultCost = defaultCost>0?defaultCost:unitCost;
        var tr = cloneRowPB.clone(true);        
        tr.removeAttr("style").removeAttr("id");          
        tr.find("td:eq(0)").html(indexRowPB);
        tr.find("td .purchasePUC").val(puc);
        tr.find("td .product_id").attr("id", "product_id"+indexRowPB).val(productId);
        tr.find("td .product_name").attr("id", "product_name"+indexRowPB).val(name);                   
        tr.find("td .small_uom_val_pb").attr("id", "small_uom_val_pb").val(smallUomVal);
        tr.find("td .qty_uom_id").attr("id", "qty_uom_id"+indexRowPB).html(uomList);
        tr.find("td .qty").attr("id", "qty_"+indexRowPB).val(qtyOrder);
        tr.find("td .qty_free").attr("id", "qty_free_"+indexRowPB).val(0);
        tr.find("td .defaltCost").val(defaultCost);
        tr.find("td .pb_conversion").val(smallUomVal);
        tr.find("td .lots_number").attr("id", "lots_number"+indexRowPB);
        tr.find("td .date_expired").attr("id", "date_expired"+indexRowPB);
        tr.find("td .discountPB").attr("id", "discountPB"+indexRowPB);
        tr.find("td .unit_cost").attr("id", "unit_cost"+indexRowPB).val(Number(unitCost).toFixed(<?php echo $rowOption[0]; ?>)); 
        tr.find("td .total_cost").attr("id", "total_cost"+indexRowPB).val(Number(unitCost).toFixed(<?php echo $rowOption[0]; ?>));
        tr.find("td .h_total_cost").attr("id", "h_total_cost"+indexRowPB).val(Number(unitCost).toFixed(<?php echo $rowOption[0]; ?>));
        if(isExpired == 1){
            tr.find("td input[name='date_expired[]']").addClass("validate[required]").val('');
        }else{
            tr.find("td input[name='date_expired[]']").removeClass("validate[required]").css('visibility', 'hidden').val('0000-00-00');
        }
        if(isLots == 1){
            tr.find("td input[name='lots_number[]']").addClass("validate[required]").val('');
        }else{
            tr.find("td input[name='lots_number[]']").removeClass("validate[required]").css('visibility', 'hidden').val('0');
        }
        // Get Uom Selected
        if(uomSelected != ''){
            tr.find("td .qty_uom_id").find("option[value='"+uomSelected+"']").attr('selected', 'selected');
        }
        $("#tblPB").append(tr);
        tr.find("td .qty").select().focus();
        
        poTimeCode = 1;
        setIndexRowPB();
        checkEventPB();
        calcTotalPB();
    }
    
    function setIndexRowPB(){
        var sort = 1;
        $(".listBodyPB").each(function(){
            $(this).find("td:eq(0)").html(sort);
            sort++;
        });
    }
    
    function checkVendorPB(field, rules, i, options){
        if($("#PurchaseOrderVendorId").val() == "" || $("#PurchaseOrderVendorName").val() == ""){
            return "* Invalid Vendor";
        }
    }
    
    function serachProCodePB(code, field, search, qtyOrder, uomSelected){
        if($("#PurchaseOrderCompanyId").val() == "" || $("#PurchaseOrderBranchId").val() == "" || $("#PurchaseOrderLocationId").val() == ""){
            $(field).val('');
            poTimeCode = 1;
            alertSelectRequireField();
        }else {
            $.ajax({
                type:   "POST",
                url:    "<?php echo $this->base . "/purchase_orders/searchProductCode/"; ?>"+ $("#PurchaseOrderCompanyId").val()+"/"+$("#PurchaseOrderBranchId").val()+"/"+code+"/"+search,
                beforeSend: function(){
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(msg){
                    $(".btnSavePurchaseBill").removeAttr('disabled');
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $(field).val('');
                    poTimeCode = 1;
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
                    }else{
                        var data = msg;
                        var skuUomId = "all";
                        var product  = $.parseJSON(data);
                        if(data){
                            $.ajax({
                                type: "GET",
                                url: "<?php echo $this->base; ?>/uoms/getRelativeUom/"+product[7]+"/"+skuUomId,
                                data: "",
                                beforeSend: function(){
                                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                                },                                                               
                                success: function(msg){
                                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                                    var productId = product[0];
                                    var sku  = product[1];
                                    var puc  = product[2];
                                    var name = product[3];
                                    var isExpired   = product[4];
                                    var isLots      = product[9];
                                    var unitCost    = product[5];
                                    var smallUomVal = product[6];
                                    var defaultCost = 0;
                                    var packetList  = product[8];
                                    if(packetList != ''){
                                        var packet = packetList.toString().split("**");
                                        var loop = 1;
                                        var time = 0;
                                        $.each(packet,function(key, item){
                                            var items = item.toString().split("||");
                                            var productCode = items[0];
                                            var uomSelected = items[1];
                                            var qtyOrder    = items[2];
                                            if(loop > 1){
                                                time += 3500;
                                            }
                                            setTimeout(function () {
                                                serachProCodePB(productCode, field, search, qtyOrder, uomSelected);
                                            }, time);
                                            loop++;
                                        });
                                    }else{
                                        addProductPB(productId, sku, puc, name, isExpired, msg, unitCost, smallUomVal, defaultCost, qtyOrder, uomSelected, isLots);
                                    }
                                }
                            });
                        }
                    }
                }
            });
        }
    }
    
    function deleteVendorPB(){
        $("#PurchaseOrderVendorId").val("");
        $("#PurchaseOrderVendorName").val("");
        $("#PurchaseOrderVendorName").removeAttr("readonly");
        $("#deleteSearchVendorPB").hide();
        $("#searchVendor").show();
    }
    
    function checkBfSavePB(){
        $("#PurchaseOrderVendorName").removeClass("validate[required]");
        $("#PurchaseOrderVendorName").addClass("validate[required,funcCall[checkVendorPB]]");
        var formName     = "#PurchaseOrderAddForm";
        var validateBack = $(formName).validationEngine("validate");
        if(!validateBack){
            $("#PurchaseOrderVendorName").removeClass("validate[required,funcCall[checkVendorPB]]");
            $("#PurchaseOrderVendorName").addClass("validate[required]");
            return false;
        }else{
            if(($("#PurchaseOrderTotalAmount").val() == undefined && $("#PurchaseOrderTotalAmount").val() == "") || $(".listBodyPB").find(".product_id").val() == undefined){
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
                $("#PurchaseOrderVendorName").removeClass("validate[required,funcCall[checkVendorPB]]");
                $("#PurchaseOrderVendorName").addClass("validate[required]");
                return false;
            }else{
                return true;
            }
        }
    }
    
    function searchProductPB(){
        if($("#PurchaseOrderCompanyId").val() == "" || $("#PurchaseOrderBranchId").val() == "" || $("#PurchaseOrderLocationId").val() == ""){
            poTimeCode = 1;
            alertSelectRequireField();
        }else{
            $.ajax({
                type:   "POST",
                url:    "<?php echo $this->base . "/purchase_orders/product/"; ?>" + $("#PurchaseOrderCompanyId").val()+"/"+$("#PurchaseOrderBranchId").val()+"/"+$("#PurchaseOrderLocationId").val(),
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
                        position:'center',
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show();
                        },
                        buttons: {
                            '<?php echo ACTION_OK; ?>': function() {
                                var data = $("input[name='chkProductPB']:checked");
                                if(data){
                                    var code = data.val();
                                    serachProCodePB(code, '#purchaseSearchSKU', 1, 1, '');
                                }
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            });
        }
    }
    
    function eventKeyRowPB(){
        loadAutoCompleteOff();
        $(".qty, .qty_free, .unit_cost, .qty_uom_id, .total_cost, .btnRemovePB, .noteAddPB, .btnDiscountPB, .btnRemoveDiscountPB, .btnProductInfo").unbind('keypress').unbind('keyup').unbind('change').unbind('click');
        $(".float").autoNumeric({mDec: <?php echo $rowOption[0]; ?>, aSep: ','});
        $(".qty, .qty_free").autoNumeric({mDec: 0, aSep: ','});
        
        $(".qty, .qty_free, .unit_cost").focus(function(){
            if(replaceNum($(this).val()) == 0){
                $(this).val('');
            }
        });
        
        $(".qty, .qty_free, .unit_cost").blur(function(){
            if($(this).val() == ''){
                $(this).val('0');
            }
        });
        
        $(".total_cost").keyup(function(){
            var value = parseFloat(replaceNum($(this).val())=="" ? 0 : replaceNum($(this).val()));
            var unit_price = 0;
            var qty = parseFloat(replaceNum($(this).closest("tr").find(".qty").val())=="" ? 0 : replaceNum($(this).closest("tr").find(".qty").val()));
            var discountAmount = replaceNum($(this).closest("tr").find("input[name='discount_amount[]']").val());
            var discountPercent = $(this).closest("tr").find("input[name='discount_percent[]']").val();
            
            if(discountAmount != 0 && discountAmount != ''){
                unit_price = converDicemalJS( (converDicemalJS( Number(value) + Number(discountAmount) )) / qty );
            }else if(discountPercent!=0 && discountPercent!=''){
                unit_price = converDicemalJS( ( converDicemalJS((converDicemalJS(Number(value) * 100)) / (converDicemalJS(100 - discountPercent))) ) / qty );
                var discount = converDicemalJS((converDicemalJS( (converDicemalJS(Number(value) * 100))  / (converDicemalJS(100 - discountPercent)) ) ) * (converDicemalJS(discountPercent / 100)));
                $(this).closest("tr").find(".discountPB").val(discount.toFixed(<?php echo $rowOption[0]; ?>));
            }else{
                unit_price = parseFloat(converDicemalJS(value / qty));
            }
            
            $(this).closest("tr").find(".h_total_cost").val(parseFloat(converDicemalJS(unit_price * qty)).toFixed(<?php echo $rowOption[0]; ?>));
            $(this).closest("tr").find(".unit_cost").val(unit_price.toFixed(<?php echo $rowOption[0]; ?>));
            calcTotalPB();
            
        });
        
        $(".qty, .qty_free").keyup(function(){
            var qty = replaceNum($(this).closest("tr").find("td .qty").val());
            var unitCost    = parseFloat(replaceNum($(this).closest("tr").find("td .unit_cost").val()!=""?$(this).closest("tr").find("td .unit_cost").val():0));
            var totalAmount = converDicemalJS(parseFloat(replaceNum(qty)) * unitCost);
            var discount    = 0;
            if(parseFloat($(this).closest("tr").find("input[name='discount_percent[]']").val()) > 0){
                discount = parseFloat(converDicemalJS( (converDicemalJS(totalAmount * $(this).closest("tr").find("input[name='discount_percent[]']").val()))/100 ));
                $(this).closest("tr").find("input[name='discount[]']").val((discount).toFixed(<?php echo $rowOption[0]; ?>));
            }else{
                discount = parseFloat(replaceNum($(this).closest("tr").find("input[name='discount[]']").val()));
            }
            $(this).closest("tr").find("td .h_total_cost").val(totalAmount.toFixed(<?php echo $rowOption[0]; ?>));
            var totalCost = converDicemalJS(parseFloat(totalAmount - discount).toFixed(<?php echo $rowOption[0]; ?>));            
            $(this).closest("tr").find("td .total_cost").val(totalCost.toFixed(<?php echo $rowOption[0]; ?>));
            calcTotalPB();
        });
        
        $(".unit_cost").keyup(function(){
            var qty         = $(this).closest("tr").find("td .qty").val()!=""?replaceNum($(this).closest("tr").find("td .qty").val()):0;
            var unitCost    = parseFloat(replaceNum($(this).closest("tr").find("td .unit_cost").val()!=""?$(this).closest("tr").find("td .unit_cost").val():0));
            var totalAmount = converDicemalJS(parseFloat(replaceNum(qty)) * unitCost);
            var discount    = 0;
            if(parseFloat($(this).closest("tr").find("input[name='discount_percent[]']").val()) > 0){
                discount = parseFloat(converDicemalJS( (converDicemalJS(totalAmount * $(this).closest("tr").find("input[name='discount_percent[]']").val()))/100 ));
                $(this).closest("tr").find("input[name='discount[]']").val((discount).toFixed(<?php echo $rowOption[0]; ?>));
            }else{
                discount = parseFloat(replaceNum($(this).closest("tr").find("input[name='discount[]']").val()));
            }
            $(this).closest("tr").find("td .h_total_cost").val(totalAmount.toFixed(<?php echo $rowOption[0]; ?>));
            var totalCost = converDicemalJS(parseFloat(totalAmount - discount).toFixed(<?php echo $rowOption[0]; ?>));            
            $(this).closest("tr").find("td .total_cost").val(totalCost.toFixed(<?php echo $rowOption[0]; ?>));
            calcTotalPB();
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
            var value         = replaceNum($(this).val());
            var smallUomVal   = parseFloat($(this).closest("tr").find(".small_uom_val_pb").val());
            var uomConversion = converDicemalJS(smallUomVal / parseFloat(replaceNum($(this).find("option[value='"+value+"']").attr('conversion'))));
            var unit_price    = (parseFloat(converDicemalJS(replaceNum($(this).closest("tr").find(".defaltCost").val()) / parseFloat(replaceNum($(this).find("option[value='"+value+"']").attr('conversion')))))).toFixed(<?php echo $rowOption[0]; ?>);
            
            if($(this).closest("tr").find(".product_id").val() != ""){
                $(this).closest("tr").find(".unit_cost").val(unit_price); 
                var totalAmount = parseFloat( converDicemalJS(unit_price * replaceNum($(this).closest("tr").find(".qty").val())));
                if($(this).closest("tr").find("input[name='discount_percent[]']").val() != ""){
                    $(this).closest("tr").find("input[name='discount[]']").val(parseFloat(converDicemalJS((converDicemalJS(totalAmount * replaceNum($(this).closest("tr").find("input[name='discount_percent[]']").val()))) / 100 )).toFixed(<?php echo $rowOption[0]; ?>));
                }else{
                    var discountAmount = replaceNum($(this).closest("tr").find("input[name='discount_amount[]']").val()) > 0 ? replaceNum($(this).closest("tr").find("input[name='discount_amount[]']").val()) : 0;
                    $(this).closest("tr").find("input[name='discount[]']").val(discountAmount.toFixed(<?php echo $rowOption[0]; ?>));
                }
                var discount = parseFloat(replaceNum($(this).closest("tr").find("input[name='discount[]']").val()>0?$(this).closest("tr").find("input[name='discount[]']").val():0 ));
                $(this).closest("tr").find(".pb_conversion").val(uomConversion);
                $(this).closest("tr").find(".h_total_cost").val(totalAmount.toFixed(<?php echo $rowOption[0]; ?>));
                $(this).closest("tr").find(".total_cost").val( (converDicemalJS(totalAmount - discount)).toFixed(<?php echo $rowOption[0]; ?>) );                               
                calcTotalPB();                
            }
            
        });
        
        $(".btnRemovePB").click(function(){
            var currentTr = $(this).closest("tr");
            $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>Are you sure to remove this order?</p>');
            $("#dialog").dialog({
                title: '<?php echo DIALOG_INFORMATION; ?>',
                resizable: false,
                position:'center',
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
                        calcTotalPB();
                        setIndexRowPB();
                        $(this).dialog("close");
                    }
                }
            });
        });
        
        $(".btnDiscountPB").click(function(){
            var obj = $(this);
            $.ajax({
                type:   "POST",
                url:    "<?php echo $this->base . "/purchase_orders/invoiceDiscount"; ?>",
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
                                obj.closest("tr").find("input[name='discount_id[]']").val(0);
                                var amount  = replaceNum($("#inputInvoiceDisAmt").val());
                                var percent = replaceNum($("#inputInvoiceDisPer").val());
                                var percenAmount = 0;
                                var totalUnit = parseFloat(replaceNum(obj.closest("tr").find(".unit_cost").val()));
                                if(amount){
                                    obj.closest("tr").find("input[name='discount_amount[]']").val(Number(amount).toFixed(<?php echo $rowOption[0]; ?>));
                                    obj.closest("tr").find("input[name='discount[]']").val(Number(amount).toFixed(<?php echo $rowOption[0]; ?>));
                                    obj.closest("tr").find(".total_cost").val(parseFloat( converDicemalJS(converDicemalJS(replaceNum(obj.closest("tr").find("input[name='qty[]']").val()) * totalUnit) - amount) ).toFixed(<?php echo $rowOption[0]; ?>));
                                }else{
                                    percenAmount = parseFloat( (converDicemalJS(percent * replaceNum(obj.closest("tr").find("input[name='qty[]']").val()) * totalUnit)) /100 );
                                    obj.closest("tr").find("input[name='discount_percent[]']").val(percent);
                                    obj.closest("tr").find("input[name='discount_amount[]']").val(percenAmount.toFixed(<?php echo $rowOption[0]; ?>));
                                    obj.closest("tr").find("input[name='discount[]']").val(percenAmount.toFixed(<?php echo $rowOption[0]; ?>));
                                    obj.closest("tr").find(".total_cost").val(parseFloat(converDicemalJS((converDicemalJS(replaceNum(obj.closest("tr").find("input[name='qty[]']").val())*totalUnit)) - percenAmount)).toFixed(<?php echo $rowOption[0]; ?>));
                                }
                                obj.closest("tr").find("input[name='discount[]']").css("display", "inline");
                                obj.closest("tr").find(".btnRemoveDiscountPB").css("display", "inline");
                                calcTotalPB();
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            });
        });
        
        $(".btnRemoveDiscountPB").click(function(){
            var discount = parseFloat(replaceNum($(this).closest("tr").find("input[name='discount[]']").val()));
            var totalAmount = parseFloat(replaceNum($(this).closest("tr").find(".total_cost").val()));
            $(this).closest("tr").find("input[name='discount_id[]']").val("");
            $(this).closest("tr").find("input[name='discount_amount[]']").val(0);
            $(this).closest("tr").find("input[name='discount_percent[]']").val(0);
            $(this).closest("tr").find("input[name='discount[]']").val('0');
            $(this).closest("tr").find(".btnRemoveDiscountPB").css("display", "none");
            $(this).closest("tr").find(".total_cost").val(parseFloat(converDicemalJS(totalAmount + discount)).toFixed(<?php echo $rowOption[0]; ?>));
            calcTotalPB();
            
            
        });
        
        $(".noteAddPB").click(function(){
            addNotePB($(this));
        });
        
        // Button Show Information
        $(".btnProductInfo").click(function(){
            showProductInfoPB($(this));
        });
        
        $('.date_expired').datepicker({
            dateFormat:'dd/mm/yy',
            changeMonth: true,
            changeYear: true
        }).unbind("blur");
    }
    
    function checkEventPB(){
        eventKeyRowPB();
        $(".listBodyPB").unbind("click");
        $(".listBodyPB").click(function(){
            eventKeyRowPB();
        });
    }
    
    function searchVendorPB(){
        var companyId = $("#PurchaseOrderCompanyId").val();
        $.ajax({
            type:   "POST",
            url:    "<?php echo $this->base . "/purchase_orders/vendor/"; ?>"+companyId,
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
                    position:'center',
                    open: function(event, ui){
                        $(".ui-dialog-buttonpane").show();
                    },
                    close: function(){
                        poTimeCode = 1;
                    },
                    buttons: {
                        '<?php echo ACTION_OK; ?>': function() {
                            // calculate due_date
                            var data = $("input[name='chkVendor']:checked").val();
                            if(data){
                                // Set Vendor
                                $("#PurchaseOrderVendorId").val(data.split('-')[0]);
                                $("#PurchaseOrderVendorName").val(data.split('-')[1]+" - "+data.split('-')[2]);
                                $("#PurchaseOrderVendorName").attr('readonly', true);
                                $("#searchVendor").hide();
                                $("#deleteSearchVendorPB").show();
                            }
                            poTimeCode = 1;
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
    
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        loadAutoCompleteOff();
        // Hide Branch
        $("#PurchaseOrderBranchId").filterOptions('com', '0', '');
        $("#PurchaseOrderLocationGroupId").chosen({width: 190});
        // Remove Clone Row List
        $("#detailPB").remove();
        
        var waitForFinalEventPB = (function () {
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
        if(tabPBReg != tabPBId){
            $("a[href='"+tabPBId+"']").click(function(){
                if($("#bodyListPB").html() != '' && $("#bodyListPB").html() != null){
                    waitForFinalEventPB(function(){
                        refreshScreenPB();
                        resizeFormTitlePB();
                        resizeFornScrollPB();  
                    }, 500, "Finish");
                }
            });
            tabPBReg = tabPBId;
        }

        waitForFinalEventPB(function(){
              refreshScreenPB();
              resizeFormTitlePB();
              resizeFornScrollPB();  
            }, 500, "Finish");
            
        $(window).resize(function(){
            if(tabPBReg == $(".ui-tabs-selected a").attr("href")){
                waitForFinalEventPB(function(){
                    refreshScreenPB();
                    resizeFormTitlePB();
                    resizeFornScrollPB();  
                  }, 500, "Finish");
            }
        });
        
        // Hide / Show Header
        $("#btnHideShowHeaderPurchaseBill").click(function(){
            var PurchaseOrderCompanyId       = $("#PurchaseOrderCompanyId").val();
            var PurchaseOrderBranchId        = $("#PurchaseOrderBranchId").val();
            var PurchaseOrderLocationGroupId = $("#PurchaseOrderLocationGroupId").val();
            var PurchaseOrderLocationId      = $("#PurchaseOrderLocationId").val();
            var PurchaseOrderOrderDate       = $("#PurchaseOrderOrderDate").val();
            var PurchaseOrderVendorId        = $("#PurchaseOrderVendorId").val();
            var PurchaseOrderChartAccountId  = $("#PurchaseOrderChartAccountId").val();
            
            if(PurchaseOrderCompanyId == "" || PurchaseOrderBranchId == "" || PurchaseOrderLocationGroupId == "" || PurchaseOrderLocationId == "" || PurchaseOrderOrderDate == "" || PurchaseOrderVendorId == "" || PurchaseOrderChartAccountId == ""){
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
                    $("#PBTop").hide();
                    img += 'arrow-down.png';
                } else {
                    action = 'Hide';
                    $("#PBTop").show();
                    img += 'arrow-up.png';
                }
                $(this).find("span").text(action);
                $(this).find("img").attr("src", img);
                if(tabPBReg == $(".ui-tabs-selected a").attr("href")){
                    waitForFinalEventPB(function(){
                        resizeFornScrollPB();
                    }, 300, "Finish");
                }
            }
        });
        
        // Form Validate
        $("#PurchaseOrderAddForm").validationEngine('detach');
        $("#PurchaseOrderAddForm").validationEngine('attach');
        
        $(".btnSavePurchaseBill").click(function(){
            if(checkBfSavePB() == true){
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
                        '<?php echo ACTION_SAVE; ?>': function() {
                            // Set Save Status
                            $("#saveExitPurchaseOrder").val(0);
                            // Action Click Save
                            $("#PurchaseOrderAddForm").submit();
                            $(this).dialog("close");
                        },
                        '<?php echo ACTION_SAVE_EXIT; ?>': function() {
                            // Set Save Status
                            $("#saveExitPurchaseOrder").val(1);
                            // Action Click Save
                            $("#PurchaseOrderAddForm").submit();
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
        
        $("#PurchaseOrderAddForm").ajaxForm({
            dataType: 'json',
            beforeSubmit: function(arr, $form, options) {
                $(".txtSavePB").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            beforeSerialize: function($form, options) {
                $("#PurchaseOrderOrderDate, #PurchaseOrderInvoiceDate, .date_expired").datepicker("option", "dateFormat", "yy-mm-dd");
                $(".float, .qty").each(function(){
                    $(this).val($(this).val().replace(/,/g,""));
                });
                $("#PurchaseOrderTotalAmount").val($("#PurchaseOrderTotalAmount").val().replace(/,/g,""));
            },
            error: function (result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                createSysAct('Purchase Bill', 'Add', 2, result.responseText);
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
                        var saveStatus = $("#saveExitPurchaseOrder").val();
                        if(saveStatus == 1){
                            backPurchaseBill();
                        } else {
                            saveContiunePurchaseBill();
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
                    codeDialogPB();
                }else if(result.code == "2"){
                    errorSavePB();
                }else{
                    createSysAct('Purchase Bill', 'Add', 1, '');
                    $("#dialog").html('<div class="buttons"><button type="submit" class="positive printInvoicePB" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span><?php echo ACTION_PRINT_PURCHASE_BILL; ?></span></button><button type="submit" class="positive printInvoiceProPB" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span><?php echo ACTION_ONLY_PRODUCT; ?></span></button></div> ');
                    $(".printInvoicePB").click(function(){
                        $.ajax({
                            type: "POST",
                            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printInvoice/"+result.po_id,
                            beforeSend: function(){
                                $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                            },
                            success: function(printInvoicePBResult){
                                w=window.open();
                                w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                                w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                                w.document.write(printInvoicePBResult);
                                w.document.close();
                                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                            }
                        });
                    });
                    $(".printInvoiceProPB").click(function(){
                        $.ajax({
                            type: "POST",
                            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printInvoiceProduct/"+result.po_id,
                            beforeSend: function(){
                                $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                            },
                            success: function(printInvoicePBResult){
                                w=window.open();
                                w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                                w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                                w.document.write(printInvoicePBResult);
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
                            var saveStatus = $("#saveExitPurchaseOrder").val();
                            if(saveStatus == 1){
                                backPurchaseBill();
                            } else {
                                saveContiunePurchaseBill();
                            }
                        },
                        buttons: {
                            '<?php echo ACTION_CLOSE; ?>': function() {
                                var saveStatus = $("#saveExitPurchaseOrder").val();
                                if(saveStatus == 1){
                                    backPurchaseBill();
                                } else {
                                    saveContiunePurchaseBill();
                                }
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            }
        });
        
        if($.cookie('PurchaseOrderLocationGroupId')!=null || $("#PurchaseOrderLocationGroupId").find("option:selected").val() != ''){
            if($.cookie('companyIdTransferOrder') != null){
                $("#PurchaseOrderLocationGroupId").val($.cookie('PurchaseOrderLocationGroupId'));
            }
            checkLocationByGroupPB();
        }
        
        $("#PurchaseOrderLocationGroupId").change(function(){
            var obj = $(this);
            $.cookie('PurchaseOrderLocationGroupId', obj.val(), { expires: 7, path: "/" });
            checkLocationByGroupPB();
        });
        
        <?php
        if(count($locationGroups) == 1){
        ?>
        checkLocationByGroupPB();
        <?php
        }
        ?>
        // Action Vendor
        
        $("#searchVendor").click(function(){
            if(checkOrderDatePB() == true && $("#PurchaseOrderCompanyId").val() != ''){
                searchVendorPB();
            }
        });
        
        $("#deleteSearchVendorPB").click(function(){
            deleteVendorPB();
        });
        
        $("#PurchaseOrderVendorName").focus(function(){
            checkOrderDatePB();
        });
        
        $("#PurchaseOrderVendorName").keypress(function(e){
            if((e.which && e.which != 13) || e.keyCode != 13){
                $("#PurchaseOrderVendorId").val("");
            }
        });
        
        $("#PurchaseOrderVendorName").autocomplete("<?php echo $this->base . "/purchase_orders/searchVendor"; ?>", {
            width: 410,
            max: 10,
            scroll: true,
            scrollHeight: 500,
            formatItem: function(data, i, n, value) {
                if(checkCompanyPB(value.split(".*")[4])){
                    return value.split(".*")[2] + " - " + value.split(".*")[1];
                }
            },
            formatResult: function(data, value) {
                if(checkCompanyPB(value.split(".*")[4])){
                    return value.split(".*")[2] + " - " + value.split(".*")[1];
                }
            }
        }).result(function(event, value){
            // Set Vendor
            $("#PurchaseOrderVendorId").val(value.toString().split(".*")[0]);
            $("#PurchaseOrderVendorName").val(value.toString().split(".*")[2] + " - " + value.toString().split(".*")[1]);
            $("#PurchaseOrderVendorName").attr('readonly', true);
            $("#searchVendor").hide();
            $("#deleteSearchVendorPB").show();
        });
        // End Action Vendor
        
        // Action Scan/Search Product
        $("#purchaseSearchSKU").autocomplete("<?php echo $this->base . "/purchase_orders/searchProduct/"; ?>", {
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
            if(poTimeCode == 1){
                poTimeCode = 2;
                serachProCodePB(code, '#purchaseSearchSKU', 1, 1, '');
            }
        });
        
        $("#purchaseSearchProduct").click(function(){
            searchProductPB();
        });

        $("#purchaseSearchSKU").keypress(function(e){
            var code =null;
            var obj = $(this);
            code = (e.keyCode ? e.keyCode : e.which);
            if (code == 13){
                if($("#PurchaseOrderCompanyId").val() ==""){
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
                        if(poTimeCode == 1){
                            poTimeCode = 2;
                            serachProCodePB($(this).val(), '#purchaseSearchSKU', 1, 1, '');
                        }
                    }
                }
                return false;
            }
        });
        
        // End Action Scan/Search Product
        
        $(".btnBackPurchaseOrder").click(function(event){
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
                        backPurchaseBill();
                    }
                }
            });
        });

        $('#PurchaseOrderOrderDate, #PurchaseOrderInvoiceDate').datepicker({
            dateFormat:'dd/mm/yy',
            changeMonth: true,
            changeYear: true
        }).unbind("blur");
        
        $("#PurchaseOrderOrderDate").datepicker("option", "minDate", "<?php echo $dataClosingDate[0]; ?>");
        $("#PurchaseOrderOrderDate").datepicker("option", "maxDate", 0);
        
        $(".addServicePB").click(function(){
            searchAllServicePB();
        });

        $(".addMiscellaneousPB").click(function(){
            searchAllMiscPB();
        });
        
        $("#PurchaseOrderCompanyId").change(function(){
            var obj    = $(this);
            var vatCal = $(this).find("option:selected").attr("vat-opt");
            if($(".listBodyPB").find(".product_id").val() == undefined){
                $.cookie('companyIdPB', obj.val(), { expires: 7, path: "/" });
                $("#PurchaseOrderVatCalculate").val(vatCal);
                $("#PurchaseOrderBranchId").filterOptions('com', obj.val(), '');
                $("#PurchaseOrderBranchId").change();
                resetFormPB();
                checkVatCompanyPB();
                changeInputCSSPB();
            }else{
                var question = "<?php echo SALES_ORDER_CONFIRM_CHANGE_COMPANY; ?>";
                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+question+'</p>');
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
                        '<?php echo ACTION_OK; ?>': function() {
                            $.cookie('companyIdPB', obj.val(), { expires: 7, path: "/" });
                            $("#PurchaseOrderVatCalculate").val(vatCal);
                            $("#PurchaseOrderBranchId").filterOptions('com', obj.val(), '');
                            $("#PurchaseOrderBranchId").change();
                            $("#tblPB").html("");
                            resetFormPB();
                            checkVatCompanyPB();
                            changeInputCSSPB();
                            $(this).dialog("close");
                        },
                        '<?php echo ACTION_CANCEL; ?>': function() {
                            $("#PurchaseOrderCompanyId").val($.cookie("companyIdPB"));
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
        // Action Branch
        $("#PurchaseOrderBranchId").change(function(){
            var obj = $(this);
            if($(".listBodyPB").find(".product_id").val() == undefined){
                $.cookie('branchIdPurchaseBill', obj.val(), { expires: 7, path: "/" });
                branchChangePurchaseBill(obj);
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
                            $.cookie('branchIdPurchaseBill', obj.val(), { expires: 7, path: "/" });
                            branchChangePurchaseBill(obj);
                            $("#tblPB").html('');
                            calcTotalPB();
                            $(this).dialog("close");
                        },
                        '<?php echo ACTION_CANCEL; ?>': function() {
                            $("#PurchaseOrderBranchId").val($.cookie("branchIdPurchaseBill"));
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
        // Company
        if($.cookie('companyIdPB') != null || $("#PurchaseOrderCompanyId").find("option:selected").val() != ''){
            if($.cookie('companyIdSales') != null){
                $("#PurchaseOrderCompanyId").val($.cookie('companyIdPB'));
            }
            var vatCal = $("#PurchaseOrderCompanyId").find("option:selected").attr("vat-opt");
            $("#PurchaseOrderVatCalculate").val(vatCal);
            checkVatCompanyPB();
            $("#PurchaseOrderBranchId").filterOptions('com', $("#PurchaseOrderCompanyId").val(), '');
            $("#PurchaseOrderBranchId").change();
        }
        // Action VAT Status
        $("#PurchaseOrderVatSettingId").change(function(){
            checkVatSelectedPB();
            calcTotalPB();
        });
        
        <?php if($allowEditInvDis){ ?>
        // Action Total Discount Amount
        $("#PurchaseOrderDiscountAmount").click(function(){
            getTotalDiscountPB();
        });
        
        
        $("#btnRemovePBTotalDiscount").click(function(){
            $("#PurchaseOrderDiscountAmount").val(0);
            $("#PurchaseOrderDiscountPercent").val(0);
            $(this).hide();
            $("#PBLabelDisPercent").html('');
            calcTotalPB();
        });
        <?php } ?>
        
        <?php
        if($allowAddVendor){
        ?>
        $("#addVendorPurchaseBill").click(function(){
            $.ajax({
                type:   "GET",
                url:    "<?php echo $this->base . "/vendors/quickAdd/"; ?>",
                beforeSend: function(){
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(msg){
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $("#dialog3").html(msg);
                    $("#dialog3").dialog({
                        title: '<?php echo MENU_VENDOR_ADD; ?>',
                        resizable: false,
                        modal: true,
                        width: '700',
                        height: '600',
                        position:'center',
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show();
                        },
                        buttons: {
                            '<?php echo ACTION_CLOSE; ?>': function() {
                                $(this).dialog("close");
                            },
                            '<?php echo ACTION_SAVE; ?>': function() {
                                var formName = "#VendorQuickAddForm";
                                var validateBack =$(formName).validationEngine("validate");
                                if(!validateBack){
                                    return false;
                                }else{
                                    if($("#VendorVgroupId").val() == null || $("#VendorVgroupId").val() == '' || $("#VendorPaymentTermId").val() == '' || $("#VendorPaymentTermId").val() == ''){
                                        alertSelectRequireField();
                                    } else {
                                        $(this).dialog("close");
                                        $.ajax({
                                            dataType: 'json',
                                            type: "POST",
                                            url: "<?php echo $this->base; ?>/vendors/quickAdd",
                                            data: $("#VendorQuickAddForm").serialize(),
                                            beforeSend: function(){
                                                $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                                            },
                                            error: function (result) {
                                                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                                createSysAct('Purchase Bill', 'Quick Add Vendor', 2, result);
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
                                                createSysAct('Purchase Bill', 'Quick Add Vendor', 1, '');
                                                var msg = '';
                                                if(result.error == 0){
                                                    msg = '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>';
                                                    // Set Vendor
                                                    $("#PurchaseOrderVendorId").val(result.id);
                                                    $("#PurchaseOrderVendorName").val(result.name);
                                                    $("#PurchaseOrderVendorName").attr("readonly", true);
                                                    $("#searchVendor").hide();
                                                    $("#deleteSearchVendorPB").show();
                                                    $("#PurchaseOrderAddForm").validationEngine("hideAll");
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
        if($allowAddProduct){
        ?>
        $("#addProductPurchaseBill").click(function(){
            $.ajax({
                type:   "GET",
                url:    "<?php echo $this->base . "/products/quickAdd/"; ?>",
                beforeSend: function(){
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(msg){
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $("#dialog3").html(msg);
                    $("#dialog3").dialog({
                        title: '<?php echo MENU_PRODUCT_MANAGEMENT_ADD; ?>',
                        resizable: false,
                        modal: true,
                        width: '900',
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
                                var formName = "#ProductQuickAddForm";
                                var validateBack =$(formName).validationEngine("validate");
                                if(!validateBack){
                                    return false;
                                }else{
                                    <?php 
                                    if(count($branches) > 1){
                                    ?>
                                    listbox_selectall('productBranchSelected', true);
                                    <?php
                                    }
                                    ?>
                                    if($("#productBranchSelected").val() == null || $("#ProductPgroupId").val() == null || $("#ProductPgroupId").val() == '' || $("#ProductUomId").val() == null || $("#ProductUomId").val() == ''){
                                        alertSelectRequireField();
                                    } else {
                                        $(this).dialog("close");
                                        var dataPost = $("#ProductQuickAddForm").serialize()+"&"+$('#formBranchProductQuick').serialize();
                                        $.ajax({
                                            type: "POST",
                                            url: "<?php echo $this->base; ?>/products/quickAdd",
                                            data: dataPost,
                                            beforeSend: function(){
                                                $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                                            },
                                            success: function(result){
                                                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                                                // Message Alert
                                                if(result != '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>' && result != '<?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?>' && result != '<?php echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM; ?>'){
                                                    createSysAct('Product', 'Quick Add', 2, result);
                                                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                                                }else {
                                                    createSysAct('Product', 'Quick Add', 1, '');
                                                    // alert message
                                                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result+'</p>');
                                                }
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
                    
        resetFormPB();
        checkVatCompanyPB();
        checkOrderDatePB();
        changeInputCSSPB();
    });
    
    function branchChangePurchaseBill(obj){
        var currency = obj.find("option:selected").attr("currency");
        var currencySymbol = obj.find("option:selected").attr("symbol");
        $("#PurchaseOrderCurrencyCenterId").val(currency);
        $(".lblSymbolPB").html(currencySymbol);
    }
    
    function getTotalDiscountPB(){
        $.ajax({
            type:   "POST",
            url:    "<?php echo $this->base . "/purchase_orders/invoiceDiscount"; ?>",
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
                            $("#PurchaseOrderDiscountAmount").val(totalDisAmt);
                            $("#PurchaseOrderDiscountPercent").val(totalDisPercent);
                            calcTotalPB();
                            if(totalDisPercent > 0){
                                $("#PBLabelDisPercent").html('('+totalDisPercent+'%)');
                            } else {
                                $("#PBLabelDisPercent").html('');
                            }
                            if(totalDisAmt > 0 || totalDisPercent > 0){
                                $("#btnRemovePBTotalDiscount").show();
                            }
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
    }
    
    function changeLblVatCalPB(){
        var vatCal = $("#PurchaseOrderVatCalculate").val();
        $("#lblPurchaseOrderVatSettingId").unbind("mouseover");
        if(vatCal != ''){
            if(vatCal == 1){
                $("#lblPurchaseOrderVatSettingId").mouseover(function(){
                    Tip('<?php echo TABLE_VAT_BEFORE_DISCOUNT; ?>');
                });
            } else {
                $("#lblPurchaseOrderVatSettingId").mouseover(function(){
                    Tip('<?php echo TABLE_VAT_AFTER_DISCOUNT; ?>');
                });
            }
        }
    }
    
    function checkVatSelectedPB(){
        var vatPercent = replaceNum($("#PurchaseOrderVatSettingId").find("option:selected").attr("rate"));
        var vatAccId   = replaceNum($("#PurchaseOrderVatSettingId").find("option:selected").attr("acc"));
        $("#PurchaseOrderVatPercent").val((vatPercent).toFixed(2));
        $("#PurchaseOrderVatChartAccountId").val(vatAccId);
    }
    
    function checkVatCompanyPB(){
        // VAT Filter
        $("#PurchaseOrderVatSettingId").filterOptions('com-id', $("#PurchaseOrderCompanyId").val(), '');
    }
    
    function checkCompanyPB(companyId){
        var companyReturn = false;
        var companyPut    = companyId.split(",");
        var companySelect = $("#PurchaseOrderCompanyId").val();
        if(companyPut.indexOf(companySelect) != -1){
            companyReturn = true;
        }
        return companyReturn;
    }
    
    function resetFormPB(){
        // Vendor
        $("#deleteSearchVendorPB").click();
        // Purchase Order
        $("#deletePONumber").click();
    }
    
    function reloadPagePB(){
        var rightPanel = $(".btnBackPurchaseOrder").parent().parent().parent().parent().parent();
        rightPanel.html("<?php echo ACTION_LOADING; ?>");
        rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/add/");
    }
    
    function checkOrderDatePB(){
        if($("#PurchaseOrderOrderDate").val() == ""){
            $("#PurchaseOrderOrderDate").focus();
            return false;
        }else{
            return true;
        }
    }
    
    function searchAllServicePB(){
        if($("#PurchaseOrderCompanyId").val()=="" || $("#PurchaseOrderBranchId").val()==""){
            poTimeCode == 1;
            alertSelectRequireField();
        }else{
            $.ajax({
                type:   "POST",
                url:    "<?php echo $this->base . "/purchase_orders/service"; ?>/"+$("#PurchaseOrderCompanyId").val()+"/"+$("#PurchaseOrderBranchId").val(),
                beforeSend: function(){
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(msg){
                    poTimeCode == 1;
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
                                    addServicePB($("#ServiceServiceId").val(),$("#ServiceServiceId").find("option:selected").attr("abbr"),$("#ServiceUnitPrice").val(),$("#ServiceServiceId").find("option:selected").attr("scode"),$("#ServiceServiceId").find("option:selected").attr("suom"));
                                }
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            });
        }
    }
    
    function checkLocationByGroupPB(){
        var locationGroup = $("#PurchaseOrderLocationGroupId").val();
        $("#PurchaseOrderLocationId").filterOptions('location-group', locationGroup, '');
    }
    
    function searchAllMiscPB(){
        if($("#PurchaseOrderCompanyId").val()=="" || $("#PurchaseOrderBranchId").val()==""){
            poTimeCode == 1;
            alertSelectRequireField();
        }else{
            $.ajax({
                type:   "POST",
                url:    "<?php echo $this->base . "/sales_orders/miscellaneous"; ?>",
                beforeSend: function(){
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(msg){
                    poTimeCode == 1;
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $("#dialog").html(msg).dialog({
                        title: '<?php echo SALES_ORDER_ADD_NEW_MISCELLANEOUS; ?>',
                        resizable: false,
                        modal: true,
                        width: 800,
                        height: 'auto',
                        position:'center',
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show();
                        },
                        buttons: {
                            '<?php echo ACTION_OK; ?>': function() {
                                var formName = "#MiscellaneousMiscellaneousForm";
                                var validateBack =$(formName).validationEngine("validate");
                                if(!validateBack){
                                    return false;
                                }else{
                                    addMiscPB($("#MiscellaneousDescription").val(),$("#MiscellaneousUnitPrice").val(),$("#hiddenUom").html());
                                }
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            });
        }
    }
    
    function codeDialogPB(){
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
                $(".btnSavePurchaseBill").removeAttr("disabled");
                $(".txtSavePB").html("<?php echo ACTION_SAVE; ?>");
            },
            buttons: {
                '<?php echo ACTION_CLOSE; ?>': function() {
                    $(this).dialog("close");
                    $(".btnSavePurchaseBill").removeAttr("disabled");
                    $(".txtSavePB").html("<?php echo ACTION_SAVE; ?>");
                }
            }
        });
    }
    
    function errorSavePB(){
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
                backPurchaseBill();
            },
            buttons: {
                '<?php echo ACTION_CLOSE; ?>': function() {
                    $(this).dialog("close");
                }
            }
        });
    }
    
    function addNotePB(currentTr){
        var note = currentTr.closest("tr").find(".note");
        $("#dialog").html("<textarea style='width:350px; height: 200px;' id='noteCommentPO'>"+note.val()+"</textarea>").dialog({
            title: '<?php echo TABLE_MEMO; ?>',
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
                    note.val($("#noteCommentPO").val());
                    $(this).dialog("close");
                }
            }
        });
    }
    
    function backPurchaseBill(){
        oCache.iCacheLower = -1;
        oPOTable.fnDraw(false);
        $("#PurchaseOrderAddForm").validationEngine("hideAll");
        var rightPanel = $("#PurchaseOrderAddForm").parent();
        var leftPanel  = rightPanel.parent().find(".leftPanel");
        $("#"+PbTableName).find("tbody").html('<tr><td colspan="9" class="dataTables_empty first"><?php echo TABLE_LOADING; ?></td></tr>');
        rightPanel.hide("slide", { direction: "right" }, 500, function(){
            leftPanel.show();
            rightPanel.html("");
        });
    }
    
    function saveContiunePurchaseBill(){
        $("#PurchaseOrderAddForm").validationEngine("hideAll");
        var rightPanel = $("#PurchaseOrderAddForm").parent();
        rightPanel.html("<?php echo ACTION_LOADING; ?>");
        rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/add/");
    }
    
    function changeInputCSSPB(){
        var cssStyle  = 'inputDisable';
        var cssRemove = 'inputEnable';
        var readonly  = true;
        var disabled  = true;
        $(".searchVendor").hide();
        $("#divSearchPB").css("visibility", "hidden");
        if($("#PurchaseOrderCompanyId").val() != ''){
            var currencySymbol = $("#PurchaseOrderCompanyId").find("option:selected").attr("symbol");
            cssStyle  = 'inputEnable';
            cssRemove = 'inputDisable';
            readonly  = false;
            disabled  = false;
            if($("#PurchaseOrderVendorName").val() == ''){
                $(".searchVendor").show();
            }
            $("#divSearchPB").css("visibility", "visible");
            $(".lblSymbolPB").html(currencySymbol);
            $("#companySymbolPurchase").html(currencySymbol);
        } else {
            $(".lblSymbolPB").html('');
            $("#companySymbolPurchase").html('');
        } 
        // Label
        $("#PurchaseOrderAddForm").find("label").removeAttr("class");
        $("#PurchaseOrderAddForm").find("label").each(function(){
            var label = $(this).attr("for");
            if(label != 'PurchaseOrderCompanyId'){
                $(this).addClass(cssStyle);
            }
        });
        // Input & Select
        $("#PurchaseOrderAddForm").find("input").each(function(){
            $(this).removeClass(cssRemove);
            $(this).addClass(cssStyle);
        });
        $("#PurchaseOrderAddForm").find("select").each(function(){
            var selectId = $(this).attr("id");
            if(selectId != 'PurchaseOrderCompanyId'){
                $(this).removeClass(cssRemove);
                $(this).addClass(cssStyle);
                $(this).attr("disabled", disabled);
            }
        });
        $(".lblSymbolPB").removeClass(cssRemove);
        $(".lblSymbolPB").addClass(cssStyle);
        $(".lblSymbolPBPercent").removeClass(cssRemove);
        $(".lblSymbolPBPercent").addClass(cssStyle);
        // Input Readonly
        $("#PurchaseOrderVendorName").attr("readonly", readonly);
        $("#purchaseSearchSKU").attr("readonly", readonly);
        // Put label VAT Calculate
        changeLblVatCalPB();
        // Check VAT Default
        getDefaultVatPB();
    }
    
    function getDefaultVatPB(){
        var vatDefault = $("#PurchaseOrderCompanyId option:selected").attr("vat-d");
        $("#PurchaseOrderVatSettingId option[value='"+vatDefault+"']").attr("selected", true);
        checkVatSelectedPB();
    }
    
    function showProductInfoPB(currentTr){
        var vendorId  = $("#PurchaseOrderVendorId").val();
        var productId = currentTr.closest("tr").find(".product_id").val();
        if(productId != ''){
            $.ajax({
                type:   "POST",
                url:    "<?php echo $this->base . "/purchase_orders/productHistory"; ?>/"+productId+"/"+vendorId,
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
<?php echo $this->Form->create('PurchaseOrder'); ?>
<input type="hidden" id="saveExitPurchaseOrder" value="1" />
<input type="hidden" value="<?php echo $rowSettingUomDetail[1]; ?>" name="data[calculate_cogs]" />
<input type="hidden" name="data[total_deposit]" id="PurchaseOrderTotalDeposit" />
<input type="hidden" value="" name="data[PurchaseOrder][vat_calculate]" id="PurchaseOrderVatCalculate" />
<input type="hidden" value="" name="data[PurchaseOrder][currency_center_id]" id="PurchaseOrderCurrencyCenterId" />
<input type="hidden" value="" name="data[PurchaseOrder][exchange_rate_id]" id="PurchaseOrderExchangeRateId" />
<div style="float: right; width: 165px; text-align: right; cursor: pointer;" id="btnHideShowHeaderPurchaseBill">
    [<span>Hide</span> Header Information <img alt="" align="absmiddle" style="width: 16px; height: 16px;" src="<?php echo $this->webroot . 'img/button/arrow-up.png'; ?>" />]
</div>
<div style="clear: both;"></div>
<div id="PBTop">
    <fieldset>
        <legend><?php __(MENU_PURCHASE_ORDER_MANAGEMENT_INFO); ?></legend>
        <table cellpadding="0" cellspacing="0" style="width: 100%;">
            <tr>
                <td style="width: 50%">
                    <table cellpadding="0" style="width: 100%">
                        <tr>
                            <td style="width: 34%"><label for="PurchaseOrderOrderDate"><?php echo TABLE_PB_DATE; ?></label> <span class="red">*</span></td>
                            <td style="width: 33%"><label for="PurchaseOrderPoCode"><?php echo TABLE_PB_NUMBER; ?></label></td>
                            <td style="width: 33%"><label for="PurchaseOrderPoNo" style="display: none;"><?php echo TABLE_PO_NUMBER; ?></label></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="inputContainer" style="width:100%">
                                    <?php echo $this->Form->text('order_date', array('value' => date("d/m/Y"), 'class' => 'validate[required]', 'readonly' => 'readonly', 'style' => 'width:70%')); ?>
                                </div>
                            </td>
                            <td>
                                <div class="inputContainer" style="width:100%">
                                    <?php echo $this->Form->text('po_code', array('style' => 'width:70%', 'placeholder' => TABLE_AUTO_GENERATE)); ?>
                                </div>
                            </td>
                            <td>
                                <div class="inputContainer" style="width:100%; display: none;">
                                    <input type="hidden" name="data[PurchaseOrder][purchase_request_id]" id="PurchaseOrderPurchaseRequestId" />
                                    <?php echo $this->Form->text('po_no', array('style' => 'width:70%')); ?>
                                    &nbsp;&nbsp; <img alt="Search" align="absmiddle" style="cursor: pointer; width:22px; height: 22px;" id="searchPONumber" onmouseover="Tip('<?php echo GENERAL_SEARCH; ?>')" src="<?php echo $this->webroot . 'img/button/search.png'; ?>" />
                                    <img alt="Delete" align="absmiddle" id="deletePONumber" onmouseover="Tip('<?php echo ACTION_DELETE; ?>')" src="<?php echo $this->webroot . 'img/button/delete.png'; ?>" style="display:none; cursor: pointer;" />
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 35%;">
                    <table cellpadding="0" style="width: 100%">
                        <tr>
                            <td style="width: 50%"><?php if(count($companies) > 1){ ?><label for="PurchaseOrderCompanyId"><?php echo TABLE_COMPANY; ?></label> <span class="red">*</span><?php } ?></td>
                            <td><?php if(count($locationGroups) > 1){ ?><label for="PurchaseOrderLocationGroupId"><?php echo TABLE_LOCATION_GROUP; ?></label> <span class="red">*</span><?php } ?></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="inputContainer" style="width:100%; <?php if(count($companies) == 1){ ?>display: none;<?php } ?>">
                                    <select name="data[PurchaseOrder][company_id]" id="PurchaseOrderCompanyId" class="validate[required]" style="width: 75%;">
                                        <?php
                                        if(count($companies) != 1){
                                        ?>
                                        <option vat-d="" value="" vat-opt=""><?php echo INPUT_SELECT; ?></option>
                                        <?php
                                        }
                                        foreach($companies AS $company){
                                            $sqlVATDefault = mysql_query("SELECT vat_modules.vat_setting_id FROM vat_modules INNER JOIN vat_settings ON vat_settings.company_id = ".$company['Company']['id']." AND vat_settings.is_active = 1 AND vat_settings.id = vat_modules.vat_setting_id WHERE vat_modules.is_active = 1 AND vat_modules.apply_to = 21 GROUP BY vat_modules.vat_setting_id LIMIT 1");
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
                <td rowspan="2" style="vertical-align: top;">
                    <table cellpadding="0" style="width: 100%;">
                        <tr>
                            <td><label for="PurchaseOrderNote"><?php echo TABLE_MEMO; ?></label></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="inputContainer" style="width:100%">
                                    <?php echo $this->Form->input('note', array('style' => 'width:90%; height: 65px;', 'label' => false)); ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr> 
            <tr>
                <td>
                    <table cellpadding="0" style="width: 100%">
                        <tr>
                            <td colspan="2"><label for="PurchaseOrderVendorName"><?php echo TABLE_VENDOR; ?></label> <span class="red">*</span></td>
                            <td style="width: 33%"></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="inputContainer" style="width:100%">
                                    <?php
                                    echo $this->Form->hidden('vendor_id');
                                    if($allowAddVendor){
                                    ?>
                                    <div class="addnewSmall" style="float: left;">
                                        <?php echo $this->Form->text('vendor_name', array('class' => 'validate[required]', 'style' => 'width: 285px; border: none;')); ?>
                                        <img alt="<?php echo MENU_VENDOR_ADD; ?>" align="absmiddle" style="cursor: pointer; width: 16px;" id="addVendorPurchaseBill" onmouseover="Tip('<?php echo MENU_VENDOR_ADD; ?>')" src="<?php echo $this->webroot . 'img/button/plus.png'; ?>" />
                                    </div>
                                    <?php
                                    } else {
                                        echo $this->Form->text('vendor_name', array('class' => 'validate[required]', 'style' => 'width:320px'));
                                    }
                                    ?>
                                    &nbsp;&nbsp;<img alt="<?php echo TABLE_SHOW_VENDOR_LIST; ?>" align="absmiddle" style="cursor: pointer; width:22px; height: 22px;" id="searchVendor" onmouseover="Tip('<?php echo TABLE_SHOW_VENDOR_LIST; ?>')" src="<?php echo $this->webroot . 'img/button/search.png'; ?>" />
                                    <img alt="<?php echo ACTION_REMOVE; ?>" align="absmiddle" id="deleteSearchVendorPB" onmouseover="Tip('<?php echo ACTION_REMOVE; ?>')" src="<?php echo $this->webroot . 'img/button/pos/remove-icon-png-25.png'; ?>" style="display:none; cursor: pointer; height: 22px;" />
                                    <div style="clear: both;"></div>
                                </div>
                            </td>
                            <td>
                                
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="vertical-align: top;">
                    <table cellpadding="0" style="width: 100%;">
                        <tr>
                            <td style="width: 50%;"><?php if(count($branches) > 1){ ?><label for="PurchaseOrderBranchId"><?php echo MENU_BRANCH; ?> <span class="red">*</span></label><?php } ?></td>
                            <td><?php if(count($locations) > 1){ ?><label for="PurchaseOrderLocationId"><?php echo TABLE_LOCATION; ?></label> <span class="red">*</span><?php } ?></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">
                                <div class="inputContainer" style="width:100%; <?php if(count($branches) == 1){ ?>display: none;<?php } ?>">
                                    <select name="data[PurchaseOrder][branch_id]" id="PurchaseOrderBranchId" class="validate[required]" style="width: 75%;">
                                        <?php
                                        if(count($branches) != 1){
                                        ?>
                                        <option value="" com="" mcode="" currency="" symbol=""><?php echo INPUT_SELECT; ?></option>
                                        <?php
                                        }
                                        foreach($branches AS $branch){
                                        ?>
                                        <option value="<?php echo $branch['Branch']['id']; ?>" com="<?php echo $branch['Branch']['company_id']; ?>" mcode="<?php echo $branch['ModuleCodeBranch']['pb_code']; ?>" currency="<?php echo $branch['Branch']['currency_center_id']; ?>" symbol="<?php echo $branch['CurrencyCenter']['symbol']; ?>"><?php echo $branch['Branch']['name']; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="inputContainer" style="width:100%; <?php if(count($locations) == 1){ ?>display: none;<?php } ?>">
                                    <select name="data[PurchaseOrder][location_id]" id="PurchaseOrderLocationId" class="validate[required]" style="width: 75%;">
                                        <?php
                                        if(count($locations) != 1){
                                        ?>
                                        <option value="" location-group="0"><?php echo INPUT_SELECT; ?></option>
                                        <?php 
                                        }
                                        foreach($locations AS $location){
                                        ?>
                                            <option value="<?php echo $location['Location']['id']; ?>" location-group="<?php echo $location['Location']['location_group_id']; ?>"><?php echo $location['Location']['name']; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </fieldset>
</div>
<div class="inputContainer" style="width:100%" id="searchFormPB">
    <table width="100%">
        <tr>
            <td style="width: 400px; text-align: left;">
                <?php
                if($allowAddProduct){
                ?>
                <div class="addnew">
                    <input type="text" id="purchaseSearchSKU" style="width:360px; height: 25px; border: none; background: none;" placeholder="<?php echo TABLE_SEARCH_SKU_NAME; ?>" />
                    <img alt="<?php echo MENU_PRODUCT_MANAGEMENT_ADD; ?>" align="absmiddle" style="cursor: pointer; width: 20px;" id="addProductPurchaseBill" onmouseover="Tip('<?php echo MENU_PRODUCT_MANAGEMENT_ADD; ?>')" src="<?php echo $this->webroot . 'img/button/plus-32.png'; ?>" />
                </div>
                <?php
                } else {
                ?>
                <input type="text" id="purchaseSearchSKU" style="width:90%; height: 25px;" placeholder="<?php echo TABLE_SEARCH_SKU_NAME; ?>" />
                <?php
                }
                ?>
            </td>
            <td id="divSearchPB" style="width: 200px; text-align: left;">
                &nbsp;&nbsp;<img alt="<?php echo TABLE_SHOW_PRODUCT_LIST; ?>" align="absmiddle" style="cursor: pointer;" id="purchaseSearchProduct" onmouseover="Tip('<?php echo TABLE_SHOW_PRODUCT_LIST; ?>')" src="<?php echo $this->webroot . 'img/button/search.png'; ?>" />
                <?php
                if ($allowAddService) {
                ?>
                <img alt="<?php echo SALES_ORDER_ADD_SERVICE; ?>" style="cursor: pointer;" align="absmiddle" class="addServicePB" onmouseover="Tip('<?php echo SALES_ORDER_ADD_SERVICE; ?>')" src="<?php echo $this->webroot . 'img/button/service.png'; ?>" /> 
                <?php
                }
                if ($allowAddMisc) {
                ?>
                &nbsp;&nbsp;<img alt="<?php echo SALES_ORDER_ADD_MISCELLANEOUS; ?>" style="cursor: pointer; height: 32px;" align="absmiddle" class="addMiscellaneousPB" onmouseover="Tip('<?php echo SALES_ORDER_ADD_MISCELLANEOUS; ?>')" src="<?php echo $this->webroot . 'img/button/misc.png'; ?>" />
                <?php
                }
                ?>
            </td>
            <td style="text-align: right;">
                <div id="exchangePurchase" style="display: none;">Exchange: 1<span id="companySymbolPurchase"></span> = <span id="divRatePurchase"></span></div>
            </td>
        </tr>
    </table>
</div>
<div id="hiddenUom" style="display: none"></div>
<table id="tblHeaderPB" class="table" cellspacing="0" style="padding:0px;">
    <tr>
        <th class="first" style="width:4%"><?php echo TABLE_NO; ?></th>
        <th style="width:10%;"><?php echo TABLE_BARCODE; ?></th>
        <th style="width:19%;"><?php echo TABLE_PRODUCT_NAME; ?></th>
        <th style="width:8%; <?php if($rowSettingUomDetail[0] == 0){ ?>display: none;<?php } ?>"><?php echo TABLE_LOTS_NO; ?></th>
        <th style="width:8%;"><?php echo TABLE_EXP_DATE_SHORT; ?></th>
        <th style="width:5%;"><?php echo TABLE_QTY; ?></th>
        <th style="width:5%;"><?php echo TABLE_F_O_C; ?></th>
        <th style="width:9%;"><?php echo TABLE_UOM; ?></th>
        <?php if($allowShowUnitCost){ ?>
        <th style="width:8%;"><?php echo TABLE_UNIT_COST; ?></th>
        <?php } ?>
        <th style="width:7%;"><?php echo GENERAL_DISCOUNT; ?></th>
        <?php if($allowShowUnitCost){ ?>
        <th style="width:7%;"><?php echo TABLE_TOTAL; ?></th>
        <?php } ?>
        <th style="width:3%;"></th>
    </tr>
</table>
<div id="bodyListPB" style="padding:0px;">
    <table id="tblPB" class="table" cellspacing="0" style="padding:0px;">
        <tr id="detailPB" class="listBodyPB" style="visibility: hidden;">
            <td class="first" style="width:4%"></td>
            <td style="width:10%"><input type="text" readonly="" style="width: 95%; height: 25px;" class="purchasePUC" /></td>
            <td style="width:19%">
                <div class="inputContainer" style="width:100%">
                    <input type="hidden" name="product_id[]" class="product_id" id="product_id" />
                    <input type="hidden" name="service_id[]" class="service_id" id="service_id" />
                    <input type="hidden" name="max_order[]" class="max_order" />
                    <input type="hidden" name="note[]" class="note" id="note" />
                    <input type="text" name="product_name[]" class="product_name validate[required]" id="product_name" readonly="readonly" style="width: 75%; height: 25px;" />
                    <img alt="Note" src="<?php echo $this->webroot . 'img/button/note.png'; ?>" class="noteAddPB" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Note')" />
                    <img alt="Information" src="<?php echo $this->webroot . 'img/button/view.png'; ?>" class="btnProductInfo" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Information')" />
                </div>
            </td>
            <td style="padding:0px; text-align: center; width:8%; <?php if($rowSettingUomDetail[0] == 0){ ?>display: none;<?php } ?>">
                <div class="inputContainer" style="width:100%">
                    <input type="text" name="lots_number[]" id="lots_number" style="width:80%; height: 25px;" class="lots_number" />
                </div>
            </td>
            <td style="padding:0px; text-align: center; width:8%;">
                <div class="inputContainer" style="width:100%">
                    <input type="text" name="date_expired[]" id="date_expired" style="width:80%; height: 25px;" class="date_expired"​ readonly="" />
                </div>
            </td>
            <td style="padding:0px; text-align: center; width:5%;">
                <div class="inputContainer" style="width:100%">
                    <input type="text" id="qty" name="qty[]" style="width:80%; height: 25px;" class="qty validate[required]" />
                </div>
            </td>
            <td style="padding:0px; text-align: center; width:5%;">
                <div class="inputContainer" style="width:100%">
                    <input type="text" id="qty_free" name="qty_free[]" style="width:80%; height: 25px;" class="qty_free" />
                </div>
            </td>
            <td style="padding:0px; text-align: center; width:9%;">
                <div class="inputContainer" style="width:100%">         
                    <input type="hidden" class="small_uom_val_pb" name="small_uom_val_pb[]"/> 
                    <input type="hidden" class="pb_conversion" name="pb_conversion[]"/>                                        
                    <select id="qty_uom_id" name="qty_uom_id[]" style="width:80%; height: 25px;" class="qty_uom_id validate[required]">
                        <?php
                        foreach ($uoms as $uom) {
                            echo "<option value='{$uom['Uom']['id']}' conversion='1'>{$uom['Uom']['name']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </td>
            <?php 
                $display ='display:none;';
                if($allowShowUnitCost){
                    $display = '';
                }
            ?>
            <td style="padding:0px; text-align: center; width:8%;<?php echo $display;?>">
                <div class="inputContainer" style="width:100%">
                    <input type="hidden" class="defaltCost" />
                    <input type="text" id="unit_cost" name="unit_cost[]" class="unit_cost validate[required] float" style="width:80%; height: 25px;" />
                </div>
            </td>
            <td style="padding:0px; text-align: center; width:7%;">
                <div class="inputContainer" style="width:100%">
                    <div style="white-space: nowrap; margin-top: 3px; width: 100%">
                        <input type="hidden" name="discount_id[]" />
                        <input type="hidden" name="discount_amount[]" />
                        <input type="hidden" name="discount_percent[]" />
                        <?php
                        if($allowDiscount){
                        ?>
                        <input type="text" name="discount[]" value="0" class="discountPB btnDiscountPB float" readonly="readonly" id="discountPB" style="width: 70%; height: 25px;" />
                        <img alt="Remove" src="<?php echo $this->webroot . 'img/button/cross.png'; ?>" class="btnRemoveDiscountPB" align="absmiddle" style="cursor: pointer; display: none;" onmouseover="Tip('Remove')" />
                        <?php
                        }else{
                        ?>
                        <input type="hidden" name="discount[]" value="0" class="discountPB btnDiscountPB float" readonly="readonly" id="discountPB" />
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </td>
            <td style="padding:0px; text-align: center; width:7%;<?php echo $display;?>">
                <input type="hidden" id="h_total_cost" class="h_total_cost float" name="h_total_cost[]" />
                <input type="text" name="total_cost[]" id="total_cost" style="width:80%; height: 25px;" class="total_cost float" />
            </td>
            <td style="white-space:nowrap; padding:0px; text-align:center; width:3%;">
                <img alt="" src="<?php echo $this->webroot . 'img/button/cross.png'; ?>" class="btnRemovePB" style="cursor: pointer;" onmouseover="Tip('Remove')" />
            </td>
        </tr>
    </table>
</div>
<div id="POFooter">
    <div style="float: left; width: 15%;">
        <div class="buttons">
            <a href="#" class="positive btnBackPurchaseOrder">
                <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
                <?php echo ACTION_BACK; ?>
            </a>
        </div>
        <div class="buttons">
            <button type="submit" class="positive btnSavePurchaseBill">
                <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
                <span class="txtSavePB"><?php echo ACTION_SAVE; ?></span>
            </button>
        </div>
    </div>
    <div style="float: right; width:83%; vertical-align: bottom;">
        <table cellpadding="0" style="width:100%; padding: 0px; margin: 0px;">
            <tr>
                <td style="width:7%; text-align: right;"><label for="PurchaseOrderTotalAmount"><?php echo TABLE_SUB_TOTAL; ?>:</label></td>
                <td style="width:11%">
                    <?php echo $this->Form->text('total_amount', array('name'=>'data[PurchaseOrder][total_amount]', 'readonly' => true, 'style' => 'width: 80%; height:15px; font-size:12px; font-weight: bold', 'value'=> '0.00')); ?> <span class="lblSymbolPB"></span>
                </td>
                <td style="width:6%; text-align: right;"><label for="PurchaseOrderTotalAmount"><?php echo GENERAL_DISCOUNT; ?>:</label></td>
                <td style="width:17%">
                    <div class="inputContainer" style="width:100%">
                        <?php echo $this->Form->hidden('discount_percent', array('class' => 'float', 'value'=>'0')); ?>
                        <?php echo $this->Form->text('discount_amount', array('style' => 'width: 50%; height:15px; font-size:12px; font-weight: bold', 'class' => 'float', 'value'=>'0', 'readonly' => true)); ?> <span class="lblSymbolPB"></span>
                        <span id="PBLabelDisPercent"></span>
                        <?php if($allowEditInvDis){ ?><img alt="Remove" src="<?php echo $this->webroot . 'img/button/cross.png'; ?>" id="btnRemovePBTotalDiscount" align="absmiddle" style="cursor: pointer; display: none;" onmouseover="Tip('Remove Discount')" /><?php } ?>
                    </div>
                </td>
                <td style="width:17%; text-align: right;">
                    <label for="PurchaseOrderVatSettingId" id="lblPurchaseOrderVatSettingId"><?php echo TABLE_VAT; ?> <span class="red">*</span>:</label>
                    <select id="PurchaseOrderVatSettingId" name="data[PurchaseOrder][vat_setting_id]" style="width: 75%;" class="validate[required]">
                        <option com-id="" value="" rate="0.00"><?php echo INPUT_SELECT; ?></option>
                        <?php
                        // VAT
                        $sqlVat = mysql_query("SELECT id, name, vat_percent, company_id, chart_account_id FROM vat_settings WHERE is_active = 1 AND type = 2 AND company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id'].");");
                        while($rowVat = mysql_fetch_array($sqlVat)){
                        ?>
                        <option com-id="<?php echo $rowVat['company_id']; ?>" value="<?php echo $rowVat['id']; ?>" rate="<?php echo $rowVat['vat_percent']; ?>" acc="<?php echo $rowVat['chart_account_id']; ?>"><?php echo $rowVat['name']; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </td>
                <td style="width:7%">
                    <input type="hidden" value="" name="data[PurchaseOrder][vat_chart_account_id]" id="PurchaseOrderVatChartAccountId" />
                    <input type="hidden" value="0" name="data[PurchaseOrder][total_vat]" id="PurchaseOrderTotalVat" />
                    <?php echo $this->Form->text('vat_percent', array('name'=>'data[PurchaseOrder][vat_percent]', 'readonly' => true, 'style' => 'width: 50%; height:15px; font-size:12px; font-weight: bold', 'value'=> 0)); ?> <span class="lblSymbolPBPercent">(%)</span>
                </td>
                <td style="width:7%; text-align: right;"><label for="PurchaseOrderGrandTotalAmount"><?php echo TABLE_TOTAL; ?>:</label></td>
                <td style="width:11%">
                    <?php echo $this->Form->text('grand_total_amount', array('readonly' => true, 'style' => 'width: 80%; height:15px; font-size:12px; font-weight: bold', 'value'=> '0.00')); ?> <span class="lblSymbolPB"></span>
                </td>
            </tr>
        </table>
    </div>
    <div style="clear: both;"></div>
</div>
<?php echo $this->Form->end(); ?>