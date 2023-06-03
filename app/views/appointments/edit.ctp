<?php echo $this->element('prevent_multiple_submit'); ?>
<?php 
$absolute_url = FULL_BASE_URL . Router::url("/", false);
echo $javascript->link('uninums.min'); 
?>
<script type="text/javascript">
    $(document).ready(function(){
        $("#AppointmentEditForm").validationEngine();
        $("#AppointmentEditForm").ajaxForm({
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
        $('#AppointmentAppDate').datepicker(
        {
            changeMonth: true,
            changeYear: true,
            showSecond: false,
            dateFormat:'dd/mm/yy'            
        });
        $("#patientDoctorId").val(<?php echo $this->data["Appointment"]["doctor_id"] ?>);
        $(".btnBackPatientAppointment").dblclick(function(event){
            event.preventDefault();
            $('#AppointmentEditForm').validationEngine('hideAll');
            oCache.iCacheLower = -1;
            oTablePatientAppointment.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });
    function checkBfSavePatient(){
        var formName = "#AppointmentEditForm";
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
<?php echo $this->Form->create('Appointment');?>
<?php echo $this->Form->input('id'); ?>
<fieldset style="padding: 5px;border: 1px dashed #3C69AD;">
<legend style="background: #EDEDED; font-weight: bold;"><?php __(MENU_APPOINTMENT_MANAGEMENT); ?></legend>
<table>
    <tr>            
        <td><label for="patient_name"><?php echo PATIENT_NAME; ?> <span class="red"> *</span> :</label></td>
        <td>           
            <?php 
            $query = mysql_query("SELECT patient_name FROM patients WHERE id = {$this->data['Appointment']['patient_id']}");
            $result = mysql_fetch_array($query);
            echo $this->Form->text('patient_name', array('value' => $result['patient_name'], 'class' => 'validate[required]', 'disabled' => 'disabled')); 
            ?>            
        </td>
    </tr>
    <tr>
        <td><label for="AppointmentAppDate"><?php echo APPOINTMENT_DATE; ?> <span class="red">*</span> :</label></td>
        <td><?php echo $this->Form->text('app_date', array('class'=>'validate[required]', 'value' => date('d/m/Y', strtotime($this->data['Appointment']['app_date'])))); ?></td>
    </tr>
    <tr>
        <td><label for="patientDoctorId"><?php echo DOCTOR_DOCTOR; ?><span class="red">*</span>  :</label></td>
        <td>
            <select id="patientDoctorId" name="data[Appointment][doctor_id]" class='validate[required]'>
                <option value=""><?php echo SELECT_OPTION;?></option>
                <?php 
                foreach ($doctors as $doctor) {
                    if($doctor['User']['id'] == $this->data['Appointment']['doctor_id']){
                        echo '<option selected="selected" value="'.$doctor['User']['id'].'">'.$doctor['Employee']['name'].'</option>';
                    }else{
                        echo '<option value="'.$doctor['User']['id'].'">'.$doctor['Employee']['name'].'</option>';
                    }
                    
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