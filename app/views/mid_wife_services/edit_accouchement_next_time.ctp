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
        $("#MidWifeServiceEditAccouchementNextTimeForm").validationEngine();
        $("#MidWifeServiceEditAccouchementNextTimeForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveMidWifeService").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $(".btnBackMidWifeServiceEditNext").click();
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
        
        $(".btnBackMidWifeServiceEditNext").click(function(event){
            event.preventDefault();
            var queueId=$('#queueId').val();
            var id = $(this).attr('rel');
            $('#dataDossierMedical').html("<?php echo ACTION_LOADING; ?>");
            $('#dataDossierMedical').load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/addMidWifeServiceDoctorDossierMedical/"+queueId);
        });
        
        $(".btnAddGL").click(function(){ 
            $("#tblGL").find("tr:last").clone(true).appendTo("#tblGL");
            $("#tblGL").find("tr:last").find("td .next_time").val('');
            $("#tblGL").find("tr:last").find("td .next_blood").val('');
            $("#tblGL").find("tr:last").find("td .next_ta").val('');
            $("#tblGL").find("tr:last").find("td .next_p").val('');          
            $("#tblGL").find("tr:last").find("td .next_temperature").val('');            
            $("#tblGL").find("tr:last").find("td .btnRemoveGL").show();
            $(this).siblings(".btnRemoveGL").show();
            $(this).hide();
            var randomNumber=Math.floor(Math.random()*1000000)+1000;
            $("#tblGL").find("tr:last").find("td .next_time").attr("id", "next_time"+randomNumber);
            $("#tblGL").find("tr:last").find("td .btnEmptyTime").attr("id", "btnEmptyTime"+randomNumber);
            $("#tblGL").find("tr:last").find("td .next_blood").attr("id", "next_blood"+randomNumber);
            $("#tblGL").find("tr:last").find("td .next_ta").attr("id", "next_ta"+randomNumber);
            $("#tblGL").find("tr:last").find("td .next_p").attr("id", "next_p"+randomNumber);            
            $("#tblGL").find("tr:last").find("td .next_temperature").attr("id", "next_temperature"+randomNumber);  
        }); 
        
        $(".btnRemoveGL").click(function(){
            var obj=$(this);
            obj.closest("tr").remove();
            $("#tblGL").find("tr:last").find("td .btnAddGL").show();
            if($('#tblGL tr').length==7){
                $("#tblGL").find("tr:eq(6)").find("td .btnRemoveGL").hide();
            }                        
        });
                               
        $("#next_time1").timepicker({
            step: 30
        }).unbind('blur');
        if($("#next_time1").val()!=""){
            $("#next_btnEmptyTime1").show();
        }else{
            $("#next_btnEmptyTime1").hide();
        }
        $("#next_time1").blur(function(){
            if($(this).val()!=""){
                $("#next_btnEmptyTime1").show();
            }else{
                $("#next_btnEmptyTime1").hide();
            }
        });
        $("#next_btnEmptyTime1").click(function(){
            $("#next_time1").val("");
            $(this).hide();
        });             
        
        $("#next_time2").timepicker({
            step: 30
        }).unbind('blur');
        if($("#next_time2").val()!=""){
            $("#next_btnEmptyTime2").show();
        }else{
            $("#next_btnEmptyTime2").hide();
        }
        $("#next_time2").blur(function(){
            if($(this).val()!=""){
                $("#next_btnEmptyTime2").show();
            }else{
                $("#next_btnEmptyTime2").hide();
            }
        });
        $("#next_btnEmptyTime2").click(function(){
            $("#next_time2").val("");
            $(this).hide();
        });
        
        $("#next_time3").timepicker({
            step: 30
        }).unbind('blur');
        if($("#next_time3").val()!=""){
            $("#next_btnEmptyTime3").show();
        }else{
            $("#next_btnEmptyTime3").hide();
        }
        $("#next_time3").blur(function(){
            if($(this).val()!=""){
                $("#next_btnEmptyTime3").show();
            }else{
                $("#next_btnEmptyTime3").hide();
            }
        });
        $("#next_btnEmptyTime3").click(function(){
            $("#next_time3").val("");
            $(this).hide();
        });
        
        $("#next_time4").timepicker({
            step: 30
        }).unbind('blur');
        if($("#next_time4").val()!=""){
            $("#next_btnEmptyTime4").show();
        }else{
            $("#next_btnEmptyTime4").hide();
        }
        $("#next_time4").blur(function(){
            if($(this).val()!=""){
                $("#next_btnEmptyTime4").show();
            }else{
                $("#next_btnEmptyTime4").hide();
            }
        });
        $("#next_btnEmptyTime4").click(function(){
            $("#next_time4").val("");
            $(this).hide();
        });
        
        $("#next_time5").timepicker({
            step: 30
        }).unbind('blur');
        if($("#next_time5").val()!=""){
            $("#next_btnEmptyTime5").show();
        }else{
            $("#next_btnEmptyTime5").hide();
        }
        $("#next_time5").blur(function(){
            if($(this).val()!=""){
                $("#next_btnEmptyTime5").show();
            }else{
                $("#next_btnEmptyTime5").hide();
            }
        });
        $("#next_btnEmptyTime5").click(function(){
            $("#next_time5").val("");
            $(this).hide();
        });
    });
    // end document
    
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="#" class="positive btnBackMidWifeServiceEditNext" rel="<?php echo $this->params['pass'][0];?>">
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
    <legend><?php echo 'ការតាមដានក្រោយសំរាល(កំឡុងពេល 2H)'; ?></legend>                                    
    <div class="accordionDetail">                                                                   
        <div>                            
            <table id="tblGL" class="defualtTable" border="1px solid" style="width:100%;">                                                
                <tr align="center">                                    
                    <td colspan="5">ម៉ោងបន្ទាប់(រៀងរាល់​ 30នាទី)</td>
                </tr>
                <tr align="center">
                    <td>ម៉ោង</td>
                    <td>ធ្លាក់ឈាម</td>
                    <td>TA</td>
                    <td>P</td>
                    <td>T&#730</td>                                    
                </tr>
                <?php
                $i=1;
                $query_next_accouchement = mysql_query("SELECT * FROM mid_wife_accouchement_next_times WHERE is_active=1 AND mid_wife_dossier_medical_id=" . $patient['MidWifeDossierMedical']['id']);
                while ($next_accouchement = mysql_fetch_array($query_next_accouchement)) {
                    ?> 
                    <tr>
                        <td class="next">
                            <input type="text" id="next_time<?php echo $i ?>" type="text" name="next_time[]" class="time" value="<?php echo $next_accouchement['next_time'] ?>" style="width:94%;"/>
                        </td>                                        
                        <td><input type="text" id="next_blood<?php echo $i ?>" name="next_blood[]" class="next_blood" value="<?php echo $next_accouchement['next_blood'] ?>" style="width:94%;"/></td>
                        <td><input type="text" id="next_ta<?php echo $i ?>" name="next_ta[]" class="next_ta" value="<?php echo $next_accouchement['next_ta'] ?>" style="width:94%;"/></td>
                        <td><input type="text" id="next_p<?php echo $i ?>" name="next_p[]" class="next_p" value="<?php echo $next_accouchement['next_p'] ?>" style="width:94%;"/></td>
                        <td><input type="text" id="next_temperature<?php echo $i ?>" name="next_temperature[]" class="next_temperature" value="<?php echo $next_accouchement['next_temperature'] ?>" style="width:94%;"/></td>                                         
                        <input type="hidden" name="next_accouchement_id[]" value="<?php echo $next_accouchement['id'] ?>" />
                    </tr>
                <?php $i++;} ?>                                                                                                                                                     
            </table>
            <table>
                <?php
                $n=1;
                    $query_allaitements = mysql_query("SELECT * FROM mid_wife_allaitements WHERE is_active=1 AND mid_wife_dossier_medical_id=" . $patient['MidWifeDossierMedical']['id']);
                    while ($allaitement = mysql_fetch_array($query_allaitements)) {                                    
                ?> 
                    <tr>
                        <td width="180px"><span style="color: red">* </span>ការបំបៅដោះ(Allaitement)</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>ភ្លាមៗ</td>
                        <td>
                            <?php if ($allaitement['soon'] == "1") { ?>                                        
                            <input align="left" type="checkbox" value="1"  name="data[MidWifeService][soon]" checked="checked" id="soon_<?php echo $n; ?>"/>  
                            <?php } else { ?>
                                <input type="checkbox" value="1"  name="data[MidWifeService][soon]" id="soon_<?php echo $n; ?>"/>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>2H ក្រោយកើត</td>
                        <td>
                            <?php if ($allaitement['two_houre_after'] == "1") { ?>                                        
                            <input align="left" type="checkbox" value="1"  name="data[MidWifeService][two_houre_after]" checked="checked" id="two_houre_<?php echo $n; ?>"/>  
                            <?php } else { ?>
                                <input type="checkbox" value="1"  name="data[MidWifeService][two_houre_after]" id="two_houre_<?php echo $n; ?>"/>
                            <?php } ?>
                            <input type="hidden" name="data[MidWifeService][allaitement_id]" value="<?php echo $allaitement['id'] ?>" />
                        </td>
                    </tr>
                    <tr>      
                        <td><input type="hidden" name="data[MidWifeService][mid_wife_dossier_medical_id]" value="<?php echo $patient['MidWifeDossierMedical']['id']; ?>" /></td>
                    </tr>
                <?php $n++;} ?> 
            </table>
        </div>                                        
    </div>                                                                                                                                                                   
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