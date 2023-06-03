<script type="text/javascript">
    $(document).ready(function(){
         preventKeyEnter();
        $("#PatientIpdPatientLeaveForm").validationEngine('attach', {
            isOverflown: true,
            overflownDIV: ".ui-tabs-panel"
        });
        $("#PatientIpdPatientLeaveForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSave").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            beforeSerialize: function($form, options) {                
                $("#PatientIpdAppDate").datetimepicker("option", "dateFormat", "yy-mm-dd", "timeFormat", "hh:mm");                
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $(".btnBackPatientLeave").click();
                // alert message
                if(result != '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>' && result != '<?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?>'){
                    createSysAct('PatientIpd', 'patientLeave', 2, result);
                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                }else {
                    createSysAct('PatientIpd', 'patientLeave', 1, '');
                    // alert message
                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result+'</p>');
                }
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
        $("#PatientIpdEndDate").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd'
        }).unbind('blur');
        
        $('#PatientIpdAppDate').datetimepicker({
            changeMonth: true,
            changeYear: true,
            timeFormat: "HH:mm:ss" ,
            showSecond: false,
            dateFormat:'dd/mm/yy'            
        });
        
        $(".btnBackPatientLeave").click(function(event){
            event.preventDefault();
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide( "slide", { direction: "right" }, 500, function() {
                oCache.iCacheLower = -1;
                oTablePatientIPD.fnDraw(false);
                leftPanel.show();
                rightPanel.html('');
            });
        });
    });
</script>
<style>
    th,
    td {
        padding: 5px;
        line-height: 20px;
    }
