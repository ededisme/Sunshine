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
App::import('model','LaboTitleItem');
App::import('model','LaboItemGroup');
App::import('model','ExtendAppModel');


class LaboTitleItemProcessComponent extends Object {

    function getLaboItemGroups($labos) {
        $laboItemGroup = array();
        foreach($labos['LaboRequest'] as $laboRequest) {
            array_push($laboItemGroup, $laboRequest["labo_item_group_id"]);
        }
        return $laboItemGroup;
    }

    function getLaboItems($groupLists) {
        $laboItemList = "";
        foreach($groupLists as $groupList) {
            $laboItemGroup = new LaboItemGroup();
            $laboItemGroups = $laboItemGroup->find("all", array("conditions"=>array("LaboItemGroup.id"=>$groupList)));
            foreach($laboItemGroups as $l) {
                $laboItemList.=$l['LaboItemGroup']['labo_item_id'].",";
            }
        }
        $laboItemList = substr($laboItemList, 0 , strlen($laboItemList)-1);
        return $laboItemList;
    }

    function getListLaboTitleItems($labos) {
        $groupLists = $this->getLaboItemGroups($labos);
        $labotItemLists = $this->getLaboItems($groupLists);
        $laboItemCategory = new LaboTitleItem();
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
                                            'LaboItem.title_item = LaboTitleItem.id',
                                    )
                            )
                    ),
                    "fields" => array("DISTINCT LaboTitleItem.*")
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
                                            'LaboItem.title_item = LaboTitleItem.id',
                                    )
                            )
                    ),
                    "fields" => array("DISTINCT LaboTitleItem.*")
                    )
            );
        }
        return $laboItemCategories;
    }


}

?>
