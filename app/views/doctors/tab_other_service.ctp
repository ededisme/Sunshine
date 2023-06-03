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
                $("#tabs3").tabs("select", 7);
                $("#tabEchoNum<?php echo $tblName;?>").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabOtherServiceNum/<?php echo $this->params['pass'][0].'/'.$this->params['pass'][1];?>");
            }
        });
        $(".legend_content").show();
        $(".legend_title").click(function(){
            $(this).siblings(".legend_content").slideToggle();
        });
        
    });
</script>
<?php echo $this->Form->create('Doctor', array('id' => 'DoctorAddForm', 'url' => '/doctors/tabOtherService/' . $this->params['pass'][0], 'enctype' => 'multipart/form-data')); ?>
<input name="data[QeuedDoctor][id]" type="hidden" value="<?php echo $queueDoctorId;?>"/>
<input name="data[Queue][id]" type="hidden" value="<?php echo $queueId;?>"/>
<div class="legend">
    <div class="legend_title"><label for="DoctorEcho"><b><?php echo TABLE_ECHO_SERVICE; ?></b></label></div>
    <div class="legend_content"><?php echo $this->Form->input('echo_description', array('label' => false, 'type' => 'textarea','style'=>'width:99%;')); ?></div>
</div>
<br />
<div class="legend">
    <div class="legend_title"><label for="DoctorXray"><b><?php echo TABLE_XRAY_SERVICE; ?></b></label></div>
    <div class="legend_content"><?php echo $this->Form->input('xray_description', array('label' => false, 'type' => 'textarea','style'=>'width:99%;')); ?></div>
</div>
<br />
<div class="legend" style="display:none;">
    <div class="legend_title"><label for="DoctorCystoscopy"><b><?php echo TABLE_CYSTOSCOPY; ?></b></label></div>
    <div class="legend_content"><?php echo $this->Form->input('cystoscopy_description', array('label' => false, 'type' => 'textarea','style'=>'width:99%;')); ?></div>
</div>
<br />
<div class="legend" style="display:none;">
    <div class="legend_title"><label for="DoctorMidWife"><b><?php echo TABLE_MID_WIFE_SERVICE; ?></b></label></div>
    <div class="legend_content"><?php echo $this->Form->input('mid_wife_description', array('label' => false, 'type' => 'textarea','style'=>'width:99%;')); ?></div>
</div>
<br />
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <?php echo ACTION_SAVE; ?>
    </button>
    <img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" class="loading" style="display: none;" />
</div>
<div style="clear: both;"></div>
<?php echo $this->Form->end(); ?>


