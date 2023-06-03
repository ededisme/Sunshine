<?php 
include("includes/function.php");
$absolute_url = FULL_BASE_URL . Router::url("/", false); 
echo $this->element('prevent_multiple_submit');

$tblName = "tbl123"; 
$queryClosingDate=mysql_query("SELECT DATE_FORMAT(date,'%d/%m/%Y') FROM account_closing_dates ORDER BY id DESC LIMIT 1");
$dataClosingDate=mysql_fetch_array($queryClosingDate);

// Authentication
$this->element('check_access');
$allowEditTerm   = checkAccess($user['User']['id'], $this->params['controller'], 'editTermsCondition');
$allowEditInvDis = checkAccess($user['User']['id'], $this->params['controller'], 'invoiceDiscount');
?>
<script type="text/javascript">
    var timeSearchAddCM = 1;
    $(document).ready(function(){
        $(".chzn-select").chosen();
        // Prevent Key Enter
        preventKeyEnter();
        clearOrderDetailOrder();
        loadOrderDetailOrder();
        $("#DoctorAddForm").validationEngine();

        $(".saveOrder").click(function(){
            if(checkBfSaveOrder() == true){
                return true;
            }else{
                return false;
            }
        });
        
        $("#CMTopOrderDoctorIPD").hide();
        $("#showSaleOrderInfoIPD").show();
        
        $("#btnSalesOrderInfo").click(function(){
            $("#CMTopOrderDoctorIPD").hide();
            $("#showSaleOrderInfoIPD").show();
        });
        
        $("#btnSalesOrderInfoShow").click(function(){
            $("#CMTopOrderDoctorIPD").show();
            $("#showSaleOrderInfoIPD").hide();
        });
        
        $("#DoctorAddForm").ajaxForm({
            dataType: 'json',
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveOrder").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            beforeSerialize: function($form, options) {
                $("#DoctorOrderDate, .expired_date").datepicker("option", "dateFormat", "yy-mm-dd");
                $(".float, .interger").each(function(){
                    $(this).val($(this).val().replace(/,/g,""));
                });                
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                if(result.code == "1"){
                    codeDialogOrder();
                }else if(result.code == "2"){
                    errorSaveOrder();
                }else{                                        
                    $("#dialog").html('<div class="buttons"><button type="submit" class="positive printPatientPrescriptionForm" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span><?php echo ACTION_PRINT_PRESCRIPTION; ?></span></button><button type="submit" class="positive printPatientPrescriptionFormNoHead" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span><?php echo ACTION_PRINT_PRESCRIPTION_NO_HEADER; ?></span></button></div>');
                    $(".printPatientPrescriptionForm").click(function(){
                        $.ajax({
                            type: "POST",
                            url: "<?php echo $this->base . '/orders'; ?>/printInvoice/"+result.id,
                            beforeSend: function(){
                                $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                            },
                            success: function(printInvoiceResult){
                                w=window.open();
                                w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                                w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                                w.document.write(printInvoiceResult);
                                w.document.close();
                                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                            }
                        });
                    });
                    $(".printPatientPrescriptionFormNoHead").click(function(){
                        $.ajax({
                            type: "POST",
                            url: "<?php echo $this->base . '/orders'; ?>/printInvoice/"+result.id+"/1",
                            beforeSend: function(){
                                $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                            },
                            success: function(printInvoiceResult){
                                w=window.open();
                                w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                                w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                                w.document.write(printInvoiceResult);
                                w.document.close();
                                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                            }
                        });
                    });
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
                        close: function(){
                            $(this).dialog({close: function(){}});
                            $(this).dialog("close");               
                        },
                        buttons: {
                            '<?php echo ACTION_CLOSE; ?>': function() {
                                $(this).dialog("close");
                                $("#tabs2").tabs("select", 1);
                                $("#tabPrescription").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabPrescription/<?php echo $this->params['pass'][0].'/'.$this->params['pass'][1];?>");               
                            }
                        }
                    });
                }
            }
        });                                        
        
        $('#DoctorAppointmentDate').datetimepicker(
        {
            changeMonth: true,
            changeYear: true,
            timeFormat: 'hh:mm',
            showSecond: false,
            dateFormat:'yy-mm-dd'            
        });
        
        $('#DoctorOrderDate').datepicker({
            dateFormat:'dd/mm/yy',
            changeMonth: true,
            changeYear: true
        }).unbind("blur");
        
        $("#DoctorOrderDate").datepicker("option", "minDate", "<?php echo $dataClosingDate[0]; ?>");
        $("#DoctorOrderDate").datepicker("option", "maxDate", 0);
                
        $("#btnSmartCodeOrder").click(function(){
            $.ajax({
                type: "POST",
                url: "<?php echo $this->base; ?>/users/smartcode/orders/quotaion_code/7/" + $("#OrderQuotaionCode").val().toUpperCase(),
                beforeSend: function(){
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(result){
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $("#OrderQuotaionCode").val(result);
                }
            });
        });
        
        // Company Action
        if($.cookie('companyIdOrder')!=null){
            $("#OrderCompanyId").val($.cookie('companyIdOrder'));
        }
        
        $("#OrderCompanyId").change(function(){
            var obj = $(this);
            if($(".tblOrderListDoctor").find(".product_id").val() == undefined){
                $.cookie('companyIdOrder', obj.val(), { expires: 7, path: "/" });
            }else{
                var question = "<?php echo SALES_ORDER_CONFIRM_CHANGE_COMPANY; ?>";
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
                            $.cookie('companyIdOrder', obj.val(), { expires: 7, path: "/" });
                            loadOrderDetailOrder();
                            $(this).dialog("close");
                        },
                        '<?php echo ACTION_CANCEL; ?>': function() {
                            $("#OrderCompanyId").val($.cookie("companyIdOrder"));
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
        
    }); // End Document Ready
    
    // add
    function addNoteOrder(currentTr){
        var note = currentTr.closest("tr").find(".note");
        $("#dialog").html("<textarea style='width:350px; height: 200px;' id='noteComment'>"+note.val()+"</textarea>").dialog({
            title: '<?php echo TABLE_NOTE; ?>',
            resizable: false,
            modal: true,
            width: 'auto',
            height: 'auto',
            position:'center',
            closeOnEscape: true,
            open: function(event, ui){
                $(".ui-dialog-buttonpane").show(); $(".ui-dialog-titlebar-close").show();
            },
            buttons: {
                '<?php echo ACTION_OK; ?>': function() {
                    note.val($("#noteComment").val());
                    $(this).dialog("close");
                }
            }
        });
    }
    
    function checkCompanyOrder(companyId){
        var companyReturn = false;
        var companyPut    = companyId.split(",");
        var companySelect = $("#OrderCompanyId").val();
        if(companyPut.indexOf(companySelect) != -1){
            companyReturn = true;
        }load
        return companyReturn;
    }              
    
    function checkOrderDate(){
        if($("#DoctorOrderDate").val() == ''){
            $("#DoctorOrderDate").focus();
            return false;
        }else{
            return true;
        }
    }
    
    function loadOrderDetailOrder(){
        $.ajax({
            type: "POST",
            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/orderDetails/",
            beforeSend: function(){
                $(".orderDetailOrderDoctorIPD").html('<img alt="Loading" src="<?php echo $this->webroot; ?>img/ajax-loader.gif" />');
                $("#tblOrder").html("");
                $("#OrderTotalAmount").val('0.00');
                $("#OrderTotalAmountSummary").val('0.00');
                $("#OrderStatus").attr("disabled", true);
                $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
            },
            success: function(msg){
                $(".orderDetailOrderDoctorIPD").html(msg);
                $(".footerSaveOrderDoctor").show();

            }
        });
    }

    function clearOrderDetailOrder(){
        $(".orderDetailOrderDoctorIPD").html("");
        $(".footerSaveOrderDoctor").hide();
        $(".deleteCustomerOrder").hide();
    }

    function checkBfSaveOrder(){
        var formName = "#DoctorAddForm";
        var validateBack = $(formName).validationEngine("validate");
        if(!validateBack){
            return false;
        }else{
            if($(".tblOrderList").find(".product").val() == undefined){
                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>Please make an order first.</p>');
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
            }else{
                return true;
            }
        }
    }
    
    function errorSaveOrder(){
        $("#dialog").html('<p style="color:red; font-size:14px;"><?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?></p>');
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
            close: function(){
                $(this).dialog({close: function(){}});
                $(this).dialog("close");
                var rightPanel=$("#DoctorAddForm").parent();
                var leftPanel=rightPanel.parent().find(".leftPanel");
                rightPanel.hide();rightPanel.html("");
                leftPanel.show("slide", { direction: "left" }, 500);
                oCache.iCacheLower = -1;
                oTableOrder.fnDraw(false);
            },
            buttons: {
                '<?php echo ACTION_CLOSE; ?>': function() {
                    $(this).dialog("close");
                }
            }
        });
    }
    
    function codeDialogOrder(){
        $("#dialog").html('<p style="color:red; font-size:14px;"><?php echo MESSAGE_CODE_ALREADY_EXISTS_IN_THE_SYSTEM; ?></p>');
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
            close: function(){
                $(this).dialog({close: function(){}});
                $(this).dialog("close");
                $(".saveOrder").removeAttr("disabled");
                $(".txtSaveOrder").html("<?php echo ACTION_SAVE; ?>");
            },
            buttons: {
                '<?php echo ACTION_CLOSE; ?>': function() {
                    $(this).dialog("close");
                    $(".saveOrder").removeAttr("disabled");
                    $(".txtSaveOrder").html("<?php echo ACTION_SAVE; ?>");
                }
            }
        });
    }
