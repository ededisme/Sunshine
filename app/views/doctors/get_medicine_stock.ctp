<?php $absolute_url = FULL_BASE_URL . Router::url("/", false); ?>
<?php echo $javascript->link('jquery.form'); ?>


<script type="text/javascript">
    $(document).ready(function(){
        $("#TreatmentAddForm").validationEngine();
        $('.addToList').click(function(event){
            $(this).hide();
            $("#btnSubmit").show();
            event.preventDefault();
            $('#noData').hide();
            var id = $(this).attr('rel');
            if(!(in_array(idList,id))){
                index++;
                idList.push(id);
                var medicines = $(this).attr('name').split("|||");                
                var typeMedicine = medicines[5];
                if(typeMedicine==1){                    
                    var type = '<?php echo GENERAL_HOSPITAL ?>';
                }else{
                    var type = '<?php echo GENERAL_OUTSIDE ?>';
                }
                
                $('#medicineRequest').append("<tr id='child"+index+"'>" +
                    "<td class='first'></td>" +
                    "<td>" +
                    "<input type='hidden' name='data[Treatment][" + index + "][discount]' value='"+ medicines[4] +"' />" +
                    "<input type='hidden' name='data[Treatment][" + index + "][amount_in_stock]' value='" + medicines[1] + "' />" +
                    "<input type='hidden' name='data[Treatment][" + index + "][price_sale_out]' value='" + medicines[2] + "' />" +
                    "<input type='hidden' name='data[Treatment][" + index + "][sale_stock_id]' value='" + id + "' />" +
                    medicines[0] +
                    "</td>" +                    
                    "<td class='amount_in_stock'>" + medicines[1] +medicines[6]+ "</td>" +
                    "<td class='medicine_type'>" + medicines[3] + "</td>" +
                    "<td class='medicine_type'>" + type + "</td>" +
                    "<td><input type='text' id='qty" + index + "' name='data[Treatment][" + index + "][qty]' class='qty validate[required,custom[integer],min[1],funcCall[checkRequest]]' style='width:40px;' /></td>" +
                    "<td><input type='text' name='data[Treatment][" + index + "][num_day]' style='width:60px;' /></td>" +     
                    "<td><input type='text' name='data[Treatment][" + index + "][morning]' style='width:60px;' /></td>" +
                    "<td><input type='text' name='data[Treatment][" + index + "][afternoon]' style='width:60px;' /></td>" +
                    "<td><input type='text' name='data[Treatment][" + index + "][evening]' style='width:60px;' /></td>" +
                    "<td><input type='text' name='data[Treatment][" + index + "][night]' style='width:60px;' /></td>" +
                    "<td><input type='text' name='data[Treatment][" + index + "][note]' style='width:120px;' /></td>" +
                    "<td>" +
                    "<a href='' rel='" + id + "' id='" + index + "' onmouseover=Tip('<?php echo ACTION_DELETE; ?>') onclick='javascript: removeRow(" + index + "," + id + "); return false;'>" +
                    "<img src=<?php echo $this->webroot . 'img/action/delete.png'; ?> />" +
                    "</a>" +
                    "</td>" +
                    "</tr>");                
                               
                $("#medicineRequest td.first").each(function(record){
                    $(this).html(record);
                });                                 
            }
            $("#TreatmentAddForm").validationEngine('detach');
            $("#TreatmentAddForm").validationEngine('attach');
            $("#TreatmentAddForm").ajaxForm({
                beforeSubmit: function(arr, $form, options) {
                    $(".loading").show();
                },
                success: function(result) {
                    $("#tabs").tabs("select", 7);
                    $("#tabPharmaNum").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabPharmaNum/<?php echo $this->params['pass'][0]; ?>");
                }
            });
        });
    });
  
    function removeRow(inc,id){
        var elm = document.getElementById('child'+inc);
        elm.parentNode.removeChild(elm);
        $("#medicineRequest td.first").each(function(record){
            $(this).html(record);
        });
        removeByElement(idList,id);
        if(idList.length == 0){
            $("#btnSubmit").hide();
            $('#noData').show();
            $("#medicineRequest td.first").html("<?php echo TABLE_NO_MATCHING_RECORD; ?>");
        }
    }
    function in_array(arr,obj){
        for(var i=0; i<arr.length; i++) {
            if (arr[i] == obj) return true;
        }
    }
    function removeByElement(arrayName,arrayElement){
        for(var i=0; i<arrayName.length;i++ ){
            if(arrayName[i]==arrayElement)
                arrayName.splice(i,1);
        }
    }
    function checkRequest(field, rules, i, options){        
        var amountInStock = Number(field.closest("tr").find("td.amount_in_stock").text());
        var amountRequest = Number(field.closest("tr").find(".qty").val());        
        if(amountRequest > amountInStock){
            return "<?php echo MESSAGE_OUT_OF_STOCK; ?>";
        }
    }
