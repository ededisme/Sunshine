<?php

class Employee extends AppModel {
    var $name = 'Employee';
    var $belongsTo = array(
        'Street' => array(
            'className' => 'Street',
            'foreignKey' => 'street_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'created_by',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
}
?>