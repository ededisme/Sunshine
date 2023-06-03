<?php

class UsersController extends AppController {

    var $name = 'Users';
    var $components = array('Helper', 'AutoId', 'Inventory', 'Billing');
    
    function connection(){
        $this->layout = 'ajax';
        $modified = $this->data['modified'];
        $result   = array();
        $cache    = ClassRegistry::init('CacheData')->find('first', array('conditions' => array('CacheData.type' => 'Products')));
        if(!empty($modified)){
            if(strtotime($modified) < strtotime($cache['CacheData']['modified'])){
                $user = $this->getCurrentUser();
                $joinProductBranch = array('table' => 'product_branches', 'type' => 'INNER', 'alias' => 'ProductBranch', 'conditions' => array('ProductBranch.product_id = Product.id', 'ProductBranch.branch_id IN (SELECT branch_id FROM user_branches WHERE user_id = '.$user['User']['id'].')'));
                $joinProductgroup  = array('table' => 'product_pgroups', 'type' => 'INNER', 'alias' => 'ProductPgroup', 'conditions' => array('ProductPgroup.product_id = Product.id'));
                $joinPgroup = array('table' => 'pgroups', 'type' => 'INNER', 'alias' => 'Pgroup', 'conditions' => array('Pgroup.id = ProductPgroup.pgroup_id', '(Pgroup.user_apply = 0 OR (Pgroup.user_apply = 1 AND Pgroup.id IN (SELECT pgroup_id FROM user_pgroups WHERE user_id = '.$user['User']['id'].')))'));
                $joinUom    = array('table' => 'uoms', 'type' => 'INNER', 'alias' => 'Uom','conditions' => array('Uom.id = Product.price_uom_id'));
                $joins      = array($joinProductgroup, $joinPgroup, $joinProductBranch, $joinUom);
                $products   = ClassRegistry::init('Product')->find('all', array(
                                'fields' => array('Product.id', 'Product.code', 'Product.barcode', 'Product.name', 'Product.photo', 'Uom.id', 'Uom.abbr'),
                                'conditions' => array('Product.company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')', 'Product.is_active' => 1, '(Product.price_uom_id IS NOT NULL AND Product.is_packet = 0)'),
                                'joins' => $joins,
                                'group' => array('Product.id')));
                $i = 0;
                foreach($products AS $product){
                    $sqlBranch = mysql_query("SELECT GROUP_CONCAT(branch_id) FROM product_branches WHERE product_id = ".$product['Product']['id']." AND branch_id IN (SELECT branch_id FROM user_branches WHERE user_id = ".$user['User']['id'].")");
                    $rowBranch = mysql_fetch_array($sqlBranch);
                    $photo = "img/button/no-images.png";
                    if($product['Product']['photo'] != ""){
                        $photo = "public/product_photo/".$product['Product']['photo'];
                    }
                    // Price
                    $priceTypeId = '';
                    $sqlPType = mysql_query("SELECT price_type_id FROM pos_price_types WHERE company_id = 1 AND is_active = 1 LIMIT 1;");
                    if(mysql_num_rows($sqlPType)){
                        $rowPType = mysql_fetch_array($sqlPType);
                        $priceTypeId = $rowPType[0];
                    }
                    $price = 0;
                    $sqlPrice = mysql_query("SELECT products.unit_cost, product_prices.price_type_id, product_prices.amount, product_prices.percent, product_prices.add_on, product_prices.set_type FROM product_prices INNER JOIN products ON products.id = product_prices.product_id WHERE product_prices.product_id =".$product['Product']['id']." AND price_type_id = ".$priceTypeId." AND product_prices.branch_id IN (SELECT branch_id FROM user_branches WHERE user_id = ".$user['User']['id'].") AND product_prices.uom_id =".$product['Uom']['id']." LIMIT 1");
                    if(mysql_num_rows($sqlPrice)){
                        while($rowPrice = mysql_fetch_array($sqlPrice)){
                            $unitCost = $this->Helper->replaceThousand(number_format($rowPrice['unit_cost'] /  1, 2));
                            if($rowPrice['set_type'] == 1){
                                $price = $rowPrice['amount'];
                            }else if($rowPrice['set_type'] == 2){
                                $percent = ($unitCost * $rowPrice['percent']) / 100;
                                $price = $unitCost + $percent;
                            }else if($rowPrice['set_type'] == 3){
                                $price = $unitCost + $rowPrice['add_on'];
                            }
                        }
                    }
                    $result['Product'][$i]["branch_id"]  = $rowBranch[0];
                    $result['Product'][$i]["sku"]        = $product['Product']['code'];
                    $result['Product'][$i]["upc"]        = $product['Product']['barcode'];
                    $result['Product'][$i]["name"]       = $product['Product']['name'];
                    $result['Product'][$i]["uom"]        = $product['Uom']['abbr'];
                    $result['Product'][$i]["uom_id"]     = $product['Uom']['id'];
                    $result['Product'][$i]["price"]      = $price;
                    $result['Product'][$i]["icon"]       = $photo;
                    $i++;
                    $productSku = mysql_query("SELECT sku, uoms.abbr, uom_id FROM product_with_skus INNER JOIN uoms ON uoms.id = product_with_skus.uom_id WHERE product_id = '".$product['Product']['id']."'");
                    while($rowSku = mysql_fetch_array($productSku)){
                        $price = 0;
                        $sqlPrice = mysql_query("SELECT products.unit_cost, product_prices.price_type_id, product_prices.amount, product_prices.percent, product_prices.add_on, product_prices.set_type FROM product_prices INNER JOIN products ON products.id = product_prices.product_id WHERE product_prices.product_id =".$product['Product']['id']." AND price_type_id = ".$priceTypeId." AND product_prices.branch_id IN (SELECT branch_id FROM user_branches WHERE user_id = ".$user['User']['id'].") AND product_prices.uom_id =".$rowSku['uom_id']." LIMIT 1");
                        if(mysql_num_rows($sqlPrice)){
                            while($rowPrice = mysql_fetch_array($sqlPrice)){
                                $unitCost = $this->Helper->replaceThousand(number_format($rowPrice['unit_cost'] /  1, 2));
                                if($rowPrice['set_type'] == 1){
                                    $price = $rowPrice['amount'];
                                }else if($rowPrice['set_type'] == 2){
                                    $percent = ($unitCost * $rowPrice['percent']) / 100;
                                    $price = $unitCost + $percent;
                                }else if($rowPrice['set_type'] == 3){
                                    $price = $unitCost + $rowPrice['add_on'];
                                }
                            }
                        }
                        $result['Product'][$i]["branch_id"]  = $rowBranch[0];
                        $result['Product'][$i]["sku"]        = $rowSku['sku'];
                        $result['Product'][$i]["upc"]        = $product['Product']['barcode'];
                        $result['Product'][$i]["name"]       = $product['Product']['name'];
                        $result['Product'][$i]["uom"]        = $rowSku['abbr'];
                        $result['Product'][$i]["uom_id"]     = $rowSku['uom_id'];
                        $result['Product'][$i]["price"]      = $price;
                        $result['Product'][$i]["icon"]       = $photo;
                        $i++;
                    }
                    $result['modified'] = date("Y-m-d H:i:s");
                }
            }
        }
        echo json_encode($result);
        exit();
    }

    function lang($lang = 'en') {
        $this->Session->write('lang', $lang);
        $this->redirect($this->getDefaultPage());
    }

    function checkDuplicate() {
        /* RECEIVE VALUE */
        $validateValue = $_GET['fieldValue'];
        $validateId = $_GET['fieldId'];

        $strTbl = $_GET['tableName'];
        $strCol = $_GET['fieldName'];
        $strCondition = $_GET['fieldCondition'];
        $condition = "id!='" . $_GET['fieldCurrentId'] . "' AND " . ($strCondition != '' ? $strCondition : 1);

        /* RETURN VALUE */
        $arrayToJs = array();
        $arrayToJs[0] = $validateId;

        $queryUser = mysql_query("SELECT " . $strCol . " FROM " . $strTbl . " WHERE " . $condition . " AND " . $strCol . "='" . mysql_real_escape_string($validateValue) . "'");
        if (!mysql_num_rows($queryUser)) {  // validate??
            $arrayToJs[1] = true;   // RETURN TRUE
            echo json_encode($arrayToJs);   // RETURN ARRAY WITH success
        } else {
            for ($x = 0; $x < 1000000; $x++) {
                if ($x == 990000) {
                    $arrayToJs[1] = false;
                    echo json_encode($arrayToJs);  // RETURN ARRAY WITH ERROR
                }
            }
        }
        exit();
    }

    function checkDuplicate2() {
        /* RECEIVE VALUE */
        $validateValue = $_GET['fieldValue'];
        $validateId = $_GET['fieldId'];

        $strTbl = $_GET['tableName2'];
        $strCol = $_GET['fieldName2'];
        $strCondition = $_GET['fieldCondition2'];
        $condition = "id!='" . $_GET['fieldCurrentId2'] . "' AND " . ($strCondition != '' ? $strCondition : 1);

        /* RETURN VALUE */
        $arrayToJs = array();
        $arrayToJs[0] = $validateId;

        $queryUser = mysql_query("SELECT " . $strCol . " FROM " . $strTbl . " WHERE " . $condition . " AND " . $strCol . "='" . mysql_real_escape_string($validateValue) . "'");
        if (!mysql_num_rows($queryUser)) {  // validate??
            $arrayToJs[1] = true;   // RETURN TRUE
            echo json_encode($arrayToJs);   // RETURN ARRAY WITH success
        } else {
            for ($x = 0; $x < 1000000; $x++) {
                if ($x == 990000) {
                    $arrayToJs[1] = false;
                    echo json_encode($arrayToJs);  // RETURN ARRAY WITH ERROR
                }
            }
        }
        exit();
    }

    function login() {
        // Check System Config
        $access = true;
        $config = "";
        $fileConfig = "config/system_config.fg";
        if (file_exists($fileConfig)) {
            $handle   = fopen($fileConfig, "r");
            $contents = fread($handle, filesize($fileConfig));
            fclose($handle);
            $config   = $contents;
        }
        if($config == "" || $config == "{}") {
            $access = false;
        }else{
            $array = json_decode($config, true);
            if(empty($array)){
                $access = false;
            }
        }
        if($access == false){
            $this->redirect(array('controller' => 'users', 'action' => 'systemConfig'));
            exit;
        }
        
        // Billing
        $db = ConnectionManager::getDataSource('default');
        mysql_connect($db->config['host'], $db->config['login'], $db->config['password']);
        mysql_select_db($db->config['database']);
        // Receive User From Billing
        $this->Billing->userRetreive();
        // Set SysCode and Send to API 
        $sqlUser = mysql_query("SELECT id, sys_code, first_name, last_name, username, password, expired, is_hash, is_sync FROM users WHERE 1");
        while($rowUser = mysql_fetch_array($sqlUser)){
            if($rowUser['sys_code'] == ""){
                $sysCode = md5(SYSTEM_CODE.rand().strtotime(date("Y-m-d H:i:s")));
                mysql_query("UPDATE users SET sys_code = '".$sysCode."' WHERE id = ".$rowUser['id']);
                $rowUser['sys_code'] = $sysCode;
            }
            if($rowUser['password'] == "273ad0d33586ddbdb7536761cc3daad0"){ // Check If password default = 1
                // Change password to Bcrypt
                $options = array(
                    'cost' => 10,
                );
                $password = "1";
                $password_hash = password_hash($password, PASSWORD_BCRYPT, $options);
                $newPassword   = str_replace("$2y$", "$2a$", $password_hash);
                mysql_query("UPDATE users SET password = '".$newPassword."', is_hash = 1 WHERE id = ".$rowUser['id']);
                $rowUser['is_hash']  = 1;
                $rowUser['password'] = $newPassword;        
            }
            if($rowUser['sys_code'] != "" && $rowUser['is_hash'] == 1 && $rowUser['is_sync'] == 0){
                // Sync to cloud
                $this->Billing->sendUserToApi($rowUser['sys_code'], $rowUser['first_name'], $rowUser['last_name'], $rowUser['username'], $rowUser['password'], $rowUser['expired'], 2, 8);
            }
        }
        // End Billing
        
        // Redirect when already logged in
        if ($this->Session->check('User')) {
            $user = $this->getCurrentUser();
            if(!empty($user)){
                $query = mysql_query("SELECT session_active FROM users WHERE id=" . $user['User']['id'] . " AND session_id='".$this->Session->id(session_id())."'");
                if (@mysql_num_rows($query)) {
                    $this->redirect($this->getDefaultPage($user['User']['id']));
                }
            }
        }
        $this->layout = 'login';
        if (!empty($this->data)) {
            if(empty($this->data['User']['username']) || empty($this->data['User']['password'])){
                $this->Session->setFlash(__('Invalid.', true), 'flash_failure');
            } else {
                $useragent = $_SERVER['HTTP_USER_AGENT'];
                require_once('captcha/securimage.php');
                
//                $user = $this->User->find('first', array(
//                            'conditions' => array(
//                                'User.username' => $this->data['User']['username'],
//                                'User.password' => md5(Configure::read('Security.salt') . $this->data['User']['password'] . Configure::read('Security.cipherSeed')),
//                                'User.is_active' => 1
//                            )
//                        ));
                
                // Billing
                $sqlChk = mysql_query("SELECT id FROM users WHERE is_active = 1 AND is_sync = 0 LIMIT 1");
                if(!mysql_num_rows($sqlChk)){
                    // Check Login with Billing
                    $login = $this->Billing->userLogin($this->data['User']['username'], $this->data['User']['password']);
                    if(!empty($login)){
                        if($login['status'] == 1 || $login['status'] == 2){ // Success
                            $user = $this->User->find('first', array('conditions' => array('User.username' => $this->data['User']['username'], 'User.is_active' => 1)));
                            $user['User']['expired'] = $login['exp'];
                        } else { // Invalid Username and Password
                            $user = null;
                        }
                    }
                } else {
                    $checkUser = $this->User->find('first', array('conditions' => array(
                                'User.username' => $this->data['User']['username'],
                                'User.is_active' => 1)));
                    if($checkUser['User']['is_hash'] == 0){
                        $user = $this->User->find('first', array(
                                    'conditions' => array(
                                        'User.username' => $this->data['User']['username'],
                                        'User.password' => md5(Configure::read('Security.salt') . $this->data['User']['password'] . Configure::read('Security.cipherSeed')),
                                        'User.is_active' => 1
                                    )
                                ));
                        // Change password to Bcrypt
                        $options = array(
                            'cost' => 10,
                        );
                        $password = mysql_real_escape_string($this->data['User']['password']);
                        $password_hash = password_hash($password, PASSWORD_BCRYPT, $options);
                        $newPassword   = str_replace("$2y$", "$2a$", $password_hash);
                        mysql_query("UPDATE users SET password = '".$newPassword."', is_hash = 1 WHERE id = ".$checkUser['User']['id']);
                        // Sync to cloud
                        if($user['User']['sys_code'] != "" && $user['User']['is_sync'] == 0){
                            $this->Billing->sendUserToApi($user['User']['sys_code'], $user['User']['first_name'], $user['User']['last_name'], $user['User']['username'], $newPassword, $user['User']['expired'], 2, 8);
                        }
                    } else {
                        if($checkUser['User']['is_sync'] == 1){
                            // Check Login with Billing
                            $login = $this->Billing->userLogin($this->data['User']['username'], $this->data['User']['password']);
                            if(!empty($login)){
                                if($login['status'] == 1 || $login['status'] == 2){ // Success
                                    $user = $this->User->find('first', array('conditions' => array('User.username' => $this->data['User']['username'], 'User.is_active' => 1)));
                                    $user['User']['expired'] = $login['exp'];
                                } else { // Invalid Username and Password
                                    $user = null;
                                }
                            }
                        } else {
                            // Check Login with local
                            $hash = str_replace("$2a$", "$2y$", $checkUser['User']['password']);
                            $password = mysql_real_escape_string($this->data['User']['password']);
                            if (password_verify($password, $hash)) {
                                $user = $checkUser;
                            } else {
                                $user = null;
                            }
                        }
                    }
                }
                // End Billing
                
                $img = new Securimage();
                $valid = true;
                $log = $this->Session->read('log');
                if ($log >= 3) {
                    if (empty($this->data['User']['code'])) {
                        $valid = false;
                    } else {
                        $valid = $img->check($this->data['User']['code']);
                    }
                }
                if ($valid) {
                    if (!empty($user)) {
                        if ($user['User']['session_active'] != "" && date("Y-m-d H:i:s", strtotime("+15 minutes", strtotime($user['User']['session_active']))) >= date("Y-m-d H:i:s")) {
                            // Detech session
                            $this->Session->setFlash(__('This username already login on other device.', true), 'flash_failure');
                        } else if (strtotime($user['User']['expired']) < strtotime(date("Y-m-d"))) {
                            // Detech expired
                            $this->Session->setFlash(__('This user is already expired.<br/> Please contact us 023/081 881 887', true), 'flash_failure');
                        } else {
                            $dateNow  = date("Y-m-d H:i:s");
                            // User
                            $UserAct = array();
                            $UserAct['User']['id'] = $user['User']['id'];
                            $UserAct['User']['session_id'] = $this->Session->id(session_id());
                            $UserAct['User']['session_start'] = date("Y-m-d H:i:s");
                            $UserAct['User']['session_active'] = date("Y-m-d H:i:s");
                            $UserAct['User']['session_lat'] = $this->data['User']['lat'];
                            $UserAct['User']['session_long'] = $this->data['User']['long'];
                            $UserAct['User']['session_accuracy'] = $this->data['User']['accuracy'];
                            $UserAct['User']['login_attempt_remote_ip'] = $this->Helper->getIpAddress();
                            $UserAct['User']['login_attempt_http_user_agent'] = "OS: ".$this->Helper->getOS($useragent)." Browser: ".$this->Helper->getBrowser($useragent);
                            $this->User->save($UserAct);
                            // User Log
                            $UserLog = array();
                            $this->loadModel('UserLog');
                            $this->UserLog->create();
                            $UserLog['UserLog']['user_id'] = $user['User']['id'];
                            $UserLog['UserLog']['type'] = 'Login';
                            $UserLog['UserLog']['http_user_agent'] = "OS: ".$this->Helper->getOS($useragent)." Browser: ".$this->Helper->getBrowser($useragent);
                            $UserLog['UserLog']['remote_addr'] = $this->Helper->getIpAddress();
                            $UserLog['UserLog']['lat'] = $this->data['User']['lat'];
                            $UserLog['UserLog']['long'] = $this->data['User']['long'];
                            $this->UserLog->save($UserLog);
                            $logID = $this->UserLog->id;
                            // User Actvity Log
                            $useragent = $_SERVER['HTTP_USER_AGENT'];
                            $browser = $this->Helper->getBrowser($useragent);
                            $os      = $this->Helper->getOS($useragent);
                            $ipAddr  = $this->Helper->getIpAddress();
                            $this->loadModel('UserActivityLog');
                            $this->UserActivityLog->create();
                            $UserActLog = array();
                            $UserActLog['UserActivityLog']['user_id'] = $user['User']['id'];
                            $UserActLog['UserActivityLog']['type'] = 'Login';
                            $UserActLog['UserActivityLog']['tbl_from_id'] = $logID;
                            $UserActLog['UserActivityLog']['tbl_to_id'] = 0;
                            $UserActLog['UserActivityLog']['action'] = "Login";
                            $UserActLog['UserActivityLog']['browser'] = $browser;
                            $UserActLog['UserActivityLog']['operating_system'] = $os;
                            $UserActLog['UserActivityLog']['ip'] = $ipAddr;
                            $this->UserActivityLog->save($UserActLog);
                            // Set Session
                            $this->Session->delete('log');
                            $this->setCurrentUser($user);
                            // Redirect
                            $this->redirect($this->getDefaultPage($user['User']['id']));
                        }
                    } else {
                        $this->Session->write('log', $log + 1);
                        $this->Session->setFlash(__('Invalid User Name or Password.', true), 'flash_failure');
                    }
                } else {
                    $this->Session->setFlash(__('Invalid Code.', true), 'flash_failure');
                }
            }
        }
        $this->set('log', $this->Session->read('log'));
    }

