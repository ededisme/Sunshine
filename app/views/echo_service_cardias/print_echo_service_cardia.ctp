<?php
$absolute_url = FULL_BASE_URL . Router::url("/", false);
require_once("includes/function.php");
?>
<style type="text/css" media="screen">
    div.print-footer {display: none;}   
    *{
        font-family: 'arial';
        font-size: 13px;
    }
</style>

<style type="text/css" media="print">
    *{
        font-family: 'arial';
        font-size: 13px;
    }
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
<div class="print_doc">
    
    <table style="width: 100%; color: #083181;">
        <tr>
            <td valign="top" style="text-align: center; width: 20%;">                
                <img style=" width: 120px; height: 120px;" alt="" src="<?php echo $this->webroot; ?>img/logo_s.png" />
                <center style="margin-top: 10px; color: #083181; text-align: center; font-size: 18px; line-height:18px; font-family: 'Khmer OS Muol';"><?php echo GENERAL_COMPANY_DOCTOR_NAME; ?> </center>
            </td>
            <td style="text-align: center; width: 80%;">      
                <center style="padding-right: 55px;"> 
                    <h2 style="color: #083181; text-align: center; font-size: 18px; line-height:18px; font-family: 'Khmer OS Muol';"><?php echo GENERAL_COMPANY_NAME_KH; ?></h2>
                    <h2 style="color: #083181; text-align: center; font-size: 18px; line-height:18px; font-family: 'Khmer OS Muol';"><?php echo GENERAL_COMPANY_NAME_EN; ?></h2>
                    <span style="font-size:19px; text-align: center;">Laboratiire d'échocardiographie</span><br>
                    <span style="font-size:19px; text-align: center;">et d'écho-doppler vasculaire</span>
                    <p style="text-align: center;">
                        <span style="font-size: 13px; font-family: 'Khmer OS Bokor';">
                           <?php echo GENERAL_COMPANY_ECHO_SERVICE_DETAIL; ?>
                        </span> <br>
                        <span style="font-size: 13px; font-family: 'Khmer OS Bokor';">
                           <?php echo GENERAL_COMPANY_ECHO_SERVICE_DETAIL_1; ?>
                        </span>
                    </p> 
                </center>
            </td>
            <td valign="top" style="text-align: center; width: 20%;">                
                <img style="width: 120px; height: 120px;" alt="" src="<?php echo $this->webroot; ?>img/logo_s.png" /> 
            </td>
        </tr>
    </table>
