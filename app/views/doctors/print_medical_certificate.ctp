<style type="text/css" media="print"> 
    @page
    {
        /*this affects the margin in the printer settings*/  
        margin: 10mm 15mm 10mm 20mm;
    }    
</style>
<div class="print_doc">
    <?php
    require_once("includes/function.php");
    foreach ($consultation as $consultation):
        ?>
        <div style="width: 40%;float: left;margin-top: 20px;">
            <table border="0" cellspacing='0' cellpadding='2' style="padding: 0; margin: 0;">
                <tr>
                    <td valign="top" style="text-align: left;">                
                        <img style="width: 75%;" alt="" src="<?php echo $this->webroot; ?>public/company_photo/<?php echo $this->data['Company']['photo']; ?>" /> 
                    </td>
                </tr>
                <tr style="display: none;">
                    <td style="text-align: left">
                        <h2 style="font-size: 12px !important; line-height: 0px; text-transform: uppercase;"><?php echo $this->data['Branch']['name'] ?></h2>         
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 12px !important;">
                        <?php echo $this->data['Branch']['address'] != '' ? 'Address : '.$this->data['Branch']['address'] :''; ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 12px !important;">
                        <?php echo $this->data['Branch']['telephone'] != '' ? 'Tel : '.$this->data['Branch']['telephone'] :''; ?>
                    </td>
                </tr>
            </table>   
        </div>
        <div class="clear"></div>
        <div style="height: 50px;"></div>
        <div style="width: 100%;">
            <table border="0" cellspacing='3' cellpadding='3' style="padding: 0; margin: 0;">
                <tr>
                    <td style="text-align: left; padding-left: 30%;"><h2 style="font-size: 18px; text-decoration: underline; text-transform: uppercase;"><?php echo TITLE_MEDICAL_CERTIFICATE; ?></h2></td>
                </tr>
                <tr>
                    <td style="border: none; width: 15%; font-size: 12px !important; padding-top: 20px;">
                        -   <span style="font-size: 12px !important; padding-left: 15px; font-weight: bold;"><?php echo TABLE_NAME; ?>: </span>
                        <?php echo $consultation['Patient']['patient_name']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="border: none; font-size: 12px !important;">
                        -   <span style="font-size: 12px !important; padding-left: 15px; font-weight: bold;"><?php echo TABLE_SEX; ?>: </span>
                        <?php
                        if ($consultation['Patient']['sex'] == "F") {
                            echo GENERAL_FEMALE;
                        } else {
                            echo GENERAL_MALE;
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td style="border: none; font-size: 12px !important;">
                        -   <span style="font-size: 12px !important; padding-left: 15px; font-weight: bold;"><?php echo TABLE_WEIGHT; ?>: </span>
                        <?php 
                        $queryPatientVital = mysql_query("SELECT weight FROM patient_vital_signs WHERE is_active = 1 AND queued_doctor_id = {$consultation['PatientConsultation']['queued_doctor_id']}");
                        $resultPatientVital = mysql_fetch_array($queryPatientVital);                        
                        ?>
                        <?php echo $resultPatientVital['weight']; ?> kg
                    </td>
                </tr>
                <tr>
                    <td style="border: none; font-size: 12px !important;">
                        -   <span style="font-size: 12px !important; padding-left: 15px; font-weight: bold;"><?php echo TABLE_DOB; ?>: </span>                
                        <?php echo date("d/m/Y", strtotime($consultation['Patient']['dob'])); ?>
                    </td>
                </tr>
                <tr>
                    <td style="border: none; font-size: 12px !important;">
                        -   <span style="font-size: 12px !important; padding-left: 15px; font-weight: bold;"><?php echo TABLE_CHIEF_COMPLAIN; ?>: </span>
                        <?php 
                        if(!empty($consultation['PatientConsultation']['chief_complain'])) { 
                            echo nl2br($consultation['PatientConsultation']['chief_complain']); 
                        }                             
                        ?>
                    </td>
                </tr>  
                <tr>
                    <td style="border: none; font-size: 12px !important;">
                        -   <span style="font-size: 12px !important; padding-left: 15px; font-weight: bold;"><?php echo TABLE_DAIGNOSTIC; ?>: </span>
                        <?php echo $consultation['PatientConsultation']['daignostic']." <br/>".$consultation['PatientConsultation']['daignostic_other_info']; ?></td>
                </tr>          
            </table>    
        </div>
        <div class="clear"></div>
        
        <p style="font-size: 12px !important;"><span style="font-size: 12px !important; font-weight: bold;"><?php echo MENU_REMARKS; ?>: </span><?php echo nl2br($consultation['PatientConsultation']['remark']); ?></p>
        
        <div style="width: 50%; float: right; padding-top: 20%; padding-right: 5%;">
            <table class="table" cellspacing='0' cellpadding='0'>
                <tr>
                    <td style="border: none; text-align: center; font-size: 12px !important;">Phnom Penh, <?php echo date("d/m/Y", strtotime($consultation['PatientConsultation']['created'])); ?></td>
                </tr>
                <tr>
                    <td style="text-align: center; border: none; height: 60px;"></td>
                </tr>
                <tr>
                    <td style="text-align: center; border: none; font-size: 12px !important;"><?php echo $user['User']['first_name'].' '.$user['User']['last_name'];?></td>
                </tr>
            </table>
        </div>
    <?php endforeach; ?>

    <br />
    <div style="clear:both;"></div>
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