<script type="text/javascript">
    $(document).ready(function() {       
        $(".btnBackQueueLabo").click(function(event) {
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableQueueLabo.fnDraw(false);
            var rightPanel = $(this).parent().parent().parent();
            var leftPanel = rightPanel.parent().find(".leftPanel");
            rightPanel.hide();
            rightPanel.html("");
            leftPanel.show("slide", {direction: "left"}, 500);
        });
    });
</script>
<div style="padding: 5px;border: 1px dashed #3C69AD;">
    <div class="buttons">
        <a href="" class="positive btnBackQueueLabo">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<div style="font-size:11px;">
    <fieldset>
        <?php
        $count = 0;
        $index=1;
        echo '<div class="column" style="background-color: #FFFFFF; float: left;padding: 10px;width: 30%;">';
        $oldLaboTitle = '';       
        foreach ($laboTitleGroup as $laboTitleGroups) {

//            if ($count == 5 || $count == 3) {
//                echo '</div><div class="column" style="background-color: #FFFFFF; float: left;padding: 10px;width: 30%;border-left: 1px solid #aaa;">';
//            }
            $titleName = $laboTitleGroups['LaboTitleGroup']['name'];
            $titleId = $laboTitleGroups['LaboTitleGroup']['id'];
            $itemId = $laboTitleGroups['LaboTitleGroup']['labo_item_group_id'];
            $subTitle = "";
            $query = mysql_query("SELECT LaboItemGroup.id, LaboItemGroup.labo_sub_title_group_id,LaboSubTitleGroup.name AS LaboSubTitleGroupName , LaboItemGroup.name, LaboItemGroup.price
                                    FROM labo_title_groups AS LaboTitleGroup
                                    INNER JOIN labo_item_groups AS LaboItemGroup ON LaboItemGroup.id
                                    IN ($itemId)   
                                    LEFT JOIN labo_sub_title_groups AS LaboSubTitleGroup ON LaboSubTitleGroup.id = LaboItemGroup.labo_sub_title_group_id
                                    WHERE LaboTitleGroup.id = $titleId
                                    ORDER BY LaboItemGroup.labo_sub_title_group_id, LaboItemGroup.code
                                   ");

            echo '<b style="background-color: #FFFFFF; color:#000;font-size:16px"><u>'.$titleName.'</u></b>';
            echo '<br />';
            while ($result = mysql_fetch_array($query)) {
                
                if($subTitle != $result['labo_sub_title_group_id']){
                   echo  $result['LaboSubTitleGroupName'];                   
                }
                $checked = false;
                if (in_array($result['id'], $laboSelected)) {
                    $checked = true;
                }
                
                if($index<10){
                    $index = '0'.$index;
                }
                echo '<table class="defaultTable" width="100%">
                            <tr>
                                <td>'.$index.'. </td>
                                <td>' . $this->Form->checkbox('', array('name' => 'data[LaboItemGroup][]', "value" => $result['id'], 'hiddenField' => false, 'checked' => $checked, 'disabled' => true, 'id' => 'laboItemGroup_' . $result['id'])) . '</td>
                                <td style="white-space: nowrap;">' . $result['name'] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                <td style="width: 80%;background: url(' . $this->webroot . 'img/bg_grey_dotted_h-line_3x1.png) repeat-x; background-position: 0 20px;"></td>                                
                             </tr>
                      </table>';
                $subTitle = $result['labo_sub_title_group_id'];
                if($index == 20 || $index == 39){
                      echo '</div><div class="column" style="background-color: #FFFFFF; float: left;padding: 10px;width: 30%;border-left: 1px solid #aaa;">';
                }
                $index++;
            }
            $count++;
        }
        
        echo '</div>';
        ?>                                               
    </fieldset>    
</div>
<script type="text/javascript">
    function equalHeight(group) {
        var tallest = 0;
        group.each(function() {
            var thisHeight = $(this).height();
            if(thisHeight > tallest) {
                tallest = thisHeight;
            }
        });
        group.height(tallest);
    }
</script>