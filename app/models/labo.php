<?php
class Labo extends AppModel {
    var $name = 'Labo';
    var $belongsTo = array(
            'QueuedLabo' => array(
                            'className' => 'QueuedLabo',
                            'foreignKey' => 'queued_id',
                            'conditions' => '',
                            'fields' => '',
                            'order' => ''
            ),            
            'User' => array(
                            'className' => 'User',
                            'foreignKey' => '',
                            'conditions' => array('User.id = Labo.created_by'),
                            'fields' => '',
                            'order' => ''
            )

    );
    var $hasMany = array(
            'LaboRequest' => array(
                            'className' => 'LaboRequest',
                            'foreignKey' => 'labo_id',
                            'conditions' => array('LaboRequest.is_active != 2'),
                            'fields' => '',
                            'order' => '')
    );

    function getLaboByQueuedLaboId($qPatientId=null, $laboId=null) {       
        if($laboId!=""){
            $conditions=array(
                'conditions'=>array(
                        'Labo.queued_id'=>$qPatientId,
                        'Labo.id'=>$laboId
                )
            );
        }else{
            $conditions=array(
                    'conditions'=>array(
                            'Labo.queued_id'=>$qPatientId,
                    )
            );
        }        
        
        $listLabo = $this->find('first',$conditions);        
        return $listLabo;

    }
    
    function getLaboById($laboId) {
        $conditions=array(
                'conditions'=>array(
                        'Labo.id'=>$laboId
                )
        );
        $listLabo = $this->find('first',$conditions);
        return $listLabo;

    }
    
    function findByQueuedLaboId($qPatientId=null) {
        $conditions=array(
                'conditions'=>array(
                        'Labo.queued_id'=>$qPatientId,
                )
        );        
        $listLabo = $this->find('first',$conditions);
        if ($listLabo['Labo']['status']==1){
            return true;
        }else{
            return false;
        }
    }
    
     function getLaboByQueuedPatientId($qPatientId) {         
        $conditions=array(
                'conditions'=>array(
                        'Labo.queued_id'=>$qPatientId
                )
        );        
        $listLabo = $this->find('first',$conditions);
        return $listLabo;

    }
    
    function getQLabo() {
        $conditions = array(
                'conditions'=>array('Labo.status'=>1)
                ,'recursive' =>2
        );
        return $this->find('all', $conditions);
    }
//    function getQLabo() {
//        $conditions = array(
//                'conditions'=>array('Labo.status'=>1, "DAY(Labo.created) = DAY(NOW()) AND MONTH(Labo.created) = MONTH(NOW()) AND YEAR(Labo.created) = YEAR(NOW()) ")
//                ,'recursive' =>2
//        );
//        return $this->find('all', $conditions);
//    }
}
?>