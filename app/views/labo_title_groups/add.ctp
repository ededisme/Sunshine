<?php 
echo $this->element('prevent_multiple_submit'); 
$absolute_url = FULL_BASE_URL . Router::url("/", false);
?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/plugins/localisation/jquery.localisation-min.js"></script>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/plugins/tmpl/jquery.tmpl.1.1.1.js"></script>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/plugins/blockUI/jquery.blockUI.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/ui.multiselect.css" />
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/ui.multiselect.js"></script>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/ui.multiselect-<?php echo $this->Session->read('lang'); ?>.js"></script>
<style type="text/css">
    #LaboTitleGroupParentId optgroup option {
        padding-left: 30px;
    }
</style>
<script type="text/javascript">
    $(document).ready(function() {
        $("#LaboTitleGroupPrice").autoNumeric();
        // Prevent Key Enter
        preventKeyEnter();
        $("#LaboTitleGroupAddForm").validationEngine();
        $("#LaboTitleGroupAddForm").ajaxForm({
            beforeSerialize: function($form, options) {
                listbox_selectall('d', true);
            },
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveLaboTitleGroup").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                var rightPanel=$("#LaboTitleGroupAddForm").parent();
                var leftPanel=rightPanel.parent().find(".leftPanel");
                rightPanel.hide();rightPanel.html("");
                leftPanel.show("slide", { direction: "left" }, 500);
                oCache.iCacheLower = -1;
                oTableLaboTitleGroup.fnDraw(false);
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
        $(".btnBackLaboItemGroup").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableLaboTitleGroup.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
        
    });
    
    String.prototype.trim = function() {
        return this.replace(/^\s+|\s+$/g,"");
    }
</script>
<div style="padding: 5px;border: 1px dashed #3C69AD;">
    <div class="buttons">
        <a href="" class="positive btnBackLaboItemGroup">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('LaboTitleGroup'); ?>
<fieldset>
    <legend><?php __(MENU_TITLE_GROUP_INFO); ?></legend>
    <table>
        <tr>
            <td><label for="LaboTitleGroupName"><?php echo TABLE_NAME; ?> <span class="red">*</span> :</label></td>
            <td><?php echo $this->Form->text('name', array('class' => 'validate[required]')); ?></td>
        </tr>                             
    </table>
</fieldset>
<br/>
<fieldset>
    <legend><?php __(GENERAL_MEMBER); ?></legend>
    <table>
        <tr>
            <th>Available Labo Sub Group:</th>
            <th></th>
            <th>Member of Labo Title Group:</th>
        </tr>
        <tr>
            <td style="vertical-align: top;">
                <select id="s" multiple="multiple" style="width: 300px; height: 200px;">
                    <?php
                    $querySource = mysql_query("SELECT labo_item_groups.id,CONCAT(labo_item_groups.name,' - ',companies.name) AS name FROM labo_item_groups LEFT JOIN companies ON labo_item_groups.company_id = companies.id  WHERE labo_item_groups.is_active=1 AND companies.is_active=1 ORDER BY labo_item_groups.name ASC");
                    while ($dataSource = mysql_fetch_array($querySource)) {
                    ?>
                        <option value="<?php echo $dataSource['id']; ?>"><?php echo $dataSource['name']; ?></option>
                    <?php } ?>
                </select>
            </td>
            <td style="vertical-align: middle;">
                <img alt="" src="<?php echo $this->webroot; ?>img/button/right.png" style="cursor: pointer;" onclick="listbox_moveacross('s', 'd')" />
                <br /><br />
                <img alt="" src="<?php echo $this->webroot; ?>img/button/left.png" style="cursor: pointer;" src="" style="cursor: pointer;" onclick="listbox_moveacross('d', 's')" />
            </td>
            <td style="vertical-align: top;">
                <select id="d" name="data[LaboTitleGroup][labo_item_group_id][]" multiple="multiple" style="width: 300px; height: 200px;">

                </select>
            </td>
        </tr>
    </table>
</fieldset>
<br/>
<div class="buttons">
    <button type="submit" class="positive txtSaveLaboTitleGroup">
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <span class="txtSaveLaboTitleGroup"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<?php echo $this->Form->end(); ?>