<?php

class MedicalHistoriesController extends AppController {

    var $name = 'MedicalHistories';
    var $components = array('Helper');

    function index() {
        $this->layout = 'ajax';
    }

    function ajax() {
        $this->layout = 'ajax';
    }

    function view($id = null) {
        $this->layout = 'ajax';
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        $this->Helper->saveUserActivity($user['User']['id'], 'Cheif_Complain', 'View', $id);
        $this->data = $this->MedicalHistory->read(null, $id);
    }

    function add() {
        $this->layout = 'ajax';
        $this->loadModel("MedicalHistory");
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
                $dateNow  = date("Y-m-d H:i:s");
                $this->MedicalHistory->create();
                $this->data['MedicalHistory']['created']    = $dateNow;
                $this->data['MedicalHistory']['created_by'] = $user['User']['id'];
                $this->data['MedicalHistory']['is_active']  = 1;
                if ($this->MedicalHistory->save($this->data)) {
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
    }

    function edit($id = null) {
        $this->layout = 'ajax';
         $this->loadModel("MedicalHistory");
        if (!$id && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
                $dateNow  = date("Y-m-d H:i:s");
                $this->MedicalHistory->create();
        
                $this->data['MedicalHistory']['id'] =    $this->data['MedicalHistory']['id'];
                $this->data['MedicalHistory']['modified']    = $dateNow;
                $this->data['MedicalHistory']['modified_by'] = $user['User']['id'];
                $this->data['MedicalHistory']['is_active']  = 1;
                if ($this->MedicalHistory->save($this->data)) {
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                 
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
            $this->data = $this->MedicalHistory->read(null, $id);
        }
    
 

    function delete($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
         $user = $this->getCurrentUser();
          $this->data['MedicalHistory']['modified'] = date("Y-m-d H:i:s");
          $this->data['MedicalHistory']['modified_by'] =$user['User']['id'] ;
          $this->data['MedicalHistory']['id'] = $id;
          $this->data['MedicalHistory']['is_active'] = 0;
          if($this->MedicalHistory->saveAll($this->data)){
              echo MESSAGE_DATA_HAS_BEEN_DELETED;
              exit;
          }else{
              echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
              exit; 
          }
     
    }

}

?>