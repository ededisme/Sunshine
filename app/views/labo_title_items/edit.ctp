<?php echo $this->element('prevent_multiple_submit'); ?>
<script type="text/javascript">
    $(document).ready(function() {
        // Prevent Key Enter
        preventKeyEnter();
        $("#LaboTitleItemEditForm").validationEngine('attach', {
            isOverflown: true,
            overflownDIV: ".ui-tabs-panel"
        });
        $("#LaboTitleItemEditForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSavePlace").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $(".btnBackLaboTitleItem").click();
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
        $(".btnBackLaboTitleItem").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableLaboTitleItem.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });
</script>
<div style="padding: 5px;border: 1px dashed #3C69AD;">
    <div class="buttons">
        <a href="" class="positive btnBackLaboTitleItem">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('LaboTitleItem'); ?>
<?php echo $this->Form->input('id'); ?>
<fieldset>
    <legend><?php __(MENU_TITLE_ITEM_INFO); ?></legend>
    <table>
        <tr>
            <td><label for="LaboTitleItemName"><?php echo TABLE_NAME; ?> <span class="red">*</span> :</label></td>
            <td>
                <div class="inputContainer">
                    <input type="hidden" id="tableName" name="tableName" value="labo_title_items" />
                    <input type="hidden" id="fieldCurrentId" name="fieldCurrentId" value="" />
                    <input type="hidden" id="fieldName" name="fieldName" value="name" />
                    <input type="hidden" id="fieldCondition" name="fieldCondition" value="is_active=1" />
                    <?php echo $this->Form->text('name', array('class'=>'validate[required,ajax[ajaxUserCall]]')); ?>
                </div>
            </td>
        </tr>
    </table>
</fieldset>
<br />
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <span class="txtSavePlace"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<div style="clear: both;"></div>
<?php echo $this->Form->end(); ?>