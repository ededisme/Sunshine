<?php

class LandingCostReceipt extends AppModel {

    var $name = 'LandingCostReceipt';

    var $belongsTo = array(
        'LandingCost' => array(
            'className' => 'LandingCost',
            'foreignKey' => 'landing_cost_id',
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
