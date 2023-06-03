<?php 
echo $this->element('prevent_multiple_submit');
$absolute_url = FULL_BASE_URL . Router::url("/", false); 
?>
<?php echo $javascript->link('jquery.form'); ?>
<style type="text/css">
    div.checkbox{
        width: 180px;
    }       
    
</style>
<?php $tblName = "tbl123"; ?>
<script type="text/javascript">
    $(document).ready(function(){
        $("#DoctorAddForm").validationEngine();
        $("#DoctorAddForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".loading").show();
            },
            success: function(result) {
                $("#tabs3").tabs("select", 9);
                $("#tabScanNum<?php echo $tblName;?>").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabScanNum/<?php echo $this->params['pass'][0].'/'.$this->params['pass'][1];?>");
            }
        });
        $(".legend_content").show();
        $(".legend_title").click(function(){
            $(this).siblings(".legend_content").slideToggle();
        });
        
    });
</script>
<?php echo $this->Form->create('Scan', array('id' => 'DoctorAddForm', 'url' => '/doctors/tabScan/' . $this->params['pass'][0], 'enctype' => 'multipart/form-data')); ?>
<input name="data[QeuedDoctor][id]" type="hidden" value="<?php echo $queueDoctorId;?>"/>
<input name="data[Queue][id]" type="hidden" value="<?php echo $queueId;?>"/>

<div class="legend">
    <div class="legend_title"><label for="ScanRequest"><b><?php echo MENU_SCAN; ?></b></label></div>
    <div class="legend_content"><?php echo $this->Form->input('request', array('label' => false, 'type' => 'textarea','style'=>'width:99%;')); ?></div>
</div>
<br />

<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
        <?php echo ACTION_SAVE; ?>
    </button>
    <img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" class="loading" style="display: none;" />
</div>
<div style="clear: both;"></div>
<?php echo $this->Form->end(); ?>


