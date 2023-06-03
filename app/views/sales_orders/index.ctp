<?php
header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
// Authentication
$this->element('check_access');
$allowAdd = checkAccess($user['User']['id'], 'sales_orders', 'add');
$allowEdit = checkAccess($user['User']['id'], $this->params['controller'], 'edit');
$allowAging = checkAccess($user['User']['id'], $this->params['controller'], 'aging');
$allowReprint = checkAccess($user['User']['id'], $this->params['controller'], 'printInvoice');
$allowVoid = checkAccess($user['User']['id'], $this->params['controller'], 'void');
$allowViewByUser = checkAccess($user['User']['id'], $this->params['controller'], 'viewByUser');
$allowApprove = checkAccess($user['User']['id'], $this->params['controller'], 'approveSale');
?>
<?php $tblName = "tbl" . rand(); ?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<script type="text/javascript">
    var oTableSalesOrder;
    var tabSalesId  = $(".ui-tabs-selected a").attr("href");
    var tabSalesReg = '';
    $(document).ready(function(){
        $('#changeDateINV').datepicker({
            dateFormat:'dd/mm/yy',
            changeMonth: true,
            changeYear: true
        }).unbind("blur");
        $("#changeDateINV").datepicker("option", "dateFormat", "yy-mm-dd");
        oTableSalesOrder = $("#<?php echo $tblName; ?>").dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo $this->base . '/' . $this->params['controller']; ?>/ajax/"+$("#changeCustomerIdINV").val()+"/"+$("#changeStatusINV").val()+"/"+$("#changeBalanceINV").val()+"/"+$("#changeDateINV").val(),
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                $("#<?php echo $tblName; ?> td:first-child").addClass('first');
                $("#<?php echo $tblName; ?> td:last-child").css("white-space", "nowrap");
                $("#<?php echo $tblName; ?> td:nth-child(9)").css("white-space", "nowrap");
                $("#changeDateINV").datepicker("option", "dateFormat", "dd/mm/yy");
                $(".btnViewSalesOrder").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var leftPanel=$(this).parent().parent().parent().parent().parent().parent().parent();
                    var rightPanel=leftPanel.parent().find(".rightPanel");
                    leftPanel.hide("slide", { direction: "left" }, 500, function() {
                        rightPanel.show();
                    });
                    rightPanel.html("<?php echo ACTION_LOADING; ?>");
                    rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/view/" + id);
                });
                <?php 
                if ($allowAging) {
                ?>
                $(".btnAgingSalesOrder").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var leftPanel  = $(this).parent().parent().parent().parent().parent().parent().parent();
                    var rightPanel = leftPanel.parent().find(".rightPanel");
                    leftPanel.hide("slide", { direction: "left" }, 500, function() {
                        rightPanel.show();
                    });
                    rightPanel.html("<?php echo ACTION_LOADING; ?>");
                    rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/aging/" + id);
                });
                <?php 
                }
                if ($allowVoid) {
                ?>
                $(".btnVoidSalesOrder").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var name = $(this).attr('name');
                    var is_pos = $(this).attr('href');
                    if(is_pos == 1){
                        voidPOS(id, name);
                    }else{
                        voidSO(id, name);
                    }
                });
                <?php 
                }
                if ($allowEdit) {
                ?>
                $(".btnEditSalesOrder").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var leftPanel  = $(".btnEditSalesOrder").parent().parent().parent().parent().parent().parent().parent();
                    var rightPanel = leftPanel.parent().find(".rightPanel");
                    leftPanel.hide("slide", { direction: "left" }, 500, function() {
                        rightPanel.show();
                    });
                    rightPanel.html("<?php echo ACTION_LOADING; ?>");
                    rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/edit/"+id);
                    
                });
                <?php 
                }
                if ($allowApprove) {
                ?>
                // Action Approve
                $(".btnSalesOrderApprove").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var name = $(this).attr('name');
                    saleApprove(id, name);
                });
                <?php 
                }
                if ($allowReprint) {
                ?>
                // Action Reprint Invoice
                $(".btnReprintInvoice").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var isPos = $(this).attr('href');
                    var url   = '';
                    if(isPos == 1){
                        url = "<?php echo $this->base . '/' . "point_of_sales"; ?>/printReceipt/";
                    } else {
                        url = "<?php echo $this->base . '/' . "sales_orders"; ?>/printInvoice/";
                    }
                    $("#dialog").html('<div class="buttons"><button type="submit" class="positive reprintInvoiceSales" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="txtReprintInvoiceSales"><?php echo ACTION_REPRINT_INVOICE; ?></span></button></div>');
                    $(".reprintInvoiceSales").click(function(){
                        $.ajax({
                            type: "POST",
                            url: url+id,
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
                });
                <?php 
                }
                ?>
                return sPre;
            },
            "aoColumnDefs": [{
                "sType": "numeric", "aTargets": [ 0 ],
                "bSortable": false, "aTargets": [ -1 ]
            }],
            "aaSorting": [[ 1, "desc" ]]
        });
        
        

        $("#changeCustomerIdINV, #changeStatusINV, #changeBalanceINV, #changeDateINV").change(function(){
            resetFilterInvoice();
        });
        
        $("#clearDateINV").click(function(){
            $('#changeDateINV').val('');
            resetFilterInvoice();
        });
        
        $("#changeCusINV").autocomplete("<?php echo $this->base ."/reports/searchPatient"; ?>", {
            width: 410,
            max: 10,
            scroll: true,
            scrollHeight: 500,
            formatItem: function(data, i, n, value) {
                if(value.split(".*")[1]!=""){
                    return value.split(".*")[1];
                }
            },
            formatResult: function(data, value) {
                if(value.split(".*")[1]!=""){
                    return value.split(".*")[1];
                }
            }
        }).result(function(event, value){
            $("#changeCustomerIdINV").val(value.toString().split(".*")[0]);
            $("#changeCusINV").val(value.toString().split(".*")[1]).attr("readonly", true);
            $("#clearCusINV").show();
            resetFilterInvoice();
        });
        
        $("#clearCusINV").click(function(){
            $("#changeCustomerIdINV").val("all");
            $("#changeCusINV").val("");
            $("#changeCusINV").removeAttr("readonly");
            $("#clearCusINV").hide();
            resetFilterInvoice();
        });
        <?php 
        if ($allowAdd) {
        ?>
        $(".btnAddSalesOrder").click(function(event){
            event.preventDefault();
            var leftPanel=$(this).parent().parent().parent();
            var rightPanel=leftPanel.parent().find(".rightPanel");
            leftPanel.hide("slide", { direction: "left" }, 500, function() {
                rightPanel.show();
            });
            rightPanel.html("<?php echo ACTION_LOADING; ?>");
            rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/add/");
        });
        <?php
        }
        ?>
    });
    
    function resetFilterInvoice(){
        $("#changeDateINV").datepicker("option", "dateFormat", "yy-mm-dd");
        var Tablesetting = oTableSalesOrder.fnSettings();
        Tablesetting.sAjaxSource = "<?php echo $this->base . '/' . $this->params['controller']; ?>/ajax/"+$("#changeCustomerIdINV").val()+"/"+$("#changeStatusINV").val()+"/"+$("#changeBalanceINV").val()+"/"+$("#changeDateINV").val();
        oCache.iCacheLower = -1;
        oTableSalesOrder.fnDraw(false);
        $("#changeDateINV").datepicker("option", "dateFormat", "dd/mm/yy");
    }
    <?php
    if ($allowVoid) {
    ?>
    function voidSO(id, name){
        $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_CONFIRM_VOID; ?> <b>' + name + '</b>?</p>');
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
                '<?php echo ACTION_VOID; ?>': function() {
                    $.ajax({
                        type: "GET",
                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/void/" + id,
                        data: "",
                        beforeSend: function(){
                            $("#dialog").dialog("close");
                            $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                        },
                        success: function(result){
                            $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                            oCache.iCacheLower = -1;
                            oTableSalesOrder.fnDraw(false);
                            if(result != '<?php echo MESSAGE_DATA_HAS_BEEN_DELETED; ?>' && result != '<?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?>'){
                                createSysAct('Sales Invoice', 'Void Invoice', 2, result);
                                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                            }else {
                                createSysAct('Sales Invoice', 'Void Invoice', 1, '');
                                // alert message
                                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result+'</p>');
                            }
                            $("#dialog").dialog({
                                title: '<?php echo DIALOG_INFORMATION; ?>',
                                resizable: false,
                                modal: true,
                                width: 'auto',
                                height: 'auto',
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
    }

    function voidPOS(id, name){
        $("#dialog").dialog('option', 'title', '<?php echo DIALOG_CONFIRMATION; ?>');
        $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_CONFIRM_VOID; ?> <b>' + name + '</b>?</p>');
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
                '<?php echo ACTION_VOID; ?>': function() {
                    $.ajax({
                        type: "GET",
                        url: "<?php echo $this->base . '/point_of_sales'; ?>/void/" + id,
                        beforeSend: function(){
                            $("#dialog").dialog("close");
                            $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                        },
                        success: function(result){
                            $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                            oCache.iCacheLower = -1;
                            oTableSalesOrder.fnDraw(false);
                            if(result != '<?php echo MESSAGE_DATA_HAS_BEEN_DELETED; ?>'){
                                createSysAct('Sales Invoice', 'Void POS', 2, result);
                                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                            }else {
                                createSysAct('Sales Invoice', 'Void POS', 1, '');
                                // alert message
                                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result+'</p>');
                            }
                            $("#dialog").dialog({
                                title: '<?php echo DIALOG_INFORMATION; ?>',
                                resizable: false,
                                modal: true,
                                width: 'auto',
                                height: 'auto',
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
    }
    <?php 
    }
    if ($allowApprove) {
    ?>
    function saleApprove(id, name){
        $("#dialog").dialog('option', 'title', '<?php echo DIALOG_CONFIRMATION; ?>');
        $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_CONFRIM_APPROVE; ?> <b>' + name + '</b>?</p>');
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
                '<?php echo ACTION_APPROVE; ?>': function() {
                    $.ajax({
                        type: "GET",
                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/approveSale/" + id,
                        data: "",
                        beforeSend: function(){
                            $("#dialog").dialog("close");
                            $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                        },
                        success: function(result){
                            $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                            oCache.iCacheLower = -1;
                            oTableSalesOrder.fnDraw(false);
                            $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result+'</p>');
                            $("#dialog").dialog({
                                title: '<?php echo DIALOG_INFORMATION; ?>',
                                resizable: false,
                                modal: true,
                                width: 'auto',
                                height: 'auto',
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
    }
    <?php } ?>
</script>
<div class="leftPanel">
    
    <div style="padding: 5px;border: 1px dashed #3C69AD;">
        <?php 
        if ($allowAdd) {
        ?>
        <div class="buttons">
                <a href="" class="positive btnAddSalesOrder">
                    <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt="" />
                <?php echo MENU_SALES_ORDER_MANAGEMENT_ADD; ?>
                    <img src="<?php echo $this->webroot; ?>img/icon/invoice.png" alt=""/>
            </a>
        </div>
        <?php } ?>
        <div style="float:right;">
            <?php echo TABLE_DATE; ?> :
            <input type="text" value="<?php echo date('d/m/Y'); ?>"  id="changeDateINV" style="width: 115px; height: 20px;" readonly="readonly" /> <img alt="" src="<?php echo $this->webroot; ?>img/button/clear.png" style="cursor: pointer; vertical-align: middle;" onmouseover="Tip('Clear Date')" id="clearDateINV" />
            <label style="margin-left: 10px;" for="changeCusINV"><?php echo PATIENT_NAME; ?> :</label>
            <input type="hidden" id="changeCustomerIdINV" value="all" />
            <input type="text" id="changeCusINV" style="width: 250px; height: 20px;" />
            <img alt="" src="<?php echo $this->webroot; ?>img/button/delete.png" style="cursor: pointer; vertical-align: middle; display: none;" onmouseover="Tip('Clear Customer')" id="clearCusINV" />
            <?php echo TABLE_STATUS; ?> :
            <select id="changeStatusINV" style="width: 130px; height: 25px;">
                <option value="all"><?php echo TABLE_ALL; ?></option>
                <option value="1">Issued</option>
                <option value="2">Fulfilled</option>
                <option value="0">Void</option>
            </select>
            <?php echo GENERAL_TYPE; ?> :
            <select id="changeBalanceINV" style="width: 130px; height: 25px;">
                <option value="all"><?php echo TABLE_ALL; ?></option>
                <option value="1"><?php echo TABLE_INDEBTED; ?></option>
                <option value="2"><?php echo GENERAL_PAID; ?></option>
            </select>
        </div>
        <div style="clear: both;"></div>
    </div>
    <br />
    <div id="dynamic">
        <table id="<?php echo $tblName; ?>" class="table" cellspacing="0">
            <thead>
                <tr>
                    <th style="width: 4%;" class="first"><?php echo TABLE_NO; ?></th>
                    <th style="width: 10%;"><?php echo TABLE_INVOICE_DATE; ?></th>
                    <th style="width: 10%;"><?php echo TABLE_INVOICE_NO; ?></th>
                    <th style="width: 27%;"><?php echo PATIENT_NAME; ?></th>
                    <th style="width: 11%;"><?php echo TABLE_TOTAL_AMOUNT; ?></th>
                    <th style="width: 11%;"><?php echo GENERAL_BALANCE; ?></th>
                    <th style="width: 7%;"><?php echo TABLE_STATUS; ?></th>
                    <th style="width: 7%;"><?php echo TABLE_TYPE; ?></th>
                    <th style="width: 7%;"><?php echo TABLE_CREATED_BY; ?></th>
                    <th style="width: 7%;"><?php echo MENU_PRESCRIPTION_CODE; ?></th>
                    <th style="width: 13%;"><?php echo ACTION_ACTION; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="11" class="dataTables_empty first"><?php echo TABLE_LOADING; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <br />
    <br />
    <?php 
       if ($allowAdd) {
    ?>
    <div style="padding: 5px;border: 1px dashed #3C69AD;">
        <div class="buttons">
            <a href="" class="positive btnAddSalesOrder">
                <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
                <?php echo MENU_SALES_ORDER_MANAGEMENT_ADD; ?>
                <img src="<?php echo $this->webroot; ?>img/icon/invoice.png" alt=""/>
            </a>
        </div>
        <div style="clear: both;"></div>
    </div>
    <?php } ?>
</div>
<div class="rightPanel"></div>