<?php 
    // Function
    include('includes/function.php');
    $tblNameRadom = "tbl" . rand();
?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<script type="text/javascript">
    var oTableViewProductReorderLevel;
    $(document).ready(function(){
        $("#refreshProductReorderLevel").unbind("click").click(function(){
            var Tablesetting = oTableViewProductReorderLevel.fnSettings();
            $("#refreshProductReorderLevel").hide();
            $("#loadingProductReorderLevel").show();
            $("#adjIssuedView").html("Loading....");
           
            Tablesetting.sAjaxSource = "<?php echo $this->base . '/' . $this->params['controller']; ?>/viewProductReorderLevelAjax";
            setTimeout(function() {
                $("#refreshProductReorderLevel").show();
                $("#loadingProductReorderLevel").hide();
            }, 2000);
            oCache.iCacheLower = -1;
            oTableViewProductReorderLevel.fnDraw(false);
        });
        
        // Prevent Key Enter
        preventKeyEnter();
        $("#labelProductReorderLevel<?php echo $tblNameRadom;?> td:first-child").addClass('first');
        oTableViewProductReorderLevel = $("#labelProductReorderLevel<?php echo $tblNameRadom;?>").dataTable({
            "aLengthMenu": [[10, 25, 50, 100, 1000000], [10, 25, 50, 100, "All"]],
            "iDisplayLength": 10,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo $this->base . '/' . $this->params['controller']; ?>/viewProductReorderLevelAjax",
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function(oSettings, iStart, iEnd, iMax, iTotal, sPre) {     
                $("#labelProductReorderLevel<?php echo $tblNameRadom;?> td:first-child").addClass('first');
                $("#labelProductReorderLevel<?php echo $tblNameRadom;?> td:last-child").css("white-space", "nowrap");
                return sPre;
            },
            "fnDrawCallback": function(oSettings, json) {
                $("#labelProductReorderLevel<?php echo $tblNameRadom;?> .colspanParent").parent().attr("colspan", 5);
                $("#labelProductReorderLevel<?php echo $tblNameRadom;?> .colspanParent").parent().next().remove();
                $("#labelProductReorderLevel<?php echo $tblNameRadom;?> .colspanParent").parent().next().remove();
                $("#labelProductReorderLevel<?php echo $tblNameRadom;?> .colspanParent").parent().next().remove();
                $("#labelProductReorderLevel<?php echo $tblNameRadom;?> .colspanParent").parent().next().remove();
            },
            "aoColumnDefs": [{
                    "sType": "numeric", "aTargets": [0],
                    "bSortable": false, "aTargets": [-1]
                }],
            "aaSorting": [[5, "asc"], [0, "asc"]]
        });
    });
</script>
<table cellpadding="5" class="table tblAlert" style="margin-bottom: 10px;" id="labelProductReorderLevel<?php echo $tblNameRadom;?>">
    <thead>
        <tr>
            <th class="first"><?php echo TABLE_NO; ?></th>
            <th style="width: 200px !important;"><?php echo TABLE_CODE; ?></th>
            <th><?php echo TABLE_NAME; ?></th>
            <th style="width: 150px !important;"><?php echo TABLE_QTY_IN_STOCK; ?></th>
            <th style="width: 150px !important;"><?php echo GENERAL_REORDER_LEVEL; ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="9" class="dataTables_empty"><?php echo TABLE_LOADING; ?></td>
        </tr>
    </tbody>
</table>