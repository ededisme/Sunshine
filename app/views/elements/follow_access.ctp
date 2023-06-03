<?php

if (!function_exists('followAccess')) {

    function followAccess($type = null) {
        $queryFollowDoctor = mysql_query("SELECT status FROM follow_permissions WHERE type='".$type."'");
        while ($dataFollowDoctor = mysql_fetch_array($queryFollowDoctor)) {
            if (!empty($dataFollowDoctor)){
                return $dataFollowDoctor['status'];
            }
        }
    }
}
?>