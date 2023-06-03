<?php

class CreditMemoWithSale extends AppModel {
    var $name = 'CreditMemoWithSale';
    var $belongsTo = array(
        'SalesOrder' => array(
            'className' => 'SalesOrder',
            'foreignKey' => 'sales_order_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'CreditMemo' => array(
            'className' => 'CreditMemo',
            'foreignKey' => 'credit_memo_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
}
?>