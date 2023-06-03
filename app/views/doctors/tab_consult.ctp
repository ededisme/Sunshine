<?php 
echo $this->element('prevent_multiple_submit');
$absolute_url = FULL_BASE_URL . Router::url("/", false); 
$rount = Router::url("/", false);               
$rnd = rand();
$btnPlusMinus = "btnPlusMinus" . $rnd;
?>
<!--<script type="text/javascript" src="<?php echo $this->webroot; ?>js/sketch.js"></script>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/plugins/blockUI/jquery.blockUI.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/ui.multiselect.css" />
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/ui.multiselect.js"></script>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/ui.multiselect-<?php echo $this->Session->read('lang'); ?>.js"></script>    -->

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
        bmi();          
        $("#PatientConsultationHeight,#PatientConsultationWeight").keyup(function(){
            bmi();
        });
        
        $(".chzn-select").chosen();
        $("#PatientConsultationTobAlch_chosen").css("width" , "500px");
        $("#PatientConsultationAddForm").validationEngine();
        $("#PatientConsultationAddForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".loading").show();
            },
            beforeSerialize: function($form, options) {                
                $("#PatientConsultationAppDate").datepicker("option", "dateFormat", "yy-mm-dd");                
            },
            success: function(result) {
                $("#tabs3").tabs("select", 1);
                $("#tabConsultNum<?php echo $tblName;?>").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabConsultNum/<?php echo $this->params['pass'][0].'/'.$this->params['pass'][1];?>");
            }
        });
        $(".legend_content").show();
        $(".legend_title").click(function(){
            $(this).siblings(".legend_content").slideToggle();
        });
        
        $('#PatientConsultationDateFirstComplaint, #PatientConsultationDateOfConsult').datepicker(
        {
            changeMonth: true,
            changeYear: true,
            showSecond: false,
            dateFormat:'yy-mm-dd'            
        });
        $('#PatientConsultationPatientDiagnostic').change(function(){
            var diagnostic = $("#PatientConsultationPatientDiagnostic").val(); 
                if(diagnostic != ""){
                        $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/getDiagnosticDescription/"+diagnostic,
                        data: "",
                        beforeSend: function(){
                        },
                        success: function(result){
                            if(result != 'null'){
                                $("#PatientConsultationDaignostic").val(result);
                            }

                        }
                    });
                }else {
                     $("#PatientConsultationDaignostic").val("");
               }
        });
        
        $('#PatientConsultationDoctorComment').change(function(){                       
            if($(this).val() != ""){
                $.ajax({
                    type: "POST",
                    url: "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/getDoctorCommentDescription/"+$(this).val(),
                    data: "",
                    success: function(result){
                        if(result != 'null'){
                            $("#PatientConsultationRemark").val(result);
                        }
                    }
                });
            }else {
                $("#PatientConsultationRemark").val("");
            }
        });
        
        $('#PatientConsultationDailyClinicalReport').change(function(){                       
            if($(this).val() != ""){
                $.ajax({
                    type: "POST",
                    url: "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/getDailyClinicalReportDescription/"+$(this).val(),
                    data: "",
                    success: function(result){
                        if(result != 'null'){
                            $("#PatientConsultationFollowUp").val(result);
                        }
                    }
                });
            }else {
                $("#PatientConsultationFollowUp").val("");
            }
        });                       
        
        $('#PatientConsultationExamination').change(function(){
            var examination = $("#PatientConsultationExamination").val(); 
            if(examination != ""){
                $.ajax({
                    type: "POST",
                    url: "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/getExaminationDescription/"+examination,
                    data: "",
                    success: function(result){
                        if(result != 'null'){
                            $("#PatientConsultationPhysicalExamination").val(result);
                        }

                    }
                });
            }else {
                 $("#PatientConsultationPhysicalExamination").val("");
           }
        });
        $("#PatientConsultationGenitoUrinarySystem").change(function() {
            var val =  $("#PatientConsultationGenitoUrinarySystem").val(); 
            if(val != 1){
                $("#PatientConsultationOther").css("display" , "none");
                $("#tblProstate").css("display" , "") ; 
                $("#PatientConsultationOther").val('');
            }else {
                $("#PatientConsultationOther").css("display" , "");
                $("#tblProstate").css("display" , "none") ;
                $("input:checkbox").prop('checked', false);
            }
        });
        
        $('#PatientConsultationComplain').change(function(){
            var chiefComplain = $("#PatientConsultationComplain").val(); 
            if(chiefComplain != ""){
                    $.ajax({
                    type: "POST",
                    url: "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/getChiefComplainDescription/"+chiefComplain,
                    data: "",
                    success: function(result){
                        if(result != 'null'){
                            $("#PatientConsultationChiefComplain").val(result);
                        }

                    }
                });
            }else {
                 $("#PatientConsultationChiefComplain").val("");
           }
        });
        
        $('#PatientConsultationMedical').change(function(){
            var chiefComplain = $("#PatientConsultationMedical").val(); 
            if(chiefComplain != ""){
                    $.ajax({
                    type: "POST",
                    url: "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/getMedicalHistory/"+chiefComplain,
                    data: "",
                    success: function(result){
                        if(result != 'null'){
                            $("#PatientConsultationMedicalHistory").val(result);
                        }

                    }
                });
            }else {
                 $("#PatientConsultationMedicalHistory").val("");
           }
        });
        
        $('#PatientConsultationConsultStatus').change(function(){
            var consultStatus = $(this).val(); 
            if(consultStatus == 2){
                $("#checkRoomId").show();
            }else {
                $("#checkRoomId").hide();
                $("#PatientConsultationCheckRoomId").val("");
           }
        });
        

        // check box for genito Urinary  Ssystem 
        $("input:checkbox").click(function() {
            if ($(this).is(":checked")) {
                var group = "input:checkbox[name='" + $(this).attr("name") + "']";
                $(group).prop("checked", false);
                $(this).prop("checked", true);
            } else {
                $(this).prop("checked", false);
            }
        });
        
        $('#PatientConsultationAppDate').datepicker({
            changeMonth: true,
            changeYear: true,
            showSecond: false,
            dateFormat:'dd/mm/yy'            
        });
        
        $(".headerPastHistory").click(function(){
            var id = $(this).attr('rel');
            if($(".getPastDetail"+id).is(':visible')==false){
                $("img.<?php echo $btnPlusMinus; ?>", this).attr("src", "<?php echo $this->webroot; ?>img/minus.gif");  
            }else{
                $("img.<?php echo $btnPlusMinus; ?>", this).attr("src", "<?php echo $this->webroot; ?>img/plus.gif");
            }
            $(".getPastDetail"+id).fadeToggle('slow');
        });
    });
    
    function bmi(){
        var result = 0;
        var height = Number($("#PatientConsultationHeight").val())/100;
        var textResult = '';
        result = Number($("#PatientConsultationWeight").val())/(height*height).toFixed(2);
        if(Number($("#PatientConsultationHeight").val())>0 && Number($("#PatientConsultationWeight").val())>0){
            if(result<=18.5){
                textResult = 'Under weight';
                $(".BMI").text('Under weight');            
            }else if(result>18.5 && result<=24.9){
                textResult = 'Normal';
                $(".BMI").text('Normal');            
            }else if(result>24.9 && result<=29.9){
                textResult = 'Over weight';
                $(".BMI").text('Over weight');
            }else if(result>29.9 && result<=40){
                textResult = 'Obese';
                $(".BMI").text('Obese');
            }else if(result>40){
                textResult = 'Over Obese';
                $(".BMI").text('Over obese');            
            }
            $(".patientConsultationBMI").val(textResult);
        }else {
            $(".BMI").text('');     
            $(".patientConsultationBMI").val(textResult);
        }
    }
