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
            <th style="width: 10%;"><?php __(PATIENT_CODE); ?></th>
            <td style="width: 25%;">: <?php echo $patient['Patient']['patient_code']; ?></td>
            <th style="width: 10%;"><?php __(PATIENT_REGISTER); ?></th>
            <td style="width: 25%;">: <?php echo date("d/m/Y", strtotime($patient['Patient']['register_date'])); ?></td>
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
            <th><?php __(TABLE_REFERRAL); ?></th>
            <td>: 
                <?php 
                    if ($patient['Patient']['referral_id'] != "") {
                    $queryReferral = mysql_query("SELECT name FROM referrals WHERE id=" . $patient['Patient']['referral_id']);
                    while ($row = mysql_fetch_array($queryReferral)) {
                        echo $row['name'];
                    }
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
            <th><?php __(TABLE_FATHER_NAME); ?></th>
            <td>: <?php echo $patient['Patient']['father_name']; ?></td>
            <th><?php __(TABLE_MOTHER_NAME); ?></th>
            <td>: <?php echo $patient['Patient']['mother_name']; ?></td>
        </tr>
        <?php 
            $display = 'display : none';
            if ($patient['Patient']['province_id'] != ""){
                $display = '';
            }
        ?>
        <tr style="<?php echo $display; ?>">
            <th><?php __(TABLE_ADDRESS); ?></th>
            <td>: 
                <?php 
                    if ($patient['Patient']['village_id'] != ""){
                        $queryVillage = mysql_query("SELECT name FROM communes WHERE id=" . $patient['Patient']['village_id']);
                        while ($rowVillage = mysql_fetch_array($queryVillage)) {
                             echo $rowVillage['name'] . ", ";
                        }
                    }
                    if ($patient['Patient']['commune_id'] != ""){
                        $queryCommune = mysql_query("SELECT name FROM communes WHERE id=" . $patient['Patient']['commune_id']);
                        while ($rowCommune = mysql_fetch_array($queryCommune)) {
                             echo $rowCommune['name'] . ", ";
                        }
                    }
                    if ($patient['Patient']['district_id'] != ""){
                        $queryDistrict = mysql_query("SELECT name FROM districts WHERE id=" . $patient['Patient']['district_id']);
                        while ($rowDistrict = mysql_fetch_array($queryDistrict)) {
                             echo $rowDistrict['name'] . ", ";
                        }
                    }
                    if ($patient['Patient']['province_id'] != ""){
                        $queryProvince = mysql_query("SELECT name FROM provinces WHERE id=" . $patient['Patient']['province_id']);
                        while ($rowProvince = mysql_fetch_array($queryProvince)) {
                            echo $rowProvince['name'];
                        }
                    }
                ?>
            </td>
        </tr>
        <?php 
            $displayAllergic = '';
            if ($patient['Patient']['unknown_allergic'] == 1){
                $displayAllergic = 'display : none';
            }
        ?>
        <tr style="<?php echo $displayAllergic; ?>">
            <th><?php __(TABLE_PATIENT_STATUS); ?></th>
            <td>:
                <input <?php if($patient['Patient']['allergic_medicine']!=0){ echo 'checked="true"';}?>  disabled="true" type="checkbox"/>
                <label for="PatientAllergicFood" style="font-weight: bold; color: red;"><?php echo TABLE_ALLERGIC_FOOD; ?></label>
                <?php
                echo '<div style="margin-left: 25px; margin-top: 10px; color: red"> NOTE : ';
                if ($patient['Patient']['allergic_medicine_note'] != "") {
                    echo nl2br($patient['Patient']['allergic_medicine_note']);
                }
                echo '</div>';
                ?>
            </td>
        </tr>
    </table>
    <br />
</fieldset>