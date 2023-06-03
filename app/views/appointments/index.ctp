<?php 
// Authentication
$this->element('check_access');
$allowAdd=checkAccess($user['User']['id'], $this->params['controller'], 'add');
$allowExport=checkAccess($user['User']['id'], $this->params['controller'], 'exportExcel');
?>
<?php $tblName = "tblApm" . rand(); ?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<script type="text/javascript">
    var oTablePatientAppointment;
    var tabSalesId  = $(".ui-tabs-selected a").attr("href");
    var tabSalesReg = '';
    $(document).ready(function(){        
        oTablePatientAppointment = $("#<?php echo $tblName; ?>").dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo $this->base.'/'.$this->params['controller']; ?>/ajax/",
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                $("#<?php echo $tblName; ?> td:first-child").addClass('first');
                $("#<?php echo $tblName; ?> td:nth-child(3)").css("white-space", "nowrap");
                $("#<?php echo $tblName; ?> td:nth-child(8)").css("white-space", "nowrap");
                $("#<?php echo $tblName; ?> td:last-child").css("white-space", "nowrap"); 
                $(".reloadAppointment").attr("src","<?php echo $this->webroot; ?>img/button/refresh-active.png");                                                                          
                
                // Double Click Appointment
                $("#<?php echo $tblName; ?> tr").click(function(){
                    changeBackgroupQueue("<?php echo $tblName; ?>");
                    $(this).closest("tr").css('background','#eeeca9');
                });
                $("#<?php echo $tblName; ?> tr").dblclick(function(){                                                                        
                    var id = $(this).find(".btnReturnAppointment").attr('rel');
                    var name = $(this).find(".btnReturnAppointment").attr('title');
                    var doctorId = $(this).find(".btnReturnAppointment").attr('doctor-id');                              
                    $.ajax({
                        type: "GET",
                        url: "<?php echo $this->base . '/' . 'patients'; ?>/returnPatient/appointment/",                            
                        data: "id=" + id +"&doctorId=" + doctorId,
                        beforeSend: function(){
                            $("#dialog").html('<p style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                        },
                        success: function(msg){
                            oCache.iCacheLower = -1;
                            oTablePatientAppDashboard.fnDraw(false);
                            $("#dialog").html(msg);
                        }
                    });
                    $("#dialog").dialog({
                        title: name,
                        resizable: false,
                        modal: true,
                        width: '500',
                        height: '315',
                        buttons: {
                            Cancel: function() {
                                $( this ).dialog( "close" );
                            }
                        }
                    });
                });
                
                $(".btnReturnAppointment").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var name = $(this).attr('title');
                    var doctorId = $(this).attr('doctor-id');
                    $.ajax({
                        type: "GET",
                        url: "<?php echo $this->base . '/' . 'patients'; ?>/returnPatient/appointment/",
                        data: "id=" + id +"&doctorId=" + doctorId,
                        beforeSend: function(){
                            $("#dialog").html('<p style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                        },
                        success: function(msg){
                            oCache.iCacheLower = -1;
                            oTablePatientAppointment.fnDraw(false);
                            $("#dialog").html(msg);
                        }
                    });
                    $("#dialog").dialog({
                        title: name,
                        resizable: false,
                        modal: true,
                        width: '500',
                        height: '315',
                        buttons: {
                            Cancel: function() {
                                $( this ).dialog( "close" );
                            }
                        }
                    });
                });
                
                $(".btnEditAppointment").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var leftPanel  = $(this).parent().parent().parent().parent().parent().parent().parent();
                    var rightPanel = leftPanel.parent().find(".rightPanel");
                    leftPanel.hide("slide", { direction: "left" }, 500, function() {
                        rightPanel.show();
                    });
                    rightPanel.html("<?php echo ACTION_LOADING; ?>");
                    rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/edit/"+id);
                    
                });
                
                $(".btnCancelAppointment").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var name = $(this).attr('title');
                    $("#dialog").dialog('option', 'title', '<?php echo DIALOG_CONFIRMATION; ?>');
                    $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_CONFIRM_DELETE; ?> <b>' + name + '</b>?</p>');
                    $("#dialog").dialog({
                        title: '<?php echo DIALOG_CONFIRMATION; ?>',
			resizable: false,
			modal: true,
                        width: 'auto',
                        height: 'auto',
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show();
                        },
			buttons: {
                            '<?php echo ACTION_DELETE; ?>': function() {
                                $.ajax({
                                    type: "GET",
                                    url: "<?php echo $this->base.'/'.$this->params['controller']; ?>/cancelAppointment/" + id,
                                    data: "",
                                    beforeSend: function(){
                                        $("#dialog").dialog("close");
                                        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                                    },
                                    success: function(result){
                                        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                        oCache.iCacheLower = -1;
                                        oTablePatientAppointment.fnDraw(false);
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
                            '<?php echo ACTION_CANCEL; ?>': function() {
                                $(this).dialog("close");
                            }
			}
                    });
                });
                
                return sPre;
            },
            "aoColumnDefs": [{
                "sType": "numeric", "aTargets": [ 0 ],
                "bSortable": false, "aTargets": [ 0,-1 ]
            }],
            "aaSorting": [[ 5, "asc" ]]
        });
        
        $(".btnPatientAppointment").click(function(event){
            event.preventDefault();
            var leftPanel=$(this).parent().parent().parent();
            var rightPanel=leftPanel.parent().find(".rightPanel");
            leftPanel.hide("slide", { direction: "left" }, 500, function() {
                rightPanel.show();
            });
            rightPanel.html("<?php echo ACTION_LOADING; ?>");
            rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/add/");
        });
        
        $('#changeDateAppFrom, #changeDateAppTo').datepicker({
            dateFormat:'dd/mm/yy',
            changeMonth: true,
            changeYear: true
        }).unbind("blur");

        
        $(".reloadAppointment").click(function(){
            resetFilterInvoice();
        });
        
        $("#clearDateApp").click(function(){
            $('#changeDateAppFrom').val('');
            $('#changeDateAppTo').val('');
            resetFilterInvoice();
        });
        
        $("#changeCusApp").autocomplete("<?php echo $this->base ."/reports/searchPatient"; ?>", {
            width: 410,
            max: 10,
            scroll: true,
            scrollHeight: 500,
            formatItem: function(data, i, n, value) {
                return value.split(".*")[1] + " - " + value.split(".*")[2];
            },
            formatResult: function(data, value) {
                return value.split(".*")[1] + " - " + value.split(".*")[2];
            }
        }).result(function(event, value){
            $("#changeCustomerIdApp").val(value.toString().split(".*")[0]);
            $("#changeCusApp").val(value.toString().split(".*")[1]+" - "+value.toString().split(".*")[2]).attr("readonly", true);
            $("#clearCusApp").show();
            resetFilterInvoice();
        });
        
        $("#clearCusApp").click(function(){
            $("#changeCustomerIdApp").val("all");
            $("#changeCusApp").val("");
            $("#changeCusApp").removeAttr("readonly");
            $("#clearCusApp").hide();
            resetFilterInvoice();
        });
    });
    
    function changeBackgroupQueue(tbl){
        $("#"+tbl+" tbody tr").each(function(){
                $(this).removeAttr('style');
        });
    }
    
    function resetFilterInvoice(){
        $(".reloadAppointment").attr("src","<?php echo $this->webroot; ?>img/layout/spinner.gif"); 
        $("#changeDateAppFrom").datepicker("option", "dateFormat", "yy-mm-dd");
        $("#changeDateAppTo").datepicker("option", "dateFormat", "yy-mm-dd");
        var Tablesetting = oTablePatientAppointment.fnSettings();
        Tablesetting.sAjaxSource = "<?php echo $this->base . '/' . $this->params['controller']; ?>/ajax/"+$("#changeCustomerIdApp").val()+"/"+$("#changeDateAppFrom").val()+"/"+$("#changeDateAppTo").val();
        oCache.iCacheLower = -1;
        oTablePatientAppointment.fnDraw(false);
        $("#changeDateAppFrom").datepicker("option", "dateFormat", "dd/mm/yy");
        $("#changeDateAppTo").datepicker("option", "dateFormat", "dd/mm/yy");
    }
