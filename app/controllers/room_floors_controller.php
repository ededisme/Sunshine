<?php

class RoomFloorsController extends AppController {

    var $name = 'RoomFloors';
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
        $this->set('RoomFloor', $this->RoomFloor->read(null, $id));
    }

    function add() {
        $this->layout = 'ajax';
        if (!empty($this->data)) {
            if ($this->Helper->checkDouplicate('name', 'room_types', trim($this->data['RoomFloor']['name']))) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $this->RoomFloor->create();
                $user = $this->getCurrentUser();
                $this->data['RoomFloor']['name'] = trim($this->data['RoomFloor']['name']);
                $this->data['RoomFloor']['created_by'] = $user['User']['id'];
                $this->data['RoomFloor']['is_active'] = 1;
                if ($this->RoomFloor->save($this->data)) {                                        
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        $RoomFloorAccount = ClassRegistry::init('AccountType')->findById(9);
        $RoomFloorAccountId = $RoomFloorAccount['AccountType']['chart_account_id'];
        $this->set(compact('patientGroups', 'RoomFloorAccountId'));
    }

    function edit($id = null) {
        $this->layout = 'ajax';
        if (!$id && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit;        
        }
        if (!empty($this->data)) {
            if ($this->Helper->checkDouplicateEdit('name', 'room_types', $id, trim($this->data['RoomFloor']['name']))) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $user = $this->getCurrentUser();
                $this->data['RoomFloor']['name'] = trim($this->data['RoomFloor']['name']);
                $this->data['RoomFloor']['modified_by'] = $user['User']['id'];
                if ($this->RoomFloor->save($this->data)) {                   
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {                    
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;

                }
            }
        }
        if (empty($this->data)) {
            $this->data = $this->RoomFloor->read(null, $id);
        }
    }

    function delete($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        $dateTime = date("Y-m-d H:i:s");
        $this->RoomFloor->updateAll(
                array('RoomFloor.is_active' => "2", 'RoomFloor.modified' => "'$dateTime'", "RoomFloor.modified_by" => $user['User']['id']),
                array('RoomFloor.id' => $id)
        );
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }

}

?>