<style type="text/css">
    .print_number{
        width:100%;
        margin:0;
    }
    .print_number td{
        padding:0px;
        width: 100%;
        font-size: 12px;
    }    
</style>
<div class="print_number">
    <table cellspacing='2' cellpadding='2' border='0' style="width: 100%;">        
        <tr>
            <td valign='top' align='center'>
                <img src= '<?php echo $this->webroot;?>img/logo.png' alt='' style="width: 18%;" />                
            </td>
        </tr>
        <tr>
            <td style="text-align: center;">
                <?php echo date("d/m/Y H:i:s", strtotime($patients['QueuedDoctorWaiting']['created'])); ?>
            </td>
        </tr>
        <tr>
            <td style="text-align: left;"><?php echo GENERAL_QUEUE_NUMBER;?>: <b style="font-size:20px;"><?php echo $patients['QueuedDoctorWaiting']['number_taken'];?></b></td>
        </tr>
        <tr style="<?php if($patients['QueuedDoctorWaiting']['room_id']==""){ echo 'display:none;';}?>">
            <td style="text-align: left;">
                <?php echo TABLE_ROOM_NUMBER;?>: 
                <?php 
                if($patients['QueuedDoctorWaiting']['room_id']!=""){
                    $queryRoom = mysql_query("SELECT room_name FROM rooms WHERE id = {$patients['QueuedDoctorWaiting']['room_id']}");
                    while ($resultRoom = mysql_fetch_array($queryRoom)) {
                        echo $resultRoom['room_name'];
                    }  
                }       
                ?>
            </td>
        </tr>        
        <tr>
            <td style="text-align: left;"><?php echo DOCTOR_NAME;?>: <?php echo $patients['Employee']['name'];?></td>
        </tr>
        <tr>
            <td style="text-align: left;"><?php echo PATIENT_CODE;?>: <?php echo $patients['Patient']['patient_code'];?></td>
        </tr>
        <tr>
            <td style="text-align: left;"><?php echo PATIENT_NAME;?>: <?php echo $patients['Patient']['patient_name'];?></td>
        </tr>
        <tr>
            <td style="text-align: left;"><?php echo TABLE_SEX;?>: 
                <?php
                if($patients['Patient']['sex']=="M"){
                    echo GENERAL_MALE;                    
                }else{
                    echo GENERAL_FEMALE;
                }
                ?>
            </td>
        </tr>
        <tr style="display: none;">
            <td style="text-align: left;">                
                <?php echo TITLE_CLIENT_INSURANCE_PROVIDER;?> : <?php if($patients['Patient']['company_insurance_id']!="") { $queryData = mysql_query("SELECT name FROM company_insurances WHERE id=".$patients['Patient']['company_insurance_id']); $resData = mysql_fetch_array($queryData); echo $resData['name'];}else { echo 'GENERAL';}?></b>                        
            </td>
        </tr>
    </table>
</div>