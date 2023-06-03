<?php
class QueuedPatient extends AppModel {
    var $name = 'QueuedPatient';
    var $belongsTo = array(
            'Patient' => array(
                            'className' => 'Patient',
                            'foreignKey' => 'patient_id',
                            'conditions' => '',
                            'fields' => '',
                            'order' => ''
            )
    );

    function beforeFind($conditions) {
        return $conditions;
    }


    function getQueuedPatientById($queuedPatientId) {
        $conditions = array(
                'conditions'=>array('QueuedPatient.id'=>$queuedPatientId, "DAY(QueuedPatient.created) = DAY(NOW()) AND MONTH(QueuedPatient.created) = MONTH(NOW()) AND YEAR(QueuedPatient.created) = YEAR(NOW()) ")
        );
        $quPatient = $this->find('first',$conditions);
        return $quPatient;
    }

    function getConsultationByPatientById($qPatient) {
        $conditions=array('conditions'=>array('QueuedPatient.id'=>$qPatient),
                'fields'=>'*',
                'joins'=>array(array('table'=>'consultations',
                                'alias'=>'Consultation',
                                'conditions'=>array('QueuedPatient.id = Consultation.queued_patient_id'),
                                'order'=>'QueuedPatient.id DSCE')));
        $consultationPatient = $this->find('first',$conditions);
        return $consultationPatient;
    }
    function getHistoryPatient($qpid) {
        $conditions =array('conditions'=>array('QueuedPatient.patient_id'=>$qpid));
        $history = $this->find('all',$conditions);
        return $history;
    }
    function getHistoryParaclinic($qpid,$para_type_ids) {
        $in = $para_type_ids;
        if('others'==$para_type_ids) {
            $in = '7,8,9,10,11,12';
        }
        if('echos' == $para_type_ids) {
            $in = '4,5,6';
        }
        $conditions =array('conditions'=>array('QueuedPatient.id'=>$qpid),
                'fields'=>'*',
                'joins'=>array(array('table'=>'exam_paraclinic_types',
                                'alias'=>'ExamParaclinicType',
                                'conditions'=>array('QueuedPatient.id = ExamParaclinicType.queued_patient_id',
                                        'ExamParaclinicType.paraclinic_type_id IN('.$in.')'
                                )
                        )
                )
        );
        $history = $this->find('all',$conditions);
        return $history;
    }
    function getHistoryLabo($qpid) {
        $conditions=array('conditions'=>array('QueuedPatient.id'=>$qpid),
                'fields'=>'*',
                'joins'=>array(array('table'=>'labos',
                                'alias'=>'Labo',
                                'conditions'=>array('QueuedPatient.id = Labo.queued_patient_id'),
                                'order'=>'QueuedPatient.id DSCE')));
        $labos = $this->find('first',$conditions);
        return $labos;
    }
    function getTreatmentById($qpid) {
        $conditions=array('conditions'=>array('QueuedPatient.id'=>$qpid),
                'fields'=>'Treatment.*',
                'joins'=>array(array('table'=>'treatments',
                                'alias'=>'Treatment',
                                'conditions'=>array('QueuedPatient.id = Treatment.queued_patient_id', "DAY(QueuedPatient.created) = DAY(NOW()) AND MONTH(QueuedPatient.created) = MONTH(NOW()) AND YEAR(QueuedPatient.created) = YEAR(NOW()) "),
                                'order'=>'QueuedPatient.id DSCE')));
        $treatments = $this->find('first',$conditions);
        return $treatments;
    }

    function getQueuedPatientByKeyWord($keywords) {
        $conditions = array(
                "OR"=>array
                (
                        "Patient.patient_code like"=> "%".$keywords."%"
                        ,"Patient.patient_name like" => "%".$keywords."%"                        
                        ,"Patient.sex" => $keywords
                        ,"Patient.phn like"=> "%".$keywords."%"
                )
        );
        $listQPatient = $this->find('all',array('conditions'=>$conditions));
        $qPatientStr = '';
        foreach($listQPatient as $qPatinet) {
            if(!empty ($qPatientStr)) {
                $qPatientStr .= ','.$qPatinet['QueuedPatient']['id'];
            }else {
                $qPatientStr .= $qPatinet['QueuedPatient']['id'];
            }
        }
        return $qPatientStr;
    }

    function getQueuePatientByPassDoctor($queueExamField,$paraclinicTypeId) {
        $conditions = array(
                'fields'=>array('*')
                ,'recursive' => 2
                ,'conditions'=>array(
                        'QueuedPatient.'.$queueExamField=>1, "DAY(QueuedPatient.created) = DAY(NOW()) AND MONTH(QueuedPatient.created) = MONTH(NOW()) AND YEAR(QueuedPatient.created) = YEAR(NOW()) "
                //'ExamParaclinicType.status'=>1
                //,'ExamParaclinicType.paraclinic_type_id IN('.$paraclinicTypeId.')'
                )
        );
        $consultationPatient = $this->find('all',$conditions);
        return $consultationPatient;
    }

    function getQLaboByPassDoctor() {
        $conditions = array(
                'conditions'=>array('QueuedPatient.in_queue_labo'=>1,'QueuedPatient.in_queue'=>0, "DAY(QueuedPatient.created) = DAY(NOW()) AND MONTH(QueuedPatient.created) = MONTH(NOW()) AND YEAR(QueuedPatient.created) = YEAR(NOW()) ")
                ,'recursive' =>2
        );
        return $this->find('all', $conditions);
    }


    function getPatientByCampany() {
        $condition = array(
                'conditions'=>array('Patient.company IS NOT NULL')
        );
        $patientArr = $this->find('all',$condition);
        return $patientArr;
    }
    function getPatientByInsurance() {
        $condition = array(
                'conditions'=>array('Patient.insurance_id IS NOT NULL'
                        ,'date(QueuedPatient.created)'=>date('Y-m-d')
                )
        );
        $patientArr = $this->find('all',$condition);
        return $patientArr;
    }
    function getPatientByPersonal() {
        $condition = array(
                'conditions'=>array('Patient.insurance_id IS NULL','Patient.company IS NULL')
        );
        $patientArr = $this->find('all',$condition);
        return $patientArr;
    }
}
?>