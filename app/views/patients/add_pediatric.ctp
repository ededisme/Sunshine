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
        
        // Prevent Key Enter
        preventKeyEnter();        
        $("#PatientAddPediatricForm").validationEngine();
        $("#PatientAddPediatricForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSavePatient").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");                
                var doctorId = $("#PatientDoctorId").val();                
                if(doctorId != ""){
                    $("#dialog").html('<div class="buttons"><button type="submit" class="positive printPatientPediatricNumber" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="txtPrintInvoice"><?php echo ACTION_PRINT_WAITING_NUMBER; ?></span></button></div>');
                    $(".printPatientPediatricNumber").click(function(){
                        $.ajax({
                            type: "POST",
                            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printDoctorWaiting/"+result,
                            beforeSend: function(){
                                $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                            },
                            success: function(printInvoiceResult){
                                w=window.open();
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
                       title: '<?php echo DIALOG_INFORMATION; ?>',
                       resizable: false,
                       modal: true,
                       width: 'auto',
                       height: 'auto',
                       position:'center',
                       closeOnEscape: true,
                       open: function(event, ui){
                           $(".ui-dialog-buttonpane").show(); $(".ui-dialog-titlebar-close").show();
                       },
                       close: function(){
                           $(this).dialog({close: function(){}});
                           $(this).dialog("close");
                           $(".btnBackPatientPediatric").dblclick();
                       },
                       buttons: {
                           '<?php echo ACTION_CLOSE; ?>': function() {
                               $("meta[http-equiv='refresh']").attr('content','0');
                               $(this).dialog("close");
                           }
                       }
                   });
                }
                $(".btnBackPatientPediatric").dblclick();
            }
        });
        $(".savePatientPediatric").click(function(){
            if(checkBfSavePatient() == true){
                return true;
            }else{
                return false;
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
        
        // check company insurance
        $("#PatientPatientBillTypeId").change(function(){
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
            }
        });
        $("#PatientCompanyInsuranceId").change(function(){
            if($("#PatientCompanyInsuranceId").val()==0 && $("#PatientCompanyInsuranceId").val()!=""){
                $("#insuranceNote").show();
            }else{
                $("#insuranceNote").hide();
            }
        });
        // close check company insurance
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
        if($("#PatientDob").val()!=''){
            var now = (new Date()).getFullYear();
            var age = now - $("#PatientDob").val().split("-",1);
            $('#PatientAge').val(age);
        }
        $("#PatientDob").change(function(){
            var now = (new Date()).getFullYear();
            var age = now - $("#PatientDob").val().split("-",1);
            $('#PatientAge').val(age);
        });
        $("#PatientAge").keyup(function(){
            var now = (new Date()).getFullYear();
            var age = parseUniInt($("#PatientAge").val());
            var year = now - age;
            if($("#PatientDob").val()!=''){
                var dob = year + $("#PatientDob").val().substr(-6);
            }else{
                var dob = year + '-01-01';
            }
            $('#PatientDob').val(dob);
        });
        
        $("#PatientAllergicMedicine").click(function(){
           allerdicMedicineCheck();
        });
        $("#PatientAllergicFood").click(function(){
           allerdicFoodCheck();
        });
        $(".btnBackPatientPediatric").dblclick(function(event){
            event.preventDefault();
            $('#PatientAddPediatricForm').validationEngine('hideAll');
            oCache.iCacheLower = -1;
            oTablePatientPediatrict.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });
    
    function allerdicMedicineCheck(){
        if($("#PatientAllergicMedicine").is(":checked")){
            $("#PatientAllergicMedicineNote").show();
            $("#inpShow").hide();
        }else{
            $("#PatientAllergicMedicineNote").hide();
            $("#inpShow").show();
        }
    }
    function allerdicFoodCheck(){
        if($("#PatientAllergicFood").is(":checked")){
            $("#PatientAllergicFoodNote").show();
        }else{
            $("#PatientAllergicFoodNote").hide();
        }
    }
    
    function checkBfSavePatient(){
        var formName = "#PatientAddPediatricForm";
        var validateBack =$(formName).validationEngine("validate");
        if(!validateBack){            
            return false;
        }else{            
            return true;
        }
    }
    
    function isNumberKey(event){
        var charCode = (event.which)?event.which : event.keyCode;
        if ((charCode > 31 && (charCode < 46 || charCode > 57))|| charCode === 47){
            return false;
        }
        return true;
    }
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="#" class="positive btnBackPatientPediatric">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('Patient'); ?>
<fieldset>
    <legend><?php __(MENU_PEDIATRIC_PATIENT_MANAGEMENT_INFO); ?></legend>
    <table style="width: 100%;" cellspacing="0">
        <tr>
            <td><?php echo PATIENT_CODE; ?> <span class="red">*</span> :</td>
            <td>                
                <?php echo $code; ?>
                <input name="data[Patient][patient_code]" type="hidden" value="<?php echo $code;?>"/>
                <input name="data[Patient][patient_group]" type="hidden" value="2"/>
            </td>
            <td><label for="PatientDob"><?php echo TABLE_DOB; ?> <span class="red">*</span> :</label></td>
            <td>
                <?php echo $this->Form->text('dob', array('class' => 'validate[required]','onkeypress'=>'return isNumberKey(event)')); ?>
                <label for="PatientAge"><?php echo TABLE_AGE; ?>:</label>
                <?php echo $this->Form->text('age',array('style'=>'width:30px;', 'class' => 'number validate[required,max[15]]', 'maxlength' => '2')); ?>
            </td>
        </tr>
        <tr>
            <td><label for="PatientPatientName"><?php echo PATIENT_NAME; ?> <span class="red">*</span> :</label></td>
            <td><?php echo $this->Form->text('patient_name', array('class' => 'validate[required]')); ?></td>
            <td><label for="PatientReligion"><?php echo TABLE_RELIGION; ?> :</label></td>
            <td><?php echo $this->Form->text('religion'); ?></td>
        </tr>      
        <tr>
            <td><label for="PatientSex"><?php echo TABLE_SEX; ?> <span class="red">*</span> :</label></td>
            <td><?php echo $this->Form->input('sex', array('empty' => SELECT_OPTION, 'label' => false, 'class' => 'validate[required]')); ?></td>
            <td><label for="PatientNationality"><?php echo TABLE_NATIONALITY; ?> <span class="red">*</span> :</label></td>
            <td>
                <?php echo $this->Form->input('patient_group_id', array('empty' => SELECT_OPTION, 'label' => false,'class' => 'validate[required]', 'style' => 'width:150px;')); ?>                
                <?php echo $this->Form->input('nationality', array('empty' => SELECT_OPTION, 'label' => false,'class' => 'validate[required]', 'style' => 'width:156px;display:none;')); ?>
            </td>
        </tr>
        <tr>
            <td><label for="PatientFatherName"><?php echo TABLE_FATHER_NAME; ?> :</label></td>
            <td><?php echo $this->Form->text('father_name'); ?></td>
            <td><label for="PatientOccupation"><?php echo TABLE_OCCUPATION; ?> :</label></td>
            <td><?php echo $this->Form->text('occupation'); ?></td>
        </tr>
        <tr>
            <td><label for="PatientMotherName"><?php echo TABLE_MOTHER_NAME; ?> :</label></td>
            <td><?php echo $this->Form->text('mother_name'); ?></td>
            <td><label for="PatientTelephone"><?php echo TABLE_TELEPHONE; ?>:</label></td>
            <td><?php echo $this->Form->text('telephone', array('class' => 'validate[custom[phone]]')); ?></td>
        </tr>
        <tr>
            <td><label for="PatientAddress"><?php echo TABLE_ADDRESS; ?> :</label></td>
            <td><?php echo $this->Form->text('address'); ?></td>
            <td><label for="PatientEmail"><?php echo TABLE_EMAIL; ?> :</label></td>
            <td><?php echo $this->Form->text('email', array('class' => 'validate[custom[email]]')); ?></td>
        </tr>
        <tr>   
            <td><label for="PatientLocationId"><?php echo TABLE_CITY_PROVINCE; ?><span class="red">*</span> :</label></td>
            <td><?php echo $this->Form->input('location_id', array('empty' => SELECT_OPTION, 'label' => false, 'class' => 'validate[required]')); ?></td>                        
            <td><label for="PatientRelationPatient"><?php echo TABLE_RELITION_PATIENT; ?> :</label></td>
            <td><?php echo $this->Form->text('relation_patient'); ?></td>
        </tr>  
        <tr>
            <td><label for="PatientCaseEmergencyTel"><?php echo TABLE_CASE_EMERGENCY_TEL; ?> :</label></td>
            <td><?php echo $this->Form->text('case_emergency_tel', array('class' => 'validate[custom[phone]]')); ?></td>
            <td><label for="PatientCaseEmergencyName"><?php echo TABLE_CASE_EMERGENCY_NAME; ?> :</label></td>
            <td><?php echo $this->Form->text('case_emergency_name'); ?></td>
        </tr>                                    
        <tr>
            <td><label for="PatientPatientBillTypeId"><?php echo TABLE_BILL_PAID_BY; ?><span class="red">*</span> :</label></td>
            <td>
                <?php echo $this->Form->input('patient_bill_type_id', array('empty' => SELECT_OPTION, 'label' => false, 'class' => 'validate[required]', 'style' => 'width:153px;')); ?>
                <?php echo $this->Form->input('company_insurance_id', array('empty' => SELECT_OPTION, 'label' => false, 'class' => 'validate[required]', 'style' => 'width:153px;')); ?>
            </td>
            <td><label for="PatientPatientTypeId"><?php echo TABLE_PATIENT_TYPE; ?> :</label></td>
            <td><?php echo $this->Form->input('patient_type_id', array('empty' => SELECT_OPTION, 'default' => '2' , 'label' => false)); ?></td>
        </tr>
        <tr id="insuranceNote" style="display: none;">
            <td><?php echo TABLE_NOTE;?></td>
            <td>
                <?php echo $this->Form->textarea('insurance_note', array('label' => false, 'style' => 'width:295px;')); ?>
            </td>
        </tr>
        <tr>    
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
            <td><?php echo TABLE_PATIENT_STATUS; ?></td>
            <td>
                <input name="data[Patient][allergic_medicine]" type="checkbox" id="PatientAllergicMedicine" value="1"/>
                <label style="padding-right: 20px;" for="PatientAllergicMedicine"><?php echo TABLE_ALLERGIC_MEDICINE; ?></label>
                <?php if($_SESSION['lang']=="kh"){ echo '<br/>';}?>
                <input name="data[Patient][allergic_food]" type="checkbox" id="PatientAllergicFood" value="1"/>
                <label for="PatientAllergicFood"><?php echo TABLE_ALLERGIC_FOOD; ?></label>
            </td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td>
                <div style="float:left;width: 33%;">
                    <input type="text" id="inpShow" style="width:140px;height:25px;border: none;background-image: none;" readonly="true">
                    <?php echo $this->Form->textarea('allergic_medicine_note', array('label' => false, 'style' => 'width:140px;height:25px;display:none;')); ?>
                </div>
                <div style="float:left;width: 45%;">
                    <?php echo $this->Form->textarea('allergic_food_note', array('label' => false, 'style' => 'width:140px;height:25px;display:none;')); ?>
                </div>
            </td>
        </tr>
    </table>
    <br/>
    <fieldset>
        <legend><?php __(HOW_TO_KNOW_LIM_TAING_CLINIC); ?></legend>
        <?php 
        foreach ($patientConnections as $patientConnection) {
            echo '<input id="PatientConnectionWithHospital'.$patientConnection['PatientConnectionWithHospital']['id'].'" name="data[Patient][patient_conection_id][]" type="checkbox" value="'.$patientConnection['PatientConnectionWithHospital']['id'].'"/>';
            echo '<label style="padding-right: 20px;" for="PatientConnectionWithHospital'.$patientConnection['PatientConnectionWithHospital']['id'].'">'.$patientConnection['PatientConnectionWithHospital']['name'].'</label>';
        }
        ?>
    </fieldset>    
</fieldset>
<br/>
<div class="buttons">
    <button type="submit" class="positive savePatientPediatric" >
        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
        <span class="txtSavePatient"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<?php echo $this->Form->end(); ?>
