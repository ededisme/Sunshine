<script type="text/javascript" src="<?php echo $this->webroot; ?>js/jquery-1.4.4.min.js"></script>
<?php
include('includes/function.php');
$query_invoice=mysql_query("SELECT * FROM invoices WHERE id=".$this->params['pass'][0]);
$data_invoice=mysql_fetch_array($query_invoice);
?>
<style type="text/css">
    .info th {
        padding: 2px;
        font-size: 11px;        
    }
    .info td {
        padding: 2px;
        font-size: 11px;
    }
    .table_solid th {
        font-size: 12px;        
    }
    .table td {
        font-size: 12px;
    }         
</style>
<style type="text/css" media="print">
    div#print-content { width:100%; }       
    table tr td{ font-size: 13px; }
    @page
    {
        /*this affects the margin in the printer settings*/  
        margin: 5mm 5mm 5mm 5mm;
    }
    table.table .first{
        border-color: #000000;
        border-left: 1px solid #000000;
        color: #000000;
    }
    table.table {
        border-color: #000000;
        border-collapse: separate;
        border :  0px solid #000000;
        border-spacing: 0;
        width: 100%;
        color: #000000;
    }
    table.table th {
        border-color: #000000;
        border-right: 1px solid #000000;
        color: #000000;
    }
    table.table tr {        
        border-color:  #000000 ;
        border-style: none ;
        border-width: 0 ;
        color: #000000;
    }
    table.table td {
        border-color: #000000;
        border-right: 1px solid #000000;
        color: #000000;
    }
