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
    h2{ font-size: 18px;}
</style>
<div id="printQuotationPatient" class="print_doc">
    <table style="width: 100%;">   
        <tr>
            <td style="vertical-align: top;text-align: left;width: 15%;">
                <img alt="" src="<?php echo $this->webroot; ?>img/logo_s.png" />           
            </td>
            <td style="text-align: center;">            
                <h2 style="font-size: 18px;margin: -18px 0px -18px;text-decoration: underline;"><?php echo MENU_QUOTATION_PATIENT_MANAGEMENT;?></h2>                      
            </td>
        </tr>       
    </table>
    <table style="width: 100%;">
        <tr>
            <td style="text-align: left;">
                <?php echo QUOTATION_CODE?>: <?php echo $patient['PatientQuotation']['quotation_code']; ?>
            </td>        
            <td style="text-align: right;">
                <?php list($year, $month, $day) = split('-', substr($patient['PatientQuotation']['created'], 0, 10)); ?>            
                <?php echo GENEARL_DATE;?>:<?php echo $day; ?>/<?php echo $month; ?>/<?php echo $year; ?>
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

    </table>
    <div id="dynamic">
        <table id="example" class="table" cellspacing="0">
            <tr>            
                <th class="first" style="width: 30%;"><?php echo TABLE_PROCEDURE; ?></th>
                <th style="width: 10%;"><?php echo TABLE_QTY; ?></th>
                <th style="width: 10%;"><?php echo GENERAL_PRICE; ?> ($)</th>
                <th style="width: 10%;"><?php echo GENERAL_AMOUNT; ?> ($)</th>
                <th style="width: 20%;"><?php echo DRUG_NOTE; ?></th>
            </tr>        
            <?php        
            $index = 1;
            $queryService = mysql_query("SELECT ser.*, sec.name As sectionName, pqsd.price, pqsd.description, pqsd.qty 
                                            FROM patient_quotation_service_details AS pqsd 
                                            INNER JOIN patient_quotations AS pq ON pq.id = pqsd.patient_quotation_id
                                            INNER JOIN services AS ser ON ser.id = pqsd.service_id 
                                            INNER JOIN sections AS sec ON sec.id = ser.section_id 
                                            WHERE pqsd.is_active=1 AND pqsd.patient_quotation_id = '".$patient['PatientQuotation']['id']."' 
                                            ORDER BY pqsd.id ASC");
            while ($resultService = mysql_fetch_array($queryService)) {                            
                ?>
                <tr>
                    <td class="first"><?php echo $resultService['name'];?></td>
                    <td style="text-align: center;"><?php echo $resultService['qty'];?></td>
                    <td style="text-align: right;"><?php echo number_format($resultService['price'], 2);?></td>
                    <td style="text-align: right;"><?php echo number_format($resultService['price']*$resultService['qty'], 2);?></td>
                    <td><?php echo $resultService['description'];?></td>
                </tr>
            <?php }?>        
        </table>
        <h4><?php __(MENU_EXCLUDE); ?></h4>
        <?php        
            $index = 1;
            $queryExclude = mysql_query("SELECT eq.* FROM patient_quotation_exclude_details AS pqed INNER JOIN exclude_quotations AS eq ON eq.id = pqed.exclude_quotation_id WHERE pqed.is_active=1 AND pqed.patient_quotation_id = ".$patient['PatientQuotation']['id']);
            while ($resultExclude = mysql_fetch_array($queryExclude)) {
                echo '<p>'.$index++.'- '.$resultExclude['name_' . $_SESSION['lang']].'</p>';
            }
        ?>    
    </div>
    <br />
    <br />
    <span style="float: left;font-size: 14px;">
        <p style="text-align: left;"><?php echo GENERAL_PROVIDED_BY; ?></p>
        <br />
        <br />
        <br />
        <p style="text-align: left;">.......................................................</p>
        <p style="text-align: left;"><?php echo GENERAL_SIGNATURE; ?></p>    
        <p style="text-align: left;"><?php echo GENERAL_HOPITAL_STAFF; ?></p>
    </span>
    <span style="float: right;font-size: 14px;">    
        <p style="text-align: left;"><?php echo GENERAL_AGREED_BY; ?></p>   
        <br />
        <br />
        <br />    
        <p style="text-align: left;">.......................................................</p>
        <p style="text-align: left;"><?php echo GENERAL_SIGNATURE; ?></p>    
        <p style="text-align: left;"><?php echo GENEARL_RELATIONSHIP; ?>......................................</p>
    </span>
    <div style="clear:both"></div>
    <br />        
    <div style="float:left;width: 450px;">
        <div>
            <input type="button" value="<?php echo ACTION_PRINT; ?>" id='btnDisappearPrint' class='noprint'>                
        </div>
    </div>
    <div style="clear:both"></div>
</div>
<div id="footerInfoFix" class="print-footer">
    <?php echo $this->element('print_footer_quotation_fix'); ?>
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