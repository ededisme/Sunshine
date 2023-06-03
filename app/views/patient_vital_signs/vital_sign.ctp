<?php include('includes/function.php'); ?>
<?php
echo $this->element('prevent_multiple_submit');
$absolute_url = FULL_BASE_URL . Router::url("/", false);
echo $javascript->link('uninums.min');
?>
<style type="text/css">
    .input{
        float:left;
    }           
</style>
<script type="text/javascript">
    $(document).ready(function(){ 
        bmi();          
        $("#PatientVitalSignHeight,#PatientVitalSignWeight").keyup(function(){
            bmi();
        });      
                
        // Prevent Key Enter
        preventKeyEnter();
        $("#PatientVitalSignVitalSignForm").validationEngine();
        $("#PatientVitalSignVitalSignForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSavePatient").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $(".btnBackQueueNurse").click();
                // alert message
                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result+'</p>');
                $("#dialog").dialog({
                    title: '<?php echo DIALOG_INFORMATION; ?>',
                    resizable: false,
                    modal: true,
                    width: 'auto',
                    height: 'auto',
                    open: function(event, ui){
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
        
        $(".btnBackQueueNurse").click(function(event) {
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableQueueNurse.fnDraw(false);
            var rightPanel = $(this).parent().parent().parent();
            var leftPanel = rightPanel.parent().find(".leftPanel");
            rightPanel.hide();
            rightPanel.html("");
            leftPanel.show("slide", {direction: "left"}, 500);
        });
        
        //Hide Patinen Info
        $("#btnHidePatientInfo").click(function(){
            $("#patientInfo").hide(900);
            $("#showPatientInfo").show();
        });
        //Show Patinen Info
        $("#btnShowPatientInfo").click(function(){
            $("#patientInfo").show(900);
            $("#showPatientInfo").hide();
        });
    
    });
    function bmi(){
        var result = 0;
        var height = Number($("#PatientVitalSignHeight").val())/100;
        var textResult = '';
        result = Number($("#PatientVitalSignWeight").val())/(height*height).toFixed(2);
        if(Number($("#PatientVitalSignHeight").val())>0 && Number($("#PatientVitalSignWeight").val())>0){
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
            $(".PatientVitalSignBMI").val(textResult);
        }else{
            $(".BMI").text('');     
            $(".PatientVitalSignBMI").val(textResult);
        }
    }
</script>
<!-- <?php debug($patient)?> -->
<div style="padding: 5px; border: 1px dashed #3C69AD;">
    <div class="buttons">
        <a href="" class="positive btnBackQueueNurse">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('PatientVitalSign'); ?>
<?php echo $this->Form->input('id'); ?>
<input name="data[PatientVitalSignBloodPressure][id]" type="hidden" value="<?php echo $patient['PatientVitalSignBloodPressure']['id'];?>"/>
<input name="data[QeuedDoctor][id]" type="hidden" value="<?php echo $patient['QeuedDoctor']['id'];?>"/>
<input name="data[Queue][id]" type="hidden" value="<?php echo $patient['Queue'][0]['id'];?>"/>

<legend id="showPatientInfo" style="display:none;"><a href="#" id="btnShowPatientInfo" style="background: #CCCCCC; font-weight: bold;"><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?> [ Show ] </a> </legend>
<fieldset id="patientInfo" style="border: 1px dashed #3C69AD;">
    <legend><a href="#" id="btnHidePatientInfo" style="background: #CCCCCC; font-weight: bold;"> <?php echo MENU_PATIENT_MANAGEMENT_INFO; ?> [ Hide ] </a></legend>
    <table style="width: 100%;" cellspacing="3">
        <tr>
            <td style="width: 15%;"><?php echo PATIENT_CODE; ?> :</td>
            <td style="width: 35%;"><?php echo $patient['Patient']['patient_code']; ?></td>
            <td style="width: 15%;"><?php echo TABLE_DOB; ?> :</td>
            <td style="width: 35%;">
                <?php echo date("d/m/Y", strtotime($patient['Patient']['dob'])); ?>
                <?php echo TABLE_AGE; ?> :
                <?php
                if($patient['Patient']['dob']!="0000-00-00" || $patient['Patient']['dob']!=""){
                    echo getAgePatient($patient['Patient']['dob']);
                }                
                ?>
            </td>
        </tr>
        <tr>
            <td style="width: 15%;"><?php echo PATIENT_NAME; ?> :</td>
            <td>
                <?php echo $patient['Patient']['patient_name']; ?>
            </td>
            <td style="width: 15%;"><?php echo TABLE_NATIONALITY; ?> :</td>
            <td>
                <?php
                if ($patient['Patient']['patient_group_id'] != "") {
                    $query = mysql_query("SELECT name FROM patient_groups WHERE id=" . $patient['Patient']['patient_group_id']);
                    while ($row = mysql_fetch_array($query)) {
                        if ($patient['Patient']['patient_group_id'] == 1) {
                            echo $row['name'];
                        } else {
                            echo $row['name'] . '&nbsp;&nbsp;(' . $patient['Nationality']['name'] . ')';
                        }
                    }
                } else {
                    echo $patient['Nationality']['name'];
                }
                ?>
            </td>
        </tr>      
        <tr>
            <td style="width: 15%;"><?php echo TABLE_SEX; ?> :</td>
            <td>
                <?php
                if ($patient['Patient']['sex'] == "F") {
                    echo GENERAL_FEMALE;
                } else {
                    echo GENERAL_MALE;
                }
                ?>
            </td>
            <td style="width: 15%;"><?php echo TABLE_EMAIL; ?> :</td>
            <td>
                <?php echo $patient['Patient']['email']; ?>
            </td>            
        </tr>
        <tr>            
            <td style="width: 15%;"><?php echo TABLE_OCCUPATION; ?> :</td>
            <td>
                <?php echo $patient['Patient']['occupation']; ?>
            </td>
            <td style="width: 15%;"><?php echo TABLE_TELEPHONE; ?>:</td>
            <td>
                <?php echo $patient['Patient']['telephone']; ?>
            </td>
        </tr>        
        <tr>
            <td style="width: 15%;"><?php echo TABLE_ADDRESS; ?> :</td>
            <td>
                <?php echo $patient['Patient']['address']; ?>
            </td>
            <td style="width: 15%;"><?php echo TABLE_CITY_PROVINCE; ?> :</td>
            <td>
                <?php                
                if($patient['Patient']['location_id']!=""){
                    $query = mysql_query("SELECT name FROM patient_locations WHERE id=" . $patient['Patient']['location_id']);
                    while ($row = mysql_fetch_array($query)) {
                        echo $row['name'];
                    }
                }
                ?>
            </td>
        </tr>
    </table>     
</fieldset>
<br/>
<fieldset style="border: 1px dashed #3C69AD;">
    <legend><?php __(MENU_VITAL_SING_INFO); ?></legend>
    <table style="width: 100%;" cellspacing="0">
        <tr>
        <!-- Weight -->
            <td style="width: 5%;"><label for="PatientVitalSignWeight"><?php echo TABLE_WEIGHT; ?> <span class="red">*</span> :</label></td>
            <td style="width: 20%;"><?php echo $this->Form->text('weight', array('class' => 'float validate[required]', 'value' => $patient['PatientVitalSign']['weight'], 'style' => 'width: 150px;', 'autocomplete' => "off")); ?> kg</td>
        <!-- Temperature -->
            <td style="width: 6%;"><label for="PatientVitalSignTemperature"><?php echo TABLE_TEMPERATURE; ?> :</label></td>
            <td style="width: 20%;"><?php echo $this->Form->text('temperature', array('class' => 'float', 'value' => $patient['PatientVitalSign']['temperature'], 'style' => 'width: 150px;')); ?> °C</td>    
        <!-- Respiratory -->
            <td style="width: 6%;"><label for="PatientVitalSignRespiratory"><?php echo TABLE_RESPIRATORY; ?> :</label></td>
            <td style="width: 20%;"><?php echo $this->Form->text('respiratory', array('class' => 'float', 'value' => $patient['PatientVitalSign']['respiratory'], 'style' => 'width: 150px;')); ?> /m</td>
        <!-- Systilic    -->
            <td style="width:5%">Systolic</td>   
            <td style="width: 20%;"><?php echo $this->Form->text('resultSystolic1', array('class' => 'float', 'name'=>'data[PatientVitalSignBloodPressure][result_systolic_1]', 'value' => $patient['PatientVitalSignBloodPressure']['result_systolic_1'], 'style' => 'width: 150px;')); ?> /m</td>
            <!-- <td style="width:20%"><input style="height:20px; width: 150px;" id="resultSystolic1" name="data[PatientVitalSignBloodPressure][result_systolic_1]" value="<?php echo $patient['PatientVitalSignBloodPressure']['result_systolic_1']?>" class="float"/> mmHg</td> -->
        </tr>

        <tr>
        <!-- Height -->
            <td style="width: 5%;"><label for="PatientVitalSignHeight"><?php echo TABLE_HEIGHT; ?></td>
            <td style="width: 20%;"><?php echo $this->Form->text('height', array('class' => 'float', 'value' => $patient['PatientVitalSign']['height'], 'style' => 'width: 150px;', 'autocomplete' => "off")); ?> cm</td>
        <!-- Pulse -->
            <td style="width: 5%;"><label for="PatientVitalSignPulse"><?php echo TABLE_PULSE; ?> :</label></td>
            <td style="width: 20%;"><?php echo $this->Form->text('pulse', array('class' => 'float', 'value' => $patient['PatientVitalSign']['pulse'], 'style' => 'width: 150px;')); ?> /mn</td>
        <!-- SpO2 -->
            <td style="width: 6%;"><label for="PatientVitalSignSop2"><?php echo TABLE_SOP2; ?> :</label></td>
            <td style="width: 20%;"><?php echo $this->Form->text('sop2', array('value' => $patient['PatientVitalSign']['sop2'], 'style' => 'width: 150px;')); ?></td>
        <!-- Diastolic -->
            <td style="width: 6%;">Diastolic</td>          
            <td style="width: 20%;"><?php echo $this->Form->text('resultDiastolic1', array('value' => $patient['PatientVitalSignBloodPressure']['result_diastolic_1'], 'name'=>'data[PatientVitalSignBloodPressure][result_diastolic_1]', 'style' => 'width: 150px;')); ?></td>
            <!-- <td style="width: 20%;"><input style="height:20px; width: 150px;" id="resultDiastolic1" name="data[PatientVitalSignBloodPressure][result_diastolic_1]" value="<?php echo $patient['PatientVitalSignBloodPressure']['result_diastolic_1']?>" class="float"/> mmHg</td> -->

        </tr>
    </table>      
</fieldset>
<!-- <fieldset style="border: 1px dashed #3C69AD; display:none;">
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
            <td><input id="resultSystolic1" name="data[PatientVitalSignBloodPressure][result_systolic_1]" value="<?php echo $patient['PatientVitalSignBloodPressure']['result_systolic_1']?>" class="float"/> mmHg</td>
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

<br/>
<fieldset style="border: 1px dashed #3C69AD;">
     <legend>Other Description</legend>
     <table class="table" style="width: 100% ">
         <tr>
             <td>
                 <textarea style="width: 99%" name="data[PatientVitalSign][other_info]"><?php echo $patient['PatientVitalSign']['other_info']; ?></textarea>
             </td>
         </tr>
     </table>
</fieldset>


<div class="clear"></div>
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <span class="txtSavePatient"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<?php echo $this->Form->end(); ?>