</style>
<div id="print-content" class="print_doc">
    <table style="width: 100%;">
        <tr>
            <td style="text-align: center;width:15%;">                
                <img alt="" style="width: 120px;" src="<?php echo $this->webroot; ?>img/logo_s.png" />
                <p style="text-align: center;display: none;">
                    <span style="font-size:14px;font-weight: bold;">
                        <?php echo GENERAL_COMPANY_ADDRESS; ?>                
                        <?php echo GENERAL_COMPANY_TEL; ?>
                        <br/>
                       <?php echo GENERAL_COMPANY_EMAIL; ?>
                    </span>
                </p>                
            </td>
            <td>
                <h2 style="font-size: 18px;line-height: 25px"><?php echo GENERAL_COMPANY_NAME_KH; ?></h2>
                <h2 style="font-size: 14px;line-height: 0px;"><?php echo GENERAL_COMPANY_NAME_EN; ?></h2>
            </td>
        </tr>
    </table>  
    <table style="width: 100%;" cellspacing="0">    
        <tr style="display:none;">
            <td style="width:15%;"><?php echo PATIENT_CODE?>: </td>
            <td style="width:35%;"><?php echo $patient['Patient']['patient_code']; ?></td>
            <td style="width:15%;"><?php echo GENERAL_INVOICE_DATE;?>: </td>            
            <td style="width:35%;"><?php echo date("d/m/Y H:i:s", strtotime($patient['Invoice']['created'])); ?></td>
        </tr>  
        <tr>
            <td><?php echo 'អតិថិជន/Customer'; ?>: </td>
            <td><?php echo $patient['Patient']['patient_name']; ?></td>
            <td><?php echo 'លេខរៀងវិក្កយបត្រ'; ?>: </td>
            <td><?php echo $patient['Invoice']['invoice_code']; ?></td>
        </tr>
        <tr>
            <td><?php echo 'ឈ្មោះក្រុមហ៊ុនឬអតិថិជន' ?>: </td>
            <td><?php if($patient['Patient']['company_insurance_id']!="") {$queryData = mysql_query("SELECT name FROM company_insurances WHERE id=".$patient['Patient']['company_insurance_id']); $resData = mysql_fetch_array($queryData); echo $resData['name'];}else { echo 'GENERAL';}?></td>
            <td><?php echo 'Invoice N&ordm;'; ?></td>
            <td></td>
        </tr>
        <tr>
            <td><?php echo 'Company Name/Customer' ?></td>
            <td>&nbsp;</td>
            <td><?php echo 'កាលបរិច្ឆេទ'; ?>: </td>
            <td><?php echo date("d/m/Y H:i:s", strtotime($patient['Invoice']['created'])); ?></td>
        </tr>
        <tr>
            <td><?php echo 'អាស័យដ្ឋានក្រុមហ៊ុន' ?>: </td>
            <td rowspan="2"><?php echo nl2br(GENERAL_COMPANY_ADDRESS); ?></td>
            <td><?php echo 'Date'; ?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><?php echo 'Company Address' ?></td>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td><?php echo 'ទូរស័ព្ទលេខ' ?>: </td>
            <td colspan="3">023 214955</td>
        </tr>
        <tr>
            <td><?php echo 'Tel N&ordm;' ?></td>
            <td colspan="3">&nbsp;</td>
        </tr>  
    </table>
    <table class="table" cellspacing="0">
        <tr>
            <th style="text-align:center;" class="first">លរ<br>N&ordm;</th>        
            <th style="text-align:center;">បរិយាយមុខទំនិញ<br>Description</th>
            <th style="text-align:center;"> គ្រូពេទ្យ<br>Doctor</th>
            <th style="text-align:center;">បរិមាណ<br>Quantity</th>
            <th style="text-align:center;">ថ្លៃឯកតា<br>Unit Price</th>
            <th style="text-align:center;">ថ្លៃសរុប<br>Amount</th>
        </tr>    
        <?php
        $index=1;
        $total=0;    
        $query=mysql_query("SELECT * FROM invoice_details WHERE is_active=1 AND invoice_id=".$this->params['pass'][0]);
        while($data=mysql_fetch_array($query)){?>
        <tr>
            <?php
            $total+=$data['total_price'];
            ?>
            <td class="first"><?php echo $index++; ?></td>        
            <td style="width:30%;">
                <?php
                    if($data['type']==1){
                        $query_service=mysql_query("SELECT sec.name, ser.name FROM sections sec INNER JOIN services ser ON ser.section_id=sec.id WHERE ser.id=".$data['service_id']);
                        $data_service=mysql_fetch_array($query_service);
                        echo $data_service[1]; 
                    }else if($data['type']==2){
                        $queryLaboName = mysql_query("SELECT name FROM labo_item_groups WHERE id = '".$data['service_id']."' ");
                        $dataLaboName=mysql_fetch_array($queryLaboName);
                        echo $dataLaboName['name']; 
                    }else if($data['type']==3){
                        echo 'Medicine';                        
                    }
                ?>
            </td>
            <?php
            if($data['doctor_id']!=""){
                $query_doctor=mysql_query("SELECT Employee.name FROM employees Employee INNER JOIN user_employees UserEmployee ON Employee.id=UserEmployee.employee_id WHERE UserEmployee.user_id=".$data['doctor_id']);
                $data_doctor=mysql_fetch_array($query_doctor);
            }else{
                $data_doctor[0] = "";
            }
            ?>
            <td style="white-space: nowrap;"><?php echo $data_doctor[0];?></td>
            <td style="text-align: center;"><?php echo $data['qty']; ?></td>
            <td style="text-align: right;"><?php echo number_format($data['unit_price'], 2); ?></td>
            <td style="text-align: right;"><?php echo number_format($data['total_price'], 2); ?></td>
        </tr>
        <?php } ?>
        
        <?php
        $query=mysql_query("SELECT sales_orders.*, orders.created_by FROM sales_orders LEFT JOIN orders ON orders.id = sales_orders.order_id WHERE sales_orders.status>=1 AND sales_orders.queue_id =".$patient['Invoice']['queue_id']);
        while($data=mysql_fetch_array($query)){?>
        <tr>
            <?php
            $total+=($data['total_amount']-$data['discount']);
            ?>
            <td class="first"><?php echo $index++; ?></td>        
            <td style="width:30%;">Medicine</td>
            <?php
            if($data['created_by']!=""){
               $query_doctor=mysql_query("SELECT Employee.name FROM employees Employee INNER JOIN user_employees UserEmployee ON Employee.id=UserEmployee.employee_id WHERE UserEmployee.user_id=".$data['created_by']);
               $data_doctor=mysql_fetch_array($query_doctor);
            }else{
                $data_doctor[0] = "";
            }
            ?>
            <td><?php echo $data_doctor[0];?></td>
            <td style="text-align: center;">1</td>
            <td style="text-align: right;"><?php echo number_format($data['total_amount'], 2); ?></td>
            <td style="text-align: right;"><?php echo number_format($data['total_amount'], 2); ?></td>
        </tr>
        <?php } ?>

        <tr>
            <td colspan="4" style="border-bottom:none;"></td>
            <td style="text-align: right;">ថ្លៃសរុប<br>Total&nbsp;Price</td>
            <td style="text-align: right;"><?php echo number_format($total,2); ?></td>
        </tr>
        <tr style="display:none;">
            <td colspan="6" style="text-align: right;border-bottom: 0px;"><b>Total Discount</b></td>
            <td style="text-align: right;"><?php echo number_format($patient['Invoice']['total_discount'], 2); ?></td>
        </tr>  
        <tr style="display:none;">
            <td colspan="6" style="text-align: right;border-bottom: 0px;"><b>Net Bill Amount</b></td>
            <td style="text-align: right;">
                <?php
                $netBill = 0;
                $netBill = $total - $patient['Invoice']['total_discount'];
                echo number_format($netBill, 2); 
                ?>
            </td>
        </tr>
        <tr style="display:none;">
            <td colspan="6" style="text-align: right;border-bottom: 0px;"><b>Amount Received From Patient</b></td>
            <td style="text-align: right;">
                <?php
                $amountPaid = 0;
                $queryAmountPaid=mysql_query("SELECT sum(total_amount_paid) AS amountPaid FROM receipts WHERE is_void=0 AND invoice_id=".$this->params['pass'][0]);
                while($dataAmountPaid=mysql_fetch_array($queryAmountPaid)){
                    $amountPaid = $dataAmountPaid['amountPaid'];
                }
                echo number_format($amountPaid, 2); ?>
                <input type="hidden" id="totalAmountRecieved" value="<?php echo $amountPaid;?>"/>
            </td>
        </tr>
        <tr style="display:none;">
            <td colspan="6" style="text-align: right;border-bottom: 0px;"><b>Due</b></td>
            <td style="text-align: right;">
                <?php
                if($patient['Invoice']['balance']>0){
                    echo number_format($patient['Invoice']['balance'], 2);   
                }else{
                    echo number_format(0, 2);
                }
                ?>
            </td>
        </tr>
    </table>
    <div class="clear"></div>
    <br/><br/>
    <span style="float: left;font-size: 14px;display: none">
        <p style="text-align: left;"><?php echo TITLE_AMOUNT_RECEIVED_IN_WORDS; ?> - <span id="towordTotalPrice" style="text-transform: capitalize;"><?php echo convertNumberToWords($amountPaid); ?></span></p>
    </span>
    <div class="clear"></div>
    <table>
        <tr style="display:none;">
            <td style="border-bottom: 0px;border-right: 0px;">
                <?php echo GENERAL_PAID_BY; ?>: <b><?php echo $patient['Patient']['patient_name']; ?></b>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?php echo GENERAL_RECEIVED_BY; ?>: <b><?php echo getInvoiceCreator($data_invoice['created_by']); ?></b>
            </td>
        </tr>
        <tfoot style="width: 100%;">
            <tr>
                <td style="width: 100%;">
                    <table style="position:fixed;bottom: 0;right: 0;left: 0;width: 100%;">
                        <tr>
                            <td style="width:20%;"></td>
                            <td style="width:30%;padding-right:30px;">
                                <div style=" margin: 0px auto; width: 100%; border-top: 1px solid #000; text-align: center; font-size: 12px; font-weight: bold; font-family: 'Calibri'">
                                    <span style='font-size: 12px; font-weight: bold;'>ហត្ថលេខា និងឈ្មោះអតិថិជន </span><br />
                                    Customer's Signature & Name
                                </div>
                            </td>
                            <td style="width:30%;padding-left:30px">
                                <div style=" margin: 0px auto; width: 100%; border-top: 1px solid #000; text-align: center; font-size: 12px; font-weight: bold; font-family: 'Calibri'">
                                    <span style='font-size: 12px; font-weight: bold;'>ហត្ថលេខា និងឈ្មោះអ្នកគិតប្រាក់</span> <br />
                                    Cashier's Signature & Name
                                </div>
                            </td>
                            <td style="width:20%;">

                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="5" style="text-align:left;font-size: 12px;">
                                 <div style="">
                                    <span style="font-family: 'Moul'; font-size: 12px;">សម្គាល់៖</span>ច្បាប់ដើមសម្រាប់អតិថិជន ច្បាប់ចម្លងសម្រាប់ក្រុមហ៊ុន
                                    <br />
                                    <span style="font-weight: bold; font-size: 12px;">Note: </span>Original invoice for customer, copied invoice for company
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" style="width:100%;padding-bottom: 0;"><hr></td>
                        </tr>
                        <tr>
                            <td colspan="5" style="width:100%;font-size: 11px;">
                                <span>ADDRESS</span>: <?php echo GENERAL_COMPANY_ADDRESS; ?>   
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" style="width:100%;font-size: 11px;">
                                <span><?php echo GENERAL_COMPANY_TEL; ?></span>
                                <span style="padding-left: 10px;"><?php echo GENERAL_COMPANY_EMAIL; ?></span> 
                                <span style="padding-left: 10px;"><?php echo GENERAL_COMPANY_WEBSITE; ?></span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tfoot>
    </table>
</div>