</script>
<?php $i = 1; ?>
<table class="table" cellspacing="0">
    <tr>
        <th class="first"><?php echo TABLE_NO; ?></th>
        <th><?php echo DRUG_COMMERCIAL_NAME; ?></th>
        <th><?php echo GENERAL_TYPE; ?></th>
        <th><?php echo DRUG_MADE_IN; ?></th>            
        <th><?php echo DRUG_ORIGIN; ?></th>
        <th><?php echo GENERAL_DISCOUNT; ?></th>
        <th><?php echo DRUG_EXPIRED_DATE; ?></th>
        <th><?php echo DRUG_AMOUNT_IN_STOCK; ?></th>
        <th><?php echo ACTION_ACTION; ?></th>
    </tr>
    <?php if (!empty($medicineStocks)) {
 ?>
<?php foreach ($medicineStocks as $medicineStock) { ?>


            <?php
            $type = "";
            $totalStock = 0;
            $queryStok = mysql_query("SELECT sum(amount) AS total FROM stock_on_hands WHERE sale_stock_id=" . $medicineStock['SaleStock']['id']);
            $resultStock = mysql_fetch_array($queryStok);
            $totalStock = $resultStock['total'];
            $amountStock = $medicineStock['SaleStock']['amount'] - $totalStock;
  
            if ($medicineStock['MedicineUnit']['medicine_type'] == "Injection") {
                if ($medicineStock['MedicineUnit']['flacont'] == 0) {
                    $type = " " . MEASURE_AMPOULE;
                } else {
                    $type = " " . MEASURE_FLACON;
                }
            } else if ($medicineStock['MedicineUnit']['medicine_type'] == "Tablet") {
                $type = " " . MEASURE_TABLET;
            } else if ($medicineStock['MedicineUnit']['medicine_type'] == "Capsule") {
                $type = " " . MEASURE_CAPSULE;
            } else if ($medicineStock['MedicineUnit']['medicine_type'] == "Powder") {
                $type = " " . MEASURE_POWDER;
            } else if ($medicineStock['MedicineUnit']['medicine_type'] == "Syrup") {
                $type = " " . MEASURE_WATER;
            } else if ($medicineStock['MedicineUnit']['medicine_type'] == "Cream") {
                $type = " tubes";
            } else if ($medicineStock['MedicineUnit']['medicine_type'] == "jel") {
                $type = " tubes";
            } else if ($medicineStock['MedicineUnit']['medicine_type'] == "form") {
                $type = " tubes";
            } else if ($medicineStock['MedicineUnit']['medicine_type'] == "cleaningBar") {
                $type = " tubes";
            } else if ($medicineStock['MedicineUnit']['medicine_type'] == "sprite") {
                $type = " tubes";
            } else if ($medicineStock['MedicineUnit']['medicine_type'] == "ointment") {
                $type = " tubes";
            } else if ($medicineStock['MedicineUnit']['medicine_type'] == "shampoo") {
                $type = " tubes";
            } else if ($medicineStock['MedicineUnit']['medicine_type'] == "lotion") {
                $type = " tubes";
            } else if ($medicineStock['MedicineUnit']['medicine_type'] == "stick") {
                $type = " tubes";
            } else if ($medicineStock['MedicineUnit']['medicine_type'] == "Liquid") {
                $type = " amp";
            } else if ($medicineStock['MedicineUnit']['medicine_type'] == "Other") {
                if ($medicineStock['MedicineUnit']['flacont'] == 0) {
                    $type = " no";
                } else {
                    $type = " fla";
                }
            } else if ($medicineStock['GrandStock']['medicine_type'] == "Suppository") {
                if ($medicineStock['MedicineUnit']['suppository'] == 0) {
                    $type = " unidoses";
                } else {
                    $type = " sup";
                }
            } ?>

            <tr class="addToList" style="cursor: pointer;" onmouseover="Tip('<?php echo ACTION_ADD; ?>')" rel="<?php echo $medicineStock['SaleStock']['id']; ?>" name="<?php
            echo $medicineStock['GrandStock']['commercial_name'] . '|||' .
            $amountStock . '|||' .
            $medicineStock['GrandStock']['price_sale_out'] . '|||' .
            $medicineStock['MedicineUnit']['medicine_type'] . '|||' .
            $medicineStock['GrandStock']['discount'] . '|||' .
            $medicineStock['GrandStock']['type_medicine']. '|||' .$type
            ?>">
            <td class="first"><?php echo $i++; ?></td>
            <td><?php echo $medicineStock['GrandStock']['commercial_name']; ?></td>
            <td><?php echo $medicineStock['MedicineUnit']['medicine_type']; ?></td>
            <td><?php echo $medicineStock['Country']['name']; ?></td>
        <td><?php
            if ($medicineStock['GrandStock']['type_medicine'] == 1) {
                echo $typeMedicine = GENERAL_HOSPITAL;
            } else {
                echo $typeMedicine = GENERAL_OUTSIDE;
            }
            ?>
        </td>
        <td><?php echo $discount = number_format($medicineStock['GrandStock']['discount'], 2) ?>%</td>
        <td>
            <?php
            $query_expired_date = mysql_query("SELECT expired_date FROM sale_stock_details WHERE sale_stock_id=" . $medicineStock['SaleStock']['id'] . " ORDER BY id DESC LIMIT 1");
            $data_expired_date = mysql_fetch_array($query_expired_date);
            echo $data_expired_date['expired_date'];
            ?>
        </td>
        <td>

            <?php echo $amountStock ?>
            <?php
            echo $type;
            ?>
        </td>
        <td>
            <a href="" onmouseover="Tip('<?php echo ACTION_ADD; ?>')"
               class="addToList" rel="<?php echo $medicineStock['SaleStock']['id']; ?>"
               name="<?php
               echo $medicineStock['GrandStock']['commercial_name'] . '|||' .
               $amountStock . '|||' .
               $medicineStock['GrandStock']['price_sale_out'] . '|||' .
               $medicineStock['MedicineUnit']['medicine_type'] . '|||' .
               $medicineStock['GrandStock']['discount'] . '|||' .
               $medicineStock['GrandStock']['type_medicine'] . '|||' . $type;
            ?>">
<?php echo $html->image("action/add.png", array('alt' => 'Add to list')); ?>
            </a>
        </td>
    </tr>
<?php } ?>
<?php } else { ?>
           <tr>
               <td class="first" colspan="7" style="text-align: center;"><?php echo TABLE_NO_RECORD; ?></td>
           </tr>
<?php } ?>
</table>