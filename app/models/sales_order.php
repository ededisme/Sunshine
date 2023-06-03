<?php

class SalesOrder extends AppModel {
    var $name = 'SalesOrder';
    var $belongsTo = array(
        'PaymentTerm' => array(
            'className' => 'PaymentTerm',
            'foreignKey' => 'payment_term_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'LocationGroup' => array(
            'className' => 'LocationGroup',
            'foreignKey' => 'location_group_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Location' => array(
            'className' => 'Location',
            'foreignKey' => 'location_id',
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
        ),
        'Branch' => array(
            'className' => 'Branch',
            'foreignKey' => 'branch_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Patient' => array(
            'className' => 'Patient',
            'foreignKey' => 'patient_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Customer' => array(
            'className' => 'Customer',
            'foreignKey' => 'customer_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'CustomerContact' => array(
            'className' => 'CustomerContact',
            'foreignKey' => 'customer_contact_id',
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
        'Delivery' => array(
            'className' => 'Delivery',
            'foreignKey' => 'delivery_id',
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