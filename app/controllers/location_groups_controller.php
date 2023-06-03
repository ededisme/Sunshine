<?php

class LocationGroupsController extends AppController {

    var $name = 'LocationGroups';
    var $components = array('Helper');

    function index() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $this->Helper->saveUserActivity($user['User']['id'], 'Warehouse', 'Dashboard');
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
        $this->Helper->saveUserActivity($user['User']['id'], 'Warehouse', 'View', $id);
        $this->set('locationGroup', $this->LocationGroup->read(null, $id));
    }
    
    function viewProductWarehouse($locationGroupId = null) {
        $this->layout = 'ajax';
        if (!$locationGroupId) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        $this->Helper->saveUserActivity($user['User']['id'], 'Location', 'View Product Warehouse', $locationGroupId);
        $this->set(compact("locationGroupId"));
    }
    
    function viewProductWarehouseAjax($locationGroupId = null, $category = null) {
        $this->layout = 'ajax';
        if (!$locationGroupId) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $this->set(compact("locationGroupId", "category"));
    }

    function add() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            if ($this->Helper->checkDouplicate('name', 'location_groups', $this->data['LocationGroup']['name'])) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                Configure::write('debug', 0);
                $r = 0;
                $restCode  = array();
                $dateNow   = date("Y-m-d H:i:s");
                $this->LocationGroup->create();
                $this->data['LocationGroup']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $this->data['LocationGroup']['created']    = $dateNow;
                $this->data['LocationGroup']['created_by'] = $user['User']['id'];
                $this->data['LocationGroup']['is_active']  = 1;
                if ($this->LocationGroup->save($this->data)) {
                    $error = mysql_error();
                    if($error != 'Invalid Data'){
                        $lastInsertId = $this->LocationGroup->id;
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($this->data['LocationGroup'], 'location_groups');
                        $restCode[$r]['modified']   = $dateNow;
                        $restCode[$r]['dbtodo']     = 'location_groups';
                        $restCode[$r]['actodo']     = 'is';
                        $r++;
                        // Create Table For Store Total
                        mysql_query("CREATE TABLE `".$lastInsertId."_group_totals` (
                                            `product_id` INT(11) NOT NULL DEFAULT '0',
                                            `lots_number` VARCHAR(50) NOT NULL DEFAULT '0' COLLATE 'utf8_unicode_ci',
                                            `expired_date` DATE NOT NULL,
                                            `location_id` INT(11) NOT NULL DEFAULT '0',
                                            `location_group_id` INT(11) NOT NULL DEFAULT '0',
                                            `total_qty` DECIMAL(15,3) NULL DEFAULT '0',
                                            `total_order` DECIMAL(15,3) NULL DEFAULT '0',
                                            PRIMARY KEY (`product_id`, `location_id`, `location_group_id`, `lots_number`, `expired_date`),
                                            INDEX `index_keys` (`product_id`, `location_id`, `location_group_id`, `lots_number`, `expired_date`)
                                    )
                                    COLLATE='utf8_unicode_ci'
                                    ENGINE=InnoDB;");
                        // Convert to REST
                        $restCode[$r]['actcont'] = "CREATE TABLE `".$lastInsertId."_group_totals` (`product_id` INT(11) NOT NULL DEFAULT '0',`lots_number` VARCHAR(50) NOT NULL DEFAULT '0' COLLATE 'utf8_unicode_ci',`expired_date` DATE NOT NULL,`location_id` INT(11) NOT NULL DEFAULT '0',`location_group_id` INT(11) NOT NULL DEFAULT '0',`total_qty` DECIMAL(15,3) NULL DEFAULT '0',`total_order` DECIMAL(15,3) NULL DEFAULT '0',PRIMARY KEY (`product_id`, `location_id`, `location_group_id`, `lots_number`, `expired_date`),INDEX `index_keys` (`product_id`, `location_id`, `location_group_id`, `lots_number`, `expired_date`))COLLATE='utf8_unicode_ci' ENGINE=InnoDB;";
                        $restCode[$r]['actodo']  = 'sqr';
                        $r++;
                        // Create Table For Store Total Detail
                        mysql_query("CREATE TABLE `".$lastInsertId."_group_total_details` (
                                            `product_id` INT(11) NOT NULL DEFAULT '0',
                                            `location_group_id` INT(11) NOT NULL DEFAULT '0',
                                            `total_cycle` DECIMAL(15,3) NULL DEFAULT '0',
                                            `total_so` DECIMAL(15,3) NULL DEFAULT '0',
                                            `total_so_free` DECIMAL(15,3) NULL DEFAULT '0',
                                            `total_pos` DECIMAL(15,3) NULL DEFAULT '0',
                                            `total_pos_free` DECIMAL(15,3) NULL DEFAULT '0',
                                            `total_pb` DECIMAL(15,3) NULL DEFAULT '0',
                                            `total_pbc` DECIMAL(15,3) NULL DEFAULT '0',
                                            `total_cm` DECIMAL(15,3) NULL DEFAULT '0',
                                            `total_cm_free` DECIMAL(15,3) NULL DEFAULT '0',
                                            `total_to_in` DECIMAL(15,3) NULL DEFAULT '0',
                                            `total_to_out` DECIMAL(15,3) NULL DEFAULT '0',
                                            `total_cus_consign_in` DECIMAL(15,3) NULL DEFAULT '0',
                                            `total_cus_consign_out` DECIMAL(15,3) NULL DEFAULT '0',
                                            `total_ven_consign_in` DECIMAL(15,3) NULL DEFAULT '0',
                                            `total_ven_consign_out` DECIMAL(15,3) NULL DEFAULT '0',
                                            `total_order` DECIMAL(15,3) NULL DEFAULT '0',
                                            `date` DATE NOT NULL,
                                            PRIMARY KEY (`product_id`, `location_group_id`, `date`),
                                            INDEX `index_key` (`product_id`, `location_group_id`, `date`)
                                    )
                                    COLLATE='utf8_unicode_ci'
                                    ENGINE=InnoDB;");
                        // Convert to REST
                        $restCode[$r]['actcont'] = "CREATE TABLE `".$lastInsertId."_group_total_details` (`product_id` INT(11) NOT NULL DEFAULT '0',`location_group_id` INT(11) NOT NULL DEFAULT '0',`total_cycle` DECIMAL(15,3) NULL DEFAULT '0',`total_so` DECIMAL(15,3) NULL DEFAULT '0',`total_so_free` DECIMAL(15,3) NULL DEFAULT '0',`total_pos` DECIMAL(15,3) NULL DEFAULT '0',`total_pos_free` DECIMAL(15,3) NULL DEFAULT '0',`total_pb` DECIMAL(15,3) NULL DEFAULT '0',`total_pbc` DECIMAL(15,3) NULL DEFAULT '0',`total_cm` DECIMAL(15,3) NULL DEFAULT '0',`total_cm_free` DECIMAL(15,3) NULL DEFAULT '0',`total_to_in` DECIMAL(15,3) NULL DEFAULT '0',`total_to_out` DECIMAL(15,3) NULL DEFAULT '0',`total_cus_consign_in` DECIMAL(15,3) NULL DEFAULT '0',`total_cus_consign_out` DECIMAL(15,3) NULL DEFAULT '0',`total_ven_consign_in` DECIMAL(15,3) NULL DEFAULT '0',`total_ven_consign_out` DECIMAL(15,3) NULL DEFAULT '0',`total_order` DECIMAL(15,3) NULL DEFAULT '0',`date` DATE NOT NULL,PRIMARY KEY (`product_id`, `location_group_id`, `date`),INDEX `index_key` (`product_id`, `location_group_id`, `date`))COLLATE='utf8_unicode_ci' ENGINE=InnoDB;";
                        $restCode[$r]['actodo']  = 'sqr';
                        $r++;
                        // User Location Group
                        if(isset($this->data['LocationGroup']['user_id'])){
                            for($i=0;$i<sizeof($this->data['LocationGroup']['user_id']);$i++){
                                mysql_query("INSERT INTO user_location_groups (user_id, location_group_id) VALUES ('".$this->data['LocationGroup']['user_id'][$i]."','".$lastInsertId."')");
                                // Convert to REST
                                $restCode[$r]['location_group_id'] = $this->data['LocationGroup']['sys_code'];
                                $restCode[$r]['user_id']   = $this->Helper->getSQLSysCode("users", $this->data['LocationGroup']['user_id'][$i]);
                                $restCode[$r]['dbtodo']    = 'user_location_groups';
                                $restCode[$r]['actodo']    = 'is';
                                $r++;
                            }
                        }
                        // User Location Group Class with Company
                        if(!empty($this->data['LocationGroup']['company_id']) && !empty($this->data['LocationGroup']['class_id'])){
                            for($i=0;$i<sizeof($this->data['LocationGroup']['company_id']);$i++){
                                mysql_query("INSERT INTO location_group_classese VALUES (".$this->data['LocationGroup']['company_id'][$i].", ".$lastInsertId.", ".$this->data['LocationGroup']['class_id'][$i].") ON DUPLICATE KEY UPDATE class_id='".$this->data['LocationGroup']['class_id'][$i]."';");
                                // Convert to REST
                                $restCode[$r]['location_group_id'] = $this->data['LocationGroup']['sys_code'];
                                $restCode[$r]['company_id'] = $this->Helper->getSQLSysCode("companies", $this->data['LocationGroup']['company_id'][$i]);
                                $restCode[$r]['class_id']   = 0;
                                $restCode[$r]['dbtodo']     = 'location_group_classese';
                                $restCode[$r]['actodo']     = 'is';
                                $r++;
                                $classArray = array();
                                $sqlLocGroup = mysql_query("SELECT * FROM location_group_classese WHERE company_id = ".$this->data['LocationGroup']['company_id'][$i]);
                                while($rowLocGroup = mysql_fetch_array($sqlLocGroup)){
                                    $locationGroupId = $rowLocGroup['location_group_id'];
                                    $classArray[$this->data['LocationGroup']['company_id'][$i]][$locationGroupId] = $rowLocGroup['class_id'];
                                }
                                $fileClass = serialize($classArray);
                                mysql_query("UPDATE companies SET classes = '{$fileClass}' WHERE id = ".$this->data['LocationGroup']['company_id'][$i]);
                                // Convert to REST
                                $restCode[$r]['classes'] = $fileClass;
                                $restCode[$r]['dbtodo']  = 'companies';
                                $restCode[$r]['actodo']  = 'ut';
                                $restCode[$r]['con']     = "sys_code = '".$this->Helper->getSQLSysCode("companies", $this->data['LocationGroup']['company_id'][$i])."'";
                                $r++;
                            }
                        }
                        // Save File Send
                        $this->Helper->sendFileToSync($restCode, 0, 0);
                        // Save User Activity
                        $this->Helper->saveUserActivity($user['User']['id'], 'Warehouse', 'Save Add New', $lastInsertId);
                        echo MESSAGE_DATA_HAS_BEEN_SAVED;
                        exit;
                    } else {
                        $this->Helper->saveUserActivity($user['User']['id'], 'Warehouse', 'Save Add New (Error '.$error.')');
                        echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                        exit;
                    }
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'Warehouse', 'Save Add New (Error)');
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        $this->Helper->saveUserActivity($user['User']['id'], 'Warehouse', 'Add New');
        $locationGroupTypes = ClassRegistry::init('LocationGroupType')->find("list", array("conditions" => array("LocationGroupType.is_active = 1", "LocationGroupType.id != 1")));
        $companies = ClassRegistry::init('Company')->find("all", array("conditions" => array("Company.is_active = 1", "Company.id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id'].")")));
        $this->set(compact("companies", "locationGroupTypes"));
    }

    function edit($id = null) {
        $this->layout = 'ajax';
        if (!$id && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            if ($this->Helper->checkDouplicateEdit('name', 'location_groups', $id, $this->data['LocationGroup']['name'])) {
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                Configure::write('debug', 0);
                $r = 0;
                $restCode = array();
                $dateNow  = date("Y-m-d H:i:s");
                $this->data['LocationGroup']['modified']    = $dateNow;
                $this->data['LocationGroup']['modified_by'] = $user['User']['id'];
                if ($this->LocationGroup->save($this->data)) {
                    $error = mysql_error();
                    if($error != 'Data cloud not been delete' && $error != 'Invalid Data'){
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($this->data['LocationGroup'], 'location_groups');
                        $restCode[$r]['dbtodo'] = 'location_groups';
                        $restCode[$r]['actodo'] = 'ut';
                        $restCode[$r]['con']    = "sys_code = '".$this->data['LocationGroup']['sys_code']."'";
                        $r++;
                        // User Location
                        mysql_query("DELETE FROM user_location_groups WHERE location_group_id=".$id);
                        // Convert to REST
                        $restCode[$r]['dbtodo'] = 'user_location_groups';
                        $restCode[$r]['actodo'] = 'dt';
                        $restCode[$r]['con']    = "location_group_id = ".$this->data['LocationGroup']['sys_code'];
                        $r++;
                        if(isset($this->data['LocationGroup']['user_id'])){
                            for($i=0;$i<sizeof($this->data['LocationGroup']['user_id']);$i++){
                                mysql_query("INSERT INTO user_location_groups (user_id, location_group_id) VALUES ('".$this->data['LocationGroup']['user_id'][$i]."','".$id."')");
                                // Convert to REST
                                $restCode[$r]['location_group_id'] = $this->data['LocationGroup']['sys_code'];
                                $restCode[$r]['user_id']   = $this->Helper->getSQLSysCode("users",$this->data['LocationGroup']['user_id'][$i]);
                                $restCode[$r]['dbtodo']    = 'user_location_groups';
                                $restCode[$r]['actodo']    = 'is';
                                $r++;
                            }
                        }
                        // Save File Send
                        $this->Helper->sendFileToSync($restCode, 0, 0);
                        // Save User Activity
                        $this->Helper->saveUserActivity($user['User']['id'], 'Warehouse', 'Save Edit', $id);
                        echo MESSAGE_DATA_HAS_BEEN_SAVED;
                        exit;
                    } else {
                        $this->Helper->saveUserActivity($user['User']['id'], 'Warehouse', 'Save Edit (Error '.$error.')', $id);
                        echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                        exit;
                    }
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'Warehouse', 'Save Edit (Error)', $id);
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        if (empty($this->data)) {
            $this->Helper->saveUserActivity($user['User']['id'], 'Warehouse', 'Edit', $id);
            $this->data = $this->LocationGroup->read(null, $id);
            $locationGroupTypes = ClassRegistry::init('LocationGroupType')->find("list", array("conditions" => array("LocationGroupType.is_active = 1", "LocationGroupType.id != 1")));
            $this->set(compact("locationGroupTypes"));
        }
    }

    function delete($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        $sqlCheck = mysql_query("SELECT id FROM locations WHERE location_group_id = ".$id." AND is_active = 1 LIMIT 1;");
        if(!mysql_num_rows($sqlCheck)){
            $r = 0;
            $restCode = array();
            $dateNow  = date("Y-m-d H:i:s");
            Configure::write('debug', 0);
            $this->data = $this->LocationGroup->read(null, $id);
            mysql_query("UPDATE `location_groups` SET `is_active`=2, `modified`='".$dateNow."', `modified_by`=".$user['User']['id']." WHERE `id`=".$id.";");
            $error = mysql_error();
            if($error != 'Data cloud not been delete' && $error != 'Invalid Data'){
                // Convert to REST
                $restCode[$r]['is_active']   = 2;
                $restCode[$r]['modified']    = $dateNow;
                $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
                $restCode[$r]['dbtodo'] = 'location_groups';
                $restCode[$r]['actodo'] = 'ut';
                $restCode[$r]['con']    = "sys_code = '".$this->data['LocationGroup']['sys_code']."'";
                // Save File Send
                $this->Helper->sendFileToSync($restCode, 0, 0);
                // Save User Activity
                $this->Helper->saveUserActivity($user['User']['id'], 'Warehouse', 'Delete', $id);
                echo MESSAGE_DATA_HAS_BEEN_DELETED;
                exit;
            } else {
                $this->Helper->saveUserActivity($user['User']['id'], 'Warehouse', 'Delete (Error have location)', $id);
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                exit;
            }
        } else {
            $this->Helper->saveUserActivity($user['User']['id'], 'Warehouse', 'Delete (Error)', $id);
            echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
            exit;
        }
    }
    
    function exportExcel(){
        $this->layout = 'ajax';
        if (isset($_POST['action']) && $_POST['action'] == 'export') {
            $user = $this->getCurrentUser();
            $this->Helper->saveUserActivity($user['User']['id'], 'Warehouse', 'Export to Excel');
            $filename = "public/report/location_group_export.csv";
            $fp = fopen($filename, "wb");
            $excelContent = 'Location Group' . "\n\n";
            $excelContent .= TABLE_NO . "\t" . TABLE_NAME;
            $query = mysql_query('SELECT id, name FROM location_groups WHERE is_active=1 ORDER BY name');
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