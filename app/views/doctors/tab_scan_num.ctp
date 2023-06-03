<?php
if (empty($request_scans)) {
    echo GENERAL_NO_RECORD;
    exit();
}
require_once("includes/function.php");
?>
<?php $absolute_url = FULL_BASE_URL . Router::url("/", false); ?>
<?php echo $javascript->link('jquery.form'); ?>
<style type="text/css">
    div.checkbox{
        width: 30px;
    }
</style>
<?php $tblName = "tbl" . rand(); ?>
<script type="text/javascript">
    $(document).ready(function(){
        $(".RequestScanEditForm").validationEngine();
        $(".RequestScanEditForm").ajaxForm({
            dataType: 'json',
            beforeSubmit: function(arr, $form, options) {
                $(".loading").show();
            },
            success: function(result) {
                $(".loading").hide();
                $("#tabs3").tabs("select", 0);
                $("#tabScanNum").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabScanNum/<?php echo $this->params['pass'][0] . '/' . $this->params['pass'][1]; ?>");                                             
                $("#dialog").html('<div><br/><center><div class="buttons" style="display: inline-block;"><button type="submit" class="positive printRequestScanNum" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="txtPrintInvoice"><?php echo ACTION_PRINT; ?></span></button></div></center></div>');                
                $(".printRequestScanNum").click(function(){
                    $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printOtherService/" + result.requestScanId + "/" + result.queueDoctorId + "/" + result.queueId,
                        beforeSend: function(){
                            $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                        },
                        success: function(printPatientConsultResult){
                            w=window.open();
                            w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                            w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                            w.document.write(printPatientConsultResult);
                            w.document.close();
                            try
                            {
                                //Run some code here                                                                                                       
                                jsPrintSetup.setSilentPrint(1);
                                jsPrintSetup.printWindow(w);
                            }
                            catch(err)
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
                   title: '<?php echo ACTION_PRINT_DOCTOR_OTHER_SERVICE; ?>',
                   resizable: false,
                   modal: true,
                   width: 'auto',
                   height: '150',
                   position:'center',
                   closeOnEscape: true,
                   open: function(event, ui){
                       $(".ui-dialog-buttonpane").show(); $(".ui-dialog-titlebar-close").show();
                   },
                   close: function(){
                       $(this).dialog({close: function(){}});
                       $(this).dialog("close");
                       $(".btnBackRequestScan").dblclick();
                   },
                   buttons: {
                       '<?php echo ACTION_CLOSE; ?>': function() {
                           $("meta[http-equiv='refresh']").attr('content','0');
                           $(this).dialog("close");
                       }
                   }
               });
            }
        });
        
        $("#request_scan").accordion({
            collapsible: true,
            autoHeight: false,
            navigation: false,
            active: false
        });
        
        $(".btnPrint").click(function(event){
            event.preventDefault();
            $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            event.stopPropagation();
            var btnRequestScan=$("#dialogPrint<?php echo $tblName;?>").html();
            var requestScanId = $(this).attr('requestScanId');
            var queuedDoctorId = $(this).attr('queuedDoctorId');
            var queueId = $(this).attr('queueId');
            var name = $(this).attr('name');
            $("#requestScan").load("<?php echo $absolute_url . $this->params['controller']; ?>/printOtherService/" + requestScanId + "/" + queuedDoctorId + "/" + queueId);
            $("#dialogPrint<?php echo $tblName;?>").html(btnRequestScan);
            $("#dialogPrint<?php echo $tblName;?>").dialog({
                title: '<?php echo ACTION_PRINT_DOCTOR_OTHER_SERVICE; ?>',
                resizable: false,
                modal: true,                       
                buttons: {
                    Ok: function() {
                        $( this ).dialog( "close" );
                    }
                }
            });
            $("#btnRequestScan<?php echo $tblName;?>").click(function(){                        
                w=window.open();
                w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                w.document.write('<style type="text/css">.info th{font-size: 12px;}.info td{font-size: 12px;}.table th{font-size: 12px;}.table td{font-size: 12px;}</style>');
                w.document.write($("#requestScan").html());
                w.document.close();
                try
                {
                    //Run some code here                                                                                                       
                    jsPrintSetup.setSilentPrint(1);
                    jsPrintSetup.printWindow(w);
                }
                catch(err)
                {
                    //Handle errors here                                    
                    w.print();                                     
                } 
                w.close();
            });
        });
        
        $(".legend_content").show();
        $(".legend_title").click(function(){
            $(this).siblings(".legend_content").slideToggle();
        });
    });
</script>
<div id="request_scan">
    <?php
    $ind = 0; 
    foreach ($request_scans as $request_scan):
        ?>
        <h3>
            <a href="#">
                <?php echo date('d/m/Y H:i:s', strtotime($request_scan['RequestScan']['created'])); ?>
                <div style="float:right;">
                    <img alt="" requestScanId="<?php echo $request_scan['RequestScan']['id']?>" queuedDoctorId="<?php echo $request_scan['RequestScan']['queued_doctor_id'];?>" queueId="<?php echo $request_scan['Queue']['id'];?>" src="<?php echo $this->webroot; ?>img/button/printer1.png" class="btnPrint"  name="" onmouseover="Tip('<?php echo ACTION_PRINT; ?>')" />
                </div>
            </a>
        </h3>
        <div class="<?php echo $request_scan['RequestScan']['id']; ?>">
            <?php echo $this->Form->create('RequestScan', array('id' => 'RequestScanEditForm'.$request_scan['RequestScan']['id'], 'class' => 'RequestScanEditForm', 'rel' => $request_scan['RequestScan']['id'] , 'url' => '/doctors/editScan/' . $request_scan['RequestScan']['id'] . '/' . $request_scan['RequestScan']['queued_doctor_id'] . '/' . $request_scan['Queue']['id'], 'enctype' => 'multipart/form-data')); ?>
            <input name="data[QeuedDoctor][id]" type="hidden" value="<?php echo $request_scan['QeuedDoctor']['id']; ?>"/>
            <input name="data[Queue][id]" type="hidden" value="<?php echo $request_scan['Queue']['id']; ?>"/>
            <?php echo $this->Form->hidden('request_scan_id', array('label' => false, 'value' => $request_scan['RequestScan']['id'])); ?>
           <div class="legend">
                <input type="hidden" type="text" value="<?php echo $request_scan['RequestScan']['id']; ?>" name="data[RequestScan][id]"/>
                <div class="legend_title"><label for="ScanRequest"><b><?php echo MENU_SCAN; ?></b></label></div>
                <div class="legend_content">
                    <?php echo $this->Form->input('request', array('label' => false, 'type' => 'textarea', 'value' => $request_scan['RequestScan']['request'],'style'=>'width:99.5%')); ?>
                </div>
           </div>
           <br>
            <div class="buttons">
                <button type="submit" class="positive">
                    <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
                    <?php echo ACTION_SAVE; ?>
                </button>
                <img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" class="loading" style="display: none;" />
            </div>
            <div style="clear: both;"></div>
            <?php echo $this->Form->end(); ?>   
        </div>
        <?php $ind++; endforeach; ?>
</div>

<div id="dialog" title=""></div>
<div id="dialogPrint<?php echo $tblName;?>" title="" style="display: none;">
    <br />
    <center>
        <div class="buttons" style="display: inline-block;">
            <button type="button" id="btnRequestScan<?php echo $tblName;?>" class="positive">
                <img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/>
                <?php echo ACTION_PRINT; ?>
            </button>
        </div>
    </center>
</div>
<div id="requestScan" style="display: none;"></div>