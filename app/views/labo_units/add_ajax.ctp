<?php if($result==''){ ?>
<?php echo $javascript->link('jquery.form'); ?>
<?php echo $this->Form->create('LaboUnit'); ?>
<br/>
<table>
    <tr>
        <td>
            <label for="LaboUnitName"><?php echo TABLE_ITEM_UNIT; ?> <span class="red">*</span> :</label>
        </td>
        <td>
            <?php echo $this->Form->input('name', array('label' => false, 'class' => 'validate[required]')); ?>
        </td>
    </tr>
    <tr>
        <td>
            <label for="LaboUnitDescription"><?php echo TABLE_DESCRIPTION; ?></label>
        </td>
        <td>
            <?php echo $this->Form->input('description', array('label' => false)); ?>
        </td>
    </tr>
</table>
<br/>
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
        <?php echo ACTION_SAVE; ?>
    </button>
</div>
<?php echo $this->Form->end(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $("#LaboUnitAddAjaxForm").validationEngine();
        $("#LaboUnitAddAjaxForm").ajaxForm({
            success: function(result){
                $("#LaboItemItemLaboUnit").val("");
                $("#LaboItemItemLaboUnit").append(result);
                $("#dialog").dialog("close");
            }
        });
    });
</script>
<?php }else{ 
    echo '<option value="'.$result['name'].'" selected="selected" >'.$result['name'].'</option>';
} ?>