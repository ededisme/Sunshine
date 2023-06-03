<script type="text/javascript">
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        $(".btnBackService").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableService.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="" class="positive btnBackService">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<table width="100%" class="info">
    <tr>
        <th><?php __(TABLE_CODE); ?></th>
        <td><?php echo $service['Service']['code']; ?></td>
    </tr>
    <tr>
        <th><?php __(TABLE_SECTION); ?></th>
        <td><?php echo $service['Section']['name']; ?></td>
    </tr>
    <tr>
        <th><?php __(TABLE_NAME); ?></th>
        <td><?php echo $service['Service']['name']; ?></td>
    </tr>
    <!--
    <tr>
        <th><?php __(SALES_ORDER_UNIT_PRICE); ?></th>
        <td><?php echo number_format($service['Service']['unit_price']); ?> $</td>
    </tr>
    <tr>
        <th><?php __(TABLE_UOM); ?></th>
        <td>
            <?php 
            if(!empty($service['Service']['uom_id'])){
                $sqlUom = mysql_query("SELECT name FROM uoms WHERE id = ".$service['Service']['uom_id']);
                $rowUom = mysql_fetch_array($sqlUom);
                echo $rowUom[0];
            } 
            ?>
        </td>
    </tr>
    -->
    <tr>
        <th><?php __(GENERAL_DESCRIPTION); ?></th>
        <td><?php echo $service['Service']['description']; ?></td>
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
        $query = mysql_query("SELECT name,unit_price FROM services_patient_group_details
                                INNER JOIN patient_groups ON patient_groups.id = services_patient_group_details.patient_group_id
                                WHERE services_patient_group_details.is_active = 1 AND services_patient_group_details.service_id=".$service['Service']['id']);
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