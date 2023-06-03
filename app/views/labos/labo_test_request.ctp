<?php echo $this->element('prevent_multiple_submit'); ?>
<script type="text/javascript">
    $(document).ready(function() {       
        // Prevent Key Enter
        preventKeyEnter();
        $("#FormLaboRequest").validationEngine();
        $("#FormLaboRequest").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSavePlace").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $(".btnBackQueueLabo").click();
                // alert message
                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result+'</p>');
                $("#dialog").dialog({
                    title: '<?php echo DIALOG_INFORMATION; ?>',
                    resizable: false,
                    modal: true,
                    width: 'auto',
                    height: 'auto',
                    open: function(event, ui){
                        $(".ui-dialog-buttonpane").show();
                    },
                    buttons: {
                        '<?php echo ACTION_CLOSE; ?>': function() {
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
        
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
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="" class="positive btnBackQueueLabo">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $form->create('Labo',array('id'=>'FormLaboRequest','action'=>'laboRequestSave'));?>
<div style="font-size:11px;">    
    <?php echo $form->hidden('queued_patient_id',array('name'=>'data[QueuedLabo][id]','value'=>$QueuedLaboId));?>
    <?php echo $form->hidden('labo_id',array('name'=>'data[Labo][id]','value'=>$labo['Labo']['id']));?>
    <fieldset>
        
        <?php 
        $count=0;
        $index=1;
        echo '<div class="column" style="background-color: #FFFFFF; float: left;padding: 10px;width: 30%;">';
        foreach ($laboTitleGroup as $laboTitleGroups){
            if($count==3 || $count==5){
                echo '</div><div class="column" style="background-color: #FFFFFF; float: left;padding: 10px;width: 30%;border-left: 1px solid #aaa;">';
            }
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
                                    ORDER BY LaboItemGroup.labo_sub_title_group_id
                                   ");
            
            echo '<b style="background-color: #FFFFFF; color:#000;font-size:16px"><u>'.$titleName.'</u></b>';
            echo '<br />';
            while ($result = mysql_fetch_array($query)) {
                if($subTitle != $result['labo_sub_title_group_id']){
                   echo '<br />';
                   echo  $result['LaboSubTitleGroupName'];
                }
                if($index<10){
                    $index = '0'.$index;
                }
                 echo '<table style="background-color: #FFFFFF;" class="defaultTable" width="100%">
                           <tr>
                                <td>'.$index.'. </td>
                                <td>' . $this->Form->checkbox('', array('name' => 'data[LaboItemGroup][]', "value"=>$result['id'], 'hiddenField' => false, 'id' => 'laboItemGroup_'.$result['id'])) . '</td>
                                <td style="white-space: nowrap;">'.'<label for="laboItemGroup_'.$result['id'].'">'.$result['name'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>'.'</td>
                                <td style="width: 80%;background: url(' . $this->webroot . 'img/bg_grey_dotted_h-line_3x1.png) repeat-x; background-position: 0 20px;"></td>                                
                           </tr>
                       </table>';
                 $subTitle = $result['labo_sub_title_group_id'];
                 $index++;
            }
            $count++;
        }        
        echo '</div>';
        ?>                                               
    </fieldset>        
</div>
<br/>
<br />
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
        <span class="txtSavePlace"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<?php echo $this->Form->end(); ?>