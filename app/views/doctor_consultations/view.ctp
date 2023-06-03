<script type="text/javascript">
    $(document).ready(function(){
        $(".btnBackDoctorConsltation").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableDoctorConsultation.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="" class="positive btnBackDoctorConsltation">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<fieldset>
    <legend><?php __(MENU_DOCTOR_CONSULTATION); ?></legend>
    <table width="100%" class="info">
        <tr>
            <th style="width: 5%;"><?php __(DOCTOR_NAME); ?></th>
            <td style="width: 15%;"><?php echo $this->data['DoctorConsultation']['name']; ?></td>
            
            <th style="width: 5%;"><?php __(TABLE_SEX); ?></th>
            <td style="width: 10%;"><?php echo $this->data['DoctorConsultation']['sex']; ?></td>
            
            <th style="width: 5%;"><?php __(TABLE_TELEPHONE_PERSONAL); ?></th>
            <td style="width: 10%;"><?php echo $this->data['DoctorConsultation']['phone_number']; ?></td>
            
            <th style="width: 5%;"><?php __(TABLE_EMAIL); ?></th>
            <td style="width: 15%;"><?php echo $this->data['DoctorConsultation']['email']; ?></td>
        </tr>
    </table>
</fieldset>