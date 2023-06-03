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
        $("#MidWifeServiceEditAccouchementForm").validationEngine();
        $("#MidWifeServiceEditAccouchementForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveMidWifeService").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $(".btnBackMidWifeServiceEditAccouchement").click();
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
        
        $(".btnBackMidWifeServiceEditAccouchement").click(function(event){
            event.preventDefault();
            var queueId=$('#queueId').val();
            var id = $(this).attr('rel');
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
    // end document
    
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="#" class="positive btnBackMidWifeServiceEditAccouchement" rel="<?php echo $this->params['pass'][0];?>">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('MidWifeService',array('enctype' => 'multipart/form-data')); ?>
<?php echo $this->Form->input('id',array('type'=>'hidden')); ?>
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
        <input type="hidden" value="<?php echo $patient['Queue']['id']; ?>" name="data[Queue][id]" id="queueId">
        <input type="hidden" value="<?php echo $patient['MidWifeDossierMedical']['id']; ?>" name="data[MidWifeDossierMedical][id]">
    </div>
</fieldset><br> 
<br/>
<fieldset>
    <legend><?php __(MENU_MID_WIFE_SERVICE_INFO); ?></legend>
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
                        <td style="width:19%;">
                            <?php if ($patient['MidWifeAccouchement']['acconchem_rerme'] == "1") { ?>                                        
                                <input align="left" type="checkbox" value="1"  name="data[MidWifeService][acconchem_rerme]" checked="checked" />  
                            <?php } else { ?>
                                <input type="checkbox" value="1"  name="data[MidWifeService][acconchem_rerme]"/>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><?php echo 'កូនកើតមិនគ្រប់ខែ'; ?></td>
                        <td>
                            <?php if ($patient['MidWifeAccouchement']['accon_chement'] == "1") { ?>                                        
                                <input align="left" type="checkbox" value="1"  name="data[MidWifeService][accon_chement]" checked="checked" />  
                            <?php } else { ?>
                                <input type="checkbox" value="1"  name="data[MidWifeService][accon_chement]"/>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td width="170px"> <?php echo 'សំរាលកូនពិបាក'; ?></td>
                        <td>
                            <?php if ($patient['MidWifeAccouchement']['anormat'] == "1") { ?>                                        
                                <input align="left" type="checkbox" value="1"  name="data[MidWifeService][anormat]" checked="checked" />  
                            <?php } else { ?>
                                <input type="checkbox" value="1"  name="data[MidWifeService][anormat]"/>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td width="300px"> <?php echo 'បូម / នាក់'; ?> </td>
                        <td>
                            <?php if ($patient['MidWifeAccouchement']['acc_par_ventonse'] == "1") { ?>                                        
                                <input align="left" type="checkbox" value="1"  name="data[MidWifeService][acc_par_ventonse]" checked="checked" />  
                            <?php } else { ?>
                                <input type="checkbox" value="1"  name="data[MidWifeService][acc_par_ventonse]"/>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td> <?php echo 'វះកាត់ / នាក់ ';?></td>
                        <td>
                            <?php if ($patient['MidWifeAccouchement']['caesarean'] == "1") { ?>                                        
                                <input align="left" type="checkbox" value="1"  name="data[MidWifeService][caesarean]" checked="checked" />  
                            <?php } else { ?>
                                <input type="checkbox" value="1"  name="data[MidWifeService][caesarean]"/>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td><?php echo $this->Form->input('g', array('tabindex' => '1', 'label' => 'Vg ', 'value' => $patient['MidWifeAccouchement']['g'],'style'=>'width:100px;')); ?></td>
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
                            <?php echo $this->Form->text('time1', array('tabindex' => '4', 'style' => 'width: 50%;', 'value' => $patient['MidWifeAccouchement']['time1'])); ?>
                            <img alt="" id="btnEmptyTime1" src="<?php echo $this->webroot; ?>img/close.gif" style="cursor: pointer;" align="middle" />
                        </td>
                        <td>
                            <?php echo $this->Form->text('dob', array('tabindex' => '4', 'style' => 'width: 57%;', 'value' => $patient['MidWifeAccouchement']['dob'])); ?>                                       
                        </td>
                    </tr>
                    <tr>
                        <td>Girl                                         
                                <?php if ($patient['MidWifeAccouchement']['girl'] == "1") { ?>                                        
                                    <input align="left" type="checkbox" value="1"  name="data[MidWifeService][girl]" checked="checked" />  
                                <?php } else { ?>
                                    <input type="checkbox" value="1"  name="data[MidWifeService][girl]"/>
                                <?php } ?>
                        </td>
                        <td>Boy                                         
                                <?php if ($patient['MidWifeAccouchement']['boy'] == "1") { ?>                                        
                                    <input align="left" type="checkbox" value="1"  name="data[MidWifeService][boy]" checked="checked" />  
                                <?php } else { ?>
                                    <input type="checkbox" value="1"  name="data[MidWifeService][boy]"/>
                                <?php } ?>
                        </td>
                        <td>
                            <?php echo $this->Form->input('weight', array('tabindex' => '1', 'label' => 'ទំងន់' .' / Kg'. '<span class="red">* </span> ', 'class' => 'validate[required]', 'value' => $patient['MidWifeAccouchement']['weight'],'style'=>'width:100px;')); ?>
                        </td>                                    
                        <td>
                            <?php echo $this->Form->input('long', array('tabindex' => '1', 'label' => 'ប្រវែង' .' / Cm'. '<span class="red">* </span> ', 'class' => 'validate[required]', 'value' => $patient['MidWifeAccouchement']['long'],'style'=>'width:100px;')); ?>
                        </td>                                                                                                                                                            
                    </tr>
                    <tr>
                        <td>
                            <?php echo $this->Form->input('head_size', array('tabindex' => '1', 'label' => 'បរិមាត្រក្បាល' .' / Cm'. '<span class="red">*</span>', 'class' => 'validate[required]', 'value' => $patient['MidWifeAccouchement']['head_size'],'style'=>'width:100px;')); ?>
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
                                    <td width="15px" height="25px"><input type="text" name="data[MidWifeService][one_minute]" value="<?php echo $patient['MidWifeAccouchement']['one_minute']?>" /></td>
                                    <td><input type="text" name="data[MidWifeService][five_minute]" value="<?php echo $patient['MidWifeAccouchement']['five_minute']?>"/></td>
                                    <td><input type="text" name="data[MidWifeService][ten_minute]" value="<?php echo $patient['MidWifeAccouchement']['ten_minute']?>"/></td>
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
                        <td width="100px">Good                                        
                                <?php if ($patient['MidWifeAccouchement']['good'] == "1") { ?>                                        
                                    <input align="left" type="checkbox" value="1"  name="data[MidWifeService][good]" checked="checked" />  
                                <?php } else { ?>
                                    <input type="checkbox" value="1"  name="data[MidWifeService][good]"/>
                                <?php } ?>
                        </td>
                        <td>Not Good                                        
                                <?php if ($patient['MidWifeAccouchement']['not_good'] == "1") { ?>                                        
                                    <input align="left" type="checkbox" value="1"  name="data[MidWifeService][not_good]" checked="checked" />  
                                <?php } else { ?>
                                    <input type="checkbox" value="1"  name="data[MidWifeService][not_good]"/>
                                <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>បរិមាណឈាម</td>
                        <td>Little                                        
                                <?php if ($patient['MidWifeAccouchement']['little'] == "1") { ?>                                        
                                    <input align="left" type="checkbox" value="1"  name="data[MidWifeService][little]" checked="checked" />  
                                <?php } else { ?>
                                    <input type="checkbox" value="1"  name="data[MidWifeService][little]"/>
                                <?php } ?>
                        </td>
                        <td>Much                                        
                                <?php if ($patient['MidWifeAccouchement']['much'] == "1") { ?>                                        
                                    <input align="left" type="checkbox" value="1"  name="data[MidWifeService][much]" checked="checked" />  
                                <?php } else { ?>
                                    <input type="checkbox" value="1"  name="data[MidWifeService][much]"/>
                                <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td colspan="1" style="vertical-align: middle;text-align: center;">(<300ml),(>300ml)</td>                                                                                                
                    </tr>                                                                
                    <tr>                                     
                        <td><input type="hidden" name="data[MidWifeService][mid_wife_dossier_medical_id]" value="<?php echo $patient['MidWifeDossierMedical']['id']; ?>" /></td>                                                                          
                        <td><input type="hidden" name="data[MidWifeService][accouchement_id]" value="<?php echo $patient['MidWifeAccouchement']['id'] ?>" /></td>                                      
                    </tr>
                </table>
            </div>                                        
        </div>  
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
<?php echo $this->Form->end(); ?>