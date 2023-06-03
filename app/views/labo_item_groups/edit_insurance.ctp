<?php 
echo $this->element('prevent_multiple_submit'); 
$absolute_url = FULL_BASE_URL . Router::url("/", false);
?>
<style type="text/css">
    #LaboItemGroupParentId optgroup option {
        padding-left: 30px;
    }
</style>
<script type="text/javascript">
    $(document).ready(function() {
        $(".float").autoNumeric({mDec: 2});
        // Prevent Key Enter
        preventKeyEnter();        
        $("#LaboItemGroupEditInsuranceForm").validationEngine();
        $("#LaboItemGroupEditInsuranceForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSavePatient").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");                                                
                $(".btnBackLaboTitleItemInsurance").dblclick();
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
        
        $(".btnBackLaboTitleItemInsurance").dblclick(function(event){
            event.preventDefault();
            $('#LaboItemGroupEditInsuranceForm').validationEngine('hideAll');
            oCache.iCacheLower = -1;
            oTableLaboSubGroupInsurance.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });    
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="#" class="positive btnBackLaboTitleItemInsurance">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('LaboItemGroup'); ?>
<?php echo $this->Form->input('id'); ?>
<input type="hidden" id="" name="data[LaboItemPriceInsurance][id]" value="<?php echo $servicesPriceInsurance['LaboItemPriceInsurance']['id'];?>"/>
<input type="hidden" id="" name="data[LaboItemPriceInsurancePatientGroupDetail][id]" value="<?php echo $servicesPriceInsurance['LaboItemPriceInsurancePatientGroupDetail']['id'];?>"/>
<fieldset>
    <legend><?php __(MENU_SUB_GROUP_INFO); ?></legend>
    <table>        
        <tr>
            <td><label for="LaboItemGroupName"><?php echo TABLE_NAME; ?> <span class="red">*</span> :</label></td>
            <td><?php echo $this->Form->text('name', array('class' => 'validate[required]', 'readonly' => true)); ?></td>
        </tr>
        <tr>
            <td><label for="ServicesPriceInsuranceCompanyInsuranceId"><?php echo TABLE_COMPANY_INSURANCE_NAME; ?> <span class="red">*</span> :</label></td>
            <td>                
                <?php echo $this->Form->input('company_insurance_id', array('label' => false, 'selected' => $servicesPriceInsurance['LaboItemPriceInsurance']['company_insurance_id'], 'class' => 'validate[required]')); ?>
            </td>
        </tr>
        <tr>
            <td><label for="ServicesPriceInsurancePatientGroupId"><?php echo PATIENT_TYPE; ?> <span class="red">*</span> :</label></td>
            <td>
                <?php echo $this->Form->input('patient_group_id', array('empty' => SELECT_OPTION, 'selected' => $servicesPriceInsurance['LaboItemPriceInsurancePatientGroupDetail']['patient_group_id'], 'label' => false,'class' => 'validate[required]')); ?>                                
            </td>
        </tr>
        <tr>
            <td><label for="ServicesPriceInsuranceUnitPrice"><?php echo GENERAL_UNIT_PRICE; ?> <span class="red">*</span> :</label></td>
            <td><?php echo $this->Form->text('unit_price', array('class' => 'unit_price float validate[required]', 'value' => $servicesPriceInsurance['LaboItemPriceInsurancePatientGroupDetail']['unit_price'])); ?> </td>
        </tr>
    </table>
</fieldset>
<br/>
<div class="buttons">
    <button type="submit" class="positive savePatient" >
        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
        <span class="txtSavePatient"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<?php echo $this->Form->end(); ?>