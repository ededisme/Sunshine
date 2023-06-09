<?php 
echo $this->element('prevent_multiple_submit');
$absolute_url = FULL_BASE_URL . Router::url("/", false);
echo $javascript->link('uninums.min'); 
?>
<style type="text/css">
    .input{
        float:left;
    }
    #PatientCompanyInsuranceId{
        display: none;
    }
</style>
<script type="text/javascript">
    $(document).ready(function(){  
        //$(".chzn-select").chosen();
        $("#PatientDoctorId").chosen({width: 200});
        selectCompanyInsurance();
        allerdicMedicineCheck();
        allerdicFoodCheck();
        // Prevent Key Enter
        preventKeyEnter();
        $("#PatientEditForm").validationEngine();
        $("#PatientEditForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSavePatient").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            beforeSerialize: function($form, options) {
                $("#PatientRegisterDate").datepicker("option", "dateFormat", "yy-mm-dd");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif"); 
                $(".btnBackPatient").dblclick();                
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
        
        if($("#PatientPatientGroupId").val()==2){
            $("#PatientNationality").show();
        }else if($("#PatientPatientGroupId").val()==1){
            $("#PatientNationality").find("option[value='']").attr("selected", true);
            $("#PatientNationality").hide();
        }else{
            $("#PatientNationality").find("option[value='']").attr("selected", true);
            $("#PatientNationality").hide();
        }
        
        $("#PatientPatientGroupId").change(function(){
            if($("#PatientPatientGroupId").val()==2){
                $("#PatientNationality").show();
            }else if($("#PatientPatientGroupId").val()==1){
                $("#PatientNationality").find("option[value='']").attr("selected", true);
                $("#PatientNationality").hide();
            }else{
                $("#PatientNationality").find("option[value='']").attr("selected", true);
                $("#PatientNationality").hide();
            }
        });                
            
        $("#PatientPatientBillTypeId").change(function(){
            selectCompanyInsurance();
        });
              
        // check patient insurance
        if($("#PatientPatientBillTypeId").val()==3){ 
            $("#PatientCompanyInsuranceId").show();
            if($("#PatientCompanyInsuranceId").val()>0){
                $("#insuranceNote").show();
            }
            
        }      
              
        $("#PatientCompanyInsuranceId").change(function(){
            if($("#PatientCompanyInsuranceId").val()==0 && $("#PatientCompanyInsuranceId").val()!=""){
                $("#insuranceNote").show();
            }else{
                $("#insuranceNote").hide();
            }
        });
        
        $("#PatientDob" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
            yearRange: '-100:-0',
            maxDate: 0,
            beforeShow: function(){
                setTimeout(function(){
                    $("#ui-datepicker-div").css("z-index", 1000);
                }, 10);
            }
        }).unbind("blur");
        
        $("#PatientDob").change(function(){
            var dob = $("#PatientDob").val();
                dob = dob.substr(0, 10).split("-");
                dob = dob[1] + "/" + dob[2] + "/" + dob[0];
            var age = getAge(dob);
                age = age.substr(0, 10).split(",");
                $('#PatientAge').val(age[0]);
                $('#PatientAgeMonth').val(age[1]);
                if(age[2]>0){
                    $('#PatientAgeDay').val(age[2]-1);
                }else{
                    $('#PatientAgeDay').val(age[2]);
                }
        });  
        
        if($("#PatientDob").val()!="0000-00-00"){
            var dob = $("#PatientDob").val();
                dob = dob.substr(0, 10).split("-");
                dob = dob[1] + "/" + dob[2] + "/" + dob[0];
            var age = getAge(dob);
                age = age.substr(0, 10).split(",");
                $('#PatientAge').val(age[0]);
                $('#PatientAgeMonth').val(age[1]);
                if(age[2]>0){
                    $('#PatientAgeDay').val(age[2]-1);
                }else{
                    $('#PatientAgeDay').val(age[2]);
                }
        }
        
        $('#PatientAge').attr('autocomplete', 'off');
        
        $("#PatientAge").keyup(function(){
            var now = (new Date()).getFullYear();
            var age = parseUniInt($("#PatientAge").val());
            var year = now - age;
            var dob = year + '-01-01';
            $('#PatientDob').val(dob);
            $('#PatientAgeMonth').val('');
            $('#PatientAgeDay').val('');            
            getAgePatient(dob);                        
        });
        
        $("#PatientAllergicMedicine").click(function(){
           allerdicMedicineCheck();
        });
        $("#PatientAllergicFood").click(function(){
           allerdicFoodCheck();
        });
        $("#PatientUnknownAllergic").click(function(){
           allerdicUnknowCheck();
        });
        
        $(".btnBackPatient").dblclick(function(event){
            event.preventDefault();
            $('#PatientEditForm').validationEngine('hideAll');
            oCache.iCacheLower = -1;
            oTablePatient.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });     
        
        $("#PatientRegisterDate").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd/mm/yy',
            maxDate: 0
        }).unbind("blur");

        // Action Address
        // Province
        // $(".province").click(function(){
        //     if($(this).val()!=""){
        //         $(".district").val('');
        //         $(".district option[class!='']").hide();
        //         $(".district option[class='"  + $(this).val() + "']").show();
        //     }else{
        //         $(".district").val('');
        //         $(".commune").val('');
        //         $(".village").val('');
        //         $(".district option").show();
        //         $(".commune option").show();
        //         $(".village option").show();
        //     }
        //     comboRefesh(".district",".province");
        // });
        // // District
        // $(".district").change(function(){
        //     if($(this).val()!=""){
        //         $(".province").val($(".district").find("option:selected").attr("class"));
        //         $(".commune").val('');
        //         $(".commune option[class!='']").hide();
        //         $(".commune option[class='"  + $(this).val() + "']").show();
        //     }else{
        //         $(".commune").val('');
        //         $(".village").val('');
        //         $(".commune option").show();
        //         $(".village option").show();
        //     }
        //     comboRefesh(".commune",".district");
        // });
        // // Commune
        // $(".commune").change(function(){
        //     if($(this).val()!=""){
        //         $(".village").val('');
        //         $(".village option[class!='']").hide();
        //         $(".village option[class='"  + $(this).val() + "']").show();
        //     }else{
        //         $(".village").val('');
        //         $(".village option").show();
        //     }
        // });


        $("#PatientProvinceId").click(function(){
            if($(this).val()!=""){
                $("#PatientDistrictId").val('');
                $("#PatientDistrictId option[class!='']").hide();
                $("#PatientDistrictId option[class='"  + $(this).val() + "']").show();
            }else{
                $("#PatientDistrictId").val('');
                $("#PatientCommuneId").val('');
                $("#PatientVillageId").val('');
                $("#PatientDistrictId option").show();
                $("#PatientCommuneId option").show();
                $("#PatientVillageId option").show();
            }
            comboRefesh("#PatientDistrictId","#PatientProvinceId");
        });
        // District
        $("#PatientDistrictId").change(function(){
            if($(this).val()!=""){
                $("#PatientProvinceId").val($("#PatientDistrictId").find("option:selected").attr("class"));
                $("#PatientCommuneId").val('');
                $("#PatientCommuneId option[class!='']").hide();
                $("#PatientCommuneId option[class='"  + $(this).val() + "']").show();
            }else{
                $("#PatientCommuneId").val('');
                $("#PatientVillageId").val('');
                $("#PatientCommuneId option").show();
                $("#PatientVillageId option").show();
            }
            comboRefesh("#PatientCommuneId","#PatientDistrictId");
        });
        // Commune
        $("#PatientCommuneId").change(function(){
            if($(this).val()!=""){
                $("#PatientVillageId").val('');
                $("#PatientVillageId option[class!='']").hide();
                $("#PatientVillageId option[class='"  + $(this).val() + "']").show();
            }else{
                $("#PatientVillageId").val('');
                $("#PatientVillageId option").show();
            }
        });
    }); 
    
    function getAgePatient(dob = null){            
        dob = dob.substr(0, 10).split("-");
        dob = dob[1] + "/" + dob[2] + "/" + dob[0];
        var age = getAge(dob);
            age = age.substr(0, 10).split(",");
            $('#PatientAge').val(age[0]);
            $('#PatientAgeMonth').val(age[1]);
            if(age[2]>0){
                $('#PatientAgeDay').val(age[2]-1);
            }else{
                $('#PatientAgeDay').val(age[2]);
            }
    }
    
    function allerdicMedicineCheck(){
        if($("#PatientAllergicMedicine").is(":checked")){
            $("#PatientAllergicMedicine").val('1');
            $("#PatientAllergicMedicineNote").show();
            $("#inpShow").hide();
        }else{
            $("#PatientAllergicMedicine").val('0');
            $("#PatientAllergicMedicineNote").hide();
            $("#PatientAllergicMedicineNote").text('');
            $("#inpShow").show();
        }
    }
    function allerdicFoodCheck(){
        if($("#PatientAllergicFood").is(":checked")){
            $("#PatientAllergicFood").val('1');
            $("#PatientAllergicFoodNote").show();
        }else{
            $("#PatientAllergicFood").val('0');
            $("#PatientAllergicFoodNote").hide();
            $("#PatientAllergicFoodNote").text('');
        }
    }
    
    function allerdicUnknowCheck(){
        if($("#PatientUnknownAllergic").is(":checked")){
            $("#PatientUnknownAllergic").val('1');
        }else{
            $("#PatientUnknownAllergic").val('0');
        }
    }
    
    function selectCompanyInsurance(){
        var patientBillType = $("#PatientPatientBillTypeId").val();            
        if(patientBillType==3){
            $("#PatientCompanyInsuranceId").show();
            if($("#PatientCompanyInsuranceId").val()==0 && $("#PatientCompanyInsuranceId").val()!=""){
                $("#insuranceNote").show();
            }else{
                $("#insuranceNote").hide();
            }
        }else{
            $("#PatientCompanyInsuranceId").hide();
            $("#PatientCompanyInsuranceId").css("display","none");
        }       
    }
    function isNumberKey(event){
        var charCode = (event.which)?event.which : event.keyCode;
        if ((charCode > 31 && (charCode < 46 || charCode > 57))|| charCode === 47){
            return false;
        }
        return true;
    }
    function getAge(dateString) {
        var now = new Date();
        var today = new Date(now.getYear(),now.getMonth(),now.getDate());

        var yearNow = now.getYear();
        var monthNow = now.getMonth();
        var dateNow = now.getDate();

        var dob = new Date(dateString.substring(6,10),
                           dateString.substring(0,2)-1,                   
                           dateString.substring(3,5)                  
                           );

        var yearDob = dob.getYear();
        var monthDob = dob.getMonth();
        var dateDob = dob.getDate();
        var age = {};
        var ageString = "";
        var yearString = "";
        var monthString = "";
        var dayString = "";
        yearAge = yearNow - yearDob;

        if (monthNow >= monthDob)
          var monthAge = monthNow - monthDob;
        else {
          yearAge--;
          var monthAge = 12 + monthNow -monthDob;
        }

        if (dateNow >= dateDob)
          var dateAge = dateNow - dateDob;
        else {
          monthAge--;
          var dateAge = 31 + dateNow - dateDob;

          if (monthAge < 0) {
            monthAge = 11;
            yearAge--;
          }
        }

        age = {
            years: yearAge,
            months: monthAge,
            days: dateAge
        };

        if ( age.years > 1 ) yearString = " years";
        else yearString = " year";
        if ( age.months> 1 ) monthString = " months";
        else monthString = " month";
        if ( age.days > 1 ) dayString = " days";
        else dayString = " day";

        if ( (age.years > 0) && (age.months > 0) && (age.days > 0) )
            ageString = age.years + ", " + age.months + ", " + age.days ;
        else if ( (age.years == 0) && (age.months == 0) && (age.days > 0) )
          ageString = "0,0, " + age.days;
        else if ( (age.years > 0) && (age.months == 0) && (age.days == 0) )
          ageString = age.years  + ",0,0";
        else if ( (age.years > 0) && (age.months > 0) && (age.days == 0) )
          ageString = age.years + "," + age.months +",0";
        else if ( (age.years == 0) && (age.months > 0) && (age.days > 0) )
          ageString = "0, " + age.months + ", " + age.days ;
        else if ( (age.years > 0) && (age.months == 0) && (age.days > 0) )
          ageString = age.years + " ,0, " + age.days ;
        else if ( (age.years == 0) && (age.months > 0) && (age.days == 0) )
          ageString = "0,"+age.months + ",0";
        else ageString = "0,0,0";

        return ageString;
    }
    
