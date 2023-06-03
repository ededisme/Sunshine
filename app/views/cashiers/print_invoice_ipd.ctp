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
    <?php
        $msg = "RECEIPT";
        $telTitle     = 'Tel: ';
        $companyTitle = $patient['Branch']['name'];
        $companyTitleKH = $patient['Branch']['name_other'];
        echo $this->element('/print/header-invoice-make-payment', array('msg' => $msg, 'barcode' => $patient['Invoice']['invoice_code'], 'address' => $patient['Branch']['address'], 'telephone' => $telTitle.$patient['Branch']['telephone'], 'logo' => $patient['Company']['photo'], 'title' => $companyTitle, 'titleKH' => $companyTitleKH, 'mail' => $patient['Branch']['email_address']));
    ?>
    <br>
    <table style="width: 100%;">    
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
        <tr>
            <td>
                <?php 
                echo TABLE_DAIGNOSTIC;                   
                ?>
            </td>
            <td colspan="3">
                :
                <?php                 
                $queryPatientConsultation = mysql_query("SELECT patient_consultations.daignostic FROM patient_consultations INNER JOIN queued_doctors ON patient_consultations.queued_doctor_id = queued_doctors.id "
                                                    . " WHERE patient_consultations.is_active = 1 AND queued_doctors.queue_id = {$patient['Invoice']['queue_id']} LIMIT 1");                
                while ($resultPatientConsultation = mysql_fetch_array($queryPatientConsultation)) {
                    echo $resultPatientConsultation['daignostic']; 
                }
                ?>
            </td>
        </tr> 
    </table>
    <p><b><?php echo TITLE_CLIENT_INSURANCE_PROVIDER;?> : <?php if($patient['Patient']['company_insurance_id']!="") {$queryData = mysql_query("SELECT name FROM company_insurances WHERE id=".$patient['Patient']['company_insurance_id']); $resData = mysql_fetch_array($queryData); echo $resData['name'];}else { echo 'GENERAL';}?></b></p>
    <table class="table" cellspacing="0">
        <tr>        
            <th class="first"><?php echo SERVICE_SERVICE; ?></th>
            <th><?php echo GENEARL_DATE; ?></th>
            <th><?php echo GENERAL_QTY_PRINT; ?></th>
            <th><?php echo GENERAL_UNIT_PRICE_PRINT; ?></th>        
            <th><?php echo GENERAL_TOTAL_PRICE; ?></th>
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
            <td class="first" style="width: 30%;">
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
            <td>
                <?php                
                if ($data['date_created'] != "" && $data['date_created'] != "0000-00-00") {
                    echo date("d/m/Y", strtotime($data['date_created']));
                }else if($data['created'] != "" && $data['created'] != "0000-00-00 00:00:00") {
                    echo date("d/m/Y", strtotime($data['created']));
                }
                ?>
            </td>
            <td style="text-align: center;"><?php echo $data['qty']; ?></td>
            <td style="text-align: right;"><?php echo number_format($data['unit_price'], 2); ?></td>        
            <td style="text-align: right;"><?php echo number_format($data['total_price'], 2); ?></td>
        </tr>
        <?php } ?>        
        <tr>
            <td style="text-align: right;border-bottom: 0px;" colspan="4"><b>Gross Bill Amount</b></td>
            <td style="text-align: right;font-weight: bold;"><?php echo number_format($total,2); ?></td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: right;border-bottom: 0px;"><b>Total Discount</b></td>
            <td style="text-align: right;font-weight: bold;"><?php 
                $amountPaid = 0;
                $disc = 0;
                $disRec = 0;
                $queryAmountPaid=mysql_query("SELECT sum(total_amount_paid) AS amountPaid,SUM(total_dis) AS TotalDis FROM receipts WHERE is_void=0 AND invoice_id=".$this->params['pass'][0]);
                while($dataAmountPaid=mysql_fetch_array($queryAmountPaid)){
                    $amountPaid = $dataAmountPaid['amountPaid'];
                    $disRec = $dataAmountPaid['TotalDis'];
                }
                if($patient['Invoice']['total_discount']!='' && $disRec==''){
                    $disc = $patient['Invoice']['total_discount'];
                }else{
                    $disc = $disRec;
                }
                echo number_format($disc, 2); ?>
            </td>
        </tr>  
        <tr>
            <td colspan="4" style="text-align: right;border-bottom: 0px;"><b>Net Bill Amount</b></td>
            <td style="text-align: right;font-weight: bold;">
                <?php
                $netBill = 0;
                $netBill = $total - $disc;
                echo number_format($netBill, 2); 
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: right;border-bottom: 0px;"><b>Amount Received From Patient</b></td>
            <td style="text-align: right;font-weight: bold;">
                <?php
                echo number_format($amountPaid, 2); ?>
                <input type="hidden" id="totalAmountRecieved" value="<?php echo $amountPaid;?>"/>
            </td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: right;border-bottom: 0px;"><b>Due</b></td>
            <td style="text-align: right;font-weight: bold;"><?php echo number_format($patient['Invoice']['balance'], 2); ?></td>
        </tr>
    </table>
    <div class="clear"></div>
    <br/><br/>
    <span style="float: left;font-size: 14px;">
        <p style="text-align: left;"><?php echo TITLE_AMOUNT_RECEIVED_IN_WORDS; ?> - 
            <span id="towordTotalPrice" style="text-transform: capitalize;">
                <?php 
                    $amountPaidString = number_format($amountPaid, 2);
                    echo convertNumberToWords($amountPaidString); 
                ?>
            </span>
        </p>
    </span>
    <div class="clear"></div>
    <table>
        <tr>
            <td style="border-bottom: 0px;border-right: 0px;">
                <?php echo GENERAL_PAID_BY; ?>: <b><?php echo $patient['Patient']['patient_name']; ?></b>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?php echo TABLE_PREPARE_BY; ?>: <b><?php echo getInvoiceCreator($data_invoice['created_by']); ?></b>
            </td>
        </tr>
    </table>
</div>