</style>
<?php include('includes/function.php'); ?>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="" class="positive btnBackPatientLeave">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt="" />
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('PatientIpd'); ?>
<?php echo $this->Form->input('id', array('type' => 'hidden')); ?>
<!-- <fieldset style="width: 48%; float: left;"> -->
<fieldset>
    <legend><?php __(TABLE_PATIENT_DISCHARGE); ?></legend>
    <table style="width: 100%">
        <tr>
            <th style="width: 15%;"><label for="patient_name"><?php echo PATIENT_NAME; ?> :</label></th>
            <td style="width: 35%;">
                <?php echo $this->Form->hidden('patient_ipd_room_id', array('id' => 'patient_ipd_room_id', 'value' => $this->data['PatientIpdRoom']['id'], 'readonly' => true)); ?>
                <?php echo $this->Form->hidden('patient_id', array('id' => 'patient_id', 'value' => $this->data['Patient']['id'], 'readonly' => true)); ?>
                <!-- <?php echo $this->Form->text('patient_name', array('id' => 'patient_name', 'value' => $this->data['Patient']['patient_name'], 'readonly' => true, 'style' => 'width:150px')); ?> -->
                <?php echo $this->data['Patient']['patient_name']; ?>
            </td>
            <th style="width: 15%;"><label for="patient_code"><?php echo PATIENT_CODE; ?> :</label></th>
            <td style="width: 35%;">
                <?php echo $this->data['Patient']['patient_code']; ?>
            </td>
        </tr>
        <tr>
            <th><label for="patient_sex"><?php echo TABLE_SEX; ?> :</label></th>
            <td>
                <?php
                if ($this->data['Patient']['sex'] == "F") {
                    echo GENERAL_FEMALE;
                } else {
                    echo GENERAL_MALE;
                }
                ?>
            </td>
            <th><label for="patient_nationality"><?php echo TABLE_NATIONALITY; ?> <span class="red"></span> :</label></th>
            <td>
                <?php
                if ($this->data['Patient']['patient_group_id'] != "") {
                    $query = mysql_query("SELECT name FROM patient_groups WHERE id=" . $this->data['Patient']['patient_group_id']);
                    while ($row = mysql_fetch_array($query)) {
                        if ($this->data['Patient']['patient_group_id'] == 1) {
                            echo $row['name'];
                        } else {
                            echo $row['name'] . '&nbsp;&nbsp;(' . $this->data['Nationality']['name'] . ')';
                        }
                    }
                } else {
                    echo $this->data['Nationality']['name'];
                }
                ?>
            </td>
        </tr>
        <tr>
            <th><label for="patient_dob"><?php echo TABLE_AGE; ?> :</label></th>
            <td>
                <?php echo getAgePatient($this->data['Patient']['dob']) ?>
            </td>
            <th><label for="patient_address"><?php echo TABLE_ADDRESS; ?> :</label></th>
            <td>
                <?php echo $this->data['Patient']['address']; ?>
            </td>
        </tr>
        <tr>
            <!-- <th><label for="PatientIpdRoom"><?php echo MENU_ROOM_MANAGEMENT . ' ' . TABLE_NO; ?> :</label></th> -->
            <th><label for="patient_occupation"><?php echo TABLE_PATIENT_OCCUPATION; ?> :</label></th>
            <td colspan="3">
                <?php echo $this->data['Patient']['occupation']; ?>
                <!-- Room  -->
                <?php
                foreach ($rooms as $room) {
                    if ($this->data['Room']['id'] == $room['Room']['id']) { ?>
                        <?php echo $this->Form->hidden('room_id', array('id' => 'room_id', 'value' => $this->data['Room']['id'], 'readonly' => true)); ?>
                        <?php echo $this->Form->hidden('room_name', array('id' => 'room_name', 'value' => $room['Room']['room_name'], 'readonly' => true, 'style' => 'width:150px')); ?>

                <?php }
                } ?>
                <!--  -->
            </td>
        </tr>
        <tr>
            <th><label for="father_name"><?php echo TABLE_FATHER_NAME; ?> :</label></th>
            <td>
                <?php echo $this->data['Patient']['father_name']; ?>
            </td>
            <th><label for="father_occupation"><?php echo TABLE_FATHER_OCCUPATION; ?> :</label></th>
            <td>
                <?php echo $this->data['Patient']['father_occupation']; ?>
            </td>
        </tr>
        <tr>
            <th><label for="mother_name"><?php echo TABLE_MOTHER_NAME; ?> :</label></th>
            <td>
                <?php echo $this->data['Patient']['mother_name']; ?>
            </td>
            <th><label for="mother_occupation"><?php echo TABLE_MOTHER_OCCUPATION; ?> :</label></th>
            <td>
                <?php echo $this->data['Patient']['mother_occupation']; ?>
            </td>

        </tr>
        <tr>
            <th><label for="date_ipd"><?php echo TABLE_CHECK_IN; ?> :</label></th>
            <td>
                <?php echo dateTimeConvert($this->data['PatientIpd']['date_ipd']); ?>
            </td>
        </tr>
        <tr>
            <th><label for="PatientIpdEndDate"><?php echo TABLE_ROOM_DATE_OUT; ?> <span class="red">*</span> :</label></th>
            <td>
                <?php echo $this->Form->text('end_date', array('class' => 'validate[required]', 'style' => 'width:190px')); ?>
            </td>
        </tr>
        <tr>
            <th><label for="outpatient_condition"><?php echo TABLE_OUTPATIENT_CONDITION; ?> :</label> </th>
            <td colspan="3">
                <?php echo $this->Form->textarea('note', array('style' => 'width:400px')); ?>
            </td>
        </tr>
        <tr>
            <th><label for="patient_ipd_diagnotist_after"><?php echo TABLE_DISCHARGE_DIAGNOSIS; ?> :</label></th>
            <td colspan="3"><?php echo $this->Form->textarea('diagnotist_after', array('style' => 'width:400px')); ?></td>
        </tr>
        <tr>
            <th><label for="doctor_advice"><?php echo TABLE_DOCTOR_ADVICE; ?> :</label></th>
            <td colspan="3">
                <?php echo $this->Form->textarea('doctor_nme', array('style' => 'width:400px')); ?>

                <?php echo $this->Form->hidden('app_date', array('readonly' => true)); ?>
                <?php echo $this->Form->hidden('description'); ?>
            </td>
        </tr>

    </table>
</fieldset>
<br />
<div class="clear"></div>
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt="" />
        <span class="txtSave"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<?php echo $this->Form->end(); ?>