<?php 
$rnd = rand();
$frmName = "frm" . $rnd;
$date    = "date" . $rnd;
$interval = "interval" . $rnd;
$through  = "through" . $rnd;
$company  = "company" . $rnd;
$cgroup   = "cgroup" . $rnd;
$cgroupId = "cgroupId".$rnd;
$cgroupDel = "cgroupDel".$rnd;
$customer  = "customer" . $rnd;
$customerId  = "customerId".$rnd;
$customerDel = "customerDel".$rnd;
$class = "class" . $rnd;
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
        var now = new Date();
        $("#<?php echo $date; ?>").val(now.toString('dd/MM/yyyy'));
        $("#<?php echo $date; ?>").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd/mm/yy',
            beforeShow: function(){
                setTimeout(function(){
                    $("#ui-datepicker-div").css("z-index", 1000);
                }, 10);
            }
        }).unbind("blur");
        $("#<?php echo $interval; ?>").change(function(){
            if(Number($(this).val())>Number($("#<?php echo $through; ?>").val())){
                $("#<?php echo $through; ?>").val($(this).val());
            }
        });
        $("#<?php echo $through; ?>").change(function(){
            if(Number($(this).val())<Number($("#<?php echo $interval; ?>").val())){
                $("#<?php echo $interval; ?>").val($(this).val());
            }
        });
        $("#<?php echo $btnSearch; ?>").click(function(){
            var isFormValidated=$("#<?php echo $frmName; ?>").validationEngine('validate');
            if(isFormValidated){
                $.ajax({
                    type: "POST",
                    url: "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/accountReceivableResult",
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
        
        // Search Customer Group
        $("#<?php echo $cgroup; ?>").autocomplete("<?php echo $this->base . "/reports/searchCgroup"; ?>", {
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
            $("#<?php echo $cgroupId; ?>").val(pgroupId);
            $("#<?php echo $cgroupDel; ?>").show();
        });
        
        $("#<?php echo $cgroupDel; ?>").click(function(){
            $("#<?php echo $cgroupId; ?>").val('');
            $("#<?php echo $cgroup; ?>").val('');
            $(this).hide();
        });
        
        // Search Customer
        $("#<?php echo $customer; ?>").autocomplete("<?php echo $this->base . "/reports/searchCustomer"; ?>", {
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
            $("#<?php echo $customerId; ?>").val(pgroupId);
            $("#<?php echo $customerDel; ?>").show();
        });
        
        $("#<?php echo $customerDel; ?>").click(function(){
            $("#<?php echo $customerId; ?>").val('');
            $("#<?php echo $customer; ?>").val('');
            $(this).hide();
        });
        
        // Company Change
        $("#<?php echo $company; ?>").change(function(){
            var companyId = $(this).val();
            if(companyId != ''){
                $("#<?php echo $class; ?>").filterOptions('com', companyId, '');
            } else {
                $("#<?php echo $class; ?>").find("option").removeAttr('selected');
                $("#<?php echo $class; ?>").find('option').map(function () {
                    return $(this).parent('span').length === 0 ? this : null;
                }).wrap('<span>').hide();
                $("#<?php echo $class; ?>").find("option").unwrap().show();
                $("#<?php echo $class; ?>").find('option[value=""]').attr('selected', true);
            }
        });
    });
</script>
<form id="<?php echo $frmName; ?>" action="" method="post">
<div class="legend">
    <div class="legend_title">
        <?php echo MENU_ACCOUNT_RECEIVABLE; ?> <span class="btnShowHide" id="<?php echo $btnShowHide; ?>">[<?php echo TABLE_HIDE; ?>]</span>
        <div style="clear: both;"></div>
    </div>
    <div class="legend_content <?php echo $formFilter; ?>">
        <table style="width: 100%;">
            <tr>
                <td style="width: 8%;"><label for="<?php echo $date; ?>"><?php echo TABLE_DATE; ?>:</label></td>
                <td style="width: 15%;">
                    <div class="inputContainer">
                        <input type="text" id="<?php echo $date; ?>" name="date" class="validate[required]" />
                    </div>
                </td>
                <td style="width: 8%;"><label for="<?php echo $interval; ?>"><?php echo REPORT_INTERVAL; ?>:</label></td>
                <td style="width: 15%;">
                    <div class="inputContainer">
                        <input type="text" id="<?php echo $interval; ?>" name="interval" value="30" class="validate[required,custom[integer],min[1]]" />
                    </div>
                </td>
                <td style="width: 13%;"><label for="<?php echo $through; ?>"><?php echo REPORT_THROUGH; ?>:</label></td>
                <td style="width: 15%;">
                    <div class="inputContainer">
                        <input type="text" id="<?php echo $through; ?>" name="through" value="90" class="validate[required,custom[integer],min[1]]" />
                    </div>
                </td>
                <td>
                    <div class="buttons">
                        <button type="button" id="<?php echo $btnSearch; ?>" class="positive" style="width: 130px;">
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
                <td style="width: 8%;"><label for="<?php echo $company; ?>"><?php echo TABLE_COMPANY; ?>:</label></td>
                <td style="width: 15%;">
                    <div class="inputContainer">
                        <?php echo $this->Form->select($company, $companies, null, array('escape' => false, 'name' => 'company_id', 'empty' => TABLE_ALL)); ?>
                    </div>
                </td>
                <td style="width: 10%;"><label for="<?php echo $cgroup; ?>"><?php echo TABLE_CUSTOMER_GROUP; ?>:</label></td>
                <td style="width: 15%;">
                    <div class="inputContainer">
                        <input type="hidden" name="cgroup_id" id="<?php echo $cgroupId; ?>" />
                        <?php echo $this->Form->text($cgroup, array('escape' => false, 'name' => '', 'style' => 'width: 80%;')); ?>
                        <img alt="Delete" align="absmiddle" style="display: none; cursor: pointer;" id="<?php echo $cgroupDel; ?>" onmouseover="Tip('<?php echo ACTION_DELETE; ?>')" src="<?php echo $this->webroot . 'img/button/delete.png'; ?>" />
                    </div>
                </td>
                <td style="width: 8%;"><label for="<?php echo $customer; ?>"><?php echo TABLE_CUSTOMER; ?>:</label></td>
                <td style="width: 15%;">
                    <div class="inputContainer">
                        <input type="hidden" name="customer_id" id="<?php echo $customerId; ?>" />
                        <?php echo $this->Form->text($customer, array('escape' => false, 'name' => '', 'style' => 'width: 80%;')); ?>
                        <img alt="Delete" align="absmiddle" style="display: none; cursor: pointer;" id="<?php echo $customerDel; ?>" onmouseover="Tip('<?php echo ACTION_DELETE; ?>')" src="<?php echo $this->webroot . 'img/button/delete.png'; ?>" />
                    </div>
                </td>
                <td style="width: 8%;"><label for="<?php echo $class; ?>"><?php echo TABLE_CLASS; ?>:</label></td>
                <td style="width: 15%;">
                    <div class="inputContainer">
                        <select id="<?php echo $class; ?>" name="class_id">
                            <option value=""><?php echo TABLE_ALL; ?></option>
                            <?php
                            $sqlClass=mysql_query("SELECT id, name, (SELECT GROUP_CONCAT(company_id) FROM class_companies WHERE class_id = classes.id) AS company FROM classes WHERE ISNULL(parent_id) AND is_active=1 AND id IN (SELECT class_id FROM class_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id'].")) ORDER BY name");
                            while($data=mysql_fetch_array($sqlClass)){?>
                            <option value="<?php echo $data['id']; ?>" com="<?php echo $data['company']; ?>"><?php echo $data['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </td>
                <td></td>
            </tr>
        </table>
    </div>
</div>
</form>
<div id="<?php echo $result; ?>"></div>