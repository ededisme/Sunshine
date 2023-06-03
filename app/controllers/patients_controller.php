<?php

class PatientsController extends AppController {

    var $name = 'Patients';
    var $components = array('Helper');

    function opdList() {
        $this->layout = "ajax";
    }

    function opdListAjax($dateFrom = '', $dateTo = '') {
        $this->layout = "ajax";   
        $this->set(compact('dateFrom', 'dateTo'));          
    }
    
    function index() {
        $this->layout = "ajax";
    }

    function ajax($date = '') {
        $this->layout = "ajax";   
        $this->set(compact('date'));          
    }
    
    function quotation() {
        $this->layout = "ajax";
    }

    function quotationAjax() {        
        $this->layout = "ajax";
    }
    

    function viewQuotation($id = null) {
        $this->layout = "ajax";
        $this->loadModel('PatientBillType');
        $this->loadModel('PatientType');
        $this->loadModel('Nationality');
        $this->loadModel('PatientConnectionWithHospital');
        $this->loadModel('PatientConnectionDetail');
        if (!$id) {
            $this->Session->setFlash(__('Invalid patient', true), 'flash_failure');
            $this->redirect(array('action' => 'quotation'));
        }
        
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'PatientQuotation.id' => $id),
            'fields' => array('Patient.*, PatientQuotation.*, Nationality.name, PatientType.name, Company.name'),
            'joins' => array(
                array('table' => 'patient_quotations',
                    'alias' => 'PatientQuotation',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Patient.id = PatientQuotation.patient_id'
                    )
                ),
                array('table' => 'patient_types',
                    'alias' => 'PatientType',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientType.id = Patient.patient_type_id'
                    )
                ),
                array('table' => 'nationalities',
                    'alias' => 'Nationality',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Nationality.id = Patient.nationality'
                    )
                ),
                array('table' => 'companies',
                    'alias' => 'Company',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Company.id = PatientQuotation.company_id'
                    )
                )
        )));
        $this->set(compact('patient'));
    }
    
    function returnPatient($type = null, $module = null, $queueDocId = null, $queueId = null) {
        $this->layout = "ajax";
        $this->loadModel('User');
        $doctorId = $_GET['doctorId'];
        $doctors = $this->User->find('all', array('conditions' => array('User.is_active' => 1,'UserGroup.group_id' => array(2,21)), 'order'=>array('Employee.name ASC'),
                    'fields' => array('User.*, Employee.*'),
                    'joins' => array(
                        array('table' => 'user_employees',
                            'alias' => 'UserEmployee',
                            'type' => 'INNER',
                            'conditions' => array(
                                'User.id = UserEmployee.user_id'
                            )
                        ),
                        array('table' => 'employees',
                            'alias' => 'Employee',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Employee.id = UserEmployee.employee_id'
                            )
                        ),
                        array('table' => 'user_groups',
                            'alias' => 'UserGroup',
                            'type' => 'INNER',
                            'conditions' => array(
                                'User.id = UserGroup.user_id'
                            )
                        )
                )));
        $this->set(compact('doctors', 'type', 'module', 'queueDocId', 'queueId', 'doctorId'));
        
    }

    function view($id = null) {
        $this->layout = "ajax";
        if (!$id) {
            $this->Session->setFlash(__('Invalid patient', true), 'flash_failure');
            $this->redirect(array('action' => 'index'));
        }
        $this->loadModel('PatientBillType');
        $this->loadModel('PatientType');
        $this->loadModel('Nationality');
        $this->loadModel('PatientConnectionWithHospital');
        $this->loadModel('PatientConnectionDetail');
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'Patient.id' => $id),
            'fields' => array('Patient.*, PatientBillType.name, Nationality.name, PatientType.name'),
            'joins' => array(
                array('table' => 'patient_bill_types',
                    'alias' => 'PatientBillType',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientBillType.id = Patient.patient_bill_type_id'
                    )
                ),
                array('table' => 'patient_types',
                    'alias' => 'PatientType',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientType.id = Patient.patient_type_id'
                    )
                ),
                array('table' => 'nationalities',
                    'alias' => 'Nationality',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Nationality.id = Patient.nationality'
                    )
                )
        )));                
        $patientConnections = ClassRegistry::init('PatientConnectionWithHospital')->find('all');
        $patientConnectionDetails = ClassRegistry::init('PatientConnectionDetail')->find('list', array('fields' => 'id,patient_connection_with_hospital_id', 'conditions' => array('status'=>1, 'patient_id' => $id)));        
        $this->set(compact('patient', 'patientConnections', 'patientConnectionDetails'));
    }
    
    function printPatientForm($id = null) {
        $this->layout = "ajax";
        if (!$id) {
            $this->Session->setFlash(__('Invalid patient', true), 'flash_failure');
            $this->redirect(array('action' => 'index'));
        }
        $this->loadModel('PatientBillType');
        $this->loadModel('PatientType');
        $this->loadModel('Nationality');
        $this->loadModel('Branch');
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'Patient.id' => $id),
            'fields' => array('Patient.*, PatientBillType.name, Nationality.name, PatientType.name'),
            'joins' => array(
                array('table' => 'patient_bill_types',
                    'alias' => 'PatientBillType',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientBillType.id = Patient.patient_bill_type_id'
                    )
                ),
                array('table' => 'patient_types',
                    'alias' => 'PatientType',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientType.id = Patient.patient_type_id'
                    )
                ),
                array('table' => 'nationalities',
                    'alias' => 'Nationality',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Nationality.id = Patient.nationality'
                    )
                )
        )));        
        $user = $this->getCurrentUser();
        $branches = ClassRegistry::init('Branch')->find('first',
            array(
                'joins' => array(
                    array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))
                ),
                'fields' => array('Branch.id', 'Branch.address', 'Branch.telephone', 'Branch.email_address'),
                'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])
            ));
        $this->set(compact('patient', 'branches'));
    }        
    
    function queued_patient($controller = null) {
        $this->Session->delete('keywords');
        $userGroup = $this->getCurrentUser();
        if ($userGroup['User']['usr'] == "view") {
            $this->redirect(array('action' => 'patient_waiting'));                      
        }        
        $this->QueuedPatient->recursive = 0;     
        return $this->QueuedPatient->find('all', array('conditions' => array('in_queue' => 2, 'Patient.enabled' => 1)));
    }
    
    function printDoctorWaiting($queueId = null) {        
        $this->layout = "ajax";  
        $this->loadModel('Queue');
        $this->loadModel('QueuedDoctorWaiting');
        $this->loadModel('User');        
        $patients = $this->User->find('first', array('conditions' => array('User.is_active' => 1, 'QueuedDoctorWaiting.queue_id' => $queueId),
            'fields' => array('Employee.name, QueuedDoctorWaiting.*, Patient.*'),
            'joins' => array(
                array('table' => 'queued_doctor_waitings',
                    'alias' => 'QueuedDoctorWaiting',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'User.id = QueuedDoctorWaiting.doctor_id'
                    )
                ),
                array('table' => 'queues',
                    'alias' => 'Queue',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Queue.id = QueuedDoctorWaiting.queue_id'
                    )
                ),
                array('table' => 'patients',
                    'alias' => 'Patient',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Patient.id = Queue.patient_id'
                    )
                ),
                array('table' => 'user_employees',
                    'alias' => 'UserEmployee',
                    'type' => 'INNER',
                    'conditions' => array(
                        'User.id = UserEmployee.user_id'
                    )
                ),
                array('table' => 'employees',
                    'alias' => 'Employee',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Employee.id = UserEmployee.employee_id'
                    )
                )
        )));
        $this->set(compact('patients'));
    }
        
    function addPatientWaitingNumber($module = null, $queueDocId = null, $queueId = null){
        $this->loadModel('Queue');      
        $this->loadModel('QueueNumber');
        $this->loadModel('QueuedDoctor');
        $this->loadModel('QueuedDoctorWaiting');
        if($this->data['Patient']['doctor_id']!=""){
            $user = $this->getCurrentUser();
            if($module!=""){      
                $numberTaken = 1;
                $roomId = "";
                $queryUpdate = mysql_query("UPDATE `queued_doctors` SET `doctor_id`='".$this->data['Patient']['doctor_id']."', `status`=1, `modified`='".date("Y-m-d H:i:s")."', `modified_by`=".$user['User']['id']." WHERE `id`=".$queueDocId.";");                
                if($queryUpdate){        
                    // get room id from doctor on user id
                    $queryRoomDoctor = mysql_query("SELECT room_id FROM users WHERE id = {$this->data['Patient']['doctor_id']}");
                    while ($resultRoomDotor = mysql_fetch_array($queryRoomDoctor)) {
                        $roomId = $resultRoomDotor['room_id'];
                    }
                    // check condition room id
                    if($roomId!=""){
                        $qryNumber = mysql_query("SELECT number_taken FROM queued_doctor_waitings WHERE DATE(created) >= CURDATE() AND room_id = ".$roomId);
                        while ($rowNumber = mysql_fetch_array($qryNumber)) {
                            $numberTaken = $rowNumber['number_taken'] + 1;                                    
                        }  
                    }else{
                        $qryNumber = mysql_query("SELECT number_taken FROM queued_doctor_waitings WHERE DATE(created) >= CURDATE() AND doctor_id = ".$this->data['Patient']['doctor_id']);
                        while ($rowNumber = mysql_fetch_array($qryNumber)) {
                            $numberTaken = $rowNumber['number_taken'] + 1;                                    
                        }   
                    }
                              
                    // save number taken for queue in doctor consult
                    $this->QueuedDoctorWaiting->create();
                    $data['QueuedDoctorWaiting']['queue_id'] = $queueId;
                    $data['QueuedDoctorWaiting']['doctor_id'] = $this->data['Patient']['doctor_id'];
                    $data['QueuedDoctorWaiting']['room_id'] = $roomId;
                    $data['QueuedDoctorWaiting']['number_taken'] = $numberTaken;
                    $data['QueuedDoctorWaiting']['created_by'] = $user['User']['id'];
                    if ($this->QueuedDoctorWaiting->save($data['QueuedDoctorWaiting'])) {
                        echo $queueId;
                        exit;
                    }
                }
                
            }else{
                // create new queue for patient in hospital
                $this->Queue->create();
                $data['Queue']['patient_id'] = $this->data['Patient']['id'];
                $data['Queue']['patient_type_id'] = 2;
                $data['Queue']['created_by'] = $user['User']['id'];
                if ($this->Queue->save($data['Queue'])) {
                    $queueId = $this->Queue->getLastInsertId();
                    // create patient for doctor
                    $this->QueuedDoctor->create();
                    $data['QueuedDoctor']['queue_id'] = $queueId;
                    $data['QueuedDoctor']['doctor_id'] = $this->data['Patient']['doctor_id'];
                    $data['QueuedDoctor']['created_by'] = $user['User']['id'];
                    $numberTaken = 1;
                    $roomId = "";
                    if ($this->QueuedDoctor->save($data['QueuedDoctor'])) {
                        // get room id from doctor on user id                        
                        $queryRoomDoctor = mysql_query("SELECT room_id FROM users WHERE id = {$this->data['Patient']['doctor_id']}");
                        while ($resultRoomDotor = mysql_fetch_array($queryRoomDoctor)) {
                            $roomId = $resultRoomDotor['room_id'];
                        }
                        // check condition room id
                        if($roomId!=""){
                            $qryNumber = mysql_query("SELECT number_taken FROM queued_doctor_waitings WHERE DATE(created) >= CURDATE() AND room_id = ".$roomId);
                            while ($rowNumber = mysql_fetch_array($qryNumber)) {
                                $numberTaken = $rowNumber['number_taken'] + 1;                                    
                            }  
                        }else{
                            $qryNumber = mysql_query("SELECT number_taken FROM queued_doctor_waitings WHERE DATE(created) >= CURDATE() AND doctor_id = ".$this->data['Patient']['doctor_id']);
                            while ($rowNumber = mysql_fetch_array($qryNumber)) {
                                $numberTaken = $rowNumber['number_taken'] + 1;                                    
                            } 
                        }                           
                        // save number taken for queue in doctor consult
                        $this->QueuedDoctorWaiting->create();
                        $data['QueuedDoctorWaiting']['queue_id'] = $queueId;
                        $data['QueuedDoctorWaiting']['doctor_id'] = $this->data['Patient']['doctor_id'];
                        $data['QueuedDoctorWaiting']['room_id'] = $roomId;
                        $data['QueuedDoctorWaiting']['number_taken'] = $numberTaken;
                        $data['QueuedDoctorWaiting']['created_by'] = $user['User']['id'];
                        if ($this->QueuedDoctorWaiting->save($data['QueuedDoctorWaiting'])) {
                            echo $queueId;
                            exit;
                        }
                    }
                }
            }
        }      
    }

    function add() {
        $this->layout = "ajax";
        $this->loadModel('Queue');
        $this->loadModel('PatientLocation');        
        $this->loadModel('QueueNumber');
        $this->loadModel('QueuedDoctor');
        $this->loadModel('QueuedDoctorWaiting');
        $this->loadModel('PatientBillType');
        $this->loadModel('PatientType');
        $this->loadModel('PatientGroup');
        $this->loadModel('Nationality');
        $this->loadModel('PatientConnectionWithHospital');
        $this->loadModel('PatientConnectionDetail');
        $this->loadModel('User');
        $this->loadModel('UserGroup');
        $this->loadModel('Group');
        $this->loadModel('CompanyInsurance');
        $numberTaken = 1;        
        $roomId = "";
        $user = $this->getCurrentUser();
        
        if (!empty($this->data)) {    
            $patientCode = $this->Helper->getAutoGeneratePatientCode();
            if ($this->Helper->checkDouplicate('patient_code', 'patients', $patientCode)) {                
                $this->Session->setFlash(__('The patient code "'.$patientCode.'" already exists in the system.', true), 'flash_failure');
            } else {
                $this->Patient->create();
                $user = $this->getCurrentUser();
                $this->data['Patient']['patient_code'] = $patientCode;
                if($this->data['Patient']['nationality']==""){
                    $this->data['Patient']['nationality'] = 36;
                }
                $this->data['Patient']['created_by'] = $user['User']['id'];
                $this->Patient->set('patient_code', $this->Helper->getAutoGeneratePatientCode());
                if ($this->Patient->save($this->data)) {
                    $patientId = $this->Patient->getLastInsertId();

//Insert Service Secondary					
					$patientSsId = ""; 
					echo("INSERT INTO ".DB_SS_MONY_KID."patients (`id`, `patient_code`, `patient_name`, `mother_name`, `father_name`, `sex`, `telephone`, `address`, `location_id`, `relation_patient`, `patient_id_card`, `email`, `dob`, `nationality`, `religion`, `patient_bill_type_id`, `company_insurance_id`, `insurance_note`, `occupation`, `patient_fax_number`, `case_emergency_tel`, `case_emergency_name`, `allergic_medicine`, `allergic_food`, `allergic_medicine_note`, `allergic_food_note`, `patient_type_id`, `patient_group_id`, `patient_group`, `payment_term_id`, `payment_every`, `photo`, `created`, `created_by`, `modified`, `modified_by`, `is_active`) 
                                            SELECT `id`, `patient_code`, `patient_name`, `mother_name`, `father_name`, `sex`, `telephone`, `address`, `location_id`, `relation_patient`, `patient_id_card`, `email`, `dob`, `nationality`, `religion`, `patient_bill_type_id`, `company_insurance_id`, `insurance_note`, `occupation`, `patient_fax_number`, `case_emergency_tel`, `case_emergency_name`, `allergic_medicine`, `allergic_food`, `allergic_medicine_note`, `allergic_food_note`, `patient_type_id`, `patient_group_id`, `patient_group`, `payment_term_id`, `payment_every`, `photo`, `created`, `created_by`, `modified`, `modified_by`, `is_active` FROM patients WHERE id = " . $patientId . ";");
                	$patientSsId = mysql_insert_id();

                    if(isset($this->data['Patient']['patient_conection_id'])){
                        for ($i = 0; $i < sizeof($this->data['Patient']['patient_conection_id']); $i++) {
                            if($this->data['Patient']['patient_conection_id'][$i]!=""){
                                $this->PatientConnectionDetail->create();
                                $data['PatientConnectionDetail']['patient_id'] = $patientId;
                                $data['PatientConnectionDetail']['patient_connection_with_hospital_id'] = $this->data['Patient']['patient_conection_id'][$i];
                                $this->PatientConnectionDetail->save($data['PatientConnectionDetail']);
								// Secondary
								mysql_query("INSERT INTO ".DB_SS_MONY_KID."patient_connection_details (`id`, `patient_id`, `patient_connection_with_hospital_id`, `created`, `modified`, `status`) 
								 			SELECT `id`, `patient_id`, `patient_connection_with_hospital_id`, `created`, `modified`, `status` FROM patients WHERE id = " . $patientId . ";");
							}                        
                        }         
                    }
                    
                    if($this->data['Patient']['doctor_id']!=""){
                        // create new queue for patient in hospital
                        $this->Queue->create();
                        $data['Queue']['patient_id'] = $patientId;
                        $data['Queue']['patient_type_id'] = $this->data['Patient']['patient_type_id'];
                        $data['Queue']['created_by'] = $user['User']['id'];
                        if ($this->Queue->save($data['Queue'])) {
                            $queueId = $this->Queue->getLastInsertId();
                            // create patient for doctor
                            $this->QueuedDoctor->create();
                            $data['QueuedDoctor']['queue_id'] = $queueId;
                            $data['QueuedDoctor']['doctor_id'] = $this->data['Patient']['doctor_id'];
                            $data['QueuedDoctor']['created_by'] = $user['User']['id'];
                            if ($this->QueuedDoctor->save($data['QueuedDoctor'])) {
                                // get room id from doctor on user id                        
                                $queryRoomDoctor = mysql_query("SELECT room_id FROM users WHERE id = {$this->data['Patient']['doctor_id']}");
                                while ($resultRoomDotor = mysql_fetch_array($queryRoomDoctor)) {
                                    $roomId = $resultRoomDotor['room_id'];
                                }
                                // check condition room id
                                if($roomId!=""){
                                    $qryNumber = mysql_query("SELECT number_taken FROM queued_doctor_waitings WHERE DATE(created) >= CURDATE() AND room_id = ".$roomId);
                                    while ($rowNumber = mysql_fetch_array($qryNumber)) {
                                        $numberTaken = $rowNumber['number_taken'] + 1;                                    
                                    }  
                                }else{
                                    $qryNumber = mysql_query("SELECT number_taken FROM queued_doctor_waitings WHERE DATE(created) >= CURDATE() AND doctor_id = ".$this->data['Patient']['doctor_id']);
                                    while ($rowNumber = mysql_fetch_array($qryNumber)) {
                                        $numberTaken = $rowNumber['number_taken'] + 1;                                    
                                    } 
                                }                                
                                // save number taken for queue in doctor consult
                                $this->QueuedDoctorWaiting->create();
                                $data['QueuedDoctorWaiting']['queue_id'] = $queueId;
                                $data['QueuedDoctorWaiting']['doctor_id'] = $this->data['Patient']['doctor_id'];
                                $data['QueuedDoctorWaiting']['room_id'] = $roomId;
                                $data['QueuedDoctorWaiting']['number_taken'] = $numberTaken;
                                $data['QueuedDoctorWaiting']['created_by'] = $user['User']['id'];
                                if ($this->QueuedDoctorWaiting->save($data['QueuedDoctorWaiting'])) {
                                    echo $queueId;
                                    exit;
                                }
                            }
                        }
                    }       
                    exit;                    
                }
            }
        }
        
        $sexes = array('M' => GENERAL_MALE, 'F' => GENERAL_FEMALE);
        $patientTypes = ClassRegistry::init('PatientType')->find('list', array('conditions' => 'status=1'));
        $patientGroups = ClassRegistry::init('PatientGroup')->find('list', array('conditions' => 'status=1'));
        $patientBillTypes = ClassRegistry::init('PatientBillType')->find('list', array('conditions' => 'status=1'));
        $nationalities = ClassRegistry::init('Nationality')->find('list', array('order'=>array('Nationality.name ASC')));
        $locations = ClassRegistry::init('PatientLocation')->find('list', array('order'=>array('PatientLocation.name ASC')));
        $patientConnections = ClassRegistry::init('PatientConnectionWithHospital')->find('all');
        $companyInsurances = ClassRegistry::init('CompanyInsurance')->find('list', array('conditions' => 'is_active=1'));  
        $referrals = ClassRegistry::init('Referral')->find('list', array('conditions' => 'is_active=1'));
        $doctors = $this->User->find('all', array('conditions' => array('User.is_active' => 1,'UserGroup.group_id' => array('2')), 'order'=>array('Employee.name ASC'),
                    'fields' => array('User.*, Employee.*'),
                    'joins' => array(
                        array('table' => 'user_employees',
                            'alias' => 'UserEmployee',
                            'type' => 'INNER',
                            'conditions' => array(
                                'User.id = UserEmployee.user_id'
                            )
                        ),
                        array('table' => 'employees',
                            'alias' => 'Employee',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Employee.id = UserEmployee.employee_id'
                            )
                        ),
                        array('table' => 'user_groups',
                            'alias' => 'UserGroup',
                            'type' => 'INNER',
                            'conditions' => array(
                                'User.id = UserGroup.user_id'
                            )
                        )
                )));
        $this->set('code', '');
        $this->set(compact('sexes', 'patientTypes', 'patientBillTypes', 'patientGroups', 'nationalities', 'locations', 'patientConnections', 'doctors', 'companyInsurances','referrals'));
    }

    
    function edit($id = null) {
        $this->layout = "ajax";
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid patient', true), 'flash_failure');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            $this->loadModel('PatientBillType');
            $this->loadModel('PatientType');
            $this->loadModel('PatientGroup');
            $this->loadModel('Nationality');
            $this->loadModel('PatientConnectionWithHospital');
            $this->loadModel('PatientConnectionDetail');            
            $user = $this->getCurrentUser();                
            if($this->data['Patient']['nationality']==""){
                $this->data['Patient']['nationality'] = 36;
            }
            if(empty($this->data['Patient']['allergic_medicine'])){
                $this->data['Patient']['allergic_medicine'] = 0;
            }
            if(empty($this->data['Patient']['allergic_food'])){
                $this->data['Patient']['allergic_food'] = 0;
            }
            if(empty($this->data['Patient']['unknown_allergic'])){
                $this->data['Patient']['unknown_allergic'] = 0;
            }
            $this->data['Patient']['created_by'] = $user['User']['id'];
            if ($this->Patient->save($this->data)) {                                        
                $patientId = $this->data['Patient']['id'];   

				//Update Service Secondary		
				mysql_query("DELETE FROM ".DB_SS_MONY_KID."patients WHERE id={$patientId}");
				$insertPatient = mysql_query("INSERT INTO ".DB_SS_MONY_KID."patients (`id`, `patient_code`, `patient_name`, `mother_name`, `father_name`, `sex`, `telephone`, `address`, `location_id`, `relation_patient`, `patient_id_card`, `email`, `dob`, `nationality`, `religion`, `patient_bill_type_id`, `company_insurance_id`, `insurance_note`, `occupation`, `patient_fax_number`, `case_emergency_tel`, `case_emergency_name`, `allergic_medicine`, `allergic_food`, `allergic_medicine_note`, `allergic_food_note`, `patient_type_id`, `patient_group_id`, `patient_group`, `payment_term_id`, `payment_every`, `photo`, `referral_id`,`created`, `created_by`, `modified`, `modified_by`, `is_active`) 
										SELECT `id`, `patient_code`, `patient_name`, `mother_name`, `father_name`, `sex`, `telephone`, `address`, `location_id`, `relation_patient`, `patient_id_card`, `email`, `dob`, `nationality`, `religion`, `patient_bill_type_id`, `company_insurance_id`, `insurance_note`, `occupation`, `patient_fax_number`, `case_emergency_tel`, `case_emergency_name`, `allergic_medicine`, `allergic_food`, `allergic_medicine_note`, `allergic_food_note`, `patient_type_id`, `patient_group_id`, `patient_group`, `payment_term_id`, `payment_every`, `photo`,`referral_id` `created`, `created_by`, `modified`, `modified_by`, `is_active` FROM patients WHERE id = " . $patientId . ";");


				$this->PatientConnectionDetail->updateAll(
                    array('PatientConnectionDetail.status' => "2"), array('PatientConnectionDetail.patient_id' => $patientId)
                );
                if(isset($this->data['Patient']['patient_conection_id'])){
					// update secondary
					mysql_query("UPDATE ".DB_SS_MONY_KID."patient_connection_details SET status = 2 WHERE patient_id={$patientId}");
                    for ($i = 0; $i < sizeof($this->data['Patient']['patient_conection_id']); $i++) {
                        if($this->data['Patient']['patient_conection_id'][$i]!=""){
                            $this->PatientConnectionDetail->create();
                            $data['PatientConnectionDetail']['patient_id'] = $patientId;
                            $data['PatientConnectionDetail']['patient_connection_with_hospital_id'] = $this->data['Patient']['patient_conection_id'][$i];
                            $this->PatientConnectionDetail->save($data['PatientConnectionDetail']);
                       
					   		// Secondary
							mysql_query("INSERT INTO ".DB_SS_MONY_KID."patient_connection_details (`id`, `patient_id`, `patient_connection_with_hospital_id`, `created`, `modified`, `status`) 
								 		SELECT `id`, `patient_id`, `patient_connection_with_hospital_id`, `created`, `modified`, `status` FROM patient_connection_details WHERE status = 1 AND patient_id = " . $patientId . ";");
					    }                        
                    }       
                }
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                exit;
            }        
        }
        if (empty($this->data)) {
            $this->data = $this->Patient->read(null, $id);
        }
        $sexes = array('M' => GENERAL_MALE, 'F' => GENERAL_FEMALE);
        $patientTypes = ClassRegistry::init('PatientType')->find('list', array('conditions' => 'status=1'));
        $patientGroups = ClassRegistry::init('PatientGroup')->find('list', array('conditions' => 'status=1'));
        $patientBillTypes = ClassRegistry::init('PatientBillType')->find('list', array('conditions' => 'status=1'));        
        $nationalities = ClassRegistry::init('Nationality')->find('list', array('order'=>array('Nationality.name ASC')));
        $locations = ClassRegistry::init('PatientLocation')->find('list', array('order'=>array('PatientLocation.name ASC')));
        $patientConnections = ClassRegistry::init('PatientConnectionWithHospital')->find('all');
        $patientConnectionDetails = ClassRegistry::init('PatientConnectionDetail')->find('list', array('fields' => 'id,patient_connection_with_hospital_id', 'conditions' => array('status'=>1, 'patient_id' => $id))); 
        $this->set('code', $this->data['Patient']['patient_code']);
        $companyInsurances = ClassRegistry::init('CompanyInsurance')->find('list', array('conditions' => 'is_active=1'));
        $referrals = ClassRegistry::init('Referral')->find('list', array('conditions' => 'is_active=1'));
        $this->set(compact('sexes', 'patientTypes', 'patientBillTypes', 'patientGroups', 'nationalities', 'locations', 'patientConnections', 'patientConnectionDetails', 'companyInsurances','referrals'));
    }
    
    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__(MESSAGE_DATA_INVALID, true), 'flash_failure');
            $this->redirect(array('action' => 'index'));
        }        
        $user = $this->getCurrentUser();
        $dateTime = date("Y-m-d H:i:s");
        $this->Patient->updateAll(
                array('Patient.is_active' => "2", 'Patient.modified' => "'$dateTime'", 'Patient.modified_by' => $user['User']['id']), array('Patient.id' => $id)
        );
        
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }
    
    
    function addPediatric() {
        $this->layout = "ajax";
        $this->loadModel('Queue');
        $this->loadModel('Location');        
        $this->loadModel('QueueNumber');
        $this->loadModel('QueuedDoctor');
        $this->loadModel('QueuedDoctorWaiting');
        $this->loadModel('PatientBillType');
        $this->loadModel('PatientType');
        $this->loadModel('PatientGroup');
        $this->loadModel('Nationality');
        $this->loadModel('PatientConnectionWithHospital');
        $this->loadModel('PatientConnectionDetail');
        $this->loadModel('User');
        $this->loadModel('UserGroup');
        $this->loadModel('Group');        
        $numberTaken = 1;        
        $roomId = "";
        if (!empty($this->data)) {     
            $patientCode = $this->Helper->getAutoGeneratePatientCode();
            if ($this->Helper->checkDouplicate('patient_code', 'patients', $patientCode)) {
                $this->Session->setFlash(__('The patient code "'.$patientCode.'" already exists in the system.', true), 'flash_failure');
            } else {              
                $this->Patient->create();
                $user = $this->getCurrentUser();
                $this->data['Patient']['patient_code'] = $patientCode;
                if($this->data['Patient']['nationality']==""){
                    $this->data['Patient']['nationality'] = 36;
                }
                $this->data['Patient']['created_by'] = $user['User']['id'];
                $this->Patient->set('patient_code', $this->Helper->getAutoGeneratePatientCode());
                if ($this->Patient->save($this->data)) {
                    $patientId = $this->Patient->getLastInsertId();
                    if(isset($this->data['Patient']['patient_conection_id'])){
                        for ($i = 0; $i < sizeof($this->data['Patient']['patient_conection_id']); $i++) {
                            if($this->data['Patient']['patient_conection_id'][$i]!=""){
                                $this->PatientConnectionDetail->create();
                                $data['PatientConnectionDetail']['patient_id'] = $patientId;
                                $data['PatientConnectionDetail']['patient_connection_with_hospital_id'] = $this->data['Patient']['patient_conection_id'][$i];
                                $this->PatientConnectionDetail->save($data['PatientConnectionDetail']);
                            }                        
                        }         
                    }
                    
                    if($this->data['Patient']['doctor_id']!=""){
                        // create new queue for patient in hospital
                        $this->Queue->create();
                        $data['Queue']['patient_id'] = $patientId;
                        $data['Queue']['patient_type_id'] = $this->data['Patient']['patient_type_id'];
                        $data['Queue']['created_by'] = $user['User']['id'];
                        if ($this->Queue->save($data['Queue'])) {
                            $queueId = $this->Queue->getLastInsertId();
                            // create patient for doctor
                            $this->QueuedDoctor->create();
                            $data['QueuedDoctor']['queue_id'] = $queueId;
                            $data['QueuedDoctor']['doctor_id'] = $this->data['Patient']['doctor_id'];
                            $data['QueuedDoctor']['created_by'] = $user['User']['id'];
                            if ($this->QueuedDoctor->save($data['QueuedDoctor'])) {
                                
                                // get room id from doctor on user id                        
                                $queryRoomDoctor = mysql_query("SELECT room_id FROM users WHERE id = {$this->data['Patient']['doctor_id']}");
                                while ($resultRoomDotor = mysql_fetch_array($queryRoomDoctor)) {
                                    $roomId = $resultRoomDotor['room_id'];
                                }
                                // check condition room id
                                if($roomId!=""){
                                    $qryNumber = mysql_query("SELECT number_taken FROM queued_doctor_waitings WHERE DATE(created) >= CURDATE() AND room_id = ".$roomId);
                                    while ($rowNumber = mysql_fetch_array($qryNumber)) {
                                        $numberTaken = $rowNumber['number_taken'] + 1;                                    
                                    }  
                                }else{
                                    $qryNumber = mysql_query("SELECT number_taken FROM queued_doctor_waitings WHERE DATE(created) >= CURDATE() AND doctor_id = ".$this->data['Patient']['doctor_id']);
                                    while ($rowNumber = mysql_fetch_array($qryNumber)) {
                                        $numberTaken = $rowNumber['number_taken'] + 1;                                    
                                    } 
                                }                                                            
                                // save number taken for queue in doctor consult
                                $this->QueuedDoctorWaiting->create();
                                $data['QueuedDoctorWaiting']['queue_id'] = $queueId;
                                $data['QueuedDoctorWaiting']['doctor_id'] = $this->data['Patient']['doctor_id'];
                                $data['QueuedDoctorWaiting']['room_id'] = $roomId;
                                $data['QueuedDoctorWaiting']['number_taken'] = $numberTaken;
                                $data['QueuedDoctorWaiting']['created_by'] = $user['User']['id'];
                                if ($this->QueuedDoctorWaiting->save($data['QueuedDoctorWaiting'])) {
                                    echo $queueId;
                                    exit;
                                }
                            }
                        }
                    }                    
                    exit;
                }
            }
        }
        $sexes = array('M' => GENERAL_MALE, 'F' => GENERAL_FEMALE);
        $patientTypes = ClassRegistry::init('PatientType')->find('list', array('conditions' => 'status=1'));
        $patientGroups = ClassRegistry::init('PatientGroup')->find('list', array('conditions' => 'status=1'));
        $patientBillTypes = ClassRegistry::init('PatientBillType')->find('list', array('conditions' => 'status=1'));
        $nationalities = ClassRegistry::init('Nationality')->find('list', array('order'=>array('Nationality.name ASC')));
        $locations = ClassRegistry::init('PatientLocation')->find('list', array('order'=>array('PatientLocation.name ASC')));
        $patientConnections = ClassRegistry::init('PatientConnectionWithHospital')->find('all');
        $doctors = $this->User->find('all', array('conditions' => array('User.is_active' => 1,'UserGroup.group_id' => 3), 'order'=>array('Employee.name ASC'),
                    'fields' => array('User.*, Employee.*'),
                    'joins' => array(
                        array('table' => 'user_employees',
                            'alias' => 'UserEmployee',
                            'type' => 'INNER',
                            'conditions' => array(
                                'User.id = UserEmployee.user_id'
                            )
                        ),
                        array('table' => 'employees',
                            'alias' => 'Employee',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Employee.id = UserEmployee.employee_id'
                            )
                        ),
                        array('table' => 'user_groups',
                            'alias' => 'UserGroup',
                            'type' => 'INNER',
                            'conditions' => array(
                                'User.id = UserGroup.user_id'
                            )
                        )
                )));
        
        $companyInsurances = ClassRegistry::init('CompanyInsurance')->find('list', array('conditions' => 'is_active=1'));
        $this->set('code', '');
        $this->set(compact('sexes', 'patientTypes', 'patientBillTypes', 'patientGroups', 'nationalities', 'locations', 'patientConnections', 'doctors', 'companyInsurances'));
    }

    function editPediatric($id = null) {
        $this->layout = "ajax";
        $this->loadModel('PatientBillType');
        $this->loadModel('PatientType');
        $this->loadModel('Nationality');
        $this->loadModel('PatientConnectionWithHospital');
        $this->loadModel('PatientConnectionDetail');
        $this->loadModel('PatientGroup');
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid patient', true), 'flash_failure');
            $this->redirect(array('action' => 'pediatric'));
        }
        if (!empty($this->data)) {            
            $user = $this->getCurrentUser();                
            if($this->data['Patient']['nationality']==""){
                $this->data['Patient']['nationality'] = 36;
            }
            if(empty($this->data['Patient']['allergic_medicine'])){
                $this->data['Patient']['allergic_medicine'] = 0;
            }
            if(empty($this->data['Patient']['allergic_food'])){
                $this->data['Patient']['allergic_food'] = 0;
            }
            $this->data['Patient']['created_by'] = $user['User']['id'];
            if ($this->Patient->save($this->data)) {                                        
                $patientId = $this->data['Patient']['id'];                 
                $this->PatientConnectionDetail->updateAll(
                    array('PatientConnectionDetail.status' => "2"), array('PatientConnectionDetail.patient_id' => $patientId)
                );
                if(isset($this->data['Patient']['patient_conection_id'])){
                    for ($i = 0; $i < sizeof($this->data['Patient']['patient_conection_id']); $i++) {
                        if($this->data['Patient']['patient_conection_id'][$i]!=""){
                            $this->PatientConnectionDetail->create();
                            $data['PatientConnectionDetail']['patient_id'] = $patientId;
                            $data['PatientConnectionDetail']['patient_connection_with_hospital_id'] = $this->data['Patient']['patient_conection_id'][$i];
                            $this->PatientConnectionDetail->save($data['PatientConnectionDetail']);
                        }                        
                    }    
                }
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                exit;
            }        
        }
        if (empty($this->data)) {
            $this->data = $this->Patient->read(null, $id);
        }
        $sexes = array('M' => GENERAL_MALE, 'F' => GENERAL_FEMALE);
        $patientTypes = ClassRegistry::init('PatientType')->find('list', array('conditions' => 'status=1'));
        $patientGroups = ClassRegistry::init('PatientGroup')->find('list', array('conditions' => 'status=1'));        
        $patientBillTypes = ClassRegistry::init('PatientBillType')->find('list', array('conditions' => 'status=1'));
        $nationalities = ClassRegistry::init('Nationality')->find('list', array('order'=>array('Nationality.name ASC')));
        $locations = ClassRegistry::init('PatientLocation')->find('list', array('order'=>array('PatientLocation.name ASC')));
        $patientConnections = ClassRegistry::init('PatientConnectionWithHospital')->find('all');
        $patientConnectionDetails = ClassRegistry::init('PatientConnectionDetail')->find('list', array('fields' => 'id,patient_connection_with_hospital_id', 'conditions' => array('status'=>1, 'patient_id' => $id))); 
        $this->set('code', '');
        $companyInsurances = ClassRegistry::init('CompanyInsurance')->find('list', array('conditions' => 'is_active=1'));
        $this->set(compact('sexes', 'patientTypes', 'patientBillTypes', 'patientGroups', 'nationalities', 'locations', 'patientConnections', 'patientConnectionDetails', 'companyInsurances'));
    }

    function viewPediatric($id = null) {
        $this->layout = "ajax";
        $this->loadModel('PatientBillType');
        $this->loadModel('PatientType');
        $this->loadModel('Nationality');
        $this->loadModel('PatientConnectionWithHospital');
        $this->loadModel('PatientConnectionDetail');
        if (!$id) {
            $this->Session->setFlash(__('Invalid patient', true), 'flash_failure');
            $this->redirect(array('action' => 'index'));
        }
        
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'Patient.id' => $id),
            'fields' => array('Patient.*, PatientBillType.name, Nationality.name, PatientType.name'),
            'joins' => array(
                array('table' => 'patient_bill_types',
                    'alias' => 'PatientBillType',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientBillType.id = Patient.patient_bill_type_id'
                    )
                ),
                array('table' => 'patient_types',
                    'alias' => 'PatientType',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientType.id = Patient.patient_type_id'
                    )
                ),
                array('table' => 'nationalities',
                    'alias' => 'Nationality',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Nationality.id = Patient.nationality'
                    )
                )
        )));        
        
        $patientConnections = ClassRegistry::init('PatientConnectionWithHospital')->find('all');
        $patientConnectionDetails = ClassRegistry::init('PatientConnectionDetail')->find('list', array('fields' => 'id,patient_connection_with_hospital_id', 'conditions' => array('status'=>1, 'patient_id' => $id)));        
        $this->set(compact('patient', 'patientConnections', 'patientConnectionDetails'));
    }
    
    function printPediatricForm($id = null) {
        $this->layout = "ajax";
        if (!$id) {
            $this->Session->setFlash(__('Invalid patient', true), 'flash_failure');
            $this->redirect(array('action' => 'index'));
        }
        $this->loadModel('PatientBillType');
        $this->loadModel('PatientType');
        $this->loadModel('Nationality');
        $this->loadModel('PatientConnectionWithHospital');
        $this->loadModel('PatientConnectionDetail');
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'Patient.id' => $id),
            'fields' => array('Patient.*, PatientBillType.name, Nationality.name, PatientType.name'),
            'joins' => array(
                array('table' => 'patient_bill_types',
                    'alias' => 'PatientBillType',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientBillType.id = Patient.patient_bill_type_id'
                    )
                ),
                array('table' => 'patient_types',
                    'alias' => 'PatientType',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientType.id = Patient.patient_type_id'
                    )
                ),
                array('table' => 'nationalities',
                    'alias' => 'Nationality',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Nationality.id = Patient.nationality'
                    )
                )
        )));                
        $patientConnections = ClassRegistry::init('PatientConnectionWithHospital')->find('all');
        $patientConnectionDetails = ClassRegistry::init('PatientConnectionDetail')->find('list', array('fields' => 'id,patient_connection_with_hospital_id', 'conditions' => array('status'=>1, 'patient_id' => $id)));        
        $this->set(compact('patient', 'patientConnections', 'patientConnectionDetails'));
    }
    
    function deletePediatric($id = null) {
        if (!$id) {
            $this->Session->setFlash(__(MESSAGE_DATA_INVALID, true), 'flash_failure');
            $this->redirect(array('action'=>'pediatric'));            
        }        
        $user = $this->getCurrentUser();
        $dateTime = date("Y-m-d H:i:s");
        $this->Patient->updateAll(
                array('Patient.is_active' => "2", 'Patient.modified' => "'$dateTime'", 'Patient.modified_by' => $user['User']['id']), array('Patient.id' => $id)
        );
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;                
    }
    

    function printCertificate($id = null) {
        $this->layout = "ajax";
        $patientHistory = array();
        $patientHistory = $this->Patient->getLastPatientHistoryByPatientID($id);
        if (!empty($patientHistory)) {
            $this->loadModel('User');
            $doctor = $this->User->getUserById($patientHistory['PatientHistory']['created_by'] != null ? $patientHistory['PatientHistory']['created_by'] : $patientHistory['PatientHistory']['modified_by']);
            $this->set('doctor', $doctor);
            $this->set('patient', $patientHistory);
            ClassRegistry::init('Country')->id = $this->Patient->field('nationality');
            $this->set('nationality', ClassRegistry::init('Country')->field('name'));
        }
    }

    function printCertificateEn($id = null) {
        $this->printCertificate($id);
    }

    function reAdd($id = null) {
        $this->layout = "ajax";
        $this->data = $this->Patient->read(null, $id);
    }

    function reEdit($id = null) {
        $this->layout = "ajax";
        $this->data = $this->Patient->read(null, $id);
    }

    function getPatient($id = null) {        
        $this->layout = "ajax"; 
        if(isset($_POST['patient'])){
            $id = $_POST['patient'];
        }
        $patients = $this->Patient->getPatient($id);
        $this->set('patients', $patients);
    }
    
