<?php

class ReferralsController extends AppController {

    var $name = 'Referrals';
    var $components = array('Helper');
    
    function index() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $this->Helper->saveUserActivity($user['User']['id'], 'Referral', 'Dashboard');
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
        $user = $this->getCurrentUser();
    }

    function add() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $dateNow  = date("Y-m-d H:i:s");
            $this->Referral->create();
            $this->data['Referral']['name']       = $this->data['Referral']['name'];
            $this->data['Referral']['sex']        = $this->data['Referral']['sex'];
            $this->data['Referral']['telephone']  = $this->data['Referral']['telephone'];
            $this->data['Referral']['dob']        = $this->data['Referral']['dob'];
            $this->data['Referral']['created']    = $dateNow;
            $this->data['Referral']['created_by'] = $user['User']['id'];
            $this->data['Referral']['is_active']  = 1;
            if ($this->Referral->save($this->data)) {
                $lastInsertId = $this->Referral->getLastInsertId();
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                exit;
            }
        }
        $sexes = array('M' => GENERAL_MALE, 'F' => GENERAL_FEMALE);
        $this->set(compact('sexes'));
    }

    function edit($id = null) {
        $this->layout = 'ajax';
        $this->loadModel("Referral");
        if (!$id && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $dateNow  = date("Y-m-d H:i:s");
            $this->Referral->create();
            $this->data['Referral']['name']       = $this->data['Referral']['name'];
            $this->data['Referral']['sex']        = $this->data['Referral']['sex'];
            $this->data['Referral']['telephone']  = $this->data['Referral']['telephone'];
            $this->data['Referral']['dob']        = $this->data['Referral']['dob'];
            $this->data['Referral']['modified']    = $dateNow;
            $this->data['Referral']['modified_by'] = $user['User']['id'];
            if ($this->Referral->save($this->data)) {
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                exit;
            } else {
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                exit;
            }
        }
        $sexes = array('M' => GENERAL_MALE, 'F' => GENERAL_FEMALE);
        $referral = $this->Referral->find('first', array('fields' => array('Referral.*'), 'conditions' => array('Referral.id' => $id)));
        $this->set(compact('referral','sexes'));
    }

    function delete($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        $sqlCheckWithProduct = mysql_query("SELECT id FROM product_Referrals WHERE pgroup_id = ".$id." LIMIT 1");
        if(mysql_num_rows($sqlCheckWithProduct)){
            $this->Helper->saveUserActivity($user['User']['id'], 'Referral', 'Delete Error Have Child Product');
            echo MESSAGE_DATA_HAVE_CHILD;
            exit;
        }
        $r = 0;
        $restCode = array();
        $dateNow  = date("Y-m-d H:i:s");
        $this->data = $this->Referral->read(null, $id);
        mysql_query("UPDATE `Referrals` SET `is_active`=2, `modified`='".$dateNow."', `modified_by`=".$user['User']['id']." WHERE `id`=".$id.";");
        // Convert to REST
        $restCode[$r]['is_active']   = 2;
        $restCode[$r]['modified']    = $dateNow;
        $restCode[$r]['modified_by'] = $this->Helper->getSQLSyncCode("users", $user['User']['id']);
        $restCode[$r]['dbtodo'] = 'Referrals';
        $restCode[$r]['actodo'] = 'ut';
        $restCode[$r]['con']    = "sys_code = '".$this->data['Referral']['sys_code']."'";
        // Save File Send
        $this->Helper->sendFileToSync($restCode, 0, 0);
        // Save User Activity
        $this->Helper->saveUserActivity($user['User']['id'], 'Referral', 'Delete', $id);
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }
    
    function exportExcel(){
        $this->layout = 'ajax';
        if (isset($_POST['action']) && $_POST['action'] == 'export') {
            $user = $this->getCurrentUser();
            $this->Helper->saveUserActivity($user['User']['id'], 'Referral', 'Export to Excel');
            $filename = "public/report/Referral_export.csv";
            $fp = fopen($filename, "wb");
            $excelContent = 'Referrals' . "\n\n";
            $excelContent .= TABLE_NO . "\t" . TABLE_NAME;
            $query = mysql_query('SELECT id,  name FROM Referrals WHERE is_active=1 ORDER BY name');
            $index = 1;
            while ($data = mysql_fetch_array($query)) {
                $excelContent .= "\n" . $index++ . "\t" . $data[1];
            }
            $excelContent = chr(255) . chr(254) . @mb_convert_encoding($excelContent, 'UTF-16LE', 'UTF-8');
            fwrite($fp, $excelContent);
            fclose($fp);
            exit();
        }
    }
}

?>