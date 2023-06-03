<?php

class Patient extends AppModel {

    var $name = 'Patient';
    var $hasMany = array(         
        'Queue' => array(
            'className' => 'Queue',
            'foreignKey' => 'patient_id',
            'dependent' => false,
            'conditions' => '',
            'type' => 'INNER',
            'fields' => '',
            'order' => 'Queue.id DESC',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );

    function getPatient($id) {
        $query = "SELECT * FROM patients WHERE (id LIKE '%" . $id .
                "%' OR patient_name LIKE '%" . $id .                
                "%' OR patient_code LIKE '%" . $id .
                "%' OR telephone LIKE '%" . $id . "%') AND is_active=1";                
        $result = $this->query($query);
        return $result;
    }
    
    function searchPatient($id) {
        $query = "SELECT * FROM patients AS Patient WHERE (id LIKE '%" . $id .
                "%' OR patient_name LIKE '%" . $id .                
                "%' OR patient_code LIKE '%" . $id .
                "%' OR telephone LIKE '%" . $id . "%') AND is_active=1";                
        $result = $this->query($query);
        return $result;
    }
    
    function searchPatientIpd($id) {
        $query = "SELECT Patient.*, PatientIpd.*, Groups.name, Employee.name  FROM patients AS Patient INNER JOIN patient_ipds AS PatientIpd ON Patient.id = PatientIpd.patient_id                   
                  INNER JOIN users AS User ON User.id = PatientIpd.doctor_id
                  INNER JOIN user_employees AS UserEmployee ON User.id = UserEmployee.user_id
                  INNER JOIN employees AS Employee ON Employee.id = UserEmployee.employee_id
                  INNER JOIN groups AS Groups ON Groups.id = PatientIpd.group_id
                  WHERE (Patient.id LIKE '%" . $id .
                  "%' OR patient_name LIKE '%" . $id .                
                  "%' OR patient_code LIKE '%" . $id .
                  "%' OR Patient.telephone LIKE '%" . $id . "%') AND Patient.is_active=1 AND PatientIpd.is_active=1";                
        $result = $this->query($query);
        
        
        return $result;
    }

    function getIpdPatient($id) {
        $sql = "SELECT Patient.id FROM patients Patient
                LEFT JOIN ipds Ipd ON Patient.id=Ipd.patient_id
                WHERE Ipd.date_out >= now() OR Patient.is_opd=1";
        $patient = $this->query($sql);
        $ipd = array();
        foreach ($patient as $row) {
            $ipd [] = $row['Patient']['id'];
        }
        if (count($ipd) > 0) {
            $query = "SELECT * FROM patients WHERE (id LIKE  '%" . $id .
                    "%' OR nme LIKE '%" . $id .                
                    "%' OR phn LIKE '%" . $id .
                    "%' OR cde LIKE '%" . $id . "%') AND sts=1 AND id NOT IN (" . implode(',', $ipd) . ") GROUP BY id DESC";
        } else {
            $query = "SELECT * FROM patients WHERE (id LIKE  '%" . $id .
                    "%' OR nme LIKE '%" . $id .                
                    "%' OR phn LIKE '%" . $id .
                    "%' OR cde LIKE '%" . $id . "%') AND sts=1 GROUP BY id DESC";
        }
        $result = $this->query($query);
        return $result;
    }
    
    function getLastPatientHistoryByPatientID($patientId){
        $sql = 'SELECT * FROM queues Queue 
                INNER JOIN patients Patient ON Patient.id = Queue.patient_id
                INNER JOIN queued_doctors QueuedDoctor ON QueuedDoctor.queue_id = Queue.id
                INNER JOIN patient_histories PatientHistory ON PatientHistory.queued_doctor_id = QueuedDoctor.id
                LEFT JOIN (SELECT PatientFlw.patient_history_id,PatientFlw.diagnosis,PatientH.id FROM patient_followups PatientFlw
                    INNER JOIN patient_histories PatientH ON PatientH.id = PatientFlw.patient_history_id
                    WHERE PatientFlw.is_active=1
                    ORDER BY PatientFlw.id DESC LIMIT 1) PatientFollowup ON PatientFollowup.patient_history_id = PatientHistory.id
                WHERE PatientHistory.is_active=1 AND Queue.patient_id = '.$patientId .' ORDER BY PatientHistory.id DESC LIMIT 1';
        $list = $this->query($sql);
        $patientHistory = array();
        if(!empty ($list)){
            $patientHistory = $list[0];
        }
        return $patientHistory;
    }

}

?>