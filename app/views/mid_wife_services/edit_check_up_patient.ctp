<?php
echo $this->element('prevent_multiple_submit');
$absolute_url = FULL_BASE_URL . Router::url("/", false);
echo $javascript->link('uninums.min');
?>
<script type="text/javascript">
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        $("#MidWifeServiceEditCheckUpPatientForm").validationEngine();
        $("#MidWifeServiceEditCheckUpPatientForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveMidWifeService").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $(".btnBackMidWifeServiceEdit").click();
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
        
        $(".btnBackMidWifeServiceEdit").click(function(event){
            event.preventDefault();
            var queueId=$('#queueId').val();
            var id = $(this).attr('rel');
            $('.dataEdit').html("<?php echo ACTION_LOADING; ?>");
            $('.dataEdit').load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/addMidWifeServiceDoctor/"+queueId);
        });
        
        $("#MidWifeServiceNextAppointment" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
            yearRange: '-100:-0'
        }).unbind("blur");
    });
    // end document
    
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="#" class="positive btnBackMidWifeServiceEdit" rel="<?php echo $this->params['pass'][0];?>">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('MidWifeService',array('enctype' => 'multipart/form-data')); ?>
<?php
foreach ($patient as $patient):  ?>
<fieldset>
    <legend><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?></legend>
    <div>
        <table class="info" style="width: 100%;">
            <tr>
                <th><?php echo PATIENT_CODE; ?></th>
                <td><?php echo $patient['Patient']['patient_code']; ?></td>
                <th><?php echo PATIENT_NAME; ?></th>
                <td><?php echo $patient['Patient']['patient_name']; ?></td>  
                <th><?php echo TABLE_AGE.'/'.TABLE_DOB;?> </th>
                <td>
                    <?php 
                    $then_ts = strtotime($patient['Patient']['dob']);
                    $then_year = date('Y', $then_ts);
                    $age = date('Y') - $then_year;
                    if (strtotime('+' . $age . ' years', $then_ts) > time())
                        $age--;

                    if ($age == 0) {
                        $then_year = date('m', $then_ts);
                        $month = date('m') - $then_year;
                        if (strtotime('+' . $month . ' month', $then_ts) > time())
                            $month--;
                        echo $month . ' ' . GENERAL_MONTH;
                    }else {
                        echo $age . ' ' . GENERAL_YEAR_OLD;
                    }
                    ?>
                </td>
                <th><?php echo TABLE_SEX; ?></th>
                <td>
                    <?php 
                        if($patient['Patient']['sex']=="M"){
                            echo 'Male';
                        }else{
                            echo 'Female';
                        }                        
                    ?>
                </td>
            </tr>
            <tr>
                <th><?php echo TABLE_NATIONALITY;?> </th>
                <td>
                    <?php
                    if ($patient['Patient']['patient_group_id'] != "") {
                        $query = mysql_query("SELECT name FROM patient_groups WHERE id=" . $patient['Patient']['patient_group_id']);
                        while ($row = mysql_fetch_array($query)) {
                            if ($patient['Patient']['patient_group_id'] == 1) {
                                echo $row['name'];
                            } else {
                                $queryNationality = mysql_query("SELECT name FROM nationalities WHERE id=".$patient['Patient']['nationality']);
                                while ($result = mysql_fetch_array($queryNationality)) {
                                    echo $row['name'] . '&nbsp;&nbsp;(' . $result['name'] . ')';
                                }
                            }
                        }
                    } else {
                        echo $patient['Nationality']['name'];
                    }
                    ?>
                </td>
                <th><?php echo TABLE_TELEPHONE;?> </th>
                <td><?php echo $patient['Patient']['telephone']; ?></td>
            </tr>
            <tr>
                <th><?php echo TABLE_ADDRESS;?> </th>
                <td colspan="5">
                    <?php 
                    if($patient['Patient']['address']!=""){
                        echo $patient['Patient']['address'];
                    }
                    if($patient['Patient']['location_id']!=""){
                        $query = mysql_query("SELECT name FROM patient_locations WHERE id=".$patient['Patient']['location_id']);
                        while ($row = mysql_fetch_array($query)) {
                            if($patient['Patient']['address']!=""){
                                echo ', ';
                            }
                            echo $row['name'];                
                        }
                    }
                    ?>
                </td>
                <input type="hidden" value="<?php echo $patient['Queue']['id']; ?>" name="data[Queue][id]" id="queueId">
                <input type="hidden" value="<?php echo $patient['MidWifeService']['id']; ?>" name="data[MidWifeService][id]">
                <input type="hidden" name="data[MidWifeCheckUpPatient][check_up_patient_id]" value="<?php echo $patient['MidWifeCheckUpPatient']['id']?>" />
            </tr>
        </table>
    </div>
