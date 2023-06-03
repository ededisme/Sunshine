<style type="text/css" media="screen">
    div.print-footer {display: none;}   
    #footerInfoFix {display: none;}
    table tr th{
        text-align: center !important;
    }
    table tr td{ font-size: 14px; }
    h2{
        font-size: 16px;
    }
    p{
        line-height: 20px;
        vertical-align: baseline;
        word-wrap: break-word;
    }
</style>

<style type="text/css" media="print">
    div.print_doc { width:100%; }
    div.print-footer { display: block; width: 100%;}
    #footerInfoFix { display: block; width: 100%;}
    #btnDisappearPrint { display: none;}
    table tr td{ font-size: 14px; }
    @page
    {
        /*this affects the margin in the printer settings*/  
        margin: 5mm 7mm 5mm 10mm;
    }
    p{
        padding: 0 10px;
        margin: 0;
        font-size: 14px;     
        vertical-align: baseline;   
        word-wrap: break-word;
    }
    th{ font-weight: normal; }
</style>
<div id="printPatientEmergency" class="print_doc">       
    <table style="width: 100%;">
        <tr>
            <td style="vertical-align: top;text-align: left;width: 15%;"></td>
            <td style="text-align: right;">            
                <h2 style="text-align: center !important;font-size: 18px;">
                    <?php echo TITLE_KINGDOM_OF_CAMOBODIA; ?>
                    <br/>
                    <?php echo TITLE_NATION_RELIGION; ?>
                </h2>
            </td>
        </tr> 
        <tr>
            <td style="vertical-align: top;text-align: left;width: 10%;">
                <img alt="" src="<?php echo $this->webroot; ?>img/logo_s.png" />           
            </td>      
            <td style="width: 90%;text-align: left;">
                <h2 style="font-size: 20px;line-height: 25px"><?php echo GENERAL_COMPANY_NAME_KH; ?></h2>
                <h2 style="font-size: 16px;line-height: 0px;"><?php echo GENERAL_COMPANY_NAME_EN; ?></h2>
            </td>
        </tr>       
    </table>
    <table style="width: 100%;">
        <tr>
            <td style="text-align: right;">
                <?php echo TABLE_EMERGENCY_CODE ?>: <?php echo $patient['PatientEmergency']['emergency_code']; ?>
            </td>
        </tr>         
    </table>
    <h2 style="text-align: center;text-decoration: underline;text-transform: uppercase;"><?php echo MENU_PATIENT_EMERGENCY_MANAGEMENT_INFO; ?></h2>

    <table cellpadding="3" cellspacing="3" style="width: 100%;">
        <tr>
            <td colspan="3">
                <?php echo TABLE_PATIENT_CHECK_IN_DATE; ?>: <?php echo date("d/m/Y H:i:s", strtotime($patient['PatientEmergency']['date_ipd'])); ?>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <?php echo TABLE_ROOM_NUMBER ?>: <?php echo $patient['Room']['room_name']; ?>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <?php echo TABLE_ADMITTING_PHYSICIAN ?>: <?php echo $doctor['Employee']['name']; ?>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <?php echo TABLE_DEPARTMENT ?>: <?php echo $department['Group']['name']; ?>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <?php echo PATIENT_CODE ?>: <?php echo $patient['Patient']['patient_code']; ?>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <?php echo PATIENT_NAME ?>: <?php echo $patient['Patient']['patient_name']; ?>
            </td>
        </tr>        
        <tr>                                    
            <td>
                <?php
                echo TABLE_AGE . ': ';
                $then_ts = strtotime($patient['Patient']['dob']);
                $then_year = date('Y', $then_ts);
                $age = date('Y') - $then_year;
                if (strtotime('+' . $age . ' years', $then_ts) > time())
                    $age--;

                if ($age == 0) {
                    $then_year = date('m', $then_ts);
                    $month = date('m') - $then_year;
                    if (strtotime('+' . $month . ' month', $then_ts) > time())
                        $month--;
                    echo $month . ' ' . GENERAL_MONTH;
                }else {
                    echo $age . ' ' . GENERAL_YEAR_OLD;
                }
                ?> 
            </td>
            <td>
                <?php echo TABLE_SEX; ?>: 
                <?php
                if ($patient['Patient']['sex'] == "F") {
                    echo GENERAL_FEMALE;
                } else {
                    echo GENERAL_MALE;
                }
                ?>
            </td>
            <td>
                <?php echo TABLE_NATIONALITY; ?>: 
                <?php
                if ($patient['Patient']['patient_group_id'] != "") {
                    $query = mysql_query("SELECT name FROM patient_groups WHERE id=" . $patient['Patient']['patient_group_id']);
                    while ($row = mysql_fetch_array($query)) {
                        if ($patient['Patient']['patient_group_id'] == 1) {
                            echo $row['name'];
                        } else {
                            echo $row['name'] . '&nbsp;&nbsp;(' . $patient['Nationality']['name'] . ')';
                        }
                    }
                } else {
                    echo $patient['Nationality']['name'];
                }
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <?php echo TABLE_ADDRESS ?>: <?php echo $patient['Patient']['address']; ?>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <?php echo TABLE_OCCUPATION ?>: <?php echo $patient['Patient']['occupation']; ?>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <?php echo TABLE_DIAGNOSIS; ?>: <?php echo $patient['PatientEmergency']['diagnostic']; ?>
            </td>
        </tr>        
        <tr>
            <td colspan="3">
                <label for="PatientEmergencyPulse"><?php echo 'Pouls'; ?> :</label>
                <?php echo '<span style="padding-right:20px;">' . $patient['PatientEmergency']['pulse'] . '</span>'; ?>
                <label for="PatientEmergencyBp"><?php echo 'BP'; ?> :</label>
                <?php echo '<span style="padding-right:20px;">' . $patient['PatientEmergency']['bp'] . '</span>'; ?>
                <label for="PatientEmergencyTag"><?php echo 'Tag'; ?> :</label>
                <?php echo '<span style="padding-right:20px;">' . $patient['PatientEmergency']['tag'] . '</span>'; ?>
                <label for="PatientEmergencyRespiratory"><?php echo 'Respiratory'; ?> :</label>
                <?php echo '<span style="padding-right:20px;">' . $patient['PatientEmergency']['respiratory'] . '</span>'; ?>
                <label for="PatientEmergencyGlasgowScore"><?php echo 'Glasgow Score'; ?> :</label>
                <?php echo '<span style="padding-right:20px;">' . $patient['PatientEmergency']['glasgow_score'] . '</span>'; ?>
                <label for="PatientEmergencySpO2"><?php echo 'SpO2'; ?> :</label>
                <?php echo '<span style="padding-right:20px;">' . $patient['PatientEmergency']['SpO2'] . '</span>'; ?>
            </td>            
        </tr>      
    </table>  
    <br/>
    <span style="float: left;font-size: 14px;">
        <p style="text-align: left;padding-bottom: 50px;"><?php echo GENERAL_SEEN; ?></p>
        <br />   
        <p style="text-align: left;"><?php echo GENERAL_HOPITAL_DIRECTOR; ?></p>
    </span>
    <span style="float: right;font-size: 14px;">    
        <p style="text-align: left;padding-right: 100px;padding-bottom: 50px;"><?php echo GENERAL_CREATED_PHNOM_PENH; ?>
            <?php list($year, $month, $day) = split('-', substr($patient['PatientEmergency']['created'], 0, 10)); ?>            
            <?php echo GENEARL_DATE; ?>: <?php echo $day; ?>/<?php echo $month; ?>/<?php echo $year; ?>
        </p>   
        <br /> 
        <p style="text-align: left;padding-right: 80px;"><?php echo GENERAL_PHYSICIAN; ?></p>
    </span>
    <div style="clear:both"></div>
    <br />        
    <div style="float:left;width: 450px;">
        <div>
            <input type="button" value="<?php echo ACTION_PRINT; ?>" id='btnDisappearPrint' class='noprint'>                
        </div>
    </div
    <div style="clear:both"></div>
    <br />        
</div>

<div id="footerInfoFix">
<?php echo $this->element('print_footer_address_fix'); ?>
</div> 
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(document).dblclick(function() {
            window.close();
        });
        $("#btnDisappearPrint").click(function() {
            $("#footerInfoFix").show();
            window.print();
            window.close();
        });
    });
</script>