<?php
class MidWifeServicesController extends AppController {
    var $name = 'MidWifeServices';
    var $uses = array('Patient', 'User');
    var $components = array('Helper');

    function index() {
        $this->layout = 'ajax';
    }
    
    function  ajax(){
        $this->layout = 'ajax';
    }
    
    function midWifeServiceDoctorAjax() {
        $this->layout = 'ajax';
    }
    
    function midWifeServiceDoctor() {
        
    }
    
    function view($id=null){
        $this->layout = 'ajax';        
        if (!empty($id)) {
            $dataService = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'MidWifeService.is_active' => 1, 'MidWifeService.id' => $id),
                'fields' => array('Queue.id, MidWifeService.*, Patient.*'),
                'joins' => array(                    
                    array('table' => 'queues',
                        'alias' => 'Queue',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Queue.patient_id  = Patient.id'
                        )
                    ),
                    array('table' => 'mid_wife_services',
                        'alias' => 'MidWifeService',
                        'type' => 'INNER',
                        'conditions' => array(
                            'MidWifeService.mid_wife_service_queue_id = Queue.id'
                        )
                    )
                )));            
            $this->set(compact('dataService'));
        }
    }
    
    function edit($id=null) {
        $this->layout = 'ajax';
        $this->loadModel('QueuedDoctor');
        $this->loadModel('MidWifeService');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('MidWifeServiceRequest');
        if (!empty($this->data)) {
            $user = $this->getCurrentUser();
            $data['MidWifeService']['id']=$this->data['MidWifeService']['id'];
            $data['MidWifeService']['mid_wife_service_queue_id']=$this->data['Queue']['id'];
            $data['MidWifeService']['last_mentstruation_period']=$this->data['MidWifeService']['last_mentstruation_period'];
            $data['MidWifeService']['estimate_delivery_date']=$this->data['MidWifeService']['estimate_delivery_date'];
            $data['MidWifeService']['echo']=$this->data['MidWifeService']['echo'];
            $data['MidWifeService']['weight']=$this->data['MidWifeService']['weight'];
            $data['MidWifeService']['height']=$this->data['MidWifeService']['height'];
            $data['MidWifeService']['gestation']=$this->data['MidWifeService']['gestation'];
            $data['MidWifeService']['baby']=$this->data['MidWifeService']['baby'];
            $data['MidWifeService']['abortion']=$this->data['MidWifeService']['abortion'];
            $data['MidWifeService']['interuption_volontain']=$this->data['MidWifeService']['interuption_volontain'];
            $data['MidWifeService']['birth']=$this->data['MidWifeService']['birth'];
            $data['MidWifeService']['nee_moit']=$this->data['MidWifeService']['nee_moit'];
            $data['MidWifeService']['mort_nee']=$this->data['MidWifeService']['mort_nee'];
            $data['MidWifeService']['acconchement_normal']=$this->data['MidWifeService']['acconchement_normal'];
            $data['MidWifeService']['caesarean']=$this->data['MidWifeService']['caesarean'];
            $data['MidWifeService']['acc_par_ventonse']=$this->data['MidWifeService']['acc_par_ventonse'];
            $data['MidWifeService']['edema']=$this->data['MidWifeService']['edema'];
            $data['MidWifeService']['albuminuria']=$this->data['MidWifeService']['albuminuria'];
            $data['MidWifeService']['cadiojathie']=$this->data['MidWifeService']['cadiojathie'];
            $data['MidWifeService']['asthma']=$this->data['MidWifeService']['asthma'];
            $data['MidWifeService']['other']=$this->data['MidWifeService']['other'];
            $data['MidWifeService']['modified_by']=$user['User']['id'];
            if ($this->MidWifeService->save($data)) {
                $dataServiceId = $this->MidWifeService->getLastInsertId();
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
            }
        }
        $patient = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'MidWifeService.is_active' => 1, 'MidWifeService.id' => $id),
                'fields' => array('Queue.id, MidWifeService.*, Patient.*'),
                'joins' => array(                    
                    array('table' => 'queues',
                        'alias' => 'Queue',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Queue.patient_id  = Patient.id'
                        )
                    ),
                    array('table' => 'mid_wife_services',
                        'alias' => 'MidWifeService',
                        'type' => 'INNER',
                        'conditions' => array(
                            'MidWifeService.mid_wife_service_queue_id = Queue.id'
                        )
                    )
                )));            
        $this->set(compact('patient'));
    }
            
    function add($id=null,$queueId=null) {
        
    }
    // function for show action in page index mid wife
    function addEditMidWife($queueId=null) {
        $this->layout = 'ajax';
        $this->loadModel('QueuedDoctor');
        $this->loadModel('MidWifeService');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('MidWifeServiceRequest');
        if (!empty($this->data)) {
            $user = $this->getCurrentUser();
            $this->MidWifeService->updateAll(
                    array('MidWifeService.is_active' => "2", 'MidWifeService.modified_by' => $user['User']['id']), array('MidWifeService.id' => $this->data['MidWifeService']['id'])
            );
            $this->MidWifeService->create();
            $data['MidWifeService']['mid_wife_service_queue_id']=$this->data['Queue']['id'];
            $data['MidWifeService']['last_mentstruation_period']=$this->data['MidWifeService']['last_mentstruation_period'];
            $data['MidWifeService']['estimate_delivery_date']=$this->data['MidWifeService']['estimate_delivery_date'];
            $data['MidWifeService']['echo']=$this->data['MidWifeService']['echo'];
            $data['MidWifeService']['weight']=$this->data['MidWifeService']['weight'];
            $data['MidWifeService']['height']=$this->data['MidWifeService']['height'];
            $data['MidWifeService']['gestation']=$this->data['MidWifeService']['gestation'];
            $data['MidWifeService']['baby']=$this->data['MidWifeService']['baby'];
            $data['MidWifeService']['abortion']=$this->data['MidWifeService']['abortion'];
            $data['MidWifeService']['interuption_volontain']=$this->data['MidWifeService']['interuption_volontain'];
            $data['MidWifeService']['birth']=$this->data['MidWifeService']['birth'];
            $data['MidWifeService']['nee_moit']=$this->data['MidWifeService']['nee_moit'];
            $data['MidWifeService']['mort_nee']=$this->data['MidWifeService']['mort_nee'];
            $data['MidWifeService']['acconchement_normal']=$this->data['MidWifeService']['acconchement_normal'];
            $data['MidWifeService']['caesarean']=$this->data['MidWifeService']['caesarean'];
            $data['MidWifeService']['acc_par_ventonse']=$this->data['MidWifeService']['acc_par_ventonse'];
            $data['MidWifeService']['edema']=$this->data['MidWifeService']['edema'];
            $data['MidWifeService']['albuminuria']=$this->data['MidWifeService']['albuminuria'];
            $data['MidWifeService']['cadiojathie']=$this->data['MidWifeService']['cadiojathie'];
            $data['MidWifeService']['asthma']=$this->data['MidWifeService']['asthma'];
            $data['MidWifeService']['other']=$this->data['MidWifeService']['other'];
            $data['MidWifeService']['created_by']=$user['User']['id'];
            if ($this->MidWifeService->save($data)) {
                $dataServiceId = $this->MidWifeService->getLastInsertId();
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
            }
        }
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'OtherServiceRequest.is_active' => 1, 'Queue.id' => $queueId),
            'fields' => array('Queue.id, QeuedDoctor.id, MidWifeServiceRequest.*,Patient.*'),
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
                array('table' => 'other_service_requests',
                    'alias' => 'OtherServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'OtherServiceRequest.queued_doctor_id = QeuedDoctor.id'
                    )
                ),
                array('table' => 'mid_wife_service_requests',
                    'alias' => 'MidWifeServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeServiceRequest.other_service_request_id = OtherServiceRequest.id'
                    )
                )
            )));           
        $this->set(compact('patient'));
        
        $midWife = $this->MidWifeService->find('all', array('conditions' => array('MidWifeService.is_active' => 1,'MidWifeService.mid_wife_service_queue_id' => $queueId)));
        $this->set(compact('midWife', $midWife));
    }
    // function for add new data mid wife in page index mid wife
    function addNewMidWife($queueId=null) {
        $this->layout = 'ajax';
        $this->loadModel('QueuedDoctor');
        $this->loadModel('MidWifeService');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('MidWifeServiceRequest');
        if (!empty($this->data)) {
            $this->MidWifeService->create();
            $user = $this->getCurrentUser();
            $this->data['MidWifeService']['mid_wife_service_queue_id']=$this->data['Queue']['id'];
            $this->data['MidWifeService']['created_by']=$user['User']['id'];
            if ($this->MidWifeService->save($this->data)) {
                $serReq['MidWifeServiceRequest']['id']=$this->data['MidWifeServiceRequest']['id'];
                $serReq['MidWifeServiceRequest']['is_active']=2;
                $this->MidWifeServiceRequest->save($serReq);
                $queueId = $this->data['Queue']['id'];
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
            }
        }
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'OtherServiceRequest.is_active' => 1, 'Queue.id' => $queueId),
            'fields' => array('Queue.id, QeuedDoctor.id, MidWifeServiceRequest.*,Patient.*'),
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
                array('table' => 'other_service_requests',
                    'alias' => 'OtherServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'OtherServiceRequest.queued_doctor_id = QeuedDoctor.id'
                    )
                ),
                array('table' => 'mid_wife_service_requests',
                    'alias' => 'MidWifeServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeServiceRequest.other_service_request_id = OtherServiceRequest.id'
                    )
                )
            )));
        $this->set(compact('patient'));
    }
    
    // function add data in dashboard doctor mid wife
    function addMidWifeServiceDoctor($queueId=null) {
        $this->layout = 'ajax';
        $this->loadModel('QueuedDoctor');
        $this->loadModel('MidWifeService');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('MidWifeServiceRequest');
        if (!empty($this->data)) {
            $this->MidWifeService->create();
            $user = $this->getCurrentUser();
            $this->data['MidWifeService']['mid_wife_service_queue_id']=$this->data['Queue']['id'];
            $this->data['MidWifeService']['created_by']=$user['User']['id'];
            if ($this->MidWifeService->save($this->data)) {
                $serReq['MidWifeServiceRequest']['id']=$this->data['MidWifeServiceRequest']['id'];
                $serReq['MidWifeServiceRequest']['is_active']=2;
                $this->MidWifeServiceRequest->save($serReq);
                $queueId = $this->data['Queue']['id'];
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
            }
        }
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'OtherServiceRequest.is_active' => 1, 'Queue.id' => $queueId),
            'fields' => array('Queue.id, QeuedDoctor.id, MidWifeServiceRequest.*,Patient.*'),
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
                array('table' => 'other_service_requests',
                    'alias' => 'OtherServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'OtherServiceRequest.queued_doctor_id = QeuedDoctor.id'
                    )
                ),
                array('table' => 'mid_wife_service_requests',
                    'alias' => 'MidWifeServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeServiceRequest.other_service_request_id = OtherServiceRequest.id'
                    )
                )
            )));
        $this->set(compact('patient'));
        $midWife = $this->MidWifeService->find('all', array('conditions' => array('MidWifeService.is_active' => 1,'MidWifeService.mid_wife_service_queue_id' => $queueId)));
        $this->set(compact('midWife', $midWife));
    }
    
    function checkUpPatient($id=null,$queueId=null){
        $this->layout = 'ajax'; 
        $this->loadModel('QueuedDoctor');
        $this->loadModel('MidWifeService');
        $this->loadModel('MidWifeCheckUpPatient');
        if (!empty($this->data)) {
            $this->MidWifeCheckUpPatient->create();
            $user = $this->getCurrentUser();
            $this->data['MidWifeCheckUpPatient']['mid_wife_service_id']=$this->data['MidWifeService']['id'];
            $this->data['MidWifeCheckUpPatient']['weight']=$this->data['MidWifeService']['weight'];
            $this->data['MidWifeCheckUpPatient']['height']=$this->data['MidWifeService']['height'];
            $this->data['MidWifeCheckUpPatient']['blood_pressure']=$this->data['MidWifeService']['blood_pressure'];
            $this->data['MidWifeCheckUpPatient']['pulse']=$this->data['MidWifeService']['pulse'];
            $this->data['MidWifeCheckUpPatient']['temperature']=$this->data['MidWifeService']['temperature'];
            $this->data['MidWifeCheckUpPatient']['presentation']=$this->data['MidWifeService']['presentation'];
            $this->data['MidWifeCheckUpPatient']['uterus_height']=$this->data['MidWifeService']['uterus_height'];
            $this->data['MidWifeCheckUpPatient']['baby_heart_rate']=$this->data['MidWifeService']['baby_heart_rate'];
            $this->data['MidWifeCheckUpPatient']['iron']=$this->data['MidWifeService']['iron'];
            if(!empty($this->data['MidWifeService']['edema'])){
                $edema=1;
            }else{
                $edema=0;
            }
            if(!empty($this->data['MidWifeService']['albuminuria'])){
                $albuminuria=1;
            }else{
                $albuminuria=0;
            }
            if(!empty($this->data['MidWifeService']['asthma'])){
                $asthma=1;
            }else{
                $asthma=0;
            }
            $this->data['MidWifeCheckUpPatient']['edema']=$edema;
            $this->data['MidWifeCheckUpPatient']['albuminuria']=$albuminuria;
            $this->data['MidWifeCheckUpPatient']['asthma']=$asthma;
            $this->data['MidWifeCheckUpPatient']['other']=$this->data['MidWifeService']['other'];
            $this->data['MidWifeCheckUpPatient']['next_appointment']=$this->data['MidWifeService']['next_appointment'];
            $this->data['MidWifeCheckUpPatient']['created_by']=$user['User']['id'];
            if ($this->MidWifeCheckUpPatient->save($this->data['MidWifeCheckUpPatient'])) {
                $queueId = $this->data['Queue']['id'];
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
            }
        }
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'MidWifeService.is_active' => 1, 'Queue.id' => $queueId),
            'fields' => array('Queue.id, MidWifeService.*,Patient.*'),
            'joins' => array(
                array('table' => 'queues',
                    'alias' => 'Queue',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Queue.patient_id  = Patient.id'
                    )
                ),
                array('table' => 'mid_wife_services',
                    'alias' => 'MidWifeService',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeService.mid_wife_service_queue_id = Queue.id'
                    )
                )
            )));
        $this->set(compact('patient'));
    }
    
    // function edit data dossier medical
    function editCheckUpPatient($id=null) {
        $this->layout = 'ajax';
        $this->loadModel('Queue');
        $this->loadModel('MidWifeService');
        $this->loadModel('MidWifeCheckUpPatient');
        if (!empty($this->data)) {
            $user = $this->getCurrentUser();
            $this->MidWifeCheckUpPatient->updateAll(
                        array('MidWifeCheckUpPatient.is_active' => "2", 'MidWifeCheckUpPatient.modified_by' => $user['User']['id']), array('MidWifeCheckUpPatient.id' => $this->data['MidWifeCheckUpPatient']['check_up_patient_id'])
            );
            $this->MidWifeCheckUpPatient->create();
            $this->data['MidWifeCheckUpPatient']['mid_wife_service_id']=$this->data['MidWifeService']['id'];
            $this->data['MidWifeCheckUpPatient']['weight']=$this->data['MidWifeService']['weight'];
            $this->data['MidWifeCheckUpPatient']['height']=$this->data['MidWifeService']['height'];
            $this->data['MidWifeCheckUpPatient']['blood_pressure']=$this->data['MidWifeService']['blood_pressure'];
            $this->data['MidWifeCheckUpPatient']['pulse']=$this->data['MidWifeService']['pulse'];
            $this->data['MidWifeCheckUpPatient']['temperature']=$this->data['MidWifeService']['temperature'];
            $this->data['MidWifeCheckUpPatient']['presentation']=$this->data['MidWifeService']['presentation'];
            $this->data['MidWifeCheckUpPatient']['uterus_height']=$this->data['MidWifeService']['uterus_height'];
            $this->data['MidWifeCheckUpPatient']['baby_heart_rate']=$this->data['MidWifeService']['baby_heart_rate'];
            $this->data['MidWifeCheckUpPatient']['iron']=$this->data['MidWifeService']['iron'];
            if(!empty($this->data['MidWifeService']['edema'])){
                $edema=1;
            }else{
                $edema=0;
            }
            if(!empty($this->data['MidWifeService']['albuminuria'])){
                $albuminuria=1;
            }else{
                $albuminuria=0;
            }
            if(!empty($this->data['MidWifeService']['asthma'])){
                $asthma=1;
            }else{
                $asthma=0;
            }
            $this->data['MidWifeCheckUpPatient']['edema']=$edema;
            $this->data['MidWifeCheckUpPatient']['albuminuria']=$albuminuria;
            $this->data['MidWifeCheckUpPatient']['asthma']=$asthma;
            $this->data['MidWifeCheckUpPatient']['other']=$this->data['MidWifeService']['other'];
            $this->data['MidWifeCheckUpPatient']['next_appointment']=$this->data['MidWifeService']['next_appointment'];
            $this->data['MidWifeCheckUpPatient']['created_by']=$user['User']['id'];
            if ($this->MidWifeCheckUpPatient->save($this->data['MidWifeCheckUpPatient'])) {
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
            }
        }
        $patient = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'MidWifeService.is_active' => 1,'MidWifeCheckUpPatient.is_active' => 1, 'MidWifeCheckUpPatient.id' => $id),
                'fields' => array('Queue.id, MidWifeService.*, Patient.*','MidWifeCheckUpPatient.*'),
                'joins' => array(                    
                    array('table' => 'queues',
                        'alias' => 'Queue',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Queue.patient_id  = Patient.id'
                        )
                    ),
                    array('table' => 'mid_wife_services',
                        'alias' => 'MidWifeService',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'MidWifeService.mid_wife_service_queue_id = Queue.id'
                        )
                    ),
                    array('table' => 'mid_wife_check_up_patients',
                        'alias' => 'MidWifeCheckUpPatient',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'MidWifeCheckUpPatient.mid_wife_service_id = MidWifeService.id'
                        )
                    )
                ))); 
        $this->set(compact('patient'));
    }
    
    function printMidWifeService($id = null) {
        $this->layout = 'ajax';        
        if (!empty($id)) {
            $dataService = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'MidWifeService.is_active' => 1, 'MidWifeService.id' => $id),
                'fields' => array('Queue.id, MidWifeService.*, Patient.*'),
                'joins' => array(                    
                    array('table' => 'queues',
                        'alias' => 'Queue',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Queue.patient_id  = Patient.id'
                        )
                    ),
                    array('table' => 'mid_wife_services',
                        'alias' => 'MidWifeService',
                        'type' => 'INNER',
                        'conditions' => array(
                            'MidWifeService.mid_wife_service_queue_id = Queue.id'
                        )
                    )
                )));            
            $this->set(compact('dataService'));
        }
    }
    
    // function add data in dashboard doctor mid wife (Dossier Medical)
    function addMidWifeServiceDoctorDossierMedical($queueId=null) {
        $this->layout = 'ajax';
        $this->loadModel('QueuedDoctor');
        $this->loadModel('MidWifeDossierMedical');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('MidWifeServiceRequest');
        if (!empty($this->data)) {
            $this->MidWifeService->create();
            $user = $this->getCurrentUser();
            $this->data['MidWifeService']['mid_wife_service_queue_id']=$this->data['Queue']['id'];
            $this->data['MidWifeService']['created_by']=$user['User']['id'];
            if ($this->MidWifeService->save($this->data)) {
                $serReq['MidWifeServiceRequest']['id']=$this->data['MidWifeServiceRequest']['id'];
                $serReq['MidWifeServiceRequest']['is_active']=2;
                $this->MidWifeServiceRequest->save($serReq);
                $queueId = $this->data['Queue']['id'];
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
            }
        }
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'OtherServiceRequest.is_active' => 1, 'Queue.id' => $queueId),
            'fields' => array('Queue.id, QeuedDoctor.id, MidWifeServiceRequest.*,Patient.*'),
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
                array('table' => 'other_service_requests',
                    'alias' => 'OtherServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'OtherServiceRequest.queued_doctor_id = QeuedDoctor.id'
                    )
                ),
                array('table' => 'mid_wife_service_requests',
                    'alias' => 'MidWifeServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeServiceRequest.other_service_request_id = OtherServiceRequest.id'
                    )
                )
            )));
        $this->set(compact('patient'));
        $midWife = $this->MidWifeDossierMedical->find('all', array('conditions' => array('MidWifeDossierMedical.is_active' => 1,'MidWifeDossierMedical.queue_id' => $queueId)));
        $this->set(compact('midWife', $midWife));
    }
    
    // function add data in index mid wife (Dossier Medical)
    function addMidWifeServiceDoctorDossierMedicalIndex($queueId=null) {
        $this->layout = 'ajax';
        $this->loadModel('QueuedDoctor');
        $this->loadModel('MidWifeDossierMedical');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('MidWifeServiceRequest');
        if (!empty($this->data)) {
            $this->MidWifeService->create();
            $user = $this->getCurrentUser();
            $this->data['MidWifeService']['mid_wife_service_queue_id']=$this->data['Queue']['id'];
            $this->data['MidWifeService']['created_by']=$user['User']['id'];
            if ($this->MidWifeService->save($this->data)) {
                $serReq['MidWifeServiceRequest']['id']=$this->data['MidWifeServiceRequest']['id'];
                $serReq['MidWifeServiceRequest']['is_active']=2;
                $this->MidWifeServiceRequest->save($serReq);
                $queueId = $this->data['Queue']['id'];
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
            }
        }
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'OtherServiceRequest.is_active' => 1, 'Queue.id' => $queueId),
            'fields' => array('Queue.id, QeuedDoctor.id, MidWifeServiceRequest.*,Patient.*'),
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
                array('table' => 'other_service_requests',
                    'alias' => 'OtherServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'OtherServiceRequest.queued_doctor_id = QeuedDoctor.id'
                    )
                ),
                array('table' => 'mid_wife_service_requests',
                    'alias' => 'MidWifeServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeServiceRequest.other_service_request_id = OtherServiceRequest.id'
                    )
                )
            )));
        $this->set(compact('patient'));
        $midWife = $this->MidWifeDossierMedical->find('all', array('conditions' => array('MidWifeDossierMedical.is_active' => 1,'MidWifeDossierMedical.queue_id' => $queueId)));
        $this->set(compact('midWife', $midWife));
    }
    // function add new data dossier medical
    function addNewDossierMedical($queueId=null) {
        $this->layout = 'ajax';
        $this->loadModel('QueuedDoctor');
        $this->loadModel('MidWifeDossierMedical');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('MidWifeServiceRequest');
        if (!empty($this->data)) {
            $this->MidWifeDossierMedical->create();
            $user = $this->getCurrentUser();
            $this->data['MidWifeDossierMedical']['queue_id']=$this->data['Queue']['id'];
            $this->data['MidWifeDossierMedical']['entre_le']=$this->data['MidWifeService']['entre_le'];
            $this->data['MidWifeDossierMedical']['doagnostic_entre']=$this->data['MidWifeService']['doagnostic_entre'];
            $this->data['MidWifeDossierMedical']['sortie_le']=$this->data['MidWifeService']['sortie_le'];
            $this->data['MidWifeDossierMedical']['doagnostic_sortie']=$this->data['MidWifeService']['doagnostic_sortie'];
            $this->data['MidWifeDossierMedical']['accon_chement']=$this->data['MidWifeService']['accon_chement'];
            $this->data['MidWifeDossierMedical']['accon_chement']=$this->data['MidWifeService']['accon_chement'];
            $this->data['MidWifeDossierMedical']['avortment_inv']=$this->data['MidWifeService']['avortment_inv'];
            $this->data['MidWifeDossierMedical']['baby']=$this->data['MidWifeService']['baby'];
            if(!empty($this->data['MidWifeService']['caesarean'])){
                $this->data['MidWifeDossierMedical']['caesarean']=$this->data['MidWifeService']['caesarean'];
            }else{
               $this->data['MidWifeDossierMedical']['caesarean']=0;
            }
            if(!empty($this->data['MidWifeService']['hemorrhage'])){
                $this->data['MidWifeDossierMedical']['hemorrhage']=$this->data['MidWifeService']['hemorrhage'];
            }else{
               $this->data['MidWifeDossierMedical']['hemorrhage']=0;
            }
            if(!empty($this->data['MidWifeService']['hypertension'])){
                $this->data['MidWifeDossierMedical']['hypertension']=$this->data['MidWifeService']['hypertension'];
            }else{
               $this->data['MidWifeDossierMedical']['hypertension']=0;
            }
            if(!empty($this->data['MidWifeService']['heart'])){
                $this->data['MidWifeDossierMedical']['heart']=$this->data['MidWifeService']['heart'];
            }else{
               $this->data['MidWifeDossierMedical']['heart']=0;
            }
            $this->data['MidWifeDossierMedical']['other']=$this->data['MidWifeService']['other'];
            $this->data['MidWifeDossierMedical']['ta']=$this->data['MidWifeService']['ta'];
            $this->data['MidWifeDossierMedical']['p']=$this->data['MidWifeService']['p'];
            $this->data['MidWifeDossierMedical']['t']=$this->data['MidWifeService']['t'];
            $this->data['MidWifeDossierMedical']['presentation']=$this->data['MidWifeService']['presentation'];
            $this->data['MidWifeDossierMedical']['hu']=$this->data['MidWifeService']['hu'];
            $this->data['MidWifeDossierMedical']['bcf']=$this->data['MidWifeService']['bcf'];
            $this->data['MidWifeDossierMedical']['col']=$this->data['MidWifeService']['col'];
            $this->data['MidWifeDossierMedical']['pde']=$this->data['MidWifeService']['pde'];
            $this->data['MidWifeDossierMedical']['edema']=$this->data['MidWifeService']['edema'];
            $this->data['MidWifeDossierMedical']['created_by']=$user['User']['id'];
            if ($this->MidWifeDossierMedical->save($this->data)) {
                $serReq['MidWifeServiceRequest']['id']=$this->data['MidWifeServiceRequest']['id'];
                $serReq['MidWifeServiceRequest']['is_active']=2;
                $this->MidWifeServiceRequest->save($serReq);
                $queueId = $this->data['Queue']['id'];
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
            }
        }
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'OtherServiceRequest.is_active' => 1, 'Queue.id' => $queueId),
            'fields' => array('Queue.id, QeuedDoctor.id, MidWifeServiceRequest.*,Patient.*'),
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
                array('table' => 'other_service_requests',
                    'alias' => 'OtherServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'OtherServiceRequest.queued_doctor_id = QeuedDoctor.id'
                    )
                ),
                array('table' => 'mid_wife_service_requests',
                    'alias' => 'MidWifeServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeServiceRequest.other_service_request_id = OtherServiceRequest.id'
                    )
                )
            )));
        $this->set(compact('patient'));
    }
    // function edit data dossier medical
    function editDossierMedical($id=null) {
        $this->layout = 'ajax';
        $this->loadModel('QueuedDoctor');
        $this->loadModel('MidWifeDossierMedical');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('MidWifeServiceRequest');
        if (!empty($this->data)) {
            $user = $this->getCurrentUser();
            $this->data['MidWifeDossierMedical']['id']=$this->data['MidWifeDossierMedical']['id'];
            $this->data['MidWifeDossierMedical']['queue_id']=$this->data['Queue']['id'];
            $this->data['MidWifeDossierMedical']['entre_le']=$this->data['MidWifeService']['entre_le'];
            $this->data['MidWifeDossierMedical']['doagnostic_entre']=$this->data['MidWifeService']['doagnostic_entre'];
            $this->data['MidWifeDossierMedical']['sortie_le']=$this->data['MidWifeService']['sortie_le'];
            $this->data['MidWifeDossierMedical']['doagnostic_sortie']=$this->data['MidWifeService']['doagnostic_sortie'];
            $this->data['MidWifeDossierMedical']['acconchement_rerme']=$this->data['MidWifeService']['acconchement_rerme'];
            $this->data['MidWifeDossierMedical']['accon_chement']=$this->data['MidWifeService']['accon_chement'];
            $this->data['MidWifeDossierMedical']['avortment_inv']=$this->data['MidWifeService']['avortment_inv'];
            $this->data['MidWifeDossierMedical']['baby']=$this->data['MidWifeService']['baby'];
            if(!empty($this->data['MidWifeService']['caesarean'])){
                $this->data['MidWifeDossierMedical']['caesarean']=$this->data['MidWifeService']['caesarean'];
            }else{
               $this->data['MidWifeDossierMedical']['caesarean']=0;
            }
            if(!empty($this->data['MidWifeService']['hemorrhage'])){
                $this->data['MidWifeDossierMedical']['hemorrhage']=$this->data['MidWifeService']['hemorrhage'];
            }else{
               $this->data['MidWifeDossierMedical']['hemorrhage']=0;
            }
            if(!empty($this->data['MidWifeService']['hypertension'])){
                $this->data['MidWifeDossierMedical']['hypertension']=$this->data['MidWifeService']['hypertension'];
            }else{
               $this->data['MidWifeDossierMedical']['hypertension']=0;
            }
            if(!empty($this->data['MidWifeService']['heart'])){
                $this->data['MidWifeDossierMedical']['heart']=$this->data['MidWifeService']['heart'];
            }else{
               $this->data['MidWifeDossierMedical']['heart']=0;
            }
            $this->data['MidWifeDossierMedical']['other']=$this->data['MidWifeService']['other'];
            $this->data['MidWifeDossierMedical']['ta']=$this->data['MidWifeService']['ta'];
            $this->data['MidWifeDossierMedical']['p']=$this->data['MidWifeService']['p'];
            $this->data['MidWifeDossierMedical']['t']=$this->data['MidWifeService']['t'];
            $this->data['MidWifeDossierMedical']['presentation']=$this->data['MidWifeService']['presentation'];
            $this->data['MidWifeDossierMedical']['hu']=$this->data['MidWifeService']['hu'];
            $this->data['MidWifeDossierMedical']['bcf']=$this->data['MidWifeService']['bcf'];
            $this->data['MidWifeDossierMedical']['col']=$this->data['MidWifeService']['col'];
            $this->data['MidWifeDossierMedical']['pde']=$this->data['MidWifeService']['pde'];
            $this->data['MidWifeDossierMedical']['edema']=$this->data['MidWifeService']['edema'];
            $this->data['MidWifeDossierMedical']['modified_by']=$user['User']['id'];
            if ($this->MidWifeDossierMedical->save($this->data)) {
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
            }
        }
        $patient = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'MidWifeDossierMedical.is_active' => 1, 'MidWifeDossierMedical.id' => $id),
                'fields' => array('Queue.id, MidWifeDossierMedical.*, Patient.*'),
                'joins' => array(                    
                    array('table' => 'queues',
                        'alias' => 'Queue',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Queue.patient_id  = Patient.id'
                        )
                    ),
                    array('table' => 'mid_wife_dossier_medicals',
                        'alias' => 'MidWifeDossierMedical',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'MidWifeDossierMedical.queue_id = Queue.id'
                        )
                    )
                )));        
        $this->set(compact('patient'));
    }
    
    // function add new data tacking in dossier medical
    function addNewTracking($id=null) {
        $this->layout = 'ajax';
        $this->loadModel('Queue');
        $this->loadModel('MidWifeDossierMedical');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('MidWifeServiceRequest');
        $this->loadModel('MidWifeBirth');
        $this->loadModel('MidWifeBirthDetail');
        if (!empty($this->data)) {
            $user = $this->getCurrentUser();
            for ($i = 0; $i < sizeof($_POST['time']); $i++) {
                if ($_POST['time'][$i] != '') {
                    $this->MidWifeBirth->create();
                    $Birth['MidWifeBirth']['time'] = $_POST['time'][$i];
                    $Birth['MidWifeBirth']['bcf'] = $_POST['bcf'][$i];
                    $Birth['MidWifeBirth']['pdf'] = $_POST['pdf'][$i];
                    $Birth['MidWifeBirth']['col'] = $_POST['col'][$i];
                    $Birth['MidWifeBirth']['ta'] = $_POST['ta'][$i];
                    $Birth['MidWifeBirth']['pouls'] = $_POST['pouls'][$i];
                    $Birth['MidWifeBirth']['temperature'] = $_POST['temperature'][$i];
                    $Birth['MidWifeBirth']['created_by'] = $user['User']['id'];
                    $Birth['MidWifeBirth']['mid_wife_dossier_medical_id'] = $this->data['MidWifeService']['mid_wife_dossier_medical_id'];
                    $this->MidWifeBirth->save($Birth);
                }
            }
            $patient_birth_detail = $this->MidWifeBirthDetail->find('all', array('conditions' => array('MidWifeBirthDetail.mid_wife_dossier_medical_id' => $this->data['MidWifeService']['mid_wife_dossier_medical_id'])));
            if(!empty($patient_birth_detail['0']['MidWifeBirthDetail']['id'])){
                $birth_detail_id = $patient_birth_detail['0']['MidWifeBirthDetail']['id'];
                $this->MidWifeBirthDetail->updateAll(
                        array('MidWifeBirthDetail.is_active' => "2", 'MidWifeBirthDetail.modified_by' => $user['User']['id']), array('MidWifeBirthDetail.id' => $birth_detail_id)
                );
            }
            $this->MidWifeBirthDetail->create();
            if (!empty($this->data['MidWifeService']['hydramnios'])) {
                $this->data['MidWifeBirthDetail']['hydramnios'] = 1;
            }else{
                $this->data['MidWifeBirthDetail']['hydramnios'] = 0;
            }
            if (!empty($this->data['MidWifeService']['excess'])) {
                $this->data['MidWifeBirthDetail']['excess'] = 1;
            }else{
                $this->data['MidWifeBirthDetail']['excess'] = 0;
            }
            if (!empty($this->data['MidWifeService']['normal'])) {
                $this->data['MidWifeBirthDetail']['normal'] = 1;
            }else{
                $this->data['MidWifeBirthDetail']['normal'] = 0;
            }
            if (!empty($this->data['MidWifeService']['oligoamnios'])) {
                $this->data['MidWifeBirthDetail']['oligoamnios'] = 1;
            }else{
                $this->data['MidWifeBirthDetail']['oligoamnios'] = 0;
            }
            if (!empty($this->data['MidWifeService']['whitish'])) {
                $this->data['MidWifeBirthDetail']['whitish'] = 1;
            }else{
                $this->data['MidWifeBirthDetail']['whitish'] = 0;
            }
            if (!empty($this->data['MidWifeService']['clear'])) {
                $this->data['MidWifeBirthDetail']['clear'] = 1;
            }else{
                $this->data['MidWifeBirthDetail']['clear'] = 0;
            }
            if (!empty($this->data['MidWifeService']['greenish'])) {
                $this->data['MidWifeBirthDetail']['greenish'] = 1;
            }else{
                $this->data['MidWifeBirthDetail']['greenish'] = 0;
            }
            $this->data['MidWifeBirthDetail']['mid_wife_dossier_medical_id'] = $this->data['MidWifeService']['mid_wife_dossier_medical_id'];
            $this->data['MidWifeBirthDetail']['created_by']=$user['User']['id'];
            if ($this->MidWifeBirthDetail->save($this->data['MidWifeBirthDetail'])) {
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
            }
        }
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'MidWifeDossierMedical.is_active' => 1, 'MidWifeDossierMedical.id' => $id),
            'fields' => array('Queue.id, QeuedDoctor.id, MidWifeServiceRequest.*,MidWifeDossierMedical.*,Patient.*'),
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
                array('table' => 'other_service_requests',
                    'alias' => 'OtherServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'OtherServiceRequest.queued_doctor_id = QeuedDoctor.id'
                    )
                ),
                array('table' => 'mid_wife_service_requests',
                    'alias' => 'MidWifeServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeServiceRequest.other_service_request_id = OtherServiceRequest.id'
                    )
                ),
                array('table' => 'mid_wife_dossier_medicals',
                    'alias' => 'MidWifeDossierMedical',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeDossierMedical.queue_id = Queue.id'
                    )
                )
            )));
        $this->set(compact('patient'));
    }
    
    // function edit data tacking in dossier medical
    function editTracking($id=null) {
        $this->layout = 'ajax';
        $this->loadModel('Queue');
        $this->loadModel('MidWifeDossierMedical');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('MidWifeServiceRequest');
        $this->loadModel('MidWifeBirth');
        $this->loadModel('MidWifeBirthDetail');
        if (!empty($this->data)) {
            $user = $this->getCurrentUser();
            $this->MidWifeBirth->updateAll(
                        array('MidWifeBirth.is_active' => "2", 'MidWifeBirth.modified_by' => $user['User']['id']), array('MidWifeBirth.mid_wife_dossier_medical_id' => $this->data['MidWifeService']['mid_wife_dossier_medical_id'])
                );
            for ($i = 0; $i < sizeof($_POST['time']); $i++) {
                if ($_POST['time'][$i] != '') {
                    $this->MidWifeBirth->create();
                    $Birth['MidWifeBirth']['time'] = $_POST['time'][$i];
                    $Birth['MidWifeBirth']['bcf'] = $_POST['bcf'][$i];
                    $Birth['MidWifeBirth']['pdf'] = $_POST['pdf'][$i];
                    $Birth['MidWifeBirth']['col'] = $_POST['col'][$i];
                    $Birth['MidWifeBirth']['ta'] = $_POST['ta'][$i];
                    $Birth['MidWifeBirth']['pouls'] = $_POST['pouls'][$i];
                    $Birth['MidWifeBirth']['temperature'] = $_POST['temperature'][$i];
                    $Birth['MidWifeBirth']['created_by'] = $user['User']['id'];
                    $Birth['MidWifeBirth']['mid_wife_dossier_medical_id'] = $this->data['MidWifeService']['mid_wife_dossier_medical_id'];
                    $this->MidWifeBirth->save($Birth);
                }
            }
                $this->MidWifeBirthDetail->updateAll(
                        array('MidWifeBirthDetail.is_active' => "3", 'MidWifeBirthDetail.modified_by' => $user['User']['id']), array('MidWifeBirthDetail.id' => $this->data['MidWifeService']['mid_wife_birth_detail_id'])
                );
            $this->MidWifeBirthDetail->create();
            if (!empty($this->data['MidWifeService']['hydramnios'])) {
                $this->data['MidWifeBirthDetail']['hydramnios'] = 1;
            }else{
                $this->data['MidWifeBirthDetail']['hydramnios'] = 0;
            }
            if (!empty($this->data['MidWifeService']['excess'])) {
                $this->data['MidWifeBirthDetail']['excess'] = 1;
            }else{
                $this->data['MidWifeBirthDetail']['excess'] = 0;
            }
            if (!empty($this->data['MidWifeService']['normal'])) {
                $this->data['MidWifeBirthDetail']['normal'] = 1;
            }else{
                $this->data['MidWifeBirthDetail']['normal'] = 0;
            }
            if (!empty($this->data['MidWifeService']['oligoamnios'])) {
                $this->data['MidWifeBirthDetail']['oligoamnios'] = 1;
            }else{
                $this->data['MidWifeBirthDetail']['oligoamnios'] = 0;
            }
            if (!empty($this->data['MidWifeService']['whitish'])) {
                $this->data['MidWifeBirthDetail']['whitish'] = 1;
            }else{
                $this->data['MidWifeBirthDetail']['whitish'] = 0;
            }
            if (!empty($this->data['MidWifeService']['clear'])) {
                $this->data['MidWifeBirthDetail']['clear'] = 1;
            }else{
                $this->data['MidWifeBirthDetail']['clear'] = 0;
            }
            if (!empty($this->data['MidWifeService']['greenish'])) {
                $this->data['MidWifeBirthDetail']['greenish'] = 1;
            }else{
                $this->data['MidWifeBirthDetail']['greenish'] = 0;
            }
            $this->data['MidWifeBirthDetail']['mid_wife_dossier_medical_id'] = $this->data['MidWifeService']['mid_wife_dossier_medical_id'];
            $this->data['MidWifeBirthDetail']['created_by']=$user['User']['id'];
            if ($this->MidWifeBirthDetail->save($this->data['MidWifeBirthDetail'])) {
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
            }
        }
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'MidWifeDossierMedical.is_active' => 1,'MidWifeBirthDetail.is_active' => 1, 'MidWifeDossierMedical.id' => $id),
            'fields' => array('Queue.id, QeuedDoctor.id, MidWifeServiceRequest.*,MidWifeDossierMedical.*,Patient.*','MidWifeBirth.*','MidWifeBirthDetail.*'),
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
                array('table' => 'other_service_requests',
                    'alias' => 'OtherServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'OtherServiceRequest.queued_doctor_id = QeuedDoctor.id'
                    )
                ),
                array('table' => 'mid_wife_service_requests',
                    'alias' => 'MidWifeServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeServiceRequest.other_service_request_id = OtherServiceRequest.id'
                    )
                ),
                array('table' => 'mid_wife_dossier_medicals',
                    'alias' => 'MidWifeDossierMedical',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeDossierMedical.queue_id = Queue.id'
                    )
                ),
                array('table' => 'mid_wife_births',
                    'alias' => 'MidWifeBirth',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeBirth.mid_wife_dossier_medical_id = MidWifeDossierMedical.id'
                    )
                ),
                array('table' => 'mid_wife_birth_details',
                    'alias' => 'MidWifeBirthDetail',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeBirthDetail.mid_wife_dossier_medical_id = MidWifeDossierMedical.id'
                    )
                )
            )));
        $this->set(compact('patient'));
    }
    
    // function add new data tacking in dossier medical
    function addNewAccouchement($id=null) {
        $this->layout = 'ajax';
        $this->loadModel('Queue');
        $this->loadModel('MidWifeDossierMedical');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('MidWifeServiceRequest');
        $this->loadModel('MidWifeAccouchement');
        if (!empty($this->data)) {
            $user = $this->getCurrentUser();
            $this->MidWifeAccouchement->create();
            $this->data['MidWifeAccouchement']['mid_wife_dossier_medical_id'] = $this->data['MidWifeService']['mid_wife_dossier_medical_id'];
            if(!empty($this->data['MidWifeService']['acconchem_rerme'])){
                $this->data['MidWifeAccouchement']['acconchem_rerme'] = $this->data['MidWifeService']['acconchem_rerme'];
            }else{
                $this->data['MidWifeAccouchement']['acconchem_rerme'] = 0;
            }
            if(!empty($this->data['MidWifeService']['accon_chement'])){
                $this->data['MidWifeAccouchement']['accon_chement'] = $this->data['MidWifeService']['accon_chement'];
            }else{
                $this->data['MidWifeAccouchement']['accon_chement'] = 0;
            }
            if(!empty($this->data['MidWifeService']['anormat'])){
                $this->data['MidWifeAccouchement']['anormat'] = $this->data['MidWifeService']['anormat'];
            }else{
                $this->data['MidWifeAccouchement']['anormat'] = 0;
            }
            if(!empty($this->data['MidWifeService']['acc_par_ventonse'])){
                $this->data['MidWifeAccouchement']['acc_par_ventonse'] = $this->data['MidWifeService']['acc_par_ventonse'];
            }else{
                $this->data['MidWifeAccouchement']['acc_par_ventonse'] = 0;
            }
            if(!empty($this->data['MidWifeService']['caesarean'])){
                $this->data['MidWifeAccouchement']['caesarean'] = $this->data['MidWifeService']['caesarean'];
            }else{
                $this->data['MidWifeAccouchement']['caesarean'] = 0;
            }
            if(!empty($this->data['MidWifeService']['girl'])){
                $this->data['MidWifeAccouchement']['girl'] = $this->data['MidWifeService']['girl'];
            }else{
                $this->data['MidWifeAccouchement']['girl'] = 0;
            }
            if(!empty($this->data['MidWifeService']['boy'])){
                $this->data['MidWifeAccouchement']['boy'] = $this->data['MidWifeService']['boy'];
            }else{
                $this->data['MidWifeAccouchement']['boy'] = 0;
            }
            if(!empty($this->data['MidWifeService']['good'])){
                $this->data['MidWifeAccouchement']['good'] = $this->data['MidWifeService']['good'];
            }else{
                $this->data['MidWifeAccouchement']['good'] = 0;
            }
            if(!empty($this->data['MidWifeService']['not_good'])){
                $this->data['MidWifeAccouchement']['not_good'] = $this->data['MidWifeService']['not_good'];
            }else{
                $this->data['MidWifeAccouchement']['not_good'] = 0;
            }
            if(!empty($this->data['MidWifeService']['title'])){
                $this->data['MidWifeAccouchement']['little'] = $this->data['MidWifeService']['title'];
            }else{
                $this->data['MidWifeAccouchement']['little'] = 0;
            }
            if(!empty($this->data['MidWifeService']['much'])){
                $this->data['MidWifeAccouchement']['much'] = $this->data['MidWifeService']['much'];
            }else{
                $this->data['MidWifeAccouchement']['much'] = 0;
            }
            $this->data['MidWifeAccouchement']['g'] = $this->data['MidWifeService']['g'];
            $this->data['MidWifeAccouchement']['time1'] = $this->data['MidWifeService']['time1'];
            $this->data['MidWifeAccouchement']['dob'] = $this->data['MidWifeService']['dob'];
            $this->data['MidWifeAccouchement']['weight'] = $this->data['MidWifeService']['weight'];
            $this->data['MidWifeAccouchement']['long'] = $this->data['MidWifeService']['long'];
            $this->data['MidWifeAccouchement']['head_size'] = $this->data['MidWifeService']['head_size'];
            $this->data['MidWifeAccouchement']['one_minute'] = $this->data['MidWifeService']['one_minute'];
            $this->data['MidWifeAccouchement']['five_minute'] = $this->data['MidWifeService']['five_minute'];
            $this->data['MidWifeAccouchement']['ten_minute'] = $this->data['MidWifeService']['ten_minute'];
            $this->data['MidWifeAccouchement']['created_by']=$user['User']['id'];
            if ($this->MidWifeAccouchement->save($this->data['MidWifeAccouchement'])) {
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
            }
        }
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'MidWifeDossierMedical.is_active' => 1, 'MidWifeDossierMedical.id' => $id),
            'fields' => array('Queue.id, QeuedDoctor.id, MidWifeServiceRequest.*,MidWifeDossierMedical.*,Patient.*'),
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
                array('table' => 'other_service_requests',
                    'alias' => 'OtherServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'OtherServiceRequest.queued_doctor_id = QeuedDoctor.id'
                    )
                ),
                array('table' => 'mid_wife_service_requests',
                    'alias' => 'MidWifeServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeServiceRequest.other_service_request_id = OtherServiceRequest.id'
                    )
                ),
                array('table' => 'mid_wife_dossier_medicals',
                    'alias' => 'MidWifeDossierMedical',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeDossierMedical.queue_id = Queue.id'
                    )
                )
            )));
        $this->set(compact('patient'));
    }
    
    // function edit data tacking in dossier medical
    function editAccouchement($id=null) {
        $this->layout = 'ajax';
        $this->loadModel('Queue');
        $this->loadModel('MidWifeDossierMedical');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('MidWifeServiceRequest');
        $this->loadModel('MidWifeAccouchement');
        if (!empty($this->data)) {
            $user = $this->getCurrentUser();
            $this->MidWifeAccouchement->updateAll(
                        array('MidWifeAccouchement.is_active' => "2", 'MidWifeAccouchement.modified_by' => $user['User']['id']), array('MidWifeAccouchement.id' => $this->data['MidWifeService']['accouchement_id'])
                );
            $this->MidWifeAccouchement->create();
            $this->data['MidWifeAccouchement']['mid_wife_dossier_medical_id'] = $this->data['MidWifeService']['mid_wife_dossier_medical_id'];
            if(!empty($this->data['MidWifeService']['acconchem_rerme'])){
                $this->data['MidWifeAccouchement']['acconchem_rerme'] = $this->data['MidWifeService']['acconchem_rerme'];
            }else{
                $this->data['MidWifeAccouchement']['acconchem_rerme'] = 0;
            }
            if(!empty($this->data['MidWifeService']['accon_chement'])){
                $this->data['MidWifeAccouchement']['accon_chement'] = $this->data['MidWifeService']['accon_chement'];
            }else{
                $this->data['MidWifeAccouchement']['accon_chement'] = 0;
            }
            if(!empty($this->data['MidWifeService']['anormat'])){
                $this->data['MidWifeAccouchement']['anormat'] = $this->data['MidWifeService']['anormat'];
            }else{
                $this->data['MidWifeAccouchement']['anormat'] = 0;
            }
            if(!empty($this->data['MidWifeService']['acc_par_ventonse'])){
                $this->data['MidWifeAccouchement']['acc_par_ventonse'] = $this->data['MidWifeService']['acc_par_ventonse'];
            }else{
                $this->data['MidWifeAccouchement']['acc_par_ventonse'] = 0;
            }
            if(!empty($this->data['MidWifeService']['caesarean'])){
                $this->data['MidWifeAccouchement']['caesarean'] = $this->data['MidWifeService']['caesarean'];
            }else{
                $this->data['MidWifeAccouchement']['caesarean'] = 0;
            }
            if(!empty($this->data['MidWifeService']['girl'])){
                $this->data['MidWifeAccouchement']['girl'] = $this->data['MidWifeService']['girl'];
            }else{
                $this->data['MidWifeAccouchement']['girl'] = 0;
            }
            if(!empty($this->data['MidWifeService']['boy'])){
                $this->data['MidWifeAccouchement']['boy'] = $this->data['MidWifeService']['boy'];
            }else{
                $this->data['MidWifeAccouchement']['boy'] = 0;
            }
            if(!empty($this->data['MidWifeService']['good'])){
                $this->data['MidWifeAccouchement']['good'] = $this->data['MidWifeService']['good'];
            }else{
                $this->data['MidWifeAccouchement']['good'] = 0;
            }
            if(!empty($this->data['MidWifeService']['not_good'])){
                $this->data['MidWifeAccouchement']['not_good'] = $this->data['MidWifeService']['not_good'];
            }else{
                $this->data['MidWifeAccouchement']['not_good'] = 0;
            }
            if(!empty($this->data['MidWifeService']['little'])){
                $this->data['MidWifeAccouchement']['little'] = $this->data['MidWifeService']['little'];
            }else{
                $this->data['MidWifeAccouchement']['little'] = 0;
            }
            if(!empty($this->data['MidWifeService']['much'])){
                $this->data['MidWifeAccouchement']['much'] = $this->data['MidWifeService']['much'];
            }else{
                $this->data['MidWifeAccouchement']['much'] = 0;
            }
            $this->data['MidWifeAccouchement']['g'] = $this->data['MidWifeService']['g'];
            $this->data['MidWifeAccouchement']['time1'] = $this->data['MidWifeService']['time1'];
            $this->data['MidWifeAccouchement']['dob'] = $this->data['MidWifeService']['dob'];
            $this->data['MidWifeAccouchement']['weight'] = $this->data['MidWifeService']['weight'];
            $this->data['MidWifeAccouchement']['long'] = $this->data['MidWifeService']['long'];
            $this->data['MidWifeAccouchement']['head_size'] = $this->data['MidWifeService']['head_size'];
            $this->data['MidWifeAccouchement']['one_minute'] = $this->data['MidWifeService']['one_minute'];
            $this->data['MidWifeAccouchement']['five_minute'] = $this->data['MidWifeService']['five_minute'];
            $this->data['MidWifeAccouchement']['ten_minute'] = $this->data['MidWifeService']['ten_minute'];
            $this->data['MidWifeAccouchement']['created_by']=$user['User']['id'];
            if ($this->MidWifeAccouchement->save($this->data['MidWifeAccouchement'])) {
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
            }
        }
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'MidWifeDossierMedical.is_active' => 1,'MidWifeAccouchement.is_active' => 1, 'MidWifeAccouchement.id' => $id),
            'fields' => array('Queue.id, QeuedDoctor.id, MidWifeServiceRequest.*,MidWifeDossierMedical.*,Patient.*','MidWifeAccouchement.*'),
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
                array('table' => 'other_service_requests',
                    'alias' => 'OtherServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'OtherServiceRequest.queued_doctor_id = QeuedDoctor.id'
                    )
                ),
                array('table' => 'mid_wife_service_requests',
                    'alias' => 'MidWifeServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeServiceRequest.other_service_request_id = OtherServiceRequest.id'
                    )
                ),
                array('table' => 'mid_wife_dossier_medicals',
                    'alias' => 'MidWifeDossierMedical',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeDossierMedical.queue_id = Queue.id'
                    )
                ),
                array('table' => 'mid_wife_accouchements',
                    'alias' => 'MidWifeAccouchement',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeAccouchement.mid_wife_dossier_medical_id = MidWifeDossierMedical.id'
                    )
                )
            )));
        $this->set(compact('patient'));
    }
    
    // function add new data Deliverance in dossier medical
    function addNewDeliverance($id=null) {
        $this->layout = 'ajax';
        $this->loadModel('Queue');
        $this->loadModel('MidWifeDossierMedical');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('MidWifeServiceRequest');
        $this->loadModel('MidWifeDeliverance');
        if (!empty($this->data)) {
            $user = $this->getCurrentUser();
            $this->MidWifeDeliverance->create();
            $this->data['MidWifeDeliverance']['mid_wife_dossier_medical_id'] = $this->data['MidWifeService']['mid_wife_dossier_medical_id'];
            $this->data['MidWifeDeliverance']['time'] = $this->data['MidWifeService']['time'];
            $this->data['MidWifeDeliverance']['weight'] = $this->data['MidWifeService']['weight'];
            if(!empty($this->data['MidWifeService']['beaudelauque'])){
                $this->data['MidWifeDeliverance']['beaudelauque'] = $this->data['MidWifeService']['beaudelauque'];
            }else{
                $this->data['MidWifeDeliverance']['beaudelauque'] = 0;
            }
            if(!empty($this->data['MidWifeService']['duncan'])){
                $this->data['MidWifeDeliverance']['duncan'] = $this->data['MidWifeService']['duncan'];
            }else{
                $this->data['MidWifeDeliverance']['duncan'] = 0;
            }
            if(!empty($this->data['MidWifeService']['check'])){
                $this->data['MidWifeDeliverance']['check'] = $this->data['MidWifeService']['check'];
            }else{
                $this->data['MidWifeDeliverance']['check'] = 0;
            }
            if(!empty($this->data['MidWifeService']['natural'])){
                $this->data['MidWifeDeliverance']['natural'] = $this->data['MidWifeService']['natural'];
            }else{
                $this->data['MidWifeDeliverance']['natural'] = 0;
            }
            if(!empty($this->data['MidWifeService']['by_hand'])){
                $this->data['MidWifeDeliverance']['by_hand'] = $this->data['MidWifeService']['by_hand'];
            }else{
                $this->data['MidWifeDeliverance']['by_hand'] = 0;
            }
            if(!empty($this->data['MidWifeService']['have'])){
                $this->data['MidWifeDeliverance']['have'] = $this->data['MidWifeService']['have'];
            }else{
                $this->data['MidWifeDeliverance']['have'] = 0;
            }
            if(!empty($this->data['MidWifeService']['no_have'])){
                $this->data['MidWifeDeliverance']['no_have'] = $this->data['MidWifeService']['no_have'];
            }else{
                $this->data['MidWifeDeliverance']['no_have'] = 0;
            }
            $this->data['MidWifeDeliverance']['created_by']=$user['User']['id'];
            if ($this->MidWifeDeliverance->save($this->data['MidWifeDeliverance'])) {
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
            }
        }
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'MidWifeDossierMedical.is_active' => 1, 'MidWifeDossierMedical.id' => $id),
            'fields' => array('Queue.id, QeuedDoctor.id, MidWifeServiceRequest.*,MidWifeDossierMedical.*,Patient.*'),
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
                array('table' => 'other_service_requests',
                    'alias' => 'OtherServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'OtherServiceRequest.queued_doctor_id = QeuedDoctor.id'
                    )
                ),
                array('table' => 'mid_wife_service_requests',
                    'alias' => 'MidWifeServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeServiceRequest.other_service_request_id = OtherServiceRequest.id'
                    )
                ),
                array('table' => 'mid_wife_dossier_medicals',
                    'alias' => 'MidWifeDossierMedical',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeDossierMedical.queue_id = Queue.id'
                    )
                )
            )));
        $this->set(compact('patient'));
    }
    
    // function edit data Deliverance in dossier medical
    function editDeliverance($id=null) {
        $this->layout = 'ajax';
        $this->loadModel('Queue');
        $this->loadModel('MidWifeDossierMedical');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('MidWifeServiceRequest');
        $this->loadModel('MidWifeDeliverance');
        if (!empty($this->data)) {
            $user = $this->getCurrentUser();
            $this->MidWifeDeliverance->updateAll(
                        array('MidWifeDeliverance.is_active' => "2", 'MidWifeDeliverance.modified_by' => $user['User']['id']), array('MidWifeDeliverance.id' => $this->data['MidWifeService']['deliverance_id'])
                );
            $this->MidWifeDeliverance->create();
            $this->data['MidWifeDeliverance']['mid_wife_dossier_medical_id'] = $this->data['MidWifeService']['mid_wife_dossier_medical_id'];
            $this->data['MidWifeDeliverance']['time'] = $this->data['MidWifeService']['time'];
            $this->data['MidWifeDeliverance']['weight'] = $this->data['MidWifeService']['weight'];
            if(!empty($this->data['MidWifeService']['beaudelauque'])){
                $this->data['MidWifeDeliverance']['beaudelauque'] = $this->data['MidWifeService']['beaudelauque'];
            }else{
                $this->data['MidWifeDeliverance']['beaudelauque'] = 0;
            }
            if(!empty($this->data['MidWifeService']['duncan'])){
                $this->data['MidWifeDeliverance']['duncan'] = $this->data['MidWifeService']['duncan'];
            }else{
                $this->data['MidWifeDeliverance']['duncan'] = 0;
            }
            if(!empty($this->data['MidWifeService']['check'])){
                $this->data['MidWifeDeliverance']['check'] = $this->data['MidWifeService']['check'];
            }else{
                $this->data['MidWifeDeliverance']['check'] = 0;
            }
            if(!empty($this->data['MidWifeService']['natural'])){
                $this->data['MidWifeDeliverance']['natural'] = $this->data['MidWifeService']['natural'];
            }else{
                $this->data['MidWifeDeliverance']['natural'] = 0;
            }
            if(!empty($this->data['MidWifeService']['by_hand'])){
                $this->data['MidWifeDeliverance']['by_hand'] = $this->data['MidWifeService']['by_hand'];
            }else{
                $this->data['MidWifeDeliverance']['by_hand'] = 0;
            }
            if(!empty($this->data['MidWifeService']['have'])){
                $this->data['MidWifeDeliverance']['have'] = $this->data['MidWifeService']['have'];
            }else{
                $this->data['MidWifeDeliverance']['have'] = 0;
            }
            if(!empty($this->data['MidWifeService']['no_have'])){
                $this->data['MidWifeDeliverance']['no_have'] = $this->data['MidWifeService']['no_have'];
            }else{
                $this->data['MidWifeDeliverance']['no_have'] = 0;
            }
            $this->data['MidWifeDeliverance']['created_by']=$user['User']['id'];
            if ($this->MidWifeDeliverance->save($this->data['MidWifeDeliverance'])) {
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
            }
        }
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'MidWifeDossierMedical.is_active' => 1,'MidWifeDeliverance.is_active' => 1, 'MidWifeDeliverance.id' => $id),
            'fields' => array('Queue.id, QeuedDoctor.id, MidWifeServiceRequest.*,MidWifeDossierMedical.*,Patient.*','MidWifeDeliverance.*'),
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
                array('table' => 'other_service_requests',
                    'alias' => 'OtherServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'OtherServiceRequest.queued_doctor_id = QeuedDoctor.id'
                    )
                ),
                array('table' => 'mid_wife_service_requests',
                    'alias' => 'MidWifeServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeServiceRequest.other_service_request_id = OtherServiceRequest.id'
                    )
                ),
                array('table' => 'mid_wife_dossier_medicals',
                    'alias' => 'MidWifeDossierMedical',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeDossierMedical.queue_id = Queue.id'
                    )
                ),
                array('table' => 'mid_wife_deliverances',
                    'alias' => 'MidWifeDeliverance',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeDeliverance.mid_wife_dossier_medical_id = MidWifeDossierMedical.id'
                    )
                )
            )));
        $this->set(compact('patient'));
    }
    
    // function add new data first time in dossier medical
    function addNewAccouchementFirstTime($id=null) {
        $this->layout = 'ajax';
        $this->loadModel('Queue');
        $this->loadModel('MidWifeDossierMedical');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('MidWifeServiceRequest');
        $this->loadModel('MidWifeAccouchementFirstTime');
        if (!empty($this->data)) {
            $user = $this->getCurrentUser();
            for ($i = 0; $i < sizeof($_POST['time']); $i++) {
                if ($_POST['time'][$i] != '') {
                    $this->MidWifeAccouchementFirstTime->create();
                    $first_accouchement['MidWifeAccouchementFirstTime']['time'] = $_POST['time'][$i];
                    $first_accouchement['MidWifeAccouchementFirstTime']['first_blood'] = $_POST['first_blood'][$i];
                    $first_accouchement['MidWifeAccouchementFirstTime']['first_ta'] = $_POST['first_ta'][$i];
                    $first_accouchement['MidWifeAccouchementFirstTime']['first_p'] = $_POST['first_p'][$i];
                    $first_accouchement['MidWifeAccouchementFirstTime']['first_temperature'] = $_POST['first_temperature'][$i];
                    $first_accouchement['MidWifeAccouchementFirstTime']['created_by'] = $user['User']['id'];
                    $first_accouchement['MidWifeAccouchementFirstTime']['mid_wife_dossier_medical_id'] = $this->data['MidWifeService']['mid_wife_dossier_medical_id'];
                    $this->MidWifeAccouchementFirstTime->save($first_accouchement);
                }
            }
            echo MESSAGE_DATA_HAS_BEEN_SAVED;
                exit;
        }
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'MidWifeDossierMedical.is_active' => 1, 'MidWifeDossierMedical.id' => $id),
            'fields' => array('Queue.id, QeuedDoctor.id, MidWifeServiceRequest.*,MidWifeDossierMedical.*,Patient.*'),
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
                array('table' => 'other_service_requests',
                    'alias' => 'OtherServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'OtherServiceRequest.queued_doctor_id = QeuedDoctor.id'
                    )
                ),
                array('table' => 'mid_wife_service_requests',
                    'alias' => 'MidWifeServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeServiceRequest.other_service_request_id = OtherServiceRequest.id'
                    )
                ),
                array('table' => 'mid_wife_dossier_medicals',
                    'alias' => 'MidWifeDossierMedical',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeDossierMedical.queue_id = Queue.id'
                    )
                )
            )));
        $this->set(compact('patient'));
    }
    
    // function edit data first time in dossier medical
    function editAccouchementFirstTime($id=null) {
        $this->layout = 'ajax';
        $this->loadModel('Queue');
        $this->loadModel('MidWifeDossierMedical');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('MidWifeServiceRequest');
        $this->loadModel('MidWifeAccouchementFirstTime');
        if (!empty($this->data)) {
            $user = $this->getCurrentUser();
            $this->MidWifeAccouchementFirstTime->updateAll(
                        array('MidWifeAccouchementFirstTime.is_active' => "2", 'MidWifeAccouchementFirstTime.modified_by' => $user['User']['id']), array('MidWifeAccouchementFirstTime.mid_wife_dossier_medical_id' => $this->data['MidWifeDossierMedical']['id'])
                );
            for ($i = 0; $i < sizeof($_POST['time']); $i++) {
                if ($_POST['time'][$i] != '') {
                    $this->MidWifeAccouchementFirstTime->create();
                    $first_accouchement['MidWifeAccouchementFirstTime']['time'] = $_POST['time'][$i];
                    $first_accouchement['MidWifeAccouchementFirstTime']['first_blood'] = $_POST['first_blood'][$i];
                    $first_accouchement['MidWifeAccouchementFirstTime']['first_ta'] = $_POST['first_ta'][$i];
                    $first_accouchement['MidWifeAccouchementFirstTime']['first_p'] = $_POST['first_p'][$i];
                    $first_accouchement['MidWifeAccouchementFirstTime']['first_temperature'] = $_POST['first_temperature'][$i];
                    $first_accouchement['MidWifeAccouchementFirstTime']['created_by'] = $user['User']['id'];
                    $first_accouchement['MidWifeAccouchementFirstTime']['mid_wife_dossier_medical_id'] = $this->data['MidWifeDossierMedical']['id'];
                    $this->MidWifeAccouchementFirstTime->save($first_accouchement);
                }
            }
            echo MESSAGE_DATA_HAS_BEEN_SAVED;
                exit;
        }
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'MidWifeDossierMedical.is_active' => 1,'MidWifeAccouchementFirstTime.is_active'=>1, 'MidWifeDossierMedical.id' => $id),
            'fields' => array('Queue.id, QeuedDoctor.id, MidWifeServiceRequest.*,MidWifeDossierMedical.*,Patient.*','MidWifeAccouchementFirstTime.*'),
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
                array('table' => 'other_service_requests',
                    'alias' => 'OtherServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'OtherServiceRequest.queued_doctor_id = QeuedDoctor.id'
                    )
                ),
                array('table' => 'mid_wife_service_requests',
                    'alias' => 'MidWifeServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeServiceRequest.other_service_request_id = OtherServiceRequest.id'
                    )
                ),
                array('table' => 'mid_wife_dossier_medicals',
                    'alias' => 'MidWifeDossierMedical',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeDossierMedical.queue_id = Queue.id'
                    )
                ),
                array('table' => 'mid_wife_accouchement_first_times',
                    'alias' => 'MidWifeAccouchementFirstTime',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeAccouchementFirstTime.mid_wife_dossier_medical_id = MidWifeDossierMedical.id'
                    )
                )
            )));
        $this->set(compact('patient'));
    }
    
    // function add new data next time in dossier medical
    function addNewAccouchementNextTime($id=null) {
        $this->layout = 'ajax';
        $this->loadModel('Queue');
        $this->loadModel('MidWifeDossierMedical');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('MidWifeServiceRequest');
        $this->loadModel('MidWifeAccouchementNextTime');
        $this->loadModel('MidWifeAllaitement');
        if (!empty($this->data)) {
            $user = $this->getCurrentUser();
            for ($i = 0; $i < sizeof($_POST['next_time']); $i++) {
                if ($_POST['next_time'][$i] != '') {
                    $this->MidWifeAccouchementNextTime->create();
                    $next_accouchement['MidWifeAccouchementNextTime']['next_time'] = $_POST['next_time'][$i];
                    $next_accouchement['MidWifeAccouchementNextTime']['next_blood'] = $_POST['next_blood'][$i];
                    $next_accouchement['MidWifeAccouchementNextTime']['next_ta'] = $_POST['next_ta'][$i];
                    $next_accouchement['MidWifeAccouchementNextTime']['next_p'] = $_POST['next_p'][$i];
                    $next_accouchement['MidWifeAccouchementNextTime']['next_temperature'] = $_POST['next_temperature'][$i];
                    $next_accouchement['MidWifeAccouchementNextTime']['created_by'] = $user['User']['id'];
                    $next_accouchement['MidWifeAccouchementNextTime']['mid_wife_dossier_medical_id'] = $this->data['MidWifeService']['mid_wife_dossier_medical_id'];
                    $this->MidWifeAccouchementNextTime->save($next_accouchement);
                }
            }
            $allaitement = $this->MidWifeAllaitement->find('all', array('conditions' => array('is_active'=>1,'MidWifeAllaitement.mid_wife_dossier_medical_id' => $this->data['MidWifeService']['mid_wife_dossier_medical_id'])));
            if(!empty($allaitement['0']['MidWifeAllaitement']['id'])){
                $allaitement_id = $allaitement['0']['MidWifeAllaitement']['id'];
                $this->MidWifeAllaitement->updateAll(
                        array('MidWifeAllaitement.is_active' => 2, 'MidWifeAllaitement.modified_by' => $user['User']['id']), array('MidWifeAllaitement.id' => $allaitement_id)
                );
            }
            $this->MidWifeAllaitement->create();
            $this->data['MidWifeAllaitement']['mid_wife_dossier_medical_id']=$this->data['MidWifeService']['mid_wife_dossier_medical_id'];
            if(!empty($this->data['MidWifeService']['soon'])){
                $this->data['MidWifeAllaitement']['soon']=$this->data['MidWifeService']['soon'];
            }else{
                $this->data['MidWifeAllaitement']['soon']=0;
            }
            if(!empty($this->data['MidWifeService']['two_houre_after'])){
                $this->data['MidWifeAllaitement']['two_houre_after']=$this->data['MidWifeService']['two_houre_after'];
            }else{
                $this->data['MidWifeAllaitement']['two_houre_after']=0;
            }
            $this->data['MidWifeAllaitement']['created_by'] = $user['User']['id'];
            if ($this->MidWifeAllaitement->save($this->data['MidWifeAllaitement'])) {
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
            }
        }
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'MidWifeDossierMedical.is_active' => 1, 'MidWifeDossierMedical.id' => $id),
            'fields' => array('Queue.id, QeuedDoctor.id, MidWifeServiceRequest.*,MidWifeDossierMedical.*,Patient.*'),
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
                array('table' => 'other_service_requests',
                    'alias' => 'OtherServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'OtherServiceRequest.queued_doctor_id = QeuedDoctor.id'
                    )
                ),
                array('table' => 'mid_wife_service_requests',
                    'alias' => 'MidWifeServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeServiceRequest.other_service_request_id = OtherServiceRequest.id'
                    )
                ),
                array('table' => 'mid_wife_dossier_medicals',
                    'alias' => 'MidWifeDossierMedical',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeDossierMedical.queue_id = Queue.id'
                    )
                )
            )));
        $this->set(compact('patient'));
    }
    
    // function edit data next time in dossier medical
    function editAccouchementNextTime($id=null) {
        $this->layout = 'ajax';
        $this->loadModel('Queue');
        $this->loadModel('MidWifeDossierMedical');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('MidWifeServiceRequest');
        $this->loadModel('MidWifeAccouchementNextTime');
        $this->loadModel('MidWifeAllaitement');
        if (!empty($this->data)) {
            $user = $this->getCurrentUser();
            $this->MidWifeAccouchementNextTime->updateAll(
                        array('MidWifeAccouchementNextTime.is_active' => "2", 'MidWifeAccouchementNextTime.modified_by' => $user['User']['id']), array('MidWifeAccouchementNextTime.mid_wife_dossier_medical_id' => $this->data['MidWifeService']['mid_wife_dossier_medical_id'])
                );
            for ($i = 0; $i < sizeof($_POST['next_time']); $i++) {
                if ($_POST['next_time'][$i] != '') {
                    $this->MidWifeAccouchementNextTime->create();
                    $next_accouchement['MidWifeAccouchementNextTime']['next_time'] = $_POST['next_time'][$i];
                    $next_accouchement['MidWifeAccouchementNextTime']['next_blood'] = $_POST['next_blood'][$i];
                    $next_accouchement['MidWifeAccouchementNextTime']['next_ta'] = $_POST['next_ta'][$i];
                    $next_accouchement['MidWifeAccouchementNextTime']['next_p'] = $_POST['next_p'][$i];
                    $next_accouchement['MidWifeAccouchementNextTime']['next_temperature'] = $_POST['next_temperature'][$i];
                    $next_accouchement['MidWifeAccouchementNextTime']['created_by'] = $user['User']['id'];
                    $next_accouchement['MidWifeAccouchementNextTime']['mid_wife_dossier_medical_id'] = $this->data['MidWifeService']['mid_wife_dossier_medical_id'];
                    $this->MidWifeAccouchementNextTime->save($next_accouchement);
                }
            }
            $this->MidWifeAllaitement->updateAll(
                        array('MidWifeAllaitement.is_active' => "3", 'MidWifeAllaitement.modified_by' => $user['User']['id']), array('MidWifeAllaitement.id' => $this->data['MidWifeService']['allaitement_id'])
                );
            $this->MidWifeAllaitement->create();
            $data['MidWifeAllaitement']['mid_wife_dossier_medical_id']=$this->data['MidWifeService']['mid_wife_dossier_medical_id'];
            if(!empty($this->data['MidWifeService']['soon'])){
                $data['MidWifeAllaitement']['soon']=$this->data['MidWifeService']['soon'];
            }else{
                $data['MidWifeAllaitement']['soon']=0;
            }
            if(!empty($this->data['MidWifeService']['two_houre_after'])){
                $data['MidWifeAllaitement']['two_houre_after']=$this->data['MidWifeService']['two_houre_after'];
            }else{
                $data['MidWifeAllaitement']['two_houre_after']=0;
            }
            $data['MidWifeAllaitement']['created_by'] = $user['User']['id'];
            if ($this->MidWifeAllaitement->save($data['MidWifeAllaitement'])) {
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
            }
        }
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'MidWifeDossierMedical.is_active' => 1, 'MidWifeDossierMedical.id' => $id),
            'fields' => array('Queue.id, QeuedDoctor.id, MidWifeServiceRequest.*,MidWifeDossierMedical.*,Patient.*','MidWifeAccouchementNextTime.*','MidWifeAllaitement.*'),
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
                array('table' => 'other_service_requests',
                    'alias' => 'OtherServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'OtherServiceRequest.queued_doctor_id = QeuedDoctor.id'
                    )
                ),
                array('table' => 'mid_wife_service_requests',
                    'alias' => 'MidWifeServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeServiceRequest.other_service_request_id = OtherServiceRequest.id'
                    )
                ),
                array('table' => 'mid_wife_dossier_medicals',
                    'alias' => 'MidWifeDossierMedical',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeDossierMedical.queue_id = Queue.id'
                    )
                ),
                array('table' => 'mid_wife_accouchement_next_times',
                    'alias' => 'MidWifeAccouchementNextTime',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeAccouchementNextTime.mid_wife_dossier_medical_id = MidWifeDossierMedical.id'
                    )
                ),
                array('table' => 'mid_wife_allaitements',
                    'alias' => 'MidWifeAllaitement',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeAllaitement.mid_wife_dossier_medical_id = MidWifeDossierMedical.id'
                    )
                )
            )));
        $this->set(compact('patient'));
    }
}

?>