</script>
<?php echo $this->Form->create('PatientConsultation', array('id' => 'PatientConsultationAddForm', 'url' => '/doctors/tabConsult/' . $this->params['pass'][0], 'enctype' => 'multipart/form-data')); ?>
<input name="data[QeuedDoctor][id]" type="hidden" value="<?php echo $patient['QeuedDoctor']['id'];?>"/>
<input name="data[Queue][id]" type="hidden" value="<?php echo $patient['Queue'][0]['id'];?>"/>
<input id="link_url" type="hidden" value="<?php echo $absolute_url . $this->params['controller']; ?>"/>
<input name="data[PatientVitalSign][id]" type="hidden" value="<?php echo $patient['PatientVitalSign']['id'];?>"/>
<input name="data[PatientVitalSignBloodPressure][id]" type="hidden" value="<?php echo $patient['PatientVitalSignBloodPressure']['id'];?>"/>

<!-- Doctor Name  -->
<div class="legend" style="display: none;">
    <div class="legend_title"><label for="ConsultationDateFirstComplaint"><b><?php echo MENU_DOCTOR; ?></b></label></div>
    <div class="legend_content" style="display: none;">
        <table style="width: 100%">
            <tr>
                <td style="width: 20%;"><label for="DoctorConsultationName"><?php echo DOCTOR_NAME; ?> :</label></td>
                <td style="width: 27%;">
                    <?php echo $this->Form->input('doctor_consultation_ids', array('label' => false, 'data-placeholder' => INPUT_SELECT)); ?>
                </td>
                <td style="width: 53%;"></td>
            </tr>
        </table>        
    </div>
