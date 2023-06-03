<script type="text/javascript">
    $(document).ready(function(){
        $(".btnBackRoom").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableRoom.fnDraw(false);
            var rightPanel = $(this).parent().parent().parent();
            var leftPanel  = rightPanel.parent().find(".leftPanel");
            rightPanel.hide();
            rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="" class="positive btnBackRoom">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<fieldset>
    <legend><?php __(MENU_ROOM_MANAGEMENT_INFO); ?></legend>
    <table width="100%" class="info">
        <tr>
            <th><?php __(TABLE_COMPANY); ?></th>
            <td> : <?php echo $room['Company']['name']; ?></td>
        </tr>
        <tr>
            <th><?php __(MENU_ROOM_TYPE_MANAGEMENT); ?></th>
            <td> : <?php echo $room['RoomType']['name']; ?></td>
        </tr>
        <tr>
            <th><?php __(TABLE_FLOOR); ?></th>
            <td> : <?php echo $room['RoomFloor']['name']; ?></td>
        </tr>
        <tr>
            <th><?php __(TABLE_ROOM_NUMBER); ?></th>
            <td> : <?php echo $room['Room']['room_name']; ?></td>
        </tr>
        <tr>
            <th><?php __(GENERAL_DESCRIPTION); ?></th>
            <td> : <?php echo $room['Room']['description']; ?></td>
        </tr>
    </table>
</fieldset>