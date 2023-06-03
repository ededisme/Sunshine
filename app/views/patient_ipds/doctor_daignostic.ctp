<script type="text/javascript">
    $(document).ready(function(){
        $("#DoctorDaignosticIPDAddForm").validationEngine();
        $('#DoctorDaignosticIPDReport').change(function(){
            if($(this).val() != ""){
                $.ajax({
                    type: "POST",
                    url: "<?php echo $this->base; ?>/<?php echo 'doctors'; ?>/getDailyClinicalReportDescription/"+$(this).val(),
                    data: "",
                    success: function(result){
                        if(result != 'null'){
                            $("#DoctorDaignosticDoctorDaignosticIPD").val(result);
                        }
                    }
                });
            }else {
                $("#DoctorDaignosticDoctorDaignosticIPD").val("");
            }               
        });
    });
</script>
<?php 
echo $this->Form->create('DoctorDaignostic', array ('id'=>'DoctorDaignosticIPDAddForm'));
$doctorDaignostic = "";
$doctorDaignosticId = "";
$consultDoctorDaignosticId = "";
if(!empty($dataDoctorDaignostic)){
    $doctorDaignostic = $dataDoctorDaignostic['DoctorDaignostic']['doctor_daignostic'];
    $doctorDaignosticId = $dataDoctorDaignostic['DoctorDaignostic']['id'];
}
if(!empty($dataCosultation)){
    $doctorDaignostic = $dataCosultation['PatientConsultation']['remark'];
    $consultDoctorDaignosticId = $dataCosultation['PatientConsultation']['id'];
}
?>
<div class="legend">
    <div class="legend_title"><label for="DoctorDaignosticDoctorDaignostic"><b><?php echo TABLE_DAIGNOSTIC; ?></b></label></div>
    <div class="legend_content">
        <table style="width: 100%;">
            <tr>
                <td>
                    <input type='hidden' name="data[DoctorDaignostic][id]" value='<?php echo $doctorDaignosticId;?>'/>
                    <input type='hidden' name="data[PatientConsultation][id]" value='<?php echo $consultDoctorDaignosticId;?>'/>
                    <?php //echo $this->Form->input('daily_clinical_report', array('id' => 'DoctorDaignosticIPDReport', 'empty' => SELECT_OPTION, 'label' => false, 'style' => 'width: 250px;')); ?>
                </td>
            </tr>
            <tr>
                <td><?php echo $this->Form->textarea('doctor_daignostic', array('id' => 'DoctorDaignosticDoctorDaignosticIPD', 'label' => false, 'type' => 'textarea', 'style'=>'width: 99% !important; height: 300px;', 'value' => $doctorDaignostic)); ?></td>
            </tr>
        </table>
    </div>
</div>