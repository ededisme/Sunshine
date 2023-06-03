<?php
echo $this->element('prevent_multiple_submit');
$absolute_url = FULL_BASE_URL . Router::url("/", false);
echo $javascript->link('uninums.min');
?>
<style type="text/css">
    .input{
        float:left;
    }           
</style>
<script type="text/javascript">
    $(document).ready(function(){ 
        // Prevent Key Enter
        preventKeyEnter();
        $("#MidWifeServiceCheckUpPatientForm").validationEngine();
        $("#MidWifeServiceCheckUpPatientForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSave").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {                
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif"); 
                $(".btnBackQueueMidWifeServiceCheck").click();               
                // alert message
                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>' + result + '</p>');
                $("#dialog").dialog({
                    title: '<?php echo DIALOG_INFORMATION; ?>',
                    resizable: false,
                    modal: true,
                    width: 'auto',
                    height: 'auto',
                    open: function(event, ui) {
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
        
        $(".btnBackQueueMidWifeServiceCheck").click(function(event) {
            event.preventDefault();
            var queueId=$(this).attr('queueId');
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
        
        $('#accordion').accordion({
            collapsible: true,
            active: false
        });  
    });
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="" class="positive btnBackQueueMidWifeServiceCheck" rel="<?php echo $this->params['pass'][0];?>" queueId="<?php echo $this->params['pass'][1];?>">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('MidWifeService',array('enctype' => 'multipart/form-data')); ?>
<input name="data[Queue][id]" type="hidden" value="<?php echo $patient['Queue']['id'];?>"/>
<fieldset>
    <legend><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?></legend>
    <table style="width: 100%;" cellspacing="3">
        <tr>
            <td style="width: 15%;"><?php echo PATIENT_CODE; ?> :</td>
            <td style="width: 35%;"><?php echo $patient['Patient']['patient_code']; ?></td>
            <td style="width: 15%;">
                <?php echo TABLE_DOB; ?> :</td>
            <td style="width: 35%;">
                <?php echo date("d/m/Y", strtotime($patient['Patient']['dob'])); ?>
                <?php echo TABLE_AGE; ?> :
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
        </tr>
        <tr>
            <td style="width: 15%;"><?php echo PATIENT_NAME; ?> :</td>
            <td>
                <?php echo $patient['Patient']['patient_name']; ?>
            </td>
            <td style="width: 15%;"><?php echo TABLE_NATIONALITY; ?> :</td>
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
        </tr>      
        <tr>
            <td style="width: 15%;"><?php echo TABLE_SEX; ?> :</td>
            <td>
                <?php
                if ($patient['Patient']['sex'] == "F") {
                    echo GENERAL_FEMALE;
                } else {
                    echo GENERAL_MALE;
                }
                ?>
            </td>
            <td style="width: 15%;"><?php echo TABLE_EMAIL; ?> :</td>
            <td>
                <?php echo $patient['Patient']['email']; ?>
            </td>            
        </tr>
        <tr>            
            <td style="width: 15%;"><?php echo TABLE_OCCUPATION; ?> :</td>
            <td>
                <?php echo $patient['Patient']['occupation']; ?>
            </td>
            <td style="width: 15%;"><?php echo TABLE_TELEPHONE; ?>:</td>
            <td>
                <?php echo $patient['Patient']['telephone']; ?>
            </td>
        </tr>        
        <tr>
            <td style="width: 15%;"><?php echo TABLE_ADDRESS; ?> :</td>
            <td>
                <?php echo $patient['Patient']['address']; ?>
            </td>
            <td style="width: 15%;"><?php echo TABLE_CITY_PROVINCE; ?> :</td>
            <td>
                <?php                
                if($patient['Patient']['location_id']!=""){
                    $query = mysql_query("SELECT name FROM patient_locations WHERE id=" . $patient['Patient']['location_id']);
                    while ($row = mysql_fetch_array($query)) {
                        echo $row['name'];
                    }
                }
                ?>
            </td>
        </tr>
    </table>     
</fieldset>
<br/>
<fieldset>
    <legend><?php __(MENU_CONSULT_PATIENTS); ?></legend>
    <table class="defualtTable">
        <tr>
            <td><?php echo TABLE_WEIGHT; ?> / Kg<span class="red">*</span></td>
            <td>
                <?php echo $this->Form->input('weight', array('tabindex' => '1', 'label' => FALSE , 'class' => 'validate[required]')); ?> 
            </td>
            <td><?php echo TABLE_HEIGHT; ?> / Cm<span class="red">*</span></td>
            <td>
                <?php echo $this->Form->input('height', array('tabindex' => '1', 'label' => FALSE, 'class' => 'validate[required]')); ?> 
            </td>                           
        </tr>
        <tr>
            <td><?php echo TABLE_BLOOD_PRESSURE; ?></td>
            <td>
                <?php echo $this->Form->input('blood_pressure', array('tabindex' => '1', 'label' => FALSE)); ?>
            </td>
        </tr>
        <tr>
            <td><?php echo TABLE_PULE; ?></td>
            <td>
                <?php echo $this->Form->input('pulse', array('tabindex' => '1', 'label' => FALSE)); ?>
            </td>
            <td><?php echo TABLE_TEMPERATURE; ?> / &#8451</td>
            <td>
                <?php echo $this->Form->input('temperature', array('tabindex' => '1', 'label' => FALSE)); ?> 
            </td>
        </tr>	
        <tr>
            <td><?php echo TABLE_PRESENTATION; ?></td>
            <td>
                <?php echo $this->Form->input('presentation', array('tabindex' => '1', 'label' => FALSE)); ?>
            </td>
        </tr>
        <tr>
            <td><?php echo TABLE_UTERUS_HEIGHT; ?> / Cms</td>
            <td>
                <?php echo $this->Form->input('uterus_height', array('tabindex' => '1', 'label' => FALSE)); ?> 
            </td>
        </tr>
        <tr>
            <td><?php echo TABLE_BABY_HEART_RATE; ?></td>
            <td>
                <?php echo $this->Form->input('baby_heart_rate', array('tabindex' => '1', 'label' =>FALSE )); ?>
            </td>
        </tr>
        <tr>
            <td><?php echo TABLE_IRON; ?></td>
            <td>
                <?php echo $this->Form->input('iron', array('tabindex' => '1', 'label' => FALSE)); ?>
            </td>
        </tr>
        <tr>
            <td><?php __(PATIENT_MADIE_DES_REINS); ?></td>
            <td class="test">
                <input id="edema" width="15px" type="checkbox" name="data[MidWifeService][edema]" value="1"/>
                <label for="edema"><?php __(TABLE_EDEMA); ?></label>
            </td> 
        </tr>        
        <tr>
            <td></td>
            <td class="test">
                <input id="albuminuria" width="15px" type="checkbox" name="data[MidWifeService][albuminuria]" value="1"/>
                <label for="albuminuria"><?php __(TABLE_ALBUMINURIA); ?></label>
            </td>            
        </tr>                          
        <tr>
            <td></td>
            <td class="test">
                <input id="asthma" width="15px" type="checkbox" name="data[MidWifeService][asthma]" value="1"/>
                <label for="asthma"><?php __(TABLE_ASTHMA); ?></label>
            </td>            
        </tr>
        <tr>
            <td><?php echo TABLE_OTHER; ?></td>
            <td><?php echo $this->Form->input('other', array('tabindex' => '1', 'label' => FALSE)); ?></td>   
        </tr>
        <tr>
            <td><?php echo TABLE_NEXT_APPOINTMENT; ?><span class="red">*</span></td>
            <td colspan="2">
                <?php echo $this->Form->input('next_appointment', array('tabindex' => '1', 'label' => FALSE , 'class' => 'validate[required]')); ?>
            </td>
        </tr>
        <tr>  
            <td colspan="2"><input type="hidden" name="data[MidWifeService][id]" value="<?php echo $patient['MidWifeService']['id'];?>" /></td>  
        </tr>  
    </table> 
</fieldset>
<div class="clear"></div>
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
        <span class="txtSave"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<?php echo $this->Form->end(); ?>

