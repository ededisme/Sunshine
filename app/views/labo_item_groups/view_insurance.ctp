<table width="100%" class="info">
    <tr>
        <th><?php __(TABLE_COMPANY); ?></th>
        <td> : <?php echo $laboItemGroup['Company']['name'];?></td>
    </tr>
    <tr>
        <th><?php __(TABLE_CODE); ?></th>
        <td> : <?php echo $laboItemGroup['LaboItemGroup']['code'];?></td>
    </tr>
    <tr>
        <th><?php __(MENU_LABO_SUB_GROUP_NAME); ?></th>
        <td> : <?php echo $laboItemGroup['LaboItemGroup']['name'];?></td>
    </tr>
</table>
<fieldset>
    <legend><?php __(MENU_LABO_SUB_GROUP_PRICE); ?></legend>
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
        $query = mysql_query("SELECT name,unit_price FROM labo_item_patient_groups
                                INNER JOIN patient_groups ON patient_groups.id = labo_item_patient_groups.patient_group_id
                                WHERE labo_item_patient_groups.is_active = 1 AND labo_item_patient_groups.labo_item_group_id=".$laboItemGroup['LaboItemGroup']['id']);
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