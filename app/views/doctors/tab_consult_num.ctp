<?php
if (empty($consultation)) {
    echo GENERAL_NO_RECORD;
    exit();
} 
require_once("includes/function.php");
?>
<?php 
    $absolute_url = FULL_BASE_URL . Router::url("/", false); 
    $tblName = "tbl123"; 
?>
<?php echo $javascript->link('jquery.form'); ?>
<style type="text/css" media="screen">
    div.checkbox{
        width: 180px;
    }
    .table_print_labo td{ 
        border: none !important;
        line-height: 20px;
        padding: 0;
        margin: 0;
    }
    .legend_title{
        background: #3C69AD !important;
    }
    div.legend div.legend_content {
        border-left: 1px solid #3C69AD !important;
        border-right: 1px solid #3C69AD !important;
        border-bottom: 1px solid #3C69AD !important;
    }
</style>
<?php 
$tblName = "tbl123"; 
$tblRand = "tbl" . rand();
?>
<script type="text/javascript">
    $(document).ready(function(){     
        
        $('#PatientConsultationNumAppDate').datepicker({
            changeMonth: true,
            changeYear: true,
            showSecond: false,
            dateFormat:'dd/mm/yy'            
        });
        
        $(".viewLaboResult").dblclick(function(event) {
            event.preventDefault();
            event.stopPropagation(); 
            var id = $(this).attr('rel');
            var name = $(this).attr('title');
            if(id!=""){
                $.ajax({
                    type: "GET",
                    url: "<?php echo $absolute_url.$this->params['controller']; ?>/viewLaboResult/" + id,
                    data: "",
                    beforeSend: function(){
                        $("#dialogPreviewLaboResult").html('<p style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                    },
                    success: function(msg){
                        $("#dialogPreviewLaboResult").html(msg);
                    }
                });
                $("#dialogPreviewLaboResult").dialog({
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
            } 
        });
        $(".chzn-select").chosen();
        $(".PatientConsultationEditForm").validationEngine();
        $(".PatientConsultationEditForm").ajaxForm({
            dataType: 'json',
            beforeSubmit: function(arr, $form, options) {
                $(".loading").show();
            },
            beforeSerialize: function($form, options) {                
                $("#PatientConsultationNumAppDate").datepicker("option", "dateFormat", "yy-mm-dd");                
            },
            success: function(result) {
                $(".loading").hide();
                $("#tabs3").tabs("select", 1);
                $("#tabConsultNum<?php echo $tblName;?>").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabConsultNum/<?php echo $this->params['pass'][0] . '/' . $this->params['pass'][1]; ?>");                                             
                $("#dialog").html('<div><br/><center><div class="buttons" style="display: inline-block;"><button type="submit" class="positive printPatientConsultForm" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="txtPrintInvoice"><?php echo ACTION_PRINT_MEDICAL_REPORT; ?></span></button><button type="submit" class="positive printMedicalCertificateForm" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="txtPrintMedicalCertificate"><?php echo ACTION_PRINT_MEDICAL_CERTIFICATE; ?></span></button></div></center></div>');
                $(".printPatientConsultForm").click(function() {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printRecord/" + result.patientConsultId + "/" + result.queueDoctorId + "/" + result.queueId,
                        beforeSend: function() {
                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner.gif');
                        },
                        success: function(printInvoiceResult) {
                            w = window.open();
                            w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                            w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                            w.document.write(printInvoiceResult);
                            w.document.close();
                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                        }
                    });
                });  
                $(".printMedicalCertificateForm").click(function() {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printMedicalCertificate/" + result.patientConsultId + "/" + result.queueDoctorId + "/" + result.queueId,
                        beforeSend: function() {
                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner.gif');
                        },
                        success: function(printInvoiceResult) {
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
                    open: function(event, ui) {
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
        
        $('.PatientConsultationConsultStatus').change(function(){
            var consultStatus = $(this).val(); 
            var rel = $(this).attr("rel"); 
            if(consultStatus == 2){
                $("#checkRoomId"+rel).show();
            }else {
                $("#checkRoomId"+rel).hide();
                $("#PatientConsultationCheckRoomId"+rel).val("");
           }
        });
        
        $(".legend_content").show();
        $(".legend_title").click(function(){
            $(this).siblings(".legend_content").slideToggle();
        });
        
        $("#consultation").accordion({
            collapsible: true,
            autoHeight: false,
            navigation: false,
            active: false
        });
        
        $(".followup").accordion({
            collapsible: true,
            autoHeight: false,
            navigation: true,
            active: false
        });
        
        $('#PatientConsultationDateFirstComplaint, #PatientConsultationDateOfConsult').datepicker(
        {
            changeMonth: true,
            changeYear: true,
            showSecond: false,
            dateFormat:'yy-mm-dd'            
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
            $("#dialog").html('<div><br/><center><div class="buttons" style="display: inline-block;"><button type="submit" class="positive printPatientConsultForm" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="txtPrintInvoice"><?php echo ACTION_PRINT_MEDICAL_REPORT; ?></span></button><button type="submit" class="positive printMedicalCertificateForm" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="txtPrintMedicalCertificate"><?php echo ACTION_PRINT_MEDICAL_CERTIFICATE; ?></span></button></div></center></div>');            
            $(".printPatientConsultForm").click(function() {
                $.ajax({
                    type: "POST",
                    url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printRecord/" + patientConsultationId + "/" + queuedDoctorId + "/" + queueId,
                    beforeSend: function() {
                        $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner.gif');
                    },
                    success: function(printInvoiceResult) {
                        w = window.open();
                        w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                        w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                        w.document.write(printInvoiceResult);
                        w.document.close();
                        $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    }
                });
            });                  
            $(".printMedicalCertificateForm").click(function() {
                $.ajax({
                    type: "POST",
                    url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printMedicalCertificate/" + patientConsultationId + "/" + queuedDoctorId + "/" + queueId,
                    beforeSend: function() {
                        $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner.gif');
                    },
                    success: function(printInvoiceResult) {
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
                position: 'center',
                open: function(event, ui) {
                    $(".ui-dialog-buttonpane").show();
                },
                buttons: {
                    '<?php echo ACTION_CLOSE; ?>': function() {
                        $(this).dialog("close");
                    }
                }                                                    
            });                        
        });
        
        /**
        * This script use for add new follow up using ajax to patient history by patient_history_id
        */
        
        $(".btnFollowup").click(function() {
            var patientConsultationId = this.name;
            var queueId = $(this).attr('title');
            var queuedDoctorId = $(this).attr('queuedDoctorId');
            
            $.ajax({
                type: "GET",
                url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/followup/",
                data: "",
                beforeSend: function(){
                    $("#dialog9").html('<p id="loading" style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                },
                success: function(msg){
                    $("#loading").hide();
                    $("#dialog9").html(msg);
                }
            });
            
            $("#dialog9").dialog({
                title: '<?php echo MENU_FOLLOW_UP_LABEL_ADD; ?>',
                resizable: false,
                modal: true,
                width: '70%',
                height: 500,
                buttons: {
                    "<?php echo ACTION_SAVE; ?>": function() {
                        var isFormValidated = $("#PatientFollowupAddForm").validationEngine('validate');
                        var tabId = $("#tab_id").val();
                        if(!isFormValidated){
                            return false;
                        }else{
                            var url = "<?php echo $this->base . '/' . $this->params['controller']; ?>/addNewFollowUp/" + patientConsultationId + '/' + queuedDoctorId + '/' + queueId;
                            var post = $('#PatientFollowupAddForm').serialize();
                            $.post(url,post,function(rs){
                                if(tabId==2){
                                    if(rs.indexOf('1')!=-1){
                                        $("#tabs2").tabs("select", 0);
                                        $("#tabConsultNum").load("<?php echo $absolute_url.$this->params['controller']; ?>/tabConsultNum/" + queuedDoctorId + '/' + queueId);
                                    } 
                                }else{
                                    if(rs.indexOf('1')!=-1){
                                        $("#tabs3").tabs("select", 0);
                                        $("#tabs3").tabs("select", 1);
                                        $("#tabConsultNum<?php echo $tblName; ?>").load("<?php echo $absolute_url.$this->params['controller']; ?>/tabConsultNum/" + queuedDoctorId + '/' + queueId);
                                    } 
                                }
                            });
                            $(this).dialog("close");
                        }
                    }
                }
            });
            return false;
        });   
     
    });
</script>
<?php    
    $resultImmuzation = array();    
    $queryPrscription = mysql_query("SELECT * FROM orders WHERE orders.status > 0 AND orders.patient_id = {$patientId}");
    if(mysql_num_rows($queryPrscription)){
        while ($resultPrescription = mysql_fetch_array($queryPrscription)) {        
            $queryPrscriptionDetail = mysql_query("SELECT order_details.order_id FROM order_details "
                    . "INNER JOIN products ON products.id = order_details.product_id "
                    . "INNER JOIN product_pgroups ON product_pgroups.product_id = products.id "
                    . "INNER JOIN uoms ON uoms.id = order_details.qty_uom_id "
                    . "WHERE order_details.order_id = {$resultPrescription['id']} AND pgroup_id = 2 GROUP BY order_details.order_id");
            while ($orderDetail = mysql_fetch_array($queryPrscriptionDetail)) {
                $resultImmuzation[] = $orderDetail['order_id'];
            }
       }
    }
?>
<div id="consultation">
    <?php
    $ind = 1;
    $date1 = date("Y-m-d");    
    foreach ($consultation as $consultation):
        $display = "";
        $disabled = "";
        $date2 = date('Y-m-d', strtotime($consultation['PatientConsultation']['created']));        
        if(strtotime($date1) > strtotime($date2)){
            $display = "display:none;";
            $disabled = "disabled";
        }
        ?>
        <h3>
            <a href="#">
                <?php echo "# : "; ?>
                <?php echo $consultation['PatientConsultation']['consultation_code'] . ' - ' . date('d/m/Y H:i:s', strtotime($consultation['PatientConsultation']['created'])); ?>
                <div style="float:right;">
                    <img alt="" patientConsultationId="<?php echo $consultation['PatientConsultation']['id']?>" queuedDoctorId="<?php echo $consultation['PatientConsultation']['queued_doctor_id'];?>" queueId="<?php echo $consultation['Queue']['id'];?>" src="<?php echo $this->webroot; ?>img/button/printer.png" class="btnPrint"  name="<?php echo $consultation['PatientConsultation']['consultation_code']; ?>" onmouseover="Tip('<?php echo ACTION_PRINT; ?>')" />
                </div>
            </a>
        </h3>
        <div class="<?php echo $consultation['PatientConsultation']['id']; ?>">
            <?php echo $this->Form->create('PatientConsultation', array('id' => 'PatientConsultationEditForm'.$consultation['PatientConsultation']['id'], 'class' => 'PatientConsultationEditForm', 'rel' => $consultation['PatientConsultation']['id'] , 'url' => '/doctors/editConsult/' . $consultation['PatientConsultation']['id'] . '/' . $consultation['PatientConsultation']['queued_doctor_id'] . '/' . $consultation['Queue']['id'], 'enctype' => 'multipart/form-data')); ?>
            <input type="hidden" type="text" id="patient_id" name="patient_id" value="<?php echo $consultation['Patient']['id']; ?>" />
            <input name="data[PatientConsultation][consultation_code]" type="hidden" value="<?php echo $consultation['PatientConsultation']['consultation_code']; ?>"/>
            <input name="data[QeuedDoctor][id]" type="hidden" value="<?php echo $consultation['QeuedDoctor']['id']; ?>"/>
            <input name="data[Queue][id]" type="hidden" value="<?php echo $consultation['Queue']['id']; ?>"/>
            <input id="link_url" type="hidden" value="<?php echo $absolute_url . $this->params['controller']; ?>"/>                        
            <div style="width: 100%;">
                <!-- Doctor -->
                <div class="legend" style="width: 32.5%; float: left; padding: 2px; display: none;">
                    <div class="legend_title"><label for="ConsultationDateFirstComplaint"><b><?php echo MENU_PATIENT_MANAGEMENT_HISTORY; ?></b></label></div>
                    <div class="legend_content" style="height: 100px;">
                        <table style="width: 100%">
                            <tr style="display: none;">
                                <td style="width: 25%;"><labe for="DoctorConsultationName"><?php echo DOCTOR_NAME; ?> :</label></td>
                                <td>
                                    <?php echo $this->Form->input('doctor_consultation_ids', array('label' => false, 'data-placeholder' => INPUT_SELECT, 'style'=>'width: 100%;', 'selected' => $consultation['DoctorConsultation']['id'])); ?>
                                </td>
                            </tr>
                        </table>        
                    </div>
                </div>
                <!-- Vital Sign  -->
                <div class="legend" style="width: 32.5%; float: left; padding: 2px;">
                    <div class="legend_title"><label for="ConsultationDaignostic"><b><?php echo MENU_VITAL_SING; ?></b></label></div>
                    <div class="legend_content" style="height: 100px;">
                        <fieldset>
                            <legend><?php __(MENU_VITAL_SING); ?></legend>
                            <table style="width: 100%;" cellspacing="3">
                                <tr>
                                    <td style="width: 15%;"><label for="PatientVitalSignHeight"><?php echo TABLE_HEIGHT; ?></label></td>
                                    <td style="width: 20%;">: <?php echo $consultation['PatientVitalSign']['height']; ?> cm</td>
                                    <td style="width: 15%;"><label for="PatientVitalSignWeight"><?php echo TABLE_WEIGHT; ?></label></td>
                                    <td style="width: 15%;">: <?php echo $consultation['PatientVitalSign']['weight']; ?> kg</td>
                                    <td style="width: 10%;"><label for="PatientVitalSignBMI"><?php echo TABLE_BMI; ?></label></td>
                                    <td style="width: 20%;" class="BMI">: 
                                        <?php
                                        if($consultation['PatientVitalSign']['height']>0 && $consultation['PatientVitalSign']['weight']>0){
                                            echo $consultation['PatientVitalSign']['BMI'];
                                        }
                                        ?>
                                    </td>            
                                </tr>
                                <tr>
                                    <td style="width: 15%;"><label for="PatientVitalSignPulse"><?php echo TABLE_PULSE; ?></label></td>
                                    <td style="width: 20%;">: <?php echo $consultation['PatientVitalSign']['pulse']; ?> /mn</td>
                                    <td style="width: 15%;"><label for="PatientVitalSignRespiratory"><?php echo TABLE_RESPIRATORY; ?></label></td>
                                    <td style="width: 20%;" colspan="3">: <?php echo $consultation['PatientVitalSign']['respiratory']; ?> /m</td>                     
                                </tr>
                                <tr>
                                    <td style="width: 15%;"><label for="PatientVitalSignTemperature"><?php echo TABLE_TEMPERATURE; ?></label></td>
                                    <td style="width: 20%;">: <?php echo $consultation['PatientVitalSign']['temperature']; ?> °C</td>
                                    <td style="width: 15%;"><label for="PatientVitalSignSop2"><?php echo TABLE_SOP2; ?></label></td>
                                    <td style="width: 20%;" colspan="3">: <?php echo $consultation['PatientVitalSign']['sop2']; ?></td> 
                                </tr>
                                <tr style="display: none;">
                                    <td style="width: 15%;"><label for="PatientVitalSignTemperature">Description</label></td>
                                    <td style="width: 20%;" colspan="5">: <?php  echo $consultation['PatientVitalSign']['other_info']; ?> </td>
                                </tr>
                            </table>      
                        </fieldset>
                        <fieldset style="display: none;">
                            <legend><?php __(MENU_BLOOD_PRESSURE); ?></legend>
                            <table class="table" style="width: 100%;">
                                <tr>
                                    <th class="first" style="width: 10%"></th>
                                    <th>1st reading</th>
                                    <th>2nd reading</th>
                                    <th>3rd reading</th>
                                </tr>
                                <tr>
                                    <td class="first">Systolic</td>            
                                    <td><?php echo $consultation['PatientVitalSignBloodPressure']['result_systolic_1']; ?> mmHg</td>
                                    <td><?php echo $consultation['PatientVitalSignBloodPressure']['result_systolic_2']; ?> mmHg</td>
                                    <td><?php echo $consultation['PatientVitalSignBloodPressure']['result_systolic_3']; ?> mmHg</td>
                                </tr>
                                <tr>
                                    <td class="first">Diastolic</td>            
                                    <td><?php echo $consultation['PatientVitalSignBloodPressure']['result_diastolic_1']; ?> mmHg</td>
                                    <td><?php echo $consultation['PatientVitalSignBloodPressure']['result_diastolic_2']; ?> mmHg</td>
                                    <td><?php echo $consultation['PatientVitalSignBloodPressure']['result_diastolic_3']; ?> mmHg</td>
                                </tr>
                            </table>
                        </fieldset>
                    </div>
                </div>
                <!-- Chief Complain -->
                <div class="legend" style="width: 32.5%; float: left; padding: 2px;">
                    <div class="legend_title"><label for="PatientConsultationComplain"><b><?php echo TABLE_CHIEF_COMPLAIN; ?></b></label></div>
                    <div class="legend_content" style="height: 100px;">             
                        <?php 
                        $complain = array();
                        $queryComplain = mysql_query("SELECT dc.chief_complain_id FROM doctor_chief_complains dc WHERE dc.status = 1 AND dc.queued_id = ".$consultation['Queue']['id']);
                        while ($result = mysql_fetch_array($queryComplain)) {
                            $complain[]= $result['chief_complain_id'];
                        }
                        ?>
                        <?php echo $this->Form->input('complain_id', array('label' => false, 'data-placeholder' => INPUT_SELECT, 'selected' => $complain, 'style' => 'width: 424px; display:none;' )); ?>
                        <?php echo $this->Form->input('chief_complain', array('disabled' => $disabled, 'label' => false, 'type' => 'textarea', 'value' => $consultation['PatientConsultation']['chief_complain'], 'style' => 'width: 97% ! important; height: 85px ! important;')); ?>
                    </div>
                </div>       
                <!-- Medical History -->
                <div class="legend" style="width: 32.5%; float: left; padding: 2px;">
                    <div class="legend_title"><label for="PatientConsultationMedicalHistory"><b><?php echo MEDICAl_HISTORY; ?></b></label></div>
                    <div class="legend_content" style="height: 100px;">
                        <?php echo $this->Form->input('medical_history', array('label' => false, 'type' => 'textarea', 'style' => 'width: 97% ! important; height: 85px ! important;', 'value' => $consultation['PatientConsultation']['medical_history'])); ?>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
            <div style="width: 100%;">                
                <!-- Past History -->
                <div class="legend" style="width: 32.5%; float: left; padding: 2px; display: none;">
                    <div class="legend_title"><label><b><?php echo TABLE_PAST_HISTORY; ?></b></label></div>
                    <div class="legend_content" style="height: 140px;">
                        <table style="width: 100%">
                            <tr>
                                <td style="width: 30%;"><labe for="PatientConsultationMedicalSurgeryHistory"><?php echo PAST_MEDICAl_HISTORY; ?> :</label></td>
                                <td>
                                    <?php echo $this->Form->input('past_medical_history', array('label' => false, 'type' => 'textarea', 'style' => 'width: 97% ! important; height: 30px ! important;', 'value' => $consultation['PatientConsultation']['past_medical_history'] )); ?>                    
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 30%;"><labe for="PatientConsultationMedicalSurgeryHistory"><?php echo MEDICAL_SURGERY_HISTORY; ?> :</label></td>
                                <td>
                                    <?php echo $this->Form->input('medical_surgery_history', array('label' => false, 'type' => 'textarea', 'style' => 'width: 97% ! important; height: 30px ! important;', 'value' => $consultation['PatientConsultation']['medical_surgery'] )); ?>                    
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 30%;"><label for="PatientConsultationFamilyHistory"><?php echo TABLE_FAMILY_HISTORY; ?> :</label></td>
                                <td>
                                    <?php echo $this->Form->input('family_history', array('label' => false, 'type' => 'textarea', 'style' => 'width: 97% ! important; height: 30px ! important;', 'value' => $consultation['PatientConsultation']['family_history'])); ?>                    
                                </td>
                            </tr>
                            <tr style="display: none;">
                                <td style="width: 30%;"><label for="PatientConsultationMedicalSurgeryHistory"><?php echo OBSTETRIC_GYNECOLOGIE_HISTORY; ?> :</label></td>
                                <td>
                                    <?php echo $this->Form->input('obstetic_history', array('label' => false, 'type' => 'textarea', 'style' => 'width:99%;' ,'value' => $consultation['PatientConsultation']['obstric_gynecologie'])); ?>                    
                                </td>
                            </tr>
                        </table>        
                    </div>
                </div>
                <!-- Physical Examination -->
                <div class="legend" style="width: 32.5%; float: left; padding: 2px;">
                    <div class="legend_title"><label for="PatientConsultationDateFirstComplaint"><b><?php echo PHYSICAL_EXAMINATION; ?></b></label></div>
                    <div class="legend_content" style="height: 120px;">
                        <div style="display: none;"><?php echo $this->Form->input('examination', array('empty' => SELECT_OPTION, 'label' => false, 'style' => 'width: 200px;' , 'class' => 'validate[require]' , 'selected' => $consultation['PatientConsultation']['physical_examination_id'])); ?> </div>
                        <?php echo $this->Form->input('physical_examination', array('disabled' => $disabled, 'label' => false, 'type' => 'textarea', 'value' => $consultation['PatientConsultation']['physical_examination'], 'style' => 'width: 97% ! important; height: 105px ! important;')); ?>                      
                    </div>
                </div>   
                <!-- Laboratory -->
                <div class="legend" style="width: 32.5%; float: left; padding: 2px;">
                    <div class="legend_title"><label for="PatientConsultationPrescription"><b><?php echo MENU_LABO_MANAGEMENT; ?></b></label></div>
                    <div class="legend_content viewLaboResult" rel="<?php echo $consultation['QeuedLabo']['id'];?>" title="<?php echo MENU_LABO_MANAGEMENT; ?>" style="height: 120px; overflow-y: scroll; cursor: pointer;">
                        <table style="width: 100%;">
                            <?php      
                            if(!empty($consultation['QeuedLabo']['id'])){
                                $queryLaboRequest = mysql_query("SELECT labg.name, labg.code FROM labos As la "
                                                        . "INNER JOIN labo_requests As lar ON la.id = lar.labo_id "
                                                        . "INNER JOIN  labo_item_groups As labg ON labg.id = lar.labo_item_group_id "
                                                        . "WHERE lar.is_active = 1 AND la.status > 0 AND la.queued_id = {$consultation['QeuedLabo']['id']}");
                                while ($rowLaboRequest = mysql_fetch_array($queryLaboRequest)) {
                                    ?>
                                    <tr>
                                        <td style="width: 10%;"><input type="checkbox" checked="checked" disabled="disabled" /></td>
                                        <td><label><?php echo $rowLaboRequest['name'];?></label></td>
                                    </tr>
                                    <?php
                                }
                            }
                           ?>
                        </table>
                    </div>                    
                </div>
                <!-- Diagnostic -->
                <div class="legend" style="width: 32.5%; float: left; padding: 2px;">
                    <div class="legend_title"><label for="PatientConsultationDaignostic"><b><?php echo TABLE_DAIGNOSTIC; ?></b></label></div>
                    <div class="legend_content" style="height: 120px;">         
                        <div style="display: none;">
                            <?php echo $this->Form->input('patient_diagnostic', array('empty' => SELECT_OPTION, 'label' => false ,  'style' => 'width: 200px;' , 'selected' => $consultation['PatientConsultation']['daignostic_id'])); ?>   
                        </div>
                        <?php echo $this->Form->input('daignostic', array('disabled' => $disabled, 'label' => false, 'type' => 'textarea', 'value' => $consultation['PatientConsultation']['daignostic'], 'style' => 'width: 97% ! important; height: 105px ! important;')); ?>
                    </div>                    
                </div>
            </div>
            <div class="clear"></div>
            <div style="width: 100%;">
                <!-- Prescription -->
                <div class="legend" style="width: 32.5%; float: left; padding: 2px;">
                    <div class="legend_title"><label for="PatientConsultationPrescription"><b><?php echo MENU_PRESCRIPTION; ?></b></label></div>
                    <div class="legend_content" style="height: 120px; overflow-y: scroll;">
                        <?php                                         
                        $queryPrscription = mysql_query("SELECT * FROM orders WHERE orders.status > 0 AND orders.queue_doctor_id = {$consultation['QeuedDoctor']['id']}");
                        $resultPrescription = mysql_fetch_array($queryPrscription);
                        if(mysql_num_rows($queryPrscription)){
                        ?>                            
                            <div id="contentDoctor">
                                <table style="border: none;">                                    
                                    <?php
                                    $index = 0;
                                    $queryPrscriptionDetail = mysql_query("SELECT *, products.name, products.code, uoms.abbr FROM order_details INNER JOIN products ON products.id = order_details.product_id INNER JOIN product_pgroups ON product_pgroups.product_id = products.id INNER JOIN uoms ON uoms.id = order_details.qty_uom_id WHERE product_pgroups.pgroup_id = 1 AND order_details.order_id = {$resultPrescription['id']}");
                                    while ($orderDetail = mysql_fetch_array($queryPrscriptionDetail)) {
                                        $productName = $orderDetail['name'];                               
                                    ?>
                                        <tr>
                                            <td>
                                                <?php echo++$index; ?> - 
                                                <?php 
                                                echo $productName;
                                                if(trim($orderDetail['note'])!=""){
                                                    echo '&nbsp;&nbsp;>&nbsp;&nbsp;'.$orderDetail['note'];
                                                }
                                                if($orderDetail['qty']!=""){
                                                    echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$orderDetail['qty'];
                                                }
                                                if($orderDetail['abbr']!=""){
                                                    echo '&nbsp;&nbsp;&nbsp; '.$orderDetail['abbr'];
                                                }
                                                if($orderDetail['num_days']!=""){
                                                    echo ',&nbsp;&nbsp;&nbsp;'.$orderDetail['num_days'];
                                                }
                                                $medicinUseMorning = "";
                                                if($orderDetail['morning_use_id']!= ""){
                                                    $queryMedicineUse = mysql_query("SELECT name FROM treatment_uses WHERE id = {$orderDetail['morning_use_id']}");
                                                    while ($resultMedicineUse = mysql_fetch_array($queryMedicineUse)) {                        
                                                        $medicinUseMorning = $resultMedicineUse['name'];                                    
                                                    }
                                                }
                                                echo ',&nbsp;&nbsp;&nbsp;'.$medicinUseMorning;
                                                ?>
                                            </td>
                                        </tr>                                        
                                    <?php                
                                    }

                                    $queryPrscriptionMisc = mysql_query("SELECT order_miscs.*, uoms.abbr FROM order_miscs INNER JOIN uoms ON uoms.id =  order_miscs.qty_uom_id WHERE order_miscs.order_id = {$resultPrescription['id']}");
                                    while ($orderMisc = mysql_fetch_array($queryPrscriptionMisc)) {                                                           
                                    ?>
                                        <tr>
                                            <td>
                                                <?php echo ++$index; ?> -  
                                                <?php 
                                                echo $productName = $orderMisc['description']; 
                                                if(trim($orderMisc['note'])!=""){
                                                    echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$orderMisc['note'];
                                                }
                                                if($orderMisc['qty']!=""){
                                                    echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$orderMisc['qty'];
                                                }                
                                                if($orderMisc['num_days']!=""){
                                                    echo ',&nbsp;&nbsp;&nbsp; '.TABLE_NUM_DAYS.': '.$orderMisc['num_days'];
                                                }
                                                $medicinUseMorning = "";
                                                if($orderMisc['morning_use_id']!= ""){
                                                    $queryMedicineUse = mysql_query("SELECT name FROM treatment_uses WHERE id = {$orderMisc['morning_use_id']}");
                                                    while ($resultMedicineUse = mysql_fetch_array($queryMedicineUse)) {             
                                                        $medicinUseMorning = $resultMedicineUse['name'];                                    
                                                    }
                                                }               
                                                echo ',&nbsp;&nbsp;&nbsp;'.$medicinUseMorning;
                                                ?>
                                            </td>                                            
                                        </tr>
                                    <?php
                                    }
                                    ?>                
                                </table>
                            </div>
                            <p style="font-size: 11px; font-weight: bold; display: none;">
                                <?php echo TABLE_CREATED_BY;?>: 
                                <?php 
                                $name = "";
                                $query = mysql_query(" SELECT (SELECT name FROM employees WHERE id = user_employees.employee_id) AS name FROM users INNER JOIN user_employees ON user_employees.user_id = users.id WHERE users.id = '" . $user['User']['id'] . "' GROUP BY users.id LIMIT 0,1");
                                while ($row = mysql_fetch_array($query)) {
                                    $name = $row['name'];
                                }
                                echo $name; 
                                ?>
                            </p>
                        <?php }?>
                    </div>                    
                </div>
                <!-- Doctor Appointment -->
                <div class="legend" style="width: 32.5%; float: left; padding: 2px;">
                    <div class="legend_title"><label for="ConsultationDateFirstComplaint"><b><?php echo MENU_APPOINTMENT_MANAGEMENT; ?></b></label></div>
                    <div class="legend_content" style="height: 120px;">
                        <table style="width: 100%">
                            <?php 
                            $appointmentId = "";
                            $appointmentDate = "";
                            $appointmentDesc = "";
                            $queryAppointment = mysql_query("SELECT id, app_date, description FROM appointments WHERE queue_doctor_id = {$consultation['QeuedDoctor']['id']}");
                            while ($rowAppointment = mysql_fetch_array($queryAppointment)) {
                                $appointmentId = $rowAppointment['id'];
                                $appointmentDate = date('d/m/Y', strtotime($rowAppointment['app_date']));
                                $appointmentDesc = $rowAppointment['description'];
                            }
                            $patientConsultationAppDate = "PatientConsultationNumAppDate";
                            if(!empty($display)){
                                $patientConsultationAppDate = "PatientConsultationNumAppDate".$consultation['PatientConsultation']['id'];
                            }
                            ?>
                            <tr>
                                <td style="width: 30%;">
                                    <input type="hidden" name="data[Appointment][id]" value="<?php echo $appointmentId;?>" />
                                    <label for="<?php echo $patientConsultationAppDate;?>"><?php echo APPOINTMENT_DATE; ?> :</label>
                                </td>
                                <td><?php echo $this->Form->text('app_date', array('id' => $patientConsultationAppDate, 'disabled' => $disabled, 'value' => $appointmentDate, 'readonly' => true, "style" => "width: 97% ! important;")); ?></td>
                            </tr>
                            <tr>
                                <td><label for="PatientConsultationDescription"><?php echo TABLE_FOR; ?> :</label></td>
                                <td><?php echo $this->Form->textarea('description', array('disabled' => $disabled, 'type' => 'textarea', 'value' => $appointmentDesc,'style'=>'width: 97% ! important; height: 72px ! important;')); ?></td>
                            </tr>
                        </table>        
                    </div>
                </div> 
                <!-- Immunization -->
                <div class="legend" style="width: 32.5%; float: left; padding: 2px;">
                    <div class="legend_title"><label for="PatientConsultationImmunization"><b><?php echo 'Immunization'; ?></b></label></div>
                    <div class="legend_content" style="height: 120px; overflow-y: scroll;">
                        <?php     
                        if(!empty($resultImmuzation)){        
                            $resultVaccine = implode(',', $resultImmuzation);
                            $queryPrscription = mysql_query("SELECT * FROM orders WHERE orders.status > 0 AND orders.id IN ({$resultVaccine})");
                            if(mysql_num_rows($queryPrscription)){
                            while ($resultPrescription = mysql_fetch_array($queryPrscription)) {
                            ?>                            
                                <div id="contentDoctor">
                                    <h1 id="headerVaccine<?php echo $resultPrescription['id'];?>">
                                        - <?php echo date('d/m/Y', strtotime($resultPrescription['order_date']));?>
                                        <span style="float: right; font-size: 11px;"><?php echo getDoctor($resultPrescription['created_by']); ?></span>
                                    </h1>
                                    <?php 
                                    $index = 0;
                                    $queryPrscriptionDetail = mysql_query("SELECT *, products.name, products.code, uoms.abbr FROM order_details "
                                            . "INNER JOIN products ON products.id = order_details.product_id "
                                            . "INNER JOIN product_pgroups ON product_pgroups.product_id = products.id "
                                            . "INNER JOIN uoms ON uoms.id = order_details.qty_uom_id "
                                            . "WHERE order_details.order_id = {$resultPrescription['id']} AND pgroup_id = 2");

                                    ?>
                                    <table style="border: none; padding-left: 5%;" class="tableVaccine" rel="<?php echo $resultPrescription['id'];?>" record="<?php echo mysql_num_rows($queryPrscriptionDetail);?>">                                    
                                        <?php                                    
                                        while ($orderDetail = mysql_fetch_array($queryPrscriptionDetail)) {
                                            $productName = $orderDetail['name'];                               
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php echo++$index; ?> - 
                                                    <?php 
                                                    echo $productName;
                                                    if(trim($orderDetail['note'])!=""){
                                                        echo '&nbsp;&nbsp;>&nbsp;&nbsp;'.$orderDetail['note'];
                                                    }
                                                    if($orderDetail['qty']!=""){
                                                        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$orderDetail['qty'];
                                                    }
                                                    if($orderDetail['abbr']!=""){
                                                        echo '&nbsp;&nbsp;&nbsp; '.$orderDetail['abbr'];
                                                    }
                                                    if($orderDetail['num_days']!=""){
                                                        echo ',&nbsp;&nbsp;&nbsp;'.$orderDetail['num_days'];
                                                    }
                                                    $medicinUseMorning = "";
                                                    if($orderDetail['morning_use_id']!= ""){
                                                        $queryMedicineUse = mysql_query("SELECT name FROM treatment_uses WHERE id = {$orderDetail['morning_use_id']}");
                                                        while ($resultMedicineUse = mysql_fetch_array($queryMedicineUse)) {                        
                                                            $medicinUseMorning = $resultMedicineUse['name'];                                    
                                                        }
                                                    }
                                                    echo ',&nbsp;&nbsp;&nbsp;'.$medicinUseMorning;
                                                    ?>
                                                </td>
                                            </tr>                                        
                                        <?php                
                                        }

                                        $queryPrscriptionMisc = mysql_query("SELECT order_miscs.*, uoms.abbr FROM order_miscs INNER JOIN uoms ON uoms.id =  order_miscs.qty_uom_id WHERE order_miscs.order_id = {$resultPrescription['id']}");
                                        while ($orderMisc = mysql_fetch_array($queryPrscriptionMisc)) {                                                           
                                        ?>
                                            <tr>
                                                <td>
                                                    <?php echo ++$index; ?> -  
                                                    <?php 
                                                    echo $productName = $orderMisc['description']; 
                                                    if(trim($orderMisc['note'])!=""){
                                                        echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$orderMisc['note'];
                                                    }
                                                    if($orderMisc['qty']!=""){
                                                        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$orderMisc['qty'];
                                                    }                
                                                    if($orderMisc['num_days']!=""){
                                                        echo ',&nbsp;&nbsp;&nbsp; '.TABLE_NUM_DAYS.': '.$orderMisc['num_days'];
                                                    }
                                                    $medicinUseMorning = "";
                                                    if($orderMisc['morning_use_id']!= ""){
                                                        $queryMedicineUse = mysql_query("SELECT name FROM treatment_uses WHERE id = {$orderMisc['morning_use_id']}");
                                                        while ($resultMedicineUse = mysql_fetch_array($queryMedicineUse)) {             
                                                            $medicinUseMorning = $resultMedicineUse['name'];                                    
                                                        }
                                                    }               
                                                    echo ',&nbsp;&nbsp;&nbsp;'.$medicinUseMorning;
                                                    ?>
                                                </td>                                            
                                            </tr>
                                        <?php
                                        }
                                        ?>                
                                    </table>
                                </div>
                            <?php }
                            }                            
                        }
                        ?>
                        <div class="clear"></div>
                    </div>                    
                </div>
            </div>
            <div class="clear"></div>  
            <div style="width: 100%;">                
                <!-- Doctor Comment -->
                <div class="legend" style="width: 32.5%; float: left; padding: 2px;">
                    <div class="legend_title"><label for="PatientConsultationRemark"><b><?php echo MENU_REMARKS; ?></b></label></div>
                    <div class="legend_content" style="height: 120px;">
                        <?php echo $this->Form->input('remark', array('disabled' => $disabled, 'label' => false, 'type' => 'textarea', 'value' => $consultation['PatientConsultation']['remark'], 'style' => 'width: 97% ! important; height: 105px ! important;')); ?>                                                                        
                    </div>
                </div>
                <!-- Daily Clinical Report -->
                <div class="legend" style="width: 32.5%; float: left; padding: 2px;">
                    <div class="legend_title"><label for="PatientConsultationFollowUp"><b><?php echo MENU_FOLLOW_UP; ?></b></label></div>
                    <div class="legend_content" style="height: 120px; overflow-y: scroll;">
                        <?php echo $this->Form->input('follow_up', array('disabled' => $disabled, 'label' => false, 'type' => 'hidden', 'value' => $consultation['PatientConsultation']['follow_up'], 'style' => 'width: 97% ! important; height: 105px ! important;')); ?>
                        <table width="100%" style="border:none;">
                            <thead>
                                <tr>
                                    <td style="border: none; font-weight: bold; background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo date('d/m/Y H:i:s', strtotime($consultation['PatientConsultation']['created'])); ?></td>
                                    <td style="border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo getDoctor($consultation['PatientConsultation']['created_by']);?></td>
                                </tr>
                                <tr>
                                    <td style="border: none; padding-left: 5%; color: rgb(0, 0, 0);" colspan="2"><?php echo nl2br($consultation['PatientConsultation']['follow_up']); ?></td>
                                </tr>
                            </thead>
                            <tbody id="tbFollowUpResult">
                                <?php
                                $index = 1;
                                $query_followup = mysql_query("SELECT * FROM patient_followups WHERE is_active=1 AND patient_consultation_id=" . $consultation['PatientConsultation']['id']." ORDER BY created ASC");
                                while ($data_followup = mysql_fetch_array($query_followup)) {
                                    ?>
                                    <tr>
                                        <td style="border: none; font-weight: bold; background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo date('d/m/Y H:i:s', strtotime($data_followup['created'])); ?></td>
                                        <td style="border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo getDoctor($data_followup['created_by']);?></td>
                                    </tr>
                                    <tr>
                                        <td style="border: none; padding-left: 5%; color: rgb(0, 0, 0);" colspan="2">
                                            <?php echo nl2br($data_followup['followup']);?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>                
                    <div class="legend_content">
                        <div class="buttons">
                            <button  queuedDoctorId="<?php echo $consultation['PatientConsultation']['queued_doctor_id'];?>" name="<?php echo $consultation['PatientConsultation']['id']; ?>" title="<?php echo $consultation['Queue']['id'];?>" class="positive btnFollowup" id="">
                                <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
                                 <?php echo MENU_FOLLOW_UP_LABEL_ADD; ?>
                            </button>
                        </div>
                        <div style="clear: both;"></div>
                    </div>
                </div>
                <div class="legend" style="width: 33%; float: left; padding: 2px; display: none;">
                    <div class="legend_title"><label><b><?php echo MENU_PATIENT_IPD; ?></b></label></div>
                    <div class="legend_content" style="height: 120px;">
                        <table style="width: 100%">
                            <tr>
                                <td style="width: 30%;"><label for="PatientConsultationRoomId"><?php echo TABLE_ROOM_NUMBER; ?> :</label></td>
                                <td>
                                    <input type="hidden" name="data[PatientIpd][id]" value="<?php echo $patientIpd['PatientIpd']['id'];?>"/>
                                    <?php 
                                    $roomDisabled = "";
                                    if($disabled!=""){
                                        $roomDisabled = "disabled='disabled'";
                                    }
                                    ?>
                                    <select <?php echo $disabled;?> id="PatientConsultationRoomId" name="data[PatientIpd][room_id]" class="classRoom">
                                        <option value=""><?php echo SELECT_OPTION;?></option>
                                        <?php 
                                        foreach ($rooms as $room) {
                                            $ipdStatus = "";
                                            $disabled = "";
                                            $queryPatientStay = mysql_query("SELECT patient_ipds.id FROM patient_stay_in_rooms INNER JOIN patient_ipds ON patient_ipds.id = patient_stay_in_rooms.patient_ipd_id WHERE patient_ipds.is_active = 1 AND room_id = {$room['Room']['id']} AND patient_stay_in_rooms.status = 1 ");
                                            if(mysql_num_rows($queryPatientStay)){
                                                $ipdStatus = "color:red;";
                                                $disabled = "disabled='disabled'";
                                            }
                                            
                                            if($room['Room']['id'] == $patientIpd['Room']['id']){
                                                echo '<option style="'.$ipdStatus.'" selected="selected" value="'.$room['Room']['id'].'">'.$room['Room']['room_name'].'-'.$room['RoomType']['name'].'</option>';
                                            }else{
                                                echo '<option '.$disabled.' style="'.$ipdStatus.'" value="'.$room['Room']['id'].'">'.$room['Room']['room_name'].'-'.$room['RoomType']['name'].'</option>';
                                            }
                                        }
                                        ?>
                                    </select>  
                                </td>
                            </tr>
                            <tr style="display: none;">
                                <td><label for="PatientConsultationAllergies"><?php echo TABLE_ALLERGIC; ?> :</label></td>
                                <td><?php echo $this->Form->textarea('allergies', array('name' => 'data[PatientIpd][allergies]', 'disabled' => $disabled, 'style' => 'width: 97% !important; height: 70px ! important;', 'value' => $patientIpd['PatientIpd']['allergies'])); ?></td>
                            </tr>
                        </table>        
                    </div>        
                </div>
                <div class="legend" style="width: 33%; float: left; padding: 2px;">
                    <div class="legend_title"><label><b><?php echo TABLE_PATIENT_STATUS; ?></b></label></div>
                    <div class="legend_content" style="height: 100px;">
                        <table style="width: 100%">
                            <tr>
                                <td style="width: 30%;"><label for="PatientConsultationConsultStatus<?php echo $consultation['PatientConsultation']['id'];?>"><?php echo TABLE_STATUS; ?> :</label></td>
                                <td>
                                    <select <?php echo $roomDisabled;?> class="PatientConsultationConsultStatus" rel="<?php echo $consultation['PatientConsultation']['id'];?>" id="PatientConsultationConsultStatus<?php echo $consultation['PatientConsultation']['id'];?>" name="data[PatientConsultation][consult_status]" style="width: 150px;">
                                        <option <?php if($consultation['PatientConsultation']['consult_status'] == 1){ echo 'selected="selected"';}?> value="1">Patient OPD</option>
                                        <option <?php if($consultation['PatientConsultation']['consult_status'] == 2){ echo 'selected="selected"';}?> value="2">Patient IPD</option>
                                    </select>
                                </td>
                            </tr>
                            <tr id="checkRoomId<?php echo $consultation['PatientConsultation']['id'];?>" style="<?php if($consultation['PatientConsultation']['consult_status'] == 1){ echo 'display: none;';}?>">
                                <td><label for="PatientConsultationCheckRoomId<?php echo $consultation['PatientConsultation']['id'];?>"><?php echo TABLE_ROOM_NUMBER; ?><span class="red">*</span> :</label></td>
                                <td>
                                    <select <?php echo $roomDisabled;?> id="PatientConsultationCheckRoomId<?php echo $consultation['PatientConsultation']['id'];?>" name="data[PatientConsultation][room_id]" class="validate[required]" style="width: 150px;">
                                        <option value=""><?php echo SELECT_OPTION;?></option>
                                        <?php
                                        foreach ($rooms as $room) {
                                            if($room['Room']['id'] == $consultation['PatientConsultation']['room_id']){
                                                echo '<option selected value="'.$room['Room']['id'].'">'.$room['Room']['room_name'].'</option>';
                                            }else{
                                                echo '<option value="'.$room['Room']['id'].'">'.$room['Room']['room_name'].'</option>';
                                            }
                                            
                                        }
                                        ?>
                                    </select>  
                                </td>
                            </tr>
                        </table>        
                    </div>        
                </div>
            </div>
            <div class="clear"></div>
            <div class="buttons" style='<?php echo $display;?>' >
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
<div id="dialog9" title=""></div>
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
<div id="dialogPreviewLaboResult" title=""></div>