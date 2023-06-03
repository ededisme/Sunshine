<?php
// Authentication
$this->element('check_access');
$allowAdd    = checkAccess($user['User']['id'], $this->params['controller'], 'add');
$allowPrint  = checkAccess($user['User']['id'], $this->params['controller'], 'printInvoice');
$allowEdit   = checkAccess($user['User']['id'], $this->params['controller'], 'edit');
$allowAging  = checkAccess($user['User']['id'], $this->params['controller'], 'aging');
$allowDelete = checkAccess($user['User']['id'], $this->params['controller'], 'delete');
$allowClose  = checkAccess($user['User']['id'], $this->params['controller'], 'close');
$tblName = "tbl" . rand(); ?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<script type="text/javascript">
    var oPOTable;
    var PbTableName = "<?php echo $tblName; ?>";
    var tabPBId  = $(".ui-tabs-selected a").attr("href");
    var tabPBReg = '';
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        $("#<?php echo $tblName; ?> td:first-child").addClass('first');
        oPOTable = $("#<?php echo $tblName; ?>").dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo $this->base.'/'.$this->params['controller']; ?>/ajax/"+$("#changeStatusPurchaseOrder").val()+"/"+$("#changeBalancePurchaseOrder").val()+"/"+$("#changeVendorIdPurchaseOrder").val()+"/"+$("#changeDatePurchaseOrder").val(),
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                $("#<?php echo $tblName; ?> td:first-child").addClass('first');
                $("#<?php echo $tblName; ?> td:last-child").css("white-space", "nowrap");
                $(".btnViewPurchaseOrder").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var leftPanel  = $(".btnAddPurchaseOrder").parent().parent().parent();
                    var rightPanel = leftPanel.parent().find(".rightPanel");
                    leftPanel.hide("slide", { direction: "left" }, 500, function() {
                        rightPanel.show();
                    });
                    rightPanel.html("<?php echo ACTION_LOADING; ?>");
                    rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/view/" + id);
                });
                <?php if($allowEdit){ ?>
                $(".btnEditPurchaseOrder").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var leftPanel  =  $(".btnAddPurchaseOrder").parent().parent().parent();
                    var rightPanel =  leftPanel.parent().find(".rightPanel");
                    leftPanel.hide("slide", { direction: "left" }, 500, function() {
                        rightPanel.show();
                    });
                    rightPanel.html("<?php echo ACTION_LOADING; ?>");
                    rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/edit/"+id);
                    
                });
                <?php
                }
                if($allowDelete){
                ?>
                $(".btnDeletePurchaseOrder").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var name = $(this).attr('name');
                    $("#dialog").dialog('option', 'title', '<?php echo DIALOG_CONFIRMATION; ?>');
                    $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_CONFIRM_DELETE; ?> <b>' + name + '</b>?</p>');
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
                                        oPOTable.fnDraw(false);
                                        // alert message
                                        if(result != '<?php echo MESSAGE_DATA_HAS_BEEN_DELETED; ?>' && result != '<?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?>'){
                                            createSysAct('Purchase Bill', 'Delete', 2, result);
                                            $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                                        }else {
                                            createSysAct('Purchase Bill', 'Delete', 1, '');
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
                if($allowAging){
                ?>
                $(".btnAgingPurchaseOrder").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var leftPanel  = $(".btnAddPurchaseOrder").parent().parent().parent();
                    var rightPanel = leftPanel.parent().find(".rightPanel");
                    leftPanel.hide("slide", { direction: "left" }, 500, function() {
                        rightPanel.show();
                    });
                    rightPanel.html("<?php echo ACTION_LOADING; ?>");
                    rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/aging/" + id);
                });
                <?php
                }
                if($allowPrint){
                ?>
                $(".btnPrintPurchaseOrder").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr("rel");
                    $("#dialog").html('<div class="buttons"><button type="submit" class="positive printInvoicePB" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span><?php echo ACTION_PRINT_PURCHASE_BILL; ?></span></button><button type="submit" class="positive printInvoiceProPO" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span><?php echo ACTION_ONLY_PRODUCT; ?></span></button></div> ');
                    $(".printInvoicePB").click(function(){
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
                    $(".printInvoiceProPO").click(function(){
                        $.ajax({
                            type: "POST",
                            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printInvoiceProduct/"+id,
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
                if($allowClose){
                ?>
                $(".btnClosePurchaseOrder").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var name = $(this).attr('name');
                    $("#dialog").dialog('option', 'title', '<?php echo DIALOG_CONFIRMATION; ?>');
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
                                        oPOTable.fnDraw(false);
                                        // Alert Message
                                        if(result != '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>'){
                                            createSysAct('Purchase Bill', 'Close (All Items are Service)', 2, result);
                                            $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                                        }else {
                                            createSysAct('Purchase Bill', 'Close (All Items are Service)', 1, '');
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
                "sType": "numeric", "aTargets": [ 0 ],
                "bSortable": false, "aTargets": [ 0,-1 ]
            }],
            "aaSorting": [[ 1, "desc" ]]
            
        });
        $('#changeDatePurchaseOrder').datepicker({
            dateFormat:'dd/mm/yy',
            changeMonth: true,
            changeYear: true
        }).unbind("blur");
        $("#clearDatePO").click(function(){
            $('#changeDatePurchaseOrder').val('');
            filterPurchaseBill();
        });
        $("#changeStatusPurchaseOrder, #changeBalancePurchaseOrder, #changeDatePurchaseOrder").change(function(){
            filterPurchaseBill();
        });
        $("#changeVendorPurchaseOrder").autocomplete("<?php echo $this->base ."/reports/searchVendor"; ?>", {
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
            $("#changeVendorIdPurchaseOrder").val(value.toString().split(".*")[0]);
            $("#changeVendorPurchaseOrder").val(value.toString().split(".*")[2]+" - "+value.toString().split(".*")[1]).attr("readonly","readonly");
            $("#clearVendorPurchaseOrder").show();
            filterPurchaseBill();
        });
        
        $("#clearVendorPurchaseOrder").click(function(){
            $("#changeVendorIdPurchaseOrder").val("all");
            $("#changeVendorPurchaseOrder").val("");
            $("#changeVendorPurchaseOrder").removeAttr("readonly");
            $("#clearVendorPurchaseOrder").hide();
            filterPurchaseBill();
        });
        <?php if($allowAdd){ ?>
        $(".btnAddPurchaseOrder").click(function(event){
            event.preventDefault();
            var leftPanel  = $(this).parent().parent().parent();
            var rightPanel = leftPanel.parent().find(".rightPanel");
            leftPanel.hide("slide", { direction: "left" }, 500, function() {
                rightPanel.show();
            });
            rightPanel.html("<?php echo ACTION_LOADING; ?>");
            rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/add/");
        });
        <?php } ?>
    });
    
    function filterPurchaseBill(){
        $("#changeDatePurchaseOrder").datepicker("option", "dateFormat", "yy-mm-dd");
        var Tablesetting = oPOTable.fnSettings();
        Tablesetting.sAjaxSource = "<?php echo $this->base . '/' . $this->params['controller']; ?>/ajax/"+$("#changeStatusPurchaseOrder").val()+"/"+$("#changeBalancePurchaseOrder").val()+"/"+$("#changeVendorIdPurchaseOrder").val()+"/"+$("#changeDatePurchaseOrder").val(),
        oCache.iCacheLower = -1;
        oPOTable.fnDraw(false);
        $("#changeDatePurchaseOrder").datepicker("option", "dateFormat", "dd/mm/yy");
    }
</script>
<div class="leftPanel">
    <div style="padding: 5px;border: 1px dashed #bbbbbb;">
        <?php if($allowAdd){ ?>
        <div class="buttons">
            <a href="" class="positive btnAddPurchaseOrder">
                <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
                <?php echo MENU_PURCHASE_ORDER_MANAGEMENT_ADD; ?>
            </a>
        </div>
        <?php } ?>
        <div style="float:right;">
            <?php echo TABLE_DATE; ?> :
            <input type="text" id="changeDatePurchaseOrder" style="width: 115px; height: 20px;" readonly="readonly" /> <img alt="" src="<?php echo $this->webroot; ?>img/button/clear.png" style="cursor: pointer;" onmouseover="Tip('Clear Date')" id="clearDatePO" />
            <label for="changeVendorPurchaseOrder"><?php echo TABLE_VENDOR; ?> :</label>
            <input type="hidden" id="changeVendorIdPurchaseOrder" value="all" />
            <input type="text" id="changeVendorPurchaseOrder" style="width: 250px; height: 20px;" />
            <img alt="" src="<?php echo $this->webroot; ?>img/button/delete.png" style="cursor: pointer; display: none;" onmouseover="Tip('Clear Vendor')" id="clearVendorPurchaseOrder" />
            <?php echo TABLE_STATUS; ?> :
            <select id="changeStatusPurchaseOrder" style="width: 130px; height: 25px;">
                <option value="all"><?php echo TABLE_ALL; ?></option>
                <option value="1">Issued</option>
                <option value="2">Partial</option>
                <option value="3">Fulfilled</option>
                <option value="0">Void</option>
            </select>
            <?php echo GENERAL_TYPE; ?> :
            <select id="changeBalancePurchaseOrder" style="width: 130px; height: 25px;">
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
                    <th class="first"><?php echo TABLE_NO; ?></th>
                    <th style="width: 120px !important;"><?php echo TABLE_DATE; ?></th>
                    <th style="width: 120px !important;"><?php echo TABLE_PB_NUMBER; ?></th>
                    <th style="width: 180px !important;"><?php echo TABLE_LOCATION_GROUP; ?></th>
                    <th><?php echo TABLE_VENDOR; ?></th>
                    <th style="width: 140px !important;"><?php echo TABLE_TOTAL_AMOUNT; ?></th>
                    <th style="width: 140px !important;"><?php echo GENERAL_BALANCE; ?></th>
                    <th style="width: 80px !important;"><?php echo TABLE_STATUS; ?></th>
                    <th style="width: 120px !important;"><?php echo ACTION_ACTION; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="9" class="dataTables_empty first"><?php echo TABLE_LOADING; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <br />
    <br />
    <?php if($allowAdd){ ?>
    <div style="padding: 5px;border: 1px dashed #bbbbbb;">
        <div class="buttons">
            <a href="" class="positive btnAddPurchaseOrder">
                <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
                <?php echo MENU_PURCHASE_ORDER_MANAGEMENT_ADD; ?>
            </a>
        </div>
        <div style="clear: both;"></div>
    </div>
    <?php } ?>
</div>
<div class="rightPanel"></div>