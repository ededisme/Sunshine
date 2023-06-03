<?php

class ExaminationsController extends AppController {

    var $name = 'Examinations';
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
        $this->Helper->saveUserActivity($user['User']['id'], 'Examination', 'View', $id);
        $this->data = $this->Examination->read(null, $id);
    }

    function add() {
        $this->layout = 'ajax';
        $this->loadModel("Examination");
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
                $dateNow  = date("Y-m-d H:i:s");
                $this->Examination->create();
                $this->data['Examination']['created']    = $dateNow;
                $this->data['Examination']['created_by'] = $user['User']['id'];
                $this->data['Examination']['is_active']  = 1;
                if ($this->Examination->save($this->data)) {
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
         $this->loadModel("Examination");
        if (!$id && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
                $dateNow  = date("Y-m-d H:i:s");
                $this->Examination->create();
        
                $this->data['Examination']['id'] =    $this->data['Examination']['id'];
                $this->data['Examination']['modified']    = $dateNow;
                $this->data['Examination']['modified_by'] = $user['User']['id'];
                $this->data['Examination']['is_active']  = 1;
                if ($this->Examination->save($this->data)) {
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                 
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
            $this->data = $this->Examination->read(null, $id);
        }
    
 

    function delete($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
         $user = $this->getCurrentUser();
          $this->data['Examination']['modified'] = date("Y-m-d H:i:s");
          $this->data['Examination']['modified_by'] =$user['User']['id'] ;
          $this->data['Examination']['id'] = $id;
          $this->data['Examination']['is_active'] = 0;
          if($this->Examination->saveAll($this->data)){
              echo MESSAGE_DATA_HAS_BEEN_DELETED;
              exit;
          }else{
              echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
              exit; 
          }
     
    }

}

?>