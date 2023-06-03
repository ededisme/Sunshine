<?php
class LaboSitesController extends AppController {

    var $name = 'LaboSites';
    var $components = array('Helper');

    function index() {   
        $this->layout = 'ajax';
    }
    
    function ajax(){
        $this->layout = 'ajax';
    }
    
    function add() {
        $this->layout = 'ajax';
        if(!empty($this->data)) {            
            if ($this->Helper->checkDouplicate('name', 'labo_sites', trim($this->data['LaboSite']['name']))) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $user = $this->getCurrentUser();
                $this->data['LaboSite']['name'] = trim($this->data['LaboSite']['name']);
                $this->data['LaboSite']['created_by'] = $user['User']['id'];
                $this->data['LaboSite']['is_active'] = 1;
                if ($this->LaboSite->save($this->data)) {
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
        $this->LaboSite->updateAll(
                array('LaboSite.is_active' => "2"),
                array('LaboSite.id' => $id)
        );
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }
    function edit($id) {
        $this->layout = 'ajax';
        if(!empty($this->data)) {            
            if ($this->Helper->checkDouplicateEdit('name', 'labo_sites', $id, trim($this->data['LaboSite']['name']))) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $user = $this->getCurrentUser();
                $this->data['LaboSite']['name'] = trim($this->data['LaboSite']['name']);
                $this->data['LaboSite']['modified_by'] = $user['User']['id'];

                if ($this->LaboSite->save($this->data)) {
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        $this->data = $this->LaboSite->read(null, $id);
    }
}
?>