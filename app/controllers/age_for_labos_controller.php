<?php
class AgeForLabosController extends AppController {

    var $name = 'AgeForLabos';
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
            if ($this->Helper->checkDouplicate('name', 'age_for_labos', trim($this->data['AgeForLabo']['name']))) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {            
                $from = ($this->data['AgeForLabo']['from_year']*12)+$this->data['AgeForLabo']['from_month'];
                $to = ($this->data['AgeForLabo']['to_year']*12)+$this->data['AgeForLabo']['to_month'];            
                $user = $this->getCurrentUser();                        
                $this->data['AgeForLabo']['from'] = $from;
                $this->data['AgeForLabo']['to'] = $to;
                $this->data['AgeForLabo']['created_by'] = $user['User']['id'];                                               
                if ($this->AgeForLabo->save($this->data)) {
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        $sexes = array('M' => GENERAL_MALE, 'F' => GENERAL_FEMALE);
        $this->set(compact('sexes'));
    }
    function delete($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $this->AgeForLabo->updateAll(
                array('AgeForLabo.is_active' => "0"),
                array('AgeForLabo.id' => $id)
        );
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }
    function edit($id) {
        $this->layout = 'ajax';
        if(!empty($this->data)) {
            if ($this->Helper->checkDouplicateEdit('name', 'age_for_labos', $id, trim($this->data['AgeForLabo']['name']))) {
                $this->Session->setFlash(__(MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM, true), 'flash_failure');
            } else {
                $from = ($this->data['AgeForLabo']['from_year']*12)+$this->data['AgeForLabo']['from_month'];
                $to = ($this->data['AgeForLabo']['to_year']*12)+$this->data['AgeForLabo']['to_month'];
                $user = $this->getCurrentUser();            
                $this->data['AgeForLabo']['id'] = $id;
                $this->data['AgeForLabo']['modified_by'] = $user['User']['id'];
                $this->data['AgeForLabo']['from'] = $from;
                $this->data['AgeForLabo']['to'] = $to;            
                if ($this->AgeForLabo->save($this->data)) {
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        $this->data = $this->AgeForLabo->read(null, $id);
        $sexes = array('M' => GENERAL_MALE, 'F' => GENERAL_FEMALE);
        $this->set(compact('sexes'));
    }

}
?>