<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/
/**
 * Description of Helper
 *
 * @author Hay sokhom
 */
App::import('model','Labo');
App::import('model','LaboItemCategory');
App::import('model','LaboItemGroup');
App::import('model','ExtendAppModel');


class LaboProcessComponent extends Object {

    function getLaboItemGroups($labos) {
        $laboItemGroup = array();
        foreach($labos['LaboRequest'] as $laboRequest) {
            array_push($laboItemGroup, $laboRequest["labo_item_group_id"]);
        }
        return $laboItemGroup;
    }

    function getLaboItems($groupLists) {
        $laboItemList = "";
        foreach ($groupLists as $groupList) {
            $laboItemGroup = new LaboItemGroup();
            $laboItemGroups = $laboItemGroup->find("all", array("conditions" => array("LaboItemGroup.id" => $groupList), 'order' => 'code ASC'));
            foreach ($laboItemGroups as $l) {
                if ($l['LaboItemGroup']['labo_item_id'] != "") {
                    $laboItemList.=$l['LaboItemGroup']['labo_item_id'] . ",";
                }
            }
        }
        $laboItemList = substr($laboItemList, 0, strlen($laboItemList) - 1);
        return $laboItemList;
    }
    
    function getListLaboItemRequest($labos) {
        $groupLists = $this->getLaboItemGroups($labos);
        $labotItemLists = $this->getLaboItems($groupLists);
        $laboItemCategory = new LaboItemCategory();
        return $labotItemLists;
    }

    function getListLaboItemCategories($labos) {
        $groupLists = $this->getLaboItemGroups($labos);
        $labotItemLists = $this->getLaboItems($groupLists);
        $laboItemCategory = new LaboItemCategory();
        if(trim($labotItemLists)!='') {
            $laboItemCategories = $laboItemCategory->find("all"
                    ,array(
                    "conditions" =>array("LaboItem.id in ($labotItemLists)"),
                    "joins"  =>array(
                            array(
                                    'table' => 'labo_items',
                                    'alias' => 'LaboItem',
                                    'type' => 'INNER',
                                    'conditions' => array(
                                            'LaboItem.category = LaboItemCategory.id',
                                    )
                            )
                    ),
                    "fields" => array("DISTINCT LaboItemCategory.*")
                    )
            );
        }else{
            $laboItemCategories = $laboItemCategory->find("all"
                    ,array(
                    "conditions" =>array("LaboItem.id = '$labotItemLists'"),
                    "joins"  =>array(
                            array(
                                    'table' => 'labo_items',
                                    'alias' => 'LaboItem',
                                    'type' => 'INNER',
                                    'conditions' => array(
                                            'LaboItem.category = LaboItemCategory.id',
                                    )
                            )
                    ),
                    "fields" => array("DISTINCT LaboItemCategory.*")
                    )
            );
        }
        return $laboItemCategories;
    }

    function getListLaboItemCategoriesPrint($labos,$category_id) {
        $groupLists = $this->getLaboItemGroups($labos);
        $labotItemLists = $this->getLaboItems($groupLists);
        $laboItemCategory = new LaboItemCategory();
        if(trim($labotItemLists)!='') {
            $laboItemCategories = $laboItemCategory->find("all"
                    ,array(
                    "conditions" =>array("LaboItem.id in ($labotItemLists)"),
                    "joins"  =>array(
                            array(
                                    'table' => 'labo_items',
                                    'alias' => 'LaboItem',
                                    'type' => 'INNER',
                                    'conditions' => array(
                                            'LaboItem.category = LaboItemCategory.id',
                                            'LaboItem.category'=>$category_id
                                    )
                            )
                    ),
                    "fields" => array("DISTINCT LaboItemCategory.*")
                    )
            );
        }else{
            $laboItemCategories = $laboItemCategory->find("all"
                    ,array(
                    "conditions" =>array("LaboItem.id = '$labotItemLists'"),
                    "joins"  =>array(
                            array(
                                    'table' => 'labo_items',
                                    'alias' => 'LaboItem',
                                    'type' => 'INNER',
                                    'conditions' => array(
                                            'LaboItem.category = LaboItemCategory.id',
                                            'LaboItem.category'=>$category_id
                                    )
                            )
                    ),
                    "fields" => array("DISTINCT LaboItemCategory.*")
                    )
            );
        }
        return $laboItemCategories;
    }
    
}

?>
