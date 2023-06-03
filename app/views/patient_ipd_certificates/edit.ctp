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
        $("#PatientIpdCertificateEditForm").validationEngine();
        $("#PatientIpdCertificateEditForm").ajaxForm({
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
                        success: function(printPatientIPDCertificate){
                            w=window.open();
                            w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                            w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                            w.document.write(printPatientIPDCertificate);
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
        // close condition foreiner or cambodian
        
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
            $('#PatientIpdCertificateEditForm').validationEngine('hideAll');
            oCache.iCacheLower = -1;
            oTablePatientCertificate.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });
    function checkBfSavePatient(){
        var formName = "#PatientIpdCertificateEditForm";
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
        <a href="#" class="positive btnBackPatientCertificate">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('PatientIpdCertificate'); ?>
<?php echo $this->Form->input('id'); ?>
<input name="data[PatientIpdCertificate][id]" type="hidden" value="<?php echo $patient['PatientIpdCertificate']['id'];?>"/>
<input name="data[Patient][id]" type="hidden" value="<?php echo $patient['Patient']['id'];?>"/>
<input name="data[PatientIpd][id]" type="hidden" value="<?php echo $patient['PatientIpd']['id'];?>"/>
<fieldset>
    <legend><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?></legend>
    <table style="width: 100%;" cellspacing="3">
        <tr>
            <td style="width: 15%;"><?php echo PATIENT_CODE; ?> <span class="red">*</span> :</td>
            <td style="width: 35%;" class="trPatientIpdCertificateCode"><?php echo $patient['Patient']['patient_code'];?></td>
            <td style="width: 15%;"><label for="PatientIpdCertificateDob"><?php echo TABLE_DOB; ?> <span class="red">*</span> :</label></td>
            <td style="width: 35%;">
                <?php echo date("d/m/Y", strtotime($patient['Patient']['dob']));; ?>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?php 
                echo TABLE_AGE.': ';
                $then_ts = strtotime($patient['Patient']['dob']);
                $then_year = date('Y', $then_ts);
                $age = date('Y') - $then_year;
                if(strtotime('+' . $age . ' years', $then_ts) > time()) $age--;              

                if($age==0){
                    $then_year = date('m', $then_ts);
                    $month = date('m') - $then_year;
                    if(strtotime('+' . $month . ' month', $then_ts) > time()) $month--;
                    echo $month.' '.GENERAL_MONTH;
                }else{
                    echo $age.' '.GENERAL_YEAR_OLD;
                }
                ?> 
            </td>
        </tr>
        <tr>
            <td><label for="PatientIpdCertificatePatientName"><?php echo PATIENT_NAME; ?> <span class="red">*</span> :</label></td>
            <td>
                <?php echo $patient['Patient']['patient_name']; ?>
            </td>
            <td><label for="PatientIpdCertificateNationality"><?php echo TABLE_NATIONALITY; ?> <span class="red">*</span> :</label></td>
            <td>
                <?php                 
                if($patient['Patient']['patient_group_id']!=""){
                    $query = mysql_query("SELECT name FROM patient_groups WHERE id=".$patient['Patient']['patient_group_id']);
                    while ($row = mysql_fetch_array($query)) {
                        echo $row['name'].'&nbsp;&nbsp;('.$patient['Nationality']['name'].')';
                    }
                }else{
                    echo $patient['Nationality']['name'];
                }
                ?>
            </td>
        </tr>      
        <tr>
            <td><label for="PatientIpdCertificateSex"><?php echo TABLE_SEX; ?> <span class="red">*</span> :</label></td>
            <td>
                <?php 
                if ($patient['Patient']['sex'] == "F") {
                    echo GENERAL_FEMALE;
                } else {
                    echo GENERAL_MALE;
                }
                ?>
            </td>
            <td><label for="PatientIpdCertificateEmail"><?php echo TABLE_EMAIL; ?> :</label></td>
            <td><?php echo $patient['Patient']['email']; ?></td>
        </tr>
        <tr>            
            <td><label for="PatientIpdCertificateOccupation"><?php echo TABLE_OCCUPATION; ?> :</label></td>
            <td><?php echo $patient['Patient']['occupation']; ?></td>
            <td><label for="PatientIpdCertificateTelephone"><?php echo TABLE_TELEPHONE; ?>:</label></td>
            <td><?php echo $patient['Patient']['telephone']; ?></td>
        </tr>        
        <tr>
            <td><label for="PatientIpdCertificateAddress"><?php echo TABLE_ADDRESS; ?> :</label></td>
            <td><?php echo $patient['Patient']['address']; ?></td>
            <td><label for="PatientIpdCertificateLocationId"><?php echo TABLE_CITY_PROVINCE; ?> :</label></td>
            <td>
                <?php
                if($patient['Patient']['location_id']!=""){
                    $query = mysql_query("SELECT name FROM patient_locations WHERE id=".$patient['Patient']['location_id']);
                    if(mysql_num_rows($query)){
                        while ($row = mysql_fetch_array($query)) {
                            echo $row['name'];                
                        }
                    }
                }
                ?>
            </td>            
        </tr>
    </table>     
</fieldset>
<br/>
<fieldset id="patientIdpManagementInfo">
    <legend><?php __(MENU_IPD_MANAGEMENT_INFO); ?></legend>
    <table style="width: 100%;" cellspacing="0">
        <tr>
            <td style="width: 15%;"><label for="PatientIdpCode"><?php echo TABLE_HN; ?> <span class="red">*</span> :</label></td>
            <td style="width: 35%;" id="PatientIdpCode"><?php echo $patient['PatientIpd']['ipd_code']; ?></td>
            <td style="width: 15%;height: 25px;"><label for="PatientIdpDateIpd"><?php echo TABLE_PATIENT_CHECK_IN_DATE; ?> <span class="red">*</span> :</label></td>
            <td style="width: 35%;" id="PatientIdpDateIpd"><?php echo date("d/m/Y H:i:s", strtotime($patient['PatientIpd']['date_ipd'])); ?></td>            
        </tr>
        <tr>
            <td style="height: 25px;"><label for="PatientIdpDoctorId"><?php echo TABLE_ADMITTING_PHYSICIAN; ?> <span class="red">*</span> :</label></td>
            <td id="PatientIdpDoctorId"><?php echo $doctor['Employee']['name']; ?></td>
            <td style="height: 25px;"><label for="PatientIdpDepartmentId"><?php echo TABLE_DEPARTMENT; ?> <span class="red">*</span> :</label></td>
            <td id="PatientIdpDepartmentId"><?php echo $department['Group']['name'];?></td>
        </tr>
        <tr>
            <td style="height: 25px;"><label for="PatientIdpAllergies"><?php echo TABLE_ALLERGIC; ?> <span class="red">*</span> :</label></td>
            <td id="PatientIdpAllergies"><?php echo $patient['PatientIpd']['allergies']; ?></td>
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
                <?php echo $this->Form->text('date_certificate_from', array('class' => 'validate[required]', 'value' => $patient['PatientIpdCertificate']['date_certificate_from'])); ?>
            </td>
            <td style="width: 15%;"><label for="PatientIpdCertificateDateCertificateTo"><?php echo TABLE_PARENT_IPD_DATE_CERTIFICATE_TO; ?> <span class="red">*</span> :</label></td>
            <td style="width: 35%;">
                <?php echo $this->Form->text('date_certificate_to', array('class' => 'validate[required]', 'value' => $patient['PatientIpdCertificate']['date_certificate_to'])); ?>
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

