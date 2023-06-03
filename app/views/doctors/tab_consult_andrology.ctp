<?php 
echo $this->element('prevent_multiple_submit');
$absolute_url = FULL_BASE_URL . Router::url("/", false); 
$rount = Router::url("/", false);
?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/sketch.js"></script>
<?php echo $javascript->link('jquery.form'); ?>
<style type="text/css">
    div.checkbox{
        width: 180px;
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
<?php $tblName = "tbl123"; ?>
<script type="text/javascript">
    $(document).ready(function(){
        $("#PatientConsultationAndrologyAddForm").validationEngine();
        $("#PatientConsultationAndrologyAddForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".loading").show();
            },
            success: function(result) {
                $("#tabs3").tabs("select", 4);
                $("#tabConsultAndrologyNum<?php echo $tblName;?>").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabConsultAndrologyNum/<?php echo $this->params['pass'][0].'/'.$this->params['pass'][1];?>");
            }
        });
        $(".legend_content").show();
        $(".legend_title").click(function(){
            $(this).siblings(".legend_content").slideToggle();
        });
        
        $('#PatientConsultationDateFirstComplaintAndrology, #PatientConsultationDateOfConsultAndrology').datepicker(
        {
            changeMonth: true,
            changeYear: true,
            showSecond: false,
            dateFormat:'yy-mm-dd'            
        });
        $('#PatientConsultationPatientDiagnosticAndrology').change(function(){
            var diagnostic = $("#PatientConsultationPatientDiagnosticAndrology").val(); 
                if(diagnostic != ""){
                        $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/getDiagnosticDescription/"+diagnostic,
                        data: "",
                        beforeSend: function(){
                        },
                        success: function(result){
                            if(result != 'null'){
                                $("#PatientConsultationDaignosticAndrology").val(result);
                            }

                        }
                    });
                }else {
                     $("#PatientConsultationDaignosticAndrology").val("");
               }
        });
        
       $('#PatientConsultationExaminationAndrology').change(function(){
            var examination = $("#PatientConsultationExaminationAndrology").val(); 
                if(examination != ""){
                    
                        $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/getExaminationDescription/"+examination,
                        data: "",
                        success: function(result){
                            if(result != 'null'){
                                $("#PatientConsultationPhysicalExaminationAndrology").val(result);
                            }

                        }
                    });
                }else {
                     $("#PatientConsultationPhysicalExaminationAndrology").val("");
               }
        });
        $('#PatientConsultationComplainAndrology').change(function(){
            var chiefComplain = $("#PatientConsultationComplainAndrology").val(); 
                if(chiefComplain != ""){
                        $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/getChiefComplainDescription/"+chiefComplain,
                        data: "",
                        success: function(result){
                            if(result != 'null'){
                                $("#PatientConsultationChiefComplainAndrology").val(result);
                            }

                        }
                    });
                }else {
                     $("#PatientConsultationChiefComplainAndrology").val("");
               }
        });
    });
</script>
<script type="text/javascript">
    $(function() {
        $('#tools_sketch').sketch({defaultColor: "#f00", defaultSize: 2});
    });
