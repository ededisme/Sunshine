<?php
include('includes/function.php');
$absolute_url = FULL_BASE_URL . Router::url("/", false);
$exchangeRate = getExchangeRate();
$treatmentFee = getTreatmentFee($this->params['pass'][0]);
$tr = 1;
if ($treatmentFee >= 0)
    $tr++;
?>
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
</style>
<script type="text/javascript" src="<?php echo $this->webroot . 'js/jquery.formatCurrency-1.4.0.min.js'; ?>"></script>
<script type="text/javascript">
    var selected;
<?php
$queryPreviousExpense = mysql_query("SELECT IFNULL(SUM(total_amount-discount),0) FROM invoices WHERE queue_id IN (SELECT id FROM queues WHERE patient_id='" . $patient['Patient']['id'] . "')");
$dataPreviousExpense = mysql_fetch_array($queryPreviousExpense);
?>
    var previousExpense=<?php echo $dataPreviousExpense[0]; ?>;
    var numDiscount=0;       
    function calc(){
        var amount_d=0;
        var numDiscount=0;
        var exchange_rate=$("#exchange_rate").text();
       // amount_d+=parseFloat($("#room_fee").val());
        $(".total_price_d").each(function(){
            amount_d+=parseFloat(Number($(this).val()));
        });
        $("#treatment_fee").each(function(){
            amount_d+=parseFloat(Number($(this).val()));
        });
        $("#label_amount_r").text((amount_d*exchange_rate).toFixed(10)).formatCurrency({colorize:true,symbol: '៛'});
        $("#label_amount_d").text(amount_d.toFixed(10)).formatCurrency({colorize:true});

        var paid=(Number($("#total_amount_r").val())/exchange_rate)+parseFloat(Number($("#total_amount_d").val()));
        var balance=amount_d-paid;
        var discount=0;
        if($("#discount_p").val()!=0){
            discount=amount_d*$("#discount_p").val()/100;
            balance-=discount;
        }else if($("#discount_d").val()!=0){
            discount=$("#discount_d").val();
            balance-=discount;
        }

        // discount by expense
        var totalExpense=Number(amount_d-discount-Number($("#discount_d_by_code").val()))+Number(previousExpense);
        $("#totalExpense").text(totalExpense.toFixed(10)).formatCurrency({colorize:true});
        var remaining=numDiscount*500+500-totalExpense;
        if(remaining>0){
            $("#msgTotalExpense").text(remaining.toFixed(10)).formatCurrency({colorize:true});
            $("#msgTotalExpense2").text("remaining");
            $("#discount_d_by_expense").val(0);
        }else{
            $("#num_discount").val(Math.ceil(Math.abs(remaining)/500));
            $("#msgTotalExpense").text("discount");
            $("#msgTotalExpense2").text("x " + $("#num_discount").val());
            $("#discount_d_by_expense").val(25*$("#num_discount").val());
        }

        $("#label_balance_r").text((balance*exchange_rate).toFixed(10)).formatCurrency({colorize:true,symbol: '៛'});
        $("#label_balance_d").text(balance.toFixed(10)).formatCurrency({colorize:true});

        // set price to hidden field
        $("#amount").val(amount_d);
        $("#balance").val(balance);
        $("#discount").val(discount);
    }
    function checkDiscount(field, rules, i, options){
        if($("#discount_p").val()!=0 && $("#discount_d").val()!=0){
            return "<?php echo VALIDATION_ALLOW_1_METHOD_ONLY; ?>";
        }
    }
    $(document).ready(function(){
   
        $("#total_amount_d").select().focus();
        calc();                        
        $("#total_amount_d").click(function(){              
            if($(this).val()!=""){
                $(this).val("");                        
            }                       
        });  
        $("#total_amount_r").click(function(){                    
            if($(this).val()!=""){
                $(this).val("");                        
            }                       
        });  
        $("#discount_d").click(function(){                    
            if($(this).val()!=""){
                $(this).val("");                        
            }                       
        });
        $(".forHide").hide();
        $("#total_amount_d").focus();

        $("#total_amount_d").blur(function(){            
            if($(this).val()==''){
                $(this).val();
            }
        });        
        $("#total_amount_r").keyup(function(){
            calc();
        });
        $("#total_amount_d").keyup(function(){            
            calc();
        });
        $("#total_amount_r").blur(function(){
            calc();
            if($(this).val()==''){
                $(this).val(0);
            }
        });
        $("#total_amount_d").blur(function(){
            calc();
            if($(this).val()==''){
                $(this).val(0);
            }
        });
        $("#discount_p").keyup(function(){
            calc();
        });
        $("#discount_d").keyup(function(){
            calc();
        });
        $("#discount_p").blur(function(){
            calc();
            if($(this).val()==''){
                $(this).val(0);
            }
        });
        $("#discount_d").blur(function(){
            calc();
            if($(this).val()==''){
                $(this).val(0);
            }
        });
        $("#btnSubmit").click(function(){
            var isFormValidated=$("#CheckoutForm").validationEngine('validate');
            if(!isFormValidated){
                return false;
            }else{                           
                var exchange_rate=$("#exchange_rate").text();
                var paid=(Number($("#total_amount_r").val())/exchange_rate)+parseFloat(Number($("#total_amount_d").val()));
                var amount_d = parseFloat($(".total_price_d").val());  
                var discount = parseFloat($("#discount_d").val());
                $a=parseFloat($(".total_price_d").val()); 
                if($("#total_amount_r").val()==0 && $("#total_amount_d").val()==0){
                    $("#btnPrintReceipt").hide();
                }
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
                    var url = '<?php echo $absolute_url . $this->params['controller']; ?>/select_service/<?php echo $this->params['pass'][0]; ?>';
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
                            $("#invoice").load("<?php echo $absolute_url; ?>cashiers/printInvoiceService/" + invoiceId);
                            $("#btnPrintInvoice").click(function(){
                                w=window.open();
                                w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                                w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                                w.document.write($("#invoice").html());
                                w.document.close();
                                w.print();
                                w.close();
                            });
                            $("#receipt").load("<?php echo $absolute_url; ?>cashiers/printReceiptService/" + invoiceId);
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
<h1 class="title"><?php __(GENERAL_INVOICE); ?></h1>
<table class="info">    
    <tr>        
        <th><?php echo PATIENT_CODE; ?></th>
        <td><?php echo $patient['Patient']['cde']; ?></td>
        <th><?php echo PATIENT_NAME; ?></th>
        <td><?php echo $patient['Patient']['nme']; ?></td>
        <th>&nbsp;</th>
        <td>&nbsp;</td>
    </tr>    
    <tr>
        <th><?php echo TABLE_SEX; ?></th>
        <td><?php echo $patient['Patient']['sex']; ?></td>
        <th><?php echo TABLE_AGE; ?></th>
        <td>
            <?php
            echo $patient['Patient']['dob'];
            ?>
        </td>
    </tr>
    <tr>
        <th><?php echo TABLE_ADDRESS; ?></th>
        <td>
            <?php
            $queryLocation = mysql_query("SELECT nme FROM lcts WHERE id=" . $patient['Patient']['lct_id']);
            $dataLocation = mysql_fetch_array($queryLocation);
            echo $dataLocation['nme'];
            ?>
        </td>
        <th><?php echo TABLE_TELEPHONE; ?></th>
        <td><?php echo $patient['Patient']['phn']; ?></td>        
        <th>&nbsp;</th>
        <td>&nbsp;</td>
    </tr>    
</table>
<br />
<?php echo $this->Form->create('SelectService', array('id' => 'CheckoutForm', 'url' => '/cashiers/select_service/' . $this->params['pass'][0])); ?>
<input type="hidden" name="action" value="select_service" />
<input type="hidden" name="patient_id" value="<?php echo getPatientIdByQueueId($this->params['pass'][0]); ?>" />
<input type="hidden" name="exchange_rate_id" value="<?php echo getExchangeRateId(); ?>" />
<input type="hidden" name="exchange_rate" value="<?php echo $exchangeRate; ?>" />
<input type="hidden" class="total_price_d" name="treatment_fee" value="<?php echo $treatmentFee; ?>" />
<input type="hidden" id="amount" name="amount" value="" />
<input type="hidden" id="balance" name="balance" value="" />
<br />
<table id="service" class="table" cellspacing="0">
    <tr>
        <th class="first"><?php echo SECTION_SECTION; ?></th>
        <th><?php echo SERVICE_SERVICE; ?></th>
        <th><?php echo GENERAL_QTY; ?></th>
        <th><?php echo GENERAL_UNIT_PRICE; ?> ($)</th>
        <th><?php echo GENERAL_TOTAL_PRICE; ?> ($)</th>
    </tr>
    <?php  $totalPriceLast = 0;
    ?>
    <tr>
        <td class="first">
            <select id="section_id" name="section_id[]" class="section validate[optional]">
                <option value="">Please Select</option>
                <?php
                $query=mysql_query("SELECT * FROM sections");
                while($data=  mysql_fetch_array($query)){?>
                <option value="<?php echo $data['id']; ?>"><?php echo $data['name']; ?></option>
                <?php } ?>
            </select>
        </td>
        <td>
            <select id="service_id" name="service_id[]" class="service validate[optional]">
                <option value="">Please Select</option>
                <?php
                $query=mysql_query("SELECT * FROM services WHERE services.sts=1");
                while($data=  mysql_fetch_array($query)){?>
                <option class="<?php echo $data['section_id']; ?>" title="<?php echo $data['price_us']; ?>" value="<?php echo $data['id']; ?>"><?php echo $data['name']; ?></option>
                <?php } ?>
            </select>
        </td>
        <td><input type="text" id="qty" name="qty[]" class="qty validate[optional,custom[integer]]" /></td>
        <td><input type="text" name="unit_price_d[]" style="" class="unit_price_d" readonly="readonly" /></td>
        <td>
            <input type="text" name="total_price_d[]" class="total_price_d" readonly="readonly" />
            <div style="float: right;">
                <img alt="" src="<?php echo $this->webroot.'img/button/plus.png'; ?>" class="btnAdd" style="cursor: pointer;" onmouseover="Tip('Add New Service')" />
                <img alt="" src="<?php echo $this->webroot.'img/button/cross.png'; ?>" class="btnRemove" style="cursor: pointer;display: none;" onmouseover="Tip('Remove')" />
            </div>
        </td>
        
    </tr>
</table>
<br>
<table class="table_solid" style="width: 600px;">
    <tr>
        <th style="width: 37%;"><?php echo GENERAL_EXCHANGE_RATE; ?></th>
        <td colspan="2" style="text-align: right;">1$ = <span id="exchange_rate"><?php echo $exchangeRate; ?></span>៛</td>
    </tr>
    <tr>
        <th><?php echo GENERAL_AMOUNT; ?></th>
        <td id="label_amount_r" style="text-align: right;"></td>
        <td id="label_amount_d" style="text-align: right;"></td>
    </tr>
    <tr>
        <th><?php echo GENERAL_DISCOUNT; ?></th>
        <td><input class="forHide" type="text" id="discount_p" name="discount_p" value="0" class="validate[custom[number],funcCall[checkDiscount]]" /></td>
        <td><input type="text" id="discount_d" name="discount_d" value="0" class="validate[custom[number],funcCall[checkDiscount]]" /> ($)</td>
    </tr>
    <tr class="forHide">
        <th><?php echo GENERAL_DISCOUNT_BY_CODE; ?></th>
        <td>
            <input type="hidden" id="discount_code" name="discount_code" />
            <input type="text" id="discount_code_input" name="discount_code_input" />
            <input type="button" id="btnValidateCode" value="Check" />
        </td>
        <td><input type="text" id="discount_d_by_code" name="discount_d_by_code" value="0" readonly="readonly" /> ($)</td>
    </tr>
    <tr class="forHide">
        <th><?php echo GENERAL_DISCOUNT_BY_EXPENSE; ?></th>
        <td>
            <span id="totalExpense"></span> (<b id="msgTotalExpense"></b> <span id="msgTotalExpense2"></span>)
        </td>
        <td><input type="text" id="discount_d_by_expense" name="discount_d_by_expense" value="0" readonly="readonly" /> ($)</td>
    </tr>
    <tr>
        <th><?php echo GENERAL_BALANCE; ?></th>
        <td id="label_balance_r" style="text-align: right;font-size: 16px;font-weight: bold;"></td>
        <td id="label_balance_d" style="text-align: right;font-size: 16px;font-weight: bold;"></td>
    </tr>
    <tr>
        <th><?php echo GENERAL_AMOUNT_PAID; ?></th>
        <td><input type="text" id="total_amount_r" name="total_amount_r" class="validate[custom[number]]" /> (៛)​</td>
        <td><input type="text" id="total_amount_d" name="total_amount_d" class="validate[custom[number]]" value=""/> ($)</td>
    </tr>
</table>
<br />
<div class="buttons">
    <button type="button" id="btnSubmit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
<?php echo ACTION_SAVE; ?>
    </button>

    <a href="<?php echo $this->base; ?>/dashboards/cashier/" class="negative">
        <img src="<?php echo $this->webroot; ?>img/button/cross.png" alt=""/>
<?php echo ACTION_CANCEL; ?>
    </a>
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
<?php echo $this->Form->end(); ?>
<script>
    function comboRefesh(){
        selected=new Array();
        $(".service").each(function(){
            if($(this).val()!=''){
                selected.push($(this).val());
            }
        });       
        $(".service").each(function(){
            if($(this).closest("tr").find("td .section").val()!=''){
                $(this).find("option[title='"  + $(this).closest("tr").find("td .section").val() + "']").show();
            }else{
                $(this).find("option").show();
            }
            for(var i in selected){
                if(selected[i]!=$(this).val()){
                    $(this).children("option[value=" + selected[i] + "]").hide();
                }
            }
        });
    }
    $(document).ready(function(){
        $("#CheckoutForm").validationEngine();
        $(".section").change(function(){
            if($(this).val()!=''){
                $(this).closest("tr").find("td .service").val('');
                $(this).closest("tr").find("td .service option[class!='']").hide();
                $(this).closest("tr").find("td .service option[class='"  + $(this).val() + "']").show();
            }else{
                $(this).closest("tr").find("td .service option").show();
            }        
            comboRefesh();
        });
        $(".service").change(function(){
            $(this).closest("tr").find("td .section").val($(this).find("option:selected").attr("class"));
            $(this).closest("tr").find("td .unit_price_d").val($(this).find("option:selected").attr("title"));
            
            comboRefesh();

            $(this).closest("tr").find("td .total_price_d").val($(this).closest("tr").find("td .qty").val()*$(this).closest("tr").find("td .unit_price_d").val());
            calc();
        });
        $(".btnAdd").click(function(){
            $("#service").find("tr:last").clone(true).appendTo("#service");

            $("#service").find("tr:last").find("td .qty").val('');
            $("#service").find("tr:last").find("td .unit_price_d").val('');
            $("#service").find("tr:last").find("td .total_price_d").val('');
            $("#service").find("tr:last").find("td .btnRemove").show();

            $(this).siblings(".btnRemove").show();
            $(this).hide();

            comboRefesh();
        });
        $(".btnRemove").click(function(){
            $(this).closest("tr").remove();
            $("#service").find("tr:last").find("td .btnAdd").show();
            if($('#service tr').length==<?php echo $tr+1; ?>){
                $("#service").find("tr:eq(<?php echo $tr; ?>)").find("td .btnRemove").hide();
            }
            comboRefesh();
            calc();
        });
        $(".qty").keyup(function(){
            $(this).closest("tr").find("td .total_price_d").val($(this).val()*$(this).closest("tr").find("td .unit_price_d").val());
            calc();
        });
    //end
    });
</script>