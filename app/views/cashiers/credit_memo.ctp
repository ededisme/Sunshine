<?php

include('includes/function.php');

$absolute_url  = FULL_BASE_URL . Router::url("/", false);

$exchangeRate = getExchangeRate();

$cityProvince = array(
    0 => 'Banteay Meanchey',
    1 => 'Battambang',
    2 => 'Kampong Cham',
    3 => 'Kampong Chhnang',
    4 => 'Kampong Speu',
    5 => 'Kampong Thom',
    6 => 'Kampot',
    7 => 'Kandal',
    8 => 'Kep',
    9 => 'Koh Kong',
    10 => 'Kratie',
    11 => 'Mondul Kiri',
    12 => 'Otdar Meanchey',
    13 => 'Pailin',
    14 => 'Phnom Penh',
    15 => 'Preah Vihear',
    16 => 'Prey Veng',
    17 => 'Pursat',
    18 => 'Ratanak Kiri',
    19 => 'Siemreap',
    20 => 'Sihanoukville',
    21 => 'Stueng Treng',
    22 => 'Svay Rieng',
    23 => 'Takeo'
);

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
<script type="text/javascript" src="<?php echo $this->webroot.'js/jquery.formatCurrency-1.4.0.min.js'; ?>"></script>
<script type="text/javascript">
    var selected;
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
    function calc(){
        var amount_d=0;
        var exchange_rate=$("#exchange_rate").text();

        $(".total_price_d").each(function(){
            amount_d+=parseFloat(Number($(this).val()));
        });
        
        $("#label_amount_r").text((amount_d*exchange_rate).toFixed(10)).formatCurrency({colorize:true,symbol: '៛'});
        $("#label_amount_d").text(amount_d.toFixed(10)).formatCurrency({colorize:true});

        // set price to hidden field
        $("#amount").val(amount_d);
    }
    $(document).ready(function(){
        calc();
        $("#CheckoutForm").validationEngine();
        $(".section").change(function(){
            if($(this).val()!=''){
                $(this).closest("tr").find("td .service").val('');
                $(this).closest("tr").find("td .service option[class!='']").hide();
                $(this).closest("tr").find("td .service option[class='"  + $(this).val() + "']").show();
            }else{
                $('#test2').html('hi');
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
            $("#tblService").find("tr:last").clone(true).appendTo("#tblService");

            $("#tblService").find("tr:last").find("td .qty").val('');
            $("#tblService").find("tr:last").find("td .unit_price_d").val('');
            $("#tblService").find("tr:last").find("td .total_price_d").val('');
            $("#tblService").find("tr:last").find("td .btnRemove").show();

            $(this).siblings(".btnRemove").show();
            $(this).hide();

            comboRefesh();
        });
        $(".btnRemove").click(function(){
            $(this).closest("tr").remove();
            $("#tblService").find("tr:last").find("td .btnAdd").show();
            if($('#tblService tr').length==2){
                $("#tblService").find("tr:eq(1)").find("td .btnRemove").hide();
            }
            comboRefesh();
            calc();
        });
        $(".qty").keyup(function(){
            $(this).closest("tr").find("td .total_price_d").val($(this).val()*$(this).closest("tr").find("td .unit_price_d").val());
            calc();
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
                var url = '<?php echo $absolute_url.$this->params['controller']; ?>/creditMemo/<?php echo $this->params['pass'][0]; ?>';
                var post = $('#CheckoutForm').serialize();
                $.post(url,post,function(rs){
                    if(rs.indexOf('success')!=-1){
                        rs=rs.split("/");
                        var creditMemoId=rs[1];
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
                        $("#creditMemo").load("<?php echo $absolute_url; ?>cashiers/printCreditMemo/" + creditMemoId);
                        $("#btnPrintCreditMemo").click(function(){
                            w=window.open();
                            w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                            w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                            w.document.write($("#creditMemo").html());
                            w.document.close();
                            w.print();
                            w.close();
                        });
                    }
                });
            }
        });
    });
</script>
<h1 class="title"><?php __(GENERAL_CREDIT_MEMO);?></h1>
<table class="info">
    <tr>
        <th><?php echo PATIENT_CODE; ?></th>
        <td><?php echo $patient['Patient']['patient_code']; ?></td>
        <th><?php echo PATIENT_NAME; ?></th>
        <td><?php echo $patient['Patient']['patient_name']; ?></td>
    </tr>    
    <tr>
        <th><?php echo TABLE_SEX; ?></th>
        <td><?php echo $patient['Patient']['sex']; ?></td>
        <th><?php echo TABLE_AGE; ?></th>
        <td>
            <?php
            list($curYear,$curMonth,$curDay) = split('-',date('Y-m-d'));
            list($year,$month,$day) = split('-',$patient['Patient']['dob']);
            echo $curYear-$year . ' ' . GENERAL_YEAR_OLD;
            ?>
        </td>
    </tr>
    <tr>
        <th><?php echo TABLE_ADDRESS; ?></th>
        <td colspan="3"><?php echo $patient['Patient']['address']; ?><?php echo $patient['Patient']['address']!=''?', ':''; ?><?php echo $patient['Patient']['city_province']!=''?$cityProvince[$patient['Patient']['city_province']]:''; ?></td>
    </tr>
    <tr>
        <th><?php echo TABLE_TELEPHONE; ?></th>
        <td><?php echo $patient['Patient']['telephone']; ?></td>
        <th><?php echo TABLE_EMAIL; ?></th>
        <td><?php echo $patient['Patient']['email']; ?></td>
    </tr>
    <tr>
        <th><?php echo TABLE_NATIONALITY; ?></th>
        <td colspan="3"><?php echo $nationality; ?></td>
    </tr>
</table>
<br />
<?php echo $this->Form->create('Checkout', array ('id'=>'CheckoutForm','url'=>'/cashiers/creditMemo/'.$this->params['pass'][0]));?>
<input type="hidden" name="action" value="checkout" />
<input type="hidden" name="patient_id" value="<?php echo $this->params['pass'][0]; ?>" />
<input type="hidden" name="exchange_rate_id" value="<?php echo getExchangeRateId(); ?>" />
<input type="hidden" name="exchange_rate" value="<?php echo $exchangeRate; ?>" />
<input type="hidden" id="amount" name="amount" value="" />
<table id="tblService" class="table" cellspacing="0">
    <tr>
        <th class="first"><?php echo SECTION_SECTION; ?></th>
        <th><?php echo SERVICE_SERVICE; ?></th>
        <th><?php echo GENERAL_QTY; ?></th>
        <th><?php echo GENERAL_UNIT_PRICE; ?> ($)</th>
        <th><?php echo GENERAL_TOTAL_PRICE; ?> ($)</th>
    </tr>
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
                $query=mysql_query("SELECT * FROM services WHERE is_active=1");
                while($data=  mysql_fetch_array($query)){?>
                <option class="<?php echo $data['section_id']; ?>" title="<?php echo $patient['Patient']['nationality']==36?$data['price']:$data['price_foreigner']; ?>" value="<?php echo $data['id']; ?>"><?php echo $data['name']; ?></option>
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
            <button type="button" id="btnPrintCreditMemo" class="positive">
                <img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/>
                <?php echo GENERAL_CREDIT_MEMO; ?>
            </button>
        </div>
    </center>
</div>
<div id="dialog2" title="" style="display: none;"></div>
<div id="creditMemo" style="display: none;"></div>
<?php echo $this->Form->end(); ?>