</script>
<?php 
if(empty($patientLeave['PatientLeave'])){
    echo $this->Form->create('Doctor', array('id' => 'DoctorAddForm', 'url' => '/doctors/tabPrescription/' . $this->params['pass'][0].'/'.$this->params['pass'][1], 'enctype' => 'multipart/form-data')); 
    $queryPatient = mysql_query("SELECT patients.* FROM patients INNER JOIN queues ON patients.id = queues.patient_id WHERE queues.id = {$queueId}");
    $resultPatient = mysql_fetch_array($queryPatient); 
    ?>
    <input id="patientId" type="hidden" value="<?php echo $resultPatient['id'];?>"/>
    <input name="data[Doctor][patient_id]" type="hidden" value="<?php echo $resultPatient['id'];?>"/>
    <input name="data[Doctor][queue_doctor_id]" type="hidden" value="<?php echo $queueDoctorId;?>"/>
    <input name="data[Doctor][queue_id]" type="hidden" value="<?php echo $queueId;?>"/>
    <input type="hidden" value="" name="data[Order][currency_center_id]" id="OrderCurrencyCenterId" />
    <div style="display: none;">
        <legend id="showSaleOrderInfoIPD" style="display:none;"><a href="#" id="btnSalesOrderInfoShow" style="background: #CCCCCC; font-weight: bold;"><?php __(MENU_ORDER_INFO); ?> [ Show ] </a> </legend>
        <fieldset id="CMTopOrderDoctorIPD">
            <legend><a href="#" id="btnSalesOrderInfo" style="background: #CCCCCC; font-weight: bold;"><?php __(MENU_ORDER_INFO); ?> [ Hide ] </a> </legend>
            <table style="width: 100%; margin-left: 10px; border-spacing:0 10px;">
                <tr style="width: 100%;">
                    <td style="width: 35%; vertical-align: top;">
                        <label for="OrderCompanyId"><?php echo TABLE_COMPANY; ?> <span class="red">*</span> </label>
                        <?php echo $this->Form->input('company_id', array('default' => 1, 'empty' => INPUT_SELECT, 'label' => false, 'class' => 'chzn-select validate[required]', 'style' => 'width:88%')); ?>
                    </td>

                    <td style="padding-left: 30px; width: 25%; vertical-align: top;">
                        <label for="OrderBranchId"><?php echo MENU_BRANCH; ?> <span class="red">*</span></label>
                        <div class="inputContainer" style="width:100%">
                            <select name="data[Order][branch_id]" id="OrderBranchId" class="chzn-select validate[required]" style="width: 90%;">
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

                    <td style="padding-left: 30px; width: 30%; vertical-align: top;">
                        <?php 
                        $appointmentId = "";
                        $appointmentDate = "";
                        $appointmentDesc = "";
                        $queryAppointment = mysql_query("SELECT id, app_date, description FROM appointments WHERE queue_doctor_id = {$queueDoctorId}");
                        while ($rowAppointment = mysql_fetch_array($queryAppointment)) {
                            $appointmentId = $rowAppointment['id'];
                            $appointmentDate = $rowAppointment['app_date'];
                            $appointmentDesc = $rowAppointment['description'];
                        }
                        ?>
                        <label for="OrderOrderAppointmentDate"><?php echo APPOINTMENT_DATE; ?> </label>
                        <div class="inputContainer" style="width:100%">
                            <input type="hidden" name="data[Appointment][id]" value="<?php echo $appointmentId;?>" />
                            <?php echo $this->Form->text('appointment_date', array('readonly' => 'readonly', 'style' => 'width:80%', 'value' => $appointmentDate)); ?>
                        </div>
                    </td>
                    <td style="width: 5%; vertical-align: top;">

                    </td>
                </tr>

                <tr style="width: 100%;">
                    <td style="width: 35%; vertical-align: top;">
                        <label for="OrderOrderCode"><?php echo TABLE_SALES_ORDER_NUMBER; ?> <span class="red">*</span></label>
                        <div class="inputContainer" style="width:100%">
                           <input type="hidden" id="tableName" name="tableName" value="orders" />
                           <input type="hidden" id="fieldCurrentId" name="fieldCurrentId" value="" />
                           <input type="hidden" id="fieldName" name="fieldName" value="order_code" />
                           <input type="hidden" id="fieldCondition" name="fieldCondition" value="status > 0" />
                           <?php //echo $this->Form->text('order_code', array('value' => $code, 'id' => 'OrderOrderCode', 'class' => 'validate[required,ajax[ajaxUserCall]]', 'style' => 'width:85%')); ?>
                           <?php echo $this->Form->text('order_code', array('value' => $code, 'id' => 'OrderOrderCode', 'style' => 'width:85%')); ?>
                           <img alt="" src="<?php echo $this->webroot . 'img/button/cycle.png'; ?>" id="btnSmartCodeOrder" style="cursor: pointer; display: none;" onmouseover="Tip('Smart Code')" />
                        </div>
                    </td>

                    <td style="padding-left: 30px; width: 30%; vertical-align: top;">
                        <label for="DoctorOrderDate"><?php echo TABLE_SALES_ORDER_DATE; ?> <span class="red">*</span></label>
                        <div class="inputContainer" style="width:100%">
                            <?php echo $this->Form->text('order_date', array('value' => date("d/m/Y"),'readonly' => 'readonly', 'class' => 'validate[required]', 'style' => 'width:85%')); ?>
                        </div>    
                    </td>
                    <td style="padding-left: 30px; width: 30%; vertical-align: top;">
                        <label for="QuotationAppointmentDescription"><?php echo GENERAL_DESCRIPTION; ?> </label>
                        <div class="inputContainer" style="width:100%">
                            <?php echo $this->Form->input('description', array('label' => false, 'type' => 'textarea', 'style' => 'width:80%; height:60px;', 'value' => $appointmentDesc)); ?>
                        </div>
                    </td>

                    <td style="width: 5%; vertical-align: top;">
                        <table style="<?php if(count($locationGroups) == 1){ echo 'display:none;';}?>" cellpadding="0" style="width: 100%">
                            <tr>
                                <td><?php if(count($locationGroups) > 1){ ?><label for="DoctorLocationGroupId"><?php echo TABLE_LOCATION_GROUP; ?> <span class="red">*</span><?php } ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="inputContainer" style="width:100%; <?php if(count($locationGroups) == 1){ ?>display: none;<?php } ?>">
                                        <?php
                                        $emptyWare = INPUT_SELECT;
                                        if(count($locationGroups) == 1){
                                            $emptyWare = false;
                                        }
                                        echo $this->Form->input('location_group_id', array('empty' => $emptyWare, 'label' => false)); 
                                        ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>
    <div class="orderDetailOrderDoctorIPD" style=" margin-top: 5px; text-align: center;"></div>
    <br><br>
    <div class="footerSaveOrderDoctor" style="">
        <div style="float: left; width: 21%;">
            <div class="buttons">
                <button type="submit" class="positive saveOrder" >
                    <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
                    <span class="txtSaveOrder"><?php echo ACTION_SAVE; ?></span>
                </button>
            </div>
            <div style="clear: both;"></div>
        </div>
        <div style="clear: both;"></div>
    </div>
    <?php 
    echo $this->Form->end(); 
}
?>


