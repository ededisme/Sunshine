<?php
class QueuedLabo extends AppModel {
    var $name = 'QueuedLabo';
    var $belongsTo = array(            
            'Queue' => array(
                            'className' => 'Queue',
                            'foreignKey' => 'queue_id',
                            'conditions' => '',
                            'fields' => '',
                            'order' => ''
            ),
            'Patient' => array(
                            'className' => 'Patient',
                            'foreignKey' => 'id',
                            'conditions' => array('Patient.is_active != 2'),
                            'fields' => '',
                            'order' => ''
            )
    );
    var $hasMany = array(
            'Patient' => array(
                            'className' => 'Patient',
                            'foreignKey' => 'id',
                            'conditions' => array('Patient.is_active != 2'),
                            'fields' => '',
                            'order' => '')
    );

    function beforeFind($conditions) {
        return $conditions;
    }


    function getQueuedLaboById($QueuedLaboId) {
        $conditions = array(
                'conditions'=>array('QueuedLabo.id'=>$QueuedLaboId, "DAY(QueuedLabo.created) = DAY(NOW()) AND MONTH(QueuedLabo.created) = MONTH(NOW()) AND YEAR(QueuedLabo.created) = YEAR(NOW()) ")
        );
        $quPatient = $this->find('first',$conditions);
        return $quPatient;
    }

    function getConsultationByPatientById($qPatient) {
        $conditions=array('conditions'=>array('QueuedLabo.id'=>$qPatient),
                'fields'=>'*',
                'joins'=>array(array('table'=>'consultations',
                                'alias'=>'Consultation',
                                'conditions'=>array('QueuedLabo.id = Consultation.queued_id'),
                                'order'=>'QueuedLabo.id DSCE')));
        $consultationPatient = $this->find('first',$conditions);
        return $consultationPatient;
    }
    function getHistoryPatient($qpid) {
        $conditions =array('conditions'=>array('QueuedLabo.queue_id'=>$qpid));
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
        $conditions =array('conditions'=>array('QueuedLabo.id'=>$qpid),
                'fields'=>'*',
                'joins'=>array(array('table'=>'exam_paraclinic_types',
                                'alias'=>'ExamParaclinicType',
                                'conditions'=>array('QueuedLabo.id = ExamParaclinicType.queued_id',
                                        'ExamParaclinicType.paraclinic_type_id IN('.$in.')'
                                )
                        )
                )
        );
        $history = $this->find('all',$conditions);
        return $history;
    }
    function getHistoryLabo($qpid) {
        $conditions=array('conditions'=>array('QueuedLabo.id'=>$qpid),
                'fields'=>'*',
                'joins'=>array(array('table'=>'labos',
                                'alias'=>'Labo',
                                'conditions'=>array('QueuedLabo.id = Labo.queued_id'),
                                'order'=>'QueuedLabo.id DSCE')));
        $labos = $this->find('first',$conditions);
        return $labos;
    }
    function getTreatmentById($qpid) {
        $conditions=array('conditions'=>array('QueuedLabo.id'=>$qpid),
                'fields'=>'Treatment.*',
                'joins'=>array(array('table'=>'treatments',
                                'alias'=>'Treatment',
                                'conditions'=>array('QueuedLabo.id = Treatment.queued_id', "DAY(QueuedLabo.created) = DAY(NOW()) AND MONTH(QueuedLabo.created) = MONTH(NOW()) AND YEAR(QueuedLabo.created) = YEAR(NOW()) "),
                                'order'=>'QueuedLabo.id DSCE')));
        $treatments = $this->find('first',$conditions);
        return $treatments;
    }

    function getQueuedLaboByKeyWord($keywords) {
        $conditions = array(
                "OR"=>array
                (
                        "Patient.patient_code like"=> "%".$keywords."%"
                        ,"Patient.patient_name like" => "%".$keywords."%"                        
                        ,"Patient.sex" => $keywords
                        ,"Patient.telephone like"=> "%".$keywords."%"
                )
        );
        $listQPatient = $this->find('all',array('conditions'=>$conditions));
        $qPatientStr = '';
        foreach($listQPatient as $qPatinet) {
            if(!empty ($qPatientStr)) {
                $qPatientStr .= ','.$qPatinet['QueuedLabo']['id'];
            }else {
                $qPatientStr .= $qPatinet['QueuedLabo']['id'];
            }
        }
        return $qPatientStr;
    }

    function getQueuePatientByPassDoctor($queueExamField,$paraclinicTypeId) {
        $conditions = array(
                'fields'=>array('*')
                ,'recursive' => 2
                ,'conditions'=>array(
                        'QueuedLabo.'.$queueExamField=>1, "DAY(QueuedLabo.created) = DAY(NOW()) AND MONTH(QueuedLabo.created) = MONTH(NOW()) AND YEAR(QueuedLabo.created) = YEAR(NOW()) "
                //'ExamParaclinicType.status'=>1
                //,'ExamParaclinicType.paraclinic_type_id IN('.$paraclinicTypeId.')'
                )
        );
        $consultationPatient = $this->find('all',$conditions);
        return $consultationPatient;
    }

    function getQLaboByPassDoctor() {
        $conditions = array(
                'conditions'=>array('QueuedLabo.status'=>1)
                ,'recursive' =>2
        );
        return $this->find('all', $conditions);
    }
    
//    function getQLaboByPassDoctor() {
//        $conditions = array(
//                'conditions'=>array('QueuedLabo.status'=>1, "DAY(QueuedLabo.created) = DAY(NOW()) AND MONTH(QueuedLabo.created) = MONTH(NOW()) AND YEAR(QueuedLabo.created) = YEAR(NOW()) "),
//                'fields'=>'*',
//                'joins'=>array(
//                    array('table'=>'queues',
//                                'alias'=>'Queued',
//                                'type' => 'LEFT',
//                                'conditions'=>array('Queued.id = QueuedLabo.queue_id'),
//                    ),
//                    array('table'=>'patients',
//                                'alias'=>'Patient',
//                                'type' => 'LEFT',
//                                'conditions'=>array('Patient.id = Queued.patient_id'),
//                    )
//                )            
//                ,'recursive' =>2
//        );
//        return $this->find('all', $conditions);
//    }
    


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
                        ,'date(QueuedLabo.created)'=>date('Y-m-d')
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