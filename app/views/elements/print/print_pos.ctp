<div class="print_doc" style="width: 350px;">
    <?php
    $printerName = '';
    $silent = 0;
    $sqlPrinter = mysql_query("SELECT printer_name, silent FROM printers WHERE module_name = 'POS' AND is_active = 1 ORDER BY id DESC LIMIT 1");
    if(mysql_num_rows($sqlPrinter)){
        $rowPrinter = mysql_fetch_array($sqlPrinter);
        $printerName = $rowPrinter['printer_name'];
        $silent = $rowPrinter['silent'];
    }
    
    ?>
    <input type="hidden" id="printerSettingSilent" value="<?php echo $printerName; ?>" />
    <input type="hidden" id="printerSettingName" value="<?php echo $silent; ?>" />
    <table style="width: 100%;">
        <tr>
            <td style="vertical-align: top; text-align: center;">
                <img alt="" src="" style="height: 80px;" id="printCompanyLogo" />
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top; text-align: center;">
                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                    <tr>
                        <td style="vertical-align: top; text-align: center;">
                            <div style="font-size: 12px; text-align: center;" id="printBranchName">
                                // Branch Name
                            </div>
                            <div style="font-size: 12px; text-align: center;" id="printBranchAddress">
                                // Branch Address
                            </div>
                            <div style="font-size: 12px; text-align: center;">
                                Tel: <span id="printBranchTel">// Branch Telephone</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top; text-align: center; white-space: nowrap; font-size: 17px; font-weight: bold;">
                វិក័យប័ត្រ
            </td>
        </tr>
    </table>
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 17%; font-size: 12px;">លេខវិក្ក័យប័ត្រ:</td>
            <td style="width: 35%; font-size: 12px;" id="printInvoiceCode">// Invoice Code</td>
            <td style="width: 17%; font-size: 12px; text-align: right;">កាលបរិច្ឆេទ:</td>
            <td style="font-size: 12px;" id="printInvoiceDate">// Invoice Date</td>
        </tr>
        <tr>
            <td style="width: 15%; font-size: 11px;">បេឡាករ:</td>
            <td style="font-size: 11px;" id="printUsername">// User Name</td>
            <td style="font-size: 11px;">អតិថិជន:</td>
            <td style="font-size: 11px;" id="printCustomerName">// Customer</td>
        </tr>
    </table>
    <table cellpadding="0" cellspacing="0" style="width:100%;" border="1">
            <tr>
                <th style="padding-bottom: 0px; padding-top: 0px; color:#000; font-size: 11px; font-weight: bold; width: 5%; text-align: center; border: 1px solid #000;" class="first">
                    ល.រ
                    <span style="display:block; font-size: 10px;">No.</span>
                </th>
                <th style="padding-bottom: 0px; padding-top: 0px; color:#000; font-size: 11px; font-weight: bold; width: 43% !important; text-align: center; border: 1px solid #000;">
                    បរិយាយ
                    <span style="display:block; font-size: 10px;">Description</span>
                </th>
                <th style="padding-bottom: 0px; padding-top: 0px; color:#000; font-size: 11px; font-weight: bold; width: 10%; text-align: center; border: 1px solid #000;">
                    បរិមាណ
                    <span style="display:block; font-size: 10px;">Qty</span>
                </th>
                <th style="padding-bottom: 0px; padding-top: 0px; color:#000; font-size: 11px; font-weight: bold; width: 7%; text-align: center; border: 1px solid #000;">
                    ខ្នាត
                    <span style="display:block; font-size: 10px;">UoM</span>
                </th>
                <th style="padding-bottom: 0px; padding-top: 0px; color:#000; font-size: 11px; font-weight: bold; width: 10%; text-align: center; border: 1px solid #000;">
                    តម្លៃ
                    <span style="display:block; font-size: 10px;">Price</span>
                </th>
                <th style="padding-bottom: 0px; padding-top: 0px; color:#000; font-size: 11px; font-weight: bold; width: 10%; text-align: center; border: 1px solid #000;">
                    ចុះថ្លៃ
                    <span style="display:block; font-size: 10px;">Dis</span>
                </th>
                <th style="padding-bottom: 0px; padding-top: 0px; color:#000; font-size: 11px; font-weight: bold; width: 10%; text-align: center; border: 1px solid #000;">
                    សរុប
                    <span style="display:block; font-size: 10px;">Total</span>
                </th>
            </tr>
            <tbody id="printDetailItems">
                <!-- Product Normal -->
                <tr>
                    <td style="text-align: center; padding-bottom: 0px; padding-top: 0px; font-size: 11px;" id="printDetailNo">
                        // Index
                    </td>
                    <td style="padding-bottom: 0px; padding-top: 0px; font-size: 11px;" id="printDetailName">// Product Name</td>
                    <td style="padding-bottom: 0px; padding-top: 0px; font-size: 11px; text-align: center;" id="printDetailQTY">// QTY</td>
                    <td style="padding-bottom: 0px; padding-top: 0px; font-size: 11px; text-align: center;" id="printDetailUoM">
                        // UoM Name
                    </td>
                    <td style="padding-bottom: 0px; padding-top: 0px; font-size: 11px; text-align: right;" id="printDetailUnitPrice">// Unit Price</td>
                    <td style="padding-bottom: 0px; padding-top: 0px; font-size: 11px; text-align: right;" id="printDetailDiscount">// Discount</td>
                    <td style="padding-bottom: 0px; padding-top: 0px; font-size: 11px; text-align: right;" id="printDetailTotalPrice">// Total Price</td>
                </tr>
            </tbody>
    </table>
    <table cellpadding="0" cellspacing="0" style="width: 100%;">
        <tr>
            <td style="width: 45%; padding-top: 0px; padding-bottom: 0px; font-size: 11px;"><span style="font-size: 12px;">សរុប</span> / Sub Total</td>
            <td style="width: 25%; padding-top: 0px; padding-bottom: 0px; text-align: right;"><div style="width:10px; float: left; font-size:14px; margin-left: 5px;" class="printMainCurrency">// Main Symbol Currency</div><span id="printSubTotalMain" style="font-size: 12px;">// Sub Total Amount</span></td>
            <td style="width: 30%; padding-top: 0px; padding-bottom: 0px; text-align: right;"></td>
        </tr>
        <tr>
            <td style="padding-top: 0px; padding-bottom: 0px; font-size: 11px;"><span style="font-size: 12px;">បញ្ចុះតំលៃ</span> / Discount <span id="printDiscountPercent" style="font-size: 11px;"></span></td>
            <td style="padding-top: 0px; padding-bottom: 0px; text-align: right;"><div style="width:10px; float: left; font-size:14px; margin-left: 5px;" class="printMainCurrency">// Main Symbol Currency</div><span id="printSubTotalDiscount" style="font-size: 12px;">// Total Discount</span></td>
            <td style="padding-top: 0px; padding-bottom: 0px; text-align: right;"></td>
        </tr>
        <tr>
            <td style="padding-top: 0px; padding-bottom: 0px; font-size: 11px;">VAT</td>
            <td style="padding-top: 0px; padding-bottom: 0px; text-align: right;"><div style="width:10px; float: left; font-size:14px; margin-left: 5px;" class="printMainCurrency">// Main Symbol Currency</div><span id="printSubTotalVAT" style="font-size: 12px;">// Total VAT</span></td>
            <td style="padding-top: 0px; padding-bottom: 0px; text-align: right;"></td>
        </tr>
        <tr>
            <td style="padding-top: 0px; padding-bottom: 0px; font-size: 11px;"><span style="font-size: 12px;">សរុបចុងក្រោយ</span> / Total</td>
            <td style="padding-top: 0px; padding-bottom: 0px; text-align: right; font-weight: bold;"><div style="width:10px; float: left; font-size:14px; margin-left: 5px;" class="printMainCurrency">// Main Symbol Currency</div><span id="printTotalMain" style="font-size: 16px; font-weight: bold;">// Total Amount</span></td>
            <td style="padding-top: 0px; padding-bottom: 0px; text-align: right; font-weight: bold;"><div style="width:10px; float: left; font-size:14px; margin-left: 5px;" class="printOtherCurrency">// Other Symbol Currency</div><span id="printTotalOther" style="font-size: 16px; font-weight: bold;">// Total Amount Other Currency</span></td>
        </tr>
        <tr class="payNow">
            <td style="padding-top: 0px; padding-bottom: 0px; font-size: 11px;"><span style="font-size: 12px;">ប្រាក់ទទួល</span> / Received</td>
            <td style="padding-top: 0px; padding-bottom: 0px; text-align: right;"><div style="width:10px; float: left; font-size:14px; margin-left: 5px;" class="printMainCurrency">// Main Symbol Currency</div><span id="printTotalReceiveMain" style="font-size: 16px; font-weight: bold;">// Total Received</span></td>
            <td style="padding-top: 0px; padding-bottom: 0px; text-align: right;"><div style="width:10px; float: left; font-size:14px; margin-left: 5px;" class="printOtherCurrency">// Other Symbol Currency</div><span id="printTotalReceiveOther" style="font-size: 16px; font-weight: bold;">// Total Received Other Currency</span></td>
        </tr>
        <tr class="payNow">
            <td style="padding-top: 0px; padding-bottom: 0px; font-size: 11px;"><span style="font-size: 12px;">ប្រាក់អាប់</span> / Change</td>
            <td style="padding-top: 0px; padding-bottom: 0px; text-align: right; font-weight: bold;"><div style="width:10px; float: left; font-size:14px; margin-left: 5px;" class="printMainCurrency">// Main Symbol Currency</div><span id="printTotalChangeMain" style="font-size: 16px; font-weight: bold;">// Total Change</span></td>
            <td style="padding-top: 0px; padding-bottom: 0px; text-align: right; font-weight: bold;"><div style="width:10px; float: left; font-size:14px; margin-left: 5px;" class="printOtherCurrency">// Other Symbol Currency</div><span id="printTotalChangeOther" style="font-size: 16px; font-weight: bold;">// Total Change Other Currency</span></td>
        </tr>
    </table>
    <div style="clear:both;"></div>
    <div style="margin-top: 10px; font-size:10px; margin-bottom: 100px; padding: 0px;">
        <div style="font-size:9px; text-align: center; line-height: 20px;">
            Print: <span id="printDatePOS" style="font-size:9px;">// Print Date</span>
            <span style="display:block; font-size: 10px;">អរគុណ សូមអញ្ចើញមកម្តងទៀត។</span>
            Thank you. Please come again.
            <span style="display:block; font-size: 9px;">បង្កើតឡើងដោយ UDAYA TECHNOLOGY Co,.Ltd</span>
        </div>
    </div>
    <div style="clear:both"></div>
</div>