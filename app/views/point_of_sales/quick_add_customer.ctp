<?php 
// Authentication
$this->element('check_access');
$allowAddCgroup = checkAccess($user['User']['id'], $this->params['controller'], 'addCgroup');
$allowAddTerm   = checkAccess($user['User']['id'], $this->params['controller'], 'addTerm');
?>
<script type="text/javascript">
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        $("#CustomerSex").chosen({ width: 355 });
        <?php
        if($allowAddCgroup){
        ?>
        $("#CustomerCgroupId").chosen({ width: 355, allow_add: true, allow_add_label: '<?php echo MENU_CUSTOMER_GROUP_MANAGEMENT_ADD; ?>', allow_add_id: 'addNewCgroup' });
        $("#addNewCgroup").click(function(){
            $.ajax({
                type:   "GET",
                url:    "<?php echo $this->base . "/point_of_sales/addCgroup/"; ?>",
                beforeSend: function(){
                    $("#CustomerCgroupId").trigger("chosen:close");
                    $("#progress").show();
                },
                success: function(msg){
                    $("#progress").hide();
                    $("#dialog1").html(msg);
                    $("#dialog1").dialog({
                        title: '<?php echo MENU_CUSTOMER_GROUP_MANAGEMENT_ADD; ?>',
                        resizable: false,
                        modal: true,
                        width: '450',
                        height: '200',
                        position:'center',
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show();
                        },
                        buttons: {
                            '<?php echo ACTION_CLOSE; ?>': function() {
                                $(this).dialog("close");
                            },
                            '<?php echo ACTION_SAVE; ?>': function() {
                                var formName = "#CgroupAddCgroupForm";
                                var validateBack =$(formName).validationEngine("validate");
                                if(!validateBack){
                                    return false;
                                }else{
                                    $(this).dialog("close");
                                    $.ajax({
                                        dataType: "json",
                                        type: "POST",
                                        url: "<?php echo $this->base; ?>/point_of_sales/addCgroup",
                                        data: $("#CgroupAddCgroupForm").serialize(),
                                        beforeSend: function(){
                                            $("#progress").show();
                                        },
                                        error: function (result) {
                                            $("#progress").hide();
                                            createSysAct('Customer', 'Quick Add Vgroup', 2, result);
                                            $("#dialog1").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                                            $("#dialog1").dialog({
                                                title: '<?php echo DIALOG_INFORMATION; ?>',
                                                resizable: false,
                                                modal: true,
                                                width: 'auto',
                                                height: 'auto',
                                                position:'center',
                                                closeOnEscape: true,
                                                open: function(event, ui){
                                                    $(".ui-dialog-buttonpane").show(); $(".ui-dialog-titlebar-close").show();
                                                },
                                                buttons: {
                                                    '<?php echo ACTION_CLOSE; ?>': function() {
                                                        $(this).dialog("close");
                                                    }
                                                }
                                            });
                                        },
                                        success: function(result){
                                            $("#progress").hide();
                                            createSysAct('Customer', 'Quick Add Vgroup', 1, '');
                                            var msg = '<?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?>';
                                            if(result.error == 0){
                                                // Update Pgroup
                                                $("#CustomerCgroupId").html(result.option);
                                                $("#CustomerCgroupId").trigger("chosen:updated");
                                                msg = '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>';
                                            } else if(result.error == 2){
                                                msg = '<?php echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM; ?>';
                                            }
                                            // Message Alert
                                            $("#dialog1").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+msg+'</p>');
                                            $("#dialog1").dialog({
                                                title: '<?php echo DIALOG_INFORMATION; ?>',
                                                resizable: false,
                                                modal: true,
                                                width: 'auto',
                                                height: 'auto',
                                                position: 'center',
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
                                }  
                            }
                        }
                    });
                }
            });
        });
        <?php
        } else {
        ?>
        $("#CustomerCgroupId").chosen({ width: 355 });
        <?php
        }
        if($allowAddTerm){
        ?>
        $("#CustomerPaymentTermId").chosen({ width: 355, allow_add: true, allow_add_label: '<?php echo MENU_PAYMENT_TERM_MANAGEMENT_ADD; ?>', allow_add_id: 'addNewTerm' });
        $("#addNewTerm").click(function(){
            $.ajax({
                type: "GET",
                url:  "<?php echo $this->base . "/point_of_sales/addTerm/"; ?>",
                beforeSend: function(){
                    $("#CustomerPaymentTermId").trigger("chosen:close");
                    $("#progress").show();
                },
                success: function(msg){
                    $("#progress").hide();
                    $("#dialog1").html(msg);
                    $("#dialog1").dialog({
                        title: '<?php echo MENU_PAYMENT_TERM_MANAGEMENT_ADD; ?>',
                        resizable: false,
                        modal: true,
                        width: '450',
                        height: '200',
                        position:'center',
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show();
                        },
                        buttons: {
                            '<?php echo ACTION_CLOSE; ?>': function() {
                                $(this).dialog("close");
                            },
                            '<?php echo ACTION_SAVE; ?>': function() {
                                var formName = "#PaymentTermAddTermForm";
                                var validateBack =$(formName).validationEngine("validate");
                                if(!validateBack){
                                    return false;
                                }else{
                                    $(this).dialog("close");
                                    $.ajax({
                                        dataType: "json",
                                        type: "POST",
                                        url: "<?php echo $this->base; ?>/point_of_sales/addTerm",
                                        data: $("#PaymentTermAddTermForm").serialize(),
                                        beforeSend: function(){
                                            $("#progress").show();
                                        },
                                        error: function (result) {
                                            $("#progress").hide();
                                            createSysAct('Customer', 'Quick Add Term', 2, result);
                                            $("#dialog1").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                                            $("#dialog1").dialog({
                                                title: '<?php echo DIALOG_INFORMATION; ?>',
                                                resizable: false,
                                                modal: true,
                                                width: 'auto',
                                                height: 'auto',
                                                position:'center',
                                                closeOnEscape: true,
                                                open: function(event, ui){
                                                    $(".ui-dialog-buttonpane").show(); $(".ui-dialog-titlebar-close").show();
                                                },
                                                buttons: {
                                                    '<?php echo ACTION_CLOSE; ?>': function() {
                                                        $(this).dialog("close");
                                                    }
                                                }
                                            });
                                        },
                                        success: function(result){
                                            $("#progress").hide();
                                            createSysAct('Customer', 'Quick Add Term', 1, '');
                                            var msg = '<?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?>';
                                            if(result.error == 0){
                                                // Update Pgroup
                                                $("#CustomerPaymentTermId").html(result.option);
                                                $("#CustomerPaymentTermId").trigger("chosen:updated");
                                                msg = '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>';
                                            } else if(result.error == 2){
                                                msg = '<?php echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM; ?>';
                                            }
                                            // Message Alert
                                            $("#dialog1").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+msg+'</p>');
                                            $("#dialog1").dialog({
                                                title: '<?php echo DIALOG_INFORMATION; ?>',
                                                resizable: false,
                                                modal: true,
                                                width: 'auto',
                                                height: 'auto',
                                                position: 'center',
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
                                }  
                            }
                        }
                    });
                }
            });
        });
        <?php
        } else {
        ?>
        $("#CustomerPaymentTermId").chosen({ width: 355 });
        <?php
        }
        ?>
                
        // Customer Name
        $("#CustomerName").blur(function(){
            $("#CustomerNameKh").val($(this).val());
        });
        
        $("#CustomerNameKh").focus(function(){
            $(this).select().focus();
        });
        
        
        $("#CustomerDob" ).datepicker({
            changeMonth: false,
            changeYear: false,
            dateFormat: 'yy-mm-dd',
            yearRange: '-100:-0',
            maxDate: 0,
            beforeShow: function(){
                setTimeout(function(){
                    $("#ui-datepicker-div").css("z-index", 2000);
                }, 10);
            }
        }).unbind("blur");
        if($("#CustomerDob").val()!=''){
            var now = (new Date()).getFullYear();
            var age = now - $("#CustomerDob").val().split("-",1);
            $('#CustomerAge').val(age);
        }
        $("#CustomerDob").change(function(){
            var now = (new Date()).getFullYear();
            var age = now - $("#CustomerDob").val().split("-",1);
            $('#CustomerAge').val(age);
        });
        $("#CustomerAge").keyup(function(){
            var now = (new Date()).getFullYear();
            var age = $("#CustomerAge").val();
            var year = now - age;
            if($("#CustomerDob").val()!=''){
                var dob = year + $("#CustomerDob").val().substr(-6);
            }else{
                var dob = year + '-01-01';
            }
            $('#CustomerDob').val(dob);
        });
        
    });
</script>
<br />
<?php 
echo $this->Form->create('Customer', array('inputDefaults' => array('div' => false, 'label' => false))); 
if(count($companies) == 1){
    $companyId = key($companies);
?>
<input type="hidden" value="<?php echo $companyId; ?>" name="data[Customer][company_id]" id="CustomerCompanyId" />
<?php
}
?>
<table style="width: 99%;">
    <tr>
        <td><label for="CustomerCode"><?php echo PATIENT_CODE; ?> <span class="red">*</span> :</label></td>
        <td>
            <div class="inputContainer" style="width: 100%;">
                <?php echo $code; ?>
                <input name="data[Patient][customer_code]" type="hidden" value="<?php echo $code;?>"/>
            </div>
        </td>
    </tr>
    <tr>
        <td><label for="CustomerName"><?php echo PATIENT_NAME; ?> <span class="red">*</span> :</label></td>
        <td>
            <div class="inputContainer" style="width: 100%;">
                <?php echo $this->Form->text('name', array('class' => 'validate[required]', 'style' => 'width: 90%; text-align: left;')); ?>
            </div>
        </td>
    </tr>
    <tr>
        <td><label for="PatientSex"><?php echo TABLE_SEX; ?> <span class="red">*</span> :</label></td>
        <td>
            <div class="inputContainer" style="width: 100%;">
                <?php echo $this->Form->input('sex', array('empty' => SELECT_OPTION, 'class' => 'validate[required]', 'style' => 'width: 90%; text-align: left;')); ?>
            </div>
        </td>
    </tr>
    <tr>
        <td><label for="PatientDob"><?php echo TABLE_DOB; ?> <span class="red">*</span> :</label></td>
        <td>
            <div class="inputContainer" style="width: 100%;">
                <?php echo $this->Form->text('dob', array('style'=>'text-align: left; width: 55%;', 'class' => 'validate[required]','onkeypress'=>'return isNumberKey(event)')); ?>
                <label for="PatientAge" style="margin-left: 5px;"><?php echo TABLE_AGE; ?>:</label>
                <?php echo $this->Form->text('age',array('style'=>'text-align: left; width: 22%;', 'class' => 'number validate[required]', 'maxlength' => '3')); ?>
            </div>
        </td>
    </tr>
    
    <tr>
        <td style="width: 25%;"><label for="CustomerCgroupId"><?php echo TABLE_GROUP; ?> <span class="red">*</span> :</label></td>
        <td>
            <div class="inputContainer" style="width: 100%;">
                <?php echo $this->Form->input('cgroup_id', array('label' => false, 'empty' => INPUT_SELECT)); ?>
            </div>
        </td>
    </tr>
    <tr>
        <td><label for="CustomerMainNumberAdd"><?php echo TABLE_TELEPHONE; ?> <span class="red">*</span>:</label></td>
        <td>
            <div class="inputContainer" style="width: 100%;">
                <?php echo $this->Form->text('main_number', array('class' => 'validate[required]', 'id' => 'CustomerMainNumberAdd', 'style' => 'width: 90%; text-align: left;')); ?>
            </div>
        </td>
    </tr>
    <tr>
        <td><label for="CustomerEmailAdd"><?php echo TABLE_EMAIL; ?> :</label></td>
        <td>
            <div class="inputContainer" style="width: 100%;">
                <?php echo $this->Form->text('email', array('id' => 'CustomerEmailAdd', 'class' => 'validate[optional,custom[email]]', 'style' => 'width: 90%; text-align: left;')); ?>
            </div>
        </td>
    </tr>
    <tr>
        <td><label for="CustomerPaymentTermId"><?php echo TABLE_PAYMENT_TERMS; ?>:</label></td>
        <td>
            <div class="inputContainer" style="width: 100%;">
                <?php echo $this->Form->input('payment_term_id', array('empty' => INPUT_NONE, 'label' => false, 'style' => 'text-align: left;')); ?>
            </div>
        </td>
    </tr>
    <tr>
        <td><label for="CustomerAddress"><?php echo TABLE_ADDRESS; ?> :</label></td>
        <td>
            <div class="inputContainer" style="width: 100%;">
                <?php echo $this->Form->textarea('address', array('style' => 'width: 90%; height: 80px; text-align: left;')); ?>
            </div>
        </td>
    </tr>
</table>
<?php echo $this->Form->end(); ?>