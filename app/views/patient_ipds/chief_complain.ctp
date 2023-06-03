<!-- <script type="text/javascript">
    $(document).ready(function(){
        $("#DoctorDaignosticIPDAddForm").validationEngine();
        // $('#DoctorDaignosticIPDReport').change(function(){
        //     if($(this).val() != ""){
        //         $.ajax({
        //             type: "POST",
        //             url: "<?php echo $this->base; ?>/<?php echo 'doctors'; ?>/getDailyClinicalReportDescription/"+$(this).val(),
        //             data: "",
        //             success: function(result){
        //                 if(result != 'null'){
        //                     $("#DoctorDaignosticDoctorDaignosticIPD").val(result);
        //                 }
        //             }
        //         });
        //     }else {
        //         $("#DoctorDaignosticDoctorDaignosticIPD").val("");
        //     }               
        // });
    });
</script> -->
<?php 
echo $this->Form->create('DoctorChiefComplain', array ('id'=>'DoctorChiefComplainIPDAddForm'));
$doctorDaignostic = "";
$doctorDaignosticId = "";
$consultDoctorDaignosticId = "";
if(!empty($dataDoctorChiefComplain)){
    $doctorDaignostic = $dataDoctorChiefComplain['DoctorChiefComplain']['chief_complain'];
    $doctorDaignosticId = $dataDoctorChiefComplain['DoctorChiefComplain']['id'];
}
if(!empty($dataCosultation)){
    $doctorDaignostic = $dataCosultation['PatientConsultation']['chief_complain'];
    $consultDoctorDaignosticId = $dataCosultation['PatientConsultation']['id'];
}
?> 
<div class="legend">
    <div class="legend_title"><label for="DoctorDaignosticDoctorDaignostic"><b><?php echo TABLE_DAIGNOSTIC; ?></b></label></div>
    <div class="legend_content">
        <table style="width: 100%;">
            <tr>
                <td>
                    <input type='hidden' name="data[DoctorChiefComplain][id]" value='<?php echo $doctorDaignosticId;?>'/>
                    <input type='hidden' name="data[PatientConsultation][id]" value='<?php echo $consultDoctorDaignosticId;?>'/>
                    <?php //echo $this->Form->input('daily_clinical_report', array('id' => 'DoctorDaignosticIPDReport', 'empty' => SELECT_OPTION, 'label' => false, 'style' => 'width: 250px;')); ?>
                </td>
            </tr>
            <tr>
                <td><?php echo $this->Form->textarea('chief_complain', array('label' => false, 'type' => 'textarea', 'style'=>'width: 99% !important; height: 300px;', 'value' => $doctorDaignostic)); ?></td>
            </tr>
        </table>
    </div>
</div>