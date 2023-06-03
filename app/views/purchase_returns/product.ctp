<?php 
// Authentication
$this->element('check_access');
$allowAddProduct = checkAccess($user['User']['id'], 'products', 'quickAdd');

$tblName = "tbl" . rand(); 
$rand    = rand();
?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        var oTable<?php echo $rand; ?> = $("#<?php echo $tblName; ?>").dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo $this->base . '/' . $this->params['controller']; ?>/product_ajax/<?php echo $companyId; ?>/<?php echo $branchId; ?>/<?php echo $locationId; ?>/<?php echo $orderDate; ?>?br_id=<?php echo $brId; ?>",
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                $("#dialog").dialog("option", "position", "center");
                $("#<?php echo $tblName; ?> td:first-child").addClass('first');
                $("#<?php echo $tblName; ?> td:nth-child(3)").css("white-space", "nowrap");
                $("#<?php echo $tblName; ?> td:last-child").css("white-space", "nowrap");
                $("#<?php echo $tblName; ?> tr").click(function(){
                    $(this).find("input[name='chkProductPR']").attr("checked", true);
                });
                return sPre;
            },
            "aoColumnDefs": [{
                    "sType": "numeric", "aTargets": [ 0 ],
                    "bSortable": false, "aTargets": [ 0,-1 ]
                }]
        });
        $("#changeCategorySaleOrderSelectProduct").change(function(){
            var valueId = $(this).val();
            var Tablesetting = oTable<?php echo $rand; ?>.fnSettings();
            Tablesetting.sAjaxSource = "<?php echo $this->base . '/' . $this->params['controller']; ?>/product_ajax/<?php echo $companyId; ?>/<?php echo $branchId; ?>/<?php echo $locationId; ?>/<?php echo $orderDate; ?>/"+valueId+"?br_id=<?php echo $brId; ?>";
            oCache.iCacheLower = -1;
            oTable<?php echo $rand; ?>.fnDraw(false);
        });
        <?php
        if($allowAddProduct){
        ?>
        $(".addProductInventoryPhysical").click(function(event){
            event.preventDefault();
            $.ajax({
                type:   "GET",
                url:    "<?php echo $this->base . "/products/quickAdd/"; ?>",
                beforeSend: function(){
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                    $(".addProductInventoryPhysical").attr("disabled", true);
                    $("#txtAddProductInventoryPhysical").text('<?php echo ACTION_LOADING; ?>');
                },
                success: function(msg){
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $(".addProductInventoryPhysical").attr("disabled", false);
                    $("#txtAddProductInventoryPhysical").text('<?php echo MENU_PRODUCT_NAME_MANAGEMENT_ADD; ?>');
                    $("#dialog2").html(msg);
                    $("#dialog2").dialog({
                        title: '<?php echo MENU_PRODUCT_MANAGEMENT_ADD; ?>',
                        resizable: false,
                        modal: true,
                        width: '700',
                        height: '600',
                        position:'center',
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show();
                        },
                        buttons: {
                            '<?php echo ACTION_SAVE; ?>': function() {
                                var formName = "#ProductQuickAddForm";
                                var validateBack =$(formName).validationEngine("validate");
                                if(!validateBack){
                                    return false;
                                }else{
                                    $(this).dialog("close");
                                    $.ajax({
                                        type: "POST",
                                        url: "<?php echo $this->base; ?>/products/quickAdd",
                                        data: $("#ProductQuickAddForm").serialize(),
                                        beforeSend: function(){
                                            $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                                        },
                                        success: function(result){
                                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                                            // Message Alert
                                            if(result != '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>' && result != '<?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?>' && result != '<?php echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM; ?>'){
                                                createSysAct('Product', 'Quick Add', 2, result);
                                                $("#dialog2").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                                            }else {
                                                createSysAct('Product', 'Quick Add', 1, '');
                                                // alert message
                                                $("#dialog2").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result+'</p>');
                                            }
                                            $("#dialog2").dialog({
                                                title: '<?php echo DIALOG_INFORMATION; ?>',
                                                resizable: false,
                                                modal: true,
                                                width: 'auto',
                                                height: 'auto',
                                                position: 'center',
                                                open: function(event, ui){
                                                    $(".ui-dialog-buttonpane").show();
                                                },
                                                buttons: {
                                                    '<?php echo ACTION_CLOSE; ?>': function() {
                                                        $(this).dialog("close");
                                                    }
                                                }
                                            });
                                        }
                                    });
                                }  
                            }
                        }
                    });
                }
            });
        });
        <?php
        }
        ?>
    });
</script>
<br />
<div style="padding: 5px;border: 1px dashed #bbbbbb; margin-bottom: 5px;">
    <?php 
    if($allowAddProduct){
    ?>
    <div class="buttons">
        <a href="#" class="positive addProductInventoryPhysical">
            <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
            <span id="txtAddProductInventoryPhysical"><?php echo MENU_PRODUCT_NAME_MANAGEMENT_ADD; ?></span>
        </a>
    </div>
    <?php 
    } 
    ?>
    <div style="float:right;">
        <?php echo MENU_PRODUCT_GROUP_MANAGEMENT; ?>:
        <select id="changeCategorySaleOrderSelectProduct" style="width:150px;">
            <option value=""><?php echo TABLE_ALL; ?></option>
            <?php
            $queryPgroup = mysql_query("SELECT * FROM `pgroups` WHERE is_active=1 AND (user_apply = 0 OR (user_apply = 1 AND id IN (SELECT pgroup_id FROM user_pgroups WHERE user_id = ".$user['User']['id']."))) AND id IN (SELECT pgroup_id FROM pgroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id'].")) ORDER BY name");
            while($dataPgroup=mysql_fetch_array($queryPgroup)){
            ?>
            <option value="<?php echo $dataPgroup['id']; ?>"><?php echo $dataPgroup['name']; ?></option>
            <?php
            }
            ?>
        </select>
    </div>
    <div style="clear: both;"></div>
</div>
<div id="dynamic">
    <table id="<?php echo $tblName; ?>" class="table" cellspacing="0">
        <thead>
            <tr>
                <th class="first"></th>
                <th><?php echo TABLE_SKU; ?></th>
                <th><?php echo TABLE_PRODUCT_NAME; ?></th>
                <th><?php echo TABLE_EXPIRED_DATE; ?></th>
                <th><?php echo TABLE_QTY_IN_STOCK; ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="5" class="dataTables_empty first"><?php echo TABLE_LOADING; ?></td>
            </tr>
        </tbody>
    </table>
</div>