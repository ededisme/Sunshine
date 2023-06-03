<?php
// Get Decimal
$sqlOption = mysql_query("SELECT product_cost_decimal, uom_detail_option FROM setting_options");
$rowOption = mysql_fetch_array($sqlOption);

$checkICS = 0;
$sqlCheckPgroupUse = mysql_query("SELECT id FROM inventories WHERE product_id = ".$this->data['Product']['id']." LIMIT 1"); 
if(mysql_num_rows($sqlCheckPgroupUse)){
    $checkICS = 1;
}
$checkStock = mysql_query("SELECT IFNULL(SUM(total_qty), 0) AS total FROM product_inventories WHERE product_id = ".$this->data['Product']['id']);
$rowStock   = mysql_fetch_array($checkStock);
include("includes/function.php");
// Authentication
$this->element('check_access');
$allowDelete   = checkAccess($user['User']['id'], $this->params['controller'], 'delete');
$allowSetCost  = checkAccess($user['User']['id'], $this->params['controller'], 'setCost');
$allowSetPrice = checkAccess($user['User']['id'], $this->params['controller'], 'productPrice');
$allowAddPgroup = checkAccess($user['User']['id'], $this->params['controller'], 'addPgroup');
$allowAddUoM    = checkAccess($user['User']['id'], $this->params['controller'], 'addUom');
$allowAddBrand  = checkAccess($user['User']['id'], $this->params['controller'], 'addBrand');
$frmName = "frm" . rand();
$frmNameMain = "frmMain" . rand();
$dialogPhoto = "dialogPhoto" . rand();
$cropPhoto = "cropPhoto" . rand();
$photoNameHidden = "photoNameHidden" . rand();
echo $this->element('prevent_multiple_submit'); 

$productUseReady = 0;
$inventoryTotal['qty'] = 0;
$queryProductInUsed = mysql_query("SELECT SUM(qty) AS qty FROM inventories WHERE product_id=" . $this->data['Product']['id']." GROUP BY product_id");

// Quotation
$queryQuotationProductInUsed = mysql_query("SELECT count(quotations.id) FROM quotations INNER JOIN quotation_details ON quotations.id = quotation_details.quotation_id WHERE quotations.status >= 1 AND quotation_details.product_id=" . $this->data['Product']['id']." GROUP BY quotation_details.product_id");
// Order
$queryOrderProductInUsed = mysql_query("SELECT count(orders.id) FROM orders INNER JOIN order_details ON orders.id = order_details.order_id WHERE orders.status >= 1 AND order_details.product_id=" . $this->data['Product']['id']." GROUP BY order_details.product_id");
// Credit Memo
$queryCMProductInUsed = mysql_query("SELECT count(credit_memos.id) FROM credit_memos INNER JOIN credit_memo_details ON credit_memos.id = credit_memo_details.credit_memo_id WHERE credit_memos.status >= 1 AND credit_memo_details.product_id=" . $this->data['Product']['id']." GROUP BY credit_memo_details.product_id");
// Transfer Order
$queryRequestStockProductInUsed = mysql_query("SELECT count(request_stocks.id) FROM request_stocks INNER JOIN request_stock_details ON request_stocks.id = request_stock_details.request_stock_id WHERE request_stocks.status >= 1 AND request_stock_details.product_id=" . $this->data['Product']['id']." GROUP BY request_stock_details.product_id");
// Inventory Adjustment
$queryInvAdjProductInUsed = mysql_query("SELECT count(cycle_products.id) FROM cycle_products INNER JOIN cycle_product_details ON cycle_products.id = cycle_product_details.cycle_product_id WHERE cycle_products.status >= 1 AND cycle_product_details.product_id=" . $this->data['Product']['id']." GROUP BY cycle_product_details.product_id");

// Purchase Request
$queryPOProductInUsed = mysql_query("SELECT count(purchase_requests.id) FROM purchase_requests INNER JOIN purchase_request_details ON purchase_requests.id = purchase_request_details.purchase_request_id WHERE purchase_requests.status >= 1 AND purchase_request_details.product_id=" . $this->data['Product']['id']." GROUP BY purchase_request_details.product_id");
// Purchase Bill
$queryPBProductInUsed = mysql_query("SELECT count( purchase_orders.id) FROM purchase_orders INNER JOIN purchase_order_details ON  purchase_orders.id = purchase_order_details.purchase_order_id WHERE  purchase_orders.status >= 1 AND purchase_order_details.product_id=" . $this->data['Product']['id']." GROUP BY purchase_order_details.product_id");


