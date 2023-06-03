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
        $("#MidWifeServiceAddNewAccouchementFirstTimeForm").validationEngine();
        $("#MidWifeServiceAddNewAccouchementFirstTimeForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSave").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {                
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif"); 
                $(".btnBackAddNewDossierMedicalAccouchementFirst").click();               
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
        
        $(".btnBackAddNewDossierMedicalAccouchementFirst").click(function(event) {
            event.preventDefault();
            event.preventDefault();
            var queueId=$(this).attr('queueId');
            $('#dataDossierMedical').html("<?php echo ACTION_LOADING; ?>");
            $('#dataDossierMedical').load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/addMidWifeServiceDoctorDossierMedical/"+queueId);
        });
        
        $(".btnAddGL").click(function(){  
            $("#tblGL").find("tr:last").clone(true).appendTo("#tblGL");
            $("#tblGL").find("tr:last").find("td .time").val('');
            $("#tblGL").find("tr:last").find("td .first_blood").val('');
            $("#tblGL").find("tr:last").find("td .first_ta").val('');
            $("#tblGL").find("tr:last").find("td .first_p").val('');          
            $("#tblGL").find("tr:last").find("td .first_temperature").val('');            
            $("#tblGL").find("tr:last").find("td .btnRemoveGL").show();
            $(this).siblings(".btnRemoveGL").show();
            $(this).hide();
            var randomNumber=Math.floor(Math.random()*1000000)+1000;
            $("#tblGL").find("tr:last").find("td .time").attr("id", "time"+randomNumber);
            $("#tblGL").find("tr:last").find("td .btnEmptyTime").attr("id", "btnEmptyTime"+randomNumber);
            $("#tblGL").find("tr:last").find("td .first_blood").attr("id", "first_blood"+randomNumber);
            $("#tblGL").find("tr:last").find("td .first_ta").attr("id", "first_ta"+randomNumber);
            $("#tblGL").find("tr:last").find("td .first_p").attr("id", "first_p"+randomNumber);            
            $("#tblGL").find("tr:last").find("td .first_temperature").attr("id", "first_temperature"+randomNumber);  
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
        $("#MidWifeServiceDob" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
            yearRange: '-100:-0'
        }).unbind("blur");
    });
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="#" class="positive btnBackAddNewDossierMedicalAccouchementFirst" rel="<?php echo $this->params['pass'][0];?>" queueId="<?php echo $patient['Queue']['id'];?>">
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
    <legend><?php echo 'ការតាមដានក្រោយសំរាល(កំឡុងពេល 2H)'; ?></legend>                                    
    <div class="accordionDetail">                                                                   
        <div>
            <table id="tblGL" class="defualtTable" border="1px solid" style="width:100%">                                                
                <tr align="center">
                    <td colspan="5">1 ម៉ោងដំបូង​(រៀងរាល់​ 15នាទី)</td>                                   
                </tr>
                <tr align="center">
                    <td>ម៉ោង</td>
                    <td>ធ្លាក់ឈាម</td>
                    <td>TA</td>
                    <td>P</td>
                    <td>T&#730</td>                                    
                </tr>
                <?php for ($counter = 1; $counter <= 5; $counter++) { ?>
                    <tr>
                        <td class="first">
                            <input type="text" id="time<?php echo $counter ?>" type="text" name="time[]" class="time" style="width:94%;"/>
                            <img alt="" id="btnEmptyTime<?php echo $counter ?>" src="<?php echo $this->webroot; ?>/img/close.gif" style="cursor: pointer;" align="middle" />
                        </td>                                        
                        <td><input type="text" id="first_blood<?php echo $counter ?>" name="first_blood[]" class="first_blood" style="width:94%;"/></td>
                        <td><input type="text" id="first_ta<?php echo $counter ?>" name="first_ta[]" class="first_ta" style="width:94%;"/></td>
                        <td><input type="text" id="first_p<?php echo $counter ?>" name="first_p[]" class="first_p" style="width:94%;"/></td>
                        <td><input type="text" id="first_temperature<?php echo $counter ?>" name="first_temperature[]" class="first_temperature" style="width:94%;"/></td> 
                    </tr>
                <?php } ?> 
            </table>                            
            <table>
                <tr>  
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

