<?php

class PurchaseReceiveResult extends AppModel {
    var $name = 'PurchaseReceiveResult';
    var $belongsTo = array(
        'PurchaseOrder' => array(
            'className' => 'PurchaseOrder',
            'foreignKey' => 'purchase_order_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
}
?>