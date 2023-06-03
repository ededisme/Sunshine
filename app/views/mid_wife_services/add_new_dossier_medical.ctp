<?php
echo $this->element('prevent_multiple_submit');
$absolute_url = FULL_BASE_URL . Router::url("/", false);
echo $javascript->link('uninums.min');
include("includes/function.php");
$tblNameRadom = "tbl" . rand();
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
        $("#MidWifeServiceAddNewDossierMedicalForm").validationEngine();
        $("#MidWifeServiceAddNewDossierMedicalForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSave").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {                
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif"); 
                $(".btnBackAddNewDossierMedical").click();               
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
        
        $(".btnBackAddNewDossierMedical").click(function(event) {
            event.preventDefault();
            event.preventDefault();
            var queueId=$(this).attr('queueId');
            $('#dataDossierMedical').html("<?php echo ACTION_LOADING; ?>");
            $('#dataDossierMedical').load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/addMidWifeServiceDoctorDossierMedical/"+queueId);
        });
        
        $("#MidWifeServiceEntreLe,#MidWifeServiceSortieLe" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
            yearRange: '-100:-0'
        }).unbind("blur");
        
        //Hide Patinen Info
        $("#btnHidePatientInfo<?php echo $tblNameRadom;?>").click(function(){
            $("#patientInfo<?php echo $tblNameRadom;?>").hide(900);
            $("#showPatientInfo<?php echo $tblNameRadom;?>").show();
        });
        //Show Patinen Info
        $("#btnShowPatientInfo<?php echo $tblNameRadom;?>").click(function(){
            $("#patientInfo<?php echo $tblNameRadom;?>").show(900);
            $("#showPatientInfo<?php echo $tblNameRadom;?>").hide();
        });
    });
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="#" class="positive btnBackAddNewDossierMedical" rel="<?php echo $this->params['pass'][0];?>" queueId="<?php echo $this->params['pass'][0];?>">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('MidWifeService',array('enctype' => 'multipart/form-data')); ?>
<input name="data[QeuedDoctor][id]" type="hidden" value="<?php echo $patient['QeuedDoctor']['id'];?>"/>
<input name="data[Queue][id]" type="hidden" value="<?php echo $this->params['pass'][0];?>"/>

<legend id="showPatientInfo<?php echo $tblNameRadom;?>" style="display:none;"><a href="#" id="btnShowPatientInfo<?php echo $tblNameRadom;?>" style="background: #CCCCCC; font-weight: bold;"><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?> [ Show ] </a> </legend>
<fieldset id="patientInfo<?php echo $tblNameRadom;?>" style="border: 1px dashed #3C69AD;">
<legend><a href="#" id="btnHidePatientInfo<?php echo $tblNameRadom;?>" style="background: #CCCCCC; font-weight: bold;"> <?php echo MENU_PATIENT_MANAGEMENT_INFO; ?> [ Hide ] </a></legend>
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
<fieldset style="border: 1px dashed #3C69AD;">
    <legend style="background: #EF0931; font-weight: bold;"><?php __(GENERAL_REQUEST); ?></legend>
    <table style="width: 100%;" cellspacing="0">
        <tr>
            <td>
                <?php 
                    $queryDataFromDoctor=  mysql_query("SELECT mwsreq.*,mwsreq.id as id FROM mid_wife_service_requests as mwsreq "
                            . "INNER JOIN other_service_requests as osreq ON osreq.id=mwsreq.other_service_request_id "
                            . "INNER JOIN queued_doctors as qd ON qd.id=osreq.queued_doctor_id "
                            . "INNER JOIN queues as q ON q.id=qd.queue_id WHERE osreq.is_active=1 AND queue_id=".$this->params['pass'][0]);
                    $dataRequest=  mysql_fetch_array($queryDataFromDoctor);
                    echo $dataRequest['mid_wife_description'];
                ?>
                <input type="hidden" value="<?php echo $dataRequest['id']; ?>" name="data[MidWifeServiceRequest][id]">
            </td>
        </tr>
    </table>      
