<table style="width:100%;" align='center'>
    <?php
    $query = mysql_query("SELECT (SELECT name FROM labo_sites WHERE id = labos.labo_site_id) As site_id, number_lab,modified,doctor_id FROM labos WHERE labos.id=" . $code);
    while ($result = mysql_fetch_array($query)) {
        $site_id = $result['site_id'];
        $lab_num = $result['number_lab'];
        $modified = $result['modified'];
        $doctorId = $result['doctor_id'];
    }
    $doctor_name = "";
    if ($doctorId != "") {
        $queryDoctor = mysql_query("SELECT Employee.name FROM `users` AS `User` INNER JOIN user_employees AS `UserEmployee` ON (`User`.`id` = `UserEmployee`.`user_id`) INNER JOIN employees AS `Employee` ON (`Employee`.`id` = `UserEmployee`.`employee_id`) INNER JOIN user_groups AS `UserGroup` ON (`User`.`id` = `UserGroup`.`user_id`)  WHERE `User`.`is_active` = 1 AND `UserGroup`.`group_id` = 3 AND `User`.`id` = " . $doctorId);
        while ($resultDoctor = mysql_fetch_array($queryDoctor)) {
            $doctor_name = $resultDoctor['name'];
        }
    }
    ?>    
    <tr>
        <td style="width: 20%;"> Lab Number</span></td>                
        <td style="width: 5%;">:</td>
        <td style="width: 25%;"><?php echo $lab_num; ?></td>
        <td style="width: 20%;">ថែ្ងខែមក ពិនិត្យ / Date</td>
        <td style="width: 5%;">:</td>
        <td style="width: 25%;"><?php echo date("d/m/Y H:i:s", strtotime($requestDate)); ?></td>
    </tr>
    <tr>
        <td style="width: 20%;"> ឈ្មោះ / Name </td>
        <td style="width: 5%;">:</td>
        <td style="width: 25%;"><?php echo $patient['Patient']['patient_name']; ?></td>
        <td style="width: 15%;"> ភេទ / Sex :
         <?php
            if ($patient['Patient']['sex'] == "M")
                echo GENERAL_MALE_KH. " (M)";
            else
                echo GENERAL_FEMALE_KH. " (F)";
            ?>
        </td>
        <td style="width: 5%;">:</td>
        <td style="width: 30%;"> <?php echo TABLE_AGE_KH." / Age"; ?> :
            <?php
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
		<td style="width: 20%;"> លេខទូរស័ព្ទ/ Telephone </td>
        <td style="width: 5%;">:</td>
        <td colspan="4"><?php echo $patient['Patient']['telephone']; ?></td>
	</tr>
</table>