<?php
include('includes/function.php');
echo $this->element('prevent_multiple_submit');
$this->element('check_access');
$allowProductDiscount = checkAccess($user['User']['id'], $this->params['controller'], 'discount');
$allowVoidService = checkAccess($user['User']['id'], 'dashboards', 'voidServiceInvoice');
$allowVoidReceipt = checkAccess($user['User']['id'], 'dashboards', 'voidReceipt');
$absolute_url = FULL_BASE_URL . Router::url("/", false);
$totalBalance = 0;
$query_invoice = mysql_query("SELECT * FROM invoices WHERE id=" . $this->params['pass'][0]);
$data_invoice = mysql_fetch_array($query_invoice);
//$exchangeRate = getExchangeRate();
$exchangeRate = 4150;
?>
<?php $tblName = "tbl" . rand(); ?>
<style type="text/css">
    form input[type=text] {
        width: 120px;
    }
    form input[type=password] {
        width: 120px;
    }
    form select {
        width: 120px;
    }
    #voidSH{
        display: none;
    }
    .clHideTd{
        display: none;
    }
</style>
<script type="text/javascript" src="<?php echo $this->webroot . 'js/jquery.formatCurrency-1.4.0.min.js'; ?>"></script>
<script type="text/javascript">
    function calc() {
        var amount_d = Number($("#label_amount_d").attr("title"));
        var exchange_rate = Number($("#exchange_rate").text());
        $("#label_amount_r").text((amount_d * exchange_rate).toFixed(2)).formatCurrency({colorize: true, symbol: '៛'});
        $("#label_amount_d").text(amount_d.toFixed(10)).formatCurrency({colorize: true});
        if ($("#total_amount_r").val() <= 0) {
            if ($("#total_amount_d").val() != "") {
                var paid = parseFloat($("#total_amount_d").val());
            } else {
                var paid = 0;
            }
        } else {
            if ($("#total_amount_d").val() != "") {
                var paid = ($("#total_amount_r").val() / exchange_rate) + parseFloat($("#total_amount_d").val());
            } else {
                var paid = ($("#total_amount_r").val() / exchange_rate);
            }
        }
        var totalDiscountAll = parseFloat($("#PatientDiscountTotal").val());
        var balance = amount_d - (paid + totalDiscountAll);

        $("#label_balance_r").text((balance * exchange_rate).toFixed(2)).formatCurrency({colorize: true, symbol: '៛'});
        $("#label_balance_d").text(balance.toFixed(2)).formatCurrency({colorize: true});

        // set price to hidden field
        $("#amount").val(amount_d);
        $("#balance").val(balance);
    }
    
    function checkAmount(field, rules, i, options) {
        if ($("#total_amount_r").val() == 0 && $("#total_amount_d").val() == 0) {
            return options.allrules.required.alertText;
        }
    }
    $(document).ready(function() {
        calc();
        showVoid();
        var acc = $("select.patient_coa_id option:selected").val();
        $("#chartAcc").val(acc);
        $("#CheckoutForm").validationEngine();
        $("#total_amount_r").keyup(function() {
            calc();
        });
        $("#total_amount_r").live('click', function() {
            $("#total_amount_r").val("");
        });

        $("#total_amount_d").keyup(function() {
            calc();
        });
        $("#total_amount_d").live('click', function() {
            $("#total_amount_d").val("");
        });


        $("#total_amount_r").blur(function() {
            if ($(this).val() == '') {
                $(this).val("0.00");
            }
            calc();
        });
        $("#total_amount_d").blur(function() {
            if ($(this).val() == '') {
                $(this).val("0.00");
            }
            calc();
        });


        // Prevent Key Enter
        preventKeyEnter();
        $("#CheckoutForm").validationEngine();
        $("#CheckoutForm").ajaxForm({
            dataType: 'json',
            beforeSubmit: function(arr, $form, options) {
                var dis = $("#PatientDiscountTotal").val();
                var totalP = $("#total_amount_d").val();
                if((dis!='' && dis!=0) || (totalP !='' && totalP !=0)){
                    return true;
                }else{
                   alert('<?php echo MESSAGE_ENTRY_DATA_PAID_OR_DISCOUNT; ?>');
                   return false;
                }  
                $(".txtSaveCheckoutDebt").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $("#dialog").html('<div class="buttons"><button type="submit" class="positive printCheckOutFormInvoice" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="printCheckOutForm"><?php echo ACTION_PRINT_INVOICE; ?></span></button> <button style="display: none;" type="submit" class="positive printCheckOutFormInvoiceVat"><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="printCheckOutForm"><?php echo ACTION_PRINT_INVOICE_VAT; ?></span></button> <button type="submit" class="positive printCheckOutFormReceipt" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="printCheckOutForm"><?php echo ACTION_PRINT_RECEIPT; ?></span></button></div>');
                $(".printCheckOutFormInvoice").click(function() {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printInvoiceIpd/" + result,
                        beforeSend: function() {
                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner.gif');
                        },
                        success: function(printCheckOutFormResult) {
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
                            }
                            catch (err)
                            {
                                //Handle errors here                                    
                                w.print();
                            }
                            w.close();
                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                        }
                    });
                });
                $(".printCheckOutFormInvoiceVat").click(function(){
                    $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printInvoiceVat/"+result,
                        beforeSend: function(){
                            $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                        },
                        success: function(printCheckOutFormResult){
                            w=window.open();
                            w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                            w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                            w.document.write(printCheckOutFormResult);
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
                $(".printCheckOutFormReceipt").click(function() {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printInvoiceReceiptIpd/" + result,
                        beforeSend: function() {
                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner.gif');
                        },
                        success: function(printCheckOutFormResult) {
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
                            }
                            catch (err)
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
                    position: 'center',
                    closeOnEscape: true,
                    open: function(event, ui) {
                        $(".ui-dialog-buttonpane").show();
                        $(".ui-dialog-titlebar-close").show();
                    },
                    close: function() {
                        $(this).dialog({close: function() {
                            }});
                        $(this).dialog("close");
                        $(".btnBackCheckoutDebt").dblclick();
                    },
                    buttons: {
                        '<?php echo ACTION_CLOSE; ?>': function() {
                            $("meta[http-equiv='refresh']").attr('content', '0');
                            $(this).dialog("close");
                        }
                    }
                });
                $(".btnBackCheckoutDebt").dblclick();
            }
        });
        $(".saveCheckOutDebt").click(function() {
            if (checkBfSaveCheckoutDebt() == true) {
                $(".saveCheckOutDebt").attr('disabled', 'disabled');
                $(".txtSaveCheckoutDebt").html("<?php echo ACTION_LOADING; ?>");
                return true;
            } else {
                return false;
            }
        });
        
        $(".btnDiscountTotal").click(function(){            
            addNewDiscountTotal($(this).closest("tr"));
        });
        $(".btnRemoveDiscountTotal").click(function(){
            removeDiscountTotal($(this).closest("tr"));
        });
        
        $(".btnVoidRec").click(function(event){
            event.preventDefault();
            var id = $(this).attr('rel');
            var invoiceId = $(this).attr('invoiceID');
            var amountBalance = $(this).attr('amountBalance');
            var name = $(this).attr('name');
            var sts = $(this).attr('sts');
            // condition for check credit memo before void
            if(sts=='0'){
                alert('<?php echo MESSAGE_CREDIT_MEMO_BEFORE_VOID; ?>');
                return false;
            }
            $("#dialog").dialog('option', 'title', '<?php echo DIALOG_CONFIRMATION; ?>');
            $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_CONFIRM_VOID; ?> <b>' + name + '</b>?</p>');
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
                    '<?php echo ACTION_VOID; ?>': function() {
                        $.ajax({
                            type: "GET",
                            url: "<?php echo $this->base.'/dashboards'; ?>/voidReceipt/" + id + "/" + invoiceId + "/" + amountBalance,
                            data: "",
                            beforeSend: function(){
                                $("#dialog").dialog("close");
                                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                            },
                            success: function(result){
                                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                $("#divTblService").load($("#tblSer").html());
                                $("#divTbleReceipt").load($("#tbleRec").html());
                                oCache.iCacheLower = -1;
                                oTableDebtList.fnDraw(false);
                                // alert message
                                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result+'</p>');
                                $("#dialog").dialog({
                                    title: '<?php echo DIALOG_INFORMATION; ?>',
                                    resizable: false,
                                    modal: true,
                                    width: 'auto',
                                    height: 'auto',
                                    buttons: {
                                        '<?php echo ACTION_CLOSE; ?>': function() {
                                            $(this).dialog("close");
                                        }
                                    }
                                });
                                 $('#divTblService').load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/checkoutDebt/"+<?php echo $this->params['pass'][0]; ?>);
                            }
                        });
                    },
                    '<?php echo ACTION_CANCEL; ?>': function() {
                        $(this).dialog("close");
                    }
                }
            });
        });
        
        $(".btnVoidSerInv").click(function(event){
            event.preventDefault();
            var id = $(this).attr('rel');
            var name = $(this).attr('name');
            var invoiceId = $(this).attr('invoiceId');
            var balance = $(this).attr('amountBalance');
            var total = $(this).attr('amountTotal');
            $("#dialog").dialog('option', 'title', '<?php echo DIALOG_CONFIRMATION; ?>');
            $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_CONFIRM_VOID; ?> <b>' + name + '</b>?</p>');
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
                    '<?php echo ACTION_VOID; ?>': function() {
                        $.ajax({
                            type: "GET",
                            url: "<?php echo $this->base.'/dashboards'; ?>/voidServiceInvoice/" + id + "/" + balance + "/" + total + "/" + invoiceId ,
                            data: "",
                            beforeSend: function(){
                                $("#dialog").dialog("close");
                                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                            },
                            success: function(result){
                                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                oCache.iCacheLower = -1;
                                oTableDebtList.fnDraw(false);
                                // alert message
                                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result+'</p>');
                                $("#dialog").dialog({
                                    title: '<?php echo DIALOG_INFORMATION; ?>',
                                    resizable: false,
                                    modal: true,
                                    width: 'auto',
                                    height: 'auto',
                                    buttons: {
                                        '<?php echo ACTION_CLOSE; ?>': function() {
                                            $(this).dialog("close");
                                        }
                                    }
                                });
                                $('#divTblService').load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/checkoutDebt/"+<?php echo $this->params['pass'][0]; ?>);
                            }
                        });
                    },
                    '<?php echo ACTION_CANCEL; ?>': function() {
                        $(this).dialog("close");
                    }
                }
            });
        });
        
        $(".btnBackCheckoutDebt").dblclick(function(event) {
            event.preventDefault();
            $('#CheckoutForm').validationEngine('hideAll');
            oCache.iCacheLower = -1;
            oTableDebtList.fnDraw(false);
            var rightPanel = $(this).parent().parent().parent().parent();
            var leftPanel = rightPanel.parent().parent().find(".leftPanel");
            rightPanel.hide();
            rightPanel.html("");
            leftPanel.show("slide", {direction: "left"}, 500);
        });
        $(".float").autoNumeric();
        
        var stsInv = $("#stsInvDShow").val();
        if(stsInv==0){
            $(".btnVoidSerInv").hide();
            $("#tblSer").find('th:last').addClass('clHideTd');
            $(".trSH").find('td:last').addClass('clHideTd');
        }else{
            $(".btnVoidSerInv").removeAttr('id','voidSH');
            $("#tblSer").find('th:last').removeClass('clHideTd');
            $("#tblSer").find('.trSH td:last').removeClass('clHideTd');
        }
        
           
        //Hide Patinen Info
        $("#btnHidePatientInfo<?php echo $tblName; ?>").click(function(){
            $("#patientInfo<?php echo $tblName; ?>").hide(900);
            $("#showPatientInfo<?php echo $tblName; ?>").show();
        });
        //Show Patinen Info
        $("#btnShowPatientInfo<?php echo $tblName; ?>").click(function(){
            $("#patientInfo<?php echo $tblName; ?>").show(900);
            $("#showPatientInfo<?php echo $tblName; ?>").hide();
        });
        
        
        
    });
    
    function checkBfSaveCheckoutDebt(){
        var formName = "#CheckoutForm";
        var validateBack =$(formName).validationEngine("validate");
        if(!validateBack){            
            return false;
        }else{            
            return true;
        }
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
                            var discountAmount     = $("#inputInvoiceDisAmt").val();
                            var discountPercent    = $("#inputInvoiceDisPer").val();
                            var calTotalPrice       = Number($("#label_amount_d").attr("title"));
                            var discount = 0;
                            
                            if (discountPercent>0) {
                                $("#LabelDebDisPercent").html('('+discountPercent+'%)');
                                discount = (parseFloat(discountPercent) * calTotalPrice) / 100;
                            }
                            if (discountAmount>0) {
                                $("#LabelDebDisPercent").html('');
                                discount = parseFloat(discountAmount);
                            }
                            
                            if (discount >= 0) {
                                tr.find("input[name='data[Patient][total_discount]']").val(discount.toFixed(2));
                                tr.find("input[name='data[Patient][total_discount_per]']").val(parseFloat(discountPercent));
                            } else {
                                tr.find("input[name='data[Patient][total_discount]']").val(discount.toFixed(2));
                                tr.find("input[name='data[Patient][total_discount_per]']").val(parseFloat(discountPercent));
                            }
                            
                            calc();
                            
                            /*
                            var discountTr = $("input[name='chkDiscount']:checked").closest("tr");
                            if(discountTr != "" && discountTr != undefined){                                
                                tr.find("input[name='data[Patient][total_discount]']").css("display", "inline");
                                tr.find(".btnRemoveDiscountTotal").css("display", "inline");
                                var discountAmount      = discountTr.find("input[name='patientDiscountAmount']").val();
                                var discountPercent     = discountTr.find("input[name='patientDiscountPercent']").val();
                                var calTotalPrice       = Number($("#label_amount_d").attr("title"));
                                //Calculate Discount
                                var discount = 0;
                                if(discountAmount != ''){
                                    discount = parseFloat(discountAmount);
                                }else if(discountPercent != ''){
                                    discount = (parseFloat(discountPercent) * calTotalPrice) / 100;
                                }
                                if(discount>=0){
                                    tr.find("input[name='data[Patient][total_discount]']").val(discount.toFixed(2));
                                    tr.find("input[name='data[Patient][total_discount_per]']").val(parseFloat(discountPercent));
                                }else{
                                    tr.find("input[name='data[Patient][total_discount]']").val(discount.toFixed(2));
                                    tr.find("input[name='data[Patient][total_discount_per]']").val(parseFloat(discountPercent));
                                }
                                calc();
                            } */
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
        calc();
    }
    
    function showVoid(){
        $("#tbleRec").find("tr:last").find("td .btnVoidRec").show();
    }
 
