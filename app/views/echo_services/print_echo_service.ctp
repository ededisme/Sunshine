<?php
$absolute_url = FULL_BASE_URL . Router::url("/", false);
require_once("includes/function.php");

?>
<style type="text/css" media="screen">
    div.print-footer {display: none;}  
    .titleKH{font-size: 22px}
    .titleEN{font-size : 18px}
    .medicalImagine{font-size : 16px}
</style>

<style type="text/css" media="print">
    div.print_doc { width:100%; }
    div.print-footer { display: block; width: 100%;}
    input[type="checkbox"] { transform:scale(1.6, 1.6);}
    #btnDisappearPrint { display: none;}
    table tr td{ font-size: 8pt; }
    @page
    {
        /*this affects the margin in the printer settings*/  
        margin: 5mm 7mm 5mm 10mm;
    }
    p{
        padding: 0 10px;
        margin: 0;
        font-size: 12px;
    }
    th{ font-weight: normal; }   
    h2{ font-size: 18px;}
    .titleKH{font-size: 20pt}
    .titleEN{font-size : 16pt}
    .medicalImagine{font-size : 11pt}
</style>
<div class="print_doc">
<?php
foreach ($dataService as $dataService):
    ?>
    <table style="width: 100%;margin-top: 40px ">
        <tr>
            <td valign="middle" style="text-align: center; width: 20%;">                
                <img style=" width: 80px; height: 80px;" alt="" src="<?php echo $this->webroot; ?>img/logo_s.png" />
            </td>
            <td style="text-align: center; width: 60%;">      
                    <center> 
                        <h2 class="titleKH" style="text-align: center; line-height:22px;  font-family: 'Khmer OS Muol';"><?php echo $this->data['Branch']['name_other'] ?></h2>
                        <h2 class="titleEN" style="margin-top:20px;  text-align: center;  line-height:18px; font-family: 'Khmer Unicode R1';"><?php echo $this->data['Branch']['name'] ?></h2>

                        <!--<h2 style="color: #ef2828; text-align: center; font-size: 15px; line-height:18px; font-family: 'Khmer OS Muol';"><?php echo MUNU_XRAY_SERVICE;  ?> - X-rays</h2>-->
                        <!--<h2 style="color: #03bbc8; text-align: center; font-size: 14px; line-height:18px; font-family: 'Khmer OS Metal Chrieng';">Tel : <?php echo $this->data['Branch']['telephone']; ?></h2>-->
                    </center>
            </td>
            <td valign="middle" style="text-align: center; width: 20%;">                
                <img style="width: 80px; height: 80px;" alt="" src="<?php echo $this->webroot; ?>img/logo_s.png" /> 
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <p style="text-align: center;margin-top:20px">
                    <span style="font-family: 'Khmer OS Metal Chrieng';font-size: 10px;"><?php echo GENERAL_COMPANY_ECHO_SERVICE_DESCRIPTION_KH;  ?></span>
                    <span style="font-family: 'Times New Roman' ; font-size: 10px;" ><?php echo GENERAL_COMPANY_ECHO_SERVICE_DESCRIPTION_EN;  ?></span>
                <br>
                    <span style="font-family: 'Khmer OS Metal Chrieng';font-size: 10px;"><?php echo GENERAL_COMPANY_ECHO_SERVICE_DESCRIPTION2_KH; ?></span>
                    <span style="font-family: 'Times New Roman' ;font-size: 10px;" ><?php echo GENERAL_COMPANY_ECHO_SERVICE_DESCRIPTION2_EN; ?></span>
                </p> 
            </td>
        </tr>
        <tr>
            <td colspan="3"><p class="medicalImagine" style="color: #03bbc8; text-align: center; line-height:18px; font-family: 'Khmer OS Bokor';"><?php echo MEDICAL_IMAGING_KH; ?> - Medical Imaging</p></td>
        </tr>
    </table>
    <br/><br/>
    <table style="float: left; width: 100%;font-family: 'Khmer OS'">
        <tr>
            <td style="width: 20%;"> លេខសំគាល់ / ID</td>
            <td style="width: 5%;"> : </td>
            <td style="width: 25%;"> <?php echo $dataService['Patient']['patient_code']; ?>  </td>
            <td style="width: 20%;"> ថែ្ងខែមក ពិនិត្យ / Date </td>
            <td style="width: 5%;"> : </td>
            <td style="width: 25%;"> <?php echo date("d/m/Y H:i:s", strtotime($dataService['EchoService']['created'])); ?></td>
        </tr>
        <tr>
            <td style="width: 20%;"> ឈ្មោះ / Name </td>
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
    </table>
    <div style="clear:both;"></div>
    <br/>
    <div style="width: 35%; float: right; font-weight: bold; margin-top: 10px;font-size :15px">
        <?php echo ULTRASOUND_REPORT; ?>
    </div>
    <div style="clear:both;"></div>
    <br/>
    <table style="width: 100%;padding-bottom: 50px">
        <tr>
            <td style="width: 45%;" valign="top">
                <?php
                    $i = 0 ; 
                   $queryImage=  mysql_query("SELECT * FROM echo_service_images as esim WHERE is_active=1 AND echo_srv_id=".$dataService['EchoService']['id']);
                   if(@mysql_num_rows($queryImage)){
                       while ($dataImage=  mysql_fetch_array($queryImage)){ 
                           $i++; 
                           ?>
                       <p>
                           <img src="<?php echo $this->webroot; ?>img/echo/<?php echo $dataImage['src_name']; ?>" alt="<?php echo $dataImage['src_name']; ?>" width="250px" height="250px">
                       </p>
                       <?php 
                       }
                   }if($i < 1 ){
                        echo '<p style="padding-top:500px"> </p>' ; 
                    }else if($i < 2 ){ 
                       echo '<p style="padding-top:250px"> </p>' ; 
                   } 
                   ?>
            </td>
            <td style="width: 55%;" valign="top">
                <div>
                    <?php 
                        echo "<b>".TABLE_DESCRIPTION."</b>";  
                        echo $dataService['EchoService']['description']; 
                        ?>
                </div><br>
                <div>
                   <?php echo "<b>".TABLE_CONCLUSION."</b>";?> :
                   <?php echo $dataService['EchoService']['conclusion']; ?>
               </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div style="width: 35%; float: right; margin-right: 10px;">
                    <p style="text-align: right ; font-family: 'Khmer OS' ; font-size: 8pt"> 
                       គ្រូពេទ្យពិនិត្យ / Reporting Doctor      
                    </p>
                    <br><br>

                </div>
            </td>
        </tr>
    </table>

    <div style="clear:both;"></div>
<?php endforeach; ?>
   
    
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
    <div style="clear:both"></div>
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
