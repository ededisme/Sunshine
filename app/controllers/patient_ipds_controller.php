<?php

class PatientIpdsController extends AppController {

    var $name = 'PatientIpds';
    var $uses = array('PatientLeave');
    var $components = array('Helper');

    function index() {
        $this->layout = 'ajax';
    }

    function ajax($status = "all", $companyInsurance = 'all', $dateFrom = '', $dateTo = '') {
        $this->layout = 'ajax';
        $this->set(compact('status', 'companyInsurance', 'dateFrom', 'dateTo'));
    }

    function addService($patientIpdId = null, $patientId = null, $ipdType = null, $view = null) {
        $this->layout = 'ajax';
        if (empty($this->data)) {
            $this->loadModel('Section');
            $this->loadModel('Service');
            $this->loadModel('Company');
            $this->loadModel('User');
            $this->loadModel('AccountType');
            $this->loadModel('Patient');
            $this->loadModel('PatientIpd');
            $this->loadModel('PatientIpdServiceDetail');
            $this->loadModel('DoctorConsultation');
            $user = $this->getCurrentUser();
            $companies = ClassRegistry::init('Company')->find('all', array(
                'joins' => array(
                    array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                    )
                ),
                'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                    )
            );
            $sections = ClassRegistry::init('Section')->find('list', array('conditions' => 'Section.is_active=1'));
            $services = ClassRegistry::init('Service')->find('list', array('conditions' => 'Service.is_active=1'));

            $patientIpd = ClassRegistry::init('PatientIpd')->find('first', array('conditions' => array('PatientIpd.is_active=1', 'PatientIpd.id' => $patientIpdId)));
            $dataServiceDetail = $this->PatientIpdServiceDetail->find('all', array('conditions' => array('PatientIpdServiceDetail.is_active >' => 0, 'PatientIpdServiceDetail.type' => 1, 'PatientIpdServiceDetail.patient_ipd_id' => $patientIpdId, 'PatientIpd.patient_id' => $patientId),
                'fields' => array('PatientIpdServiceDetail.*, Section.id'),
                'joins' => array(
                    array('table' => 'services',
                        'alias' => 'Service',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Service.id = PatientIpdServiceDetail.service_id'
                        )
                    ),
                    array('table' => 'sections',
                        'alias' => 'Section',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Section.id = Service.section_id'
                        )
                    ),
                    array('table' => 'patient_ipds',
                        'alias' => 'PatientIpd',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'PatientIpd.id = PatientIpdServiceDetail.patient_ipd_id'
                        )
                    )
            )));

            $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'Patient.id' => $patientId),
                'fields' => array('Patient.*, PatientBillType.name, PatientBillType.id, Nationality.name, PatientType.name'),
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

            $doctors = $this->User->find('all', array('conditions' => array('User.is_active' => 1, 'UserGroup.group_id' => array('2', '21')), 'order' => array('Employee.name ASC'), 'group' => 'User.id',
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
            $arAccount = ClassRegistry::init('AccountType')->findById(7);
            $arAccountId = $arAccount['AccountType']['chart_account_id'];
            $companyInsurances = ClassRegistry::init('CompanyInsurance')->find('list', array('conditions' => 'is_active=1'));
            $this->set(compact('sections', 'services', 'patient', 'companies', 'doctors', 'arAccountId', 'locations', 'companyInsurances', 'patientIpdId', 'dataServiceDetail', 'patientIpd', 'ipdType', 'view'));
        }
        if (!empty($this->data)) {
            $user = $this->getCurrentUser();
            $date = date('Y-m-d H:i:s');
            $this->loadModel('PatientIpd');
            $this->loadModel('PatientIpdServiceDetail');
            if (isset($this->data['PatientIpd']['company_insurance_id']) && $this->data['PatientIpd']['company_insurance_id'] != "") {
                $this->PatientIpd->updateAll(
                        array('PatientIpd.company_insurance_id' => $this->data['PatientIpd']['company_insurance_id'], 'PatientIpd.modified' => "'$date'", 'PatientIpd.modified_by' => $user['User']['id']), array('PatientIpd.id' => $this->data['PatientIpd']['id'])
                );
            }
            $this->PatientIpdServiceDetail->updateAll(
                    array('PatientIpdServiceDetail.is_active' => "0", 'PatientIpdServiceDetail.modified' => "'$date'", 'PatientIpdServiceDetail.modified_by' => $user['User']['id']), array('PatientIpdServiceDetail.patient_ipd_id' => $this->data['PatientIpd']['id'], 'PatientIpdServiceDetail.is_active' => 1, 'PatientIpdServiceDetail.type' => 1)
            );
            for ($i = 0; $i < sizeof($this->data['Patient']['section_id']); $i++) {
                if ($this->data['Patient']['section_id'][$i] != '') {
                    $this->PatientIpdServiceDetail->create();
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['patient_ipd_id'] = $this->data['PatientIpd']['id'];
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['exchange_rate_id'] = $this->data['Patient']['exchange_rate_id'];
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['service_id'] = $this->data['Patient']['service_id'][$i];
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['doctor_id'] = $this->data['Patient']['doctor_id'][$i];
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['qty'] = $this->data['Patient']['qty'][$i];
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['date_created'] = $this->data['Patient']['date_created'][$i];
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['unit_price'] = $this->data['Patient']['unit_price'][$i];
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['total_price'] = $this->data['Patient']['total_price'][$i];
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['created_by'] = $user['User']['id'];
                    $this->PatientIpdServiceDetail->save($PatientIpdServiceDetail['PatientIpdServiceDetail']);
                    // marady
                }
            }
            echo MESSAGE_DATA_HAS_BEEN_SAVED;
            exit;
        }
    }

    function addServiceMedicalSurgery($patientIpdId = null, $patientId = null, $ipdType = null) {
        $this->layout = 'ajax';
        if (empty($this->data)) {
            $this->loadModel('Section');
            $this->loadModel('Service');
            $this->loadModel('Company');
            $this->loadModel('User');
            $this->loadModel('AccountType');
            $this->loadModel('Patient');
            $this->loadModel('PatientIpd');
            $this->loadModel('PatientIpdServiceDetail');
            $user = $this->getCurrentUser();
            $companies = ClassRegistry::init('Company')->find('all', array(
                'joins' => array(
                    array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                    )
                ),
                'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                    )
            );
            $sections = ClassRegistry::init('Section')->find('list', array('conditions' => 'Section.is_active=1'));
            $services = ClassRegistry::init('Service')->find('list', array('conditions' => 'Service.is_active=1'));

            $patientIpd = ClassRegistry::init('PatientIpd')->find('first', array('conditions' => array('PatientIpd.is_active=1', 'PatientIpd.id' => $patientIpdId)));
            $dataServiceDetail = $this->PatientIpdServiceDetail->find('all', array('conditions' => array('PatientIpdServiceDetail.is_active >' => 0, 'PatientIpdServiceDetail.patient_ipd_id' => $patientIpdId, 'PatientIpd.patient_id' => $patientId),
                'fields' => array('PatientIpdServiceDetail.*, Section.id'),
                'joins' => array(
                    array('table' => 'services',
                        'alias' => 'Service',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Service.id = PatientIpdServiceDetail.service_id'
                        )
                    ),
                    array('table' => 'sections',
                        'alias' => 'Section',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Section.id = Service.section_id'
                        )
                    ),
                    array('table' => 'patient_ipds',
                        'alias' => 'PatientIpd',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'PatientIpd.id = PatientIpdServiceDetail.patient_ipd_id'
                        )
                    )
            )));

            $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'Patient.id' => $patientId),
                'fields' => array('Patient.*, PatientBillType.name, PatientBillType.id, Nationality.name, PatientType.name'),
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
            $doctors = ClassRegistry::init('DoctorConsultation')->find('all', array(
                'order' => array('DoctorConsultation.name ASC'),
                'conditions' => array('DoctorConsultation.is_active' => 1)
            ));
            $arAccount = ClassRegistry::init('AccountType')->findById(7);
            $arAccountId = $arAccount['AccountType']['chart_account_id'];
            $companyInsurances = ClassRegistry::init('CompanyInsurance')->find('list', array('conditions' => 'is_active=1'));
            $this->set(compact('sections', 'services', 'patient', 'companies', 'doctors', 'arAccountId', 'locations', 'companyInsurances', 'patientIpdId', 'dataServiceDetail', 'patientIpd', 'ipdType'));
        }
        if (!empty($this->data)) {
            $user = $this->getCurrentUser();
            $date = date('Y-m-d H:i:s');
            $this->loadModel('PatientIpd');
            $this->loadModel('PatientIpdServiceDetail');
            if (isset($this->data['PatientIpd']['company_insurance_id']) && $this->data['PatientIpd']['company_insurance_id'] != "") {
                $this->PatientIpd->updateAll(
                        array('PatientIpd.company_insurance_id' => $this->data['PatientIpd']['company_insurance_id'], 'PatientIpd.modified' => "'$date'", 'PatientIpd.modified_by' => $user['User']['id']), array('PatientIpd.id' => $this->data['PatientIpd']['id'])
                );
            }
            $this->PatientIpdServiceDetail->updateAll(
                    array('PatientIpdServiceDetail.is_active' => "0", 'PatientIpdServiceDetail.modified' => "'$date'", 'PatientIpdServiceDetail.modified_by' => $user['User']['id']), array('PatientIpdServiceDetail.patient_ipd_id' => $this->data['PatientIpd']['id'], 'PatientIpdServiceDetail.is_active' => 1)
            );
            for ($i = 0; $i < sizeof($this->data['Patient']['section_id']); $i++) {
                if ($this->data['Patient']['section_id'][$i] != '') {
                    $this->PatientIpdServiceDetail->create();
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['patient_ipd_id'] = $this->data['PatientIpd']['id'];
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['exchange_rate_id'] = $this->data['Patient']['exchange_rate_id'];
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['service_id'] = $this->data['Patient']['service_id'][$i];
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['doctor_id'] = $this->data['Patient']['doctor_id'][$i];
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['qty'] = $this->data['Patient']['qty'][$i];
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['date_created'] = $this->data['Patient']['date_created'][$i];
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['unit_price'] = $this->data['Patient']['unit_price'][$i];
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['total_price'] = $this->data['Patient']['total_price'][$i];
                    $PatientIpdServiceDetail['PatientIpdServiceDetail']['created_by'] = $user['User']['id'];
                    $this->PatientIpdServiceDetail->save($PatientIpdServiceDetail['PatientIpdServiceDetail']);
                }
            }
            echo MESSAGE_DATA_HAS_BEEN_SAVED;
            exit;
        }
    }

    function getService($sectionId = null) {
        $this->layout = 'ajax';
        $this->loadModel('Service');
        $services = ClassRegistry::init('Service')->find('all', array('conditions' => array('Service.is_active=1', 'Service.section_id' => $sectionId)));
        $this->set(compact('services'));
    }

    function getServicePrice($id = null, $pateintGroup = null, $companyInsuranceId = null) {
        $this->layout = 'ajax';
        $this->loadModel('ServicesPatientGroupDetail');
        $this->loadModel('ServicesPriceInsurancePatientGroupDetail');
        $this->loadModel('ServicesPriceInsurance');
        if ($companyInsuranceId != "undefined" && $companyInsuranceId != 0) {
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
        } else {
            $services = $this->ServicesPatientGroupDetail->find('first', array('fields' => array('ServicesPatientGroupDetail.unit_price'), 'conditions' => array('ServicesPatientGroupDetail.service_id' => $id, 'ServicesPatientGroupDetail.patient_group_id' => $pateintGroup, 'ServicesPatientGroupDetail.is_active' => 1)));
            echo $services['ServicesPatientGroupDetail']['unit_price'];
        }

        exit();
    }

    function add() {
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
        $this->loadModel('DoctorConsultation');
        $this->loadModel('PatientIpd');

        $user = $this->getCurrentUser();

        if (!empty($this->data)) {
            if ($this->data['Patient']['id'] != "") {
                $user = $this->getCurrentUser();
                if ($this->data['Patient']['nationality'] == "") {
                    $this->data['Patient']['nationality'] = 36;
                }
                $this->data['Patient']['patient_type_id'] = 1;
                $this->data['Patient']['modified'] = date('Y-m-d H:i:s');
                $this->data['Patient']['modified_by'] = $user['User']['id'];
                $this->Patient->set('patient_code', $this->Helper->getAutoGeneratePatientCode());
                if ($this->Patient->save($this->data)) {
                    $patientId = $this->data['Patient']['id'];
                    $this->PatientIpd->create();
                    $data['PatientIpd']['patient_id'] = $patientId;
                    $data['PatientIpd']['company_id'] = $this->data['PatientIpd']['company_id'];
                    $data['PatientIpd']['date_ipd'] = $this->data['PatientIpd']['date_ipd'];
                    $data['PatientIpd']['doctor_id'] = $this->data['PatientIpd']['doctor_id'];
                    $data['PatientIpd']['group_id'] = $this->data['PatientIpd']['department_id'];
                    $data['PatientIpd']['allergies'] = $this->data['PatientIpd']['allergies'];
                    $data['PatientIpd']['witness_name'] = $this->data['PatientIpd']['witness_name'];
                    $data['PatientIpd']['authorized_name'] = $this->data['PatientIpd']['authorized_name'];
                    $data['PatientIpd']['authorized_telephone'] = $this->data['PatientIpd']['authorized_telephone'];
                    $data['PatientIpd']['authorized_address'] = $this->data['PatientIpd']['authorized_address'];
                    $data['PatientIpd']['authorized_id_card'] = $this->data['PatientIpd']['authorized_id_card'];
                    $data['PatientIpd']['authorized_issue_date'] = $this->data['PatientIpd']['authorized_issue_date'];
                    $data['PatientIpd']['authorized_expiration_date'] = $this->data['PatientIpd']['authorized_expiration_date'];
                    $data['PatientIpd']['authorized_issue_place'] = $this->data['PatientIpd']['authorized_issue_place'];
                    $data['PatientIpd']['ipd_code'] = $this->Helper->getAutoGeneratePatientIpdCode();
                    $data['PatientIpd']['created'] = date('Y-m-d H:i:s');
                    $data['PatientIpd']['created_by'] = $user['User']['id'];
                    if ($this->PatientIpd->save($data['PatientIpd'])) {
                        $patientIpdId = $this->PatientIpd->getLastInsertId();
                        $this->PatientStayInRoom->create();
                        $data['PatientStayInRoom']['patient_ipd_id'] = $patientIpdId;
                        $data['PatientStayInRoom']['room_id'] = $this->data['PatientIpd']['room_id'];
                        $this->PatientStayInRoom->save($data['PatientStayInRoom']);
                        echo $patientIpdId;
                        exit;
                    } else {
                        $result['msg'] = MESSAGE_DATA_COULD_NOT_BE_SAVED;
                        $result['error'] = 2;
                        echo json_encode($result);
                        exit;
                    }
                }
            } else {
                if ($this->Helper->checkDouplicate('patient_code', 'Patients', $this->data['Patient']['patient_code'])) {
                    $this->Session->setFlash(__('The patient code "' . $this->data['Patient']['patient_code'] . '" already exists in the system.', true), 'flash_failure');
                } else {
                    $this->Patient->create();
                    $user = $this->getCurrentUser();
                    if ($this->data['Patient']['nationality'] == "") {
                        $this->data['Patient']['nationality'] = 36;
                    }
                    $this->data['Patient']['patient_type_id'] = 1;
                    $this->data['Patient']['created'] = date('Y-m-d H:i:s');
                    $this->data['Patient']['created_by'] = $user['User']['id'];
                    $this->Patient->set('patient_code', $this->Helper->getAutoGeneratePatientCode());
                    if ($this->Patient->save($this->data)) {
                        $patientId = $this->Patient->getLastInsertId();
                        $this->PatientIpd->create();
                        $data['PatientIpd']['patient_id'] = $patientId;
                        $data['PatientIpd']['company_id'] = $this->data['PatientIpd']['company_id'];
                        $data['PatientIpd']['date_ipd'] = $this->data['PatientIpd']['date_ipd'];
                        $data['PatientIpd']['doctor_id'] = $this->data['PatientIpd']['doctor_id'];
                        $data['PatientIpd']['group_id'] = $this->data['PatientIpd']['department_id'];
                        $data['PatientIpd']['allergies'] = $this->data['PatientIpd']['allergies'];
                        $data['PatientIpd']['ipd_code'] = $this->Helper->getAutoGeneratePatientIpdCode();
                        $data['PatientIpd']['created'] = date('Y-m-d H:i:s');
                        $data['PatientIpd']['created_by'] = $user['User']['id'];
                        if ($this->PatientIpd->save($data['PatientIpd'])) {
                            $patientIpdId = $this->PatientIpd->getLastInsertId();
                            $this->PatientStayInRoom->create();
                            $data['PatientStayInRoom']['patient_ipd_id'] = $patientIpdId;
                            $data['PatientStayInRoom']['room_id'] = $this->data['PatientIpd']['room_id'];
                            $this->PatientStayInRoom->save($data['PatientStayInRoom']);
                            echo $patientIpdId;
                            exit;
                        }
                    } else {
                        $result['msg'] = MESSAGE_DATA_COULD_NOT_BE_SAVED;
                        $result['error'] = 2;
                        echo json_encode($result);
                        exit;
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
//        $doctors = ClassRegistry::init('DoctorConsultation')->find('all', 
//                array(
//                    'order' => array('DoctorConsultation.name ASC'),
//                    'conditions' => array('DoctorConsultation.is_active' => 1)
//                ));       
        $doctors = $this->User->find('all', array('conditions' => array('User.is_active' => 1, 'UserGroup.group_id' => array('2', '21')), 'order' => array('Employee.name ASC'), 'group' => 'User.id',
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
        $departments = $this->User->find('all', array('conditions' => array('User.is_active' => 1), 'group' => 'Group.id',
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

        $companies = ClassRegistry::init('Company')->find('list', array(
            'joins' => array(
                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                )
            ),
            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                )
        );

        $this->set(compact('rooms', 'sexes', 'patientGroups', 'nationalities', 'locations', 'code', 'doctors', 'departments', 'companies'));
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
        $this->loadModel('DoctorConsultation');
        $this->loadModel('PatientIpd');
        $user = $this->getCurrentUser();

        if (!empty($this->data)) {
            if ($this->data['Patient']['id'] != "") {
                $user = $this->getCurrentUser();
                if ($this->data['Patient']['nationality'] == "") {
                    $this->data['Patient']['nationality'] = 36;
                }
                $this->data['Patient']['modified'] = date('Y-m-d H:i:s');
                $this->data['Patient']['modified_by'] = $user['User']['id'];
                if ($this->Patient->save($this->data)) {
                    $patientId = $this->data['Patient']['id'];
                    $this->PatientIpd->create();
                    $data['PatientIpd']['id'] = $this->data['PatientIpd']['id'];
                    $data['PatientIpd']['patient_id'] = $patientId;
                    $data['PatientIpd']['company_id'] = $this->data['PatientIpd']['company_id'];
                    $data['PatientIpd']['date_ipd'] = $this->data['PatientIpd']['date_ipd'];
                    $data['PatientIpd']['doctor_id'] = $this->data['PatientIpd']['doctor_id'];
                    $data['PatientIpd']['group_id'] = $this->data['PatientIpd']['department_id'];
                    $data['PatientIpd']['allergies'] = $this->data['PatientIpd']['allergies'];
                    $data['PatientIpd']['witness_name'] = $this->data['PatientIpd']['witness_name'];
                    $data['PatientIpd']['authorized_name'] = $this->data['PatientIpd']['authorized_name'];
                    $data['PatientIpd']['authorized_telephone'] = $this->data['PatientIpd']['authorized_telephone'];
                    $data['PatientIpd']['authorized_address'] = $this->data['PatientIpd']['authorized_address'];
                    $data['PatientIpd']['authorized_id_card'] = $this->data['PatientIpd']['authorized_id_card'];
                    $data['PatientIpd']['authorized_issue_date'] = $this->data['PatientIpd']['authorized_issue_date'];
                    $data['PatientIpd']['authorized_expiration_date'] = $this->data['PatientIpd']['authorized_expiration_date'];
                    $data['PatientIpd']['authorized_issue_place'] = $this->data['PatientIpd']['authorized_issue_place'];
                    $data['PatientIpd']['modified'] = date('Y-m-d H:i:s');
                    $data['PatientIpd']['modified_by'] = $user['User']['id'];
                    if ($this->PatientIpd->save($data['PatientIpd'])) {
                        $patientIpdId = $this->data['PatientIpd']['id'];
                        $this->PatientStayInRoom->updateAll(
                                array('PatientStayInRoom.status' => "0"), array('PatientStayInRoom.patient_ipd_id' => $patientIpdId)
                        );
                        $this->PatientStayInRoom->create();
                        $data['PatientStayInRoom']['patient_ipd_id'] = $patientIpdId;
                        $data['PatientStayInRoom']['room_id'] = $this->data['PatientIpd']['room_id'];
                        $this->PatientStayInRoom->save($data['PatientStayInRoom']);
                        echo $patientIpdId;
                        exit;
                    }
                } else {
                    $result['msg'] = MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    $result['error'] = 2;
                    echo json_encode($result);
                    exit;
                }
            }
        } else {
            $this->data = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'PatientStayInRoom.status' => 1, 'PatientIpd.id' => $id),
                'fields' => array('Patient.*, PatientIpd.*, Nationality.name, PatientType.name, Company.id, Room.id'),
                'joins' => array(
                    array('table' => ' patient_ipds',
                        'alias' => 'PatientIpd',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Patient.id = PatientIpd.patient_id'
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
                            'Company.id = PatientIpd.company_id'
                        )
                    ),
                    array('table' => 'patient_stay_in_rooms',
                        'alias' => 'PatientStayInRoom',
                        'type' => 'INNER',
                        'conditions' => array(
                            'PatientIpd.id = PatientStayInRoom.patient_ipd_id'
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
//        $doctors =  ClassRegistry::init('DoctorConsultation')->find('all', 
//                    array(
//                        'order' => array('DoctorConsultation.name ASC'),
//                        'conditions' => array('DoctorConsultation.is_active' => 1)
//                    ));  
        $doctors = $this->User->find('all', array('conditions' => array('User.is_active' => 1, 'UserGroup.group_id' => array('2', '21')), 'order' => array('Employee.name ASC'), 'group' => 'User.id',
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

        $departments = $this->User->find('all', array('conditions' => array('User.is_active' => 1), 'group' => 'Group.id',
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

        $companies = ClassRegistry::init('Company')->find('list', array(
            'joins' => array(
                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                )
            ),
            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                )
        );

        $this->set(compact('rooms', 'sexes', 'patientGroups', 'nationalities', 'locations', 'code', 'doctors', 'departments', 'companies'));
    }

    function printPatientIpd($patientIpdId = null) {
        $this->layout = 'ajax';
        $this->loadModel('Patient');
        $this->loadModel('User');
        $this->loadModel('Group');
        $this->loadModel('Branch');
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'PatientConsultation.is_active' => 1, 'PatientStayInRoom.status > 0', 'PatientIpd.id' => $patientIpdId),
            'fields' => array('Patient.*, PatientIpd.*, Room.room_name, PatientLeave.*, PatientConsultation.*'),
            'joins' => array(
                array('table' => 'patient_ipds',
                    'alias' => 'PatientIpd',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Patient.id = PatientIpd.patient_id'
                    )
                ),
                array('table' => 'patient_leaves',
                    'alias' => 'PatientLeave',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientLeave.patient_ipd_id = PatientIpd.id'
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
                array('table' => 'patient_consultations',
                    'alias' => 'PatientConsultation',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientIpd.queued_doctor_id = PatientConsultation.queued_doctor_id'
                    )
                )
        )));
        $this->data = $this->Branch->read(null, 1);
        $this->set(compact('patient'));
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

        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'PatientIpd.id' => $id),
            'fields' => array('Patient.*, PatientIpd.*, Nationality.name, Company.name, Room.room_name, PatientLeave.*'),
            'joins' => array(
                array('table' => 'patient_ipds',
                    'alias' => 'PatientIpd',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Patient.id = PatientIpd.patient_id'
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
                ),
                array('table' => 'patient_leaves',
                    'alias' => 'PatientLeave',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientLeave.patient_ipd_id = PatientIpd.id'
                    )
                )
        )));
        $doctor = ClassRegistry::init('DoctorConsultation')->find('first', array(
            'fields' => array('DoctorConsultation.name'),
            'order' => array('DoctorConsultation.name ASC'),
            'conditions' => array('DoctorConsultation.is_active' => 1, 'DoctorConsultation.id' => $patient['PatientIpd']['doctor_id'])
        ));

        $department = ClassRegistry::init('Group')->find('first', array('conditions' => array('id' => $patient['PatientIpd']['group_id'])));

        $this->set(compact('patient', 'doctor', 'department'));
    }

    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__(MESSAGE_DATA_INVALID, true), 'flash_failure');
            $this->redirect(array('action' => 'index'));
        }
        $this->loadModel('PatientIpd');
        $user = $this->getCurrentUser();
        $dateTime = date("Y-m-d H:i:s");
        $this->PatientIpd->updateAll(
                array('PatientIpd.is_active' => "0", 'PatientIpd.modified_by' => $user['User']['id'], 'PatientIpd.modified' => "'$dateTime'"), array('PatientIpd.id' => $id)
        );
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }

    function medicalSurgery() {
        $this->layout = 'ajax';
    }

    function medicalSurgeryAjax() {
        $this->layout = 'ajax';
    }

    function addMedicalSurgery() {
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
        $result = array();
        if (!empty($this->data)) {
            if ($this->data['Patient']['id'] != "") {
                $user = $this->getCurrentUser();
                if ($this->data['Patient']['nationality'] == "") {
                    $this->data['Patient']['nationality'] = 36;
                }
                $this->data['Patient']['patient_type_id'] = 1;
                $this->data['Patient']['created'] = date('Y-m-d H:i:s');
                $this->data['Patient']['created_by'] = $user['User']['id'];
                $this->Patient->set('patient_code', $this->Helper->getAutoGeneratePatientCode());
                if ($this->Patient->save($this->data)) {
                    $patientId = $this->data['Patient']['id'];
                    $this->PatientIpd->create();
                    $data['PatientIpd']['patient_id'] = $patientId;
                    $data['PatientIpd']['company_id'] = $this->data['PatientIpd']['company_id'];
                    $data['PatientIpd']['date_ipd'] = $this->data['PatientIpd']['date_ipd'];
                    $data['PatientIpd']['doctor_id'] = $this->data['PatientIpd']['doctor_id'];
                    $data['PatientIpd']['group_id'] = $this->data['PatientIpd']['department_id'];
                    $data['PatientIpd']['allergies'] = $this->data['PatientIpd']['allergies'];
                    $data['PatientIpd']['witness_name'] = $this->data['PatientIpd']['witness_name'];
                    $data['PatientIpd']['authorized_name'] = $this->data['PatientIpd']['authorized_name'];
                    $data['PatientIpd']['authorized_telephone'] = $this->data['PatientIpd']['authorized_telephone'];
                    $data['PatientIpd']['authorized_address'] = $this->data['PatientIpd']['authorized_address'];
                    $data['PatientIpd']['authorized_id_card'] = $this->data['PatientIpd']['authorized_id_card'];
                    $data['PatientIpd']['authorized_issue_date'] = $this->data['PatientIpd']['authorized_issue_date'];
                    $data['PatientIpd']['authorized_expiration_date'] = $this->data['PatientIpd']['authorized_expiration_date'];
                    $data['PatientIpd']['authorized_issue_place'] = $this->data['PatientIpd']['authorized_issue_place'];
                    $data['PatientIpd']['doctor_explain_to_patient'] = $this->data['PatientIpd']['doctor_explain_to_patient'];
                    $data['PatientIpd']['patient_following_surgical'] = $this->data['PatientIpd']['patient_following_surgical'];
                    $data['PatientIpd']['according_to_patient'] = $this->data['PatientIpd']['according_to_patient'];
                    $data['PatientIpd']['according_number'] = $this->data['PatientIpd']['according_number'];
                    $data['PatientIpd']['ipd_code'] = $this->Helper->getAutoGeneratePatientIpdCode();
                    $data['PatientIpd']['ipd_type'] = 2;
                    $data['PatientIpd']['created'] = date('Y-m-d H:i:s');
                    $data['PatientIpd']['created_by'] = $user['User']['id'];
                    if ($this->PatientIpd->save($data['PatientIpd'])) {
                        $patientIpdId = $this->PatientIpd->getLastInsertId();
                        $this->PatientStayInRoom->create();
                        $data['PatientStayInRoom']['patient_ipd_id'] = $patientIpdId;
                        $data['PatientStayInRoom']['room_id'] = $this->data['PatientIpd']['room_id'];
                        $this->PatientStayInRoom->save($data['PatientStayInRoom']);
                        echo $patientIpdId;
                        exit;
                    } else {
                        $result['msg'] = MESSAGE_DATA_COULD_NOT_BE_SAVED;
                        $result['error'] = 2;
                        echo json_encode($result);
                        exit;
                    }
                }
            } else {
                if ($this->Helper->checkDouplicate('patient_code', 'Patients', $this->data['Patient']['patient_code'])) {
                    $result['msg'] = 'The patient code "' . $this->data['Patient']['patient_code'] . '" already exists in the system.';
                    $result['error'] = 2;
                    echo json_encode($result);
                    exit;
                } else {
                    $this->Patient->create();
                    $user = $this->getCurrentUser();
                    if ($this->data['Patient']['nationality'] == "") {
                        $this->data['Patient']['nationality'] = 36;
                    }
                    $this->data['Patient']['patient_type_id'] = 1;
                    $this->data['Patient']['created'] = date('Y-m-d H:i:s');
                    $this->data['Patient']['created_by'] = $user['User']['id'];
                    $this->Patient->set('patient_code', $this->Helper->getAutoGeneratePatientCode());
                    if ($this->Patient->save($this->data)) {
                        $patientId = $this->Patient->getLastInsertId();
                        $this->PatientIpd->create();
                        $data['PatientIpd']['patient_id'] = $patientId;
                        $data['PatientIpd']['company_id'] = $this->data['PatientIpd']['company_id'];
                        $data['PatientIpd']['date_ipd'] = $this->data['PatientIpd']['date_ipd'];
                        $data['PatientIpd']['doctor_id'] = $this->data['PatientIpd']['doctor_id'];
                        $data['PatientIpd']['group_id'] = $this->data['PatientIpd']['department_id'];
                        $data['PatientIpd']['allergies'] = $this->data['PatientIpd']['allergies'];
                        $data['PatientIpd']['ipd_code'] = $this->Helper->getAutoGeneratePatientIpdCode();
                        $data['PatientIpd']['doctor_explain_to_patient'] = $this->data['PatientIpd']['doctor_explain_to_patient'];
                        $data['PatientIpd']['patient_following_surgical'] = $this->data['PatientIpd']['patient_following_surgical'];
                        $data['PatientIpd']['according_to_patient'] = $this->data['PatientIpd']['according_to_patient'];
                        $data['PatientIpd']['according_number'] = $this->data['PatientIpd']['according_number'];
                        $data['PatientIpd']['ipd_type'] = 2;
                        $data['PatientIpd']['created'] = date('Y-m-d H:i:s');
                        $data['PatientIpd']['created_by'] = $user['User']['id'];
                        if ($this->PatientIpd->save($data['PatientIpd'])) {
                            $patientIpdId = $this->PatientIpd->getLastInsertId();
                            $this->PatientStayInRoom->create();
                            $data['PatientStayInRoom']['patient_ipd_id'] = $patientIpdId;
                            $data['PatientStayInRoom']['room_id'] = $this->data['PatientIpd']['room_id'];
                            $this->PatientStayInRoom->save($data['PatientStayInRoom']);
                            echo $patientIpdId;
                            exit;
                        } else {
                            $result['msg'] = MESSAGE_DATA_COULD_NOT_BE_SAVED;
                            $result['error'] = 2;
                            echo json_encode($result);
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
        $doctors = ClassRegistry::init('DoctorConsultation')->find('all', array(
            'order' => array('DoctorConsultation.name ASC'),
            'conditions' => array('DoctorConsultation.is_active' => 1)
        ));

        $departments = $this->User->find('all', array('conditions' => array('User.is_active' => 1), 'group' => 'Group.id',
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

        $companies = ClassRegistry::init('Company')->find('list', array(
            'joins' => array(
                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                )
            ),
            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                )
        );

        $this->set(compact('rooms', 'sexes', 'patientGroups', 'nationalities', 'locations', 'code', 'doctors', 'departments', 'companies'));
    }

    function editMedicalSurgery($id = null) {
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
            if ($this->data['Patient']['id'] != "") {
                $user = $this->getCurrentUser();
                if ($this->data['Patient']['nationality'] == "") {
                    $this->data['Patient']['nationality'] = 36;
                }
                $this->data['Patient']['modified'] = date('Y-m-d H:i:s');
                $this->data['Patient']['modified_by'] = $user['User']['id'];
                if ($this->Patient->save($this->data)) {
                    $patientId = $this->data['Patient']['id'];
                    $this->PatientIpd->create();
                    $data['PatientIpd']['id'] = $this->data['PatientIpd']['id'];
                    $data['PatientIpd']['patient_id'] = $patientId;
                    $data['PatientIpd']['company_id'] = $this->data['PatientIpd']['company_id'];
                    $data['PatientIpd']['date_ipd'] = $this->data['PatientIpd']['date_ipd'];
                    $data['PatientIpd']['doctor_id'] = $this->data['PatientIpd']['doctor_id'];
                    $data['PatientIpd']['group_id'] = $this->data['PatientIpd']['department_id'];
                    $data['PatientIpd']['allergies'] = $this->data['PatientIpd']['allergies'];
                    $data['PatientIpd']['witness_name'] = $this->data['PatientIpd']['witness_name'];
                    $data['PatientIpd']['authorized_name'] = $this->data['PatientIpd']['authorized_name'];
                    $data['PatientIpd']['authorized_telephone'] = $this->data['PatientIpd']['authorized_telephone'];
                    $data['PatientIpd']['authorized_address'] = $this->data['PatientIpd']['authorized_address'];
                    $data['PatientIpd']['authorized_id_card'] = $this->data['PatientIpd']['authorized_id_card'];
                    $data['PatientIpd']['authorized_issue_date'] = $this->data['PatientIpd']['authorized_issue_date'];
                    $data['PatientIpd']['authorized_expiration_date'] = $this->data['PatientIpd']['authorized_expiration_date'];
                    $data['PatientIpd']['authorized_issue_place'] = $this->data['PatientIpd']['authorized_issue_place'];
                    $data['PatientIpd']['ipd_code'] = $this->Helper->getAutoGeneratePatientIpdCode();
                    $data['PatientIpd']['doctor_explain_to_patient'] = $this->data['PatientIpd']['doctor_explain_to_patient'];
                    $data['PatientIpd']['patient_following_surgical'] = $this->data['PatientIpd']['patient_following_surgical'];
                    $data['PatientIpd']['according_to_patient'] = $this->data['PatientIpd']['according_to_patient'];
                    $data['PatientIpd']['according_number'] = $this->data['PatientIpd']['according_number'];
                    $data['PatientIpd']['ipd_type'] = 2;
                    $data['PatientIpd']['modified'] = date('Y-m-d H:i:s');
                    $data['PatientIpd']['modified_by'] = $user['User']['id'];
                    if ($this->PatientIpd->save($data['PatientIpd'])) {
                        $patientIpdId = $this->data['PatientIpd']['id'];
                        $this->PatientStayInRoom->updateAll(
                                array('PatientStayInRoom.status' => "0"), array('PatientStayInRoom.patient_ipd_id' => $patientIpdId)
                        );
                        $this->PatientStayInRoom->create();
                        $data['PatientStayInRoom']['patient_ipd_id'] = $patientIpdId;
                        $data['PatientStayInRoom']['room_id'] = $this->data['PatientIpd']['room_id'];
                        $this->PatientStayInRoom->save($data['PatientStayInRoom']);
                        echo $patientIpdId;
                        exit;
                    } else {
                        $result['msg'] = MESSAGE_DATA_COULD_NOT_BE_SAVED;
                        $result['error'] = 2;
                        echo json_encode($result);
                        exit;
                    }
                }
            }
        } else {
            $this->data = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'PatientStayInRoom.status' => 1, 'PatientIpd.id' => $id),
                'fields' => array('Patient.*, PatientIpd.*, Nationality.name, PatientType.name, Company.id, Room.id'),
                'joins' => array(
                    array('table' => ' patient_ipds',
                        'alias' => 'PatientIpd',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Patient.id = PatientIpd.patient_id'
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
                            'Company.id = PatientIpd.company_id'
                        )
                    ),
                    array('table' => 'patient_stay_in_rooms',
                        'alias' => 'PatientStayInRoom',
                        'type' => 'INNER',
                        'conditions' => array(
                            'PatientIpd.id = PatientStayInRoom.patient_ipd_id'
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
        $doctors = $this->User->find('all', array('conditions' => array('User.is_active' => 1, 'UserGroup.group_id' => 3), 'group' => 'User.id',
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

        $departments = $this->User->find('all', array('conditions' => array('User.is_active' => 1), 'group' => 'Group.id',
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

        $companies = ClassRegistry::init('Company')->find('list', array(
            'joins' => array(
                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                )
            ),
            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                )
        );

        $this->set(compact('rooms', 'sexes', 'patientGroups', 'nationalities', 'locations', 'code', 'doctors', 'departments', 'companies'));
    }

    function printPatientMedicalSurgery($patientIpdId = null) {
        $this->layout = 'ajax';
        $this->loadModel('Patient');
        $this->loadModel('User');
        $this->loadModel('Group');
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'PatientStayInRoom.status' => 1, 'PatientIpd.id' => $patientIpdId),
            'fields' => array('Patient.*, PatientIpd.*, Room.room_name'),
            'joins' => array(
                array('table' => 'patient_ipds',
                    'alias' => 'PatientIpd',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Patient.id = PatientIpd.patient_id'
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
                )
        )));

        $doctor = $this->User->find('first', array('conditions' => array('User.is_active' => 1, 'User.id' => $patient['PatientIpd']['doctor_id']),
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

    function viewMedicalSurgery($id = null) {
        $this->layout = 'ajax';
        $this->loadModel('Patient');
        $this->loadModel('User');
        $this->loadModel('Group');
        if (!$id) {
            $this->Session->setFlash(__('Invalid patient', true), 'flash_failure');
            $this->redirect(array('action' => 'quotation'));
        }

        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'PatientIpd.id' => $id),
            'fields' => array('Patient.*, PatientIpd.*, Nationality.name, Company.name, Room.room_name'),
            'joins' => array(
                array('table' => 'patient_ipds',
                    'alias' => 'PatientIpd',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Patient.id = PatientIpd.patient_id'
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
        $doctor = $this->User->find('first', array('conditions' => array('User.is_active' => 1, 'User.id' => $patient['PatientIpd']['doctor_id']),
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

    function deleteMedicalSurgery($id = null) {
        if (!$id) {
            $this->Session->setFlash(__(MESSAGE_DATA_INVALID, true), 'flash_failure');
            $this->redirect(array('action' => 'medicalSurgery'));
        }
        $this->loadModel('PatientIpd');
        $user = $this->getCurrentUser();
        $dateTime = date("Y-m-d H:i:s");
        $this->PatientIpd->updateAll(
                array('PatientIpd.is_active' => "0", 'PatientIpd.modified_by' => $user['User']['id'], 'PatientIpd.modified' => "'$dateTime'"), array('PatientIpd.id' => $id)
        );
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }

    function patientLeave($id = null) {
        $this->layout = 'ajax';

        if (!empty($this->data)) {

            $this->loadModel('PatientIpd');
            $this->loadModel('PatientStayInRoom');
            $this->loadModel('PatientLeave');
            $this->loadModel('Appointment');
            $user = $this->getCurrentUser();
            $this->PatientIpd->updateAll(
                    array('PatientIpd.is_active' => "2", 'PatientIpd.modified' => '"' . date('Y-m-d H:i:s') . '"', 'PatientIpd.modified_by' => $user['User']['id']), array('id' => $this->data['PatientIpd']['id'])
            );
            $this->PatientStayInRoom->updateAll(
                    array('PatientStayInRoom.status' => "2"), array('PatientStayInRoom.patient_ipd_id' => $this->data['PatientIpd']['id'])
            );
            $this->data['PatientLeave']['patient_ipd_id'] = $this->data['PatientIpd']['id'];
            $this->data['PatientLeave']['patient_id'] = $this->data['PatientIpd']['patient_id'];
            $this->data['PatientLeave']['end_date'] = $this->data['PatientIpd']['end_date'];
            // $this->data['PatientLeave']['type_leave'] = $this->data['PatientIpd']['type_leave'];
            $this->data['PatientLeave']['diagnotist_after'] = $this->data['PatientIpd']['diagnotist_after'];
            $this->data['PatientLeave']['note'] = $this->data['PatientIpd']['note'];
        //add new
            $this->data['PatientLeave']['doctor_nme'] = $this->data['PatientIpd']['doctor_nme'];
        //
            $this->data['PatientLeave']['created_by'] = $user['User']['id'];
            if ($this->PatientLeave->save($this->data)) {

                /**
                 * Create new appointment
                 */
                if ($this->data['PatientIpd']['app_date'] != "") {
                    $this->Appointment->create();
                    $appointment['Appointment']['patient_id'] = $this->data['PatientIpd']['patient_id'];
                    $appointment['Appointment']['doctor_id'] = $user['User']['id'];
                    $appointment['Appointment']['app_date'] = $this->data['PatientIpd']['app_date'];
                    $appointment['Appointment']['description'] = $this->data['PatientIpd']['description'];
                    $appointment['Appointment']['created_by'] = $user['User']['id'];
                    $appointment['Appointment']['is_close'] = 0;
                    $this->Appointment->save($appointment);
                }

                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                exit;
            }
        } else {
            $this->loadModel('Patient');
            $this->data = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'PatientIpdRoom.status' => 1, 'PatientIpd.id' => $id),
                'fields' => array('Patient.*, PatientIpd.*,PatientIpdRoom.*, Room.id'),
                'joins' => array(
                    array('table' => ' patient_ipds',
                        'alias' => 'PatientIpd',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Patient.id = PatientIpd.patient_id'
                        )
                    ),
                    array('table' => 'patient_stay_in_rooms',
                        'alias' => 'PatientIpdRoom',
                        'type' => 'INNER',
                        'conditions' => array(
                            'PatientIpd.id = PatientIpdRoom.patient_ipd_id'
                        )
                    ),
                    array('table' => 'rooms',
                        'alias' => 'Room',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Room.id = PatientIpdRoom.room_id'
                        )
                    )
            )));
        }
        if (empty($this->data)) {
            $this->data = $this->PatientIpd->read(null, $id);
        }
        $rooms = ClassRegistry::init('Room')->find('all', array('conditions' => 'Room.is_active=1'));
        $this->set(compact('rooms'));
    }

    function tabConsultNum($queueDoctorId = null, $queueId = null, $patientId = null) {
        $this->layout = 'ajax';
        $this->loadModel('PatientVitalSignBloodPressure');
        $this->loadModel('PatientVitalSign');
        $this->loadModel('PatientConsultation');
        $this->loadModel('QueuedDoctor');
        $this->loadModel('PatientIpd');
        $this->loadModel('PatientStayInRoom');
        $this->loadModel('Patient');
        $this->loadModel('Queue');
        $this->loadModel('QueuedLabo');
        $this->loadModel('Labo');
        if (!empty($queueId)) {
            $consultation = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'PatientConsultation.is_active' => 1, 'QeuedDoctor.status >=1', 'QeuedDoctor.id' => $queueDoctorId),
                'fields' => array('Queue.id, QeuedDoctor.id, PatientVitalSign.*, PatientVitalSignBloodPressure.*, PatientConsultation.*, QeuedLabo.id, DoctorConsultation.* , GenitoUrinarySystem.*'),
                'joins' => array(
                    array('table' => 'queues',
                        'alias' => 'Queue',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Queue.patient_id  = Patient.id'
                        )
                    ),
                    array('table' => 'queued_doctors',
                        'alias' => 'QeuedDoctor',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'QeuedDoctor.queue_id = Queue.id'
                        )
                    ),
                    array('table' => 'queued_labos',
                        'alias' => 'QeuedLabo',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'QeuedLabo.queue_id = Queue.id', 'QeuedLabo.status=2'
                        )
                    ),
                    array('table' => 'patient_consultations',
                        'alias' => 'PatientConsultation',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'PatientConsultation.queued_doctor_id = QeuedDoctor.id'
                        )
                    ),
                    array('table' => 'patient_vital_signs',
                        'alias' => 'PatientVitalSign',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'PatientVitalSign.queued_doctor_id = QeuedDoctor.id'
                        )
                    ),
                    array('table' => 'patient_vital_sign_blood_pressures',
                        'alias' => 'PatientVitalSignBloodPressure',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'PatientVitalSignBloodPressure.patient_vital_sign_id = PatientVitalSign.id'
                        )
                    ),
                    array('table' => 'doctor_consultations',
                        'alias' => 'DoctorConsultation',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'DoctorConsultation.id = PatientConsultation.doctor_consultation_ids'
                        )
                    ),
                    array('table' => 'genito_urinary_systems',
                        'alias' => 'GenitoUrinarySystem',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'GenitoUrinarySystem.queued_doctor_id = QeuedDoctor.id AND GenitoUrinarySystem.status = 1'
                        )
                    ),
                ),
                'order' => 'PatientConsultation.consultation_code DESC, PatientConsultation.created DESC',
                'group' => 'PatientConsultation.queued_doctor_id'
            ));

            $patientIpd = $this->PatientIpd->find('first', array('conditions' => array('PatientStayInRoom.status' => 1, 'PatientIpd.is_active >= 1', 'PatientIpd.queued_doctor_id' => $queueDoctorId),
                'fields' => array('PatientIpd.*, Room.id'),
                'joins' => array(
                    array('table' => 'patient_stay_in_rooms',
                        'alias' => 'PatientStayInRoom',
                        'type' => 'INNER',
                        'conditions' => array(
                            'PatientIpd.id = PatientStayInRoom.patient_ipd_id'
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
            $rooms = ClassRegistry::init('Room')->find('all', array('conditions' => 'Room.is_active=1', 'order' => 'room_name'));
//            $labos = $this->QueuedLabo->find('all', array('conditions' => array('Labo.status >=' => 1, 'LaboRequest.is_active =' => 1, 'QueuedLabo.queue_id' => $queueId),
//                'fields' => array('LaboItemGroup.id, LaboItemGroup.name, Labo.doctor_id', 'QueuedLabo.doctor_id'),
//                'joins' => array(
//                    array('table' => 'labos',
//                        'alias' => 'Labo',
//                        'type' => 'LEFT',
//                        'conditions' => array(
//                            'Labo.queued_id = QueuedLabo.id'
//                        )
//                    ),
//                    array('table' => 'labo_requests',
//                        'alias' => 'LaboRequest',
//                        'type' => 'LEFT',
//                        'conditions' => array(
//                            'Labo.id = LaboRequest.labo_id'
//                        )
//                    ),
//                    array('table' => 'labo_item_groups',
//                        'alias' => 'LaboItemGroup',
//                        'type' => 'LEFT',
//                        'conditions' => array(
//                            'LaboItemGroup.id = LaboRequest.labo_item_group_id'
//                        )
//                    )
//            )));
            $this->set(compact('consultation', 'patientId', 'patientIpd', 'rooms'));
        }
    }

    function followup($followUpId = null, $type = null) {
        $this->layout = 'ajax';
        $this->loadModel('PatientFollowup');
        $this->loadModel('PatientConsultation');
        $dataFollowup = array();
        $dataCosultation = array();
        if (!empty($type)) {
            if ($type == "followup") {
                $dataFollowup = ClassRegistry::init('PatientFollowup')->find('first', array('conditions' => array('id' => $followUpId)));
            } else {
                $dataCosultation = ClassRegistry::init('PatientConsultation')->find('first', array('conditions' => array('id' => $followUpId)));
            }
        }
        $this->set(compact('dataFollowup', 'dataCosultation'));
    }

    /**
     * patientConsultationId : Patient Consultation ID
     * queueId  : Queue ID
     */
    function addNewFollowUp($patientConsultationId = null, $queueDoctorId = null, $queueId = null) {
        $this->loadModel('PatientFollowup');
        $this->loadModel('PatientConsultation');
        $user = $this->getCurrentUser();
        if (!empty($this->data['PatientFollowup']['id'])) {
            $this->data['PatientFollowup']['modified_by'] = $user['User']['id'];
            if ($this->PatientFollowup->save($this->data['PatientFollowup'])) {
                echo '1';
                exit();
            } else {
                echo '0';
                exit();
            }
        } else if (!empty($this->data['PatientConsultation']['id'])) {
            $this->data['PatientConsultation']['follow_up'] = $this->data['PatientFollowup']['followup'];
            $this->data['PatientConsultation']['modified_by'] = $user['User']['id'];
            if ($this->PatientConsultation->save($this->data['PatientConsultation'])) {
                echo '1';
                exit();
            } else {
                echo '0';
                exit();
            }
        } else {
            $this->data['PatientFollowup']['queue_id'] = $queueId;
            $this->data['PatientFollowup']['queued_doctor_id'] = $queueDoctorId;
            $this->data['PatientFollowup']['patient_consultation_id'] = $patientConsultationId;
            $this->data['PatientFollowup']['created_by'] = $user['User']['id'];
            if ($this->PatientFollowup->save($this->data['PatientFollowup'])) {
                echo '1';
                exit();
            } else {
                echo '0';
                exit();
            }
        }
    }

    function getFollowUp($patientConsultationId = null, $queueDoctorId = null) {
        $this->layout = 'ajax';
        $this->set(compact('patientConsultationId'));
    }

    function doctorComment($followUpId = null, $type = null) {
        $this->layout = 'ajax';
        $this->loadModel('DoctorComment');
        $this->loadModel('PatientConsultation');
        $dataDoctorComment = array();
        $dataCosultation = array();
        if (!empty($type)) {
            if ($type == "doctor_comment") {
                $dataDoctorComment = ClassRegistry::init('DoctorComment')->find('first', array('conditions' => array('id' => $followUpId)));
            } else {
                $dataCosultation = ClassRegistry::init('PatientConsultation')->find('first', array('conditions' => array('id' => $followUpId)));
            }
        }
        $this->set(compact('dataDoctorComment', 'dataCosultation'));
    }

    /**
     * patientConsultationId : Patient Consultation ID
     * queueId  : Queue ID
     */
    function addNewDoctorComment($patientConsultationId = null, $queueDoctorId = null, $queueId = null) {
        $this->loadModel('DoctorComment');
        $this->loadModel('PatientConsultation');
        $user = $this->getCurrentUser();
        if (!empty($this->data['DoctorComment']['id'])) {
            $this->data['DoctorComment']['modified_by'] = $user['User']['id'];
            if ($this->DoctorComment->save($this->data['DoctorComment'])) {
                echo '1';
                exit();
            } else {
                echo '0';
                exit();
            }
        } else if (!empty($this->data['PatientConsultation']['id'])) {
            $this->data['PatientConsultation']['doctor_comment'] = $this->data['DoctorComment']['doctor_comment'];
            $this->data['PatientConsultation']['modified_by'] = $user['User']['id'];
            if ($this->PatientConsultation->save($this->data['PatientConsultation'])) {
                echo '1';
                exit();
            } else {
                echo '0';
                exit();
            }
        } else {
            $this->data['DoctorComment']['queue_id'] = $queueId;
            $this->data['DoctorComment']['queued_doctor_id'] = $queueDoctorId;
            $this->data['DoctorComment']['patient_consultation_id'] = $patientConsultationId;
            $this->data['DoctorComment']['created_by'] = $user['User']['id'];
            if ($this->DoctorComment->save($this->data['DoctorComment'])) {
                echo '1';
                exit();
            } else {
                echo '0';
                exit();
            }
        }
    }

    function getDoctorComment($patientConsultationId = null, $queueDoctorId = null) {
        $this->layout = 'ajax';
        $this->set(compact('patientConsultationId'));
    }

    function doctorDaignostic($followUpId = null, $type = null) {
        $this->layout = 'ajax';
        $this->loadModel('DoctorDaignostic');
        $this->loadModel('PatientConsultation');
        $dataDoctorDaignostic = array();
        $dataCosultation = array();
        if (!empty($type)) {
            if ($type == "doctor_daignostic") {
                $dataDoctorDaignostic = ClassRegistry::init('DoctorDaignostic')->find('first', array('conditions' => array('id' => $followUpId)));
            } else {
                $dataCosultation = ClassRegistry::init('PatientConsultation')->find('first', array('conditions' => array('id' => $followUpId)));
            }
        }
        $this->set(compact('dataDoctorDaignostic', 'dataCosultation'));
    }

    function chiefComplain($followUpId = null, $type = null) {
        $this->layout = 'ajax';
        $this->loadModel('DoctorChiefComplain');
        $this->loadModel('PatientConsultation');
        $dataDoctorChiefComplain = array();
        $dataCosultation = array();
        if (!empty($type)) {
            if ($type == "chief_complain") {
                $dataDoctorChiefComplain = ClassRegistry::init('DoctorChiefComplain')->find('first', array('conditions' => array('id' => $followUpId)));
            } else {
                $dataCosultation = ClassRegistry::init('PatientConsultation')->find('first', array('conditions' => array('id' => $followUpId)));
            }
        }
        $this->set(compact('dataDoctorChiefComplain', 'dataCosultation'));
    }

    function addNewChiefComplain($patientConsultationId = null, $queueDoctorId = null, $queueId = null) {
        $this->loadModel('DoctorChiefComplain');
        $user = $this->getCurrentUser();
        if (!empty($this->data['DoctorChiefComplain']['id'])) {
            $this->data['DoctorChiefComplain']['modified_by'] = $user['User']['id'];
            $this->DoctorChiefComplain->save($this->data['DoctorChiefComplain']);
            exit;
        } else if (!empty($this->data['PatientConsultation']['id'])) {
            $this->loadModel('PatientConsultation');
            $this->data['PatientConsultation']['chief_complain'] = $this->data['DoctorChiefComplain']['chief_complain'];
            $this->data['PatientConsultation']['modified_by'] = $user['User']['id'];
            $this->PatientConsultation->save($this->data['PatientConsultation']);
            exit;
        } else {
            $this->data['DoctorChiefComplain']['queued_id'] = $queueId;
            $this->data['DoctorChiefComplain']['queued_doctor_id'] = $queueDoctorId;
            $this->data['DoctorChiefComplain']['patient_consultation_id'] = $patientConsultationId;
            $this->data['DoctorChiefComplain']['created_by'] = $user['User']['id'];
            $this->DoctorChiefComplain->save($this->data['DoctorChiefComplain']);
            exit;
        }
    }

    function getChiefComplain($patientConsultationId = null, $queueDoctorId = null) {
        $this->layout = 'ajax';
        $this->set(compact('patientConsultationId'));
    }

    function medicalHistory($followUpId = null, $type = null) {
        $this->layout = 'ajax';
        $this->loadModel('DoctorMedicalHistorie');
        $this->loadModel('PatientConsultation');
        $dataDoctorMedicalHistorie = array();
        $dataConsultation = array();
        if (!empty($type)) {
            if ($type == "medical_history") {
                $dataDoctorMedicalHistorie = ClassRegistry::init('DoctorMedicalHistorie')->find('first', array('conditions' => array('id' => $followUpId)));
            } else {
                $dataConsultation = ClassRegistry::init('PatientConsultation')->find('first', array('conditions' => array('id' => $followUpId)));
            }
        }
        $this->set(compact('dataDoctorMedicalHistorie', 'dataConsultation'));
    }

    function addNewMedicalHistory($patientConsultationId = null, $queueDoctorId = null, $queueId = null) {
        $this->loadModel('PatientConsultation');
        $this->loadModel('DoctorMedicalHistorie');
        $user = $this->getCurrentUser();
        if (!empty($this->data['DoctorMedicalHistorie']['id'])) {
            $this->data['DoctorMedicalHistorie']['modified_by'] = $user['User']['id'];
            if ($this->DoctorMedicalHistorie->save($this->data['DoctorMedicalHistorie'])) {
                echo '1';
                exit();
            } else {
                echo '0';
                exit();
            }
        } else if (!empty($this->data['PatientConsultation']['id'])) {
            $this->data['PatientConsultation']['medical_history'] = $this->data['DoctorMedicalHistorie']['medical_history'];
            $this->data['PatientConsultation']['modified_by'] = $user['User']['id'];
            if ($this->PatientConsultation->save($this->data['PatientConsultation'])) {
                echo '1';
                exit();
            } else {
                echo '0';
                exit();
            }
        } else {
            $this->data['DoctorMedicalHistorie']['queued_id'] = $queueId;
            $this->data['DoctorMedicalHistorie']['queued_doctor_id'] = $queueDoctorId;
            $this->data['DoctorMedicalHistorie']['patient_consultation_id'] = $patientConsultationId;
            $this->data['DoctorMedicalHistorie']['created_by'] = $user['User']['id'];
            if ($this->DoctorMedicalHistorie->save($this->data['DoctorMedicalHistorie'])) {
                echo '1';
                exit();
            } else {
                echo '0';
                exit();
            }
        }
    }

    function getMedicalHistory($patientConsultationId = null, $queueDoctorId = null) {
        $this->layout = 'ajax';
        $this->set(compact('patientConsultationId'));
    }

    /**
     * patientConsultationId : Patient Consultation ID
     * queueId  : Queue ID
     */
    function addNewDoctorDaignostic($patientConsultationId = null, $queueDoctorId = null, $queueId = null) {
        $this->loadModel('DoctorDaignostic');
        $this->loadModel('PatientConsultation');
        $user = $this->getCurrentUser();
        if (!empty($this->data['DoctorDaignostic']['id'])) {
            $this->data['DoctorDaignostic']['modified_by'] = $user['User']['id'];
            if ($this->DoctorDaignostic->save($this->data['DoctorDaignostic'])) {
                echo '1';
                exit();
            } else {
                echo '0';
                exit();
            }
        } else if (!empty($this->data['PatientConsultation']['id'])) {
            $this->data['PatientConsultation']['doctor_daignostic'] = $this->data['DoctorDaignostic']['doctor_daignostic'];
            $this->data['PatientConsultation']['modified_by'] = $user['User']['id'];
            if ($this->PatientConsultation->save($this->data['PatientConsultation'])) {
                echo '1';
                exit();
            } else {
                echo '0';
                exit();
            }
        } else {
            $this->data['DoctorDaignostic']['queue_id'] = $queueId;
            $this->data['DoctorDaignostic']['queued_doctor_id'] = $queueDoctorId;
            $this->data['DoctorDaignostic']['patient_consultation_id'] = $patientConsultationId;
            $this->data['DoctorDaignostic']['created_by'] = $user['User']['id'];
            if ($this->DoctorDaignostic->save($this->data['DoctorDaignostic'])) {
                echo '1';
                exit();
            } else {
                echo '0';
                exit();
            }
        }
    }

    function getDoctorDaignostic($patientConsultationId = null, $queueDoctorId = null) {
        $this->layout = 'ajax';
        $this->set(compact('patientConsultationId'));
    }

    function doctorChiefComplain($followUpId = null, $type = null) {
        $this->layout = 'ajax';
        $this->loadModel('PatientConsultation');
        $dataCosultation = array();
        $dataCosultation = ClassRegistry::init('PatientConsultation')->find('first', array('conditions' => array('id' => $followUpId)));

        $this->set(compact('dataCosultation'));
    }

    function tabPrescription($queueDoctorId = null, $queueId = null, $patientIpdId = null) {
        $this->layout = 'ajax';
        $this->loadModel('Order');
        $user = $this->getCurrentUser();
        $orders = "";
        if (!empty($this->data)) {
            if (preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/', $this->data['Doctor']['order_date'])) {
                $this->data['Doctor']['order_date'] = $this->Helper->dateConvert($this->data['Doctor']['order_date']);
            }
            if (!isset($this->data['Doctor']['order_date']) || is_null($this->data['Doctor']['order_date']) || $this->data['Doctor']['order_date'] == '0000-00-00' || $this->data['Doctor']['order_date'] == '' || !preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $this->data['Doctor']['order_date'])) {
                $result['code'] = 2;
                echo json_encode($result);
                exit;
            }
            if ($this->Helper->checkDouplicate('order_code', 'orders', $this->data['Doctor']['order_code'], 'status > 0')) {
                $invalid['code'] = '1';
                echo json_encode($invalid);
                exit;
            } else {
                $result = array();
                // Load Table
                $this->loadModel('OrderDetail');
                $this->loadModel('OrderMisc');
                $this->loadModel('Appointment');
                //  Table Begin
                $this->Order->begin();
                $this->OrderDetail->begin();
                $this->OrderMisc->begin();
                // Order                
                $this->Order->create();
                $order = array();
                $order['Order']['company_id'] = $this->data['Doctor']['company_id'];
                $order['Order']['branch_id'] = $this->data['Order']['branch_id'];
                $order['Order']['currency_center_id'] = ((empty($this->data['Order']['currency_center_id'])) ? '1' : $this->data['Order']['currency_center_id']);
                $order['Order']['patient_id'] = $this->data['Doctor']['patient_id'];
                $order['Order']['queue_doctor_id'] = $this->data['Doctor']['queue_doctor_id'];
                $order['Order']['queue_id'] = $this->data['Doctor']['queue_id'];
                $order['Order']['order_code'] = $this->data['Doctor']['order_code'];
                $order['Order']['order_date'] = $this->data['Doctor']['order_date'];
                $order['Order']['prescription_type'] = $this->data['Doctor']['prescription_type'];
                $order['Order']['created_by'] = $user['User']['id'];
                $order['Order']['status'] = 1;
                if ($this->Order->save($order)) {
                    $result['id'] = $orderId = $this->Order->id;
                    // get latest order code 
                    $soCode = $this->Helper->getAutoGenerateOrderCode();
                    mysql_query("UPDATE orders SET order_code = '" . $soCode . "' WHERE id = " . $orderId);

                    if ($this->data['Doctor']['appointment_date'] != "" || $this->data['Doctor']['appointment_date'] != "0000-00-00 00:00:00") {
                        $this->Appointment->create();
                        $appointment['Appointment']['patient_id'] = $this->data['Doctor']['patient_id'];
                        $appointment['Appointment']['doctor_id'] = $user['User']['id'];
                        $appointment['Appointment']['order_id'] = $orderId;
                        $appointment['Appointment']['app_date'] = ((empty($this->data['Doctor']['appointment_date'])) ? '0000-00-00' : $this->data['Doctor']['appointment_date']);
                        $appointment['Appointment']['description'] = $this->data['Doctor']['description'];
                        $appointment['Appointment']['created_by'] = $user['User']['id'];
                        $this->Appointment->save($appointment);
                    }

                    for ($i = 0; $i < sizeof($_POST['product']); $i++) {
                        if (!empty($_POST['product_id'][$i])) {
                            $orderDetail = array();
                            // Order Detail
                            $this->OrderDetail->create();
                            $orderDetail['OrderDetail']['order_id'] = $orderId;
                            $orderDetail['OrderDetail']['product_id'] = $_POST['product_id'][$i];
                            $orderDetail['OrderDetail']['qty'] = $_POST['qty'][$i];
                            $orderDetail['OrderDetail']['unit_cost'] = $_POST['unit_cost'][$i];
                            $orderDetail['OrderDetail']['unit_price'] = $_POST['unit_price'][$i];
                            $orderDetail['OrderDetail']['qty_uom_id'] = $_POST['qty_uom_id'][$i];
                            $orderDetail['OrderDetail']['conversion'] = $_POST['conversion'][$i];
                            $orderDetail['OrderDetail']['num_days'] = $_POST['num_days'][$i];
                            $orderDetail['OrderDetail']['morning'] = $_POST['morning'][$i];
                            $orderDetail['OrderDetail']['afternoon'] = $_POST['afternoon'][$i];
                            $orderDetail['OrderDetail']['evening'] = $_POST['evening'][$i];
                            $orderDetail['OrderDetail']['night'] = $_POST['night'][$i];
                            $orderDetail['OrderDetail']['note'] = $_POST['note'][$i];
                            $orderDetail['OrderDetail']['morning_use_id'] = ((empty($_POST['morning_use_id'][$i])) ? 0 : $_POST['morning_use_id'][$i]);
                            $orderDetail['OrderDetail']['afternoon_use_id'] = ((empty($_POST['afternoon_use_id'][$i])) ? 0 : $_POST['afternoon_use_id'][$i]);
                            $orderDetail['OrderDetail']['evening_use_id'] = ((empty($_POST['evening_use_id'][$i])) ? 0 : $_POST['evening_use_id'][$i]);
                            $orderDetail['OrderDetail']['night_use_id'] = ((empty($_POST['night_use_id'][$i])) ? 0 : $_POST['night_use_id'][$i]);
                            $this->OrderDetail->save($orderDetail);
                        } else {
                            $orderMisc = array();
                            // Quotation Detail
                            $this->OrderMisc->create();
                            $orderMisc['OrderMisc']['order_id'] = $orderId;
                            $orderMisc['OrderMisc']['description'] = $_POST['product'][$i];
                            $orderMisc['OrderMisc']['qty'] = $_POST['qty'][$i];
                            $orderMisc['OrderMisc']['qty_uom_id'] = $_POST['qty_uom_id'][$i];
                            $orderMisc['OrderMisc']['conversion'] = $_POST['conversion'][$i];
                            $orderMisc['OrderMisc']['num_days'] = $_POST['num_days'][$i];
                            $orderMisc['OrderMisc']['morning'] = $_POST['morning'][$i];
                            $orderMisc['OrderMisc']['afternoon'] = $_POST['afternoon'][$i];
                            $orderMisc['OrderMisc']['evening'] = $_POST['evening'][$i];
                            $orderMisc['OrderMisc']['night'] = $_POST['night'][$i];
                            $orderMisc['OrderMisc']['note'] = $_POST['note'][$i];
                            $orderMisc['OrderMisc']['morning_use_id'] = ((empty($_POST['morning_use_id'][$i])) ? 0 : $_POST['morning_use_id'][$i]);
                            $orderMisc['OrderMisc']['afternoon_use_id'] = ((empty($_POST['afternoon_use_id'][$i])) ? 0 : $_POST['afternoon_use_id'][$i]);
                            $orderMisc['OrderMisc']['evening_use_id'] = ((empty($_POST['evening_use_id'][$i])) ? 0 : $_POST['evening_use_id'][$i]);
                            $orderMisc['OrderMisc']['night_use_id'] = ((empty($_POST['night_use_id'][$i])) ? 0 : $_POST['night_use_id'][$i]);
                            $this->OrderMisc->save($orderMisc);
                        }
                    }
                }
                if (!empty($result['id'])) {
                    $this->Order->commit();
                    $this->OrderDetail->commit();
                    $this->OrderMisc->commit();
                } else {
                    $this->Order->rollback();
                    $this->OrderDetail->rollback();
                    $this->OrderMisc->rollback();
                }
                echo json_encode($result);
                exit;
            }
        }

        $companies = ClassRegistry::init('Company')->find('list', array(
            'joins' => array(
                array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id')
                )
            ),
            'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                )
        );

        $branches = ClassRegistry::init('Branch')->find('all', array(
            'joins' => array(
                array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id')),
                array('table' => 'module_code_branches AS ModuleCodeBranch', 'type' => 'left', 'conditions' => array('ModuleCodeBranch.branch_id=Branch.id'))
            ),
            'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id', 'ModuleCodeBranch.so_code', 'Branch.currency_center_id', 'CurrencyCenter.symbol'),
            'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])
        ));

        $code = date('y') . 'SO';

        // Get Loction Setting
        $locSetting = ClassRegistry::init('LocationSetting')->findById(4);
        $locCon = '';
        if ($locSetting['LocationSetting']['location_status'] == 1) {
            $locCon = 'locations.is_for_sale = 1';
        }
        $joinUsers = array('table' => 'user_location_groups', 'type' => 'INNER', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'));
        $joinLocation = array('table' => 'locations', 'type' => 'INNER', 'conditions' => array('locations.location_group_id=LocationGroup.id', $locCon));
        $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('fields' => array('LocationGroup.id', 'LocationGroup.name'), 'joins' => array($joinUsers, $joinLocation), 'conditions' => array('user_location_groups.user_id=' . $user['User']['id'], 'LocationGroup.is_active' => '1', 'LocationGroup.location_group_type_id != 1'), 'group' => 'LocationGroup.id'));
        $patientLeave = $this->PatientLeave->find('first', array('conditions' => array('PatientLeave.status' => 1, 'PatientLeave.patient_ipd_id' => $patientIpdId)));

        $this->set(compact("code", "companies", "queueDoctorId", "queueId", "orders", 'branches', 'locationGroups', 'patientLeave'));
    }

    function orderDetails() {
        $this->layout = 'ajax';
        $this->loadModel('TreatmentUse');
        $uoms = ClassRegistry::init('Uom')->find('all', array('fields' => array('Uom.id', 'Uom.name'), 'conditions' => array('Uom.is_active' => 1)));
        $treatmentUses = ClassRegistry::init('TreatmentUse')->find("all", array('conditions' => array('TreatmentUse.is_active' => 1)));
        $this->set(compact('uoms', "treatmentUses"));
    }

    function tabLabo($queueDoctorId = null, $queueId = null, $patientIpdId = null) {
        $this->layout = 'ajax';
        $this->loadModel('LaboRequest');
        $this->loadModel('LaboItemGroup');
        $this->loadModel('LaboTitleGroup');
        $this->loadModel('Labo');
        $this->loadModel('QueuedLabo');
        $this->loadModel('PatientConsultation');
        $this->loadModel('Queue');
        $this->loadModel('PatientLeave');
        $patientConsultations = $this->PatientConsultation->find('first', array('conditions' => array('PatientConsultation.is_active' => 1, 'PatientConsultation.queued_doctor_id' => $queueDoctorId)));
        $queueStatus = $this->Queue->find('first', array('conditions' => array('Queue.id' => $queueId)));
        $laboItemGroups = $this->LaboItemGroup->find('all', array('fields' => array('LaboItemGroup.id', 'LaboItemGroup.name'), 'conditions' => array('LaboItemGroup.is_active != 2')));
        $laboTitleGroup = $this->LaboTitleGroup->find("all", array("conditions" => array("LaboTitleGroup.is_active!=2")));
        $queueLaboResult = $this->QueuedLabo->find('first', array('conditions' => array('QueuedLabo.queue_id' => $queueId), 'order' => 'QueuedLabo.id DESC'));
        $labo = $this->Labo->getLaboByQueuedLaboId($queueLaboResult['QueuedLabo']['id']);
        if ($labo['Labo']['status'] == 2) {
            $labo = $this->Labo->getLaboByQueuedLaboId(0);
        }
        $laboSelected = $this->LaboRequest->find('list', array('conditions' => array('LaboRequest.labo_id' => $labo['Labo']['id'], 'LaboRequest.is_active != 2'), 'fields' => array('LaboRequest.id', 'LaboRequest.labo_item_group_id')));
        $patientLeave = $this->PatientLeave->find('first', array('conditions' => array('PatientLeave.status' => 1, 'PatientLeave.patient_ipd_id' => $patientIpdId)));
        $this->set(compact('patientConsultations', 'laboItemGroups', 'queueId', 'laboTitleGroup', 'labo', 'laboSelected', 'queueStatus', 'queueDoctorId', 'patientLeave', 'patientIpdId'));
    }

    function laboRequestSave($queueId = null) {
        if (!empty($this->data)) {
            $this->loadModel("LaboRequest");
            $this->loadModel("LaboItemGroup");
            $this->loadModel("Labo");
            $this->loadModel('QueuedLabo');
            $this->loadModel('PatientConsultation');
            $laboItemGroupSave = array();
            $user = $this->getCurrentUser();
            $userId = $user['User']['id'];
            $queueId = $this->data['Queue']['id'];
            $laboId = $this->data['Labo']['id'];
            $queueDoctorId = $this->data['QueueDoctor']['queued_doctor_id'];
            $diagnostic = $this->data['Labo']['daignostic'];
            $chiefComplain = $this->data['Labo']['chief_complain'];
            $dateTime = date("Y-m-d H:i:s");
            $laboStatus = "";
            $condtionLabo = "";
            $this->QueuedLabo->updateAll(
                    array('QueuedLabo.status' => "0"), array('QueuedLabo.queue_id' => $queueId)
            );
            $queuedLabo['QueuedLabo']['queue_id'] = $queueId;
            $queuedLabo['QueuedLabo']['doctor_id'] = $userId;
            $queuedLabo['QueuedLabo']['created_by'] = $userId;
            $queuedLabo['QueuedLabo']['status'] = 2;
            if ($this->QueuedLabo->save($queuedLabo)) {
                $queuedLaboId = $this->QueuedLabo->getLastInsertId();
                if ($laboId != "") {
                    $labo['Labo']['id'] = $laboId;
                    $queryLabo = mysql_query("SELECT status FROM labos WHERE id = " . $laboId);
                    while ($rowLabo = mysql_fetch_array($queryLabo)) {
                        if ($rowLabo['status'] == 1) {
                            $condtionLabo = " AND labo_id = {$laboId} ";
                        }
                        $laboStatus = $rowLabo['status'];
                    }
                }
                $labo['Labo']['queued_id'] = $queuedLaboId;
                $labo['Labo']['diagonist'] = $diagnostic;
                $labo['Labo']['chief_complain'] = $chiefComplain;
                $labo['Labo']['created_by'] = $userId;
                $labo['Labo']['status'] = 1;
                if (!empty($this->data['Labo']['labo_item_group_id'])) {
                    $laboItemGroupSave = $this->data['Labo']['labo_item_group_id'];
                }
                // for update diagnostic and chief complain auto to patient consultation
                $updatePatientConsult = mysql_query("UPDATE `patient_consultations` SET `daignostic` = '" . $diagnostic . "', `chief_complain` = '" . $chiefComplain . "', `modified` = '" . $dateTime . "', `modified_by` = " . $user['User']['id'] . "  WHERE  `queued_doctor_id`=" . $queueDoctorId . ";");
                // close 

                $this->LaboRequest->updateAll(
                        array('LaboRequest.is_active' => "2"), array('LaboRequest.labo_id' => $laboId)
                );
                if ($this->Labo->save($labo['Labo'])) {
                    if ($laboId == "") {
                        $laboId = $this->Labo->getLastInsertId();
                    }
                    // check labo item before save new record
                    $this->loadModel('Patient');
                    $this->loadModel("PatientIpdServiceDetail");

                    if ($laboStatus == 1) {
                        $this->PatientIpdServiceDetail->updateAll(
                                array('PatientIpdServiceDetail.is_active' => "0", 'PatientIpdServiceDetail.modified' => "'$dateTime'", 'PatientIpdServiceDetail.modified_by' => $user['User']['id']), array('PatientIpdServiceDetail.patient_ipd_id' => $this->data['PatientIpd']['id'], 'PatientIpdServiceDetail.is_active = 1 ' . $condtionLabo, 'PatientIpdServiceDetail.type' => 2)
                        );
                    }

//                    $this->PatientIpdServiceDetail->updateAll(                   
//                        array('PatientIpdServiceDetail.is_active' => "0", 'PatientIpdServiceDetail.modified' =>  "'$dateTime'", 'PatientIpdServiceDetail.modified_by' => $user['User']['id']), array('PatientIpdServiceDetail.patient_ipd_id' => $this->data['PatientIpd']['id'], 'PatientIpdServiceDetail.is_active' => 1, 'PatientIpdServiceDetail.type' => 2)
//                    );

                    $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'Patient.id' => $this->data['Patient']['id'])));
                    foreach ($this->data['LaboItemGroup'] as $laboItemGoup) {
                        $this->LaboRequest->create();
                        $laboItemIds = $this->LaboItemGroup->find("first", array("conditions" => array("LaboItemGroup.id" => $laboItemGoup), "fields" => array("LaboItemGroup.labo_item_id")));
                        $laboItemIds = explode(",", $laboItemIds['LaboItemGroup']['labo_item_id']);
                        $laboRequest['LaboRequest']['labo_id'] = $this->Labo->id;
                        $laboRequest['LaboRequest']['labo_item_group_id'] = $laboItemGoup;
                        $laboRequest['LaboRequest']['request'] = @serialize($laboItemIds);
                        $laboRequest['LaboRequest']['created_by'] = $userId;
                        $this->LaboRequest->save($laboRequest);
                        // insert labo itmes service
                        // check labo price 
                        // patient have insurance company
                        $unitPrice = 0;
                        if ($patient['Patient']['patient_bill_type_id'] == 3 && $patient['Patient']['company_insurance_id'] != 0) {
                            $queryLaboPatientPrices = mysql_query("SELECT LaboItemPriceInsurance.id, unit_price FROM labo_item_price_insurances AS LaboItemPrice LEFT JOIN labo_item_price_insurance_patient_group_details AS LaboItemPriceInsurance ON LaboItemPrice.id = LaboItemPriceInsurance.labo_item_price_insurance_id "
                                    . " WHERE LaboItemPrice.labo_item_group_id = '" . $laboItemGoup . "' AND LaboItemPrice.company_insurance_id ='" . $patient['Patient']['company_insurance_id'] . "' AND patient_group_id = '" . $patient['Patient']['patient_group_id'] . "' AND LaboItemPriceInsurance.is_active = 1 AND LaboItemPrice.is_active = 1");
                            while ($resultLaboPatientPrice = mysql_fetch_array($queryLaboPatientPrices)) {
                                $unitPrice = $resultLaboPatientPrice['unit_price'];
                            }
                        } else {
                            $queryLaboPatientPrices = mysql_query("SELECT id, unit_price, hospital_price FROM labo_item_patient_groups "
                                    . " WHERE labo_item_group_id = '" . $laboItemGoup . "' AND patient_group_id = '" . $patient['Patient']['patient_group_id'] . "' AND is_active = 1");
                            while ($resultLaboPatientPrice = mysql_fetch_array($queryLaboPatientPrices)) {
                                $unitPrice = $resultLaboPatientPrice['unit_price'];
                            }
                        }
                        $this->PatientIpdServiceDetail->create();
                        $PatientIpdServiceDetail['PatientIpdServiceDetail']['patient_ipd_id'] = $this->data['PatientIpd']['id'];
                        $PatientIpdServiceDetail['PatientIpdServiceDetail']['labo_id'] = $laboId;
                        $PatientIpdServiceDetail['PatientIpdServiceDetail']['exchange_rate_id'] = $this->data['Patient']['exchange_rate_id'];
                        $PatientIpdServiceDetail['PatientIpdServiceDetail']['service_id'] = $laboItemGoup;
                        $PatientIpdServiceDetail['PatientIpdServiceDetail']['doctor_id'] = $userId;
                        $PatientIpdServiceDetail['PatientIpdServiceDetail']['type'] = 2;
                        $PatientIpdServiceDetail['PatientIpdServiceDetail']['qty'] = 1;
                        $PatientIpdServiceDetail['PatientIpdServiceDetail']['date_created'] = $dateTime;
                        $PatientIpdServiceDetail['PatientIpdServiceDetail']['unit_price'] = $unitPrice;
                        $PatientIpdServiceDetail['PatientIpdServiceDetail']['total_price'] = $unitPrice;
                        $PatientIpdServiceDetail['PatientIpdServiceDetail']['created_by'] = $user['User']['id'];
                        $this->PatientIpdServiceDetail->save($PatientIpdServiceDetail['PatientIpdServiceDetail']);
                    }
                }
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                exit;
            }
        }
        exit();
    }

    function tabOtherService($queueDoctorId = null, $queueId = null, $patientIpdId = null) {
        $this->layout = 'ajax';
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('EchoServiceRequest');
        $this->loadModel('XrayServiceRequest');
        $this->loadModel('MidWifeServiceRequest');
        $this->loadModel('CystoscopyServiceRequest');
        $this->loadModel('QueuedDoctor');
        $this->loadModel('Patient');
        if (!empty($this->data)) {
            $queueDoctorId = $this->data['QeuedDoctor']['id'];
            $user = $this->getCurrentUser();
            $this->OtherServiceRequest->create();
            $this->data['OtherServiceRequest']['queued_doctor_id'] = $this->data['QeuedDoctor']['id'];
            $this->data['OtherServiceRequest']['created_by'] = $user['User']['id'];
            if ($this->OtherServiceRequest->save($this->data['OtherServiceRequest'])) {
                $otherServiceId = $this->OtherServiceRequest->getLastInsertId();
                if (!empty($this->data['Doctor']['echo_description'])) {
                    $this->EchoServiceRequest->create();
                    $this->data['EchoServiceRequest']['other_service_request_id'] = $otherServiceId;
                    $this->data['EchoServiceRequest']['echo_description'] = $this->data['Doctor']['echo_description'];
                    $this->data['EchoServiceRequest']['created_by'] = $user['User']['id'];
                    $this->EchoServiceRequest->save($this->data['EchoServiceRequest']);
                }
                if (!empty($this->data['Doctor']['xray_description'])) {
                    $this->XrayServiceRequest->create();
                    $this->data['XrayServiceRequest']['other_service_request_id'] = $otherServiceId;
                    $this->data['XrayServiceRequest']['xray_description'] = $this->data['Doctor']['xray_description'];
                    $this->data['XrayServiceRequest']['created_by'] = $user['User']['id'];
                    $this->XrayServiceRequest->save($this->data['XrayServiceRequest']);
                }
                if (!empty($this->data['Doctor']['cystoscopy_description'])) {
                    $this->CystoscopyServiceRequest->create();
                    $this->data['CystoscopyServiceRequest']['other_service_request_id'] = $otherServiceId;
                    $this->data['CystoscopyServiceRequest']['cystoscopy_description'] = $this->data['Doctor']['cystoscopy_description'];
                    $this->data['CystoscopyServiceRequest']['created_by'] = $user['User']['id'];
                    $this->CystoscopyServiceRequest->save($this->data['CystoscopyServiceRequest']);
                }
                if (!empty($this->data['Doctor']['mid_wife_description'])) {
                    $this->MidWifeServiceRequest->create();
                    $this->data['MidWifeServiceRequest']['other_service_request_id'] = $otherServiceId;
                    $this->data['MidWifeServiceRequest']['mid_wife_description'] = $this->data['Doctor']['mid_wife_description'];
                    $this->data['MidWifeServiceRequest']['created_by'] = $user['User']['id'];
                    $this->MidWifeServiceRequest->save($this->data['MidWifeServiceRequest']);
                }
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                exit;
            }
        }

        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'QeuedDoctor.status' => 1, 'Queue.id' => $queueId, 'QeuedDoctor.id' => $queueDoctorId),
            'fields' => array('Queue.id, QeuedDoctor.id, EchoServiceRequest.*'),
            'joins' => array(
                array('table' => 'queues',
                    'alias' => 'Queue',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Queue.patient_id  = Patient.id'
                    )
                ),
                array('table' => 'queued_doctors',
                    'alias' => 'QeuedDoctor',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'QeuedDoctor.queue_id = Queue.id'
                    )
                ),
                array('table' => 'other_service_requests',
                    'alias' => 'OtherServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'OtherServiceRequest.queued_doctor_id = QeuedDoctor.id'
                    )
                ),
                array('table' => 'echo_service_requests',
                    'alias' => 'EchoServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'EchoServiceRequest.other_service_request_id = OtherServiceRequest.id'
                    )
                ),
                array('table' => 'xray_service_requests',
                    'alias' => 'XrayServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'XrayServiceRequest.other_service_request_id = OtherServiceRequest.id'
                    )
                ),
                array('table' => 'cystoscopy_service_requests',
                    'alias' => 'CystoscopyServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'CystoscopyServiceRequest.other_service_request_id = OtherServiceRequest.id'
                    )
                ),
                array('table' => 'mid_wife_service_requests',
                    'alias' => 'MidWifeServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'MidWifeServiceRequest.other_service_request_id = OtherServiceRequest.id'
                    )
                )
        )));
        $this->loadModel('PatientLeave');
        $patientLeave = $this->PatientLeave->find('first', array('conditions' => array('PatientLeave.status' => 1, 'PatientLeave.patient_ipd_id' => $patientIpdId)));
        $this->set(compact('patient', 'patientLeave'));
        $this->set('queueDoctorId', $queueDoctorId);
        $this->set('queueId', $queueId);
    }

    function vitalSign($patientIPDId = null, $patientIpdVitalSignId = null) {
        $this->layout = 'ajax';
        $this->loadModel('PatientIpdVitalSign');
        if (!empty($patientIpdVitalSignId)) {
            $this->data = ClassRegistry::init('PatientIpdVitalSign')->find('first', array('conditions' => array('is_active' => 1, 'id' => $patientIpdVitalSignId)));
        }
        $this->set(compact('patientIPDId'));
    }

    function addVitalSign() {
        $this->loadModel('PatientIpdVitalSign');
        $user = $this->getCurrentUser();
        if (!empty($this->data['PatientIpdVitalSign']['id'])) {
            $this->data['PatientIpdVitalSign']['modified_by'] = $user['User']['id'];
            $this->PatientIpdVitalSign->save($this->data['PatientIpdVitalSign']);
            exit;
        } else {
            $this->PatientIpdVitalSign->create();
            $this->data['PatientIpdVitalSign']['created_by'] = $user['User']['id'];
            $this->PatientIpdVitalSign->save($this->data['PatientIpdVitalSign']);
            exit;
        }
    }

    function getVitalSign($patientIPDId = null) {
        $this->layout = 'ajax';
        $this->set(compact('patientIPDId'));
    }
    function printPatientLeave($patientIpdId = null) {
        $this->layout = 'ajax';
        $this->loadModel('Patient');
        $this->loadModel('User');
        $this->loadModel('Group');
        $this->loadModel('Branch');
        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'PatientConsultation.is_active' => 1, 'PatientStayInRoom.status > 0', 'PatientIpd.id' => $patientIpdId),
            'fields' => array('Patient.*, PatientIpd.*, Room.room_name, PatientLeave.*, PatientConsultation.*'),
            'joins' => array(
                array('table' => 'patient_ipds',
                    'alias' => 'PatientIpd',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Patient.id = PatientIpd.patient_id'
                    )
                ),
                array('table' => 'patient_leaves',
                    'alias' => 'PatientLeave',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientLeave.patient_ipd_id = PatientIpd.id'
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
                array('table' => 'patient_consultations',
                    'alias' => 'PatientConsultation',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PatientIpd.queued_doctor_id = PatientConsultation.queued_doctor_id'
                    )
                )
        )));
        $this->data = $this->Branch->read(null, 1);
        $this->set(compact('patient'));
    }

    function tabLaboResult($patientIPDId = null, $patientId = null) {
        $this->layout = 'ajax';
        $this->loadModel('PatientLeave');
        $patientLeave = $this->PatientLeave->find('first', array('conditions' => array('PatientLeave.status' => 1, 'PatientLeave.patient_ipd_id' => $patientIPDId)));
        $this->set(compact('patientLeave'));
    }

    function tabDailyClinical($queueDoctorId = null, $queueId = null, $patientId = null) {
        $this->layout = 'ajax';
        $this->loadModel('PatientConsultation');
        $this->loadModel('QueuedDoctor');
        $this->loadModel('Patient');
        $this->loadModel('Queue');
   
        if (!empty($queueId)) {
            $consultation = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'PatientConsultation.is_active' => 1, 'QeuedDoctor.status >=1', 'QeuedDoctor.id' => $queueDoctorId),
                'fields' => array('Queue.id, QeuedDoctor.id, PatientConsultation.*'),
                'joins' => array(
                    array('table' => 'queues',
                        'alias' => 'Queue',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Queue.patient_id  = Patient.id'
                        )
                    ),
                    array('table' => 'queued_doctors',
                        'alias' => 'QeuedDoctor',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'QeuedDoctor.queue_id = Queue.id'
                        )
                    ),
                    array('table' => 'patient_consultations',
                        'alias' => 'PatientConsultation',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'PatientConsultation.queued_doctor_id = QeuedDoctor.id'
                        )
                    ),
                ),
                'order' => 'PatientConsultation.consultation_code DESC, PatientConsultation.created DESC',
                'group' => 'PatientConsultation.queued_doctor_id'
            ));
            $this->set(compact('consultation', 'patientId'));
        }
    }

    function getExtension($str)
    {
        $i = strrpos($str,".");
        if (!$i) { return ""; }
        $l = strlen($str) - $i;
        $ext = substr($str,$i+1,$l);
        return $ext;
    }

    function tabAttachFile($patientId = null, $viewId = 5) {
        $this->layout = 'ajax';
        $this->loadModel('PatientDocument'); 
        if(!empty($_FILES['photos']['name']) && $patientId != ""){
            $user = $this->getCurrentUser();
            $this->PatientDocument->create();
            $valid_formats = array("jpg", "png", "gif", "bmp","jpeg", "pdf");
            $uploaddir = "public/patient_document/"; //a directory inside
            for($k=0; $k<sizeof($_FILES['photos']['name']); $k++){
                $filename = stripslashes($_FILES['photos']['name'][$k]);
                if($filename!=""){
                    $size=filesize($_FILES['photos']['tmp_name'][$k]);
                    //get the extension of the file in a lower case format
                    $ext = $this->getExtension($filename);
                    $ext = strtolower($ext);
                    if(in_array($ext,$valid_formats)){
                        if ($size < (9000*1024)){
                            $image_name=time().$filename;
                            $newname=$uploaddir.$image_name;
                            if(move_uploaded_file($_FILES['photos']['tmp_name'][$k], $newname)){
                                $time=time();
                                $image['PatientDocument']['src_name'] =$image_name;
                            }else{
                                 echo '<span class="imgList">You have exceeded the size limit! so moving unsuccessful! </span>';
                            }
                        }
                     }
                    if(!empty($_POST['photo_old'][$k]) && $_POST['photo_old'][$k]!=''){
                        $image['PatientDocument']['src_name'] = $_POST['photo_old'][$k];
                    }
                    $image['PatientDocument']['extension']=$ext;
                    $image['PatientDocument']['patient_id']=$patientId;
                    $image['PatientDocument']['created_by']=$user['User']['id'];
                    $this->PatientDocument->saveAll($image);
                }
            }
            exit;
        }
        $this->set(compact('patientId', 'viewId'));
    }

    function deleteImageAttachFile($id = null, $name = null) {
        $this->loadModel('PatientDocument');
        $user = $this->getCurrentUser();
        $date = date("Y-m-d H:i:s");
        $this->PatientDocument->updateAll(
            array('PatientDocument.is_active' => '2', 'PatientDocument.modified' => "'$date'", 'PatientDocument.modified_by' => $user['User']['id']),
            array('PatientDocument.id' => $id)
        );
        echo MESSAGE_DATA_HAS_BEEN_DELETED;      
        exit; 
    }

}

?>