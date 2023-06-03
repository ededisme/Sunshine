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
 * 12 - Patient IPD Id
 * 13 - Patient IPD Code
 * 14 - Patient IPD Doctor Id
 * 15 - Patient IPD Group Id
 * 16 - Patient IPD Allergies
 * 17 - Patient IPD Date Ipd
 */

if (!empty($patients)) {
    foreach ($patients as $patient) {        
        echo "{$patient['Patient']['id']}.*{$patient['Patient']['patient_name']}.*{$patient['Patient']['patient_code']}.*{$patient['Patient']['sex']}.*{$patient['Patient']['dob']}.*{$patient['Patient']['patient_group_id']}.*{$patient['Patient']['email']}.*{$patient['Patient']['occupation']}.*{$patient['Patient']['telephone']}.*{$patient['Patient']['address']}.*{$patient['Patient']['location_id']}.*{$patient['Patient']['nationality']}.*{$patient['PatientIpd']['id']}.*{$patient['PatientIpd']['ipd_code']}.*{$patient['Employee']['name']}.*{$patient['Groups']['name']}.*{$patient['PatientIpd']['allergies']}.*{$patient['PatientIpd']['date_ipd']}\n";
    }
}
?>