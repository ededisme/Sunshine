<style type="text/css" media="screen">
    div.print-footer {display: none;}   
    table tr th{
        text-align: center !important;
    }
    p{
        line-height: 20px;
        vertical-align: baseline;
        word-wrap: break-word;
    }
    h2{
        font-size: 18px;
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
    h2{
        font-size: 18px;
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
<div id="printQuotationPatient" class="print_doc">
    <table style="width: 100%;">
        <tr>
            <td style="vertical-align: top;text-align: left;width: 15%;"></td>
            <td style="text-align: right;">            
                <h2 style="text-align: center !important;font-size: 18px;">
                    <?php echo TITLE_KINGDOM_OF_CAMOBODIA;?>
                    <br/>
                    <?php echo TITLE_NATION_RELIGION;?>
                </h2>
            </td>
        </tr> 
        <tr>
            <td style="vertical-align: top;text-align: left;">
                <img style="width: 120px;" alt="" src="<?php echo $this->webroot; ?>img/logo_s.png" />           
            </td>      
            <td style="width: 90%;text-align: center;">
                <h2 style="font-size: 18px;line-height: 25px"><?php echo GENERAL_COMPANY_NAME_KH; ?></h2>
                <h2 style="font-size: 14px;line-height: 0px;"><?php echo GENERAL_COMPANY_NAME_EN; ?></h2>
            </td>
        </tr>       
    </table>
    <table style="width: 100%;">
        <tr>
            <td style="text-align: right;">
                <?php echo TABLE_HN?>: <?php echo $patient['PatientIpd']['ipd_code']; ?>
            </td>
        </tr>         
    </table>
    <h2 style="text-align: center;text-decoration: underline;text-transform: uppercase;font-size: 18px;"><?php echo MENU_PATIENT_IPD_CERTIFICATE;?></h2>
    
    <table style="width: 100%;">
        <tr>
            <td colspan="3">
                <p><?php echo TABLE_PHYSICIAN_SERVICE;?>: <?php echo $department['Group']['name']; ?></p>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <?php echo PATIENT_CODE?>: <?php echo $patient['Patient']['patient_code']; ?>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <?php echo PATIENT_NAME?>: <?php echo $patient['Patient']['patient_name']; ?>
            </td>
        </tr>
        <tr>                                    
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
            <td>
                <?php echo TABLE_NATIONALITY;?>: 
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
            <td colspan="3">
                <?php echo TABLE_ADDRESS?>: <?php echo $patient['Patient']['address']; ?>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <?php echo TABLE_OCCUPATION?>: <?php echo $patient['Patient']['occupation']; ?>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <?php echo TABLE_ALLERGIC?>: <?php echo $patient['PatientIpd']['allergies']; ?>
            </td>            
        </tr>
        <tr>
            <td colspan="3">
                <?php echo TABLE_PATIENT_IS_HOSPITALIZED;?>: <?php echo $department['Group']['name']; ?> <?php echo TABLE_TURE;?>
            </td>
        </tr>        
        <tr>
            <td colspan="3">
                <p>
                    <?php echo TABLE_DATE_CERTIFICATE_START;?>: <?php echo date("d/m/Y", strtotime($patient['PatientIpdCertificate']['date_certificate_from']));?>
                    <?php echo TABLE_DATE_CERTIFICATE_END;?> <?php echo date("d/m/Y", strtotime($patient['PatientIpdCertificate']['date_certificate_to']));?>
                </p>
            </td>
        </tr>        
    </table>  
    <br/>
    <span style="float: left;font-size: 14px;">
        <p style="text-align: left;padding-bottom: 30px;"><?php echo GENERAL_SEEN; ?></p>
        <br />   
        <p style="text-align: left;"><?php echo GENERAL_HOPITAL_DIRECTOR; ?></p>
    </span>
    <span style="float: right;font-size: 14px;">    
        <p style="text-align: left;padding-right: 100px;padding-bottom: 30px;"><?php echo GENERAL_CREATED_PHNOM_PENH; ?>
            <?php list($year, $month, $day) = split('-', substr($patient['PatientIpdCertificate']['created'], 0, 10)); ?>            
            <?php echo GENEARL_DATE;?>: <?php echo $day; ?>/<?php echo $month; ?>/<?php echo $year; ?>
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