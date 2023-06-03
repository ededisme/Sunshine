<?php
echo $this->element('prevent_multiple_submit');
?>
<script type="text/javascript">
    $(document).ready(function(){
        // Set Cache
        localStorage.setItem("products", "[]");
        localStorage.setItem("modified", "");
        // Set Form Validate
        $("#UserLoginForm").validationEngine();
        // clear cookie
        $.cookie('cookieTitle', null, { expires: 7, path: "/" });
        $.cookie('cookieHref', null, { expires: 7, path: "/" });
        $.cookie('cookieTabIndex', null, { expires: 7, path: "/" });
        // Check & Focus
        if($("#UserUsername").val() != ''){
            $("#UserPassword").focus();
        } else {
            $("#UserUsername").focus();
        }
        
        $(".btnLogin").click(function(){
            var formName = "#UserLoginForm";
            var validateBack =$(formName).validationEngine("validate");
            if(!validateBack){
                return false;
            }else{
                $(".txtLogin").text('Loading...');
            }
        });
    });
</script>
<?php echo $this->Form->create('User', array('action' => 'login')); ?>
<input type="hidden" id="lat" name="data[User][lat]" />
<input type="hidden" id="long" name="data[User][long]" />
<input type="hidden" id="accuracy" name="data[User][accuracy]" />
<table cellpadding="8" cellspacing="0" width="100%">
    <tr>
        <th colspan="2" class="title">Membership Login</th>
    </tr>
    <tr>
        <td width="35%" align="right"><label for="UserUsername">Username:</label></td>
        <td width="65%"><input id="UserUsername" class="validate[required]" type="text" name="data[User][username]" /></td>
    </tr>
    <tr>
        <td align="right"><label for="UserPassword">Password:</label></td>
        <td align="left"><input id="UserPassword" class="validate[required]" type="password" name="data[User][password]" /></td>
    </tr>
    <?php if ($log >= 3) { ?>
    <tr>
        <td style="text-align: center;" colspan="2">
            <div style="width: 300px;display: inline-block;">
                <img alt="" id="secret" align="left" style="border: 0;" src="captcha/securimage_show_example.php?sid=<?php echo md5(time()) ?>" />
                <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="19" height="19" id="SecurImage_as3" align="middle">
                    <param name="allowScriptAccess" value="sameDomain" />
                    <param name="allowFullScreen" value="false" />
                    <param name="movie" value="captcha/securimage_play.swf?audio=captcha/securimage_play.php&bgColor1=#777&bgColor2=#fff&iconColor=#000&roundedCorner=5" />
                    <param name="quality" value="high" />
                    <param name="bgcolor" value="#ffffff" />
                    <param name="wmode" value="transparent" />
                    <embed src="captcha/securimage_play.swf?audio=captcha/securimage_play.php&bgColor1=#777&bgColor2=#fff&iconColor=#000&roundedCorner=5" quality="high" bgcolor="#ffffff" width="19" height="19" name="SecurImage_as3" align="middle" allowScriptAccess="sameDomain" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" wmode="transparent" />
                </object>
                <br />
                <a style="border-style: none" href="#" title="Refresh Image" onclick="document.getElementById('secret').src = 'captcha/securimage_show_example.php?sid=' + Math.random(); return false"><img src="<?php $this->webroot; ?>captcha/images/refresh.png" alt="Reload Image" border="0" onclick="this.blur()" align="bottom" /></a>
            </div>
            <div class="clearer"></div>
        </td>
    </tr>
    <tr>
        <td align="right"><label for="UserCode">Security Code:</label></td>
        <td><?php echo $form->text('code'); ?></td>
    </tr>
    <?php } ?>
    <tr>
        <td></td>
        <td>
            <div class="buttons">
                <button type="submit" class="positive btnLogin">
                    <img src="<?php echo $this->webroot; ?>img/button/textfield_key.png" alt=""/>
                    <span class="txtLogin">Login</span>
                </button>
            </div>
        </td>
    </tr>
</table>
<?php echo $this->Form->end(); ?>