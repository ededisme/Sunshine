<style type="text/css">
    table td { color: #000;} 
    .table{
        font-size: 12px !important;
    }  
    .table td.first {
        border-left: 1px solid #000;       
    }
    .table td {
        border-right: 1px solid #000;
        border-bottom: 1px solid #000;
        padding: 6px 6px 6px 12px;
        color: #000;
    }    
    .table_print th{
        background: none;
    }
    .none-border{
        border: none !important;
    }
    .none-border tr td{
        border: none !important;
        font-size: 12px !important;
    }
    .table_print_labo td{ 
        border: none !important;
        line-height: 20px;
        padding: 0;
        margin: 0;
    }
    div.print-footer {display: none;} 
</style>
<style type="text/css" media="print"> 
    table td { color: #000;} 
    .table{
        font-size: 12px !important;
    }  
    .table td.first {
        border-left: 1px solid #000;       
    }
    .table td {
        border-right: 1px solid #000;
        border-bottom: 1px solid #000;
        padding: 6px 6px 6px 12px;
        color: #000;
    }
    .table_print th{
        background: none;
    }
    .table_print_labo td{ 
        border: none !important;
        line-height: 20px;
        padding: 0;
        margin: 0;
    }
    .none-border{
        border: none !important;
        font-size: 12px !important;
    }
    .none-border tr td{
        border: none !important;
        font-size: 12px !important;
    }
    tr{
         page-break-inside:avoid;
    }
    p{
        font-size: 12px !important;
    } 
    div.print-footer {display: block; width: 100%; position: fixed; bottom: 2px; font-size: 11px; text-align: center;}
    @page
    {
        /*this affects the margin in the printer settings*/  
        margin: 5mm 7mm 5mm 10mm;
    }    
</style>
<div class="print_doc">
    <?php
    require_once("includes/function.php");
    foreach ($consultation as $consultation):
        ?>
        <div style="width: 45%;float: left;margin-top: 20px;">
            <table cellspacing='0' cellpadding='0' style="width: 100%;border: 0;">
                <tr>
                    <td style="vertical-align: top;text-align: left;">
                        <img alt="" src="<?php echo $this->webroot; ?>img/logo_s.png"  style="width:80%;"/>
                    </td>
                </tr>
            </table>    
        </div>        
        <div class="clear"></div>
        <div style="width: 100%;">
            <h2 style="text-align : center ; font-size: 18px ;text-decoration: underline"><?php echo TITLE_MEDICAL_REPORT; ?></h2>
        </div>
        
        
        <table cellpadding="5" cellspacing="0" style="width: 100%; color: #083181;">
            <tr>
                <td><?php echo PATIENT_NAME; ?>: <?php echo $consultation['Patient']['patient_name'];?></td>                
            </tr>  
            <tr>
                <td>
                    <?php echo TABLE_SEX; ?>: 
                    <?php 
                    $gender = "";
                    if($consultation['Patient']['sex'] == "F"){
                        $gender = GENERAL_FEMALE;
                    }  else {
                        $gender = GENERAL_MALE;
                    }
                    echo $gender;
                    ?>
                </td>
            </tr>
            <tr>    
                <td>
                    <?php echo TABLE_DOB; ?>:
                    <?php                   
                    $dob = "";
                    if($consultation['Patient']['dob']!="0000-00-00" || $consultation['Patient']['dob']!=""){
                        echo date('d/m/Y', strtotime($consultation['Patient']['dob'])).'&nbsp;&nbsp;&nbsp;&nbsp;';
                        $dob = getAgePatient($consultation['Patient']['dob']);
                    }                    
                    echo TABLE_AGE.': ';                    
                    echo $dob;
                    ?>
                </td>
            </tr>
        </table>
        <table class="table" cellspacing='0' cellpadding='0'>            
             <!-- Virtal Sign  -->
            <tr>
                <td class="first" style="width:15%; border-top: 1px solid #000;">
                    <span class="title-report"><?php echo MENU_VITAL_SING; ?> </span>
                </td>
                <td style="width:85%; border-top: 1px solid #000;">
                    <table class="none-border" style="width: 100%;border: none !important;" cellspacing="0">
                        <tr>
                            <td style="width: 15%;"><?php echo TABLE_HEIGHT; ?></td>
                            <td style="width: 20%;">: <?php echo $consultation['PatientVitalSign']['height']; ?> cm</td>
                            <td style="width: 15%;"><?php echo TABLE_WEIGHT; ?></td>
                            <td style="width: 20%;">: <?php echo $consultation['PatientVitalSign']['weight']; ?> kg</td>
                            <td style="width: 10%;"><?php echo TABLE_BMI; ?></td>
                            <td style="width: 20%;" class="BMI">: 
                                <?php
                                if($consultation['PatientVitalSign']['height']>0 && $consultation['PatientVitalSign']['weight']>0){
                                    echo $consultation['PatientVitalSign']['BMI'];
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 15%;"><?php echo TABLE_PULSE; ?></td>
                            <td style="width: 20%;">: <?php echo $consultation['PatientVitalSign']['pulse']; ?> /mn</td>
                            <td style="width: 15%;"><?php echo TABLE_RESPIRATORY; ?></td>
                            <td style="width: 20%;">: <?php echo $consultation['PatientVitalSign']['respiratory']; ?> /m</td>                     
                        </tr>
                        <tr>
                            <td style="width: 15%;"><?php echo TABLE_TEMPERATURE; ?></td>
                            <td style="width: 20%;">: <?php echo $consultation['PatientVitalSign']['temperature']; ?> °C</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <!-- Chief Complain -->
            <tr>
                <td class="first" style="width: 20%;">
                    <span class="title-report"><?php echo TABLE_CHIEF_COMPLAIN; ?> </span>
                </td>
                <td style="width: 80%">
                    <table style="width:100%">
                        <tr>
                            <?php if(!empty($consultation['PatientConsultation']['chief_complain'])) { ?>
                            <td style="border: none;">
                               <?php echo nl2br($consultation['PatientConsultation']['chief_complain']); ?>
                            </td>
                            <?php } ?>
                        </tr>
                    </table>
                </td>
            </tr>
            <!-- Medical History -->
            <tr>
                <td class="first" style="width: 20%;">
                    <span class="title-report"><?php echo MEDICAl_HISTORY; ?> </span>
                </td>
                <td style="width: 80%">
                    <table style="width:100%">
                        <tr>
                            <td style="border: none;"><?php echo nl2br($consultation['PatientConsultation']['medical_history']); ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <!-- Past History -->
            <tr style="display: none;">
                <td class="first" style="width: 20%;">
                    <span class="title-report"><?php echo TABLE_PAST_HISTORY; ?> </span>
                </td>
                <td style="width: 80%;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width:20%; border: none"><?php echo PAST_MEDICAl_HISTORY ." : "?></td>
                            <td style="border: none"><?php echo nl2br($consultation['PatientConsultation']['past_medical_history']); ?></td>
                        </tr>
                        <tr>
                            <td style="width:20%; border: none"><?php echo MEDICAL_SURGERY_HISTORY ." : "?></td>
                            <td style="border: none"><?php echo nl2br($consultation['PatientConsultation']['medical_surgery']); ?></td>
                        </tr>
                        <tr>
                            <td style="widht:20%; border: none"><?php echo TABLE_FAMILY_HISTORY." : "; ?> </td>
                            <td style="border: none"><?php echo nl2br($consultation['PatientConsultation']['family_history']); ?> </td>
                        </tr>
                    </table>   
                </td>
            </tr>
            <!-- Physical Examination -->
            <tr>
                <td class="first" style="width: 20%;">
                    <span class="title-report"><?php echo PHYSICAL_EXAMINATION; ?> </span>
                </td>
                <td style="width: 80%;">
                    <table style="width:100%; border: none">
                        <tr>
                             <td style="width:80%; border: none"><?php echo nl2br($consultation['PatientConsultation']['physical_examination']); ?></td>
                        </tr>
                        <tr style="display: none;">
                            <td colspan= "2" style="width: 80%; border: none;">
                                <?php if(!empty($consultation['GenitoUrinarySystem']['other'])){ ?>
                                <table style="width: 100% ; border: none" >
                                    <tr>
                                        <td colspan="2" style="border:none">
                                            <?php echo "Other : ". nl2br($consultation['GenitoUrinarySystem']['other']) ?>

                                        </td> 
                                    </tr>
                                </table>
                                <?php }else if( !empty ($consultation['GenitoUrinarySystem']['size']) || !empty ($consultation['GenitoUrinarySystem']['surface']) ||!empty ($consultation['GenitoUrinarySystem']['consistency']) ||!empty ($consultation['GenitoUrinarySystem']['median_sulcus'] ) || !empty ($consultation['GenitoUrinarySystem']['pain']) || !empty ($consultation['GenitoUrinarySystem']['no_dule'])){ ?>
                                <table border="0" style="width: 100%; border: none;" id="tblProstate">
                                    <h4 style="text-align:left">Prostate : </h4>
                                        <tr>                                     
                                            <td style="border: none;">Size : </td>
                                            <td style="border: none;">
                                                <label><input style="width:50% ;" type="checkbox" class="radio" value="enlarge" id="SizeEnlarge" name="data[PatientConsultation][size][]" <?php echo $consultation['GenitoUrinarySystem']['size'] == "enlarge" ? 'checked' : '' ; ?>  >Enlarge</label>
                                            </td>
                                            <td style="border: none;">    
                                                <label><input style="width:50% ;" type="checkbox" class="radio" value="normal"  id="SizeNormal" name="data[PatientConsultation][size][]" <?php echo $consultation['GenitoUrinarySystem']['size'] == "normal" ?  'checked'  : '' ; ?>  >Normal</label>
                                            </td>
                                        </tr>
                                         <tr>                                     
                                            <td style="border: none;">Surface : </td>
                                            <td style="border: none;">
                                                <label ><input style="width:50%" type="checkbox" class="radio" value="smooth" name="data[PatientConsultation][surface][]" <?php echo $consultation['GenitoUrinarySystem']['surface'] == "smooth" ?  'checked'  : '' ; ?>/>Smooth</label>
                                            <td style="border: none;">
                                                <label><input style="width:50%" type="checkbox" class="radio" value="irregular" name="data[PatientConsultation][surface][]" <?php echo $consultation['GenitoUrinarySystem']['surface'] == "irregular" ?  'checked'  : '' ; ?> />Irregular</label>
                                            </td>
                                        </tr>
                                        <tr>                                     
                                            <td style="border: none;">Consistency : </td>
                                            <td style="border: none;">
                                                <label><input style="width:50%" type="checkbox" class="radio" value="firm" name="data[PatientConsultation][consistency][]" <?php echo $consultation['GenitoUrinarySystem']['consistency'] == "firm" ?  'checked'  : '' ; ?> />Firm</label>
                                            </td>
                                            <td style="border: none;">
                                                <label><input style="width:50%" type="checkbox" class="radio" value="elastic" name="data[PatientConsultation][consistency][]" <?php echo $consultation['GenitoUrinarySystem']['consistency'] == "elastic" ?  'checked'  : '' ; ?> />Elastic</label>                     
                                            </td>
                                        </tr>
                                        <tr>                                     
                                            <td style="border: none;">Median Sulcus : </td>
                                            <td style="border: none;">
                                                <label><input style="width:50%" type="checkbox" class="radio" value="obliterated" name="data[PatientConsultation][median_sulcus][]" <?php echo $consultation['GenitoUrinarySystem']['median_sulcus'] == "obliterated" ?  'checked'  : '' ; ?> />Obliterated</label>
                                            </td>
                                            <td style="border: none;">
                                                <label><input style="width:50%" type="checkbox" class="radio" value="absent" name="data[PatientConsultation][median_sulcus][]" <?php echo $consultation['GenitoUrinarySystem']['median_sulcus'] == "absent" ?  'checked'  : '' ; ?> />Absent</label>                 
                                            </td>
                                        </tr>
                                        <tr>                                     
                                            <td style="border: none;">Pain : </td>
                                            <td style="border: none;">
                                                <label><input style="width:50%" type="checkbox" class="radio" value="yes" name="data[PatientConsultation][pain][]" <?php echo $consultation['GenitoUrinarySystem']['pain'] == "yes" ?  'checked'  : '' ; ?>/>Yes</label>
                                            </td>
                                            <td style="border: none;">
                                                <label><input style="width:50%" type="checkbox" class="radio" value="no" name="data[PatientConsultation][pain][]" <?php echo $consultation['GenitoUrinarySystem']['pain'] == "no" ?  'checked'  : '' ; ?> />No</label>
                                            </td>
                                        </tr>
                                        <tr>                                     
                                            <td style="border: none;">Nodule : </td>
                                            <td style="border: none;">
                                                <label><input style="width:50%" type="checkbox" class="radio" value="yes" name="data[PatientConsultation][no_dule][]" <?php echo $consultation['GenitoUrinarySystem']['no_dule'] == "yes" ?  'checked'  : '' ; ?>/>Yes</label>
                                            </td>
                                            <td style="border: none;">
                                                <label><input style="width:50%" type="checkbox" class="radio" value="no" name="data[PatientConsultation][no_dule][]" <?php echo $consultation['GenitoUrinarySystem']['no_dule'] == "no" ?  'checked'  : '' ; ?> />No</label>
                                            </td>
                                        </tr>
                                    </table>
                               
                                <?php } ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <!-- Labolatory -->
            <tr>
                <td class="first">
                    <span class="title-report"><?php echo MENU_LABO_MANAGEMENT;?> </span>
                </td>
                <td>
                    <table width="100%" style="border:none;">
                        <?php      
                        if(!empty($consultation['QeuedLabo']['id'])){
                            $queryLaboRequest = mysql_query("SELECT labg.name, labg.code FROM labos As la "
                                                    . "INNER JOIN labo_requests As lar ON la.id = lar.labo_id "
                                                    . "INNER JOIN  labo_item_groups As labg ON labg.id = lar.labo_item_group_id "
                                                    . "WHERE lar.is_active = 1 AND la.status > 0 AND la.queued_id = {$consultation['QeuedLabo']['id']}");
                            while ($rowLaboRequest = mysql_fetch_array($queryLaboRequest)) {
                                ?>
                                <tr>
                                    <td style="width: 5%; border :none; display: none;"><input type="checkbox" checked="checked" disabled="disabled" /></td>
                                    <td style="border :none;"><label>- <?php echo $rowLaboRequest['name'];?></label></td>
                                </tr>
                                <?php
                            }
                        }
                       ?>   
                    </table>
                </td>
            </tr>            
            <!-- Diagnosis -->
            <tr>
                <td class="first" style="width: 20%">
                    <span class="title-report"><?php echo TABLE_DAIGNOSTIC; ?> </span>
                </td>
                <td style="width:80%">
                    <table width="100%" style="border:none;">
                        <tr>
                            <td style="border :none"><?php echo  nl2br($consultation['PatientConsultation']['daignostic'])." <br/>".nl2br($consultation['PatientConsultation']['daignostic_other_info'])  ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="first" style="width: 20%">
                    <span class="title-report"><?php echo MENU_PRESCRIPTION; ?> </span>
                </td>
                <td style="width:80%">
                    <?php                                         
                        $queryPrscription = mysql_query("SELECT * FROM orders WHERE orders.status > 0 AND orders.queue_doctor_id = {$consultation['QeuedDoctor']['id']}");
                        $resultPrescription = mysql_fetch_array($queryPrscription);
                        if(mysql_num_rows($queryPrscription)){
                        ?>                            
                            <div id="contentDoctor">
                                <table style="border: none;">                                    
                                    <?php
                                    $index = 0;
                                    $queryPrscriptionDetail = mysql_query("SELECT *, products.name, products.code, uoms.abbr FROM order_details INNER JOIN products ON products.id = order_details.product_id INNER JOIN uoms ON uoms.id = order_details.qty_uom_id WHERE order_details.order_id = {$resultPrescription['id']}");
                                    while ($orderDetail = mysql_fetch_array($queryPrscriptionDetail)) {
                                        $productName = $orderDetail['name'];                               
                                    ?>
                                        <tr>
                                            <td style="border:none;">
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
                                            <td style="border:none;">
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
                                    ?>                
                                </table>
                            </div>
                        <?php }?> 
                </td>
            </tr>
            <tr>
                <td class="first">
                    <span class="title-report"><?php echo MENU_REMARKS; ?> </span>
                </td>
                <td>
                    <table width="100%" style="border:none;">
                        <tr>
                            <td style="border :none"><?php echo nl2br($consultation['PatientConsultation']['remark']); ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="first">
                    <span class="title-report"><?php echo MENU_APPOINTMENT_MANAGEMENT;?> </span>
                    <br/>
                </td>
                <td>
                    <?php 
                    $appointmentDate = "";
                    $appointmentDesc = "";
                    $queryAppointment = mysql_query("SELECT app_date, description FROM appointments WHERE queue_doctor_id = {$consultation['QeuedDoctor']['id']}");
                    while ($rowAppointment = mysql_fetch_array($queryAppointment)) {
                        $appointmentDate = date('d/m/Y', strtotime($rowAppointment['app_date']));
                        $appointmentDesc = $rowAppointment['description'];
                    }
                    ?>
                    <table width="100%" style="border:none;">
                        <tr>
                            <td style="border :none"><?php echo $appointmentDate; ?></td>
                        </tr>
                        <tr>
                            <td style="border :none"><?php echo nl2br($appointmentDesc);?></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="first">
                    <span class="title-report"><?php echo MENU_FOLLOW_UP;?> </span>
                </td>
                <td>
                    <table width="100%" style="border:none;">
                        <tr>
                            <td style="border :none"><?php echo nl2br($consultation['PatientConsultation']['follow_up']); ?></td>
                        </tr>
                        <?php
                        $index = 1;
                        $query_followup = mysql_query("SELECT * FROM patient_followups WHERE is_active=1 AND patient_consultation_id=" . $consultation['PatientConsultation']['id']);
                        while ($data_followup = mysql_fetch_array($query_followup)) {
                            ?>
                            <tr>
                                <td style="border: none; font-weight: bold;"><?php echo date('d/m/Y H:i:s', strtotime($data_followup['created'])) .' - '. getDoctor($data_followup['created_by']); ?></td>
                                <td style="border: none; font-weight: bold;"></td>
                            </tr>
                            <tr>
                                <td style="border: none; padding-left: 5%;" colspan="2">
                                    <?php echo nl2br($data_followup['followup']);?>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="first">
                    <span class="title-report"><?php echo TABLE_REPORT_WRITTEN_BY; ?> </span>
                    <br/><br/><br/>
                    <span class="title-report"><?php echo TABLE_DATE; ?> </span>
                </td>
                <td>
                    <?php echo getDoctor($consultation['PatientConsultation']['created_by']);?>
                    <br/><br/><br/>
                    <?php echo date("d/m/Y H:i:s", strtotime($consultation['PatientConsultation']['created'])); ?>
                </td>
            </tr>
        </table>
    <?php endforeach; ?>
    <br />
    <div style="clear:both;"></div>
    <div class="print-footer" style="position: fixed; bottom: 0; text-align: center; width: 100%;">
        <center style="">
            <table style="width:100% ;">
                <tr>
                    <td rowspan="2" style="font-size:10pt; width: 75%; font-family:'Times New Roman'; vertical-align: top;"><?php echo $this->data['Branch']['address'] ?></td>
                    <td style="font-size:10pt ; width: 25% ; font-family:'Times New Roman'">Tel : <?php echo $this->data['Branch']['telephone'] ?></td>
                </tr>
                <tr>                    
                    <td style="font-size:10pt ;font-family:'Times New Roman'">Email : <?php echo $this->data['Branch']['email_address'] ?></td>
                </tr>
            </table>
        </center>
    </div>
    <div style="float:left;width: 450px">
        <div>
            <input type="button" value="<?php echo ACTION_PRINT; ?>" id='btnDisappearPrint' class='noprint'>
        </div>
    </div>
    <div style="clear:both"></div>
</div>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(document).dblclick(function() {
            window.close();
        });
        $("#btnDisappearPrint").click(function() {
            $("#footerTablePrint").show();
            $("#footerTablePrint").css("width", "100%");
            try
            {
                jsPrintSetup.setOption('scaling', 100);
                jsPrintSetup.clearSilentPrint();
                jsPrintSetup.setOption('printBGImages', 1);
                jsPrintSetup.setOption('printBGColors', 1);
                jsPrintSetup.setSilentPrint(1);

                // Choose printer using one or more of the following functions
                // jsPrintSetup.getPrintersList...
                // jsPrintSetup.setPrinter...
                // we add douplicate \\ for it working, if user use share printer
                jsPrintSetup.setPrinter('Udaya-A4');

                jsPrintSetup.print();
                window.close();
            }
            catch (err)
            {
                //Default printing if jsPrintsetup is not available
                window.print();
                window.close();
            }
        });
    });
</script>