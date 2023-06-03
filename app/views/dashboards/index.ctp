<?php
// Authentication
$this->element('check_access');
// Product
$viewProductReorderLevel  = checkAccess($user['User']['id'], 'products', 'viewProductReorderLevel');
$viewProductExpireDate  = checkAccess($user['User']['id'], 'products', 'viewProductExpireDate');
$viewAdjIssued  = checkAccess($user['User']['id'], 'inv_adjs', 'viewAdjustmentIssued');
$viewTotalSales = checkAccess($user['User']['id'], 'dashboards', 'viewTotalSales');
$viewExpense    = checkAccess($user['User']['id'], 'dashboards', 'viewExpenseGraph');
$viewSalesTop10 = checkAccess($user['User']['id'], 'dashboards', 'viewSalesTop10Graph');
$viewProfitLoss = checkAccess($user['User']['id'], 'dashboards', 'viewProfitLoss');
$viewReceivable = checkAccess($user['User']['id'], 'dashboards', 'viewReceivable');
$viewPayable    = checkAccess($user['User']['id'], 'dashboards', 'viewPayable');

$allowQueueCashier = checkAccess($user['User']['id'], 'cashiers', 'dashboard');
$allowQueueDoctor = checkAccess($user['User']['id'], 'doctors', 'dashboard');
$allowQueueLabo = checkAccess($user['User']['id'], 'labos', 'queueLabo');
$allowQueueEchoDoctor = checkAccess($user['User']['id'], 'echo_services', 'echoServiceDoctor');
$allowQueueNurse = checkAccess($user['User']['id'], 'patient_vital_signs', 'dashboard');
$allowQueueXrayDoctor = checkAccess($user['User']['id'], 'xray_services', 'xrayServiceDoctor');
$allowQueueMidWifeDoctor = checkAccess($user['User']['id'], 'mid_wife_services', 'midWifeServiceDoctor');
$allowAppointment = checkAccess($user['User']['id'], 'appointments', 'dashboardAppointment');
$allowQueueCystoscopyDoctor = checkAccess($user['User']['id'], 'cystoscopy_services', 'cystoscopyServiceDoctor');

