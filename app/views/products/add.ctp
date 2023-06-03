<?php
// Get Decimal
$sqlOption = mysql_query("SELECT product_cost_decimal, uom_detail_option FROM setting_options");
$rowOption = mysql_fetch_array($sqlOption);

include("includes/function.php");
// Authentication
$this->element('check_access');
$allowSetCost   = checkAccess($user['User']['id'], $this->params['controller'], 'setCost');
$allowAddPgroup = checkAccess($user['User']['id'], $this->params['controller'], 'addPgroup');
$allowAddUoM    = checkAccess($user['User']['id'], $this->params['controller'], 'addUom');
$allowAddBrand  = checkAccess($user['User']['id'], $this->params['controller'], 'addBrand');
$frmName      = "frm" . rand();
$frmNameMain  = "frmMain" . rand();
$dialogPhoto  = "dialogPhoto" . rand();
$cropPhoto    = "cropPhoto" . rand();
$photoNameHidden = "photoNameHidden" . rand();
echo $this->element('prevent_multiple_submit'); ?>
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
        $("#ProductUnitCost").autoNumeric({mDec: <?php echo $rowOption[0]; ?>, aSep: ','});
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
        if($allowAddUoM){
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
        } else {
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
        
        $("#ProductAddForm").validationEngine('attach', {
            isOverflown: true,
            overflownDIV: ".ui-tabs-panel"
        });
        // Form Action Save
        $("#ProductAddForm").ajaxForm({
            beforeSerialize: function($form, options) {
                if($("#ProductPgroupId").val() == null || $("#ProductPgroupId").val() == ""){
                    alertSelectGroupProduct();
                    return false;
                }
                if($("#ProductPriceUomId").val() == null || $("#ProductPriceUomId").val() == ""){
                    alertSelectUoMProduct();
                    return false;
                }
                <?php 
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
                $(".float, .interger, #ProductUnitCost").each(function(){
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
                    createSysAct('Products', 'Add', 2, result);
                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                }else {
                    createSysAct('Products', 'Add', 1, '');
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
        $("#ProductBarcode").blur(function(){
            var companyId     = $("#ProductCompanyId").val();
            var puc           = $(this);
            var imgLoad       = $(this).closest("tr").find(".loadSkuUomPro");
            var available     = $(this).closest("tr").find(".availableSkuUomPro");
            var noneAvailable = $(this).closest("tr").find(".noneAvailableSkuUomPro");
            if(companyId != ''){
                if(puc.val() != ''){
                    $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/checkPuc/"+companyId+"/"+puc.val(),
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
                        url: "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/checkSkuUom/"+companyId+"/"+sku.val(),
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
        
        // Sub Product Of
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
        
        // Check Clone
        <?php
        if(!empty($cloneId)){
        ?>
            cloneProductInfo('<?php echo $cloneId; ?>');
        <?php
        }
        ?>
        resetFormProduct();
        onBlurSkuUomPro();
        changeInputCSSProduct();
        eventKeyMultiPhoto(0);
        <?php 
        if(count($companies) == 1 && COUNT($branches) > 1){
        ?>
        getBranchProduct();
        <?php
        }
        ?>
    });
    <?php 
    if(count($companies) == 1 && COUNT($branches) > 1){
    ?>
    function getBranchProduct(){
        var companyId = $("#ProductCompanyId").val();
        if(companyId != ''){
            $.ajax({
                type: "GET",
                url: "<?php echo $this->base . '/users'; ?>/getBranchByCompany/" + companyId,
                data: "",
                beforeSend: function(){
                    listbox_selectall('productBranchSelected', true);
                    listbox_moveacross('productBranchSelected', 'productBranch');
                    $("#productBranch").html('');
                    $(".loader").attr("src","<?php echo $this->webroot; ?>img/layout/spinner.gif");
                },
                success: function(opt){
                    $(".loader").attr("src","<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                    $("#productBranch").append(opt);
                    // Auto Select Branch
                    listbox_selectall('productBranch', true);
                    listbox_moveacross('productBranch', 'productBranchSelected');
                }
            });
        }
    }
    <?php
    }
    ?>
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
           dataMultiPhoto += "<input type='hidden' name='data[photo][]' value='"+$(this).val()+"' />";
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
        // SELECT
        $("#productIsClone").val(0);
        <?php
        if(!$allowSetCost){
        ?>
        $("#ProductUnitCost").val(0);
        <?php
        }
        ?>
        $("#ProductIsPacket").val(0);
        $("#ProductIsExpiredDate").find("option[value='0']").attr("selected", true);
        $("#ProductPriceUomId").find("option[value='']").attr("selected", true);
        $("#ProductisNotForSale").find("option[value='0']").attr("selected", true);
        $("#ProductWeightUomId").find("option[value='']").attr("selected", true);
        // Production Show
        $("#dvProductPacket").hide();
        $(".divNonPacket").show();
        // Textarea
        $("#ProductAddForm").find("textarea").val('');
        // INPUT FILE
        $("#divProductPhoto").html(divPhotoUpload);
        // UOM DETAIL
        $("#dvSkuUomPro").html('');
        // SYMBOL CHECK CODE
        $(".availableSkuUomPro").hide();
        $(".noneAvailableSkuUomPro").hide();
        // PRODUCT GROUP
        $("#ProductPgroupId_chzn").find(".chzn-choices").find(".search-choice-close").click();
    }
    
    function onBlurSkuUomPro(){
        $(".skuUomPro").unbind("blur").unbind("keyup").unbind("keypress");
        $(".skuUomPro").keypress(function(e){
            if((e.which && e.which == 13) || e.keyCode == 13){                                    
                return false;
            }
        });
        $(".skuUomPro").blur(function(){
            var id            = $(this).attr('id');
            var companyId     = $("#ProductCompanyId").val();
            var sku           = $(this);
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
                    url: "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/checkSkuUom/"+companyId+"/"+sku.val(),
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
            }else if(sku.val() != ''){
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
        $("#ProductAddForm").find("label").removeAttr("class");
        $("#ProductAddForm").find("label").each(function(){
            var label = $(this).attr("for");
            if(label != 'ProductCompanyId'){
                $(this).addClass(cssStyle);
            }
        });
        $("label[for='ProductPhoto']").removeAttr("class");
        $("label[for='ProductPhoto']").addClass(cssStyle);
        // Input & Select
        $("#ProductAddForm").find("input").each(function(){
            $(this).removeClass(cssRemove);
            $(this).addClass(cssStyle);
        });
        $("#ProductPhoto").removeClass(cssRemove);
        $("#ProductPhoto").addClass(cssStyle);
        $("#ProductPhoto").attr("disabled", disabled);
        $("#ProductAddForm").find("select").each(function(){
            var selectId = $(this).attr("id");
            if(selectId != 'ProductCompanyId'){
                $(this).removeClass(cssRemove);
                $(this).addClass(cssStyle);
                $(this).attr("disabled", disabled);
            }
        });
        // Read Only
        $("#ProductAddForm").find("input").attr("readonly", disabled);
        $("#ProductAddForm").find("textarea").attr("readonly", disabled);
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
    
    function cloneProductInfo(id){
        $.ajax({
            dataType: 'json',
            type:   "POST",
            url:    "<?php echo $this->base . '/' . $this->params['controller']; ?>/cloneProductInfo/"+id,
            beforeSend: function(){
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(clone){
                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                if(clone.error == 0){
                    $("#ProductIsExpiredDate").val(clone.Product.is_expired_date);
                    $("#ProductName").val(clone.Product.name);
                    $("#ProductColor").val(clone.Product.color);
                    $("#ProductPriceUomId").val(clone.Product.price_uom_id);
                    $("#ProductisNotForSale").val(clone.Product.is_not_for_sale);
                    $("#ProductPeriodFrom").val(clone.Product.period_from);
                    $("#ProductPeriodTo").val(clone.Product.period_to);
                    $("#ProductReorderLevel").val(clone.Product.reorder_level);
                    $("#ProductSpec").val(clone.Product.spec);
                    $("#ProductDescription").val(clone.Product.description);
                    $("#ProductWidth").val(clone.Product.width);
                    $("#ProductHeight").val(clone.Product.height);
                    $("#ProductLength").val(clone.Product.length);
                    $("#ProductSizeUomId").val(clone.Product.size_uom_id);
                    $("#ProductCubicMeter").val(clone.Product.cubic_meter);
                    $("#ProductWeight").val(clone.Product.weight);
                    $("#ProductWeightUomId").val(clone.Product.weight_uom_id);
                    $("#ProductPriceUomId").change();
                    // ICS
                    $("#t1").val(clone.Product.ics_inv);
                    $("#t2").val(clone.Product.ics_cogs);
                    $("#t8").val(clone.Product.ics_sales);
                    // Photo
                    if(clone.Product.photo != ''){
                        $("#productIsClone").val(1);
                        $(".fsMainPhoto").removeAttr("style");
                        $("#mainPhotoDisplay").attr("src", "<?php echo $this->webroot; ?>" + "public/product_photo/" + clone.Product.photo);
                        $("#<?php echo $photoNameHidden; ?>").val(clone.Product.photo);
                    }
                }
            }
        });
    }
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="" class="positive btnBackProduct">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
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
                        <img alt="" id="mainPhotoDisplay" style="width: 170px; height: 120px;" />
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
    <form class="<?php echo $frmName; ?>" action="<?php echo $this->base; ?>/products/upload/0" method="post" enctype="multipart/form-data">
        <fieldset>
            <legend><?php __(TABLE_SHOW_OTHER_PHOTO); ?>&nbsp; <span style="border-left: 1px solid #000;"></span><span class="hidePhotoOther" style="padding: 5px; cursor: pointer;"><?php echo TABLE_HIDE_OTHER_PHOTO; ?></span></legend>
            <table id="tblMutiPhoto">
                <tr id="OrderListMutiPhoto" class="tblMutiPhotoList">
                    <td colspan="2">
                        <img id="photoDisplay" class="photoDisplay" alt="" style="width: 100px; height: 50px;" />
                    </td>
                    <td valign="top">
                        <input type="file" id="ProductPhoto" class="ProductPhoto" rel="0" name="photo" value="" />
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
        </fieldset>
    </form>
</div>
<?php echo $this->Form->create('Product', array('inputDefaults' => array('label' => false))); ?>
<input type="hidden" id="<?php echo $photoNameHidden; ?>" name="data[Product][photo]" />
<div id="loadMultiPhoto"></div>
<?php
if(!$allowSetCost){
?>
<input type="hidden" value="0" name="data[Product][unit_cost]" id="ProductUnitCost" />
<?php
}
if(count($companies) == 1){
    $companyId = key($companies);
?>
<input type="hidden" value="<?php echo $companyId; ?>" name="data[Product][company_id]" id="ProductCompanyId" />
<?php
}
if(COUNT($branches) == 1){
    $branchId = key($branches);
?>
<input type="hidden" name="data[Product][branch_id][]" value="<?php echo $branchId; ?>" />
<?php
}
?>
<table cellpadding="0" cellspacing="0" style="width: 100%;">
    <tr>
        <td style="width: 50%; vertical-align: top;">
            <fieldset>
                <legend><?php __(MENU_PRODUCT_MANAGEMENT_INFO); ?>&nbsp; <span class="showMainPhoto" style="border-left: 1px solid #000; display: none;"></span><span class="showMainPhoto" style="padding: 5px; cursor: pointer; display: none;"><?php echo TABLE_SHOW_MAIN_PHOTO; ?></span></legend>
                <table width="100%" cellpadding="5">
                    <tr>
                        <td><label for="ProductName"><?php echo TABLE_NAME; ?> <span class="red">*</span> :</label></td>
                        <td>
                            <div class="inputContainer" style="width: 100%;"><?php echo $this->Form->text('name', array('class' => 'validate[required]', 'style' => 'width: 80%; height: 25px;')); ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="ProductChemical"><?php echo TABLE_CHIMICAL_NAME; ?> <span class="red">*</span> :</label></td>
                        <td>
                            <div class="inputContainer" style="width: 100%;"><?php echo $this->Form->text('chemical', array('class' => 'validate[required]', 'style' => 'width: 80%; height: 25px;')); ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="ProductParentName"><?php echo PRODUCT_PARENT; ?>:</label></td>
                        <td>
                            <input type="hidden" name="data[Product][parent_id]" id ="ProductParentId" />
                            <input type="text" id="ProductParentName" style="width: 80%;" />
                            <img alt="Search" align="absmiddle" style="cursor: pointer; width: 22px; height: 22px;" class="searchProductParent" onmouseover="Tip('<?php echo GENERAL_SEARCH; ?>')" src="<?php echo $this->webroot . 'img/button/search.png'; ?>" />
                            <img alt="Delete" align="absmiddle" style="display: none; cursor: pointer;" class="deleteProductParent" onmouseover="Tip('<?php echo ACTION_DELETE; ?>')" src="<?php echo $this->webroot . 'img/button/delete.png'; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%;"><label for="ProductPgroupId"><?php echo TABLE_GROUP; ?> <span class="red">*</span>:</label></td>
                        <td>
                            <div class="inputContainer" style="width: 100%;">
                            <?php echo $this->Form->input('pgroup_id', array('label' => false, 'empty' => INPUT_SELECT)); ?>
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
                                <img src="<?php echo $this->webroot; ?>img/button/delete.png" onmouseover="Tip('<?php echo MESSAGE_UPC_EXIST_IN_SYSTEM; ?>')" class="availableSkuUomPro" style="display:none;" /> 
                                <img src="<?php echo $this->webroot; ?>img/button/tick.png" class="noneAvailableSkuUomPro" style="display:none;" />
                            </div>
                            <div class="inputContainer availableSkuUomPro" style="width: 100%; color: red; display: none;">
                                <?php echo MESSAGE_UPC_EXIST_IN_SYSTEM; ?>
                            </div>
                        </td>
                    </tr>
                    <tr style="display: none;">
                        <td><label for="ProductCode"><?php echo TABLE_SKU; ?> :</label></td>
                        <td>
                            <div class="inputContainer" style="width: 100%;">
                                <?php echo $this->Form->input('code', array('div' => false, 'style' => 'width: 80%;')); ?>
                                <img src="<?php echo $this->webroot; ?>img/layout/spinner.gif" class="loadSkuUomPro" style="display:none;" />
                                <img src="<?php echo $this->webroot; ?>img/button/delete.png" onmouseover="Tip('<?php echo MESSAGE_SKU_EXIST_IN_SYSTEM; ?>')" class="availableSkuUomPro" style="display:none;" /> 
                                <img src="<?php echo $this->webroot; ?>img/button/tick.png" class="noneAvailableSkuUomPro" style="display:none;" />
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
                        <td><label for="ProductUnitCost"><?php echo TABLE_UNIT_COST; ?> ($) <span class="red">*</span> :</label></td>
                        <td>
                            <div class="inputContainer" style="width: 100%;">
                            <?php echo $this->Form->text('unit_cost', array('class' => 'validate[required]', 'style' => 'width: 80%;')); ?>
                            </div>
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                    <tr>
                        <td><label for="ProductPriceUomId"><?php echo TABLE_UOM; ?> <span class="red">*</span> :</label></td>
                        <td>
                            <div class="inputContainer" style="width: 100%;">
                                <?php echo $this->Form->input('price_uom_id', array('options' => $uoms, 'name' => 'data[Product][price_uom_id]', 'empty' => INPUT_SELECT, 'label' => false, 'class' => 'validate[required]', 'div' => false)); ?>
                                <img src="<?php echo $this->webroot; ?>img/layout/spinner.gif" id="loadUomPro" style="display:none;" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" id="dvSkuUomPro">

                        </td>
                    </tr>
                    <tr>
                        <td><label for="ProductIsExpiredDate"><?php echo TABLE_HAS_EXPIRED_DATE; ?>:</label></td>
                        <td>
                            <div class="inputContainer" style="width: 100%;">
                                <select name="data[Product][is_expired_date]" id="ProductIsExpiredDate">
                                    <option value="0"><?php echo ACTION_NO; ?></option>
                                    <option value="1"><?php echo ACTION_YES; ?></option>
                                </select>
                                <input type="hidden" value="0" name="data[Product][is_packet]" id="ProductIsPacket" />
                                <div style="clear: both;"></div>
                            </div>
                        </td>
                    </tr>
                    <?php 
                    if($rowOption[1] == 1){
                    ?>
                    <tr>
                        <td><label for="ProductIsLots"><?php echo TABLE_TRACK_LOT_SERIES; ?>:</label></td>
                        <td>
                            <div class="inputContainer" style="width: 100%;">
                                <select name="data[Product][is_lots]" id="ProductIsLots">
                                    <option value="0"><?php echo ACTION_NO; ?></option>
                                    <option value="1"><?php echo ACTION_YES; ?></option>
                                </select>
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
                                <option value="0"><?php echo TABLE_ACTIVE; ?></option>
                                <option value="1"><?php echo TABLE_INACTIVE; ?></option>
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
                        <td><?php echo $this->Form->textarea('description'); ?></td>
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
                            <select id="productBranch" multiple="multiple" style="width: 280px; height: 200px;"></select>
                        </td>
                        <td style="vertical-align: middle;">
                            <img alt="" src="<?php echo $this->webroot; ?>img/button/right.png" style="cursor: pointer;" onclick="listbox_moveacross('productBranch', 'productBranchSelected')" />
                            <br /><br />
                            <img alt="" src="<?php echo $this->webroot; ?>img/button/left.png" style="cursor: pointer;" onclick="listbox_moveacross('productBranchSelected', 'productBranch')" />
                        </td>
                        <td style="vertical-align: top;">
                            <select id="productBranchSelected" name="data[Product][branch_id][]" multiple="multiple" style="width: 280px; height: 200px;"></select>
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