</script>
<?php echo $this->Form->create('PatientConsultation', array('id' => 'PatientConsultationAndrologyAddForm', 'url' => '/doctors/tabConsultAndrology/' . $this->params['pass'][0], 'enctype' => 'multipart/form-data')); ?>
<input name="data[QeuedDoctor][id]" type="hidden" value="<?php echo $patient['QeuedDoctor']['id'];?>"/>
<input name="data[Queue][id]" type="hidden" value="<?php echo $patient['Queue'][0]['id'];?>"/>
<input id="link_url" type="hidden" value="<?php echo $absolute_url . $this->params['controller']; ?>"/>
<div class="legend">
    <div class="legend_title"><label for="ConsultationDaignostic"><b><?php echo MENU_VITAL_SING; ?></b></label></div>
    <div class="legend_content">
        <fieldset>
            <legend><?php __(MENU_VITAL_SING_INFO); ?></legend>
            <table style="width: 100%;" cellspacing="3">
                <tr>
                    <td style="width: 15%;"><label for="PatientVitalSignHeight"><?php echo TABLE_HEIGHT; ?></label></td>
                    <td style="width: 20%;">: <?php echo $patient['PatientVitalSign']['height'];?> cm</td>
                    <td style="width: 15%;"><label for="PatientVitalSignWeight"><?php echo TABLE_WEIGHT; ?></label></td>
                    <td style="width: 20%;">: <?php echo $patient['PatientVitalSign']['weight']; ?> kg</td>
                    <td style="width: 10%;"><label for="PatientVitalSignBMI"><?php echo TABLE_BMI; ?></label></td>
                    <td style="width: 20%;" class="BMI">: <?php echo $patient['PatientVitalSign']['BMI'] ?></td>            
                </tr>
                <tr>
                    <td style="width: 15%;"><label for="PatientVitalSignPulse"><?php echo TABLE_PULSE; ?></label></td>
                    <td style="width: 20%;">: <?php echo $patient['PatientVitalSign']['pulse']; ?> /m</td>
                    <td style="width: 15%;"><label for="PatientVitalSignRespiratory"><?php echo TABLE_RESPIRATORY; ?></label></td>
                    <td style="width: 20%;">: <?php echo $patient['PatientVitalSign']['respiratory']; ?> /m</td>                     
                </tr>
                <tr>
                    <td style="width: 15%;"><label for="PatientVitalSignTemperature"><?php echo TABLE_TEMPERATURE; ?></label></td>
                    <td style="width: 20%;">: <?php echo $patient['PatientVitalSign']['temperature']; ?> °C</td>
                    <td style="width: 15%;"><label for="PatientVitalSignSop2"><?php echo TABLE_SOP2; ?></label></td>
                    <td style="width: 20%;">: <?php echo $patient['PatientVitalSign']['sop2']; ?></td>       
                </tr>
            </table>      
        </fieldset>
        <br/>
        <fieldset>
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
                    <td><?php echo $patient['PatientVitalSignBloodPressure']['result_systolic_1']; ?> mmHg</td>
                    <td><?php echo $patient['PatientVitalSignBloodPressure']['result_systolic_2']; ?> mmHg</td>
                    <td><?php echo $patient['PatientVitalSignBloodPressure']['result_systolic_3']; ?> mmHg</td>
                </tr>
                <tr>
                    <td class="first">Diastolic</td>            
                    <td><?php echo $patient['PatientVitalSignBloodPressure']['result_diastolic_1']; ?> mmHg</td>
                    <td><?php echo $patient['PatientVitalSignBloodPressure']['result_diastolic_2']; ?> mmHg</td>
                    <td><?php echo $patient['PatientVitalSignBloodPressure']['result_diastolic_3']; ?> mmHg</td>
                </tr>
            </table>
        </fieldset>
    </div>
</div>
<br />
<div class="legend">
    <div class="legend_title"><label for="ConsultationDateFirstComplaint"><b><?php echo MENU_DOCTOR; ?></b></label></div>
    <div class="legend_content">
        <table style="width: 100%">
            <tr>
                <td style="width: 20%;"><labe for="DoctorConsultationName"><?php echo DOCTOR_NAME; ?> :</label></td>
                <td style="width: 27%;">
                    <?php echo $this->Form->input('doctor_consultation_ids', array('label' => false, 'data-placeholder' => INPUT_SELECT)); ?>
                </td>
                <td style="width: 53%;"></td>
            </tr>
        </table>        
    </div>
</div>
<br>
<div class="legend">
    <div class="legend_title"><label for="ConsultationDateFirstComplaintAndrology"><b><?php echo TABLE_PRESENT_MEDICAL_HISTORY; ?></b></label></div>
    <div class="legend_content">
        <table style="width: 100%">
            <tr>
                <td style="width: 20%;"><label for="PatientConsultationDateFirstComplaint"><?php echo DATE_OF_FIRST_COMPLAINT; ?> :</label></td>
                <td>
                    <?php echo $this->Form->input('date_first_complaint_andrology', array('label' => false, 'type' => 'text')); ?>
                </td>
            </tr>
            <tr>
                <td style="width: 20%;"><label for="PatientConsultationDateOfConsultAndrology"><?php echo DATE_OF_FIRST_CONSULTATION; ?> :</label></td>
                <td>
                    <?php echo $this->Form->input('date_of_consult_andrology', array('label' => false, 'type' => 'text')); ?>                    
                </td>
            </tr>
            <tr>
                <td style="width: 20%;"><labe for="PatientConsultationPhysicalExamination"><?php echo PHYSICAL_EXAMINATION; ?> :</label></td>
                <td>
                    <?php echo $this->Form->input('examination_andrology', array('empty' => SELECT_OPTION, 'label' => false, 'style' => 'width: 200px;' , 'class' => 'validate[require]')); ?> 
                    <?php echo $this->Form->input('physical_examination_andrology', array('label' => false, 'type' => 'textarea')); ?>
                    <?php echo $this->Form->input('physical_examination_other_info', array('label' => false, 'type' => 'textarea' , 'placeholder' => 'Other Information')); ?>
                </td>
            </tr>
        </table>        
    </div>
