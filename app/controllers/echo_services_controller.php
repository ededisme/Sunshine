<?php

class EchoServicesController extends AppController {

    var $name = 'EchoServices';
    var $uses = array('Patient', 'User');
    var $components = array('Helper');

    function index() {
        $this->layout = 'ajax';
    }
    
    function  ajax(){
        $this->layout = 'ajax';
    }
    
    function echoServiceDoctorAjax() {
        $this->layout = 'ajax';
    }
    
    function echoServiceDoctor() {
        
    }
    
    function view($id=null){
        $this->layout = 'ajax';        
        if (!empty($id)) {
            $dataService = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'EchoService.is_active' => 1, 'EchoService.id' => $id),
                'fields' => array('Queue.id, EchoService.*, Patient.*'),
                'joins' => array(                    
                    array('table' => 'queues',
                        'alias' => 'Queue',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Queue.patient_id  = Patient.id'
                        )
                    ),
                    array('table' => 'echo_services',
                        'alias' => 'EchoService',
                        'type' => 'INNER',
                        'conditions' => array(
                            'EchoService.echo_service_queue_id = Queue.id'
                        )
                    )
                )));            
            $this->set(compact('dataService'));
        }
    }
    
    function edit($id=null) {
        $this->layout = 'ajax';
        $this->loadModel('QueuedDoctor');
        $this->loadModel('EchoService');
        $this->loadModel('EchoServiceImage');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('EchoServiceRequest');
        if (!empty($this->data)) {
              
            $user = $this->getCurrentUser();
            $this->EchoService->updateAll(
                    array('EchoService.is_active' => "2", 'EchoService.modified_by' => $user['User']['id']), array('EchoService.id' => $this->data['EchoService']['id'])
            );
            $this->EchoService->create();
            $data['EchoService']['echo_service_queue_id']=$this->data['Queue']['id'];
            $data['EchoService']['description']=$this->data['EchoService']['description'];
            $data['EchoService']['echo_date']=$this->data['EchoService']['echo_date'];
            $data['EchoService']['conclusion']=$this->data['EchoService']['conclusion'];
            $data['EchoService']['created_by']=$user['User']['id'];
            if ($this->EchoService->save($data)) {
                $dataServiceId = $this->EchoService->getLastInsertId();
                if(!empty($_FILES['photos']['name'])){
                        $this->EchoServiceImage->create();
                        $valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
                        $uploaddir = "img/echo/"; //a directory inside
                        for($k=0; $k<sizeof($_FILES['photos']['name']); $k++){
                            $filename = stripslashes($_FILES['photos']['name'][$k]);
                            $size=filesize($_FILES['photos']['tmp_name'][$k]);
                            //get the extension of the file in a lower case format
                            $ext = $this->getExtension($filename);
                            $ext = strtolower($ext);
                            if(in_array($ext,$valid_formats)){
                                if ($size < (9000*1024)){
                                    $image_name=time().$filename;
                                    $newname=$uploaddir.$image_name;

                                    if (move_uploaded_file($_FILES['photos']['tmp_name'][$k], $newname)){
                                        $time=time();
                                        $image['EchoServiceImage']['src_name'] =$image_name;
                                        $image['EchoServiceImage']['echo_srv_id']=$dataServiceId;
                                        $image['EchoServiceImage']['created_by']=$user['User']['id'];
                                        $this->EchoServiceImage->saveAll($image);
                                  }else{
                                         echo '<span class="imgList">You have exceeded the size limit! so moving unsuccessful! </span>';
                                    }
                                }
                             }
                            
                            
                        }
                    }
                    if(!empty($_POST['photo_old'])){
                        for($k=0; $k<sizeof($_POST['photo_old']); $k++){
                            $image['EchoServiceImage']['src_name'] = $_POST['photo_old'][$k];
                            $image['EchoServiceImage']['echo_srv_id']=$dataServiceId;
                            $image['EchoServiceImage']['created_by']=$user['User']['id'];
                            $this->EchoServiceImage->saveAll($image);
                        }
                    }
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
            }
        }
        
        $patient = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'EchoService.is_active' => 1, 'EchoService.id' => $id),
                'fields' => array('Queue.id, EchoService.*, Patient.*'),
                'joins' => array(                    
                    array('table' => 'queues',
                        'alias' => 'Queue',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Queue.patient_id  = Patient.id'
                        )
                    ),
                    array('table' => 'echo_services',
                        'alias' => 'EchoService',
                        'type' => 'INNER',
                        'conditions' => array(
                            'EchoService.echo_service_queue_id = Queue.id'
                        )
                    )
                )));            
        $this->set(compact('patient'));
    }
            
    function add() {
        
    }
    
    function addEchoServiceDoctor($qDoctorId=null,$queueId=null) {
        $this->layout = 'ajax';
        $this->loadModel('QueuedDoctor');
        $this->loadModel('EchoService');
        $this->loadModel('EchoServiceImage');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('EchoServiceRequest');
        $result = array();
        if (!empty($this->data)) {
            $this->EchoService->create();
            $user = $this->getCurrentUser();
            $data['EchoService']['echo_service_queue_id']=$this->data['Queue']['id'];
            $data['EchoService']['description']=$this->data['EchoService']['description'];
            $data['EchoService']['echo_date']=$this->data['EchoService']['echo_date'];
            $data['EchoService']['conclusion']=$this->data['EchoService']['conclusion'];
            $data['EchoService']['created_by']=$user['User']['id'];
            if ($this->EchoService->save($data)) {
                $dataServiceId = $this->EchoService->getLastInsertId();
                // get last insert id
                $result['id'] = $dataServiceId;
                if(!empty($_FILES['photos']['name'])){
                        $this->EchoServiceImage->create();
                        $valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
                        $uploaddir = "img/echo/"; //a directory inside
                        for($k=0; $k<sizeof($_FILES['photos']['name']); $k++){
                            $filename = stripslashes($_FILES['photos']['name'][$k]);
                            $size=filesize($_FILES['photos']['tmp_name'][$k]);
                            //get the extension of the file in a lower case format
                            $ext = $this->getExtension($filename);
                            $ext = strtolower($ext);
                            if(in_array($ext,$valid_formats)){
                                if ($size < (9000*1024)){
                                    $image_name=time().$filename;
                                    $newname=$uploaddir.$image_name;

                                    if (move_uploaded_file($_FILES['photos']['tmp_name'][$k], $newname)){
                                        $time=time();
                                        $image['EchoServiceImage']['src_name'] =$image_name;    
                                     }else{
                                         echo '<span class="imgList">You have exceeded the size limit! so moving unsuccessful! </span>';
                                     }
                                }
                             }
                            $image['EchoServiceImage']['echo_srv_id']=$dataServiceId;
                            $image['EchoServiceImage']['created_by']=$user['User']['id'];
                            $this->EchoServiceImage->saveAll($image);
                        }
                    }
                $serReq['EchoServiceRequest']['id']=$this->data['EchoServiceRequest']['id'];
                $serReq['EchoServiceRequest']['is_active']=2;
                $this->EchoServiceRequest->save($serReq);
                $queueId = $this->data['Queue']['id'];
                echo json_encode($result);
                exit;
            } else {
                $result['code'] = 1;
                echo json_encode($result);
                exit;
            }
        }
        
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'OtherServiceRequest.is_active' => 1, 'Queue.id' => $queueId, 'QeuedDoctor.id' => $qDoctorId),
            'fields' => array('Queue.id, QeuedDoctor.id, EchoServiceRequest.*,Patient.*'),
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
                array('table' => 'echo_service_requests',
                    'alias' => 'EchoServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'EchoServiceRequest.other_service_request_id = OtherServiceRequest.id'
                    )
                )
            )));
        $this->set(compact('patient'));
    }
    
    function printEchoService($id = null) {
        $this->layout = 'ajax';   
        $this->loadModel('Branch');
        if (!empty($id)) {
            $this->data = $this->Branch->read(null, 1);
            $dataService = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'EchoService.is_active' => 1, 'EchoService.id' => $id),
                'fields' => array('Queue.id, EchoService.*, Patient.*'),
                'joins' => array(                    
                    array('table' => 'queues',
                        'alias' => 'Queue',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Queue.patient_id  = Patient.id'
                        )
                    ),
                    array('table' => 'echo_services',
                        'alias' => 'EchoService',
                        'type' => 'INNER',
                        'conditions' => array(
                            'EchoService.echo_service_queue_id = Queue.id'
                        )
                    )
                )));            
            $this->set(compact('dataService'));
        }
    }
    
    function deleteImage($id = null, $name = null){
        $this->loadModel('EchoServiceImage');
        $user = $this->getCurrentUser();
        $date = date("Y-m-d H:i:s");
        $this->EchoServiceImage->updateAll(
                array('EchoServiceImage.is_active' => "2", 'EchoServiceImage.modified' => "'$date'", 'EchoServiceImage.modified_by' => $user['User']['id']), array('EchoServiceImage.id' => $id)
        );
        echo MESSAGE_DATA_HAS_BEEN_DELETED;      
        exit;    
    }
    
    // echo Obstetnique
    function addEchoServiceObstetniqueDoctor($qDoctorId=null,$queueId=null) {
        $this->layout = 'ajax';
        $this->loadModel('QueuedDoctor');
        $this->loadModel('EchoService');
        $this->loadModel('EchographyInfom');
        $this->loadModel('Indication');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('EchoServiceRequest');
        $this->loadModel('EchographiePatient');
        $result = array();
        if (!empty($this->data)) {
            $user = $this->getCurrentUser();
            $echographie_id = $this->data['EchoService']['echograph_id'];
            $description = $this->data['EchoService']['description'][$echographie_id];
            $this->EchographiePatient->create();
            $echoSaveResult['EchographiePatient']['queue_id'] = $this->data['Queue']['id'];
            $echoSaveResult['EchographiePatient']['echography_infom_id'] = $echographie_id;
            $echoSaveResult['EchographiePatient']['doctor_name'] = $this->data['EchoService']['doctor_name'];
            $echoSaveResult['EchographiePatient']['indication_id'] = $this->data['EchoService']['Indication'];
            $echoSaveResult['EchographiePatient']['ddr'] = $this->data['EchoService']['ddr'];
            $echoSaveResult['EchographiePatient']['description'] = stripslashes($description);
            $echoSaveResult['EchographiePatient']['form_child'] = $this->data['EchoService']['form_child'];
            $echoSaveResult['EchographiePatient']['num_child'] = $this->data['EchoService']['num_child'];
            $echoSaveResult['EchographiePatient']['healthy_child'] = $this->data['EchoService']['healthy_child'];
            $echoSaveResult['EchographiePatient']['sex_child'] = $this->data['EchoService']['sex_child'];
            $echoSaveResult['EchographiePatient']['teok_plos'] = $this->data['EchoService']['teok_plos'];
            $echoSaveResult['EchographiePatient']['location_sok'] = $this->data['EchoService']['location_sok'];
            $echoSaveResult['EchographiePatient']['weight_child'] = $this->data['EchoService']['weight_child'];
            $echoSaveResult['EchographiePatient']['week_child'] = $this->data['EchoService']['year_child'];
            $echoSaveResult['EchographiePatient']['day_child'] = $this->data['EchoService']['day_child'];
            $echoSaveResult['EchographiePatient']['born_date'] = $this->data['EchoService']['born_date'];
            $echoSaveResult['EchographiePatient']['created_by'] = $user['User']['id'];
            if ($this->EchographiePatient->save($echoSaveResult)) {
                // get last insert id
                $result['id'] = $this->EchographiePatient->getLastInsertId(); 
                
                $serReq['EchoServiceRequest']['id']=$this->data['EchoServiceRequest']['id'];
                $serReq['EchoServiceRequest']['is_active']=2;
                $this->EchoServiceRequest->save($serReq);
                $queueId = $this->data['Queue']['id'];
                
                echo json_encode($result);
                exit;
                
            } else {
                $result['code'] = 1;
                echo json_encode($result);
                exit;
            }
        }
        
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'OtherServiceRequest.is_active' => 1, 'Queue.id' => $queueId, 'QeuedDoctor.id' => $qDoctorId),
            'fields' => array('Queue.id, QeuedDoctor.id, EchoServiceRequest.*,Patient.*'),
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
                array('table' => 'echo_service_requests',
                    'alias' => 'EchoServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'EchoServiceRequest.other_service_request_id = OtherServiceRequest.id'
                    )
                )
            )));
        $this->set(compact('patient'));
        $echographies = $this->EchographyInfom->find("all", array("conditions" => array("EchographyInfom.is_active=1")));
        $indications = $this->Indication->find("all", array("conditions" => array("Indication.is_active=1")));
        $this->set(compact('echographies', 'indications'));
    }
    
    function addEchoServiceCardiaqueDoctor($qDoctorId=null,$queueId=null) {
        $this->layout = 'ajax';
        $this->loadModel('QueuedDoctor');
        $this->loadModel('EchoServiceCardia');
        $this->loadModel('EchoServiceCardiaImage');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('EchoServiceRequest');
        $result = array();
        if (!empty($this->data)) {
            $this->EchoServiceCardia->create();
            $user = $this->getCurrentUser();
            $data['EchoServiceCardia']['queue_id']=$this->data['Queue']['id'];
            $data['EchoServiceCardia']['effecture']=$this->data['EchoService']['effecture'];
            $data['EchoServiceCardia']['doctor_name']=$this->data['EchoService']['doctor_name'];
            $data['EchoServiceCardia']['motif_exam']=$this->data['EchoService']['motif_exam'];
            $data['EchoServiceCardia']['vd_dtd']=$this->data['EchoService']['vd_dtd'];
            $data['EchoServiceCardia']['ao_ascend']=$this->data['EchoService']['ao_ascend'];
            $data['EchoServiceCardia']['og_1']=$this->data['EchoService']['og_1'];
            $data['EchoServiceCardia']['og_2']=$this->data['EchoService']['og_2'];
            $data['EchoServiceCardia']['siv_1']=$this->data['EchoService']['siv_1'];
            $data['EchoServiceCardia']['siv_2']=$this->data['EchoService']['siv_2'];
            $data['EchoServiceCardia']['vgdtd_dts_1']=$this->data['EchoService']['vgdtd_dts_1'];
            $data['EchoServiceCardia']['vgdtd_dts_2']=$this->data['EchoService']['vgdtd_dts_2'];
            $data['EchoServiceCardia']['pp_vg_1']=$this->data['EchoService']['pp_vg_1'];
            $data['EchoServiceCardia']['pp_vg_2']=$this->data['EchoService']['pp_vg_2'];
            $data['EchoServiceCardia']['frvg_fevg_1']=$this->data['EchoService']['frvg_fevg_1'];
            $data['EchoServiceCardia']['frvg_fevg_2']=$this->data['EchoService']['frvg_fevg_2'];
            $data['EchoServiceCardia']['description']=$this->data['EchoService']['description'];
            $data['EchoServiceCardia']['conclusion']=$this->data['EchoService']['conclusion'];
            $data['EchoServiceCardia']['created_by']=$user['User']['id'];
            if ($this->EchoServiceCardia->save($data)) {
                $dataServiceId = $this->EchoServiceCardia->getLastInsertId();
                // get last insert id
                $result['id'] = $dataServiceId;
                if(!empty($_FILES['photos']['name'])){
                        $valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
                        $uploaddir = "img/echo_cardia/"; //a directory inside
                        for($k=0; $k<sizeof($_FILES['photos']['name']); $k++){
                            if(!empty($_FILES['photos']['name'][$k])){
                                $this->EchoServiceCardiaImage->create();
                                $filename = stripslashes($_FILES['photos']['name'][$k]);
                                $size=filesize($_FILES['photos']['tmp_name'][$k]);
                                //get the extension of the file in a lower case format
                                $ext = $this->getExtension($filename);
                                $ext = strtolower($ext);
                                if(in_array($ext,$valid_formats)){
                                    if ($size < (9000*1024)){
                                        $image_name=time().$filename;
                                        $newname=$uploaddir.$image_name;

                                        if (move_uploaded_file($_FILES['photos']['tmp_name'][$k], $newname)){
                                            $time=time();
                                            $image['EchoServiceCardiaImage']['src_name'] =$image_name;    
                                         }else{
                                             echo '<span class="imgList">You have exceeded the size limit! so moving unsuccessful! </span>';
                                         }
                                    }
                                 }
                                $image['EchoServiceCardiaImage']['echo_srv_cardia_id']=$dataServiceId;
                                $image['EchoServiceCardiaImage']['created_by']=$user['User']['id'];
                                $this->EchoServiceCardiaImage->saveAll($image);
                            }
                        }
                    }
                $serReq['EchoServiceRequest']['id']=$this->data['EchoServiceRequest']['id'];
                $serReq['EchoServiceRequest']['is_active']=2;
                $this->EchoServiceRequest->save($serReq);
                $queueId = $this->data['Queue']['id'];
                echo json_encode($result);
                exit;
            } else {
                $result['code'] = 1;
                echo json_encode($result);
                exit;
            }
        }
        
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'OtherServiceRequest.is_active' => 1, 'Queue.id' => $queueId, 'QeuedDoctor.id' => $qDoctorId),
            'fields' => array('Queue.id, QeuedDoctor.id, EchoServiceRequest.*,Patient.*'),
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
                array('table' => 'echo_service_requests',
                    'alias' => 'EchoServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'EchoServiceRequest.other_service_request_id = OtherServiceRequest.id'
                    )
                )
            )));
        $this->set(compact('patient'));
    }
    
    function printEchoServiceCardia($id = null) {
        $this->layout = 'ajax';        
        if (!empty($id)) {
            $dataService = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'EchoServiceCardia.is_active' => 1, 'EchoServiceCardia.id' => $id),
                'fields' => array('Queue.id, EchoServiceCardia.*, Patient.*'),
                'joins' => array(                    
                    array('table' => 'queues',
                        'alias' => 'Queue',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Queue.patient_id  = Patient.id'
                        )
                    ),
                    array('table' => 'echo_service_cardias',
                        'alias' => 'EchoServiceCardia',
                        'type' => 'INNER',
                        'conditions' => array(
                            'EchoServiceCardia.queue_id = Queue.id'
                        )
                    )
                )));            
            $this->set(compact('dataService'));
        }
    }
    
    function getExtension($str){
        $i = strrpos($str,".");
        if (!$i) { return ""; }
        $l = strlen($str) - $i;
        $ext = substr($str,$i+1,$l);
        return $ext;
    }
}

?>