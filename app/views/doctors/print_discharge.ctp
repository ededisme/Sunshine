<?php echo $this->element('/prints/header'); ?>
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
        <td colspan="5"><?php echo $this->data['PatientDischarge']['discharge_to']; ?></td>
    </tr>
    <tr>
        <th><?php echo PATIENT_RELATIONSHIP; ?></th>
        <td colspan="5"><?php echo $this->data['PatientDischarge']['relationship']; ?></td>
    </tr>
    <tr>
        <th><?php echo TABLE_TELEPHONE; ?></th>
        <td colspan="5"><?php echo $this->data['PatientDischarge']['telephone']; ?></td>
    </tr>
    <tr>
        <th><?php echo TABLE_TELEPHONE_ALT; ?></th>
        <td colspan="5"><?php echo $this->data['PatientDischarge']['telephone_alt']; ?></td>
    </tr>
    <tr>
        <th><?php echo PATIENT_DATE_ADMISSION; ?></th>
        <td colspan="5"><?php echo $patient['Patient']['created']; ?></td>
    </tr>
    <tr>
        <th><?php echo PATIENT_DATE_DISCHARGE; ?></th>
        <td colspan="5"><?php echo $this->data['PatientDischarge']['created']; ?></td>
    </tr>
</table>
<br />
<div class="legend">
    <div class="legend_title"><label for="PatientDischargeDiagnosis"><b>Diagnosis</b></label></div>
    <div class="legend_content"><?php echo $this->data['PatientDischarge']['diagnosis']; ?></div>
</div>
<br />
<div class="legend">
    <div class="legend_title"><label for="PatientDischargeRational"><b>Rational for Discharge</b></label></div>
    <div class="legend_content"><?php echo $this->data['PatientDischarge']['rational']; ?></div>
</div>
<br />
<div class="legend">
    <div class="legend_title"><label for="PatientDischargeHistoryFinding"><b>History Finding</b></label></div>
    <div class="legend_content"><?php echo $this->data['PatientDischarge']['history_finding']; ?></div>
</div>
<br />
<div class="legend">
    <div class="legend_title"><label for="PatientDischargeLabFinding"><b>Lab Finding</b></label></div>
    <div class="legend_content"><?php echo $this->data['PatientDischarge']['lab_finding']; ?></div>
</div>
<br />
<div class="legend">
    <div class="legend_title"><label for="PatientDischargePatientCondition"><b>Patient's Condition On  the Date Discharges</b></label></div>
    <div class="legend_content"><?php echo $this->data['PatientDischarge']['patient_condition']; ?></div>
</div>
<br />
<div class="legend">
    <div class="legend_title"><label for="PatientDischargeFutureCare"><b>Plans for Future Care</b></label></div>
    <div class="legend_content"><?php echo $this->data['PatientDischarge']['future_care']; ?></div>
</div>
<br />
<div class="legend">
    <div class="legend_title"><label for="PatientDischargeManagement"><b>Management</b></label></div>
    <div class="legend_content"><?php echo $this->data['PatientDischarge']['management']; ?></div>
</div>
<br />
<div class="legend">
    <div class="legend_title"><label for="PatientDischargeRecommendations"><b>Recommendations</b></label></div>
    <div class="legend_content"><?php echo $this->data['PatientDischarge']['recommendations']; ?></div>
</div>