<?php
echo $this->element('prevent_multiple_submit');
$absolute_url = FULL_BASE_URL . Router::url("/", false);
echo $javascript->link('uninums.min');
include("includes/function.php");
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
        $("#EchoServiceAddEchoServiceObstetniqueDoctorForm").validationEngine();
        $("#EchoServiceAddEchoServiceObstetniqueDoctorForm").ajaxForm({
            dataType: 'json',
            beforeSubmit: function(arr, $form, options) {
                $(".txtSave").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {                
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif"); 
                $(".btnBackQueueEchoService").click();                                    
                if(result.code == "1"){
                    errorSaveData();
                }else{                                        
                    $("#dialog").html('<div><br/><center><div class="buttons" style="display: inline-block;"><button type="submit" class="positive printPatientObstentnique" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="txtPrintInvoice"><?php echo ACTION_PRINT; ?></span></button></div></center></div>');
                    $(".printPatientObstentnique").click(function(){
                        $.ajax({
                            type: "POST",
                            url: "<?php echo $this->base . '/echographie_patients'; ?>/printObstetniquePatient/"+result.id,
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
        
        $(".box").hide();
        $("#EchoServiceEchographId").change(function(){
            var category = $(this).val();
            if(category==""){
                $(".box").hide();            
            }else{
                $(".box").hide();
                $(".box[rel=" + category + "]").show();
            }            
        });
        $(".btnBackQueueEchoService").click(function(event) {
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableQueueEchoDoctor.fnDraw(false);
            var rightPanel = $(this).parent().parent().parent();
            var leftPanel = rightPanel.parent().find(".leftPanel");
            rightPanel.hide();
            rightPanel.html("");
            leftPanel.show("slide", {direction: "left"}, 500);
        });
        
        $("#dataDate" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
            yearRange: '-100:-0'
        }).unbind("blur");
        
        //Hide Patinen Info
        $("#btnHidePatientInfo").click(function(){
            $("#patientInfo").hide(900);
            $("#showPatientInfo").show();
        });
        //Show Patinen Info
        $("#btnShowPatientInfo").click(function(){
            $("#patientInfo").show(900);
            $("#showPatientInfo").hide();
        });
        
    });    
    
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
                var rightPanel=$("#EchoServiceAddEchoServiceObstetniqueDoctorForm").parent();
                var leftPanel=rightPanel.parent().find(".leftPanel");
                rightPanel.hide();rightPanel.html("");
                leftPanel.show("slide", { direction: "left" }, 500);
                oCache.iCacheLower = -1;
                oTableOrder.fnDraw(false);
            },
            buttons: {
                '<?php echo ACTION_CLOSE; ?>': function() {
                    $(this).dialog("close");
                }
            }
        });
    }
    <?php
    foreach ($echographies as $echographie):
    ?>   
        // Editor English
        CKEDITOR.replace( 'EchoServiceDescription<?php echo $echographie['EchographyInfom']['id'];?>', {
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
    <?php           
    endforeach;
    ?>     
</script>
<div style="padding: 5px;border: 1px dashed #3C69AD;">
    <div class="buttons">
        <a href="" class="positive btnBackQueueEchoService">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('EchoService',array('enctype' => 'multipart/form-data')); ?>
<input name="data[QeuedDoctor][id]" type="hidden" value="<?php echo $patient['QeuedDoctor']['id'];?>"/>
<input name="data[Queue][id]" type="hidden" value="<?php echo $patient['Queue'][0]['id'];?>"/>

<legend id="showPatientInfo" style="display:none;"><a href="#" id="btnShowPatientInfo" style="background: #CCCCCC; font-weight: bold;"><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?> [ Show ] </a> </legend>
<fieldset id="patientInfo" style="border: 1px dashed #3C69AD;">
    <legend><a href="#" id="btnHidePatientInfo" style="background: #CCCCCC; font-weight: bold;"> <?php echo MENU_PATIENT_MANAGEMENT_INFO; ?> [ Hide ] </a></legend>
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
                    $queryDataFromDoctor=  mysql_query("SELECT esreq.*,esreq.id as id FROM echo_service_requests as esreq "
                            . "INNER JOIN other_service_requests as osreq ON osreq.id=esreq.other_service_request_id "
                            . "INNER JOIN queued_doctors as qd ON qd.id=osreq.queued_doctor_id "
                            . "INNER JOIN queues as q ON q.id=qd.queue_id WHERE osreq.is_active=1 AND queue_id=".$this->params['pass'][1]);
                    $dataRequest=  mysql_fetch_array($queryDataFromDoctor);
                    echo $dataRequest['echo_description'];
                ?>
                <input type="hidden" value="<?php echo $dataRequest['id']; ?>" name="data[EchoServiceRequest][id]">
            </td>
        </tr>
    </table>      
</fieldset>
<br/>
<fieldset style="border: 1px dashed #3C69AD;">
    <legend><?php __(MENU_ECHO_SERVICE_INFO); ?></legend>
    <table class="defaultTable">
        <input type="hidden" value="<?php echo $patient['Patient']['id']?>" name="patient_id"/>  
        <tr>
            <td style="width: 35%" valign="top">
                <fieldset>
                    <table class="defualtTable">
                        <tr>
                            <td>    
                                <?php
                                $a = array();
                                $i = "";
                                foreach ($echographies as $echographie):
                                    $i = $echographie['EchographyInfom']['id'];
                                    $a[$i] = $echographie['EchographyInfom']['name'];
                                endforeach;
                                echo $form->input('echograph_id', array('label' => 'Echographie' . '<span class="red">&nbsp;&nbsp;*</span>:&nbsp;&nbsp;', 'empty' => INPUT_SELECT, 'options' => $a, 'class' => 'validate[required]','style'=>'width:309px;'));
                                ?>                                                
                            </td> 
                        </tr>
                        <tr>
                            <td>
                                <?php echo $this->Form->input('doctor_name', array('label' => 'Examen par' . '<span class="red">&nbsp;&nbsp;*</span>:&nbsp;&nbsp;', 'class' => 'validate[required]','style'=>'margin-left:4px; width: 295px;')); ?>
                            </td>                                            
                        </tr>  
                        <tr>
                            <td>
                                <?php
                                $a = array();
                                $i = "";
                                foreach ($indications as $indication):
                                    $i = $indication['Indication']['id'];
                                    $a[$i] = $indication['Indication']['name'];                                                
                                endforeach;
                                echo $form->input('Indication', array('label' => 'Indication' . '<span class="red">&nbsp;&nbsp;*</span>:&nbsp;&nbsp;', 'empty' => INPUT_SELECT, 'options' => $a, 'class' => 'validate[required]','style'=>'width:309px;margin-left:17px;'));
                                ?>                                                
                            </td>                                            
                        </tr>
                        <tr>
                            <td>                                                
                                <?php echo $this->Form->input('ddr', array('label' => 'D.D.R' . '<span>&nbsp;&nbsp;:&nbsp;&nbsp;</span>','style'=>'margin-left:42px; width: 295px;')); ?>
                            </td>                                            
                        </tr> 
                    </table>                                                                      
                </fieldset>
            </td>  

            <td style="width: 65%; vertical-align: top;" rowspan="2">                            
                <fieldset class="description" style="height: 475px; width: 90%;">
                    <?php
                    foreach ($echographies as $echographie):
                        ?>
                        <div class="box" rel="<?php echo $echographie['EchographyInfom']['id'] ?>" style="width:95%;height: 95%; vertical-align: top;">
                            <?php
                            $id = $echographie['EchographyInfom']['id'];
                            echo $this->Form->textarea('description',array('name'=> "data[EchoService][description][$id]", 'label' => 'R E S U L T A T', 'id' => 'EchoServiceDescription'.$id, 'class'=>'mceEditor', 'style'=>'height:100px;width:999px;','value' => $echographie['EchographyInfom']['description'])); 
                            echo '</div>';
                        endforeach;
                        ?>                                                                   
                </fieldset>
            </td>

            <!--For big child-->
        </tr>                         
        <td valign="top">
            <fieldset>   
                <table class="defualtTable">
                    <tr>
                        <td colspan="3">    
                            <?php echo $this->Form->input('form_child', array('label' => 'ទំរង់កូន' . '<span>&nbsp;&nbsp;:&nbsp;&nbsp;</span>','style'=>'margin-left:22px; width: 295px;')); ?>
                        </td>                                            
                    </tr>
                    <tr>
                        <td colspan="3">
                            <?php echo $this->Form->input('num_child', array('tabindex' => '5', 'label' => 'ចំនួនកូន' . '<span>&nbsp;&nbsp;:&nbsp;&nbsp;</span>','style'=>'width:307px;margin-left:18px;', 'options' => array('' => 'Please Select','០១' => '០១', '០២' => '០២', '០៣' => '០៣', '០៤' => '០៤', '០៥' => '០៥', '០៦' => '០៦'))); ?>                                        
                        </td>                                            
                    </tr>  
                    <tr>
                        <td colspan="3">
                            <?php echo $this->Form->input('healthy_child', array('label' => 'សុខភាពកូន' . '<span>&nbsp;&nbsp;:&nbsp;&nbsp;</span>', 'style'=>'width:295px;')); ?>
                        </td>                                            
                    </tr>
                    <tr>
                        <td colspan="3">                                        
                            <?php echo $this->Form->input('sex_child', array('tabindex' => '3', 'label' => 'ភេទកូន' . '<span>&nbsp;&nbsp;:&nbsp;&nbsp;</span>','style'=>'width:307px;margin-left:23px;', 'options' => array('' => 'Please Select','M' => 'Male', 'F' => 'Female', 'M+F' => 'Male+Female', 'M+M' => 'Male+Male', 'F+F' => 'Female+Female'))); ?>
                        </td>                                            
                    </tr>
                    <tr>
                        <td  colspan="3">
                            <?php echo $this->Form->input('teok_plos', array('label' => 'ទឹកភ្លោះ' . '<span>&nbsp;&nbsp;:&nbsp;&nbsp;</span>','style'=>'margin-left:21px; width: 295px;')); ?>
                        </td>                                            
                    </tr>
                    <tr>
                        <td colspan="3"> 
                            <?php echo $this->Form->input('location_sok', array('label' => 'ទីតាំងសុក' . '<span>&nbsp;&nbsp;:&nbsp;&nbsp;</span>','style'=>'margin-left:9px; width: 295px;')); ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 80%" colspan="2">
                            <?php echo $this->Form->input('weight_child', array('style'=>'width:30%;margin-left:18px;', 'label' => 'ទំងន់កូន' . '<span>&nbsp;&nbsp;:&nbsp;&nbsp;</span>')); ?>
                        </td>     
                        <td style="width: 80%">ក្រាម</td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo $this->Form->input('year_child', array('style'=>'width:38%;margin-left:18px;', 'label' => 'អាយុកូន' . '<span>&nbsp;&nbsp;:&nbsp;&nbsp;</span>')); ?>
                        </td>     
                        <td style="width: 20%">សបា្តហ៍</td>
                        <td style="width: 40%"><?php echo $this->Form->input('day_child', array('style'=>'width:80%;', 'label' => false)); ?></td>
                        <td style="width: 0%">ថ្ងៃ</td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <?php echo $this->Form->input('born_date', array('label' => 'គ្រប់ខែថ្ងៃ' . '<span>&nbsp;&nbsp;:&nbsp;&nbsp;</span>','style'=>'margin-left:14px; width: 295px;')); ?>
                        </td>                                   
                    </tr>
                </table>
            </fieldset>
        </td>                            
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