    function logout() {
        $user = $this->getCurrentUser();
        $r = 0;
        $restCode = array();
        $dateNow  = date("Y-m-d H:i:s");
        // Create log
        if ($user['User']['id'] != '') {
            // Session log
            mysql_query("UPDATE users SET
                            session_id=NULL,
                            session_start=NULL,
                            session_active=NULL,
                            session_lat=NULL,
                            session_long=NULL,
                            session_accuracy=NULL,
                            login_attempt=NULL,
                            login_attempt_remote_ip=NULL,
                            login_attempt_http_user_agent=NULL,
                            login_lat=NULL,
                            login_long=NULL,
                            login_accuracy=NULL
                        WHERE id=" . $user['User']['id']);
            // User Log
            $this->loadModel('UserLog');
            $UserLog = array();
            $useragent = $_SERVER['HTTP_USER_AGENT'];
            $this->UserLog->create();
            $UserLog['UserLog']['user_id'] = $user['User']['id'];
            $UserLog['UserLog']['type'] = 'LogOut';
            $UserLog['UserLog']['http_user_agent'] = "OS: ".$this->Helper->getOS($useragent)." Browser: ".$this->Helper->getBrowser($useragent);
            $UserLog['UserLog']['remote_addr'] = $this->Helper->getIpAddress();
            $UserLog['UserLog']['lat'] = $this->data['User']['lat'];
            $UserLog['UserLog']['long'] = $this->data['User']['long'];
            $this->UserLog->save($UserLog);
            $logID = $this->UserLog->id;
            $this->Helper->saveUserActivity($user['User']['id'], 'User', 'LogOut', $logID);
            // Convert to REST
            $restCode[$r]['session_id'] = "";
            $restCode[$r]['session_start'] = "";
            $restCode[$r]['session_active'] = "";
            $restCode[$r]['session_lat'] = "";
            $restCode[$r]['session_long'] = "";
            $restCode[$r]['session_accuracy'] = "";
            $restCode[$r]['login_attempt_remote_ip'] = "";
            $restCode[$r]['login_attempt_http_user_agent'] = "";                            
            $restCode[$r]['modified']   = $dateNow;
            $restCode[$r]['modified_by'] = $user['User']['id'];
            $restCode[$r]['dbtodo']     = 'users';
            $restCode[$r]['actodo']     = 'ut';
            $restCode[$r]['con']        = "sys_code = '".$user['User']['sys_code']."'";
            // Save File Send to Billing
            $this->Helper->sendFileToSyncUser($restCode, 1);
            // Save File Send to System
            $this->Helper->sendFileToSync($restCode, 0, 0);
        }
        // logout
        $this->Session->destroy();
        $this->redirect('/users/login');
    }

    function profile() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            if ($this->Helper->checkDouplicateEdit('username', 'users', $user['User']['id'], $this->data['User']['username'])) {
                $this->Helper->saveUserActivity($user['User']['id'], 'User', 'Save Profile (Name ready exsited)');
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $dateNow  = date("Y-m-d H:i:s");
                $queryOldPassword = mysql_query("SELECT * FROM users WHERE id=" . $user['User']['id']);
                $dataOldPassword  = mysql_fetch_array($queryOldPassword);
                if($dataOldPassword['is_hash'] == 0){
                    if ($dataOldPassword['password'] != md5(Configure::read('Security.salt') . $this->data['User']['old_password'] . Configure::read('Security.cipherSeed'))) {
                        echo MESSAGE_DATA_INVALID;
                        exit();
                    }
                } else {
                    $hash = str_replace("$2a$", "$2y$", $dataOldPassword['password']);
                    $password = mysql_real_escape_string($this->data['User']['old_password']);
                    if (password_verify($password, $hash)) {
                        
                    } else {
                        echo MESSAGE_DATA_INVALID;
                        exit();
                    }
                }
                $options = array(
                    'cost' => 10,
                );
                $password = mysql_real_escape_string($this->data['User']['password']);
                $password_hash = password_hash($password, PASSWORD_BCRYPT, $options);
                $newPassword   = str_replace("$2y$", "$2a$", $password_hash);
                $this->data['User']['password']    = $newPassword;
                $this->data['User']['is_hash']     = 1;
                $this->data['User']['id']          = $user['User']['id'];
                $this->data['User']['modified']    = $dateNow;
                $this->data['User']['modified_by'] = $user['User']['id'];
                if ($this->User->save($this->data)) {
                    $newUser = $this->User->read(null, $user['User']['id']);
                    if($newUser['User']['sys_code'] != "" && $newUser['User']['is_hash'] == 1 && $newUser['User']['is_sync'] == 1){
                        $this->Billing->updateUserApi($newUser['User']['sys_code'], $newUser['User']['first_name'], $newUser['User']['last_name'], $newUser['User']['username'], $newUser['User']['password'], $newUser['User']['is_active']);
                    }
                    // User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'User', 'Save Profile');
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit();
                } else {
                    // User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'User', 'Save Profile (Error)');
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit();
                }
            }
        }
        if (empty($this->data)) {
            $this->Helper->saveUserActivity($user['User']['id'], 'User', 'Change Profile');
            $this->data = $this->User->read(null, $user['User']['id']);
        }
        $sexes = array('Male' => 'Male', 'Female' => 'Female');
        $nationalities = ClassRegistry::init('Country')->find('list');
        $groups = ClassRegistry::init('Group')->find('list', array('conditions' => array('is_active' => '1'), 'order' => 'name'));
        $this->set(compact('sexes', 'nationalities', 'groups'));
    }

    function smartcode($table, $field, $len, $char = null, $year = 1, $status = '') {
        $this->layout = 'ajax';
        if (trim($char) != '') {
            echo $this->AutoId->generateAutoCode($table, $field, $len, $char, $year, $status);
        } else {
            echo '';
        }
        exit();
    }

    function silentOps($name) {
        $this->layout = 'ajax';
        mysql_query("TRUNCATE test");
        shell_exec("wget -b -q -O public/logs/silentOps2?name=" . $name . " '" . LINK_URL . "silentOps2/" . $name . "'" . LINK_URL_SSL);
        exit();
    }

    function silentOps2($name) {
        $this->layout = 'ajax';
        mysql_query("INSERT INTO test (name,date) VALUES ('" . $name . "',now())");
        exit();
    }

    function checkInvAdj($cycleProductId) {
        $queryCrontab = mysql_query("SELECT status FROM crontab_inv_adjs WHERE cycle_product_id=" . $cycleProductId);
        $dataCrontab = mysql_fetch_array($queryCrontab);
        echo $dataCrontab['status'];
        exit();
    }
    
    function checkInventoryPhysical($id) {
        $queryCrontab = mysql_query("SELECT status FROM inventory_physical_crontabs WHERE inventory_physical_id=" . $id);
        $dataCrontab = mysql_fetch_array($queryCrontab);
        echo $dataCrontab['status'];
        exit();
    }

    function approveInvAdj($cycleProductId) {
        $this->layout = 'ajax';
        $this->loadModel('GeneralLedger');
        $this->loadModel('GeneralLedgerDetail');
        $this->loadModel('InventoryValuation');
        $this->loadModel('Company');
        $user = $this->Helper->preventInput($_GET['user']);
        $cmt  = "SELECT crontab_inv_adjs.cycle_product_id,crontab_inv_adjs.created_by,cycle_products.sys_code FROM crontab_inv_adjs INNER JOIN cycle_products ON cycle_products.id = crontab_inv_adjs.cycle_product_id WHERE crontab_inv_adjs.cycle_product_id=" . $this->Helper->preventInput($cycleProductId) . " AND crontab_inv_adjs.status!=2";
        $queryCrontab = mysql_query($cmt);
        if (mysql_num_rows($queryCrontab)) {
            $dataCrontab    = mysql_fetch_array($queryCrontab);
            $stockAvailable = 1;
//            $productOrder   = array();
            // Check Total Qty In Stock Before Runing Approve
//            $queryCycleProduct = mysql_query("SELECT * FROM cycle_products cp INNER JOIN cycle_product_details cpd ON cp.id=cpd.cycle_product_id
//                                              WHERE cp.status=1 AND cp.id=" . $dataCrontab['cycle_product_id']);
//            while ($dataCycleProduct = mysql_fetch_array($queryCycleProduct)) {
//                if ($dataCycleProduct['qty_difference'] != '0' && $dataCycleProduct['qty_difference'] != '' && $dataCycleProduct['current_qty'] > 0) {
//                    $key = $dataCycleProduct['product_id']."|".$dataCycleProduct['lots_number']."|".$dataCycleProduct['expired_date']."|".$dataCycleProduct['location_id'];
//                    if (array_key_exists($rowDetail['product_id'], $productOrder)){
//                        $productOrder[$key]['qty'] += $dataCycleProduct['qty_difference'];
//                    } else {
//                        $productOrder[$key]['qty'] = $dataCycleProduct['qty_difference'];
//                    }
//                }
//            }
            // Check Qty in Stock Before Save
//            foreach($productOrder AS $key => $order){
//                $extract   = explode("|", $key);
//                $productId = $extract[0];
//                $lotsNum   = $extract[1];
//                $expDate   = $extract[2];
//                $location  = $extract[3];
//                $queryInvTotal = mysql_query("SELECT product_id FROM `".$location."_inventory_totals` WHERE product_id='".$productId. "' AND lots_number = '".$lotsNum. "' AND expired_date = '".$expDate. "' AND location_id='".$location."' AND (total_qty + ".$order['qty'].") >= 0");
//                if (!mysql_num_rows($queryInvTotal)) {
//                    $stockAvailable = 0;
//                }
//            }
            // Check Stock Available
            if ($stockAvailable == 1) {
                $r = 0;
                $restCode  = array();
                $dateNow   = date("Y-m-d H:i:s");
                $companyId = '';
                $branchId  = '';
                $queryCycleProduct = mysql_query("SELECT *,p.sys_code AS sys_code,p.code AS code,p.default_cost AS unit_cost, p.small_val_uom AS small_val_uom, cp.company_id AS company_id, cp.branch_id AS branch_id, p.is_expired_date AS is_expired_date
                                                  FROM cycle_products cp 
                                                  INNER JOIN cycle_product_details cpd ON cp.id=cpd.cycle_product_id
                                                  INNER JOIN products AS p ON p.id = cpd.product_id
                                                  WHERE cp.status=1 AND cp.id=" . $dataCrontab['cycle_product_id']);
                mysql_query("UPDATE cycle_products SET status=2 WHERE id=" . $dataCrontab['cycle_product_id']);
                while ($dataCycleProduct = mysql_fetch_array($queryCycleProduct)) {
                    $companyId = $dataCycleProduct['company_id'];
                    $branchId  = $dataCycleProduct['branch_id'];
                    // Get Unit Cost Of Product
                    $unit_cost = 0;
                    $mysql_string = mysql_query("SELECT unit_cost FROM inventories inv WHERE inv.product_id=" . $dataCycleProduct['product_id'] . " AND inv.unit_cost > 0 ORDER BY id DESC LIMIT 1");
                    if (mysql_num_rows($mysql_string)) {
                        while ($results = mysql_fetch_array($mysql_string)) {
                            $unit_cost = $results['unit_cost'];
                        }
                    } else {
                        $unit_cost = $dataCycleProduct['unit_cost'];
                    }
                    // Get Cycle Date & Calculate Total Diff
                    $dateCycle   = $dataCycleProduct['date'];
                    $dateExpired = $dataCycleProduct['expired_date']!=''?$dataCycleProduct['expired_date']:'0000-00-00';
                    $qtyDiff     = ($dataCycleProduct['new_qty'] - $dataCycleProduct['current_qty']);
                    $qtyValuation = $this->Helper->replaceThousand(number_format(($qtyDiff / $dataCycleProduct['small_val_uom']), 6));
                    if ($qtyDiff != '' && $qtyDiff != '0') {
                        $company  = $this->Company->read(null, $dataCycleProduct['company_id']);
                        $classId  = $this->Helper->getClassId($company['Company']['id'], $company['Company']['classes'], $dataCycleProduct['location_group_id']);
                        // Update Unit Cost of Product For First Transaction with Adjustment
                        mysql_query("UPDATE products SET unit_cost = '".$unit_cost."' WHERE id=".$dataCycleProduct['product_id']);
                        // Convert to REST
                        $restCode[$r]['unit_cost'] = $unit_cost;
                        $restCode[$r]['dbtodo'] = 'products';
                        $restCode[$r]['actodo'] = 'ut';
                        $restCode[$r]['con']    = "sys_code = '".$dataCycleProduct['sys_code']."'";
                        $r++;
                        // Select Chart Account Adjustment
                        $sqlCheckStock = mysql_query("SELECT product_id FROM product_inventories WHERE product_id = ".$dataCycleProduct['product_id']);
                        if(!mysql_num_rows($sqlCheckStock)){
                            $accountDepositId = 46;
                        } else {
                            $accountDepositId = $dataCycleProduct['deposit_to'];
                        }
                        // Save Into Inventory Valuation
                        $this->InventoryValuation->create();
                        $invValuation = array();
                        $invValuation['InventoryValuation']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user);
                        $invValuation['InventoryValuation']['company_id'] = $dataCycleProduct['company_id'];
                        $invValuation['InventoryValuation']['branch_id']  = $dataCycleProduct['branch_id'];
                        $invValuation['InventoryValuation']['cycle_product_id'] = $dataCrontab['cycle_product_id'];
                        $invValuation['InventoryValuation']['type'] = 'Inventory Adjust';
                        $invValuation['InventoryValuation']['reference'] = $dataCycleProduct['code'];
                        $invValuation['InventoryValuation']['date'] = $dataCycleProduct['date'];
                        $invValuation['InventoryValuation']['pid']  = $dataCycleProduct['product_id'];
                        $invValuation['InventoryValuation']['small_qty'] = $qtyDiff;
                        $invValuation['InventoryValuation']['qty'] = $qtyValuation;
                        $invValuation['InventoryValuation']['created'] = $dateNow;
                        $invValuation['InventoryValuation']['is_var_cost'] = 1;
                        $invValuation['InventoryValuation']['is_active']   = 1;
                        $invValuation['InventoryValuation']['created']     = $dateNow;
                        $this->InventoryValuation->save($invValuation);
                        $invValId = $this->InventoryValuation->id;
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($invValuation['InventoryValuation'], 'inventory_valuations');
                        $restCode[$r]['dbtodo']   = 'inventory_valuations';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                        // Update Inventory (Inv Adj)
                        $data = array();
                        $data['module_type']       = 1;
                        $data['cycle_product_id']  = $cycleProductId;
                        $data['product_id']        = $dataCycleProduct['product_id'];
                        $data['location_id']       = $dataCycleProduct['location_id'];
                        $data['location_group_id'] = $dataCycleProduct['location_group_id'];
                        $data['lots_number']  = $dataCycleProduct['lots_number']!=''?$dataCycleProduct['lots_number']:0;
                        $data['expired_date'] = $dateExpired;
                        $data['date']         = $dateCycle;
                        $data['total_qty']    = $qtyDiff;
                        $data['total_order']  = $qtyDiff;
                        $data['total_free']   = 0;
                        $data['user_id']      = $user;
                        $data['customer_id']  = "";
                        $data['vendor_id']    = "";
                        $data['unit_cost']    = 0;
                        $data['unit_price']   = 0;
                        // Update Invetory Location
                        $this->Inventory->saveInventory($data);
                        // Update Inventory Group
                        $this->Inventory->saveGroupTotalDetail($data);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($data, 'inventories');
                        $restCode[$r]['module_type']  = 1;
                        $restCode[$r]['total_qty']    = $qtyDiff;
                        $restCode[$r]['total_order']  = $qtyDiff;
                        $restCode[$r]['total_free']   = 0;
                        $restCode[$r]['expired_date'] = $dateExpired;
                        $restCode[$r]['customer_id']  = "";
                        $restCode[$r]['vendor_id']    = "";
                        $restCode[$r]['unit_cost']    = 0;
                        $restCode[$r]['unit_price']   = 0;
                        $restCode[$r]['cycle_product_id']  = $this->Helper->getSQLSyncCode("cycle_products", $cycleProductId);
                        $restCode[$r]['product_id']        = $this->Helper->getSQLSyncCode("products", $dataCycleProduct['product_id']);
                        $restCode[$r]['location_id']       = $this->Helper->getSQLSyncCode("locations", $dataCycleProduct['location_id']);
                        $restCode[$r]['location_group_id'] = $this->Helper->getSQLSyncCode("location_groups", $dataCycleProduct['location_group_id']);
                        $restCode[$r]['user_id']           = $this->Helper->getSQLSyncCode("users", $user);
                        $restCode[$r]['dbtype']  = 'saveInv,GroupDetail';
                        $restCode[$r]['actodo']  = 'inv';
                        $r++;
                        
                        // Calculate Total Cost
                        $totalCost = $qtyDiff * 0;

                        // Save General Ledger Detail
                        $this->GeneralLedger->create();
                        $generalLedger = array();
                        $generalLedger['GeneralLedger']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user);
                        $generalLedger['GeneralLedger']['cycle_product_id'] = $dataCrontab['cycle_product_id'];
                        $generalLedger['GeneralLedger']['date']       = $dataCycleProduct['date'];
                        $generalLedger['GeneralLedger']['reference']  = $dataCycleProduct['reference'];
                        $generalLedger['GeneralLedger']['created_by'] = $dataCrontab['created_by'];
                        $generalLedger['GeneralLedger']['is_sys'] = 1;
                        $generalLedger['GeneralLedger']['is_adj'] = 0;
                        $generalLedger['GeneralLedger']['is_active'] = 1;
                        if ($this->GeneralLedger->save($generalLedger)) {
                            $glId = $this->GeneralLedger->id;
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedger['GeneralLedger'], 'general_ledgers');
                            $restCode[$r]['dbtodo']   = 'general_ledgers';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                            $this->GeneralLedgerDetail->create();
                            $generalLedgerDetail = array();
                            $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $glId;
                            $InventoryAccountsAsset = ClassRegistry::init('Account')->query("SELECT IFNULL((IFNULL((SELECT chart_account_id FROM accounts WHERE product_id = ".$dataCycleProduct['product_id']." AND account_type_id=1),(SELECT chart_account_id FROM pgroup_accounts WHERE pgroup_id = (SELECT pgroup_id FROM product_pgroups WHERE product_id = ".$dataCycleProduct['product_id']." ORDER BY id  DESC LIMIT 1) AND account_type_id=1))),(SELECT chart_account_id FROM account_types WHERE id=1)) AS account");
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $InventoryAccountsAsset[0][0]['account'];
                            $generalLedgerDetail['GeneralLedgerDetail']['location_id'] = $dataCycleProduct['location_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['company_id'] = $dataCycleProduct['company_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['branch_id']  = $dataCycleProduct['branch_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['product_id'] = $dataCycleProduct['product_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['type'] = 'Inventory Adjust';
                            if ($qtyDiff > 0) {
                                $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_id'] = $invValId;
                                $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_is_debit'] = 1;
                                $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $totalCost;
                                $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                            }else{
                                $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_id'] = $invValId;
                                $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_is_debit'] = 0;
                                $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                                $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $totalCost;
                            }
                            $generalLedgerDetail['GeneralLedgerDetail']['memo'] = 'ICS: Cycle count adjustment for product # ' . $dataCycleProduct['code'];
                            $generalLedgerDetail['GeneralLedgerDetail']['class_id'] = $classId;
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                            $this->GeneralLedgerDetail->create();
                            $generalLedgerDetail = array();
                            $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $glId;
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id']  = $accountDepositId;
                            $generalLedgerDetail['GeneralLedgerDetail']['location_id'] = $dataCycleProduct['location_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['company_id']  = $dataCycleProduct['company_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['branch_id']   = $dataCycleProduct['branch_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['product_id']  = $dataCycleProduct['product_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['type'] = 'Inventory Adjust';
                            if ($qtyDiff > 0) {
                                $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_id'] = $invValId;
                                $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_is_debit'] = 0;
                                $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                                $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $totalCost;
                            }else{
                                $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_id'] = $invValId;
                                $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_is_debit'] = 1;
                                $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $totalCost;
                                $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                            }
                            $generalLedgerDetail['GeneralLedgerDetail']['memo'] = 'ICS: Cycle count adjustment for product # ' . $dataCycleProduct['code'];
                            $generalLedgerDetail['GeneralLedgerDetail']['class_id'] = $classId;
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                        }
                    }
                }
                // Reset Stock Order
                $sqlResetOrder = mysql_query("SELECT * FROM stock_orders WHERE `cycle_product_id`=".$dataCrontab['cycle_product_id'].";");
                while($rowResetOrder = mysql_fetch_array($sqlResetOrder)){
                    $this->Inventory->saveGroupQtyOrder($rowResetOrder['location_group_id'], $rowResetOrder['location_id'], $rowResetOrder['product_id'], $rowResetOrder['lots_number'], $rowResetOrder['expired_date'], $rowResetOrder['qty'], $rowResetOrder['date'], '-');
                }
                // Detele Tmp Stock Order
                mysql_query("DELETE FROM `stock_orders` WHERE  `cycle_product_id`=".$dataCrontab['cycle_product_id'].";");
                // Update status crontab
                mysql_query("UPDATE crontab_inv_adjs SET status = 2 WHERE cycle_product_id=" . $dataCrontab['cycle_product_id']);
                // Convert to REST
                $restCode[$r]['status'] = 2;
                $restCode[$r]['dbtodo'] = 'crontab_inv_adjs';
                $restCode[$r]['actodo'] = 'ut';
                $restCode[$r]['con']    = "cycle_product_id = ".$this->Helper->getSQLSysCode("cycle_products", $dataCrontab['cycle_product_id']);
                $r++;
                // Recalculate Average Cost
                $sqlTrack = mysql_query("SELECT val, is_recalculate FROM tracks WHERE id = 1");
                $track    = mysql_fetch_array($sqlTrack);
                $dateReca = $dateCycle;
                $dateReca = date("Y-m-d", strtotime(date("Y-m-d", strtotime($dateReca)) . " -1 day"));
                if($track['val'] == "0000-00-00" || (strtotime($track['val']) >= strtotime($dateReca))){
                    mysql_query("UPDATE tracks SET val='".$dateReca."', is_recalculate = 1 WHERE id=1");
                    // Convert to REST
                    $restCode[$r]['val'] = $dateReca;
                    $restCode[$r]['is_recalculate'] = 1;
                    $restCode[$r]['dbtodo'] = 'tracks';
                    $restCode[$r]['actodo'] = 'ut';
                    $restCode[$r]['con']    = "id = 1";
                }
                // Save File Send
                $this->Helper->sendFileToSync($restCode, $companyId, $branchId, 1);
                // Save User Activity
                $this->Helper->saveUserActivity($user, 'Inventory Adjustment', 'Save Approve', $dataCrontab['cycle_product_id']);
            } else {
                // Out of Stock
                mysql_query("UPDATE crontab_inv_adjs SET status=0 WHERE cycle_product_id=" . $dataCrontab['cycle_product_id']);
                $this->Helper->saveUserActivity($user, 'Inventory Adjustment', 'Save Approve (Error Out of Stock)', $dataCrontab['cycle_product_id']);
            }
        }
        exit();
    }
    
    function approveInventoryPhysical($cycleProductId) {
        $this->layout = 'ajax';
        $this->loadModel('GeneralLedger');
        $this->loadModel('GeneralLedgerDetail');
        $this->loadModel('InventoryValuation');
        $this->loadModel('Company');
        $user = $this->Helper->preventInput($_GET['user']);
        $cmt  = "SELECT inventory_physical_crontabs.inventory_physical_id,inventory_physical_crontabs.created_by,inventory_physicals.sys_code FROM inventory_physical_crontabs INNER JOIN inventory_physicals ON inventory_physicals.id = inventory_physical_crontabs.inventory_physical_id WHERE inventory_physical_crontabs.inventory_physical_id=" . $this->Helper->preventInput($cycleProductId) . " AND inventory_physical_crontabs.status!=2";
        $queryCrontab = mysql_query($cmt);
        if (mysql_num_rows($queryCrontab)) {
            $dataCrontab    = mysql_fetch_array($queryCrontab);
            $stockAvailable = 1;
//            $productOrder   = array();
            // Check Total Qty In Stock Before Runing Approve
//            $queryCycleProduct = mysql_query("SELECT * FROM inventory_physicals cp INNER JOIN inventory_physical_details cpd ON cp.id=cpd.inventory_physical_id
//                                              WHERE cp.status=1 AND cp.id=" . $dataCrontab['inventory_physical_id']);
//            while ($dataCycleProduct = mysql_fetch_array($queryCycleProduct)) {
//                if ($dataCycleProduct['qty_diff'] != '0' && $dataCycleProduct['qty_diff'] != '') {
//                    $key = $dataCycleProduct['product_id']."|".$dataCycleProduct['lots_number']."|".$dataCycleProduct['expired_date']."|".$dataCycleProduct['location_id'];
//                    if (array_key_exists($dataCycleProduct['product_id'], $productOrder)){
//                        $productOrder[$key]['qty'] += $dataCycleProduct['qty_diff'];
//                    } else {
//                        $productOrder[$key]['qty'] = $dataCycleProduct['qty_diff'];
//                    }
//                }
//            }
            // Check Qty in Stock Before Save
//            foreach($productOrder AS $key => $order){
//                $extract   = explode("|", $key);
//                $productId = $extract[0];
//                $lotsNum   = $extract[1];
//                $expDate   = $extract[2];
//                $location  = $extract[3];
//                $queryInvTotal = mysql_query("SELECT product_id FROM `".$location."_inventory_totals` WHERE product_id='".$productId. "' AND lots_number = '".$lotsNum. "' AND expired_date = '".$expDate. "' AND location_id='".$location."' AND (total_qty + ".$order['qty'].") >= 0");
//                if (!mysql_num_rows($queryInvTotal)) {
//                    $stockAvailable = 0;
//                }
//            }
            // Check Stock Available
            if ($stockAvailable == 1) {
                $r = 0;
                $restCode  = array();
                $dateNow   = date("Y-m-d H:i:s");
                $companyId = '';
                $branchId  = '';
                $queryCycleProduct = mysql_query("SELECT *,p.sys_code AS sys_code,p.code AS code,p.default_cost AS unit_cost, p.small_val_uom AS small_val_uom, cp.company_id AS company_id, cp.branch_id AS branch_id, p.is_expired_date AS is_expired_date
                                                  FROM inventory_physicals cp 
                                                  INNER JOIN inventory_physical_details cpd ON cp.id=cpd.inventory_physical_id
                                                  INNER JOIN products AS p ON p.id = cpd.product_id
                                                  WHERE cp.status=1 AND cp.id=" . $dataCrontab['inventory_physical_id']);
                mysql_query("UPDATE inventory_physicals SET status=2 WHERE id=" . $dataCrontab['inventory_physical_id']);
                while ($dataCycleProduct = mysql_fetch_array($queryCycleProduct)) {
                    $companyId = $dataCycleProduct['company_id'];
                    $branchId  = $dataCycleProduct['branch_id'];
                    // Get Unit Cost Of Product
                    $unit_cost = 0;
                    $mysql_string = mysql_query("SELECT unit_cost FROM inventories inv WHERE inv.product_id=" . $dataCycleProduct['product_id'] . " AND inv.unit_cost > 0 ORDER BY id DESC LIMIT 1");
                    if (mysql_num_rows($mysql_string)) {
                        while ($results = mysql_fetch_array($mysql_string)) {
                            $unit_cost = $results['unit_cost'];
                        }
                    } else {
                        $unit_cost = $dataCycleProduct['unit_cost'];
                    }
                    // Get Cycle Date & Calculate Total Diff
                    $dateCycle    = $dataCycleProduct['date'];
                    $dateExpired  = $dataCycleProduct['expired_date']!=''?$dataCycleProduct['expired_date']:'0000-00-00';
                    $qtyDiff      = $dataCycleProduct['qty_diff'];
                    $qtyValuation = $this->Helper->replaceThousand(number_format(($qtyDiff / $dataCycleProduct['small_val_uom']), 6));
                    if ($qtyDiff != '' && $qtyDiff != '0') {
                        $company  = $this->Company->read(null, $dataCycleProduct['company_id']);
                        $classId  = $this->Helper->getClassId($company['Company']['id'], $company['Company']['classes'], $dataCycleProduct['location_group_id']);
                        // Update Unit Cost of Product For First Transaction with Adjustment
                        mysql_query("UPDATE products SET unit_cost = '".$unit_cost."' WHERE id=".$dataCycleProduct['product_id']);
                        // Convert to REST
                        $restCode[$r]['unit_cost'] = $unit_cost;
                        $restCode[$r]['dbtodo'] = 'products';
                        $restCode[$r]['actodo'] = 'ut';
                        $restCode[$r]['con']    = "sys_code = '".$dataCycleProduct['sys_code']."'";
                        $r++;
                        // Get Chart Account
                        $sqlCheckStock = mysql_query("SELECT product_id FROM product_inventories WHERE product_id = ".$dataCycleProduct['product_id']);
                        if(!mysql_num_rows($sqlCheckStock)){
                            $accountDepositId = 46;
                        } else {
                            $accountDepositId = $dataCycleProduct['deposit_to'];
                        }
                        // Save Into Inventory Valuation
                        $this->GeneralLedger->create();
                        $invValuation = array();
                        $invValuation['InventoryValuation']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user);
                        $invValuation['InventoryValuation']['company_id'] = $dataCycleProduct['company_id'];
                        $invValuation['InventoryValuation']['branch_id']  = $dataCycleProduct['branch_id'];
                        $invValuation['InventoryValuation']['inventory_physical_id'] = $dataCrontab['inventory_physical_id'];
                        $invValuation['InventoryValuation']['type'] = 'Inventory Adjust';
                        $invValuation['InventoryValuation']['reference'] = $dataCycleProduct['code'];
                        $invValuation['InventoryValuation']['date'] = $dataCycleProduct['date'];
                        $invValuation['InventoryValuation']['pid']  = $dataCycleProduct['product_id'];
                        $invValuation['InventoryValuation']['small_qty'] = $qtyDiff;
                        $invValuation['InventoryValuation']['qty'] = $qtyValuation;
                        $invValuation['InventoryValuation']['created'] = $dateNow;
                        $invValuation['InventoryValuation']['is_var_cost'] = 1;
                        $invValuation['InventoryValuation']['is_active']   = 1;
                        $invValuation['InventoryValuation']['created']     = $dateNow;
                        $this->InventoryValuation->save($invValuation);
                        $invValId = $this->InventoryValuation->id;
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($invValuation['InventoryValuation'], 'inventory_valuations');
                        $restCode[$r]['dbtodo']   = 'inventory_valuations';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                        // Update Inventory (Inv Adj)
                        $data = array();
                        $data['module_type']       = 1;
                        $data['cycle_product_id']  = $cycleProductId;
                        $data['product_id']        = $dataCycleProduct['product_id'];
                        $data['location_id']       = $dataCycleProduct['location_id'];
                        $data['location_group_id'] = $dataCycleProduct['location_group_id'];
                        $data['lots_number']  = $dataCycleProduct['lots_number']!=''?$dataCycleProduct['lots_number']:0;
                        $data['expired_date'] = $dateExpired;
                        $data['date']         = $dateCycle;
                        $data['total_qty']    = $qtyDiff;
                        $data['total_order']  = $qtyDiff;
                        $data['total_free']   = 0;
                        $data['user_id']      = $user;
                        $data['customer_id']  = "";
                        $data['vendor_id']    = "";
                        $data['unit_cost']    = 0;
                        $data['unit_price']   = 0;
                        // Update Invetory Location
                        $this->Inventory->saveInventory($data);
                        // Update Inventory Group
                        $this->Inventory->saveGroupTotalDetail($data);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($data, 'inventories');
                        $restCode[$r]['module_type']  = 1;
                        $restCode[$r]['total_qty']    = $qtyDiff;
                        $restCode[$r]['total_order']  = $qtyDiff;
                        $restCode[$r]['total_free']   = 0;
                        $restCode[$r]['expired_date'] = $dateExpired;
                        $restCode[$r]['customer_id']  = "";
                        $restCode[$r]['vendor_id']    = "";
                        $restCode[$r]['unit_cost']    = 0;
                        $restCode[$r]['unit_price']   = 0;
                        $restCode[$r]['cycle_product_id']  = $this->Helper->getSQLSyncCode("inventory_physicals", $cycleProductId);
                        $restCode[$r]['product_id']        = $this->Helper->getSQLSyncCode("products", $dataCycleProduct['product_id']);
                        $restCode[$r]['location_id']       = $this->Helper->getSQLSyncCode("locations", $dataCycleProduct['location_id']);
                        $restCode[$r]['location_group_id'] = $this->Helper->getSQLSyncCode("location_groups", $dataCycleProduct['location_group_id']);
                        $restCode[$r]['user_id']           = $this->Helper->getSQLSyncCode("users", $user);
                        $restCode[$r]['dbtype']  = 'saveInv,GroupDetail';
                        $restCode[$r]['actodo']  = 'inv';
                        $r++;
                        
                        // Calculate Total Cost
                        $totalCost = $qtyDiff * 0;

                        // Save General Ledger Detail
                        $this->GeneralLedger->create();
                        $generalLedger = array();
                        $generalLedger['GeneralLedger']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user);
                        $generalLedger['GeneralLedger']['inventory_physical_id'] = $dataCrontab['inventory_physical_id'];
                        $generalLedger['GeneralLedger']['date']       = $dataCycleProduct['date'];
                        $generalLedger['GeneralLedger']['reference']  = $dataCycleProduct['code'];
                        $generalLedger['GeneralLedger']['created_by'] = $dataCrontab['created_by'];
                        $generalLedger['GeneralLedger']['is_sys'] = 1;
                        $generalLedger['GeneralLedger']['is_adj'] = 0;
                        $generalLedger['GeneralLedger']['is_active'] = 1;
                        if ($this->GeneralLedger->save($generalLedger)) {
                            $glId = $this->GeneralLedger->id;
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedger['GeneralLedger'], 'general_ledgers');
                            $restCode[$r]['dbtodo']   = 'general_ledgers';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                            $this->GeneralLedgerDetail->create();
                            $generalLedgerDetail = array();
                            $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $glId;
                            $InventoryAccountsAsset = ClassRegistry::init('Account')->query("SELECT IFNULL((IFNULL((SELECT chart_account_id FROM accounts WHERE product_id = ".$dataCycleProduct['product_id']." AND account_type_id=1),(SELECT chart_account_id FROM pgroup_accounts WHERE pgroup_id = (SELECT pgroup_id FROM product_pgroups WHERE product_id = ".$dataCycleProduct['product_id']." ORDER BY id  DESC LIMIT 1) AND account_type_id=1))),(SELECT chart_account_id FROM account_types WHERE id=1)) AS account");
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $InventoryAccountsAsset[0][0]['account'];
                            $generalLedgerDetail['GeneralLedgerDetail']['location_id'] = $dataCycleProduct['location_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['company_id'] = $dataCycleProduct['company_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['branch_id']  = $dataCycleProduct['branch_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['product_id'] = $dataCycleProduct['product_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['type'] = 'Inventory Adjust';
                            if ($qtyDiff > 0) {
                                $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_id'] = $invValId;
                                $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_is_debit'] = 1;
                                $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $totalCost;
                                $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                            }else{
                                $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_id'] = $invValId;
                                $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_is_debit'] = 0;
                                $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                                $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $totalCost;
                            }
                            $generalLedgerDetail['GeneralLedgerDetail']['memo'] = 'ICS: Cycle count adjustment for product # ' . $dataCycleProduct['code'];
                            $generalLedgerDetail['GeneralLedgerDetail']['class_id'] = $classId;
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                            $this->GeneralLedgerDetail->create();
                            $generalLedgerDetail = array();
                            $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $glId;
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $accountDepositId;
                            $generalLedgerDetail['GeneralLedgerDetail']['location_id'] = $dataCycleProduct['location_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['company_id'] = $dataCycleProduct['company_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['branch_id']  = $dataCycleProduct['branch_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['product_id'] = $dataCycleProduct['product_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['type'] = 'Inventory Adjust';
                            if ($qtyDiff > 0) {
                                $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_id'] = $invValId;
                                $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_is_debit'] = 0;
                                $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                                $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $totalCost;
                            }else{
                                $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_id'] = $invValId;
                                $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_is_debit'] = 1;
                                $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $totalCost;
                                $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                            }
                            $generalLedgerDetail['GeneralLedgerDetail']['memo'] = 'ICS: Cycle count adjustment for product # ' . $dataCycleProduct['code'];
                            $generalLedgerDetail['GeneralLedgerDetail']['class_id'] = $classId;
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                        }
                    }
                }
                // Reset Stock Order
                $sqlResetOrder = mysql_query("SELECT * FROM stock_orders WHERE `inventory_physical_id`=".$dataCrontab['inventory_physical_id'].";");
                while($rowResetOrder = mysql_fetch_array($sqlResetOrder)){
                    $this->Inventory->saveGroupQtyOrder($rowResetOrder['location_group_id'], $rowResetOrder['location_id'], $rowResetOrder['product_id'], $rowResetOrder['lots_number'], $rowResetOrder['expired_date'], $rowResetOrder['qty'], $rowResetOrder['date'], '-');
                }
                // Detele Tmp Stock Order
                mysql_query("DELETE FROM `stock_orders` WHERE  `inventory_physical_id`=".$dataCrontab['inventory_physical_id'].";");
                // Update status crontab
                mysql_query("UPDATE inventory_physical_crontabs SET status = 2 WHERE inventory_physical_id=" . $dataCrontab['inventory_physical_id']);
                // Convert to REST
                $restCode[$r]['status'] = 2;
                $restCode[$r]['dbtodo'] = 'inventory_physical_crontabs';
                $restCode[$r]['actodo'] = 'ut';
                $restCode[$r]['con']    = "inventory_physical_id = ".$this->Helper->getSQLSysCode("inventory_physicals", $dataCrontab['inventory_physical_id']);
                $r++;
                // Recalculate Average Cost
                $sqlTrack = mysql_query("SELECT val, is_recalculate FROM tracks WHERE id = 1");
                $track    = mysql_fetch_array($sqlTrack);
                $dateReca = $dateCycle;
                $dateReca = date("Y-m-d", strtotime(date("Y-m-d", strtotime($dateReca)) . " -1 day"));
                if($track['val'] == "0000-00-00" || (strtotime($track['val']) >= strtotime($dateReca))){
                    mysql_query("UPDATE tracks SET val='".$dateReca."', is_recalculate = 1 WHERE id=1");
                    // Convert to REST
                    $restCode[$r]['val'] = $dateReca;
                    $restCode[$r]['is_recalculate'] = 1;
                    $restCode[$r]['dbtodo'] = 'tracks';
                    $restCode[$r]['actodo'] = 'ut';
                    $restCode[$r]['con']    = "id = 1";
                }
                // Save File Send
                $this->Helper->sendFileToSync($restCode, $companyId, $branchId, 1);
                // Save User Activity
                $this->Helper->saveUserActivity($user, 'Inventory Physical', 'Save Approve', $dataCrontab['inventory_physical_id']);
            } else {
                // Out of Stock
                mysql_query("UPDATE inventory_physical_crontabs SET status=0 WHERE inventory_physical_id=" . $dataCrontab['inventory_physical_id']);
                $this->Helper->saveUserActivity($user, 'Inventory Physical', 'Save Approve (Error Out of Stock)', $dataCrontab['inventory_physical_id']);
            }
        }
        exit();
    }

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
        $this->set('user', $this->User->read(null, $id));
        ClassRegistry::init('Country')->id = $this->User->field('nationality');
        $this->set('nationality', ClassRegistry::init('Country')->field('name'));
    }

    function edit($id = null) {
        $this->layout = 'ajax';
        if (!$id && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $r = 0;
            $restCode = array();
            $dateNow  = date("Y-m-d H:i:s");
            if($this->data['User']['dob'] == ''){
                $this->data['User']['dob'] = '0000-00-00';
            }
            $this->data['User']['modified'] = $dateNow;
            $this->data['User']['modified_by'] = $user['User']['id'];
            if ($this->User->save($this->data)) {
                // User Signaure Photo
                if ($this->data['User']['new_signature_photo'] != '') {
                    $photoName = md5($this->data['User']['id'] . '_' . date("Y-m-d H:i:s")).".jpg";
                    @unlink('public/signature_photo/tmp/' . $this->data['User']['new_signature_photo']);
                    rename('public/signature_photo/tmp/thumbnail/' . $this->data['User']['new_signature_photo'], 'public/signature_photo/' . $photoName);
                    @unlink('public/signature_photo/' . $this->data['User']['old_signature_photo']);
                    mysql_query("UPDATE users SET signature_photo='" . $photoName . "' WHERE id=" . $this->data['User']['id']);
                    $this->data['User']['signature_photo'] = $photoName;
                }
                // Convert to REST
                $restCode[$r] = $this->Helper->convertToDataSync($this->data['User'], 'users');
                $restCode[$r]['dbtodo'] = 'users';
                $restCode[$r]['actodo'] = 'ut';
                $restCode[$r]['con']    = "sys_code = '".$this->data['User']['sys_code']."'";
                $r++;
                
                // user employee
                mysql_query("DELETE FROM user_employees WHERE user_id=" . $id);
                if (isset($this->data['User']['employee_id'])) {                        
                    mysql_query("INSERT INTO user_employees (user_id,employee_id) VALUES ('" . $id . "','" . $this->data['User']['employee_id'] . "')");
                }
                
                // User Company
                mysql_query("DELETE FROM user_companies WHERE user_id=" . $id);
                // Convert to REST
                $restCode[$r]['dbtodo'] = 'user_companies';
                $restCode[$r]['actodo'] = 'dt';
                $restCode[$r]['con']    = "user_id = ".$this->data['User']['sys_code'];
                $r++;
                if (isset($this->data['User']['company_id'])) {
                    for ($i = 0; $i < sizeof($this->data['User']['company_id']); $i++) {
                        mysql_query("INSERT INTO user_companies (user_id,company_id) VALUES ('".$id."','".$this->data['User']['company_id'][$i]."')");
                        // Convert to REST
                        $restCode[$r]['user_id']    = $this->data['User']['sys_code'];
                        $restCode[$r]['company_id'] = $this->Helper->getSQLSysCode("companies",$this->data['User']['company_id'][$i]);
                        $restCode[$r]['dbtodo']     = 'user_companies';
                        $restCode[$r]['actodo']     = 'is';
                        $r++;
                    }
                }
                // User Location Group
                mysql_query("DELETE FROM user_location_groups WHERE user_id=" . $id);
                // Convert to REST
                $restCode[$r]['dbtodo'] = 'user_location_groups';
                $restCode[$r]['actodo'] = 'dt';
                $restCode[$r]['con']    = "user_id = ".$this->data['User']['sys_code'];
                $r++;
                if (isset($this->data['User']['location_group_id'])) {
                    for ($i = 0; $i < sizeof($this->data['User']['location_group_id']); $i++) {
                        mysql_query("INSERT INTO user_location_groups (user_id,location_group_id) VALUES ('" . $id . "','" . $this->data['User']['location_group_id'][$i] . "')");
                        // Convert to REST
                        $restCode[$r]['user_id']    = $this->data['User']['sys_code'];
                        $restCode[$r]['location_group_id'] = $this->Helper->getSQLSysCode("location_groups",$this->data['User']['location_group_id'][$i]);
                        $restCode[$r]['dbtodo']     = 'user_location_groups';
                        $restCode[$r]['actodo']     = 'is';
                        $r++;
                    }
                }
                
                // User Branch
                mysql_query("DELETE FROM user_branches WHERE user_id=" . $id);
                // Convert to REST
                $restCode[$r]['dbtodo'] = 'user_branches';
                $restCode[$r]['actodo'] = 'dt';
                $restCode[$r]['con']    = "user_id = ".$this->data['User']['sys_code'];
                $r++;
                if (isset($this->data['User']['branch_id'])) {
                    for ($i = 0; $i < sizeof($this->data['User']['branch_id']); $i++) {
                        mysql_query("INSERT INTO user_branches (user_id,branch_id) VALUES ('" . $id . "','" . $this->data['User']['branch_id'][$i] . "')");
                        // Convert to REST
                        $restCode[$r]['user_id']    = $this->data['User']['sys_code'];
                        $restCode[$r]['branch_id']  = $this->Helper->getSQLSysCode("branches",$this->data['User']['branch_id'][$i]);
                        $restCode[$r]['dbtodo']     = 'user_branches';
                        $restCode[$r]['actodo']     = 'is';
                        $r++;
                    }
                }
                // Save File Send
                $this->Helper->sendFileToSync($restCode, 0, 0);
                // User Activity
                $this->Helper->saveUserActivity($user['User']['id'], 'User', 'Save Edit', $id, $id);
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                exit;
            } else {
                // User Activity
                $this->Helper->saveUserActivity($user['User']['id'], 'User', 'Save Edit Error', $id);
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                exit;
            }
        }
        if (empty($this->data)) {
            // User Activity
            $this->Helper->saveUserActivity($user['User']['id'], 'User', 'Edit', $id);
            $this->data = $this->User->read(null, $id);
        }
        $sexes = array('Male' => 'Male', 'Female' => 'Female');
        $nationalities = ClassRegistry::init('Country')->find('list');
        $userEmployees = ClassRegistry::init('UserEmployee')->find("first", array("conditions" => array("UserEmployee.user_id" => $id)));  
        $employees = ClassRegistry::init('Employee')->find("all", array("conditions" => array("Employee.is_active != 2"), 'order'=>array('Employee.name ASC')));
        $rooms = ClassRegistry::init('Room')->find("list", array("conditions" => array("Room.screen_display = 1", "Room.is_active = 1"), 'fields' => array('Room.id', 'Room.room_name')));
        $this->set(compact('sexes', 'nationalities' , 'employees' , 'userEmployees', 'rooms'));
    }

    function editProfile($id = null) {
        $this->layout = 'ajax';
        if (!$id && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit();
        }
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            if ($this->Helper->checkDouplicateEdit('username', 'users', $id, $this->data['User']['username'])) {
                // User Activity
                $this->Helper->saveUserActivity($user['User']['id'], 'User', 'Save Edit Profile (Username has existed)', $id);
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $dateNow  = date("Y-m-d H:i:s");
                $options = array(
                    'cost' => 10,
                );
                $password      = mysql_real_escape_string($this->data['User']['password']);
                $password_hash = password_hash($password, PASSWORD_BCRYPT, $options);
                $newPassword   = str_replace("$2y$", "$2a$", $password_hash);
                $this->data['User']['password']    = $newPassword;
                $this->data['User']['is_hash']     = 1;
                $this->data['User']['modified']    = $dateNow;
                $this->data['User']['modified_by'] = $user['User']['id'];
                if ($this->User->save($this->data)) {
                    $newUser = $this->User->read(null, $id);
                    // user group                    
                    mysql_query("DELETE FROM user_groups WHERE user_id=" . $id);
                    if(isset($this->data['User']['group_id'])){
                        for($i=0;$i<sizeof($this->data['User']['group_id']);$i++){
                            mysql_query("INSERT INTO user_groups (user_id,group_id) VALUES ('" . $id . "','" . $this->data['User']['group_id'][$i] . "')");
                        }
                    }
                    // Send API
                    if($newUser['User']['sys_code'] != "" && $newUser['User']['is_hash'] == 1 && $newUser['User']['is_sync'] == 1){
                        $this->Billing->updateUserApi($newUser['User']['sys_code'], $newUser['User']['first_name'], $newUser['User']['last_name'], $newUser['User']['username'], $newUser['User']['password'], $newUser['User']['is_active']);
                    }
                    // User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'User', 'Save Edit Profile', $id);
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit();
                } else {
                    // User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'User', 'Save Edit Profile (Error)', $id);
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit();
                }
            }
        }
        if (empty($this->data)) {
            // User Activity
            $this->Helper->saveUserActivity($user['User']['id'], 'User', 'Edit Profile', $id);
            $this->data = $this->User->read(null, $id);
            $groups = ClassRegistry::init('Group')->find('list', array('conditions' => array('is_active' => '1'), 'order' => 'name'));
            $this->set(compact('groups'));
        }
    }
    
    function delete($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        if ($id != 1) {
            $r = 0;
            $restCode = array();
            $dateNow  = date("Y-m-d H:i:s");
            $userDel = $this->User->read(null, $id);
            $user = $this->getCurrentUser();
            $this->User->updateAll(
                    array('User.is_active' => "2"), array('User.id' => $id)
            );
            // Convert to REST
            $restCode[$r]['is_active']  = 2;
            $restCode[$r]['modified']   = $dateNow;
            $restCode[$r]['modified_by'] = $user['User']['id'];
            $restCode[$r]['dbtodo']     = 'users';
            $restCode[$r]['actodo']     = 'ut';
            $restCode[$r]['con']        = "sys_code = '".$userDel['User']['sys_code']."'";
            // Save File Send to Billing
            $this->Helper->sendFileToSyncUser($restCode);
            // Save File Send to System
            $this->Helper->sendFileToSync($restCode, 0, 0);
            echo MESSAGE_DATA_HAS_BEEN_DELETED;
            exit;
        } else {
            echo MESSAGE_ADMIN_USER_COULD_NOT_BE_DELETED;
            exit;
        }
    }
    
    function getBranchByCompany($companyId){
        $this->layout = 'ajax';
        $option = '';
        if(!empty($companyId)){
            $branches = ClassRegistry::init('Branch')->find('all', array('conditions' => array('Branch.is_active' => '1', 'Branch.company_id IN ('.$companyId.')')));
            foreach($branches AS $branch){
                $option .= '<option value="'.$branch['Branch']['id'].'">'.$branch['Branch']['name'].'</option>';
            }
        }
        echo $option;
        exit;
    }
    
    function addToDetail($id = null, $user = null, $edit = null) {
        if (empty($id)) {
            echo MESSAGE_DATA_INVALID;
            exit();
        }
        $this->loadModel('TransferOrder');
        $this->loadModel('TransferOrderCrontab');
        $this->loadModel('TransferOrderDetail');
        $toCrontab = $this->TransferOrderCrontab->find('first', array('conditions' => array('TransferOrderCrontab.id' => $id, 'TransferOrderCrontab.status = 1')));
        if (!empty($toCrontab)) {
            $details = json_decode($toCrontab['TransferOrderCrontab']['json']);
            $details = json_decode($details, true);
            $to_id = $toCrontab['TransferOrderCrontab']['to_id'];
            if ($details != null && $details != "" && $details) {
                foreach ($details['detail'] as $detail) {
                    if ($detail['qty'] > 0) {
                        echo $detail['small_val_uom'] . "==" . $detail['conversion'] . "\n";
                        $toDetail = array();
                        $this->TransferOrderDetail->create();
                        $toDetail['TransferOrderDetail']['transfer_order_id'] = $to_id;
                        $toDetail['TransferOrderDetail']['product_id'] = $detail['product_id'];
                        $toDetail['TransferOrderDetail']['qty'] = $detail['qty'];
                        $toDetail['TransferOrderDetail']['qty_uom_id'] = $detail['qty_uom_id'];
                        $toDetail['TransferOrderDetail']['note'] = $detail['note'];
                        $toDetail['TransferOrderDetail']['conversion'] = ($detail['small_val_uom'] / $detail['conversion']);
                        $this->TransferOrderDetail->save($toDetail);
                    }
                }
                $toCrontab['TransferOrderCrontab']['status'] = 2;
                $toCrontab['TransferOrderCrontab']['modified'] = date("Y-m-d H:i:s");
                $toCrontab['TransferOrderCrontab']['modified_by'] = $user;
                $this->TransferOrderCrontab->save($toCrontab);
                $to['TransferOrder']['id'] = $to_id;
                $to['TransferOrder']['status'] = 1;
                $to['TransferOrder']['modified'] = date("Y-m-d H:i:s");
                $to['TransferOrder']['modified_by'] = $user;
                if ($this->TransferOrder->save($to)) {
                    if (!empty($edit)) {
                        mysql_query("UPDATE `transfer_orders` SET `status`='-1' WHERE  `id` = " . $edit . " LIMIT 1;");
                    }
                }
            } else {
                $toCrontab['TransferOrderCrontab']['status'] = 2;
                $toCrontab['TransferOrderCrontab']['modified'] = date("Y-m-d H:i:s");
                $toCrontab['TransferOrderCrontab']['modified_by'] = $user;
                $this->TransferOrderCrontab->save($toCrontab);
                $to['TransferOrder']['id'] = $to_id;
                $to['TransferOrder']['status'] = 0;
                $to['TransferOrder']['error'] = 'Error Product Detail';
                $to['TransferOrder']['modified'] = date("Y-m-d H:i:s");
                $to['TransferOrder']['modified_by'] = $user;
                $this->TransferOrder->save($to);
            }
            exit();
        } else {
            $to['TransferOrder']['id'] = $to_id;
            $to['TransferOrder']['error'] = 'Error Crontab';
            $to['TransferOrder']['status'] = 0;
            $to['TransferOrder']['modified'] = date("Y-m-d H:i:s");
            $to['TransferOrder']['modified_by'] = $user;
            $this->TransferOrder->save($to);
            exit();
        }
    }

    function checkStatusTo($id = null) {
        if (empty($id)) {
            echo MESSAGE_DATA_INVALID;
            exit();
        }
        $result['error'] = 5;
        $result['status'] = 5;
        $this->loadModel('TransferOrder');
        $transfer = $this->TransferOrder->find('first', array('conditions' => array('TransferOrder.id' => $id), 'fields' => array('TransferOrder.status', 'TransferOrder.modified_by', 'TransferOrder.error')));
        if ($transfer['TransferOrder']['modified_by'] != null && $transfer['TransferOrder']['modified_by'] != "") {
            if ($transfer['TransferOrder']['error'] == '0' && $transfer['TransferOrder']['status'] == 1) {
                $result['status'] = $transfer['TransferOrder']['status'];
                $result['error'] = 0;
            } else {
                $result['error'] = 1;
                $result['status'] = 0;
            }
        }
        echo json_encode($result);
        exit();
    }

    function receiveToAll() {
        $this->layout = "ajax";
        if (!empty($_GET['transfer_order_id'])) {
            $user = $_GET['user'];
            $transfer_order = ClassRegistry::init('TransferOrder')->read(null, $_GET['transfer_order_id']);
            $file = $_GET['json'];
            $filename = "public/" . $file;
            if (file_exists($filename)) {
                $handle = fopen($filename, "r");
                $contents = fread($handle, filesize($filename));
                fclose($handle);
                if ($contents) {
                    $details = json_decode($contents);
                    $details = json_decode($details, true);
                    shell_exec("rm -r " . $filename);
                    if (preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/', $_GET['receive_date'])) {
                        $_GET['receive_date'] = $this->Helper->dateConvert($_GET['receive_date']);
                    }
                    if (!isset($_GET['receive_date']) || is_null($_GET['receive_date']) || $_GET['receive_date'] == '0000-00-00' || $_GET['receive_date'] == '') {
                        $this->data['TransferOrder']['id'] = $_GET['transfer_order_id'];
                        $this->data['TransferOrder']['is_process'] = 0;
                        $this->data['TransferOrder']['modified'] = date("Y-m-d H:i:s");
                        $this->data['TransferOrder']['modified_by'] = $user;
                        ClassRegistry::init('TransferOrder')->saveAll($this->data);
                        exit();
                    }
                    if ($transfer_order['TransferOrder']['status'] > 0 && $transfer_order['TransferOrder']['status'] < 3) {
                        $outStock = false;
                        $error_list = "";
                        $productOrder = array();
                        foreach ($details['detail'] as $detail) {
                            $losNum   = $detail['lots_number']!=""?$detail['lots_number']:'0';
                            $expDate  = $detail['expired_date']!=""?$detail['expired_date']:'0000-00-00';
                            $key      = $detail['product_id']."|".$losNum."|".$expDate."|".$detail['from_location_id'];
                            if (array_key_exists($key, $productOrder)){
                                $productOrder[$key]['qty'] += $this->Helper->replaceThousand($detail['qty_transfer']) * $this->Helper->replaceThousand($detail['uom_conversion']);
                            } else {
                                $productOrder[$key]['qty'] = $this->Helper->replaceThousand($detail['qty_transfer']) * $this->Helper->replaceThousand($detail['uom_conversion']);
                            }
                        }
                        foreach($productOrder AS $key => $order){
                            $totalOrder  = 0;
                            $extract     = explode("|", $key);
                            $productId   = $extract[0];
                            $lotsNumber  = $extract[1];
                            $expDate     = $extract[2];
                            $fromLoc     = $extract[3];
                            $qtyTransfer = $order['qty'];
                            $cmt = "SELECT SUM((inv.total_pb + total_to_in + total_cm + total_cycle + total_cus_consign_in) - (inv.total_so + inv.total_pos + inv.total_pbc + inv.total_to_out + total_cus_consign_out + inv.total_order)) AS total_qty FROM {$fromLoc}_inventory_total_details AS inv WHERE inv.product_id = {$productId} AND inv.lots_number = '{$lotsNumber}' AND inv.expired_date = '{$expDate}' AND inv.date <= '{$transfer_order['TransferOrder']['order_date']}' GROUP BY inv.product_id";
                            $totalStock = mysql_fetch_array(mysql_query($cmt));
                            // Total Order
                            $sqlOrder = mysql_query("SELECT SUM(qty) AS qty FROM stock_orders WHERE transfer_order_id = ".$_GET['transfer_order_id']." AND product_id = ".$productId." AND location_group_id = {$transfer_order['TransferOrder']['from_location_group_id']} AND location_id = {$fromLoc} AND lots_number = '{$lotsNumber}' AND expired_date = '{$expDate}' GROUP BY product_id");
                            if(mysql_num_rows($sqlOrder)){
                                $rowOrder = mysql_fetch_array($sqlOrder);
                                $totalOrder = $rowOrder[0];
                            }
                            if ($qtyTransfer > ($totalStock[0] + $totalOrder)) {
                                $error_list .= $detail['product_id'] . ": " . $qtyTransfer . " > " . $totalStock[0] . "\n";
                                $outStock = true;
                            }
                        }
                        if ($outStock == false) {
                            $r = 0;
                            $restCode = array();
                            $dateNow  = date("Y-m-d H:i:s");
                            // Load Model
                            $this->loadModel('TransferReceiveResult');
                            $this->TransferReceiveResult->create();
                            $transferRecResult = array();
                            $transferRecResult['TransferReceiveResult']['sys_code'] = md5(rand().strtotime(date("Y-m-d H:i:s")).$user);
                            $transferRecResult['TransferReceiveResult']['created']  = $dateNow;
                            $transferRecResult['TransferReceiveResult']['code']     = $_GET['receive_number'];
                            $transferRecResult['TransferReceiveResult']['transfer_order_id'] = $transfer_order['TransferOrder']['id'];
                            $transferRecResult['TransferReceiveResult']['date'] = $_GET['receive_date'];
                            $transferRecResult['TransferReceiveResult']['created_by'] = $user; 
                            if($this->TransferReceiveResult->save($transferRecResult)) {
                                $transferReceiveId = $this->TransferReceiveResult->id;
                                // Get Module Code
                                $modCode = $this->Helper->getModuleCode($_GET['receive_number'], $transferReceiveId, 'code', 'transfer_receive_results', '1');
                                // Updaet Module Code
                                $transferRecResult['TransferReceiveResult']['code'] = $modCode;
                                mysql_query("UPDATE transfer_receive_results SET code = '".$modCode."' WHERE id = ".$transferReceiveId);
                                // Convert to REST
                                $restCode[$r] = $this->Helper->convertToDataSync($transferRecResult['TransferReceiveResult'], 'transfer_receive_results');
                                $restCode[$r]['dbtodo'] = 'transfer_receive_results';
                                $restCode[$r]['actodo'] = 'is';
                                $r++;
                                $k = 0;
                                foreach ($details['detail'] as $detail) {
                                    if ($detail['product_id'] != '' AND $detail['qty_transfer'] != '' AND $detail['qty_transfer'] > 0) {
                                        $qty_inv = $this->Helper->replaceThousand($detail['qty_transfer']) * $this->Helper->replaceThousand($detail['uom_conversion']);

                                        if ($qty_inv > 0) {
                                            $dateNow = $_GET['receive_date'];
                                            /* Transfer Order Out */
                                            // Update Inventory (Transfer Out)
                                            $dataOut = array();
                                            $dataOut['module_type']       = 3;
                                            $dataOut['transfer_order_id'] = $_GET['transfer_order_id'];
                                            $dataOut['product_id']        = $detail['product_id'];
                                            $dataOut['location_id']       = $detail['from_location_id'];
                                            $dataOut['location_group_id'] = $transfer_order['TransferOrder']['from_location_group_id'];
                                            $dataOut['lots_number']  = $detail['lots_number'];
                                            $dataOut['expired_date'] = $detail['expired_date']!='0000-00-00'?$detail['expired_date']:'0000-00-00';
                                            $dataOut['date']         = $dateNow;
                                            $dataOut['total_qty']    = $qty_inv;
                                            $dataOut['total_order']  = $qty_inv;
                                            $dataOut['total_free']   = 0;
                                            $dataOut['user_id']      = $user;
                                            $dataOut['customer_id']  = "";
                                            $dataOut['vendor_id']    = "";
                                            $dataOut['unit_cost']    = 0;
                                            $dataOut['unit_price']   = 0;
                                            // Update Invetory Location
                                            $this->Inventory->saveInventory($dataOut);
                                            // Update Inventory Group
                                            $this->Inventory->saveGroupTotalDetail($dataOut);
                                            // Convert to REST
                                            $restCode[$r] = $this->Helper->convertToDataSync($dataOut, 'inventories');
                                            $restCode[$r]['module_type']  = 3;
                                            $restCode[$r]['expired_date'] = $detail['expired_date'];
                                            $restCode[$r]['total_qty']    = $qty_inv;
                                            $restCode[$r]['total_order']  = $qty_inv;
                                            $restCode[$r]['total_free']   = 0;
                                            $restCode[$r]['expired_date'] = $dataOut['expired_date'];
                                            $restCode[$r]['customer_id']  = "";
                                            $restCode[$r]['vendor_id']    = "";
                                            $restCode[$r]['unit_cost']    = 0;
                                            $restCode[$r]['unit_price']   = 0;
                                            $restCode[$r]['transfer_order_id'] = $this->Helper->getSQLSyncCode("transfer_orders", $_GET['transfer_order_id']);
                                            $restCode[$r]['product_id']        = $this->Helper->getSQLSyncCode("products", $detail['product_id']);
                                            $restCode[$r]['location_id']       = $this->Helper->getSQLSyncCode("locations", $detail['from_location_id']);
                                            $restCode[$r]['location_group_id'] = $this->Helper->getSQLSyncCode("location_groups", $transfer_order['TransferOrder']['from_location_group_id']);
                                            $restCode[$r]['user_id'] = $this->Helper->getSQLSyncCode("users", $user);
                                            $restCode[$r]['dbtype']  = 'saveInv,GroupDetail';
                                            $restCode[$r]['actodo']  = 'inv';
                                            $r++;

                                            /* Transfer Order In */
                                            // Update Inventory (Transfer In)
                                            $dataIn = array();
                                            $dataIn['module_type']       = 2;
                                            $dataIn['transfer_order_id'] = $_GET['transfer_order_id'];
                                            $dataIn['product_id']        = $detail['product_id'];
                                            $dataIn['location_id']       = $detail['to_location_id'];
                                            $dataIn['location_group_id'] = $transfer_order['TransferOrder']['to_location_group_id'];
                                            $dataIn['lots_number']  = $detail['lots_number'];
                                            $dataIn['expired_date'] = $detail['expired_date']!='0000-00-00'?$detail['expired_date']:'0000-00-00';
                                            $dataIn['date']         = $dateNow;
                                            $dataIn['total_qty']    = $qty_inv;
                                            $dataIn['total_order']  = $qty_inv;
                                            $dataIn['total_free']   = 0;
                                            $dataIn['user_id']      = $user;
                                            $dataIn['customer_id']  = "";
                                            $dataIn['vendor_id']    = "";
                                            $dataIn['unit_cost']    = 0;
                                            $dataIn['unit_price']   = 0;
                                            // Update Invetory Location
                                            $this->Inventory->saveInventory($dataIn);
                                            // Update Inventory Group
                                            $this->Inventory->saveGroupTotalDetail($dataIn);
                                            // Convert to REST
                                            $restCode[$r] = $this->Helper->convertToDataSync($dataIn, 'inventories');
                                            $restCode[$r]['module_type']  = 2;
                                            $restCode[$r]['total_qty']    = $qty_inv;
                                            $restCode[$r]['total_order']  = $qty_inv;
                                            $restCode[$r]['total_free']   = 0;
                                            $restCode[$r]['expired_date'] = $dataIn['expired_date'];
                                            $restCode[$r]['customer_id']  = "";
                                            $restCode[$r]['vendor_id']    = "";
                                            $restCode[$r]['unit_cost']    = 0;
                                            $restCode[$r]['unit_price']   = 0;
                                            $restCode[$r]['transfer_order_id'] = $this->Helper->getSQLSyncCode("transfer_orders", $_GET['transfer_order_id']);
                                            $restCode[$r]['product_id']        = $this->Helper->getSQLSyncCode("products", $detail['product_id']);
                                            $restCode[$r]['location_id']       = $this->Helper->getSQLSyncCode("locations", $detail['to_location_id']);
                                            $restCode[$r]['location_group_id'] = $this->Helper->getSQLSyncCode("location_groups", $transfer_order['TransferOrder']['to_location_group_id']);
                                            $restCode[$r]['user_id'] = $this->Helper->getSQLSyncCode("users", $user);
                                            $restCode[$r]['dbtype']  = 'saveInv,GroupDetail';
                                            $restCode[$r]['actodo']  = 'inv';
                                            $r++;

                                            // Save Transfer Receive
                                            $this->data['TransferReceive']['transfer_receive_result_id'] = $transferReceiveId;
                                            $this->data['TransferReceive']['transfer_order_detail_id'] = $detail['detail_id'];
                                            $this->data['TransferReceive']['transfer_order_id'] = $_GET['transfer_order_id'];
                                            $this->data['TransferReceive']['lots_number']  = $detail['lots_number'];
                                            $this->data['TransferReceive']['expired_date'] = $detail['expired_date'];
                                            $this->data['TransferReceive']['product_id'] = $detail['product_id'];
                                            $this->data['TransferReceive']['qty'] = $detail['qty_transfer'];
                                            $this->data['TransferReceive']['qty_uom_id'] = $detail['purchase_uom'];
                                            $this->data['TransferReceive']['conversion'] = $detail['uom_conversion'];
                                            $this->data['TransferReceive']['status'] = 1;
                                            $this->data['TransferReceive']['created_by'] = $user;
                                            ClassRegistry::init('TransferReceive')->saveAll($this->data);
                                            // Convert to REST
                                            $restCode[$r] = $this->Helper->convertToDataSync($this->data['TransferReceive'], 'transfer_receives');
                                            $restCode[$r]['transfer_order_id']  = $this->Helper->getSQLSysCode("transfer_orders", $_GET['transfer_order_id']);
                                            $restCode[$r]['transfer_receive_result_id']  = $this->Helper->getSQLSysCode("transfer_receive_results", $transferReceiveId);
                                            $restCode[$r]['transfer_order_detail_id'] = $this->Helper->getSQLSysCode("transfer_order_details", $detail['detail_id']);
                                            $restCode[$r]['modified'] = $dateNow;
                                            $restCode[$r]['dbtodo']   = 'transfer_receives';
                                            $restCode[$r]['actodo']   = 'is';
                                            $r++;
                                            $k++;
                                            // Reset Inventory Order
                                            $this->Inventory->saveGroupQtyOrder($transfer_order['TransferOrder']['from_location_group_id'], $detail['from_location_id'], $detail['product_id'], $detail['lots_number'], $detail['expired_date'], $qty_inv, $transfer_order['TransferOrder']['order_date'], '-');
                                            // Convert to REST
                                            $restCode[$r]['group']    = $this->Helper->getSQLSyncCode("location_groups", $transfer_order['TransferOrder']['from_location_group_id']);
                                            $restCode[$r]['location'] = $this->Helper->getSQLSyncCode("locations", $detail['from_location_id']);
                                            $restCode[$r]['product']  = $this->Helper->getSQLSyncCode("products", $detail['product_id']);
                                            $restCode[$r]['lots']   = $detail['lots_number'];
                                            $restCode[$r]['expd']   = $detail['expired_date']!='0000-00-00'?$detail['expired_date']:'0000-00-00';
                                            $restCode[$r]['qty']    = $qty_inv;
                                            $restCode[$r]['date']   = $transfer_order['TransferOrder']['order_date'];
                                            $restCode[$r]['syml']   = '-';
                                            $restCode[$r]['dbtype'] = 'saveOrder';
                                            $restCode[$r]['actodo'] = 'inv';
                                            $r++;
                                        }
                                    }
                                }

                                // Update Status Transfer Order
                                $sqlTo = mysql_query("SELECT sum(qty) as total FROM transfer_order_details WHERE transfer_order_id = " . $_GET['transfer_order_id']);
                                $totalTo = mysql_fetch_array($sqlTo);
                                $sqlReceive = mysql_query("SELECT sum(qty) as total FROM transfer_receives WHERE transfer_order_id = " . $_GET['transfer_order_id'] . " AND status = 1");
                                $totalReceive = mysql_fetch_array($sqlReceive);
                                $this->data['TransferOrder']['id'] = $_GET['transfer_order_id'];
                                $this->data['TransferOrder']['fulfillment_date'] = $_GET['receive_date'];
                                $this->data['TransferOrder']['is_process'] = 0;
                                $this->data['TransferOrder']['error'] = 0;
                                $this->data['TransferOrder']['modified'] = date("Y-m-d H:i:s");
                                $this->data['TransferOrder']['modified_by'] = $user;
                                if ($totalReceive[0] >= $totalTo[0]) {
                                    $this->data['TransferOrder']['status'] = 3;
                                } else {
                                    $this->data['TransferOrder']['status'] = 2;
                                }
                                ClassRegistry::init('TransferOrder')->saveAll($this->data);
                                // Convert to REST
                                $restCode[$r] = $this->Helper->convertToDataSync($this->data['TransferOrder'], 'transfer_orders');
                                $restCode[$r]['dbtodo'] = 'transfer_orders';
                                $restCode[$r]['actodo'] = 'ut';
                                $restCode[$r]['con']    = "sys_code = '".$transfer_order['TransferOrder']['sys_code']."'";
                                $r++;
                                if($this->data['TransferOrder']['status'] == 3){
                                    // Detele Tmp Stock Order
                                    mysql_query("DELETE FROM `stock_orders` WHERE `transfer_order_id`=".$_GET['transfer_order_id'].";");
                                    // Convert to REST
                                    $restCode[$r]['dbtodo'] = 'stock_orders';
                                    $restCode[$r]['actodo'] = 'dt';
                                    $restCode[$r]['con']    = "transfer_order_id = ".$transfer_order['TransferOrder']['sys_code'];
                                }
                                // Save File Send
                                $this->Helper->sendFileToSync($restCode, 0, 0);
                                // Save User Activity
                                $this->Helper->saveUserActivity($user['User']['id'], 'Transfer Receive', 'Save Add New', $transferReceiveId);
                            }else{
                                $this->data['TransferOrder']['id'] = $_GET['transfer_order_id'];
                                $this->data['TransferOrder']['is_process'] = 0;
                                $this->data['TransferOrder']['status'] = 1;
                                $this->data['TransferOrder']['error'] = 1;
                                $this->data['TransferOrder']['modified'] = date("Y-m-d H:i:s");
                                $this->data['TransferOrder']['modified_by'] = $user;
                                ClassRegistry::init('TransferOrder')->saveAll($this->data);
                                $this->Helper->saveUserActivity($user['User']['id'], 'Transfer Receive', 'Save Receive (Error)', $_GET['transfer_order_id']);
                            }
                        } else {
                            $this->data['TransferOrder']['id'] = $_GET['transfer_order_id'];
                            $this->data['TransferOrder']['is_process'] = 0;
                            $this->data['TransferOrder']['status'] = 1;
                            $this->data['TransferOrder']['error'] = 1;
                            $this->data['TransferOrder']['modified'] = date("Y-m-d H:i:s");
                            $this->data['TransferOrder']['modified_by'] = $user;
                            ClassRegistry::init('TransferOrder')->saveAll($this->data);
                            $this->Helper->saveUserActivity($user['User']['id'], 'Transfer Receive', 'Save Receive (Error Out of Stock)', $_GET['transfer_order_id']);
                        }
                    } else {
                        $this->data['TransferOrder']['id'] = $_GET['transfer_order_id'];
                        $this->data['TransferOrder']['is_process'] = 0;
                        $this->data['TransferOrder']['modified'] = date("Y-m-d H:i:s");
                        $this->data['TransferOrder']['modified_by'] = $user;
                        ClassRegistry::init('TransferOrder')->saveAll($this->data);
                        $this->Helper->saveUserActivity($user['User']['id'], 'Transfer Receive', 'Save Receive (Error Status)', $_GET['transfer_order_id']);
                    }
                } else {
                    $this->data['TransferOrder']['id'] = $_GET['transfer_order_id'];
                    $this->data['TransferOrder']['is_process'] = 0;
                    $this->data['TransferOrder']['modified'] = date("Y-m-d H:i:s");
                    $this->data['TransferOrder']['modified_by'] = $user;
                    ClassRegistry::init('TransferOrder')->saveAll($this->data);
                    $this->Helper->saveUserActivity($user['User']['id'], 'Transfer Receive', 'Save Receive (Error Contents)', $_GET['transfer_order_id']);
                    exit();
                }
            } else {
                $this->data['TransferOrder']['id'] = $_GET['transfer_order_id'];
                $this->data['TransferOrder']['is_process'] = 0;
                $this->data['TransferOrder']['modified'] = date("Y-m-d H:i:s");
                $this->data['TransferOrder']['modified_by'] = $user;
                ClassRegistry::init('TransferOrder')->saveAll($this->data);
                $this->Helper->saveUserActivity($user['User']['id'], 'Transfer Receive', 'Save Receive (Error Files)', $_GET['transfer_order_id']);
                exit();
            }
        }
        exit();
    }

    function checkReceiveAllTO($id) {
        if (empty($id)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $this->loadModel('TransferOrder');
        $result['empty'] = 1;
        $transfer = $this->TransferOrder->find('first', array('conditions' => array('TransferOrder.id' => $id), 'fields' => array('TransferOrder.status', 'TransferOrder.is_process', 'TransferOrder.error')));
        if ($transfer['TransferOrder']['status'] == 1 && $transfer['TransferOrder']['error'] == 1 && $transfer['TransferOrder']['is_process'] == 0) {
            $result['error'] = 2;
        } else {
            if ($transfer['TransferOrder']['status'] > 2 && $transfer['TransferOrder']['is_process'] == 0) {
                $result['success'] = 1;
            } else if ($transfer['TransferOrder']['status'] == 2 && $transfer['TransferOrder']['is_process'] == 0) {
                $result['success'] = 2;
            } else if ($transfer['TransferOrder']['status'] == 1 && $transfer['TransferOrder']['is_process'] == 0) {
                $result['error'] = 1;
            }
        }
        echo json_encode($result);
        exit();
    }

    function deliveryStock() {
        $this->layout = 'ajax';
        if (!empty($_GET)) {
            $this->loadModel("SalesOrder");
            $this->loadModel("Delivery");
            $delivery = $this->Delivery->read(null, $_GET['id']);
            if ($delivery['Delivery']['status'] == 1 && $delivery['Delivery']['is_active'] == 1) {
                $delivery['Delivery']['id'] = $_GET['id'];
                $delivery['Delivery']['modified'] = date("Y-m-d H:i:s");
                $delivery['Delivery']['modified_by'] = $_GET['user'];
                $delivery['Delivery']['status'] = 2;
                $delivery['Delivery']['is_process'] = 0;
                if ($this->Delivery->save($delivery)) {
                    $delivery_id = $this->Delivery->id;
                    $name = $_GET['json'];
                    $filename = "public/" . $name;
                    $listQtyFailed = "";
                    $listSalesOrder = array();
                    $arrayDate = array();
                    if (file_exists($filename)) {
                        $handle = fopen($filename, "r");
                        $contents = fread($handle, filesize($filename));
                        fclose($handle);
                        if ($contents) {
                            $details = json_decode($contents);
                            $details = json_decode($details, true);
                            shell_exec("rm -r " . $filename);
                            $lenthData = count($details['detail']);
                            $j = 1;
                            foreach ($details['detail'] as $detail) {
                                $salesOrderId = $detail['sales_order_id'];
                                // Update Status Sales Order
                                if (!in_array($salesOrderId, $listSalesOrder)) {
                                    array_push($listSalesOrder, $salesOrderId);
                                    $salesOrder = $this->SalesOrder->read(null, $salesOrderId);
                                    $arrayDate[$salesOrderId] = $salesOrder['SalesOrder']['order_date'];
                                    $this->SalesOrder->updateAll(
                                            array('SalesOrder.status' => 2), 
                                            array('SalesOrder.id' => $detail['sales_order_id'])
                                    );
                                    // Delete Stock Order
                                    mysql_query("DELETE FROM `stock_orders` WHERE  `sales_order_id`= " . $detail['sales_order_id']);
                                }
                                $j++;
                            }
                            if ($lenthData > $j) {
                                echo "Process done: " . $j . " of " . $lenthData;
                            }
                            echo $listQtyFailed;
                        } else {
                            $delivery['Delivery']['id'] = $_GET['id'];
                            $delivery['Delivery']['modified'] = date("Y-m-d H:i:s");
                            $delivery['Delivery']['modified_by '] = $_GET['user'];
                            $delivery['Delivery']['status'] = 1;
                            $delivery['Delivery']['is_process'] = 0;
                            $this->Delivery->save($delivery);
                        }
                    } else {
                        $delivery['Delivery']['id'] = $_GET['id'];
                        $delivery['Delivery']['modified'] = date("Y-m-d H:i:s");
                        $delivery['Delivery']['modified_by '] = $_GET['user'];
                        $delivery['Delivery']['status'] = 1;
                        $delivery['Delivery']['is_process'] = 0;
                        $this->Delivery->save($delivery);
                    }
                }
            } else {
                $delivery['Delivery']['id'] = $_GET['id'];
                $delivery['Delivery']['modified'] = date("Y-m-d H:i:s");
                $delivery['Delivery']['modified_by '] = $_GET['user'];
                $delivery['Delivery']['is_process'] = 0;
                $this->Delivery->save($delivery);
            }
        }
        exit();
    }

    function checkDnPickUp($id) {
        if (empty($id)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $this->loadModel('Delivery');
        $result['empty'] = 1;
        $dn = $this->Delivery->find('first', array('conditions' => array('Delivery.id' => $id), 'fields' => array('Delivery.status', 'Delivery.is_process', 'Delivery.is_active')));
        if ($dn['Delivery']['status'] > 1 && $dn['Delivery']['is_process'] == 0 && $dn['Delivery']['is_active'] == 1) {
            $result['success'] = 1;
            $result['dn_id'] = $id;
        } else if ($dn['Delivery']['status'] == 1 && $dn['Delivery']['is_process'] == 0 && $dn['Delivery']['is_active'] == 1) {
            $result['error'] = 1;
        }
        echo json_encode($result);
        exit();
    }
    
    function deliveryPos() {
        if (!empty($_GET['sales_order_id'])) {
            // Get Value From Process First
            $sales_order_id = $_GET['sales_order_id'];
            $glId           = $_GET['gl'];
            $user           = $_GET['user'];
            $sales_order    = ClassRegistry::init('SalesOrder')->read(null, $_GET['sales_order_id']);
            $company_id     = $_GET['company_id'];
            $branchId       = $sales_order['SalesOrder']['branch_id'];
            $salesOrderCode = $sales_order['SalesOrder']['so_code'];
            $calculateCogs  = $_GET['calculate_cogs'];
            $file           = $_GET['json'];
            $filename       = "public/pos/" . $file;
            $totalPriceSales = 0;
            $return = true;
            // Check File Process Second Exist
            if (file_exists($filename)) {
                $handle = fopen($filename, "r");
                $contents = fread($handle, filesize($filename));
                fclose($handle);
                if (!empty($contents)) {
                    $r = 0;
                    $restCode = array();
                    $dateNow  = date("Y-m-d H:i:s");
                    // Load Json & Model
                    $details = json_decode($contents, true);
                    $this->loadModel('SalesOrderDetail');
                    $this->loadModel('SalesOrderService');
                    $this->loadModel('SalesOrder');
                    $this->loadModel("InventoryValuation");
                    $this->loadModel('GeneralLedger');
                    $this->loadModel('GeneralLedgerDetail');
                    $this->loadModel('AccountType');
                    $this->loadModel('PosPickDetail');

                    // Find Chart Account
                    $salesDiscAccount = $this->AccountType->findById(11);
                    // Loop Json
                    foreach ($details as $detail) {
                        if ($detail['product_id'] != '') {
                            // Save Sales Order Detail
                            $this->SalesOrderDetail->create();
                            $salesOrderDetail = array();
                            $salesOrderDetail['SalesOrderDetail']['sys_code']         = md5(rand().strtotime(date("Y-m-d H:i:s")).$user);
                            $salesOrderDetail['SalesOrderDetail']['sales_order_id']   = $sales_order_id;
                            $salesOrderDetail['SalesOrderDetail']['discount_id']      = $detail['discount_id']!=''?$detail['discount_id']:0;
                            $salesOrderDetail['SalesOrderDetail']['discount_amount']  = $this->Helper->replaceThousand($detail['discount_amount']);
                            $salesOrderDetail['SalesOrderDetail']['discount_percent'] = $this->Helper->replaceThousand($detail['discount_percent']);
                            $salesOrderDetail['SalesOrderDetail']['product_id']  = $detail['product_id'];
                            $salesOrderDetail['SalesOrderDetail']['qty_uom_id']  = $detail['qty_uom_id'];
                            $salesOrderDetail['SalesOrderDetail']['conversion']  = $detail['conversion'];
                            $salesOrderDetail['SalesOrderDetail']['qty']         = $this->Helper->replaceThousand($detail['qty']);
                            $salesOrderDetail['SalesOrderDetail']['qty_free']    = $this->Helper->replaceThousand($detail['qty_free']);
                            $salesOrderDetail['SalesOrderDetail']['unit_price']  = $this->Helper->replaceThousand($detail['unit_price']);
                            $salesOrderDetail['SalesOrderDetail']['total_price'] = $this->Helper->replaceThousand($detail['total_price']);
                            $salesOrderDetail['SalesOrderDetail']['lots_number']  = $this->Helper->replaceThousand($detail['lots_number']);
                            $salesOrderDetail['SalesOrderDetail']['expired_date'] = $this->Helper->replaceThousand($detail['expired_date']);
                            $totalPriceSales += $this->Helper->replaceThousand($detail['total_price']) - $this->Helper->replaceThousand($detail['discount_amount']);
                            $this->SalesOrderDetail->save($salesOrderDetail);
                            $salesOrderDetailId = $this->SalesOrderDetail->id;
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($salesOrderDetail['SalesOrderDetail'], 'sales_order_details');
                            $restCode[$r]['dbtodo']   = 'sales_order_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                            // Get Qty Order
                            $dateSales = date("Y-m-d");
                            $qtyOrder  = ($salesOrderDetail['SalesOrderDetail']['qty'] * $salesOrderDetail['SalesOrderDetail']['conversion']);
                            $qtyFree   = ($salesOrderDetail['SalesOrderDetail']['qty_free'] * $salesOrderDetail['SalesOrderDetail']['conversion']);
                            $priceSales = $detail['total_price'] - $detail['discount_amount'];
                            $queryProductCodeName = mysql_query("SELECT CONCAT(code,' - ',name) AS name, unit_cost AS unit_cost, small_val_uom FROM products WHERE id=".$detail['product_id']);
                            $dataProductCodeName  = mysql_fetch_array($queryProductCodeName);
                            
                            $totalQtyOrder = $this->Helper->replaceThousand($detail['qty_order']);
                            /* Inventory Valuation */
                            $inv_valutaion = array();
                            $this->InventoryValuation->create();
                            $inv_valutaion['InventoryValuation']['sys_code']          = md5(rand().strtotime(date("Y-m-d H:i:s")).$user);
                            $inv_valutaion['InventoryValuation']['point_of_sales_id'] = $sales_order_id;
                            $inv_valutaion['InventoryValuation']['company_id'] = $company_id;
                            $inv_valutaion['InventoryValuation']['branch_id']  = $branchId;
                            $inv_valutaion['InventoryValuation']['type'] = "Invoice";
                            $inv_valutaion['InventoryValuation']['reference']   = $salesOrderCode;
                            $inv_valutaion['InventoryValuation']['customer_id'] = $sales_order['SalesOrder']['customer_id'];
                            $inv_valutaion['InventoryValuation']['date'] = date("Y-m-d");
                            $inv_valutaion['InventoryValuation']['pid']  = $detail['product_id'];
                            $inv_valutaion['InventoryValuation']['qty']  = "-" . $this->Helper->replaceThousand(number_format(($totalQtyOrder / $dataProductCodeName['small_val_uom']), 2));
                            $inv_valutaion['InventoryValuation']['small_qty'] = "-" . $totalQtyOrder;
                            $inv_valutaion['InventoryValuation']['cost'] = null;
                            $inv_valutaion['InventoryValuation']['is_var_cost'] = 1;
                            $inv_valutaion['InventoryValuation']['created'] = $dateNow;
                            $this->InventoryValuation->saveAll($inv_valutaion);
                            $inv_valutation_id = $this->InventoryValuation->getLastInsertId();
                            $inventoryAsset = 0;
                            $cogs = 0;
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($inv_valutaion['InventoryValuation'], 'inventory_valuations');
                            $restCode[$r]['dbtodo']   = 'inventory_valuations';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                            
                            $totalQtyOrder = $this->Helper->replaceThousand($detail['qty_order']);
                            // Update Inventory
                            $dataGroup = array();
                            $dataGroup['module_type']       = 8;
                            $dataGroup['point_of_sales_id'] = $sales_order_id;
                            $dataGroup['product_id']        = $detail['product_id'];
                            $dataGroup['location_group_id'] = $sales_order['SalesOrder']['location_group_id'];
                            $dataGroup['date']         = $dateSales;
                            $dataGroup['total_qty']    = $totalQtyOrder;
                            $dataGroup['total_order']  = $qtyOrder;
                            $dataGroup['total_free']   = $qtyFree;
                            // Update Inventory Group
                            $this->Inventory->saveGroupTotalDetail($dataGroup);
                            // Convert to REST
                            $restCode[$r]['module_type']       = 8;
                            $restCode[$r]['point_of_sales_id'] = $this->Helper->getSQLSyncCode("sales_orders", $sales_order_id);
                            $restCode[$r]['date']              = $dateSales;
                            $restCode[$r]['total_qty']         = $totalQtyOrder;
                            $restCode[$r]['total_order']       = $qtyOrder;
                            $restCode[$r]['total_free']        = $qtyFree;
                            $restCode[$r]['product_id']        = $this->Helper->getSQLSyncCode("products", $detail['product_id']);
                            $restCode[$r]['location_group_id'] = $this->Helper->getSQLSyncCode("location_groups", $sales_order['SalesOrder']['location_group_id']);
                            $restCode[$r]['dbtype']  = 'GroupDetail';
                            $restCode[$r]['actodo']  = 'inv';
                            $r++;
                            // Get Loction Setting
                            $locSetting = ClassRegistry::init('LocationSetting')->findById(4);
                            $locCon     = '';
                            if($locSetting['LocationSetting']['location_status'] == 1){
                                $locCon = ' AND is_for_sale = 1';
                            }
                            // Get Lots, Expired, Total Qty
                            $invInfos   = array();
                            $index      = 0;
                            $totalOrder = $totalQtyOrder;
                            // Calculate Location, Lot, Expired Date
                            $sqlInventory = mysql_query("SELECT SUM(IFNULL(group_totals.total_qty,0) - IFNULL(group_totals.total_order,0)) AS total_qty, group_totals.location_id AS location_id, group_totals.lots_number AS lots_number, group_totals.expired_date AS expired_date FROM ".$sales_order['SalesOrder']['location_group_id']."_group_totals AS group_totals WHERE group_totals.location_id IN (SELECT id FROM locations WHERE location_group_id = ".$sales_order['SalesOrder']['location_group_id'].$locCon.") AND group_totals.product_id = ".$detail['product_id']." AND group_totals.lots_number = '".$detail['lots_number']."' AND group_totals.expired_date = '".$detail['expired_date']."' GROUP BY group_totals.location_id, group_totals.product_id, group_totals.lots_number, group_totals.expired_date HAVING total_qty > 0 ORDER BY group_totals.lots_number, group_totals.expired_date, group_totals.location_id ASC");
                            while($rowInventory = mysql_fetch_array($sqlInventory)){
                                if($totalOrder > 0 && $rowInventory['total_qty'] > 0){
                                    if($rowInventory['total_qty'] >= $totalOrder) {
                                        $invInfos[$index]['total_qty']    = $totalOrder;
                                        $invInfos[$index]['location_id']  = $rowInventory['location_id'];
                                        $invInfos[$index]['lots_number']  = $rowInventory['lots_number'];
                                        $invInfos[$index]['expired_date'] = $rowInventory['expired_date'];
                                        $totalOrder = 0;
                                        ++$index;
                                    } else if($rowInventory['total_qty'] < $totalOrder) {
                                        $invInfos[$index]['total_qty']    = $rowInventory['total_qty'];
                                        $invInfos[$index]['location_id']  = $rowInventory['location_id'];
                                        $invInfos[$index]['lots_number']  = $rowInventory['lots_number'];
                                        $invInfos[$index]['expired_date'] = $rowInventory['expired_date'];
                                        $totalOrder = $totalOrder - $rowInventory['total_qty'];
                                        ++$index;
                                    }
                                }
                            }
                            // Check Warehouse Option Allow Negative
                            $warehouseOption = ClassRegistry::init('LocationGroup')->findById($sales_order['SalesOrder']['location_group_id']);
                            if($warehouseOption['LocationGroup']['allow_negative_stock'] == 1 && $totalOrder > 0){ // Allow Negative Stock
                                $sqlLocation = mysql_query("SELECT id FROM locations WHERE location_group_id = ".$sales_order['SalesOrder']['location_group_id']." ORDER BY id ASC LIMIT 1");
                                $rowLocation = mysql_fetch_array($sqlLocation);
                                $invInfos[$index]['total_qty']    = $totalOrder;
                                $invInfos[$index]['location_id']  = $rowLocation['id'];
                                $invInfos[$index]['lots_number']  = $detail['lots_number'];
                                $invInfos[$index]['expired_date'] = $detail['expired_date'];
                            }
                            
                            foreach($invInfos AS $invInfo){
                                // Update Inventory (POS)
                                $data = array();
                                $data['module_type']       = 8;
                                $data['point_of_sales_id'] = $sales_order_id;
                                $data['product_id']        = $detail['product_id'];
                                $data['location_id']       = $invInfo['location_id'];
                                $data['location_group_id'] = $sales_order['SalesOrder']['location_group_id'];
                                $data['lots_number']  = $invInfo['lots_number']!=''?$invInfo['lots_number']:0;
                                $data['expired_date'] = $invInfo['expired_date']!=''?$invInfo['expired_date']:'0000-00-00';
                                $data['date']         = $dateSales;
                                $data['total_qty']    = $invInfo['total_qty'];
                                $data['total_order']  = $invInfo['total_qty'];
                                $data['total_free']   = 0;
                                $data['user_id']      = $user;
                                $data['customer_id']  = $sales_order['SalesOrder']['customer_id'];
                                $data['vendor_id']    = "";
                                $data['unit_cost']    = 0;
                                $data['unit_price']   = $priceSales;
                                // Update Invetory Location
                                $this->Inventory->saveInventory($data);
                                // Convert to REST
                                $restCode[$r] = $this->Helper->convertToDataSync($data, 'inventories');
                                $restCode[$r]['module_type']  = 8;
                                $restCode[$r]['total_qty']    = $invInfo['total_qty'];
                                $restCode[$r]['total_order']  = $invInfo['total_qty'];
                                $restCode[$r]['total_free']   = 0;
                                $restCode[$r]['expired_date'] = $data['expired_date'];
                                $restCode[$r]['vendor_id']    = "";
                                $restCode[$r]['unit_cost']    = 0;
                                $restCode[$r]['unit_price']   = $priceSales;
                                $restCode[$r]['customer_id']  = $this->Helper->getSQLSyncCode("customers", $sales_order['SalesOrder']['customer_id']);
                                $restCode[$r]['point_of_sales_id'] = $this->Helper->getSQLSyncCode("sales_orders", $sales_order_id);
                                $restCode[$r]['product_id']        = $this->Helper->getSQLSyncCode("products", $detail['product_id']);
                                $restCode[$r]['location_id']       = $this->Helper->getSQLSyncCode("locations", $invInfo['location_id']);
                                $restCode[$r]['location_group_id'] = $this->Helper->getSQLSyncCode("location_groups", $sales_order['SalesOrder']['location_group_id']);
                                $restCode[$r]['user_id']           = $this->Helper->getSQLSyncCode("users", $user);
                                $restCode[$r]['dbtype']  = 'saveInv';
                                $restCode[$r]['actodo']  = 'inv';
                                $r++;
                                //Insert Into Delivery Detail
                                $posPickDetail = array();
                                $this->PosPickDetail->create();
                                $posPickDetail['PosPickDetail']['sales_order_id'] = $sales_order_id;
                                $posPickDetail['PosPickDetail']['sales_order_detail_id'] = $salesOrderDetailId;
                                $posPickDetail['PosPickDetail']['product_id']     = $detail['product_id'];
                                $posPickDetail['PosPickDetail']['location_id']    = $invInfo['location_id'];
                                $posPickDetail['PosPickDetail']['lots_number']    = $invInfo['lots_number']!=''?$invInfo['lots_number']:0;
                                $posPickDetail['PosPickDetail']['expired_date']   = $invInfo['expired_date']!=''?$invInfo['expired_date']:'0000-00-00';
                                $posPickDetail['PosPickDetail']['total_qty']      = $invInfo['total_qty'];
                                $posPickDetail['PosPickDetail']['created']        = $dateNow;
                                $this->PosPickDetail->save($posPickDetail);
                                // Convert to REST
                                $restCode[$r] = $this->Helper->convertToDataSync($posPickDetail['PosPickDetail'], 'pos_pick_details');
                                $restCode[$r]['dbtodo']   = 'pos_pick_details';
                                $restCode[$r]['actodo']   = 'is';
                                $r++;
                            }

                            // General Ledger Detail (Product)
                            $this->GeneralLedgerDetail->create();
                            $generalLedgerDetail = array();
                            $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $glId;
                            $queryIncAccount = mysql_query("SELECT IFNULL((IFNULL((SELECT chart_account_id FROM accounts WHERE product_id = ".$detail['product_id']." AND account_type_id=8),(SELECT chart_account_id FROM pgroup_accounts WHERE pgroup_id = (SELECT pgroup_id FROM product_pgroups WHERE product_id = ".$detail['product_id']." ORDER BY id  DESC LIMIT 1) AND account_type_id=8))),(SELECT chart_account_id FROM account_types WHERE id=8))");
                            $dataIncAccount  = mysql_fetch_array($queryIncAccount);
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $dataIncAccount[0];
                            $generalLedgerDetail['GeneralLedgerDetail']['company_id']  = $sales_order['SalesOrder']['company_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['branch_id']   = $sales_order['SalesOrder']['branch_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['customer_id'] = $sales_order['SalesOrder']['customer_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['product_id']  = $detail['product_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['type'] = 'POS';
                            $generalLedgerDetail['GeneralLedgerDetail']['debit'] = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $this->Helper->replaceThousand($detail['total_price']);
                            $generalLedgerDetail['GeneralLedgerDetail']['memo'] = 'ICS: POS # ' . $salesOrderCode . ' ' . $dataProductCodeName[0];
                            $generalLedgerDetail['GeneralLedgerDetail']['class_id'] = $detail['class_id'];
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                            // General Ledger Detail Discount
                            if ($this->Helper->replaceThousand($detail['discount_amount']) > 0) {
                                $this->GeneralLedgerDetail->create();
                                $generalLedgerDetail = array();
                                $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $glId;
                                $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $salesDiscAccount['AccountType']['chart_account_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['company_id']  = $sales_order['SalesOrder']['company_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['branch_id']   = $sales_order['SalesOrder']['branch_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['location_group_id'] = $sales_order['SalesOrder']['location_group_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['customer_id'] = $sales_order['SalesOrder']['customer_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['product_id']  = $detail['product_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['type']   = 'POS';
                                $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $this->Helper->replaceThousand($detail['discount_amount']);
                                $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                                $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: POS # ' . $salesOrderCode . ' ' . $dataProductCodeName[0] . ' Discount';
                                $generalLedgerDetail['GeneralLedgerDetail']['class_id'] = $detail['class_id'];
                                $this->GeneralLedgerDetail->save($generalLedgerDetail);
                                // Convert to REST
                                $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                                $restCode[$r]['dbtodo']   = 'general_ledger_details';
                                $restCode[$r]['actodo']   = 'is';
                                $r++;
                            }

                            // General Ledger Detail (Asset Inventory)
                            $this->GeneralLedgerDetail->create();
                            $generalLedgerDetail = array();
                            $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $glId;
                            $queryInvAccount = mysql_query("SELECT IFNULL((IFNULL((SELECT chart_account_id FROM accounts WHERE product_id = ".$detail['product_id']." AND account_type_id=1),(SELECT chart_account_id FROM pgroup_accounts WHERE pgroup_id = (SELECT pgroup_id FROM product_pgroups WHERE product_id = ".$detail['product_id']." ORDER BY id  DESC LIMIT 1) AND account_type_id=1))),(SELECT chart_account_id FROM account_types WHERE id=1))");
                            $dataInvAccount = mysql_fetch_array($queryInvAccount);
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $dataInvAccount[0];
                            $generalLedgerDetail['GeneralLedgerDetail']['company_id']  = $sales_order['SalesOrder']['company_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['branch_id']   = $sales_order['SalesOrder']['branch_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['customer_id'] = $sales_order['SalesOrder']['customer_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['location_group_id'] = $sales_order['SalesOrder']['location_group_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['product_id']  = $detail['product_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_id']       = $inv_valutation_id;
                            $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_is_debit'] = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['type']   = 'POS';
                            $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $inventoryAsset;
                            $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['memo'] = 'ICS: Inventory for POS # ' . $salesOrderCode . ' ' . $dataProductCodeName[0];
                            $generalLedgerDetail['GeneralLedgerDetail']['class_id'] = $detail['class_id'];
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                            // General Ledger Detail (COGS)
                            $this->GeneralLedgerDetail->create();
                            $generalLedgerDetail = array();
                            $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $glId;
                            $queryCogsAccount = mysql_query("SELECT IFNULL((IFNULL((SELECT chart_account_id FROM accounts WHERE product_id = ".$detail['product_id']." AND account_type_id=2),(SELECT chart_account_id FROM pgroup_accounts WHERE pgroup_id = (SELECT pgroup_id FROM product_pgroups WHERE product_id = ".$detail['product_id']." ORDER BY id  DESC LIMIT 1) AND account_type_id=2))),(SELECT chart_account_id FROM account_types WHERE id=2))");
                            $dataCogsAccount = mysql_fetch_array($queryCogsAccount);
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $dataCogsAccount[0];
                            $generalLedgerDetail['GeneralLedgerDetail']['company_id']  = $sales_order['SalesOrder']['company_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['branch_id']   = $sales_order['SalesOrder']['branch_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['location_group_id'] = $sales_order['SalesOrder']['location_group_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['customer_id'] = $sales_order['SalesOrder']['customer_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['product_id']  = $detail['product_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_id'] = $inv_valutation_id;
                            $generalLedgerDetail['GeneralLedgerDetail']['inventory_valuation_is_debit'] = 1;
                            $generalLedgerDetail['GeneralLedgerDetail']['type']  = 'POS';
                            $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $cogs;
                            $generalLedgerDetail['GeneralLedgerDetail']['memo'] = 'ICS: COGS for POS # ' . $salesOrderCode . ' ' . $dataProductCodeName[0];
                            $generalLedgerDetail['GeneralLedgerDetail']['class_id'] = $detail['class_id'];
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                        } else if ($detail['service_id'] != '') { 
                            // Save Service
                            $this->SalesOrderService->create();
                            $salesOrderService = array();
                            $salesOrderService['SalesOrderService']['sales_order_id']   = $sales_order_id;
                            $salesOrderService['SalesOrderService']['service_id']       = $detail['service_id'];
                            $salesOrderService['SalesOrderService']['discount_id']      = $this->Helper->replaceThousand($detail['discount_id']);
                            $salesOrderService['SalesOrderService']['discount_amount']  = $this->Helper->replaceThousand($detail['discount_amount']);
                            $salesOrderService['SalesOrderService']['discount_percent'] = $this->Helper->replaceThousand($detail['discount_percent']);
                            $salesOrderService['SalesOrderService']['qty']              = $this->Helper->replaceThousand($detail['qty']);
                            $salesOrderService['SalesOrderService']['qty_free']         = $this->Helper->replaceThousand($detail['qty_free']);
                            $salesOrderService['SalesOrderService']['unit_price']       = $this->Helper->replaceThousand($detail['unit_price']);
                            $salesOrderService['SalesOrderService']['total_price']      = $this->Helper->replaceThousand($detail['total_price']);
                            $totalPriceSales += $this->Helper->replaceThousand($detail['total_price']) - $this->Helper->replaceThousand($detail['discount_amount']);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($salesOrderService['SalesOrderService'], 'sales_order_services');
                            $restCode[$r]['dbtodo']   = 'sales_order_services';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                            // General Ledger Detail (Service)
                            $this->GeneralLedgerDetail->create();
                            $generalLedgerDetail = array();
                            $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $glId;
                            $queryServiceAccount = mysql_query("SELECT IFNULL((SELECT chart_account_id FROM services WHERE id=" . $detail['service_id'] . "),(SELECT chart_account_id FROM account_types WHERE id=9))");
                            $dataServiceAccount = mysql_fetch_array($queryServiceAccount);
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $dataServiceAccount[0];
                            $generalLedgerDetail['GeneralLedgerDetail']['company_id']  = $sales_order['SalesOrder']['company_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['branch_id']   = $sales_order['SalesOrder']['branch_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['location_group_id'] = $sales_order['SalesOrder']['location_group_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['customer_id'] = $sales_order['SalesOrder']['customer_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['service_id'] = $detail['service_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['type'] = 'POS';
                            $generalLedgerDetail['GeneralLedgerDetail']['debit'] = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $this->Helper->replaceThousand($detail['total_price']);
                            $queryServiceCodeName = mysql_query("SELECT CONCAT(code,' - ',name) FROM services WHERE id=" . $detail['service_id']);
                            $dataServiceCodeName = mysql_fetch_array($queryServiceCodeName);
                            $generalLedgerDetail['GeneralLedgerDetail']['memo'] = 'ICS: POS # ' . $salesOrderCode . ' ' . $dataServiceCodeName[0];
                            $generalLedgerDetail['GeneralLedgerDetail']['class_id'] = $detail['class_id'];
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                            // General Ledger Detail Discount
                            if ($this->Helper->replaceThousand($detail['discount_amount']) > 0) {
                                $this->GeneralLedgerDetail->create();
                                $generalLedgerDetail = array();
                                $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $glId;
                                $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $salesDiscAccount['AccountType']['chart_account_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['company_id']  = $sales_order['SalesOrder']['company_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['branch_id']   = $sales_order['SalesOrder']['branch_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['location_group_id'] = $sales_order['SalesOrder']['location_group_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['customer_id'] = $sales_order['SalesOrder']['customer_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['service_id']  = $detail['service_id'];
                                $generalLedgerDetail['GeneralLedgerDetail']['type']    = 'POS';
                                $generalLedgerDetail['GeneralLedgerDetail']['debit']   = $this->Helper->replaceThousand($detail['discount_amount']);
                                $generalLedgerDetail['GeneralLedgerDetail']['credit']  = 0;
                                $generalLedgerDetail['GeneralLedgerDetail']['memo']    = 'ICS: POS # ' . $salesOrderCode . ' ' . $dataServiceCodeName[0] . ' Discount';
                                $generalLedgerDetail['GeneralLedgerDetail']['class_id'] = $detail['class_id'];
                                $this->GeneralLedgerDetail->save($generalLedgerDetail);
                                // Convert to REST
                                $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                                $restCode[$r]['dbtodo']   = 'general_ledger_details';
                                $restCode[$r]['actodo']   = 'is';
                                $r++;
                            }
                                
                            if (!$this->SalesOrderService->save($salesOrderService) || $return == false) {
                                $return = false;
                                break;
                            }                          
                        }
                    }
                    if ($return) {
                        $sales['SalesOrder']['total_amount'] = $totalPriceSales;
                        if (!$this->SalesOrder->save($sales)) {
                            echo "Error Update Sales Order!";
                        } else {
                            // Save File Send
                            $this->Helper->sendFileToSync($restCode, 0, 0);
                            shell_exec("rm -r public/pos/".$file);
                        }
                    } else {
                        echo "Error Save to sales order detail";
                    }
                }else{
                    echo "Error Find Content!";// End Check Content
                }
            }// End Check File
        } // End Check Sales Id
        exit;
    }
    
    function systemConfig(){
        $this->layout = 'system_config';
        if (!empty($this->data)) {
            // Update COGS Type
            if($this->data['uom_detail'] ==  1){
                $uomDetail = 0;
            }else{
                $uomDetail = 1;
            }
            mysql_query("UPDATE setting_options SET uom_detail_option = ".$uomDetail.", calculate_cogs =".$this->data['cogs_type']);
            // Location Setting
            mysql_query("UPDATE location_settings SET location_status =".$this->data['location_pb']." WHERE AND id = 1");
            mysql_query("UPDATE location_settings SET location_status =".$this->data['location_br']." WHERE AND id = 2");
            mysql_query("UPDATE location_settings SET location_status =".$this->data['location_pos']." WHERE AND id = 3");
            mysql_query("UPDATE location_settings SET location_status =".$this->data['location_sale']." WHERE AND id = 4");
            mysql_query("UPDATE location_settings SET location_status =".$this->data['location_cm']." WHERE AND id = 5");
            
            // Get System Info
            $array = array();
            $array['titleKh'] = $this->data['system_name_kh'];
            $array['title'] = $this->data['system_name'];
            $array['start'] = $this->data['system_start'];
            
            // Get Path
            $linkUrl    = $this->data['system_link_url'];
            $linkUrlSSL = $this->data['system_link_url_ssl'];
            
            // Upload Logo
            if ($_FILES['photo_big']['name'] != '') {
                $target_folder = 'img/';
                $ext = explode(".", $_FILES['photo_big']['name']);
                $target_name = 'logo.' . $ext[sizeof($ext) - 1];
                move_uploaded_file($_FILES['photo_big']['tmp_name'], $target_folder . $target_name);
            }
            if ($_FILES['photo_small']['name'] != '') {
                $target_folder = 'img/';
                $ext = explode(".", $_FILES['photo_small']['name']);
                $target_name = 'logo_s.' . $ext[sizeof($ext) - 1];
                move_uploaded_file($_FILES['photo_small']['tmp_name'], $target_folder . $target_name);
            }
            // Config WGET
            $path_to_file = 'path.php';
            $file_contents = file_get_contents($path_to_file);
            $file_contents = str_replace("WGETURL", $linkUrl, $file_contents);
            $file_contents = str_replace("WGETSSL", $linkUrlSSL, $file_contents);
            file_put_contents($path_to_file,$file_contents);
            
            // Create System Info
            $json = json_encode($array);
            $filename = "config/system_config.fg";
            $file = fopen($filename, "w");
            fwrite($file, $json);
            fclose($file);
            $this->redirect($this->getDefaultPage());
            exit;
        }
    }
    
    function createSysAct($mod, $act, $staus){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $bug = mysql_real_escape_string($_POST['bug']);
        $this->Helper->createSysActivity($mod, $act, $bug, $user['User']['id'], $staus);
        exit;
    }
    
    function sync(){
        $this->layout = 'ajax';
        $user = $_POST['user'];
        $pwd  = $_POST['pwd'];
        $project = $_POST['project'];
        $post    = "user=".$user."&pwd=".$pwd."&project=".$project;
        // CURL
        $curl  = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'http://localhost/Introduction/users/sync');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close ($curl);
        $data = json_decode($result, TRUE);
        debug($data);
        if($data['error'] == 0){
            $accessSave = true;
            $contents   = json_decode($data['contents'], TRUE);
            foreach($contents AS $content){
                $sysCOde   = $content['sys'];
                $sqlCheckR = mysql_query("SELECT id FROM module_receives WHERE syn_code = '".$sysCOde."' LIMIT 1");
                if(!mysql_num_rows($sqlCheckR)){
                    // Convert to Original And Excecute SQL Comment
                    $dataRecords = json_decode($content['txt'], TRUE);
                    $sqlDeploy   = '';
                    foreach($dataRecords AS $datas){
                        $fields       = array();
                        $condition    = '';
                        $dbName       = '';
                        $convertToSQL = 0;
                        $sqlCmt  = '';
                        foreach($datas AS $key => $val){
                            $key = $key;
                            if($key == 'actodo' && $val == 'is'){
                                $convertToSQL = 1;
                            } else if($key == 'actodo' && $val == 'ut'){
                                $convertToSQL = 2;
                            } else if($key == 'actodo' && $val == 'dt'){
                                $convertToSQL = 3;
                            }
                            if($key == 'con' && ($convertToSQL == 2 || $convertToSQL == 3)){
                                $condition = html_entity_decode($val, ENT_QUOTES);
                            }
                            if($key == 'dbtodo'){
                                $dbName = $val;
                            }
                            if($key != 'dbtodo' && $key != 'actodo' && $key != 'con'){
                                $sqlVal = html_entity_decode($val, ENT_QUOTES);
                                $fields[$key] = $sqlVal;
                            }
                        }
                        if($convertToSQL == 1){
                            $sqlCmt = $this->Helper->generateSqlInsertSync($dbName, $fields);
                        } else if($convertToSQL == 2){
                            $sqlCmt = $this->Helper->generateSqlUpdateSync($dbName, $fields, $condition, "");
                        } else if($convertToSQL == 3){
                            $sqlCmt = $this->Helper->generateSqlDeleteSync($dbName, $condition);
                        }
                        if($sqlCmt != ''){
                            $sqlDeploy .= $sqlCmt;
                            $result = mysql_query($sqlCmt);
                        } else {
                            $accessSave = false;
                        }
                    }
                    if($accessSave == true){
                        $serverPath = str_replace("app\webroot", 'crontab\SQL-Update', getcwd());
                        $filename   = rand()."-".date("d-m-Y").".txt";
                        if($sqlDeploy != ''){
                            $fp = fopen($serverPath."\sync".$filename, "wb");
                            fwrite($fp, $sqlDeploy);
                            fclose($fp);
                        }
                        // INSERT Receive
                        mysql_query("INSERT INTO `module_receives` (`syn_code`, `created`) VALUES ('".$sysCOde."', '".date("Y-m-d H:i:s")."');");
                    }
                }
            }
        }
        exit;
    }
    
     function upload() {
        $this->layout = 'ajax';
        if ($_FILES['photo']['name'] != '') {
            $target_folder = 'public/signature_photo/tmp/';
            $ext = explode(".", $_FILES['photo']['name']);
            $target_name = rand() . '.' . $ext[sizeof($ext) - 1];
            move_uploaded_file($_FILES['photo']['tmp_name'], $target_folder . $target_name);
            if (isset($_SESSION['signature_photo']) && $_SESSION['signature_photo'] != '') {
                @unlink($target_folder . $_SESSION['signature_photo']);
            }
            echo $_SESSION['signature_photo'] = $target_name;
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
            $target_folder = 'public/signature_photo/tmp/';
            $target_thumbnail = 'public/signature_photo/tmp/thumbnail/';
            $ext = explode(".", $_POST['photoName']);
            $target_name = rand() . '.' . $ext[sizeof($ext) - 1];
            Resize($_POST['photoFolder'], $_POST['photoName'], $target_folder, $target_name, $_POST['w'], $_POST['h'], 100, true);
            Resize($_POST['photoFolder'], $_POST['photoName'], $target_thumbnail, $target_name, 300, 300, 100, true);
            @unlink($target_folder . $_POST['photoName']);
        }
        echo $target_name;
        exit();
    }
    
    function clearSession($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }else{
            $this->User->updateAll( 	 	
                    array('User.session_id' => NULL, 'User.session_active' => NULL, 'User.session_start' => NULL, 'User.login_attempt_remote_ip' => NULL, 'User.login_attempt_http_user_agent' => NULL), array('User.id' => $id)
            );
            echo MESSAGE_DATA_HAS_BEEN_SAVED;
            exit;
        }
    }
    
}

?>