</div>
<br />
<div class="legend">
    <div class="legend_title"><label for="PatientConsultationDaignostic"><b><?php echo TABLE_CHIEF_COMPLAIN; ?></b></label></div>
    <div class="legend_content">
        <?php echo $this->Form->input('complain_andrology', array('empty' => SELECT_OPTION, 'label' => false, 'style' => 'width: 200px;')); ?>   
        <?php echo $this->Form->input('chief_complain_andrology', array('label' => false, 'type' => 'textarea')); ?>
    </div>
    <div class="legend_content"><?php echo $this->Form->input('chief_complain_other_info', array('label' => false,'placeholder' => 'Other Information' ,  'type' => 'textarea')); ?></div>
</div>
<br />
<div class="legend">
    <div class="legend_title"><label for="PatientConsultationDaignostic"><b><?php echo TABLE_DAIGNOSTIC; ?></b></label></div>
    <div class="legend_content">
        <?php echo $this->Form->input('patient_diagnostic_andrology', array('empty' => SELECT_OPTION, 'label' => false ,  'style' => 'width: 200px;')); ?>   
        <?php echo $this->Form->input('daignostic_andrology', array('label' => false, 'type' => 'textarea')); ?>
    </div>
    <div class="legend_content"><?php echo $this->Form->input('daignostic_other_info', array('label' => false,'placeholder' => 'Other Information' , 'type' => 'textarea')); ?></div>
</div>
<br />
<div class="legend" style="display: none;">
    <div class="legend_title"><label for="PatientConsultationTreatment"><b><?php echo MENU_TREATMENT; ?></b></label></div>
    <div class="legend_content"><?php echo $this->Form->input('treatment', array('label' => false,  'type' => 'textarea')); ?></div>
</div>
<br />
<div class="legend">
    <div class="legend_title"><label for="PatientConsultationFollowUp"><b><?php echo MENU_FOLLOW_UP; ?></b></label></div>
    <div class="legend_content"><?php echo $this->Form->input('follow_up', array('label' => false, 'type' => 'textarea')); ?></div>
</div>
<br />
<div class="legend">
    <div class="legend_title"><label for="PatientConsultationRemark"><b><?php echo MENU_REMARKS; ?></b></label></div>
    <div class="legend_content"><?php echo $this->Form->input('remark', array('label' => false, 'type' => 'textarea')); ?></div>
</div>
<br />
<br />

<div class="legend">
    <div class="legend_title">
        <label for="ConsultImageDermatology"><b><?php echo CONSULT_IMAGE_DERMATOLOGY; ?></b></label>
    </div>
    <div class="legend_content">
        <input type="hidden" type="text" id="patient_id" name="patient_id" value="<?php echo $patient['Patient']['id']; ?>" />
        <input type="hidden" type="text" id="queue_id" name="queue_id" value="<?php echo $patient['Queue'][0]['id'];?>" />
        <input type="hidden" type="text" id="doctor_id" name="doctor_id" value="<?php echo $patient['QeuedDoctor']['id'];?>" /> 
        <canvas id="tools_sketch" width="800" height="450" style="background: url(<?php echo $this->webroot; ?>img/photo.png) no-repeat center center;"></canvas>                                                      
        <br/>
        <a style="margin-left: 20px;" href="#tools_sketch" data-tool="marker"><img alt="paint" src="<?php echo $this->webroot; ?>img/tool/pencil.png" /></a>&nbsp;&nbsp;&nbsp;&nbsp;
        <a href="#tools_sketch" data-tool="eraser"><img alt="erase" src="<?php echo $this->webroot; ?>img/tool/eraser.png"  /></a>&nbsp;&nbsp;&nbsp;&nbsp;
        <a href="#tools_sketch" data-download="png"><img alt="save" src="<?php echo $this->webroot; ?>img/tool/save.png" /></a>     
        <br/>
        <p style="margin-left: 10px;"><?php echo GENERAL_DESCRIPTION; ?></p>
        <?php echo $this->Form->input('description_image', array('label' => false, 'type' => 'textarea', 'style'=> 'margin-left: 10px;')); ?>
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