</script>
<div id="divTblService">
<div style="padding: 5px;border: 1px dashed #3C69AD;">
    <div class="buttons">
        <a href="#" class="positive btnBackCheckoutDebt">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<legend id="showPatientInfo<?php echo $tblName; ?>" style="display:none;"><a href="#" id="btnShowPatientInfo<?php echo $tblName; ?>" style="background: #CCCCCC; font-weight: bold;"><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?> [ Show ] </a> </legend>
<fieldset id="patientInfo<?php echo $tblName; ?>">
    <legend><a href="#" id="btnHidePatientInfo<?php echo $tblName; ?>" style="background: #CCCCCC; font-weight: bold;"> <?php __(MENU_PATIENT_MANAGEMENT_INFO); ?> [ Hide ] </a> </legend>
    <table style="width: 100%;" cellspacing="3">
        <tr>
            <th style="width: 10%;"><?php __(PATIENT_CODE); ?></th>
            <td style="width: 40%;">: <?php echo $patient['Patient']['patient_code']; ?></td>
            <th style="width: 10%;"><?php __(TABLE_DOB); ?></th>
            <td style="width: 40%;">: 
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
            </td>
        </tr>
        <tr>
            <th><?php echo TABLE_COMPANY; ?></th>
            <td>
                : <?php echo $patient['Company']['name']; ?>
            </td>
            <th><?php __(TABLE_BILL_PAID_BY); ?></th>
            <td>
                : <?php echo $patient['PatientBillType']['name']; ?>
            </td>       
        </tr>
        <?php if ($patient['PatientBillType']['id'] == 3) { ?>
            <tr>
                <th><?php echo TABLE_COMPANY_INSURANCE_NAME; ?></th>
                <td colspan="3">
                    : <?php echo $patient['CompanyInsurance']['name']; ?>
                </td> 
            </tr>
        <?php } ?>
    </table>
