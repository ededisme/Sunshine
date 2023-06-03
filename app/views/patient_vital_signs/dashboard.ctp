<?php $absolute_url  = FULL_BASE_URL . Router::url("/", false); ?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(".table td:first-child").addClass('first');
        /**
         * This script use for display queue list.
         */
        var oTableQueue = $("#queueList").dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo $absolute_url.$this->params['controller']; ?>/dashboardPatientQueueAjax/",
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                $(".table td:first-child").addClass('first');                
                return sPre;
            },
            "aoColumnDefs": [
                { "sType": "numeric", "aTargets": [ 0 ] }
            ]
        });
        setInterval(function() {
            oCache.iCacheLower = -1;
            oTableQueue.fnDraw(false);
        },60*1000);
    });
</script>
<h1 class="title"><?php __(MENU_QUEUE);?></h1>
<div id="dynamic">
    <table id="queueList" class="table" cellspacing="0">
        <thead>
            <tr>
                <th class="first"><?php echo TABLE_NO; ?></th>
                <th><?php echo PATIENT_CODE; ?></th>
                <th><?php echo PATIENT_NAME; ?></th>                
                <th><?php echo TABLE_SEX; ?></th>
                <th><?php echo TABLE_DOB; ?></th>
                <th><?php echo TABLE_TELEPHONE; ?></th>
                <th><?php echo CONSULT_CONSULTATION; ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="8" class="dataTables_empty"><?php echo TABLE_LOADING; ?></td>
            </tr>
        </tbody>
    </table>
</div>
<div id="dialog" title=""></div>