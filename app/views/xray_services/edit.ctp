<?php
echo $this->element('prevent_multiple_submit');
$absolute_url = FULL_BASE_URL . Router::url("/", false);
echo $javascript->link('uninums.min');
$tblNameRadom = "tbl" . rand();
?>
<script type="text/javascript">
    $(document).ready(function(){
        $("#dataDate" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
            yearRange: '-100:-0'
        }).unbind("blur");
        // Prevent Key Enter
        preventKeyEnter();
        $("#XrayServiceEditForm").validationEngine();
        $("#XrayServiceEditForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveXrayService").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $(".btnBackXrayService").click();
                // alert message
                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result+'</p>');
                $("#dialog").dialog({
                    title: '<?php echo DIALOG_INFORMATION; ?>',
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
        });
        
        $(".btnDelete").click(function(event){
            event.preventDefault();
            var rightPanel=$("#XrayServiceEditForm").parent();
            var id = $(this).attr('rel');
            var name = $(this).attr('name');
            $("#dialog").dialog('option', 'title', '<?php echo DIALOG_CONFIRMATION; ?>');
            $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_CONFIRM_DELETE; ?> ?</p>');
            $("#dialog").dialog({
                title: '<?php echo DIALOG_CONFIRMATION; ?>',
                resizable: false,
                modal: true,
                width: 'auto',
                height: 'auto',
                open: function(event, ui){
                    $(".ui-dialog-buttonpane").show();
                },
                buttons: {
                    '<?php echo ACTION_DELETE; ?>': function() {
                        $.ajax({
                            type: "GET",
                            url: "<?php echo $this->base.'/'.$this->params['controller']; ?>/deleteImage/" + id+"/"+name,
                            data: "",
                            beforeSend: function(){
                                $("#dialog").dialog("close");
                                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                            },
                            success: function(result){
                                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                oCache.iCacheLower = -1;
                                oTableXray.fnDraw(false);
                                // alert message
                                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result+'</p>');
                                $("#dialog").dialog({
                                    title: '<?php echo DIALOG_INFORMATION; ?>',
                                    resizable: false,
                                    modal: true,
                                    width: 'auto',
                                    height: 'auto',
                                    buttons: {
                                        '<?php echo ACTION_CLOSE; ?>': function() {
                                            $(this).dialog("close");
                                            rightPanel.load("<?php echo $absolute_url . $this->params['controller']; ?>/edit/<?php echo $this->params['pass'][0]; ?>");                                             
                                        }
                                    }
                                });
                            }
                        });
                    },
                    '<?php echo ACTION_CANCEL; ?>': function() {
                        $(this).dialog("close");
                    }
                }
            });
        });
        
        $('.btnDeleteImage').click(function(event){
            var id = $(this).attr('rel');
            var name = $(this).attr('name');
            if (confirm("Are you sure you want to delete "+name)) {
                $.ajax({
                    type: "GET",
                    url: "<?php echo $this->base.'/'.$this->params['controller']; ?>/deleteImage/" + id+"/"+name,
                    data: "",
                    beforeSend: function() {

                    },
                    success: function(msg) {
                        alert(msg); 
                        $('#Image_Xray_History_' + id).fadeOut(800, function() {
                            $('#Image_Xray_History_' + id).remove();
                        });
                    }
                });
            }
            return false;
        });
        $(".btnBackXrayService").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableXray.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
        
        //Hide Patinen Info
        $("#btnHidePatientInfo<?php echo $tblNameRadom;?>").click(function(){
            $("#patientInfo<?php echo $tblNameRadom;?>").hide(900);
            $("#showPatientInfo<?php echo $tblNameRadom;?>").show();
        });
        //Show Patinen Info
        $("#btnShowPatientInfo<?php echo $tblNameRadom;?>").click(function(){
            $("#patientInfo<?php echo $tblNameRadom;?>").show(900);
            $("#showPatientInfo<?php echo $tblNameRadom;?>").hide();
        });
        
    });
    
    
    // Editor English
    CKEDITOR.replace( 'XrayServiceDescription', {
            allowedContent:
                    'h1 h2 h3 p pre[align]; ' +
                    'blockquote code kbd samp var del ins cite q b i u strike ul ol li hr table tbody tr td th caption; ' +
                    'img[!src,alt,align,width,height]; font[!face]; font[!family]; font[!color]; font[!size]; font{!background-color}; a[!href]; a[!name]',
            coreStyles_bold: { element: 'b' },
            coreStyles_italic: { element: 'i' },
            coreStyles_underline: { element: 'u' },
            coreStyles_strike: { element: 'strike' },
            font_style: {
                    element: 'font',
                    attributes: { 'face': '#(family)' }
            },
            fontSize_sizes: 'xx-small/1;x-small/2;small/3;medium/4;large/5;x-large/6;xx-large/7',
            fontSize_style: {
                    element: 'font',
                    attributes: { 'size': '#(size)' }
            },
            colorButton_foreStyle: {
                    element: 'font',
                    attributes: { 'color': '#(color)' }
            },

            colorButton_backStyle: {
                    element: 'font',
                    styles: { 'background-color': '#(color)' }
            },
            stylesSet: [
                    { name: 'Computer Code', element: 'code' },
                    { name: 'Keyboard Phrase', element: 'kbd' },
                    { name: 'Sample Text', element: 'samp' },
                    { name: 'Variable', element: 'var' },
                    { name: 'Deleted Text', element: 'del' },
                    { name: 'Inserted Text', element: 'ins' },
                    { name: 'Cited Work', element: 'cite' },
                    { name: 'Inline Quotation', element: 'q' }
            ],
            uiColor: '#CCEAEE',
            on: {
                    pluginsLoaded: configureTransformations,
                    loaded: configureHtmlWriter
            }
    });
    
    CKEDITOR.replace( 'XrayServiceConclusion', {
            allowedContent:
                    'h1 h2 h3 p pre[align]; ' +
                    'blockquote code kbd samp var del ins cite q b i u strike ul ol li hr table tbody tr td th caption; ' +
                    'img[!src,alt,align,width,height]; font[!face]; font[!family]; font[!color]; font[!size]; font{!background-color}; a[!href]; a[!name]',
            coreStyles_bold: { element: 'b' },
            coreStyles_italic: { element: 'i' },
            coreStyles_underline: { element: 'u' },
            coreStyles_strike: { element: 'strike' },
            font_style: {
                    element: 'font',
                    attributes: { 'face': '#(family)' }
            },
            fontSize_sizes: 'xx-small/1;x-small/2;small/3;medium/4;large/5;x-large/6;xx-large/7',
            fontSize_style: {
                    element: 'font',
                    attributes: { 'size': '#(size)' }
            },
            colorButton_foreStyle: {
                    element: 'font',
                    attributes: { 'color': '#(color)' }
            },

            colorButton_backStyle: {
                    element: 'font',
                    styles: { 'background-color': '#(color)' }
            },
            stylesSet: [
                    { name: 'Computer Code', element: 'code' },
                    { name: 'Keyboard Phrase', element: 'kbd' },
                    { name: 'Sample Text', element: 'samp' },
                    { name: 'Variable', element: 'var' },
                    { name: 'Deleted Text', element: 'del' },
                    { name: 'Inserted Text', element: 'ins' },
                    { name: 'Cited Work', element: 'cite' },
                    { name: 'Inline Quotation', element: 'q' }
            ],
            uiColor: '#CCEAEE',
            on: {
                    pluginsLoaded: configureTransformations,
                    loaded: configureHtmlWriter
            }
    });

    // end document
    var nbRow = 1;
    $(function()
    {
        $('.btn-add').click(function(e)
        {            
            e.preventDefault();
            var controlForm = $('.controls'),
            currentEntry = $(this).parents('.entry:first'),
            newEntry = $(currentEntry.clone()).appendTo(controlForm);
            newEntry.find('img').each(function(){
            var id   = $(this).attr("data")+"_"+nbRow;
            $(this).attr("id",id);
            $(this).css("display","none");
        });
          
            newEntry.find('input').each(function(){
                  $(this).val('');
                var name = $(this).attr("data_name")+"[]";
                var id   = $(this).attr("data")+"_"+nbRow;
                var cls = $(this).attr("data");
                $(this).attr('rel', nbRow);
                $(this).attr('name', name);
                $(this).attr("id",id);
                $(this).attr("class",cls);
                controlForm.find('.entry:not(:first) .btn-add')
                .removeClass('btn-add').addClass('btn-remove')
                .html('<img style="width: 24px; height: 24px;" src="<?php echo $this->webroot; ?>img/button/trash.png" alt=""/>');
            remove();
            });
            
            nbRow++;
        });
        
    });
    function remove(){
        $(".btn-remove").unbind("click").click(function(){
           $(this).parents('.entry:last').remove();
            e.preventDefault();
            return false;
        });     
    }
    function popUpViewImage(mylink, windowname) {
        if (! window.focus)
            return true;
        var href;
        if (typeof(mylink) == 'string') href=mylink; else href=mylink.href;
        window.open(href, windowname, 'width=700,height=500,scrollbars=yes');
        return false;
    }

    function PreviewImage(clicked_id) {
        var id = $("#"+clicked_id).attr('rel');
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById(clicked_id).files[0]);
        oFReader.onload = function(oFREvent) {            
            if(id!=""){
                document.getElementById("uploadPreview_"+id).src = oFREvent.target.result;
                $("#uploadPreview_"+id).css("display","block");
            }else{
                document.getElementById("uploadPreview").src = oFREvent.target.result;
                $("#uploadPreview").css("display","block");
            }
        };
    }
