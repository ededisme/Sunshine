<?php echo $this->element('prevent_multiple_submit'); ?>
<?php 
$absolute_url = FULL_BASE_URL . Router::url("/", false);
echo $javascript->link('uninums.min'); 
?>
<script type="text/javascript">
    $(document).ready(function(){         
        $('#loading').hide();
        $('#AppointmentAppDate').datepicker(
        {
            changeMonth: true,
            changeYear: true,
            showSecond: false,
            dateFormat:'dd/mm/yy'            
        });
        
        $('#btnSearch').click(function(){
            $.ajax({
                type: "POST",
                url: '<?php echo $this->base; ?>/patients/getPatient',
                data: "patient=" + $('#PatientSearch').val(),
                beforeSend: function(){
                    $('#loading').show();
                },
                success: function(msg){
                    $('#loading').hide();
                    $("#PatientList").html(msg);
                }
            });
        });
        
        $(".searchPatientAppointment").click(function(){            
            searchAllPatientAppointment();            
        });
        // delete search patient
        $(".deleteSearchPatientAppointment").click(function(){
            $("#patientId").val("");
            $("#AppointmentPatientName").val("");
            $(".searchPatientAppointment").show();
            $(".deleteSearchPatientAppointment").hide();
        });
        
        $("#AppointmentAddForm").validationEngine();
        $("#AppointmentAddForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSavePatient").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            beforeSerialize: function($form, options) {                
                $("#AppointmentAppDate").datepicker("option", "dateFormat", "yy-mm-dd");                
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");                
                $(".btnBackPatientAppointment").dblclick();
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
        $(".savePatientAppointment").click(function(){
            if(checkBfSavePatient() == true){
                return true;
            }else{
                return false;
            }
        }); 
        $(".btnBackPatientAppointment").dblclick(function(event){
            event.preventDefault();
            $('#AppointmentAddForm').validationEngine('hideAll');
            oCache.iCacheLower = -1;
            oTablePatientAppointment.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });
    // search all patient with button search
    function searchAllPatientAppointment(){        
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
                                $("#AppointmentPatientName").val(value.toString().split(".*")[2]+"-"+value.toString().split(".*")[1]);
                                $("#patientId").val(value.toString().split(".*")[0]);
                                
                                // action hidden search button and show delete button
                                $(".searchPatientAppointment").hide();
                                $(".deleteSearchPatientAppointment").show();
                            }
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
    }
    
    function checkBfSavePatient(){
        var formName = "#AppointmentAddForm";
        var validateBack =$(formName).validationEngine("validate");
        if(!validateBack){            
            return false;
        }else{            
            return true;
        }
    }   
</script>
<div style="padding: 5px;border: 1px dashed #3C69AD;">
    <div class="buttons">
        <a href="#" class="positive btnBackPatientAppointment">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('Appointment'); ?>
<fieldset style="padding: 5px;border: 1px dashed #3C69AD;">
    <legend style="background: #EDEDED; font-weight: bold;"><?php __(MENU_APPOINTMENT_MANAGEMENT); ?></legend>
    <table>
        <tr>     
            <td><label for="patient_name"><?php echo PATIENT_NAME; ?> <span class="red"> *</span> :</label></td>
            <td>
                <?php echo $this->Form->hidden('patient_id', array('id' => 'patientId')); ?>                
                <?php echo $this->Form->text('patient_name', array('class' => 'validate[required]', 'readonly' => true)); ?>
                &nbsp;&nbsp;
                <img alt="Search" align="absmiddle" style="cursor: pointer;width: 24px;height: 24px;" class="searchPatientAppointment" onmouseover="Tip('<?php echo GENERAL_SEARCH; ?>')" src="<?php echo $this->webroot . 'img/button/search.png'; ?>" />
                <img alt="Delete" align="absmiddle" style="cursor: pointer; display: none;" class="deleteSearchPatientAppointment" onmouseover="Tip('<?php echo ACTION_DELETE; ?>')" src="<?php echo $this->webroot . 'img/button/delete.png'; ?>" />
                &nbsp;&nbsp;
                <img alt="<?php echo MENU_PATIENT_MANAGEMENT_ADD; ?>" align="absmiddle" style="cursor: pointer; width: 16px; display: none;" id="addCustomerSales" onmouseover="Tip('<?php echo MENU_PATIENT_MANAGEMENT_ADD; ?>')" src="<?php echo $this->webroot . 'img/button/plus.png'; ?>" />
            </td>
        </tr>
        <tr>
            <td><label for="AppointmentAppDate"><?php echo APPOINTMENT_DATE; ?> <span class="red">*</span> :</label></td>
            <td><?php echo $this->Form->text('app_date', array('class' => 'validate[required]', 'readonly' => true)); ?></td>
        </tr>
        <tr>
            <td><label for="patientDoctorId"><?php echo DOCTOR_DOCTOR; ?><span class="red">*</span>  :</label></td>
            <td>
                <select id="patientDoctorId" name="data[Appointment][doctor_id]" class='validate[required]'>
                    <option value=""><?php echo SELECT_OPTION;?></option>
                    <?php 
                    foreach ($doctors as $doctor) {
                        echo '<option value="'.$doctor['User']['id'].'">'.$doctor['Employee']['name'].'</option>';
                    }
                    ?>
                </select>                
            </td>
        </tr>
        <tr>
            <td><label for="AppointmentDescription"><?php echo GENERAL_DESCRIPTION; ?> :</label></td>
            <td><?php echo $this->Form->textarea('description'); ?></td>
        </tr>
    </table>
</fieldset>
<br />
<div class="buttons">
    <button type="submit" class="positive savePatientAppointment" >
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <span class="txtSavePatient"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<?php echo $this->Form->end(); ?>