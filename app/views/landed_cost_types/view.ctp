<script type="text/javascript">
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        $(".btnBackLandedCostType").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableLandedCostType.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="" class="positive btnBackLandedCostType">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<table width="100%" class="info">
    <tr>
        <th><?php __(TABLE_COMPANY); ?></th>
        <td>
            <?php 
            $sqlCom = mysql_query("SELECT name FROM companies WHERE id = ".$this->data['LandedCostType']['company_id']);
            $rowCom = mysql_fetch_array($sqlCom);
            echo $rowCom[0]; 
            ?>
        </td>
    </tr>
    <tr>
        <th><?php __(TABLE_NAME); ?></th>
        <td><?php echo $this->data['LandedCostType']['name']; ?></td>
    </tr>
</table>