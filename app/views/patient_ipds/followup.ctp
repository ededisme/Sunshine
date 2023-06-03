<script type="text/javascript">
    $(document).ready(function(){
        $("#PatientFollowupIPDAddForm").validationEngine();
        $('#PatientFollowupIPDReport').change(function(){
            if($(this).val() != ""){
                $.ajax({
                    type: "POST",
                    url: "<?php echo $this->base; ?>/<?php echo 'doctors'; ?>/getDailyClinicalReportDescription/"+$(this).val(),
                    data: "",
                    success: function(result){
                        if(result != 'null'){
                            $("#PatientFollowupFollowupIPD").val(result);
                        }
                    }
                });
            }else {
                $("#PatientFollowupFollowupIPD").val("");
            }               
        });
    });
</script>
<?php 
echo $this->Form->create('PatientFollowup', array ('id'=>'PatientFollowupIPDAddForm'));
$followUp = "";
$followUpId = "";
$consultFollowUpId = "";
if(!empty($dataFollowup)){
    $followUp = $dataFollowup['PatientFollowup']['followup'];
    $followUpId = $dataFollowup['PatientFollowup']['id'];
}
if(!empty($dataCosultation)){
    $followUp = $dataCosultation['PatientConsultation']['follow_up'];
    $consultFollowUpId = $dataCosultation['PatientConsultation']['id'];
}
?>
<div class="legend">
    <div class="legend_title"><label for="PatientFollowupFollowup"><b><?php echo MENU_FOLLOW_UP; ?></b></label></div>
    <div class="legend_content">
        <table style="width: 100%;">
            <tr>
                <td>
                    <input type='hidden' name="data[PatientFollowup][id]" value='<?php echo $followUpId;?>'/>
                    <input type='hidden' name="data[PatientConsultation][id]" value='<?php echo $consultFollowUpId;?>'/>
                    <?php //echo $this->Form->input('daily_clinical_report', array('id' => 'PatientFollowupIPDReport', 'empty' => SELECT_OPTION, 'label' => false, 'style' => 'width: 250px;')); ?>
                </td>
            </tr>
            <tr>
                <td><?php echo $this->Form->textarea('followup', array('id' => 'PatientFollowupFollowupIPD', 'label' => false, 'type' => 'textarea', 'style'=>'width: 99% !important; height: 300px;', 'value' => $followUp)); ?></td>
            </tr>
        </table>
    </div>
</div>