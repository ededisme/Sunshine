<?php
// Authentication
$this->element('check_access');
$allowAdd=checkAccess($user['User']['id'], $this->params['controller'], 'add');
$allowExport=checkAccess($user['User']['id'], $this->params['controller'], 'exportExcel');
?>
<?php $tblName = "tbl" . rand(); ?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<script type="text/javascript">
    var oTableLaboList;
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        keyEvent();
        $("#<?php echo $tblName; ?> td:first-child").addClass('first');
        oTableLaboList = $("#<?php echo $tblName; ?>").dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo $this->base.'/'.$this->params['controller']; ?>/laboListAjax/"+ $("#checkValidate").val()+"/"+ $("#laboResultDate").val(),
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                $("#<?php echo $tblName; ?> td:first-child").addClass('first');
                $("#<?php echo $tblName; ?> td:last-child").css("white-space", "nowrap");                                
                
                // Action Reprint Invoice
                $(".btnPrintLabo").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    $("#dialog").html('<div class="buttons"><button type="submit" class="positive rePrintLabo" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="txtRePrintLabo"><?php echo ACTION_PRINT_LABO_RESULT; ?></span></button></div> ');
                    $(".rePrintLabo").click(function(){
                        $.ajax({
                            type: "POST",
                            //url: "<?php echo $this->base.'/'.$this->params['controller']; ?>/printLabo/"+id,
                            url: "<?php echo $this->base.'/'.$this->params['controller']; ?>/printLaboWithoutCategory/"+id,
                            beforeSend: function(){
                                $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                            },
                                    
                            success: function(printInvoiceResult) {
                                w = window.open();
                                w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                                w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                                w.document.write(printInvoiceResult);
                                w.document.close();
                                try
                                {
                                    //Run some code here                                                                                                       
                                    jsPrintSetup.setSilentPrint(1);
                                    jsPrintSetup.printWindow(w);
                                }
                                catch (err)
                                {
                                    //Handle errors here                                    
                                    w.print();
                                }
                                w.close();
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
                
                // Action Reprint Invoice
                $(".btnPrintLaboAfterPrint").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var labid = $(this).attr('lab');
                    $("#dialog").html('<div class="buttons"><button type="submit" class="positive rePrintLaboAfterPrint" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="txtRePrintLaboAfterPrint"><?php echo ACTION_PRINT; ?></span></button></div> ');
                    $(".rePrintLaboAfterPrint").click(function(){
                        $.ajax({
                            type: "POST",
                            url: "<?php echo $this->base.'/'.$this->params['controller']; ?>/printLaboAfterPrint/"+labid+'/'+id,
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
                
                $(".btnViewLaboList").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var name = $(this).attr('title');
                    $.ajax({
                        type: "GET",
                        url: "<?php echo $this->base.'/'.$this->params['controller']; ?>/view/" + id,
                        data: "",
                        beforeSend: function(){
                            $("#dialog").html('<p style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                        },
                        success: function(msg){
                            $("#dialog").html(msg);
                        }
                    });
                    $("#dialog").dialog({
                        title: name + ' Information',
                        resizable: false,
                        modal: true,
                        width: '90%',
                        height: 500,
                        buttons: {
                            Ok: function() {
                                $( this ).dialog( "close" );
                            }
                        }
                    });
                });
                
                $(".btnViewAfterPrint").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var name = $(this).attr('title');
                    var labid = $(this).attr('lab');
                    $.ajax({
                        type: "GET",
                        url: "<?php echo $this->base.'/'.$this->params['controller']; ?>/viewAfterPrint/" + labid + "/"+ id,
                        data: "",
                        beforeSend: function(){
                            $("#dialog").html('<p style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                        },
                        success: function(msg){
                            $("#dialog").html(msg);
                        }
                    });
                    $("#dialog").dialog({
                        title: name + ' Information',
                        resizable: false,
                        modal: true,
                        width: '90%',
                        height: 500,
                        buttons: {
                            Ok: function() {
                                $( this ).dialog( "close" );
                            }
                        }
                    });
                });
                
                $(".btnEditLaboList").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var leftPanel  = $(this).parent().parent().parent().parent().parent().parent().parent();
                    var rightPanel = leftPanel.parent().find(".rightPanel");
                    leftPanel.hide("slide", { direction: "left" }, 500, function() {
                        rightPanel.show();
                    });
                    rightPanel.html("<?php echo ACTION_LOADING; ?>");
                    rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/edit/" + id);
                });
                
                $(".btnEditLaboListAfterPrint").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var labid = $(this).attr('lab');
                    var leftPanel  = $(this).parent().parent().parent().parent().parent().parent().parent();
                    var rightPanel = leftPanel.parent().find(".rightPanel");
                    leftPanel.hide("slide", { direction: "left" }, 500, function() {
                        rightPanel.show();
                    });
                    rightPanel.html("<?php echo ACTION_LOADING; ?>");
                    rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/editAfterPrint/" + labid + "/"+ id);
                });
                
                $(".btnApproveLabo").click(function(event) {
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
                        open: function(event, ui) {
                            $(".ui-dialog-buttonpane").show();
                        },
                        buttons: {
                            '<?php echo ACTION_APPROVE; ?>': function() {
                                $.ajax({
                                    type: "GET",
                                    url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/approve/" + id,
                                    data: "",
                                    beforeSend: function() {
                                        $("#dialog").dialog("close");
                                        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                                    },
                                    success: function(result) {
                                        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");                                        
                                        // alert message
                                        if (result != '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>') {
                                            createSysAct('Labo', 'Approve', 2, result);
                                            $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                                        } else {
                                            createSysAct('Labo', 'Approve', 1, '');
                                            // alert message
                                            $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>' + result + '</p>');
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
                                        oCache.iCacheLower = -1;
                                        oTableLaboList.fnDraw(false);
                                    }
                                });
                            },
                            '<?php echo ACTION_CANCEL; ?>': function() {
                                $(this).dialog("close");
                            }
                        }
                    });
                });
                
                $(".btnDisApproveLabo").click(function(event) {
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var name = $(this).attr('name');
                    $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_CONFIRM_DISAPPROVE; ?> <b>' + name + '</b>?</p>');
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
                            '<?php echo ACTION_DISAPPROVE; ?>': function() {
                                $.ajax({
                                    type: "GET",
                                    url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/disapprove/" + id,
                                    data: "",
                                    beforeSend: function() {
                                        $("#dialog").dialog("close");
                                        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                                    },
                                    success: function(result) {
                                        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                        // alert message
                                        if (result != '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>') {
                                            createSysAct('Labo', 'Approve', 2, result);
                                            $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                                        } else {
                                            createSysAct('Labo', 'Approve', 1, '');
                                            // alert message
                                            $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>' + result + '</p>');
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
                                        oCache.iCacheLower = -1;
                                        oTableLaboList.fnDraw(false);
                                    }
                                });
                            },
                            '<?php echo ACTION_CANCEL; ?>': function() {
                                $(this).dialog("close");
                            }
                        }
                    });
                });
                
                return sPre;
            },
            "aoColumnDefs": [{
                "sType": "numeric", "aTargets": [ 0 ],
                "bSortable": false, "aTargets": [ 0,-1 ]
            }]
        }); 
        
        $('#laboResultDate').datepicker({
            dateFormat:'yy-mm-dd',
            changeMonth: true,
            changeYear: true
        }).unbind("blur");
        
        $("#clearDateLaboResult").click(function(){
            $('#laboResultDate').val('');
            resetFilterInvoice();
        });
        
        $("#reloadLaboResult").click(function(){
            resetFilterInvoice();
        });
        $("#checkValidate").change(function(){
            resetFilterInvoice();
        });
        
        
    });
    
    function resetFilterInvoice(){
        var Tablesetting = oTableLaboList.fnSettings();
        Tablesetting.sAjaxSource = "<?php echo $this->base . '/' . $this->params['controller']; ?>/laboListAjax/"+ $("#checkValidate").val()+"/"+ $("#laboResultDate").val();
        oCache.iCacheLower = -1;
        oTableLaboList.fnDraw(false);
    }
    
    function keyEvent(){
         $(".btnViewLaboList").unbind('click').unbind('keyup').unbind('keypress').unbind('change').unbind('blur');
    }
