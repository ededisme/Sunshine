<?php
include('includes/function.php');

$absolute_url = FULL_BASE_URL . Router::url("/", false);

$exchangeRate = getExchangeRate();

$treatmentFee = getTreatmentFee($queueId);


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
        
        amount_d=parseFloat($("#treatment_fee").val());                    
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
            if($(this).val()==''){
                $(this).val(0);
            }
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
                    var url = '<?php echo $absolute_url . $this->params['controller']; ?>/checkoutPassDoctor/<?php echo $this->params['pass'][0]; ?>';
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
        <th>NGo</th>
        <td>
            <?php
            if ($patient['Patient']['ngo_id'] == 0) {
                echo 'Without NGo';
            } else {
                $queryNgo = mysql_query("SELECT cde FROM ngos WHERE id=" . $patient['Patient']['ngo_id']);
                $dataNgo = mysql_fetch_array($queryNgo);
                echo $dataNgo['cde'];
            }
            ?>
        </td>
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
<?php echo $this->Form->create('Checkout', array('id' => 'CheckoutForm', 'url' => '/cashiers/checkout/' . $this->params['pass'][0])); ?>
<input type="hidden" name="action" value="checkout" />
<input type="hidden" name="patient_id" value="<?php echo getPatientIdByQueueId($this->params['pass'][0]); ?>" />
<input type="hidden" name="exchange_rate_id" value="<?php echo getExchangeRateId(); ?>" />
<input type="hidden" name="exchange_rate" value="<?php echo $exchangeRate; ?>" />
<input type="hidden" id="amount" name="amount" value="" />
<input type="hidden" id="balance" name="balance" value="" />
<input type="hidden" id="queueId" name="queueId" value="<?php echo $queueId?>" />
<input type="hidden" id="totalAmount" name="totalAmount" value="<?php echo $treatmentFee; ?>" />
<table id="tblService" class="table" cellspacing="0">
    <tr>
        <th class="first"><?php echo TABLE_NO; ?></th>            
        <th><?php echo DRUG_COMMERCIAL_NAME; ?></th>            
        <th><?php echo GENERAL_QTY; ?></th>
        <th><?php echo GENERAL_UNIT_PRICE . '($)'; ?></th>          
        <th><?php echo GENERAL_DISCOUNT . '(%)'; ?></th>
        <th><?php echo GENERAL_TOTAL_PRICE . '($)'; ?></th>
    </tr> 
    
    <?php if ($treatmentFee >= 0) { ?>
        <?php        
        $query = mysql_query("SELECT id FROM treatments WHERE queue_id=" .$queueId);

        $row = mysql_fetch_array($query);

        $query_drug = mysql_query("       SELECT td.id,td.amount,td.sale_stock_id,td.discount,stockOnHand.id AS stockOnhandId, stockOnHand.amount AS stockOnhandAmount
                                            FROM treatment_details td 
                                            INNER JOIN  stock_on_hands AS stockOnHand ON stockOnHand.treatment_detail_id = td.id
                                            WHERE td.treatment_id=" . $row['id']);
        $i = 1;
        $discountAll = 0;
        $type = "";
        $totalPriceAll = 0;
        while ($result = mysql_fetch_array($query_drug)) {
            $query_drug_next = mysql_query("  SELECT commercial_name,price_sale_out,mu.*,g.discount,s.id AS saleStockId,g.id AS grandStockId
                                                FROM grand_stocks g
                                                    INNER JOIN medicine_units mu ON g.medicine_unit_id=mu.id
                                                    INNER JOIN sale_stocks s ON g.id=s.grand_stock_id                                                    
                                                WHERE s.id=" . $result['sale_stock_id']);
            while ($finish = mysql_fetch_array($query_drug_next)) {
                $discount = $finish['discount'];
                $discountAll = $discountAll + $discount;
                if ($finish['medicine_type'] == "Injection") {
                    if ($finish['flacont'] == 0) {
                        $type = MEASURE_AMPOULE;
                    } else {
                        $type = MEASURE_FLACON;
                    }
                } else if ($finish['medicine_type'] == "Tablet") {
                    $type = MEASURE_TABLET;
                } else if ($finish['medicine_type'] == "Capsule") {
                    $type = MEASURE_CAPSULE;
                } else if ($finish['medicine_type'] == "Powder") {
                    $type = MEASURE_POWDER;
                } else if ($finish['medicine_type'] == "Syrup") {
                    $type = MEASURE_WATER;
                } else if ($finish['medicine_type'] == "Cream") {
                    $type = "tube";
                } else if ($finish['medicine_type'] == "Liquid") {
                    $type = " amp";
                } else if ($finish['medicine_type'] == "Other") {
                    if ($finish['flacont'] == 0) {
                        $type = " no";
                    } else {
                        $type = " fla";
                    }
                } else if ($finish['medicine_type'] == "Suppository") {
                    if ($finish['suppository'] == 0) {
                        $type = " unidoses";
                    } else {
                        $type = " sup";
                    }
                }
                echo '<tr>';
                echo '<td class="first">' . $i++ . '</td>';
                echo '<td>' . $finish['commercial_name'] . '</td>';
                echo '<td>' . $result['amount'] . ' ' . $type . '</td>';
                echo '<td>' . number_format($finish['price_sale_out'], 2) . '</td>';
                echo '<td class="discount">' . $discount . '</td>';
                echo '<td class="totalPrice">';
                if ($discount > 0) {
                    $totalPrice = $result['amount'] * $finish['price_sale_out'];
                    $totalDis = ($totalPrice * $discount) / 100;
                    $totalPrice = $totalPrice - $totalDis;
                } else {
                    $totalPrice = $result['amount'] * $finish['price_sale_out'];
                }
                echo number_format($totalPrice, 2);
                $totalPriceAll = $totalPriceAll+$totalPrice;
                echo '<input type="hidden" name="data[SaleStock][id][]" value="' . $finish['saleStockId'] . '" />';
                echo '<input type="hidden" name="data[SaleStock][amount][]" value="' . $result['amount'] . '" />';
                echo '<input type="hidden" name="data[GrandStock][id][]" value="' . $finish['grandStockId'] . '" />';
                echo '<input type="hidden" name="data[GrandStock][amount][]" value="' . $result['amount'] . '" />';
                echo "<input value='" . $result['stockOnhandId'] . "' type='hidden' name='data[StockOnHand][id][]' />";
                echo "<input value='" . $result['stockOnhandAmount'] . "' type='hidden' name='data[StockOnHand][amount][]' />";
                echo '</td>';
                echo '</tr>';
            }
        }
        ?>
        <input type="hidden" name="discount" value="<?php echo $discountAll; ?>" />
        <input type="hidden" id="treatment_fee" name="treatment_fee" value="<?php echo $totalPriceAll; ?>" />
    <?php } ?>
</table>
<br />
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
        <td><input type="text" id="total_amount_d" name="total_amount_d" class="validate[custom[number]]" value="<?php echo $totalPriceAll; ?>"/> ($)</td>
    </tr>
</table>
<br />
<div class="buttons">
    <button type="button" id="btnSubmit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
<?php echo ACTION_SAVE; ?>
    </button>

</div>
<div id="dialog" title="" style="display: none;">
    <br />
    <center>
        <div class="buttons" style="display: inline-block;">           
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