<?php

class DoctorConsultationsController extends AppController {

    var $name = 'DoctorConsultations';
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
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $this->data = $this->DoctorConsultation->read(null, $id);
    }

    function add() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            if ($this->Helper->checkDouplicate('name', 'doctor_consultations', $this->data['DoctorConsultation']['name'])) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $this->DoctorConsultation->create();
                $this->data['DoctorConsultation']['created_by'] = $user['User']['id'];
                $this->data['DoctorConsultation']['is_active'] = 1;
                if ($this->DoctorConsultation->save($this->data)) {
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        $sexes   = array('Male' => 'Male', 'Female' => 'Female');
        $this->set(compact('sexes'));
    }

    function edit($id = null) {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!$id && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        if (!empty($this->data)) {
            if ($this->Helper->checkDouplicateEdit('name', 'doctor_consultations', $id, $this->data['DoctorConsultation']['name'])) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $this->data['DoctorConsultation']['modified_by'] = $user['User']['id'];
                if ($this->DoctorConsultation->save($this->data)) {
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        $this->data = $this->DoctorConsultation->read(null, $id);
        $sexes   = array('Male' => 'Male', 'Female' => 'Female');
        $this->set(compact('sexes'));
    }

    function delete($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        mysql_query("UPDATE `doctor_consultations` SET `is_active`=2, `modified`='".date("Y-m-d H:i:s")."', `modified_by`=".$user['User']['id']." WHERE `id`=".$id.";");
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }
}

?>