</script>
<div class="leftPanel">   
    <div id="dynamic">
        <div style="padding-top: 3px; padding-bottom: 3px; padding-left: 5px; padding-right: 5px; border: 1px dashed #bbbbbb; margin-bottom: 5px;" id="divHeader">
            <img src="<?php echo $this->webroot;?>img/icon/blood_test.png" />
                <?php __(MENU_LABO_RESULT); ?>
            <div class="buttons" style="float: right; margin-left: 10px;">                
                <button type="button" id="reloadLaboResult" class="positive">
                    <img src="<?php echo $this->webroot; ?>img/button/refresh-active.png" alt=""/>
                    <?php echo ACTION_REFRESH; ?>
                </button>
            </div>
            <div style="float: right; vertical-align: middle; ">
                <?php
                echo TABLE_DATE;
                ?>
                <input type="text" id="laboResultDate" style="font-size: 11px; height: 24px;" value="<?php echo date('Y-m-d'); ?>" />
                <img alt="" src="<?php echo $this->webroot; ?>img/button/clear.png" style="cursor: pointer; vertical-align: middle;" onmouseover="Tip('Clear Date')" id="clearDateLaboResult" />
                <label for="checkValidate"><?php echo TABLE_VALIDATE; ?></label>
                <select id="checkValidate" style="width: 100px; height: 30px;">
                    <option value="all"><?php echo TABLE_ALL; ?></option>
                    <option value="0"><?php echo ACTION_NO; ?></option>
                    <option value="1"><?php echo ACTION_YES; ?></option>
                </select>
            </div>
            <div style="clear: both;"></div>
        </div>
        <br/>
        <table id="<?php echo $tblName; ?>" class="table" cellspacing="0">
            <thead>
                <tr>
                    <th class="first"><?php echo TABLE_NO; ?></th>
                    <th><?php echo 'Lab Number'; ?></th>
                    <th><?php echo PATIENT_NAME; ?></th>
                    <th><?php echo TABLE_SEX; ?></th>                
                    <th><?php echo TABLE_TELEPHONE; ?></th>                
                    <th><?php echo OTHER_REQUESTED_DATE; ?></th>
                    <th><?php echo OTHER_RESULT_DATE; ?></th>
                    <th><?php echo TABLE_VALIDATE; ?></th>
                    <th><?php echo ACTION_ACTION; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="9" class="dataTables_empty"><?php echo TABLE_LOADING; ?></td>
                </tr>
            </tbody>
        </table>
    </div>    
</div>
<div class="rightPanel"></div>