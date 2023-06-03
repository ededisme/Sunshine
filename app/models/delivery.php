<?php

class Delivery extends AppModel {
    var $name = 'Delivery';

    var $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'created_by',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );
}
?>