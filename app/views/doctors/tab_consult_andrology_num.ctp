<?php
if (empty($consultation)) {
    echo GENERAL_NO_RECORD;
    exit();
}
require_once("includes/function.php");
?>
<?php 
    $absolute_url = FULL_BASE_URL . Router::url("/", false); 
    $tblName = "tbl123"; 
?>
<?php echo $javascript->link('jquery.form'); ?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/sketch.js"></script>
<style type="text/css" media="screen">
    div.checkbox{
        width: 180px;
    }
    .table_print_labo td{ 
        border: none !important;
        line-height: 20px;
        padding: 0;
        margin: 0;
    }
    .legend_title{
        background: #3C69AD !important;
    }
    div.legend div.legend_content {
        border-left: 1px solid #3C69AD !important;
        border-right: 1px solid #3C69AD !important;
        border-bottom: 1px solid #3C69AD !important;
    }
</style>
<script type="text/javascript">
    $(function() {
        $('#tools_sketch').sketch({defaultColor: "#f00", defaultSize: 2});
    });
</script>

<?php 
$tblName = "tbl123"; 
$tblRand = "tbl" . rand();
?>
<script type="text/javascript">
    $(document).ready(function(){
        $(".chzn-select").chosen();
        $(".PatientConsultationEditForm").validationEngine();
        $(".PatientConsultationEditForm").ajaxForm({
            dataType: 'json',
            beforeSubmit: function(arr, $form, options) {
                $(".loading").show();
            },
            success: function(result) {
                $(".loading").hide();
                $("#tabs3").tabs("select", 0);
                $("#tabConsultNum<?php echo $tblName;?>").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabConsultNum/<?php echo $this->params['pass'][0] . '/' . $this->params['pass'][1]; ?>");                                             
                $("#dialog").html('<div><br/><center><div class="buttons" style="display: inline-block;"><button type="submit" class="positive printPatientConsultForm" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="txtPrintInvoice"><?php echo ACTION_PRINT; ?></span></button></div></center></div>');
                $(".printPatientConsultForm").click(function() {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printRecord/" + result.patientConsultId + "/" + result.queueDoctorId + "/" + result.queueId,
                        beforeSend: function() {
                            $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner.gif');
                        },
                        success: function(printInvoiceResult) {
                            w = window.open();
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
                    open: function(event, ui) {
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
        
        $(".legend_content").show();
        $(".legend_title").click(function(){
            $(this).siblings(".legend_content").slideToggle();
        });
        
        $("#consultation").accordion({
            collapsible: true,
            autoHeight: false,
            navigation: false,
            active: false
        });
        
        $(".followup").accordion({
            collapsible: true,
            autoHeight: false,
            navigation: true,
            active: false
        });
        
        $('#PatientConsultationDateFirstComplaint, #PatientConsultationDateOfConsult').datepicker(
        {
            changeMonth: true,
            changeYear: true,
            showSecond: false,
            dateFormat:'yy-mm-dd'            
        }); 
        
        $(".btnPrint").click(function(event){                
            event.preventDefault();
            $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            event.stopPropagation();
            var btnPatientConsultation=$("#dialogPrint<?php echo $tblName;?>").html();
            var patientConsultationId = $(this).attr('patientConsultationId');
            var queuedDoctorId = $(this).attr('queuedDoctorId');
            var queueId = $(this).attr('queueId');
            var name = $(this).attr('name');
            $("#dialog").html('<div><br/><center><div class="buttons" style="display: inline-block;"><button type="submit" class="positive printPatientConsultForm" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="txtPrintInvoice"><?php echo ACTION_PRINT; ?></span></button></div></center></div>');            
            $(".printPatientConsultForm").click(function() {
                $.ajax({
                    type: "POST",
                    url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printRecord/" + patientConsultationId + "/" + queuedDoctorId + "/" + queueId,
                    beforeSend: function() {
                        $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner.gif');
                    },
                    success: function(printInvoiceResult) {
                        w = window.open();
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
                open: function(event, ui) {
                    $(".ui-dialog-buttonpane").show();
                },
                buttons: {
                    '<?php echo ACTION_CLOSE; ?>': function() {
                        $(this).dialog("close");
                    }
                }                                                    
            });                        
        });
        
        /**
        * This script use for add new follow up using ajax to patient history by patient_history_id
        */
        
        $("#btnFollowup<?php echo $tblRand; ?>").click(function() {
            var patientConsultationId = this.name;
            var queueId = $(this).attr('title');
            var queuedDoctorId = $(this).attr('queuedDoctorId');
            
            $.ajax({
                type: "GET",
                //url: "<?php echo $this->base; ?>/doctors/followup/",
                url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/followup/",
                data: "",
                beforeSend: function(){
                    $("#dialog9").html('<p id="loading" style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                },
                success: function(msg){
                    $("#loading").hide();
                    $("#dialog9").html(msg);
                }
            });
            
            $("#dialog9").dialog({
                title: '<?php echo MENU_FOLLOW_UP_LABEL_ADD; ?>',
                resizable: false,
                modal: true,
                width: '70%',
                height: 500,
                buttons: {
                    "<?php echo ACTION_SAVE; ?>": function() {
                        var isFormValidated = $("#PatientFollowupAddForm").validationEngine('validate');
                        var tabId = $("#tab_id").val();
                        if(!isFormValidated){
                            return false;
                        }else{
                            var url = "<?php echo $this->base . '/' . $this->params['controller']; ?>/addNewFollowUp/" + patientConsultationId + '/' + queuedDoctorId + '/' + queueId;
                            var post = $('#PatientFollowupAddForm').serialize();
                            $.post(url,post,function(rs){
                                if(tabId==2){
                                    if(rs.indexOf('1')!=-1){
                                        $("#tabs2").tabs("select", 0);
                                        $("#tabConsultNum").load("<?php echo $absolute_url.$this->params['controller']; ?>/tabConsultNum/" + queuedDoctorId + '/' + queueId);
                                    } 
                                }else{
                                    if(rs.indexOf('1')!=-1){
                                        $("#tabs3").tabs("select", 0);
                                        $("#tabs3").tabs("select", 1);
                                        $("#tabConsultNum<?php echo $tblName; ?>").load("<?php echo $absolute_url.$this->params['controller']; ?>/tabConsultNum/" + queuedDoctorId + '/' + queueId);
                                    } 
                                }
                            });
                            $(this).dialog("close");
                        }
                    }
                }
            });
            return false;
        });   
        
    });
</script>

<div id="consultation">
    <?php
    $ind = 1;
    $date1 = date("Y-m-d");    
    foreach ($consultation as $consultation):
        $display = "";
        $disabled = "";
        $date2 = date('Y-m-d', strtotime($consultation['PatientConsultation']['created']));        
        if(strtotime($date1) > strtotime($date2)){
            $display = "display:none;";
            $disabled = "disabled";
        }
        ?>
        <h3>
            <a href="#">
                <?php echo "# : "; ?>
                <?php echo $consultation['PatientConsultation']['consultation_code'] . ' - ' . date('d/m/Y H:i:s', strtotime($consultation['PatientConsultation']['created'])); ?>
                <div style="float:right;">
                    <img alt="" patientConsultationId="<?php echo $consultation['PatientConsultation']['id']?>" queuedDoctorId="<?php echo $consultation['PatientConsultation']['queued_doctor_id'];?>" queueId="<?php echo $consultation['Queue']['id'];?>" src="<?php echo $this->webroot; ?>img/button/printer.png" class="btnPrint"  name="<?php echo $consultation['PatientConsultation']['consultation_code']; ?>" onmouseover="Tip('<?php echo ACTION_PRINT; ?>')" />
                </div>
            </a>
        </h3>
        <div class="<?php echo $consultation['PatientConsultation']['id']; ?>">
            <?php echo $this->Form->create('PatientConsultation', array('id' => 'PatientConsultationEditForm'.$consultation['PatientConsultation']['id'], 'class' => 'PatientConsultationEditForm', 'rel' => $consultation['PatientConsultation']['id'] , 'url' => '/doctors/editConsult/' . $consultation['PatientConsultation']['id'] . '/' . $consultation['PatientConsultation']['queued_doctor_id'] . '/' . $consultation['Queue']['id'], 'enctype' => 'multipart/form-data')); ?>
            <input name="data[PatientConsultation][consultation_code]" type="hidden" value="<?php echo $consultation['PatientConsultation']['consultation_code']; ?>"/>
            <input name="data[QeuedDoctor][id]" type="hidden" value="<?php echo $consultation['QeuedDoctor']['id']; ?>"/>
            <input name="data[Queue][id]" type="hidden" value="<?php echo $consultation['Queue']['id']; ?>"/>
            <input id="link_url" type="hidden" value="<?php echo $absolute_url . $this->params['controller']; ?>"/>
            <div class="legend">
                <div class="legend_title"><label for="ConsultationDaignostic"><b><?php echo MENU_VITAL_SING; ?></b></label></div>
                <div class="legend_content">
                    <fieldset>
                        <legend><?php __(MENU_VITAL_SING_INFO); ?></legend>
                        <table style="width: 100%;" cellspacing="3">
                            <tr>
                                <td style="width: 15%;"><label for="PatientVitalSignHeight"><?php echo TABLE_HEIGHT; ?></label></td>
                                <td style="width: 20%;">: <?php echo $consultation['PatientVitalSign']['height']; ?> cm</td>
                                <td style="width: 15%;"><label for="PatientVitalSignWeight"><?php echo TABLE_WEIGHT; ?></label></td>
                                <td style="width: 20%;">: <?php echo $consultation['PatientVitalSign']['weight']; ?> kg</td>
                                <td style="width: 10%;"><label for="PatientVitalSignBMI"><?php echo TABLE_BMI; ?></label></td>
                                <td style="width: 20%;" class="BMI">: <?php echo $consultation['PatientVitalSign']['BMI'] ?></td>            
                            </tr>
                            <tr>
                                <td style="width: 15%;"><label for="PatientVitalSignPulse"><?php echo TABLE_PULSE; ?></label></td>
                                <td style="width: 20%;">: <?php echo $consultation['PatientVitalSign']['pulse']; ?> /m</td>
                                <td style="width: 15%;"><label for="PatientVitalSignRespiratory"><?php echo TABLE_RESPIRATORY; ?></label></td>
                                <td style="width: 20%;">: <?php echo $consultation['PatientVitalSign']['respiratory']; ?> /m</td>                     
                            </tr>
                            <tr>
                                <td style="width: 15%;"><label for="PatientVitalSignTemperature"><?php echo TABLE_TEMPERATURE; ?></label></td>
                                <td style="width: 20%;">: <?php echo $consultation['PatientVitalSign']['temperature']; ?> °C</td>
                                <td style="width: 15%;"><label for="PatientVitalSignSop2"><?php echo TABLE_SOP2; ?></label></td>
                                <td style="width: 20%;">: <?php echo $consultation['PatientVitalSign']['sop2']; ?></td> 
                            </tr>
                        </table>      
                    </fieldset>
                    <br/>
                    <fieldset>
                        <legend><?php __(MENU_BLOOD_PRESSURE); ?></legend>
                        <table class="table" style="width: 100%;">
                            <tr>
                                <th class="first" style="width: 10%"></th>
                                <th>1st reading</th>
                                <th>2nd reading</th>
                                <th>3rd reading</th>
                            </tr>
                            <tr>
                                <td class="first">Systolic</td>            
                                <td><?php echo $consultation['PatientVitalSignBloodPressure']['result_systolic_1']; ?> mmHg</td>
                                <td><?php echo $consultation['PatientVitalSignBloodPressure']['result_systolic_2']; ?> mmHg</td>
                                <td><?php echo $consultation['PatientVitalSignBloodPressure']['result_systolic_3']; ?> mmHg</td>
                            </tr>
                            <tr>
                                <td class="first">Diastolic</td>            
                                <td><?php echo $consultation['PatientVitalSignBloodPressure']['result_diastolic_1']; ?> mmHg</td>
                                <td><?php echo $consultation['PatientVitalSignBloodPressure']['result_diastolic_2']; ?> mmHg</td>
                                <td><?php echo $consultation['PatientVitalSignBloodPressure']['result_diastolic_3']; ?> mmHg</td>
                            </tr>
                        </table>
                    </fieldset>
                </div>
            </div>
            <br />
            <div class="legend">
                <div class="legend_title"><label for="ConsultationDateFirstComplaint"><b><?php echo MENU_DOCTOR; ?></b></label></div>
                <div class="legend_content">
                    <table style="width: 100%">
                        <tr>
                            <td style="width: 20%;"><labe for="DoctorConsultationName"><?php echo DOCTOR_NAME; ?> :</label></td>
                            <td style="width: 27%;">
                                <?php echo $this->Form->input('doctor_consultation_ids', array('label' => false, 'data-placeholder' => INPUT_SELECT, 'style'=>'width: 73%;', 'selected' => $consultation['DoctorConsultation']['id'])); ?>
                            </td>
                            <td style="width: 53%;"></td>
                        </tr>
                    </table>        
                </div>
            </div>
            <br />
            <div class="legend">
                <div class="legend_title"><label for="PatientConsultationDateFirstComplaint"><b><?php echo TABLE_PRESENT_MEDICAL_HISTORY; ?></b></label></div>
                <div class="legend_content">
                    <table style="width: 100%">
                        <tr>
                            <td style="width: 20%;"><labe for="PatientConsultationDateFirstComplaint"><?php echo DATE_OF_FIRST_COMPLAINT; ?> :</label></td>
                            <td>
                                <?php echo $this->Form->input('date_first_complaint', array('disabled' => $disabled, 'label' => false, 'style' => 'width:32%;', 'type' => 'text', 'value' => $consultation['PatientConsultation']['date_first_complaint'] != "0000-00-00" ? $consultation['PatientConsultation']['date_first_complaint'] : "")); ?>                    
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%;"><labe for="PatientConsultationDateOfConsult"><?php echo DATE_OF_FIRST_CONSULTATION; ?> :</label></td>
                            <td>
                                <?php echo $this->Form->input('date_of_consult', array('disabled' => $disabled, 'label' => false, 'style' => 'width:32%;', 'type' => 'text', 'value' => $consultation['PatientConsultation']['date_of_consult'] != "0000-00-00" ? $consultation['PatientConsultation']['date_of_consult'] : "")); ?>                    
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%;"><labe for="PatientConsultationPhysicalExamination"><?php echo PHYSICAL_EXAMINATION; ?> :</label></td>
                            <td>
                                <?php echo $this->Form->input('physical_examination', array('disabled' => $disabled, 'label' => false, 'type' => 'textarea', 'value' => $consultation['PatientConsultation']['physical_examination'], 'style' => 'width:99%;')); ?>
                                <?php echo $this->Form->input('physical_examination_other_info', array('disabled' => $disabled,'label' => false, 'type' => 'textarea' ,  'value' => $consultation['PatientConsultation']['physical_examination_other_info'], 'placeholder' => 'Other Information', 'style' => 'width:99%;')); ?>
                            </td>
                        </tr>
                    </table>        
                </div>
            </div>
            <br />
            <div class="legend">
                <div class="legend_title"><label for="PatientConsultationDaignostic"><b><?php echo TABLE_CHIEF_COMPLAIN; ?></b></label></div>
                <div class="legend_content"><?php echo $this->Form->input('chief_complain', array('disabled' => $disabled, 'label' => false, 'type' => 'textarea', 'value' => $consultation['PatientConsultation']['chief_complain'], 'style' => 'width:99%;')); ?></div>
                <div class="legend_content"><p>Other Information</p><?php echo $this->Form->input('chief_complain_other_info', array('disabled' => $disabled,'label' => false,'placeholder' => 'Other Information' ,  'type' => 'textarea' ,  'value' => $consultation['PatientConsultation']['chief_complain_other_info'] , 'style' => 'width:99%;')); ?></div>
            </div>
            
            <br />
            <div class="legend">
                <div class="legend_title"><label for="PatientConsultationDaignostic"><b><?php echo TABLE_DAIGNOSTIC; ?></b></label></div>
                <div class="legend_content"><?php echo $this->Form->input('daignostic', array('disabled' => $disabled, 'label' => false, 'type' => 'textarea', 'value' => $consultation['PatientConsultation']['daignostic'], 'style' => 'width:99%;')); ?></div>
                <div class="legend_content"><p>Other Information</p><?php echo $this->Form->input('daignostic_other_info', array('disabled' => $disabled,'label' => false,'placeholder' => 'Other Information' , 'type' => 'textarea', 'value' => $consultation['PatientConsultation']['daignostic_other_info'] , 'style' => 'width:99%;')); ?></div>
            </div>
            <br />
            <div class="legend">
                <div class="legend_title"><label for="PatientConsultationFollowUp"><b><?php echo MENU_FOLLOW_UP; ?></b></label></div>
                <div class="legend_content"><?php echo $this->Form->input('follow_up', array('disabled' => $disabled, 'label' => false, 'type' => 'textarea', 'value' => $consultation['PatientConsultation']['follow_up'], 'style' => 'width:99%;')); ?></div>
            </div>
            <br />
            <div class="legend">
                <div class="legend_title"><label for="PatientConsultationRemark"><b><?php echo MENU_REMARKS; ?></b></label></div>
                <div class="legend_content"><?php echo $this->Form->input('remark', array('disabled' => $disabled, 'label' => false, 'type' => 'textarea', 'value' => $consultation['PatientConsultation']['remark'], 'style' => 'width:99%;')); ?></div>
            </div>
            <br />
            <div class="legend">
                <div class="legend_title"><label for="PatientConsultationTreatment"><b><?php echo MENU_TREATMENT; ?></b></label></div>
                <div class="legend_content">
                    <h1 align="left" style="color:#000;font-size:12px;text-decoration:underline"><?php echo MENU_LABO_RESUTL?></h1>
                    <div id="laboResult" style="width: 100%;">
                        <?php 
                        $data = $this->requestAction('/labos/getResultLabo/'.$consultation['QeuedLabo']['id']);   
                        if(!empty($data)){
                        ?>
                        <table class="table_print_labo" style="width:100%; float: left; margin:0px;padding-left:10px;border: none;" align='center' id="print">
                            <tr>
                                <td valign="top">                     
                                    <!-- START -->
                                    <?php 
                                    $dob = $data[0]['Patient']['dob'];
                                    $sex = $data[0]['Patient']['sex'];
                                    $oldLaboTitle = '';
                                    $then_ts = strtotime($data[0]['Patient']['dob']);
                                    $then_year = date('Y', $then_ts);
                                    $age = date('Y') - $then_year;
                                    if(strtotime('+' . $age . ' years', $then_ts) > time()) $age--;              

                                    if($age==0){
                                        $then_year = date('m', $then_ts);
                                        $month = date('m') - $then_year;
                                        if(strtotime('+' . $month . ' month', $then_ts) > time()) $month--;
                                        $dob = $month;
                                    }else{
                                        $dob = $age * 12;
                                    } 
                                    ?>                                    
                                    <?php foreach ($data[2] as $laboItemCategory) { ?>
                                        <div class="boxTestBlood" rel="<?php echo $laboItemCategory['LaboItemCategory']['id'] ?>">
                                            <h1 align="center" style="color:#000;font-size:13px;text-decoration:underline"><?php echo $laboItemCategory['LaboItemCategory']['name']; ?></h1>                                                                       
                                            <?php
                                            $laboGroupIndex = 0;
                                            echo "<table style='width:100%;'>";
                                            echo '<tr>';
                                                echo '<td style="color:green;font-size:13px;font-weight: bold;width: 50%;">Test Name</td>';
                                                echo '<td style="color:green;font-size:13px;font-weight: bold;width: 15%;">Result</td>';
                                                echo '<td style="color:green;font-size:13px;font-weight: bold;width: 15%;">Unit</td>';
                                                echo '<td style="white-space: nowrap;color:green;font-size:13px;font-weight: bold;">Reference Ranges</td>';
                                            echo '</tr>';
                                            if ($laboItemCategory['LaboItemCategory']['id'] == 6 || $laboItemCategory['LaboItemCategory']['id'] == 7 || $laboItemCategory['LaboItemCategory']['id'] == 10 || $laboItemCategory['LaboItemCategory']['id'] == 9) {
                                                $query = mysql_query("SELECT speciment_type FROM speciment_types WHERE labo_item_category_id = '" . $laboItemCategory['LaboItemCategory']['id'] . "' AND labo_id = " . $data[1]['Labo']['id']);
                                                while ($specimentType = mysql_fetch_array($query)) {
                                                    echo '<tr>';
                                                        echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 600px;padding-left:15px;font-size: 12px">' . mb_str_pad('Specimen type', 52, '.', STR_PAD_RIGHT) . ':' . '</td>';
                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold">' . wordwrap($specimentType['speciment_type'], 16, "<br />\n") . '</td>';
                                                    echo '<tr/>';
                                                }
                                            }                                            
                                            foreach ($data[1]['LaboRequest'] as $laboRequest) {
                                                $item_requests = @unserialize($laboRequest['request']);
                                                $item_results = @unserialize($laboRequest['result']);
                                                ?>                                                        
                                                <?php
                                                if (!empty($data[3])) {
                                                    $items = array();
                                                    foreach ($data[3] as $i) {
                                                        if ($i['LaboItem']['parent_id'] == NULL) {
                                                            $items[$i['LaboItem']['category']][] = $i;
                                                        }
                                                        foreach ($data[3] as $j) {
                                                            if ($j['LaboItem']['parent_id'] == $i['LaboItem']['id']) {
                                                                $items[$i['LaboItem']['category']][] = $j;
                                                            }
                                                        }
                                                    }
                                                    $results = @unserialize($items);
                                                    $keys = array_keys($items);

                                                    foreach ($keys as $k) {
                                                        $index = 0;
                                                        foreach ($items[$k] as $ind => $i) {
                                                            $queryLaboTitle = mysql_query("SELECT name FROM labo_title_items WHERE id='" . $i['LaboItem']['title_item'] . "'");
                                                            $dataLaboTitle = mysql_fetch_array($queryLaboTitle);

                                                            if ($laboItemCategory['LaboItemCategory']['id'] == $i['LaboItem']['category']) {
                                                                if (in_array($i['LaboItem']['id'], $item_requests) || in_array($i['LaboItem']['parent_id'], $item_requests)) {
                                                                    if (++$index == 1) {

                                                                    }
                                                                    //if result is not null                                                
                                                                    if ($item_results[$i['LaboItem']['id']] != "") {
                                                                        $min = "";
                                                                        $max = "";
                                                                        $labo_item_id = $i['LaboItem']['id'];
                                                                        $query = mysql_query('SELECT * FROM age_for_labos as afl INNER JOIN labo_item_details as lid ON lid.status >0 AND afl.id = lid.age_for_labo_id AND lid.labo_item_id = "' . $labo_item_id . '"');
                                                                        while ($row = mysql_fetch_array($query)) {
                                                                            if ($dob >= $row['from'] && $dob < $row['to']) {
                                                                                if ($row['sex'] != "" && $row['sex'] == $sex) {
                                                                                    $query_next = mysql_query('SELECT * FROM age_for_labos as afl INNER JOIN labo_item_details as lid ON lid.status >0 AND afl.id = lid.age_for_labo_id AND afl.sex = "' . $sex . '" AND afl.from<="' . $dob . '" AND afl.to>"' . $dob . '" AND lid.labo_item_id = "' . $labo_item_id . '"');
                                                                                    while ($row_next = mysql_fetch_array($query_next)) {
                                                                                        $min = $row_next['min_value'];
                                                                                        $max = $row_next['max_value'];
                                                                                    }
                                                                                } else if ($row['sex'] == "") {
                                                                                    $query_next = mysql_query('SELECT * FROM age_for_labos as afl INNER JOIN labo_item_details as lid ON lid.status >0 AND afl.id = lid.age_for_labo_id AND afl.from<="' . $dob . '" AND afl.to>"' . $dob . '" AND lid.labo_item_id = "' . $labo_item_id . '"');
                                                                                    while ($row_next = mysql_fetch_array($query_next)) {
                                                                                        $min = $row_next['min_value'];
                                                                                        $max = $row_next['max_value'];
                                                                                    }
                                                                                }
                                                                            }
                                                                            if ($row['from'] == 0 && $row['to'] == 0 && $row['sex'] == "") {
                                                                                $min = $row['min_value'];
                                                                                $max = $row['max_value'];
                                                                            }
                                                                        }
                                                                        if ($i['LaboItem']['title_item'] != NULL) {
                                                                            if ($i['LaboItem']['title_item'] != $oldLaboTitle) {
                                                                                echo '<tr>';
                                                                                echo '<td style="white-space: nowrap;color:#000;font-size:13px;padding-left:15px;">' . $dataLaboTitle[0] . ' </td>';
                                                                                echo '</tr>';
                                                                            }
                                                                            echo '<tr>';
                                                                            echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 600px;padding-left:30px;font-size: 12px">'
                                                                                     . ($i['LaboItem']['parent_id'] != NULL ? '&nbsp;&nbsp;&nbsp;&nbsp;' : '') . mb_str_pad($i['LaboItem']['name'], 50, '.', STR_PAD_RIGHT) . ':';
                                                                                     if($i['LaboItem']['description']!=""){
                                                                                         echo '<p>' . nl2br($i['LaboItem']['description']) . '</p>';
                                                                                     }
                                                                            echo '</td>';                                                                            

                                                                            if ($i['LaboItem']['normal_value_type'] == "Positive / Negative") {
                                                                                echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 394px;font-size: 12px;font-weight: bold">' . ($item_results[$i['LaboItem']['id']][0] == 'Positive' ? 'Positive &nbsp;' . $item_results[$i['LaboItem']['id']][1] : "" . $item_results[$i['LaboItem']['id']][0] . "" ) . '</td>';
                                                                            } else if ($i['LaboItem']['normal_value_type'] == "Free Style") {
                                                                                if (isset($item_results[$i['LaboItem']['id']])) {
                                                                                    $test = wordwrap($item_results[$i['LaboItem']['id']], 16, "<br />\n");
                                                                                } else {
                                                                                    $test = "";
                                                                                }
                                                                                echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 394px;font-size: 12px">' . $test . '</td>';
                                                                                echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 300px;font-size: 12px"></td>';
                                                                            } else if ($i['LaboItem']['normal_value_type'] == "Number") {
                                                                                if ($min != "") {
                                                                                    $code = trim($min);
                                                                                    $var = substr($code, 0, 1);
                                                                                } else {
                                                                                    $code = trim($max);
                                                                                    $var = substr($code, 0, 1);
                                                                                }
                                                                                if ($i['LaboItem']['item_unit'] == "/mm3") {
                                                                                    //$type = "/mm<sup>3</sup>";
                                                                                    $type = "/mm3";
                                                                                    if ($var == "<" || $var == ">") {
                                                                                        if ($min != "") {
                                                                                            $code = trim($min);
                                                                                        } else {
                                                                                            $code = trim($max);
                                                                                        }
                                                                                        $var = substr($code, 0, 2);
                                                                                        $newVar = substr($code, 2, 20);
                                                                                        $newVar = trim($newVar);
                                                                                        $result = $item_results[$i['LaboItem']['id']];
                                                                                        if ($var == "<=") {
                                                                                            if ($result > $newVar) {
                                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold;font-size: 12px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                            } else {
                                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 12px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                            }
                                                                                        } else if ($var == ">=") {
                                                                                            if ($result < $newVar) {
                                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold;font-size: 12px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                            } else {
                                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 12px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                            }
                                                                                        } else {
                                                                                            if ($min != "") {
                                                                                                $code = trim($min);
                                                                                            } else {
                                                                                                $code = trim($max);
                                                                                            }
                                                                                            $var = substr($code, 0, 1);
                                                                                            $newVar = substr($code, 1, 20);
                                                                                            $newVar = trim($newVar);
                                                                                            if ($var == "<") {
                                                                                                if ($result > $newVar) {
                                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold;font-size: 12px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                                } else {
                                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 12px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                                }
                                                                                            } else {
                                                                                                if ($result < $newVar) {
                                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold;font-size: 12px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                                } else {
                                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 12px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    } else if ($item_results[$i['LaboItem']['id']] > $max || $item_results[$i['LaboItem']['id']] < $min) {
                                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold;font-size: 12px;vertical-align: top;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                                                    } else {
                                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 12px;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                                                    }
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 300px;font-size: 12px;vertical-align: top;">' . $type . '</td>';
                                                                                } else {
                                                                                    if ($var == "<" || $var == ">") {
                                                                                        if ($min != "") {
                                                                                            $code = trim($min);
                                                                                        } else {
                                                                                            $code = trim($max);
                                                                                        }
                                                                                        $var = substr($code, 0, 2);
                                                                                        $newVar = substr($code, 2, 20);
                                                                                        $newVar = trim($newVar);
                                                                                        $result = $item_results[$i['LaboItem']['id']];
                                                                                        if ($var == "<=") {
                                                                                            if ($result > $newVar) {
                                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold;font-size: 12px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                            } else {
                                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 12px;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                            }
                                                                                        } else if ($var == ">=") {
                                                                                            if ($result < $newVar) {
                                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold;font-size: 12px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                            } else {
                                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 12px;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                            }
                                                                                        } else {
                                                                                            if ($min != "") {
                                                                                                $code = trim($min);
                                                                                            } else {
                                                                                                $code = trim($max);
                                                                                            }
                                                                                            $var = substr($code, 0, 1);
                                                                                            $newVar = substr($code, 1, 20);
                                                                                            $newVar = trim($newVar);
                                                                                            if ($var == "<") {
                                                                                                if ($result > $newVar) {
                                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold;font-size: 12px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                                } else {
                                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 12px;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                                }
                                                                                            } else {
                                                                                                if ($result < $newVar) {
                                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold;font-size: 12px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                                } else {
                                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 12px;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    } else if ($item_results[$i['LaboItem']['id']] > $max || $item_results[$i['LaboItem']['id']] < $min) {
                                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold;font-size: 12px;vertical-align: top;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                                                    } else {
                                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 12px;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                                                    }
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 300px;font-size: 12px;vertical-align: top;">' . $i['LaboItem']['item_unit'] . '</td>';
                                                                                }
                                                                            }
                                                                            if (trim($min) == '') {
                                                                                echo ' <td style="color:#000;white-space: nowrap;font-family:monospace;width: 550px;font-size: 12px;vertical-align: top;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' ? $max : '') . '</td>';
                                                                            } else if (trim($max) == '') {
                                                                                echo ' <td style="color:#000;white-space: nowrap;font-family:monospace;width: 550px;font-size: 12px;vertical-align: top;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' ? $min : '') . '</td>';
                                                                            } else {
                                                                                echo ' <td style="color:#000;white-space: nowrap;font-family:monospace;width: 550px;font-size: 12px;vertical-align: top;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' ? ' (' . $min . ' - ' . $max . ')' : '') . '</td>';
                                                                            }
                                                                            echo '</tr>';
                                                                            $oldLaboTitle = $i['LaboItem']['title_item'];
                                                                        } else {

                                                                            echo '<tr>';
                                                                            echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 600px;padding-left:15px;font-size: 12px">'
                                                                                     . ($i['LaboItem']['parent_id'] != NULL ? '&nbsp;&nbsp;&nbsp;&nbsp;' : '') . mb_str_pad($i['LaboItem']['name'], 52, '.', STR_PAD_RIGHT) . ':';
                                                                                     if($i['LaboItem']['description']!=""){
                                                                                         echo '<p>' . nl2br($i['LaboItem']['description']) . '</p>';
                                                                                     }
                                                                            echo '</td>';
                                                                            if ($i['LaboItem']['normal_value_type'] == "Positive / Negative") {
                                                                                echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 394px;font-size: 12px;font-weight: bold">' . ($item_results[$i['LaboItem']['id']][0] == 'Positive' ? 'Positive &nbsp;' . $item_results[$i['LaboItem']['id']][1] : "" . $item_results[$i['LaboItem']['id']][0] . "" ) . '</td>';
                                                                            } else if ($i['LaboItem']['normal_value_type'] == "Free Style") {
                                                                                if (isset($item_results[$i['LaboItem']['id']])) {
                                                                                    $test = wordwrap($item_results[$i['LaboItem']['id']], 16, "<br />\n");
                                                                                } else {
                                                                                    $test = "";
                                                                                }
                                                                                echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 394px;font-size: 12px">' . $test . '</td>';
                                                                                echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 300px;font-size: 12px"></td>';
                                                                            } else if ($i['LaboItem']['normal_value_type'] == "Number") {
                                                                                if ($min != "") {
                                                                                    $code = trim($min);
                                                                                    $var = substr($code, 0, 1);
                                                                                } else {
                                                                                    $code = trim($max);
                                                                                    $var = substr($code, 0, 1);
                                                                                }
                                                                                if ($i['LaboItem']['item_unit'] == "/mm3") {
                                                                                    //$type = "/mm<sup>3</sup>";
                                                                                    $type = "/mm3";
                                                                                    if ($var == "<" || $var == ">") {
                                                                                        if ($min != "") {
                                                                                            $code = trim($min);
                                                                                        } else {
                                                                                            $code = trim($max);
                                                                                        }
                                                                                        $var = substr($code, 0, 2);
                                                                                        $newVar = substr($code, 2, 20);
                                                                                        $newVar = trim($newVar);
                                                                                        $result = $item_results[$i['LaboItem']['id']];
                                                                                        if ($var == "<=") {
                                                                                            if ($result > $newVar) {
                                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold;font-size: 12px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                            } else {
                                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 12px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                            }
                                                                                        } else if ($var == ">=") {
                                                                                            if ($result < $newVar) {
                                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold;font-size: 12px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                            } else {
                                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 12px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                            }
                                                                                        } else {
                                                                                            if ($min != "") {
                                                                                                $code = trim($min);
                                                                                            } else {
                                                                                                $code = trim($max);
                                                                                            }
                                                                                            $var = substr($code, 0, 1);
                                                                                            $newVar = substr($code, 1, 20);
                                                                                            $newVar = trim($newVar);
                                                                                            if ($var == "<") {
                                                                                                if ($result > $newVar) {
                                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold;font-size: 12px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                                } else {
                                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 12px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                                }
                                                                                            } else {
                                                                                                if ($result < $newVar) {
                                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold;font-size: 12px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                                } else {
                                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 12px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    } else if ($item_results[$i['LaboItem']['id']] > $max || $item_results[$i['LaboItem']['id']] < $min) {
                                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold;font-size: 12px;vertical-align: top;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                                                    } else {
                                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 12px;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                                                    }
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 300px;font-size: 12px;vertical-align: top;">' . $type . '</td>';
                                                                                } else {
                                                                                    if ($var == "<" || $var == ">") {
                                                                                        if ($min != "") {
                                                                                            $code = trim($min);
                                                                                        } else {
                                                                                            $code = trim($max);
                                                                                        }
                                                                                        $var = substr($code, 0, 2);
                                                                                        $newVar = substr($code, 2, 20);
                                                                                        $newVar = trim($newVar);
                                                                                        $result = $item_results[$i['LaboItem']['id']];
                                                                                        if ($var == "<=") {
                                                                                            if ($result > $newVar) {
                                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold;font-size: 12px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                            } else {
                                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 12px;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                            }
                                                                                        } else if ($var == ">=") {
                                                                                            if ($result < $newVar) {
                                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold;font-size: 12px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                            } else {
                                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 12px;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                            }
                                                                                        } else {
                                                                                            if ($min != "") {
                                                                                                $code = trim($min);
                                                                                            } else {
                                                                                                $code = trim($max);
                                                                                            }
                                                                                            $var = substr($code, 0, 1);
                                                                                            $newVar = substr($code, 1, 20);
                                                                                            $newVar = trim($newVar);
                                                                                            if ($var == "<") {
                                                                                                if ($result > $newVar) {
                                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold;font-size: 12px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                                } else {
                                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 12px;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                                }
                                                                                            } else {
                                                                                                if ($result < $newVar) {
                                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold;font-size: 12px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                                } else {
                                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 12px;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    } else if ($item_results[$i['LaboItem']['id']] > $max || $item_results[$i['LaboItem']['id']] < $min) {
                                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold;font-size: 12px;vertical-align: top;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                                                    } else {
                                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 12px;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                                                    }
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 300px;font-size: 12px;vertical-align: top;">' . $i['LaboItem']['item_unit'] . '</td>';
                                                                                }
                                                                            }
                                                                            if (trim($min) == '') {
                                                                                echo ' <td style="color:#000;white-space: nowrap;font-family:monospace;width: 550px;font-size: 12px;vertical-align: top;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' ? $max : '') . '</td>';
                                                                            } else if (trim($max) == '') {
                                                                                echo ' <td style="color:#000;white-space: nowrap;font-family:monospace;width: 550px;font-size: 12px;vertical-align: top;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' ? $min : '') . '</td>';
                                                                            } else {
                                                                                echo ' <td style="color:#000;white-space: nowrap;font-family:monospace;width: 550px;font-size: 12px;vertical-align: top;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' ? ' (' . $min . ' - ' . $max . ')' : '') . '</td>';
                                                                            }
                                                                            echo '</tr>';
                                                                            $oldLaboTitle = $i['LaboItem']['title_item'];
                                                                        }
                                                                    } else {                                                                        
                                                                        if ($i['LaboItem']['id'] == "182" || $i['LaboItem']['id'] == "161" || $i['LaboItem']['id'] == "196") {
                                                                            $query = mysql_query("SELECT med.name,antd.resistance,antd.intermidiate,antd.sensible FROM antibiograms AS ant INNER JOIN antibiogram_details AS antd ON ant.id= antd.antibiogram_id INNER JOIN labo_medicines AS med ON med.id =antd.medicine_id  WHERE labo_request_id = '" . $laboRequest['id'] . "' AND labo_item_id=" . $i['LaboItem']['id']);
                                                                            $numRow = mysql_num_rows($query);
                                                                            if ($numRow != 0) {
                                                                                echo '<tr>';
                                                                                    echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 600px;padding-left:15px;font-size: 12px">' . ($i['LaboItem']['parent_id'] != NULL ? '&nbsp;&nbsp;&nbsp;&nbsp;' : '') . $i['LaboItem']['name'] . '</td>';
                                                                                echo '</tr>';
                                                                                echo '<tr>';
                                                                                    echo '<td colspan="4">';
                                                                                        echo '<table id="tableAddService" cellspacing="0" cellpadding="0" border="1" style="width: 100%;">';
                                                                                            echo '<tr>';
                                                                                                echo '<th style="font-size: 15px;border:1px solid">MEDICATION</th>';
                                                                                                echo '<th style="font-size: 15px;border:1px solid">RESISTANCE</th>';
                                                                                                echo '<th style="font-size: 15px;border:1px solid">INTERMIDAITE</th>';
                                                                                                echo '<th style="font-size: 15px;border:1px solid">SENSIBLE</th>';
                                                                                            echo '</tr>';
                                                                                            while ($result = mysql_fetch_array($query)) {
                                                                                                echo '<tr>';
                                                                                                    echo '<td style="font-size: 12px;text-align:left;padding-left:10px;">' . $result['name'] . '</td>';
                                                                                                    echo '<td style="font-size: 12px;text-align:center">' . $result['resistance'] . '</td>';
                                                                                                    echo '<td style="font-size: 12px;text-align:center">' . $result['intermidiate'] . '</td>';
                                                                                                    echo '<td style="border-right:1px solid;font-size: 12px;text-align:center">' . $result['sensible'] . '</td>';
                                                                                                echo '</tr>';
                                                                                            }
                                                                                        echo '</table>';
                                                                                    echo '</td>';
                                                                                echo '</tr>';
                                                                                echo '<tr><td>&nbsp;</td></tr>';
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                ?>                                    
                                                <?php
                                                $laboGroupIndex++;
                                            }
                                            echo "</table>";
                                            $categoryId = $laboItemCategory['LaboItemCategory']['id'];
                                            $query = mysql_query("SELECT comment FROM comment_category_results WHERE category_id = $categoryId AND labo_id=" . $data[1]['Labo']['id']);
                                            while ($results = mysql_fetch_array($query)) {
                                                $comment = $results['comment'];
                                                if (isset($comment) && $comment != "") {
                                                    ?>
                                                    <table>
                                                        <tr>
                                                            <td style="font-size: 12px;text-align: right;width: 140px;">Comment :</td>
                                                            <td  style="font-size: 12px;">
                                                                <?php
                                                                echo wordwrap($comment, 180, "<br />\n");
                                                                ?>                                            
                                                            </td>
                                                        </tr>
                                                    </table>                         
                                                    <?php
                                                }
                                            }
                                        echo '</div>';
                                        }
                                    ?>                    
                                </td>
                            </tr>
                        </table>  
                        <?php }?>
                    </div>
                    
                    <div class="clear"></div>
                    <br>
                    <h1 align="left" style="color:#000;font-size:12px;text-decoration:underline"><?php echo MENU_PRESCRIPTION;?></h1>
                    <?php                                         
                    $queryPrscription = mysql_query("SELECT * FROM orders WHERE orders.queue_doctor_id = {$consultation['QeuedDoctor']['id']}");
                    $resultPrescription = mysql_fetch_array($queryPrscription);
                    if(mysql_num_rows($queryPrscription)){
                    ?>
                        <p style="font-size: 11px; font-weight: bold;">
                            <?php echo TABLE_CREATED_BY;?>: 
                            <?php 
                            $name = "";
                            $query = mysql_query(" SELECT (SELECT name FROM employees WHERE id = user_employees.employee_id) AS name FROM users INNER JOIN user_employees ON user_employees.user_id = users.id WHERE users.id = '" . $user['User']['id'] . "' GROUP BY users.id LIMIT 0,1");
                            while ($row = mysql_fetch_array($query)) {
                                $name = $row['name'];
                            }
                            echo $name; 
                            ?>
                        </p>
                        <div id="contentDoctor">
                            <table class="table_print" style="border: none;">
                                <tr>
                                    <th class="first" style="font-size: 11px;"><?php echo TABLE_NO; ?></th>
                                    <th style="text-transform: uppercase; font-size: 11px;"><?php echo TABLE_SKU; ?></th>
                                    <th style="text-transform: uppercase; font-size: 11px; width: 20%;"><?php echo GENERAL_DESCRIPTION; ?></th>
                                    <th style="text-transform: uppercase; font-size: 11px; text-align: center;"><?php echo TABLE_QTY ?></th>
                                    <th style="text-transform: uppercase; font-size: 11px; text-align: center;"><?php echo TABLE_UOM; ?></th>
                                    <th style="text-transform: uppercase; font-size: 11px;width:7%; text-align: center;"><?php echo TABLE_NUM_DAYS; ?></th>
                                    <th style="text-transform: uppercase; font-size: 11px;width:7%; text-align: center;"><?php echo TABLE_MORNING; ?></th>
                                    <th style="text-transform: uppercase; font-size: 11px;width:7%; text-align: center;"><?php echo TABLE_AFTERNOON; ?></th>
                                    <th style="text-transform: uppercase; font-size: 11px;width:7%; text-align: center;"><?php echo TABLE_EVENING; ?></th>
                                    <th style="text-transform: uppercase; font-size: 11px;width:7%; text-align: center;"><?php echo TABLE_NIGHT; ?></th>
                                    <th style="text-transform: uppercase; font-size: 11px;width:15%; text-align: center;"><?php echo TABLE_NOTE; ?></th>
                                </tr>
                                <?php
                                $index = 0;
                                $queryPrscriptionDetail = mysql_query("SELECT *, products.name, products.code, uoms.abbr FROM order_details INNER JOIN products ON products.id = order_details.product_id INNER JOIN uoms ON uoms.id = order_details.qty_uom_id WHERE order_details.order_id = {$resultPrescription['id']}");
                                while ($orderDetail = mysql_fetch_array($queryPrscriptionDetail)) {

                                    // Check Name With Customer
                                    $productName = $orderDetail['name'];                               
                                ?>
                                    <tr>
                                        <td class="first" style="text-align: center; font-size: 11px;"><?php echo ++$index; ?></td>
                                        <td style="font-size: 11px;"><?php echo $orderDetail['code']; ?></td>
                                        <td style="font-size: 11px;"><?php echo $productName; ?></td>
                                        <td style="font-size: 11px; text-align: center;"><?php echo number_format($orderDetail['qty'], 0); ?></td>
                                        <td style="font-size: 11px; text-align: center;"><?php echo $orderDetail['abbr']; ?></td>
                                        <td style="font-size: 11px; text-align: center;"><?php echo $orderDetail['num_days']!="" ? $orderDetail['num_days'] : '-'; ?></td>
                                        <td style="font-size: 11px; text-align: center;">
                                            <?php 
                                                echo $orderDetail['morning']!="" ? $orderDetail['morning'] : '-'; 
                                                if($orderDetail['morning']!="" && $orderDetail['morning_use_id']!= ""){
                                                    $queryMedicineUse = mysql_query("SELECT name FROM treatment_uses WHERE id = {$orderDetail['morning_use_id']}");
                                                    while ($resultMedicineUse = mysql_fetch_array($queryMedicineUse)) {
                                                        echo '<br/>';
                                                        echo $resultMedicineUse['name'];                                    
                                                    }
                                                }
                                            ?>
                                        </td>
                                        <td style="text-align: right; font-size: 11px; text-align: center;">
                                            <?php 
                                                echo $orderDetail['afternoon']!="" ? $orderDetail['afternoon'] : '-'; 
                                                if($orderDetail['afternoon']!="" && $orderDetail['afternoon_use_id']!= ""){
                                                    $queryMedicineUse = mysql_query("SELECT name FROM treatment_uses WHERE id = {$orderDetail['afternoon_use_id']}");
                                                    while ($resultMedicineUse = mysql_fetch_array($queryMedicineUse)) {
                                                        echo '<br/>';
                                                        echo $resultMedicineUse['name'];                                    
                                                    }
                                                }
                                            ?>
                                        </td>
                                        <td style="text-align: right; font-size: 11px; text-align: center;">
                                            <?php 
                                                echo $orderDetail['evening']!="" ? $orderDetail['evening'] : '-'; 
                                                if($orderDetail['evening']!="" && $orderDetail['evening_use_id']!= ""){
                                                    $queryMedicineUse = mysql_query("SELECT name FROM treatment_uses WHERE id = {$orderDetail['evening_use_id']}");
                                                    while ($resultMedicineUse = mysql_fetch_array($queryMedicineUse)) {
                                                        echo '<br/>';
                                                        echo $resultMedicineUse['name'];                                    
                                                    }
                                                }
                                            ?>
                                        </td>
                                        <td style="text-align: right; font-size: 11px; text-align: center;">
                                            <?php 
                                                echo $orderDetail['night']!="" ? $orderDetail['night'] : '-';  
                                                if($orderDetail['night']!="" && $orderDetail['night_use_id']!= ""){
                                                    $queryMedicineUse = mysql_query("SELECT name FROM treatment_uses WHERE id = {$orderDetail['night_use_id']}");
                                                    while ($resultMedicineUse = mysql_fetch_array($queryMedicineUse)) {
                                                        echo '<br/>';
                                                        echo $resultMedicineUse['name'];                                    
                                                    }
                                                }
                                            ?>
                                        </td>
                                        <td style="text-align: right; font-size: 11px; text-align: left;"><?php echo $orderDetail['note']; ?></td>
                                    </tr>
                                <?php                
                                }

                                $queryPrscriptionMisc = mysql_query("SELECT order_miscs.*, uoms.abbr FROM order_miscs INNER JOIN uoms ON uoms.id =  order_miscs.qty_uom_id WHERE order_miscs.order_id = {$resultPrescription['id']}");
                                while ($orderMisc = mysql_fetch_array($queryPrscriptionMisc)) {                                                           
                                ?>
                                    <tr>
                                        <td class="first" style="text-align: center; font-size: 11px;"><?php echo ++$index; ?></td>
                                        <td style="font-size: 11px;"></td>
                                        <td style="font-size: 11px;"><?php echo $orderMisc['description']; ?></td>
                                        <td style="font-size: 11px; text-align: center;"><?php echo number_format($orderMisc['qty'], 0); ?></td>
                                        <td style="font-size: 11px; text-align: center;"><?php echo $orderMisc['abbr']; ?></td>
                                        <td style="font-size: 11px; text-align: center;"><?php echo $orderMisc['num_days']!="" ? $orderMisc['num_days'] : '-'; ?></td>
                                        <td style="font-size: 11px; text-align: center;">
                                            <?php
                                            echo $orderMisc['morning']!="" ? $orderMisc['morning'] : '-';
                                            if($orderMisc['morning']!="" && $orderMisc['morning_use_id']!= ""){
                                                $queryMedicineUse = mysql_query("SELECT name FROM treatment_uses WHERE id = {$orderMisc['morning_use_id']}");
                                                while ($resultMedicineUse = mysql_fetch_array($queryMedicineUse)) {
                                                    echo '<br/>';
                                                    echo $resultMedicineUse['name'];                                    
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td style="font-size: 11px; text-align: center;">
                                            <?php 
                                            echo $orderMisc['afternoon']!="" ? $orderMisc['afternoon'] : '-'; 
                                            if($orderMisc['afternoon']!="" && $orderMisc['afternoon_use_id']!= ""){
                                                $queryMedicineUse = mysql_query("SELECT name FROM treatment_uses WHERE id = {$orderMisc['afternoon_use_id']}");
                                                while ($resultMedicineUse = mysql_fetch_array($queryMedicineUse)) {
                                                    echo '<br/>';
                                                    echo $resultMedicineUse['name'];                                    
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td style="font-size: 11px; text-align: center;">
                                            <?php 
                                            echo $orderMisc['evening']!="" ? $orderMisc['evening'] : '-'; 
                                            if($orderMisc['evening']!="" && $orderMisc['evening_use_id']!= ""){
                                                $queryMedicineUse = mysql_query("SELECT name FROM treatment_uses WHERE id = {$orderMisc['evening_use_id']}");
                                                while ($resultMedicineUse = mysql_fetch_array($queryMedicineUse)) {
                                                    echo '<br/>';
                                                    echo $resultMedicineUse['name'];                                    
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td style="font-size: 11px; text-align: center;">
                                            <?php 
                                            echo $orderMisc['night']!="" ? $orderMisc['night'] : '-';  
                                            if($orderMisc['night']!="" && $orderMisc['night_use_id']!= ""){
                                                $queryMedicineUse = mysql_query("SELECT name FROM treatment_uses WHERE id = {$orderMisc['night_use_id']}");
                                                while ($resultMedicineUse = mysql_fetch_array($queryMedicineUse)) {
                                                    echo '<br/>';
                                                    echo $resultMedicineUse['name'];                                    
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td style="font-size: 11px; text-align: left;"><?php echo $orderMisc['note']; ?></td>
                                    </tr>
                                <?php
                                }
                                ?>                
                            </table>
                        </div>
                    <?php }?>   
                    <div class="clear"></div>
                </div>
            </div>
            <br>
            <div class="legend">                
                <div class="legend_title">
                    <label for="ConsultImageDermatology"><b><?php echo CONSULT_IMAGE_DERMATOLOGY; ?></b></label>
                </div>
                <div class="legend_content">
                    <input type="hidden" type="text" id="patient_id" name="patient_id" value="<?php echo $consultation['Patient']['id']; ?>" />
                    <input type="hidden" type="text" id="doctor_id" name="doctor_id" value="<?php echo $consultation['QeuedDoctor']['id'];?>" /> 
                    <input type="hidden" type="text" id="queue_id" name="queue_id" value="<?php echo $consultation['Queue']['id'];?>" />  
                    <canvas id="tools_sketch" width="800" height="450" style="background: url(<?php echo $this->webroot; ?>img/dermatology/queueid_<?php echo $consultation['Queue']['id']; ?>.jpg) no-repeat center center;"></canvas>                                                                                                      
                    <a href="#tools_sketch" data-tool="marker"><img alt="paint" src="<?php echo $this->webroot; ?>img/tool/pencil.png" /></a>&nbsp;&nbsp;&nbsp;
                    <a href="#tools_sketch" data-tool="eraser"><img alt="erase" src="<?php echo $this->webroot; ?>img/tool/eraser.png"  /></a>&nbsp;&nbsp;&nbsp;
                    <a href="#tools_sketch" data-download="png"><img alt="save" src="<?php echo $this->webroot; ?>img/tool/save.png" /></a>  
                    <br>
                    <p style="margin-left: 10px;">
                    <?php echo GENERAL_DESCRIPTION; ?>
                    </p>
                    <?php echo $this->Form->input('description_image', array('label' => false, 'type' => 'textarea', 'style' => 'width: 99%;', 'value' => $consultation['PatientConsultation']['description_image'])); ?>
                </div>
            </div>
            <br>
            <div class="legend">
                <div class="legend_title"><b><?php echo MENU_FOLLOW_UP_LABEL; ?></b></div>
                <div class="legend_content">
                    <div class="followup">
                    <?php
                    $query_followup = mysql_query("SELECT * FROM patient_followups WHERE is_active=1 AND patient_consultation_id=" . $consultation['PatientConsultation']['id']);
                    while ($data_followup = mysql_fetch_array($query_followup)) {
                        ?>
                        <h3 style="background-color: #e6eff0;">
                            <a href="#">
                                <?php echo $data_followup['created']; ?>
                            </a>
                        </h3>
                        
                        <div>
                            <div class="legend">
                                <div class="legend_title"><b><?php echo MENU_FOLLOW_UP_LABEL; ?></b></div>
                                <div class="legend_content"><?php echo $data_followup['followup']; ?></div>
                            </div>
                            <br />
                            <div class="legend">

                                <div class="legend_title"><b><?php echo TABLE_DAIGNOSTIC; ?></b></div>
                                <div class="legend_content"><?php echo $data_followup['diagnosis']; ?></div>
                            </div>
                            <br />
                            <div class="legend">
                                <div class="legend_title"><b><?php echo MENU_TREATMENT; ?></b></div>                                    
                                <div class="legend_content"><?php echo $this->Form->input('treatment', array('style' => 'width:99%' ,'readonly' => 'readonly', 'label' => false, 'type' => 'textarea', 'value' => $data_followup['treatment'])); ?></div>
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                    <br>
                    <?php
//                        if($display==""){ ?>
                        <div class="buttons">
                            <button type="submit" queuedDoctorId="<?php echo $consultation['PatientConsultation']['queued_doctor_id'];?>" name="<?php echo $consultation['PatientConsultation']['id']; ?>" title="<?php echo $consultation['Queue']['id'];?>" class="positive" id="btnFollowup<?php echo $tblRand; ?>">
                                <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
                                 <?php echo MENU_FOLLOW_UP_LABEL_ADD; ?>
                            </button>
                        </div>
                        <div style="clear: both;"></div>
                    <?php //} ?>
                   
                </div>
            </div> 
            
            <br><br>
<!--            <div class="buttons" style='<?php echo $display;?>'>
                <button type="submit" class="positive">
                    <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
                    <?php echo ACTION_SAVE; ?>
                </button>
                <img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" class="loading" style="display: none;" />
            </div>-->
            <div style="clear: both;"></div>

            <?php echo $this->Form->end(); ?>   
        </div>
        <?php
        $ind++;
    endforeach;
    ?>
</div>
<div id="dialog" title=""></div>
<div id="dialog9" title=""></div>
<div id="dialogPrint<?php echo $tblName;?>" title="" style="display: none;">
    <br />
    <center>
        <div class="buttons" style="display: inline-block;">
            <button type="button" id="btnPatientConsultation<?php echo $tblName;?>" class="positive">
                <img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/>
                <?php echo ACTION_PRINT; ?>
            </button>
        </div>
    </center>
</div>
<div id="patientConsultation" style="display: none;"></div>