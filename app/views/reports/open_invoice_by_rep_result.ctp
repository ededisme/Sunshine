<?php

$rnd = rand();
$tblName = "tbl" . rand();
$printArea = "printArea" . $rnd;
$btnPrint = "btnPrint" . $rnd;
$btnExport = "btnExport" . $rnd;

include('includes/function.php');

/**
 * export to excel
 */
$filename="public/report/open_invoice_by_rep_" . $user['User']['id'] . ".csv";
$fp=fopen($filename,"wb");
$excelContent = '';

?>
<script type="text/javascript" src="<?php echo $this->webroot.'js/jquery.formatCurrency-1.4.0.min.js'; ?>"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(".btnPrintOpenInvoiceByRep").click(function(event){
            event.preventDefault();
            if($(this).attr("trans_type")=="Invoice"){
                var url = "<?php echo $this->base . '/sales_orders'; ?>/printInvoice/"+$(this).attr("rel");
            }else{
                var url = "<?php echo $this->base . '/credit_memos'; ?>/printInvoice/"+$(this).attr("rel");
            }
            $.ajax({
                type: "POST",
                url: url,
                beforeSend: function(){
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(printResult){
                    w=window.open();
                    w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                    w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                    w.document.write(printResult);
                    w.document.close();
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                }
            });
        });
        
        $("#<?php echo $btnPrint; ?>").click(function(){
            w=window.open();
            w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
            w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
            w.document.write($("#<?php echo $printArea; ?>").html());
            w.document.close();
            w.print();
            w.close();
        });
        
        $("#<?php echo $btnExport; ?>").click(function(){
            window.open("<?php echo $this->webroot; ?>public/report/open_invoice_by_rep_<?php echo $user['User']['id']; ?>.csv", "_blank");
        });
    });
