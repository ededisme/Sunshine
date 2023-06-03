<?php $absolute_url  = FULL_BASE_URL . Router::url("/", false); ?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(".table td:first-child").addClass('first');

        /**
         * This script use for display patient payment list.
         */
        var oTablePaymentList11 = $("#paymentList11").dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo $absolute_url.$this->params['controller']; ?>/dashboardPaymentAjax/",
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                $(".table td:first-child").addClass('first');
                return sPre;
            },
            "aoColumnDefs": [
                {
                "sType": "numeric", "aTargets": [ 0 ],
                "bSortable": false, "aTargets": [ 0,-1 ]
                }
            ]
        });
        setInterval(function() {
            oCache.iCacheLower = -1;
            oTablePaymentList11.fnDraw(false);
        },60*1000);
        
        /**
         * This script use for display patient debt list.
         */
//        var oTableDebtList = $("#debtList").dataTable({
//            "bProcessing": true,
//            "bServerSide": true,
//            "sAjaxSource": "<?php echo $absolute_url.$this->params['controller']; ?>/cashierDebtAjax/",
//            "fnServerData": fnDataTablesPipeline,
//            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
//                $(".table td:first-child").addClass('first');
//                return sPre;
//            },
//            "aoColumnDefs": [
//                {
//                "sType": "numeric", "aTargets": [ 0 ],
//                "bSortable": false, "aTargets": [ 0,-1 ]
//                }
//            ]
//        });
//        setInterval(function() {
//            oCache.iCacheLower = -1;
//            oTableDebtList.fnDraw(false);
//        },60*1000);
        
        /**
         * This script use for display all invoice have created today.
         */
//        var oTableInvoiceList = $("#invoiceList").dataTable({
//            "bProcessing": true,
//            "bServerSide": true,
//            "sAjaxSource": "<?php echo $absolute_url.$this->params['controller']; ?>/cashierInvoiceAjax/",
//            "fnServerData": fnDataTablesPipeline,
//            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
//                $(".table td:first-child").addClass('first');
//                $(".btnView").click(function(event){
//                    event.preventDefault();
//                    var id = $(this).attr('rel');
//                    var name = $(this).attr('title');
//                    $.ajax({
//                        type: "GET",
//                        url: "<?php echo $absolute_url; ?>cashiers/printInvoiceReceipt/" + id,
//                        data: "",
//                        beforeSend: function(){
//                            $("#dialog").html('<p style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
//                        },
//                        success: function(msg){
//                            $("#dialog").html(msg);
//                        }
//                    });
//                    $("#dialog").dialog({
//                        width: '90%',
//                        height: 400,
//                        title: 'Invoice ID: ' + name,
//                        resizable: false,
//                        modal: true,
//                        buttons: {
//                            "<?php echo ACTION_PRINT; ?>": function() {
//                                w=window.open();
//                                w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
//                                w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
//                                w.document.write($("#dialog").html());
//                                w.print();
//                                w.close();
//                                $( this ).dialog( "close" );
//                            }
//                        }
//                    });
//                });
//                
//                $(".btnVoid").click(function(event){
//                    event.preventDefault();
//                    var id = $(this).attr('rel');
//                    var name = $(this).attr('name');
//                    var sts = $(this).attr('sts');
//                    var stsRec = $(this).attr('stsRec');
//                    // condition for check credit memo before void
//                    if(sts=='0'){
//                        alert('<?php echo MESSAGE_CREDIT_MEMO_BEFORE_VOID; ?>');
//                        return false;
//                    }
//                    if(stsRec=='0'){
//                        alert('<?php echo MESSAGE_VOID_RECEIPT_BEFORE_VOID; ?>');
//                        return false;
//                    }
//                    $("#dialog").dialog('option', 'title', '<?php echo DIALOG_CONFIRMATION; ?>');
//                    $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_CONFIRM_VOID; ?> <b>' + name + '</b>?</p>');
//                    $("#dialog").dialog({
//                        title: '<?php echo DIALOG_CONFIRMATION; ?>',
//			resizable: false,
//			modal: true,
//                        width: 'auto',
//                        height: 'auto',
//                        open: function(event, ui){
//                            $(".ui-dialog-buttonpane").show();
//                        },
//			buttons: {
//                            '<?php echo ACTION_VOID; ?>': function() {
//                                $.ajax({
//                                    type: "GET",
//                                    url: "<?php echo $this->base.'/dashboards'; ?>/voidInvoice/" + id,
//                                    data: "",
//                                    beforeSend: function(){
//                                        $("#dialog").dialog("close");
//                                        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
//                                    },
//                                    success: function(result){
//                                        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
//                                        oCache.iCacheLower = -1;
//                                        oTableInvoiceList.fnDraw(false);
//                                        // alert message
//                                        $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result+'</p>');
//                                        $("#dialog").dialog({
//                                            title: '<?php echo DIALOG_INFORMATION; ?>',
//                                            resizable: false,
//                                            modal: true,
//                                            width: 'auto',
//                                            height: 'auto',
//                                            buttons: {
//                                                '<?php echo ACTION_CLOSE; ?>': function() {
//                                                    $(this).dialog("close");
//                                                }
//                                            }
//                                        });
//                                    }
//                                });
//                            },
//                            '<?php echo ACTION_CANCEL; ?>': function() {
//                                $(this).dialog("close");
//                            }
//			}
//                    });
//                });
//                return sPre;
//            },
//            "aoColumnDefs": [
//                {
//                "sType": "numeric", "aTargets": [ 0 ],
//                "bSortable": false, "aTargets": [ 0,-1 ]
//                }
//            ]
//        });
//        setInterval(function() {
//            oCache.iCacheLower = -1;
//            oTableInvoiceList.fnDraw(false);
//        },60*1000);
//        
        
        /**
         * This script use for display patient ipd list.
         */
