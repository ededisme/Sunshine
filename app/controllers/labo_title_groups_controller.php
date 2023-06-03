<?php
class LaboTitleGroupsController extends AppController {

    var $name = 'LaboTitleGroups';
    var $components = array('LaboTitleGroupHandler', 'Helper');    

    function index(){
        $this->layout = 'ajax';
    }

    function ajax(){
        $this->layout = 'ajax';
    }
    
    function add(){
        $this->layout = 'ajax';
        if(!empty($this->data)) {
            if ($this->Helper->checkDouplicate('name', 'labo_title_groups', trim($this->data['LaboTitleGroup']['name']))) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $id_lists = "";
                $user = $this->getCurrentUser();                        
                foreach($this->data['LaboTitleGroup']['labo_item_group_id'] as $laboItemId) {
                    if(!empty($laboItemId)) {
                        $id_lists .= $laboItemId.", ";
                    }
                }

                $id_lists = substr($id_lists, 0, strlen($id_lists)-2);
                $this->data['LaboTitleGroup']['name'] = trim($this->data['LaboTitleGroup']['name']);
                $this->data['LaboTitleGroup']['labo_item_group_id'] = $id_lists;
                $this->data['LaboTitleGroup']['created_by'] = $user['User']['id'];
                $this->data['LaboTitleGroup']['is_active'] = 1;

                if ($this->LaboTitleGroup->save($this->data)) {
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
        $this->layout = 'ajax';
        $this->LoadModel('LaboTitleGroups');
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        $this->LaboTitleGroups->updateAll(
                array('LaboTitleGroups.is_active' => "2", 'LaboTitleGroups.modified_by' => $user['User']['id']),
                array('LaboTitleGroups.id' => $id)
        );
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }
    function edit($id) {
        $this->layout = 'ajax';
        if(!empty($this->data)) {
            if ($this->Helper->checkDouplicateEdit('name', 'labo_title_groups', $id, trim($this->data['LaboTitleGroup']['name']))) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $id_lists = "";
                $user = $this->getCurrentUser($this->Session);
                foreach($this->data['LaboTitleGroup']['labo_item_group_id'] as $laboItemId) {
                    if(!empty($laboItemId)) {
                        $id_lists .= $laboItemId.", ";
                    }
                }
                $id_lists = substr($id_lists, 0, strlen($id_lists)-2);
                $this->data['LaboTitleGroup']['name'] = trim($this->data['LaboTitleGroup']['name']);
                $this->data['LaboTitleGroup']['labo_item_group_id'] = $id_lists;
                $this->data['LaboTitleGroup']['modified_by'] = $user['User']['id'];

                if ($this->LaboTitleGroup->save($this->data)) {
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                        exit;
                } else {
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        $this->data = $this->LaboTitleGroup->read(null, $id);
        $titles = $this->data;
        $options = $this->LaboTitleGroupHandler->getOptionsLaboItem($this->data['LaboTitleGroup']['labo_item_group_id']);
        $this->set(compact("titles"));
        $this->set(compact("options"));
    }

    function view($id) {     
        $this->layout = 'ajax'; 
        if(!empty($id)) {              
            $laboItemGroup = $this->LaboTitleGroup->find("first", array("conditions" => array("LaboTitleGroup.id"=>$id)));            
            $laboItemGroups = $this->LaboTitleGroup->getLaboTitleGroupsDetail($id, $laboItemGroup['LaboTitleGroup']['labo_item_group_id']);            
            if(empty($laboItemGroups)) {
                $this->redirect(array('action' => 'index'));
            }
            $this->set(compact('laboItemGroups'));
        }else {            
            $this->redirect(array('action' => 'index'));
        }
    }
}
?>