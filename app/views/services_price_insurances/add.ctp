<?php 
//echo $this->element('prevent_multiple_submit'); 
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
        width: 304px !important;
        height: 35px !important;
    }
    .chzn-drop{
        width: 304px !important;
    }
</style>
<script type="text/javascript">
    $(document).ready(function() {
        $(".float").autoNumeric();
        // Prevent Key Enter
        preventKeyEnter();
        $("#ServicesPriceInsuranceAddForm").validationEngine();
        $("#ServicesPriceInsuranceAddForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                var comIns = $("#ServicesPriceInsuranceCompanyInsuranceId").val();
                if(comIns==null || comIns==''){
                    alert('Please Select Company Insurance Name');
                    return false;
                }
                $(".txtSaveService").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $(".btnBackServicePriceInsurance").click();
                // alert message
                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>' + result + '</p>');
                $("#dialog").dialog({
                    title: '<?php echo DIALOG_INFORMATION; ?>',
                    resizable: false,
                    modal: true,
                    width: 'auto',
                    height: 'auto',
                    open: function(event, ui) {
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
        
        // chosen init
        $(".chzn-select").chosen({width: 250});
        $("#ServicesPriceInsuranceAddForm").validationEngine();        
        var countPatientGroup = $("#countPatientGroup").val();        
        // add more patient's group for services
        $(".btnAddType").click(function() {                          
            if(Number($("#example").find(".serviceId:last").text())<countPatientGroup){
                $("#example").find(".ServicePriceInsuranceTr:last").clone(true).appendTo("#example");
                $("#example").find(".ServicePriceInsuranceTr:last").find("td .btnRemoveType").show();
                $(this).siblings(".btnRemoveType").show();
                $(this).hide(); 
                comboRefeshType();
                staffRefreshType();
            }
           
        });
        $(".btnRemoveType").click(function() {
            $(this).closest(".ServicePriceInsuranceTr").remove();
            $("#example").find(".ServicePriceInsuranceTr:last").find("td .btnAddType").show();            
            if ($('#example .ServicePriceInsuranceTr').length == 1) {
                $("#example").find(".ServicePriceInsuranceTr:last").find("td .btnRemoveType").hide();
            }
            staffRefreshType()
        });
        
        // for sort section in company        
        $(".classSection").change(function(){
            if($(this).val()!=''){
                $("#ServicesPriceInsuranceServiceId").closest("tr").find("td .classService").val('');
                $("#ServicesPriceInsuranceServiceId").closest("tr").find("td .classService option[class!='']").hide();
                $("#ServicesPriceInsuranceServiceId").closest("tr").find("td .classService option[class='"  + $(this).val() + "']").show();
            }else{                
                $("#ServicesPriceInsuranceServiceId").closest("tr").find("td .classService option[class!='']").show();
            }       
            comboRefesh();
        });

        $(".classService").change(function(){
            $("#ServicesPriceInsuranceSectionId").closest("tr").find("td .classService").val($(this).find("option:selected").attr("class"));
            comboRefesh();          
        });
        
        $(".btnBackServicePriceInsurance").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableServicePriceInsurance.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });
    
    function comboRefesh(){
        selected=new Array();
        $(".classService").each(function(){
            if($(this).val()!=''){
                selected.push($(this).val());
            }
        });           
    }
    
    function staffRefreshType() {
        var i = 1;
        $(".serviceId").each(function() {
            $("#example").find(".serviceId:last").text(i++);
        });
    }
    function comboRefeshType() {                 
        $(".unit_price").each(function() {
            $("#example").find(".unit_price:last").val("");
        });       
        $(".servicePatientGroup").each(function() {
            $("#example").find(".servicePatientGroup:last").val("");
        }); 
    }
</script>
<div style="padding: 5px;border: 1px dashed #3C69AD;">
    <div class="buttons">
        <a href="" class="positive btnBackServicePriceInsurance">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('ServicesPriceInsurance'); ?>
<fieldset>
    <legend><?php __(MENU_INSURANCE_SERVICE_PRICE_MANAGEMENT_INFO); ?></legend>
    <table style="width: 50%; border-spacing:0 5px;" cellspacing="0">
        <tr>
            <td><label for="ServicesPriceInsuranceSectionId"><?php echo TABLE_SECTION_NAME; ?> <span class="red">*</span> :</label></td>
            <td>
                <?php 
                $query = mysql_query("SELECT sections.id, sections.name, section_companies.company_id  FROM `sections` INNER JOIN section_companies ON sections.id = section_companies.section_id 
                                        WHERE sections.id IN (SELECT section_id FROM section_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = '".$user['User']['id']."'))");
                ?>
                <select id="ServicesPriceInsuranceSectionId" class="classSection validate[required]" name="data[ServicesPriceInsurance][section_id]" style="width: 250px;">
                    <option value=""><?php echo SELECT_OPTION;?></option>
                    <?php while ($row = mysql_fetch_array($query)) {
                        echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';

                    }?>
                </select>
            </td>
        </tr>
        <tr>
            <td><label for="ServicesPriceInsuranceServiceId"><?php echo TABLE_SERVICE_NAME; ?> <span class="red">*</span> :</label></td>
            <td>
                <select id="ServicesPriceInsuranceServiceId" class="classService validate[required]" name="data[ServicesPriceInsurance][service_id]" style="width: 250px;">
                    <option value=""><?php echo SELECT_OPTION;?></option>
                    <?php foreach($services As $service) {
                        echo '<option class="'.$service['Service']['section_id'].'" value="'.$service['Service']['id'].'">'.$service['Service']['name'].'</option>';
                    }?>
                </select>
            </td>
        </tr>
        <tr>
            <td><label for="ServicesPriceInsuranceCompanyInsuranceId"><?php echo TABLE_COMPANY_INSURANCE_NAME; ?> <span class="red">*</span> :</label></td>
            <td>                
                <?php echo $this->Form->input('company_insurance_id', array('label' => false,'class' => 'chzn-select validate[required]', 'multiple' => true)); ?>
            </td>
        </tr>
    </table>    
</fieldset>
<br/>
<fieldset>
    <legend><?php __(MENU_SERVICES_PRICE); ?></legend>    
    <table id="example" class="table" cellspacing="0">
        <input type="hidden" id="countPatientGroup" value="<?php echo count($patientGroups);?>"/>
        <tr>
            <th style="width: 5%;" class="first"><?php echo TABLE_NO; ?></th>
            <th><?php echo TABLE_PATIENT_GROUP; ?></th>
            <th><?php echo GENERAL_UNIT_PRICE; ?></th>
            <th style="width: 10% !important; ">&nbsp;</th>
        </tr>
        <tr class="ServicePriceInsuranceTr">
            <td class="first serviceId">1</td>            
            <td>
                <?php echo $this->Form->input('patient_group_id', array('name' => 'data[ServicesPriceInsurance][patient_group_id][]', 'empty' => SELECT_OPTION, 'label' => false,'class' => 'servicePatientGroup validate[required]', 'style' => 'width:220px;')); ?>
            </td>
            <td>
                <?php echo $this->Form->text('unit_price', array('name' => 'data[ServicesPriceInsurance][unit_price][]', 'class' => 'unit_price float validate[required]', 'style' => 'width:200px;')); ?> 
            </td>
            <td>
                <img alt="" src="<?php echo $this->webroot; ?>img/button/plus.png" class="btnAddType" style="cursor: pointer;" />
                <img alt="" src="<?php echo $this->webroot; ?>img/button/cross.png" class="btnRemoveType" style="cursor: pointer;display: none;" />
            </td>
        </tr>
    </table>
</fieldset>
<br/>
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <span class="txtSaveService"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<div style="clear: both;"></div>
<?php echo $this->Form->end(); ?>