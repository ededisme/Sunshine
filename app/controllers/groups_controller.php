<?php

class GroupsController extends AppController {

    var $name = 'Groups';
    var $components = array('Helper');

    function index() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        $this->Helper->saveUserActivity($user['User']['id'], 'Group', 'Dashboard');
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
        $this->Helper->saveUserActivity($user['User']['id'], 'Group', 'View', $id);
        $this->set('group', $this->Group->read(null, $id));
    }

    function add() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            if ($this->Helper->checkDouplicate('name', 'groups', $this->data['Group']['name'], 'is_active = 1')) {
                $this->Helper->saveUserActivity($user['User']['id'], 'Group', 'Save Add New (Name ready exsited)');
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $r = 0;
                $restCode = array();
                $dateNow  = date("Y-m-d H:i:s");
                $this->Group->create();
                $this->data['Group']['sys_code'] = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $this->data['Group']['created']  = $dateNow;
                $this->data['Group']['created_by'] = $user['User']['id'];
                $this->data['Group']['is_active']  = 1;
                if ($this->Group->save($this->data)) {
                    $lastInsertId=$this->Group->getLastInsertId();
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($this->data['Group'], 'groups');
                    $restCode[$r]['modified'] = $dateNow;
                    $restCode[$r]['dbtodo']   = 'groups';
                    $restCode[$r]['actodo']   = 'is';
                    $r++;
                    // user group
                    if(isset($this->data['Group']['user_id'])){
                        for($i=0;$i<sizeof($this->data['Group']['user_id']);$i++){
                            mysql_query("INSERT INTO user_groups (user_id,group_id) VALUES ('".$this->data['Group']['user_id'][$i]."','".$lastInsertId."')");
                            // Convert to REST
                            $restCode[$r]['user_id']  = $this->Helper->getSQLSysCode("users",$this->data['Group']['user_id'][$i]);
                            $restCode[$r]['group_id'] = $this->data['Group']['sys_code'];
                            $restCode[$r]['dbtodo']   = 'user_groups';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                        }
                    }
                    // permission
                    $queryModule=mysql_query("SELECT id FROM modules");
                    while($dataModule=mysql_fetch_array($queryModule)){
                        $module="module" . $dataModule['id'];
                        if(isset($_POST[$module])){
                            mysql_query("INSERT INTO permissions (group_id,module_id) VALUES ('".$lastInsertId."','".$dataModule['id']."')");
                            // Convert to REST
                            $restCode[$r]['module_id'] = $dataModule['id'];
                            $restCode[$r]['group_id']  = $this->data['Group']['sys_code'];
                            $restCode[$r]['dbtodo']    = 'permissions';
                            $restCode[$r]['actodo']    = 'is';
                            $r++;
                        }
                    }
                    // Save File Send
                    $this->Helper->sendFileToSync($restCode, 0, 0);
                    $this->Helper->saveUserActivity($user['User']['id'], 'Group', 'Save Add New', $lastInsertId);
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'Group', 'Save Add New (Error)');
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        $this->Helper->saveUserActivity($user['User']['id'], 'Group', 'Add New');
    }

    function edit($id = null) {
        $this->layout = 'ajax';
        if (!$id && empty($this->data)) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            if ($this->Helper->checkDouplicateEdit('name', 'groups', $id, $this->data['Group']['name'], 'is_active = 1')) {
                $this->Helper->saveUserActivity($user['User']['id'], 'Group', 'Save Edit (Name ready existed)', $id);
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $r = 0;
                $restCode = array();
                $dateNow  = date("Y-m-d H:i:s");
                $this->data['Group']['modified']    = $dateNow;
                $this->data['Group']['modified_by'] = $user['User']['id'];
                if ($this->Group->save($this->data)) {
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($this->data['Group'], 'groups');
                    $restCode[$r]['dbtodo'] = 'groups';
                    $restCode[$r]['actodo'] = 'ut';
                    $restCode[$r]['con']    = "sys_code = '".$this->data['Group']['sys_code']."'";
                    $r++;
                    // user group
                    mysql_query("DELETE FROM user_groups WHERE group_id=".$id);
                    // Convert to REST
                    $restCode[$r]['dbtodo'] = 'user_groups';
                    $restCode[$r]['actodo'] = 'dt';
                    $restCode[$r]['con']    = "group_id = ".$this->data['Group']['sys_code'];
                    $r++;
                    if(isset($this->data['Group']['user_id'])){
                        for($i=0;$i<sizeof($this->data['Group']['user_id']);$i++){
                            mysql_query("INSERT INTO user_groups (user_id,group_id) VALUES ('".$this->data['Group']['user_id'][$i]."','".$id."')");
                            // Convert to REST
                            $restCode[$r]['user_id']  = $this->Helper->getSQLSysCode("users",$this->data['Group']['user_id'][$i]);
                            $restCode[$r]['group_id'] = $this->data['Group']['sys_code'];
                            $restCode[$r]['dbtodo']   = 'user_groups';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                        }
                    }
                    // Permission
                    mysql_query("DELETE FROM permissions WHERE group_id=".$id);
                    // Convert to REST
                    $restCode[$r]['dbtodo'] = 'permissions';
                    $restCode[$r]['actodo'] = 'dt';
                    $restCode[$r]['con']    = "group_id = ".$this->data['Group']['sys_code'];
                    $r++;
                    $queryModule=mysql_query("SELECT id FROM modules");
                    while($dataModule=mysql_fetch_array($queryModule)){
                        $module="module" . $dataModule['id'];
                        if(isset($_POST[$module])){
                            mysql_query("INSERT INTO permissions (group_id,module_id) VALUES ('".$id."','".$dataModule['id']."')");
                            // Convert to REST
                            $restCode[$r]['module_id'] = $dataModule['id'];
                            $restCode[$r]['group_id']  = $this->data['Group']['sys_code'];
                            $restCode[$r]['dbtodo']    = 'permissions';
                            $restCode[$r]['actodo']    = 'is';
                            $r++;
                        }
                    }
                    // Save File Send
                    $this->Helper->sendFileToSync($restCode, 0, 0);
                    $this->Helper->saveUserActivity($user['User']['id'], 'Group', 'Save Edit', $id);
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'Group', 'Save Edit (Error)', $id);
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        if (empty($this->data)) {
            $this->Helper->saveUserActivity($user['User']['id'], 'Group', 'Edit', $id);
            $this->data = $this->Group->read(null, $id);
        }
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
        $group = $this->Group->read(null, $id);
        $this->Helper->saveUserActivity($user['User']['id'], 'Group', 'Delete');
        $this->Group->updateAll(
                array('Group.is_active' => "2"),
                array('Group.id' => $id)
        );
        // Convert to REST
        $restCode[$r]['is_active']  = 2;
        $restCode[$r]['modified']   = $dateNow;
        $restCode[$r]['modified_by'] = $user['User']['id'];
        $restCode[$r]['dbtodo']     = 'groups';
        $restCode[$r]['actodo']     = 'ut';
        $restCode[$r]['con']        = "sys_code = '".$group['Group']['sys_code']."'";
        // Save File Send
        $this->Helper->sendFileToSync($restCode, 0, 0);
        echo MESSAGE_DATA_HAS_BEEN_DELETED;
        exit;
    }

}

?>