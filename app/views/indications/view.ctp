<script type="text/javascript">
    $(document).ready(function(){
        $(".btnBackIndication").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableIndication.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });
</script>
<div style="padding: 5px;border: 1px dashed #3C69AD;">
    <div class="buttons">
        <a href="" class="positive btnBackIndication">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<table width="100%" class="info">
    <tr>
        <th><?php __(TABLE_NAME); ?></th>
        <td><?php echo $indication['Indication']['name']; ?></td>
    </tr>
</table>