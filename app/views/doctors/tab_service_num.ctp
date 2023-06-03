<?php
if (empty($tmpService)) {
    echo GENERAL_NO_RECORD;
    exit();
}
require_once("includes/function.php");
?>
<?php
$absolute_url = FULL_BASE_URL . Router::url("/", false);
$tblName = "tbl123";
$exchangeRate = 4150;
$tblNameRadom = "tbl" . rand();
$rnd = rand();
$btnShowHide = "btnShowHide" . $rnd;
$formFilter = "PatientCheckoutTr";
?>
<?php echo $javascript->link('jquery.form'); ?>
<style type="text/css">
    div.checkbox{
        width: 30px;
    }
</style>
<?php $tblName = "tbl123"; ?>
<script type="text/javascript">
    $(document).ready(function () {
        $(".TmpServiceEditForm").validationEngine();
        $(".TmpServiceEditForm").ajaxForm({
            beforeSubmit: function (arr, $form, options) {
                $(".txtSavePatient").html("<?php echo ACTION_LOADING; ?>");
                $(".loading").show();
            },
            success: function (result) {
                $(".loading").hide();
                $("#tabs3").tabs("select", 7);
                $("#tabServiceNum<?php echo $tblName; ?>").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabServiceNum/<?php echo $this->params['pass'][0] . '/' . $this->params['pass'][1]; ?>");
                    }
            
            });

            $("#tmpService").accordion({
                collapsible: true,
                autoHeight: false,
                navigation: false,
                active: false
            });

            $(".btnPrint").click(function (event) {
                event.preventDefault();
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                event.stopPropagation();
                var btnPatientConsultation = $("#dialogPrint<?php echo $tblName; ?>").html();
                var patientConsultationId = $(this).attr('patientConsultationId');
                var queuedDoctorId = $(this).attr('queuedDoctorId');
                var queueId = $(this).attr('queueId');
                var name = $(this).attr('name');
                $("#patientConsultation").load("<?php echo $absolute_url . $this->params['controller']; ?>/printOtherService/" + patientConsultationId + "/" + queuedDoctorId + "/" + queueId);
                $("#dialogPrint<?php echo $tblName; ?>").html(btnPatientConsultation);
                $("#dialogPrint<?php echo $tblName; ?>").dialog({
                    title: '<?php echo ACTION_PRINT_DOCTOR_OTHER_SERVICE; ?>',
                    resizable: false,
                    modal: true,
                    buttons: {
                        Ok: function () {
                            $(this).dialog("close");
                        }
                    }
                });
                $("#btnPatientConsultation<?php echo $tblName; ?>").click(function () {
                    w = window.open();
                    w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                    w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                    w.document.write('<style type="text/css">.info th{font-size: 12px;}.info td{font-size: 12px;}.table th{font-size: 12px;}.table td{font-size: 12px;}</style>');
                    w.document.write($("#patientConsultation").html());
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
                });
            });

            $(".legend_content").show();
            $(".legend_title").click(function () {
                $(this).siblings(".legend_content").slideToggle();
            });
            //$(".chzn-select").chosen(); 
            $(".PatientTypePaymentId").chosen({width: 250});
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
                beforeSubmit: function (arr, $form, options) {
                    $(".txtSavePatient").html("<?php echo ACTION_LOADING; ?>");
                    $(".loading").show();
                },
                success: function (result) {
                    $(".loading").hide();
                    $("#tabs3").tabs("select", 9);
                    $("#tabServiceNum<?php echo $tblName; ?>").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabServiceNum/<?php echo $this->params['pass'][0] . '/' . $this->params['pass'][1]; ?>");
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
            if ($(".PatientCompanyId").val() == '') {
                $("#addPatientCheckOut").show();
                $(".classSection").closest("tr").find("td .classSection").val('');
                $(".classSection").closest("tr").find("td .classSection option[class!='']").hide();
                $(".classSection").closest("tr").find("td .classSection option[class='" + $(".PatientCompanyId").val() + "']").show();
            }
            // for sort section in company
            $(".classCompany").change(function () {
                if ($(".PatientCompanyId").val() != '' && $("#PatientPatientGroupId").val() != "" && $("#CheckoutCompanyInsuranceId").val() != "") {
                    $("#addPatientCheckOut").show();
                    $(".classSection").closest("tr").find("td .classSection").val('');
                    $(".classSection").closest("tr").find("td .classSection option[class!='']").hide();
                    $(".classSection").closest("tr").find("td .classSection option[class='" + $(".PatientCompanyId").val() + "']").show();
                } else {
                    $(".classSection").closest("tr").find("td .classSection option[class!='']").show();
                    $("#addPatientCheckOut").hide();
                }
                comboRefesh();
            });
            $("#CheckoutCompanyInsuranceId").change(function () {
                if ($(".PatientCompanyId").val() != '' && $("#PatientPatientGroupId").val() != "" && $("#CheckoutCompanyInsuranceId").val() != "") {
                    $("#addPatientCheckOut").show();
                    $(".classSection").closest("tr").find("td .classSection").val('');
                    $(".classSection").closest("tr").find("td .classSection option[class!='']").hide();
                    $(".classSection").closest("tr").find("td .classSection option[class='" + $(".PatientCompanyId").val() + "']").show();
                } else {
                    $("#addPatientCheckOut").hide();
                }
            });

            $("#SerNumTotalAmountPaid").blur(function () {
                if ($(this).val() == "") {
                    $(this).val('0.00');
                    getTotalAmountPatientCheckOut();
                }
            });
            $("#SerNumTotalAmountPaid").live('click', function () {
                $("#SerNumTotalAmountPaid").val("");
            });
            $("#SerNumTotalAmountPaid").live('keyup', function () {
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
                $("#example").find(".unit_price:last").attr('id', 'CheckoutUnitPrice' + id);
                $("#example").find(".unit_price:last").attr('rel', id);
                $("#example").find(".srv_total_price:last").attr('id', 'CheckoutTotalPrice' + id);
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

            $(".float").autoNumeric();

    });
    
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
        if (companyInsuranceId == undefined) {
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
        if (companyInsuranceId == undefined) {
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
            var totalPrice = Number($(this).val()) * $("#CheckoutQty" + id).val();
            $('#CheckoutTotalPrice' + id).val(Number(totalPrice).toFixed(2));
            getTotalAmountPatientCheckOut();
        }
    });
    $(".unit_price").live('keyup', function () {
        var id = $(this).attr('rel');         
        if ($(this).val() != "") {
            var totalPrice = Number($(this).val()) * $("#CheckoutQty" + id).val();
            $('#CheckoutTotalPrice' + id).val(Number(totalPrice).toFixed(2));
            getTotalAmountPatientCheckOut();
        }
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
        var discountAmount = replaceNum($(this).closest("tr").find("input[name='discount_amount[]']").val());

        if (discountPercent != 0 && discountPercent != '') {
            var totalDiscountPer = discountPercent * qty;
            $(this).closest("tr").find("input[name='data[Patient][discount][]']").val(totalDiscountPer);
            var totalPrice = (Number($("#CheckoutUnitPrice" + id).val()) * $(this).val()) - totalDiscountPer;
        } else {
            var totalPrice = (Number($("#CheckoutUnitPrice" + id).val()) * $(this).val()) - discount;
        }

        //var totalPrice = (Number($("#CheckoutUnitPrice" + id).val()) * $(this).val()) - discount;
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
        $(".unit_price").each(function () {
            $("#example").find(".unit_price:last").val("");
        });
        $(".srv_total_price").each(function () {
            $("#example").find(".srv_total_price:last").val("");
        });
        var i = 1;
        $(".patientCheckoutUnitPrice").each(function () {
            $("#example").find(".patientCheckoutUnitPrice:last").val("");
            i++;
        });
    }

    function getTotalAmountPatientCheckOut() {
        var totalAmount = 0;
        var totalAmountPaid = 0;
        var totalDiscountAll = parseFloat($("#PatientDiscountTotal").val());
        var totalAmountPaid = parseFloat($("#SerNumTotalAmountPaid").val());
        totalDiscountAll = totalDiscountAll != "" ? totalDiscountAll : 0;
        totalAmountPaid = totalAmountPaid != "" ? totalAmountPaid : 0;
        $(".srv_total_price").each(function () {
            if ($.trim($(this).val()) != '' || $(this).val() != undefined) {
                totalAmount += Number($(this).val());
            }
        });
        if (isNaN(totalAmount)) {
            $("#SerNumTotalAmount").val(0.00);
            $("#PatientDiscountTotal").val(0.00);
            $("#PatientSubTotalAmount").val(0.00);
        } else {
            $("#SerNumTotalAmount").val((totalAmount).toFixed(2));
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

                            var discountAmount = $("#inputInvoiceDisAmt").val();
                            var discountPercent = $("#inputInvoiceDisPer").val();
                            var calTotalPrice = tr.find("input[name='data[Patient][total_price][]']").val();

                            var discount = 0;
                            if (discountPercent > 0) {
                                discount = (parseFloat(discountPercent) * calTotalPrice) / 100;
                                tr.closest("tr").find("input[name='discount_percent[]']").val(discount);
                            }
                            if (discountAmount > 0) {
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

                            var discountAmount = $("#inputInvoiceDisAmt").val();
                            var discountPercent = $("#inputInvoiceDisPer").val();
                            var calTotalPrice = Number($("#SerNumTotalAmount").val());
                            var discount = 0;
                            if (discountPercent > 0) {
                                $("#LabelDisPercent").html('(' + discountPercent + '%)');
                                discount = (parseFloat(discountPercent) * calTotalPrice) / 100;
                            }
                            if (discountAmount > 0) {
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
<div id="tmpService">
    <?php
    $ind = 0;
    $totalPrice = 0;
    foreach ($tmpService as $tmpService):
        $subTotal = 0;
        ?>
        <h3>
            <a href="#">
                <?php echo date('d/m/Y H:i:s', strtotime($tmpService['TmpService']['created'])); ?>
                <div style="float:right;display: none">
                    <img alt="" patientConsultationId="<?php echo $tmpService['TmpService']['id'] ?>" queuedDoctorId="<?php echo $tmpService['TmpService']['queued_doctor_id']; ?>" queueId="<?php echo $tmpService['Queue']['id']; ?>" src="<?php echo $this->webroot; ?>img/button/printer.png" class="btnPrint"  name="" onmouseover="Tip('<?php echo ACTION_PRINT; ?>')" />
                </div>
            </a>
        </h3>
        <div class="<?php echo $tmpService['TmpService']['id']; ?>">
            <?php echo $this->Form->create('TmpService', array('id' => 'ServiceDoctor' . $tmpService['TmpService']['id'], 'class' => 'TmpServiceEditForm', 'rel' => $tmpService['TmpService']['id'], 'url' => '/doctors/editTmpService/' . $tmpService['TmpService']['id'] . '/' . $tmpService['TmpService']['queued_doctor_id'] . '/' . $tmpService['Queue']['id'], 'enctype' => 'multipart/form-data')); ?>
            <input name="data[QeuedDoctor][id]" type="hidden" value="<?php echo $tmpService['QeuedDoctor']['id']; ?>"/>
            <input name="data[Queue][id]" type="hidden" value="<?php echo $tmpService['Queue']['id']; ?>"/>
            <?php echo $this->Form->hidden('tmp_service_id', array('class' => 'tmpServiceId', 'label' => false, 'value' => $tmpService['TmpService']['id'])); ?>
            <input type="hidden" name="data[Patient][exchange_rate_id]" value="<?php /* echo getExchangeRateId(); */ echo 1; ?>" />
            <input type="hidden" name="data[Patient][exchange_rate]" value="<?php echo $exchangeRate; ?>" />

            <fieldset style="display: none;" id="patient_info<?php echo $tblNameRadom; ?>">
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
                                <select id="PatientCompanyId" class="PatientCompanyId classCompany validate[required]" name="data[Patient][company_id]" style="width:250px; height: 35px;">                    
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
                                <select name="data[Patient][branch_id]" id="OrderBranchId" class="OrderBranchId validate[required]" style="width:250px;">
                                    <?php
                                    if (count($branches) != 1) {
                                        ?>
                                        <option value="" com="" mcode="" currency="" symbol=""><?php echo INPUT_SELECT; ?></option>
                                        <?php
                                    }
                                    foreach ($branches AS $branch) {
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
            <div <?php
            if ($tmpService['TmpService']['status'] == 1) {
                echo'id="addPatientCheckOut"';
            }
            ?> >    
                <table <?php
                if ($tmpService['TmpService']['status'] == 1) {
                    echo 'id="example"';
                }
                ?>  class=" table" cellspacing="0">
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
                    <tbody <?php
                    if ($tmpService['TmpService']['status'] == 1) {
                        echo 'id="tableToModify"';
                    }
                    ?>>

                        <?php
                        if (!empty($tmpService)) {
                            $index = 1;

                            $queryTmpServiceDetails = mysql_query("SELECT * FROM tmp_service_details sd WHERE sd.tmp_service_id = " . $tmpService['TmpService']['id']);
                            while ($resultTmpService = mysql_fetch_array($queryTmpServiceDetails)) {
                                $sectionID = '';
                                $queryGetSection = mysql_query("SELECT sc.* ,s.name AS `service_name` FROM services s INNER JOIN sections sc ON sc.id = s.section_id WHERE s.id = " . $resultTmpService['service_id']);
                                if ($getSectionID = mysql_fetch_array($queryGetSection)) {
                                    $sectionID = $getSectionID['id'];
                                    $sectionName = $getSectionID['name'];
                                    $serviceName = $getSectionID['service_name'];
                                }
                                ?>
                                <tr class="PatientCheckoutTr">
                                    <td class="first serviceId">
                                        <?php echo $index ?>
                                    </td>  
                                    <td>
                                        <div class="inputContainer">
                                            <?php
                                            if ($tmpService['TmpService']['status'] == 1) {
                                                echo $this->Form->input('sections', array('rel' => $index, 'id' => 'ServiceSectionId' . $index, 'class' => 'classSection', 'name' => 'data[Patient][section_id][]', 'empty' => SELECT_OPTION, 'label' => false, 'style' => 'width: 200px;', 'selected' => $sectionID));
                                            } else {
                                                echo $sectionName;
                                            }
                                            ?>                               
                                        </div>    
                                    </td>
                                    <td>
                                        <div class="inputContainer">
                                            <?php
                                            if ($tmpService['TmpService']['status'] == 1) {
                                                echo $this->Form->input('service', array('rel' => $index, 'id' => 'CheckoutServiceId' . $index, 'class' => 'patientCheckoutService', 'name' => 'data[Patient][service_id][]', 'empty' => SELECT_OPTION, 'label' => false, 'style' => 'width: 200px;', 'selected' => $resultTmpService['service_id']));
                                            } else {
                                                echo $serviceName;
                                            }
                                            ?>                               
                                        </div>  

                                    </td>
                                    <td>
                                        <?php if ($tmpService['TmpService']['status'] == 1) { ?>
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
                                            <?php
                                        } else {
                                            foreach ($doctors as $doctor) {
                                                $selected = "";
                                                if ($resultTmpService['doctor_id'] == $doctor['User']['id']) {
                                                    echo $doctor['Employee']['name'];
                                                }
                                            }
                                        }
                                        ?> 
                                    </td>
                                    <td>
                                        <?php
                                        if ($tmpService['TmpService']['status'] == 1) {
                                            echo $this->Form->text('qty', array('value' => $resultTmpService['qty'], 'id' => 'CheckoutQty' . $index, 'name' => 'data[Patient][qty][]', 'class' => 'qty integer ', 'style' => 'width:50px;text-align:center;', 'rel' => $index, 'autocomplete' => 'off'));
                                        } else {
                                            echo $resultTmpService['qty'];
                                        }
                                        ?> 
                                    </td>
                                    <td>
                                        <?php
                                        if ($tmpService['TmpService']['status'] == 1) {
                                            echo $this->Form->text('unit_price', array('id' => 'CheckoutUnitPrice' . $index, 'name' => 'data[Patient][unit_price][]', 'class' => 'unit_price float validate[required]', 'rel' => $index, 'autocomplete' => 'off', 'style' => 'width:100px;', 'value' => number_format($resultTmpService['unit_price'], 2)));
                                        } else {
                                            echo number_format($resultTmpService['unit_price'], 2);
                                        }
                                        ?> 
                                    </td>
                                    <td>
                                        <?php if ($tmpService['TmpService']['status'] == 1) { ?> 
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
                                            if ($resultTmpService['discount'] > 0) {
                                                echo $resultTmpService['discount'];
                                            };
                                        }
                                        ?> 
                                    </td>
                                    <td>
                                        <?php
                                        if ($tmpService['TmpService']['status'] == 1) {
                                            echo $this->Form->text('total_price', array('id' => 'CheckoutTotalPrice' . $index, 'name' => 'data[Patient][total_price][]', 'class' => 'srv_total_price', 'style' => 'width:154px;', 'readonly' => true, 'value' => number_format($resultTmpService['total_price'], 2)));
                                        } else {
                                            $subTotal+= $resultTmpService['total_price'];
                                            echo number_format($resultTmpService['total_price'], 2);
                                        }
                                        ?>
                                    </td> 
                                    <td style="padding: 5px 5px 5px 5px !important;">
                                        <?php if ($tmpService['TmpService']['status'] == 1) { ?>
                                            <img alt="" src="<?php echo $this->webroot; ?>img/button/cross.png" class="btnRemoveType" style="cursor: pointer; display:inline; " />
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php
                                $index++;
                            }
                        }
                        ?>
                        <?php if ($tmpService['TmpService']['status'] == 1) { ?>
                            <tr class="PatientCheckoutTr" >
                                <td class="first serviceId">
                                    <?php
                                    echo $index;
                                    ?>
                                </td>            
                                <td>
                                    <div class="inputContainer">
                                        <?php
                                        $query = mysql_query("SELECT sections.id, sections.name, section_companies.company_id  FROM `sections` INNER JOIN section_companies ON sections.id = section_companies.section_id 
                                                        WHERE sections.id IN (SELECT section_id FROM section_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = '" . $user['User']['id'] . "'))")
                                        ?>
                                        <select id="ServiceSectionId<?php echo $index; ?>" rel="<?php echo $index; ?>" class="classSection" name="data[Patient][section_id][]" style="width:200px;">
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
                                    <?php echo $this->Form->input('service_id', array('id' => 'CheckoutServiceId' . $index, 'name' => 'data[Patient][service_id][]', 'empty' => SELECT_OPTION, 'label' => false, 'class' => 'patientCheckoutService ', 'style' => 'width:200px;', 'rel' => $index)); ?>
                                </td>
                                <td>
                                    <select id="PatientIpdDoctorId<?php echo $index; ?>" style="width:130px;" name="data[Patient][doctor_id][]" class="classDoctor ">
                                        <option value=""><?php echo SELECT_OPTION; ?></option>
                                        <?php
                                        foreach ($doctors as $doctor) {
                                            echo '<option class="' . $doctor['Company']['id'] . '" value="' . $doctor['User']['id'] . '">' . $doctor['Employee']['name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <?php echo $this->Form->text('qty', array('id' => 'CheckoutQty' . $index, 'name' => 'data[Patient][qty][]', 'class' => 'qty integer ', 'style' => 'width:50px;text-align:center;', 'rel' => $index, 'autocomplete' => 'off')); ?> 
                                </td>
                                <td>
                                    <?php echo $this->Form->text('unit_price', array('id' => 'CheckoutUnitPrice' . $index, 'name' => 'data[Patient][unit_price][]', 'class' => 'unit_price float ', 'rel' => $index, 'autocomplete' => 'off', 'style' => 'width:100px;')); ?> 
                                </td>
                                <td>
                                    <?php
                                    //                if ($allowProductDiscount) {
                                    ?>
                                    <input type="hidden" name="discount_amount[]" value="0" />
                                    <input type="hidden" name="discount_percent[]" value="0" />
                                    <input type="text" id="CheckoutDiscount<?php echo $index; ?>" class="float discount btnDiscountCheckOut" name="data[Patient][discount][]" style="width: 50px;" readonly="readonly" rel="<?php echo $index; ?>"/>
                                    <img alt="Remove" src="<?php echo $this->webroot . 'img/button/cross.png'; ?>" class="btnRemoveDiscountCheckOut" align="absmiddle" style="cursor: pointer; display: none"  onmouseover="Tip('Remove')" />
                                    <?php
                                    //                } else {
                                    ?>
                                    <!--<input type="hidden" id="CheckoutDiscount<?php echo $index; ?>" class="float discount btnDiscountCheckOut" name="data[Patient][discount][]" style="width: 50px;" readonly="readonly" rel=""/>-->
                                    <?php
                                    //                }
                                    ?>                    
                                </td>
                                <td>
                                    <?php echo $this->Form->text('total_price', array('id' => 'CheckoutTotalPrice' . $index, 'name' => 'data[Patient][total_price][]', 'class' => 'srv_total_price', 'style' => 'width:154px;', 'readonly' => true)); ?>
                                </td>
                                <?php if ($tmpService['TmpService']['status'] == 1) { ?>
                                    <td style="padding: 5px 5px 5px 5px !important;" >
                                        <img alt="" src="<?php echo $this->webroot; ?>img/button/plus.png" class="btnAddType" style="cursor: pointer;" />
                                        <img alt="" src="<?php echo $this->webroot; ?>img/button/cross.png" class="btnRemoveType" style="cursor: pointer;display: none;" />
                                    </td>
                                <?php } ?>
                            </tr>  
                        <?php } ?>
                    </tbody>
                    <tr>
                        <td class="first" style="text-align: right;" colspan="7"><label for="SerNumTotalAmount">Sub Total ($)</label></td>
                        <td>
                            <?php if ($tmpService['TmpService']['status'] == 1) { ?>
                                <input type="text" id="SerNumTotalAmount" value="<?php echo number_format($totalPrice, 2); ?>" class="validate[required]" readonly="readonly" style="width:154px; height: 30px;font-weight: bold;" name="data[Patient][total_amount]">
                                <?php
                            } else {
                                echo number_format($subTotal, 2);
                            }
                            ?>
                        </td>
                    </tr>


                </table>    
            </div>
            <br />
            <?php if ($tmpService['TmpService']['status'] == 1) { ?>
                <div class="buttons">
                    <button type="submit" class="positive">
                        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
                        <?php echo ACTION_SAVE; ?>
                    </button>
                    <img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" class="loading" style="display: none;" />
                </div>
            <?php } ?>
            <div style="clear: both;"></div>

            <?php echo $this->Form->end(); ?>   
        </div>
        <?php
        $ind++;
    endforeach;
    ?>
</div>
<div id="dialog" title=""></div>
<div id="dialogPrint<?php echo $tblName; ?>" title="" style="display: none;">
    <br />
    <center>
        <div class="buttons" style="display: inline-block;">
            <button type="button" id="btnPatientConsultation<?php echo $tblName; ?>" class="positive">
                <img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/>
                <?php echo ACTION_PRINT; ?>
            </button>
        </div>
    </center>
</div>
<div id="patientConsultation" style="display: none;"></div>