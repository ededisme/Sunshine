<?php

class VendorsController extends AppController {

    var $name = 'Vendors';
    var $components = array('Helper', 'Address');

    function index() {
        $this->layout = "ajax";
        $user = $this->getCurrentUser();
        $this->Helper->saveUserActivity($user['User']['id'], 'Vendor', 'Dashboard');
    }

    function ajax() {
        $this->layout = "ajax";
    }

    function add() {
        $this->layout = "ajax";
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            if ($this->Helper->checkDouplicate('name', 'vendors', $this->data['Vendor']['name'])) {
                $this->Helper->saveUserActivity($user['User']['id'], 'Vendor', 'Save Add New (Name ready existed)');
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $r = 0;
                $restCode  = array();
                $dateNow   = date("Y-m-d H:i:s");
                $this->data['Vendor']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $this->data['Vendor']['created']    = $dateNow;
                $this->data['Vendor']['created_by'] = $user['User']['id'];
                $this->data['Vendor']['is_active']  = 1;
                if ($this->Vendor->saveAll($this->data)) {
                    $lastInsertId = $this->Vendor->getLastInsertId();
                    // Vendor photo
                    if ($this->data['Vendor']['photo'] != '') {
                        $photoName = md5($lastInsertId . '_' . date("Y-m-d H:i:s")).".jpg";;
                        @unlink('public/vendor_photo/tmp/' . $this->data['Vendor']['photo']);
                        rename('public/vendor_photo/tmp/thumbnail/' . $this->data['Vendor']['photo'], 'public/vendor_photo/' . $photoName);
                        mysql_query("UPDATE vendors SET photo='" . $photoName . "' WHERE id=" . $lastInsertId);
                        $this->data['Vendor']['photo'] = $photoName;
                    }
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($this->data['Vendor'], 'vendors');
                    $restCode[$r]['modified']   = $dateNow;
                    $restCode[$r]['dbtodo']     = 'vendors';
                    $restCode[$r]['actodo']     = 'is';
                    $r++;
                    // Vendor Company
                    if (isset($this->data['Vendor']['company_id'])) {
                        mysql_query("INSERT INTO vendor_companies (vendor_id, company_id) VALUES ('" . $lastInsertId . "','" . $this->data['Vendor']['company_id'] . "')");
                        // Convert to REST
                        $restCode[$r]['vendor_id']  = $this->data['Vendor']['sys_code'];
                        $restCode[$r]['company_id'] = $this->Helper->getSQLSysCode("companies", $this->data['Vendor']['company_id']);
                        $restCode[$r]['dbtodo']     = 'vendor_companies';
                        $restCode[$r]['actodo']     = 'is';
                        $r++;
                    }
                    
                    // Vendor Group
                    if(!empty($this->data['Vendor']['vgroup_id'])){
                        mysql_query("INSERT INTO vendor_vgroups (vendor_id,vgroup_id) VALUES ('" . $lastInsertId . "','" . $this->data['Vendor']['vgroup_id'] . "')");
                        // Convert to REST
                        $restCode[$r]['vendor_id'] = $this->data['Vendor']['sys_code'];
                        $restCode[$r]['vgroup_id'] = $this->Helper->getSQLSysCode("vgroups", $this->data['Vendor']['vgroup_id']);
                        $restCode[$r]['dbtodo']    = 'vendor_vgroups';
                        $restCode[$r]['actodo']    = 'is';
                        $r++;
                    }
                    // Save File Send
                    $this->Helper->sendFileToSync($restCode, 0, 0);
                    // Save User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'Vendor', 'Save Add New', $lastInsertId);
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'Vendor', 'Save Add New (Error)');
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        if(empty($this->data)){
            $this->Helper->saveUserActivity($user['User']['id'], 'Vendor', 'Add New');
            $conditionUser = "id IN (SELECT vgroup_id FROM vgroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))";
            $companies = ClassRegistry::init('Company')->find('list', array('order' => 'id', 'conditions' => array('is_active' => 1, 'id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')')));
            $vgroups = ClassRegistry::init('Vgroup')->find('list', array('order' => 'id', 'conditions' => array('is_active' => 1,$conditionUser)));
            $paymentTerms = ClassRegistry::init('PaymentTerm')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'name'));
            $countries = ClassRegistry::init('Country')->find("list");
            $code = $this->Helper->getAutoGenerateVendorCode();
            $this->set(compact('paymentTerms', 'vgroups', "countries", "companies", "code"));
        }
    }

    function edit($id=null) {
        $this->layout = "ajax";
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            if ($this->Helper->checkDouplicateEdit('name', 'vendors', $id, $this->data['Vendor']['name'])) {
                $this->Helper->saveUserActivity($user['User']['id'], 'Vendor', 'Save Edit (Error Name ready existed)', $id);
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $r = 0;
                $restCode  = array();
                $dateNow   = date("Y-m-d H:i:s");
                $this->data['Vendor']['modified']    = $dateNow;
                $this->data['Vendor']['modified_by'] = $user['User']['id'];
                $this->data['Vendor']['is_active']   = 1;
                if ($this->Vendor->saveAll($this->data)) {
                    // Vendor photo
                    if ($this->data['Vendor']['new_photo'] != '') {
                        $photoName = md5($this->data['Vendor']['id'] . '_' . date("Y-m-d H:i:s")).".jpg";;
                        @unlink('public/vendor_photo/tmp/' . $this->data['Vendor']['new_photo']);
                        rename('public/vendor_photo/tmp/thumbnail/' . $this->data['Vendor']['new_photo'], 'public/vendor_photo/' . $photoName);
                        @unlink('public/vendor_photo/' . $this->data['Vendor']['old_photo']);
                        mysql_query("UPDATE vendors SET photo='" . $photoName . "' WHERE id=" . $this->data['Vendor']['id']);
                        $this->data['Vendor']['photo'] = $photoName;
                    }
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($this->data['Vendor'], 'vendors');
                    $restCode[$r]['dbtodo'] = 'vendors';
                    $restCode[$r]['actodo'] = 'ut';
                    $restCode[$r]['con']    = "sys_code = '".$this->data['Vendor']['sys_code']."'";
                    $r++;
                    // Vendor Group
                    mysql_query("DELETE FROM vendor_vgroups WHERE vendor_id=" . $id);
                    // Convert to REST
                    $restCode[$r]['dbtodo'] = 'vendor_vgroups';
                    $restCode[$r]['actodo'] = 'dt';
                    $restCode[$r]['con']    = "vendor_id = ".$this->data['Vendor']['sys_code'];
                    $r++;
                    if (!empty($this->data['Vendor']['vgroup_id'])) {
                        for ($i = 0; $i < sizeof($this->data['Vendor']['vgroup_id']); $i++) {
                            mysql_query("INSERT INTO vendor_vgroups (vendor_id,vgroup_id) VALUES ('" . $id . "','" . $this->data['Vendor']['vgroup_id'][$i] . "')");
                            // Convert to REST
                            $restCode[$r]['vendor_id'] = $this->data['Vendor']['sys_code'];
                            $restCode[$r]['vgroup_id'] = $this->Helper->getSQLSysCode("vgroups", $this->data['Vendor']['vgroup_id'][$i]);
                            $restCode[$r]['dbtodo']    = 'vendor_vgroups';
                            $restCode[$r]['actodo']    = 'is';
                            $r++;
                        }
                    }
                    
                    // Vendor Company
                    mysql_query("DELETE FROM vendor_companies WHERE vendor_id=" . $id);
                    // Convert to REST
                    $restCode[$r]['dbtodo'] = 'vendor_companies';
                    $restCode[$r]['actodo'] = 'dt';
                    $restCode[$r]['con']    = "vendor_id = ".$this->data['Vendor']['sys_code'];
                    $r++;
                    if (isset($this->data['Vendor']['company_id'])) {
                        mysql_query("INSERT INTO vendor_companies (vendor_id, company_id) VALUES ('" . $id . "','" . $this->data['Vendor']['company_id'] . "')");
                        // Convert to REST
                        $restCode[$r]['vendor_id']  = $this->data['Vendor']['sys_code'];
                        $restCode[$r]['company_id'] = $this->Helper->getSQLSysCode("companies", $this->data['Vendor']['company_id']);
                        $restCode[$r]['dbtodo']     = 'vendor_companies';
                        $restCode[$r]['actodo']     = 'is';
                        $r++;
                    }
                    // Save File Send
                    $this->Helper->sendFileToSync($restCode, 0, 0);
                    // Save User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'Vendor', 'Save Edit', $id);
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'Vendor', 'Save Edit (Error)', $id);
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        if (empty($this->data)) {
            $this->Helper->saveUserActivity($user['User']['id'], 'Vendor', 'Edit', $id);
            if($user['User']['id'] == 1){
                $conditionUser = "";
            }else{
                $conditionUser = "id IN (SELECT vgroup_id FROM vgroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))";
            }
            $this->data = $this->Vendor->read(null, $id);
            $paymentTerms = ClassRegistry::init('PaymentTerm')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'name'));
            $vgroupsSellecteds = ClassRegistry::init('VendorVgroup')->find('list', array('fields' => array('id', 'vgroup_id'), 'order' => 'id', 'conditions' => array('vendor_id' => $id)));
            $vgroupsSellected = array();
            foreach ($vgroupsSellecteds as $cs) {
                array_push($vgroupsSellected, $cs);
            }
            $companies = ClassRegistry::init('Company')->find('list', array('order' => 'id', 'conditions' => array('is_active' => 1, 'id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')')));
            $vgroups = ClassRegistry::init('Vgroup')->find('list', array('order' => 'id', 'conditions' => array('is_active' => 1,$conditionUser)));
            $countries = ClassRegistry::init('Country')->find("list");
            $this->set(compact('paymentTerms', 'vgroupsSellected', 'vgroups', 'countries', 'companies'));
        }
    }

    function view($id = null) {
        $this->layout = 'ajax';
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        $this->Helper->saveUserActivity($user['User']['id'], 'Vendor', 'View', $id);
        $this->data = $this->Vendor->read(null, $id);
    }

    function delete($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $r = 0;
        $restCode = array();
        $dateNow  = date("Y-m-d H:i:s");
        $user = $this->getCurrentUser();
        $this->data = $this->Vendor->read(null, $id);
        mysql_query("UPDATE `vendors` SET `is_active`=2, `modified`='".$dateNow."', `modified_by`=".$user['User']['id']." WHERE `id`=".$id.";");
        // Convert to REST
        $restCode[$r]['is_active']   = 2;
        $restCode[$r]['modified']    = $dateNow;
        $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
        $restCode[$r]['dbtodo'] = 'vendors';
        $restCode[$r]['actodo'] = 'ut';
        $restCode[$r]['con']    = "sys_code = '".$this->data['Vendor']['sys_code']."'";
        // Save File Send
        $this->Helper->sendFileToSync($restCode, 0, 0);
        // Save User Activity
        $this->Helper->saveUserActivity($user['User']['id'], 'Vendor', 'Delete', $id);
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }

    function searchVendor() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $userPermission = 'Vendor.id IN (SELECT vendor_id FROM vendor_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id ='.$user['User']['id'].'))';
        $vendors = $this->Vendor->find('all', array(
                    'conditions' => array('OR' => array(
                            'Vendor.name LIKE' => '%'.$this->params['url']['q'].'%',
                            'Vendor.vendor_code LIKE' => '%'.$this->params['url']['q'].'%',
                        ), 'Vendor.is_active' => 1, $userPermission
                    ),
                ));
        $this->set(compact('vendors'));
    }
    
    function exportExcel(){
        $this->layout = 'ajax';
        if (isset($_POST['action']) && $_POST['action'] == 'export') {
            $user = $this->getCurrentUser();
            $this->Helper->saveUserActivity($user['User']['id'], 'Vendor', 'Export to Excel');
            $filename = "public/report/vendor_export.csv";
            $fp = fopen($filename, "wb");
            $excelContent = 'Vendors' . "\n\n";
            $excelContent .= TABLE_NO . "\t" . TABLE_VENDOR_GROUP. "\t" . TABLE_CODE. "\t" . TABLE_NAME. "\t" . TABLE_TELEPHONE_WORK. "\t" . TABLE_TELEPHONE_OTHER. "\t" . TABLE_FAX. "\t" . TABLE_EMAIL;
            $conditionUser = " AND id IN (SELECT vendor_id FROM vendor_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))";
            $query = mysql_query('SELECT id, (SELECT GROUP_CONCAT(name) FROM companies WHERE id IN (SELECT company_id FROM vendor_companies WHERE vendor_id = vendors.id)), (SELECT GROUP_CONCAT(name) FROM vgroups WHERE id IN (SELECT vgroup_id FROM vendor_vgroups WHERE vendor_id = vendors.id)), vendor_code, name, work_telephone, other_number, fax_number, email_address '
                    . '           FROM vendors WHERE is_active=1'.$conditionUser.' ORDER BY name');
            $index = 1;
            while ($data = mysql_fetch_array($query)) {
                $excelContent .= "\n" . $index++ . "\t" . $data[2]. "\t" . $data[3]. "\t" . $data[4]. "\t" . $data[5]. "\t" . $data[6]. "\t" . $data[7]. "\t" . $data[8];
            }
            $excelContent = chr(255) . chr(254) . @mb_convert_encoding($excelContent, 'UTF-16LE', 'UTF-8');
            fwrite($fp, $excelContent);
            fclose($fp);
            exit();
        }
    }
    
    function upload() {
        $this->layout = 'ajax';
        if ($_FILES['photo']['name'] != '') {
            $target_folder = 'public/vendor_photo/tmp/';
            $ext = explode(".", $_FILES['photo']['name']);
            $target_name = rand() . '.' . $ext[sizeof($ext) - 1];
            move_uploaded_file($_FILES['photo']['tmp_name'], $target_folder . $target_name);
            if (isset($_SESSION['pos_photo']) && $_SESSION['pos_photo'] != '') {
                @unlink($target_folder . $_SESSION['pos_photo']);
            }
            echo $_SESSION['pos_photo'] = $target_name;
            exit();
        }
    }

    function cropPhoto() {
        $this->layout = 'ajax';

        // Function
        include('includes/function.php');

        $_POST['photoFolder'] = str_replace("|||", "/", $_POST['photoFolder']);
        list($ImageWidth, $ImageHeight, $TypeCode) = getimagesize($_POST['photoFolder'] . $_POST['photoName']);
        $ImageType = ($TypeCode == 1 ? "gif" : ($TypeCode == 2 ? "jpeg" : ($TypeCode == 3 ? "png" : ($TypeCode == 6 ? "bmp" : FALSE))));
        $CreateFunction = "imagecreatefrom" . $ImageType;
        $OutputFunction = "image" . $ImageType;
        if ($ImageType) {
            $ImageSource = $CreateFunction($_POST['photoFolder'] . $_POST['photoName']);
            $ResizedImage = imagecreatetruecolor($_POST['w'], $_POST['h']);
            imagecopyresampled($ResizedImage, $ImageSource, 0, 0, $_POST['x'], $_POST['y'], $ImageWidth, $ImageHeight, $ImageWidth, $ImageHeight);
            imagejpeg($ResizedImage, $_POST['photoFolder'] . $_POST['photoName'], 100);
            // Rename
            $target_folder = 'public/vendor_photo/tmp/';
            $target_thumbnail = 'public/vendor_photo/tmp/thumbnail/';
            $ext = explode(".", $_POST['photoName']);
            $target_name = rand() . '.' . $ext[sizeof($ext) - 1];
            Resize($_POST['photoFolder'], $_POST['photoName'], $target_folder, $target_name, $_POST['w'], $_POST['h'], 100, true);
            Resize($_POST['photoFolder'], $_POST['photoName'], $target_thumbnail, $target_name, 300, 300, 100, true);
            @unlink($target_folder . $_POST['photoName']);
        }
        echo $target_name;
        exit();
    }
    
    function addVgroup(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $this->loadModel('Vgroup');
            $result = array();
            $comCheck = $this->data['Vgroup']['company_id'];
            if ($this->Helper->checkDouplicate('name', 'vgroups', $this->data['Vgroup']['name'], 'is_active = 1 AND id IN (SELECT vgroup_id FROM vgroup_companies WHERE company_id IN ('.$comCheck.'))')) {
                $this->Helper->saveUserActivity($user['User']['id'], 'Vendor Group', 'Save Quick Add New (Error Name ready existed)');
                $result['error'] = 2;
                echo json_encode($result);
                exit;
            } else {
                $r = 0;
                $restCode  = array();
                $dateNow   = date("Y-m-d H:i:s");
                $this->Vgroup->create();
                $this->data['Vgroup']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $this->data['Vgroup']['created']    = $dateNow;
                $this->data['Vgroup']['created_by'] = $user['User']['id'];
                $this->data['Vgroup']['is_active']  = 1;
                if ($this->Vgroup->save($this->data)) {
                    $lastInsertId = $this->Vgroup->getLastInsertId();
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($this->data['Vgroup'], 'vgroups');
                    $restCode[$r]['modified']   = $dateNow;
                    $restCode[$r]['dbtodo']     = 'vgroups';
                    $restCode[$r]['actodo']     = 'is';
                    $r++;
                    // vgroup company
                    if (isset($this->data['Vgroup']['company_id'])) {
                        mysql_query("INSERT INTO vgroup_companies (vgroup_id, company_id) VALUES ('" . $lastInsertId . "','" . $this->data['Vgroup']['company_id'] . "')");
                        // Convert to REST
                        $restCode[$r]['vgroup_id']  = $this->data['Vgroup']['sys_code'];
                        $restCode[$r]['company_id'] = $this->Helper->getSQLSysCode("companies", $this->data['Vgroup']['company_id']);
                        $restCode[$r]['dbtodo']     = 'vgroup_companies';
                        $restCode[$r]['actodo']     = 'is';
                        $r++;
                    }
                    // Save File Send
                    $this->Helper->sendFileToSync($restCode, 0, 0);
                    // Save User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'Vendor Group', 'Save Quick Add New', $lastInsertId);
                    $result['error']  = 0;
                    $result['option'] = '<option value="">'.INPUT_SELECT.'</option>';
                    $vgroups = ClassRegistry::init('Vgroup')->find('all', array('order' => 'name', 'conditions' => array('is_active' => 1)));
                    foreach($vgroups AS $vgroup){
                        $selected = '';
                        if($vgroup['Vgroup']['id'] == $lastInsertId){
                            $selected = 'selected="selected"';
                        }
                        $result['option'] .= '<option value="'.$vgroup['Vgroup']['id'].'" '.$selected.'>'.$vgroup['Vgroup']['name'].'</option>';
                    }
                    echo json_encode($result);
                    exit;
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'Vendor Group', 'Save Quick Add New (Error)');
                    $result['error'] = 1;
                    echo json_encode($result);
                    exit;
                }
            }
        }
        $this->Helper->saveUserActivity($user['User']['id'], 'Vendor Group', 'Quick Add New');
        $companies = ClassRegistry::init('Company')->find('list', array('order' => 'id', 'conditions' => array('is_active' => 1, 'id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')')));
        $this->set(compact("companies"));
    }
    
    function addTerm(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $this->loadModel('PaymentTerm');
            $result = array();
            if ($this->Helper->checkDouplicate('name', 'payment_terms', $this->data['PaymentTerm']['name'])) {
                $this->Helper->saveUserActivity($user['User']['id'], 'Payment Term', 'Save Quick Add New (Name ready existed)');
                $result['error'] = 2;
                echo json_encode($result);
                exit;
            } else {
                $r = 0;
                $restCode = array();
                $dateNow  = date("Y-m-d H:i:s");
                $this->PaymentTerm->create();
                $this->data['PaymentTerm']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $this->data['PaymentTerm']['created']    = $dateNow;
                $this->data['PaymentTerm']['created_by'] = $user['User']['id'];
                $this->data['PaymentTerm']['is_active'] = 1;
                if ($this->PaymentTerm->save($this->data)) {
                    $termId = $this->PaymentTerm->id;
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($this->data['PaymentTerm'], 'payment_terms');
                    $restCode[$r]['modified']   = $dateNow;
                    $restCode[$r]['dbtodo']     = 'payment_terms';
                    $restCode[$r]['actodo']     = 'is';
                    // Save File Send
                    $this->Helper->sendFileToSync($restCode, 0, 0);
                    // Save User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'Payment Term', 'Save Quick Add New', $termId);
                    $result['error']  = 0;
                    $result['option'] = '<option value="">'.INPUT_SELECT.'</option>';
                    $terms = ClassRegistry::init('PaymentTerm')->find('all', array('order' => 'name', 'conditions' => array('is_active' => 1)));
                    foreach($terms AS $term){
                        $selected = '';
                        if($term['PaymentTerm']['id'] == $termId){
                            $selected = 'selected="selected"';
                        }
                        $result['option'] .= '<option value="'.$term['PaymentTerm']['id'].'" '.$selected.'>'.$term['PaymentTerm']['name'].'</option>';
                    }
                    echo json_encode($result);
                    exit;
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'Payment Term', 'Save Quick Add New (Error)');
                    $result['error'] = 1;
                    echo json_encode($result);
                    exit;
                }
            }
        }
        $this->Helper->saveUserActivity($user['User']['id'], 'Payment Term', 'Quick Add New');
    }
    
    function quickAdd(){
        $this->layout = "ajax";
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $result = array();
            if ($this->Helper->checkDouplicate('name', 'vendors', $this->data['Vendor']['name'])) {
                $this->Helper->saveUserActivity($user['User']['id'], 'Vendor', 'Save Quick Add New (Name ready existed)');
                $result['error'] = 2;
                echo json_encode($result);
                exit;
            } else {
                $r = 0;
                $restCode  = array();
                $dateNow   = date("Y-m-d H:i:s");
                $this->data['Vendor']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $this->data['Vendor']['created']    = $dateNow;
                $this->data['Vendor']['created_by'] = $user['User']['id'];
                $this->data['Vendor']['is_active']  = 1;
                if ($this->Vendor->saveAll($this->data)) {
                    $lastInsertId = $this->Vendor->getLastInsertId();
                    $result['error'] = 0;
                    $result['id']    = $lastInsertId;
                    $result['name']  = $this->data['Vendor']['vendor_code']." - ".$this->data['Vendor']['name'];
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($this->data['Vendor'], 'vendors');
                    $restCode[$r]['modified']   = $dateNow;
                    $restCode[$r]['dbtodo']     = 'vendors';
                    $restCode[$r]['actodo']     = 'is';
                    $r++;
                    // Vendor Group
                    if(!empty($this->data['Vendor']['vgroup_id'])){
                        mysql_query("INSERT INTO vendor_vgroups (vendor_id,vgroup_id) VALUES ('" . $lastInsertId . "','" . $this->data['Vendor']['vgroup_id'] . "')");
                        // Convert to REST
                        $restCode[$r]['vendor_id'] = $this->data['Vendor']['sys_code'];
                        $restCode[$r]['vgroup_id'] = $this->Helper->getSQLSysCode("vgroups", $this->data['Vendor']['vgroup_id']);
                        $restCode[$r]['dbtodo']    = 'vendor_vgroups';
                        $restCode[$r]['actodo']    = 'is';
                        $r++;
                    }
                    // Vendor Company
                    if (isset($this->data['Vendor']['company_id'])) {
                        mysql_query("INSERT INTO vendor_companies (vendor_id, company_id) VALUES ('" . $lastInsertId . "','" . $this->data['Vendor']['company_id'] . "')");
                        // Convert to REST
                        $restCode[$r]['vendor_id']  = $this->data['Vendor']['sys_code'];
                        $restCode[$r]['company_id'] = $this->Helper->getSQLSysCode("companies", $this->data['Vendor']['company_id']);
                        $restCode[$r]['dbtodo']     = 'vendor_companies';
                        $restCode[$r]['actodo']     = 'is';
                        $r++;
                    }
                    // Save File Send
                    $this->Helper->sendFileToSync($restCode, 0, 0);
                    // Save User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'Vendor', 'Save Quick Add New', $lastInsertId);
                    echo json_encode($result);
                    exit;
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'Vendor', 'Save Quick Add New (Error)');
                    $result['error'] = 1;
                    echo json_encode($result);
                    exit;
                }
            }
        }
        if(empty($this->data)){
            $this->Helper->saveUserActivity($user['User']['id'], 'Vendor', 'Quick Add New');
            $conditionUser = "id IN (SELECT vgroup_id FROM vgroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))";
            $companies = ClassRegistry::init('Company')->find('list', array('order' => 'id', 'conditions' => array('is_active' => 1, 'id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')')));
            $vgroups = ClassRegistry::init('Vgroup')->find('list', array('order' => 'id', 'conditions' => array('is_active' => 1,$conditionUser)));
            $paymentTerms = ClassRegistry::init('PaymentTerm')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'name'));
            $code = $this->Helper->getAutoGenerateVendorCode();
            $this->set(compact('paymentTerms', 'vgroups', "companies", "code"));
        }
    }

}

?>