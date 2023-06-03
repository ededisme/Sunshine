<?php
echo $this->element('prevent_multiple_submit');
$absolute_url = FULL_BASE_URL . Router::url("/", false); 
?>
<?php echo $javascript->link('uninums.min'); ?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/jquery.form.js"></script>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/listbox.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(".chzn-select").chosen({width: 260});
        // Prevent Key Enter
        preventKeyEnter();
        $("#CompanyInsuranceAddForm").validationEngine();
        $("#CompanyInsuranceAddForm").ajaxForm({
            dataType: 'json',
            beforeSerialize: function($form, options) {                
                if($("#CompanyInsuranceCompanyId").val() == "" || $("#CompanyInsuranceCompanyId").val() == null){
                    alertSelectCompanyEmp();
                    return false;
                }
            },
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveCompany").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");                
                if(result.error == 2){
                    // alert message
                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result.msg+'</p>');
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
                    return false;
                }else{
                    $(".btnBackCompanyInsurance").click();
                    // alert message
                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result.msg+'</p>');
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
            }
        });
        $(".btnBackCompanyInsurance").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableCompanyInsurance.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });   
    
    function alertSelectCompanyEmp(){
        $("#dialog").html('<p style="color:red; font-size:14px;"><?php echo MESSAGE_SELECT_COMPANY; ?></p>');
        $("#dialog").dialog({
            title: '<?php echo DIALOG_INFORMATION; ?>',
            resizable: false,
            modal: true,
            closeOnEscape: false,
            width: 'auto',
            height: 'auto',
            position:'center',
            open: function(event, ui){
                $(".ui-dialog-buttonpane").show();
                $(".ui-dialog-titlebar-close").hide();
            },
            buttons: {
                '<?php echo ACTION_CLOSE; ?>': function() {
                    $(this).dialog("close");
                    $(".btnSaveCompanyInsurance").removeAttr('disabled');
                    $(".ui-dialog-titlebar-close").show();
                }
            }
        });
    }
</script>
<style type="text/css">
    .chosen-container-multi{
        width: 410px;
    }
    
</style>
<div style="padding: 5px;border: 1px dashed #3C69AD;">
    <div class="buttons">
        <a href="" class="positive btnBackCompanyInsurance">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('CompanyInsurance', array('inputDefaults' => array('div' => false, 'label' => false))); ?>
<fieldset>
    <legend><?php __(MENU_COMPANY_INSURANCE_MANAGEMENT_INFO); ?></legend>
    <table cellspacing="0" style="width: 50%;  border-spacing:0 5px;">     
        <tr>
            <td><label for="CompanyInsuranceCompanyId"><?php echo TABLE_COMPANY; ?> <span class="red">*</span> :</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->input('company_id', array('label' => false, 'multiple' => 'multiple', 'data-placeholder' => INPUT_SELECT, 'class' => 'chzn-select', 'style' => 'width: 260px;')); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><label for="CompanyInsuranceGroupInsuranceId"><?php echo MENU_COMPANY_INSURANCE_GROUP_MANAGEMENT; ?> <span class="red">*</span> :</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->input('group_insurance_id', array('label' => false, 'empty' => INPUT_SELECT, 'class' => 'validate[required]', 'style' => 'width: 307px;')); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><label for="CompanyInsuranceCompanyInsuranceCode"><?php echo TABLE_CODE; ?> <span class="red">*</span> :</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->text('insurance_code', array('class' => 'validate[required]', 'value'=>$code)); ?>
                </div>
            </td>
        </tr>        
        <tr>
            <td><label for="CompanyInsuranceName"><?php echo TABLE_NAME; ?> <span class="red">*</span> :</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->text('name', array('class' => 'validate[required]', 'id' => 'CompanyInsuranceNameAdd')); ?>
                </div>
            </td>
        </tr>       
        <tr>
            <td><label for="CompanyInsuranceBusinessNumberAdd"><?php echo TABLE_TELEPHONE_WORK; ?>:</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->text('business_number', array( 'id' => 'CompanyInsuranceBusinessNumberAdd')); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><label for="CompanyInsurancePersonalNumberAdd"><?php echo TABLE_TELEPHONE_PERSONAL; ?>:</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->text('personal_number', array('id' => 'CompanyInsurancePersonalNumberAdd')); ?>
                </div>
            </td>
        </tr> 
        <tr>
            <td><label for="CompanyInsuranceOtherNumberAdd"><?php echo TABLE_TELEPHONE_OTHER; ?>:</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->text('other_number', array('id' => 'CompanyInsuranceOtherNumberAdd')); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><label for="CompanyInsuranceFaxNumberAdd"><?php echo TABLE_FAX_NUMBER; ?>:</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->text('fax_number', array('id' => 'CompanyInsuranceFaxNumberAdd')); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><label for="CompanyInsuranceEmailAddressAdd"><?php echo TABLE_EMAIL; ?>:</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->text('email_address', array('id' => 'CompanyInsuranceEmailAddressAdd', 'class' => 'validate[optional,custom[email]]')); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top;"><label for="CompanyInsuranceAddressAdd"><?php echo TABLE_ADDRESS; ?>:</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->textarea('address', array('id' => 'CompanyInsuranceAddressAdd', 'style' => 'width:400px')); ?>
                </div>
            </td>
        </tr>
    </table>
</fieldset>
<div class="clear"></div>
<div class="buttons">
    <button type="submit" class="positive  btnSaveCompanyInsurance">
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <span class="txtSaveCompany"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<div style="clear: both;"></div>
<?php echo $this->Form->end(); ?>