//        var oTablePatientIpdList = $("#patientIpd").dataTable({
//            "bProcessing": true,
//            "bServerSide": true,
//            "sAjaxSource": "<?php echo $absolute_url.$this->params['controller']; ?>/dashboardPatientIpdAjax/",
//            "fnServerData": fnDataTablesPipeline,
//            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
//                $(".table td:first-child").addClass('first');
//                $(".btnPrint").click(function(event){
//                    event.preventDefault();
//                    var btnQuotation=$("#dialogPrint").html();  
//                    var id = $(this).attr('rel');
//                    var name = $(this).attr('title');
//                    $("#patientInformation").load("<?php echo $absolute_url . $this->params['controller']; ?>/printPatientService/" + id);
//                    $("#dialog").html(btnQuotation);
//                    $("#dialog").dialog({
//                        title: '<?php echo MENU_PATIENT_IPD_ADMISSION_CONSENT_FORM_INFO; ?>',
//                        resizable: false,
//                        modal: true,   
//                        buttons: {
//                            Ok: function() {
//                                $( this ).dialog( "close" );
//                            }
//                        }
//                    });
//                    $("#btnpatientInformation").click(function(){                        
//                        w=window.open();
//                        w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
//                        w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
//                        w.document.write('<style type="text/css">.info th{font-size: 10px;}.info td{font-size: 10px;}.table th{font-size: 10px;}.table td{font-size: 10px;}</style>');
//                        w.document.write($("#patientInformation").html());
//                        w.document.close();
//                        try
//                        {
//                            //Run some code here                                                                                                       
//                            jsPrintSetup.setSilentPrint(1);
//                            jsPrintSetup.printWindow(w);
//                        }
//                        catch(err)
//                        {
//                            //Handle errors here                                    
//                            w.print();                                     
//                        } 
//                        w.close();
//                    });
//                });
//                return sPre;
//            },
//            "aoColumnDefs": [
//                {
//                "sType": "numeric", "aTargets": [ 0 ],
//                "bSortable": false, "aTargets": [ 0,-1 ]
//                }
//            ]
//        });
//        setInterval(function() {
//            oCache.iCacheLower = -1;
//            oTablePatientIpdList.fnDraw(false);
//        },60*1000);
    });
