<script type="text/javascript">
    $(document).ready(function() {
        $(".btnBackPatientPediatric").click(function(event) {
            event.preventDefault();
            var rightPanel = $(this).parent().parent().parent();
            var leftPanel = rightPanel.parent().find(".leftPanel");
            rightPanel.hide("slide", {direction: "right"}, 500, function() {
                leftPanel.show();
                rightPanel.html('');
            });
        });
    });
</script>

<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="" class="positive btnBackPatientPediatric">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<fieldset>
    <legend><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?></legend>
    <table style="width: 100% !important;" class="info">
        <tr>
            <th style="width: 15%;"><?php __(PATIENT_CODE); ?></th>
            <td style="width: 25%;">: <?php echo $patient['Patient']['patient_code']; ?></td>
            <th style="width: 15%;"><?php __(TABLE_DOB); ?></th>
            <td style="width: 25%;">: 
                <?php echo date("d/m/Y", strtotime($patient['Patient']['dob'])); ?>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?php
                echo TABLE_AGE . ': ';
                $then_ts = strtotime($patient['Patient']['dob']);
                $then_year = date('Y', $then_ts);
                $age = date('Y') - $then_year;
                if (strtotime('+' . $age . ' years', $then_ts) > time())
                    $age--;

                if ($age == 0) {
                    $then_year = date('m', $then_ts);
                    $month = date('m') - $then_year;
                    if (strtotime('+' . $month . ' month', $then_ts) > time())
                        $month--;
                    echo $month . ' ' . GENERAL_MONTH;
                }else {
                    echo $age . ' ' . GENERAL_YEAR_OLD;
                }
                ?> 
            </td>
        </tr>
        <tr>
            <th><?php __(PATIENT_NAME); ?></th>
            <td>: <?php echo $patient['Patient']['patient_name']; ?></td>
            <th><?php __(TABLE_RELIGION); ?></th>
            <td>: <?php echo $patient['Patient']['religion']; ?></td>
        </tr>   
        <tr>
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
            <th><?php __(TABLE_ID_CARD); ?></th>
            <td>: <?php echo $patient['Patient']['patient_id_card']; ?></td>
            <th><?php __(TABLE_OCCUPATION); ?></th>
            <td>: <?php echo $patient['Patient']['occupation']; ?></td>
        </tr>
        <tr>
            <th><?php __(TABLE_FAX_NUMBER); ?></th>
            <td>: <?php echo $patient['Patient']['patient_fax_number']; ?></td>
            <th><?php __(TABLE_TELEPHONE); ?></th>
            <td>: <?php echo $patient['Patient']['telephone']; ?></td>
        </tr>
        <tr>
            <th><?php __(TABLE_ADDRESS); ?></th>
            <td>: <?php echo $patient['Patient']['address']; ?></td>
            <th><?php __(TABLE_EMAIL); ?></th>
            <td>: <?php echo $patient['Patient']['email']; ?></td>
        </tr>
        <tr>
            <th><?php __(TABLE_CITY_PROVINCE); ?></th>
            <td>: 
                <?php
                if ($patient['Patient']['location_id'] != "") {
                    $query = mysql_query("SELECT name FROM patient_locations WHERE id=" . $patient['Patient']['location_id']);
                    while ($row = mysql_fetch_array($query)) {
                        echo $row['name'];
                    }
                }
                ?>
            </td>
            <th><?php __(TABLE_RELITION_PATIENT); ?></th>
            <td>: <?php echo $patient['Patient']['relation_patient']; ?></td>
        </tr>

        <tr>
            <th><?php __(TABLE_CASE_EMERGENCY_TEL); ?></th>
            <td>: <?php echo $patient['Patient']['case_emergency_tel']; ?></td>
            <th><?php __(TABLE_CASE_EMERGENCY_NAME); ?></th>
            <td>: <?php echo $patient['Patient']['case_emergency_name']; ?></td>
        </tr>
        <tr>
            <th><?php __(TABLE_BILL_PAID_BY); ?></th>
            <td>: <?php echo $patient['PatientBillType']['name']; ?></td>
            <th><?php __(TABLE_PATIENT_STATUS); ?></th>
            <td>: 
                <input <?php
                if ($patient['Patient']['allergic_medicine'] != "") {
                    echo 'checked="true"';
                }
                ?>  disabled="true" type="checkbox" id="PatientAllergicMedicine"/>
                <label style="padding-right: 20px;" for="PatientAllergicMedicine"><?php echo TABLE_ALLERGIC_MEDICINE; ?></label>
                <?php
                if ($_SESSION['lang'] == "kh") {
                    echo '<br/>&nbsp;';
                }
                ?>
                <input <?php
                    if ($patient['Patient']['allergic_food'] != "") {
                        echo 'checked="true"';
                    }
                ?>  disabled="true" type="checkbox" id="PatientAllergicFood"/>
                <label for="PatientAllergicFood"><?php echo TABLE_ALLERGIC_FOOD; ?></label>
            </td>       
        </tr>
        <tr>
            <td colspan="3"></td>
            <td>
                <?php
                echo '<div style="width:45%;float:left;">';
                if ($patient['Patient']['allergic_medicine_note'] != "") {
                    echo nl2br($patient['Patient']['allergic_medicine_note']);
                }
                echo '</div>';
                echo '<div style="width:45%;float:left;">';
                if ($patient['Patient']['allergic_food_note'] != "") {
                    echo nl2br($patient['Patient']['allergic_food_note']);
                }
                echo '</div>';
                ?>
            </td>
        </tr>
    </table>
    <br/>
    <fieldset>
        <legend><?php __(HOW_TO_KNOW_LIM_TAING_CLINIC); ?></legend>
        <?php
        foreach ($patientConnections as $patientConnection) {
            if (in_array($patientConnection['PatientConnectionWithHospital']['id'], $patientConnectionDetails)) {
                echo '<input disabled="true" checked="true" id="PatientConnectionWithHospital' . $patientConnection['PatientConnectionWithHospital']['id'] . '" name="data[Patient][patient_conection_id][]" type="checkbox" value="' . $patientConnection['PatientConnectionWithHospital']['id'] . '"/>';
            } else {
                echo '<input disabled="true" id="PatientConnectionWithHospital' . $patientConnection['PatientConnectionWithHospital']['id'] . '" name="data[Patient][patient_conection_id][]" type="checkbox" value="' . $patientConnection['PatientConnectionWithHospital']['id'] . '"/>';
            }

            echo '<label style="padding-right: 20px;" for="PatientConnectionWithHospital' . $patientConnection['PatientConnectionWithHospital']['id'] . '">' . $patientConnection['PatientConnectionWithHospital']['name'] . '</label>';
        }
        ?>
    </fieldset>  
</fieldset>    