
<script type="text/javascript">
    $(document).ready(function(){
        $(document).dblclick(function(){
            window.close();
        });
        $('#btnDisappearPrint').click(function(){                                    
            $("#optionCategory").hide();
            $("#btnDisappearPrint").hide();
            $(this).removeClass('noprint');            
            if($("#LaboCategory").val()==""){                
                $.ajax({
                    type: "POST",
                    url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/updateLaboStatus/",
                    beforeSend: function() {
                        $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner.gif');
                    },
                    success: function(printLaboResult) {                    
                        w=window.open();
                        w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                        w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                        w.document.write(printLaboResult);
                        w.document.close();
                        try
                        {
                            //Run some code here                                                                                                       
                            jsPrintSetup.setSilentPrint(1);
                            jsPrintSetup.printWindow(w);
                        }
                        catch(err)
                        {
                            //Handle errors here                                    
                            w.print();                                     
                        } 
                        w.close();
                    }
                });                
            }else{
                window.print();
                window.close();
            }
            
        });
        $("#LaboCategory").change(function(){             
            var category = $(this).val();            
            if(category==""){
                $(".boxTestBlood").show();            
            }else{
                $(".boxTestBlood").hide();
                $(".boxTestBlood[rel=" + category + "]").show();
            }            
        });
    
    });
</script>
<style type="text/css" media="screen">
    div.print-footer {display: none;}    
    .table_print td{ 
        border: none !important;
        line-height: 20px;
        padding: 0;
        margin: 0;
    }
</style>
<style type="text/css" media="print">
    div.print_doc { width:100%; }
    div.print-footer { display: block; width: 100%;}
    input[type="checkbox"] { transform:scale(1.6, 1.6);}
    #btnDisappearPrint { display: none;}
    table tr td{ font-size: 13px; }
    .table_print td{ 
        border: none !important;
        line-height: 20px;
        padding: 0;
        margin: 0;
    }
    @page
    {
        /*this affects the margin in the printer settings*/  
        margin: 5mm 7mm 5mm 10mm;
    }
    p{
        padding: 0 10px;
        margin: 0;
        font-size: 14px;
    }
    th{ font-weight: normal; }   
    h2{ font-size: 18px;}
    #signature {page-break-inside: avoid;}
