<?php

class CreditMemoService extends AppModel {

    var $name = 'CreditMemoService';

    var $belongsTo = array(
        'CreditMemo' => array(
            'className' => 'CreditMemo',
            'foreignKey' => 'credit_memo_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Service' => array(
            'className' => 'Service',
            'foreignKey' => 'service_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Discount' => array(
            'className' => 'Discount',
            'foreignKey' => 'discount_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
}

?>