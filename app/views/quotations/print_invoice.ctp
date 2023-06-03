<style type="text/css" media="screen">
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
</style>
<style type="text/css" media="print">
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
    #btnDisappearPrint { display: none;}
    div.print-footer {display: block; width: 100%; position: fixed; bottom: 2px; font-size: 11px; text-align: center;}
</style>
<div class="print_doc">
    <?php
    include("includes/function.php");
    $display = "";
    if($this->data['Quotation']['vat_percent'] <= 0){
        $display = "display:none;";
    }
    $sqlRevise = mysql_query("SELECT COUNT(id) FROM quotations WHERE quotation_code = '{$this->data['Quotation']['quotation_code']}' AND id < {$this->data['Quotation']['id']}");
    $rowRevise = mysql_fetch_array($sqlRevise);
    ?>
    <table cellpadding="0" cellspacing="0" style="width: 100%;">
        <thead>
            <tr>
                <td style="height: 95px; vertical-align: bottom; padding-left: 40px;">
                    <img src="<?php echo $this->webroot; ?>public/company_photo/<?php echo $this->data['Company']['photo']; ?>" style=" width: 130px; margin: 0px auto; <?php if($head == 1){ ?>display: none;<?php } ?>" />
                </td>
            </tr>
            <tr>
                <td>
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <tr>
                            <td style=" text-align: center; font-size: 20px;">Quotation</td>
                        </tr>
                    </table>
                    <div style="height: 10px"></div>
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 8px;">
                        <tr>
                            <td style="width: 5%;" class="titleHeader titleContent contentHeight">To:</td>
                            <td style="width: 46%;" class="titleHeader"> <?php echo $this->data['Customer']['name']; ?></td>
                            <td style="width: 38%;" class="titleHeader titleContent">Quote ID:</td>
                            <td class="titleHeader"> <?php echo $this->data['Quotation']['quotation_code']; ?> <?php if($rowRevise[0] > 0){ echo "(R".($rowRevise[0]).")"; } ?></td>
                        </tr>
                        <?php
                            $rowSpan = 1;
                            $addressTop = '';
                            $addressBottom = '';
                            if($this->data['Customer']['type'] == 1){
                                if($this->data['Customer']['province_id'] > 0){
                                    $provinceId = $this->data['Customer']['province_id'];
                                    $districtId = $this->data['Customer']['district_id']>0?$this->data['Customer']['district_id']:'0';
                                    $communeId  = $this->data['Customer']['commune_id']>0?$this->data['Customer']['commune_id']:'0';
                                    $villageId  = $this->data['Customer']['village_id']>0?$this->data['Customer']['village_id']:'0';
                                    $sqlAddress = mysql_query("SELECT p.name AS p_name, d.name AS d_name, c.name AS c_name, v.name AS v_name FROM provinces AS p LEFT JOIN districts AS d ON d.province_id = p.id AND d.id = {$districtId} LEFT JOIN communes AS c ON c.district_id = d.id AND c.id = {$communeId} LEFT JOIN villages AS v ON v.commune_id = c.id AND v.id = {$villageId} WHERE p.id = {$this->data['Customer']['province_id']}");    
                                    $rowAddress = mysql_fetch_array($sqlAddress);
                                }else{
                                    $rowAddress['p_name'] = '';
                                    $rowAddress['d_name'] = '';
                                    $rowAddress['c_name'] = '';
                                    $rowAddress['v_name'] = '';
                                }
                                $house = $this->data['Customer']['house_no']!=''?$this->data['Customer']['house_no'].",":'';
                                $street = '';
                                if($this->data['Customer']['street_id'] != ''){
                                    $sqlStreet = mysql_query("SELECT name FROM streets WHERE id = ".$this->data['Customer']['street_id']);
                                    $rowStreet = mysql_fetch_array($sqlStreet);
                                    $street = " ".$rowStreet[0].",";
                                }
                                $village  = $rowAddress['v_name']!=''?" ".$rowAddress['v_name'].",":'';
                                $commune  = $rowAddress['c_name']!=''?" Sangkat ".$rowAddress['c_name'].",":'';
                                $district = $rowAddress['d_name']!=''?" Khan ".$rowAddress['d_name'].",":'';
                                $province = $rowAddress['p_name']!=''?" ".$rowAddress['p_name']."":'';
                                $addressTop    = $house.$street.$village.$commune.$district;
                                $addressBottom = $province;
                            }else{
                                $addressTop =  nl2br($this->data['Customer']['address']);
                                $rowSpan = 2;
                            }
                        ?>
                        <tr>
                            <td class="titleHeader titleContent contentHeight">Add:</td>
                            <td class="titleHeader" rowspan="<?php echo $rowSpan; ?>"> <?php echo $addressTop; ?></td>
                            <td class="titleHeader titleContent">Date:</td>
                            <td class="titleHeader"> <?php echo dateShort($this->data['Quotation']['quotation_date']); ?></td>
                        </tr>
                        <tr>
                            <td class="titleHeader contentHeight"></td>
                            <?php if($addressBottom!=''){ ?><td class="titleHeader"> <?php echo $addressBottom; ?></td><?php } ?>
                            <td class="titleHeader titleContent"></td>
                            <td class="titleHeader"></td>
                        </tr>
                        <tr>
                            <td class="titleHeader titleContent contentHeight" style="<?php echo $display;?>">VATTIN:</td>
                            <td class="titleHeader" style="<?php echo $display;?>"> <?php echo $this->data['Customer']['vat']; ?></td>
                            <td class="titleHeader titleContent" style="<?php echo $display;?>"></td>
                            <td class="titleHeader" style="<?php echo $display;?>"></td>
                        </tr>
                        <tr>
                            <td class="titleHeader titleContent contentHeight">Attn:</td>
                            <td class="titleHeader"> <?php echo $this->data['CustomerContact']['contact_name']; ?></td>
                            <td class="titleHeader titleContent" style="<?php echo $display;?>">VAT:</td>
                            <td class="titleHeader" style="<?php echo $display;?>"> <?php echo $this->data['Company']['vat_number']; ?></td>
                        </tr>
                        <tr>
                            <td class="titleHeader titleContent contentHeight">Tel:</td>
                            <td class="titleHeader"> <?php echo $this->data['CustomerContact']['contact_telephone']; ?></td>
                            <td class="titleHeader titleContent" style="<?php echo $display;?>"></td>
                            <td class="titleHeader" style="<?php echo $display;?>"></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <table class="table_print" style="border: none; margin: 0px; padding: 0px; width: 100%;">
                        <tr>
                            <th style="width: 5%;" class="titleHeaderTable titleHeaderHeight">No.</th>
                            <th class="titleHeaderTable">DESCRIPTION</th>
                            <th style="width: 13%;" class="titleHeaderTable">BRAND</th>
                            <th style="width: 10%;" class="titleHeaderTable">Code</th>
                            <th style="width: 5%;" class="titleHeaderTable">QTY</th>
                            <th style="width: 9%;" class="titleHeaderTable">UoM</th>
                            <th style="width: 10%;" class="titleHeaderTable">Price</th>
                            <th style="width: 10%;" class="titleHeaderTable">Amount</th>
                        </tr>
                        <?php
                        $index = 0;
                        foreach ($quotationDetails as $quotationDetail) {
                            // Check Name With Customer
                            $productName = $quotationDetail['Product']['name'];
                            $sqlProCus   = mysql_query("SELECT name FROM product_with_customers WHERE product_id = ".$quotationDetail['Product']['id']." AND customer_id = ".$this->data['Customer']['id']." ORDER BY created DESC LIMIT 1");
                            if(@mysql_num_rows($sqlProCus)){
                                $rowProCus = mysql_fetch_array($sqlProCus);
                                $productName = $rowProCus['name'];
                            }
                            $sqlPgroups  = mysql_query("SELECT GROUP_CONCAT(name) FROM pgroups WHERE id IN (SELECT pgroup_id FROM product_pgroups WHERE product_id = ".$quotationDetail['Product']['id'].")");
                            $rowPgroups  = mysql_fetch_array($sqlPgroups);
                        ?>
                            <!-- Product Description -->
                            <tr>
                                <td class="first" style="text-align: center; font-size: 11px; height: 20px; padding-bottom: 0px; padding-top: 0px;"><?php echo ++$index; ?></td>
                                <td style="font-size: 11px; padding-bottom: 0px; padding-top: 0px; font-weight: bold;">
                                    <?php 
                                        echo $productName;
                                        // show discount of product
                                        if ($quotationDetail['QuotationDetail']['discount_amount'] > 0) {
                                            echo '<br/>';
                                            echo '<p style="font-size: 11px; font-weight: normal; padding-left: 5px; margin: 0px;">'.$quotationDetail['Discount']['name'] . ' - '.$this->data['CurrencyCenter']['symbol'].' ' . number_format($quotationDetail['QuotationDetail']['discount_amount'], 2).'</p>';
                                        }
                                    ?>
                                </td>
                                <td style="font-size: 11px; padding-bottom: 0px; padding-top: 0px;"><?php echo $rowPgroups[0]; ?></td>
                                <td style="font-size: 11px; padding-bottom: 0px; padding-top: 0px;"><?php echo $quotationDetail['Product']['code']; ?></td>
                                <td style="font-size: 11px; padding-bottom: 0px; padding-top: 0px; text-align: center;"><?php echo number_format($quotationDetail['QuotationDetail']['qty'], 0); ?></td>
                                <td style="font-size: 11px; padding-bottom: 0px; padding-top: 0px; text-align: center;"><?php echo $quotationDetail['Uom']['abbr']; ?></td>
                                <td style="text-align: right; font-size: 11px; padding-bottom: 0px; padding-top: 0px;"><span style="float: left; width: 12px; font-size: 11px;"><?php echo $this->data['CurrencyCenter']['symbol']; ?></span><?php echo number_format($quotationDetail['QuotationDetail']['unit_price'], 2); ?></td>
                                <td style="text-align: right; font-size: 11px; padding-bottom: 0px; padding-top: 0px;"><span style="float: left; width: 12px; font-size: 11px;"><?php echo $this->data['CurrencyCenter']['symbol']; ?></span><?php echo number_format($quotationDetail['QuotationDetail']['total_price'] - $quotationDetail['QuotationDetail']['discount_amount'], 2); ?></td>
                            </tr>
                            <!-- Product Spec & Photo -->
                            <?php
                            if($quotationDetail['Product']['photo'] != '' || $quotationDetail['Product']['spec'] != ''){
                            ?>
                            <tr>
                                <td class="first" style="text-align: center; font-size: 11px; height: 20px; padding-bottom: 0px; padding-top: 0px;"></td>
                                <td style="font-size: 11px; padding-bottom: 0px; padding-top: 0px; vertical-align: top;"><?php echo nl2br($quotationDetail['Product']['spec']); ?></td>
                                <td style="font-size: 11px; padding-bottom: 2px; padding-top: 2px; text-align: center;" colspan="4">
                                    <?php if($quotationDetail['Product']['photo'] != ''){ ?><img src="<?php echo $this->webroot; ?>public/product_photo/<?php echo $quotationDetail['Product']['photo']; ?>" style=" max-width: 180px; max-height: 110px; margin: 0px auto;" /><?php } ?>
                                </td>
                                <td style="text-align: right; font-size: 11px; padding-bottom: 0px; padding-top: 0px;"></td>
                                <td style="text-align: right; font-size: 11px; padding-bottom: 0px; padding-top: 0px;"></td>
                            </tr>
                        <?php
                            }
                        }
                        foreach ($quotationServices as $quotationService) {
                            $uomName = '';
                            if($quotationService['Service']['uom_id'] != ''){
                                $sqlUom = mysql_query("SELECT abbr FROM uoms WHERE id = ".$quotationService['Service']['uom_id']);
                                $rowUom = mysql_fetch_array($sqlUom);
                                $uomName = $rowUom[0];
                            }
                        ?>
                            <tr>
                                <td class="first" style="text-align: center; font-size: 11px; height: 20px; padding-bottom: 0px; padding-top: 0px;"><?php echo ++$index; ?></td>
                                <td style="font-size: 11px; padding-bottom: 0px; padding-top: 0px; font-weight: bold;">
                                    <?php
                                        echo $quotationService['Service']['name'];
                                        if (trim($quotationService['Service']['description']) != "") {
                                            echo '<br/>';
                                            echo '<p style="padding-left:10px;font-size:10px;">' . nl2br($quotationService['Service']['description']) . '</p>';
                                        }
                                        // show discount of service
                                        if ($quotationService['QuotationService']['discount_amount'] > 0) {
                                            echo $quotationService['Discount']['name'] . ' - '.$this->data['CurrencyCenter']['symbol'].' ' . number_format($quotationService['QuotationService']['discount_amount'], 2);
                                        }
                                    ?>
                                </td>
                                <td></td>
                                <td style="font-size: 11px; padding-bottom: 0px; padding-top: 0px;"><?php echo $quotationService['Service']['code']; ?></td>
                                <td style="font-size: 11px; padding-bottom: 0px; padding-top: 0px; text-align: center;"><?php echo number_format($quotationService['QuotationService']['qty'], 0); ?></td>
                                <td style="font-size: 11px; padding-bottom: 0px; padding-top: 0px; text-align: center;"><?php echo $uomName; ?></td>
                                <td style="text-align: right; font-size: 11px; padding-bottom: 0px; padding-top: 0px;"><span style="float: left; width: 12px; font-size: 11px;"><?php echo $this->data['CurrencyCenter']['symbol']; ?></span><?php echo number_format($quotationService['QuotationService']['unit_price'], 2); ?></td>
                                <td style="text-align: right; font-size: 11px; padding-bottom: 0px; padding-top: 0px;"><span style="float: left; width: 12px; font-size: 11px;"><?php echo $this->data['CurrencyCenter']['symbol']; ?></span><?php echo number_format($quotationService['QuotationService']['total_price'] - $quotationService['QuotationService']['discount_amount'], 2); ?></td>
                            </tr>
                        <?php
                        }
                        foreach ($quotationMiscs as $quotationMisc) {
                        ?>
                            <tr>
                                <td class="first" style="text-align: center; font-size: 11px; height: 20px; padding-bottom: 0px; padding-top: 0px;"><?php echo ++$index; ?></td>
                                <td style="font-size: 11px; padding-bottom: 0px; padding-top: 0px; font-weight: bold;">
                                    <?php 
                                        echo $quotationMisc['QuotationMisc']['description']; 
                                        // show discount of Misc
                                        if ($quotationMisc['QuotationMisc']['discount_amount'] > 0) {
                                            echo '<br/>';
                                            echo $quotationMisc['Discount']['name'] . ' - '.$this->data['CurrencyCenter']['symbol'].' ' . number_format($quotationMisc['QuotationMisc']['discount_amount'], 2);
                                        }
                                    ?>
                                </td>
                                <td></td>
                                <td style="font-size: 11px; padding-bottom: 0px; padding-top: 0px;"></td>
                                <td style="font-size: 11px; padding-bottom: 0px; padding-top: 0px; text-align: center;"><?php echo number_format($quotationMisc['QuotationMisc']['qty'], 0); ?></td>
                                <td style="font-size: 11px; padding-bottom: 0px; padding-top: 0px; text-align: center;"><?php echo $quotationMisc['Uom']['abbr']; ?></td>
                                <td style="text-align: right; font-size: 11px; padding-bottom: 0px; padding-top: 0px;"><span style="float: left; width: 12px; font-size: 11px;"><?php echo $this->data['CurrencyCenter']['symbol']; ?></span><?php echo number_format($quotationMisc['QuotationMisc']['unit_price'], 2); ?></td>
                                <td style="text-align: right; font-size: 11px; padding-bottom: 0px; padding-top: 0px;"><span style="float: left; width: 12px; font-size: 11px;"><?php echo $this->data['CurrencyCenter']['symbol']; ?></span><?php echo number_format($quotationMisc['QuotationMisc']['total_price'] - $quotationMisc['QuotationMisc']['discount_amount'], 2); ?></td>
                            </tr>
                        <?php
                        }
                        $rowSpan = 3;
                        if ($this->data['Quotation']['discount'] > 0) {
                            $rowSpan = $rowSpan + 1;
                        }
                        if ($this->data['Quotation']['total_vat'] > 0) {
                            $rowSpan = $rowSpan + 1;
                        }
                        ?>
                        <tr>
                            <td class="first"  rowspan="<?php echo $rowSpan; ?>" style="border-bottom: none; border-left: none; border-right: none; text-align: left; font-size: 11px; height: 20px; padding-bottom: 0px; padding-top: 0px;" colspan="6">
                                <?php
                                $sqlTerm = mysql_query("SELECT term_conditions.name AS name FROM quotation_term_conditions INNER JOIN term_conditions ON term_conditions.id = quotation_term_conditions.term_condition_id WHERE quotation_term_conditions.quotation_id = ".$this->data['Quotation']['id']);                                    
                                if(mysql_num_rows($sqlTerm)){
                                ?>
                                <!-- Term & Condition -->
                                <table cellpadding="0" cellspacing="0" style="width: 100%; margin-top: 5px;">
                                    <tr>
                                        <td style="font-size: 10px; font-weight: bold; vertical-align: top; border: none; padding: 0px; height: 15px;">Terms and Condition:</td>
                                    </tr>
                                    <?php
                                    while($rowTerm = mysql_fetch_array($sqlTerm)){
                                    ?>
                                    <tr>
                                        <td style="font-size: 10px; vertical-align: top; border: none; padding: 0px;">- <?php echo $rowTerm['name']; ?></td>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                </table>
                                <!-- Note -->
                                <?php
                                }
                                if(!empty($this->data['Quotation']['note'])){
                                ?>
                                <table cellpadding="0" cellspacing="0" style="width: 100%; margin-top: 10px;">
                                    <tr>
                                        <td style="width: 40px; font-size: 10px; font-weight: bold; vertical-align: top; border: none; padding: 0px; height: 15px;">Notice:</td>
                                        <td style="font-size: 10px; text-align: left; vertical-align: top; border: none; padding: 0px;"> <?php echo nl2br($this->data['Quotation']['note']); ?></td>
                                    </tr>
                                </table>
                                <?php 
                                }
                                ?>
                            </td>
                            <td style="text-align: right; font-size: 11px; font-weight: bold; padding-bottom: 0px; padding-top: 0px; height: 20px;">
                                <?php 
                                if($this->data['Quotation']['total_vat'] > 0 || $this->data['Quotation']['discount'] > 0){ 
                                    echo 'SUB TOTAL';
                                }else { 
                                    echo 'TOTAL';
                                }
                                ?>
                            </td>
                            <td style="text-align: right; font-size: 11px; padding-bottom: 0px; padding-top: 0px;"><span style="float: left; width: 12px; font-size: 11px;"><?php echo $this->data['CurrencyCenter']['symbol']; ?></span><?php echo number_format($this->data['Quotation']['total_amount'], 2); ?></td>
                        </tr>
                        <?php
                        if($this->data['Quotation']['discount'] > 0){
                        ?>
                        <tr>
                            <td style="text-align: right; font-size: 11px; font-weight: bold; padding-bottom: 0px; padding-top: 0px; height: 20px;">DISCOUNT <?php if($this->data['Quotation']['discount_percent'] > 0){ echo '('.number_format($this->data['Quotation']['discount_percent'], 2).'%)'; } ?></td>
                            <td style="text-align: right; font-size: 11px; padding-bottom: 0px; padding-top: 0px;"><span style="float: left; width: 12px; font-size: 11px;"><?php echo $this->data['CurrencyCenter']['symbol']; ?></span><?php echo number_format($this->data['Quotation']['discount'], 2); ?></td>
                        </tr>
                        <?php
                        }
                        if($this->data['Quotation']['total_vat'] > 0){
                        ?>
                        <tr>                            
                            <td style="text-align: right; font-size: 11px; font-weight: bold; padding-bottom: 0px; padding-top: 0px; height: 20px;">VAT (<?php echo number_format($this->data['Quotation']['vat_percent'], 0); ?>%)</td>
                            <td style="text-align: right; font-size: 11px; padding-bottom: 0px; padding-top: 0px;"><span style="float: left; width: 12px; font-size: 11px;"><?php echo $this->data['CurrencyCenter']['symbol']; ?></span><?php echo number_format($this->data['Quotation']['total_vat'], 2); ?></td>
                        </tr>
                        <?php
                        }
                        if($this->data['Quotation']['discount'] > 0 || $this->data['Quotation']['total_vat'] > 0){
                        ?>
                        <tr>
                            <td style="text-align: right; font-size: 11px; font-weight: bold; padding-bottom: 0px; padding-top: 0px; height: 20px;">TOTAL</td>
                            <td style="text-align: right; font-size: 11px; padding-bottom: 0px; padding-top: 0px;"><span style="float: left; width: 12px; font-size: 11px;"><?php echo $this->data['CurrencyCenter']['symbol']; ?></span><?php echo number_format($this->data['Quotation']['total_amount'] - $this->data['Quotation']['discount'] + $this->data['Quotation']['total_vat'], 2); ?></td>
                        </tr>
                        <?php
                        }
                        ?>
                        <tr>
                            <td colspan="2" style="border: none;"></td>
                        </tr>
                    </table>
                    <div style="clear:both"></div>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td style="height: 165px;">
                    &nbsp;
                    <div id="footerTablePrint" style="display: none;">
                        <table style="width: 100%;" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="padding: 0px; font-size: 9px; vertical-align: bottom;">
                                    <table style="display: none;">
                                        <tr>
                                            <td style="font-size: 9px; width: 20px; padding: 0px; height: 12px;">
                                                Tel/Fax: 
                                            </td>
                                            <td style="font-size: 9px; padding: 0px;">
                                                <?php echo $this->data['Company']['business_number']; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 9px; vertical-align: top; padding: 0px; height: 12px;">
                                                Address: 
                                            </td>
                                            <td style="font-size: 9px; padding: 0px;">
                                                <?php echo nl2br($this->data['Company']['address']); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 9px; padding: 0px; height: 12px;">
                                                Website: 
                                            </td>
                                            <td style="font-size: 9px; padding: 0px;">
                                                
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="width: 25%; text-align: right;" rowspan="2">
                                    <table cellpadding="0" cellspacing="0" style="width: 200px; float: right; margin-right: 20px;">
                                        <tr>
                                            <td style="text-align: left; font-size: 10px;">Approved By: </td>
                                        </tr>
                                        <tr>
                                            <td style="height: 110px; text-align: left; vertical-align: bottom; font-size: 10px;">Signature:&nbsp;&nbsp;&nbsp;..........................................................................................</td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="width: 25%; text-align: right; padding-left: 5px;" rowspan="2">
                                    <table cellpadding="0" cellspacing="0" style="width: 200px; float: right; margin-right: 20px;">
                                        <tr>
                                            <td style="text-align: left; font-size: 10px;">Issued By: <?php echo $this->data['User']['first_name']." ".$this->data['User']['last_name']; ?></td>
                                        </tr>
                                        <tr>
                                            <td style="height: 110px; text-align: left; vertical-align: bottom; font-size: 10px;">Signature:&nbsp;&nbsp;&nbsp;..........................................................................................</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
    <br />
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
            window.print();
            window.close();
        });
    });
</script>