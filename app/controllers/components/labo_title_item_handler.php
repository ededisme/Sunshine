<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

/**
 * Description of Helper
 *
 * @author asus
 */



App::import('model','ExtendAppModel');
App::import('model','LaboItem');



class LaboTitleItemHandlerComponent extends Object {

    function getOptionsLaboItem($laboItemids) {
        $arrId = explode("," , $laboItemids);
        $laboItems = new LaboItem();
        $laboItems = $laboItems->find("list", array("conditions"=>array("LaboItem.is_active != 2 AND LaboItem.parent_id IS NULL")));
        return $this->optionsGeneration($laboItems, $arrId);
    }

    function optionsGeneration($laboItems, $arrId) {
        $options = array();
        foreach($laboItems as $key => $laboItem) {
            if (in_array($key, $arrId)) {
                array_push($options, array('id' => $key, "value" => $laboItem, "selected" => true));
            }else{
                array_push($options, array('id' => $key, "value" => $laboItem, "selected" => false));
            }
        }
        return $options;
    }
}

?>
