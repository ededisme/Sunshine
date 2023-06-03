<?php
echo $this->element('prevent_multiple_submit');
$absolute_url = FULL_BASE_URL . Router::url("/", false);
echo $javascript->link('uninums.min');
?>
<script type="text/javascript">
    $(document).ready(function(){
        $("#MidWifeServiceEntreLe,#MidWifeServiceSortieLe" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
            yearRange: '-100:-0'
        }).unbind("blur");
        // Prevent Key Enter
        preventKeyEnter();
        $("#MidWifeServiceEditDossierMedicalForm").validationEngine();
        $("#MidWifeServiceEditDossierMedicalForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveMidWifeService").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $(".btnBackMidWifeServiceEditDossierMedical").click();
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
        
        $(".btnBackMidWifeServiceEditDossierMedical").click(function(event){
            event.preventDefault();
            var queueId=$('#queueId').val();
            var id = $(this).attr('rel');
            $('#dataDossierMedical').html("<?php echo ACTION_LOADING; ?>");
            $('#dataDossierMedical').load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/addMidWifeServiceDoctorDossierMedical/"+queueId);
        });
    });
    // end document
    
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="#" class="positive btnBackMidWifeServiceEditDossierMedical" rel="<?php echo $this->params['pass'][0];?>">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('MidWifeService',array('enctype' => 'multipart/form-data')); ?>
<?php echo $this->Form->input('id',array('type'=>'hidden')); ?>
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
            </tr>
        </table>
    </div>
</fieldset><br> 

<fieldset>
    <legend><?php __(GENERAL_REQUEST); ?></legend>
    <table style="width: 100%;" cellspacing="0">
        <tr>
            <td>
                <?php 
                    $queryDataFromDoctor=  mysql_query("SELECT mwsreq.*,mwsreq.id as id FROM mid_wife_service_requests as mwsreq "
                            . "INNER JOIN other_service_requests as osreq ON osreq.id=mwsreq.other_service_request_id "
                            . "INNER JOIN queued_doctors as qd ON qd.id=osreq.queued_doctor_id "
                            . "INNER JOIN queues as q ON q.id=qd.queue_id WHERE osreq.is_active=1 AND queue_id=".$patient['MidWifeDossierMedical']['queue_id']);
                    $dataRequest=  mysql_fetch_array($queryDataFromDoctor);
                    echo $dataRequest['mid_wife_description'];
                ?>
                <input type="hidden" value="<?php echo $dataRequest['id']; ?>" name="data[MidWifeServiceRequest][id]">
                <input type="hidden" value="<?php echo $patient['Queue']['id']; ?>" name="data[Queue][id]" id="queueId">
                <input type="hidden" value="<?php echo $patient['MidWifeDossierMedical']['id']; ?>" name="data[MidWifeDossierMedical][id]">
            </td>
        </tr>
    </table>      
