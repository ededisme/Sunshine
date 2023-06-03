<script type="text/javascript">
    $(document).ready(function(){
        $("#PatientDischargeAddForm").validationEngine();
    });
</script>
<?php echo $this->Form->create('PatientDischarge', array ('id'=>'PatientDischargeAddForm'));?>
<h1 class="title"><?php __('Discharge Summary');?></h1>
<table class="info">
    <tr>
        <th><?php echo PATIENT_NAME; ?></th>
        <td><?php echo $patient['Patient']['patient_name']; ?></td>
        <th><?php echo TABLE_AGE; ?></th>
        <td>
            <?php
            list($curYear,$curMonth,$curDay) = split('-',date('Y-m-d'));
            list($year,$month,$day) = split('-',$patient['Patient']['dob']);
            echo $curYear-$year;
            ?>
        </td>
        <th><?php echo TABLE_SEX; ?></th>
        <td><?php echo $patient['Patient']['sex']; ?></td>
    </tr>
    <tr>
        <th><?php echo TABLE_NATIONALITY; ?></th>
        <td colspan="5"><?php echo $nationality; ?></td>
    </tr>
    <tr>
        <th><?php echo TABLE_ADDRESS; ?></th>
        <td colspan="5"><?php echo $patient['Patient']['address']; ?></td>
    </tr>
    <tr>
        <th><?php echo PATIENT_DISCHARGE_TO; ?></th>
        <td colspan="5"><?php echo $this->Form->text('discharge_to', array('class'=>'validate[required]')); ?></td>
    </tr>
    <tr>
        <th><?php echo PATIENT_RELATIONSHIP; ?></th>
        <td colspan="5"><?php echo $this->Form->text('relationship', array('class'=>'validate[required]')); ?></td>
    </tr>
    <tr>
        <th><?php echo TABLE_TELEPHONE; ?></th>
        <td colspan="5"><?php echo $this->Form->text('telephone'); ?></td>
    </tr>
    <tr>
        <th><?php echo TABLE_TELEPHONE_ALT; ?></th>
        <td colspan="5"><?php echo $this->Form->text('telephone_alt'); ?></td>
    </tr>
    <tr>
        <th><?php echo PATIENT_DATE_ADMISSION; ?></th>
        <td colspan="5"><?php echo $patient['Patient']['created']; ?></td>
    </tr>
    <tr>
        <th><?php echo PATIENT_DATE_DISCHARGE; ?></th>
        <td colspan="5"><?php echo date('Y-m-d H:i:s'); ?></td>
    </tr>
</table>
<br />
<div class="legend">
    <div class="legend_title"><label for="PatientDischargeDiagnosis"><b>Diagnosis</b></label></div>
    <div class="legend_content"><?php echo $this->Form->input('diagnosis', array('label' => false, 'type' => 'textarea')); ?></div>
</div>
<br />
<div class="legend">
    <div class="legend_title"><label for="PatientDischargeRational"><b>Rational for Discharge</b></label></div>
    <div class="legend_content"><?php echo $this->Form->input('rational', array('label' => false, 'type' => 'textarea')); ?></div>
</div>
<br />
<div class="legend">
    <div class="legend_title"><label for="PatientDischargeHistoryFinding"><b>History Finding</b></label></div>
    <div class="legend_content"><?php echo $this->Form->input('history_finding', array('label' => false, 'type' => 'textarea')); ?></div>
</div>
<br />
<div class="legend">
    <div class="legend_title"><label for="PatientDischargeLabFinding"><b>Lab Finding</b></label></div>
    <div class="legend_content"><?php echo $this->Form->input('lab_finding', array('label' => false, 'type' => 'textarea')); ?></div>
</div>
<br />
<div class="legend">
    <div class="legend_title"><label for="PatientDischargePatientCondition"><b>Patient's Condition On  the Date Discharges</b></label></div>
    <div class="legend_content"><?php echo $this->Form->input('patient_condition', array('label' => false, 'type' => 'textarea')); ?></div>
</div>
<br />
<div class="legend">
    <div class="legend_title"><label for="PatientDischargeFutureCare"><b>Plans for Future Care</b></label></div>
    <div class="legend_content"><?php echo $this->Form->input('future_care', array('label' => false, 'type' => 'textarea')); ?></div>
</div>
<br />
<div class="legend">
    <div class="legend_title"><label for="PatientDischargeManagement"><b>Management</b></label></div>
    <div class="legend_content"><?php echo $this->Form->input('management', array('label' => false, 'type' => 'textarea')); ?></div>
</div>
<br />
<div class="legend">
    <div class="legend_title"><label for="PatientDischargeRecommendations"><b>Recommendations</b></label></div>
    <div class="legend_content"><?php echo $this->Form->input('recommendations', array('label' => false, 'type' => 'textarea')); ?></div>
</div>