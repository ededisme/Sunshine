<?php

class CystoscopyServicesController extends AppController {

    var $name = 'CystoscopyServices';
    var $uses = array('Patient', 'User');
    var $components = array('Helper');

    function index() {
        $this->layout = 'ajax';
    }

    function ajax() {
        $this->layout = 'ajax';
    }

    function cystoscopyServiceDoctorAjax() {
        $this->layout = 'ajax';
    }

    function cystoscopyServiceDoctor() {
        $this->layout = 'ajax';
    }

    function view($id = null) {
        $this->layout = 'ajax';
        if (!empty($id)) {
            $dataService = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'CystoscopyService.is_active' => 1, 'CystoscopyService.id' => $id),
                'fields' => array('Queue.id, CystoscopyService.*, Patient.*'),
                'joins' => array(
                    array('table' => 'queues',
                        'alias' => 'Queue',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Queue.patient_id  = Patient.id'
                        )
                    ),
                    array('table' => 'xray_services',
                        'alias' => 'CystoscopyService',
                        'type' => 'INNER',
                        'conditions' => array(
                            'CystoscopyService.xray_service_queue_id = Queue.id'
                        )
                    )
            )));
            $this->set(compact('dataService'));
        }
    }

    function edit($id = null) {
        $this->layout = 'ajax';
        $this->loadModel('QueuedDoctor');
        $this->loadModel('CystoscopyService');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('CystoscopyServiceRequest');
        $result = array();
        if (!empty($this->data)) {
            $user = $this->getCurrentUser();
            $dataHistory = $this->CystoscopyService->find('first', array('conditions' => array('id' => $this->data['CystoscopyService']['id'])));
            
            $this->CystoscopyService->updateAll(
                    array('CystoscopyService.is_active' => "2", 'CystoscopyService.modified_by' => $user['User']['id']), array('CystoscopyService.id' => $this->data['CystoscopyService']['id'])
            );
            $this->CystoscopyService->create();
            $valid_formats = array("jpg", "png", "gif", "bmp", "jpeg");
            $uploaddir = "img/cystoscopy/"; //a directory inside
            $urethraImg = "";
            $prostateImg = "";
            $bladderNeckImg = "";
            $bladderImg = "";
            $afterFiveMinuteImg = "";
            if (!empty($_FILES['urethra_img']['name'])) {
                $filename = stripslashes($_FILES['urethra_img']['name']);
                $size = filesize($_FILES['urethra_img']['tmp_name']);
                //get the extension of the file in a lower case format
                $ext = $this->getExtension($filename);
                $ext = strtolower($ext);
                if (in_array($ext, $valid_formats)) {
                    if ($size < (9000 * 1024)) {
                        $image_name = 'urethra' . time() . $filename;
                        $newname = $uploaddir . $image_name;
                        if (move_uploaded_file($_FILES['urethra_img']['tmp_name'], $newname)) {
                            $time = time();
                            $urethraImg = $image_name;
                        } else {
                            $urethraImg = "";
                        }
                    }
                }
            }else{
                $urethraImg = $dataHistory['CystoscopyService']['urethra_img'];
            }
            if (!empty($_FILES['prostate_img']['name'])) {
                $filename = stripslashes($_FILES['prostate_img']['name']);
                $size = filesize($_FILES['prostate_img']['tmp_name']);
                //get the extension of the file in a lower case format
                $ext = $this->getExtension($filename);
                $ext = strtolower($ext);
                if (in_array($ext, $valid_formats)) {
                    if ($size < (9000 * 1024)) {
                        $image_name = 'prostate' . time() . $filename;
                        $newname = $uploaddir . $image_name;
                        if (move_uploaded_file($_FILES['prostate_img']['tmp_name'], $newname)) {
                            $time = time();
                            $prostateImg = $image_name;
                        } else {
                            $prostateImg = "";
                        }
                    }
                }
            }else{
                $prostateImg = $dataHistory['CystoscopyService']['prostate_img'];
            }
            if (!empty($_FILES['bladder_neck_img']['name'])) {
                $filename = stripslashes($_FILES['bladder_neck_img']['name']);
                $size = filesize($_FILES['bladder_neck_img']['tmp_name']);
                //get the extension of the file in a lower case format
                $ext = $this->getExtension($filename);
                $ext = strtolower($ext);
                if (in_array($ext, $valid_formats)) {
                    if ($size < (9000 * 1024)) {
                        $image_name = 'bladder_neck' . time() . $filename;
                        $newname = $uploaddir . $image_name;
                        if (move_uploaded_file($_FILES['bladder_neck_img']['tmp_name'], $newname)) {
                            $time = time();
                            $bladderNeckImg = $image_name;
                        } else {
                            $bladderNeckImg = "";
                        }
                    }
                }
            }else{
                $bladderNeckImg = $dataHistory['CystoscopyService']['bladder_neck_img'];
            }
            if (!empty($_FILES['bladder_img']['name'])) {
                $filename = stripslashes($_FILES['bladder_img']['name']);
                $size = filesize($_FILES['bladder_img']['tmp_name']);
                //get the extension of the file in a lower case format
                $ext = $this->getExtension($filename);
                $ext = strtolower($ext);
                if (in_array($ext, $valid_formats)) {
                    if ($size < (9000 * 1024)) {
                        $image_name = 'bladder' . time() . $filename;
                        $newname = $uploaddir . $image_name;
                        if (move_uploaded_file($_FILES['bladder_img']['tmp_name'], $newname)) {
                            $time = time();
                            $bladderImg = $image_name;
                        } else {
                            $bladderImg = "";
                        }
                    }
                }
            }else{
                $bladderImg = $dataHistory['CystoscopyService']['bladder_img'];
            }
            if (!empty($_FILES['after_five_minute_img']['name'])) {
                $filename = stripslashes($_FILES['after_five_minute_img']['name']);
                $size = filesize($_FILES['after_five_minute_img']['tmp_name']);
                //get the extension of the file in a lower case format
                $ext = $this->getExtension($filename);
                $ext = strtolower($ext);
                if (in_array($ext, $valid_formats)) {
                    if ($size < (9000 * 1024)) {
                        $image_name = 'after_five_minute' . time() . $filename;
                        $newname = $uploaddir . $image_name;
                        if (move_uploaded_file($_FILES['after_five_minute_img']['tmp_name'], $newname)) {
                            $time = time();
                            $afterFiveMinuteImg = $image_name;
                        } else {
                            $afterFiveMinuteImg = "";
                        }
                    }
                }
            }else{
                $afterFiveMinuteImg = $dataHistory['CystoscopyService']['after_five_minute_img'];
            }
            $data['CystoscopyService']['cystoscopy_service_request_id'] = $dataHistory['CystoscopyService']['cystoscopy_service_request_id'];
            $data['CystoscopyService']['cystoscopy_service_queue_id'] = $dataHistory['CystoscopyService']['cystoscopy_service_queue_id'];
            $data['CystoscopyService']['urethra_img'] = $urethraImg;
            $data['CystoscopyService']['urethra'] = $this->data['CystoscopyService']['urethra'];
            $data['CystoscopyService']['prostate_img'] = $prostateImg;
            $data['CystoscopyService']['prostate'] = $this->data['CystoscopyService']['prostate'];
            $data['CystoscopyService']['bladder_neck_img'] = $bladderNeckImg;
            $data['CystoscopyService']['bladder_neck'] = $this->data['CystoscopyService']['bladder_neck'];
            $data['CystoscopyService']['bladder_img'] = $bladderImg;
            $data['CystoscopyService']['bladder'] = $this->data['CystoscopyService']['bladder'];
            $data['CystoscopyService']['after_five_minute_img'] = $afterFiveMinuteImg;
            $data['CystoscopyService']['after_five_minute'] = $this->data['CystoscopyService']['after_five_minute'];
            $data['CystoscopyService']['descript_before_sdate'] = $this->data['CystoscopyService']['descript_before_sdate'];
            $data['CystoscopyService']['start_date'] = $this->data['CystoscopyService']['start_date'];
            $data['CystoscopyService']['end_date'] = $this->data['CystoscopyService']['end_date'];
            $data['CystoscopyService']['conclusion'] = $this->data['CystoscopyService']['conclusion'];
            $data['CystoscopyService']['created_by'] = $user['User']['id'];
            if ($this->CystoscopyService->save($data)) {
                $dataServiceId = $this->CystoscopyService->getLastInsertId();
                $result['id'] = $dataServiceId;
                $result['code'] = 0;
                echo json_encode($result);
                exit;
            } else {
                $result['code'] = 1;
                echo json_encode($result);
                exit;
            }
        }

        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'CystoscopyService.is_active' => 1, 'CystoscopyService.id' => $id),
            'fields' => array('Queue.id, CystoscopyService.*, Patient.*'),
            'joins' => array(
                array('table' => 'queues',
                    'alias' => 'Queue',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Queue.patient_id  = Patient.id'
                    )
                ),
                array('table' => 'cystoscopy_services',
                    'alias' => 'CystoscopyService',
                    'type' => 'INNER',
                    'conditions' => array(
                        'CystoscopyService.cystoscopy_service_queue_id = Queue.id'
                    )
                )
        )));
        
        $this->set(compact('patient'));
    }

    function addCystoscopyServiceDoctor($qDoctorId = null, $queueId = null) {
        $this->layout = 'ajax';
        $this->loadModel('QueuedDoctor');
        $this->loadModel('CystoscopyService');
        $this->loadModel('OtherServiceRequest');
        $this->loadModel('CystoscopyServiceRequest');
        $result = array();
        if (!empty($this->data)) {
            $this->CystoscopyService->create();
            $user = $this->getCurrentUser();
            $valid_formats = array("jpg", "png", "gif", "bmp", "jpeg");
            $uploaddir = "img/cystoscopy/"; //a directory inside
            $urethraImg = "";
            $prostateImg = "";
            $bladderNeckImg = "";
            $bladderImg = "";
            $afterFiveMinuteImg = "";
            if (!empty($_FILES['urethra_img']['name'])) {
                $filename = stripslashes($_FILES['urethra_img']['name']);
                $size = filesize($_FILES['urethra_img']['tmp_name']);
                //get the extension of the file in a lower case format
                $ext = $this->getExtension($filename);
                $ext = strtolower($ext);
                if (in_array($ext, $valid_formats)) {
                    if ($size < (9000 * 1024)) {
                        $image_name = 'urethra' . time() . $filename;
                        $newname = $uploaddir . $image_name;
                        if (move_uploaded_file($_FILES['urethra_img']['tmp_name'], $newname)) {
                            $time = time();
                            $urethraImg = $image_name;
                        } else {
                            $urethraImg = "";
                        }
                    }
                }
            }
            if (!empty($_FILES['prostate_img']['name'])) {
                $filename = stripslashes($_FILES['prostate_img']['name']);
                $size = filesize($_FILES['prostate_img']['tmp_name']);
                //get the extension of the file in a lower case format
                $ext = $this->getExtension($filename);
                $ext = strtolower($ext);
                if (in_array($ext, $valid_formats)) {
                    if ($size < (9000 * 1024)) {
                        $image_name = 'prostate' . time() . $filename;
                        $newname = $uploaddir . $image_name;
                        if (move_uploaded_file($_FILES['prostate_img']['tmp_name'], $newname)) {
                            $time = time();
                            $prostateImg = $image_name;
                        } else {
                            $prostateImg = "";
                        }
                    }
                }
            }
            if (!empty($_FILES['bladder_neck_img']['name'])) {
                $filename = stripslashes($_FILES['bladder_neck_img']['name']);
                $size = filesize($_FILES['bladder_neck_img']['tmp_name']);
                //get the extension of the file in a lower case format
                $ext = $this->getExtension($filename);
                $ext = strtolower($ext);
                if (in_array($ext, $valid_formats)) {
                    if ($size < (9000 * 1024)) {
                        $image_name = 'bladder_neck' . time() . $filename;
                        $newname = $uploaddir . $image_name;
                        if (move_uploaded_file($_FILES['bladder_neck_img']['tmp_name'], $newname)) {
                            $time = time();
                            $bladderNeckImg = $image_name;
                        } else {
                            $bladderNeckImg = "";
                        }
                    }
                }
            }
            if (!empty($_FILES['bladder_img']['name'])) {
                $filename = stripslashes($_FILES['bladder_img']['name']);
                $size = filesize($_FILES['bladder_img']['tmp_name']);
                //get the extension of the file in a lower case format
                $ext = $this->getExtension($filename);
                $ext = strtolower($ext);
                if (in_array($ext, $valid_formats)) {
                    if ($size < (9000 * 1024)) {
                        $image_name = 'bladder' . time() . $filename;
                        $newname = $uploaddir . $image_name;
                        if (move_uploaded_file($_FILES['bladder_img']['tmp_name'], $newname)) {
                            $time = time();
                            $bladderImg = $image_name;
                        } else {
                            $bladderImg = "";
                        }
                    }
                }
            }
            if (!empty($_FILES['after_five_minute_img']['name'])) {
                $filename = stripslashes($_FILES['after_five_minute_img']['name']);
                $size = filesize($_FILES['after_five_minute_img']['tmp_name']);
                //get the extension of the file in a lower case format
                $ext = $this->getExtension($filename);
                $ext = strtolower($ext);
                if (in_array($ext, $valid_formats)) {
                    if ($size < (9000 * 1024)) {
                        $image_name = 'after_five_minute' . time() . $filename;
                        $newname = $uploaddir . $image_name;
                        if (move_uploaded_file($_FILES['after_five_minute_img']['tmp_name'], $newname)) {
                            $time = time();
                            $afterFiveMinuteImg = $image_name;
                        } else {
                            $afterFiveMinuteImg = "";
                        }
                    }
                }
            }
            $data['CystoscopyService']['cystoscopy_service_request_id'] = $this->data['CystoscopyServiceRequest']['id'];
            $data['CystoscopyService']['cystoscopy_service_queue_id'] = $this->data['Queue']['id'];
            $data['CystoscopyService']['urethra_img'] = $urethraImg;
            $data['CystoscopyService']['urethra'] = $this->data['CystoscopyService']['urethra'];
            $data['CystoscopyService']['prostate_img'] = $prostateImg;
            $data['CystoscopyService']['prostate'] = $this->data['CystoscopyService']['prostate'];
            $data['CystoscopyService']['bladder_neck_img'] = $bladderNeckImg;
            $data['CystoscopyService']['bladder_neck'] = $this->data['CystoscopyService']['bladder_neck'];
            $data['CystoscopyService']['bladder_img'] = $bladderImg;
            $data['CystoscopyService']['bladder'] = $this->data['CystoscopyService']['bladder'];
            $data['CystoscopyService']['after_five_minute_img'] = $afterFiveMinuteImg;
            $data['CystoscopyService']['after_five_minute'] = $this->data['CystoscopyService']['after_five_minute'];
            $data['CystoscopyService']['descript_before_sdate'] = $this->data['CystoscopyService']['descript_before_sdate'];
            $data['CystoscopyService']['start_date'] = $this->data['CystoscopyService']['start_date'];
            $data['CystoscopyService']['end_date'] = $this->data['CystoscopyService']['end_date'];
            $data['CystoscopyService']['conclusion'] = $this->data['CystoscopyService']['conclusion'];
            $data['CystoscopyService']['created_by'] = $user['User']['id'];
            if ($this->CystoscopyService->save($data)) {
                $dataServiceId = $this->CystoscopyService->getLastInsertId();
                // get last insert id
                $result['id'] = $dataServiceId;
                $result['code'] = 0;
                $serReq['CystoscopyServiceRequest']['id'] = $this->data['CystoscopyServiceRequest']['id'];
                $serReq['CystoscopyServiceRequest']['is_active'] = 2;
                $this->CystoscopyServiceRequest->save($serReq);
                $queueId = $this->data['Queue']['id'];
                echo json_encode($result);
                exit;
            } else {
                $result['code'] = 1;
                echo json_encode($result);
                exit;
            }
        }

        $patient = $this->Patient->find('first', array('conditions' => array('Patient.is_active' => 1, 'OtherServiceRequest.is_active' => 1, 'Queue.id' => $queueId, 'QeuedDoctor.id' => $qDoctorId),
            'fields' => array('Queue.id, QeuedDoctor.id, CystoscopyServiceRequest.*,Patient.*'),
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
                array('table' => 'cystoscopy_service_requests',
                    'alias' => 'CystoscopyServiceRequest',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'CystoscopyServiceRequest.other_service_request_id = OtherServiceRequest.id'
                    )
                )
        )));
        $this->set(compact('patient'));
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

    function printCystoscopyService($id = null) {
        $this->layout = 'ajax';
        $this->loadModel('Branch');
        if (!empty($id)) {
            $this->data = $this->Branch->read(null, 1);
            $dataService = $this->Patient->find('all', array('conditions' => array('Patient.is_active' => 1, 'CystoscopyService.is_active' => 1, 'CystoscopyService.id' => $id),
                'fields' => array('Queue.id, CystoscopyService.*, Patient.*'),
                'joins' => array(
                    array('table' => 'queues',
                        'alias' => 'Queue',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Queue.patient_id  = Patient.id'
                        )
                    ),
                    array('table' => 'cystoscopy_services',
                        'alias' => 'CystoscopyService',
                        'type' => 'INNER',
                        'conditions' => array(
                            'CystoscopyService.cystoscopy_service_queue_id = Queue.id'
                        )
                    )
            )));
            $this->set(compact('dataService'));
        }
    }

    

}

?>