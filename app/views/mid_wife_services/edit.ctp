<?php
echo $this->element('prevent_multiple_submit');
$absolute_url = FULL_BASE_URL . Router::url("/", false);
echo $javascript->link('uninums.min');
$tblNameRadom = "tbl" . rand();
?>
<script type="text/javascript">
    $(document).ready(function(){
        $("#ment_date,#est_deliv_date,#ech_date" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
            yearRange: '-100:-0'
        }).unbind("blur");
        // Prevent Key Enter
        preventKeyEnter();
        $("#MidWifeServiceEditForm").validationEngine();
        $("#MidWifeServiceEditForm").ajaxForm({
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
    // end document
    
</script>
<div style="padding: 5px;border: 1px dashed #3C69AD;">
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
<?php echo $this->Form->input('id'); ?>
<?php
foreach ($patient as $patient):  ?>

<legend id="showPatientInfo<?php echo $tblNameRadom;?>" style="display:none;"><a href="#" id="btnShowPatientInfo<?php echo $tblNameRadom;?>" style="background: #CCCCCC; font-weight: bold;"><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?> [ Show ] </a> </legend>
<fieldset id="patientInfo<?php echo $tblNameRadom;?>" style="border: 1px dashed #3C69AD;">
<legend><a href="#" id="btnHidePatientInfo<?php echo $tblNameRadom;?>" style="background: #CCCCCC; font-weight: bold;"> <?php echo MENU_PATIENT_MANAGEMENT_INFO; ?> [ Hide ] </a></legend>
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
            </tr>
        </table>
    </div>
</fieldset><br> 

<fieldset style="border: 1px dashed #3C69AD;">
    <legend style="background: #EF0931; font-weight: bold;"><?php __(GENERAL_REQUEST); ?></legend>
    <table style="width: 100%;" cellspacing="0">
        <tr>
            <td>
                <?php 
                    $queryDataFromDoctor=  mysql_query("SELECT mwsreq.*,mwsreq.id as id FROM mid_wife_service_requests as mwsreq "
                            . "INNER JOIN other_service_requests as osreq ON osreq.id=mwsreq.other_service_request_id "
                            . "INNER JOIN queued_doctors as qd ON qd.id=osreq.queued_doctor_id "
                            . "INNER JOIN queues as q ON q.id=qd.queue_id WHERE osreq.is_active=1 AND queue_id=".$patient['MidWifeService']['mid_wife_service_queue_id']);
                    $dataRequest=  mysql_fetch_array($queryDataFromDoctor);
                    echo $dataRequest['mid_wife_description'];
                ?>
                <input type="hidden" value="<?php echo $dataRequest['id']; ?>" name="data[MidWifeServiceRequest][id]">
                <input type="hidden" value="<?php echo $patient['Queue']['id']; ?>" name="data[Queue][id]" id="queueId">
                <input type="hidden" value="<?php echo $patient['MidWifeService']['id']; ?>" name="data[MidWifeService][id]">
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
                <td style="width:20%;"><input type="text" id="ment_date" name="data[MidWifeService][last_mentstruation_period]" value="<?php echo $patient['MidWifeService']['last_mentstruation_period']; ?>" style="width:90%;"></td>
                <td></td>
            </tr>
            <tr>
                <td><?php echo TABLE_ESTIMATE_DELIVERY_DATE; ?></td>
                <td style="width:3%;">:</td>
                <td><input type="text" id="est_deliv_date" name="data[MidWifeService][estimate_delivery_date]" value="<?php echo $patient['MidWifeService']['estimate_delivery_date']; ?>" style="width:90%;" ></td>
                <td></td>
            </tr>
            <tr>
                <td><?php echo TABLE_ECHO; ?></td>
                <td>:</td>
                <td><input type="text" id="ech_date" name="data[MidWifeService][echo]" style="width:90%;" value="<?php echo $patient['MidWifeService']['echo']; ?>"></td>
                <td></td>
            </tr>
            <tr>
                <td style="width:10%;"><?php echo TABLE_WEIGHT; ?></td>
                <td>:</td>
                <td style="width:20%;"><input type="text" name="data[MidWifeService][weight]" style="width:90%;" value="<?php echo $patient['MidWifeService']['weight']; ?>"></td>
                <td style="width:10%;"><?php echo TABLE_HEIGHT; ?></td>
                <td style="width:3%;">:</td>
                <td style="width:20%;"><input type="text" name="data[MidWifeService][height]" style="width:90%;" value="<?php echo $patient['MidWifeService']['height']; ?>"></td>
                <td></td>
            </tr>
            <tr>
                <td><?php echo TABLE_GESTRATION; ?></td>
                <td>:</td>
                <td><input type="text" name="data[MidWifeService][gestation]" style="width:90%;" value="<?php echo $patient['MidWifeService']['gestation']; ?>"></td>
                <td><?php echo TABLE_BABY; ?></td>
                <td>:</td>
                <td><input type="text" name="data[MidWifeService][baby]" style="width:90%;" value="<?php echo $patient['MidWifeService']['baby']; ?>"></td>
                <td></td>
            </tr>
        </table>
    </fieldset>
    <br>
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
                <td style="width:10%;"><?php echo $this->Form->input('abortion',array('name'=>'data[MidWifeService][abortion]','value'=>$patient['MidWifeService']['abortion'],'style'=>'width:100%;','label'=>FALSE)); ?></td>
                <td style="width:10%;padding-left:20px;"><?php echo TABLE_INTERUPTION_VOLONTAIN; ?></td>
                <td style="width:2%;">:</td>
                <td style="width:10%;"><?php echo $this->Form->input('interuption_volontain',array('name'=>'data[MidWifeService][interuption_volontain]','value'=>$patient['MidWifeService']['interuption_volontain'],'style'=>'width:100%;','label'=>FALSE)); ?></td>
                <td style="width:52%;" colspan="4"></td>
            </tr>
            <tr>
                <td><?php __(MENU_ACCON_CHEMENT); ?></td>    
            </tr>
            <tr>
                <td style="width:8%;"></td>
                <td style="width:10%;"><?php echo TABLE_BIRTH; ?></td>
                <td style="width:2%;">:</td>
                <td style="width:10%;"><?php echo $this->Form->input('birth',array('name'=>'data[MidWifeService][birth]','value'=>$patient['MidWifeService']['birth'],'style'=>'width:100%;','label'=>FALSE)); ?></td>
                <td style="width:15%;padding-left:20px;"><?php echo MENU_NEE_MOIT; ?></td>
                <td style="width:2%;">:</td>
                <td style="width:10%;"><?php echo $this->Form->input('nee_moit',array('name'=>'data[MidWifeService][nee_moit]','value'=>$patient['MidWifeService']['nee_moit'],'style'=>'width:100%;','label'=>FALSE)); ?></td>
                <td style="width:15%;padding-left:20px;"><?php echo MENU_MOIT_NEE; ?></td>
                <td style="width:2%;">:</td>
                <td style="width:10%;"><?php echo $this->Form->input('mort_nee',array('name'=>'data[MidWifeService][mort_nee]','value'=>$patient['MidWifeService']['mort_nee'],'style'=>'width:100%;','label'=>FALSE)); ?></td>
                <td style=""></td>
            </tr>
            <tr>
                <td><?php __(MENU_ACCONCHEMENT_RERME); ?></td>    									
            </tr> 
            <tr>
                <td></td>
                <td><?php echo TABLE_ACCONCHEM_NORMAL; ?></td>
                <td style="width:2%;">:</td>
                <td style="width:10%;"><?php echo $this->Form->input('acconchement_normal',array('name'=>'data[MidWifeService][acconchement_normal]','value'=>$patient['MidWifeService']['acconchement_normal'],'style'=>'width:100%;','label'=>FALSE)); ?></td>
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
                <td style="width:10%;"><?php echo $this->Form->input('caesarean',array('name'=>'data[MidWifeService][caesarean]','value'=>$patient['MidWifeService']['caesarean'],'style'=>'width:100%;','label'=>FALSE)); ?></td>
                <td style="padding-left:20px;"><?php echo TABLE_ACC_PAR_VENTONSE; ?></td>
                <td style="width:2%;">:</td>
                <td style="width:10%;"><?php echo $this->Form->input('acc_par_ventonse',array('name'=>'data[MidWifeService][acc_par_ventonse]','value'=>$patient['MidWifeService']['acc_par_ventonse'],'style'=>'width:100%;','label'=>FALSE)); ?></td>
                <td></td>
            </tr>
        </table>
    </fieldset>
    <br>
    <fieldset>
        <legend><?php __(PATIENT_STORY_BEFORE); ?></legend>
        <table style="width: 100%;">
            <tr>
                <td style="width:15%;"><?php __(PATIENT_MADIE_DES_REINS); ?></td>
                <td style="width:2%;">:</td>
                <td style="width:10%;">
                    <?php 
                    if($patient['MidWifeService']['edema']==1){
                        echo $this->Form->input('edema',array('type'=>'checkbox','name'=>'data[MidWifeService][edema]','value'=>'1','label'=>FALSE,'checked'=>TRUE));
                    }else{
                        echo $this->Form->input('edema',array('type'=>'checkbox','name'=>'data[MidWifeService][edema]','value'=>'1','label'=>FALSE));
                    }
                    ?>
                    <label for="MidWifeServiceEdema"><?php echo TABLE_EDEMA; ?></td></label>
                
                <td style="width:10%;">
                    <?php 
                    if($patient['MidWifeService']['albuminuria']==1){
                        echo $this->Form->input('albuminuria',array('type'=>'checkbox','name'=>'data[MidWifeService][albuminuria]','value'=>'1','label'=>FALSE,'checked'=>TRUE));
                    }else{
                        echo $this->Form->input('albuminuria',array('type'=>'checkbox','name'=>'data[MidWifeService][albuminuria]','value'=>'1','label'=>FALSE));
                    }
                    ?>
                    <label for="MidWifeServiceAlbuminuria"><?php echo TABLE_ALBUMINURIA; ?></label>
                </td>
                <td style="width:10%;">
                    <?php 
                    if($patient['MidWifeService']['cadiojathie']==1){
                        echo $this->Form->input('cadiojathie',array('type'=>'checkbox','name'=>'data[MidWifeService][cadiojathie]','value'=>'1','label'=>FALSE,'checked'=>TRUE));
                    }else{
                        echo $this->Form->input('cadiojathie',array('type'=>'checkbox','name'=>'data[MidWifeService][cadiojathie]','value'=>'1','label'=>FALSE));
                    }
                    ?>
                    <label for="MidWifeServiceCadiojathie"><?php echo TABLE_CADIOJATHIE; ?></label>
                </td>
                <td>
                    <?php 
                    if($patient['MidWifeService']['asthma']==1){
                        echo $this->Form->input('asthma',array('type'=>'checkbox','name'=>'data[MidWifeService][asthma]','value'=>'1','label'=>FALSE,'checked'=>TRUE));
                    }else{
                        echo $this->Form->input('asthma',array('type'=>'checkbox','name'=>'data[MidWifeService][asthma]','value'=>'1','label'=>FALSE));
                    }
                    ?>
                    <label for="MidWifeServiceAsthma"><?php echo TABLE_ASTHMA; ?></label>
                </td>
            </tr>            
            <tr>
                <td><?php echo TABLE_OTHER; ?></td>
                <td>:</td>
                <td colspan="5"><?php echo $this->Form->input('other', array('name'=>'data[MidWifeService][other]', 'label'=>FALSE, 'value'=>$patient['MidWifeService']['other'], 'style' => 'width:450px;')); ?></td>
            </tr>
        </table>
    </fieldset>
</fieldset>
<br />
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <span class="txtSaveMidWifeService"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<div style="clear: both;"></div>
<?php endforeach; ?>
<?php echo $this->Form->end(); ?>