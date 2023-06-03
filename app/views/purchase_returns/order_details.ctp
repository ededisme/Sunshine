<?php
$sqlSettingUomDeatil = mysql_query("SELECT uom_detail_option FROM setting_options");
$rowSettingUomDetail = mysql_fetch_array($sqlSettingUomDeatil);
// Authentication
$this->element('check_access');
$allowAddService = checkAccess($user['User']['id'], $this->params['controller'], 'service');
$allowAddMisc    = checkAccess($user['User']['id'], $this->params['controller'], 'miscellaneous');
$allowAddProduct = checkAccess($user['User']['id'], 'products', 'quickAdd');
$rand = rand();
?>
<style type="text/css">
    #tblPurchaseReturn tr:hover {
        background-color: #F0F0F0;
    }
</style>
<script type="text/javascript">
    var rowListPR  = $("#OrderListRowPR");
    var indexRowPR = 0;
    var searchCodePR = 1;
    
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        $("#OrderListRowPR").remove();
        var waitForFinalEventPR = (function () {
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
        if(tabBRReg != tabBRId){
            $("a[href='"+tabBRId+"']").click(function(){
                if($("#bodyListPR").html() != '' && $("#bodyListPR").html() != null){
                    waitForFinalEventPR(function(){
                        refreshScreenPR();
                        resizeFormTitlePR();
                        resizeFornScrollPR();
                    }, 500, "Finish");
                }
            });
            tabBRReg = tabBRId;
        }
        
        waitForFinalEventPR(function(){
            refreshScreenPR();
            resizeFormTitlePR();
            resizeFornScrollPR();
        }, 500, "Finish");
        
        $(window).resize(function(){
            if(tabBRId == $(".ui-tabs-selected a").attr("href")){
                waitForFinalEventPR(function(){
                    refreshScreenPR();
                    resizeFormTitlePR();
                    resizeFornScrollPR();
                }, 500, "Finish");
            }
        });
        
        //Change Date
        if($.cookie('PurchaseReturnOrderDate')!=null){
            $("#PurchaseReturnOrderDate").val($.cookie('PurchaseReturnOrderDate'));
        }
        $("#PurchaseReturnOrderDate").change(function(){
            if($(".tblPurchaseReturnList").find(".product_id").val() == undefined){
                $.cookie("PurchaseReturnOrderDate", $("#PurchaseReturnOrderDate").val(), {
                    expires : 7,
                    path    : '/'
                });
            }else{
                var question = "<?php echo MESSAGE_CONFRIM_CHANGE_LOCATION_GROUP; ?>";
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
                            $.cookie("PurchaseReturnOrderDate", $("#PurchaseReturnOrderDate").val(), {
                                expires : 7,
                                path    : '/'
                            });
                            loadOrderDetailPurchaseReturn();
                            $(this).dialog("close");
                        },
                        '<?php echo ACTION_CANCEL; ?>': function() {
                            $("#PurchaseReturnOrderDate").val($.cookie("PurchaseReturnOrderDate"));
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
        
        // Hide / Show Header
        $("#btnHideShowHeaderBillReturn").click(function(){
            var PurchaseReturnCompanyId       = $("#PurchaseReturnCompanyId").val();
            var PurchaseReturnBranchId        = $("#PurchaseReturnBranchId").val();
            var PurchaseReturnLocationGroupId = $("#PurchaseReturnLocationGroupId").val();
            var PurchaseReturnLocationId      = $("#PurchaseReturnLocationId").val();
            var PurchaseReturnVendorId        = $("#PurchaseReturnVendorId").val();
            var PurchaseReturnOrderDate       = $("#PurchaseReturnOrderDate").val();
            
            if(PurchaseReturnCompanyId == "" || PurchaseReturnBranchId == "" || PurchaseReturnLocationGroupId == "" || PurchaseReturnLocationId == "" || PurchaseReturnVendorId == "" || PurchaseReturnOrderDate == ""){
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
                    $("#BRTop").hide();
                    img += 'arrow-down.png';
                } else {
                    action = 'Hide';
                    $("#BRTop").show();
                    img += 'arrow-up.png';
                }
                $(this).find("span").text(action);
                $(this).find("img").attr("src", img);
                if(tabBRId == $(".ui-tabs-selected a").attr("href")){
                    waitForFinalEventPR(function(){
                        resizeFornScrollPR();
                    }, 300, "Finish");
                }
            }
        });
       
        $("#PurchaseReturnCodeProduct").autocomplete("<?php echo $this->base . "/purchase_returns/searchProduct/"; ?>", {
            width: 400,
            formatItem: function(data, i, n, value) {
                return value.split(".*")[0]+"-"+value.split(".*")[1];
            },
            formatResult: function(data, value) {
                return value.split(".*")[0]+"-"+value.split(".*")[1];
            }
        }).result(function(event, value){
            var code = value.toString().split(".*")[0];
            $("#PurchaseReturnCodeProduct").val(code);
            if(searchCodePR == 1){
                searchCodePR = 2;
                searchProductByCodePR(code, '');
            }
        });
        
        $("#PurchaseReturnCodeProduct").keypress(function(e){
            if((e.which && e.which == 13) || e.keyCode == 13){
                if(searchCodePR == 1){
                    searchCodePR = 2;
                    searchProductByCodePR($(this).val(), '');
                }
                return false;
            }
        });
        
        $(".searchProductPR").click(function(){
            searchProductPR();
        });
        // Action Service
        $(".addServicePR").click(function(){
            addServicePR();
        });
        // Action Miscs
        $(".addMiscellaneousPR").click(function(){
            addMiscPR();
        });
        
        <?php
        if($allowAddProduct){
        ?>
        $("#addProductPurchaseReturn").click(function(){
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
                
        changeInputCSSBR();
    });
    
    function moveRowBR(){
        $(".btnMoveDownBR, .btnMoveUpBR").unbind('click');
        $(".btnMoveDownBR").click(function () {
            var rowToMove = $(this).parents('tr.tblPurchaseReturnList:first');
            var next = rowToMove.next('tr.tblPurchaseReturnList');
            if (next.length == 1) { next.after(rowToMove); }
            sortNuTableAddCM();
        });

        $(".btnMoveUpBR").click(function () {
            var rowToMove = $(this).parents('tr.tblPurchaseReturnList:first');
            var prev = rowToMove.prev('tr.tblPurchaseReturnList');
            if (prev.length == 1) { prev.before(rowToMove); }
            sortNuTableAddCM();
        });
    }
    function sortNuTableAddCM(){
        var sort = 1;
        $(".tblPurchaseReturnList").each(function(){
            $(this).find("td:eq(0)").html(sort);
            sort++;
        });
    }
    
    function searchProductPR(){
        if($("#PurchaseReturnCompanyId").val()== "" || $("#PurchaseReturnBranchId").val()== "" || $("#PurchaseReturnLocationId").val()==""){
            alertSelectRequireField();
        }else{
            var dateOrder = $("#PurchaseReturnOrderDate").val().split("/")[2]+"-"+$("#PurchaseReturnOrderDate").val().split("/")[1]+"-"+$("#PurchaseReturnOrderDate").val().split("/")[0];
            $.ajax({
                type:   "POST",
                url:    "<?php echo $this->base . "/purchase_returns/product/"; ?>" + $("#PurchaseReturnCompanyId").val()+"/"+$("#PurchaseReturnBranchId").val()+"/"+$("#PurchaseReturnLocationId").val()+"/"+dateOrder,
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
                                if($("input[name='chkProductPR']:checked").val()){
                                    var expiryDate = $("input[name='chkProductPR']:checked").attr("exp");
                                    searchProductByCodePR($("input[name='chkProductPR']:checked").val(), expiryDate);
                                }
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            });
        }
    }
    
    function addServicePR(){
        if($("#PurchaseReturnCompanyId").val()=="" || $("#PurchaseReturnBranchId").val()==""){
            alertSelectRequireField();
        }else{
            $.ajax({
                type:   "POST",
                url:    "<?php echo $this->base . "/purchase_returns/service"; ?>/"+$("#PurchaseReturnCompanyId").val()+"/"+$("#PurchaseReturnBranchId").val(),
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
                                    addNewServicePR();
                                }
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            });
        }
    }
    
    function addMiscPR(){
        if($("#PurchaseReturnCompanyId").val()=="" || $("#PurchaseReturnBranchId").val()==""){
            alertSelectRequireField();
        }else{
            $.ajax({
                type:   "POST",
                url:    "<?php echo $this->base . "/purchase_returns/miscellaneous"; ?>",
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
                                    addNewMiscPR();
                                }
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            });
        }
    }
    
    function select<?php echo $rand; ?>Tabs(){
        $("#tabs").tabs("add", "", "");
        clearTmpTabs();
        $("#tabs").tabs("select", index<?php echo $rand; ?>Tab);
    }
    
    function resizeFormTitlePR(){
        var screen = 16;
        var widthList = $("#bodyListPR").width();
        $("#tblPurchaseReturnHeader").css('width',widthList);
        var widthTitle = widthList - screen;
        $("#tblPurchaseReturnHeader").css('padding','0px');
        $("#tblPurchaseReturnHeader").css('margin-top','5px');
        $("#tblPurchaseReturnHeader").css('width',widthTitle);
    }
    
    function resizeFornScrollPR(){
        $("#PurchaseReturnAddForm").validationEngine("hideAll");
        $(".footerPurchaseReturn").show();
        var tabHeight = $(tabBRId).height();
        var formHeader = 0;
        if ($('#BRTop').is(':hidden')) {
            formHeader = 0;
        } else {
            formHeader = $("#BRTop").height();
        }
        var btnHeader   = $("#btnHideShowHeaderBillReturn").height();
        var formFooter  = $(".footerPurchaseReturn").height();
        var formSearch  = $("#searchFormBR").height();
        var tableHeader = $("#tblPurchaseReturnHeader").height();
        var spaceRemain = 20;
        var getHeight   = tabHeight - (formHeader + btnHeader + tableHeader + formSearch + formFooter + spaceRemain);
        
        $("#bodyListPR").css('height', parseInt(getHeight));
        $("#bodyListPR").css('padding','0px');
        $("#bodyListPR").css('width','100%');
        $("#bodyListPR").css('overflow-x','hidden');
        $("#bodyListPR").css('overflow-y','scroll');
    }
    
    function refreshScreenPR(){
        $("#tblPurchaseReturnHeader").removeAttr('style');
    }
    
    function isOutOfStockPR(productId, productAvailable, qtyOrder, expiryDate){
        var totalProductOrder = getTotalProductOrderPRT(productId, expiryDate) + replaceNum(qtyOrder);
        if(totalProductOrder > productAvailable){
            return true;
        }else{
            return false;
        }
    }
    
    function eventKeyPR(){
        loadAutoCompleteOff();
        $(".qty, .unit_price, .qty_uom_id, .total_price, .btnRemoveBR, .notePR, .expired_date").unbind('click').unbind('keyup').unbind('keypress').unbind('change');
        $(".qty").priceFormat();
        $(".float").autoNumeric({mDec: 2, aSep: ','});
        
        $(".qty").keypress(function(e){
            if((e.which && e.which == 13) || e.keyCode == 13){
                $(this).closest("tr").find(".unit_price").select();
                return false;
            }
        });

        $(".unit_price").keypress(function(e){
            if((e.which && e.which == 13) || e.keyCode == 13){
                if($(this).val() != ""){
                    if($(this).closest("tr").find(".qty_uom_id").find("option").val() != "none"){
                        $(this).closest("tr").find(".qty_uom_id").select().focus();
                    }else{
                        $("#PurchaseReturnCodeProduct").focus().select();
                    }
                }else{
                    $(this).select().focus();
                }
                return false;
            }
        });
        
        $(".unit_price, .qty").blur(function(){
            if($(this).val() == ''){
                $(this).val(0);
            }
            onChangeQtyPR($(this));
            checkQtyOrderPRT($(this));
        });
        
        $(".unit_price, .qty").keyup(function(){
            onChangeQtyPR($(this));
            checkQtyOrderPRT($(this));
        });
         
        $(".qty_uom_id").change(function(){
            var value         = replaceNum($(this).val());
            var uomConversion = replaceNum($(this).find("option[value='"+value+"']").attr('conversion'));
            var smallUoMVal   = replaceNum($(this).closest("tr").find(".small_val_uom").val());
            var conversion    = converDicemalJS(smallUoMVal / uomConversion);
            $(this).closest("tr").find(".conversion").val(conversion);
            // Calculate Unit Cost
            var unitCost      = replaceNum($(this).closest("tr").find(".unit_price").val());
            var NewUnitCost   = converDicemalJS(unitCost / (smallUoMVal / conversion));
            $(this).closest("tr").find(".unit_price").val(NewUnitCost);
            onChangeQtyPR($(this));
            checkQtyOrderPRT($(this));
        });
        
        $(".qty_uom_id").keypress(function(e){
            if((e.which && e.which == 13) || e.keyCode == 13){
                $("#PurchaseReturnCodeProduct").focus().select();
                return false;
            }
        });

        $(".btnRemoveBR").click(function(){
            var currentTr = $(this).closest("tr");
            $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>Are you sure to remove this item?</p>');
            $("#dialog").dialog({
                title: '<?php echo DIALOG_INFORMATION; ?>',
                resizable: false,
                modal: true,
                width: '300',
                height: 'auto',
                position:'center',
                open: function(event, ui){
                    $(".ui-dialog-buttonpane").show();
                },
                buttons: {
                    '<?php echo ACTION_CANCEL; ?>': function() {
                        $(this).dialog("close");
                    },
                    '<?php echo ACTION_OK; ?>': function() {
                        currentTr.remove();
                        getTotalAmountPR();
                        sortNuTablePR();
                        $(this).dialog("close");
                    }
                }
            });
        });
        $(".total_price").keyup(function(){
            var value    = replaceNum($(this).val());
            var qty      = replaceNum($(this).closest("tr").find(".qty").val());
            var uniiCost = converDicemalJS(value / qty);
            $(this).closest("tr").find(".unit_price").val(uniiCost.toFixed(2));
            clearNullPR();
            getTotalAmountPR();
        });
        
        $(".notePR").click(function(){
            addNotePR($(this));
        });
        
        $(".expired_date").click(function(){
            var productId = $(this).closest("tr").find("input[name='product_id[]']").val();
            var obj       = $(this);
            searchProductByExpPR(productId, obj);
        });
        
        moveRowBR();
    }
    
    function getTotalProductOrderPRT(productId, expiryDate){
        var totalProduct = 0;
        if(expiryDate == '') {
            expiryDate = '0000-00-00';
        }
        $(".tblPurchaseReturnList").find(".product_id").each(function(){
            var productExp = $(this).closest("tr").find("input[name='expired_date[]']").val();
            if(productId == $(this).val() && productExp == expiryDate){
                totalProduct += replaceNum($(this).closest("tr").find("input.total_qty").val());
            }
        });
        return totalProduct;
    }

    function searchProductByCodePR(productCode, expDate){
        var orderDate = $("#PurchaseReturnOrderDate").val().toString().split("/")[2]+"-"+$("#PurchaseReturnOrderDate").val().toString().split("/")[1]+"-"+$("#PurchaseReturnOrderDate").val().toString().split("/")[0];
        var companyId = $("#PurchaseReturnCompanyId").val();
        var branchId  = $("#PurchaseReturnBranchId").val();
        if(companyId != '' && branchId != ''){
            $.ajax({
                type:   "POST",
                url:    "<?php echo $this->base . "/purchase_returns/searchProductByCode"; ?>/" + companyId+"/"+branchId,
                data:   "data[code]=" + productCode +
                    "&data[order_date]="  +  orderDate +
                    "&data[expiry_date]=" +  expDate +
                    "&data[location_id]=" +  $("#PurchaseReturnLocationId").val(),
                beforeSend: function(){
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(msg){
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $("#PurchaseReturnCodeProduct").val('');
                    $("#loadProductPR").html(msg);
                    if($("#qtyOfProduct").val() > 0 && $("#purchaseReturnProductId").val() != ""){
                        addProductPR();
                    }else if($("#qtyOfProduct").val() == '0' && $("#purchaseReturnProductId").val() != ""){
                        searchCodePR = 1;
                        $("#dialog").html('<p style="font-size: 16px;"><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_OUT_OF_STOCK; ?></p>');
                        $("#dialog").dialog({
                            title: '<?php echo DIALOG_WARNING; ?>',
                            resizable: false,
                            modal: true,
                            width: 300,
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
                        searchCodePR = 1;
                        $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo TABLE_NO_PRODUCT; ?></p>');
                        $("#dialog").dialog({
                            title: '<?php echo DIALOG_INFORMATION; ?>',
                            resizable: false,
                            modal: true,
                            width: '300',
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
        } else {
            alertSelectRequireField();
        }
    }

    function addNewServicePR(){
        qtyTr = Math.floor((Math.random() * 100000) + 1);
        var tr =rowListPR.clone(true);
        // Service Information
        var serviceId    = $("#ServiceServiceId").val();
        var serviceCode  = $("#ServiceServiceId").find("option:selected").attr('scode');
        var serviceName  = $("#ServiceServiceId").find("option:selected").attr('abbr');
        var servicePrice = $("#ServiceUnitPrice").val();
        var serviceUomId = $("#ServiceServiceId").find("option:selected").attr('suom');
        
        tr.removeAttr("style").removeAttr("id");
        tr.find("td:eq(0)").html(qtyTr);
        tr.find("input[name='inv_qty[]']").val(1);
        tr.find("input[name='total_qty[]']").val(0);
        tr.find("input[name='service_id[]']").val(serviceId);
        tr.find(".lblSKU").val(serviceCode);
        tr.find("input[name='product[]']").attr("id", "product_"+qtyTr).val(serviceName).attr("readonly", false);
        tr.find("input[name='qty[]']").attr("id", "qty_"+qtyTr).val(1);
        tr.find("input[name='unit_price[]']").attr("id", "unit_price_"+qtyTr).val(servicePrice);
        tr.find("input[name='note[]']").attr("id", "note_"+qtyTr);
        tr.find("input[name='total_price[]']").attr("id", "total_price_"+qtyTr).val(tr.find("input[name='unit_price[]']").val());
        if(serviceUomId == ''){
            tr.find("select[name='qty_uom_id[]']").attr("id", "qty_uom_id_"+qtyTr).html('<option value="1" conversion="1" selected="selected">None</option>').css('visibility', 'hidden');
        } else {
            tr.find("select[name='qty_uom_id[]']").attr("id", "qty_uom_id_"+qtyTr).find("option[value='"+serviceUomId+"']").attr("selected", true);
            tr.find("select[name='qty_uom_id[]']").find("option[value!='"+serviceUomId+"']").hide();
        }
        tr.find(".expired_date").attr("id", "expired_date"+indexRowPR).hide();
        $("#tblPurchaseReturn").append(tr);
        checkEventPR();
        sortNuTablePR();
        getTotalAmountPR();
        $("#tblPurchaseReturn").find("tr.tblPurchaseReturnList:last").find(".qty").select().focus();
    }

    function addNewMiscPR(){
        qtyTr = Math.floor((Math.random() * 100000) + 1);
        
        var tr = rowListPR.clone(true);
        tr.removeAttr("style").removeAttr("id");

        // Misc Information
        var productName         = $("#MiscellaneousDescription").val();
        var productPrice        = $("#MiscellaneousUnitPrice").val();
        tr.find("td:eq(0)").html(qtyTr);
        tr.find("input[name='inv_qty[]']").val(1);
        tr.find("input[name='total_qty[]']").val(0);
        tr.find("input[name='product[]']").attr("id", "product_"+qtyTr).val(productName).attr("readonly", false);
        tr.find("input[name='qty[]']").attr("id", "qty_"+qtyTr).val(1);
        tr.find("input[name='unit_price[]']").attr("id", "unit_price_"+qtyTr).val(productPrice);
        tr.find("input[name='note[]']").attr("id", "note_"+qtyTr);
        tr.find("input[name='total_price[]']").attr("id", "total_price_"+qtyTr).val(tr.find("input[name='unit_price[]']").val());
        tr.find(".expired_date").attr("id", "expired_date"+indexRowPR).hide();
        $("#tblPurchaseReturn").append(tr);
        checkEventPR();
        sortNuTablePR();
        getTotalAmountPR();
        $("#tblPurchaseReturn").find("tr.tblPurchaseReturnList:last").find(".qty").focus().select();
    }

    function addProductPR(){
        indexRowPR = Math.floor((Math.random() * 100000) + 1);
        // Product Information
        var productId           = $("#purchaseReturnProductId").val();
        var productCode         = $("#purchaseReturnProductCode").val();
        var productName         = $("#purchaseReturnProductName").val();
        var productPrice        = $("#purchaseReturnProductPrice").val();
        var productPriceUomId   = $("#purchaseReturnProductPriceUomId").val();
        var invQty              = $("#purchaseReturnProductInventoryTotal").val();
        var smallValUom         = $("#purchaseReturnProductSmallValUom").val();
        var isExpiryDate        = $("#purchaseReturnProductIsExpiry").val();
        var expiryDate          = $("#purchaseReturnProductExpiryDate").val();
        
        // Procssing Clone Table
        if(isOutOfStockPR(productId, invQty, smallValUom, expiryDate)){
            var question = "<?php echo MESSAGE_OUT_OF_STOCK; ?>";
            $("#dialog").html('<p>'+question+'</p>');
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
                    
                },
                buttons: {
                    '<?php echo ACTION_CLOSE; ?>': function() {
                        $(this).dialog("close");
                    }
                }
            });
        }else{
            var tr =rowListPR.clone(true);
            tr.removeAttr("style").removeAttr("id");
            tr.find("td:eq(0)").html(indexRowPR);
            tr.find("input[name='inv_qty[]']").val(invQty);
            tr.find("input[name='product_id[]']").val(productId);
            tr.find(".lblSKU").val(productCode);
            tr.find("input[name='product[]']").attr("id", "product_"+indexRowPR).val(productName);
            tr.find("input[name='unit_price[]']").attr("id", "unit_price_"+indexRowPR).val(productPrice);
            tr.find("input[name='small_val_uom[]']").attr("id", "small_val_uom_"+indexRowPR).val(smallValUom);
            tr.find("input[name='conversion[]']").attr("id", "small_val_uom_"+indexRowPR).val(smallValUom);
            tr.find("input[name='qty[]']").attr("id", "qty_"+indexRowPR).val(1).attr("readonly", true);
            tr.find("input[name='total_qty[]']").attr("id", "total_qty_"+indexRowPR).val(smallValUom);
            tr.find("input[name='note[]']").attr("id", "note_"+indexRowPR);
            tr.find("input[name='total_price[]']").attr("id", "total_price_"+indexRowPR).val(productPrice);
            tr.find("select[name='qty_uom_id[]']").html('<option value=""><?php echo INPUT_SELECT; ?></option>');
            tr.find(".expired_date").attr("id", "expired_date"+indexRowPR);
            if(isExpiryDate == 1){
                tr.find("input[name='expired_date[]']").addClass("validate[required]").val(expiryDate);
            }else{
                tr.find("input[name='expired_date[]']").removeClass("validate[required]").css('visibility', 'hidden').val('0000-00-00');
            }
            // Get UoM
            $.ajax({
                type:   "POST",
                url:    "<?php echo $this->base . "/uoms/getRelativeUom/"; ?>"+productPriceUomId,
                success: function(msg){
                    tr.find("select[name='qty_uom_id[]']").attr("id", "qty_uom_id_"+indexRowPR).html(msg).val(1);
                    tr.find("select[name='qty_uom_id[]']").find("option").each(function(){
                        if($(this).attr("conversion")==1){
                            $(this).attr("selected", true);
                            tr.find("input[name='qty[]']").removeAttr("readonly");
                            checkEventPR();
                            onChangeQtyPR(tr.find("select[name='qty_uom_id[]']"));
                            tr.find(".qty").focus().select();
                            return false;
                        }
                    });
                    
                }
            });
            $("#tblPurchaseReturn").append(tr);
            $("#loadProduct").html('');
            sortNuTablePR();
            searchCodePR = 1;
        }
    }
    
    function checkQtyOrderPRT(obj){
        var pId = obj.closest("tr").find("input[name='product_id[]']").val();
        var exp = obj.closest("tr").find("input[name='expired_date[]']").val();
        if(pId != ""){
            var qtyInStock = obj.closest("tr").find("input[name='inv_qty[]']").val();
            if(getTotalProductOrderPRT(pId, exp) > qtyInStock){
                var question = "<?php echo MESSAGE_OUT_OF_STOCK; ?>";
                obj.closest("tr").find("input[name='qty[]']").val(0);
                $("#dialog").html('<p>'+question+'</p>');
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
                            onChangeQtyPR(obj.closest("tr"));
                            obj.closest("tr").find("input[name='qty[]']").select().focus();
                        }
                    }
                });
            }
        }
    }

    function checkExistingRecordPR(productId){
        var isFound = false;
        $("#tblPurchaseReturn").find("tr").each(function(){
            if(productId == $(this).find("input[name='product_id[]']").val()){
                isFound = true;
            }
        });
        return isFound;
    }

    function onChangeQtyPR(current){
        var trCurrent   = current.closest("tr");
        var qty         = trCurrent.find("input[name='qty[]']");
        var total_qty   = trCurrent.find("input[name='total_qty[]']");
        var unitPrice   = trCurrent.find("input[name='unit_price[]']");
        var totalPrice  = trCurrent.find("input[name='total_price[]']");
        var conversion  = trCurrent.find("input[name='conversion[]']");
        // Calculate Total Order
        var calQtyOrder = converDicemalJS(replaceNum(qty.val()) * replaceNum(conversion.val()));
        total_qty.val(calQtyOrder);
        // Calculate Total Cost
        var totalCost   = converDicemalJS(replaceNum(qty.val()) * replaceNum(unitPrice.val()));
        totalPrice.val(totalCost.toFixed(2));
        getTotalAmountPR();
    }
    
    function getTotalAmountPR(){
        var totalAmount = 0;
        var totalVatPer = replaceNum($("#PurchaseReturnVatPercent").val());
        var totalVat    = 0;
        var grandTotal  = 0;
        var vatCal           = $("#PurchaseReturnVatCalculate").val();
        var totalBfDis       = 0;
        var totalAmtCalVat   = 0;
        $("#tblPurchaseReturn").find(".total_price").each(function(){
            if(replaceNum($(this).val()) != '' || replaceNum($(this).val()) != undefined ){
                totalAmount += replaceNum($(this).val());
            }
        });
        totalAmtCalVat = totalAmount;
        if(vatCal == 1){
            $(".tblPurchaseReturnList").each(function(){
                var qty   = replaceNum($(this).find(".qty").val());
                var price = replaceNum($(this).find(".unit_price").val());
                totalBfDis += replaceNum(converDicemalJS(qty * price));
            });
            totalAmtCalVat = totalBfDis;
        }
        totalVat   = replaceNum(converDicemalJS((totalAmtCalVat * totalVatPer) / 100).toFixed(2));
        grandTotal = totalAmount + totalVat;
        $("#PurchaseReturnTotalAmount").val((totalAmount).toFixed(2));
        $("#PurchaseReturnTotalVat").val((totalVat).toFixed(2));
        $("#PurchaseReturnSubTotalAmount").val((grandTotal).toFixed(2));
    }
    
    function sortNuTablePR(){
        var sort = 1;
        $(".tblPurchaseReturnList").each(function(){
            $(this).find("td:eq(0)").html(sort);
            sort++;
        });
    }
    
    function clearNullPR(){
        $(".float").each(function(){
            if($(this).val() == ""){
                $(this).val(0);
            }
        });
    }
    
    function addNotePR(currentTr){
        var note = currentTr.closest("tr").find(".note");
        $("#dialog").html("<textarea style='width:350px; height: 200px;' id='noteCommentPR'>"+note.val()+"</textarea>").dialog({
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
                    note.val($("#noteCommentPR").val());
                    $(this).dialog("close");
                }
            }
        });
    }
    
    function checkEventPR(){
        eventKeyPR();
        $(".tblPurchaseReturnList").unbind("click");
        $(".tblPurchaseReturnList").click(function(){
            eventKeyPR();
        });
    }
    
    // Search Product By Expiry Date
    function searchProductByExpPR(productId, obj){
        $("#PurchaseReturnOrderDate").datepicker("option", "dateFormat", "yy-mm-dd");
        var locationId = $("#PurchaseReturnLocationId").val();
        var orderDate  = $("#SalesOrderOrderDate").val();
        $("#PurchaseReturnOrderDate").datepicker("option", "dateFormat", "dd/mm/yy");
        if(locationId != '' && orderDate != ''){
            $.ajax({
                type:   "POST",
                url:    "<?php echo $this->base . "/".$this->params['controller']."/getProductByExp/"; ?>"+productId+"/"+locationId+"/"+orderDate,
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
                                $(this).dialog("close");
                                if($("input[name='chkProductByExpPR']:checked").val()){
                                    var totalQty = $("input[name='chkProductByExpPR']:checked").val();
                                    var proExp   = $("input[name='chkProductByExpPR']:checked").attr("exp");
                                    obj.closest("tr").find(".total_qty").val(totalQty);
                                    obj.closest("tr").find("input[name='expired_date[]']").val(proExp);
                                    onChangeQtyPR(obj);
                                    checkQtyOrderPRT(obj);
                                }
                            }
                        }
                    });
                }
            });
        }
    }
