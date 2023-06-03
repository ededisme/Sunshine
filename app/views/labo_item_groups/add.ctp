<?php 
echo $this->element('prevent_multiple_submit'); 
$absolute_url = FULL_BASE_URL . Router::url("/", false);
?>
<?php $tblName = "tbl" . rand(); ?>
<style type="text/css">
    #LaboItemGroupParentId optgroup option {
        padding-left: 30px;
    }
</style>
<script type="text/javascript">
    $(document).ready(function() {
        // Prevent Key Enter
        preventKeyEnter();
        $("#LaboItemGroupAddForm").validationEngine();
        $("#LaboItemGroupAddForm").ajaxForm({
            beforeSerialize: function($form, options) {
                listbox_selectall('d', true);
            },
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveLaboItemGroup").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                var rightPanel=$("#LaboItemGroupAddForm").parent();
                var leftPanel=rightPanel.parent().find(".leftPanel");
                rightPanel.hide();rightPanel.html("");
                leftPanel.show("slide", { direction: "left" }, 500);
                oCache.iCacheLower = -1;
                oTableLaboItemGroup.fnDraw(false);
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
            oTableLaboItemGroup.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
        
        
        $("#LaboItemGroupPrice").autoNumeric();
        var countPatientGroup = $("#countPatientGroup").val();  
        $(".btnAddType").click(function() {            
            if(Number($("#<?php echo $tblName;?>").find(".serviceId:last").text())<countPatientGroup){
                $("#<?php echo $tblName;?>").find(".LaboItemGroupTr:last").clone(true).appendTo("#<?php echo $tblName;?>");
                $("#<?php echo $tblName;?>").find(".LaboItemGroupTr:last").find("td .btnRemoveType").show();
                $(this).siblings(".btnRemoveType").show();
                $(this).hide(); 
                comboRefeshType();
                staffRefreshType()
            }
            
           
        });
        $(".btnRemoveType").click(function() {
            $(this).closest(".LaboItemGroupTr").remove();
            $("#<?php echo $tblName;?>").find(".LaboItemGroupTr:last").find("td .btnAddType").show();            
            if ($('#<?php echo $tblName;?> .LaboItemGroupTr').length == 1) {
                $("#<?php echo $tblName;?>").find(".LaboItemGroupTr:last").find("td .btnRemoveType").hide();
            }
            staffRefreshType()
        });
    });
    function staffRefreshType() {
        var i = 1;
        $(".serviceId").each(function() {
            $("#<?php echo $tblName;?>").find(".serviceId:last").text(i++);
        });
    }
    function comboRefeshType() {                 
        $(".unit_price").each(function() {
            $("#<?php echo $tblName;?>").find(".unit_price:last").val("");
        });       
        $(".hospital_price").each(function() {
            $("#<?php echo $tblName;?>").find(".hospital_price:last").val("");
        });
        $(".laboItemGroupPatientGroup").each(function() {            
            $("#<?php echo $tblName;?>").find(".servicePatientGroup:last").val("");
        }); 
    }        
   
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="" class="positive btnBackLaboItemGroup">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('LaboItemGroup'); ?>
<input type="hidden" id="countPatientGroup" value="<?php echo count($patientGroups);?>"/>
<fieldset>
    <legend><?php __(MENU_SUB_GROUP_INFO); ?></legend>
    <table style="width: 60%;">
        <tr>
            <td><label for="LaboItemGroupCompanyId"><?php echo TABLE_COMPANY; ?> <span class="red">*</span> :</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->input('company_id', array('empty' => SELECT_OPTION, 'class' => 'validate[required]', 'label' => false)); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><label for="LaboItemGroupChartAccountId"><?php echo TABLE_ACCOUNT; ?> <span class="red">*</span> :</label></td>
            <td>
                <div class="inputContainer">
                    <select id="LaboItemGroupChartAccountId" name="data[LaboItemGroup][chart_account_id]" class="validate[required]">
                        <option value=""><?php echo SELECT_OPTION; ?></option>
                        <?php
                        $query[0]=mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE ISNULL(parent_id) AND is_active=1 ORDER BY account_codes");
                        while($data[0]=mysql_fetch_array($query[0])){
                            $queryIsNotLastChild=mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=".$data[0]['id']);
                        ?>
                        <option value="<?php echo $data[0]['id']; ?>" chart_account_type_name="<?php echo $data[0]['chart_account_type_name']; ?>" company_id="<?php echo $data[0]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild)?'disabled="disabled"':''; ?> <?php echo $data[0]['id']==$serviceAccountId?'selected="selected"':''; ?>><?php echo $data[0]['name']; ?></option>
                            <?php
                            $query[1]=mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=".$data[0]['id']." AND is_active=1 ORDER BY account_codes");
                            while($data[1]=mysql_fetch_array($query[1])){
                                $queryIsNotLastChild=mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=".$data[1]['id']);
                            ?>
                            <option value="<?php echo $data[1]['id']; ?>" chart_account_type_name="<?php echo $data[1]['chart_account_type_name']; ?>" company_id="<?php echo $data[1]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild)?'disabled="disabled"':''; ?> <?php echo $data[1]['id']==$serviceAccountId?'selected="selected"':''; ?> style="padding-left: 25px;"><?php echo $data[1]['name']; ?></option>
                                <?php
                                $query[2]=mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=".$data[1]['id']." AND is_active=1 ORDER BY account_codes");
                                while($data[2]=mysql_fetch_array($query[2])){
                                    $queryIsNotLastChild=mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=".$data[2]['id']);
                                ?>
                                <option value="<?php echo $data[2]['id']; ?>" chart_account_type_name="<?php echo $data[2]['chart_account_type_name']; ?>" company_id="<?php echo $data[2]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild)?'disabled="disabled"':''; ?> <?php echo $data[2]['id']==$serviceAccountId?'selected="selected"':''; ?> style="padding-left: 50px;"><?php echo $data[2]['name']; ?></option>
                                    <?php
                                    $query[3]=mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=".$data[2]['id']." AND is_active=1 ORDER BY account_codes");
                                    while($data[3]=mysql_fetch_array($query[3])){
                                        $queryIsNotLastChild=mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=".$data[3]['id']);
                                    ?>
                                    <option value="<?php echo $data[3]['id']; ?>" chart_account_type_name="<?php echo $data[3]['chart_account_type_name']; ?>" company_id="<?php echo $data[3]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild)?'disabled="disabled"':''; ?> <?php echo $data[3]['id']==$serviceAccountId?'selected="selected"':''; ?> style="padding-left: 75px;"><?php echo $data[3]['name']; ?></option>
                                        <?php
                                        $query[4]=mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=".$data[3]['id']." AND is_active=1 ORDER BY account_codes");
                                        while($data[4]=mysql_fetch_array($query[4])){
                                            $queryIsNotLastChild=mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=".$data[4]['id']);
                                        ?>
                                        <option value="<?php echo $data[4]['id']; ?>" chart_account_type_name="<?php echo $data[4]['chart_account_type_name']; ?>" company_id="<?php echo $data[4]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild)?'disabled="disabled"':''; ?> <?php echo $data[4]['id']==$serviceAccountId?'selected="selected"':''; ?> style="padding-left: 100px;"><?php echo $data[4]['name']; ?></option>
                                            <?php
                                            $query[5]=mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=".$data[4]['id']." AND is_active=1 ORDER BY account_codes");
                                            while($data[5]=mysql_fetch_array($query[5])){
                                                $queryIsNotLastChild=mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=".$data[5]['id']);
                                            ?>
                                            <option value="<?php echo $data[5]['id']; ?>" chart_account_type_name="<?php echo $data[5]['chart_account_type_name']; ?>" company_id="<?php echo $data[5]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild)?'disabled="disabled"':''; ?> <?php echo $data[5]['id']==$serviceAccountId?'selected="selected"':''; ?> style="padding-left: 125px;"><?php echo $data[5]['name']; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td><label for="LaboSubTitleGroupId"><?php echo LABO_SUB_TITLE_GROUP; ?> :</label></td>
            <td>
                <div class="inputContainer">
                    <?php echo $this->Form->input('labo_sub_title_group_id', array('empty' => SELECT_OPTION, 'label' => false)); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><label for="LaboItemGroupCode"><?php echo TABLE_CODE; ?> :</label></td>
            <td><?php echo $this->Form->text('code'); ?></td>
        </tr>
        <tr>
            <td><label for="LaboItemGroupName"><?php echo MENU_LABO_SUB_GROUP_NAME; ?> <span class="red">*</span> :</label></td>
            <td><?php echo $this->Form->text('name', array('class' => 'validate[required]')); ?></td>
        </tr>        
    </table>
</fieldset>
<br/>
<fieldset>
    <legend><?php __(GENERAL_MEMBER); ?></legend>
    <table>
        <tr>
            <th>Available Labo Item:</th>
            <th></th>
            <th>Member of Labo Item Group:</th>
        </tr>
        <tr>
            <td style="vertical-align: top;">
                <select id="s" multiple="multiple" style="width: 300px; height: 200px;">
                    <?php
                    $querySource = mysql_query("SELECT labo_items.id,CONCAT(labo_items.name,' - ',labo_item_categories.name) AS name FROM labo_items LEFT JOIN labo_item_categories ON  labo_items.category = labo_item_categories.id  WHERE labo_items.is_active=1 AND labo_item_categories.is_active=1 ORDER BY name ASC");
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
                <select id="d" name="data[LaboItemGroup][labo_item_id][]" multiple="multiple" style="width: 300px; height: 200px;">

                </select>
            </td>
        </tr>
    </table>
</fieldset>
<br/>
<fieldset>
    <legend><?php __(MENU_LABO_SUB_GROUP_PRICE); ?></legend>    
    <table id="<?php echo $tblName;?>" class="table" cellspacing="0">
        <tr>
            <th style="width: 5%;" class="first"><?php echo TABLE_NO; ?></th>
            <th><?php echo TABLE_PATIENT_GROUP; ?></th>
            <th><?php echo TABLE_PATIENT_PRICE; ?></th>
            <th><?php echo TABLE_HOSPITAL_PRICE; ?></th>
            <th style="width: 10% !important; ">&nbsp;</th>
        </tr>
        <tr class="LaboItemGroupTr">
            <td class="first serviceId">1</td>            
            <td>
                <?php echo $this->Form->input('patient_group_id', array('name' => 'data[LaboItemGroup][patient_group_id][]', 'empty' => SELECT_OPTION, 'label' => false,'class' => 'laboItemGroupPatientGroup validate[required]')); ?>
            </td>
            <td>
                <?php echo $this->Form->text('unit_price', array('name' => 'data[LaboItemGroup][unit_price][]', 'class' => 'unit_price float validate[required]')); ?> 
            </td>
            <td>
                <?php echo $this->Form->text('hospital_price', array('name' => 'data[LaboItemGroup][hospital_price][]', 'class' => 'hospital_price float validate[required]')); ?> 
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
        <span class="txtSaveLaboItemGroup"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<?php echo $this->Form->end(); ?>