</fieldset>
<br/>
<fieldset style="border: 1px dashed #3C69AD;">
    <legend><?php __(MENU_MID_WIFE_SERVICE_INFO); ?></legend>
    <fieldset>
        <legend><?php echo 'ស្ថានភាពអ្នកជំងឺ'; ?></legend>                            

        <table class="defualtTableNew">                                
            <tr>
                <td><?php echo 'Entr&eacute;e le'. '<span class="red">*</span>'; ?></td>
                <td>
                    <?php echo $this->Form->input('entre_le', array('tabindex' => '1', 'label' => FALSE , 'class' => 'validate[required]')); ?>                                        
                </td>                                    
            </tr>
            <tr>
                <td><?php echo "Diagnostic d'entr&eacute;e"; ?></td>
                <td>
                    <?php echo $this->Form->input('doagnostic_entre', array('tabindex' => '1', 'label' => FALSE)); ?>
                </td>                                    
            </tr> 
            <tr>
                <td><?php echo 'Sotrie le'. '<span class="red">*</span>'; ?></td>
                <td>
                    <?php echo $this->Form->input('sortie_le', array('tabindex' => '1', 'label' => FALSE , 'class' => 'validate[required]')); ?>                                        
                </td>                                    
            </tr>
            <tr>
                <td style="width:10%;"><?php echo "Diagnostic de sortie"; ?></td>
                <td>
                    <?php echo $this->Form->input('doagnostic_sortie', array('tabindex' => '1', 'label' => FALSE)); ?>
                </td>                                    
            </tr>   
            <tr>
                <td>PARA</td>
            </tr>
            <tr>
                <td colspan="2">
                    <table border="0px">
                        <tr>
                            <td width="170px"><input style="width: 290px;" type="text" name="data[MidWifeService][acconchement_rerme]" /></td>
                            <td width="170px"><input style="width: 290px;" type="text" name="data[MidWifeService][accon_chement]" /></td>
                            <td width="170px"><input style="width: 290px;" type="text" name="data[MidWifeService][avortment_inv]" /></td>
                            <td width="170px"><input style="width: 290px;" type="text" name="data[MidWifeService][baby]" /></td>
                            <td class="Hidden"></td>
                        </tr>                                                
                    </table>
                </td>
            </tr>
            <tr>
                <td>ATCD</td>                                        
            </tr>
            <tr>                                       
                <td class="test">+ C&eacute;sarienne...</td>   
                <td><input align="left" type="checkbox" value="1"  name="data[MidWifeService][caesarean]" /></td>
            </tr>
            <tr>                                
                <td class="test">+ H&eacute;morragie...</td>
                <td><input type="checkbox" value="1" name="data[MidWifeService][hemorrhage]" /></td>
            </tr>                                    
            <tr>                                        
                <td class="test">+ Hypertension...</td>
                <td><input type="checkbox" value="1" name="data[MidWifeService][hypertension]" /></td>
            </tr>
            <tr>                                
                <td class="test">+ Cardiopathie...</td>
                <td><input type="checkbox" value="1" name="data[MidWifeService][heart]" /></td>
            </tr>  
            <tr>
                <td><?php echo "Other"; ?></td>
                <td>
                    <?php echo $this->Form->input('other', array('tabindex' => '1', 'label' => FALSE)); ?>                                        
                </td>
            </tr>
        </table>                                   
    </fieldset>
    <br>
    <fieldset>
        <legend><?php echo "Examen &agrave; l'entr&eacute;e"; ?></legend>
        <table class="defualtTableNew"> 
            <tr>
                <td><?php echo "TA / CmHg"; ?></td>
                <td>
                    <?php echo $this->Form->input('ta', array('tabindex' => '1', 'label' => FALSE, 'style' => 'width: 400px;')); ?>                                        
                </td>
                <td><?php echo "P / mn"; ?></td>
                <td>
                    <?php echo $this->Form->input('p', array('tabindex' => '1', 'label' => FALSE, 'style' => 'width: 290px;')); ?>                                        
                </td>
                <td><?php echo "T&#730 / &#8451"; ?></td>
                <td>
                    <?php echo $this->Form->input('t', array('tabindex' => '1', 'label' => FALSE, 'style' => 'width: 290px;')); ?>                                        
                </td>
            </tr>
            <tr>
                <td><?php echo "Pr&eacute;sentation"; ?></td>
                <td>
                    <?php echo $this->Form->input('presentation', array('tabindex' => '1', 'label' => FALSE)); ?>                                        
                </td>
            </tr>
            <tr>
                <td><?php echo "H.U / Cm"; ?></td>
                <td>
                    <?php echo $this->Form->input('hu', array('tabindex' => '1', 'label' => FALSE)); ?>                                        
                </td>
            </tr>
            <tr>
                <td><?php echo "BCF / mn"; ?></td>
                <td>
                    <?php echo $this->Form->input('bcf', array('tabindex' => '1', 'label' => FALSE)); ?>                                        
                </td>
            </tr>
            <tr>
                <td><?php echo "Col / Cm"; ?></td>
                <td>
                    <?php echo $this->Form->input('col', array('tabindex' => '1', 'label' => FALSE)); ?>                                        
                </td>
            </tr>
            <tr>
                <td><?php echo "PDE"; ?></td>
                <td>
                    <?php echo $this->Form->input('pde', array('tabindex' => '1', 'label' => FALSE)); ?>                                        
                </td>
            </tr>
            <tr>
                <td><?php echo "OEd&egrave;me (+)or(-)"; ?></td>
                <td>
                    <?php echo $this->Form->input('edema', array('tabindex' => '1', 'label' => FALSE)); ?>                                        
                </td>
            </tr>
        </table>
    </fieldset>
</fieldset>
<div class="clear"></div>
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <span class="txtSave"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<?php echo $this->Form->end(); ?>

