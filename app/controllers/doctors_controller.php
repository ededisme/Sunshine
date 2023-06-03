<?php

class DoctorsController extends AppController {

    var $uses = array('Patient', 'Queue');
    var $components = array('Helper', 'LaboProcess');

    function dashboard() {
        
    }

    function dashboardPatientQueueAjax($date = null) {
        $this->layout = 'ajax';
        $this->set(compact('date'));
    }

    function dashboardPatientFollowupAjax() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $this->set('userId', $user['User']['id']);
    }

    function cancelAppointment($appointmentId = null) {
        $user = $this->getCurrentUser();

        $this->loadModel('Appointment');
        $Appointment['Appointment']['id'] = $appointmentId;
        $Appointment['Appointment']['is_close'] = 1;
        $Appointment['Appointment']['modified_by'] = $user['User']['id'];
        $this->Appointment->save($Appointment);

        $this->redirect(array('action' => 'dashboard'));
    }

    function patient() {
        $this->layout = 'ajax';
    }

    function patientAjax() {
        $this->layout = 'ajax';
    }

    function view($queueDoctorId = null, $queues = null, $patientId = null) {
        $this->layout = 'ajax';
        if (!$patientId && empty($this->data)) {
            $this->Session->setFlash(__('Invalid patient', true), 'flash_failure');
            $this->redirect(array('action' => 'queue'));
        }
        if (empty($this->data)) {
            $this->set('patient', $this->Patient->read(null, $patientId));
        }
    }

    function newConsultation($qpid = null) {
        $this->layout = 'ajax';
        $this->loadModel("QueuedLabo");
        $patient = $this->QueuedLabo->findById($qpid);
        if (empty($patient)) {
            $this->Session->setFlash(__(MESSAGE_SELECT_A_PATIENT, true), flash_success);
            $this->redirect($this->referer());
        } else {
            $this->set('patient', $patient);
        }
        $isConsult = ClassRegistry::init('Consultation')->findByQueuedLaboId($qpid);
        $isTreatment = ClassRegistry::init('Treatment')->findByQueuedLaboId($qpid);
        $isProtocol = ClassRegistry::init('Protocol')->findByQueuedLaboId($qpid);
        $isPara = ClassRegistry::init('ExamParaclinicType')->findByQueuedLaboId($qpid);
        $isLabo = ClassRegistry::init('Labo')->findByQueuedLaboId($qpid);
        $this->set(compact('isConsult', 'isTreatment', 'isProtocol', 'isPara', 'isLabo'));
    }

    function consultation($queueDoctorId = null, $queueId = null) {
        $this->layout = 'ajax';
        if (!$queueId && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        if (empty($this->data)) {
            ClassRegistry::init('Queue')->id = $queueId;
            $this->set('patient', $this->Patient->read(null, ClassRegistry::init('Queue')->field('patient_id')));
        }
    }

    function consultationNurse($queueDoctorId = null, $queueId = null) {
        $this->layout = 'ajax';
        if (!$queueId && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        if (empty($this->data)) {
            ClassRegistry::init('Queue')->id = $queueId;
            $this->set('patient', $this->Patient->read(null, ClassRegistry::init('Queue')->field('patient_id')));
        }
    }

    function tabConsult($queueDoctorId = null, $queueId = null) {
        $this->layout = 'ajax';
        $this->loadModel('PatientVitalSignBloodPressure');
        $this->loadModel('PatientVitalSign');
        $this->loadModel('PatientConsultation');
        $this->loadModel('QueuedDoctor');
        $this->loadModel('Examination');
        $this->loadModel('ChiefComplain');
        $this->loadModel('MedicalHistory');
        $this->loadModel('DoctorComment');
        $this->loadModel('Diagnostic');
        $this->loadModel('DailyClinicalReport');
        $this->loadModel('DoctorChiefComplain');
        $this->loadModel('GenitoUrinarySystem');
        $this->loadModel('TobaccoAlcohol');
        $this->loadModel('Appointment');
        $this->loadModel('PatientIpd');
        $this->loadModel('PatientStayInRoom');
        $this->loadModel('PatientIpdServiceDetail');

        $isDone = $this->PatientConsultation->find('list', array('conditions' => array('queued_doctor_id' => $queueDoctorId)));
        if (!empty($isDone)) {
            echo GENERAL_DONE;
            exit();
        }

        if (!empty($this->data)) {
            /**
             * include customize function
             */
            include('includes/function.php');
            $queueDoctorId = $this->data['QeuedDoctor']['id'];
            $user = $this->getCurrentUser();

            $this->PatientConsultation->create();
            $this->data['PatientConsultation']['consultation_code'] = $this->Helper->getAutoGenerateConsultationCode();
            $this->data['PatientConsultation']['queued_doctor_id'] = $this->data['QeuedDoctor']['id'];
            $this->data['PatientConsultation']['physical_examination_id'] = $this->data['PatientConsultation']['examination'];
            $this->data['PatientConsultation']['daignostic_id'] = $this->data['PatientConsultation']['patient_diagnostic'];
            $this->data['PatientConsultation']['created_by'] = $user['User']['id'];
            if ($this->PatientConsultation->save($this->data['PatientConsultation'])) {
                /**
                 * Add Doctor Chief Complain
                 */
                for ($i = 0; $i < sizeof($this->data['PatientConsultation']['complain']); $i++) {
                    if (isset($this->data['PatientConsultation']['complain'][$i])) {
                        if ($this->data['PatientConsultation']['complain'][$i] != "") {
                            $this->DoctorChiefComplain->create();
                            $data['DoctorChiefComplain']['queued_id'] = $this->data['Queue']['id'];
                            $data['DoctorChiefComplain']['queued_doctor_id'] = $this->data['QeuedDoctor']['id'];
                            $data['DoctorChiefComplain']['chief_complain_id'] = $this->data['PatientConsultation']['complain'][$i];
                            $data['DoctorChiefComplain']['chief_complain_id'] = $this->data['PatientConsultation']['complain'][$i];
                            $data['DoctorChiefComplain']['chief_complain_id'] = $this->data['PatientConsultation']['complain'][$i];
                            $data['DoctorChiefComplain']['created_by'] = $user['User']['id'];
                            $data['DoctorChiefComplain']['status'] = 1;
                            $this->DoctorChiefComplain->save($data['DoctorChiefComplain']);
                        }
                    }
                }
                /**
                 * Add Illness with Tobacco and Alcohol
                 */
                for ($i = 0; $i < sizeof($this->data['PatientConsultation']['tob_alch']); $i++) {
                    if (isset($this->data['PatientConsultation']['tob_alch'][$i])) {
                        if ($this->data['PatientConsultation']['tob_alch'][$i] != "") {
                            $this->TobaccoAlcohol->create();
                            $data['TobaccoAlcohol']['queued_id'] = $this->data['Queue']['id'];
                            $data['TobaccoAlcohol']['queued_doctor_id'] = $this->data['QeuedDoctor']['id'];
                            $data['TobaccoAlcohol']['tob_achol'] = $this->data['PatientConsultation']['tob_alch'][$i];
                            $data['TobaccoAlcohol']['created_by'] = $user['User']['id'];
                            $data['TobaccoAlcohol']['status'] = 1;
                            $this->TobaccoAlcohol->save($data['TobaccoAlcohol']);
                        }
                    }
                }

                /**
                 * Add Geneto Urinary System
                 */
                if (isset($this->data['PatientConsultation']['size'][0]) || isset($this->data['PatientConsultation']['surface'][0]) || isset($this->data['PatientConsultation']['consistency'][0]) || isset($this->data['PatientConsultation']['median_sulcus'][0]) || isset($this->data['PatientConsultation']['pain'][0]) || isset($this->data['PatientConsultation']['no_dule'][0]) || $this->data['PatientConsultation']['other'] != '') {
                    $this->GenitoUrinarySystem->create();
                    $data['GenitoUrinarySystem']['queued_id'] = $this->data['Queue']['id'];
                    $data['GenitoUrinarySystem']['queued_doctor_id'] = $this->data['QeuedDoctor']['id'];
                    $data['GenitoUrinarySystem']['size'] = isset($this->data['PatientConsultation']['size'][0]) ? $this->data['PatientConsultation']['size'][0] : null;
                    $data['GenitoUrinarySystem']['surface'] = isset($this->data['PatientConsultation']['surface'][0]) ? $this->data['PatientConsultation']['surface'][0] : null;
                    $data['GenitoUrinarySystem']['consistency'] = isset($this->data['PatientConsultation']['consistency'][0]) ? $this->data['PatientConsultation']['consistency'][0] : null;
                    $data['GenitoUrinarySystem']['median_sulcus'] = isset($this->data['PatientConsultation']['median_sulcus'][0]) ? $this->data['PatientConsultation']['median_sulcus'][0] : null;
                    $data['GenitoUrinarySystem']['pain'] = isset($this->data['PatientConsultation']['pain'][0]) ? $this->data['PatientConsultation']['pain'][0] : null;
                    $data['GenitoUrinarySystem']['no_dule'] = isset($this->data['PatientConsultation']['no_dule'][0]) ? $this->data['PatientConsultation']['no_dule'][0] : null;
                    $data['GenitoUrinarySystem']['other'] = $this->data['PatientConsultation']['other'] != '' ? $this->data['PatientConsultation']['other'] : null;
                    $data['GenitoUrinarySystem']['created_by'] = $user['User']['id'];
                    $data['GenitoUrinarySystem']['status'] = 1;
                    $this->GenitoUrinarySystem->save($data['GenitoUrinarySystem']);
                }

                // update/insert patient vital sign
                $this->PatientVitalSign->create();
                $this->data['PatientVitalSign']['queued_doctor_id'] = $queueDoctorId;
                $this->data['PatientVitalSign']['created_by'] = $user['User']['id'];
                if ($this->PatientVitalSign->save($this->data['PatientVitalSign'])) {
                    if ($this->data['PatientVitalSign']['id'] == "") {
                        $patientVitalSignId = $this->PatientVitalSign->getLastInsertId();
                    } else {
                        $patientVitalSignId = $this->data['PatientVitalSign']['id'];
                    }
                    // start insert blood pressure
                    $this->PatientVitalSignBloodPressure->create();
                    $this->data['PatientVitalSignBloodPressure']['patient_vital_sign_id'] = $patientVitalSignId;
                    $this->PatientVitalSignBloodPressure->save($this->data['PatientVitalSignBloodPressure']);
                    // close insert blood pressure
                }
                // close update/insert patient vital sign

                /**
                 * Create new appointment
                 */
                if ($this->data['PatientConsultation']['app_date'] != "") {
                    $this->Appointment->create();
                    $appointment['Appointment']['patient_id'] = $_POST['patient_id'];
                    $appointment['Appointment']['doctor_id'] = $user['User']['id'];
                    $appointment['Appointment']['queue_id'] = $this->data['Queue']['id'];
                    $appointment['Appointment']['queue_doctor_id'] = $queueDoctorId;
                    $appointment['Appointment']['app_date'] = $this->data['PatientConsultation']['app_date'];
                    $appointment['Appointment']['description'] = $this->data['PatientConsultation']['description'];
                    $appointment['Appointment']['created_by'] = $user['User']['id'];
                    $appointment['Appointment']['is_close'] = 0;
                    $this->Appointment->save($appointment);
                }

                /**
                 * Update Queue
                 */
                $Queue['Queue']['id'] = $this->data['Queue']['id'];
                $Queue['Queue']['status'] = 2; // ready to pay
                $Queue['Queue']['modified_by'] = $user['User']['id'];
                $this->Queue->save($Queue);

                /**
                 * Update Queue Doctor
                 */
                $QueueDoctor['QueuedDoctor']['id'] = $queueDoctorId;
                $QueueDoctor['QueuedDoctor']['status'] = 2; // ready to pay
                $QueueDoctor['QueuedDoctor']['doctor_id'] = $user['User']['id'];
                $QueueDoctor['QueuedDoctor']['modified_by'] = $user['User']['id'];
                $this->QueuedDoctor->save($QueueDoctor);

                /**
                 * Patient IPD
                 */
                if ($this->data['PatientConsultation']['room_id'] != "") {
                    $this->PatientIpd->create();
                    $data['PatientIpd']['patient_id'] = $_POST['patient_id'];
                    $data['PatientIpd']['date_ipd'] = date("Y-m-d H:i:s");
                    $data['PatientIpd']['queued_doctor_id'] = $this->data['QeuedDoctor']['id'];
                    $data['PatientIpd']['doctor_id'] = $user['User']['id'];
                    $data['PatientIpd']['company_id'] = 1;
                    $data['PatientIpd']['group_id'] = 2;
                    $data['PatientIpd']['allergies'] = $this->data['PatientIpd']['allergies'];
                    $data['PatientIpd']['ipd_code'] = $this->Helper->getAutoGeneratePatientIpdCode();
                    $data['PatientIpd']['created'] = date('Y-m-d H:i:s');
                    $data['PatientIpd']['created_by'] = $user['User']['id'];
                    if ($this->PatientIpd->save($data['PatientIpd'])) {
                        $patientIpdId = $this->PatientIpd->getLastInsertId();
                        $this->PatientStayInRoom->create();
                        $data['PatientStayInRoom']['patient_ipd_id'] = $patientIpdId;
                        $data['PatientStayInRoom']['room_id'] = $this->data['PatientConsultation']['room_id'];
                        $this->PatientStayInRoom->save($data['PatientStayInRoom']);

                        // check labo from opd
                        $patient = ClassRegistry::init('Patient')->find('first', array('fields' => 'patient_group_id,company_insurance_id,patient_bill_type_id', 'conditions' => array('is_active' => 1, 'id' => $_POST['patient_id'])));
                        $queryLaboOpd = mysql_query("SELECT labo_requests.labo_item_group_id, queued_labos.doctor_id, labos.id AS laboId  FROM labos INNER JOIN queued_labos ON labos.queued_id = queued_labos.id INNER JOIN labo_requests ON labos.id = labo_requests.labo_id WHERE queued_labos.queue_id = {$this->data['Queue']['id']} AND labos.status > 0 AND labo_requests.is_active = 1 AND queued_labos.status = 2");
                        while ($rowLaboOpd = mysql_fetch_array($queryLaboOpd)) {
                            $hospitalPrice = 0;
                            $unitPrice = 0;
                            // patient have insurance company
                            if ($patient['Patient']['patient_bill_type_id'] == 3 && $patient['Patient']['company_insurance_id'] != 0) {
                                $queryLaboPatientPrices = mysql_query("SELECT LaboItemPriceInsurance.id, unit_price FROM labo_item_price_insurances AS LaboItemPrice LEFT JOIN labo_item_price_insurance_patient_group_details AS LaboItemPriceInsurance ON LaboItemPrice.id = LaboItemPriceInsurance.labo_item_price_insurance_id "
                                        . " WHERE LaboItemPrice.labo_item_group_id = '" . $rowLaboOpd['labo_item_group_id'] . "' AND LaboItemPrice.company_insurance_id ='" . $patient['Patient']['company_insurance_id'] . "' AND patient_group_id = '" . $patient['Patient']['patient_group_id'] . "' AND LaboItemPriceInsurance.is_active = 1 AND LaboItemPrice.is_active = 1");
                                while ($resultLaboPatientPrice = mysql_fetch_array($queryLaboPatientPrices)) {
                                    $laboItemId = $resultLaboPatientPrice['id'];
                                    $unitPrice = $resultLaboPatientPrice['unit_price'];
                                }
                            } else {

                                $queryLaboPatientPrices = mysql_query("SELECT id, unit_price, hospital_price FROM labo_item_patient_groups "
                                        . " WHERE labo_item_group_id = '" . $rowLaboOpd['labo_item_group_id'] . "' AND patient_group_id = '" . $patient['Patient']['patient_group_id'] . "' AND is_active = 1");
                                while ($resultLaboPatientPrice = mysql_fetch_array($queryLaboPatientPrices)) {
                                    $laboItemId = $resultLaboPatientPrice['id'];
                                    $unitPrice = $resultLaboPatientPrice['unit_price'];
                                    $hospitalPrice = $resultLaboPatientPrice['hospital_price'];
                                }
                            }
                            $this->PatientIpdServiceDetail->create();
                            $data['PatientIpdServiceDetail']['patient_ipd_id'] = $patientIpdId;
                            $data['PatientIpdServiceDetail']['labo_id'] = $rowLaboOpd['laboId'];
                            $data['PatientIpdServiceDetail']['type'] = 2;
                            $data['PatientIpdServiceDetail']['exchange_rate_id'] = 3;
                            $data['PatientIpdServiceDetail']['doctor_id'] = $rowLaboOpd['doctor_id'];
                            $data['PatientIpdServiceDetail']['service_id'] = $rowLaboOpd['labo_item_group_id'];
                            $data['PatientIpdServiceDetail']['qty'] = 1;
                            $data['PatientIpdServiceDetail']['date_created'] = date('Y-m-d H:i:s');
                            $data['PatientIpdServiceDetail']['unit_price'] = $unitPrice;
                            $data['PatientIpdServiceDetail']['total_price'] = $unitPrice;
                            $data['PatientIpdServiceDetail']['created'] = date('Y-m-d H:i:s');
                            $data['PatientIpdServiceDetail']['created_by'] = $user['User']['id'];
                            $this->PatientIpdServiceDetail->save($data['PatientIpdServiceDetail']);
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

        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'QeuedDoctor.status' => 1, 'Queue.id' => $queueId, 'QeuedDoctor.id' => $queueDoctorId),
            'fields' => array('Queue.id, QeuedDoctor.id, PatientVitalSign.*, PatientVitalSignBloodPressure.*'),
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
                )
        )));
        $doctorConsultationIds = ClassRegistry::init('DoctorConsultation')->find('list', array(
            'order' => array('DoctorConsultation.name ASC'),
            'conditions' => array('DoctorConsultation.is_active' => 1)
        ));
        $examinations = ClassRegistry::init('Examination')->find('list', array('order' => array('Examination.name ASC'), 'conditions' => array('is_active=1')));
        $complains = ClassRegistry::init('ChiefComplain')->find('list', array('order' => array('ChiefComplain.name ASC'), 'conditions' => array('is_active=1')));
        $medicals = ClassRegistry::init('MedicalHistory')->find('list', array('order' => array('MedicalHistory.name ASC'), 'conditions' => array('is_active=1')));
        $patientDiagnostics = ClassRegistry::init('Diagnostic')->find('list', array('order' => array('Diagnostic.name ASC'), 'conditions' => array('is_active=1')));
        $doctorComments = ClassRegistry::init('DoctorComment')->find('list', array('order' => array('DoctorComment.name ASC'), 'conditions' => array('is_active=1 AND type = 1')));
        $dailyClinicalReports = ClassRegistry::init('DailyClinicalReport')->find('list', array('order' => array('DailyClinicalReport.name ASC'), 'conditions' => array('is_active=1')));
        $rooms = ClassRegistry::init('Room')->find('all', array('order' => array('Room.name ASC'), 'conditions' => 'Room.is_active=1', 'order' => 'room_name'));
        $this->set(compact('patient', 'doctorConsultationIds', 'examinations', 'complains', 'medicals', 'patientDiagnostics', 'rooms', 'doctorComments', 'dailyClinicalReports'));
    }

    function tabConsultNum($queueDoctorId = null, $queueId = null, $patientId = null) {
        $this->layout = 'ajax';
        $this->loadModel('PatientVitalSignBloodPressure');
        $this->loadModel('PatientVitalSign');
        $this->loadModel('PatientConsultation');
        $this->loadModel('QueuedDoctor');
        $this->loadModel('QueuedDoctor');
        $this->loadModel('ChiefComplain');
        $this->loadModel('MedicalHistory');
        $this->loadModel('PatientIpd');
        $this->loadModel('PatientStayInRoom');
        if (!empty($queueId)) {
            if ($queueId == "view") {
                //new update for show patient consultation
                $patientId = $queueDoctorId;
            } else {
                $patientResult = $this->Queue->find('first', array('conditions' => array('Queue.id' => $queueId)));
                $patientId = $patientResult['Queue']['patient_id'];
            }
            $consultation = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'PatientConsultation.is_active' => 1, 'QeuedDoctor.status >=1', 'Patient.id' => $patientId),
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
                'order' => 'PatientConsultation.created DESC',
                'group' => 'PatientConsultation.queued_doctor_id'
            ));
            $patientIpd = $this->PatientIpd->find('first', array('conditions' => array('PatientStayInRoom.status' => 1, 'PatientIpd.is_active' => 1, 'PatientIpd.patient_id' => $patientId),
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
            $complains = ClassRegistry::init('ChiefComplain')->find('list', array('order' => array('ChiefComplain.name ASC'), 'conditions' => array('is_active=1')));
            $this->set(compact('consultation', 'doctorConsultationIds', 'complains', 'medicals', 'examinations', 'patientDiagnostics', 'patientId', 'patientIpd', 'rooms'));
        }
    }

    /**
     * patientConsultId : Patient Consultation ID
     * queueId  : Queue ID
     * queueDoctorId : queue Doctor ID
     */
    function editConsult($patientConsultId = null, $queueDoctorId = null, $queueId = null) {
        $this->layout = 'ajax';
        if (!empty($this->data)) {

            $result = array();
            $this->loadModel('PatientVitalSignBloodPressure');
            $this->loadModel('PatientVitalSign');
            $this->loadModel('PatientConsultation');
            $this->loadModel('QueuedDoctor');
            $this->loadModel('DoctorChiefComplain');
            $this->loadModel('Appointment');
            $this->loadModel('PatientIpd');
            $this->loadModel('PatientStayInRoom');
            $this->loadModel('PatientIpdServiceDetail');
            /**
             * include customize function
             */
            include('includes/function.php');

            $user = $this->getCurrentUser();


            if ($this->data['PatientConsultation']['app_date'] != "") {
                $this->Appointment->create();
                $appointment['Appointment']['id'] = $this->data['Appointment']['id'];
                $appointment['Appointment']['patient_id'] = $_POST['patient_id'];
                $appointment['Appointment']['doctor_id'] = $user['User']['id'];
                $appointment['Appointment']['queue_id'] = $this->data['Queue']['id'];
                $appointment['Appointment']['queue_doctor_id'] = $this->data['QeuedDoctor']['id'];
                $appointment['Appointment']['app_date'] = $this->data['PatientConsultation']['app_date'];
                $appointment['Appointment']['description'] = $this->data['PatientConsultation']['description'];
                $appointment['Appointment']['created_by'] = $user['User']['id'];
                $appointment['Appointment']['is_close'] = 0;
                $this->Appointment->save($appointment);
            }

            $this->loadModel('PatientConsultation');
            /**
             * Step 1 : Shadow Patient Consultation
             */
            $this->PatientConsultation->updateAll(
                    array('PatientConsultation.is_active' => "2", 'PatientConsultation.modified_by' => $user['User']['id']), array('PatientConsultation.id' => $patientConsultId)
            );
            /**
             * Step 2 : Create New Patient Consultation
             */
            $user = $this->getCurrentUser();

            $this->PatientConsultation->create();
            $this->data['PatientConsultation']['consultation_code'] = $this->Helper->getAutoGenerateConsultationCode();
            $this->data['PatientConsultation']['queued_doctor_id'] = $this->data['QeuedDoctor']['id'];
            $this->data['PatientConsultation']['physical_examination_id'] = $this->data['PatientConsultation']['examination'];
            $this->data['PatientConsultation']['daignostic_id'] = $this->data['PatientConsultation']['patient_diagnostic'];
            $this->data['PatientConsultation']['medical_surgery'] = $this->data['PatientConsultation']['medical_surgery_history'];
            $this->data['PatientConsultation']['obstric_gynecologie'] = $this->data['PatientConsultation']['obstetic_history'];
            $this->data['PatientConsultation']['created_by'] = $user['User']['id'];
            if ($this->PatientConsultation->save($this->data['PatientConsultation'])) {
                /**
                 * Add Doctor Chief Complain
                 */
                $this->DoctorChiefComplain->updateAll(
                        array('DoctorChiefComplain.status' => "2", 'DoctorChiefComplain.modified_by' => $user['User']['id']), array('DoctorChiefComplain.queued_id' => $this->data['Queue']['id'], 'DoctorChiefComplain.queued_doctor_id' => $this->data['QeuedDoctor']['id'])
                );

                for ($i = 0; $i < sizeof($this->data['PatientConsultation']['complain_id']); $i++) {
                    if (isset($this->data['PatientConsultation']['complain_id'][$i])) {
                        if ($this->data['PatientConsultation']['complain_id'][$i] != "") {
                            $this->DoctorChiefComplain->create();
                            $data['DoctorChiefComplain']['queued_id'] = $this->data['Queue']['id'];
                            $data['DoctorChiefComplain']['queued_doctor_id'] = $this->data['QeuedDoctor']['id'];
                            $data['DoctorChiefComplain']['chief_complain_id'] = $this->data['PatientConsultation']['complain_id'][$i];
                            $data['DoctorChiefComplain']['created_by'] = $user['User']['id'];
                            $data['DoctorChiefComplain']['status'] = 1;
                            $this->DoctorChiefComplain->save($data['DoctorChiefComplain']);
                        }
                    }
                }
                // update Patient Follow Up
                mysql_query("UPDATE patient_followups SET patient_consultation_id=" . $this->PatientConsultation->getLastInsertId() . " WHERE patient_consultation_id=" . $patientConsultId);


                /**
                 * Patient IPD
                 */
                if ($this->data['PatientConsultation']['room_id'] != "") {
                    $this->PatientIpd->create();
                    if (empty($this->data['PatientIpd']['id'])) {
                        $dataPatientIpd['PatientIpd']['ipd_code'] = $this->Helper->getAutoGeneratePatientIpdCode();
                    } else {
                        $dataPatientIpd['PatientIpd']['id'] = $this->data['PatientIpd']['id'];
                        $patientIpdId = $this->data['PatientIpd']['id'];
                    }
                    $dataPatientIpd['PatientIpd']['patient_id'] = $_POST['patient_id'];
                    $dataPatientIpd['PatientIpd']['queued_doctor_id'] = $this->data['QeuedDoctor']['id'];
                    $dataPatientIpd['PatientIpd']['date_ipd'] = date("Y-m-d H:i:s");
                    $dataPatientIpd['PatientIpd']['doctor_id'] = $user['User']['id'];
                    $dataPatientIpd['PatientIpd']['company_id'] = 1;
                    $dataPatientIpd['PatientIpd']['group_id'] = 2;
                    $dataPatientIpd['PatientIpd']['allergies'] = $this->data['PatientIpd']['allergies'];
                    $dataPatientIpd['PatientIpd']['modified'] = date('Y-m-d H:i:s');
                    $dataPatientIpd['PatientIpd']['modified_by'] = $user['User']['id'];
                    if ($this->PatientIpd->save($dataPatientIpd['PatientIpd'])) {
                        if (empty($this->data['PatientIpd']['id'])) {
                            $patientIpdId = $this->PatientIpd->getLastInsertId();
                        }
                        $this->PatientStayInRoom->updateAll(
                                array('PatientStayInRoom.status' => "0"), array('PatientStayInRoom.patient_ipd_id' => $patientIpdId)
                        );
                        $this->PatientStayInRoom->create();
                        $dataPatientStayInRoom['PatientStayInRoom']['patient_ipd_id'] = $patientIpdId;
                        $dataPatientStayInRoom['PatientStayInRoom']['room_id'] = $this->data['PatientConsultation']['room_id'];
                        $this->PatientStayInRoom->save($dataPatientStayInRoom['PatientStayInRoom']);

                        // check labo from opd
                        if (empty($this->data['PatientIpd']['id'])) {
                            $patient = ClassRegistry::init('Patient')->find('first', array('fields' => 'patient_group_id,company_insurance_id,patient_bill_type_id', 'conditions' => array('is_active' => 1, 'id' => $_POST['patient_id'])));

                            $queryLaboOpd = mysql_query("SELECT labo_requests.labo_item_group_id, queued_labos.doctor_id, labos.id AS laboId FROM labos INNER JOIN queued_labos ON labos.queued_id = queued_labos.id INNER JOIN labo_requests ON labos.id = labo_requests.labo_id WHERE queued_labos.queue_id = {$this->data['Queue']['id']} AND labos.status > 0 AND labo_requests.is_active = 1 AND queued_labos.status = 2");
                            while ($rowLaboOpd = mysql_fetch_array($queryLaboOpd)) {
                                $hospitalPrice = 0;
                                $unitPrice = 0;
                                // patient have insurance company
                                if ($patient['Patient']['patient_bill_type_id'] == 3 && $patient['Patient']['company_insurance_id'] != 0) {
                                    $queryLaboPatientPrices = mysql_query("SELECT LaboItemPriceInsurance.id, unit_price FROM labo_item_price_insurances AS LaboItemPrice LEFT JOIN labo_item_price_insurance_patient_group_details AS LaboItemPriceInsurance ON LaboItemPrice.id = LaboItemPriceInsurance.labo_item_price_insurance_id "
                                            . " WHERE LaboItemPrice.labo_item_group_id = '" . $rowLaboOpd['labo_item_group_id'] . "' AND LaboItemPrice.company_insurance_id ='" . $patient['Patient']['company_insurance_id'] . "' AND patient_group_id = '" . $patient['Patient']['patient_group_id'] . "' AND LaboItemPriceInsurance.is_active = 1 AND LaboItemPrice.is_active = 1");
                                    while ($resultLaboPatientPrice = mysql_fetch_array($queryLaboPatientPrices)) {
                                        $laboItemId = $resultLaboPatientPrice['id'];
                                        $unitPrice = $resultLaboPatientPrice['unit_price'];
                                    }
                                } else {

                                    $queryLaboPatientPrices = mysql_query("SELECT id, unit_price, hospital_price FROM labo_item_patient_groups "
                                            . " WHERE labo_item_group_id = '" . $rowLaboOpd['labo_item_group_id'] . "' AND patient_group_id = '" . $patient['Patient']['patient_group_id'] . "' AND is_active = 1");
                                    while ($resultLaboPatientPrice = mysql_fetch_array($queryLaboPatientPrices)) {
                                        $laboItemId = $resultLaboPatientPrice['id'];
                                        $unitPrice = $resultLaboPatientPrice['unit_price'];
                                        $hospitalPrice = $resultLaboPatientPrice['hospital_price'];
                                    }
                                }
                                $this->PatientIpdServiceDetail->create();
                                $data['PatientIpdServiceDetail']['patient_ipd_id'] = $patientIpdId;
                                $data['PatientIpdServiceDetail']['labo_id'] = $rowLaboOpd['laboId'];
                                $data['PatientIpdServiceDetail']['type'] = 2;
                                $data['PatientIpdServiceDetail']['exchange_rate_id'] = 3;
                                $data['PatientIpdServiceDetail']['doctor_id'] = $rowLaboOpd['doctor_id'];
                                $data['PatientIpdServiceDetail']['service_id'] = $rowLaboOpd['labo_item_group_id'];
                                $data['PatientIpdServiceDetail']['qty'] = 1;
                                $data['PatientIpdServiceDetail']['date_created'] = date('Y-m-d H:i:s');
                                $data['PatientIpdServiceDetail']['unit_price'] = $unitPrice;
                                $data['PatientIpdServiceDetail']['total_price'] = $unitPrice;
                                $data['PatientIpdServiceDetail']['created'] = date('Y-m-d H:i:s');
                                $data['PatientIpdServiceDetail']['created_by'] = $user['User']['id'];
                                $this->PatientIpdServiceDetail->save($data['PatientIpdServiceDetail']);
                            }
                        }
                    }
                }


                $result['patientConsultId'] = $patientConsultId;
                $result['queueDoctorId'] = $queueDoctorId;
                $result['queueId'] = $queueId;
                echo json_encode($result);
                exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                exit;
            }
        }
    }

    function printRecord($patientConsultId = null, $queueDoctorId = null, $queueId = null) {
        $this->layout = 'ajax';
        $this->loadModel('Branch');
        if (!empty($patientConsultId)) {
            $this->data = $this->Branch->read(null, 1);
            $consultation = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'PatientConsultation.is_active' => 1, 'QeuedDoctor.status >= 1', 'Queue.id' => $queueId, 'QeuedDoctor.id' => $queueDoctorId),
                'fields' => array('Queue.id, QeuedDoctor.id, PatientVitalSign.*, PatientVitalSignBloodPressure.*, PatientConsultation.*, Patient.*, QeuedLabo.id, DoctorConsultation.*', 'GenitoUrinarySystem.*'),
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
                            'GenitoUrinarySystem.queued_doctor_id = QeuedDoctor.id AND GenitoUrinarySystem.queued_id = Queue.id AND GenitoUrinarySystem.status = 1'
                        )
                    ),
            )));
            $this->set(compact('consultation'));
        }
    }

    function printMedicalCertificate($patientConsultId = null, $queueDoctorId = null, $queueId = null) {
        $this->layout = 'ajax';
        $this->loadModel('Branch');
        if (!empty($patientConsultId)) {
            $this->data = $this->Branch->read(null, 1);
            $consultation = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'PatientConsultation.is_active' => 1, 'QeuedDoctor.status >= 1', 'Queue.id' => $queueId, 'QeuedDoctor.id' => $queueDoctorId),
                'fields' => array('Queue.id, QeuedDoctor.id, PatientConsultation.*, Patient.*, DoctorConsultation.*'),
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
                    array('table' => 'doctor_consultations',
                        'alias' => 'DoctorConsultation',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'DoctorConsultation.id = PatientConsultation.doctor_consultation_ids'
                        )
                    ),
            )));
            $this->set(compact('consultation'));
        }
    }

    function printTreatment($queueId = null, $treatmentId = null) {
        $this->layout = 'ajax';
        $this->loadModel('Treatment');
        $this->loadModel('TreatmentDetail');
        $treatments = $this->Treatment->read(null, $treatmentId);
        $treatmentDetails = $this->TreatmentDetail->find('all', array('conditions' => array('treatment_id' => $treatmentId)));
        $patients = $this->Queue->read(null, $queueId);
        $this->set('patients', $patients);
        $this->set('treatments', $treatments);
        $this->set('treatmentDetails', $treatmentDetails);
        $this->set('qId', $queueId);
        if (!empty($queueId)) {
            $this->Queue->id = $queueId;
            $queueList = $this->Queue->find('list', array('conditions' => array('patient_id' => $this->Queue->field('patient_id'))));
            $this->set('treatment', ClassRegistry::init('Treatment')->find('all', array('conditions' => array('queue_id' => $queueList), 'order' => 'created DESC')));
        }
    }

    function followup() {
        $this->layout = 'ajax';
        $this->loadModel('DailyClinicalReport');
        $dailyClinicalReports = ClassRegistry::init('DailyClinicalReport')->find('list', array('conditions' => array('is_active=1')));
        $this->set(compact('dailyClinicalReports'));
    }

    /**
     * patientConsultationId : Patient Consultation ID
     * queueId  : Queue ID
     */
    function addNewFollowUp($patientConsultationId = null, $queueDoctorId = null, $queueId = null) {
        $this->loadModel('PatientFollowup');
        $user = $this->getCurrentUser();
        $this->data['PatientFollowup']['queue_id'] = $queueId;
        $this->data['PatientFollowup']['queued_doctor_id'] = $queueDoctorId;
        $this->data['PatientFollowup']['patient_consultation_id'] = $patientConsultationId;
        $this->data['PatientFollowup']['created_by'] = $user['User']['id'];
        if ($this->PatientFollowup->save($this->data['PatientFollowup'])) {
            /**
             * Update Queue
             */
            $Queue['Queue']['id'] = $queueId;
            $Queue['Queue']['status'] = 2; // ready to pay
            $Queue['Queue']['modified_by'] = $user['User']['id'];
            $this->Queue->save($Queue);

            echo '1';
            exit();
        } else {
            echo '0';
            exit();
        }
    }

    function discharge($pId = null) {
        $this->layout = 'ajax';
        $query_last_discharge = mysql_query("SELECT id FROM patient_discharges WHERE patient_id=" . $pId . " ORDER BY created DESC LIMIT 1");
        if (mysql_num_rows($query_last_discharge)) {
            $data = mysql_fetch_array($query_last_discharge);
            $this->loadModel('PatientDischarge');
            $this->data = $this->PatientDischarge->read(null, $data['id']);
        }
        $this->set('patient', $this->Patient->read(null, $pId));
        ClassRegistry::init('Country')->id = $this->Patient->field('nationality');
        $this->set('nationality', ClassRegistry::init('Country')->field('name'));
    }

    function addNewDischarge($pId = null) {
        $this->loadModel('PatientDischarge');
        $user = $this->getCurrentUser();
        $this->data['PatientDischarge']['patient_id'] = $pId;
        $this->data['PatientDischarge']['created_by'] = $user['User']['id'];

        if ($this->PatientDischarge->save($this->data['PatientDischarge'])) {
            echo '1';
        } else {
            echo '0';
        }
    }

    function printDischarge($pId = null) {
        $this->layout = 'ajax';
        $query_last_discharge = mysql_query("SELECT id FROM patient_discharges WHERE patient_id=" . $pId . " ORDER BY created DESC LIMIT 1");
        if (mysql_num_rows($query_last_discharge)) {
            $data = mysql_fetch_array($query_last_discharge);
            $this->loadModel('PatientDischarge');
            $this->data = $this->PatientDischarge->read(null, $data['id']);
        }
        $this->set('patient', $this->Patient->read(null, $pId));
        ClassRegistry::init('Country')->id = $this->Patient->field('nationality');
        $this->set('nationality', ClassRegistry::init('Country')->field('name'));
    }

    function tabPharma($id = null) {
        $this->layout = 'ajax';
        $this->loadModel('Treatment');
        $isDone = $this->Treatment->findByQueueId($id);
        if (!empty($isDone)) {
            echo GENERAL_DONE;
            exit();
        }
        if (!empty($this->data)) {
            $this->loadModel('TreatmentDetail');
            $this->loadModel('SaleStock');
            $this->loadModel('SaleStockDetail');
            $this->loadModel('StockOnHand');

            $user = $this->getCurrentUser();
            $this->Treatment->create();

            $data['Treatment']['treatment_code'] = $this->Helper->getAutoGenerateTreatmentCode();
            $data['Treatment']['queue_id'] = $id;
            $data['Treatment']['diagnostic'] = $this->data['Diagnostic']['diagnostic'];
            $data['Treatment']['created_by'] = $user['User']['id'];
            $data['Treatment']['is_active'] = 1;
            if ($this->Treatment->save($data['Treatment'])) {
                $treatmentId = $this->Treatment->getLastInsertId();
                $data['TreatmentDetail']['treatment_id'] = $treatmentId;
                $total_price = 0;
                foreach ($this->data['Treatment'] as $medicine) {
                    $amount = $medicine['qty'];
                    $total_price += $amount * $medicine['price_sale_out'];
                    /**
                     * Treatment Detail
                     */
                    $this->TreatmentDetail->create();
                    $data['TreatmentDetail']['sale_stock_id'] = $medicine['sale_stock_id'];
                    $data['TreatmentDetail']['amount'] = $amount;
                    $data['TreatmentDetail']['morning'] = $medicine['morning'];
                    $data['TreatmentDetail']['afternoon'] = $medicine['afternoon'];
                    $data['TreatmentDetail']['evening'] = $medicine['evening'];
                    $data['TreatmentDetail']['night'] = $medicine['night'];
                    $data['TreatmentDetail']['num_day'] = $medicine['num_day'];
                    $data['TreatmentDetail']['note'] = $medicine['note'];
                    $data['TreatmentDetail']['discount'] = $medicine['discount'];
                    $this->TreatmentDetail->save($data['TreatmentDetail']);
                    $treatmentDetailId = $this->TreatmentDetail->getLastInsertId();


                    $this->StockOnHand->create();
                    $data['StockOnHand']['treatment_id'] = $treatmentId;
                    $data['StockOnHand']['treatment_detail_id'] = $treatmentDetailId;
                    $data['StockOnHand']['sale_stock_id'] = $medicine['sale_stock_id'];
                    $data['StockOnHand']['amount'] = $amount;
                    $data['StockOnHand']['created_by'] = $user['User']['id'];
                    $this->StockOnHand->save($data['StockOnHand']);
                }

                /**
                 * Update total_price in Treatment
                 */
                $Treatment['Treatment']['id'] = $treatmentId;
                $Treatment['Treatment']['total_price'] = $total_price;
                $this->Treatment->save($Treatment);

                /**
                 * Update Queue
                 */
                $Queue['Queue']['id'] = $id;
                $Queue['Queue']['pharmacy'] = 2;
                $Queue['Queue']['status'] = 2; // ready to pay
                $Queue['Queue']['modified_by'] = $user['User']['id'];
                $this->Queue->save($Queue);

                $this->Session->setFlash(__('The phamacotherapy has been saved', true), 'flash_success');
                $this->redirect(array('controller' => 'doctors', 'action' => 'consultation', $id));
            } else {
                $this->Session->setFlash(__('The phamacotherapy could not be saved. Please, try again.', true), 'flash_failure');
            }
        }
    }

    function getMedicineStock() {
        $this->layout = 'ajax';
        $this->loadModel('MedicineRequest');
        $argument = $_POST['medicine'];
        $medcineStock = $this->MedicineRequest->getMedicineFromSaleStock($argument);
        $this->set('medicineStocks', $medcineStock);
    }

    function tabPharmaNum($id = null) {
        $this->layout = 'ajax';
        if (!empty($id)) {
            $this->Queue->id = $id;
            $queueList = $this->Queue->find('list', array('conditions' => array('patient_id' => $this->Queue->field('patient_id'))));
            $this->set('treatment', ClassRegistry::init('Treatment')->find('all', array('conditions' => array('queue_id' => $queueList), 'order' => 'created DESC')));
        }
    }

    function removePharma($treatmentId = null) {
        $this->layout = 'ajax';
        $queryTreatmentDetail = mysql_query("SELECT * FROM treatment_details WHERE treatment_id=" . $treatmentId);
        mysql_query("DELETE FROM treatments WHERE id=" . $treatmentId);
        mysql_query("DELETE FROM treatment_details WHERE treatment_id=" . $treatmentId);
        exit();
    }

    function tabLabo($queueDoctorId = null, $queueId = null) {
        $this->layout = 'ajax';
        $this->loadModel('LaboRequest');
        $this->loadModel('LaboItemGroup');
        $this->loadModel('LaboTitleGroup');
        $this->loadModel('Labo');
        $this->loadModel('QueuedLabo');
        $this->loadModel('PatientConsultation');
        // check patient ipd
        $this->loadModel('PatientIpd');
        $patientIPD =  $this->PatientIpd->find('first', array('conditions' => array('queued_doctor_id' => $queueDoctorId, 'is_active' => 1)));
        if (!empty($patientIPD)) {
            echo MESSAGE_PATIENT_ALREADY_ADMINISTRATION;
            exit();
        }
        $patientConsultations = $this->PatientConsultation->find('first', array('conditions' => array('PatientConsultation.is_active' => 1, 'PatientConsultation.queued_doctor_id' => $queueDoctorId)));
        $queueStatus = $this->Queue->find('first', array('conditions' => array('Queue.id' => $queueId)));
        $laboItemGroups = $this->LaboItemGroup->find('all', array('fields' => array('LaboItemGroup.id', 'LaboItemGroup.name'), 'conditions' => array('LaboItemGroup.is_active != 2')));
        $laboTitleGroup = $this->LaboTitleGroup->find("all", array("conditions" => array("LaboTitleGroup.is_active!=2") , 'order' => 'LaboTitleGroup.ordering ASC'));    
        $queueLaboResult = $this->QueuedLabo->find('first', array('conditions' => array('QueuedLabo.queue_id' => $queueId), 'order' => 'QueuedLabo.id DESC'));
        $labo = $this->Labo->getLaboByQueuedLaboId($queueLaboResult['QueuedLabo']['id']);
        if ($labo['Labo']['status'] == 2) {
            $labo = $this->Labo->getLaboByQueuedLaboId(0);
        }
        $laboSelected = $this->LaboRequest->find('list', array('conditions' => array('LaboRequest.labo_id' => $labo['Labo']['id'], 'LaboRequest.is_active != 2'), 'fields' => array('LaboRequest.id', 'LaboRequest.labo_item_group_id')));
        $this->set(compact('patientConsultations', 'laboItemGroups', 'queueId', 'laboTitleGroup', 'labo', 'laboSelected', 'queueStatus', 'queueDoctorId'));
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
            $otherReqeust = $this->data['Labo']['other_reqeust'];
            $dateTime = date("Y-m-d H:i:s");

            $this->QueuedLabo->updateAll(
                    array('QueuedLabo.status' => "0"), array('QueuedLabo.queue_id' => $queueId)
            );

            $queuedLabo['QueuedLabo']['queue_id'] = $queueId;
            $queuedLabo['QueuedLabo']['doctor_id'] = $userId;
            $queuedLabo['QueuedLabo']['created_by'] = $userId;
            $queuedLabo['QueuedLabo']['status'] = 2;
            if ($this->QueuedLabo->save($queuedLabo)) {
                $queuedLaboId = $this->QueuedLabo->getLastInsertId();
                $labo['Labo']['id'] = $laboId;
                $labo['Labo']['queued_id'] = $queuedLaboId;
                $labo['Labo']['diagonist'] = $diagnostic;
                $labo['Labo']['chief_complain'] = $chiefComplain;
                $labo['Labo']['other_reqeust'] = $otherReqeust;      
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
                    foreach ($this->data['LaboItemGroup'] as $laboItemGoup) {
                        $this->LaboRequest->create();
                        $laboItemIds = $this->LaboItemGroup->find("first", array("conditions" => array("LaboItemGroup.id" => $laboItemGoup), "fields" => array("LaboItemGroup.labo_item_id")));
                        $laboItemIds = explode(",", $laboItemIds['LaboItemGroup']['labo_item_id']);
                        $laboRequest['LaboRequest']['labo_id'] = $this->Labo->id;
                        $laboRequest['LaboRequest']['labo_item_group_id'] = $laboItemGoup;
                        $laboRequest['LaboRequest']['request'] = @serialize($laboItemIds);
                        $laboRequest['LaboRequest']['created_by'] = $userId;
                        $this->LaboRequest->save($laboRequest);
                    }
                }
                echo MESSAGE_LABO_SAVED;
                exit;
            }
        }
        exit();
    }

    function tabLaboNum($queueDoctorId = null, $queueId = null) {
        $this->layout = 'ajax';
        $this->loadModel("Labo");
        $this->loadModel("QueuedLabo");
        $this->loadModel("LaboRequest");

        //marady                
        if ($queueId == "view") {
            $queueId = $queueDoctorId;
        }
        $patientResult = $this->Queue->find('first', array('conditions' => array('Queue.id' => $queueId)));
        $patientId = $patientResult['Queue']['patient_id'];

        $queueResult = $this->Queue->find('list', array('conditions' => array('Queue.patient_id' => $patientId)));
        $queuedLabo = $this->QueuedLabo->find('list', array('fields' => array('QueuedLabo.id'), 'conditions' => array('QueuedLabo.status <= 2', 'queue_id' => $queueResult)));

        $this->set('labo', ClassRegistry::init('Labo')->find('all', array('conditions' => array('queued_id' => $queuedLabo), 'order' => 'Labo.created DESC')));
    }

    function printLab($queueId = null, $historyLabo = null) {
        $this->layout = 'ajax';
        $this->loadModel("Labo");
        $this->loadModel("QueuedLabo");
        $this->loadModel("LaboRequest");
        $this->loadModel('Branch');
        $queuedLabo = $this->QueuedLabo->find('first', array('fields' => array('QueuedLabo.id'), 'conditions' => array('QueuedLabo.status = 2', 'queue_id' => $queueId)));
        $patientResult = $this->Queue->find('first', array('conditions' => array('Queue.id' => $queueId)));
        $patientId = $patientResult['Queue']['patient_id'];
        $patient = $this->Patient->find('first', array('conditions' => array('id' => $patientId)));
        $labo = ClassRegistry::init('Labo')->find('all', array('conditions' => array('queued_id' => $queuedLabo['QueuedLabo']['id']), 'order' => 'Labo.created DESC'));
        $this->data = $this->Branch->read(null, 1);
        $user = $this->getCurrentUser();
        $this->set(compact('patient', 'labo', 'user'));
    }

    function viewLaboResult($qLaboId = null) {
        $this->layout = 'ajax';
        if (!empty($qLaboId)) {
            $this->loadModel('QueuedLabo');
            $this->loadModel('LaboItemCategory');
            $this->loadModel('Labo');
            $this->loadModel('Patient');
            $this->loadModel('LaboItem');
            $conditions = array(
                'conditions' => array('QueuedLabo.id' => $qLaboId)
            );
            $laboItemCategories = $this->LaboItemCategory->find('all');
            $qPatient = $this->QueuedLabo->find('first', $conditions);
            $patient = $this->Patient->find('first', array('conditions' => array('id' => $qPatient['Queue']['patient_id'])));
            $labo = $this->Labo->getLaboByQueuedLaboId($qLaboId);
            $listLaboItemCategories = $this->LaboProcess->getListLaboItemCategories($labo);
            $itemIds = "";
            $laboItems = $this->LaboItem->find('all');
            $this->set('sex', $this->Patient->field('sex'));
            $this->set(compact('qPatient', 'patient', 'listLaboItemCategories', 'laboItems', 'labo', 'laboItemCategories'));
        }
    }

    function tabPrescription($queueDoctorId = null, $queueId = null) {
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
                            $orderDetail['OrderDetail']['unit_cost'] = isset($_POST['unit_cost'][$i]) ? $_POST['unit_cost'][$i] : 0;
                            $orderDetail['OrderDetail']['unit_price'] = isset($_POST['unit_price'][$i]) ? $_POST['unit_price'][$i] : 0;
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
        } else {
            $orders = ClassRegistry::init('Order')->find("all", array('conditions' => array('Order.queue_doctor_id' => $queueDoctorId, 'Order.queue_id' => $queueId, 'Order.status >= 1')));
        }

        if (!empty($orders)) {
            echo GENERAL_DONE;
            exit();
        }
        
        // check patient ipd
        $this->loadModel('PatientIpd');
        $patientIPD =  $this->PatientIpd->find('first', array('conditions' => array('queued_doctor_id' => $queueDoctorId, 'is_active' => 1)));
        if (!empty($patientIPD)) {
            echo MESSAGE_PATIENT_ALREADY_ADMINISTRATION;
            exit();
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
        $this->set(compact("code", "companies", "queueDoctorId", "queueId", "orders", 'branches'));
    }

    function tabPrescriptionNum($queueDoctorId = null, $queueId = null) {
        $this->layout = 'ajax';
        $this->loadModel('Queue');
        $this->loadModel('Order');
        $this->loadModel('OrderDetail');
        $this->loadModel('OrderService');
        $this->loadModel('OrderMisc');
        $patientResult = $this->Queue->find('first', array('conditions' => array('Queue.id' => $queueId)));
        $patientId = $patientResult['Queue']['patient_id'];
        $queueResult = $this->Queue->find('list', array('conditions' => array('Queue.patient_id' => $patientId)));
        $orderResult = ClassRegistry::init('Order')->find("all", array('conditions' => array('Order.queue_id' => $queueResult, 'Order.status >= 1'), 'order' => 'Order.created DESC'));
        $this->set(compact('orderResult'));
    }

    function orderDetails123() {
        $this->layout = 'ajax';
        $this->loadModel('TreatmentUse');
        $uoms = ClassRegistry::init('Uom')->find('all', array('fields' => array('Uom.id', 'Uom.name'), 'conditions' => array('Uom.is_active' => 1)));
        $treatmentUses = ClassRegistry::init('TreatmentUse')->find("all", array('conditions' => array('TreatmentUse.is_active' => 1)));
        $this->set(compact('uoms', "treatmentUses"));
    }

    function orderDetails($patientId= null) {
        $this->layout = 'ajax';
        $this->loadModel('TreatmentUse');
        $uoms = ClassRegistry::init('Uom')->find('all', array('fields' => array('Uom.id', 'Uom.name'), 'conditions' => array('Uom.is_active' => 1)));
        $treatmentUses = ClassRegistry::init('TreatmentUse')->find("all", array('conditions' => array('TreatmentUse.is_active' => 1)));
        $order = ClassRegistry::init('Order')->find("first", array('conditions' => array('Order.status' => 1,'Order.patient_id' => $patientId),'order'=>'Order.id DESC'));  
		$orderDetails = ClassRegistry::init('OrderDetail')->find('all', array('conditions' => array('OrderDetail.order_id' => $order['Order']['id'])));
        $orderMiscs    = ClassRegistry::init('OrderMisc')->find("all", array('conditions' => array('OrderMisc.order_id' => $order['Order']['id'])));
        $this->set(compact('uoms', "treatmentUses","orderDetails","orderMiscs"));
    }

    function printInvoice($id = null, $head = 0) {
        $this->layout = 'ajax';
        $this->loadModel('Order');
        $this->loadModel('OrderDetail');
        $this->loadModel('OrderService');
        $this->loadModel('OrderMisc');
        $this->loadModel('Appointment');
        if (!empty($id)) {
            $this->data = $this->Order->read(null, $id);
            if (!empty($this->data)) {
                $orderDetails = ClassRegistry::init('OrderDetail')->find("all", array('conditions' => array('OrderDetail.order_id' => $id)));
                $orderServices = ClassRegistry::init('OrderService')->find("all", array('conditions' => array('OrderService.order_id' => $id)));
                $orderMiscs = ClassRegistry::init('OrderMisc')->find("all", array('conditions' => array('OrderMisc.order_id' => $id)));
                $appointment = ClassRegistry::init('Appointment')->find("all", array('conditions' => array('Appointment.order_id' => $id)));
                $this->set(compact('orderDetails', 'orderServices', 'appointment', 'orderMiscs', 'head'));
            } else {
                exit;
            }
        } else {
            exit;
        }
    }

    function printTreatmentSticker($id = null) {
        $this->layout = 'ajax';
        if (!empty($id)) {
            $patientName = $_POST['pntName'];
            $sex = $_POST['sex'];
            $dob = $_POST['dob'];
            $medicineName = $_POST['medicineName'];
            $qty = $_POST['qty'];
            $uom = $_POST['uom'];
            $note = $_POST['note'];
            $frequency = $_POST['frequency'];
            $numDay = $_POST['numDay'];
            $weight = $_POST['weight'];
            $doctorName = $_POST['doctorName'];
            $this->set(compact('patientName', 'sex', 'dob', 'medicineName', 'qty', 'uom', 'note', 'frequency', 'numDay', 'weight', 'doctorName'));
        } else {
            exit;
        }
    }

    function product($companyId = null, $branchId = null) {
        $this->layout = 'ajax';
        $orderDate = $_POST['order_date'];
        $this->set(compact('companyId', 'branchId', 'orderDate'));
    }

    function productAjax($companyId = null, $branchId = null, $category = null) {
        $this->layout = 'ajax';
        $orderDate = $_GET['order_date'];
        $this->set(compact('companyId', 'branchId', 'category', 'orderDate'));
    }

    function searchProduct() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $joinProductBranch = array(
            'table' => 'product_branches',
            'type' => 'INNER',
            'alias' => 'ProductBranch',
            'conditions' => array(
                'ProductBranch.product_id = Product.id',
                'ProductBranch.branch_id IN (SELECT branch_id FROM user_branches WHERE user_id = ' . $user['User']['id'] . ')'
        ));
        $joinProductgroup = array(
            'table' => 'product_pgroups',
            'type' => 'INNER',
            'alias' => 'ProductPgroup',
            'conditions' => array('ProductPgroup.product_id = Product.id')
        );
        $joinPgroup = array(
            'table' => 'pgroups',
            'type' => 'INNER',
            'alias' => 'Pgroup',
            'conditions' => array(
                'Pgroup.id = ProductPgroup.pgroup_id',
                '(Pgroup.user_apply = 0 OR (Pgroup.user_apply = 1 AND Pgroup.id IN (SELECT pgroup_id FROM user_pgroups WHERE user_id = ' . $user['User']['id'] . ')))'
            )
        );
        $joins = array(
            $joinProductgroup,
            $joinPgroup,
            $joinProductBranch
        );
        $products = ClassRegistry::init('Product')->find('all', array(
            'conditions' => array('OR' => array(
                    'Product.code LIKE' => '%' . trim($this->params['url']['q']) . '%',
                    'Product.barcode LIKE' => '%' . trim($this->params['url']['q']) . '%',
                    'Product.name LIKE' => '%' . trim($this->params['url']['q']) . '%',
                    'Product.chemical LIKE' => '%' . trim($this->params['url']['q']) . '%',
                ), 'Product.company_id IN (SELECT company_id FROM user_companies WHERE user_id = ' . $user['User']['id'] . ')'
                , 'Product.is_active' => 1
                , '((Product.price_uom_id IS NOT NULL AND Product.is_packet = 0) OR (Product.price_uom_id IS NULL AND Product.is_packet = 1))'
            ),
            'joins' => $joins,
            'group' => array(
                'Product.id'
            )
        ));
        $this->set(compact('products'));
    }

    function searchProductByCode($companyId = null, $customerId = 0, $branchId = null) {
        $this->layout = 'ajax';
        $order_date = !empty($_POST['order_date']) ? $_POST['order_date'] : "0000-00-00";
        $product_code = !empty($this->data['code']) ? $this->data['code'] : "";
        $user = $this->getCurrentUser();
        $joinProductBranch = array(
            'table' => 'product_branches',
            'type' => 'INNER',
            'alias' => 'ProductBranch',
            'conditions' => array(
                'ProductBranch.product_id = Product.id',
                'ProductBranch.branch_id' => $branchId
        ));
        $joinProductgroup = array(
            'table' => 'product_pgroups',
            'type' => 'INNER',
            'alias' => 'ProductPgroup',
            'conditions' => array('ProductPgroup.product_id = Product.id')
        );
        $joinPgroup = array(
            'table' => 'pgroups',
            'type' => 'INNER',
            'alias' => 'Pgroup',
            'conditions' => array(
                'Pgroup.id = ProductPgroup.pgroup_id',
                '(Pgroup.user_apply = 0 OR (Pgroup.user_apply = 1 AND Pgroup.id IN (SELECT pgroup_id FROM user_pgroups WHERE user_id = ' . $user['User']['id'] . ')))'
            )
        );
        $joins = array(
            $joinProductgroup,
            $joinPgroup,
            $joinProductBranch
        );
        $product = ClassRegistry::init('Product')->find('first', array(
            'fields' => array(
                'Product.id',
                'Product.name',
                'Product.code',
                'Product.barcode',
                'Product.price_uom_id',
                'Product.is_packet',
                'Product.small_val_uom'
            ),
            'conditions' => array(
                array(
                    "OR" => array(
                        'trim(Product.code)' => trim($product_code),
                        'trim(Product.barcode)' => trim($product_code),
                        'trim(Product.chemical)' => trim($product_code)
                    )
                ), 'Product.is_active' => 1
                , 'Product.company_id' => $companyId
                , '((Product.price_uom_id IS NOT NULL AND Product.is_packet = 0) OR (Product.price_uom_id IS NULL AND Product.is_packet = 1))'
                , "((is_not_for_sale = 0 AND period_from IS NULL AND period_to IS NULL) OR (is_not_for_sale = 0 AND period_from <= '" . $order_date . "' AND period_to >= '" . $order_date . "') OR (is_not_for_sale = 1 AND period_from IS NOT NULL AND period_to IS NOT NULL AND '" . $order_date . "' NOT BETWEEN period_from AND period_to))"
            ),
            'joins' => $joins,
            'group' => array(
                'Product.id'
            )
        ));
        $this->set(compact('product', 'customerId'));

        $db = ConnectionManager::getDataSource('default');
        mysql_select_db($db->config['database']);
    }

    function service($companyId) {
        $this->layout = 'ajax';
        $sections = ClassRegistry::init('Section')->find("list", array("conditions" => array("Section.is_active != 2")));
        $services = $this->serviceCombo($companyId);
        $this->set(compact('sections', 'services'));
    }

    function serviceCombo($companyId) {
        $array = array();
        $services = ClassRegistry::init('Service')->find("all", array("conditions" => array("Service.company_id=" . $companyId . " AND Service.is_active != 2")));
        foreach ($services as $service) {
            array_push($array, array('value' => $service['Service']['id'], 'name' => $service['Service']['name'], 'class' => $service['Section']['id'], 'price' => $service['Service']['unit_price']));
        }
        return $array;
    }

    function miscellaneous() {
        $this->layout = 'ajax';
    }

    function tabMedia($id = null) {
        $this->layout = 'ajax';
        if (!empty($this->data)) {
            $file = $_FILES['file'];
            echo '{"name":"' . $file['name'] . '","type":"' . $file['type'] . '","size":"' . $file['size'] . '"}';
        }
    }

    function upload($id = null) {
        /**
         * include customize function
         */
        include('includes/function.php');

        if ($_FILES['file']['name'] != '') {
            $user = $this->getCurrentUser();

            $patientId = getPatientIdByQueueId($id);
            $patientCode = getPatientCode($patientId);

            $targetFolder = 'public/media/';
            $targetName = $patientCode . '_' . str_replace(' ', '_', $_FILES['file']['name']);
            move_uploaded_file($_FILES['file']['tmp_name'], $targetFolder . $targetName);

            mysql_query('INSERT INTO medias (patient_id,src,created,created_by,is_active) VALUES (' . $patientId . ',"' . $targetName . '",now(),"' . $user['User']['id'] . '",1)') or die(mysql_error());

            echo '{"name":"<a href=\"' . $this->webroot . 'public/media/' . $targetName . '\" target=\"_blank\">' . $targetName . '</a>","type":"' . $_FILES['file']['type'] . '","size":"' . $_FILES['file']['size'] . '"}';

            exit();
        }
    }

    function report() {
        
    }

    function reportResult() {
        $this->layout = 'ajax';
    }

    function reportAjax($datas = null) {
        $this->layout = 'ajax';
        $datas = explode(",", $datas);
        $this->set("datas", $datas);
        $user = $this->getCurrentUser();
        $this->set('userId', $user['User']['id']);
    }

    function dermatology() {
        
    }

    function dermatologyResult() {
        $this->layout = 'ajax';
    }

    function dermatologyAjax($datas = null) {
        $this->layout = 'ajax';
        $datas = explode(",", $datas);
        $this->set("datas", $datas);
        $user = $this->getCurrentUser();
        $this->set('userId', $user['User']['id']);
    }

    function tabDiagnosis($id = null) {
        $this->layout = 'ajax';
        $this->loadModel('DiagnosisItemRequest');
        $this->loadModel('DiagnosisItemRequestDetail');
        $isDone = $this->DiagnosisItemRequest->findByQueueId($id);
        if (!empty($isDone)) {
            echo GENERAL_DONE;
            exit();
        }
        if (!empty($this->data)) {
            $user = $this->getCurrentUser();
            $userId = $user['User']['id'];
            $queueId = $this->data['Queue']['id'];
            $this->DiagnosisItemRequest->create();
            $diagnosisItemRequestSave['DiagnosisItemRequest']['queue_id'] = $queueId;
            $diagnosisItemRequestSave['DiagnosisItemRequest']['diagnosis_location'] = $this->data['Diagnosis']['diagnosis_location'];
            $diagnosisItemRequestSave['DiagnosisItemRequest']['differential_diagnosis'] = $this->data['Diagnosis']['differential_diagnosis'];
            $diagnosisItemRequestSave['DiagnosisItemRequest']['created_by'] = $userId;
            if ($this->DiagnosisItemRequest->save($diagnosisItemRequestSave)) {
                $diagnosisItemRequestId = $this->DiagnosisItemRequest->getLastInsertId();
                foreach ($this->data['GroupDermatoItemId'] as $itemId) {
                    $this->DiagnosisItemRequestDetail->create();
                    $groupDermatoItemId['DiagnosisItemRequestDetail']['diagnosis_item_request_id'] = $diagnosisItemRequestId;
                    $groupDermatoItemId['DiagnosisItemRequestDetail']['group_dermato_item_id'] = $itemId;
                    $groupDermatoItemId['DiagnosisItemRequestDetail']['created_by'] = $userId;
                    $this->DiagnosisItemRequestDetail->save($groupDermatoItemId);
                }
                $this->Session->setFlash(__('Diagnosis Request has been saved', true), 'flash_success');
                $this->redirect(array('controller' => 'doctors', 'action' => 'consultation', $id));
            }
        }
        if (!empty($id)) {
            $qPatient = $this->Queue->getQueuePatientById($id);
            $this->set('qPatient', $qPatient);
            $this->set('qId', $id);
        }
    }

    function tabDiagnosisNum($id = null) {
        $this->layout = 'ajax';
        $this->loadModel('DiagnosisItemRequest');
        $this->loadModel('DiagnosisItemRequestDetail');
        if (!empty($this->data)) {
            $user = $this->getCurrentUser();
            $userId = $user['User']['id'];
            $queueId = $this->data['Queue']['id'];
            $this->DiagnosisItemRequestDetail->updateAll(
                    array('DiagnosisItemRequestDetail.status' => "2"), array('DiagnosisItemRequestDetail.diagnosis_item_request_id' => $this->data['DiagnosisItemRequest']['id'])
            );

            $diagnosisItemRequestSave['DiagnosisItemRequest']['id'] = $this->data['DiagnosisItemRequest']['id'];
            $diagnosisItemRequestSave['DiagnosisItemRequest']['diagnosis_location'] = $this->data['Diagnosis']['diagnosis_location'];
            $diagnosisItemRequestSave['DiagnosisItemRequest']['differential_diagnosis'] = $this->data['Diagnosis']['differential_diagnosis'];
            $diagnosisItemRequestSave['DiagnosisItemRequest']['modified_by'] = $userId;
            if ($this->DiagnosisItemRequest->save($diagnosisItemRequestSave)) {
                foreach ($this->data['GroupDermatoItemId'] as $itemId) {
                    $this->DiagnosisItemRequestDetail->create();
                    $groupDermatoItemId['DiagnosisItemRequestDetail']['diagnosis_item_request_id'] = $this->data['DiagnosisItemRequest']['id'];
                    $groupDermatoItemId['DiagnosisItemRequestDetail']['group_dermato_item_id'] = $itemId;
                    $groupDermatoItemId['DiagnosisItemRequestDetail']['created_by'] = $userId;
                    $this->DiagnosisItemRequestDetail->save($groupDermatoItemId);
                }
                $this->Session->setFlash(__('Blood Diagnosis Request has been saved', true), 'flash_success');
                $this->redirect(array('controller' => 'doctors', 'action' => 'consultation', $id));
            }
        }
        if (!empty($id)) {
            $qPatient = $this->Queue->getQueuePatientById($id);
            $this->set('qPatient', $qPatient);
            $this->set('qId', $id);
            $this->Queue->id = $id;
            $queueList = $this->Queue->find('list', array('conditions' => array('patient_id' => $this->Queue->field('patient_id'))));
            $this->set('diagnosisRequests', ClassRegistry::init('DiagnosisItemRequest')->find('all', array('conditions' => array('queue_id' => $queueList), 'order' => 'created DESC')));
        }
    }

    function laboTestRequest($queueId) {
        //$this->layout = 'ajax';
        $this->loadModel('LaboRequest');
        $this->loadModel('LaboItemGroup');
        $laboItemGroups = $this->LaboItemGroup->find('all', array('fields' => array('LaboItemGroup.id', 'LaboItemGroup.name'), 'conditions' => array('LaboItemGroup.is_active != 2')));
        $this->set(compact("laboItemGroups"));
        $this->set('queuedPatientId', $queueId);

        $this->loadModel('LaboTitleGroup');
        $laboTitleGroup = $this->LaboTitleGroup->find("all", array("conditions" => array("LaboTitleGroup.is_active!=2") , 'order' => 'LaboTitleGroup.ordering ASC'));    
        $this->set('laboTitleGroup', $laboTitleGroup);

        $this->loadModel('Labo');
        $labo = $this->Labo->getLaboByQueuedLaboId($queueId);
        $laboSelected = $this->LaboRequest->find('list', array('conditions' => array('LaboRequest.labo_id' => $labo['Labo']['id'], 'LaboRequest.is_active != 2'), 'fields' => array('LaboRequest.id', 'LaboRequest.labo_item_group_id')));

        $this->set(compact('labo', 'laboSelected'));
    }

    function tabOtherService($queueDoctorId = null, $queueId = null) {
        $this->layout = 'ajax';
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('EchoServiceRequest');
        $this->loadModel('XrayServiceRequest');
        $this->loadModel('MidWifeServiceRequest');
        $this->loadModel('CystoscopyServiceRequest');
        $this->loadModel('QueuedDoctor');
        $isDone = $this->OtherServiceRequest->find('list', array('conditions' => array('queued_doctor_id' => $queueDoctorId)));
        if (!empty($isDone)) {
            echo GENERAL_DONE;
            exit();
        }

        if (!empty($this->data)) {
            /**
             * include customize function
             */
            include('includes/function.php');
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
        $this->set(compact('patient'));
        $this->set('queueDoctorId', $queueDoctorId);
        $this->set('queueId', $queueId);
    }

    function tabOtherServiceNum($queueDoctorId = null, $queueId = null) {
        $this->layout = 'ajax';
        $this->loadModel('EchoServiceRequest');
        $this->loadModel('XrayServiceRequest');
        $this->loadModel('MidWifeServiceRequest');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('QueuedDoctor');
        $this->loadModel('CystoscopyServiceRequest');
        if (!empty($queueId)) {
            if ($queueId == "view") {
                //new update for show history patient
                $patientId = $queueDoctorId;
            } else {
                $patientResult = $this->Queue->find('first', array('conditions' => array('Queue.id' => $queueId)));
                $patientId = $patientResult['Queue']['patient_id'];
            }
            $others = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'OtherServiceRequest.is_active' => 1, 'Patient.id' => $patientId),
                'fields' => array('Queue.id, QeuedDoctor.id, OtherServiceRequest.*, XrayServiceRequest.*, EchoServiceRequest.*', 'MidWifeServiceRequest.*, CystoscopyServiceRequest.*, Patient.sex'),
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
                ),
                'group' => 'OtherServiceRequest.created',
                'order' => 'OtherServiceRequest.created ASC'
            ));
            $this->set(compact('others'));
        }
    }

    function editOtherService($patientOtherServiceId = null, $queueDoctorId = null, $queueId = null) {
        $this->layout = 'ajax';
        if (!empty($this->data)) {
            $result = array();
            $this->loadModel('EchoServiceRequest');
            $this->loadModel('XrayServiceRequest');
            $this->loadModel('MidWifeServiceRequest');
            $this->loadModel('CystoscopyServiceRequest');
            $this->loadModel('OtherServiceRequest');
            $this->loadModel('QueuedDoctor');
            $this->loadModel('OtherServiceRequestUpdate');
            $this->loadModel('EchoServiceRequestUpdate');
            $this->loadModel('XrayServiceRequestUpdate');
            $this->loadModel('CystoscopyServiceRequestUpdate');
            $this->loadModel('MidWifeServiceRequestUpdate');
            /**
             * include customize function
             */
            include('includes/function.php');
            $user = $this->getCurrentUser();
            $this->OtherServiceRequestUpdate->create();
            $this->data['OtherServiceRequestUpdate']['other_service_request_id'] = $this->data['OtherServiceRequest']['other_id'];
            $this->data['OtherServiceRequestUpdate']['queued_doctor_id'] = $queueDoctorId;
            $this->data['OtherServiceRequestUpdate']['created_by'] = $user['User']['id'];
            $this->OtherServiceRequestUpdate->save($this->data['OtherServiceRequestUpdate']);
            $this->data['OtherServiceRequest']['id'] = $this->data['OtherServiceRequest']['other_id'];
            $this->data['OtherServiceRequest']['queued_doctor_id'] = $queueDoctorId;
            $this->data['OtherServiceRequest']['modified_by'] = $user['User']['id'];
            if ($this->OtherServiceRequest->save($this->data['OtherServiceRequest'])) {
                if (!empty($this->data['OtherServiceRequest']['echo_description'])) {
                    $this->EchoServiceRequestUpdate->create();
                    $this->data['EchoServiceRequestUpdate']['other_service_request_id'] = $this->data['OtherServiceRequest']['other_id'];
                    $this->data['EchoServiceRequestUpdate']['echo_description'] = $this->data['OtherServiceRequest']['echo_description_old'];
                    $this->data['EchoServiceRequestUpdate']['created_by'] = $user['User']['id'];
                    if ($this->EchoServiceRequestUpdate->save($this->data['EchoServiceRequestUpdate'])) {
                        $this->data['EchoServiceRequest']['id'] = $this->data['OtherServiceRequest']['echo_id'];
                        $this->data['EchoServiceRequest']['echo_description'] = $this->data['OtherServiceRequest']['echo_description'];
                        $this->data['EchoServiceRequest']['modified_by'] = $user['User']['id'];
                        $this->EchoServiceRequest->save($this->data['EchoServiceRequest']);
                    }
                }

                if (!empty($this->data['OtherServiceRequest']['xray_description'])) {
                    $this->XrayServiceRequestUpdate->create();
                    $this->data['XrayServiceRequestUpdate']['other_service_request_id'] = $this->data['OtherServiceRequest']['other_id'];
                    $this->data['XrayServiceRequestUpdate']['xray_description'] = $this->data['OtherServiceRequest']['xray_description_old'];
                    $this->data['XrayServiceRequestUpdate']['created_by'] = $user['User']['id'];
                    if ($this->XrayServiceRequestUpdate->save($this->data['XrayServiceRequestUpdate'])) {
                        $this->data['XrayServiceRequest']['id'] = $this->data['OtherServiceRequest']['xray_id'];
                        $this->data['XrayServiceRequest']['xray_description'] = $this->data['OtherServiceRequest']['xray_description'];
                        $this->data['XrayServiceRequest']['modified_by'] = $user['User']['id'];
                        $this->XrayServiceRequest->save($this->data['XrayServiceRequest']);
                    }
                }

                if (!empty($this->data['OtherServiceRequest']['cystoscopy_description'])) {
                    $this->CystoscopyServiceRequestUpdate->create();
                    $this->data['CystoscopyServiceRequestUpdate']['other_service_request_id'] = $this->data['OtherServiceRequest']['other_id'];
                    $this->data['CystoscopyServiceRequestUpdate']['cystoscopy_description'] = $this->data['OtherServiceRequest']['cystoscopy_description_old'];
                    $this->data['CystoscopyServiceRequestUpdate']['created_by'] = $user['User']['id'];
                    if ($this->CystoscopyServiceRequestUpdate->save($this->data['CystoscopyServiceRequestUpdate'])) {
                        $this->data['CystoscopyServiceRequest']['id'] = $this->data['OtherServiceRequest']['cystoscopy_id'];
                        $this->data['CystoscopyServiceRequest']['cystoscopy_description'] = $this->data['OtherServiceRequest']['cystoscopy_description'];
                        $this->data['CystoscopyServiceRequest']['modified_by'] = $user['User']['id'];
                        $this->CystoscopyServiceRequest->save($this->data['CystoscopyServiceRequest']);
                    }
                }

                if (!empty($this->data['OtherServiceRequest']['mid_wife_description'])) {
                    $this->MidWifeServiceRequestUpdate->create();
                    $this->data['MidWifeServiceRequestUpdate']['other_service_request_id'] = $this->data['OtherServiceRequest']['other_id'];
                    $this->data['MidWifeServiceRequestUpdate']['mid_wife_description'] = $this->data['OtherServiceRequest']['mid_wife_description_old'];
                    $this->data['MidWifeServiceRequestUpdate']['created_by'] = $user['User']['id'];
                    if ($this->MidWifeServiceRequestUpdate->save($this->data['MidWifeServiceRequestUpdate'])) {
                        $this->data['MidWifeServiceRequest']['id'] = $this->data['OtherServiceRequest']['mid_wife_id'];
                        $this->data['MidWifeServiceRequest']['mid_wife_description'] = $this->data['OtherServiceRequest']['mid_wife_description'];
                        $this->data['MidWifeServiceRequest']['modified_by'] = $user['User']['id'];
                        $this->MidWifeServiceRequest->save($this->data['MidWifeServiceRequest']);
                    }
                }
                $result['patientConsultId'] = $patientOtherServiceId;
                $result['queueDoctorId'] = $queueDoctorId;
                $result['queueId'] = $queueId;
                echo json_encode($result);
                exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                exit;
            }
        }
    }

    function printOtherService($patientOtherServiceId = null, $queueDoctorId = null, $queueId = null) {
        $this->layout = 'ajax';
        if (!empty($patientOtherServiceId)) {
            $otherService = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'OtherServiceRequest.is_active' => 1, 'Queue.id' => $queueId, 'QeuedDoctor.id' => $queueDoctorId),
                'fields' => array('Queue.id, QeuedDoctor.id, OtherServiceRequest.*, EchoServiceRequest.echo_description, XrayServiceRequest.xray_description,MidWifeServiceRequest.mid_wife_description, Patient.*, CystoscopyServiceRequest.cystoscopy_description'),
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
            $this->set(compact('otherService'));
        }
    }

    function getRelativeUom($uomId = null, $uomSku = 'all', $productId = null) {
        $this->layout = 'ajax';
        $this->set('uomId', $uomId);
        $this->set('uomSku', $uomSku);
        $this->set('productId', $productId);
    }

    /*
     * Scan 
     * 
     */

    function tabScan($queueDoctorId = null, $queueId = null) {
        $this->layout = 'ajax';
        $this->loadModel('RequestScan');
        $this->loadModel('Scan');
        $isDone = $this->RequestScan->find('list', array('conditions' => array('queued_doctor_id' => $queueDoctorId)));
        if (!empty($isDone)) {
            echo GENERAL_DONE;
            exit();
        }

        if (!empty($this->data)) {
            $userLogin = $this->getCurrentUser();
            $userLoginId = $userLogin['User']['id'];
            $scan['RequestScan']['queue_id'] = $this->data['Queue']['id'];
            $scan['RequestScan']['queued_doctor_id'] = $this->data['QeuedDoctor']['id'];
            $scan['RequestScan']['request'] = $this->data['Scan']['request'];
            $scan['RequestScan']['created_by'] = $userLoginId;

            if ($this->RequestScan->save($scan)) {
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                exit;
            }
        }

        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'QeuedDoctor.status' => 1, 'Queue.id' => $queueId, 'QeuedDoctor.id' => $queueDoctorId),
            'fields' => array('Queue.id, QeuedDoctor.id, RequestScan.*'),
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
                array('table' => 'request_scans',
                    'alias' => 'RequestScan',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'RequestScan.queued_doctor_id = QeuedDoctor.id'
                    )
                )
        )));
        $this->set(compact('patient'));
        $this->set('queueDoctorId', $queueDoctorId);
        $this->set('queueId', $queueId);
    }

    function tabScanNum($queueDoctorId = null, $queueId = null) {
        $this->layout = 'ajax';
        $this->loadModel('RequestScan');
        $this->loadModel('Scan');
        if (!empty($queueId)) {
            if ($queueId == "view") {
                //new update for show history patient
                $patientId = $queueDoctorId;
            } else {
                $patientResult = $this->Queue->find('first', array('conditions' => array('Queue.id' => $queueId)));
                $patientId = $patientResult['Queue']['patient_id'];
            }

            $request_scans = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'RequestScan.is_active' => 1, 'Patient.id' => $patientId),
                'fields' => array('Queue.id, QeuedDoctor.id, RequestScan.*'),
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
                    array('table' => 'request_scans',
                        'alias' => 'RequestScan',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'RequestScan.queued_doctor_id = QeuedDoctor.id'
                        )
                    )
                ),
                'group' => 'RequestScan.created',
                'order' => 'RequestScan.created ASC'
            ));
            $this->set(compact('request_scans'));
        }
    }

    function editScan($requestScanId = null, $queueDoctorId = null, $queueId = null) {
        $this->layout = 'ajax';
        if (!empty($this->data)) {
            $result = array();
            $this->loadModel('RequestScan');
            $this->loadModel('Scan');
            $this->loadModel('QueuedDoctor');
            /**
             * include customize function
             */
            include('includes/function.php');
            $user = $this->getCurrentUser();
            /**
             * Step 1 : Shadow Request Scan
             */
            $this->RequestScan->updateAll(
                    array('RequestScan.is_active' => "2", 'RequestScan.modified_by' => $user['User']['id']), array('RequestScan.id' => $requestScanId)
            );
            /**
             * Step 2 : Create Request Scan
             */
            $this->RequestScan->create();
            $this->data['RequestScan']['queued_doctor_id'] = $queueDoctorId;
            $this->data['RequestScan']['queue_id'] = $queueId;
            $this->data['RequestScan']['created_by'] = $user['User']['id'];
            if ($this->RequestScan->save($this->data['RequestScan'])) {
                $result['requestScanId'] = $requestScanId;
                $result['queueDoctorId'] = $queueDoctorId;
                $result['queueId'] = $queueId;
                echo json_encode($result);
                exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                exit;
            }
        }
    }

    function printScan($patientOtherServiceId = null, $queueDoctorId = null, $queueId = null) {
        $this->layout = 'ajax';
        if (!empty($patientOtherServiceId)) {
            $otherService = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'OtherServiceRequest.is_active' => 1, 'Queue.id' => $queueId, 'QeuedDoctor.id' => $queueDoctorId),
                'fields' => array('Queue.id, QeuedDoctor.id, OtherServiceRequest.*, EchoServiceRequest.*, XrayServiceRequest.*,MidWifeServiceRequest.*, Patient.*'),
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
                    array('table' => 'mid_wife_service_requests',
                        'alias' => 'MidWifeServiceRequest',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'MidWifeServiceRequest.other_service_request_id = OtherServiceRequest.id'
                        )
                    )
            )));
            $this->set(compact('otherService'));
        }
    }

    function getExtension($str) {
        $i = strrpos($str, ".");
        if (!$i) {
            return "";
        }
        $l = strlen($str) - $i;
        $ext = substr($str, $i + 1, $l);
        return $ext;
    }

    // BOE 
    function getDiagnosticDescription($id = null) {
        $this->loadModel('Diagnostic');
        $diagonsticDescription = ClassRegistry::init('Diagnostic')->find('all', array('conditions' => array('is_active=1', 'id' => $id)));
        if (empty($diagonsticDescription)) {
            echo "null";
            exit;
        } else {
            echo $diagonsticDescription[0]["Diagnostic"]["description"];
            exit;
        }
    }

    function getExaminationDescription($id = null) {
        $this->loadModel('Examination');
        $examinationDescription = ClassRegistry::init('Examination')->find('all', array('conditions' => array('is_active=1', 'id' => $id)));
        if (empty($examinationDescription)) {
            echo "null";
            exit;
        } else {
            echo $examinationDescription[0]["Examination"]["description"];
            exit;
        }
    }

    function getChiefComplainDescription($id = null) {
        $this->loadModel('ChiefComplain');
        $chiefComplainDescription = ClassRegistry::init('ChiefComplain')->find('first', array('conditions' => array('is_active=1', 'id' => $id)));
        if (empty($chiefComplainDescription)) {
            echo "null";
            exit;
        } else {
            echo $chiefComplainDescription["ChiefComplain"]["description"];
            exit;
        }
    }

    function getMedicalHistory($id = null) {
        $this->loadModel('MedicalHistory');
        $medicalHistory = ClassRegistry::init('MedicalHistory')->find('first', array('conditions' => array('is_active=1', 'id' => $id)));
        if (empty($medicalHistory)) {
            echo "null";
            exit;
        } else {
            echo $medicalHistory["MedicalHistory"]["description"];
            exit;
        }
    }

    function getDoctorCommentDescription($id = null) {
        $this->loadModel('DoctorComment');
        $doctorCommentDescription = ClassRegistry::init('DoctorComment')->find('first', array('conditions' => array('is_active=1', 'id' => $id)));
        if (empty($doctorCommentDescription)) {
            echo "null";
            exit;
        } else {
            echo $doctorCommentDescription["DoctorComment"]["description"];
            exit;
        }
    }

    function getDailyClinicalReportDescription($id = null) {
        $this->loadModel('DailyClinicalReport');
        $dailyClinicalReportDescription = ClassRegistry::init('DailyClinicalReport')->find('first', array('conditions' => array('is_active=1', 'id' => $id)));
        if (empty($dailyClinicalReportDescription)) {
            echo "null";
            exit;
        } else {
            echo $dailyClinicalReportDescription["DailyClinicalReport"]["description"];
            exit;
        }
    }

    function tabConsultAndrology($queueDoctorId = null, $queueId = null) {
        $this->layout = 'ajax';
        $this->loadModel('PatientVitalSignBloodPressure');
        $this->loadModel('PatientVitalSign');
        $this->loadModel('PatientConsultation');
        $this->loadModel('QueuedDoctor');
        $this->loadModel('Examination');
        $this->loadModel('ChiefComplain');
        $this->loadModel('Diagnostic');
        //$this->loadModel('DoctorConsultation');
        $isDone = $this->PatientConsultation->find('list', array('conditions' => array('queued_doctor_id' => $queueDoctorId)));
        if (!empty($isDone)) {
            echo GENERAL_DONE;
            exit();
        }

        if (!empty($this->data)) {
            /**
             * include customize function
             */
            include('includes/function.php');
            $queueDoctorId = $this->data['QeuedDoctor']['id'];
            $user = $this->getCurrentUser();

            $this->PatientConsultation->create();
            $this->data['PatientConsultation']['consultation_code'] = $this->Helper->getAutoGenerateConsultationCode();
            $this->data['PatientConsultation']['queued_doctor_id'] = $this->data['QeuedDoctor']['id'];
            $this->data['PatientConsultation']['date_first_complaint'] = $this->data['PatientConsultation']['date_first_complaint_andrology'];
            $this->data['PatientConsultation']['date_of_consult'] = $this->data['PatientConsultation']['date_of_consult_andrology'];
            $this->data['PatientConsultation']['physical_examination_id'] = $this->data['PatientConsultation']['examination_andrology'];
            $this->data['PatientConsultation']['physical_examination'] = $this->data['PatientConsultation']['physical_examination_andrology'];
            $this->data['PatientConsultation']['daignostic_id'] = $this->data['PatientConsultation']['patient_diagnostic_andrology'];
            $this->data['PatientConsultation']['daignostic'] = $this->data['PatientConsultation']['daignostic_andrology'];
            $this->data['PatientConsultation']['chief_complain_id'] = $this->data['PatientConsultation']['complain_andrology'];
            $this->data['PatientConsultation']['chief_complain'] = $this->data['PatientConsultation']['chief_complain_andrology'];
            $this->data['PatientConsultation']['created_by'] = $user['User']['id'];
            $this->data['PatientConsultation']['consult_type'] = 2;
            if ($this->PatientConsultation->save($this->data['PatientConsultation'])) {
                /**
                 * Update Queue
                 */
                $Queue['Queue']['id'] = $this->data['Queue']['id'];
                $Queue['Queue']['status'] = 2; // ready to pay
                $Queue['Queue']['modified_by'] = $user['User']['id'];
                $this->Queue->save($Queue);

                /**
                 * Update Queue Doctor
                 */
                $QueueDoctor['QueuedDoctor']['id'] = $queueDoctorId;
                $QueueDoctor['QueuedDoctor']['status'] = 2; // ready to pay
                $QueueDoctor['QueuedDoctor']['doctor_id'] = $user['User']['id'];
                $QueueDoctor['QueuedDoctor']['modified_by'] = $user['User']['id'];
                $this->QueuedDoctor->save($QueueDoctor);

                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                exit;
            }
        }

        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'QeuedDoctor.status' => 1, 'Queue.id' => $queueId, 'QeuedDoctor.id' => $queueDoctorId),
            'fields' => array('Queue.id, QeuedDoctor.id, PatientVitalSign.*, PatientVitalSignBloodPressure.*'),
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
                )
        )));
        $doctorConsultationIds = ClassRegistry::init('DoctorConsultation')->find('list', array(
            'order' => array('DoctorConsultation.name ASC'),
            'conditions' => array('DoctorConsultation.is_active' => 1)
        ));
        $examinationAndrologies = ClassRegistry::init('Examination')->find('list', array('conditions' => array('is_active=1', 'type = 2')));
        $complainAndrologies = ClassRegistry::init('ChiefComplain')->find('list', array('conditions' => array('is_active=1', 'type = 2')));
        $patientDiagnosticAndrologies = ClassRegistry::init('Diagnostic')->find('list', array('conditions' => array('is_active=1', 'type = 2')));
        $this->set(compact('patient', 'doctorConsultationIds', 'examinationAndrologies', 'complainAndrologies', 'patientDiagnosticAndrologies'));
    }

    function tabConsultAndrologyNum($queueDoctorId = null, $queueId = null) {
        $this->layout = 'ajax';
        $this->loadModel('PatientVitalSignBloodPressure');
        $this->loadModel('PatientVitalSign');
        $this->loadModel('PatientConsultation');
        $this->loadModel('QueuedDoctor');
        if (!empty($queueId)) {

            if ($queueId == "view") {
                //new update for show patient consultation
                $patientId = $queueDoctorId;
            } else {
                $patientResult = $this->Queue->find('first', array('conditions' => array('Queue.id' => $queueId)));
                $patientId = $patientResult['Queue']['patient_id'];
            }

            $consultation = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'PatientConsultation.is_active' => 1, 'PatientConsultation.consult_type' => 2, 'QeuedDoctor.status >=1', 'Patient.id' => $patientId),
                'fields' => array('Queue.id, QeuedDoctor.id, PatientVitalSign.*, PatientVitalSignBloodPressure.*, PatientConsultation.*, QeuedLabo.id, DoctorConsultation.*'),
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
                    )
                ),
                'order' => 'PatientConsultation.consultation_code, PatientConsultation.created',
                'group' => 'PatientConsultation.consultation_code'
            ));
            $doctorConsultationIds = ClassRegistry::init('DoctorConsultation')->find('list', array(
                'order' => array('DoctorConsultation.name ASC'),
                'conditions' => array('DoctorConsultation.is_active' => 1)
            ));
            $this->set(compact('consultation', 'doctorConsultationIds'));
        }
    }

    function tabService($queueDoctorId = null, $queueId = null) {
        $this->layout = 'ajax';
        $this->loadModel('TmpService');
        $this->loadModel('TmpServiceDetail');
        $user = $this->getCurrentUser();
        $isDone = $this->TmpService->find('list', array('conditions' => array('queued_doctor_id' => $queueDoctorId)));
        $checkPaid = $this->Queue->find('list', array('conditions' => array('id' => $queueId, 'status' => 3)));
        if (!empty($isDone)) {
            echo GENERAL_DONE;
            exit();
        } else if (!empty($checkPaid)) {
            echo 'Patient checkout already!';
            exit();
        }
        
        // check patient ipd
        $this->loadModel('PatientIpd');
        $patientIPD =  $this->PatientIpd->find('first', array('conditions' => array('queued_doctor_id' => $queueDoctorId, 'is_active' => 1)));
        if (!empty($patientIPD)) {
            echo MESSAGE_PATIENT_ALREADY_ADMINISTRATION;
            exit();
        }
        
        if (!$queueId && empty($this->data)) {
            $this->Session->setFlash(__('Invalid patient', true), 'flash_failure');
            $this->redirect(array('action' => 'queue'));
        }

        if (empty($this->data)) {
            $this->loadModel('Section');
            $this->loadModel('Service');
            $this->loadModel('Company');
            $this->loadModel('User');
            ClassRegistry::init('Queue')->id = $queueId;
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
            $services = ClassRegistry::init('Service')->find('list', array('fields' => 'Service.id, Service.name', 'conditions' => array('Service.is_active=1'), 'order' => 'Service.name', 'recursive' => 0));

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
            $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'Queue.id' => $queueId),
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
                    ),
                    array('table' => 'queues',
                        'alias' => 'Queue',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Patient.id = Queue.patient_id'
                        )
                    )
            )));
            $branches = ClassRegistry::init('Branch')->find('all', array(
                'joins' => array(
                    array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id')),
                    array('table' => 'module_code_branches AS ModuleCodeBranch', 'type' => 'left', 'conditions' => array('ModuleCodeBranch.branch_id=Branch.id'))
                ),
                'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id', 'ModuleCodeBranch.so_code', 'Branch.currency_center_id', 'CurrencyCenter.symbol'),
                'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])
            ));
            $this->set(compact('sections', 'services', 'patient', 'companies', 'doctors', 'branches'));
        }

        if (!empty($this->data)) {
            $this->TmpService->create();
            $tmpService['TmpService']['exchange_rate_id'] = $this->data['Patient']['exchange_rate_id'];
            $tmpService['TmpService']['company_id'] = $this->data['Patient']['company_id'];
            $tmpService['TmpService']['branch_id'] = $this->data['Patient']['branch_id'];
            $tmpService['TmpService']['queue_id'] = $queueId;
            $tmpService['TmpService']['queued_doctor_id'] = $queueDoctorId;
            $tmpService['TmpService']['created_by'] = $user['User']['id'];
            if ($this->TmpService->save($tmpService['TmpService'])) {
                $tmpServiceId = $this->TmpService->getLastInsertId();
                for ($i = 0; $i < sizeof($this->data['Patient']['section_id']); $i++) {
                    if ($this->data['Patient']['section_id'][$i] != '') {
                        $this->TmpServiceDetail->create();
                        $tmpServiceDetail['TmpServiceDetail']['tmp_service_id'] = $tmpServiceId;
                        $tmpServiceDetail['TmpServiceDetail']['type'] = 1;
                        $tmpServiceDetail['TmpServiceDetail']['date_created'] = date('Y-m-d');
                        $tmpServiceDetail['TmpServiceDetail']['service_id'] = $this->data['Patient']['service_id'][$i];
                        $tmpServiceDetail['TmpServiceDetail']['doctor_id'] = $this->data['Patient']['doctor_id'][$i];
                        $tmpServiceDetail['TmpServiceDetail']['qty'] = $this->data['Patient']['qty'][$i];
                        if ($this->data['Patient']['discount'][$i] == "") {
                            $tmpServiceDetail['TmpServiceDetail']['discount'] = 0;
                        } else {
                            $tmpServiceDetail['TmpServiceDetail']['discount'] = $this->data['Patient']['discount'][$i];
                        }
                        $tmpServiceDetail['TmpServiceDetail']['unit_price'] = $this->data['Patient']['unit_price'][$i];
                        $tmpServiceDetail['TmpServiceDetail']['total_price'] = $this->data['Patient']['total_price'][$i];
                        $tmpServiceDetail['TmpServiceDetail']['created_by'] = $user['User']['id'];
                        $this->TmpServiceDetail->save($tmpServiceDetail['TmpServiceDetail']);
                    }
                }
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                exit;
            }
        }
    }

    function tabServiceNum($queueDoctorId = null, $queueId = null) {
        $this->layout = 'ajax';
        $this->loadModel('TmpService');
        $this->loadModel('Section');
        $this->loadModel('Service');
        $this->loadModel('Company');
        $this->loadModel('User');
        if (!empty($queueId)) {
            ClassRegistry::init('Queue')->id = $queueId;
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
            $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'Queue.id' => $queueId),
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
                    ),
                    array('table' => 'queues',
                        'alias' => 'Queue',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Patient.id = Queue.patient_id'
                        )
                    )
            )));
            $branches = ClassRegistry::init('Branch')->find('all', array(
                'joins' => array(
                    array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id')),
                    array('table' => 'module_code_branches AS ModuleCodeBranch', 'type' => 'left', 'conditions' => array('ModuleCodeBranch.branch_id=Branch.id'))
                ),
                'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id', 'ModuleCodeBranch.so_code', 'Branch.currency_center_id', 'CurrencyCenter.symbol'),
                'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])
            ));
            $patientResult = $this->Queue->find('first', array('conditions' => array('Queue.id' => $queueId)));
            $patientId = $patientResult['Queue']['patient_id'];

            $tmpService = $this->Patient->find('all', array('conditions' => array('Patient.is_active ' => 1, 'QeuedDoctor.status >=1', 'Queue.status <=3', 'TmpService.status >=' => 1, 'Patient.id' => $patientId),
                'fields' => array('Queue.id, QeuedDoctor.id , TmpService.*'),
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
                    array('table' => 'tmp_services',
                        'alias' => 'TmpService',
                        'type' => 'INNER',
                        'conditions' => array(
                            'TmpService.queue_id = Queue.id'
                        )
                    )
                ),
                'order' => 'TmpService.created DESC'
            ));

            $this->set(compact('tmpService', 'sections', 'services', 'patient', 'companies', 'doctors', 'branches'));
        }
    }

    function getService($sectionId = null) {
        $this->layout = 'ajax';
        $this->loadModel('Service');
        $services = ClassRegistry::init('Service')->find('all', array('fields' => 'Service.id, Service.name', 'conditions' => array('Service.is_active=1', 'Service.section_id' => $sectionId), 'order' => 'Service.name', 'recursive' => 0));
        $this->set(compact('services'));
    }

    function getServicePrice($id = null, $pateintGroup = null, $companyInsuranceId = null) {
        $this->layout = 'ajax';
        $this->loadModel('ServicesPatientGroupDetail');
        $this->loadModel('ServicesPriceInsurancePatientGroupDetail');
        $this->loadModel('ServicesPriceInsurance');
        if ($companyInsuranceId != "") {
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

    function editTmpService($tmpServiceId = null, $queuedDoctorId = null, $queueId = null) {
        $this->layout = 'ajax';
        if (!empty($this->data)) {
            $result = array();
            $this->loadModel('TmpService');
            $this->loadModel('TmpServiceDetail');
            /**
             * include customize function
             */
            include('includes/function.php');
            $user = $this->getCurrentUser();
            $this->TmpService->updateAll(
                    array('TmpService.status' => "0", 'TmpService.modified_by' => $user['User']['id']), array('TmpService.id' => $tmpServiceId)
            );
            $this->TmpService->create();
            $this->data['TmpService']['exchange_rate_id'] = $this->data['Patient']['exchange_rate_id'];
            $this->data['TmpService']['company_id'] = $this->data['Patient']['company_id'];
            $this->data['TmpService']['branch_id'] = $this->data['Patient']['branch_id'];
            $this->data['TmpService']['queue_id'] = $this->data['Queue']['id'];
            $this->data['TmpService']['queued_doctor_id'] = $this->data['QeuedDoctor']['id'];
            $this->data['TmpService']['created_by'] = $user['User']['id'];

            if ($this->TmpService->save($this->data['TmpService'])) {
                $tmpServiceId = $this->TmpService->getLastInsertId();

                for ($i = 0; $i < sizeof($this->data['Patient']['section_id']); $i++) {
                    if ($this->data['Patient']['section_id'][$i] != '') {
                        $this->TmpServiceDetail->create();
                        $tmpServiceDetail['TmpServiceDetail']['tmp_service_id'] = $tmpServiceId;
                        $tmpServiceDetail['TmpServiceDetail']['type'] = 1;
                        $tmpServiceDetail['TmpServiceDetail']['date_created'] = date('Y-m-d');
                        $tmpServiceDetail['TmpServiceDetail']['service_id'] = $this->data['Patient']['service_id'][$i];
                        $tmpServiceDetail['TmpServiceDetail']['doctor_id'] = $this->data['Patient']['doctor_id'][$i];
                        $tmpServiceDetail['TmpServiceDetail']['qty'] = $this->data['Patient']['qty'][$i];
                        if ($this->data['Patient']['discount'][$i] == "") {
                            $tmpServiceDetail['TmpServiceDetail']['discount'] = 0;
                        } else {
                            $tmpServiceDetail['TmpServiceDetail']['discount'] = $this->data['Patient']['discount'][$i];
                        }
                        $tmpServiceDetail['TmpServiceDetail']['unit_price'] = $this->data['Patient']['unit_price'][$i];
                        $tmpServiceDetail['TmpServiceDetail']['total_price'] = $this->data['Patient']['total_price'][$i];
                        $tmpServiceDetail['TmpServiceDetail']['created_by'] = $user['User']['id'];
                        $this->TmpServiceDetail->save($tmpServiceDetail['TmpServiceDetail']);
                    }
                }
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                exit();
            }
        } else {
            echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
            exit();
        }
        exit();
    }

}

?>