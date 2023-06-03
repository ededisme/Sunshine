<?php 
include("includes/function.php");
$tblName = "tbl" . rand();
$queryClosingDate = mysql_query("SELECT DATE_FORMAT(date,'%d/%m/%Y') FROM account_closing_dates ORDER BY id DESC LIMIT 1");
$dataClosingDate  = mysql_fetch_array($queryClosingDate);
?>
<script type="text/javascript">
    var fieldRequireInvAdj = ['InvAdjLocationGroupId'];
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        // Hide Branch
        $("#InvAdjBranchId").filterOptions('com', '<?php echo $this->data['CycleProduct']['company_id']; ?>', '<?php echo $this->data['CycleProduct']['branch_id']; ?>');
        <?php if(count($locationGroups) > 1){ ?>
        $("#InvAdjLocationGroupId").chosen({ width: 280});
        <?php } ?>
        // Form Validate
        $("#InvAdjForm").validationEngine('detach');
        $("#InvAdjForm").validationEngine('attach');
        
        // Date Datepicker
        $("#InvAdjDate").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd/mm/yy',
            minDate: '<?php echo $dataClosingDate[0]; ?>',
            maxDate: 0,
            onSelect: function(dateText, inst) {
                $("#InvAdjForm").validationEngine("hideAll");
                var obj       = $(this);
                var productId = $("#tblInvAdj").find(".product_name").val();
                if(productId == undefined){
                    setCookie('InvAdjDate', obj.val());
                }else{
                    var question = "<?php echo MESSAGE_CONFRIM_CHANGE_DATE; ?>";
                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+question+'</p>');
                    $("#dialog").dialog({
                        title: '<?php echo DIALOG_INFORMATION; ?>',
                        resizable: false,
                        modal: true,
                        width: 'auto',
                        height: 'auto',
                        position:'center',
                        closeOnEscape: true,
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show(); 
                            $(".ui-dialog-titlebar-close").show();
                        },
                        buttons: {
                            '<?php echo ACTION_OK; ?>': function() {
                                setCookie('InvAdjDate', obj.val());
                                // Call Detail Inventory Adjustment
                                loadDetailInventoryAdj(0);
                                $(this).dialog("close");
                            },
                            '<?php echo ACTION_CANCEL; ?>': function() {
                                useCookie("#InvAdjDate", "InvAdjDate");
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            }
        });
        
        // Action Button Back
        $(".btnBackInvAdj").click(function(event){
            event.preventDefault();
            $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_DO_YOU_WANT_TO_BACK; ?></p>');
            $("#dialog").dialog({
                title: '<?php echo DIALOG_CONFIRMATION; ?>',
                resizable: false,
                modal: true,
                width: 'auto',
                height: 'auto',
                open: function(event, ui){
                    $(".ui-dialog-buttonpane").show();
                },
                buttons: {
                    '<?php echo ACTION_NO; ?>': function() {
                        $(this).dialog("close");
                    },
                    '<?php echo ACTION_YES; ?>': function() {
                        $(this).dialog("close");
                        backInvAdj();
                    }
                }
            });
        });
        
        // Action Change Location Group
        $.cookie('InvAdjLocationGroupId', $("#InvAdjLocationGroupId").val(), { expires: 7, path: "/" });
        $("#InvAdjLocationGroupId").change(function(){
            var obj = $(this);
            var productId   = $("#tblInvAdj").find(".product_name").val();
            if(productId == undefined){
                setCookie('InvAdjLocationGroupId', obj.val());
            }else{
                var question = "<?php echo MESSAGE_CONFRIM_CHANGE_LOCATION_GROUP; ?>";
                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+question+'</p>');
                $("#dialog").dialog({
                    title: '<?php echo DIALOG_CONFIRMATION; ?>',
                    resizable: false,
                    modal: true,
                    width: 'auto',
                    height: 'auto',
                    position:'center',
                    closeOnEscape: true,
                    open: function(event, ui){
                        $(".ui-dialog-buttonpane").show(); 
                        $(".ui-dialog-titlebar-close").show();
                    },
                    buttons: {
                        '<?php echo ACTION_OK; ?>': function() {
                            setCookie('InvAdjLocationGroupId', obj.val());
                            // Call Detail Inventory Adjustment
                            loadDetailInventoryAdj(0);
                            $(this).dialog("close");
                        },
                        '<?php echo ACTION_CANCEL; ?>': function() {
                            useCookie("#InvAdjLocationGroupId", "InvAdjLocationGroupId");
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
        
        // Company Action
        $.cookie('companyIdInvAdj', $("#InvAdjCompanyId").val(), { expires: 7, path: "/" });
        $("#InvAdjCompanyId").change(function(){
            var obj   = $(this);
            if($(".tblInvAdjList").find(".product_id").val() == undefined){
                $.cookie('companyIdInvAdj', obj.val(), { expires: 7, path: "/" });
                $("#InvAdjBranchId").filterOptions('com', obj.val(), '');
                $("#InvAdjBranchId").change();
                changeInputCSSInvAdj();
            }else{
                var question = "<?php echo SALES_ORDER_CONFIRM_CHANGE_COMPANY; ?>";
                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+question+'</p>');
                $("#dialog").dialog({
                    title: '<?php echo DIALOG_CONFIRMATION; ?>',
                    resizable: false,
                    modal: true,
                    width: 'auto',
                    height: 'auto',
                    position:'center',
                    closeOnEscape: true,
                    open: function(event, ui){
                        $(".ui-dialog-buttonpane").show(); 
                        $(".ui-dialog-titlebar-close").show();
                    },
                    buttons: {
                        '<?php echo ACTION_OK; ?>': function() {
                            $.cookie('companyIdInvAdj', obj.val(), { expires: 7, path: "/" });
                            $("#InvAdjBranchId").filterOptions('com', obj.val(), '');
                            $("#InvAdjBranchId").change();
                            changeInputCSSInvAdj();
                            loadDetailInventoryAdj(0);
                            $(this).dialog("close");
                        },
                        '<?php echo ACTION_CANCEL; ?>': function() {
                            $("#InvAdjCompanyId").val($.cookie("companyIdInvAdj"));
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
        // Action Branch
        $.cookie('branchIdInvAdj', $("#InvAdjBranchId").val(), { expires: 7, path: "/" });
        $("#InvAdjBranchId").change(function(){
            var obj = $(this);
            if($(".tblInvAdjList").find(".product_id").val() == undefined){
                $.cookie('branchIdInvAdj', obj.val(), { expires: 7, path: "/" });
                branchChangeInvAdj(obj);
            } else {
                var question = "<?php echo MESSAGE_CONFIRM_CHANGE_BRANCH; ?>";
                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+question+'</p>');
                $("#dialog").dialog({
                    title: '<?php echo DIALOG_CONFIRMATION; ?>',
                    resizable: false,
                    modal: true,
                    width: 'auto',
                    height: 'auto',
                    position:'center',
                    closeOnEscape: true,
                    open: function(event, ui){
                        $(".ui-dialog-buttonpane").show(); 
                        $(".ui-dialog-titlebar-close").show();
                    },
                    buttons: {
                        '<?php echo ACTION_OK; ?>': function() {
                            $.cookie('branchIdInvAdj', obj.val(), { expires: 7, path: "/" });
                            branchChangeInvAdj(obj);
                            loadDetailInventoryAdj(0);
                            $(this).dialog("close");
                        },
                        '<?php echo ACTION_CANCEL; ?>': function() {
                            $("#InvAdjBranchId").val($.cookie("branchIdInvAdj"));
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
        changeInputCSSInvAdj();
        // Call Detail Inventory Adjustment
        loadDetailInventoryAdj(<?php echo $id; ?>);
        loadAutoCompleteOff();
    });
    
    function branchChangeInvAdj(obj){
        var mCode  = obj.find("option:selected").attr("mcode");
        $("#InvAdjReference").val("<?php echo date("y"); ?>"+mCode);
    }
    
    // Detail Inventory Adjustment
    function loadDetailInventoryAdj(invAdjId){
        $.ajax({
            type: "POST",
            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/editDetail/"+invAdjId+"/<?php echo $this->data['CycleProduct']['location_group_id']; ?>",
            beforeSend: function(){
                $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                $(".order-detail-inventory-adjustment").html('<center><img alt="loading..." src="<?php echo $this->webroot . 'img/ajax-loader.gif'; ?>" /></center>');
            },
            success: function(msg){
                $(".order-detail-inventory-adjustment").html(msg);
                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
            }
        });
    }
    
    // Check Record Before Save
    function checkExistBeforeSaveInvAdj(){
        var formName = "#InvAdjForm";
        var validateBack =$(formName).validationEngine("validate");
        if(!validateBack){
            return false;
        }else{
            var productId   = $("#tblInvAdj").find(".product_name").val();
            if(productId == undefined){
                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>Please adjust product first.</p>');
                $("#dialog").dialog({
                    title: '<?php echo DIALOG_INFORMATION; ?>',
                    resizable: false,
                    modal: true,
                    width: 'auto',
                    height: 'auto',
                    position:'center',
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
            } else{
                return true;
            }
        }
    }
    
    function backInvAdj(){
        $("#InvAdjForm").validationEngine("hideAll");
        var rightPanel = $(".btnBackInvAdj").parent().parent().parent().parent().parent();
        var leftPanel  = rightPanel.parent().find(".leftPanel");
        rightPanel.hide();rightPanel.html("");
        leftPanel.show("slide", { direction: "left" }, 500);
        oCache.iCacheLower = -1;
        oTableInvAdj.fnDraw(false);
    }
    
    function changeInputCSSInvAdj(){
        var cssStyle  = 'inputDisable';
        var cssRemove = 'inputEnable';
        var disabled  = true;
        $(".searchProductInvAdj").hide();
        if($("#InvAdjCompanyId").val() != ''){
            cssStyle  = 'inputEnable';
            cssRemove = 'inputDisable';
            disabled  = false;
            $(".searchProductInvAdj").show();
        }   
        // Label
        $("#InvAdjForm").find("label").removeAttr("class");
        $("#InvAdjForm").find("label").each(function(){
            var label = $(this).attr("for");
            if(label != 'InvAdjCompanyId'){
                $(this).addClass(cssStyle);
            }
        });
        // Input & Select
        $("#InvAdjForm").find("input").each(function(){
            $(this).removeClass(cssRemove);
            $(this).addClass(cssStyle);
        });
        $("#InvAdjForm").find("select").each(function(){
            var selectId = $(this).attr("id");
            if(selectId != 'InvAdjCompanyId'){
                $(this).removeClass(cssRemove);
                $(this).addClass(cssStyle);
                $(this).attr("disabled", disabled);
            }
        });
    }
</script>
<?php echo $this->Form->create('InvAdj', array('id' => 'InvAdjForm', 'url' => '/inv_adjs/saveEdit/')); ?>
<input type="hidden" value="<?php echo $id; ?>" name="data[InvAdj][id]" />
<div style="float: right; width: 165px; text-align: right; cursor: pointer;" id="btnHideShowHeaderInvAdj">
    [<span>Hide</span> Header Information <img alt="" align="absmiddle" style="width: 16px; height: 16px;" src="<?php echo $this->webroot . 'img/button/arrow-up.png'; ?>" />]
</div>
<div style="clear: both;"></div>
<fieldset id="topInvAdj">
    <legend><?php echo MENU_INVENTORY_ADJUSTMENT; ?></legend>
    <table cellpadding="3" cellspacing="0" style="width: 100%;">
        <tr>
            <td style="width: 7%; vertical-align: top;"><label for="InvAdjDate"><?php echo TABLE_DATE; ?></label> <span class="red">*</span> :</td>
            <td style="width: 17%; vertical-align: top;">
                <div class="inputContainer" style="width:100%">
                    <?php echo $this->Form->input('date', array('value' => dateShort($this->data['CycleProduct']['date']), 'empty' => INPUT_SELECT, 'label' => false, 'class' => 'validate[required]', 'style' => 'width:70%')); ?>
                </div>
            </td>
            <td rowspan="2" style="width: 5%; vertical-align: top;"><label for="InvAdjNote"><?php echo TABLE_MEMO; ?> :</label></td>
            <td rowspan="2">
                <div class="inputContainer" style="width:100%">
                    <textarea style="width: 90%; height: 70px;" id="InvAdjNote" name="data[InvAdj][note]"><?php echo $this->data['CycleProduct']['note']; ?></textarea>
                </div>
            </td>
            <td style="width: 7%; vertical-align: top;"><?php if(count($companies) > 1){ ?><label for="InvAdjCompanyId"><?php echo TABLE_COMPANY; ?></label> <span class="red">*</span> :<?php } ?></td>
            <td style="width: 15%; vertical-align: top;">
                <div class="inputContainer" style="width:100%; <?php if(count($companies) == 1){ ?>display: none;<?php } ?>">
                    <select name="data[InvAdj][company_id]" id="InvAdjCompanyId" class="validate[required]" style="width: 75%;">
                        <?php
                        if(count($companies) != 1){
                        ?>
                        <option value=""><?php echo INPUT_SELECT; ?></option>
                        <?php
                        }
                        foreach($companies AS $company){
                        ?>
                        <option <?php if($this->data['CycleProduct']['company_id'] == $company['Company']['id']){ ?>selected="selected"<?php } ?> value="<?php echo $company['Company']['id']; ?>"><?php echo $company['Company']['name']; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </td>
            <td style="width: 7%; vertical-align: top;"><?php if(count($locationGroups) > 1){ ?><label for="InvAdjLocationGroupId"><?php echo TABLE_LOCATION_GROUP; ?></label> <span class="red">*</span> :<?php } ?></td>
            <td style="width: 25%; vertical-align: top;">
                <div class="inputContainer" style="width:100%; <?php if(count($locationGroups) == 1){ ?>display: none;<?php } ?>">
                    <?php echo $this->Form->input('location_group_id', array('selected' => $this->data['CycleProduct']['location_group_id'], 'empty' => INPUT_SELECT, 'label' => false, 'class' => 'validate[required]', 'style' => 'width:280px')); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><label for="InvAdjReference"><?php echo TABLE_ADJ_NO; ?></label> <span class="red">*</span> :</td>
            <td>
                <div class="inputContainer" style="width:100%">
                    <?php echo $this->Form->input('reference', array('value' => $this->data['CycleProduct']['reference'], 'empty' => INPUT_SELECT, 'label' => false, 'class' => 'validate[required]', 'style' => 'width:70%', 'readonly' => true)); ?>
                </div>
            </td>
            <td><?php if(count($branches) > 1){ ?><label for="InvAdjBranchId"><?php echo MENU_BRANCH; ?></label> <span class="red">*</span> :<?php } ?></td>
            <td>
                <div class="inputContainer" style="width:100%; <?php if(count($branches) == 1){ ?>display: none;<?php } ?>">
                    <select name="data[InvAdj][branch_id]" id="InvAdjBranchId" class="validate[required]" style="width: 75%;">
                        <?php
                        if(count($branches) != 1){
                        ?>
                        <option value=""><?php echo INPUT_SELECT; ?></option>
                        <?php
                        }
                        foreach($branches AS $branch){
                        ?>
                        <option <?php if($this->data['CycleProduct']['branch_id'] == $branch['Branch']['id']){ ?>selected="selected"<?php } ?> value="<?php echo $branch['Branch']['id']; ?>" com="<?php echo $branch['Branch']['company_id']; ?>" mcode="<?php echo $branch['ModuleCodeBranch']['adj_code']; ?>"><?php echo $branch['Branch']['name']; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </td>
            <td></td>
            <td></td>
        </tr>
    </table>
</fieldset>
<div class="order-detail-inventory-adjustment" style=" margin-top: 5px;"></div>
<div class="tblInvAdjFooter" style="display: none;">
    <div style="float: left; width: 30%;">
        <div class="buttons">
            <a href="#" class="positive btnBackInvAdj">
                <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
                <?php echo ACTION_BACK; ?>
            </a>
        </div>
        <div class="buttons">
            <button type="submit" class="positive saveInventory" >
                <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
                <span class="txtSaveInvAdj"><?php echo ACTION_SAVE; ?></span>
            </button>
        </div>
        <div style="clear: both;"></div>
    </div>
    <div style="clear: both;"></div>
</div>
<?php echo $this->Form->end(); ?>
