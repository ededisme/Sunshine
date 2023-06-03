<?php
// Authentication
$this->element('check_access');
$allowAdd=checkAccess($user['User']['id'], $this->params['controller'], 'addXrayServiceDoctor');
?>
<?php $tblName = "tbl" . rand(); ?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<script type="text/javascript">
    var oTableXrayService;
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        $("#example td:first-child").addClass('first');
        oTableXrayService = $("#example").dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo $this->base.'/'.$this->params['controller']; ?>/cystoscopyServiceDoctorAjax/",
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                $("#example td:first-child").addClass('first');
                $("#example td:last-child").css("white-space", "nowrap");   
                
                $(".btnXrayServiceDoctor").click(function(event){
                    event.preventDefault();
                     var queueId = $(this).attr('rel');
                    var queueDoctorId = $(this).attr('queueDoctorId');
                    var leftPanel=$(this).parent().parent().parent().parent().parent().parent().parent();
                    var rightPanel=leftPanel.parent().find(".rightPanel");
                    leftPanel.hide("slide", { direction: "left" }, 500, function() {
                        rightPanel.show();
                    });
                    rightPanel.html("<?php echo ACTION_LOADING; ?>");
                    rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/addXrayServiceDoctor/" + queueDoctorId+"/"+queueId);
                });
                return sPre;
            },
            "aoColumnDefs": [{
                "sType": "numeric", "aTargets": [ 0 ],
                "bSortable": false, "aTargets": [ 0,-1 ]
            }]
        });
    });
</script>
<h1 class="title"><?php __(MENU_QUEUE);?></h1>
<div id="dynamic">
    <table id="example" class="table" cellspacing="0">
        <thead>
            <tr>
                <th class="first"><?php echo TABLE_NO; ?></th>
                <th><?php echo PATIENT_CODE; ?></th>
                <th><?php echo PATIENT_NAME; ?></th>                
                <th><?php echo TABLE_SEX; ?></th>
                <th><?php echo TABLE_TELEPHONE; ?></th>          
                <th><?php echo OTHER_REQUESTED_DATE; ?></th>
                <th><?php echo ACTION_ACTION; ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="7" class="dataTables_empty"><?php echo TABLE_LOADING; ?></td>
            </tr>
        </tbody>
    </table>
</div>
<div id="dialog" title=""></div>