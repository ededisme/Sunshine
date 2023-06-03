<script type="text/javascript">
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
    });
</script>
<br />
<?php echo $this->Form->create('Section'); 
if(count($companies) == 1){
    $companyId = key($companies);
?>
<input type="hidden" value="<?php echo $companyId; ?>" name="data[company_id]" />
<?php
}
?>
<table style="width: 100%;">
    <tr>
        <td style="width: 20%;"><label for="SectionName"><?php echo TABLE_NAME; ?> <span class="red">*</span> :</label></td>
        <td>
            <div class="inputContainer" style="width: 100%;">
                <?php echo $this->Form->text('name', array('class'=>'validate[required]', 'style' => 'width: 100%; height: 25px;')); ?>
            </div>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: top;"><label for="SectionDescription"><?php echo GENERAL_DESCRIPTION; ?>:</label></td>
        <td>
            <div class="inputContainer" style="width: 100%;">
                <?php echo $this->Form->textarea('description', array('style' => 'width: 100%;')); ?>
            </div>
        </td>
    </tr>
</table>
<div style="clear: both;"></div>
<?php echo $this->Form->end(); ?>