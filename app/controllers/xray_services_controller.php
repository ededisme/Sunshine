<?php

class XrayServicesController extends AppController {

    var $name = 'XrayServices';
    var $uses = array('Patient', 'User');
    var $components = array('Helper');

    function index() {
        $this->layout = 'ajax';
    }
    
    function  ajax(){
        $this->layout = 'ajax';
    }
    
    function xrayServiceDoctorAjax() {
        $this->layout = 'ajax';
    }
    
    function xrayServiceDoctor() {
        
    }
    
    function view($id=null){
        $this->layout = 'ajax';        
        if (!empty($id)) {
            $dataService = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'XrayService.is_active' => 1, 'XrayService.id' => $id),
                'fields' => array('Queue.id, XrayService.*, Patient.*'),
                'joins' => array(                    
                    array('table' => 'queues',
                        'alias' => 'Queue',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Queue.patient_id  = Patient.id'
                        )
                    ),
                    array('table' => 'xray_services',
                        'alias' => 'XrayService',
                        'type' => 'INNER',
                        'conditions' => array(
                            'XrayService.xray_service_queue_id = Queue.id'
                        )
                    )
                )));            
            $this->set(compact('dataService'));
        }
    }
    
    function edit($id=null) {
        $this->layout = 'ajax';
        $this->loadModel('QueuedDoctor');
        $this->loadModel('XrayService');
        $this->loadModel('XrayServiceImage');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('XrayServiceRequest');
        if (!empty($this->data)) {
              
            $user = $this->getCurrentUser();
            $this->XrayService->updateAll(
                    array('XrayService.is_active' => "2", 'XrayService.modified_by' => $user['User']['id']), array('XrayService.id' => $this->data['XrayService']['id'])
            );
            $this->XrayService->create();
            $data['XrayService']['xray_service_queue_id']=$this->data['Queue']['id'];
            $data['XrayService']['description']=$this->data['XrayService']['description'];
            $data['XrayService']['xray_date']=$this->data['XrayService']['xray_date'];
            $data['XrayService']['conclusion']=$this->data['XrayService']['conclusion'];
            $data['XrayService']['created_by']=$user['User']['id'];
            if ($this->XrayService->save($data)) {
                $dataServiceId = $this->XrayService->getLastInsertId();
                if(!empty($_FILES['photos']['name'])){
                        $this->XrayServiceImage->create();
                        $valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
                        $uploaddir = "img/x-ray/"; //a directory inside
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
                                        $image['XrayServiceImage']['src_name'] =$image_name;
                                        $image['XrayServiceImage']['xray_srv_id']=$dataServiceId;
                                        $image['XrayServiceImage']['created_by']=$user['User']['id'];
                                        $this->XrayServiceImage->saveAll($image);
                                        
                                    }else{
                                         echo '<span class="imgList">You have exceeded the size limit! so moving unsuccessful! </span>';
                                    }
                                }
                             }
                          
                            
                        }
                    }
                if(!empty($_POST['photo_old'])){
                    for($k=0; $k<sizeof($_POST['photo_old']); $k++){
                        $image['XrayServiceImage']['src_name'] = $_POST['photo_old'][$k];
                        $image['XrayServiceImage']['xray_srv_id']=$dataServiceId;
                        $image['XrayServiceImage']['created_by']=$user['User']['id'];
                        $this->XrayServiceImage->saveAll($image);
                    }
                } 
                $queueId = $this->data['Queue']['id'];
               echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
            }
        }
        
        $patient = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'XrayService.is_active' => 1, 'XrayService.id' => $id),
                'fields' => array('Queue.id, XrayService.*, Patient.*'),
                'joins' => array(                    
                    array('table' => 'queues',
                        'alias' => 'Queue',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Queue.patient_id  = Patient.id'
                        )
                    ),
                    array('table' => 'xray_services',
                        'alias' => 'XrayService',
                        'type' => 'INNER',
                        'conditions' => array(
                            'XrayService.xray_service_queue_id = Queue.id'
                        )
                    )
                )));            
        $this->set(compact('patient'));
    }            
    
    function addXrayServiceDoctor($qDoctorId=null,$queueId=null) {
        $this->layout = 'ajax';
        $this->loadModel('QueuedDoctor');
        $this->loadModel('XrayService');
        $this->loadModel('XrayServiceImage');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('XrayServiceRequest');
        $result = array();
        if (!empty($this->data)) {
            $this->XrayService->create();
            $user = $this->getCurrentUser();
            $data['XrayService']['xray_service_queue_id']=$this->data['Queue']['id'];
            $data['XrayService']['description']=$this->data['XrayService']['description'];
            $data['XrayService']['xray_date']=$this->data['XrayService']['xray_date'];
            $data['XrayService']['conclusion']=$this->data['XrayService']['conclusion'];
            $data['XrayService']['created_by']=$user['User']['id'];
            if ($this->XrayService->save($data)) {
                $dataServiceId = $this->XrayService->getLastInsertId();
                // get last insert id
                $result['id'] = $dataServiceId;
                if(!empty($_FILES['photos']['name'])){
                    $this->XrayServiceImage->create();
                    $valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
                    $uploaddir = "img/x-ray/"; //a directory inside
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
                                    $image['XrayServiceImage']['src_name'] =$image_name;    
                                 }else{
                                     echo '<span class="imgList">You have exceeded the size limit! so moving unsuccessful! </span>';
                                 }
                            }
                         }
                        $image['XrayServiceImage']['xray_srv_id']=$dataServiceId;
                        $image['XrayServiceImage']['created_by']=$user['User']['id'];
                        $this->XrayServiceImage->saveAll($image);
                    }
                }
                $serReq['XrayServiceRequest']['id']=$this->data['XrayServiceRequest']['id'];
                $serReq['XrayServiceRequest']['is_active']=2;
                $this->XrayServiceRequest->save($serReq);
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
            'fields' => array('Queue.id, QeuedDoctor.id, XrayServiceRequest.*,Patient.*'),
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
                array('table' => 'xray_service_requests',
                    'alias' => 'XrayServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'XrayServiceRequest.other_service_request_id = OtherServiceRequest.id'
                    )
                )
            )));
        $this->set(compact('patient'));
    }
    
    function getExtension($str){
        $i = strrpos($str,".");
        if (!$i) { return ""; }
        $l = strlen($str) - $i;
        $ext = substr($str,$i+1,$l);
        return $ext;
    }
    
    function printXrayService($id = null) {
        $this->layout = 'ajax';     
        $this->loadModel('Branch');
        if (!empty($id)) {
            $this->data = $this->Branch->read(null, 1);
            $dataService = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'XrayService.is_active' => 1, 'XrayService.id' => $id),
                'fields' => array('Queue.id, XrayService.*, Patient.*'),
                'joins' => array(                    
                    array('table' => 'queues',
                        'alias' => 'Queue',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Queue.patient_id  = Patient.id'
                        )
                    ),
                    array('table' => 'xray_services',
                        'alias' => 'XrayService',
                        'type' => 'INNER',
                        'conditions' => array(
                            'XrayService.xray_service_queue_id = Queue.id'
                        )
                    )
                )));            
            $this->set(compact('dataService'));
        }
    }
    
    function deleteImage($id = null, $name = null){
        $this->loadModel('XrayServiceImage');
        $user = $this->getCurrentUser();
        $date = date("Y-m-d H:i:s");
        $this->XrayServiceImage->updateAll(
                array('XrayServiceImage.is_active' => "2", 'XrayServiceImage.modified' => "'$date'", 'XrayServiceImage.modified_by' => $user['User']['id']), array('XrayServiceImage.id' => $id)
        );
        echo MESSAGE_DATA_HAS_BEEN_DELETED;      
        exit;    
    }
    
}

?>