<?php

class RoomsController extends AppController {

    var $name = 'Rooms';
    var $components = array('Helper');

    function index() {
        $this->layout = 'ajax';
    }

    function ajax() {
        $this->layout = 'ajax';
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
        $this->set(compact('companies'));
    }

    function view($id = null) {
        $this->layout = 'ajax';
        if (!$id) {
            $this->Session->setFlash(__(MESSAGE_DATA_INVALID, true), 'flash_failure');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('room', $this->Room->read(null, $id));        
    }

    function add() {
        $this->layout = 'ajax';
        if (!empty($this->data)) {
            if ($this->Helper->checkDouplicateService('room_name', 'room_type_id', 'company_id', 'rooms', trim($this->data['Room']['room_name']), $this->data['Room']['room_type_id'], $this->data['Room']['company_id'])) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $this->Room->create();
                $user = $this->getCurrentUser();
                $this->data['Room']['room_name'] = trim($this->data['Room']['room_name']);
                $this->data['Room']['created_by'] = $user['User']['id'];
                $this->data['Room']['is_active'] = 1;
                if ($this->Room->save($this->data)) {                    
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
        $roomTypes = ClassRegistry::init('RoomType')->find('list', array('conditions' => 'is_active=1', 'ORDER' => 'name'));
        $roomFloors = ClassRegistry::init('RoomFloor')->find('list', array('conditions' => 'is_active=1', 'ORDER' => 'name'));
        $this->set(compact('companies', 'roomTypes', 'roomFloors'));
    }

    function edit($id = null) {
        $this->layout = 'ajax';
        if (!$id && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        if (!empty($this->data)) {
            if ($this->Helper->checkDouplicateServiceEdit('room_name', 'room_type_id', 'company_id', 'rooms', $id, trim($this->data['Room']['room_name']), $this->data['Room']['room_type_id'], $this->data['Room']['company_id'])) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $user = $this->getCurrentUser();
                $this->data['Room']['room_name'] = trim($this->data['Room']['room_name']);
                $this->data['Room']['modified_by'] = $user['User']['id'];
                if ($this->Room->save($this->data)) {
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
        $roomTypes = ClassRegistry::init('RoomType')->find('list', array('conditions' => 'is_active=1', 'ORDER' => 'name'));   
        $roomFloors = ClassRegistry::init('RoomFloor')->find('list', array('conditions' => 'is_active=1', 'ORDER' => 'name'));
        $this->set(compact('companies', 'roomTypes', 'roomFloors'));
        $this->data = $this->Room->read(null, $id);        
    }

    function delete($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;            
        }
        $user = $this->getCurrentUser();
        $dateTime = date("Y-m-d H:i:s"); 
        $this->Room->updateAll(
                array('Room.is_active' => "2", 'Room.modified' => "'$dateTime'", "Room.modified_by" => $user['User']['id']),
                array('Room.id' => $id)
        );
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }
    
    function exportExcel(){
        $this->layout = 'ajax';
        if (isset($_POST['action']) && $_POST['action'] == 'export') {
            $user = $this->getCurrentUser();
            $filename = "public/report/room_export.csv";
            $fp = fopen($filename, "wb");
            $excelContent = 'Rooms' . "\n\n";
            $excelContent .= TABLE_NO . "\t" . TABLE_COMPANY. "\t" . TABLE_SECTION. "\t" . TABLE_NAME. "\t" . SALES_ORDER_UNIT_PRICE. "\t" . GENERAL_DESCRIPTION. "\t" . TABLE_ACCOUNT;
            if($user['User']['id'] == 1 || $user['User']['id'] == 57){
                $conditionUser = "";
            }else{
                $conditionUser = " AND rooms.company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id'].")";
            }
            $query = mysql_query('SELECT rooms.id, (SELECT name FROM companies WHERE id = rooms.company_id), sections.name, rooms.name, rooms.unit_price, rooms.description, (SELECT CONCAT(account_codes,"-",account_description) FROM chart_accounts WHERE id = rooms.chart_account_id) '
                    . '           FROM rooms INNER JOIN sections ON sections.id = rooms.section_id WHERE rooms.is_active=1'.$conditionUser.' ORDER BY rooms.name');
            $index = 1;
            while ($data = mysql_fetch_array($query)) {
                $excelContent .= "\n" . $index++ . "\t" . $data[1]. "\t" . $data[2]. "\t" . $data[3]. "\t" . $data[4]. "\t" . $data[5]. "\t" . $data[6];
            }
            $excelContent = chr(255) . chr(254) . @mb_convert_encoding($excelContent, 'UTF-16LE', 'UTF-8');
            fwrite($fp, $excelContent);
            fclose($fp);
            exit();
        }
    }

}

?>