<table style="width:100%;" cellpadding="5" cellspacing="0" >
    <tr>
        <td style=""><?php __(PATIENT_NAME); ?></td>
        <td> : 
            <?php echo $patient['Patient']['patient_name']; ?>&nbsp;&nbsp; 
        </td>
        <td style="width: 100px;"><?php __(TABLE_SEX); ?></td>
        <td> : 
            <?php 
                if ($patient['Patient']['sex'] == "M")
                    echo GENERAL_MALE;
                else
                    echo GENERAL_FEMALE;
            ?>
        </td>
    </tr>
    <tr>
        <td style="width: 150px;"><?php echo PATIENT_CODE; ?></td>
        <td> : 
            <?php echo $patient['Patient']['patient_code']; ?>
        </td>
        <td style=""><?php __(TABLE_AGE); ?></td>
        <td> : 
            <?php 
            echo getAgePatient($patient['Patient']['dob']);           
            ?>
        </td>
    </tr>
    <tr>
        <td style="width: 150px;">
            <?php echo OTHER_REQUESTED_DATE; ?>
        </td>
        <td> : <?php echo date("d/m/Y H:i:s", strtotime($requestDate)); ?></td>
    </tr>
</table>