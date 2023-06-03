<?php 
include('includes/function.php'); 
$absolute_url = FULL_BASE_URL . Router::url("/", false); 
$tblName = "tbl123"; 
$tblNameRadom = "tbl" . rand();

// Authentication
$this->element('check_access');
$allowQueueScan = checkAccess($user['User']['id'], 'scans', 'dashboardScan');
?>
<?php
if(isset($_POST['drawing'])){
    
    $_POST['drawing'] = str_replace("plus","+",$_POST['drawing']);   
    // Load the stamp and the photo to apply the watermark to
    $stamp = imagecreatefrompng($_POST['drawing']);    
    $im = imagecreatefrompng('img/photo.png');
    
    // Set the margins for the stamp and get the height/width of the stamp image
    $marge_right = 0;
    $marge_bottom = 0;
    $sx = imagesx($stamp);
    $sy = imagesy($stamp);

    // Copy the stamp image onto our photo using the margin offsets and the photo
    // width to calculate positioning of the stamp.
    imagecopy($im, $stamp, imagesx($im) - $sx - $marge_right, imagesy($im) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));

    $newImageName = 'img/dermatology/queueid_' . $_POST['queue_id'] . '.jpg';
    imagejpeg($im, $newImageName, 100);    
    echo $newImageName;    
    exit();
}
?>
<script type="text/javascript">
    $tabPharma = false;
    $tabPharmaNum = false;
    $(document).ready(function(){
        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
        $("#tabs3").tabs();
        
        $("#tabConsult<?php echo $tblName;?>").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabConsult/<?php echo $this->params['pass'][0].'/'.$this->params['pass'][1]; ?>");        
        // form consultation
        $("#btnTabConsult<?php echo $tblName;?>").click(function(){
            $("#tabConsult<?php echo $tblName;?>").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabConsult/<?php echo $this->params['pass'][0].'/'.$this->params['pass'][1]; ?>");
        });
        $("#btnTabConsultNum<?php echo $tblName;?>").click(function(){
            $("#tabConsultNum<?php echo $tblName;?>").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabConsultNum/<?php echo $this->params['pass'][0].'/'.$this->params['pass'][1].'/'.$patient['Patient']['id']; ?>");
        });        
        //close form consultation    
                           
        // form consultation Andrology
//        $("#btnConsultAndrology<?php echo $tblName;?>").click(function(){
//            $("#tabConsultAndrology<?php echo $tblName;?>").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabConsultAndrology/<?php echo $this->params['pass'][0].'/'.$this->params['pass'][1]; ?>");
//        });
//        $("#btnConsultAndrologyNum<?php echo $tblName;?>").click(function(){
//            $("#tabConsultAndrologyNum<?php echo $tblName;?>").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabConsultAndrologyNum/<?php echo $this->params['pass'][0].'/'.$this->params['pass'][1]; ?>");
//        });        
        //close form consultation 
           
        // form labo
        $("#btnTabLabo<?php echo $tblName;?>").click(function(){
            $("#tabLabo<?php echo $tblName;?>").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabLabo/<?php echo $this->params['pass'][0].'/'.$this->params['pass'][1]; ?>");
        });
        $("#btnTabLaboNum<?php echo $tblName;?>").click(function(){
            $("#tabLaboNum<?php echo $tblName;?>").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabLaboNum/<?php echo $this->params['pass'][0].'/'.$this->params['pass'][1]; ?>");
        });                
        // close form labo
        
        // form Prescription
        $("#btnTabPrescription<?php echo $tblName;?>").click(function(){           
            $("#tabPrescription<?php echo $tblName;?>").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabPrescription/<?php echo $this->params['pass'][0].'/'.$this->params['pass'][1]; ?>");
        });
        $("#btnTabPrescriptionNum<?php echo $tblName;?>").click(function(){            
            $("#tabPrescriptionNum<?php echo $tblName;?>").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabPrescriptionNum/<?php echo $this->params['pass'][0].'/'.$this->params['pass'][1]; ?>");
        });  
        // close form Prescription
        
        // form other service                
        $("#btnTabEcho<?php echo $tblName;?>").click(function(){
            $("#tabEcho<?php echo $tblName;?>").load("<?php echo $absolute_url.$this->params['controller']; ?>/tabOtherService/<?php echo $this->params['pass'][0].'/'.$this->params['pass'][1]; ?>");
        });
        $("#btnTabEchoNum<?php echo $tblName;?>").click(function(){
            $("#tabEchoNum<?php echo $tblName;?>").load("<?php echo $absolute_url.$this->params['controller']; ?>/tabOtherServiceNum/<?php echo $this->params['pass'][0].'/'.$this->params['pass'][1]; ?>");
        });
        // close form other service
        
        
        // form scan               
        $("#btnTabScan<?php echo $tblName;?>").click(function(){
            $("#tabScan<?php echo $tblName;?>").load("<?php echo $absolute_url.$this->params['controller']; ?>/tabScan/<?php echo $this->params['pass'][0].'/'.$this->params['pass'][1]; ?>");
        });
        $("#btnTabScanNum<?php echo $tblName;?>").click(function(){
            $("#tabScanNum<?php echo $tblName;?>").load("<?php echo $absolute_url.$this->params['controller']; ?>/tabScanNum/<?php echo $this->params['pass'][0].'/'.$this->params['pass'][1]; ?>");
        });
        // close scan
        
        // form Service               
        $("#btnTabService<?php echo $tblName;?>").click(function(){
            $("#tabService<?php echo $tblName;?>").load("<?php echo $absolute_url.$this->params['controller']; ?>/tabService/<?php echo $this->params['pass'][0].'/'.$this->params['pass'][1]; ?>");
        });
        $("#btnTabServiceNum<?php echo $tblName;?>").click(function(){
            $("#tabServiceNum<?php echo $tblName;?>").load("<?php echo $absolute_url.$this->params['controller']; ?>/tabServiceNum/<?php echo $this->params['pass'][0].'/'.$this->params['pass'][1]; ?>");
        });
        
        
        $("#consultation").accordion({
            collapsible: true,
            autoHeight: false,
            navigation: true,
            active: false
        });
        
        $(".btnBackPatientConsultation").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableQueue.fnDraw(false);
            var rightPanel = $(this).parent().parent().parent();
            var leftPanel  = rightPanel.parent().find(".leftPanel");
            rightPanel.hide();
            rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
        
        // show / hide patient information
        $("#btnHideShowPatientInfo<?php echo $tblNameRadom;?>").click(function(){
            var label  = $(this).find("span").text();
            var action = '';
            var img    = '<?php echo $this->webroot . 'img/button/'; ?>';
            if(label == 'Hide'){
                action = 'Show';
                $("#patient_info<?php echo $tblNameRadom;?>").hide();
                img += 'arrow-down.png';
            } else {
                action = 'Hide';
                $("#patient_info<?php echo $tblNameRadom;?>").show();
                img += 'arrow-up.png';
            }
            $(this).find("span").text(action);
            $(this).find("img").attr("src", img);
        });
        
    });
