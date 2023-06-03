<?php

class Queue extends AppModel {

    var $name = 'Queues';
    var $belongsTo = array(
        'Patient' => array(
            'className' => 'Patient',
            'foreignKey' => 'patient_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    function getQueuePatientById($queueId) {
        $conditions = array(
            'conditions' => array('Queue.id' => $queueId)
        );
        $queueList = $this->find('first', $conditions);
        return $queueList;
    }

}

?>