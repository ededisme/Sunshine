<?php
// Authentication
$this->element('check_access');
$allowAdd=checkAccess($user['User']['id'], $this->params['controller'], 'add');
$allowExport=checkAccess($user['User']['id'], $this->params['controller'], 'exportExcel');
?>
<?php $tblName = "tbl" . rand(); ?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<script type="text/javascript">
    var oTableReferral;
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        $("#<?php echo $tblName; ?> td:first-child").addClass('first');
        oTableReferral = $("#<?php echo $tblName; ?>").dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo $this->base.'/'.$this->params['controller']; ?>/ajax/",
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                $("#<?php echo $tblName; ?> td:first-child").addClass('first');
                $("#<?php echo $tblName; ?> td:last-child").css("white-space", "nowrap");
                $("#<?php echo $tblName; ?> td:eq(4)").css("width", "400px");
                $(".btnViewReferral").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var leftPanel=$(this).parent().parent().parent().parent().parent().parent().parent();
                    var rightPanel=leftPanel.parent().find(".rightPanel");
                    leftPanel.hide("slide", { direction: "left" }, 500, function() {
                        rightPanel.show();
                    });
                    rightPanel.html("<?php echo ACTION_LOADING; ?>");
                    rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/view/" + id);
                });
                $(".tnEditReferral").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var leftPanel=$(this).parent().parent().parent().parent().parent().parent().parent();
                    var rightPanel=leftPanel.parent().find(".rightPanel");
                    leftPanel.hide("slide", { direction: "left" }, 500, function() {
                        rightPanel.show();
                    });
                    rightPanel.html("<?php echo ACTION_LOADING; ?>");
                    rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/edit/" + id);
                });
                $(".btnDeleteReferral").click(function(event){
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
                                        oTableReferral.fnDraw(false);
                                        // alert message
                                        if(result != '<?php echo MESSAGE_DATA_HAS_BEEN_DELETED; ?>' && result != '<?php echo MESSAGE_DATA_HAVE_CHILD; ?>'){
                                            createSysAct('Brand', 'Delete', 2, result);
                                            $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                                        }else {
                                            createSysAct('Brand', 'Delete', 1, '');
                                            // alert message
                                            $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result+'</p>');
                                        }
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
            }]
        });
        $(".btnExportReferral").click(function(){
            $.ajax({
                type: "POST",
                url: "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/exportExcel",
                data: "action=export",
                beforeSend: function(){
                    $(".btnExportReferral").attr('disabled','disabled');
                    $(".btnExportReferral").find('img').attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                },
                success: function(){
                    $(".btnExportReferral").removeAttr('disabled');
                    $(".btnExportReferral").find('img').attr("src", "<?php echo $this->webroot; ?>img/button/csv.png");
                    window.open("<?php echo $this->webroot; ?>public/report/referral_export.csv", "_blank");
                }
            });
        });
        $(".btnAddReferral").click(function(event){
            event.preventDefault();
            var leftPanel=$(this).parent().parent().parent();
            var rightPanel=leftPanel.parent().find(".rightPanel");
            leftPanel.hide("slide", { direction: "left" }, 500, function() {
                rightPanel.show();
            });
            rightPanel.html("<?php echo ACTION_LOADING; ?>");
            rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/add/");
        });
    });
</script>
<div class="leftPanel">
    <div style="padding: 5px;border: 1px dashed #bbbbbb;">
        <?php if($allowAdd){ ?>
        <div class="buttons">
            <a href="" class="positive btnAddReferral">
                <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
                <?php echo MENU_REFERRAL_ADD; ?>
            </a>
        </div>
        <?php } ?>
        <?php if($allowExport){ ?>
        <div class="buttons">
            <button type="button" class="positive btnExportReferral">
                <img src="<?php echo $this->webroot; ?>img/button/csv.png" alt=""/>
                <?php echo ACTION_EXPORT_TO_EXCEL; ?>
            </button>
        </div>
        <?php } ?>
        <div style="clear: both;"></div>
    </div>
    <br />
    <div id="dynamic">
        <table id="<?php echo $tblName; ?>" class="table" cellspacing="0">
            <thead>
                <tr>
                    <th class="first"><?php echo TABLE_NO; ?></th>
                    <th><?php echo TABLE_NAME; ?></th>
                    <th><?php echo TABLE_SEX; ?></th>
                    <th><?php echo TABLE_TELEPHONE; ?></th>
                    <th><?php echo TABLE_DOB; ?></th>
                    <th><?php echo ACTION_ACTION; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="6" class="dataTables_empty"><?php echo TABLE_LOADING; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <br />
    <br />
    <div style="padding: 5px;border: 1px dashed #bbbbbb;">
        <?php 
        if($allowAdd){ 
        ?>
        <div class="buttons">
            <a href="" class="positive btnAddReferral">
                <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
                <?php echo MENU_REFERRAL_ADD; ?>
            </a>
        </div>
        <?php 
        } 
        if($allowExport){ ?>
        <div class="buttons">
            <button type="button" class="positive btnExportReferral">
                <img src="<?php echo $this->webroot; ?>img/button/csv.png" alt=""/>
                <?php echo ACTION_EXPORT_TO_EXCEL; ?>
            </button>
        </div>
        <?php } ?>
        <div style="clear: both;"></div>
    </div>
</div>
<div class="rightPanel"></div>