$listDashboard  = array();
?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<script type="text/javascript">
    var oTablePaymentList;
    var oTableQueueLabo;  
    var oTableQueue;
    var oTableDebtList;
    var oTablePatientIpdList;
    var oTableInvoiceList;
    var oTableQueueNurse;
    var oTableQueueEchoDoctor;
    var oTableQueueXrayDoctor;
    var oTableQueueCystoscopyDoctor;
    var oTableQueueMidWifeDoctor;
    var oTablePatientAppDashboard;
    $(document).ready(function(){
        
        $('#queueDoctorDate, #queueNurseDate, #queueLaboDate, #queueAppointmentDate').datepicker({
            dateFormat:'dd/mm/yy',
            changeMonth: true,
            changeYear: true
        }).unbind("blur");
        
        
        // check request data in queue opd payment
        $(".reloadQueueCashier").click(function(event){    
            $(".reloadQueueCashier").attr("src","<?php echo $this->webroot; ?>img/layout/spinner.gif");             
            oCache.iCacheLower = -1;
            /**
            * This script use for display patient payment list.
            */
            oTablePaymentList = $("#paymentList").dataTable({
               "bProcessing": true,
               "bServerSide": true,
               "sAjaxSource": "<?php echo $this->base . '/cashiers'; ?>/dashboardPaymentAjax/",
               "fnServerData": fnDataTablesPipeline,
               "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                   $(".reloadQueueCashier").attr("src","<?php echo $this->webroot; ?>img/button/refresh-active.png");
                   $(".table td:first-child").addClass('first');
                   
                   // Double Click Checkout
                    $("#paymentList tr").click(function(){
                        changeBackgroupQueue("paymentList");
                        $(this).closest("tr").css('background','#eeeca9');
                    });
                    $("#paymentList tr").dblclick(function(){                                                                        
                        var id = $(this).find(".btnCheckOut").attr('rel');                      
                        var leftPanel = $("#paymentList").parent().parent().parent();
                        var rightPanel = leftPanel.parent().find(".rightPanel");
                        leftPanel.hide("slide", {direction: "left"}, 500, function() {
                           rightPanel.show();
                        });
                        rightPanel.html("<?php echo ACTION_LOADING; ?>");
                        rightPanel.load("<?php echo $this->base; ?>/cashiers/checkout/" + id);
                    });
                   
                   
                   $(".btnCheckOut").click(function(event) {
                       event.preventDefault();
                       var id = $(this).attr('rel');
                       var leftPanel = $(this).parent().parent().parent().parent().parent().parent().parent();
                       var rightPanel = leftPanel.parent().find(".rightPanel");
                       leftPanel.hide("slide", {direction: "left"}, 500, function() {
                           rightPanel.show();
                       });
                       rightPanel.html("<?php echo ACTION_LOADING; ?>");
                       rightPanel.load("<?php echo $this->base; ?>/cashiers/checkout/" + id);
                   });
                   
                   $(".btnCancelPayment").click(function(event){
                        event.preventDefault();
                        var id = $(this).attr('rel');
                        var name = $(this).attr('title');
                        $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Are you sure you want to void this payment of ' + name + '?</p>');
                        $("#dialog").dialog({
                            title: 'Void Payment',
                            resizable: false,
                            modal: true,
                            width: 'auto',
                            height: 'auto',
                            buttons: {
                                '<?php echo ACTION_VOID; ?>': function() {
                                    $.ajax({
                                        type: "GET",
                                        url: "<?php echo $this->base . '/cashiers'; ?>/voidPayment/" + id,
                                        data: "",
                                        beforeSend: function(){
                                            $("#dialog").dialog("close");
                                            $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                                        },
                                        success: function(result){
                                            $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                            oCache.iCacheLower = -1;
                                            oTablePaymentList.fnDraw(false);
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
                                    $( this ).dialog( "close" );
                                }
                            }
                        });
                    });

                   return sPre;
               },
               "aaSorting": [[ 8, "asc" ]],
               "aoColumnDefs": [
                   {
                   "sType": "numeric", "aTargets": [ 0 ],
                   "bSortable": false, "aTargets": [ 0,-1 ]
                   }
               ],
               "bDestroy": true
            });
        });
        // Dashboard Appointment
        $(".reloadAppointment").click(function(event){  
            $("#queueAppointmentDate").datepicker("option", "dateFormat", "yy-mm-dd");    
            $(".reloadAppointment").attr("src","<?php echo $this->webroot; ?>img/layout/spinner.gif");       
            oCache.iCacheLower = -1;
            oTablePatientAppDashboard = $("#appointment").dataTable({
               "bProcessing": true,
               "bServerSide": true,
               "sAjaxSource": "<?php echo $this->base ; ?>/appointments/dashboardAppointmentAjax/" + $("#queueAppointmentDate").val(),
               "fnServerData": fnDataTablesPipeline,
               "fnInfoCallback": function(oSettings, iStart, iEnd, iMax, iTotal, sPre) {
                    $(".reloadAppointment").attr("src","<?php echo $this->webroot; ?>img/button/refresh-active.png");
                    $(".table td:first-child").addClass('first');
                    $("#queueAppointmentDate").datepicker("option", "dateFormat", "dd/mm/yy");
                    // Double Click Appointment
                    $("#appointment tr").click(function(){
                        changeBackgroupQueue("appointment");
                        $(this).closest("tr").css('background','#eeeca9');
                    });
                    $("#appointment tr").dblclick(function(){                                                                        
                        var id = $(this).find(".btnReturnAppoDashboard").attr('rel');
                        var name = $(this).find(".btnReturnAppoDashboard").attr('title');
                        var doctorId = $(this).find(".btnReturnAppoDashboard").attr('doctor-id');                              
                        $.ajax({
                            type: "GET",
                            url: "<?php echo $this->base . '/' . 'patients'; ?>/returnPatient/appDashboard/",                            
                            data: "id=" + id +"&doctorId=" + doctorId,
                            beforeSend: function(){
                                $("#dialog").html('<p style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                            },
                            success: function(msg){
                                oCache.iCacheLower = -1;
                                oTablePatientAppDashboard.fnDraw(false);
                                $("#dialog").html(msg);
                            }
                        });
                        $("#dialog").dialog({
                            title: name,
                            resizable: false,
                            modal: true,
                            width: '500',
                            height: '315',
                            buttons: {
                                Cancel: function() {
                                    $( this ).dialog( "close" );
                                }
                            }
                        });
                    });
                     
                    $(".btnReturnAppoDashboard").click(function(event){
                        event.preventDefault();
                        var id = $(this).attr('rel');
                        var name = $(this).attr('title');
                        var doctorId = $(this).attr('doctor-id');
                        $.ajax({
                            type: "GET",
                            url: "<?php echo $this->base . '/' . 'patients'; ?>/returnPatient/appDashboard/",                            
                            data: "id=" + id +"&doctorId=" + doctorId,
                            beforeSend: function(){
                                $("#dialog").html('<p style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                            },
                            success: function(msg){
                                oCache.iCacheLower = -1;
                                oTablePatientAppDashboard.fnDraw(false);
                                $("#dialog").html(msg);
                            }
                        });
                        $("#dialog").dialog({
                            title: name,
                            resizable: false,
                            modal: true,
                            width: '500',
                            height: '315',
                            buttons: {
                                Cancel: function() {
                                    $( this ).dialog( "close" );
                                }
                            }
                        });
                    });
                
                    $(".btnEditAppointment").click(function(event){
                        event.preventDefault();
                        var id = $(this).attr('rel');
                        var leftPanel  = $(this).parent().parent().parent().parent().parent().parent().parent();
                        var rightPanel = leftPanel.parent().find(".rightPanel");
                        leftPanel.hide("slide", { direction: "left" }, 500, function() {
                            rightPanel.show();
                        });
                        rightPanel.html("<?php echo ACTION_LOADING; ?>");
                        rightPanel.load("<?php echo $this->base; ?>/appointments/edit/"+id);
                    });
                     
                    $(".btnCancelAppointment").click(function(event){
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
                            open: function(event, ui){
                                $(".ui-dialog-buttonpane").show();
                            },
                            buttons: {
                                '<?php echo ACTION_DELETE; ?>': function() {
                                    $.ajax({
                                        type: "GET",
                                        url: "<?php echo $this->base; ?>/appointments/cancelAppointment/" + id,
                                        data: "",
                                        beforeSend: function(){
                                            $("#dialog").dialog("close");
                                            $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                                        },
                                        success: function(result){
                                            $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                            oCache.iCacheLower = -1;
                                            oTablePatientAppDashboard.fnDraw(false);
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
                   return sPre;
               },
               "aaSorting": [[ 5, "asc" ]],
               "aoColumnDefs": [
                   {
                   "sType": "numeric", "aTargets": [ 0 ],
                   "bSortable": false, "aTargets": [ 0,-1,-3 ]
                   }
               ],
               "bDestroy": true
           });
        });     
        // check request data in queue debt list
        $(".reloadQueueDebtList").click(function(event){        
            $(".reloadQueueDebtList").attr("src","<?php echo $this->webroot; ?>img/layout/spinner.gif");      
            oCache.iCacheLower = -1;
            oTableDebtList = $("#debtList").dataTable({
               "bProcessing": true,
               "bServerSide": true,
               "sAjaxSource": "<?php echo $this->base . '/cashiers'; ?>/cashierDebtAjax/",
               "fnServerData": fnDataTablesPipeline,
               "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                   $(".reloadQueueDebtList").attr("src","<?php echo $this->webroot; ?>img/button/refresh-active.png");
                   $(".table td:first-child").addClass('first');
                   // Double Click Debt
                    $("#debtList tr").click(function(){
                        changeBackgroupQueue("debtList");
                        $(this).closest("tr").css('background','#eeeca9');
                    });
                    $("#debtList tr").dblclick(function(){                                                                        
                        var id = $(this).find(".btnCheckOutDebt").attr('rel');                      
                        var leftPanel = $("#debtList").parent().parent().parent();
                        var rightPanel = leftPanel.parent().find(".rightPanel");
                        leftPanel.hide("slide", {direction: "left"}, 500, function() {
                           rightPanel.show();
                        });
                        rightPanel.html("<?php echo ACTION_LOADING; ?>");
                        rightPanel.load("<?php echo $this->base; ?>/cashiers/checkoutDebt/" + id);
                    });
                   
                    $(".btnCheckOutDebt").click(function(event) {
                        event.preventDefault();
                        var id = $(this).attr('rel');
                        var leftPanel = $(this).parent().parent().parent().parent().parent().parent().parent();
                        var rightPanel = leftPanel.parent().find(".rightPanel");
                        leftPanel.hide("slide", {direction: "left"}, 500, function() {
                            rightPanel.show();
                        });
                        rightPanel.html("<?php echo ACTION_LOADING; ?>");
                        rightPanel.load("<?php echo $this->base; ?>/cashiers/checkoutDebt/" + id);
                    });
                    return sPre;
               },
               "aaSorting": [[ 7, "asc" ]],
               "aoColumnDefs": [
                   {"sType": "numeric", "aTargets": [0]}
               ],
               "bDestroy": true
           });
        });
          
        // check request data in queue doctor
        $(".reloadQueueDoctor").click(function(event){  
            $("#queueDoctorDate").datepicker("option", "dateFormat", "yy-mm-dd");
            $(".reloadQueueDoctor").attr("src","<?php echo $this->webroot; ?>img/layout/spinner.gif");      
            oCache.iCacheLower = -1;
            /**
            * This script use for display queue list for doctor.
            */
           oTableQueue = $("#queueList").dataTable({
               "bProcessing": true,
               "bServerSide": true,
               "sAjaxSource": "<?php echo $this->base . '/doctors'; ?>/dashboardPatientQueueAjax/"+$("#queueDoctorDate").val(),
               "fnServerData": fnDataTablesPipeline,
               "fnInfoCallback": function(oSettings, iStart, iEnd, iMax, iTotal, sPre) {
                   $(".reloadQueueDoctor").attr("src","<?php echo $this->webroot; ?>img/button/refresh-active.png");
                   $(".table td:first-child").addClass('first');
                    $("#queueDoctorDate").datepicker("option", "dateFormat", "dd/mm/yy");
                    // Double Click Consultation
                    $("#queueList tr").click(function(){
                        changeBackgroupQueue("queueList");
                        $(this).closest("tr").css('background','#eeeca9');
                    });
                    $("#queueList tr").dblclick(function(){                                                                        
                        var queueId = $(this).find(".btnConsultation").attr('rel');
                        var queueDoctorId = $(this).find(".btnConsultation").attr('queueDoctorId');                        
                        var leftPanel = $("#queueList").parent().parent().parent();
                        var rightPanel = leftPanel.parent().find(".rightPanel");
                        leftPanel.hide("slide", {direction: "left"}, 500, function() {
                           rightPanel.show();
                        });
                        rightPanel.html("<?php echo ACTION_LOADING; ?>");
                        rightPanel.load("<?php echo $this->base; ?>/doctors/consultation/" + queueDoctorId + "/" + queueId);
                    });
             
                    $(".btnPrintPatientCard").click(function(event){
                        event.preventDefault();
                        var id = $(this).attr('rel');
                        $.ajax({
                            type: "POST",
                            url: "<?php echo $this->base; ?>/patients/printPatientCard/" + id,
                            beforeSend: function(){
                                $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                            },
                            success: function(printInvoiceResult){
                                w = window.open();
                                w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                                w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                                w.document.write(printInvoiceResult);
                                w.document.close();
                                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                            }
                        });
                    });  

                   $(".btnConsultation").click(function(event) {
                       event.preventDefault();
                       var queueId = $(this).attr('rel');
                       var queueDoctorId = $(this).attr('queueDoctorId');
                       var leftPanel = $(this).parent().parent().parent().parent().parent().parent().parent();
                       var rightPanel = leftPanel.parent().find(".rightPanel");
                       leftPanel.hide("slide", {direction: "left"}, 500, function() {
                           rightPanel.show();
                       });
                       rightPanel.html("<?php echo ACTION_LOADING; ?>");
                       rightPanel.load("<?php echo $this->base; ?>/doctors/consultation/" + queueDoctorId + "/" + queueId);
                   });
                   return sPre;
               },
               "aaSorting": [[ 8, "asc" ]],
               "aoColumnDefs": [{
                   "sType": "numeric", "aTargets": [0],
                   "bSortable": false, "aTargets": [0, -1]
               }],
               "bDestroy": true
           });
        });
        
        // check request data in queue labo
        $(".reloadQueueLabo").click(function(event){              
            $(".reloadQueueLabo").attr("src","<?php echo $this->webroot; ?>img/layout/spinner.gif");
            $("#queueLaboDate").datepicker("option", "dateFormat", "yy-mm-dd");   
            oCache.iCacheLower = -1;
            /**
            * This script use for display queue list for labo.
            */
            oTableQueueLabo = $("#queueListLabo").dataTable({
              "bProcessing": true,
              "bServerSide": true,
              "sAjaxSource": "<?php echo $this->base . '/labos'; ?>/queueLaboAjax/"+ $("#queueLaboDate").val(),
              "fnServerData": fnDataTablesPipeline,
              "fnInfoCallback": function(oSettings, iStart, iEnd, iMax, iTotal, sPre) {
                  $("#queueListLabo td:first-child").addClass('first');
                  $("#queueListLabo td:last-child").css("white-space", "nowrap");
                  $(".reloadQueueLabo").attr("src","<?php echo $this->webroot; ?>img/button/refresh-active.png");
                  $("#queueLaboDate").datepicker("option", "dateFormat", "dd/mm/yy");
                  
                  $(".btnLaboRequest").click(function(event) {
                      event.preventDefault();
                      var id = $(this).attr('rel');
                      var leftPanel = $(this).parent().parent().parent().parent().parent().parent().parent();
                      var rightPanel = leftPanel.parent().find(".rightPanel");
                      leftPanel.hide("slide", {direction: "left"}, 500, function() {
                          rightPanel.show();
                      });
                      rightPanel.html("<?php echo ACTION_LOADING; ?>");
                      rightPanel.load("<?php echo $this->base; ?>/labos/laboRequest/" + id);
                  });

                  $(".btnBloodTest").click(function(event) {
                      event.preventDefault();
                      var id = $(this).attr('rel');
                      var leftPanel = $(this).parent().parent().parent().parent().parent().parent().parent();
                      var rightPanel = leftPanel.parent().find(".rightPanel");
                      leftPanel.hide("slide", {direction: "left"}, 500, function() {
                          rightPanel.show();
                      });
                      rightPanel.html("<?php echo ACTION_LOADING; ?>");
                      rightPanel.load("<?php echo $this->base; ?>/labos/bloodTest/" + id);
                  });

                  return sPre;
              },
              "aaSorting": [[ 5, "asc" ]],
              "aoColumnDefs": [
                  {"sType": "numeric", "aTargets": [0, -1]}
              ],
              "bDestroy": true
          });
       });
       
       
        // check request data in queue nurse
        $(".reloadQueueNurse").click(function(event){      
            $(".reloadQueueNurse").attr("src","<?php echo $this->webroot; ?>img/layout/spinner.gif");  
            $("#queueNurseDate").datepicker("option", "dateFormat", "yy-mm-dd");     
            oCache.iCacheLower = -1;
            oTableQueueNurse = $("#queueListNurse").dataTable({
               "bProcessing": true,
               "bServerSide": true,
               "sAjaxSource": "<?php echo $this->base . '/patient_vital_signs'; ?>/dashboardPatientQueueAjax/" + $("#queueNurseDate").val(),
               "fnServerData": fnDataTablesPipeline,
               "fnInfoCallback": function(oSettings, iStart, iEnd, iMax, iTotal, sPre) {
                   $(".reloadQueueNurse").attr("src","<?php echo $this->webroot; ?>img/button/refresh-active.png");
                   $(".table td:first-child").addClass('first');
                   $("#queueNurseDate").datepicker("option", "dateFormat", "dd/mm/yy");
                   // Double Click nurse
                    $("#queueListNurse tr").click(function(){
                        changeBackgroupQueue("queueListNurse");
                        $(this).closest("tr").css('background','#eeeca9');
                    });
                    $("#queueListNurse tr").dblclick(function(){                                                                        
                        var queueId = $(this).find(".btnVitalSign").attr('rel');
                        var queueDoctorId = $(this).find(".btnVitalSign").attr('queueDoctorId');   
                        var patientVitalSignId = $(this).find(".btnVitalSign").attr('patientVitalSignId');                     
                        var leftPanel = $("#queueListNurse").parent().parent().parent();
                        var rightPanel = leftPanel.parent().find(".rightPanel");
                        leftPanel.hide("slide", {direction: "left"}, 500, function() {
                           rightPanel.show();
                        });
                        rightPanel.html("<?php echo ACTION_LOADING; ?>");
                        rightPanel.load("<?php echo $this->base; ?>/patient_vital_signs/vitalSign/" + queueDoctorId + "/" + queueId + "/" + patientVitalSignId);
                    });
                    $(".btnVitalSign").click(function(event) {
                       event.preventDefault();
                       var queueId = $(this).attr('rel');
                       var queueDoctorId = $(this).attr('queueDoctorId');
                       var patientVitalSignId = $(this).attr('patientVitalSignId');
                       var leftPanel = $(this).parent().parent().parent().parent().parent().parent().parent();
                       var rightPanel = leftPanel.parent().find(".rightPanel");
                       leftPanel.hide("slide", {direction: "left"}, 500, function() {
                           rightPanel.show();
                       });
                       rightPanel.html("<?php echo ACTION_LOADING; ?>");
                       rightPanel.load("<?php echo $this->base; ?>/patient_vital_signs/vitalSign/" + queueDoctorId + "/" + queueId + "/" + patientVitalSignId);
                    });


                    $(".btnConsultationNurse").click(function(event) {
                       event.preventDefault();
                       var queueId = $(this).attr('rel');
                       var queueDoctorId = $(this).attr('queueDoctorId');
                       var leftPanel = $(this).parent().parent().parent().parent().parent().parent().parent();
                       var rightPanel = leftPanel.parent().find(".rightPanel");
                       leftPanel.hide("slide", {direction: "left"}, 500, function() {
                           rightPanel.show();
                       });
                       rightPanel.html("<?php echo ACTION_LOADING; ?>");
                       rightPanel.load("<?php echo $this->base; ?>/doctors/consultationNurse/" + queueDoctorId + "/" + queueId);
                    });

                    $(".btnCancelDoctor").click(function(event) {
                       event.preventDefault();
                       var id = $(this).attr('queueDoctorId');
                       var queueId = $(this).attr('queueId');
                       var patientId = $(this).attr('patientId');
                       var name = $(this).attr('name');
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
                                       url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/cancelDoctor/" + id + "/" + queueId,
                                       data: "",
                                       beforeSend: function() {
                                           $("#dialog").dialog("close");
                                           $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                                       },
                                       success: function(result) {
                                           $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                           oCache.iCacheLower = -1;
                                           oTableQueueNurse.fnDraw(false);
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
                                                       $('#dialog').dialog("destroy");
                                                       $(this).dialog("close");
                                                   }
                                               }
                                           });
                                       }
                                   });
                               },
                               '<?php echo ACTION_CANCEL; ?>': function() {
                                   $('#dialog').dialog("destroy");
                                   $(this).dialog("close");
                               }
                           }
                       });
                   });

                   $(".btnReturn").click(function(event){
                       event.preventDefault();
                       var queueDoctorId = $(this).attr('queueDoctorId');
                       var queueId = $(this).attr('queueId');
                       var patientId = $(this).attr('patientId');
                       var name = $(this).attr('title');
                       $.ajax({
                           type: "GET",
                           url: "<?php echo $this->base . '/patients'; ?>/returnPatient/patient/change/"+queueDoctorId+"/"+queueId,
                           data: "id=" + patientId,
                           beforeSend: function(){
                               $("#dialog").html('<p style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                           },
                           success: function(msg){
                               oCache.iCacheLower = -1;
                               oTableQueueNurse.fnDraw(false);
                               $("#dialog").html(msg);
                           }
                       });
                       $("#dialog").dialog({
                           title: name,
                           resizable: false,
                           modal: true,
                           width: '500',
                           height: '315',
                           buttons: {
                               Cancel: function() {
                                   $( this ).dialog( "close" );
                               }
                           }
                       });
                   });

                   return sPre;
               },
               "aaSorting": [[ 6, "asc" ]],
               "aoColumnDefs": [{
                   "sType": "numeric", "aTargets": [0],
                   "bSortable": false, "aTargets": [0, -1]
               }],
               "bDestroy": true
           });
        });
        
        
        // check request data in queue echo
        $(".reloadQueueEcho").click(function(event){    
            $(".reloadQueueEcho").attr("src","<?php echo $this->webroot; ?>img/layout/spinner.gif");   
            oCache.iCacheLower = -1;
            oTableQueueEchoDoctor = $("#queueListEchoDoctor").dataTable({
               "bProcessing": true,
               "bServerSide": true,
               "sAjaxSource": "<?php echo $this->base . '/echo_services'; ?>/echoServiceDoctorAjax/",
               "fnServerData": fnDataTablesPipeline,
               "fnInfoCallback": function(oSettings, iStart, iEnd, iMax, iTotal, sPre) {
                   $(".reloadQueueEcho").attr("src","<?php echo $this->webroot; ?>img/button/refresh-active.png");
                   $(".table td:first-child").addClass('first');
                   $(".btnEchoServiceDoctor").click(function(event) {
                       event.preventDefault();
                       var queueId = $(this).attr('rel');
                       var queueDoctorId = $(this).attr('queueDoctorId');
                       var leftPanel = $(this).parent().parent().parent().parent().parent().parent().parent();
                       var rightPanel = leftPanel.parent().find(".rightPanel");
                       leftPanel.hide("slide", {direction: "left"}, 500, function() {
                           rightPanel.show();
                       });
                       rightPanel.html("<?php echo ACTION_LOADING; ?>");
                       rightPanel.load("<?php echo $this->base; ?>/echo_services/addEchoServiceDoctor/" + queueDoctorId + "/" + queueId);
                   });
                   $(".btnEchoServiceObstetniqueDoctor").click(function(event) {
                       event.preventDefault();
                       var queueId = $(this).attr('rel');
                       var queueDoctorId = $(this).attr('queueDoctorId');
                       var leftPanel = $(this).parent().parent().parent().parent().parent().parent().parent();
                       var rightPanel = leftPanel.parent().find(".rightPanel");
                       leftPanel.hide("slide", {direction: "left"}, 500, function() {
                           rightPanel.show();
                       });
                       rightPanel.html("<?php echo ACTION_LOADING; ?>");
                       rightPanel.load("<?php echo $this->base; ?>/echo_services/addEchoServiceObstetniqueDoctor/" + queueDoctorId + "/" + queueId);
                   });
                   $(".btnEchoServiceCardiaqueDoctor").click(function(event) {
                       event.preventDefault();
                       var queueId = $(this).attr('rel');
                       var queueDoctorId = $(this).attr('queueDoctorId');
                       var leftPanel = $(this).parent().parent().parent().parent().parent().parent().parent();
                       var rightPanel = leftPanel.parent().find(".rightPanel");
                       leftPanel.hide("slide", {direction: "left"}, 500, function() {
                           rightPanel.show();
                       });
                       rightPanel.html("<?php echo ACTION_LOADING; ?>");
                       rightPanel.load("<?php echo $this->base; ?>/echo_services/addEchoServiceCardiaqueDoctor/" + queueDoctorId + "/" + queueId);
                   });

                   return sPre;
               },
               "aaSorting": [[ 5, "asc" ]],
               "aoColumnDefs": [
                   {"sType": "numeric", "aTargets": [0]}
               ],
               "bDestroy": true
           });
        });
        
        
        // check request data in queue xray
        $(".reloadQueueXray").click(function(event){     
            $(".reloadQueueXray").attr("src","<?php echo $this->webroot; ?>img/layout/spinner.gif");          
            oCache.iCacheLower = -1;
            oTableQueueXrayDoctor = $("#queueListXrayDoctor").dataTable({
               "bProcessing": true,
               "bServerSide": true,
               "sAjaxSource": "<?php echo $this->base . '/xray_services'; ?>/xrayServiceDoctorAjax/",
               "fnServerData": fnDataTablesPipeline,
               "fnInfoCallback": function(oSettings, iStart, iEnd, iMax, iTotal, sPre) {
                   $(".reloadQueueXray").attr("src","<?php echo $this->webroot; ?>img/button/refresh-active.png");
                   $(".table td:first-child").addClass('first');
                   $(".btnXrayServiceDoctor").click(function(event) {
                       event.preventDefault();
                       var queueId = $(this).attr('rel');
                       var queueDoctorId = $(this).attr('queueDoctorId');
                       var leftPanel = $(this).parent().parent().parent().parent().parent().parent().parent();
                       var rightPanel = leftPanel.parent().find(".rightPanel");
                       leftPanel.hide("slide", {direction: "left"}, 500, function() {
                           rightPanel.show();
                       });
                       rightPanel.html("<?php echo ACTION_LOADING; ?>");
                       rightPanel.load("<?php echo $this->base; ?>/xray_services/addXrayServiceDoctor/" + queueDoctorId + "/" + queueId);
                   });

                   return sPre;
               },
               "aaSorting": [[ 5, "asc" ]],
               "aoColumnDefs": [
                   {"sType": "numeric", "aTargets": [0],"bSortable": false, "aTargets": [ 0,-1 ]}
               ],
               "bDestroy": true
           });
        });
        
        // check request data in queue xray
        $(".reloadQueueCysto").click(function(event){     
            $(".reloadQueueCysto").attr("src","<?php echo $this->webroot; ?>img/layout/spinner.gif");          
            oCache.iCacheLower = -1;
            oTableQueueCystoscopyDoctor = $("#queueListCystoscopyDoctor").dataTable({
               "bProcessing": true,
               "bServerSide": true,
               "sAjaxSource": "<?php echo $this->base . '/cystoscopy_services'; ?>/cystoscopyServiceDoctorAjax/",
               "fnServerData": fnDataTablesPipeline,
               "fnInfoCallback": function(oSettings, iStart, iEnd, iMax, iTotal, sPre) {
                   $(".reloadQueueCysto").attr("src","<?php echo $this->webroot; ?>img/button/refresh-active.png");
                   $(".table td:first-child").addClass('first');
                   $(".btnCysServiceDoctor").click(function(event) {
                       event.preventDefault();
                       var queueId = $(this).attr('rel');
                       var queueDoctorId = $(this).attr('queueDoctorId');
                       var leftPanel = $(this).parent().parent().parent().parent().parent().parent().parent();
                       var rightPanel = leftPanel.parent().find(".rightPanel");
                       leftPanel.hide("slide", {direction: "left"}, 500, function() {
                           rightPanel.show();
                       });
                       rightPanel.html("<?php echo ACTION_LOADING; ?>");
                       rightPanel.load("<?php echo $this->base; ?>/cystoscopy_services/addCystoscopyServiceDoctor/" + queueDoctorId + "/" + queueId);
                   });

                   return sPre;
               },
               "aaSorting": [[ 5, "asc" ]],
               "aoColumnDefs": [
                   {"sType": "numeric", "aTargets": [0],"bSortable": false, "aTargets": [ 0,-1 ]}
               ],
               "bDestroy": true
           });
        });
        
        
        // check request data in queue mid wife
        $(".reloadQueueMidWife").click(function(event){      
            $(".reloadQueueMidWife").attr("src","<?php echo $this->webroot; ?>img/layout/spinner.gif");       
            oCache.iCacheLower = -1;
            oTableQueueMidWifeDoctor = $("#queueListMidWifeDoctor").dataTable({
               "bProcessing": true,
               "bServerSide": true,
               "sAjaxSource": "<?php echo $this->base . '/mid_wife_services'; ?>/midWifeServiceDoctorAjax/",
               "fnServerData": fnDataTablesPipeline,
               "fnInfoCallback": function(oSettings, iStart, iEnd, iMax, iTotal, sPre) {
                   $(".reloadQueueMidWife").attr("src","<?php echo $this->webroot; ?>img/button/refresh-active.png");
                   $(".table td:first-child").addClass('first');
                   $(".btnMidWifeServiceDoctor").click(function(event) {
                       event.preventDefault();
                       var queueId = $(this).attr('rel');
                       var queueDoctorId = $(this).attr('queueDoctorId');
                       var leftPanel = $(this).parent().parent().parent().parent().parent().parent().parent();
                       var rightPanel = leftPanel.parent().find(".rightPanel");
                       leftPanel.hide("slide", {direction: "left"}, 500, function() {
                           rightPanel.show();
                       });
                       rightPanel.html("<?php echo ACTION_LOADING; ?>");
                       rightPanel.load("<?php echo $this->base; ?>/mid_wife_services/addMidWifeServiceDoctor/"+ queueId);
                   });
                   $(".btnMidWifeServiceDoctorDossierMedical").click(function(event) {
                       event.preventDefault();
                       var queueId = $(this).attr('rel');
                       var leftPanel = $(this).parent().parent().parent().parent().parent().parent().parent();
                       var rightPanel = leftPanel.parent().find(".rightPanel");
                       leftPanel.hide("slide", {direction: "left"}, 500, function() {
                           rightPanel.show();
                       });
                       rightPanel.html("<?php echo ACTION_LOADING; ?>");
                       rightPanel.load("<?php echo $this->base; ?>/mid_wife_services/addMidWifeServiceDoctorDossierMedical/"+ queueId);
                   });

                   return sPre;
               },
               "aaSorting": [[ 5, "asc" ]],
               "aoColumnDefs": [
                   {
                   "sType": "numeric", "aTargets": [ 0 ],
                   "bSortable": false, "aTargets": [ 0,-1,-3 ]
                   }
               ],
               "bDestroy": true
           });
        });
        
        // check request data in queue ipd payment
        $(".reloadQueueIPDPayment").click(function(event){  
            $(".reloadQueueIPDPayment").attr("src","<?php echo $this->webroot; ?>img/layout/spinner.gif");           
            oCache.iCacheLower = -1;
//            oTablePatientIpdList.fnDraw(false);
            /**
            * This script use for display patient ipd list.
            */
           oTablePatientIpdList = $("#patientIpd").dataTable({
               "bProcessing": true,
               "bServerSide": true,
               "sAjaxSource": "<?php echo $this->base . '/cashiers'; ?>/dashboardPatientIpdAjax/",
               "fnServerData": fnDataTablesPipeline,
               "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                   $(".reloadQueueIPDPayment").attr("src","<?php echo $this->webroot; ?>img/button/refresh-active.png");
                   $(".table td:first-child").addClass('first');                
                   $(".btnPayment").click(function(event) {
                       event.preventDefault();
                       var id = $(this).attr('rel');
                       var leftPanel = $(this).parent().parent().parent().parent().parent().parent().parent();
                       var rightPanel = leftPanel.parent().find(".rightPanel");
                       leftPanel.hide("slide", {direction: "left"}, 500, function() {
                           rightPanel.show();
                       });
                       rightPanel.html("<?php echo ACTION_LOADING; ?>");
                       rightPanel.load("<?php echo $this->base; ?>/cashiers/patientPayment/" + id);
                   });
                   return sPre;
               },
               "aaSorting": [[ 7, "asc" ]],
               "aoColumnDefs": [
                   {
                   "sType": "numeric", "aTargets": [ 0 ],
                   "bSortable": false, "aTargets": [ 0,-1 ]
                   }
               ],
               "bDestroy": true
           });
        });
	
        // check request data in queue ipd payment
        $(".reloadListInvoiceToday").click(function(event){         
            $(".reloadListInvoiceToday").attr("src","<?php echo $this->webroot; ?>img/layout/spinner.gif");             
            oCache.iCacheLower = -1;
//            oTableInvoiceList.fnDraw(false);
            /**
            * This script use for display all invoice have created today.
            */
           oTableInvoiceList = $("#invoiceList").dataTable({
               "bProcessing": true,
               "bServerSide": true,
               "sAjaxSource": "<?php echo $this->base . '/cashiers'; ?>/cashierInvoiceAjax/",
               "fnServerData": fnDataTablesPipeline,
               "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                   $(".reloadListInvoiceToday").attr("src","<?php echo $this->webroot; ?>img/button/refresh-active.png");
                   $(".table td:first-child").addClass('first');
                   // Action Reprint Invoice
                   $(".btnView").click(function(event){
                       event.preventDefault();
                       var id = $(this).attr('rel');
                       $("#dialog").html('<div class="buttons"><button type="submit" class="positive reprintPatientInvoice" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="txtReprintInvoiceSales"><?php echo ACTION_PRINT_INVOICE; ?></span></button><button type="submit" class="positive rePrintFormInvoiceVat" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="printCheckOutForm"><?php echo ACTION_PRINT_INVOICE_VAT; ?></span></button></div> ');
                       $(".reprintPatientInvoice").click(function(){
                           $.ajax({
                               type: "POST",
                               url: "<?php echo $this->base . '/cashiers'; ?>/printInvoiceReceipt/"+id,
                               beforeSend: function(){
                                   $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                               },
                               success: function(printInvoiceResult){
                                   w = window.open();
                                   w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                                   w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                                   w.document.write(printInvoiceResult);
                                   w.document.close();
                                   $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                               }
                           });
                       });           

                       $(".rePrintFormInvoiceVat").click(function(){
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
                           title: '<?php echo DIALOG_INFORMATION; ?>',
                           resizable: false,
                           modal: true,
                           width: 'auto',
                           height: 'auto',
                           position:'center',
                           open: function(event, ui){
                               $(".ui-dialog-buttonpane").show();
                           },
                           buttons: {
                               '<?php echo ACTION_CLOSE; ?>': function() {
                                   $(this).dialog("close");
                               }
                           }
                       });
                   });

                   $(".btnVoid").click(function(event){
                       event.preventDefault();
                       var id = $(this).attr('rel');
                       var name = $(this).attr('name');
                       var sts = $(this).attr('sts');
                       var stsRec = $(this).attr('stsRec');
                       // condition for check credit memo before void
                       if(sts=='0'){
                           alert('<?php echo MESSAGE_CREDIT_MEMO_BEFORE_VOID; ?>');
                           return false;
                       }
                       if(stsRec=='0'){
                           alert('<?php echo MESSAGE_VOID_RECEIPT_BEFORE_VOID; ?>');
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
                                           oTableInvoiceList.fnDraw(false);
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

                   return sPre;
               },
               "aoColumnDefs": [
                   {
                   "sType": "numeric", "aTargets": [ 0 ],
                   "bSortable": false, "aTargets": [ 0,-1 ]
                   }
               ],
               "bDestroy": true
           });
        });
        
        
        
        // Action Refresh
        $(".refreshDashboard").bind('mouseover', function(){
            $(this).attr('src', '<?php echo $this->webroot; ?>img/button/refresh-active.png');
        });

        $(".refreshDashboard").bind('mouseout', function(){
            $(this).attr('src', '<?php echo $this->webroot; ?>img/button/refresh-inactive.png');
        });
        
        // Action Minimize
        $(".minimizeDashboard").bind('mouseover', function(){
            $(this).attr('src', '<?php echo $this->webroot; ?>img/button/minimize-active.png');
        });

        $(".minimizeDashboard").bind('mouseout', function(){
            $(this).attr('src', '<?php echo $this->webroot; ?>img/button/minimize-inactive.png');
        });
        
        // Action Customize Dashboard
        $("#customizeDashboard").click(function(event){
            event.preventDefault();
            var contents = createCusDashboard();
            $("#dialog").html(contents);
            $("#dialog").dialog({
                title: 'Customize Dashboard',
                resizable: false,
                modal: true,
                width: 850,
                height: 500,
                position: 'center',
                open: function(event, ui){
                    $(".ui-dialog-buttonpane").show();
                    // Set Checkbox Style
                    $('.dashboardOption').bootstrapToggle('destroy');
                    $('.dashboardOption').bootstrapToggle({on:"Show", off:"Hide"}).change(function(){
                        var dashName = $(this).closest("tr").find(".customizeDashName").text();
                        var objDash;
                        var dis;
                        var url  = '';
                        var auto = 1;
                        var time = 5;
                        var cntView = '';
                        var comapnyView = '';
                        var filterView = '';
                        var groupView = '';
                        var chartView = '';
                        if($(this).prop('checked')){
                            dis  = 1;
                        } else {
                            dis  = 2;
                        }
                        $(".boxDashboard").each(function() {
                            var name    = $(this).find(".dashboardName").text();
                            if(name == dashName){
                                objDash = $(this);
                                if(dis == 1){
                                    objDash.attr('display', dis);
                                   $(this).show();
                                } else {
                                    objDash.attr('display', dis);
                                    $(this).hide();
                                }
                                url  = $(this).attr("role");
                                auto = $(this).find(".defaultCheckSetting").val();
                                time = $(this).find(".defaultTimeSetting").val();
                                cntView = $(this).children("div:first").first().attr("id");
                                if(objDash.find(".companyView").val() != undefined){
                                    comapnyView = objDash.find(".companyView").val()+"/";
                                }
                                if(objDash.find(".filterView").val() != undefined){
                                    filterView = objDash.find(".filterView").val()+"/";
                                }
                                if(objDash.find(".groupView").val() != undefined){
                                    groupView = objDash.find(".groupView").val()+"/";
                                }
                                if(objDash.find(".chartView").val() != undefined){
                                    chartView = objDash.find(".chartView").val();
                                }
                            }
                        });
                        $.ajax({
                            dataType: 'json',
                            type:   'GET',
                            url:    "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/userDashboard/"+url+"/"+auto+"/"+time+"/"+dis,
                            success: function(msg){
                                if(msg.result == 0){
                                    if(dis == 1){
                                        objDash.hide();
                                        objDash.attr('display', 2);
                                    } else {
                                        objDash.show();
                                        objDash.attr('display', 1);
                                    }
                                } else {
                                    if(dis == 1){
                                        $("#"+cntView).html('Loading....');
                                        $("#"+cntView).load("<?php echo $this->base; ?>/"+url+"/"+comapnyView+filterView+groupView+chartView);
                                    }
                                }
                            }
                        });
                    });
                },
                buttons: {
                    '<?php echo ACTION_CLOSE; ?>': function() {
                        $(this).dialog("close");
                    }
                }
            });
        });
        
        <?php
        if($viewProductReorderLevel){
            // Module Id 
            $sqlMod = mysql_query("SELECT id FROM modules WHERE name = 'Products Reorder Level' LIMIT 1");
            $rowMod = mysql_fetch_array($sqlMod);

            $listDashboard[$rowMod['id']] = MENU_PRODUCT_REORDER;
            $viewProductReorderLevelAuto = 1;
            $viewProductReorderLevelTime = 30;
            $viewProductReorderLevelDisp = '';
            $displayProductReorderLevel  = 1;
            $sqlDash = mysql_query("SELECT * FROM user_dashboards WHERE module_id = ".$rowMod[0]." AND user_id = {$user['User']['id']} LIMIT 1");
            if(mysql_num_rows($sqlDash)){
                $rowDash = mysql_fetch_array($sqlDash);
                $viewProductReorderLevelAuto = $rowDash['auto_refresh'];
                $viewProductReorderLevelTime = $rowDash['time_refresh'];
                if($rowDash['display'] == 2){
                    $viewProductReorderLevelDisp = 'display: none';
                }
                $displayProductReorderLevel = $rowDash['display'];
            }
            if($displayProductReorderLevel == 1) { ?>
            $("#viewProductReorderLevel").load("<?php echo $this->base; ?>/products/viewProductReorderLevel/");
            <?php        
            } 
        } 
        ?>
                
        <?php
        if($viewProductExpireDate){
            // Module Id 
            $sqlMod = mysql_query("SELECT id FROM modules WHERE name = 'Products Expire Date' LIMIT 1");
            $rowMod = mysql_fetch_array($sqlMod);

            $listDashboard[$rowMod['id']] = TABLE_PRODUCT_EXPIRED_DATE;
            $viewProductExpireDateAuto = 1;
            $viewProductExpireDateTime = 30;
            $viewProductExpireDateDisp = '';
            $displayProductExpireDate  = 1;
            $sqlDash = mysql_query("SELECT * FROM user_dashboards WHERE module_id = ".$rowMod[0]." AND user_id = {$user['User']['id']} LIMIT 1");
            if(mysql_num_rows($sqlDash)){
                $rowDash = mysql_fetch_array($sqlDash);
                $viewProductReorderLevelAuto = $rowDash['auto_refresh'];
                $viewProductReorderLevelTime = $rowDash['time_refresh'];
                if($rowDash['display'] == 2){
                    $viewProductExpireDateDisp = 'display: none';
                }
                $displayProductExpireDate = $rowDash['display'];
            }
            if($displayProductExpireDate == 1) { ?>
            $("#viewProductExpireDate").load("<?php echo $this->base; ?>/products/viewProductExpireDate/");
            <?php        
            } 
        } 
        ?>        
                
        
        <?php
        if($viewAdjIssued){
            // Module Id 
            $sqlMod = mysql_query("SELECT id FROM modules WHERE name = 'Physical Count (Issue)' OR name = 'Inventory Adjustment (Issue)' LIMIT 1");
            $rowMod = mysql_fetch_array($sqlMod);
            
            $listDashboard[$rowMod['id']] = MENU_INVENTORY_ADJUSTMENT;
            $viewAdjIssuedAuto = 1;
            $viewAdjIssuedTime = 5;
            $viewAdjIssuedDisp = '';
            $displayAdjIssued  = 1;
            $sqlDash = mysql_query("SELECT * FROM user_dashboards WHERE module_id = ".$rowMod[0]." AND user_id = {$user['User']['id']} LIMIT 1");
            if(mysql_num_rows($sqlDash)){
                $rowDash = mysql_fetch_array($sqlDash);
                $viewAdjIssuedAuto = $rowDash['auto_refresh'];
                $viewAdjIssuedTime = $rowDash['time_refresh'];
                if($rowDash['display'] == 2){
                    $viewAdjIssuedDisp = 'display: none';
                }
                $displayAdjIssued = $rowDash['display'];
            }
            if($displayAdjIssued == 1){
        ?>
        $("#adjIssuedView").load("<?php echo $this->base; ?>/inv_adjs/viewAdjustmentIssued/");
        <?php        
            }
        ?>
        $('#settingAdjIssued').makeMenu({url : "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/userDashboard/inv_adjs/viewAdjustmentIssued"});
        <?php
        }
        if($viewTotalSales){
            // Module Id 
            $sqlMod = mysql_query("SELECT id FROM modules WHERE name = 'Total Sales By Graph' LIMIT 1");
            $rowMod = mysql_fetch_array($sqlMod);

            $listDashboard[$rowMod['id']] = TABLE_TOTAL_SALES;
            $viewTotalSalesAuto = 1;
            $viewTotalSalesTime = 30;
            $viewTotalSalesDisp = '';
            $displayTotalSales  = 1;
            $sqlDash = mysql_query("SELECT * FROM user_dashboards WHERE module_id = ".$rowMod[0]." AND user_id = {$user['User']['id']} LIMIT 1");
            if(mysql_num_rows($sqlDash)){
                $rowDash = mysql_fetch_array($sqlDash);
                $viewTotalSalesAuto = $rowDash['auto_refresh'];
                $viewTotalSalesTime = $rowDash['time_refresh'];
                if($rowDash['display'] == 2){
                    $viewTotalSalesDisp = 'display: none';
                }
                $displayTotalSales = $rowDash['display'];
            }
            if($displayTotalSales == 1){
        ?>
        $("#TotalSalesView").load("<?php echo $this->base; ?>/dashboards/viewTotalSales/"+$("#filterTotalSales").val()+"/"+$("#groupTotalSales").val()+"/"+$("#chartTotalSales").val());
        <?php    
            }
        ?>
        $('#settingTotalSales').makeMenu({url : "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/userDashboard/dashboards/viewTotalSales"});
        <?php    
        }
        if($viewExpense){
            // Module Id 
            $sqlMod = mysql_query("SELECT id FROM modules WHERE name = 'Expense (Graph)' LIMIT 1");
            $rowMod = mysql_fetch_array($sqlMod);
            
            $listDashboard[$rowMod[0]] = MENU_EXPENSE;
            $viewExpenseGraphAuto  = 1;
            $viewExpenseGraphTime  = 5;
            $viewExpenseGraphDisp  = '';
            $displayExpenseGraph   = 1;
            $sqlDash = mysql_query("SELECT * FROM user_dashboards WHERE module_id = ".$rowMod[0]." AND user_id = {$user['User']['id']} LIMIT 1");
            if(mysql_num_rows($sqlDash)){
                $rowDash = mysql_fetch_array($sqlDash);
                $viewExpenseGraphAuto = $rowDash['auto_refresh'];
                $viewExpenseGraphTime = $rowDash['time_refresh'];
                if($rowDash['display'] == 2){
                    $viewExpenseGraphDisp = 'display: none';
                }
                $displayExpenseGraph = $rowDash['display'];
            }
            if($displayExpenseGraph == 1){
        ?>
        $("#expenseGraphView").load("<?php echo $this->base; ?>/dashboards/viewExpenseGraph/"+$("#filterExpenseGraph").val());
        <?php        
            }
        ?>
        $('#settingExpenseGraph').makeMenu({url : "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/userDashboard/dashboards/viewExpenseGraph"});
        <?php
        }
        if($viewSalesTop10){
            // Module Id 
            $sqlMod = mysql_query("SELECT id FROM modules WHERE name = 'Sales Top 10 Items (Graph)' LIMIT 1");
            $rowMod = mysql_fetch_array($sqlMod);
            
            $listDashboard[$rowMod[0]] = MENU_EXPENSE;
            $viewSalesTop10Auto  = 1;
            $viewSalesTop10Time  = 5;
            $viewSalesTop10Disp  = '';
            $displaySalesTop10   = 1;
            $sqlDash = mysql_query("SELECT * FROM user_dashboards WHERE module_id = ".$rowMod[0]." AND user_id = {$user['User']['id']} LIMIT 1");
            if(mysql_num_rows($sqlDash)){
                $rowDash = mysql_fetch_array($sqlDash);
                $viewSalesTop10Auto = $rowDash['auto_refresh'];
                $viewSalesTop10Time = $rowDash['time_refresh'];
                if($rowDash['display'] == 2){
                    $viewSalesTop10Disp = 'display: none';
                }
                $displaySalesTop10 = $rowDash['display'];
            }
            if($displaySalesTop10 == 1){
        ?>
        $("#salesTop10View").load("<?php echo $this->base; ?>/dashboards/viewSalesTop10Graph/"+$("#filterSalesTop10").val());
        <?php        
            }
        ?>
        $('#settingSalesTop10').makeMenu({url : "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/userDashboard/dashboards/viewSalesTop10Graph"});
        <?php
        }
        if($viewProfitLoss){
            // Module Id 
            $sqlMod = mysql_query("SELECT id FROM modules WHERE name = 'Profit & Loss (Graph)' LIMIT 1");
            $rowMod = mysql_fetch_array($sqlMod);
            
            $listDashboard[$rowMod['id']] = MENU_PROFIT_AND_LOSS;
            $viewProfitLossAuto = 1;
            $viewProfitLossTime = 5;
            $viewProfitLossDisp = '';
            $displayProfitLoss  = 1;
            $sqlDash = mysql_query("SELECT * FROM user_dashboards WHERE module_id = ".$rowMod[0]." AND user_id = {$user['User']['id']} LIMIT 1");
            if(mysql_num_rows($sqlDash)){
                $rowDash = mysql_fetch_array($sqlDash);
                $viewProfitLossAuto = $rowDash['auto_refresh'];
                $viewProfitLossTime = $rowDash['time_refresh'];
                if($rowDash['display'] == 2){
                    $viewProfitLossDisp = 'display: none';
                }
                $displayProfitLoss = $rowDash['display'];
            }
            if($displayProfitLoss == 1){
        ?>
        $("#profitLossView").load("<?php echo $this->base; ?>/dashboards/viewProfitLoss/");
        <?php        
            }
        ?>
        $('#settingProfitLoss').makeMenu({url : "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/userDashboard/dashboards/viewProfitLoss"});
        <?php
        }
        if($viewReceivable){
            // Module Id 
            $sqlMod = mysql_query("SELECT id FROM modules WHERE name = 'Total Receivables' LIMIT 1");
            $rowMod = mysql_fetch_array($sqlMod);
            
            $listDashboard[$rowMod['id']] = TABLE_TOTAL_RECEIVABLES;
            $viewReceivableAuto = 1;
            $viewReceivableTime = 5;
            $viewReceivableDisp = '';
            $displayReceivable  = 1;
            $sqlDash = mysql_query("SELECT * FROM user_dashboards WHERE module_id = ".$rowMod[0]." AND user_id = {$user['User']['id']} LIMIT 1");
            if(mysql_num_rows($sqlDash)){
                $rowDash = mysql_fetch_array($sqlDash);
                $viewReceivableAuto = $rowDash['auto_refresh'];
                $viewReceivableTime = $rowDash['time_refresh'];
                if($rowDash['display'] == 2){
                    $viewReceivableDisp = 'display: none';
                }
                $displayReceivable = $rowDash['display'];
            }
            if($displayReceivable == 1){
        ?>
        $("#receivableView").load("<?php echo $this->base; ?>/dashboards/viewReceivable/");
        <?php        
            }
        ?>
        $('#settingReceivable').makeMenu({url : "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/userDashboard/dashboards/viewReceivable"});
        <?php
        }
        if($viewPayable){
            // Module Id 
            $sqlMod = mysql_query("SELECT id FROM modules WHERE name = 'Total Payables' LIMIT 1");
            $rowMod = mysql_fetch_array($sqlMod);
            
            $listDashboard[$rowMod['id']] = TABLE_TOTAL_PAYABLES;
            $viewPayableAuto = 1;
            $viewPayableTime = 5;
            $viewPayableDisp = '';
            $displayPayable  = 1;
            $sqlDash = mysql_query("SELECT * FROM user_dashboards WHERE module_id = ".$rowMod[0]." AND user_id = {$user['User']['id']} LIMIT 1");
            if(mysql_num_rows($sqlDash)){
                $rowDash = mysql_fetch_array($sqlDash);
                $viewPayableAuto = $rowDash['auto_refresh'];
                $viewPayableTime = $rowDash['time_refresh'];
                if($rowDash['display'] == 2){
                    $viewPayableDisp = 'display: none';
                }
                $displayPayable = $rowDash['display'];
            }
            if($displayPayable == 1){
        ?>
        $("#payableView").load("<?php echo $this->base; ?>/dashboards/viewPayable/");
        <?php        
            }
        ?>
        $('#settingPayable').makeMenu({url : "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/userDashboard/dashboards/viewPayable"});
        <?php
        }
        ?>
    });
    
    function changeBackgroupQueue(tbl){
        $("#"+tbl+" tbody tr").each(function(){
                $(this).removeAttr('style');
        });
    }
    
    function createCusDashboard(){
        var i   = 0;
        var div = '';
        $(".boxDashboard").each(function() {
            if(i == 3){
                i  = 0;
                div += '<div style="clear: both;"></div>';
            }
            var display = $(this).attr("display");
            var name    = $(this).find(".dashboardName").text();
            var checked = '';
            if(display == 1){
                checked = 'checked="checked"';
            }
            div += '<div style="float: left; margin-right: 5px; heidht: 40px; width: 250px; border: 1px solid #1761c7;">';
            div += '<table cellpadding="5" cellspacing="0" style="width: 100%;">';
            div += '<tr>';
            div += '<td style="width: 70%; vertical-align: top;" class="customizeDashName">'+name+'</td>';
            div += '<td style="vertical-align: top;"><input type="checkbox" '+checked+' class="dashboardOption" data-size="small" data-toggle="toggle" /></td>';
            div += '</tr>';
            div += '</table>';
            div += '</div>';
            i++;
        });
        if(i > 5){
             div += '<div style="clear: both;"></div>';
        }
        return div;
    }
</script>
<div class="leftPanel">   
<?php
if(!empty($listDashboard)){
?>
<div class="buttons">
    <a href="#" class="positive" id="customizeDashboard">
        <img src="<?php echo $this->webroot; ?>img/button/setting-active.png" />
        Customize Dashboard
    </a>
</div>
<div style="clear: both;"></div>
<br/>
<?php
} ?>
<?php if(!empty($allowQueueDoctor)) { ?>
    <div style="margin-right: 20px;">
        <h1 class="title-no-image">
            <img style="margin-left: -40px;" src="<?php echo $this->webroot;?>img/icon/doctor.png" /> 
            <?php echo GENERAL_QUEUE_DOCTOR; ?>
            <div style="float: right; vertical-align: middle; margin: 5px 0px 5px 5px;">
                <?php
                echo TABLE_DATE;
                ?>
                <input type="text" id="queueDoctorDate" style="font-size: 11px; height: 15px;" value="<?php echo date('d/m/Y'); ?>" />
                <span id="showActionRefresh3" style="float: right;cursor: pointer;padding-left: 10px;">                    
                    <img onmouseover="Tip('Refresh')" class="reloadQueueDoctor" alt="Refresh" src="<?php echo $this->webroot;?>img/button/refresh-active.png" />                    
                </span>
            </div>
        </h1>
        <table id="queueList" class="table" cellspacing="0">
            <thead>
                <tr>
                    <th class="first"><?php echo TABLE_NO; ?></th>
                    <th><?php echo PATIENT_NAME; ?></th>                
                    <th style="width: 100px;"><?php echo TABLE_SEX; ?></th>
                    <th style="width: 100px;"><?php echo TABLE_DOB; ?></th>
                    <th><?php echo TABLE_DAIGNOSTIC; ?></th>
                    <th style="width: 100px;"><?php echo TABLE_STATUS; ?></th>
                    <th style="width: 100px;"><?php echo TABLE_ROOM_NUMBER; ?></th>
                    <th><?php echo TABLE_TELEPHONE; ?></th>
                    <th style="width: 100px;"><?php echo OTHER_REQUESTED_DATE; ?></th>  
                    <th><?php echo DOCTOR_NAME; ?></th>
                    <th style="width: 100px;"><?php echo CONSULT_CONSULTATION; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="11" class="dataTables_empty first"><?php echo MESSAGE_CLICK_BUTTON_GET_LATEST_DATA; ?></td>
                </tr>
            </tbody>
        </table>
        <div class="clear"></div>
    </div>
    <br>
<?php } ?>

<?php if(!empty($allowQueueNurse)) { ?>
    <div style="margin-right: 20px;">
        <h1 class="title-no-image">
            <img style="margin-left: -40px;" src="<?php echo $this->webroot;?>img/icon/nurse.png" /> 
            <?php __(GENERAL_QUEUE . ' Nurse'); ?>
            <div style="float: right; vertical-align: middle; margin: 5px 0px 5px 5px;">
                <?php
                echo TABLE_DATE;
                ?>
                <input type="text" id="queueNurseDate" style="font-size: 11px; height: 15px;" value="<?php echo date('d/m/Y'); ?>" />
                <span style="float: right;cursor: pointer;padding-left: 10px">                    
                    <img onmouseover="Tip('Refresh')" class="reloadQueueNurse" alt="Refresh" src="<?php echo $this->webroot;?>img/button/refresh-active.png" />                     
                </span>
            </div>
        </h1>
        <table id="queueListNurse" class="table" cellspacing="0">
                <thead>
                    <tr>
                        <th class="first"><?php echo TABLE_NO; ?></th>
                        <th><?php echo PATIENT_CODE; ?></th>
                        <th><?php echo PATIENT_NAME; ?></th>                
                        <th><?php echo TABLE_SEX; ?></th>
                        <th><?php echo TABLE_DOB; ?></th>
                        <th><?php echo TABLE_TELEPHONE; ?></th>          
                        <th><?php echo OTHER_REQUESTED_DATE; ?></th>  
                        <th><?php echo DOCTOR_NAME; ?></th>
                        <th><?php echo ACTION_ACTION; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="9" class="dataTables_empty first"><?php echo MESSAGE_CLICK_BUTTON_GET_LATEST_DATA; ?></td>
                    </tr>
                </tbody>
        </table>
        <div class="clear"></div>
    </div>
    <br>
<?php } ?>

<?php if(!empty($allowQueueCashier)) { ?>
    <div style="margin-right: 20px;">
        <h1 class="title-no-image">
            <img style="margin-left: -40px;" src="<?php echo $this->webroot;?>img/icon/make_payment.png" />
            <?php echo TITLE_MAKE_PAYMENT_OPD_PATIENT; ?>
            <span id="showActionRefresh9" style="float: right;vertical-align: middle;margin-top: 5px;cursor: pointer;">                    
                <img onmouseover="Tip('Refresh')" class="reloadQueueCashier" alt="Refresh" src="<?php echo $this->webroot;?>img/button/refresh-active.png" />                    
            </span>
        </h1>
        <table id="paymentList" class="table" cellspacing="0">
            <thead>
                <tr>
                    <th class="first"><?php echo TABLE_NO; ?></th>
                    <th><?php echo PATIENT_CODE; ?></th>
                    <th><?php echo PATIENT_NAME; ?></th>                
                    <th><?php echo TABLE_SEX; ?></th>
                    <th><?php echo TABLE_DOB; ?></th>
                    <th><?php echo TABLE_TELEPHONE; ?></th>          
                    <th style="width: 100px;"><?php echo TABLE_STATUS; ?></th>
                    <th style="width: 100px;"><?php echo TABLE_ROOM_NUMBER; ?></th>
                    <th><?php echo OTHER_REQUESTED_DATE; ?></th>
                    <th><?php echo ACTION_ACTION; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="10" class="dataTables_empty first"><?php echo MESSAGE_CLICK_BUTTON_GET_LATEST_DATA; ?></td>
                </tr>
            </tbody>
        </table>
        <div class="clear"></div>
    </div>
    <br>
<?php } ?>

<?php if(!empty($allowQueueCashier)) { ?>
    <div style="margin-right: 20px;">
        <h1 class="title-no-image">
            <img style="margin-left: -40px;" src="<?php echo $this->webroot;?>img/button/cashier.png" />
            <?php __(DASHBOARD_INVOICE_LIST_TODAY);?>
            <span id="showActionRefresh12" style="float: right;vertical-align: middle;margin-top: 5px;cursor: pointer;">                    
                <img onmouseover="Tip('Refresh')" class="reloadListInvoiceToday" alt="Refresh" src="<?php echo $this->webroot;?>img/button/refresh-active.png" />                    
            </span>
        </h1>
        <table id="invoiceList" class="table" cellspacing="0">
            <thead>
            <tr>
                <th class="first"><?php echo TABLE_NO; ?></th>
                <th><?php echo TABLE_INVOICE_CODE; ?></th>
                <th><?php echo PATIENT_NAME; ?></th>
                <th><?php echo TABLE_SEX; ?></th>
                <th><?php echo GENERAL_AMOUNT; ?> ($)</th>
                <th><?php echo GENERAL_DISCOUNT; ?> ($)</th>
                <th><?php echo ACTION_ACTION; ?></th>
            </tr>
            </head>
            <tbody>
            <tr>
                <td colspan="7" class="dataTables_empty first"><?php echo MESSAGE_CLICK_BUTTON_GET_LATEST_DATA; ?></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="clear"></div>
    <br>
    <div style="margin-right: 20px;">
        <h1 class="title-no-image">
            <img style="margin-left: -40px;" src="<?php echo $this->webroot;?>img/icon/incomplete.png" />
            <?php __(DASHBOARD_DEBT_LIST);?>
            <span id="showActionRefresh10" style="float: right;vertical-align: middle;margin-top: 5px;cursor: pointer;">                    
                <img onmouseover="Tip('Refresh')" class="reloadQueueDebtList" alt="Refresh" src="<?php echo $this->webroot;?>img/button/refresh-active.png" />                    
            </span>
        </h1>
        <table id="debtList" class="table" cellspacing="0">
            <thead>
                <tr>
                    <th class="first"><?php echo TABLE_NO; ?></th>
                    <th><?php echo TABLE_INVOICE_CODE; ?></th>
                    <th><?php echo PATIENT_CODE; ?></th>
                    <th><?php echo PATIENT_NAME; ?></th>
                    <th><?php echo TABLE_SEX; ?></th>
                    <th><?php echo TABLE_DOB; ?></th>
                    <th><?php echo TABLE_TELEPHONE; ?></th>    
                    <th><?php echo OTHER_REQUESTED_DATE; ?></th>          
                    <th><?php echo ACTION_ACTION; ?></th>
                </tr>
            </head>
            <tbody>
            <tr>
                <td colspan="9" class="dataTables_empty first"><?php echo MESSAGE_CLICK_BUTTON_GET_LATEST_DATA; ?></td>
            </tr>
            </tbody>
        </table>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
    <br>
    <div style="margin-right: 20px;">
        <h1 class="title-no-image">
            <img style="margin-left: -40px;" src="<?php echo $this->webroot;?>img/button/hospital.png" />
            <?php __('IPD Payment');?>
            <span id="showActionRefresh11" style="float: right;vertical-align: middle;margin-top: 5px;cursor: pointer;">                    
                <img onmouseover="Tip('Refresh')" class="reloadQueueIPDPayment" alt="Refresh" src="<?php echo $this->webroot;?>img/button/refresh-active.png" />                    
            </span>
        </h1>
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
                    <th><?php echo OTHER_REQUESTED_DATE; ?></th>                
                    <th><?php echo ACTION_ACTION; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="9" class="dataTables_empty first"><?php echo MESSAGE_CLICK_BUTTON_GET_LATEST_DATA; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="clear"></div>
    <br>
<?php } ?>

<?php if(!empty($allowAppointment)) { ?>
    <div style="margin-right: 20px;">
        <h1 class="title-no-image">
            <img style="margin-left: -40px;" src="<?php echo $this->webroot;?>img/icon/time.png" />
            <?php __(MENU_APPOINTMENT_MANAGEMENT); ?>
            <div style="float: right; vertical-align: middle; margin: 5px 0px 5px 5px;">
                <?php
                echo TABLE_DATE;
                ?>
                <input type="text" id="queueAppointmentDate" style="font-size: 11px; height: 15px;" value="<?php echo date('d/m/Y'); ?>" />
                <span id="showActionRefresh3" style="float: right;cursor: pointer;padding-left: 10px;">                    
                    <img onmouseover="Tip('Refresh')" class="reloadAppointment" alt="Refresh" src="<?php echo $this->webroot;?>img/button/refresh-active.png" />         
                </span>
            </div>
        </h1>
        <table id="appointment" class="table" cellspacing="0">
            <thead>
                <tr>
                    <th class="first"><?php echo TABLE_NO; ?></th>
                    <th><?php echo PATIENT_CODE; ?></th>
                    <th><?php echo TABLE_NAME; ?></th>                
                    <th><?php echo TABLE_SEX; ?></th>
                    <th><?php echo TABLE_TELEPHONE; ?></th>
                    <th><?php echo APPOINTMENT_DATE; ?></th>
                    <th><?php echo TABLE_REMAINING; ?></th>
                    <th><?php echo DOCTOR_DOCTOR; ?></th>
                    <th><?php echo GENERAL_DESCRIPTION; ?></th>
                    <th><?php echo ACTION_ACTION; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="10" class="dataTables_empty first"><?php echo MESSAGE_CLICK_BUTTON_GET_LATEST_DATA; ?></td>
                </tr>
            </tbody>
        </table>
        <div class="clear"></div>
    </div>
    <br>
<?php } ?>

<?php if(!empty($allowQueueLabo)) { ?>
    <div style="margin-right: 20px;">
        <h1 class="title-no-image">
            <img style="margin-left: -40px;" src="<?php echo $this->webroot;?>img/icon/blood_test.png" />
            <?php __(GENERAL_QUEUE.' Labo Test'); ?>
            <div style="float: right; vertical-align: middle; margin: 5px 0px 5px 5px;">
                <?php
                echo TABLE_DATE;
                ?>
                <input type="text" id="queueLaboDate" style="font-size: 11px; height: 15px;" value="<?php echo date('d/m/Y'); ?>" />
                <span style="float: right;cursor: pointer;padding-left: 10px">                    
                    <img onmouseover="Tip('Refresh')" class="reloadQueueLabo" alt="Refresh" src="<?php echo $this->webroot;?>img/button/refresh-active.png" />                    
                </span>
            </div>
        </h1>
        <table id="queueListLabo" class="table" cellspacing="0">
            <thead>
                <tr>
                    <th class="first"><?php echo TABLE_NO; ?></th>
                    <th><?php echo PATIENT_CODE; ?></th>
                    <th><?php echo PATIENT_NAME; ?></th>
                    <th><?php echo TABLE_SEX; ?></th>                
                    <th><?php echo TABLE_TELEPHONE; ?></th>                
                    <th><?php echo OTHER_REQUESTED_DATE; ?></th>
                    <th><?php echo ACTION_ACTION; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="7" class="dataTables_empty first"><?php echo MESSAGE_CLICK_BUTTON_GET_LATEST_DATA; ?></td>
                </tr>
            </tbody>
        </table>
        <div class="clear"></div>
    </div>
    <br>    
<?php } ?>

<?php if(!empty($allowQueueEchoDoctor)) { ?>
    <div style="margin-right: 20px;">
        <h1 class="title-no-image">
            <img style="margin-left: -40px;" src="<?php echo $this->webroot;?>img/icon/doctor_21.png" />
            <?php __(GENERAL_QUEUE . ' For Echo From Doctor'); ?>
            <span style="float: right;vertical-align: middle;margin-top: 5px;cursor: pointer;">                    
                <img onmouseover="Tip('Refresh')" class="reloadQueueEcho" alt="Refresh" src="<?php echo $this->webroot;?>img/button/refresh-active.png" />                    
            </span>
        </h1>
        <table id="queueListEchoDoctor" class="table" cellspacing="0">
                <thead>
                    <tr>
                        <th class="first"><?php echo TABLE_NO; ?></th>
                        <th><?php echo PATIENT_CODE; ?></th>
                        <th><?php echo PATIENT_NAME; ?></th>                
                        <th><?php echo TABLE_SEX; ?></th>
                        <th><?php echo TABLE_TELEPHONE; ?></th>          
                        <th><?php echo OTHER_REQUESTED_DATE; ?></th>
                        <th><?php echo ACTION_ACTION; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="dataTables_empty first"><?php echo MESSAGE_CLICK_BUTTON_GET_LATEST_DATA; ?></td>
                    </tr>
                </tbody>
        </table>
        <div class="clear"></div>
    </div>
    <br>
<?php } ?>

<?php if(!empty($allowQueueXrayDoctor)) { ?>
    <div style="margin-right: 20px;">
        <h1 class="title-no-image">
            <img style="margin-left: -40px;" src="<?php echo $this->webroot;?>img/icon/xray_of_bones.png" />
            <?php __(GENERAL_QUEUE . ' For Xray From Doctor'); ?>
            <span style="float: right;vertical-align: middle;margin-top: 5px;cursor: pointer;">                    
                <img onmouseover="Tip('Refresh')" class="reloadQueueXray" alt="Refresh" src="<?php echo $this->webroot;?>img/button/refresh-active.png" />                    
            </span>
        </h1>
        <table id="queueListXrayDoctor" class="table" cellspacing="0">
                <thead>
                    <tr>
                        <th class="first"><?php echo TABLE_NO; ?></th>
                        <th><?php echo PATIENT_CODE; ?></th>
                        <th><?php echo PATIENT_NAME; ?></th>                
                        <th><?php echo TABLE_SEX; ?></th>
                        <th><?php echo TABLE_TELEPHONE; ?></th>          
                        <th><?php echo OTHER_REQUESTED_DATE; ?></th>
                        <th><?php echo ACTION_ACTION; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="dataTables_empty first"><?php echo MESSAGE_CLICK_BUTTON_GET_LATEST_DATA; ?></td>
                    </tr>
                </tbody>
        </table>
        <div class="clear"></div>
    </div>
    <br>
<?php } ?>

<?php if(!empty($allowQueueCystoscopyDoctor)) { ?>
    <div style="margin-right: 20px;">
        <h1 class="title-no-image">
            <img style="margin-left: -40px;" src="<?php echo $this->webroot;?>img/icon/cystoscopy.png" />
            <?php __(GENERAL_QUEUE . ' For Cystoscopy From Doctor'); ?>
            <span style="float: right;vertical-align: middle;margin-top: 5px;cursor: pointer;">                    
                <img onmouseover="Tip('Refresh')" class="reloadQueueCysto" alt="Refresh" src="<?php echo $this->webroot;?>img/button/refresh-active.png" />                    
            </span>
        </h1>
        <table id="queueListCystoscopyDoctor" class="table" cellspacing="0">
                <thead>
                    <tr>
                        <th class="first"><?php echo TABLE_NO; ?></th>
                        <th><?php echo PATIENT_CODE; ?></th>
                        <th><?php echo PATIENT_NAME; ?></th>                
                        <th><?php echo TABLE_SEX; ?></th>
                        <th><?php echo TABLE_TELEPHONE; ?></th>          
                        <th><?php echo OTHER_REQUESTED_DATE; ?></th>
                        <th><?php echo ACTION_ACTION; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="dataTables_empty first"><?php echo MESSAGE_CLICK_BUTTON_GET_LATEST_DATA; ?></td>
                    </tr>
                </tbody>
        </table>
        <div class="clear"></div>
    </div>
    <br>
<?php } ?>

<?php if(!empty($allowQueueMidWifeDoctor)) { ?>
    <div style="margin-right: 20px;">
        <h1 class="title-no-image">
            <img style="margin-left: -40px;" src="<?php echo $this->webroot;?>img/icon/pregnancy.png" />
            <?php __(GENERAL_QUEUE . ' For Mid Wife From Doctor'); ?>
            <span style="float: right;vertical-align: middle;margin-top: 5px;cursor: pointer;">                    
                <img onmouseover="Tip('Refresh')" class="reloadQueueMidWife" alt="Refresh" src="<?php echo $this->webroot;?>img/button/refresh-active.png" />                    
            </span>
        </h1>
        <table id="queueListMidWifeDoctor" class="table" cellspacing="0">
            <thead>
                <tr>
                    <th class="first"><?php echo TABLE_NO; ?></th>
                    <th><?php echo PATIENT_CODE; ?></th>
                    <th><?php echo PATIENT_NAME; ?></th>                
                    <th><?php echo TABLE_SEX; ?></th>
                    <th><?php echo TABLE_TELEPHONE; ?></th>          
                    <th><?php echo OTHER_REQUESTED_DATE; ?></th>
                    <th><?php echo ACTION_ACTION; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="7" class="dataTables_empty first"><?php echo MESSAGE_CLICK_BUTTON_GET_LATEST_DATA; ?></td>
                </tr>
            </tbody>
        </table>
        <div class="clear"></div>
    </div>
    <br>
<?php } ?>

<?php if($viewProfitLoss){
?>
<div class="boxDashboard" role="dashboards/viewProfitLoss" display="<?php echo $displayProfitLoss; ?>" confirm="<?php echo MESSAGE_CONFIRM_HIDE; ?>" dialog="<?php echo DIALOG_CONFIRMATION; ?>" cancel="<?php echo ACTION_CANCEL; ?>" hide="<?php echo TABLE_HIDE; ?>" style="width: 49%; float: left; font-size: 14px; font-weight: bold; margin-bottom: 10px; margin-right: 5px; <?php echo $viewProfitLossDisp; ?>">
    <h1 class="title"><span class="dashboardName"><?php echo MENU_PROFIT_AND_LOSS; ?></span>
        <img onmouseover="Tip('Setting')" src="<?php echo $this->webroot; ?>img/button/setting-inactive.png" id="settingProfitLoss" style="width: 20px; float: right; cursor: pointer;" />
        <img onmouseover="Tip('Loading...')" src="<?php echo $this->webroot; ?>img/button/refresh-animation.gif" id="loadingProfitLoss" style="width: 20px; float: right; display: none; margin-right: 10px;" /> 
        <img onmouseover="Tip('Refresh')" src="<?php echo $this->webroot; ?>img/button/refresh-inactive.png" id="refreshProfitLoss" class="refreshDashboard" style="width: 20px; float: right; cursor: pointer; margin-right: 10px;" /> 
        <img onmouseover="Tip('Hide')" src="<?php echo $this->webroot; ?>img/button/minimize-inactive.png" id="minimizeProfitLoss" class="minimizeDashboard" style="width: 20px; float: right; cursor: pointer; margin-right: 10px;" />
        <div style="clear: both;"></div>
        <div id="settingProfitLossMenu" class="settingMenu">
            <input type="hidden" class="defaultCheckSetting" value="<?php echo $viewProfitLossAuto; ?>" />
            <input type="hidden" class="defaultTimeSetting" value="<?php echo $viewProfitLossTime; ?>" />
            <input type="hidden" class="selectSetting" value="0" />
            <div class="divMenu">
                <h1>Setting <span style="float: right; margin-right: 3px; color: #337ab7; display: none;" class="saveCompleted">Save Completed</span><span style="float: right; margin-right: 3px; color: red; display: none;" class="saveFailed">Save Failed</span></h1>
                <table cellpadding="5" cellspacing="0">
                    <tr>
                        <td style="width: 40%;">Auto Refresh</td>
                        <td style="text-align: left;">
                            <input type="checkbox" class="settingCheck" data-size="small" data-toggle="toggle" />
                        </td>
                    </tr>
                    <tr>
                        <td>Every</td>
                        <td>
                            <input type="text" style="width: 45px;" value="0" class="settingProfitLossTimeRefresh" /> Second(s)
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <button type="button" style="float: right; margin-right: 10px;" class="settingProfitLossSave">
                                <span class="settingProfitLossTxtSave"><?php echo ACTION_SAVE; ?></span>
                            </button>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </h1>
    <div style="width: 100%; font-size: 12px; height: 300px;" id="profitLossView">
        Loading....
    </div>
</div>
<?php 
}
if($viewTotalSales){
?>
<div class="boxDashboard" role="dashboards/viewTotalSales" display="<?php echo $displayTotalSales; ?>" confirm="<?php echo MESSAGE_CONFIRM_HIDE; ?>" dialog="<?php echo DIALOG_CONFIRMATION; ?>" cancel="<?php echo ACTION_CANCEL; ?>" hide="<?php echo TABLE_HIDE; ?>" style="width: 49%; float: left; font-size: 14px; font-weight: bold; margin-bottom: 10px; margin-right: 5px; <?php echo $viewTotalSalesDisp; ?>">
    <h1 class="title"><span class="dashboardName"><?php echo TABLE_TOTAL_SALES; ?></span>
        <img onmouseover="Tip('Setting')" src="<?php echo $this->webroot; ?>img/button/setting-inactive.png" id="settingTotalSales" style="width: 20px; float: right; cursor: pointer;" />
        <img onmouseover="Tip('Loading...')" src="<?php echo $this->webroot; ?>img/button/refresh-animation.gif" id="loadingTotalSales" style="width: 20px; float: right; display: none; margin-right: 10px;" /> 
        <img onmouseover="Tip('Refresh')" src="<?php echo $this->webroot; ?>img/button/refresh-inactive.png" id="refreshTotalSales" class="refreshDashboard" style="width: 20px; float: right; cursor: pointer; margin-right: 10px;" /> 
        <img onmouseover="Tip('Hide')" src="<?php echo $this->webroot; ?>img/button/minimize-inactive.png" id="minimizeTotalSales" class="minimizeDashboard" style="width: 20px; float: right; cursor: pointer; margin-right: 10px;" />
        <div style="width: 400px; float: right;">
            <select id="filterTotalSales" class="filterView" style="width: 130px; border: none;">
                <option value="ThisWeek">This Week</option>
                <option value="ThisWeekToDate">This Week-to-date</option>
                <option value="ThisMonth">This Month</option>
                <option value="LastWeek">Last Week</option>
                <option value="LastWeekToDate">Last Week-to-date</option>
                <option value="LastMonth">Last Month</option>
            </select>
            <select id="groupTotalSales" class="groupView" style="width: 130px; border: none;">
                <option value="1">Group By Day</option>
                <option value="2" selected="selected">Group By Month</option>
                <option value="3">Group By Quarter</option>
                <option value="4">Group By Year</option>
            </select>
            <select id="chartTotalSales" class="chartView" style="width: 100px; border: none;">
                <option value="line">Line Chart</option>
                <option value="column" selected="selected">Bar Chart</option>
                <option value="area">Area Chart</option>
            </select>
        </div>
        <div style="clear: both;"></div>
        <div id="settingTotalSalesMenu" class="settingMenu">
            <input type="hidden" class="defaultCheckSetting" value="<?php echo $viewTotalSalesAuto; ?>" />
            <input type="hidden" class="defaultTimeSetting" value="<?php echo $viewTotalSalesTime; ?>" />
            <input type="hidden" class="selectSetting" value="0" />
            <div class="divMenu">
                <h1>Setting <span style="float: right; margin-right: 3px; color: #337ab7; display: none;" class="saveCompleted">Save Completed</span><span style="float: right; margin-right: 3px; color: red; display: none;" class="saveFailed">Save Failed</span></h1>
                <table cellpadding="5" cellspacing="0">
                    <tr>
                        <td style="width: 40%;">Auto Refresh</td>
                        <td style="text-align: left;">
                            <input type="checkbox" class="settingCheck" data-size="small" data-toggle="toggle" />
                        </td>
                    </tr>
                    <tr>
                        <td>Every</td>
                        <td>
                            <input type="text" style="width: 45px;" value="0" class="settingTotalSalesTimeRefresh" /> Second(s)
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <button type="button" style="float: right; margin-right: 10px;" class="settingTotalSalesSave">
                                <span class="settingTotalSalesTxtSave"><?php echo ACTION_SAVE; ?></span>
                            </button>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </h1>
    <div style="width: 100%; font-size: 12px; height: 300px;" id="TotalSalesView">
        Loading....
    </div>
</div>
<?php   
}
if($viewExpense){
?>
<div class="boxDashboard" role="dashboards/viewExpenseGraph" display="<?php echo $displayExpenseGraph; ?>" confirm="<?php echo MESSAGE_CONFIRM_HIDE; ?>" dialog="<?php echo DIALOG_CONFIRMATION; ?>" cancel="<?php echo ACTION_CANCEL; ?>" hide="<?php echo TABLE_HIDE; ?>" style="width: 49%; float: left; font-size: 14px; font-weight: bold; margin-bottom: 10px; margin-right: 5px; <?php echo $viewExpenseGraphDisp; ?>">
    <h1 class="title"><span class="dashboardName"><?php echo MENU_EXPENSE; ?></span>
        <img onmouseover="Tip('Setting')" src="<?php echo $this->webroot; ?>img/button/setting-inactive.png" id="settingExpenseGraph" style="width: 20px; float: right; cursor: pointer;" />
        <img onmouseover="Tip('Loading...')" src="<?php echo $this->webroot; ?>img/button/refresh-animation.gif" id="loadingExpenseGraph" style="width: 20px; float: right; display: none; margin-right: 10px;" /> 
        <img onmouseover="Tip('Refresh')" src="<?php echo $this->webroot; ?>img/button/refresh-inactive.png" id="refreshExpenseGraph" class="refreshDashboard" style="width: 20px; float: right; cursor: pointer; margin-right: 10px;" /> 
        <img onmouseover="Tip('Hide')" src="<?php echo $this->webroot; ?>img/button/minimize-inactive.png" id="minimizeExpenseGraph" class="minimizeDashboard" style="width: 20px; float: right; cursor: pointer; margin-right: 10px;" />
        <div style="width: 110px; float: right;">
            <select id="filterExpenseGraph" class="filterView" style="width: 100px; border: none;">
                <option value="ThisMonth">This Month</option>
                <option value="ThisQuarter">This Quarter</option>
                <option value="ThisYear">This Year</option>
                <option value="LastMonth">Last Month</option>
                <option value="LastQuarter">Last Quarter</option>
                <option value="LastYear">Last Year</option>
            </select>
        </div>
        <div style="clear: both;"></div>
        <div id="settingExpenseGraphMenu" class="settingMenu">
            <input type="hidden" class="defaultCheckSetting" value="<?php echo $viewExpenseGraphAuto; ?>" />
            <input type="hidden" class="defaultTimeSetting" value="<?php echo $viewExpenseGraphTime; ?>" />
            <input type="hidden" class="selectSetting" value="0" />
            <div class="divMenu">
                <h1>Setting <span style="float: right; margin-right: 3px; color: #337ab7; display: none;" class="saveCompleted">Save Completed</span><span style="float: right; margin-right: 3px; color: red; display: none;" class="saveFailed">Save Failed</span></h1>
                <table cellpadding="5" cellspacing="0">
                    <tr>
                        <td style="width: 40%;">Auto Refresh</td>
                        <td style="text-align: left;">
                            <input type="checkbox" class="settingCheck" data-size="small" data-toggle="toggle" />
                        </td>
                    </tr>
                    <tr>
                        <td>Every</td>
                        <td>
                            <input type="text" style="width: 45px;" value="0" class="settingExpenseGraphTimeRefresh" /> Second(s)
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <button type="button" style="float: right; margin-right: 10px;" class="settingExpenseGraphSave">
                                <span class="settingExpenseGraphTxtSave"><?php echo ACTION_SAVE; ?></span>
                            </button>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </h1>
    <div style="width: 100%; font-size: 12px;" id="expenseGraphView">
        Loading....
    </div>
</div>
<?php 
}
if($viewSalesTop10){
?>
<div class="boxDashboard" role="dashboards/viewSalesTop10" display="<?php echo $displaySalesTop10; ?>" confirm="<?php echo MESSAGE_CONFIRM_HIDE; ?>" dialog="<?php echo DIALOG_CONFIRMATION; ?>" cancel="<?php echo ACTION_CANCEL; ?>" hide="<?php echo TABLE_HIDE; ?>" style="width: 49%; float: left; font-size: 14px; font-weight: bold; margin-bottom: 10px; margin-right: 5px; <?php echo $viewSalesTop10Disp; ?>">
    <h1 class="title"><span class="dashboardName">Sales Top 10 Items</span>
        <img onmouseover="Tip('Setting')" src="<?php echo $this->webroot; ?>img/button/setting-inactive.png" id="settingSalesTop10" style="width: 20px; float: right; cursor: pointer;" />
        <img onmouseover="Tip('Loading...')" src="<?php echo $this->webroot; ?>img/button/refresh-animation.gif" id="loadingSalesTop10" style="width: 20px; float: right; display: none; margin-right: 10px;" /> 
        <img onmouseover="Tip('Refresh')" src="<?php echo $this->webroot; ?>img/button/refresh-inactive.png" id="refreshSalesTop10" class="refreshDashboard" style="width: 20px; float: right; cursor: pointer; margin-right: 10px;" /> 
        <img onmouseover="Tip('Hide')" src="<?php echo $this->webroot; ?>img/button/minimize-inactive.png" id="minimizeSalesTop10" class="minimizeDashboard" style="width: 20px; float: right; cursor: pointer; margin-right: 10px;" />
        <div style="width: 110px; float: right;">
            <select id="filterSalesTop10" class="filterView" style="width: 100px; border: none;">
                <option value="ThisMonth">This Month</option>
                <option value="ThisQuarter">This Quarter</option>
                <option value="ThisYear">This Year</option>
                <option value="LastMonth">Last Month</option>
                <option value="LastQuarter">Last Quarter</option>
                <option value="LastYear">Last Year</option>
            </select>
        </div>
        <div style="clear: both;"></div>
        <div id="settingSalesTop10Menu" class="settingMenu">
            <input type="hidden" class="defaultCheckSetting" value="<?php echo $viewSalesTop10Auto; ?>" />
            <input type="hidden" class="defaultTimeSetting" value="<?php echo $viewSalesTop10Time; ?>" />
            <input type="hidden" class="selectSetting" value="0" />
            <div class="divMenu">
                <h1>Setting <span style="float: right; margin-right: 3px; color: #337ab7; display: none;" class="saveCompleted">Save Completed</span><span style="float: right; margin-right: 3px; color: red; display: none;" class="saveFailed">Save Failed</span></h1>
                <table cellpadding="5" cellspacing="0">
                    <tr>
                        <td style="width: 40%;">Auto Refresh</td>
                        <td style="text-align: left;">
                            <input type="checkbox" class="settingCheck" data-size="small" data-toggle="toggle" />
                        </td>
                    </tr>
                    <tr>
                        <td>Every</td>
                        <td>
                            <input type="text" style="width: 45px;" value="0" class="settingSalesTop10TimeRefresh" /> Second(s)
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <button type="button" style="float: right; margin-right: 10px;" class="settingSalesTop10Save">
                                <span class="settingSalesTop10TxtSave"><?php echo ACTION_SAVE; ?></span>
                            </button>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </h1>
    <div style="width: 100%; font-size: 12px;" id="salesTop10View">
        Loading....
    </div>
</div>
<?php 
}
if($viewReceivable){
?>
<div class="boxDashboard" role="dashboards/viewReceivable" display="<?php echo $displayReceivable; ?>" confirm="<?php echo MESSAGE_CONFIRM_HIDE; ?>" dialog="<?php echo DIALOG_CONFIRMATION; ?>" cancel="<?php echo ACTION_CANCEL; ?>" hide="<?php echo TABLE_HIDE; ?>" style="width: 49%; float: left; font-size: 14px; font-weight: bold; margin-bottom: 10px; margin-right: 5px; <?php echo $viewReceivableDisp; ?>">
    <h1 class="title"><span class="dashboardName"><?php echo TABLE_TOTAL_RECEIVABLES; ?></span>
        <img onmouseover="Tip('Setting')" src="<?php echo $this->webroot; ?>img/button/setting-inactive.png" id="settingReceivable" style="width: 20px; float: right; cursor: pointer;" />
        <img onmouseover="Tip('Loading...')" src="<?php echo $this->webroot; ?>img/button/refresh-animation.gif" id="loadingReceivable" style="width: 20px; float: right; display: none; margin-right: 10px;" /> 
        <img onmouseover="Tip('Refresh')" src="<?php echo $this->webroot; ?>img/button/refresh-inactive.png" id="refreshReceivable" class="refreshDashboard" style="width: 20px; float: right; cursor: pointer; margin-right: 10px;" /> 
        <img onmouseover="Tip('Hide')" src="<?php echo $this->webroot; ?>img/button/minimize-inactive.png" id="minimizeReceivable" class="minimizeDashboard" style="width: 20px; float: right; cursor: pointer; margin-right: 10px;" />
        <div style="clear: both;"></div>
        <div id="settingReceivableMenu" class="settingMenu">
            <input type="hidden" class="defaultCheckSetting" value="<?php echo $viewReceivableAuto; ?>" />
            <input type="hidden" class="defaultTimeSetting" value="<?php echo $viewReceivableTime; ?>" />
            <input type="hidden" class="selectSetting" value="0" />
            <div class="divMenu">
                <h1>Setting <span style="float: right; margin-right: 3px; color: #337ab7; display: none;" class="saveCompleted">Save Completed</span><span style="float: right; margin-right: 3px; color: red; display: none;" class="saveFailed">Save Failed</span></h1>
                <table cellpadding="5" cellspacing="0">
                    <tr>
                        <td style="width: 40%;">Auto Refresh</td>
                        <td style="text-align: left;">
                            <input type="checkbox" class="settingCheck" data-size="small" data-toggle="toggle" />
                        </td>
                    </tr>
                    <tr>
                        <td>Every</td>
                        <td>
                            <input type="text" style="width: 45px;" value="0" class="settingReceivableTimeRefresh" /> Second(s)
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <button type="button" style="float: right; margin-right: 10px;" class="settingReceivableSave">
                                <span class="settingReceivableTxtSave"><?php echo ACTION_SAVE; ?></span>
                            </button>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </h1>
    <div style="width: 100%; font-size: 12px;" id="receivableView">
        Loading....
    </div>
</div>
<?php 
}
if($viewPayable){
?>
<div class="boxDashboard" role="dashboards/viewPayable" display="<?php echo $displayPayable; ?>" confirm="<?php echo MESSAGE_CONFIRM_HIDE; ?>" dialog="<?php echo DIALOG_CONFIRMATION; ?>" cancel="<?php echo ACTION_CANCEL; ?>" hide="<?php echo TABLE_HIDE; ?>" style="width: 49%; float: left; font-size: 14px; font-weight: bold; margin-bottom: 10px; margin-right: 5px; <?php echo $viewPayableDisp; ?>">
    <h1 class="title"><span class="dashboardName"><?php echo TABLE_TOTAL_PAYABLES; ?></span>
        <img onmouseover="Tip('Setting')" src="<?php echo $this->webroot; ?>img/button/setting-inactive.png" id="settingPayable" style="width: 20px; float: right; cursor: pointer;" />
        <img onmouseover="Tip('Loading...')" src="<?php echo $this->webroot; ?>img/button/refresh-animation.gif" id="loadingPayable" style="width: 20px; float: right; display: none; margin-right: 10px;" /> 
        <img onmouseover="Tip('Refresh')" src="<?php echo $this->webroot; ?>img/button/refresh-inactive.png" id="refreshPayable" class="refreshDashboard" style="width: 20px; float: right; cursor: pointer; margin-right: 10px;" /> 
        <img onmouseover="Tip('Hide')" src="<?php echo $this->webroot; ?>img/button/minimize-inactive.png" id="minimizePayable" class="minimizeDashboard" style="width: 20px; float: right; cursor: pointer; margin-right: 10px;" />
        <div style="clear: both;"></div>
        <div id="settingPayableMenu" class="settingMenu">
            <input type="hidden" class="defaultCheckSetting" value="<?php echo $viewPayableAuto; ?>" />
            <input type="hidden" class="defaultTimeSetting" value="<?php echo $viewPayableTime; ?>" />
            <input type="hidden" class="selectSetting" value="0" />
            <div class="divMenu">
                <h1>Setting <span style="float: right; margin-right: 3px; color: #337ab7; display: none;" class="saveCompleted">Save Completed</span><span style="float: right; margin-right: 3px; color: red; display: none;" class="saveFailed">Save Failed</span></h1>
                <table cellpadding="5" cellspacing="0">
                    <tr>
                        <td style="width: 40%;">Auto Refresh</td>
                        <td style="text-align: left;">
                            <input type="checkbox" class="settingCheck" data-size="small" data-toggle="toggle" />
                        </td>
                    </tr>
                    <tr>
                        <td>Every</td>
                        <td>
                            <input type="text" style="width: 45px;" value="0" class="settingPayableTimeRefresh" /> Second(s)
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <button type="button" style="float: right; margin-right: 10px;" class="settingPayableSave">
                                <span class="settingPayableTxtSave"><?php echo ACTION_SAVE; ?></span>
                            </button>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </h1>
    <div style="width: 100%; font-size: 12px;" id="payableView">
        Loading....
    </div>
</div>
<?php 
}
if($viewAdjIssued){
?>
<div class="boxDashboard" role="inv_adjs/viewAdjustmentIssued" display="<?php echo $displayAdjIssued; ?>" confirm="<?php echo MESSAGE_CONFIRM_HIDE; ?>" dialog="<?php echo DIALOG_CONFIRMATION; ?>" cancel="<?php echo ACTION_CANCEL; ?>" hide="<?php echo TABLE_HIDE; ?>" style="width: 49%; float: left; font-size: 14px; font-weight: bold; margin-bottom: 10px; margin-right: 5px; <?php echo $viewAdjIssuedDisp; ?>">
    <h1 class="title"><span class="dashboardName"><?php echo MENU_INVENTORY_ADJUSTMENT; ?></span>
        <img onmouseover="Tip('Setting')" src="<?php echo $this->webroot; ?>img/button/setting-inactive.png" id="settingAdjIssued" style="width: 20px; float: right; cursor: pointer;" />
        <img onmouseover="Tip('Loading...')" src="<?php echo $this->webroot; ?>img/button/refresh-animation.gif" id="loadingAdjIssued" style="width: 20px; float: right; display: none; margin-right: 10px;" /> 
        <img onmouseover="Tip('Refresh')" src="<?php echo $this->webroot; ?>img/button/refresh-inactive.png" id="refreshAdjIssued" class="refreshDashboard" style="width: 20px; float: right; cursor: pointer; margin-right: 10px;" /> 
        <img onmouseover="Tip('Hide')" src="<?php echo $this->webroot; ?>img/button/minimize-inactive.png" id="minimizeAdjIssued" class="minimizeDashboard" style="width: 20px; float: right; cursor: pointer; margin-right: 10px;" />
        <div style="clear: both;"></div>
        <div id="settingAdjIssuedMenu" class="settingMenu">
            <input type="hidden" class="defaultCheckSetting" value="<?php echo $viewAdjIssuedAuto; ?>" />
            <input type="hidden" class="defaultTimeSetting" value="<?php echo $viewAdjIssuedTime; ?>" />
            <input type="hidden" class="selectSetting" value="0" />
            <div class="divMenu">
                <h1>Setting <span style="float: right; margin-right: 3px; color: #337ab7; display: none;" class="saveCompleted">Save Completed</span><span style="float: right; margin-right: 3px; color: red; display: none;" class="saveFailed">Save Failed</span></h1>
                <table cellpadding="5" cellspacing="0">
                    <tr>
                        <td style="width: 40%;">Auto Refresh</td>
                        <td style="text-align: left;">
                            <input type="checkbox" class="settingCheck" data-size="small" data-toggle="toggle" />
                        </td>
                    </tr>
                    <tr>
                        <td>Every</td>
                        <td>
                            <input type="text" style="width: 45px;" value="0" class="settingAdjIssuedTimeRefresh" /> Second(s)
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <button type="button" style="float: right; margin-right: 10px;" class="settingAdjIssuedSave">
                                <span class="settingAdjIssuedTxtSave"><?php echo ACTION_SAVE; ?></span>
                            </button>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </h1>
    <div style="width: 100%; font-size: 12px;" id="adjIssuedView">
        Loading....
    </div>
</div>
<?php 
}
?>

<?php 
if($viewProductReorderLevel){ 
?>
 <div class="boxDashboard" role="products/viewProductReorderLevel" display="<?php echo $displayProductReorderLevel; ?>" confirm="<?php echo MESSAGE_CONFIRM_HIDE; ?>" dialog="<?php echo DIALOG_CONFIRMATION; ?>" cancel="<?php echo ACTION_CANCEL; ?>" hide="<?php echo TABLE_HIDE; ?>" style="width: 49%; float: left; font-size: 14px; font-weight: bold; margin-bottom: 10px; margin-right: 5px; <?php echo $viewProductReorderLevelDisp; ?>">
     <h1 class="title-no-image">
         <span class="dashboardName">
            <img style="margin-left: -40px;" src="<?php echo $this->webroot;?>img/icon/reorder.png" />
            <?php echo MENU_PRODUCT_REORDER; ?>
         </span>
         <img onmouseover="Tip('Setting')" src="<?php echo $this->webroot; ?>img/button/setting-inactive.png" id="settingProductReorderLevel" style="width: 20px; float: right; cursor: pointer;" />
         <img onmouseover="Tip('Loading...')" src="<?php echo $this->webroot; ?>img/button/refresh-animation.gif" id="loadingProductReorderLevel" style="width: 20px; float: right; display: none; margin-right: 10px;" /> 
         <img onmouseover="Tip('Refresh')" src="<?php echo $this->webroot; ?>img/button/refresh-inactive.png" id="refreshProductReorderLevel" class="refreshDashboard" style="width: 20px; float: right; cursor: pointer; margin-right: 10px;" /> 
         <img onmouseover="Tip('Hide')" src="<?php echo $this->webroot; ?>img/button/minimize-inactive.png" id="minimizeReceivable" class="minimizeDashboard" style="width: 20px; float: right; cursor: pointer; margin-right: 10px;" />
         <div style="clear: both;"></div>
     </h1>
     <input type="hidden" class="defaultCheckSetting" value="<?php echo $viewProductReorderLevelAuto; ?>" />
     <input type="hidden" class="defaultTimeSetting" value="<?php echo $viewProductReorderLevelTime; ?>" />
     <input type="hidden" class="selectSetting" value="0" />
     <div style="width: 100%; font-size: 12px;" id="viewProductReorderLevel">
         Loading....
     </div>
 </div>
<?php   
}
?>


<?php 
if($viewProductExpireDate){ 
?>
 <div class="boxDashboard" role="products/viewProductExpireDate" display="<?php echo $displayProductExpireDate; ?>" confirm="<?php echo MESSAGE_CONFIRM_HIDE; ?>" dialog="<?php echo DIALOG_CONFIRMATION; ?>" cancel="<?php echo ACTION_CANCEL; ?>" hide="<?php echo TABLE_HIDE; ?>" style="width: 49%; float: left; font-size: 14px; font-weight: bold; margin-bottom: 10px; margin-right: 5px; <?php echo $viewProductExpireDateDisp; ?>">
     <h1 class="title-no-image">
         <span class="dashboardName"> 
            <img style="margin-left: -40px;" src="<?php echo $this->webroot;?>img/icon/tablets.png" />
            <?php echo TABLE_PRODUCT_EXPIRED_DATE; ?>
         </span>
         <img onmouseover="Tip('Setting')" src="<?php echo $this->webroot; ?>img/button/setting-inactive.png" id="settingProductExpireDate" style="width: 20px; float: right; cursor: pointer;" />
         <img onmouseover="Tip('Loading...')" src="<?php echo $this->webroot; ?>img/button/refresh-animation.gif" id="loadingProductExpireDate" style="width: 20px; float: right; display: none; margin-right: 10px;" /> 
         <img onmouseover="Tip('Refresh')" src="<?php echo $this->webroot; ?>img/button/refresh-inactive.png" id="refreshProductExpireDate" class="refreshDashboard" style="width: 20px; float: right; cursor: pointer; margin-right: 10px;" /> 
         <img onmouseover="Tip('Hide')" src="<?php echo $this->webroot; ?>img/button/minimize-inactive.png" id="minimizeReceivable" class="minimizeDashboard" style="width: 20px; float: right; cursor: pointer; margin-right: 10px;" />
         <div style="clear: both;"></div>
     </h1>
     <input type="hidden" class="defaultCheckSetting" value="<?php echo $viewProductExpireDateAuto; ?>" />
     <input type="hidden" class="defaultTimeSetting" value="<?php echo $viewProductExpireDateTime; ?>" />
     <input type="hidden" class="selectSetting" value="0" />
     <div style="width: 100%; font-size: 12px;" id="viewProductExpireDate">
         Loading....
     </div>
 </div>
<?php   
}
?>


  
</div>
<div class="rightPanel"></div>



