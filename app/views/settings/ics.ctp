<?php echo $this->element('prevent_multiple_submit'); ?>
<script type="text/javascript">
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        $("#SettingIcsForm").validationEngine('attach', {
            isOverflown: true,
            overflownDIV: ".ui-tabs-panel"
        });
        $("#SettingIcsForm").ajaxForm({
            beforeSerialize: function($form, options) {
                
            },
            beforeSubmit: function(arr, $form, options) {
                $(".txtSave").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".txtSave").html("<?php echo ACTION_SAVE; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                // alert message
                if(result != '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>' && result != '<?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?>'){
                    createSysAct('ICS', 'Add', 2, result);
                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                }else {
                    createSysAct('ICS', 'Add', 1, '');
                    // alert message
                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result+'</p>');
                }
                $("#dialog").dialog({
                    title: '<?php echo DIALOG_INFORMATION; ?>',
                    resizable: false,
                    modal: true,
                    width: 'auto',
                    height: 'auto',
                    buttons: {
                        '<?php echo ACTION_CLOSE; ?>': function() {
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
    });
</script>
<?php echo $this->Form->create('Setting');?>
<fieldset>
    <legend><?php __(MENU_ICS_MANAGEMENT); ?></legend>
    <table>
        <?php
        $queryAccountType = mysql_query("SELECT id,name FROM account_types WHERE status = 1 ORDER BY ordering");
        while($dataAccountType=mysql_fetch_array($queryAccountType)){
            $name="t".$dataAccountType['id'];
            switch($dataAccountType['id']){
                case 1:
                    // Inventory Asset Account
                    $filter="";
                    break;
                case 2:
                    // COGS Account
                    $filter="";
                    break;
                case 3:
                    // Inventory Adjustment Account
                    $filter="";
                    break;
                case 4:
                    // Scrapped Inventory Account
                    $filter="";
                    break;
                case 5:
                    // Internal Use Account
                    $filter="";
                    break;
                case 6:
                    // Cash & Bank Account for Sales Order
                    $filter="";
                    break;
                case 7:
                    // Accounts Receivable
                    $filter="";
                    break;
                case 8:
                    // Sales Income
                    $filter="";
                    break;
                case 9:
                    // Service Account
                    $filter="";
                    break;
                case 10:
                    // Miscellaneous Account
                    $filter="";
                    break;
                case 11:
                    // Sales Discount
                    $filter="";
                    break;
                case 12:
                    // Sales Change
                    $filter="";
                    break;
                case 13:
                    // Cash & Bank Account for Purchase Order
                    $filter="";
                    break;
                case 14:
                    // Accounts Payable
                    $filter="";
                    break;
                case 15:
                    // Purchase Discount
                    $filter="";
                    break;
                case 16:
                    // Inventory on Order
                    $filter="";
                    break;
                case 16:
                    // Sales Markup
                    $filter="";
                    break;
            }
            if(isset($productId)){
                $queryAccount=mysql_query("SELECT chart_account_id FROM accounts WHERE product_id='".$productId."' AND account_type_id=".$dataAccountType['id']);
            }else{
                $queryAccount=mysql_query("SELECT chart_account_id FROM account_types WHERE id=".$dataAccountType['id']);
            }
            $dataAccount=mysql_fetch_array($queryAccount);

            if(!isset($productId) || (isset($productId) && in_array($dataAccountType['id'],array(1,2,3,4,5,8)))){
        ?>
        <tr>
            <td><label for="<?php echo $name; ?>"><?php echo $dataAccountType['name']; ?> <span class="red">*</span> :</label></td>
            <td>
                <div class="inputContainer">
                    <select id="<?php echo $name; ?>" name="<?php echo $name; ?>" class="ics_coa_id<?php echo isset($productId)?'':' validate[required]'; ?>" style="width: 300px;">
                        <option value=""><?php echo isset($productId)?'Use Default':INPUT_SELECT; ?></option>
                        <?php
                        $query[0]=mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE ISNULL(parent_id) AND is_active=1 AND id IN (SELECT chart_account_id FROM chart_account_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id'].")) ".$filter." ORDER BY account_codes");
                        while($data[0]=mysql_fetch_array($query[0])){
                            $queryIsNotLastChild=mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=".$data[0]['id']);
                        ?>
                        <option value="<?php echo $data[0]['id']; ?>" chart_account_type_name="<?php echo $data[0]['chart_account_type_name']; ?>" company_id="<?php echo $data[0]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild)?'disabled="disabled"':''; ?> <?php echo $data[0]['id']==$dataAccount['chart_account_id']?'selected="selected"':''; ?>><?php echo $data[0]['name']; ?></option>
                            <?php
                            $query[1]=mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=".$data[0]['id']." AND is_active=1 AND id IN (SELECT chart_account_id FROM chart_account_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id'].")) ".$filter." ORDER BY account_codes");
                            while($data[1]=mysql_fetch_array($query[1])){
                                $queryIsNotLastChild=mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=".$data[1]['id']);
                            ?>
                            <option value="<?php echo $data[1]['id']; ?>" chart_account_type_name="<?php echo $data[1]['chart_account_type_name']; ?>" company_id="<?php echo $data[1]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild)?'disabled="disabled"':''; ?> <?php echo $data[1]['id']==$dataAccount['chart_account_id']?'selected="selected"':''; ?> style="padding-left: 25px;"><?php echo $data[1]['name']; ?></option>
                                <?php
                                $query[2]=mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=".$data[1]['id']." AND is_active=1 AND id IN (SELECT chart_account_id FROM chart_account_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id'].")) ".$filter." ORDER BY account_codes");
                                while($data[2]=mysql_fetch_array($query[2])){
                                    $queryIsNotLastChild=mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=".$data[2]['id']);
                                ?>
                                <option value="<?php echo $data[2]['id']; ?>" chart_account_type_name="<?php echo $data[2]['chart_account_type_name']; ?>" company_id="<?php echo $data[2]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild)?'disabled="disabled"':''; ?> <?php echo $data[2]['id']==$dataAccount['chart_account_id']?'selected="selected"':''; ?> style="padding-left: 50px;"><?php echo $data[2]['name']; ?></option>
                                    <?php
                                    $query[3]=mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=".$data[2]['id']." AND is_active=1 AND id IN (SELECT chart_account_id FROM chart_account_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id'].")) ".$filter." ORDER BY account_codes");
                                    while($data[3]=mysql_fetch_array($query[3])){
                                        $queryIsNotLastChild=mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=".$data[3]['id']);
                                    ?>
                                    <option value="<?php echo $data[3]['id']; ?>" chart_account_type_name="<?php echo $data[3]['chart_account_type_name']; ?>" company_id="<?php echo $data[3]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild)?'disabled="disabled"':''; ?> <?php echo $data[3]['id']==$dataAccount['chart_account_id']?'selected="selected"':''; ?> style="padding-left: 75px;"><?php echo $data[3]['name']; ?></option>
                                        <?php
                                        $query[4]=mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=".$data[3]['id']." AND is_active=1 AND id IN (SELECT chart_account_id FROM chart_account_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id'].")) ".$filter." ORDER BY account_codes");
                                        while($data[4]=mysql_fetch_array($query[4])){
                                            $queryIsNotLastChild=mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=".$data[4]['id']);
                                        ?>
                                        <option value="<?php echo $data[4]['id']; ?>" chart_account_type_name="<?php echo $data[4]['chart_account_type_name']; ?>" company_id="<?php echo $data[4]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild)?'disabled="disabled"':''; ?> <?php echo $data[4]['id']==$dataAccount['chart_account_id']?'selected="selected"':''; ?> style="padding-left: 100px;"><?php echo $data[4]['name']; ?></option>
                                            <?php
                                            $query[5]=mysql_query("SELECT id,CONCAT(account_codes,' · ',account_description) AS name,(SELECT name FROM chart_account_types WHERE id=chart_accounts.chart_account_type_id) AS chart_account_type_name,(SELECT GROUP_CONCAT(company_id) FROM chart_account_companies WHERE chart_account_id=chart_accounts.id) AS company_id FROM chart_accounts WHERE parent_id=".$data[4]['id']." AND is_active=1 AND id IN (SELECT chart_account_id FROM chart_account_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id'].")) ".$filter." ORDER BY account_codes");
                                            while($data[5]=mysql_fetch_array($query[5])){
                                                $queryIsNotLastChild=mysql_query("SELECT id FROM chart_accounts WHERE is_active=1 AND parent_id=".$data[5]['id']);
                                            ?>
                                            <option value="<?php echo $data[5]['id']; ?>" chart_account_type_name="<?php echo $data[5]['chart_account_type_name']; ?>" company_id="<?php echo $data[5]['company_id']; ?>" <?php echo mysql_num_rows($queryIsNotLastChild)?'disabled="disabled"':''; ?> <?php echo $data[5]['id']==$dataAccount['chart_account_id']?'selected="selected"':''; ?> style="padding-left: 125px;"><?php echo $data[5]['name']; ?></option>
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
        <?php 
            }
        } 
        ?>
    </table>
</fieldset>
<br />
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <span class="txtSave"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<div style="clear: both;"></div>