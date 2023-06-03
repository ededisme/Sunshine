<?php
class LaboItemGroup extends AppModel {
    var $name = 'LaboItemGroup';
    var $belongsTo = array(		
                'Company' => array(
			'className' => 'Company',
			'foreignKey' => 'company_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
        
	);
    
    function getLaboItemGroupDetail($id, $labo_item_id) {
        $sql = "SELECT *
                    FROM labo_item_groups AS LaboItemGroup
                    INNER JOIN labo_items AS LaboItem ON LaboItem.id in ($labo_item_id)
                    INNER JOIN labo_item_categories AS LaboItemCategory ON LaboItem.category = LaboItemCategory.id
                    WHERE LaboItemGroup.id = $id ORDER BY LaboItemCategory.id, LaboItem.id ";
        $result = $this->query($sql);
        return $result;
    }


}
?>