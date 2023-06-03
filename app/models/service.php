<?php

class Service extends AppModel {
    var $name = 'Service';
    var $belongsTo = array(
        'Section' => array(
            'className' => 'Section',
            'foreignKey' => 'section_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
}
?>