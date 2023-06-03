<?php echo $this->element('prevent_multiple_submit'); ?>
<?php $absolute_url  = FULL_BASE_URL . Router::url("/", false); ?>
<?php echo $javascript->link('uninums.min'); ?>
<script type="text/javascript">
    $(document).ready(function(){        
        // Prevent Key Enter
        preventKeyEnter();
        $("#RoomFloorAddForm").validationEngine();
        $("#RoomFloorAddForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveService").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $(".btnBackRoomFloor").click();
                // alert message
                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>' + result + '</p>');
                $("#dialog").dialog({
                    title: '<?php echo DIALOG_INFORMATION; ?>',
                    resizable: false,
                    modal: true,
                    width: 'auto',
                    height: 'auto',
                    open: function(event, ui) {
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
        $(".btnBackRoomFloor").click(function(event) {
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableRoomFloor.fnDraw(false);
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
        <a href="" class="positive btnBackRoomFloor">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('RoomFloor'); ?>
<fieldset>
    <legend><?php __(MENU_ROOM_FLOOR_MANAGEMENT_INFO); ?></legend>
    <table>
        <tr>
            <td><?php echo MENU_ROOM_FLOOR_MANAGEMENT;?><span class="red" style="padding-right:5px">*</span>:</td>
            <td>
                <?php echo $this->Form->input('name', array('label' => false, 'class' => 'validate[required]')); ?>
            </td>
        </tr>
        <tr>
            <td><?php echo GENERAL_DESCRIPTION;?>:</td>
            <td>
                <?php echo $this->Form->textarea('description', array('label' => false)); ?>
            </td>
        </tr>    
    </table>
</fieldset>
<br/>
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
        <span class="txtSaveService"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<div style="clear: both;"></div>
<?php echo $this->Form->end(); ?>