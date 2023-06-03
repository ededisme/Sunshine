<?php 
include('includes/function.php'); 
$absolute_url  = FULL_BASE_URL . Router::url("/", false);
$tblName = "tbl" . rand();
$queueId = $patient['PatientIpd']['queued_doctor_id'];
$queryQueue = mysql_query("SELECT queue_id FROM queued_doctors WHERE id = ".$patient['PatientIpd']['queued_doctor_id']);
while ($rowQueue = mysql_fetch_array($queryQueue)) {
    $queueId = $rowQueue['queue_id'];
}
?>
<script type="text/javascript">
    $(document).ready(function(){       
        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                
        $("#tabs2").tabs();
        $("#tabConsultNum").load("<?php echo $absolute_url.$this->params['controller']; ?>/tabConsultNum/<?php echo $patient['PatientIpd']['queued_doctor_id'].'/'.$queueId.'/'.$patient['Patient']['id']; ?>");
        $("#btnTabConsultNum").click(function(){
            $("#tabConsultNum").load("<?php echo $absolute_url.$this->params['controller']; ?>/tabConsultNum/<?php echo $patient['PatientIpd']['queued_doctor_id'].'/'.$queueId.'/'.$patient['Patient']['id']; ?>");
        });
        $("#btnTabPrescription").click(function(){           
            $("#tabPrescription").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabPrescription/<?php echo $patient['PatientIpd']['queued_doctor_id'].'/'.$queueId.'/'.$patient['PatientIpd']['id']; ?>");
        });
        $("#btnTabLabo").click(function(){           
            $("#tabLabo").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabLabo/<?php echo $patient['PatientIpd']['queued_doctor_id'].'/'.$queueId.'/'.$patient['PatientIpd']['id']; ?>");
        });
                    
        $("#btnTabEcho").click(function(){
            $("#tabEcho").load("<?php echo $absolute_url.$this->params['controller']; ?>/tabOtherService/<?php echo $patient['PatientIpd']['queued_doctor_id'].'/'.$queueId.'/'.$patient['PatientIpd']['id']; ?>");
        });
        
        $("#btnTabService").click(function(){           
            $("#tabService").load("<?php echo $absolute_url . $this->params['controller']; ?>/addService/<?php echo $patient['PatientIpd']['id']."/".$patient['PatientIpd']['patient_id'].'/'.$patient['PatientIpd']['ipd_type'].'/view'; ?>");            
        });
        $("#btnTabLaboResult").click(function(){           
            $("#tabLaboResult").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabLaboResult/<?php echo $patient['PatientIpd']['id']."/".$patient['PatientIpd']['patient_id'].'/'.$patient['PatientIpd']['ipd_type'].'/view'; ?>");            
        });
        $("#btnTabDailyClinical").click(function(){          
            $("#tabDailyClinical").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabDailyClinical/<?php echo $patient['PatientIpd']['queued_doctor_id'].'/'.$queueId.'/'.$patient['Patient']['id']; ?>");            
        });
        $("#btnTabAttachFile").click(function(){        
            $("#tabAttachFiles").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabAttachFile/<?php echo $patient['Patient']['id']; ?>/5");            
        });
        
        
        $("#consultation").accordion({
            collapsible: true,
            autoHeight: false,
            navigation: true,
            active: false
        });
                
        $(".btnBackPatientIPD").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTablePatientIPD.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide( "slide", { direction: "right" }, 500, function() {
                leftPanel.show();
                rightPanel.html('');
            });
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
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="#" class="positive btnBackPatientIPD">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<span id="showPatientInfo<?php echo $tblName; ?>" style="display:none;"><a href="#" id="btnShowPatientInfo<?php echo $tblName; ?>" style="background: #CCCCCC; font-weight: bold;"><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?> [ Show ] </a> </span>
<div style="width: 100%; float: left;" id="patientInfo<?php echo $tblName; ?>">
    <a href="#" id="btnHidePatientInfo<?php echo $tblName; ?>" style="background: #CCCCCC; font-weight: bold;"> <?php __(MENU_PATIENT_MANAGEMENT_INFO); ?> [ Hide ] </a>
    <div class="clear"></div>
    <fieldset style="width: 48%; float: left; min-height: 200px;">
        <legend><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?></legend>
        <table class="info" cellpadding="3">
            <tr>
                <th style="width: 15%;"><?php __(PATIENT_CODE); ?></th>
                <td style="width: 30%;">: <?php echo $patient['Patient']['patient_code']; ?></td>
                <th style="width: 15%;"><?php __(TABLE_DOB); ?></th>
                <td style="width: 40%;">: 
                    <?php echo date("d/m/Y", strtotime($patient['Patient']['dob']));; ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <?php                 
                    if($patient['Patient']['dob']!="0000-00-00" || $patient['Patient']['dob']!=""){
                        echo TABLE_AGE . ': '. getAgePatient($patient['Patient']['dob']);
                    }
                    ?> 
                </td>
            </tr>
            <tr>
                <th><?php __(PATIENT_NAME); ?></th>
                <td>: <?php echo $patient['Patient']['patient_name']; ?></td>        
                <th><?php __(TABLE_SEX); ?></th>
                <td>: 
                    <?php 
                    if ($patient['Patient']['sex'] == "F") {
                        echo GENERAL_FEMALE;
                    } else {
                        echo GENERAL_MALE;
                    }
                    ?>
                </td>
            </tr>  
            <tr>   
                <th><?php __(TABLE_TELEPHONE); ?></th>
                <td>: <?php echo $patient['Patient']['telephone']; ?></td>
                <th><?php __(TABLE_EMAIL); ?></th>
                <td>: <?php echo $patient['Patient']['email']; ?></td>
            </tr>    
            <tr>
                <th><?php __(TABLE_ADDRESS); ?></th>
                <td colspan="3">: <?php echo $patient['Patient']['address']; ?></td> 
            </tr>
        </table>
    </fieldset>
    <fieldset style="width: 48%; float: left; min-height: 200px;">
        <legend><?php __(MENU_IPD_MANAGEMENT_INFO); ?></legend>
        <table class="info">
            <tr>
                <th style="width: 15%;"><?php __(TABLE_PATIENT_CHECK_IN_DATE); ?></th>
                <td style="width: 35%;">: <?php echo date("d/m/Y", strtotime($patient['PatientIpd']['date_ipd'])); ?></td>        
                <th style="width: 15%;"><?php __(TABLE_ROOM_NUMBER); ?></th>
                <td style="width: 35%;">: <?php echo $patient['Room']['room_name'];?></td>
            </tr>
            <tr>
                <th><?php __(TABLE_ADMITTING_PHYSICIAN); ?></th>
                <td>: <?php echo $doctor['DoctorConsultation']['name']; ?></td>        
                <th><?php __(TABLE_DEPARTMENT); ?></th>
                <td>: <?php echo $department['Group']['name'];?></td>
            </tr>
            <tr>
                <th><?php __(TABLE_ALLERGIC); ?></th>
                <td colspan="3">: <?php echo $patient['PatientIpd']['allergies']; ?></td>                    
            </tr>
            <?php             
            if($patient['PatientLeave']['end_date']!="0000-00-00" && $patient['PatientLeave']['end_date']!=""){
            ?>
            <tr>
                <th><?php __(TABLE_ROOM_DATE_OUT); ?></th>
                <td>: <?php echo date("d/m/Y", strtotime($patient['PatientLeave']['end_date'])); ?></td>        
                <th><?php __(TABLE_PATIENT_LEAVE); ?></th>
                <td>: <?php echo $patient['PatientLeave']['type_leave'];?></td>
            </tr>
            <tr>
                <th><?php __(GENERAL_DESCRIPTION); ?></th>
                <td colspan="3">: <?php echo $patient['PatientLeave']['note'];?></td>
            </tr>
            <?php }?>
        </table>
    </fieldset>
</div>
<div class="clear"></div>
<div id="tabs2" style="padding: 5px;border: 1px dashed #3C69AD;">
    <ul>
        <li><a id="btnTabConsultNum" href="#tabConsultNum">Previous Consultation</a></li>
        <?php if($patient['PatientLeave']['id']==""){ ?>
            <li><a id="btnTabDailyClinical" href="#tabDailyClinical">Daily Clinical Report</a></li>
            <li><a id="btnTabPrescription" href="#tabPrescription">Prescription</a></li>
            <li><a id="btnTabLabo" href="#tabLabo">Labo</a></li>
            <li><a id="btnTabEcho" href="#tabEcho">Imagery</a></li>
            <li><a id="btnTabService" href="#tabService">Service</a></li>
            <li><a id="btnTabLaboResult" href="#tabLaboResult">Laboratory Result</a></li>
            <li><a id="btnTabAttachFile" href="#tabAttachFiles">Attach File</a></li>
        <?php } ?>
    </ul>
    <div id="tabConsultNum"></div>
    <div id="tabDailyClinical"></div>
    <div id="tabPrescription"></div> 
    <div id="tabLabo"></div> 
    <div id="tabEcho"></div>
    <div id="tabService"></div> 
    <div id="tabLaboResult"></div> 
    <div id="tabAttachFiles"></div>
</div>
<br />