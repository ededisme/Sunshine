<?php 
$this->element('check_access');
include("includes/function.php");
// Get Introduction Dashboard
$varIntroduce  = 'product'.rand();
$funcIntroName = 'productIntro'.rand();
$funcIntroduce = getIntroduction($this->params['controller'], 'Dashboard', $user['User']['id'], $varIntroduce, $funcIntroName);
// Authentication
$allowAdd      = checkAccess($user['User']['id'], $this->params['controller'], 'add');
$allowViewCost = checkAccess($user['User']['id'], $this->params['controller'], 'viewCost');
$allowExport   = checkAccess($user['User']['id'], $this->params['controller'], 'exportExcel');
$allowPrintAllBarcode   = checkAccess($user['User']['id'], $this->params['controller'], 'printBarcode');
$allowPrintAllProduct   = checkAccess($user['User']['id'], $this->params['controller'], 'printProduct');
$allowPrintByChecked    = checkAccess($user['User']['id'], $this->params['controller'], 'printProductByCheck');
$tblName = "tbl" . rand(); 

$isSetPriceType = 0;
$queryPOSpriceType = mysql_query("SELECT price_type_id FROM pos_price_types WHERE is_active = 1");
if(mysql_num_rows($queryPOSpriceType)){
    $isSetPriceType = 1;
}
?>
<style>
    .text_colsan{
        display: table-cell;
    }
