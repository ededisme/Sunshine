<?php

class GroupInsurancesController extends AppController {

    var $name = 'GroupInsurances';
    var $components = array('Helper');

    function index() {
        $this->layout = 'ajax';
    }

    function ajax() {
        $this->layout = 'ajax';
    }

    function view($id = null) {
        $this->layout = 'ajax';
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $this->data = $this->GroupInsurance->read(null, $id);
    }

    function add() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            if ($this->Helper->checkDouplicate('name', 'group_insurances', $this->data['GroupInsurance']['name'])) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $this->GroupInsurance->create();
                $this->data['GroupInsurance']['created_by'] = $user['User']['id'];
                $this->data['GroupInsurance']['is_active'] = 1;
                if ($this->GroupInsurance->save($this->data)) {
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }      
    }

    function edit($id = null) {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!$id && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        if (!empty($this->data)) {
            if ($this->Helper->checkDouplicateEdit('name', 'group_insurances', $id, $this->data['GroupInsurance']['name'])) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $this->data['GroupInsurance']['modified_by'] = $user['User']['id'];
                if ($this->GroupInsurance->save($this->data)) {
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        $this->data = $this->GroupInsurance->read(null, $id);    
        $this->set(compact('companies'));
    }

    function delete($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        mysql_query("UPDATE `group_insurances` SET `is_active`=2, `modified`='".date("Y-m-d H:i:s")."', `modified_by`=".$user['User']['id']." WHERE `id`=".$id.";");
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }
    
    function exportExcel(){
        $this->layout = 'ajax';
        if (isset($_POST['action']) && $_POST['action'] == 'export') {
            $filename = "public/report/departments_export.csv";
            $fp = fopen($filename, "wb");
            $excelContent = 'GroupInsurance' . "\n\n";
            $excelContent .= TABLE_NO . "\t" . TABLE_NAME . "\t" . GENERAL_ABBR;
            $query = mysql_query('  SELECT id, name FROM departments WHERE is_active=1 ORDER BY name');
            $index = 1;
            while ($data = mysql_fetch_array($query)) {
                $excelContent .= "\n" . $index++ . "\t" . $data[1] ;
            }
            $excelContent = chr(255) . chr(254) . @mb_convert_encoding($excelContent, 'UTF-16LE', 'UTF-8');
            fwrite($fp, $excelContent);
            fclose($fp);
            exit();
        }
    }

}

?>