</style>
<?php
$absolute_url = FULL_BASE_URL . Router::url("/", false);
require_once("includes/function.php");
echo $this->element('labo_item_category');
?>
<?php $sex = @$patient['Patient']['sex']; ?>
<div class="print_doc">    
    <?php 
        echo $this->element('print_header_labo'); 
        echo $this->element('print/print_patient_info_labo', array('patient' => $patient, 'requestDate' => $labo['Labo']['created'], 'code' => $labo['Labo']['id'])); 
   ?>
    <div>
        <div id="optionCategory">
            <?php echo $form->create('Labo'); ?>
            <?php echo $form->hidden('labo_id', array('name' => 'data[Labo][id]', 'value' => $labo['Labo']['id'])); ?>
            <table>
                <tr>
                    <td style="width:100px;">
                        <label for="LaboItemCategory"><?php echo GENERAL_CATEGORY; ?></label>
                    </td> 
                    <td style="width:12px;"><span>:</span></td>
                    <td><?php echo $this->Form->input('category', array('empty' => SELECT_OPTION, 'label' => false, 'style' => 'width:200px;')); ?></td>                    
                </tr>                        
            </table>
            </form>
        </div>
        <table class="table_print" style="width:100%; float: left; margin:0px;padding-left:10px;border: none;" align='center' id="print">
            <tbody>
            <tr>
                <td valign="top">                     
                    <!-- START -->
                    <?php $dob = $patient['Patient']['dob']; ?>
                    <?php
                    $oldLaboTitle = '';
                    $then_ts = strtotime($patient['Patient']['dob']);
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
                    <?php echo $form->create('Labo', array('action' => 'updateLaboStatus')); ?>
                    <?php echo $form->hidden('labo_id', array('name' => 'data[Labo][id]', 'value' => $labo['Labo']['id'])); ?>
                    <?php echo $form->hidden('queue_id', array('name' => 'data[QueuedLabo][id]', 'value' => $qPatient['QueuedLabo']['id'])); ?>
                    <?php foreach ($listLaboItemCategories as $laboItemCategory) { ?>
                        <div class="boxTestBlood" rel="<?php echo $laboItemCategory['LaboItemCategory']['id'] ?>">
                            <h1 align="center" style="color:#000;font-size:15px;text-decoration:underline"><?php echo $laboItemCategory['LaboItemCategory']['name']; ?></h1>                                                                       
                            <?php
                            $laboGroupIndex = 0;
                            echo "<table>";
                            echo '<tr>';
                                echo '<td style="color:green;font-size:15px;font-weight: bold;width: 380px;">Test Name</td>';
                                echo '<td style="color:green;font-size:15px;font-weight: bold;width: 145px;">Result</td>';
                                echo '<td style="color:green;font-size:15px;font-weight: bold;width: 115px;">Unit</td>';
                                echo '<td style="white-space: nowrap;color:green;font-size:15px;font-weight: bold;">Reference Ranges</td>';
                            echo '</tr>';
                            if ($laboItemCategory['LaboItemCategory']['id'] == 6 || $laboItemCategory['LaboItemCategory']['id'] == 7 || $laboItemCategory['LaboItemCategory']['id'] == 10 || $laboItemCategory['LaboItemCategory']['id'] == 9) {
                                $query = mysql_query("SELECT speciment_type FROM speciment_types WHERE labo_item_category_id = '" . $laboItemCategory['LaboItemCategory']['id'] . "' AND labo_id = " . $labo['Labo']['id']);
                                while ($specimentType = mysql_fetch_array($query)) {
                                    echo '<tr>';
                                        echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 600px;padding-left:15px;font-size: 14px">' . mb_str_pad('Specimen type', 32, '.', STR_PAD_RIGHT) . ':' . '</td>';
                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold">' . wordwrap($specimentType['speciment_type'], 16, "<br />\n") . '</td>';
                                    echo '<tr/>';
                                }
                            }
                            foreach ($labo['LaboRequest'] as $laboRequest) {
                                $item_requests = @unserialize($laboRequest['request']);
                                $item_results = @unserialize($laboRequest['result']);
                                ?>                                                        
                                <?php
                                if (!empty($laboItems)) {
                                    $items = array();
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
                                                    //if result is null                                                
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
                                                                echo '<td style="white-space: nowrap;color:#000;font-size:14px;padding-left:15px;">' . $dataLaboTitle[0] . ' </td>';
                                                                echo '</tr>';
                                                            }
                                                            echo '<tr>';
                                                            echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 600px;padding-left:30px;font-size: 14px">'
                                                                     . ($i['LaboItem']['parent_id'] != NULL ? '&nbsp;&nbsp;&nbsp;&nbsp;' : '') . mb_str_pad($i['LaboItem']['name'], 30, '.', STR_PAD_RIGHT) . ':';
                                                                     if($i['LaboItem']['description']!=""){
                                                                         echo '<p>' . nl2br($i['LaboItem']['description']) . '</p>';
                                                                     }
                                                            echo '</td>';
                                                            //echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 600px;padding-left:30px;font-size: 14px">' . ($i['LaboItem']['parent_id'] != NULL ? '&nbsp;&nbsp;&nbsp;&nbsp;' : '') . mb_str_pad($i['LaboItem']['name'], 30, '.', STR_PAD_RIGHT) . ':' . '<p>' . $i['LaboItem']['description'] . '</p></td>';
                                                            
                                                            if ($i['LaboItem']['normal_value_type'] == "Positive / Negative") {
                                                                echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px;font-weight: bold">' . ($item_results[$i['LaboItem']['id']][0] == 'Positive' ? 'Positive &nbsp;' . $item_results[$i['LaboItem']['id']][1] : "" . $item_results[$i['LaboItem']['id']][0] . "" ) . '</td>';
                                                            } else if ($i['LaboItem']['normal_value_type'] == "Free Style") {
                                                                if (isset($item_results[$i['LaboItem']['id']])) {
                                                                    $test = wordwrap($item_results[$i['LaboItem']['id']], 16, "<br />\n");
                                                                } else {
                                                                    $test = "";
                                                                }
                                                                echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px">' . $test . '</td>';
                                                                echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 300px;font-size: 14px"></td>';
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
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                            } else {
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                            }
                                                                        } else if ($var == ">=") {
                                                                            if ($result < $newVar) {
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                            } else {
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
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
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                } else {
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                }
                                                                            } else {
                                                                                if ($result < $newVar) {
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                } else {
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                }
                                                                            }
                                                                        }
                                                                    } else if ($item_results[$i['LaboItem']['id']] > $max || $item_results[$i['LaboItem']['id']] < $min) {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px;vertical-align: top;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                                    } else {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                                    }
                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 300px;font-size: 14px;vertical-align: top;">' . $type . '</td>';
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
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                            } else {
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                            }
                                                                        } else if ($var == ">=") {
                                                                            if ($result < $newVar) {
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                            } else {
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px;">' . wordwrap($result, 16, "<br />\n") . '</td>';
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
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                } else {
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                }
                                                                            } else {
                                                                                if ($result < $newVar) {
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                } else {
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                }
                                                                            }
                                                                        }
                                                                    } else if ($item_results[$i['LaboItem']['id']] > $max || $item_results[$i['LaboItem']['id']] < $min) {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px;vertical-align: top;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                                    } else {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                                    }
                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 300px;font-size: 14px;vertical-align: top;">' . $i['LaboItem']['item_unit'] . '</td>';
                                                                }
                                                            }
                                                            if (trim($min) == '') {
                                                                echo ' <td style="color:#000;white-space: nowrap;font-family:monospace;width: 550px;font-size: 14px;vertical-align: top;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' ? $max : '') . '</td>';
                                                            } else if (trim($max) == '') {
                                                                echo ' <td style="color:#000;white-space: nowrap;font-family:monospace;width: 550px;font-size: 14px;vertical-align: top;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' ? $min : '') . '</td>';
                                                            } else {
                                                                echo ' <td style="color:#000;white-space: nowrap;font-family:monospace;width: 550px;font-size: 14px;vertical-align: top;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' ? ' (' . $min . ' - ' . $max . ')' : '') . '</td>';
                                                            }
                                                            echo '</tr>';
                                                            $oldLaboTitle = $i['LaboItem']['title_item'];
                                                        } else {

                                                            echo '<tr>';
                                                            echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 600px;padding-left:15px;font-size: 14px">'
                                                                     . ($i['LaboItem']['parent_id'] != NULL ? '&nbsp;&nbsp;&nbsp;&nbsp;' : '') . mb_str_pad($i['LaboItem']['name'], 32, '.', STR_PAD_RIGHT) . ':';
                                                                     if($i['LaboItem']['description']!=""){
                                                                         echo '<p>' . nl2br($i['LaboItem']['description']) . '</p>';
                                                                     }
                                                            echo '</td>';
                                                            if ($i['LaboItem']['normal_value_type'] == "Positive / Negative") {
                                                                echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px;font-weight: bold">' . ($item_results[$i['LaboItem']['id']][0] == 'Positive' ? 'Positive &nbsp;' . $item_results[$i['LaboItem']['id']][1] : "" . $item_results[$i['LaboItem']['id']][0] . "" ) . '</td>';
                                                            } else if ($i['LaboItem']['normal_value_type'] == "Free Style") {
                                                                if (isset($item_results[$i['LaboItem']['id']])) {
                                                                    $test = wordwrap($item_results[$i['LaboItem']['id']], 16, "<br />\n");
                                                                } else {
                                                                    $test = "";
                                                                }
                                                                echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px">' . $test . '</td>';
                                                                echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 300px;font-size: 14px"></td>';
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
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                            } else {
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                            }
                                                                        } else if ($var == ">=") {
                                                                            if ($result < $newVar) {
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                            } else {
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
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
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                } else {
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                }
                                                                            } else {
                                                                                if ($result < $newVar) {
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                } else {
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                }
                                                                            }
                                                                        }
                                                                    } else if ($item_results[$i['LaboItem']['id']] > $max || $item_results[$i['LaboItem']['id']] < $min) {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px;vertical-align: top;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                                    } else {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                                    }
                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 300px;font-size: 14px;vertical-align: top;">' . $type . '</td>';
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
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                            } else {
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                            }
                                                                        } else if ($var == ">=") {
                                                                            if ($result < $newVar) {
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                            } else {
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px;">' . wordwrap($result, 16, "<br />\n") . '</td>';
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
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                } else {
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                }
                                                                            } else {
                                                                                if ($result < $newVar) {
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold;color: red; font-size: 14px;vertical-align: top;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                } else {
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                }
                                                                            }
                                                                        }
                                                                    } else if ($item_results[$i['LaboItem']['id']] > $max || $item_results[$i['LaboItem']['id']] < $min) {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px;vertical-align: top;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                                    } else {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                                    }
                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 300px;font-size: 14px;vertical-align: top;">' . $i['LaboItem']['item_unit'] . '</td>';
                                                                }
                                                            }
                                                            if (trim($min) == '') {
                                                                echo ' <td style="color:#000;white-space: nowrap;font-family:monospace;width: 550px;font-size: 14px;vertical-align: top;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' ? $max : '') . '</td>';
                                                            } else if (trim($max) == '') {
                                                                echo ' <td style="color:#000;white-space: nowrap;font-family:monospace;width: 550px;font-size: 14px;vertical-align: top;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' ? $min : '') . '</td>';
                                                            } else {
                                                                echo ' <td style="color:#000;white-space: nowrap;font-family:monospace;width: 550px;font-size: 14px;vertical-align: top;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' ? ' (' . $min . ' - ' . $max . ')' : '') . '</td>';
                                                            }
                                                            echo '</tr>';
                                                            $oldLaboTitle = $i['LaboItem']['title_item'];
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
                            $query = mysql_query("SELECT comment FROM comment_category_results WHERE category_id = $categoryId AND labo_id=" . $labo['Labo']['id']);
                            while ($results = mysql_fetch_array($query)) {
                                $comment = $results['comment'];
                                if (isset($comment) && $comment != "") {
                                    ?>
                                    <table>
                                        <tr>
                                            <td style="font-size: 14px;text-align: right;width: 140px;">Comment :</td>
                                            <td  style="font-size: 14px;">
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
            </tbody>
            <tfoot>
                <tr>
                    <td style="height :  70px">
                        <div class="print-footer" style="position: fixed; bottom: 0; text-align: center; width: 100%;">
                            <center style="font-size: 11px">
                              <table style="width:100% ; ">
                                <tr>
                                    <td style="font-size:10px ; width: 70% ;font-family:'Times New Roman'"><?php echo $this->data['Branch']['address_other'] ?></td>
                                    <td style="font-size:11px ; width: 30% ; font-family:'Times New Roman'">Tel : <?php echo $this->data['Branch']['telephone'] ?></td>
                                </tr>
                                <tr>
                                    <td style="font-size:10px ;font-family:'Times New Roman'"><?php //echo $this->data['Branch']['address'] ?></td>
                                    <td style="font-size:11px ;font-family:'Times New Roman'">Email : <?php echo $this->data['Branch']['email_address'] ?></td>
                                </tr>
                            </table>
                                
                            </center>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>      
        <div style="float:right; margin-right:50px;margin-top:50px;margin-bottom:50px;">
            <table id="signature">
                <tr>
                    <td style="text-align: center;">
                        <span style="color:#000;white-space: nowrap;font-family:monospace;font-size: 16px"><?php echo 'Examiner' ?></span>
                    </td>
                </tr>
                     <?php if($user['User']['signature_photo'] != ''){ ?>
                <tr>
                    <td style="text-align: center;">
                        <img alt="" <?php echo $user['User']['signature_photo'] != '' ? 'src="' . $this->webroot . 'public/signature_photo/' . $user['User']['signature_photo'] . '"' : ''; ?> style="width: 50px; height: 50px;" />
                    </td>
                </tr>
                <?php }else { ?>
                <tr>
                    <td><br/><br/><br/></td>
                </tr>
                <?php } ?>
                <tr>
                    <td style="text-align: center; display:none;">
                        <span style="color:#000;white-space: nowrap;font-family:monospace;font-size: 16px"> Lab. <?php echo $user['User']['first_name'] . " ". $user['User']['last_name'] ;  ?>  </span>
                    </td>
                </tr>
            </table>                    
            <br/>            
        </div>
    </div>
    <div class="clear"></div>
    <div style="float:left;width: 450px;">
        <button id='btnDisappearPrint' class='btnPrint'><?php __(ACTION_PRINT); ?></button>
    </div>
</div>