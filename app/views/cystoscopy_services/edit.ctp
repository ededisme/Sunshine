<?php
echo $this->element('prevent_multiple_submit');
$absolute_url = FULL_BASE_URL . Router::url("/", false);
echo $javascript->link('uninums.min');
include("includes/function.php");
$tblNameRadom = "tbl" . rand();
?>
<style type="text/css">
    .input{
        float:left;
    }           
</style>
<script type="text/javascript">
    $(document).ready(function(){ 
        // Prevent Key Enter
        preventKeyEnter();
        $("#CystoscopyServiceEditForm").validationEngine();
        $("#CystoscopyServiceEditForm").ajaxForm({
            dataType: 'json',
            beforeSubmit: function(arr, $form, options) {
                $(".txtSave").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {                
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif"); 
                $(".btnBackQueueCystoscopyService").click();
                if(result.code == "1"){
                    errorSaveData();
                }else{                                        
                    $("#dialog").html('<div><br/><center><div class="buttons" style="display: inline-block;"><button type="submit" class="positive printPatientCystoscopyService" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="txtPrintInvoice"><?php echo ACTION_PRINT; ?></span></button></div></center></div>');
                    $(".printPatientCystoscopyService").click(function(){
                        $.ajax({
                            type: "POST",
                            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printCystoscopyService/"+result.id,                            
                            beforeSend: function(){
                                $(".loader").attr('src','<?php echo $this->webroot; ?>img/layout/spinner.gif');
                            },
                            success: function(printInvoiceResult){
                                w=window.open();
                                w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                                w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                                w.document.write(printInvoiceResult);
                                w.document.close();
                                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                            }
                        });
                    });
                    $("#dialog").dialog({
                        title: '<?php echo DIALOG_INFORMATION; ?>',
                        resizable: false,
                        modal: true,
                        width: '250',
                        height: '150',
                        open: function(event, ui){
                            $(".ui-dialog-buttonpane").show();
                        },
                        close: function(){
                            $(this).dialog({close: function(){}});
                            $(this).dialog("close");                            
                        },
                        buttons: {
                            '<?php echo ACTION_CLOSE; ?>': function() {
                                $(this).dialog("close");                                
                            }
                        }
                    });
                }                
            }
        });
        
        $(".btnBackQueueCystoscopyService").click(function(event) {
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableCystoscopy.fnDraw(false);
            var rightPanel = $(this).parent().parent().parent();
            var leftPanel = rightPanel.parent().find(".leftPanel");
            rightPanel.hide();
            rightPanel.html("");
            leftPanel.show("slide", {direction: "left"}, 500);
        });
        
        var dates = $("#startDate, #endDate").datepicker({
            timeFormat: 'hh:mm',
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            onSelect: function( selectedDate ) {
                var option = this.id == "startDate" ? "minDate" : "maxDate",
                    instance = $( this ).data( "datepicker" );
                    date = $.datepicker.parseDate(
                        instance.settings.dateFormat ||
                        $.datepicker._defaults.dateFormat,
                        selectedDate, instance.settings );
                dates.not( this ).datepicker( "option", option, date );
            }
        });
        
        //Hide Patinen Info
        $("#btnHidePatientInfo<?php echo $tblNameRadom;?>").click(function(){
            $("#patientInfo<?php echo $tblNameRadom;?>").hide();
            $("#showPatientInfo<?php echo $tblNameRadom;?>").show();
        });
        //Show Patinen Info
        $("#btnShowPatientInfo<?php echo $tblNameRadom;?>").click(function(){
            $("#patientInfo<?php echo $tblNameRadom;?>").show();
            $("#showPatientInfo<?php echo $tblNameRadom;?>").hide();
        });
        
    });
    
    function PreviewImage(clicked_id) {
        var id = $("#"+clicked_id).attr('rel');
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById(clicked_id).files[0]);
        oFReader.onload = function(oFREvent) {            
            if(id!=""){
                document.getElementById("uploadPreview"+id).src = oFREvent.target.result;
                $("#uploadPreview"+id).css("display","block");
            }else{
                document.getElementById("uploadPreview").src = oFREvent.target.result;
                $("#uploadPreview").css("display","block");
            }
        };
    };
    function errorSaveData(){
        $("#dialog").html('<p style="color:red; font-size:14px;"><?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?></p>');
        $("#dialog").dialog({
            title: '<?php echo DIALOG_INFORMATION; ?>',
            resizable: false,
            modal: true,
            width: 'auto',
            height: 'auto',
            position:'center',
            open: function(event, ui){
                $(".ui-dialog-buttonpane").show();
            },
            close: function(){
                $(this).dialog({close: function(){}});
                $(this).dialog("close");
                var rightPanel=$("#CystoscopyServiceEditForm").parent();
                var leftPanel=rightPanel.parent().find(".leftPanel");
                rightPanel.hide();rightPanel.html("");
                leftPanel.show("slide", { direction: "left" }, 500);
                oCache.iCacheLower = -1;
                oTableCystoscopy.fnDraw(false);
            },
            buttons: {
                '<?php echo ACTION_CLOSE; ?>': function() {
                    $(this).dialog("close");
                }
            }
        });
    }
    
    // Editor English
