<?php
// Authentication
$this->element('check_access');
$allowAdmin=checkAccess($user['User']['id'], 'users', 'editProfile');
require_once("includes/function.php");
$index = 1;
$query_followup = mysql_query("SELECT * FROM doctor_chief_complains WHERE status=1 AND patient_consultation_id=" . $patientConsultationId." ORDER BY created ASC");
while ($data_followup = mysql_fetch_array($query_followup)) {
    ?>
    <tr>
        <td style="border: none; font-weight: bold; background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo date('d/m/Y H:i:s', strtotime($data_followup['created'])); ?></td>
        <td style="border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo getDoctor($data_followup['created_by']);?></td>
        <td style="width: 5%;border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);">
            <?php
            if($data_followup['created_by'] == $user['User']['id'] || $allowAdmin){
                echo '<a href="#" type="chief_complain" class="btnEditChiefComplain" rel="' . $data_followup['id'] . '" ><img alt="Edit" onmouseover="Tip(\'' . ACTION_EDIT . '\')" src="' . $this->webroot . 'img/action/edit.png" /></a>';
            }
            ?>
        </td>
    </tr>
    <tr>
        <td style="border: none; padding-left: 5%; color: rgb(0, 0, 0);" colspan="3">
            <?php echo nl2br($data_followup['chief_complain']);?>
        </td>
    </tr>
<?php } ?>