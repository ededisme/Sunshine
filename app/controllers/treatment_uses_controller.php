<?php

class TreatmentUsesController extends AppController {

    var $name = 'TreatmentUses';
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
        $this->Helper->saveUserActivity($user['User']['id'], 'Treatment Use', 'View', $id);
        $this->data = $this->TreatmentUse->read(null, $id);
    }

    function add() {
        $this->layout = 'ajax';
        $this->loadModel("TreatmentUse");
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
                $dateNow  = date("Y-m-d H:i:s");
                $this->TreatmentUse->create();
                $this->data['TreatmentUse']['created']    = $dateNow;
                $this->data['TreatmentUse']['created_by'] = $user['User']['id'];
                $this->data['TreatmentUse']['is_active']  = 1;
                if ($this->TreatmentUse->save($this->data)) {
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
         $this->loadModel("TreatmentUse");
        if (!$id && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
                $dateNow  = date("Y-m-d H:i:s");
                $this->TreatmentUse->create();
        
                $this->data['TreatmentUse']['id'] =    $this->data['TreatmentUse']['id'];
                $this->data['TreatmentUse']['modified']    = $dateNow;
                $this->data['TreatmentUse']['modified_by'] = $user['User']['id'];
                $this->data['TreatmentUse']['is_active']  = 1;
                if ($this->TreatmentUse->save($this->data)) {
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                 
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
            $this->data = $this->TreatmentUse->read(null, $id);
        }
    
 

    function delete($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
         $user = $this->getCurrentUser();
          $this->data['TreatmentUse']['modified'] = date("Y-m-d H:i:s");
          $this->data['TreatmentUse']['modified_by'] =$user['User']['id'] ;
          $this->data['TreatmentUse']['id'] = $id;
          $this->data['TreatmentUse']['is_active'] = 0;
          if($this->TreatmentUse->saveAll($this->data)){
              echo MESSAGE_DATA_HAS_BEEN_DELETED;
              exit;
          }else{
              echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
              exit; 
          }
     
    }

}

?>