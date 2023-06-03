<?php
$absolute_url = FULL_BASE_URL . Router::url("/", false);
require_once("includes/function.php");
?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(document).dblclick(function() {
            window.close();
        });
        $("#btnDisappearPrint").click(function() {
            window.print();
            window.close();
        });
    });
</script>
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
foreach ($dataService as $dataService):
    ?>
    <div style="width: 35%;float: left;margin-top: 20px;">
        <table cellspacing='0' cellpadding='0' style="width: 100%;border: 0;">
            <tr>
                <td style="vertical-align: top;text-align: center;">
                    <img alt="" src="<?php echo $this->webroot; ?>img/logo_s.png" />           
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
                    <?php echo date("d/m/Y H:i:s", strtotime($dataService['MidWifeService']['created'])); ?>
                </td>
            </tr>
            <tr>
                <td class="first patient-info"><?php echo PATIENT_CODE;?> :</td>
                <td><?php echo $dataService['Patient']['patient_code']; ?></td>
            </tr>
            <tr>
                <td class="first patient-info"><?php echo PATIENT_NAME;?> :</td>
                <td><?php echo $dataService['Patient']['patient_name']; ?></td>
            </tr>            
            <tr>
                <td class="first patient-info"><?php echo TABLE_AGE.'/'.TABLE_DOB;?> :</td>
                <td>
                    <?php 
                    $then_ts = strtotime($dataService['Patient']['dob']);
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
                    if ($dataService['Patient']['sex'] == "F") {
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
                    if ($dataService['Patient']['patient_group_id'] != "") {
                        $query = mysql_query("SELECT name FROM patient_groups WHERE id=" . $dataService['Patient']['patient_group_id']);
                        while ($row = mysql_fetch_array($query)) {
                            if ($dataService['Patient']['patient_group_id'] == 1) {
                                echo $row['name'];
                            } else {
                                $queryNationality = mysql_query("SELECT name FROM nationalities WHERE id=".$dataService['Patient']['nationality']);
                                while ($result = mysql_fetch_array($queryNationality)) {
                                    echo $row['name'] . '&nbsp;&nbsp;(' . $result['name'] . ')';
                                }
                            }
                        }
                    } else {
                        echo $dataService['Nationality']['name'];
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td class="first patient-info"><?php echo TABLE_ADDRESS;?> :</td>
                <td>
                    <?php 
                    if($dataService['Patient']['address']!=""){
                        echo $dataService['Patient']['address'];
                    }
                    if($dataService['Patient']['location_id']!=""){
                        $query = mysql_query("SELECT name FROM patient_locations WHERE id=".$dataService['Patient']['location_id']);
                        while ($row = mysql_fetch_array($query)) {
                            if($dataService['Patient']['address']!=""){
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
                <td><?php echo $dataService['Patient']['telephone']; ?></td>
            </tr>
            <tr>
                <td class="first patient-info"><?php echo TABLE_TREATING_DOCTOR;?> :</td>
                <td>
                    <?php 
                    $doctor = "";
                    $query = mysql_query("SELECT emp.name FROM users As u INNER JOIN user_employees As useremployee ON useremployee.user_id = u.id INNER JOIN employees As emp ON emp.id = useremployee.employee_id WHERE u.id=".$dataService['MidWifeService']['created_by']);
                    while ($result = mysql_fetch_array($query)) {
                        echo $doctor = $result['name'];
                    }
                    ?>
                </td>
            </tr>            
        </table>    
    </div>   
    <div class="clear"></div>
    <table style="width: 100%;padding-left:20px;" cellspacing='0' cellpadding='0'>
        <tr>
            <td style="width:10%;"><?php echo TABLE_LAST_MENSTRUATION_PERIOD; ?></td>
            <td style="width:3%;">:</td>
            <td style="width:20%;"><?php echo dateShort($dataService['MidWifeService']['last_mentstruation_period']); ?></td>
            <td></td>
        </tr>
        <tr>
            <td><?php echo TABLE_ESTIMATE_DELIVERY_DATE; ?></td>
            <td style="width:3%;">:</td>
            <td><?php echo dateShort($dataService['MidWifeService']['estimate_delivery_date']); ?></td>
            <td></td>
        </tr>
        <tr>
            <td><?php echo TABLE_ECHO; ?></td>
            <td>:</td>
            <td><?php echo dateShort($dataService['MidWifeService']['echo']); ?></td>
            <td></td>
        </tr>
        <tr>
            <td style="width:10%;"><?php echo TABLE_WEIGHT; ?></td>
            <td>:</td>
            <td style="width:20%;"><?php echo $dataService['MidWifeService']['weight']; ?></td>
            <td style="width:10%;"><?php echo TABLE_HEIGHT; ?></td>
            <td style="width:3%;">:</td>
            <td style="width:20%;"><?php echo $dataService['MidWifeService']['height']; ?></td>
            <td></td>
        </tr>
        <tr>
            <td><?php echo TABLE_GESTRATION; ?></td>
            <td>:</td>
            <td><?php echo $dataService['MidWifeService']['gestation']; ?></td>
            <td><?php echo TABLE_BABY; ?></td>
            <td>:</td>
            <td><?php echo $dataService['MidWifeService']['baby']; ?></td>
            <td></td>
        </tr>
    </table>
    <table style="width: 100%;padding-left:20px;" cellspacing='0' cellpadding='0'>
        <tr>
            <td style="width:8%;"><?php __(MENU_PATIENT_STORY_SEE); ?></td>
        </tr>
        <tr>
            <td style="width:8%;"></td>
            <td style="width:8%;"><?php echo TABLE_ABORTION; ?></td>
            <td style="width:2%;">:</td>
            <td style="width:10%;"><?php echo $dataService['MidWifeService']['abortion']; ?></td>
            <td style="width:10%;"><?php echo TABLE_INTERUPTION_VOLONTAIN; ?></td>
            <td style="width:2%;">:</td>
            <td style="width:10%;"><?php echo $dataService['MidWifeService']['interuption_volontain']; ?></td>
            <td style="width:52%;" colspan="4"></td>
        </tr>
        <tr>
            <td><?php __(MENU_ACCON_CHEMENT); ?></td>    
        </tr>
        <tr>
            <td style="width:8%;"></td>
            <td style="width:10%;"><?php echo TABLE_BIRTH; ?></td>
            <td style="width:2%;">:</td>
            <td style="width:15%;"><?php echo $dataService['MidWifeService']['birth']; ?></td>
            <td style="width:15%;"><?php echo MENU_NEE_MOIT; ?></td>
            <td style="width:2%;">:</td>
            <td style="width:15%;"><?php echo $dataService['MidWifeService']['nee_moit']; ?></td>
            <td style="width:15%;"><?php echo MENU_MOIT_NEE; ?></td>
            <td style="width:2%;">:</td>
            <td style="width:10%;"><?php echo $dataService['MidWifeService']['mort_nee']; ?></td>
            <td style=""></td>
        </tr>
        <tr>
            <td><?php __(MENU_ACCONCHEMENT_RERME); ?></td>    									
        </tr> 
        <tr>
            <td></td>
            <td><?php echo TABLE_ACCONCHEM_NORMAL; ?></td>
            <td style="width:2%;">:</td>
            <td style="width:10%;"><?php echo $dataService['MidWifeService']['acconchement_normal']; ?></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td><?php echo MENU_ANORMAT; ?></td>
        </tr>
        <tr>
            <td></td>
            <td><?php echo TABLE_CAESAREAN; ?></td>
            <td style="width:2%;">:</td>
            <td style="width:10%;"><?php echo $dataService['MidWifeService']['caesarean']; ?></td>
            <td><?php echo TABLE_ACC_PAR_VENTONSE; ?></td>
            <td style="width:2%;">:</td>
            <td style="width:10%;"><?php echo $dataService['MidWifeService']['acc_par_ventonse']; ?></td>
            <td></td>
        </tr>
    </table>
    <table style="width: 100%;padding-left:20px;" cellspacing='0' cellpadding='0'>
        <tr>
            <td style="width:15%;"><?php __(PATIENT_MADIE_DES_REINS); ?></td>
            <td style="width:2%;">:</td>
            <td style="width:10%;">
                <?php 
                if($dataService['MidWifeService']['edema']==1){
                    echo $this->Form->input('edema',array('type'=>'checkbox','name'=>'data[MidWifeService][edema]','value'=>'1','label'=>FALSE,'checked'=>TRUE,'disabled' => "disabled"));
                }else{
                    echo $this->Form->input('edema',array('type'=>'checkbox','name'=>'data[MidWifeService][edema]','value'=>'1','label'=>FALSE,'disabled' => "disabled"));
                }
                ?>
                <?php echo TABLE_EDEMA; ?>
            </td>

            <td style="width:10%;">
                <?php 
                if($dataService['MidWifeService']['albuminuria']==1){
                    echo $this->Form->input('albuminuria',array('type'=>'checkbox','name'=>'data[MidWifeService][albuminuria]','value'=>'1','label'=>FALSE,'checked'=>TRUE,'disabled' => "disabled"));
                }else{
                    echo $this->Form->input('albuminuria',array('type'=>'checkbox','name'=>'data[MidWifeService][albuminuria]','value'=>'1','label'=>FALSE,'disabled' => "disabled"));
                }
                ?>
                <?php echo TABLE_ALBUMINURIA; ?>
            </td>
            <td style="width:51%;"></td>
        </tr>
        <tr>
            <td>
                <?php 
                if($dataService['MidWifeService']['cadiojathie']==1){
                    echo $this->Form->input('cadiojathie',array('type'=>'checkbox','name'=>'data[MidWifeService][cadiojathie]','value'=>'1','label'=>FALSE,'checked'=>TRUE,'disabled' => "disabled"));
                }else{
                    echo $this->Form->input('cadiojathie',array('type'=>'checkbox','name'=>'data[MidWifeService][cadiojathie]','value'=>'1','label'=>FALSE,'disabled' => "disabled"));
                }
                ?>
                <?php echo TABLE_CADIOJATHIE; ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php 
                if($dataService['MidWifeService']['asthma']==1){
                    echo $this->Form->input('asthma',array('type'=>'checkbox','name'=>'data[MidWifeService][asthma]','value'=>'1','label'=>FALSE,'checked'=>TRUE,'disabled' => "disabled"));
                }else{
                    echo $this->Form->input('asthma',array('type'=>'checkbox','name'=>'data[MidWifeService][asthma]','value'=>'1','label'=>FALSE,'disabled' => "disabled"));
                }
                ?>
                <?php echo TABLE_ASTHMA; ?>
            </td>
        </tr>
        <tr>
            <td><?php echo TABLE_OTHER; ?></td>
            <td>:</td>
            <td colspan="5"><?php echo $dataService['MidWifeService']['other']; ?></td>
        </tr>
    </table>
    <!--<div style="page-break-after:always;"></div>-->
<?php endforeach; ?>
<div class="clear"></div>
<div style="float:left;width: 450px;">
    <div>
        <input type="button" value="<?php echo ACTION_PRINT; ?>" id='btnDisappearPrint' class='noprint' style="width:50%;height:100px;font-size:29px;">                
    </div>
</div>