</script>
<style>
    #PatientConsultationAddForm input {
        width: 50% !important;
    }
    #PatientConsultationAddForm textarea{
        width: 98% !important;
        height: 50px !important;
    }
    #PatientConsultationAndrologyAddForm input {
        width: 30% !important;
    }
    #PatientConsultationAndrologyAddForm textarea{
        width: 98% !important;
        height: 50px !important;
    }
    .ui-tabs-panel{
        overflow: auto !important;
        height: auto !important;
    }
</style>
<div style="padding: 5px;border: 1px dashed #3C69AD;">
    <div class="buttons">
        <a href="" class="positive btnBackPatientConsultation">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<div style="float: right; width: 165px; text-align: right; cursor: pointer; margin-right: 10px;" id="btnHideShowPatientInfo<?php echo $tblNameRadom; ?>">
    [ <span>Hide</span> Patient Info <img alt="" align="absmiddle" style="width: 16px; height: 16px;" src="<?php echo $this->webroot . 'img/button/arrow-up.png'; ?>" /> ]
</div>
<div style="clear: both;"></div>
<fieldset style="padding: 5px; border: 1px dashed #3C69AD;" id="patient_info<?php echo $tblNameRadom; ?>">
    <legend><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?></legend>
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
            <th><?php __(TABLE_PATIENT_STATUS); ?></th>
                <td>: 
                    <input <?php if($patient['Patient']['allergic_medicine']!=0){ echo 'checked="true"';}?>  disabled="true" type="checkbox"/>
                    <label style="padding-right: 5px;"><?php echo TABLE_ALLERGIC.' :'; ?></label>
                    <?php
                        if($patient['Patient']['allergic_medicine']!=0){
                            echo '<span style="color:red;">'.$patient['Patient']['allergic_medicine_note'].'</span>';                            
                        }
                    ?>
                    <?php if($_SESSION['lang']=="kh"){ echo '<br/>';}?>
                    <input <?php if($patient['Patient']['unknown_allergic']!=0){ echo 'checked="true"';}?> disabled="true" type="checkbox"/>
                    <label><?php echo TABLE_UNKNOWN_ALLERGIC; ?></label>
                </td> 
            </tr>
        </table>
    </div>
