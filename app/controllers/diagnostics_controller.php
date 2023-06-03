<?php

class DiagnosticsController extends AppController {

    var $name = 'Diagnostics';
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
        $this->Helper->saveUserActivity($user['User']['id'], 'Diagnostic', 'View', $id);
        $this->data = $this->Diagnostic->read(null, $id);
    }

    function add() {
        $this->layout = 'ajax';
        $this->loadModel("Diagnostic");
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
                $dateNow  = date("Y-m-d H:i:s");
                $this->Diagnostic->create();
                $this->data['Diagnostic']['created']    = $dateNow;
                $this->data['Diagnostic']['created_by'] = $user['User']['id'];
                $this->data['Diagnostic']['is_active']  = 1;
                if ($this->Diagnostic->save($this->data)) {
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
         $this->loadModel("Diagnostic");
        if (!$id && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
                $dateNow  = date("Y-m-d H:i:s");
                $this->Diagnostic->create();
        
                $this->data['Diagnostic']['id'] =    $this->data['Diagnostic']['id'];
                $this->data['Diagnostic']['modified']    = $dateNow;
                $this->data['Diagnostic']['modified_by'] = $user['User']['id'];
                $this->data['Diagnostic']['is_active']  = 1;
                if ($this->Diagnostic->save($this->data)) {
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                 
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
            $this->data = $this->Diagnostic->read(null, $id);
        }
    
 

    function delete($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
         $user = $this->getCurrentUser();
          $this->data['Diagnostic']['modified'] = date("Y-m-d H:i:s");
          $this->data['Diagnostic']['modified_by'] =$user['User']['id'] ;
          $this->data['Diagnostic']['id'] = $id;
          $this->data['Diagnostic']['is_active'] = 0;
          if($this->Diagnostic->saveAll($this->data)){
              echo MESSAGE_DATA_HAS_BEEN_DELETED;
              exit;
          }else{
              echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
              exit; 
          }
     
    }

}

?>