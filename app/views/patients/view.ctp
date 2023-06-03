<script type="text/javascript">
    $(document).ready(function() {
        $(".btnBackPatient").click(function(event) {
            event.preventDefault();
            var rightPanel = $(this).parent().parent().parent();
            var leftPanel = rightPanel.parent().find(".leftPanel");
            rightPanel.hide("slide", {
                direction: "right"
            }, 500, function() {
                leftPanel.show();
                rightPanel.html('');
            });
        });
    });
</script>

<div style="padding: 5px;border: 1px dashed #3C69AD;">
    <div class="buttons">
        <a href="" class="positive btnBackPatient">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt="" />
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<fieldset style="padding: 5px;border: 1px dashed #3C69AD;">
    <legend style="background: #EDEDED; font-weight: bold;"><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?></legend>
    <table style="width: 100% !important;" class="info">
        <tr>
            <th style="width: 15%;"><?php __(PATIENT_CODE); ?></th>
            <td style="width: 25%;">: <?php echo $patient['Patient']['patient_code']; ?></td>
        </tr>
        <tr>
            <th><?php __(PATIENT_NAME); ?></th>
            <td>: <?php echo $patient['Patient']['patient_name']; ?></td>
            <th><?php __(TABLE_SEX); ?></th>
            <td>:
                <?php
                if ($patient['Patient']['sex'] == "F") {
                    echo GENERAL_FEMALE;
                } else {
                    echo GENERAL_MALE;
                }
                ?>
            </td>
        </tr>
        <tr>
            <th><?php __(TABLE_DATE_OF_BIRTH); ?></th>
            <td>:
                <?php echo date("d/m/Y", strtotime($patient['Patient']['dob'])); ?>
            </td>
            <th><?php __(TABLE_TELEPHONE); ?></th>
            <td>: <?php echo $patient['Patient']['telephone']; ?></td>
        </tr>
        <tr>
            <th><?php __(TABLE_ADDRESS); ?></th>
            <td>: <?php echo $patient['Patient']['address']; ?></td>
            <th><?php __(TABLE_NATIONALITY); ?></th>
            <td>:
                <?php
                if ($patient['Patient']['patient_group_id'] != "") {
                    $query = mysql_query("SELECT name FROM patient_groups WHERE id=" . $patient['Patient']['patient_group_id']);
                    while ($row = mysql_fetch_array($query)) {
                        if ($patient['Patient']['patient_group_id'] == 1) {
                            echo $row['name'];
                        } else {
                            echo $row['name'] . '&nbsp;&nbsp;(' . $patient['Nationality']['name'] . ')';
                        }
                    }
                } else {
                    echo $patient['Nationality']['name'];
                }
                ?>
            </td>
        </tr>
        <tr>
            <th><?php __(TABLE_PATIENT_OCCUPATION); ?></th>
            <td>: <?php echo $patient['Patient']['occupation']; ?></td>
            <th><?php __(TABLE_PATIENT_STATUS); ?></th>
            <!-- <td>:
                <input <?php if ($patient['Patient']['allergic_medicine'] != 0) {
                            echo 'checked="true"';
                        } ?> disabled="true" type="checkbox" id="PatientAllergicMedicine" />
                <label style="padding-right: 20px;" for="PatientAllergicMedicine"><?php echo TABLE_ALLERGIC; ?></label>
                <?php
                echo '<div style="margin-left: 25px; margin-top: 10px;  font-weight: bold;"> NOTE : ';
                if ($patient['Patient']['allergic_medicine_note'] != "") {
                    echo nl2br($patient['Patient']['allergic_medicine_note']);
                }
                echo '</div>';
                ?>
            </td> -->
            <td>:
                <input style="" <?php if ($patient['Patient']['allergic_medicine'] != 0) {
                                    echo 'checked="true"';
                                } ?> disabled="true" type="checkbox" />
                <label style="padding-right: 20px;"><?php echo TABLE_ALLERGIC; ?></label>
                <?php if ($_SESSION['lang'] == "kh") {
                    echo '<br/>';
                } ?>
                <input <?php if ($patient['Patient']['unknown_allergic'] != 0) {
                            echo 'checked="true"';
                        } ?> disabled="true" type="checkbox" />
                <label><?php echo TABLE_UNKNOWN_ALLERGIC; ?></label>
            </td>
        </tr>
        <tr>
            <th><?php __(TABLE_FATHER_NAME); ?></th>
            <td>: <?php echo $patient['Patient']['father_name']; ?></td>
            <th><?php __(TABLE_FATHER_OCCUPATION); ?></th>
            <td>: <?php echo $patient['Patient']['father_occupation']; ?></td>
        </tr>
        <tr>
            <th><?php __(TABLE_MOTHER_NAME); ?></th>
            <td>: <?php echo $patient['Patient']['mother_name']; ?></td>
            <th><?php __(TABLE_MOTHER_OCCUPATION); ?></th>
            <td>: <?php echo $patient['Patient']['mother_occupation']; ?></td>
        </tr>
        <tr>
            <th><?php __(TABLE_REFERRAL); ?></th>
            <td colspan="3">: <?php echo $patient['Referral']['name']; ?></td>
        </tr>
    </table>
    <br />
</fieldset>