<?php 
// Prevent Button Submit
echo $this->element('prevent_multiple_submit'); ?>
<script type="text/javascript">
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        $("#sex_doctor").chosen({width: 405});
        $("#DoctorConsultationEditForm").validationEngine('attach', {
            isOverflown: true,
            overflownDIV: ".ui-tabs-panel"
        });
        $("#DoctorConsultationEditForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveDoctorConsultation").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $(".btnBackDoctorConsultation").click();
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
        $(".btnBackDoctorConsultation").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableDoctorConsultation.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="" class="positive btnBackDoctorConsultation">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('DoctorConsultation'); ?>
<fieldset>
    <legend><?php __(MENU_DOCTOR_CONSULTATION); ?></legend>
    <table style="width: 50%;">
        <tr>
            <td style="width: 30%;"><label for="Name"><?php echo DOCTOR_NAME; ?> <span class="red">*</span> :</label></td>
            <td style="width: 70%;">
                <div class="inputContainer" style="width: 100%;">                    
                    <?php echo $this->Form->text('name', array('class' => 'validate[required]', 'style' => 'width: 90%;')); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td style="width: 30%;"><label for="Sex"><?php echo TABLE_SEX; ?> <span class="red">*</span> :</label></td>
            <td style="width: 70%;">
                <div class="inputContainer" style="width: 100%;">
                    <?php echo $this->Form->input('sex', array('label' => false, 'data-placeholder' => INPUT_SELECT, 'id' => 'sex_doctor', 'style'=>'width: 95%;')); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td style="width: 30%;"><label for="PhoneNumber"><?php echo TABLE_TELEPHONE_PERSONAL; ?> :</label></td>
            <td style="width: 70%;">
                <div class="inputContainer" style="width: 100%;">
                    <?php echo $this->Form->text('phone_number', array('id' => 'PersonalNumber', 'style' => 'width: 90%;')); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td style="width: 30%;"><label for="Email"><?php echo TABLE_EMAIL; ?> :</label></td>
            <td style="width: 70%;">
                <div class="inputContainer" style="width: 100%;">
                    <?php echo $this->Form->text('email', array('id' => 'Email', 'class' => 'validate[optional,custom[email]]', 'style' => 'width: 90%;')); ?>
                </div>
            </td>
        </tr>
    </table>
</fieldset>
<br />
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <span class="txtSaveDoctorConsultation"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<div style="clear: both;"></div>
<?php echo $this->Form->end();
?>