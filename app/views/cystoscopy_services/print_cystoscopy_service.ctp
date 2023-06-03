    <?php
    $absolute_url = FULL_BASE_URL . Router::url("/", false);
    require_once("includes/function.php");
    ?>
    <style type="text/css" media="screen">
        div.print-footer {display: none;}    
        .titleKH{font-size: 22px}
        .titleEN{font-size : 18px}
        .medicalImagine{font-size : 11pt}
        p{
            font-family: 'Times New Roman';
            font-size: 12px;
            text-align: justify;
        }
        span{
            font-family: 'Times New Roman';
            font-size: 12px;
        }
    </style>
    <style type="text/css" media="print">
        div.print_doc { width:100%; }
        div.print-footer { display: block; width: 100%;}
        input[type="checkbox"] { transform:scale(1.6, 1.6);}
        #btnDisappearPrint { display: none;}
        table tr td{ font-size: 8pt; font-family: "Khmer OS"}
        @page
        {
            /*this affects the margin in the printer settings*/  
            margin: 5mm 7mm 5mm 10mm;
        }
        p{
            font-family: 'Times New Roman';
            font-size: 12px;
            text-align: justify;
        }
        span{
            font-family: 'Times New Roman';
            font-size: 12px;
        }
        th{ font-weight: normal; }   
        .titleKH{font-size: 20pt}
        .titleEN{font-size : 16pt}
        .medicalImagine{font-size : 11pt}
    </style>
    <div class="print_doc">
        <?php
        foreach ($dataService as $dataService):
        ?>
        <table style="width: 100%;margin-top: 20px">
            <tr>
                <td valign="middle" style="text-align: center; width: 20%;">                
                    <img style="width: 80px; height: 80px;" alt="" src="<?php echo $this->webroot; ?>img/logo_s.png" />

                </td>
                <td style="text-align: center; width: 60%;">      
                    <center> 
                        <h2 class="titleKH" style="text-align: center;  line-height:22px;  font-family: 'Khmer OS Muol';"><?php echo $this->data['Branch']['name_other'] ?></h2>
                        <h2 class="titleEN" style="margin-top:20px;  text-align: center;  line-height:18px; font-family: 'Khmer Unicode R1';"><?php echo $this->data['Branch']['name'] ?></h2>
                    </center>
                </td>
                <td valign="middle" style="text-align: center; width: 20%;">                
                    <img style="width: 80px; height: 80px;" alt="" src="<?php echo $this->webroot; ?>img/photo_2018-11-19_11-48-09.jpg" /> 
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <p style="text-align: center;margin-top:10px">
                        <span  style="font-family: 'Khmer OS Metal Chrieng';font-size: 10px;"><?php echo GENERAL_COMPANY_ECHO_SERVICE_DESCRIPTION_KH;  ?></span>
                        <span  style="font-family: 'Times New Roman' ; font-size: 10px;" ><?php echo GENERAL_COMPANY_ECHO_SERVICE_DESCRIPTION_EN;  ?></span>
                        <br>
                        <span style="font-family: 'Khmer OS Metal Chrieng';font-size: 10px;"><?php echo GENERAL_COMPANY_ECHO_SERVICE_DESCRIPTION2_KH; ?></span>
                        <span style="font-family: 'Times New Roman' ;font-size: 10px;" ><?php echo GENERAL_COMPANY_ECHO_SERVICE_DESCRIPTION2_EN; ?></span>
                    </p> 
                </td>
            </tr>
            <tr>
                <td colspan="3"><p class="medicalImagine" style="color: #03bbc8; text-align: center; line-height:18px; font-family: 'Khmer OS Bokor'; text-align: center;"><?php echo MEDICAL_ENDOSCOPY_KH; ?> - Endoscopy investigations</p></td>
            </tr>
        </table>
        <table style="float: left; width: 100%; font-family: 'Khmer OS'">
            <tr>
                <td style="width: 15%;"> លេខសំគាល់ / ID </td>
                <td style="width: 5%;"> : </td>
                <td style="width: 25%;"> <?php echo $dataService['Patient']['patient_code']; ?>  </td>
                <td style="width: 15%;"> ថែ្ងខែមក ពិនិត្យ / Date </td>
                <td style="width: 5%;"> : </td>
                <td style="width: 25%;"> <?php echo date("d/m/Y H:i:s", strtotime($dataService['CystoscopyService']['created'])); ?></td>
            </tr>   
            <tr>
                <td style="width: 15%;">  ឈ្មោះ / Name </td>
                <td style="width: 5%;"> : </td>
                <td style="width: 25%;"> <?php echo $dataService['Patient']['patient_name']; ?> </td>
                <td style="width: 15%;"> ភេទ / Sex :
                    <?php 
                        if($dataService['Patient']['sex'] == "F"){
                            echo GENERAL_FEMALE_KH. " (F)";
                        } else {
                            echo GENERAL_MALE_KH. " (M)";
                        }
                    ?>
                </td>
                <td style="width: 5%;"> : </td>
                <td style="width: 30%;">
                    <?php echo TABLE_AGE_KH." / Age"; ?> : 
                    <?php
                    if($dataService['Patient']['dob']!="0000-00-00" || $dataService['Patient']['dob']!=""){
                        echo getAgePatient($dataService['Patient']['dob']);
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td style="width: 15%;">  រោគវិនិច្ឆ័យ / Diagnosis </td>
                <td style="width: 5%;"> : </td>
                <td colspan="4"> 
                    <?php 
                    $queryDocConsult = mysql_query("SELECT daignostic FROM patient_consultations WHERE is_active = 1 AND queued_doctor_id = {$dataService['CystoscopyService']['cystoscopy_service_queue_id']} LIMIT 1 ");
                    while ($rowDocConsult = mysql_fetch_array($queryDocConsult)) {
                        echo $rowDocConsult['daignostic'];
                    }                    
                    ?>
                </td>
            </tr>
        </table>
        <div style="clear:both;"></div>
        <div style="width: 53%; float: right; font-weight: bold; margin-top: 10px;font-size :15px">
            <?php echo 'URETHROCYSTOSCOPY REPORT'; ?>
        </div>
        <div style="clear:both;"></div>
        <table style="width: 100%;padding-bottom: 50px">
            <tr>
                <td style="width: 25%;" valign="top"></td>
                <td style="padding-left: 30px;">
                    <div>
                        <p><?php echo $dataService['CystoscopyService']['descript_before_sdate']; ?> <span style="font-weight: bold;<?php if($dataService['CystoscopyService']['start_date']=="0000-00-00"){ echo 'display:none;';}?>"><?php echo date("d F Y", strtotime($dataService['CystoscopyService']['start_date'])); ?></span>,we perform an urethrocystocopy on <span style="font-weight: bold;"><?php echo date("d F Y", strtotime($dataService['CystoscopyService']['end_date'])); ?></span></p>
                    </div>                    
                </td>
            </tr>
            <tr style="<?php if ($dataService['CystoscopyService']['urethra_img'] == "") { echo 'display:none;';}?>">
                <td style="width: 25%;" valign="top">
                   <img src="<?php echo $this->webroot; ?>/img/cystoscopy/<?php echo $dataService['CystoscopyService']['urethra_img']; ?>" alt="<?php echo $dataService['CystoscopyService']['urethra_img']; ?>" width=180px" height="100px" vspace='2px' style="margin-left:5px;">  
                </td>
                <td style="vertical-align: middle; padding-left: 30px;">
                    <div style="text-orientation: initial; font-family: 'Times New Roman' ; font-size: 12px;">
                        <b style="font-family: 'Times New Roman' ; font-size: 12px;"><?php echo 'Urethra'; ?> :</b>
                        <?php echo nl2br($dataService['CystoscopyService']['urethra']); ?>
                    </div>                    
                </td>
            </tr>
            <tr style="<?php if ($dataService['Patient']['sex'] == "F" && $dataService['CystoscopyService']['prostate_img'] == "") { echo 'display:none;';}?>">
                <td valign="top">
                   <img src="<?php echo $this->webroot; ?>/img/cystoscopy/<?php echo $dataService['CystoscopyService']['prostate_img']; ?>" alt="<?php echo $dataService['CystoscopyService']['prostate_img']; ?>" width=180px" height="100px" vspace='2px' style="margin-left:5px;">  
                </td>
                <td style="vertical-align: middle; padding-left: 30px;">
                    <div style="text-orientation: initial; font-family: 'Times New Roman' ; font-size: 12px;">
                        <b style="font-family: 'Times New Roman' ; font-size: 12px;"><?php echo 'Prostate'; ?> :</b>
                        <?php echo nl2br($dataService['CystoscopyService']['prostate']); ?>
                    </div>                    
                </td>
            </tr>
            <tr style="<?php if ($dataService['CystoscopyService']['bladder_neck_img'] == "") { echo 'display:none;';}?>">
                <td valign="top">
                   <img src="<?php echo $this->webroot; ?>/img/cystoscopy/<?php echo $dataService['CystoscopyService']['bladder_neck_img']; ?>" alt="<?php echo $dataService['CystoscopyService']['bladder_neck_img']; ?>" width=180px" height="100px" vspace='2px' style="margin-left:5px;">  
                </td>
                <td style="vertical-align: middle; padding-left: 30px;">
                    <div style="text-orientation: initial; font-family: 'Times New Roman' ; font-size: 12px;">
                        <b style="font-family: 'Times New Roman' ; font-size: 12px;"><?php echo 'Bladder neck'; ?> :</b>
                        <?php echo nl2br($dataService['CystoscopyService']['bladder_neck']); ?>
                    </div>                    
                </td>
            </tr>
            <tr style="<?php if ($dataService['CystoscopyService']['bladder_img'] == "") { echo 'display:none;';}?>">
                <td valign="top">
                   <img src="<?php echo $this->webroot; ?>/img/cystoscopy/<?php echo $dataService['CystoscopyService']['bladder_img']; ?>" alt="<?php echo $dataService['CystoscopyService']['bladder_img']; ?>" width=180px" height="100px" vspace='2px' style="margin-left:5px;">  
                </td>
                <td style="vertical-align: middle; padding-left: 30px;">
                    <div style="text-orientation: initial; font-family: 'Times New Roman' ; font-size: 12px;">
                        <b style="font-family: 'Times New Roman' ; font-size: 12px;"><?php echo 'Bladder'; ?> :</b>
                        <?php echo nl2br($dataService['CystoscopyService']['bladder']); ?>
                    </div>                    
                </td>
            </tr>
            <tr style="<?php if ($dataService['Patient']['sex'] == "M" && $dataService['CystoscopyService']['after_five_minute_img'] == "") { echo 'display:none;';}?>">
                <td valign="top">
                   <img src="<?php echo $this->webroot; ?>/img/cystoscopy/<?php echo $dataService['CystoscopyService']['after_five_minute_img']; ?>" alt="<?php echo $dataService['CystoscopyService']['after_five_minute_img']; ?>" width=180px" height="100px" vspace='2px' style="margin-left:5px;">  
                </td>
                <td style="vertical-align: middle; padding-left: 30px;">
                    <div style="text-orientation: initial; font-family: 'Times New Roman' ; font-size: 12px;">
                        <b style="font-family: 'Times New Roman' ; font-size: 12px;"><?php echo 'After 5 minutes cysto-hydrodistention'; ?> :</b>
                        <?php echo nl2br($dataService['CystoscopyService']['after_five_minute']); ?>
                    </div>                    
                </td>
            </tr>
            <tr>
                <td></td>
                <td style="padding-left: 30px;">
                    <div style="text-orientation: initial; font-family: 'Times New Roman' ; font-size: 12px;">
                        <b style="font-family: 'Times New Roman' ; font-size: 12px;"><?php echo TABLE_CONCLUSION;?> :</b>
                        <?php echo nl2br($dataService['CystoscopyService']['conclusion']); ?>
                    </div>
                </td
            </tr>
            <tr>
                <td colspan="2">
                    <div style="width: 35%; float: right; margin-right: 10px;">
                        <p style="text-align: right; font-family: 'Khmer OS' ; font-size: 8pt"> គ្រូពេទ្យពិនិត្យ / Reporting Doctor</p>
                        <br><br>
                    </div>
                </td>
            </tr>
        </table>
        <div style="clear:both;"></div>
        <?php endforeach; ?>
        <br />
      
        
        <div style="clear:both;"></div>
        <div class="print-footer" style="position: fixed; bottom: 0;width: 100%;">
            <center style="">
                <table style="width:100% ; ">
                    <tr>
                        <td style="font-size:7pt ; width: 65% ;font-family:'Khmer OS Metal Chrieng'"><?php echo $this->data['Branch']['address_other'] ?></td>
                        <td style="font-size:9pt ; width: 35% ; font-family:'Times New Roman'">Tel : <?php echo $this->data['Branch']['telephone'] ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:8pt;font-family:'Times New Roman'"><?php echo $this->data['Branch']['address'] ?></td>
                        <td style="font-size:10pt ;font-family:'Times New Roman'">Email : <?php echo $this->data['Branch']['email_address'] ?></td>
                    </tr>
                </table>
            </center>
        </div>
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
                window.print();
                window.close();
            });
        });
    </script>
