<script type="text/javascript">
    $(document).ready(function(){
        $("#DoctorCommentIPDAddForm").validationEngine();
        $('#DoctorCommentIPDReport').change(function(){
            if($(this).val() != ""){
                $.ajax({
                    type: "POST",
                    url: "<?php echo $this->base; ?>/<?php echo 'doctors'; ?>/getDailyClinicalReportDescription/"+$(this).val(),
                    data: "",
                    success: function(result){
                        if(result != 'null'){
                            $("#DoctorCommentDoctorCommentIPD").val(result);
                        }
                    }
                });
            }else {
                $("#DoctorCommentDoctorCommentIPD").val("");
            }               
        });
    });
</script>
<?php 
echo $this->Form->create('DoctorComment', array ('id'=>'DoctorCommentIPDAddForm'));
$doctorComment = "";
$doctorCommentId = "";
$consultDoctorCommentId = "";
if(!empty($dataDoctorComment)){
    $doctorComment = $dataDoctorComment['DoctorComment']['doctor_comment'];
    $doctorCommentId = $dataDoctorComment['DoctorComment']['id'];
}
if(!empty($dataCosultation)){
    $doctorComment = $dataCosultation['PatientConsultation']['remark'];
    $consultDoctorCommentId = $dataCosultation['PatientConsultation']['id'];
}
?>
<div class="legend">
    <div class="legend_title"><label for="DoctorCommentDoctorComment"><b><?php echo MENU_REMARKS; ?></b></label></div>
    <div class="legend_content">
        <table style="width: 100%;">
            <tr>
                <td>
                    <input type='hidden' name="data[DoctorComment][id]" value='<?php echo $doctorCommentId;?>'/>
                    <input type='hidden' name="data[PatientConsultation][id]" value='<?php echo $consultDoctorCommentId;?>'/>
                    <?php //echo $this->Form->input('daily_clinical_report', array('id' => 'DoctorCommentIPDReport', 'empty' => SELECT_OPTION, 'label' => false, 'style' => 'width: 250px;')); ?>
                </td>
            </tr>
            <tr>
                <td><?php echo $this->Form->textarea('doctor_comment', array('id' => 'DoctorCommentDoctorCommentIPD', 'label' => false, 'type' => 'textarea', 'style'=>'width: 99% !important; height: 300px;', 'value' => $doctorComment)); ?></td>
            </tr>
        </table>
    </div>
</div>