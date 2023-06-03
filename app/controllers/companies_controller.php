<?php

class CompaniesController extends AppController {

    var $name = 'Companies';
    var $components = array('Helper');

    function index() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        // User Activity
        $this->Helper->saveUserActivity($user['User']['id'], 'Company', 'Dashborad');
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
        // User Activity
        $this->Helper->saveUserActivity($user['User']['id'], 'Company', 'View', $id);
        $this->data = $this->Company->read(null, $id);
    }

    function add() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            if ($this->Helper->checkDouplicate('name', 'companies', $this->data['Company']['name'])) {
                // User Activity
                $this->Helper->saveUserActivity($user['User']['id'], 'Company', 'Save Add New (Name has existed)');
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $r = 0;
                $restCode = array();
                $dateNow  = date("Y-m-d H:i:s");
                $this->Company->create();
                $this->data['Company']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $this->data['Company']['created']    = $dateNow;
                $this->data['Company']['created_by'] = $user['User']['id'];
                $this->data['Company']['is_active']  = 1;
                if ($this->Company->save($this->data)) {
                    $companyId = $this->Company->id;
                    // Load Model
                    $this->loadModel('Branch');
                    $this->loadModel('ModuleCodeBranch');
                    // Company Photo
                    if ($this->data['Company']['photo'] != '') {
                        $extPhoto  = explode(".", $this->data['Company']['photo']);
                        $photoName = md5($companyId . '_' . date("Y-m-d H:i:s")).".".$extPhoto[1];
                        rename('public/company_photo/tmp/' . $this->data['Company']['photo'], 'public/company_photo/' . $photoName);
                        mysql_query("UPDATE companies SET photo='".$photoName."' WHERE id=" . $companyId);
                        $this->data['Company']['photo'] = $photoName;
                    }
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($this->data['Company'], 'companies');
                    $restCode[$r]['modified'] = $dateNow;
                    $restCode[$r]['dbtodo']   = 'companies';
                    $restCode[$r]['actodo']   = 'is';
                    $r++;
                    // Company with Category
                    if(!empty($this->data['Company']['company_category_id'])){
                        for($i=0;$i<sizeof($this->data['Company']['company_category_id']);$i++){
                            mysql_query("INSERT INTO company_with_categories (company_category_id, company_id) VALUES ('".$this->data['Company']['company_category_id'][$i]."','".$companyId."')");
                            // Convert to REST
                            $restCode[$r]['company_id'] = $this->data['Company']['sys_code'];
                            $restCode[$r]['company_category_id']    = $this->Helper->getSQLSysCode("company_categories",$this->data['Company']['company_category_id'][$i]);
                            $restCode[$r]['dbtodo']     = 'company_with_categories';
                            $restCode[$r]['actodo']     = 'is';
                            $r++;
                        }
                    }
                    // User Company
                    if(!empty($this->data['Company']['user_id'])){
                        for($i=0;$i<sizeof($this->data['Company']['user_id']);$i++){
                            mysql_query("INSERT INTO user_companies (user_id, company_id) VALUES ('".$this->data['Company']['user_id'][$i]."','".$companyId."')");
                            // Convert to REST
                            $restCode[$r]['company_id'] = $this->data['Company']['sys_code'];
                            $restCode[$r]['user_id']    = $this->Helper->getSQLSysCode("users",$this->data['Company']['user_id'][$i]);
                            $restCode[$r]['dbtodo']     = 'user_companies';
                            $restCode[$r]['actodo']     = 'is';
                            $r++;
                        }
                    }
                    // Head Office
                    $this->Branch->create();
                    $this->data['Branch']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                    $this->data['Branch']['company_id'] = $companyId;
                    $this->data['Branch']['currency_center_id'] = $this->data['Company']['currency_center_id'];
                    $this->data['Branch']['created']    = $dateNow;
                    $this->data['Branch']['created_by'] = $user['User']['id'];
                    $this->data['Branch']['is_head']    = 1;
                    $this->data['Branch']['is_active']  = 1;
                    if ($this->Branch->save($this->data)) {
                        $branchId = $this->Branch->id;
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($this->data['Branch'], 'branches');
                        $restCode[$r]['modified']   = $dateNow;
                        $restCode[$r]['dbtodo']     = 'branches';
                        $restCode[$r]['actodo']     = 'is';
                        $r++;
                        // Branch Module Code
                        $this->ModuleCodeBranch->create();
                        $this->data['ModuleCodeBranch']['branch_id'] = $branchId;
                        $this->ModuleCodeBranch->save($this->data);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($this->data['ModuleCodeBranch'], 'module_code_branches');
                        $restCode[$r]['dbtodo'] = 'module_code_branches';
                        $restCode[$r]['actodo'] = 'is';
                        $r++;
                        // User Branch
                        if(!empty($this->data['Company']['user_id'])){
                            for($i=0;$i<sizeof($this->data['Company']['user_id']);$i++){
                                mysql_query("INSERT INTO user_branches (user_id, branch_id) VALUES ('".$this->data['Company']['user_id'][$i]."','".$branchId."')");
                                // Convert to REST
                                $restCode[$r]['branch_id'] = $this->data['Branch']['sys_code'];
                                $restCode[$r]['user_id']   = $this->Helper->getSQLSysCode("users", $this->data['Company']['user_id'][$i]);
                                $restCode[$r]['dbtodo']    = 'user_branches';
                                $restCode[$r]['actodo']    = 'is';
                                $r++;
                            }
                        }
                    }
                    // Save File Send
                    $this->Helper->sendFileToSync($restCode, 0, 0);
                    // Send to E-Commerce
                    // E-Commerce Shop
//                    $this->loadModel('EStoreShare');
//                    $this->EStoreShare->create();
//                    $shop = array();
//                    $shop['EStoreShare']['sys_code']   = $this->data['Company']['sys_code'];
//                    $shop['EStoreShare']['company_id'] = $companyId;
//                    $shop['EStoreShare']['name']       = $this->data['Company']['name'];
//                    $shop['EStoreShare']['telephone']  = $this->data['Branch']['telephone'];
//                    $shop['EStoreShare']['address']    = $this->data['Branch']['address'];
//                    $shop['EStoreShare']['e_mail']     = $this->data['Branch']['email_address'];
//                    $shop['EStoreShare']['website']    = $this->data['Company']['website'];
//                    $shop['EStoreShare']['description'] = $this->data['Company']['description'];
//                    $shop['EStoreShare']['created']    = $dateNow;
//                    $shop['EStoreShare']['created_by'] = $user['User']['id'];
//                    $this->EStoreShare->save($shop);
//                    $e = 0;
//                    $syncEco = array();
                    // Convert to REST
                    // Shop Category SysCode
//                    $sqlSt = mysql_query("SELECT sct_c FROM ".SYNC_PUBLIC."syn_opts WHERE 1;");
//                    $rowSt = mysql_fetch_array($sqlSt);
//                    $syncEco[$e]['sys_code'] = $this->data['Company']['sys_code'];
//                    $syncEco[$e]['description'] = $this->data['Company']['description'];
//                    $syncEco[$e]['name']      = $this->data['Company']['name'];
//                    $syncEco[$e]['photo']     = $this->data['Company']['photo'];
//                    $syncEco[$e]['website']   = $this->data['Company']['website'];
//                    $syncEco[$e]['telephone'] = $this->data['Branch']['telephone'];
//                    $syncEco[$e]['e_mail']    = $this->data['Branch']['email_address'];
//                    $syncEco[$e]['address']   = $this->data['Branch']['address'];
//                    $syncEco[$e]['sct_c']     = $rowSt['sct_c'];
//                    $syncEco[$e]['created']   = $dateNow;
//                    $syncEco[$e]['modified']  = $dateNow;
//                    $syncEco[$e]['status']    = 2;
//                    $syncEco[$e]['dbtodo']    = 'shops';
//                    $syncEco[$e]['actodo']    = 'is';
                    // Shop with Category
//                    if(!empty($this->data['Company']['company_category_id'])){
//                        for($i=0;$i<sizeof($this->data['Company']['company_category_id']);$i++){
//                            $shopCategorySys = $this->Helper->getSQLSysCode("company_categories", $this->data['Company']['company_category_id'][$i]);
//                            // Convert to REST
//                            $syncEco[$e]['shop_id']  = $this->data['Company']['sys_code'];
//                            $syncEco[$e]['shop_category_id'] = $shopCategorySys;
//                            $syncEco[$e]['dbtodo']   = 'shop_with_categories';
//                            $syncEco[$e]['actodo']   = 'is';
//                            $e++;
//                        }
//                    }
                    // Save File Send to E-Commerce
//                    $this->Helper->sendFileToSyncPublic($syncEco);
                    // User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'Company', 'Save Add New', $companyId);
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    // User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'Company', 'Save Add New (Error)');
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        // User Activity
        $this->Helper->saveUserActivity($user['User']['id'], 'Company', 'Add New');
        $companyCategories = ClassRegistry::init('CompanyCategory')->find('list', array("conditions" => array("CompanyCategory.is_active = 1"), "order" => "CompanyCategory.name"));
        $countries = ClassRegistry::init('Country')->find('list', array("conditions" => array("Country.is_active = 1")));
        $branchTypes = ClassRegistry::init('BranchType')->find('list', array('conditions' => array('BranchType.is_active = 1')));
        $currencyCenters = ClassRegistry::init('CurrencyCenter')->find('list', array('conditions' => array('CurrencyCenter.is_active = 1'), "order" => "CurrencyCenter.name"));
        $this->set(compact('currencyCenters', 'countries', 'branchTypes', 'companyCategories'));
    }

    function edit($id = null) {
        $this->layout = 'ajax';
        if (!$id && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            if ($this->Helper->checkDouplicateEdit('name', 'companies', $id, $this->data['Company']['name'])) {
                // User Activity
                $this->Helper->saveUserActivity($user['User']['id'], 'Company', 'Save Edit (Name has existed)', $id);
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $r = 0;
                $restCode = array();
                $dateNow  = date("Y-m-d H:i:s");
                $this->data['Company']['modified'] = $dateNow;
                $this->data['Company']['modified_by'] = $user['User']['id'];
                if ($this->Company->save($this->data)) {
                    // Company photo
                    if ($this->data['Company']['new_photo'] != '') {
                        $extPhoto  = explode(".", $this->data['Company']['new_photo']);
                        $photoName = md5($this->data['Company']['id'] . '_' . date("Y-m-d H:i:s")).".".$extPhoto[1];
                        rename('public/company_photo/tmp/' . $this->data['Company']['new_photo'], 'public/company_photo/' . $photoName);
                        @unlink('public/company_photo/' . $this->data['Company']['old_photo']);
                        mysql_query("UPDATE companies SET photo='" . $photoName . "' WHERE id=" . $this->data['Company']['id']);
                        $this->data['Company']['photo'] = $photoName;
                    }
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($this->data['Company'], 'companies');
                    $restCode[$r]['dbtodo'] = 'companies';
                    $restCode[$r]['actodo'] = 'ut';
                    $restCode[$r]['con']    = "sys_code = '".$this->data['Company']['sys_code']."'";
                    $r++;
                    // Update Currency to Branch
                    if(!empty($this->data['Company']['currency_center_id'])){
                        mysql_query("UPDATE branches SET currency_center_id = ".$this->data['Company']['currency_center_id']." WHERE company_id=".$this->data['Company']['id']);
                        // Convert to REST
                        $restCode[$r]['currency_center_id'] = $this->data['Company']['currency_center_id'];
                        $restCode[$r]['dbtodo'] = 'branches';
                        $restCode[$r]['actodo'] = 'ut';
                        $restCode[$r]['con']    = "company_id = ".$this->data['Company']['sys_code'];
                        $r++;
                    }
                    // Company with Category
                    mysql_query("DELETE FROM company_with_categories WHERE company_id=".$this->data['Company']['id']);
                    // Convert to REST
                    $restCode[$r]['dbtodo'] = 'company_with_categories';
                    $restCode[$r]['actodo'] = 'dt';
                    $restCode[$r]['con']    = "company_id = ".$this->data['Company']['sys_code'];
                    $r++;
                    if(!empty($this->data['Company']['company_category_id'])){
                        for($i=0;$i<sizeof($this->data['Company']['company_category_id']);$i++){
                            mysql_query("INSERT INTO company_with_categories (company_category_id, company_id) VALUES ('".$this->data['Company']['company_category_id'][$i]."','".$this->data['Company']['id']."')");
                            // Convert to REST
                            $restCode[$r]['company_id'] = $this->data['Company']['sys_code'];
                            $restCode[$r]['company_category_id']    = $this->Helper->getSQLSysCode("company_categories",$this->data['Company']['company_category_id'][$i]);
                            $restCode[$r]['dbtodo']     = 'company_with_categories';
                            $restCode[$r]['actodo']     = 'is';
                            $r++;
                        }
                    }
                    // User location
                    mysql_query("DELETE FROM user_companies WHERE company_id=".$this->data['Company']['id']);
                    // Convert to REST
                    $restCode[$r]['dbtodo'] = 'user_companies';
                    $restCode[$r]['actodo'] = 'dt';
                    $restCode[$r]['con']    = "company_id = ".$this->data['Company']['sys_code'];
                    $r++;
                    if(!empty($this->data['Company']['user_id'])){
                        for($i=0;$i<sizeof($this->data['Company']['user_id']);$i++){
                            mysql_query("INSERT INTO user_companies (user_id, company_id) VALUES ('".$this->data['Company']['user_id'][$i]."','".$this->data['Company']['id']."')");
                            // Convert to REST
                            $restCode[$r]['company_id'] = $this->data['Company']['sys_code'];
                            $restCode[$r]['user_id']    = $this->Helper->getSQLSysCode("users",$this->data['Company']['user_id'][$i]);
                            $restCode[$r]['dbtodo']     = 'user_companies';
                            $restCode[$r]['actodo']     = 'is';
                            $r++;
                        }
                    }
                    // Save File Send
                    $this->Helper->sendFileToSync($restCode, 0, 0);
                    // User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'Company', 'Save Edit', $id);
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    // User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'Company', 'Save Edit (Error)', $id);
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        if (empty($this->data)) {
            // User Activity
            $this->Helper->saveUserActivity($user['User']['id'], 'Company', 'Edit', $id);
            $this->data = $this->Company->read(null, $id);
            $categorySellecteds = ClassRegistry::init('CompanyWithCategory')->find('list', array('fields' => array('id', 'company_category_id'), 'order' => 'id', 'conditions' => array('company_id' => $id)));
            $categorySellected = array();
            foreach ($categorySellecteds as $cs) {
                array_push($categorySellected, $cs);
            }
            $companyCategories = ClassRegistry::init('CompanyCategory')->find('list', array("conditions" => array("CompanyCategory.is_active = 1"), "order" => "CompanyCategory.name"));
            $currencyCenters = ClassRegistry::init('CurrencyCenter')->find('list', array('conditions' => array('CurrencyCenter.is_active = 1')));
            $this->set(compact('currencyCenters', 'companyCategories', 'categorySellected'));
        }
    }

    function delete($id = null) {
        $sqlBranch = mysql_query("SELECT id FROM branches WHERE company_id = ".$id." AND is_active = 1 LIMIT 1");
        if (!$id || mysql_num_rows($sqlBranch)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $r = 0;
        $restCode = array();
        $dateNow  = date("Y-m-d H:i:s");
        $user = $this->getCurrentUser();
        $this->data = $this->Company->read(null, $id);
        // User Activity
        $this->Helper->saveUserActivity($user['User']['id'], 'Company', 'Delete', $id);
        mysql_query("UPDATE `companies` SET `is_active`=2, `modified`='".$dateNow."', `modified_by`=".$user['User']['id']." WHERE `id`=".$id.";");
        // Convert to REST
        $restCode[$r]['is_active'] = 2;
        $restCode[$r]['modified']  = $dateNow;
        $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
        $restCode[$r]['dbtodo'] = 'companies';
        $restCode[$r]['actodo'] = 'ut';
        $restCode[$r]['con']    = "sys_code = '".$this->data['Company']['sys_code']."'";
        // Save File Send
        $this->Helper->sendFileToSync($restCode, 0, 0);
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }
    
    function upload() {
        $this->layout = 'ajax';
        if ($_FILES['photo']['name'] != '') {
            $target_folder = 'public/company_photo/tmp/';
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

}

?>