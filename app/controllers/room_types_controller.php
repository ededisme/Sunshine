<?php

class RoomTypesController extends AppController {

    var $name = 'RoomTypes';
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
            $this->Session->setFlash(__(MESSAGE_DATA_INVALID, true), 'flash_failure');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('RoomType', $this->RoomType->read(null, $id));
    }

    function add() {
        $this->layout = 'ajax';
        if (!empty($this->data)) {
            if ($this->Helper->checkDouplicate('name', 'room_types', trim($this->data['RoomType']['name']))) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $this->RoomType->create();
                $user = $this->getCurrentUser();
                $this->data['RoomType']['name'] = trim($this->data['RoomType']['name']);
                $this->data['RoomType']['created_by'] = $user['User']['id'];
                $this->data['RoomType']['is_active'] = 1;
                if ($this->RoomType->save($this->data)) {                                        
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        $RoomTypeAccount = ClassRegistry::init('AccountType')->findById(9);
        $RoomTypeAccountId = $RoomTypeAccount['AccountType']['chart_account_id'];
        $this->set(compact('patientGroups', 'RoomTypeAccountId'));
    }

    function edit($id = null) {
        $this->layout = 'ajax';
        if (!$id && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit;        
        }
        if (!empty($this->data)) {
            if ($this->Helper->checkDouplicateEdit('name', 'room_types', $id, trim($this->data['RoomType']['name']))) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $user = $this->getCurrentUser();
                $this->data['RoomType']['name'] = trim($this->data['RoomType']['name']);
                $this->data['RoomType']['modified_by'] = $user['User']['id'];
                if ($this->RoomType->save($this->data)) {                   
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {                    
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;

                }
            }
        }
        if (empty($this->data)) {
            $this->data = $this->RoomType->read(null, $id);
        }
    }

    function delete($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        $dateTime = date("Y-m-d H:i:s");
        $this->RoomType->updateAll(
                array('RoomType.is_active' => "2", 'RoomType.modified' => "'$dateTime'", "RoomType.modified_by" => $user['User']['id']),
                array('RoomType.id' => $id)
        );
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }

}

?>