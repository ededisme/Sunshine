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
            <td style="vertical-align: top;text-align: left;width: 10%;">
                <img alt="" src="<?php echo $this->webroot; ?>img/logo_s.png" />           
            </td>      
            <td style="width: 90%;text-align: left;">
                <h2 style="font-size: 18px;line-height: 25px"><?php echo GENERAL_COMPANY_NAME_KH; ?></h2>
                <h2 style="font-size: 14px;line-height: 0px;"><?php echo GENERAL_COMPANY_NAME_EN; ?></h2>
            </td>
        </tr>       
    </table>
    <h2 style="text-align: center;text-decoration: underline;text-transform: uppercase;"><?php echo 'OBSERVATION MEDICAL';?></h2>
    <table style="width: 100%;">
        <tr>
            <td style="text-align: right;">
                <?php echo TABLE_EMERGENCY_CODE?>: <?php echo $result['PatientEmergency']['emergency_code']; ?>
            </td>
        </tr>
        <tr>
            <td style="text-align: right;">
                <?php 
                echo GENEARL_DATE.': '.date('d/m/Y H:i:s', strtotime($result['PatientEmergencyObservation']['created']));
                ?>
            </td>
        </tr> 
    </table>
    <table style="width: 100%;">           
        <tr>
            <td class="first">I</td>     
            <td>
                <span class="titleItem"><?php echo 'Entry Motif'?></span>                
            </td>
        </tr>
        <tr>
            <td class="first">&nbsp;</td>
            <td><p><?php echo $result['PatientEmergencyObservation']['entry_motif'];?></p></td>
        </tr>
        <tr>
            <td class="first">II</td>     
            <td>
                <span class="titleItem"><?php echo 'Present Medical Condition'?></span>                
            </td>
        </tr>
        <tr>
            <td class="first">&nbsp;</td>
            <td><p><?php echo $result['PatientEmergencyObservation']['present_medical_condition'];?></p></td>
        </tr>
        
        <tr>
            <td class="first">III</td>     
            <td>
                <span class="titleItem"><?php echo 'Past Medical History'?></span>                
            </td>
        </tr>
        <tr>
            <td class="first">&nbsp;</td>
            <td>
                <p><?php echo '- Medical'; ?> : <?php echo nl2br($result['PatientEmergencyObservation']['medical']);?></p>
                <p><?php echo '- Surfical'; ?> : <?php echo nl2br($result['PatientEmergencyObservation']['surfical']);?></p>
            </td>
        </tr>
        <tr>
            <td class="first">IV</td>     
            <td>
                <span class="titleItem"><?php echo 'Clinical Examination'?></span>                
            </td>
        </tr>
        <tr>
            <td class="first">&nbsp;</td>
            <td>
                <p><?php echo '- General Sign'; ?> : <?php echo $result['PatientEmergencyObservation']['general_sign'];?></p>
                <p><?php echo '- Cardiovascular'; ?> : <?php echo $result['PatientEmergencyObservation']['cadiovascular'];?></p>
                <p><?php echo '- Respiratory'; ?> : <?php echo $result['PatientEmergencyObservation']['respiratory'];?></p>
                <p><?php echo '- Digestifs'; ?> : <?php echo $result['PatientEmergencyObservation']['digestifs'];?></p>
                <p><?php echo '- Uro-Genital'; ?> : <?php echo $result['PatientEmergencyObservation']['uro_genital'];?></p>
            </td>
        </tr>
    </table>   
    <div style="clear:both"></div>
    <br />        
    <div style="float:left;width: 450px;">
        <div>
            <input type="button" value="<?php echo ACTION_PRINT; ?>" id='btnDisappearPrint' class='noprint'>                
        </div>
    </div>
</div>

<div id="footerInfoFix" class="print-footer">
    <?php echo $this->element('print_footer_address_fix'); ?>
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