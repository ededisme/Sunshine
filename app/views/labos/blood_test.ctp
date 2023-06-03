<?php
echo $this->element('prevent_multiple_submit');
$absolute_url = FULL_BASE_URL . Router::url("/", false);
echo $javascript->link('uninums.min');
?>
<?php
$sex = @$patient['Patient']['sex'];
require_once("includes/function.php");
echo $this->element('labo_item_category');
echo $javascript->link('jquery-dynamic-form', true);
$absolute_url = FULL_BASE_URL . Router::url("/", false);
?>
<style type="text/css">
    textarea{
        width: 400px; height: 70px;
    }
    input{
        height: 25px;
    }
</style>
<script type="text/javascript">
    $(document).ready(function() {
        //$(".chzn-select").chosen();
        $("#LaboCategory").chosen({width: 280});
        $("#site_id").chosen({width: 200});
        $("#PatientDoctorId").chosen({width: 200});
        
        // Prevent Key Enter
        preventKeyEnter();
        $("#LaboLaboResultSaveForm").validationEngine();
        $("#LaboLaboResultSaveForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSavePatient").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                var category = result.split(".*")[0];
                var queueId = result.split(".*")[1];
                $("#dialog").html('<div class="buttons"><button type="submit" class="positive printLaboResult" ><img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/><span class="txtPrintInvoice"><?php echo ACTION_PRINT_LABO_RESULT; ?></span></button></div>');
                $(".printLaboResult").click(function() {
                    if (category != "") {
                        $.ajax({
                            type: "POST",
                            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/savePrint/" + queueId + "/" + category,
                            beforeSend: function() {
                                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner.gif');
                            },
                            success: function(printLaboWithoutCategoryFrom) {
                                w = window.open();
                                w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                                w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                                w.document.write(printLaboWithoutCategoryFrom);
                                w.document.close();
                                try
                                {
                                    //Run some code here                                                                                                       
                                    jsPrintSetup.setSilentPrint(1);
                                    jsPrintSetup.printWindow(w);
                                }
                                catch (err)
                                {
                                    //Handle errors here                                    
                                    w.print();
                                }
                                w.close();
                                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                            }
                        });
                    } else {
                        $.ajax({
                            type: "POST",
                            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printLaboWithoutCategory/" + queueId,
                            beforeSend: function() {
                                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner.gif');
                            },
                            success: function(printLaboWithoutCategoryFrom) {
                                w = window.open();
                                w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                                w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                                w.document.write(printLaboWithoutCategoryFrom);
                                w.document.close();
                                try
                                {
                                    //Run some code here                                                                                                       
                                    jsPrintSetup.setSilentPrint(1);
                                    jsPrintSetup.printWindow(w);
                                }
                                catch (err)
                                {
                                    //Handle errors here                                    
                                    w.print();
                                }
                                w.close();
                                $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                            }
                        });
                    }

                });
                $("#dialog").dialog({
                    title: '<?php echo ACTION_PRINT_DOCTOR_LABO_FROM; ?>',
                    resizable: false,
                    modal: true,
                    width: 'auto',
                    height: 'auto',
                    position: 'center',
                    closeOnEscape: true,
                    open: function(event, ui) {
                        $(".ui-dialog-buttonpane").show();
                        $(".ui-dialog-titlebar-close").show();
                    },
                    close: function() {
                        $(this).dialog({close: function() {
                            }});
                        $(this).dialog("close");
                        $(".btnBackQueueLabo").click();
                    },
                    buttons: {
                        '<?php echo ACTION_CLOSE; ?>': function() {
                            $("meta[http-equiv='refresh']").attr('content', '0');
                            $(this).dialog("close");
                        }
                    }
                });
                $(".btnBackQueueLabo").click();
            }
        });

        $(".positive").live("change", function() {
            if ($(this).val() == "Positive") {
                $(this).parent().find(".positive-text").show();
            } else {
                $(this).parent().find(".positive-text").hide();
            }
        });
        $('#LaboNumberLab').focus();
        $('input').live("keypress", function(e) {
            /* ENTER PRESSED*/
            if (e.keyCode == 13) {
                /* FOCUS ELEMENT */
                var inputs = $(this).parents("form").eq(0).find(":input");
                var idx = inputs.index(this);

                if (idx == inputs.length - 1) {
                    inputs[0].select()
                } else {
                    inputs[idx + 1].focus(); //  handles submit buttons
                    inputs[idx + 1].select();
                }
                return false;
            }
        });

        $("#LaboCategory").change(function() {
            var category = $(this).val();
            if (category == "") {
                $(".boxTestBlood").show();
            } else {
                $(".boxTestBlood").hide();
                $(".boxTestBlood[rel=" + category + "]").show();
            }
        });
        $(".btnBackQueueLabo").click(function(event) {
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableQueueLabo.fnDraw(false);
            var rightPanel = $(this).parent().parent().parent();
            var leftPanel = rightPanel.parent().find(".leftPanel");
            rightPanel.hide();
            rightPanel.html("");
            leftPanel.show("slide", {direction: "left"}, 500);
        });
        
        //Hide Patinen Info
        $("#btnHidePatientInfo").click(function(){
            $("#patientInfo").hide();
            $("#showPatientInfo").show();
        });
        //Show Patinen Info
        $("#btnShowPatientInfo").click(function(){
            $("#patientInfo").show();
            $("#showPatientInfo").hide();
        });
        
        $(".btnAddType").click(function () {
            var id = $(this).attr('rel');
            var clone = $("#laboPdfFile" + id).find(".LaboPdfFileTr" + id + ":last").clone(true);
            clone.appendTo("#tableToModifyLaboPdfFile" + id);

            comboRefeshType(id);
            $("#laboPdfFile" + id).find(".LaboPdfFileTr" + id + ":last").find("td .btnRemoveType").show();
        });

        $(".btnRemoveType").click(function () {
            var id = $(this).attr('rel');
            $(this).closest(".LaboPdfFileTr" + id + "").remove();
            $("#laboPdfFile" + id).find(".LaboPdfFileTr" + id + ":last").find("td .btnAddType").show();
            if ($('#laboPdfFile' + id + ' .LaboPdfFileTr' + id).length == 1) {
                $("#laboPdfFile" + id).find(".LaboPdfFileTr" + id + ":last").find("td .btnRemoveType").hide();
            }
        });
        
    });
     
    function comboRefeshType(id) {
        $(".contract").each(function () {
            $("#laboPdfFile" + id).find(".contract:last").val("");
        });
    }
