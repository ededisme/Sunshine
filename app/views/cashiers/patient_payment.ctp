<?php
include('includes/function.php');
$absolute_url = FULL_BASE_URL . Router::url("/", false);
$exchangeRate = getExchangeRate();
// Authentication
$this->element('check_access');
$allowProductDiscount = checkAccess($user['User']['id'], $this->params['controller'], 'discount');
?>
<script type="text/javascript" src="<?php echo $this->webroot . 'js/jquery.formatCurrency-1.4.0.min.js'; ?>"></script>

<style type="text/css">
    .input{
        float:left;
    }
</style>
<script type="text/javascript">
    var selected;  
    function comboRefesh(){
        selected=new Array();
        $(".classSection").each(function(){
            if($(this).val()!=''){
                selected.push($(this).val());
            }
        });           
    }
    
    function sortNuTablePatientIpd(){
        var sort = 1;
        $(".PatientPatientIpdTr").each(function(){
            $(this).find("td:eq(0)").html(sort);
            sort++;
        });
    }
    
    function staffRefreshType() {                
        var i = Number($("#example").find(".serviceId:last").text())+1;        
        $("#example").find(".serviceId:last").text(i);                
        
    }
    function comboRefeshType() {
        $(".classSection").each(function() {
            $("#example").find(".classSection:last").val("");
        });
        $(".patientPatientIpdService").each(function() {
            $("#example").find(".patientPatientIpdService:last").val("");
        });
        $(".classDoctor").each(function() {
            $("#example").find(".classDoctor:last").val("");
        });
        $(".checkout_qty").each(function() {
            $("#example").find(".checkout_qty:last").val("");
        });   
        $(".checkout_unit_price").each(function() {
            $("#example").find(".checkout_unit_price:last").val("");
        });
        $(".checkout_total_price").each(function() {
            $("#example").find(".checkout_total_price:last").val("");
        });
        var i = 1;
        $(".patientPatientIpdUnitPrice").each(function() {
            $("#example").find(".patientPatientIpdUnitPrice:last").val("");            
            i++;
        });
    }
    
    // add new discount total
    
    function addNewDiscountTotal(tr){
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
                    width: 450,
                    height: 180,
                    position:'center',
                    closeOnEscape: true,
                    open: function(event, ui){
                        $(".ui-dialog-buttonpane").show(); $(".ui-dialog-titlebar-close").show();
                    },
                    buttons: {
                        '<?php echo ACTION_OK; ?>': function() {
                            var discountTr = $("input[name='chkDiscount']:checked").closest("tr");
                            if(discountTr != "" && discountTr != undefined){                                
                                tr.find("input[name='data[Patient][total_discount]']").css("display", "inline");
                                tr.find(".btnRemoveDiscountTotal").css("display", "inline");
                                // var discountAmount      = discountTr.find("input[name='patientDiscountAmount']").val();
                                // var discountPercent     = discountTr.find("input[name='patientDiscountPercent']").val();
								var discountAmount     = $("#inputInvoiceDisAmt").val();
                            	var discountPercent    = $("#inputInvoiceDisPer").val();

                                var calTotalPrice       = Number($("#PatientTotalAmount").val());
                                //Calculate Discount
                                var discount = 0;
                                // if(discountAmount != ''){
                                //     discount = parseFloat(discountAmount);
                                // }else if(discountPercent != ''){
                                //     discount = (parseFloat(discountPercent) * calTotalPrice) / 100;
                                // }

								if (discountPercent>0) {
									$("#LabelDisPercent").html('('+discountPercent+'%)');
									discount = (parseFloat(discountPercent) * calTotalPrice) / 100;
								}
								if (discountAmount>0) {
									$("#LabelDisPercent").html('');
									discount = parseFloat(discountAmount);
								}

                                if(discount>=0){
                                    tr.find("input[name='data[Patient][total_discount]']").val(discount.toFixed(2));
									tr.find("input[name='data[Patient][total_discount_per]']").val(parseFloat(discountPercent));
                                }else{
                                    tr.find("input[name='data[Patient][total_discount]']").val(discount.toFixed(2));
									tr.find("input[name='data[Patient][total_discount_per]']").val(parseFloat(discountPercent));
                                }
                                getTotalAmountPatientPatientIpd();
                            }
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
    }
    function removeDiscountTotal(tr){
        var discount = tr.find("input[name='data[Patient][total_discount]']").val();        
        tr.find("input[name='data[Patient][total_discount]']").val("0.00");        
        tr.find(".btnRemoveDiscountTotal").css("display", "none");
        getTotalAmountPatientPatientIpd();
    }
    
    function getTotalAmountPatientPatientIpd(){
        var totalAmount      = 0;
        var totalAmountPaid = 0;
        var totalDiscountAll = parseFloat($("#PatientDiscountTotal").val());
        var totalAmountPaid = parseFloat($("#PatientTotalAmountPaid").val());        
        totalDiscountAll     = totalDiscountAll !="" ? totalDiscountAll : 0;
        totalAmountPaid     = totalAmountPaid !="" ? totalAmountPaid : 0;        
        $(".checkout_total_price").each(function(){                   
            if($.trim($(this).val()) != '' || $(this).val() != undefined ){                
                totalAmount += Number($(this).val());
            }
        });
        if(isNaN(totalAmount)){
            $("#PatientTotalAmount").val(0.00);            
            $("#PatientDiscountTotal").val(0.00);
            $("#PatientSubTotalAmount").val(0.00);            
        }else{            
            $("#PatientTotalAmount").val((totalAmount).toFixed(2));            
            var total_sum = totalDiscountAll+totalAmountPaid;
            $("#PatientSubTotalAmount").val((totalAmount - total_sum).toFixed(2));
        }
    }
    
    $(document).ready(function(){    
        var acc = $("select.patient_aging_coa_id option:selected").val();
        var accSa = $("select.sales_order_coa_id option:selected").val();
        $("#chartAccPatientAgingCoa").val(acc);
        $("#chartAccSalesOrder").val(accSa);
        getTotalAmountPatientPatientIpd();                   
        keyEventPatientIpd();
        
        $(".btnDateCreated").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd/mm/yy',
            yearRange: '-100:-0',
            maxDate: 0,
            beforeShow: function(){
                setTimeout(function(){
                    $("#ui-datepicker-div").css("z-index", 1000);
                }, 10);
            }
        }).unbind("blur");
                        
         // for sort section in company
        if($("#PatientCompanyId").val()!='' && $("#PatientPatientGroupId").val()!=""){
            $("#addPatientPatientIpd").show();                        
        }else{            
            $("#addPatientPatientIpd").hide();
        }     
        
        $(".classCompany").change(function(){
            if($(this).val()!=''){
                $(".classSection").closest("tr").find("td .classSection").val('');
                $(".classSection").closest("tr").find("td .classSection option[class!='']").hide();
                $(".classSection").closest("tr").find("td .classSection option[class='"  + $(this).val() + "']").show();
            }else{                
                $(".classSection").closest("tr").find("td .classSection option[class!='']").show();
            }       
            comboRefesh();
        });
        
        // close company
        $("#PatientIpdCompanyInsuranceId").change(function(){
            if($("#PatientCompanyId").val()!='' && $("#PatientPatientGroupId").val()!="" && $("#PatientIpdCompanyInsuranceId").val()!=""){
                $("#addPatientPatientIpd").show();
                $(".classSection").closest("tr").find("td .classSection").val('');
                $(".classSection").closest("tr").find("td .classSection option[class!='']").hide();
                $(".classSection").closest("tr").find("td .classSection option[class='"  + $("#PatientCompanyId").val() + "']").show();
            }else{
                $("#addPatientPatientIpd").hide();
            }
        });
        $(".patientPatientIpdService").click(function(){
            var section = $(this).closest("tr").find("td .classSection option:selected").val();
            if(section==''){
               alert('<?php echo MESSAGE_SELECT_SECTION; ?>');
               return false;
            }
        });
        
        $(".btnDiscountTotal").click(function(){            
            addNewDiscountTotal($(this).closest("tr"));
        });
        $(".btnRemoveDiscountTotal").click(function(){
            removeDiscountTotal($(this).closest("tr"));
        });
        
        $("#PatientTotalAmountPaid").blur(function(){
            if($(this).val() == ""){                                    
                $(this).val('0.00');
                getTotalAmountPatientPatientIpd();
            }
        });
        $("#PatientTotalAmountPaid").live('click',function(){              
            $("#PatientTotalAmountPaid").val("");
        });
        
        $("#PatientTotalAmountPaid").live('keyup',function(){            
            getTotalAmountPatientPatientIpd();
        });            
        
        // form add new service for quotation
        $(".btnAddType").click(function() {
            
            $('input.btnDateCreated').datepicker('destroy');
            var id = "";                        
            var clone = $("#example").find(".PatientPatientIpdTr:last").clone(true);
            clone.appendTo("#tableToModify");                                    
            
            id = $("#example").find(".classSection:last").attr('rel');            
            if(id == ""){
                var id = $("#example").find(".serviceId:last").text();
            }else {
                id++; 
            }                            
                        
            // update id in tr : last                        
            
            $("#example").find(".classSection:last").attr('id', 'ServiceSectionId'+id);
            $("#example").find(".classSection:last").attr('rel', id);
            $("#example").find(".patientPatientIpdService:last").attr('id', 'PatientIpdServiceId'+id);
            $("#example").find(".patientPatientIpdService:last").attr('rel', id);
            $("#example").find(".classDoctor:last").attr('id', 'PatientIpdDoctorId'+id);
            $("#example").find(".checkout_qty:last").attr('id', 'PatientIpdQty'+id);
            $("#example").find(".checkout_qty:last").attr('rel', id);            
            $("#example").find(".btnDateCreated:last").attr('id', 'dateCreated'+id);
            $("#example").find(".btnDateCreated:last").attr('rel', id);            
            $("#example").find(".checkout_unit_price:last").attr('id', 'PatientIpdUnitPrice'+id);
            $("#example").find(".checkout_unit_price:last").attr('rel', id);            
            $("#example").find(".checkout_total_price:last").attr('id', 'PatientIpdTotalPrice'+id);            
            $("#example").find(".btnDateCreated:last").val("");            
            $("#example").find(".PatientPatientIpdTr:last").find("td .btnRemoveType").show();                                                            
             
            $(this).siblings(".btnRemoveType").show();
            $(this).hide();           
            
            $('input.btnDateCreated').datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'dd/mm/yy',
                yearRange: '-100:-0',
                maxDate: 0,
                beforeShow: function(){
                    setTimeout(function(){
                        $("#ui-datepicker-div").css("z-index", 1000);
                    }, 10);
                }
            }).unbind("blur");
            
            comboRefeshType();
            staffRefreshType();                        
            keyEventPatientIpd();
           
        });
        $(".btnRemoveType").click(function() {
            $(this).closest(".PatientPatientIpdTr").remove();
            $("#example").find(".PatientPatientIpdTr:last").find("td .btnAddType").show();            
            if ($('#example .PatientPatientIpdTr').length == 1) {
                $("#example").find(".PatientPatientIpdTr:last").find("td .btnRemoveType").hide();
            }            
            sortNuTablePatientIpd();
            getTotalAmountPatientPatientIpd();                    
            keyEventPatientIpd();
        });        
        // close form service for payment                
        
        // Prevent Key Enter
        preventKeyEnter();     
        $("#PatientPaymentForm").validationEngine();
        $("#PatientPaymentForm").ajaxForm({
            dataType: 'json',
            beforeSubmit: function(arr, $form, options) {
                $(".txtSavePatient").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            beforeSerialize: function($form, options) {                
                $(".btnDateCreated").datepicker("option", "dateFormat", "yy-mm-dd");                
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");                
                $("#dialog").html('<div class="buttons"><button type="submit" class="positive printPatientPaymentFormInvoice" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="printPatientPaymentForm"><?php echo ACTION_PRINT_INVOICE; ?></span></button> <button type="submit" class="positive printPatientPaymentFormInvoiceVat"><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="printPatientIpdForm"><?php echo ACTION_PRINT_INVOICE_DETAIL; ?></span></button> <button type="submit" class="positive printPatientPaymentFormReceipt" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="printPatientPaymentForm"><?php echo ACTION_PRINT_RECEIPT; ?></span></button></div>');
                $(".printPatientPaymentFormInvoice").click(function(){
                    $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printInvoiceIpd/"+result,
                        beforeSend: function(){
                            $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                        },
                        success: function(printPatientPaymentFormResult){
                            w=window.open();
                            w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                            w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                            w.document.write(printPatientPaymentFormResult);
                            w.document.close();
                            try
                            {
                                //Run some code here                                                                                                       
                                jsPrintSetup.setSilentPrint(1);
                                jsPrintSetup.printWindow(w);
                            }
                            catch(err)
                            {
                                //Handle errors here                                    
                                w.print();                                     
                            } 
                            w.close();
                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                        }
                    });
                });
                
                $(".printPatientPaymentFormInvoiceVat").click(function(){
                    $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printInvoiceIpdDetail/"+result,
                        beforeSend: function(){
                            $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                        },
                        success: function(printPatientIpdFormResult){
                            w=window.open();
                            w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                            w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                            w.document.write(printPatientIpdFormResult);
                            w.document.close();
                            try
                            {
                                //Run some code here                                                                                                       
                                jsPrintSetup.setSilentPrint(1);
                                jsPrintSetup.printWindow(w);
                            }
                            catch(err)
                            {
                                //Handle errors here                                    
                                w.print();                                     
                            } 
                            w.close();
                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                        }
                    });
                });
                
                $(".printPatientPaymentFormReceipt").click(function(){
                    $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printInvoiceReceiptIpd/"+result,
                        beforeSend: function(){
                            $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                        },
                        success: function(printPatientPaymentFormResult){
                            w=window.open();
                            w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                            w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                            w.document.write(printPatientPaymentFormResult);
                            w.document.close();
                            try
                            {
                                //Run some code here                                                                                                       
                                jsPrintSetup.setSilentPrint(1);
                                jsPrintSetup.printWindow(w);
                            }
                            catch(err)
                            {
                                //Handle errors here                                    
                                w.print();                                     
                            } 
                            w.close();
                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                        }
                    });
                });
                
                
                $("#dialog").dialog({
                   title: '<?php echo 'Print Invoice/Receipt'; ?>',
                   resizable: false,
                   modal: true,
                   width: 'auto',
                   height: 'auto',
                   position:'center',
                   closeOnEscape: true,
                   open: function(event, ui){
                       $(".ui-dialog-buttonpane").show(); $(".ui-dialog-titlebar-close").show();
                   },
                   close: function(){
                       $(this).dialog({close: function(){}});
                       $(this).dialog("close");
                       $(".btnBackPatientPayment").dblclick();
                   },
                   buttons: {
                       '<?php echo ACTION_CLOSE; ?>': function() {
                           $("meta[http-equiv='refresh']").attr('content','0');
                           $(this).dialog("close");
                       }
                   }
               });
               $(".btnBackPatientPayment").dblclick();
            }
        });
        
        // $(".savePatientPayment").click(function(){
        //     if(chcekBfPatientPayment() == true){
        //         return true;
        //     }else{
        //         return false;
        //     }
        // });
        
        $(".btnBackPatientPayment").dblclick(function(event){
            event.preventDefault();
            $('#PatientPaymentForm').validationEngine('hideAll');
            oCache.iCacheLower = -1;
            oTablePatientIpdList.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
        $(".float").autoNumeric();
    });
    
    function keyEventPatientIpd() {
        $(".classSection, .patientPatientIpdService, .checkout_qty, .checkout_unit_price").unbind('click').unbind('keyup').unbind('keypress').unbind('change').unbind('blur');
        $(".checkout_qty, .checkout_unit_price").attr('autocomplete','off')
        $(".classSection").change(function(){
            $("#ServiceCompanyId").closest("tr").find("td .classCompany").val($(this).find("option:selected").attr("class"));            
            var serId = this.id;                             
            var pateintGroup = $("#PatientPatientGroupId").val();
            var companyInsuranceId = $("#PatientIpdCompanyInsuranceId").val();            
            var id = $(this).attr('rel');
            if(companyInsuranceId==undefined){
                companyInsuranceId = "";
            }
            $.ajax({
                type: "POST",
                url: '<?php echo $absolute_url . 'cashiers'; ?>/getService/' + $(this).val(),
                data: "",
                success: function(msg){
                    var values = [];
                    $('select.patientPatientIpdService').each(function () {
                        values.push($(this).val());
                    });                    
                    $("#PatientIpdServiceId" + id).html(msg).find("option").each(function () {
                        var s = $(this);
                        $.each(values, function (v, i) {
                            if (s.val() == i && s.val() != '') {
                                s.hide();
                            }
                        });
                    });
                    var service = $('select.patientPatientIpdService');
                    $("#PatientIpdServiceId" + id).change(function () {                    
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
        // action change service price
        $(".patientPatientIpdService").change(function () {  
            var id = $(this).attr('rel');
            var pateintGroup = $("#PatientPatientGroupId").val();
            var companyInsuranceId = $("#PatientIpdCompanyInsuranceId").val();   
            if(companyInsuranceId==undefined){
                companyInsuranceId = "";
            }  
            $.ajax({
                type: "POST",
                url: '<?php echo $absolute_url . $this->params['controller']; ?>/getServicePrice/' + $(this).val() + '/' + pateintGroup + '/' + companyInsuranceId,
                data: "",
                success: function (msg) {
                    $('#PatientIpdQty' + id).val(1);
                    $('#PatientIpdUnitPrice' + id).val(Number(msg).toFixed(2));
                    $('#PatientIpdTotalPrice' + id).val(Number(msg).toFixed(2));
                    getTotalAmountPatientPatientIpd();
                    $('#PatientIpdUnitPrice' + id).attr('rel', $(this).val());
                    $('#PatientIpdUnitPrice' + id).attr('index', id);
                   
                }
            });
        });
        
        $(".checkout_unit_price").blur(function () {
            var id = $(this).attr('rel');         
            if ($(this).val() != "") {
                var totalPrice = Number($(this).val()) * $("#PatientIpdQty" + id).val();
                $('#PatientIpdTotalPrice' + id).val(Number(totalPrice).toFixed(2));
                getTotalAmountPatientPatientIpd();
            }
        });
        $(".checkout_unit_price").live('keyup', function () {
            var id = $(this).attr('rel');
            var qty = replaceNum($(this).closest("tr").find(".checkout_qty").val());
            var totalPrice = (Number($(this).val()) * $("#PatientIpdQty" + id).val());          
            $('#PatientIpdTotalPrice' + id).val(Number(totalPrice).toFixed(2));
            getTotalAmountPatientPatientIpd();
           
        });
    
        $(".checkout_qty").blur(function(){
            var qtyId = this.id; 
            var id = $(this).attr('rel');            
            if($(this).val() == ""){                                    
                $(this).val('1');                                
                var totalPrice = Number($("#PatientIpdUnitPrice"+id).val())*$(this).val();
                $('#PatientIpdTotalPrice'+id).val(Number(totalPrice).toFixed(2));
                getTotalAmountPatientPatientIpd();
            }
        });
        $(".checkout_qty").live('click',function(){  
            var id = $(this).attr('rel');            
            $("#PatientIpdQty"+id).val("");                                 
        });
        $(".checkout_qty").live('keyup',function(){                                
            var id = $(this).attr('rel');            
            var totalPrice = (Number($("#PatientIpdUnitPrice"+id).val())*$(this).val());
            $('#PatientIpdTotalPrice'+id).val(Number(totalPrice).toFixed(2));
            getTotalAmountPatientPatientIpd();
        });
    }
    
    function chcekBfPatientPayment(){
        var formName = "#PatientPaymentForm";
        var validateBack =$(formName).validationEngine("validate");
        if(!validateBack){            
            return false;
        }else{            
            return true;
        }
    }
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="#" class="positive btnBackPatientPayment">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('PatientPayment', array('id' => 'PatientPaymentForm', 'url' => '/cashiers/patientPayment/' . $this->params['pass'][0])); ?>
<input type="hidden" name="data[PatientIpd][id]" value="<?php echo $patientIpdId; ?>" />
<input type="hidden" name="data[Patient][id]" value="<?php echo $patient['Patient']['id']; ?>" />
<input type="hidden" name="data[Patient][exchange_rate_id]" value="<?php echo getExchangeRateId(); ?>" />
<input type="hidden" name="data[Patient][exchange_rate]" value="<?php echo $exchangeRate; ?>" />
<input type="hidden" value="1" name="data[SalesOrder][currency_center_id]" id="OrderCurrencyCenterId" />
<fieldset>
    <legend><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?></legend>
    <table style="width: 100%;" cellspacing="3">
        <tr>
            <th style="width: 15%;"><?php __(PATIENT_CODE); ?></th>
            <td style="width: 35%;">: <?php echo $patient['Patient']['patient_code']; ?></td>
            <th style="width: 15%;"><?php __(TABLE_DOB); ?></th>
            <td style="width: 35%;">: 
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
                if ($patient['Patient']['location_id'] != "") {
                    $query = mysql_query("SELECT name FROM patient_locations WHERE id=" . $patient['Patient']['location_id']);
                    while ($row = mysql_fetch_array($query)) {
                        echo $row['name'];
                    }
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
                <input type="hidden" id="PatientPatientGroupId" value="<?php echo $patient['Patient']['patient_group_id']; ?>"/>
                <input type="hidden" id="PatientCompanyId" name="data[Patient][company_id]" value="<?php echo $patientIpd['PatientIpd']['company_id']; ?>"/>

            </td>
        </tr>
        <tr style="display: none;">
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
        <tr>            
            <th><?php __(TABLE_BILL_PAID_BY); ?></th>
            <td>
                : <?php echo $patient['PatientBillType']['name']; ?>            
            </td>       
            <?php if ($patient['PatientBillType']['id'] == 3) { ?>            
                <th><?php echo '<label for="PatientIpdCompanyInsuranceId">' . TABLE_COMPANY_INSURANCE_NAME . ' <span class="red">*</span> : </label>'; ?></th>
                <td colspan="3"><?php echo $this->Form->input('company_insurance_id', array('empty' => SELECT_OPTION, 'selected' => $patient['Patient']['company_insurance_id'], 'label' => false, 'class' => 'validate[required]', 'style' => 'width:200px;height: 35px;', 'disabled' => true)); ?></td>
                <input type="hidden" name="data[Patient][company_insurance_id]" value="<?php echo $patient['Patient']['company_insurance_id']; ?>"/>
            <?php } ?>
        </tr>
    </table>     
</fieldset>
<br/>
<div id="addPatientPatientIpd" style="display:none;">    
    <table id="example" class="table" cellspacing="0">
        <tr>
            <th class="first"><?php echo TABLE_NO; ?></th>
            <th><?php echo SECTION_SECTION; ?></th>
            <th><?php echo TABLE_SERVICE_NAME; ?></th>
            <th><?php echo DOCTOR_NAME; ?></th>
            <th><?php echo GENERAL_QTY; ?></th>
            <th><?php echo GENERAL_UNIT_PRICE; ?></th>
            <th><?php echo GENEARL_DATE; ?></th>
            <th><?php echo GENERAL_TOTAL_PRICE; ?></th>            
            <th>&nbsp;</th>
        </tr>
        <tbody id="tableToModify">
            <?php
            $index = 1;
            // check labo price 
            foreach ($dataServiceLabo as $dataServiceLabo) { ?>
                <tr class="PatientPatientIpdTr">
                    <td class="first serviceId">
                        <?php // echo $index;?>
                        <img src="<?php echo $this->webroot; ?>img/icon/blood_test.png" alt=""/>
                    </td>            
                    <td>
                        <div class="inputContainer">
                            <select id="ServiceSectionId<?php echo $index;?>" rel="<?php echo $index;?>" class="classSection validate[required]" name="data[Patient][section_id][]" style="width:160px;">
                                <option value="labo"><?php echo 'Labo'; ?></option> 
                            </select>
                        </div>                    
                    </td>
                    <td>
                        <select id="PatientIpdServiceId<?php echo $index; ?>" style="width:160px;" name="data[Patient][service_id][]" class="validate[required]">
                            <option selected="selected" value="<?php echo $dataServiceLabo['PatientIpdServiceDetail']['service_id']; ?>"><?php echo $dataServiceLabo['LaboItemGroup']['name']; ?></option>
                        </select>
                    </td>
                    <td>
                        <select id="PatientIpdDoctorId<?php echo $index;?>" style="width:130px;" rel="<?php echo $index;?>" name="data[Patient][doctor_id][]" class="classDoctor validate[required]">
                            <option value=""><?php echo SELECT_OPTION;?></option>
                            <?php 
                            foreach ($doctors as $doctor) {
                                if ($dataServiceLabo['PatientIpdServiceDetail']['doctor_id'] == $doctor['User']['id']) {
                                    echo '<option selected="selected" class="' . $doctor['Company']['id'] . '" value="' . $doctor['User']['id'] . '">' . $doctor['Employee']['name'] . '</option>';
                                } else {
                                    echo '<option class="' . $doctor['Company']['id'] . '" value="' . $doctor['User']['id'] . '">' . $doctor['Employee']['name'] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <?php echo $this->Form->text('qty', array('value' => $dataServiceLabo['PatientIpdServiceDetail']['qty'], 'id' => 'PatientIpdQty'.$index, 'name' => 'data[Patient][qty][]', 'class' => 'checkout_qty integer validate[required]', 'style' => 'width:50px;text-align:center;', 'rel' => $index)); ?> 
                    </td>
                    <td>
                        <?php echo $this->Form->text('unit_price', array('value' => number_format($dataServiceLabo['PatientIpdServiceDetail']['unit_price'], 2), 'id' => 'PatientIpdUnitPrice'.$index, 'name' => 'data[Patient][unit_price][]', 'class' => 'checkout_unit_price float validate[required]', 'rel' => $index, 'readonly' => true, 'style' => 'width:100px;')); ?> 
                    </td>
                    <td>
                        <?php 
                        $createdDate = "";
                        if($dataServiceLabo['PatientIpdServiceDetail']['date_created']!="" && $dataServiceLabo['PatientIpdServiceDetail']['date_created']!="0000-00-00"){
                           $createdDate = date('d/m/Y', strtotime($dataServiceLabo['PatientIpdServiceDetail']['date_created']));
                        }
                        ?>
                        <input type="text" id="dateCreated<?php echo $index;?>" value="<?php echo $createdDate;?>" class="btnDateCreated" name="data[Patient][date_created][]" style="width: 100px;" rel="<?php echo $index;?>"/>
                    </td>
                    <td>
                        <?php echo $this->Form->text('total_price', array('value' => number_format($dataServiceLabo['PatientIpdServiceDetail']['total_price'], 2), 'id' => 'PatientIpdTotalPrice'.$index, 'name' => 'data[Patient][total_price][]', 'class' => 'checkout_total_price', 'style' => 'width:120px;', 'readonly' => true)); ?>
                    </td>
                    <td style="padding: 5px 5px 5px 5px !important;;">
                        &nbsp;
                    </td>
                </tr>                                                
            <?php
                $index++;                
            }
            if (!empty($salesOrders)) {
                foreach ($salesOrders as $salesOrder) {
                    ?>
                    <tr class="PatientPatientIpdTr" style="background: #EDEEF0;">
                        <td class="first serviceId">
                            <img src="<?php echo $this->webroot; ?>img/icon/medicine.png" alt=""/>
                            <input type="hidden" id="stsDN" value="<?php echo $salesOrders[0]['SalesOrder']['status']; ?>" />
                            <input type="hidden" name="data[SalesOrder][id]" value="<?php echo $salesOrders[0]['SalesOrder']['id']; ?>" />
                            <input type="hidden" name="data[SalesOrder][total_amount]" value="<?php echo $salesOrders[0]['SalesOrder']['balance']; ?>" />
                            <input type="hidden" name="data[SalesOrder][balance_us]" value="0" />
                        </td>            
                        <td>
                            <div class="inputContainer">                                    
                                <select id="ServiceSectionId<?php echo $index; ?>" rel="<?php echo $index; ?>" class="validate[required]" name="data[Patient][section_id][]" style="width:160px;">
                                    <option value="medicine"><?php echo 'Medicine'; ?></option>                                        
                                </select>
                            </div>                    
                        </td>
                        <td>
                            <select id="CheckoutServiceId<?php echo $index; ?>" style="width:160px;" name="data[Patient][service_id][]" class="validate[required]">
                                <option selected="selected" value="<?php echo $salesOrder['SalesOrder']['id']; ?>"><?php echo $salesOrder['SalesOrder']['so_code']; ?></option>
                            </select>                                
                        </td>
                        <td>                                
                            <select id="PatientIpdDoctorId<?php echo $index; ?>" style="width:130px;" name="data[Patient][doctor_id][]" class="classDoctor validate[required]">
                                <option value=""><?php echo SELECT_OPTION; ?></option>
                                <?php
                                foreach ($doctors as $doctor) {
                                    if ($salesOrder['SalesOrder']['created_by'] == $doctor['User']['id']) {
                                        echo '<option selected="selected" class="' . $doctor['Company']['id'] . '" value="' . $doctor['User']['id'] . '">' . $doctor['Employee']['name'] . '</option>';
                                    } else {
                                        echo '<option class="' . $doctor['Company']['id'] . '" value="' . $doctor['User']['id'] . '">' . $doctor['Employee']['name'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                            <?php echo $this->Form->text('qty', array('id' => 'PatientIpdQty' . $index, 'name' => 'data[Patient][qty][]', 'class' => 'validate[required]', 'style' => 'width:50px; text-align:center;', 'readonly' => true, 'rel' => $index, 'value' => 1, 'autocomplete' => 'off')); ?> 
                        </td>
                        <td>
                            <?php echo $this->Form->text('unit_price', array('id' => 'CheckoutUnitPrice' . $index, 'name' => 'data[Patient][unit_price][]', 'class' => 'checkout_unit_price float validate[required]', 'rel' => $index, 'readonly' => true, 'style' => 'width:100px;', 'value' => $salesOrder['SalesOrder']['balance'])); ?> 
                            <input type="hidden" name="data[Patient][hospital_price][]" value="0" />
                        </td>
                        <td>
                            <input type="text" id="dateCreated" value="<?php echo date('d/m/Y', strtotime($salesOrder['SalesOrder']['order_date']));?>" class="btnDateCreated" name="data[Patient][date_created][]" style="width: 100px;" rel=""/>             
                        </td>
                        <td>
                            <?php echo $this->Form->text('total_price', array('id' => 'PatientIpdTotalPrice' . $index, 'name' => 'data[Patient][total_price][]', 'class' => 'checkout_total_price', 'style' => 'width:120px;', 'readonly' => true, 'value' => $salesOrder['SalesOrder']['balance'])); ?>
                        </td> 
                        <td>&nbsp;</td>
                    </tr>
                    <?php
                    $index++;
                }
            } else {
                ?>
                <input type="hidden" name="data[SalesOrder][id]" value="" />
                <input type="hidden" name="data[SalesOrder][total_amount]" value="" />
                <input type="hidden" name="data[SalesOrder][balance_us]" value="" />    
                <?php
            }
            ?> 
            <?php if(empty($dataServiceDetail)) { ?>
                        <tr class="PatientPatientIpdTr">
                            <td class="first serviceId">
                                <?php echo $index; ?>
                            </td>            
                            <td>
                                <div class="inputContainer">
                                    <?php
                                    $query = mysql_query("SELECT sections.id, sections.name, section_companies.company_id  FROM `sections` INNER JOIN section_companies ON sections.id = section_companies.section_id 
                                                                WHERE sections.is_active = 1 
                                                                AND sections.id IN (SELECT section_id FROM section_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = '" . $user['User']['id'] . "')) ORDER BY sections.name ASC");
                                    ?>
                                    <select id="ServiceSectionId<?php echo $index; ?>" rel="<?php echo $index; ?>" class="classSection validate[required]" name="data[Patient][section_id][]" style="width:160px;">
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
                                <?php echo $this->Form->input('service_id', array('id' => 'PatientIpdServiceId'.$index, 'name' => 'data[Patient][service_id][]', 'empty' => SELECT_OPTION, 'label' => false, 'class' => 'patientPatientIpdService validate[required]', 'style' => 'width:160px;', 'rel' => $index)); ?>
                            </td>
                            <td>
                                <select id="PatientIpdDoctorId<?php echo $index; ?>" style="width:130px;" name="data[Patient][doctor_id][]" class="classDoctor validate[required]">
                                    <option value=""><?php echo SELECT_OPTION;?></option>
                                    <?php 
                                    foreach ($doctors as $doctor) {
                                        echo '<option class="' . $doctor['Company']['id'] . '" value="' . $doctor['User']['id'] . '">' . $doctor['Employee']['name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <?php echo $this->Form->text('qty', array('id' => 'PatientIpdQty'.$index, 'name' => 'data[Patient][qty][]', 'class' => 'checkout_qty integer validate[required]', 'style' => 'width:50px;text-align:center;', 'rel' => $index)); ?> 
                            </td>
                            <td>
                                <?php echo $this->Form->text('unit_price', array('id' => 'PatientIpdUnitPrice'.$index, 'name' => 'data[Patient][unit_price][]', 'class' => 'checkout_unit_price float validate[required]', 'rel' => $index, 'style' => 'width:100px;')); ?> 
                            </td>
                            <td>
                                <input type="text" id="dateCreated<?php echo $index; ?>" class="btnDateCreated" name="data[Patient][date_created][]" style="width: 100px;" rel=""/>
                            </td>
                            <td>
                                <?php echo $this->Form->text('total_price', array('id' => 'PatientIpdTotalPrice'.$index, 'name' => 'data[Patient][total_price][]', 'class' => 'checkout_total_price', 'style' => 'width:120px;', 'readonly' => true)); ?>
                            </td>
                            <td style="padding: 5px 5px 5px 5px !important;;">
                                <img alt="" src="<?php echo $this->webroot; ?>img/button/plus.png" class="btnAddType" style="cursor: pointer;" />
                                <img alt="" src="<?php echo $this->webroot; ?>img/button/cross.png" class="btnRemoveType" style="cursor: pointer;display: none;" />
                            </td>
                        </tr>
            
            <?php }else {?>
                    <?php                   
                    foreach ($dataServiceDetail as $resultServiceDetail) { ?>
                        <tr class="PatientPatientIpdTr">
                            <td class="first serviceId"><?php echo $index;?></td>            
                            <td>
                                <div class="inputContainer">
                                    <?php
                                    $queryService = mysql_query("SELECT sections.id, sections.name, section_companies.company_id  FROM `sections` INNER JOIN section_companies ON sections.id = section_companies.section_id 
                                                                WHERE sections.is_active = 1 AND sections.id IN (SELECT section_id FROM section_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = '" . $user['User']['id'] . "'))")
                                    ?>
                                    <select id="ServiceSectionId<?php echo $index;?>" rel="<?php echo $index;?>" class="classSection validate[required]" name="data[Patient][section_id][]" style="width:160px;">
                                        <option value=""><?php echo SELECT_OPTION; ?></option>
                                        <?php
                                        while ($row = mysql_fetch_array($queryService)) {
                                            
                                            if($resultServiceDetail['Section']['id']==$row['id'] && $row['company_id']==$patientIpd['PatientIpd']['company_id']){                                
                                                echo '<option selected="selected" class="'.$row['company_id'].'" value="'.$row['id'].'">'.$row['name'].'</option>';                                
                                            }else{
                                                if($row['company_id']==$patientIpd['PatientIpd']['company_id']){
                                                    echo '<option class="'.$row['company_id'].'" value="'.$row['id'].'">'.$row['name'].'</option>';
                                                }else{
                                                    echo '<option style="display:none;" class="'.$row['company_id'].'" value="'.$row['id'].'">'.$row['name'].'</option>';
                                                }

                                            }
                                        }
                                        ?>
                                    </select>
                                </div>                    
                            </td>
                            <td>
                                <?php echo $this->Form->input('service_id', array('selected' =>$resultServiceDetail['PatientIpdServiceDetail']['service_id'], 'id' => 'PatientIpdServiceId'.$index, 'name' => 'data[Patient][service_id][]', 'empty' => SELECT_OPTION, 'label' => false, 'class' => 'patientPatientIpdService validate[required]', 'style' => 'width:160px;', 'rel' => $index)); ?>
                            </td>
                            <td>
                                <select id="PatientIpdDoctorId<?php echo $index;?>" style="width:130px;" rel="<?php echo $index;?>" name="data[Patient][doctor_id][]" class="classDoctor validate[required]">
                                    <option value=""><?php echo SELECT_OPTION;?></option>
                                    <?php 
                                    foreach ($doctors as $doctor) {
                                        if ($resultServiceDetail['PatientIpdServiceDetail']['doctor_id'] == $doctor['User']['id']) {
                                            echo '<option selected="selected" class="' . $doctor['Company']['id'] . '" value="' . $doctor['User']['id'] . '">' . $doctor['Employee']['name'] . '</option>';
                                        } else {
                                            echo '<option class="' . $doctor['Company']['id'] . '" value="' . $doctor['User']['id'] . '">' . $doctor['Employee']['name'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <?php echo $this->Form->text('qty', array('value' => $resultServiceDetail['PatientIpdServiceDetail']['qty'], 'id' => 'PatientIpdQty'.$index, 'name' => 'data[Patient][qty][]', 'class' => 'checkout_qty integer validate[required]', 'style' => 'width:50px;text-align:center;', 'rel' => $index)); ?> 
                            </td>
                            <td>
                                <?php echo $this->Form->text('unit_price', array('value' => number_format($resultServiceDetail['PatientIpdServiceDetail']['unit_price'], 2), 'id' => 'PatientIpdUnitPrice'.$index, 'name' => 'data[Patient][unit_price][]', 'class' => 'checkout_unit_price float validate[required]', 'rel' => $index, 'readonly' => true, 'style' => 'width:100px;')); ?> 
                            </td>
                            <td>
                                <?php 
                                $createdDate = "";
                                if($resultServiceDetail['PatientIpdServiceDetail']['date_created']!="" && $resultServiceDetail['PatientIpdServiceDetail']['date_created']!="0000-00-00"){
                                   $createdDate = date('d/m/Y', strtotime($resultServiceDetail['PatientIpdServiceDetail']['date_created']));
                                }
                                ?>
                                <input type="text" id="dateCreated<?php echo $index;?>" value="<?php echo $createdDate;?>" class="btnDateCreated" name="data[Patient][date_created][]" style="width: 100px;" rel="<?php echo $index;?>"/>
                            </td>
                            <td>
                                <?php echo $this->Form->text('total_price', array('value' => number_format($resultServiceDetail['PatientIpdServiceDetail']['total_price'], 2), 'id' => 'PatientIpdTotalPrice'.$index, 'name' => 'data[Patient][total_price][]', 'class' => 'checkout_total_price', 'style' => 'width:120px;', 'readonly' => true)); ?>
                            </td>
                            
                            <td style="padding: 5px 5px 5px 5px !important;;">
                                <img alt="" src="<?php echo $this->webroot; ?>img/button/plus.png" class="btnAddType" style="cursor: pointer;<?php if($index!=count($dataServiceDetail)){ echo 'display: none;';}?>" />
                                <img style="cursor: pointer; display: inline;" class="btnRemoveType" src="<?php echo $this->webroot; ?>img/button/cross.png" alt="">
                            </td>
                        </tr>                                                
                    <?php
                    $index++;                
                    }?>
            <?php }?>
        </tbody>
        <tr>
            <td class="first" style="text-align: right;" colspan="7"><label for="PatientTotalAmount">Sub Total ($):</label></td>
            <td>
                <input type="text" id="PatientTotalAmount" value="0.00" class="validate[required]" readonly="readonly" style="width:120px; height: 30px;font-weight: bold;" name="data[Patient][total_amount]">
            </td>
        </tr>
        <tr>
            <td class="first" style="text-align: right;" colspan="7"><label for="PatientDiscountTotal">Total Discount :</label></td>
            <td style="height: 30px;">
                <?php                    
                if ($allowProductDiscount) {
                    ?>
                    <input type="text" id="PatientDiscountTotal" value="0.00" class="float btnDiscountTotal" style="width:120px; height: 30px;font-weight: bold;" name="data[Patient][total_discount]" readonly="readonly">                    
                    <input type="hidden" id="PatientDiscountTotalP" value="0.00" name="data[Patient][total_discount_per]">   
					<img alt="Remove" src="<?php echo $this->webroot . 'img/button/cross.png'; ?>" class="btnRemoveDiscountTotal" align="absmiddle" style="cursor: pointer; display: none"  onmouseover="Tip('Remove')" />
                    <?php
                }else{
                ?>  
                    <input type="hidden" id="PatientDiscountTotal" value="0.00" class="float" style="width:120px; height: 30px;font-weight: bold;" name="data[Patient][total_discount]" readonly="readonly">                    
                <?php
                }
                ?>                 
            </td>
        </tr>
        <tr>
            <td class="first" style="text-align: right;" colspan="7"><label for="PatientChartAccountId"><?php echo 'Deposit To'; ?> <span class="red">*</span> :</label></td>
            <td>
                <?php
                $filter="AND chart_account_type_id IN (1)";
                ?>
                <div class="inputContainer">
                    <select id="PatientChartAccountIdCash" name="data[Patient][chart_account_id_cash]" class="patient_aging_coa_id validate[required]" style="width: 125px;" disabled="disabled">
                        <option value=""><?php echo SELECT_OPTION; ?></option>
                        <?php
                        $query = array();
                        $query[0]=mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE ISNULL(parent_id) AND is_active=1 ".$filter." ORDER BY account_codes");
                        while($data[0]=mysql_fetch_array($query[0])){
                            $queryIsNotLastChild=mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=".$data[0]['id']);
                        ?>
                        <option value="<?php echo $data[0]['id']; ?>" chart_account_type_name="<?php echo $data[0]['chart_account_type_name']; ?>" company_id="<?php echo $data[0]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild)?'disabled="disabled"':''; ?> <?php echo $data[0]['id']==$cashBankAccountId?'selected="selected"':''; ?>><?php echo $data[0]['name']; ?></option>
                            <?php
                            $query[1]=mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=".$data[0]['id']." AND is_active=1 ".$filter." ORDER BY account_codes");
                            while($data[1]=mysql_fetch_array($query[1])){
                                $queryIsNotLastChild=mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=".$data[1]['id']);
                            ?>
                            <option value="<?php echo $data[1]['id']; ?>" chart_account_type_name="<?php echo $data[1]['chart_account_type_name']; ?>" company_id="<?php echo $data[1]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild)?'disabled="disabled"':''; ?> <?php echo $data[1]['id']==$cashBankAccountId?'selected="selected"':''; ?> style="padding-left: 25px;"><?php echo $data[1]['name']; ?></option>
                                <?php
                                $query[2]=mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=".$data[1]['id']." AND is_active=1 ".$filter." ORDER BY account_codes");
                                while($data[2]=mysql_fetch_array($query[2])){
                                    $queryIsNotLastChild=mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=".$data[2]['id']);
                                ?>
                                <option value="<?php echo $data[2]['id']; ?>" chart_account_type_name="<?php echo $data[2]['chart_account_type_name']; ?>" company_id="<?php echo $data[2]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild)?'disabled="disabled"':''; ?> <?php echo $data[2]['id']==$cashBankAccountId?'selected="selected"':''; ?> style="padding-left: 50px;"><?php echo $data[2]['name']; ?></option>
                                    <?php
                                    $query[3]=mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=".$data[2]['id']." AND is_active=1 ".$filter." ORDER BY account_codes");
                                    while($data[3]=mysql_fetch_array($query[3])){
                                        $queryIsNotLastChild=mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=".$data[3]['id']);
                                    ?>
                                    <option value="<?php echo $data[3]['id']; ?>" chart_account_type_name="<?php echo $data[3]['chart_account_type_name']; ?>" company_id="<?php echo $data[3]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild)?'disabled="disabled"':''; ?> <?php echo $data[3]['id']==$cashBankAccountId?'selected="selected"':''; ?> style="padding-left: 75px;"><?php echo $data[3]['name']; ?></option>
                                        <?php
                                        $query[4]=mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=".$data[3]['id']." AND is_active=1 ".$filter." ORDER BY account_codes");
                                        while($data[4]=mysql_fetch_array($query[4])){
                                            $queryIsNotLastChild=mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=".$data[4]['id']);
                                        ?>
                                        <option value="<?php echo $data[4]['id']; ?>" chart_account_type_name="<?php echo $data[4]['chart_account_type_name']; ?>" company_id="<?php echo $data[4]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild)?'disabled="disabled"':''; ?> <?php echo $data[4]['id']==$cashBankAccountId?'selected="selected"':''; ?> style="padding-left: 100px;"><?php echo $data[4]['name']; ?></option>
                                            <?php
                                            $query[5]=mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=".$data[4]['id']." AND is_active=1 ".$filter." ORDER BY account_codes");
                                            while($data[5]=mysql_fetch_array($query[5])){
                                                $queryIsNotLastChild=mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=".$data[5]['id']);
                                            ?>
                                            <option value="<?php echo $data[5]['id']; ?>" chart_account_type_name="<?php echo $data[5]['chart_account_type_name']; ?>" company_id="<?php echo $data[5]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild)?'disabled="disabled"':''; ?> <?php echo $data[5]['id']==$cashBankAccountId?'selected="selected"':''; ?> style="padding-left: 125px;"><?php echo $data[5]['name']; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </select>
                    <input type="hidden" id="chartAccPatientAgingCoa" name="data[Patient][chart_account_id_cash]" >
                </div>
            </td>
        </tr>
        <tr>
            <td class="first" style="text-align: right;" colspan="7"><label for="PatientTotalAmountPaid">Total Paid ($):</label></td>
            <td>
                <input type="text" id="PatientTotalAmountPaid" value="0.00" class="float validate[custom[number],required]" style="width:120px; height: 30px;font-weight: bold;" name="data[Patient][total_amount_paid]" autocomplete="off">
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
                    <select id="PatientChartAccountId" name="data[Patient][chart_account_id]" class="sales_order_coa_id validate[required]" style="width:125px; height: 30px;" disabled="disabled">
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
                    <input type="hidden" id="chartAccSalesOrder" name="data[Patient][chart_account_id]" >
                </div>
            </td>
        </tr>
    </table>    
</div>
<div class="clear"></div>
<div class="buttons">
    <button type="submit" class="positive savePatientPayment" >
        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
        <span class="txtSavePatient"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<?php echo $this->Form->end(); ?>
