<script type="text/javascript">
    $(document).ready(function(){       
        $(".btnBackPatientMedicalSurgery").click(function(event){
            event.preventDefault();
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide( "slide", { direction: "right" }, 500, function() {
                leftPanel.show();
                rightPanel.html('');
            });
        });
    });
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="#" class="positive btnBackPatientMedicalSurgery">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<fieldset>
    <legend><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?></legend>
    <table class="info">
        <tr>
            <th style="width: 15%;"><?php __(PATIENT_CODE); ?></th>
            <td style="width: 35%;">: <?php echo $patient['Patient']['patient_code']; ?></td>
            <th style="width: 15%;"><?php __(TABLE_DOB); ?></th>
            <td style="width: 35%;">: 
                <?php echo date("d/m/Y", strtotime($patient['Patient']['dob']));; ?>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?php 
                echo TABLE_AGE.': ';
                $then_ts = strtotime($patient['Patient']['dob']);
                $then_year = date('Y', $then_ts);
                $age = date('Y') - $then_year;
                if(strtotime('+' . $age . ' years', $then_ts) > time()) $age--;              

                if($age==0){
                    $then_year = date('m', $then_ts);
                    $month = date('m') - $then_year;
                    if(strtotime('+' . $month . ' month', $then_ts) > time()) $month--;
                    echo $month.' '.GENERAL_MONTH;
                }else{
                    echo $age.' '.GENERAL_YEAR_OLD;
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
            <th><?php __(TABLE_OCCUPATION); ?></th>
            <td>: <?php echo $patient['Patient']['occupation']; ?></td>
            <th><?php __(TABLE_NATIONALITY); ?></th>
            <td>: 
                <?php                 
                    if($patient['Patient']['patient_group_id']!=""){
                        $query = mysql_query("SELECT name FROM patient_groups WHERE id=".$patient['Patient']['patient_group_id']);
                        while ($row = mysql_fetch_array($query)) {
                            if($patient['Patient']['patient_group_id']==1){
                                echo $row['name'];
                            }else{
                                echo $row['name'].'&nbsp;&nbsp;('.$patient['Nationality']['name'].')';
                            }
                        }
                    }else{
                        echo $patient['Nationality']['name'];
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
            <td>: <?php echo $patient['Patient']['address']; ?></td>        
            <th><?php __(TABLE_CITY_PROVINCE); ?></th>
            <td>: 
                <?php
                if($patient['Patient']['location_id']!=""){
                    $query = mysql_query("SELECT name FROM patient_locations WHERE id=".$patient['Patient']['location_id']);
                    if(mysql_num_rows($query)){
                        while ($row = mysql_fetch_array($query)) {
                            echo $row['name'];                
                        }
                    }
                }
                ?>
            </td>
        </tr>
        <tr>
            <th><?php __(TABLE_COMPANY); ?></th>
            <td>: <?php echo $patient['Company']['name']; ?></td>     
            <th>&nbsp;</th>
            <td>&nbsp;</td>
        </tr>
    </table>
</fieldset>
<br/>
<fieldset>
    <legend><?php __(MENU_IPD_MANAGEMENT_INFO); ?></legend>
    <table style="width:91% !important" class="info">
        <tr>
            <th style="width: 15%;"><?php __(TABLE_PATIENT_CHECK_IN_DATE); ?></th>
            <td style="width: 35%;">: <?php echo date("d/m/Y H:i:s", strtotime($patient['PatientIpd']['date_ipd'])); ?></td>        
            <th style="width: 15%;"><?php __(TABLE_ROOM_NUMBER); ?></th>
            <td style="width: 35%;">: <?php echo $patient['Room']['room_name'];?></td>
        </tr>
        <tr>
            <th><?php __(TABLE_ADMITTING_PHYSICIAN); ?></th>
            <td>: <?php echo $doctor['Employee']['name']; ?></td>        
            <th><?php __(TABLE_DEPARTMENT); ?></th>
            <td>: <?php echo $department['Group']['name'];?></td>
        </tr>
        <tr>
            <th><?php __(TABLE_ALLERGIC); ?></th>
            <td>: <?php echo $patient['PatientIpd']['allergies']; ?></td>        
            <th><?php __(TABLE_WITNESS_NAME); ?></th>
            <td>: <?php echo $patient['PatientIpd']['witness_name'];?></td>
        </tr>
        <tr>
            <th><?php __(TABLE_DOCTOR_EXPLAINED_TO_PATIENT); ?></th>
            <td>: <?php echo $patient['PatientIpd']['doctor_explain_to_patient']; ?></td>        
            <th><?php __(TABLE_PATIENT_FOLLOWING_SURGICAL); ?></th>
            <td>: <?php echo $patient['PatientIpd']['patient_following_surgical'];?></td>
        </tr>
        <tr>
            <th><?php __(TABLE_ACCORDING_TO_PATIENT); ?></th>
            <td>: <?php echo $patient['PatientIpd']['according_to_patient']; ?></td>        
            <th><?php __(TABLE_ACCORDING_NUMBER); ?></th>
            <td>: <?php echo $patient['PatientIpd']['according_number'];?></td>
        </tr>
        <tr>
            <th><?php __(TABLE_AUTHORIZE_NAME); ?></th>
            <td>: <?php echo $patient['PatientIpd']['authorized_name']; ?></td>        
            <th><?php __(TABLE_TELEPHONE); ?></th>
            <td>: <?php echo $patient['PatientIpd']['authorized_telephone'];?></td>
        </tr>
        <tr>
            <th><?php __(TABLE_ADDRESS); ?></th>
            <td>: <?php echo $patient['PatientIpd']['authorized_address']; ?></td>        
            <th><?php __(TABLE_ID_CARD); ?></th>
            <td>: <?php echo $patient['PatientIpd']['authorized_id_card'];?></td>
        </tr>
        <tr>
            <th><?php __(TABLE_ISSUE_DATE); ?></th>
            <td>: <?php echo $patient['PatientIpd']['authorized_issue_date'] != "0000-00-00" ? date("d/m/Y", strtotime($patient['PatientIpd']['authorized_issue_date'])) : ""; ?></td>        
            <th><?php __(TABLE_EXPIRATION_DATE); ?></th>
            <td>: <?php echo $patient['PatientIpd']['authorized_expiration_date'] != "0000-00-00" ? date("d/m/Y", strtotime($patient['PatientIpd']['authorized_expiration_date'])) : ""; ?></td>
        </tr>
        <tr>
            <th><?php __(TABLE_ISSUE_PLACE); ?></th>
            <td>: <?php echo $patient['PatientIpd']['authorized_issue_place']; ?></td>        
            <th>&nbsp;</th>
            <td>&nbsp;</td>
        </tr>
    </table>
</fieldset>