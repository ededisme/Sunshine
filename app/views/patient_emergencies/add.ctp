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
        $("#PatientEmergencyAddForm").validationEngine();
        $("#PatientEmergencyAddForm").ajaxForm({
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
        
        // search patient information
        $("#PatientEmergencyPatientName").autocomplete("<?php echo $absolute_url . 'patients' . "/searchPatient"; ?>", {        
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
            $(".trPatientEmergencyCode").text(value.toString().split(".*")[2]);
            $("#patientCode").val(value.toString().split(".*")[2]);
            $("#patientId").val(value.toString().split(".*")[0]);
            if(value.toString().split(".*")[4]!=""){
                $("#PatientEmergencyDob").val(value.toString().split(".*")[4]);
                var now = (new Date()).getFullYear();
                var age = now - $("#PatientEmergencyDob").val().split("-",1);
                $('#PatientEmergencyAge').val(age);
            }                     
            
            // condition select gender of patient
            if(value.toString().split(".*")[3]!=""){
                $("#PatientEmergencySex").find("option").each(function(){                    
                    if($(this).val()==value.toString().split(".*")[3]){                        
                        $(this).attr("selected", true);
                    }
                });
            }
            // condition select patient's group
            if(value.toString().split(".*")[5]!=""){
                $("#PatientEmergencyPatientGroupId").find("option").each(function(){                    
                    if($(this).val()==value.toString().split(".*")[5]){
                        $(this).attr("selected", true);
                        if(value.toString().split(".*")[5]=="2"){
                            $("#PatientEmergencyNationality").show();                            
                            $("#PatientEmergencyNationality").find("option").each(function(){
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
                $("#PatientEmergencyLocationId").find("option").each(function(){                    
                    if($(this).val()==value.toString().split(".*")[10]){                        
                        $(this).attr("selected", true);
                    }
                });
            }
            
            // condition select patient type of bill 
            if(value.toString().split(".*")[12]!=""){
                $("#PatientEmergencyPatientBillTypeId").find("option").each(function(){                    
                    if($(this).val()==value.toString().split(".*")[12]){                        
                        $(this).attr("selected", true);
                    }                    
                });
                if(value.toString().split(".*")[12] == 3){
                    $("#PatientEmergencyCompanyInsuranceId").show();
                    $("#PatientEmergencyCompanyInsuranceId").find("option").each(function(){                    
                        if($(this).val()==value.toString().split(".*")[13]){                        
                            $(this).attr("selected", true);
                        }                    
                    });
                    if(value.toString().split(".*")[13]==0){                        
                        $("#PatientInsuranceNote").val(value.toString().split(".*")[14]);
                        $("#insuranceNote").show();
                    }
                }
                    
            }
            
            $("#PatientEmergencyEmail").val(value.toString().split(".*")[6]);
            $("#PatientEmergencyOccupation").val(value.toString().split(".*")[7]);
            $("#PatientEmergencyTelephone").val(value.toString().split(".*")[8]);
            $("#PatientEmergencyAddress").val(value.toString().split(".*")[9]);
            
            // show image delete patient
            $(".searchPatientEmergency").hide();
            $(".deleteSearchPatientEmergency").show();
        });
        
        
        $(".searchPatientEmergency").click(function(){            
            searchAllPatientEmergency();            
        });
        // close search patient information
        
        // delete search patient
        $(".deleteSearchPatientEmergency").click(function(){
            $(".trPatientEmergencyCode").text($("#getPatientEmergencyCode").val());
            $("#patientCode").val($("#getPatientEmergencyCode").val());
            $("#patientId").val("");
            $("#PatientEmergencyDob").val("");
            $('#PatientEmergencyAge').val("");
            $("#PatientEmergencyPatientName").val("");
            $("#PatientEmergencyPatientGroupId").val("");
            $("#PatientEmergencyNationality").hide();
            $("#PatientEmergencySex").val("");
            $("#PatientEmergencyEmail").val("");
            $("#PatientEmergencyOccupation").val("");
            $("#PatientEmergencyTelephone").val("");
            $("#PatientEmergencyAddress").val("");
            $("#PatientEmergencyLocationId").val("");
            $("#PatientEmergencyPatientBillTypeId").val("");
            $("#PatientEmergencyCompanyInsuranceId").val("");
            $("#PatientInsuranceNote").val("");
            
            $("#PatientEmergencyCompanyInsuranceId").hide();
            $("#PatientInsuranceNote").hide();
            $(".searchPatientEmergency").show();
            $(".deleteSearchPatientEmergency").hide();
        });
        // close delete search patient
        
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
        if($("#PatientEmergencyCompanyId").val()!=""){            
            $("#patientIdpManagementInfo").show();
            $(".classRoom").closest("tr").find("td .classRoom").val('');
            $(".classRoom").closest("tr").find("td .classRoom option[class!='']").hide();
            $(".classRoom").closest("tr").find("td .classRoom option[class='"  + $("#PatientEmergencyCompanyId").val() + "']").show();

            $(".classDoctor").closest("tr").find("td .classDoctor").val('');
            $(".classDoctor").closest("tr").find("td .classDoctor option[class!='']").hide();
            $(".classDoctor").closest("tr").find("td .classDoctor option[class='"  + $("#PatientEmergencyCompanyId").val() + "']").show();

            $(".classDepartment").closest("tr").find("td .classDepartment").val('');
            $(".classDepartment").closest("tr").find("td .classDepartment option[class!='']").hide();
            $(".classDepartment").closest("tr").find("td .classDepartment option[class='"  + $("#PatientEmergencyCompanyId").val() + "']").show();
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
             
        // check patient insurance
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
            $('#PatientEmergencyAddForm').validationEngine('hideAll');
            oCache.iCacheLower = -1;
            oTablePatientEmergecy.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
        
    });
        
    function checkBfSavePatient(){
        var formName = "#PatientEmergencyAddForm";
        var validateBack =$(formName).validationEngine("validate");
        if(!validateBack){            
            return false;
        }else{            
            return true;
        }
    }   
    
    // search all patient with button search
    function searchAllPatientEmergency(){        
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
                                $(".trPatientEmergencyCode").text(value.toString().split(".*")[2]);
                                $("#patientCode").val(value.toString().split(".*")[2]);
                                $("#PatientEmergencyPatientName").val(value.toString().split(".*")[1]);
                                $("#patientId").val(value.toString().split(".*")[0]);
                                if(value.toString().split(".*")[4]!=""){
                                    $("#PatientEmergencyDob").val(value.toString().split(".*")[4]);
                                    var now = (new Date()).getFullYear();
                                    var age = now - $("#PatientEmergencyDob").val().split("-",1);
                                    $('#PatientEmergencyAge').val(age);
                                }                     

                                // condition select gender of patient
                                if(value.toString().split(".*")[3]!=""){
                                    $("#PatientEmergencySex").find("option").each(function(){                    
                                        if($(this).val()==value.toString().split(".*")[3]){                        
                                            $(this).attr("selected", true);
                                        }
                                    });
                                }
                                // condition select patient's group
                                if(value.toString().split(".*")[5]!=""){
                                    $("#PatientEmergencyPatientGroupId").find("option").each(function(){                    
                                        if($(this).val()==value.toString().split(".*")[5]){
                                            $(this).attr("selected", true);
                                            if(value.toString().split(".*")[5]=="2"){
                                                $("#PatientEmergencyNationality").show();                            
                                                $("#PatientEmergencyNationality").find("option").each(function(){
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
                                    $("#PatientEmergencyLocationId").find("option").each(function(){                    
                                        if($(this).val()==value.toString().split(".*")[10]){                        
                                            $(this).attr("selected", true);
                                        }
                                    });
                                }
                                
                                // condition select patient type of bill 
                                if(value.toString().split(".*")[11]!=""){
                                    $("#PatientEmergencyPatientBillTypeId").find("option").each(function(){                    
                                        if($(this).val()==value.toString().split(".*")[11]){                        
                                            $(this).attr("selected", true);
                                        }                    
                                    });
                                    if(value.toString().split(".*")[11] == 3){
                                        $("#PatientEmergencyCompanyInsuranceId").show();
                                        $("#PatientEmergencyCompanyInsuranceId").find("option").each(function(){                    
                                            if($(this).val()==value.toString().split(".*")[12]){                        
                                                $(this).attr("selected", true);
                                            }                    
                                        });
                                        if(value.toString().split(".*")[12]==0){
                                            $("#PatientInsuranceNote").val(value.toString().split(".*")[13]);
                                            $("#insuranceNote").show();
                                        }
                                    }

                                }

                                $("#PatientEmergencyEmail").val(value.toString().split(".*")[6]);
                                $("#PatientEmergencyOccupation").val(value.toString().split(".*")[7]);
                                $("#PatientEmergencyTelephone").val(value.toString().split(".*")[8]);
                                $("#PatientEmergencyAddress").val(value.toString().split(".*")[9]);
                                
                                // action hidden search button and show delete button
                                $(".searchPatientEmergency").hide();
                                $(".deleteSearchPatientEmergency").show();
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
        <a href="#" class="positive btnBackPatientEmergency">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('PatientEmergency'); ?>
<input id="patientCode" name="data[Patient][patient_code]" type="hidden" value="<?php echo $code;?>"/>
<input id="getPatientEmergencyCode" type="hidden" value="<?php echo $code;?>"/>
<input id="patientId" name="data[Patient][id]" type="hidden" value=""/>
<fieldset>
    <legend><?php __(MENU_PATIENT_IPD_INFO); ?></legend>
    <table style="width: 100%;" cellspacing="0">
        <tr>
            <td style="width: 15%;"><?php echo PATIENT_CODE; ?> <span class="red">*</span> :</td>
            <td style="width: 35%;" class="trPatientEmergencyCode">                
                <?php echo $code; ?>                
            </td>
            <td style="width: 15%;"><label for="PatientEmergencyDob"><?php echo TABLE_DOB; ?> <span class="red">*</span> :</label></td>
            <td style="width: 35%;">
                <?php echo $this->Form->text('dob', array('name' => "data[Patient][dob]", 'class' => 'validate[required]','onkeypress'=>'return isNumberKey(event)', 'style' => "width:228px")); ?>
                <label for="PatientEmergencyAge"><?php echo TABLE_AGE; ?>:</label>
                <?php echo $this->Form->text('age',array('style'=>'width:30px;', 'class' => 'number validate[required,maxSize[3]]', 'maxlength' => '3')); ?>
            </td>
        </tr>
        <tr>
            <td><label for="PatientEmergencyPatientName"><?php echo PATIENT_NAME; ?> <span class="red">*</span> :</label></td>
            <td>
                <?php echo $this->Form->text('patient_name', array('class' => 'validate[required]', 'name' => "data[Patient][patient_name]")); ?>
                <img alt="Search" align="absmiddle" style="cursor: pointer;" class="searchPatientEmergency" onmouseover="Tip('<?php echo GENERAL_SEARCH; ?>')" src="<?php echo $this->webroot . 'img/button/search.png'; ?>" />
                <img alt="Delete" align="absmiddle" style="cursor: pointer; display: none;" class="deleteSearchPatientEmergency" onmouseover="Tip('<?php echo ACTION_DELETE; ?>')" src="<?php echo $this->webroot . 'img/button/delete.png'; ?>" />
            </td>
            <td><label for="PatientEmergencyNationality"><?php echo TABLE_NATIONALITY; ?> <span class="red">*</span> :</label></td>
            <td>
                <?php echo $this->Form->input('patient_group_id', array('name' => "data[Patient][patient_group_id]", 'empty' => SELECT_OPTION, 'label' => false,'class' => 'validate[required]', 'style' => 'width:150px;')); ?>                
                <?php echo $this->Form->input('nationality', array('name' => "data[Patient][nationality]", 'empty' => SELECT_OPTION, 'label' => false,'class' => 'validate[required]', 'style' => 'width:156px;display:none;')); ?>
            </td>
        </tr>      
        <tr>
            <td><label for="PatientEmergencySex"><?php echo TABLE_SEX; ?> <span class="red">*</span> :</label></td>
            <td><?php echo $this->Form->input('sex', array('name' => "data[Patient][sex]", 'empty' => SELECT_OPTION, 'label' => false, 'class' => 'validate[required]')); ?></td>
            <td><label for="PatientEmergencyEmail"><?php echo TABLE_EMAIL; ?> :</label></td>
            <td><?php echo $this->Form->text('email', array('name' => "data[Patient][email]", 'class' => 'validate[custom[email]]')); ?></td>            
        </tr>
        <tr>            
            <td><label for="PatientEmergencyOccupation"><?php echo TABLE_OCCUPATION; ?> :</label></td>
            <td><?php echo $this->Form->text('occupation', array('name' => "data[Patient][occupation]")); ?></td>
            <td><label for="PatientEmergencyTelephone"><?php echo TABLE_TELEPHONE; ?>:</label></td>
            <td><?php echo $this->Form->text('telephone', array('name' => "data[Patient][telephone]", 'class' => 'validate[custom[phone]]')); ?></td>
        </tr>        
        <tr>
            <td><label for="PatientEmergencyAddress"><?php echo TABLE_ADDRESS; ?> :</label></td>
            <td><?php echo $this->Form->text('address', array('name' => "data[Patient][address]")); ?></td>
            <td><label for="PatientEmergencyLocationId"><?php echo TABLE_CITY_PROVINCE; ?> :</label></td>
            <td><?php echo $this->Form->input('location_id', array('name' => "data[Patient][location_id]", 'empty' => SELECT_OPTION, 'label' => false)); ?></td>            
        </tr>    
        <tr>
            <td><label for="PatientEmergencyPatientBillTypeId"><?php echo TABLE_BILL_PAID_BY; ?><span class="red">*</span> :</label></td>
            <td>
                <?php echo $this->Form->input('patient_bill_type_id', array('name' => "data[Patient][patient_bill_type_id]", 'empty' => SELECT_OPTION, 'label' => false, 'class' => 'validate[required]', 'style' => 'width:153px;')); ?>
                <?php echo $this->Form->input('company_insurance_id', array('name' => "data[Patient][company_insurance_id]", 'empty' => SELECT_OPTION, 'label' => false, 'class' => 'validate[required]', 'style' => 'width:153px;')); ?>
            </td>            
            <td><label for="PatientEmergencyTypeId"><?php echo TABLE_PATIENT_TYPE; ?> :</label></td>
            <td><?php echo $this->Form->input('patient_type_id', array('name' => "data[Patient][patient_type_id]", 'empty' => SELECT_OPTION, 'default' => '3' , 'label' => false)); ?></td>
        </tr>
        <tr id="insuranceNote" style="display: none;">
            <td><?php echo TABLE_NOTE;?></td>
            <td>
                <?php echo $this->Form->textarea('insurance_note', array('name' => "data[Patient][insurance_note]", 'label' => false, 'style' => 'width:295px;')); ?>
            </td>
        </tr>
        <tr>
            <td><label for="PatientEmergencyCompanyId"><?php echo TABLE_COMPANY; ?> <span class="red">*</span> :</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->input('company_id', array('empty' => SELECT_OPTION, 'default' => '1', 'class' => 'classCompany validate[required]', 'label' => false)); ?>
                </div>
            </td>
        </tr>        
    </table>     
</fieldset>
<br/>
<fieldset id="patientIdpManagementInfo" style="display:none;">
    <legend><?php __(MENU_PATIENT_EMERGENCY_MANAGEMENT_INFO); ?></legend>
    <table style="width: 100%;" cellspacing="0">
        <tr>
            <td style="width: 15%;"><label for="PatientEmergencyDateIpd"><?php echo TABLE_PATIENT_CHECK_IN_DATE; ?> <span class="red">*</span> :</label></td>
            <td style="width: 35%;">
                <?php echo $this->Form->text('date_ipd', array('class' => 'validate[required]', 'onkeypress'=>'return isNumberKey(event)', 'value' => date("Y-m-d H:i"))); ?>
            </td>
            <td style="width: 15%;"><label for="PatientEmergencyRoomId"><?php echo TABLE_ROOM_NUMBER; ?> <span class="red">*</span> :</label></td>
            <td style="width: 35%;">
                <select id="PatientEmergencyRoomId" name="data[PatientEmergency][room_id]" class="classRoom validate[required]">
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
            <td><label for="PatientEmergencyDoctorId"><?php echo TABLE_ADMITTING_PHYSICIAN; ?> <span class="red">*</span> :</label></td>
            <td>
                
                <select id="PatientEmergencyDoctorId" name="data[PatientEmergency][doctor_id]" class="classDoctor validate[required]">
                    <option value=""><?php echo SELECT_OPTION;?></option>
                    <?php 
                    foreach ($doctors as $doctor) {
                        echo '<option class="'.$doctor['Company']['id'].'" value="'.$doctor['User']['id'].'">'.$doctor['Employee']['name'].'</option>';
                    }
                    ?>
                </select>                
            </td>
            <td><label for="PatientEmergencyDepartmentId"><?php echo TABLE_DEPARTMENT; ?> <span class="red">*</span> :</label></td>
            <td>                
                <select id="PatientEmergencyDepartmentId" name="data[PatientEmergency][department_id]" class="classDepartment validate[required]">
                    <option value=""><?php echo SELECT_OPTION;?></option>
                    <?php 
                    foreach ($departments as $department) {
                        echo '<option class="'.$department['Department']['company_id'].'" value="'.$department['Department']['id'].'">'.$department['Department']['name'].'</option>';
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