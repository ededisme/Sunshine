<?php

class LabosController extends AppController {

    var $name = 'Labos';
    var $uses = array('Labo', 'LaboItem', 'Patient', 'QueuedLabo', 'User');
    var $components = array('Helper', 'LaboProcess');

    function index() {
        $this->layout = 'ajax';
    }
    
    function  ajax(){
        $this->layout = 'ajax';
    }
    
    function queueLaboAjax($date = null) {
        $this->layout = 'ajax';
        $this->set(compact('date'));
    }

    function queueLaboByPassDoctorAjax() {
        $this->layout = 'ajax';
    }
    
    function approve($id){
        $this->layout = 'ajax';
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        $modified = date("Y-m-d H:i:s");                    
        $this->Labo->updateAll(
                array('Labo.is_validate' => "1", "Labo.validate_by" => $user['User']['id'], 'Labo.validate' => "'$modified'"),
                array('Labo.id' => $id)
        );
        $this->Helper->saveUserActivity($user['User']['id'], 'Labo', 'Save Approve', $id);
        echo MESSAGE_DATA_HAS_BEEN_SAVED;
        exit;
    }
    
    function disapprove($id){
        $this->layout = 'ajax';
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        $modified = date("Y-m-d H:i:s");                    
        $this->Labo->updateAll(
                array('Labo.is_validate' => "0", "Labo.validate_by" => $user['User']['id'], 'Labo.validate' => "'$modified'"),
                array('Labo.id' => $id)
        );
        $this->Helper->saveUserActivity($user['User']['id'], 'Labo', 'Save Approve', $id);
        echo MESSAGE_DATA_HAS_BEEN_SAVED;
        exit;
    }
    
    function laboTestResult($laboId = null) {
        $this->layout = 'ajax';
        $this->loadModel('LaboItem');
        $laboItems = $this->LaboItem->find('all');
        $exam = $this->getCurrentExam();
        $this->loadModel('LaboItemGroup');
        $this->loadModel('LaboTitleGroup');
        $laboItemGroups = $this->LaboItemGroup->find('all', array('fields' => array('LaboItemGroup.id', 'LaboItemGroup.name'), 'conditions' => array('LaboItemGroup.is_active != 2')));
        $laboTitleGroup = $this->LaboTitleGroup->find("all", array("conditions" => array("LaboTitleGroup.is_active!=2")));
        $labo = $this->Labo->getLaboByQueuedLaboId($laboId);
        $laboSelected = $this->LaboRequest->find('list', array('conditions' => array('LaboRequest.labo_id' => $laboId, 'LaboRequest.is_active != 2'), 'fields' => array('LaboRequest.id', 'LaboRequest.labo_item_group_id')));
        $this->set(compact('patientId', 'laboItemGroups', 'laboTitleGroup', 'laboSelected', 'labo', 'laboItems', 'exam'));
    }

    function selectPatient($qPatientId) {
        if (!empty($qPatientId)) {
            $this->loadModel('QueuedLabo');
            $conditions = array(
                'conditions' => array('QueuedLabo.id' => $qPatientId)
            );
            $qPatient = $this->QueuedLabo->find('all', $conditions);
            $this->set('qPatient', $qPatient);
        }
    }

