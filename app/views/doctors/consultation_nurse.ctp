<?php include('includes/function.php'); ?>
<?php $absolute_url = FULL_BASE_URL . Router::url("/", false); ?>

<?php $tblName = "tbl123"; ?>

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
            $("#tabConsultNum<?php echo $tblName;?>").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabConsultNum/<?php echo $this->params['pass'][0].'/'.$this->params['pass'][1]; ?>");
        });        
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
        
        $("#consultation").accordion({
            collapsible: true,
            autoHeight: false,
            navigation: true,
            active: false
        });
        
        $(".btnBackPatientConsultation").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableQueueNurse.fnDraw(false);
            var rightPanel = $(this).parent().parent().parent();
            var leftPanel  = rightPanel.parent().find(".leftPanel");
            rightPanel.hide();
            rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });
</script>
<style>
    #PatientConsultationAddForm input {
        width: 30% !important;
    }
    #PatientConsultationAddForm textarea{
        width: 98% !important;
        height: 50px !important;
    }
    .ui-tabs-panel{
        overflow: auto !important;
        height: auto !important;
    }
</style>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="" class="positive btnBackPatientConsultation">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<fieldset>
    <legend><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?></legend>
        <div id="profile">
            <table style="width: 100%;" cellpadding="3">
                <tr>
                    <th style="width: 15%;"><?php echo PATIENT_CODE; ?></th>
                    <td style="width: 35%;">: <?php echo $patient['Patient']['patient_code']; ?></td>
                    <th style="width: 15%;"><?php echo PATIENT_NAME; ?></th>
                    <td style="width: 35%;">: <?php echo $patient['Patient']['patient_name']; ?></td>            
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
                    <th><?php echo TABLE_AGE; ?></th>            
                    <td>: 
                        <?php
                        if($patient['Patient']['dob']!="0000-00-00" || $patient['Patient']['dob']!=""){
                            echo getAgePatient($patient['Patient']['dob']);
                        }
                        ?>                        
                    </td>
                </tr>
                <tr>
                    <th><?php __(TABLE_OCCUPATION); ?></th>
                    <td>: <?php echo $patient['Patient']['occupation']; ?></td>
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
                </tr>
                <tr>
                    <th><?php echo TABLE_EMAIL; ?></th>            
                    <td>
                        : <?php echo $patient['Patient']['email']; ?>
                    </td>
                    <th><?php echo TABLE_TELEPHONE; ?></th>
                    <td>: <?php echo $patient['Patient']['telephone']; ?></td>            
                </tr>                  
                <tr>
                    <th><?php __(TABLE_ADDRESS); ?></th>
                    <td>: <?php echo $patient['Patient']['address']; ?></td>        
                    <th><?php __(TABLE_CITY_PROVINCE); ?></th>
                    <td>: 
                        <?php
                        if ($patient['Patient']['location_id'] != "") {
                            $query = mysql_query("SELECT name FROM patient_locations WHERE id=" . $patient['Patient']['location_id']);
                            if (mysql_num_rows($query)) {
                                while ($row = mysql_fetch_array($query)) {
                                    echo $row['name'];
                                }
                            }
                        }
                        ?>
                    </td>
                </tr>
                <tr>           
                    <th><?php __(TABLE_PATIENT_STATUS); ?></th>
                    <td colspan="3">: 
                        <input <?php if($patient['Patient']['allergic_medicine']!=""){ echo 'checked="true"';}?>  disabled="true" type="checkbox" id="PatientAllergicMedicine"/>
                        <label style="padding-right: 20px;" for="PatientAllergicMedicine"><?php echo TABLE_ALLERGIC_MEDICINE; ?></label>
                        <?php if($_SESSION['lang']=="kh"){ echo '<br/>&nbsp;';}?>
                        <input <?php if($patient['Patient']['allergic_food']!=""){ echo 'checked="true"';}?>  disabled="true" type="checkbox" id="PatientAllergicFood"/>
                        <label for="PatientAllergicFood"><?php echo TABLE_ALLERGIC_FOOD; ?></label>
                    </td>       
                </tr>
            </table>
        </div>
</fieldset>
<br />
<div id="tabs3">
    <ul>
        <li><a id="btnTabConsult<?php echo $tblName;?>" href="#tabConsult<?php echo $tblName;?>">Consultation</a></li>
        <li><a id="btnTabConsultNum<?php echo $tblName;?>" href="#tabConsultNum<?php echo $tblName;?>">Previous Consultation</a></li>
        <li><a id="btnTabLabo<?php echo $tblName;?>" href="#tabLabo<?php echo $tblName;?>">Laboratory</a></li>
        <li><a id="btnTabLaboNum<?php echo $tblName;?>" href="#tabLaboNum<?php echo $tblName;?>">Previous Laboratory</a></li>   
        <li><a id="btnTabPrescription<?php echo $tblName;?>" href="#tabPrescription<?php echo $tblName;?>">Prescription</a></li>
        <li><a id="btnTabPrescriptionNum<?php echo $tblName;?>" href="#tabPrescriptionNum<?php echo $tblName;?>">Previous Prescription</a></li>   
        <li><a id="btnTabEcho<?php echo $tblName;?>" href="#tabEcho<?php echo $tblName;?>">Imagery</a></li>
        <li><a id="btnTabEchoNum<?php echo $tblName;?>" href="#tabEchoNum<?php echo $tblName;?>">Previous Imagery</a></li>   
    </ul>
    <div id="tabConsult<?php echo $tblName;?>"></div>
    <div id="tabConsultNum<?php echo $tblName;?>"></div>        
    <div id="tabLabo<?php echo $tblName;?>"></div>
    <div id="tabLaboNum<?php echo $tblName;?>"></div>        
    <div id="tabPrescription<?php echo $tblName;?>"></div>
    <div id="tabPrescriptionNum<?php echo $tblName;?>"></div>
    <div id="tabEcho<?php echo $tblName;?>"></div>
    <div id="tabEchoNum<?php echo $tblName;?>"></div>
</div>
<br/>