</div>
<br/>
<!-- Vial Sign -->
<div class="legend" style="width: 99.7%; padding: 2px;">
    <div class="legend_title"><label for="ConsultationDaignostic"><b><?php echo MENU_VITAL_SING; ?></b></label></div>
    <div class="legend_content">
        <div style="width: 100%;">
            <fieldset style="width: 98%; padding-left:15px; float: left; height: 80px;">
                <legend><?php __(MENU_VITAL_SING_INFO); ?></legend>
                <table style="width: 100%;" cellspacing="0">
                    <tr>
                    <!-- Weight -->
                        <td style="width: 6%;"><label for="PatientConsultationWeight"><?php echo TABLE_WEIGHT; ?> :</label></td>
                        <td style="width: 20%;"><?php echo $this->Form->text('weight', array('class' => 'float', 'name' => 'data[PatientVitalSign][weight]', 'value' => $patient['PatientVitalSign']['weight'], 'style' => 'width: 150px;', 'autocomplete' => "off")); ?> kg</td>
                    <!-- Temperature   -->
                        <td style="width: 6%;"><label for="PatientConsultationTemperature"><?php echo TABLE_TEMPERATURE; ?> :</label></td>
                        <td style="width: 20%;"><?php echo $this->Form->text('temperature', array('class' => 'float', 'name' => 'data[PatientVitalSign][temperature]', 'value' => $patient['PatientVitalSign']['temperature'], 'style' => 'width: 150px;')); ?> °C</td>           
                        
                    <!-- Respiratore  -->
                        <td style="width: 6%;"><label for="PatientConsultationRespiratory"><?php echo TABLE_RESPIRATORY; ?> :</label></td>
                        <td style="width: 20%;"><?php echo $this->Form->text('respiratory', array('class' => 'float', 'name' => 'data[PatientVitalSign][respiratory]', 'value' => $patient['PatientVitalSign']['respiratory'], 'style' => 'width: 150px;')); ?> /m</td>       
                    <!-- Systolic -->
                        <td style="width: 6%;"><label for="PatientConsultationResultSystolic1"><?php echo "Systolic"; ?> :</label></td>
                        <td style="width: 20%;"><?php echo $this->Form->text('resultSystolic1', array('value' => $patient['PatientVitalSignBloodPressure']['result_systolic_1'], 'name' => 'data[PatientVitalSignBloodPressure][result_systolic_1]', 'style' => 'width: 150px;')); ?></td>
                    </tr>

                    <tr>
                    <!-- height -->
                        <td style="width: 6%; line-height:25px;"><label for="PatientConsultationHeight"><?php echo TABLE_HEIGHT; ?> :</label></td>
                        <td style="width: 20%;"><?php echo $this->Form->text('height', array('class' => 'float', 'name' => 'data[PatientVitalSign][height]', 'value' => $patient['PatientVitalSign']['height'], 'style' => 'width: 150px;', 'autocomplete' => "off")); ?> cm</td>
                    <!-- Pulse -->
                        <td style="width: 6%;"><label for="PatientConsultationPulse"><?php echo TABLE_PULSE; ?> :</label></td>
                        <td style="width: 20%;"><?php echo $this->Form->text('pulse', array('class' => 'float', 'name' => 'data[PatientVitalSign][pulse]', 'value' => $patient['PatientVitalSign']['pulse'], 'style' => 'width: 150px;')); ?> /mn</td>
                    <!-- SpO2 -->
                        <td style="width: 6%;"><label for="PatientConsultationSop2"><?php echo TABLE_SOP2; ?> :</label></td>
                        <td style="width: 20%;"><?php echo $this->Form->text('sop2', array('value' => $patient['PatientVitalSign']['sop2'], 'name' => 'data[PatientVitalSign][sop2]', 'style' => 'width: 150px;')); ?></td>
                    <!-- Diastolic -->
                        <td style="width: 6%;"><label for="PatientConsultationResultDiastolic1"><?php echo "Diastolic"; ?> :</label></td>
                        <td style="width: 20%;"><?php echo $this->Form->text('resultDiastolic1', array('value' => $patient['PatientVitalSignBloodPressure']['result_diastolic_1'], 'name' => 'data[PatientVitalSignBloodPressure][result_diastolic_1]', 'style' => 'width: 150px;')); ?></td>
                    </tr>
                </table>      
            </fieldset>
            <!-- <fieldset style="width: 48%; display:none;float: left; ">
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
                        <td><input style="height:20px" id="resultSystolic1" name="data[PatientVitalSignBloodPressure][result_systolic_1]" value="<?php echo $patient['PatientVitalSignBloodPressure']['result_systolic_1']?>" class="float"/> mmHg</td>
                        <td><input style="height:20px" id="resultSystolic2" name="data[PatientVitalSignBloodPressure][result_systolic_2]" value="<?php echo $patient['PatientVitalSignBloodPressure']['result_systolic_2']?>" class="float"/> mmHg</td>
                        <td><input style="height:20px" id="resultSystolic3" name="data[PatientVitalSignBloodPressure][result_systolic_3]" value="<?php echo $patient['PatientVitalSignBloodPressure']['result_systolic_3']?>" class="float"/> mmHg</td>
                    </tr>
                    <tr>
                        <td class="first">Diastolic</td>            
                        <td><input style="height:20px" id="resultDiastolic1" name="data[PatientVitalSignBloodPressure][result_diastolic_1]" value="<?php echo $patient['PatientVitalSignBloodPressure']['result_diastolic_1']?>" class="float"/> mmHg</td>
                        <td><input style="height:20px" id="resultDiastolic2" name="data[PatientVitalSignBloodPressure][result_diastolic_2]" value="<?php echo $patient['PatientVitalSignBloodPressure']['result_diastolic_2']?>" class="float"/> mmHg</td>
                        <td><input style="height:20px" id="resultDiastolic3" name="data[PatientVitalSignBloodPressure][result_diastolic_3]" value="<?php echo $patient['PatientVitalSignBloodPressure']['result_diastolic_3']?>" class="float"/> mmHg</td>
                    </tr>
                </table>
            </fieldset> -->
        </div>
        <div class="clear"></div>
        <fieldset style="border: 1px dashed #3C69AD; width: 97.5%; display: none;">
            <legend>Other Description</legend>
            <table class="table" style="width: 100%;">
                <tr>
                    <td style="border: none;">
                        <textarea style="width: 99%; border: 1px solid #AAA;" name="data[PatientVitalSign][other_info]"><?php echo $patient['PatientVitalSign']['other_info']; ?></textarea>
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>
</div>
<div class="clear"></div>
<div style="width: 100%;">
    <!-- Chief Complain -->
    <div class="legend" style="width: 33%; float: left; padding: 2px;">
        <div class="legend_title"><label for="PatientConsultationDaignostic"><b><?php echo TABLE_CHIEF_COMPLAIN; ?></b></label></div>
        <div class="legend_content" style="min-height: 90px;">
            <table style="width: 100%">
                <tr>
                    <td>
                        <?php echo $this->Form->input('complain', array('label' => false, 'style' => 'width: 150px;', 'empty' => INPUT_SELECT)); ?>   
                        <?php echo $this->Form->input('chief_complain', array('label' => false, 'type' => 'textarea', 'style' => 'width: 97% ! important; height: 50px ! important;')); ?>
                    </td>
                </tr>
            </table>            
        </div>   
    </div>
    <!-- Medical History -->
    <div class="legend" style="width: 33%; float: left; padding: 2px;">
        <div class="legend_title"><label for="PatientConsultationMedicalHistory"><b><?php echo MEDICAl_HISTORY; ?></b></label></div>
        <div class="legend_content" style="min-height: 90px;">
            <table style="width: 100%">
                <tr>
                    <td>
                        <?php echo $this->Form->input('medical', array('label' => false, 'style' => 'width: 150px;', 'empty' => INPUT_SELECT)); ?> 
                        <?php echo $this->Form->input('medical_history', array('label' => false, 'type' => 'textarea', 'style' => 'width: 97% ! important; height: 50px ! important;')); ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <!-- Present Medical History -->
    <div class="legend" style="width: 33%; float: left; padding: 2px;">
        <div class="legend_title"><label for="ConsultationDateFirstComplaint"><b><?php echo PHYSICAL_EXAMINATION; ?></b></label></div>
        <div class="legend_content" style="min-height: 90px;">
            <table style="width: 100%">
                <tr style="display: none;">
                    <td style="width: 20%;"><label for="PatientConsultationDateFirstComplaint"><?php echo DATE_OF_FIRST_COMPLAINT; ?> :</label></td>
                    <td>
                        <?php echo $this->Form->input('date_first_complaint', array('label' => false, 'type' => 'text', 'style' => 'width: 97% ! important; height: 50px ! important;')); ?>
                    </td>
                </tr>   
                <tr style="display: none;">
                    <td style="width: 20%;"><label for="PatientConsultationDateOfConsult"><?php echo DATE_OF_FIRST_CONSULTATION; ?> :</label></td>
                    <td>
                        <?php echo $this->Form->input('date_of_consult', array('label' => false, 'type' => 'text' )); ?>                    
                    </td>
                </tr>
                <tr>
                    <td style="width: 20%; display: none;"><label for="PatientConsultationPhysicalExamination"><?php echo PHYSICAL_EXAMINATION; ?> :</label></td>
                    <td>
                        <?php echo $this->Form->input('examination', array('empty' => SELECT_OPTION, 'label' => false, 'style' => 'width: 150px;' )); ?> 
                        <?php echo $this->Form->input('physical_examination', array('label' => false, 'type' => 'textarea', 'style' => 'width: 97% ! important; height: 50px ! important;')); ?>
                    </td>
                </tr>

                <tr style="display: none;">
                    <td style="width: 20%;"><label for=""><?php echo TABLE_PRESENT_MEDICAL_HISTORY; ?> :</label></td>
                    <td>
                        <?php echo $this->Form->input('present_medical_history', array('empty' => SELECT_OPTION, 'label' => false, 'style' => 'width: 150px;' )); ?> 
                        <?php echo $this->Form->input('present_medical_history_description', array('label' => false, 'type' => 'textarea')); ?>
                    </td>
                </tr>
            </table>
            <table style="width: 100%; display: none;" >
                <tr>
                    <td style="width: 20%;"><label>DRE :</label></td>
                    <td>
                        <?php echo $this->Form->input('genito_urinary_system', array('label' => false, 'style' => 'width:400px' ,'options' => array(1 => 'Other', 2 => 'Prostate'))); ?>                    
                    </td>
                </tr>
                <tr>
                    <td style="width: 20%;"></td>
                    <td>
                        <?php echo $this->Form->input('other', array('label' => false, 'type' => 'textarea' , 'style' => 'width:99%')); ?>
                    </td> 
                </tr>
            </table>
            <table style="width: 50% ;display:none" id="tblProstate">
                    <tr>                                     
                        <td>Size : </td>
                        <td>
                            <label><input type="checkbox" class="radio" value="enlarge" name="data[PatientConsultation][size][]" />Enlarge</label>
                        </td>
                        <td>    
                            <label><input type="checkbox" class="radio" value="normal" name="data[PatientConsultation][size][]" />Normal</label>
                        </td>
                    </tr>
                     <tr>                                     
                        <td>Surface : </td>
                        <td>
                            <label><input type="checkbox" class="radio" value="smooth" name="data[PatientConsultation][surface][]" />Smooth</label>
                        </td>
                        <td>
                            <label><input type="checkbox" class="radio" value="irregular" name="data[PatientConsultation][surface][]" />Irregular</label>
                        </td>
                    </tr>
                    <tr>                                     
                        <td>Consistency : </td>
                        <td>
                            <label><input type="checkbox" class="radio" value="firm" name="data[PatientConsultation][consistency][]" />Firm</label>
                        </td>
                        <td>
                            <label><input type="checkbox" class="radio" value="elastic" name="data[PatientConsultation][consistency][]" />Elastic</label>                     
                        </td>
                    </tr>
                    <tr>                                     
                        <td>Median Sulcus : </td>
                        <td>
                            <label><input type="checkbox" class="radio" value="obliterated" name="data[PatientConsultation][median_sulcus][]" />Obliterated</label>
                        </td>
                        <td>
                            <label><input type="checkbox" class="radio" value="absent" name="data[PatientConsultation][median_sulcus][]" />Absent</label>                 
                        </td>
                    </tr>
                    <tr>                                     
                        <td>Pain : </td>
                        <td>
                            <label><input type="checkbox" class="radio" value="yes" name="data[PatientConsultation][pain][]" />Yes</label>
                        </td>
                        <td>
                            <label><input type="checkbox" class="radio" value="no" name="data[PatientConsultation][pain][]" />No</label>
                        </td>
                    </tr>
                    <tr>                                     
                        <td>Nodule : </td>
                        <td>
                            <label><input type="checkbox" class="radio" value="yes" name="data[PatientConsultation][no_dule][]" />Yes</label>
                        </td>
                        <td>
                            <label><input type="checkbox" class="radio" value="no" name="data[PatientConsultation][no_dule][]" />No</label>
                        </td>
                    </tr>
            </table>
        </div>
    </div>
