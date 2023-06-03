<?php
/**
 * Copyright UDAYA Technology Co,.LTD (http://www.udaya-tech.com)
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
include("includes/function.php");
$config = getSysconfig();
if(!empty($config)){
    $title = $config['title'];
    $start = $config['start'];
}else{
    $title = "";
    $start = "";
}
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        
        <title>
            <?php __('UT • '.$title); ?>
        </title>

        <!-- icon -->
        <link rel="shortcut icon" type="image/x-icon" href="<?php echo $this->webroot; ?>img/favicon.ico" />

        <!-- general stylesheet -->
        <link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/login.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" />

        <!-- jquery -->
        <script type="text/javascript" src="<?php echo $this->webroot; ?>js/jquery-1.7.min.js"></script>
        <script type="text/javascript" src="<?php echo $this->webroot; ?>js/jquery.cookie.js"></script>

        <!-- jquery ui -->
        <link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>js/jquery-ui-1.8.14.custom/development-bundle/themes/base/jquery.ui.all.css" />

        <!-- validator -->
        <link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>js/validateEngine/css/validationEngine.jquery.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>js/validateEngine/css/template.css" />
        <script type="text/javascript" src="<?php echo $this->webroot; ?>js/validateEngine/js/jquery.validationEngine-en.js"></script>
        <script type="text/javascript" src="<?php echo $this->webroot; ?>js/validateEngine/js/jquery.validationEngine.js"></script>
    </head>
    <body>
        <script type="text/javascript" src="<?php echo $this->webroot; ?>js/wz_tooltip_v4.js"></script>
        <center>
            <table style="height: 100%;">
                <tr>
                    <td style="vertical-align: middle;">
                        <div id="contain_login">
                            <div id="div_left">
                                <div id="img"><img alt="" src="<?php echo $this->webroot; ?>img/logo.png" style="height: 118px;" /></div>
                                <div class="clear"></div>
                                <div id="content">
                                    <div class="title">&nbsp;</div>
                                    <p>UT-Acc comes as a comprehensive solution for the efficient management and development of your accounting system. It will assist you in the complex and strategic process of managing this crucial transaction of your enterprise.</p><p>Based on modular architecture, it facilitates a vast range of account activities, with features that reflect the main accounting management activities. It comes as a web-enabled application and considering the available flexibility, UT-Acc is a perfect platform for reengineering your accounting processes and achieving a new level of accounting management.</p>
                                </div>
                            </div><!-- Left -->
                            <div id="div_right">
                                <div id="login">
                                    <?php echo $this->Session->flash(); ?>
                                    <?php echo $content_for_layout; ?>
                                </div><!-- login -->
                            </div><!-- Right -->
                        <div class="clear"></div>
                        </div>
                        <div id="div_footer">
                            &copy; Copyright <?php echo $start; ?><?php echo date("Y") != $start ? "-" . date("Y") : ""; ?> <?php echo $title; ?>. All rights reserved.
                            <div style="padding: 2px;"></div>
                            Powered by UDAYA Technology Co., Ltd.
                            <div style="padding: 2px;"></div>
                            <center><a href="http://www.udaya-tech.com/" target="_blank" style="text-decoration: none;"><div style="width: 85px;" onmouseover="Tip('<center><img alt=&quot;&quot; src=&quot;<?php echo $this->webroot; ?>img/udaya.png&quot; style=\'padding: 10px; height: 50px;\' /><table><tr><th style=\'text-align: left;\'>Tel/Fax:</th><td style=\'white-space: nowrap;\'>023 881 887/081 881 887</td></tr><tr><th style=\'text-align: left;\'>Email:</th><td style=\'white-space: nowrap;\'>info@udaya-tech.com</td></tr></table></center>', ABOVE, true, CENTERMOUSE, true)"><div style="float: left;padding: 5px;background: #008cbd;color: #FFF;width: 20px;font-weight: bold;">UT</div><div style="float: left;background: #FFF;font-size: 5px;">&nbsp;</div><div style="float: left;padding: 5px;background: #ea8326;color: #FFF;width: 40px;">Mini</div></div></a></center>
                        </div>
                    </td>
                </tr>
            </table>
        </center>
    </body>
</html>