<?php $absolute_url  = FULL_BASE_URL . Router::url("/", false); ?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<?php $tblName = "tbl" . rand(); ?>
<script type="text/javascript">
    var oTablePatientHistory;
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        $("#<?php echo $tblName; ?> td:first-child").addClass('first');
        oTablePatientHistory = $("#<?php echo $tblName; ?>").dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo $this->base.'/'.$this->params['controller']; ?>/patientAjax/",
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                $("#<?php echo $tblName; ?> td:first-child").addClass('first');
                $("#<?php echo $tblName; ?> td:last-child").css("white-space", "nowrap");           
                $(".btnViewPatientHistory").click(function(event){
                 
                    event.preventDefault();
                    var name = $(this).attr('name');
                    var queueId = $(this).attr('rel');
                    var queueDoctorId = $(this).attr('queueDoctorId');
                    var patientId = $(this).attr('patientID');
                    var leftPanel=$(this).parent().parent().parent().parent().parent().parent().parent();
                    var rightPanel=leftPanel.parent().find(".rightPanel");
                    leftPanel.hide("slide", { direction: "left" }, 500, function() {
                        rightPanel.show();
                    });
                    rightPanel.html("<?php echo ACTION_LOADING; ?>");
                    rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/view/" + queueDoctorId + "/" + queueId + "/" + patientId);
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
<div class="leftPanel">   
    <div id="dynamic">
        <table id="<?php echo $tblName; ?>" class="table" cellspacing="0">
            <thead>
                <tr>
                    <th class="first"><?php echo TABLE_NO; ?></th> 
                    <th><?php echo PATIENT_CODE; ?></th>
                    <th><?php echo PATIENT_NAME; ?></th>
                    <th><?php echo TABLE_SEX; ?></th>   
                    <th><?php echo TABLE_DOB; ?></th> 
                    <th><?php echo TABLE_TELEPHONE; ?></th>
                    <th><?php echo TABLE_PATIENT_TYPE; ?></th>  
                    <th><?php echo ACTION_ACTION; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="8" class="dataTables_empty"><?php echo TABLE_LOADING; ?></td>
                </tr>
            </tbody>
        </table>
    </div>    
</div>
<div class="rightPanel"></div>