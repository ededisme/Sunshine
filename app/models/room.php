<?php

class Room extends AppModel {

    var $name = 'Room';
    var $belongsTo = array(
            'RoomType' => array(
                            'className' => 'RoomType',
                            'foreignKey' => 'room_type_id',
                            'conditions' => '',
                            'fields' => '',
                            'order' => ''
            ),
            'RoomFloor' => array(
                            'className' => 'RoomFloor',
                            'foreignKey' => 'room_floor_id',
                            'conditions' => '',
                            'fields' => '',
                            'order' => ''
            ),
            'Company' => array(
                            'className' => 'Company',
                            'foreignKey' => 'company_id',
                            'conditions' => '',
                            'fields' => '',
                            'order' => ''
            )

    );

}

?>