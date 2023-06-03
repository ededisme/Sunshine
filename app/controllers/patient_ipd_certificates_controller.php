<?php

class PatientIpdCertificatesController extends AppController {

    var $name = 'PatientIpdCertificates';
    var $components = array('Helper');

    function index() {
        $this->layout = 'ajax';
    }

    function ajax() {
        $this->layout = 'ajax';
    }
    
    function add() {              
        $this->layout = 'ajax';  
        $this->loadModel('User');
        $this->loadModel('PatientGroup');                
        $this->loadModel('PatientIpd');
        $this->loadModel('Patient');
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {            
            $patientIpdId = $this->data['PatientIpd']['id'];                    
            $this->PatientIpdCertificate->create();
            $data['PatientIpdCertificate']['patient_ipd_id'] = $patientIpdId;
            $data['PatientIpdCertificate']['date_certificate_from'] = $this->data['PatientIpdCertificate']['date_certificate_from'];
            $data['PatientIpdCertificate']['date_certificate_to'] = $this->data['PatientIpdCertificate']['date_certificate_to'];
            $data['PatientIpdCertificate']['created'] = date('Y-m-d H:i:s');
            $data['PatientIpdCertificate']['created_by'] = $user['User']['id'];
            if ($this->PatientIpdCertificate->save($data['PatientIpdCertificate'])) {                
                $PatientIpdCertificateId = $this->PatientIpdCertificate->getLastInsertId();
                echo $PatientIpdCertificateId;
                exit;
            }else{
                $result['msg'] = MESSAGE_DATA_COULD_NOT_BE_SAVED;
                $result['error'] = 2;
                echo json_encode($result);
                exit;
            }
        }
        $sexes = array('M' => GENERAL_MALE, 'F' => GENERAL_FEMALE);
        $rooms = ClassRegistry::init('Room')->find('all', array('conditions' => 'Room.is_active=1'));
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
        
        $departments = $this->User->find('all', array('conditions' => array('User.is_active' => 1),'group' => 'Group.id',
                    'fields' => array('*'),
                    'joins' => array(                        
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
                        ),
                        array('table' => 'groups',
                            'alias' => 'Group',
                            'type' => 'INNER',
                            'conditions' => array(
                                'UserGroup.group_id = Group.id'
                            )
                        )
                )));
        
