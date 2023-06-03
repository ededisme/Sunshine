<?php 
// Prevent Button Submit
echo $this->element('prevent_multiple_submit'); ?>
<script type="text/javascript">
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        $("#GroupInsuranceEditForm").validationEngine('attach', {
            isOverflown: true,
            overflownDIV: ".ui-tabs-panel"
        });
        $("#GroupInsuranceEditForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveGroupInsurance").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $(".btnBackGroupInsurance").click();
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
        $(".btnBackGroupInsurance").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableGroupInsurance.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });
</script>
<div style="padding: 5px;border: 1px dashed #3C69AD;">
    <div class="buttons">
        <a href="" class="positive btnBackGroupInsurance">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('GroupInsurance'); ?>
<?php echo $this->Form->input('id'); ?>
<fieldset>
    <legend><?php __(MENU_COMPANY_INSURANCE_GROUP_MANAGEMENT_INFO); ?></legend>
    <table>       
        <tr>
            <td><label for="GroupInsuranceName"><?php echo TABLE_NAME; ?> <span class="red">*</span> :</label></td>
            <td>
                <div class="inputContainer">
                    <input type="hidden" id="tableName" name="tableName" value="departments" />
                    <input type="hidden" id="fieldCurrentId" name="fieldCurrentId" value="<?php echo $this->data['GroupInsurance']['id']; ?>" />
                    <input type="hidden" id="fieldName" name="fieldName" value="name" />
                    <input type="hidden" id="fieldCondition" name="fieldCondition" value="is_active=1" />
                    <?php echo $this->Form->text('name', array('class'=>'validate[required]')); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top;"><label for="GroupInsuranceDescription"><?php echo GENERAL_DESCRIPTION; ?>:</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->textarea('description', array('style' => 'width:400px')); ?>
                </div>
            </td>
        </tr>
    </table>
</fieldset>
<br />
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <span class="txtSaveGroupInsurance"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<div style="clear: both;"></div>
<?php echo $this->Form->end(); ?>