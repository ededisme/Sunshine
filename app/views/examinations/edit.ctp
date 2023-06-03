<?php 
// Prevent Button Submit
echo $this->element('prevent_multiple_submit'); ?>
<script type="text/javascript">
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        $("#ExaminationEditForm").validationEngine();
        $("#ExaminationEditForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveExamination").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $(".btnBack").click();
                // alert message
                if(result != '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>' && result != '<?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?>'){
                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                }else {
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

        $(".btnBack").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableExamination.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
        
        $("#Examinations").val(<?php echo $this->data['Examination']['type']; ?>);
    });

</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="" class="positive btnBack">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php 
echo $this->Form->create('Examination'); ?>
<fieldset>
    <legend><?php __(MENU_CHIEF_COMPLAIN); ?></legend>
    <table>
        <input type="hidden" name="data[Examination][id]" value="<?php echo $this->data['Examination']['id']; ?>">
        <tr>
            <td><label for="CommuneProvinceId"><?php echo TABLE_NAME; ?> <span class="red">*</span> :</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->input('name', array( 'label' => false, 'class'=>'validate[required]')); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><label >Description <span class="red">*</span> :</label></td>
            <td><textarea id="Description" name="data[Examination][description]" style="width:97%;" class ="validate[required]" ><?php echo $this->data['Examination']['description']; ?></textarea></td>
        </tr> 
    </table>
</fieldset>
<br />
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
        <span class="txtSaveExamination"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<div style="clear: both;"></div>
<?php echo $this->Form->end(); ?>