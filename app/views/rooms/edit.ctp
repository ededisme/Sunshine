<?php
echo $this->element('prevent_multiple_submit'); 
e($javascript->link('autoNumeric-1.6.2'));
?>
<script type="text/javascript">
    $(document).ready(function() {           
        // Prevent Key Enter
        preventKeyEnter();
        $("#RoomEditForm").validationEngine();
        $("#RoomEditForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveRoom").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $(".btnBackRoom").click();
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
        $(".btnBackRoom").click(function(event) {
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableRoom.fnDraw(false);
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
        <a href="" class="positive btnBackRoom">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('Room'); ?>
<?php echo $this->Form->input('id'); ?>
<fieldset>
    <legend><?php __(MENU_ROOM_MANAGEMENT_INFO); ?></legend>
    <table>
        <tr>
            <td><label for="RoomCompanyId"><?php echo TABLE_COMPANY; ?> <span class="red">*</span> :</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->input('company_id', array('empty' => SELECT_OPTION, 'class' => 'classCompany validate[required]', 'label' => false)); ?>
                </div>
            </td>
        </tr>        
        <tr>
            <td><label for="RoomTypeIdId"><?php echo MENU_ROOM_TYPE_MANAGEMENT; ?> <span class="red">*</span> :</label></td>
            <td><?php echo $this->Form->input('room_type_id', array('empty' => SELECT_OPTION, 'class' => 'classCompany validate[required]', 'label' => false)); ?></td>
        </tr>        
        <tr>
            <td><label for="RoomFloor"><?php echo TABLE_FLOOR; ?> :</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->input('room_floor_id', array('empty' => SELECT_OPTION, 'class' => 'classCompany', 'label' => false)); ?>                    
                </div>
            </td>
        </tr>
        <tr style="display: none;">
            <td><label for="RoomScreenDisplay"><?php echo 'Display Screen'; ?>:</label></td>
            <td>
                <div class="inputContainer">
                    <select name="data[Room][screen_display]" id="ProductIsExpiredDate">
                        <option <?php if($this->data['Room']['screen_display'] == 0){ echo 'selected="selected"';}?> value="0"><?php echo ACTION_NO; ?></option>
                        <option <?php if($this->data['Room']['screen_display'] == 1){ echo 'selected="selected"';}?> value="1"><?php echo ACTION_YES; ?></option>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td><label for="RoomName"><?php echo TABLE_ROOM_NUMBER; ?> <span class="red">*</span> :</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->text('room_name', array('class' => 'validate[required]')); ?>
                </div>
            </td>
        </tr>        
        <tr>
            <td style="vertical-align: top;"><label for="RoomDescription"><?php echo GENERAL_DESCRIPTION; ?>:</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->textarea('description'); ?>
                </div>
            </td>
        </tr>
    </table>    
</fieldset>
<br/>
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
        <span class="txtSaveRoom"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<div style="clear: both;"></div>
<?php echo $this->Form->end(); ?>