</script>
<h1 class="title"><?php __('Patient OPD Payment');?>

</h1>
<div id="dynamic">
    <table id="paymentList11" class="table" cellspacing="0">
                <thead>
                    <tr>
                        <th class="first"><?php echo TABLE_NO; ?></th>
                        <th><?php echo PATIENT_CODE; ?></th>
                        <th><?php echo PATIENT_NAME; ?></th>                
                        <th><?php echo TABLE_SEX; ?></th>
                        <th><?php echo TABLE_DOB; ?></th>
                        <th><?php echo TABLE_TELEPHONE; ?></th>          
                        <th><?php echo OTHER_REQUESTED_DATE; ?></th>
                        <th><?php echo ACTION_ACTION; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="8" class="dataTables_empty first"><?php echo TABLE_LOADING; ?></td>
                    </tr>
                </tbody>
    </table>
</div>
<br />
<br />
<br />
<!--<h1 class="title"><?php __(DASHBOARD_DEBT_LIST);?></h1>
<div id="dynamic">
    <table id="debtList" class="table" cellspacing="0">
        <thead>
            <tr>
                <th class="first"><?php echo TABLE_NO; ?></th>
                <th><?php echo REPORT_INVOICE_CODE; ?></th>
                <th><?php echo PATIENT_CODE; ?></th>
                <th><?php echo PATIENT_NAME; ?></th>
                <th><?php echo TABLE_SEX; ?></th>
                <th><?php echo TABLE_DOB; ?></th>
                <th><?php echo TABLE_TELEPHONE; ?></th>            
                <th><?php echo ACTION_ACTION; ?></th>
            </tr>
        </head>
        <tbody>
        <tr>
            <td colspan="8" class="dataTables_empty"><?php echo TABLE_LOADING; ?></td>
        </tr>
        </tbody>
    </table>
</div>-->
<br />
<br />
<br />
<!--<h1 class="title"><?php __('Patient IPD Payment');?></h1>
<div id="dynamic">
    <table id="patientIpd" class="table" cellspacing="0">
        <thead>
            <tr>
                <th class="first"><?php echo TABLE_NO; ?></th>
                <th><?php echo TABLE_HN; ?></th>
                <th><?php echo PATIENT_CODE; ?></th>
                <th><?php echo PATIENT_NAME; ?></th>
                <th><?php echo TABLE_SEX; ?></th>
                <th><?php echo TABLE_DOB; ?></th>
                <th><?php echo TABLE_TELEPHONE; ?></th>                
                <th><?php echo ACTION_ACTION; ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="8" class="dataTables_empty"><?php echo TABLE_LOADING; ?></td>
            </tr>
        </tbody>
    </table>
</div>-->
<br />
<br />
<br />
<!--<h1 class="title"><?php __(DASHBOARD_INVOICE_LIST_TODAY);?></h1>
<div id="dynamic">
    <table id="invoiceList" class="table" cellspacing="0">
        <thead>
        <tr>
            <th class="first"><?php echo TABLE_NO; ?></th>
            <th><?php echo REPORT_INVOICE_CODE; ?></th>
            <th><?php echo PATIENT_NAME; ?></th>
            <th><?php echo TABLE_SEX; ?></th>
            <th><?php echo GENERAL_AMOUNT; ?> ($)</th>
            <th><?php echo GENERAL_DISCOUNT; ?> ($)</th>
            <th><?php echo ACTION_ACTION; ?></th>
        </tr>
        </head>
        <tbody>
        <tr>
            <td colspan="7" class="dataTables_empty"><?php echo TABLE_LOADING; ?></td>
        </tr>
        </tbody>
    </table>
</div>-->
<div id="dialog" title=""></div>