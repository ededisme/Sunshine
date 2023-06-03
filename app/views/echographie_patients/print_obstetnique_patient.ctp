<?php
$absolute_url = FULL_BASE_URL . Router::url("/", false);
require_once("includes/function.php");
?>
<style type="text/css" media="screen">
    div.print-footer {display: none;}   
    fieldset{
        border: 1px solid #c1dad7;
    }
</style>
<style type="text/css" media="print">
    fieldset{
        border: 1px solid #c1dad7;
    }
    div.print_doc { width:100%; }
    div.print-footer { display: block; width: 100%;}
    input[type="checkbox"] { transform:scale(1.6, 1.6);}
    #btnDisappearPrint { display: none;}
    table tr td{ font-size: 13px; }
    @page
    {
        /*this affects the margin in the printer settings*/  
        margin: 2mm 5mm 2mm 5mm;
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
    <?php
    foreach ($dataService as $dataService):
        ?>
    
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
                    <p style="text-align: center;">
                        <span style="font-size: 14px; font-family: 'Khmer OS Bokor';">
                           <?php echo GENERAL_COMPANY_ECHO_SERVICE_DETAIL; ?>
                        </span> <br>
                        <span style="font-size: 14px; font-family: 'Khmer OS Bokor';">
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
    <br>
    <table class="defaultTable" cellspacing="0" cellpadding="0" style="width: 100%; margin: 0; padding: 0;">
        <tr>
            <td style="width: 35%; vertical-align: top;">
                <fieldset>
                    <table class="defualtTable" style="width: 100%;">
                        <tr>
                            <td style="width: 35%; vertical-align: top;">Echographie :</td>
                            <td>    
                                <?php echo $dataService['EchographyInfom']['name']; ?>                                                
                            </td> 
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">Examen par :</td>
                            <td>
                                <?php echo $dataService['EchographiePatient']['doctor_name']; ?>
                            </td>                                            
                        </tr>  
                        <tr>
                            <td style="vertical-align: top;">Indication :</td>
                            <td>
                                <?php echo $dataService['Indication']['name']; ?>
                            </td>                                            
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">D.D.R :</td>                                
                            <td>       
                                <?php echo $dataService['EchographiePatient']['ddr']; ?>
                            </td>                                            
                        </tr> 
                    </table>                                                                       
                </fieldset>
            </td>
            <td style="width: 65%" rowspan="2" valign="top">                            
                <fieldset class="description" style="min-height: 382px !important;">
                    <div class="box" style="width:95%;">
                        <?php echo $dataService['EchographiePatient']['description']; ?>
                    </div>
                </fieldset>
            </td>
            <!--For big child-->
        </tr>
        <tr>
            <td valign="top" style="padding-top: 5px;">
                <fieldset>   
                    <table class="defualtTable" style="width: 100%;">
                        <tr>
                            <td style="width: 35%;">ទំរង់កូន :</td>
                            <td><?php echo $dataService['EchographiePatient']['form_child']; ?></td>                                                                     
                        </tr>
                        <tr>
                            <td>ចំនួនកូន :</td>
                            <td><?php echo $dataService['EchographiePatient']['num_child']; ?></td>                                                                         
                        </tr>  
                        <tr>
                            <td>សុខភាពកូន :</td>
                            <td><?php echo $dataService['EchographiePatient']['healthy_child']; ?></td>                                                                      
                        </tr>
                        <tr>
                            <td>ភេទកូន :</td>
                            <td><?php echo $dataService['EchographiePatient']['sex_child']; ?></td>                                                                      
                        </tr>
                        <tr>
                            <td>ទឹកភ្លោះ :</td>
                            <td><?php echo $dataService['EchographiePatient']['teok_plos']; ?></td>                                                                      
                        </tr>
                        <tr>
                            <td>ទីតាំងសុក :</td>
                            <td><?php echo $dataService['EchographiePatient']['location_sok']; ?></td>                              
                        </tr>
                        <tr>
                            <td>ទំងន់កូន :</td>
                            <td>
                                <?php echo $dataService['EchographiePatient']['weight_child']; ?>
                                &nbsp;&nbsp;
                                ក្រាម
                            </td>                                                    
                        </tr>
                        <tr>
                            <td>អាយុកូន :</td>
                            <td>
                                <?php echo $dataService['EchographiePatient']['week_child']; ?>
                                &nbsp;&nbsp;
                                សបា្តហ៍
                                &nbsp;&nbsp;
                                <?php echo $dataService['EchographiePatient']['day_child']; ?>
                                &nbsp;&nbsp;
                                ថ្ងៃ
                            </td>                            
                        </tr>
                        <tr>
                            <td>គ្រប់ខែថ្ងៃ :</td>
                            <td>
                                <?php echo $dataService['EchographiePatient']['born_date']; ?>
                                &nbsp;&nbsp;
                                ក្រាម
                            </td>                                                      
                        </tr>
                    </table>
                </fieldset>
            </td>                            
        </tr>
    </table> 
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
                $query = mysql_query("SELECT emp.name FROM users As u INNER JOIN user_employees As useremployee ON useremployee.user_id = u.id INNER JOIN employees As emp ON emp.id = useremployee.employee_id WHERE u.id=" . $dataService['EchographiePatient']['created_by']);
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
