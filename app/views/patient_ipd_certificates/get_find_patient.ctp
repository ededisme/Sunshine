<?php $absolute_url  = FULL_BASE_URL . Router::url("/", false); ?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<?php $tblName = "tbl" . rand(); ?>
<script type="text/javascript">
    $(document).ready(function(){
        $(".table td:first-child").addClass('first');
        $("#<?php echo $tblName; ?>").dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo $absolute_url.$this->params['controller']; ?>/getFindPatientAjax/",
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                $(".table td:first-child").addClass('first');                
                $("#dialog").dialog("option", "position", "center");
                $(".table td:first-child").addClass('first');
                $(".table td:last-child").css("white-space", "nowrap");
                $(".table tr").click(function(){
                    $(this).find("input[name='chkPatient']").attr("checked", true);
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

<div id="dynamic">
    <table id="<?php echo $tblName; ?>" class="table" cellspacing="0">
        <thead>
            <tr>
                <th class="first"></th>
                <th><?php echo PATIENT_CODE; ?></th>
                <th><?php echo PATIENT_NAME; ?></th>
                <th><?php echo TABLE_SEX; ?></th>
                <th><?php echo TABLE_DOB; ?></th>
                <th><?php echo MENU_OTHER; ?></th> 
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="6" class="dataTables_empty"><?php echo TABLE_LOADING; ?></td>
            </tr>
        </tbody>
    </table>
</div>