</script>
<div style="padding: 5px;border: 1px dashed #3C69AD;">
    <div class="buttons">
        <a href="#" class="positive btnBackPatient">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('Patient'); ?>
<?php echo $this->Form->input('id'); ?>
<?php
$age= '';
$day = '';
$month = '';
?>
<fieldset style="padding: 5px; border: 1px dashed #3C69AD;">
    <legend style="background: #EDEDED; font-weight: bold;"><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?></legend>
    <table style="margin-left: 10px; width: 97%; border-spacing: 0em 0.2em; border-collapse: separate;" cellspacing="2" cellpadding="2">
        <tr>
            <td style="width: 10%;"><?php echo PATIENT_CODE; ?> <span class="red">*</span> :</td>
            <td style="width: 35%;">                
                <?php echo $code; ?>
                <input name="data[Patient][patient_code]" type="hidden" value="<?php echo $code;?>"/>
                <input name="data[Patient][patient_group]" type="hidden" value="1"/>
            </td>
            <td style="width: 6%;"></td>
            <td style="width: 10%;"></td>
            <td style="width: 30%;"></td>
        </tr>
        <tr>
        <td style="width: 10%;"><?php echo PATIENT_REGISTER; ?> <span class="red">*</span> :</td>
            <td style="width: 35%;">
                <?php echo $this->Form->text('register_date', array('value' => date("d/m/Y"), 'label' => false, 'class' => 'validate[required]', 'readonly' => true, 'style' => 'width: 283px')); ?>
            </td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td><label for="PatientName"><?php echo PATIENT_NAME; ?> <span class="red">*</span> :</label></td>
            <td><?php echo $this->Form->text('patient_name', array('class' => 'validate[required]')); ?></td>
            <td> </td>
            <td><label for="PatientSex"><?php echo TABLE_SEX; ?> <span class="red">*</span> :</label></td>
            <td><?php echo $this->Form->input('sex', array('empty' => SELECT_OPTION, 'label' => false, 'class' => 'validate[required]', 'style' => 'width: 200px;')); ?></td>
        </tr>
        <tr>
            <td><label for="PatientDob"><?php echo TABLE_DOB; ?> <span class="red">*</span> :</label></td>
            <td>
                <?php echo $this->Form->text('dob', array('style'=>'width: 13%;', 'readonly' => true, 'class' => 'validate[required]','onkeypress'=>'return isNumberKey(event)')); ?>
                &nbsp;
                <label for="PatientAge" style="margin-left: 2px;"><?php echo TABLE_AGE; ?>:</label>
                <?php echo $this->Form->text('age',array('style'=>'width: 20px;', 'class' => 'number validate[required]', 'maxlength' => '3')); ?>
                &nbsp;
                <?php echo TABLE_AGE_MONTH;?>
                <?php echo $this->Form->text('age_month',array('style'=>'width:20px;', 'readonly'=>'readonly','disabled'=> true)); ?>
                &nbsp;
                <?php echo TABLE_AGE_DAY;?>
                <?php echo $this->Form->text('age_day',array('style'=>'width:20px;', 'readonly'=>'readonly', 'disabled'=> true)); ?>
            <td></td>
            <td><label for="PatientNationality"><?php echo TABLE_NATIONALITY; ?> :</td>
            <td>
                <?php echo $this->Form->input('patient_group_id', array('empty' => SELECT_OPTION, 'label' => false, 'default' => '1', 'class' => 'validate[required]', 'style' => 'width: 200px;')); ?>
                &nbsp;&nbsp;
                <?php echo $this->Form->input('nationality', array('empty' => SELECT_OPTION, 'label' => false,'class' => 'validate[required]', 'style' => 'width: 200px; display:none;')); ?>
            </td>
        </tr>
        <tr>
            <td><label for="PatientTelephone"><?php echo TABLE_TELEPHONE; ?>: </label> <span class="red">*</span> :</label></td>
            <td><?php echo $this->Form->text('telephone', array('class' => 'validate[required,custom[phone]]')); ?></td>
            <td></td>
            <td><label for="PatientTypeID"><?php echo PATIENT_TYPE; ?> :</label></td>
            <td>
                <?php echo $this->Form->input('patient_type_id', array('empty' => SELECT_OPTION, 'default'=> '2', 'label' => false, 'style' => 'width: 200px;')); ?>
            </td>
        </tr>
        <tr>
            <td><label for="father_name"><?php echo TABLE_FATHER_NAME; ?> :</label></td>
            <td><?php echo $this->Form->text('father_name'); ?></td>
            <td></td>
            <td><label for=""><?php echo TABLE_REFERRAL; ?> :</label></td>
            <td><?php echo $this->Form->input('referral_id', array('empty' => SELECT_OPTION, 'label' => false, 'style' => 'width: 200px;')); ?>
            </td>
        </tr>
        <tr>
            <td><label for="mother_name"><?php echo TABLE_MOTHER_NAME; ?> :</label></td>
            <td><?php echo $this->Form->text('mother_name'); ?></td>
            <td></td>
            <td><label for="PatientDoctorId"><?php echo DOCTOR_DOCTOR; ?> :</label></td>
            <td>
                <select id="PatientDoctorId" name="data[Patient][doctor_id]">
                    <option value=""><?php echo SELECT_OPTION;?></option>
                    <?php 
                        foreach ($doctors as $doctor) {
                            echo '<option value="'.$doctor['User']['id'].'">'.$doctor['Employee']['name'].'</option>';
                        }
                    ?>
                </select>
            </td>
        </tr>
        <tr style='display : none;'>
            <td><?php echo $this->Form->text('religion'); ?></td>
            <td><?php echo $this->Form->text('father_occupation'); ?></td>
            <td><?php echo $this->Form->text('mother_occupation'); ?></td>
            <td><?php echo $this->Form->text('address'); ?></td>
            <td><?php echo $this->Form->text('occupation'); ?></td>
        </tr>
        <tr>
            <td colspan="2">
                <fieldset>
                    <legend><?php echo TABLE_ADDRESS; ?> <span class="red">*</span></legend>
                    <table cellpadding="3" cellspacing="0" style="width: 100%;">
                        <tr>
                            <td style="width: 18%;"><label for="PatientProvinceId"><?php echo TABLE_PROVINCE; ?> :
                                </label></td>
                            <td style="width: 33%;">
                                <div class="inputContainer">
                                    <?php echo $this->Form->input('province_id', array('empty' => INPUT_SELECT, 'class'=>'province', 'label'=>false, 'style' => 'width: 200px;')); ?>
                                </div>
                            </td>
                            <td style="width: 3%;"></td>
                            <td style="width: 10%;"><label for="EmployeeDistrictId"><?php echo TABLE_DISTRICT; ?> :
                                </label></td>
                            <td style="width: 30%;">
                                <div class="inputContainer">
                                    <?php echo $this->Form->input('district_id', array('empty' => INPUT_SELECT, 'class'=>'district', 'label'=>false, 'style' => 'width: 200px;')); ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="PatientCommuneId"><?php echo TABLE_COMMUNE; ?> : </label></td>
                            <td>
                                <div class="inputContainer">
                                    <?php echo $this->Form->input('commune_id', array('empty' => INPUT_SELECT, 'class'=>'commune', 'label'=>false, 'style' => 'width: 200px;')); ?>
                                </div>
                            </td>
                            <td></td>
                            <td><label for="PatientVillageId"><?php echo TABLE_VILLAGE; ?> : </label>
                            </td>
                            <td>
                                <div class="inputContainer">
                                    <?php echo $this->Form->input('village_id', array('empty' => INPUT_SELECT,  'class'=>'village', 'label'=>false, 'style' => 'width: 200px;')); ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
            <td></td>
            <td><?php echo TABLE_PATIENT_STATUS; ?> :</td>
            <td>
                <input name="data[Patient][allergic_medicine]" type="checkbox" id="PatientAllergicMedicine" value="1" />
                <label style="padding-right: 20px;" for="PatientAllergicMedicine"><?php echo TABLE_ALLERGIC; ?></label>
                <?php if($_SESSION['lang']=="kh"){ echo '<br/>';}?>
                <div style="display: none;">
                    <input name="data[Patient][allergic_food]" type="checkbox" id="PatientAllergicFood" value="1" />
                    <label style="padding-right: 20px;"
                        for="PatientAllergicFood"><?php echo TABLE_ALLERGIC_FOOD; ?></label>
                    <?php if($_SESSION['lang']=="kh"){ echo '<br/>';}?>
                </div>
                <input name="data[Patient][unknown_allergic]" type="checkbox" id="PatientUnknownAllergic" value="1" />
                <label for="PatientUnknownAllergic"><?php echo TABLE_UNKNOWN_ALLERGIC; ?></label>
            </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>
                <div style="float:left; padding-right: 10px;">
                    <input type="text" id="inpShow" style="width:140px;height:25px;border: none;background-image: none;"
                        readonly="true">
                    <?php echo $this->Form->textarea('allergic_medicine_note', array('label' => false, 'style' => 'width:140px;height:25px;display:none;')); ?>
                </div>
                <div style="width: 10%;"></div>
                <div style="float:left; width: 50%;">
                    <?php echo $this->Form->textarea('allergic_food_note', array('label' => false, 'style' => 'width:140px;height:25px;display:none;')); ?>
                </div>
            </td>
        </tr>
    </table>
    <fieldset style="display: none;">
        <legend><?php __(HOW_TO_KNOW_OUR_HOSPITAL); ?></legend>
        <?php         
        foreach ($patientConnections as $patientConnection) {
            if(in_array($patientConnection['PatientConnectionWithHospital']['id'], $patientConnectionDetails) ){
                echo '<input checked="true" id="PatientConnectionWithHospital'.$patientConnection['PatientConnectionWithHospital']['id'].'" name="data[Patient][patient_conection_id][]" type="checkbox" value="'.$patientConnection['PatientConnectionWithHospital']['id'].'"/>';
            }else{
                echo '<input id="PatientConnectionWithHospital'.$patientConnection['PatientConnectionWithHospital']['id'].'" name="data[Patient][patient_conection_id][]" type="checkbox" value="'.$patientConnection['PatientConnectionWithHospital']['id'].'"/>';
            }
            
            echo '<label style="padding-right: 20px;" for="PatientConnectionWithHospital'.$patientConnection['PatientConnectionWithHospital']['id'].'">'.$patientConnection['PatientConnectionWithHospital']['name'].'</label>';
        }
        ?>
    </fieldset>    
</fieldset>
<br/>
<div class="buttons">
    <button type="submit" class="positive savePatient">
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <span class="txtSavePatient"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<?php echo $this->Form->end(); ?>