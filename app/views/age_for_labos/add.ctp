<?php echo $this->element('prevent_multiple_submit'); ?>

<script type="text/javascript">
    $(document).ready(function() {        
        // Prevent Key Enter
        preventKeyEnter();
        $("#AgeForLaboAddForm").validationEngine();
        $("#AgeForLaboAddForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSavePlace").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $(".btnBackLaboAge").click();
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
        $(".btnBackLaboAge").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableLaboAge.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
        
        // action age from month
        $("#AgeForLaboFromYear").click(function(){ 
            $("#AgeForLaboFromYear").val("");
        });
        $("#AgeForLaboFromYear").blur(function(){           
            if($("#AgeForLaboFromYear").val()==""){
                $("#AgeForLaboFromYear").val(0)
            }
        });
        
        // action age to year
        $("#AgeForLaboToYear").click(function(){ 
            $("#AgeForLaboToYear").val("");
        });
        $("#AgeForLaboToYear").blur(function(){           
            if($("#AgeForLaboToYear").val()==""){
                $("#AgeForLaboToYear").val(0)
            }
        });
        
        // action age from month
        $("#AgeForLaboFromMonth").click(function(){ 
            $("#AgeForLaboFromMonth").val("");
        });
        $("#AgeForLaboFromMonth").blur(function(){           
            if($("#AgeForLaboFromMonth").val()==""){
                $("#AgeForLaboFromMonth").val(0)
            }
        });
        // action age to month
        $("#AgeForLaboToMonth").click(function(){ 
            $("#AgeForLaboToMonth").val("");
        });
        $("#AgeForLaboToMonth").blur(function(){           
            if($("#AgeForLaboToMonth").val()==""){
                $("#AgeForLaboToMonth").val(0)
            }
        });
    });
    function isNumberKey(event){
        var charCode = (event.which)?event.which : event.keyCode;
        if ((charCode > 31 && (charCode < 46 || charCode > 57))|| charCode === 47){
            return false;
        }
        return true;
    }
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="" class="positive btnBackLaboAge">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('AgeForLabo'); ?>
<fieldset>
    <legend><?php __(MENU_LABO_AGE_INFO); ?></legend>
    <table style="width: 100%;" cellspacing="0">
        <tr>
            <td><label for="AgeForLaboName"><?php echo TABLE_AGE; ?> <span class="red">*</span> :</label></td>
            <td><?php echo $this->Form->text('name', array('class' => 'validate[required]')); ?></td>            
        </tr>
        <tr>
            <td><label for="AgeForLaboSex"><?php echo TABLE_SEX; ?> :</label></td>
            <td><?php echo $this->Form->input('sex', array('empty' => SELECT_OPTION, 'label' => false)); ?></td>
        </tr>
        <tr>
            <td><label for="AgeForLaboFROM"><?php echo REPORT_FROM; ?> :</label></td>
            <td><?php echo $this->Form->text('from_year', array('default' => 0, 'class' => 'validate[custom[number]]', 'onkeypress'=>'return isNumberKey(event)')); ?>Year</td>                            
            <td><?php echo $this->Form->text('from_month', array('default' => 0, 'class' => 'validate[custom[number]]', 'onkeypress'=>'return isNumberKey(event)')); ?>Month</td>
        </tr>
        <tr>
            <td><label for="AgeForLaboTo"><?php echo REPORT_TO; ?> :</label></td>
            <td><?php echo $this->Form->text('to_year', array('default' => 0, 'class' => 'validate[custom[number]]', 'onkeypress'=>'return isNumberKey(event)')); ?>Year</td>                            
            <td><?php echo $this->Form->text('to_month', array('default' => 0, 'class' => 'validate[custom[number]]', 'onkeypress'=>'return isNumberKey(event)')); ?>Month</td>
        </tr>
    </table>
</fieldset>
<br />
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
        <span class="txtSavePlace"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<?php echo $this->Form->end(); ?>
                