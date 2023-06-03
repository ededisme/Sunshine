<script type="text/javascript">
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        $(".btnBack").click(function(event){
            event.preventDefault();
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
            oCache.iCacheLower = -1;
            oTableVendorGroup.fnDraw(false);
        });
    });
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="" class="positive btnBack">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<table width="100%" class="info">
    <tr>
        <th><?php echo TABLE_SUB_OF_GROUP; ?> :</th>
        <td>
            <?php
            if(!empty($companyGroup[0]['PatientCompanyGroup']['parent_id'])){
                $sqlSub = mysql_query("SELECT name FROM patient_company_groups WHERE id = ".$companyGroup[0]['PatientCompanyGroup']['parent_id']);
                $rowSub = mysql_fetch_array($sqlSub);
                echo $rowSub[0];
            }
            ?>
        </td>
    </tr>
    <tr>
        <th><?php echo TABLE_NO; ?>:</th>
        <td><?php echo $companyGroup[0]['PatientCompanyGroup']['id']; ?></td>
    </tr>
    <tr>
        <th><?php echo TABLE_NAME; ?>: </th>
        <td><?php echo $companyGroup[0]['PatientCompanyGroup']['name']; ?></td>
    </tr>
    <tr>
        <th><?php echo TABLE_CREATED; ?>:</th>
        <td><?php echo $companyGroup[0]['PatientCompanyGroup']['created']; ?></td>
    </tr>
</table>