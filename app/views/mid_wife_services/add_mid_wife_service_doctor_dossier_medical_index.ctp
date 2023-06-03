<?php
// Authentication
$this->element('check_access');
$allowMidWifeAddNewDossierMedical=checkAccess($user['User']['id'], $this->params['controller'], 'addNewDossierMedical');
$allowMidWifeEditDossierMedical=checkAccess($user['User']['id'], $this->params['controller'], 'editDossierMedical');
$allowMidWifeAddNewTracking=checkAccess($user['User']['id'], $this->params['controller'], 'addNewTracking');
$allowMidWifeEditTracking=checkAccess($user['User']['id'], $this->params['controller'], 'editTracking');
$allowMidWifeAddNewAccouchement=checkAccess($user['User']['id'], $this->params['controller'], 'addNewAccouchement');
$allowMidWifeEditAccouchement=checkAccess($user['User']['id'], $this->params['controller'], 'editAccouchement');
$allowMidWifeAddNewDeliverance=checkAccess($user['User']['id'], $this->params['controller'], 'addNewDeliverance');
$allowMidWifeEditDeliverance=checkAccess($user['User']['id'], $this->params['controller'], 'editDeliverance');
$allowMidWifeAddNewAccouchementFirst=checkAccess($user['User']['id'], $this->params['controller'], 'addNewAccouchementFirstTime');
$allowMidWifeEditAccouchementFirst=checkAccess($user['User']['id'], $this->params['controller'], 'editAccouchementFirstTime');
$allowMidWifeAddNewAccouchementNext=checkAccess($user['User']['id'], $this->params['controller'], 'addNewAccouchementNextTime');
$allowMidWifeEditAccouchementNext=checkAccess($user['User']['id'], $this->params['controller'], 'editAccouchementNextTime');
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
        $("#MidWifeServiceAddMidWifeServiceDoctorDossierMedicalIndexForm").validationEngine();
        $("#MidWifeServiceAddMidWifeServiceDoctorDossierMedicalIndexForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSave").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {                
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif"); 
                $(".btnBackQueueMidWifeServiceDossierMedical").click();               
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
        
        // add new mid wife service dossier medical
        $(".btnAddNew").click(function(event){
            event.preventDefault();
            var queueId = $(this).attr('rel');
            $('#dataDossierMedical').html("<?php echo ACTION_LOADING; ?>");
            $('#dataDossierMedical').load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/addNewDossierMedical/"+queueId);
        });
        // edit mid wife service
        $(".btnEdit").click(function(event){
            event.preventDefault();
            var id = $(this).attr('rel');
            $('#dataDossierMedical').html("<?php echo ACTION_LOADING; ?>");
            $('#dataDossierMedical').load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/editDossierMedical/"+id);

        });
        // add new tracking
        $(".btnAddNewTracking").click(function(event){
            event.preventDefault();
            var id = $(this).attr('rel');
            $('#dataDossierMedical').html("<?php echo ACTION_LOADING; ?>");
            $('#dataDossierMedical').load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/addNewTracking/"+id);
        });
        //edit tracking
        $(".btnEditTracking").click(function(event){
            event.preventDefault();
            var id = $(this).attr('rel');
            $('#dataDossierMedical').html("<?php echo ACTION_LOADING; ?>");
            $('#dataDossierMedical').load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/editTracking/"+id);

        });
        // add new accouchement
        $(".btnAddNewAccouchement").click(function(event){
            event.preventDefault();
            var id = $(this).attr('rel');
            $('#dataDossierMedical').html("<?php echo ACTION_LOADING; ?>");
            $('#dataDossierMedical').load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/addNewAccouchement/"+id);
        });
        //edit accouchement
        $(".btnEditAccouchement").click(function(event){
            event.preventDefault();
            var id = $(this).attr('rel');
            $('#dataDossierMedical').html("<?php echo ACTION_LOADING; ?>");
            $('#dataDossierMedical').load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/editAccouchement/"+id);

        });
        // add new Deliverance
        $(".btnAddNewDeliverance").click(function(event){
            event.preventDefault();
            var id = $(this).attr('rel');
            $('#dataDossierMedical').html("<?php echo ACTION_LOADING; ?>");
            $('#dataDossierMedical').load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/addNewDeliverance/"+id);
        });
        //edit Deliverance
        $(".btnEditDeliverance").click(function(event){
            event.preventDefault();
            var id = $(this).attr('rel');
            $('#dataDossierMedical').html("<?php echo ACTION_LOADING; ?>");
            $('#dataDossierMedical').load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/editDeliverance/"+id);

        });
        // add new first time
        $(".btnAddNewFirstTime").click(function(event){
            event.preventDefault();
            var id = $(this).attr('rel');
            $('#dataDossierMedical').html("<?php echo ACTION_LOADING; ?>");
            $('#dataDossierMedical').load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/addNewAccouchementFirstTime/"+id);
        });
        //edit first time
        $(".btnEditFirstTime").click(function(event){
            event.preventDefault();
            var id = $(this).attr('rel');
            $('#dataDossierMedical').html("<?php echo ACTION_LOADING; ?>");
            $('#dataDossierMedical').load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/editAccouchementFirstTime/"+id);

        });
        // add new next time
        $(".btnAddNewNextTime").click(function(event){
            event.preventDefault();
            var id = $(this).attr('rel');
            $('#dataDossierMedical').html("<?php echo ACTION_LOADING; ?>");
            $('#dataDossierMedical').load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/addNewAccouchementNextTime/"+id);
        });
        //edit next time
        $(".btnEditNextTime").click(function(event){
            event.preventDefault();
            var id = $(this).attr('rel');
            $('#dataDossierMedical').html("<?php echo ACTION_LOADING; ?>");
            $('#dataDossierMedical').load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/editAccouchementNextTime/"+id);

        });
        // button back
        $(".btnBackQueueMidWifeServiceDossierMedical").click(function(event) {
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableMidWife.fnDraw(false);
            var rightPanel = $(this).parent().parent().parent();
            var leftPanel = rightPanel.parent().parent().parent().find(".leftPanel");
            rightPanel.hide();
            rightPanel.html("");
            leftPanel.show("slide", {direction: "left"}, 500);
        });
        
        $( ".accordionDetail" ).accordion({
            collapsible: true,
            active: false
        });               
        $('#accordion').accordion({
            collapsible: true,
            active: false
        });   
        
        $("#ment_date,#est_deliv_date,#ech_date" ).datepicker({
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
<div id="dataDossierMedical">
    <div style="padding: 5px;border: 1px dashed #bbbbbb;">
        <div class="buttons">
            <a href="" class="positive btnBackQueueMidWifeServiceDossierMedical">
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
                    <input type="hidden" value="<?php echo $patient['Queue']['id']; ?>" name="data[Queue][id]">
                </td>
            </tr>
        </table>      
    </fieldset>
    <br/>
<!--data dossier medical-->
    <fieldset style="border: 1px dashed #3C69AD;">
        <legend><?php __(MENU_MID_WIFE_SERVICE_INFO); ?></legend>
        <div id="accordion">
            <?php foreach ($midWife as $dossierMedical){?>
            <h3>
                <a href="#" rel="<?php echo $dossierMedical['MidWifeDossierMedical']['id'];?>">
                    <?php echo $dossierMedical['MidWifeDossierMedical']['created']?>
                    <?php if($allowMidWifeEditDossierMedical){ ?>
                    <div style="float:right;">
                        <img alt="" rel="<?php echo $dossierMedical['MidWifeDossierMedical']['id'];?>" src="<?php echo $this->webroot; ?>img/action/edit.png" class="btnEdit" name="<?php echo '1'; ?>" onmouseover="Tip('<?php echo ACTION_EDIT; ?>')"  />
                    </div>
                    <?php } ?>
                    <div style="clear: both;"></div>
                </a>
            </h3>
            <div>
                <fieldset>
                    <legend><?php echo 'ស្ថានភាពអ្នកជំងឺ'; ?></legend> 
                    <table class="defualtTableNew">                                
                        <tr>
                            <td><?php echo 'Entr&eacute;e le'; ?></td>
                            <td style="width:2%;">:</td>
                            <td>
                                <?php echo $dossierMedical['MidWifeDossierMedical']['entre_le']; ?>                                        
                            </td>                                    
                        </tr>
                        <tr>
                            <td><?php echo "Diagnostic d'entr&eacute;e"; ?></td>
                            <td>:</td>
                            <td><?php echo $dossierMedical['MidWifeDossierMedical']['doagnostic_entre']; ?></td>                                    
                        </tr> 
                        <tr>
                            <td><?php echo 'Sotrie le'; ?></td>
                            <td>:</td>
                            <td><?php echo $dossierMedical['MidWifeDossierMedical']['sortie_le']; ?></td>                                    
                        </tr>
                        <tr>
                            <td style="width:10%;"><?php echo "Diagnostic de sortie"; ?></td>
                            <td>:</td>
                            <td><?php echo $dossierMedical['MidWifeDossierMedical']['doagnostic_sortie']; ?></td>                                    
                        </tr>   
                        <tr>
                            <td>PARA</td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <table border="0px">
                                    <tr>
                                        <td width="170px"><?php echo $dossierMedical['MidWifeDossierMedical']['acconchement_rerme']; ?></td>
                                        <td width="170px"><?php echo $dossierMedical['MidWifeDossierMedical']['accon_chement']; ?></td>
                                        <td width="170px"><?php echo $dossierMedical['MidWifeDossierMedical']['avortment_inv']; ?></td>
                                        <td width="170px"><?php echo $dossierMedical['MidWifeDossierMedical']['baby']; ?></td>
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
                            <td>
                                <?php if ($dossierMedical['MidWifeDossierMedical']['caesarean']=="1"){?>
                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                <?php }else{?>
                                    <input type="checkbox" disabled="disabled" />
                                <?php }?>
                            </td>
                        </tr>
                        <tr>                                
                            <td class="test">+ H&eacute;morragie...</td>
                            <td>
                                <?php if ($dossierMedical['MidWifeDossierMedical']['hemorrhage']=="1"){?>
                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                <?php }else{?>
                                    <input type="checkbox" disabled="disabled" />
                                <?php }?>
                            </td>
                        </tr>                                    
                        <tr>                                        
                            <td class="test">+ Hypertension...</td>
                            <td>
                                <?php if ($dossierMedical['MidWifeDossierMedical']['hypertension']=="1"){?>
                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                <?php }else{?>
                                    <input type="checkbox" disabled="disabled" />
                                <?php }?>
                            </td>
                        </tr>
                        <tr>                                
                            <td class="test">+ Cardiopathie...</td>
                            <td>
                                <?php if ($dossierMedical['MidWifeDossierMedical']['heart']=="1"){?>
                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                <?php }else{?>
                                    <input type="checkbox" disabled="disabled" />
                                <?php }?>
                            </td>
                        </tr>  
                        <tr>
                            <td><?php echo "Other"; ?></td>
                            <td colspan="2"><?php echo $dossierMedical['MidWifeDossierMedical']['other']; ?></td>
                        </tr>
                    </table>                                   
                </fieldset>
                <br>
                <fieldset>
                    <legend><?php echo "Examen &agrave; l'entr&eacute;e"; ?></legend>
                    <table class="defualtTableNew"> 
                        <tr>
                            <td><?php echo "TA / CmHg"; ?></td>
                            <td>:</td>
                            <td><?php echo $dossierMedical['MidWifeDossierMedical']['ta']; ?></td>
                            <td><?php echo "P / mn"; ?></td>
                            <td>:</td>
                            <td><?php echo $dossierMedical['MidWifeDossierMedical']['p']; ?></td>
                            <td><?php echo "T&#730 / &#8451"; ?></td>
                            <td>:</td>
                            <td><?php echo $dossierMedical['MidWifeDossierMedical']['t']; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo "Pr&eacute;sentation"; ?></td>
                            <td>:</td>
                            <td><?php echo $dossierMedical['MidWifeDossierMedical']['presentation']; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo "H.U / Cm"; ?></td>
                            <td>:</td>
                            <td><?php echo $dossierMedical['MidWifeDossierMedical']['hu']; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo "BCF / mn"; ?></td>
                            <td>:</td>
                            <td><?php echo $dossierMedical['MidWifeDossierMedical']['bcf']; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo "Col / Cm"; ?></td>
                            <td>:</td>
                            <td><?php echo $dossierMedical['MidWifeDossierMedical']['col']; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo "PDE"; ?></td>
                            <td>:</td>
                            <td><?php echo $dossierMedical['MidWifeDossierMedical']['pde']; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo "OEd&egrave;me (+)or(-)"; ?></td>
                            <td>:</td>
                            <td><?php echo $dossierMedical['MidWifeDossierMedical']['edema']; ?></td>
                        </tr>
                    </table>
                </fieldset>
                <br>
<!--data tracking-->           
                <fieldset>
                    <legend><?php echo 'ការតាមដានពេលសំរាល'; ?></legend>                                    
                    <fieldset>
                        <legend><?php echo 'តាមដានការសំរាល'; ?></legend> 
                        <?php
                            $birth_detail = mysql_query("SELECT * FROM mid_wife_birth_details WHERE is_active=1 AND mid_wife_dossier_medical_id =" . $dossierMedical['MidWifeDossierMedical']['id']);
                            while ($birth_details = mysql_fetch_array($birth_detail)) {
                                ?>
                                <div class="accordionDetail"> 
                                    <h3>
                                        <a href="#" rel="<?php echo $birth_details['id'];?>">
                                            <?php echo $birth_details['created']?>
                                            <?php if($allowMidWifeEditTracking){ ?>
                                            <div style="float:right;">
                                                <img alt="" rel="<?php echo $birth_details['mid_wife_dossier_medical_id'];?>" src="<?php echo $this->webroot; ?>img/action/edit.png" class="btnEditTracking" name="<?php echo '1'; ?>" onmouseover="Tip('<?php echo ACTION_EDIT; ?>')"  />
                                            </div>
                                            <?php } ?>
                                            <div style="clear: both;"></div>
                                        </a>
                                    </h3>
                                    <div>
                                        <table class="defualtTable" border="1px" style="width:100%;">
                                            <tr align="center">
                                                <td class="birth_child" rowspan="2">ម៉ោង</td>                                                            
                                                <td class="birth_child" colspan="2">កូន</td>                                                            
                                                <td class="birth_child" colspan="4">ម្តាយ</td>
                                                <td width="30%" rowspan="7">                                                                
                                                    <table class="new_table">
                                                        <tr>
                                                            <td><span style="color:red">* </span>បរិមាណទឹកភ្លោះ</td>                                                                            
                                                        </tr>
                                                        <tr>   
                                                            <td></td>
                                                            <td class="test">Hydramnios</td>
                                                            <td>
                                                                <?php if ($birth_details['hydramnios'] == "1") { ?>
                                                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                                                <?php } else { ?>
                                                                    <input type="checkbox" disabled="disabled" />
                                                                <?php } ?>  
                                                            </td>
                                                        </tr>
                                                        <tr>    
                                                            <td></td>
                                                            <td class="test">Exc&egrave;s LA</td>
                                                            <td>
                                                                <?php if ($birth_details['excess'] == "1") { ?>
                                                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                                                <?php } else { ?>
                                                                    <input type="checkbox" disabled="disabled" />
                                                                <?php } ?> 
                                                            </td>
                                                        </tr>
                                                        <tr>  
                                                            <td></td>
                                                            <td class="test">Normal</td>
                                                            <td>
                                                                <?php if ($birth_details['normal'] == "1") { ?>
                                                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                                                <?php } else { ?>
                                                                    <input type="checkbox" disabled="disabled" />
                                                                <?php } ?>  
                                                            </td>
                                                        </tr>
                                                        <tr>  
                                                            <td></td>
                                                            <td class="test">Oligoamnios</td>
                                                            <td>
                                                                <?php if ($birth_details['oligoamnios'] == "1") { ?>
                                                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                                                <?php } else { ?>
                                                                    <input type="checkbox" disabled="disabled" />
                                                                <?php } ?>    
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><span style="color:red">* </span>ពណ៌ទឹកភ្លោះ</td>                                                                            
                                                        </tr>
                                                        <tr>   
                                                            <td></td>
                                                            <td class="test">Blanch&acirc;tre</td>
                                                            <td>
                                                                <?php if ($birth_details['whitish'] == "1") { ?>
                                                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                                                <?php } else { ?>
                                                                    <input type="checkbox" disabled="disabled" />
                                                                <?php } ?> 
                                                            </td>
                                                        </tr>
                                                        <tr>    
                                                            <td></td>
                                                            <td class="test">Clair</td>
                                                            <td>
                                                                <?php if ($birth_details['clear'] == "1") { ?>
                                                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                                                <?php } else { ?>
                                                                    <input type="checkbox" disabled="disabled" />
                                                                <?php } ?>   
                                                            </td>
                                                        </tr>
                                                        <tr> 
                                                            <td></td>
                                                            <td class="test">Verd&acirc;tre</td>
                                                            <td>
                                                                <?php if ($birth_details['greenish'] == "1") { ?>
                                                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                                                <?php } else { ?>
                                                                    <input type="checkbox" disabled="disabled" />
                                                                <?php } ?>    
                                                            </td>  
                                                            <td><input type="hidden" name="data[MidWifeService][mid_wife_dossier_medical_id]" value="<?php echo $dossierMedical['MidWifeDossierMedical']['id']; ?>" /></td>                                      
                                                        </tr>                                                              
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr align="center">                                                           
                                                <td class="birth_child">BCF</td>
                                                <td class="birth_child">PDF</td>
                                                <td class="birth_child">Col</td>                                                            
                                                <td class="birth_child">TA</td>
                                                <td class="birth_child">Pouls</td>
                                                <td class="birth_child">T&#730</td>                                                            
                                            </tr>
                                            <?php
                                            $query_birth = mysql_query("SELECT * FROM mid_wife_births WHERE mid_wife_dossier_medical_id =" . $dossierMedical['MidWifeDossierMedical']['id']);
                                            while ($births = mysql_fetch_array($query_birth)) {
                                                $birth = $births['id'];
                                                ?>
                                                <tr align="center">
                                                    <td height="20px"><?php echo $births['time'] ?></td>
                                                    <td><?php echo $births['bcf'] ?></td>
                                                    <td><?php echo $births['pdf'] ?></td>
                                                    <td><?php echo $births['col'] ?></td>
                                                    <td><?php echo $births['ta'] ?></td>
                                                    <td><?php echo $births['pouls'] ?></td>
                                                    <td><?php echo $births['temperature'] ?></td>                                                            
                                                </tr>
                                            <?php } ?>
                                        </table>
                                    </div>                                              
                                </div>
                            <?php } ?><br>
                        <?php if($allowMidWifeAddNewTracking){ ?>
                            <div class="buttons">
                                <button class="btnAddNewTracking" rel="<?php echo $dossierMedical['MidWifeDossierMedical']['id']; ?>">
                                    <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
                                    <span class="txtSave"><?php echo 'បញ្ចូលការតាមដាន'; ?></span>
                                </button>
                            </div>
                        <?php } ?>
                    </fieldset>
                </fieldset>
                
<!--end data tracking-->
<!--data accouchement-->
                <fieldset>                                             
                    <legend><?php echo 'ការសំរាលកូន(Accouchement)'; ?></legend>   
                    <?php
                        $query = mysql_query("SELECT * FROM mid_wife_accouchements WHERE is_active=1 AND mid_wife_dossier_medical_id=" . $dossierMedical['MidWifeDossierMedical']['id']);
                        while ($data = mysql_fetch_array($query)) {
                            ?>
                            <div class="accordionDetail"> 
                                <h3>
                                    <a href="#" rel="<?php echo $data['id'];?>">
                                        <?php echo $data['created']?>
                                        <?php if($allowMidWifeEditAccouchement){ ?>
                                        <div style="float:right;">
                                            <img alt="" rel="<?php echo $data['id'];?>" src="<?php echo $this->webroot; ?>img/action/edit.png" class="btnEditAccouchement" name="<?php echo '1'; ?>" onmouseover="Tip('<?php echo ACTION_EDIT; ?>')"  />
                                        </div>
                                        <?php } ?>
                                        <div style="clear: both;"></div>
                                    </a>
                                </h3>
                                <div>
                                    <table class="defualtTable" style="width:100%;">
                                        <tr​​>
                                            <td width="86px"><span style="color:red">* </span>ការសំរាលកូន</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td style="width:15%;"> សំរាលកូនធម្មតាគ្រប់ខែ</td>
                                            <td style="width:15%;">
                                                <?php if ($data['acconchem_rerme'] == "1") { ?>
                                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                                <?php } else { ?>
                                                    <input type="checkbox" disabled="disabled" />
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td> ការសំរាលកូនមិនគ្រប់ខែ</td>
                                            <td>
                                                <?php if ($data['accon_chement'] == "1") { ?>
                                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                                <?php } else { ?>
                                                    <input type="checkbox" disabled="disabled" />
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td> សំរាលកូនពិបាក</td>
                                            <td>
                                                <?php if ($data['anormat'] == "1") { ?>
                                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                                <?php } else { ?>
                                                    <input type="checkbox" disabled="disabled" />
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td> បូម</td>
                                            <td>
                                                <?php if ($data['acc_par_ventonse'] == "1") { ?>
                                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                                <?php } else { ?>
                                                    <input type="checkbox" disabled="disabled" />
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td> វះកាត់</td>
                                            <td>
                                                <?php if ($data['caesarean'] == "1") { ?>
                                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                                <?php } else { ?>
                                                    <input type="checkbox" disabled="disabled" />
                                                <?php } ?>
                                            </td>
                                            <td>Vg :</td>
                                            <td><?php echo $data['g']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>កើតនៅម៉ោង</td>
                                            <td><?php echo $data['time1']; ?></td>
                                            <td><?php echo 'ថ្ងៃ ខែ ឆ្នាំ កំណើត'; ?></td>
                                            <td><?php echo $data['dob']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Girl 
                                                <?php if ($data['girl'] == "1") { ?>
                                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                                <?php } else { ?>
                                                    <input type="checkbox" disabled="disabled" />
                                                <?php } ?>
                                            </td>
                                            <td>Boy 
                                                <?php if ($data['boy'] == "1") { ?>
                                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                                <?php } else { ?>
                                                    <input type="checkbox" disabled="disabled" />
                                                <?php } ?>
                                            </td>
                                            <td><?php echo 'ទំងន់' ?></td>
                                            <td><?php echo $data['weight'] . ' Kg'; ?></td>
                                            <td>ប្រវែង</td>
                                            <td><?php echo $data['long'] . ' Cm'; ?></td>
                                            <td>បរិមាត្រក្បាល</td>
                                            <td><?php echo $data['head_size']; ?></td> 
                                        </tr>
                                        <tr>
                                            <td>Score d'Apgar:</td>                                                            
                                            <td colspan="3">
                                                <table border="1" style="width:100%;">
                                                    <tr align="center">
                                                        <td width="35px" height="25px">1នាទី</td>
                                                        <td width="35px" height="25px">5នាទី</td>
                                                        <td width="35px" height="25px">10នាទី</td>
                                                    </tr>
                                                    <tr>
                                                        <td width="35px" height="25px"><?php echo $data['one_minute']; ?></td>
                                                        <td><?php echo $data['five_minute']; ?></td>
                                                        <td><?php echo $data['ten_minute']; ?></td>
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
                                            <td>Good</td>
                                            <td>
                                                <?php if ($data['good'] == "1") { ?>
                                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                                <?php } else { ?>
                                                    <input type="checkbox" disabled="disabled" />
                                                <?php } ?>
                                            </td>
                                            <td>Not Good</td>
                                            <td>
                                                <?php if ($data['not_good'] == "1") { ?>
                                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                                <?php } else { ?>
                                                    <input type="checkbox" disabled="disabled" />
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>បរិមាណឈាម</td>
                                            <td>Little</td>
                                            <td>
                                                <?php if ($data['little'] == "1") { ?>
                                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                                <?php } else { ?>
                                                    <input type="checkbox" disabled="disabled" />
                                                <?php } ?>
                                            </td>
                                            <td>Much</td>
                                            <td>
                                                <?php if ($data['much'] == "1") { ?>
                                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                                <?php } else { ?>
                                                    <input type="checkbox" disabled="disabled" />
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td colspan="2">(<300ml),(>300ml)</td>                                                            
                                        </tr>
                                    </table>
                                </div>                                        
                            </div>           
                        <?php } ?><br>
                        <?php if($allowMidWifeAddNewAccouchement){ ?>
                            <div class="buttons">
                                <button class="btnAddNewAccouchement" rel="<?php echo $dossierMedical['MidWifeDossierMedical']['id']; ?>">
                                    <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
                                    <span class="txtSave"><?php echo 'បញ្ចូលការសំរាលកូន'; ?></span>
                                </button>
                            </div>
                        <?php } ?>
                </fieldset>
<!--end data accouchement-->
<!--data deliverance-->
                <fieldset>                                            
                    <legend><?php echo 'ការទំលាក់សុក(D&egrave;livrance)'; ?></legend> 
                    <?php
                        $k = 1;
                        $query = mysql_query("SELECT * FROM mid_wife_deliverances WHERE is_active=1 AND mid_wife_dossier_medical_id=" . $dossierMedical['MidWifeDossierMedical']['id']);
                        while ($deliverance = mysql_fetch_array($query)) {
                            ?> 
                            <div class="accordionDetail">
                                <h3>
                                    <a href="#" rel="<?php echo $deliverance['id'];?>">
                                        <?php echo $deliverance['created']?>
                                        <?php if($allowMidWifeEditDeliverance){ ?>
                                        <div style="float:right;">
                                            <img alt="" rel="<?php echo $deliverance['id'];?>" src="<?php echo $this->webroot; ?>img/action/edit.png" class="btnEditDeliverance" name="<?php echo '1'; ?>" onmouseover="Tip('<?php echo ACTION_EDIT; ?>')"  />
                                        </div>
                                        <div style="clear: both;"></div>
                                        <?php } ?>
                                    </a>
                                </h3>
                                <div>
                                    <table class="defualtTable1">
                                        <tr>
                                            <td​​></td>
                                            <td width="120px">ម៉ោងទំលាក់សុក</td>
                                            <td><?php echo $deliverance['time'] ?></td>
                                            <td>ទំងន់សុក:</td>
                                            <td><?php echo $deliverance['weight'] . ' Kg' ?></td>                                                            
                                        </tr>
                                        <tr>
                                            <td>បែប</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>BEAUDELAUQUE</td>
                                            <td>
                                                <?php if ($deliverance['beaudelauque'] == "1") { ?>
                                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                                <?php } else { ?>
                                                    <input type="checkbox" disabled="disabled" />
                                                <?php } ?>
                                            </td>
                                            <td>DUNCAN</td>
                                            <td>
                                                <?php if ($deliverance['duncan'] == "1") { ?>
                                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                                <?php } else { ?>
                                                    <input type="checkbox" disabled="disabled" />
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>ទំលាក់សុកដោយ</td>                                                            
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>ត្រួតពិនិត្យ</td>
                                            <td>
                                                <?php if ($deliverance['check'] == "1") { ?>
                                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                                <?php } else { ?>
                                                    <input type="checkbox" disabled="disabled" />
                                                <?php } ?>
                                            </td>
                                            <td>បែបធម្មជាតិ</td>
                                            <td>
                                                <?php if ($deliverance['natural'] == "1") { ?>
                                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                                <?php } else { ?>
                                                    <input type="checkbox" disabled="disabled" />
                                                <?php } ?>
                                            </td>
                                            <td colspan="5">បារទំលាក់ដោយដៃ</td>
                                            <td>
                                                <?php if ($deliverance['by_hand'] == "1") { ?>
                                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                                <?php } else { ?>
                                                    <input type="checkbox" disabled="disabled" />
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>ការបារសំអាតស្បូន</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>មាន</td>
                                            <td>
                                                <?php if ($deliverance['have'] == "1") { ?>
                                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                                <?php } else { ?>
                                                    <input type="checkbox" disabled="disabled" />
                                                <?php } ?>
                                            </td>
                                            <td colspan="5">គ្មាន</td>
                                            <td>
                                                <?php if ($deliverance['no_have'] == "1") { ?>
                                                    <input type="checkbox" disabled="disabled" checked="checked" />
                                                <?php } else { ?>
                                                    <input type="checkbox" disabled="disabled" />
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>                                        
                            </div>
                        <?php } ?><br>
                        <?php if($allowMidWifeAddNewDeliverance){ ?>
                            <div class="buttons">
                                <button class="btnAddNewDeliverance" rel="<?php echo $dossierMedical['MidWifeDossierMedical']['id']; ?>">
                                    <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
                                    <span class="txtSave"><?php echo 'បញ្ចូលការទំលាក់សុក'; ?></span>
                                </button>
                            </div>
                        <?php } ?>
                </fieldset>
<!--end data diliverance-->
<!--data first time and next time-->
                <fieldset>                                   
                    <legend><?php echo 'ការតាមដានក្រោយសំរាល(កំឡុងពេល 2H)'; ?></legend>  
                    <div class="accordionDetail"> 
                        <h3>
                            <a href="#" rel="<?php echo $deliverance['id'];?>">
                                <?php echo "ការតាមដានក្រោយសំរាល"; ?>
                                <div style="clear: both;"></div>
                            </a>
                        </h3>
                        <div>
                            <table style="width:100%;">
                                <tr>
                                    <td style="width: 45%;" valign="top">
                                        <table class="defualtTable" border="1px" style="width: 100%;">                                                
                                            <tr align="center">
                                                <td colspan="5">1 ម៉ោងដំបូង​(រៀងរាល់​ 15នាទី)
                                                    <a href="#" rel="<?php echo $dossierMedical['MidWifeDossierMedical']['id'];?>">
                                                        <?php if($allowMidWifeEditAccouchementFirst){ ?>
                                                        <div style="float:right;">
                                                            <img alt="" rel="<?php echo $dossierMedical['MidWifeDossierMedical']['id'];?>" src="<?php echo $this->webroot; ?>img/action/edit.png" class="btnEditFirstTime" name="<?php echo '1'; ?>" onmouseover="Tip('<?php echo ACTION_EDIT; ?>')"  />
                                                        </div>
                                                        <?php } ?>
                                                    </a>
                                                </td>                                                                    
                                            </tr>
                                            <tr align="center">
                                                <td>ម៉ោង</td>
                                                <td>ធ្លាក់ឈាម</td>
                                                <td>TA</td>
                                                <td>P</td>
                                                <td>T&#730</td>                                                                   
                                            </tr>
                                            <?php
                                            $query_first_accouchement = mysql_query("SELECT * FROM mid_wife_accouchement_first_times WHERE is_active=1 AND mid_wife_dossier_medical_id=" . $dossierMedical['MidWifeDossierMedical']['id']);
                                            while ($first_accouchement = mysql_fetch_array($query_first_accouchement)) {
                                                ?> 
                                                <tr align="center">
                                                    <td height="20px"><?php echo $first_accouchement['time'] ?></td>
                                                    <td><?php echo $first_accouchement['first_blood'] ?></td>
                                                    <td><?php echo $first_accouchement['first_ta'] ?></td>
                                                    <td><?php echo $first_accouchement['first_p'] ?></td>
                                                    <td><?php echo $first_accouchement['first_temperature'] ?></td>                                                                                                                                 
                                                </tr>
                                            <?php } ?>                                                                                                                                                     
                                        </table>  
                                    </td>
                                    <td style="width: 45%;" valign="top">
                                        <table class="defualtTable" border="1px" style="width: 100%;">                                                
                                            <tr align="center">                                                                    
                                                <td colspan="5">ម៉ោងបន្ទាប់(រៀងរាល់​ 30នាទី)
                                                    <a href="#" rel="<?php echo $dossierMedical['MidWifeDossierMedical']['id'];?>">
                                                        <?php if($allowMidWifeEditAccouchementNext){ ?>
                                                        <div style="float:right;">
                                                            <img alt="" rel="<?php echo $dossierMedical['MidWifeDossierMedical']['id'];?>" src="<?php echo $this->webroot; ?>img/action/edit.png" class="btnEditNextTime" name="<?php echo '1'; ?>" onmouseover="Tip('<?php echo ACTION_EDIT; ?>')"  />
                                                        </div>
                                                        <?php } ?>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr align="center">                                                                    
                                                <td>ម៉ោង</td>
                                                <td>ធ្លាក់ឈាម</td>
                                                <td>TA</td>
                                                <td>P</td>
                                                <td>T&#730</td>
                                            </tr>
                                            <?php
                                            $query_next_accouchement = mysql_query("SELECT * FROM mid_wife_accouchement_next_times WHERE is_active=1 AND mid_wife_dossier_medical_id=" . $dossierMedical['MidWifeDossierMedical']['id']);
                                            while ($next_accouchement = mysql_fetch_array($query_next_accouchement)) {
                                                ?> 
                                                <tr align="center">
                                                    <td height="20px"><?php echo $next_accouchement['next_time'] ?></td>
                                                    <td><?php echo $next_accouchement['next_blood'] ?></td>
                                                    <td><?php echo $next_accouchement['next_ta'] ?></td>
                                                    <td><?php echo $next_accouchement['next_p'] ?></td>
                                                    <td><?php echo $next_accouchement['next_temperature'] ?></td>                                                                                                                                 
                                                </tr>
                                            <?php } ?>
                                        </table>                                                          
                                    </td>
                                </tr>
                            </table>
                            <?php
                            $query_allaitements = mysql_query("SELECT * FROM mid_wife_allaitements WHERE is_active<=2 AND mid_wife_dossier_medical_id=" . $dossierMedical['MidWifeDossierMedical']['id']);
                            while ($query_allaitement = mysql_fetch_array($query_allaitements)) {
                                ?>
                                <table>
                                    <tr>
                                        <td width="180px"><span style="color: red">* </span>ការបំបៅដោះ(Allaitement)</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>ភ្លាមៗ</td>
                                        <td>
                                            <?php if ($query_allaitement['soon'] == "1") { ?>
                                                <input type="checkbox" disabled="disabled" checked="checked" />
                                            <?php } else { ?>
                                                <input type="checkbox" disabled="disabled" />
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>2H ក្រោយកើត</td>
                                        <td>
                                            <?php if ($query_allaitement['two_houre_after'] == "1") { ?>
                                                <input type="checkbox" disabled="disabled" checked="checked" />
                                            <?php } else { ?>
                                                <input type="checkbox" disabled="disabled" />
                                            <?php } ?>
                                        </td>
                                    </tr>
                                </table>
                            <?php } ?>
                        </div>                                        
                    </div><br>
                    <?php if($allowMidWifeAddNewAccouchementFirst){ ?>
                    <div class="buttons">
                        <button class="btnAddNewFirstTime" rel="<?php echo $dossierMedical['MidWifeDossierMedical']['id']; ?>">
                            <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
                            <span class="txtSave"><?php echo 'បញ្ចូលមួយម៉ោងដំបូង'; ?></span>
                        </button>
                    </div>
                    <?php
                    }
                    if($allowMidWifeAddNewAccouchementNext){
                    ?>
                    <div class="buttons">
                        <button class="btnAddNewNextTime" rel="<?php echo $dossierMedical['MidWifeDossierMedical']['id']; ?>">
                            <img src="<?php echo $this->webroot; ?>img/icon/plus_red.png" alt=""/>
                            <span class="txtSave"><?php echo 'បញ្ចូលម៉ោងបន្ទាប់'; ?></span>
                        </button>
                    </div>
                    <?php } ?>
                </fieldset>
            </div>
            <?php } ?>
        </div><br>
        <?php if($allowMidWifeAddNewDossierMedical){ ?>
        <div class="buttons">
            <button class="btnAddNew" rel="<?php echo $this->params['pass'][0]; ?>">
                <img src="<?php echo $this->webroot; ?>img/icon/plus.png" alt=""/>
                <span class="txtSave"><?php echo 'ADD DOSSIER MEDICAL'; ?></span>
            </button>
        </div>
        <?php } ?>
    </fieldset>
    <div class="clear"></div>
    <?php echo $this->Form->end(); ?>
</div>