</fieldset><br> 
<fieldset>
    <legend><?php __(MENU_MID_WIFE_SERVICE_INFO); ?></legend>
    <fieldset>
       <legend><?php __(MENU_CONSULT_PATIENTS); ?></legend>
       <table class="defualtTable">
           <tr>
               <td><?php echo TABLE_WEIGHT . '<span class="red">*</span>'; ?></td>
               <td>
                   <?php echo $this->Form->input('weight', array('tabindex' => '1', 'label' => FALSE, 'class' => 'validate[required]', 'value' => $patient['MidWifeCheckUpPatient']['weight'])); ?>
               </td>
               <td><?php echo TABLE_HEIGHT . '<span class="red">*</span>'; ?></td>
               <td>
                   <?php echo $this->Form->input('height', array('tabindex' => '1', 'label' => FALSE, 'class' => 'validate[required]', 'value' => $patient['MidWifeCheckUpPatient']['height'])); ?>
               </td>                           
           </tr>
           <tr>
               <td><?php echo TABLE_BLOOD_PRESSURE; ?></td>
               <td>
                   <?php echo $this->Form->input('blood_pressure', array('tabindex' => '1', 'label' => FALSE, 'value' => $patient['MidWifeCheckUpPatient']['blood_pressure'])); ?>
               </td>
           </tr>
           <tr>
               <td><?php echo TABLE_PULE; ?></td>
               <td>
                   <?php echo $this->Form->input('pulse', array('tabindex' => '1', 'label' => FALSE, 'value' => $patient['MidWifeCheckUpPatient']['pulse'])); ?>
               </td>
               <td><?php echo TABLE_TEMPERATURE; ?></td>
               <td>
                   <?php echo $this->Form->input('temperature', array('tabindex' => '1', 'label' => FALSE, 'value' => $patient['MidWifeCheckUpPatient']['temperature'])); ?>
               </td>
           </tr>	
           <tr>
               <td><?php echo TABLE_PRESENTATION; ?></td>
               <td>
                   <?php echo $this->Form->input('presentation', array('tabindex' => '1', 'label' => FALSE, 'value' => $patient['MidWifeCheckUpPatient']['presentation'])); ?>
               </td>
           </tr>
           <tr>
               <td><?php echo TABLE_UTERUS_HEIGHT; ?></td>
               <td>
                   <?php echo $this->Form->input('uterus_height', array('tabindex' => '1', 'label' => FALSE, 'value' => $patient['MidWifeCheckUpPatient']['uterus_height'])); ?>
               </td>
           </tr>
           <tr>
               <td><?php echo TABLE_BABY_HEART_RATE; ?></td>
               <td>
                   <?php echo $this->Form->input('baby_heart_rate', array('tabindex' => '1', 'label' => FALSE, 'value' => $patient['MidWifeCheckUpPatient']['baby_heart_rate'])); ?>
               </td>
           </tr>
           <tr>
               <td><?php echo TABLE_IRON; ?></td>
               <td>
                   <?php echo $this->Form->input('iron', array('tabindex' => '1', 'label' => FALSE, 'value' => $patient['MidWifeCheckUpPatient']['iron'])); ?>
               </td>
           </tr>
           <tr>
               <td><?php __(PATIENT_MADIE_DES_REINS); ?></td>
               <td>
                    <?php if ($patient['MidWifeCheckUpPatient']['edema'] == "1") { ?>
                        <input id="edema" type="checkbox" checked="checked"​ name="data[MidWifeService][edema]" value="1" />
                    <?php } else { ?>
                        <input id="edema" type="checkbox" name="data[MidWifeService][edema]" value="1" />
                    <?php } ?>
                   <label for="edema"><?php __(TABLE_EDEMA); ?> </label>                   
               </td>               
            </tr> 
            <tr>
                <td></td>
                <td>
                    <?php if ($patient['MidWifeCheckUpPatient']['albuminuria'] == "1") { ?>
                        <input id="albuminuria" type="checkbox" checked="checked"​ name="data[MidWifeService][albuminuria]" value="1" />
                    <?php } else { ?>
                        <input id="albuminuria" type="checkbox" name="data[MidWifeService][albuminuria]" value="1" />
                    <?php } ?>
                    <label for="albuminuria"><?php __(TABLE_ALBUMINURIA); ?> </label>                   
                    
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <?php if ($patient['MidWifeCheckUpPatient']['asthma'] == "1") { ?>
                        <input id="asthma" type="checkbox" checked="checked"​ name="data[MidWifeService][asthma]" value="1" />
                    <?php } else { ?>
                        <input id="asthma" type="checkbox" name="data[MidWifeService][asthma]" value="1" />
                    <?php } ?>
                    <label for="asthma"><?php __(TABLE_ASTHMA); ?> </label>                                          
                </td>
            </tr>
            <tr>
                <td><?php echo TABLE_OTHER; ?></td>
                <td>
                    <?php echo $this->Form->input('other', array('tabindex' => '1', 'label' => FALSE, 'value' => $patient['MidWifeCheckUpPatient']['other'])); ?>
                </td>
            </tr>
            <tr>
                <td><?php echo TABLE_NEXT_APPOINTMENT; ?></td>
               <td>
                   <?php echo $this->Form->input('next_appointment', array('tabindex' => '1', 'label' => FALSE . '<span class="red">*</span>', 'class' => 'validate[required]', 'value' => $patient['MidWifeCheckUpPatient']['next_appointment'])); ?>
               </td>
            </tr>
       </table>
   </fieldset>
</fieldset>
<br />
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
        <span class="txtSaveMidWifeService"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<div style="clear: both;"></div>
<?php endforeach; ?>
<?php echo $this->Form->end(); ?>