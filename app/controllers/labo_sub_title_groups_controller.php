<?php
class LaboSubTitleGroupsController extends AppController {

    var $name = 'LaboSubTitleGroups';
    var $components = array('Helper');    

    function index(){
        $this->layout = 'ajax';
    }

    function ajax(){
        $this->layout = 'ajax';
    }
    
    function add(){
        $this->layout = 'ajax';
        if(!empty($this->data)) {
            if ($this->Helper->checkDouplicate('name', 'labo_sub_title_groups', trim($this->data['LaboSubTitleGroup']['name']))) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $id_lists = "";
                $user = $this->getCurrentUser();                                   
                $this->data['LaboSubTitleGroup']['name'] = trim($this->data['LaboSubTitleGroup']['name']);
                $this->data['LaboSubTitleGroup']['created_by'] = $user['User']['id'];
                $this->data['LaboSubTitleGroup']['is_active'] = 1;

                if ($this->LaboSubTitleGroup->save($this->data)) {
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
        $this->LoadModel('LaboSubTitleGroups');
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        $this->LaboSubTitleGroups->updateAll(
                array('LaboSubTitleGroups.is_active' => "2", 'LaboSubTitleGroups.modified_by' => $user['User']['id']),
                array('LaboSubTitleGroups.id' => $id)
        );
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }
    function edit($id) {
        $this->layout = 'ajax';
        if(!empty($this->data)) {
            if ($this->Helper->checkDouplicateEdit('name', 'labo_sub_title_groups', $id, trim($this->data['LaboSubTitleGroup']['name']))) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $user = $this->getCurrentUser();
                $this->data['LaboSubTitleGroup']['name'] = trim($this->data['LaboSubTitleGroup']['name']);
                $this->data['LaboSubTitleGroup']['modified_by'] = $user['User']['id'];
                if ($this->LaboSubTitleGroup->save($this->data)) {
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                        exit;
                } else {
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        $this->data = $this->LaboSubTitleGroup->read(null, $id);
        $titles = $this->data;
        $this->set(compact("titles"));
    }

    function view($id) {     
        $this->layout = 'ajax'; 
        if(!empty($id)) {              
            $laboItemGroup = $this->LaboSubTitleGroup->find("first", array("conditions" => array("LaboSubTitleGroup.id"=>$id)));            
            $laboItemGroups = $this->LaboSubTitleGroup->getLaboSubTitleGroupsDetail($id, $laboItemGroup['LaboSubTitleGroup']['labo_item_group_id']);            
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
