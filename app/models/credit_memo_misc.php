<?php

class CreditMemoMisc extends AppModel {

    var $name = 'CreditMemoMisc';

    var $belongsTo = array(
        'CreditMemo' => array(
            'className' => 'CreditMemo',
            'foreignKey' => 'credit_memo_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Uom' => array(
            'className' => 'Uom',
            'foreignKey' => 'qty_uom_id',
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