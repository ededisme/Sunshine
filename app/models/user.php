<?php
class User extends AppModel {
    var $name = 'User';
   
    function getUserById($userId){
        $conditions = array(
            'conditions'=>array('User.id'=>$userId)
        );
        return $this->find('first',$conditions);
    }
}
?>