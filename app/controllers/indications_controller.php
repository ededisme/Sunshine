<?php
class IndicationsController extends AppController {

    var $name = 'Indications';
    var $components = array('Helper');

    function index() {
        $this->layout = 'ajax';
    }
    
    function ajax() {
        $this->layout = 'ajax';
    }
    
    function add() {
        $this->layout = 'ajax';
        if(!empty($this->data)) {
            if ($this->Helper->checkDouplicate('name', 'indications', trim($this->data['Indication']['name']))) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $user = $this->getCurrentUser();
                $this->data['Indication']['name'] = trim($this->data['Indication']['name']);
                $this->data['Indication']['created_by'] = $user['User']['id'];
                $this->data['Indication']['is_active'] = 1;
                if ($this->Indication->save($this->data)) {
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
        $this->Indication->updateAll(
                array('Indication.is_active' => "3", 'Indication.modified_by' => $user['User']['id']),
                array('Indication.id' => $id)
        );
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;       
    }
    function edit($id) {
        $this->layout = 'ajax';
        if(!empty($this->data)) {
            if ($this->Helper->checkDouplicateEdit('name', 'indications', $id, trim($this->data['Indication']['name']))) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $user = $this->getCurrentUser();
                $this->Indication->updateAll(
                    array('Indication.is_active' => "2", 'Indication.modified_by' => $user['User']['id']),
                    array('Indication.id' => $this->data['Indication']['id'])
                );
                $this->Indication->create();
                $data['Indication']['name'] = trim($this->data['Indication']['name']);
                $data['Indication']['created_by'] = $user['User']['id'];
                if ($this->Indication->save($data)) {
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        $this->data = $this->Indication->read(null, $id);
    }
    
    function view($id) {     
        $this->layout = 'ajax';       
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $this->set('indication', $this->Indication->read(null, $id));
    }
}
?>