</script>

<div class="leftPanel">
    <div style="padding: 5px;border: 1px dashed #3C69AD;">
        <?php if($allowAdd){ ?>
        <div class="buttons">
            <a href="" class="positive btnPatientAppointment">
                <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
                <?php echo MENU_APPOINTMENT_MANAGEMENT_ADD; ?>
                &nbsp;&nbsp;<img src="<?php echo $this->webroot; ?>img/icon/time.png" alt=""/>
            </a>
        </div>
        <?php } ?>        
        <div style="float:right;">
            <?php echo REPORT_FROM; ?> :
            <input type="text" id="changeDateAppFrom" style="width: 115px; height: 20px;" readonly="readonly" /> 
            <?php echo REPORT_TO; ?> :
            <input type="text" id="changeDateAppTo" style="width: 115px; height: 20px;" readonly="readonly" /> 
            <img alt="" src="<?php echo $this->webroot; ?>img/button/clear.png" style="cursor: pointer; vertical-align: middle;" onmouseover="Tip('Clear Date')" id="clearDateApp" />
            <label style="margin-left: 10px;" for="changeCusApp"><?php echo PATIENT_NAME; ?> :</label>
            <input type="hidden" id="changeCustomerIdApp" value="all" />
            <input type="text" id="changeCusApp" style="width: 250px; height: 20px;" />
            <img alt="" src="<?php echo $this->webroot; ?>img/button/delete.png" style="cursor: pointer; vertical-align: middle; display: none;" onmouseover="Tip('Clear Customer')" id="clearCusApp" />
            
            <span style="float: right; cursor: pointer; padding-left: 10px; vertical-align: middle; padding-top: 3px;">                    
                <img onmouseover="Tip('Refresh')" class="reloadAppointment" alt="Refresh" src="<?php echo $this->webroot;?>img/button/refresh-active.png" />                    
            </span>
        </div>
        <div style="clear: both;"></div>
    </div>
    <br />
    <div id="dynamic">
        <table id="<?php echo $tblName; ?>" class="table" cellspacing="0">
            <thead>
                <tr>
                    <th class="first"><?php echo TABLE_NO; ?></th>
                    <th><?php echo PATIENT_CODE; ?></th>
                    <th><?php echo TABLE_NAME; ?></th>                
                    <th><?php echo TABLE_SEX; ?></th>
                    <th><?php echo TABLE_TELEPHONE; ?></th>
                    <th><?php echo APPOINTMENT_DATE; ?></th>
                    <th><?php echo TABLE_REMAINING; ?></th>
                    <th><?php echo DOCTOR_DOCTOR; ?></th>
                    <th><?php echo GENERAL_DESCRIPTION; ?></th>
                    <th><?php echo ACTION_ACTION; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="10" class="dataTables_empty"><?php echo TABLE_LOADING; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <br />
    <br />
    <div style="padding: 5px;border: 1px dashed #3C69AD;">
        <?php if($allowAdd){ ?>
        <div class="buttons">
            <a href="" class="positive btnPatientAppointment">
                <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
                <?php echo MENU_APPOINTMENT_MANAGEMENT_ADD; ?>
                &nbsp;&nbsp;<img src="<?php echo $this->webroot; ?>img/icon/time.png" alt=""/>
            </a>
        </div>
        <?php } ?>        
        <div style="clear: both;"></div>
    </div>
</div>
<div class="rightPanel"></div>