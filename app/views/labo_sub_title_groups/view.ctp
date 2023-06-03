<style type="text/css">
    .yellow {color: #999933;}

    .body li{
        list-style:none;
        margin-left: 30px;
    }

</style>
<div id="dynamic">
    <div class="child">
        <h1 class="title"><?php echo $laboItemGroups[0]['LaboTitleGroup']['name']; ?></h1>
        <div class="body">
            <?php
            //debug($laboItemGroups);
            $laboCategoryCurrent = '';
            foreach($laboItemGroups AS $laboItemGroup) {
                if($laboCategoryCurrent != $laboItemGroup['LaboTitleGroup']['id']) {
                    echo "<div class='yellow'>{$laboItemGroup['LaboTitleGroup']['name']}</div>";
                    $laboCategoryCurrent = $laboItemGroup['LaboTitleGroup']['id'];
                }

                $laboItems = $this->requestAction('/labo_items/getLaboItem/'.$laboItemGroup['LaboTitleGroup']['id']);
                echo "<li>".str_pad($laboItemGroup['LaboItemGroup']['name'],170,'.',STR_PAD_RIGHT)."($ ".$laboItemGroup['LaboItemGroup']['price'].")";
                //debug($laboItems);
                foreach($laboItems as $laboItem) {
                    echo "<ul><li>".str_pad($laboItem['LaboItemGroup']['name'],170,'.',STR_PAD_RIGHT)."</li></ul>";
                }
                echo "</li>";
            }
            ?>
        </div>
    </div>
    <div class="child">
        <div class="buttons">
            <a href="<?php echo $this->base; ?>/labo_title_groups/index" class="negative">
                <img src="<?php echo $this->webroot; ?>img/button/cross.png" alt=""/>
                <?php echo ACTION_CANCEL; ?>
            </a>
        </div>
        <div style="clear: both;"></div>
    </div>
</div>
