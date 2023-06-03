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
            "sAjaxSource": "<?php echo $this->base.'/'.$this->params['controller']; ?>/customerBalanceAjax/<?php echo str_replace("/", "|||", implode(',', $_POST)); ?>",
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                $("#<?php echo $tblName; ?> td:first-child").addClass('first');
                $("#<?php echo $tblName; ?> td:nth-child(8)").css("text-align", "right");
                $("#<?php echo $tblName; ?> td:nth-child(9)").css("text-align", "right");
                $("#<?php echo $tblName; ?> td:last-child").css("white-space", "nowrap");
                // hide date < date_from
                $(".CustomerBalanceDate[rel='hide']").closest("tr").hide();
                // btn link to general ledger
                $(".link2gl").each(function(){
                    var general_ledger_id=$(this).val();
                    $(this).closest("tr").css("cursor", "pointer");
                    $(this).closest("tr").click(function(){
                        $('#tabs ul li a').not("[href=#]").each(function(index) {
                            if($(this).text().indexOf(jQuery.trim("<?php echo MENU_JOURNAL_ENTRY_MANAGEMENT; ?>"))!=-1){
                                $("#tabs").tabs("select", $(this).attr("href"));
                                var selIndex = $("#tabs").tabs("option", "selected");
                                $("#tabs").tabs("remove", selIndex);
                            }
                        });
                        $("#tabs").tabs("add", "<?php echo $this->base; ?>/general_ledgers/indexById/" + general_ledger_id, "<?php echo MENU_JOURNAL_ENTRY_MANAGEMENT; ?>");
                    });
                });
                return sPre;
            },
            "fnDrawCallback": function(oSettings, json) {
                $("#<?php echo $tblName; ?> .colspanParent").parent().attr("colspan", 5);
                $("#<?php echo $tblName; ?> .colspanParent").parent().next().remove();
                $("#<?php echo $tblName; ?> .colspanParent").parent().next().remove();
                $("#<?php echo $tblName; ?> .colspanParent").parent().next().remove();
                $("#<?php echo $tblName; ?> .colspanParent").parent().next().remove();
            },
            "aoColumnDefs": [{
                "sType": "numeric", "aTargets": [ 0 ],
                "bSortable": false, "aTargets": [ 0,-1,-2,-3,-4,-5,-6,-7,-8 ]
            }],
            "aaSorting": [[ 1, "asc" ]]
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
            window.open("<?php echo $this->webroot; ?>public/report/customer_balance_<?php echo $user['User']['id']; ?>.csv", "_blank");
        });
    });
</script>
<div id="<?php echo $printArea; ?>">
    <?php
    $msg = '<b style="font-size: 18px;">' . MENU_REPORT_CUSTOMER_BALANCE . '</b><br /><br />';
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
                    <th style="width: 150px !important;"><?php echo TABLE_TYPE; ?></th>
                    <th style="width: 80px !important;"><?php echo TABLE_DATE; ?></th>
                    <th style="width: 120px !important;"><?php echo TABLE_COMPANY; ?></th>
                    <th><?php echo TABLE_REFERENCE; ?></th>
                    <th style="width: 120px !important;"><?php echo TABLE_INVOICE_CODE; ?></th>
                    <th style="width: 220px !important;"><?php echo TABLE_ACCOUNT; ?></th>
                    <th style="width: 120px !important;"><?php echo GENERAL_AMOUNT; ?></th>
                    <th style="width: 120px !important;"><?php echo GENERAL_BALANCE; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="9" class="dataTables_empty"><?php echo TABLE_LOADING; ?></td>
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
    <button type="button" id="<?php echo $btnExport; ?>" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/csv.png" alt=""/>
        <?php echo ACTION_EXPORT_TO_EXCEL; ?>
    </button>
</div>
<div style="clear: both;"></div>