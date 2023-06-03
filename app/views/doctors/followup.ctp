<script type="text/javascript">
    $(document).ready(function(){
        $("#PatientFollowupAddForm").validationEngine();
        $('#PatientFollowupDailyClinicalReport').change(function(){
            if($(this).val() != ""){
                $.ajax({
                    type: "POST",
                    url: "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/getDailyClinicalReportDescription/"+$(this).val(),
                    data: "",
                    success: function(result){
                        if(result != 'null'){
                            $("#PatientFollowupFollowup").val(result);
                        }
                    }
                });
            }else {
                $("#PatientFollowupFollowup").val("");
            }                        
        });
    });
</script>
<?php echo $this->Form->create('PatientFollowup', array ('id'=>'PatientFollowupAddForm'));?>
<div class="legend">
    <div class="legend_title"><label for="PatientFollowupFollowup"><b>Follow Up  </b></label></div>
    <div class="legend_content" style="height: 320px;">
        <?php echo $this->Form->input('daily_clinical_report', array('empty' => SELECT_OPTION, 'label' => false, 'style' => 'width: 250px;')); ?>  
        <?php echo $this->Form->input('followup', array('label' => false, 'type' => 'textarea', 'style'=>'width: 99%; height: 250px !important;')); ?>
    </div>
</div>
<br />
<div class="legend" style="display: none;">
    <div class="legend_title"><label for="PatientFollowupDiagnosis"><b>Diagnosis</b></label></div>
    <div class="legend_content"><?php echo $this->Form->input('diagnosis', array('label' => false, 'type' => 'textarea', 'style'=>'width: 99%;')); ?></div>
</div>
<br />
<div class="legend" style="display: none;">
    <div class="legend_title"><label for="PatientFollowupTreatment"><b>Treatment</b></label></div>
    <div class="legend_content"><?php echo $this->Form->input('treatment', array('label' => false, 'type' => 'textarea', 'style'=>'width: 99%;')); ?></div>
</div>