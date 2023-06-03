<?php 
$rnd = rand();
$frmName = "frm" . $rnd;
$dueDate = "dueDate" . $rnd;
$dateFrom = "dateFrom" . $rnd;
$dateTo = "dateTo" . $rnd;
$columns = "columns" . $rnd;
$locationGroup = "locationGroup" . $rnd;
$location = "location" . $rnd;
$pgroup = "pgroup" . $rnd;
$pgroupId = "pgroupId".$rnd;
$pgroupDel = "pgroupDel".$rnd;
$parent = "parent" . $rnd;
$parentId = "parentId".$rnd;
$parentDel = "parentDel".$rnd;
$product = "product" . $rnd;
$productId = "productId".$rnd;
$productDel = "productDel".$rnd;
$btnSearchLabel = "txtBtnSearch". $rnd;
$btnSearch = "btnSearch" . $rnd;
$btnShowHide = "btnShowHide". $rnd;
$formFilter  = "formFilter".$rnd;
$result = "result" . $rnd;
?>
<script type="text/javascript">
    $(document).ready(function(){
        $("#<?php echo $frmName; ?>").validationEngine('attach', {
            isOverflown: true,
            overflownDIV: ".ui-tabs-panel"
        });
        var dates = $("#<?php echo $dateFrom; ?>, #<?php echo $dateTo; ?>").datepicker({
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true,
            onSelect: function( selectedDate ) {
                var option = this.id == "<?php echo $dateFrom; ?>" ? "minDate" : "maxDate",
                    instance = $( this ).data( "datepicker" );
                    date = $.datepicker.parseDate(
                        instance.settings.dateFormat ||
                        $.datepicker._defaults.dateFormat,
                        selectedDate, instance.settings );
                dates.not( this ).datepicker( "option", option, date );
            }
        });
        $("#<?php echo $dueDate; ?>").change(function(){
            var date = getDateByDateRange($(this).val());
            $('#<?php echo $dateTo; ?>').datepicker( "option", "minDate", date[0]);
            $('#<?php echo $dateFrom; ?>').datepicker("setDate", date[0]);
            $('#<?php echo $dateTo; ?>').datepicker("setDate", date[1]);
        });
        $("#<?php echo $btnSearch; ?>").click(function(){
            var url= "";
            if($("#<?php echo $columns; ?>").val()==1){
                url="<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/inventoryActivityParentResult";
            }else if($("#<?php echo $columns; ?>").val()==2){
                url="<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/inventoryActivityResult";
            }else if($("#<?php echo $columns; ?>").val()==3){
                url="<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/inventoryActivityWithGlobalResult";
            }else if($("#<?php echo $columns; ?>").val()==4){
                url="<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/inventoryActivityWithGlobalDetailResult";
            }else{
                url="<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/inventoryActivityDetailResult";
            }
            var isFormValidated=$("#<?php echo $frmName; ?>").validationEngine('validate');
            if(isFormValidated){
                $.ajax({
                    type: "POST",
                    url: url,
                    data: $("#<?php echo $frmName; ?>").serialize(),
                    beforeSend: function(){
                        $("#<?php echo $btnSearch; ?>").attr("disabled", true);
                        $("#<?php echo $btnSearchLabel; ?>").html("<?php echo ACTION_LOADING; ?>");
                        $(".loader").attr("src","<?php echo $this->webroot; ?>img/layout/spinner.gif");
                    },
                    success: function(result){
                        $("#<?php echo $btnSearch; ?>").removeAttr("disabled");
                        $("#<?php echo $btnSearchLabel; ?>").html("<?php echo GENERAL_SEARCH; ?>");
                        $(".loader").attr("src","<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                        $("#<?php echo $result; ?>").html(result);
                    }
                });
            }
        });
        // Button Show Hide
        $("#<?php echo $btnShowHide; ?>").click(function(){
            var text = $(this).text();
            var formFilter = $(".<?php echo $formFilter; ?>");
            if(text == "[<?php echo TABLE_SHOW; ?>]"){
                formFilter.show();
                $(this).text("[<?php echo TABLE_HIDE; ?>]");
            }else{
                formFilter.hide();
                $(this).text("[<?php echo TABLE_SHOW; ?>]");
            }
        });
        
        // Search Product
        $("#<?php echo $product; ?>").autocomplete("<?php echo $this->base . "/reports/searchProduct"; ?>", {
            width: 410,
            max: 10,
            scroll: true,
            scrollHeight: 500,
            formatItem: function(data, i, n, value) {
                return value.split(".*")[1];
            },
            formatResult: function(data, value) {
                return value.split(".*")[1];
            }
        }).result(function(event, value){
            var productId = value.toString().split(".*")[0];
            $("#<?php echo $productId; ?>").val(productId);
            $("#<?php echo $productDel; ?>").show();
        });
        
        $("#<?php echo $productDel; ?>").click(function(){
            $("#<?php echo $productId; ?>").val('');
            $("#<?php echo $product; ?>").val('');
            $(this).hide();
        });
        
        // Search Parent
        $("#<?php echo $parent; ?>").autocomplete("<?php echo $this->base . "/reports/searchProduct"; ?>", {
            width: 410,
            max: 10,
            scroll: true,
            scrollHeight: 500,
            formatItem: function(data, i, n, value) {
                return value.split(".*")[1];
            },
            formatResult: function(data, value) {
                return value.split(".*")[1];
            }
        }).result(function(event, value){
            var productId = value.toString().split(".*")[0];
            $("#<?php echo $parentId; ?>").val(productId);
            $("#<?php echo $parentDel; ?>").show();
        });
        
        $("#<?php echo $parentDel; ?>").click(function(){
            $("#<?php echo $parentId; ?>").val('');
            $("#<?php echo $parent; ?>").val('');
            $(this).hide();
        });
        
        // Search Product Group
        $("#<?php echo $pgroup; ?>").autocomplete("<?php echo $this->base . "/reports/searchPgroup"; ?>", {
            width: 410,
            max: 10,
            scroll: true,
            scrollHeight: 500,
            formatItem: function(data, i, n, value) {
                return value.split(".*")[1];
            },
            formatResult: function(data, value) {
                return value.split(".*")[1];
            }
        }).result(function(event, value){
            var pgroupId = value.toString().split(".*")[0];
            $("#<?php echo $pgroupId; ?>").val(pgroupId);
            $("#<?php echo $pgroupDel; ?>").show();
        });
        
        $("#<?php echo $pgroupDel; ?>").click(function(){
            $("#<?php echo $pgroupId; ?>").val('');
            $("#<?php echo $pgroup; ?>").val('');
            $(this).hide();
        });
        
        // Location Group
        $("#<?php echo $locationGroup; ?>").change(function(){
            resetLocation<?php echo $rnd; ?>();
        });
        // Reset Location
        resetLocation<?php echo $rnd; ?>();
    });
    
    function resetLocation<?php echo $rnd; ?>(){
        var locationGroup = $("#<?php echo $locationGroup; ?>").val();
        $("#<?php echo $location; ?>").filterOptions('location-group', locationGroup, '');
    }
</script>
<form id="<?php echo $frmName; ?>" action="" method="post">
<div class="legend">
    <div class="legend_title">
        <?php echo MENU_PRODUCT_INVENTORY_ACTIVITY; ?> <span class="btnShowHide" id="<?php echo $btnShowHide; ?>">[<?php echo TABLE_HIDE; ?>]</span>
        <div style="clear: both;"></div>
    </div>
    <div class="legend_content <?php echo $formFilter; ?>">
        <table style="width: 100%;">
            <tr>
                <td style="width: 8%;"><label for="<?php echo $dueDate; ?>"><?php echo REPORT_DUE_DATE; ?>:</label></td>
                <td style="width: 15%;"><?php echo $this->Form->select($dueDate, $dateRange, null, array('escape' => false, 'empty' => INPUT_SELECT, 'name' => 'due_date', 'style' => 'width: 90%;')); ?></td>
                <td style="width: 8%;"><label for="<?php echo $dateFrom; ?>"><?php echo REPORT_FROM; ?>:</label></td>
                <td style="width: 15%;">
                    <div class="inputContainer">
                        <input type="text" id="<?php echo $dateFrom; ?>" name="date_from" class="validate[required]" />
                    </div>
                </td>
                <td style="width: 8%;"><label for="<?php echo $dateTo; ?>"><?php echo REPORT_TO; ?>:</label></td>
                <td style="width: 15%;">
                    <div class="inputContainer">
                        <input type="text" id="<?php echo $dateTo; ?>" name="date_to" class="validate[required]" />
                    </div>
                </td>
                <td style="width: 4%;"><label for="<?php echo $columns; ?>"><?php echo ACTION_VIEW; ?>:</label></td>
                <td style="width: 15%;">
                    <select id="<?php echo $columns; ?>">
                        <option value="1"><?php echo TABLE_PARENT_SUMMARY; ?></option>
                        <option value="2" selected="selected"><?php echo TABLE_ITEM_SUMMARY; ?></option>
                        <option value="3"><?php echo TABLE_ITEM_ACTIVITY_SUMMARY; ?></option>
                        <option value="4"><?php echo TABLE_ITEM_ACTIVITY_DETAIL; ?></option>
                        <option value="5"><?php echo TABLE_ITEM_DETAIL; ?></option>
                    </select>
                </td>
                <td>
                    <div class="buttons">
                        <button type="button" id="<?php echo $btnSearch; ?>" class="positive">
                            <img src="<?php echo $this->webroot; ?>img/button/search.png" alt=""/>
                            <span id="<?php echo $btnSearchLabel; ?>"><?php echo GENERAL_SEARCH; ?></span>
                        </button>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="legend_content <?php echo $formFilter; ?>">
        <table style="width: 100%;">
            <tr>
                <td style="width: 8%;"><label for="<?php echo $locationGroup; ?>"><?php echo TABLE_LOCATION_GROUP; ?>:</label></td>
                <td style="width: 15%;">
                    <div class="inputContainer" style="width: 100%;">
                        <?php echo $this->Form->select($locationGroup, $locationGroups, null, array('escape' => false, 'name' => 'location_group_id', 'empty' => TABLE_ALL, 'style' => 'width: 90%;')); ?>
                    </div>
                </td>
                <td style="width: 8%;"><label for="<?php echo $location; ?>"><?php echo TABLE_LOCATION; ?>:</label></td>
                <td style="width: 15%;">
                    <div class="inputContainer" style="width: 100%;">
                        <select id="<?php echo $location; ?>" name="location_id">
                            <option value="" location-group=""><?php echo TABLE_ALL; ?></option>
                            <?php
                            foreach($locations AS $loc){
                            ?>
                            <option value="<?php echo $loc['Location']['id']; ?>" location-group="<?php echo $loc['Location']['location_group_id']; ?>"><?php echo $loc['Location']['name']; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </td>
                <td style="width: 8%;"><label for="<?php echo $pgroup; ?>"><?php echo MENU_PRODUCT_GROUP_MANAGEMENT; ?>:</label></td>
                <td style="width: 25%;">
                    <div class="inputContainer" style="width: 100%;">
                        <input type="hidden" name="pgroup_id" id="<?php echo $pgroupId; ?>" />
                        <?php echo $this->Form->text($pgroup, array('escape' => false, 'name' => '', 'style' => 'width: 80%;')); ?>
                        <img alt="Delete" align="absmiddle" style="display: none; cursor: pointer;" id="<?php echo $pgroupDel; ?>" onmouseover="Tip('<?php echo ACTION_DELETE; ?>')" src="<?php echo $this->webroot . 'img/button/delete.png'; ?>" />
                    </div>
                </td>
                <td></td>
            </tr>
        </table>
    </div>
    <div class="legend_content <?php echo $formFilter; ?>">
        <table style="width: 100%;">
            <tr>
                <td style="width: 8%;"><label for="<?php echo $parent; ?>"><?php echo PRODUCT_PARENT; ?>:</label></td>
                <td style="width: 25%;">
                    <div class="inputContainer">
                        <input type="hidden" name="parent_id" id="<?php echo $parentId; ?>" />
                        <?php echo $this->Form->text($parent, array('escape' => false, 'name' => '', 'style' => 'width: 80%;')); ?>
                        <img alt="Delete" align="absmiddle" style="display: none; cursor: pointer;" id="<?php echo $parentDel; ?>" onmouseover="Tip('<?php echo ACTION_DELETE; ?>')" src="<?php echo $this->webroot . 'img/button/delete.png'; ?>" />
                    </div>
                </td>
                <td style="width: 5%;"><label for="<?php echo $product; ?>"><?php echo TABLE_PRODUCT; ?>:</label></td>
                <td style="width: 25%;">
                    <div class="inputContainer">
                        <input type="hidden" name="product_id" id="<?php echo $productId; ?>" />
                        <?php echo $this->Form->text($product, array('escape' => false, 'name' => '', 'style' => 'width: 80%;')); ?>
                        <img alt="Delete" align="absmiddle" style="display: none; cursor: pointer;" id="<?php echo $productDel; ?>" onmouseover="Tip('<?php echo ACTION_DELETE; ?>')" src="<?php echo $this->webroot . 'img/button/delete.png'; ?>" />
                    </div>
                </td>
                <td></td>
            </tr>
        </table>
    </div>
</div>
</form>
<div id="<?php echo $result; ?>"></div>