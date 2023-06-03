<?php
function getLaboCategoryName($id, $laboItemCategories){
    foreach ($laboItemCategories as $laboItemCategory){
        if($laboItemCategory['LaboItemCategory']['id']==$id){
            return $laboItemCategory['LaboItemCategory']['name'];
        }
    }
}
?>
