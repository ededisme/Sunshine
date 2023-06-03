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
        $("#MidWifeServiceAddNewTrackingForm").validationEngine();
        $("#MidWifeServiceAddNewTrackingForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSave").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {                
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif"); 
                $(".btnBackAddNewDossierMedicalTracking").click();               
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
        
        $(".btnAddGL").click(function(){                             
            
            $("#tblGL").find("tr:last").clone(true).appendTo("#tblGL");
            $("#tblGL").find("tr:last").find("td .time").val('');
            $("#tblGL").find("tr:last").find("td .bcf").val('');
            $("#tblGL").find("tr:last").find("td .pdf").val('');
            $("#tblGL").find("tr:last").find("td .col").val('');
            $("#tblGL").find("tr:last").find("td .ta").val('');
            $("#tblGL").find("tr:last").find("td .pouls").val('');
            $("#tblGL").find("tr:last").find("td .temperature").val('');            
            $("#tblGL").find("tr:last").find("td .btnRemoveGL").show();
            $(this).siblings(".btnRemoveGL").show();
            $(this).hide();

            var randomNumber=Math.floor(Math.random()*1000000)+1000;
            $("#tblGL").find("tr:last").find("td .time").attr("id", "time"+randomNumber);
            $("#tblGL").find("tr:last").find("td .btnEmptyTime").attr("id", "btnEmptyTime"+randomNumber);
            $("#tblGL").find("tr:last").find("td .bcf").attr("id", "bcf"+randomNumber);
            $("#tblGL").find("tr:last").find("td .pdf").attr("id", "pdf"+randomNumber);
            $("#tblGL").find("tr:last").find("td .col").attr("id", "col"+randomNumber);
            $("#tblGL").find("tr:last").find("td .ta").attr("id", "ta"+randomNumber);
            $("#tblGL").find("tr:last").find("td .pouls").attr("id", "pouls"+randomNumber);
            $("#tblGL").find("tr:last").find("td .temperature").attr("id", "temperature"+randomNumber);                                                            
            
        }); 
        
        $(".btnRemoveGL").click(function(){
            var obj=$(this);
            obj.closest("tr").remove();
            $("#tblGL").find("tr:last").find("td .btnAddGL").show();
            if($('#tblGL tr').length==7){
                $("#tblGL").find("tr:eq(6)").find("td .btnRemoveGL").hide();
            }                        
        });
        
        $("#time1").timepicker({
            step: 15
        }).unbind('blur');
        if($("#time1").val()!=""){
            $("#btnEmptyTime1").show();
        }else{
            $("#btnEmptyTime1").hide();
        }
        $("#time1").blur(function(){
            if($(this).val()!=""){
                $("#btnEmptyTime1").show();
            }else{
                $("#btnEmptyTime1").hide();
            }
        });
        $("#btnEmptyTime1").click(function(){
            $("#time1").val("");
            $(this).hide();
        });             
        
        $("#time2").timepicker({
            step: 15
        }).unbind('blur');
        if($("#time2").val()!=""){
            $("#btnEmptyTime2").show();
        }else{
            $("#btnEmptyTime2").hide();
        }
        $("#time2").blur(function(){
            if($(this).val()!=""){
                $("#btnEmptyTime2").show();
            }else{
                $("#btnEmptyTime2").hide();
            }
        });
        $("#btnEmptyTime2").click(function(){
            $("#time2").val("");
            $(this).hide();
        });
        
        $("#time3").timepicker({
            step: 15
        }).unbind('blur');
        if($("#time3").val()!=""){
            $("#btnEmptyTime3").show();
        }else{
            $("#btnEmptyTime3").hide();
        }
        $("#time3").blur(function(){
            if($(this).val()!=""){
                $("#btnEmptyTime3").show();
            }else{
                $("#btnEmptyTime3").hide();
            }
        });
        $("#btnEmptyTime3").click(function(){
            $("#time3").val("");
            $(this).hide();
        });
        
        $("#time4").timepicker({
            step: 15
        }).unbind('blur');
        if($("#time4").val()!=""){
            $("#btnEmptyTime4").show();
        }else{
            $("#btnEmptyTime4").hide();
        }
        $("#time4").blur(function(){
            if($(this).val()!=""){
                $("#btnEmptyTime4").show();
            }else{
                $("#btnEmptyTime4").hide();
            }
        });
        $("#btnEmptyTime4").click(function(){
            $("#time4").val("");
            $(this).hide();
        });                                  
        $("#time5").timepicker({
            step: 15
        }).unbind('blur');
        if($("#time5").val()!=""){
            $("#btnEmptyTime5").show();
        }else{
            $("#btnEmptyTime5").hide();
        }
        $("#time5").blur(function(){
            if($(this).val()!=""){
                $("#btnEmptyTime5").show();
            }else{
                $("#btnEmptyTime5").hide();
            }
        });
        $("#btnEmptyTime5").click(function(){
            $("#time5").val("");
            $(this).hide();
        });
        
        $(".btnBackAddNewDossierMedicalTracking").click(function(event) {
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
    });
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="#" class="positive btnBackAddNewDossierMedicalTracking" rel="<?php echo $this->params['pass'][0];?>" queueId="<?php echo $patient['Queue']['id'];?>">
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
                <?php for ($counter = 1; $counter <= 5; $counter++) { ?>
                    <tr>
                        <td class="time">                                           
                            <input id="time<?php echo $counter ?>" type="text" name="time[]" class="time" style="width:94%;"/>
                            <img alt="" class="btnEmptyTime" id="btnEmptyTime<?php echo $counter ?>" src="<?php echo $this->webroot; ?>/img/close.gif" style="cursor: pointer;" align="middle" />
                        </td>
                        <td class="next">                                            
                            <input id="bcf<?php echo $counter ?>" type="text" name="bcf[]" class="bcf" style="width:94%;"/>                                                                                       
                        </td>
                        <td class="next">                                            
                            <input id="pdf<?php echo $counter ?>" type="text" name="pdf[]" class="pdf" style="width:94%;"/>                                                                                 
                        </td>
                        <td class="next">                                            
                            <input id="col<?php echo $counter ?>" type="text" name="col[]" class="col" style="width:94%;"/>                                                                                      
                        </td>
                        <td class="next">                                            
                            <input id="ta<?php echo $counter ?>" type="text" name="ta[]" class="ta" style="width:94%;"/>                                                                                  
                        </td>
                        <td class="next">                                            
                            <input id="pouls<?php echo $counter ?>" type="text" name="pouls[]" class="pouls" style="width:94%;"/>                                                                                   
                        </td>
                        <td class="next">                                            
                            <input id="temperature<?php echo $counter ?>" type="text" name="temperature[]" class="temperature" style="width:94%;"/>                                                                               
                        </td>                                                            
<!--                                        <td style="white-space: nowrap;">
                            <img alt="" src="<?php echo $this->webroot . 'img/plus.png'; ?>" class="btnAddGL" style="cursor: pointer;<?php echo $counter != 5 ? 'display: none;' : ''; ?>" onmouseover="Tip('Add New')" />
                            <img alt="" src="<?php echo $this->webroot . 'img/minus_white.png'; ?>" class="btnRemoveGL"​​​ style="cursor: pointer;<?php echo $counter < 6 ? 'display: none;' : ''; ?>" onmouseover="Tip('Remove')" />
                        </td>-->
                    </tr>                                    
                <?php } ?>                                                                                                                

            </table>

            <table class="new_table">
                <tr>
                    <td><span style="color:red">* </span>បរិមាណទឹកភ្លោះ</td>                                                                            
                </tr>
                <tr>                                                
                    <td class="test">Hydramnios</td>
                    <td><input type="checkbox" value="1" name="data[MidWifeService][hydramnios]"></td>
                </tr>
                <tr>                                                
                    <td class="test">Exc&egrave;s LA</td>
                    <td><input type="checkbox" value="1" name="data[MidWifeService][excess]"></td>
                </tr>
                <tr>                                                
                    <td class="test">Normal </td>
                    <td><input type="checkbox" value="1" name="data[MidWifeService][normal]"></td>
                </tr>
                <tr>                                                
                    <td class="test">Oligoamnios</td>
                    <td><input type="checkbox" value="1" name="data[MidWifeService][oligoamnios]"></td>
                </tr>
                <tr>
                    <td><span style="color:red">* </span>ពណ៌ទឹកភ្លោះ</td>                                                                            
                </tr>
                <tr>                                                
                    <td class="test">Blanch&acirc;tre </td>
                    <td><input type="checkbox" value="1" name="data[MidWifeService][whitish]"></td>
                </tr>
                <tr>                                                
                    <td class="test">Clair </td>
                    <td><input type="checkbox" value="1" name="data[MidWifeService][clear]"></td>
                </tr>
                <tr>                                                
                    <td class="test">Verd&acirc;tre </td>
                    <td><input type="checkbox" value="1" name="data[MidWifeService][greenish]"></td>
                    <td><input type="hidden" name="data[MidWifeService][patient_id]" value="<?php echo $patient['Patient']['id']; ?>" /></td>   
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

