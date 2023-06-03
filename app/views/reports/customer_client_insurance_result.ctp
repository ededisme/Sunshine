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
            "sAjaxSource": "<?php echo $this->base.'/'.$this->params['controller']; ?>/customerClientInsuranceAjax/<?php echo str_replace("/", "|||", implode(',', $_POST)); ?>",
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                $("#<?php echo $tblName; ?> td:first-child").addClass('first');
                $("#<?php echo $tblName; ?> td:nth-child(5)").css("text-align", "left");
                $("#<?php echo $tblName; ?> td:nth-child(6)").css("text-align", "right");
                $("#<?php echo $tblName; ?> td:nth-child(7)").css("text-align", "right");
                $("#<?php echo $tblName; ?> td:last-child").css("white-space", "nowrap");
                $(".btnPrintCustomerInvoice").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr("rel");
                    $("#dialog").html('<div class="buttons"><button type="submit" class="positive printCheckOutFormInvoice" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="printCheckOutForm"><?php echo ACTION_PRINT_INVOICE; ?></span></button> <button type="submit" class="positive printCheckOutFormInvoiceVat" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="printCheckOutForm"><?php echo ACTION_PRINT_INVOICE.' Vat'; ?></span></button></div>');
                    $(".printCheckOutFormInvoice").click(function(){
                        $.ajax({
                            type: "POST",
                            url: "<?php echo $this->base . '/cashiers'; ?>/printInvoice/"+id,
                            beforeSend: function(){
                                $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                            },
                            success: function(printCheckOutFormResult){
                                w=window.open();
                                w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                                w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                                w.document.write(printCheckOutFormResult);
                                w.document.close();
                                try
                                {
                                    //Run some code here                                                                                                       
                                    jsPrintSetup.setSilentPrint(1);
                                    jsPrintSetup.printWindow(w);
                                }
                                catch(err)
                                {
                                    //Handle errors here                                    
                                    w.print();                                     
                                } 
                                w.close();
                                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                            }
                        });
                    });
                    $(".printCheckOutFormInvoiceVat").click(function(){
                        $.ajax({
                            type: "POST",
                            url: "<?php echo $this->base . '/cashiers'; ?>/printInvoiceVat/"+id,
                            beforeSend: function(){
                                $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                            },
                            success: function(printCheckOutFormResult){
                                w=window.open();
                                w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                                w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                                w.document.write(printCheckOutFormResult);
                                w.document.close();
                                try
                                {
                                    //Run some code here                                                                                                       
                                    jsPrintSetup.setSilentPrint(1);
                                    jsPrintSetup.printWindow(w);
                                }
                                catch(err)
                                {
                                    //Handle errors here                                    
                                    w.print();                                     
                                } 
                                w.close();
                                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                            }
                        });
                    });
                    $("#dialog").dialog({
                        title: '<?php echo 'Print Invoice'; ?>',
                        resizable: false,
                        modal: true,
                        width: 'auto',
                        height: 'auto',
                        position:'center',
                        closeOnEscape: true,
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show(); $(".ui-dialog-titlebar-close").show();
                        },
                        close: function(){
                            $(this).dialog({close: function(){}});
                            $(this).dialog("close");
                        },
                        buttons: {
                            '<?php echo ACTION_CLOSE; ?>': function() {
                                $("meta[http-equiv='refresh']").attr('content','0');
                                $(this).dialog("close");
                            }
                        }
                    });
                });
                $(".btnVoidRep").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var name = $(this).attr('name');
                    var sts = $(this).attr('sts');
                    // condition for check credit memo before void
                    if(sts=='0'){
                        alert('<?php echo MESSAGE_CREDIT_MEMO_BEFORE_VOID; ?>');
                        return false;
                    }
                    $("#dialog").dialog('option', 'title', '<?php echo DIALOG_CONFIRMATION; ?>');
                    $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_CONFIRM_VOID; ?> <b>' + name + '</b>?</p>');
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
                            '<?php echo ACTION_VOID; ?>': function() {
                                $.ajax({
                                    type: "GET",
                                    url: "<?php echo $this->base.'/dashboards'; ?>/voidInvoice/" + id,
                                    data: "",
                                    beforeSend: function(){
                                        $("#dialog").dialog("close");
                                        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                                    },
                                    success: function(result){
                                        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                        oCache.iCacheLower = -1;
                                        <?php echo $oTable; ?>.fnDraw(false);
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
                
                var total = 0;
                var totalRemaining = 0;
                $("#<?php echo $tblName; ?> tr:gt(0)").each(function(){
                    total += Number($(this).find("td:eq(5)").text().replace(/,/g, ""));
                    totalRemaining += Number($(this).find("td:eq(6)").text().replace(/,/g, ""));
                });
                $('#<?php echo $tblName; ?> > tbody:last').append('<tr><td class="first" style="font-weight: bold;" colspan="5"><?php echo TABLE_TOTAL; ?>:</td><td class="formatCurrency" style="text-align: right;font-weight: bold;">' + (total) + '</td><td class="formatCurrency" style="text-align: right;font-weight: bold;">' + (totalRemaining) + '</td><td colspan="3"></td></tr>');
                $('.formatCurrency').formatCurrency({colorize:true});
                return sPre;
            },
            "aoColumnDefs": [{
                "sType": "numeric", "aTargets": [ 0 ],
                "bSortable": false, "aTargets": [ 0 ]
            }],
            "aaSorting": [[ 1, "desc" ]]
        });
        $("#<?php echo $btnPrint; ?>").click(function(){
            $(".dataTables_length").hide();
            $(".dataTables_filter").hide();
            $(".dataTables_paginate").hide();
            $(".dataTables_last").hide();
            $("#<?php echo $tblName; ?> th:last-child").hide();
            $("#<?php echo $tblName; ?> td:last-child").hide();
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
            $("#<?php echo $tblName; ?> th:last-child").show();
            $("#<?php echo $tblName; ?> td:last-child").show();
        });
    });
</script>
<div id="<?php echo $printArea; ?>">
    <?php
    $msg = '<b style="font-size: 18px;">' . TITLE_CLIENT_INSURANCE_PROVIDER . '</b><br /><br />';
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
                    <th style="width: 120px !important;"><?php echo TABLE_INVOICE_DATE; ?></th>
                    <th style="width: 120px !important;"><?php echo TABLE_INVOICE_CODE; ?></th>
                    <th><?php echo TABLE_PATIENT; ?></th>
                    <th style="width: 250px !important;"><?php echo TITLE_CLIENT_INSURANCE_PROVIDER; ?></th>
                    <th style="width: 140px !important;"><?php echo TABLE_TOTAL_AMOUNT; ?> ($)</th>
                    <th style="width: 140px !important;"><?php echo GENERAL_BALANCE; ?> ($)</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="7" class="dataTables_empty"><?php echo TABLE_LOADING; ?></td>
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
<div style="clear: both;"></div>