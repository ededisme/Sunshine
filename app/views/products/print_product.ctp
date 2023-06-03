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
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="text-align: center;"><u style="font-size: 20px; font-weight: bold;"><?php echo TABLE_PRODUCT_INFORMATION;?></u></td>
        </tr>
    </table>
    <br/>
    
    <div style="width: 100%; height: 90%;">
        <?php 
        $index = 1;
        $conditionPgroup = "";
        if($pgroupId != "all"){
            $conditionPgroup = " id IN (SELECT product_id FROM product_pgroups WHERE pgroup_id = ".$pgroupId.") AND ";
        }
        
        //Check User Print Product
        $queryUserPrintProduct = mysql_query("SELECT * FROM `user_print_product` WHERE user_id = ".$user['User']['id']."");
        if(mysql_num_rows($queryUserPrintProduct) > 0){
            $queryProdcut = mysql_query("SELECT product_id AS id, (SELECT company_id FROM products WHERE id = product_id) AS company_id, (SELECT code FROM products WHERE id = product_id) AS code, (SELECT name FROM products WHERE id = product_id) AS name, (SELECT unit_cost FROM products WHERE id = product_id) AS unit_cost, (SELECT barcode FROM products WHERE id = product_id) AS barcode, (SELECT price_uom_id FROM products WHERE id = product_id) AS price_uom_id FROM user_print_product WHERE ".$conditionPgroup." user_id = ".$user['User']['id']."");
        }else{
            $queryProdcut = mysql_query("SELECT id, code, name, unit_cost, barcode, price_uom_id, company_id FROM products WHERE ".$conditionPgroup." is_active = 1 AND is_packet = 0 ORDER BY code ASC");
        }
        while($rowProduct = mysql_fetch_array($queryProdcut)){
        ?>
            <?php 
                if($rowProduct['code'] != "" || $rowProduct['barcode'] !=""){
            ?>
            <div style="width:340.15748px; height: 207.874016px; float: left; padding: 10px;">
                <table width="100%" cellpadding="0" cellspacing="0" style='background-color: #fff !important; border: 1px solid #000;'>
                    <tr>
                        <td style="width:60%; background-color: #fff;">
                            <table style="width:100%; text-align: center;">
                                <tr>
                                    <td>
                                        <img class="barcode" alt="" src="<?php echo $this->webroot; ?>barcodegen.1d-php5.v2.2.0/generate_barcode.php?str=<?php echo $rowProduct['code']; ?>" style="width: 200px; height: 80px;" />
                                    </td>
                                </tr>
                                <tr style="vertical-align:bottom; text-align: center;">
                                    <td style="text-align: center; font-size: 14px; font-weight: bold;">
                                        <?php echo $rowProduct['barcode'];?><br>
                                        <?php echo $rowProduct['name'];?>
                                    </td>
                                </tr>
                                
                            </table>
                        </td>
                        <td style="background-color: #fff; width:40%; text-align: center;" rowspan="3">
                            <b style="font-size:30px;">Price</b><br>
                            <span style="font-size:28px;">
                                <?php 
                                    //Check Price Type POS 
                                    if(!empty($priceType)){
                                        $sqlMainCurrency = mysql_query("SELECT symbol FROM currency_centers WHERE id = (SELECT currency_center_id FROM companies WHERE id = ".$rowProduct['company_id'].")");
                                        $rowMainCurrency = mysql_fetch_array($sqlMainCurrency);
                                        $query = mysql_query("SELECT id,name,abbr,1 AS conversion FROM uoms WHERE id=" . $rowProduct['price_uom_id'] . "");
                                        $j = 0;
                                        $data = mysql_fetch_array($query);
                                        $unitCost   = ($rowProduct['unit_cost'] / 1);
                                        $sql = "SELECT amount, percent, add_on, set_type FROM product_prices WHERE product_id=" . $rowProduct['id'] . " AND price_type_id=" . $priceType . " AND uom_id=" . $rowProduct['price_uom_id'];
                                        $qry = mysql_query($sql);
                                        if(mysql_num_rows($qry)){
                                            $row = mysql_fetch_array($qry);
                                            $oldAmt     = $row['amount'];
                                            $oldPercent = $row['percent'];
                                            $oldAddOn   = $row['add_on'];

                                            if($oldAmt > 0 || $oldPercent > 0 || $oldAddOn > 0){
                                                $setType = $row['set_type'];
                                            }else{
                                                $setType = 0;
                                            }

                                        }else{
                                            $oldAmt     = 0;
                                            $oldPercent = 0;
                                            $oldAddOn   = 0;
                                            $setType    = 0;
                                        }

                                        if($setType == 0 || $setType == 1){
                                            $unitPrice = $oldAmt;
                                        } else if($setType == 2){
                                            $percent   = ($unitCost * $oldPercent) / 100;
                                            $unitPrice = $unitCost + $percent;
                                        } else {
                                            $unitPrice = $unitCost + $oldAddOn;
                                        }
                                        
                                        $unitPriceLast = substr(number_format($unitPrice, 2), -1); 
                                        if($unitPriceLast == 0){
                                            echo substr(number_format($unitPrice, 2), 0, -1)." ".$rowMainCurrency[0];
                                        }else{
                                            echo number_format($unitPrice, 2)." ".$rowMainCurrency[0];
                                        }
                                    }
                                ?>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        <?php 
                $index++;
            }
        }
        ?>
    </div>
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
            $.ajax({
                type: "POST",
                url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printProductByCheck/clearData",
                beforeSend: function() {
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(result) {
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                }
            });
            window.print();
            window.close();
        });
    });
</script>