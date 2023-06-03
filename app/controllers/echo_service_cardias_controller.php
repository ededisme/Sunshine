<?php

class EchoServiceCardiasController extends AppController {

    var $name = 'EchoServiceCardias';
    var $uses = array('Patient', 'User');
    var $components = array('Helper');

    function index() {
        $this->layout = 'ajax';
    }
    
    function  ajax(){
        $this->layout = 'ajax';
    }
    
    function view($id=null){
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
    
    function edit($id=null) {
        $this->layout = 'ajax';
        $this->loadModel('QueuedDoctor');
        $this->loadModel('EchoServiceCardia');
        $this->loadModel('EchoServiceCardiaImage');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('EchoServiceRequest');
        if (!empty($this->data)) {
            $user = $this->getCurrentUser();
            
            mysql_query("UPDATE echo_service_cardias SET is_active=0,modified_by='".$user['User']['id']."' WHERE id=".$this->data['EchoServiceCardia']['id']);
            $this->EchoServiceCardia->create();
            $data['EchoServiceCardia']['queue_id']=$this->data['Queue']['id'];
            $data['EchoServiceCardia']['effecture']=$this->data['EchoServiceCardia']['effecture'];
            $data['EchoServiceCardia']['doctor_name']=$this->data['EchoServiceCardia']['doctor_name'];
            $data['EchoServiceCardia']['motif_exam']=$this->data['EchoServiceCardia']['motif_exam'];
            $data['EchoServiceCardia']['vd_dtd']=$this->data['EchoServiceCardia']['vd_dtd'];
            $data['EchoServiceCardia']['ao_ascend']=$this->data['EchoServiceCardia']['ao_ascend'];
            $data['EchoServiceCardia']['og_1']=$this->data['EchoServiceCardia']['og_1'];
            $data['EchoServiceCardia']['og_2']=$this->data['EchoServiceCardia']['og_2'];
            $data['EchoServiceCardia']['siv_1']=$this->data['EchoServiceCardia']['siv_1'];
            $data['EchoServiceCardia']['siv_2']=$this->data['EchoServiceCardia']['siv_2'];
            $data['EchoServiceCardia']['vgdtd_dts_1']=$this->data['EchoServiceCardia']['vgdtd_dts_1'];
            $data['EchoServiceCardia']['vgdtd_dts_2']=$this->data['EchoServiceCardia']['vgdtd_dts_2'];
            $data['EchoServiceCardia']['pp_vg_1']=$this->data['EchoServiceCardia']['pp_vg_1'];
            $data['EchoServiceCardia']['pp_vg_2']=$this->data['EchoServiceCardia']['pp_vg_2'];
            $data['EchoServiceCardia']['frvg_fevg_1']=$this->data['EchoServiceCardia']['frvg_fevg_1'];
            $data['EchoServiceCardia']['frvg_fevg_2']=$this->data['EchoServiceCardia']['frvg_fevg_2'];
            $data['EchoServiceCardia']['description']=$this->data['EchoServiceCardia']['description'];
            $data['EchoServiceCardia']['conclusion']=$this->data['EchoServiceCardia']['conclusion'];
            $data['EchoServiceCardia']['created_by']=$user['User']['id'];
            if ($this->EchoServiceCardia->save($data)) {
                $dataServiceId = $this->EchoServiceCardia->getLastInsertId();
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
                                if(!empty($_POST['photo_old'][$k]) && $_POST['photo_old'][$k]!=''){
                                    $image['EchoServiceCardiaImage']['src_name'] = $_POST['photo_old'][$k];
                                } 
                                $image['EchoServiceCardiaImage']['echo_srv_cardia_id']=$dataServiceId;
                                $image['EchoServiceCardiaImage']['created_by']=$user['User']['id'];
                                $this->EchoServiceCardiaImage->saveAll($image);
                            }
                        }
                    }
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
            }
        }
        
        $patient = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'EchoServiceCardia.is_active' => 1, 'EchoServiceCardia.id' => $id),
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
        $this->set(compact('patient'));
    }
    
    function deleteImage($id = null, $name = null){
        $this->loadModel('EchoServiceCardiaImage');
        $user = $this->getCurrentUser();
        $date = date("Y-m-d H:i:s");
        $this->EchoServiceCardiaImage->updateAll(
                array('EchoServiceCardiaImage.is_active' => "-1", 'EchoServiceCardiaImage.modified' => "'$date'", 'EchoServiceCardiaImage.modified_by' => $user['User']['id']), array('EchoServiceCardiaImage.id' => $id)
        );
        echo MESSAGE_DATA_HAS_BEEN_DELETED;      
        exit;    
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