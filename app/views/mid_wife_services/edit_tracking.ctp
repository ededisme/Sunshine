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
        $("#MidWifeServiceEditTrackingForm").validationEngine();
        $("#MidWifeServiceEditTrackingForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveMidWifeService").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $(".btnBackMidWifeServiceEditTracking").click();
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
        
        $(".btnBackMidWifeServiceEditTracking").click(function(event){
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
        <a href="#" class="positive btnBackMidWifeServiceEditTracking" rel="<?php echo $this->params['pass'][0];?>">
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
        <legend><?php echo 'តាមដានការសំរាល'; ?></legend>                                    
        <div class="accordionDetail">                                                                   
            <div>
                <table id="tblGL" class="defualtTable" border="1px" style="width:100%;">
                    <tr align="center">
                        <td width="15%" rowspan="2" height="10%">ម៉ោង</td>                                                            
                        <td colspan="2">កូន</td>                                                            
                        <td colspan="4">ម្តាយ</td>
                    </tr>
                    <tr align="center">                                                           
                        <td>BCF</td>
                        <td>PDF</td>
                        <td>Col</td>                                                            
                        <td>TA</td>
                        <td>Pouls</td>
                        <td>T&#730</td>                                                            
                    </tr>                                                                    
                    <?php
                    $i=1;
                    $queryData=  mysql_query("SELECT * FROM mid_wife_births WHERE mid_wife_dossier_medical_id=".$patient['MidWifeBirth']['mid_wife_dossier_medical_id']);
                    while ($data=  mysql_fetch_array($queryData)){
                        ?>
                        <tr>
                            <td class="time">                                           
                                <input id="time<?php echo $i ?>" type="text" name="time[]" class="time" value="<?php echo $data['time'] ?>" style="width:94%;"/>
                                <img alt="" class="btnEmptyTime" id="btnEmptyTime<?php echo $i ?>" src="<?php echo $this->webroot; ?>/img/close.gif" style="cursor: pointer;" align="middle" />
                            </td>
                            <td class="next">                                            
                                <input id="bcf<?php echo $i ?>" type="text" name="bcf[]" class="bcf" value="<?php echo $data['bcf'] ?>" style="width:94%;"/>                                                                                       
                            </td>
                            <td class="next">                                            
                                <input id="pdf<?php echo $i ?>" type="text" name="pdf[]" class="pdf"value="<?php echo $data['pdf'] ?>" style="width:94%;"/>                                                                                 
                            </td>
                            <td class="next">                                            
                                <input id="col<?php echo $i ?>" type="text" name="col[]" class="col" value="<?php echo $data['col'] ?>" style="width:94%;"/>                                                                                      
                            </td>
                            <td class="next">                                            
                                <input id="ta<?php echo $i ?>" type="text" name="ta[]" class="ta" value="<?php echo $data['ta'] ?>" style="width:94%;"/>                                                                                  
                            </td>
                            <td class="next">                                            
                                <input id="pouls<?php echo$i ?>" type="text" name="pouls[]" class="pouls" value="<?php echo $data['pouls'] ?>" style="width:94%;"/>                                                                                   
                            </td>
                            <td class="next">                                            
                                <input id="temperature<?php echo $i ?>" type="text" name="temperature[]" class="temperature" value="<?php echo $data['temperature'] ?>" style="width:94%;"/>                                                                               
                                <input type="hidden" name="mid_wife_birth_id[]" value="<?php echo $data['id'] ?>" />
                            </td>                                                                                                   
                        </tr>                                        
                    <?php $i++;} ?>  
                </table>

                <table class="new_table">
                    <tr>
                        <td><span style="color:red">* </span>បរិមាណទឹកភ្លោះ</td>                                                                            
                    </tr>
                    <tr>                                                
                        <td class="test">Hydramnios</td>
                        <td>
                            <?php if ($patient['MidWifeBirthDetail']['hydramnios'] == "1") { ?>                                        
                                <input align="left" type="checkbox" value="1"  name="data[MidWifeService][hydramnios]" checked="checked" />  
                            <?php } else { ?>
                                <input type="checkbox" value="1"  name="data[MidWifeService][hydramnios]"/>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>                                                
                        <td class="test">Exc&egrave;s LA</td>
                        <td>
                            <?php if ($patient['MidWifeBirthDetail']['excess'] == "1") { ?>                                        
                                <input align="left" type="checkbox" value="1"  name="data[MidWifeService][excess]" checked="checked" />  
                            <?php } else { ?>
                                <input type="checkbox" value="1"  name="data[MidWifeService][excess]"/>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>                                                
                        <td class="test">Normal</td>
                        <td>
                            <?php if ($patient['MidWifeBirthDetail']['normal'] == "1") { ?>                                        
                                <input align="left" type="checkbox" value="1"  name="data[MidWifeService][normal]" checked="checked" />  
                            <?php } else { ?>
                                <input type="checkbox" value="1"  name="data[MidWifeService][normal]"/>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>                                                
                        <td class="test">Oligoamnios</td>
                        <td>
                            <?php if ($patient['MidWifeBirthDetail']['oligoamnios'] == "1") { ?>                                        
                                <input align="left" type="checkbox" value="1"  name="data[MidWifeService][oligoamnios]" checked="checked" />  
                            <?php } else { ?>
                                <input type="checkbox" value="1"  name="data[MidWifeService][oligoamnios]"/>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><span style="color:red">* </span>ពណ៌ទឹកភ្លោះ</td>                                                                            
                    </tr>
                    <tr>                                                
                        <td class="test">Blanch&acirc;tre</td>
                        <td>
                            <?php if ($patient['MidWifeBirthDetail']['whitish'] == "1") { ?>                                        
                                <input align="left" type="checkbox" value="1"  name="data[MidWifeService][whitish]" checked="checked" />  
                            <?php } else { ?>
                                <input type="checkbox" value="1"  name="data[MidWifeService][whitish]"/>                                                
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>                                                
                        <td class="test">Clair</td>
                        <td>
                            <?php if ($patient['MidWifeBirthDetail']['clear'] == "1") { ?>                                        
                                <input align="left" type="checkbox" value="1"  name="data[MidWifeService][clear]" checked="checked" />  
                            <?php } else { ?>
                                <input type="checkbox" value="1"  name="data[MidWifeService][clear]"/>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>                                                
                        <td class="test">Verd&acirc;tre
                            <input type="hidden" name="data[MidWifeService][mid_wife_birth_detail_id]" value="<?php echo $patient['MidWifeBirthDetail']['id'] ?>" />
                        </td> 
                        <td>
                            <?php if ($patient['MidWifeBirthDetail']['greenish'] == "1") { ?>                                        
                                <input align="left" type="checkbox" value="1"  name="data[MidWifeService][greenish]" checked="checked" />  
                            <?php } else { ?>
                                <input type="checkbox" value="1"  name="data[MidWifeService][greenish]"/>
                            <?php } ?>
                        </td>
                        <td><input type="hidden" name="data[MidWifeService][mid_wife_dossier_medical_id]" value="<?php echo $patient['MidWifeDossierMedical']['id']; ?>" /></td>   
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