<?php
// Authentication
$this->element('check_access');
$allowAdd = checkAccess($user['User']['id'], 'orders', 'add');
$allowEdit = checkAccess($user['User']['id'], $this->params['controller'], 'edit');
$allowPrint = checkAccess($user['User']['id'], $this->params['controller'], 'printInvoice');
$allowVoid = checkAccess($user['User']['id'], $this->params['controller'], 'delete');
$allowApprove = checkAccess($user['User']['id'], $this->params['controller'], 'approve');
$allowClose = checkAccess($user['User']['id'], $this->params['controller'], 'close');
$allowOpen = checkAccess($user['User']['id'], $this->params['controller'], 'open');
?>
<?php $tblName = "tbl" . rand(); ?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<script type="text/javascript">
    var oTableOrder;
    var tabOrderId  = $(".ui-tabs-selected a").attr("href");
    var tabOrderReg = '';
    $(document).ready(function() {
        $('#changeDateOrder').datepicker({
            dateFormat:'dd/mm/yy',
            changeMonth: true,
            changeYear: true
        }).unbind("blur");
        // Prevent Key Enter
        preventKeyEnter();
        $("#<?php echo $tblName; ?> td:first-child").addClass('first'); 
        $("#changeDateOrder").datepicker("option", "dateFormat", "yy-mm-dd");
        oTableOrder = $("#<?php echo $tblName; ?>").dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo $this->base . '/' . $this->params['controller']; ?>/ajax/"+$("#changeCustomerIdOrder").val()+"/"+$("#changeStatusOrder").val()+"/"+$("#changeApproveOrder").val()+"/"+$("#changeDateOrder").val(),
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function(oSettings, iStart, iEnd, iMax, iTotal, sPre) {
                $("#<?php echo $tblName; ?> td:first-child").addClass('first');
                $("#<?php echo $tblName; ?> td:last-child").css("white-space", "nowrap");
                $(".reloadPrescription").attr("src","<?php echo $this->webroot; ?>img/button/refresh-active.png");
                $("#changeDateOrder").datepicker("option", "dateFormat", "dd/mm/yy");
                $(".btnViewOrder").click(function(event) {
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var leftPanel = $(this).parent().parent().parent().parent().parent().parent().parent();
                    var rightPanel = leftPanel.parent().find(".rightPanel");
                    leftPanel.hide("slide", {direction: "left"}, 500, function() {
                        rightPanel.show();
                    });
                    rightPanel.html("<?php echo ACTION_LOADING; ?>");
                    rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/view/" + id);
                });
                <?php
                if($allowPrint){
                ?>
                $(".btnPrintInvoiceOrder").click(function(event) {
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    $("#dialog").html('<div class="buttons"><button type="submit" class="positive printInvoiceOrder" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span><?php echo ACTION_PRINT_PRESCRIPTION; ?></span></button><button type="submit" class="positive printInvoiceOrderNoHead" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span><?php echo ACTION_PRINT_PRESCRIPTION_NO_HEADER; ?></span></button></div>');
                    $(".printInvoiceOrder").click(function(){
                        $.ajax({
                            type: "POST",
                            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printInvoice/"+id,
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
                            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printInvoice/"+id+"/1",
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
                        buttons: {
                            '<?php echo ACTION_CLOSE; ?>': function() {
                                $(this).dialog("close");
                            }
                        }
                    });
                });
                <?php
                }
                if($allowEdit){
                ?>
                $(".btnEditOrder").click(function(event) {
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var leftPanel = $(this).parent().parent().parent().parent().parent().parent().parent();
                    var rightPanel = leftPanel.parent().find(".rightPanel");
                    leftPanel.hide("slide", {direction: "left"}, 500, function() {
                        rightPanel.show();
                    });
                    rightPanel.html("<?php echo ACTION_LOADING; ?>");
                    rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/edit/" + id);
                });
                <?php
                }
                if($allowVoid){
                ?>
                $(".btnVoidOrder").click(function(event) {
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var name = $(this).attr('name');
                    voidOrder(id, name);
                });
                <?php
                }
                if($allowClose){
                ?>
                $(".btnCloseOrder").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var name = $(this).attr('name');
                    $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_CONFIRM_CLOSE; ?> <b>' + name + '</b>?</p>');
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
                            '<?php echo ACTION_CLOSE; ?>': function() {
                                $.ajax({
                                    type: "GET",
                                    url: "<?php echo $this->base.'/'.$this->params['controller']; ?>/close/" + id,
                                    data: "",
                                    beforeSend: function(){
                                        $("#dialog").dialog("close");
                                        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                                    },
                                    success: function(result){
                                        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                        oCache.iCacheLower = -1;
                                        oTableOrder.fnDraw(false);
                                        // alert message
                                        if(result != '<?php echo MESSAGE_DATA_HAS_BEEN_CLOSED; ?>'){
                                            createSysAct('Sale Order', 'Close', 2, result);
                                            $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                                        }else {
                                            createSysAct('Sale Order', 'Close', 1, '');
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
                });
                <?php
                }
                if($allowOpen){
                ?>
                $(".btnOpenOrder").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var name = $(this).attr('name');
                    $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_CONFIRM_OPEN; ?> <b>' + name + '</b>?</p>');
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
                            '<?php echo ACTION_OPEN; ?>': function() {
                                $.ajax({
                                    type: "GET",
                                    url: "<?php echo $this->base.'/'.$this->params['controller']; ?>/open/" + id,
                                    data: "",
                                    beforeSend: function(){
                                        $("#dialog").dialog("close");
                                        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                                    },
                                    success: function(result){
                                        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                        oCache.iCacheLower = -1;
                                        oTableOrder.fnDraw(false);
                                        // alert message
                                        if(result != '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>'){
                                            createSysAct('Sale Order', 'Open', 2, result);
                                            $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                                        }else {
                                            createSysAct('Sale Order', 'Open', 1, '');
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
                });
                <?php
                }
                if($allowApprove){
                ?>
                $(".btnApproveOrder").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var name = $(this).attr('name');
                    $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_CONFIRM_APPROVE; ?> <b>' + name + '</b>?</p>');
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
                                    url: "<?php echo $this->base.'/'.$this->params['controller']; ?>/approve/" + id,
                                    data: "",
                                    beforeSend: function(){
                                        $("#dialog").dialog("close");
                                        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                                    },
                                    success: function(result){
                                        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                        oCache.iCacheLower = -1;
                                        oTableOrder.fnDraw(false);
                                        // alert message
                                        if(result != '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>'){
                                            createSysAct('Sales Order', 'Approve', 2, result);
                                            $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                                        }else {
                                            createSysAct('Sales Order', 'Approve', 1, '');
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
                });
                <?php
                }
                ?>
                return sPre;
            },
            "aoColumnDefs": [{
                    "sType": "numeric", "aTargets": [0],
                    "bSortable": false, "aTargets": [-1]
                }],
            "aaSorting": [[2, "desc"]]
        });
        <?php
        if($allowAdd){
        ?>
        $(".btnAddOrder").click(function(event) {
            event.preventDefault();
            var leftPanel = $(this).parent().parent().parent();
            var rightPanel = leftPanel.parent().find(".rightPanel");
            leftPanel.hide("slide", {direction: "left"}, 500, function() {
                rightPanel.show();
            });
            rightPanel.html("<?php echo ACTION_LOADING; ?>");
            rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/add/");
        });
        <?php
        }
        ?>
        
        $(".reloadPrescription").click(function(){
            resetFilterOrder();
        });
        
        $("#clearDateOrder").click(function(){
            $("#changeDateOrder").val('');
            resetFilterOrder();
        });
        
        $("#changeDateOrder, #changeCustomerIdOrder, #changeStatusOrder, #changeApproveOrder").change(function(){
            resetFilterOrder();
        });
        
        $("#changeCusOrder").autocomplete("<?php echo $this->base ."/reports/searchPatient"; ?>", {
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
            $("#changeCustomerIdOrder").val(value.toString().split(".*")[0]);
            $("#changeCusOrder").val(value.toString().split(".*")[1]).attr("readonly", true);
            $("#clearCusOrder").show();
            resetFilterOrder();
        });
        
        $("#clearCusOrder").click(function(){
            $("#changeCustomerIdOrder").val("all");
            $("#changeCusOrder").val("");
            $("#changeCusOrder").removeAttr("readonly");
            $("#clearCusOrder").hide();
            resetFilterOrder();
        });
    });
    
    function resetFilterOrder(){
        $(".reloadPrescription").attr("src","<?php echo $this->webroot; ?>img/layout/spinner.gif"); 
        $("#changeDateOrder").datepicker("option", "dateFormat", "yy-mm-dd");
        var Tablesetting = oTableOrder.fnSettings();
        Tablesetting.sAjaxSource = "<?php echo $this->base . '/' . $this->params['controller']; ?>/ajax/"+$("#changeCustomerIdOrder").val()+"/"+$("#changeStatusOrder").val()+"/"+$("#changeApproveOrder").val()+"/"+$("#changeDateOrder").val();
        oCache.iCacheLower = -1;
        oTableOrder.fnDraw(false);
        $("#changeDateOrder").datepicker("option", "dateFormat", "dd/mm/yy");
    }
    <?php
    if($allowVoid){
    ?>
    function voidOrder(id, name) {
        $("#dialog").dialog('option', 'title', '<?php echo DIALOG_CONFIRMATION; ?>');
        $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_CONFIRM_VOID; ?> <b>' + name + '</b>?</p>');
        $("#dialog").dialog({
            title: '<?php echo DIALOG_CONFIRMATION; ?>',
            resizable: false,
            modal: true,
            width: 'auto',
            height: 'auto',
            open: function(event, ui) {
                $(".ui-dialog-buttonpane").show();
            },
            buttons: {
                '<?php echo ACTION_VOID; ?>': function() {
                    $.ajax({
                        type: "GET",
                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/delete/" + id,
                        data: "",
                        beforeSend: function() {
                            $("#dialog").dialog("close");
                            $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                        },
                        success: function(result) {
                            $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                            oCache.iCacheLower = -1;
                            oTableOrder.fnDraw(false);
                            // Alert message
                            if(result != '<?php echo MESSAGE_DATA_HAS_BEEN_DELETED; ?>' && result != '<?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?>'){
                                createSysAct('Sale Order', 'Delete', 2, result);
                                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                            }else {
                                createSysAct('Sale Order', 'Delete', 1, '');
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
    ?>
</script>
<div class="leftPanel">
        
        <div style="padding: 5px;border: 1px dashed #3C69AD;">   
            <div style="float:right;">
                <label for="changeDateOrder"><?php echo TABLE_DATE; ?> :</label>
                <input type="text" value="<?php echo date('d/m/Y'); ?>" id="changeDateOrder" style="width: 115px; height: 20px;" readonly="readonly" /> 
                <img alt="" src="<?php echo $this->webroot; ?>img/button/clear.png" style="cursor: pointer; vertical-align: middle;" onmouseover="Tip('Clear Date')" id="clearDateOrder" />
                <label style="margin-left: 20px;" for="changeCusOrder"><?php echo TABLE_PATIENT; ?> :</label>
                <input type="hidden" id="changeCustomerIdOrder" value="all" />
                <input type="text" id="changeCusOrder" style="width: 250px; height: 20px;" />
                <img alt="" src="<?php echo $this->webroot; ?>img/button/delete.png" style="cursor: pointer; display: none; vertical-align: middle;" onmouseover="Tip('Clear Customer')" id="clearCusOrder" />
                <label for="changeStatusOrder" style="display: none;"><?php echo TABLE_STATUS; ?> :</label>
                <select id="changeStatusOrder" style="width: 130px; height: 25px; display: none;">
                    <option value="all"><?php echo TABLE_ALL; ?></option>
                    <option value="0"><?php echo ACTION_OPEN; ?></option>
                    <option value="1"><?php echo ACTION_CLOSE; ?></option>
                </select>
                <label for="changeApproveOrder" style="display: none;"><?php echo ACTION_APPROVE; ?> :</label>
                <select id="changeApproveOrder" style="width: 130px; height: 25px; display: none;">
                    <option value="all"><?php echo TABLE_ALL; ?></option>
                    <option value="0"><?php echo ACTION_NO; ?></option>
                    <option value="1"><?php echo ACTION_YES; ?></option>
                </select>
                <span style="float: right; cursor: pointer; padding-left: 10px; vertical-align: middle; padding-top: 3px;">                    
                    <img onmouseover="Tip('Refresh')" class="reloadPrescription" alt="Refresh" src="<?php echo $this->webroot;?>img/button/refresh-active.png" />                    
                </span>
            </div>
            <div style="clear: both;"></div>
        </div>
        <br />
    <div id="dynamic">
        <table id="<?php echo $tblName; ?>" class="table" cellspacing="0">
            <thead>
                <tr>
                    <th class="first" style="width: 4% !important;"><?php echo TABLE_NO; ?></th>
                    <th style="width: 10% !important;"><?php echo GENEARL_DATE; ?></th>
                    <th style="width: 12% !important;"><?php echo MENU_PRESCRIPTION_CODE; ?></th>
                    <th style="width: 29% !important;"><?php echo PATIENT_NAME; ?></th>
                    <th style="width: 6% !important;"><?php echo TABLE_SEX; ?></th>
                    <th style="width: 10% !important;"><?php echo TABLE_DOB; ?></th>
                    <th style="width: 14% !important;"><?php echo TABLE_TELEPHONE; ?></th>
                    <th style="width: 10% !important;"><?php echo TABLE_TYPE; ?></th>
                    <th style="width: 17% !important;"><?php echo ACTION_ACTION; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="9" class="dataTables_empty"><?php echo TABLE_LOADING; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <br />
    <br />
</div>
<div class="rightPanel"></div>