</script>
<div class="inputContainer" style="width: 100%;" id="searchFormBR">
    <table style="width: 100%;">
        <tr>
            <td style="width: 400px; text-align: left;">
                <?php
                if($allowAddProduct){
                ?>
                <div class="addnew">
                    <input type="text" id="PurchaseReturnCodeProduct" style="width:360px; height: 25px; border: none; background: none;" placeholder="<?php echo TABLE_SEARCH_SKU_NAME; ?>" />
                    <img alt="<?php echo MENU_PRODUCT_MANAGEMENT_ADD; ?>" align="absmiddle" style="cursor: pointer; width: 20px;" id="addProductPurchaseReturn" onmouseover="Tip('<?php echo MENU_PRODUCT_MANAGEMENT_ADD; ?>')" src="<?php echo $this->webroot . 'img/button/plus-32.png'; ?>" />
                </div>
                <?php
                } else {
                ?>
                <input type="text" id="PurchaseReturnCodeProduct" style="width:90%; height: 25px;" placeholder="<?php echo TABLE_SEARCH_SKU_NAME; ?>" />
                <?php
                }
                ?>
            </td>
            <td id="divSearchBR" style="text-align: left;">
                &nbsp;&nbsp;<img alt="<?php echo TABLE_SHOW_PRODUCT_LIST; ?>" align="absmiddle" style="cursor: pointer;"class="searchProductPR" onmouseover="Tip('<?php echo TABLE_SHOW_PRODUCT_LIST; ?>')" src="<?php echo $this->webroot . 'img/button/search.png'; ?>" />
                <?php
                if ($allowAddService) {
                ?>
                <img alt="<?php echo SALES_ORDER_ADD_SERVICE; ?>" style="cursor: pointer;" align="absmiddle" class="addServicePR" onmouseover="Tip('<?php echo SALES_ORDER_ADD_SERVICE; ?>')" src="<?php echo $this->webroot . 'img/button/service.png'; ?>" /> 
                <?php
                }
                if ($allowAddMisc) {
                ?>
                &nbsp;&nbsp;<img alt="<?php echo SALES_ORDER_ADD_MISCELLANEOUS; ?>" style="cursor: pointer; height: 32px;" align="absmiddle" class="addMiscellaneousPR" onmouseover="Tip('<?php echo SALES_ORDER_ADD_MISCELLANEOUS; ?>')" src="<?php echo $this->webroot . 'img/button/misc.png'; ?>" />
                <?php
                }
                ?>
            </td>
        </tr>
    </table>
