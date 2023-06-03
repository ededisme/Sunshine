<?php

class PatientVitalSignsController extends AppController {

    var $name = 'PatientVitalSigns';
    var $uses = array('Patient', 'Queue');
    var $components = array('Helper');

    function dashboard() {

    }
    function dashboardPatientQueueAjax($date = null) {
        $this->layout = 'ajax';
        $this->set(compact('date'));
    }

    function dashboardPatientFollowupAjax() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $this->set('userId', $user['User']['id']);
    }   

    function vitalSign($queueDoctorId = null, $queueId = null, $patientVitalSignId = null){
        $this->layout = 'ajax';
        $this->loadModel('PatientVitalSignBloodPressure');
        $this->loadModel('PatientVitalSign');
        if(!empty($this->data)){
            $user = $this->getCurrentUser();
            $this->PatientVitalSign->create();
            $this->data['PatientVitalSign']['queued_doctor_id'] = $this->data['QeuedDoctor']['id'];
            $this->data['PatientVitalSign']['created_by'] = $user['User']['id'];
            if ($this->PatientVitalSign->save($this->data['PatientVitalSign'])) {
                    if($this->data['PatientVitalSign']['id']==""){
                        $patientVitalSignId = $this->PatientVitalSign->getLastInsertId();
                    }else{
                        $patientVitalSignId = $this->data['PatientVitalSign']['id'];
                    }
                    
                    $this->PatientVitalSignBloodPressure->create();                    
                    $this->data['PatientVitalSignBloodPressure']['patient_vital_sign_id'] = $patientVitalSignId;                    
                    if ($this->PatientVitalSignBloodPressure->save($this->data['PatientVitalSignBloodPressure'])) {
                        echo MESSAGE_DATA_HAS_BEEN_SAVED;
                        exit;
                    }                                                        
                } else {
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
        }
        $condition= "";
        if($patientVitalSignId!=""){
            $condition = "PatientVitalSign.id={$patientVitalSignId}" ;
        }else{
            $condition = "QeuedDoctor.id={$queueDoctorId}" ;
        }
        
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'QeuedDoctor.status <= 2', 'Queue.id' => $queueId, $condition),
            'fields' => array('Patient.*, Nationality.name, PatientType.name, QeuedDoctor.id, PatientVitalSign.*, PatientVitalSignBloodPressure.*'),
            'joins' => array(
                array('table' => 'queues',
                    'alias' => 'Queue',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Queue.patient_id  = Patient.id'
                    )
                ),
                array('table' => 'queued_doctors',
                    'alias' => 'QeuedDoctor',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'QeuedDoctor.queue_id = Queue.id'
                    )
                ),
                array('table' => 'patient_vital_signs',
                    'alias' => 'PatientVitalSign',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientVitalSign.queued_doctor_id = QeuedDoctor.id'
                    )
                ),
                array('table' => 'patient_vital_sign_blood_pressures',
                    'alias' => 'PatientVitalSignBloodPressure',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientVitalSignBloodPressure.patient_vital_sign_id = PatientVitalSign.id'
                    )
                ),
                array('table' => 'patient_types',
                    'alias' => 'PatientType',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientType.id = Patient.patient_type_id'
                    )
                ),
                array('table' => 'nationalities',
                    'alias' => 'Nationality',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Nationality.id = Patient.nationality'
                    )
                )
        )));
        $this->data = $this->PatientVitalSign->read(null, $patientVitalSignId);
        $this->set(compact('patient'));
    }
}

?>