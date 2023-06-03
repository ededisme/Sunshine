<?php 
// Prevent Button Submit
echo $this->element('prevent_multiple_submit'); ?>
<script type="text/javascript">
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        $("#CommuneEditForm").validationEngine('attach', {
            isOverflown: true,
            overflownDIV: ".ui-tabs-panel"
        });
        $("#CommuneEditForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveCommune").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $(".btnBackCommune").click();
                // alert message
                if(result != '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>' && result != '<?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?>'){
                    createSysAct('Commune', 'Edit', 2, result);
                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                }else {
                    createSysAct('Commune', 'Edit', 1, '');
                    // alert message
                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result+'</p>');
                }
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
        var CommuneProvinceId = $("#CommuneDistrictId").find("option:selected").attr("class");
        $("#CommuneProvinceId").val(CommuneProvinceId);
        $("#CommuneDistrictId").find("option").each(function(){
            if($(this).attr("class")== CommuneProvinceId){
                $(this).show();
            }else{
                $(this).hide();
            }
        });
        $("#CommuneDistrictId").live("change", function(){
            var province_id = $(this).find("option:selected").attr("class");
            $("#CommuneProvinceId").val(province_id);
        });
        $("#CommuneProvinceId").live("change", function(){
            var province_id = $(this).val();
            $("#CommuneDistrictId").find("option").each(function(){
                if($(this).attr("class")== province_id){
                    $(this).show();
                }else{
                    $(this).hide();
                }
            });
            $("#CommuneDistrictId").val("");
        });
        $(".btnBackCommune").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableCommune.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="" class="positive btnBackCommune">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php 
echo $this->Form->create('Commune');
echo $this->Form->input('id');
echo $this->Form->hidden('sys_code');
?>
<fieldset>
    <legend><?php __(COMMUNE_INFO); ?></legend>
    <table>
        <tr>
            <td><label for="CommuneProvinceId"><?php echo TABLE_PROVINCE; ?> <span class="red">*</span> :</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->input('province_id', array('empty' => INPUT_SELECT, 'label' => false, 'class'=>'validate[required]')); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><label for="CommuneDistrictId"><?php echo TABLE_DISTRICT; ?> <span class="red">*</span> :</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->input('district_id', array('empty' => INPUT_SELECT, 'label' => false, 'class'=>'validate[required]')); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><label for="CommuneName"><?php echo TABLE_COMMUNE; ?> <span class="red">*</span> :</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->text('name', array('class'=>'validate[required]')); ?>
                </div>
            </td>
        </tr>
    </table>
</fieldset>
<br />
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
        <span class="txtSaveCommune"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<div style="clear: both;"></div>
<?php echo $this->Form->end(); ?>