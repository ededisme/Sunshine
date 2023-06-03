<script type="text/javascript" src="<?php echo $this->webroot; ?>js/jquery-1.4.4.min.js"></script>
<?php
include('includes/function.php');
$query_invoice = mysql_query("SELECT * FROM invoices WHERE id=".$this->params['pass'][0]);
$data_invoice = mysql_fetch_array($query_invoice);
?>
<style type="text/css">
    div.print-footer {display: none;} 
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
    div.print-footer {display: block; width: 100%; position: fixed; bottom: 2px; font-size: 11px; text-align: center;}
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
    <?php
        $msg = "INVOICE";
        $telTitle     = 'Tel: ';
        $companyTitle = $patient['Branch']['name'];
        $companyTitleKH = $patient['Branch']['name_other'];
        echo $this->element('/print/header-invoice-make-payment', array('msg' => $msg, 'barcode' => $patient['Invoice']['invoice_code'], 'address' => $patient['Branch']['address'], 'telephone' => $telTitle.$patient['Branch']['telephone'], 'logo' => $patient['Company']['photo'], 'title' => $companyTitle, 'titleKH' => $companyTitleKH, 'mail' => $patient['Branch']['email_address']));
    ?>
    <table style="width: 100%;" class="tbl-class">
        <tr>
            <td style="width: 15%;">
                <?php echo PATIENT_CODE?>
            </td>
            <td style="width: 35%;">
                : <?php echo $patient['Patient']['patient_code']; ?>
            </td>
            <td style="width: 15%;">
                <?php echo GENERAL_INVOICE_DATE;?>
            </td>            
            <td style="width: 35%;">
                : <?php echo date("d/m/Y H:i:s", strtotime($patient['Invoice']['created'])); ?>
            </td>
        </tr>  
        <tr>
            <td>
                <?php echo PATIENT_NAME?>
            </td>
            <td>: <?php echo $patient['Patient']['patient_name']; ?></td>
            <td>
                <?php echo TABLE_INVOICE_CODE?>
            </td>
            <td>
                : <?php echo $patient['Invoice']['invoice_code']; ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php 
                echo TABLE_AGE;                
                ?>
            </td>          
            <td> 
                : 
                <?php
                if($patient['Patient']['dob']!="0000-00-00" || $patient['Patient']['dob']!=""){
                    echo getAgePatient($patient['Patient']['dob']);
                }                
                ?>             
            </td>
            <td>
                <?php echo TABLE_SEX;?>
            </td>
            <td>
                : 
                <?php 
                    if ($patient['Patient']['sex'] == "F") {
                        echo GENERAL_FEMALE;
                    } else {
                        echo GENERAL_MALE;
                    }
                ?>
            </td>
        </tr> 
    </table>
    <p><b style="font-size: 11px;"><?php echo TITLE_CLIENT_INSURANCE_PROVIDER;?> : <?php if($patient['Patient']['company_insurance_id']!="") { $queryData = mysql_query("SELECT name FROM company_insurances WHERE id=".$patient['Patient']['company_insurance_id']); $resData = mysql_fetch_array($queryData); echo $resData['name'];}else { echo 'GENERAL';}?></b></p>
    <table class="table" cellspacing="0" style="padding-bottom: 100px">
        <tr>
            <th class="first"><?php echo TABLE_NO; ?></th>        
            <th><?php echo SERVICE_SERVICE; ?></th>
            <th><?php echo DOCTOR_NAME; ?></th>
            <th><?php echo GENERAL_QTY_PRINT; ?></th>
            <th><?php echo GENERAL_UNIT_PRICE_PRINT; ?></th>
            <th><?php echo GENERAL_DISCOUNT_PRINT; ?> ($)</th>
            <th><?php echo GENERAL_TOTAL_PRICE; ?></th>
        </tr>    
        <?php
        $index=1;
        $total=0;
        $totalLabo = 0;
        $queryLabo = mysql_query("SELECT total_price,doctor_id FROM invoice_details WHERE is_active=1 AND type = 2 AND invoice_id=" . $this->params['pass'][0]);
        while ($dataLabo = mysql_fetch_array($queryLabo)) {
            $totalLabo += $dataLabo['total_price'];
        }
        $query=mysql_query("SELECT * FROM invoice_details WHERE is_active=1 AND type != 2 AND invoice_id=".$this->params['pass'][0]);
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
            $doctorName = "";
            if($data['doctor_id']!=""){
               $doctorName = getDoctor($data['doctor_id']);               
            }
            ?>
            <td style="white-space: nowrap;"><?php echo $doctorName;?></td>
            <td style="text-align: center;"><?php echo $data['qty']; ?></td>
            <td style="text-align: right;"><?php echo number_format($data['unit_price'], 2); ?></td>
            <td style="text-align: right;"><?php echo number_format($data['discount'], 2); ?></td>
            <td style="text-align: right;"><?php echo number_format($data['total_price'], 2); ?></td>
        </tr>
        <?php } ?>
        <?php
        if ($totalLabo > 0) {
            $total+=$totalLabo;
            ?>
            <tr>
                <td class="first" style="text-align: center;"><?php echo $index++; ?></td>        
                <td style="width:30%;">Laboratory</td>
                <td style="text-align: center;"></td>
                <td style="text-align: center;">1</td>
                <td style="text-align: right;"><?php echo number_format($totalLabo, 2); ?></td>
                <td style="text-align: right;"><?php echo number_format(0, 2); ?></td>
                <td style="text-align: right;"><?php echo number_format($totalLabo, 2); ?></td>
            </tr>
            <?php
        }
        ?>
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
            $doctorName = "";
            if($data['created_by']!=""){
               $doctorName = getDoctor($data['created_by']);               
            }
            ?>
            <td style="white-space: nowrap;"><?php echo $doctorName;?></td>
            <td style="text-align: center;">1</td>
            <td style="text-align: right;"><?php echo number_format($data['total_amount'], 2); ?></td>
            <td style="text-align: right;"><?php echo number_format($data['discount'], 2); ?></td>
            <td style="text-align: right;"><?php echo number_format($data['total_amount']-$data['discount'], 2); ?></td>
        </tr>
        <?php } ?>
        <tr>
            <td style="text-align: right;border-bottom: 0px;" colspan="6"><b style="font-size: 10px;">Gross Bill Amount</b></td>
            <td style="text-align: right;font-weight: bold;"><?php echo number_format($total,2); ?></td>
        </tr>
        <tr>
            <td colspan="6" style="text-align: right;border-bottom: 0px;"><b style="font-size: 10px;">Total Discount</b></td>
            <td style="text-align: right;font-weight: bold;"><?php 
                $amountPaid = 0;
                $disc = 0;
                $disRec = 0;
                $queryAmountPaid=mysql_query("SELECT sum(total_amount_paid) AS amountPaid,SUM(total_dis) AS TotalDis FROM receipts WHERE is_void=0 AND invoice_id=".$this->params['pass'][0]);
                while($dataAmountPaid=mysql_fetch_array($queryAmountPaid)){
                    $amountPaid = $dataAmountPaid['amountPaid'];
                    $disRec = $dataAmountPaid['TotalDis'];
                }
                if($patient['Invoice']['total_discount']!='' && ($disRec=='' OR $disRec==0)){
                    $disc = $patient['Invoice']['total_discount'];
                }else{
                    $disc = $disRec;
                }
                echo number_format($disc, 2); ?>
            </td>
        </tr>  
        <tr>
            <td colspan="6" style="text-align: right;border-bottom: 0px;"><b style="font-size: 10px;">Net Bill Amount ($)</b></td>
            <td style="text-align: right;font-weight: bold;">
                <?php
                $netBill = 0;
                $netBill = $total - $disc;
                echo number_format($netBill, 2); 
                ?>
            </td>
        </tr>
        <?php
        $exchangeRiel = 4000;
        $exchangeRates = mysql_query("SELECT rate_to_sell FROM exchange_rates WHERE id =" . $patient['Invoice']['exchange_rate_id']);
        while ($resultExchangeRate = mysql_fetch_array($exchangeRates)) {
            $exchangeRiel = $resultExchangeRate['rate_to_sell'];
        }
        ?>
        <!-- Net Bill in Riel  -->
        <tr>
            <td colspan="6" style="text-align: right;border-bottom: 0px;"><b>Net Bill Amount (៛)</b></td>
            <td style="text-align: right;font-weight: bold;">
                <?php
                $netBillRiel = 0;
                $netBillRiel = $netBill * $exchangeRiel;
                echo $netBillRiel; 
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" style="text-align: right;border-bottom: 0px;"><b style="font-size: 10px;">Amount Received From Patient ($)</b></td>
            <td style="text-align: right;font-weight: bold;">
                <?php
                echo number_format($amountPaid, 2); ?>
                <input type="hidden" id="totalAmountRecieved" value="<?php echo $amountPaid;?>"/>
            </td>
        </tr>
        <!-- Amount Received From Patient in Riel -->
        <tr>
            <td colspan="6" style="text-align: right;border-bottom: 0px;"><b>Amount Received From Patient (៛)</b></td>
            <td style="text-align: right;font-weight: bold;">
                <?php
                    $amountPaidRiel = 0;
                    $amountPaidRiel = $amountPaid * $exchangeRiel;
                    echo $amountPaidRiel; 
                ?>
            </td>
        </tr>
        <?php 
        $change = number_format($amountPaid - $netBill  , 2 ); 
        if($change >0){         
        ?>
        <tr>
            <td colspan="6" style="text-align: right;border-bottom: 0px;"><b style="font-size: 10px;">Change</b></td>
            <td style="text-align: right;font-weight: bold;">
                <?php
                echo $change; ?>
                <input type="hidden" id="totalAmountRecieved" value="<?php echo $change;?>"/>
            </td>
        </tr>
        <?php }  ?>
        <tr>
            <td colspan="6" style="text-align: right;border-bottom: 0px;"><b style="font-size: 10px;">Due</b></td>
            <td style="text-align: right;font-weight: bold;">
                <?php
                if($patient['Invoice']['balance']>0){
                    echo number_format($patient['Invoice']['balance'], 2);   
                }else{
                    echo number_format(0, 2);
                }
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="7" style="border-style: none">
                <span style="float: left; font-size: 13px; width: 100%">
                    <p style="text-align: left; font-size: 13px;"><?php echo TITLE_AMOUNT_RECEIVED_IN_WORDS; ?> - 
                        <span id="towordTotalPrice" style="text-transform: capitalize; font-size: 13px;">
                        <?php 
                            $amountPaidString = number_format($amountPaid, 2);
                            echo convertNumberToWords($amountPaidString); 
                        ?>
                        </span>
                    </p>
                    <br/>
                    <p style="margin-right: 20% ; text-align: right; font-size: 13px;">Cashier</p>
                </span>
            </td> 
        </tr>
    </table>
    <div class="clear"></div>
    <br/><br/>
    
    <div class="clear"></div>
    <div class="print-footer" style="position: fixed; bottom: 0;width: 100%; padding-top: 90px ; margin-top: 90px ">
        <center >
            <table style="width:100%;">
                <tr>
                    <td style="font-size:10px; font-family:'Khmer OS Metal Chrieng'; padding-bottom: 10px;"><?php echo $patient['Branch']['address_other'] ?></td>
                    <td style="font-size:10px; font-family:'Times New Roman'">Tel : <?php echo $patient['Branch']['telephone'] ?></td>
                </tr>

                <tr>
                    <td style="font-size:10px; font-family:'Times New Roman'"><?php echo $patient['Branch']['address'] ?></td>
                    <td style="font-size:10px; font-family:'Times New Roman'">Email : <?php echo $patient['Branch']['email_address'] ?></td>
                </tr>
            </table>
        </center>
    </div>
  
    <div style="float:left;width: 450px;">
        <div>
            <input type="button" value="<?php echo ACTION_PRINT; ?>" id='btnDisappearPrintInvoice' class='noprint' style="font-size:200%;height: 70px;width: 200px;">
        </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo $this->webroot; ?>js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(document).dblclick(function() {
            window.close();
        });
        $("#btnDisappearPrintInvoice").click(function() {
            try
            {
                jsPrintSetup.setOption('scaling', 100);
                jsPrintSetup.clearSilentPrint();
                jsPrintSetup.setOption('printBGImages', 1);
                jsPrintSetup.setOption('printBGColors', 1);
                jsPrintSetup.setSilentPrint(1);

                // Choose printer using one or more of the following functions
                // jsPrintSetup.getPrintersList...
                // jsPrintSetup.setPrinter...
                // we add douplicate \\ for it working, if user use share printer
                jsPrintSetup.setPrinter('Udaya-A5');

                jsPrintSetup.print();
                window.close();
            }
            catch (err)
            {
                //Default printing if jsPrintsetup is not available
                window.print();
                window.close();
            }
        });
    });
</script>