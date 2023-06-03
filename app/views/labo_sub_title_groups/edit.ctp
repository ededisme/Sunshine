<?php 
echo $this->element('prevent_multiple_submit'); 
$absolute_url = FULL_BASE_URL . Router::url("/", false);
?>
<style type="text/css">
    #LaboSubTitleGroupParentId optgroup option {
        padding-left: 30px;
    }
</style>
<script type="text/javascript">
    $(document).ready(function() {
        // Prevent Key Enter
        preventKeyEnter();
        $("#LaboSubTitleGroupEditForm").validationEngine();
        $("#LaboSubTitleGroupEditForm").ajaxForm({           
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveLaboSubTitleGroup").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                var rightPanel=$("#LaboSubTitleGroupEditForm").parent();
                var leftPanel=rightPanel.parent().find(".leftPanel");
                rightPanel.hide();rightPanel.html("");
                leftPanel.show("slide", { direction: "left" }, 500);
                oCache.iCacheLower = -1;
                oTableLaboSubTitleGroup.fnDraw(false);
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
        $(".btnBackLaboSubTitleGroup").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableLaboSubTitleGroup.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });    
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="" class="positive btnBackLaboSubTitleGroup">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('LaboSubTitleGroup'); ?>
<?php echo $this->Form->input('id'); ?>
<?php $title = $titles['LaboSubTitleGroup']['name'] ?>
<fieldset>
    <legend><?php __(MENU_SUB_TITLE_GROUP_INFO); ?></legend>
    <table>
        <tr>
            <td><label for="LaboSubTitleGroupName"><?php echo TABLE_NAME; ?> <span class="red">*</span> :</label></td>
            <td><?php echo $this->Form->text('name', array('class' => 'validate[required]', 'value' => $title)); ?></td>
        </tr>                             
    </table>
</fieldset>
<br />
<div class="buttons">
    <button type="submit" class="positive txtSaveLaboSubTitleGroup">
        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
        <?php echo ACTION_SAVE; ?>
    </button>
</div>
