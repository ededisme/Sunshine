<?php 
echo $this->element('prevent_multiple_submit');
$absolute_url = FULL_BASE_URL . Router::url("/", false); 
?>
<?php echo $javascript->link('jquery.form'); ?>
<script type="text/javascript">
    $(document).ready(function(){
        $("#PatientIPDOtherService").validationEngine();
        $("#PatientIPDOtherService").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".loading").show();
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
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
                            $("#tabs2").tabs("select", 3);
                            $(".btnTabEcho").click();
                            $(this).dialog("close");
                            $(".loading").hide();
                            $(".txtSaveOtherSerRequest").removeAttr('disabled');
                        }
                    }
                });  
            }
        });
        $(".legend_content").show();
        $(".legend_title").click(function(){
            $(this).siblings(".legend_content").slideToggle();
        });
        
    });
</script>
<style>
    .textarea{
        width: 100%;
    }
</style>
<?php
if(empty($patientLeave['PatientLeave'])){
    echo $this->Form->create('Doctor', array('id' => 'PatientIPDOtherService', 'url' => '/patient_ipds/tabOtherService/' . $this->params['pass'][0], 'enctype' => 'multipart/form-data'));
    ?>
    <input name="data[QeuedDoctor][id]" type="hidden" value="<?php echo $queueDoctorId;?>"/>
    <input name="data[Queue][id]" type="hidden" value="<?php echo $queueId;?>"/>
    <div class="legend">
        <div class="legend_title"><label for="DoctorEcho"><b><?php echo TABLE_ECHO_SERVICE; ?></b></label></div>
        <div class="legend_content">
            <?php echo $this->Form->input('echo_description', array('label' => false, 'type' => 'textarea','style'=>'width:99%;')); ?>
            <div class="clear"></div>
        </div>
    </div>
    <div class="clear"></div>
    <div class="legend">
        <div class="legend_title"><label for="DoctorXray"><b><?php echo TABLE_XRAY_SERVICE; ?></b></label></div>
        <div class="legend_content">
            <?php echo $this->Form->input('xray_description', array('label' => false, 'type' => 'textarea','style'=>'width:99%;')); ?>
            <div class="clear"></div>
        </div>
    </div>
    <div class="clear"></div>
    <div class="legend">
        <div class="legend_title"><label for="DoctorCystoscopy"><b><?php echo TABLE_CYSTOSCOPY; ?></b></label></div>
        <div class="legend_content">
            <?php echo $this->Form->input('cystoscopy_description', array('label' => false, 'type' => 'textarea','style'=>'width:99%;')); ?>
            <div class="clear"></div>    
        </div>
    </div>
    <div class="clear"></div>
    <div class="legend">
        <div class="legend_title"><label for="DoctorMidWife"><b><?php echo TABLE_MID_WIFE_SERVICE; ?></b></label></div>
        <div class="legend_content">
            <?php echo $this->Form->input('mid_wife_description', array('label' => false, 'type' => 'textarea','style'=>'width:99%;')); ?>
            <div class="clear"></div>    
        </div>
    </div>
    <div class="clear"></div>
    <div class="buttons">
        <button type="submit" class="positive txtSaveOtherSerRequest">
            <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
            <?php echo ACTION_SAVE; ?>
        </button>
        <img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" class="loading" style="display: none;" />
    </div>
    <div style="clear: both;"></div>
    <?php 
    echo $this->Form->end(); 
}
?>


