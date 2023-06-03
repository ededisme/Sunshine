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
        $("#LaboItemGroupCloneServicePriceForm").validationEngine();
        $("#LaboItemGroupCloneServicePriceForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
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
        $(".chzn-select").chosen();
        $("#LaboItemGroupCloneServicePriceForm").validationEngine();        
        var countPatientGroup = $("#countPatientGroup").val();        
        // add more patient's group for services
        $(".btnAddType").click(function() {                          
            if(Number($("#example").find(".serviceId:last").text())<countPatientGroup){
                $("#example").find(".ServicePriceInsuranceTr:last").clone(true).appendTo("#example");
                $("#example").find(".ServicePriceInsuranceTr:last").find("td .btnRemoveType").show();
                $(this).siblings(".btnRemoveType").show();
                $(this).hide(); 
                comboRefeshType();
                staffRefreshType()
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
                $("#LaboItemGroupServiceId").closest("tr").find("td .classService").val('');
                $("#LaboItemGroupServiceId").closest("tr").find("td .classService option[class!='']").hide();
                $("#LaboItemGroupServiceId").closest("tr").find("td .classService option[class='"  + $(this).val() + "']").show();
            }else{                
                $("#LaboItemGroupServiceId").closest("tr").find("td .classService option[class!='']").show();
            }       
            comboRefesh();
        });

        $(".classService").change(function(){
            $("#LaboItemGroupSectionId").closest("tr").find("td .classService").val($(this).find("option:selected").attr("class"));
            comboRefesh();          
        });
        
        $(".btnBackServicePriceInsurance").click(function(event) {
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableLaboItemGroupInsurance.fnDraw(false);
            var rightPanel = $(this).parent().parent().parent();
            var leftPanel = rightPanel.parent().find(".leftPanel");
            rightPanel.hide();
            rightPanel.html("");
            leftPanel.show("slide", {direction: "left"}, 500);
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
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="" class="positive btnBackServicePriceInsurance">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('LaboItemGroup'); ?>
<fieldset>
    <legend><?php __(MENU_INSURANCE_SERVICE_PRICE_MANAGEMENT_INFO); ?></legend>
    <table style="width: 100%;" cellspacing="0">              
        <tr>
            <td>From :</td>
            <td><label for="LaboItemGroupCompanyInsuranceId"><?php echo TABLE_COMPANY_INSURANCE_NAME; ?> <span class="red">*</span> </label></td>
            <td>                
                <?php echo $this->Form->input('company_insurance_id', array('label' => false, 'empty' => SELECT_OPTION, 'class' => 'validate[required]')); ?>
            </td>
            <td>To :</td>
            <td><label for="LaboItemGroupCompanyInsuranceIdTo"><?php echo TABLE_COMPANY_INSURANCE_NAME; ?> <span class="red">*</span> </label></td>
            <td>                
                <select id="LaboItemGroupCompanyInsuranceIdTo" class="validate[required]" name="data[LaboItemGroup][company_insurance_id_to]">
                    <option value=""><?php echo SELECT_OPTION; ?></option>
                    <?php 
                    $query = mysql_query("SELECT id, name FROM company_insurances WHERE is_active = 1");
                    while ($row = mysql_fetch_array($query)) {
                        echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';                    
                    }
                    ?>
                                    
                </select>
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
            <th style="width: 10% !important; ">&nbsp;</th>
        </tr>
        <tr class="ServicePriceInsuranceTr">
            <td class="first serviceId">1</td>            
            <td>
                <?php echo $this->Form->input('patient_group_id', array('name' => 'data[LaboItemGroup][patient_group_id][]', 'empty' => SELECT_OPTION, 'label' => false,'class' => 'servicePatientGroup validate[required]', 'style' => 'width:220px;')); ?>
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
        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
        <span class="txtSaveService"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<div style="clear: both;"></div>
<?php echo $this->Form->end(); ?>