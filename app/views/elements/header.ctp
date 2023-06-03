<?php
//include("includes/function.php");
$sqlCom = mysql_query("SELECT id FROM companies WHERE is_active = 1;");
?>
<script type="text/javascript">
    var tabName="";
    
    function convertToSeparator(string){
        return string.toString().trim().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
    }
    
    function checkConnection(){
        var a = a||{};
        a.checkURL = window.location.href.replace('dashboards/index', 'users/connection');
        a.checkInterval = 8000;
        a.msgNot = "No Connection";
        a.msgCon = "Connected";
        getConnection(a);
    }
    
    function getConnection(a){
        var isCheck = 1;
        var modified = "";
        if (localStorage.getItem("modified") != null && localStorage.getItem("modified") != '[]' && localStorage.getItem("modified") != '') {
            modified = localStorage.getItem("modified");
        }
        $.ajax({
            type: "POST",
            dataType: "json",
            data: "data[modified]="+modified,
            url: a.checkURL,
            cache: !1,
            error: function() {
                isCheck = 0;
            },
            complete: function(){
                if(isCheck == 0){
                    isCheck = 1;
                    $("#connectWarning").css('background', '#FF0000').text(a.msgNot).show();
                }else{
                    $("#connectWarning").css('background', '#03C').text(a.msgCon).fadeOut(10000);
                }
            },
            success: function(result){
                if(jQuery.isEmptyObject(result)){
                    // Empty
                } else {
                    var objData  = JSON.stringify(result.Product);
                    var modified = result.modified;
                    localStorage.setItem("products", objData.toString());
                    localStorage.setItem("modified", modified);
                }
            }
        });
        setTimeout( function(){getConnection(a);},a.checkInterval);
    }
    
    function preventKeyEnter(){
        // Prevent Input Key Enter
        $("input[type='text']").keypress(function(e){
            if((e.which && e.which == 13) || e.keyCode == 13){
                return false;
            }
        });
    }
    
    function clearTmpTabs(){
        $("#tabs").tabs( "remove" ,$("#tabs").tabs("length")-1);
    }
    
    function replaceSlash(string){
        if(string != ""){
           string = string.toString().trim().replace(/\//g, '\\/');
        }else{
           string = "";
        }
        return string;
    }
    
    function replaceDoubleQuote(string){
        if(string != ""){
           string = string.toString().trim().replace(/"/g, '\\"');
        }else{
           string = "";
        }
        return string;
    }
    
    function replaceNum(str){
        if(str != "" && str != undefined && str != null){
            var str = parseFloat(str.toString().replace(/,/g,""));
        }else{
            var str = 0;
        }
        return str;
    }
    
    function converDicemalJS(value){
        return Math.round(parseFloat(value) * 1000000000)/1000000000;
    }
    
    function converDicemalRound(value){
        value = converDicemalJS(value * 1000);
        if(value.toString().match(/\./)){
            value = value.toString().split(".")[0];
        }
        value = converDicemalJS(parseFloat(value) / 1000);
        return value;
    }
    
    function calculateQtyDisplay(totalQty, labelMain, labelSmall, uomSmall){
        var totalRemain = "";
        var totalMain   = parseInt(parseInt(totalQty) / parseInt(uomSmall));
        var checkRemain = parseInt(parseInt(totalQty) % parseInt(uomSmall));
        if(checkRemain != 0){
            totalRemain = (parseInt(totalQty) - parseInt((totalMain * uomSmall)))+""+labelSmall;
        }
        return totalMain+""+labelMain+" "+totalRemain;
    }
    
    function checkFieldRecord(val){
        var result = true;
        if(val == "" || val == undefined || val == null){
            result = false;
        }
        return result;
    }
    
    // Set Cookie
    function setCookie(cookie, val){
        $.cookie(cookie, val, { expires: 7, path: "/" });
    }
    
    // Use Cookie
    function useCookie(obj, cookie){
        $(obj).val($.cookie(cookie));
    }
    // Alert Confirm Valid Code
    function alertConfirmValidCode(){
        $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_DATA_INVALID; ?></p>');
        $("#dialog").dialog({
            title: '<?php echo DIALOG_WARNING; ?>',
            resizable: false,
            modal: true,
            width: 'auto',
            height: 'auto',
            open: function(event, ui){
                $(".ui-dialog-buttonpane").show();
            },
            buttons: {
                '<?php echo ACTION_CLOSE; ?>': function() {
                    $(this).dialog("close");
                }
            }
        });
    }
    
    function createSysAct(mod, act, status, bug){
        var bugSend = bug.toString().replace(/&nbsp;/g, "").replace(/&gt;/g, "$"); 
        $.ajax({
            type:   "POST",
            url:    "<?php echo $this->base . '/'; ?>users/createSysAct/"+mod+"/"+act+"/"+status,
            data:   "bug="+bugSend
        });
    }
    
    function checkRequireField(fields){
        var result = true;
        if(fields.length > 0){
            $.each(fields, function(key, value) {
                if($("#"+value).val() == "" || $("#"+value).val() ==  null){
                    result = false;
                }
            });
        } else {
            result = false;
        }
        return result;
    }
    
    function checkRequireFieldMulti(fields){
        var result = true;
        if(fields.length > 0){
            $.each(fields, function(key, value) {
                if($("#"+value+"_chzn").find(".chzn-choices").find(".search-choice").find("span").text() ==  ""){
                    result = false;
                }
            });
        } else {
            result = false;
        }
        return result;
    }
    
    
     /*
     * Add missing content transformations.
     */
    function configureTransformations(evt) {
        var editor = evt.editor;

        editor.dataProcessor.htmlFilter.addRules({
            attributes: {
                style: function(value, element) {
                    // Return #RGB for background and border colors
                    return CKEDITOR.tools.convertRgbToHex(value);
                }
            }
        });

        // Default automatic content transformations do not yet take care of
        // align attributes on blocks, so we need to add our own transformation rules.
        function alignToAttribute(element) {
            if (element.styles[ 'text-align' ]) {
                element.attributes.align = element.styles[ 'text-align' ];
                delete element.styles[ 'text-align' ];
            }
        }
        editor.filter.addTransformations([
            [{element: 'p', right: alignToAttribute}],
            [{element: 'h1', right: alignToAttribute}],
            [{element: 'h2', right: alignToAttribute}],
            [{element: 'h3', right: alignToAttribute}],
            [{element: 'pre', right: alignToAttribute}]
        ]);
    }

    /*
     * Adjust the behavior of htmlWriter to make it output HTML like FCKeditor.
     */
    function configureHtmlWriter(evt) {
        var editor = evt.editor, dataProcessor = editor.dataProcessor;

        // Out self closing tags the HTML4 way, like <br>.
        dataProcessor.writer.selfClosingEnd = '>';

        // Make output formatting behave similar to FCKeditor.
        var dtd = CKEDITOR.dtd;
        for (var e in CKEDITOR.tools.extend({}, dtd.$nonBodyContent, dtd.$block, dtd.$listItem, dtd.$tableContent)) {
            dataProcessor.writer.setRules(e, {
                indent: true,
                breakBeforeOpen: true,
                breakAfterOpen: false,
                breakBeforeClose: !dtd[ e ][ '#' ],
                breakAfterClose: true
            });
        }
    }
    
    
    
    function alertSelectRequireField(){
        $("#dialog").html('<p style="color:red; font-size:14px;"><?php echo MESSAGE_COMFIRM_INPUT_ALL_REQUIREMENT; ?></p>');
        $("#dialog").dialog({
            title: '<?php echo DIALOG_INFORMATION; ?>',
            resizable: false,
            modal: true,
            closeOnEscape: false,
            width: 'auto',
            height: 'auto',
            position:'center',
            open: function(event, ui){
                $(".ui-dialog-buttonpane").show();
                $(".ui-dialog-titlebar-close").hide();
            },
            buttons: {
                '<?php echo ACTION_CLOSE; ?>': function() {
                    $(this).dialog("close");
                    $(".ui-dialog-titlebar-close").show();
                }
            }
        });
    }
    
    function getProductCache(){
        $.ajax({
            dataType: "json",
            url: "<?php echo $this->base; ?>/dashboards/getProductCache",
            data: "",
            success: function(result){
                if(jQuery.isEmptyObject(result)){
                    localStorage.setItem("products", "[]");
                } else {
                    var objData  = JSON.stringify(result.Product);
                    var modified = result.modified;
                    localStorage.setItem("products", objData.toString());
                    localStorage.setItem("modified", modified);
                }
            }
        });
    }
    
    
    function popUpViewHelp(mylink, windowname) { 
        if (! window.focus)
        return true; 
        var href; 
        if (typeof(mylink) == 'string') href=mylink; else href=mylink.href; 
        window.open(href, windowname, 'width=700,height=500,scrollbars=yes'); 
        return false; 
    }
    
    
    $(document).ready(function(){
        // Check Product Cache
        if (localStorage.getItem("products") == null || localStorage.getItem("products") == '[]' || localStorage.getItem("products") == '') {
            getProductCache();
        }
        // Check Connection
        checkConnection();
        // Check Company not yet create
        <?php
        if(!mysql_num_rows($sqlCom)){
        ?>
        var url   = "<?php echo $this->base?>/companies/index";
        var found = false;
        if(tabName != "<?php echo MENU_COMPANY_MANAGEMENT; ?>"){
            tabName = "<?php echo MENU_COMPANY_MANAGEMENT; ?>";
            $('#tabs a').not("[href=#]").each(function() {
                if("<?php echo MENU_COMPANY_MANAGEMENT; ?>" == "<?php echo MENU_DASHBOARD; ?>"){
                    found=true;
                    $("#tabs").tabs("select", 0);
                }else if(url == $.data(this, 'href.tabs')){
                    found=true;
                    $("#tabs").tabs("select", url);
                }
            });
            if(found==false){
                $("#tabs").tabs("add", url, "<?php echo MENU_COMPANY_MANAGEMENT; ?>");
            }
        }
        <?php
        }
        ?>
        $("#lang").change(function(){
            clearCookie = true;
            window.open('<?php echo $this->base; ?>/users/lang/' + $(this).val(), '_self');
        });
        $(".ajax").click(function(event){
            event.preventDefault();
            var obj=$(this);
            var found=false;
            if(tabName!=$(this).text()){
                tabName=$(this).text();
                $('#tabs a').not("[href=#]").each(function() {
                    if(obj.text()=="<?php echo MENU_DASHBOARD; ?>"){
                        found=true;
                        $("#tabs").tabs("select", 0);
                    }else if(obj.attr("href")==$.data(this, 'href.tabs')){
                        found=true;
                        $("#tabs").tabs("select", $(this).attr("href"));
                    }
                });
                if(found==false){
                    $("#tabs").tabs("add", $(this).attr("href"), $(this).text());
                }
            }
        });
        $("#btnViewWarning").click(function(){
            
        });
        // Action Logout
        $("#actionLogout").click(function(){
            $("#showWarning").hide();
        });
        // Function Option Hide/Show
        $.fn.showHideDropdownOptions = function(value, canShowOption) { 
            $(this).find('option[value="' + value + '"]').map(function () {
                return $(this).parent('span').length === 0 ? this : null;
            }).wrap('<span>').hide();

            if (canShowOption) {
                $(this).find('option[value="' + value + '"]').unwrap().show();
            } else {
                $(this).find('option[value="' + value + '"]').hide();
            }
       };
       
       // Function Option
       $.fn.filterOptions = function(objCompare, compare, selected) { 
            var object = $(this);
            // Hide by Filter
            object.find("option").removeAttr('selected');
            object.find("option").each(function(){
                if($(this).val() != '' && $(this).val() != 'all'){
                    var value = $(this).val();
                    var compareId = $(this).attr(objCompare).split(",");
                    if(compareId.indexOf(compare)==-1){
                        object.showHideDropdownOptions(value, false);
                    } else {
                        object.showHideDropdownOptions(value, true);
                    }
                }
            });
            // OPTION SELECTED
            object.find('option[value="'+selected+'"]').attr('selected', true);
       };
       
       // SYNC Module
       $("#syncModule").click(function(){
           $("#divSync").dialog({
                title: 'SYNC Module Login',
                resizable: false,
                modal: true,
                width: 530,
                height: 'auto',
                position: 'center',
                open: function(event, ui){
                    $(".ui-dialog-buttonpane").show();
                },
                buttons: {
                    'SYNC': function() {
                        var user = $("#syncUser").val();
                        var pwd  = $("#syncPwd").val();
                        var project  = $("#syncProject").val();
                        $.ajax({
                            type: "POST",
                            url: "<?php echo $this->base . '/users'; ?>/sync",
                            data: 'user='+user+'&pwd='+pwd+'&project='+project,
                            beforeSend: function(){
                                $("#syncProcess").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                            },
                            success: function(result){
                                $("#syncProcess").attr('src', '<?php echo $this->webroot; ?>img/button/cycle.png');
                            }
                        });
                        $(this).dialog("close");
                    }
                }
            });
       });
    });
</script>
<table style="width: 100%;height: 100%;" cellspacing="0">
    <tr style="vertical-align: top;height: 33px;">
        <td rowspan="2" style="width: 160px;"><img alt="" src="<?php echo $this->webroot; ?>img/logo_s.png" style="position: absolute; top: 3px; left: 10px; max-width: 200px; height: 100%; max-height: 80px;" /></td>
        <td style="text-align: left;vertical-align: top;padding-left: 10px;">
            <?php echo $this->element('menu'); ?>
        </td>
        <td style="text-align: right;vertical-align: top;">
            <div class="buttons" style="display: none;">
                <a href="#" class="positive" id="syncModule">
                    <img src="<?php echo $this->webroot; ?>img/button/cycle.png" id="syncProcess" />
                    Sync Module
                </a>
            </div>
            <?php echo GENERAL_WELCOME; ?> <?php echo $html->link($user['User']['first_name'].' '.$user['User']['last_name'],array('controller'=>'users','action'=>'profile'),array('class' => 'ajax')); ?>
            [ <?php echo $html->link(GENERAL_LOG_OUT,array('controller'=>'users','action'=>'logout', 'id' => 'actionLogout')); ?> ]
        </td>
    </tr>
    <tr style="background: url(<?php echo $this->webroot; ?>img/layout/line.gif);background-repeat: repeat-x;">
        <td style="vertical-align: top;">
            <div style="width: 49%; float: left; color: red; font-size: 14px;">
                <?php
                $expRemain = dateDiff(date("Y-m-d"), $user['User']['expired']);
                if($expRemain <= 15){
                    if($expRemain == 1){
                        $day = "in ".$expRemain." day";
                    } else if($expRemain == 0){
                        $day = "today";
                    }else{
                        $day = "in ".$expRemain." days";
                    }
                    echo "The current user will be expired ".$day.". <br/><span style='font-weight: normal; color:#3a99b3;'>Please contact us 023/081 881 887.</span>";
                }
                ?>
            </div>
            <div id="showWarning" style="width: 49%; float: right;">
                <div style="color: #fff; font-size: 16px; text-align: center; width: 100%; background: #FF0000; display: none;" id="connectWarning">No Connection</div>
            </div>
            <div style="clear: both;"></div>
            <div style="height: 10px;"></div>
        </td>
        <td style="vertical-align: top;">
            <table style="float: right;">
                <tr>
                    <td>
                        <select id="lang" class="chzn-select" style="width: 150px;">                            
                            <option value="kh" <?php echo $this->Session->read('lang')=='kh'?'selected="selected"':''; ?>>Khmer</option>
                            <option value="en" <?php echo $this->Session->read('lang')=='en'?'selected="selected"':''; ?>>English</option>
                        </select>
                    </td>
                    <td><img alt="" src="<?php echo $this->webroot; ?>img/layout/toolbox-divider.gif" align="absmiddle" /></td>
                    <td><img alt="" src="<?php echo $this->webroot; ?>img/button/safety.png" align="absmiddle" id="btnViewWarning" style="cursor: pointer; display: none;" /></td>
                    <td><img alt="" src="<?php echo $this->webroot; ?>img/layout/toolbox-divider.gif" align="absmiddle" /></td>
                    <td><img alt="" src="<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif" align="absmiddle" class="loader" /></td>
                    <td>
                        <a href="<?php echo $this->webroot; ?>img/EMR_Process_Follow.png" onClick="return popUpViewHelp(this, 'viewHelp')">
                            <img style="vertical-align: middle; padding: 8px; cursor: pointer;" onmouseover="Tip('EMR Follow')" class="uploadPreview" data="uploadPreview" src="<?php echo $this->webroot; ?>img/icon/question.png"/>
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<!-- DIV SYNC -->
<div id="divSync" style="display: none;">
    <table cellpadding="5" cellspacing="0" width="500" id="frmSyncLogin">
        <tr>
            <td style="width: 15%;">Username</td>
            <td><input type="text" id="syncUser" style="width: 90%;" /></td>
        </tr>
        <tr>
            <td style="width: 15%;">Password</td>
            <td><input type="password" id="syncPwd" style="width: 90%;" /></td>
        </tr>
        <tr>
            <td style="width: 15%;">Project</td>
            <td><input type="text" id="syncProject" style="width: 90%;" /></td>
        </tr>
    </table>
</div>