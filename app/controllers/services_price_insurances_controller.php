<?php

class ServicesPriceInsurancesController extends AppController {

    var $name = 'ServicesPriceInsurances';
    var $components = array('Helper');

    function index() {        
        $this->layout = 'ajax';
        $this->loadModel('CompanyInsurance');
        $user = $this->getCurrentUser();
        // User Activity
        $companies = ClassRegistry::init('Company')->find('list',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        $companyInsurances = $this->CompanyInsurance->find("all", array('conditions' => array('CompanyInsurance.is_active' => 1)));
        $this->set(compact('companies', 'companyInsurances'));
    }        
    
    function view($id = null) {
        $this->layout = 'ajax';
        if (!$id) {
            $this->Session->setFlash(__(MESSAGE_DATA_INVALID, true), 'flash_failure');
            $this->redirect(array('action' => 'index'));
        }
        $servicesPriceInsurance = $this->ServicesPriceInsurance->find('first', array('conditions' => array('ServicesPriceInsurance.is_active' => 1, 'ServicesPriceInsurance.id' => $id),
            'fields' => array('Section.name, Service.name, ServicesPriceInsurance.id, CompanyInsurance.name'),
            'joins' => array(
                array('table' => 'services',
                    'alias' => 'Service',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Service.id = ServicesPriceInsurance.service_id'
                    )
                ),
                array('table' => 'sections',
                    'alias' => 'Section',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Section.id = Service.section_id'
                    )
                ),
                array('table' => 'company_insurances',
                    'alias' => 'CompanyInsurance',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'CompanyInsurance.id = ServicesPriceInsurance.company_insurance_id'
                    )
                )
        )));
        $this->set('servicesPriceInsurance', $servicesPriceInsurance);        
    }

    function ajax($companyInsurance = 'all', $company = 'all') {
        $this->layout = 'ajax';
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
        $this->set(compact('companies', 'companyInsurance', 'company'));
    }

    function add() {
        $this->layout = 'ajax';
        if (!empty($this->data)) {                        
            $user = $this->getCurrentUser();
            $save = 0;
            for ($i = 0; $i < sizeof($this->data['ServicesPriceInsurance']['company_insurance_id']); $i++) {
                $this->ServicesPriceInsurance->create();
                $data['ServicesPriceInsurance']['service_id'] = $this->data['ServicesPriceInsurance']['service_id'];
                $data['ServicesPriceInsurance']['company_insurance_id'] = $this->data['ServicesPriceInsurance']['company_insurance_id'][$i];
                $data['ServicesPriceInsurance']['created_by'] = $user['User']['id'];            
                if($this->ServicesPriceInsurance->save($data['ServicesPriceInsurance'])){                    
                    $servicesPriceInsuranceId = $this->ServicesPriceInsurance->getLastInsertId();
                    if (isset($this->data['ServicesPriceInsurance']['patient_group_id'])) {
                        for ($j = 0; $j < sizeof($this->data['ServicesPriceInsurance']['patient_group_id']); $j++) {
                            mysql_query("INSERT INTO services_price_insurance_patient_group_details (services_price_insurance_id,patient_group_id,unit_price) VALUES ('" . $servicesPriceInsuranceId . "','" . $this->data['ServicesPriceInsurance']['patient_group_id'][$j] . "','" . $this->data['ServicesPriceInsurance']['unit_price'][$j] . "')");
                        }
                    }   
                    $save = 1;
                }else{
                    $save = 0;
                }
            }
            if($save == 1){
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                exit;
            }            
        }
        $patientGroups = ClassRegistry::init('PatientGroup')->find('list', array('conditions' => 'status=1'));                
        $services = ClassRegistry::init('Service')->find('all', array('conditions' => 'Service.is_active=1'));
        $companyInsurances = ClassRegistry::init('CompanyInsurance')->find('list', array('conditions' => 'is_active=1'));
        $this->set(compact('patientGroups', 'services', 'companyInsurances'));
    }

    function edit($id = null) {
        $this->layout = 'ajax';
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__(MESSAGE_DATA_INVALID, true), 'flash_failure');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {            
            $user = $this->getCurrentUser();
            $servicesPriceInsuranceId = $this->data['ServicesPriceInsurance']['id'];
            $patientGroupId = $this->data['ServicesPriceInsurance']['patient_group_id'];
            $unitPrice = $this->data['ServicesPriceInsurance']['unit_price'];
            $this->loadModel('ServicesPriceInsurancePatientGroupDetail');
            
            $this->data['ServicesPriceInsurance']['modified_by'] = $user['User']['id'];            
            if ($this->ServicesPriceInsurance->saveAll($this->data['ServicesPriceInsurance'])) {  
                
                $this->ServicesPriceInsurancePatientGroupDetail->updateAll(
                        array('ServicesPriceInsurancePatientGroupDetail.is_active' => "0"),
                        array('ServicesPriceInsurancePatientGroupDetail.id' => $this->data['ServicesPriceInsurancePatientGroupDetail']['id'])
                );
                $data['ServicesPriceInsurancePatientGroupDetail']['services_price_insurance_id'] = $servicesPriceInsuranceId;
                $data['ServicesPriceInsurancePatientGroupDetail']['patient_group_id'] = $patientGroupId;
                $data['ServicesPriceInsurancePatientGroupDetail']['unit_price'] = $unitPrice;
                $data['ServicesPriceInsurancePatientGroupDetail']['modified_by'] = $user['User']['id'];            
                if ($this->ServicesPriceInsurancePatientGroupDetail->save($data['ServicesPriceInsurancePatientGroupDetail'])) {
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                }
                
            }else{
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                exit;
            }
        }        
        $patientGroups = ClassRegistry::init('PatientGroup')->find('list', array('conditions' => 'status=1'));                
        $services = ClassRegistry::init('Service')->find('all', array('conditions' => 'Service.is_active=1'));
        $companyInsurances = ClassRegistry::init('CompanyInsurance')->find('list', array('conditions' => 'is_active=1'));
        $sections = ClassRegistry::init('Section')->find('first', array('conditions' => 'Section.id='.$services[0]['Service']['section_id']));        
        $servicesPriceInsurance = $this->ServicesPriceInsurance->find('first', array('conditions' => array('ServicesPriceInsurancePatientGroupDetail.is_active' => 1, 'ServicesPriceInsurancePatientGroupDetail.id' => $id),
            'fields' => array('ServicesPriceInsurance.*,ServicesPriceInsurancePatientGroupDetail.*'),
            'joins' => array(
                array('table' => 'services_price_insurance_patient_group_details',
                    'alias' => 'ServicesPriceInsurancePatientGroupDetail',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'ServicesPriceInsurance.id = ServicesPriceInsurancePatientGroupDetail.services_price_insurance_id'
                    )
                )
        )));        
        $this->set(compact('patientGroups', 'services', 'companyInsurances', 'sections', 'servicesPriceInsurance'));
    }

    function cloneServicePrice() {
        $this->layout = 'ajax';
        $this->loadModel('ServicesPriceInsurance');
        $this->loadModel('CompanyInsurance');
        if (!empty($this->data)) {                        
            $user = $this->getCurrentUser();
            $save = 0;
            if (isset($this->data['ServicesPriceInsurance']['patient_group_id'])) {                                
                $queryCompanyInsurance = mysql_query("SELECT * FROM services_price_insurances WHERE is_active = 1 AND company_insurance_id = '".$this->data['ServicesPriceInsurance']['company_insurance_id']."' ");
                while ($rowCompanyInsurance = mysql_fetch_array($queryCompanyInsurance)) {                                    
                    $queryCheckCompanyInsurance = mysql_query("SELECT id FROM services_price_insurances WHERE is_active = 1 AND service_id = '".$rowCompanyInsurance['service_id']."' AND company_insurance_id = '".$this->data['ServicesPriceInsurance']['company_insurance_id_to']."' ");
                    if(!mysql_num_rows($queryCheckCompanyInsurance)){
                        $this->ServicesPriceInsurance->create();
                        $data['ServicesPriceInsurance']['service_id'] = $rowCompanyInsurance['service_id'];
                        $data['ServicesPriceInsurance']['company_insurance_id'] = $this->data['ServicesPriceInsurance']['company_insurance_id_to'];
                        $data['ServicesPriceInsurance']['created_by'] = $user['User']['id'];            
                        if($this->ServicesPriceInsurance->save($data['ServicesPriceInsurance'])){                    
                            $servicesPriceInsuranceId = $this->ServicesPriceInsurance->getLastInsertId();
                            if (isset($this->data['ServicesPriceInsurance']['patient_group_id'])) {                        
                                for ($j = 0; $j < sizeof($this->data['ServicesPriceInsurance']['patient_group_id']); $j++) {                            
                                    $queryCompanyInsurancePatientGroup = mysql_query("SELECT * FROM services_price_insurance_patient_group_details WHERE is_active = 1 AND services_price_insurance_id = '".$rowCompanyInsurance['id']."' AND patient_group_id = '".$this->data['ServicesPriceInsurance']['patient_group_id'][$j]."' ");
                                    while ($rowCompanyInsurancePatientGroup = mysql_fetch_array($queryCompanyInsurancePatientGroup)) {                                
                                        mysql_query("INSERT INTO services_price_insurance_patient_group_details (services_price_insurance_id,patient_group_id,unit_price) VALUES ('" . $servicesPriceInsuranceId . "','" . $this->data['ServicesPriceInsurance']['patient_group_id'][$j] . "','" . $rowCompanyInsurancePatientGroup['unit_price'] . "')");
                                    }                                                        
                                }
                            }   
                            $save = 1;
                        }else{
                            $save = 0;
                        }
                    }else{
                        while ($resultCheckCompanyInsurance = mysql_fetch_array($queryCheckCompanyInsurance)) {
                            $servicesPriceInsuranceId = $resultCheckCompanyInsurance['id'];
                            if (isset($this->data['ServicesPriceInsurance']['patient_group_id'])) {                        
                                for ($j = 0; $j < sizeof($this->data['ServicesPriceInsurance']['patient_group_id']); $j++) {
                                    $queryCheckPatientGroup = mysql_query("SELECT * FROM services_price_insurance_patient_group_details WHERE is_active = 1 AND services_price_insurance_id = '".$servicesPriceInsuranceId."' AND patient_group_id = '".$this->data['ServicesPriceInsurance']['patient_group_id'][$j]."' ");
                                    if(!mysql_num_rows($queryCheckPatientGroup)){
                                        $queryCompanyInsurancePatientGroup = mysql_query("SELECT * FROM services_price_insurance_patient_group_details WHERE is_active = 1 AND services_price_insurance_id = '".$rowCompanyInsurance['id']."' AND patient_group_id = '".$this->data['ServicesPriceInsurance']['patient_group_id'][$j]."' ");
                                        while ($rowCompanyInsurancePatientGroup = mysql_fetch_array($queryCompanyInsurancePatientGroup)) {                                
                                            mysql_query("INSERT INTO services_price_insurance_patient_group_details (services_price_insurance_id,patient_group_id,unit_price) VALUES ('" . $servicesPriceInsuranceId . "','" . $this->data['ServicesPriceInsurance']['patient_group_id'][$j] . "','" . $rowCompanyInsurancePatientGroup['unit_price'] . "')");
                                        }  
                                    }                                                                                         
                                }
                                $save = 1;
                            }else{
                                $save = 0;
                            }
                            
                        }
                    }
                }
            }
                        
            if($save == 1){
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                exit;
            }            
        }
        $patientGroups = ClassRegistry::init('PatientGroup')->find('list', array('conditions' => 'status=1'));        
        $companyInsurances = $this->CompanyInsurance->find('list', array('conditions' => array('CompanyInsurance.is_active' => 1, 'ServicesPriceInsurance.is_active' => 1),
            'fields' => array('CompanyInsurance.name'),
            'joins' => array(
                array('table' => 'services_price_insurances',
                    'alias' => 'ServicesPriceInsurance',
                    'type' => 'INNER',
                    'conditions' => array(
                        'CompanyInsurance.id = ServicesPriceInsurance.company_insurance_id'
                    )
                )
            ),
            'group' => '`CompanyInsurance`.`id`',
        ));         
        $this->set(compact('patientGroups', 'services', 'companyInsurances'));
    }
    
    function delete($id = null) {
        if (!$id) {            
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $this->loadModel('ServicesPriceInsurancePatientGroupDetail');
        $user = $this->getCurrentUser();
        $this->ServicesPriceInsurancePatientGroupDetail->updateAll(
                array('ServicesPriceInsurancePatientGroupDetail.is_active' => "2"),
                array('ServicesPriceInsurancePatientGroupDetail.id' => $id)
        );
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }
    
    function deleteServicePrice($id = null) {
        $id = $_GET['id'];
        if (!$id) {            
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $this->loadModel('ServicesPriceInsurancePatientGroupDetail');
        $user = $this->getCurrentUser();
        for ($i = 0; $i < sizeof($id); $i++) {              
            $this->ServicesPriceInsurancePatientGroupDetail->updateAll(
                array('ServicesPriceInsurancePatientGroupDetail.is_active' => "2"),
                array('ServicesPriceInsurancePatientGroupDetail.id' => $id[$i])
            );
        }  
        
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }
    
    function exportExcel($companyId = 'all', $companyInsuranceId = 'all'){
        $this->layout = 'ajax';
        if (isset($_POST['action']) && $_POST['action'] == 'export') {
            $user = $this->getCurrentUser();
            $filename = "public/report/ServicesPriceInsuranceExport.csv";
            $fp = fopen($filename, "wb");
            $excelContent = 'ServicesPriceInsurances' . "\n\n";
            $excelContent .= TABLE_NO . "\t" . TABLE_COMPANY. "\t" . TABLE_SECTION. "\t" . TABLE_NAME. "\t" . PATIENT_TYPE. "\t" . TABLE_COMPANY_INSURANCE_NAME. "\t" . SALES_ORDER_UNIT_PRICE;
            $condition = "";
            if ($companyInsuranceId != "all") {
                $condition .= " AND services_price_insurances.company_insurance_id='" . $companyInsuranceId . "'";
            }
            if ($companyId != "all") {
                $condition .= " AND company_insurance_companies.company_id='" . $companyId . "'";
            }
            $query = mysql_query('SELECT services_price_insurance_patient_group_details.id, (SELECT name FROM companies WHERE id=company_insurance_companies.company_id), (SELECT name FROM sections WHERE id=(SELECT section_id FROM services WHERE id=services_price_insurances.service_id)) AS sectionName, (SELECT name FROM services WHERE id=services_price_insurances.service_id) As serviceName, (SELECT name FROM patient_groups WHERE id=services_price_insurance_patient_group_details.patient_group_id), (SELECT name FROM company_insurances WHERE id=services_price_insurances.company_insurance_id), services_price_insurance_patient_group_details.unit_price '
                               . ' FROM services_price_insurances INNER JOIN services_price_insurance_patient_group_details ON services_price_insurances.id = services_price_insurance_patient_group_details.services_price_insurance_id INNER JOIN company_insurance_companies ON company_insurance_companies.company_insurance_id = services_price_insurances.company_insurance_id '
                               . ' WHERE services_price_insurances.is_active=1 AND services_price_insurance_patient_group_details.is_active=1'.$condition.' ORDER BY services_price_insurances.company_insurance_id, serviceName, services_price_insurances.service_id ASC');
            $index = 1;
            while ($data = mysql_fetch_array($query)) {
                $excelContent .= "\n" . $index++ . "\t" . $data[1] . $data[2]. "\t" . $data[3]. "\t" . $data[4]. "\t" . $data[5]. "\t". $data[6]. "\t";
            }
            $excelContent = chr(255) . chr(254) . @mb_convert_encoding($excelContent, 'UTF-16LE', 'UTF-8');
            fwrite($fp, $excelContent);
            fclose($fp);
            exit();
        }
    }

}

?>