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
        $("#MidWifeServiceAddNewDeliveranceForm").validationEngine();
        $("#MidWifeServiceAddNewDeliveranceForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSave").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {                
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif"); 
                $(".btnBackAddNewDossierMedicalDeliverance").click();               
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
        
        
        $(".btnBackAddNewDossierMedicalDeliverance").click(function(event) {
            event.preventDefault();
            event.preventDefault();
            var queueId=$(this).attr('queueId');
            $('#dataDossierMedical').html("<?php echo ACTION_LOADING; ?>");
            $('#dataDossierMedical').load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/addMidWifeServiceDoctorDossierMedical/"+queueId);
        });
        $("#MidWifeServiceTime").timepicker({
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
        <a href="#" class="positive btnBackAddNewDossierMedicalDeliverance" rel="<?php echo $this->params['pass'][0];?>" queueId="<?php echo $patient['Queue']['id'];?>">
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
    <legend><?php echo 'ការទំលាក់សុក(D&egrave;livrance)'; ?></legend>                                    
    <div class="accordionDetail">  
        <div>
            <table class="defualtTable">
                <tr>    
                    <td>
                        <?php echo 'Time'. '<span class="red">*</span>'; ?>
                    </td>
                    <td>
                        <?php echo $this->Form->input('time', array('tabindex' => '1', 'label' => FALSE, 'class' => 'validate[required]')); ?>
                    </td>
                    <td class="empty">
                        <img alt="" id="btnEmptyTime" src="<?php echo $this->webroot; ?>/img/close.gif" style="cursor: pointer;" align="middle" />
                    </td>                                                                               
                </tr>
                <tr>
                    <td><?php echo 'ទំងន់សុក'. '<span class="red">*</span>'; ?></td>
                    <td><?php echo $this->Form->input('weight', array('tabindex' => '1', 'label' => FALSE, 'class' => 'validate[required]')); ?></td>                                    
                    <td class="empty">Kg</td>
                </tr>
                <tr>
                    <td>បែប</td>
                </tr>
                <tr>                                          
                    <td class="test">BEAUDELAUQUE</td>     
                    <td><input type="checkbox" value="1" name="data[MidWifeService][beaudelauque]" /></td>
                </tr>
                <tr>                                    
                    <td class="test">DUNCAN</td>
                    <td><input type="checkbox" value="1" name="data[MidWifeService][duncan]" /></td>
                </tr>

                <tr>
                    <td>ទំលាក់សុកដោយ</td>                                                            
                </tr>
                <tr>                                         
                    <td class="test">ត្រួតពិនិត្យ</td>
                    <td><input type="checkbox" value="1" name="data[MidWifeService][check]" /></td>
                </tr>
                <tr>                                  
                    <td class="test">បែបធម្មជាតិ</td>
                    <td><input type="checkbox" value="1" name="data[MidWifeService][natural]" /></td>
                </tr>
                <tr>                                    
                    <td class="test">បារទំលាក់ដោយដៃ</td>
                    <td><input type="checkbox" value="1" name="data[MidWifeService][by_hand]" /></td>
                </tr>
                <tr>
                    <td>ការបារសំអាតស្បូន</td>
                </tr>
                <tr>                                    
                    <td class="test">មាន</td>
                    <td><input type="checkbox" value="1" name="data[MidWifeService][have]"/></td>
                </tr>
                <tr>
                    <td class="test">គ្មាន</td>
                    <td><input value="1" type="checkbox" name="data[MidWifeService][no_have]"/></td>
                    <td><input type="hidden" name="data[MidWifeService][mid_wife_dossier_medical_id]" value="<?php echo $patient['MidWifeDossierMedical']['id']; ?>" /></td>  
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

