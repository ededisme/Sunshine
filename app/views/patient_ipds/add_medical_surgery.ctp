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
        $("#PatientIpdAddMedicalSurgeryForm").validationEngine();
        $("#PatientIpdAddMedicalSurgeryForm").ajaxForm({
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
                
                $("#dialog").html('<div class="buttons"><button type="submit" class="positive printPatientMedicalSurgeryForm" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="txtPrintInvoice"><?php echo ACTION_PRINT_PATIENT_IPD; ?></span></button></div>');
                $(".printPatientMedicalSurgeryForm").click(function(){
                    $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printPatientMedicalSurgery/"+result,
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
                   title: '<?php echo MENU_PATIENT_IPD_MEDICAL_SURGERY_CONSENT_FORM; ?>',
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
                       $(".btnBackPatientMedicalSurgery").dblclick();
                   },
                   buttons: {
                       '<?php echo ACTION_CLOSE; ?>': function() {
                           $("meta[http-equiv='refresh']").attr('content','0');
                           $(this).dialog("close");
                       }
                   }
               });
                $(".btnBackPatientMedicalSurgery").dblclick();
            }
        });
        $(".savePatientMedicalSurgery").click(function(){
            if(checkBfSavePatient() == true){
                return true;
            }else{
                return false;
            }
        });
        
        // search patient information
        $("#PatientIpdPatientName").autocomplete("<?php echo $absolute_url . 'patients' . "/searchPatient"; ?>", {        
            width: 410,
            max: 10,
            scroll: true,
            scrollHeight: 500,
            formatItem: function(data, i, n, value) {
                return value.split(".*")[2] + "-" + value.split(".*")[1];
            },
            formatResult: function(data, value) {
                return value.split(".*")[1];
            }
        }).result(function(event, value){            
            $(".trPatientIpdCode").text(value.toString().split(".*")[2]);
            $("#patientCode").val(value.toString().split(".*")[2]);
            $("#patientId").val(value.toString().split(".*")[0]);
            if(value.toString().split(".*")[4]!=""){
                $("#PatientIpdDob").val(value.toString().split(".*")[4]);
                var now = (new Date()).getFullYear();
                var age = now - $("#PatientIpdDob").val().split("-",1);
                $('#PatientIpdAge').val(age);
            }                     
            
            // condition select gender of patient
            if(value.toString().split(".*")[3]!=""){
                $("#PatientIpdSex").find("option").each(function(){                    
                    if($(this).val()==value.toString().split(".*")[3]){                        
                        $(this).attr("selected", true);
                    }
                });
            }
            // condition select patient's group
            if(value.toString().split(".*")[5]!=""){
                $("#PatientIpdPatientGroupId").find("option").each(function(){                    
                    if($(this).val()==value.toString().split(".*")[5]){
                        $(this).attr("selected", true);
                        if(value.toString().split(".*")[5]=="2"){
                            $("#PatientIpdNationality").show();                            
                            $("#PatientIpdNationality").find("option").each(function(){
                                if($(this).val()==value.toString().split(".*")[11]){
                                    $(this).attr("selected", true);
                                }
                            });
                        }
                    }
                });
            }
            // condition select patient's location
            if(value.toString().split(".*")[10]!=""){
                $("#PatientIpdLocationId").find("option").each(function(){                    
                    if($(this).val()==value.toString().split(".*")[10]){                        
                        $(this).attr("selected", true);
                    }
                });
            }
            
            $("#PatientIpdEmail").val(value.toString().split(".*")[6]);
            $("#PatientIpdOccupation").val(value.toString().split(".*")[7]);
            $("#PatientIpdTelephone").val(value.toString().split(".*")[8]);
            $("#PatientIpdAddress").val(value.toString().split(".*")[9]);
            
            // show image delete patient
            $(".searchPatientIpd").hide();
            $(".deleteSearchPatientIpd").show();
        });
        
        
        $(".searchPatientIpd").click(function(){            
            searchAllPatientIpd();            
        });
        // close search patient information
        
        // delete search patient
        $(".deleteSearchPatientIpd").click(function(){
            $(".trPatientIpdCode").text($("#getPatientIpdCode").val());
            $("#patientCode").val($("#getPatientIpdCode").val());
            $("#patientId").val("");
            $("#PatientIpdDob").val("");
            $('#PatientIpdAge').val("");
            $("#PatientIpdPatientName").val("");
            $("#PatientIpdPatientGroupId").val("");
            $("#PatientIpdNationality").hide();
            $("#PatientIpdSex").val("");
            $("#PatientIpdEmail").val("");
            $("#PatientIpdOccupation").val("");
            $("#PatientIpdTelephone").val("");
            $("#PatientIpdAddress").val("");
            $("#PatientIpdLocationId").val("");
            
            $(".searchPatientIpd").show();
            $(".deleteSearchPatientIpd").hide();
        });
        // close delete search patient
        
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
        if($("#PatientIpdCompanyId").val()!=""){            
            $("#patientIdpManagementInfo").show();
            $(".classRoom").closest("tr").find("td .classRoom").val('');
            $(".classRoom").closest("tr").find("td .classRoom option[class!='']").hide();
            $(".classRoom").closest("tr").find("td .classRoom option[class='"  + $("#PatientIpdCompanyId").val() + "']").show();

            $(".classDoctor").closest("tr").find("td .classDoctor").val('');
            $(".classDoctor").closest("tr").find("td .classDoctor option[class!='']").hide();
            $(".classDoctor").closest("tr").find("td .classDoctor option[class='"  + $("#PatientIpdCompanyId").val() + "']").show();

            $(".classDepartment").closest("tr").find("td .classDepartment").val('');
            $(".classDepartment").closest("tr").find("td .classDepartment option[class!='']").hide();
            $(".classDepartment").closest("tr").find("td .classDepartment option[class='"  + $("#PatientIpdCompanyId").val() + "']").show();
        }else{
            $("#patientIdpManagementInfo").hide();
        }
        
        // for sort section in company
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
                    $("#ui-datepicker-div").css("z-medicalSurgery", 1000);
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
                    $("#ui-datepicker-div").css("z-medicalSurgery", 1000);
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
        $(".btnBackPatientMedicalSurgery").dblclick(function(event){
            event.preventDefault();
            $('#PatientIpdAddMedicalSurgeryForm').validationEngine('hideAll');
            oCache.iCacheLower = -1;
            oTableMedicalSurgery.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });
        
    function checkBfSavePatient(){
        var formName = "#PatientIpdAddMedicalSurgeryForm";
        var validateBack =$(formName).validationEngine("validate");
        if(!validateBack){            
            return false;
        }else{            
            return true;
        }
    }     
    // search all patient with button search
    function searchAllPatientIpd(){        
        $.ajax({
            type:   "POST",
            url:    "<?php echo $absolute_url . 'patients'; ?>/getFindPatient/",
            beforeSend: function(){
                $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
            },
            success: function(msg){
                
                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                $("#dialog").html(msg).dialog({
                    title: '<?php echo MENU_PATIENT_MANAGEMENT_INFO; ?>',
                    resizable: false,
                    modal: true,
                    width: 900,
                    height: 600,
                    position:'center',
                    closeOnEscape: true,
                    open: function(event, ui){
                        $(".ui-dialog-buttonpane").show(); $(".ui-dialog-titlebar-close").show();
                    },
                    buttons: {
                        '<?php echo ACTION_OK; ?>': function() {
                            
                            if($("input[name='chkPatient']:checked").val()){
                                value = $("input[name='chkPatient']:checked").attr("rel");
                                $(".trPatientIpdCode").text(value.toString().split(".*")[2]);
                                $("#patientCode").val(value.toString().split(".*")[2]);
                                $("#PatientIpdPatientName").val(value.toString().split(".*")[1]);
                                $("#patientId").val(value.toString().split(".*")[0]);
                                if(value.toString().split(".*")[4]!=""){
                                    $("#PatientIpdDob").val(value.toString().split(".*")[4]);
                                    var now = (new Date()).getFullYear();
                                    var age = now - $("#PatientIpdDob").val().split("-",1);
                                    $('#PatientIpdAge').val(age);
                                }                     

                                // condition select gender of patient
                                if(value.toString().split(".*")[3]!=""){
                                    $("#PatientIpdSex").find("option").each(function(){                    
                                        if($(this).val()==value.toString().split(".*")[3]){                        
                                            $(this).attr("selected", true);
                                        }
                                    });
                                }
                                // condition select patient's group
                                if(value.toString().split(".*")[5]!=""){
                                    $("#PatientIpdPatientGroupId").find("option").each(function(){                    
                                        if($(this).val()==value.toString().split(".*")[5]){
                                            $(this).attr("selected", true);
                                            if(value.toString().split(".*")[5]=="2"){
                                                $("#PatientIpdNationality").show();                            
                                                $("#PatientIpdNationality").find("option").each(function(){
                                                    if($(this).val()==value.toString().split(".*")[11]){
                                                        $(this).attr("selected", true);
                                                    }
                                                });
                                            }
                                        }
                                    });
                                }
                                // condition select patient's location
                                if(value.toString().split(".*")[10]!=""){
                                    $("#PatientIpdLocationId").find("option").each(function(){                    
                                        if($(this).val()==value.toString().split(".*")[10]){                        
                                            $(this).attr("selected", true);
                                        }
                                    });
                                }

                                $("#PatientIpdEmail").val(value.toString().split(".*")[6]);
                                $("#PatientIpdOccupation").val(value.toString().split(".*")[7]);
                                $("#PatientIpdTelephone").val(value.toString().split(".*")[8]);
                                $("#PatientIpdAddress").val(value.toString().split(".*")[9]);
                                
                                // action hidden search button and show delete button
                                $(".searchPatientIpd").hide();
                                $(".deleteSearchPatientIpd").show();
                            }
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
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
        <a href="#" class="positive btnBackPatientMedicalSurgery">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('PatientIpd'); ?>
<input id="patientCode" name="data[Patient][patient_code]" type="hidden" value="<?php echo $code;?>"/>
<input id="getPatientIpdCode" type="hidden" value="<?php echo $code;?>"/>
<input id="patientId" name="data[Patient][id]" type="hidden" value=""/>
<fieldset>
    <legend><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?></legend>
    <table style="width: 100%;" cellspacing="0">
        <tr>
            <td style="width: 15%;"><?php echo PATIENT_CODE; ?> <span class="red">*</span> :</td>
            <td style="width: 35%;" class="trPatientIpdCode">                
                <?php echo $code; ?>                
            </td>
            <td style="width: 15%;"><label for="PatientIpdDob"><?php echo TABLE_DOB; ?> <span class="red">*</span> :</label></td>
            <td style="width: 35%;">
                <?php echo $this->Form->text('dob', array('name' => "data[Patient][dob]", 'class' => 'validate[required]','onkeypress'=>'return isNumberKey(event)', 'style' => "width:228px")); ?>
                <label for="PatientIpdAge"><?php echo TABLE_AGE; ?>:</label>
                <?php echo $this->Form->text('age',array('style'=>'width:30px;', 'class' => 'number validate[required,maxSize[3]]', 'maxlength' => '3')); ?>
            </td>
        </tr>
        <tr>
            <td><label for="PatientIpdPatientName"><?php echo PATIENT_NAME; ?> <span class="red">*</span> :</label></td>
            <td>
                <?php echo $this->Form->text('patient_name', array('class' => 'validate[required]', 'name' => "data[Patient][patient_name]")); ?>
                <img alt="Search" align="absmiddle" style="cursor: pointer;" class="searchPatientIpd" onmouseover="Tip('<?php echo GENERAL_SEARCH; ?>')" src="<?php echo $this->webroot . 'img/button/search.png'; ?>" />
                <img alt="Delete" align="absmiddle" style="cursor: pointer; display: none;" class="deleteSearchPatientIpd" onmouseover="Tip('<?php echo ACTION_DELETE; ?>')" src="<?php echo $this->webroot . 'img/button/delete.png'; ?>" />
            </td>
            <td><label for="PatientIpdNationality"><?php echo TABLE_NATIONALITY; ?> <span class="red">*</span> :</label></td>
            <td>
                <?php echo $this->Form->input('patient_group_id', array('name' => "data[Patient][patient_group_id]", 'empty' => SELECT_OPTION, 'label' => false,'class' => 'validate[required]', 'style' => 'width:150px;')); ?>                
                <?php echo $this->Form->input('nationality', array('name' => "data[Patient][nationality]", 'empty' => SELECT_OPTION, 'label' => false,'class' => 'validate[required]', 'style' => 'width:156px;display:none;')); ?>
            </td>
        </tr>      
        <tr>
            <td><label for="PatientIpdSex"><?php echo TABLE_SEX; ?> <span class="red">*</span> :</label></td>
            <td><?php echo $this->Form->input('sex', array('name' => "data[Patient][sex]", 'empty' => SELECT_OPTION, 'label' => false, 'class' => 'validate[required]')); ?></td>
            <td><label for="PatientIpdEmail"><?php echo TABLE_EMAIL; ?> :</label></td>
            <td><?php echo $this->Form->text('email', array('name' => "data[Patient][email]", 'class' => 'validate[custom[email]]')); ?></td>            
        </tr>
        <tr>            
            <td><label for="PatientIpdOccupation"><?php echo TABLE_OCCUPATION; ?> :</label></td>
            <td><?php echo $this->Form->text('occupation', array('name' => "data[Patient][occupation]")); ?></td>
            <td><label for="PatientIpdTelephone"><?php echo TABLE_TELEPHONE; ?>:</label></td>
            <td><?php echo $this->Form->text('telephone', array('name' => "data[Patient][telephone]", 'class' => 'validate[custom[phone]]')); ?></td>
        </tr>        
        <tr>
            <td><label for="PatientIpdAddress"><?php echo TABLE_ADDRESS; ?> :</label></td>
            <td><?php echo $this->Form->text('address', array('name' => "data[Patient][address]")); ?></td>
            <td><label for="PatientIpdLocationId"><?php echo TABLE_CITY_PROVINCE; ?> :</label></td>
            <td><?php echo $this->Form->input('location_id', array('name' => "data[Patient][location_id]", 'empty' => SELECT_OPTION, 'label' => false)); ?></td>            
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
<fieldset id="patientIdpManagementInfo" style="display:none;">
    <legend><?php __(MENU_IPD_MANAGEMENT_INFO); ?></legend>
    <table style="width: 100%;" cellspacing="0">
        <tr>
            <td style="width: 15%;"><label for="PatientIpdDateIpd"><?php echo TABLE_PATIENT_CHECK_IN_DATE; ?> <span class="red">*</span> :</label></td>
            <td style="width: 35%;">
                <?php echo $this->Form->text('date_ipd', array('class' => 'validate[required]', 'onkeypress'=>'return isNumberKey(event)', 'readonly' => true)); ?>
            </td>
            <td style="width: 15%;"><label for="PatientIpdRoomId"><?php echo TABLE_ROOM_NUMBER; ?> <span class="red">*</span> :</label></td>
            <td style="width: 35%;">
                <select id="PatientIpdRoomId" name="data[PatientIpd][room_id]" class="classRoom validate[required]">
                    <option value=""><?php echo SELECT_OPTION;?></option>
                    <?php 
                    foreach ($rooms as $room) {
                        echo '<option class="'.$room['Room']['company_id'].'" value="'.$room['Room']['id'].'">'.$room['Room']['room_name'].'-'.$room['RoomType']['name'].'</option>';
                    }
                    ?>
                </select>                
            </td>
        </tr>
        <tr>
            <td><label for="PatientIpdDoctorId"><?php echo TABLE_ADMITTING_PHYSICIAN; ?> <span class="red">*</span> :</label></td>
            <td>
                
                <select id="PatientIpdDoctorId" name="data[PatientIpd][doctor_id]" class="classDoctor validate[required]">
                    <option value=""><?php echo SELECT_OPTION;?></option>
                    <?php 
                    foreach ($doctors as $doctor) {
                        echo '<option class="1" value="'.$doctor['DoctorConsultation']['id'].'">'.$doctor['DoctorConsultation']['name'].'</option>';
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
                        echo '<option class="'.$departments['Company']['id'].'" value="'.$departments['Group']['id'].'">'.$departments['Group']['name'].'</option>';
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
            <td><label for="PatientIpdDoctorExplainToPatient"><?php echo TABLE_DOCTOR_EXPLAINED_TO_PATIENT; ?> :</label></td>
            <td>
                <?php echo $this->Form->text('doctor_explain_to_patient'); ?>                
            </td>           
            <td><label for="PatientIpdPatientFollowingSurgical"><?php echo TABLE_PATIENT_FOLLOWING_SURGICAL; ?> :</label></td>
            <td>
                <?php echo $this->Form->text('patient_following_surgical'); ?>
            </td>
        </tr>
        <tr>
            <td><label for="PatientIpdAccordingToPatient"><?php echo TABLE_ACCORDING_TO_PATIENT; ?> :</label></td>
            <td>
                <?php echo $this->Form->text('according_to_patient'); ?>
            </td>
            <td><label for="PatientIpdAccordingNumber"><?php echo TABLE_ACCORDING_NUMBER; ?> :</label></td>
            <td>
                <?php echo $this->Form->text('according_number'); ?>
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
                <?php echo $this->Form->text('authorized_issue_date'); ?>                
            </td>
            <td><label for="PatientIpdAuthorizedExpirationDate"><?php echo TABLE_EXPIRATION_DATE; ?> :</label></td>
            <td>
                <?php echo $this->Form->text('authorized_expiration_date'); ?>
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
<div class="clear"></div>
<div class="buttons">
    <button type="submit" class="positive savePatientMedicalSurgery" >
        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
        <span class="txtSavePatient"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<?php echo $this->Form->end(); ?>

