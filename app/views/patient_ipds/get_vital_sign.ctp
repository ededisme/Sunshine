<?php
// Authentication
$this->element('check_access');
$allowAdmin=checkAccess($user['User']['id'], 'users', 'editProfile');
require_once("includes/function.php");
$index = 1;
$query_vital_sign = mysql_query("SELECT * FROM patient_ipd_vital_signs WHERE is_active=1 AND patient_ipd_id=" . $patientIPDId . " ORDER BY created ASC");
while ($data_vital_sign = mysql_fetch_array($query_vital_sign)) {
    ?>
    <tr>
        <td style="border: none; font-weight: bold; background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo date('d/m/Y H:i:s', strtotime($data_vital_sign['created'])); ?></td>
        <td style="border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo getDoctor($data_vital_sign['created_by']);?></td>
        <td style="width: 5%;border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);">
            <?php
            if ($data_vital_sign['created_by'] == $user['User']['id'] || $allowAdmin) {
                echo '<a href="#" type="followup" class="btnEditVitalSignIPD" patientIPDId="' . $data_vital_sign['patient_ipd_id'] . '" rel="' . $data_vital_sign['id'] . '" ><img alt="Edit" onmouseover="Tip(\'' . ACTION_EDIT . '\')" src="' . $this->webroot . 'img/action/edit.png" /></a>';
            }
            ?>
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <table style="width: 100%;" cellpadding="3" cellspacing="0">
                <tr>
                    <td><label for="PatientIPDVitalSignBp"><?php echo 'BP'; ?></label> = <?php echo $data_vital_sign['bp']; ?> mmHg</td>
                </tr>
                <tr>
                    <td><label for="PatientIPDVitalSignHr"><?php echo 'HR'; ?></label> = <?php echo $data_vital_sign['hr']; ?> bpm</td>
                </tr>
                <tr>
                    <td><label for="PatientIPDVitalSignTemperature"><?php echo 'T<sup>o</sup>'; ?></label> = <?php echo $data_vital_sign['temperature']; ?> °C</td>
                </tr>
                <tr>     
                    <td><label for="PatientIPDVitalSignRr"><?php echo 'RR'; ?></label> = <?php echo $data_vital_sign['rr']; ?> / min</td>
                </tr>
				<tr>     
                    <td><label for="PatientIPDVitalSignSpo2"><?php echo "SpO<sub>2</sub>"; ?></label> = <?php echo $data_vital_sign['sop2']; ?></td>
                </tr>
                <tr style="display: none;">
                    <td><label for="PatientIPDVitalSignUrine"><?php echo 'Urine'; ?></label> = <?php echo $data_vital_sign['urine']; ?> ml/24h</td>
                </tr>
                <tr style="display: none;">
                    <td><label for="PatientIPDVitalSignGasFecal"><?php echo 'Gas and Fecal'; ?> : </label> <?php echo $data_vital_sign['gas_fecal']; ?></td>
                </tr>
                <tr style="display: none;">
                    <td><label for="PatientIPDVitalSignDrainage"><?php echo 'Drainage'; ?></label> : <?php echo $data_vital_sign['drainage']; ?> mL</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;"><label for="PatientIPDVitalSignNote"><?php echo 'Note'; ?></label> : <?php echo nl2br($data_vital_sign['note']); ?></td>
                </tr>
            </table>
        </td>
    </tr>
<?php } ?>