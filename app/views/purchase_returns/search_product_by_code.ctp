<?php
include("includes/function.php");
$dateNow = date("Y-m-d");
if(!empty($product)){
    // Get Unit Cost
    $price   = $product['Product']['unit_cost'];
    $totalStock = $product[0]['total_qty'];
    $totalOrder = 0;
    if(strtotime($order_date) < strtotime($dateNow) ){
        $sqlCurrentStock = mysql_query("SELECT SUM(total_qty - total_order) AS total_qty FROM ".$location_id."_inventory_totals WHERE product_id = ".$product['Product']['id']." AND expired_date = '".$expiryDate."';");
        if(mysql_num_rows($sqlCurrentStock)){
            $rowCurrentStock = mysql_fetch_array($sqlCurrentStock);
            if($rowCurrentStock[0] < $totalStock){
                $totalStock = $rowCurrentStock[0];
            }
        } else {
            $totalStock = 0;
        }
    }
    if(!empty($brId)){
        $sql = mysql_query("SELECT sum(sor.qty) as total_order FROM `stock_orders` as sor WHERE sor.product_id = ".$product['Product']['id']." AND sor.purchase_return_id = ".$brId." AND sor.location_id = ".$location_id." AND sor.expired_date = '".$expiryDate."' AND sor.date = '".$order_date."' GROUP BY sor.product_id");
        if(mysql_num_rows($sql)){
            $rowOrder   = mysql_fetch_array($sql);
            $totalOrder = $rowOrder['total_order'];
        }
    }
    if($expiryDate != '' && $expiryDate != '0000-00-00'){
        $expiryDate = dateShort($expiryDate);
    } else {
        $expiryDate = '';
    }
    $productId = $product['Product']['id'];
    $productCode = htmlspecialchars($product['Product']['code'], ENT_QUOTES, 'UTF-8');
    $productName = htmlspecialchars($product['Product']['name'], ENT_QUOTES, 'UTF-8');
    $productSmallVal = $product['Product']['small_val_uom'];
    $productUomId    = $product['Product']['price_uom_id'];
    $productIsExp    = $product['Product']['is_expired_date'];
    $productCost     = $product['Product']['unit_cost'];
} else {
    $totalStock = 0;
    $totalOrder = 0;
    $productId  = 0;
    $productCode = '';
    $productName = '';
    $productSmallVal = 0;
    $productUomId = 0;
    $productIsExp = 0;
    $expiryDate = '';
    $productCost = 0;
}
?>
<input type="hidden" value="<?php echo ($totalStock + $totalOrder); ?>" id="qtyOfProduct" />
<input type="hidden" id="purchaseReturnProductCode" value="<?php echo $productCode; ?>" />
<input type="hidden" id="purchaseReturnProductName" value="<?php echo $productName; ?>" />
<input type="hidden" id="purchaseReturnProductId" value="<?php echo $productId; ?>" />
<input type="hidden" id="purchaseReturnProductSmallValUom" value="<?php echo $productSmallVal; ?>" />
<input type="hidden" id="purchaseReturnProductPriceUomId" value="<?php echo $productUomId; ?>" />
<input type="hidden" id="purchaseReturnProductIsExpiry" value="<?php echo $productIsExp; ?>" />
<input type="hidden" id="purchaseReturnProductExpiryDate" value="<?php echo $expiryDate; ?>" />
<input type="hidden" id="purchaseReturnProductInventoryTotal" value="<?php echo ($totalStock + $totalOrder); ?>" />
<input type="hidden" id="purchaseReturnProductPrice" value="<?php echo number_format($productCost, 2); ?>" />