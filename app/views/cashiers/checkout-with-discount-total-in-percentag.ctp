<?php
include('includes/function.php');
$absolute_url = FULL_BASE_URL . Router::url("/", false);
$exchangeRate = getExchangeRate();
// Authentication
$this->element('check_access');
$allowProductDiscount = checkAccess($user['User']['id'], $this->params['controller'], 'discount');
?>
<script type="text/javascript" src="<?php echo $this->webroot . 'js/jquery.formatCurrency-1.4.0.min.js'; ?>"></script>

<script type="text/javascript">
    var selected;
    //    var numDiscount=0;           
    function checkDiscount(field, rules, i, options){
        if($("#discount_p").val()!=0 && $("#discount_d").val()!=0){
            return "<?php echo VALIDATION_ALLOW_1_METHOD_ONLY; ?>";
        }
    }
    function comboRefesh(){
        selected=new Array();
        $(".classSection").each(function(){
            if($(this).val()!=''){
                selected.push($(this).val());
            }
        });           
    }
    
    function sortNuTableCheckOut(){
        var sort = 1;
        $(".PatientCheckoutTr").each(function(){
            $(this).find("td:eq(0)").html(sort);
            sort++;
        });
    }
    
    function staffRefreshType() {                
        var i = Number($("#example").find(".serviceId:last").text())+1;        
        $("#example").find(".serviceId:last").text(i);                
        
    }
    function comboRefeshType() {                 
        $(".qty").each(function() {
            $("#example").find(".qty:last").val("");
        });   
        $(".unit_price").each(function() {
            $("#example").find(".unit_price:last").val("");
        });
        $(".total_price").each(function() {
            $("#example").find(".total_price:last").val("");
        });
        var i = 1;
        $(".patientCheckoutUnitPrice").each(function() {
            $("#example").find(".patientCheckoutUnitPrice:last").val("");            
            i++;
        });
    }
    
    function getTotalAmountPatientCheckOut(){
        var totalAmount      = 0;
        var totalAmountPaid = 0;
        var totalDiscountAll = parseFloat($("#PatientDiscountUs").val());
        var totalAmountPaid = parseFloat($("#PatientTotalAmountPaid").val());        
        totalDiscountAll     = totalDiscountAll !="" ? totalDiscountAll : 0;
        totalAmountPaid     = totalAmountPaid !="" ? totalAmountPaid : 0;        
        $(".total_price").each(function(){                   
            if($.trim($(this).val()) != '' || $(this).val() != undefined ){                
                totalAmount += Number($(this).val());
            }
        });
        
        if(isNaN(totalAmount)){
            $("#PatientTotalAmount").val(0.00);            
            $("#PatientDiscountUs").val(0);
            $("#PatientMarkUp").val(0.00);
            $("#PatientSubTotalAmount").val(0.00);            
        }else{            
            $("#PatientTotalAmount").val((totalAmount).toFixed(2));
            var total_sum = (totalAmount * totalDiscountAll) / 100;
            total_sum = total_sum+totalAmountPaid;
            $("#PatientSubTotalAmount").val((totalAmount - total_sum).toFixed(2));
        }
    }
    
    function addNewDiscountCheckOut(tr){
        $.ajax({
            type:   "POST",
            url:    "<?php echo $this->base . "/cashiers/discount"; ?>",
            beforeSend: function(){
                $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
            },
            success: function(msg){
                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                $("#dialog").html(msg).dialog({
                    title: '<?php echo 'Select Discount'; ?>',
                    resizable: false,
                    modal: true,
                    width: 800,
                    height: 550,
                    position:'center',
                    closeOnEscape: true,
                    open: function(event, ui){
                        $(".ui-dialog-buttonpane").show(); $(".ui-dialog-titlebar-close").show();
                    },
                    buttons: {
                        '<?php echo ACTION_OK; ?>': function() {
                            var discountTr = $("input[name='chkDiscount']:checked").closest("tr");
                            if(discountTr != "" && discountTr != undefined){
                                tr.find("input[name='discount_id[]']").val(discountTr.find("input[name='chkDiscount']").val());
                                tr.find("input[name='discount_amount[]']").val(discountTr.find("input[name='salesOrderDiscountAmount']").val());
                                tr.find("input[name='discount_percent[]']").val(discountTr.find("input[name='salesOrderDiscountPercent']").val());
                                tr.find("input[name='discount[]']").css("display", "inline");
                                tr.find(".btnRemoveDiscountCheckOut").css("display", "inline");
                                var discountAmount      = discountTr.find("input[name='salesOrderDiscountAmount']").val();
                                var discountPercent     = discountTr.find("input[name='salesOrderDiscountPercent']").val();
                                var calTotalPrice       = tr.find("input[name='data[Patient][total_price][]']").val();                                
                                //Calculate Discount
                                var discount = 0;
                                if(discountAmount != ''){
                                    discount = parseFloat(discountAmount);
                                }else if(discountPercent != ''){
                                    discount = (parseFloat(discountPercent) * calTotalPrice) / 100;
                                }
                                if(discount>=0){
                                    tr.find("input[name='discount[]']").val(discount.toFixed(2));
                                }else{
                                    tr.find("input[name='discount[]']").val(discount.toFixed(2));
                                }
                                
                                var id = tr.find("input[name='discount[]']").attr('rel');                                 
                                var totalPrice = (Number($("#CheckoutUnitPrice"+id).val()) * Number($("#CheckoutQty"+id).val())) - discount;                                
                                $('#CheckoutTotalPrice'+id).val(Number(totalPrice).toFixed(2));
                                getTotalAmountPatientCheckOut();
                            }
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
    }
    function removeDiscountCheckOut(tr){
        var discount = tr.find("input[name='discount[]']").val();
        var id = tr.find("input[name='discount[]']").attr('rel');                                 
        var totalPrice = Number($("#CheckoutUnitPrice"+id).val()) * Number($("#CheckoutQty"+id).val());
        $('#CheckoutTotalPrice'+id).val(Number(totalPrice).toFixed(2));
        
        tr.find("input[name='discount_id[]']").val("");
        tr.find("input[name='discount_amount[]']").val(0.00);
        tr.find("input[name='discount_percent[]']").val(0.00);
        tr.find("input[name='discount[]']").val("");
        tr.find(".btnRemoveDiscountCheckOut").css("display", "none");
        getTotalAmountPatientCheckOut();
    }
    
    $(document).ready(function(){
    
        // hide coa that not belong to the company
        $(".sales_order_coa_id option").show();
        $(".sales_order_coa_id option").each(function(){            
//            if($(this).attr("company_id")){
//                companyId=$(this).attr("company_id").split(",");
//                if(companyId.indexOf($("#PatientCompanyId").val())==-1){
//                    $(this).hide();
//                }
//            }
            if($(this).attr("chart_account_type_name")!='Accounts Receivable' && $(this).val() != ""){
                $(this).attr("disabled", 'disabled');
            }
        });
        
         // for sort section in company
        $(".classCompany").change(function(){
            if($("#PatientCompanyId").val()!='' && $("#PatientPatientGroupId").val()!="" && $("#CheckoutCompanyInsuranceId").val()!=""){
                $("#addPatientCheckOut").show();
                $(".classSection").closest("tr").find("td .classSection").val('');
                $(".classSection").closest("tr").find("td .classSection option[class!='']").hide();
                $(".classSection").closest("tr").find("td .classSection option[class='"  + $("#PatientCompanyId").val() + "']").show();
            }else{                
                $(".classSection").closest("tr").find("td .classSection option[class!='']").show();
                $("#addPatientCheckOut").hide();
            }       
            comboRefesh();
        });
        $("#CheckoutCompanyInsuranceId").change(function(){
            if($("#PatientCompanyId").val()!='' && $("#PatientPatientGroupId").val()!="" && $("#CheckoutCompanyInsuranceId").val()!=""){
                $("#addPatientCheckOut").show();
                $(".classSection").closest("tr").find("td .classSection").val('');
                $(".classSection").closest("tr").find("td .classSection option[class!='']").hide();
                $(".classSection").closest("tr").find("td .classSection option[class='"  + $("#PatientCompanyId").val() + "']").show();
            }else{
                $("#addPatientCheckOut").hide();
            }
        });
        
        $(".classSection").change(function(){
            $("#ServiceCompanyId").closest("tr").find("td .classCompany").val($(this).find("option:selected").attr("class"));            
            var serId = this.id;                             
            var pateintGroup = $("#PatientPatientGroupId").val();
            var companyInsuranceId = $("#CheckoutCompanyInsuranceId").val();
            $.ajax({
                type: "POST",
                url: '<?php echo $absolute_url . $this->params['controller']; ?>/getService/' + $(this).val(),
                data: "",
                success: function(msg){
                    
                    if(serId.length > 16){
                        var getSerId = serId.substr(16, serId.length)*1;                        
                        var values = [];
                        $('select.patientCheckoutService').each(function(){
                            values.push($(this).val());
                        });
                        
                        $("#CheckoutServiceId"+getSerId).html(msg).find("option").each(function(){
                            var s = $(this);
                            $.each(values,function(v,i){
                                if(s.val()== i && s.val() != ''){
                                    s.hide();
                                }
                            })
                        });                  
                        var service = $('select.patientCheckoutService');                                                
                        $("#CheckoutServiceId"+getSerId).live('change',function(){
                            
                            values = [];
                            service.each(function(){
                                if($(this).val()!=''){
                                    values.push($(this).val());
                                }
                            });
                            service.find('option').show().each(function(){
                                if($.inArray(this.value, values) != -1){
                                    $(this).hide();
                                }
                            });
                            var serviceId = $(this).val();
                            $.ajax({
                                type: "POST",
                                url: '<?php echo $absolute_url . $this->params['controller']; ?>/getServicePrice/' + $(this).val() + '/' + pateintGroup + '/' + companyInsuranceId,
                                data:"",
                                success: function(msg){                                    
                                    var unitPrice=msg.split('/')[0];
                                    $('#CheckoutQty'+getSerId).val(1);                                    
                                    $('#CheckoutUnitPrice'+getSerId).val(Number(unitPrice).toFixed(2));
                                    $('#CheckoutTotalPrice'+getSerId).val(Number(unitPrice).toFixed(2));
                                    $('#CheckoutUnitPrice'+getSerId).attr('rel', serviceId);
                                    $('#CheckoutUnitPrice'+getSerId).attr('index', getSerId);
                                    getTotalAmountPatientCheckOut();
                                }
                            });
                        });
                        
                    } else{
                        
                        var getSerId = serId.substr(16, serId.length)*1;                                                
                        var values = [];
                        $('select.patientCheckoutService').each(function(){
                            values.push($(this).val());
                        });           
                        
                        $("#CheckoutServiceId").html(msg).find("option").each(function(){
                            var s = $(this);
                            $.each(values,function(v,i){
                                if(s.val()== i && s.val() != ''){
                                    s.hide();
                                }
                            })
                        });                  
                        var service = $('select.patientCheckoutService');                                                
                        $("#CheckoutServiceId").live('change',function(){                            
                            values = [];
                            service.each(function(){
                                if($(this).val()!=''){
                                    values.push($(this).val());
                                }
                            });
                            service.find('option').show().each(function(){
                                if($.inArray(this.value, values) != -1){
                                    $(this).hide();
                                }
                            });
                            var serviceId = $(this).val();
                            $.ajax({
                                type: "POST",
                                url: '<?php echo $absolute_url . $this->params['controller']; ?>/getServicePrice/' + $(this).val() + '/' + pateintGroup + '/' + companyInsuranceId,
                                data:"",
                                success: function(msg){
                                    var unitPrice=msg.split('/')[0];
                                    $('#CheckoutQty').val(1);
                                    $('#CheckoutUnitPrice').val(Number(unitPrice).toFixed(2));
                                    $('#CheckoutTotalPrice').val(Number(unitPrice).toFixed(2));
                                    $('#CheckoutUnitPrice').attr('rel', serviceId);
                                    $('#CheckoutUnitPrice').attr('index', '');
                                    getTotalAmountPatientCheckOut();
                                }
                            });                              
                        });                        
                    }
                }
            });
                
        });
    
    
        $(".qty").blur(function(){
            var qtyId = this.id; 
            var id = $(this).attr('rel');            
            if($(this).val() == ""){                                    
                $(this).val('1');                                
                var totalPrice = Number($("#CheckoutUnitPrice"+id).val())*$(this).val();
                $('#CheckoutTotalPrice'+id).val(Number(totalPrice).toFixed(2));
                getTotalAmountPatientCheckOut();
            }
        });
        $(".qty").live('click',function(){  
            var id = $(this).attr('rel');            
            $("#CheckoutQty"+id).val("");                                 
        });
        $(".qty").live('keyup',function(){                                
            var id = $(this).attr('rel');
            var discount = Number($("#CheckoutDiscount"+id).val());
            var totalPrice = (Number($("#CheckoutUnitPrice"+id).val())*$(this).val())-discount;            
            $('#CheckoutTotalPrice'+id).val(Number(totalPrice).toFixed(2));
            getTotalAmountPatientCheckOut();
        });
        
        
        
        $("#PatientDiscountUs").blur(function(){
            if($(this).val() == ""){                                    
                $(this).val('0.00');
                getTotalAmountPatientCheckOut();
            }
        });
        $("#PatientDiscountUs").live('click',function(){              
            $("#PatientDiscountUs").val("");
        });
        
        $("#PatientTotalAmountPaid").blur(function(){
            if($(this).val() == ""){                                    
                $(this).val('0.00');
                getTotalAmountPatientCheckOut();
            }
        });
        $("#PatientTotalAmountPaid").live('click',function(){              
            $("#PatientTotalAmountPaid").val("");
        });
        
        $("#PatientDiscountUs, #PatientTotalAmountPaid").live('keyup',function(){            
            getTotalAmountPatientCheckOut();
        });
    
    
        $(".btnDiscountCheckOut").click(function(){
            addNewDiscountCheckOut($(this).closest("tr"));
        });
        $(".btnRemoveDiscountCheckOut").click(function(){
            removeDiscountCheckOut($(this).closest("tr"));
        });
    
        // form add new service for quotation
        $(".btnAddType").click(function() {
            var id = "";
            $("#example").find(".PatientCheckoutTr:last").clone(true).appendTo("#tableToModify");
            
            id = $("#example").find(".classSection:last").attr('rel');            
            if(id == ""){
                var id = $("#example").find(".serviceId:last").text();
            }else {
                id++; 
            }
            
            // update id in tr : last            
            
            $("#example").find(".classSection:last").attr('id', 'ServiceSectionId'+id);
            $("#example").find(".classSection:last").attr('rel', id);
            $("#example").find(".patientCheckoutService:last").attr('id', 'CheckoutServiceId'+id);
            $("#example").find(".classDoctor:last").attr('id', 'PatientIpdDoctorId'+id);
            $("#example").find(".qty:last").attr('id', 'CheckoutQty'+id);
            $("#example").find(".qty:last").attr('rel', id);            
            $("#example").find(".discount:last").attr('id', 'CheckoutDiscount'+id);
            $("#example").find(".discount:last").attr('rel', id);            
            $("#example").find(".unit_price:last").attr('id', 'CheckoutUnitPrice'+id);
            $("#example").find(".total_price:last").attr('id', 'CheckoutTotalPrice'+id);
            
            $("#example").find(".discount:last").val("");
            $("#example").find(".btnRemoveDiscountCheckOut:last").hide();
            
            $("#example").find(".PatientCheckoutTr:last").find("td .btnRemoveType").show();
            $(this).siblings(".btnRemoveType").show();
            $(this).hide(); 
            comboRefeshType();
            staffRefreshType();
           
        });
        $(".btnRemoveType").click(function() {
            $(this).closest(".PatientCheckoutTr").remove();
            $("#example").find(".PatientCheckoutTr:last").find("td .btnAddType").show();            
            if ($('#example .PatientCheckoutTr').length == 1) {
                $("#example").find(".PatientCheckoutTr:last").find("td .btnRemoveType").hide();
            }            
            sortNuTableCheckOut();
            getTotalAmountPatientCheckOut();
        });        
        // clodse form service for quotation
    
        $("#btnSubmit").click(function(){
            var isFormValidated=$("#CheckoutForm").validationEngine('validate');
            if(!isFormValidated){
                return false;
            }else{                           
                var exchange_rate=$("#exchange_rate").text();
                var paid=(Number($("#total_amount_r").val())/exchange_rate)+parseFloat(Number($("#total_amount_d").val()));
                var amount_d = parseFloat($("#treatment_fee").val());  
                var discount = parseFloat($("#discount_d").val());     
                if(paid>=(amount_d-discount)){ 
                    var btnInvoiceReceipt=$("#dialog").html();
                    $("#dialog").html('<p style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                    $("#dialog").dialog({
                        title: 'Saving',
                        resizable: false,
                        modal: true,
                        close: function() {
                            window.open("<?php echo $absolute_url; ?>dashboards/cashier/","_self");
                        },
                        buttons: {
                            Ok: function() {
                                $( this ).dialog( "close" );
                            }
                        }
                    });                                   
                    var url = '<?php echo $absolute_url . $this->params['controller']; ?>/checkout/<?php echo $this->params['pass'][0]; ?>';
                    var post = $('#CheckoutForm').serialize();
                    $.post(url,post,function(rs){
                        if(rs.indexOf('success')!=-1){
                            rs=rs.split("/");
                            var invoiceId=rs[1];                                              
                            $("#dialog").html(btnInvoiceReceipt);
                            $("#dialog").dialog({
                                title: '<?php echo ACTION_PRINT; ?>',
                                resizable: false,
                                modal: true,
                                close: function() {
                                    window.open("<?php echo $absolute_url; ?>dashboards/cashier/","_self");
                                },
                                buttons: {
                                    Ok: function() {
                                        $( this ).dialog( "close" );
                                    }
                                }
                            });
                            $("#invoice").load("<?php echo $absolute_url; ?>cashiers/printInvoice/" + invoiceId);
                            $("#btnPrintInvoice").click(function(){
                                w=window.open();
                                w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                                w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                                w.document.write($("#invoice").html());
                                w.document.close();
                                w.print();
                                w.close();
                            });
                            $("#receipt").load("<?php echo $absolute_url; ?>cashiers/printReceipt/" + invoiceId);
                            $("#btnPrintReceipt").click(function(){
                                w=window.open();
                                w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                                w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                                w.document.write($("#receipt").html());
                                w.document.close();
                                w.print();
                                w.close();
                            });
                        }
                    });
                }else{
                    alert("ទឹកប្រាក់ដែលបានបង់មិនទាន់គ្រប់ចំនួន!");
                    return false;
                }
            }
        });
    });
</script>
<style type="text/css">
    .qty{
        text-align: center;
    }
    table td{
        padding: 5px 5px 5px 20px !important;
    }
</style>
<h1 class="title"><?php __(GENERAL_INVOICE); ?></h1>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">       
        <a href="<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/dashboard">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>    
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('Checkout', array('id' => 'CheckoutForm', 'url' => '/cashiers/checkout/' . $this->params['pass'][0])); ?>
<table class="info">
    <tr>
        <th style="width: 10%;"><?php __(PATIENT_CODE); ?></th>
        <td style="width: 40%;">: <?php echo $patient['Patient']['patient_code']; ?></td>
        <th style="width: 10%;"><?php __(TABLE_DOB); ?></th>
        <td style="width: 40%;">: 
            <?php echo date("d/m/Y", strtotime($patient['Patient']['dob'])); ?>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            echo TABLE_AGE . ': ';
            $then_ts = strtotime($patient['Patient']['dob']);
            $then_year = date('Y', $then_ts);
            $age = date('Y') - $then_year;
            if (strtotime('+' . $age . ' years', $then_ts) > time())
                $age--;

            if ($age == 0) {
                $then_year = date('m', $then_ts);
                $month = date('m') - $then_year;
                if (strtotime('+' . $month . ' month', $then_ts) > time())
                    $month--;
                echo $month . ' ' . GENERAL_MONTH;
            }else {
                echo $age . ' ' . GENERAL_YEAR_OLD;
            }
            ?> 
        </td>
    </tr>
    <tr>
        <th><?php __(PATIENT_NAME); ?></th>
        <td>: <?php echo $patient['Patient']['patient_name']; ?></td>
        <th><?php __(TABLE_SEX); ?></th>
        <td>: 
            <?php
            if ($patient['Patient']['sex'] == "F") {
                echo GENERAL_FEMALE;
            } else {
                echo GENERAL_MALE;
            }
            ?>
        </td>        
    </tr>   
    <tr>
        <th><?php __(TABLE_TELEPHONE); ?></th>
        <td>: <?php echo $patient['Patient']['telephone']; ?></td>
        <th><?php __(TABLE_EMAIL); ?></th>
        <td>: <?php echo $patient['Patient']['email']; ?></td>        
    </tr>
    <tr>        
        <th><?php __(TABLE_ADDRESS); ?></th>
        <td>: 
            <?php echo $patient['Patient']['address']; ?>
            <?php
            $query = mysql_query("SELECT name FROM patient_locations WHERE id=" . $patient['Patient']['location_id']);
            while ($row = mysql_fetch_array($query)) {
                echo $row['name'];
            }
            ?>
        </td>
        <th><?php __(TABLE_NATIONALITY); ?></th>
        <td>: 
            <?php
            if ($patient['Patient']['patient_group_id'] != "") {
                $query = mysql_query("SELECT name FROM patient_groups WHERE id=" . $patient['Patient']['patient_group_id']);
                while ($row = mysql_fetch_array($query)) {
                    if ($patient['Patient']['patient_group_id'] == 1) {
                        echo $row['name'];
                    } else {
                        echo $row['name'] . '&nbsp;&nbsp;(' . $patient['Nationality']['name'] . ')';
                    }
                }
            } else {
                echo $patient['Nationality']['name'];
            }
            ?>
            <input type="hidden" id="PatientPatientGroupId" value="<?php echo $patient['Patient']['patient_group_id'];?>"/>
        </td>
    </tr>
    <tr>
        <th><label for="PatientCompanyId"><?php echo TABLE_COMPANY; ?> <span class="red">*</span> :</label></th>
        <td>
            <div class="inputContainer">
                <select id="PatientCompanyId" class="classCompany validate[required]" name="data[Patient][company_id]" style="width:200px;height: 35px;">
                    <option value=""><?php echo SELECT_OPTION;?></option>
                    <?php 
                    foreach ($companies as $company) { 
                        echo '<option value="'.$company['Company']['id'].'">'.$company['Company']['name'].'</option>';
                    }
                    ?>
                </select>                
            </div>
        </td>
        <th><?php __(TABLE_BILL_PAID_BY); ?></th>
        <td>
            :<?php echo $patient['PatientBillType']['name']; ?>            
        </td>       
    </tr>
    <?php if($patient['PatientBillType']['id']==3){ ?>
    <tr>
        <th><?php echo '<label for="CheckoutCompanyInsuranceId">'.TABLE_COMPANY_INSURANCE_NAME.' <span class="red">*</span> :</label>';?></th>
        <td colspan="3"><?php echo $this->Form->input('company_insurance_id', array('empty' => SELECT_OPTION, 'label' => false, 'class' => 'validate[required]', 'style' => 'width:200px;height: 35px;'));?></td>
    </tr>
    <?php }?>
</table>
<br />
<div id="addPatientCheckOut" style="display:none;">    
    <table id="example" class="table" cellspacing="0">
        <tr>
            <th class="first"><?php echo TABLE_NO; ?></th>
            <th><?php echo SECTION_SECTION; ?></th>
            <th><?php echo TABLE_SERVICE_NAME; ?></th>
            <th><?php echo DOCTOR_NAME; ?></th>
            <th><?php echo GENERAL_QTY; ?></th>
            <th><?php echo GENERAL_UNIT_PRICE; ?></th>
            <th><?php echo GENERAL_DISCOUNT; ?></th>
            <th><?php echo GENERAL_TOTAL_PRICE; ?></th>            
            <th>&nbsp;</th>
        </tr>
        <tbody id="tableToModify">
            <tr class="PatientCheckoutTr">
                <td class="first serviceId">1</td>            
                <td>
                    <div class="inputContainer">
                        <?php
                        $query = mysql_query("SELECT sections.id, sections.name, section_companies.company_id  FROM `sections` INNER JOIN section_companies ON sections.id = section_companies.section_id 
                                                    WHERE sections.id IN (SELECT section_id FROM section_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = '" . $user['User']['id'] . "'))")
                        ?>
                        <select id="ServiceSectionId" rel="" class="classSection validate[required]" name="data[Service][section_id]" style="width:160px;">
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
                    <?php echo $this->Form->input('service_id', array('name' => 'data[Patient][service_id][]', 'empty' => SELECT_OPTION, 'label' => false, 'class' => 'patientCheckoutService validate[required]', 'style' => 'width:160px;')); ?>
                </td>
                <td>
                    <select id="PatientIpdDoctorId" style="width:130px;" name="data[PatientIpd][doctor_id]" class="classDoctor validate[required]">
                        <option value=""><?php echo SELECT_OPTION;?></option>
                        <?php 
                        foreach ($doctors as $doctor) {
                            echo '<option class="'.$doctor['Company']['id'].'" value="'.$doctor['User']['id'].'">'.$doctor['Employee']['name'].'</option>';
                        }
                        ?>
                    </select>
                </td>
                <td>
                    <?php echo $this->Form->text('qty', array('name' => 'data[Patient][qty][]', 'class' => 'qty integer validate[required]', 'style' => 'width:50px;', 'rel' => "")); ?> 
                </td>
                <td>
                    <?php echo $this->Form->text('unit_price', array('name' => 'data[Patient][unit_price][]', 'class' => 'unit_price float validate[required]', 'readonly' => true, 'style' => 'width:100px;')); ?> 
                </td>
                <td>
                    <?php                    
                    if ($allowProductDiscount) {
                        ?>
                        <input type="text" id="CheckoutDiscount" class="float discount btnDiscountCheckOut" name="discount[]" style="width: 50px;" readonly="readonly" rel=""/>
                        <img alt="Remove" src="<?php echo $this->webroot . 'img/button/cross.png'; ?>" class="btnRemoveDiscountCheckOut" align="absmiddle" style="cursor: pointer; display: none"  onmouseover="Tip('Remove')" />
                        <?php
                    }else{
                    ?>
                        <input type="hidden" id="CheckoutDiscount" class="float discount btnDiscountCheckOut" name="discount[]" style="width: 50px;" readonly="readonly" rel=""/>
                    <?php
                    }
                    ?>                    
                </td>
                <td>
                    <?php echo $this->Form->text('total_price', array('name' => 'data[Patient][total_price][]', 'class' => 'total_price', 'style' => 'width:120px;', 'readonly' => true)); ?>
                </td>
                <td style="padding: 5px 5px 5px 5px !important;;"><img alt="" src="<?php echo $this->webroot; ?>img/button/plus.png" class="btnAddType" style="cursor: pointer;" />
                    <img alt="" src="<?php echo $this->webroot; ?>img/button/cross.png" class="btnRemoveType" style="cursor: pointer;display: none;" />
                </td>
            </tr>  
        </tbody>
        <tr>
            <td class="first" style="text-align: right;" colspan="7"><label for="PatientTotalAmount">Sub Total ($):</label></td>
            <td>
                <input type="text" id="PatientTotalAmount" value="0.00" class="validate[required]" readonly="readonly" style="width:120px; height: 30px;font-weight: bold;" name="data[Patient][total_amount]">
            </td>
        </tr>
        <tr>
            <td class="first" style="text-align: right;" colspan="7"><label for="PatientDiscountUs">Discount (%):</label></td>
            <td>
                <input type="text" id="PatientDiscountUs" value="0.00" class="float" style="width:120px; height: 30px;font-weight: bold;" name="data[Patient][discount_us]">
            </td>
        </tr>
        <tr>
            <td class="first" style="text-align: right;" colspan="7"><label for="PatientTotalAmountPaid">Total Paid ($):</label></td>
            <td>
                <input type="text" id="PatientTotalAmountPaid" value="0.00" class="float validate[required]" style="width:120px; height: 30px;font-weight: bold;" name="data[Patient][total_amount_paid]">
            </td>
        </tr>
        <tr>
            <td class="first" style="text-align: right;" colspan="7"><label for="PatientSubTotalAmount">Balance ($):</label></td>
            <td>
                <input type="text" id="PatientSubTotalAmount" value="0.00" style="width:120px; height: 30px;font-weight: bold;" class="validate[required]" readonly="readonly" name="data[Patient][sub_total_amount]">                    
            </td>
        </tr>
        <tr>
            <td class="first" style="text-align: right;" colspan="7"><label for="PatientChartAccountId">A/R <span class="red">*</span> :</label></td>
            <td>
                <div class="inputContainer">                            
                    <?php 
                    $filter="AND chart_account_type_id IN (2)";
                    $query = array();
                    ?>
                    <select id="PatientChartAccountId" name="data[Patient][chart_account_id]" class="sales_order_coa_id validate[required]" style="width:125px; height: 30px;">
                        <option value=""><?php echo SELECT_OPTION; ?></option>
                        <?php
                        $query[0]=mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE is_active=1 ".$filter." ORDER BY account_codes");
                        while($data[0]=mysql_fetch_array($query[0])){
                            $queryIsNotLastChild=mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=".$data[0]['id']);
                        ?>
                        <option value="<?php echo $data[0]['id']; ?>" chart_account_type_name="<?php echo $data[0]['chart_account_type_name']; ?>" company_id="<?php echo $data[0]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild)?'disabled="disabled"':''; ?> <?php echo $data[0]['id']==$arAccountId?'selected="selected"':''; ?>><?php echo $data[0]['name']; ?></option>
                            <?php
                            $query[1]=mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=".$data[0]['id']." AND is_active=1 ".$filter." ORDER BY account_codes");
                            while($data[1]=mysql_fetch_array($query[1])){
                                $queryIsNotLastChild=mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=".$data[1]['id']);
                            ?>
                            <option value="<?php echo $data[1]['id']; ?>" chart_account_type_name="<?php echo $data[1]['chart_account_type_name']; ?>" company_id="<?php echo $data[1]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild)?'disabled="disabled"':''; ?> <?php echo $data[1]['id']==$arAccountId?'selected="selected"':''; ?> style="padding-left: 25px;"><?php echo $data[1]['name']; ?></option>
                                <?php
                                $query[2]=mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=".$data[1]['id']." AND is_active=1 ".$filter." ORDER BY account_codes");
                                while($data[2]=mysql_fetch_array($query[2])){
                                    $queryIsNotLastChild=mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=".$data[2]['id']);
                                ?>
                                <option value="<?php echo $data[2]['id']; ?>" chart_account_type_name="<?php echo $data[2]['chart_account_type_name']; ?>" company_id="<?php echo $data[2]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild)?'disabled="disabled"':''; ?> <?php echo $data[2]['id']==$arAccountId?'selected="selected"':''; ?> style="padding-left: 50px;"><?php echo $data[2]['name']; ?></option>
                                    <?php
                                    $query[3]=mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=".$data[2]['id']." AND is_active=1 ".$filter." ORDER BY account_codes");
                                    while($data[3]=mysql_fetch_array($query[3])){
                                        $queryIsNotLastChild=mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=".$data[3]['id']);
                                    ?>
                                    <option value="<?php echo $data[3]['id']; ?>" chart_account_type_name="<?php echo $data[3]['chart_account_type_name']; ?>" company_id="<?php echo $data[3]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild)?'disabled="disabled"':''; ?> <?php echo $data[3]['id']==$arAccountId?'selected="selected"':''; ?> style="padding-left: 75px;"><?php echo $data[3]['name']; ?></option>
                                        <?php
                                        $query[4]=mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=".$data[3]['id']." AND is_active=1 ".$filter." ORDER BY account_codes");
                                        while($data[4]=mysql_fetch_array($query[4])){
                                            $queryIsNotLastChild=mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=".$data[4]['id']);
                                        ?>
                                        <option value="<?php echo $data[4]['id']; ?>" chart_account_type_name="<?php echo $data[4]['chart_account_type_name']; ?>" company_id="<?php echo $data[4]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild)?'disabled="disabled"':''; ?> <?php echo $data[4]['id']==$arAccountId?'selected="selected"':''; ?> style="padding-left: 100px;"><?php echo $data[4]['name']; ?></option>
                                            <?php
                                            $query[5]=mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=".$data[4]['id']." AND is_active=1 ".$filter." ORDER BY account_codes");
                                            while($data[5]=mysql_fetch_array($query[5])){
                                                $queryIsNotLastChild=mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=".$data[5]['id']);
                                            ?>
                                            <option value="<?php echo $data[5]['id']; ?>" chart_account_type_name="<?php echo $data[5]['chart_account_type_name']; ?>" company_id="<?php echo $data[5]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild)?'disabled="disabled"':''; ?> <?php echo $data[5]['id']==$arAccountId?'selected="selected"':''; ?> style="padding-left: 125px;"><?php echo $data[5]['name']; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </td>
        </tr>
    </table>
    <br/>
    <div class="buttons">
        <button type="button" id="btnSubmit" class="positive">
            <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
            <?php echo ACTION_SAVE; ?>
        </button>
    </div>
</div>
<div id="dialog" title="" style="display: none;">
    <br />
    <center>
        <div class="buttons" style="display: inline-block;">
            <button type="button" id="btnPrintInvoice" class="positive">
                <img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/>
                <?php echo GENERAL_INVOICE; ?>
            </button>

            <button type="button" id="btnPrintReceipt" class="positive">
                <img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/>
                <?php echo GENERAL_RECEIPT; ?>
            </button>
        </div>
    </center>
</div>
<!--
<div id="dialog2" title="" style="display: none;"></div>-->
<div id="invoice" style="display: none;"></div>
<div id="receipt" style="display: none;"></div>
