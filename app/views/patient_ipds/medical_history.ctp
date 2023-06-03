<?php 
echo $this->Form->create('DoctorMedicalHistorie', array ('id'=>'DoctorMedicalHistoryIPDAddForm'));
$doctorDaignostic = "";
$doctorDaignosticId = "";
$consultDoctorDaignosticId = "";
if(!empty($dataDoctorMedicalHistorie)){
    $doctorDaignostic = $dataDoctorMedicalHistorie['DoctorMedicalHistorie']['medical_history'];
    $doctorDaignosticId = $dataDoctorMedicalHistorie['DoctorMedicalHistorie']['id'];
}
if(!empty($dataConsultation)){
    $doctorDaignostic = $dataConsultation['PatientConsultation']['medical_history'];
    $consultDoctorDaignosticId = $dataConsultation['PatientConsultation']['id'];
}
?> 
<div class="legend">
    <div class="legend_title"><label for="PatientConsultationMedicalHistory"><b><?php echo MENU_MEDICAL_HISTORY; ?></b></label></div>
    <div class="legend_content">
        <table style="width: 100%;">
            <tr>
                <td>
                    <input type='hidden' name="data[DoctorMedicalHistorie][id]" value='<?php echo $doctorDaignosticId;?>'/>
                    <input type='hidden' name="data[PatientConsultation][id]" value='<?php echo $consultDoctorDaignosticId;?>'/>
                    <?php //echo $this->Form->input('daily_clinical_report', array('id' => 'DoctorDaignosticIPDReport', 'empty' => SELECT_OPTION, 'label' => false, 'style' => 'width: 250px;')); ?>
                </td>
            </tr>
            <tr>
                <td><?php echo $this->Form->textarea('medical_history', array('label' => false, 'type' => 'textarea', 'style'=>'width: 99% !important; height: 300px;', 'value' => $doctorDaignostic)); ?></td>
            </tr>
        </table>
    </div>
</div>