</fieldset>
<br/>
<fieldset>
    <legend><?php __(MENU_MID_WIFE_SERVICE_INFO); ?></legend>
    <fieldset>
        <legend><?php echo 'ស្ថានភាពអ្នកជំងឺ'; ?></legend>  
        <table class="defualtTableNew">                                
            <tr>
                <td><?php echo 'Entr&eacute;e le'. '<span class="red">*</span>'; ?></td>
                <td>
                    <?php echo $this->Form->input('entre_le', array('tabindex' => '1','value'=>$patient['MidWifeDossierMedical']['entre_le'], 'label' => FALSE , 'class' => 'validate[required]')); ?>                                        
                </td>                                    
            </tr>
            <tr>
                <td><?php echo "Diagnostic d'entr&eacute;e"; ?></td>
                <td>
                    <?php echo $this->Form->input('doagnostic_entre', array('tabindex' => '1','value'=>$patient['MidWifeDossierMedical']['doagnostic_entre'], 'label' => FALSE)); ?>
                </td>                                    
            </tr> 
            <tr>
                <td><?php echo 'Sotrie le'. '<span class="red">*</span>'; ?></td>
                <td>
                    <?php echo $this->Form->input('sortie_le', array('tabindex' => '1','value'=>$patient['MidWifeDossierMedical']['sortie_le'], 'label' => FALSE , 'class' => 'validate[required]')); ?>                                        
                </td>                                    
            </tr>
            <tr>
                <td style="width:10%;"><?php echo "Diagnostic de sortie"; ?></td>
                <td>
                    <?php echo $this->Form->input('doagnostic_sortie', array('tabindex' => '1','value'=>$patient['MidWifeDossierMedical']['doagnostic_sortie'], 'label' => FALSE)); ?>
                </td>                                    
            </tr>   
            <tr>
                <td>PARA</td>
            </tr>
            <tr>
                <td colspan="2">
                    <table border="0px">
                        <tr>
                            <td width="170px"><input type="text" name="data[MidWifeService][acconchement_rerme]" value="<?php echo $patient['MidWifeDossierMedical']['acconchement_rerme']; ?>"/></td>
                            <td width="170px"><input type="text" name="data[MidWifeService][accon_chement]" value="<?php echo $patient['MidWifeDossierMedical']['accon_chement']; ?>"/></td>
                            <td width="170px"><input type="text" name="data[MidWifeService][avortment_inv]" value="<?php echo $patient['MidWifeDossierMedical']['avortment_inv']; ?>"/></td>
                            <td width="170px"><input type="text" name="data[MidWifeService][baby]" value="<?php echo $patient['MidWifeDossierMedical']['baby']; ?>"/></td>
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
                    <?php
                    if($patient['MidWifeDossierMedical']['caesarean']==1){
                        echo '<input type="checkbox" value="1" name="data[MidWifeService][caesarean]" checked="True"/>';
                    }else{
                        echo '<input type="checkbox" value="1" name="data[MidWifeService][caesarean]" />';
                    }
                    ?>
                </td>
            </tr>
            <tr>                                
                <td class="test">+ H&eacute;morragie...</td>
                <td>
                    <?php
                    if($patient['MidWifeDossierMedical']['hemorrhage']==1){
                        echo '<input type="checkbox" value="1" name="data[MidWifeService][hemorrhage]" checked="True"/>';
                    }else{
                        echo '<input type="checkbox" value="1" name="data[MidWifeService][hemorrhage]" />';
                    }
                    ?>
                </td>
            </tr>                                    
            <tr>                                        
                <td class="test">+ Hypertension...</td>
                <td>
                    <?php
                    if($patient['MidWifeDossierMedical']['hypertension']==1){
                        echo '<input type="checkbox" value="1" name="data[MidWifeService][hypertension]" checked="True"/>';
                    }else{
                        echo '<input type="checkbox" value="1" name="data[MidWifeService][hypertension]" />';
                    }
                    ?>
                </td>
            </tr>
            <tr>                                
                <td class="test">+ Cardiopathie...</td>
                <td>
                    <?php
                    if($patient['MidWifeDossierMedical']['heart']==1){
                        echo '<input type="checkbox" value="1" name="data[MidWifeService][heart]" checked="True"/>';
                    }else{
                        echo '<input type="checkbox" value="1" name="data[MidWifeService][heart]" />';
                    }
                    ?>
                </td>
            </tr>  
            <tr>
                <td><?php echo "Other"; ?></td>
                <td>
                    <?php echo $this->Form->input('other', array('tabindex' => '1', 'label' => FALSE,'value'=>$patient['MidWifeDossierMedical']['other'])); ?>                                        
                </td>
            </tr>
        </table>                                   
    </fieldset>

    <fieldset>
        <legend><?php echo "Examen &agrave; l'entr&eacute;e"; ?></legend>
        <table class="defualtTableNew"> 
            <tr>
                <td><?php echo "TA / CmHg"; ?></td>
                <td>
                    <?php echo $this->Form->input('ta', array('tabindex' => '1', 'label' => FALSE,'value'=>$patient['MidWifeDossierMedical']['ta'])); ?>                                        
                </td>
                <td><?php echo "P / mn"; ?></td>
                <td>
                    <?php echo $this->Form->input('p', array('tabindex' => '1', 'label' => FALSE,'value'=>$patient['MidWifeDossierMedical']['p'])); ?>                                        
                </td>
                <td><?php echo "T&#730 / &#8451"; ?></td>
                <td>
                    <?php echo $this->Form->input('t', array('tabindex' => '1', 'label' => FALSE,'value'=>$patient['MidWifeDossierMedical']['t'])); ?>                                        
                </td>
            </tr>
            <tr>
                <td><?php echo "Pr&eacute;sentation"; ?></td>
                <td>
                    <?php echo $this->Form->input('presentation', array('tabindex' => '1', 'label' => FALSE,'value'=>$patient['MidWifeDossierMedical']['presentation'])); ?>                                        
                </td>
            </tr>
            <tr>
                <td><?php echo "H.U / Cm"; ?></td>
                <td>
                    <?php echo $this->Form->input('hu', array('tabindex' => '1', 'label' => FALSE,'value'=>$patient['MidWifeDossierMedical']['hu'])); ?>                                        
                </td>
            </tr>
            <tr>
                <td><?php echo "BCF / mn"; ?></td>
                <td>
                    <?php echo $this->Form->input('bcf', array('tabindex' => '1', 'label' => FALSE,'value'=>$patient['MidWifeDossierMedical']['bcf'])); ?>                                        
                </td>
            </tr>
            <tr>
                <td><?php echo "Col / Cm"; ?></td>
                <td>
                    <?php echo $this->Form->input('col', array('tabindex' => '1', 'label' => FALSE,'value'=>$patient['MidWifeDossierMedical']['col'])); ?>                                        
                </td>
            </tr>
            <tr>
                <td><?php echo "PDE"; ?></td>
                <td>
                    <?php echo $this->Form->input('pde', array('tabindex' => '1', 'label' => FALSE,'value'=>$patient['MidWifeDossierMedical']['pde'])); ?>                                        
                </td>
            </tr>
            <tr>
                <td><?php echo "OEd&egrave;me (+)or(-)"; ?></td>
                <td>
                    <?php echo $this->Form->input('edema', array('tabindex' => '1', 'label' => FALSE,'value'=>$patient['MidWifeDossierMedical']['edema'])); ?>                                        
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