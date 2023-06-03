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
        $("#MidWifeServiceEditDeliveranceForm").validationEngine();
        $("#MidWifeServiceEditDeliveranceForm").ajaxForm({
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
        <legend><?php echo 'ការទំលាក់សុក(D&egrave;livrance)'; ?></legend>                                    
        <div class="accordionDetail">   
            <div>
                <table class="defualtTable">
                    <tr>   
                        <td><?php echo 'Time'. '<span class="red">*</span>'; ?></td>
                        <td>
                            <?php echo $this->Form->input('time', array('tabindex' => '1', 'label' => FALSE, 'class' => 'validate[required]', 'value'=> $patient['MidWifeDeliverance']['time'])); ?>
                        </td>                                                                             
                    </tr>
                    <tr>
                        <td><?php echo 'ទំងន់សុក'. '<span class="red">*</span>'; ?></td>
                        <td><?php echo $this->Form->input('weight', array('tabindex' => '1', 'label' => FALSE, 'class' => 'validate[required]', 'value'=> $patient['MidWifeDeliverance']['weight'])); ?></td>                                    
                        <td class="empty">Kg</td>
                    </tr>
                    <tr>
                        <td>បែប</td>
                    </tr>
                    <tr>                                          
                        <td class="test">BEAUDELAUQUE</td>  
                        <td>
                            <?php if ($patient['MidWifeDeliverance']['beaudelauque'] == "1") { ?>                                        
                                <input align="left" type="checkbox" value="1"  name="data[MidWifeService][beaudelauque]" checked="checked" />  
                            <?php } else { ?>
                                <input type="checkbox" value="1"  name="data[MidWifeService][beaudelauque]"/>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>                                    
                        <td class="test">DUNCAN</td>
                        <td>
                            <?php if ($patient['MidWifeDeliverance']['duncan'] == "1") { ?>                                        
                                <input align="left" type="checkbox" value="1"  name="data[MidWifeService][duncan]" checked="checked" />  
                            <?php } else { ?>
                                <input type="checkbox" value="1"  name="data[MidWifeService][duncan]"/>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td>ទំលាក់សុកដោយ</td>                                                            
                    </tr>
                    <tr>                                         
                        <td class="test">ត្រួតពិនិត្យ</td>
                        <td>
                            <?php if ($patient['MidWifeDeliverance']['check'] == "1") { ?>                                        
                                <input align="left" type="checkbox" value="1"  name="data[MidWifeService][check]" checked="checked" />  
                            <?php } else { ?>
                                <input type="checkbox" value="1"  name="data[MidWifeService][check]"/>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>                                  
                        <td class="test">បែបធម្មជាតិ</td>
                        <td>
                            <?php if ($patient['MidWifeDeliverance']['natural'] == "1") { ?>                                        
                                <input align="left" type="checkbox" value="1"  name="data[MidWifeService][natural]" checked="checked" />  
                            <?php } else { ?>
                                <input type="checkbox" value="1"  name="data[MidWifeService][natural]"/>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>                                    
                        <td class="test">បារទំលាក់ដោយដៃ</td>
                        <td>
                            <?php if ($patient['MidWifeDeliverance']['by_hand'] == "1") { ?>                                        
                                <input align="left" type="checkbox" value="1"  name="data[MidWifeService][by_hand]" checked="checked" />  
                            <?php } else { ?>
                                <input type="checkbox" value="1"  name="data[MidWifeService][by_hand]"/>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td>ការបារសំអាតស្បូន</td>
                    </tr>
                    <tr>                                    
                        <td class="test">មាន</td>
                        <td>
                            <?php if ($patient['MidWifeDeliverance']['have'] == "1") { ?>                                        
                                <input align="left" type="checkbox" value="1"  name="data[MidWifeService][have]" checked="checked" />  
                            <?php } else { ?>
                                <input type="checkbox" value="1"  name="data[MidWifeService][have]"/>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="test">គ្មាន</td>
                        <td>
                            <?php if ($patient['MidWifeDeliverance']['no_have'] == "1") { ?>                                        
                                <input align="left" type="checkbox" value="1"  name="data[MidWifeService][no_have]" checked="checked" />  
                            <?php } else { ?>
                                <input type="checkbox" value="1"  name="data[MidWifeService][no_have]"/>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>                  
                        <td><input type="hidden" name="data[MidWifeService][mid_wife_dossier_medical_id]" value="<?php echo $patient['MidWifeDossierMedical']['id']; ?>" /></td>                                      
                        <td><input type="hidden" name="data[MidWifeService][deliverance_id]" value="<?php echo $patient['MidWifeDeliverance']['id'] ?>" /></td>  
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