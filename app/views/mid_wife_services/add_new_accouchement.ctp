<?php
echo $this->element('prevent_multiple_submit');
$absolute_url = FULL_BASE_URL . Router::url("/", false);
echo $javascript->link('uninums.min');
include("includes/function.php");
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
        $("#MidWifeServiceAddNewAccouchementForm").validationEngine();
        $("#MidWifeServiceAddNewAccouchementForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSave").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {                
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif"); 
                $(".btnBackAddNewDossierMedicalAccouchement").click();               
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
        
        
        $(".btnBackAddNewDossierMedicalAccouchement").click(function(event) {
            event.preventDefault();
            event.preventDefault();
            var queueId=$(this).attr('queueId');
            $('#dataDossierMedical').html("<?php echo ACTION_LOADING; ?>");
            $('#dataDossierMedical').load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/addMidWifeServiceDoctorDossierMedical/"+queueId);
        });
        $("#MidWifeServiceTime1").timepicker({
            step: 15
        }).unbind('blur');
        $("#MidWifeServiceDob" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
            yearRange: '-100:-0'
        }).unbind("blur");
    });
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="#" class="positive btnBackAddNewDossierMedicalAccouchement" rel="<?php echo $this->params['pass'][0];?>" queueId="<?php echo $patient['Queue']['id'];?>">
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
                echo getAgePatient($patient['Patient']['dob']);               
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
    <legend><?php echo 'ការសំរាលកូន(Accouchement)'; ?></legend>                                    
    <div class="accordionDetail">  
        <div>
            <table class="defualtTable" style="width:100%;">
                <tr​​>
                    <td style="width:15%;"><span style="color:red">* </span>ការសំរាលកូន</td>
                </tr>
                <tr>                                    
                    <td></td>
                    <td style="width:15%;"><?php echo 'សំរាលធម្មតា'; ?></td>
                    <td><input type="checkbox" value="1" name="data[MidWifeService][acconchem_rerme]"/></td>
                </tr>
                <tr>
                    <td></td>
                    <td><?php echo 'កូនកើតមិនគ្រប់ខែ'; ?></td>
                    <td><input type="checkbox" value="1" name="data[MidWifeService][accon_chement]"/></td>
                </tr>
                <tr>
                    <td></td>
                    <td> <?php echo 'សំរាលកូនពិបាក'; ?></td>
                    <td><input type="checkbox" value="1" name="data[MidWifeService][anormat]"/></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td> <?php echo 'បូម / នាក់'; ?></td>
                    <td><input type="checkbox" value="1" name="data[MidWifeService][acc_par_ventonse]"/></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td> <?php echo 'វះកាត់ / នាក់ ';?></td>  
                    <td><input type="checkbox" value="1" name="data[MidWifeService][caesarean]"/></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td><?php echo $this->Form->input('g', array('tabindex' => '1', 'label' => 'Vg', 'style' => 'width: 94%;')); ?></td>
                </tr>
                <tr>
                    <td class="test">
                        <div><label for="MidWifeServiceTime1"><?php echo 'កើតនៅម៉ោង'; ?></label></div>
                    </td>
                    <td>
                        <div class="input select"><label for="Dob"><?php echo 'ថ្ងៃ ខែ ឆ្នាំ កំណើត'; ?></label></div>                                   
                    </td>
                </tr>
                <tr>
                    <td class="test">
                        <?php echo $this->Form->text('time1', array('tabindex' => '4', 'style' => 'width: 50%;')); ?>
                        <img alt="" id="btnEmptyTime1" src="<?php echo $this->webroot; ?>img/close.gif" style="cursor: pointer;" align="middle" />
                    </td>
                    <td>
                        <?php echo $this->Form->text('dob', array('tabindex' => '4', 'style' => 'width: 57%;')); ?>                                       
                    </td>
                </tr>
                <tr>
                    <td>Girl <input type="checkbox" name="data[MidWifeService][girl]" value="1"/></td>
                    <td>Boy <input type="checkbox" name="data[MidWifeService][boy]" value="1"/></td>
                    <td>
                        <?php echo 'ទំងន់' .' / Kg'. '<span class="red">*</span>'; ?>
                    </td>                                    
                    <td>
                        <?php echo 'ប្រវែង' .' / Cm'. '<span class="red">*</span>'; ?>
                    </td>                                                                                                                                                            
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td style="padding-right:20px;"><?php echo $this->Form->input('weight', array('tabindex' => '1', 'label' => FALSE, 'class' => 'validate[required]','style'=>'width:94%;')); ?></td>
                    <td>
                        <?php echo $this->Form->input('long', array('tabindex' => '1', 'label' => FALSE, 'class' => 'validate[required]','style'=>'width:54%;')); ?>
                    </td> 
                </tr>
                <tr>
                    <td>
                        <?php echo $this->Form->input('head_size', array('tabindex' => '1', 'label' => 'បរិមាត្រក្បាល' .' / Cm'. '<span class="red">*</span>', 'class' => 'validate[required]' ,'style'=>'width:53%;')); ?>
                    </td>
                </tr>
                <tr>
                    <td>Score d'Apgar</td>                                                            
                    <td colspan="3">
                        <table border="1" class="new_table" style="width:50%;">
                            <tr align="center">
                                <td width="25px" height="25px">1នាទី</td>
                                <td width="25px" height="25px">5នាទី</td>
                                <td width="25px" height="25px">10នាទី</td>
                            </tr>
                            <tr>
                                <td width="15px" height="25px"><input type="text" name="data[MidWifeService][one_minute]" style="width:92%;"/></td>
                                <td><input type="text" name="data[MidWifeService][five_minute]" style="width:92%;"/></td>
                                <td><input type="text" name="data[MidWifeService][ten_minute]" style="width:92%;"/></td>
                            </tr>
                        </table>
                    </td>
                </tr>                                                                                                                                                                   
                <tr>
                    <td><span style="color:red">* </span>Other</td>
                </tr>
                <tr>
                    <td></td>
                    <td>ស្បូនកន្រ្តាក់</td>
                    <td width="100px">Good<input type="checkbox" name="data[MidWifeService][good]" value="1"/></td>
                    <td>Not Good<input type="checkbox" name="data[MidWifeService][not_good]" value="1"/></td>
                </tr>
                <tr>
                    <td></td>
                    <td>បរិមាណឈាម</td>
                    <td>Little<input type="checkbox" name="data[MidWifeService][title]" value="1"/></td>
                    <td>Much<input type="checkbox" name="data[MidWifeService][much]" value="1"/></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td colspan="1" style="vertical-align: middle;text-align: center;">(<300ml),(>300ml)</td>                                                            
                    <td><input type="hidden" name="data[MidWifeService][mid_wife_dossier_medical_id]" value="<?php echo $patient['MidWifeDossierMedical']['id']; ?>" style="width:94%;"/></td>  
                </tr>                                                                
            </table>
        </div>                                        
    </div>                                                                    
</fieldset>
<div class="clear"></div>
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
        <span class="txtSave"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<?php echo $this->Form->end(); ?>