</div>
<div class="clear"></div>
<!-- Past History -->
<div class="legend" style="display: none;">
    <div class="legend_title"><label><b><?php echo TABLE_PAST_HISTORY; ?></b></label></div>
    <div class="legend_content" style="display: none;">
        <table style="width: 100%">
            <tr>
                <td style="width: 50%;">
                    <table style="width: 100%">
                        <tr>
                            <td style="width: 20%;"><label for=""><?php echo PAST_MEDICAl_HISTORY; ?> :</label></td>
                            <td>
                                <?php echo $this->Form->input('past_medical_history', array('label' => false, 'type' => 'textarea')); ?>                    
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%;"><label for="PatientConsultationMedicalSurgeryHistory"><?php echo MEDICAL_SURGERY_HISTORY; ?> :</label></td>
                            <td>
                                <?php echo $this->Form->input('medical_surgery', array('label' => false, 'type' => 'textarea')); ?>                    
                            </td>
                        </tr>
                        <tr style="display: none;">
                            <td style="width: 20%;"><labe for="PatientConsultationObsteticHistory"><?php echo OBSTETRIC_GYNECOLOGIE_HISTORY; ?> :</label></td>
                            <td>
                                <?php echo $this->Form->input('obstric_gynecologie', array('label' => false, 'type' => 'textarea')); ?>                    
                            </td>
                        </tr>
                        <tr>
                             <td style="width: 20%;"><label for="PatientConsultationFamilyHistory"><?php echo TABLE_FAMILY_HISTORY; ?> :</label></td>
                            <td>
                                <?php echo $this->Form->input('family_history', array('label' => false, 'type' => 'textarea','style' => 'width:99%;')); ?>                    
                            </td>
                        </tr>
                    </table>
                </td>
                <td valign="top">
                    <div style="min-height: 40px; vertical-align: middle; overflow-y: scroll; height: 205px;">
                        <table style="width: 100%;">
                            <!-- Past History -->
                            <?php         
                            $imgCondition = "minus.gif";
                            $hidden = "";
                            $arrayQueueDoc = array();
                            $queryQueueDoctor = mysql_query("SELECT id FROM queued_doctors WHERE queue_id IN (SELECT id FROM queues WHERE patient_id = {$patient['Patient']['id']} AND (status = 2 OR status = 3)) ");
                            while ($rowQueueDoctor = mysql_fetch_array($queryQueueDoctor)) {                            
                                $arrayQueueDoc[] = $rowQueueDoctor['id'];
                            }
                            if(!empty($arrayQueueDoc)){
                                $resultQueueDoc = implode(',', $arrayQueueDoc);
                                $index = 1;
                                $queryDocCon = mysql_query("SELECT * FROM patient_consultations WHERE queued_doctor_id IN ({$resultQueueDoc}) AND is_active = 1 ORDER BY created DESC");
                                while ($rowDocCon = mysql_fetch_array($queryDocCon)) {
                                    if($index>1){
                                        $imgCondition = "plus.gif";
                                        $hidden = "display:none;";
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <h1 class="headerPastHistory" style="cursor: pointer;" rel="<?php echo $rowDocCon['id']; ?>" >
                                                <img class="<?php echo $btnPlusMinus; ?>" src="<?php echo $this->webroot; ?>img/<?php echo $imgCondition; ?>" alt="">&nbsp;
                                                <span><?php echo date('d/m/Y H:i:s', strtotime($rowDocCon['created'])); ?></span>
                                            </h1>
                                        </td>
                                    </tr>
                                    <tr class="getPastDetail<?php echo $rowDocCon['id']; ?>" style="<?php echo $hidden; ?>">                                    
                                        <td style="width: 80%;">
                                            <table style="width: 100%;">
                                                <tr>
                                                    <td style="width:20%; border: none"><?php echo PAST_MEDICAl_HISTORY ." : "?></td>
                                                    <td style="border: none"><?php echo nl2br($rowDocCon['past_medical_history']); ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="width:20%; border: none"><?php echo MEDICAL_SURGERY_HISTORY ." : "?></td>
                                                    <td style="border: none"><?php echo nl2br($rowDocCon['medical_surgery']); ?></td>
                                                </tr>
                                                <tr style="display: none;">
                                                    <td style="widht:20%; border: none"><?php echo OBSTETRIC_GYNECOLOGIE_HISTORY .":"?></td>                            
                                                    <td style="border: none"><?php echo nl2br($rowDocCon['obstric_gynecologie']); ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="widht:20%; border: none"><?php echo TABLE_FAMILY_HISTORY." : "; ?> </td>
                                                    <td style="border: none"><?php echo nl2br($rowDocCon['family_history']); ?> </td>
                                                </tr>
                                            </table>   
                                        </td>
                                    </tr>
                                    <?php
                                    $index++;
                                }
                            }
                            ?>
                        </table>
                    </div>
                </td>
            </tr>
        </table>        
    </div>
    <br /> 
</div>
<!-- Present Illnss-->
<div class="legend" style="display: none;">
    <div class="legend_title"><label><b><?php echo TABLE_CURRENT_TREATMENT_AND_MEDICAL_STATUS; ?></b></label></div>
    <div class="legend_content" style="display: none;">
        <table style="width: 100%">
            <tr style="display: none;">
                 <td style="width: 20%;"><label for=""><?php echo TABLE_TREAT; ?> :</label></td>
                <td>
                    <?php echo $this->Form->input('treat', array('label' => false, 'type' => 'textarea')); ?>                    
                </td>
            </tr>
            <tr>
                <td style="width: 20%;"><label for="PatientConsultationPresentIllness"><?php echo MENU_PRESENT_ILLNESS; ?> :</label></td>
                <td>
                    <?php echo $this->Form->input('present_illness', array('label' => false, 'type' => 'textarea')); ?>                    
                </td>
            </tr>
             <tr>
                 <td style="width: 20%;"><label for="PatientConsultationMedication"><?php echo MENU_MEDICATION; ?> :</label></td>
                <td>
                    <?php echo $this->Form->input('medication', array('label' => false, 'type' => 'textarea')); ?>                    
                </td>
            </tr>
            <tr>
                <td style="width: 20%;"><label for="PatientConsultationAllergies"><?php echo MENU_ALLERGIES; ?> :</label></td>
                <td>
                    <?php echo $this->Form->input('allergies', array('label' => false, 'type' => 'textarea')); ?>                    
                </td>
            </tr>
            <tr style="display: none;">
                <td style="width: 20%;"><label><?php echo MENU_TOBACCO_ALCOHOL; ?> :</label></td>
                <td>
                    <?php echo $this->Form->input('tob_alch', array('options' => array(1 => 'NO', 2 => 'T', 3 => 'A'),'label' => false, 'class' => 'chzn-select validate[required]', 'multiple' => true , 'style' => 'width:400px')); ?>                    
                </td>
            </tr>
            <tr style="display: none;">
                <td style="width: 20%;"><label for="PatientConsultationAllergies"><?php echo MENU_TOBACCO_ALCOHOL_DESCRIPTION; ?> :</label></td>
                <td>
                    <?php echo $this->Form->input('tob_alch_description', array('label' => false, 'type' => 'textarea')); ?>                    
                </td>
            </tr>
        </table>        
    </div>
</div>
<div style="width: 100%;">
    <!-- Daignostic -->
    <div class="legend" style="width: 33%; float: left; padding: 2px;">
        <div class="legend_title"><label for="PatientConsultationDaignostic"><b><?php echo TABLE_DAIGNOSTIC; ?></b></label></div>
        <div class="legend_content" style="min-height: 100px;">
            <table style="width: 100%">
                <tr>
                    <td>
                        <?php echo $this->Form->input('patient_diagnostic', array('empty' => SELECT_OPTION, 'label' => false ,  'style' => 'width: 150px;')); ?>   
                        <?php echo $this->Form->input('daignostic', array('label' => false, 'type' => 'textarea')); ?>
                    </td>
                </tr>
            </table>
        </div> 
    </div>
    <div class="legend" style="width: 33%; float: left; padding: 2px;">
        <div class="legend_title"><label for="PatientConsultationRemark"><b><?php echo MENU_REMARKS; ?></b></label></div>
        <div class="legend_content" style="min-height: 100px;">
            <table style="width: 100%">
                <tr>
                    <td>
                        <?php echo $this->Form->input('doctor_comment', array('empty' => SELECT_OPTION, 'label' => false ,  'style' => 'width: 150px;')); ?>  
                        <?php echo $this->Form->input('remark', array('label' => false, 'type' => 'textarea')); ?>
                    </td>
                </tr>
            </table>
        </div>        
    </div>
    <div class="legend" style="width: 33%; float: left; padding: 2px;">
        <div class="legend_title"><label><b><?php echo MENU_APPOINTMENT_MANAGEMENT; ?></b></label></div>
        <div class="legend_content" style="min-height: 100px;">
            <table style="width: 100%">
                <tr>
                    <td style="width: 30%;"><label for="PatientConsultationAppDate"><?php echo APPOINTMENT_DATE; ?> :</label></td>
                    <td>
                        <?php echo $this->Form->text('app_date', array('style' => 'width: 97% !important')); ?>
                        <input type="hidden" type="text" id="patient_id" name="patient_id" value="<?php echo $patient['Patient']['id']; ?>" />
                        <input type="hidden" type="text" id="queue_id" name="queue_id" value="<?php echo $patient['Queue'][0]['id'];?>" />
                        <input type="hidden" type="text" id="doctor_id" name="doctor_id" value="<?php echo $patient['QeuedDoctor']['id'];?>" /> 
                    </td>
                </tr>
                <tr>
                    <td><label for="PatientConsultationDescription"><?php echo TABLE_FOR; ?> :</label></td>
                    <td><?php echo $this->Form->textarea('description', array('style' => 'width: 97% !important')); ?></td>
                </tr>
            </table>        
        </div>        
    </div>    
</div>
<div class="clear"></div>
<div class="legend" style="display: none;">
    <div class="legend_title"><label for="PatientConsultationTreatment"><b><?php echo MENU_TREATMENT; ?></b></label></div>
    <div class="legend_content" style="display: none;"><?php echo $this->Form->input('treatment', array('label' => false,  'type' => 'textarea')); ?></div>   
</div> 
<div style="width: 100%;">
    <div class="legend" style="width: 33%; float: left; padding: 2px;">
        <div class="legend_title"><label for="PatientConsultationFollowUp"><b><?php echo MENU_FOLLOW_UP; ?></b></label></div>
        <div class="legend_content" style="height: 100px;">
            <?php echo $this->Form->input('daily_clinical_report', array('empty' => SELECT_OPTION, 'label' => false, 'style' => 'width: 150px;')); ?>  
            <?php echo $this->Form->input('follow_up', array('label' => false, 'type' => 'textarea', 'style' => 'width: 98% !important; height: 60px ! important;')); ?>
        </div>
    </div>
    <div class="legend" style="width: 33%; float: left; padding: 2px; display: none;">
        <div class="legend_title"><label><b><?php echo MENU_PATIENT_IPD; ?></b></label></div>
        <div class="legend_content" style="height: 100px;">
            <table style="width: 100%">
                <tr>
                    <td style="width: 30%;"><label for="PatientConsultationRoomId"><?php echo TABLE_ROOM_NUMBER; ?> :</label></td>
                    <td>
                        <select id="PatientConsultationRoomId" name="data[PatientIpd][room_id]" class="classRoom" style="width: 150px;">
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
                                echo '<option '.$disabled.' style="'.$ipdStatus.'" class="'.$room['Room']['company_id'].'" value="'.$room['Room']['id'].'">'.$room['Room']['room_name'].'-'.$room['RoomType']['name'].'</option>';
                            }
                            ?>
                        </select>  
                    </td>
                </tr>
                <tr style="display: none;">
                    <td><label for="PatientConsultationAllergies"><?php echo TABLE_ALLERGIC; ?> :</label></td>
                    <td><?php echo $this->Form->textarea('allergies', array('name' => 'data[PatientIpd][allergies]', 'style' => 'width: 97% !important')); ?></td>
                </tr>
            </table>        
        </div>        
    </div>
    <div class="legend" style="width: 33%; float: left; padding: 2px;">
        <div class="legend_title"><label><b><?php echo TABLE_PATIENT_STATUS; ?></b></label></div>
        <div class="legend_content" style="height: 100px;">
            <table style="width: 100%">
                <tr>
                    <td style="width: 30%;"><label for="PatientConsultationConsultStatus"><?php echo TABLE_STATUS; ?> :</label></td>
                    <td>
                        <select id="PatientConsultationConsultStatus" name="data[PatientConsultation][consult_status]" style="width: 150px;">
                            <option selected="selected" value="1">Patient OPD</option>
                            <option value="2">Patient IPD</option>
                        </select>  
                    </td>
                </tr>
                <tr id="checkRoomId" style="display: none;">
                    <td><label for="PatientConsultationCheckRoomId"><?php echo TABLE_ROOM_NUMBER; ?><span class="red">*</span> :</label></td>
                    <td>
                        <select id="PatientConsultationCheckRoomId" name="data[PatientConsultation][room_id]" class="validate[required]" style="width: 150px;">
                            <option value=""><?php echo SELECT_OPTION;?></option>
                            <?php
                            foreach ($rooms as $room) {
                                echo '<option value="'.$room['Room']['id'].'">'.$room['Room']['room_name'].'</option>';
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
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <?php echo ACTION_SAVE; ?>
    </button>
    <img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" class="loading" style="display: none;" />
</div>
<div style="clear: both;"></div>
<?php echo $this->Form->end(); ?>


