<script type="text/javascript">
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        $(".btnBackPgroup").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableBrand.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="" class="positive btnBackPgroup">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<fieldset>
    <legend><?php __(MENU_BRAND_MANAGEMENT_INFO); ?></legend>
    <table width="100%" class="info">
        <tr>
            <td style="width: 10%;"><?php echo TABLE_NAME; ?> :</td>
            <td><?php echo $brand['Brand']['name']; ?></td>
        </tr>
    </table>
</fieldset>