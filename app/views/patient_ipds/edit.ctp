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
        $("#PatientIpdEditForm").validationEngine();
        $("#PatientIpdEditForm").ajaxForm({
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
                $("#dialog").html('<div class="buttons"><button type="submit" class="positive printPatientDeath" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="txtPrintInvoice"><?php echo ACTION_PRINT_PATIENT_IPD; ?></span></button></div>');
                $(".printPatientDeath").click(function(){
                    $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printPatientIpd/"+result,
                        beforeSend: function(){
                            $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                        },
                        success: function(printPatientIPDResult){
                            w=window.open();
                            w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                            w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                            w.document.write(printPatientIPDResult);
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
                   title: '<?php echo MENU_PATIENT_IPD_ADMISSION_CONSENT_FORM_INFO; ?>',
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
                       $(".btnBackPatientIPD").dblclick();
                   },
                   buttons: {
                       '<?php echo ACTION_CLOSE; ?>': function() {
                           $("meta[http-equiv='refresh']").attr('content','0');
                           $(this).dialog("close");
                       }
                   }
               });
                $(".btnBackPatientIPD").dblclick();
            }
        });
        $(".savePatientIPD").click(function(){
            if(checkBfSavePatient() == true){
                return true;
            }else{
                return false;
            }
        });
        // for condition foreiner or cambodian
        if($("#PatientIpdPatientGroupId").val()==2){
            $("#PatientIpdNationality").show();
        }else if($("#PatientIpdPatientGroupId").val()==1){
            $("#PatientIpdNationality").find("option[value='']").attr("selected", true);
            $("#PatientIpdNationality").hide();
        }else{
            $("#PatientIpdNationality").find("option[value='']").attr("selected", true);
            $("#PatientIpdNationality").hide();
        }
        
        $("#PatientIpdPatientGroupId").change(function(){            
            if($("#PatientIpdPatientGroupId").val()==2){
                $("#PatientIpdNationality").show();
            }else if($("#PatientIpdPatientGroupId").val()==1){
                $("#PatientIpdNationality").find("option[value='']").attr("selected", true);
                $("#PatientIpdNationality").hide();
            }else{
                $("#PatientIpdNationality").find("option[value='']").attr("selected", true);
                $("#PatientIpdNationality").hide();                
            }                        
        });
        // close condition foreiner or cambodian                
        
        
        // condition select company 
        $("#PatientIpdCompanyId").change(function() {
            if($("#PatientIpdCompanyId").val()!=""){
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
        
        var dates = $("#PatientIpdAuthorizedIssueDate, #PatientIpdAuthorizedExpirationDate").datepicker({
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
                var option = this.id == "PatientIpdAuthorizedIssueDate" ? "minDate" : "maxDate",
                        instance = $(this).data("datepicker");
                date = $.datepicker.parseDate(
                        instance.settings.dateFormat ||
                        $.datepicker._defaults.dateFormat,
                        selectedDate, instance.settings);
                dates.not(this).datepicker("option", option, date);
            }
        }).unbind("blur");
                
        $("#PatientIpdDob" ).datepicker({
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
        if($("#PatientIpdDob").val()!=''){
            var now = (new Date()).getFullYear();
            var age = now - $("#PatientIpdDob").val().split("-",1);
            $('#PatientIpdAge').val(age);
        }
        $("#PatientIpdDob").change(function(){
            var now = (new Date()).getFullYear();
            var age = now - $("#PatientIpdDob").val().split("-",1);
            $('#PatientIpdAge').val(age);
        });
        $("#PatientIpdAge").keyup(function(){
            var now = (new Date()).getFullYear();
            var age = parseUniInt($("#PatientIpdAge").val());
            var year = now - age;
            if($("#PatientIpdDob").val()!=''){
                var dob = year + $("#PatientIpdDob").val().substr(-6);
            }else{
                var dob = year + '-01-01';
            }
            $('#PatientIpdDob').val(dob);
        });
        
        $('#PatientIpdDateIpd').datetimepicker(
        {
            changeMonth: true,
            changeYear: true,
            timeFormat: 'hh:mm',
            showSecond: false,
            dateFormat:'yy-mm-dd'            
        });
        $(".btnBackPatientIPD").dblclick(function(event){
            event.preventDefault();
            $('#PatientIpdEditForm').validationEngine('hideAll');
            oCache.iCacheLower = -1;
            oTablePatientIPD.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });        
    
    function checkBfSavePatient(){
        var formName = "#PatientIpdEditForm";
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
        <a href="#" class="positive btnBackPatientIPD">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('PatientIpd'); ?>
<?php echo $this->Form->input('id'); ?>
<input id="patientCode" name="data[Patient][patient_code]" type="hidden" value="<?php echo $this->data['Patient']['patient_code'];?>"/>
<input id="patientId" name="data[Patient][id]" type="hidden" value="<?php echo $this->data['Patient']['id'];?>"/>
<fieldset>
    <legend><?php __(MENU_PATIENT_IPD_INFO); ?></legend>
    <table style="width: 100%;" cellspacing="0">
        <tr>
            <td style="width: 15%;"><?php echo PATIENT_CODE; ?> <span class="red">*</span> :</td>
            <td style="width: 35%;" class="trPatientIpdCode">                
                <?php echo $this->data['Patient']['patient_code']; ?>                
            </td>
            <td style="width: 15%;"><label for="PatientIpdDob"><?php echo TABLE_DOB; ?> <span class="red">*</span> :</label></td>
            <td style="width: 35%;">
                <?php echo $this->Form->text('dob', array('name' => "data[Patient][dob]", 'class' => 'validate[required]','onkeypress'=>'return isNumberKey(event)', 'style' => "width:235px", 'value' => $this->data['Patient']['dob'])); ?>
                <label for="PatientIpdAge"><?php echo TABLE_AGE; ?>:</label>
                <?php echo $this->Form->text('age',array('style'=>'width:30px;', 'class' => 'number validate[required,maxSize[3]]', 'maxlength' => '3')); ?>
            </td>
        </tr>
        <tr>
            <td><label for="PatientIpdPatientName"><?php echo PATIENT_NAME; ?> <span class="red">*</span> :</label></td>
            <td>
                <?php echo $this->Form->text('patient_name', array('value' => $this->data['Patient']['patient_name'], 'class' => 'validate[required]', 'name' => "data[Patient][patient_name]")); ?>                
            </td>
            <td><label for="PatientIpdNationality"><?php echo TABLE_NATIONALITY; ?> <span class="red">*</span> :</label></td>
            <td>
                <?php echo $this->Form->input('patient_group_id', array('name' => "data[Patient][patient_group_id]", 'selected' => $this->data['Patient']['patient_group_id'], 'empty' => SELECT_OPTION, 'label' => false,'class' => 'validate[required]', 'style' => 'width:150px;')); ?>
                <?php echo $this->Form->input('nationality', array('name' => "data[Patient][nationality]", 'selected' => $this->data['Patient']['nationality'], 'empty' => SELECT_OPTION, 'label' => false,'class' => 'validate[required]', 'style' => 'width:156px;display:none;')); ?>
            </td>
        </tr>      
        <tr>
            <td><label for="PatientIpdSex"><?php echo TABLE_SEX; ?> <span class="red">*</span> :</label></td>
            <td><?php echo $this->Form->input('sex', array('name' => "data[Patient][sex]", 'selected' => $this->data['Patient']['sex'], 'empty' => SELECT_OPTION, 'label' => false, 'class' => 'validate[required]')); ?></td>
            <td><label for="PatientIpdEmail"><?php echo TABLE_EMAIL; ?> :</label></td>
            <td><?php echo $this->Form->text('email', array('value' => $this->data['Patient']['email'], 'name' => "data[Patient][email]", 'class' => 'validate[custom[email]]')); ?></td>            
        </tr>
        <tr>            
            <td><label for="PatientIpdOccupation"><?php echo TABLE_OCCUPATION; ?> :</label></td>
            <td><?php echo $this->Form->text('occupation', array('value' => $this->data['Patient']['occupation'], 'name' => "data[Patient][occupation]")); ?></td>
            <td><label for="PatientIpdTelephone"><?php echo TABLE_TELEPHONE; ?>:</label></td>
            <td><?php echo $this->Form->text('telephone', array('value' => $this->data['Patient']['telephone'], 'name' => "data[Patient][telephone]", 'class' => 'validate[custom[phone]]')); ?></td>
        </tr>        
        <tr>
            <td><label for="PatientIpdAddress"><?php echo TABLE_ADDRESS; ?> :</label></td>
            <td><?php echo $this->Form->text('address', array('value' => $this->data['Patient']['address'], 'name' => "data[Patient][address]")); ?></td>
            <td><label for="PatientIpdLocationId"><?php echo TABLE_CITY_PROVINCE; ?> :</label></td>
            <td><?php echo $this->Form->input('location_id', array('name' => "data[Patient][location_id]", 'selected' => $this->data['Patient']['location_id'], 'empty' => SELECT_OPTION, 'label' => false)); ?></td>            
        </tr>    
        <tr>
            <td><label for="PatientIpdCompanyId"><?php echo TABLE_COMPANY; ?> <span class="red">*</span> :</label></td>
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
            <td style="width: 15%;"><label for="PatientIpdDateIpd"><?php echo TABLE_PATIENT_CHECK_IN_DATE; ?> <span class="red">*</span> :</label></td>
            <td style="width: 35%;">
                <?php echo $this->Form->text('date_ipd', array('class' => 'validate[required]','onkeypress'=>'return isNumberKey(event)', 'readonly' => true)); ?>
            </td>
            <td style="width: 15%;"><label for="PatientIpdRoomId"><?php echo TABLE_ROOM_NUMBER; ?> <span class="red">*</span> :</label></td>
            <td style="width: 35%;">
                <select id="PatientIpdRoomId" name="data[PatientIpd][room_id]" class="classRoom validate[required]">
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
            <td><label for="PatientIpdDoctorId"><?php echo TABLE_ADMITTING_PHYSICIAN; ?> <span class="red">*</span> :</label></td>
            <td>
                <?php //debug($this->data);?>
                <select id="PatientIpdDoctorId" name="data[PatientIpd][doctor_id]" class="classDoctor validate[required]">
                    <option value=""><?php echo SELECT_OPTION;?></option>
                    <?php 
                    foreach ($doctors as $doctor) {
                        if($this->data['PatientIpd']['doctor_id']==$doctor['User']['id']){                            
                            if($doctor['Company']['id']==$this->data['Company']['id']){
                                echo '<option selected="selected" class="1" value="'.$doctor['User']['id'].'">'.$doctor['Employee']['name'].'</option>';
                            }else{
                                echo '<option style="display:none;" class="1" value="'.$doctor['User']['id'].'">'.$doctor['Employee']['name'].'</option>';
                            }
                            
                        }else{
                            if($doctor['Company']['id']==$this->data['Company']['id']){
                                echo '<option class="1" value="'.$doctor['User']['id'].'">'.$doctor['Employee']['name'].'</option>';
                            }else{
                                echo '<option style="display:none;" class="1" value="'.$doctor['User']['id'].'">'.$doctor['Employee']['name'].'</option>';
                            }
                        }
                    }
                    ?>
                </select>                
            </td>
            <td><label for="PatientIpdDepartmentId"><?php echo TABLE_DEPARTMENT; ?> <span class="red">*</span> :</label></td>
            <td>
                <select id="PatientIpdDepartmentId" name="data[PatientIpd][department_id]" class="classDepartment validate[required]">
                    <option value=""><?php echo SELECT_OPTION;?></option>
                    <?php                     
                    foreach ($departments as $departments) {                        
                        if($this->data['PatientIpd']['group_id']==$departments['Group']['id']){
                            if($departments['Company']['id']==$this->data['Company']['id']){
                                echo '<option selected="selected" class="'.$departments['Company']['id'].'" value="'.$departments['Group']['id'].'">'.$departments['Group']['name'].'</option>';
                            }else{
                                echo '<option style="display:none;" class="'.$departments['Company']['id'].'" value="'.$departments['Group']['id'].'">'.$departments['Group']['name'].'</option>';
                            }                                                        
                        }else{
                            
                            if($departments['Company']['id']==$this->data['Company']['id']){
                                echo '<option class="'.$departments['Company']['id'].'" value="'.$departments['Group']['id'].'">'.$departments['Group']['name'].'</option>';
                            }else{
                                echo '<option style="display:none;" class="'.$departments['Company']['id'].'" value="'.$departments['Group']['id'].'">'.$departments['Group']['name'].'</option>';
                            }
                        }
                    }
                    ?>
                </select>                
            </td>
        </tr>
        <tr>
            <td><label for="PatientIpdAllergies"><?php echo TABLE_ALLERGIC; ?> <span class="red">*</span> :</label></td>
            <td>
                <?php echo $this->Form->text('allergies', array('class' => 'validate[required]')); ?>                
            </td>
            <td><label for="PatientIpdWitnessName"><?php echo TABLE_WITNESS_NAME; ?> :</label></td>
            <td>
                <?php echo $this->Form->text('witness_name'); ?>                
            </td>
        </tr>
        <tr>
            <td><label for="PatientIpdAuthorizedName"><?php echo TABLE_AUTHORIZE_NAME; ?> :</label></td>
            <td>
                <?php echo $this->Form->text('authorized_name'); ?>                
            </td>
            <td><label for="PatientIpdAuthorizedTelephone"><?php echo TABLE_TELEPHONE; ?> :</label></td>
            <td>
                <?php echo $this->Form->text('authorized_telephone'); ?>
            </td>
        </tr>
        <tr>
            <td><label for="PatientIpdAuthorizedAddress"><?php echo TABLE_ADDRESS; ?> :</label></td>
            <td>
                <?php echo $this->Form->text('authorized_address'); ?>                
            </td>
            <td><label for="PatientIpdAuthorizedIdCard"><?php echo TABLE_ID_CARD; ?> :</label></td>
            <td>
                <?php echo $this->Form->text('authorized_id_card'); ?>
            </td>
        </tr>
        <tr>
            <td><label for="PatientIpdAuthorizedIssueDate"><?php echo TABLE_ISSUE_DATE; ?> :</label></td>
            <td>
                <?php echo $this->Form->text('authorized_issue_date', array('value' => $this->data['PatientIpd']['authorized_issue_date'] != "0000-00-00" ? $this->data['PatientIpd']['authorized_issue_date'] : "")); ?>                
            </td>
            <td><label for="PatientIpdAuthorizedExpirationDate"><?php echo TABLE_EXPIRATION_DATE; ?> :</label></td>
            <td>
                <?php echo $this->Form->text('authorized_expiration_date', array('value' => $this->data['PatientIpd']['authorized_issue_date'] != "0000-00-00" ? $this->data['PatientIpd']['authorized_issue_date'] : "")); ?>
            </td>
        </tr>
        <tr>
            <td><label for="PatientIpdAuthorizedIssuePlace"><?php echo TABLE_ISSUE_PLACE; ?> :</label></td>
            <td>
                <?php echo $this->Form->text('authorized_issue_place'); ?>                
            </td>           
        </tr>
    </table>
</fieldset>        
<br/> 
<div class="buttons">
    <button type="submit" class="positive savePatientIPD" >
        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
        <span class="txtSavePatient"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<?php echo $this->Form->end(); ?>

