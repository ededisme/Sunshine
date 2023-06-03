<?php

class PgroupsController extends AppController {

    var $name = 'Pgroups';
    var $components = array('Helper');

    function index() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $this->Helper->saveUserActivity($user['User']['id'], 'Product Group', 'Dashboard');
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
        $joins = array(
            array('table' => 'products',
                'type' => 'INNER',
                'alias' => 'Product',
                'conditions' => array('ProductPgroup.product_id = Product.id')));
        $products = ClassRegistry::init('ProductPgroup')->find('all', array(
                    'fields' => array('Product.*'),
                    'conditions' => array('ProductPgroup.pgroup_id' => $id, 'Product.is_active' => 1),
                    'joins' => $joins
                    , 'order' => array('Product.name, Product.code DESC')));
        $users = ClassRegistry::init('UserPgroup')->find('all', array(
                    'fields' => array('User.*'),
                    'conditions' => array('UserPgroup.pgroup_id' => $id, 'User.is_active' => 1),
                    'order' => array('User.first_name DESC')));
        $user = $this->getCurrentUser();
        $this->Helper->saveUserActivity($user['User']['id'], 'Product Group', 'View', $id);
        $this->set('pgroup', $this->Pgroup->read(null, $id));
        $this->set(compact('products', 'users'));
    }

    function add() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $comCheck = $this->data['Pgroup']['company_id'];
            if ($this->Helper->checkDouplicate('name', 'pgroups', $this->data['Pgroup']['name'], 'is_active = 1 AND id IN (SELECT pgroup_id FROM pgroup_companies WHERE company_id IN ('.$comCheck.'))')) {
                $this->Helper->saveUserActivity($user['User']['id'], 'Product Group', 'Save Add New (Name ready existed)');
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $r = 0;
                $e = 0;
                $syncEco   = array();
                $restCode  = array();
                $dateNow   = date("Y-m-d H:i:s");
                $this->Pgroup->create();
                $this->data['Pgroup']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $this->data['Pgroup']['created']    = $dateNow;
                $this->data['Pgroup']['created_by'] = $user['User']['id'];
                $this->data['Pgroup']['is_active']  = 1;
                if ($this->Pgroup->save($this->data)) {
                    $lastInsertId=$this->Pgroup->getLastInsertId();

					$insertSales = mysql_query("INSERT INTO ".DB_SS_MONY_KID."pgroups (sys_code, parent_id, name, created, created_by, modified, modified_by, is_active) 
                                            SELECT sys_code, parent_id, name, created, created_by, modified, modified_by, is_active FROM services WHERE id = " . $lastInsertId . ";");
                	$pgroupSsId = mysql_insert_id();

                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($this->data['Pgroup'], 'pgroups');
                    $restCode[$r]['modified']   = $dateNow;
                    $restCode[$r]['dbtodo']     = 'pgroups';
                    $restCode[$r]['actodo']     = 'is';
                    $r++;
                    // Pgroup Company
                    if (!empty($this->data['Pgroup']['company_id'])) {
                        mysql_query("INSERT INTO pgroup_companies (pgroup_id, company_id) VALUES ('" . $lastInsertId . "','" . $this->data['Pgroup']['company_id'] . "')");
                        
						// Secondary
							mysql_query("INSERT INTO ".DB_SS_MONY_KID."pgroup_companies (pgroup_id,company_id) VALUES ('" . $pgroupSsId . "','" . $this->data['Pgroup']['company_id'] . "')");
						// Convert to REST
                        $restCode[$r]['pgroup_id']  = $this->data['Pgroup']['sys_code'];
                        $restCode[$r]['company_id'] = $this->Helper->getSQLSysCode("companies", $this->data['Pgroup']['company_id']);
                        $restCode[$r]['dbtodo']     = 'pgroup_companies';
                        $restCode[$r]['actodo']     = 'is';
                        $r++;
                    }
                    // Pgroup User
                    if (!empty($this->data['Pgroup']['user_id'])) {
                        for ($i = 0; $i < sizeof($this->data['Pgroup']['user_id']); $i++) {
                            mysql_query("INSERT INTO user_pgroups (pgroup_id, user_id) VALUES ('" . $lastInsertId . "','" . $this->data['Pgroup']['user_id'][$i] . "')");
                            
							// Secondary
							mysql_query("INSERT INTO ".DB_SS_MONY_KID."user_pgroups (pgroup_id,user_id) VALUES ('" . $pgroupSsId . "','" . $this->data['Pgroup']['user_id'][$i] . "')");

							// Convert to REST
                            $restCode[$r]['pgroup_id']  = $this->data['Pgroup']['sys_code'];
                            $restCode[$r]['user_id']    = $this->Helper->getSQLSysCode("users", $this->data['Pgroup']['user_id'][$i]);
                            $restCode[$r]['dbtodo']     = 'user_pgroups';
                            $restCode[$r]['actodo']     = 'is';
                            $r++;
                        }
                    }
                    // Send to E-Commerce
                    // Convert to REST
                    $syncEco[$e]['sys_code']  = $this->data['Pgroup']['sys_code'];
                    $syncEco[$e]['name']      = $this->data['Pgroup']['name'];
                    $syncEco[$e]['status']    = 2;
                    $syncEco[$e]['created']   = $dateNow;
                    $syncEco[$e]['dbtodo']    = 'pgroups';
                    $syncEco[$e]['actodo']    = 'is';
                    $e++;
                    // product group
                    if(!empty($this->data['Pgroup']['product_id'])){
                        for($i=0;$i<sizeof($this->data['Pgroup']['product_id']);$i++){
                            mysql_query("INSERT INTO product_pgroups (product_id,pgroup_id) VALUES ('".$this->data['Pgroup']['product_id'][$i]."','".$lastInsertId."')");
                            // Convert to REST
                            $restCode[$r]['pgroup_id']  = $this->data['Pgroup']['sys_code'];
                            $restCode[$r]['product_id'] = $this->Helper->getSQLSysCode("products", $this->data['Pgroup']['product_id'][$i]);
                            $restCode[$r]['dbtodo']     = 'product_pgroups';
                            $restCode[$r]['actodo']     = 'is';
                            $r++;
                            // Send to E-Commerce
                            // Convert to REST
                            $syncEco[$e]['pgroup_id']  = $this->data['Pgroup']['sys_code'];
                            $syncEco[$e]['product_id'] = $this->Helper->getSQLSysCode("products", $this->data['Pgroup']['product_id'][$i]);
                            $syncEco[$e]['dbtodo']     = 'product_pgroups';
                            $syncEco[$e]['actodo']     = 'is';
                            $e++;
                        }
                    }
                    // Save File Send
                    $this->Helper->sendFileToSync($restCode, 0, 0);
                    // Save File Send to E-Commerce
                    $this->Helper->sendFileToSyncPublic($syncEco);
                    // Save User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'Product Group', 'Save Add New', $lastInsertId);
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'Product Group', 'Save Add New (Error)');
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        $this->Helper->saveUserActivity($user['User']['id'], 'Product Group', 'Add New');
        $companies = ClassRegistry::init('Company')->find('list', array('order' => 'id', 'conditions' => array('is_active' => 1, 'id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')')));
        $this->set(compact("companies"));
    }

    function edit($id = null) {
        $this->layout = 'ajax';
        if (!$id && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $comCheck = $this->data['Pgroup']['company_id'];
            if ($this->Helper->checkDouplicateEdit('name', 'pgroups', $id, $this->data['Pgroup']['name'], 'is_active = 1 AND id IN (SELECT pgroup_id FROM pgroup_companies WHERE company_id IN ('.$comCheck.'))')) {
                $this->Helper->saveUserActivity($user['User']['id'], 'Product Group', 'Save Edit (Name ready existed)', $id);
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $r = 0;
                $restCode  = array();
                $dateNow   = date("Y-m-d H:i:s");
                $this->data['Pgroup']['modified']    = $dateNow;
                $this->data['Pgroup']['modified_by'] = $user['User']['id'];
                if ($this->Pgroup->save($this->data)) {

					//Update Service Secondary		
					$insertSales = mysql_query("UPDATE ".DB_SS_MONY_KID."pgroups SET name = '".$this->data['Pgroup']['name']."', parent_id = '".$this->data['Pgroup']['section_id']."', modified = '".$dateNow."', modified_by = '".$user['User']['id']."' WHERE sys_code ='".$this->data['Pgroup']['sys_code']."'");

                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($this->data['Pgroup'], 'pgroups');
                    $restCode[$r]['dbtodo'] = 'pgroups';
                    $restCode[$r]['actodo'] = 'ut';
                    $restCode[$r]['con']    = "sys_code = '".$this->data['Pgroup']['sys_code']."'";
                    $r++;
                    $sqlCheckPgroupUse = mysql_query("SELECT id FROM inventories WHERE product_id IN (SELECT product_id FROM product_pgroups WHERE pgroup_id = ".$id.") LIMIT 1"); 
                    if(!mysql_num_rows($sqlCheckPgroupUse)){
                        // Pgroup Company
                        mysql_query("DELETE FROM pgroup_companies WHERE pgroup_id=" . $id);

						// SS Company
                        mysql_query("DELETE FROM ".DB_SS_MONY_KID."pgroup_companies WHERE pgroup_id IN(SELECT id ".DB_SS_MONY_KID."pgroup WHERE is_active = 1 AND sys_code='".$this->data['Pgroup']['sys_code']."')");
                        // Convert to REST
                        $restCode[$r]['dbtodo'] = 'pgroup_companies';
                        $restCode[$r]['actodo'] = 'dt';
                        $restCode[$r]['con']    = "pgroup_id = ".$this->data['Pgroup']['sys_code'];
                        $r++;
                        if (!empty($this->data['Pgroup']['company_id'])) {
                            mysql_query("INSERT INTO pgroup_companies (pgroup_id, company_id) VALUES ('" . $id . "','" . $this->data['Pgroup']['company_id']. "')");
                            
							// Secondary
							$pgroupSsId = "(SELECT id ".DB_SS_MONY_KID."pgroup WHERE is_active = 1 AND sys_code='".$this->data['Pgroup']['sys_code']."')";
							mysql_query("INSERT INTO ".DB_SS_MONY_KID."pgroup_companies (pgroup_id,company_id) VALUES (" . $pgroupSsId . ",'" . $this->data['Pgroup']['company_id'][$i] . "')");
							
							// Convert to REST
                            $restCode[$r]['pgroup_id']  = $this->data['Pgroup']['sys_code'];
                            $restCode[$r]['company_id'] = $this->Helper->getSQLSysCode("companies", $this->data['Pgroup']['company_id']);
                            $restCode[$r]['dbtodo']     = 'pgroup_companies';
                            $restCode[$r]['actodo']     = 'is';
                            $r++;
                        }
                    }

					// SS Company
                    mysql_query("DELETE FROM ".DB_SS_MONY_KID."user_pgroups WHERE pgroup_id IN(SELECT id ".DB_SS_MONY_KID."pgroup WHERE is_active = 1 AND sys_code='".$this->data['Pgroup']['sys_code']."')");
                    // Pgroup User
                    mysql_query("DELETE FROM user_pgroups WHERE pgroup_id=" . $id);
                    // Convert to REST
                    $restCode[$r]['dbtodo'] = 'user_pgroups';
                    $restCode[$r]['actodo'] = 'dt';
                    $restCode[$r]['con']    = "pgroup_id = ".$this->data['Pgroup']['sys_code'];
                    $r++;
                    if (!empty($this->data['Pgroup']['user_id'])) {
                        for ($i = 0; $i < sizeof($this->data['Pgroup']['user_id']); $i++) {
                            mysql_query("INSERT INTO user_pgroups (pgroup_id, user_id) VALUES ('" . $id . "','" . $this->data['Pgroup']['user_id'][$i] . "')");
                            
							// Secondary
							$pgroupSsId = "(SELECT id ".DB_SS_MONY_KID."pgroup WHERE is_active = 1 AND sys_code='".$this->data['Pgroup']['sys_code']."')";
							mysql_query("INSERT INTO ".DB_SS_MONY_KID."user_pgroups (pgroup_id,user_id) VALUES (" . $pgroupSsId . ",'" . $this->data['Pgroup']['user_id'][$i] . "')");
							// Convert to REST
                            $restCode[$r]['pgroup_id'] = $this->data['Pgroup']['sys_code'];
                            $restCode[$r]['user_id']   = $this->Helper->getSQLSysCode("users", $this->data['Pgroup']['user_id'][$i]);
                            $restCode[$r]['dbtodo']    = 'user_pgroups';
                            $restCode[$r]['actodo']    = 'is';
                            $r++;
                        }
                    }
                    // Send to E-Commerce
                    $e = 0;
                    $syncEco = array();
                    // Convert to REST
                    $syncEco[$e]['name']     = $this->data['Pgroup']['name'];
                    $syncEco[$e]['modified'] = $dateNow;
                    $syncEco[$e]['dbtodo']   = 'pgroups';
                    $syncEco[$e]['actodo']   = 'ut';
                    $syncEco[$e]['con']      = "sys_code = '".$this->data['Pgroup']['sys_code']."'";
                    // user group
                    mysql_query("DELETE FROM product_pgroups WHERE pgroup_id=".$id);
                    // Convert to REST
                    $restCode[$r]['dbtodo'] = 'product_pgroups';
                    $restCode[$r]['actodo'] = 'dt';
                    $restCode[$r]['con']    = "pgroup_id = ".$this->data['Pgroup']['sys_code'];
                    $r++;
                    // Convert to REST E-Commerce
                    $syncEco[$e]['dbtodo'] = 'product_pgroups';
                    $syncEco[$e]['actodo'] = 'dt';
                    $syncEco[$e]['con']    = "pgroup_id = ".$this->data['Pgroup']['sys_code'];
                    $e++;
                    if(!empty($this->data['Pgroup']['product_id'])){
                        for($i=0;$i<sizeof($this->data['Pgroup']['product_id']);$i++){
                            mysql_query("INSERT INTO product_pgroups (product_id,pgroup_id) VALUES ('".$this->data['Pgroup']['product_id'][$i]."','".$id."')");
                            // Convert to REST
                            $restCode[$r]['pgroup_id']  = $this->data['Pgroup']['sys_code'];
                            $restCode[$r]['product_id'] = $this->Helper->getSQLSysCode("products", $this->data['Pgroup']['product_id'][$i]);
                            $restCode[$r]['dbtodo']     = 'product_pgroups';
                            $restCode[$r]['actodo']     = 'is';
                            $r++;
                            // Convert to REST E-Commerce
                            $syncEco[$e]['pgroup_id']  = $this->data['Pgroup']['sys_code'];
                            $syncEco[$e]['product_id'] = $this->Helper->getSQLSysCode("products", $this->data['Pgroup']['product_id'][$i]);
                            $syncEco[$e]['dbtodo']     = 'product_pgroups';
                            $syncEco[$e]['actodo']     = 'is';
                            $e++;
                        }
                    }
                    // Save File Send
                    $this->Helper->sendFileToSync($restCode, 0, 0);
                    // Save File Send to E-Commerce
                    $this->Helper->sendFileToSyncPublic($syncEco);
                    // Save User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'Product Group', 'Save Edit', $id);
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'Product Group', 'Save Edit (Error)', $id);
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        if (empty($this->data)) {
            $this->Helper->saveUserActivity($user['User']['id'], 'Product Group', 'Edit', $id);
            $this->data = $this->Pgroup->read(null, $id);
            $companySellecteds = ClassRegistry::init('PgroupCompany')->find('list', array('fields' => array('id', 'company_id'), 'order' => 'id', 'conditions' => array('pgroup_id' => $id)));
            $companySellected = array();
            foreach ($companySellecteds as $cs) {
                array_push($companySellected, $cs);
            }
            $companies = ClassRegistry::init('Company')->find('list', array('order' => 'id', 'conditions' => array('is_active' => 1, 'id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')')));
            $this->set(compact('companies', 'companySellected'));
        }
    }

    function delete($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        $sqlCheckWithProduct = mysql_query("SELECT id FROM product_pgroups WHERE pgroup_id = ".$id." LIMIT 1");
        if(mysql_num_rows($sqlCheckWithProduct)){
            $this->Helper->saveUserActivity($user['User']['id'], 'Product Group', 'Delete '.$this->data['Product']['code'].' Error Have Child Product');
            echo MESSAGE_DATA_HAVE_CHILD;
            exit;
        }
        $r = 0;
        $restCode = array();
        $dateNow  = date("Y-m-d H:i:s");
        $this->data = $this->Pgroup->read(null, $id);
        mysql_query("UPDATE `pgroups` SET `is_active`=2, `modified`='".$dateNow."', `modified_by`=".$user['User']['id']." WHERE `id`=".$id.";");
        // Convert to REST
        $restCode[$r]['is_active']   = 2;
        $restCode[$r]['modified']    = $dateNow;
        $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
        $restCode[$r]['dbtodo'] = 'pgroups';
        $restCode[$r]['actodo'] = 'ut';
        $restCode[$r]['con']    = "sys_code = '".$this->data['Pgroup']['sys_code']."'";
        // Save File Send
        $this->Helper->sendFileToSync($restCode, 0, 0);
        // Send to E-Commerce
        $e = 0;
        $syncEco = array();
        // Convert to REST
        $syncEco[$e]['status']   = 0;
        $syncEco[$e]['modified'] = $dateNow;
        $syncEco[$e]['dbtodo']   = 'pgroups';
        $syncEco[$e]['actodo']   = 'ut';
        $syncEco[$e]['con']      = "sys_code = '".$this->data['Pgroup']['sys_code']."'";
        // Save File Send to E-Commerce
        $this->Helper->sendFileToSyncPublic($syncEco);
        // Save User Activity
        $this->Helper->saveUserActivity($user['User']['id'], 'Product Group', 'Delete', $id);
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }


    function searchProduct() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $userPermission = 'Product.company_id IN (SELECT company_id FROM user_companies WHERE user_id ='.$user['User']['id'].')';
        $products = ClassRegistry::init('Product')->find('all', array(
                    'conditions' => array('OR' => array(
                            'Product.name LIKE' => '%' . $this->params['url']['q'] . '%',
                            'Product.code LIKE ' => '%' . $this->params['url']['q'] . '%',), 'Product.is_active' => 1, $userPermission)));
        $this->set(compact('products'));
    }

    function product($companyId = null) {
        $this->layout = 'ajax';
        $this->set(compact('companyId'));
    }

    function productAjax($companyId = null) {
        $this->layout = 'ajax';
        $this->set(compact('companyId'));
    }
    
    function exportExcel(){
        $this->layout = 'ajax';
        if (isset($_POST['action']) && $_POST['action'] == 'export') {
            $user = $this->getCurrentUser();
            $this->Helper->saveUserActivity($user['User']['id'], 'Product Group', 'Export to Excel');
            $filename = "public/report/product_group_export.csv";
            $fp = fopen($filename, "wb");
            $excelContent = 'Product Groups' . "\n\n";
            $excelContent .= TABLE_NO . "\t" . TABLE_PARENT. "\t" . TABLE_NAME;
            $conditionUser = " AND id IN (SELECT pgroup_id FROM pgroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))";
            $query = mysql_query('SELECT id, (SELECT pg.name FROM pgroups AS pg WHERE pg.id = pgroups.parent_id), name '
                    . '           FROM pgroups WHERE is_active=1'.$conditionUser.' ORDER BY name');
            $index = 1;
            while ($data = mysql_fetch_array($query)) {
                $excelContent .= "\n" . $index++ . "\t" . $data[1]. "\t" . $data[2];
            }
            $excelContent = chr(255) . chr(254) . @mb_convert_encoding($excelContent, 'UTF-16LE', 'UTF-8');
            fwrite($fp, $excelContent);
            fclose($fp);
            exit();
        }
    }
}

?>