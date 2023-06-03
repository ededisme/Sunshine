<?php
// Authentication
$this->element('check_access');
?>
<?php $tblName = "tbl" . rand(); ?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<script type="text/javascript">
    var oTablePatientOPD;
    var tabOPDId  = $(".ui-tabs-selected a").attr("href");
    var tabSalesRegOPD = '';
    $(document).ready(function(){      
        var dates = $("#changeDateFromPtnOPD,#changeDateToPtnOPD").datepicker({
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true
        });              
        
        $("#changeDateFromPtnOPD").datepicker("option", "dateFormat", "yy-mm-dd");
        $("#changeDateToPtnOPD").datepicker("option", "dateFormat", "yy-mm-dd");
        oTablePatientOPD = $("#<?php echo $tblName; ?>").dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo $this->base.'/'.$this->params['controller']; ?>/opdListAjax/"+$("#changeDateFromPtnOPD").val()+'/'+$("#changeDateToPtnOPD").val(),
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                $("#<?php echo $tblName; ?> td:first-child").addClass('first');
                $("#<?php echo $tblName; ?> td:last-child").css("white-space", "nowrap");
                $(".reloadPatientOPD").attr("src","<?php echo $this->webroot; ?>img/button/refresh-active.png");                        
                $("#changeDateFromPtnOPD").datepicker("option", "dateFormat", "dd/mm/yy");           
                $("#changeDateToPtnOPD").datepicker("option", "dateFormat", "dd/mm/yy");                                                                                                                                    
                return sPre;
            },
            "aoColumnDefs": [{
                "sType": "numeric", "aTargets": [ 0 ],
                "bSortable": false, "aTargets": [ 0,-1 ]
            }]
        });
        
        $(".reloadPatientOPD").click(function(){
            resetFilterPatientOPD();
        });
        
        $("#clearDatePtnOPD").click(function(){
            $("#changeDateFromPtnOPD").val('');
            $("#changeDateToPtnOPD").val('');
            resetFilterPatientOPD();
        });
                
    });
    
    function resetFilterPatientOPD(){
        $(".reloadPatientOPD").attr("src","<?php echo $this->webroot; ?>img/layout/spinner.gif"); 
        $("#changeDateFromPtnOPD").datepicker("option", "dateFormat", "yy-mm-dd");
        $("#changeDateToPtnOPD").datepicker("option", "dateFormat", "yy-mm-dd");
        var Tablesetting = oTablePatientOPD.fnSettings();
        Tablesetting.sAjaxSource = "<?php echo $this->base . '/' . $this->params['controller']; ?>/opdListAjax/"+$("#changeDateFromPtnOPD").val()+'/'+$("#changeDateToPtnOPD").val();
        oCache.iCacheLower = -1;
        oTablePatientOPD.fnDraw(false);
        $("#changeDateFromPtnOPD").datepicker("option", "dateFormat", "dd/mm/yy");
        $("#changeDateToPtnOPD").datepicker("option", "dateFormat", "dd/mm/yy");
    }
</script>
<div class="leftPanel">
    <div style="padding: 5px; border: 1px dashed #3C69AD;"> 
        <div style="float:right;">
            <label for="changeDateFromPtnOPD"><?php echo REPORT_FROM; ?> :</label>
            <input type="text" value="<?php echo date('d/m/Y'); ?>" id="changeDateFromPtnOPD" style="width: 115px; height: 20px;" readonly="readonly" /> 
            <?php echo REPORT_TO;?>
            <input type="text" value="<?php echo date('d/m/Y'); ?>" id="changeDateToPtnOPD" style="width: 115px; height: 20px;" readonly="readonly" /> 
            <img alt="" src="<?php echo $this->webroot; ?>img/button/clear.png" style="cursor: pointer; vertical-align: middle;" onmouseover="Tip('Clear Date')" id="clearDatePtnOPD" />
            <span style="float: right; cursor: pointer; padding-left: 10px; vertical-align: middle; padding-top: 3px;">                    
                <img onmouseover="Tip('Refresh')" class="reloadPatientOPD" alt="Refresh" src="<?php echo $this->webroot;?>img/button/refresh-active.png" />                    
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
                    <th><?php echo PATIENT_NAME; ?></th>                
                    <th><?php echo TABLE_SEX; ?></th>
                    <th><?php echo TABLE_DOB; ?></th>
                    <th><?php echo TABLE_TELEPHONE; ?></th>
                    <th><?php echo OTHER_REQUESTED_DATE; ?></th>  
                    <th><?php echo DOCTOR_NAME; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="10" class="first dataTables_empty"><?php echo TABLE_LOADING; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <br />
    <br />
</div>
<div class="rightPanel"></div>