//    CKEDITOR.replace( 'CystoscopyServiceUrethra', {
//            allowedContent:
//                    'h1 h2 h3 p pre[align]; ' +
//                    'blockquote code kbd samp var del ins cite q b i u strike ul ol li hr table tbody tr td th caption; ' +
//                    'img[!src,alt,align,width,height]; font[!face]; font[!family]; font[!color]; font[!size]; font{!background-color}; a[!href]; a[!name]',
//            coreStyles_bold: { element: 'b' },
//            coreStyles_italic: { element: 'i' },
//            coreStyles_underline: { element: 'u' },
//            coreStyles_strike: { element: 'strike' },
//            font_style: {
//                    element: 'font',
//                    attributes: { 'face': '#(family)' }
//            },
//            fontSize_sizes: 'xx-small/1;x-small/2;small/3;medium/4;large/5;x-large/6;xx-large/7',
//            fontSize_style: {
//                    element: 'font',
//                    attributes: { 'size': '#(size)' }
//            },
//            colorButton_foreStyle: {
//                    element: 'font',
//                    attributes: { 'color': '#(color)' }
//            },
//
//            colorButton_backStyle: {
//                    element: 'font',
//                    styles: { 'background-color': '#(color)' }
//            },
//            stylesSet: [
//                    { name: 'Computer Code', element: 'code' },
//                    { name: 'Keyboard Phrase', element: 'kbd' },
//                    { name: 'Sample Text', element: 'samp' },
//                    { name: 'Variable', element: 'var' },
//                    { name: 'Deleted Text', element: 'del' },
//                    { name: 'Inserted Text', element: 'ins' },
//                    { name: 'Cited Work', element: 'cite' },
//                    { name: 'Inline Quotation', element: 'q' }
//            ],
//            uiColor: '#CCEAEE',
//            on: {
//                    pluginsLoaded: configureTransformations,
//                    loaded: configureHtmlWriter
//            }
//    });
//    CKEDITOR.replace( 'CystoscopyServiceProstate', {
//            allowedContent:
//                    'h1 h2 h3 p pre[align]; ' +
//                    'blockquote code kbd samp var del ins cite q b i u strike ul ol li hr table tbody tr td th caption; ' +
//                    'img[!src,alt,align,width,height]; font[!face]; font[!family]; font[!color]; font[!size]; font{!background-color}; a[!href]; a[!name]',
//            coreStyles_bold: { element: 'b' },
//            coreStyles_italic: { element: 'i' },
//            coreStyles_underline: { element: 'u' },
//            coreStyles_strike: { element: 'strike' },
//            font_style: {
//                    element: 'font',
//                    attributes: { 'face': '#(family)' }
//            },
//            fontSize_sizes: 'xx-small/1;x-small/2;small/3;medium/4;large/5;x-large/6;xx-large/7',
//            fontSize_style: {
//                    element: 'font',
//                    attributes: { 'size': '#(size)' }
//            },
//            colorButton_foreStyle: {
//                    element: 'font',
//                    attributes: { 'color': '#(color)' }
//            },
//
//            colorButton_backStyle: {
//                    element: 'font',
//                    styles: { 'background-color': '#(color)' }
//            },
//            stylesSet: [
//                    { name: 'Computer Code', element: 'code' },
//                    { name: 'Keyboard Phrase', element: 'kbd' },
//                    { name: 'Sample Text', element: 'samp' },
//                    { name: 'Variable', element: 'var' },
//                    { name: 'Deleted Text', element: 'del' },
//                    { name: 'Inserted Text', element: 'ins' },
//                    { name: 'Cited Work', element: 'cite' },
//                    { name: 'Inline Quotation', element: 'q' }
//            ],
//            uiColor: '#CCEAEE',
//            on: {
//                    pluginsLoaded: configureTransformations,
//                    loaded: configureHtmlWriter
//            }
//    });
//    CKEDITOR.replace( 'CystoscopyServiceBladderNeck', {
//            allowedContent:
//                    'h1 h2 h3 p pre[align]; ' +
//                    'blockquote code kbd samp var del ins cite q b i u strike ul ol li hr table tbody tr td th caption; ' +
//                    'img[!src,alt,align,width,height]; font[!face]; font[!family]; font[!color]; font[!size]; font{!background-color}; a[!href]; a[!name]',
//            coreStyles_bold: { element: 'b' },
//            coreStyles_italic: { element: 'i' },
//            coreStyles_underline: { element: 'u' },
//            coreStyles_strike: { element: 'strike' },
//            font_style: {
//                    element: 'font',
//                    attributes: { 'face': '#(family)' }
//            },
//            fontSize_sizes: 'xx-small/1;x-small/2;small/3;medium/4;large/5;x-large/6;xx-large/7',
//            fontSize_style: {
//                    element: 'font',
//                    attributes: { 'size': '#(size)' }
//            },
//            colorButton_foreStyle: {
//                    element: 'font',
//                    attributes: { 'color': '#(color)' }
//            },
//
//            colorButton_backStyle: {
//                    element: 'font',
//                    styles: { 'background-color': '#(color)' }
//            },
//            stylesSet: [
//                    { name: 'Computer Code', element: 'code' },
//                    { name: 'Keyboard Phrase', element: 'kbd' },
//                    { name: 'Sample Text', element: 'samp' },
//                    { name: 'Variable', element: 'var' },
//                    { name: 'Deleted Text', element: 'del' },
//                    { name: 'Inserted Text', element: 'ins' },
//                    { name: 'Cited Work', element: 'cite' },
//                    { name: 'Inline Quotation', element: 'q' }
//            ],
//            uiColor: '#CCEAEE',
//            on: {
//                    pluginsLoaded: configureTransformations,
//                    loaded: configureHtmlWriter
//            }
//    });
//    CKEDITOR.replace( 'CystoscopyServiceBladder', {
//            allowedContent:
//                    'h1 h2 h3 p pre[align]; ' +
//                    'blockquote code kbd samp var del ins cite q b i u strike ul ol li hr table tbody tr td th caption; ' +
//                    'img[!src,alt,align,width,height]; font[!face]; font[!family]; font[!color]; font[!size]; font{!background-color}; a[!href]; a[!name]',
//            coreStyles_bold: { element: 'b' },
//            coreStyles_italic: { element: 'i' },
//            coreStyles_underline: { element: 'u' },
//            coreStyles_strike: { element: 'strike' },
//            font_style: {
//                    element: 'font',
//                    attributes: { 'face': '#(family)' }
//            },
//            fontSize_sizes: 'xx-small/1;x-small/2;small/3;medium/4;large/5;x-large/6;xx-large/7',
//            fontSize_style: {
//                    element: 'font',
//                    attributes: { 'size': '#(size)' }
//            },
//            colorButton_foreStyle: {
//                    element: 'font',
//                    attributes: { 'color': '#(color)' }
//            },
//
//            colorButton_backStyle: {
//                    element: 'font',
//                    styles: { 'background-color': '#(color)' }
//            },
//            stylesSet: [
//                    { name: 'Computer Code', element: 'code' },
//                    { name: 'Keyboard Phrase', element: 'kbd' },
//                    { name: 'Sample Text', element: 'samp' },
//                    { name: 'Variable', element: 'var' },
//                    { name: 'Deleted Text', element: 'del' },
//                    { name: 'Inserted Text', element: 'ins' },
//                    { name: 'Cited Work', element: 'cite' },
//                    { name: 'Inline Quotation', element: 'q' }
//            ],
//            uiColor: '#CCEAEE',
//            on: {
//                    pluginsLoaded: configureTransformations,
//                    loaded: configureHtmlWriter
//            }
//    });
//    CKEDITOR.replace( 'CystoscopyServiceAfterFiveMinute', {
//            allowedContent:
//                    'h1 h2 h3 p pre[align]; ' +
//                    'blockquote code kbd samp var del ins cite q b i u strike ul ol li hr table tbody tr td th caption; ' +
//                    'img[!src,alt,align,width,height]; font[!face]; font[!family]; font[!color]; font[!size]; font{!background-color}; a[!href]; a[!name]',
//            coreStyles_bold: { element: 'b' },
//            coreStyles_italic: { element: 'i' },
//            coreStyles_underline: { element: 'u' },
//            coreStyles_strike: { element: 'strike' },
//            font_style: {
//                    element: 'font',
//                    attributes: { 'face': '#(family)' }
//            },
//            fontSize_sizes: 'xx-small/1;x-small/2;small/3;medium/4;large/5;x-large/6;xx-large/7',
//            fontSize_style: {
//                    element: 'font',
//                    attributes: { 'size': '#(size)' }
//            },
//            colorButton_foreStyle: {
//                    element: 'font',
//                    attributes: { 'color': '#(color)' }
//            },
//
//            colorButton_backStyle: {
//                    element: 'font',
//                    styles: { 'background-color': '#(color)' }
//            },
//            stylesSet: [
//                    { name: 'Computer Code', element: 'code' },
//                    { name: 'Keyboard Phrase', element: 'kbd' },
//                    { name: 'Sample Text', element: 'samp' },
//                    { name: 'Variable', element: 'var' },
//                    { name: 'Deleted Text', element: 'del' },
//                    { name: 'Inserted Text', element: 'ins' },
//                    { name: 'Cited Work', element: 'cite' },
//                    { name: 'Inline Quotation', element: 'q' }
//            ],
//            uiColor: '#CCEAEE',
//            on: {
//                    pluginsLoaded: configureTransformations,
//                    loaded: configureHtmlWriter
//            }
//    });
//    CKEDITOR.replace( 'CystoscopyServiceConclusion', {
//            allowedContent:
//                    'h1 h2 h3 p pre[align]; ' +
//                    'blockquote code kbd samp var del ins cite q b i u strike ul ol li hr table tbody tr td th caption; ' +
//                    'img[!src,alt,align,width,height]; font[!face]; font[!family]; font[!color]; font[!size]; font{!background-color}; a[!href]; a[!name]',
//            coreStyles_bold: { element: 'b' },
//            coreStyles_italic: { element: 'i' },
//            coreStyles_underline: { element: 'u' },
//            coreStyles_strike: { element: 'strike' },
//            font_style: {
//                    element: 'font',
//                    attributes: { 'face': '#(family)' }
//            },
//            fontSize_sizes: 'xx-small/1;x-small/2;small/3;medium/4;large/5;x-large/6;xx-large/7',
//            fontSize_style: {
//                    element: 'font',
//                    attributes: { 'size': '#(size)' }
//            },
//            colorButton_foreStyle: {
//                    element: 'font',
//                    attributes: { 'color': '#(color)' }
//            },
//
//            colorButton_backStyle: {
//                    element: 'font',
//                    styles: { 'background-color': '#(color)' }
//            },
//            stylesSet: [
//                    { name: 'Computer Code', element: 'code' },
//                    { name: 'Keyboard Phrase', element: 'kbd' },
//                    { name: 'Sample Text', element: 'samp' },
//                    { name: 'Variable', element: 'var' },
//                    { name: 'Deleted Text', element: 'del' },
//                    { name: 'Inserted Text', element: 'ins' },
//                    { name: 'Cited Work', element: 'cite' },
//                    { name: 'Inline Quotation', element: 'q' }
//            ],
//            uiColor: '#CCEAEE',
//            on: {
//                    pluginsLoaded: configureTransformations,
//                    loaded: configureHtmlWriter
//            }
//    });
</script>
<div style="padding: 5px;border: 1px dashed #3C69AD;">
    <div class="buttons">
        <a href="" class="positive btnBackQueueCystoscopyService">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('CystoscopyService',array('enctype' => 'multipart/form-data')); ?>