        $companies = ClassRegistry::init('Company')->find('list',
                        array(
                            'joins' => array(
                                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                                )
                            ),
                            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                        )
        );
        
        $this->set(compact('rooms' ,'sexes', 'patientGroups', 'nationalities', 'locations', 'code', 'doctors', 'departments', 'companies'));
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
            $this->PatientIpdCertificate->create();
            $data['PatientIpdCertificate']['id'] = $this->data['PatientIpdCertificate']['id'];
            $data['PatientIpdCertificate']['patient_ipd_id'] = $this->data['PatientIpd']['id'];
            $data['PatientIpdCertificate']['date_certificate_from'] = $this->data['PatientIpdCertificate']['date_certificate_from'];
            $data['PatientIpdCertificate']['date_certificate_to'] = $this->data['PatientIpdCertificate']['date_certificate_to'];
            $data['PatientIpdCertificate']['modified'] = date('Y-m-d H:i:s');
            $data['PatientIpdCertificate']['modified_by'] = $user['User']['id'];
            if ($this->PatientIpdCertificate->save($data['PatientIpdCertificate'])) {               
                $PatientIpdCertificateId = $this->data['PatientIpdCertificate']['id'];
                echo $PatientIpdCertificateId;
                exit;
            }else{
                $result['msg'] = MESSAGE_DATA_COULD_NOT_BE_SAVED;
                $result['error'] = 2;
                echo json_encode($result);
                exit;
            }
        }else{
            $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'PatientIpdCertificate.is_active' => 1, 'PatientIpdCertificate.id' => $id),
                'fields' => array('Patient.*, PatientIpd.*, PatientIpdCertificate.*, Nationality.name, Company.name, Room.room_name'),
                'joins' => array(
                    array('table' => 'patient_ipds',
                        'alias' => 'PatientIpd',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Patient.id = PatientIpd.patient_id'
                        )
                    ),
                    array('table' => 'patient_ipd_certificates',
                        'alias' => 'PatientIpdCertificate',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'PatientIpd.id = PatientIpdCertificate.patient_ipd_id'
                        )
                    ),
                    array('table' => 'patient_stay_in_rooms',
                        'alias' => 'PatientStayInRoom',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'PatientIpd.id = PatientStayInRoom.patient_ipd_id'
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
                            'Company.id = PatientIpd.company_id'
                        )
                    )
            )));

            $doctor = $this->User->find('first', array('conditions' => array('User.is_active' => 1,'User.id' => $patient['PatientIpd']['doctor_id']),'group' => 'User.id',
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

            $department = ClassRegistry::init('Group')->find('first', array('conditions' => array('id' => $patient['PatientIpd']['group_id'])));
            $this->set(compact('patient', 'doctor', 'department'));
        }        
        $this->set(compact('patients', 'doctors', 'departments', 'companies'));
    }
        
    function printPatientIpdCertificate($PatientIpdCertificateId = null) {
        $this->layout = 'ajax'; 
        $this->loadModel('Patient');
        $this->loadModel('User');
        $this->loadModel('Group');
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'PatientIpdCertificate.is_active' => 1, 'PatientIpdCertificate.id' => $PatientIpdCertificateId),
            'fields' => array('Patient.*, PatientIpd.*, PatientIpdCertificate.*, Nationality.name, Company.name, Room.room_name'),
            'joins' => array(
                array('table' => 'patient_ipds',
                    'alias' => 'PatientIpd',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Patient.id = PatientIpd.patient_id'
                    )
                ),
                array('table' => 'patient_ipd_certificates',
                    'alias' => 'PatientIpdCertificate',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientIpd.id = PatientIpdCertificate.patient_ipd_id'
                    )
                ),
                array('table' => 'patient_stay_in_rooms',
                    'alias' => 'PatientStayInRoom',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientIpd.id = PatientStayInRoom.patient_ipd_id'
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
                        'Company.id = PatientIpd.company_id'
                    )
                )
        )));
        
        $doctor = $this->User->find('first', array('conditions' => array('User.is_active' => 1,'User.id' => $patient['PatientIpd']['doctor_id']),'group' => 'User.id',
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
        
        $department = ClassRegistry::init('Group')->find('first', array('conditions' => array('id' => $patient['PatientIpd']['group_id'])));
        
        
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
        
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'PatientIpdCertificate.is_active' => 1, 'PatientIpdCertificate.id' => $id),
            'fields' => array('Patient.*, PatientIpd.*, PatientIpdCertificate.*, Nationality.name, Company.name, Room.room_name'),
            'joins' => array(
                array('table' => 'patient_ipds',
                    'alias' => 'PatientIpd',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Patient.id = PatientIpd.patient_id'
                    )
                ),
                array('table' => 'patient_ipd_certificates',
                    'alias' => 'PatientIpdCertificate',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientIpd.id = PatientIpdCertificate.patient_ipd_id'
                    )
                ),
                array('table' => 'patient_stay_in_rooms',
                    'alias' => 'PatientStayInRoom',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientIpd.id = PatientStayInRoom.patient_ipd_id'
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
                        'Company.id = PatientIpd.company_id'
                    )
                )
        )));
        $doctor = $this->User->find('first', array('conditions' => array('User.is_active' => 1,'User.id' => $patient['PatientIpd']['doctor_id']),'group' => 'User.id',
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
        
        $department = ClassRegistry::init('Group')->find('first', array('conditions' => array('id' => $patient['PatientIpd']['group_id'])));
        
        $this->set(compact('patient', 'doctor', 'department'));
    }
    
    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__(MESSAGE_DATA_INVALID, true), 'flash_failure');
            $this->redirect(array('action'=>'index'));
        }
        $this->loadModel('PatientIpdCertificate');        
        $user = $this->getCurrentUser();
        $dateTime = date("Y-m-d H:i:s"); 
        $this->PatientIpdCertificate->updateAll(
                array('PatientIpdCertificate.is_active' => "0", 'PatientIpdCertificate.modified' => "'$dateTime'", 'PatientIpdCertificate.modified_by' => $user['User']['id']), array('PatientIpdCertificate.id' => $id)
        );
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }
    
    
    function searchPatient($id = null) {
        $this->loadModel('Patient');
        $this->loadModel('PatientIpd');
        $this->layout = 'ajax';
        $patients = $this->Patient->searchPatientIpd($this->params['url']['q']);
        $this->set('patients', $patients);
    }

    function getFindPatient(){
        $this->layout = 'ajax';
    }
    
    function getFindPatientAjax(){
        $this->layout = 'ajax';
    }
    
    function getIpdPatient() {
        $this->layout = 'ajax';
        $id = $_POST['patient'];
        $patients = $this->Patient->getIpdPatient($id);
        $this->set('patients', $patients);
    }
}

?>