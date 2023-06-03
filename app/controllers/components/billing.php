<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Helper
 *
 * @author UDAYA
 */

class BillingComponent extends Object {

    var $components = array('Helper');

    function userLogin($username, $password){
        $result['status'] = 0;
        $result['info']   = "Login Failed";
        $url  = API_BILLING."system-login/login";
        $post = array(
            'securityCode' => CODE_BILLING,
            'typeId'    => 1,
            'username'  => $username,
            'password'  => $password,
        );
        $headers = array(
            'accept: */*'
        );
        // CURL
        $curl  = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $curlResp     = curl_exec($curl);
        $curl_errno   = curl_errno($curl);
        $curl_error   = curl_error($curl);
        curl_close ($curl);
        $response = json_decode($curlResp, true);
        if ($curl_errno > 0) {
            $result['status'] = 0;
            $result['info'] = "cURL Error ($curl_errno): $curl_error\n";
        } else {
            if($response['header']['result'] == true){
                if($response['body']['status'] == 1){
                    // Check Expired Date
                    if(strtotime($response['body']['expired']) > strtotime(date("Y-m-d"))){
                        $result['status'] = 1;
                        $result['info']   = "Login Success";
                    } else {
                        $result['status'] = 2;
                        $result['info']   = "Expired";
                    }
                    $result['exp']    = $response['body']['expired'];
                }
            }
        }
        $return = $result;
        return $return;
    }
    
    function userRetreive(){
        $result['status'] = 0;
        $result['info']   = "Retrieve Failed";
        $url  = API_BILLING."system-login/list";
        $post = array(
            'securityCode' => CODE_BILLING,
            'typeId' => 1
        );
        // CURL
        $curl  = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $curlResp   = curl_exec($curl);
        $curl_errno = curl_errno($curl);
        $curl_error = curl_error($curl);
        curl_close ($curl);
        $response = json_decode($curlResp, true);
        if ($curl_errno > 0) {
            $result['status'] = 0;
            $result['info']   = "cURL Error ($curl_errno): $curl_error\n";
        } else {
            if($response['header']['result'] == true){
                if(!empty($response['body'])){
                    foreach($response['body'] AS $user){
                        $active = 2;
                        if($user['isActive'] == 1){ // Active
                            $active = 1;
                        } else if($user['isActive'] == 2){ // Disactive
                            $active = 3;
                        }
                        // Check User Existed
                        $sqlCk = mysql_query("SELECT * FROM users WHERE sys_code = '".$user['sysCode']."' LIMIT 1");
                        if(mysql_num_rows($sqlCk)){
                            mysql_query("UPDATE users SET is_active = ".$active.", username = '".$user['username']."', password = '".$user['password']."', expired = '".$user['expired']."', first_name = '".$user['firstName']."', last_name = '".$user['lastName']."' WHERE sys_code = '".$user['sysCode']."' LIMIT 1");
                        } else {
                            mysql_query("INSERT INTO `users` (`sys_code`, `username`, `password`, `expired`, `first_name`, `last_name`, `is_hash`, `is_sync`, `created`, `created_by`, `modified`, `is_active`) 
                                         VALUES ('".$user['sysCode']."', '".$user['username']."', '".$user['password']."', '".$user['expired']."', '".$user['firstName']."', '".$user['lastName']."', 1, 1, '".date("Y-m-d H:i:s")."', 1, '".date("Y-m-d H:i:s")."', ".$active.");");
                        }
                    }
                    $result['status'] = 1;
                    $result['info']   = "Retrieve Success";
                }
            }
        }
        return $result;
    }
    
    function sendUserToApi($sysCode , $firstName, $lastName, $username, $password, $expired, $type, $price){
        $result['status'] = 0;
        $result['info']   = "Sync Failed";
        $url  = API_BILLING."system-login/sysn";
        $post = array(
            'securityCode' => CODE_BILLING,
            'sysCode' => $sysCode,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'username' => $username,
            'password' => $password,
            'expired' => $expired,
            'type'  => $type,
            'price' => $price
        );
        // CURL
        $curl  = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $curlResp   = curl_exec($curl);
        $curl_errno = curl_errno($curl);
        $curl_error = curl_error($curl);
        curl_close ($curl);
        $response = json_decode($curlResp, true);
        if ($curl_errno > 0) {
            $result['status'] = 0;
            $result['info']   = "cURL Error ($curl_errno): $curl_error\n";
        } else {
            if($response['header']['result'] == true){
                $result['status'] = 1;
                $result['info']   = $response['body'];
                // Update User Is Sync
                mysql_query("UPDATE users SET is_sync = 1 WHERE sys_code = '".$sysCode."'");
            }
        }
        return $result;
    }
    
    function updateUserApi($sysCode , $firstName, $lastName, $username, $password, $isActive){
        $result['status'] = 0;
        $result['info']   = "Sync Failed";
        $url  = API_BILLING."syn-update-user/update-profile";
        $post = array('securityCode' => CODE_BILLING,
            'sysCode'   => $sysCode,
            'firstName' => $firstName,
            'lastName'  => $lastName,
            'username'  => $username,
            'password'  => $password,
            'isActive'  => $isActive
        );
        // CURL
        $curl  = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $curlResp   = curl_exec($curl);
        $curl_errno = curl_errno($curl);
        $curl_error = curl_error($curl);
        curl_close ($curl);
        $response = json_decode($curlResp, true);
        if ($curl_errno > 0) {
            $result['status'] = 0;
            $result['info']   = "cURL Error ($curl_errno): $curl_error\n";
        } else {
            if($response['header']['result'] == true){
                $result['status'] = 1;
                $result['info']   = $response['body'];
            }
        }
        return $result;
    }

}

?>