<?php echo $this->Form->input('id', array('value' => $patient['CystoscopyService']['id'])); ?>

<legend id="showPatientInfo<?php echo $tblNameRadom;?>" style="display:none;"><a href="#" id="btnShowPatientInfo<?php echo $tblNameRadom;?>" style="background: #CCCCCC; font-weight: bold;"><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?> [ Show ] </a> </legend>
<fieldset id="patientInfo<?php echo $tblNameRadom;?>" style="border: 1px dashed #3C69AD;">
    <legend><a href="#" id="btnHidePatientInfo<?php echo $tblNameRadom;?>" style="background: #CCCCCC; font-weight: bold;"> <?php echo MENU_PATIENT_MANAGEMENT_INFO; ?> [ Hide ] </a></legend>
    <table style="width: 100%;" cellspacing="3">
        <tr>
            <td style="width: 15%;"><?php echo PATIENT_CODE; ?> :</td>
            <td style="width: 35%;"><?php echo $patient['Patient']['patient_code']; ?></td>
            <td style="width: 15%;">
                <?php echo TABLE_DOB; ?> :</td>
            <td style="width: 35%;">
                <?php echo date("d/m/Y", strtotime($patient['Patient']['dob'])); ?>
                <?php echo TABLE_AGE; ?> :
                <?php
                echo getAgePatient($patient['Patient']['dob']);               
                ?>                
            </td>
        </tr>
        <tr>
            <td style="width: 15%;"><?php echo PATIENT_NAME; ?> :</td>
            <td>
                <?php echo $patient['Patient']['patient_name']; ?>
            </td>
            <td style="width: 15%;"><?php echo TABLE_NATIONALITY; ?> :</td>
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
        </tr>      
        <tr>
            <td style="width: 15%;"><?php echo TABLE_SEX; ?> :</td>
            <td>
                <?php
                if ($patient['Patient']['sex'] == "F") {
                    echo GENERAL_FEMALE;
                } else {
                    echo GENERAL_MALE;
                }
                ?>
            </td>
            <td style="width: 15%;"><?php echo TABLE_EMAIL; ?> :</td>
            <td>
                <?php echo $patient['Patient']['email']; ?>
            </td>            
        </tr>
        <tr>            
            <td style="width: 15%;"><?php echo TABLE_OCCUPATION; ?> :</td>
            <td>
                <?php echo $patient['Patient']['occupation']; ?>
            </td>
            <td style="width: 15%;"><?php echo TABLE_TELEPHONE; ?>:</td>
            <td>
                <?php echo $patient['Patient']['telephone']; ?>
            </td>
        </tr>        
        <tr>
            <td style="width: 15%;"><?php echo TABLE_ADDRESS; ?> :</td>
            <td>
                <?php echo $patient['Patient']['address']; ?>
            </td>
            <td style="width: 15%;"><?php echo TABLE_CITY_PROVINCE; ?> :</td>
            <td>
                <?php                
                if($patient['Patient']['location_id']!=""){
                    $query = mysql_query("SELECT name FROM patient_locations WHERE id=" . $patient['Patient']['location_id']);
                    while ($row = mysql_fetch_array($query)) {
                        echo $row['name'];
                    }
                }
                ?>
            </td>
        </tr>
    </table>     
