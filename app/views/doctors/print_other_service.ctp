<style type="text/css">
    .none-border{
        border: none !important;
    }
    .none-border tr td{
        border: none !important;
        font-size: 12px !important;
    }
    .table{
        font-size: 12px !important;
    }   
</style>
<style type="text/css" media="print"> 
    .none-border{
        border: none !important;
        font-size: 12px !important;
    }
    .none-border tr td{
        border: none !important;
        font-size: 12px !important;
    }
    .table{
        font-size: 12px !important;
    } 
    p{
        font-size: 12px !important;
    } 
    @page
    {
        /*this affects the margin in the printer settings*/  
        margin: 5mm 7mm 5mm 10mm;
    }    
</style>
<?php
foreach ($otherService as $otherService):
    ?>
    <div style="width: 35%;float: left;margin-top: 20px;">
        <table cellspacing='0' cellpadding='0' style="width: 100%;border: 0;">
            <tr>
                <td style="vertical-align: top;text-align: center;">
                    <img alt="" src="<?php echo $this->webroot; ?>img/logo_s.png" style="widht:120px ; height: 120px" />           
                </td>
                <td style="width: 75%;padding-left: 5px;">
                    <h2 style="font-size: 18px;line-height: 25px"><?php echo GENERAL_COMPANY_NAME_KH; ?></h2>
                    <h2 style="font-size: 14px;line-height: 0px;"><?php echo GENERAL_COMPANY_NAME_EN; ?></h2>      
                </td>
            </tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr>
                <td colspan="2" style="padding-left: 50px;text-decoration: underline;"><?php // echo TITLE_MEDICAL_REPORT;?></td>
            </tr>
        </table>    
    </div>
    <div style="width: 65%;float: right;margin-top: 10px;">
        <table class="table" cellspacing='0' cellpadding='0'>
            <tr>
                <td class="first patient-info" style="width: 35%; border-top: 1px solid #C1DAD7;"><?php echo TABLE_DATE;?> :</td>
                <td style="width: 65%; border-top: 1px solid #C1DAD7;">
                    <?php echo date("d/m/Y H:i:s", strtotime($otherService['OtherServiceRequest']['created'])); ?>
                </td>
            </tr>
            <tr>
                <td class="first patient-info"><?php echo PATIENT_CODE;?> :</td>
                <td><?php echo $otherService['Patient']['patient_code']; ?></td>
            </tr>
            <tr>
                <td class="first patient-info"><?php echo PATIENT_NAME;?> :</td>
                <td><?php echo $otherService['Patient']['patient_name']; ?></td>
            </tr>            
            <tr>
                <td class="first patient-info"><?php echo TABLE_AGE.'/'.TABLE_DOB;?> :</td>
                <td>
                    <?php 
                    $then_ts = strtotime($otherService['Patient']['dob']);
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
            </tr>
            <tr>
                <td class="first patient-info"><?php echo TABLE_SEX;?> :</td>
                <td>
                    <?php
                    if ($otherService['Patient']['sex'] == "F") {
                        echo GENERAL_FEMALE;
                    } else {
                        echo GENERAL_MALE;
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td class="first patient-info"><?php echo TABLE_NATIONALITY;?> :</td>
                <td>
                    <?php
                    if ($otherService['Patient']['patient_group_id'] != "") {
                        $query = mysql_query("SELECT name FROM patient_groups WHERE id=" . $otherService['Patient']['patient_group_id']);
                        while ($row = mysql_fetch_array($query)) {
                            if ($otherService['Patient']['patient_group_id'] == 1) {
                                echo $row['name'];
                            } else {
                                $queryNationality = mysql_query("SELECT name FROM nationalities WHERE id=".$otherService['Patient']['nationality']);
                                while ($result = mysql_fetch_array($queryNationality)) {
                                    echo $row['name'] . '&nbsp;&nbsp;(' . $result['name'] . ')';
                                }
                            }
                        }
                    } else {
                        echo $otherService['Nationality']['name'];
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td class="first patient-info"><?php echo TABLE_ADDRESS;?> :</td>
                <td>
                    <?php 
                    if($otherService['Patient']['address']!=""){
                        echo $otherService['Patient']['address'];
                    }
                    if($otherService['Patient']['location_id']!=""){
                        $query = mysql_query("SELECT name FROM patient_locations WHERE id=".$otherService['Patient']['location_id']);
                        while ($row = mysql_fetch_array($query)) {
                            if($otherService['Patient']['address']!=""){
                                echo ', ';
                            }
                            echo $row['name'];                
                        }
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td class="first patient-info"><?php echo TABLE_TELEPHONE;?> :</td>
                <td><?php echo $otherService['Patient']['telephone']; ?></td>
            </tr>
            <tr>
                <td class="first patient-info"><?php echo TABLE_TREATING_DOCTOR;?> :</td>
                <td>
                    <?php 
                    $doctor = "";
                    $query = mysql_query("SELECT emp.name FROM users As u INNER JOIN user_employees As useremployee ON useremployee.user_id = u.id INNER JOIN employees As emp ON emp.id = useremployee.employee_id WHERE u.id=".$otherService['OtherServiceRequest']['created_by']);
                    while ($result = mysql_fetch_array($query)) {
                        echo $doctor = $result['name'];
                    }
                    ?>
                </td>
            </tr>            
        </table>    
    </div>   
    <div class="clear"></div>
    <table class="table" cellspacing='0' cellpadding='0'>
        <tr>
            <td class="first" style="width:20%;border-top:1px solid #C1DAD7;">
                <span class="title-report"><?php echo TABLE_ECHO_SERVICE;?> </span>
            </td>
            <td valign="top" style="border-top:1px solid #C1DAD7;"><?php echo $otherService['EchoServiceRequest']['echo_description'] ?></td>
        </tr>
        <tr>
            <td class="first">
                <span class="title-report"><?php echo TABLE_XRAY_SERVICE;?> </span>
            </td>
            <td valign="top"><?php echo $otherService['XrayServiceRequest']['xray_description'] ?></td>
        </tr>
        <tr>
            <td class="first">
                <span class="title-report"><?php echo TABLE_CYSTOSCOPY;?> </span>
            </td>
            <td><?php echo $otherService['CystoscopyServiceRequest']['cystoscopy_description'] ?></td>
        </tr>
        <tr>
            <td class="first">
                <span class="title-report"><?php echo TABLE_MID_WIFE_SERVICE;?> </span>
            </td>
            <td><?php echo $otherService['MidWifeServiceRequest']['mid_wife_description'] ?></td>
        </tr>
        <tr>
            <td class="first">
                <span class="title-report"><?php echo TABLE_DATE;?> </span>
            </td>
            <td>
                <?php echo $doctor;?>
                <br/><br/><br/>
                <?php echo date("d/m/Y H:i:s", strtotime($otherService['OtherServiceRequest']['created'])); ?>
            </td>
        </tr>
    </table>
    <div style="page-break-after:always;"></div>
<?php endforeach; ?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
            var w = window;
            try {
                jsPrintSetup.refreshOptions();
                var printer = 'Udaya-A4';
                var silent  = 1;
                jsPrintSetup.setPrinter(printer);
                jsPrintSetup.setOption('marginTop', 0);
                jsPrintSetup.setOption('marginBottom', 0);
                jsPrintSetup.setOption('marginLeft', 0);
                jsPrintSetup.setOption('marginRight', 0);
                jsPrintSetup.setSilentPrint(silent);
                jsPrintSetup.printWindow(w);
                w.close();
            } catch (e) {
                w.print();
                w.close();
            }
        });
</script>
