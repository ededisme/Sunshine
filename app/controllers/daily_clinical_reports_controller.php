<?php

class DailyClinicalReportsController extends AppController {

    var $name = 'DailyClinicalReports';
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
        $this->data = $this->DailyClinicalReport->read(null, $id);
    }

    function add() {
        $this->layout = 'ajax';
        $this->loadModel("DailyClinicalReport");
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {                
                $this->DailyClinicalReport->create();
                $this->data['DailyClinicalReport']['created_by'] = $user['User']['id'];
                $this->data['DailyClinicalReport']['is_active']  = 1;
                if ($this->DailyClinicalReport->save($this->data)) {
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
         $this->loadModel("DailyClinicalReport");
        if (!$id && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
                $dateNow  = date("Y-m-d H:i:s");
                $this->DailyClinicalReport->create();
        
                $this->data['DailyClinicalReport']['id'] =    $this->data['DailyClinicalReport']['id'];
                $this->data['DailyClinicalReport']['modified']    = $dateNow;
                $this->data['DailyClinicalReport']['modified_by'] = $user['User']['id'];
                $this->data['DailyClinicalReport']['is_active']  = 1;
                if ($this->DailyClinicalReport->save($this->data)) {
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                 
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
            $this->data = $this->DailyClinicalReport->read(null, $id);
        }
    
 

    function delete($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
         $user = $this->getCurrentUser();
          $this->data['DailyClinicalReport']['modified'] = date("Y-m-d H:i:s");
          $this->data['DailyClinicalReport']['modified_by'] =$user['User']['id'] ;
          $this->data['DailyClinicalReport']['id'] = $id;
          $this->data['DailyClinicalReport']['is_active'] = 0;
          if($this->DailyClinicalReport->saveAll($this->data)){
              echo MESSAGE_DATA_HAS_BEEN_DELETED;
              exit;
          }else{
              echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
              exit; 
          }
     
    }

}

?>