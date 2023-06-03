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
        $("#MidWifeServiceAddNewMidWifeForm").validationEngine();
        $("#MidWifeServiceAddNewMidWifeForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSave").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {                
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif"); 
                $(".btnBackAddNewMidWifeService").click();               
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
        
        $(".btnBackAddNewMidWifeService").click(function(event) {
            event.preventDefault();
            event.preventDefault();
            var queueId=$(this).attr('queueId');
            $('.dataEdit').html("<?php echo ACTION_LOADING; ?>");
            $('.dataEdit').load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/addMidWifeServiceDoctor/"+queueId);
        });
        
        $("#ment_date,#est_deliv_date,#ech_date" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd'
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
<div style="padding: 5px;border: 1px dashed #3C69AD;">
    <div class="buttons">
        <a href="#" class="positive btnBackAddNewMidWifeService" rel="<?php echo $this->params['pass'][0];?>" queueId="<?php echo $this->params['pass'][0];?>">
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
        <legend><?php __(MENU_STORY_PATIENTS); ?></legend>
        <table style="width: 100%;">
            <tr>
                <td style="width:10%;"><?php echo TABLE_LAST_MENSTRUATION_PERIOD; ?></td>
                <td style="width:3%;">:</td>
                <td style="width:20%;"><input type="text" id="ment_date" name="data[MidWifeService][last_mentstruation_period]" style="width:90%;"></td>
                <td></td>
            </tr>
            <tr>
                <td><?php echo TABLE_ESTIMATE_DELIVERY_DATE; ?></td>
                <td style="width:3%;">:</td>
                <td><input id="est_deliv_date" type="text" name="data[MidWifeService][estimate_delivery_date]" style="width:90%;" ></td>
                <td></td>
            </tr>
            <tr>
                <td><?php echo TABLE_ECHO; ?></td>
                <td>:</td>
                <td><input id="ech_date" type="text" name="data[MidWifeService][echo]" style="width:90%;"></td>
                <td></td>
            </tr>
            <tr>
                <td style="width:10%;"><?php echo TABLE_WEIGHT; ?></td>
                <td>:</td>
                <td style="width:20%;"><input type="text" id="" name="data[MidWifeService][weight]" style="width:90%;"></td>
                <td style="width:10%;"><?php echo TABLE_HEIGHT; ?></td>
                <td style="width:3%;">:</td>
                <td style="width:20%;"><input type="text" id="" name="data[MidWifeService][height]" style="width:90%;"></td>
                <td></td>
            </tr>
            <tr>
                <td><?php echo TABLE_GESTRATION; ?></td>
                <td>:</td>
                <td><input type="text" id="" name="data[MidWifeService][gestation]" style="width:90%;" value="0"></td>
                <td><?php echo TABLE_BABY; ?></td>
                <td>:</td>
                <td><input type="text" id="" name="data[MidWifeService][baby]" style="width:90%;" value="0"></td>
                <td></td>
            </tr>
        </table>
    </fieldset>
    <fieldset>
        <legend><?php __(MENU_PATIENT_STORY_SEE); ?></legend>
        <table style="width: 100%;">
            <tr>
                <td style="width:8%;"><?php __(MENU_PATIENT_STORY_SEE); ?></td>
            </tr>
            <tr>
                <td style="width:8%;"></td>
                <td style="width:8%;"><?php echo TABLE_ABORTION; ?></td>
                <td style="width:2%;">:</td>
                <td style="width:10%;"><?php echo $this->Form->input('abortion',array('name'=>'data[MidWifeService][abortion]','value'=>'0','style'=>'width:100%;','label'=>FALSE)); ?></td>
                <td style="width:10%;"><?php echo TABLE_INTERUPTION_VOLONTAIN; ?></td>
                <td style="width:2%;">:</td>
                <td style="width:10%;"><?php echo $this->Form->input('interuption_volontain',array('name'=>'data[MidWifeService][interuption_volontain]','value'=>'0','style'=>'width:100%;','label'=>FALSE)); ?></td>
                <td style="width:52%;" colspan="4"></td>
            </tr>
            <tr>
                <td><?php __(MENU_ACCON_CHEMENT); ?></td>    
            </tr>
            <tr>
                <td style="width:8%;"></td>
                <td style="width:10%;"><?php echo TABLE_BIRTH; ?></td>
                <td style="width:2%;">:</td>
                <td style="width:15%;"><?php echo $this->Form->input('birth',array('name'=>'data[MidWifeService][birth]','value'=>'0','style'=>'width:100%;','label'=>FALSE)); ?></td>
                <td style="width:15%;"><?php echo MENU_NEE_MOIT; ?></td>
                <td style="width:2%;">:</td>
                <td style="width:15%;"><?php echo $this->Form->input('nee_moit',array('name'=>'data[MidWifeService][nee_moit]','value'=>'0','style'=>'width:100%;','label'=>FALSE)); ?></td>
                <td style="width:15%;"><?php echo MENU_MOIT_NEE; ?></td>
                <td style="width:2%;">:</td>
                <td style="width:10%;"><?php echo $this->Form->input('mort_nee',array('name'=>'data[MidWifeService][mort_nee]','value'=>'0','style'=>'width:100%;','label'=>FALSE)); ?></td>
                <td style=""></td>
            </tr>
            <tr>
                <td><?php __(MENU_ACCONCHEMENT_RERME); ?></td>    									
            </tr> 
            <tr>
                <td></td>
                <td><?php echo TABLE_ACCONCHEM_NORMAL; ?></td>
                <td style="width:2%;">:</td>
                <td style="width:10%;"><?php echo $this->Form->input('acconchement_normal',array('name'=>'data[MidWifeService][acconchement_normal]','value'=>'0','style'=>'width:100%;','label'=>FALSE)); ?></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td><?php echo MENU_ANORMAT; ?></td>
            </tr>
            <tr>
                <td></td>
                <td><?php echo TABLE_CAESAREAN; ?></td>
                <td style="width:2%;">:</td>
                <td style="width:10%;"><?php echo $this->Form->input('caesarean',array('name'=>'data[MidWifeService][caesarean]','value'=>'0','style'=>'width:100%;','label'=>FALSE)); ?></td>
                <td><?php echo TABLE_ACC_PAR_VENTONSE; ?></td>
                <td style="width:2%;">:</td>
                <td style="width:10%;"><?php echo $this->Form->input('acc_par_ventonse',array('name'=>'data[MidWifeService][acc_par_ventonse]','value'=>'0','style'=>'width:100%;','label'=>FALSE)); ?></td>
                <td></td>
            </tr>
        </table>
    </fieldset>
    <fieldset>
        <legend><?php __(PATIENT_STORY_BEFORE); ?></legend>
        <table style="width: 100%;">
            <tr>
                <td style="width:15%;"><?php __(PATIENT_MADIE_DES_REINS); ?></td>
                <td style="width:2%;">:</td>
                <td style="width:10%;"><?php echo $this->Form->input('edema',array('type'=>'checkbox','name'=>'data[MidWifeService][edema]','value'=>'1','label'=>FALSE)); ?><label for="MidWifeServiceEdema"><?php echo TABLE_EDEMA; ?></label></td>                
                <td style="width:10%;"><?php echo $this->Form->input('albuminuria',array('type'=>'checkbox','name'=>'data[MidWifeService][albuminuria]','value'=>'1','label'=>FALSE)); ?><label for="MidWifeServiceAlbuminuria"><?php echo TABLE_ALBUMINURIA; ?></label></td>
                <td style="width:10%;">
                    <?php echo $this->Form->input('cadiojathie',array('type'=>'checkbox','name'=>'data[MidWifeService][cadiojathie]','value'=>'1','label'=>FALSE)); ?><label for="MidWifeServiceCadiojathie"><?php echo TABLE_CADIOJATHIE; ?></label>                    
                </td>
                <td>
                    <?php echo $this->Form->input('asthma',array('type'=>'checkbox','name'=>'data[MidWifeService][asthma]','value'=>'1','label'=>FALSE)); ?><label for="MidWifeServiceAsthma"><?php echo TABLE_ASTHMA; ?></label>
                </td>
            </tr>                                   
            <tr>
                <td><?php echo TABLE_OTHER; ?></td>
                <td>:</td>
                <td colspan="5"><?php echo $this->Form->input('other', array('name'=>'data[MidWifeService][other]', 'label'=>FALSE, 'style' => 'width:450px;')); ?></td>
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

