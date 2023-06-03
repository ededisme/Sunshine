<?php
include('includes/function.php');
$rnd = rand();
$oTable = "oTable" . $rnd;
$printArea = "printArea" . $rnd;
$btnPrint = "btnPrint" . $rnd;
$btnExport = "btnExport" . $rnd;

?>
<?php $tblName = "tbl" . rand(); ?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<script type="text/javascript">
    var <?php echo $oTable; ?>;
    $(document).ready(function(){
        $("#<?php echo $tblName; ?> td:first-child").addClass('first');
        <?php echo $oTable; ?> = $("#<?php echo $tblName; ?>").dataTable({
            "aLengthMenu": [[50, 100, 500, 1000, 5000, 10000, 1000000*1000000], [50, 100, 500, 1000, 5000, 10000, "All"]],
            "iDisplayLength": 1000000*1000000,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo $this->base.'/'.$this->params['controller']; ?>/serviceReferralAjax/<?php echo str_replace("/", "|||", implode(',', $_POST)); ?>",
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                $("#<?php echo $tblName; ?> td:first-child").addClass('first');
                $("#<?php echo $tblName; ?> td:nth-child(9)").css("text-align", "center");
                $("#<?php echo $tblName; ?> td:nth-child(10)").css("text-align", "center");
                $("#<?php echo $tblName; ?> td:nth-child(11)").css("text-align", "right");
                $("#<?php echo $tblName; ?> td:nth-child(12)").css("text-align", "right");
                $("#<?php echo $tblName; ?> td:nth-child(13)").css("text-align", "right");
                $("#<?php echo $tblName; ?> td:last-child").css("white-space", "nowrap");
                $(".btnPrintCustomerInvoice").click(function(event){
                    event.preventDefault();
                    var url = "<?php echo $this->base . '/sales_orders'; ?>/printInvoice/"+$(this).attr("rel");
                    $.ajax({
                        type: "POST",
                        url: url,
                        beforeSend: function(){
                            $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                        },
                        success: function(printResult){
                            w=window.open();
                            w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                            w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                            w.document.write(printResult);
                            w.document.close();
                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                        }
                    });
                });
                var totalQTY = 0;
                var totalDiscount = 0  ; 
                var totalUnitPrice = 0;
                var totalAmount = 0;
                $("#<?php echo $tblName; ?> tr:gt(0)").each(function(){
                    totalQTY += Number($(this).find("td:eq(9)").text().replace(/,/g, ""));
                    totalUnitPrice += Number($(this).find("td:eq(10)").text().replace(/,/g, ""));
                    totalDiscount += Number($(this).find("td:eq(11)").text().replace(/,/g, ""));
                    totalAmount += Number($(this).find("td:eq(12)").text().replace(/,/g, ""));
                });
                $('#<?php echo $tblName; ?> > tbody:last').append('<tr><td class="first" style="font-weight: bold;" colspan="9"><?php echo TABLE_TOTAL; ?>:</td><td class="" style="text-align: center ;font-weight: bold;">' + (totalQTY) + '</td><td class="formatCurrency" style="text-align: right;font-weight: bold;">' + (totalUnitPrice) + '</td><td class="formatCurrency" style="text-align: right;font-weight: bold;">' + (totalDiscount) +'</td><td class="formatCurrency" style="text-align: right;font-weight: bold;">' + (totalAmount) + '</td><td></td></tr>');
                $('.formatCurrency').formatCurrency({colorize:true});
                return sPre;
            },
            "aoColumnDefs": [{
                "sType": "numeric", "aTargets": [ 0 ],
                "bSortable": false, "aTargets": [ 0,-1,-2,-3,-4,-5,-6,-7,-8,-9]
            }],
            "aaSorting": [[ 1, "ASC" ]]
        });
        $("#<?php echo $btnPrint; ?>").click(function(){
            $(".dataTables_length").hide();
            $(".dataTables_filter").hide();
            $(".dataTables_paginate").hide();
            w=window.open();
            w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
            w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
            w.document.write($("#<?php echo $printArea; ?>").html());
            w.document.close();
            w.print();
            w.close();
            $(".dataTables_length").show();
            $(".dataTables_filter").show();
            $(".dataTables_paginate").show();
        });
        $("#<?php echo $btnExport; ?>").click(function(){
            window.open("<?php echo $this->webroot; ?>public/report/report_section_service_<?php echo $user['User']['id']; ?>.csv", "_blank");
            window.close();
        });
    });
</script>
<div id="<?php echo $printArea; ?>">
    <?php
    $msg = '<b style="font-size: 18px;">' . MENU_REFERRAL_REPORT . '</b><br /><br />';
    if($_POST['date_from']!='') {
        $msg .= REPORT_FROM.': '.$_POST['date_from'];
    }
    if($_POST['date_to']!='') {
        $msg .= ' '.REPORT_TO.': '.$_POST['date_to'];
    }
    echo $this->element('/print/header-report',array('msg'=>$msg));
    ?>
    <div id="dynamic">
        <table id="<?php echo $tblName; ?>" class="table_report">
            <thead>
                <tr>
                    <th class="first"><?php echo TABLE_NO; ?></th>
                    <th style="width: 150px !important; text-align: left;"><?php echo TABLE_DATE; ?></th>
                    <th style="width: 100px !important; text-align: left;"><?php echo TABLE_INVOICE_CODE; ?></th>
                    <th><?php echo PATIENT_CODE; ?></th>
                    <th><?php echo PATIENT_NAME; ?></th>
                    <th><?php echo DOCTOR_DOCTOR; ?></th>
                    <th><?php echo SECTION_SECTION; ?></th>
                    <th><?php echo SERVICE_SERVICE; ?></th>
                    <th><?php echo TABLE_REFERRAL; ?></th>
                    <th style="width: 100px !important;"><?php echo TABLE_QTY; ?></th>
                    <th style="width: 120px !important;"><?php echo GENERAL_UNIT_PRICE; ?></th>
                    <th style="width: 120px !important;"><?php echo GENERAL_DISCOUNT; ?> ($)</th>
                    <th style="width: 140px !important;"><?php echo TABLE_TOTAL_AMOUNT; ?> ($)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="13" class="dataTables_empty"><?php echo TABLE_LOADING; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div style="clear: both;"></div>
<br />
<div class="buttons">
    <button type="button" id="<?php echo $btnPrint; ?>" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/>
        <?php echo ACTION_PRINT; ?>
    </button>
</div>
<div class="buttons">
    <button type="button" id="<?php echo $btnExport; ?>" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/csv.png" alt=""/>
        <?php echo ACTION_EXPORT_TO_EXCEL; ?>
    </button>
</div>
<div style="clear: both;"></div>