</fieldset>
<br />
<div id="tabs3" style="padding: 5px; border: 1px dashed #3C69AD;">
    <ul>
        <li><a id="btnTabConsult<?php echo $tblName;?>" href="#tabConsult<?php echo $tblName;?>">Consultation</a></li>
        <li><a id="btnTabConsultNum<?php echo $tblName;?>" href="#tabConsultNum<?php echo $tblName;?>">Previous Consultation</a></li>
        <li><a id="btnTabLabo<?php echo $tblName;?>" href="#tabLabo<?php echo $tblName;?>">Laboratory</a></li>
        <li><a id="btnTabLaboNum<?php echo $tblName;?>" href="#tabLaboNum<?php echo $tblName;?>">Previous Laboratory</a></li>   
        <li><a id="btnTabPrescription<?php echo $tblName;?>" href="#tabPrescription<?php echo $tblName;?>">Prescription</a></li>
        <li><a id="btnTabPrescriptionNum<?php echo $tblName;?>" href="#tabPrescriptionNum<?php echo $tblName;?>">Previous Prescription</a></li>                              
        <li><a id="btnTabEcho<?php echo $tblName;?>" href="#tabEcho<?php echo $tblName;?>">Imagery</a></li>
        <li><a id="btnTabEchoNum<?php echo $tblName;?>" href="#tabEchoNum<?php echo $tblName;?>">Previous Imagery</a></li> 
		<li><a id="btnTabService<?php echo $tblName;?>" href="#tabService<?php echo $tblName;?>">Service</a></li>
        <li><a id="btnTabServiceNum<?php echo $tblName;?>" href="#tabServiceNum<?php echo $tblName;?>">Previous Service</a></li>
            
    </ul>
    <div id="tabConsult<?php echo $tblName;?>"></div>
    <div id="tabConsultNum<?php echo $tblName;?>"></div>     
    <div id="tabLabo<?php echo $tblName;?>"></div>
    <div id="tabLaboNum<?php echo $tblName;?>"></div>        
    <div id="tabPrescription<?php echo $tblName;?>"></div>
    <div id="tabPrescriptionNum<?php echo $tblName;?>" class="tabPrescriptionNum">> </div>   
	<div id="tabEcho<?php echo $tblName;?>"></div>
    <div id="tabEchoNum<?php echo $tblName;?>"></div>     
    <div id="tabService<?php echo $tblName;?>"></div>
    <div id="tabServiceNum<?php echo $tblName;?>"></div>
    
</div>
<br/>