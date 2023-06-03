<style type="text/css">select{width: 250px;}</style>
<script type="text/javascript">
    $(document).ready(function(){        
        $(".chzn-select").chosen({ width: 335});
        $("#ServiceServiceId").change(function(){
            $("#ServiceSectionId").val($(this).find("option:selected").attr("class"));
			$("#ServiceUnitPrice").val($(this).find("option:selected").attr("price"));
                        $("#ServiceCode").val($(this).find("option:selected").attr("code"));
                        $("#ServiceUomId").val($(this).find("option:selected").attr("uom-name"));
                        $("#ServiceUomSerId").val($(this).find("option:selected").attr("uom-id"));
			var section=$("#ServiceSectionId option:selected" ).text();
			var service=$("#ServiceServiceId option:selected" ).text();
			if(section=='<?php echo INPUT_SELECT;?>'){$("#lblSection").show();}else{$("#lblSection").hide();}
			if(service=='<?php echo INPUT_SELECT;?>'){$("#lblService").show();}else{$("#lblService").hide();}
			if($("#ServiceUnitPrice" ).val()==''){$("#lblUnitPrice").show();}else{$("#lblUnitPrice").hide();}
                        if($("#ServiceCode" ).val()==''){$("#lblCode").show();}else{$("#lblCode").hide();}
                        if($("#ServiceUomId" ).val()==''){$("#lblUom").show();}else{$("#lblUom").hide();}
        });
        $("#ServiceSectionId").change(function(){
            $("#ServiceServiceId").find("option").hide();
            $("#ServiceServiceId").find("option[value='']").show();
            $("#ServiceServiceId").find("option[value='']").attr('selected','selected');
            $("#ServiceUnitPrice").val(0);
            $("#ServiceCode").val('');
            $("#ServiceUomId").val('');
            $("#ServiceServiceId").find("option").each(function(){if($(this).attr("class") == $("#ServiceSectionId").val()){$(this).show();}else{$(this).hide();}});
			var section=$("#ServiceSectionId option:selected" ).text();
			var service=$("#ServiceServiceId option:selected" ).text();
			if(section=='<?php echo INPUT_SELECT;?>'){$("#lblSection").show();}else{$("#lblSection").hide();}
			if(service=='<?php echo INPUT_SELECT;?>'){$("#lblService").show();}else{$("#lblService").hide();}
			if($("#ServiceUnitPrice" ).val()==''){$("#lblUnitPrice").show();}else{$("#lblUnitPrice").hide();}
                        if($("#ServiceCode" ).val()==''){$("#lblCode").show();}else{$("#lblCode").hide();}
                        if($("#ServiceUomId" ).val()==''){$("#lblUom").show();}else{$("#lblUom").hide();}
        });
        var services = [];
        $("#tblSO").find("input[name='service_id[]']").each(function(){if($(this).val() != '' || $(this).val() != undefined){services.push($(this).val());}});
        $("#ServiceServiceId").find("option").each(function(){if($(this).val()!=''){if(in_array($(this).val(), services)){$(this).remove();}}});
	});
    function in_array(needle, haystack){for(var key in haystack){if(needle === haystack[key]){return true;}}return false;}
</script>
<?php echo $this->Form->create('Service', array('inputDefaults' => array('div' => false, 'label' => false))); ?>
<input type="hidden" id="ServiceUomSerId" value="0" />
<table style="width: 100%;">
    <tr>
        <td style="width: 30%;"><?php echo TABLE_SECTION; ?><span class="red">*</span>:</td>
        <td><?php echo $this->Form->input('section_id', array('empty' => INPUT_SELECT, 'class' => 'chzn-select')); ?></td>
        <td style="width: 20%; text-align: center;"><label id="lblSection" style="color: red; display: none"> (*require)</label></td>
    </tr>
    <tr>
        <td><?php echo TABLE_SERVICE; ?> <span class="red">*</span>:</td><td><?php echo $this->Form->input('service_id', array('empty' => INPUT_SELECT, 'class' => 'chzn-select')); ?></td>
        <td style="text-align: center;"><label id="lblService" style="color: red; display: none"> (*require)</label></td>
    </tr>
    <tr>
        <td><?php echo TABLE_CODE; ?> <span class="red">*</span>:</td>
        <td><?php echo $this->Form->text('code', array('style'=>'width: 96%', 'class' => 'textAlignLeft')); ?></td>
        <td style="text-align: center;"><label id="lblCode" style="color: red; display: none"> (*require)</label></td>
    </tr>
    <tr>
        <td><?php echo TABLE_UOM; ?> <span class="red">*</span>:</td>
        <td><?php echo $this->Form->text('uom_id', array('style'=>'width: 96%', 'class' => 'textAlignLeft')); ?></td>
        <td style="text-align: center;"><label id="lblUom" style="color: red; display: none"> (*require)</label></td>
    </tr>
    <tr>
        <td><?php echo SALES_ORDER_UNIT_PRICE; ?> <span class="red">*</span>:</td>
        <td><?php echo $this->Form->text('unit_price', array('style'=>'width: 96%', 'class' => 'textAlignLeft')); ?></td>
        <td style="text-align: center;"><label id="lblUnitPrice" style="color: red; display: none"> (*require)</label></td>
    </tr>
</table>
<div style="clear: both;"></div>
<?php echo $this->Form->end(); ?>