</div>
<div style="clear: both;"></div>
<table id="tblPurchaseReturnHeader" class="table" cellspacing="0" style="margin-top: 5px; padding:0px; width: 99%">
    <tr>
        <th class="first" style="width:7%;"><?php echo TABLE_NO; ?></th>
        <th style="width:10%"><?php echo TABLE_BARCODE; ?></th>
        <th style="width:24%;"><?php echo TABLE_PRODUCT_NAME; ?></th>
        <th style="width:11%;"><?php echo TABLE_EXP_DATE_SHORT; ?></th>
        <th style="width:8%;"><?php echo TABLE_QTY; ?></th>
        <th style="width:10%;"><?php echo TABLE_UOM; ?></th>
        <th style="width:10%;"><?php echo TABLE_UNIT_COST; ?></th>
        <th style="width:10%;"><?php echo TABLE_TOTAL_COST; ?></th>
        <th style="width:10%;"></th>
    </tr>
</table>
<div id="bodyListPR">
    <table id="tblPurchaseReturn" class="table" cellspacing="3" style="padding: 0px; width:100%">
        <tr id="OrderListRowPR" class="tblPurchaseReturnList" style="visibility: hidden;" >
            <td class="first" style="width:7%; text-align: center; padding: 0px; height: 30px;"></td>
            <td style="width:10%; text-align: left; padding: 5px;">
                <input type="text" class="lblSKU" readonly="readonly" style="width: 90%;" />
            </td>
            <td style="width:24%; text-align: center; padding: 0px;">
                <div class="inputContainer" style="width:100%">
                    <input type="hidden" name="inv_qty[]" />
                    <input type="hidden" name="total_qty[]" class="total_qty" />
                    <input type="hidden" name="product_id[]" class="product_id" />
                    <input type="hidden" name="service_id[]" />
                    <input type="hidden" value="1" name="small_val_uom[]" class="small_val_uom" />
                    <input type="hidden" value="1" name="conversion[]" class="conversion" />
                    <input type="hidden" name="note[]" class="note" />
                    <input type="text" id="product" readonly="readonly" name="product[]" class="product validate[required]" style="width: 80%;" />
                    <img alt="Note" src="<?php echo $this->webroot . 'img/button/note.png'; ?>" class="notePR" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Note')" />
                </div>
            </td>
            <td style="width:11%; text-align: center;padding: 0px;">
                <div class="inputContainer" style="width:100%">
                    <input type="text" id="expired_date" name="expired_date[]" style="width:80%;" class="expired_date" />
                </div>
            </td>
            <td style="width:8%; text-align: center;padding: 0px;">
                <div class="inputContainer" style="width:100%">
                    <input type="text" id="qty" name="qty[]" style="width:80%;" class="qty" />
                </div>
            </td>
            <td style="width:10%; padding: 0px; text-align: center">
                <div class="inputContainer" style="width:100%">
                    <select id="qty_uom_id" style="width:80%; height: 20px;" name="qty_uom_id[]" class="qty_uom_id validate[required]" >
                        <?php
                        foreach ($uoms as $uom) {
                            echo "<option value='{$uom['Uom']['id']}' conversion='1'>{$uom['Uom']['name']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </td>
            <td style="width: 10%; padding: 0px; text-align: center">
                <div class="inputContainer" style="width:100%">
                    <input type="hidden" class="org_price" />
                    <input type="text" id="unit_price" name="unit_price[]" style="width:80%;" class="unit_price float validate[required]" />
                </div>
            </td>
            <td style="width:10%; text-align: center; padding: 0px;">
                <div class="inputContainer" style="width:100%">
                    <input type="text" name="total_price[]" style="width:80%" class="total_price float" />
                </div>
            </td>
            <td style="width:10%; text-align: center; padding: 0px;">
                <img alt="Remove" src="<?php echo $this->webroot . 'img/button/cross.png'; ?>" class="btnRemoveBR" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Remove')" />
                &nbsp; <img alt="Up" src="<?php echo $this->webroot . 'img/button/move_up.png'; ?>" class="btnMoveUpBR" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Up')" />
                &nbsp; <img alt="Down" src="<?php echo $this->webroot . 'img/button/move_down.png'; ?>" class="btnMoveDownBR" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Down')" />
            </td>
        </tr>
    </table>
</div>