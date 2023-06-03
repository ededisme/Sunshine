<?php
// Authentication
$this->element('check_access');
$allowAdd=checkAccess($user['User']['id'], $this->params['controller'], 'add');
$allowCloneServicePrice=checkAccess($user['User']['id'], $this->params['controller'], 'cloneServicePrice');
$allowDeleteAll=checkAccess($user['User']['id'], $this->params['controller'], 'deleteServicePrice');
$allowExport=checkAccess($user['User']['id'], $this->params['controller'], 'exportExcel');
$rnd = rand();
$company = "changeCompanyServiceInsurance";
$companyInsurance = "companyInsurance";
?>
<?php $tblName = "tbl" . rand(); ?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<script type="text/javascript">
    var oTableServicePriceInsurance;    
    function checkLocationWithCompany() {
        $("#companyInsurance option").each(function() {
            if ($(this).attr("company-id")) {
                var companyId = $(this).attr("company-id").split(",");
                if (companyId.indexOf($("#changeCompanyServiceInsurance").val()) == -1) {
                    $(this).removeAttr('selected');
                    $(this).hide();
                } else {
                    $(this).show();
                }
            } else {
                
                $(this).removeAttr('selected');
                $(this).show();
            }
        });
    }
        
    $(document).ready(function(){
        checkLocationWithCompany();
        $("#<?php echo $company;?>").change(function(){
            var obj = $(this);
            checkLocationWithCompany();
        });
        // Prevent Key Enter
        preventKeyEnter();
        $("#<?php echo $tblName; ?> td:first-child").addClass('first');
        oTableServicePriceInsurance = $("#<?php echo $tblName; ?>").dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo $this->base.'/'.$this->params['controller']; ?>/ajax/",
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                $("#<?php echo $tblName; ?> td:first-child").addClass('first');
                $("#<?php echo $tblName; ?> td:nth-child(8)").css("text-align", "right");
                $("#<?php echo $tblName; ?> td:last-child").css("white-space", "nowrap");
                $(".btnViewServicePriceInsurance").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var name = $(this).attr('name');
                    var leftPanel=$(this).parent().parent().parent().parent().parent().parent().parent();
                    var rightPanel=leftPanel.parent().find(".rightPanel");
                    leftPanel.hide("slide", { direction: "left" }, 500, function() {
                        rightPanel.show();
                    });
                    rightPanel.html("<?php echo ACTION_LOADING; ?>");
                    rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/view/" + id);
                });
                $(".btnEditServicePriceInsurance").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var name = $(this).attr('name');
                    var leftPanel=$(this).parent().parent().parent().parent().parent().parent().parent();
                    var rightPanel=leftPanel.parent().find(".rightPanel");
                    leftPanel.hide("slide", { direction: "left" }, 500, function() {
                        rightPanel.show();
                    });
                    rightPanel.html("<?php echo ACTION_LOADING; ?>");
                    rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/edit/" + id);
                });
                $(".btnDeleteServicePriceInsurance").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var name = $(this).attr('name');
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
                                    url: "<?php echo $this->base.'/'.$this->params['controller']; ?>/delete/" + id,
                                    data: "",
                                    beforeSend: function(){
                                        $("#dialog").dialog("close");
                                        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                                    },
                                    success: function(result){
                                        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                        oCache.iCacheLower = -1;
                                        oTableServicePriceInsurance.fnDraw(false);
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
                "bSortable": false, "aTargets": [ 0,1,-1 ]
            }]
        });
        
        $("#changeCompanyServiceInsurance").change(function(){
            var Tablesetting = oTableServicePriceInsurance.fnSettings();
            Tablesetting.sAjaxSource = "<?php echo $this->base . '/' . $this->params['controller']; ?>/ajax"+"/"+$("#companyInsurance").val()+"/"+$("#changeCompanyServiceInsurance").val(),
            oCache.iCacheLower = -1;
            oTableServicePriceInsurance.fnDraw(false);
            $("#changeDate").datepicker("option", "dateFormat", "dd/mm/yy");
        });
        
        $("#companyInsurance").change(function(){
            var Tablesetting = oTableServicePriceInsurance.fnSettings();
            Tablesetting.sAjaxSource = "<?php echo $this->base . '/' . $this->params['controller']; ?>/ajax"+"/"+$("#companyInsurance").val()+"/"+$("#changeCompanyServiceInsurance").val(),
            oCache.iCacheLower = -1;
            oTableServicePriceInsurance.fnDraw(false);
            $("#changeDate").datepicker("option", "dateFormat", "dd/mm/yy");
        });
        
        $(".btnAddServicePriceInsurance").click(function(event){
            event.preventDefault();
            var leftPanel=$(this).parent().parent().parent();
            var rightPanel=leftPanel.parent().find(".rightPanel");
            leftPanel.hide("slide", { direction: "left" }, 500, function() {
                rightPanel.show();
            });
            rightPanel.html("<?php echo ACTION_LOADING; ?>");
            rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/add/");
        });
        
        $(".btnCloneServicePriceInsurance").click(function(event){
            event.preventDefault();
            var leftPanel=$(this).parent().parent().parent();
            var rightPanel=leftPanel.parent().find(".rightPanel");
            leftPanel.hide("slide", { direction: "left" }, 500, function() {
                rightPanel.show();
            });
            rightPanel.html("<?php echo ACTION_LOADING; ?>");
            rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/cloneServicePrice/");
        });
                        
        $("#checkAll").click(function(){
            if($(this).is(':checked')){
                modify_boxes(1);
            }else{
                modify_boxes(2);
            }
            
        });
        $(".btnDeleteAllServicePriceInsurance").click(function(event){
            event.preventDefault();
            var i = 0
            var dataDelete = "";
            $(".servicePriceInsuranceGroupId").each(function(){            
                if($(this).is(":checked")){
                    if(i > 0){
                        dataDelete += "&";
                    }
                    dataDelete += "id[]="+$(this).val();                            
                    i++;
                }
            });
            var insuranceGroupId = dataDelete;
            if(insuranceGroupId!=""){
                $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Are you sure you want to delete all services insurance?</p>');
                $("#dialog").dialog({
                    title: '<?php echo ACTION_DELETE;?>',
                    resizable: false,
                    modal: true,
                    width: 'auto',
                    height: 'auto',
                    buttons: {
                        Delete: function() {                              
                            $( this ).dialog( "close" );
                            $.ajax({
                                type: "GET",
                                url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/deleteServicePrice",
                                data: insuranceGroupId,
                                success: function(msg){                                                                           
                                    $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                    oCache.iCacheLower = -1;
                                    oTableServicePriceInsurance.fnDraw(false);
                                    // alert message
                                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+msg+'</p>');
                                    $("#dialog").dialog({
                                        title: '<?php echo DIALOG_INFORMATION; ?>',
                                        resizable: false,
                                        modal: true,
                                        width: 'auto',
                                        height: 'auto',
                                        buttons: {
                                            '<?php echo ACTION_CLOSE; ?>': function() {
                                                $("#checkAll").attr('checked', false);
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
            }else{
                $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Please check data first.</p>');
                $("#dialog").dialog({
                    title: '<?php echo ACTION_DELETE;?>',
                    resizable: false,
                    modal: true,
                    width: 'auto',
                    height: 'auto',
                    buttons: {                        
                        Close: function() {
                            $( this ).dialog( "close" );
                        }
                    }
                });
            }
            
        });
        
        
        $(".btnExportServicePriceInsurance").click(function(){
            var companyId = $("#changeCompanyServiceInsurance").val();
            var companyInsurance = $("#companyInsurance").val();
            $.ajax({
                type: "POST",
                url: "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/exportExcel/"+companyId+"/"+companyInsurance,
                data: "action=export",
                beforeSend: function(){
                    $(".btnExportServicePriceInsurance").attr('disabled','disabled');
                    $(".btnExportServicePriceInsurance").find('img').attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                },
                success: function(){
                    $(".btnExportServicePriceInsurance").removeAttr('disabled');
                    $(".btnExportServicePriceInsurance").find('img').attr("src", "<?php echo $this->webroot; ?>img/button/csv.png");
                    window.open("<?php echo $this->webroot; ?>public/report/ServicesPriceInsuranceExport.csv", "_blank");
                }
            });
        });
        
    });
    
    function modify_boxes(Tochecked){
        var value;
        if(Tochecked == 1){
            value = true;
        }else{
            value = false;
        }
        $(".servicePriceInsuranceGroupId").each(function(){
            $(this).attr('checked', value);
        });
    }
</script>
<div class="leftPanel">
    <div style="padding: 5px;border: 1px dashed #3C69AD;">
        <?php if($allowAdd){ ?>
        <div class="buttons">
            <a href="" class="positive btnAddServicePriceInsurance">
                <img src="<?php echo $this->webroot; ?>img/icon/plus.png" alt=""/>
                <?php echo MENU_INSURANCE_SERVICE_PRICE_MANAGEMENT_ADD; ?>
            </a>
        </div>
        <?php } ?>
        <?php if($allowCloneServicePrice){ ?>
        <div class="buttons">
            <a href="" class="positive btnCloneServicePriceInsurance">
                <img src="<?php echo $this->webroot; ?>img/icon/clone.png" alt=""/>
                <?php echo 'Clone Insurance Service Price'; ?>
            </a>
        </div>
        <?php } ?>   
        <?php if($allowDeleteAll){ ?>
        <div class="buttons">
            <a href="" class="positive btnDeleteAllServicePriceInsurance">
                <img src="<?php echo $this->webroot; ?>img/button/delete.png" alt=""/>
                <?php echo ACTION_DELETE; ?>
            </a>
        </div>
        <?php } ?>  
        <?php if($allowExport){ ?>
        <div class="buttons">
            <button type="button" class="positive btnExportServicePriceInsurance">
                <img src="<?php echo $this->webroot; ?>img/button/csv.png" alt=""/>
                <?php echo ACTION_EXPORT_TO_EXCEL; ?>
            </button>
        </div>
        <?php } ?>
        
        
        <div style="float:right; vertical-align: middle; padding-top: 5px">           
            <?php echo TABLE_COMPANY; ?> :
            <?php echo $this->Form->select('CompanyId', $companies, null, array('id' => $company, 'default' => 1, 'escape' => false, 'name' => 'company_id', 'class' => 'validate[required]', 'empty' => INPUT_SELECT, 'style' => 'width: 150px;')); ?>            
            <?php echo TABLE_COMPANY_INSURANCE_NAME; ?> :
            <select name="data[company_insurances]" id="<?php echo $companyInsurance;?>" style="width: 220px;">
                <option value="all" company-id=""><?php echo TABLE_ALL; ?></option>
                <?php 
                foreach($companyInsurances AS $companyInsurance){
                    $queryCompanyName = mysql_query("SELECT (SELECT GROUP_CONCAT(company_id) FROM company_insurance_companies WHERE company_insurance_id = company_insurances.id GROUP BY company_insurance_id) AS company_id,name FROM company_insurances WHERE is_active=1 AND id=" . $companyInsurance['CompanyInsurance']['id']);
                    $dataCompanyName = mysql_fetch_array($queryCompanyName);
                ?>
                    <option value="<?php echo $companyInsurance['CompanyInsurance']['id']; ?>" company-id="<?php echo $dataCompanyName['company_id']; ?>"><?php echo $companyInsurance['CompanyInsurance']['name']; ?></option>
                <?php
                }
            ?>
            </select>          
        </div>
        
        <div style="clear: both;"></div>
    </div>
    <br />
    <div id="dynamic">
        <table id="<?php echo $tblName; ?>" class="table" cellspacing="0">
            <thead>
                <tr>
                    <th class="first"><input id="checkAll" type="checkbox"  /></th>    
                    <th class="first"><?php echo TABLE_NO; ?></th>
                    <th><?php echo TABLE_COMPANY; ?></th>
                    <th><?php echo TABLE_SECTION_NAME; ?></th>
                    <th><?php echo TABLE_SERVICE_NAME; ?></th>
                    <th><?php echo PATIENT_TYPE; ?></th>                
                    <th><?php echo TABLE_COMPANY_INSURANCE_NAME; ?></th>
                    <th><?php echo GENERAL_UNIT_PRICE; ?></th>
                    <th><?php echo ACTION_ACTION; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="9" class="dataTables_empty"><?php echo TABLE_LOADING; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <br />
    <br />
    <div style="padding: 5px;border: 1px dashed #3C69AD;">
        <?php if($allowAdd){ ?>
        <div class="buttons">
            <a href="" class="positive btnAddServicePriceInsurance">
                 <img src="<?php echo $this->webroot; ?>img/icon/plus.png" alt=""/>
                <?php echo MENU_INSURANCE_SERVICE_PRICE_MANAGEMENT_ADD; ?>
            </a>
        </div>
        <?php } ?>        
        <?php if($allowCloneServicePrice){ ?>
        <div class="buttons">
            <a href="" class="positive btnCloneServicePriceInsurance">
                <img src="<?php echo $this->webroot; ?>img/icon/clone.png" alt=""/>
                <?php echo 'Clone Insurance Service Price'; ?>
            </a>
        </div>
        <?php } ?>
        <?php if($allowDeleteAll){ ?>
        <div class="buttons">
            <a href="" class="positive btnDeleteAllServicePriceInsurance">
                <img src="<?php echo $this->webroot; ?>img/button/delete.png" alt=""/>
                <?php echo ACTION_DELETE; ?>
            </a>
        </div>
        <?php } ?> 
        <?php if($allowExport){ ?>
        <div class="buttons">
            <button type="button" class="positive btnExportServicePriceInsurance">
                <img src="<?php echo $this->webroot; ?>img/button/csv.png" alt=""/>
                <?php echo ACTION_EXPORT_TO_EXCEL; ?>
            </button>
        </div>
        <?php } ?>
        <div style="clear: both;"></div>
    </div>
</div>
<div class="rightPanel"></div>