<?php
include('includes/function.php');
echo $this->element('prevent_multiple_submit');
$absolute_url = FULL_BASE_URL . Router::url("/", false);
$exchangeRate = getExchangeRate();
// $exchangeRate = 4150; 
$tblNameRadom = "tbl" . rand();
// Authentication
$this->element('check_access');
$allowProductDiscount = checkAccess($user['User']['id'], $this->params['controller'], 'discount');
$rnd = rand();
$btnShowHide = "btnShowHide" . $rnd;
$formFilter = "PatientCheckoutTr";
?>
<!--
<script type="text/javascript" src="<?php echo $this->webroot . 'js/jquery.formatCurrency-1.4.0.min.js'; ?>"></script>
-->
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
        $(".classSection, .patientCheckoutService, .qty").unbind('click').unbind('keyup').unbind('keypress').unbind('change').unbind('blur');
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
                    $('#CheckoutQty' + id).val(1);
                    $('#CheckoutUnitPrice' + id).val(Number(msg).toFixed(2));
                    $('#CheckoutTotalPrice' + id).val(Number(msg).toFixed(2));
                    getTotalAmountPatientCheckOut();
                    $('#CheckoutUnitPrice' + id).attr('rel', $(this).val());
                    $('#CheckoutUnitPrice' + id).attr('index', id);
                   
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
                    var values = [];
                    $('select.patientCheckoutService').each(function () {
                        values.push($(this).val());
                    });

                    $("#CheckoutServiceId" + id).html(msg).find("option").each(function () {
                        var s = $(this);
                        $.each(values, function (v, i) {
                            if (s.val() == i && s.val() != '') {
                                s.hide();
                            }
                        });
                    });
                    var service = $('select.patientCheckoutService');
                    $("#CheckoutServiceId" + id).change(function () {  
                        values = [];
                        service.each(function () {
                            if ($(this).val() != '') {
                                values.push($(this).val());
                            }
                        });
                    });
                }
            });
        });
        
        $(".qty").blur(function () {
            var qtyId = this.id;
            var id = $(this).attr('rel');
            if ($(this).val() == "") {
                $(this).val('1');
                var totalPrice = Number($("#CheckoutUnitPrice" + id).val()) * $(this).val();
                $('#CheckoutTotalPrice' + id).val(Number(totalPrice).toFixed(2));
                getTotalAmountPatientCheckOut();
            }
        });
        $(".qty").live('click', function () {
            var id = $(this).attr('rel');
            $("#CheckoutQty" + id).val("");
        });
        $(".qty").live('keyup', function () {
            var id = $(this).attr('rel');
            var qty = replaceNum($(this).closest("tr").find(".qty").val());
            var discount = Number($("#CheckoutDiscount" + id).val());
            var discountPercent = replaceNum($(this).closest("tr").find("input[name='discount_percent[]']").val());
            var discountAmount  = replaceNum($(this).closest("tr").find("input[name='discount_amount[]']").val());
          
            if(discountPercent !=0 && discountPercent !=''){
               var totalDiscountPer = discountPercent * qty;
               $(this).closest("tr").find("input[name='data[Patient][discount][]']").val(totalDiscountPer);
               var totalPrice = (Number($("#CheckoutUnitPrice" + id).val()) * $(this).val()) - totalDiscountPer;
            } else{
               var totalPrice = (Number($("#CheckoutUnitPrice" + id).val()) * $(this).val()) - discount;
            }            
            $('#CheckoutTotalPrice' + id).val(Number(totalPrice).toFixed(2));
            getTotalAmountPatientCheckOut();
        });
    }
    
    function staffRefreshType() {
        var i = Number($("#example").find(".serviceId:last").text()) + 1;
        $("#example").find(".serviceId:last").text(i);
    }
    
    function comboRefeshType() {
        $(".qty").each(function () {
            $("#example").find(".qty:last").val("");
        });
        $(".checkout_unit_price").each(function () {
            $("#example").find(".checkout_unit_price:last").val("");
        });
        $(".checkout_total_price").each(function () {
            $("#example").find(".checkout_total_price:last").val("");
        });
        var i = 1;
        $(".patientCheckoutUnitPrice").each(function () {
            $("#example").find(".patientCheckoutUnitPrice:last").val("");
            i++;
        });
    }


    function numberWithCommas(x) {
       var parts = x.toString().split(".");
       parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
       return parts.join(".");
    }

    function numberFormatCommas() {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
    }

    function getTotalAmountPatientCheckOut() {
        var totalAmount = 0;
        var totalAmountPaid = 0;
        var totalDiscountAll = parseFloat($("#PatientDiscountTotal").val());
        var totalAmountPaid = parseFloat($("#PatientTotalAmountPaid").val());
        var totalAmountPaidRiel = parseFloat($(".PatientTotalAmountPaidRiel").val().replace(/,/g, ''));
        var exchangeRate = Number($("input[name='data[Patient][exchange_rate]']").val());
        totalDiscountAll = totalDiscountAll != "" ? totalDiscountAll : 0;
        totalAmountPaid = totalAmountPaid != "" ? totalAmountPaid : 0;
        $(".checkout_total_price").each(function () {
            var value = $(this).val().replace(/,/g,'')
            if ($.trim(value) != '' || value != undefined) {
                totalAmount += Number(value);
            }
        });
        if (isNaN(totalAmount)) {
            $("#PatientTotalAmount").val(0.00);
            $("#PatientDiscountTotal").val(0.00);
            $("#PatientSubTotalAmount").val(0.00);
            $("#PatientSubTotalAmountRiel").val(0.00);
            $("input[name='data[Patient][total_amount_paid]']").val(0.00);
        } else {
            if(totalAmountPaid > 0){
                $("input[name='data[Patient][total_amount_paid]']").val(Number(totalAmountPaid).toFixed(2))
            }
            if(totalAmountPaidRiel > 0){
                var amountRiel = Number(totalAmountPaidRiel / exchangeRate);
                $("input[name='data[Patient][total_amount_paid]']").val(amountRiel.toFixed(2))
            }
            if(totalAmountPaid > 0 && totalAmountPaidRiel > 0) {
                var totalPaid = Number(totalAmountPaid * exchangeRate) + Number(totalAmountPaidRiel);
                $("input[name='data[Patient][total_amount_paid]']").val(Number(totalPaid / exchangeRate).toFixed(2))
                totalAmountPaid = totalAmountPaid + Number(totalAmountPaidRiel / exchangeRate);
            }
            $("#PatientTotalAmount").val((totalAmount).toFixed(2));

            var total_sum = totalDiscountAll + (totalAmountPaid ? Number(totalAmountPaid) : Number(totalAmountPaidRiel / exchangeRate));
            $("#PatientSubTotalAmount").val((totalAmount - total_sum).toFixed(2));
            $("#PatientSubTotalAmountRiel").val(numberFormatCommas().format((totalAmount - total_sum) * exchangeRate))
            
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
							var qty = tr.find("input[name='data[Patient][qty][]']").val();
                            var unitPrice = tr.find("input[name='data[Patient][unit_price][]']").val();
                            var calTotalPrice = qty * unitPrice;
                            
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
                            var totalPrice = (Number($("#CheckoutUnitPrice" + id).val()) * Number($("#CheckoutQty" + id).val())) - discount;
                            $('#CheckoutTotalPrice' + id).val(Number(totalPrice).toFixed(2));
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
        var totalPrice = Number($("#CheckoutUnitPrice" + id).val()) * Number($("#CheckoutQty" + id).val());
        $('#CheckoutTotalPrice' + id).val(Number(totalPrice).toFixed(2));

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
        //$(".chzn-select").chosen();
        $("#PatientCompanyId").chosen({width: 250});
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
        $("#CheckoutForm").validationEngine();
        $("#CheckoutForm").ajaxForm({
            dataType: 'json',
            beforeSubmit: function (arr, $form, options) {
                var sts = $("#stsDN").val();
                if (sts != '2' && sts > 0) {
                    alert('<?php echo MESSAGE_DELIVERY_BEFORE_PAYMENT; ?>');
                    return false;
                }
                $(".txtSaveCheckout").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function (result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $("#dialog").html('<div class="buttons"><button type="submit" class="positive printCheckOutFormInvoice" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="printCheckOutForm"><?php echo ACTION_PRINT_INVOICE; ?></span></button><button type="submit" class="positive printCheckOutFormInvoiceDetail" ><img src="<?php echo $this->webroot; ?>img/button/printer_detail.png" alt=""/><span class="printCheckOutForm"><?php echo ACTION_PRINT_INVOICE_DETAIL; ?></span></button> <button style="display: none;" type="submit" class="positive printCheckOutFormInvoiceVat"><img src="<?php echo $this->webroot; ?>img/button/printer_detail.png" alt=""/><span class="printCheckOutForm"><?php echo ACTION_PRINT_INVOICE_VAT; ?></span></button> <button type="submit" class="positive printCheckOutFormReceipt" ><img src="<?php echo $this->webroot; ?>img/button/printer_receipt.png" alt=""/><span class="printCheckOutForm"><?php echo ACTION_PRINT_RECEIPT; ?></span></button></div>');
                
                $(".printCheckOutFormInvoice").click(function () {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printInvoice/" + result,
                        beforeSend: function () {
                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner.gif');
                        },
                        success: function (printCheckOutFormResult) {
                            w = window.open();
                            w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                            w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                            w.document.write(printCheckOutFormResult);
                            w.document.close();
                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                        }
                    });
                });

                $(".printCheckOutFormInvoiceDetail").click(function () {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printInvoiceDetail/" + result,
                        beforeSend: function () {
                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner.gif');
                        },
                        success: function (printCheckOutFormResult) {
                            w = window.open();
                            w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                            w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                            w.document.write(printCheckOutFormResult);
                            w.document.close();
                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                        }
                    });
                });

                $(".printCheckOutFormInvoiceVat").click(function () {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printInvoiceVat/" + result,
                        beforeSend: function () {
                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner.gif');
                        },
                        success: function (printCheckOutFormResult) {
                            w = window.open();
                            w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                            w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                            w.document.write(printCheckOutFormResult);
                            w.document.close();
                            try
                            {
                                //Run some code here                                                                                                       
                                jsPrintSetup.setSilentPrint(1);
                                jsPrintSetup.printWindow(w);
                            } catch (err)
                            {
                                //Handle errors here                                    
                                w.print();
                            }
                            w.close();
                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                        }
                    });
                });
                
                $(".printCheckOutFormReceipt").click(function () {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printInvoiceReceipt/" + result,
                        beforeSend: function () {
                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner.gif');
                        },
                        success: function (printCheckOutFormResult) {
                            w = window.open();
                            w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                            w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                            w.document.write(printCheckOutFormResult);
                            w.document.close();
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
                    position: 'center',
                    closeOnEscape: true,
                    open: function (event, ui) {
                        $(".ui-dialog-buttonpane").show();
                        $(".ui-dialog-titlebar-close").show();
                    },
                    close: function () {
                        $(this).dialog({close: function () {}});
                        $(this).dialog("close");
                        $(".btnBackCheckout").dblclick();
                    },
                    buttons: {
                        '<?php echo ACTION_CLOSE; ?>': function () {
                            $("meta[http-equiv='refresh']").attr('content', '0');
                            $(this).dialog("close");
                        }
                    }
                });
                $(".btnBackCheckout").dblclick();
            }
        });
        $(".saveCheckOut").click(function () {
            if (chcekBfSaveCheckout() == true) {
                $(".saveCheckOut").attr('disabled', 'disabled');
                $(".txtSaveCheckout").html("<?php echo ACTION_LOADING; ?>");
                return true;
            } else {
                return false;
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
            $(".classSection").closest("tr").find("td .classSection").val('');
            $(".classSection").closest("tr").find("td .classSection option[class!='']").hide();
            $(".classSection").closest("tr").find("td .classSection option[class='" + $("#PatientCompanyId").val() + "']").show();
        }
        // for sort section in company
        $(".classCompany").change(function () {
            if ($("#PatientCompanyId").val() != '' && $("#PatientPatientGroupId").val() != "" && $("#CheckoutCompanyInsuranceId").val() != "") {
                $("#addPatientCheckOut").show();
                $(".classSection").closest("tr").find("td .classSection").val('');
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
                $(".classSection").closest("tr").find("td .classSection").val('');
                $(".classSection").closest("tr").find("td .classSection option[class!='']").hide();
                $(".classSection").closest("tr").find("td .classSection option[class='" + $("#PatientCompanyId").val() + "']").show();
            } else {
                $("#addPatientCheckOut").hide();
            }
        });
        
        $("#PatientTotalAmountPaid").blur(function () {
            if ($(this).val() == "") {
                $(this).val('0.00');
                $("input[name='data[Patient][total_amount_paid]']").val(0.00);
                getTotalAmountPatientCheckOut();
            }
        });
        $("#PatientTotalAmountPaid").live('click', function () {
            $("#PatientTotalAmountPaid").val("");
        });
        $("#PatientTotalAmountPaid").live('keyup', function () {
            getTotalAmountPatientCheckOut();
        });

        $("#PatientTotalAmountPaidRiel").blur(function () {
            if ($(this).val() == "") {
                $(this).val('0.00');
                $("input[name='data[Patient][total_amount_paid]']").val(0.00);
                getTotalAmountPatientCheckOut();
            }else {
                $(this).val(numberFormatCommas().format($(this).val()))
            }
            
        });
        
        $("#PatientTotalAmountPaidRiel").live('click', function () {
            $("#PatientTotalAmountPaidRiel").val("");
        });

        $("#PatientTotalAmountPaidRiel").live('keyup', function (e) {
            getTotalAmountPatientCheckOut();
        });

        $(document).on('input', '.PatientTotalAmountPaidRiel', function (e) {
            var val = $(this).val().replace(/,/g, '');
            var format = numberWithCommas(val);
            $(this).val(format);
        })

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
        // form add new service for quotation
        $(".btnAddType").click(function () {
            var id = "";
            $("#example").find(".PatientCheckoutTr:last").clone(true).appendTo("#tableToModify");

            id = $("#example").find(".classSection:last").attr('rel');
            if (id == "") {
                var id = $("#example").find(".serviceId:last").text();
            } else {
                id++;
            }
            // update id in tr : last                        
            $("#example").find(".classSection:last").attr('id', 'ServiceSectionId' + id);
            $("#example").find(".classSection:last").attr('rel', id);
            $("#example").find(".patientCheckoutService:last").attr('id', 'CheckoutServiceId' + id);
            $("#example").find(".patientCheckoutService:last").attr('rel', id);
            $("#example").find(".classDoctor:last").attr('id', 'PatientIpdDoctorId' + id);
            $("#example").find(".qty:last").attr('id', 'CheckoutQty' + id);
            $("#example").find(".qty:last").attr('rel', id);
            $("#example").find(".discount:last").attr('id', 'CheckoutDiscount' + id);
            $("#example").find(".discount:last").attr('rel', id);
            $("#example").find(".checkout_unit_price:last").attr('id', 'CheckoutUnitPrice' + id);
            $("#example").find(".checkout_total_price:last").attr('id', 'CheckoutTotalPrice' + id);
            $("#example").find(".discount:last").val("");
            $("#example").find(".btnRemoveDiscountCheckOut:last").hide();
            $("#example").find(".PatientCheckoutTr:last").find("td .btnRemoveType").show();
            $(this).siblings(".btnRemoveType").show();
            $(this).hide();
            comboRefeshType();
            staffRefreshType();
            keyEventCheckout();
        });
        $(".btnRemoveType").click(function () {
            $(this).closest(".PatientCheckoutTr").remove();
            $("#example").find(".PatientCheckoutTr:last").find("td .btnAddType").show();
            if ($('#example .PatientCheckoutTr').length == 1) {
                $("#example").find(".PatientCheckoutTr:last").find("td .btnRemoveType").hide();
            }
            sortNuTableCheckOut();
            getTotalAmountPatientCheckOut();
            keyEventCheckout();
        });
        // clodse form service for quotation

        // get total amount of patient
        getTotalAmountPatientCheckOut();

        $(".btnBackCheckout").dblclick(function (event) {
            event.preventDefault();
            $('#CheckoutForm').validationEngine('hideAll');
            oCache.iCacheLower = -1;
            oTablePaymentList.fnDraw(false);
            var rightPanel = $(this).parent().parent().parent();
            var leftPanel = rightPanel.parent().find(".leftPanel");
            rightPanel.hide();
            rightPanel.html("");
            leftPanel.show("slide", {direction: "left"}, 500);
        });
        $(".float").autoNumeric();
        
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
        var formName = "#CheckoutForm";
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
    .first{
        text-align: center;
    }
</style>
<div style="padding: 5px;border: 1px dashed #3C69AD;">
    <div class="buttons">
        <a href="#" class="positive btnBackCheckout">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<div style="float: right; width: 165px; text-align: right; cursor: pointer; margin-right: 10px;" id="btnHideShowPatientInfo<?php echo $tblNameRadom; ?>">
    [ <span>Hide</span> Patient Info <img alt="" align="absmiddle" style="width: 16px; height: 16px;" src="<?php echo $this->webroot . 'img/button/arrow-up.png'; ?>" /> ]
</div>
<div style="clear: both;"></div>

<?php echo $this->Form->create('Checkout', array('id' => 'CheckoutForm', 'url' => '/cashiers/checkout/' . $this->params['pass'][0])); ?>

<input type="hidden" name="data[Patient][exchange_rate_id]" value="<?php echo getExchangeRateId(); ?>" />
<input type="hidden" name="data[Patient][exchange_rate]" value="<?php echo $exchangeRate; ?>" />
<input type="hidden" value="1" name="data[SalesOrder][currency_center_id]" id="OrderCurrencyCenterId" />
<input type="hidden" name="data[Patient][id]" value="<?php echo $patient['Patient']['id']; ?>" >
<input type="hidden" name="data[TmpService][id]" value="<?php echo $tmpService['TmpService']['id'] ?>" />
<fieldset id="patient_info<?php echo $tblNameRadom; ?>">
    <legend><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?></legend>
    <table class="table-dashbord" style="width: 100%;" cellspacing="3">
        <tr>
            <th style="width: 15%;"><?php __(PATIENT_CODE); ?></th>
            <td style="width: 35%;">: <?php echo $patient['Patient']['patient_code']; ?></td>
            <th style="width: 15%;"><?php __(TABLE_DOB); ?></th>
            <td style="width: 35%;">: 
                <?php echo date("d/m/Y", strtotime($patient['Patient']['dob'])); ?>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?php
                echo TABLE_AGE . ': ';
                if($patient['Patient']['dob']!="0000-00-00" || $patient['Patient']['dob']!=""){
                    echo getAgePatient($patient['Patient']['dob']);
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
            </td>
        </tr>
        <tr style="display: none;">
            <th><?php echo TABLE_COMPANY; ?> <span class="red">*</span> :</th>
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
            
            <?php if ($patient['PatientBillType']['id'] == 3) { ?>
                <th><?php echo TABLE_COMPANY_INSURANCE_NAME . ' <span class="red">*</span> :'; ?></th>
                <td colspan="3"><?php echo $this->Form->input('company_insurance_id', array('empty' => SELECT_OPTION, 'selected' => $patient['Patient']['company_insurance_id'], 'label' => false, 'class' => 'validate[required]', 'style' => 'width:200px;height: 35px;')); ?></td>
            <?php } ?>
        </tr>
        <tr>
            <th><?php echo GENERAL_TYPE; ?>  :</th>
            <td>
                <div class="inputContainer">
                    <select id="PatientTypePaymentId" class="classTypePayment" name="data[Patient][type_payment_id]" style="width:250px; height: 35px;">                    
                        <?php
                        foreach ($typePayments as $typePayment) {
                            if ($typePayment['TypePayment']['id'] == 1) {
                                echo '<option selected="selected" value="' . $typePayment['TypePayment']['id'] . '">' . $typePayment['TypePayment']['name'] . '</option>';
                            } else {
                                echo '<option value="' . $typePayment['TypePayment']['id'] . '">' . $typePayment['TypePayment']['name'] . '</option>';
                            }
                        }
                        ?>
                    </select>                
                </div>
            </td> 
            <th><?php __(TABLE_BILL_PAID_BY); ?></th>
            <td>
                : <?php echo $patient['PatientBillType']['name']; ?>            
            </td>
        </tr>
    </table>
</fieldset>
<br />

<?php
$index = "";
$totalPrice = 0;
if (!empty($salesOrders) || !empty($labos) || !empty($tmpService)) {
    ?>
    <span id="<?php echo $btnShowHide; ?>" class="btnShowHide" style="float: right;">[Hide]</span>
    <?php
     $index = 1;
}
?>
<div id="addPatientCheckOut" style="display:none;">    
    <table id="example" class="table" cellspacing="0">
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
            if (!empty($labos)) {
                $doctorId = "";
                foreach ($labos as $labo) {
                    if ($labo['Labo']['doctor_id'] != "") {
                        $doctorId = $labo['Labo']['doctor_id'];
                    } else {
                        $doctorId = $labo['QueuedLabo']['doctor_id'];
                    }
                    ?>
                    <tr style="background: #EDEEF0;"> 
                        <td class="first serviceId">
                            <?php //echo $index; ?>
                            <img src="<?php echo $this->webroot; ?>img/icon/blood_test.png" alt=""/>
                        </td>            
                        <td>
                            <div class="inputContainer">
                                <?php
                                $hospitalPrice = 0;
                                $unitPrice = number_format(0, 2);
                                $name = $labo['LaboItemGroup']['name'];
                                // patient have insurance company
                                if ($patient['Patient']['patient_bill_type_id'] == 3 && $patient['Patient']['company_insurance_id'] != 0) {

                                    $queryLaboPatientPrices = mysql_query("SELECT LaboItemPriceInsurance.id, unit_price FROM labo_item_price_insurances AS LaboItemPrice LEFT JOIN labo_item_price_insurance_patient_group_details AS LaboItemPriceInsurance ON LaboItemPrice.id = LaboItemPriceInsurance.labo_item_price_insurance_id "
                                            . " WHERE LaboItemPrice.labo_item_group_id = '" . $labo['LaboItemGroup']['id'] . "' AND LaboItemPrice.company_insurance_id ='" . $patient['Patient']['company_insurance_id'] . "' AND patient_group_id = '" . $patient['Patient']['patient_group_id'] . "' AND LaboItemPriceInsurance.is_active = 1 AND LaboItemPrice.is_active = 1");
                                    while ($resultLaboPatientPrice = mysql_fetch_array($queryLaboPatientPrices)) {
                                        $laboItemId = $resultLaboPatientPrice['id'];
                                        $unitPrice = number_format($resultLaboPatientPrice['unit_price'], 2);
                                        $totalPrice += $resultLaboPatientPrice['unit_price'];
                                    }
                                } else {

                                    $queryLaboPatientPrices = mysql_query("SELECT id, unit_price, hospital_price FROM labo_item_patient_groups "
                                            . " WHERE labo_item_group_id = '" . $labo['LaboItemGroup']['id'] . "' AND patient_group_id = '" . $patient['Patient']['patient_group_id'] . "' AND is_active = 1");
                                    while ($resultLaboPatientPrice = mysql_fetch_array($queryLaboPatientPrices)) {
                                        $laboItemId = $resultLaboPatientPrice['id'];
                                        $unitPrice = number_format($resultLaboPatientPrice['unit_price'], 2);
                                        $hospitalPrice = $resultLaboPatientPrice['hospital_price'];
                                        $totalPrice += $resultLaboPatientPrice['unit_price'];
                                    }
                                }
                                ?>
                                <select id="ServiceSectionId<?php echo $index; ?>" rel="<?php echo $index; ?>" class="validate[required]" name="data[Patient][section_id][]" style="width:160px;">
                                    <option value="labo"><?php echo 'Labo'; ?></option>                                        
                                </select>
                            </div>                    
                        </td>
                        <td>
                            <select id="CheckoutServiceId<?php echo $index; ?>" style="width:160px;" name="data[Patient][service_id][]" class="validate[required]">
                                <option selected="selected" value="<?php echo $labo['LaboItemGroup']['id']; ?>"><?php echo $name; ?></option>
                            </select>                                
                        </td>
                        <td>
                            <select id="PatientIpdDoctorId<?php echo $index; ?>" style="width:130px;" name="data[Patient][doctor_id][]" class="classDoctor validate[required]">
                                <option value=""><?php echo SELECT_OPTION; ?></option>
                                <?php
                                foreach ($doctors as $doctor) {
                                    $selected = "";
                                    if ($doctorId == $doctor['User']['id']) {
                                        echo '<option selected="selected" class="' . $doctor['Company']['id'] . '" value="' . $doctor['User']['id'] . '">' . $doctor['Employee']['name'] . '</option>';
                                    } else {
                                        echo '<option class="' . $doctor['Company']['id'] . '" value="' . $doctor['User']['id'] . '">' . $doctor['Employee']['name'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                            <?php echo $this->Form->text('qty', array('id' => 'CheckoutQty' . $index, 'name' => 'data[Patient][qty][]', 'class' => 'qty integer validate[required]', 'style' => 'width:50px;', 'rel' => $index, 'value' => 1, 'autocomplete' => 'off')); ?> 
                        </td>
                        <td>
                            <?php echo $this->Form->text('unit_price', array('id' => 'CheckoutUnitPrice' . $index, 'name' => 'data[Patient][unit_price][]', 'class' => 'checkout_unit_price float validate[required]', 'readonly' => true, 'style' => 'width:100px;', 'value' => $unitPrice)); ?> 
                            <input type="hidden" name="data[Patient][hospital_price][]" value="<?php echo $hospitalPrice;?>" />
                        </td>
                        <td>
                            <?php
                            if ($allowProductDiscount) {
                                ?>
                                <input type="hidden" name="discount_amount[]" value="0" />
                                <input type="hidden" name="discount_percent[]" value="0" />
                                <input type="text" id="CheckoutDiscount<?php echo $index; ?>" class="float discount btnDiscountCheckOut" name="data[Patient][discount][]" style="width: 50px;" readonly="readonly" rel="<?php echo $index; ?>"/>
                                <img alt="Remove" src="<?php echo $this->webroot . 'img/button/cross.png'; ?>" class="btnRemoveDiscountCheckOut" align="absmiddle" style="cursor: pointer; display: none"  onmouseover="Tip('Remove')" />
                                <?php
                            } else {
                                ?>
                                <input type="hidden" id="CheckoutDiscount<?php echo $index; ?>" class="float discount btnDiscountCheckOut" name="data[Patient][discount][]" style="width: 50px;" readonly="readonly" rel="<?php echo $index; ?>"/>
                                <?php
                            }
                            ?>                    
                        </td>
                        <td>
                            <?php echo $this->Form->text('total_price', array('id' => 'CheckoutTotalPrice' . $index, 'name' => 'data[Patient][total_price][]', 'class' => 'checkout_total_price', 'style' => 'width:154px;', 'readonly' => true, 'value' => $unitPrice)); ?>
                        </td> 
                        <td>&nbsp;</td>
                    </tr>
                    <?php
                    $index++;
                }
            }
            ?>
            <?php
            if (!empty($salesOrders)) {
                foreach ($salesOrders as $salesOrder) {
                    ?>
                    <tr style="background: #EDEEF0;">
                        <td class="first serviceId">
                            <?php //echo $index; ?>
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
                                $orderDoctorId = "";
                                $queryCheckDoctor = mysql_query("SELECT created_by FROM orders WHERE status >=1 AND queue_doctor_id = {$salesOrder['SalesOrder']['queue_doctor_id']} LIMIT 1");
                                while ($rowCheckDoctor = mysql_fetch_array($queryCheckDoctor)) {
                                    $orderDoctorId = $rowCheckDoctor['created_by'];
                                }
                                foreach ($doctors as $doctor) {
                                    if ($orderDoctorId == $doctor['User']['id']) {
                                        echo '<option selected="selected" class="' . $doctor['Company']['id'] . '" value="' . $doctor['User']['id'] . '">' . $doctor['Employee']['name'] . '</option>';
                                    } else {
                                        echo '<option class="' . $doctor['Company']['id'] . '" value="' . $doctor['User']['id'] . '">' . $doctor['Employee']['name'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                            <?php echo $this->Form->text('qty', array('id' => 'CheckoutQty' . $index, 'name' => 'data[Patient][qty][]', 'class' => 'validate[required]', 'style' => 'width:50px; text-align:center;', 'readonly' => true, 'rel' => $index, 'value' => 1, 'autocomplete' => 'off')); ?> 
                        </td>
                        <td>
                            <?php echo $this->Form->text('unit_price', array('id' => 'CheckoutUnitPrice' . $index, 'name' => 'data[Patient][unit_price][]', 'class' => 'checkout_unit_price float validate[required]', 'readonly' => true, 'style' => 'width:100px;', 'value' => $salesOrder['SalesOrder']['balance'])); ?> 
                            <input type="hidden" name="data[Patient][hospital_price][]" value="0" />
                        </td>
                        <td>
                            <?php
                            if ($allowProductDiscount) {
                                ?>
                                <input type="hidden" name="discount_amount[]" value="0" />
                                <input type="hidden" name="discount_percent[]" value="0" />
                                <input type="text" id="CheckoutDiscount<?php echo $index; ?>" class="float discount btnDiscountCheckOut" name="data[Patient][discount][]" style="width: 50px;" readonly="readonly" rel="<?php echo $index; ?>" value="<?php
                                if ($salesOrder['SalesOrder']['discount'] > 0) {
                                    echo $salesOrder['SalesOrder']['discount'];
                                };
                                ?>"/>
                                <img alt="Remove" src="<?php echo $this->webroot . 'img/button/cross.png'; ?>" class="btnRemoveDiscountCheckOut" align="absmiddle" style="cursor: pointer; display: none"  onmouseover="Tip('Remove')" />
                                <?php
                            } else {
                                ?>
                                <input type="hidden" id="CheckoutDiscount<?php echo $index; ?>" class="float discount btnDiscountCheckOut" name="data[Patient][discount][]" style="width: 50px;" readonly="readonly" rel="<?php echo $index; ?>" value="<?php
                                if ($salesOrder['SalesOrder']['discount'] > 0) {
                                    echo $salesOrder['SalesOrder']['discount'];
                                };
                                ?>"/>
                                       <?php
                                   }
                                   ?>                    
                        </td>
                        <td>
                            <?php echo $this->Form->text('total_price', array('id' => 'CheckoutTotalPrice' . $index, 'name' => 'data[Patient][total_price][]', 'class' => 'checkout_total_price', 'style' => 'width:154px;', 'readonly' => true, 'value' => $salesOrder['SalesOrder']['balance'])); ?>
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
            
        <?php 
            $checkValidate = "validate[required]";
            if(!empty($tmpService)){    
                $checkValidate = "";
                $queryTmpServiceDetails = mysql_query("SELECT * FROM tmp_service_details sd WHERE sd.tmp_service_id = " . $tmpService['TmpService']['id']) ; 
                while ($resultTmpService = mysql_fetch_array($queryTmpServiceDetails)) {
                ?>
                <tr style="background: #EDEEF0;">
                    <td class="first serviceId">
                        <img src="<?php echo $this->webroot; ?>img/icon/doctor.png" alt=""/>
                    </td>  
                    <td>
                        <div class="inputContainer">
                            <?php
                            $query = mysql_query("SELECT sections.id, sections.name, section_companies.company_id  FROM `sections` INNER JOIN section_companies ON sections.id = section_companies.section_id 
                                                            INNER JOIN services ON services.section_id = sections.id
                                                            WHERE sections.is_active = 1 AND sections.id IN (SELECT section_id FROM section_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = '" . $user['User']['id'] . "')) AND services.id = "  .$resultTmpService['service_id'])  ;
                            ?>
                            <select id="ServiceSectionId<?php echo $index; ?>" rel="<?php echo $index; ?>" class="classSection validate[required]" name="data[Patient][section_id][]" style="width:160px;">
                                <?php
                                while ($row = mysql_fetch_array($query)) {
                                    echo '<option class="' . $row['company_id'] . '" value="' . $row['id'] . '"  selected="selected">' . $row['name'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>    
                    </td>
                    <td>
                        <div class="inputContainer">
                            <?php
                            $query = mysql_query("SELECT * FROM services WHERE services.is_active = 1 AND services.id = "  .$resultTmpService['service_id'])  ;
                            ?>
                            <select id="ServiceSectionId<?php echo $index; ?>" rel="<?php echo $index; ?>" class="classSection validate[required]" name="data[Patient][service_id][]" style="width:160px;">
                                <?php
                                while ($row = mysql_fetch_array($query)) {
                                    echo '<option class="' . $row['company_id'] . '" value="' . $row['id'] . '" >' . $row['name'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>  

                    </td>
                    <td>
                        <select id="PatientIpdDoctorId<?php echo $index; ?>" style="width:130px;" name="data[Patient][doctor_id][]" class="classDoctor validate[required]">
                            <option value=""><?php echo SELECT_OPTION; ?></option>
                            <?php
                            foreach ($doctors as $doctor) {
                                $selected = "";
                                if ($resultTmpService['doctor_id'] == $doctor['User']['id']) {
                                    echo '<option selected="selected" class="' . $doctor['Company']['id'] . '" value="' . $doctor['User']['id'] . '">' . $doctor['Employee']['name'] . '</option>';
                                } else {
                                    echo '<option class="' . $doctor['Company']['id'] . '" value="' . $doctor['User']['id'] . '">' . $doctor['Employee']['name'] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <?php echo $this->Form->text('qty', array('id' => 'CheckoutQty' . $index, 'name' => 'data[Patient][qty][]', 'class' => 'validate[required]', 'style' => 'width:50px; text-align:center;', 'readonly' => true, 'rel' => $index, 'value' => $resultTmpService['qty'], 'autocomplete' => 'off')); ?> 
                    </td>
                    <td>
                        <?php echo $this->Form->text('unit_price', array('id' => 'CheckoutUnitPrice' . $index, 'name' => 'data[Patient][unit_price][]', 'class' => 'checkout_unit_price float validate[required]', 'readonly' => true, 'style' => 'width:100px;', 'value' => number_format($resultTmpService['unit_price'] , 2) )); ?> 
                        <input type="hidden" name="data[Patient][hospital_price][]" value="0" />
                    </td>
                    <td>
                        <?php
                        if ($allowProductDiscount) {
                            ?>
                            <input type="hidden" name="discount_amount[]" value="0" />
                            <input type="hidden" name="discount_percent[]" value="0" />
                            <input type="text" id="CheckoutDiscount<?php echo $index; ?>" class="float discount btnDiscountCheckOut" name="data[Patient][discount][]" style="width: 50px;" readonly="readonly" rel="<?php echo $index; ?>" value="<?php
                            if ($resultTmpService['discount'] > 0) {
                                echo $resultTmpService['discount'];
                            };
                            ?>"/>
                            <img alt="Remove" src="<?php echo $this->webroot . 'img/button/cross.png'; ?>" class="btnRemoveDiscountCheckOut" align="absmiddle" style="cursor: pointer; display: none"  onmouseover="Tip('Remove')" />
                            <?php
                        } else {
                            ?>
                            <input type="hidden" id="CheckoutDiscount<?php echo $index; ?>" class="float discount btnDiscountCheckOut" name="data[Patient][discount][]" style="width: 50px;" readonly="readonly" rel="<?php echo $index; ?>" value="<?php
                            if ($resultTmpService['discount'] > 0) {
                                echo $resultTmpService['discount'];
                            };
                            ?>"/>
                        <?php
                            }
                        ?>                    
                    </td>
                    <td>
                        <?php echo $this->Form->text('total_price', array('id' => 'CheckoutTotalPrice' . $index, 'name' => 'data[Patient][total_price][]', 'class' => 'checkout_total_price', 'style' => 'width:154px;', 'readonly' => true, 'value' => number_format($resultTmpService['total_price'], 2))); ?>
                    </td> 
                    <td>&nbsp;</td>
                </tr>
                <?php 
                    $index++; 
                }  
            }  
        ?>
        <tr class="PatientCheckoutTr">
            <td class="first serviceId"><?php
                if ($index == "") {
                    echo '1';
                } else {
                    echo $index;
                }
                ?></td>            
            <td>
                <div class="inputContainer">
                    <?php
                    $query = mysql_query("SELECT sections.id, sections.name, section_companies.company_id  FROM `sections` INNER JOIN section_companies ON sections.id = section_companies.section_id 
                                                    WHERE sections.is_active = 1 AND sections.id IN (SELECT section_id FROM section_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = '" . $user['User']['id'] . "')) ORDER BY name ASC")
                    ?>
                    <select id="ServiceSectionId<?php echo $index; ?>" rel="<?php echo $index; ?>" class="classSection <?php echo $checkValidate;?>" name="data[Patient][section_id][]" style="width:160px;">
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
                <?php echo $this->Form->input('service_id', array('id' => 'CheckoutServiceId' . $index, 'name' => 'data[Patient][service_id][]', 'empty' => SELECT_OPTION, 'label' => false, 'class' => 'patientCheckoutService '.$checkValidate, 'style' => 'width:160px;', 'rel' => $index)); ?>
            </td>
            <td>
                <select id="PatientIpdDoctorId<?php echo $index; ?>" style="width:130px;" name="data[Patient][doctor_id][]" class="classDoctor <?php echo $checkValidate;?>">
                    <option value=""><?php echo SELECT_OPTION; ?></option>
                    <?php
                    foreach ($doctors as $doctor) {
                        echo '<option class="' . $doctor['Company']['id'] . '" value="' . $doctor['User']['id'] . '">' . $doctor['Employee']['name'] . '</option>';
                    }
                    ?>
                </select>
            </td>
            <td>
                <?php echo $this->Form->text('qty', array('id' => 'CheckoutQty' . $index, 'name' => 'data[Patient][qty][]', 'class' => 'qty integer'.$checkValidate, 'style' => 'width:50px;', 'rel' => $index, 'autocomplete' => 'off')); ?> 
            </td>
            <td>
                <?php echo $this->Form->text('unit_price', array('id' => 'CheckoutUnitPrice' . $index, 'name' => 'data[Patient][unit_price][]', 'class' => 'checkout_unit_price float'.$checkValidate, 'readonly' => true, 'style' => 'width:100px;')); ?> 
                <input type="hidden" name="data[Patient][hospital_price][]" value="0" />
            </td>
            <td>
                <?php
                if ($allowProductDiscount) {
                    ?>
                    <input type="hidden" name="discount_amount[]" value="0" />
                    <input type="hidden" name="discount_percent[]" value="0" />
                    <input type="text" id="CheckoutDiscount<?php echo $index; ?>" class="float discount btnDiscountCheckOut" name="data[Patient][discount][]" style="width: 50px;" readonly="readonly" rel="<?php echo $index; ?>"/>
                    <img alt="Remove" src="<?php echo $this->webroot . 'img/button/cross.png'; ?>" class="btnRemoveDiscountCheckOut" align="absmiddle" style="cursor: pointer; display: none"  onmouseover="Tip('Remove')" />
                    <?php
                } else {
                    ?>
                    <input type="hidden" id="CheckoutDiscount<?php echo $index; ?>" class="float discount btnDiscountCheckOut" name="data[Patient][discount][]" style="width: 50px;" readonly="readonly" rel=""/>
                    <?php
                }
                ?>                       
            </td>
            <td>
                <?php echo $this->Form->text('total_price', array('id' => 'CheckoutTotalPrice' . $index, 'name' => 'data[Patient][total_price][]', 'class' => 'checkout_total_price', 'style' => 'width:154px;', 'readonly' => true)); ?>
            </td>
            <td style="padding: 5px 5px 5px 5px !important;;">
                <img alt="" src="<?php echo $this->webroot; ?>img/button/plus.png" class="btnAddType" style="cursor: pointer;" />
                <img alt="" src="<?php echo $this->webroot; ?>img/button/cross.png" class="btnRemoveType" style="cursor: pointer;display: none;" />
            </td>
        </tr>  
        </tbody>
        <tr>
            <td class="first" style="text-align: right;" colspan="7"><label for="PatientTotalAmount">Sub Total ($):</label></td>
            <td>
                <input type="text" id="PatientTotalAmount" value="<?php echo number_format($totalPrice, 2); ?>" class="validate[required]" readonly="readonly" style="width:154px; height: 30px;font-weight: bold;" name="data[Patient][total_amount]">
            </td>
        </tr>
        <tr>
            <td class="first" style="text-align: right;" colspan="7"><label for="PatientDiscountTotal">Total Discount ($):</label></td>
            <td style="height: 30px;">
                <?php
                if ($allowProductDiscount) {
                    ?>
                    <input type="text" id="PatientDiscountTotal" value="0.00" class="float btnDiscountTotal" style="width:154px; height: 30px;font-weight: bold;" name="data[Patient][total_discount]" readonly="readonly">                    
                    <input type="hidden" id="PatientDiscountTotalP" value="0.00" name="data[Patient][total_discount_per]">   
                    <img alt="Remove" src="<?php echo $this->webroot . 'img/button/cross.png'; ?>" class="btnRemoveDiscountTotal" align="absmiddle" style="cursor: pointer; display: none"  onmouseover="Tip('Remove')" />
                    <?php
                } else {
                    ?>  
                    <input type="hidden" id="PatientDiscountTotal" value="0.00" class="float" style="width:154px; height: 30px;font-weight: bold;" name="data[Patient][total_discount]" readonly="readonly">                    
                    <?php
                }
                ?>                 
            </td>
        </tr>
        <tr>
            <td class="first" style="text-align: right;" colspan="7"><label for="PatientChartAccountId"><?php echo 'Deposit To'; ?> <span class="red">*</span> :</label></td>
            <td>
                <?php
                $filter = "AND chart_account_type_id IN (1)";
                $query = array();
                ?>
                <div class="inputContainer">
                    <select id="PatientChartAccountId" name="data[Patient][chart_account_cash_id]" class="patient_coa_id validate[required]" style="width: 160px;" disabled="disabled">
                        <option value=""><?php echo SELECT_OPTION; ?></option>
                        <?php
                        $query[0] = mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE ISNULL(parent_id) AND is_active=1 " . $filter . " ORDER BY account_codes");
                        while ($data[0] = mysql_fetch_array($query[0])) {
                            $queryIsNotLastChild = mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=" . $data[0]['id']);
                            ?>
                            <option value="<?php echo $data[0]['id']; ?>" chart_account_type_name="<?php echo $data[0]['chart_account_type_name']; ?>" company_id="<?php echo $data[0]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild) ? 'disabled="disabled"' : ''; ?> <?php echo $data[0]['id'] == $cashBankAccountId ? 'selected="selected"' : ''; ?>><?php echo $data[0]['name']; ?></option>
                            <?php
                            $query[1] = mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=" . $data[0]['id'] . " AND is_active=1 " . $filter . " ORDER BY account_codes");
                            while ($data[1] = mysql_fetch_array($query[1])) {
                                $queryIsNotLastChild = mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=" . $data[1]['id']);
                                ?>
                                <option value="<?php echo $data[1]['id']; ?>" chart_account_type_name="<?php echo $data[1]['chart_account_type_name']; ?>" company_id="<?php echo $data[1]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild) ? 'disabled="disabled"' : ''; ?> <?php echo $data[1]['id'] == $cashBankAccountId ? 'selected="selected"' : ''; ?> style="padding-left: 25px;"><?php echo $data[1]['name']; ?></option>
                                <?php
                                $query[2] = mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=" . $data[1]['id'] . " AND is_active=1 " . $filter . " ORDER BY account_codes");
                                while ($data[2] = mysql_fetch_array($query[2])) {
                                    $queryIsNotLastChild = mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=" . $data[2]['id']);
                                    ?>
                                    <option value="<?php echo $data[2]['id']; ?>" chart_account_type_name="<?php echo $data[2]['chart_account_type_name']; ?>" company_id="<?php echo $data[2]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild) ? 'disabled="disabled"' : ''; ?> <?php echo $data[2]['id'] == $cashBankAccountId ? 'selected="selected"' : ''; ?> style="padding-left: 50px;"><?php echo $data[2]['name']; ?></option>
                                    <?php
                                    $query[3] = mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=" . $data[2]['id'] . " AND is_active=1 " . $filter . " ORDER BY account_codes");
                                    while ($data[3] = mysql_fetch_array($query[3])) {
                                        $queryIsNotLastChild = mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=" . $data[3]['id']);
                                        ?>
                                        <option value="<?php echo $data[3]['id']; ?>" chart_account_type_name="<?php echo $data[3]['chart_account_type_name']; ?>" company_id="<?php echo $data[3]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild) ? 'disabled="disabled"' : ''; ?> <?php echo $data[3]['id'] == $cashBankAccountId ? 'selected="selected"' : ''; ?> style="padding-left: 75px;"><?php echo $data[3]['name']; ?></option>
                                        <?php
                                        $query[4] = mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=" . $data[3]['id'] . " AND is_active=1 " . $filter . " ORDER BY account_codes");
                                        while ($data[4] = mysql_fetch_array($query[4])) {
                                            $queryIsNotLastChild = mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=" . $data[4]['id']);
                                            ?>
                                            <option value="<?php echo $data[4]['id']; ?>" chart_account_type_name="<?php echo $data[4]['chart_account_type_name']; ?>" company_id="<?php echo $data[4]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild) ? 'disabled="disabled"' : ''; ?> <?php echo $data[4]['id'] == $cashBankAccountId ? 'selected="selected"' : ''; ?> style="padding-left: 100px;"><?php echo $data[4]['name']; ?></option>
                                            <?php
                                            $query[5] = mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=" . $data[4]['id'] . " AND is_active=1 " . $filter . " ORDER BY account_codes");
                                            while ($data[5] = mysql_fetch_array($query[5])) {
                                                $queryIsNotLastChild = mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=" . $data[5]['id']);
                                                ?>
                                                <option value="<?php echo $data[5]['id']; ?>" chart_account_type_name="<?php echo $data[5]['chart_account_type_name']; ?>" company_id="<?php echo $data[5]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild) ? 'disabled="disabled"' : ''; ?> <?php echo $data[5]['id'] == $cashBankAccountId ? 'selected="selected"' : ''; ?> style="padding-left: 125px;"><?php echo $data[5]['name']; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </select>
                    <input type="hidden" id="chartAccPatientCoa" name="data[Patient][chart_account_cash_id]" >
                </div>
            </td>
        </tr>
        <tr>
            <td class="first" style="text-align: right;" colspan="7"><label for="PatientTotalAmountPaid">Total Paid ($):</label></td>
            <td>
                <input type="hidden" value="0.00" name="data[Patient][total_amount_paid]">
                <input type="text" id="PatientTotalAmountPaid" value="0.00" class="float validate[required]" style="width:154px; height: 30px;font-weight: bold;" autocomplete="off">
            </td>
        </tr>
        <tr>
            <td class="first" style="text-align: right;" colspan="7"><label for="PatientTotalAmountPaidRiel">Total Paid (៛):</label></td>
            <td>
                <input type="text" id="PatientTotalAmountPaidRiel" value="0.00" class="float validate[required] PatientTotalAmountPaidRiel" style="width:154px; height: 30px;font-weight: bold;" autocomplete="off">
            </td>
        </tr>
        <tr>
            <td class="first" style="text-align: right;" colspan="7"><label for="PatientSubTotalAmount">Balance ($):</label></td>
            <td>
                <input type="text" id="PatientSubTotalAmount" value="<?php echo number_format($totalPrice, 2); ?>" style="width:154px; height: 30px;font-weight: bold;" class="validate[required]" readonly="readonly" name="data[Patient][sub_total_amount]">                    
            </td>
        </tr>
        <tr>
            <td class="first" style="text-align: right;" colspan="7"><label for="PatientSubTotalAmountRiel">Balance (៛):</label></td>
            <td>
                <input type="text" id="PatientSubTotalAmountRiel" value="<?php echo number_format($totalPrice * $exchangeRate); ?>" style="width:154px; height: 30px;font-weight: bold;" class="validate[required]" readonly="readonly">                    
            </td>
        </tr>
        <tr>
            <td class="first" style="text-align: right;" colspan="7"><label for="PatientChartAccountId">A/R <span class="red">*</span> :</label></td>
            <td>
                <div class="inputContainer">                            
                    <?php
                    $filter = "AND chart_account_type_id IN (2)";
                    $query = array();
                    ?>
                    <select id="PatientChartAccountId" name="data[Patient][chart_account_id]" class="sales_order_coa_id validate[required]" style="width:160px; height: 30px;" disabled="disabled">
                        <option value=""><?php echo SELECT_OPTION; ?></option>
                        <?php
                        $query[0] = mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE is_active=1 " . $filter . " ORDER BY account_codes");
                        while ($data[0] = mysql_fetch_array($query[0])) {
                            $queryIsNotLastChild = mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=" . $data[0]['id']);
                            ?>
                            <option value="<?php echo $data[0]['id']; ?>" chart_account_type_name="<?php echo $data[0]['chart_account_type_name']; ?>" company_id="<?php echo $data[0]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild) ? 'disabled="disabled"' : ''; ?> <?php echo $data[0]['id'] == $arAccountId ? 'selected="selected"' : ''; ?>><?php echo $data[0]['name']; ?></option>
                            <?php
                            $query[1] = mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=" . $data[0]['id'] . " AND is_active=1 " . $filter . " ORDER BY account_codes");
                            while ($data[1] = mysql_fetch_array($query[1])) {
                                $queryIsNotLastChild = mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=" . $data[1]['id']);
                                ?>
                                <option value="<?php echo $data[1]['id']; ?>" chart_account_type_name="<?php echo $data[1]['chart_account_type_name']; ?>" company_id="<?php echo $data[1]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild) ? 'disabled="disabled"' : ''; ?> <?php echo $data[1]['id'] == $arAccountId ? 'selected="selected"' : ''; ?> style="padding-left: 25px;"><?php echo $data[1]['name']; ?></option>
                                <?php
                                $query[2] = mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=" . $data[1]['id'] . " AND is_active=1 " . $filter . " ORDER BY account_codes");
                                while ($data[2] = mysql_fetch_array($query[2])) {
                                    $queryIsNotLastChild = mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=" . $data[2]['id']);
                                    ?>
                                    <option value="<?php echo $data[2]['id']; ?>" chart_account_type_name="<?php echo $data[2]['chart_account_type_name']; ?>" company_id="<?php echo $data[2]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild) ? 'disabled="disabled"' : ''; ?> <?php echo $data[2]['id'] == $arAccountId ? 'selected="selected"' : ''; ?> style="padding-left: 50px;"><?php echo $data[2]['name']; ?></option>
                                    <?php
                                    $query[3] = mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=" . $data[2]['id'] . " AND is_active=1 " . $filter . " ORDER BY account_codes");
                                    while ($data[3] = mysql_fetch_array($query[3])) {
                                        $queryIsNotLastChild = mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=" . $data[3]['id']);
                                        ?>
                                        <option value="<?php echo $data[3]['id']; ?>" chart_account_type_name="<?php echo $data[3]['chart_account_type_name']; ?>" company_id="<?php echo $data[3]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild) ? 'disabled="disabled"' : ''; ?> <?php echo $data[3]['id'] == $arAccountId ? 'selected="selected"' : ''; ?> style="padding-left: 75px;"><?php echo $data[3]['name']; ?></option>
                                        <?php
                                        $query[4] = mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=" . $data[3]['id'] . " AND is_active=1 " . $filter . " ORDER BY account_codes");
                                        while ($data[4] = mysql_fetch_array($query[4])) {
                                            $queryIsNotLastChild = mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=" . $data[4]['id']);
                                            ?>
                                            <option value="<?php echo $data[4]['id']; ?>" chart_account_type_name="<?php echo $data[4]['chart_account_type_name']; ?>" company_id="<?php echo $data[4]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild) ? 'disabled="disabled"' : ''; ?> <?php echo $data[4]['id'] == $arAccountId ? 'selected="selected"' : ''; ?> style="padding-left: 100px;"><?php echo $data[4]['name']; ?></option>
                                            <?php
                                            $query[5] = mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=" . $data[4]['id'] . " AND is_active=1 " . $filter . " ORDER BY account_codes");
                                            while ($data[5] = mysql_fetch_array($query[5])) {
                                                $queryIsNotLastChild = mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=" . $data[5]['id']);
                                                ?>
                                                <option value="<?php echo $data[5]['id']; ?>" chart_account_type_name="<?php echo $data[5]['chart_account_type_name']; ?>" company_id="<?php echo $data[5]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild) ? 'disabled="disabled"' : ''; ?> <?php echo $data[5]['id'] == $arAccountId ? 'selected="selected"' : ''; ?> style="padding-left: 125px;"><?php echo $data[5]['name']; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </select>
                    <input type="hidden" id="chartAccSalesOrderCoa" name="data[Patient][chart_account_id]" >
                </div>
            </td>
        </tr>
    </table>    
</div>
<div class="clear"></div>
<div class="buttons">
    <button type="submit" class="positive saveCheckOut" >
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <span class="txtSaveCheckout"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>

<?php echo $this->Form->end(); ?>