</fieldset>
<br/>
<fieldset style="border: 1px dashed #3C69AD;">
    <legend style="background: #EF0931; font-weight: bold;"><?php __(GENERAL_REQUEST); ?></legend>
    <table style="width: 100%;" cellspacing="0">
        <tr>
            <td>
                <?php 
                    $queryDataFromDoctor =  mysql_query("SELECT * FROM cystoscopy_service_requests WHERE id=".$patient['CystoscopyService']['cystoscopy_service_request_id']);
                    $dataRequest=  mysql_fetch_array($queryDataFromDoctor);
                    echo $dataRequest['cystoscopy_description'];
                ?>
                <input type="hidden" value="<?php echo $dataRequest['id']; ?>" name="data[CystoscopyServiceRequest][id]">
            </td>
        </tr>
    </table>      
</fieldset>
<br/>
<fieldset style="border: 1px dashed #3C69AD;">
    <legend><?php __(MENU_XRAY_SERVICE_INFO); ?></legend>
    <table style="width: 100%;">
        <tr>
            <td><?php echo TABLE_DESCRIPTION; ?> <span class="red">*</span></td>
            <td>:</td>
            <td colspan="2"><input type="text" id="descriptBeforeSdate" name="data[CystoscopyService][descript_before_sdate]" style="width:26%;" value="<?php echo $patient['CystoscopyService']['descript_before_sdate']; ?>" class ="validate[required]"></td>
        </tr>
        <tr>
            <td><?php echo TABLE_START_DATE; ?></td>
            <td>:</td>
            <td colspan="2"><input type="text" id="startDate" name="data[CystoscopyService][start_date]" style="width:26%;" value="<?php echo $patient['CystoscopyService']['start_date']; ?>" ></td>
        </tr>
        <tr>
            <td><?php echo TABLE_END_DATE; ?> <span class="red">*</span></td>
            <td>:</td>
            <td colspan="2"><input type="text" id="endDate" name="data[CystoscopyService][end_date]" style="width:26%;" value="<?php echo $patient['CystoscopyService']['end_date']; ?>" class ="validate[required]"></td>
        </tr>
        <tr>            
            <td><?php echo 'Urethra'; ?></td>
            <td>:</td>
            <td style="width: 200px;">
                <img id="uploadPreview1" src="<?php echo $this->webroot; ?>img/cystoscopy/<?php echo $patient['CystoscopyService']["urethra_img"]; ?>" class="uploadPreview" style="width: 120px; height: 120px;" data="uploadPreview1"/>
                <br/><br/>
                <input id="uploadImage1" type="file" name="urethra_img" onchange="PreviewImage(this.id);" rel="1" class="uploadImage" data_name="photos" data="uploadImage"/>
            </td>
            <td><?php echo $this->Form->textarea('urethra',array('name'=>'data[CystoscopyService][urethra]', 'value' => $patient['CystoscopyService']['urethra'], 'class'=>'mceEditor','style'=>'height:100px;width:650px;')); ?></td>
        </tr>
        <tr style="<?php if ($patient['Patient']['sex'] == "F") { echo 'display:none;';}?>">            
            <td><?php echo 'Prostate'; ?></td>
            <td>:</td>
            <td style="width: 200px;">
                <img id="uploadPreview5" src="<?php echo $this->webroot; ?>img/cystoscopy/<?php echo $patient['CystoscopyService']["prostate_img"]; ?>" class="uploadPreview" style="width: 120px; height: 120px;" data="uploadPreview5"/>
                <br/><br/>
                <input id="uploadImage5" type="file" name="prostate_img" onchange="PreviewImage(this.id);" rel="5" class="uploadImage" data_name="photos" data="uploadImage"/>
            </td>
            <td><?php echo $this->Form->textarea('prostate',array('name'=>'data[CystoscopyService][prostate]', 'value' => $patient['CystoscopyService']['prostate'], 'class'=>'mceEditor','style'=>'height:100px;width:650px;')); ?></td>
        </tr>
        <tr>            
            <td><?php echo 'Bladder neck'; ?></td>
            <td>:</td>
            <td style="width: 200px;">
                <img id="uploadPreview2" src="<?php echo $this->webroot; ?>img/cystoscopy/<?php echo $patient['CystoscopyService']["bladder_neck_img"]; ?>" class="uploadPreview" style="width: 120px; height: 120px;" data="uploadPreview2"/>
                <br/><br/>
                <input id="uploadImage2" type="file" name="bladder_neck_img" onchange="PreviewImage(this.id);" rel="2" class="uploadImage" data_name="photos" data="uploadImage"/>
            </td>
            <td><?php echo $this->Form->textarea('bladder_neck',array('name'=>'data[CystoscopyService][bladder_neck]', 'value' => $patient['CystoscopyService']['bladder_neck'], 'class'=>'mceEditor','style'=>'height:100px;width:650px;')); ?></td>
        </tr>
        <tr>            
            <td><?php echo 'Bladder'; ?></td>
            <td>:</td>
            <td style="width: 200px;">
                <img id="uploadPreview3" src="<?php echo $this->webroot; ?>img/cystoscopy/<?php echo $patient['CystoscopyService']["bladder_img"]; ?>" class="uploadPreview" style="width: 120px; height: 120px;" data="uploadPreview3"/>
                <br/><br/>
                <input id="uploadImage3" type="file" name="bladder_img" onchange="PreviewImage(this.id);" rel="3" class="uploadImage" data_name="photos" data="uploadImage"/>
            </td>
            <td><?php echo $this->Form->textarea('bladder',array('name'=>'data[CystoscopyService][bladder]', 'value' => $patient['CystoscopyService']['bladder'], 'class'=>'mceEditor','style'=>'height:100px;width:650px;')); ?></td>
        </tr>
        <tr style="<?php if ($patient['Patient']['sex'] == "M") { echo 'display:none;';}?>">            
            <td><?php echo 'After 5 minutes <br/>cysto-hydrodistention'; ?></td>
            <td>:</td>
            <td style="width: 200px;">
                <img id="uploadPreview4" src="<?php echo $this->webroot; ?>img/cystoscopy/<?php echo $patient['CystoscopyService']["after_five_minute_img"]; ?>" class="uploadPreview" style="width: 120px; height: 120px;" data="uploadPreview4"/>
                <br/><br/>
                <input id="uploadImage4" type="file" name="after_five_minute_img" onchange="PreviewImage(this.id);" rel="4" class="uploadImage" data_name="photos" data="uploadImage"/>
            </td>
            <td><?php echo $this->Form->textarea('after_five_minute',array('name'=>'data[CystoscopyService][after_five_minute]', 'value' => $patient['CystoscopyService']['after_five_minute'], 'class'=>'mceEditor','style'=>'height:100px;width:650px;')); ?></td>
        </tr>
        <tr>
            <td><?php echo TABLE_CONCLUSION; ?></td>
            <td>:</td>
            <td colspan="2"><?php echo $this->Form->textarea('conclusion',array('name'=>'data[CystoscopyService][conclusion]', 'value' => $patient['CystoscopyService']['conclusion'], 'class'=>'mceEditor','style'=>'height:100px;width:650px;')); ?></td>
        </tr>        
    </table>
</fieldset>
<div class="clear"></div>
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <span class="txtSave"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<?php echo $this->Form->end(); ?>