if (mysql_num_rows($queryProductInUsed) || mysql_num_rows($queryQuotationProductInUsed) || mysql_num_rows($queryOrderProductInUsed) || mysql_num_rows($queryCMProductInUsed) || mysql_num_rows($queryRequestStockProductInUsed) || mysql_num_rows($queryInvAdjProductInUsed) || mysql_num_rows($queryPOProductInUsed) || mysql_num_rows($queryPBProductInUsed)) {
    $productUseReady = 1;
}
?>
<script type="text/javascript">
    
    //Multi Photo
    var rowTableMultiPhoto    =  $("#OrderListMutiPhoto");
    var rowIndexMultiPhoto    = 0;
    var timeBarcodeMultiPhoto = 1;    
    
    var jcrop_api='';
    var x,y,x2,y2,w,h;
    var obj;
    function showCoords(c)
    {
        x=c.x;
        y=c.y;
        x2=c.x2;
        y2=c.y2;
        w=c.w;
        h=c.h;
    };
    var divPhotoUpload   = $("#divProductPhoto").html();
    var specialChars     = [62,33,36,64,35,37,94,38,42,40,41,95,45,43,61,47,96,126];
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        $("#cloneProductPacket").remove();
        $("#ProductDefaultCost").autoNumeric({mDec: <?php echo $rowOption[0]; ?>, aSep: ','});
        $(".float").autoNumeric({mDec: 3, aSep: ','});
        $(".interger").autoNumeric({aDec: '.', mDec: 5, aSep: ','});
        <?php
        if($allowAddBrand){
        ?>
        $("#ProductBrandId").chosen({ width: 350, allow_add: true, allow_add_label: '<?php echo MENU_BRAND_MANAGEMENT_ADD; ?>', allow_add_id: 'addNewBrandProduct' });
        $("#addNewBrandProduct").click(function(){
            $.ajax({
                type:   "GET",
                url:    "<?php echo $this->base . "/products/addBrand/"; ?>",
                beforeSend: function(){
                    $("#ProductBrandId").trigger("chosen:close");
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(msg){
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $("#dialog").html(msg);
                    $("#dialog").dialog({
                        title: '<?php echo MENU_BRAND_MANAGEMENT_ADD; ?>',
                        resizable: false,
                        modal: true,
                        width: '450',
                        height: '300',
                        position:'center',
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show();
                        },
                        buttons: {
                            '<?php echo ACTION_CLOSE; ?>': function() {
                                $(this).dialog("close");
                            },
                            '<?php echo ACTION_SAVE; ?>': function() {
                                var formName = "#BrandAddBrandForm";
                                var validateBack =$(formName).validationEngine("validate");
                                if(!validateBack){
                                    return false;
                                }else{
                                    $(this).dialog("close");
                                    $.ajax({
                                        dataType: "json",
                                        type: "POST",
                                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/addBrand",
                                        data: $("#BrandAddBrandForm").serialize(),
                                        beforeSend: function(){
                                            $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                                        },
                                        error: function (result) {
                                            $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                            createSysAct('Products', 'Quick Add Brand', 2, result);
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
                                                buttons: {
                                                    '<?php echo ACTION_CLOSE; ?>': function() {
                                                        $(this).dialog("close");
                                                    }
                                                }
                                            });
                                        },
                                        success: function(result){
                                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                                            createSysAct('Products', 'Quick Add Brand', 1, '');
                                            var msg = '<?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?>';
                                            if(result.error == 0){
                                                // Update Brand
                                                $("#ProductBrandId").html(result.option);
                                                $("#ProductBrandId").trigger("chosen:updated");
                                                msg = '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>';
                                            } else if(result.error == 2){
                                                msg = '<?php echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM; ?>';
                                            }
                                            // Message Alert
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
                    });
                }
            });
        });
        <?php
        } else {
        ?>
        $("#ProductBrandId").chosen({width: 350});
        <?php
        }
        if($allowAddPgroup){
        ?>
        $("#ProductPgroupId").chosen({ width: 350, allow_add: true, allow_add_label: '<?php echo MENU_PRODUCT_GROUP_MANAGEMENT_ADD; ?>', allow_add_id: 'addNewPgroupProduct' });
        $("#addNewPgroupProduct").click(function(){
            $.ajax({
                type:   "GET",
                url:    "<?php echo $this->base . "/products/addPgroup/"; ?>",
                beforeSend: function(){
                    $("#ProductPgroupId").trigger("chosen:close");
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(msg){
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $("#dialog").html(msg);
                    $("#dialog").dialog({
                        title: '<?php echo MENU_PRODUCT_GROUP_MANAGEMENT_ADD; ?>',
                        resizable: false,
                        modal: true,
                        width: '450',
                        height: '300',
                        position:'center',
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show();
                        },
                        buttons: {
                            '<?php echo ACTION_CLOSE; ?>': function() {
                                $(this).dialog("close");
                            },
                            '<?php echo ACTION_SAVE; ?>': function() {
                                var formName = "#PgroupAddPgroupForm";
                                var validateBack =$(formName).validationEngine("validate");
                                if(!validateBack){
                                    return false;
                                }else{
                                    $(this).dialog("close");
                                    $.ajax({
                                        dataType: "json",
                                        type: "POST",
                                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/addPgroup",
                                        data: $("#PgroupAddPgroupForm").serialize(),
                                        beforeSend: function(){
                                            $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                                        },
                                        error: function (result) {
                                            $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                            createSysAct('Products', 'Quick Add Pgroup', 2, result);
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
                                                buttons: {
                                                    '<?php echo ACTION_CLOSE; ?>': function() {
                                                        $(this).dialog("close");
                                                    }
                                                }
                                            });
                                        },
                                        success: function(result){
                                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                                            createSysAct('Products', 'Quick Add Pgroup', 1, '');
                                            var msg = '<?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?>';
                                            if(result.error == 0){
                                                // Update Pgroup
                                                $("#ProductPgroupId").html(result.option);
                                                $("#ProductPgroupId").trigger("chosen:updated");
                                                msg = '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>';
                                            } else if(result.error == 2){
                                                msg = '<?php echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM; ?>';
                                            }
                                            // Message Alert
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
                    });
                }
            });
        });
        <?php
        } else {
        ?>
        $("#ProductPgroupId").chosen({width: 350});
        <?php
        }
        if($allowAddUoM && $productUseReady == 0){
        ?>
        $("#ProductPriceUomId").chosen({ width: 200, allow_add: true, allow_add_label: '<?php echo MENU_UOM_MANAGEMENT_ADD; ?>', allow_add_id: 'addNewUoMProduct' });
        $("#addNewUoMProduct").click(function(){
            $.ajax({
                type:   "GET",
                url:    "<?php echo $this->base . "/products/addUom/"; ?>",
                beforeSend: function(){
                    $("#ProductPriceUomId").trigger("chosen:close");
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(msg){
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $("#dialog").html(msg);
                    $("#dialog").dialog({
                        title: '<?php echo MENU_UOM_MANAGEMENT_ADD; ?>',
                        resizable: false,
                        modal: true,
                        width: '1000',
                        height: '400',
                        position:'center',
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show();
                        },
                        buttons: {
                            '<?php echo ACTION_CLOSE; ?>': function() {
                                $(this).dialog("close");
                            },
                            '<?php echo ACTION_SAVE; ?>': function() {
                                var formName     = "#UomAddUomForm";
                                var validateBack = $(formName).validationEngine("validate");
                                if(!validateBack){
                                    return false;
                                }else{
                                    $(this).dialog("close");
                                    $.ajax({
                                        dataType: "json",
                                        type: "POST",
                                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/addUom",
                                        data: $("#UomAddUomForm").serialize(),
                                        beforeSend: function(){
                                            $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                                        },
                                        error: function (result) {
                                            $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                            createSysAct('Products', 'Quick Add UoM', 2, result);
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
                                                buttons: {
                                                    '<?php echo ACTION_CLOSE; ?>': function() {
                                                        $(this).dialog("close");
                                                    }
                                                }
                                            });
                                        },
                                        success: function(result){
                                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                                            createSysAct('Products', 'Quick Add UoM', 1, '');
                                            var msg = '<?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?>';
                                            if(result.error == 0){
                                                // Update Pgroup
                                                $("#ProductPriceUomId").html(result.option);
                                                $("#ProductPriceUomId").trigger("chosen:updated");
                                                $("#ProductPriceUomId").change();
                                                msg = '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>';
                                            } else if(result.error == 2){
                                                msg = '<?php echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM; ?>';
                                            }
                                            // Message Alert
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
                    });
                }
            });
        });
        <?php
        } else if($productUseReady == 0) {
        ?>
        $("#ProductPriceUomId").chosen({width: 200});
        <?php
        }
        ?>
        // Protect Input
        $("#ProductBarcode, #ProductCode").keypress(function(event) {
            if($.inArray(event.which,specialChars) != -1) {
                event.preventDefault();
            }
        });
        $("#ProductEditForm").validationEngine();
        // Form Action Save
        $("#ProductEditForm").ajaxForm({
            beforeSerialize: function($form, options) {
                if($("#ProductPgroupId").val() == null || $("#ProductPgroupId").val() == ""){
                    alertSelectGroupProduct();
                    return false;
                }
                <?php
                if($productUseReady == 0) {
                ?>
                if($("#ProductPriceUomId").val() == null || $("#ProductPriceUomId").val() == ""){
                    alertSelectUoMProduct();
                    return false;
                }
                <?php
                }
//                if(count($branches) > 1){
                ?>
//                listbox_selectall('productBranchSelected', true);
//                if($("#productBranchSelected").val() == null){
//                    alertSelectBranchProduct();
//                    return false;
//                }
                <?php
//                }
                ?>
                $("#ProductPeriodFrom, #ProductPeriodTo").datepicker("option", "dateFormat", "yy-mm-dd");
                $(".float, .interger, #ProductDefaultCost").each(function(){
                    $(this).val($(this).val().replace(/,/g,""));
                });
            },
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveProduct").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                if(result != '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>' && result != '<?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?>' && result != '<?php echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM; ?>'){
                    createSysAct('Products', 'Edit', 2, result);
                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                }else {
                    createSysAct('Products', 'Edit', 1, '');
                    // alert message
                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result+'</p>');
                }
                $(".btnBackProduct").click();
                $("#dialog").dialog({
                    title: '<?php echo DIALOG_INFORMATION; ?>',
                    resizable: false,
                    modal: true,
                    width: 'auto',
                    height: 'auto',
                    position: 'center',
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
        $(".btnBackProduct").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableProductDashBoard.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
        $("#lgWeiSizePro").click(function(){
            var status = $("#tblWeiSizePro").attr("status");
            if(status == 0){
                $("#tblWeiSizePro").hide();
                $("#tblWeiSizePro").attr("status", '1');
            }else if(status == 1){
                $("#tblWeiSizePro").show();
                $("#tblWeiSizePro").attr("status", '0');
            }
        });
        <?php
        if($productUseReady == 0) {
        ?>
        $("#ProductPriceUomId").change(function(){
            var companyId     = $("#ProductCompanyId").val();
            var val = $(this).val();
            var obj = $(this);
            if(companyId != ''){
                if(val != ''){
                    $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/getSkuUom/"+val,
                        data: '',
                        beforeSend: function(){
                            $(".btnSavePro").hide();
                            obj.attr('disabled',true);
                            $("#loadUomPro").show();
                        },
                        success: function(result){
                            $("#loadUomPro").hide(); 
                            obj.removeAttr('disabled');
                            if(result != 'Error Select Uom'){
                                $("#dvSkuUomPro").html(result);
                                onBlurSkuUomPro();
                            }
                            $(".btnSavePro").show();
                        }
                    });
                }else{
                    $("#dvSkuUomPro").html('');
                }
            }else{
                obj.find("option[value='']").attr("selected","selected");
            }
        });
        <?php
        }
        ?>
        $("#ProductBarcode").blur(function(){
            var companyId     = $("#ProductCompanyId").val();
            var puc           = $(this);
            var productId     = $("#ProductId").val();
            var imgLoad       = $(this).closest("tr").find(".loadSkuUomPro");
            var available     = $(this).closest("tr").find(".availableSkuUomPro");
            var noneAvailable = $(this).closest("tr").find(".noneAvailableSkuUomPro");
            if(companyId != ''){
                if(puc.val() != ''){
                    $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/checkPucEdit/"+companyId+"/"+puc.val()+"/"+productId,
                        data: '',
                        beforeSend: function(){
                            imgLoad.show();
                            noneAvailable.hide();
                            available.hide();
                            $(".btnSavePro").hide();
                        },
                        success: function(result){
                            imgLoad.hide(); 
                            if(result == 'available'){
                                noneAvailable.hide();
                                available.show();
                                puc.select().focus();
                            }else if(result == 'not available'){
                                noneAvailable.show();
                                available.hide();
                            }else if(result == 'Error UPC'){
                                puc.val('');
                            }
                            $(".btnSavePro").show();
                        }
                    });
                }
            }else{
                puc.val("");
            }
        });
        
        $("#ProductCode").blur(function(){
            var companyId     = $("#ProductCompanyId").val();
            var sku           = $(this);
            var productId     = $("#ProductId").val();
            var imgLoad       = $(this).closest("tr").find(".loadSkuUomPro");
            var available     = $(this).closest("tr").find(".availableSkuUomPro");
            var noneAvailable = $(this).closest("tr").find(".noneAvailableSkuUomPro");
            var checkSku      = true;
            if(companyId != ''){
                if ($('.skuUomPro').length) {
                    $(".skuUomPro").each(function(){
                        var obj = $(this);
                        if(obj.val() == sku.val()){
                            checkSku = false;
                            return false;
                        }
                    });
                }
                if(checkSku == true && sku.val() != ''){
                    $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/checkSkuUomEdit/"+companyId+"/"+sku.val()+"/"+productId,
                        data: '',
                        beforeSend: function(){
                            imgLoad.show();
                            noneAvailable.hide();
                            available.hide();
                            $(".btnSavePro").hide();
                        },
                        success: function(result){
                            imgLoad.hide(); 
                            if(result == 'available'){
                                noneAvailable.hide();
                                available.show();
                                sku.select().focus();
                            }else if(result == 'not available'){
                                noneAvailable.show();
                                available.hide();
                            }else if(result == 'Error Sku'){
                                sku.val('');
                            }
                            $(".btnSavePro").show();
                        }
                    });
                }else{
                    noneAvailable.hide();
                    available.show();
                    sku.val('');
                }
            }else{
                sku.val("");
            }
        });
        
        $(".searchProductParent").click(function(){
            var companyId     = $("#ProductCompanyId").val();
            if(companyId != ''){
                $.ajax({
                    type:   "POST",
                    url:    "<?php echo $this->base . '/' . $this->params['controller']; ?>/product/"+companyId,
                    beforeSend: function(){
                        $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                    },
                    success: function(msg){
                        $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                        $("#dialog").html(msg).dialog({
                            title: '<?php echo PRODUCT_PARENT; ?>',
                            resizable: false,
                            modal: true,
                            width: 850,
                            height: 500,
                            position:'center',
                            open: function(event, ui){
                                $(".ui-dialog-buttonpane").show();
                                $(".ui-dialog-titlebar-close").show();
                            },
                            buttons: {
                                '<?php echo ACTION_OK; ?>': function() {
                                    if($("input[name='chkProductPacket']:checked").val()){
                                        var id   = $("input[name='chkProductPacket']:checked").val();
                                        var name = $("input[name='chkProductPacket']:checked").attr('code')+" - "+$("input[name='chkProductPacket']:checked").attr('abbr');
                                        $("#ProductParentId").val(id);
                                        $("#ProductParentName").val(name);
                                        $(".searchProductParent").hide();
                                        $(".deleteProductParent").show();
                                    }
                                    $(this).dialog("close");
                                }
                            }
                        });
                    }
                });
            }
        });
        
        $(".deleteProductParent").click(function(){
            $(".searchProductParent").show();
            $(".deleteProductParent").hide();
            $("#ProductParentId").val('');
            $("#ProductParentName").val('');
        });
        
        var dates = $("#ProductPeriodFrom, #ProductPeriodTo").datepicker({
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true,
            onSelect: function( selectedDate ) {
                var option = this.id == "ProductPeriodFrom" ? "minDate" : "maxDate",
                    instance = $( this ).data( "datepicker" );
                    date = $.datepicker.parseDate(
                        instance.settings.dateFormat ||
                        $.datepicker._defaults.dateFormat,
                        selectedDate, instance.settings );
                dates.not( this ).datepicker( "option", option, date );
                $("#ProductPeriodFrom, #ProductPeriodTo").removeAttr('class');
                $("#ProductPeriodFrom, #ProductPeriodTo").attr('class', 'hasDatepicker validate[required]');
            }
        });
        
        $("#clearPeriod").click(function(){
            $("#ProductPeriodFrom, #ProductPeriodTo").val('');
            $("#ProductPeriodFrom, #ProductPeriodTo").removeAttr('class');
            $("#ProductPeriodFrom, #ProductPeriodTo").attr('class', 'hasDatepicker');
        });
        <?php
        if($allowDelete){
        ?>
        $(".btnDeleteProduct").click(function(event){
            event.preventDefault();
            var name = "<?php echo $this->data['Product']['code'].' - '.$this->data['Product']['name']; ?>";
            $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_CONFIRM_DELETE; ?> <b>' + name + '</b>?</p>');
            $("#dialog").dialog({
                title: '<?php echo DIALOG_CONFIRMATION; ?>',
                resizable: false,
                modal: true,
                width: 'auto',
                height: 'auto',
                position: 'center',
                open: function(event, ui){
                    $(".ui-dialog-buttonpane").show();
                },
                buttons: {
                    '<?php echo ACTION_DELETE; ?>': function() {
                        $.ajax({
                            type: "GET",
                            url: "<?php echo $this->base.'/'.$this->params['controller']; ?>/delete/<?php echo $this->data['Product']['id']; ?>",
                            data: "",
                            beforeSend: function(){
                                $("#dialog").dialog("close");
                                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                            },
                            success: function(result){
                                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                $(".btnBackProduct").click();
                                if(result != '<?php echo MESSAGE_DATA_HAVE_CHILD; ?>' && result != '<?php echo MESSAGE_DATA_HAS_BEEN_DELETED; ?>'){
                                    createSysAct('Products', 'Delete', 2, result);
                                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                                }else {
                                    createSysAct('Products', 'Delete', 1, '');
                                    // alert message
                                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result+'</p>');
                                }
                                // alert message
                                $("#dialog").dialog({
                                    title: '<?php echo DIALOG_INFORMATION; ?>',
                                    resizable: false,
                                    modal: true,
                                    width: 'auto',
                                    height: 'auto',
                                    position: 'center',
                                    buttons: {
                                        '<?php echo ACTION_CLOSE; ?>': function() {
                                            $(this).dialog("close");
                                        }
                                    }
                                });
                            }
                        });
                    },
                    '<?php echo ACTION_CANCEL; ?>': function() {
                        $(this).dialog("close");
                    }
                }
            });
        });
        <?php
        }
        if($allowSetPrice){
        ?>
        $(".btnSetPriceProduct").click(function(event){
            event.preventDefault();
            var obj = $(this);
            $.ajax({
                type:   "POST",
                url:    "<?php echo $this->base . "/products/productPrice/".$this->data['Product']['id']; ?>",
                beforeSend: function(){
                    obj.find("img").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(msg){
                    obj.find("img").attr('src', '<?php echo $this->webroot; ?>img/button/salary.png');
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $("#dialog").html(msg);
                    $("#dialog").dialog({
                        title: '<?php echo ACTION_SET_PRICE; ?>',
                        resizable: false,
                        modal: true,
                        width: '95%',
                        height: '570',
                        position:'center',
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show();
                        },
                        buttons: {
                            '<?php echo ACTION_SAVE; ?>': function() {
                                var formName = "#ProductPrice";
                                var validateBack =$(formName).validationEngine("validate");
                                if(!validateBack){
                                    return false;
                                }else{
                                    $(this).dialog("close");
                                    $.ajax({
                                        type: "POST",
                                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/productPrice",
                                        data: $(":input").serialize(),
                                        beforeSend: function(){
                                            $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                                        },
                                        success: function(result){
                                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                                            if(result != '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>'){
                                                createSysAct('Products', 'Set Price', 2, result);
                                                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                                            }else {
                                                createSysAct('Products', 'Set Price', 1, '');
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
                    });
                }
            });
        });
        <?php
        }
        ?>
            
        $(".btnViewProductHistory").click(function(event){            
            event.preventDefault();
            var productId = $(this).attr('rel');
            $.ajax({
                type: "POST",
                url:    "<?php echo $this->base . "/".$this->params['controller']."/viewProductHistory"; ?>/"+ productId,
                beforeSend: function() {
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(result) {
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $("#dialog").dialog('option', 'title', '<?php echo DIALOG_CONFIRMATION; ?>');
                    $("#dialog").html(result);
                    $("#dialog").dialog({
                        title: '<?php echo TABLE_PRODUCT_INFORMATION; ?>',
                        resizable: false,
                        modal: true,
                        width: '90%',
                        height: '500',
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
        });   
        
        $(".btnAddMultiPhoto").click(function(){
            if($(this).closest("tr").find("td .ProductPhotoMultiData").val() != ""){
                addMultiPhoto(); 
            }
        });
        
        $(".showPhotoOther").click(function(){
            $(".divPhotoOther").show();
            $(".hidePhotoOther").show();
            $(".showPhotoOther").hide();
        });
        
        $(".hidePhotoOther").click(function(){
            $(".divPhotoOther").hide();
            $(".hidePhotoOther").hide();
            $(".showPhotoOther").show();
        });
        
        $(".showCatalog").click(function(){
            $(".divCatalog").show();
            $(".showCatalog").hide();
        });
        
        $(".showMainPhoto").click(function(){
            $(".divMainPhoto").show();
            $(".showMainPhoto").hide();
        });
        
        $(".hideMainPhoto").click(function(){
            $(".divMainPhoto").hide();
            $(".showMainPhoto").show();
        });
        
        $(".hideCatalog").click(function(){
            $(".divCatalog").hide();
            $(".showCatalog").show();
        });
        
        // From Action Upload Photo
        $("#<?php echo $frmNameMain; ?>").ajaxForm({
            beforeSerialize: function($form, options) {
                extArray = new Array(".bmp",".jpg",".gif",".tif",".png");
                allowSubmit = false;
                file = $("#ProductMainPhoto").val();
                if (!file) return;
                while (file.indexOf("\\") != -1)
                    file = file.slice(file.indexOf("\\") + 1);
                ext = file.slice(file.indexOf(".")).toLowerCase();
                for (var i = 0; i < extArray.length; i++) {
                    if (extArray[i] == ext) { allowSubmit = true; break; }
                }
                if (!allowSubmit){
                    // alert message
                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>Please only upload files that end in types: <b>' + (extArray.join("  ")) + '</b>. Please select a new file to upload again.</p>');
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
                    return false;
                }
            },
            beforeSend: function() {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                var photoFolder='';
                var photoName=result;
                photoFolder="public/product_photo/tmp/";
                $('#<?php echo $cropPhoto; ?>').attr("src", "<?php echo $this->webroot; ?>" + photoFolder + photoName + "?" + Math.random());
                if(jcrop_api==''){
                    $('#<?php echo $cropPhoto; ?>').Jcrop({
                        setSelect: [0,0,10000,10000],
                        allowSelect: false,
                        onChange:   showCoords,
                        onSelect:   showCoords
                    },function(){
                        jcrop_api = this;
                    });
                }else{
                    jcrop_api.setImage("<?php echo $this->webroot; ?>" + photoFolder + photoName);
                    jcrop_api.setSelect([0,0,10000,10000]);
                }
                $("#<?php echo $dialogPhoto; ?>").dialog({
                    title: 'Crop Image',
                    resizable: false,
                    modal: true,
                    width: '90%',
                    height: '400',
                    position: 'center',
                    open: function(event, ui){
                        $(".ui-dialog-buttonpane").show();
                    },
                    buttons: {
                        'Crop': function() {
                            $.ajax({
                                type: "POST",
                                url: "<?php echo $this->base; ?>/products/cropPhoto",
                                data: "photoFolder=" + photoFolder.replace(/\//g,"|||") + "&photoName=" + photoName + "&x=" + x + "&y=" + y + "&x2=" + x2 + "&y2=" + y2 + "&w=" + w + "&h=" + h,
                                beforeSend: function(){
                                    $("#<?php echo $dialogPhoto; ?>").dialog("close");
                                },
                                success: function(result){
                                    $("#mainPhotoDisplay").attr("src", "<?php echo $this->webroot; ?>" + photoFolder + "thumbnail/" + result);
                                    $("#<?php echo $photoNameHidden; ?>").val(result);
                                }
                            });
                        }
                    }
                });
            }
        });
        $("#ProductMainPhoto").live('change', function(){
            $("#<?php echo $frmNameMain; ?>").submit();
        });

        onBlurSkuUomPro();
        changeInputCSSProduct();
        eventKeyMultiPhoto(0);
        $("#loadMultiPhoto").html('');
    });
    
    function eventKeyMultiPhoto(rel){
        //Remove Photo in Crop
        $('#<?php echo $cropPhoto; ?>').removeAttr("src");
        
        // From Action Upload Photo
        $(".<?php echo $frmName; ?>").ajaxForm({
            beforeSerialize: function($form, options) {
                extArray = new Array(".bmp",".jpg",".gif",".tif",".png");
                allowSubmit = false;
                file = $(".ProductPhoto").val();
                if (!file) return;
                while (file.indexOf("\\") != -1)
                    file = file.slice(file.indexOf("\\") + 1);
                ext = file.slice(file.indexOf(".")).toLowerCase();
                for (var i = 0; i < extArray.length; i++) {
                    if (extArray[i] == ext) { allowSubmit = true; break; }
                }
                if (!allowSubmit){
                    // alert message
                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>Please only upload files that end in types: <b>' + (extArray.join("  ")) + '</b>. Please select a new file to upload again.</p>');
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
                    return false;
                }
            },
            beforeSend: function() {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                
                //Explode Multi Photo
                var explodeMultiPhoto = result.split("|*|");
                var rel = explodeMultiPhoto[1];
                
                var photoFolder = '';
                var photoName   = explodeMultiPhoto;
                photoFolder="public/product_photo/tmp/";
                
                $('#<?php echo $cropPhoto; ?>').attr("src", "<?php echo $this->webroot; ?>" + photoFolder + photoName + "?" + Math.random());
                if(jcrop_api==''){
                    $('#<?php echo $cropPhoto; ?>').Jcrop({
                        setSelect: [0,0,10000,10000],
                        allowSelect: false,
                        onChange:   showCoords,
                        onSelect:   showCoords
                    },function(){
                        jcrop_api = this;
                    });
                }else{
                    jcrop_api.setImage("<?php echo $this->webroot; ?>" + photoFolder + photoName);
                    jcrop_api.setSelect([0,0,10000,10000]);
                }
                $("#<?php echo $dialogPhoto; ?>").dialog({
                    title: 'Crop Image',
                    resizable: false,
                    modal: true,
                    width: '90%',
                    height: '400',
                    position: 'center',
                    open: function(event, ui){
                        $(".ui-dialog-buttonpane").show();
                    },
                    buttons: {
                        'Crop': function() {
                            $.ajax({
                                type: "POST",
                                url: "<?php echo $this->base; ?>/products/cropPhoto",
                                data: "photoFolder=" + photoFolder.replace(/\//g,"|||") + "&photoName=" + photoName + "&x=" + x + "&y=" + y + "&x2=" + x2 + "&y2=" + y2 + "&w=" + w + "&h=" + h,
                                beforeSend: function(){
                                    $("#<?php echo $dialogPhoto; ?>").dialog("close");
                                },
                                success: function(result){
                                    //Set Rel Multi Photo
                                    if(rel == 0){
                                        rel = "";
                                    }
                                    $("#photoDisplay"+rel).attr("src", "<?php echo $this->webroot; ?>" + photoFolder + "thumbnail/" + result);
                                    $("#ProductPhotoMultiData"+rel).val(result);
                                    loadMultiPhoto();
                                }
                            });
                        }
                    }
                });
            }
        });
        
        $(".ProductPhoto").click(function(){
            //Set Rel Multi Photo
            var rel = $(this).attr("rel");
            var url = '<?php echo $this->base; ?>';
            if(rel == 0){
                rel = "";
            }
            $("#ProductPhoto"+rel).live('change', function(){
                $(".<?php echo $frmName; ?>").removeAttr("action");
                $(".<?php echo $frmName; ?>").attr("action", url+"/products/upload/"+((rel=="")?0:rel));
                $(".<?php echo $frmName; ?>").submit();
            });
        });
        
        $(".btnnRemoveMultiPhoto").click(function(){
            btnnRemoveMultiPhoto($(this));
        });
        
        $(".btnnRemoveOldMultiPhoto").click(function(){
            var id   = $(this).attr("rel");
            var data = $(this).closest("tr").find(".ProductPhotoMultiData").val();
            $(this).closest("tr").remove();
            $.ajax({
                type: "POST",
                url: "<?php echo $this->base.'/'.$this->params['controller']; ?>/removePhoto/" + id,
                data: "photo="+data,
                beforeSend: function(){
                    $("#<?php echo $dialogPhoto; ?>").dialog("close");
                },
                success: function(result){
                }
            });
        });
        
        loadMultiPhoto();
    }
    
    function addMultiPhoto(){
        var tr  = rowTableMultiPhoto.clone(true);
        rowIndexMultiPhoto = parseInt($(".tblMutiPhotoList:last").find(".ProductPhoto").attr("rel")) + 1;
        tr.removeAttr("style").removeAttr("id");
        
        tr.find(".photoDisplay").removeAttr("id").removeAttr("id");
        tr.find(".ProductPhoto").removeAttr("id").removeAttr("id");
        tr.find(".ProductPhotoMultiData").removeAttr("id").removeAttr("id");
        
        tr.find(".photoDisplay").attr("id", "photoDisplay"+rowIndexMultiPhoto);
        tr.find(".photoDisplay").removeAttr("src");
        tr.find(".ProductPhoto").attr("id", "ProductPhoto"+rowIndexMultiPhoto).val("");
        tr.find(".ProductPhotoMultiData").attr("id", "ProductPhotoMultiData"+rowIndexMultiPhoto).val("");
        
        tr.find(".ProductPhoto").attr("rel", rowIndexMultiPhoto);
        $("#tblMutiPhoto").append(tr);
        eventKeyMultiPhoto(rowIndexMultiPhoto);
    }
    
    function btnnRemoveMultiPhoto(obj){
        var currentTr = obj.closest("tr");
        currentTr.remove();
        loadMultiPhoto();
    }
    
    function loadMultiPhoto(){
        var item = 0;
        var dataItem = 0;
        var dataMultiPhoto = "";
        $("#loadMultiPhoto").html('');
        $("#tblMutiPhoto").find(".ProductPhotoMultiData").each(function(){
           dataMultiPhoto += "<input type='hidden' name='data[new_photo][]' value='"+$(this).val()+"' />";
           item++;
           if($(this).val() != ""){
               dataItem++;
           }
        });
        $("#loadMultiPhoto").html(dataMultiPhoto);
        if(item == 1){
            if(dataItem == 0){
                $(".tblMutiPhotoList").find(".btnAddMultiPhoto").hide();
            }else{
                $(".tblMutiPhotoList").find(".btnAddMultiPhoto").show();
            }
            $(".tblMutiPhotoList").find(".btnnRemoveMultiPhoto").hide();
        }else{
            $(".tblMutiPhotoList").find(".btnAddMultiPhoto").hide();
            $(".tblMutiPhotoList:last").find(".btnAddMultiPhoto").show();
            $(".tblMutiPhotoList").find(".btnnRemoveMultiPhoto").show();
        }
    }  
    
    function resetFormProduct(){
        $("#ProductEditForm").find("input[value!='<?php echo INPUT_SELECT; ?>']").val('');
        // SELECT
        $("#ProductIsPacket").val(0);
        $("#ProductIsExpiredDate").find("option[value='0']").attr("selected", true);
        <?php
        if($productUseReady == 0) {
        ?>
        $("#ProductPriceUomId").find("option[value='']").attr("selected", true);
        <?php
        }
        ?>
        $("#ProductisNotForSale").find("option[value='0']").attr("selected", true);
        $("#ProductWeightUomId").find("option[value='']").attr("selected", true);
        // Production Show
        $("#dvProductPacket").hide();
        $(".divNonPacket").show();
        // Textarea
        $("#ProductEditForm").find("textarea").val('');
        // INPUT FILE
        $("#divProductPhoto").html(divPhotoUpload);
        // UOM DETAIL
        $("#dvSkuUomPro").html('');
        // SYMBOL CHECK CODE
        $(".availableSkuUomPro").hide();
        $(".noneAvailableSkuUomPro").hide();
        // Product Group
        $("#ProductPgroupId_chzn").find(".chzn-choices").find(".search-choice-close").click();
    }
    
    function onBlurSkuUomPro(){
        $(".skuUomPro").unbind("blur").unbind("keyup").unbind("keypress").unbind("focus");
        $(".skuUomPro").keypress(function(e){
            if((e.which && e.which == 13) || e.keyCode == 13){                                    
                return false;
            }
        });
        $(".skuUomPro").blur(function(){
            var companyId     = $("#ProductCompanyId").val();
            var sku           = $(this);
            var proSku        = $(this).attr("sku-uom");
            var skuMain       = $("#ProductCode").val();
            var imgLoad       = $(this).closest("tr").find(".loadSkuUomPro");
            var available     = $(this).closest("tr").find(".availableSkuUomPro");
            var noneAvailable = $(this).closest("tr").find(".noneAvailableSkuUomPro");
            var checkSkuUOm   = true;
            $(".skuUomPro").each(function(){
                var obj = $(this);
                if(obj.attr('id') != sku.attr('id') && obj.val() == sku.val()){
                    checkSkuUOm = false;
                    return false;
                }
            });
            if(sku.val() != '' && sku.val() != skuMain && checkSkuUOm == true){
                $.ajax({
                    type: "POST",
                    url: "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/checkSkuUomEdit/"+companyId+"/"+sku.val()+"/0/"+proSku,
                    data: '',
                    beforeSend: function(){
                        imgLoad.show();
                        noneAvailable.hide();
                        available.hide();
                        $(".btnSavePro").hide();
                    },
                    success: function(result){
                        imgLoad.hide(); 
                        if(result == 'available'){
                            noneAvailable.hide();
                            available.show();
                            sku.select().focus();
                        }else if(result == 'not available'){
                            noneAvailable.show();
                            available.hide();
                        }else if(result == 'Error Sku'){
                            sku.val('');
                        }
                        $(".btnSavePro").show();
                    }
                });
            }else{
                noneAvailable.hide();
                available.show();
                sku.val('');
            }
        });
    }
    
    function alertSelectCompanyPro(){
        $("#dialog").html('<p style="color:red; font-size:14px;"><?php echo MESSAGE_SELECT_COMPANY_FIRST; ?></p>');
        $("#dialog").dialog({
            title: '<?php echo DIALOG_INFORMATION; ?>',
            resizable: false,
            modal: true,
            width: 'auto',
            height: 'auto',
            position:'center',
            open: function(event, ui){
                $(".ui-dialog-buttonpane").show();
                $(".ui-dialog-titlebar-close").show();
            },
            buttons: {
                '<?php echo ACTION_CLOSE; ?>': function() {
                    $(this).dialog("close");
                    $("#ProductCompanyId").select();
                }
            }
        });
    }
    
    function changeInputCSSProduct(){
        var cssStyle  = 'inputDisable';
        var cssRemove = 'inputEnable';
        var disabled  = true;
        // Button 
        $(".searchProductParent").hide();
        $("#clearPeriod").hide();
        $(".deleteProductParent").hide();
        if($("#ProductCompanyId").val() != ''){
            cssStyle  = 'inputEnable';
            cssRemove = 'inputDisable';
            disabled  = false;
            // Button 
            $(".searchProductParent").show();
            $("#clearPeriod").show();
            $("#deleteProductParent").hide();
        } 
        // Label
        $("#ProductEditForm").find("label").removeAttr("class");
        $("#ProductEditForm").find("label").each(function(){
            var label = $(this).attr("for");
            if(label != 'ProductCompanyId'){
                $(this).addClass(cssStyle);
            }
        });
        $("label[for='ProductPhoto']").removeAttr("class");
        $("label[for='ProductPhoto']").addClass(cssStyle);
        // Input & Select
        $("#ProductEditForm").find("input").each(function(){
            $(this).removeClass(cssRemove);
            $(this).addClass(cssStyle);
        });
        $("#ProductPhoto").removeClass(cssRemove);
        $("#ProductPhoto").addClass(cssStyle);
        $("#ProductPhoto").attr("disabled", disabled);
        $("#ProductFileCatalog").attr("disabled", disabled);
        $("#ProductEditForm").find("select").each(function(){
            var selectId = $(this).attr("id");
            if(selectId != 'ProductCompanyId'){
                $(this).removeClass(cssRemove);
                $(this).addClass(cssStyle);
                $(this).attr("disabled", disabled);
            }
        });
        // Read Only
        $("#ProductEditForm").find("input").attr("readonly", disabled);
        $("#ProductEditForm").find("textarea").attr("readonly", disabled);
    }
    
    function alertSelectGroupProduct(){
        $(".btnSavePro").removeAttr('disabled');
        $("#dialog").html('<p style="color:red; font-size:14px;"><?php echo MESSAGE_CONFIRM_SELECT_GROUP; ?></p>');
        $("#dialog").dialog({
            title: '<?php echo DIALOG_INFORMATION; ?>',
            resizable: false,
            modal: true,
            closeOnEscape: false,
            width: 'auto',
            height: 'auto',
            position:'center',
            open: function(event, ui){
                $(".ui-dialog-buttonpane").show();
                $(".ui-dialog-titlebar-close").hide();
            },
            buttons: {
                '<?php echo ACTION_CLOSE; ?>': function() {
                    $(this).dialog("close");
                    $(".ui-dialog-titlebar-close").show();
                }
            }
        });
    }
    
    function alertSelectUoMProduct(){
        $(".btnSavePro").removeAttr('disabled');
        $("#dialog").html('<p style="color:red; font-size:14px;"><?php echo MESSAGE_CONFIRM_SELECT_UOM; ?></p>');
        $("#dialog").dialog({
            title: '<?php echo DIALOG_INFORMATION; ?>',
            resizable: false,
            modal: true,
            closeOnEscape: false,
            width: 'auto',
            height: 'auto',
            position:'center',
            open: function(event, ui){
                $(".ui-dialog-buttonpane").show();
                $(".ui-dialog-titlebar-close").hide();
            },
            buttons: {
                '<?php echo ACTION_CLOSE; ?>': function() {
                    $(this).dialog("close");
                    $(".ui-dialog-titlebar-close").show();
                }
            }
        });
    }
    
    function alertSelectBranchProduct(){
        $(".btnSavePro").removeAttr('disabled');
        $("#dialog").html('<p style="color:red; font-size:14px;"><?php echo MESSAGE_CONFIRM_SELECT_BRANCH; ?></p>');
        $("#dialog").dialog({
            title: '<?php echo DIALOG_INFORMATION; ?>',
            resizable: false,
            modal: true,
            closeOnEscape: false,
            width: 'auto',
            height: 'auto',
            position:'center',
            open: function(event, ui){
                $(".ui-dialog-buttonpane").show();
                $(".ui-dialog-titlebar-close").hide();
            },
            buttons: {
                '<?php echo ACTION_CLOSE; ?>': function() {
                    $(this).dialog("close");
                    $(".ui-dialog-titlebar-close").show();
                }
            }
        });
    }
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="#" class="positive btnBackProduct">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="float:right;">
         <?php
        if($allowDelete){
        ?>
        <div class="buttons" style="float: right;">
            <a href="#" class="positive btnDeleteProduct">
                <img src="<?php echo $this->webroot; ?>img/button/delete.png" alt=""/>
                <?php echo ACTION_DELETE; ?>
            </a>
        </div>
        <?php
        }
        if($allowSetPrice){
        ?>
        <div class="buttons" style="float: right;">
            <a href="#" class="positive btnSetPriceProduct">
                <img src="<?php echo $this->webroot; ?>img/button/salary.png" alt=""/>
                <?php echo ACTION_SET_PRICE; ?>
            </a>
        </div>
        <?php
        }
        ?>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<div class="divMainPhoto" style="width: 50%; float: left;">
    <form id="<?php echo $frmNameMain; ?>" action="<?php echo $this->base; ?>/products/upload" method="post" enctype="multipart/form-data">
        <fieldset>
            <legend><span class="hideMainPhoto" style="cursor: pointer;"><?php __(TABLE_HIDE_MAIN_PHOTO); ?></span> &nbsp; <span class="showPhotoOther" style="border-left: 1px solid #000;">&nbsp;</span> <span class="showPhotoOther" style="padding: 5px; cursor: pointer;"><?php echo TABLE_SHOW_PHOTO_OTHER; ?></span> &nbsp; <span class="showCatalog" style="border-left: 1px solid #000; display: none;">&nbsp;</span> <span class="showCatalog" style="padding: 5px; cursor: pointer; display: none;"><?php echo TABLE_SHOW_CATALOG; ?></span></legend>
            <table>
                <tr>
                    <td colspan="2">
                        <img alt="" id="mainPhotoDisplay" <?php echo $this->data['Product']['photo'] != '' ? 'src="' . $this->webroot . 'public/product_photo/' . $this->data['Product']['photo'] . '"' : ''; ?> style="width: 170px; height: 120px;" />
                    </td>
                    <td valign="top">
                        <input type="file" name="photoMain" id="ProductMainPhoto" />
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
</div>
<div class="divPhotoOther" style="width: 50%; float: left; display: none;">
    <fieldset>
        <legend><?php __(TABLE_SHOW_OTHER_PHOTO); ?>&nbsp; <span style="border-left: 1px solid #000;"></span><span class="hidePhotoOther" style="padding: 5px; cursor: pointer;"><?php echo TABLE_HIDE_OTHER_PHOTO; ?></span></legend>
        <table>
            <?php 
                $indexPhoto = 1;
                $queryProductPhoto = mysql_query("SELECT * FROM product_photos WHERE product_id = '".$this->data['Product']['id']."'");
                if(mysql_num_rows($queryProductPhoto)){
                    while($rowProductPhoto = mysql_fetch_array($queryProductPhoto)){
            ?>
            <tr>
                <td colspan="2">
                    <img class="photoDisplay" alt="" <?php echo $rowProductPhoto['photo'] != '' ? 'src="' . $this->webroot . 'public/product_photo/' . $rowProductPhoto['photo'] . '"' : ''; ?> style="width: 100px; height: 50px;" />
                </td>
                <td>
                    <input type="hidden" class="ProductPhotoMultiData" value="<?php echo $rowProductPhoto['photo']; ?>" />
                </td>
                <td>
                    <img alt="Remove" rel="<?php echo $rowProductPhoto['id']; ?>" src="<?php echo $this->webroot . 'img/button/cross.png'; ?>" class="btnnRemoveOldMultiPhoto" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Remove')" />
                </td>
            </tr>
            <?php   
                    $indexPhoto++;
                    }
                }
            ?>
        </table>
        <form class="<?php echo $frmName; ?>" action="<?php echo $this->base; ?>/products/upload/0" method="post" enctype="multipart/form-data">
            <table id="tblMutiPhoto">
                <tr id="OrderListMutiPhoto" class="tblMutiPhotoList">
                    <td colspan="2">
                        <img id="photoDisplay" class="photoDisplay" alt="" style="width: 100px; height: 50px;" />
                    </td>
                    <td valign="top">
                        <input type="file" id="ProductPhoto" class="ProductPhoto" rel="0" name="photo" />
                    </td>
                    <td>
                        <input type="hidden" id="ProductPhotoMultiData" class="ProductPhotoMultiData" />
                    </td>
                    <td>
                        <img alt="Add New Photo" src="<?php echo $this->webroot . 'img/button/plus.png'; ?>" class="btnAddMultiPhoto" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Add New Photo')" />
                        <img alt="Remove" src="<?php echo $this->webroot . 'img/button/cross.png'; ?>" class="btnnRemoveMultiPhoto" align="absmiddle" style="cursor: pointer;" onmouseover="Tip('Remove')" />
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
</div>
<?php 
echo $this->Form->create('Product', array('inputDefaults' => array('label' => false))); 
echo $this->Form->input('id'); 
echo $this->Form->hidden('sys_code'); 

$indexPhoto = 1;
$queryProductPhoto = mysql_query("SELECT * FROM product_photos WHERE product_id = '".$this->data['Product']['id']."'");
if(mysql_num_rows($queryProductPhoto)){
    while($rowProductPhoto = mysql_fetch_array($queryProductPhoto)){
?>
        <input type="hidden" name="data[old_photo][]" value="<?php echo $rowProductPhoto['photo']; ?>" />
<?php
    }
}
if(count($companies) == 1){
?>
<input type="hidden" value="<?php echo $this->data['Product']['company_id']; ?>" name="data[Product][company_id]" id="ProductCompanyId" />
<?php
}
if(COUNT($branches) == 1){
    $sqlBranch = mysql_query("SELECT product_branches.branch_id FROM product_branches INNER JOIN branches ON branches.id = product_branches.branch_id AND branches.is_active = 1 WHERE product_id = ".$this->data['Product']['id']." LIMIT 1");
    $rowBranch = mysql_fetch_array($sqlBranch);
?>
<input type="hidden" name="data[Product][branch_id][]" value="<?php echo $rowBranch[0]; ?>" />
<?php
}
?>
<input type="hidden" name="data[Product][old_photo]" value="<?php echo $this->data['Product']['photo']; ?>" />
<input type="hidden" id="<?php echo $photoNameHidden; ?>" name="data[Product][new_photo]" />
<div id="loadMultiPhoto"></div>
<table cellpadding="0" cellspacing="0" style="width: 100%;">
    <tr>
        <td style="width: 50%; vertical-align: top;">
            <fieldset>
                <legend><?php __(MENU_PRODUCT_MANAGEMENT_INFO); ?>&nbsp; <span class="showMainPhoto" style="border-left: 1px solid #000; display: none;"></span><span class="showMainPhoto" style="padding: 5px; cursor: pointer; display: none;"><?php echo TABLE_SHOW_MAIN_PHOTO; ?></span></legend>
                <table width="100%" cellpadding="5">
                    <tr>
                        <td><label for="ProductName"><?php echo TABLE_NAME; ?> <span class="red">*</span> :</label></td>
                        <td>
                            <div class="inputContainer" style="width: 100%;">
                                <?php echo $this->Form->text('name', array('class' => 'validate[required]', 'style' => 'width: 80%;')); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="ProductChemical"><?php echo TABLE_CHIMICAL_NAME; ?> <span class="red">*</span> :</label></td>
                        <td>
                            <div class="inputContainer" style="width: 100%;"><?php echo $this->Form->text('chemical', array('class' => 'validate[required]', 'style' => 'width: 80%; height: 25px;')); ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="ProductParentId"><?php echo PRODUCT_PARENT; ?> :</label></td>
                        <td valign="top">
                            <input type="hidden" name="data[Product][parent_id]" id ="ProductParentId" value="<?php echo $this->data['Product']['parent_id'] ?>" />
                            <?php
                            if($this->data['Product']['parent_id'] > 0){
                                $sql = mysql_query("SELECT CONCAT_WS(' - ',code,name) FROM products WHERE id = ".$this->data['Product']['parent_id']);
                                $parentName = mysql_fetch_array($sql);
                                $displaySearch = "display: none;";
                                $displayDelete = "";
                            }else{
                                $parentName = "";
                                $displayDelete = "display: none;";
                                $displaySearch = "";
                            }
                            ?>
                            <input type="text" id="ProductParentName" value="<?php echo $parentName; ?>" style="width: 80%;" />
                            <img alt="Search" align="absmiddle" style="<?php echo $displaySearch; ?> cursor: pointer; width: 22px; height: 22px;" class="searchProductParent" onmouseover="Tip('<?php echo GENERAL_SEARCH; ?>')" src="<?php echo $this->webroot . 'img/button/search.png'; ?>" />
                            <img alt="Delete" align="absmiddle" style="<?php echo $displayDelete; ?> cursor: pointer;" class="deleteProductParent" onmouseover="Tip('<?php echo ACTION_DELETE; ?>')" src="<?php echo $this->webroot . 'img/button/delete.png'; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%;"><label for="ProductPgroupId"><?php echo TABLE_GROUP; ?> <span class="red">*</span>:</label></td>
                        <td>
                            <div class="inputContainer" style="width: 100%;">
                                <?php echo $this->Form->input('pgroup_id', array('selected' => $pgroupsSellected, 'empty' => INPUT_SELECT, 'style' => 'width: 350px;')); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%;"><label for="ProductBrandId"><?php echo TABLE_BRAND; ?> :</label></td>
                        <td>
                            <div class="inputContainer" style="width: 100%;">
                            <?php echo $this->Form->input('brand_id', array('label' => false, 'empty' => INPUT_SELECT)); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="ProductBarcode"><?php echo TABLE_BARCODE; ?> <span class="red">*</span> :</label></td>
                        <td>
                            <div class="inputContainer" style="width: 100%;">
                                <?php echo $this->Form->input('barcode', array('class' => 'validate[required]', 'div' => false, 'style' => 'width: 80%;')); ?>
                                <img src="<?php echo $this->webroot; ?>img/layout/spinner.gif" class="loadSkuUomPro" style="display:none;" />
                                <img src="<?php echo $this->webroot; ?>img/button/delete.png" onmouseover="Tip('UPC have existed!')" class="availableSkuUomPro" style="display:none;" /> 
                                <img src="<?php echo $this->webroot; ?>img/button/tick.png" class="noneAvailableSkuUomPro" />
                            </div>
                            <div class="inputContainer availableSkuUomPro" style="width: 100%; color: red; display: none;">
                                <?php echo MESSAGE_UPC_EXIST_IN_SYSTEM; ?>
                            </div>
                        </td>
                    </tr>
                    <tr style="display: none;">
                        <td><label for="ProductCode"><?php echo TABLE_SKU; ?> <span class="red">*</span> :</label></td>
                        <td>
                            <div class="inputContainer" style="width: 100%;">
                                <?php echo $this->Form->input('code', array('div' => false, 'style' => 'width: 80%;')); ?>
                                <img src="<?php echo $this->webroot; ?>img/layout/spinner.gif" class="loadSkuUomPro" style="display:none;" />
                                <img src="<?php echo $this->webroot; ?>img/button/delete.png" onmouseover="Tip('SKU have existed!')" class="availableSkuUomPro" style="display:none;" /> 
                                <img src="<?php echo $this->webroot; ?>img/button/tick.png" class="noneAvailableSkuUomPro" />
                            </div>
                            <div class="inputContainer availableSkuUomPro" style="width: 100%; color: red; display: none;">
                                <?php echo MESSAGE_SKU_EXIST_IN_SYSTEM; ?>
                            </div>
                        </td>
                    </tr>
                    <?php
                    if($allowSetCost){
                    ?>
                    <tr>
                        <td><label for="ProductDefaultCost"><?php echo TABLE_UNIT_COST; ?> ($) <span class="red">*</span> :</label></td>
                        <td style="height: 30px;">
                            <div class="inputContainer" style="width: 100%;">
                                <?php
                                if ($productUseReady == 0) {
                                    echo $this->Form->text('default_cost', array('class' => 'validate[required] float', 'value' => number_format($this->data['Product']['default_cost'], $rowOption[0]), 'style' => 'width: 80%;'));
                                } else {
                                    echo number_format($this->data['Product']['unit_cost'], $rowOption[0]);
                                }
                                ?>
                            </div>
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                    <tr>
                        <td><label for="ProductPriceUomId"><?php echo TABLE_UOM; ?> <span class="red">*</span> :</label></td>
                        <td style="height: 30px;">
                            <?php
                            if ($productUseReady == 0) {
                            ?>
                                <div class="inputContainer" style="width: 100%;">
                                    <?php echo $this->Form->input('price_uom_id', array('options' => $uoms, 'name' => 'data[Product][price_uom_id]', 'empty' => INPUT_SELECT, 'label' => false, 'div' => false, 'style' => 'width: 200px;', 'id' => 'ProductPriceUomId')); ?>
                                    <img src="<?php echo $this->webroot; ?>img/layout/spinner.gif" id="loadUomPro" style="display:none;" />
                                </div>
                            <?php
                            } else {
                                $sql = mysql_query("SELECT name FROM uoms WHERE id = " . $this->data['Product']['price_uom_id']);
                                $uom = mysql_fetch_array($sql);
                                echo $uom[0];
                                echo '&nbsp;&nbsp;&nbsp;';
                                echo '<a href="" class="btnViewProductHistory" rel="' . $this->data['Product']['id'] . '" name="' . $this->data['Product']['name'] . '"><img alt="View" onmouseover="Tip(\'' . ACTION_VIEW . '\')" src="' . $this->webroot . 'img/button/view.png" /></a> ';
                            
                            ?>
                                <input type="hidden" name="data[Product][price_uom_id]" value="<?php echo $this->data['Product']['price_uom_id']; ?>" id="ProductPriceUomId" />
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" id="dvSkuUomPro">
                            <?php
                            $query = mysql_query("SELECT id,name,abbr,1 AS conversion FROM uoms WHERE id=" . $this->data['Product']['price_uom_id'] . "
                                                    UNION
                                                    SELECT id,name,abbr,(SELECT value FROM uom_conversions WHERE is_active=1 AND from_uom_id=" . $this->data['Product']['price_uom_id'] . " AND to_uom_id=uoms.id) AS conversion FROM uoms WHERE id IN (SELECT to_uom_id FROM uom_conversions WHERE is_active=1 AND from_uom_id=" . $this->data['Product']['price_uom_id'] . ")
                                                    ORDER BY conversion ASC");
                            $index = 1;
                            if (mysql_num_rows($query) > 1) {
                            ?>
                            <fieldset>
                                <legend><?php __(TABLE_CODE . " of " . TABLE_UOM); ?></legend>
                                <table style="width:90%">
                                    <?php
                                    while ($data = mysql_fetch_array($query)) {
                                        if($data['id'] != $this->data['Product']['price_uom_id']){
                                            $value = "";
                                            $id = "";
                                            $s = mysql_query("SELECT id, sku FROM product_with_skus WHERE product_id = " . $this->data['Product']['id'] . " AND uom_id=" . $data['id']);
                                            if (mysql_num_rows($s)) {
                                                $skuUom = mysql_fetch_array($s);
                                                $id = $skuUom[0];
                                                $value = $skuUom[1];
                                            }
                                    ?>
                                        <tr>
                                            <td style="width: 15%;"><label for="skuUomPro_<?php echo $data['id']; ?>"><?php echo TABLE_CODE; ?></label> :</td>
                                            <td>
                                                <input type="hidden" value="<?php echo $data['id']; ?>" name="data[sku_uom][]" />
                                                <input type="text" sku-uom="<?php echo $id; ?>" value="<?php echo $value; ?>" name="data[sku_uom_value][]" id="skuUomPro_<?php echo $data['id']; ?>" class="skuUomPro" style="width: 60%;" /> of <?php echo $data['name']; ?>
                                                <img src="<?php echo $this->webroot; ?>img/layout/spinner.gif" class="loadSkuUomPro" style="display:none;" />
                                                <img src="<?php echo $this->webroot; ?>img/button/delete.png" onmouseover="Tip('SKU of this uom have existed!')" class="availableSkuUomPro" style="display:none;" /> 
                                                <img src="<?php echo $this->webroot; ?>img/button/tick.png" class="noneAvailableSkuUomPro" />
                                            </td>
                                        </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                </table>
                            </fieldset>
                            <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="ProductIsExpiredDate"><?php echo TABLE_HAS_EXPIRED_DATE; ?>:</label></td>
                        <td>
                            <div class="inputContainer">
                                <input type="hidden" value="<?php echo $this->data['Product']['is_packet']; ?>" name="data[Product][is_packet]" id="ProductIsPacket" />
                                <?php
                                if($rowStock[0] == 0){
                                ?>
                                <select name="data[Product][is_expired_date]" id="ProductIsExpiredDate">
                                    <option value="0" <?php if($this->data['Product']['is_expired_date'] == 0){ ?>selected="selected"<?php } ?>><?php echo ACTION_NO; ?></option>
                                    <option value="1" <?php if($this->data['Product']['is_expired_date'] == 1){ ?>selected="selected"<?php } ?>><?php echo ACTION_YES; ?></option>
                                </select>
                                <?php
                                } else {
                                ?>
                                <input type="hidden" value="<?php echo $this->data['Product']['is_expired_date']; ?>" name="data[Product][is_expired_date]" id="ProductIsExpiredDate" />
                                <?php
                                    if($this->data['Product']['is_expired_date'] == 0){
                                        echo ACTION_NO;
                                    } else {
                                        echo ACTION_YES;
                                    }
                                }
                                ?>
                            </div>
                        </td>
                    </tr>
                    <?php 
                    if($rowOption[1] == 1){
                    ?>
                    <tr>
                        <td><label for="ProductIsLots"><?php echo TABLE_TRACK_LOT_SERIES; ?>:</label></td>
                        <td>
                            <div class="inputContainer">
                                <?php
                                if($rowStock[0] == 0){
                                ?>
                                <select name="data[Product][is_lots]" id="ProductIsLots">
                                    <option value="0" <?php if($this->data['Product']['is_lots'] == 0){ ?>selected="selected"<?php } ?>><?php echo ACTION_NO; ?></option>
                                    <option value="1" <?php if($this->data['Product']['is_lots'] == 1){ ?>selected="selected"<?php } ?>><?php echo ACTION_YES; ?></option>
                                </select>
                                <?php
                                } else {
                                    if($this->data['Product']['is_lots'] == 0){
                                        echo ACTION_NO;
                                    } else {
                                        echo ACTION_YES;
                                    }
                                }
                                ?>
                            </div>
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                    <tr>
                        <td><label for="ProductisNotForSale"><?php echo TABLE_ACTIVE_INACTIVE; ?>:</label></td>
                        <td>
                            <select name="data[Product][is_not_for_sale]" id="ProductisNotForSale" style="width: 200px;">
                                <option value="0" <?php if($this->data['Product']['is_not_for_sale'] ==0){ ?>selected="selected"<?php } ?>><?php echo TABLE_ACTIVE; ?></option>
                                <option value="1" <?php if($this->data['Product']['is_not_for_sale'] ==1){ ?>selected="selected"<?php } ?>><?php echo TABLE_INACTIVE; ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="ProductReorderLevel"><?php echo TABLE_PRODUCT . " " . GENERAL_REORDER_LEVEL; ?>:</label></td>
                        <td>
                            <div class="inputContainer" style="width: 100%;">
                                <?php echo $this->Form->input('reorder_level', array('class' => 'interger', 'style' => 'width: 190px;')); ?>
                            </div>
                        </td>
                    </tr>
                    <tr style="display: none;">
                        <td style="vertical-align: top;"><label for="ProductSpec"><?php echo TABLE_SPEC; ?> :</label></td>
                        <td><?php echo $this->Form->textarea('spec'); ?></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;"><label for="ProductDescription"><?php echo GENERAL_DESCRIPTION; ?> :</label></td>
                        <td style="vertical-align: top;"><?php echo $this->Form->textarea('description'); ?></td>
                    </tr>
                </table>
            </fieldset>
        </td>
        <td style="width: 50%; vertical-align: top;">
            <fieldset>
                <legend><?php echo TABLE_SIZE . AND_ . TABLE_WEIGHT ?></legend>
                <div>
                    <table width="90%">
                        <tr>
                            <td><label for="ProductWidth"><?php echo TABLE_WIDTH; ?>:</label></td>
                            <td><label for="ProductHeight"><?php echo TABLE_HEIGHT; ?>:</label></td>
                            <td><label for="ProductLength"><?php echo TABLE_LENGTH; ?>:</label></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->Form->input('width', array('style' => 'width: 150px', 'class' => 'interger')); ?></td>
                            <td><?php echo $this->Form->input('height', array('style' => 'width: 150px', 'class' => 'interger')); ?></td>
                            <td><?php echo $this->Form->input('length', array('empty' => INPUT_SELECT, 'label' => false, 'style' => 'width: 150px', 'class' => 'interger')); ?></td>
                        </tr>
                        <tr>
                            <td><label for="ProductSizeUomId"><?php echo TABLE_UOM; ?>:</label></td>
                            <td><label for="ProductCubicMeter"><?php echo TABLE_METER_THREE; ?>:</label></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->Form->input('uom_id', array('name' => 'data[Product][size_uom_id]', 'id' => 'ProductSizeUomId', 'empty' => INPUT_SELECT, 'label' => false, 'style' => 'width: 160px')); ?></td>
                            <td><?php echo $this->Form->input('cubic_meter', array('style' => 'width: 150px', 'class' => 'interger')); ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><label for="ProductWeight"><?php echo TABLE_WEIGHT; ?>:</label></td>
                            <td><label for="ProductWeightUomId"><?php echo TABLE_UOM; ?>:</label></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->Form->input('weight', array('style' => 'width: 150px', 'empty' => INPUT_SELECT, 'label' => false, 'class' => 'float')); ?></td>
                            <td><?php echo $this->Form->input('uom_id', array('name' => 'data[Product][weight_uom_id]', 'id' => 'ProductWeightUomId', 'empty' => INPUT_SELECT, 'label' => false, 'style' => 'width: 160px')); ?></td>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </fieldset>
            <?php 
            if(count($branches) > 1){
            ?>
            <fieldset style="display: none;">
                <legend><?php __(TABLE_APPLY_WITH_BRANCH); ?></legend>
                <table>
                    <tr>
                        <th><label for="productBranch">Available:</label></th>
                        <th></th>
                        <th><label for="productBranchSelected">Member of:</label></th>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <select id="productBranch" multiple="multiple" style="width: 280px; height: 200px;">
                                <?php
                                $querySource=mysql_query("SELECT id,name FROM branches WHERE is_active=1 AND id NOT IN (SELECT branch_id FROM product_branches WHERE product_id = ".$this->data['Product']['id'].") AND company_id =".$this->data['Product']['company_id']);
                                while($dataSource=mysql_fetch_array($querySource)){
                                ?>
                                <option value="<?php echo $dataSource['id']; ?>"><?php echo $dataSource['name']; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td style="vertical-align: middle;">
                            <img alt="" src="<?php echo $this->webroot; ?>img/button/right.png" style="cursor: pointer;" onclick="listbox_moveacross('productBranch', 'productBranchSelected')" />
                            <br /><br />
                            <img alt="" src="<?php echo $this->webroot; ?>img/button/left.png" style="cursor: pointer;" onclick="listbox_moveacross('productBranchSelected', 'productBranch')" />
                        </td>
                        <td style="vertical-align: top;">
                            <select id="productBranchSelected" name="data[Product][branch_id][]" multiple="multiple" style="width: 280px; height: 200px;">
                                <?php
                                $queryBranch=mysql_query("SELECT DISTINCT branch_id,(SELECT name FROM branches WHERE id=product_branches.branch_id) AS name FROM product_branches WHERE branch_id NOT IN (SELECT id FROM branches WHERE is_active!=1) AND product_id=".$this->data['Product']['id']);
                                while($dataBranch=mysql_fetch_array($queryBranch)){
                                ?>
                                <option value="<?php echo $dataBranch['branch_id']; ?>"><?php echo $dataBranch['name']; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <?php
            }
            ?>
        </td>
    </tr>
</table>
<br />
<div class="buttons">
    <button type="submit" class="positive btnSavePro">
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <span class="txtSaveProduct"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<div style="clear: both;"></div>
<?php echo $this->Form->end(); ?>
<div id="<?php echo $dialogPhoto; ?>" style="display: none;">
    <img id="<?php echo $cropPhoto; ?>" alt="" />
</div>