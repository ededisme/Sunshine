<?php

class LaboItemsController extends AppController {

    var $name = 'LaboItems';
    var $components = array('Helper');

    function index() {
        $this->layout = 'ajax';
    }
    function ajax() {
        $this->layout = 'ajax';
    }

    function add() {
        $this->layout = 'ajax';
        $this->loadModel("LaboUnit");
        $this->loadModel("AgeForLabo");
        $this->loadModel("LaboItemCategory");
        $this->loadModel("LaboTitleItem");
        $this->loadModel('laboItemDetail');
        if (!empty($this->data)) {
            if ($this->Helper->checkDouplicate('name', 'labo_items', trim($this->data['LaboItem']['name']))) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $this->LaboItem->create();
                $user = $this->getCurrentUser();
                $this->data['LaboItem']['name'] = trim($this->data['LaboItem']['name']);
                $this->data['LaboItem']['created_by'] = $user['User']['id'];                
                $this->data['LaboItem']['item_unit'] = $this->data['LaboItem']['item_labo_unit'];                
                if ($this->LaboItem->save($this->data)) {
                    $labo_item_id = $this->LaboItem->getLastInsertID();
                    $normal_val = $this->data['LaboItem']['normal_value_type'];
                    if ($normal_val == "Number") {
                        for ($i = 0; $i < sizeof($this->data['LaboItem']['age_for_labo_id']); $i++) {
                            $this->laboItemDetail->create();
                            $labo_item_detail['LaboItem']['labo_item_id'] = $labo_item_id;
                            $labo_item_detail['LaboItem']['age_for_labo_id'] = $this->data['LaboItem']['age_for_labo_id'][$i];
                            $labo_item_detail['LaboItem']['min_value'] = $this->data['LaboItem']['min_value'][$i];
                            $labo_item_detail['LaboItem']['max_value'] = $this->data['LaboItem']['max_value'][$i];
                            $labo_item_detail['LaboItem']['created_by'] = $user['User']['id'];
                            if ($this->data['LaboItem']['min_value'][$i] != "" || $this->data['LaboItem']['max_value'][$i] != "") {
                                $this->laboItemDetail->save($labo_item_detail['LaboItem']);
                            }
                        }
                    }
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        $parents = $this->LaboItem->find('list', array(
                    'conditions' => array('parent_id' => NULL),
                    'fields' => array('id', 'name', 'Category.name'),
                    'joins' => array(
                        array(
                            'table' => 'labo_item_categories',
                            'alias' => 'Category',
                            'type' => 'INNER',
                            'foreignKey' => false,
                            'conditions' => array('LaboItem.category = Category.id'),
                        ),
                    ),
                    'order' => array('LaboItem.category', 'LaboItem.name')
                        )
        );
        $categories = $this->LaboItemCategory->find('list', array('fields' => array('LaboItemCategory.id', 'LaboItemCategory.name'), 'conditions' => array('LaboItemCategory.is_active' => 1)));
        $titleItems = $this->LaboTitleItem->find('list', array('fields' => array('LaboTitleItem.id', 'LaboTitleItem.name'), 'conditions' => array('LaboTitleItem.is_active' => 1)));        
        $normalValueTypes = array('Number' => 'Number', 'Positive / Negative' => 'Positive / Negative', 'Free Style' => 'Free Style');
        $itemLaboUnits = $this->LaboUnit->find("list", array("fields" => array("LaboUnit.name", "LaboUnit.name"), "conditions" => array("LaboUnit.is_active != 2")));
        $itemAgeForLabos = $this->AgeForLabo->find("all", array("conditions" => array("AgeForLabo.is_active != 0")));
        $this->set(compact('parents', 'categories', 'normalValueTypes', 'itemLaboUnits', 'itemAgeForLabos', 'titleItems'));
    }

    function edit($id = null) {
        $this->layout = 'ajax';
        $this->loadModel("LaboUnit");
        $this->loadModel("AgeForLabo");
        $this->loadModel("LaboItemCategory");
        $this->loadModel("LaboTitleItem");
        $this->loadModel('laboItemDetail');
        if (!$id && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        if (!empty($this->data)) {
            if ($this->Helper->checkDouplicateEdit('name', 'labo_items', $id, trim($this->data['LaboItem']['name']))) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $user = $this->getCurrentUser();
                $this->data['LaboItem']['name'] = trim($this->data['LaboItem']['name']);
                $this->data['LaboItem']['modified_by'] = $user['User']['id'];
                $normal_val = $this->data['LaboItem']['normal_value_type'];
                if ($normal_val != "Number") {
                    $this->data['LaboItem']['item_unit'] = "";
                }else{
                    $this->data['LaboItem']['item_unit'] = $this->data['LaboItem']['item_labo_unit'];
                }
                if ($this->LaboItem->save($this->data)) {
                    if ($normal_val == "Number") {
                        for ($i = 0; $i < sizeof($this->data['LaboItem']['age_for_labo_id']); $i++) {
                            $this->laboItemDetail->create();
                            $labo_item_detail['LaboItem']['labo_item_id'] = $id;
                            $labo_item_detail['LaboItem']['age_for_labo_id'] = $this->data['LaboItem']['age_for_labo_id'][$i];
                            $labo_item_detail['LaboItem']['min_value'] = $this->data['LaboItem']['min_value'][$i];
                            $labo_item_detail['LaboItem']['max_value'] = $this->data['LaboItem']['max_value'][$i];
                            $labo_item_detail['LaboItem']['created_by'] = $user['User']['id'];

                            $this->laboItemDetail->updateAll(
                                    array('laboItemDetail.status' => "0"), array('laboItemDetail.age_for_labo_id' => $this->data['LaboItem']['age_for_labo_id'][$i], 'laboItemDetail.labo_item_id' => $id)
                            );
                            if ($this->data['LaboItem']['min_value'][$i] != "" || $this->data['LaboItem']['max_value'][$i] != "") {
                                $this->laboItemDetail->save($labo_item_detail['LaboItem']);                                                                    
                            }
                        }
                        echo MESSAGE_DATA_HAS_BEEN_SAVED;
                        exit;
                    } else {
                        $this->laboItemDetail->updateAll(
                                array('laboItemDetail.status' => "0"), array('laboItemDetail.labo_item_id' => $id)
                        );
                        echo MESSAGE_DATA_HAS_BEEN_SAVED;
                        exit;
                    }            
                } else {
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        if (empty($this->data)) {
            $this->data = $this->LaboItem->read(null, $id);
        }
        $parents = $this->LaboItem->find('list', array(
                    'conditions' => array('parent_id' => NULL),
                    'fields' => array('id', 'name', 'Category.name'),
                    'joins' => array(
                        array(
                            'table' => 'labo_item_categories',
                            'alias' => 'Category',
                            'type' => 'INNER',
                            'foreignKey' => false,
                            'conditions' => array('LaboItem.category = Category.id'),
                        ),
                    ),
                    'order' => array('LaboItem.category', 'LaboItem.name')
                        )
        );
        $titleItems = $this->LaboTitleItem->find('list', array('fields' => array('LaboTitleItem.id', 'LaboTitleItem.name'), 'conditions' => array('LaboTitleItem.is_active' => 1)));        
        $categories = $this->LaboItemCategory->find('list', array('fields' => array('LaboItemCategory.id', 'LaboItemCategory.name'), 'conditions' => array('LaboItemCategory.is_active' => 1)));
        $normalValueTypes = array('Number' => 'Number', 'Positive / Negative' => 'Positive / Negative', 'Free Style' => 'Free Style');
        $itemLaboUnits = $this->LaboUnit->find("all", array("fields" => array("LaboUnit.id", "LaboUnit.name"), "conditions" => array("LaboUnit.is_active != 2")));
        $itemAgeForLabos = $this->AgeForLabo->find("all", array("conditions" => array("AgeForLabo.is_active != 0")));
        $labo_item_id = $id;
        $this->set(compact('parents', 'categories', 'normalValueTypes', 'itemLaboUnits', 'itemAgeForLabos', 'labo_item_id', 'titleItems'));
    }

    function delete($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        $this->LaboItem->updateAll(
                array('LaboItem.is_active' => "2", 'LaboItem.modified_by' => $user['User']['id']), array('LaboItem.id' => $id)
        );
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }

    function getLaboItem($parent_id = null) {
        if (!empty($parent_id)) {
            $laboItems = $this->LaboItem->find("all", array("conditions" => array("LaboItem.parent_id" => $parent_id, "LaboItem.is_active" => 1)));
            return $laboItems;
        }
    }

}

?>