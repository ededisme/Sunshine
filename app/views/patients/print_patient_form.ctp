<style type="text/css" media="screen">
    div.print-footer {display: none;}    
</style>

<style type="text/css" media="print">
    div.print_doc { width:100%; }
    div.print-footer { display: block; width: 100%;}
    input[type="checkbox"] { transform:scale(1.6, 1.6);}
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
<?php 
include('includes/function.php'); ?> 
<div id="printQuotationPatient" class="print_doc">
    <table style="width: 100%;">   
        <tr>
            <td style="vertical-align: top;text-align: center;">
                <img style="width: 40%;" alt="" src="<?php echo $this->webroot; ?>img/logo_s.png" />           
            </td>
        </tr>
        <tr>
            <td style="text-align: center;">            
                <h2>
                    <?php echo TITLE_REGISTER_PATIENT_FORM;?>                   
                </h2>
            </td>
        </tr>
    </table>
    <table style="width: 100%;">
        <tr>            
            <td style="text-align: right;">
                <?php list($year, $month, $day) = split('-', substr($patient['Patient']['created'], 0, 10)); ?>            
                <p><?php echo GENEARL_DATE;?>:<?php echo $day; ?>/<?php echo $month; ?>/<?php echo $year; ?></p>            
            </td>
        </tr> 
    </table>
    <br/>
    <table cellpadding="3" style="width: 100%;">
        <tr>
            <td>
                <?php echo PATIENT_CODE;?>: <?php echo $patient['Patient']['patient_code']; ?>
            </td>
            <td>
                <?php echo PATIENT_NAME;?>: <?php echo $patient['Patient']['patient_name']; ?>
            </td>    
            <td>
                <?php echo TABLE_SEX;?>:
                <?php  
                if($patient['Patient']['sex']=="M") {
                    echo GENERAL_MALE;
                }else {
                    echo GENERAL_FEMALE;
                }
                ?>   
            </td>
        </tr>
        <tr>
            <td style="width: 35%;">
                <?php echo TABLE_DOB;?>: <?php echo $patient['Patient']['dob']?>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?php 
                echo TABLE_AGE.': ';
                echo getAgePatient($patient['Patient']['dob']);                
                ?>               
            </td>
            <td style="width: 30%;">
                <?php echo TABLE_TELEPHONE;?>: 
                <?php
                if($patient['Patient']['telephone']!=""){
                    echo $patient['Patient']['telephone'];
                }else{
                    echo str_pad('', 65, '.', STR_PAD_RIGHT);
                }                
                ?>
            </td>
            <td style="width: 30%;" style="white-space:pre-wrap !important; "><?php echo TABLE_EMAIL;?>: 
                <?php 
                if($patient['Patient']['email']!=""){
                    echo $patient['Patient']['email'];
                }else{
                    echo str_pad('', 100, '.', STR_PAD_RIGHT);
                }
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="white-space: nowrap"><?php echo TABLE_ADDRESS;?>: 
                <?php 
                if($patient['Patient']['address']!=""){
                    echo $patient['Patient']['address'];
                }
                ?>
            </td>            
        </tr>       
        <tr>
            <td colspan="3" style="white-space: nowrap">
                <?php echo TABLE_ALLERGIC;?>:
                <?php
                    if($patient['Patient']['allergic_medicine']!=0) {
                        echo ACTION_YES;                        
                    }else{
                        echo ACTION_NO;   
                    }
                ?>
            </td>
        </tr>
    </table>    
    <br />       
    <div style="clear:both;"></div>
    <div class="print-footer" style="position: fixed; bottom: 0; text-align: center; width: 100%;">
        <center style="">
            <table style="width:100% ;">
                <tr>
                    <td rowspan="2" style="font-size:10pt; width: 75%; font-family:'Times New Roman'; vertical-align: top;"><?php echo $branches['Branch']['address'] ?></td>
                    <td style="font-size:10pt ; width: 25% ; font-family:'Times New Roman'">Tel : <?php echo $branches['Branch']['telephone'] ?></td>
                </tr>
                <tr>                    
                    <td style="font-size:10pt ;font-family:'Times New Roman'">Email : <?php echo $branches['Branch']['email_address'] ?></td>
                </tr>
           
            </table>
        </center>
    </div>
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
                jsPrintSetup.setPrinter('Udaya-A5');

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