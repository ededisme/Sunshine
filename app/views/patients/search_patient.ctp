<?php

/**
 * 0 - Patient ID
 * 1 - Patient Full Name
 * 2 - Patient Code
 * 3 - Patient Sex
 * 4 - Patient DOB
 * 5 - Patient Patient Group
 * 6 - Patient email
 * 7 - Patient occupation
 * 8 - Patient telephone
 * 9 - Patient address
 * 10 - Patient location
 * 11 - Patient nationality
 * 12 - Patient type of bill 
 * 13 - Patient insurance
 * 14 - Patient insurance note
 */

if (!empty($patients)) {
    foreach ($patients as $patient) {        
        echo "{$patient['Patient']['id']}.*{$patient['Patient']['patient_name']}.*{$patient['Patient']['patient_code']}.*{$patient['Patient']['sex']}.*{$patient['Patient']['dob']}.*{$patient['Patient']['patient_group_id']}.*{$patient['Patient']['email']}.*{$patient['Patient']['occupation']}.*{$patient['Patient']['telephone']}.*{$patient['Patient']['address']}.*{$patient['Patient']['location_id']}.*{$patient['Patient']['nationality']}.*{$patient['Patient']['patient_bill_type_id']}.*{$patient['Patient']['company_insurance_id']}.*{$patient['Patient']['insurance_note']}\n";
    }
}
?>