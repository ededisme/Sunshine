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

<div id="printQuotationPatient" class="print_doc">
    <table style="width: 100%;">   
        <tr>
            <td style="vertical-align: top;text-align: center;">
                <img alt="" src="<?php echo $this->webroot; ?>img/logo_s.png" />           
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
    <table style="width: 100%;">
        <tr>
            <td colspan="3">
                <?php echo TABLE_ALLERGIC_MEDICINE;?>:
                <?php
                    if($patient['Patient']['allergic_medicine']!="") { 
                        echo GENERAL_YES;
                        if($_SESSION['lang']=="en"){
                            echo str_pad('', 120, '.', STR_PAD_RIGHT);
                        }else{
                            echo str_pad('', 114, '.', STR_PAD_RIGHT);
                        }
                    }else {
                        if($_SESSION['lang']=="en"){
                            echo str_pad('', 150, '.', STR_PAD_RIGHT);
                        }else{
                            echo str_pad('', 114, '.', STR_PAD_RIGHT);
                        }
                    }
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <?php echo TABLE_ALLERGIC_FOOD;?>:
                <?php
                    if($patient['Patient']['allergic_food']!="") { 
                        echo GENERAL_YES;
                        echo str_pad('', 200, '.', STR_PAD_RIGHT);
                    }else {
                        echo str_pad('', 200, '.', STR_PAD_RIGHT);                       
                    }
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <input type="checkbox" <?php if($patient['Patient']['patient_group']==1){ if($patient['Patient']['sex']=="M") {echo 'checked="true"';}}?>/><?php echo GENERAL_MR;?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox" <?php if($patient['Patient']['patient_group']==1){ if($patient['Patient']['sex']=="F") {echo 'checked="true"';}}?>/><?php echo GENERAL_MRS;?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox" /><?php echo GENERAL_MS;?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox" <?php if($patient['Patient']['patient_group']==2){ if($patient['Patient']['sex']=="M") {echo 'checked="true"';}}?>/><?php echo GENERAL_MASTER;?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox" <?php if($patient['Patient']['patient_group']==2){ if($patient['Patient']['sex']=="F") {echo 'checked="true"';}}?>/><?php echo GENERAL_MISS;?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo PATIENT_CODE;?>: <?php echo $patient['Patient']['patient_code']; ?>
            </td>
            <td colspan="2">
                <?php echo PATIENT_NAME;?>: <?php echo $patient['Patient']['patient_name']; ?>
            </td>                        
        </tr>
        <tr>
            <td style="width: 35%;">
                <?php echo TABLE_DOB;?>: <?php echo $patient['Patient']['dob']?>
                &nbsp;&nbsp;&nbsp;&nbsp;
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
            <td style="width: 15%;">
                <?php echo TABLE_RELIGION;?>: 
                <?php 
                if($patient['Patient']['religion']!=""){
                    echo $patient['Patient']['religion'];
                }else{
                    if($_SESSION['lang']=="en"){
                        echo str_pad('', 29, '.', STR_PAD_RIGHT);
                    }else{
                        echo str_pad('', 35, '.', STR_PAD_RIGHT);
                    }
                    
                }                
                ?>
            </td>
            <td style="width: 50%;" style="white-space:pre-wrap !important; "><?php echo TABLE_NATIONALITY;?>: 
                <?php                 
                    if($patient['Patient']['patient_group_id']!=""){
                        $query = mysql_query("SELECT id, name FROM patient_groups WHERE id=".$patient['Patient']['patient_group_id']);
                        while ($row = mysql_fetch_array($query)) {
                            if($row['id']!=1){
                                echo $row['name'].'&nbsp;&nbsp;('.$patient['Nationality']['name'].')';
                            }else{
                                echo $row['name'];
                            }
                            
                        }
                    }else{
                        echo $patient['Nationality']['name'];
                    }
                ?>
            </td>
        </tr>
        <tr>
            <td style="white-space: nowrap">
                <?php echo TABLE_ID_CARD;?>: 
                <?php 
                if($patient['Patient']['patient_id_card']!=""){
                    echo $patient['Patient']['patient_id_card'];
                }else{
                    echo str_pad('', 47, '.', STR_PAD_RIGHT);
                }                
                ?>                
            </td>
            <td colspan="2" style="white-space: nowrap">
                <?php echo TABLE_OCCUPATION;?>:
                <?php 
                if($patient['Patient']['occupation']!=""){
                    echo $patient['Patient']['occupation'];
                }else{
                    echo str_pad('', 100, '.', STR_PAD_RIGHT);
                }                
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="3"><?php echo TABLE_ADDRESS;?>: 
                <?php 
                if($patient['Patient']['address']!=""){
                    echo $patient['Patient']['address'];
                }
                if($patient['Patient']['location_id']!=""){
                    $query = mysql_query("SELECT name FROM patient_locations WHERE id=".$patient['Patient']['location_id']);
                    while ($row = mysql_fetch_array($query)) {
                        if($patient['Patient']['address']!=""){
                            echo ', ';
                        }
                        echo $row['name'];                
                    }
                }
                
                ?>
            </td>            
        </tr>
        <tr>
            <td style="white-space: nowrap"><?php echo TABLE_TELEPHONE;?>:                 
                <?php 
                if($patient['Patient']['telephone']!=""){
                    echo $patient['Patient']['telephone'];
                }else{
                    echo str_pad('', 65, '.', STR_PAD_RIGHT);
                }                
                ?>
            </td>
            <td style="white-space: nowrap">
                <?php echo TABLE_FAX_NUMBER;?>: 
                <?php 
                if($patient['Patient']['patient_fax_number']!=""){
                    echo $patient['Patient']['patient_fax_number'];
                }else{
                    echo str_pad('', 30, '.', STR_PAD_RIGHT);
                }
                ?>
            </td>
            <td style="white-space: nowrap">
                <?php echo TABLE_EMAIL;?>:
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
            <td colspan="3" style="white-space: nowrap"><?php echo TABLE_CASE_EMERGENCY_NAME?>: 
                <?php 
                if($patient['Patient']['case_emergency_name']!=""){
                    echo $patient['Patient']['case_emergency_name'];
                }else{
                    echo str_pad('', 157, '.', STR_PAD_RIGHT);
                }
                ?>
            </td>
        </tr>
        <tr>
            <td style="white-space: nowrap"><?php echo TABLE_CASE_EMERGENCY_TEL?>: 
                <?php 
                if($patient['Patient']['case_emergency_tel']!=""){
                    echo $patient['Patient']['case_emergency_tel'];
                }else{
                    if($_SESSION['lang']=="en"){
                        echo str_pad('', 38, '.', STR_PAD_RIGHT);
                    }else{
                        echo str_pad('', 23, '.', STR_PAD_RIGHT);
                    }
                    
                }
                ?>                
            </td>
            <td><?php echo TABLE_FAX_NUMBER;?>: <?php echo str_pad('', 30, '.', STR_PAD_RIGHT);?></td>
            <td><?php echo TABLE_EMAIL;?>: <?php echo str_pad('', 100, '.', STR_PAD_RIGHT);?></td>
        </tr>
        <tr>
            <td colspan="3" style="white-space: nowrap">
                <?php echo TABLE_RELITION_PATIENT;?>: 
                <?php 
                if($patient['Patient']['relation_patient']!=""){
                    echo $patient['Patient']['relation_patient'];
                }else{
                    echo str_pad('', 200, '.', STR_PAD_RIGHT);
                }
                ?>
            </td>
        </tr>
    </table>
    <br/>
    <table style="width: 100%;border: 2px solid #000;">
       
        <tr>
            <td colspan="2"><?php echo TITLE_PATIENT_UNDER_15YEARS_OLD;?>:</td>            
        </tr>
        <tr>
            <td style="width: 102px;"><?php echo TABLE_FATHER_NAME;?>:</td>
            <td>
                <?php 
                if($patient['Patient']['father_name']!=""){
                    echo $patient['Patient']['father_name'];
                }else{
                    echo str_pad('', 170, '.', STR_PAD_RIGHT);
                }                    
                ?>
            </td>
        </tr>
        <tr>
            <td><?php echo TABLE_MOTHER_NAME;?>:</td>
            <td>
                <?php 
                if($patient['Patient']['mother_name']!=""){
                    echo $patient['Patient']['mother_name'];
                }else{
                    echo str_pad('', 170, '.', STR_PAD_RIGHT);
                }
                ?>
            </td>
        </tr>
    </table>
    <br/>
    <table style="width: 100%;">
        <tr>
            <td><?php echo TABLE_BILL_PAID_BY;?>: <?php echo $patient['PatientBillType']['name']; ?></td>
        </tr>
    </table>
    <br/>
    <fieldset>
        <legend><?php __(HOW_TO_KNOW_LIM_TAING_CLINIC); ?></legend>
        <?php         
        foreach ($patientConnections as $patientConnection) {
            if(in_array($patientConnection['PatientConnectionWithHospital']['id'], $patientConnectionDetails) ){
                echo '<input checked="true" id="PatientConnectionWithHospital'.$patientConnection['PatientConnectionWithHospital']['id'].'" name="data[Patient][patient_conection_id][]" type="checkbox" value="'.$patientConnection['PatientConnectionWithHospital']['id'].'"/>';
            }else{
                echo '<input id="PatientConnectionWithHospital'.$patientConnection['PatientConnectionWithHospital']['id'].'" name="data[Patient][patient_conection_id][]" type="checkbox" value="'.$patientConnection['PatientConnectionWithHospital']['id'].'"/>';
            }

            echo '<label style="padding-right: 20px;" for="PatientConnectionWithHospital'.$patientConnection['PatientConnectionWithHospital']['id'].'">'.$patientConnection['PatientConnectionWithHospital']['name'].'</label>';
        }
        ?>
    </fieldset>
    <br />
    <br />
    <span style="float: left;font-size: 14px;">                
        <p style="text-align: left;"><?php echo GENERAL_REGISTRATION_STAFF; ?></p>            
    </span>
    <span style="float: right;font-size: 14px;">
        <p style="text-align: left;"><?php echo GENERAL_PATIENT_SIGNATURE; ?></p>            
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
    <?php echo $this->element('print_footer_patient_information_fix'); ?>
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