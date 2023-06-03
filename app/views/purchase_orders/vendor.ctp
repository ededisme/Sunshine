<?php 
// Authentication
$this->element('check_access');
$allowAddVendor = checkAccess($user['User']['id'], 'vendors', 'quickAdd');

$tblName = "tbl" . rand(); ?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $("#<?php echo $tblName; ?>").dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo $this->base . '/' . $this->params['controller']; ?>/vendorAjax/<?php echo $companyId; ?>",
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                $("#dialog").dialog("option", "position", "center");
                $(".table td:first-child").addClass('first');
                $(".table td:last-child").css("white-space", "nowrap");
                $(".table tr").click(function(){
                    $(this).find("input[name='chkVendor']").attr("checked", true);
                });
                return sPre;
            },
            "aoColumnDefs": [{
                    "sType": "numeric", "aTargets": [ 0 ],
                    "bSortable": false, "aTargets": [ 0,-1,-2 ],
                    "bSearchable": false, "aTargets": [ 0,-1,-2 ]
            }]
        });
        <?php
        if($allowAddVendor){
        ?>
        $(".addVendorPurchaseBill").click(function(event){
            event.preventDefault();
            $.ajax({
                type:   "GET",
                url:    "<?php echo $this->base . "/vendors/quickAdd/"; ?>",
                beforeSend: function(){
                    $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                    $(".addVendorPurchaseBill").attr("disabled", true);
                    $("#txtAddVendorPurchaseBill").text('<?php echo ACTION_LOADING; ?>');
                },
                success: function(msg){
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                    $(".addVendorPurchaseBill").attr("disabled", false);
                    $("#txtAddVendorPurchaseBill").text('<?php echo MENU_VENDOR_ADD; ?>');
                    $("#dialog3").html(msg);
                    $("#dialog3").dialog({
                        title: '<?php echo MENU_VENDOR_ADD; ?>',
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
                                var formName = "#VendorQuickAddForm";
                                var validateBack =$(formName).validationEngine("validate");
                                if(!validateBack){
                                    return false;
                                }else{
                                    if($("#VendorVgroupId").val() == null || $("#VendorVgroupId").val() == '' || $("#VendorPaymentTermId").val() == '' || $("#VendorPaymentTermId").val() == ''){
                                        alertSelectRequireField();
                                    } else {
                                        $(this).dialog("close");
                                        $.ajax({
                                            dataType: 'json',
                                            type: "POST",
                                            url: "<?php echo $this->base; ?>/vendors/quickAdd",
                                            data: $("#VendorQuickAddForm").serialize(),
                                            beforeSend: function(){
                                                $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                                            },
                                            error: function (result) {
                                                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                                createSysAct('Purchase Bill', 'Quick Add Vendor', 2, result);
                                                $("#dialog1").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                                                $("#dialog1").dialog({
                                                    title: '<?php echo DIALOG_INFORMATION; ?>',
                                                    resizable: false,
                                                    modal: true,
                                                    width: 'auto',
                                                    height: 'auto',
                                                    position:'center',
                                                    closeOnEscape: true,
                                                    open: function(event, ui){
                                                        $(".ui-dialog-buttonpane").show(); $(".ui-dialog-titlebar-close").show();
                                                    },
                                                    buttons: {
                                                        '<?php echo ACTION_CLOSE; ?>': function() {
                                                            $(this).dialog("close");
                                                        }
                                                    }
                                                });
                                            },
                                            success: function(result){
                                                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                                                createSysAct('Purchase Bill', 'Quick Add Vendor', 1, '');
                                                var msg = '';
                                                if(result.error == 0){
                                                    msg = '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>';
                                                    // Set Vendor
                                                    $("#PurchaseOrderVendorId").val(result.id);
                                                    $("#PurchaseOrderVendorName").val(result.name);
                                                    $("#PurchaseOrderVendorName").attr("readonly", true);
                                                    $("#searchVendor").hide();
                                                    $("#deleteSearchVendorPB").show();
                                                } else  if (result.error == 1){
                                                    msg = '<?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?>'; 
                                                } else  if (result.error == 2){
                                                    msg = '<?php echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM; ?>';
                                                }
                                                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+msg+'</p>');
                                                $("#dialog").dialog({
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
<?php 
if($allowAddVendor){
?>
<div style="padding: 5px;border: 1px dashed #bbbbbb; margin-bottom: 5px;">
    <div class="buttons">
        <a href="#" class="positive addVendorPurchaseBill">
            <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
            <span id="txtAddVendorPurchaseBill"><?php echo MENU_VENDOR_ADD; ?></span>
        </a>
    </div> 
    <div style="clear: both;"></div>
</div>
<br />
<?php 
} 
?>
<div id="dynamic">
    <table id="<?php echo $tblName; ?>" class="table" cellspacing="0">
        <thead>
            <tr>
                <th class="first"></th>
                <th><?php echo TABLE_VENDOR_NUMBER; ?></th>
                <th><?php echo TABLE_NAME; ?></th>
                <th><?php echo TABLE_TELEPHONE_WORK; ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="4" class="dataTables_empty first"><?php echo TABLE_LOADING; ?></td>
            </tr>
        </tbody>
    </table>
</div>