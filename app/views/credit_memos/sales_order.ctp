<?php $tblName = "tbl" . rand(); ?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(".table td:first-child").addClass('first');
        $("#<?php echo $tblName; ?>").dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo $this->base . '/' . $this->params['controller']; ?>/salesOrderAjax/<?php echo $companyId; ?>/<?php echo $branchId; ?>/<?php echo $customerId; ?>",
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                $("#dialog").dialog("option", "position", "center");
                $(".table td:first-child").addClass('first');
                $(".table td:last-child").css("white-space", "nowrap");
                $(".table tr").click(function(){
                    $(this).find("input[name='chkCMSalesOrder']").attr("checked", true);
                });
                return sPre;
            },
            "aoColumnDefs": [{
                "sType": "numeric", "aTargets": [ 0 ],
                "bSortable": false, "aTargets": [ 0,-1,-2 ]
            }],
            "aaSorting": [[ 2, "asc" ]]
        });
    });
</script>
<br />
<div id="dynamic">
    <table id="<?php echo $tblName; ?>" class="table" cellspacing="0">
        <thead>
            <tr>
                <th class="first"></th>
                <th><?php echo TABLE_INVOICE_DATE; ?></th>
                <th><?php echo TABLE_INVOICE_NO; ?></th>
                <th><?php echo PATIENT_CODE; ?></th>
                <th><?php echo PATIENT_NAME; ?></th>
                <th><?php echo TABLE_LOCATION_GROUP; ?></th>
                <th><?php echo TABLE_TOTAL_AMOUNT; ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="7" class="dataTables_empty"><?php echo TABLE_LOADING; ?></td>
            </tr>
        </tbody>
    </table>
</div>