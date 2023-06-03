<?php echo $this->element('prevent_multiple_submit'); ?>
<script type="text/javascript">
    var vGroupRequestVendor = null;
    $(document).ready(function(){
        $("#PatientGroupParentId").chosen({width: 410});
        // Prevent Key Enter
        preventKeyEnter();
        $("#PatientCompanyGroupEditForm").validationEngine();
        $("#PatientCompanyGroupEditForm").ajaxForm({
            beforeSerialize: function($form, options) {
//                listbox_selectall('vendor_id', true);
            },
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveCompany").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $(".btnBack").click();
                // alert message
                if(result != '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>' && result != '<?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?>' && result != '<?php echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM; ?>'){
                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                }else {
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
        $(".btnBack").click(function(event){
            event.preventDefault();
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
            oCache.iCacheLower = -1;
            oTableVendorGroup.fnDraw(false);
        });
        
    });
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="" class="positive btnBack">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />

<?php
echo $this->Form->create('PatientCompanyGroup');

?>
<input type="hidden" value="<?php echo $this->data['PatientCompanyGroup']['id']; ?>" name="data[PatientCompanyGroup][id]" id="PatientCompanyGroupId" />
<?php
print_r( $this->data['PatientCompanyGroup']['parent_id']);
//?>
<fieldset>
    <legend><?php __(MENU_VENDOR_GROUP_MANAGEMENT); ?></legend>
    <table>
        <tr>
            <td style="width: 25%;"><label for="PatientGroupParentId"><?php echo TABLE_SUB_OF_GROUP; ?>:</label></td>
            <td>
                <select id="PatientGroupParentId" name="data[PatientCompanyGroup][parent_id]">
                    <option value=""><?php echo INPUT_SELECT; ?></option>
                    <?php
                    $sqlParent = mysql_query("select id , name from patient_company_groups where is_active = 1");
                    while($rowParent = mysql_fetch_array($sqlParent)){
                        ?>
                        <option com="<?php echo $rowParent['id']; ?>" value="<?php echo $rowParent['id']; ?>" <?php if($rowParent['id'] == $this->data['PatientCompanyGroup']['parent_id']){ ?>selected="selected" <?php }?>><?php echo $rowParent['name']; ?></option>
                        <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <th style="vertical-align: top;"><label for="PatientCompanyGroupsName"><?php echo TABLE_NAME; ?> <span class="red">*</span> :</label></th>
            <td style="vertical-align: top;">
                <div class="inputContainer" style="width: 100%;">
                    <input class="validate[required]" name="data[PatientCompanyGroup][name]" id="PatientPatientCode" type="text" value="<?php echo $this->data['PatientCompanyGroup']['name'];?>"/>
                </div>
            </td>
        </tr>
    </table>
</fieldset>
<br />
<div class="buttons">
    <button type="submit" class="positive btnSaveCompanyGroup">
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <span class="txtSaveCompany"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<div style="clear: both;"></div>
<?php echo $this->Form->end(); ?>