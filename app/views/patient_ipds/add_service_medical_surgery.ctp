<?php
include('includes/function.php');
echo $this->element('prevent_multiple_submit');
$absolute_url = FULL_BASE_URL . Router::url("/", false);
echo $javascript->link('uninums.min');
$exchangeRate = getExchangeRate();
?>
<style type="text/css">
    .input{
        float:left;
    }
</style>
<script type="text/javascript">
    var selected;  
    function comboRefesh(){
        selected=new Array();
        $(".classSection").each(function(){
            if($(this).val()!=''){
                selected.push($(this).val());
            }
        });           
    }
    
    function sortNuTablePatientIpd(){
        var sort = 1;
        $(".PatientIpdAddMedicalSurgeryTr").each(function(){
            $(this).find("td:eq(0)").html(sort);
            sort++;
        });
    }
    
    function staffRefreshType() {                
        var i = Number($("#example").find(".serviceId:last").text())+1;        
        $("#example").find(".serviceId:last").text(i);                
        
    }
    function comboRefeshType() {
        $(".classSection").each(function() {
            $("#example").find(".classSection:last").val("");
        });
        $(".patientPatientIpdService").each(function() {
            $("#example").find(".patientPatientIpdService:last").val("");
        });
        $(".classDoctor").each(function() {
            $("#example").find(".classDoctor:last").val("");
        });
        $(".qty").each(function() {
            $("#example").find(".qty:last").val("");
        });   
        $(".unit_price").each(function() {
            $("#example").find(".unit_price:last").val("");
        });
        $(".total_price").each(function() {
            $("#example").find(".total_price:last").val("");
        });
        var i = 1;
        $(".patientPatientIpdUnitPrice").each(function() {
            $("#example").find(".patientPatientIpdUnitPrice:last").val("");            
            i++;
        });
    }
    
    function getTotalAmountPatientPatientIpd(){
        var totalAmount      = 0;
        var totalAmountPaid = 0;
        
        var totalAmountPaid = parseFloat($("#PatientTotalAmountPaid").val());        
        
        totalAmountPaid     = totalAmountPaid !="" ? totalAmountPaid : 0;        
        $(".total_price").each(function(){                   
            if($.trim($(this).val()) != '' || $(this).val() != undefined ){                
                totalAmount += Number($(this).val());
            }
        });
        if(isNaN(totalAmount)){
            $("#PatientTotalAmount").val(0.00);            
            $("#PatientMarkUp").val(0.00);
            $("#PatientSubTotalAmount").val(0.00);            
        }else{            
            $("#PatientTotalAmount").val((totalAmount).toFixed(2));            
            var total_sum = totalAmountPaid;
            $("#PatientSubTotalAmount").val((totalAmount - total_sum).toFixed(2));
        }
    }
    
    $(document).ready(function(){        
        // Prevent Key Enter
        preventKeyEnter();        
        $("#PatientIpdAddServiceMedicalSurgeryForm").validationEngine();
        $("#PatientIpdAddServiceMedicalSurgeryForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSavePatient").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");                
                $(".btnBackPatientMedicalSurgery").dblclick();
                // alert message
                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result+'</p>');
                $("#dialog").dialog({
                    title: '<?php echo DIALOG_INFORMATION; ?>',
                    resizable: false,
                    modal: true,
                    width: 'auto',
                    height: 'auto',
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
        $(".savePatientMedicalSurgery").click(function(){
            if(checkBfSavePatient() == true){
                return true;
            }else{
                return false;
            }
        });
        getTotalAmountPatientPatientIpd();
        $(".btnDateCreated").datepicker({
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
                        
         // for sort section in company
        if($("#PatientCompanyId").val()!='' && $("#PatientPatientGroupId").val()!=""){
            $("#addPatientPatientIpd").show();                        
        }else{            
            $("#addPatientPatientIpd").hide();
        }     
        
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
        
        // close company
        $("#PatientIpdCompanyInsuranceId").change(function(){
            if($("#PatientCompanyId").val()!='' && $("#PatientPatientGroupId").val()!="" && $("#PatientIpdCompanyInsuranceId").val()!=""){
                $("#addPatientPatientIpd").show();
                $(".classSection").closest("tr").find("td .classSection").val('');
                $(".classSection").closest("tr").find("td .classSection option[class!='']").hide();
                $(".classSection").closest("tr").find("td .classSection option[class='"  + $("#PatientCompanyId").val() + "']").show();
            }else{
                $("#addPatientPatientIpd").hide();
            }
        });
        
        $(".classSection").change(function(){
            $("#ServiceCompanyId").closest("tr").find("td .classCompany").val($(this).find("option:selected").attr("class"));            
            var serId = this.id;                             
            var pateintGroup = $("#PatientPatientGroupId").val();
            var companyInsuranceId = $("#PatientIpdCompanyInsuranceId").val();
            $.ajax({
                type: "POST",
                url: '<?php echo $absolute_url . $this->params['controller']; ?>/getService/' + $(this).val(),
                data: "",
                success: function(msg){
                    
                    if(serId.length > 16){
                        var getSerId = serId.substr(16, serId.length)*1;                        
                        var values = [];
                        $('select.patientPatientIpdService').each(function(){
                            values.push($(this).val());
                        });
                                   
                        var service = $('select.patientPatientIpdService');                                                
                        $("#PatientIpdServiceId"+getSerId).live('change',function(){
                            
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
                                url: '<?php echo $absolute_url . $this->params['controller']; ?>/getServicePrice/' + $(this).val() + '/' + pateintGroup + '/' + companyInsuranceId,
                                data:"",
                                success: function(msg){                                    
                                    var unitPrice=msg.split('/')[0];
                                    $('#PatientIpdQty'+getSerId).val(1);                                    
                                    $('#PatientIpdUnitPrice'+getSerId).val(Number(unitPrice).toFixed(2));
                                    $('#PatientIpdTotalPrice'+getSerId).val(Number(unitPrice).toFixed(2));
                                    $('#PatientIpdUnitPrice'+getSerId).attr('rel', serviceId);
                                    $('#PatientIpdUnitPrice'+getSerId).attr('index', getSerId);
                                    getTotalAmountPatientPatientIpd();
                                }
                            });
                        });
                        
                    } else{
                        
                        var getSerId = serId.substr(16, serId.length)*1;                                                
                        var values = [];
                        $('select.patientPatientIpdService').each(function(){
                            values.push($(this).val());
                        });           
                        
                        $("#PatientIpdServiceId").html(msg).find("option").each(function(){
                            var s = $(this);
                            $.each(values,function(v,i){
                                if(s.val()== i && s.val() != ''){
                                    s.hide();
                                }
                            })
                        });                  
                        var service = $('select.patientPatientIpdService');                                                
                        $("#PatientIpdServiceId").live('change',function(){                            
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
                                url: '<?php echo $absolute_url . $this->params['controller']; ?>/getServicePrice/' + $(this).val() + '/' + pateintGroup + '/' + companyInsuranceId,
                                data:"",
                                success: function(msg){
                                    var unitPrice=msg.split('/')[0];
                                    $('#PatientIpdQty').val(1);
                                    $('#PatientIpdUnitPrice').val(Number(unitPrice).toFixed(2));
                                    $('#PatientIpdTotalPrice').val(Number(unitPrice).toFixed(2));
                                    $('#PatientIpdUnitPrice').attr('rel', serviceId);
                                    $('#PatientIpdUnitPrice').attr('index', '');
                                    getTotalAmountPatientPatientIpd();
                                }
                            });                              
                        });                        
                    }
                }
            });
                
        });
    
    
        $(".qty").blur(function(){
            var qtyId = this.id; 
            var id = $(this).attr('rel');            
            if($(this).val() == ""){                                    
                $(this).val('1');                                
                var totalPrice = Number($("#PatientIpdUnitPrice"+id).val())*$(this).val();
                $('#PatientIpdTotalPrice'+id).val(Number(totalPrice).toFixed(2));
                getTotalAmountPatientPatientIpd();
            }
        });
        $(".qty").live('click',function(){  
            var id = $(this).attr('rel');            
            $("#PatientIpdQty"+id).val("");                                 
        });
        $(".qty").live('keyup',function(){                                
            var id = $(this).attr('rel');            
            var totalPrice = (Number($("#PatientIpdUnitPrice"+id).val())*$(this).val());
            $('#PatientIpdTotalPrice'+id).val(Number(totalPrice).toFixed(2));
            getTotalAmountPatientPatientIpd();
        });
        
        $("#PatientTotalAmountPaid").blur(function(){
            if($(this).val() == ""){                                    
                $(this).val('0.00');
                getTotalAmountPatientPatientIpd();
            }
        });
        $("#PatientTotalAmountPaid").live('click',function(){              
            $("#PatientTotalAmountPaid").val("");
        });
        
        $("#PatientTotalAmountPaid").live('keyup',function(){            
            getTotalAmountPatientPatientIpd();
        });            
        
        // form add new service for quotation
        $(".btnAddType").click(function() {
            
            $('input.btnDateCreated').datepicker('destroy');
            var id = "";                        
            var clone = $("#example").find(".PatientIpdAddMedicalSurgeryTr:last").clone(true);
            clone.appendTo("#tableToModify");                                    
            
            id = $("#example").find(".classSection:last").attr('rel');            
            if(id == ""){
                var id = $("#example").find(".serviceId:last").text();
            }else {
                id++; 
            }                            
                        
            // update id in tr : last                        
            
            $("#example").find(".classSection:last").attr('id', 'ServiceSectionId'+id);
            $("#example").find(".classSection:last").attr('rel', id);
            $("#example").find(".patientPatientIpdService:last").attr('id', 'PatientIpdServiceId'+id);
            $("#example").find(".classDoctor:last").attr('id', 'PatientIpdDoctorId'+id);
            $("#example").find(".qty:last").attr('id', 'PatientIpdQty'+id);
            $("#example").find(".qty:last").attr('rel', id);            
            $("#example").find(".btnDateCreated:last").attr('id', 'dateCreated'+id);
            $("#example").find(".btnDateCreated:last").attr('rel', id);            
            $("#example").find(".unit_price:last").attr('id', 'PatientIpdUnitPrice'+id);
            $("#example").find(".total_price:last").attr('id', 'PatientIpdTotalPrice'+id);            
            $("#example").find(".btnDateCreated:last").val("");            
            $("#example").find(".PatientIpdAddMedicalSurgeryTr:last").find("td .btnRemoveType").show();                                                            
             
            $(this).siblings(".btnRemoveType").show();
            $(this).hide();           
            
            $('input.btnDateCreated').datepicker({
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
            
            comboRefeshType();
            staffRefreshType();                        
            
           
        });
        $(".btnRemoveType").click(function() {
            $(this).closest(".PatientIpdAddMedicalSurgeryTr").remove();
            $("#example").find(".PatientIpdAddMedicalSurgeryTr:last").find("td .btnAddType").show();            
            if ($('#example .PatientIpdAddMedicalSurgeryTr').length == 1) {
                $("#example").find(".PatientIpdAddMedicalSurgeryTr:last").find("td .btnRemoveType").hide();
            }            
            sortNuTablePatientIpd();
            getTotalAmountPatientPatientIpd();
        });        
        // clodse form service for quotation    
        
        $(".btnBackPatientMedicalSurgery").dblclick(function(event){
            event.preventDefault();
            $('#PatientIpdAddServiceMedicalSurgeryForm').validationEngine('hideAll');
            oCache.iCacheLower = -1;
            oTableMedicalSurgery.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });
    function checkBfSavePatient(){
        var formName = "#PatientIpdAddServiceMedicalSurgeryForm";
        var validateBack =$(formName).validationEngine("validate");
        if(!validateBack){            
            return false;
        }else{            
            return true;
        }
    }   
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="#" class="positive btnBackPatientMedicalSurgery">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('PatientIpd'); ?>
<input type="hidden" name="data[PatientIpd][id]" value="<?php echo $patientIpdId; ?>" />
<input type="hidden" name="data[Patient][exchange_rate_id]" value="<?php echo getExchangeRateId(); ?>" />
<input type="hidden" name="data[Patient][exchange_rate]" value="<?php echo $exchangeRate; ?>" />
<input type="hidden" name="data[Patient][ipd_type]" value="<?php echo $ipdType; ?>" />
<fieldset>
    <legend><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?></legend>
    <table class="info">
        <tr>
            <th style="width: 10%;"><?php __(PATIENT_CODE); ?></th>
            <td style="width: 40%;">: <?php echo $patient['Patient']['patient_code']; ?></td>
            <th style="width: 10%;"><?php __(TABLE_DOB); ?></th>
            <td style="width: 40%;">: 
                <?php echo date("d/m/Y", strtotime($patient['Patient']['dob'])); ?>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?php
                echo TABLE_AGE . ': ';
                $then_ts = strtotime($patient['Patient']['dob']);
                $then_year = date('Y', $then_ts);
                $age = date('Y') - $then_year;
                if (strtotime('+' . $age . ' years', $then_ts) > time())
                    $age--;

                if ($age == 0) {
                    $then_year = date('m', $then_ts);
                    $month = date('m') - $then_year;
                    if (strtotime('+' . $month . ' month', $then_ts) > time())
                        $month--;
                    echo $month . ' ' . GENERAL_MONTH;
                }else {
                    echo $age . ' ' . GENERAL_YEAR_OLD;
                }
                ?> 
            </td>
        </tr>
        <tr>
            <th><?php __(PATIENT_NAME); ?></th>
            <td>: <?php echo $patient['Patient']['patient_name']; ?></td>
            <th><?php __(TABLE_SEX); ?></th>
            <td>: 
                <?php
                if ($patient['Patient']['sex'] == "F") {
                    echo GENERAL_FEMALE;
                } else {
                    echo GENERAL_MALE;
                }
                ?>
            </td>        
        </tr>   
        <tr>
            <th><?php __(TABLE_TELEPHONE); ?></th>
            <td>: <?php echo $patient['Patient']['telephone']; ?></td>
            <th><?php __(TABLE_EMAIL); ?></th>
            <td>: <?php echo $patient['Patient']['email']; ?></td>        
        </tr>
        <tr>        
            <th><?php __(TABLE_ADDRESS); ?></th>
            <td>: 
                <?php echo $patient['Patient']['address']; ?>
                <?php
                if ($patient['Patient']['location_id'] != "") {
                    $query = mysql_query("SELECT name FROM patient_locations WHERE id=" . $patient['Patient']['location_id']);
                    while ($row = mysql_fetch_array($query)) {
                        echo $row['name'];
                    }
                }
                ?>
            </td>
            <th><?php __(TABLE_NATIONALITY); ?></th>
            <td>: 
                <?php
                if ($patient['Patient']['patient_group_id'] != "") {
                    $query = mysql_query("SELECT name FROM patient_groups WHERE id=" . $patient['Patient']['patient_group_id']);
                    while ($row = mysql_fetch_array($query)) {
                        if ($patient['Patient']['patient_group_id'] == 1) {
                            echo $row['name'];
                        } else {
                            echo $row['name'] . '&nbsp;&nbsp;(' . $patient['Nationality']['name'] . ')';
                        }
                    }
                } else {
                    echo $patient['Nationality']['name'];
                }
                ?>
                <input type="hidden" id="PatientPatientGroupId" value="<?php echo $patient['Patient']['patient_group_id']; ?>"/>
                <input type="hidden" id="PatientCompanyId" value="<?php echo $patientIpd['PatientIpd']['company_id']; ?>"/>
            </td>
        </tr>
        <tr>            
            <th><?php __(TABLE_BILL_PAID_BY); ?></th>
            <td>
                : <?php echo $patient['PatientBillType']['name']; ?>            
            </td>       
            <?php $queryPayment = mysql_query("SELECT id FROM patient_ipd_service_details WHERE is_active = 2 AND patient_ipd_id = ".$patientIpd['PatientIpd']['id']);?>
            <?php if ($patient['PatientBillType']['id'] == 3) { ?>            
                <th><?php echo '<label for="PatientIpdCompanyInsuranceId">' . TABLE_COMPANY_INSURANCE_NAME . ' <span class="red">*</span> : </label>'; ?></th>
                <td colspan="3">
                    <?php                     
                    if(mysql_num_rows($queryPayment)){
                        echo $this->Form->input('company_insurance_id', array('empty' => SELECT_OPTION, 'selected' => $patientIpd['PatientIpd']['company_insurance_id'], 'label' => false, 'class' => 'validate[required]', 'style' => 'width:200px;height: 35px;', 'disabled' => true)); 
                    }else{
                        echo $this->Form->input('company_insurance_id', array('empty' => SELECT_OPTION, 'selected' => $patientIpd['PatientIpd']['company_insurance_id'], 'label' => false, 'class' => 'validate[required]', 'style' => 'width:200px;height: 35px;')); 
                    }
                    ?>
                </td>
            <?php } ?>
        </tr>
    </table>      
</fieldset>
<div id="addPatientPatientIpd" style="display:none;">    
    <table id="example" class="table" cellspacing="0">
        <tr>
            <th class="first"><?php echo TABLE_NO; ?></th>
            <th><?php echo SECTION_SECTION; ?></th>
            <th><?php echo TABLE_SERVICE_NAME; ?></th>
            <th><?php echo DOCTOR_NAME; ?></th>
            <th><?php echo GENERAL_QTY; ?></th>
            <th><?php echo GENERAL_UNIT_PRICE; ?></th>
            <th><?php echo TABLE_CREATED; ?></th>
            <th><?php echo GENERAL_TOTAL_PRICE; ?></th>            
            <th>&nbsp;</th>
        </tr>
        <tbody id="tableToModify">
            <?php if(empty($dataServiceDetail)) {?>
                        <tr class="PatientIpdAddMedicalSurgeryTr">
                            <td class="first serviceId">1</td>            
                            <td>
                                <div class="inputContainer">
                                    <?php
                                    $query = mysql_query("SELECT sections.id, sections.name, section_companies.company_id  FROM `sections` INNER JOIN section_companies ON sections.id = section_companies.section_id 
                                                                WHERE sections.id IN (SELECT section_id FROM section_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = '" . $user['User']['id'] . "'))")
                                    ?>
                                    <select id="ServiceSectionId" rel="" class="classSection validate[required]" name="data[Patient][section_id][]" style="width:160px;">
                                        <option value=""><?php echo SELECT_OPTION; ?></option>
                                        <?php
                                        while ($row = mysql_fetch_array($query)) {                                            
                                            echo '<option class="' . $row['company_id'] . '" value="' . $row['id'] . '">' . $row['name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>                    
                            </td>
                            <td>
                                <?php echo $this->Form->input('service_id', array('name' => 'data[Patient][service_id][]', 'empty' => SELECT_OPTION, 'label' => false, 'class' => 'patientPatientIpdService validate[required]', 'style' => 'width:160px;')); ?>
                            </td>
                            <td>
                                <select id="PatientIpdDoctorId" style="width:130px;" name="data[Patient][doctor_id][]" class="classDoctor validate[required]">
                                    <option value=""><?php echo SELECT_OPTION;?></option>
                                    <?php 
                                    foreach ($doctors as $doctor) {
                                        echo '<option class="1" value="'.$doctor['DoctorConsultation']['id'].'">'.$doctor['DoctorConsultation']['name'].'</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <?php echo $this->Form->text('qty', array('name' => 'data[Patient][qty][]', 'class' => 'qty integer validate[required]', 'style' => 'width:50px;text-align:center;', 'rel' => "")); ?> 
                            </td>
                            <td>
                                <?php echo $this->Form->text('unit_price', array('name' => 'data[Patient][unit_price][]', 'class' => 'unit_price float validate[required]', 'readonly' => true, 'style' => 'width:100px;')); ?> 
                            </td>
                            <td>
                                <input type="text" id="dateCreated" class="btnDateCreated" name="data[Patient][date_created][]" style="width: 100px;" rel=""/>
                            </td>
                            <td>
                                <?php echo $this->Form->text('total_price', array('name' => 'data[Patient][total_price][]', 'class' => 'total_price', 'style' => 'width:120px;', 'readonly' => true)); ?>
                            </td>
                            <td style="padding: 5px 5px 5px 5px !important;;">
                                <img alt="" src="<?php echo $this->webroot; ?>img/button/plus.png" class="btnAddType" style="cursor: pointer;" />
                                <img alt="" src="<?php echo $this->webroot; ?>img/button/cross.png" class="btnRemoveType" style="cursor: pointer;display: none;" />
                            </td>
                        </tr>
            
            <?php }else {?>
                        
                    <?php 
                    $index = 1;
                    foreach ($dataServiceDetail as $resultServiceDetail) { ?>
                        <tr class="PatientIpdAddMedicalSurgeryTr">
                            <td class="first serviceId"><?php echo $index;?></td>            
                            <td>
                                <div class="inputContainer">
                                    <?php
                                    $query = mysql_query("SELECT sections.id, sections.name, section_companies.company_id  FROM `sections` INNER JOIN section_companies ON sections.id = section_companies.section_id 
                                                                WHERE sections.id IN (SELECT section_id FROM section_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = '" . $user['User']['id'] . "'))")
                                    ?>
                                    <select <?php if($resultServiceDetail['PatientIpdServiceDetail']['is_active']==2) { echo 'disabled="true"';}else { echo 'class="classSection validate[required]" name="data[Patient][section_id][]"';}?> id="ServiceSectionId<?php echo $index;?>" rel="<?php echo $index;?>" style="width:160px;">
                                        <option value=""><?php echo SELECT_OPTION; ?></option>
                                        <?php
                                        while ($row = mysql_fetch_array($query)) {
                                            if($resultServiceDetail['Section']['id']==$row['id'] && $row['company_id']==$patientIpd['PatientIpd']['company_id']){                                
                                                echo '<option selected="selected" class="'.$row['company_id'].'" value="'.$row['id'].'">'.$row['name'].'</option>';                                
                                            }else{
                                                if($row['company_id']==$patientIpd['PatientIpd']['company_id']){
                                                    echo '<option class="'.$row['company_id'].'" value="'.$row['id'].'">'.$row['name'].'</option>';
                                                }else{
                                                    echo '<option style="display:none;" class="'.$row['company_id'].'" value="'.$row['id'].'">'.$row['name'].'</option>';
                                                }

                                            }
                                        }
                                        ?>
                                    </select>
                                </div>                    
                            </td>
                            <td>
                                 <?php 
                                    if($resultServiceDetail['PatientIpdServiceDetail']['is_active']==2) { 
                                        echo $this->Form->input('service_id', array('selected' =>$resultServiceDetail['PatientIpdServiceDetail']['service_id'], 'id' => 'PatientIpdServiceId'.$index, 'name' => 'data[Patient][service_id][]', 'empty' => SELECT_OPTION, 'label' => false, 'style' => 'width:160px;', 'rel' => $index, 'disabled' => true));
                                        
                                    }else{
                                        echo $this->Form->input('service_id', array('selected' =>$resultServiceDetail['PatientIpdServiceDetail']['service_id'], 'id' => 'PatientIpdServiceId'.$index, 'name' => 'data[Patient][service_id][]', 'empty' => SELECT_OPTION, 'label' => false, 'class' => 'patientPatientIpdService validate[required]', 'style' => 'width:160px;', 'rel' => $index));
                                    }
                                 ?>                                 
                            </td>
                            <td>
                                <select name="data[Patient][doctor_id][]" <?php if($resultServiceDetail['PatientIpdServiceDetail']['is_active']==2) { echo 'disabled="true"';}?> id="PatientIpdDoctorId<?php echo $index;?>" class="classDoctor validate[required]" style="width:130px;" rel="<?php echo $index;?>" >
                                    <option value=""><?php echo SELECT_OPTION;?></option>
                                    <?php 
                                    foreach ($doctors as $doctor) {
                                        if($doctor['DoctorConsultation']['id']==$resultServiceDetail['PatientIpdServiceDetail']['doctor_id']){
                                            echo '<option selected="selected" class="1" value="'.$doctor['DoctorConsultation']['id'].'">'.$doctor['DoctorConsultation']['name'].'</option>';
                                        }else{
                                            echo '<option class="1" value="'.$doctor['DoctorConsultation']['id'].'">'.$doctor['DoctorConsultation']['name'].'</option>';
                                        }
                                        
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <?php 
                                if($resultServiceDetail['PatientIpdServiceDetail']['is_active']==2) { 
                                    echo $this->Form->text('qty', array('value' => $resultServiceDetail['PatientIpdServiceDetail']['qty'], 'id' => 'PatientIpdQty'.$index, 'name' => 'data[Patient][qty][]', 'class' => 'qty integer validate[required]', 'style' => 'width:50px;text-align:center;', 'rel' => $index, 'disabled' => true)); 
                                }else{
                                    echo $this->Form->text('qty', array('value' => $resultServiceDetail['PatientIpdServiceDetail']['qty'], 'id' => 'PatientIpdQty'.$index, 'name' => 'data[Patient][qty][]', 'class' => 'qty integer validate[required]', 'style' => 'width:50px;text-align:center;', 'rel' => $index)); 
                                }
                                ?>
                            </td>
                            <td>
                                <?php 
                                if($resultServiceDetail['PatientIpdServiceDetail']['is_active']==2) {                                     
                                    echo $this->Form->text('unit_price', array('value' => number_format($resultServiceDetail['PatientIpdServiceDetail']['unit_price'], 2), 'id' => 'PatientIpdUnitPrice'.$index, 'name' => 'data[Patient][unit_price][]', 'class' => 'unit_price float validate[required]', 'readonly' => true, 'style' => 'width:100px;', 'disabled' => true));
                                }else{
                                    echo $this->Form->text('unit_price', array('value' => number_format($resultServiceDetail['PatientIpdServiceDetail']['unit_price'], 2), 'id' => 'PatientIpdUnitPrice'.$index, 'name' => 'data[Patient][unit_price][]', 'class' => 'unit_price float validate[required]', 'readonly' => true, 'style' => 'width:100px;'));
                                }
                                ?>                                
                            </td>
                            <td>
                                <input <?php if($resultServiceDetail['PatientIpdServiceDetail']['is_active']==2) { echo 'disabled="true"';}?>  type="text" id="dateCreated<?php echo $index;?>" value="<?php echo $resultServiceDetail['PatientIpdServiceDetail']['date_created'];?>" class="btnDateCreated" name="data[Patient][date_created][]" style="width: 100px;" rel="<?php echo $index;?>"/>
                            </td>
                            <td>
                                <?php 
                                if($resultServiceDetail['PatientIpdServiceDetail']['is_active']==2) {
                                    echo $this->Form->text('total_price', array('value' => number_format($resultServiceDetail['PatientIpdServiceDetail']['total_price'], 2), 'id' => 'PatientIpdTotalPrice'.$index, 'name' => 'data[Patient][total_price][]', 'class' => 'total_price', 'style' => 'width:120px;', 'disabled' => true));
                                }else{
                                    echo $this->Form->text('total_price', array('value' => number_format($resultServiceDetail['PatientIpdServiceDetail']['total_price'], 2), 'id' => 'PatientIpdTotalPrice'.$index, 'name' => 'data[Patient][total_price][]', 'class' => 'total_price', 'style' => 'width:120px;', 'readonly' => true));
                                }
                                ?>                                 
                            </td>
                            <td style="padding: 5px 5px 5px 5px !important;;">
                                 <?php if($resultServiceDetail['PatientIpdServiceDetail']['is_active']==1) { ?>
                                    <img alt="" src="<?php echo $this->webroot; ?>img/button/plus.png" class="btnAddType" style="cursor: pointer;<?php if($index!=count($dataServiceDetail) || mysql_num_rows($queryPayment)){ echo 'display: none;';}?>" />
                                    <img style="cursor: pointer; display: inline;" class="btnRemoveType" src="<?php echo $this->webroot; ?>img/button/cross.png" alt="">
                                <?php }?>
                            </td>
                        </tr>
                    <?php
                    $index++;                
                    }?>                        
                    <?php if(mysql_num_rows($queryPayment)){?>
                        <tr class="PatientIpdAddMedicalSurgeryTr">
                            <td class="first serviceId"><?php echo $index;?></td>            
                            <td>
                                <div class="inputContainer">
                                    <?php
                                    $query = mysql_query("SELECT sections.id, sections.name, section_companies.company_id  FROM `sections` INNER JOIN section_companies ON sections.id = section_companies.section_id 
                                                                WHERE sections.id IN (SELECT section_id FROM section_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = '" . $user['User']['id'] . "'))")
                                    ?>
                                    <select id="ServiceSectionId<?php echo $index;?>" rel="<?php echo $index;?>" class="classSection validate[required]" name="data[Patient][section_id][]" style="width:160px;">
                                        <option value=""><?php echo SELECT_OPTION; ?></option>
                                        <?php
                                        while ($row = mysql_fetch_array($query)) {
                                            if($row['company_id']==$patientIpd['PatientIpd']['company_id']){
                                                echo '<option class="'.$row['company_id'].'" value="'.$row['id'].'">'.$row['name'].'</option>';
                                            }else{
                                                echo '<option style="display:none;" class="'.$row['company_id'].'" value="'.$row['id'].'">'.$row['name'].'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>                    
                            </td>
                            <td>
                                <?php echo $this->Form->input('service_id', array('id' => 'PatientIpdServiceId'.$index, 'name' => 'data[Patient][service_id][]', 'empty' => SELECT_OPTION, 'label' => false, 'class' => 'patientPatientIpdService validate[required]', 'style' => 'width:160px;', 'rel' => $index)); ?>
                            </td>
                            <td>
                                <select name="data[Patient][doctor_id][]" id="PatientIpdDoctorId<?php echo $index;?>" style="width:130px;" name="data[Patient][doctor_id][]" class="classDoctor validate[required]" rel="<?php echo $index;?>">
                                    <option value=""><?php echo SELECT_OPTION;?></option>
                                    <?php 
                                    foreach ($doctors as $doctor) {
                                        echo '<option class="1" value="'.$doctor['DoctorConsultation']['id'].'">'.$doctor['DoctorConsultation']['name'].'</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <?php echo $this->Form->text('qty', array('id' => 'PatientIpdQty'.$index, 'name' => 'data[Patient][qty][]', 'class' => 'qty integer validate[required]', 'style' => 'width:50px;text-align:center;', 'rel' => $index)); ?> 
                            </td>
                            <td>
                                <?php echo $this->Form->text('unit_price', array('id' => 'PatientIpdUnitPrice'.$index, 'name' => 'data[Patient][unit_price][]', 'class' => 'unit_price float validate[required]', 'readonly' => true, 'style' => 'width:100px;', 'rel' => $index)); ?> 
                            </td>
                            <td>
                                <input type="text" id="dateCreated<?php echo $index;?>" class="btnDateCreated" name="data[Patient][date_created][]" style="width: 100px;" rel="<?php echo $index;?>"/>
                            </td>
                            <td>
                                <?php echo $this->Form->text('total_price', array('id' => 'PatientIpdTotalPrice'.$index, 'name' => 'data[Patient][total_price][]', 'class' => 'total_price', 'style' => 'width:120px;', 'readonly' => true)); ?>
                            </td>
                            <td style="padding: 5px 5px 5px 5px !important;;">
                                <img alt="" src="<?php echo $this->webroot; ?>img/button/plus.png" class="btnAddType" style="cursor: pointer;" />
                                <img alt="" src="<?php echo $this->webroot; ?>img/button/cross.png" class="btnRemoveType" style="cursor: pointer;" />
                            </td>
                        </tr>
                    <?php }?>
            <?php }?>
        </tbody>
        <tr>
            <td class="first" style="text-align: right;" colspan="7"><label for="PatientTotalAmount"><b>Sub Total ($)</b>:</label></td>
            <td>
                <input type="text" id="PatientTotalAmount" value="0.00" class="validate[required]" readonly="readonly" style="width:120px; height: 30px;font-weight: bold;" name="data[Patient][total_amount]">
            </td>
        </tr>                        
    </table>
    <br/>
    <div class="buttons">
        <button type="submit" class="positive savePatientMedicalSurgery" >
            <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
            <span class="txtSavePatient"><?php echo ACTION_SAVE; ?></span>
        </button>
    </div>
</div>
<?php echo $this->Form->end(); ?>