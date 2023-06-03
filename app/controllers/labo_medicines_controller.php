<?php
class LaboMedicinesController extends AppController {

    var $name = 'LaboMedicines';
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
            if ($this->Helper->checkDouplicate('name', 'labo_medicines', trim($this->data['LaboMedicine']['name']))) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $user = $this->getCurrentUser($this->Session);
                $this->data['LaboMedicine']['name'] = trim($this->data['LaboMedicine']['name']);
                $this->data['LaboMedicine']['created_by'] = $user['User']['id'];
                $this->data['LaboMedicine']['is_active'] = 1;
                if ($this->LaboMedicine->save($this->data)) {
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
        $this->LaboMedicine->updateAll(
                array('LaboMedicine.is_active' => "2"),
                array('LaboMedicine.id' => $id)
        );
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }
    function edit($id) {
        $this->layout = 'ajax';
        if(!empty($this->data)) {
            if ($this->Helper->checkDouplicateEdit('name', 'labo_medicines', $id, trim($this->data['LaboMedicine']['name']))) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $user = $this->getCurrentUser($this->Session);
                $this->data['LaboMedicine']['name'] = trim($this->data['LaboMedicine']['name']);
                $this->data['LaboMedicine']['modified_by'] = $user['User']['id'];
                if ($this->LaboMedicine->save($this->data)) {
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        $this->data = $this->LaboMedicine->read(null, $id);
    }
}
?>