<?php $absolute_url  = FULL_BASE_URL . Router::url("/", false); ?>
<?php 
include("includes/function.php");
// Authentication
$this->element('check_access');
$allowAdd=checkAccess($user['User']['id'], $this->params['controller'], 'add');
?>
<?php $tblName = "tbl" . rand(); ?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<script type="text/javascript">
    var oTablePatientPediatrict;
    var patientPediatricId  = $(".ui-tabs-selected a").attr("href");
    var tabSalesReg = '';
    $(document).ready(function(){   
        $('#tabs a').not("[href=#]").each(function() {
            if($.data(this, 'href.tabs')=="<?php echo $this->base; ?>/patients/index"){
                $("#tabs").tabs("remove", $(this).attr("href"));
            }
        });
        oTablePatientPediatrict = $("#<?php echo $tblName; ?>").dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo $this->base.'/'.$this->params['controller']; ?>/pediatricAjax/",
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                $("#<?php echo $tblName; ?> td:first-child").addClass('first');
                $("#<?php echo $tblName; ?> td:last-child").css("white-space", "nowrap");
                
                $(".btnViewPatientPediatric").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var name = $(this).attr('name');
                    var leftPanel=$(this).parent().parent().parent().parent().parent().parent().parent();
                    var rightPanel=leftPanel.parent().find(".rightPanel");
                    leftPanel.hide("slide", { direction: "left" }, 500, function() {
                        rightPanel.show();
                    });
                    rightPanel.html("<?php echo ACTION_LOADING; ?>");
                    rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/viewPediatric/" + id);                                                            
                });                                               
                
                $(".btnEditPatientPediatric").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var leftPanel  = $(this).parent().parent().parent().parent().parent().parent().parent();
                    var rightPanel = leftPanel.parent().find(".rightPanel");
                    leftPanel.hide("slide", { direction: "left" }, 500, function() {
                        rightPanel.show();
                    });
                    rightPanel.html("<?php echo ACTION_LOADING; ?>");
                    rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/editPediatric/"+id);
                    
                });
                
                $(".btnDeletePatientPediatric").click(function(event){
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
                                    url: "<?php echo $this->base.'/'.$this->params['controller']; ?>/deletePediatric/" + id,
                                    data: "",
                                    beforeSend: function(){
                                        $("#dialog").dialog("close");
                                        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                                    },
                                    success: function(result){
                                        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                        oCache.iCacheLower = -1;
                                        oTablePatientPediatrict.fnDraw(false);
                                        // alert message
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
                });
                
                $(".btnReturn").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var name = $(this).attr('title');
                    $.ajax({
                        type: "GET",
                        url: "<?php echo $absolute_url.$this->params['controller']; ?>/returnPatient/pediatric",
                        data: "id=" + id,
                        beforeSend: function(){
                            $("#dialog").html('<p style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                        },
                        success: function(msg){
                            $("#dialog").html(msg);
                        }
                    });
                    $("#dialog").dialog({
                        title: name,
                        resizable: false,
                        modal: true,
                        width: '500',
                        height: '315',
                        buttons: {
                            Cancel: function() {
                                $( this ).dialog( "close" );
                            }
                        }
                    });
                });
                
                
                // Action Reprint Invoice
                $(".btnPrintPatientPediatric").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    $("#dialog").html('<div class="buttons"><button type="submit" class="positive printPediatricForm" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="txtReprintInvoiceSales"><?php echo ACTION_PRINT_PATIENT_INFORMATION; ?></span></button></div> ');
                    $(".printPediatricForm").click(function(){
                        $.ajax({
                            type: "POST",
                            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printPediatricForm/"+id,
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
                
                return sPre;
            },
            "aoColumnDefs": [{
                "sType": "numeric", "aTargets": [ 0 ],
                "bSortable": false, "aTargets": [ 0,-1 ]
            }]
        });
        
        $(".btnAddPatientPediatric").click(function(event){
            event.preventDefault();
            var leftPanel=$(this).parent().parent().parent();
            var rightPanel=leftPanel.parent().find(".rightPanel");
            leftPanel.hide("slide", { direction: "left" }, 500, function() {
                rightPanel.show();
            });
            rightPanel.html("<?php echo ACTION_LOADING; ?>");
            rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/addPediatric/");
        });
        
        
    });
</script>


<div class="leftPanel">
    <div style="padding: 5px;border: 1px dashed #bbbbbb;">
        <?php if($allowAdd){ ?>
        <div class="buttons">
            <a href="" class="positive btnAddPatientPediatric">
                <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
                <?php echo MENU_PEDIATRIC_PATIENT_MANAGEMENT_ADD; ?>
                &nbsp;&nbsp;<img src="<?php echo $this->webroot; ?>img/button/patient.png" alt=""/>
            </a>
        </div>
        <?php } ?>        
        <div style="clear: both;"></div>
    </div>
    <br />
    <div id="dynamic">
        <table id="<?php echo $tblName; ?>" class="table" cellspacing="0">
            <thead>
                <tr>
                    <th class="first"><?php echo TABLE_NO; ?></th>
                    <th><?php echo PATIENT_CODE; ?></th>
                    <th><?php echo PATIENT_NAME; ?></th>                
                    <th><?php echo TABLE_SEX; ?></th>
                    <th><?php echo TABLE_DOB; ?></th>
                    <th><?php echo TABLE_FATHER_NAME; ?></th>
                    <th><?php echo TABLE_MOTHER_NAME; ?></th>                
                    <th style="width: 650px !important;"><?php echo ACTION_ACTION; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="8" class="first dataTables_empty"><?php echo TABLE_LOADING; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <br />
    <br />
    <div style="padding: 5px;border: 1px dashed #bbbbbb;">
        <?php if($allowAdd){ ?>
        <div class="buttons">
            <a href="" class="positive btnAddPatientPediatric">
                <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
                <?php echo MENU_PEDIATRIC_PATIENT_MANAGEMENT_ADD; ?>
                &nbsp;&nbsp;<img src="<?php echo $this->webroot; ?>img/button/patient.png" alt=""/>
            </a>
        </div>
        <?php } ?>        
        <div style="clear: both;"></div>
    </div>
</div>
<div class="rightPanel"></div>