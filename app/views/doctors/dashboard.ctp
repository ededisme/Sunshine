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
        /**
         * This script use for display appointment list.
         */
        var oTableAppointment = $("#appointmentList").dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo $absolute_url.$this->params['controller']; ?>/dashboardPatientFollowupAjax/",
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                $(".table td:first-child").addClass('first');
                $(".btnCancel").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var name = $(this).attr('title');
                    $("#dialog").dialog('option', 'title', 'Cancel Appointment');
                    $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Are you sure you want to cancel appointment with ' + name + '?</p>');
                    $("#dialog").dialog({
                        title: 'Cancel Appointment',
			resizable: false,
			modal: true,
                        width: 'auto',
                        height: 'auto',
			buttons: {
                            OK: function() {
                                $.ajax({
                                    type: "GET",
                                    url: "<?php echo $this->base.'/appointments'; ?>/cancelAppointment/" + id,
                                    data: "",
                                    beforeSend: function(){
                                        $("#dialog").dialog("close");
                                        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                                    },
                                    success: function(result){
                                        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                        oCache.iCacheLower = -1;
                                        oTableAppointment.fnDraw(false);
                                        // alert message
                                        $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result+'</p>');
                                        $("#dialog").dialog({
                                            title: '<?php echo DIALOG_INFORMATION; ?>',
                                            resizable: false,
                                            modal: true,
                                            width: 'auto',
                                            height: 'auto',
                                            buttons: {
                                                '<?php echo ACTION_CLOSE; ?>': function() {
                                                    $(this).dialog("close");
                                                }
                                            }
                                        });
                                    }
                                });                                  
                            },
                            Cancel: function() {
                                $( this ).dialog( "close" );
                            }
			}
                    });
                });
                return sPre;
            },
            "aoColumnDefs": [{
                "sType": "numeric", "aTargets": [ 0 ],
                "bSortable": false, "aTargets": [ 0,-1 ]
            }]
        });
        setInterval(function() {
            oCache.iCacheLower = -1;
            oTableAppointment.fnDraw(false);
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
<br />
<br />
<br />
<h1 class="title"><?php __(MENU_APPOINTMENT_MANAGEMENT_LIST);?></h1>
<div id="dynamic">
    <table id="appointmentList" class="table" cellspacing="0">
        <thead>
        <tr>
            <th class="first"><?php echo TABLE_NO; ?></th>
            <th><?php echo PATIENT_CODE; ?></th>
            <th><?php echo PATIENT_NAME; ?></th>            
            <th><?php echo TABLE_SEX; ?></th>
            <th><?php echo TABLE_TELEPHONE; ?></th>
            <th><?php echo APPOINTMENT_DATE; ?></th>
            <th><?php echo TABLE_REMAINING; ?></th>
            <th><?php echo ACTION_ACTION; ?></th>
        </tr>
        </head>
        <tbody>
        <tr>
            <td colspan="8" class="dataTables_empty"><?php echo TABLE_LOADING; ?></td>
        </tr>
        </tbody>
    </table>
</div>
<div id="dialog" title=""></div>