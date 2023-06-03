<?php
class LaboItemCategory extends AppModel {
    var $name = 'LaboItemCategory';


    var $hasMany = array(
            'LaboItem' => array(
                            'className' => 'LaboItem',
                            'foreignKey' => 'category',
                            'conditions' => '',
                            'fields' => '',
                            'order' => '')
    );
}
?>