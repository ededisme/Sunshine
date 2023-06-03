<style type="text/css" media="screen">
    div.print-footer {display: none;}   
    table tr th{
        text-align: center !important;
    }
</style>
<style type="text/css" media="print">
    div.print_doc { width:100%; margin-top:130px;}
    div.print-footer { display: block; width: 100%;}
    #btnDisappearPrint { display: none;}
    table tr th{
        text-align: center !important;
    }
    table tr td{ font-size: 12px; }
    table tr td p{
        padding: 0 10px;
        margin: 0;
        font-size: 12px;
    }
    table tr td span{
        padding: 0 10px;
        margin: 0;
        font-size: 12px;
    }
    .tblPnt tr td span{
        padding-left: 10%;
        font-size: 12px;
    }
    @page
    {
        /*this affects the margin in the printer settings*/  
        margin: 5mm 7mm 5mm 10mm;
    }
</style>
<?php 
require_once("includes/function.php");
?>
<div id="printQuotationPatient" class="print_doc">
    <table cellpadding="0" cellspacing="0" style="width: 100%;">
    <div style="height: 80px;"></div>
        <tbody>
            <tr>
                <td>
                    <table style="width: 100%;">   
                        <!-- <tr>
                            <td style="vertical-align: top;text-align: left;width: 15%;">
                                <img alt="" style="width: 120px;" src="<?php echo $this->webroot; ?>img/logo_s.png" />           
                            </td> -->
                            <td style="text-align: center;">            
                                <!-- <h2 style="font-size: 16px; text-decoration: underline;"><?php echo $this->data['Branch']['name'];?></h2>    -->
                                <h3 style="font-size: 14px; text-decoration: underline;"><?php echo 'Clinic observation';?></h3>
                            </td>
                            <!-- <td style="vertical-align: top;text-align: left;width: 15%;">
                                <img alt="" style="width: 120px;" src="<?php echo $this->webroot; ?>img/logo_s.png" />           
                            </td>
                        </tr>        -->
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table style="width: 100%; padding-top: 20px;" cellspacing="5">
                        <tr>
                            <td style="width: 50%;">
                                <span style="text-decoration: underline;">Name of the patient:</span> <?php echo $patient['Patient']['patient_name']; ?>
                            </td>
                            <td style="width: 15%;">
                                Age:
                                <?php 
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
                            <td style="width: 15%;">
                                sexe:
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
                            <td colspan="3">
                                <span style="text-decoration: underline;">Profession:</span> <?php echo $patient['Patient']['occupation']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="text-decoration: underline;">Admission/Consultation Date:</span> <?php echo date("d/m/Y", strtotime($patient['PatientIpd']['date_ipd'])); ?>
                            </td>
                            <td colspan="2">
                                Time: <?php echo date("H:i", strtotime($patient['PatientIpd']['date_ipd'])); ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <span style="text-decoration: underline;"><?php __(MENU_VITAL_SING); ?>:</span>
                                <br/>
                                <table class="tblPnt" style="width: 100%; padding-left: 20px;" cellspacing="3">
                                    <?php 
                                    $queryPatientVitalSign = mysql_query("SELECT * FROM patient_vital_signs WHERE is_active = 1 AND queued_doctor_id = ".$patient['PatientConsultation']['queued_doctor_id']);
                                    $resultPatientVitalSign = mysql_fetch_array($queryPatientVitalSign);
                                    ?>
                                    <tr>
                                        <td style="width: 15%;"><?php echo TABLE_HEIGHT; ?></td>
                                        <td style="width: 20%;">: <?php echo $resultPatientVitalSign['height']; ?> cm</td>
                                        <td style="width: 15%;"><?php echo TABLE_WEIGHT; ?></td>
                                        <td style="width: 15%;">: <?php echo $resultPatientVitalSign['weight']; ?> kg</td>
                                        <td style="width: 25%;"><?php echo TABLE_BMI; ?>: <span class="BMI"><?php echo $resultPatientVitalSign['BMI'] ?></span></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%;"><?php echo TABLE_PULSE; ?></td>
                                        <td style="width: 20%;">: <?php echo $resultPatientVitalSign['pulse']; ?> /m</td>
                                        <td style="width: 15%;"><?php echo TABLE_RESPIRATORY; ?></td>
                                        <td style="width: 20%;" colspan="2">: <?php echo $resultPatientVitalSign['respiratory']; ?> /m</td>                     
                                    </tr>
                                    <tr>
                                        <td style="width: 15%;"><?php echo TABLE_TEMPERATURE; ?></td>
                                        <td style="width: 20%;">: <?php echo $resultPatientVitalSign['temperature']; ?> °C</td>
                                        <td style="width: 15%;"><?php echo TABLE_SOP2; ?></td>
                                        <td style="width: 20%;" colspan="2">: <?php echo $resultPatientVitalSign['sop2']; ?></td> 
                                    </tr>
                                    <tr style="display: none;">
                                        <td style="width: 15%;">Description</td>
                                        <td style="width: 20%;" colspan="4">: <?php  echo $resultPatientVitalSign['other_info']; ?> </td>
                                    </tr>
                                </table>    
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <span style="text-decoration: underline;"><?php __(MENU_BLOOD_PRESSURE); ?>:</span>
                                <br/>
                                <table class="tblPnt" style="width: 100%; padding-left: 20px;" cellspacing="3">
                                    <?php 
                                    if(!empty($resultPatientVitalSign['id'])){
                                        $queryBloodPressure = mysql_query("SELECT * FROM patient_vital_sign_blood_pressures WHERE patient_vital_sign_id = ".$resultPatientVitalSign['id']);
                                        $resultPatientBloodPressure = mysql_fetch_array($queryBloodPressure);
                                    }
                                    ?>
                                    <tr>
                                        <td style="width: 10%" class="first">Systolic</td>            
                                        <td>: &nbsp;&nbsp;<?php echo $resultPatientBloodPressure['result_systolic_1'];?> mmHg</td>
                                    </tr>
                                    <tr>
                                        <td class="first">Diastolic</td>            
                                        <td>: &nbsp;&nbsp;<?php echo $resultPatientBloodPressure['result_diastolic_1'];?> mmHg</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <span style="text-decoration: underline;">Chief complaint:</span> <?php echo $patient['PatientConsultation']['chief_complain']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <span style="text-decoration: underline;">Entry diagnosis:</span> <?php echo $patient['PatientConsultation']['daignostic']; ?>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="3">
                                <span style="text-decoration: underline;">Discharge diagnosis:</span> <?php echo $patient['PatientLeave']['diagnotist_after']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <span style="text-decoration: underline;">Disease History:</span> 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="padding: 10px;">
                                <?php echo nl2br($patient['PatientConsultation']['medical_history']); ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <span style="text-decoration: underline;">Antecedents:</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <table class="tblPnt" style="width: 100%; padding-left: 20px;">
                                    <tr>
                                        <td style="width: 20%;">Past Medical History:</td>
                                        <td>
                                            <?php echo nl2br($patient['PatientConsultation']['past_medical_history']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Past Surgery History:</td>
                                        <td>
                                            <?php echo nl2br($patient['PatientConsultation']['medical_surgery']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Family History:</td>
                                        <td>
                                            <?php echo nl2br($patient['PatientConsultation']['family_history']); ?>
                                        </td>
                                    </tr>
                                </table>

                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <span style="text-decoration: underline;">Clinical Examination:</span> 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="padding: 10px;">
                                <?php echo nl2br($patient['PatientConsultation']['physical_examination']); ?>
                            </td>
                        </tr>
                    </table>
                    <br/>
                    <table style="width: 100%;" cellpadding="3" cellspacing="0">
                        <tr>
                            <th style="width: 33%; font-size: 12px; border-right: 2px solid rgb(0, 0, 0); border-bottom: 2px solid rgb(0, 0, 0);">Date/Vital sign</th>
                            <th style="width: 33%; font-size: 12px; border-right: 2px solid rgb(0, 0, 0); border-bottom: 2px solid rgb(0, 0, 0);">Clinical observation</th>
                            <th style="width: 33%; font-size: 12px; border-bottom: 2px solid rgb(0, 0, 0);">Treatment</th>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; border-right: 2px solid rgb(0, 0, 0); ">
                                <table class="tblPnt" style="width: 100%;">
                                <?php
                                if(!empty($patient['PatientIpd']['id'])){
                                    $query_vital_sign = mysql_query("SELECT * FROM patient_ipd_vital_signs WHERE is_active=1 AND patient_ipd_id=" . $patient['PatientIpd']['id']." ORDER BY created ASC");
                                    while ($data_vital_sign = mysql_fetch_array($query_vital_sign)) {
                                        ?>
                                        <tr>
                                            <td style="border: none; font-size: 12px; font-weight: bold; background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo date('d/m/Y H:i:s', strtotime($data_vital_sign['created'])); ?></td>
                                            <td style="border: none; font-size: 12px; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo getDoctor($data_vital_sign['created_by']);?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <table class="tblPnt" style="width: 100%;" cellpadding="3" cellspacing="0">
                                                    <tr>
                                                        <td><?php echo 'BP'; ?> = <?php echo $data_vital_sign['bp']; ?> mmHg</td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo 'HR'; ?> = <?php echo $data_vital_sign['hr']; ?> bpm</td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo 'T<sup>o</sup>'; ?> = <?php echo $data_vital_sign['temperature']; ?> °C</td>
                                                    </tr>
                                                    <tr>     
                                                        <td><?php echo 'RR'; ?> = <?php echo $data_vital_sign['rr']; ?> / min</td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo 'Urine'; ?> = <?php echo $data_vital_sign['urine']; ?> ml/24h</td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo 'Gas and Fecal'; ?> : <?php echo $data_vital_sign['gas_fecal']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo 'Drainage'; ?> : <?php echo $data_vital_sign['drainage']; ?> mL</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="vertical-align: top;"><?php echo 'Note'; ?> : <?php echo nl2br($data_vital_sign['note']); ?></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                } 
                                ?>
                                </table>
                            </td>
                            <td style="vertical-align: top; border-right: 2px solid rgb(0, 0, 0); ">
                                <table class="tblPnt" style="width: 100%;">
                                    <?php
                                    if(!empty($patient['PatientConsultation']['id'])){
                                        $query_followup = mysql_query("SELECT * FROM patient_followups WHERE is_active=1 AND patient_consultation_id=" . $patient['PatientConsultation']['id']." ORDER BY created ASC");
                                        while ($data_followup = mysql_fetch_array($query_followup)) {
                                            ?>
                                            <tr>
                                                <td style="border: none; font-size: 12px; font-weight: bold; background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo date('d/m/Y H:i:s', strtotime($data_followup['created'])); ?></td>
                                                <td style="border: none; font-size: 12px; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo getDoctor($data_followup['created_by']);?></td>
                                            </tr>
                                            <tr>
                                                <td style="border: none; padding: 5%; color: rgb(0, 0, 0);" colspan="2">
                                                    <?php echo nl2br($data_followup['followup']);?>
                                                </td>
                                            </tr>
                                        <?php   
                                        }
                                    }
                                    ?>
                                </table>
                            </td>
                            <td style="vertical-align: top;">
                                <table class="tblPnt" style="width: 100%;">
                                    <?php       
                                        $resultImmuzation = array();    
                                        $queryPrscription = mysql_query("SELECT * FROM orders WHERE orders.status > 0 AND orders.patient_id = {$patient['Patient']['id']}");
                                        if(mysql_num_rows($queryPrscription)){
                                            while ($resultPrescription = mysql_fetch_array($queryPrscription)) {        
                                                $queryPrscriptionDetail = mysql_query("SELECT order_details.order_id FROM order_details "
                                                        . "INNER JOIN products ON products.id = order_details.product_id "
                                                        . "INNER JOIN product_pgroups ON product_pgroups.product_id = products.id "
                                                        . "INNER JOIN uoms ON uoms.id = order_details.qty_uom_id "
                                                        . "WHERE order_details.order_id = {$resultPrescription['id']} AND pgroup_id = 27 GROUP BY order_details.order_id");
                                                while ($orderDetail = mysql_fetch_array($queryPrscriptionDetail)) {
                                                    $resultImmuzation[] = $orderDetail['order_id'];
                                                }
                                           }
                                        }
                                        $conditionVaccine = "";
                                        if(!empty($resultImmuzation)){
                                            $resultVaccine = implode(',', $resultImmuzation);
                                            $conditionVaccine = "AND orders.id NOT IN ({$resultVaccine})";
                                        }
                                        $queryPrscription = mysql_query("SELECT * FROM orders WHERE orders.status > 0 AND orders.queue_doctor_id = {$patient['PatientConsultation']['queued_doctor_id']} {$conditionVaccine}  ORDER BY created ASC");                        
                                        if(mysql_num_rows($queryPrscription)){
                                            while ($resultPrescription = mysql_fetch_array($queryPrscription)) {                             
                                            ?>                        
                                                <tr>
                                                    <td style="border: none; font-size: 12px; font-weight: bold; background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo date('d/m/Y H:i:s', strtotime($resultPrescription['created'])); ?></td>
                                                    <td style="border: none; font-size: 12px; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo getDoctor($resultPrescription['created_by']);?></td>
                                                </tr>
                                                <?php
                                                $index = 0;
                                                $queryPrscriptionDetail = mysql_query("SELECT *, products.name, products.code, uoms.abbr FROM order_details INNER JOIN products ON products.id = order_details.product_id INNER JOIN uoms ON uoms.id = order_details.qty_uom_id WHERE order_details.order_id = {$resultPrescription['id']}");
                                                while ($orderDetail = mysql_fetch_array($queryPrscriptionDetail)) {
                                                    $productName = $orderDetail['name'];                               
                                                ?>
                                                    <tr>
                                                        <td style="border: none;" colspan="2">
                                                            <?php echo++$index; ?> - 
                                                            <?php 
                                                            echo $productName;
                                                            if(trim($orderDetail['note'])!=""){
                                                                echo '&nbsp;&nbsp;>&nbsp;&nbsp;'.$orderDetail['note'];
                                                            }
                                                            if($orderDetail['qty']!=""){
                                                                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$orderDetail['qty'];
                                                            }
                                                            if($orderDetail['abbr']!=""){
                                                                echo '&nbsp;&nbsp;&nbsp; '.$orderDetail['abbr'];
                                                            }
                                                            if($orderDetail['num_days']!=""){
                                                                echo ',&nbsp;&nbsp;&nbsp;'.$orderDetail['num_days'];
                                                            }
                                                            $medicinUseMorning = "";
                                                            if($orderDetail['morning_use_id']!= ""){
                                                                $queryMedicineUse = mysql_query("SELECT name FROM treatment_uses WHERE id = {$orderDetail['morning_use_id']}");
                                                                while ($resultMedicineUse = mysql_fetch_array($queryMedicineUse)) {                        
                                                                    $medicinUseMorning = $resultMedicineUse['name'];                                    
                                                                }
                                                            }
                                                            echo ',&nbsp;&nbsp;&nbsp;'.$medicinUseMorning;
                                                            ?>
                                                        </td>
                                                    </tr>                                        
                                                <?php                
                                                }

                                                $queryPrscriptionMisc = mysql_query("SELECT order_miscs.*, uoms.abbr FROM order_miscs INNER JOIN uoms ON uoms.id =  order_miscs.qty_uom_id WHERE order_miscs.order_id = {$resultPrescription['id']}");
                                                while ($orderMisc = mysql_fetch_array($queryPrscriptionMisc)) {                                                           
                                                ?>
                                                    <tr>
                                                        <td style="border: none;" colspan="2">
                                                            <?php echo ++$index; ?> -  
                                                            <?php 
                                                            echo $productName = $orderMisc['description']; 
                                                            if(trim($orderMisc['note'])!=""){
                                                                echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$orderMisc['note'];
                                                            }
                                                            if($orderMisc['qty']!=""){
                                                                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$orderMisc['qty'];
                                                            }                
                                                            if($orderMisc['num_days']!=""){
                                                                echo ',&nbsp;&nbsp;&nbsp; '.TABLE_NUM_DAYS.': '.$orderMisc['num_days'];
                                                            }
                                                            $medicinUseMorning = "";
                                                            if($orderMisc['morning_use_id']!= ""){
                                                                $queryMedicineUse = mysql_query("SELECT name FROM treatment_uses WHERE id = {$orderMisc['morning_use_id']}");
                                                                while ($resultMedicineUse = mysql_fetch_array($queryMedicineUse)) {             
                                                                    $medicinUseMorning = $resultMedicineUse['name'];                                    
                                                                }
                                                            }               
                                                            echo ',&nbsp;&nbsp;&nbsp;'.$medicinUseMorning;
                                                            ?>
                                                        </td>                                            
                                                    </tr>
                                                <?php
                                                }                        
                                            }                           
                                        }

                                        if(!empty($resultImmuzation)){        
                                            $resultVaccine = implode(',', $resultImmuzation);
                                            $queryPrscription = mysql_query("SELECT * FROM orders WHERE orders.status > 0 AND orders.queue_doctor_id = {$patient['PatientConsultation']['queued_doctor_id']} AND orders.id IN ({$resultVaccine}) ORDER BY created ASC");
                                            if(mysql_num_rows($queryPrscription)){
                                            while ($resultPrescription = mysql_fetch_array($queryPrscription)) {
                                            ?>                            
                                                <tr>
                                                    <td style="border: none; font-size: 12px; font-weight: bold; background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo date('d/m/Y H:i:s', strtotime($resultPrescription['created'])); ?></td>
                                                    <td style="border: none; font-size: 12px; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo getDoctor($resultPrescription['created_by']);?></td>
                                                </tr>
                                                <?php 
                                                $index = 0;
                                                $queryPrscriptionDetail = mysql_query("SELECT *, products.name, products.code, uoms.abbr FROM order_details "
                                                        . "INNER JOIN products ON products.id = order_details.product_id "
                                                        . "INNER JOIN product_pgroups ON product_pgroups.product_id = products.id "
                                                        . "INNER JOIN uoms ON uoms.id = order_details.qty_uom_id "
                                                        . "WHERE order_details.order_id = {$resultPrescription['id']} AND pgroup_id = 27");

                                                    while ($orderDetail = mysql_fetch_array($queryPrscriptionDetail)) {
                                                        $productName = $orderDetail['name'];                               
                                                        ?>
                                                        <tr>
                                                            <td style="border: none;" colspan="2">
                                                                <?php echo++$index; ?> - 
                                                                <?php 
                                                                echo $productName;
                                                                if(trim($orderDetail['note'])!=""){
                                                                    echo '&nbsp;&nbsp;>&nbsp;&nbsp;'.$orderDetail['note'];
                                                                }
                                                                if($orderDetail['qty']!=""){
                                                                    echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$orderDetail['qty'];
                                                                }
                                                                if($orderDetail['abbr']!=""){
                                                                    echo '&nbsp;&nbsp;&nbsp; '.$orderDetail['abbr'];
                                                                }
                                                                if($orderDetail['num_days']!=""){
                                                                    echo ',&nbsp;&nbsp;&nbsp;'.$orderDetail['num_days'];
                                                                }
                                                                $medicinUseMorning = "";
                                                                if($orderDetail['morning_use_id']!= ""){
                                                                    $queryMedicineUse = mysql_query("SELECT name FROM treatment_uses WHERE id = {$orderDetail['morning_use_id']}");
                                                                    while ($resultMedicineUse = mysql_fetch_array($queryMedicineUse)) {                        
                                                                        $medicinUseMorning = $resultMedicineUse['name'];                                    
                                                                    }
                                                                }
                                                                echo ',&nbsp;&nbsp;&nbsp;'.$medicinUseMorning;
                                                                ?>
                                                            </td>
                                                        </tr>                                        
                                                    <?php                
                                                    }
                                                    $queryPrscriptionMisc = mysql_query("SELECT order_miscs.*, uoms.abbr FROM order_miscs INNER JOIN uoms ON uoms.id =  order_miscs.qty_uom_id WHERE order_miscs.order_id = {$resultPrescription['id']}");
                                                    while ($orderMisc = mysql_fetch_array($queryPrscriptionMisc)) {                                                           
                                                    ?>
                                                        <tr>
                                                            <td style="border: none;" colspan="2">
                                                                <?php echo ++$index; ?> -  
                                                                <?php 
                                                                echo $productName = $orderMisc['description']; 
                                                                if(trim($orderMisc['note'])!=""){
                                                                    echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$orderMisc['note'];
                                                                }
                                                                if($orderMisc['qty']!=""){
                                                                    echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$orderMisc['qty'];
                                                                }                
                                                                if($orderMisc['num_days']!=""){
                                                                    echo ',&nbsp;&nbsp;&nbsp; '.TABLE_NUM_DAYS.': '.$orderMisc['num_days'];
                                                                }
                                                                $medicinUseMorning = "";
                                                                if($orderMisc['morning_use_id']!= ""){
                                                                    $queryMedicineUse = mysql_query("SELECT name FROM treatment_uses WHERE id = {$orderMisc['morning_use_id']}");
                                                                    while ($resultMedicineUse = mysql_fetch_array($queryMedicineUse)) {             
                                                                        $medicinUseMorning = $resultMedicineUse['name'];                                    
                                                                    }
                                                                }               
                                                                echo ',&nbsp;&nbsp;&nbsp;'.$medicinUseMorning;
                                                                ?>
                                                            </td>                                            
                                                        </tr>
                                                    <?php
                                                    }                                
                                                }
                                            }                            
                                        }
                                        ?>
                                </table>
                            </td>
                        </tr>
                    </table>
                    
                    <br/>
                    <br/>
                    <table style="float: right;">
                        <tr>
                            <td>                    
                                <?php echo TITLE_DAY_CREATED. ' ' .date("d/m/Y"). ' ';?>                                            
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php 
                                $queryCreatedBy = mysql_query("SELECT signature_photo FROM users WHERE id = ".$user['User']['id']);
                                $resultCreatedBy = mysql_fetch_array($queryCreatedBy);
                                ?>
                                <div style="<?php if($resultCreatedBy['signature_photo']=="") { echo '<br/><br/><br/>';}?>"></div>
                                <p>
                                    <img style="<?php if($resultCreatedBy['signature_photo']=="") { echo 'display:none;';}?> width: 100px; height: 50px;" src="<?php echo $this->webroot;?>public/signature_photo/<?php echo $resultCreatedBy['signature_photo'];?>" />
                                </p>
                                <?php echo getDoctor($user['User']['id']);?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
        <!-- <tfoot>
            <tr>
                <td style="height: 60px;">
                    <div class="print-footer" style="position: fixed; bottom: 0; text-align: center; width: 100%;">
                        <center style="">
                            <table style="width:100% ;">
                                <tr>
                                    <td colspan="2" style="font-size:10pt; width: 100%; font-family:'Times New Roman'; vertical-align: top;"><?php echo $this->data['Branch']['name'];?></td>
                                </tr>
                                <tr>
                                    <td style="font-size:9pt; width: 75%; font-family:'Times New Roman'; vertical-align: top;"><?php echo $this->data['Branch']['address'] ?></td>
                                    <td style="text-align: right; font-size:9pt; width: 30% ; font-family:'Times New Roman'">Tel : <?php echo $this->data['Branch']['telephone'] ?></td>
                                </tr>
                            </table>
                        </center>
                    </div>
                </td>
            </tr>
        </tfoot> -->
    </table>
    <div style="clear:both"></div>
    <br />        
    <div style="float:left;width: 450px;">
        <div>
            <input type="button" value="<?php echo ACTION_PRINT; ?>" id='btnDisappearPrint' class='noprint'>                
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(document).dblclick(function() {
            window.close();
        });
        $("#btnDisappearPrint").click(function() {
            $("#footerPrint").show();
            window.print();
            window.close();
        });
    });
</script>