<style type="text/css" media="screen">
    .titleHeader{
        vertical-align: top; 
        padding-bottom: 0px !important; 
        padding-top: 0px !important;
        padding-right: 2px !important;
        font-size: 10px;
        text-align: center;
    }
    .titleContent{
        font-weight: bold;
        text-align: right;
    }
    .contentHeight{
        height: 14px !important;
    }
    .marginTop10{
        padding-top: 10px !important;
    }
    .titleHeaderTable{
        padding-bottom: 0px !important; 
        padding-top: 0px !important;
        text-transform: uppercase; 
        font-size: 10px;
        color: #000;
    }
    .titleHeaderHeight{
        height: 20px !important;
    }
</style>
<style type="text/css" media="print">
    .titleHeader{
        vertical-align: top; 
        padding-bottom: 0px !important; 
        padding-top: 0px !important;
        padding-right: 2px !important;
        font-size: 10px;
        text-align: center;
    }
    .titleContent{
        font-weight: bold;
        text-align: right;
    }
    .contentHeight{
        height: 14px !important;
    }
    .marginTop10{
        padding-top: 10px !important;
    }
    .titleHeaderTable{
        padding-bottom: 0px !important; 
        padding-top: 0px !important;
        text-transform: uppercase; 
        font-size: 10px;
        color: #000;
    }
    .titleHeaderHeight{
        height: 20px !important;
    }
    div.print_doc { width:100%;}
    #btnDisappearPrint { display: none;}
    div.print-footer {display: block; width: 100%; position: fixed; bottom: 2px; font-size: 10px; text-align: center;} 
</style>
<div class="print_doc">
    <?php
    include("includes/function.php");
    ?>
    <?php
    for ($count = 1; $count <= 1; $count++) {
        $display = "style='display: none;'";
        if ($count == 1) {
            $display = "";
        }
    ?>
        <div style="height: 20px"></div>
        <table width="100%" cellpadding="0" cellspacing="0" class="bacode" <?php echo $display; ?>>
            <?php
            $queryPriceUomId = mysql_query("SELECT price_uom_id, unit_cost FROM products WHERE id = '" . $product['Product']['id'] . "'");
            $dataPriceUomId = mysql_fetch_array($queryPriceUomId);
            $query = mysql_query("SELECT id,name,abbr,1 AS conversion FROM uoms WHERE id=" . $dataPriceUomId[0] . "
                                             UNION
                                            SELECT id,name,abbr,(SELECT value FROM uom_conversions WHERE is_active=1 AND from_uom_id=" . $dataPriceUomId[0] . " AND to_uom_id=uoms.id) AS conversion FROM uoms WHERE id IN (SELECT to_uom_id FROM uom_conversions WHERE is_active=1 AND from_uom_id=" . $dataPriceUomId[0] . ")
                                            ORDER BY conversion ASC");
            $j = 0;
            while ($data = mysql_fetch_array($query)) {
                $unitCost = ($dataPriceUomId[1] / $data['conversion']);
                $sql = "SELECT amount, percent, add_on, set_type FROM product_prices WHERE product_id=" . $product['Product']['id'] . " AND price_type_id = ".$priceType." AND uom_id=" . $data['id'];
                $qry = mysql_query($sql);
                if (mysql_num_rows($qry)) {
                    $re_row = mysql_fetch_array($qry);
                    $oldAmt = $re_row['amount'];
                    $oldPercent = $re_row['percent'];
                    $oldAddOn = $re_row['add_on'];
                    $setType = $re_row['set_type'];
                } else {
                    $oldAmt = 0;
                    $oldPercent = 0;
                    $oldAddOn = 0;
                    $setType = 1;
                }
                if ($setType == 1) {
                    $unitPrice = $oldAmt;
                } else if ($setType == 2) {
                    $percent = ($unitCost * $oldPercent) / 100;
                    $unitPrice = $unitCost + $percent;
                } else {
                    $unitPrice = $unitCost + $oldAddOn;
                }
            }
            ?>
            <tr>
                <td class="titleHeader">
                    <?php echo $product['Product']['name'];//echo "Price: ៛" . number_format($unitPrice, 2); ?>
                </td>
            </tr>
            <tr>
                <td class="titleHeader">
                    <img class="barcode" alt="" src="<?php echo $this->webroot; ?>barcodegen.1d-php5.v2.2.0/generate_barcode.php?str=<?php echo $product['Product']['code']; ?>" style="border:0px; margin: 0px; width: 140px; height: 23px;padding-left: 2px;" />
                </td>
            </tr>
            <tr>
                <td class="titleHeader">
                    <?php echo $product['Product']['code']; ?>
                </td>
            </tr>
        </table>

        <div style="page-break-after:always;"></div> 
        <?php
    }
    ?>
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td class="titleHeader">
                <input type="button" value="<?php echo ACTION_PRINT; ?>" id='btnDisappearPrint' class='noprint'>
            </td>
        </tr>
    </table>
</div>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(document).dblclick(function() {
            window.close();
        });
        $("#btnDisappearPrint").click(function() {
            $("#footerPrint").show();
            $(".bacode").show();
            window.print();
            window.close();
        });
    });
</script>