<?php
include('includes/function.php');
$this->element('check_access');
$rnd = rand();
$oTable = "oTable" . $rnd;
$printArea = "printArea" . $rnd;
$btnPrint = "btnPrint" . $rnd;
$btnExport = "btnExport" . $rnd;
$allowConvertSI = checkAccess($user['User']['id'], $this->params['controller'], 'convertInvoice');
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
            "sAjaxSource": "<?php echo $this->base.'/'.$this->params['controller']; ?>/customerInvoiceAjax/<?php echo str_replace("/", "|||", implode(',', $_POST)); ?>",
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                $("#<?php echo $tblName; ?> td:first-child").addClass('first');
                $("#<?php echo $tblName; ?> td:nth-child(5)").css("text-align", "right");
                $("#<?php echo $tblName; ?> td:nth-child(6)").css("text-align", "right");
                $("#<?php echo $tblName; ?> td:nth-child(7)").css("text-align", "center");
                $("#<?php echo $tblName; ?> td:last-child").css("white-space", "nowrap");
                $(".btnPrintCustomerInvoice").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr("rel");
                    $("#dialog").html('<div class="buttons"><button type="submit" class="positive printCheckOutFormInvoice" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="printCheckOutForm"><?php echo ACTION_PRINT_INVOICE; ?></span></button><button type="submit" class="positive printCheckOutFormInvoiceDetail" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="printCheckOutForm"><?php echo ACTION_PRINT_INVOICE_DETAIL; ?></span></button> <button style="display: none;" type="submit" class="positive printCheckOutFormInvoiceVat" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="printCheckOutForm"><?php echo ACTION_PRINT_INVOICE.' Vat'; ?></span></button></div>');
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
                                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                            }
                        });
                    });
                    $(".printCheckOutFormInvoiceDetail").click(function(){
                        $.ajax({
                            type: "POST",
                            url: "<?php echo $this->base . '/' ; ?>cashiers/printInvoiceDetail/"+id,
                            beforeSend: function(){
                                $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                            },
                            success: function(printCheckOutFormResult){
                                w=window.open();
                                w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                                w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                                w.document.write(printCheckOutFormResult);
                                w.document.close();
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
                    total += Number($(this).find("td:eq(4)").text().replace(/,/g, ""));
                    totalRemaining += Number($(this).find("td:eq(5)").text().replace(/,/g, ""));
                });
                $('#<?php echo $tblName; ?> > tbody:last').append('<tr><td class="first" style="font-weight: bold;" colspan="4"><?php echo TABLE_TOTAL; ?>:</td><td class="formatCurrency" style="text-align: right;font-weight: bold;">' + (total) + '</td><td class="formatCurrency" style="text-align: right;font-weight: bold;">' + (totalRemaining) + '</td><td colspan="3"></td></tr>');
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


		$(".btnConvertInvoice").click(function(){
            var i = 0;
            var soSelected = "";
            $(".invoice_check").each(function(){
                if($(this).is(':checked')){
                    if(i > 0){
                        soSelected += "&";
                    }
                    soSelected += "soId[]="+$(this).attr("invoice-id");                            
                    i++;
                }
            });
            if(soSelected!=""){
                var message = "Do you want to export all datas selected?";
                $("#dialog").dialog('option', 'title', '<?php echo DIALOG_CONFIRMATION; ?>');
                $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>'+message+'</p>');
                $("#dialog").dialog({
                    title: '<?php echo DIALOG_CONFIRMATION; ?>',
                    resizable: false,
                    modal: true,
                    width: 'auto',
                    height: 'auto',
                    open: function(event, ui){
                        $(".ui-dialog-buttonpane").show();
                        $(".txtConvertInvoice").html("<?php echo ACTION_LOADING; ?>");                        
                    },
                    buttons: {
                        '<?php echo ACTION_YES; ?>': function() {     
                            $(".txtConvertInvoice").html("<?php echo ACTION_CONVERT_DATA; ?>");                       
                            $.ajax({
                                type: "POST",
                                url: "<?php echo $this->base.'/'.$this->params['controller']; ?>/convertInvoice/",
                                data: soSelected,
                                beforeSend: function(){
                                    $("#dialog").dialog("close");
                                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo ACTION_LOADING; ?></p>');
                                    $("#dialog").dialog({
                                        title: '<?php echo DIALOG_INFORMATION; ?>',
                                        resizable: false,
                                        modal: true,
                                        width: 230,
                                        height: 'auto',
                                        buttons: { }
                                    });
                                    $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                                },
                                success: function(result){                              
                                    $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");                                    
                                    oCache.iCacheLower = -1;
                                    <?php echo $oTable; ?>.fnDraw(false);
                                    $("#convertInvoice").removeAttr('checked');
                                    $(".invoice_check").removeAttr('checked');
                                    // alert message
                                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result+'</p>');
                                    $("#dialog").dialog({
                                        title: '<?php echo DIALOG_INFORMATION; ?>',
                                        resizable: false,
                                        modal: true,
                                        width: 230,
                                        height: 'auto',
                                        buttons: {
                                            '<?php echo ACTION_CLOSE; ?>': function() {
                                                $(this).dialog("close");
                                                $(".txtConvertInvoice").html("<?php echo ACTION_CONVERT_DATA; ?>");
                                            }
                                        }
                                    });
//                                    window.open("<?php echo $this->webroot; ?>public/report/total_sales_<?php echo date("Y-m-d"); ?>.csv", "_blank");
//                                    window.close();
                                }
                            });
                        },
                        '<?php echo ACTION_CANCEL; ?>': function() {
                            $(".txtConvertInvoice").html("<?php echo ACTION_CONVERT_DATA; ?>");                            
                            $(this).dialog("close");
                        }
                    }
                });                                                
            }
        });

		$("#convertInvoice").change(function(){
            if($(this).is(":checked")){
				$(".invoice_check").attr('checked', true);
            } else {
                $(".invoice_check").attr('checked', false);
            }
        });
    });
</script>
<div id="<?php echo $printArea; ?>">
    <?php
    $msg = '<b style="font-size: 18px;">' . MENU_REPORT_SALES_ORDER_INVOICE . '</b><br /><br />';
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
                   <th class="first" style="width: 50px !important;">
					<?php if($allowConvertSI){ ?>
						<input type="checkbox" id="convertInvoice" /></th>      
					<?php }else {?>
						<?php echo TABLE_NO; ?>
					<?php }?> 
                    <th style="width: 120px !important;"><?php echo TABLE_INVOICE_DATE; ?></th>
                    <th style="width: 120px !important;"><?php echo TABLE_INVOICE_CODE; ?></th>
                    <th><?php echo PATIENT_NAME; ?></th>
                    <th style="width: 140px !important;"><?php echo TABLE_TOTAL_AMOUNT; ?> ($)</th>
                    <th style="width: 140px !important;"><?php echo GENERAL_BALANCE; ?> ($)</th>
                    <th style="width: 80px !important;"><?php echo TABLE_STATUS; ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="8" class="dataTables_empty"><?php echo TABLE_LOADING; ?></td>
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
<?php 
if($allowConvertSI){
?>
<div class="buttons">
    <button type="button" class="positive btnConvertInvoice">
        <img src="<?php echo $this->webroot; ?>img/button/convert.png" alt=""/>
        <span class="txtConvertInvoice"><?php echo ACTION_CONVERT_DATA; ?></span>
    </button>
</div>
<?php }?>
<div style="clear: both;"></div>