    function laboResultSave() {
        $this->loadModel("LaboRequest");
        $this->loadModel("BackUpLaboRequest");
        $this->loadModel("CommentCategoryResult");
        $this->loadModel("Antibiogram");
        $this->loadModel("AntibiogramDetail");
        $this->loadModel("BackUpAntibiogramDetail");
        $this->loadModel("SpecimentType");
        $this->loadModel("LaboFile");
        $user = $this->Session->read('User');

        //updated antibiogram
        if (isset($_POST['antibiogram_test'])) {
            // updated antibiogram                                         
            for ($k = 0; $k < sizeof($_POST['testLaboRequest']); $k++) {
                $antibiogram_id = $_POST['testLaboRequest'][$k];
                for ($i = 0; $i < sizeof($_POST['antibiogram' . $antibiogram_id]); $i++) {
                    if ($_POST['medecineEdit' . $antibiogram_id] != "") {
                        $antibiogramDetail['id'] = $_POST['antibiogram' . $antibiogram_id][$i];
                        $antibiogramDetail['medicine_id'] = $_POST['medecineEdit' . $antibiogram_id][$i];
                        $antibiogramDetail['resistance'] = $_POST['resistanceEdit' . $antibiogram_id][$i];
                        $antibiogramDetail['intermidiate'] = $_POST['intermidiateEdit' . $antibiogram_id][$i];
                        $antibiogramDetail['sensible'] = $_POST['sensibleEdit' . $antibiogram_id][$i];
                        $antibiogramDetail['created_by'] = $user['User']['id'];
                        $this->AntibiogramDetail->save($antibiogramDetail);
                        //create new backup
                        $this->BackUpAntibiogramDetail->create();
                        $backUpAntibiogram['medicine_id'] = $_POST['medecineEdit' . $antibiogram_id][$i];
                        $backUpAntibiogram['antibiogram_id'] = $_POST['antibiogram_id'][$k];
                        $backUpAntibiogram['resistance'] = $_POST['resistanceEdit' . $antibiogram_id][$i];
                        $backUpAntibiogram['intermidiate'] = $_POST['intermidiateEdit' . $antibiogram_id][$i];
                        $backUpAntibiogram['sensible'] = $_POST['sensibleEdit' . $antibiogram_id][$i];
                        $backUpAntibiogram['created_by'] = $user['User']['id'];
                        $this->BackUpAntibiogramDetail->save($backUpAntibiogram);
                    }
                }
                for ($j = 0; $j < sizeof($_POST['medecine' . $antibiogram_id]); $j++) {
                    if ($_POST['medecine' . $antibiogram_id][$j] != "") {
                        $this->AntibiogramDetail->create();
                        $antiDetail['medicine_id'] = $_POST['medecine' . $antibiogram_id][$j];
                        $antiDetail['antibiogram_id'] = $_POST['antibiogram_id'][$k];
                        $antiDetail['resistance'] = $_POST['resistance' . $antibiogram_id][$j];
                        $antiDetail['intermidiate'] = $_POST['intermidiate' . $antibiogram_id][$j];
                        $antiDetail['sensible'] = $_POST['sensible' . $antibiogram_id][$j];
                        $antiDetail['created_by'] = $user['User']['id'];
                        $this->AntibiogramDetail->save($antiDetail);
                        //created new back up
                        $this->BackUpAntibiogramDetail->create();
                        $backUpAntibiogramDetail['medicine_id'] = $_POST['medecine' . $antibiogram_id][$j];
                        $backUpAntibiogramDetail['antibiogram_id'] = $_POST['antibiogram_id'][$k];
                        $backUpAntibiogramDetail['resistance'] = $_POST['resistance' . $antibiogram_id][$j];
                        $backUpAntibiogramDetail['intermidiate'] = $_POST['intermidiate' . $antibiogram_id][$j];
                        $backUpAntibiogramDetail['sensible'] = $_POST['sensible' . $antibiogram_id][$j];
                        $backUpAntibiogramDetail['created_by'] = $user['User']['id'];
                        $this->BackUpAntibiogramDetail->save($backUpAntibiogramDetail);
                    }
                }
            }
        }
        //create new antibiogram
        else {
            if (isset($_POST['labo_request_id'])) {
                for ($i = 0; $i < sizeof($_POST['labo_request_id']); $i++) {
                    $this->Antibiogram->create();
                    $antibiogram['Antibiogram']['labo_request_id'] = $_POST['labo_request_id'][$i];
                    $antibiogram['Antibiogram']['labo_item_id'] = $_POST['labo_item_id'][$i];
                    $antibiogram['Antibiogram']['created_by'] = $user['User']['id'];
                    $this->Antibiogram->save($antibiogram);
                    $antibiogram_id = $this->Antibiogram->getLastInsertId();
                    $id = $_POST['labo_request_id'][$i];
                    for ($j = 0; $j < sizeof($_POST['medecine' . $id]); $j++) {
                        if ($_POST['medecine' . $id][$j] != "") {
                            $this->AntibiogramDetail->create();
                            $antibiogramDetail['medicine_id'] = $_POST['medecine' . $id][$j];
                            $antibiogramDetail['antibiogram_id'] = $antibiogram_id;
                            $antibiogramDetail['resistance'] = $_POST['resistance' . $id][$j];
                            $antibiogramDetail['intermidiate'] = $_POST['intermidiate' . $id][$j];
                            $antibiogramDetail['sensible'] = $_POST['sensible' . $id][$j];
                            $antibiogramDetail['created_by'] = $user['User']['id'];
                            $this->AntibiogramDetail->save($antibiogramDetail);
                            //back up antibiogram detail
                            $this->BackUpAntibiogramDetail->create();
                            $backUpAntibiogramDetail['medicine_id'] = $_POST['medecine' . $id][$j];
                            $backUpAntibiogramDetail['antibiogram_id'] = $antibiogram_id;
                            $backUpAntibiogramDetail['resistance'] = $_POST['resistance' . $id][$j];
                            $backUpAntibiogramDetail['intermidiate'] = $_POST['intermidiate' . $id][$j];
                            $backUpAntibiogramDetail['sensible'] = $_POST['sensible' . $id][$j];
                            $backUpAntibiogramDetail['created_by'] = $user['User']['id'];
                            $this->BackUpAntibiogramDetail->save($backUpAntibiogramDetail);
                        }
                    }
                }
            }
        }

        // edit labo request after print 
        if (isset($_POST['backup_request'])) {

            $laboId = $this->data['Labo']['id'];
            $laboSave = $this->Labo->find("first", array("conditions" => array("Labo.id" => $laboId)));
            $i = 0;
            $j = 0;
            if (!empty($laboSave['LaboRequest'])) {
                foreach ($laboSave['LaboRequest'] as $laboResult) {

                    $laboResultSave['LaboRequest']['id'] = $laboResult['id'];
                    $laboResultSave['LaboRequest']['labo_item_group_id'] = $laboResult['labo_item_group_id'];
                    $laboResultSave['LaboRequest']['request'] = $laboResult['request'];
                    $laboResultSave['LaboRequest']['result'] = @serialize($this->data['Labo']['laboItems'][$i]);
                    $laboResultSave['LaboRequest']['modified_by'] = $user['User']['id'];
                    $this->LaboRequest->save($laboResultSave);
                    $i++;
                }
                //created in back up labo request 
                foreach ($laboSave['LaboRequest'] as $backUpLaboResult) {

                    $this->BackUpLaboRequest->create();
                    $backUpLaboResult['BackUpLaboRequest']['labo_id'] = $laboId;
                    $backUpLaboResult['BackUpLaboRequest']['labo_item_group_id'] = $backUpLaboResult['labo_item_group_id'];
                    $backUpLaboResult['BackUpLaboRequest']['request'] = $backUpLaboResult['request'];
                    $backUpLaboResult['BackUpLaboRequest']['result'] = @serialize($this->data['Labo']['laboItems'][$j]);
                    $backUpLaboResult['BackUpLaboRequest']['modified_by'] = $user['User']['id'];
                    $this->BackUpLaboRequest->save($backUpLaboResult);
                    $j++;
                }
            }
            for ($i = 0; $i < sizeof($this->data['Labo']['comment']); $i++) {
                if (!isset($this->data['Labo']['commentId'])) {
                    if ($this->data['Labo']['comment'][$i] != "") {
                        $this->CommentCategoryResult->create();
                        $comment['CommentCategoryResult']['labo_id'] = $laboId;
                        $comment['CommentCategoryResult']['category_id'] = $this->data['Labo']['categoryId'][$i];
                        $comment['CommentCategoryResult']['comment'] = $this->data['Labo']['comment'][$i];
                        $this->CommentCategoryResult->save($comment);
                    }
                } else {
                    $comment['CommentCategoryResult']['id'] = $this->data['Labo']['commentId'][$i];
                    $comment['CommentCategoryResult']['labo_id'] = $laboId;
                    $comment['CommentCategoryResult']['category_id'] = $this->data['Labo']['categoryId'][$i];
                    $comment['CommentCategoryResult']['comment'] = $this->data['Labo']['comment'][$i];
                    $this->CommentCategoryResult->save($comment);
                }
            }

            //update speciment type
            if (isset($this->data['Labo']['category_id'])) {
                for ($i = 0; $i < sizeof($this->data['Labo']['category_id']); $i++) {
                    $speciment['labo_item_category_id'] = $this->data['Labo']['category_id'][$i];
                    $speciment['speciment_type'] = $this->data['Labo']['speciment_type'][$i];
                    mysql_query("UPDATE speciment_types SET speciment_type= '" . $speciment['speciment_type'] . "'                 
                             WHERE labo_item_category_id = '" . $speciment['labo_item_category_id'] . "' AND labo_id = " . $laboId);
                }
            }
            // update labos
            $user = $this->Session->read('User');            
            // get auto lab number
            if($this->data['Labo']['number_lab']==""){
                $modCode = $this->Helper->getAutoGenerateLaboCode();  
                $laboSave['Labo']['number_lab'] = $modCode;
            }    
            $laboSave['Labo']['id'] = $laboId;
            $laboSave['Labo']['modified'] = date('Y-m-d H:i:s');
            $laboSave['Labo']['modified_by'] = $user['User']['id'];            
            $laboSave['Labo']['labo_site_id 	'] = $this->data['Labo']['labo_site_id 	'];
            $laboSave['Labo']['doctor_id'] = $this->data['Labo']['doctor_id'];
            if ($this->Labo->save($laboSave)) {
                $queue_id = $this->data['QueuedLabo']['id'];
                $category_id = $this->data['Labo']['category'];
                echo $category_id.'.*'.$queue_id;
                exit;
            }
        } else {
            $laboId = $this->data['Labo']['id'];
            for ($i = 0; $i < sizeof($this->data['Labo']['comment']); $i++) {
                if (!isset($this->data['Labo']['commentId'])) {
                    if ($this->data['Labo']['comment'][$i] != "") {
                        $this->CommentCategoryResult->create();
                        $comment['CommentCategoryResult']['labo_id'] = $laboId;
                        $comment['CommentCategoryResult']['category_id'] = $this->data['Labo']['categoryId'][$i];
                        $comment['CommentCategoryResult']['comment'] = $this->data['Labo']['comment'][$i];
                        $this->CommentCategoryResult->save($comment);
                    }
                } else {
                    $comment['CommentCategoryResult']['id'] = $this->data['Labo']['commentId'][$i];
                    $comment['CommentCategoryResult']['labo_id'] = $this->data['Labo']['laboItem'];
                    $comment['CommentCategoryResult']['category_id'] = $this->data['Labo']['categoryId'][$i];
                    $comment['CommentCategoryResult']['comment'] = $this->data['Labo']['comment'][$i];
                    $this->CommentCategoryResult->save($comment);
                }
            }
            $user = $this->Session->read('User');
            $qPatientSave['QueuedLabo']['id'] = $this->data['QueuedLabo']['id'];
            $qPatientSave['QueuedLabo']['modified'] = date('Y-m-d H:i:s');
            $qPatientSave['QueuedLabo']['modified_by'] = $user['User']['id'];
            $qPatientSave['QueuedLabo']['status'] = 2;
            if ($this->QueuedLabo->save($qPatientSave)) {                
                $laboId = $this->data['Labo']['id'];
                $laboSave = $this->Labo->find("first", array("conditions" => array("Labo.id" => $laboId)));
                $i = 0;
                $j = 0;
                // get auto lab number
                if($this->data['Labo']['number_lab']==""){
                    $modCode = $this->Helper->getAutoGenerateLaboCode();   
                    $laboSave['Labo']['number_lab'] = $modCode;
                } 
                $laboSave['Labo']['id'] = $laboId;
                $laboSave['Labo']['modified'] = date('Y-m-d H:i:s');
                $laboSave['Labo']['modified_by'] = $user['User']['id'];
                $laboSave['Labo']['status'] = 2;                
                $laboSave['Labo']['labo_site_id'] = $this->data['Labo']['labo_site_id'];
                $laboSave['Labo']['doctor_id'] = $this->data['Labo']['doctor_id'];
                if ($this->Labo->save($laboSave)) {
                    if (!empty($laboSave['LaboRequest'])) {
                        foreach ($laboSave['LaboRequest'] as $laboResult) {
                            $this->LaboRequest->create();
                            $laboResult['labo_id'] = $laboId;
                            $laboResult['result'] = @serialize($this->data['Labo']['laboItems'][$i]);
                            $laboResultSave['LaboRequest'] = $laboResult;
                            $laboResultSave['LaboRequest']['modified_by'] = $user['User']['id'];
                            $this->LaboRequest->save($laboResultSave);
                            $i++;
                        }
                        foreach ($laboSave['LaboRequest'] as $backUpLaboResult) {
                            $this->BackUpLaboRequest->create();
                            $backUpLaboResult['labo_id'] = $laboId;
                            $backUpLaboResult['result'] = @serialize($this->data['Labo']['laboItems'][$j]);
                            $backUpLaboResult['LaboRequest'] = $backUpLaboResult;
                            $backUpLaboResult['LaboRequest']['modified_by'] = $user['User']['id'];
                            $this->BackUpLaboRequest->save($backUpLaboResult);
                            $j++;
                        }
                    }
                }
                // speciment types
                if (isset($this->data['Labo']['category_id'])) {
                    for ($i = 0; $i < sizeof($this->data['Labo']['category_id']); $i++) {
                        $result = mysql_query("SELECT id FROM speciment_types WHERE labo_id=$laboId AND labo_item_category_id=" . $this->data['Labo']['category_id'][$i]);
                        $num = mysql_num_rows($result);
                        if ($num > 0) {
                            $speciment['labo_item_category_id'] = $this->data['Labo']['category_id'][$i];
                            $speciment['speciment_type'] = $this->data['Labo']['speciment_type'][$i];
                            mysql_query("UPDATE speciment_types SET speciment_type= '" . $speciment['speciment_type'] . "'                 
                                         WHERE labo_item_category_id = '" . $speciment['labo_item_category_id'] . "' AND labo_id = " . $laboId);
                        } else {
                            $this->SpecimentType->create();
                            $speciment['labo_id'] = $laboId;
                            $speciment['labo_item_category_id'] = $this->data['Labo']['category_id'][$i];
                            $speciment['speciment_type'] = $this->data['Labo']['speciment_type'][$i];
                            $this->SpecimentType->save($speciment);
                        }
                    }
                }
                
                if ($_FILES['data']['name']['Labo']['file_pdf'] != '') {
                    for ($i = 0; $i < sizeof($_FILES['data']['name']['Labo']['file_pdf']); $i++) {
                        $allowed = array('pdf', 'PDF', 'gif', 'GIF', 'jpeg', 'JPEG', 'png', 'PNG');
                        $catalog = md5(date("Y-m-d H:i:s")) . '_' . $_FILES['data']['name']['Labo']['file_pdf'][$i];
                        $extFile = pathinfo($catalog, PATHINFO_EXTENSION);
                        $cataFolder = 'public/labo_pdf/';
                        if (in_array($extFile, $allowed)) {
                            // echo($catalog);
                            // exit;
                            move_uploaded_file($_FILES['data']['tmp_name']['Labo']['file_pdf'][$i], $cataFolder . $catalog);
                            $this->LaboFile->create();
                            $laboFile['LaboFile']['labo_id'] = $laboId;
                            $laboFile['LaboFile']['file'] = $catalog;
                            $laboFile['LaboFile']['created'] = date('Y-m-d H:i:s');
                            $laboFile['LaboFile']['created_by'] = $user['User']['id'];
                            $this->LaboFile->save($laboFile);
                        }
                    }
                }
                
                $category_id = $this->data['Labo']['category'];
                $queue_id = $this->data['QueuedLabo']['id'];
            }
            echo $category_id.'.*'.$queue_id;
            exit;
        }
    }

    function savePrint($qPatientId=null, $category_id=null) {
        $this->layout = 'ajax';
        if (!empty($qPatientId)) {
            $this->loadModel('QueuedLabo');
            $this->loadModel('User');
            $this->loadModel('LaboItemCategory');
            $this->loadModel('QueuedLabo');
            $this->loadModel("Branch");
            $conditions = array(
                'conditions' => array('QueuedLabo.id' => $qPatientId)
            );
            $this->data = $this->Branch->read(null, 1);
            $qPatient = $this->QueuedLabo->find('first', $conditions);
            $patient = $this->Patient->find('first', array('conditions' => array('id' => $qPatient['Queue']['patient_id'])));            
            $labo = $this->Labo->getLaboByQueuedPatientId($qPatientId);
            $listLaboItemCategories = $this->LaboProcess->getListLaboItemCategoriesPrint($labo, $category_id);
            $listLaboItemRequest = $this->LaboProcess->getListLaboItemRequest($labo);
            $laboItems = $this->LaboItem->find('all', array('conditions' => array("LaboItem.id in ($listLaboItemRequest)"), 'order' => array('LaboItem.item_code ASC')));
            $this->set('laboItems', $laboItems);
            $this->set('labo', $labo);
            $laboItemCategories = $this->LaboItemCategory->find('all');
            $createdBy = $this->User->getUserById($labo['Labo']['created_by']);
            $modifiedBy = $this->User->getUserById($labo['Labo']['modified_by']);
            $this->set('createdBy', $createdBy);
            $this->set('modifiedBy', $modifiedBy);
            $this->set('laboItemCategories', $laboItemCategories);
            $this->set(compact('qPatient' , 'patient', 'listLaboItemCategories'));
        }
        $this->Patient->id = $this->QueuedLabo->field('queue_id');
        $this->set('sex', $this->Patient->field('sex'));
    }

    function printLaboWithoutCategory($qPatientId = null, $laboId = null) {
        $this->layout = 'ajax';
        if (!empty($qPatientId)) {
            $this->loadModel('QueuedLabo');
            $this->loadModel('User');
            $this->loadModel('LaboItemCategory');
            $this->loadModel('Branch');
            $conditions = array(
                'conditions' => array('QueuedLabo.id' => $qPatientId)
            );
            $this->data = $this->Branch->read(null, 1);
            $qPatient = $this->QueuedLabo->find('first', $conditions);            
            $patient = $this->Patient->find('first', array('conditions' => array('id' => $qPatient['Queue']['patient_id'])));                        
            $labo = $this->Labo->getLaboByQueuedPatientId($qPatientId);            
            $listLaboItemCategories = $this->LaboProcess->getListLaboItemCategories($labo);
            $listLaboItemRequest = $this->LaboProcess->getListLaboItemRequest($labo);
            $laboItems = $this->LaboItem->find('all', array('conditions' => array("LaboItem.id in ($listLaboItemRequest)"), 'order' => array('LaboItem.item_code ASC')));
            $laboItemCategories = $this->LaboItemCategory->find('all');
            $createdBy = $this->User->getUserById($labo['Labo']['created_by']);
            $modifiedBy = $this->User->getUserById($labo['Labo']['modified_by']);        
            $user = $this->getCurrentUser();
            $this->set(compact('qPatient', 'patient', 'listLaboItemCategories', "laboItemCategories", "modifiedBy", "createdBy", "labo", "laboItems", "laboId" , "user"));
        }
        $this->Patient->id = $this->QueuedLabo->field('queue_id');
        $this->set('sex', $this->Patient->field('sex'));
    }

    function laboResultSaveAfterPrint() {
        $this->loadModel("LaboRequest");
        $this->loadModel("SpecimentType");
        $this->loadModel("CommentCategoryResult");
        $qPatientSave['QueuedLabo']['id'] = $this->data['QueuedLabo']['id'];
        $qPatientSave['QueuedLabo']['status'] = 2;

        if ($this->QueuedLabo->save($qPatientSave)) {
            // update status old labo to 4  
            $laboIdOld = $this->data['Labo']['id'];
            $laboUpdate['Labo']['id'] = $this->data['Labo']['id'];
            $laboUpdate['Labo']['status'] = 4;
            $this->Labo->save($laboUpdate);
            
            // create new labo 
            $this->Labo->create();
            $user = $this->getCurrentUser();
            $laboSave['Labo']['queued_id'] = $this->data['QueuedLabo']['id'];
//            $laboSave['Labo']['number_lab'] = $this->data['Labo']['number_lab'];
            $laboSave['Labo']['labo_site_id'] = $this->data['Labo']['labo_site_id'];
            $laboSave['Labo']['doctor_id'] = $this->data['Labo']['doctor_id'];
            $laboSave['Labo']['created_by'] = $user['User']['id'];
            $laboSave['Labo']['modified_by'] = $user['User']['id'];
            $laboSave['Labo']['status'] = 3;
            $i = 0;
            if ($this->Labo->save($laboSave)) {
                $laboId = $this->data['Labo']['id'];
                $laboSave = $this->Labo->find("first", array("conditions" => array("Labo.id" => $laboId)));
                $i = 0;
                $labo_id = $this->Labo->getLastInsertId();
                if (!empty($laboSave['LaboRequest'])) {
                    foreach ($laboSave['LaboRequest'] as $laboResult) {
                        $this->LaboRequest->create();
                        $laboResultSave['LaboRequest']['labo_id'] = $labo_id;
                        $laboResultSave['LaboRequest']['labo_item_group_id'] = $laboResult['labo_item_group_id'];
                        $laboResultSave['LaboRequest']['request'] = $laboResult['request'];
                        $laboResultSave['LaboRequest']['result'] = @serialize($this->data['Labo']['laboItems'][$i]);
                        $laboResultSave['LaboRequest']['created_by'] = $laboResult['created_by'];
                        $laboResultSave['LaboRequest']['modified_by'] = $user['User']['id'];
                        $this->LaboRequest->save($laboResultSave);
                        $i++;
                    }
                }
                
                // create comment for result
                for ($i = 0; $i < sizeof($this->data['Labo']['comment']); $i++) {
                    if (!isset($this->data['Labo']['commentId'])) {
                        if ($this->data['Labo']['comment'][$i] != "") {
                            $this->CommentCategoryResult->create();
                            $comment['CommentCategoryResult']['labo_id'] = $labo_id;
                            $comment['CommentCategoryResult']['category_id'] = $this->data['Labo']['categoryId'][$i];
                            $comment['CommentCategoryResult']['comment'] = $this->data['Labo']['comment'][$i];
                            $this->CommentCategoryResult->save($comment);
                        }
                    } else {
                        $comment['CommentCategoryResult']['id'] = $this->data['Labo']['commentId'][$i];
                        $comment['CommentCategoryResult']['labo_id'] = $labo_id;
                        $comment['CommentCategoryResult']['category_id'] = $this->data['Labo']['categoryId'][$i];
                        $comment['CommentCategoryResult']['comment'] = $this->data['Labo']['comment'][$i];
                        $this->CommentCategoryResult->save($comment);
                    }
                }

                // speciment types
                if (isset($this->data['Labo']['category_id'])) {
                    for ($i = 0; $i < sizeof($this->data['Labo']['category_id']); $i++) {
                        $result = mysql_query("SELECT id FROM speciment_types WHERE labo_id=$labo_id AND labo_item_category_id=" . $this->data['Labo']['category_id'][$i]);
                        $num = mysql_num_rows($result);
                        if ($num > 0) {
                            $speciment['labo_item_category_id'] = $this->data['Labo']['category_id'][$i];
                            $speciment['speciment_type'] = $this->data['Labo']['speciment_type'][$i];
                            mysql_query("UPDATE speciment_types SET speciment_type= '" . $speciment['speciment_type'] . "'                 
                                         WHERE labo_item_category_id = '" . $speciment['labo_item_category_id'] . "' AND labo_id = " . $labo_id);
                        } else {
                            $this->SpecimentType->create();
                            $speciment['labo_id'] = $labo_id;
                            $speciment['labo_item_category_id'] = $this->data['Labo']['category_id'][$i];
                            $speciment['speciment_type'] = $this->data['Labo']['speciment_type'][$i];
                            $this->SpecimentType->save($speciment);
                        }
                    }
                }
            }
        }
        $queue_id = $this->data['QueuedLabo']['id'];
        $category_id = $this->data['Labo']['category'];
        echo $category_id.'.*'.$queue_id.'.*'.$labo_id;
        exit;
    }

    function chart() {
        $this->loadModel('LaboItem');
        $this->loadModel('LaboItemCategory');
    }

    function chartShow() {
        $this->layout = 'ajax';
    }

    function bloodTest($qPatientId = null) {
        $this->layout = 'ajax';
        if (!empty($qPatientId)) {
            $this->loadModel('QueuedLabo');
            $this->loadModel('LaboItemCategory');
            $this->loadModel('LaboTitleItem');
            $this->loadModel('LaboSite');
            $conditions = array(
                'conditions' => array('QueuedLabo.id' => $qPatientId)
            );
            $qPatient = $this->QueuedLabo->find('first', $conditions);
            $patient = $this->Patient->find('first', array('conditions' => array('id' => $qPatient['Queue']['patient_id'])));
            $labo = $this->Labo->getLaboByQueuedLaboId($qPatientId);
            $listLaboItemCategories = $this->LaboProcess->getListLaboItemCategories($labo);
            $listLaboItemRequest = $this->LaboProcess->getListLaboItemRequest($labo);
            $laboItems = $this->LaboItem->find('all', array('conditions' => array("LaboItem.id in ($listLaboItemRequest)"), 'order' => array('LaboItem.item_code ASC')));
            $this->Patient->id = $this->QueuedLabo->field('queue_id');
            $laboItemCategories = $this->LaboItemCategory->find('all');
            $this->set('sex', $this->Patient->field('sex'));            
            $categories = $this->LaboItemCategory->find('list', array('fields' => array('LaboItemCategory.id', 'LaboItemCategory.name'), 'conditions' => array('LaboItemCategory.is_active' => 1)));
            $sites = $this->LaboSite->find('all', array('fields' => array('LaboSite.id', 'LaboSite.name'), 'conditions' => array('LaboSite.is_active' => 1)));
            $doctors = $this->User->find('all', array('conditions' => array('User.is_active' => 1, 'UserGroup.group_id' => array('2', '21')), 'order'=>array('Employee.name ASC'),
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
            $this->set(compact('qPatient', 'listLaboItemCategories', 'laboItemCategories', 'labo', 'laboItems', 'categories', 'sites', 'doctors', 'patient'));
        }
    }

    function normalValue() {
        $this->loadModel('LaboItem');
        $this->loadModel('LaboItemCategory');
        $laboItems = $this->LaboItem->find('all', array('conditions' => array('LaboItem.parent_id IS NULL')));
        $laboItemCategories = $this->LaboItemCategory->find('all');
        $this->set('laboItems', $laboItems);
        $this->set('laboItemCategories', $laboItemCategories);
    }

    function normalValueSave($gender = null) {
        foreach ($this->data['LaboItem'] as $id => $item) {
            $this->LaboItem->id = $id;
            if ($gender == 'M') {
                $item['min_value_m'] = $item['min_value_m'];
                $item['max_value_m'] = $item['max_value_m'];
            } else {
                $item['min_value_f'] = $item['min_value_f'];
                $item['max_value_f'] = $item['max_value_f'];
            }
            $item['item_unit'] = $item['item_unit'];
            $this->LaboItem->save($item);
        }
        exit;
    }

    function editNormalChildNormalValue($id) {
        $this->layout = "ajax";
        $this->loadModel('LaboItemCategory');
        $laboItems = $this->LaboItem->find("all", array("conditions" => array("LaboItem.parent_id " => $id)));
        $laboItemCategories = $this->LaboItemCategory->find('all');
        $this->set('laboItemCategories', $laboItemCategories);
        $this->set(compact('laboItems'));
    }

    function laboList() {
        $this->layout = 'ajax';       
    }
    function laboListAjax($validate = 'all', $date = null) {
        $this->layout = 'ajax';
        $this->set(compact('validate', 'date'));
    }

    function view($qPatientId = null) {
        $this->layout = 'ajax';
        if (!empty($qPatientId)) {
            $this->loadModel('QueuedLabo');
            $this->loadModel('LaboItemCategory');
            $conditions = array(
                'conditions' => array('QueuedLabo.id' => $qPatientId)
            );
            $laboItemCategories = $this->LaboItemCategory->find('all');
            $qPatient = $this->QueuedLabo->find('first', $conditions);
            $patient = $this->Patient->find('first', array('conditions' => array('id' => $qPatient['Queue']['patient_id'])));            
            $labo = $this->Labo->getLaboByQueuedLaboId($qPatientId);
            $listLaboItemCategories = $this->LaboProcess->getListLaboItemCategories($labo);
            $listLaboItemRequest = $this->LaboProcess->getListLaboItemRequest($labo);
            $laboItems = $this->LaboItem->find('all', array('conditions' => array("LaboItem.id in ($listLaboItemRequest)"), 'order' => array('LaboItem.item_code ASC')));
            $this->set('sex', $this->Patient->field('sex'));
            $this->set(compact('qPatient', 'patient', 'listLaboItemCategories', 'laboItems', 'labo', 'laboItemCategories'));
        }
    }

    function viewAfterPrint($laboId = null, $qPatientId = null) {
        $this->layout = 'ajax';
        if (!empty($laboId)) {
            $this->loadModel('QueuedLabo');
            $this->loadModel('LaboItemCategory');
            $conditions = array(
                'conditions' => array('QueuedLabo.id' => $qPatientId)
            );
            $laboItemCategories = $this->LaboItemCategory->find('all');
            $qPatient = $this->QueuedLabo->find('first', $conditions);
            $patient = $this->Patient->find('first', array('conditions' => array('id' => $qPatient['Queue']['patient_id'])));
            $labo = $this->Labo->getLaboById($laboId);
            $listLaboItemCategories = $this->LaboProcess->getListLaboItemCategories($labo);
            $listLaboItemRequest = $this->LaboProcess->getListLaboItemRequest($labo);
            $laboItems = $this->LaboItem->find('all', array('conditions' => array("LaboItem.id in ($listLaboItemRequest)"), 'order' => array('LaboItem.item_code ASC')));
            $this->set('sex', $this->Patient->field('sex'));
            $this->set('laboItemCategories', $laboItemCategories);
            $this->set(compact('listLaboItemCategories', 'laboItems', 'labo', 'qPatient', 'patient'));
        }
    }

    function edit($qPatientId = null) {
        $this->layout = 'ajax';
        if (!empty($qPatientId)) {
            $this->loadModel('LaboSite');
            $this->loadModel('QueuedLabo');
            $this->loadModel('LaboItemCategory');
            $conditions = array(
                'conditions' => array('QueuedLabo.id' => $qPatientId)
            );
            $qPatient = $this->QueuedLabo->find('first', $conditions);
            $patient = $this->Patient->find('first', array('conditions' => array('id' => $qPatient['Queue']['patient_id'])));            
            $labo = $this->Labo->getLaboByQueuedLaboId($qPatientId);
            $listLaboItemCategories = $this->LaboProcess->getListLaboItemCategories($labo);
            $listLaboItemRequest = $this->LaboProcess->getListLaboItemRequest($labo);
            $laboItems = $this->LaboItem->find('all', array('conditions' => array("LaboItem.id in ($listLaboItemRequest)"), 'order' => array('LaboItem.item_code ASC')));
            $laboItemCategories = $this->LaboItemCategory->find('all', array('codition' => array('is_active' => 1)));
            $this->set('sex', $this->Patient->field('sex'));
            $categories = $this->LaboItemCategory->find('list', array('fields' => array('LaboItemCategory.id', 'LaboItemCategory.name'), 'conditions' => array('LaboItemCategory.is_active' => 1)));
            $sites = $this->LaboSite->find('all', array('fields' => array('LaboSite.id', 'LaboSite.name'), 'conditions' => array('LaboSite.is_active' => 1)));
            $doctors = $this->User->find('all', array('conditions' => array('User.is_active' => 1, 'UserGroup.group_id' => array('2', '21')), 'order'=>array('Employee.name ASC'),
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
            $this->set(compact('qPatient', 'patient', 'categories', 'listLaboItemCategories', 'laboItemCategories', 'labo', 'laboItems', 'sites', 'doctors'));
        }
    }

    function editAfterPrint($laboId = null, $qPatientId = null) {
        $this->layout = 'ajax';        
        if (!empty($qPatientId)) {
            $this->loadModel('LaboSite');
            $this->loadModel('QueuedLabo');
            $this->loadModel('LaboItemCategory');
            $conditions = array(
                'conditions' => array('QueuedLabo.id' => $qPatientId)
            );            
            $qPatient = $this->QueuedLabo->find('first', $conditions);
            $patient = $this->Patient->find('first', array('conditions' => array('id' => $qPatient['Queue']['patient_id'])));            
            $labo = $this->Labo->getLaboByQueuedLaboId($qPatientId, $laboId);
            $listLaboItemCategories = $this->LaboProcess->getListLaboItemCategories($labo);
            $listLaboItemRequest = $this->LaboProcess->getListLaboItemRequest($labo);
            $laboItems = $this->LaboItem->find('all', array('conditions' => array("LaboItem.id in ($listLaboItemRequest)"), 'order' => array('LaboItem.item_code ASC')));
            $laboItemCategories = $this->LaboItemCategory->find('all', array('codition' => array('is_active' => 1)));
            $this->set('sex', $this->Patient->field('sex'));
            $categories = $this->LaboItemCategory->find('list', array('fields' => array('LaboItemCategory.id', 'LaboItemCategory.name'), 'conditions' => array('LaboItemCategory.is_active' => 1)));
            $sites = $this->LaboSite->find('all', array('fields' => array('LaboSite.id', 'LaboSite.name'), 'conditions' => array('LaboSite.is_active' => 1)));
            $doctors = $this->User->find('all', array('conditions' => array('User.is_active' => 1, 'UserGroup.group_id' => 3), 'order'=>array('Employee.name ASC'),
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
            $this->set(compact('qPatient', 'patient', 'categories', 'listLaboItemCategories', 'laboItemCategories', 'labo', 'laboItems', 'sites', 'doctors'));
        }
    }

    function printLabo($qPatientId) {
        $this->layout = 'print';
        if (!empty($qPatientId)) {
            $this->loadModel('QueuedLabo');
            $this->loadModel('User');
            $this->loadModel('LaboItemCategory');
            $this->loadModel('QueuedLabo');
            $conditions = array(
                'conditions' => array('QueuedLabo.id' => $qPatientId)
            );
            $this->data = $this->Branch->read(null, 1);
            $qPatient = $this->QueuedLabo->find('first', $conditions);
            $patient = $this->Patient->find('first', array('conditions' => array('id' => $qPatient['Queue']['patient_id'])));            
            $labo = $this->Labo->getLaboByQueuedLaboId($qPatientId);
            $listLaboItemCategories = $this->LaboProcess->getListLaboItemCategories($labo);
            $listLaboItemRequest = $this->LaboProcess->getListLaboItemRequest($labo);
            $laboItems = $this->LaboItem->find('all', array('conditions' => array("LaboItem.id in ($listLaboItemRequest)"), 'order' => array('LaboItem.item_code ASC')));
            $laboItemCategories = $this->LaboItemCategory->find('all', array('codition' => array('is_active' => 1)));
            $categories = $this->LaboItemCategory->find('list', array('fields' => array('LaboItemCategory.id', 'LaboItemCategory.name'), 'conditions' => array('LaboItemCategory.is_active' => 1)));
            $createdBy = $this->User->getUserById($labo['Labo']['created_by']);
            $modifiedBy = $this->User->getUserById($labo['Labo']['modified_by']);              
            $this->set(compact('qPatient', 'patient', 'listLaboItemCategories', 'createdBy', 'modifiedBy', 'laboItemCategories', 'laboItems', 'labo', 'categories'));
        }
        $this->Patient->id = $this->QueuedLabo->field('queue_id');
        $this->set('sex', $this->Patient->field('sex'));
    }
    
    
    function getResultLabo($qPatientId = null) {
        
        if(!empty($qPatientId)){
            $data = array();
            $this->loadModel('QueuedLabo');
            $this->loadModel('User');
            $this->loadModel('LaboItemCategory');
            $conditions = array(
                'conditions' => array('QueuedLabo.id' => $qPatientId)
            );
            $qPatient = $this->QueuedLabo->find('first', $conditions);
            $patient = $this->Patient->find('first', array('conditions' => array('id' => $qPatient['Queue']['patient_id'])));            
            $labo = $this->Labo->getLaboByQueuedLaboId($qPatientId);
            $listLaboItemCategories = $this->LaboProcess->getListLaboItemCategories($labo);
            $listLaboItemRequest = $this->LaboProcess->getListLaboItemRequest($labo);
            $laboItems = $this->LaboItem->find('all', array('conditions' => array("LaboItem.id in ($listLaboItemRequest)"), 'order' => array('LaboItem.item_code ASC')));
            $laboItemCategories = $this->LaboItemCategory->find('all', array('codition' => array('is_active' => 1)));
            $categories = $this->LaboItemCategory->find('list', array('fields' => array('LaboItemCategory.id', 'LaboItemCategory.name'), 'conditions' => array('LaboItemCategory.is_active' => 1)));
            $createdBy = $this->User->getUserById($labo['Labo']['created_by']);
            $modifiedBy = $this->User->getUserById($labo['Labo']['modified_by']);  

            $data[0] = $patient;
            $data[1] = $labo;
            $data[2] = $listLaboItemCategories;
            $data[3] = $laboItems;
            $data[4] = $laboItemCategories;
            $data[5] = $categories;
            return $data;
        }else{
            return null;
        }
        
    }
    

    function updateLaboStatus() {
        $this->layout = 'print';
        if (!empty($this->data)) {
            $laboUpdate['Labo']['id'] = $this->data['Labo']['id'];
            $laboUpdate['Labo']['status'] = 3;
            if($this->Labo->save($laboUpdate)){                   
                $this->loadModel('QueuedLabo');
                $this->loadModel('User');
                $this->loadModel('LaboItemCategory');  
                $qPatientId = $this->data['QueuedLabo']['id'];                
                $conditions = array(
                    'conditions' => array('QueuedLabo.id' => $qPatientId)
                );
                $qPatient = $this->QueuedLabo->find('first', $conditions);            
                $patient = $this->Patient->find('first', array('conditions' => array('id' => $qPatient['Queue']['patient_id'])));                        
                $labo = $this->Labo->getLaboByQueuedPatientId($qPatientId);            
                $listLaboItemCategories = $this->LaboProcess->getListLaboItemCategories($labo);
                $listLaboItemRequest = $this->LaboProcess->getListLaboItemRequest($labo);
                $laboItems = $this->LaboItem->find('all', array('conditions' => array("LaboItem.id in ($listLaboItemRequest)"), 'order' => array('LaboItem.item_code ASC')));
                $laboItemCategories = $this->LaboItemCategory->find('all');
                $createdBy = $this->User->getUserById($labo['Labo']['created_by']);
                $modifiedBy = $this->User->getUserById($labo['Labo']['modified_by']);            
                $this->set(compact('qPatient', 'patient', 'listLaboItemCategories', "laboItemCategories", "modifiedBy", "createdBy", "labo", "laboItems", "laboId"));
            }
        }
    }

    function printLaboAfterPrint($laboId = null, $qPatientId) {
        $this->layout = 'print';
        if (!empty($qPatientId)) {
            $this->loadModel('QueuedLabo');
            $this->loadModel('User');
            $this->loadModel('LaboItemCategory');

            $conditions = array(
                'conditions' => array('QueuedLabo.id' => $qPatientId)
            );
            $qPatient = $this->QueuedLabo->find('first', $conditions);
            $patient = $this->Patient->find('first', array('conditions' => array('id' => $qPatient['Queue']['patient_id'])));
            $labo = $this->Labo->getLaboById($laboId);
            $listLaboItemCategories = $this->LaboProcess->getListLaboItemCategories($labo);
            $listLaboItemRequest = $this->LaboProcess->getListLaboItemRequest($labo);
            $laboItems = $this->LaboItem->find('all', array('conditions' => array("LaboItem.id in ($listLaboItemRequest)"), 'order' => array('LaboItem.item_code ASC')));            
            $categories = $this->LaboItemCategory->find('list', array('fields' => array('LaboItemCategory.id', 'LaboItemCategory.name'), 'conditions' => array('LaboItemCategory.is_active' => 1)));
            $laboItemCategories = $this->LaboItemCategory->find('all');
            $createdBy = $this->User->getUserById($labo['Labo']['created_by']);
            $modifiedBy = $this->User->getUserById($labo['Labo']['modified_by']);            
            $this->set(compact('qPatient', 'patient', 'listLaboItemCategories', 'laboItems', 'categories', 'labo', 'laboItemCategories', 'modifiedBy', 'createdBy'));
        }
        $this->Patient->id = $this->QueuedLabo->field('queue_id');
        $this->set('sex', $this->Patient->field('sex'));
    }

    function laboTestRequest($QueuedLaboId=null) {
        $this->layout = 'ajax';
        $this->loadModel('LaboItemGroup');
        $laboItemGroups = $this->LaboItemGroup->find('all', array('fields' => array('LaboItemGroup.id', 'LaboItemGroup.name'), 'conditions' => array('LaboItemGroup.is_active != 2')));
        $this->set(compact("laboItemGroups"));
        $this->loadModel('LaboTitleGroup');
        $laboTitleGroup = $this->LaboTitleGroup->find("all", array("conditions" => array("LaboTitleGroup.is_active!=2") , 'order' => 'LaboTitleGroup.ordering ASC'));    
        $this->set('laboTitleGroup', $laboTitleGroup);        
        $this->set('QueuedLaboId', $QueuedLaboId);
        $this->loadModel('Labo');
        $labo = $this->Labo->getLaboByQueuedLaboId($QueuedLaboId);
        $this->set('labo', $labo);
    }

    function laboRequestSave() {
        $this->layout = 'ajax';
        if (!empty($this->data) && isset($this->data['LaboItemGroup'])) {            
            $this->loadModel("LaboRequest");
            $this->loadModel("LaboItemGroup");
            $this->loadModel('QueuedLabo');
            $createdBy = $this->getCurrentUser();
            $QueuedLaboId = $this->data['QueuedLabo']['id'];            
            $laboId = $this->data['Labo']['id'];
            $labo['Labo']['id'] = $laboId;
            $labo['Labo']['status'] = 1;            
            $labo['Labo']['created_by'] = $createdBy['User']['id'];
            $labo['Labo']['queued_id'] = $QueuedLaboId;
            $qPatientUpdate['QueuedLabo']['id'] = $QueuedLaboId;
            $qPatientUpdate['QueuedLabo']['status'] = 2;            
            if ($this->Labo->save($labo)) {
                foreach ($this->data['LaboItemGroup'] as $laboItemGoup) {
                    $this->LaboRequest->create();
                    $laboItemIds = $this->LaboItemGroup->find("first", array("conditions" => array("LaboItemGroup.id" => $laboItemGoup), "fields" => array("LaboItemGroup.labo_item_id")));
                    $laboItemIds = explode(",", $laboItemIds['LaboItemGroup']['labo_item_id']);
                    $laboRequest['LaboRequest']['labo_id'] = $this->Labo->id;
                    $laboRequest['LaboRequest']['labo_item_group_id'] = $laboItemGoup;
                    $laboRequest['LaboRequest']['request'] = @serialize($laboItemIds);
                    $laboRequest['LaboRequest']['created_by'] = $createdBy['User']['id'];
                    $this->LaboRequest->save($laboRequest);
                }
                if ($this->QueuedLabo->save($qPatientUpdate)) {
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                }
            }
        }else{
            echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
            exit;
        }
    }

    /**
     * Can not edit the result, after approve it by print result.
     * We change status to 3
     */
    function approveResult() {
        $this->layout = 'ajax';
        if (!empty($this->data)) {
            $laboId = $this->data['Labo']['id'];
            $labo['Labo']['id'] = $laboId;
            $labo['Labo']['status'] = 3;
            if ($this->Labo->save($labo)) {
                
            }
        }
    }
    
    function laboRequest($Lid=null) {
        $this->layout = 'ajax';
        $this->loadModel('Labo');
        $this->loadModel('LaboRequest');
        $this->loadModel('LaboItemGroup');
        $this->loadModel('LaboAnapathRequest');   
        
        $labo = $this->Labo->find('first', array('conditions' => array('Labo.id' => $Lid)));
        $laboItemGroups = $this->LaboItemGroup->find('all', array('fields' => array('LaboItemGroup.id', 'LaboItemGroup.name'), 'conditions' => array('LaboItemGroup.is_active != 2')));
        $this->set(compact("laboItemGroups"));
        $this->set('queuedPatientId', $labo['Labo']['queued_id']);
        $laboSelected = $this->LaboRequest->find('list', array('conditions' => array('LaboRequest.labo_id' => $Lid, 'LaboRequest.is_active != 2'), 'fields' => array('LaboRequest.id', 'LaboRequest.labo_item_group_id')));
        $this->set(compact('labo', 'laboSelected'));
        $this->loadModel('LaboTitleGroup');
        $laboTitleGroup = $this->LaboTitleGroup->find("all", array("conditions" => array("LaboTitleGroup.is_active!=2") , 'order' => 'LaboTitleGroup.ordering ASC'));            
        $this->set('laboTitleGroup', $laboTitleGroup);
    }
    
    function deletePdfFile($id = null, $table = null) {
        if (!$id) {
            echo '0';
            exit;
        }
        $user = $this->getCurrentUser();
        $modified = date("Y-m-d H:i:s");
        $this->loadModel('LaboFile');
        $this->LaboFile->updateAll(
                array('LaboFile.status' => "2", 'LaboFile.modified' => "'$modified'", "LaboFile.modified_by" => $user['User']['id']), array('LaboFile.id' => $id)
        );
        echo '1';
        exit;
    }
    
    
}

?>