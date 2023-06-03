
<style type="text/css">
    li{
        list-style: none;
        margin-bottom: 10px;
    }
</style>
<script type="text/javascript">
    $(document).ready(function(){
        $(".rowDiscountPb").click(function(){
            $(".rowDiscountPb").css("backgroundColor", "white").find("input[type=radio]").attr("checked", false);
            $(this).css("backgroundColor", "#F8F8F8").find("input[type=radio]").attr("checked", true);
        });
        
    });
</script>
<div>
    <h1><?php echo MENU_DISCOUNT_INFORMATION ?> </h1>
</div>
<div style="overflow: auto; width: 100%; padding: 0px; margin: 0px; " >
    <table class="table" style="" cellspacing="0" >
        <thead>
            <tr>
                <th class="first" style="width:8%"></th>
                <th style="width:23%"><?php echo TABLE_NAME; ?></th>
                <th style="width:23%"><?php echo GENERAL_DESCRIPTION; ?></th>
                <th style="width:23%"><?php echo TABLE_PERCENT; ?> (%)</th>
                <th style="width:23%"><?php echo GENERAL_AMOUNT; ?> <?php echo TABLE_CURRENCY_DEFAULT; ?></th>
            </tr>
        </thead>
        <tbody style="width: 774px; ">
            <?php
            $i = 0;
            foreach ($discounts as $discount) {
                $i++;
            ?>
                <tr class="<?php if ($i == 1) { echo "first-row"; } ?> rowDiscountPb" >
                    <td class="first" style="width:8%; <?php if ($i == 1) { echo 'border-top:1px solid #C1DAD7;'; } ?>">
                    <input type="radio" name="chkDiscountPb" value="<?php echo $discount['Discount']['id']; ?>" />
                    <input type="hidden" name="PbDiscountAmount" value="<?php echo $discount['Discount']['amount']; ?>" />
                    <input type="hidden" name="PbDiscountPercent" value="<?php echo $discount['Discount']['percent']; ?>" />
                    </td>
                    <td style="width:23%; <?php if ($i == 1) { echo 'border-top:1px solid #C1DAD7;'; } ?> "><?php echo $discount['Discount']['name']; ?></td>
                    <td style="width:23%; <?php if ($i == 1) { echo 'border-top:1px solid #C1DAD7;'; } ?>"><?php echo $discount['Discount']['description']; ?></td>
                    <td style="width:23%; <?php if ($i == 1) { echo 'border-top:1px solid #C1DAD7;'; } ?>"><?php echo $discount['Discount']['percent']; ?></td>
                    <td style="width:23%; <?php if ($i == 1) { echo 'border-top:1px solid #C1DAD7;'; } ?>"><?php echo $discount['Discount']['amount']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
