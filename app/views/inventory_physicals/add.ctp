<?php 
$queryClosingDate = mysql_query("SELECT DATE_FORMAT(date,'%d/%m/%Y') FROM account_closing_dates ORDER BY id DESC LIMIT 1");
$dataClosingDate  = mysql_fetch_array($queryClosingDate);
?>
<script type="text/javascript">
    var fieldRequireInventoryPhysical = ['InventoryPhysicalLocationGroupId'];
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        // Hide Branch
        $("#InventoryPhysicalBranchId").filterOptions('com', '0', '');
        $("#InventoryPhysicalLocationGroupId").chosen({ width: 280});
        // Form Validate
        $("#InventoryPhysicalForm").validationEngine('detach');
        $("#InventoryPhysicalForm").validationEngine('attach');
        
        // Date Datepicker
        $("#InventoryPhysicalDate").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd/mm/yy',
            minDate: '<?php echo $dataClosingDate[0]; ?>',
            maxDate: 0,
            onSelect: function(dateText, inst) {
                $("#InventoryPhysicalForm").validationEngine("hideAll");
                var obj       = $(this);
                var productId = $("#tblInventoryPhysical").find(".product_name").val();
                if(productId == undefined){
                    setCookie('InventoryPhysicalDate', obj.val());
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
                                setCookie('InventoryPhysicalDate', obj.val());
                                // Call Detail Inventory Adjustment
                                loadDetailInventoryAdj();
                                $(this).dialog("close");
                            },
                            '<?php echo ACTION_CANCEL; ?>': function() {
                                useCookie("#InventoryPhysicalDate", "InventoryPhysicalDate");
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            }
        });
        
        // Action Button Back
        $(".btnBackInventoryPhysical").click(function(event){
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
                        backInventoryPhysical();
                    }
                }
            });
        });
        
        // Action Change Location Group
        $("#InventoryPhysicalLocationGroupId").change(function(){
            var obj = $(this);
            var productId   = $("#tblInventoryPhysical").find(".product_name").val();
            if(productId == undefined){
                setCookie('InventoryPhysicalLocationGroupId', obj.val());
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
                            setCookie('InventoryPhysicalLocationGroupId', obj.val());
                            // Call Detail Inventory Adjustment
                            loadDetailInventoryAdj();
                            $(this).dialog("close");
                        },
                        '<?php echo ACTION_CANCEL; ?>': function() {
                            useCookie("#InventoryPhysicalLocationGroupId", "InventoryPhysicalLocationGroupId");
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
        
        $("#InventoryPhysicalCompanyId").change(function(){
            var obj   = $(this);
            if($(".tblInventoryPhysicalList").find(".product_id").val() == undefined){
                $.cookie('companyIdInventoryPhysical', obj.val(), { expires: 7, path: "/" });
                $("#InventoryPhysicalBranchId").filterOptions('com', obj.val(), '');
                $("#InventoryPhysicalBranchId").change();
                changeInputCSSInventoryPhysical();
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
                            $.cookie('companyIdInventoryPhysical', obj.val(), { expires: 7, path: "/" });
                            $("#InventoryPhysicalBranchId").filterOptions('com', obj.val(), '');
                            $("#InventoryPhysicalBranchId").change();
                            changeInputCSSInventoryPhysical();
                            loadDetailInventoryAdj();
                            $(this).dialog("close");
                        },
                        '<?php echo ACTION_CANCEL; ?>': function() {
                            $("#InventoryPhysicalCompanyId").val($.cookie("companyIdInventoryPhysical"));
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
        // Action Branch
        $("#InventoryPhysicalBranchId").change(function(){
            var obj = $(this);
            if($(".tblInventoryPhysicalList").find(".product_id").val() == undefined){
                $.cookie('branchIdInventoryPhysical', obj.val(), { expires: 7, path: "/" });
                branchChangeInventoryPhysical(obj);
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
                            $.cookie('branchIdInventoryPhysical', obj.val(), { expires: 7, path: "/" });
                            branchChangeInventoryPhysical(obj);
                            loadDetailInventoryAdj();
                            $(this).dialog("close");
                        },
                        '<?php echo ACTION_CANCEL; ?>': function() {
                            $("#InventoryPhysicalBranchId").val($.cookie("branchIdInventoryPhysical"));
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
        // Company Action
        if($.cookie('companyIdInventoryPhysical')!=null || $("#InventoryPhysicalCompanyId").find("option:selected").val() != ''){
            if($.cookie('companyIdTransferOrder') != null){
                $("#InventoryPhysicalCompanyId").val($.cookie('companyIdInventoryPhysical'));
            }
            $("#InventoryPhysicalBranchId").filterOptions('com', $("#InventoryPhysicalCompanyId").val(), '');
            $("#InventoryPhysicalBranchId").change();
        }
        changeInputCSSInventoryPhysical();
        // Call Detail Inventory Adjustment
        loadDetailInventoryAdj();
        loadAutoCompleteOff();
    });
    
    function branchChangeInventoryPhysical(obj){
        var mCode  = obj.find("option:selected").attr("mcode");
        $("#InventoryPhysicalReference").val("<?php echo date("y"); ?>"+mCode);
    }
    
    // Detail Inventory Adjustment
    function loadDetailInventoryAdj(){
        $.ajax({
            type: "POST",
            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/addDetail/",
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
    function checkExistBeforeSaveInventoryPhysical(){
        var formName = "#InventoryPhysicalForm";
        var validateBack =$(formName).validationEngine("validate");
        if(!validateBack){
            return false;
        }else{
            var productId   = $("#tblInventoryPhysical").find(".product_name").val();
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
    
    function backInventoryPhysical(){
        $("#InventoryPhysicalForm").validationEngine("hideAll");
        var rightPanel = $(".btnBackInventoryPhysical").parent().parent().parent().parent().parent();
        var leftPanel  = rightPanel.parent().find(".leftPanel");
        rightPanel.hide();rightPanel.html("");
        leftPanel.show("slide", { direction: "left" }, 500);
        oCache.iCacheLower = -1;
        oTableInventoryPhysical.fnDraw(false);
    }
    
    function saveContinueInventoryPhysical(){
        $("#InventoryPhysicalForm").validationEngine("hideAll");
        var rightPanel = $(".btnBackInventoryPhysical").parent().parent().parent().parent().parent();
        rightPanel.html("<?php echo ACTION_LOADING; ?>");
        rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/add/");
    }
    
    function changeInputCSSInventoryPhysical(){
        var cssStyle  = 'inputDisable';
        var cssRemove = 'inputEnable';
        var disabled  = true;
        $(".searchProductInventoryPhysical").hide();
        if($("#InventoryPhysicalCompanyId").val() != ''){
            cssStyle  = 'inputEnable';
            cssRemove = 'inputDisable';
            disabled  = false;
            $(".searchProductInventoryPhysical").show();
        }   
        // Label
        $("#InventoryPhysicalForm").find("label").removeAttr("class");
        $("#InventoryPhysicalForm").find("label").each(function(){
            var label = $(this).attr("for");
            if(label != 'InventoryPhysicalCompanyId'){
                $(this).addClass(cssStyle);
            }
        });
        // Input & Select
        $("#InventoryPhysicalForm").find("input").each(function(){
            $(this).removeClass(cssRemove);
            $(this).addClass(cssStyle);
        });
        $("#InventoryPhysicalForm").find("select").each(function(){
            var selectId = $(this).attr("id");
            if(selectId != 'InventoryPhysicalCompanyId'){
                $(this).removeClass(cssRemove);
                $(this).addClass(cssStyle);
                $(this).attr("disabled", disabled);
            }
        });
    }
</script>
<?php echo $this->Form->create('InventoryPhysical', array('id' => 'InventoryPhysicalForm', 'url' => '/inventory_physicals/save/')); ?>
<input type="hidden" id="saveExitInventoryPhysical" value="1" />
<div style="float: right; width: 165px; text-align: right; cursor: pointer;" id="btnHideShowHeaderInventoryPhysical">
    [<span>Hide</span> Header Information <img alt="" align="absmiddle" style="width: 16px; height: 16px;" src="<?php echo $this->webroot . 'img/button/arrow-up.png'; ?>" />]
</div>
<div style="clear: both;"></div>
<fieldset id="topInventoryPhysical">
    <legend><?php echo MENU_SALES_MIX_INFO; ?></legend>
    <table cellpadding="3" cellspacing="0" style="width: 100%;">
        <tr>
            <td style="width: 7%; vertical-align: top;"><label for="InventoryPhysicalDate"><?php echo TABLE_DATE; ?></label> <span class="red">*</span> :</td>
            <td style="width: 17%; vertical-align: top;">
                <div class="inputContainer" style="width:100%">
                    <?php echo $this->Form->text('date', array('value' => date("d/m/Y"),'empty' => INPUT_SELECT, 'label' => false, 'class' => 'validate[required]', 'style' => 'width:85%;')); ?>
                </div>
            </td>
            <td rowspan="2" style="width: 5%; vertical-align: top;"><label for="InventoryPhysicalNote"><?php echo TABLE_MEMO; ?> :</label></td>
            <td rowspan="2">
                <div class="inputContainer" style="width:100%;">
                    <textarea style="width: 90%; height: 60px;" id="InventoryPhysicalNote" name="data[InventoryPhysical][note]"></textarea>
                </div>
            </td>
            <td style="width: 7%; vertical-align: top;"><?php if(count($companies) > 1){ ?><label for="InventoryPhysicalCompanyId"><?php echo TABLE_COMPANY; ?></label> <span class="red">*</span> :<?php } ?></td>
            <td style="width: 15%; vertical-align: top;">
                <div class="inputContainer" style="width:100%; <?php if(count($companies) == 1){ ?>display: none;<?php } ?>">
                    <select name="data[InventoryPhysical][company_id]" id="InventoryPhysicalCompanyId" class="validate[required]" style="width: 85%;">
                        <?php
                        if(count($companies) != 1){
                        ?>
                        <option value=""><?php echo INPUT_SELECT; ?></option>
                        <?php
                        }
                        foreach($companies AS $company){
                        ?>
                        <option value="<?php echo $company['Company']['id']; ?>"><?php echo $company['Company']['name']; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </td>
            <td style="width: 7%; vertical-align: top;"><?php if(count($locationGroups) > 1){ ?><label for="InventoryPhysicalLocationGroupId"><?php echo TABLE_LOCATION_GROUP; ?></label> <span class="red">*</span> :<?php } ?></td>
            <td style="width: 25%; vertical-align: top;">
                <div class="inputContainer" style="width:100%; <?php if(count($locationGroups) == 1){ ?>display: none;<?php } ?>">
                    <?php 
                    $empty = INPUT_SELECT;
                    if(COUNT($locationGroups) == 1){
                        $empty = false;
                    }
                    echo $this->Form->input('location_group_id', array('empty' => $empty, 'label' => false, 'style' => 'width:280px')); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><label for="InventoryPhysicalCode"><?php echo TABLE_REFERENCE; ?></label> <span class="red">*</span> :</td>
            <td>
                <div class="inputContainer" style="width:100%">
                    <?php echo $this->Form->input('code', array('empty' => INPUT_SELECT, 'label' => false, 'class' => 'validate[required]', 'style' => 'width:85%', 'readonly' => true, 'value' => 'IVM')); ?>
                </div>
            </td>
            <td><?php if(count($branches) > 1){ ?><label for="InventoryPhysicalBranchId"><?php echo MENU_BRANCH; ?></label> <span class="red">*</span> :<?php } ?></td>
            <td>
                <div class="inputContainer" style="width:100%; <?php if(count($branches) == 1){ ?>display: none;<?php } ?>">
                    <select name="data[InventoryPhysical][branch_id]" id="InventoryPhysicalBranchId" class="validate[required]" style="width: 85%;">
                        <?php
                        if(count($branches) != 1){
                        ?>
                        <option value=""><?php echo INPUT_SELECT; ?></option>
                        <?php
                        }
                        foreach($branches AS $branch){
                        ?>
                        <option value="<?php echo $branch['Branch']['id']; ?>" com="<?php echo $branch['Branch']['company_id']; ?>" mcode="IVM"><?php echo $branch['Branch']['name']; ?></option>
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
<div class="order-detail-inventory-adjustment" style="margin-top: 5px;"></div>
<div class="tblInventoryPhysicalFooter" style="display: none;">
    <div style="float: left; width: 30%;">
        <div class="buttons">
            <a href="#" class="positive btnBackInventoryPhysical">
                <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
                <?php echo ACTION_BACK; ?>
            </a>
        </div>
        <div class="buttons">
            <button type="submit" class="positive saveInventoryPhysical" >
                <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
                <span class="txtSaveInventoryPhysical"><?php echo ACTION_SAVE; ?></span>
            </button>
        </div>
        <div style="clear: both;"></div>
    </div>
    <div style="clear: both;"></div>
</div>
<?php echo $this->Form->end(); ?>
