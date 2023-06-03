<?php echo $this->element('prevent_multiple_submit'); ?>
<script type="text/javascript">
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        $("#BrandAddForm").validationEngine('attach', {
            isOverflown: true,
            overflownDIV: ".ui-tabs-panel"
        });
        $("#BrandAddForm").ajaxForm({
            beforeSerialize: function($form, options) {
                
            },
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveBrand").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $(".btnBackBrand").click();
                // alert message
                if(result != '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>' && result != '<?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?>' && result != '<?php echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM; ?>'){
                    createSysAct('Brand', 'Add', 2, result);
                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                }else {
                    createSysAct('Brand', 'Add', 1, '');
                    // alert message
                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result+'</p>');
                }
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
        
        $(".btnBackBrand").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableBrand.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
        
    });
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="" class="positive btnBackBrand">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php 
echo $this->Form->create('Brand'); ?>
<div>
    <fieldset>
        <legend><?php __(MENU_BRAND_MANAGEMENT_INFO); ?></legend>
        <table style="height: 100px;">
            <tr>
                <td><label for="BrandName"><?php echo TABLE_NAME; ?> <span class="red">*</span> :</label></td>
                <td>
                    <div class="inputContainer">
                        <?php echo $this->Form->text('name', array('class' => 'validate[required]')); ?>
                    </div>
                </td>
            </tr>
        </table>
    </fieldset>
</div>
<div style="clear: both;"></div>
<br />
<div class="buttons">
    <button type="submit" class="positive btnSaveBrand">
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <span class="txtSaveBrand"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<div style="clear: both;"></div>
<?php echo $this->Form->end(); ?>