<?php
class LaboUnitsController extends AppController {

    var $name = 'LaboUnits';
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
            if ($this->Helper->checkDouplicate('name', 'labo_units', $this->data['LaboUnit']['name'])) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $user = $this->getCurrentUser();
                $this->data['LaboUnit']['name'] = trim($this->data['LaboUnit']['name']);
                $this->data['LaboUnit']['created_by'] = $user['User']['id'];
                $this->data['LaboUnit']['is_active'] = 1;
                if ($this->LaboUnit->save($this->data)) {
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
        $this->LaboUnit->updateAll(
                array('LaboUnit.is_active' => "2", 'LaboUnit.modified_by' => $user['User']['id']),
                array('LaboUnit.id' => $id)
        );
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }
    function edit($id) {
        $this->layout = 'ajax';
        if(!empty($this->data)) {
            if ($this->Helper->checkDouplicateEdit('name', 'labo_units', $id, $this->data['LaboUnit']['name'])) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $user = $this->getCurrentUser();
                $this->data['LaboUnit']['name'] = trim($this->data['LaboUnit']['name']);
                $this->data['LaboUnit']['modified_by'] = $user['User']['id'];
                if ($this->LaboUnit->save($this->data)) {
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        $this->data = $this->LaboUnit->read(null, $id);
    }

    function addAjax() {
        $this->layout = "ajax";
        $result = "";
        if(!empty($this->data)) {
            $user = $this->getCurrentUser();
            $this->data['LaboUnit']['created_by'] = $user['User']['id'];
            $this->data['LaboUnit']['is_active'] = 1;

            if ($this->LaboUnit->save($this->data)) {
                $result['id'] = $this->LaboUnit->id;
                $result['name'] = $this->data['LaboUnit']['name'];
            }
        }
        $this->set(compact('result'));
    }
}
?>