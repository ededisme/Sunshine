<?php echo $this->element('prevent_multiple_submit'); ?>
<?php echo $javascript->link('uninums.min'); ?>

<script type="text/javascript">
    $(document).ready(function() {
        // Prevent Key Enter
        $('#LaboItemCategory').chosen({width: 470});
        $('#LaboItemTitleItem').chosen({width: 470});
        $('#LaboItemParentId').chosen({width: 470});
        $('#LaboItemNormalValueType').chosen({width: 470});
        $('#LaboItemItemLaboUnit').chosen({width: 470});
        preventKeyEnter();
        $("#LaboItemAddForm").validationEngine();
        $("#LaboItemEditForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveRoom").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $(".btnBackLaboItem").click();
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
        $("#LaboItemCategory").change(function(){
            $("#LaboItemParentId").children("optgroup").hide();
            $("#LaboItemParentId").children("optgroup[label="+$('option:selected', this).text()+"]").show();
            $("#LaboItemParentId").val('');
        });
        $("#LaboItemParentId").change(function(){
            $("#LaboItemCategory").val($("#LaboItemCategory option:contains('" + $(this).find("option:selected").parent().attr("label") + "')").val());            
        });
        
        $("#LaboItemNormalValueType").change(function(){
            if($("#LaboItemNormalValueType").val()=='Number'){
                $(".set_normal").show();
            }else{
                $(".set_normal").hide();
            }
        });
        
        $("#AddUnit").click(function(event){
            event.preventDefault();
            $("#dialog").dialog({
                modal:true,
                resizable:false,
                width:'50%',
                title: '<?php echo UNIT_ADD_NEW_UNIT; ?>',
                buttons:{
                    '<?php echo ACTION_OK;?>': function(){
                        $(this).dialog("close");
                    }
                }
            });
            $.ajax({
                type: "POST",
                url: "<?php echo $this->webroot; ?>labo_units/addAjax",
                success: function(msg){
                    $("#dialog").html(msg);
                }
            });
        });
        $(".btnBackLaboItem").click(function(event) {        
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableLaboItem.fnDraw(false);
            var rightPanel = $(this).parent().parent().parent();
            var leftPanel = rightPanel.parent().find(".leftPanel");
            rightPanel.hide();
            rightPanel.html("");
            leftPanel.show("slide", {direction: "left"}, 500);
        });
       
    });

    String.prototype.trim = function() {
        return this.replace(/^\s+|\s+$/g,"");
    }
</script>
<style type="text/css">
    #LaboItemDescription{
        border: 1px solid #d3d1d1;
        width: 460px;
        height: 100px;
    }
</style>
<div style="padding: 5px;border: 1px dashed #3C69AD;">
    <div class="buttons">
        <a href="" class="positive btnBackLaboItem">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('LaboItem'); ?>
<?php echo $this->Form->input('id'); ?>
<fieldset>
    <legend><?php __(MENU_LABO_ITEMS_INFO); ?></legend>
    <table>
        <tr>
            <td style="width: 200px;"><label for="LaboItemCategory"><?php echo GENERAL_CATEGORY; ?> <span class="red">*</span> :</label></td>
            <td><?php echo $this->Form->input('category', array('empty' => 'Please Select', 'label' => false, 'class' => 'validate[required]')); ?></td>
        </tr>
        <tr>
            <td><label for="LaboItemTitleGroup"><?php echo GENERAL_TITLE_ITEM; ?> :</label></td>                            
            <td><?php echo $this->Form->input('title_item', array('empty' => SELECT_OPTION, 'label' => false, 'class' => 'name')); ?></td>
        </tr>
        <tr>
            <td><label for="LaboItemParent">Parent:</label></td>
            <td><?php echo $this->Form->input('parent_id', array('empty' => 'No Parent', 'label' => false)); ?></td>
        </tr>
        <tr>
            <td><label for="LaboItemName"><?php echo TABLE_NAME; ?> <span class="red">*</span> :</label></td>
            <td><?php echo $this->Form->text('name', array('class' => 'validate[required]', 'style' => 'width: 460px;')); ?></td>
        </tr>
        <tr>
            <td>
                <label for="LaboItemDescription"><?php echo TABLE_DESCRIPTION; ?> :
                </label>
            </td>
            <td colspan="3">
                <?php echo $this->Form->textarea("description"); ?>
            </td>
        </tr>
        <tr>
            <td><label for="LaboItemNormalValueType"><?php echo TABLE_TYPE; ?> <span class="red">*</span> :</label></td>
            <td><?php echo $this->Form->input('normal_value_type', array('empty' => 'Please Select', 'label' => false, 'class' => 'validate[required]')); ?></td>
        </tr>
        <tr class="set_normal" <?php if($this->data['LaboItem']['normal_value_type']!='Number'){ echo 'style="display:none;"';}?>>
            <td>
                <label for="LaboItemItemUnit"><?php echo TABLE_ITEM_UNIT; ?> :</label>
            </td>
            <td>
                <select id="LaboItemItemLaboUnit" name="data[LaboItem][item_labo_unit]">                    
                    <option value=""><?php echo SELECT_OPTION;?></option>
                    <?php 
                    foreach ($itemLaboUnits as $itemLaboUnit) {
                    ?>
                        <option <?php if($itemLaboUnit['LaboUnit']['name']==$this->data['LaboItem']['item_unit']) { echo 'selected="true"'; } ?> value="<?php echo $itemLaboUnit['LaboUnit']['name'];?>"><?php echo $itemLaboUnit['LaboUnit']['name'];?></option>
                    <?php
                    }
                    ?>                    
                </select>                                
                <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt="" class="add_button" id="AddUnit"  align="absmiddle" />
            </td>
        </tr>
        <?php                    
        foreach ($itemAgeForLabos as $itemAgeForLabo) {                            
            // get age_id and labo_item_id for select from table  labo_item_details
            $min = "";
            $max = "";
            $itemAgeId = $itemAgeForLabo['AgeForLabo']['id'];
            $query = mysql_query('SELECT * FROM labo_item_details WHERE status > 0 AND labo_item_id = "'.$labo_item_id.'" AND age_for_labo_id = "'.$itemAgeId.'"');
            while ($row = mysql_fetch_array($query)) {
                $min = $row['min_value'];
                $max = $row['max_value'];
            }
            ?>
            <tr class="set_normal" <?php if($this->data['LaboItem']['normal_value_type']!='Number'){ echo 'style="display:none;"';}?>>                                
                <td><?php echo $itemAgeForLabo['AgeForLabo']['name']; ?>:</td>
                <td>
                    <table>
                        <tr>                                     
                            <input type="hidden" name="data[LaboItem][age_for_labo_id][]" value="<?php echo $itemAgeForLabo['AgeForLabo']['id']?>"/>
                            <td>Min Value</td>
                            <td><input type="text" name="data[LaboItem][min_value][]" value="<?php echo $min?>" style="width:241px"/></td>
                            <td>Max Value</td>
                            <td><input type="text" name="data[LaboItem][max_value][]" value="<?php echo $max?>" style="width:241px"/></td>
                        </tr>
                    </table>
                </td>
            </tr>
        <?php } ?>      
    </table>
</fieldset>         
<br/>
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <span class="txtSaveRoom"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<div style="clear: both;"></div>
<?php echo $this->Form->end(); ?>
