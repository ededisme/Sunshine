<?php
if (empty($others)) {
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
<?php $tblName = "tbl123"; ?>
<script type="text/javascript">
    $(document).ready(function(){
        $(".OtherServiceRequestEditForm").validationEngine();
        $(".OtherServiceRequestEditForm").ajaxForm({
            dataType: 'json',
            beforeSubmit: function(arr, $form, options) {
                $(".loading").show();
            },
            success: function(result) {
                $(".loading").hide();
                $("#tabs3").tabs("select", 7);
                $("#tabEchoNum").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabOtherServiceNum/<?php echo $this->params['pass'][0] . '/' . $this->params['pass'][1]; ?>");                                             
                $("#dialog").html('<div><br/><center><div class="buttons" style="display: inline-block;"><button type="submit" class="positive printPatientOtherServiceNum" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="txtPrintInvoice"><?php echo ACTION_PRINT; ?></span></button></div></center></div>');                
                $(".printPatientOtherServiceNum").click(function(){
                    $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printOtherService/" + result.patientConsultId + "/" + result.queueDoctorId + "/" + result.queueId,
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
        
        $("#other").accordion({
            collapsible: true,
            autoHeight: false,
            navigation: false,
            active: false
        });
		
        $(".btnPrint").click(function(event){
            event.preventDefault();
            $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            event.stopPropagation();
            var btnPatientConsultation=$("#dialogPrint<?php echo $tblName;?>").html();
            var patientConsultationId = $(this).attr('patientConsultationId');
            var queuedDoctorId = $(this).attr('queuedDoctorId');
            var queueId = $(this).attr('queueId');
            var name = $(this).attr('name');
            $("#dialog").html('<div><br/><center><div class="buttons" style="display: inline-block;"><button type="submit" class="positive printOtherService" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span><?php echo ACTION_PRINT; ?></span></button></div></center></div>');
            $(".printOtherService").click(function () {
                $.ajax({
                    type: "POST",
                    url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printOtherService/" + patientConsultationId + "/" + queuedDoctorId + "/" + queueId,
                    beforeSend: function () {
                        $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner.gif');
                    },
                    success: function (printOtherServiceResult) {
                        w = window.open();
                        w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                        w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                        w.document.write(printOtherServiceResult);
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
                position: 'center',
                open: function (event, ui) {
                    $(".ui-dialog-buttonpane").show();
                },
                buttons: {
                    '<?php echo ACTION_CLOSE; ?>': function () {
                        $(this).dialog("close");
                    }
                }
            });
        });
        
        
        $(".legend_content").show();
        $(".legend_title").click(function(){
            $(this).siblings(".legend_content").slideToggle();
        });
    });
</script>
<div id="other">
    <?php
    $ind = 0; 
    foreach ($others as $other):
        ?>
        <h3>
            <a href="#">
                <?php echo date('d/m/Y H:i:s', strtotime($other['OtherServiceRequest']['created'])); ?>
                <div style="float:right;">
                    <img alt="" patientConsultationId="<?php echo $other['OtherServiceRequest']['id']?>" queuedDoctorId="<?php echo $other['OtherServiceRequest']['queued_doctor_id'];?>" queueId="<?php echo $other['Queue']['id'];?>" src="<?php echo $this->webroot; ?>img/button/printer.png" class="btnPrint"  name="" onmouseover="Tip('<?php echo ACTION_PRINT; ?>')" />
                </div>
            </a>
        </h3>
        <div class="<?php echo $other['OtherServiceRequest']['id']; ?>">
            <?php echo $this->Form->create('OtherServiceRequest', array('id' => 'OtherServiceRequestEditForm'.$other['OtherServiceRequest']['id'], 'class' => 'OtherServiceRequestEditForm', 'rel' => $other['OtherServiceRequest']['id'] , 'url' => '/doctors/editOtherService/' . $other['OtherServiceRequest']['id'] . '/' . $other['OtherServiceRequest']['queued_doctor_id'] . '/' . $other['Queue']['id'], 'enctype' => 'multipart/form-data')); ?>
            <input name="data[QeuedDoctor][id]" type="hidden" value="<?php echo $other['QeuedDoctor']['id']; ?>"/>
            <input name="data[Queue][id]" type="hidden" value="<?php echo $other['Queue']['id']; ?>"/>
            <?php echo $this->Form->hidden('other_id', array('label' => false, 'value' => $other['OtherServiceRequest']['id'])); ?>
            <div class="legend">
                <div class="legend_title"><label for="OtherServiceRequestEcho"><b><?php echo TABLE_ECHO_SERVICE; ?></b></label></div>
                <div class="legend_content">
                    <?php echo $this->Form->input('echo_description', array('label' => false, 'type' => 'textarea', 'value' => $other['EchoServiceRequest']['echo_description'],'style'=>'width:99%;')); ?><br>
                    <?php echo $this->Form->hidden('echo_id', array('label' => false, 'value' => $other['EchoServiceRequest']['id'])); ?>
                    <?php echo $this->Form->hidden('echo_description_old', array('label' => false, 'value' => $other['EchoServiceRequest']['echo_description'])); ?>
                    <?php
                    $queryDataEcho=  mysql_query("SELECT * FROM echo_services as EchoService WHERE EchoService.is_active=1 AND echo_service_queue_id=".$other['Queue']['id']);
                    while ($dataResult=  mysql_fetch_array($queryDataEcho)){
                    ?>
                    <span><?php echo TABLE_DATE;?></span><span style="padding-left:37px;"> : </span><span><?php echo dateShort($dataResult['echo_date']); ?></span><br>
                    <span><?php echo TABLE_DESCRIPTION;?> : <?php echo $dataResult['description']; ?></span><br>    
                    <span><?php echo TABLE_CONCLUSION;?> : <?php echo $dataResult['conclusion']; ?></span><br>    
                    <span><?php echo TABLE_IMAGE;?></span><span style="padding-left:37px;"> : </span>    
                    <span>
                        <?php
                    $queryImage=  mysql_query("SELECT * FROM echo_service_images as esim WHERE is_active=1 AND echo_srv_id=".$dataResult['id']);
                    if(@mysql_num_rows($queryImage)){
                        while ($dataImage=  mysql_fetch_array($queryImage)){ ?>
                        <img src="<?php echo $this->webroot; ?>/img/echo/<?php echo $dataImage['src_name']; ?>" alt="<?php echo $dataImage['src_name']; ?>" width="150px" height="100px" vspace='2px' style="margin-left:5px;">
                    <?php } 
                    }?>
                    </span>
                    <?php } ?>
                </div>
            </div>
            <br />
            <div class="legend">
                <div class="legend_title"><label for="OtherServiceRequestXray"><b><?php echo TABLE_XRAY_SERVICE; ?></b></label></div>
                <div class="legend_content">
                    <?php echo $this->Form->input('xray_description', array('label' => false, 'type' => 'textarea', 'value' => $other['XrayServiceRequest']['xray_description'],'style'=>'width:99%;')); ?><br>
                    <?php echo $this->Form->hidden('xray_id', array('label' => false, 'value' => $other['XrayServiceRequest']['id'])); ?>
                    <?php echo $this->Form->hidden('xray_description_old', array('label' => false, 'value' => $other['XrayServiceRequest']['xray_description'])); ?>
                    <?php
                    $queryDataXray=  mysql_query("SELECT * FROM xray_services as XrayService WHERE XrayService.is_active=1 AND xray_service_queue_id=".$other['Queue']['id']);
                    while ($dataResult=  mysql_fetch_array($queryDataXray)){
                    ?>
                        <table class="table">
                            <tr>
                                <th style="text-align: left;" class="first"><?php echo TABLE_DATE;?></th>
                                <th style="text-align: left;"><?php echo dateShort($dataResult['xray_date']); ?></th>
                            </tr>
                            <tr>
                                <td class="first"><?php echo TABLE_DESCRIPTION;?></td>
                                <td><?php echo $dataResult['description']; ?></td>
                            </tr>
                            <tr>
                                <td class="first"><?php echo TABLE_CONCLUSION;?></td>
                                <td><?php echo $dataResult['conclusion']; ?></td>
                            </tr>
                            <tr>
                                <td class="first"><?php echo TABLE_IMAGE;?></td>
                                <td>
                                    <?php
                                    $queryImage=  mysql_query("SELECT * FROM xray_service_images as xsim WHERE is_active=1 AND xray_srv_id=".$dataResult['id']);
                                    if(@mysql_num_rows($queryImage)){
                                        while ($dataImage=  mysql_fetch_array($queryImage)){ ?>
                                        <img src="<?php echo $this->webroot; ?>/img/x-ray/<?php echo $dataImage['src_name']; ?>" alt="<?php echo $dataImage['src_name']; ?>" width="150px" height="100px" vspace='2px' style="margin-left:5px;">
                                    <?php } 
                                    }?>
                                </td>
                            </tr>
                        </table>
                    <?php } ?>
                </div>
            </div>
            <br>
            <div class="legend" style="display:none;">
                <div class="legend_title"><label for="OtherServiceRequestCystoscopy"><b><?php echo TABLE_CYSTOSCOPY; ?></b></label></div>
                <div class="legend_content">
                    <?php echo $this->Form->input('cystoscopy_description', array('label' => false, 'type' => 'textarea', 'value' => $other['CystoscopyServiceRequest']['cystoscopy_description'],'style'=>'width:99%;')); ?><br>
                    <?php echo $this->Form->hidden('cystoscopy_id', array('label' => false, 'value' => $other['CystoscopyServiceRequest']['id'])); ?>
                    <?php echo $this->Form->hidden('cystoscopy_description_old', array('label' => false, 'value' => $other['CystoscopyServiceRequest']['cystoscopy_description'])); ?>                    
                    <?php
                    $queryDataCystoscopy=  mysql_query("SELECT * FROM cystoscopy_services WHERE is_active=1 AND cystoscopy_service_request_id = ".$other['CystoscopyServiceRequest']['id']);
                    while ($dataResult=  mysql_fetch_array($queryDataCystoscopy)){
                    ?>
                        <table style="width: 100%;padding-bottom: 50px">
                            <tr>
                                <td style="width: 25%;" valign="top"></td>
                                <td style="padding-left: 10px;">
                                    <div>
                                        <p>After urine culture sterilized on <span style="text-decoration: underline;"><?php echo date("d F Y", strtotime($dataResult['start_date'])); ?></span>,we perform an urethrocystocopy on <span style="text-decoration: underline;"><?php echo date("d F Y", strtotime($dataResult['end_date'])); ?></span></p>
                                    </div>                    
                                </td>
                            </tr>
                            <tr style="<?php if ($dataResult['urethra_img'] == "") { echo 'display:none;';}?>">
                                <td style="width: 25%;" valign="top">
                                   <img src="<?php echo $this->webroot; ?>/img/cystoscopy/<?php echo $dataResult['urethra_img']; ?>" alt="<?php echo $dataResult['urethra_img']; ?>" width=180px" height="80px" vspace='2px' style="margin-left:5px;">  
                                </td>
                                <td style="vertical-align: middle; padding-left: 10px;">
                                    <div style="text-orientation: initial;">
                                        <b style="font-family: 'Times New Roman' ; font-size: 12px;"><?php echo 'Urethra'; ?> :</b>
                                        <?php echo $dataResult['urethra']; ?>
                                    </div>                    
                                </td>
                            </tr>
                            <tr style="<?php if ($other['Patient']['sex'] == "F" && $dataResult['prostate_img'] == "") { echo 'display:none;';}?>">
                                <td valign="top">
                                   <img src="<?php echo $this->webroot; ?>/img/cystoscopy/<?php echo $dataResult['prostate_img']; ?>" alt="<?php echo $dataResult['prostate_img']; ?>" width=180px" height="80px" vspace='2px' style="margin-left:5px;">  
                                </td>
                                <td style="vertical-align: middle; padding-left: 10px;">
                                    <div>
                                        <b style="font-family: 'Times New Roman' ; font-size: 12px;"><?php echo 'Prostate'; ?> :</b>
                                        <?php echo $dataResult['prostate']; ?>
                                    </div>                    
                                </td>
                            </tr>
                            <tr style="<?php if ($dataResult['bladder_neck_img'] == "") { echo 'display:none;';}?>">
                                <td valign="top">
                                   <img src="<?php echo $this->webroot; ?>/img/cystoscopy/<?php echo $dataResult['bladder_neck_img']; ?>" alt="<?php echo $dataResult['bladder_neck_img']; ?>" width=180px" height="80px" vspace='2px' style="margin-left:5px;">  
                                </td>
                                <td style="vertical-align: middle; padding-left: 10px;">
                                    <div>
                                        <b style="font-family: 'Times New Roman' ; font-size: 12px;"><?php echo 'Bladder neck'; ?> :</b>
                                        <?php echo $dataResult['bladder_neck']; ?>
                                    </div>                    
                                </td>
                            </tr>
                            <tr style="<?php if ($dataResult['bladder_img'] == "") { echo 'display:none;';}?>">
                                <td valign="top">
                                   <img src="<?php echo $this->webroot; ?>/img/cystoscopy/<?php echo $dataResult['bladder_img']; ?>" alt="<?php echo $dataResult['bladder_img']; ?>" width=180px" height="80px" vspace='2px' style="margin-left:5px;">  
                                </td>
                                <td style="vertical-align: middle; padding-left: 10px;">
                                    <div>
                                        <b style="font-family: 'Times New Roman' ; font-size: 12px;"><?php echo 'Bladder'; ?> :</b>
                                        <?php echo $dataResult['bladder']; ?>
                                    </div>                    
                                </td>
                            </tr>
                            <tr style="<?php if ($other['Patient']['sex'] == "M" && $dataResult['after_five_minute_img'] == "") { echo 'display:none;';}?>">
                                <td valign="top">
                                   <img src="<?php echo $this->webroot; ?>/img/cystoscopy/<?php echo $dataResult['after_five_minute_img']; ?>" alt="<?php echo $dataResult['after_five_minute_img']; ?>" width=180px" height="80px" vspace='2px' style="margin-left:5px;">  
                                </td>
                                <td style="vertical-align: middle; padding-left: 10px;">
                                    <div>
                                        <b style="font-family: 'Times New Roman' ; font-size: 12px;"><?php echo 'After 5 minutes <br/>cysto-hydrodistention'; ?> :</b>
                                        <?php echo $dataResult['after_five_minute']; ?>
                                    </div>                    
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td style="padding-left: 10px;">
                                    <div>
                                        <b style="font-family: 'Times New Roman' ; font-size: 12px;"><?php echo TABLE_CONCLUSION;?> :</b>
                                        <?php echo $dataResult['conclusion']; ?>
                                    </div>
                                </td
                            </tr>
                        </table>
                    <?php } ?>
                    
                </div>
            </div>
            <br />
            <div class="legend" style="display:none;">
                <div class="legend_title"><label for="OtherServiceRequestMidWife"><b><?php echo TABLE_MID_WIFE_SERVICE; ?></b></label></div>
                <div class="legend_content">
                    <?php echo $this->Form->input('mid_wife_description', array('label' => false, 'type' => 'textarea', 'value' => $other['MidWifeServiceRequest']['mid_wife_description'],'style'=>'width:99%;')); ?>
                    <?php echo $this->Form->hidden('mid_wife_id', array('label' => false, 'value' => $other['MidWifeServiceRequest']['id'])); ?>
                    <?php echo $this->Form->hidden('mid_wife_description_old', array('label' => false, 'value' => $other['MidWifeServiceRequest']['mid_wife_description'])); ?>
                    <?php
                    $queryDataMidWife=  mysql_query("SELECT * FROM mid_wife_services as MidWifeService WHERE MidWifeService.is_active=1 AND mid_wife_service_queue_id=".$other['Queue']['id']);
                    while ($dataResult=  mysql_fetch_array($queryDataMidWife)){
                    ?>
                    <br>
                    <fieldset>
                    <legend><?php __(MENU_MID_WIFE_SERVICE_INFO); ?></legend>
                        <fieldset>
                            <legend><?php __(MENU_STORY_PATIENTS); ?></legend>
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width:10%;"><?php echo TABLE_LAST_MENSTRUATION_PERIOD; ?></td>
                                    <td style="width:3%;">:</td>
                                    <td style="width:20%;"><?php echo dateShort($dataResult['last_mentstruation_period']); ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><?php echo TABLE_ESTIMATE_DELIVERY_DATE; ?></td>
                                    <td style="width:3%;">:</td>
                                    <td><?php echo dateShort($dataResult['estimate_delivery_date']); ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><?php echo TABLE_ECHO; ?></td>
                                    <td>:</td>
                                    <td><?php echo dateShort($dataResult['echo']); ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="width:10%;"><?php echo TABLE_WEIGHT; ?></td>
                                    <td>:</td>
                                    <td style="width:20%;"><?php echo $dataResult['weight']; ?></td>
                                    <td style="width:10%;"><?php echo TABLE_HEIGHT; ?></td>
                                    <td style="width:3%;">:</td>
                                    <td style="width:20%;"><?php echo $dataResult['height']; ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><?php echo TABLE_GESTRATION; ?></td>
                                    <td>:</td>
                                    <td><?php echo $dataResult['gestation']; ?></td>
                                    <td><?php echo TABLE_BABY; ?></td>
                                    <td>:</td>
                                    <td><?php echo $dataResult['baby']; ?></td>
                                    <td></td>
                                </tr>
                            </table>
                        </fieldset><br>
                        <fieldset>
                            <legend><?php __(MENU_PATIENT_STORY_SEE); ?></legend>
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width:8%;"><?php __(MENU_PATIENT_STORY_SEE); ?></td>
                                </tr>
                                <tr>
                                    <td style="width:8%;"></td>
                                    <td style="width:8%;"><?php echo TABLE_ABORTION; ?></td>
                                    <td style="width:2%;">:</td>
                                    <td style="width:10%;"><?php echo $dataResult['abortion']; ?></td>
                                    <td style="width:10%;"><?php echo TABLE_INTERUPTION_VOLONTAIN; ?></td>
                                    <td style="width:2%;">:</td>
                                    <td style="width:10%;"><?php echo $dataResult['interuption_volontain']; ?></td>
                                    <td style="width:52%;" colspan="4"></td>
                                </tr>
                                <tr>
                                    <td><?php __(MENU_ACCON_CHEMENT); ?></td>    
                                </tr>
                                <tr>
                                    <td style="width:8%;"></td>
                                    <td style="width:10%;"><?php echo TABLE_BIRTH; ?></td>
                                    <td style="width:2%;">:</td>
                                    <td style="width:15%;"><?php echo $dataResult['birth']; ?></td>
                                    <td style="width:15%;"><?php echo MENU_NEE_MOIT; ?></td>
                                    <td style="width:2%;">:</td>
                                    <td style="width:15%;"><?php echo $dataResult['nee_moit']; ?></td>
                                    <td style="width:15%;"><?php echo MENU_MOIT_NEE; ?></td>
                                    <td style="width:2%;">:</td>
                                    <td style="width:10%;"><?php echo $dataResult['mort_nee']; ?></td>
                                    <td style=""></td>
                                </tr>
                                <tr>
                                    <td><?php __(MENU_ACCONCHEMENT_RERME); ?></td>    									
                                </tr> 
                                <tr>
                                    <td></td>
                                    <td><?php echo TABLE_ACCONCHEM_NORMAL; ?></td>
                                    <td style="width:2%;">:</td>
                                    <td style="width:10%;"><?php echo $dataResult['acconchement_normal']; ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td><?php echo MENU_ANORMAT; ?></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td><?php echo TABLE_CAESAREAN; ?></td>
                                    <td style="width:2%;">:</td>
                                    <td style="width:10%;"><?php echo $dataResult['caesarean']; ?></td>
                                    <td><?php echo TABLE_ACC_PAR_VENTONSE; ?></td>
                                    <td style="width:2%;">:</td>
                                    <td style="width:10%;"><?php echo $dataResult['acc_par_ventonse']; ?></td>
                                    <td></td>
                                </tr>
                            </table>
                        </fieldset><br>
                        <fieldset>
                            <legend><?php __(PATIENT_STORY_BEFORE); ?></legend>
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width:15%;"><?php __(PATIENT_MADIE_DES_REINS); ?></td>
                                    <td style="width:2%;">:</td>
                                    <td style="width:10%;">
                                        <?php 
                                        if($dataResult['edema']==1){
                                            echo $this->Form->input('edema',array('type'=>'checkbox','name'=>'data[MidWifeService][edema]','value'=>'1','label'=>FALSE,'checked'=>TRUE,'disabled' => "disabled"));
                                        }else{
                                            echo $this->Form->input('edema',array('type'=>'checkbox','name'=>'data[MidWifeService][edema]','value'=>'1','label'=>FALSE,'disabled' => "disabled"));
                                        }
                                        ?>
                                        <?php echo TABLE_EDEMA; ?>
                                    </td>

                                    <td style="width:10%;">
                                        <?php 
                                        if($dataResult['albuminuria']==1){
                                            echo $this->Form->input('albuminuria',array('type'=>'checkbox','name'=>'data[MidWifeService][albuminuria]','value'=>'1','label'=>FALSE,'checked'=>TRUE,'disabled' => "disabled"));
                                        }else{
                                            echo $this->Form->input('albuminuria',array('type'=>'checkbox','name'=>'data[MidWifeService][albuminuria]','value'=>'1','label'=>FALSE,'disabled' => "disabled"));
                                        }
                                        ?>
                                        <?php echo TABLE_ALBUMINURIA; ?>
                                    </td>
                                    <td style="width:51%;"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php 
                                        if($dataResult['cadiojathie']==1){
                                            echo $this->Form->input('cadiojathie',array('type'=>'checkbox','name'=>'data[MidWifeService][cadiojathie]','value'=>'1','label'=>FALSE,'checked'=>TRUE,'disabled' => "disabled"));
                                        }else{
                                            echo $this->Form->input('cadiojathie',array('type'=>'checkbox','name'=>'data[MidWifeService][cadiojathie]','value'=>'1','label'=>FALSE,'disabled' => "disabled"));
                                        }
                                        ?>
                                        <?php echo TABLE_CADIOJATHIE; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php 
                                        if($dataResult['asthma']==1){
                                            echo $this->Form->input('asthma',array('type'=>'checkbox','name'=>'data[MidWifeService][asthma]','value'=>'1','label'=>FALSE,'checked'=>TRUE,'disabled' => "disabled"));
                                        }else{
                                            echo $this->Form->input('asthma',array('type'=>'checkbox','name'=>'data[MidWifeService][asthma]','value'=>'1','label'=>FALSE,'disabled' => "disabled"));
                                        }
                                        ?>
                                        <?php echo TABLE_ASTHMA; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo TABLE_OTHER; ?></td>
                                    <td>:</td>
                                    <td colspan="5"><?php echo $dataResult['other']; ?></td>
                                </tr>
                            </table>
                        </fieldset>
                    </fieldset>
                    <?php } ?>
                </div>
            </div>
            <br />
            <div class="buttons">
                <button type="submit" class="positive">
                    <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
                    <?php echo ACTION_SAVE; ?>
                </button>
                <img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" class="loading" style="display: none;" />
            </div>
            <div style="clear: both;"></div>

            <?php echo $this->Form->end(); ?>   
        </div>
        <?php
        $ind++;
    endforeach;
    ?>
</div>
<div id="dialog" title=""></div>
<div id="dialogPrint<?php echo $tblName;?>" title="" style="display: none;">
    <br />
    <center>
        <div class="buttons" style="display: inline-block;">
            <button type="button" id="btnPatientConsultation<?php echo $tblName;?>" class="positive">
                <img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/>
                <?php echo ACTION_PRINT; ?>
            </button>
        </div>
    </center>
</div>
<div id="patientConsultation" style="display: none;"></div>