</script>
<div style="padding: 5px;border: 1px dashed #3C69AD;">
    <div class="buttons">
        <a href="" class="positive btnBackXrayService">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('XrayService',array('enctype' => 'multipart/form-data')); ?>
<?php echo $this->Form->input('id'); ?>
<?php
foreach ($patient as $patient):  ?>
<input type="hidden" value="<?php echo $patient['XrayService']['id']; ?>" name="data[XrayService][id]">
<legend id="showPatientInfo<?php echo $tblNameRadom;?>" style="display:none;"><a href="#" id="btnShowPatientInfo<?php echo $tblNameRadom;?>" style="background: #CCCCCC; font-weight: bold;"><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?> [ Show ] </a> </legend>
<fieldset id="patientInfo<?php echo $tblNameRadom;?>" style="border: 1px dashed #3C69AD;">
    <legend><a href="#" id="btnHidePatientInfo<?php echo $tblNameRadom;?>" style="background: #CCCCCC; font-weight: bold;"> <?php echo MENU_PATIENT_MANAGEMENT_INFO; ?> [ Hide ] </a></legend>
    <div>
        <table class="info" style="width: 100%;">
            <tr>
                <th><?php echo PATIENT_CODE; ?></th>
                <td><?php echo $patient['Patient']['patient_code']; ?></td>
                <th><?php echo PATIENT_NAME; ?></th>
                <td><?php echo $patient['Patient']['patient_name']; ?></td>  
                <th><?php echo TABLE_AGE.'/'.TABLE_DOB;?> </th>
                <td>
                    <?php 
                    $then_ts = strtotime($patient['Patient']['dob']);
                    $then_year = date('Y', $then_ts);
                    $age = date('Y') - $then_year;
                    if (strtotime('+' . $age . ' years', $then_ts) > time())
                        $age--;

                    if ($age == 0) {
                        $then_year = date('m', $then_ts);
                        $month = date('m') - $then_year;
                        if (strtotime('+' . $month . ' month', $then_ts) > time())
                            $month--;
                        echo $month . ' ' . GENERAL_MONTH;
                    }else {
                        echo $age . ' ' . GENERAL_YEAR_OLD;
                    }
                    ?>
                </td>
                <th><?php echo TABLE_SEX; ?></th>
                <td>
                    <?php 
                        if($patient['Patient']['sex']=="M"){
                            echo 'Male';
                        }else{
                            echo 'Female';
                        }                        
                    ?>
                </td>
            </tr>
            <tr>
                <th><?php echo TABLE_NATIONALITY;?> </th>
                <td>
                    <?php
                    if ($patient['Patient']['patient_group_id'] != "") {
                        $query = mysql_query("SELECT name FROM patient_groups WHERE id=" . $patient['Patient']['patient_group_id']);
                        while ($row = mysql_fetch_array($query)) {
                            if ($patient['Patient']['patient_group_id'] == 1) {
                                echo $row['name'];
                            } else {
                                $queryNationality = mysql_query("SELECT name FROM nationalities WHERE id=".$patient['Patient']['nationality']);
                                while ($result = mysql_fetch_array($queryNationality)) {
                                    echo $row['name'] . '&nbsp;&nbsp;(' . $result['name'] . ')';
                                }
                            }
                        }
                    } else {
                        echo $patient['Nationality']['name'];
                    }
                    ?>
                </td>
                <th><?php echo TABLE_TELEPHONE;?> </th>
                <td><?php echo $patient['Patient']['telephone']; ?></td>
            </tr>
            <tr>
                <th><?php echo TABLE_ADDRESS;?> </th>
                <td colspan="5">
                    <?php 
                    if($patient['Patient']['address']!=""){
                        echo $patient['Patient']['address'];
                    }
                    if($patient['Patient']['location_id']!=""){
                        $query = mysql_query("SELECT name FROM patient_locations WHERE id=".$patient['Patient']['location_id']);
                        while ($row = mysql_fetch_array($query)) {
                            if($patient['Patient']['address']!=""){
                                echo ', ';
                            }
                            echo $row['name'];                
                        }
                    }
                    ?>
                </td>
            </tr>
            <tr>
                
            </tr>
        </table>
    </div>
