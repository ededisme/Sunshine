<?php
// Authentication
$this->element('check_access');
$allowMidWifeEditCheckUpPatient=checkAccess($user['User']['id'], $this->params['controller'], 'editCheckUpPatient');
$allowMidWifeCheckUpPatient=checkAccess($user['User']['id'], $this->params['controller'], 'checkUpPatient');
$allowMidWifeAdd=checkAccess($user['User']['id'], $this->params['controller'], 'addNewMidWife');
$allowMidWifeEdit=checkAccess($user['User']['id'], $this->params['controller'], 'edit');
echo $this->element('prevent_multiple_submit');
$absolute_url = FULL_BASE_URL . Router::url("/", false);
echo $javascript->link('uninums.min');
include("includes/function.php");
$tblNameRadom = "tbl" . rand();
?>
<script type="text/javascript">
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        $("#MidWifeServiceAddEditMidWifeForm").validationEngine();
        $("#MidWifeServiceAddEditMidWifeForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveMidWifeService").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $(".btnBackMidWifeService").click();
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
        // add new mid wife service
        $(".btnAddNew").click(function(event){
            event.preventDefault();
            var queueId = $(this).attr('rel');
            $('.dataEdit').html("<?php echo ACTION_LOADING; ?>");
            $('.dataEdit').load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/addNewMidWife/"+queueId);
        });
        // edit mid wife service
        $(".btnEdit").click(function(event){
            event.preventDefault();
            var id = $(this).attr('rel');
            $('.dataEdit').html("<?php echo ACTION_LOADING; ?>");
            $('.dataEdit').load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/edit/"+id);

        });
        // add new check up patient mid wife
        $(".btnConsult").click(function(event){
            event.preventDefault();
            var id = $(this).attr('rel');
            var queueId = $(this).attr('queue');
            $('.dataEdit').html("<?php echo ACTION_LOADING; ?>");
            $('.dataEdit').load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/checkUpPatient/"+id+'/'+queueId);
        });
        // edit check up patient mid wife
        $(".btnEditConsult").click(function(event){
            event.preventDefault();
            var id = $(this).attr('rel');
            $('.dataEdit').html("<?php echo ACTION_LOADING; ?>");
            $('.dataEdit').load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/editCheckUpPatient/"+id);
        });
        
        $(".btnBackMidWifeService").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableMidWife.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().parent().parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
        
        $( ".accordionDetail" ).accordion({
            collapsible: true,
            active: false
        });               
        $('#accordion').accordion({
            collapsible: true,
            active: false
        });          
        
        $(".btnBackQueueMidWifeService").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableMidWife.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().parent().parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
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
<div class="dataEdit">
    <div style="padding: 5px;border: 1px dashed #bbbbbb;">
        <div class="buttons">
            <a href="#" class="positive btnBackQueueMidWifeService">
                <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
                <?php echo ACTION_BACK; ?>
            </a>
        </div>
        <div style="clear: both;"></div>
    </div>
    <br />
    <?php echo $this->Form->create('MidWifeService',array('enctype' => 'multipart/form-data')); ?>
    <input name="data[QeuedDoctor][id]" type="hidden" value="<?php echo $patient['QeuedDoctor']['id'];?>"/>
    <input name="data[Queue][id]" type="hidden" value="<?php echo $patient['Queue'][0]['id'];?>"/>
    
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
        <div id="accordion">
            <?php foreach ($midWife as $patient){?>
            <h3>
                <a href="" rel="<?php echo $patient['MidWifeService']['id'];?>">
                    <?php echo $patient['MidWifeService']['created']?>
                    <?php if($allowMidWifeEdit){ ?>
                    <div style="float:right;">
                        <img alt="" rel="<?php echo $patient['MidWifeService']['id'];?>" src="<?php echo $this->webroot; ?>img/action/edit.png" class="btnEdit" name="<?php echo '1'; ?>" onmouseover="Tip('<?php echo ACTION_EDIT; ?>')"  />
                    </div>
                    <?php } ?>
                    <div style="clear: both;"></div>
                </a>
            </h3>
            <div>
                <fieldset>
                    <legend><?php __(MENU_STORY_PATIENTS); ?></legend>
                    <table style="width: 100%;">
                        <tr>
                            <td style="width:10%;"><?php echo TABLE_LAST_MENSTRUATION_PERIOD; ?></td>
                            <td style="width:3%;">:</td>
                            <td style="width:20%;"><?php echo $patient['MidWifeService']['last_mentstruation_period']; ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?php echo TABLE_ESTIMATE_DELIVERY_DATE; ?></td>
                            <td style="width:3%;">:</td>
                            <td><?php echo $patient['MidWifeService']['estimate_delivery_date']; ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?php echo TABLE_ECHO; ?></td>
                            <td>:</td>
                            <td><?php echo $patient['MidWifeService']['echo']; ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td style="width:10%;"><?php echo TABLE_WEIGHT; ?></td>
                            <td>:</td>
                            <td style="width:20%;"><?php echo $patient['MidWifeService']['weight']; ?></td>
                            <td style="width:10%;"><?php echo TABLE_HEIGHT; ?></td>
                            <td style="width:3%;">:</td>
                            <td style="width:20%;"><?php echo $patient['MidWifeService']['height']; ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?php echo TABLE_GESTRATION; ?></td>
                            <td>:</td>
                            <td><?php echo $patient['MidWifeService']['gestation']; ?></td>
                            <td><?php echo TABLE_BABY; ?></td>
                            <td>:</td>
                            <td><?php echo $patient['MidWifeService']['baby']; ?></td>
                            <td></td>
                        </tr>
                    </table>
                </fieldset><br>
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
                            <td style="width:10%;"><?php echo $patient['MidWifeService']['abortion']; ?></td>
                            <td style="width:10%;padding-left:20px;"><?php echo TABLE_INTERUPTION_VOLONTAIN; ?></td>
                            <td style="width:2%;">:</td>
                            <td style="width:10%;"><?php echo $patient['MidWifeService']['interuption_volontain']; ?></td>
                            <td style="width:52%;" colspan="4"></td>
                        </tr>
                        <tr>
                            <td><?php __(MENU_ACCON_CHEMENT); ?></td>    
                        </tr>
                        <tr>
                            <td style="width:8%;"></td>
                            <td style="width:10%;"><?php echo TABLE_BIRTH; ?></td>
                            <td style="width:2%;">:</td>
                            <td style="width:10%;"><?php echo $patient['MidWifeService']['birth']; ?></td>
                            <td style="width:15%;padding-left:20px;"><?php echo MENU_NEE_MOIT; ?></td>
                            <td style="width:2%;">:</td>
                            <td style="width:10%;"><?php echo $patient['MidWifeService']['nee_moit']; ?></td>
                            <td style="width:15%;padding-left:20px;"><?php echo MENU_MOIT_NEE; ?></td>
                            <td style="width:2%;">:</td>
                            <td style="width:10%;"><?php echo $patient['MidWifeService']['mort_nee']; ?></td>
                            <td style=""></td>
                        </tr>
                        <tr>
                            <td><?php __(MENU_ACCONCHEMENT_RERME); ?></td>    									
                        </tr> 
                        <tr>
                            <td></td>
                            <td><?php echo TABLE_ACCONCHEM_NORMAL; ?></td>
                            <td style="width:2%;">:</td>
                            <td style="width:10%;"><?php echo $patient['MidWifeService']['acconchement_normal']; ?></td>
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
                            <td style="width:10%;"><?php echo $patient['MidWifeService']['caesarean']; ?></td>
                            <td style="padding-left:20px;"><?php echo TABLE_ACC_PAR_VENTONSE; ?></td>
                            <td style="width:2%;">:</td>
                            <td style="width:10%;"><?php echo $patient['MidWifeService']['acc_par_ventonse']; ?></td>
                            <td></td>
                        </tr>
                    </table>
                </fieldset><br>
                <fieldset>
                    <legend><?php __(PATIENT_STORY_BEFORE); ?></legend>
                    <table style="width: 100%;">
                        <tr>
                            <td style="width:10%;"><?php __(PATIENT_MADIE_DES_REINS); ?></td>
                            <td style="width:2%;">:</td>
                            <td style="width:10%;">
                                <?php 
                                if($patient['MidWifeService']['edema']==1){
                                    echo $this->Form->input('edema',array('type'=>'checkbox','name'=>'data[MidWifeService][edema]','value'=>'1','label'=>FALSE,'checked'=>TRUE,'disabled'=>TRUE));
                                }else{
                                    echo $this->Form->input('edema',array('type'=>'checkbox','name'=>'data[MidWifeService][edema]','value'=>'1','label'=>FALSE,'disabled'=>TRUE));
                                }
                                ?>
                                <?php echo TABLE_EDEMA; ?></td>

                            <td style="width:10%;">
                                <?php 
                                if($patient['MidWifeService']['albuminuria']==1){
                                    echo $this->Form->input('albuminuria',array('type'=>'checkbox','name'=>'data[MidWifeService][albuminuria]','value'=>'1','label'=>FALSE,'checked'=>TRUE,'disabled'=>TRUE));
                                }else{
                                    echo $this->Form->input('albuminuria',array('type'=>'checkbox','name'=>'data[MidWifeService][albuminuria]','value'=>'1','label'=>FALSE,'disabled'=>TRUE));
                                }
                                ?>
                                <?php echo TABLE_ALBUMINURIA; ?></td>
                            <td style="width:10%;">
                                <?php 
                                if($patient['MidWifeService']['cadiojathie']==1){
                                    echo $this->Form->input('cadiojathie',array('type'=>'checkbox','name'=>'data[MidWifeService][cadiojathie]','value'=>'1','label'=>FALSE,'checked'=>TRUE,'disabled'=>TRUE));
                                }else{
                                    echo $this->Form->input('cadiojathie',array('type'=>'checkbox','name'=>'data[MidWifeService][cadiojathie]','value'=>'1','label'=>FALSE,'disabled'=>TRUE));
                                }
                                ?>
                                <?php echo TABLE_CADIOJATHIE; ?>
                            </td>
                            <td>
                                <?php 
                                if($patient['MidWifeService']['asthma']==1){
                                    echo $this->Form->input('asthma',array('type'=>'checkbox','name'=>'data[MidWifeService][asthma]','value'=>'1','label'=>FALSE,'checked'=>TRUE,'disabled'=>TRUE));
                                }else{
                                    echo $this->Form->input('asthma',array('type'=>'checkbox','name'=>'data[MidWifeService][asthma]','value'=>'1','label'=>FALSE,'disabled'=>TRUE));
                                }
                                ?>
                                <?php echo TABLE_ASTHMA; ?>
                            </td>
                        </tr>                       
                        <tr>
                            <td><?php echo TABLE_OTHER; ?></td>
                            <td>:</td>
                            <td colspan="5" style="padding-left: 5px;">
                                <?php echo nl2br($patient['MidWifeService']['other']);?>
                            </td>
                        </tr>
                    </table>
                </fieldset><br>
                <fieldset>
                    <legend><?php echo MENU_CONSULT_PATIENTS; ?></legend>
                    <div class="accordionDetail">
                        <?php 
                        $query=mysql_query("SELECT * FROM mid_wife_check_up_patients WHERE is_active=1 AND mid_wife_service_id=".$patient['MidWifeService']['id']);
                        while($data=mysql_fetch_array($query)){  
                        ?>
                        <h3>
                            <a href="">                                                
                                <?php echo $data['created'];?>
                                <?php if($allowMidWifeEditCheckUpPatient){ ?>
                                <div style="float:right;">
                                    <img alt="" rel="<?php echo $data['id'];?>" src="<?php echo $this->webroot; ?>img/action/edit.png" class="btnEditConsult" name="<?php echo '1'; ?>" onmouseover="Tip('<?php echo ACTION_EDIT; ?>')"  />
                                </div>
                                <?php } ?>
                                <div style="clear: both;"></div>
                            </a>
                        </h3>
                        <div>
                            <table class="defualtTable1">
                                <tr>
                                    <td><?php echo TABLE_WEIGHT.':'; ?></td>
                                    <td width="20%"><?php echo $data['weight'].' Kg'; ?></td>
                                    <td width="10%"><?php echo TABLE_HEIGHT.':'; ?></td>
                                    <td><?php echo $data['height']. ' Cm' ?></td>                            
                                </tr>
                                <tr>
                                    <td><?php echo TABLE_BLOOD_PRESSURE.':'; ?></td>
                                    <td colspan="7"><?php echo $data['blood_pressure'] ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo TABLE_PULE.':'; ?></td>
                                    <td><?php echo $data['pulse'] ?></td>
                                    <td><?php echo TABLE_TEMPERATURE.':'; ?></td>
                                    <td colspan="7"><?php echo $data['temperature']. '  &#8451' ?></td>                            
                                </tr>
                                <tr>
                                    <td><?php echo TABLE_PRESENTATION.':'; ?></td>
                                    <td colspan="7"><?php echo $data['presentation'] ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo TABLE_UTERUS_HEIGHT.':'; ?></td>
                                    <td colspan="7"><?php echo $data['uterus_height'] ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo TABLE_BABY_HEART_RATE.':'; ?></td>
                                    <td colspan="7"><?php echo $data['baby_heart_rate'] ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo TABLE_IRON.':'; ?></td>
                                    <td colspan="7"><?php echo $data['iron'] ?></td>
                                </tr>                                               
                                <tr>
                                    <td width="15%"><?php echo PATIENT_MADIE_DES_REINS.':'; ?></td>
                                </tr>  
                                <tr>
                                    <td>
                                        <?php echo TABLE_EDEMA.':'; ?>
                                    </td>
                                    <td>
                                        <?php if ($data['edema']=="1"){?>
                                            <input type="checkbox" disabled="disabled" checked="checked" />
                                        <?php }else{?>
                                            <input type="checkbox" disabled="disabled" />
                                        <?php }?>
                                    </td>
                                </tr>  
                                <tr>        
                                    <td><?php echo TABLE_ALBUMINURIA.':'; ?></td>
                                    <td>
                                        <?php if ($data['albuminuria']=="1"){?>
                                            <input type="checkbox" disabled="disabled" checked="checked" />
                                        <?php }else{?>
                                            <input type="checkbox" disabled="disabled" />
                                        <?php }?>
                                    </td>                            
                                </tr>  
                                <tr>
                                    <td><?php echo TABLE_ASTHMA.':'; ?></td>
                                    <td colspan="7">
                                        <?php if ($data['asthma']=="1"){?>
                                            <input type="checkbox" disabled="disabled" checked="checked" />
                                        <?php }else{?>
                                            <input type="checkbox" disabled="disabled" />
                                        <?php }?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo TABLE_OTHER.':'; ?></td>
                                    <td colspan="7" ><?php echo $data['other']; ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo TABLE_NEXT_APPOINTMENT.':'; ?></td>
                                    <td colspan="7"><?php echo dateShort($data['next_appointment']); ?></td>
                                </tr>

                            </table>
                        </div>
                    <?php } ?>
                    </div><br>
                    <?php if($allowMidWifeCheckUpPatient){ ?>
                    <div class="buttons">
                        <button class="btnConsult" rel="<?php echo $patient['MidWifeService']['id']; ?>" queue="<?php echo $this->params['pass'][0]; ?>">
                            <img src="<?php echo $this->webroot; ?>img/icon/plus_red.png" alt=""/>
                            <span class="txtSave"><?php echo ADD_CONSULT_PATIENTS; ?></span>
                        </button>
                    </div>
                    <?php } ?>
                </fieldset>
            </div>
            <?php }?>
        </div><br>
        <?php if($allowMidWifeAdd){ ?>
        <div class="buttons">
            <button class="btnAddNew" rel="<?php echo $this->params['pass'][0]; ?>">
                <img src="<?php echo $this->webroot; ?>img/icon/plus.png" alt=""/>
                <span class="txtSave"><?php echo ADD_STORY_PATIENT; ?></span>
            </button>
        </div>
        <?php } ?>
    </fieldset>
    <div class="clear"></div>
    <?php echo $this->Form->end(); ?>
</div>