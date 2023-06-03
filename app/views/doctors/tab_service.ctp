<?php
include('includes/function.php');
echo $this->element('prevent_multiple_submit');
$absolute_url = FULL_BASE_URL . Router::url("/", false);
//$exchangeRate = getExchangeRate();
$tblName = "tbl123"; 
$exchangeRate = 4150; 
$tblNameRadom = "tbl" . rand();
// Authentication
$this->element('check_access');
$allowProductDiscount = checkAccess($user['User']['id'], $this->params['controller'], 'discount');
$rnd = rand();
$btnShowHide = "btnShowHide" . $rnd;
$formFilter = "PatientCheckoutTr";
?>
<script type="text/javascript">
    var selected;
    function checkDiscount(field, rules, i, options) {
        if ($("#discount_p").val() != 0 && $("#discount_d").val() != 0) {
            return "<?php echo VALIDATION_ALLOW_1_METHOD_ONLY; ?>";
        }
    }
    function comboRefesh() {
        selected = new Array();
        $(".classSection").each(function () {
            if ($(this).val() != '') {
                selected.push($(this).val());
            }
        });
    }

    function sortNuTableCheckOut() {
        var sort = 1;
        $(".PatientCheckoutTr").each(function () {
            $(this).find("td:eq(0)").html(sort);
            sort++;
        });
    }
    
    function keyEventCheckout() {
        $(".classSection, .patientCheckoutService, .qty, .unit_price").unbind('click').unbind('keyup').unbind('keypress').unbind('change').unbind('blur');
        $(".patientCheckoutService").click(function () {
            var section = $(this).closest("tr").find("td .classSection option:selected").val();
            if (section == '') {
                alert('<?php echo MESSAGE_SELECT_SECTION; ?>');
                return false;
            }
        });
        // action change service price
        $(".patientCheckoutService").change(function () {  
            var id = $(this).attr('rel');
            var pateintGroup = $("#PatientPatientGroupId").val();
            var companyInsuranceId = $("#CheckoutCompanyInsuranceId").val();     
            if(companyInsuranceId==undefined){
                companyInsuranceId = "";
            }     
            $.ajax({
                type: "POST",
                url: '<?php echo $absolute_url . $this->params['controller']; ?>/getServicePrice/' + $(this).val() + '/' + pateintGroup + '/' + companyInsuranceId,
                data: "",
                success: function (msg) {
                    $('#DocSerQty' + id).val(1);
                    $('#DocSerUnitPrice' + id).val(Number(msg).toFixed(2));
                    $('#DocSerTotalPrice' + id).val(Number(msg).toFixed(2));
                    getTotalAmountPatientCheckOut();    
                    $('#DocSerUnitPrice' + id).attr('rel', $(this).val());
                    $('#DocSerUnitPrice' + id).attr('index', id);               
                }
            });
        });
        
        
        $(".classSection").change(function () {
            $("#ServiceCompanyId").closest("tr").find("td .classCompany").val($(this).find("option:selected").attr("class"));
            var serId = this.id;
            var pateintGroup = $("#PatientPatientGroupId").val();
            var companyInsuranceId = $("#CheckoutCompanyInsuranceId").val();              
            if(companyInsuranceId==undefined){
                companyInsuranceId = "";
            }
            var id = $(this).attr('rel');
            
            $.ajax({
                type: "POST",
                url: '<?php echo $absolute_url . $this->params['controller']; ?>/getService/' + $(this).val(),
                data: "",
                success: function (msg) {
                    
                    $("#DocServiceId" + id).html(msg);
                    return true;
                    
                    var values = [];
                    $('select.patientCheckoutService').each(function () {
                        values.push($(this).val());
                    });                    
                    $("#DocServiceId" + id).html(msg).find("option").each(function () {
                        var s = $(this);
                        $.each(values, function (v, i) {
                            if (s.val() == i && s.val() != '') {
                                s.hide();
                            }
                        });
                    });
                    var service = $('select.patientCheckoutService');
                    $("#DocServiceId" + id).change(function () {                    
                        values = [];
                        service.each(function () {
                            if ($(this).val() != '') {
                                values.push($(this).val());
                            }
                        });
                        service.find('option').show().each(function () {
                            if ($.inArray(this.value, values) != -1) {
                                $(this).hide();
                            }
                        });
                    });
                }
            });
        });
        
        $(".unit_price").blur(function () {
            var id = $(this).attr('rel');         
            if ($(this).val() != "") {
                var totalPrice = Number($(this).val()) * $("#DocSerQty" + id).val();
                $('#DocSerTotalPrice' + id).val(Number(totalPrice).toFixed(2));
                getTotalAmountPatientCheckOut();
            }
        });
        $(".unit_price").live('keyup', function () {
            var id = $(this).attr('rel');         
            if ($(this).val() != "") {
                var totalPrice = Number($(this).val()) * $("#DocSerQty" + id).val();
                $('#DocSerTotalPrice' + id).val(Number(totalPrice).toFixed(2));
                getTotalAmountPatientCheckOut();
            }
        });
        
        
        $(".qty").blur(function () {
            var qtyId = this.id;
            var id = $(this).attr('rel');
            if ($(this).val() == "") {
                $(this).val('1');
                var totalPrice = Number($("#DocSerUnitPrice" + id).val()) * $(this).val();
                $('#DocSerTotalPrice' + id).val(Number(totalPrice).toFixed(2));
                getTotalAmountPatientCheckOut();
            }
        });
        $(".qty").live('click', function () {
            var id = $(this).attr('rel');
            $("#DocSerQty" + id).val("");
        });
        $(".qty").live('keyup', function () {
            var id = $(this).attr('rel');
            var qty = replaceNum($(this).closest("tr").find(".qty").val());
            var discount = Number($("#DocSerDiscount" + id).val());
            var discountPercent = replaceNum($(this).closest("tr").find("input[name='discount_percent[]']").val());
            var discountAmount  = replaceNum($(this).closest("tr").find("input[name='discount_amount[]']").val());
          
            if(discountPercent !=0 && discountPercent !=''){
               var totalDiscountPer = discountPercent * qty;
               $(this).closest("tr").find("input[name='data[Patient][discount][]']").val(totalDiscountPer);
               var totalPrice = (Number($("#DocSerUnitPrice" + id).val()) * $(this).val()) - totalDiscountPer;
            } else{
               var totalPrice = (Number($("#DocSerUnitPrice" + id).val()) * $(this).val()) - discount;
            }
            
            $('#DocSerTotalPrice' + id).val(Number(totalPrice).toFixed(2));
            getTotalAmountPatientCheckOut();
        });
    }
    
    function staffRefreshType() {
        var i = Number($("#doctorTabSer").find(".serviceId:last").text()) + 1;
        $("#doctorTabSer").find(".serviceId:last").text(i);
    }
    
    function comboRefeshType() {
        $(".qty").each(function () {
            $("#doctorTabSer").find(".qty:last").val("");
        });
        $(".unit_price").each(function () {
            $("#doctorTabSer").find(".unit_price:last").val("");
        });
        $(".total_price").each(function () {
            $("#doctorTabSer").find(".total_price:last").val("");
        });
        var i = 1;
        $(".patientDocSerUnitPrice").each(function () {
            $("#doctorTabSer").find(".patientDocSerUnitPrice:last").val("");
            i++;
        });
    }

    function getTotalAmountPatientCheckOut() {
        var totalAmount = 0;
        var totalAmountPaid = 0;
        var totalDiscountAll = parseFloat($("#PatientDiscountTotal").val());
        var totalAmountPaid = parseFloat($("#PatientTotalAmountPaid").val());
        totalDiscountAll = totalDiscountAll != "" ? totalDiscountAll : 0;
        totalAmountPaid = totalAmountPaid != "" ? totalAmountPaid : 0;
        $(".total_price").each(function () {
            if ($.trim($(this).val()) != '' || $(this).val() != undefined) {
                totalAmount += Number($(this).val());
            }
        });
        if (isNaN(totalAmount)) {
            $("#PatientTotalAmount").val(0.00);
            $("#PatientDiscountTotal").val(0.00);
            $("#PatientSubTotalAmount").val(0.00);
        } else {
            $("#PatientTotalAmount").val((totalAmount).toFixed(2));
            $("#PatientSubTotalAmount").val((totalAmount).toFixed(2));
        }
    }

    function addNewDiscountCheckOut(tr) {
        $.ajax({
            type: "POST",
            url: "<?php echo $this->base . "/cashiers/discount"; ?>",
            beforeSend: function () {
                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner.gif');
            },
            success: function (msg) {
                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                $("#dialog").html(msg).dialog({
                    title: '<?php echo 'Select Discount'; ?>',
                    resizable: false,
                    modal: true,
                    width: 450,
                    height: 180,
                    position: 'center',
                    closeOnEscape: true,
                    open: function (event, ui) {
                        $(".ui-dialog-buttonpane").show();
                        $(".ui-dialog-titlebar-close").show();
                    },
                    buttons: {
                        '<?php echo ACTION_OK; ?>': function () {
                            
                            var discountAmount     = $("#inputInvoiceDisAmt").val();
                            var discountPercent = $("#inputInvoiceDisPer").val();
                            var calTotalPrice = tr.find("input[name='data[Patient][total_price][]']").val();
                            
                            var discount = 0;
                            if (discountPercent>0) {
                                discount = (parseFloat(discountPercent) * calTotalPrice) / 100;
                                tr.closest("tr").find("input[name='discount_percent[]']").val(discount);
                            } 
                            if (discountAmount>0) {
                                discount = parseFloat(discountAmount);
                                tr.closest("tr").find("input[name='discount_amount[]']").val(discount);
                            }
                            if (discount >= 0) {
                                tr.find("input[name='data[Patient][discount][]']").val(discount.toFixed(2));
                            } else {
                                tr.find("input[name='data[Patient][discount][]']").val(discount.toFixed(2));
                            }
                            
                            var id = tr.find("input[name='data[Patient][discount][]']").attr('rel');
                            var totalPrice = (Number($("#DocSerUnitPrice" + id).val()) * Number($("#DocSerQty" + id).val())) - discount;
                            $('#DocSerTotalPrice' + id).val(Number(totalPrice).toFixed(2));
                            getTotalAmountPatientCheckOut();
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
    }
    
    function removeDiscountCheckOut(tr) {
        var discount = tr.find("input[name='data[Patient][discount][]']").val();
        var id = tr.find("input[name='data[Patient][discount][]']").attr('rel');
        var totalPrice = Number($("#DocSerUnitPrice" + id).val()) * Number($("#DocSerQty" + id).val());
        $('#DocSerTotalPrice' + id).val(Number(totalPrice).toFixed(2));

        tr.find("input[name='discount_id[]']").val("");
        tr.find("input[name='discount_amount[]']").val(0.00);
        tr.find("input[name='discount_percent[]']").val(0.00);
        tr.find("input[name='data[Patient][discount][]']").val("");
        tr.find(".btnRemoveDiscountCheckOut").css("display", "none");
        getTotalAmountPatientCheckOut();
    }

    // add new discount total
    function addNewDiscountTotal(tr) {
        $.ajax({
            type: "POST",
            url: "<?php echo $this->base . "/cashiers/discount"; ?>",
            beforeSend: function () {
                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner.gif');
            },
            success: function (msg) {
                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                $("#dialog").html(msg).dialog({
                    title: '<?php echo 'Select Discount'; ?>',
                    resizable: false,
                    modal: true,
                    width: 450,
                    height: 180,
                    position: 'center',
                    closeOnEscape: true,
                    open: function (event, ui) {
                        $(".ui-dialog-buttonpane").show();
                        $(".ui-dialog-titlebar-close").show();
                    },
                    buttons: {
                        '<?php echo ACTION_OK; ?>': function () {
                            
                            var discountAmount     = $("#inputInvoiceDisAmt").val();
                            var discountPercent    = $("#inputInvoiceDisPer").val();
                            var calTotalPrice      = Number($("#PatientTotalAmount").val());
                            var discount = 0;
                            if (discountPercent>0) {
                                $("#LabelDisPercent").html('('+discountPercent+'%)');
                                discount = (parseFloat(discountPercent) * calTotalPrice) / 100;
                            }
                            if (discountAmount>0) {
                                $("#LabelDisPercent").html('');
                                discount = parseFloat(discountAmount);
                            }
                            if (discount >= 0) {
                                tr.find("input[name='data[Patient][total_discount]']").val(discount.toFixed(2));
                                tr.find("input[name='data[Patient][total_discount_per]']").val(parseFloat(discountPercent));
                            } else {
                                tr.find("input[name='data[Patient][total_discount]']").val(discount.toFixed(2));
                                tr.find("input[name='data[Patient][total_discount_per]']").val(parseFloat(discountPercent));
                            }
                            getTotalAmountPatientCheckOut();
                            
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
    }
    
    function removeDiscountTotal(tr) {
        var discount = tr.find("input[name='data[Patient][total_discount]']").val();
        tr.find("input[name='data[Patient][total_discount]']").val("0.00");
        tr.find(".btnRemoveDiscountTotal").css("display", "none");
        getTotalAmountPatientCheckOut();
    }

    $(document).ready(function () {
        $("#OrderBranchId").chosen({width: 250});
        $("#PatientTypePaymentId").chosen({width: 250});
        $("#CheckoutCompanyInsuranceId").chosen({width: 250});
       
        var acc = $("select.patient_coa_id option:selected").val();
        var accSa = $("select.sales_order_coa_id option:selected").val();
        $("#chartAccPatientCoa").val(acc);
        $("#chartAccSalesOrderCoa").val(accSa);
        keyEventCheckout();
        // Prevent Key Enter
        preventKeyEnter();
        $("#CheckoutFormDoctor").validationEngine();
        $("#CheckoutFormDoctor").ajaxForm({
            beforeSubmit: function (arr, $form, options) {
                $(".txtSavePatient").html("<?php echo ACTION_LOADING; ?>");
                 $(".loading").show();
            },
            success: function (result) {
                $(".loading").hide(); 
                $("#tabs3").tabs("select", 9);
                $("#tabServiceNum<?php echo $tblName;?>").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabServiceNum/<?php echo $this->params['pass'][0] . '/' . $this->params['pass'][1]; ?>");                    
            }

        });

        // Button Show Hide
        $("#<?php echo $btnShowHide; ?>").click(function () {
            var text = $(this).text();
            var formFilter = $(".<?php echo $formFilter; ?>");
            if (text == "[<?php echo 'Show'; ?>]") {
                formFilter.show();
                $(this).text("[<?php echo 'Hide'; ?>]");
                $(this).css('color', '#000');
            } else {
                formFilter.hide();
                $(this).text("[<?php echo 'Show'; ?>]");
                $(this).css('color', 'red');
            }
        });

        // hide coa that not belong to the company
        $(".sales_order_coa_id option").show();
        $(".sales_order_coa_id option").each(function () {
            if ($(this).attr("chart_account_type_name") != 'Accounts Receivable' && $(this).val() != "") {
                $(this).attr("disabled", 'disabled');
            }
        });
        if ($("#PatientCompanyId").val() != '') {
            $("#addPatientCheckOut").show();
//            $(".classSection").closest("tr").find("td .classSection").val('');
            $(".classSection").closest("tr").find("td .classSection option[class!='']").hide();
            $(".classSection").closest("tr").find("td .classSection option[class='" + $("#PatientCompanyId").val() + "']").show();
        }
        // for sort section in company
        $(".classCompany").change(function () {
            if ($("#PatientCompanyId").val() != '' && $("#PatientPatientGroupId").val() != "" && $("#CheckoutCompanyInsuranceId").val() != "") {
                $("#addPatientCheckOut").show();
//                $(".classSection").closest("tr").find("td .classSection").val('');
                $(".classSection").closest("tr").find("td .classSection option[class!='']").hide();
                $(".classSection").closest("tr").find("td .classSection option[class='" + $("#PatientCompanyId").val() + "']").show();
            } else {
                $(".classSection").closest("tr").find("td .classSection option[class!='']").show();
                $("#addPatientCheckOut").hide();
            }
            comboRefesh();
        });
        $("#CheckoutCompanyInsuranceId").change(function () {
            if ($("#PatientCompanyId").val() != '' && $("#PatientPatientGroupId").val() != "" && $("#CheckoutCompanyInsuranceId").val() != "") {
                $("#addPatientCheckOut").show();
//                $(".classSection").closest("tr").find("td .classSection").val('');
                $(".classSection").closest("tr").find("td .classSection option[class!='']").hide();
                $(".classSection").closest("tr").find("td .classSection option[class='" + $("#PatientCompanyId").val() + "']").show();
            } else {
                $("#addPatientCheckOut").hide();
            }
        });

        $("#PatientTotalAmountPaid").blur(function () {
            if ($(this).val() == "") {
                $(this).val('0.00');
                getTotalAmountPatientCheckOut();
            }
        });
        $("#PatientTotalAmountPaid").live('click', function () {
            $("#PatientTotalAmountPaid").val("");
        });
        $("#PatientTotalAmountPaid").live('keyup', function () {
            getTotalAmountPatientCheckOut();
        });
        $(".btnDiscountTotal").click(function () {
            addNewDiscountTotal($(this).closest("tr"));
        });
        $(".btnRemoveDiscountTotal").click(function () {
            removeDiscountTotal($(this).closest("tr"));
        });
        $(".btnDiscountCheckOut").click(function () {
            addNewDiscountCheckOut($(this).closest("tr"));
        });
        $(".btnRemoveDiscountCheckOut").click(function () {
            removeDiscountCheckOut($(this).closest("tr"));
        });
        
        $("#PatientOPDDocId").change(function () {
            if($(this).val()!=""){
                localStorage.setItem('doctor_id', $(this).val());
            }
        });
        
        // form add new service for quotation
        $(".btnAddType").click(function () {            
            $("#doctorTabSer").find(".PatientCheckoutTr:last").clone(true).appendTo("#tableToModify");
            var id = $("#doctorTabSer").find(".classSection:last").attr('rel');            
            if (id == "") {
                id = $("#doctorTabSer").find(".serviceId:last").text();                
            } else {
                id++;
            }
            // update id in tr : last                        
            $("#doctorTabSer").find(".classSection:last").attr('id', 'DocSecId' + id);
            $("#doctorTabSer").find(".classSection:last").attr('rel', id);
            $("#doctorTabSer").find(".patientCheckoutService:last").attr('id', 'DocServiceId' + id);
            $("#doctorTabSer").find(".patientCheckoutService:last").attr('rel', id);
            $("#doctorTabSer").find(".classDoctor:last").attr('id', 'PatientOPDDocId' + id);
            $("#doctorTabSer").find(".classDoctor:last").find("option").each(function(){
                if(parseFloat($(this).val()) == parseFloat(localStorage.getItem('doctor_id'))){
                    $(this).attr("selected", true);
                }
            });
            $("#doctorTabSer").find(".qty:last").attr('id', 'DocSerQty' + id);
            $("#doctorTabSer").find(".qty:last").attr('rel', id);
            $("#doctorTabSer").find(".discount:last").attr('id', 'DocSerDiscount' + id);
            $("#doctorTabSer").find(".discount:last").attr('rel', id);
            $("#doctorTabSer").find(".unit_price:last").attr('id', 'DocSerUnitPrice' + id);
            $("#doctorTabSer").find(".unit_price:last").attr('rel', id);
            $("#doctorTabSer").find(".total_price:last").attr('id', 'DocSerTotalPrice' + id);
            $("#doctorTabSer").find(".discount:last").val("");
            $("#doctorTabSer").find(".btnRemoveDiscountCheckOut:last").hide();
            $("#doctorTabSer").find(".PatientCheckoutTr:last").find("td .btnRemoveType").show();
            $(this).siblings(".btnRemoveType").show();
            $(this).hide();
            comboRefeshType();
            staffRefreshType();
            keyEventCheckout();
        });
        
        
        $(".btnRemoveType").click(function () {
            $(this).closest(".PatientCheckoutTr").remove();
            $("#doctorTabSer").find(".PatientCheckoutTr:last").find("td .btnAddType").show();
            if ($('#doctorTabSer .PatientCheckoutTr').length == 1) {
                $("#doctorTabSer").find(".PatientCheckoutTr:last").find("td .btnRemoveType").hide();
            }
            sortNuTableCheckOut();
            getTotalAmountPatientCheckOut();
            keyEventCheckout();
        });
        // clodse form service for quotation 
        
        $(".float").autoNumeric();    
        // get total amount of patient
        getTotalAmountPatientCheckOut();
        
        // show / hide patient information
        $("#btnHideShowPatientInfo<?php echo $tblNameRadom;?>").click(function(){
            var label  = $(this).find("span").text();
            var action = '';
            var img    = '<?php echo $this->webroot . 'img/button/'; ?>';
            if(label == 'Hide'){
                action = 'Show';
                $("#patient_info<?php echo $tblNameRadom;?>").hide(950);
                img += 'arrow-down.png';
            } else {
                action = 'Hide';
                $("#patient_info<?php echo $tblNameRadom;?>").show(950);
                img += 'arrow-up.png';
            }
            $(this).find("span").text(action);
            $(this).find("img").attr("src", img);
        });
        
    });

    function chcekBfSaveCheckout() {
        var formName = "#CheckoutFormDoctor";
        var validateBack = $(formName).validationEngine("validate");
        if (!validateBack) {
            return false;
        } else {
            return true;
        }
    }
</script>
<style type="text/css">
    .qty{
        text-align: center;
    }
</style>

<br />
<div style="display: none; float: right; width: 165px; text-align: right; cursor: pointer; margin-right: 10px;" id="btnHideShowPatientInfo<?php echo $tblNameRadom; ?>">
    [ <span>Hide</span>  <img alt="" align="absmiddle" style="width: 16px; height: 16px;" src="<?php echo $this->webroot . 'img/button/arrow-up.png'; ?>" /> ]
</div>
<?php echo $this->Form->create('Checkout', array('id' => 'CheckoutFormDoctor', 'url' => '/doctors/tabService/' . $this->params['pass'][0] . '/' . $this->params['pass'][1])); ?>
<input type="hidden" name="data[Patient][exchange_rate_id]" value="<?php /* echo getExchangeRateId(); */ echo 1; ?>" />
<input type="hidden" name="data[Patient][exchange_rate]" value="<?php echo $exchangeRate; ?>" />
<div style="display: none;">
    <fieldset id="patient_info<?php echo $tblNameRadom; ?>">
        <table class="table-dashbord" style="width: 100%;" cellspacing="3">
            <tr> 
                <td colspan="2">
                    <input type="hidden" id="PatientPatientGroupId" value="<?php echo $patient['Patient']['patient_group_id']; ?>"/>
                </td>
            </tr>
            <tr>
                <th style="width: 10%;"><?php echo TABLE_COMPANY; ?> <span class="red">*</span> :</th>
                <td>
                    <div class="inputContainer">
                        <select id="PatientCompanyId" class="classCompany validate[required]" name="data[Patient][company_id]" style="width:250px; height: 35px;">                    
                            <?php
                            foreach ($companies as $company) {
                                if ($company['Company']['id'] == 1) {
                                    echo '<option selected="selected" value="' . $company['Company']['id'] . '">' . $company['Company']['name'] . '</option>';
                                } else {
                                    echo '<option value="' . $company['Company']['id'] . '">' . $company['Company']['name'] . '</option>';
                                }
                            }
                            ?>
                        </select>                
                    </div>
                </td>
                <th><label for="OrderBranchId"><?php echo MENU_BRANCH; ?> <span class="red">*</span></label> :</th>
                <td>
                    <div class="inputContainer" style="width:100%">
                        <select name="data[Patient][branch_id]" id="OrderBranchId" class="validate[required]" style="width:250px;">
                            <?php
                            if(count($branches) != 1){
                            ?>
                            <option value="" com="" mcode="" currency="" symbol=""><?php echo INPUT_SELECT; ?></option>
                            <?php
                            }
                            foreach($branches AS $branch){
                            ?>
                            <option value="<?php echo $branch['Branch']['id']; ?>" com="<?php echo $branch['Branch']['company_id']; ?>" mcode="<?php echo $branch['ModuleCodeBranch']['so_code']; ?>" currency="<?php echo $branch['Branch']['currency_center_id']; ?>" symbol="<?php echo $branch['CurrencyCenter']['symbol']; ?>"><?php echo $branch['Branch']['name']; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </td>
            </tr>
        </table>
    </fieldset>
    <br />
    <?php
    if (!empty($salesOrders) || !empty($labos)) {
        ?>
        <span id="<?php echo $btnShowHide; ?>" class="btnShowHide" style="float: right;">[Hide]</span>
        <?php
    }
    ?>
</div>
<div id="addPatientCheckOut" style="display:none;">    
    <table id="doctorTabSer" class="table" cellspacing="0">
        <tr>
            <th class="first"><?php echo TABLE_NO; ?></th>
            <th><?php echo SECTION_SECTION; ?></th>
            <th><?php echo TABLE_SERVICE_NAME; ?></th>
            <th><?php echo DOCTOR_NAME; ?></th>
            <th><?php echo GENERAL_QTY; ?></th>
            <th><?php echo GENERAL_UNIT_PRICE; ?></th>
            <th><?php echo GENERAL_DISCOUNT . ' ($)'; ?></th>
            <th><?php echo GENERAL_TOTAL_PRICE; ?></th>            
            <th>&nbsp;</th>
        </tr>
        <tbody id="tableToModify">                   
            <?php 
            $checkValidate = "validate[required]";
            $index = 1;
            $totalAmount = 0;
            $querySerDefault = mysql_query("SELECT sec.name As sctName, srv.*, srvp.unit_price "
                                        . "FROM services As srv INNER JOIN sections As sec ON sec.id = srv.section_id INNER JOIN services_patient_group_details As srvp ON srvp.service_id = srv.id "
                                        . "WHERE srv.is_active = 1 AND srvp.is_active = 1 AND patient_group_id = 1 AND srv.is_default = 1");
            if(mysql_num_rows($querySerDefault)){
                $checkValidate = "";
                while ($rowSerDefault = mysql_fetch_array($querySerDefault)) {
                    ?>
                    <tr class="PatientCheckoutTr">
                        <td class="first serviceId">
                            <?php echo $index;?>
                        </td>            
                        <td>
                            <div class="inputContainer">                            
                                <?php
                                $query = mysql_query("SELECT sections.id, sections.name, section_companies.company_id  FROM `sections` INNER JOIN section_companies ON sections.id = section_companies.section_id 
                                                                WHERE sections.id IN (SELECT section_id FROM section_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = '" . $user['User']['id'] . "'))")
                                ?>
                                <select id="DocSecId<?php echo $index; ?>" rel="<?php echo $index; ?>" class="classSection validate[required]" name="data[Patient][section_id][]" style="width:160px;">
                                    <option value=""><?php echo SELECT_OPTION; ?></option>
                                    <?php
                                    while ($row = mysql_fetch_array($query)) {
                                        if($row['id'] == $rowSerDefault['section_id']){
                                            echo '<option selected="selected" class="' . $row['company_id'] . '" value="' . $row['id'] . '">' . $row['name'] . '</option>';
                                        }else{
                                            echo '<option class="' . $row['company_id'] . '" value="' . $row['id'] . '">' . $row['name'] . '</option>';
                                        }
                                    }
                                    ?>
                               </select>
                            </div>                    
                        </td>
                        <td>
                            <?php echo $this->Form->input('service_id', array('id' => 'DocServiceId' . $index, 'selected' => $rowSerDefault['id'], 'name' => 'data[Patient][service_id][]', 'empty' => SELECT_OPTION, 'label' => false, 'class' => 'patientCheckoutService validate[required]', 'style' => 'width:160px;', 'rel' => $index)); ?>
                        </td>
                        <td>
                            <select id="PatientOPDDocId<?php echo $index; ?>" style="width:130px;" name="data[Patient][doctor_id][]" class="classDoctor validate[required]">
                                <option value=""><?php echo SELECT_OPTION; ?></option>
                                <?php
                                foreach ($doctors as $doctor) {
                                    if($doctor['User']['id']==$user['User']['id']){
                                        echo '<option selected="selected" class="' . $doctor['Company']['id'] . '" value="' . $doctor['User']['id'] . '">' . $doctor['Employee']['name'] . '</option>';
                                    }else{
                                        echo '<option class="' . $doctor['Company']['id'] . '" value="' . $doctor['User']['id'] . '">' . $doctor['Employee']['name'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                            <?php echo $this->Form->text('qty', array('id' => 'DocSerQty' . $index, 'value' => 1, 'name' => 'data[Patient][qty][]', 'class' => 'qty integer validate[required]', 'style' => 'width:50px;', 'rel' => $index, 'autocomplete' => 'off')); ?> 
                        </td>
                        <td>
                            <?php echo $this->Form->text('unit_price', array('id' => 'DocSerUnitPrice' . $index, 'value' => number_format($rowSerDefault['unit_price'], 2), 'name' => 'data[Patient][unit_price][]', 'class' => 'unit_price float validate[required]', 'rel' => $index, 'autocomplete' => 'off', 'style' => 'width:100px;')); ?> 
                        </td>
                        <td>
                            <input type="hidden" name="discount_amount[]" value="0" />
                            <input type="hidden" name="discount_percent[]" value="0" />
                            <input type="text" id="DocSerDiscount<?php echo $index; ?>" class="float discount btnDiscountCheckOut" name="data[Patient][discount][]" style="width: 50px;" readonly="readonly" rel="<?php echo $index; ?>"/>
                            <img alt="Remove" src="<?php echo $this->webroot . 'img/button/cross.png'; ?>" class="btnRemoveDiscountCheckOut" align="absmiddle" style="cursor: pointer; display: none"  onmouseover="Tip('Remove')" />                                   
                        </td>
                        <td>
                            <?php echo $this->Form->text('total_price', array('id' => 'DocSerTotalPrice' . $index, 'value' => number_format($rowSerDefault['unit_price'], 2), 'name' => 'data[Patient][total_price][]', 'class' => 'total_price', 'style' => 'width:154px;', 'readonly' => true)); ?>
                        </td>
                        <td style="padding: 5px 5px 5px 5px !important;;">
                            <img alt="" src="<?php echo $this->webroot; ?>img/button/plus.png" class="btnAddType" style="cursor: pointer; display: none" />
                            <img alt="" src="<?php echo $this->webroot; ?>img/button/cross.png" class="btnRemoveType" style="cursor: pointer;" />
                        </td>
                    </tr>
                    <?php
                    $totalAmount += $rowSerDefault['unit_price'];
                    $index++;
                }
            }
            ?>
            <tr class="PatientCheckoutTr">
                <td class="first serviceId">
                    <?php echo $index;?>
                </td>            
                <td>
                    <div class="inputContainer">
                        <?php
                        $query = mysql_query("SELECT sections.id, sections.name, section_companies.company_id  FROM `sections` INNER JOIN section_companies ON sections.id = section_companies.section_id 
                                                        WHERE sections.id IN (SELECT section_id FROM section_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = '" . $user['User']['id'] . "'))")
                        ?>
                        <select id="DocSecId<?php echo $index; ?>" rel="<?php echo $index; ?>" class="classSection <?php echo $checkValidate;?>" name="data[Patient][section_id][]" style="width:160px;">
                            <option value=""><?php echo SELECT_OPTION; ?></option>
                            <?php
                            while ($row = mysql_fetch_array($query)) {
                                echo '<option class="' . $row['company_id'] . '" value="' . $row['id'] . '">' . $row['name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>                    
                </td>
                <td>
                    <?php echo $this->Form->input('service_id', array('id' => 'DocServiceId' . $index, 'name' => 'data[Patient][service_id][]', 'empty' => SELECT_OPTION, 'label' => false, 'class' => 'patientCheckoutService '.$checkValidate, 'style' => 'width:160px;', 'rel' => $index)); ?>
                </td>
                <td>
                    <select id="PatientOPDDocId<?php echo $index; ?>" style="width:130px;" name="data[Patient][doctor_id][]" class="classDoctor <?php echo $checkValidate;?>">
                        <option value=""><?php echo SELECT_OPTION; ?></option>
                        <?php
                        foreach ($doctors as $doctor) {
                            if($doctor['User']['id']==$user['User']['id'] && $checkValidate!=""){
                                echo '<option selected="selected" class="' . $doctor['Company']['id'] . '" value="' . $doctor['User']['id'] . '">' . $doctor['Employee']['name'] . '</option>';
                            }else{
                                echo '<option class="' . $doctor['Company']['id'] . '" value="' . $doctor['User']['id'] . '">' . $doctor['Employee']['name'] . '</option>';
                            }

                        }
                        ?>
                    </select>
                </td>
                <td>
                    <?php echo $this->Form->text('qty', array('id' => 'DocSerQty' . $index, 'name' => 'data[Patient][qty][]', 'class' => 'qty integer '.$checkValidate, 'style' => 'width:50px;', 'rel' => $index, 'autocomplete' => 'off')); ?> 
                </td>
                <td>
                    <?php echo $this->Form->text('unit_price', array('id' => 'DocSerUnitPrice' . $index, 'name' => 'data[Patient][unit_price][]', 'class' => 'unit_price float '.$checkValidate, 'rel' => $index, 'autocomplete' => 'off', 'style' => 'width:100px;')); ?> 
                </td>
                <td>
                    <input type="hidden" name="discount_amount[]" value="0" />
                    <input type="hidden" name="discount_percent[]" value="0" />
                    <input type="text" id="DocSerDiscount<?php echo $index; ?>" class="float discount btnDiscountCheckOut" name="data[Patient][discount][]" style="width: 50px;" readonly="readonly" rel="<?php echo $index; ?>"/>
                    <img alt="Remove" src="<?php echo $this->webroot . 'img/button/cross.png'; ?>" class="btnRemoveDiscountCheckOut" align="absmiddle" style="cursor: pointer; display: none"  onmouseover="Tip('Remove')" />                                   
                </td>
                <td>
                    <?php echo $this->Form->text('total_price', array('id' => 'DocSerTotalPrice' . $index, 'name' => 'data[Patient][total_price][]', 'class' => 'total_price', 'style' => 'width:154px;', 'readonly' => true)); ?>
                </td>
                <td style="padding: 5px 5px 5px 5px !important;;">
                    <img alt="" src="<?php echo $this->webroot; ?>img/button/plus.png" class="btnAddType" style="cursor: pointer;" />
                    <img alt="" src="<?php echo $this->webroot; ?>img/button/cross.png" class="btnRemoveType" style="cursor: pointer;display: none;" />
                </td>
            </tr>  
        </tbody>
        <tfoot>
            <tr>
                <td class="first" style="text-align: right;" colspan="7"><label for="PatientTotalAmount">Sub Total ($):</label></td>
                <td>
                    <input type="text" id="PatientTotalAmount" value="<?php echo number_format($totalAmount, 2);?>" class="validate[required]" readonly="readonly" style="width:154px; height: 30px;font-weight: bold;" name="data[Patient][total_amount]">
                </td>
            </tr>
        </tfoot>
    </table>    
</div>
<div class="clear"></div>
<div class="buttons">
    <button type="submit" class="positive saveCheckOut" >
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <span class="txtSavePatient"><?php echo ACTION_SAVE; ?></span>
    </button>
    <img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" class="loading" style="display: none;" />
</div>
<?php echo $this->Form->end(); ?>