</script>
<div style="padding: 5px;border: 1px dashed #3C69AD;">
    <div class="buttons">
        <a href="" class="positive btnBackQueueLabo">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $form->create('Labo', array('action' => 'laboResultSave')); ?>                
<?php echo $form->hidden('queue_id', array('name' => 'data[QueuedLabo][id]', 'value' => $qPatient['QueuedLabo']['id'])); ?>
<div id="content_wrapper">
    <div class="child" style="margin-bottom: 10px;">
        <div class="body">
            <legend id="showPatientInfo" style="display:none;"><a href="#" id="btnShowPatientInfo" style="background: #CCCCCC; font-weight: bold;"><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?> [ Show ] </a> </legend>
            <fieldset id="patientInfo" style="padding: 5px; border: 1px dashed #3C69AD;">
                <legend><a href="#" id="btnHidePatientInfo" style="background: #CCCCCC; font-weight: bold;"> <?php echo MENU_PATIENT_MANAGEMENT_INFO; ?> [ Hide ] </a></legend>
                <?php echo $this->element('print/patient_info_print', array('patient' => $patient, 'requestDate' => $labo['Labo']['created'], 'code' => $labo['Labo']['id'])); ?>
            </fieldset>            
        </div>
    </div>
    <fieldset style="padding: 5px; border: 1px dashed #3C69AD;">
        <legend><?php __(OTHER_BLOOD_TEST); ?></legend>
        <div class="child">        
            <div class="body">
                <!-- START -->            
                <?php
                $oldLaboTitle = '';
                ?>
                <table style="width: 100%">                                                         
                    <tr>
                        <td style="width: 10%;"><label for="LaboItemCategory"><?php echo GENERAL_CATEGORY; ?></label></td>
                        <td><?php echo $this->Form->input('category', array('empty' => SELECT_OPTION, 'label' => false, 'style' => 'width:280px;')); ?></td>
                        <td style="display: none;"><?php echo $this->Form->input('number_lab', array('tabindex' => '1', 'label' => 'Number Lab ', 'style' => 'width:200px;')); ?></td>
                        <td style="display: none;"><label for="LaboSiteId"><?php echo 'Ward/Site'; ?><span class="red">*</span> :</label></td>
                        <td style="display: none;">
                            <select name="data[Labo][labo_site_id]" id="site_id" style="width:200px;" class="validate[required]">
                                <option value=""><?php echo SELECT_OPTION; ?></option>
                                <?php
                                foreach ($sites as $site) {
                                    if($site['LaboSite']['id']==1){
                                        $sleected = "selected = 'selected' " ;
                                    }
                                    ?>
                                    <option <?php echo $sleected;?> value="<?php echo $site['LaboSite']['id']; ?>"><?php echo $site['LaboSite']['name']; ?></option>                                                                                   
                                <?php } ?>
                            </select>
                        </td>
                        <td style="display: none;">
                            <label for="LaboDoctorName"><?php echo DOCTOR_DOCTOR; ?> <span class="red">*</span> :</label>
                            <select id="PatientDoctorId" name="data[Labo][doctor_id]" stye="width:200px;" class="validate[required]">
                                <option value=""><?php echo SELECT_OPTION; ?></option>
                                <?php
                                foreach ($doctors as $doctor) {
                                    $selected = "";
                                    if($qPatient['QueuedLabo']['doctor_id']==$doctor['User']['id']){
                                        $selected = 'selected="selected"';
                                    }
                                    ?>
                                    <option <?php echo $selected;?> value="<?php echo $doctor['User']['id']; ?>"><?php echo $doctor['Employee']['name']; ?></option>
                                    <?php
                                }
                                ?>
                            </select>  
                        </td>
                        <td style="width: 40%;">
                            <table id="laboPdfFile" style="width: 100%; padding: 10px;">
                                <tbody id="tableToModifyLaboPdfFile">
                                    <tr class="LaboPdfFileTr">
                                        <td style="width: 40%;"><label for="LaboPdfFile"><?php echo 'Materail File (.pdf) only'; ?> :</label></td>
                                        <td style="width: 50%;">
                                            <input class="contract" type="file" id="LaboPdfFile" name="data[Labo][file_pdf][]" />
                                        </td>
                                        <td style="width: 10%;">
                                            <img alt="" src="<?php echo $this->webroot; ?>img/button/plus.png" rel="" class="btnAddType" style="cursor: pointer;" />
                                            <img alt="" src="<?php echo $this->webroot; ?>img/button/cross.png" rel="" class="btnRemoveType" style="cursor: pointer;display: none;" />
                                        </td>
                                    </tr>     
                                </tbody>
                            </table>   
                        </td>
                    </tr>
                </table>
                <?php
                $then_ts = strtotime($qPatient['Patient']['dob']);
                $then_year = date('Y', $then_ts);
                $age = date('Y') - $then_year;
                if (strtotime('+' . $age . ' years', $then_ts) > time())
                    $age--;

                if ($age == 0) {
                    $then_year = date('m', $then_ts);
                    $month = date('m') - $then_year;
                    if (strtotime('+' . $month . ' month', $then_ts) > time())
                        $month--;
                    $dob = $month;
                }else {
                    $dob = $age * 12;
                }
                ?>
                <br/>
                <?php foreach ($listLaboItemCategories as $laboItemCategory) { ?>
                    <div class="boxTestBlood" rel="<?php echo $laboItemCategory['LaboItemCategory']['id'] ?>">
                        <h1 align="center" style="color:#000;font-size:16px"><?php echo $laboItemCategory['LaboItemCategory']['name']; ?></h1>
                        <table style="width:100%;">
                            <tr>
                                <td style="color:green;font-size: 18px;font-family:monospace;width: 450px;padding-left:0px;">Test Name</td>
                                <td style="color:green;font-size: 18px;font-family:monospace;width: 194px;">Result</td>                         
                                <td style="color:green;font-size: 18px;font-family:monospace;width: 200px;">Unit</td>
                                <td style="color:green;font-size: 18px;width: 190px;font-family:monospace;">Reference Ranges</td>
                            </tr>
                            <?php
                            $laboGroupIndex = 0;
                            $categoryId = $laboItemCategory['LaboItemCategory']['id'];
                            if ($laboItemCategory['LaboItemCategory']['id'] == 6 || $laboItemCategory['LaboItemCategory']['id'] == 7 || $laboItemCategory['LaboItemCategory']['id'] == 10 || $laboItemCategory['LaboItemCategory']['id'] == 9) {
                                $query = mysql_query("SELECT speciment_type FROM speciment_types WHERE labo_item_category_id = '" . $laboItemCategory['LaboItemCategory']['id'] . "' AND labo_id = " . $labo['Labo']['id']);
                                while ($specimentType = mysql_fetch_array($query)) {
                                    echo '<tr>';
                                    echo '<td style="font-family:monospace;width: 560px;padding-left:15px;">' . mb_str_pad('Specimen type', 72, '.', STR_PAD_RIGHT) . '</td>';
                                    echo '<td style="font-family:monospace;padding-top:0px;width: 350px;"><input style="width: 150px;" name="data[Labo][speciment_type][]" value="' . $specimentType['speciment_type'] . '" /></td>';
                                    echo '<td style="font-family:monospace;padding-top:0px;width: 350px;"><input type = "hidden" style="width: 150px;" name="data[Labo][category_id][]" value="' . $categoryId . '" /></td>';
                                    echo '<tr/>';
                                }
                            }

                            foreach ($labo['LaboRequest'] as $laboRequest) {
                                $item_requests = @unserialize($laboRequest['request']);
                                if (!empty($laboItems)) {
                                    $items = array();
                                    $title = array();
                                    foreach ($laboItems as $i) {
                                        if ($i['LaboItem']['parent_id'] == NULL) {
                                            $items[$i['LaboItem']['category']][] = $i;
                                        }
                                        foreach ($laboItems as $j) {
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
                                                                echo '<td style="white-space: nowrap;color:#000;font-size:14px;padding-left:15px;">' . $dataLaboTitle[0] . ' </td>';
                                                                echo '</tr>';
                                                            }
                                                            echo '<tr>';
                                                            echo '<td style="font-family:monospace;width: 560px;padding-left:15px;">' . ($i['LaboItem']['parent_id'] != NULL ? '&nbsp;&nbsp;&nbsp;&nbsp;' : '') . mb_str_pad($i['LaboItem']['name'], 52, '.', STR_PAD_RIGHT) . '</td>';
                                                            if ($i['LaboItem']['normal_value_type'] == "Positive / Negative" || $i['LaboItem']['normal_value_type'] == "Number") {
                                                                if ($i['LaboItem']['item_unit'] == "/mm3") {
                                                                    $type = "/mm<sup>3</sup>";
                                                                    echo '<td style="font-family:monospace;padding-top:0px;width: 250;vertical-align: top;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' ? '<input style="width: 150px;" name="data[Labo][laboItems][' . $laboGroupIndex . '][' . $i['LaboItem']['id'] . ']" value="' . (isset($results[$i['LaboItem']['id']]) ? $results[$i['LaboItem']['id']] : '') . '" />' : '<select style="width: 80px;" class="positive" name="data[Labo][laboItems][' . $laboGroupIndex . '][' . $i['LaboItem']['id'] . '][]"><option value="Positive">Positive</option><option value="Negative" ' . ($results[$i['LaboItem']['id']] == 'Negative' ? 'selected="selected"' : '') . '>Negative</option></select><br /><br /><input style="width: 150px" name="data[Labo][laboItems][' . $laboGroupIndex . '][' . $i['LaboItem']['id'] . '][]" class="positive-text" />') . '</td>';
                                                                    echo '<td style="vertical-align: top;">' . $type . '</td>';
                                                                } else {
                                                                    echo '<td style="font-family:monospace;width: 250;vertical-align: top;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' ? '<input style="width: 150px;" name="data[Labo][laboItems][' . $laboGroupIndex . '][' . $i['LaboItem']['id'] . ']" value="' . (isset($results[$i['LaboItem']['id']]) ? $results[$i['LaboItem']['id']] : '') . '" />' : '<select style="width: 80px;" class="positive" name="data[Labo][laboItems][' . $laboGroupIndex . '][' . $i['LaboItem']['id'] . '][]"><option value="Positive">Positive</option><option value="Negative" ' . ($results[$i['LaboItem']['id']] == 'Negative' ? 'selected="selected"' : '') . '>Negative</option></select><br /><br /><input style="width: 150" name="data[Labo][laboItems][' . $laboGroupIndex . '][' . $i['LaboItem']['id'] . '][]" class="positive-text" />') . '</td>';
                                                                    echo '<td style="vertical-align: top;">' . $i['LaboItem']['item_unit'] . '</td>';
                                                                }
                                                            } else if ($i['LaboItem']['normal_value_type'] == "Free Style") {
                                                                echo '<td style="font-family:monospace;width: 250;vertical-align: top;"><input style="width: 150px;" name="data[Labo][laboItems][' . $laboGroupIndex . '][' . $i['LaboItem']['id'] . ']" /></td>';
                                                                echo '<td>&nbsp;</td>';
                                                            }
                                                            if (trim($min) == '') {
                                                                echo ' <td style="width: 250;font-family:monospace;vertical-align: top;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' && $i['LaboItem']['normal_value_type'] != 'Free Style' ? '('. $max . ')' : '') . '</td>';
                                                                echo '<td>&nbsp;</td>';
                                                            } else if (trim($max) == '') {
                                                                echo ' <td style="width: 250;font-family:monospace;vertical-align: top;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' && $i['LaboItem']['normal_value_type'] != 'Free Style' ? '(' . $min. ')' : '') . '</td>';
                                                                echo '<td>&nbsp;</td>';
                                                            } else {
                                                                echo ' <td style="width: 250;font-family:monospace;vertical-align: top;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' && $i['LaboItem']['normal_value_type'] != 'Free Style' ? ' (' . $min . ' - ' . $max . ')' : '') . '</td>';
                                                                echo '<td>&nbsp;</td>';
                                                            }
                                                            echo '</tr>';
                                                            $oldLaboTitle = $i['LaboItem']['title_item'];
                                                        } else {
                                                            echo '<tr>';
                                                            echo '<td style="font-family:monospace;width: 560px;padding-left:15px;">' . ($i['LaboItem']['parent_id'] != NULL ? '&nbsp;&nbsp;&nbsp;&nbsp;' : '') . mb_str_pad($i['LaboItem']['name'], 52, '.', STR_PAD_RIGHT) .  '</td>';
                                                            if ($i['LaboItem']['normal_value_type'] == "Positive / Negative" || $i['LaboItem']['normal_value_type'] == "Number") {
                                                                if ($i['LaboItem']['item_unit'] == "/mm3") {
                                                                    $type = "/mm<sup>3</sup>";
                                                                    echo '<td style="font-family:monospace;padding-top:0px;width: 250;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative'  ? '<input style="width: 150px;" name="data[Labo][laboItems][' . $laboGroupIndex . '][' . $i['LaboItem']['id'] . ']" value="' . (isset($results[$i['LaboItem']['id']]) ? $results[$i['LaboItem']['id']] : '') . '" />' : '<select style="width: 80px;" class="positive" name="data[Labo][laboItems][' . $laboGroupIndex . '][' . $i['LaboItem']['id'] . '][]"><option value="Positive">Positive</option><option value="Negative" ' . ($results[$i['LaboItem']['id']] == 'Negative' ? 'selected="selected"' : '') . '>Negative</option></select><br /><br /><input style="width: 150px" name="data[Labo][laboItems][' . $laboGroupIndex . '][' . $i['LaboItem']['id'] . '][]" class="positive-text" />') . '</td>';
                                                                    echo '<td style="vertical-align: top;">' . $type . '</td>';
                                                                } else {
                                                                    echo '<td style="font-family:monospace;width: 250;vertical-align: top;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' ? '<input style="width: 150px;" name="data[Labo][laboItems][' . $laboGroupIndex . '][' . $i['LaboItem']['id'] . ']" value="' . (isset($results[$i['LaboItem']['id']]) ? $results[$i['LaboItem']['id']] : '') . '" />' : '<select style="width: 80px;" class="positive" name="data[Labo][laboItems][' . $laboGroupIndex . '][' . $i['LaboItem']['id'] . '][]"><option value="Positive">Positive</option><option value="Negative" ' . ($results[$i['LaboItem']['id']] == 'Negative' ? 'selected="selected"' : '') . '>Negative</option></select><br /><br /><input style="width: 150" name="data[Labo][laboItems][' . $laboGroupIndex . '][' . $i['LaboItem']['id'] . '][]" class="positive-text" />') . '</td>';
                                                                    echo '<td style="vertical-align: top;">' . $i['LaboItem']['item_unit'] . '</td>';
                                                                }
                                                            } else if ($i['LaboItem']['normal_value_type'] == "Free Style") {
                                                                echo '<td style="font-family:monospace;width: 250;vertical-align: top;"><input style="width: 150px;" name="data[Labo][laboItems][' . $laboGroupIndex . '][' . $i['LaboItem']['id'] . ']" /></td>';
                                                                echo '<td>&nbsp;</td>';
                                                            }
                                                            if (trim($min) == '') {
                                                                echo ' <td style="width: 250;font-family:monospace;vertical-align: top;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' && $i['LaboItem']['normal_value_type'] != 'Free Style' ? '('.$max.')' : '') . '</td>';
                                                                echo '<td>&nbsp;</td>';
                                                            } else if (trim($max) == '') {
                                                                echo ' <td style="width: 250;font-family:monospace;vertical-align: top;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' && $i['LaboItem']['normal_value_type'] != 'Free Style' ? '('.$min .')': '') . '</td>';
                                                                echo '<td>&nbsp;</td>';
                                                            } else {
                                                                echo ' <td style="width: 250;font-family:monospace;vertical-align: top;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' && $i['LaboItem']['normal_value_type'] != 'Free Style' ? ' (' . $min . ' - ' . $max . ')' : '') . '</td>';
                                                                echo '<td>&nbsp;</td>';
                                                            }
                                                            echo '</tr>';
                                                            if($i['LaboItem']['description']!=""){
                                                            echo '<tr>'
                                                                    . '<td colspan="4">'
                                                                        . '<p>' . nl2br($i['LaboItem']['description']) . '</p>'
                                                                    . '</td>'
                                                                . '</tr>';
                                                            }
                                                            $oldLaboTitle = $i['LaboItem']['title_item'];
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
                            ?>
                            <td style="width: 30%;">
                                <label style="margin-left: 15px;" for="LaboComment"><?php echo 'Comment'; ?> : </label>                        
                                <input name="data[Labo][comment][]" type="text" style="margin-left: 10px; width: 450px;" id="LaboComment"/>
                                <input type="hidden" name="data[Labo][categoryId][]" value="<?php echo $laboItemCategory['LaboItemCategory']['id'] ?>"/>
                            </td>
                            <?php
                            echo '</div>';
                        }
                        ?>
                        <input type="hidden" name="data[Labo][id]" value="<?php echo $labo['Labo']['id'] ?>"/>
                </div>            
            </div>
    </fieldset>    
</div>
<div class="clear"></div>
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <span class="txtSavePatient"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<?php echo $this->Form->end(); ?>
