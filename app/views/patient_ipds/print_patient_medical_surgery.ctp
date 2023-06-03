<style type="text/css" media="screen">
    div.print-footer {display: none;}   
    table tr th{
        text-align: center !important;
    }
</style>

<style type="text/css" media="print">
    div.print_doc { width:100%; }
    div.print-footer { display: block; width: 100%;}
    #btnDisappearPrint { display: none;}
    table tr td{ font-size: 13px; }
    @page
    {
        /*this affects the margin in the printer settings*/  
        margin: 5mm 7mm 5mm 10mm;
    }
    p{
        padding: 0 10px;
        margin: 0;
        font-size: 14px;
    }
    th{ font-weight: normal; }
</style>
<div id="printQuotationPatient" class="print_doc">
    <table style="width: 100%;">   
        <tr>
            <td style="vertical-align: top;text-align: left;width: 15%;">
                <img alt="" src="<?php echo $this->webroot; ?>img/logo_s.png" />           
            </td>
            <td style="width: 25%">
                <h2 style="font-size: 18px;line-height: 25px"><?php echo GENERAL_COMPANY_NAME_KH; ?></h2>
                <h2 style="font-size: 14px;line-height: 0px;"><?php echo GENERAL_COMPANY_NAME_EN; ?></h2>
            </td>
            <td style="text-align: center;">            
                <h2 style="font-size: 14px;margin: -18px 0px -18px;text-decoration: underline;"><?php echo TITLE_TREATMENT_MEDICAL_SURGERY;?></h2>                      
            </td>
        </tr>       
    </table>
    <table style="width: 100%;">
        <tr>
            <td style="text-align: right;">
                <?php list($year, $month, $day) = split('-', substr($patient['PatientIpd']['created'], 0, 10)); ?>            
                <?php echo GENEARL_DATE;?>: <?php echo $day; ?>/<?php echo $month; ?>/<?php echo $year; ?>
            </td>
        </tr> 
    </table>
    <table style="width: 100%;">
        <tr>
            <td>
                <?php echo PATIENT_CODE?>: <?php echo $patient['Patient']['patient_code']; ?>
            </td>
            <td>
                <?php echo PATIENT_NAME?>: <?php echo $patient['Patient']['patient_name']; ?>
            </td>
            <td>
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
            <td>
                <?php echo TABLE_SEX;?>: 
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
            <td>
                <?php echo TABLE_HN?>: <?php echo $patient['PatientIpd']['ipd_code']; ?>
            </td>
            <td>
                <?php echo TABLE_PATIENT_CHECK_IN_DATE?>: <?php echo date("d/m/Y H:i:s", strtotime($patient['PatientIpd']['date_ipd'])); ?>
            </td>
            <td>
                <?php echo TABLE_ROOM_NUMBER?>: <?php echo $patient['Room']['room_name']; ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <?php echo TABLE_ADMITTING_PHYSICIAN?>: <?php echo $doctor['Employee']['name']; ?>
            </td>
            <td>
                <?php echo TABLE_DEPARTMENT?>: <?php echo $department['Group']['name']; ?>
            </td>            
        </tr>
        <tr>
            <td colspan="3">
                <?php echo TABLE_ALLERGIC?>: <?php echo $patient['PatientIpd']['allergies']; ?>
            </td>            
        </tr>
    </table>
    <br />        
    <p>
        <?php echo PARAGRAPH_PATIENT_MEDICAL_SURGERY_ONE;?> : 
        <?php 
        if($patient['PatientIpd']['doctor_explain_to_patient'] != NULL){
            echo $patient['PatientIpd']['doctor_explain_to_patient'];
        }else{
            echo str_pad($patient['PatientIpd']['doctor_explain_to_patient'], 250, '.', STR_PAD_RIGHT);
        }
        ?>
    </p>    
    <p>
        <?php echo PARAGRAPH_PATIENT_MEDICAL_SURGERY_TWO;?> :
        <?php 
        if($patient['PatientIpd']['patient_following_surgical'] != NULL){
            echo $patient['PatientIpd']['patient_following_surgical'];
        }else{
            echo str_pad($patient['PatientIpd']['patient_following_surgical'], 250, '.', STR_PAD_RIGHT);
        }
        ?>
    </p>            
    <p><?php echo PARAGRAPH_PATIENT_MEDICAL_SURGERY_THREE;?></p>        
    <p>
        <?php echo PARAGRAPH_PATIENT_MEDICAL_SURGERY_ACCORDING;?> :
        <?php 
        if($patient['PatientIpd']['according_to_patient'] != NULL){
            echo $patient['PatientIpd']['according_to_patient'];
        }else{
            echo str_pad($patient['PatientIpd']['according_to_patient'], 50, '.', STR_PAD_RIGHT);
        }
        ?>
        <?php echo PARAGRAPH_PATIENT_MEDICAL_SURGERY_NUMBER;?> :
        <?php 
        if($patient['PatientIpd']['according_number'] != NULL){
            echo $patient['PatientIpd']['according_number'];
        }else{
            echo str_pad($patient['PatientIpd']['according_number'], 50, '.', STR_PAD_RIGHT);
        }
        ?>
    </p>
    <p><?php echo PARAGRAPH_PATIENT_MEDICAL_SURGERY_FOUR;?></p>    
    <p><?php echo PARAGRAPH_PATIENT_MEDICAL_SURGERY_FIVE;?></p>    
    
    <p><b><?php echo PARAGRAPH_PATIENT_MEDICAL_SURGERY_SIX;?></b></p>    
    
    <table class="patientIpd" style="width: 100%;">
        <tr>
            <td><?php echo PATIENT_NAME;?>: <?php echo $patient['Patient']['patient_name'];?></td>
            <td><?php echo TABLE_THUMP_PRINTED;?>: <?php echo str_pad('', 25, '.', STR_PAD_RIGHT);?></td>
            <td><?php echo GENEARL_DATE;?>: <?php echo str_pad('', 25, '.', STR_PAD_RIGHT);?></td>
        </tr>
        <tr>
            <td><?php echo TABLE_WITNESS_NAME;?>: 
                <?php 
                if($patient['PatientIpd']['witness_name'] != NULL){
                    echo $patient['PatientIpd']['witness_name'];
                }else{
                    echo str_pad($patient['PatientIpd']['witness_name'], 25, '.', STR_PAD_RIGHT);
                }
                ?>
            </td>
            <td><?php echo TABLE_THUMP_PRINTED;?>: <?php echo str_pad('', 25, '.', STR_PAD_RIGHT);?></td>
            <td><?php echo GENEARL_DATE;?>: <?php echo str_pad('', 25, '.', STR_PAD_RIGHT);?></td>
        </tr>
        <tr>
            <td><?php echo TABLE_WITNESS_NAME_SECOND;?>: 
                <?php echo str_pad('', 25, '.', STR_PAD_RIGHT);?>
            </td>
            <td><?php echo TABLE_THUMP_PRINTED;?>: <?php echo str_pad('', 25, '.', STR_PAD_RIGHT);?></td>
            <td><?php echo GENEARL_DATE;?>: <?php echo str_pad('', 25, '.', STR_PAD_RIGHT);?></td>
        </tr>
        <tr>
            <td><?php echo DOCTOR_NAME;?>: 
                <?php echo str_pad('', 25, '.', STR_PAD_RIGHT);?>
            </td>
            <td><?php echo TABLE_THUMP_PRINTED;?>: <?php echo str_pad('', 25, '.', STR_PAD_RIGHT);?></td>
            <td><?php echo GENEARL_DATE;?>: <?php echo str_pad('', 25, '.', STR_PAD_RIGHT);?></td>
        </tr>
        <tr>
            <td colspan="3">
                <br/>
                <p style="padding-left: 20px;">
                    <?php echo PARAGRAPH_PATIENT_IPD_NOTE;?>                    
                </p>
            </td>
        </tr>
        <tr>
            <td><?php echo TABLE_AUTHORIZE_NAME;?>: 
                <?php 
                if($patient['PatientIpd']['authorized_name'] != NULL){
                    echo $patient['PatientIpd']['authorized_name'];
                }else{
                    echo str_pad($patient['PatientIpd']['authorized_name'], 25, '.', STR_PAD_RIGHT);
                }
                ?>                
            </td>
            <td><?php echo TABLE_THUMP_PRINTED;?>: <?php echo str_pad('', 25, '.', STR_PAD_RIGHT);?></td>
            <td><?php echo GENEARL_DATE;?>: <?php echo str_pad('', 25, '.', STR_PAD_RIGHT);?></td>
        </tr>
        <tr>
            <td><?php echo TABLE_TELEPHONE;?>: 
                <?php 
                if($patient['PatientIpd']['authorized_telephone'] != NULL){
                    echo $patient['PatientIpd']['authorized_telephone'];
                }else{
                    echo str_pad($patient['PatientIpd']['authorized_telephone'], 45, '.', STR_PAD_RIGHT);
                }
                ?>                
            </td>
            <td colspan="2"><?php echo TABLE_ADDRESS_ID_PRESENTED;?>: 
                <?php 
                if($patient['PatientIpd']['authorized_address'] != NULL){
                    echo $patient['PatientIpd']['authorized_address'];
                }else{
                    echo str_pad($patient['PatientIpd']['authorized_address'], 25, '.', STR_PAD_RIGHT);
                }
                ?>
            </td>
        </tr>
         <tr>            
            <td colspan="3">
                <?php echo TABLE_ID_CARD;?>: 
                <?php 
                if($patient['PatientIpd']['authorized_id_card'] != NULL){
                    echo $patient['PatientIpd']['authorized_id_card'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                }else{
                    echo str_pad($patient['PatientIpd']['authorized_id_card'], 45, '.', STR_PAD_RIGHT);
                }
                ?>                  
                <?php echo TABLE_ISSUE_DATE;?>: 
                <?php 
                if($patient['PatientIpd']['authorized_issue_date'] != NULL && $patient['PatientIpd']['authorized_issue_date'] != "0000-00-00"){
                    echo date("d/m/Y", strtotime($patient['PatientIpd']['authorized_issue_date'])).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                }else{
                    echo str_pad('', 25, '.', STR_PAD_RIGHT);
                }
                ?>    
                <?php echo TABLE_EXPIRATION_DATE;?>:                 
                <?php 
                if($patient['PatientIpd']['authorized_expiration_date'] != NULL && $patient['PatientIpd']['authorized_expiration_date']!="0000-00-00"){
                    echo date("d/m/Y", strtotime($patient['PatientIpd']['authorized_expiration_date'])).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                }else{
                    echo str_pad('', 25, '.', STR_PAD_RIGHT);
                }
                ?>                    
                <?php echo TABLE_ISSUE_PLACE;?>: 
                <?php 
                if($patient['PatientIpd']['authorized_issue_place'] != NULL){
                    echo $patient['PatientIpd']['authorized_issue_place'];
                }else{
                    echo str_pad($patient['PatientIpd']['authorized_issue_place'], 25, '.', STR_PAD_RIGHT);
                }
                ?>
            </td>
        </tr>
    </table>
    <span style="float: left;font-size: 14px;">
        <p style="text-align: left;"><?php echo PATIENT_IPD_NOTE_FOOTER;?></p>
        <p style="text-align: left;"><?php echo PATIENT_IPD_NOTE_FOOTER_FIRST;?></p>
        <p style="text-align: left;"><?php echo PATIENT_IPD_NOTE_FOOTER_SECOND;?></p>
    </span>   
    <div style="clear:both"></div>
    <br />        
    <div style="float:left;width: 450px;">
        <div>
            <input type="button" value="<?php echo ACTION_PRINT; ?>" id='btnDisappearPrint' class='noprint'>                
        </div>
    </div>
</div>
<div id="footerInfoFix" class="print-footer">
    <?php echo $this->element('print_footer_patient_ipd_fix'); ?>
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