<?php

class PatientEmergenciesController extends AppController {

    var $name = 'PatientEmergencies';
    var $components = array('Helper');

    function index() {
        $this->layout = "ajax";
        
    }

    function ajax() {
        $this->layout = "ajax";
        
    }        
    
    function add() {
        $this->layout = "ajax";
        $this->loadModel('Location');
        $this->loadModel('Nationality');        
        $this->loadModel('User');
        $this->loadModel('PatientGroup');        
        $this->loadModel('Section');
        $this->loadModel('Service');
        $this->loadModel('Company');
        $this->loadModel('PatientStayInRoom');
        $this->loadModel('Patient');
        $this->loadModel('Department');
        $user = $this->getCurrentUser();
        
        if (!empty($this->data)) {            
            if($this->data['Patient']['id']!=""){
                $user = $this->getCurrentUser();
                if($this->data['Patient']['nationality']==""){
                    $this->data['Patient']['nationality'] = 36;
                }
                $this->data['Patient']['patient_type_id'] = 1;
                $this->data['Patient']['modified'] = date('Y-m-d H:i:s');
                $this->data['Patient']['modified_by'] = $user['User']['id'];
                $this->Patient->set('patient_code', $this->Helper->getAutoGeneratePatientCode());
                if ($this->Patient->save($this->data)) {                   
                    $patientId = $this->data['Patient']['id'];                    
                    $this->PatientEmergency->create();
                    $this->data['PatientEmergency']['patient_id'] = $patientId;                                                            
                    $this->data['PatientEmergency']['group_id'] = $this->data['PatientEmergency']['department_id'];                   
                    $this->data['PatientEmergency']['emergency_code'] = $this->Helper->getAutoGeneratePatientEmergencyCode();
                    $this->data['PatientEmergency']['created'] = date('Y-m-d H:i:s');
                    $this->data['PatientEmergency']['created_by'] = $user['User']['id'];
                    if ($this->PatientEmergency->save($this->data['PatientEmergency'])) {
                        $patientEmergencyId = $this->PatientEmergency->getLastInsertId();                        
                        echo $patientEmergencyId;
                        exit;
                    }else{
                        echo $result['error'] = 2;
                        exit;
                    }
                    
                }
            }else{            
                if ($this->Helper->checkDouplicate('patient_code', 'Patients', $this->data['Patient']['patient_code'])) {
                    $this->Session->setFlash(__('The patient code "'.$this->data['Patient']['patient_code'].'" already exists in the system.', true), 'flash_failure');                
                } else {
                    $this->Patient->create();
                    $user = $this->getCurrentUser();
                    if($this->data['Patient']['nationality']==""){
                        $this->data['Patient']['nationality'] = 36;
                    }       
                    $this->data['Patient']['patient_type_id'] = 1;
                    $this->data['Patient']['created'] = date('Y-m-d H:i:s');
                    $this->data['Patient']['created_by'] = $user['User']['id'];
                    $this->Patient->set('patient_code', $this->Helper->getAutoGeneratePatientCode());
                    if ($this->Patient->save($this->data)) {                        
                        $patientId = $this->Patient->getLastInsertId();                        
                        $this->PatientEmergency->create();                        
                        $this->data['PatientEmergency']['patient_id'] = $patientId;
                        $this->data['PatientEmergency']['group_id'] = $this->data['PatientEmergency']['department_id'];                   
                        $this->data['PatientEmergency']['emergency_code'] = $this->Helper->getAutoGeneratePatientEmergencyCode();
                        $this->data['PatientEmergency']['created'] = date('Y-m-d H:i:s');
                        $this->data['PatientEmergency']['created_by'] = $user['User']['id'];
                        if ($this->PatientEmergency->save($this->data['PatientEmergency'])) {
                            $patientEmergencyId = $this->PatientEmergency->getLastInsertId();                            
                            echo $patientEmergencyId;
                            exit;
                        }else{
                            echo $result['error'] = 2;
                            exit;
                        }
                    }
                }
            }
        }
        $sexes = array('M' => GENERAL_MALE, 'F' => GENERAL_FEMALE);
        $rooms = ClassRegistry::init('Room')->find('all', array('conditions' => 'Room.is_active=1', 'order' => 'room_name'));
        $patientGroups = ClassRegistry::init('PatientGroup')->find('list', array('conditions' => 'status=1'));
        $nationalities = ClassRegistry::init('Nationality')->find('list');
        $locations = ClassRegistry::init('PatientLocation')->find('list');               
        $code = $this->Helper->getAutoGeneratePatientCode();
        $doctors = $this->User->find('all', array('conditions' => array('User.is_active' => 1,'UserGroup.group_id' => 3),'group' => 'User.id',
                    'fields' => array('User.id, Employee.name, Company.id'),
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
                        array('table' => 'user_companies',
                            'alias' => 'UserCompany',
                            'type' => 'INNER',
                            'conditions' => array(
                                'UserCompany.user_id = User.id'
                            )
                        ),
                        array('table' => 'companies',
                            'alias' => 'Company',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Company.id = UserCompany.company_id'
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
        
        $departments = ClassRegistry::init('Department')->find('all', array('conditions' => 'is_active=1'));
        
        $companies = ClassRegistry::init('Company')->find('list',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        $companyInsurances = ClassRegistry::init('CompanyInsurance')->find('list', array('conditions' => 'is_active=1'));
        $patientBillTypes = ClassRegistry::init('PatientBillType')->find('list', array('conditions' => 'status=1'));
        $patientTypes = ClassRegistry::init('PatientType')->find('list', array('conditions' => 'status=1'));
        $this->set(compact('rooms' ,'sexes', 'patientGroups', 'nationalities', 'locations', 'code', 'doctors', 'departments', 'companies', 'companyInsurances', 'patientBillTypes', 'patientTypes'));
    }
    
    
    function edit($id = null) {
        $this->layout = 'ajax';    
        $this->loadModel('Location');
        $this->loadModel('Nationality');        
        $this->loadModel('User');
        $this->loadModel('PatientGroup');        
        $this->loadModel('Section');
        $this->loadModel('Service');
        $this->loadModel('Company');
        $this->loadModel('PatientStayInRoom');
        $this->loadModel('Patient');
        $user = $this->getCurrentUser();
        
        if (!empty($this->data)) {                        
            if($this->data['Patient']['id']!=""){                
                $user = $this->getCurrentUser();
                if($this->data['Patient']['nationality']==""){
                    $this->data['Patient']['nationality'] = 36;
                }
                $this->data['Patient']['modified'] = date('Y-m-d H:i:s');
                $this->data['Patient']['modified_by'] = $user['User']['id'];                
                if ($this->Patient->save($this->data)) {                   
                    $patientId = $this->data['Patient']['id'];
                    $this->PatientEmergency->create();
                    $this->data['PatientEmergency']['patient_id'] = $patientId;
                    $this->data['PatientEmergency']['company_id'] = $this->data['PatientEmergency']['company_id'];                    
                    $this->data['PatientEmergency']['group_id'] = $this->data['PatientEmergency']['department_id'];
                    $this->data['PatientEmergency']['modified'] = date('Y-m-d H:i:s');
                    $this->data['PatientEmergency']['modified_by'] = $user['User']['id'];
                    if ($this->PatientEmergency->save($this->data['PatientEmergency'])) {
                        $patientEmergencyId = $this->data['PatientEmergency']['id'];
                        $this->PatientStayInRoom->updateAll(
                                array('PatientStayInRoom.status' => "0"), array('PatientStayInRoom.patient_ipd_id' => $patientEmergencyId)
                        );
                        $this->PatientStayInRoom->create();
                        $data['PatientStayInRoom']['patient_ipd_id'] = $patientEmergencyId;
                        $data['PatientStayInRoom']['room_id'] = $this->data['PatientEmergency']['room_id'];                                
                        $this->PatientStayInRoom->save($data['PatientStayInRoom']);
                        echo $patientEmergencyId;
                        exit;
                    }else{
                        echo $result['error'] = 2;
                        exit;
                    }
                }
            }
        }else{
            $this->data = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'PatientStayInRoom.status' => 1, 'PatientEmergency.id' => $id),
                        'fields' => array('Patient.*, PatientEmergency.*, Nationality.name, PatientType.name, Company.id, Room.id'),
                        'joins' => array(
                            array('table' => ' patient_emergencies',
                                'alias' => 'PatientEmergency',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'Patient.id = PatientEmergency.patient_id'
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
                                    'Company.id = PatientEmergency.company_id'
                                )
                            ),
                            array('table' => 'patient_stay_in_rooms',
                                'alias' => 'PatientStayInRoom',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'PatientEmergency.id = PatientStayInRoom.patient_ipd_id'
                                )
                            ),
                            array('table' => 'rooms',
                                'alias' => 'Room',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Room.id = PatientStayInRoom.room_id'
                                )
                            )
                    )));
        }
        $sexes = array('M' => GENERAL_MALE, 'F' => GENERAL_FEMALE);
        $rooms = ClassRegistry::init('Room')->find('all', array('conditions' => 'Room.is_active=1', 'order' => 'room_name'));
        $patientGroups = ClassRegistry::init('PatientGroup')->find('list', array('conditions' => 'status=1'));
        $nationalities = ClassRegistry::init('Nationality')->find('list');
        $locations = ClassRegistry::init('PatientLocation')->find('list');               
        $code = $this->Helper->getAutoGeneratePatientCode();
        $doctors = $this->User->find('all', array('conditions' => array('User.is_active' => 1,'UserGroup.group_id' => 3),'group' => 'User.id',
                    'fields' => array('User.id, Employee.name, Company.id'),
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
                        array('table' => 'user_companies',
                            'alias' => 'UserCompany',
                            'type' => 'INNER',
                            'conditions' => array(
                                'UserCompany.user_id = User.id'
                            )
                        ),
                        array('table' => 'companies',
                            'alias' => 'Company',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Company.id = UserCompany.company_id'
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
        
        $departments = ClassRegistry::init('Department')->find('all', array('conditions' => 'is_active=1'));        
        $companies = ClassRegistry::init('Company')->find('list',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        $companyInsurances = ClassRegistry::init('CompanyInsurance')->find('list', array('conditions' => 'is_active=1'));
        $patientBillTypes = ClassRegistry::init('PatientBillType')->find('list', array('conditions' => 'status=1'));
        $patientTypes = ClassRegistry::init('PatientType')->find('list', array('conditions' => 'status=1'));
        $this->set(compact('rooms' ,'sexes', 'patientGroups', 'nationalities', 'locations', 'code', 'doctors', 'departments', 'companies', 'companyInsurances', 'patientBillTypes', 'patientTypes'));
    }
        
    function printPatientEmergency($patientEmergencyId = null) {
        $this->layout = 'ajax'; 
        $this->loadModel('Patient');
        $this->loadModel('User');
        $this->loadModel('Group');
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'PatientEmergency.id' => $patientEmergencyId),
            'fields' => array('Patient.*, PatientEmergency.*, Nationality.name, Company.name, Room.room_name'),
            'joins' => array(
                array('table' => 'patient_emergencies',
                    'alias' => 'PatientEmergency',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Patient.id = PatientEmergency.patient_id'
                    )
                ),
                array('table' => 'patient_stay_in_rooms',
                    'alias' => 'PatientStayInRoom',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientEmergency.id = PatientStayInRoom.patient_ipd_id'
                    )
                ),
                array('table' => 'rooms',
                    'alias' => 'Room',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Room.id = PatientEmergency.room_id'
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
                        'Company.id = PatientEmergency.company_id'
                    )
                )
        )));
        $doctor = $this->User->find('first', array('conditions' => array('User.is_active' => 1,'User.id' => $patient['PatientEmergency']['doctor_id']),
                    'fields' => array('Employee.name'),
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
                        )
                )));
        
        $department = ClassRegistry::init('Group')->find('first', array('conditions' => array('id' => $patient['PatientEmergency']['group_id'])));        
        
        $this->set(compact('patient', 'doctor', 'department'));
    }
    
    function view($id = null) {
        $this->layout = 'ajax';    
        $this->loadModel('Patient');
        $this->loadModel('User');
        $this->loadModel('Group');
        if (!$id) {
            $this->Session->setFlash(__('Invalid patient', true), 'flash_failure');
            $this->redirect(array('action' => 'quotation'));
        }
        
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'PatientEmergency.id' => $id),
            'fields' => array('Patient.*, PatientEmergency.*, Nationality.name, Company.name, Room.room_name'),
            'joins' => array(
                array('table' => 'patient_emergencies',
                    'alias' => 'PatientEmergency',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Patient.id = PatientEmergency.patient_id'
                    )
                ),
                array('table' => 'patient_stay_in_rooms',
                    'alias' => 'PatientStayInRoom',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientEmergency.id = PatientStayInRoom.patient_ipd_id'
                    )
                ),
                array('table' => 'rooms',
                    'alias' => 'Room',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Room.id = PatientStayInRoom.room_id'
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
                        'Company.id = PatientEmergency.company_id'
                    )
                )
        )));
        
        $doctor = $this->User->find('first', array('conditions' => array('User.is_active' => 1,'User.id' => $patient['PatientEmergency']['doctor_id']),
                    'fields' => array('Employee.name'),
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
                        )
                )));
        
        $department = ClassRegistry::init('Group')->find('first', array('conditions' => array('id' => $patient['PatientEmergency']['group_id'])));
        
        $this->set(compact('patient', 'doctor', 'department'));
    }
    
    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__(MESSAGE_DATA_INVALID, true), 'flash_failure');
            $this->redirect(array('action'=>'index'));
        }
        $this->loadModel('PatientEmergency');
        $user = $this->getCurrentUser();
        $dateTime = date("Y-m-d H:i:s"); 
        $this->PatientEmergency->updateAll(
                array('PatientEmergency.is_active' => "0", 'PatientEmergency.modified' => "'$dateTime'", 'PatientEmergency.modified_by' => $user['User']['id']), array('PatientEmergency.id' => $id)
        );
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }
    
    function viewDetail($id = null){
        $this->layout = 'ajax';   
        $this->loadModel('Patient');
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'PatientEmergency.id' => $id),
            'fields' => array('Patient.*, PatientEmergency.*, Nationality.name, Company.name, Room.room_name'),
            'joins' => array(
                array('table' => 'patient_emergencies',
                    'alias' => 'PatientEmergency',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Patient.id = PatientEmergency.patient_id'
                    )
                ),
                array('table' => 'patient_stay_in_rooms',
                    'alias' => 'PatientStayInRoom',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientEmergency.id = PatientStayInRoom.patient_ipd_id'
                    )
                ),
                array('table' => 'rooms',
                    'alias' => 'Room',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Room.id = PatientStayInRoom.room_id'
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
                        'Company.id = PatientEmergency.company_id'
                    )
                )
        )));
        $this->set('patient', $patient);        
    }
    
    function tabObservation($id = null) {
        $this->layout = 'ajax';
        $this->loadModel('PatientEmergencyObservation');
        $consultation = $this->PatientEmergencyObservation->find('all', array('conditions' => array('PatientEmergencyObservation.is_active' => 1, 'PatientEmergency.id' => $id),
            'fields' => array('PatientEmergency.emergency_code, PatientEmergencyObservation.*'),
            'joins' => array(
                array('table' => 'patient_emergencies',
                    'alias' => 'PatientEmergency',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientEmergencyObservation.patient_emergency_id = PatientEmergency.id'
                    )
                )
        )));
        $this->set(compact('id', 'consultation'));
    }        
    
    function addObservation($id = null) {
        $this->layout = 'ajax';
        if (!empty($this->data)) {
            $this->loadModel('PatientEmergencyObservation');
            $user = $this->getCurrentUser();
            $this->PatientEmergencyObservation->create();
            $this->data['PatientEmergencyObservation']['patient_emergency_id'] = $this->data['PatientEmergency']['id'];                                                            
            $this->data['PatientEmergencyObservation']['entry_motif'] = $this->data['PatientEmergency']['entry_motif'];
            $this->data['PatientEmergencyObservation']['present_medical_condition'] = $this->data['PatientEmergency']['present_medical_condition'];
            $this->data['PatientEmergencyObservation']['medical'] = $this->data['PatientEmergency']['medical'];
            $this->data['PatientEmergencyObservation']['surfical'] = $this->data['PatientEmergency']['surfical'];
            $this->data['PatientEmergencyObservation']['general_sign'] = $this->data['PatientEmergency']['general_sign'];
            $this->data['PatientEmergencyObservation']['cadiovascular'] = $this->data['PatientEmergency']['cadiovascular'];
            $this->data['PatientEmergencyObservation']['respiratory'] = $this->data['PatientEmergency']['respiratory'];
            $this->data['PatientEmergencyObservation']['digestifs'] = $this->data['PatientEmergency']['digestifs'];
            $this->data['PatientEmergencyObservation']['uro_genital'] = $this->data['PatientEmergency']['uro_genital'];
            $this->data['PatientEmergencyObservation']['created'] = date('Y-m-d H:i:s');
            $this->data['PatientEmergencyObservation']['created_by'] = $user['User']['id'];
            if ($this->PatientEmergencyObservation->save($this->data['PatientEmergencyObservation'])) {
                echo "success";exit;
            }
        }
        $this->set('id', $id);    
        
    }
    
    function editObservation($id = null, $emergencyId = null) {
        $this->layout = 'ajax';
        if (!empty($this->data)) {
            $this->loadModel('PatientEmergencyObservation');
            $user = $this->getCurrentUser();
            $this->PatientEmergencyObservation->create();
            $this->data['PatientEmergencyObservation']['id'] = $id;
            $this->data['PatientEmergencyObservation']['patient_emergency_id'] = $emergencyId;
            $this->data['PatientEmergencyObservation']['entry_motif'] = $this->data['PatientEmergency']['entry_motif'];
            $this->data['PatientEmergencyObservation']['present_medical_condition'] = $this->data['PatientEmergency']['present_medical_condition'];
            $this->data['PatientEmergencyObservation']['medical'] = $this->data['PatientEmergency']['medical'];
            $this->data['PatientEmergencyObservation']['surfical'] = $this->data['PatientEmergency']['surfical'];
            $this->data['PatientEmergencyObservation']['general_sign'] = $this->data['PatientEmergency']['general_sign'];
            $this->data['PatientEmergencyObservation']['cadiovascular'] = $this->data['PatientEmergency']['cadiovascular'];
            $this->data['PatientEmergencyObservation']['respiratory'] = $this->data['PatientEmergency']['respiratory'];
            $this->data['PatientEmergencyObservation']['digestifs'] = $this->data['PatientEmergency']['digestifs'];
            $this->data['PatientEmergencyObservation']['uro_genital'] = $this->data['PatientEmergency']['uro_genital'];
            $this->data['PatientEmergencyObservation']['modified'] = date('Y-m-d H:i:s');
            $this->data['PatientEmergencyObservation']['modified_by'] = $user['User']['id'];
            if ($this->PatientEmergencyObservation->save($this->data['PatientEmergencyObservation'])) {                                
                echo "success";
                exit;
            }
        }
    }
    
    function deleteObservation($id = null){
        $this->loadModel('PatientEmergencyObservation');
        $user = $this->getCurrentUser();
        $dateTime = date("Y-m-d H:i:s"); 
        $this->PatientEmergencyObservation->updateAll(
                array('PatientEmergencyObservation.is_active' => "0", 'PatientEmergencyObservation.modified' => "'$dateTime'", 'PatientEmergencyObservation.modified_by' => $user['User']['id']), array('PatientEmergencyObservation.id' => $id)
        );
        echo "success";
        exit;
    }
    
    function printObservationMedical($id = null){
        $this->layout = 'ajax'; 
        $this->loadModel('PatientEmergencyObservation');
        $result = $this->PatientEmergencyObservation->find('first', array('conditions' => array('PatientEmergencyObservation.is_active' => 1, 'PatientEmergencyObservation.id' => $id),
            'fields' => array('PatientEmergency.emergency_code, PatientEmergencyObservation.*'),
            'joins' => array(
                array('table' => 'patient_emergencies',
                    'alias' => 'PatientEmergency',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientEmergencyObservation.patient_emergency_id = PatientEmergency.id'
                    )
                )
        )));
        $this->set(compact('result'));
    }
    
    function tabEvolutionNum($id = null) {
        $this->layout = 'ajax';
        $this->loadModel('PatientEmergencyEvolution');
        $consultation = $this->PatientEmergencyEvolution->find('all', array('conditions' => array('PatientEmergencyEvolution.is_active' => 1, 'PatientEmergency.id' => $id),
            'fields' => array('PatientEmergency.emergency_code, PatientEmergencyEvolution.*'),
            'joins' => array(
                array('table' => 'patient_emergencies',
                    'alias' => 'PatientEmergency',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientEmergencyEvolution.patient_emergency_id = PatientEmergency.id'
                    )
                )
        )));
        $this->set(compact('id', 'consultation'));
    }
    function addEvolutionNum($id = null) {
        $this->layout = 'ajax';
        if (!empty($this->data)) {
            $this->loadModel('PatientEmergencyEvolution');
            $user = $this->getCurrentUser();
            $this->PatientEmergencyEvolution->create();
            $this->data['PatientEmergencyEvolution']['patient_emergency_id'] = $this->data['PatientEmergency']['id'];                                                            
            $this->data['PatientEmergencyEvolution']['date_evolution'] = $this->data['PatientEmergency']['date_evolution'];
            $this->data['PatientEmergencyEvolution']['evolution_description'] = $this->data['PatientEmergency']['evolution_description'];
            $this->data['PatientEmergencyEvolution']['prescription'] = $this->data['PatientEmergency']['prescription'];        
            $this->data['PatientEmergencyEvolution']['created'] = date('Y-m-d H:i:s');
            $this->data['PatientEmergencyEvolution']['created_by'] = $user['User']['id'];
            if ($this->PatientEmergencyEvolution->save($this->data['PatientEmergencyEvolution'])) {
                echo "success";exit;
            }
        }
        $this->set('id', $id);    
        
    }
    
    function editEvolutionNum($id = null, $emergencyId = null) {
        $this->layout = 'ajax';
        if (!empty($this->data)) {            
            $this->loadModel('PatientEmergencyEvolution');
            $user = $this->getCurrentUser();
            $this->PatientEmergencyEvolution->create();
            $this->data['PatientEmergencyEvolution']['id'] = $id;
            $this->data['PatientEmergencyEvolution']['patient_emergency_id'] = $emergencyId;            
            $this->data['PatientEmergencyEvolution']['date_evolution'] = $this->data['PatientEmergency']['edit_date_evolution'];
            $this->data['PatientEmergencyEvolution']['evolution_description'] = $this->data['PatientEmergency']['edit_evolution_description'];
            $this->data['PatientEmergencyEvolution']['prescription'] = $this->data['PatientEmergency']['prescription'];        
            $this->data['PatientEmergencyEvolution']['modified'] = date('Y-m-d H:i:s');
            $this->data['PatientEmergencyEvolution']['modified_by'] = $user['User']['id'];
            if ($this->PatientEmergencyEvolution->save($this->data['PatientEmergencyEvolution'])) {                
                echo "success";
                exit;
            }
        }
        
    }
    
    function deleteEvolutionNum($id = null){
        $this->loadModel('PatientEmergencyEvolution');
        $user = $this->getCurrentUser();
        $dateTime = date("Y-m-d H:i:s"); 
        $this->PatientEmergencyEvolution->updateAll(
                array('PatientEmergencyEvolution.is_active' => "0", 'PatientEmergencyEvolution.modified' => "'$dateTime'", 'PatientEmergencyEvolution.modified_by' => $user['User']['id']), array('PatientEmergencyEvolution.id' => $id)
        );
        echo "success";
        exit;
    }
    
    function printEvolutionNum($id = null){
        $this->layout = 'ajax'; 
        $this->loadModel('PatientEmergencyEvolution');
        $result = $this->PatientEmergencyEvolution->find('first', array('conditions' => array('PatientEmergencyEvolution.is_active' => 1, 'PatientEmergencyEvolution.id' => $id),
            'fields' => array('PatientEmergency.emergency_code, PatientEmergencyEvolution.*'),
            'joins' => array(
                array('table' => 'patient_emergencies',
                    'alias' => 'PatientEmergency',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientEmergencyEvolution.patient_emergency_id = PatientEmergency.id'
                    )
                )
        )));
        $this->set(compact('result'));
    }
}

?>