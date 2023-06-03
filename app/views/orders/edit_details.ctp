<?php
// Authentication
$this->element('check_access');
$allowaddService = checkAccess($user['User']['id'], $this->params['controller'], 'service');
$allowAddMisc = checkAccess($user['User']['id'], $this->params['controller'], 'miscellaneous');
$allowEditPrice = checkAccess($user['User']['id'], $this->params['controller'], 'editPrice');
$allowDiscount = checkAccess($user['User']['id'], $this->params['controller'], 'discount');
include("includes/function.php");
?>
<script type="text/javascript">
    var tblRowOrder  = $("#OrderListPrescription");
    var indexRowOrder   = 0;
    var searchCodeOrder = 1;
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        $("#OrderListPrescription").remove();
        checkEventOrder();
        var waitForFinalEventOrder = (function () {
          var timersOrder = {};
          return function (callback, ms, uniqueId) {
            if (!uniqueId) {
              uniqueId = "Don't call this twice without a uniqueId";
            }
            if (timersOrder[uniqueId]) {
              clearTimeout (timersOrder[uniqueId]);
            }
            timersOrder[uniqueId] = setTimeout(callback, ms);
          };
        })();
        
        // Click Tab Refresh Form List: Screen, Title, Scroll
        if(tabOrderReg != tabOrderId){
            $("a[href='"+tabOrderId+"']").click(function(){
                if($(".orderDetailOrder").html() != '' && $(".orderDetailOrder").html() != null){
                    waitForFinalEventOrder(function(){
                        refreshScreenOrder();
                        resizeFormTitleOrder();
                        resizeFornScrollOrder();
                    }, 500, "Finish");
                }
            });
            tabOrderReg = tabOrderId;
        }
        
        waitForFinalEventOrder(function(){
                refreshScreenOrder();
                resizeFormTitleOrder();
                resizeFornScrollOrder();
        }, 500, "Finish");
        
        $(window).resize(function(){
            if(tabOrderReg == $(".ui-tabs-selected a").attr("href")){
                waitForFinalEventOrder(function(){
                    refreshScreenOrder();
                    resizeFormTitleOrder();
                    resizeFornScrollOrder();
                }, 500, "Finish");
            }
        });
        
        // Hide / Show Header
        $("#btnHideShowHeaderOrder").click(function(){
            var OrderCompanyId = $("#OrderCompanyId").val();
            var OrderBranchId  = $("#OrderBranchId").val();
            var OrderOrderDate = $("#OrderOrderDate").val();
            var OrderCustomerId = $("#OrderCustomerId").val();
            
            if(OrderCompanyId == "" || OrderOrderDate == "" || OrderCustomerId == "" || OrderBranchId == ""){
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
                    $("#orderHeaderForm").hide();
                    img += 'arrow-down.png';
                } else {
                    action = 'Hide';
                    $("#orderHeaderForm").show();
                    img += 'arrow-up.png';
                }
                $(this).find("span").text(action);
                $(this).find("img").attr("src", img);
                if(tabOrderReg == $(".ui-tabs-selected a").attr("href")){
                    waitForFinalEventOrder(function(){
                        resizeFornScrollOrder();
                    }, 500, "Finish");
                }
            }
        });
        
        $("#SearchProductSkuPrescription").autocomplete("<?php echo $this->base . "/orders/searchProduct/"; ?>", {
            width: 400,
            formatItem: function(data, i, n, value) {
                return value.split(".*")[0]+"-"+value.split(".*")[1];
            },
            formatResult: function(data, value) {
                return value.split(".*")[0]+"-"+value.split(".*")[1];
            }
        }).result(function(event, value){
            var code = value.toString().split(".*")[0];
            $("#SearchProductSkuPrescription").val(code);
            if(searchCodeOrder == 1){
                searchCodeOrder = 2;
                searchProductByCodeOrder(code, '', 1);
            }
        });
        
        $("#SearchProductSkuPrescription").keypress(function(e){
            if((e.which && e.which == 13) || e.keyCode == 13){
                if(searchCodeOrder == 1){
                    searchCodeOrder = 2;
                    searchProductByCodeOrder($(this).val(), '', 1);
                }
                return false;
            }
        });
        
        $("#SearchProductPucOrder").keypress(function(e){
            if((e.which && e.which == 13) || e.keyCode == 13){
                if(searchCodeOrder == 1){
                    searchCodeOrder = 2;
                    searchProductByCodeOrder($(this).val(), '', 1);
                }
                return false;
            }
        });
        
        // Search Product
        $(".searchProductListOrder").click(function(){
            if(searchCodeOrder == 1){
                searchCodeOrder = 2;
                searchProductListOrder();
            }
        });
        // Search Service
        $(".addServiceOrder").click(function(){
            if(searchCodeOrder == 1){
                searchCodeOrder = 2;
                searchAllServiceOrder();
            }
        });
        // Search Misc
        $(".addMiscellaneousOrder").click(function(){
            if(searchCodeOrder == 1){
                searchCodeOrder = 2;
                searchAllMiscOrder();
            }
        });
        
        // Change Price Type
        $.cookie("typePriceOrder", $("#typeOfPriceOrder").val(), {expires : 7,path    : '/'});
        $("#typeOfPriceOrder").change(function(){
            if($(".tblPrescriptionList").find(".product_id").val() == undefined){
                changePriceTypeOrder();
            } else {
                var question = "<?php echo MESSAGE_CONFIRM_CHANGE_PRICE_TYPE; ?>";
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
                            changePriceTypeOrder();
                            $.cookie("typePriceOrder", $("#typeOfPriceOrder").val(), {expires : 7,path    : '/'});
                            $(this).dialog("close");
                        },
                        '<?php echo ACTION_CANCEL; ?>': function() {
                            $("#typeOfPriceOrder").val($.cookie('typePriceOrder'));
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
        
        <?php  
        if(!empty($order)) {
            $priceTypeId = $order['Order']['price_type_id'];
            //$sqlPriceType = mysql_query("SELECT GROUP_CONCAT(price_type_id) FROM cgroup_price_types WHERE cgroup_id IN (SELECT cgroup_id FROM customer_cgroups WHERE customer_id = ".$order['Order']['customer_id']." GROUP BY cgroup_id)");
            //$rowPriceType = mysql_fetch_array($sqlPriceType);
        ?>
        // Check Price Type With Company
        //checkPriceTypeOrder();
        // Put label VAT Calculate
        //changeLblVatCalOrder();
        // Check Customer Price Type
        //customerPriceTypeOrder('<?php //echo $rowPriceType[0]; ?>', <?php //echo $priceTypeId; ?>);
        <?php
        } else {
            $priceTypeId = '';
        ?>
        // Change Input CSS
        //changeInputCSSOrder();
        <?php
        }
        ?>
        $(".noteAddOrder").click(function(){
            addNoteOrder($(this));
        });       
                
    }); // End Document Ready
    
    function changePriceTypeOrder(){
        if($(".product").val() != undefined && $(".product").val() != ''){
            var priceType  = parseFloat(replaceNum($("#typeOfPriceOrder").val()));
            $(".tblPrescriptionList").each(function(){
                if($(this).find("input[name='product_id[]']").val() != ''){
                    var unitPrice = replaceNum($(this).closest("tr").find(".qty_uom_id").find("option:selected").attr("price-uom-"+priceType));
                    $(this).find(".unit_price").val(converDicemalJS(unitPrice).toFixed(2));
                    calculateTotalRowQu($(this).find("input[name='product_id[]']"));
                }
            });
        }
    }
    
    function searchProductListOrder(){
        searchCodeOrder = 1;
        if($("#OrderCustomerName").val() == "" || $("#OrderCompanyId").val() == "" || $("#OrderBranchId").val() == ""){
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
            var dateOrder = $("#OrderOrderDate").val().split("/")[2]+"-"+$("#OrderOrderDate").val().split("/")[1]+"-"+$("#OrderOrderDate").val().split("/")[0];
            $.ajax({
                type:   "POST",
                url:    "<?php echo $this->base . "/orders/product/"; ?>"+$("#OrderCompanyId").val()+"/"+$("#OrderBranchId").val(),
                data:   "order_date="+dateOrder,
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
                        height: 500,
                        position:'center',
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show();
                        },
                        buttons: {
                            '<?php echo ACTION_OK; ?>': function() {
                                if($("input[name='chkProduct']:checked").val()){
                                    searchCodeOrder = 2;
                                    searchProductByCodeOrder($("input[name='chkProduct']:checked").val(), '', 1);
                                }
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            });
        }
    }
    
    function searchAllServiceOrder(){
        searchCodeOrder = 1;
        if($("#OrderCompanyId").val() == "" || $("#OrderCustomerName").val() == "" || $("#OrderBranchId").val() == ""){
            $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_SELECT_FIELD_REQURIED; ?></p>');
            $("#dialog").dialog({
                title: '<?php echo DIALOG_WARNING; ?>',
                resizable: false,
                modal: true,
                width: 'auto',
                height: 'auto',
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
        }else{
            $.ajax({
                type:   "POST",
                url:    "<?php echo $this->base . "/orders/service"; ?>/"+$("#OrderCompanyId").val()+"/"+$("#OrderBranchId").val(),
                beforeSend: function(){
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(msg){
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $("#dialog").html(msg).dialog({
                        title: '<?php echo SALES_ORDER_ADD_SERVICE; ?>',
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
                                var formName = "#ServiceServiceForm";
                                var validateBack =$(formName).validationEngine("validate");
                                if(!validateBack){
                                    return false;
                                }else{
                                    searchCodeOrder = 2;
                                    addNewServiceOrder();
                                }
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            });
        }
    }
    
    function searchAllMiscOrder(){
        searchCodeOrder = 1;
        if($("#OrderCompanyId").val() == "" || $("#OrderCustomerName").val() == "" || $("#OrderBranchId").val() == ""){
            $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_SELECT_FIELD_REQURIED; ?></p>');
            $("#dialog").dialog({
                title: '<?php echo DIALOG_WARNING; ?>',
                resizable: false,
                modal: true,
                width: 'auto',
                height: 'auto',
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
        }else{
            $.ajax({
                type:   "POST",
                url:    "<?php echo $this->base . "/orders/miscellaneous"; ?>",
                beforeSend: function(){
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(msg){
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $("#dialog").html(msg).dialog({
                        title: '<?php echo SALES_ORDER_ADD_NEW_MISCELLANEOUS; ?>',
                        resizable: false,
                        modal: true,
                        width: 800,
                        height: 'auto',
                        position:'center',
                        closeOnEscape: true,
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show(); $(".ui-dialog-titlebar-close").show();
                        },
                        buttons: {
                            '<?php echo ACTION_OK; ?>': function() {
                                var formName = "#MiscellaneousMiscellaneousForm";
                                var validateBack =$(formName).validationEngine("validate");
                                if(!validateBack){
                                    return false;
                                }else{
                                    searchCodeOrder = 2;
                                    addNewMiscOrder();
                                }
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            });
        }
    }
    
    function resizeFormTitleOrder(){
        var screen = 16;
        var widthList = $("#bodyListPrescription").width();
        $("#tblPrescriptionHeader").css('width',widthList);
        var widthTitle = widthList - screen;
        $("#tblPrescriptionHeader").css('padding','0px');
        $("#tblPrescriptionHeader").css('margin-top','5px');
        $("#tblPrescriptionHeader").css('width',widthTitle);
    }
    
    function resizeFornScrollOrder(){
        var windowHeight = $(window).height();
        var formHeader = 0;
        if ($('#orderHeaderForm').is(':hidden')) {
            formHeader = 0;
        } else {
            formHeader = $("#orderHeaderForm").height();
        }
        var btnHeader    = $("#btnHideShowHeaderOrder").height();
        var formFooter   = $(".footerSaveOrder").height();
        var formSearch   = $("#orderSearchForm").height();
        var tableHeader  = $("#tblPrescriptionHeader").height();
        var screenRemain = 223;
        var getHeight    = windowHeight - (formHeader + btnHeader + formFooter + formSearch + tableHeader + screenRemain);
        if(getHeight < 30){
           getHeight = 65; 
        }
        $("#bodyListPrescription").css('height',getHeight);
        $("#bodyListPrescription").css('padding','0px');
        $("#bodyListPrescription").css('width','100%');
        $("#bodyListPrescription").css('overflow-x','hidden');
        $("#bodyListPrescription").css('overflow-y','scroll');
    }
    
    function refreshScreenOrder(){
        $("#tblPrescriptionHeader").removeAttr('style');
    }

    function searchProductByCodeOrder(productCode, uomSelected, qtyOrder){
        if($("#OrderCompanyId").val() == "" || $("#OrderBranchId").val() == ""){
            //alert('Customer = '+$("#OrderCustomerId").val() + " Company =" + $("#OrderCompanyId").val() + '  branch = '+$("#OrderBranchId").val()) ; exit ;
            timeSearchOrder = 1;
            searchCodeOrder = 1;
            $("#SearchProductPucOrder").val("");
            $("#SearchProductSkuPrescription").val('');
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
        }else{
            var customerId = 0 ; 
            $("#OrderOrderDate").datepicker("option", "dateFormat", "yy-mm-dd");
            var orderDate = $("#OrderOrderDate").val();
            $.ajax({
                dataType: "json",
                type:   "POST",
                url:    "<?php echo $this->base . "/orders/searchProductByCode/"; ?>"+$("#OrderCompanyId").val()+"/"+customerId+"/"+$("#OrderBranchId").val(),
                data:   "data[code]="+productCode+"&order_date="+orderDate,
                beforeSend: function(){
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(msg){
                    
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $("#SearchProductPucOrder").val("");
                    $("#SearchProductSkuPrescription").val("");
                    if(msg.product_id != ""){
                        if(msg.packet != ''){
                            var packet = msg.packet.toString().split("--");
                            var loop = 1;
                            var time = 0;
                            $.each(packet,function(key, item){
                                var items = item.toString().split("||");
                                var productCode = items[0];
                                var uomSelected = items[1];
                                var qtyOrder    = items[2];
                                if(loop > 1){
                                    time += 300;
                                }
                                setTimeout(function () {
                                    searchProductByCodeOrder(productCode, uomSelected, qtyOrder, msg);
                                }, time);
                                loop++;
                            });
                        }else{
                            addProductToListOrder(uomSelected, qtyOrder, msg);
                        }
                    }else{
                        searchCodeOrder = 1;
                        $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo TABLE_NO_PRODUCT; ?></p>');
                        $("#dialog").dialog({
                            title: '<?php echo DIALOG_INFORMATION; ?>',
                            resizable: false,
                            modal: true,
                            width: '500',
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
                    }
                }
            });
        }
    }
    
    function eventKeyOrder(){
        loadAutoCompleteOff();
        $(".product, .qty, .unit_price, .qty_uom_id, .total_price, .discount, .btnRemoveDiscountQu, .btnRemoveOrderList, .btnProductInfo").unbind('click').unbind('keyup').unbind('keypress').unbind('change');
        $(".interger").autoNumeric({mDec: 0, aSep: ','});
        $(".float").autoNumeric({mDec: 3, aSep: ','});
        
        $(".expired_date").datepicker({
            dateFormat:'dd/mm/yy',
            changeMonth: true,
            changeYear: true
        }).unbind("blur");
        
        $(".btnRemoveOrderList").click(function(){
            var currentTr = $(this).closest("tr");
            $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>Are you sure to remove this item?</p>');
            $("#dialog").dialog({
                title: '<?php echo DIALOG_INFORMATION; ?>',
                resizable: false,
                position:'center',
                modal: true,
                width: 'auto',
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
                        getTotalAmountOrder();
                        sortNuTableOrder();
                        $(this).dialog("close");
                    }
                }
            });
        });
        
        $(".noteAddOrder").click(function(){
            addNoteOrder($(this));
        });
        
        $(".qty, .qty_free, .unit_price, .total_price").focus(function(){
            if($(this).val() == "0"){
                $(this).val("");
            }
        });
        
        $(".qty, .qty_free, .unit_price, .total_price").blur(function(){
            if($(this).val() == ""){
                $(this).val("0");
            }
        });

        $(".qty").keypress(function(e){
            if((e.which && e.which == 13) || e.keyCode == 13){
                $(this).closest("tr").find(".unit_price").select().focus();
                return false;
            }
        });

        $(".unit_price").keypress(function(e){
            if((e.which && e.which == 13) || e.keyCode == 13){
                if($(this).val() != ""){
                    if($(this).closest("tr").find(".qty_uom_id").find("option").val() != "none"){
                        $(this).closest("tr").find(".qty_uom_id").select().focus();
                    }else{
                        $("#SearchProductSkuPrescription").focus().select();
                    }
                }else{
                    $(this).select().focus();
                }
                return false;
            }
        });

        $(".qty_uom_id").keypress(function(e){
            if((e.which && e.which == 13) || e.keyCode == 13){
                $("#SearchProductSkuPrescription").select().focus();
                return false;
            }
        });

        $(".qty, .unit_price").keyup(function(){
            calculateTotalRowQu($(this));
        });
        
        $(".total_price").keyup(function(){
            var unitPrice       = 0;
            var discount        = 0;
            var totalPrice      = replaceNum($(this).val());
            var totalBfDis      = 0;
            var qty             = replaceNum($(this).closest("tr").find(".qty").val());
            var discountPercent = replaceNum($(this).closest("tr").find("input[name='discount_percent[]']").val());
            var discountAmount  = replaceNum($(this).closest("tr").find("input[name='discount_amount[]']").val());
            if(qty > 0){
                if(discountAmount != 0 && discountAmount != ''){
                    unitPrice = converDicemalJS((totalPrice + discountAmount) / qty);
                    discount  = discountAmount;
                }else if (discountPercent != 0 && discountPercent != ''){
                    unitPrice = converDicemalJS(( converDicemalJS((converDicemalJS(totalPrice * 100)) / (converDicemalJS(100 - discountPercent))) ) / qty);
                    discount  = converDicemalJS(( converDicemalJS((converDicemalJS(totalPrice * 100)) / (converDicemalJS(100 - discountPercent))) ) * (converDicemalJS(discountPercent / 100)) );
                }else{
                    unitPrice = converDicemalJS(totalPrice / qty);
                }
                totalBfDis = converDicemalJS(unitPrice * qty);
                $(this).closest("tr").find(".discount").val(discount.toFixed(2));
                $(this).closest("tr").find(".unit_price").val((unitPrice).toFixed(2));
                $(this).closest("tr").find(".total_price_bf_dis").val((totalBfDis).toFixed(2));
            }else{
                $(this).val(0);
            }
            calculateTotalRowQu($(this));
        });
        
        $(".qty_uom_id").change(function(){
            if($(this).closest("tr").find(".product_id").val() != '' && replaceNum($(this).closest("tr").find(".product_id").val()) > 0){
                var value         = replaceNum($(this).val());
                var uomConversion = replaceNum($(this).find("option[value='"+value+"']").attr('conversion'));
                var uomSmVal      = replaceNum($(this).find("option[data-sm='1']").attr('conversion'));
                var conversion    = converDicemalJS(uomSmVal / uomConversion);
                var priceType     = replaceNum($("#typeOfPriceOrder").val());
                var unitPrice     = replaceNum($(this).find("option:selected").attr("price-uom-"+priceType));
                var unitCost      = replaceNum($(this).find("option:selected").attr("cost-uom-"+priceType));
                $(this).closest("tr").find(".unit_cost").val(unitCost.toFixed(2));
                $(this).closest("tr").find(".unit_price").val(unitPrice.toFixed(2));
                $(this).closest("tr").find(".conversion").val(conversion);
            }
            calculateTotalRowQu($(this));
        });
        
        // Action Discount
        $(".discount").click(function(){
            <?php
            if($allowDiscount){
            ?>
            addNewDiscountOrder($(this).closest("tr"));
            <?php
            }
            ?>
        });
        
        $(".btnRemoveDiscountQu").click(function(){
            removeDiscountQu($(this).closest("tr"));
        });
        
        // Change Product Name With Customer
        $(".product").blur(function(){
            var proName    = $(this).val();
            var productId  = $(this).closest("tr").find("input[name='product_id[]']").val();
            var customerId = $("#OrderCustomerId").val();
            if(productId != '' && customerId != ''){
                $.ajax({
                    type:   "POST",
                    url:    "<?php echo $this->base . "/products/setProductWithCustomer/"; ?>"+productId+"/"+customerId,
                    data:   "data[name]="+proName,
                    beforeSend: function(){
                        $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                    },
                    success: function(msg){
                        $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    }
                });
            }
        });
        
        // Button Show Information
        $(".btnProductInfo").click(function(){
            showProductInfoOrder($(this));
        });
        
        moveRowOrder();
    }
    
    function moveRowOrder(){
        $(".btnMoveDownOrderList, .btnMoveUpOrderList").unbind('click');
        $(".btnMoveDownOrderList").click(function () {
            var rowToMove = $(this).parents('tr.tblPrescriptionList:first');
            var next = rowToMove.next('tr.tblPrescriptionList');
            if (next.length == 1) { next.after(rowToMove); }
            sortNuTableOrder();
        });

        $(".btnMoveUpOrderList").click(function () {
            var rowToMove = $(this).parents('tr.tblPrescriptionList:first');
            var prev = rowToMove.prev('tr.tblPrescriptionList');
            if (prev.length == 1) { prev.before(rowToMove); }
            sortNuTableOrder();
        });
        
        sortNuTableOrder();
    }
    
    function addNewDiscountOrder(tr){
        if($("#OrderCompanyId").val() != ''){
            $.ajax({
                type:   "POST",
                url:    "<?php echo $this->base . "/orders/discount"; ?>/"+$("#OrderCompanyId").val(),
                beforeSend: function(){
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(msg){
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $("#dialog").html(msg).dialog({
                        title: '<?php echo SALES_ORDER_SELECT_DISCOUNT; ?>',
                        resizable: false,
                        modal: true,
                        width: 800,
                        height: 550,
                        position:'center',
                        closeOnEscape: true,
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show(); 
                            $(".ui-dialog-titlebar-close").show();
                        },
                        buttons: {
                            '<?php echo ACTION_OK; ?>': function() {
                                var discountTr = $("input[name='chkDiscount']:checked").closest("tr");
                                if(discountTr != "" && discountTr != undefined){
                                    tr.find("input[name='discount_id[]']").val(discountTr.find("input[name='chkDiscount']").val());
                                    tr.find("input[name='discount_amount[]']").val(discountTr.find("input[name='salesOrderDiscountAmount']").val());
                                    tr.find("input[name='discount_percent[]']").val(discountTr.find("input[name='salesOrderDiscountPercent']").val());
                                    tr.find("input[name='discount[]']").css("display", "inline");
                                    tr.find(".btnRemoveDiscountQu").css("display", "inline");
                                    calculateTotalRowQu(tr);
                                }
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            });
        }
    }
    
    function removeDiscountQu(tr){
        tr.find("input[name='discount_id[]']").val("");
        tr.find("input[name='discount_amount[]']").val(0);
        tr.find("input[name='discount_percent[]']").val(0);
        tr.find("input[name='discount[]']").val(0);
        tr.find(".btnRemoveDiscountQu").css("display", "none");
        calculateTotalRowQu(tr);
    }

    function addProductToListOrder(uomSelected, qtyOrder, msg){
        indexRowOrder = Math.floor((Math.random() * 100000) + 1);
        // Product Information
        var productId    = msg.product_id;
        var productPUC   = msg.product_barcode;
        var productSku   = msg.product_code;
        var productName  = msg.product_name;
        var proCusName   = msg.product_cus_name;
        var productUomId = msg.product_uom_id;
        var smallUomVal  = msg.small_uom_val;
        var tr           = tblRowOrder.clone(true);
        var productInfo  = showOriginalNameOrder(productPUC, productSku, productName);
        var branchId     = $("#OrderBranchId").val();
        
        tr.removeAttr("style").removeAttr("id");
        tr.find("td:eq(0)").html(indexRowOrder);
        tr.find(".lblSKU").html(productSku);
        tr.find("input[name='product_id[]']").val(productId);
        tr.find(".orgProName").val(productInfo);
        tr.find("input[name='product[]']").attr("id", "product_"+indexRowOrder).val(proCusName).removeAttr('readonly');
        tr.find("input[name='qty[]']").attr("id", "qty_"+indexRowOrder).val(qtyOrder).attr('readonly', true);
        tr.find("input[name='qty_free[]']").attr("id", "qty_free_"+indexRowOrder).val(0);
        tr.find("select[name='qty_uom_id[]']").attr("id", "qty_uom_id_"+indexRowOrder).html('<option value="">Please Select Uom</option>');
        tr.find(".conversion").attr("id", "conversion_"+indexRowOrder).val(smallUomVal);
        tr.find(".discount").attr("id", "discount_"+indexRowOrder).val(0);
        $.ajax({
            type:   "POST",
            url:    "<?php echo $this->base . "/uoms/getRelativeUom/"; ?>"+productUomId+"/all/"+productId+"/"+branchId,
            success: function(msg){
                var complete = false;
                var productPrice = 0;
                var unitCost = 0;
                tr.find("select[name='qty_uom_id[]']").html(msg).val(1);
                tr.find("select[name='qty_uom_id[]']").find("option").each(function(){
                    if($(this).attr("conversion") == 1){
                        if($(this).attr("conversion") == 1 && uomSelected == ''){
                            $(this).attr("selected", true);
                            // Price
                            var priceType     = $("#typeOfPriceOrder").val();
                            var lblPrice      = "price-uom-"+priceType;
                            productPrice  = parseFloat($(this).attr(lblPrice));
                            var lblCost       = "cost-uom-"+priceType;
                            unitCost      = parseFloat($(this).attr(lblCost));
                            complete = true;
                        } else{
                            if(parseFloat($(this).val()) == parseFloat(uomSelected)){
                                $(this).attr("selected", true);
                                // Price
                                var priceType     = $("#typeOfPriceOrder").val();
                                var lblPrice      = "price-uom-"+priceType;
                                productPrice  = parseFloat($(this).attr(lblPrice));
                                var lblCost       = "cost-uom-"+priceType;
                                unitCost      = parseFloat($(this).attr(lblCost));
                                complete = true;
                            }
                        }
                        if(complete == true){
                            var totalPrice = converDicemalJS(productPrice * parseFloat(qtyOrder));
                            tr.find("input[name='unit_cost[]']").val(unitCost);
                            tr.find("input[name='unit_price[]']").attr("id", "unit_price_"+indexRowOrder).val(productPrice);
                            tr.find("input[name='total_price_bf_dis[]']").val(totalPrice);
                            tr.find("input[name='total_price[]']").attr("id", "total_price_"+indexRowOrder).val(totalPrice);
                            checkEventOrder();
                            getTotalAmountOrder();
                            if(unitCost > productPrice){
                                tr.find(".priceDownOrder").show();
                                tr.find(".unit_price").css("color", "red");
                            } else {
                                tr.find(".priceDownOrder").hide();
                                tr.find(".unit_price").css("color", "#000");
                            }
                            tr.find("input[name='qty[]']").removeAttr('readonly');
                            tr.find("input[name='qty[]']").select().focus();
                        }
                        return false;
                    }
                });
            }
        });
        $("#tblPrescription").append(tr);
        sortNuTableOrder();
        searchCodeOrder = 1;
    }
    
    function addNewServiceOrder(){
        indexRowOrder = Math.floor((Math.random() * 100000) + 1);
        // Service Information
        var serviceId    = $("#ServiceServiceId").val();
        var serviceCode  = $("#ServiceServiceId").find("option:selected").attr('scode');
        var serviceName  = $("#ServiceServiceId").find("option:selected").attr('abbr');
        var servicePrice = $("#ServiceUnitPrice").val();
        var serviceUomId = $("#ServiceServiceId").find("option:selected").attr('suom');
        var tr           = tblRowOrder.clone(true);
        
        tr.removeAttr("style").removeAttr("id");
        tr.find("td:eq(0)").html(indexRowOrder);
        tr.find(".lblSKU").html(serviceCode);
        tr.find("input[name='product_id[]']").val('');
        tr.find("input[name='service_id[]']").val(serviceId);
        tr.find("input[name='product[]']").attr("id", "product_"+indexRowOrder).val(serviceName);
        tr.find("input[name='qty[]']").attr("id", "qty_"+indexRowOrder).val(1);
        tr.find("input[name='qty_free[]']").attr("id", "qty_free_"+indexRowOrder).val(0);
        tr.find(".conversion").attr("id", "conversion_"+indexRowOrder).val(1);
        tr.find(".discount").attr("id", "discount_"+indexRowOrder).val(0);
        tr.find("input[name='unit_price[]']").attr("id", "unit_price_"+indexRowOrder).val(servicePrice);
        tr.find("input[name='total_price_bf_dis[]']").val(servicePrice);
        tr.find("input[name='total_price[]']").attr("id", "total_price_"+indexRowOrder).val(servicePrice);
        tr.find(".btnProductInfo").hide();
        if(serviceUomId == ''){
            tr.find("select[name='qty_uom_id[]']").attr("id", "qty_uom_id_"+indexRowOrder).html('<option value="1" conversion="1" selected="selected">None</option>').css('visibility', 'hidden');
        } else {
            tr.find("select[name='qty_uom_id[]']").attr("id", "qty_uom_id_"+indexRowOrder).find("option[value='"+serviceUomId+"']").attr("selected", true);
            tr.find("select[name='qty_uom_id[]']").find("option[value!='"+serviceUomId+"']").hide();
        }
        $("#tblPrescription").append(tr);
        $("#tblPrescription").find("tr:last").find(".qty").select().focus();
        sortNuTableOrder();
        checkEventOrder();
        getTotalAmountOrder();
        searchCodeOrder = 1;
    }
    
    function addNewMiscOrder(){
        indexRowOrder = Math.floor((Math.random() * 100000) + 1);
        // Service Information
        var miscName  = $("#MiscellaneousDescription").val();
        var miscPrice = $("#MiscellaneousUnitPrice").val();
        var tr        = tblRowOrder.clone(true);
        
        tr.removeAttr("style").removeAttr("id");
        tr.find("td:eq(0)").html(indexRowOrder);
        tr.find(".lblSKU").html('');
        tr.find("input[name='product[]']").attr("id", "product_"+indexRowOrder).val(miscName);
        tr.find("input[name='qty[]']").attr("id", "qty_"+indexRowOrder).val(1);
        tr.find("input[name='qty_free[]']").attr("id", "qty_free_"+indexRowOrder).val(0);
        tr.find(".conversion").attr("id", "conversion_"+indexRowOrder).val(1);
        tr.find(".discount").attr("id", "discount_"+indexRowOrder).val(0);
        tr.find("input[name='unit_price[]']").attr("id", "unit_price_"+indexRowOrder).val(miscPrice);
        tr.find("input[name='total_price_bf_dis[]']").val(miscPrice);
        tr.find("input[name='total_price[]']").attr("id", "total_price_"+indexRowOrder).val(miscPrice);
        tr.find(".btnProductInfo").hide();
        $("#tblPrescription").append(tr);
        $("#tblPrescription").find("tr:last").find(".qty").select().focus();
        sortNuTableOrder();
        checkEventOrder();
        getTotalAmountOrder();
        searchCodeOrder = 1;
    }

    function checkExistingRecordOrder(productId){
        var isFound = false;
        $("#tblPrescription").find("tr").each(function(){
            if(productId == $(this).find("input[name='product_id[]']").val()){
                isFound = true;
            }
        });
        return isFound;
    }

    function calculateTotalRowQu(obj){
        var tr  = obj.closest("tr");
        var qty = replaceNum(tr.find(".qty").val());
        var unitCost   = replaceNum(tr.find(".unit_cost").val());
        var unitPrice  = replaceNum(tr.find(".unit_price").val());
        var discount   = 0;
        var disAmt     = replaceNum(tr.find("input[name='discount_amount[]']").val());
        var disPercent = replaceNum(tr.find("input[name='discount_percent[]']").val());
        var totalPrice = converDicemalJS(qty * unitPrice);
        var total      = 0;
        // Set Unit Price If Action Not Unit Price
        if(obj.attr("class") != 'float unit_price' && obj.attr("class") != 'float unit_price inputEnable'){
            tr.find(".unit_price").val((unitPrice).toFixed(2));
        }
        // Set Total Price If Action Not Total Price
        if(obj.attr("class") != 'float total_price' && obj.attr("class") != 'float total_price inputEnable'){
            if(disPercent > 0){
                discount = converDicemalJS(converDicemalJS(totalPrice * disPercent) / 100);
            }else if(disAmt > 0){
                discount = disAmt;
            }
            total = converDicemalJS(totalPrice - discount);
            tr.find(".discount").val((discount).toFixed(2));
            tr.find(".total_price_bf_dis").val((totalPrice).toFixed(2));
            tr.find(".total_price").val((total).toFixed(2));
        }
        if(tr.find(".product_id").val() != ''){
            if(unitCost > unitPrice){
                tr.find(".priceDownOrder").show();
                tr.find(".unit_price").css("color", "red");
            } else {
                tr.find(".priceDownOrder").hide();
                tr.find(".unit_price").css("color", "#000");
            }
        }
        getTotalAmountOrder();
    }
    
    function getTotalAmountOrder(){
        var totalAmount = 0;
        var totalVatPercent  = replaceNum($("#OrderVatPercent").val());
        var totalVat = 0;
        var totalDiscount    = replaceNum($("#OrderDiscount").val());
        var totalDisPercent  = replaceNum($("#OrderDiscountPercent").val());
        var total    = 0;
        var vatCal   = $("#OrderVatCalculate").val();
        var totalBfDis = 0;
        var totalAmtCalVat = 0;
        $(".tblPrescriptionList").find(".total_price").each(function(){
            if($.trim($(this).val()) != '' || $(this).val() != undefined ){
                totalAmount += replaceNum($(this).val());
            }
        });
        if(totalDisPercent > 0){
            totalDiscount  = replaceNum(converDicemalJS((totalAmount * totalDisPercent) / 100).toFixed(2));
        }
        totalAmtCalVat = replaceNum(converDicemalJS(totalAmount - totalDiscount));
        // Check VAT Calculate Before Discount, Free, Mark Up
        if(vatCal == 1){
            $(".tblPrescriptionList").each(function(){
                var qty   = replaceNum($(this).find(".qty").val()) + replaceNum($(this).find(".qty_free").val());
                var price = replaceNum($(this).find(".unit_price").val());
                totalBfDis += replaceNum(converDicemalJS(qty * price));
            });
            totalAmtCalVat = totalBfDis;
        }
        totalVat = replaceNum(converDicemalJS(converDicemalJS(totalAmtCalVat * totalVatPercent) / 100).toFixed(2));
        total = converDicemalJS((totalAmount - totalDiscount) + totalVat);
        
        $("#OrderTotalAmount").val((totalAmount).toFixed(2));
        $("#OrderDiscount").val((totalDiscount).toFixed(2));
        $("#OrderTotalVat").val((totalVat).toFixed(2));
        $("#OrderTotalAmountSummary").val((total).toFixed(2));
        $("#OrderTotalAmount, #OrderTotalAmountSummary").priceFormat({
            centsLimit: 2,
            centsSeparator: '.'
        });
    }
    
    function sortNuTableOrder(){
        var sort = 1;
        $(".tblPrescriptionList").each(function(){
            $(this).find("td:eq(0)").html(sort);
            sort++;
        });
    }
    
    function checkEventOrder(){
        eventKeyOrder();
        $(".tblPrescriptionList").unbind("click");
        $(".tblPrescriptionList").click(function(){
            eventKeyOrder();
        });
    }
    
    function showOriginalNameOrder(puc, sku, name){
        var orgName = '';
        orgName += 'PUC: '+puc;
        orgName += '<br/><br/>SKU: '+puc;
        orgName += '<br/><br/>Name: '+name;
        return orgName;
    }
    
    function showProductInfoOrder(currentTr){
        var customerId = $("#OrderCustomerId").val();
        var productId  = currentTr.closest("tr").find(".product_id").val();
        if(productId != ''){
            $.ajax({
                type:   "POST",
                url:    "<?php echo $this->base . "/orders/productHistory"; ?>/"+productId+"/"+customerId,
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
    
    
    // add Note
    function addNoteOrder(currentTr){
        var note = currentTr.closest("tr").find(".note");
        $("#dialog").html("<textarea style='width:350px; height: 200px;' id='noteComment'>"+note.val()+"</textarea>").dialog({
            title: '<?php echo TABLE_NOTE; ?>',
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
                    note.val($("#noteComment").val());
                    $(this).dialog("close");
                }
            }
        });
    }
</script>
<div class="inputContainer" style="width:100%;" id="orderSearchForm">
    <table style="width: 100%;">
        <tr>
            <td style="width: 300px; text-align: left; display: none;">
                <?php echo $this->Form->text('product', array('id' => 'SearchProductPucOrder', 'style' => 'width: 90%; height:15px;', 'placeholder' => TABLE_SCAN_ENTER_UPC)); ?>
            </td>
            <td style="width: 300px; text-align: left;">
                <?php echo $this->Form->text('code', array('id' => 'SearchProductSkuPrescription', 'style' => 'width: 90%; height:15px;', 'placeholder' => TABLE_SEARCH_SKU_NAME)); ?>
            </td>
            <td style="width: 15%; text-align: left;" id="divSearchOrder">
                <img alt="Search" align="absmiddle" style="cursor: pointer;"class="searchProductListOrder" onmouseover="Tip('<?php echo GENERAL_SEARCH; ?>')" src="<?php echo $this->webroot . 'img/button/search.png'; ?>" /> 
                <?php
                if ($allowAddMisc) {
                ?>
                    &nbsp;&nbsp;<img alt="<?php echo SALES_ORDER_ADD_MISCELLANEOUS; ?>" style="cursor: pointer; height: 32px;" align="absmiddle" class="addMiscellaneousOrder" onmouseover="Tip('<?php echo SALES_ORDER_ADD_MISCELLANEOUS; ?>')" src="<?php echo $this->webroot . 'img/button/misc.png'; ?>" />
                <?php
                }
                ?>
            </td>
            <td style="text-align: right;">
                <label for="prescriptionType"><?php echo TABLE_PRESCRIPTION_TYPE;?></label>
                <select id="prescriptionType" name="data[Order][prescription_type]">
                    <option <?php if($order['Order']['prescription_type'] == 0){ ?>selected="selected"<?php } ?> value="0"><?php echo TABLE_HOME_MEDICAL;?></option>
                    <option <?php if($order['Order']['prescription_type'] == 1){ ?>selected="selected"<?php } ?> value="1"><?php echo TABLE_HOSPITAL_MEDICAL;?></option>
                </select>
            </td>
            <td style="text-align:right; display: none;">
                <div style="width:100%; float: right;">
                    <?php echo TABLE_PRICE_TYPE; ?> : &nbsp;&nbsp;&nbsp;  
                    <select id="typeOfPriceOrder" name="data[Order][price_type_id]" style="height: 30px; width: 50%">
                        <?php
                        $sqlPrice = mysql_query("SELECT id, name, (SELECT GROUP_CONCAT(company_id) FROM price_type_companies WHERE price_type_id = price_types.id) AS company_id FROM price_types WHERE is_active = 1 AND is_ecommerce = 0 AND id IN (SELECT price_type_id FROM price_type_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id'].") GROUP BY price_type_companies.price_type_id) ORDER BY ordering ASC");
                        while($row = mysql_fetch_array($sqlPrice)){
                        ?>
                        <option value="<?php echo $row['id']; ?>" comp="<?php echo $row['company_id']; ?>" <?php if($priceTypeId == $row['id']){ ?>selected="selected"<?php } ?>><?php echo $row['name']; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </td>
        </tr>
    </table>
</div>
<div style="clear: both;"></div>


<table id="tblPrescriptionHeader" class="table" cellspacing="0" style="margin-top: 5px; padding:0px; width: 99%">
    <tr>
        <th class="first" style="width:4%"><?php echo TABLE_NO; ?></th>
        <th style="width:7%"><?php echo TABLE_SKU; ?></th>
        <th style="width:18%"><?php echo TABLE_PRODUCT_NAME; ?></th>
        <th style="width:5%"><?php echo TABLE_QTY; ?></th>
        <th style="width:7%"><?php echo TABLE_UOM; ?></th>
        <th style="width:18%;"><?php echo TABLE_INSTRUCTION; ?></th>
        <th style="width:8%;"><?php echo TABLE_MORNING; ?></th>
        <th style="width:8%; display: none;"><?php echo TABLE_AFTERNOON; ?></th>
        <th style="width:8%; display: none;"><?php echo TABLE_EVENING; ?></th>
        <th style="width:8%; display: none;"><?php echo TABLE_NIGHT; ?></th>
        <th style="width:8%"></th>
    </tr>
</table>
<div id="bodyListPrescription">
    <table id="tblPrescription" class="table" cellspacing="0" style="padding: 0px; width:100%">
        <tr id="OrderListPrescription" class="tblPrescriptionList" style="visibility: hidden;">
            <td class="first" style="width:4%; text-align: center; padding: 0px; height: 30px;"></td>
            <td style="width:7%; text-align: left; padding: 5px;">
                <span class="lblSKU"></span>
            </td>
            <td style="width:18%; text-align: left; padding: 5px;">
                <div class="inputContainer" style="width:100%">
                    <input type="hidden" name="product_id[]" value="" class="product_id" />
                    <input type="hidden" name="service_id[]" value="" />
                    <input type="hidden" class="orgProName" />
                    <input type="text" id="product" name="product[]" readonly="readonly" class="product validate[required]" style="width: 85%;" />
                    <input name="note[]" id="note" class="note" value="" type="hidden">
                    <img alt="Note" src="<?php echo $this->webroot . 'img/button/note.png'; ?>" class="noteAddOrder" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Doze')" />
                    <img alt="Information" src="<?php echo $this->webroot . 'img/button/view.png'; ?>" class="btnProductInfo" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Information')" />
                </div>
            </td>
            <td style="width:5%; text-align: center;padding: 0px;">
                <div class="inputContainer" style="width:100%">
                    <input type="text" id="qty" name="qty[]" style="width:80%;" class="qty interger" />
                </div>
            </td>
            <td style="width:7%; padding: 0px; text-align: center">
                <div class="inputContainer" style="width:100%">
                    <input type="hidden" value="1" name="conversion[]" class="conversion" />
                    <select id="qty_uom_id" style="width:80%; height: 20px;" name="qty_uom_id[]" class="qty_uom_id validate[required]">
                        <?php 
                        foreach($uoms as $uom){
                        ?>
                        <option conversion="1" value="<?php echo $uom['Uom']['id']; ?>"><?php echo $uom['Uom']['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </td>
            <td style="width:18%; text-align: center;padding: 0px;">
                <div class="inputContainer" style="width:100%">
                    <input type="text" id="numDays" name="num_days[]" style="width:95%; text-align: left;" class="numDays" />
                </div>
            </td>
            <td style="width:8%; text-align: center;padding: 0px;">
                <div class="clear"></div>
                <div class="inputContainer" style="width:100%">
                    <input type="text" id="morning" name="morning[]" style="width:60%; text-align: center;" class="interger morning" />
                </div>
                <div class="clear"></div>
                <div>
                    <select id="morningUse" style="width:80%; height: 25px; border: 1px solid #CCC;" name="morning_use_id[]" class="morning_use_id">
                        <option value=""><?php echo INPUT_SELECT;?></option>
                        <?php 
                        foreach($treatmentUses as $treatmentUse){
                        ?>
                        <option value="<?php echo $treatmentUse['TreatmentUse']['id']; ?>"><?php echo $treatmentUse['TreatmentUse']['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="clear"></div>
            </td>
            <td style="width:8%; text-align: center;padding: 0px; display: none;">
                <div class="clear"></div>
                <div class="inputContainer" style="width:100%">
                    <input type="text" id="afternoon" name="afternoon[]" style="width:60%; text-align: center;" class="interger afternoon" />
                </div>
                <div class="clear"></div>
                <div>
                    <select id="afternoonUse" style="width:80%; height: 25px; border: 1px solid #CCC;" name="afternoon_use_id[]" class="afternoon_use_id">
                        <option value=""><?php echo INPUT_SELECT;?></option>
                        <?php 
                        foreach($treatmentUses as $treatmentUse){
                        ?>
                        <option value="<?php echo $treatmentUse['TreatmentUse']['id']; ?>"><?php echo $treatmentUse['TreatmentUse']['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="clear"></div>
            </td>
            <td style="width:8%; text-align: center;padding: 0px; display: none;">
                <div class="clear"></div>
                <div class="inputContainer" style="width:100%">
                    <input type="text" id="evening" name="evening[]" style="width:60%; text-align: center;" class="interger evening" />
                </div>
                <div class="clear"></div>
                <div>
                    <select id="eveningUse" style="width:80%; height: 25px; border: 1px solid #CCC;" name="evening_use_id[]" class="evening_use_id">
                        <option value=""><?php echo INPUT_SELECT;?></option>
                        <?php 
                        foreach($treatmentUses as $treatmentUse){
                        ?>
                        <option value="<?php echo $treatmentUse['TreatmentUse']['id']; ?>"><?php echo $treatmentUse['TreatmentUse']['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="clear"></div>
            </td>
            <td style="width:8%; text-align: center;padding: 0px; display: none;">
                <div class="clear"></div>
                <div class="inputContainer" style="width:100%">
                    <input type="text" id="night" name="night[]" style="width:60%; text-align: center;" class="interger night" />
                </div>
                <div class="clear"></div>
                <div>
                    <select id="nightUse" style="width:80%; height: 25px; border: 1px solid #CCC;" name="night_use_id[]" class="night_use_id">
                        <option value=""><?php echo INPUT_SELECT;?></option>
                        <?php 
                        foreach($treatmentUses as $treatmentUse){
                        ?>
                        <option value="<?php echo $treatmentUse['TreatmentUse']['id']; ?>"><?php echo $treatmentUse['TreatmentUse']['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="clear"></div>
            </td>
            
            
            <td style="width:8%">
                <img alt="Remove" src="<?php echo $this->webroot . 'img/button/cross.png'; ?>" class="btnRemoveOrderList" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Remove')" />
                &nbsp; <img alt="Up" src="<?php echo $this->webroot . 'img/button/move_up.png'; ?>" class="btnMoveUpOrderList" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Up')" />
                &nbsp; <img alt="Down" src="<?php echo $this->webroot . 'img/button/move_down.png'; ?>" class="btnMoveDownOrderList" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Down')" />
            </td>            
        </tr>
        <?php
        $index = 0;
        if(!empty($orderDetails)){
            foreach($orderDetails AS $orderDetail){
                $productName = $orderDetail['Product']['name'];
        ?>
        <tr class="tblPrescriptionList">
            <td class="first" style="width:4%; text-align: center; padding: 0px; height: 30px;"><?php echo ++$index; ?></td>
            <td style="width:7%; text-align: left; padding: 5px;">
                <span class="lblSKU"><?php echo $orderDetail['Product']['code']; ?></span>
            </td>
            <td style="width:18%; text-align: left; padding: 5px;">
                <div class="inputContainer" style="width:100%">
                    <input type="hidden" name="product_id[]" value="<?php echo $orderDetail['OrderDetail']['product_id']; ?>" class="product_id" />
                    <input type="hidden" name="service_id[]" value="" />
                    <input type="hidden" class="orgProName" value="<?php echo "PUC: ".htmlspecialchars($orderDetail['Product']['barcode'], ENT_QUOTES, 'UTF-8')."<br/><br/>SKU: ".htmlspecialchars($orderDetail['Product']['code'], ENT_QUOTES, 'UTF-8')."<br/><br/>Name: ".htmlspecialchars($orderDetail['Product']['name'], ENT_QUOTES, 'UTF-8'); ?>" />
                    <input type="text" id="product_<?php echo $index; ?>" value="<?php echo str_replace('"', '&quot;', $productName); ?>" name="product[]" class="product validate[required]" style="width: 85%;" />
                    <input type="hidden" name="note[]" id="note" class="note" value="<?php echo $orderDetail['OrderDetail']['note']; ?>" />
                    <img alt="Note" src="<?php echo $this->webroot . 'img/button/note.png'; ?>" class="noteAddOrder" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Doze')" />
                    <img alt="Information" src="<?php echo $this->webroot . 'img/button/view.png'; ?>" class="btnProductInfo" align="absmiddle" style="padding-left: 1px; cursor: pointer;" onmouseover="Tip('Information')" />
                </div>
            </td>
            <td style="width:5%; text-align: center;padding: 0px;">
                <div class="inputContainer" style="width:100%">
                    <input type="text" id="qty_<?php echo $index; ?>" value="<?php echo $orderDetail['OrderDetail']['qty']; ?>" name="qty[]" style="width:70%;" class="qty interger" />
                </div>
            </td>
            
            <td style="width:7%; padding: 0px; text-align: center">
                <div class="inputContainer" style="width:100%">
                    <input type="hidden" name="conversion[]" class="conversion" value="<?php echo $orderDetail['OrderDetail']['conversion']; ?>" />
                    <select id="qty_uom_id_<?php echo $index; ?>" style="width:80%; height: 20px;" name="qty_uom_id[]" class="qty_uom_id validate[required]" >
                        <?php
                        $query=mysql_query("SELECT id,name,abbr,1 AS conversion FROM uoms WHERE id=".$orderDetail['Product']['price_uom_id']."
                                            UNION
                                            SELECT id,name,abbr,(SELECT value FROM uom_conversions WHERE is_active=1 AND from_uom_id=".$orderDetail['Product']['price_uom_id']." AND to_uom_id=uoms.id) AS conversion FROM uoms WHERE id IN (SELECT to_uom_id FROM uom_conversions WHERE is_active=1 AND from_uom_id=".$orderDetail['Product']['price_uom_id'].")
                                            ORDER BY conversion ASC");
                        $i = 1;
                        $length = mysql_num_rows($query);
                        $costSelected = 0;
                        while($data=mysql_fetch_array($query)){
                            $selected = "";
                            $priceLbl = "";
                            $costLbl  = "";
                            if($data['id'] == $orderDetail['OrderDetail']['qty_uom_id']){   
                                $selected = ' selected="selected" ';
                            }
                            // Get Price
                            $sqlPrice = mysql_query("SELECT products.unit_cost, product_prices.price_type_id, product_prices.amount, product_prices.percent, product_prices.add_on, product_prices.set_type FROM product_prices INNER JOIN products ON products.id = product_prices.product_id WHERE product_prices.product_id =".$orderDetail['Product']['id']." AND product_prices.uom_id =".$data['id']);
                            if(@mysql_num_rows($sqlPrice)){
                                $price = 0;
                                while($rowPrice = mysql_fetch_array($sqlPrice)){
                                    $unitCost = $rowPrice['unit_cost'] /  $data['conversion'];
                                    if($rowPrice['set_type'] == 1){
                                        $price = $rowPrice['amount'];
                                    }else if($rowPrice['set_type'] == 2){
                                        $percent = ($unitCost * $rowPrice['percent']) / 100;
                                        $price = $unitCost + $percent;
                                    }else if($rowPrice['set_type'] == 3){
                                        $price = $unitCost + $rowPrice['add_on'];
                                    }
                                    $priceLbl .= 'price-uom-'.$rowPrice['price_type_id'].'="'.$price.'" ';
                                    $costLbl  .= 'cost-uom-'.$rowPrice['price_type_id'].'="'.$unitCost.'" ';
                                    if($data['id'] == $orderDetail['OrderDetail']['qty_uom_id'] && $rowPrice['price_type_id'] == $priceTypeId){
                                        $costSelected = $unitCost;
                                    }
                                }
                            }else{
                                $priceLbl .= 'price-uom-1="0" price-uom-2="0"';
                                $costLbl  .= 'cost-uom-1="0" cost-uom-2="0"';
                            }
                        ?>
                        <option <?php echo $costLbl; ?> <?php echo $priceLbl; ?> <?php echo $selected; ?>data-sm="<?php if($length == $i){ ?>1<?php }else{ ?>0<?php } ?>" data-item="<?php if($data['id'] == $orderDetail['Product']['price_uom_id']){ echo "first"; }else{ echo "other";} ?>" value="<?php echo $data['id']; ?>" conversion="<?php echo $data['conversion']; ?>"><?php echo $data['name']; ?></option>
                        <?php 
                        $i++;
                        } ?>
                    </select>
                </div>
            </td>
            
            <td style="width:18%; text-align: center;padding: 0px;">
                <div class="inputContainer" style="width:100%">
                    <input type="text" id="numDays_<?php echo $index;?>" name="num_days[]" value="<?php echo $orderDetail['OrderDetail']['num_days'];?>" style="text-align: left; width:95%;" class="numDays" />
                </div>
            </td>
            <td style="width:8%; text-align: center;padding: 0px;">
                <div class="clear"></div>
                <div class="inputContainer" style="width:100%">
                    <input type="text" id="morning_<?php echo $index;?>" name="morning[]" value="<?php echo $orderDetail['OrderDetail']['morning'];?>" style="text-align: center; width:60%;" class="interger morning" />
                </div>
                <div class="clear"></div>
                <div>
                    <select id="morningUse" style="width:80%; height: 25px; border: 1px solid #CCC;" name="morning_use_id[]" class="morning_use_id">
                        <option value=""><?php echo INPUT_SELECT;?></option>
                        <?php 
                        foreach($treatmentUses as $treatmentUse){
                            $selected = "";
                            if($treatmentUse['TreatmentUse']['id'] == $orderDetail['OrderDetail']['morning_use_id']){
                                $selected = "selected='selected'";
                            }
                        ?>
                        <option <?php echo $selected;?> value="<?php echo $treatmentUse['TreatmentUse']['id']; ?>"><?php echo $treatmentUse['TreatmentUse']['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="clear"></div>
            </td>
            <td style="width:8%; text-align: center;padding: 0px; display: none;">
                <div class="clear"></div>
                <div class="inputContainer" style="width:100%">
                    <input type="text" id="afternoon_<?php echo $index;?>" name="afternoon[]" value="<?php echo $orderDetail['OrderDetail']['afternoon'];?>" style="text-align: center; width:60%;" class="interger afternoon" />
                </div>
                <div class="clear"></div>
                <div>
                    <select id="afternoonUse" style="width:80%; height: 25px; border: 1px solid #CCC;" name="afternoon_use_id[]" class="afternoon_use_id">
                        <option value=""><?php echo INPUT_SELECT;?></option>
                        <?php 
                        foreach($treatmentUses as $treatmentUse){
                            $selected = "";
                            if($treatmentUse['TreatmentUse']['id'] == $orderDetail['OrderDetail']['afternoon_use_id']){
                                $selected = "selected='selected'";
                            }
                        ?>
                        <option <?php echo $selected;?> value="<?php echo $treatmentUse['TreatmentUse']['id']; ?>"><?php echo $treatmentUse['TreatmentUse']['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="clear"></div>
            </td>
            <td style="width:8%; text-align: center;padding: 0px; display: none;">
                <div class="clear"></div>
                <div class="inputContainer" style="width:100%">
                    <input type="text" id="evening_<?php echo $index;?>" name="evening[]" value="<?php echo $orderDetail['OrderDetail']['evening'];?>" style="text-align: center; width:60%;" class="interger evening" />
                </div>
                <div class="clear"></div>
                <div>
                    <select id="eveningUse" style="width:80%; height: 25px; border: 1px solid #CCC;" name="evening_use_id[]" class="evening_use_id">
                        <option value=""><?php echo INPUT_SELECT;?></option>
                        <?php 
                        foreach($treatmentUses as $treatmentUse){
                            $selected = "";
                            if($treatmentUse['TreatmentUse']['id'] == $orderDetail['OrderDetail']['evening_use_id']){
                                $selected = "selected='selected'";
                            }
                        ?>
                        <option <?php echo $selected;?> value="<?php echo $treatmentUse['TreatmentUse']['id']; ?>"><?php echo $treatmentUse['TreatmentUse']['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="clear"></div>
            </td>
            <td style="width:8%; text-align: center;padding: 0px; display: none;">
                <div class="clear"></div>
                <div class="inputContainer" style="width:100%">
                    <input type="text" id="night_<?php echo $index;?>" name="night[]" value="<?php echo $orderDetail['OrderDetail']['night'];?>" style="text-align: center; width:60%;" class="interger night" />
                </div>
                <div class="clear"></div>
                <div>
                    <select id="nightUse" style="width:80%; height: 25px; border: 1px solid #CCC;" name="night_use_id[]" class="night_use_id">
                        <option value=""><?php echo INPUT_SELECT;?></option>
                        <?php 
                        foreach($treatmentUses as $treatmentUse){
                            $selected = "";
                            if($treatmentUse['TreatmentUse']['id'] == $orderDetail['OrderDetail']['night_use_id']){
                                $selected = "selected='selected'";
                            }
                        ?>
                        <option <?php echo $selected;?> value="<?php echo $treatmentUse['TreatmentUse']['id']; ?>"><?php echo $treatmentUse['TreatmentUse']['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="clear"></div>
            </td>   
            <td style="width:8%">
                <img alt="Remove" src="<?php echo $this->webroot . 'img/button/cross.png'; ?>" class="btnRemoveOrderList" id="btnRemoveOrderList_<?php echo $index; ?>" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Remove')" />
                &nbsp; <img alt="Up" src="<?php echo $this->webroot . 'img/button/move_up.png'; ?>" class="btnMoveUpOrderList" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Up')" />
                &nbsp; <img alt="Down" src="<?php echo $this->webroot . 'img/button/move_down.png'; ?>" class="btnMoveDownOrderList" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Down')" />
            </td>            
        </tr>
        <?php
            }
        }
        if(!empty($orderServices)){
            foreach($orderServices AS $orderService){
                $uomName = 'None';
                $uomVal  = 1;
                if($orderService['Service']['uom_id'] != ''){
                    $sqlUom = mysql_query("SELECT abbr FROM uoms WHERE id = ".$orderService['Service']['uom_id']);
                    $rowUom = mysql_fetch_array($sqlUom);
                    $uomName = $rowUom[0];
                    $uomVal  = $orderService['Service']['uom_id'];
                }
        ?>
        <tr class="tblPrescriptionList">
            <td class="first" style="width:5%; text-align: center;padding: 0px; height: 30px;"><?php echo ++$index; ?></td>
            <td style="width:11%; text-align: left; padding: 5px;">
                <span class="lblSKU"><?php echo $orderService['Service']['code']; ?></span>
            </td>
            <td style="width:18%; text-align: left; padding: 5px;">
                <div class="inputContainer" style="width:100%">
                    <input type="hidden" name="product_id[]" value="" class="product_id" />
                    <input type="hidden" name="service_id[]" value="<?php echo $orderService['OrderService']['service_id']; ?>" />
                    <input type="hidden" class="orgProName" />
                    <input type="text" id="product_<?php echo $index; ?>" value="<?php echo str_replace('"', '&quot;', $orderService['Service']['name']); ?>" readonly="readonly" name="product[]" class="product validate[required]" style="width: 85%;" />
                </div>
            </td>
            <td style="width:5%; text-align: center;padding: 0px;">
                <div class="inputContainer" style="width:100%">
                    <input type="text" id="qty_<?php echo $index; ?>" value="<?php echo $orderService['OrderService']['qty']; ?>" name="qty[]" style="width:70%;" class="qty interger" />
                </div>
            </td>
            <td style="width:8%; text-align: center;padding: 0px;">
                <div class="inputContainer" style="width:100%">
                    <input type="text" id="qty_free_<?php echo $index; ?>" value="<?php echo $orderService['OrderService']['qty_free']; ?>" name="qty_free[]" style="width:80%;" class="qty_free interger" />
                </div>
            </td>
            <td style="width:13%; padding: 0px; text-align: center">
                <div class="inputContainer" style="width:100%">
                    <input type="hidden" name="conversion[]" class="conversion" value="1" />
                    <select id="qty_uom_id_<?php echo $index; ?>" style="width:80%; height: 20px; <?php if($uomName == 'None'){ ?>visibility: hidden;<?php } ?>" name="qty_uom_id[]" class="qty_uom_id">
                        <option value="<?php echo $uomVal; ?>" conversion="1" selected="selected"><?php echo $uomName;?></option>
                    </select>
                </div>
            </td>
            <td style="width:9%; padding: 0px; text-align: center">
                <div class="inputContainer" style="width:100%">
                    <input type="hidden" class="float unit_cost" name="unit_cost[]" value="0" />
                    <input type="text" id="unit_price_<?php echo $index; ?>" <?php if(!$allowEditPrice){ ?>readonly="readonly"<?php } ?> value="<?php echo number_format($orderService['OrderService']['unit_price'], 2); ?>" name="unit_price[]" style="width:70%;" class="float unit_price" />
                </div>
            </td>
            <td style="width:9%; padding: 0px; text-align: center">
                <div class="inputContainer" style="width:100%">
                    <input type="hidden" name="discount_id[]" value="<?php echo $orderService['OrderService']['discount_id']; ?>" />
                    <input type="hidden" name="discount_amount[]" value="<?php echo $orderService['OrderService']['discount_amount']; ?>" />
                    <input type="hidden" name="discount_percent[]" value="<?php echo $orderService['OrderService']['discount_percent']; ?>" />
                    <input type="text" id="discount_<?php echo $index; ?>" class="discount" name="discount[]" value="<?php echo number_format($orderService['OrderService']['discount_amount'], 2); ?>" style="width: 60%;" readonly="readonly" />
                    <img alt="Remove" src="<?php echo $this->webroot . 'img/button/cross.png'; ?>" class="btnRemoveDiscountQu" align="absmiddle" style="cursor: pointer; <?php if(empty($orderService['OrderService']['discount_id'])){ ?> display: none;<?php } ?>" onmouseover="Tip('Remove')" />
                </div>
            </td>
            <td style="width:9%; text-align: center; padding: 0px;">
                <input type="hidden" value="<?php echo number_format($orderService['OrderService']['total_price'], 2); ?>" class="total_price_bf_dis float" name="total_price_bf_dis[]" />
                <input type="text" id="total_price_<?php echo $index; ?>" <?php if(!$allowEditPrice){ ?>readonly="readonly"<?php } ?> value="<?php echo number_format($orderService['OrderService']['total_price'] - $orderService['OrderService']['discount_amount'], 2); ?>" name="total_price[]" style="width:84%" class="float total_price" />
            </td>
            <td style="width:8%">
                <img alt="Remove" src="<?php echo $this->webroot . 'img/button/cross.png'; ?>" class="btnRemoveOrderList" id="btnRemoveOrderList_<?php echo $index; ?>" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Remove')" />
                &nbsp; <img alt="Up" src="<?php echo $this->webroot . 'img/button/move_up.png'; ?>" class="btnMoveUpOrderList" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Up')" />
                &nbsp; <img alt="Down" src="<?php echo $this->webroot . 'img/button/move_down.png'; ?>" class="btnMoveDownOrderList" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Down')" />
            </td>
        </tr>
        <?php
            }
        }
        if(!empty($orderMiscs)){
            foreach($orderMiscs AS $orderMisc){
        ?>
        <tr class="tblPrescriptionList" style="background-color: #f7f9fb;">
            <td class="first" style="width:4%; text-align: center;padding: 0px; height: 30px;"><?php echo ++$index; ?></td>
            <td style="width:7%; text-align: left; padding: 5px;">
                <span class="lblSKU"></span>
            </td>
            <td style="width:18%; text-align: left; padding: 5px;">
                <div class="inputContainer" style="width:100%">
                    <input type="hidden" name="product_id[]" value="" class="product_id" />
                    <input type="hidden" name="service_id[]" value="" />
                    <input type="hidden" class="orgProName" />
                    <input type="text" id="product_<?php echo $index; ?>" value="<?php echo str_replace('"', '&quot;', $orderMisc['OrderMisc']['description']); ?>" readonly="readonly" name="product[]" class="product validate[required]" style="width: 85%;" />
                    <input type="hidden" id="qty_free_<?php echo $index; ?>" value="<?php echo $orderMisc['OrderMisc']['qty_free']; ?>" name="qty_free[]" style="width:80%;" class="qty_free interger" />
                    <input type="hidden" name="note[]" id="note" class="note" value="<?php echo $orderMisc['OrderMisc']['note']; ?>" />
                    <img alt="Note" src="<?php echo $this->webroot . 'img/button/note.png'; ?>" class="noteAddOrder" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Doze')" />
                </div>
            </td>
            <td style="width:5%; text-align: center;padding: 0px;">
                <div class="inputContainer" style="width:100%">
                    <input type="text" id="qty_<?php echo $index; ?>" value="<?php echo $orderMisc['OrderMisc']['qty']; ?>" name="qty[]" style="width:70%;" class="qty interger" />
                </div>
            </td>
            <td style="width:7%; padding: 0px; text-align: center">
                <div class="inputContainer" style="width:100%">
                    <input type="hidden" name="conversion[]" class="conversion" value="1" />
                    <select id="qty_uom_id_<?php echo $index; ?>" style="width:80%; height: 20px;" name="qty_uom_id[]" class="qty_uom_id validate[required]">
                        <?php 
                        foreach($uoms as $uom){
                        ?>
                        <option conversion="1" value="<?php echo $uom['Uom']['id']; ?>" <?php if($uom['Uom']['id'] == $orderMisc['OrderMisc']['qty_uom_id']){ ?>selected="selected"<?php } ?>><?php echo $uom['Uom']['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </td>
            <td style="width:18%; text-align: center; padding: 0px;">
                <div class="inputContainer" style="width:100%">
                    <input type="text" id="numDays_<?php echo $index;?>" name="num_days[]" value="<?php echo $orderMisc['OrderMisc']['num_days'];?>" style="text-align: left; width:95%;" class="numDays" />
                </div>
            </td>
            <td style="width:8%; text-align: center;padding: 0px;">
                <div class="clear"></div>
                <div class="inputContainer" style="width:100%">
                    <input type="text" id="morning_<?php echo $index;?>" name="morning[]" value="<?php echo $orderMisc['OrderMisc']['morning'];?>" style="text-align: center; width:60%;" class="interger morning" />
                </div>
                <div class="clear"></div>
                <div>
                    <select id="morningUse" style="width:80%; height: 25px; border: 1px solid #CCC;" name="morning_use_id[]" class="morning_use_id">
                        <option value=""><?php echo INPUT_SELECT;?></option>
                        <?php 
                        foreach($treatmentUses as $treatmentUse){
                            $selected = "";
                            if($treatmentUse['TreatmentUse']['id'] == $orderMisc['OrderMisc']['morning_use_id']){
                                $selected = "selected='selected'";
                            }
                        ?>
                        <option <?php echo $selected;?> value="<?php echo $treatmentUse['TreatmentUse']['id']; ?>"><?php echo $treatmentUse['TreatmentUse']['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="clear"></div>
            </td>
            <td style="width:8%; text-align: center; padding: 0px; display: none;">
                <div class="clear"></div>
                <div class="inputContainer" style="width:100%">
                    <input type="text" id="afternoon_<?php echo $index;?>" name="afternoon[]" value="<?php echo $orderMisc['OrderMisc']['afternoon'];?>" style="text-align: center; width:60%;" class="interger afternoon" />
                </div>
                <div class="clear"></div>
                <div>
                    <select id="afternoonUse" style="width:80%; height: 25px; border: 1px solid #CCC;" name="afternoon_use_id[]" class="afternoon_use_id">
                        <option value=""><?php echo INPUT_SELECT;?></option>
                        <?php 
                        foreach($treatmentUses as $treatmentUse){
                            $selected = "";
                            if($treatmentUse['TreatmentUse']['id'] == $orderMisc['OrderMisc']['afternoon_use_id']){
                                $selected = "selected='selected'";
                            }
                        ?>
                        <option <?php echo $selected;?> value="<?php echo $treatmentUse['TreatmentUse']['id']; ?>"><?php echo $treatmentUse['TreatmentUse']['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="clear"></div>
            </td>
            <td style="width:8%; text-align: center; padding: 0px; display: none;">
                <div class="clear"></div>
                <div class="inputContainer" style="width:100%">
                    <input type="text" id="evening_<?php echo $index;?>" name="evening[]" value="<?php echo $orderMisc['OrderMisc']['evening'];?>" style="text-align: center; width:60%;" class="interger evening" />
                </div>
                <div class="clear"></div>
                <div>
                    <select id="eveningUse" style="width:80%; height: 25px; border: 1px solid #CCC;" name="evening_use_id[]" class="evening_use_id">
                        <option value=""><?php echo INPUT_SELECT;?></option>
                        <?php 
                        foreach($treatmentUses as $treatmentUse){
                            $selected = "";
                            if($treatmentUse['TreatmentUse']['id'] == $orderMisc['OrderMisc']['evening_use_id']){
                                $selected = "selected='selected'";
                            }
                        ?>
                        <option <?php echo $selected;?> value="<?php echo $treatmentUse['TreatmentUse']['id']; ?>"><?php echo $treatmentUse['TreatmentUse']['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="clear"></div>
            </td>
            <td style="width:8%; text-align: center; padding: 0px; display: none;">
                <div class="clear"></div>
                <div class="inputContainer" style="width:100%">
                    <input type="text" id="night_<?php echo $index;?>" name="night[]" value="<?php echo $orderMisc['OrderMisc']['night'];?>" style="text-align: center; width:60%;" class="interger night" />
                </div>
                <div class="clear"></div>
                <div>
                    <select id="nightUse" style="width:80%; height: 25px; border: 1px solid #CCC;" name="night_use_id[]" class="night_use_id">
                        <option value=""><?php echo INPUT_SELECT;?></option>
                        <?php 
                        foreach($treatmentUses as $treatmentUse){
                            $selected = "";
                            if($treatmentUse['TreatmentUse']['id'] == $orderMisc['OrderMisc']['night_use_id']){
                                $selected = "selected='selected'";
                            }
                        ?>
                        <option <?php echo $selected;?> value="<?php echo $treatmentUse['TreatmentUse']['id']; ?>"><?php echo $treatmentUse['TreatmentUse']['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="clear"></div>
            </td>   
            
            <td style="width:8%">
                <img alt="Remove" src="<?php echo $this->webroot . 'img/button/cross.png'; ?>" class="btnRemoveOrderList" id="btnRemoveOrderList_<?php echo $index; ?>" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Remove')" />
                &nbsp; <img alt="Up" src="<?php echo $this->webroot . 'img/button/move_up.png'; ?>" class="btnMoveUpOrderList" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Up')" />
                &nbsp; <img alt="Down" src="<?php echo $this->webroot . 'img/button/move_down.png'; ?>" class="btnMoveDownOrderList" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Down')" />
            </td>
        </tr>
        <?php
            }
        }
        ?>
    </table>
</div>