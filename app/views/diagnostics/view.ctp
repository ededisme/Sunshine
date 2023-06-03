<script type="text/javascript">
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        $(".btnBack").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableDiagnostic.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
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
<br />
<fieldset>
    <legend><?php __(MENU_DIAGNOSTICS); ?></legend>
    <table style="width: 100%;" cellpadding="5">
        <tr>
            <th style="font-size: 12px; width: 10%;"><?php __(TABLE_NAME); ?> :</th>
            <td style="font-size: 12px;"><?php echo $this->data['Diagnostic']['name']; ?></td>
        </tr>
<!--        <tr>
            <th style="font-size: 12px; width: 10%;"><?php __(TABLE_NAME_IN_KHMER); ?> :</th>
            <td style="font-size: 12px;">
             <?php 
//                if($this->data['Diagnostic']['type'] == 1){
//                    echo MAN_DISEASE; 
//                } else {
//                    echo ANDROLOGY_MAN_AND_WOMAN_DISEASE; 
//                }
                ?>
            </td>
        </tr>-->
        <tr>
            <th style="font-size: 12px;"><?php echo  TABLE_DESCRIPTION ;  ?> :</th>
            <td style="font-size: 12px;"><?php echo $this->data['Diagnostic']['description']; ?></td>
        </tr>
        </tr>
        <tr>
            <th style="font-size: 12px;"><?php echo TABLE_CREATED; ?> :</th>
            <td style="font-size: 12px;" colspan="5"><?php echo $this->data['Diagnostic']['created']; ?></td>
        </tr>
    </table>
</fieldset>
<br />   