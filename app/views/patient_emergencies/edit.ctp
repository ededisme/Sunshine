<?php 
echo $this->element('prevent_multiple_submit'); 
$absolute_url = FULL_BASE_URL . Router::url("/", false);
echo $javascript->link('uninums.min'); 
?>
<?php $tblName = "tbl" . rand(); ?>

<style type="text/css">
    .input{
        float:left;
    }
</style>
<script type="text/javascript">
    $(document).ready(function(){ 
        // Prevent Key Enter
        preventKeyEnter();     
        $("#PatientEmergencyEditForm").validationEngine();
        $("#PatientEmergencyEditForm").ajaxForm({
            dataType: 'json',
            beforeSubmit: function(arr, $form, options) {
                $(".txtSavePatient").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");       
                if(result.error == 2){
                    // alert message
                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result.msg+'</p>');
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
                    return false;
                }
                $("#dialog").html('<div class="buttons"><button type="submit" class="positive printPatientEmergencyForm" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="txtPrintInvoice"><?php echo ACTION_PRINT_PATIENT_EMERGENCY; ?></span></button></div>');
                $(".printPatientEmergencyForm").click(function(){
                    $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printPatientEmergency/"+result,
                        beforeSend: function(){
                            $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                        },
                        success: function(printPatientEmergencyResult){
                            w=window.open();
                            w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                            w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                            w.document.write(printPatientEmergencyResult);
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
                   title: '<?php echo MENU_PATIENT_EMERGENCY_MANAGEMENT_INFO; ?>',
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
                       $(".btnBackPatientEmergency").dblclick();
                   },
                   buttons: {
                       '<?php echo ACTION_CLOSE; ?>': function() {
                           $("meta[http-equiv='refresh']").attr('content','0');
                           $(this).dialog("close");
                       }
                   }
               });
               $(".btnBackPatientEmergency").dblclick();
            }
        });
        $(".savePatientEmergency").click(function(){
            if(checkBfSavePatient() == true){
                return true;
            }else{
                return false;
            }
        });
        
        // for condition foreiner or cambodian
        if($("#PatientEmergencyPatientGroupId").val()==2){
            $("#PatientEmergencyNationality").show();
        }else if($("#PatientEmergencyPatientGroupId").val()==1){
            $("#PatientEmergencyNationality").find("option[value='']").attr("selected", true);
            $("#PatientEmergencyNationality").hide();
        }else{
            $("#PatientEmergencyNationality").find("option[value='']").attr("selected", true);
            $("#PatientEmergencyNationality").hide();
        }
        
        $("#PatientEmergencyPatientGroupId").change(function(){            
            if($("#PatientEmergencyPatientGroupId").val()==2){
                $("#PatientEmergencyNationality").show();
            }else if($("#PatientEmergencyPatientGroupId").val()==1){
                $("#PatientEmergencyNationality").find("option[value='']").attr("selected", true);
                $("#PatientEmergencyNationality").hide();
            }else{
                $("#PatientEmergencyNationality").find("option[value='']").attr("selected", true);
                $("#PatientEmergencyNationality").hide();                
            }                        
        });
        // close condition foreiner or cambodian                
        
        
        // condition select company 
        $("#PatientEmergencyCompanyId").change(function() {
            if($("#PatientEmergencyCompanyId").val()!=""){
                $("#patientIdpManagementInfo").show();
            }else{
                $("#patientIdpManagementInfo").hide();
            }
        });
        
        // for condition open form add new patient ipd

        // for sort room, doctor and department in company
        $(".classCompany").change(function(){
            if($(this).val()!=''){
                $(".classRoom").closest("tr").find("td .classRoom").val('');
                $(".classRoom").closest("tr").find("td .classRoom option[class!='']").hide();
                $(".classRoom").closest("tr").find("td .classRoom option[class='"  + $(this).val() + "']").show();
                
                $(".classDoctor").closest("tr").find("td .classDoctor").val('');
                $(".classDoctor").closest("tr").find("td .classDoctor option[class!='']").hide();
                $(".classDoctor").closest("tr").find("td .classDoctor option[class='"  + $(this).val() + "']").show();
                
                $(".classDepartment").closest("tr").find("td .classDepartment").val('');
                $(".classDepartment").closest("tr").find("td .classDepartment option[class!='']").hide();
                $(".classDepartment").closest("tr").find("td .classDepartment option[class='"  + $(this).val() + "']").show();
                
            }else{           
                
                $(".classRoom").closest("tr").find("td .classRoom option[class!='']").show();
                $(".classDoctor").closest("tr").find("td .classDoctor option[class!='']").show();
                $(".classDepartment").closest("tr").find("td .classDepartment option[class!='']").show();
            }
        });
        
        // close select company        
        
        // check patient insurance
        if($("#PatientEmergencyPatientBillTypeId").val()==3){            
            $("#PatientEmergencyCompanyInsuranceId").show();
            if($("#PatientEmergencyCompanyInsuranceId").val()==0){
                $("#insuranceNote").show();
            }
            
        }  
        
        $("#PatientEmergencyPatientBillTypeId").change(function(){
            var patientBillType = $("#PatientEmergencyPatientBillTypeId").val();            
            if(patientBillType==3){
                $("#PatientEmergencyCompanyInsuranceId").show();
                if($("#PatientEmergencyCompanyInsuranceId").val()==0 && $("#PatientEmergencyCompanyInsuranceId").val()!=""){
                    $("#insuranceNote").show();
                }else{
                    $("#insuranceNote").hide();
                }
            }else{
                $("#PatientEmergencyCompanyInsuranceId").hide();
            }
        });
        $("#PatientEmergencyCompanyInsuranceId").change(function(){
            if($("#PatientEmergencyCompanyInsuranceId").val()==0 && $("#PatientEmergencyCompanyInsuranceId").val()!=""){
                $("#insuranceNote").show();
            }else{
                $("#insuranceNote").hide();
            }
        });
        // close check patient insurance
        
        var dates = $("#PatientEmergencyAuthorizedIssueDate, #PatientEmergencyAuthorizedExpirationDate").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            beforeShow: function(){
                setTimeout(function(){
                    $("#ui-datepicker-div").css("z-index", 1000);
                }, 10);
            },
            onSelect: function(selectedDate) {
                var option = this.id == "PatientEmergencyAuthorizedIssueDate" ? "minDate" : "maxDate",
                        instance = $(this).data("datepicker");
                date = $.datepicker.parseDate(
                        instance.settings.dateFormat ||
                        $.datepicker._defaults.dateFormat,
                        selectedDate, instance.settings);
                dates.not(this).datepicker("option", option, date);
            }
        }).unbind("blur");
                
        $("#PatientEmergencyDob" ).datepicker({
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
        if($("#PatientEmergencyDob").val()!=''){
            var now = (new Date()).getFullYear();
            var age = now - $("#PatientEmergencyDob").val().split("-",1);
            $('#PatientEmergencyAge').val(age);
        }
        $("#PatientEmergencyDob").change(function(){
            var now = (new Date()).getFullYear();
            var age = now - $("#PatientEmergencyDob").val().split("-",1);
            $('#PatientEmergencyAge').val(age);
        });
        $("#PatientEmergencyAge").keyup(function(){
            var now = (new Date()).getFullYear();
            var age = parseUniInt($("#PatientEmergencyAge").val());
            var year = now - age;
            if($("#PatientEmergencyDob").val()!=''){
                var dob = year + $("#PatientEmergencyDob").val().substr(-6);
            }else{
                var dob = year + '-01-01';
            }
            $('#PatientEmergencyDob').val(dob);
        });
        
        $('#PatientEmergencyDateIpd').datetimepicker(
        {
            changeMonth: true,
            changeYear: true,
            timeFormat: 'hh:mm',
            showSecond: false,
            dateFormat:'yy-mm-dd'            
        });
        
        $(".btnBackPatientEmergency").dblclick(function(event){
            event.preventDefault();
            $('#PatientEmergencyEditForm').validationEngine('hideAll');
            oCache.iCacheLower = -1;
            oTablePatientEmergecy.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });        
    
    function checkBfSavePatient(){
        var formName = "#PatientEmergencyEditForm";
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
        <a href="#" class="positive btnBackPatientEmergency">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('PatientEmergency'); ?>
<?php echo $this->Form->input('id'); ?>
<input id="patientCode" name="data[Patient][patient_code]" type="hidden" value="<?php echo $this->data['Patient']['patient_code'];?>"/>
<input id="patientId" name="data[Patient][id]" type="hidden" value="<?php echo $this->data['Patient']['id'];?>"/>
<fieldset>
    <legend><?php __(MENU_PATIENT_IPD_INFO); ?></legend>
    <table style="width: 100%;" cellspacing="0">
        <tr>
            <td style="width: 15%;"><?php echo PATIENT_CODE; ?> <span class="red">*</span> :</td>
            <td style="width: 35%;" class="trPatientEmergencyCode">                
                <?php echo $this->data['Patient']['patient_code']; ?>                
            </td>
            <td style="width: 15%;"><label for="PatientEmergencyDob"><?php echo TABLE_DOB; ?> <span class="red">*</span> :</label></td>
            <td style="width: 35%;">
                <?php echo $this->Form->text('dob', array('name' => "data[Patient][dob]", 'class' => 'validate[required]','onkeypress'=>'return isNumberKey(event)', 'style' => "width:235px", 'value' => $this->data['Patient']['dob'])); ?>
                <label for="PatientEmergencyAge"><?php echo TABLE_AGE; ?>:</label>
                <?php echo $this->Form->text('age',array('style'=>'width:30px;', 'class' => 'number validate[required,maxSize[3]]', 'maxlength' => '3')); ?>
            </td>
        </tr>
        <tr>
            <td><label for="PatientEmergencyPatientName"><?php echo PATIENT_NAME; ?> <span class="red">*</span> :</label></td>
            <td>
                <?php echo $this->Form->text('patient_name', array('value' => $this->data['Patient']['patient_name'], 'class' => 'validate[required]', 'name' => "data[Patient][patient_name]")); ?>                
            </td>
            <td><label for="PatientEmergencyNationality"><?php echo TABLE_NATIONALITY; ?> <span class="red">*</span> :</label></td>
            <td>
                <?php echo $this->Form->input('patient_group_id', array('name' => "data[Patient][patient_group_id]", 'selected' => $this->data['Patient']['patient_group_id'], 'empty' => SELECT_OPTION, 'label' => false,'class' => 'validate[required]', 'style' => 'width:150px;')); ?>
                <?php echo $this->Form->input('nationality', array('name' => "data[Patient][nationality]", 'selected' => $this->data['Patient']['nationality'], 'empty' => SELECT_OPTION, 'label' => false,'class' => 'validate[required]', 'style' => 'width:156px;display:none;')); ?>
            </td>
        </tr>      
        <tr>
            <td><label for="PatientEmergencySex"><?php echo TABLE_SEX; ?> <span class="red">*</span> :</label></td>
            <td><?php echo $this->Form->input('sex', array('name' => "data[Patient][sex]", 'selected' => $this->data['Patient']['sex'], 'empty' => SELECT_OPTION, 'label' => false, 'class' => 'validate[required]')); ?></td>
            <td><label for="PatientEmergencyEmail"><?php echo TABLE_EMAIL; ?> :</label></td>
            <td><?php echo $this->Form->text('email', array('value' => $this->data['Patient']['email'], 'name' => "data[Patient][email]", 'class' => 'validate[custom[email]]')); ?></td>            
        </tr>
        <tr>            
            <td><label for="PatientEmergencyOccupation"><?php echo TABLE_OCCUPATION; ?> :</label></td>
            <td><?php echo $this->Form->text('occupation', array('value' => $this->data['Patient']['occupation'], 'name' => "data[Patient][occupation]")); ?></td>
            <td><label for="PatientEmergencyTelephone"><?php echo TABLE_TELEPHONE; ?>:</label></td>
            <td><?php echo $this->Form->text('telephone', array('value' => $this->data['Patient']['telephone'], 'name' => "data[Patient][telephone]", 'class' => 'validate[custom[phone]]')); ?></td>
        </tr>        
        <tr>
            <td><label for="PatientEmergencyAddress"><?php echo TABLE_ADDRESS; ?> :</label></td>
            <td><?php echo $this->Form->text('address', array('value' => $this->data['Patient']['address'], 'name' => "data[Patient][address]")); ?></td>
            <td><label for="PatientEmergencyLocationId"><?php echo TABLE_CITY_PROVINCE; ?> :</label></td>
            <td><?php echo $this->Form->input('location_id', array('name' => "data[Patient][location_id]", 'selected' => $this->data['Patient']['location_id'], 'empty' => SELECT_OPTION, 'label' => false)); ?></td>            
        </tr>
        <tr>
            <td><label for="PatientEmergencyPatientBillTypeId"><?php echo TABLE_BILL_PAID_BY; ?><span class="red">*</span> :</label></td>
            <td>
                <?php                 
                echo $this->Form->input('patient_bill_type_id', array('name' => "data[Patient][patient_bill_type_id]", 'empty' => SELECT_OPTION, 'selected' => $this->data['Patient']['patient_bill_type_id'], 'label' => false, 'class' => 'validate[required]', 'style' => 'width:153px;')); ?>
                <?php echo $this->Form->input('company_insurance_id', array('name' => "data[Patient][company_insurance_id]", 'empty' => SELECT_OPTION, 'selected' => $this->data['Patient']['company_insurance_id'], 'label' => false, 'class' => 'validate[required]', 'style' => 'width:153px;')); ?>
            </td>            
            <td><label for="PatientEmergencyTypeId"><?php echo TABLE_PATIENT_TYPE; ?> :</label></td>
            <td><?php echo $this->Form->input('patient_type_id', array('name' => "data[Patient][patient_type_id]", 'empty' => SELECT_OPTION , 'label' => false)); ?></td>
        </tr>
        <tr id="insuranceNote" style="display: none;">
            <td><?php echo TABLE_NOTE;?></td>
            <td>
                <?php echo $this->Form->textarea('insurance_note', array('name' => "data[Patient][insurance_note]", 'label' => false, 'style' => 'width:295px;', 'value' => $this->data['Patient']['insurance_note'])); ?>
            </td>
        </tr>
        <tr>
            <td><label for="PatientEmergencyCompanyId"><?php echo TABLE_COMPANY; ?> <span class="red">*</span> :</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->input('company_id', array('empty' => SELECT_OPTION, 'class' => 'classCompany validate[required]', 'label' => false)); ?>
                </div>
            </td>
        </tr>        
    </table>     
</fieldset>
<br/>
<fieldset id="patientIdpManagementInfo">
    <legend><?php __(MENU_IPD_MANAGEMENT_INFO); ?></legend>
    <table style="width: 100%;" cellspacing="0">
        <tr>
            <td style="width: 15%;"><label for="PatientEmergencyDateIpd"><?php echo TABLE_PATIENT_CHECK_IN_DATE; ?> <span class="red">*</span> :</label></td>
            <td style="width: 35%;">
                <?php echo $this->Form->text('date_ipd', array('class' => 'validate[required]','onkeypress'=>'return isNumberKey(event)')); ?>
            </td>
            <td style="width: 15%;"><label for="PatientEmergencyRoomId"><?php echo TABLE_ROOM_NUMBER; ?> <span class="red">*</span> :</label></td>
            <td style="width: 35%;">
                <select id="PatientEmergencyRoomId" name="data[PatientEmergency][room_id]" class="classRoom validate[required]">
                    <option value=""><?php echo SELECT_OPTION;?></option>
                    <?php 
                    foreach ($rooms as $room) { 
                        
                        if($this->data['Room']['id']==$room['Room']['id']){                            
                            if($room['Room']['company_id']==$this->data['Company']['id']){
                                echo '<option selected="selected" class="'.$room['Room']['company_id'].'" value="'.$room['Room']['id'].'">'.$room['Room']['room_name'].'-'.$room['RoomType']['name'].'</option>';
                            }else{
                                echo '<option style="display:none;" class="'.$room['Room']['company_id'].'" value="'.$room['Room']['id'].'">'.$room['Room']['room_name'].'-'.$room['RoomType']['name'].'</option>';
                            }
                            
                        }else{
                            if($room['Room']['company_id']==$this->data['Company']['id']){
                                echo '<option class="'.$room['Room']['company_id'].'" value="'.$room['Room']['id'].'">'.$room['Room']['room_name'].'-'.$room['RoomType']['name'].'</option>';
                            }else{
                                echo '<option style="display:none;" class="'.$room['Room']['company_id'].'" value="'.$room['Room']['id'].'">'.$room['Room']['room_name'].'-'.$room['RoomType']['name'].'</option>';
                            }
                        }
                    }
                    ?>
                </select>                
            </td>
        </tr>
        <tr>
            <td><label for="PatientEmergencyDoctorId"><?php echo TABLE_ADMITTING_PHYSICIAN; ?> <span class="red">*</span> :</label></td>
            <td>
                <?php //debug($this->data);?>
                <select id="PatientEmergencyDoctorId" name="data[PatientEmergency][doctor_id]" class="classDoctor validate[required]">
                    <option value=""><?php echo SELECT_OPTION;?></option>
                    <?php 
                    foreach ($doctors as $doctor) {
                        if($this->data['PatientEmergency']['doctor_id']==$doctor['User']['id']){                            
                            if($doctor['Company']['id']==$this->data['Company']['id']){
                                echo '<option selected="selected" class="'.$doctor['Company']['id'].'" value="'.$doctor['User']['id'].'">'.$doctor['Employee']['name'].'</option>';
                            }else{
                                echo '<option style="display:none;" class="'.$doctor['Company']['id'].'" value="'.$doctor['User']['id'].'">'.$doctor['Employee']['name'].'</option>';
                            }
                            
                        }else{
                            if($doctor['Company']['id']==$this->data['Company']['id']){
                                echo '<option class="'.$doctor['Company']['id'].'" value="'.$doctor['User']['id'].'">'.$doctor['Employee']['name'].'</option>';
                            }else{
                                echo '<option style="display:none;" class="'.$doctor['Company']['id'].'" value="'.$doctor['User']['id'].'">'.$doctor['Employee']['name'].'</option>';
                            }
                        }
                    }
                    ?>
                </select>                
            </td>
            <td><label for="PatientEmergencyDepartmentId"><?php echo TABLE_DEPARTMENT; ?> <span class="red">*</span> :</label></td>
            <td>
                <select id="PatientEmergencyDepartmentId" name="data[PatientEmergency][department_id]" class="classDepartment validate[required]">
                    <option value=""><?php echo SELECT_OPTION;?></option>
                    <?php                     
                    foreach ($departments as $departments) {                        
                        if($this->data['PatientEmergency']['group_id']==$departments['Department']['id']){
                            if($departments['Department']['company_id']==$this->data['Company']['id']){
                                echo '<option selected="selected" class="'.$departments['Department']['company_id'].'" value="'.$departments['Department']['id'].'">'.$departments['Department']['name'].'</option>';
                            }else{
                                echo '<option style="display:none;" class="'.$departments['Department']['company_id'].'" value="'.$departments['Department']['id'].'">'.$departments['Department']['name'].'</option>';
                            }                                                        
                        }else{
                            
                            if($departments['Department']['company_id']==$this->data['Company']['id']){
                                echo '<option class="'.$departments['Department']['company_id'].'" value="'.$departments['Department']['id'].'">'.$departments['Department']['name'].'</option>';
                            }else{
                                echo '<option style="display:none;" class="'.$departments['Department']['company_id'].'" value="'.$departments['Department']['id'].'">'.$departments['Department']['name'].'</option>';
                            }
                        }
                    }
                    ?>
                </select>                
            </td>
        </tr>
        <tr>
            <td><label for="PatientEmergencyDiagnostic"><?php echo TABLE_DIAGNOSIS; ?> <span class="red">*</span> :</label></td>
            <td>
                <?php echo $this->Form->text('diagnostic', array('class' => 'validate[required]')); ?>                
            </td>            
        </tr>
        <tr>
            <td colspan="4">
                <label for="PatientEmergencyPulse"><?php echo 'Pouls'; ?> :</label>
                <?php echo $this->Form->text('pulse', array('style' => 'width:120px;')); ?>
                <label for="PatientEmergencyBp"><?php echo 'BP'; ?> :</label>
                <?php echo $this->Form->text('bp', array('style' => 'width:120px;')); ?>
                <label for="PatientEmergencyTag"><?php echo 'Tag'; ?> :</label>
                <?php echo $this->Form->text('tag', array('style' => 'width:120px;')); ?>
                <label for="PatientEmergencyRespiratory"><?php echo 'Respiratory'; ?> :</label>
                <?php echo $this->Form->text('respiratory', array('style' => 'width:120px;')); ?>
                <label for="PatientEmergencyGlasgowScore"><?php echo 'Glasgow Score'; ?> :</label>
                <?php echo $this->Form->text('glasgow_score', array('style' => 'width:120px;')); ?>
                <label for="PatientEmergencySpO2"><?php echo 'SpO2'; ?> :</label>
                <?php echo $this->Form->text('SpO2', array('style' => 'width:120px;')); ?>
            </td>
        </tr>
    </table>
</fieldset>    
<div class="clear"></div>
<div class="buttons">
    <button type="submit" class="positive savePatientEmergency" >
        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
        <span class="txtSavePatient"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<?php echo $this->Form->end(); ?>

