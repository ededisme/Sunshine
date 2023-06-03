<?php

class EchographiePatientsController extends AppController {

    var $name = 'EchographiePatients';
    var $uses = array('Patient', 'User','EchographiePatient');
    var $components = array('Helper');

    function index() {
        $this->layout = 'ajax';
    }
    
    function  ajax(){
        $this->layout = 'ajax';
    }
    
    function view($id=null){
        $this->layout = 'ajax';      
        $this->loadModel('EchographyInfom');
        $this->loadModel('Indication');
        if (!empty($id)) {
            $dataService = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'EchographiePatient.status' => 1, 'EchographiePatient.id' => $id),
                'fields' => array('Queue.id, EchographiePatient.*, Patient.*','EchographyInfom.*','Indication.name'),
                'joins' => array(                    
                    array('table' => 'queues',
                        'alias' => 'Queue',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Queue.patient_id  = Patient.id'
                        )
                    ),
                    array('table' => 'echographie_patients',
                        'alias' => 'EchographiePatient',
                        'type' => 'INNER',
                        'conditions' => array(
                            'EchographiePatient.queue_id = Queue.id'
                        )
                    ),
                    array('table' => 'echography_infoms',
                        'alias' => 'EchographyInfom',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'EchographyInfom.id = EchographiePatient.echography_infom_id'
                        )
                    ),
                    array('table' => 'indications',
                        'alias' => 'Indication',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Indication.id = EchographiePatient.indication_id'
                        )
                    )
                )));            
            $this->set(compact('dataService'));
        }
    }
    
    function edit($id=null) {
        $this->layout = 'ajax';
        $this->loadModel('QueuedDoctor');
        $this->loadModel('EchographiePatient');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('EchographyInfom');
        $this->loadModel('Indication');
        if (!empty($this->data)) {
            $user = $this->getCurrentUser();
            $this->EchographiePatient->updateAll(
                    array('EchographiePatient.status' => "2", 'EchographiePatient.modified_by' => $user['User']['id']), array('EchographiePatient.id' => $this->data['EchographiePatient']['id'])
            );
            $this->EchographiePatient->create();
            $echographie_id = $this->data['EchographiePatient']['echography_infom_id'];
            $description = $this->data['EchographiePatient']['description'];
            $echoSaveResult['EchographiePatient']['queue_id'] = $this->data['Queue']['id'];
            $echoSaveResult['EchographiePatient']['echography_infom_id'] = $echographie_id;
            $echoSaveResult['EchographiePatient']['doctor_name'] = $this->data['EchographiePatient']['doctor_name'];
            $echoSaveResult['EchographiePatient']['indication_id'] = $this->data['EchographiePatient']['Indication_id'];
            $echoSaveResult['EchographiePatient']['ddr'] = $this->data['EchographiePatient']['ddr'];
            $echoSaveResult['EchographiePatient']['description'] = stripslashes($description);
            $echoSaveResult['EchographiePatient']['form_child'] = $this->data['EchographiePatient']['form_child'];
            $echoSaveResult['EchographiePatient']['num_child'] = $this->data['EchographiePatient']['num_child'];
            $echoSaveResult['EchographiePatient']['healthy_child'] = $this->data['EchographiePatient']['healthy_child'];
            $echoSaveResult['EchographiePatient']['sex_child'] = $this->data['EchographiePatient']['sex_child'];
            $echoSaveResult['EchographiePatient']['teok_plos'] = $this->data['EchographiePatient']['teok_plos'];
            $echoSaveResult['EchographiePatient']['location_sok'] = $this->data['EchographiePatient']['location_sok'];
            $echoSaveResult['EchographiePatient']['weight_child'] = $this->data['EchographiePatient']['weight_child'];
            $echoSaveResult['EchographiePatient']['week_child'] = $this->data['EchographiePatient']['year_child'];
            $echoSaveResult['EchographiePatient']['day_child'] = $this->data['EchographiePatient']['day_child'];
            $echoSaveResult['EchographiePatient']['born_date'] = $this->data['EchographiePatient']['born_date'];
            $echoSaveResult['EchographiePatient']['created_by']=$user['User']['id'];
            if ($this->EchographiePatient->save($echoSaveResult)) {
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
            }
        }
        
        $patient = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'EchographiePatient.status' => 1, 'EchographiePatient.id' => $id),
                'fields' => array('Queue.id, EchographiePatient.*, Patient.*'),
                'joins' => array(                    
                    array('table' => 'queues',
                        'alias' => 'Queue',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Queue.patient_id  = Patient.id'
                        )
                    ),
                    array('table' => 'echographie_patients',
                        'alias' => 'EchographiePatient',
                        'type' => 'INNER',
                        'conditions' => array(
                            'EchographiePatient.queue_id = Queue.id'
                        )
                    )
                )));            
        $this->set(compact('patient'));
        $echographiePatients = $this->EchographiePatient->find("all", array("conditions" => array("EchographiePatient.status=1", "EchographiePatient.id" => $id)));
        $echographies = $this->EchographyInfom->find("all", array("conditions" => array("EchographyInfom.is_active=1", "EchographyInfom.id" => $echographiePatients[0]['EchographiePatient']['echography_infom_id'])));
        $indications = $this->Indication->find("all", array("conditions" => array("Indication.is_active=1")));
        $this->set(compact('echographies', 'indications','echographiePatients'));
    }
    
    function printObstetniquePatient($id = null) {
        $this->layout = 'ajax';        
        if (!empty($id)) {
            $dataService = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'EchographiePatient.status' => 1, 'EchographiePatient.id' => $id),
                'fields' => array('Queue.id, EchographiePatient.*, Patient.*','EchographyInfom.*','Indication.name'),
                'joins' => array(                    
                    array('table' => 'queues',
                        'alias' => 'Queue',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Queue.patient_id  = Patient.id'
                        )
                    ),
                    array('table' => 'echographie_patients',
                        'alias' => 'EchographiePatient',
                        'type' => 'INNER',
                        'conditions' => array(
                            'EchographiePatient.queue_id = Queue.id'
                        )
                    ),
                    array('table' => 'echography_infoms',
                        'alias' => 'EchographyInfom',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'EchographyInfom.id = EchographiePatient.echography_infom_id'
                        )
                    ),
                    array('table' => 'indications',
                        'alias' => 'Indication',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Indication.id = EchographiePatient.indication_id'
                        )
                    )
                )));            
            $this->set(compact('dataService'));
        }
    }
}

?>