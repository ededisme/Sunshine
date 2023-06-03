<?php
class LaboItemCategoriesController extends AppController {

    var $name = 'LaboItemCategories';
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
            if ($this->Helper->checkDouplicate('name', 'labo_item_categories', trim($this->data['LaboItemCategory']['name']))) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $user = $this->getCurrentUser();
                $this->data['LaboItemCategory']['name'] = trim($this->data['LaboItemCategory']['name']);
                $this->data['LaboItemCategory']['created_by'] = $user['User']['id'];
                $this->data['LaboItemCategory']['is_active'] = 1;
                if ($this->LaboItemCategory->save($this->data)) {
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
        $this->LaboItemCategory->updateAll(
                array('LaboItemCategory.is_active' => "2", 'LaboItemCategory.modified_by' => $user['User']['id']),
                array('LaboItemCategory.id' => $id)
        );
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;       
    }
    function edit($id) {
        $this->layout = 'ajax';
        if(!empty($this->data)) {
            if ($this->Helper->checkDouplicateEdit('name', 'labo_item_categories', $id, trim($this->data['LaboItemCategory']['name']))) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $user = $this->getCurrentUser();
                $this->data['LaboItemCategory']['name'] = trim($this->data['LaboItemCategory']['name']);
                $this->data['LaboItemCategory']['modified_by'] = $user['User']['id'];
                if ($this->LaboItemCategory->save($this->data)) {
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        $this->data = $this->LaboItemCategory->read(null, $id);
    }
}
?>