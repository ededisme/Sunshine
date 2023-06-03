<?php
class EchographyInfomsController extends AppController {

    var $name = 'EchographyInfoms';
    var $components = array('Helper');
    var $helpers = array('Html', 'Form', 'Javascript');


    function index() {
        $this->layout = 'ajax';
    }
    
    function ajax() {
        $this->layout = 'ajax';
    }
    
    function add() {
        $this->layout = 'ajax';
        if(!empty($this->data)) {
            if ($this->Helper->checkDouplicate('name', 'echography_infoms', trim($this->data['EchographyInfom']['name']))) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $user = $this->getCurrentUser();
                $this->data['EchographyInfom']['name'] = trim($this->data['EchographyInfom']['name']);
                $this->data['EchographyInfom']['description'] = stripslashes($this->data['EchographyInfom']['description']);   
                $this->data['EchographyInfom']['created_by'] = $user['User']['id'];
                $this->data['EchographyInfom']['is_active'] = 1;
                if ($this->EchographyInfom->save($this->data)) {
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
    }
    function delete($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        $this->EchographyInfom->updateAll(
                array('EchographyInfom.is_active' => "3", 'EchographyInfom.modified_by' => $user['User']['id']),
                array('EchographyInfom.id' => $id)
        );
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;       
    }
    function edit($id) {
        $this->layout = 'ajax';
        if(!empty($this->data)) {
            if ($this->Helper->checkDouplicateEdit('name', 'echography_infoms', $id, trim($this->data['EchographyInfom']['name']))) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $user = $this->getCurrentUser();
                $this->EchographyInfom->updateAll(
                    array('EchographyInfom.is_active' => "2", 'EchographyInfom.modified_by' => $user['User']['id']),
                    array('EchographyInfom.id' => $this->data['EchographyInfom']['id'])
                );
                $this->EchographyInfom->create();
                $data['EchographyInfom']['name'] = trim($this->data['EchographyInfom']['name']);
                $data['EchographyInfom']['description'] = stripslashes($this->data['EchographyInfom']['description']);  
                $data['EchographyInfom']['created_by'] = $user['User']['id'];
                if ($this->EchographyInfom->save($data)) {
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        $this->data = $this->EchographyInfom->read(null, $id);
    }
    
    function view($id) {     
        $this->layout = 'ajax';       
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $this->set('echographyFom', $this->EchographyInfom->read(null, $id));
    }
}
?>