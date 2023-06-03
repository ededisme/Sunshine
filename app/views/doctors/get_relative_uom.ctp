<?php
$query=mysql_query("SELECT id,name,abbr,1 AS conversion FROM uoms WHERE id=".$uomId."
                    UNION
                    SELECT id,name,abbr,(SELECT value FROM uom_conversions WHERE is_active=1 AND from_uom_id=".$uomId." AND to_uom_id=uoms.id) AS conversion FROM uoms WHERE id IN (SELECT to_uom_id FROM uom_conversions WHERE is_active=1 AND from_uom_id=".$uomId.")
                    ORDER BY conversion ASC");
$i = 1;
$length = mysql_num_rows($query);
while($data=mysql_fetch_array($query)){
    $selected = "";
    $priceLbl = "";
    if($data['id'] == $uomId){   
        $selected = ' selected="selected" ';
    }
    if(!empty($productId)){
        $sqlPrice = mysql_query("SELECT products.unit_cost, product_prices.price_type_id, product_prices.amount, product_prices.percent, product_prices.add_on, product_prices.set_type FROM product_prices INNER JOIN products ON products.id = product_prices.product_id WHERE product_prices.product_id =".$productId." AND product_prices.uom_id =".$data['id']);
        if(@mysql_num_rows($sqlPrice)){
            $price = 0;
            while($rowPrice = mysql_fetch_array($sqlPrice)){
                $unitCost = $rowPrice['unit_cost'] /  $data['conversion'];
                if($rowPrice['set_type'] == 1){
                    $price = $rowPrice['amount'];
                }else if($rowPrice['set_type'] == 2){
                    $percent = ($unitCost * $rowPrice['percent']) / 100;
                    $price = $unitCost + $percent;
                }else if($rowPrice['set_type'] == 3){
                    $price = $unitCost + $rowPrice['add_on'];
                }
                $priceLbl .= 'price-uom-'.$rowPrice['price_type_id'].'="'.$price.'" ';
            }
        }else{
            $priceLbl .= 'price-uom-1="0" price-uom-2="0"';
        }
    }else{
        $priceLbl .= 'price-uom-1="0" price-uom-2="0"';
    }
?>
<option <?php echo $priceLbl; ?> <?php echo $selected; ?>data-sm="<?php if($length == $i){ ?>1<?php }else{ ?>0<?php } ?>" data-item="<?php if($data['id'] == $uomId){ echo "first"; }else{ echo "other";} ?>" value="<?php echo $data['id']; ?>" conversion="<?php echo $data['conversion']; ?>"><?php echo $data['name']; ?></option>
<?php 
$i++;
} ?>