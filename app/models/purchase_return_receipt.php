<?php

class PurchaseReturnReceipt extends AppModel {

    var $name = 'PurchaseReturnReceipt';

    var $belongsTo = array(
        'PurchaseReturn' => array(
            'className' => 'PurchaseReturn',
            'foreignKey' => 'purchase_return_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'ExchangeRate' => array(
            'className' => 'ExchangeRate',
            'foreignKey' => 'exchange_rate_id',
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
        ),
        'CurrencyCenter' => array(
            'className' => 'CurrencyCenter',
            'foreignKey' => 'currency_center_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
}

?>