</fieldset><br> 

<fieldset style="border: 1px dashed #3C69AD;">
    <legend style="background: #EF0931; font-weight: bold;"><?php __(GENERAL_REQUEST); ?></legend>
    <table style="width: 100%;" cellspacing="0">
        <tr>
            <td>
                <?php 
                    $queryDataFromDoctor=  mysql_query("SELECT xsreq.*,xsreq.id as id FROM xray_service_requests as xsreq "
                            . "INNER JOIN other_service_requests as osreq ON osreq.id=xsreq.other_service_request_id "
                            . "INNER JOIN queued_doctors as qd ON qd.id=osreq.queued_doctor_id "
                            . "INNER JOIN queues as q ON q.id=qd.queue_id WHERE osreq.is_active=1 AND queue_id=".$patient['XrayService']['xray_service_queue_id']);
                    $dataRequest=  mysql_fetch_array($queryDataFromDoctor);
                    echo $dataRequest['xray_description'];
                ?>
                <input type="hidden" value="<?php echo $dataRequest['id']; ?>" name="data[XrayServiceRequest][id]">
                <input type="hidden" value="<?php echo $patient['Queue']['id']; ?>" name="data[Queue][id]">
            </td>
        </tr>
    </table>      
</fieldset>
<br/>
<fieldset style="border: 1px dashed #3C69AD;">
    <legend><?php __(MENU_XRAY_SERVICE_INFO); ?></legend>
    <table style="width: 100%;" id="reload_img">
        <tr>
            <td><?php echo TABLE_DATE; ?></td>
            <td>:</td>
            <td>
                <input id="dataDate" name="data[XrayService][xray_date]" style="width:26%;" class ="validate[required]" value="<?php echo $patient['XrayService']['xray_date']; ?>">
                <input type="hidden" value="<?php echo $patient['XrayService']['id']; ?>" name="data[XrayService][id]">
            </td>
        </tr>
        <tr>
            <td><?php echo TABLE_DESCRIPTION; ?></td>
            <td>:</td>
            <td><?php echo $this->Form->textarea('description',array('name'=>'data[XrayService][description]','class'=>'mceEditor','style'=>'height:100px;width:999px;','value'=>$patient['XrayService']['description'])); ?></td>
        </tr>
        <tr>
            <td><?php echo TABLE_CONCLUSION; ?></td>
            <td>:</td>
            <td><?php echo $this->Form->textarea('conclusion',array('name'=>'data[XrayService][conclusion]','class'=>'mceEditor','style'=>'height:100px;width:999px;','value'=>$patient['XrayService']['conclusion'])); ?></td>
        </tr>
        <tr>
            <td valign="top"><?php echo TABLE_IMAGE; ?></td>
            <td style="padding-top:2px;" valign="top">:</td>
            <td>
                <?php $index=1; ?> 
                <?php
                $queryImage=  mysql_query("SELECT * FROM xray_service_images as xsim WHERE is_active=1 AND xray_srv_id=".$patient['XrayService']['id']);
                if(@mysql_num_rows($queryImage)){
                    while ($dataImage=  mysql_fetch_array($queryImage)){ ?>
                <div id="Image_Xray_History_<?php echo $dataImage['id'] ?>"> 
                    <a href="<?php echo $this->webroot; ?>img/x-ray/<?php echo $dataImage["src_name"]; ?> " onClick="return popUpViewImage(this, 'PdfImageAttchmentView') ">
                        <img id="uploadPreview_<?php echo 1000+$index?>" class="uploadPreview" style="width: 100px; height: 100px;" data="uploadPreview" src="<?php echo $this->webroot; ?>img/x-ray/<?php echo $dataImage["src_name"]; ?>"/>
                    </a>
                    <input id="uploadImage_<?php echo 1000+$index?>" value="<?php echo $dataImage["src_name"];?>" type="file" name="photos[]" onchange="PreviewImage(this.id);" rel="" class="uploadImage" data_name="photos" data="uploadImage" style="display:none;"/>
                    <input id="imageOld_<?php echo 1000+$index?>" type="hidden" value="<?php echo $dataImage["src_name"];?>" name="photo_old[]" rel="" />
                    <a class="buttons positive btnDeleteImage btn-remove" type="button" data="btn" id="btn" rel="<?php echo $dataImage["id"];?>" name="<?php echo $dataImage["src_name"];?>">
                        <img style="width: 24px; height: 24px;" src="<?php echo $this->webroot; ?>img/button/trash.png" alt=""/>
                    </a> 
                </div>
                <?php 
                $index++;
                    } 
                }
                ?>
                <div class="controls">                                                                                                                                         
                    <div class="entry">
                        <img id="uploadPreview" class="uploadPreview" style="width: 100px; height: 100px;" data="uploadPreview"/>
                        <input id="uploadImage" type="file" name="photos[]" onchange="PreviewImage(this.id);" rel="" class="uploadImage" data_name="photos" data="uploadImage" style="margin-left:104px;"/>
                        <a class="buttons positive btn-add" type="button" data="btn" id="btn">
                            <img style="width: 22px; height: 22px;" src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
                        </a>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</fieldset>
<br />
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <span class="txtSaveXrayService"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<div style="clear: both;"></div>
<?php endforeach; ?>
<?php echo $this->Form->end(); ?>