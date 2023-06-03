<?php
class LaboItemGroupsController extends AppController {

    var $name = 'LaboItemGroups';
    var $components = array('LaboItemGroupHandler', 'Helper');    

    function index() {
        $this->layout = 'ajax';
    }
    function ajax() {
        $this->layout = 'ajax';
    }
    
    function insurance() {
        $this->layout = 'ajax';
        $this->loadModel('CompanyInsurance');
        $companyInsurances = $this->CompanyInsurance->find("all", array('conditions' => array('CompanyInsurance.is_active' => 1)));
        $this->set(compact('companyInsurances'));
    }
    function insuranceAjax($companyInsurance = 'all') {
        $this->layout = 'ajax';
        $this->set(compact('companyInsurance'));
    }
    
    function viewInsurance($id) {
        $this->layout = 'ajax';        
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $this->set('laboItemGroup', $this->LaboItemGroup->read(null, $id));       
    }
    function addInsurance() {
        $this->layout = 'ajax';
        $this->loadModel('CompanyInsurance');
        $this->loadModel('LaboItemInsurance');
		$this->loadModel('LaboItemInsuranceDetail');
        if(!empty($this->data)) {   
		
            $user = $this->getCurrentUser();
            $save = 0;
			$this->LaboItemInsurance->create();
			$data['LaboItemInsurance']['company_insurance_id'] = $this->data['LaboItemGroup']['company_insurance_id'];
			$data['LaboItemInsurance']['created_by'] = $user['User']['id'];
			if ($this->LaboItemInsurance->save($data['LaboItemInsurance'])) {
				$laboItemInsurnaceId = $this->LaboItemInsurance->getLastInsertId();
				for ($i = 0; $i < sizeof($this->data['LaboItemGroup']['labo_item_group_id']); $i++) {
					if($this->data['LaboItemGroup']['labo_item_group_id'][$i]!=""){
						$this->LaboItemInsuranceDetail->create();
						$data['LaboItemInsuranceDetail']['labo_item_insurance_id'] = $laboItemInsurnaceId;
						$data['LaboItemInsuranceDetail']['labo_item_group_id'] = $this->data['LaboItemGroup']['labo_item_group_id'][$i];
						$data['LaboItemInsuranceDetail']['patient_group_id'] = $this->data['LaboItemGroup']['patient_group_id'][$i];
						$data['LaboItemInsuranceDetail']['unit_price'] = $this->data['LaboItemGroup']['unit_price'][$i];
						$data['LaboItemInsuranceDetail']['hospital_price'] = $this->data['LaboItemGroup']['hospital_price'][$i];
						$data['LaboItemInsuranceDetail']['created_by'] = $user['User']['id'];
						$data['LaboItemInsuranceDetail']['is_active'] = 1;
						if ($this->LaboItemInsuranceDetail->save($data['LaboItemInsuranceDetail'])) {
							$save = 1;
						}else{
							$save = 0;
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
        $laboItemGroups = ClassRegistry::init('LaboItemGroup')->find('list', array('conditions' => 'is_active=1'));        
        $companyInsurances = ClassRegistry::init('CompanyInsurance')->find('list', array('conditions' => 'is_active=1'));
        $patientAllGroups = ClassRegistry::init('PatientGroup')->find('all', array('conditions' => 'status=1'));        
        $patientGroups = ClassRegistry::init('PatientGroup')->find('list', array('conditions' => 'status=1'));
        $this->set(compact('laboItemGroups', 'companyInsurances', 'patientAllGroups', 'patientGroups'));
    }
    
    function editInsurance($id = null) {
        $this->layout = 'ajax';
        $this->loadModel('CompanyInsurance');
        $this->loadModel('LaboItemInsurance');
        $this->loadModel('LaboItemInsuranceDetail');
        $this->loadModel('LaboItemPatientGroup');
        if(!empty($this->data)) {   
			// 	debug($this->data);
			// 	echo sizeof($this->data['LaboItemGroup']['labo_item_group_id']);
			// exit;
            $user = $this->getCurrentUser();
            $save = 0;
            $laboItemInsurnaceId = $this->data['LaboItemInsurance']['id'];
            $data['LaboItemInsurance']['id'] = $laboItemInsurnaceId;
            $data['LaboItemInsurance']['company_insurance_id'] = $this->data['LaboItemGroup']['company_insurance_id'];
            $data['LaboItemInsurance']['modified_by'] = $user['User']['id'];                    
            if ($this->LaboItemInsurance->save($data['LaboItemInsurance'])) {            
                $this->LaboItemInsuranceDetail->updateAll(
                        array('LaboItemInsuranceDetail.is_active' => "0"),
                        array('LaboItemInsuranceDetail.labo_item_insurance_id' => $this->data['LaboItemInsurance']['id'])
                );
				for ($i = 0; $i < sizeof($this->data['LaboItemGroup']['labo_item_group_id']); $i++) {
					if ($this->data['LaboItemGroup']['labo_item_group_id'][$i]!="") {
						$this->LaboItemInsuranceDetail->create();
						$data['LaboItemInsuranceDetail']['labo_item_insurance_id'] = $this->data['LaboItemInsurance']['id'];
						$data['LaboItemInsuranceDetail']['labo_item_group_id'] = $this->data['LaboItemGroup']['labo_item_group_id'][$i];
						$data['LaboItemInsuranceDetail']['patient_group_id'] = $this->data['LaboItemGroup']['patient_group_id'][$i];
						$data['LaboItemInsuranceDetail']['unit_price'] = $this->data['LaboItemGroup']['unit_price'][$i];
						$data['LaboItemInsuranceDetail']['hospital_price'] = $this->data['LaboItemGroup']['hospital_price'][$i];
						$data['LaboItemInsuranceDetail']['modified_by'] = $user['User']['id'];           
						if ($this->LaboItemInsuranceDetail->save($data['LaboItemInsuranceDetail'])) {
							$save = 1;
							echo 1 . "<br>";
						}else{
							$save = 0;
						} 
					}
				}
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                exit;
            }        
			if($save == 1){
				echo MESSAGE_DATA_HAS_BEEN_SAVED;
				exit;
			} else {
				echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
				exit;
			}
        }
        $servicesPriceInsurance = $this->LaboItemInsurance->find('first', array('conditions' => array('LaboItemInsurance.is_active'=>1, 'LaboItemInsuranceDetail.is_active' => 1, 'LaboItemInsurance.id' => $id),
            'fields' => array('LaboItemInsurance.*,LaboItemInsuranceDetail.*'),
            'joins' => array(
                array('table' => 'labo_item_insurance_details',
                    'alias' => 'LaboItemInsuranceDetail',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'LaboItemInsurance.id = LaboItemInsuranceDetail.labo_item_insurance_id'
                    )
                )
        )));  
        
        $user = $this->getCurrentUser();      
        // $this->data = $this->LaboItemGroup->read(null, $laboItemGroupId);
        $laboItemGroups = ClassRegistry::init('LaboItemGroup')->find('list', array('conditions' => 'is_active=1'));        

        $companyInsurances = ClassRegistry::init('CompanyInsurance')->find('list', array('conditions' => 'is_active=1'));
        $patientAllGroups = ClassRegistry::init('PatientGroup')->find('all', array('conditions' => 'status=1'));        
        $patientGroups = ClassRegistry::init('PatientGroup')->find('list', array('conditions' => 'status=1'));
        $this->set(compact('laboItemGroups', 'servicesPriceInsurance', 'companyInsurances', 'patientAllGroups', 'patientGroups'));
    }
    
    function deleteInsurance($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $this->loadModel('LaboItemInsurance');
        $this->loadModel('LaboItemInsuranceDetail');
        $user = $this->getCurrentUser();
        $this->LaboItemInsuranceDetail->updateAll(
                array('LaboItemInsuranceDetail.is_active' => "2"),
                array('LaboItemInsuranceDetail.id' => $id)
        );
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    } 
    
    
    function cloneServicePrice() {
        $this->layout = 'ajax';
        if (!empty($this->data)) {     
			debug($this->data);          
            $this->loadModel('CompanyInsurance');
            $this->loadModel('LaboItemInsurance');
            $user = $this->getCurrentUser();
            $save = 0;
            if (isset($this->data['LaboItemGroup']['patient_group_id'])) {                                
                $queryCompanyInsurance = mysql_query("SELECT * FROM labo_item_insurances WHERE is_active = 1 AND company_insurance_id = '".$this->data['LaboItemGroup']['company_insurance_id']."' ");
                while ($rowCompanyInsurance = mysql_fetch_array($queryCompanyInsurance)) {                                    
                    $queryCheckCompanyInsurance = mysql_query("SELECT id FROM labo_item_insurances WHERE is_active = 1 AND company_insurance_id = '".$this->data['LaboItemGroup']['company_insurance_id_to']."' ");
                    if(!mysql_num_rows($queryCheckCompanyInsurance)){
                        $this->LaboItemInsurance->create();
                        // $data['LaboItemInsurance']['labo_item_group_id'] = $rowCompanyInsurance['labo_item_group_id'];
                        $data['LaboItemInsurance']['company_insurance_id'] = $this->data['LaboItemGroup']['company_insurance_id_to'];
                        $data['LaboItemInsurance']['created_by'] = $user['User']['id'];            
                        if($this->LaboItemInsurance->save($data['LaboItemInsurance'])){                    
                            $servicesPriceInsuranceId = $this->LaboItemInsurance->getLastInsertId();
                            if (isset($this->data['LaboItemGroup']['patient_group_id'])) {                        
                                for ($j = 0; $j < sizeof($this->data['LaboItemGroup']['patient_group_id']); $j++) {                            
                                    $queryCompanyInsurancePatientGroup = mysql_query("SELECT * FROM labo_item_insurance_details WHERE is_active = 1 AND labo_item_insurance_id = '".$rowCompanyInsurance['id']."' AND patient_group_id = '".$this->data['LaboItemGroup']['patient_group_id'][$j]."' ");
                                    while ($rowCompanyInsurancePatientGroup = mysql_fetch_array($queryCompanyInsurancePatientGroup)) {                                
                                        mysql_query("INSERT INTO labo_item_insurance_details (labo_item_insurance_id,patient_group_id,labo_item_group_id,unit_price,hospital_price) VALUES ('" . $servicesPriceInsuranceId . "','" . $this->data['LaboItemGroup']['patient_group_id'][$j] . "','" . $rowCompanyInsurancePatientGroup['labo_item_group_id'] . "','" . $rowCompanyInsurancePatientGroup['unit_price'] . "','" . $rowCompanyInsurancePatientGroup['hospital_price'] . "')");
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
                            if (isset($this->data['LaboItemGroup']['patient_group_id'])) {                        
                                for ($j = 0; $j < sizeof($this->data['LaboItemGroup']['patient_group_id']); $j++) {
                                    $queryCheckPatientGroup = mysql_query("SELECT * FROM labo_item_insurance_details WHERE is_active = 1 AND labo_item_insurance_id = '".$servicesPriceInsuranceId."' AND patient_group_id = '".$this->data['LaboItemGroup']['patient_group_id'][$j]."' ");
                                    if(!mysql_num_rows($queryCheckPatientGroup)){
                                        $queryCompanyInsurancePatientGroup = mysql_query("SELECT * FROM labo_item_insurance_details WHERE is_active = 1 AND labo_item_insurance_id = '".$rowCompanyInsurance['id']."' AND patient_group_id = '".$this->data['LaboItemGroup']['patient_group_id'][$j]."' ");
                                        while ($rowCompanyInsurancePatientGroup = mysql_fetch_array($queryCompanyInsurancePatientGroup)) {                                
                                            mysql_query("INSERT INTO labo_item_insurance_details (labo_item_insurance_id,patient_group_id,labo_item_group_id,unit_price,hospital_price) VALUES ('" . $servicesPriceInsuranceId . "','" . $this->data['LaboItemGroup']['patient_group_id'][$j] . "','" . $rowCompanyInsurancePatientGroup['labo_item_group_id'] . "','". $rowCompanyInsurancePatientGroup['unit_price'] . "','" . $rowCompanyInsurancePatientGroup['hospital_price'] . "')");
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
        $this->loadModel('CompanyInsurance');
        $this->loadModel('LaboItemInsurance');
        $patientGroups = ClassRegistry::init('PatientGroup')->find('list', array('conditions' => 'status=1'));        
        $companyInsurances = $this->CompanyInsurance->find('list', array('conditions' => array('CompanyInsurance.is_active' => 1, 'LaboItemInsurance.is_active' => 1),
            'fields' => array('CompanyInsurance.name'),
            'joins' => array(
                array('table' => 'labo_item_insurances',
                    'alias' => 'LaboItemInsurance',
                    'type' => 'INNER',
                    'conditions' => array(
                        'CompanyInsurance.id = LaboItemInsurance.company_insurance_id'
                    )
                )
            ),
            'group' => '`CompanyInsurance`.`id`',
        ));         
        $this->set(compact('patientGroups', 'companyInsurances'));
    }
    
    function deleteServicePrice($id = null) {
        $id = $_GET['id'];
        if (!$id) {            
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $this->loadModel('LaboItemInsuranceDetail');
        $user = $this->getCurrentUser();
        for ($i = 0; $i < sizeof($id); $i++) {              
            $this->LaboItemInsuranceDetail->updateAll(
                array('LaboItemInsuranceDetail.is_active' => "2"),
                array('LaboItemInsuranceDetail.id' => $id[$i])
            );
        }  
        
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }

    function add() {
        $this->layout = 'ajax';
        if(!empty($this->data)) {
            if ($this->Helper->checkDouplicateLabo('name', 'company_id', 'labo_item_groups', trim($this->data['LaboItemGroup']['name']), $this->data['LaboItemGroup']['company_id'])) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $id_lists = "";
                $user = $this->getCurrentUser();
                if(isset($this->data['LaboItemGroup']['labo_item_id'])){
                   foreach($this->data['LaboItemGroup']['labo_item_id'] as $laboItemId) {
                        if(!empty($laboItemId)) {
                            $id_lists .= $laboItemId.",";
                        }
                    } 
                }
                $id_lists = substr($id_lists, 0, strlen($id_lists)-1);
                $this->data['LaboItemGroup']['name'] = trim($this->data['LaboItemGroup']['name']);
                $this->data['LaboItemGroup']['labo_item_id'] = $id_lists;
                $this->data['LaboItemGroup']['created_by'] = $user['User']['id'];
                $this->data['LaboItemGroup']['is_active'] = 1;
                if ($this->LaboItemGroup->save($this->data)) {
                    
                    $laboItemGroupId = $this->LaboItemGroup->getLastInsertId();
                    if (isset($this->data['LaboItemGroup']['patient_group_id'])) {
                        for ($i = 0; $i < sizeof($this->data['LaboItemGroup']['patient_group_id']); $i++) {
                            mysql_query("INSERT INTO labo_item_patient_groups (labo_item_group_id,patient_group_id,unit_price,hospital_price) VALUES ('" . $laboItemGroupId . "','" . $this->data['LaboItemGroup']['patient_group_id'][$i] . "','" . $this->data['LaboItemGroup']['unit_price'][$i] . "','" . $this->data['LaboItemGroup']['hospital_price'][$i] . "')");
                        }
                    }   
                    
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
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
        $patientGroups = ClassRegistry::init('PatientGroup')->find('list', array('conditions' => 'status=1'));
        $laboSubTitleGroups  = ClassRegistry::init('LaboSubTitleGroup')->find('list', array('conditions' => 'is_active=1'));
        $serviceAccount = ClassRegistry::init('AccountType')->findById(9);
        $serviceAccountId = $serviceAccount['AccountType']['chart_account_id'];
        $this->set(compact('companies', 'patientGroups', 'laboSubTitleGroups', 'serviceAccountId'));
    }
    
    function delete($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        $this->LaboItemGroup->updateAll(
                array('LaboItemGroup.is_active' => "2", 'LaboItemGroup.modified_by' => $user['User']['id']),
                array('LaboItemGroup.id' => $id)
        );
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }            
    
    function edit($id) {
        $this->layout = 'ajax';
        if(!empty($this->data)) {
            $this->loadModel('LaboItemPatientGroup');
            if ($this->Helper->checkDouplicateLaboEdit('name', 'company_id', 'labo_item_groups', $id, trim($this->data['LaboItemGroup']['name']), $this->data['LaboItemGroup']['company_id'])) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $id_lists = "";
                $user = $this->getCurrentUser();
                if(isset($this->data['LaboItemGroup']['labo_item_id'])){
                    foreach($this->data['LaboItemGroup']['labo_item_id'] as $laboItemId) {
                        if(!empty($laboItemId)) {
                            $id_lists .= $laboItemId.",";
                        }
                    }
                    $id_lists = substr($id_lists, 0, strlen($id_lists)-1);
                }else {
                    $id_lists = null ; 
                }
                $this->data['LaboItemGroup']['name'] = trim($this->data['LaboItemGroup']['name']);
                $this->data['LaboItemGroup']['labo_item_id'] = $id_lists;
                $this->data['LaboItemGroup']['modified_by'] = $user['User']['id'];

                if ($this->LaboItemGroup->save($this->data)) {
                    
                    $this->LaboItemPatientGroup->updateAll(
                            array('LaboItemPatientGroup.is_active' => "2"),
                            array('LaboItemPatientGroup.labo_item_group_id' => $id)
                    );
                    if (isset($this->data['LaboItemGroup']['patient_group_id'])) {
                        for ($i = 0; $i < sizeof($this->data['LaboItemGroup']['patient_group_id']); $i++) {
                            mysql_query("INSERT INTO labo_item_patient_groups (labo_item_group_id,patient_group_id,unit_price,hospital_price) VALUES ('" . $id . "','" . $this->data['LaboItemGroup']['patient_group_id'][$i] . "','" . $this->data['LaboItemGroup']['unit_price'][$i] . "','" . $this->data['LaboItemGroup']['hospital_price'][$i] . "')");
                        }
                    }
                    
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        $user = $this->getCurrentUser();
        $this->data = $this->LaboItemGroup->read(null, $id);        
        $companies = ClassRegistry::init('Company')->find('list',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        $laboSubTitleGroups  = ClassRegistry::init('LaboSubTitleGroup')->find('list', array('conditions' => 'is_active=1'));
        $patientAllGroups = ClassRegistry::init('PatientGroup')->find('all', array('conditions' => 'status=1'));        
        $patientGroups = ClassRegistry::init('PatientGroup')->find('list', array('conditions' => 'status=1'));
        $this->set(compact('options', 'companies', 'laboSubTitleGroups', 'patientAllGroups', 'patientGroups'));
    }

    function view($id) {
        $this->layout = 'ajax';        
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $this->set('laboItemGroup', $this->LaboItemGroup->read(null, $id));       
    }
    
    function exportExcel(){
        $this->layout = 'ajax';
        if (isset($_POST['action']) && $_POST['action'] == 'export') {
            $user = $this->getCurrentUser();
            $filename = "public/report/labo_sub_group_export.csv";
            $fp = fopen($filename, "wb");
            $excelContent = 'Labo Sub Group' . "\n\n";
            $excelContent .= TABLE_NO . "\t" . TABLE_COMPANY . "\t" . TABLE_CODE. "\t" . MENU_LABO_SUB_GROUP_NAME. "\t" . GENERAL_UNIT_PRICE;
            $query = mysql_query(' SELECT laboItemGroup.id, (SELECT name FROM companies WHERE id = laboItemGroup.company_id),laboItemGroup.code,laboItemGroup.name FROM labo_item_groups laboItemGroup WHERE is_active=1 ');
            $index = 1;
            while ($data = mysql_fetch_array($query)) {
                $result = "";
                $unitPrice = 0;
                $patientGroup = "";    
                $queryPrice = mysql_query("SELECT name,unit_price FROM labo_item_patient_groups
                                        INNER JOIN patient_groups ON patient_groups.id = labo_item_patient_groups.patient_group_id
                                        WHERE labo_item_patient_groups.is_active = 1 AND labo_item_patient_groups.labo_item_group_id=".$data[0]);
                while ($rowPrice = mysql_fetch_row($queryPrice)) {
                    $unitPrice = $rowPrice[1];
                    $patientGroup = $rowPrice[0];  
                    $result .= $patientGroup.' = '. number_format($unitPrice, 2).'  ';
                }
                $excelContent .= "\n" . $index++ . "\t" . $data[1] . $data[2]. "\t". $data[3]. "\t" . $result;
            }
            $excelContent = chr(255) . chr(254) . @mb_convert_encoding($excelContent, 'UTF-16LE', 'UTF-8');
            fwrite($fp, $excelContent);
            fclose($fp);
            exit();
        }
    }
}
?>