</script>
<div id="<?php echo $printArea; ?>">
    <?php
    $msg = '<b style="font-size: 18px;">' . MENU_REPORT_OPEN_INVOICE_BY_REP . '</b><br /><br />';
    $excelContent .= MENU_REPORT_OPEN_INVOICE_BY_REP."\n\n";
    if($_POST['date']!='') {
        $msg .= TABLE_DATE.': '.$_POST['date'];
        $excelContent .= TABLE_DATE.': '.$_POST['date']."\n\n";
    }
    echo $this->element('/print/header-report',array('msg'=>$msg));
    $excelContent .= TABLE_NO."\t".TABLE_DATE."\t".TABLE_INVOICE_CODE."\t".TABLE_MEMO."\t".TABLE_CUSTOMER."\t".TABLE_TOTAL_AMOUNT."\t".GENERAL_BALANCE."\t".GENERAL_AGING."\t".TABLE_STATUS;
    ?>
    <table id="<?php echo $tblName; ?>" class="table_report">
        <tr>
            <th class="first"><?php echo TABLE_NO; ?></th>
            <th style="width: 120px !important;"><?php echo TABLE_DATE; ?></th>
            <th style="width: 120px !important;"><?php echo TABLE_INVOICE_CODE; ?></th>
            <th style="width: 120px !important;"><?php echo TABLE_MEMO; ?></th>
            <th><?php echo TABLE_CUSTOMER; ?></th>
            <th style="width: 140px !important;"><?php echo TABLE_TOTAL_AMOUNT; ?> ($)</th>
            <th style="width: 140px !important;"><?php echo GENERAL_BALANCE; ?> ($)</th>
            <th style="width: 80px !important;"><?php echo GENERAL_AGING; ?></th>
            <th style="width: 80px !important;"><?php echo TABLE_STATUS; ?></th>
        </tr>
        <?php
        $date = dateConvert(str_replace("|||", "/", $data[0]));

        // general condition
        $col = implode(',', $_POST);
        $col = explode(",", $col);
        $condition = '';
        if ($data[0] != '') {
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= '"' . $date . '" >= DATE(order_date)';
        }
        $condition != '' ? $condition .= ' AND ' : '';
        if ($data[1] == '') {
            $condition .= 'status > 0';
        } else {
            $condition .= 'status=' . $data[1];
        }
        if ($data[2] != '') {
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= 'company_id=' . $data[2];
        } else {
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= 'company_id IN (SELECT company_id FROM user_companies WHERE user_id = ' . $user['User']['id'].')';
        }
        if ($data[3] != '') {
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= 'branch_id = ' . $data[3];
        } else {
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= 'branch_id IN (SELECT branch_id FROM user_branches WHERE user_id = ' . $user['User']['id'].')';
        }
        if ($data[4] != '') {
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= 'location_group_id=' . $data[4];
        }
        if ($data[5] != '') {
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= 'customer_id IN (SELECT customer_id FROM customer_cgroups WHERE cgroup_id=' . $data[5] . ')';
        }
        if ($data[6] != '') {
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= 'customer_id=' . $data[6];
        }
        if ($data[7] != '') {
            $condition != '' ? $condition .= ' AND ' : '';
            $condition .= 'created_by=' . $data[7];
        }

        // declare
        $grandTotalAmount=0;
        $grandTotalBalance=0;
        ?>

        <?php $excelContent .= "\n".'Product'; ?>
        <tr style="font-weight: bold;"><td class="first" colspan="9" style="font-size: 14px;">Product</td></tr>
        <?php
        $index=1;
        $arrCode=array();
        $arrProduct=array();
        $arrCustomer=array();
        $arrLocation=array();
        $oldCustomerGroupId='';
        $oldCustomerGroupName='';
        $subTotalAmount=0;
        $totalAmount=0;
        $subTotalBalance=0;
        $totalBalance=0;
        $query=mysql_query("SELECT
                                'Invoice' AS trans_type,
                                (SELECT id FROM cgroups WHERE id=(SELECT cgroup_id FROM customer_cgroups WHERE customer_id=sales_orders.customer_id AND cgroup_id IN (SELECT id FROM cgroups WHERE is_active=1) LIMIT 1)) AS cgroup_id,
                                (SELECT name FROM cgroups WHERE id=(SELECT cgroup_id FROM customer_cgroups WHERE customer_id=sales_orders.customer_id AND cgroup_id IN (SELECT id FROM cgroups WHERE is_active=1) LIMIT 1)) AS cgroup_name,
                                sales_orders.id AS id,
                                sales_orders.order_date AS order_date,
                                IF(is_pos=1,(SELECT receipt_code FROM sales_order_receipts WHERE sales_order_id=sales_orders.id),sales_orders.so_code) AS invoice_code,
                                sales_orders.memo,
                                CONCAT_WS(' - ', customers.customer_code, customers.name) AS customer_name,
                                (SELECT name FROM locations WHERE id=location_id) AS location_name,
                                sales_orders.total_amount-IFNULL(sales_orders.discount,0)+IFNULL(sales_orders.total_vat,0) AS total_amount,
                                sales_orders.balance + IFNULL((SELECT SUM(IFNULL(amount_us,0)+IFNULL(amount_other/(SELECT rate_to_sell FROM exchange_rates WHERE id=sales_order_receipts.exchange_rate_id),0)) FROM sales_order_receipts WHERE DATE_SUB(DATE(IF(pay_date IS NULL,(SELECT date FROM general_ledgers WHERE sales_order_receipt_id=sales_order_receipts.id),pay_date)), INTERVAL 1 DAY) >= '" . $date . "' AND sales_order_id=sales_orders.id AND is_void=0),0) + IFNULL((SELECT SUM(total_price) FROM credit_memo_with_sales WHERE status=1 AND sales_order_id=sales_orders.id AND DATE_SUB(DATE(IF(apply_date IS NULL,(SELECT order_date FROM credit_memos WHERE id=credit_memo_with_sales.credit_memo_id),apply_date)), INTERVAL 1 DAY) >= '" . $date . "'),0) AS balance,
                                IF(balance>0 AND DATEDIFF(now(),due_date)>0,DATEDIFF(now(),due_date),'.') AS aging,
                                CASE sales_orders.status WHEN 0 THEN 'Void' WHEN 1 THEN 'Issued' WHEN 2 THEN 'Fulfilled' END AS status
                            FROM sales_orders
                                LEFT JOIN customers ON customers.id = sales_orders.customer_id
                            WHERE "
                                . str_replace('created_by','sales_orders.created_by',$condition)
                                . " AND sales_orders.balance + IFNULL((SELECT SUM(IFNULL(amount_us,0)+IFNULL(amount_other/(SELECT rate_to_sell FROM exchange_rates WHERE id=sales_order_receipts.exchange_rate_id),0)) FROM sales_order_receipts WHERE DATE_SUB(DATE(IF(pay_date IS NULL,(SELECT date FROM general_ledgers WHERE sales_order_receipt_id=sales_order_receipts.id),pay_date)), INTERVAL 1 DAY) >= '" . $date . "' AND sales_order_id=sales_orders.id AND is_void=0),0) + IFNULL((SELECT SUM(total_price) FROM credit_memo_with_sales WHERE status=1 AND sales_order_id=sales_orders.id AND DATE_SUB(DATE(IF(apply_date IS NULL,(SELECT order_date FROM credit_memos WHERE id=credit_memo_with_sales.credit_memo_id),apply_date)), INTERVAL 1 DAY) >= '" . $date . "'),0) > 0.001"
                                . " AND is_pos IN (0,2)"
                                . "
                            UNION
                            SELECT
                                'Credit Memo' AS trans_type,
                                (SELECT id FROM cgroups WHERE id=(SELECT cgroup_id FROM customer_cgroups WHERE customer_id=credit_memos.customer_id AND cgroup_id IN (SELECT id FROM cgroups WHERE is_active=1) LIMIT 1)) AS cgroup_id,
                                (SELECT name FROM cgroups WHERE id=(SELECT cgroup_id FROM customer_cgroups WHERE customer_id=credit_memos.customer_id AND cgroup_id IN (SELECT id FROM cgroups WHERE is_active=1) LIMIT 1)) AS cgroup_name,
                                credit_memos.id AS id,
                                credit_memos.order_date AS order_date,
                                credit_memos.cm_code AS invoice_code,
                                credit_memos.note AS memo,
                                CONCAT_WS(' - ', customers.customer_code, customers.name) AS customer_name,
                                (SELECT name FROM locations WHERE id=location_id) AS location_name,
                                (credit_memos.total_amount-IFNULL(credit_memos.discount,0)+IFNULL(credit_memos.mark_up,0)+IFNULL(credit_memos.total_vat,0))*-1 AS total_amount,
                                (credit_memos.balance + IFNULL((SELECT SUM(IFNULL(amount_us,0)+IFNULL(amount_other/(SELECT rate_to_sell FROM exchange_rates WHERE id=credit_memo_receipts.exchange_rate_id),0)) FROM credit_memo_receipts WHERE DATE_SUB(DATE(IF(pay_date IS NULL,(SELECT date FROM general_ledgers WHERE credit_memo_receipt_id=credit_memo_receipts.id),pay_date)), INTERVAL 1 DAY) >= '" . $date . "' AND credit_memo_id=credit_memos.id AND is_void=0),0) + IFNULL((SELECT SUM(total_price) FROM credit_memo_with_sales WHERE status=1 AND credit_memo_id=credit_memos.id AND DATE_SUB(DATE(IF(apply_date IS NULL,(SELECT order_date FROM sales_orders WHERE id=credit_memo_with_sales.sales_order_id),apply_date)), INTERVAL 1 DAY) >= '" . $date . "'),0))*-1 AS balance,
                                IF(balance>0 AND DATEDIFF(now(),due_date)>0,DATEDIFF(now(),due_date),'.') AS aging,
                                CASE credit_memos.status WHEN 0 THEN 'Void' WHEN 1 THEN 'Issued' WHEN 2 THEN 'Fulfilled' END AS status
                            FROM credit_memos
                                LEFT JOIN customers ON customers.id = credit_memos.customer_id
                            WHERE "
                                . str_replace('created_by','credit_memos.created_by',$condition)
                                . " AND credit_memos.balance + IFNULL((SELECT SUM(IFNULL(amount_us,0)+IFNULL(amount_other/(SELECT rate_to_sell FROM exchange_rates WHERE id=credit_memo_receipts.exchange_rate_id),0)) FROM credit_memo_receipts WHERE DATE_SUB(DATE(IF(pay_date IS NULL,(SELECT date FROM general_ledgers WHERE credit_memo_receipt_id=credit_memo_receipts.id),pay_date)), INTERVAL 1 DAY) >= '" . $date . "' AND credit_memo_id=credit_memos.id AND is_void=0),0) + IFNULL((SELECT SUM(total_price) FROM credit_memo_with_sales WHERE status=1 AND credit_memo_id=credit_memos.id AND DATE_SUB(DATE(IF(apply_date IS NULL,(SELECT order_date FROM sales_orders WHERE id=credit_memo_with_sales.sales_order_id),apply_date)), INTERVAL 1 DAY) >= '" . $date . "'),0) > 0.001"
                                . "
                            ORDER BY cgroup_name,order_date") or die(mysql_error());
        while($data=mysql_fetch_array($query)){
            $arrCode[] = $data['invoice_code'];
            $arrCustomer[] = $data['customer_name'];
            $arrLocation[] = $data['location_name'];
        ?>

            <?php if($data['cgroup_id']!=$oldCustomerGroupId){ ?>
            <?php if($oldCustomerGroupName!=''){ ?>
            <?php $excelContent .= "\n".'Total '.$oldCustomerGroupName."\t\t\t\t\t".number_format($subTotalAmount,2)."\t".number_format($subTotalBalance,2); ?>
            <tr style="font-weight: bold;"><td class="first" colspan="5">Total <?php echo $oldCustomerGroupName; ?></td><td style="text-align: right;"><?php echo number_format($subTotalAmount,2); ?></td><td style="text-align: right;"><?php echo number_format($subTotalBalance,2); ?></td><td></td><td></td></tr>
            <?php
                $index=1;
                $subTotalAmount=0;
                $subTotalBalance=0;
            }
            ?>
            <?php $excelContent .= "\n".$data['cgroup_name']; ?>
            <tr><td class="first" colspan="11" style="font-weight: bold;"><?php echo $data['cgroup_name']; ?></td></tr>
            <?php } ?>

            <?php $excelContent .= "\n".$index."\t".dateShort($data['order_date'])."\t".$data['invoice_code']."\t".$data['memo']."\t".$data['customer_name']."\t".$data['total_amount']."\t".$data['balance']."\t".$data['aging']."\t".$data['status']; ?>
            <tr>
                <td class="first"><?php echo $index++; ?></td>
                <td><?php echo dateShort($data['order_date']); ?></td>
                <td><a href="" class="btnPrintOpenInvoiceByRep" rel="<?php echo $data['id']; ?>" trans_type="<?php echo $data['trans_type']; ?>"><?php echo $data['invoice_code']; ?></a></td>
                <td><?php echo $data['memo']; ?></td>
                <td><?php echo $data['customer_name']; ?></td>
                <td style="text-align: right;"><?php echo number_format($data['total_amount'],2); ?></td>
                <td style="text-align: right;"><?php echo number_format($data['balance'],2); ?></td>
                <td style="text-align: center;"><?php echo $data['aging']; ?></td>
                <td style="text-align: center;"><?php echo $data['status']; ?></td>
            </tr>

            <?php
            $subTotalAmount+=$data['total_amount'];
            $totalAmount+=$data['total_amount'];
            $grandTotalAmount+=$data['total_amount'];
            $subTotalBalance+=$data['balance'];
            $totalBalance+=$data['balance'];
            $grandTotalBalance+=$data['balance'];
            $oldCustomerGroupId=$data['cgroup_id'];
            $oldCustomerGroupName=$data['cgroup_name'];
            ?>

        <?php } ?>

        <?php if(mysql_num_rows($query)){ ?>
        <?php $excelContent .= "\n".'Total '.$oldCustomerGroupName."\t\t\t\t\t".number_format($subTotalAmount,2)."\t".number_format($subTotalBalance,2); ?>
        <tr style="font-weight: bold;"><td class="first" colspan="5">Total <?php echo $oldCustomerGroupName; ?></td><td style="text-align: right;"><?php echo number_format($subTotalAmount,2); ?></td><td style="text-align: right;"><?php echo number_format($subTotalBalance,2); ?></td><td></td><td></td></tr>
        <?php } ?>

        <?php $excelContent .= "\n\n".'Grand Total Amount'."\t\t".sizeof(array_unique($arrCode))."\t\t".sizeof(array_unique($arrCustomer))."\t".number_format($grandTotalAmount,2)."\t".number_format($grandTotalBalance,2); ?>
        <tr><td colspan="9">&nbsp;</td></tr>
        <tr style="font-weight: bold;">
            <td class="first" colspan="2" style="font-size: 14px;">Grand Total Amount</td>
            <td style="text-align: center;font-size: 14px;text-decoration: underline;"><?php echo sizeof(array_unique($arrCode)); ?></td>
            <td></td>
            <td style="text-align: center;font-size: 14px;text-decoration: underline;"><?php echo sizeof(array_unique($arrCustomer)); ?></td>
            <td style="text-align: right;font-size: 14px;text-decoration: underline;"><?php echo number_format($grandTotalAmount,2); ?></td>
            <td style="text-align: right;font-size: 14px;text-decoration: underline;"><?php echo number_format($grandTotalBalance,2); ?></td>
            <td></td>
            <td></td>
        </tr>
    </table>
</div>
<br />
<div class="buttons">
    <button type="button" id="<?php echo $btnPrint; ?>" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/>
        <?php echo ACTION_PRINT; ?>
    </button>
    <button type="button" id="<?php echo $btnExport; ?>" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/approved.png" alt=""/>
        <?php echo ACTION_EXPORT_TO_EXCEL; ?>
    </button>
</div>
<div style="clear: both;"></div>
<?php

$excelContent = chr(255).chr(254).@mb_convert_encoding($excelContent, 'UTF-16LE', 'UTF-8');
fwrite($fp,$excelContent);
fclose($fp);

?>