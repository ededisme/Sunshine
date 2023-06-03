<?php
// Authentication
$this->element('check_access');
$allowAdd = checkAccess($user['User']['id'], $this->params['controller'], 'adds');
$allowExport = checkAccess($user['User']['id'], $this->params['controller'], 'exportExcel');
?>
<?php $tblName = "tbl" . rand(); ?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<script type="text/javascript">
    var oTablePatientIPD;
    var tabSalesId = $(".ui-tabs-selected a").attr("href");
    var tabSalesReg = '';
    $(document).ready(function() {
        $('#tabs a').not("[href=#]").each(function() {
            if ($.data(this, 'href.tabs') == "<?php echo $this->base; ?>/patient_ipds/medicalSurgery") {
                $("#tabs").tabs("remove", $(this).attr("href"));
            }
        });
        oTablePatientIPD = $("#<?php echo $tblName; ?>").dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo $this->base . '/' . $this->params['controller']; ?>/ajax/" + $("#changeStatusPatientIPD").val() + "/" + $("#companyInsuranceIPD").val(),
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function(oSettings, iStart, iEnd, iMax, iTotal, sPre) {
                $("#reloadPatientIPD").attr("src", "<?php echo $this->webroot; ?>img/button/refresh-active.png");
                $("#<?php echo $tblName; ?> td:first-child").addClass('first');
                $("#<?php echo $tblName; ?> td:last-child").css("white-space", "nowrap");
                $(".btnLeavePatientIPD").click(function(event) {
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var leftPanel = $(this).parent().parent().parent().parent().parent().parent().parent();
                    var rightPanel = leftPanel.parent().find(".rightPanel");
                    leftPanel.hide("slide", {
                        direction: "left"
                    }, 500, function() {
                        rightPanel.show();
                    });
                    rightPanel.html("<?php echo ACTION_LOADING; ?>");
                    rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/patientLeave/" + id);
                });

                $(".btnViewPatientIPD").click(function(event) {
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var name = $(this).attr('name');
                    var leftPanel = $(this).parent().parent().parent().parent().parent().parent().parent();
                    var rightPanel = leftPanel.parent().find(".rightPanel");
                    leftPanel.hide("slide", {
                        direction: "left"
                    }, 500, function() {
                        rightPanel.show();
                    });
                    rightPanel.html("<?php echo ACTION_LOADING; ?>");
                    rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/view/" + id);
                });

                $(".btnEditPatientIPD").click(function(event) {
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var leftPanel = $(this).parent().parent().parent().parent().parent().parent().parent();
                    var rightPanel = leftPanel.parent().find(".rightPanel");
                    leftPanel.hide("slide", {
                        direction: "left"
                    }, 500, function() {
                        rightPanel.show();
                    });
                    rightPanel.html("<?php echo ACTION_LOADING; ?>");
                    rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/edit/" + id);

                });
                $(".btnAddMoreService").click(function(event) {
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var patientId = $(this).attr('patientId');
                    var ipdType = $(this).attr('ipdType');
                    var leftPanel = $(this).parent().parent().parent().parent().parent().parent().parent();
                    var rightPanel = leftPanel.parent().find(".rightPanel");
                    leftPanel.hide("slide", {
                        direction: "left"
                    }, 500, function() {
                        rightPanel.show();
                    });
                    rightPanel.html("<?php echo ACTION_LOADING; ?>");
                    rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/addService/" + id + "/" + patientId + "/" + ipdType);

                });

                $(".btnDeletePatientIPD").click(function(event) {
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
                        open: function(event, ui) {
                            $(".ui-dialog-buttonpane").show();
                        },
                        buttons: {
                            '<?php echo ACTION_DELETE; ?>': function() {
                                $.ajax({
                                    type: "GET",
                                    url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/delete/" + id,
                                    data: "",
                                    beforeSend: function() {
                                        $("#dialog").dialog("close");
                                        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                                    },
                                    success: function(result) {
                                        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                        oCache.iCacheLower = -1;
                                        oTablePatientIPD.fnDraw(false);
                                        // alert message
                                        $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>' + result + '</p>');
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

                // Action Reprint Patient Info
                $(".btnPrintPatientIPD").click(function(event) {
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    $("#dialog").html('<div class="buttons"><button type="submit" class="positive reprintPatientIPD" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="txtReprintInvoiceSales"><?php echo ACTION_PRINT_PATIENT_IPD; ?></span></button><button type="submit" class="positive reprintPatientLeave" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="txtReprintPatientLeave"><?php echo ACTION_PRINT_PATIENT_LEAVE; ?></span></button></div>');
                    $(".reprintPatientIPD").click(function() {
                        $.ajax({
                            type: "POST",
                            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printPatientIpd/" + id,
                            beforeSend: function() {
                                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner.gif');
                            },
                            success: function(printPatientDepositFrom) {
                                w = window.open();
                                w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                                w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                                w.document.write(printPatientDepositFrom);
                                w.document.close();
                                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                            }
                        });
                    });
                    $(".reprintPatientLeave").click(function() {
                        $.ajax({
                            type: "POST",
                            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printPatientLeave/" + id,
                            beforeSend: function() {
                                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner.gif');
                            },
                            success: function(printPatientDepositFrom) {
                                w = window.open();
                                w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                                w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                                w.document.write(printPatientDepositFrom);
                                w.document.close();
                                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                            }
                        });
                    });

                    $("#dialog").dialog({
                        title: '<?php echo MENU_PATIENT_IPD_ADMISSION_CONSENT_FORM_INFO; ?>',
                        resizable: false,
                        modal: true,
                        width: 'auto',
                        height: 'auto',
                        position: 'center',
                        open: function(event, ui) {
                            $(".ui-dialog-buttonpane").show();
                        },
                        buttons: {
                            '<?php echo ACTION_CLOSE; ?>': function() {
                                $(this).dialog("close");
                            }
                        }
                    });
                });

                return sPre;
            },
            "aoColumnDefs": [{
                "sType": "numeric",
                "aTargets": [0],
                "bSortable": false,
                "aTargets": [0, -1]
            }],
            "aaSorting": [
                [7, "asc"]
            ]
        });

        $(".btnAddPatientIPD").click(function(event) {
            event.preventDefault();
            var leftPanel = $(this).parent().parent().parent();
            var rightPanel = leftPanel.parent().find(".rightPanel");
            leftPanel.hide("slide", {
                direction: "left"
            }, 500, function() {
                rightPanel.show();
            });
            rightPanel.html("<?php echo ACTION_LOADING; ?>");
            rightPanel.load("<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/add/");
        });

        $('#patientIPDDateFrom, #patientIPDDateTo').datepicker({
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true
        }).unbind("blur");

        $("#changeStatusPatientIPD").change(function() {
            resetFilterPatientIPD();
        });
        $("#reloadPatientIPD").click(function() {
            resetFilterPatientIPD();
        });

        $("#companyInsuranceIPD").change(function() {
            resetFilterPatientIPD();
        });


    });

    function resetFilterPatientIPD() {
        $("#patientIPDDateFrom").datepicker("option", "dateFormat", "yy-mm-dd");
        $("#patientIPDDateTo").datepicker("option", "dateFormat", "yy-mm-dd");
        $("#reloadPatientIPD").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
        var Tablesetting = oTablePatientIPD.fnSettings();
        Tablesetting.sAjaxSource = "<?php echo $this->base . '/' . $this->params['controller']; ?>/ajax/" + $("#changeStatusPatientIPD").val() + "/" + $("#companyInsuranceIPD").val() + "/" + $("#patientIPDDateFrom").val() + "/" + $("#patientIPDDateTo").val();
        oCache.iCacheLower = -1;
        oTablePatientIPD.fnDraw(false);
        $("#patientIPDDateFrom").datepicker("option", "dateFormat", "dd/mm/yy");
        $("#patientIPDDateTo").datepicker("option", "dateFormat", "dd/mm/yy");
    }
</script>

<div class="leftPanel">
    <div style="padding: 5px;border: 1px dashed #bbbbbb;">
        <?php if ($allowAdd) { ?>
            <div class="buttons" style="display: none;">
                <a href="" class="positive btnAddPatientIPD">
                    <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt="" />
                    <?php echo MENU_PATIENT_IPD_ADMISSION_CONSENT_FORM_ADD; ?>
                    &nbsp;&nbsp;<img src="<?php echo $this->webroot; ?>img/button/patient_1.png" alt="" />
                </a>
            </div>
        <?php } ?>
        <div style="float:right;">
            <?php echo REPORT_FROM; ?>
            <input type="text" id="patientIPDDateFrom" style="font-size: 11px; height: 20px;" />
            <?php echo REPORT_TO; ?>
            <input type="text" id="patientIPDDateTo" style="font-size: 11px; height: 20px;" />
            <label><?php echo MENU_COMPANY_INSURANCE_MANAGEMENT; ?>: </label>
            <select id="companyInsuranceIPD" style="width: 120px;">
                <option value="all"><?php echo TABLE_ALL; ?></option>
                <option value="0"><?php echo 'Walk-in'; ?></option>
                <?php
                $queryInsurance = mysql_query("SELECT id, name FROM company_insurances WHERE is_active = 1");
                while ($rowInsurance = mysql_fetch_array($queryInsurance)) {
                ?>
                    <option value="<?php echo $rowInsurance['id']; ?>"><?php echo $rowInsurance['name']; ?></option>
                <?php
                }
                ?>
            </select>
            <?php echo TABLE_STATUS; ?> :
            <select id="changeStatusPatientIPD" style="width: 130px; height: 25px;">
                <option value="all"><?php echo TABLE_ALL; ?></option>
                <option selected="selcted" value="1">Patient IPD</option>
                <option value="2">Patient Leaved</option>
            </select>
            <img alt="" src="<?php echo $this->webroot; ?>img/button/refresh-active.png" style="cursor: pointer; vertical-align: middle;" onmouseover="Tip('Reload Patient IPD')" id="reloadPatientIPD" />
        </div>
        <div style="clear: both;"></div>
    </div>
    <br />
    <div id="dynamic">
        <table id="<?php echo $tblName; ?>" class="table" cellspacing="0">
            <thead>
                <tr>
                    <th class="first"><?php echo TABLE_NO; ?></th>
                    <th><?php echo TABLE_HN; ?></th>
                    <th><?php echo PATIENT_CODE; ?></th>
                    <th><?php echo PATIENT_NAME; ?></th>
                    <th><?php echo TABLE_SEX; ?></th>
                    <th><?php echo TABLE_DOB; ?></th>
                    <th><?php echo TABLE_TELEPHONE; ?></th>
                    <th><?php echo TABLE_CHECK_IN; ?></th>
                    <th><?php echo TABLE_REMAINING; ?></th>
                    <th><?php echo TABLE_ROOM_NUMBER; ?></th>
                    <th><?php echo TABLE_ADMITTING_PHYSICIAN; ?></th>
                    <th><?php echo MENU_COMPANY_INSURANCE_MANAGEMENT; ?></th>
                    <th><?php echo ACTION_ACTION; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="13" class="dataTables_empty first"><?php echo TABLE_LOADING; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <br />
    <br />
    <div style="padding: 5px;border: 1px dashed #bbbbbb;">
        <?php if ($allowAdd) { ?>
            <div class="buttons" style="display: none;">
                <a href="" class="positive btnAddPatientIPD">
                    <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt="" />
                    <?php echo MENU_PATIENT_IPD_ADMISSION_CONSENT_FORM_ADD; ?>
                    &nbsp;&nbsp;<img src="<?php echo $this->webroot; ?>img/button/patient_1.png" alt="" />
                </a>
            </div>
        <?php } ?>
        <div style="clear: both;"></div>
    </div>
</div>
<div class="rightPanel"></div>