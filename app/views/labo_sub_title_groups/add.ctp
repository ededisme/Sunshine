<?php 
echo $this->element('prevent_multiple_submit'); 
$absolute_url = FULL_BASE_URL . Router::url("/", false);
?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/plugins/localisation/jquery.localisation-min.js"></script>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/plugins/tmpl/jquery.tmpl.1.1.1.js"></script>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/plugins/blockUI/jquery.blockUI.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/ui.multiselect.css" />
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/ui.multiselect.js"></script>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/ui.multiselect-<?php echo $this->Session->read('lang'); ?>.js"></script>
<style type="text/css">
    #LaboSubTitleGroupParentId optgroup option {
        padding-left: 30px;
    }
</style>
<script type="text/javascript">
    $(document).ready(function() {
        $("#LaboSubTitleGroupPrice").autoNumeric();
        // Prevent Key Enter
        preventKeyEnter();
        $("#LaboSubTitleGroupAddForm").validationEngine();
        $("#LaboSubTitleGroupAddForm").ajaxForm({         
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveLaboSubTitleGroup").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                var rightPanel=$("#LaboSubTitleGroupAddForm").parent();
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
    
    String.prototype.trim = function() {
        return this.replace(/^\s+|\s+$/g,"");
    }
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
<fieldset>
    <legend><?php __(MENU_SUB_TITLE_GROUP_INFO); ?></legend>
    <table>
        <tr>
            <td><label for="LaboSubTitleGroupName"><?php echo TABLE_NAME; ?> <span class="red">*</span> :</label></td>
            <td><?php echo $this->Form->text('name', array('class' => 'validate[required]')); ?></td>
        </tr>                             
    </table>
</fieldset>
<br/>
<div class="buttons">
    <button type="submit" class="positive txtSaveLaboSubTitleGroup">
        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
        <span class="txtSaveLaboSubTitleGroup"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<?php echo $this->Form->end(); ?>