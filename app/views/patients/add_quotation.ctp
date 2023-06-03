<?php 
echo $this->element('prevent_multiple_submit'); 
$absolute_url = FULL_BASE_URL . Router::url("/", false);
echo $javascript->link('uninums.min'); 
?>
<!-- Choosen -->
<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>js/harvesthq-chosen-v0.9.1/chosen/chosen.css" />
<style type="text/css">
    .input{
        float:left;
    }
    .chzn-container-multi .chzn-choices {
        width: 304px;
        height: 35px !important;
    }
    #PatientCompanyInsuranceId{
        display: none;
    }
</style>
<script type="text/javascript">
    $(document).ready(function(){ 
        // chosen init
        $(".chzn-select").chosen();
        $(".float").autoNumeric({mDec: 2});
        
        // Prevent Key Enter
        preventKeyEnter();        
        $("#PatientAddQuotationForm").validationEngine();
        $("#PatientAddQuotationForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSavePatient").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");                
                
                $("#dialog").html('<div class="buttons"><button type="submit" class="positive printPatientQuotationForm" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="txtPrintInvoice"><?php echo ACTION_PRINT_QUOTATION_PATIENT; ?></span></button></div>');
                $(".printPatientQuotationForm").click(function(){
                    $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printPatientQuotation/"+result,
                        beforeSend: function(){
                            $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                        },
                        success: function(printInvoiceResult){
                            w=window.open();
                            w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                            w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                            w.document.write(printInvoiceResult);
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
                   closeOnEscape: true,
                   open: function(event, ui){
                       $(".ui-dialog-buttonpane").show(); $(".ui-dialog-titlebar-close").show();
                   },
                   close: function(){
                       $(this).dialog({close: function(){}});
                       $(this).dialog("close");
                       $(".btnBackPatientEstimateExpense").dblclick();
                   },
                   buttons: {
                       '<?php echo ACTION_CLOSE; ?>': function() {
                           $("meta[http-equiv='refresh']").attr('content','0');
                           $(this).dialog("close");
                       }
                   }
               });
                $(".btnBackPatientEstimateExpense").dblclick();
            }
        });
        
        // search patient information
        $("#PatientPatientName").autocomplete("<?php echo $absolute_url . $this->params['controller']. "/searchPatient"; ?>", {        
            width: 410,
            max: 10,
            scroll: true,
            scrollHeight: 500,
            formatItem: function(data, i, n, value) {
                return value.split(".*")[2] + "-" + value.split(".*")[1];
            },
            formatResult: function(data, value) {
                return value.split(".*")[1];
            }
        }).result(function(event, value){            
            $(".trPatientCode").text(value.toString().split(".*")[2]);
            $("#patientCode").val(value.toString().split(".*")[2]);
            $("#patientId").val(value.toString().split(".*")[0]);
            if(value.toString().split(".*")[4]!=""){
                $("#PatientDob").val(value.toString().split(".*")[4]);
                var now = (new Date()).getFullYear();
                var age = now - $("#PatientDob").val().split("-",1);
                $('#PatientAge').val(age);
            }                     
            
            // condition select gender of patient
            if(value.toString().split(".*")[3]!=""){
                $("#PatientSex").find("option").each(function(){                    
                    if($(this).val()==value.toString().split(".*")[3]){                        
                        $(this).attr("selected", true);
                    }
                });
            }
            // condition select patient's group
            if(value.toString().split(".*")[5]!=""){
                $("#PatientPatientGroupId").find("option").each(function(){                    
                    if($(this).val()==value.toString().split(".*")[5]){
                        $(this).attr("selected", true);
                        if(value.toString().split(".*")[5]=="2"){
                            $("#PatientNationality").show();                            
                            $("#PatientNationality").find("option").each(function(){
                                if($(this).val()==value.toString().split(".*")[11]){
                                    $(this).attr("selected", true);
                                }
                            });
                        }
                    }
                });
            }
            // condition select patient's location
            if(value.toString().split(".*")[10]!=""){
                $("#PatientLocationId").find("option").each(function(){                    
                    if($(this).val()==value.toString().split(".*")[10]){                        
                        $(this).attr("selected", true);
                    }
                });
            }
            
            // condition select patient type of bill 
            if(value.toString().split(".*")[12]!=""){
                $("#PatientPatientBillTypeId").find("option").each(function(){                    
                    if($(this).val()==value.toString().split(".*")[12]){                        
                        $(this).attr("selected", true);
                    }                    
                });
                if(value.toString().split(".*")[12] == 3){
                    $("#PatientCompanyInsuranceId").show();
                    $("#PatientCompanyInsuranceId").find("option").each(function(){                    
                        if($(this).val()==value.toString().split(".*")[13]){                        
                            $(this).attr("selected", true);
                        }                    
                    });
                    if(value.toString().split(".*")[13]==0){
                        $("#insuranceNote").show(); 
                        $("#insuranceNote").val(value.toString().split(".*")[13]);
                    }
                }
                    
            }
            
            
            $("#PatientEmail").val(value.toString().split(".*")[6]);
            $("#PatientOccupation").val(value.toString().split(".*")[7]);
            $("#PatientTelephone").val(value.toString().split(".*")[8]);
            $("#PatientAddress").val(value.toString().split(".*")[9]);
            
            // show image delete patient
            $(".searchPatient").hide();
            $(".deleteSearchPatient").show();
            
            checkPatientCompany();
        });
        
        
        $(".searchPatient").click(function(){
            searchAllPatient();            
        });
        // close search patient information
        
        // delete search patient
        $(".deleteSearchPatient").click(function(){
            $(".trPatientCode").text($("#getPatientCode").val());
            $("#patientCode").val($("#getPatientCode").val());
            $("#patientId").val("");
            $("#PatientDob").val("");
            $('#PatientAge').val("");
            $("#PatientPatientName").val("");
            $("#PatientPatientGroupId").val("");
            $("#PatientNationality").hide();
            $("#PatientSex").val("");
            $("#PatientEmail").val("");
            $("#PatientOccupation").val("");
            $("#PatientTelephone").val("");
            $("#PatientAddress").val("");
            $("#PatientLocationId").val("");
            $("#PatientPatientBillTypeId").val("");
            $("#PatientCompanyInsuranceId").val("");
            $("#PatientInsuranceNote").val("");
            
            $("#PatientCompanyInsuranceId").hide();
            $("#PatientInsuranceNote").hide();
            $(".searchPatient").show();
            $(".deleteSearchPatient").hide();
            
            checkPatientCompany();
        });
        // close delete search patient
        
                
        // for condition open form add new service
        if($("#PatientCompanyId").val()!="" && $("#PatientPatientGroupId").val()!=""){
            $("#addQuotationService").show();
        }else{
            $("#addQuotationService").hide();
        }
        
        $("#PatientCompanyId").change(function() {
            if($("#PatientCompanyId").val()!="" && $("#PatientPatientGroupId").val()!=""){
                $("#addQuotationService").show();
            }else{
                $("#addQuotationService").hide();
            }
        });
        
        $("#PatientPatientBillTypeId").change(function(){
            var patientBillType = $("#PatientPatientBillTypeId").val();            
            if(patientBillType==3){
                $("#PatientCompanyInsuranceId").show();
                if($("#PatientCompanyInsuranceId").val()==0 && $("#PatientCompanyInsuranceId").val()!=""){
                    $("#insuranceNote").show();
                }else{
                    $("#insuranceNote").hide();
                }
            }else{
                $("#PatientCompanyInsuranceId").hide();
            }
        });
        $("#PatientCompanyInsuranceId").change(function(){
            if($("#PatientCompanyInsuranceId").val()==0 && $("#PatientCompanyInsuranceId").val()!=""){
                $("#insuranceNote").show();
            }else{
                $("#insuranceNote").hide();
            }
        });
        
        
        
        // form add new service for quotation
        $(".btnAddType").click(function() {                        
            var id = "";
            $("#example").find(".QuatationServiceTr:last").clone(true).appendTo("#example");
            id = $("#example").find(".classSection:last").attr('rel');
            if(id == ""){
                var id = $("#example").find(".serviceId:last").text();
            }else {
                id++; 
            }
            
            // update id in tr : last            
            
            $("#example").find(".classSection:last").attr('id', 'ServiceSectionId'+id);
            $("#example").find(".classSection:last").attr('rel', id);
            $("#example").find(".patientQuatationService:last").attr('id', 'PatientServiceId'+id);            
            $("#example").find(".qty:last").attr('id', 'PatientQty'+id);               
            $("#example").find(".unit_price:last").attr('id', 'PatientUnitPrice'+id);            
            
            $("#example").find(".QuatationServiceTr:last").find("td .btnRemoveType").show();
            $(this).siblings(".btnRemoveType").show();
            $(this).hide(); 
            comboRefeshType();
            staffRefreshType()            
           
        });
        $(".btnRemoveType").click(function() {
            $(this).closest(".QuatationServiceTr").remove();
            $("#example").find(".QuatationServiceTr:last").find("td .btnAddType").show();            
            if ($('#example .QuatationServiceTr').length == 1) {
                $("#example").find(".QuatationServiceTr:last").find("td .btnRemoveType").hide();
            }
            sortNuTableCheckOut();
        });        
        // close form service for quotation
        
        // for condition foreiner or cambodian
        if($("#PatientPatientGroupId").val()==2){
            $("#PatientNationality").show();
        }else if($("#PatientPatientGroupId").val()==1){
            $("#PatientNationality").find("option[value='']").attr("selected", true);
            $("#PatientNationality").hide();
        }else{
            $("#PatientNationality").find("option[value='']").attr("selected", true);
            $("#PatientNationality").hide();
        }        
        
        $("#PatientPatientGroupId").change(function(){            
            if($("#PatientPatientGroupId").val()==2){
                $("#PatientNationality").show();
            }else if($("#PatientPatientGroupId").val()==1){
                $("#PatientNationality").find("option[value='']").attr("selected", true);
                $("#PatientNationality").hide();
            }else{
                $("#PatientNationality").find("option[value='']").attr("selected", true);
                $("#PatientNationality").hide();                
            }
            
            // condition show form quotation service
            if($("#PatientCompanyId").val()!="" && $("#PatientPatientGroupId").val()!=""){
                $("#addQuotationService").show();
                
                $(".unit_price").each(function() {
                    var serviceId = $(this).attr('rel');  
                    var index = $(this).attr('index');
                    if(index==""){
                        $.ajax({
                            type: "POST",
                            url: '<?php echo $absolute_url . $this->params['controller']; ?>/getServicePrice/' + serviceId + '/' + $("#PatientPatientGroupId").val() + '/' + $("#PatientCompanyInsuranceId").val(),
                            data:"",
                            success: function(msg){                                    
                                var unitPrice=msg.split('/')[0];
                                $('#PatientUnitPrice').val(Number(unitPrice).toFixed(2));
                            }
                        });
                    }else{
                        $.ajax({
                            type: "POST",
                            url: '<?php echo $absolute_url . $this->params['controller']; ?>/getServicePrice/' + serviceId + '/' + $("#PatientPatientGroupId").val() + '/' + $("#PatientCompanyInsuranceId").val(),
                            data:"",
                            success: function(msg){                                    
                                var unitPrice=msg.split('/')[0];
                                $('#PatientUnitPrice'+index).val(Number(unitPrice).toFixed(2));
                            }
                        });
                    }
                });
                
                
            }else{
                $("#addQuotationService").hide();
            }
            // close form quotation
        });
        // close condition foreiner or cambodian
        
        // for sort section in company
        $(".classCompany").change(function(){
            if($(this).val()!=''){
                $(".classSection").closest("tr").find("td .classSection").val('');
                $(".classSection").closest("tr").find("td .classSection option[class!='']").hide();
                $(".classSection").closest("tr").find("td .classSection option[class='"  + $(this).val() + "']").show();
            }else{                
                $(".classSection").closest("tr").find("td .classSection option[class!='']").show();
            }       
            comboRefesh();
        });

        $(".classSection").change(function(){
            $("#ServiceCompanyId").closest("tr").find("td .classCompany").val($(this).find("option:selected").attr("class"));            
            var serId = this.id;                             
            var pateintGroup = $("#PatientPatientGroupId").val();           
            $.ajax({
                type: "POST",
                url: '<?php echo $absolute_url . $this->params['controller']; ?>/getService/' + $(this).val(),
                data: "",
                success: function(msg){
                    
                    if(serId.length > 16){
                        var getSerId = serId.substr(16, serId.length)*1;
                        var values = [];
                        $('select.patientQuatationService').each(function(){
                            values.push($(this).val());
                        });
                        
                        $("#PatientServiceId"+getSerId).html(msg).find("option").each(function(){
                            var s = $(this);
                            $.each(values,function(v,i){
                                if(s.val()== i && s.val() != ''){
                                    s.hide();
                                }
                            })
                        });                  
                        var service = $('select.patientQuatationService');                                                
                        $("#PatientServiceId"+getSerId).live('change',function(){
                            
                            values = [];
                            service.each(function(){
                                if($(this).val()!=''){
                                    values.push($(this).val());
                                }
                            });
                            service.find('option').show().each(function(){
                                if($.inArray(this.value, values) != -1){
                                    $(this).hide();
                                }
                            });
                            var serviceId = $(this).val();
                            $.ajax({
                                type: "POST",
                                url: '<?php echo $absolute_url . $this->params['controller']; ?>/getServicePrice/' + $(this).val() + '/' + pateintGroup + '/' + $("#PatientCompanyInsuranceId").val(),
                                data:"",
                                success: function(msg){                                    
                                    var unitPrice=msg.split('/')[0];
                                    // insert qty
                                    $('#PatientQty'+getSerId).val(1);
                                    $('#PatientQty'+getSerId).attr('rel', serviceId);
                                    $('#PatientQty'+getSerId).attr('index', getSerId);
                                    
                                    $('#PatientUnitPrice'+getSerId).val(Number(unitPrice).toFixed(2));
                                    $('#PatientUnitPrice'+getSerId).attr('rel', serviceId);
                                    $('#PatientUnitPrice'+getSerId).attr('index', getSerId);
                                }
                            });
                        });
                    } else{
                    
                        var getSerId = serId.substr(16, serId.length)*1 + 1;                                                
                        var values = [];
                        $('select.patientQuatationService').each(function(){
                            values.push($(this).val());
                        });           
                        
                        $("#PatientServiceId").html(msg).find("option").each(function(){
                            var s = $(this);
                            $.each(values,function(v,i){
                                if(s.val()== i && s.val() != ''){
                                    s.hide();
                                }
                            })
                        });                  
                        var service = $('select.patientQuatationService');                                                
                        $("#PatientServiceId").live('change',function(){
                            var getId = this.id;
                            var getSer = getId.substr(7, getId.length)*1;
                            
                            values = [];
                            service.each(function(){
                                if($(this).val()!=''){
                                    values.push($(this).val());
                                }
                            });
                            service.find('option').show().each(function(){
                                if($.inArray(this.value, values) != -1){
                                    $(this).hide();
                                }
                            });
                            var serviceId = $(this).val();
                            $.ajax({
                                type: "POST",
                                url: '<?php echo $absolute_url . $this->params['controller']; ?>/getServicePrice/' + $(this).val() + '/' + pateintGroup + '/' + $("#PatientCompanyInsuranceId").val(),
                                data:"",
                                success: function(msg){
                                    // insert qty
                                    $('#PatientQty').val(1);
                                    $('#PatientQty').attr('rel', serviceId);
                                    $('#PatientQty').attr('index', '');
                                    var unitPrice=msg.split('/')[0];
                                    $('#PatientUnitPrice').val(Number(unitPrice).toFixed(2));
                                    $('#PatientUnitPrice').attr('rel', serviceId);
                                    $('#PatientUnitPrice').attr('index', '');
                                }
                            });
                            
                        });
                    
                    }
                }
            });                
        });                                                  
        
        $("#PatientDob" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
            yearRange: '-100:-0',
            maxDate: 0,
            beforeShow: function(){
                setTimeout(function(){
                    $("#ui-datepicker-div").css("z-index", 1000);
                }, 10);
            }
        }).unbind("blur");
        if($("#PatientDob").val()!=''){
            var now = (new Date()).getFullYear();
            var age = now - $("#PatientDob").val().split("-",1);
            $('#PatientAge').val(age);
        }
        $("#PatientDob").change(function(){
            var now = (new Date()).getFullYear();
            var age = now - $("#PatientDob").val().split("-",1);
            $('#PatientAge').val(age);
        });
        $("#PatientAge").keyup(function(){
            var now = (new Date()).getFullYear();
            var age = parseUniInt($("#PatientAge").val());
            var year = now - age;
            if($("#PatientDob").val()!=''){
                var dob = year + $("#PatientDob").val().substr(-6);
            }else{
                var dob = year + '-01-01';
            }
            $('#PatientDob').val(dob);
        });
        
        $(".btnBackPatientEstimateExpense").dblclick(function(event){
            event.preventDefault();
            $('#PatientAddQuotationForm').validationEngine('hideAll');
            oCache.iCacheLower = -1;
            oTableEstimateExpense.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });
        
    // check patient's company and national of patient    
    function checkPatientCompany(){
        if($("#PatientCompanyId").val()!="" && $("#PatientPatientGroupId").val()!=""){
                $("#addQuotationService").show();
        }
    }
        
    // search all patient with button search
    function searchAllPatient(){      
        $.ajax({
            type:   "POST",
            url:    "<?php echo $this->base . '/' . $this->params['controller']; ?>/getFindPatient/",
            data:   "",
            beforeSend: function(){
                $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
            },
            success: function(msg){
                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                $("#dialog").html(msg).dialog({
                    title: '<?php echo MENU_CUSTOMER_MANAGEMENT_INFO; ?>',
                    resizable: false,
                    modal: true,
                    width: 900,
                    height: 600,
                    position:'center',
                    closeOnEscape: true,
                    open: function(event, ui){
                        $(".ui-dialog-buttonpane").show(); $(".ui-dialog-titlebar-close").show();
                    },
                    buttons: {
                        '<?php echo ACTION_OK; ?>': function() {
                            if($("input[name='chkPatient']:checked").val()){
                                value = $("input[name='chkPatient']:checked").attr("rel");
                                $(".trPatientCode").text(value.toString().split(".*")[2]);
                                $("#patientCode").val(value.toString().split(".*")[2]);
                                $("#PatientPatientName").val(value.toString().split(".*")[1]);
                                $("#patientId").val(value.toString().split(".*")[0]);
                                if(value.toString().split(".*")[4]!=""){
                                    $("#PatientDob").val(value.toString().split(".*")[4]);
                                    var now = (new Date()).getFullYear();
                                    var age = now - $("#PatientDob").val().split("-",1);
                                    $('#PatientAge').val(age);
                                }                     

                                // condition select gender of patient
                                if(value.toString().split(".*")[3]!=""){
                                    $("#PatientSex").find("option").each(function(){                    
                                        if($(this).val()==value.toString().split(".*")[3]){                        
                                            $(this).attr("selected", true);
                                        }
                                    });
                                }
                                // condition select patient's group
                                if(value.toString().split(".*")[5]!=""){
                                    $("#PatientPatientGroupId").find("option").each(function(){                    
                                        if($(this).val()==value.toString().split(".*")[5]){
                                            $(this).attr("selected", true);
                                            if(value.toString().split(".*")[5]=="2"){
                                                $("#PatientNationality").show();                            
                                                $("#PatientNationality").find("option").each(function(){
                                                    if($(this).val()==value.toString().split(".*")[11]){
                                                        $(this).attr("selected", true);
                                                    }
                                                });
                                            }
                                        }
                                    });
                                }
                                // condition select patient's location
                                if(value.toString().split(".*")[10]!=""){
                                    $("#PatientLocationId").find("option").each(function(){                    
                                        if($(this).val()==value.toString().split(".*")[10]){                        
                                            $(this).attr("selected", true);
                                        }
                                    });
                                }
                                
                                // condition select patient type of bill 
                                if(value.toString().split(".*")[11]!=""){
                                    $("#PatientPatientBillTypeId").find("option").each(function(){                    
                                        if($(this).val()==value.toString().split(".*")[11]){                        
                                            $(this).attr("selected", true);
                                        }                    
                                    });
                                    if(value.toString().split(".*")[11] == 3){
                                        $("#PatientCompanyInsuranceId").show();
                                        $("#PatientCompanyInsuranceId").find("option").each(function(){                    
                                            if($(this).val()==value.toString().split(".*")[12]){                        
                                                $(this).attr("selected", true);
                                            }                    
                                        });
                                        if(value.toString().split(".*")[12]==0){
                                            $("#PatientInsuranceNote").val(value.toString().split(".*")[13]);
                                            $("#insuranceNote").show();
                                        }
                                    }

                                }

                                $("#PatientEmail").val(value.toString().split(".*")[6]);
                                $("#PatientOccupation").val(value.toString().split(".*")[7]);
                                $("#PatientTelephone").val(value.toString().split(".*")[8]);
                                $("#PatientAddress").val(value.toString().split(".*")[9]);
                                
                                // action hidden search button and show delete button
                                $(".searchPatient").hide();
                                $(".deleteSearchPatient").show();
                                
                                checkPatientCompany();
                            }
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
    }
    
    function comboRefesh(){
        selected=new Array();
        $(".classSection").each(function(){
            if($(this).val()!=''){
                selected.push($(this).val());
            }
        });           
    }
    function comboRefeshClassType(){
        selected=new Array();
        $(".classCompany").each(function(){
            if($(this).val()!=''){
                selected.push($(this).val());
            }
        });              
    }
    
    
    function isNumberKey(event){
        var charCode = (event.which)?event.which : event.keyCode;
        if ((charCode > 31 && (charCode < 46 || charCode > 57))|| charCode === 47){
            return false;
        }
        return true;
    }
    
    function staffRefreshType() {        
        var i = Number($("#example").find(".serviceId:last").text())+1;        
        $("#example").find(".serviceId:last").text(i);
      
    }
    function comboRefeshType() {                 
        $(".unit_price").each(function() {
            $("#example").find(".unit_price:last").val("");
        }); 
        $(".qty").each(function() {
            $("#example").find(".qty:last").val("");
        });
        $(".description").each(function() {
            $("#example").find(".description:last").val("");
        });
        var i = 1;
        $(".patientQuatationService").each(function() {
            $("#example").find(".patientQuatationService:last").val("");            
            i++;
        });
    }
    function sortNuTableCheckOut(){
        var sort = 1;
        $(".QuatationServiceTr").each(function(){
            $(this).find("td:eq(0)").html(sort);
            sort++;
        });
    }
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="#" class="positive btnBackPatientEstimateExpense">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('Patient'); ?>
<input id="patientCode" name="data[Patient][patient_code]" type="hidden" value="<?php echo $code;?>"/>
<input id="getPatientCode" type="hidden" value="<?php echo $code;?>"/>
<input id="patientId" name="data[Patient][id]" type="hidden" value=""/>
<fieldset>
    <legend><?php __(MENU_QUOTATION_PATIENT_MANAGEMENT_INFO); ?></legend>
    <table style="width: 100%;" cellspacing="0">
        <tr>
            <td><?php echo PATIENT_CODE; ?> <span class="red">*</span> :</td>
            <td class="trPatientCode">                
                <?php echo $code; ?>                
            </td>
            <td><label for="PatientDob"><?php echo TABLE_DOB; ?> <span class="red">*</span> :</label></td>
            <td>
                <?php echo $this->Form->text('dob', array('class' => 'validate[required]','onkeypress'=>'return isNumberKey(event)')); ?>
                <label for="PatientAge"><?php echo TABLE_AGE; ?>:</label>
                <?php echo $this->Form->text('age',array('style'=>'width:30px;', 'class' => 'number validate[required,maxSize[3]]', 'maxlength' => '3')); ?>
            </td>
        </tr>
        <tr>
            <td><label for="PatientPatientName"><?php echo PATIENT_NAME; ?> <span class="red">*</span> :</label></td>
            <td>
                <?php echo $this->Form->text('patient_name', array('class' => 'validate[required]')); ?>
                <img alt="Search" align="absmiddle" style="cursor: pointer;width: 22px; height: 22px;" class="searchPatient" onmouseover="Tip('<?php echo GENERAL_SEARCH; ?>')" src="<?php echo $this->webroot . 'img/button/search.png'; ?>" />
                <img alt="Delete" align="absmiddle" style="cursor: pointer; display: none;" class="deleteSearchPatient" onmouseover="Tip('<?php echo ACTION_DELETE; ?>')" src="<?php echo $this->webroot . 'img/button/delete.png'; ?>" />
            </td>
            <td><label for="PatientNationality"><?php echo TABLE_NATIONALITY; ?> <span class="red">*</span> :</label></td>
            <td>
                <?php echo $this->Form->input('patient_group_id', array('empty' => SELECT_OPTION, 'label' => false,'class' => 'validate[required]', 'style' => 'width:150px;')); ?>                
                <?php echo $this->Form->input('nationality', array('empty' => SELECT_OPTION, 'label' => false,'class' => 'validate[required]', 'style' => 'width:156px;display:none;')); ?>
            </td>
        </tr>      
        <tr>
            <td><label for="PatientSex"><?php echo TABLE_SEX; ?> <span class="red">*</span> :</label></td>
            <td><?php echo $this->Form->input('sex', array('empty' => SELECT_OPTION, 'label' => false, 'class' => 'validate[required]')); ?></td>
            <td><label for="PatientEmail"><?php echo TABLE_EMAIL; ?> :</label></td>
            <td><?php echo $this->Form->text('email', array('class' => 'validate[custom[email]]')); ?></td>            
        </tr>
        <tr>            
            <td><label for="PatientOccupation"><?php echo TABLE_OCCUPATION; ?> :</label></td>
            <td><?php echo $this->Form->text('occupation'); ?></td>
            <td><label for="PatientTelephone"><?php echo TABLE_TELEPHONE; ?>:</label></td>
            <td><?php echo $this->Form->text('telephone', array('class' => 'validate[custom[phone]]')); ?></td>
        </tr>        
        <tr>
            <td><label for="PatientAddress"><?php echo TABLE_ADDRESS; ?> :</label></td>
            <td><?php echo $this->Form->text('address'); ?></td>
            <td><label for="PatientLocationId"><?php echo TABLE_CITY_PROVINCE; ?> :</label></td>
            <td><?php echo $this->Form->input('location_id', array('empty' => SELECT_OPTION, 'label' => false)); ?></td>            
        </tr>
        <tr>
            <td><label for="PatientPatientBillTypeId"><?php echo TABLE_BILL_PAID_BY; ?><span class="red">*</span> :</label></td>
            <td>
                <?php echo $this->Form->input('patient_bill_type_id', array('empty' => SELECT_OPTION, 'label' => false, 'class' => 'validate[required]', 'style' => 'width:153px;')); ?>
                <?php echo $this->Form->input('company_insurance_id', array('empty' => SELECT_OPTION, 'label' => false, 'class' => 'validate[required]', 'style' => 'width:153px;')); ?>
            </td>            
            <td><label for="PatientPatientTypeId"><?php echo TABLE_PATIENT_TYPE; ?> :</label></td>
            <td><?php echo $this->Form->input('patient_type_id', array('empty' => SELECT_OPTION, 'default' => '4' , 'label' => false)); ?></td>
        </tr>
        <tr id="insuranceNote" style="display: none;">
            <td><?php echo TABLE_NOTE;?></td>
            <td>
                <?php echo $this->Form->textarea('insurance_note', array('label' => false, 'style' => 'width:295px;')); ?>
            </td>
        </tr>
        <tr>
            <td><label for="PatientExcludeQuatation"><?php echo MENU_EXCLUDE; ?> :</label></td>
            <td>
                <select id="PatientExcludeQuatation" class="chzn-select" name="data[Patient][exclude_quotation_id][]" multiple="true">                        
                    <?php
                    foreach ($excludeQuotations as $excludeQuotation) {
                        echo '<option title="' . $excludeQuotation['ExcludeQuotation']['name_' . $_SESSION['lang']] . '" value="' . $excludeQuotation['ExcludeQuotation']['id'] . '" >' . $excludeQuotation['ExcludeQuotation']['name_' . $_SESSION['lang']] . '</option>';
                    }
                    ?>
                </select> 
            </td>
            <td><label for="PatientCompanyId"><?php echo TABLE_COMPANY; ?> <span class="red">*</span> :</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->input('company_id', array('empty' => SELECT_OPTION, 'default' => '1', 'class' => 'classCompany validate[required]', 'label' => false)); ?>
                </div>
            </td>
        </tr>
    </table>     
</fieldset>
<br/>
<div class="clear"></div>

<fieldset id="addQuotationService" style="display:none;">
    <legend><?php __(MENU_QUOTATION_PATIENT_SERVICE_INFO); ?></legend>
    <table id="example" class="table" cellspacing="0">
        <tr>
            <th class="first"><?php echo TABLE_NO; ?></th>
            <th><?php echo SECTION_SECTION; ?></th>
            <th><?php echo TABLE_SERVICE_NAME; ?></th>
            <th><?php echo TABLE_QTY; ?></th>
            <th><?php echo GENERAL_PRICE; ?> ($)</th>
            <th><?php echo DRUG_NOTE; ?></th>
            <th>&nbsp;</th>
        </tr>
        <tr class="QuatationServiceTr">
            <td class="first serviceId">1</td>            
            <td>
                <div class="inputContainer">
                    <?php 
                    $query = mysql_query("SELECT sections.id, sections.name, section_companies.company_id  FROM `sections` INNER JOIN section_companies ON sections.id = section_companies.section_id 
                                            WHERE sections.id IN (SELECT section_id FROM section_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = '".$user['User']['id']."'))")
                    ?>
                    <select id="ServiceSectionId" rel="" class="classSection validate[required]" name="data[Service][section_id]" style="width:220px;">
                        <option value=""><?php echo SELECT_OPTION;?></option>
                        <?php while ($row = mysql_fetch_array($query)) {
                            echo '<option class="'.$row['company_id'].'" value="'.$row['id'].'">'.$row['name'].'</option>';

                        }?>
                    </select>
                </div>                    
            </td>
            <td>
                <?php echo $this->Form->input('service_id', array('name' => 'data[Patient][service_id][]', 'empty' => SELECT_OPTION, 'label' => false,'class' => 'patientQuatationService validate[required]', 'style' => 'width:220px;')); ?>
            </td>
            <td>
                <?php echo $this->Form->text('qty', array('name' => 'data[Patient][qty][]', 'class' => 'qty float validate[required]', 'style' => 'width:100px;')); ?> 
            </td>
            <td>
                <?php echo $this->Form->text('unit_price', array('name' => 'data[Patient][unit_price][]', 'class' => 'unit_price float validate[required]', 'style' => 'width:220px;', 'readonly' => true)); ?> 
            </td>
            <td>
                <?php echo $this->Form->text('description', array('name' => 'data[Patient][description][]', 'class' => 'description', 'style' => 'width:200px;')); ?> 
            </td>
            <td><img alt="" src="<?php echo $this->webroot; ?>img/button/plus.png" class="btnAddType" style="cursor: pointer;" />
                <img alt="" src="<?php echo $this->webroot; ?>img/button/cross.png" class="btnRemoveType" style="cursor: pointer;display: none;" />
            </td>
        </tr>
    </table>
</fieldset>
<br/>
<div class="buttons">
    <button type="submit" class="positive savePatient" >
        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
        <span class="txtSavePatient"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<?php echo $this->Form->end(); ?>
<div id="dialog"></div>

