<?php

class DoctorCommentsController extends AppController {

    var $name = 'DoctorComments';
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
        $this->Helper->saveUserActivity($user['User']['id'], 'Doctor Comment', 'View', $id);
        $this->data = $this->DoctorComment->read(null, $id);
    }

    function add() {
        $this->layout = 'ajax';
        $this->loadModel("DoctorComment");
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
                $dateNow  = date("Y-m-d H:i:s");
                $this->DoctorComment->create();
                $this->data['DoctorComment']['created']    = $dateNow;
                $this->data['DoctorComment']['created_by'] = $user['User']['id'];
                $this->data['DoctorComment']['is_active']  = 1;
                if ($this->DoctorComment->save($this->data)) {
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
         $this->loadModel("DoctorComment");
        if (!$id && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
                $dateNow  = date("Y-m-d H:i:s");
                $this->DoctorComment->create();
        
                $this->data['DoctorComment']['id'] =    $this->data['DoctorComment']['id'];
                $this->data['DoctorComment']['modified']    = $dateNow;
                $this->data['DoctorComment']['modified_by'] = $user['User']['id'];
                $this->data['DoctorComment']['is_active']  = 1;
                if ($this->DoctorComment->save($this->data)) {
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                 
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
            $this->data = $this->DoctorComment->read(null, $id);
        }
    
 

    function delete($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
         $user = $this->getCurrentUser();
          $this->data['DoctorComment']['modified'] = date("Y-m-d H:i:s");
          $this->data['DoctorComment']['modified_by'] =$user['User']['id'] ;
          $this->data['DoctorComment']['id'] = $id;
          $this->data['DoctorComment']['is_active'] = 0;
          if($this->DoctorComment->saveAll($this->data)){
              echo MESSAGE_DATA_HAS_BEEN_DELETED;
              exit;
          }else{
              echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
              exit; 
          }
     
    }

}

?>