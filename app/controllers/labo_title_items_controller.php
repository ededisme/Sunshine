<?php
class LaboTitleItemsController extends AppController {

    var $name = 'LaboTitleItems';
    var $components = array('Helper');

    function index() {
        $this->layout = 'ajax';
    }
    
    function ajax (){
        $this->layout = 'ajax';
    }

    function add() {
        $this->layout = 'ajax';
        if(!empty($this->data)) {
            if ($this->Helper->checkDouplicate('name', 'labo_title_items', trim($this->data['LaboTitleItem']['name']))) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $id_lists = "";
                $creator = $this->getCurrentUser($this->Session);
                $this->data['LaboTitleItem']['name'] = trim($this->data['LaboTitleItem']['name']);
                $this->data['LaboTitleItem']['created_by'] = $creator['User']['id'];
                $this->data['LaboTitleItem']['is_active'] = 1;
                if ($this->LaboTitleItem->save($this->data)) {
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
        $this->LaboTitleItem->updateAll(
                array('LaboTitleItem.is_active' => "2", 'LaboTitleItem.modified_by' => $user['User']['id']),
                array('LaboTitleItem.id' => $id)
        );
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }
    function edit($id) {
        $this->layout = 'ajax';
        if(!empty($this->data)) {
            if ($this->Helper->checkDouplicateEdit('name', 'labo_title_items', $id, trim($this->data['LaboTitleItem']['name']))) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $id_lists = "";
                $creator = $this->getCurrentUser($this->Session);
                $this->data['LaboTitleItem']['name'] = trim($this->data['LaboTitleItem']['name']);
                $this->data['LaboTitleItem']['modified_by'] = $creator['User']['id'];

                if ($this->LaboTitleItem->save($this->data)) {
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        $this->data = $this->LaboTitleItem->read(null, $id);
    }
}
?>