<style type="text/css" media="screen">
    .container {
        position: relative;
        text-align: center;
        color: white;
    }
    .centered {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    .top-left {
        width:100%;
        position: absolute;
        top: 8px;
        left: 16px;
        color : black;
        text-align: left;
    }
    .titleHeader{
        vertical-align: top; 
        padding-bottom: 0px !important; 
        padding-top: 0px !important;
        padding-right: 2px !important;
        font-size: 11px;
    }
    .titleContent{
        font-weight: bold;
        text-align: right;
    }
    .titleHeaderTable{
        padding-bottom: 0px !important; 
        padding-top: 0px !important;
        text-transform: uppercase; 
        font-size: 11px;
        background-color: #dedede !important;
        color: #000;
    }
    .titleHeaderHeight{
        height: 20px !important;
    }
    .contentHeight{
        height: 14px !important;
    }
    .horizontal_dotted_line{
        width: 100%;
        display : flex;
    } 
    .dot{
        margin-left: 5px;
        flex: 1;
        border-bottom: 1px dotted #083181;
        height: 0.6em;
    }
    .dot_khmer{
      margin-left: 5px;
      flex: 1;
      border-bottom: 1px dotted #083181;
      height: 1em;
    }
    div.print-footer {display: none;} 
    .btnPrintSticker {cursor: pointer;}
</style>
<style type="text/css" media="print">
    @page
    {
        margin: 2mm 2mm 2mm 2mm;
    }
    .container {
        position: relative;
        text-align: center;
        color: white;
    }
    .centered {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    .top-left {
        width:100%;
        position: absolute;
        top: 8px;
        left: 16px;
        color : black;
        text-align: left;
    }
    #footerTablePrint { width: 100%; position: fixed; bottom: 0px; }
    .titleHeader{
        vertical-align: top; 
        padding-bottom: 0px !important; 
        padding-top: 0px !important;
        padding-right: 2px !important;
        font-size: 11px;
    }
    .titleContent{
        font-weight: bold;
        text-align: right;
    }
    .titleHeaderTable{
        padding-bottom: 0px !important; 
        padding-top: 0px !important;
        text-transform: uppercase; 
        font-size: 11px;
        background-color: #dedede !important;
        color: #000;
    }
    .titleHeaderHeight{
        height: 20px !important;
    }
    .contentHeight{
        height: 14px !important;
    }
    div.print_doc { width:100%;}
    .btnPrintSticker {display: none;}
    #btnDisappearPrint { display: none;}
    div.print-footer {display: block; width: 100%; position: fixed; bottom: 2px; font-size: 11px; text-align: center;}
    .horizontal_dotted_line{
     width: 100%;
     display : flex;
    } 
    .dot{
      margin-left: 5px;
      flex: 1;
      border-bottom: 1px dotted #083181;
      height: 0.6em;
    }
    .dot_khmer{
      margin-left: 5px;
      flex: 1;
      border-bottom: 1px dotted #083181;
      height: 1em;
    }
    .signature{
        page-break-inside: avoid;
    }
</style>

<div class="print_doc">
    <?php 
        include("includes/function.php");
        $display = "";
        if($this->data['Order']['vat_percent'] <= 0){
            $display = "display:none;";
        }
        $msg = '';
        $telTitle     = 'Tel: ';
        $companyTitle = $this->data['Branch']['name'];
        $companyTitleKH = $this->data['Branch']['name_other'];
        if($head !=1){
            echo $this->element('/print/header-prescription', array('msg' => $msg, 'barcode' => $this->data['Order']['order_code'], 'vat' => '', 'address' => $this->data['Branch']['address'], 'telephone' => $this->data['Branch']['telephone'], 'logo' => $this->data['Company']['photo'], 'title' => $companyTitle, 'titleKH' => $companyTitleKH, 'mail' => $this->data['Branch']['email_address'], 'display' => $display));
        }
        $queryPatient = mysql_query("SELECT patients.* FROM patients INNER JOIN queues ON patients.id = queues.patient_id WHERE queues.id = {$this->data['Order']['queue_id']}");
        $resultPatient = mysql_fetch_array($queryPatient); 
        $presciptionType = TABLE_HOME_MEDICAL;
        if($this->data['Order']['prescription_type']==1){
            $presciptionType = TABLE_HOSPITAL_MEDICAL;
        }
    ?>
    <table style="width: 100%;">
        <tr>
            <td style="text-align: center; font-weight: bold; line-height: 12px;">
                <p style="color: #083181; font-size: 14px; font-family: 'Khmer OS Muol';"><?php echo TITLE_TREATMENT_RESULT_KH;?> </p>
                <p style="color: #083181; font-size: 12px; font-family: 'Khmer OS Muol';"><?php echo TITLE_TREATMENT_RESULT.' ('.$presciptionType.')';?> </p>
            </td>
        </tr>
    </table>
    <table cellpadding="5" cellspacing="0" style="width: 100%; color: #083181;">
        <tr>
            <td><?php echo PATIENT_NAME; ?>: <?php echo $resultPatient['patient_name'];?></td>
            <td>
                <?php echo TABLE_SEX; ?>: 
                <?php 
                $gender = "";
                if($resultPatient['sex'] == "F"){
                    $gender = GENERAL_FEMALE;
                }  else {
                    $gender = GENERAL_MALE;
                }
                echo $gender;
                ?>
            </td>
        </tr>  
        <tr>
            <?php 
            $queryPatientConsultation = mysql_query("SELECT * FROM patient_consultations WHERE is_active = 1 AND queued_doctor_id = {$this->data['Order']['queue_doctor_id']}");
            $resultPatientConsultation = mysql_fetch_array($queryPatientConsultation);
            ?>
            <td><?php echo TABLE_DAIGNOSTIC;?> : <?php echo $resultPatientConsultation['daignostic']; ?></td>
            <td>
                <?php echo TABLE_AGE; ?>: 
                <?php
                $dob = "";
                if($resultPatient['dob']!="0000-00-00" || $resultPatient['dob']!=""){
                    $dob = getAgePatient($resultPatient['dob']);
                }
                echo $dob;
                ?>
            </td>
        </tr>
        <tr>
            <td><?php echo MENU_PRESCRIPTION_CODE; ?>: <?php echo $this->data['Order']['order_code']; ?></td>
            <td>
                <?php 
                $queryPatientVital = mysql_query("SELECT weight FROM patient_vital_signs WHERE is_active = 1 AND queued_doctor_id = {$this->data['Order']['queue_doctor_id']}");
                $resultPatientVital = mysql_fetch_array($queryPatientVital);
                ?>
                <?php echo TABLE_WEIGHT;?> : <?php echo $resultPatientVital['weight']; ?> kg
                <span style="padding-left: 20%;"><?php echo TABLE_TELEPHONE;?>: <?php echo $resultPatient['telephone'];?></span>
            </td>
        </tr>
    </table>
    <hr/>
    <table style="width: 100%; color: #083181;" cellpadding="5" cellspacing="0">
        <?php
            $doctorName = getDoctor($this->data['User']['id']);
            $index = 0;
            $totalPrice = 0;
            foreach ($orderDetails as $orderDetail) {
                // Check Name With Customer
                $productName = $orderDetail['Product']['name'];
                $sqlProCus   = mysql_query("SELECT name FROM product_with_customers WHERE product_id = ".$orderDetail['Product']['id']." AND customer_id = ".$resultPatient['id']." ORDER BY created DESC LIMIT 1");
                if(@mysql_num_rows($sqlProCus)){
                    $rowProCus = mysql_fetch_array($sqlProCus);
                    $productName = $rowProCus['name'];
                }
            ?>
        <tr>
            <td colspan="4" style="width: 100%;">
                <?php echo++$index; ?> - 
                <?php 
                echo $productName;
                if(trim($orderDetail['OrderDetail']['note'])!=""){
                    echo '&nbsp;&nbsp;>&nbsp;&nbsp;'.$orderDetail['OrderDetail']['note'];
                }
                if($orderDetail['OrderDetail']['qty']!=""){
                    echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$orderDetail['OrderDetail']['qty'];
                }
                if($orderDetail['Uom']['abbr']!=""){
                    echo '&nbsp;&nbsp;&nbsp; '.$orderDetail['Uom']['abbr'];
                }
                if($orderDetail['OrderDetail']['num_days']!=""){
                    echo ',&nbsp;&nbsp;&nbsp;'.$orderDetail['OrderDetail']['num_days'];
                }
                $medicinUseMorning = "";
                if($orderDetail['OrderDetail']['morning_use_id']!= ""){
                    $queryMedicineUse = mysql_query("SELECT name FROM treatment_uses WHERE id = {$orderDetail['OrderDetail']['morning_use_id']}");
                    while ($resultMedicineUse = mysql_fetch_array($queryMedicineUse)) {                        
                        $medicinUseMorning = $resultMedicineUse['name'];                                    
                    }
                }
                echo ',&nbsp;&nbsp;&nbsp;'.$medicinUseMorning;
                ?>
            </td>
            <td style="float: right;">
                <?php
                echo '<a class="btnPrintSticker" pnt-name="'.$resultPatient['patient_name'].'" doctorName="'.$doctorName.'" sex="'.$gender.'" dob="'.$dob.'" weight="'.$resultPatientVital['weight'].'" rel="' . $orderDetail['OrderDetail']['id'] . '" pro-name="' . $productName . '" qty="' . $orderDetail['OrderDetail']['qty'] . '" uom="' . $orderDetail['Uom']['abbr'] . '" note="' . $orderDetail['OrderDetail']['note'] . '" frequency="' . $medicinUseMorning . '" num-day="' . $orderDetail['OrderDetail']['num_days'] . '" ><img alt="Print Sticker" onmouseover="Tip(\'' . 'Print Sticker' . '\')" src="' . $this->webroot . 'img/button/printer.png" /></a> &nbsp;&nbsp;&nbsp;';
                ?>
            </td>
        </tr>
        <?php } ?>

        <?php foreach ($orderMiscs as $orderMisc) {  ?>
     
        <tr>
            <td colspan="4">
                <?php echo ++$index; ?> -  
                <?php 
                echo $productName = $orderMisc['OrderMisc']['description']; 
                if(trim($orderMisc['OrderMisc']['note'])!=""){
                    echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$orderMisc['OrderMisc']['note'];
                }
                if($orderMisc['OrderMisc']['qty']!=""){
                    echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$orderMisc['OrderMisc']['qty'];
                }                
                if($orderMisc['OrderMisc']['num_days']!=""){
                    echo ',&nbsp;&nbsp;&nbsp; '.TABLE_NUM_DAYS.': '.$orderMisc['OrderMisc']['num_days'];
                }
                $medicinUseMorning = "";
                if($orderMisc['OrderMisc']['morning_use_id']!= ""){
                    $queryMedicineUse = mysql_query("SELECT name FROM treatment_uses WHERE id = {$orderMisc['OrderMisc']['morning_use_id']}");
                    while ($resultMedicineUse = mysql_fetch_array($queryMedicineUse)) {             
                        $medicinUseMorning = $resultMedicineUse['name'];                                    
                    }
                }               
                echo ',&nbsp;&nbsp;&nbsp;'.$medicinUseMorning;
                ?>
            </td>
            <td style="float: right;">
                <?php
                
                echo '<a class="btnPrintSticker" pnt-name="'.$resultPatient['patient_name'].'" doctorName="'.$doctorName.'" sex="'.$gender.'" dob="'.$dob.'" weight="'.$resultPatientVital['weight'].'" rel="' . $orderMisc['OrderMisc']['id'] . '" pro-name="' . $productName . '" qty="' . $orderMisc['OrderMisc']['qty'] . '" uom="' . $orderMisc['Uom']['abbr'] . '" note="' . $orderMisc['OrderMisc']['note'] . '" frequency="' . $medicinUseMorning . '" num-day="' . $orderMisc['OrderMisc']['num_days'] . '" ><img alt="Print Sticker" onmouseover="Tip(\'' . 'Print Sticker' . '\')" src="' . $this->webroot . 'img/button/printer.png" /></a> &nbsp;&nbsp;&nbsp;';
                ?>
            </td>
        </tr>        
        <?php } ?>
    </table>
    <?php if($appointment[0]['Appointment']['app_date'] > 0){ ?>
        <p style="color: #083181;">Please bring the prescription during the next visit: <?php echo dateShort($appointment[0]['Appointment']['app_date']); ?> </p>
        <p style="color: #083181; font-size: 11px;">
            <?php echo nl2br($appointment[0]['Appointment']['description']);?>
        </p>
    <?php } ?> 
    <div style="float: right; margin-right: 10px;" class="signature">
        <table style="width: 100%; color: #083181;">
            <tr>
                <td>                    
                    <?php echo TITLE_DAY_CREATED. ' ' .dateShort($this->data['Order']['order_date']). ' ';?>                                            
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top;">
                    <?php
                    echo $doctorName; 
                    ?>
                </td>
            </tr>
        </table>
    </div>
        
    <div style="clear:both;"></div>
    <div class="print-footer" style="position: fixed; bottom: 0; text-align: center; width: 100%;">
        <center style="">
            <table style="width:100% ;">
                <tr>
                    <td rowspan="2" style="font-size:10pt; width: 75%; font-family:'Times New Roman'; vertical-align: top;"><?php echo $this->data['Branch']['address'] ?></td>
                    <td style="font-size:10pt ; width: 25% ; font-family:'Times New Roman'">Tel : <?php echo $this->data['Branch']['telephone'] ?></td>
                </tr>
                <tr>                    
                    <td style="font-size:10pt ;font-family:'Times New Roman'">Email : <?php echo $this->data['Branch']['email_address'] ?></td>
                </tr>
           
            </table>
        </center>
    </div>

    <div style="float:left;width: 450px">
        <div>
            <input type="button" value="<?php echo ACTION_PRINT; ?>" id='btnDisappearPrint' class='noprint' />
        </div>
    </div>
    <div style="clear:both"></div>
</div>

<script type="text/javascript" src="<?php echo $this->webroot; ?>js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(document).dblclick(function(){
            window.close();
        });
        $("#btnDisappearPrint").click(function(){
            $("#footerTablePrint").show();
            $("#footerTablePrint").css("width", "100%");
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
        
        $(".btnPrintSticker").click(function(event){
            var id = $(this).attr('rel');
            var pntName = $(this).attr('pnt-name');
            var sex = $(this).attr('sex');
            var dob = $(this).attr('dob');
            var medicineName = $(this).attr('pro-name');
            var qty = $(this).attr('qty');
            var uom = $(this).attr('uom');
            var note = $(this).attr('note');
            var frequency = $(this).attr('frequency');
            var numDay = $(this).attr('num-day');
            var weight =  $(this).attr('weight');
            var doctorName = $(this).attr('doctorName');
            url = "<?php echo $this->base . '/' . "doctors"; ?>/printTreatmentSticker/";
            $.ajax({
                type: "POST",
                url: url+id,
                data: "pntName="+pntName+"&sex="+sex+"&dob="+dob+"&medicineName="+medicineName+"&qty="+qty+"&uom="+uom+"&note="+note+"&frequency="+frequency+"&numDay="+numDay+"&weight="+weight+"&doctorName="+doctorName,
                beforeSend: function(){
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(printInvoiceResult){
                    w = window.open();
                    w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                    w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                    w.document.write(printInvoiceResult);
                    w.document.close();
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                }
            });
        });
    });
</script>