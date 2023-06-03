<script type="text/javascript">
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        $(".btnBackServicePriceInsurance").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableServicePriceInsurance.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });
</script>
<div style="padding: 5px;border: 1px dashed #3C69AD;">
    <div class="buttons">
        <a href="" class="positive btnBackServicePriceInsurance">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<table width="100%" class="info">
    <tr>
        <th><?php __(TABLE_SECTION_NAME); ?></th>
        <td> : <?php echo $servicesPriceInsurance['Section']['name']; ?></td>
    </tr>
    <tr>
        <th><?php __(TABLE_SERVICE_NAME); ?></th>
        <td> : <?php echo $servicesPriceInsurance['Service']['name']; ?></td>
    </tr>
    <tr>
        <th><?php __(TABLE_COMPANY_INSURANCE_NAME); ?></th>
        <td> : <?php echo $servicesPriceInsurance['CompanyInsurance']['name']; ?></td>
    </tr>   
</table>
<fieldset>
    <legend><?php __(MENU_SERVICES_PRICE); ?></legend>
    <table id="example" class="table" cellspacing="0">
        <tr>
            <th style="width: 5%;" class="first"><?php echo TABLE_NO; ?></th>
            <th><?php echo TABLE_PATIENT_GROUP; ?></th>
            <th><?php echo GENERAL_UNIT_PRICE; ?></th>            
        </tr>
        <?php 
        $index = 1;
        $unitPrice = 0;
        $patientGroup = "";    
        $query = mysql_query("SELECT name,unit_price FROM  services_price_insurance_patient_group_details
                                INNER JOIN patient_groups ON patient_groups.id =  services_price_insurance_patient_group_details.patient_group_id
                                WHERE services_price_insurance_patient_group_details.is_active = 1 AND  services_price_insurance_patient_group_details.services_price_insurance_id=".$servicesPriceInsurance['ServicesPriceInsurance']['id']);
        while ($row = mysql_fetch_row($query)) {
            $unitPrice = $row[1];
            $patientGroup = $row[0];        
        ?>
        <tr>
            <td class="first"><?php echo $index++;?></td>            
            <td><?php echo $patientGroup;?></td>
            <td><?php echo number_format($unitPrice, 2);?></td>
        </tr>
        <?php 
        }
        ?>
    </table>
</fieldset>
<br/>