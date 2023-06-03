<?php
include('includes/function.php');
$absolute_url  = FULL_BASE_URL . Router::url("/", false);
?>
<?php $tblName = "tbl" . rand(); ?>
<script type="text/javascript">
    $(document).ready(function(){
        $("#tabs2").tabs();
        $("#tabConsultNum").load("<?php echo $absolute_url.$this->params['controller']; ?>/tabConsultNum/<?php echo $this->params['pass'][0].'/'.$this->params['pass'][1]; ?>");
        $("#btnTabConsultNum").click(function(){
            $("#tabConsultNum").load("<?php echo $absolute_url.$this->params['controller']; ?>/tabConsultNum/<?php echo $this->params['pass'][0].'/'.$this->params['pass'][1]; ?>");
        });
        $("#btnTabLaboNum").click(function(){
            $("#tabLaboNum").load("<?php echo $absolute_url.$this->params['controller']; ?>/tabLaboNum/<?php echo $this->params['pass'][0].'/'.$this->params['pass'][1]; ?>");
        });        
        $("#btnTabPrescriptionNum").click(function(){            
            $("#tabPrescriptionNum").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabPrescriptionNum/<?php echo $this->params['pass'][0].'/'.$this->params['pass'][1]; ?>");
        }); 
        $("#btnTabServiceNum").click(function(){
            $("#tabServiceNum").load("<?php echo $absolute_url.$this->params['controller']; ?>/tabServiceNum/<?php echo $this->params['pass'][0].'/'.$this->params['pass'][1]; ?>");
        });
        
        $("#consultation").accordion({
            collapsible: true,
            autoHeight: false,
            navigation: true,
            active: false
        });
        
        $(".btnBackPatientHistory").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTablePatientHistory.fnDraw(false);
            var rightPanel = $(this).parent().parent().parent();
            var leftPanel  = rightPanel.parent().find(".leftPanel");
            rightPanel.hide();
            rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
        
        //Hide Patinen Info
        $("#btnHidePatientInfo<?php echo $tblName; ?>").click(function(){
            $("#patientInfo<?php echo $tblName; ?>").hide();
            $("#showPatientInfo<?php echo $tblName; ?>").show();
        });
        //Show Patinen Info
        $("#btnShowPatientInfo<?php echo $tblName; ?>").click(function(){
            $("#patientInfo<?php echo $tblName; ?>").show();
            $("#showPatientInfo<?php echo $tblName; ?>").hide();
        });
        
    });
</script>
<style>
    form input {
        width: 30% !important;
    }
    form textarea{
        width: 98% !important;
        height: 50px !important;
    }
    .ui-tabs-panel{
        overflow: auto !important;
        height: auto !important;
    }
</style>

<div style="padding: 5px;border: 1px dashed #3C69AD;">
    <input id="tab_id" value="2" type="hidden">
    <div class="buttons">
        <a href="" class="positive btnBackPatientHistory">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<legend id="showPatientInfo<?php echo $tblName; ?>" style="display:none;"><a href="#" id="btnShowPatientInfo<?php echo $tblName; ?>" style="background: #CCCCCC; font-weight: bold;"><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?> [ Show ] </a> </legend>
<fieldset style="padding: 5px;border: 1px dashed #3C69AD;" id="patientInfo<?php echo $tblName; ?>">
    <legend><a href="#" id="btnHidePatientInfo<?php echo $tblName; ?>" style="background: #CCCCCC; font-weight: bold;"> <?php __(MENU_PATIENT_MANAGEMENT_INFO); ?> [ Hide ] </a> </legend>
    <div id="profile">
        <table style="width: 100%;" cellpadding="3">
            <tr>
                <th style="width: 10%;"><?php echo PATIENT_CODE; ?></th>
                <td style="width: 40%;">: <?php echo $patient['Patient']['patient_code']; ?></td>
                <th style="width: 10%;"><?php echo PATIENT_NAME; ?></th>
                <td style="width: 40%;">: <?php echo $patient['Patient']['patient_name']; ?></td>            
            </tr>        
            <tr>
                <th><?php echo TABLE_SEX; ?></th>
                <td>: 
                    <?php
                    if ($patient['Patient']['sex'] == "F") {
                        echo GENERAL_FEMALE;
                    } else {
                        echo GENERAL_MALE;
                    }
                    ?>
                </td>
                <th><?php echo TABLE_DOB; ?></th>            
                <td>:
                    <?php
                    echo date("d/m/Y", strtotime($patient['Patient']['dob'])).'&nbsp;&nbsp;&nbsp;&nbsp;';
                    if($patient['Patient']['dob']!="0000-00-00" || $patient['Patient']['dob']!=""){
                        echo TABLE_AGE . ': '. getAgePatient($patient['Patient']['dob']);
                    }
                    ?>                        
                </td>
            </tr>
            <tr>
                <th><?php __(TABLE_NATIONALITY); ?></th>
                <td>: 
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
                <th><?php echo TABLE_TELEPHONE; ?></th>
                <td>: <?php echo $patient['Patient']['telephone']; ?></td>    
            </tr>             
            <tr>
                <th><?php __(TABLE_ADDRESS); ?></th>
                <td>: <?php echo $patient['Patient']['address']; ?></td>
                <th><?php echo TABLE_EMAIL; ?></th>            
                <td>
                    : <?php echo $patient['Patient']['email']; ?>
                </td>
            </tr>
            <tr>           
                <th><?php __(TABLE_PATIENT_STATUS); ?></th>
                <td colspan="3">: 
                    <input style="height:0px;" <?php if($patient['Patient']['allergic_medicine']!=0){ echo 'checked="true"';}?>  disabled="true" type="checkbox"/>
                    <label style="padding-right: 20px;"><?php echo TABLE_ALLERGIC; ?></label>
                    <?php if($_SESSION['lang']=="kh"){ echo '<br/>';}?>
                    <input <?php if($patient['Patient']['unknown_allergic']!=0){ echo 'checked="true"';}?> disabled="true" type="checkbox"/>
                    <label><?php echo TABLE_UNKNOWN_ALLERGIC; ?></label>
                </td>       
            </tr>
            <?php if($patient['Patient']['allergic_medicine']!=0){ ?>
            <tr>
                <th>&nbsp;</th>
                <td colspan="3" style="color:red;">
                    <?php echo $patient['Patient']['allergic_medicine_note']; ?>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</fieldset>
<br />
<div id="tabs2" style="padding: 5px;border: 1px dashed #3C69AD;">
    <ul>
        <li><a id="btnTabConsultNum" href="#tabConsultNum">Previous Chief Complain</a></li>
        <li><a id="btnTabLaboNum" href="#tabLaboNum">Previous Laboratory</a></li>
        <li><a id="btnTabPrescriptionNum" href="#tabPrescriptionNum">Previous Prescription</a></li>    
        <li><a id="btnTabServiceNum" href="#tabServiceNum">Previous Service</a></li>   
    </ul>
    <div id="tabConsultNum"></div>
    <div id="tabLaboNum"></div>
    <div id="tabPrescriptionNum"></div> 
    <div id="tabServiceNum"></div>
</div>
<br />