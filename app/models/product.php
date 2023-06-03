<?php

class Product extends AppModel {
    var $name = 'Product';
    var $belongsTo = array(
        'Color' => array(
            'className' => 'Color',
            'foreignKey' => 'color_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
}

?>