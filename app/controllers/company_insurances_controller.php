<?php

class CompanyInsurancesController extends AppController {

    var $name = 'CompanyInsurances';
    var $components = array('Helper');

    function index() {
        $this->layout = "ajax";
    }

    function ajax() {
        $this->layout = "ajax";
    }   
    
    function add() {
        $this->layout = "ajax";
        $this->loadModel('CompanyInsurance');        
        if (!empty($this->data)) {
            $result = array();
            if ($this->Helper->checkDouplicate('insurance_code', 'company_insurances', trim($this->data['CompanyInsurance']['insurance_code']))) {
                $result['msg'] = MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                $result['error'] = 2;
                echo json_encode($result);
                exit;
            } else {
                $user = $this->getCurrentUser();                               
                $this->data['CompanyInsurance']['name'] = trim($this->data['CompanyInsurance']['name']);
                $this->data['CompanyInsurance']['created_by'] = $user['User']['id'];
                $this->data['CompanyInsurance']['is_active'] = 1;                
                if ($this->CompanyInsurance->saveAll($this->data)) {  
                    
                    $lastInsertId = $this->CompanyInsurance->getLastInsertId();
                    // Employee Company
                    if (isset($this->data['CompanyInsurance']['company_id'])) {
                        for ($i = 0; $i < sizeof($this->data['CompanyInsurance']['company_id']); $i++) {
                            mysql_query("INSERT INTO company_insurance_companies (company_insurance_id, company_id) VALUES ('" . $lastInsertId . "','" . $this->data['CompanyInsurance']['company_id'][$i] . "')");
                        }
                    }
                    
                    $result['msg'] = MESSAGE_DATA_HAS_BEEN_SAVED;
                    $result['error'] = 1;
                    echo json_encode($result);
                    exit;
                } else {
                    $result['msg'] = MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    $result['error'] = 2;
                    echo json_encode($result);
                    exit;
                }
            }
        }
        if(empty($this->data)){            
            $code = $this->Helper->getAutoGenerateCompanyInsuranceCode();   
            $user = $this->getCurrentUser();
            
            $companies = ClassRegistry::init('Company')->find('list',
                            array(
                                'joins' => array(
                                    array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                    )
                                ),
                                'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                            )
            );     
            $groupInsurances = ClassRegistry::init('GroupInsurance')->find('list', array('conditions' => 'is_active=1'));                   
            $this->set(compact('code', 'companies', 'groupInsurances'));            
        }
    }

    function edit($id=null) {
        $this->layout = 'ajax';
        $this->loadModel('Position');
        if (!empty($this->data)) {
            if ($this->Helper->checkDouplicateEdit('insurance_code', 'company_insurances', $id, trim($this->data['CompanyInsurance']['insurance_code']))) {
                $result['msg'] = MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                $result['error'] = 2;
                echo json_encode($result);
                exit;
            } else {
                $user = $this->getCurrentUser();               
                $this->data['CompanyInsurance']['name'] = trim($this->data['CompanyInsurance']['name']);
                $this->data['CompanyInsurance']['modified_by'] = $user['User']['id'];
                $this->data['CompanyInsurance']['is_active'] = 1;
                if ($this->CompanyInsurance->saveAll($this->data)) {
                   
                    // Employee Company
                    mysql_query("DELETE FROM company_insurance_companies WHERE company_insurance_id=" . $id);
                    if (isset($this->data['CompanyInsurance']['company_id'])) {
                        for ($i = 0; $i < sizeof($this->data['CompanyInsurance']['company_id']); $i++) {
                            mysql_query("INSERT INTO company_insurance_companies (company_insurance_id, company_id) VALUES ('" . $id . "','" . $this->data['CompanyInsurance']['company_id'][$i] . "')");
                        }
                    }
                    
                    $result['msg'] = MESSAGE_DATA_HAS_BEEN_SAVED;
                    $result['error'] = 1;
                    echo json_encode($result);
                    exit;
                } else {
                    $result['msg'] = MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    $result['error'] = 2;
                    echo json_encode($result);
                    exit;
                }
            }
        }
        if (empty($this->data)) {
            $user = $this->getCurrentUser();            
            $this->data = $this->CompanyInsurance->read(null, $id);
            $code = $this->Helper->getAutoGenerateCompanyInsuranceCode();                  
            $companySellecteds = ClassRegistry::init('CompanyInsuranceCompany')->find('list', array('fields' => array('id', 'company_id'), 'order' => 'id', 'conditions' => array('company_insurance_id' => $id)));
            $companySellected = array();
            foreach ($companySellecteds as $cs) {
                array_push($companySellected, $cs);
            }
            $companies = ClassRegistry::init('Company')->find('list',
                            array(
                                'joins' => array(
                                    array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                    )
                                ),
                                'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                            )
            );       
            $groupInsurances = ClassRegistry::init('GroupInsurance')->find('list', array('conditions' => 'is_active=1'));    
            $this->set(compact('code', 'companies', 'companySellected', 'groupInsurances'));
        }
    }

    function view($id = null) {
        $this->layout = 'ajax';
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $this->set('insurance', $this->CompanyInsurance->read(null, $id));
    }

    function delete($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        $this->data['CompanyInsurance']['modified'] = $user['User']['id'];
        $this->data['CompanyInsurance']['modified_by'] = date("Y-m-d H:i:s");
        $this->data['CompanyInsurance']['id'] = $id;
        $this->data['CompanyInsurance']['is_active'] = 0;
        if($this->CompanyInsurance->saveAll($this->data)){
            echo MESSAGE_DATA_HAS_BEEN_DELETED;
            exit;
        }else{
            $result['msg'] = MESSAGE_DATA_COULD_NOT_BE_SAVED;
            $result['error'] = 2;
            echo json_encode($result);
            exit;
        }
        
    }
    
    function searchCompanyInsurance() {
        Configure::write('debug', 0);
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if($user['User']['id'] == 1){
            $userPermission = "";
        }else{
            $userPermission = 'CompanyInsurance.id IN (SELECT CompanyInsurance_id FROM CompanyInsurance_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id ='.$user['User']['id'].'))';
        }
        $CompanyInsurances = $this->CompanyInsurance->find('all', array(
                    'conditions' => array('OR' => array(
                            'CompanyInsurance.name LIKE' => '%' . $this->params['url']['q'] . '%',
                            'CompanyInsurance.CompanyInsurance_code LIKE' => '%' . $this->params['url']['q'] . '%'
                        ), 'CompanyInsurance.is_active' => 1, $userPermission
                    ),
                ));

        $this->set(compact('CompanyInsurances'));
    }

}

?>