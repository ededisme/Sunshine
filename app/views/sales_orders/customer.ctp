<?php
// Authentication
$this->element('check_access');
$allowAddCustomer = checkAccess($user['User']['id'], 'customers', 'quickAdd');

$tblName = "tbl" . rand(); 
$rand    = rand();
?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(".table td:first-child").addClass('first');
        var oTable<?php echo $rand; ?> = $("#<?php echo $tblName; ?>").dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo $this->base . '/' . $this->params['controller']; ?>/customer_ajax/<?php echo $companyId; ?>?sale_id=<?php echo $saleId; ?>",
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                $("#dialog").dialog("option", "position", "center");
                $(".table td:first-child").addClass('first');
                $(".table td:last-child").css("white-space", "nowrap");
                $(".table tr").click(function(){
                    $(this).find("input[name='chkCustomer']").attr("checked", true);
                });
                return sPre;
            },
            "aoColumnDefs": [{
                "sType": "numeric", "aTargets": [ 0 ],
                "bSortable": false, "aTargets": [ 0,-1,-2 ]
            }],
            "aaSorting": [[ 2, "asc" ]]
        });
        $("#changeSOCustomerGroup").change(function(){
            var valueId = $(this).val();
            var Tablesetting = oTable<?php echo $rand; ?>.fnSettings();
            Tablesetting.sAjaxSource = "<?php echo $this->base . '/' . $this->params['controller']; ?>/customer_ajax/<?php echo $companyId; ?>/"+valueId+"?sale_id=<?php echo $saleId; ?>";
            oCache.iCacheLower = -1;
            oTable<?php echo $rand; ?>.fnDraw(false);
        });
        <?php
        if($allowAddCustomer){
        ?>
        $(".addCustomerSales").click(function(){
            $.ajax({
                type:   "GET",
                url:    "<?php echo $this->base . "/customers/quickAdd/"; ?>",
                beforeSend: function(){
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                    $(".addCustomerSales").attr("disabled", true);
                    $("#txtAddCustomerSales").text('<?php echo ACTION_LOADING; ?>');
                },
                success: function(msg){
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $(".addCustomerSales").attr("disabled", false);
                    $("#txtAddCustomerSales").text('<?php echo MENU_CUSTOMER_MANAGEMENT_ADD; ?>');
                    $("#dialog3").html(msg);
                    $("#dialog3").dialog({
                        title: '<?php echo MENU_CUSTOMER_MANAGEMENT_ADD; ?>',
                        resizable: false,
                        modal: true,
                        width: '550',
                        height: '600',
                        position:'center',
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show();
                            $(".ui-dialog-titlebar-close").show();
                        },
                        buttons: {
                            '<?php echo ACTION_CLOSE; ?>': function() {
                                $(this).dialog("close");
                            },
                            '<?php echo ACTION_SAVE; ?>': function() {
                                var formName = "#CustomerQuickAddForm";
                                var validateBack =$(formName).validationEngine("validate");
                                if(!validateBack){
                                    return false;
                                }else{
                                    if($("#CustomerCgroupId").val() == null || $("#CustomerCgroupId").val() == ''){
                                        alertSelectRequireField();
                                    } else {
                                        $(this).dialog("close");
                                        $.ajax({
                                            dataType: 'json',
                                            type: "POST",
                                            url: "<?php echo $this->base; ?>/customers/quickAdd",
                                            data: $("#CustomerQuickAddForm").serialize(),
                                            beforeSend: function(){
                                                $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                                            },
                                            error: function (result) {
                                                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                                createSysAct('Sales Invoice', 'Quick Add Customer', 2, result);
                                                $("#dialog1").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                                                $("#dialog1").dialog({
                                                    title: '<?php echo DIALOG_INFORMATION; ?>',
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
                                                        '<?php echo ACTION_CLOSE; ?>': function() {
                                                            $(this).dialog("close");
                                                        }
                                                    }
                                                });
                                            },
                                            success: function(result){
                                                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                                                createSysAct('Sales Invoice', 'Quick Add Customer', 1, '');
                                                var msg = '';
                                                if(result.error == 0){
                                                    msg = '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>';
                                                    // Set Customer
                                                    $("#SalesOrderCustomerId").val(result.id);
                                                    $("#SalesOrderCustomerName").val(result.name);
                                                    $("#SalesOrderCustomerName").attr("readonly","readonly");
                                                    $("#SalesOrderPaymentTermId").find("option[value='"+result.term+"']").attr("selected", true);
                                                    $(".searchCustomerSales").hide();
                                                    $(".deleteCustomerSales").show();
                                                    // Reset SO & Quotation
                                                    $(".deleteOrderSales").click();
                                                    $(".deleteQuotationSales").click();
                                                    getCustomerContactSales(result.id, '');
                                                    if(result.price != ''){
                                                        // Check Price Type Customer
                                                        customerPriceTypeSales(result.price, 0);
                                                    }
                                                    $.ajax({
                                                        dataType: 'json',
                                                        type: "POST",
                                                        url: "<?php echo $this->base . '/sales_orders'; ?>/customerCondition/"+result.id+"/0",
                                                        beforeSend: function(){
                                                        },
                                                        success: function(msg){
                                                            if(msg.error == 0){
                                                                // Condition
                                                                var limitBalance = msg.limit_balance;
                                                                var limitInvoice = msg.limit_invoice;
                                                                var totalBalanceUsed = msg.balance_used;
                                                                var totalInvoiceUsed = msg.invoice_used;
                                                                // Set Condition
                                                                $("#limitBalance").val(limitBalance);
                                                                $("#limitInvoice").val(limitInvoice);
                                                                $("#totalBalanceUsed").val(totalBalanceUsed);
                                                                $("#totalInvoiceUsed").val(totalInvoiceUsed);
                                                            }else{
                                                                $(".deleteCustomerSales").click();
                                                            }
                                                        }
                                                    });
                                                } else  if (result.error == 1){
                                                    msg = '<?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?>'; 
                                                } else  if (result.error == 2){
                                                    msg = '<?php echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM; ?>';
                                                }
                                                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+msg+'</p>');
                                                $("#dialog").dialog({
                                                    title: '<?php echo DIALOG_INFORMATION; ?>',
                                                    resizable: false,
                                                    modal: true,
                                                    width: 'auto',
                                                    height: 'auto',
                                                    position: 'center',
                                                    open: function(event, ui){
                                                        $(".ui-dialog-buttonpane").show();
                                                    },
                                                    buttons: {
                                                        '<?php echo ACTION_CLOSE; ?>': function() {
                                                            $(this).dialog("close");
                                                        }
                                                    }
                                                });
                                            }
                                        });
                                    }
                                }  
                            }
                        }
                    });
                }
            });
        });
        <?php
        }
        ?>
    });
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb; margin-bottom: 5px; display: none;">
    <?php 
    if($allowAddCustomer){
    ?>
    <div class="buttons">
        <a href="#" class="positive addCustomerSales">
            <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
            <span id="txtAddCustomerSales"><?php echo MENU_CUSTOMER_MANAGEMENT_ADD; ?></span>
        </a>
    </div>
    <?php 
    } 
    ?>
    <div style="float:right;">
        <?php echo GENERAL_GROUP; ?>:
        <select id="changeSOCustomerGroup" style="width:150px;">
            <option value=""><?php echo TABLE_ALL; ?></option>
            <?php
            $queryCgroup = mysql_query("SELECT * FROM cgroups WHERE is_active=1 AND (user_apply = 0 OR (user_apply = 1 AND id IN (SELECT cgroup_id FROM user_cgroups WHERE user_id = ".$user['User']['id']."))) AND id IN (SELECT cgroup_id FROM cgroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id'].")) ORDER BY name");
            while($dataCgroup=mysql_fetch_array($queryCgroup)){
            ?>
            <option value="<?php echo $dataCgroup['id']; ?>"><?php echo $dataCgroup['name']; ?></option>
            <?php
            }
            ?>
        </select>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<div id="dynamic">
    <table id="<?php echo $tblName; ?>" class="table" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th class="first"></th>
                <th><?php echo PATIENT_CODE; ?></th>
                <th><?php echo PATIENT_NAME; ?></th>
                <th><?php echo TABLE_SEX; ?></th>
                <th><?php echo TABLE_TELEPHONE; ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="5" class="dataTables_empty"><?php echo TABLE_LOADING; ?></td>
            </tr>
        </tbody>
    </table>
</div>