<?php
foreach ($dataService as $dataService):
?>
    <table style="float: left; width: 100%;">
        <tr>
            <td style="width: 15%;"> <?php echo PATIENT_CODE_KH; ?> </td>
            <td style="width: 5%;"> : </td>
            <td style="width: 30%;"> <?php echo $dataService['Patient']['patient_code']; ?>  </td>
            <td style="width: 15%;"> <?php echo TABLE_DATE_FOR_CONTROL_KH; ?> </td>
            <td style="width: 5%;"> : </td>
            <td style="width: 30%;"> <?php echo date("d/m/Y H:i:s", strtotime($dataService['EchoServiceCardia']['created'])); ?></td>
        </tr>
        <tr>
            <td style="width: 15%;"> <?php echo PATIENT_NAME_KH; ?> </td>
            <td style="width: 5%;"> : </td>
            <td style="width: 30%;"> <?php echo $dataService['Patient']['patient_name']; ?> </td>
            <td style="width: 15%;"> <?php echo TABLE_SEX_KH; ?> :
                <?php 
                    if($dataService['Patient']['sex'] == "F"){
                        echo GENERAL_FEMALE_KH;
                    } else {
                        echo GENERAL_MALE_KH;
                    }
                ?>
            </td>
            <td style="width: 5%;"> : </td>
            <td style="width: 30%;">
                <?php echo TABLE_AGE_KH; ?> : 
                <?php
                if($dataService['Patient']['dob']!="0000-00-00" || $dataService['Patient']['dob']!=""){
                    echo getAgePatient($dataService['Patient']['dob']);
                }
                ?>
            </td>
        </tr>
    </table>
    
    <table cellspacing='0' cellpadding='0' width="100%">
        <tr>
            <td class="first patient-info" style="width: 50%; padding-top: 10px;"><?php echo TABLE_EFFECTUE;?> : <?php echo $dataService['EchoServiceCardia']['effecture']; ?></td>
            <td class="patient-info" style="width: 50%; padding-top: 10px;" colspan="2"><?php echo TABLE_PAR_DOCTOR;?> : 
                <?php 
                $doctor = "";
                $query = mysql_query("SELECT emp.name FROM users As u INNER JOIN user_employees As useremployee ON useremployee.user_id = u.id INNER JOIN employees As emp ON emp.id = useremployee.employee_id WHERE u.id=".$dataService['EchoServiceCardia']['created_by']);
                while ($result = mysql_fetch_array($query)) {
                    echo $doctor = $result['name'];
                }
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="padding-top: 10px;"><?php echo TABLE_MOTIF;?> : <?php echo $dataService['EchoServiceCardia']['effecture']; ?></td>
        </tr>
        <tr>
            <td colspan="3" style="padding-top: 10px;"><?php echo TABLE_NOM_ET_PRENOM;?> : <?php echo $dataService['Patient']['patient_name']; ?></td>
        </tr>
    </table>  
    <div class="clear"></div>
    <table cellspacing='0' cellpadding='0' width="100%">
        <tr>
            <td valign="top" style="padding-top:10px;padding-bottom: 10px;">
                <table style="width: 100%;" cellspacing="0">
                    <tr>
                        <td style="border:1px solid #aaaaaa;border-right: none;padding: 5px;">VD DTD</td>
                        <td style="border:1px solid #aaaaaa;border-left:none;padding: 5px;">(mm)</td>
                        <td colspan="2" style="border:1px solid #aaaaaa;border-left:none;padding: 5px;text-align: center;"><?php echo $dataService['EchoServiceCardia']['vd_dtd']; ?></td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #aaaaaa;border-top: none;border-right: none;padding: 5px;">Ao ascend</td>
                        <td style="border-left:none;border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;">(mm)</td>
                        <td colspan="2" style="border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;text-align: center;"><?php echo $dataService['EchoServiceCardia']['ao_ascend']; ?></td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #aaaaaa;border-top: none;border-right: none;padding: 5px;">OG</td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;width:25%;padding: 5px;">(mm et cm<sup style="font-size:9px">2</sup>)</td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;width:25%;padding: 5px;text-align: center;"><?php echo $dataService['EchoServiceCardia']['og_1']; ?></td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;width:25%;padding: 5px;text-align: center;"><?php echo $dataService['EchoServiceCardia']['og_2']; ?></td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #aaaaaa;border-top: none;border-right: none;padding: 5px;">SIV<sub style="font-size:9px">D-S</sub></td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;">(mm)</td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;text-align: center;"><?php echo $dataService['EchoServiceCardia']['siv_1']; ?></td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;text-align: center;"><?php echo $dataService['EchoServiceCardia']['siv_2']; ?></td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #aaaaaa;border-top: none;border-right: none;padding: 5px;">VGDTD-DTS</td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;">(mm)</td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;text-align: center;"><?php echo $dataService['EchoServiceCardia']['vgdtd_dts_1']; ?></td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;text-align: center;"><?php echo $dataService['EchoServiceCardia']['vgdtd_dts_2']; ?></td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #aaaaaa;border-top: none;border-right: none;padding: 5px;">PP VG<sub style="font-size:9px">D-S</sub></td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;">(mm)</td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;text-align: center;"><?php echo $dataService['EchoServiceCardia']['pp_vg_1']; ?></td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;text-align: center;"><?php echo $dataService['EchoServiceCardia']['pp_vg_2']; ?></td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #aaaaaa;border-top: none;border-right: none;padding: 5px;">FRVG-FEVG</td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;">%</td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;text-align: center;"><?php echo $dataService['EchoServiceCardia']['frvg_fevg_1']; ?></td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;text-align: center;"><?php echo $dataService['EchoServiceCardia']['frvg_fevg_2']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="text-align: center;padding: 10px;text-decoration: underline;font-weight:bold;font-size: 16px;">
                ECHO CARDIOGRAPHY
            </td>
        </tr>
        <tr>
            <td valign="top">
                <?php echo $dataService['EchoServiceCardia']['description']; ?>
            </td>
        </tr>
        <tr>
            <td style="font-weight:bold;font-size: 15px;padding-bottom: 10px;padding-top: 10px;"><?php echo 'CONCLUSION';?></td>
        </tr>
        <tr>
            <td style="padding-left:5%;padding-bottom: 5px;"><?php echo $dataService['EchoServiceCardia']['conclusion']; ?></td>
        </tr>
        <tr>
            <td>
                <?php
                $queryImage=  mysql_query("SELECT * FROM echo_service_cardia_images as esim WHERE is_active=1 AND echo_srv_cardia_id=".$dataService['EchoServiceCardia']['id']);
                if(@mysql_num_rows($queryImage)){
                    while ($dataImage=  mysql_fetch_array($queryImage)){ ?>
                    <img src="<?php echo $this->webroot; ?>img/echo_cardia/<?php echo $dataImage['src_name']; ?>" alt="<?php echo $dataImage['src_name']; ?>" width="150px" height="150px" vspace='2px' style="margin-left:5px;">
                    <?php 
                    }
                } ?>
            </td>
        </tr>
    </table>
    <!--<div style="page-break-after:always;"></div>-->
<?php endforeach; ?>
    <br><br>
    <div style="width: 35%; float: right; margin-right: 10px;">
        <p>
           <?php echo TABLE_TREATING_DOCTOR_KH; ?> 
        </p>
        <br><br>
        <p>
            <?php
                $doctor = "";
                $query = mysql_query("SELECT emp.name FROM users As u INNER JOIN user_employees As useremployee ON useremployee.user_id = u.id INNER JOIN employees As emp ON emp.id = useremployee.employee_id WHERE u.id=" . $dataService['EchoServiceCardia']['created_by']);
                while ($result = mysql_fetch_array($query)) {
                    echo $doctor = $result['name'];
                }
            ?>
        </p>
    </div>
    <div style="clear:both;"></div>
    <div class="print-footer" style="position: fixed; bottom: 0; text-align: center; width: 100%;">
        <center style="font-size: 11px;">
            <?php echo GENERAL_COMPANY_ADDRESS; ?> 
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
