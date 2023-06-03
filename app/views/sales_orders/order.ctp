<?php
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
$tblName = "tbl" . rand(); 
$rand    = rand();

?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    var product_type = $('#product_type').val();
    $(".table td:first-child").addClass('first');
    var invoiceTable<?php echo $rand; ?> = $("#<?php echo $tblName; ?>").dataTable({
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": "<?php echo $this->base . '/' . $this->params['controller']; ?>/orderAjax/<?php echo $companyId; ?>/ <?php echo $branchId; ?>/"+ product_type +"/<?php echo $customerId; ?>?sale_id=<?php echo $saleId; ?>",
        "fnServerData": fnDataTablesPipeline,
        "fnInfoCallback": function(oSettings, iStart, iEnd, iMax, iTotal, sPre) {
            $("#dialog").dialog("option", "position", "center");
            $(".table td:first-child").addClass('first');
            $(".table td:last-child").css("white-space", "nowrap");
            $(".table tr").click(function() {
                $(this).find("input[name='chkOrder']").attr("checked", true);
            });
            return sPre;
        },
        "aoColumnDefs": [{
            "sType": "numeric",
            "aTargets": [0],
            "bSortable": false,
            "aTargets": [0, -1, -2]
        }],
        "aaSorting": [
            [1, "desc"]
        ]
    });
    $('#product_type').change(function(){
        var pType = $(this).val();
        var Tablesetting = invoiceTable<?php echo $rand; ?>.fnSettings();
            Tablesetting.sAjaxSource =  "<?php echo $this->base . '/' . $this->params['controller']; ?>/orderAjax/<?php echo $companyId; ?>/ <?php echo $branchId; ?>/"+ pType +"/<?php echo $customerId; ?>?sale_id=<?php echo $saleId; ?>";
            oCache.iCacheLower = -1;
            invoiceTable<?php echo $rand; ?>.fnDraw(false);
    });
});
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb; margin-bottom: 5px;height:20px;">
    <div style="float:right;">
        Product type:
        <select id="product_type" style="width:150px;">
            <option value=""><?php echo TABLE_ALL; ?></option>
            <?php
            $queryPtype = mysql_query("SELECT * FROM `product_types` WHERE is_active=1");
            while($dataPtype=mysql_fetch_array($queryPtype)){
            ?>
            <option value="<?php echo $dataPtype['id']; ?>"><?php echo $dataPtype['name']; ?></option>
            <?php
            }
            ?>
        </select>
    </div>
</div>
<div style="clear: both;"></div>
</br>
<div id="dynamic">
    <table id="<?php echo $tblName; ?>" class="table" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th style="width: 5%;" class="first"></th>
                <th style="width: 20%;"><?php echo TABLE_PRESCRIPTION_NUMBER; ?></th>
                <th style="width: 20%;"><?php echo TABLE_DATE; ?></th>
                <th style="width: 20%;"><?php echo PATIENT_CODE; ?></th>
                <th style="width: 35%;"><?php echo PATIENT_NAME; ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="6" class="dataTables_empty first"><?php echo TABLE_LOADING; ?></td>
            </tr>
        </tbody>
    </table>
</div>