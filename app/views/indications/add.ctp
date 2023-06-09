<?php echo $this->element('prevent_multiple_submit'); ?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/jquery.form.js"></script>
<script type="text/javascript">
    $(document).ready(function() {        
        // Prevent Key Enter
        preventKeyEnter();
        $("#IndicationAddForm").validationEngine();
        $("#IndicationAddForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSavePlace").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $(".btnBackIndication").click();
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
<?php echo $this->Form->create('Indication'); ?>
<fieldset>
    <legend><?php __(MENU_INDICATION_MANAGEMENT_INFO); ?></legend>
    <table>
        <tr>
            <td><label for="IndicationName"><?php echo TABLE_NAME; ?> <span class="red">*</span> :</label></td>
            <td>                
                <?php echo $this->Form->text('name', array('class' => 'validate[required]')); ?>
            </td>
        </tr>
    </table>
</fieldset>
<br/>
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <?php echo ACTION_SAVE; ?>
    </button>
</div>
<?php echo $this->Form->end(); ?>
    

