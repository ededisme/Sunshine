<?php 
// Authentication
$this->element('check_access');
$allowAddSection = checkAccess($user['User']['id'], $this->params['controller'], 'addSection');

echo $this->element('prevent_multiple_submit'); ?>
<script type="text/javascript">
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        <?php
        if($allowAddSection){
        ?>
        $("#ServiceSectionId").chosen({ width: 410, allow_add: true, allow_add_label: '<?php echo MENU_PRODUCT_GROUP_MANAGEMENT_ADD; ?>', allow_add_id: 'addNewSectionService' });
        $("#addNewSectionService").click(function(){
            $.ajax({
                type:   "GET",
                url:    "<?php echo $this->base . "/services/addSection/"; ?>",
                beforeSend: function(){
                    $("#ServiceSectionId").trigger("chosen:close");
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(msg){
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $("#dialog").html(msg);
                    $("#dialog").dialog({
                        title: '<?php echo MENU_PRODUCT_GROUP_MANAGEMENT_ADD; ?>',
                        resizable: false,
                        modal: true,
                        width: '450',
                        height: '300',
                        position:'center',
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show();
                        },
                        buttons: {
                            '<?php echo ACTION_CLOSE; ?>': function() {
                                $(this).dialog("close");
                            },
                            '<?php echo ACTION_SAVE; ?>': function() {
                                var formName = "#SectionAddSectionForm";
                                var validateBack =$(formName).validationEngine("validate");
                                if(!validateBack){
                                    return false;
                                }else{
                                    $(this).dialog("close");
                                    $.ajax({
                                        dataType: "json",
                                        type: "POST",
                                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/addSection",
                                        data: $("#SectionAddSectionForm").serialize(),
                                        beforeSend: function(){
                                            $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                                        },
                                        error: function (result) {
                                            $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                            createSysAct('Service', 'Quick Add Section', 2, result);
                                            $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                                            $("#dialog").dialog({
                                                title: '<?php echo DIALOG_INFORMATION; ?>',
                                                resizable: false,
                                                modal: true,
                                                width: 'auto',
                                                height: 'auto',
                                                position:'center',
                                                closeOnEscape: true,
                                                open: function(event, ui){
                                                    $(".ui-dialog-buttonpane").show(); $(".ui-dialog-titlebar-close").show();
                                                },
                                                buttons: {
                                                    '<?php echo ACTION_CLOSE; ?>': function() {
                                                        $(this).dialog("close");
                                                    }
                                                }
                                            });
                                        },
                                        success: function(result){
                                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                                            createSysAct('Service', 'Quick Add Section', 1, '');
                                            var msg = '<?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?>';
                                            if(result.error == 0){
                                                // Update Pgroup
                                                $("#ServiceSectionId").html(result.option);
                                                $("#ServiceSectionId").trigger("chosen:updated");
                                                msg = '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>';
                                            } else if(result.error == 2){
                                                msg = '<?php echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM; ?>';
                                            }
                                            // Message Alert
                                            $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+msg+'</p>');
                                            $("#dialog").dialog({
                                                title: '<?php echo DIALOG_INFORMATION; ?>',
                                                resizable: false,
                                                modal: true,
                                                width: 'auto',
                                                height: 'auto',
                                                position: 'center',
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
                                }  
                            }
                        }
                    });
                }
            });
        });
        <?php
        } else {
        ?>
        $("#ServiceSectionId").chosen({width: 200});
        <?php
        }
        ?>
        $("#ServiceUomId").chosen({width: 200});
        $("#ServiceEditForm").validationEngine('attach', {
            isOverflown: true,
            overflownDIV: ".ui-tabs-panel"
        });
        $("#ServiceEditForm").ajaxForm({
            beforeSerialize: function($form, options) {
                <?php 
                if(count($branches) > 1){
                ?>
                listbox_selectall('serviceBranchSelected', true);
                if($("#serviceBranchSelected").val() == null){
                    alertSelectServiceProduct();
                    return false;
                }
                <?php
                }
                ?>
                $(".float").each(function(){
                    $(this).val($(this).val().replace(/,/g,""));
                });
            },
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveService").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $(".btnBackService").click();
                // alert message
                if(result != '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>' && result != '<?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?>' && result != '<?php echo MESSAGE_CODE_ALREADY_EXISTS_IN_THE_SYSTEM; ?>'){
                    createSysAct('Service', 'Edit', 2, result);
                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                }else {
                    createSysAct('Service', 'Edit', 1, '');
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
        $(".btnBackService").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableService.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
        $(".float").autoNumeric();
        
        
        var countPatientGroup = $("#countPatientGroup").val(); 
        // add more patient's group for services
        $(".btnAddType").click(function() {
            
            if(Number($("#example").find(".serviceId:last").text())<countPatientGroup){
                var id = Number($("#example").find(".serviceId:last").text())+1;            
                $("#example").find(".servicePatientGroup:last").attr('id', 'ServicePatientGroupId'+id);
                $("#example").find(".unit_price:last").attr('id', 'ServiceUnitPrice'+id);
                
                $("#example").find(".ServiceTr:last").clone(true).appendTo("#example");
                $("#example").find(".ServiceTr:last").find("td .btnRemoveType").show();
                $(this).siblings(".btnRemoveType").show();
                $(this).hide(); 
                comboRefeshType();
                staffRefreshType()
            }
           
        });
        $(".btnRemoveType").click(function() {
            $(this).closest(".ServiceTr").remove();
            $("#example").find(".ServiceTr:last").find("td .btnAddType").show();            
            if ($('#example .ServiceTr').length == 1) {
                $("#example").find(".ServiceTr:last").find("td .btnRemoveType").hide();
            }
            staffRefreshType()
        });
        // close add more patient's group for services
        
        
    });
    <?php 
    if(count($companies) == 1 && COUNT($branches) > 1){
    ?>
    function alertSelectServiceProduct(){
        $(".btnSavePro").removeAttr('disabled');
        $("#dialog").html('<p style="color:red; font-size:14px;"><?php echo MESSAGE_CONFIRM_SELECT_BRANCH; ?></p>');
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
                    $(".ui-dialog-titlebar-close").show();
                }
            }
        });
    }
    <?php
    }
    ?>
        
    function staffRefreshType() {
        var i = 1;
        $(".serviceId").each(function() {
            $("#example").find(".serviceId:last").text(i++);
        });
    }
     function comboRefeshType() {
        $(".unit_price").each(function() {
            $("#example").find(".unit_price:last").val("");
        }); 
        $(".servicePatientGroup").each(function() {
            $("#example").find(".servicePatientGroup:last").val("");
        }); 
    }
    
</script>
<div style="padding: 5px;border: 1px dashed #3C69AD;;">
    <div class="buttons">
        <a href="" class="positive btnBackService">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php 
echo $this->Form->create('Service', array('inputDefaults' => array('div' => false, 'label' => false))); 
echo $this->Form->input('id'); 
echo $this->Form->hidden('sys_code');
if(count($companies) == 1){
    $companyId = key($companies);
?>
<input type="hidden" value="<?php echo $companyId; ?>" name="data[Service][company_id]" id="ServiceCompanyId" />
<?php
}
if(count($branches) == 1){
    $branchId = key($branches);
?>
<input type="hidden" value="<?php echo $branchId; ?>" name="data[Service][branch_id][]" />
<?php
}
?>
<fieldset>
    <legend><?php __(MENU_EDIT_SERVICE); ?></legend>
    <table>
        <tr>
            <td><label for="ServiceCode"><?php echo TABLE_CODE; ?> <span class="red">*</span> :</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->text('code', array()); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><label for="ServiceName"><?php echo TABLE_NAME; ?> <span class="red">*</span> :</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->text('name', array('class'=>'validate[required]')); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td style="width: 20%;"><label for="ServiceSectionId"><?php echo TABLE_GROUP; ?> <span class="red">*</span> :</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->input('section_id', array('empty' => INPUT_SELECT, 'class'=>'validate[required]')); ?>
                </div>
            </td>
        </tr>
        <!--
        <tr>
            <td><label for="ServiceUomId"><?php echo TABLE_UOM; ?> :</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->input('uom_id', array('empty' => INPUT_SELECT)); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><label for="ServiceUnitPrice"><?php echo SALES_ORDER_UNIT_PRICE; ?> <span class="red">*</span> :</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->text('unit_price', array('class' => 'float validate[required]')); ?> ($)
                </div>
            </td>
        </tr>
        -->
        <tr>
            <td><label for="ServiceIsDefault"><?php echo TABLE_USE_DEFAULT; ?>:</label></td>
            <td>
                <div class="inputContainer">
                    <select name="data[Service][is_default]" id="ServiceIsDefault">
                        <option <?php if($this->data['Service']['is_default'] == 0){ ?>selected="selected"<?php } ?> value="0"><?php echo ACTION_NO; ?></option>
                        <option <?php if($this->data['Service']['is_default'] == 1){ ?>selected="selected"<?php } ?> value="1"><?php echo ACTION_YES; ?></option>
                    </select>
                </div>
            </td>
        </tr>
        <tr style="display: none;">
            <td><label for="ServiceChartAccountId"><?php echo TABLE_ACCOUNT; ?> <span class="red">*</span> :</label></td>
            <td>
                <div class="inputContainer">
                    <select id="ServiceChartAccountId" name="data[Service][chart_account_id]" class="validate[required]">
                        <option value=""><?php echo INPUT_SELECT; ?></option>
                        <?php
                        $query[0]=mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE is_active=1 ORDER BY account_codes");
                        while($data[0]=mysql_fetch_array($query[0])){
                        ?>
                        <option value="<?php echo $data[0]['id']; ?>" chart_account_type_name="<?php echo $data[0]['chart_account_type_name']; ?>" company_id="<?php echo $data[0]['company_id']; ?>" <?php echo $data[0]['id']==$this->data['Service']['chart_account_id']?'selected="selected"':''; ?>><?php echo $data[0]['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top;"><label for="ServiceDescription"><?php echo GENERAL_DESCRIPTION; ?>:</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->textarea('description'); ?>
                </div>
            </td>
        </tr>
    </table>
</fieldset>
<?php 
if(count($branches) > 1){
?>
<fieldset style="width: 48%; float: right;">
    <legend><?php __(TABLE_APPLY_WITH_BRANCH); ?></legend>
    <table>
        <tr>
            <th><label for="serviceBranch">Available:</label></th>
            <th></th>
            <th><label for="serviceBranchSelected">Member of:</label></th>
        </tr>
        <tr>
            <td style="vertical-align: top;">
                <select id="serviceBranch" multiple="multiple" style="width: 280px; height: 200px;">
                    <?php
                    $querySource=mysql_query("SELECT id,name FROM branches WHERE is_active=1 AND id NOT IN (SELECT branch_id FROM service_branches WHERE service_id = ".$this->data['Service']['id'].") AND company_id =".$this->data['Service']['company_id']);
                    while($dataSource=mysql_fetch_array($querySource)){
                    ?>
                    <option value="<?php echo $dataSource['id']; ?>"><?php echo $dataSource['name']; ?></option>
                    <?php } ?>
                </select>
            </td>
            <td style="vertical-align: middle;">
                <img alt="" src="<?php echo $this->webroot; ?>img/button/right.png" style="cursor: pointer;" onclick="listbox_moveacross('serviceBranch', 'serviceBranchSelected')" />
                <br /><br />
                <img alt="" src="<?php echo $this->webroot; ?>img/button/left.png" style="cursor: pointer;" onclick="listbox_moveacross('serviceBranchSelected', 'serviceBranch')" />
            </td>
            <td style="vertical-align: top;">
                <select id="serviceBranchSelected" name="data[Service][branch_id][]" multiple="multiple" style="width: 280px; height: 200px;">
                    <?php
                    $queryBranch=mysql_query("SELECT DISTINCT branch_id,(SELECT name FROM branches WHERE id=service_branches.branch_id) AS name FROM service_branches WHERE branch_id NOT IN (SELECT id FROM branches WHERE is_active!=1) AND service_id=".$this->data['Service']['id']);
                    while($dataBranch=mysql_fetch_array($queryBranch)){
                    ?>
                    <option value="<?php echo $dataBranch['branch_id']; ?>"><?php echo $dataBranch['name']; ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
    </table>
</fieldset>
<?php
}
?>
<br>
<fieldset>
    <legend><?php __(MENU_SERVICES_PRICE); ?></legend>    
    <table id="example" class="table" cellspacing="0">
        <tr>
            <th style="width: 5%;" class="first"><?php echo TABLE_NO; ?></th>
            <th><?php echo TABLE_PATIENT_GROUP; ?></th>
            <th><?php echo GENERAL_UNIT_PRICE; ?></th>
            <th style="width: 10% !important; ">&nbsp;</th>
        </tr>
        <?php
        $countData = "";
        $index = 1;
        $unitPrice = 0;
        $patientGroupId = "";    
        $query = mysql_query("SELECT patient_group_id,unit_price FROM services_patient_group_details
                                INNER JOIN patient_groups ON patient_groups.id = services_patient_group_details.patient_group_id
                                WHERE services_patient_group_details.is_active = 1 AND services_patient_group_details.service_id=".$this->data['Service']['id']);        
        $countData = mysql_numrows($query);
        while ($row = mysql_fetch_row($query)) {
            $patientGroupId = $row[0];
            $unitPrice = $row[1];
            if($index==1){ 
            ?>
                <tr class="ServiceTr">
                    <td class="first serviceId"><?php echo $index;?></td>            
                    <td>
                        <select id="ServicePatientGroupId<?php echo $index;?>" class="servicePatientGroup validate[required]" name="data[Service][patient_group_id][]">
                            <option value=""><?php echo SELECT_OPTION;?></option>                    
                            <?php foreach($patientGroups as $patientGroup) {?>
                                <option <?php if($patientGroup['PatientGroup']['id']==$patientGroupId) {echo 'selected="selected"';}?> value="<?php echo $patientGroup['PatientGroup']['id']?>"><?php echo $patientGroup['PatientGroup']['name'];?></option>
                            <?php }?>
                        </select>                
                    </td>
                    <td>
                        <?php echo $this->Form->text('unit_price', array('id' => 'ServiceUnitPrice'.$index, 'name' => 'data[Service][price][]', 'class' => 'unit_price float validate[required]', 'style' => 'width:200px;', 'value' => $unitPrice)); ?> 
                    </td>
                    <td>
                        <img alt="" src="<?php echo $this->webroot; ?>img/button/plus.png" class="btnAddType" style="cursor: pointer;" />
                        <img alt="" src="<?php echo $this->webroot; ?>img/button/cross.png" class="btnRemoveType" style="cursor: pointer;display: none;" />
                    </td>
                </tr>
            <?php }else { ?>
                <tr class="ServiceTr">
                    <td class="first serviceId"><?php echo $index;?></td>            
                    <td>
                        <select id="ServicePatientGroupId<?php echo $index;?>" class="servicePatientGroup validate[required]" name="data[Service][patient_group_id][]">
                            <option value=""><?php echo SELECT_OPTION;?></option>                    
                            <?php foreach($patientGroups as $patientGroup) {?>
                                <option <?php if($patientGroup['PatientGroup']['id']==$patientGroupId) {echo 'selected="selected"';}?> value="<?php echo $patientGroup['PatientGroup']['id']?>"><?php echo $patientGroup['PatientGroup']['name'];?></option>
                            <?php }?>
                        </select>                
                    </td>
                    <td>
                        <?php echo $this->Form->text('unit_price', array('id' => 'ServiceUnitPrice'.$index, 'name' => 'data[Service][price][]', 'class' => 'unit_price float validate[required]', 'style' => 'width:200px;', 'value' => $unitPrice)); ?> 
                    </td>
                    <td>
                        <img alt="" src="<?php echo $this->webroot; ?>img/button/plus.png" class="btnAddType" style="cursor: pointer;" />
                        <img alt="" src="<?php echo $this->webroot; ?>img/button/cross.png" class="btnRemoveType" style="cursor: pointer;" />
                    </td>
                </tr>
                
            <?php } $index++?>
        <?php }?>
        <input type="hidden" id="countPatientGroup" value="<?php echo $countData;?>">
    </table>
</fieldset>

<div style="clear: both;"></div>
<br />
<div style="padding: 5px;border: 1px dashed #3C69AD;">
    <div class="buttons">
        <button type="submit" class="positive">
            <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
            <span class="txtSaveService"><?php echo ACTION_SAVE; ?></span>
        </button>
    </div>
    <div style="clear: both;"></div>
</div>
<div style="clear: both;"></div>
<?php echo $this->Form->end(); ?>