//    function searchPatient($id = null) {        
//        $this->layout = "ajax";
//        $patients = $this->Patient->searchPatient($this->params['url']['q']);
//        $this->set('patients', $patients);
//    }

    function getFindPatient(){
        $this->layout = "ajax";
    }
    
    function getFindPatientAjax(){
        $this->layout = "ajax";
    }
    
    function getIpdPatient() {
        $this->layout = "ajax";
        $id = $_POST['patient'];
        $patients = $this->Patient->getIpdPatient($id);
        $this->set('patients', $patients);
    }
   
    function patient_waiting() {
        $user = 10;
        $this->set(compact('user'));
    }

    function queue_patient_waiting($controller=null) {
        $this->loadModel('QueuedPatient');
        $data = $this->QueuedPatient->find('all', array('conditions' => array('in_vaccine' => 1, 'Patient.enabled' => 1, "DAY(QueuedPatient.created) = DAY(NOW()) AND MONTH(QueuedPatient.created) = MONTH(NOW()) AND YEAR(QueuedPatient.created) = YEAR(NOW()) ")));
        return $data;
    }

    function queue_patient_waiting_ajax($controller, $user='') {
        $this->layout = "ajax";
        $this->set('user', $user);
        $this->set('controller', $controller);
    }
    
    function get_queue_waiting_echo($controller=null) {

        $this->loadModel('QueuedPatient');
        $data = $this->QueuedPatient->find('all', array('conditions' => array('in_queue_echo <= 2 AND in_queue_echo >0 ', 'Patient.enabled' => 1, "DAY(QueuedPatient.created) = DAY(NOW()) AND MONTH(QueuedPatient.created) = MONTH(NOW()) AND YEAR(QueuedPatient.created) = YEAR(NOW()) "), 'order'=>array('QueuedPatient.id ASC'), 'limit' => 3));                
        return $data;
    }

    function get_queue_waiting_mid_wife($controller=null) {

        $this->loadModel('QueuedPatient');
        $data = $this->QueuedPatient->find('all', array('conditions' => array('in_queue_labo' => 1, 'Patient.enabled' => 1, "DAY(QueuedPatient.created) = DAY(NOW()) AND MONTH(QueuedPatient.created) = MONTH(NOW()) AND YEAR(QueuedPatient.created) = YEAR(NOW()) "), 'order'=>array('QueuedPatient.id ASC'), 'limit' => 3));
        return $data;
    }

    function get_queue_waiting_doctor($controller=null) {

        $this->loadModel('QueuedPatient');
        $data = $this->QueuedPatient->find('all', array('conditions' => array('in_doctor = 1', 'Patient.enabled' => 1, "DAY(QueuedPatient.created) = DAY(NOW()) AND MONTH(QueuedPatient.created) = MONTH(NOW()) AND YEAR(QueuedPatient.created) = YEAR(NOW()) "), 'order'=>array('QueuedPatient.id ASC'), 'limit' => 3));
        return $data;
    }
    
    function refresh($controller, $user='') {
        $this->layout = "ajax";
        $this->set('user', $user);
        $this->set('controller', $controller);
    }
    
    function get_note() {
        $this->layout = "ajax";
        $str = "";
        $query_action = mysql_query("SELECT note FROM slides WHERE enable = 1 AND action_date  >= CURDATE()");
        while ($action = mysql_fetch_array($query_action)) {//                            
            $note = $action['note'];
            $str.= $note . '... ';
        }
        echo $str;
        exit;
    }

    
    function addQuotation() {
        $this->layout = "ajax";
        $this->loadModel('Location');
        $this->loadModel('Nationality');        
        $this->loadModel('User');
        $this->loadModel('PatientGroup');
        $this->loadModel('ExcludeQuotation');
        $this->loadModel('Section');
        $this->loadModel('Service');
        $this->loadModel('Company');
        $this->loadModel('PatientQuotation');
        $this->loadModel('PatientQuotationExcludeDetail');
        $this->loadModel('PatientQuotationServiceDetail');
        $this->loadModel('CompanyInsurance');
        $user = $this->getCurrentUser();
        
        if (!empty($this->data)) {
            if($this->data['Patient']['id']!=""){
                $user = $this->getCurrentUser();
                if($this->data['Patient']['nationality']==""){
                    $this->data['Patient']['nationality'] = 36;
                }
                $this->data['Patient']['created_by'] = $user['User']['id'];
                $this->Patient->set('patient_code', $this->Helper->getAutoGeneratePatientCode());
                if ($this->Patient->save($this->data)) {                   
                    $patientId = $this->data['Patient']['id'];                    
                    $this->PatientQuotation->create();                    
                    $data['PatientQuotation']['quotation_code'] = $this->Helper->getAutoGenerateQuotationCode();
                    $data['PatientQuotation']['patient_id'] = $patientId;
                    $data['PatientQuotation']['company_id'] = $this->data['Patient']['company_id'];
                    $data['PatientQuotation']['created'] = date('Y-m-d');
                    $data['PatientQuotation']['created_by'] = $user['User']['id'];
                    if ($this->PatientQuotation->save($data['PatientQuotation'])) {
                        $patientQuotationId = $this->PatientQuotation->getLastInsertId();
                        if (isset($this->data['Patient']['exclude_quotation_id'])) {
                            for ($i = 0; $i < sizeof($this->data['Patient']['exclude_quotation_id']); $i++) {
                                $this->PatientQuotationExcludeDetail->create();
                                $data['PatientQuotationExcludeDetail']['patient_quotation_id'] = $patientQuotationId;
                                $data['PatientQuotationExcludeDetail']['exclude_quotation_id'] = $this->data['Patient']['exclude_quotation_id'][$i];                                
                                $this->PatientQuotationExcludeDetail->save($data['PatientQuotationExcludeDetail']);
                            }
                        }

                        if (isset($this->data['Patient']['service_id'])) {
                            for ($i = 0; $i < sizeof($this->data['Patient']['service_id']); $i++) {
                                $this->PatientQuotationServiceDetail->create();
                                $data['PatientQuotationServiceDetail']['patient_quotation_id'] = $patientQuotationId;
                                $data['PatientQuotationServiceDetail']['service_id'] = $this->data['Patient']['service_id'][$i];  
                                $data['PatientQuotationServiceDetail']['qty'] = $this->data['Patient']['qty'][$i];                                                                
                                $data['PatientQuotationServiceDetail']['price'] = $this->data['Patient']['unit_price'][$i];
                                $data['PatientQuotationServiceDetail']['description'] = $this->data['Patient']['description'][$i];
                                $this->PatientQuotationServiceDetail->save($data['PatientQuotationServiceDetail']);
                            }
                        }
                        echo $patientQuotationId;
                        exit;
                    }
                    exit;
                }
            }else{            
                if ($this->Helper->checkDouplicate('patient_code', 'patients', trim($this->data['Patient']['patient_code']))) {
                    $this->Session->setFlash(__('The patient code "'.$this->data['Patient']['patient_code'].'" already exists in the system.', true), 'flash_failure');                
                } else {
                    $this->Patient->create();
                    $user = $this->getCurrentUser();
                    if($this->data['Patient']['nationality']==""){
                        $this->data['Patient']['nationality'] = 36;
                    }      
                    $this->data['Patient']['created_by'] = $user['User']['id'];
                    $this->Patient->set('patient_code', $this->Helper->getAutoGeneratePatientCode());
                    if ($this->Patient->save($this->data)) {                        
                        $patientId = $this->Patient->getLastInsertId();
                        $this->PatientQuotation->create();
                        $data['PatientQuotation']['quotation_code'] = $this->Helper->getAutoGenerateQuotationCode();
                        $data['PatientQuotation']['patient_id'] = $patientId;
                        $data['PatientQuotation']['company_id'] = $this->data['Patient']['company_id'];
                        $data['PatientQuotation']['created'] = date('Y-m-d');
                        $data['PatientQuotation']['created_by'] = $user['User']['id'];
                        if ($this->PatientQuotation->save($data['PatientQuotation'])) {
                            $patientQuotationId = $this->PatientQuotation->getLastInsertId();
                            if (isset($this->data['Patient']['exclude_quotation_id'])) {
                                for ($i = 0; $i < sizeof($this->data['Patient']['exclude_quotation_id']); $i++) {
                                    $this->PatientQuotationExcludeDetail->create();
                                    $data['PatientQuotationExcludeDetail']['patient_quotation_id'] = $patientQuotationId;
                                    $data['PatientQuotationExcludeDetail']['exclude_quotation_id'] = $this->data['Patient']['exclude_quotation_id'][$i];                                
                                    $this->PatientQuotationExcludeDetail->save($data['PatientQuotationExcludeDetail']);
                                }
                            }

                            if (isset($this->data['Patient']['service_id'])) {
                                for ($i = 0; $i < sizeof($this->data['Patient']['service_id']); $i++) {
                                    $this->PatientQuotationServiceDetail->create();
                                    $data['PatientQuotationServiceDetail']['patient_quotation_id'] = $patientQuotationId;
                                    $data['PatientQuotationServiceDetail']['service_id'] = $this->data['Patient']['service_id'][$i];                                                                
                                    $data['PatientQuotationServiceDetail']['price'] = $this->data['Patient']['unit_price'][$i];
                                    $data['PatientQuotationServiceDetail']['description'] = $this->data['Patient']['description'][$i];
                                    $this->PatientQuotationServiceDetail->save($data['PatientQuotationServiceDetail']);
                                }
                            }
                            echo $patientQuotationId;
                            exit;
                        }
                    }
                }
            }
            exit;
        }
        $sexes = array('M' => 'Male', 'F' => 'Female');        
        $excludeQuotations = ClassRegistry::init('ExcludeQuotation')->find('all', array('conditions' => 'is_active=1'));
        $patientGroups = ClassRegistry::init('PatientGroup')->find('list', array('conditions' => 'status=1'));
        $nationalities = ClassRegistry::init('Nationality')->find('list', array('order'=>array('Nationality.name ASC')));
        $locations = ClassRegistry::init('PatientLocation')->find('list', array('order'=>array('PatientLocation.name ASC')));
        $companies = ClassRegistry::init('Company')->find('list',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        $sections = ClassRegistry::init('Section')->find('list', array('conditions' => 'Section.is_active=1'));
        $services = ClassRegistry::init('Service')->find('list', array('conditions' => 'Service.is_active=1'));
        $code = $this->Helper->getAutoGeneratePatientCode();
        $companyInsurances = ClassRegistry::init('CompanyInsurance')->find('list', array('conditions' => 'is_active=1'));
        $patientBillTypes = ClassRegistry::init('PatientBillType')->find('list', array('conditions' => 'status=1'));
        $patientTypes = ClassRegistry::init('PatientType')->find('list', array('conditions' => 'status=1'));
        $this->set(compact('sexes', 'patientGroups', 'nationalities', 'locations', 'excludeQuotations', 'sections', 'services', 'companies', 'code', 'companyInsurances', 'patientBillTypes', 'patientTypes'));
    }
    
    function editQuotation($id=null) {
        $this->layout = "ajax";
        $this->loadModel('Location');
        $this->loadModel('Nationality');        
        $this->loadModel('User');
        $this->loadModel('PatientGroup');
        $this->loadModel('ExcludeQuotation');
        $this->loadModel('Section');
        $this->loadModel('Service');
        $this->loadModel('Company');
        $this->loadModel('PatientQuotation');
        $this->loadModel('PatientQuotationExcludeDetail');
        $this->loadModel('PatientQuotationServiceDetail');
        $this->loadModel('CompanyInsurance');
        $user = $this->getCurrentUser();
        
        if (!empty($this->data)) {
            if ($this->Helper->checkDouplicateEdit('patient_code', 'patients', $this->data['Patient']['id'], trim($this->data['Patient']['patient_code']))) {
                $this->Session->setFlash(__('The patient code "'.$this->data['Patient']['patient_code'].'" already exists in the system.', true), 'flash_failure');
            } else {              
                $this->Patient->create();
                $user = $this->getCurrentUser();
                if($this->data['Patient']['nationality']==""){
                    $this->data['Patient']['nationality'] = 36;
                }
                
                $this->data['Patient']['modifed'] = date('Y-m-d');
                $this->data['Patient']['modified_by'] = $user['User']['id'];
                $this->Patient->set('patient_code', $this->Helper->getAutoGeneratePatientCode());
                if ($this->Patient->save($this->data)) {
                    $patientId = $this->data['Patient']['id'];
                    $this->PatientQuotation->create();                                        
                                        
                    $data['PatientQuotation']['id'] = $this->data['PatientQuotation']['id'];
                    $data['PatientQuotation']['patient_id'] = $patientId;
                    $data['PatientQuotation']['company_id'] = $this->data['Patient']['company_id'];
                    $data['PatientQuotation']['modifed'] = date('Y-m-d');
                    $data['PatientQuotation']['modified_by'] = $user['User']['id'];
                    if ($this->PatientQuotation->save($data['PatientQuotation'])) {
                        $patientQuotationId = $this->data['PatientQuotation']['id'];
                        if (isset($this->data['Patient']['exclude_quotation_id'])) {
                            
                            $this->PatientQuotationExcludeDetail->updateAll(
                                    array('PatientQuotationExcludeDetail.is_active' => "2"), array('PatientQuotationExcludeDetail.patient_quotation_id' => $patientQuotationId)
                            );
                            for ($i = 0; $i < sizeof($this->data['Patient']['exclude_quotation_id']); $i++) {
                                $this->PatientQuotationExcludeDetail->create();
                                $data['PatientQuotationExcludeDetail']['patient_quotation_id'] = $patientQuotationId;
                                $data['PatientQuotationExcludeDetail']['exclude_quotation_id'] = $this->data['Patient']['exclude_quotation_id'][$i];                                
                                $this->PatientQuotationExcludeDetail->save($data['PatientQuotationExcludeDetail']);
                            }
                        }
                        
                        if (isset($this->data['Patient']['service_id'])) {
                            $this->PatientQuotationServiceDetail->updateAll(
                                    array('PatientQuotationServiceDetail.is_active' => "2"), array('PatientQuotationServiceDetail.patient_quotation_id' => $patientQuotationId)
                            );
                            for ($i = 0; $i < sizeof($this->data['Patient']['service_id']); $i++) {
                                $this->PatientQuotationServiceDetail->create();
                                $data['PatientQuotationServiceDetail']['patient_quotation_id'] = $patientQuotationId;
                                $data['PatientQuotationServiceDetail']['service_id'] = $this->data['Patient']['service_id'][$i];                                   
                                $data['PatientQuotationServiceDetail']['qty'] = $this->data['Patient']['qty'][$i];
                                $data['PatientQuotationServiceDetail']['price'] = $this->data['Patient']['unit_price'][$i];
                                $data['PatientQuotationServiceDetail']['description'] = $this->data['Patient']['description'][$i];
                                $this->PatientQuotationServiceDetail->save($data['PatientQuotationServiceDetail']);
                            }
                        }
                        
                        echo $patientQuotationId;
                        exit;
                    }
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }else{
            $this->data = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'PatientQuotation.id' => $id),
                        'fields' => array('Patient.*, PatientQuotation.*, Nationality.name, PatientType.name, Company.id, Company.name'),
                        'joins' => array(
                            array('table' => ' patient_quotations',
                                'alias' => 'PatientQuotation',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'Patient.id = PatientQuotation.patient_id'
                                )
                            ),
                            array('table' => 'patient_types',
                                'alias' => 'PatientType',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'PatientType.id = Patient.patient_type_id'
                                )
                            ),
                            array('table' => 'nationalities',
                                'alias' => 'Nationality',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'Nationality.id = Patient.nationality'
                                )
                            ),
                            array('table' => 'companies',
                                'alias' => 'Company',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'Company.id = PatientQuotation.company_id'
                                )
                            )
                    )));
        }
        $sexes = array('M' => GENERAL_MALE, 'F' => GENERAL_FEMALE);
        $excludeQuotations = ClassRegistry::init('ExcludeQuotation')->find('all', array('conditions' => 'is_active=1'));
        $patientGroups = ClassRegistry::init('PatientGroup')->find('list', array('conditions' => 'status=1'));
        $nationalities = ClassRegistry::init('Nationality')->find('list', array('order'=>array('Nationality.name ASC')));
        $locations = ClassRegistry::init('PatientLocation')->find('list', array('order'=>array('PatientLocation.name ASC')));
        $companies = ClassRegistry::init('Company')->find('all',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        $sections = ClassRegistry::init('Section')->find('list', array('conditions' => 'Section.is_active=1'));
        $services = ClassRegistry::init('Service')->find('all', array('conditions' => 'Service.is_active=1'));    
        $companyInsurances = ClassRegistry::init('CompanyInsurance')->find('list', array('conditions' => 'is_active=1'));
        $patientBillTypes = ClassRegistry::init('PatientBillType')->find('list', array('conditions' => 'status=1'));
        $patientTypes = ClassRegistry::init('PatientType')->find('list', array('conditions' => 'status=1'));
        $this->set(compact('sexes', 'patientGroups', 'nationalities', 'locations', 'excludeQuotations', 'sections', 'services', 'companies', 'companyInsurances', 'patientBillTypes', 'patientTypes'));
    }
    
    function deleteQuotation($id = null) {
        if (!$id) {
            $this->Session->setFlash(__(MESSAGE_DATA_INVALID, true), 'flash_failure');
            $this->redirect(array('action'=>'quotation'));
        }
        $this->loadModel('PatientQuotation');
        $user = $this->getCurrentUser();
        $dateTime = date("Y-m-d H:i:s"); 
        $this->PatientQuotation->updateAll(
                array('PatientQuotation.is_active' => "2", 'PatientQuotation.modified' => "'$dateTime'", 'PatientQuotation.modified_by' => $user['User']['id']), array('PatientQuotation.id' => $id)
        );
        $this->Session->setFlash(__(MESSAGE_DATA_HAS_BEEN_DELETED, true), 'flash_success');
        $this->redirect(array('action'=>'quotation'));
    }
    
    
    function getService($sectionId=null){        
        $this->layout = "ajax";
        $this->loadModel('Service');
        $services = ClassRegistry::init('Service')->find('all', array('conditions' => array('Service.is_active=1', 'Service.section_id' => $sectionId)));        
        $this->set(compact('services'));
    }
    
    function getServicePrice($id=null, $pateintGroup=null, $companyInsuranceId=null) {
        $this->layout = 'ajax';                
        $this->loadModel('ServicesPatientGroupDetail');
        $this->loadModel('ServicesPriceInsurancePatientGroupDetail');
        $this->loadModel('ServicesPriceInsurance');        
        if($companyInsuranceId!="undefined" && $companyInsuranceId!=0){
            $services = $this->ServicesPriceInsurance->find('first', array('conditions' => array('ServicesPriceInsurancePatientGroupDetail.is_active' => 1, 'ServicesPriceInsurance.service_id' => $id, 'ServicesPriceInsurance.company_insurance_id' => $companyInsuranceId, 'ServicesPriceInsurancePatientGroupDetail.patient_group_id' => $pateintGroup),
                'fields' => array('ServicesPriceInsurancePatientGroupDetail.unit_price'),
                'joins' => array(
                    array('table' => 'services_price_insurance_patient_group_details',
                        'alias' => 'ServicesPriceInsurancePatientGroupDetail',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'ServicesPriceInsurance.id = ServicesPriceInsurancePatientGroupDetail.services_price_insurance_id'
                        )
                    )
            )));
            echo $services['ServicesPriceInsurancePatientGroupDetail']['unit_price'];
        }else{
            $services = $this->ServicesPatientGroupDetail->find('first', array('fields' => array('ServicesPatientGroupDetail.unit_price'), 'conditions' => array('ServicesPatientGroupDetail.service_id' => $id, 'ServicesPatientGroupDetail.patient_group_id' => $pateintGroup, 'ServicesPatientGroupDetail.is_active' => 1)));
            echo $services['ServicesPatientGroupDetail']['unit_price'];
        }
        
        exit();
    }
    
    function printPatientQuotation($patientQuotationId = null) {
        $this->layout = "ajax";
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'PatientQuotation.id' => $patientQuotationId),
            'fields' => array('Patient.*, PatientQuotation.*, Nationality.name, PatientType.name, Company.name'),
            'joins' => array(
                array('table' => ' patient_quotations',
                    'alias' => 'PatientQuotation',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Patient.id = PatientQuotation.patient_id'
                    )
                ),
                array('table' => 'patient_types',
                    'alias' => 'PatientType',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientType.id = Patient.patient_type_id'
                    )
                ),
                array('table' => 'nationalities',
                    'alias' => 'Nationality',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Nationality.id = Patient.nationality'
                    )
                ),
                array('table' => 'companies',
                    'alias' => 'Company',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Company.id = PatientQuotation.company_id'
                    )
                )
        )));
        $this->set(compact('patient'));
    }
    
    
    /**
    * Search Patient
    */
    function searchPatient(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $customers = ClassRegistry::init('Patient')->find('all', array(
                        'conditions' => array('OR' => array(
                                'Patient.patient_name LIKE' => '%' . $this->params['url']['q'] . '%',
                                'Patient.patient_code LIKE' => '%' . $this->params['url']['q'] . '%',
                                'Patient.telephone LIKE' => '%' . $this->params['url']['q'] . '%',
                                'Patient.email LIKE' => '%' . $this->params['url']['q'] . '%'
                            ), 'Patient.is_active' => 1
                        ),
                        'joins' => array(
                                array('table' => 'queues',
                                    'alias' => 'Queue',
                                    'type' => 'LEFT',
                                    'conditions' => array(
                                        'Queue.patient_id = Patient.id'
                                    )
                                )
                        ),
                        'limit' => $this->params['url']['limit']
                    ));
        if (!empty($customers)) {
            foreach ($customers as $customer) {
                $name = $customer['Patient']['patient_name'];
                if(!empty($customer['Queue'][0]['id'])){
                    $queueId = $customer['Queue'][0]['id'];
                } else{
                    $queueId = 1;
                }
                
                //echo "{$customer['Patient']['id']}.*{$name}.*{$customer['Patient']['patient_code']}\n";
                echo "{$customer['Patient']['id']}.*{$name}.*{$customer['Patient']['patient_code']}.*{$queueId}\n";
            }
        }else{
            echo '';
        }
        exit;
    }
    
    function printPatientCard($id = null) {
        $this->layout = "ajax";
        if (!$id) {
            $this->Session->setFlash(__('Invalid patient', true), 'flash_failure');
            $this->redirect(array('action' => 'index'));
        }
        $this->loadModel('Company');
        $user = $this->getCurrentUser();
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'Patient.id' => $id), 'recursive' => 0));
        $companies = ClassRegistry::init('Company')->find('first',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        $this->set(compact('patient', 'companies'));
    }  
    
}

?>