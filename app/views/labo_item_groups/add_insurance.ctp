<?php 
echo $this->element('prevent_multiple_submit'); 
$absolute_url = FULL_BASE_URL . Router::url("/", false);
?>
<?php $tblName = "tbl" . rand(); ?>
<script type="text/javascript">    
    $(document).ready(function() { 
        $(".companyInsurance").chosen({width: 260});
        $(".float").autoNumeric({mDec: 2});
        // Prevent Key Enter
        preventKeyEnter();        
        $("#LaboItemGroupAddInsuranceForm").validationEngine();
        $("#LaboItemGroupAddInsuranceForm").ajaxForm({
            beforeSerialize: function($form, options) { 
                if($("#LaboItemGroupCompanyInsuranceId").val() == "" || $("#LaboItemGroupCompanyInsuranceId").val() == null){
                    alertSelectCompanyEmp();
                    return false;
                }               
            },
            beforeSubmit: function(arr, $form, options) {
                $(".txtSavePatient").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");                                                
                $(".btnBackLaboTitleItemInsurance").click();
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
        $(".btnBackLaboTitleItemInsurance").click(function(event){
            event.preventDefault();
            var rightPanel = $(this).parent().parent().parent();
            var leftPanel  = rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
            oCache.iCacheLower = -1;
            oTableLaboSubGroupInsurance.fnDraw(false);
        });
        
        
        var countPatientGroup = $("#countPatientGroup").val();        
        $("#LaboItemGroupEditForm").validationEngine();
        $("#LaboItemGroupPrice").autoNumeric();
        $(".btnAddType").click(function() {             
            if(Number($("#<?php echo $tblName;?>").find(".serviceId:last").text())<countPatientGroup){
                $("#<?php echo $tblName;?>").find(".LaboItemGroupTr:last").clone(true).appendTo("#<?php echo $tblName;?>");
                $("#<?php echo $tblName;?>").find(".LaboItemGroupTr:last").find("td .btnRemoveType").show();
                $(this).siblings(".btnRemoveType").show();
                $(this).hide(); 
                comboRefeshType();
                staffRefreshType()
            }
           
        });
        $(".btnRemoveType").click(function() {
            $(this).closest(".LaboItemGroupTr").remove();
            $("#<?php echo $tblName;?>").find(".LaboItemGroupTr:last").find("td .btnAddType").show();            
            if ($('#<?php echo $tblName;?> .LaboItemGroupTr').length == 1) {
                $("#<?php echo $tblName;?>").find(".LaboItemGroupTr:last").find("td .btnRemoveType").hide();
            }
            staffRefreshType()
        });
    });
    function staffRefreshType() {
        var i = 1;
        $(".serviceId").each(function() {
            $("#<?php echo $tblName;?>").find(".serviceId:last").text(i++);
        });
    }
     function comboRefeshType() {                 
        $(".unit_price").each(function() {
            $("#<?php echo $tblName;?>").find(".unit_price:last").val("");
        });       
        $(".laboItemGroupPatientGroup").each(function() {            
            $("#<?php echo $tblName;?>").find(".servicePatientGroup:last").val("");
        }); 
    }
       
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
                    $(".savePatient").removeAttr('disabled');
                    $(".ui-dialog-titlebar-close").show();
                }
            }
        });
    }
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
<input type="hidden" id="countPatientGroup" value="<?php echo count($patientGroups);?>"/>
<fieldset>
    <legend><?php __(MENU_SUB_GROUP_INFO); ?></legend>
    <table>
        <tr>
            <td><label for="LaboItemGroupName"><?php echo MENU_SUB_GROUP; ?> <span class="red">*</span> :</label></td>
            <td><?php echo $this->Form->input('labo_item_group_id', array('label' => false, 'class' => 'validate[required]', 'empty' => SELECT_OPTION)); ?></td>
        </tr>
        <tr>
            <td><label for="ServicesPriceInsuranceCompanyInsuranceId"><?php echo TABLE_COMPANY_INSURANCE_NAME; ?> <span class="red">*</span> :</label></td>
            <td>                
                <?php echo $this->Form->input('company_insurance_id', array('label' => false, 'class' => 'companyInsurance validate[required]', 'style' => 'width:300px', 'multiple' => true)); ?>
            </td>
        </tr>
    </table>
</fieldset>
<br/>
<div class="clear"></div>
<fieldset>
    <legend><?php __(MENU_LABO_SUB_GROUP_PRICE); ?></legend>    
    <table id="<?php echo $tblName;?>" class="table" cellspacing="0">
        <tr>
            <th style="width: 5%;" class="first"><?php echo TABLE_NO; ?></th>
            <th><?php echo TABLE_PATIENT_GROUP; ?></th>
            <th><?php echo GENERAL_UNIT_PRICE; ?></th>
            <th style="width: 10% !important; ">&nbsp;</th>
        </tr>
        <tr class="LaboItemGroupTr">
            <td class="first serviceId">1</td>            
            <td>
                <?php echo $this->Form->input('patient_group_id', array('name' => 'data[LaboItemGroup][patient_group_id][]', 'empty' => SELECT_OPTION, 'label' => false,'class' => 'laboItemGroupPatientGroup validate[required]')); ?>
            </td>
            <td>
                <?php echo $this->Form->text('unit_price', array('name' => 'data[LaboItemGroup][unit_price][]', 'class' => 'unit_price float validate[required]')); ?> 
            </td>
            <td>
                <img alt="" src="<?php echo $this->webroot; ?>img/button/plus.png" class="btnAddType" style="cursor: pointer;" />
                <img alt="" src="<?php echo $this->webroot; ?>img/button/cross.png" class="btnRemoveType" style="cursor: pointer;display: none;" />
            </td>
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