<?php

class ChiefComplainsController extends AppController {

    var $name = 'ChiefComplains';
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
        $this->data = $this->ChiefComplain->read(null, $id);
    }

    function add() {
        $this->layout = 'ajax';
        $this->loadModel("ChiefComplain");
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
                $dateNow  = date("Y-m-d H:i:s");
                $this->ChiefComplain->create();
                $this->data['ChiefComplain']['created']    = $dateNow;
                $this->data['ChiefComplain']['created_by'] = $user['User']['id'];
                $this->data['ChiefComplain']['is_active']  = 1;
                if ($this->ChiefComplain->save($this->data)) {
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
         $this->loadModel("ChiefComplain");
        if (!$id && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
                $dateNow  = date("Y-m-d H:i:s");
                $this->ChiefComplain->create();
        
                $this->data['ChiefComplain']['id'] =    $this->data['ChiefComplain']['id'];
                $this->data['ChiefComplain']['modified']    = $dateNow;
                $this->data['ChiefComplain']['modified_by'] = $user['User']['id'];
                $this->data['ChiefComplain']['is_active']  = 1;
                if ($this->ChiefComplain->save($this->data)) {
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                 
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
            $this->data = $this->ChiefComplain->read(null, $id);
        }
    
 

    function delete($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
         $user = $this->getCurrentUser();
          $this->data['ChiefComplain']['modified'] = date("Y-m-d H:i:s");
          $this->data['ChiefComplain']['modified_by'] =$user['User']['id'] ;
          $this->data['ChiefComplain']['id'] = $id;
          $this->data['ChiefComplain']['is_active'] = 0;
          if($this->ChiefComplain->saveAll($this->data)){
              echo MESSAGE_DATA_HAS_BEEN_DELETED;
              exit;
          }else{
              echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
              exit; 
          }
     
    }

}

?>