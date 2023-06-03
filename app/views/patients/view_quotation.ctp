<script type="text/javascript">
    $(document).ready(function() {
        $(".btnBackPatientEstimateExpense").click(function(event) {
            event.preventDefault();
            var rightPanel = $(this).parent().parent().parent();
            var leftPanel = rightPanel.parent().find(".leftPanel");
            rightPanel.hide("slide", {direction: "right"}, 500, function() {
                leftPanel.show();
                rightPanel.html('');
            });
        });
    });
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="" class="positive btnBackPatientEstimateExpense">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<table style="width: 100% !important;" class="info">
    <tr>
        <th style="width: 15%;"><?php __(PATIENT_CODE); ?></th>
        <td style="width: 25%;">: <?php echo $patient['Patient']['patient_code']; ?></td>
        <th style="width: 15%;"><?php __(TABLE_DOB); ?></th>
        <td style="width: 25%;">: 
            <?php echo date("d/m/Y", strtotime($patient['Patient']['dob']));?>
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
    </tr>
    <tr>
        <th><?php __(PATIENT_NAME); ?></th>
        <td>: <?php echo $patient['Patient']['patient_name']; ?></td>        
        <th><?php __(TABLE_SEX); ?></th>
        <td>: 
            <?php 
            if ($patient['Patient']['sex'] == "F") {
                echo GENERAL_FEMALE;
            } else {
                echo GENERAL_MALE;
            }
            ?>
        </td>
    </tr>   
    <tr>
        <th><?php __(TABLE_OCCUPATION); ?></th>
        <td>: <?php echo $patient['Patient']['occupation']; ?></td>
        <th><?php __(TABLE_NATIONALITY); ?></th>
        <td>: 
            <?php                 
                if($patient['Patient']['patient_group_id']!=""){
                    $query = mysql_query("SELECT name FROM patient_groups WHERE id=".$patient['Patient']['patient_group_id']);
                    while ($row = mysql_fetch_array($query)) {
                        if($patient['Patient']['patient_group_id']==1){
                            echo $row['name'];
                        }else{
                            echo $row['name'].'&nbsp;&nbsp;('.$patient['Nationality']['name'].')';
                        }
                    }
                }else{
                    echo $patient['Nationality']['name'];
                }
            ?>
        </td>
    </tr>
    <tr>   
        <th><?php __(TABLE_TELEPHONE); ?></th>
        <td>: <?php echo $patient['Patient']['telephone']; ?></td>
        <th><?php __(TABLE_EMAIL); ?></th>
        <td>: <?php echo $patient['Patient']['email']; ?></td>
    </tr>    
    <tr>
        <th><?php __(TABLE_ADDRESS); ?></th>
        <td>: <?php echo $patient['Patient']['address']; ?></td>        
        <th><?php __(TABLE_CITY_PROVINCE); ?></th>
        <td>: 
            <?php
            if($patient['Patient']['location_id']!=""){
                $query = mysql_query("SELECT name FROM patient_locations WHERE id=".$patient['Patient']['location_id']);
                if(mysql_num_rows($query)){
                    while ($row = mysql_fetch_array($query)) {
                        echo $row['name'];                
                    }
                }
            }
            ?>
        </td>
    </tr>
    <tr>
        <th><?php __(TABLE_COMPANY); ?></th>
        <td>: <?php echo $patient['Company']['name']; ?></td>     
        <th>&nbsp;</th>
        <td>&nbsp;</td>
    </tr>
</table>
<br/>
<fieldset>
    <legend><?php __(MENU_EXCLUDE); ?></legend>
    <?php        
        $index = 1;
        $queryExclude = mysql_query("SELECT eq.* FROM patient_quotation_exclude_details AS pqed INNER JOIN exclude_quotations AS eq ON eq.id = pqed.exclude_quotation_id WHERE pqed.is_active=1 AND pqed.patient_quotation_id = ".$patient['PatientQuotation']['id']);
        while ($resultExclude = mysql_fetch_array($queryExclude)) {
            echo '<p>'.$index++.'- '.$resultExclude['name_' . $_SESSION['lang']].'</p>';
        }
    ?>
</fieldset>
<br/>
<fieldset>
    <legend><?php __(MENU_QUOTATION_PATIENT_SERVICE_INFO); ?></legend>
    <table id="example" class="table" cellspacing="0">
        <tr>
            <th style="width: 5%;" class="first"><?php echo TABLE_NO; ?></th>
            <th><?php echo SECTION_SECTION; ?></th>
            <th><?php echo TABLE_SERVICE_NAME; ?></th>
            <th><?php echo TABLE_QTY; ?></th>
            <th><?php echo GENERAL_PRICE; ?> ($)</th>
            <th><?php echo GENERAL_AMOUNT; ?> ($)</th>
            <th><?php echo DRUG_NOTE; ?></th>
        </tr>        
        <?php        
        $index = 1;
        $queryService = mysql_query("SELECT ser.*, sec.name As sectionName, pqsd.price, pqsd.description, pqsd.qty  
                                        FROM patient_quotation_service_details AS pqsd 
                                        INNER JOIN patient_quotations AS pq ON pq.id = pqsd.patient_quotation_id
                                        INNER JOIN services AS ser ON ser.id = pqsd.service_id 
                                        INNER JOIN sections AS sec ON sec.id = ser.section_id 
                                        WHERE pqsd.is_active=1 AND pqsd.patient_quotation_id = ".$patient['PatientQuotation']['id']);
        while ($resultService = mysql_fetch_array($queryService)) {                            
            ?>
            <tr>
                <td class="first"><?php echo $index++;?></td>
                <td><?php echo $resultService['sectionName']?></td>
                <td><?php echo $resultService['name']?></td>
                <td><?php echo $resultService['qty'];?></td>
                <td><?php echo number_format($resultService['price'], 2);?></td>
                <td style="text-align: right;"><?php echo number_format($resultService['price']*$resultService['qty'], 2);?></td>
                <td><?php echo $resultService['description']?></td>
            </tr>
        <?php }?>        
    </table>
</fieldset> 