</fieldset>
<br />
<?php echo $this->Form->create('Checkout', array('id' => 'CheckoutForm', 'url' => '/cashiers/checkoutDebt/' . $this->params['pass'][0])); ?>
<input type="hidden" name="action" value="checkout" />
<input type="hidden" name="invoice_id" value="<?php echo $this->params['pass'][0]; ?>" />
<input type="hidden" name="exchange_rate_id" value="<?php /* echo getExchangeRateId(); */ echo 1; ?>" />
<input type="hidden" name="exchange_rate" value="<?php echo $exchangeRate; ?>" />
<input type="hidden" name="data[Patient][id]" value="<?php echo $patient['Patient']['id']; ?>" >
<input type="hidden" id="amount" name="amount" value="" />
<input type="hidden" id="balance" name="balance" value="" />
<?php $index = 1; ?>
    <table class="table" id="tblSer" cellspacing="0">
        <tr>
            <th class="first"><?php echo TABLE_NO; ?></th>
            <th style="width: 20%;"><?php echo SECTION_SECTION; ?></th>
            <th style="width: 25%;"><?php echo SERVICE_SERVICE; ?></th>
            <th><?php echo GENEARL_DATE; ?></th>
            <th><?php echo DOCTOR_NAME; ?></th>
            <th><?php echo GENERAL_QTY; ?></th>
            <th><?php echo GENERAL_UNIT_PRICE; ?></th>
            <th><?php echo GENERAL_DISCOUNT; ?></th>
            <th><?php echo GENERAL_TOTAL_PRICE; ?></th>
            <th></th>
        </tr>
        <?php
        $total = 0;
        $query = mysql_query("SELECT * FROM invoice_details WHERE is_active=1 AND invoice_id=" . $this->params['pass'][0]);
        while ($data = mysql_fetch_array($query)) {
            ?>
            <tr class="trSH">
                <?php
                $total+=$data['total_price'];
                ?>
                <td class="first"><?php echo $index++; ?></td>
                <td>
                    <?php
                    if ($data['type'] == 1) {
                        $query_service = mysql_query("SELECT sec.name, ser.name FROM sections sec INNER JOIN services ser ON ser.section_id=sec.id WHERE ser.id=" . $data['service_id']);
                        $data_service = mysql_fetch_array($query_service);
                        echo $data_service[0];
                    } else if ($data['type'] == 2) {
                        echo 'Labo';
                    }
                    ?>
                </td>
                <td>
                    <?php
                    if ($data['type'] == 1) {
                        echo $data_service[1];
                    } else if ($data['type'] == 2) {
                        $queryLaboName = mysql_query("SELECT name FROM labo_item_groups WHERE id = '" . $data['service_id'] . "' ");
                        $dataLaboName = mysql_fetch_array($queryLaboName);
                        echo $dataLaboName['name'];
                    }
                    ?>
                </td>
                <td>
                    <?php                
                    if ($data['date_created'] != "" && $data['date_created'] != "0000-00-00") {
                        echo date("d/m/Y", strtotime($data['date_created']));
                    }else if($data['created'] != "" && $data['created'] != "0000-00-00 00:00:00") {
                        echo date("d/m/Y", strtotime($data['created']));
                    }
                    ?>
                </td>
                <?php
                $query_doctor = mysql_query("SELECT Employee.name FROM employees Employee INNER JOIN user_employees UserEmployee ON Employee.id=UserEmployee.employee_id WHERE UserEmployee.user_id=" . $data['doctor_id']);
                $data_doctor = mysql_fetch_array($query_doctor);
                ?>
                <td><?php echo $data_doctor[0]; ?></td>
                <td style="text-align: center;"><?php echo $data['qty']; ?></td>
                <td style="text-align: right;"><?php echo number_format($data['unit_price'], 2); ?></td>
                <td style="text-align: right;"><?php echo number_format($data['discount'], 2); ?></td>
                <td style="text-align: right;"><?php echo number_format($data['total_price'], 2); ?></td>
                <td>
                    <?php
                    $stsInvD =1;
                    $totalB = $patient['Invoice']['balance']-$data['total_price'];
                    $totalAmount = $patient['Invoice']['total_amount']-$data['total_price'];
                    $queryRec = mysql_query("SELECT id FROM receipts WHERE is_void=0 AND invoice_id=". $this->params['pass'][0]);
                    if(@mysql_num_rows($queryRec)){
                        $stsInvD = 0;//not show button void service
                    }else{
                        $stsInvD = 1;//show button void service
                    }
                    echo '<input type="hidden" value="'.$stsInvD.'" id="stsInvDShow">';
                    if ($allowVoidService) { 
                        echo '<a href="" class="btnVoidSerInv" id="voidSH" invoiceId="' . $patient['Invoice']['id'] . '" amountTotal="' . $totalAmount . '" amountBalance="' . $totalB . '" rel="' . $data['id'] . '" title="Service" ><img alt="Void" onmouseover="Tip(\''.ACTION_VOID.'\')" src="' . $this->webroot . 'img/action/delete.png" /></a>';
                    }
                    ?>
                </td>
            </tr>
        <?php } ?>
        <?php
        if($patient['Invoice']['queue_id']!=""){
            $query=mysql_query("SELECT sales_orders.*, orders.created_by FROM sales_orders LEFT JOIN orders ON orders.id = sales_orders.order_id WHERE sales_orders.status>=1 AND sales_orders.queue_id =".$patient['Invoice']['queue_id']);
            while($data=mysql_fetch_array($query)){?>
            <tr>
                <?php
                $total+=($data['total_amount']-$data['discount']);
                ?>
                <td class="first"><?php echo $index++; ?></td>        
                <td colspan="2">Medicine</td>
                <?php
                if($data['created_by']!=""){
                   $query_doctor=mysql_query("SELECT Employee.name FROM employees Employee INNER JOIN user_employees UserEmployee ON Employee.id=UserEmployee.employee_id WHERE UserEmployee.user_id=".$data['created_by']);
                   $data_doctor=mysql_fetch_array($query_doctor);
                }else{
                    $data_doctor[0] = "";
                }
                ?>
                <td>
                    <?php
                    if ($data['order_date'] != "" && $data['order_date'] != "0000-00-00") {
                        echo date("d/m/Y", strtotime($data['order_date']));
                    }
                    ?>
                </td>
                <td><?php echo $data_doctor[0];?></td>
                <td style="text-align: center;">1</td>
                <td style="text-align: right;"><?php echo number_format($data['total_amount'], 2); ?></td>
                <td style="text-align: right;"><?php echo number_format($data['discount'], 2); ?></td>
                <td style="text-align: right;"><?php echo number_format($data['total_amount']-$data['discount'], 2); ?></td>
            </tr>
            <?php 
            }
        } 
        ?>
        <tr>
            <td style="text-align: right;border-bottom: 0px;" colspan="8"><b>Total : </b></td>
            <td style="text-align: right;"><?php echo number_format($total, 2); ?></td>
        </tr>
        <tr>
            <td colspan="8" style="text-align: right;border-bottom: 0px;"><b>Total Discount :</b></td>
            <td style="text-align: right;"><?php echo number_format($patient['Invoice']['total_discount'], 2); ?></td>
        </tr>
    </table>
    <table class="table" id="tbleRec" cellspacing="0">
        <tr>
            <th class="first"><?php echo TABLE_NO; ?></th>
            <th><?php echo TABLE_DATE; ?></th>
            <th><?php echo TABLE_RECEIPT_CODE; ?></th>
            <th><?php echo GENERAL_AMOUNT_PAID; ?> ($)</th>
            <th><?php echo GENERAL_BALANCE; ?> ($)</th>
            <th></th>
        </tr>
        <?php
        $index = 1;
        $totalBal = 0 ;
        $balance = $patient['Invoice']['balance'];
        $totalBalance = $patient['Invoice']['balance'];
        $query = mysql_query("SELECT * FROM receipts WHERE is_void=0 AND invoice_id=" . $this->params['pass'][0]);
        if (!mysql_num_rows($query)) {
            echo '<tr><td colspan="6" class="first dataTables_empty">' . TABLE_NO_RECORD . '</td></tr>';
        }
        while ($data = mysql_fetch_array($query)) {
            $balance = $data['balance'];
            ?>
            <tr>
                <td class="first"><?php echo $index++; ?></td>
                <td><?php echo $data['created']; ?></td>
                <td><?php echo $data['receipt_code']; ?></td>
                <td style="text-align: right;"><?php echo number_format($data['total_amount_paid'], 2); ?></td>
                <td style="text-align: right;"><?php echo number_format($data['balance'], 2); ?></td>
                <td>
                    <?php 
                    $sts = 0;
                    $totalBal = $totalBalance + $data['total_amount_paid']+$data['total_dis'];
                    $querySale = mysql_query("SELECT sales_orders.id FROM sales_orders WHERE sales_orders.queue_id=".$patient['Invoice']['queue_id']);
                    if(@mysql_num_rows($querySale)){
                        $resStsSale = mysql_fetch_array($querySale);
                        $queryCredit = mysql_query("SELECT credit_memos.id FROM credit_memos INNER JOIN credit_memo_with_invoices cminv ON credit_memos.id=cminv.credit_memo_id WHERE cminv.status=1 AND sales_order_id=".$resStsSale[0]);
                        if(@mysql_num_rows($queryCredit)){
                            $sts = 1;
                        }else{
                            $sts = 0;
                        }
                    }else{
                        $sts = 1;
                    }
                    if ($allowVoidReceipt) { 
                        echo '<a href="" class="btnVoidRec" rel="' . $data['id'] . '" invoiceID="' . $patient['Invoice']['id'] . '" amountBalance = "' . $totalBal . '" title="' . $data['receipt_code'] . '" sts="' . $sts . '" style="cursor: pointer;display: none;"><img alt="Void" onmouseover="Tip(\''.ACTION_VOID.'\')" src="' . $this->webroot . 'img/action/delete.png" /></a>';
                    }
                    ?>
                </td>
            </tr>
        <?php } ?>    
    </table>
    <br/>
    <?php
    $ind = 1;
    $queryData = mysql_query("SELECT cm.cm_code,inv.invoice_code,cmwinv.apply_date,cmwinv.total_price FROM credit_memo_with_invoices cmwinv INNER JOIN invoices inv ON inv.id=cmwinv.invoice_id INNER JOIN credit_memos cm ON cm.id=cmwinv.credit_memo_id WHERE cmwinv.status=1 AND cmwinv.invoice_id=".$patient['Invoice']['id']);
    if(@mysql_num_rows($queryData)){?>
        <table class="table" cellspacing="0">
            <tr>
                <th class="first"><?php echo TABLE_NO; ?></th>
                <th><?php echo TABLE_CREDIT_MEMO_DATE; ?></th>
                <th><?php echo TABLE_CREDIT_MEMO_NUMBER; ?></th>
                <th><?php echo TABLE_INVOICE_CODE; ?></th>
                <th><?php echo GENERAL_AMOUNT_PAID; ?> ($)</th>
            </tr>
            <?php 
            while ($res = mysql_fetch_array($queryData)){ ?>
            <tr>
                <td class="first"><?php echo $ind++; ?></td>
                <td><?php echo dateShort($res['apply_date']); ?></td>
                <td><?php echo $res['cm_code']; ?></td>
                <td><?php echo $res['invoice_code']; ?></td>
                <td><?php echo $res['total_price']; ?></td>
            </tr>
            <?php
            }
            ?>
        </table>
    <?php 
    }
    ?>
    <br>
    <div id="divLast">    
        <div style="float: left;">
            <table class="table_solid" style="width: 555px;">
                <tr>
                    <th style="width: 37%;"><?php echo GENERAL_EXCHANGE_RATE; ?></th>
                    <td colspan="2" style="text-align: right;">1$ = <span id="exchange_rate"><?php echo $exchangeRate; ?></span>៛</td>
                </tr>
                <tr>
                    <th><?php echo GENERAL_AMOUNT; ?></th>
                    <td id="label_amount_r" style="text-align: right;"></td>
                    <td id="label_amount_d" style="text-align: right;" title="<?php echo $balance; ?>"><input type="hidden" value="<?php echo $balance; ?>" id="PatientTotalAmount" ></td>
                </tr>
                <tr>
                    <th><?php echo GENERAL_BALANCE; ?></th>
                    <td id="label_balance_r" style="text-align: right;font-size: 16px;font-weight: bold;"></td>
                    <td id="label_balance_d" style="text-align: right;font-size: 16px;font-weight: bold;"></td>
                </tr>            
            </table>
        </div>

        <div style="float: right;width: 335px;">        
            <table>            
                <tr>
                    <td><label for="PatientChartAccountId"><?php echo 'Deposit To'; ?> <span class="red">*</span> :</label></td>
                    <td>
                        <?php
                        $filter = "AND chart_account_type_id IN (1)";
                        $query = array();
                        ?>
                        <div class="inputContainer">
                            <select id="PatientChartAccountId" name="data[Patient][chart_account_id]" class="patient_coa_id validate[required]" style="width: 202px;" disabled="disabled">
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
                            <input type="hidden" id="chartAcc" name="data[Patient][chart_account_id]" >
                        </div>
                    </td>
                </tr>
                <tr style="display: none;">
                    <td><label for="PatientAmountUs"><?php echo GENERAL_PAID; ?> (៛)​:</label></td>
                    <td>
                        <input type="text" id="total_amount_r" name="total_amount_r" value="0.00" style="width: 190px;" class="float validate[custom[number],funcCall[checkAmount]]" />                    
                    </td>
                </tr>
                <tr>
                    <td class="first"><label for="PatientDiscountTotal">Total Discount ($): </label> <span id="LabelDebDisPercent"></span></td>
                    <td style="height: 30px;">
                        <?php                    
                        if ($allowProductDiscount) {
                            ?>
                            <input type="text" id="PatientDiscountTotal" value="0.00" class="float btnDiscountTotal" style="width: 190px;font-weight: bold;" name="data[Patient][total_discount]" readonly="readonly">   
                            <input type="hidden" id="PatientDiscountTotalP" value="0.00" name="data[Patient][total_discount_per]">   
                            <img alt="Remove" src="<?php echo $this->webroot . 'img/button/cross.png'; ?>" class="btnRemoveDiscountTotal" align="absmiddle" style="cursor: pointer; display: none"  onmouseover="Tip('Remove')" />
                            <?php
                        }else{
                        ?>  
                            <input type="hidden" id="PatientDiscountTotal" value="0.00" class="float" style="width: 190px;font-weight: bold;" name="data[Patient][total_discount]" readonly="readonly">                    
                        <?php
                        }
                        ?>                 
                    </td>
                </tr>
                <tr>
                    <td><label for="PatientAmountKh"><?php echo GENERAL_PAID; ?> ($):</label></td>
                    <td>
                        <input type="text" id="total_amount_d" name="total_amount_d" value="0.00" style="width: 190px;" class="float validate[custom[number]]" autocomplete="off" />
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="clear"></div>
    <div class="buttons">
        <button type="submit" class="positive saveCheckOutDebt" >
            <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
            <span class="txtSaveCheckoutDebt"><?php echo ACTION_SAVE; ?></span>
        </button>
    </div>
    <?php echo $this->Form->end(); ?>
</div>