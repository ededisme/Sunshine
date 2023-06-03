<?php 
    include("includes/function.php");
    $sqlSettingUomDeatil = mysql_query("SELECT uom_detail_option FROM setting_options");
    $rowSettingUomDetail = mysql_fetch_array($sqlSettingUomDeatil);
    $status = array('0'=>'Void','1'=>'Issued','2'=>'Partial','3'=>'Fulfilled');
?>
<style type="text/css" media="screen">
    div.print-footer {display: none;}
</style> 
<style type="text/css" media="print">
    div.print_doc { width:100%;}
    #btnDisappearPrint { display: none;}
    div.print-footer {display: block; width:100%;} 
</style>
<div class="print_doc">
    <?php
    $msg = 'Transfer Order';
    echo $this->element('/print/header', array('msg' => $msg, 'barcode' => $this->data['TransferOrder']['to_code'], 'logo' => $this->data['Company']['photo']));
    ?>
    <div style="height: 10px"></div>
    <table cellpadding="5" width="100%">
        <tr>
            <td style="font-size: 10px;"><?php echo TABLE_TO_NUMBER; ?>: <?php echo $this->data['TransferOrder']['to_code']; ?></td>
            <td style="vertical-align: top; font-size: 10px;" width="25%"><?php echo TABLE_TO_DATE; ?>: <?php echo dateShort($this->data['TransferOrder']['order_date'], "d/M/Y"); ?></td>
        </tr>
        <tr>
            <td style="font-size: 10px;"><?php echo TABLE_REQUEST_STOCK_DATE; ?>: <?php if($this->data['RequestStock']['date'] != ""){ echo dateShort($this->data['RequestStock']['date']); }else { echo "N/A";} ?></td>
            <td style="vertical-align: top; font-size: 10px;" width="25%">
                <?php echo TABLE_FROM_WAREHOUSE; ?>: <?php echo $fromLocationGroups['LocationGroup']['name']; ?>
            </td>
        </tr>
        <tr>
            <td style="font-size: 10px;"><?php echo TABLE_REQUEST_STOCK_NUMBER; ?>: <?php if(!empty($this->data['RequestStock']['code'])){ echo $this->data['RequestStock']['code']; }else{ echo "N/A"; }; ?></td>
            <td style="vertical-align: top; font-size: 10px;">
                <?php echo TABLE_TO_WAREHOUSE; ?>: <?php echo $toLocationGroups['LocationGroup']['name']; ?>
            </td>
        </tr>
    </table>
    <br />
    <div>
        <div>
            <table id="tblTO" class="table_print">
                <tr>
                    <th class="first" style="width:4%; font-size: 10px;"><?php echo TABLE_NO; ?></th>
                    <th style="width:8%; font-size: 10px;"><?php echo TABLE_SKU; ?></th>
                    <th style="width:26%; font-size: 10px;"><?php echo TABLE_PRODUCT_NAME; ?></th>
                    <th style="width:10%; font-size: 10px; <?php if($rowSettingUomDetail[0] == 0){ ?>display: none;<?php } ?>"><?php echo TABLE_LOTS_NO; ?></th>
                    <th style="width:11%; font-size: 10px;"><?php echo TABLE_EXPIRED_DATE; ?></th>
                    <th style="width:11%; font-size: 10px;"><?php echo TABLE_LOCATION_FROM; ?></th>
                    <th style="width:11%; font-size: 10px;"><?php echo TABLE_LOCATION_TO; ?></th>
                    <th style="width:7%; font-size: 10px;"><?php echo TABLE_QTY; ?></th>
                    <th style="width:12%; font-size: 10px;"><?php echo TABLE_UOM; ?></th>
                </tr>
            <?php
            if(!empty($transferOrderDetails)){
                $index = 0;
                foreach($transferOrderDetails AS $transferOrderDetail){
            ?>
                <tr class="recordTODetail">
                    <td class="first" style="width:4%; font-size: 10px;"><?php echo ++$index; ?></td>
                    <td style="width:8%; font-size: 10px;">
                        <?php echo $transferOrderDetail['Product']['code']; ?>
                    </td>
                    <td style="width:18%; font-size: 10px;">
                        <?php echo $transferOrderDetail['Product']['name']; ?>
                    </td>
                    <td style="width:10%; font-size: 10px; <?php if($rowSettingUomDetail[0] == 0){ ?>display: none;<?php } ?>">
                        <?php echo $transferOrderDetail['TransferOrderDetail']['lots_number']; ?>
                    </td>
                    <td style="width:11%; font-size: 10px;">
                        <?php 
                        if($transferOrderDetail['TransferOrderDetail']['expired_date'] != "0000-00-00" && $transferOrderDetail['TransferOrderDetail']['expired_date'] != ""){
                            $expDateLbl = dateShort($transferOrderDetail['TransferOrderDetail']['expired_date'], 'd/M/Y');
                        }else{
                            $expDateLbl = "";
                        }
                        echo $expDateLbl; 
                        ?>
                    </td>
                    <td style="width:11%; font-size: 10px;">
                        <?php
                        $sqlLocationFrom = mysql_query("SELECT name FROM locations WHERE id = {$transferOrderDetail['TransferOrderDetail']['location_from_id']} ORDER BY name");
                        $rowLocationFrom = mysql_fetch_array($sqlLocationFrom);
                        echo $rowLocationFrom[0];
                        ?>
                    </td>
                    <td style="width:11%; font-size: 10px;">
                        <?php
                        $sqlLocationTo = mysql_query("SELECT name FROM locations WHERE id = {$transferOrderDetail['TransferOrderDetail']['location_to_id']} ORDER BY name");
                        $rowLocationTo = mysql_fetch_array($sqlLocationTo);
                        echo $rowLocationTo[0];
                        ?>
                    </td>
                    <td style="width:7%; font-size: 10px;">
                        <?php echo number_format($transferOrderDetail['TransferOrderDetail']['qty'], 0); ?>
                    </td>
                    <td style="width:12%; font-size: 10px;">
                        <?php
                        $uomId = $transferOrderDetail['TransferOrderDetail']['qty_uom_id'];
                        $query = mysql_query("SELECT abbr FROM uoms WHERE id=".$uomId." ORDER BY name ASC");
                        $row   = mysql_fetch_array($query);
                        echo $row[0];
                        ?>
                    </td>
                </tr>
            <?php
                }
            }
            ?>
            </table>
        </div>
        <br />
        <div style="clear:both;"></div>
        <div style="float:left;width: 450px">
            <div>
                <input type="button" value="<?php echo ACTION_PRINT; ?>" id='btnDisappearPrint' onClick='window.print();window.close();' class='noprint'>
            </div>
        </div>
        <div style="clear:both"></div>
    </div>
    <div style="position: fixed; bottom: 40px; width: 100%;" class="print-footer">
        <table style="width: 100%;">
            <tr>
                <td style="font-size: 10px; width: 33%;"><?php echo TABLE_ISSUED_BY;?>..............................</td>
                <td style="text-align: center; font-size: 10px; width: 34%;">Approved By..............................</td>
                <td style="text-align: center; font-size: 10px; width: 33%;"><?php echo TABLE_CHECKED_RECEIVE_BY; ?>..............................</td>
            </tr>
        </table>
    </div>
</div>
<div style="clear:both"></div>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(document).dblclick(function(){
            window.close();
        });
    });
</script>