</style>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<script type="text/javascript">
    var oTableProductDashBoard;
    var data = "all";
    var tabProductId  = $(".ui-tabs-selected a").attr("href");
    function calcDataTableHeight() {
        var tableHeight = $(window).height() - ($(".ui-layout-north").height() + $(".ui-layout-south").height() + $(".ui-tabs-nav").height() + $("#divHeader").height() + 37 + 22 + 56 + 110.3);
        return tableHeight;
    }
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        var waitForFinalEventProView = (function () {
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
        
        $(window).resize(function(){
            var tabSelected = $(".ui-tabs-selected a").attr("href");
            if(tabSelected == tabProductId){
                waitForFinalEventProView(function(){
                getScrollTable(); 
              }, 500, "Finish");
            }
        });
        
        $("#<?php echo $tblName; ?> td:first-child").addClass('first');
        if($.cookie('productShowStockView') != null && $.cookie('productShowStockView') == 'stock') {
            data = "stock";
            $("#productShowStockView").attr("checked", true);
        }else{
            data = "all";
            $("#productShowStockView").attr("checked", false);
        }        
        // Check Cookie Display Product
        if($.cookie('displayProduct') != null){
            $("#displayProduct").val($.cookie('displayProduct'));
        }        
        oTableProductDashBoard = $("#<?php echo $tblName; ?>").dataTable({
            "sScrollY": calcDataTableHeight(),
            "aaSorting": [[0, 'DESC']],
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo $this->base.'/'.$this->params['controller']; ?>/ajax/"+$("#showProductBranch").val()+"/"+$("#changeCategoryProductView").val()+"/"+$("#displayProduct").val()+"/"+$("#showProductPriceType").val()+"/"+$("#showProductQty").val(),
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                // Create ID for Action
                setIdActionProduct();
                var tableBody   = $(".dataTables_scrollBody .table").attr("style");
                $(".dataTables_scrollHeadInner .table").removeAttr('style');
                $(".dataTables_scrollBody .table").removeAttr('style');
                $(".dataTables_scrollHeadInner .table").attr("style", "width: 90%; padding:0px; margin-top:5px;");
                $(".dataTables_scrollBody .table").attr("style", tableBody+" padding:0px; margin:0px;");
                $("#<?php echo $tblName; ?> td:first-child").addClass('first');                
                $("#<?php echo $tblName; ?> td:nth-child(9)").css("white-space", "nowrap");
                $("#<?php echo $tblName; ?> td:last-child").css("white-space", "nowrap");
                $("#<?php echo $tblName; ?> td:nth-child(2)").css("width", "18%");
                $("#<?php echo $tblName; ?> tr").click(function(){
                    changeBackgroupProduct();
                    $(this).closest("tr").css('background','#eeeca9');
                });
                // Double Click View
                $("#<?php echo $tblName; ?> tr").dblclick(function(){
                    var id = $(this).find(".btnViewProductView").attr('rel');
                    var leftPanel = $(this).parent().parent().parent().parent().parent().parent().parent();
                    var rightPanel=leftPanel.parent().find(".rightPanel");
                    leftPanel.hide("slide", { direction: "left" }, 500, function() {
                        rightPanel.show();
                    });
                    rightPanel.html("<?php echo ACTION_LOADING; ?>");
                    rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/view/" + id);
                });
                $(".btnViewProductView").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var leftPanel=$(this).parent().parent().parent().parent().parent().parent().parent().parent().parent();
                    var rightPanel=leftPanel.parent().find(".rightPanel");
                    leftPanel.hide("slide", { direction: "left" }, 500, function() {
                        rightPanel.show();
                    });
                    rightPanel.html("<?php echo ACTION_LOADING; ?>");
                    rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/view/" + id);
                });
                $(".btnCloneProductView").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var leftPanel=$(this).parent().parent().parent().parent().parent().parent().parent().parent().parent();
                    var rightPanel=leftPanel.parent().find(".rightPanel");
                    leftPanel.hide("slide", { direction: "left" }, 500, function() {
                        rightPanel.show();
                    });
                    rightPanel.html("<?php echo ACTION_LOADING; ?>");
                    rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/add/" + id);
                });
                $(".btnEditProductView").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var url = "edit";
                    var leftPanel  = $(this).parent().parent().parent().parent().parent().parent().parent().parent().parent();
                    var rightPanel = leftPanel.parent().find(".rightPanel");
                    leftPanel.hide("slide", { direction: "left" }, 500, function() {
                        rightPanel.show();
                    });
                    rightPanel.html("<?php echo ACTION_LOADING; ?>");
                    rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/"+url+"/" + id);
                });
                $(".setProductPrice").click(function(){
                    var id = $(this).attr('data');
                    $.ajax({
                        type:   "POST",
                        url:    "<?php echo $this->base . "/products/productPrice/"; ?>"+id,
                        beforeSend: function(){
                            $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                        },
                        success: function(msg){
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
                                                    oCache.iCacheLower = -1;
                                                    oTableProductDashBoard.fnDraw(false);
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
                
                $(".btnDeleteProductView").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var name = $(this).attr('name');
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
                                    url: "<?php echo $this->base.'/'.$this->params['controller']; ?>/delete/" + id,
                                    data: "",
                                    beforeSend: function(){
                                        $("#dialog").dialog("close");
                                        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                                    },
                                    success: function(result){
                                        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                        oCache.iCacheLower = -1;
                                        oTableProductDashBoard.fnDraw(false);
                                        if(result != '<?php echo MESSAGE_DATA_HAVE_CHILD; ?>' && result != '<?php echo MESSAGE_DATA_HAS_BEEN_DELETED; ?>' && result != '<?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?>'){
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
                $(".btnCheckProduct").change(function(event){
                    event.preventDefault();
                    var id     = $(this).attr('rel');
                    var check  = $(this).is(":checked");
                    $.ajax({
                        type:   "POST",
                        url:    "<?php echo $this->base . "/products/printProductByCheck/"; ?>"+id+"/"+((check == true)?0:1),
                        beforeSend: function(){
                            $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                        },
                        success: function(msg){
                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                        }
                    });
                });
                $(".btnPrintProduct").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var url = "<?php echo $this->base . '/' . "products"; ?>/printProductByOne/";                    
                    $.ajax({
                        type: "POST",
                        url:    "<?php echo $this->base . "/products/printByUomBarcode/"; ?>"+id,
                        beforeSend: function(){
                            $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                        },
                        success: function(proUomInfo){
                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                            $("#dialog").html('<table><tr><td style="border: 1px solid #aaaaaa; padding: 5px; margin: 5px;"><select id="productBarcodeWithPriceTypeByPro" style="width: 174px; height: 25px;"><option value=""><?php echo TABLE_SELECT_PRICE_TYPE; ?></option><?php $sqlPriceType = mysql_query("SELECT id, name FROM price_types WHERE id != 1 AND id IN (SELECT price_type_id FROM price_type_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id'].")) GROUP BY id"); while($rowPriceType = mysql_fetch_array($sqlPriceType)){ ?> <option value="<?php echo $rowPriceType['id']; ?>"><?php echo $rowPriceType['name']; ?></option><?php } ?> </select><div class="buttons" style="padding-top: 2px;"> <button type="submit" class="positive reprintProductBarcode" > <img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/> <span class="txtReprintProductBarcode"><?php echo ACTION_PRINT_MAIN_UOM_BARCODE; ?></span></button></div></td><td style="border: 1px solid #aaaaaa; padding: 5px; margin: 5px;"><table><tr><td><div>'+proUomInfo+'</div></td></tr><tr><td><div class="buttons"><button type="submit" class="positive reprintProductUomSmallBarcode" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="txtReprintProductUomSmallBarcode"><?php echo ACTION_PRINT_SMALL_UOM_BARCODE; ?></span></button></div></td></tr></table></td></tr></table>');                 
                            $(".reprintProductBarcode").click(function(){
                                var priceTypeId = $("#productBarcodeWithPriceTypeByPro").val();
                                if(priceTypeId != ""){
                                    $.ajax({
                                        type: "POST",
                                        url: url+id+"/0"+"/"+priceTypeId,
                                        beforeSend: function(){
                                            $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                                        },
                                        success: function(printInvoiceResult){
                                            w = window.open();
                                            w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                                            w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                                            w.document.write(printInvoiceResult);
                                            w.document.close();
                                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                                        }
                                    });
                                }else{
                                    setPriceType();
                                }
                            });
                            $(".reprintProductUomSmallBarcode").click(function(){
                                var skuProUomCon = $("#proUomSku option:selected").attr('sku-name');
                                var priceTypeId = $("#productBarcodeWithPriceTypeByPro").val();
                                if(priceTypeId != ""){
                                    $.ajax({
                                        type: "POST",
                                        url: url+id+"/1"+"/"+priceTypeId,
                                        data: "barcode="+skuProUomCon,
                                        beforeSend: function(){
                                            $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                                        },
                                        success: function(printInvoiceResult){
                                            w = window.open();
                                            w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                                            w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                                            w.document.write(printInvoiceResult);
                                            w.document.close();
                                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                                        }
                                    });
                                }else{
                                    setPriceType();
                                }                                
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
                                buttons: {
                                    '<?php echo ACTION_CLOSE; ?>': function() {
                                        $(this).dialog("close");
                                    }
                                }
                            });
                        }
                    });                              
                });
                $(".viewInventoryProduct").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('data');
                    var code = $(this).closest("tr").find("td:eq(3)").text();
                    var name = $(this).closest("tr").find("td:eq(1)").text();
                    if(id != ''){
                        $.ajax({
                            type: "GET",
                            url: "<?php echo $this->base.'/'.$this->params['controller']; ?>/viewProductInventory/" + id,
                            data: "",
                            beforeSend: function(){
                                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                            },
                            success: function(result){
                                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                // alert message
                                $("#dialog").html(result);
                                $("#dialog").dialog({
                                    title: code+" - "+name,
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
                    }
                });
                // Start Introduce
                <?php echo $funcIntroName.'();'; ?>
                return sPre;
            },
            "fnDrawCallback": function(oSettings, json) {
                $("#<?php echo $tblName; ?> .colspanParent").parent().attr("colspan", 11);
                $("#<?php echo $tblName; ?> .colspanParentHidden").parent().css("display", "none");
            },
            "aoColumnDefs": [{
                "sType": "numeric", "aTargets": [ 0 ],
                "bSortable": false, "aTargets": [ -1 ]
            }]
        });
        
        $(".btnAddProduct").click(function(event){
            event.preventDefault();
            var leftPanel=$(this).parent().parent().parent();
            var rightPanel=leftPanel.parent().find(".rightPanel");
            leftPanel.hide("slide", { direction: "left" }, 500, function() {
                rightPanel.show();
            });
            rightPanel.html("<?php echo ACTION_LOADING; ?>");
            rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/add/");
        });
        $("#showProductBranch, #changeCategoryProductView, #displayProduct, #showProductPriceType, #showProductQty").change(function(){
            $.cookie("displayProduct", $("#displayProduct").val(), {
                expires : 7,
                path    : '/'
            });
            var Tablesetting = oTableProductDashBoard.fnSettings();
            Tablesetting.sAjaxSource = "<?php echo $this->base . '/' . $this->params['controller']; ?>/ajax/"+$("#showProductBranch").val()+"/"+$("#changeCategoryProductView").val()+"/"+$("#displayProduct").val()+"/"+$("#showProductPriceType").val()+"/"+$("#showProductQty").val();
            oCache.iCacheLower = -1;
            oTableProductDashBoard.fnDraw(false);
        });
        $(".btnExportProduct").click(function(){
            $.ajax({
                type: "POST",
                url: "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/exportExcel",
                data: "action=export",
                beforeSend: function(){
                    $(".btnExportProduct").attr('disabled','disabled');
                    $(".btnExportProduct").find('img').attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                },
                success: function(){
                    $(".btnExportProduct").removeAttr('disabled');
                    $(".btnExportProduct").find('img').attr("src", "<?php echo $this->webroot; ?>img/button/csv.png");
                    window.open("<?php echo $this->webroot; ?>public/report/product_export.csv", "_blank");
                }
            });
        });
        
        $(".btnAllProductBarcode").click(function(){
            var productGroupId = $("#changeCategoryProductView").val();
            $.ajax({
                type: "POST",
                url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printBarcode/"+productGroupId,
                beforeSend: function() {
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(printBarcode) {
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    w = window.open();
                    w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                    w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                    w.document.write(printBarcode);
                    w.document.close();
                }
            });
        });
        $(".btnAllReset").click(function(event){
            event.preventDefault();
            $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Do you want to reset all products checked </b>?</p>');
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
                    '<?php echo "Reset"; ?>': function() {
                        $.ajax({
                            type: "GET",
                            url: "<?php echo $this->base.'/'.$this->params['controller']; ?>/printProductByCheck/clearData",
                            data: "",
                            beforeSend: function(){
                                $("#dialog").dialog("close");
                                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                            },
                            success: function(result){
                                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                oCache.iCacheLower = -1;
                                oTableProductDashBoard.fnDraw(false);
                                if(result != '<?php echo MESSAGE_DATA_HAS_BEEN_DELETED; ?>'){
                                    createSysAct('Products', 'Reset', 2, result);
                                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                                }else {
                                    createSysAct('Products', 'Reset', 1, '');
                                    // alert message
                                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>Reset Successful</p>');
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
        $(".btnPrintAllProduct").click(function(){
            if($("#productBarcodeWithPriceType").val() != ""){
                var productGroupId = $("#changeCategoryProductView").val();
                $.ajax({
                    type: "POST",
                    url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printProduct/"+$("#productBarcodeWithPriceType").val()+"/"+productGroupId,
                    beforeSend: function() {
                        $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner.gif');
                    },
                    success: function(printBarcode) {
                        $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                        w = window.open();
                        w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                        w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                        w.document.write(printBarcode);
                        w.document.close();
                    }
                });
            }else{
                setPriceType();
            }
        });
        $(".btnPrintAllProductView").click(function(){
            $("#viewDialogPrint").dialog({
                title: '<?php echo DIALOG_INFORMATION_PRINT_BARCODE; ?>',
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
        });
    });
    // Function Introduction
    <?php echo $funcIntroduce; ?>
    
    function getScrollTable(){
        $('div.dataTables_scrollBody').css('height',calcDataTableHeight());
        oTableProductDashBoard.fnAdjustColumnSizing();
    }
    
    function changeBackgroupProduct(){
        $("#<?php echo $tblName; ?> tbody tr").each(function(){
                $(this).removeAttr('style');
        });
    }
    
    function popUpProductCatalog(mylink, windowname) { 
        if (! window.focus)
        return true; 
        var href; 
        if (typeof(mylink) == 'string') href=mylink; else href=mylink.href; 
        window.open(href, windowname, 'width=700,height=500,scrollbars=yes'); 
        return false; 
    }
    
    function setIdActionProduct(){
        var action = new Array('btnViewProductView', 'btnCloneProductView', 'btnEditProductView', 'btnDeleteProductView', 'setProductPrice');
        $.each( action, function( key, value ) {
            $("#<?php echo $tblName; ?> tr:eq(0)").find('.'+value).attr('id', value);
        });
    }
    
    function setPriceType(){
        // alert message set price type
        $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_CONFIRM_SELECT_PRICE_TYPE_WITH_PRINT_BARCODE; ?></p>');
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
</script>
<div class="leftPanel">
    <div style="padding-top: 3px; padding-bottom: 3px; padding-left: 5px; padding-right: 5px; border: 1px dashed #bbbbbb; margin-bottom: 5px;" id="divHeader">
        <?php 
            if($allowAdd){
        ?>
        <div class="buttons">
            <a href="" class="positive btnAddProduct">
                <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
                <?php echo MENU_PRODUCT_NAME_MANAGEMENT_ADD; ?>
            </a>
        </div>
        <?php 
            } 
        ?>
        <?php if($allowExport){ ?>
        <div class="buttons">
            <button type="button" class="positive btnExportProduct">
                <img src="<?php echo $this->webroot; ?>img/button/csv.png" alt=""/>
                <?php echo ACTION_EXPORT_TO_EXCEL; ?>
            </button>
        </div>
        <?php } ?>
        <?php if($allowPrintAllProduct || $allowPrintAllBarcode || ((($allowPrintAllBarcode || $allowPrintAllProduct && $allowPrintByChecked) || ($allowPrintByChecked)))){ ?>
        <div class="buttons">
            <button type="button" class="positive btnPrintAllProductView">
                <img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/>
                <?php echo TABLE_PRINT_BARCODE_AND_PRICE; ?>
            </button>
        </div> 
        <?php } ?>
        <div id="viewDialogPrint" style="display: none;">
            <table style="width: 100%;">
                <tr>
                    <td style="border: 1px solid #aaaaaa;">
                        <div style="float: left; padding-left: 7px; padding-top: 7px;">
                            <select id="productBarcodeWithPriceType" style="width: 195px; height: 25px;">
                                <option value=""><?php echo TABLE_SELECT_PRICE_TYPE; ?></option>
                                <?php
                                $sqlPriceType = mysql_query("SELECT id, name FROM price_types WHERE id != 1 AND id IN (SELECT price_type_id FROM price_type_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id'].")) GROUP BY id");
                                while($rowPriceType = mysql_fetch_array($sqlPriceType)){
                                ?>
                                <option value="<?php echo $rowPriceType['id']; ?>"><?php echo $rowPriceType['name']; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div style="height: 5px; clear: both;"></div>
                        <?php if($allowPrintAllProduct){?>
                        <div class="buttons" style="padding-left: 7px;">
                            <button type="button" class="positive btnPrintAllProduct" style="width: 195px;">
                                <img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/>
                                <?php echo ACTION_PRINT_ALL_PRODUCT; ?>
                            </button>
                        </div> 
                        <div style="height: 7px; clear: both;"></div>
                        <?php } ?>
                    </td>
                    <td style="border: 1px solid #aaaaaa;">
                        <?php if($allowPrintAllBarcode){?>
                        <div class="buttons" style="padding-left: 7px;">
                            <button type="button" class="positive btnAllProductBarcode">
                                <img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/>
                                <?php echo ACTION_PRINT_ALL_PRODUCT_BARCODE; ?>
                            </button>
                        </div>    
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="border: 1px solid #aaaaaa; padding: 7px;">
                        <?php if(($allowPrintAllBarcode || $allowPrintAllProduct && $allowPrintByChecked) || ($allowPrintByChecked)){?>
                        <div class="buttons">
                            <button type="button" class="positive btnAllReset" style="width: 100%; text-align: center;">
                                <img src="<?php echo $this->webroot; ?>img/button/cycle.png" alt=""/>
                                <?php echo ACTION_RESET_ALL; ?>
                            </button>
                        </div>    
                        <?php } ?>
                    </td>
                </tr>
            </table>
        </div>
        <div style="float:right;">
            <label for="displayProduct"><?php echo TABLE_SHOW; ?> </label>: 
            <select id="displayProduct" style="width: 100px; height: 25px;">
                <option value="1"><?php echo TABLE_PRODUCT_FILTER_PRODUCT; ?></option>
                <option value="2"><?php echo TABLE_PRODUCT_FILTER_SUB_PRODUCT; ?></option>
            </select>	
            &nbsp;&nbsp;&nbsp;
            <label for="changeCategoryProductView"><?php echo TABLE_GROUP; ?></label>:
            <select id="changeCategoryProductView" style="width: 130px; height: 25px;">
                <option value="all"><?php echo TABLE_ALL; ?></option>
                <?php
                $queryPgroup = mysql_query("SELECT * FROM `pgroups` WHERE is_active = 1 AND id IN (SELECT pgroup_id FROM pgroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id'].")) AND is_active = 1 ORDER BY name");
                while($dataPgroup=mysql_fetch_array($queryPgroup)){
                ?>
                <option value="<?php echo $dataPgroup['id']; ?>"><?php echo $dataPgroup['name']; ?></option>
                <?php
                }
                ?>
            </select>
            <?php
            if(COUNT($branches) == 1){
            ?>
            &nbsp;&nbsp;&nbsp;
            <label for="showProductBranch"><?php echo TABLE_BRANCH ?></label>: 
            <select id="showProductBranch" style="width: 130px; height: 25px;">
                <?php
                foreach($branches as $branch){
                ?>
                <option value="<?php echo $branch['Branch']['id']; ?>"><?php echo $branch['Branch']['name']; ?></option>
                <?php
                }
                ?>
            </select>
            <?php
            } else {
            ?>
            <input type="hidden" id="showProductBranch" value="<?php echo $branches[0]['Branch']['id']; ?>" />
            <?php
            }
            ?>
            &nbsp;&nbsp;&nbsp;
            <label for="showProductPriceType"><?php echo TABLE_PRICE_TYPE ?></label>: 
            <select id="showProductPriceType" style="width: 130px; height: 25px;">
                <?php
                $sqlPrice = mysql_query("SELECT id, name FROM price_types WHERE is_active = 1 AND is_ecommerce = 0 ORDER BY ordering ASC");
                while($row = mysql_fetch_array($sqlPrice)){
                ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                <?php
                }
                ?>
            </select>
            &nbsp;&nbsp;&nbsp;
            <label for="showProductQty"><?php echo TABLE_QTY ?></label>: 
            <select id="showProductQty" style="width: 50px; height: 25px;">
                <option value="all"><?php echo TABLE_ALL; ?></option>
                <option value="1">>0</option>
                <option value="2">=0</option>
            </select>
        </div>
        <div style="clear: both;"></div>
    </div>
    <div id="dynamic" style="height: 100%">
        <table id="<?php echo $tblName; ?>" class="table" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <th class="first"><?php echo TABLE_NO; ?></th>
                    <th><?php echo TABLE_NAME; ?></th>
                    <th><?php echo TABLE_GROUP; ?></th>
                    <th><?php echo TABLE_BARCODE; ?></th>
                    <th><?php echo TABLE_UOM; ?></th>
                    <th><?php echo TABLE_CHIMICAL_NAME; ?></th>
                    <th><?php echo TABLE_QTY; ?></th>
                    <?php
                    if($allowViewCost){
                    ?>
                    <th><?php echo TABLE_UNIT_COST; ?></th>
                    <?php
                    }
                    ?>
                    <th><?php echo TABLE_PRICE; ?></th>
                    <th><?php echo ACTION_ACTION; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="11" class="dataTables_empty first"><?php echo TABLE_LOADING; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="rightPanel"></div>