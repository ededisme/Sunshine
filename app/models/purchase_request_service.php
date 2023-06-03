<?php

class PurchaseRequestService extends AppModel {

    var $name = 'PurchaseRequestService';
    var $belongsTo = array(
        'Service' => array(
            'className' => 'Service',
            'foreignKey' => 'service_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
}
?>