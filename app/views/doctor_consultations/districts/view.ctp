<script type="text/javascript">
    $(document).ready(function(){
        $(".btnBackDistrict").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableDistrict.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="" class="positive btnBackDistrict">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<table width="100%" class="info">
    <tr>
        <th><?php __(TABLE_DISTRICT); ?></th>
        <td><?php echo $district['District']['name']; ?></td>
    </tr>
    <tr>
        <th><?php __(TABLE_PROVINCE); ?></th>
        <td><?php echo $province; ?></td>
    </tr>
</table>