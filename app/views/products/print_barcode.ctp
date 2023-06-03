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
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:10px;">
        <tr>
            <td style="text-align: center;"><u style="font-size: 20px; font-weight: bold;"><?php echo TABLE_PRODUCT_BARCODE;?></u></td>
        </tr>
    </table>
    <div style="width: 100%; height: 80%; text-align: center; margin-bottom: 10px;">
        <?php 
        $index = 1;
        $conditionPgroup = "";
        if($pgroupId != "all"){
            $conditionPgroup = " id IN (SELECT product_id FROM product_pgroups WHERE pgroup_id = ".$pgroupId.") AND ";
        }
        
        //Check User Print Product
        $queryUserPrintProduct = mysql_query("SELECT * FROM `user_print_product` WHERE user_id = ".$user['User']['id']."");
        if(mysql_num_rows($queryUserPrintProduct) > 0){
            $queryProdcut = mysql_query("SELECT (SELECT code FROM products WHERE id = product_id) AS code, (SELECT name FROM products WHERE id = product_id) AS name, (SELECT unit_cost FROM products WHERE id = product_id) AS unit_cost FROM user_print_product WHERE ".$conditionPgroup." user_id = ".$user['User']['id']."");
        }else{
            $queryProdcut = mysql_query("SELECT code, name, unit_cost FROM products WHERE ".$conditionPgroup." is_active = 1 ORDER BY code ASC");
        }
        
        $z = 1;
        while($rowProduct = mysql_fetch_array($queryProdcut)){
        ?>
            <?php 
                if($z == 7){
            ?>
            <div style="page-break-after:always;"></div>   
            <?php
                $z=1;
                }
            ?>
            <div style="width:196.535433071px; height: 105.826771654px; float: left; padding: 10px;">
                <table width="100%" cellpadding="0" cellspacing="0" style='border: 1px solid #000;'>
                    <tr>
                        <td style='text-align: center; padding: 5px;'>
                            <b><?php echo $rowProduct['name']; ?></b>
                        </td>
                    </tr>
                    <?php 
                        if($rowProduct['code'] != ""){
                    ?>
                    <tr>
                        <td style='text-align: center; padding-top: 0px;'>                            
                            <img class="barcode" alt="" src="<?php echo $this->webroot; ?>barcodegen.1d-php5.v2.2.0/generate_barcode.php?str=<?php echo $rowProduct['code']; ?>" style="border:0px; margin: 0px; width: 140px; height: 23px;padding-left: 2px;" />
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align: center; padding-top: 0px; padding-bottom: 5px; vertical-align: top;'>                            
                            <b><?php echo $rowProduct['code']; ?></b>
                        </td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        <?php 
        $z++;
        $index++;
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
            window.print();
            window.close();
        });
    });
</script>