<?php

class ProductsController extends AppController {

    var $name = 'Products';
    var $components = array('Helper', 'ProductCom');

    function index() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        // User Activity
        $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Dashboard');
        $branches = ClassRegistry::init('Branch')->find('all', array(
            'joins' => array(
                array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id')
                )
            ),
            'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id']),
            'fields' => array('id', 'name'),
            'group' => array('Branch.id')));
        $this->set(compact('branches'));
    }

    function ajax($branchId, $category, $displayPro, $priceType = 1, $qty = 'all') {
        $this->layout = 'ajax';
        $this->set(compact('branchId', 'category', 'displayPro', 'priceType', 'qty'));
    }

    function view($id = null) {
        $this->layout = 'ajax';
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        $this->data = $this->Product->read(null, $id);
        // User Activity
        $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'View', $id);
    }

    function upload($rel = null) {
        $this->layout = 'ajax';
        if($rel != ""){
            $photoText = "photo";
        }else{
            $photoText = "photoMain";
        }
        if ($_FILES[$photoText]['name'] != '') {
            $target_folder = 'public/product_photo/tmp/';
            $ext = explode(".", $_FILES[$photoText]['name']);
            $target_name = rand() . '.' . $ext[sizeof($ext) - 1];
            move_uploaded_file($_FILES[$photoText]['tmp_name'], $target_folder . $target_name);
            if (isset($_SESSION['pos_photo']) && $_SESSION['pos_photo'] != '') {
                @unlink($target_folder . $_SESSION['pos_photo']);
            }
            if($rel != ""){
                echo $_SESSION['pos_photo'] = $target_name."|*|".$rel;
            }else{
                echo $_SESSION['pos_photo'] = $target_name;
            }
            exit();
        }
    }
    
    function removePhoto($id = null) {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if ($id == null) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        if(isset($_POST['photo'])){
            $r = 0;
            $e = 0;
            $syncEco = array();
            $restCode = array();
            @unlink('public/product_photo/'.$_POST['photo']);
            @unlink('public/product_photo/tmp/thumbnail/'.$_POST['photo']);
            mysql_query("DELETE FROM `product_photos` WHERE id = '".$id."'");
            // Convert to REST
            $restCode[$r]['dbtodo'] = 'product_photos';
            $restCode[$r]['actodo'] = 'dt';
            $restCode[$r]['con']    = "photo = '".$_POST['photo']."'";
            // Convert to REST E-Commerce
            $syncEco[$e]['dbtodo'] = 'product_photos';
            $syncEco[$e]['actodo'] = 'dt';
            $syncEco[$e]['con']    = "photo = '".$_POST['photo']."'";
            // Save File Send
            $this->Helper->sendFileToSync($restCode, 0, 0);
            // Save File Send to E-Commerce
            $this->Helper->sendFileToSyncPublic($syncEco);
            // Save User Activity
            $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Delete Photo', $id);
            echo MESSAGE_DATA_HAS_BEEN_DELETED;
            exit;
        }else{
            echo MESSAGE_DATA_INVALID;
            exit;
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
            $target_folder = 'public/product_photo/tmp/';
            $target_thumbnail = 'public/product_photo/tmp/thumbnail/';
            $ext = explode(".", $_POST['photoName']);
            $target_name = rand() . '.' . $ext[sizeof($ext) - 1];
            Resize($_POST['photoFolder'], $_POST['photoName'], $target_folder, $target_name, $_POST['w'], $_POST['h'], 100, true);
            Resize($_POST['photoFolder'], $_POST['photoName'], $target_thumbnail, $target_name, 300, 300, 100, true);
            @unlink($target_folder . $_POST['photoName']);
        }
        echo $target_name;
        exit();
    }

    function add($cloneId = null) {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $this->Product->create();
            if ($this->Helper->checkDouplicate('code', 'products', $this->data['Product']['code'], "company_id=".$this->data['Product']['company_id']." AND is_active = 1")) {
                // User Activity
                $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Save Add New (Name ready existed)');
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $r = 0;
                $e = 0;
                $syncEco  = array();
                $restCode = array();
                $dateNow  = date("Y-m-d H:i:s");
                $smValUom = ClassRegistry::init('UomConversion')->find('first', array('fileds' => array('value'), 'order' => 'id', 'conditions' => array('from_uom_id' => $this->data['Product']['price_uom_id'], 'is_small_uom = 1', 'is_active' => 1)));
                if (!empty($smValUom)) {
                    $this->data['Product']['small_val_uom'] = $smValUom['UomConversion']['value'];
                } else {
                    $this->data['Product']['small_val_uom'] = 1;
                }
                if($this->data['Product']['code'] == ""){
                    $this->data['Product']['code'] = $this->data['Product']['barcode'];
                }
                $unitCost = $this->data['Product']['unit_cost'] != "" ? str_replace(",", "", $this->data['Product']['unit_cost']) : 0;
                $this->data['Product']['sys_code']        = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $this->data['Product']['default_cost']    = $unitCost;
                $this->data['Product']['unit_cost']       = $unitCost;
                $this->data['Product']['is_expired_date'] = $this->data['Product']['is_expired_date'];
                $this->data['Product']['reorder_level']   = $this->data['Product']['reorder_level']!=''?$this->data['Product']['reorder_level']:0;
                $this->data['Product']['created']         = $dateNow;
                $this->data['Product']['created_by']      = $user['User']['id'];
                $this->data['Product']['is_active']       = 1;
                if ($this->Product->save($this->data)) {
                    $lastInsertId = $this->Product->id;

					//Insert Service Secondary					
//					$proSsId = ""; 
//					$insertProduct = mysql_query("INSERT INTO ".DB_SS_MONY_KID."products (sys_code, parent_id, company_id, brand_id, photo, code, barcode, name, name_kh, chemical, spec, color_id, description, default_cost, unit_cost, price_uom_id, small_val_uom, width, height, length, size_uom_id, cubic_meter, weight, weight_uom_id, reorder_level, period_from, period_to, file_catalog, created, created_by, modified, modified_by, type, is_expired_date, is_not_for_sale, is_packet, is_lots, is_warranty, is_active) 
//                                  									  			   SELECT sys_code, parent_id, company_id, brand_id, photo, code, barcode, name, name_kh, chemical, spec, color_id, description, default_cost, unit_cost, price_uom_id, small_val_uom, width, height, length, size_uom_id, cubic_meter, weight, weight_uom_id, reorder_level, period_from, period_to, file_catalog, created, created_by, modified, modified_by, type, is_expired_date, is_not_for_sale, is_packet, is_lots, is_warranty, is_active FROM products WHERE id = " . $lastInsertId . ";");
//					$proSsId = mysql_insert_id();
                    // product main photo
                    if ($this->data['Product']['photo'] != '') {
                        $ext = pathinfo($this->data['Product']['photo'], PATHINFO_EXTENSION);
                        $photoName =  $lastInsertId . '_' . md5($this->data['Product']['photo']).".".$ext;
                        rename('public/product_photo/tmp/' . $this->data['Product']['photo'], 'public/product_photo/' . $photoName);
                        rename('public/product_photo/tmp/thumbnail/' . $this->data['Product']['photo'], 'public/product_photo/tmp/thumbnail/' . $photoName);
                        mysql_query("UPDATE products SET photo='" . $photoName . "' WHERE id=" . $lastInsertId);
                        $this->data['Product']['photo'] = $photoName;
                    }
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($this->data['Product'], 'products');
                    $restCode[$r]['modified'] = $dateNow;
                    $restCode[$r]['dbtodo']   = 'products';
                    $restCode[$r]['actodo']   = 'is';
                    $r++;
                    // Check Product Group Share
                    $checkShare = 2;
                    if (!empty($this->data['Product']['pgroup_id'])) {
                        $sqlShare = mysql_query("SELECT id FROM e_pgroup_shares WHERE pgroup_id = ".$this->data['Product']['pgroup_id']);
                        if(mysql_num_rows($sqlShare)){
                            $checkShare = 1;
                        }
                    }
                    // Send to E-Commerce
                    // Convert to REST
                    $shopSys = $this->Helper->getSQLSysCode("companies", $this->data['Product']['company_id']);
                    $syncEco[$e]['shop_id']   = $shopSys;
                    $syncEco[$e]['uom_id']    = $this->Helper->getSQLSysCode("uoms", $this->data['Product']['price_uom_id']);
                    $syncEco[$e]['sys_code']  = $this->data['Product']['sys_code'];
                    $syncEco[$e]['code']      = $this->data['Product']['code'];
                    $syncEco[$e]['barcode']   = $this->data['Product']['barcode'];
                    $syncEco[$e]['name']      = $this->data['Product']['name'];
                    $syncEco[$e]['description'] = $this->data['Product']['description'];
                    $syncEco[$e]['status']    = $checkShare;
                    $syncEco[$e]['created']   = $dateNow;
                    $syncEco[$e]['dbtodo']    = 'products';
                    $syncEco[$e]['actodo']    = 'is';
                    $e++;
                    if($checkShare == 1){
                        mysql_query("INSERT INTO `e_product_shares` (`company_id`, `product_id`, `created`, `created_by`) VALUES (".$this->data['Product']['company_id'].", ".$lastInsertId.", '".$dateNow."', ".$user['User']['id'].");");
                    }
                    // product multi photo
                    if (!empty($this->data['photo'])){
                        for ($i = 0; $i < sizeof($this->data['photo']); $i++) {
                            if(!empty($this->data['photo'][$i]) && $this->data['photo'][$i] != ''){
                                $photoName =  $lastInsertId . '_' . md5($this->data['photo'][$i]);
                                rename('public/product_photo/tmp/' . $this->data['photo'][$i], 'public/product_photo/' . $photoName);
                                rename('public/product_photo/tmp/thumbnail/' . $this->data['photo'][$i], 'public/product_photo/tmp/thumbnail/' . $photoName);
                                mysql_query("INSERT INTO `product_photos`(`product_id`, `photo`) VALUES ('".$lastInsertId."', '".$photoName."')");
                                // Convert to REST
                                $restCode[$r]['product_id'] = $this->data['Product']['sys_code'];
                                $restCode[$r]['photo']      = $photoName;
                                $restCode[$r]['dbtodo']     = 'product_photos';
                                $restCode[$r]['actodo']     = 'is';
                                $r++;
                                // Convert to REST E-Commerce
                                $syncEco[$e]['product_id'] = $this->data['Product']['sys_code'];
                                $syncEco[$e]['photo']      = $photoName;
                                $syncEco[$e]['dbtodo']     = 'product_photos';
                                $syncEco[$e]['actodo']     = 'is';
                                $e++;
                            }
                        }
                    }
                    // product group
                    if (!empty($this->data['Product']['pgroup_id'])) {
						$oPgroupSys = "";
                        mysql_query("INSERT INTO product_pgroups (product_id, pgroup_id) VALUES ('".$lastInsertId."', '".$this->data['Product']['pgroup_id']."')");
						
						//Second System
//						$sqlPgroup = mysql_query("SELECT id, sys_code FROM pgroups WHERE pgroups.is_active = 1 AND id = $this->data['Product']['pgroup_id']");
//						while($rowPgroup = mysql_num_rows($sqlPgroup)){
//							$oPgroupSys =  $rowPgroup['sys_code'];
//						}
//
//						$ssPgroupId = "(SELECT id ".DB_SS_MONY_KID."pgroup WHERE is_active = 1 AND sys_code='".$oPgroupSys."')";
//						mysql_query("INSERT INTO ".DB_SS_MONY_KID."product_pgroups (product_id, pgroup_id) VALUES ('".$proSsId."', ".$ssPgroupId.")");
                        // Convert to REST
                        $restCode[$r]['product_id'] = $this->data['Product']['sys_code'];
                        $restCode[$r]['pgroup_id']  = $this->Helper->getSQLSysCode("pgroups", $this->data['Product']['pgroup_id']);
                        $restCode[$r]['dbtodo']     = 'product_pgroups';
                        $restCode[$r]['actodo']     = 'is';
                        $r++;
                        // Convert to REST
                        $syncEco[$e]['product_id'] = $this->data['Product']['sys_code'];
                        $syncEco[$e]['pgroup_id']  = $this->Helper->getSQLSysCode("pgroups", $this->data['Product']['pgroup_id']);
                        $syncEco[$e]['dbtodo']     = 'product_pgroups';
                        $syncEco[$e]['actodo']     = 'is';
                        $e++;
                    }
                    // SKU of each UOM
                    if (!empty($this->data['sku_uom_value'])) {
                        for ($i = 0; $i < sizeof($this->data['sku_uom_value']); $i++) {
                            if ($this->data['sku_uom_value'][$i] != '' && $this->data['sku_uom'][$i] != '') {
                                mysql_query("INSERT INTO product_with_skus (product_id, sku, uom_id) VALUES ('" . $lastInsertId . "', '" . $this->data['sku_uom_value'][$i] . "', '" . $this->data['sku_uom'][$i] . "')");
                                // Convert to REST
                                $restCode[$r]['product_id'] = $this->data['Product']['sys_code'];
                                $restCode[$r]['sku']        = $this->data['sku_uom_value'][$i];
                                $restCode[$r]['uom_id']     = $this->Helper->getSQLSysCode("uoms", $this->data['sku_uom'][$i]);
                                $restCode[$r]['dbtodo']     = 'product_with_skus';
                                $restCode[$r]['actodo']     = 'is';
                                $r++;
                            }
                        }
                    }
//                    if (!empty($this->data['Product']['branch_id'])) {
//                        for ($i = 0; $i < sizeof($this->data['Product']['branch_id']); $i++) {
//                            mysql_query("INSERT INTO product_branches (product_id,branch_id) VALUES ('" . $lastInsertId . "','" . $this->data['Product']['branch_id'][$i] . "')");
//                            // Convert to REST
//                            $restCode[$r]['product_id'] = $this->data['Product']['sys_code'];
//                            $restCode[$r]['branch_id']  = $this->Helper->getSQLSysCode("branches", $this->data['Product']['branch_id'][$i]);
//                            $restCode[$r]['dbtodo']     = 'product_branches';
//                            $restCode[$r]['actodo']     = 'is';
//                            $r++;
//                        }
//                    }
                    $branches = ClassRegistry::init('Branch')->find("all", array("conditions" => array("Branch.is_active = 1")));
                    foreach($branches AS $branch){
                        mysql_query("INSERT INTO product_branches (product_id,branch_id) VALUES ('" . $lastInsertId . "','" . $branch['Branch']['id'] . "')");
						

						//Second System
//						mysql_query("INSERT INTO ".DB_SS_MONY_KID."product_branches (product_id, branch_id) VALUES ('".$proSsId."', '".$branch['Branch']['id']."')");
						
						// Convert to REST
                        $restCode[$r]['product_id'] = $this->data['Product']['sys_code'];
                        $restCode[$r]['branch_id']  = $this->Helper->getSQLSysCode("branches", $branch['Branch']['id']);
                        $restCode[$r]['dbtodo']     = 'product_branches';
                        $restCode[$r]['actodo']     = 'is';
                        $r++;
                    }
                    // Save File Send
                    $this->Helper->sendFileToSync($restCode, 0, 0);
                    // Save File Send to E-Commerce
                    $this->Helper->sendFileToSyncPublic($syncEco);
                    // Save User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Save Add New', $lastInsertId);
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    // User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Save Add New (Error)');
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        if(!empty($cloneId)){
            // User Activity
            $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Clone');
        }else{
            // User Activity
            $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Add New');
        }
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))), 'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $pgroups   = ClassRegistry::init('Pgroup')->find('list', array('order' => 'Pgroup.name', 'conditions' => array('Pgroup.is_active' => 1, 'Pgroup.id IN (SELECT pgroup_id FROM pgroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].'))')));
        $uoms      = ClassRegistry::init('Uom')->find("list", array("conditions" => array("Uom.is_active = 1"), "order" => "Uom.name"));
        $brands    = ClassRegistry::init('Brand')->find("list", array("conditions" => array("Brand.is_active = 1")));
        $this->set(compact("companies", "branches", "uoms", "pgroups", "code", "cloneId", "brands"));
    }

    function edit($id = null) {
        $this->layout = 'ajax';
        if (!$id && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            if ($this->Helper->checkDouplicateEdit('code', 'products', $this->data['Product']['id'], $this->data['Product']['code'], "company_id=".$this->data['Product']['company_id']." AND is_active = 1")) {
                // User Activity
                $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Save Edit (Name ready existed)', $id);
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $r = 0;
                $e = 0;
                $syncEco  = array();
                $restCode = array();
                $dateNow  = date("Y-m-d H:i:s");
                $makeProcess = false;
                // Check Product Tracsation
                $sqlCheckPgroupUse = mysql_query("SELECT id FROM inventories WHERE product_id = ".$id." LIMIT 1;");
                if(mysql_num_rows($sqlCheckPgroupUse)){
                    $makeProcess = true;
                }
                $smValUom = ClassRegistry::init('UomConversion')->find('first', array('fileds' => array('value'), 'order' => 'id', 'conditions' => array('from_uom_id' => $this->data['Product']['price_uom_id'], 'is_small_uom = 1', 'is_active' => 1)));
                if (!empty($smValUom)) {
                    $this->data['Product']['small_val_uom'] = $smValUom['UomConversion']['value'];
                } else {
                    $this->data['Product']['small_val_uom'] = 1;
                }
                if($this->data['Product']['code'] == ""){
                    $this->data['Product']['code'] = $this->data['Product']['barcode'];
                }
                $this->data['Product']['is_expired_date'] = $this->data['Product']['is_expired_date'];
                $this->data['Product']['reorder_level']   = $this->data['Product']['reorder_level']!=''?$this->data['Product']['reorder_level']:0;
                $this->data['Product']['modified_by']     = $user['User']['id'];
                if($makeProcess == false && !empty($this->data['Product']['default_cost'])){
                    $this->data['Product']['unit_cost']   = $this->data['Product']['default_cost'];
                }
                if ($this->Product->save($this->data)) {

					//Update Service Secondary		
					// $updateSer = mysql_query("UPDATE ".DB_SS_MONY_KID."services SET name = '".$this->data['Service']['name']."', section_id = '".$this->data['Service']['section_id']."', code = '".$this->data['Service']['code']."', description = '".$this->data['Service']['description']."', is_default= '".$this->data['Service']['is_default']."', modified = '".$dateNow."', modified_by = '".$user['User']['id']."' WHERE sys_code ='".$this->data['Service']['sys_code']."'");
//                    $insertProduct = mysql_query("UPDATE ".DB_SS_MONY_KID."products SET 
//								parent_id = '".$this->data['Product']['name']."', 
//								company_id = '".$this->data['Product']['name']."', 
//								brand_id = '".$this->data['Product']['name']."', 
//								code = '".$this->data['Product']['name']."', 
//								barcode = '".$this->data['Product']['name']."', 
//								name = '".$this->data['Product']['name']."', 
//								name_kh = '".$this->data['Product']['name']."', 
//								chemical= '".$this->data['Product']['chemical']."', 
//								spec = '".$this->data['Product']['spec']."', 
//								description= '".$this->data['Product']['description']."', 
//								default_cost= '".$this->data['Product']['default_cost']."', 
//								unit_cost= '".$this->data['Product']['default_cost']."', 
//								price_uom_id= '".$this->data['Product']['price_uom_id']."', 
//								width= '".$this->data['Product']['width']."', 
//								height= '".$this->data['Product']['height']."', 
//								length= '".$this->data['Product']['length']."', 
//								size_uom_id= '".$this->data['Product']['size_uom_id']."', 
//								cubic_meter= '".$this->data['Product']['cubic_meter']."', 
//								weight= '".$this->data['Product']['weight']."', 
//								weight_uom_id= '".$this->data['Product']['weight_uom_id']."', 
//								reorder_level= '".$this->data['Product']['reorder_level']."', 
//								modified= '".$dateNow."', 
//								modified_by= '".$user['User']['id']."', 
//								type= '1', 
//								is_expired_date= '".$this->data['Product']['is_expired_date']."', 
//								is_not_for_sale= '".$this->data['Product']['is_not_for_sale']."', 
//								is_packet= '".$this->data['Product']['is_packet']."', 
//								is_lots= '".$this->data['Product']['is_lots']."'
//								WHERE sys_code ='".$this->data['Service']['sys_code']."'");
								
					// product Main photo
                    if ($this->data['Product']['new_photo'] != '') {
                        $ext = pathinfo($this->data['Product']['new_photo'], PATHINFO_EXTENSION);
                        $photoName =  $this->data['Product']['id'] . '_' . $this->data['Product']['new_photo'].".".$ext;
                        rename('public/product_photo/tmp/' . $this->data['Product']['new_photo'], 'public/product_photo/' . $photoName);
                        rename('public/product_photo/tmp/thumbnail/' . $this->data['Product']['new_photo'], 'public/product_photo/tmp/thumbnail/' . $photoName);
                        @unlink('public/product_photo/' . $this->data['Product']['old_photo']);
                        @unlink('public/product_photo/tmp/thumbnail/' . $this->data['Product']['old_photo']);
                        mysql_query("UPDATE products SET photo='" . $photoName . "' WHERE id=" . $this->data['Product']['id']);
                        $this->data['Product']['photo'] = $photoName;
                    }
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($this->data['Product'], 'products');
                    $restCode[$r]['dbtodo'] = 'products';
                    $restCode[$r]['actodo'] = 'ut';
                    $restCode[$r]['con']    = "sys_code = '".$this->data['Product']['sys_code']."'";
                    $r++;
                    // Send to E-Commerce
                    // Convert to REST
                    $shopSys = $this->Helper->getSQLSysCode("companies", $this->data['Product']['company_id']);
                    $syncEco[$e]['shop_id']   = $shopSys;
                    $syncEco[$e]['uom_id']    = $this->Helper->getSQLSysCode("uoms", $this->data['Product']['price_uom_id']);
                    $syncEco[$e]['code']      = $this->data['Product']['code'];
                    $syncEco[$e]['barcode']   = $this->data['Product']['barcode'];
                    $syncEco[$e]['name']      = $this->data['Product']['name'];
                    $syncEco[$e]['description'] = $this->data['Product']['description'];
                    $syncEco[$e]['created']   = $dateNow;
                    $syncEco[$e]['dbtodo']    = 'products';
                    $syncEco[$e]['actodo']    = 'ut';
                    $syncEco[$e]['con']       = "sys_code = '".$this->data['Product']['sys_code']."'";
                    $e++;
                    // product Multi photo
                    if (!empty($this->data['new_photo'])) {    
                        // Insert Photo
                        for ($indexNewPhoto = 0; $indexNewPhoto < sizeof($this->data['new_photo']); $indexNewPhoto++) {
                            if(!empty($this->data['new_photo'][$indexNewPhoto]) && $this->data['new_photo'][$indexNewPhoto] != ''){
                                $extPhoto  = explode(".", $this->data['new_photo'][$indexNewPhoto]);
                                $sizePhoto = sizeof($extPhoto) - 1;
                                $photoName =  $this->data['Product']['id'] . '_' .md5($this->data['new_photo'][$indexNewPhoto]).".".$extPhoto[$sizePhoto];
                                rename('public/product_photo/tmp/' . $this->data['new_photo'][$indexNewPhoto], 'public/product_photo/' . $photoName);
                                rename('public/product_photo/tmp/thumbnail/' . $this->data['new_photo'][$indexNewPhoto], 'public/product_photo/tmp/thumbnail/' . $photoName);
                                mysql_query("INSERT INTO `product_photos`(`product_id`, `photo`) VALUES ('".$this->data['Product']['id']."', '".$photoName."')");
                                // Convert to REST
                                $restCode[$r]['product_id'] = $this->data['Product']['sys_code'];
                                $restCode[$r]['photo']      = $photoName;
                                $restCode[$r]['dbtodo']     = 'product_photos';
                                $restCode[$r]['actodo']     = 'is';
                                $r++;
                                // Convert to REST E-Commerce
                                $syncEco[$e]['product_id'] = $this->data['Product']['sys_code'];
                                $syncEco[$e]['photo']      = $photoName;
                                $syncEco[$e]['dbtodo']     = 'product_photos';
                                $syncEco[$e]['actodo']     = 'is';
                                $e++;
                            }
                        }
                    }
                    // product group
                    mysql_query("DELETE FROM product_pgroups WHERE product_id=" . $id);

					//Delete From Second System
//					$proId = "(SELECT id ".DB_SS_MONY_KID."products WHERE is_active = 1 AND sys_code='".$this->data['Product']['sys_code']."')";
//					mysql_query("DELETE FROM ".DB_SS_MONY_KID."product_pgroups WHERE product_id=".$proId."");

                    // Convert to REST
                    $restCode[$r]['dbtodo'] = 'product_pgroups';
                    $restCode[$r]['actodo'] = 'dt';
                    $restCode[$r]['con']    = "product_id = ".$this->data['Product']['sys_code'];
                    $r++;
                    // Convert to REST E-Commerce
                    $syncEco[$e]['dbtodo'] = 'product_pgroups';
                    $syncEco[$e]['actodo'] = 'dt';
                    $syncEco[$e]['con']    = "product_id = ".$this->data['Product']['sys_code'];
                    $e++;
                    if (!empty($this->data['Product']['pgroup_id'])) {
                        mysql_query("INSERT INTO product_pgroups (product_id,pgroup_id) VALUES ('" . $id . "','" . $this->data['Product']['pgroup_id'] . "')");
                    	
						
						//Second System
//						$sqlPgroup = mysql_query("SELECT id, sys_code FROM pgroups WHERE pgroups.is_active = 1 AND id = $this->data['Product']['pgroup_id']");
//						while($rowPgroup = mysql_num_rows($sqlPgroup)){
//							$oPgroupSys =  $rowPgroup['sys_code'];
//						}

//						$proId = "(SELECT id ".DB_SS_MONY_KID."products WHERE is_active = 1 AND sys_code='".$this->data['Product']['sys_code']."')";
//						$ssPgroupId = "(SELECT id ".DB_SS_MONY_KID."pgroups WHERE is_active = 1 AND sys_code='".$oPgroupSys."')";
//						mysql_query("INSERT INTO ".DB_SS_MONY_KID."product_pgroups (product_id, pgroup_id) VALUES (".$proId.", ".$ssPgroupId.")");
						
						// Convert to REST
                        $restCode[$r]['product_id'] = $this->data['Product']['sys_code'];
                        $restCode[$r]['pgroup_id']  = $this->Helper->getSQLSysCode("pgroups", $this->data['Product']['pgroup_id']);
                        $restCode[$r]['dbtodo']     = 'product_pgroups';
                        $restCode[$r]['actodo']     = 'is';
                        $r++;
                        // Convert to REST E-Commerce
                        $syncEco[$e]['product_id'] = $this->data['Product']['sys_code'];
                        $syncEco[$e]['pgroup_id']  = $this->Helper->getSQLSysCode("pgroups", $this->data['Product']['pgroup_id']);
                        $syncEco[$e]['dbtodo']     = 'product_pgroups';
                        $syncEco[$e]['actodo']     = 'is';
                        $e++;
                    }
                    
                    // SKU of each UOM
                    mysql_query("DELETE FROM product_with_skus WHERE product_id=" . $id);
                    // Convert to REST
                    $restCode[$r]['dbtodo'] = 'product_with_skus';
                    $restCode[$r]['actodo'] = 'dt';
                    $restCode[$r]['con']    = "product_id = ".$this->data['Product']['sys_code'];
                    $r++;
                    if (!empty($this->data['sku_uom_value'])) {
                        for ($i = 0; $i < sizeof($this->data['sku_uom_value']); $i++) {
                            if ($this->data['sku_uom_value'][$i] != '' && $this->data['sku_uom'][$i] != '') {
                                mysql_query("INSERT INTO product_with_skus (product_id, sku, uom_id) VALUES ('" . $id . "', '" . $this->data['sku_uom_value'][$i] . "', '" . $this->data['sku_uom'][$i] . "')");
                                // Convert to REST
                                $restCode[$r]['product_id'] = $this->data['Product']['sys_code'];
                                $restCode[$r]['sku']        = $this->data['sku_uom_value'][$i];
                                $restCode[$r]['uom_id']     = $this->Helper->getSQLSysCode("uoms", $this->data['sku_uom'][$i]);
                                $restCode[$r]['dbtodo']     = 'product_with_skus';
                                $restCode[$r]['actodo']     = 'is';
                                $r++;
                            }
                        }
                    }
                    
                    // Product Branch
                    mysql_query("DELETE FROM product_branches WHERE product_id=" . $id);

					//Delete From Second System
//					$proId = "(SELECT id ".DB_SS_MONY_KID."products WHERE is_active = 1 AND sys_code='".$this->data['Product']['sys_code']."')";
//					mysql_query("DELETE FROM ".DB_SS_MONY_KID."product_branches WHERE product_id=".$proId."");
                    // Convert to REST
                    $restCode[$r]['dbtodo'] = 'product_branches';
                    $restCode[$r]['actodo'] = 'dt';
                    $restCode[$r]['con']    = "product_id = ".$this->data['Product']['sys_code'];
                    $r++;
//                    if (!empty($this->data['Product']['branch_id'])) {
//                        for ($i = 0; $i < sizeof($this->data['Product']['branch_id']); $i++) {
//                            mysql_query("INSERT INTO product_branches (product_id,branch_id) VALUES ('" . $id . "','" . $this->data['Product']['branch_id'][$i] . "')");
//                            // Convert to REST
//                            $restCode[$r]['product_id'] = $this->data['Product']['sys_code'];
//                            $restCode[$r]['branch_id']  = $this->Helper->getSQLSysCode("branches", $this->data['Product']['branch_id'][$i]);
//                            $restCode[$r]['dbtodo']     = 'product_branches';
//                            $restCode[$r]['actodo']     = 'is';
//                            $r++;
//                        }
//                    }
                    $branches  = ClassRegistry::init('Branch')->find("all", array("conditions" => array("Branch.is_active = 1")));
                    foreach($branches AS $branch){
                        mysql_query("INSERT INTO product_branches (product_id,branch_id) VALUES ('" . $id . "','" . $branch['Branch']['id'] . "')");
						
						// Second System
//						$proId = "(SELECT id ".DB_SS_MONY_KID."products WHERE is_active = 1 AND sys_code='".$this->data['Product']['sys_code']."')";
//						mysql_query("INSERT INTO ".DB_SS_MONY_KID."product_branches (product_id, branch_id) VALUES (".$proId.", '".$branch['Branch']['id']."')");

						// Convert to REST
                        $restCode[$r]['product_id'] = $this->data['Product']['sys_code'];
                        $restCode[$r]['branch_id']  = $this->Helper->getSQLSysCode("branches", $branch['Branch']['id']);
                        $restCode[$r]['dbtodo']     = 'product_branches';
                        $restCode[$r]['actodo']     = 'is';
                        $r++;
                    }
                    // Save File Send
                    $this->Helper->sendFileToSync($restCode, 0, 0);
                    // Save File Send to E-Commerce
                    $this->Helper->sendFileToSyncPublic($syncEco);
                    // Save User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Save Edit', $id);
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    // User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Save Edit (Error)', $id);
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Product->read(null, $id);
            // User Activity
            $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Edit', $id);
            $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
            $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))), 'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
            $uoms      = ClassRegistry::init('Uom')->find("list", array("conditions" => array("Uom.is_active = 1")));
            $pgroupsSellecteds = ClassRegistry::init('ProductPgroup')->find('list', array('fields' => array('id', 'pgroup_id'), 'order' => 'id', 'conditions' => array('product_id' => $id)));
            $pgroupsSellected = array();
            foreach ($pgroupsSellecteds as $ps) {
                array_push($pgroupsSellected, $ps);
            }
            $parentName = $this->Product->find("first", array('fields' => array('name'), 'conditions' => array('Product.id' => $this->data["Product"]["parent_id"])));
            $pgroups    = ClassRegistry::init('Pgroup')->find('list', array('order' => 'id', 'conditions' => array('is_active' => 1, 'id IN (SELECT pgroup_id FROM pgroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].'))')));
            $brands     = ClassRegistry::init('Brand')->find("list", array("conditions" => array("Brand.is_active = 1")));
            $this->set(compact("companies", "branches", "uoms", "pgroups", "pgroupsSellected", "parentName", "brands"));
        }
    }

    function delete($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $child = $this->Product->find('all', array('fields' => array('Product.id'), 'conditions' => array('parent_id=' . $id), 'joins' => array(array('table' => 'inventories', 'type' => 'INNER', 'alias' => 'Inventory', 'conditions' => 'Inventory.product_id = Product.id'))));
        $r = 0;
        $restCode = array();
        $dateNow  = date("Y-m-d H:i:s");
        $user = $this->getCurrentUser();
        $this->data = $this->Product->read(null, $id);
        if (!empty($child)) {
            $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Delete '.$this->data['Product']['code'].' Error Have Child Product');
            echo MESSAGE_DATA_HAVE_CHILD;
            exit;
        } else {
            Configure::write('debug', 0);
            mysql_query("UPDATE `products` SET `is_active`=2, `modified`='".$dateNow."', `modified_by`=".$user['User']['id']." WHERE `id`=".$id.";");
            $error = mysql_error();
            if($error != 'Data cloud not been delete'){
                // Convert to REST
                $restCode[$r]['is_active']   = 2;
                $restCode[$r]['modified']    = $dateNow;
                $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
                $restCode[$r]['dbtodo'] = 'products';
                $restCode[$r]['actodo'] = 'ut';
                $restCode[$r]['con']    = "sys_code = '".$this->data['Product']['sys_code']."'";
                // Save File Send
                $this->Helper->sendFileToSync($restCode, 0, 0);
                // Send to E-Commerce
                $e = 0;
                $syncEco = array();
                // Convert to REST
                $syncEco[$e]['status']   = 0;
                $syncEco[$e]['modified'] = $dateNow;
                $syncEco[$e]['dbtodo']   = 'products';
                $syncEco[$e]['actodo']   = 'ut';
                $syncEco[$e]['con']      = "sys_code = '".$this->data['Product']['sys_code']."'";
                // Save File Send to E-Commerce
                $this->Helper->sendFileToSyncPublic($syncEco);
                // Update Share
                $checkShare = mysql_query("SELECT id FROM e_product_shares WHERE product_id = ".$id);
                if(mysql_fetch_array($checkShare)){
                    mysql_query("UPDATE `e_product_shares` SET is_active = 2 WHERE id = ".$id.";");
                }
                // Save User Activity
                $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Delete', $id);
                echo MESSAGE_DATA_HAS_BEEN_DELETED;
                exit;
            } else {
                $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Delete (Data cloud not been delete)', $id);
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                exit;
            }
        }
    }

    function product($company_id = null) {
        $this->layout = 'ajax';
        $this->set('company_id', $company_id);
    }

    function productAjax($company_id = null, $category = null) {
        $this->layout = 'ajax';
        $this->set('company_id', $company_id);
        $this->set('category', $category);
    }

    function searchProduct() {
        $this->layout = 'ajax';
        $products = $this->Product->find('all', array('conditions' => array('OR' => array('Product.name LIKE' => '%' . $this->params['url']['q'] . '%', 'Product.code LIKE' => '%' . $this->params['url']['q'] . '%', 'Product.price LIKE' => '%' . $this->params['url']['q'] . '%'), 'Product.is_active' => 1)));
        $this->set(compact('products'));
    }

    function searchProductByCode($company_id = null) {
        $this->layout = 'ajax';
        $product_code = !empty($this->data['code']) ? $this->data['code'] : "";
        $product_id = !empty($this->data['id']) ? $this->data['id'] : "";
        $product = $this->Product->find('first', array(
            'fields' => array(
                'Product.id',
                'Product.name',
                'Product.code',
                'Product.description',
                'Product.price',
                'Product.price_uom_id'
            ),
            'conditions' => array(
                array(
                    "OR" => array(
                        'Product.code' => $product_code,
                        'Product.id' => $product_id
                    )
                ),
                'Product.is_active' => 1,
                'Product.company_id' => $company_id
            ),
            'group' => array(
                'Product.id',
                'Product.name',
                'Product.code',
                'Product.description',
                'Product.price',
                'Product.price_uom_id',
            )
                ));
        $this->set(compact('product', 'pricingRules', 'timeSearch'));
    }

    function productPrice($id = null) {
        $this->layout = 'ajax';
        if (empty($id) && empty($this->data)) {
            echo '<b style="font-size: 18px;">'.MESSAGE_SELECT_BRANCH_TO_SHOW_PRICE_LIST.'</b>';
            exit;
        }
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            if(!empty($this->data['type_id'])) {
                $k = 0;
                $r = 0;
                $e = 0;
                $syncEco  = array();
                $restCode = array();
                $dateNow  = date("Y-m-d H:i:s");
                $products = $this->Product->read(null, $this->data['ProductPrice']['product_id']);
                // Save Edit Price
                $this->loadModel('EProductPrice');
//                mysql_query("DELETE FROM `product_prices` WHERE  `product_id` = ".$id);
                // Convert to REST
//                $syncEco[$e]['dbtodo'] = 'product_prices';
//                $syncEco[$e]['actodo'] = 'dt';
//                $syncEco[$e]['con']    = "product_id = ".$this->Helper->getSQLSysCode("products", $this->data['ProductPrice']['product_id']);
//                $e++;
                for ($i = 0; $i <  sizeof($this->data['type_id']); $i++) {
                    $ProductPrice['ProductPrice']['branch_id']     = $this->data['branch_id'];
                    $ProductPrice['ProductPrice']['product_id']    = $this->data['ProductPrice']['product_id'];
                    $ProductPrice['ProductPrice']['price_type_id'] = $this->data['type_id'][$i];
                    for ($j = 0; $j < sizeof($this->data['uom_id']); $j++) {
                        $productPrice = ClassRegistry::init('ProductPrice')->find('first', array('conditions' => array('price_type_id' => $this->data['type_id'][$i], 'product_id' => $this->data['ProductPrice']['product_id'], 'uom_id' => $this->data['uom_id'][$j], 'branch_id' => $this->data['branch_id'])));
                        if (!empty($productPrice)) {
                            $ProductPrice['ProductPrice']['id'] = $productPrice['ProductPrice']['id'];
                            $ProductPrice['ProductPrice']['sys_code'] = $productPrice['ProductPrice']['sys_code'];
                        } else {
                            ClassRegistry::init('ProductPrice')->create();
                            $ProductPrice['ProductPrice']['id'] = null;
                            $ProductPrice['ProductPrice']['sys_code'] = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                        }
                        $ProductPrice['ProductPrice']['uom_id'] = $this->data['uom_id'][$j];
                        $ProductPrice['ProductPrice']['old_unit_cost'] = $this->data['old_unit_cost'][$k];
                        $ProductPrice['ProductPrice']['amount_before'] = $this->data['amount_before'][$k];
                        $ProductPrice['ProductPrice']['amount']   = $this->data['amount'][$k];
                        $ProductPrice['ProductPrice']['percent']  = $this->data['percent'][$k];
                        $ProductPrice['ProductPrice']['add_on']   = $this->data['add_on'][$k];
                        $ProductPrice['ProductPrice']['set_type'] = $this->data['set_type'][$i];
                        $ProductPrice['ProductPrice']['created']  = $dateNow;
                        $ProductPrice['ProductPrice']['created_by'] = $user['User']['id'];
                        ClassRegistry::init('ProductPrice')->save($ProductPrice);
                        // Convert to REST
                        if($ProductPrice['ProductPrice']['id'] == null){
                            $restCode[$r] = $this->Helper->convertToDataSync($ProductPrice['ProductPrice'], 'product_prices');
                            $restCode[$r]['modified'] = $dateNow;
                            $restCode[$r]['dbtodo']   = 'product_prices';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                        } else {
                            $restCode[$r] = $this->Helper->convertToDataSync($ProductPrice['ProductPrice'], 'product_prices');
                            $restCode[$r]['modified'] = $dateNow;
                            $restCode[$r]['dbtodo']   = 'product_prices';
                            $restCode[$r]['actodo']   = 'ut';
                            $restCode[$r]['con']      = "sys_code = '".$ProductPrice['ProductPrice']['sys_code']."'";
                            $r++;
                        }
                        // Send to E-Commerce
                        if($this->data['type_id'][$i] == 1){
                            $eprice = array();
                            $this->EProductPrice->create();
                            $eprice['EProductPrice']['product_id']   = $this->data['ProductPrice']['product_id'];
                            $eprice['EProductPrice']['uom_id']       = $this->data['uom_id'][$j];
                            $eprice['EProductPrice']['before_price'] = $this->data['amount_before'][$k];
                            $eprice['EProductPrice']['sell_price']   = $this->data['amount'][$k];
                            $eprice['EProductPrice']['created']      = $dateNow;
                            $eprice['EProductPrice']['created_by']   = $user['User']['id'];
                            $this->EProductPrice->save($eprice);
                            // Convert to REST
                            $syncEco[$e]['product_id']   = $this->Helper->getSQLSysCode("products", $this->data['ProductPrice']['product_id']);
                            $syncEco[$e]['uom_id']       = $this->Helper->getSQLSysCode("uoms", $this->data['uom_id'][$j]);
                            $syncEco[$e]['before_price'] = $this->data['amount_before'][$k];
                            $syncEco[$e]['sell_price']   = $this->data['amount'][$k];
                            $syncEco[$e]['dbtodo']    = 'product_prices';
                            $syncEco[$e]['actodo']    = 'is';
                            $e++;
                        }
                        $k++;
                    }
                }
                // Save File Send
                $this->Helper->sendFileToSync($restCode, 0, 0);
                // Save File Send to E-Commerce
                $this->Helper->sendFileToSyncPublic($syncEco);
            }
            // Save User Activity
            $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Save Set Price', $id);
            echo MESSAGE_DATA_HAS_BEEN_SAVED;
            exit;
        }
        $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Set Price', $id);
        $branches = ClassRegistry::init('Branch')->find('all',
                    array(
                        'joins' => array(
                            array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id')),
                            array('table' => 'product_branches', 'type' => 'inner', 'conditions' => array('product_branches.branch_id=Branch.id'))
                        ),
                        'fields' => array('Branch.id', 'Branch.name'),
                        'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'], 'product_branches.product_id=' . $id)));
        $products = $this->Product->read(null, $id);
        $this->set(compact('branches', 'products'));
    }
    
    function productPriceDetail($branchId, $id){
        $this->layout = 'ajax';
        if(empty($id) && empty($branchId)){
            exit;
        }
        $currency = mysql_query("SELECT symbol FROM currency_centers WHERE id = (SELECT currency_center_id FROM branches WHERE id = ".$branchId." LIMIT 1)");
        $rowCurr  = mysql_fetch_array($currency);
        $symbol   = $rowCurr[0];
        $products = $this->Product->read(null, $id);
        $branch   = ClassRegistry::init('Branch')->read(null, $branchId);
        $this->set(compact('products', 'branchId', 'symbol', 'branch'));
    }

    function getSkuUom($uomId = null) {
        $this->layout = 'ajax';
        if ($uomId != null) {
            $this->set('uomId', $uomId);
        } else {
            echo "Error Select Uom";
        }
    }

    function checkSkuUom($company_id = null, $sku = null) {
        $this->layout = 'ajax';
        if ($sku != null) {
            $conditions = "OR pws.sku = '" . $sku . "') AND p.company_id = ".$company_id." AND p.is_active = 1";
            if ($this->Helper->checkDouplicateSku('p.code', 'products AS p', $sku, $conditions, "LEFT JOIN product_with_skus as pws ON pws.product_id = p.id")) {
                $result = 'available';
            } else {
                $result = 'not available';
            }
            echo $result;
        } else {
            echo "Error Sku";
        }
        exit;
    }

    function checkPuc($company_id = null, $puc = null) {
        $this->layout = 'ajax';
        if ($puc != null) {
            if ($this->Helper->checkDouplicate('barcode', 'products', $puc, "company_id=".$company_id." AND is_active = 1")) {
                $result = 'available';
            } else {
                $result = 'not available';
            }
            echo $result;
        } else {
            echo "Error UPC";
        }
        exit;
    }

    function checkSkuUomEdit($company_id = null, $sku = null, $product_id = "", $pSkuId = "") {
        $this->layout = 'ajax';
        if ($sku != null) {
            $compareId = "";
            if (!empty($product_id)) {
                $compareId = "p.id <> " . $product_id . " AND";
            }
            if (!empty($pSkuId)) {
                $conditions = "p.company_id = ".$company_id." AND p.is_active = 1 OR pws.sku = '" . $sku . "' AND pws.id <> " . $pSkuId;
            } else {
                $conditions = "p.company_id = ".$company_id." AND p.is_active = 1 OR pws.sku = '" . $sku . "'";
            }
            $join = "LEFT JOIN product_with_skus as pws ON pws.product_id = p.id";
            if ($this->Helper->checkDouplicateEditOther('p.code', 'products AS p', $compareId, $sku, $conditions, $join)) {
                $result = 'available';
            } else {
                $result = 'not available';
            }
            echo $result;
        } else {
            echo "Error Sku";
        }
        exit;
    }

    function checkPucEdit($company_id = null, $puc = null, $product_id = null) {
        $this->layout = 'ajax';
        if ($puc != null && $product_id != null) {
            if ($this->Helper->checkDouplicateEdit('barcode', 'products', $product_id, $puc, "company_id=".$company_id." AND is_active = 1")) {
                $result = 'available';
            } else {
                $result = 'not available';
            }
            echo $result;
        } else {
            echo "Error UPC";
        }
        exit;
    }
    
    function setExpired(){
        $this->layout = 'ajax';
    }
    
    function setProductPacket(){
        $this->layout = 'ajax';
    }
    
    function exportExcel(){
        $this->layout = 'ajax';
        if (isset($_POST['action']) && $_POST['action'] == 'export') {
            $user = $this->getCurrentUser();
            $allowViewCost = $this->Helper->checkAccess($user['User']['id'], $this->params['controller'], 'viewCost');
            $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Export to Excel');
            $filename = "public/report/product_export.csv";
            $fp = fopen($filename, "wb");
            $titlePriceType = '';
            $cmtPriceType   = "SELECT price_types.id, price_types.name FROM price_types INNER JOIN price_type_companies ON price_type_companies.price_type_id = price_types.id WHERE price_type_companies.company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id'].") AND price_types.is_active = 1 ORDER BY price_types.ordering ASC";
            $sqlPriceType   = mysql_query($cmtPriceType);
            if(mysql_num_rows($sqlPriceType)){
                while($rowPriceType = mysql_fetch_array($sqlPriceType)){
                    $titlePriceType .= "\t".$rowPriceType[1];
                }
            }
            $fieldCost = "";
            if($allowViewCost){
                $fieldCost = "\t".TABLE_UNIT_COST;
            }
            $excelContent = 'Products' . "\n\n";
            $excelContent .= TABLE_NO . "\t" . TABLE_COMPANY. "\t" . TABLE_GROUP. "\t" . TABLE_SKU. "\t" . TABLE_BARCODE. "\t" . TABLE_NAME. "\t" . TABLE_UOM. $fieldCost. $titlePriceType. "\t" .GENERAL_REORDER_LEVEL. "\t " .GENERAL_DESCRIPTION;
            $conditionUser = " AND company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id'].")";
            $query = mysql_query('SELECT id, (SELECT name FROM companies WHERE id = products.company_id) AS com_name, (SELECT GROUP_CONCAT(name) FROM pgroups WHERE id IN(SELECT pgroup_id FROM product_pgroups WHERE product_id = products.id)), code, barcode, name, (SELECT name FROM uoms WHERE id = products.price_uom_id), IFNULL(products.default_cost, products.unit_cost) AS unit_cost, price_uom_id, reorder_level, description '
                    . '           FROM products WHERE is_active=1'.$conditionUser.' ORDER BY (SELECT name FROM companies WHERE id = products.company_id)');
            $index = 1;
            while ($data = mysql_fetch_array($query)) {
                $unitCost       = $data['unit_cost']>0?$data['unit_cost']:0;
                // Price by Price Type
                $priceTypeValue = "";
                $sqlPriceType   = mysql_query($cmtPriceType);
                if(mysql_num_rows($sqlPriceType)){
                    while($rowPriceType = mysql_fetch_array($sqlPriceType)){
                        $queryProPrice = mysql_query("SELECT amount, percent, add_on, set_type FROM product_prices WHERE product_id =".$data[0]." AND uom_id = ".$data['price_uom_id']." AND price_type_id = ".$rowPriceType[0]);
                        if(mysql_num_rows($queryProPrice)){
                            while($dataProPrice  = mysql_fetch_array($queryProPrice)){
                                $type = $dataProPrice['set_type'];
                                $unitPrice = 0;
                                if($type == 1){
                                    $unitPrice = $dataProPrice['amount'];
                                }else if($type == 2){
                                    $percent   = ($unitCost * $dataProPrice['percent']) / 100;
                                    $unitPrice = $unitCost + $percent;
                                }else if($type == 3){
                                    $unitPrice = $unitCost + $dataProPrice['add_on'];
                                }
                                $priceTypeValue .= "\t".number_format($unitPrice, 2);
                            }
                        } else {
                            $priceTypeValue .= "\t0.00";
                        }
                    }
                }
                $showCost = "";
                if($allowViewCost){
                    $showCost = "\t".$unitCost;
                }
                $excelContent .= "\n" . $index++ . "\t" . $data['com_name']. "\t" . $data[2]. "\t" . $data[3]. "\t" . $data[4]. "\t" . $data[5]. "\t" . $data[6]. $showCost. $priceTypeValue. "\t" . $data['reorder_level']. "\t" . $data['description'];
            }
            $excelContent = chr(255) . chr(254) . @mb_convert_encoding($excelContent, 'UTF-16LE', 'UTF-8');
            fwrite($fp, $excelContent);
            fclose($fp);
            exit();
        }
    }
    
    function setCost(){
        $this->layout = 'ajax';
    }
    
    function setProductWithCustomer($productId, $customerId){
        $this->layout = 'ajax';
        if(!empty($productId) && !empty($customerId) && !empty($this->data)){
            if($this->data['name'] != ''){
                $user = $this->getCurrentUser();
                $productName = mysql_real_escape_string($this->data['name']);
                mysql_query("INSERT INTO `product_with_customers` (`product_id`, `customer_id`, `name`, `created`, `created_by`) VALUES (".$productId.", ".$customerId.", '".$productName."', '".date("Y-m-d H:i:s")."', ".$user['User']['id'].")
                             ON DUPLICATE KEY UPDATE `created`='".date("Y-m-d H:i:s")."';");
            }
        }
        exit;
    }
    
    function cloneProductInfo($id){
        $this->layout = 'ajax';
        $clone = array();
        $user = $this->getCurrentUser();
        if (!$id) {
            // User Activity
            $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Clone Invalid Product Id', $id);
            $clone['error'] = 1;
            echo json_encode($clone);
            exit;
        }
        $this->data = $this->Product->read(null, $id);
        if(empty($this->data)){
            // User Activity
            $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Clone Invalid Product', $id);
            $clone['error'] = 2;
            echo json_encode($clone);
            exit;
        }
        // User Activity
        $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Clone', $id);
        // Product Information
        $clone['error'] = 0;
        $clone['Product']['is_expired_date'] = $this->data['Product']['is_expired_date'];
        $clone['Product']['pgroup_id'] = '';
        $clone['Product']['name']  = $this->data['Product']['name'];
        $clone['Product']['color'] = $this->data['Product']['color'];
        $clone['Product']['photo'] = $this->data['Product']['photo'];
        $clone['Product']['price_uom_id'] = $this->data['Product']['price_uom_id'];
        $clone['Product']['is_not_for_sale'] = $this->data['Product']['is_not_for_sale'];
        $clone['Product']['reorder_level'] = $this->data['Product']['reorder_level'];
        $clone['Product']['spec'] = $this->data['Product']['spec'];
        $clone['Product']['description'] = $this->data['Product']['description'];
        $clone['Product']['width'] = $this->data['Product']['width'];
        $clone['Product']['height'] = $this->data['Product']['height'];
        $clone['Product']['length'] = $this->data['Product']['length'];
        $clone['Product']['size_uom_id'] = $this->data['Product']['size_uom_id'];
        $clone['Product']['cubic_meter'] = $this->data['Product']['cubic_meter'];
        $clone['Product']['weight'] = $this->data['Product']['weight'];
        $clone['Product']['weight_uom_id'] = $this->data['Product']['weight_uom_id'];
        $clone['Product']['period_from'] = '';
        $clone['Product']['period_to'] = '';
        if($this->data['Product']['period_from'] != '' && $this->data['Product']['period_from'] != '0000-00-00'){
            $clone['Product']['period_from'] = $this->Helper->dateShort($this->data['Product']['period_from']);
        }
        if($this->data['Product']['period_to'] != '' && $this->data['Product']['period_to'] != '0000-00-00'){
            $clone['Product']['period_to'] = $this->Helper->dateShort($this->data['Product']['period_to']);
        }
        // ICS
        $ics = mysql_query("SELECT * FROM accounts WHERE product_id = ".$id);
        if(mysql_num_rows($ics)){
            while($rowIcs = mysql_fetch_array($ics)){
                if($rowIcs['account_type_id'] == 1){
                    $clone['Product']['ics_inv'] = $rowIcs['chart_account_id'];
                } else if($rowIcs['account_type_id'] == 2){
                    $clone['Product']['ics_cogs'] = $rowIcs['chart_account_id'];
                } else {
                    $clone['Product']['ics_sales'] = $rowIcs['chart_account_id'];
                }
            }
        } else {
            $clone['Product']['ics_inv'] = '';
            $clone['Product']['ics_cogs'] = '';
            $clone['Product']['ics_sales'] = '';
        }
        $pgroup = mysql_query("SELECT pgroup_id FROM product_pgroups WHERE product_id = ".$id." LIMIT 1;");
        if(mysql_num_rows($pgroup)){
            $rowPgroup = mysql_fetch_array($pgroup);
            $clone['Product']['pgroup_id'] = $rowPgroup[0];
        }
        echo json_encode($clone);
        exit;
    }
    
    function viewTotalCostPrice(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        // Check Module Exist
        $sqlDash = mysql_query("SELECT id FROM user_dashboards WHERE module_id = 485 AND user_id = {$user['User']['id']} LIMIT 1");
        if(!mysql_num_rows($sqlDash)){
            $this->loadModel('UserDashboard');
            $userDash = array();
            $userDash['UserDashboard']['user_id']      = $user['User']['id'];
            $userDash['UserDashboard']['module_id']    = 485;
            $userDash['UserDashboard']['display']      = 1;
            $userDash['UserDashboard']['auto_refresh'] = 1;
            $userDash['UserDashboard']['time_refresh'] = 5;
            $this->UserDashboard->save($userDash);
        }
    }
    
    function viewChangeCost(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        // Check Module Exist
        $sqlDash = mysql_query("SELECT id FROM user_dashboards WHERE module_id = 486 AND user_id = {$user['User']['id']} LIMIT 1");
        if(!mysql_num_rows($sqlDash)){
            $this->loadModel('UserDashboard');
            $userDash = array();
            $userDash['UserDashboard']['user_id']      = $user['User']['id'];
            $userDash['UserDashboard']['module_id']    = 486;
            $userDash['UserDashboard']['display']      = 1;
            $userDash['UserDashboard']['auto_refresh'] = 1;
            $userDash['UserDashboard']['time_refresh'] = 5;
            $this->UserDashboard->save($userDash);
        }
    }
    
    function resultChangeCost(){
        $this->layout = 'ajax';
        $dateNow = date("Y-m-d");
        $content = array();
        $result  = '';
        $sqlHis = mysql_query("SELECT products.code, products.name, product_unit_cost_histories.old_cost, product_unit_cost_histories.new_cost FROM product_unit_cost_histories INNER JOIN products ON products.id = product_unit_cost_histories.product_id WHERE DATE(product_unit_cost_histories.created) = '".$dateNow."' ORDER BY product_unit_cost_histories.created DESC LIMIT 15");
        if(mysql_num_rows($sqlHis)){
            $index = 1;
            $symbol = '';
            if($rowHis['new_cost'] > $rowHis['old_cost']){
                $img = 'up.png';
                $color = 'color: #0a0;';
            } else if($rowHis['old_cost'] > $rowHis['new_cost']){
                $img = 'down.png';
                $color = 'color: red;';
            } else {
                $img = '';
                $color = '';
            }
            if($img != ''){
                $symbol = '<img src="' . $this->webroot . 'img/button/'.$img.'" style="margin-left: 5px;" />';
            }
            while($rowHis = mysql_fetch_array($sqlHis)){
                $result .= '<tr>';
                $result .= '<td class="first">'.$index.'</td>';
                $result .= '<td>'.$rowHis['code'].'</td>';
                $result .= '<td>'.$rowHis['name'].'</td>';
                $result .= '<td>'.number_format($rowHis['old_cost'], 2).'</td>';
                $result .= '<td style="'.$color.'">'.number_format($rowHis['new_cost'], 2).$symbol.'</td>';
                $result .= '</tr>';
            }
        } else {
            $result .= '<td colspan="5" class="first">'.TABLE_NO_RECORD.'</td>';
        }
        $content['update'] = date("d/m/Y H:i:s");
        $content['result'] = $result;
        echo json_encode($result);
        exit;
    }
    
    function viewProductHistory($productId = null){
        $this->layout = 'ajax';
        if (!$productId) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        // User Activity
        $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'View Product History', $productId);             
        $this->set(compact('productId'));
    }
    
    function viewProductReorderLevel(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        // Check Module Exist
        $sqlDash = mysql_query("SELECT id FROM user_dashboards WHERE module_id = 491 AND user_id = {$user['User']['id']} LIMIT 1");
        if(!mysql_num_rows($sqlDash)){
            $queryProductReorderLevelPer = mysql_query("SELECT id FROM modules WHERE name = 'Products Reorder Level' LIMIT 01");
            $rowProductReorderLevelPer   = mysql_fetch_array($queryProductReorderLevelPer);
            $this->loadModel('UserDashboard');
            $userDash = array();
            $userDash['UserDashboard']['user_id']      = $user['User']['id'];
            $userDash['UserDashboard']['module_id']    = $rowProductReorderLevelPer[0];
            $userDash['UserDashboard']['display']      = 1;
            $userDash['UserDashboard']['auto_refresh'] = 1;
            $userDash['UserDashboard']['time_refresh'] = 5;
            $this->UserDashboard->save($userDash);
        }
    }
    
    function viewProductReorderLevelAjax(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
    }
    
    function viewProductExpireDate(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        // Check Module Exist
        $sqlDash = mysql_query("SELECT id FROM user_dashboards WHERE module_id = (SELECT id FROM modules WHERE name = 'Products Expire Date') AND user_id = {$user['User']['id']} LIMIT 1");
        if(!mysql_num_rows($sqlDash)){
            $queryProductExpireDatePer = mysql_query("SELECT id FROM modules WHERE name = 'Products Expire Date' LIMIT 01");
            $rowProductExpireDatePer   = mysql_fetch_array($queryProductExpireDatePer);
            $this->loadModel('UserDashboard');
            $userDash = array();
            $userDash['UserDashboard']['user_id']      = $user['User']['id'];
            $userDash['UserDashboard']['module_id']    = $rowProductExpireDatePer[0];
            $userDash['UserDashboard']['display']      = 1;
            $userDash['UserDashboard']['auto_refresh'] = 1;
            $userDash['UserDashboard']['time_refresh'] = 5;
            $this->UserDashboard->save($userDash);
        }
    }
    
    function viewProductExpireDateAjax(){
        $this->layout = 'ajax';
    }
    
    function printProduct($priceType = null, $pgroupId = null){
        $this->layout = 'ajax';
        $this->set(compact("pgroupId", "priceType"));
    }
    
    function printProductByOne($id = null, $is_uom_small = 1, $priceType = null){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        
        $skuName = "";
        if(!empty($_POST['barcode'])){
            $skuName = $_POST['barcode'];
        }
        
        $product = $this->Product->read(null, $id);
        $this->set(compact("product", "is_uom_small", "skuName", "priceType"));
    }
    
    function printBarcode($pgroupId = null){
        $this->layout = 'ajax';
        $this->set(compact("pgroupId"));
    }
    
    function printBarcodeByOne($id = null, $priceType = null, $nubmerCopy = 1){
        $this->layout = 'ajax';
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $product = $this->Product->read(null, $id);
        $this->set(compact("product", "priceType", "nubmerCopy"));
    }
    
    function printProductByCheck($pId = null, $save = null){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!$pId) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        if($pId == "clearData"){
            mysql_query("DELETE FROM `user_print_product` WHERE user_id = ".$user['User']['id']."");
            echo MESSAGE_DATA_HAS_BEEN_DELETED;
        }else{
            mysql_query("DELETE FROM `user_print_product` WHERE user_id = ".$user['User']['id']." AND product_id = ".$pId."");
            if($save == 0){
                mysql_query("INSERT INTO `user_print_product`(`user_id`, `product_id`) VALUES (".$user['User']['id'].", ".$pId.")");
            }
        }
        exit;
    }
    
    function printByUomBarcode($proId = null){
        $this->layout = 'ajax';
        if (!$proId) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $selectOption = "<select id='proUomSku' style='width: 214px;'>";
        
        //Main Uom
        $queryProMainUom = mysql_query("SELECT price_uom_id, (SELECT name FROM uoms WHERE id = price_uom_id), code FROM products WHERE id = {$proId} LIMIT 01");
        $dataProMainUom  = mysql_fetch_array($queryProMainUom);
        
        //Pro With Sku
        $queryProUomSku = mysql_query("SELECT uom_id, (SELECT name FROM uoms WHERE id = uom_id), sku FROM product_with_skus WHERE product_id = {$proId} ORDER BY sku");
        if(mysql_num_rows($queryProUomSku) > 0){
            while($dataProUomSku = mysql_fetch_array($queryProUomSku)){
                $selectOption .= "<option vlaue='".$dataProUomSku[0]."' sku-name='".$dataProUomSku[2]."'>".$dataProUomSku[1]."</option>";
            }
        }else{
            $selectOption .= "<option vlaue='".$dataProMainUom[0]."' sku-name='".$dataProMainUom[2]."'>".$dataProMainUom[1]."</option>";
        }
        $selectOption .= "</select>";
        echo $selectOption;
        exit();
    }
    
    function viewActivityByGraph($productId = null, $dateRange = null, $group = null, $chart = null){
        $this->layout = 'ajax';
        if(empty($productId) || empty($dateRange) || empty($group) || empty($chart)){
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $this->set(compact('dateRange', 'group', 'chart', 'productId'));
    }
    
    function viewPurchaseSalesByGraph($productId = null, $dateRange = null, $group = null, $chart = null){
        $this->layout = 'ajax';
        if(empty($productId) || empty($dateRange) || empty($group) || empty($chart)){
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $this->set(compact('dateRange', 'group', 'chart', 'productId'));
    }
    
    function addPgroup(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $this->loadModel('Pgroup');
            $result   = array();
            $comCheck = 0;
            if(!empty($this->data['Pgroup']['company_id'])){
                if(is_array($this->data['Pgroup']['company_id'])){
                    $comCheck = implode(",", $this->data['Pgroup']['company_id']);
                } else {
                    $comCheck = $this->data['Pgroup']['company_id'];
                }
            }
            if ($this->Helper->checkDouplicate('name', 'pgroups', $this->data['Pgroup']['name'], 'is_active = 1 AND id IN (SELECT pgroup_id FROM pgroup_companies WHERE company_id IN ('.$comCheck.'))')) {
                $this->Helper->saveUserActivity($user['User']['id'], 'Product Group', 'Save Quick Add New (Name ready existed)');
                $result['error'] = 2;
                echo json_encode($result);
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
                    $pgroupId = $this->Pgroup->id;
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($this->data['Pgroup'], 'pgroups');
                    $restCode[$r]['modified']   = $dateNow;
                    $restCode[$r]['dbtodo']     = 'pgroups';
                    $restCode[$r]['actodo']     = 'is';
                    $r++;
                    // Pgroup Company
                    if (!empty($this->data['Pgroup']['company_id'])) {
                        for ($i = 0; $i < sizeof($this->data['Pgroup']['company_id']); $i++) {
                            mysql_query("INSERT INTO pgroup_companies (pgroup_id, company_id) VALUES ('" . $pgroupId . "','" . $this->data['Pgroup']['company_id'][$i] . "')");
                            // Convert to REST
                            $restCode[$r]['pgroup_id']  = $this->data['Pgroup']['sys_code'];
                            $restCode[$r]['company_id'] = $this->Helper->getSQLSysCode("companies", $this->data['Pgroup']['company_id'][$i]);
                            $restCode[$r]['dbtodo']     = 'pgroup_companies';
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
                    // Save File Send
                    $this->Helper->sendFileToSync($restCode, 0, 0);
                    // Save File Send to E-Commerce
                    $this->Helper->sendFileToSyncPublic($syncEco);
                    // Save User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'Product Group', 'Save Quick Add New', $pgroupId);
                    $result['error']  = 0;
                    $result['option'] = '<option value="">'.INPUT_SELECT.'</option>';
                    $pgroups = ClassRegistry::init('Pgroup')->find('all', array('order' => 'name', 'conditions' => array('is_active' => 1)));
                    foreach($pgroups AS $pgroup){
                        $selected = '';
                        if($pgroup['Pgroup']['id'] == $pgroupId){
                            $selected = 'selected="selected"';
                        }
                        $result['option'] .= '<option value="'.$pgroup['Pgroup']['id'].'" '.$selected.'>'.$pgroup['Pgroup']['name'].'</option>';
                    }
                    echo json_encode($result);
                    exit;
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'Product Group', 'Save Quick Add New (Error)');
                    $result['error'] = 1;
                    echo json_encode($result);
                    exit;
                }
            }
        }
        $this->Helper->saveUserActivity($user['User']['id'], 'Product Group', 'Quick Add New');
        $companies = ClassRegistry::init('Company')->find('list', array('order' => 'id', 'conditions' => array('is_active' => 1, 'id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')')));
        $this->set(compact("companies"));
    }
    
    function addUom(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $this->loadModel('Uom');
            $result = array();
            if ($this->Helper->checkDouplicate('name', 'uoms', $this->data['Uom']['name'])) {
                $this->Helper->saveUserActivity($user['User']['id'], 'UoM', 'Save Quick Add New (Name has existed)');
                $result['error'] = 2;
                echo json_encode($result);
                exit;
            } else {
                Configure::write('debug', 0);
                $r = 0;
                $restCode = array();
                $dateNow  = date("Y-m-d H:i:s");
                $this->Uom->create();
                $this->data['Uom']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $this->data['Uom']['created']    = $dateNow;
                $this->data['Uom']['created_by'] = $user['User']['id'];
                $this->data['Uom']['is_active'] = 1;
                if ($this->Uom->save($this->data)) {
                    $error = mysql_error();
                    if($error != 'Invalid Data'){
                        $uomId = $this->Uom->id;
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($this->data['Uom'], 'uoms');
                        $restCode[$r]['modified'] = $dateNow;
                        $restCode[$r]['dbtodo']   = 'uoms';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                        // Send to E-Commerce
                        $e = 0;
                        $syncEco = array();
                        // Convert to REST
                        $syncEco[$e]['sys_code']  = $this->data['Uom']['sys_code'];
                        $syncEco[$e]['name']      = $this->data['Uom']['name'];
                        $syncEco[$e]['abbr']      = $this->data['Uom']['abbr'];
                        $syncEco[$e]['created']   = $dateNow;
                        $syncEco[$e]['dbtodo']    = 'uoms';
                        $syncEco[$e]['actodo']    = 'is';
                        // Save File Send to E-Commerce
                        $this->Helper->sendFileToSyncPublic($syncEco);
                        // UoM Conversion
                        if(!empty($this->data['UomConversion']['to_uom_id'])){
                            $this->loadModel('UomConversion');
                            $this->UomConversion->create();
                            $this->data['UomConversion']['from_uom_id'] = $uomId;
                            $this->data['UomConversion']['to_uom_id']   = $this->data['UomConversion']['to_uom_id'];
                            $this->data['UomConversion']['value']       = $this->Helper->replaceThousand($this->data['UomConversion']['value']);
                            $this->data['UomConversion']['created']     = $dateNow;
                            $this->data['UomConversion']['created_by']  = $user['User']['id'];
                            $this->data['UomConversion']['is_active']   = 1;
                            $this->data['UomConversion']['is_small_uom'] = 1;
                            if ($this->UomConversion->save($this->data)) {
                                $error = mysql_error();
                                if($error != 'Invalid Data'){
                                    // Convert to REST
                                    $restCode[$r] = $this->Helper->convertToDataSync($this->data['UomConversion'], 'uom_conversions');
                                    $restCode[$r]['modified']   = $dateNow;
                                    $restCode[$r]['dbtodo']     = 'uom_conversions';
                                    $restCode[$r]['actodo']     = 'is';
                                    $r++;
                                    if(!empty($this->data['other_uom'])){
                                        for($i = 0; $i < sizeof($this->data['other_uom']); $i++){
                                            $checkVal = abs($this->data['UomConversion']['value'] % $this->data['other_value'][$i]);
                                            if($this->data['other_value'][$i] > 0 && $this->data['other_value'][$i] != '' && $checkVal == 0 && ($this->data['other_value'][$i] <= $this->data['UomConversion']['value'])){
                                                $this->UomConversion->create();
                                                $otherUom = array();
                                                $otherUom['UomConversion']['from_uom_id'] = $uomId;
                                                $otherUom['UomConversion']['to_uom_id']   = $this->data['other_uom'][$i];
                                                $otherUom['UomConversion']['value']       = $this->Helper->replaceThousand($this->data['other_value'][$i]);
                                                $otherUom['UomConversion']['created']     = $dateNow;
                                                $otherUom['UomConversion']['created_by']  = $user['User']['id'];
                                                $otherUom['UomConversion']['is_active']   = 1;
                                                $this->UomConversion->saveAll($otherUom);
                                                // Convert to REST
                                                $restCode[$r] = $this->Helper->convertToDataSync($otherUom['UomConversion'], 'uom_conversions');
                                                $restCode[$r]['modified']   = $dateNow;
                                                $restCode[$r]['dbtodo']     = 'uom_conversions';
                                                $restCode[$r]['actodo']     = 'is';
                                                $r++;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        // Save File Send
                        $this->Helper->sendFileToSync($restCode, 0, 0);
                        // Save User Activity
                        $this->Helper->saveUserActivity($user['User']['id'], 'UoM', 'Save Quick Add New', $uomId);
                        $result['error']  = 0;
                        $result['option'] = '<option value="">'.INPUT_SELECT.'</option>';
                        $uoms = ClassRegistry::init('Uom')->find('all', array('order' => 'name', 'conditions' => array('is_active' => 1)));
                        foreach($uoms AS $uom){
                            $selected = '';
                            if($uom['Uom']['id'] == $uomId){
                                $selected = 'selected="selected"';
                            }
                            $result['option'] .= '<option value="'.$uom['Uom']['id'].'" '.$selected.'>'.$uom['Uom']['name'].'</option>';
                        }
                        echo json_encode($result);
                        exit;
                    } else {
                        $this->Helper->saveUserActivity($user['User']['id'], 'UoM', 'Save Quick Add New (Error '.$error.')');
                        $result['error'] = 1;
                        echo json_encode($result);
                        exit;
                    }
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'UoM', 'Save Quick Add New (Error)');
                    $result['error'] = 1;
                    echo json_encode($result);
                    exit;
                }
            }
        }
        $this->Helper->saveUserActivity($user['User']['id'], 'UoM', 'Quick Add New');
        $types = array(
            'Count' => 'Count',
            'Weight' => 'Weight',
            'Length' => 'Length',
            'Area' => 'Area',
            'Volume' => 'Volume',
            'Time' => 'Time'
        );
        $uomList = ClassRegistry::init('Uom')->find('list', array('conditions' => array('is_active != 2', 'Uom.id NOT IN (SELECT from_uom_id FROM `uom_conversions` WHERE is_active = 1)')));
        $this->set(compact("types", "uomList"));
    }
    
    function addBrand(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $this->loadModel('Brand');
            $result   = array();
            if ($this->Helper->checkDouplicate('name', 'brands', $this->data['Brand']['name'], 'is_active = 1')) {
                $this->Helper->saveUserActivity($user['User']['id'], 'Brand', 'Save Quick Add New (Name ready existed)');
                $result['error'] = 2;
                echo json_encode($result);
                exit;
            } else {
                $r = 0;
                $restCode  = array();
                $dateNow   = date("Y-m-d H:i:s");
                $this->Brand->create();
                $this->data['Brand']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $this->data['Brand']['created']    = $dateNow;
                $this->data['Brand']['created_by'] = $user['User']['id'];
                $this->data['Brand']['is_active']  = 1;
                if ($this->Brand->save($this->data)) {
                    $brandId = $this->Brand->id;
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($this->data['Brand'], 'brands');
                    $restCode[$r]['modified']   = $dateNow;
                    $restCode[$r]['dbtodo']     = 'brands';
                    $restCode[$r]['actodo']     = 'is';
                    $r++;
                    // Save File Send
                    $this->Helper->sendFileToSync($restCode, 0, 0);
                    // Save User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'Brand', 'Save Quick Add New', $brandId);
                    $result['error']  = 0;
                    $result['option'] = '<option value="">'.INPUT_SELECT.'</option>';
                    $brands = ClassRegistry::init('Brand')->find('all', array('order' => 'name', 'conditions' => array('is_active' => 1)));
                    foreach($brands AS $brand){
                        $selected = '';
                        if($brand['Brand']['id'] == $brandId){
                            $selected = 'selected="selected"';
                        }
                        $result['option'] .= '<option value="'.$brand['Brand']['id'].'" '.$selected.'>'.$brand['Brand']['name'].'</option>';
                    }
                    echo json_encode($result);
                    exit;
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'Product Group', 'Save Quick Add New (Error)');
                    $result['error'] = 1;
                    echo json_encode($result);
                    exit;
                }
            }
        }
        $this->Helper->saveUserActivity($user['User']['id'], 'Brand', 'Quick Add New');
    }
    
    function quickAdd() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $this->Product->create();
            if ($this->Helper->checkDouplicate('code', 'products', $this->data['Product']['code'], "company_id=".$this->data['Product']['company_id']." AND is_active = 1")) {
                // User Activity
                $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Save Quick Add New (Name ready existed)');
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $r = 0;
                $e = 0;
                $syncEco  = array();
                $restCode = array();
                $dateNow  = date("Y-m-d H:i:s");
                $smValUom = ClassRegistry::init('UomConversion')->find('first', array('fileds' => array('value'), 'order' => 'id', 'conditions' => array('from_uom_id' => $this->data['Product']['price_uom_id'], 'is_small_uom = 1', 'is_active' => 1)));
                if (!empty($smValUom)) {
                    $this->data['Product']['small_val_uom'] = $smValUom['UomConversion']['value'];
                } else {
                    $this->data['Product']['small_val_uom'] = 1;
                }
                if($this->data['Product']['code'] == ""){
                    $this->data['Product']['code'] = $this->data['Product']['barcode'];
                }
                $unitCost = $this->data['Product']['unit_cost'] != "" ? str_replace(",", "", $this->data['Product']['unit_cost']) : 0;
                $this->data['Product']['sys_code']        = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $this->data['Product']['default_cost']    = $unitCost;
                $this->data['Product']['unit_cost']       = $unitCost;
                $this->data['Product']['code']            = $this->data['Product']['barcode'];
                $this->data['Product']['reorder_level']   = 0;
                $this->data['Product']['created']         = $dateNow;
                $this->data['Product']['created_by']      = $user['User']['id'];
                $this->data['Product']['is_active']       = 1;
                if ($this->Product->save($this->data)) {
                    $lastInsertId = $this->Product->id;
                    // product main photo
                    if ($this->data['Product']['photo'] != '') {
                        $ext = pathinfo($this->data['Product']['photo'], PATHINFO_EXTENSION);
                        $photoName =  $lastInsertId . '_' . md5($this->data['Product']['photo']).".".$ext;
                        rename('public/product_photo/tmp/' . $this->data['Product']['photo'], 'public/product_photo/' . $photoName);
                        rename('public/product_photo/tmp/thumbnail/' . $this->data['Product']['photo'], 'public/product_photo/tmp/thumbnail/' . $photoName);
                        mysql_query("UPDATE products SET photo='" . $photoName . "' WHERE id=" . $lastInsertId);
                        $this->data['Product']['photo'] = $photoName;
                    }
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($this->data['Product'], 'products');
                    $restCode[$r]['modified'] = $dateNow;
                    $restCode[$r]['dbtodo']   = 'products';
                    $restCode[$r]['actodo']   = 'is';
                    $r++;
                    // Check Product Group Share
                    $checkShare = 2;
                    if (!empty($this->data['Product']['pgroup_id'])) {
                        $sqlShare = mysql_query("SELECT id FROM e_pgroup_shares WHERE pgroup_id = ".$this->data['Product']['pgroup_id']);
                        if(mysql_num_rows($sqlShare)){
                            $checkShare = 1;
                        }
                    }
                    // Send to E-Commerce
                    // Convert to REST
                    $shopSys = $this->Helper->getSQLSysCode("companies", $this->data['Product']['company_id']);
                    $syncEco[$e]['shop_id']   = $shopSys;
                    $syncEco[$e]['uom_id']    = $this->Helper->getSQLSysCode("uoms", $this->data['Product']['price_uom_id']);
                    $syncEco[$e]['sys_code']  = $this->data['Product']['sys_code'];
                    $syncEco[$e]['code']      = $this->data['Product']['code'];
                    $syncEco[$e]['barcode']   = $this->data['Product']['barcode'];
                    $syncEco[$e]['name']      = $this->data['Product']['name'];
                    $syncEco[$e]['description'] = $this->data['Product']['description'];
                    $syncEco[$e]['status']    = $checkShare;
                    $syncEco[$e]['created']   = $dateNow;
                    $syncEco[$e]['dbtodo']    = 'products';
                    $syncEco[$e]['actodo']    = 'is';
                    $e++;
                    if($checkShare == 1){
                        mysql_query("INSERT INTO `e_product_shares` (`company_id`, `product_id`, `created`, `created_by`) VALUES (".$this->data['Product']['company_id'].", ".$lastInsertId.", '".$dateNow."', ".$user['User']['id'].");");
                    }
                    // product group
                    if (!empty($this->data['Product']['pgroup_id'])) {
                        mysql_query("INSERT INTO product_pgroups (product_id, pgroup_id) VALUES ('".$lastInsertId."', '".$this->data['Product']['pgroup_id']."')");
                        // Convert to REST
                        $restCode[$r]['product_id'] = $this->data['Product']['sys_code'];
                        $restCode[$r]['pgroup_id']  = $this->Helper->getSQLSysCode("pgroups", $this->data['Product']['pgroup_id']);
                        $restCode[$r]['dbtodo']     = 'product_pgroups';
                        $restCode[$r]['actodo']     = 'is';
                        $r++;
                        // Convert to REST
                        $syncEco[$e]['product_id'] = $this->data['Product']['sys_code'];
                        $syncEco[$e]['pgroup_id']  = $this->Helper->getSQLSysCode("pgroups", $this->data['Product']['pgroup_id']);
                        $syncEco[$e]['dbtodo']     = 'product_pgroups';
                        $syncEco[$e]['actodo']     = 'is';
                        $e++;
                    }
                    // SKU of each UOM
                    if (!empty($this->data['sku_uom_value'])) {
                        for ($i = 0; $i < sizeof($this->data['sku_uom_value']); $i++) {
                            if ($this->data['sku_uom_value'][$i] != '' && $this->data['sku_uom'][$i] != '') {
                                mysql_query("INSERT INTO product_with_skus (product_id, sku, uom_id) VALUES ('" . $lastInsertId . "', '" . $this->data['sku_uom_value'][$i] . "', '" . $this->data['sku_uom'][$i] . "')");
                                // Convert to REST
                                $restCode[$r]['product_id'] = $this->data['Product']['sys_code'];
                                $restCode[$r]['sku']        = $this->data['sku_uom_value'][$i];
                                $restCode[$r]['uom_id']     = $this->Helper->getSQLSysCode("uoms", $this->data['sku_uom'][$i]);
                                $restCode[$r]['dbtodo']     = 'product_with_skus';
                                $restCode[$r]['actodo']     = 'is';
                                $r++;
                            }
                        }
                    }
//                    if (!empty($this->data['Product']['branch_id'])) {
//                        for ($i = 0; $i < sizeof($this->data['Product']['branch_id']); $i++) {
//                            mysql_query("INSERT INTO product_branches (product_id,branch_id) VALUES ('" . $lastInsertId . "','" . $this->data['Product']['branch_id'][$i] . "')");
//                            // Convert to REST
//                            $restCode[$r]['product_id'] = $this->data['Product']['sys_code'];
//                            $restCode[$r]['branch_id']  = $this->Helper->getSQLSysCode("branches", $this->data['Product']['branch_id'][$i]);
//                            $restCode[$r]['dbtodo']     = 'product_branches';
//                            $restCode[$r]['actodo']     = 'is';
//                            $r++;
//                        }
//                    }
                    $branches = ClassRegistry::init('Branch')->find("all", array("conditions" => array("Branch.is_active = 1")));
                    foreach($branches AS $branch){
                        mysql_query("INSERT INTO product_branches (product_id,branch_id) VALUES ('" . $lastInsertId . "','" . $branch['Branch']['id'] . "')");
                        // Convert to REST
                        $restCode[$r]['product_id'] = $this->data['Product']['sys_code'];
                        $restCode[$r]['branch_id']  = $this->Helper->getSQLSysCode("branches", $branch['Branch']['id']);
                        $restCode[$r]['dbtodo']     = 'product_branches';
                        $restCode[$r]['actodo']     = 'is';
                        $r++;
                    }
                    // Save File Send
                    $this->Helper->sendFileToSync($restCode, 0, 0);
                    // Save File Send to E-Commerce
                    $this->Helper->sendFileToSyncPublic($syncEco);
                    // Save User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Save Quick Add New', $lastInsertId);
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    // User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Save Quick Add New (Error)');
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        // User Activity
        $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Quick Add New');
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))), 'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $pgroups   = ClassRegistry::init('Pgroup')->find('list', array('order' => 'Pgroup.name', 'conditions' => array('Pgroup.is_active' => 1, 'Pgroup.id IN (SELECT pgroup_id FROM pgroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].'))')));
        $uoms      = ClassRegistry::init('Uom')->find("list", array("conditions" => array("Uom.is_active = 1"), "order" => "Uom.name"));
        $this->set(compact("companies", "branches", "uoms", "pgroups"));
    }
    
    function viewProductInventory($productId = null){
        $this->layout = 'ajax';
        if (!$productId) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $this->set(compact('productId'));
    }
}

?>