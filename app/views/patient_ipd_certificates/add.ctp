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
        $("#PatientIpdCertificateAddForm").validationEngine();
        $("#PatientIpdCertificateAddForm").ajaxForm({
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
                $("#dialog").html('<div class="buttons"><button type="submit" class="positive printPatientIpdCertificateForm" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="txtPrintInvoice"><?php echo ACTION_PRINT_PATIENT_IPD_CERTIFICATE; ?></span></button></div>');
                $(".printPatientIpdCertificateForm").click(function(){
                    $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printPatientIpdCertificate/"+result,
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
                   title: '<?php echo MENU_PATIENT_IPD_CERTIFICATE; ?>',
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
                       $(".btnBackPatientCertificate").dblclick();
                   },
                   buttons: {
                       '<?php echo ACTION_CLOSE; ?>': function() {
                           $("meta[http-equiv='refresh']").attr('content','0');
                           $(this).dialog("close");
                       }
                   }
               });
                $(".btnBackPatientCertificate").dblclick();
            }
        });
        $(".savePatientCertificate").click(function(){
            if(checkBfSavePatient() == true){
                return true;
            }else{
                return false;
            }
        });
        // search patient information
        $("#PatientIpdCertificatePatientName").autocomplete("<?php echo $absolute_url . $this->params['controller'] . "/searchPatient"; ?>", {        
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
            $(".trPatientIpdCertificateCode").text(value.toString().split(".*")[2]);
            $("#patientCode").val(value.toString().split(".*")[2]);
            $("#patientId").val(value.toString().split(".*")[0]);
            if(value.toString().split(".*")[4]!=""){
                $("#PatientIpdCertificateDob").val(value.toString().split(".*")[4]);
                var now = (new Date()).getFullYear();
                var age = now - $("#PatientIpdCertificateDob").val().split("-",1);
                $('#PatientIpdCertificateAge').val(age);
            }                     
            
            // condition select gender of patient
            if(value.toString().split(".*")[3]!=""){
                $("#PatientIpdCertificateSex").find("option").each(function(){                    
                    if($(this).val()==value.toString().split(".*")[3]){                        
                        $(this).attr("selected", true);
                    }
                });
            }
            // condition select patient's group
            if(value.toString().split(".*")[5]!=""){
                $("#PatientIpdCertificatePatientGroupId").find("option").each(function(){                    
                    if($(this).val()==value.toString().split(".*")[5]){
                        $(this).attr("selected", true);
                        if(value.toString().split(".*")[5]=="2"){
                            $("#PatientIpdCertificateNationality").show();                            
                            $("#PatientIpdCertificateNationality").find("option").each(function(){
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
                $("#PatientIpdCertificateLocationId").find("option").each(function(){                    
                    if($(this).val()==value.toString().split(".*")[10]){                        
                        $(this).attr("selected", true);
                    }
                });
            }
            
            $("#PatientIpdCertificateEmail").val(value.toString().split(".*")[6]);
            $("#PatientIpdCertificateOccupation").val(value.toString().split(".*")[7]);
            $("#PatientIpdCertificateTelephone").val(value.toString().split(".*")[8]);
            $("#PatientIpdCertificateAddress").val(value.toString().split(".*")[9]);
            
            $("#patientIpdId").val(value.toString().split(".*")[12]);
            $("#PatientIdpCode").text(value.toString().split(".*")[13]);
            $("#PatientIdpDateIpd").text(value.toString().split(".*")[17]);
            $("#PatientIdpDoctorId").text(value.toString().split(".*")[14]);
            $("#PatientIdpDepartmentId").text(value.toString().split(".*")[15]);
            $("#PatientIdpAllergies").text(value.toString().split(".*")[16]);
            
            // show image delete patient
            $(".searchPatientIpdCertificate").hide();
            $(".deleteSearchPatientIpdCertificate").show();
        });
        
        
        $(".searchPatientIpdCertificate").click(function(){            
            searchAllPatientIpdCertificate();            
        });
        // close search patient information
        
        // delete search patient
        $(".deleteSearchPatientIpdCertificate").click(function(){
            $("#patientId").val("");
            $(".trPatientIpdCertificateCode").text("");
            $("#PatientIpdCertificateDob").val("");
            $('#PatientIpdCertificateAge').val("");
            $("#PatientIpdCertificatePatientName").val("");
            $("#PatientIpdCertificatePatientGroupId").val("");
            $("#PatientIpdCertificateNationality").hide();
            $("#PatientIpdCertificateSex").val("");
            $("#PatientIpdCertificateEmail").val("");
            $("#PatientIpdCertificateOccupation").val("");
            $("#PatientIpdCertificateTelephone").val("");
            $("#PatientIpdCertificateAddress").val("");
            $("#PatientIpdCertificateLocationId").val("");
            
            $("#patientIpdId").val("");
            $("#PatientIdpCode").text("");
            $("#PatientIdpDateIpd").text("");
            $("#PatientIdpDoctorId").text("");
            $("#PatientIdpDepartmentId").text("");
            $("#PatientIdpAllergies").text("");
            
            $(".searchPatientIpdCertificate").show();
            $(".deleteSearchPatientIpdCertificate").hide();
        });
        // close delete search patient
        
        // for condition foreiner or cambodian
        if($("#PatientIpdCertificatePatientGroupId").val()==2){
            $("#PatientIpdCertificateNationality").show();
        }else if($("#PatientIpdCertificatePatientGroupId").val()==1){
            $("#PatientIpdCertificateNationality").find("option[value='']").attr("selected", true);
            $("#PatientIpdCertificateNationality").hide();
        }else{
            $("#PatientIpdCertificateNationality").find("option[value='']").attr("selected", true);
            $("#PatientIpdCertificateNationality").hide();
        }
        
        $("#PatientIpdCertificatePatientGroupId").change(function(){            
            if($("#PatientIpdCertificatePatientGroupId").val()==2){
                $("#PatientIpdCertificateNationality").show();
            }else if($("#PatientIpdCertificatePatientGroupId").val()==1){
                $("#PatientIpdCertificateNationality").find("option[value='']").attr("selected", true);
                $("#PatientIpdCertificateNationality").hide();
            }else{
                $("#PatientIpdCertificateNationality").find("option[value='']").attr("selected", true);
                $("#PatientIpdCertificateNationality").hide();                
            }                        
        });
        // close condition foreiner or cambodian                
        
        
        // condition select company 
        $("#PatientIpdCertificateCompanyId").change(function() {
            if($("#PatientIpdCertificateCompanyId").val()!=""){
                $("#patientIdpManagementInfo").show();
            }else{
                $("#patientIdpManagementInfo").hide();
            }
        });        
        
        // close select company
                             
        if($("#PatientIpdCertificateDob").val()!=''){
            var now = (new Date()).getFullYear();
            var age = now - $("#PatientIpdCertificateDob").val().split("-",1);
            $('#PatientIpdCertificateAge').val(age);
        }
        
        var dates = $("#PatientIpdCertificateDateCertificateFrom, #PatientIpdCertificateDateCertificateTo").datepicker({
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
                var option = this.id == "PatientIpdCertificateDateCertificateFrom" ? "minDate" : "maxDate",
                        instance = $(this).data("datepicker");
                date = $.datepicker.parseDate(
                        instance.settings.dateFormat ||
                        $.datepicker._defaults.dateFormat,
                        selectedDate, instance.settings);
                dates.not(this).datepicker("option", option, date);
            }
        }).unbind("blur");
        
        $(".btnBackPatientCertificate").dblclick(function(event){
            event.preventDefault();
            $('#PatientIpdCertificateAddForm').validationEngine('hideAll');
            oCache.iCacheLower = -1;
            oTablePatientCertificate.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
        
        
    });
    function checkBfSavePatient(){
        var formName = "#PatientIpdCertificateAddForm";
        var validateBack =$(formName).validationEngine("validate");
        if(!validateBack){            
            return false;
        }else{            
            return true;
        }
    }    
    // search all patient with button search
    function searchAllPatientIpdCertificate(){        
        $.ajax({
            type:   "POST",
            url:    "<?php echo $absolute_url . $this->params['controller']; ?>/getFindPatient/",
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
                                $(".trPatientIpdCertificateCode").text(value.toString().split(".*")[2]);
                                $("#patientCode").val(value.toString().split(".*")[2]);
                                $("#PatientIpdCertificatePatientName").val(value.toString().split(".*")[1]);
                                $("#patientId").val(value.toString().split(".*")[0]);
                                if(value.toString().split(".*")[4]!=""){
                                    $("#PatientIpdCertificateDob").val(value.toString().split(".*")[4]);
                                    var now = (new Date()).getFullYear();
                                    var age = now - $("#PatientIpdCertificateDob").val().split("-",1);
                                    $('#PatientIpdCertificateAge').val(age);
                                }                     

                                // condition select gender of patient
                                if(value.toString().split(".*")[3]!=""){
                                    $("#PatientIpdCertificateSex").find("option").each(function(){                    
                                        if($(this).val()==value.toString().split(".*")[3]){                        
                                            $(this).attr("selected", true);
                                        }
                                    });
                                }
                                // condition select patient's group
                                if(value.toString().split(".*")[5]!=""){
                                    $("#PatientIpdCertificatePatientGroupId").find("option").each(function(){                    
                                        if($(this).val()==value.toString().split(".*")[5]){
                                            $(this).attr("selected", true);
                                            if(value.toString().split(".*")[5]=="2"){
                                                $("#PatientIpdCertificateNationality").show();                            
                                                $("#PatientIpdCertificateNationality").find("option").each(function(){
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
                                    $("#PatientIpdCertificateLocationId").find("option").each(function(){                    
                                        if($(this).val()==value.toString().split(".*")[10]){                        
                                            $(this).attr("selected", true);
                                        }
                                    });
                                }

                                $("#PatientIpdCertificateEmail").val(value.toString().split(".*")[6]);
                                $("#PatientIpdCertificateOccupation").val(value.toString().split(".*")[7]);
                                $("#PatientIpdCertificateTelephone").val(value.toString().split(".*")[8]);
                                $("#PatientIpdCertificateAddress").val(value.toString().split(".*")[9]);
                                                                                                
                                $("#patientIpdId").val(value.toString().split(".*")[12]);
                                $("#PatientIdpCode").text(value.toString().split(".*")[13]);
                                $("#PatientIdpDateIpd").text(value.toString().split(".*")[17]);
                                $("#PatientIdpDoctorId").text(value.toString().split(".*")[14]);
                                $("#PatientIdpDepartmentId").text(value.toString().split(".*")[15]);
                                $("#PatientIdpAllergies").text(value.toString().split(".*")[16]);
                                
                                // action hidden search button and show delete button
                                $(".searchPatientIpdCertificate").hide();
                                $(".deleteSearchPatientIpdCertificate").show();
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
        <a href="#" class="positive btnBackPatientCertificate">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('PatientIpdCertificate'); ?>
<input id="patientId" name="data[Patient][id]" type="hidden" value=""/>
<input id="patientIpdId" name="data[PatientIpd][id]" type="hidden" value=""/>
<fieldset>
    <legend><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?></legend>
    <table style="width: 100%;" cellspacing="0">
        <tr>
            <td style="width: 15%;"><?php echo PATIENT_CODE; ?> <span class="red">*</span> :</td>
            <td style="width: 35%;" class="trPatientIpdCertificateCode"> </td>
            <td style="width: 15%;"><label for="PatientIpdCertificateDob"><?php echo TABLE_DOB; ?> <span class="red">*</span> :</label></td>
            <td style="width: 35%;">
                <?php echo $this->Form->text('dob', array('name' => "data[Patient][dob]", 'class' => 'validate[required]','onkeypress'=>'return isNumberKey(event)', 'style' => "width:235px")); ?>
                <label for="PatientIpdCertificateAge"><?php echo TABLE_AGE; ?>:</label>
                <?php echo $this->Form->text('age',array('style'=>'width:30px;', 'class' => 'number validate[required,maxSize[3]]', 'maxlength' => '3', 'readonly' => true)); ?>
            </td>
        </tr>
        <tr>
            <td><label for="PatientIpdCertificatePatientName"><?php echo PATIENT_NAME; ?> <span class="red">*</span> :</label></td>
            <td>
                <?php echo $this->Form->text('patient_name', array('class' => 'validate[required]', 'name' => "data[Patient][patient_name]")); ?>
                <img alt="Search" align="absmiddle" style="cursor: pointer;" class="searchPatientIpdCertificate" onmouseover="Tip('<?php echo GENERAL_SEARCH; ?>')" src="<?php echo $this->webroot . 'img/button/search.png'; ?>" />
                <img alt="Delete" align="absmiddle" style="cursor: pointer; display: none;" class="deleteSearchPatientIpdCertificate" onmouseover="Tip('<?php echo ACTION_DELETE; ?>')" src="<?php echo $this->webroot . 'img/button/delete.png'; ?>" />
            </td>
            <td><label for="PatientIpdCertificateNationality"><?php echo TABLE_NATIONALITY; ?> <span class="red">*</span> :</label></td>
            <td>
                <?php echo $this->Form->input('patient_group_id', array('name' => "data[Patient][patient_group_id]", 'empty' => SELECT_OPTION, 'label' => false,'class' => 'validate[required]', 'style' => 'width:150px;', 'readonly' => true)); ?>                
                <?php echo $this->Form->input('nationality', array('name' => "data[Patient][nationality]", 'empty' => SELECT_OPTION, 'label' => false,'class' => 'validate[required]', 'style' => 'width:156px;display:none;', 'readonly' => true)); ?>
            </td>
        </tr>      
        <tr>
            <td><label for="PatientIpdCertificateSex"><?php echo TABLE_SEX; ?> <span class="red">*</span> :</label></td>
            <td><?php echo $this->Form->input('sex', array('name' => "data[Patient][sex]", 'empty' => SELECT_OPTION, 'label' => false, 'class' => 'validate[required]', 'readonly' => true)); ?></td>
            <td><label for="PatientIpdCertificateEmail"><?php echo TABLE_EMAIL; ?> :</label></td>
            <td><?php echo $this->Form->text('email', array('name' => "data[Patient][email]", 'class' => 'validate[custom[email]]', 'readonly' => true)); ?></td>            
        </tr>
        <tr>            
            <td><label for="PatientIpdCertificateOccupation"><?php echo TABLE_OCCUPATION; ?> :</label></td>
            <td><?php echo $this->Form->text('occupation', array('name' => "data[Patient][occupation]", 'readonly' => true)); ?></td>
            <td><label for="PatientIpdCertificateTelephone"><?php echo TABLE_TELEPHONE; ?>:</label></td>
            <td><?php echo $this->Form->text('telephone', array('name' => "data[Patient][telephone]", 'class' => 'validate[custom[phone]]', 'readonly' => true)); ?></td>
        </tr>        
        <tr>
            <td><label for="PatientIpdCertificateAddress"><?php echo TABLE_ADDRESS; ?> :</label></td>
            <td><?php echo $this->Form->text('address', array('name' => "data[Patient][address]", 'readonly' => true)); ?></td>
            <td><label for="PatientIpdCertificateLocationId"><?php echo TABLE_CITY_PROVINCE; ?> :</label></td>
            <td><?php echo $this->Form->input('location_id', array('name' => "data[Patient][location_id]", 'empty' => SELECT_OPTION, 'label' => false, 'readonly' => true)); ?></td>
        </tr>
    </table>     
</fieldset>
<br/>
<fieldset id="patientIdpManagementInfo">
    <legend><?php __(MENU_IPD_MANAGEMENT_INFO); ?></legend>
    <table style="width: 100%;" cellspacing="0">
        <tr>
            <td style="width: 15%;"><label for="PatientIdpCode"><?php echo TABLE_HN; ?> <span class="red">*</span> :</label></td>
            <td style="width: 35%;" id="PatientIdpCode"></td>
            <td style="width: 15%;height: 25px;"><label for="PatientIdpDateIpd"><?php echo TABLE_PATIENT_CHECK_IN_DATE; ?> <span class="red">*</span> :</label></td>
            <td style="width: 35%;" id="PatientIdpDateIpd"></td>            
        </tr>
        <tr>
            <td style="height: 25px;"><label for="PatientIdpDoctorId"><?php echo TABLE_ADMITTING_PHYSICIAN; ?> <span class="red">*</span> :</label></td>
            <td id="PatientIdpDoctorId"></td>
            <td style="height: 25px;"><label for="PatientIdpDepartmentId"><?php echo TABLE_DEPARTMENT; ?> <span class="red">*</span> :</label></td>
            <td id="PatientIdpDepartmentId"></td>
        </tr>
        <tr>
            <td style="height: 25px;"><label for="PatientIdpAllergies"><?php echo TABLE_ALLERGIC; ?> <span class="red">*</span> :</label></td>
            <td id="PatientIdpAllergies"></td>
        </tr>        
    </table>
</fieldset>  
<br/>
<fieldset id="PatientIpdCertificateInfo">
    <legend><?php __(MENU_PATIENT_IPD_CERTIFICATE_INFO); ?></legend>
    <table style="width: 100%;" cellspacing="0">               
        <tr>
            <td style="width: 15%;"><label for="PatientIpdCertificateDateCertificateFrom"><?php echo TABLE_PARENT_IPD_DATE_CERTIFICATE_FROM; ?> <span class="red">*</span> :</label></td>
            <td style="width: 35%;">
                <?php echo $this->Form->text('date_certificate_from', array('class' => 'validate[required]')); ?>                
            </td>
            <td style="width: 15%;"><label for="PatientIpdCertificateDateCertificateTo"><?php echo TABLE_PARENT_IPD_DATE_CERTIFICATE_TO; ?> <span class="red">*</span> :</label></td>
            <td style="width: 35%;">
                <?php echo $this->Form->text('date_certificate_to', array('class' => 'validate[required]')); ?>
            </td>
        </tr>            
    </table>
</fieldset>    
<div class="clear"></div>
<div class="buttons">
    <button type="submit" class="positive savePatientCertificate" >
        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
        <span class="txtSavePatient"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<?php echo $this->Form->end(); ?>

