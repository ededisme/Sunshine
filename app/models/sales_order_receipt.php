<?php

class SalesOrderReceipt extends AppModel {

    var $name = 'SalesOrderReceipt';

    var $belongsTo = array(
        'SalesOrder' => array(
            'className' => 'SalesOrder',
            'foreignKey' => 'sales_order_id',
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