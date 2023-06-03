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
            "sAjaxSource": "<?php echo $this->base.'/'.$this->params['controller']; ?>/customerLaboAjax/<?php echo str_replace("/", "|||", implode(',', $_POST)); ?>",
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                $("#<?php echo $tblName; ?> td:first-child").addClass('first');
                $("#<?php echo $tblName; ?> td:nth-child(6)").css("white-space", "nowrap");
                $("#<?php echo $tblName; ?> td:nth-child(7)").css("text-align", "center");
                $("#<?php echo $tblName; ?> td:nth-child(8)").css("text-align", "center");
                $("#<?php echo $tblName; ?> td:nth-child(9)").css("text-align", "right");
                $("#<?php echo $tblName; ?> td:nth-child(10)").css("text-align", "right");
                $("#<?php echo $tblName; ?> td:nth-child(11)").css("text-align", "center");
                $("#<?php echo $tblName; ?> td:nth-child(12)").css("text-align", "center");                
                var totalQty = 0;
                var totalAmount = 0;                
                var totalHospitalPrice = 0; 
                $("#<?php echo $tblName; ?> tr:gt(0)").each(function(){
                    totalQty += Number($(this).find("td:eq(7)").text().replace(/,/g, ""));
                    totalHospitalPrice += Number($(this).find("td:eq(8)").text().replace(/,/g, ""));
                    totalAmount += Number($(this).find("td:eq(9)").text().replace(/,/g, ""));
                });
                $('#<?php echo $tblName; ?> > tbody:last').append('<tr><td class="first" style="font-weight: bold;" colspan="7"><?php echo TABLE_TOTAL; ?>:</td><td style="text-align: center;font-weight: bold;">' + (totalQty) + '</td><td class="formatCurrency" style="text-align: right;font-weight: bold;">' + (totalHospitalPrice) +'</td><td class="formatCurrency" style="text-align: right;font-weight: bold;">' + (totalAmount) +'</td></tr>');
                
                $('.formatCurrency').formatCurrency({colorize:true});
                return sPre;
            },
            "aoColumnDefs": [{
                "sType": "numeric", "aTargets": [ 0 ],
                "bSortable": false, "aTargets": [ 0 ]
            }],
            "aaSorting": [[ 1, "asc" ]]
        });
        $("#<?php echo $btnPrint; ?>").click(function(){
            $(".dataTables_length").hide();
            $(".dataTables_filter").hide();
            $(".dataTables_paginate").hide();
            $(".dataTables_last").hide();
            $(".dataTables_info").hide();
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
            $(".dataTables_info").show();
        });
        $("#<?php echo $btnExport; ?>").click(function(){
            window.open("<?php echo $this->webroot; ?>public/report/report_labo_result_<?php echo $user['User']['id']; ?>.csv", "_blank");
            window.close();
        });
    });
</script>
<div id="<?php echo $printArea; ?>">
    <?php
    $msg = '<b style="font-size: 18px;">' . MENU_LABO_MANAGEMENT . '</b><br /><br />';
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
                    <th style="width: 120px !important;"><?php echo TABLE_DATE; ?></th>
                    <th><?php echo TABLE_INVOICE_CODE; ?></th>
                    <th><?php echo PATIENT_CODE; ?></th>
                    <th><?php echo PATIENT_NAME; ?></th>
                    <th><?php echo DOCTOR_DOCTOR; ?></th>
                    <th><?php echo MENU_SUB_GROUP; ?></th>
                    <th style="width: 80px !important;"><?php echo TABLE_QTY; ?></th>
                    <th style="width: 120px !important;"><?php echo TABLE_HOSPITAL_PRICE; ?></th>
                    <th style="width: 120px !important;"><?php echo TABLE_PATIENT_PRICE; ?></th>
                    <th style="width: 80px !important;"><?php echo TABLE_SEX; ?></th>
                    <th style="width: 80px !important;"><?php echo TABLE_DATE_OF_BIRTH; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="12" class="dataTables_empty"><?php echo TABLE_LOADING; ?></td>
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