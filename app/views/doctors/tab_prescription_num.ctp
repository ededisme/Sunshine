<?php
if (empty($orderResult)) {
    echo GENERAL_NO_RECORD;
    exit();
}
require_once("includes/function.php");
?>
<?php $absolute_url = FULL_BASE_URL . Router::url("/", false); ?>
<?php $tblName = "tbl123"; ?>
<script type="text/javascript">
    var tabOrderReg;
    var tabOrderId;
    var queue_id = $("#queue_id").val();
    var queue_doctor_id = $("#queue_doctor_id").val();
    $(document).ready(function() {
        $('.legend').hide();
        $("#contentMedicine<?php echo $tblName; ?>").accordion({
            collapsible: true,
            autoHeight: false,
            navigation: false,
            active: false
        });

        setTimeout(function() {
            equalHeight($(".column"));
        }, 20000);

        $(".btnPrint").click(function(event) {
            event.preventDefault();
            $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            event.stopPropagation();
            var btnPatientOrderFormFirst = $("#dialogOrderPrint").html();
            var orderId = $(this).attr('orderId');
            var name = $(this).attr('name');

            $("#dialog").html('<div class="buttons"><button type="submit" class="positive printPatientNumPrescriptionForm" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span><?php echo ACTION_PRINT_PRESCRIPTION; ?></span></button><button type="submit" class="positive printPatientNumPrescriptionFormNoHeader" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span><?php echo ACTION_PRINT_PRESCRIPTION_NO_HEADER; ?></span></button></div>');
            $(".printPatientNumPrescriptionForm").click(function() {
                $.ajax({
                    type: "POST",
                    url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printInvoice/" + orderId,
                    beforeSend: function() {
                        $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner.gif');
                    },
                    success: function(printInvoiceResult) {
                        w = window.open();
                        w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                        w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                        w.document.write(printInvoiceResult);
                        w.document.close();
                        $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    }
                });
            });

            $(".printPatientNumPrescriptionFormNoHeader").click(function() {
                $.ajax({
                    type: "POST",
                    url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printInvoice/" + orderId + "/1",
                    beforeSend: function() {
                        $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner.gif');
                    },
                    success: function(printInvoiceResult) {
                        w = window.open();
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
                position: 'center',
                open: function(event, ui) {
                    $(".ui-dialog-buttonpane").show();
                },
                buttons: {
                    '<?php echo ACTION_CLOSE; ?>': function() {
                        $(this).dialog("close");
                    }
                }
            });
        });
        $(".btnEdit").click(function(event) {
            event.preventDefault();
            event.stopPropagation();
            var orderId = $(this).attr("orderId");
            var leftPanel = $(this).parent().parent().parent();
            var rightPanel = leftPanel.parent().find(".tabPrescriptionNum");
            leftPanel.hide("slide", {
                direction: "left"
            }, 500, function() {
                rightPanel.show();
            });
            rightPanel.html("<?php echo ACTION_LOADING; ?>");
            $(".tabPrescriptionNum").load("<?php echo $this->base . "/orders"; ?>/edit/" + orderId + "/2");
        });
    });

    function equalHeight(group) {
        var tallest = 0;
        group.each(function() {
            var thisHeight = $(this).height();
            if (thisHeight > tallest) {
                tallest = thisHeight;
            }
        });
        group.height(tallest);
    }
</script>
<div id="contentMedicine<?php echo $tblName; ?>">
    <?php
    $ind = 1;
    foreach ($orderResult as $order) :
    ?>
        <h3>
            <input type="hidden" value="<?php echo $order['Order']['queue_id']; ?>" name="data[Order][queue_id]" id="queue_id" />
            <input type="hidden" value="<?php echo $order['Order']['queue_doctor_id']; ?>" name="data[Order][queue_doctor_id]" id="queue_doctor_id" />
            <a href="#">
                <?php echo "# : "; ?>
                <?php echo $order['Order']['order_code'] != "" ? $order['Order']['order_code'] . ' - ' . date('d/m/Y H:i:s', strtotime($order['Order']['created'])) : "" . date('d/m/Y H:i:s', strtotime($order['Order']['created'])); ?>
                <div style="float:right;">
                    <img alt="" src="<?php echo $this->webroot; ?>img/button/printer.png" class="btnPrint" orderId="<?php echo $order['Order']['id']; ?>" name="<?php echo $order['Order']['order_code']; ?>" onmouseover="Tip('<?php echo ACTION_PRINT; ?>')" />
                </div>
                <!-- <?php
                        $date_create  = date('d-m-Y H:i:s', strtotime($order['Order']['created']));
                        $current_date = date('d-m-Y H:i:s');
                        $datetime     = strtotime($current_date) - strtotime($date_create);
                        $hours = floor($datetime / 3600);
                        if ($hours < 24) { ?> 
                    <div style="float:right;margin-right:10px;">
                        <img alt="" src="<?php echo $this->webroot; ?>img/action/edit.png" class="btnEdit" orderId="<?php echo $order['Order']['id']; ?>" name="<?php echo $order['Order']['order_code']; ?>" onmouseover="Tip('<?php echo ACTION_EDIT; ?>')" />
                    </div>
                <?php } ?> -->
                <div style="float:right;margin-right:10px;">
                    <img alt="" src="<?php echo $this->webroot; ?>img/action/edit.png" class="btnEdit" orderId="<?php echo $order['Order']['id']; ?>" name="<?php echo $order['Order']['order_code']; ?>" onmouseover="Tip('<?php echo ACTION_EDIT; ?>')" />
                </div>

            </a>
        </h3>
        <div class="<?php echo $order['Order']['id']; ?>">
            <div>
                <div>
                    <table class="table_print" style="border: none;">
                        <tr>
                            <th class="first" style="font-size: 11px;"><?php echo TABLE_NO; ?></th>
                            <th style="text-transform: uppercase; font-size: 11px;"><?php echo TABLE_SKU; ?></th>
                            <th style="text-transform: uppercase; font-size: 11px; width: 20%;"><?php echo TABLE_PRODUCT_NAME; ?></th>
                            <th style="text-transform: uppercase; font-size: 11px; text-align: center;"><?php echo TABLE_QTY ?></th>
                            <th style="text-transform: uppercase; font-size: 11px; text-align: center;"><?php echo TABLE_UOM; ?></th>
                            <th style="text-transform: uppercase; font-size: 11px;width:20%; text-align: center;"><?php echo TABLE_INSTRUCTION; ?></th>
                            <th style="text-transform: uppercase; font-size: 11px;width:20%; text-align: center;"><?php echo TABLE_MORNING; ?></th>
                        </tr>
                        <?php
                        $index = 0;
                        $totalPrice = 0;
                        $queryOrderDetail = mysql_query("SELECT order_details.*, uoms.abbr, products.code, products.name FROM order_details INNER JOIN products ON products.id = order_details.product_id INNER JOIN uoms ON uoms.id = order_details.qty_uom_id WHERE order_id = {$order['Order']['id']}");
                        while ($orderDetail = mysql_fetch_array($queryOrderDetail)) {

                            // Check Name With Customer
                            $productName = $orderDetail['name'];
                        ?>
                            <tr>
                                <td class="first" style="text-align: center; font-size: 11px; width: 5%;"><?php echo ++$index; ?></td>
                                <td style="font-size: 11px; width: 7%;"><?php echo $orderDetail['code']; ?></td>
                                <td style="font-size: 11px;"><?php echo $productName; ?></td>
                                <td style="font-size: 11px; text-align: center;"><?php echo number_format($orderDetail['qty'], 0); ?></td>
                                <td style="font-size: 11px; text-align: center;"><?php echo $orderDetail['abbr']; ?></td>
                                <td style="text-align: right; font-size: 11px; text-align: center;"><?php echo $orderDetail['num_days'] != "" ? $orderDetail['num_days'] : '-'; ?></td>
                                <td style="text-align: right; font-size: 11px; text-align: center;">
                                    <?php
                                    if ($orderDetail['morning_use_id'] != "") {
                                        $queryMedicineUse = mysql_query("SELECT name FROM treatment_uses WHERE id = {$orderDetail['morning_use_id']}");
                                        while ($resultMedicineUse = mysql_fetch_array($queryMedicineUse)) {
                                            echo $resultMedicineUse['name'];
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php
                        }
                        $queryOrderMisc = mysql_query("SELECT order_miscs.*, uoms.abbr FROM order_miscs INNER JOIN uoms ON uoms.id = order_miscs.qty_uom_id WHERE order_id = {$order['Order']['id']}");
                        while ($orderMisc = mysql_fetch_array($queryOrderMisc)) {
                        ?>
                            <tr>
                                <td class="first" style="text-align: center; font-size: 11px;"><?php echo ++$index; ?></td>
                                <td style="font-size: 11px;"></td>
                                <td style="font-size: 11px;"><?php echo $orderMisc['description']; ?></td>
                                <td style="font-size: 11px; text-align: center;"><?php echo number_format($orderMisc['qty'], 0); ?></td>
                                <td style="font-size: 11px; text-align: center;"><?php echo $orderMisc['abbr']; ?></td>
                                <td style="text-align: right; font-size: 11px; text-align: center;"><?php echo $orderMisc['num_days'] != "" ? $orderMisc['num_days'] : '-'; ?></td>
                                <td style="text-align: right; font-size: 11px; text-align: center;">
                                    <?php
                                    if ($orderMisc['morning_use_id'] != "") {
                                        $queryMedicineUse = mysql_query("SELECT name FROM treatment_uses WHERE id = {$orderMisc['morning_use_id']}");
                                        while ($resultMedicineUse = mysql_fetch_array($queryMedicineUse)) {
                                            echo $resultMedicineUse['name'];
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </table>
                </div>
                <div style="clear:both"></div>
            </div>
        </div>
    <?php $ind++;
    endforeach;
    ?>
</div>
<div id="dialog" title=""></div>
<div id="dialogOrderPrint" title="" style="display: none;">
    <br />
    <center>
        <div class="buttons" style="display: inline-block;">
            <button type="button" id="btnPatientOrderForm" class="positive">
                <img src="<?php echo $this->webroot; ?>img/button/printer.png" alt="" />
                <?php echo ACTION_PRINT; ?>
            </button>
        </div>
    </center>
</div>
<div id="patientOrderForm" style="display: none;"></div>