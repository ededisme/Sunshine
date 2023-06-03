<?php
$sex = @$patient['Patient']['sex'];
require_once("includes/function.php");
echo $this->element('labo_item_category');
?>
<script type="text/javascript">
    $(document).ready(function() {
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
</script>
<div id="content_wrapper">
    <div class="child" style="margin-bottom: 10px;">
        <div class="body">
            <legend id="showPatientInfo" style="display:none;"><a href="#" id="btnShowPatientInfo" style="background: #CCCCCC; font-weight: bold;"><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?> [ Show ] </a> </legend>
            <fieldset id="patientInfo" style="padding: 5px;border: 1px dashed #3C69AD;">
                <legend><a href="#" id="btnHidePatientInfo" style="background: #CCCCCC; font-weight: bold;"> <?php echo MENU_PATIENT_MANAGEMENT_INFO;?> [ Hide ] </a> </legend>
                <?php echo $this->element('print/print_patient_info_labo', array('patient' => $patient, 'requestDate' => $labo['Labo']['created'], 'code' => $labo['Labo']['id'])); ?>
            </fieldset>
        </div>
    </div>
    <fieldset style="padding: 5px;border: 1px dashed #3C69AD;">
        <legend style="background: #EDEDED; font-weight: bold;"><?php __(OTHER_BLOOD_TEST); ?></legend>
        <div class="child">        
            <div class="body"> 
                <table style="width: 50%;">
                    <?php
                    $queryMaterail = mysql_query("SELECT * FROM  labo_files WHERE labo_id='" . $labo['Labo']['id'] . "' AND status = 1 ORDER BY created ASC");
                    if (mysql_num_rows($queryMaterail)) {
                        ?>
                        <tr>
                            <th style="width:30%;">Date Update</th>
                            <th>Materail File</th>
                            <th>&nbsp;</th>
                        </tr>
                        <?php
                    }
                    while ($resultMaterail = mysql_fetch_array($queryMaterail)) {
                        echo '<tr class="labo_pdf_' . $resultMaterail['id'] . '">';
                        echo '<td style="width:30%;">' . $resultMaterail['created'] . '</td>';
                        echo '<td><a target="_blank" href="' . $this->webroot . 'public/labo_pdf/' . $resultMaterail['file'] . '">' . $resultMaterail['file'] . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </table>
                <!-- START -->
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
                <?php foreach ($listLaboItemCategories as $laboItemCategory) { ?>

                    <h1 align="center" style="color:#000;font-size:16px;text-decoration:underline"><?php echo $laboItemCategory['LaboItemCategory']['name']; ?></h1>
                    <table style="width:100%;">
                        <tr>
                            <td style="color:green;font-size: 18px;font-family:monospace;width: 450px;padding-left:0px;">Test Name</td>
                            <td style="color:green;font-size: 18px;font-family:monospace;width: 194px;">Result</td>                         
                            <td style="color:green;font-size: 18px;font-family:monospace;width: 200px;">Unit</td>
                            <td style="color:green;font-size: 18px;width: 190px;font-family:monospace;">Reference Ranges</td>
                        </tr>                   
                    <?php
                   
                    if ($laboItemCategory['LaboItemCategory']['id'] == 6 || $laboItemCategory['LaboItemCategory']['id'] == 7 || $laboItemCategory['LaboItemCategory']['id'] == 10 || $laboItemCategory['LaboItemCategory']['id'] == 9) {
                        $query = mysql_query("SELECT speciment_type FROM speciment_types WHERE labo_item_category_id = '" . $laboItemCategory['LaboItemCategory']['id'] . "' AND labo_id = " . $labo['Labo']['id']);
                        while ($specimentType = mysql_fetch_array($query)) {
                            echo '<tr>';
                                echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 600px;padding-left:15px;font-size: 14px">' . mb_str_pad('Specimen type', 72, '.', STR_PAD_RIGHT) . ':' . '</td>';
                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold">' . wordwrap($specimentType['speciment_type'], 16, "<br />\n") . '</td>';
                            echo '<tr/>';
                        }
                    }
                    $laboGroupIndex = 0;
                    //debug($labo['LaboRequest']);
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
                                        //debug($item_requests);
                                        if (in_array($i['LaboItem']['id'], $item_requests) || in_array($i['LaboItem']['parent_id'], $item_requests)) {
                                            //echo 'x';
                                            if (++$index == 1) {
                                                
                                            }
                                            $min = "";
                                            $max = "";
                                            $labo_item_id = $i['LaboItem']['id'];
//                                            if ($i['LaboItem']['id'] == "182" || $i['LaboItem']['id'] == "161" || $i['LaboItem']['id'] == "196") {
//                                                $query = mysql_query("SELECT med.name,antd.resistance,antd.intermidiate,antd.sensible FROM antibiograms AS ant INNER JOIN antibiogram_details AS antd ON ant.id= antd.antibiogram_id INNER JOIN labo_medicines AS med ON med.id =antd.medicine_id  WHERE labo_request_id = '" . $laboRequest['id'] . "' AND labo_item_id=" . $i['LaboItem']['id']);
//                                                $numRow = mysql_num_rows($query);
//                                                if ($numRow != 0) {
//                                                    echo '<tr>';
//                                                        echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 600px;padding-left:15px;font-size: 14px">' . ($i['LaboItem']['parent_id'] != NULL ? '&nbsp;&nbsp;&nbsp;&nbsp;' : '') . $i['LaboItem']['name'] . '</td>';
//                                                    echo '</tr>';
//                                                    echo '<tr>';
//                                                        echo '<td colspan="4">';
//                                                            echo '<table id="tableAddService" cellspacing="0" cellpadding="0" border="1" style="width: 100%;">';
//                                                                echo '<tr>';
//                                                                    echo '<th style="font-size: 15px;border:1px solid">MEDICATION</th>';
//                                                                    echo '<th style="font-size: 15px;border:1px solid">RESISTANCE</th>';
//                                                                    echo '<th style="font-size: 15px;border:1px solid">INTERMIDAITE</th>';
//                                                                    echo '<th style="font-size: 15px;border:1px solid">SENSIBLE</th>';
//                                                                echo '</tr>';
//                                                                while ($result = mysql_fetch_array($query)) {
//                                                                    echo '<tr>';
//                                                                        echo '<td style="font-size: 14px;text-align:left;padding-left:10px;">' . $result['name'] . '</td>';
//                                                                        echo '<td style="font-size: 14px;text-align:center">' . $result['resistance'] . '</td>';
//                                                                        echo '<td style="font-size: 14px;text-align:center">' . $result['intermidiate'] . '</td>';
//                                                                        echo '<td style="border-right:1px solid;font-size: 14px;text-align:center">' . $result['sensible'] . '</td>';
//                                                                    echo '</tr>';
//                                                                }
//                                                            echo '</table>';
//                                                        echo '</td>';
//                                                    echo '</tr>';
//                                                    echo '<tr><td>&nbsp;</td></tr>';
//                                                }
//                                            } 
//                                            else {
                                                
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
                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 450px;padding-left:15px">' 
                                                                . ($i['LaboItem']['parent_id'] != NULL ? '&nbsp;&nbsp;&nbsp;&nbsp;' : '') . mb_str_pad($i['LaboItem']['name'], 72, '.', STR_PAD_RIGHT) . ':';
//                                                                if($i['LaboItem']['description']!=""){
//                                                                    echo '<p>' . nl2br($i['LaboItem']['description']) . '</p>';
//                                                                }
                                                    echo '</td>';
                                                    if ($i['LaboItem']['normal_value_type'] == "Positive / Negative") {
                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 400px;font-weight: bold">' . ($item_results[$i['LaboItem']['id']][0] == 'Positive' ? '<b>Positive</b> &nbsp;' . $item_results[$i['LaboItem']['id']][1] : "<b>" . $item_results[$i['LaboItem']['id']][0] . "</b>" ) . '</td>';
                                                    } else if ($i['LaboItem']['normal_value_type'] == "Free Style") {
                                                        if (isset($item_results[$i['LaboItem']['id']])) {
                                                            $test = wordwrap($item_results[$i['LaboItem']['id']], 16, "<br />\n");
                                                        } else {
                                                            $test = "";
                                                        }
                                                        echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 194px;font-size: 14px">' . $test . '</td>';
                                                        echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 200px; vertical-align: top;font-size: 14px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
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
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;font-weight: bold; vertical-align: top;">' . $result . '</td>';
                                                                    } else {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;">' . $result . '</td>';
                                                                    }
                                                                } else if ($var == ">=") {
                                                                    if ($result < $newVar) {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;font-weight: bold; vertical-align: top;">' . $result . '</td>';
                                                                    } else {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;">' . $result . '</td>';
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
                                                                            echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;font-weight: bold; vertical-align: top;">' . $result . '</td>';
                                                                        } else {
                                                                            echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;">' . $result . '</td>';
                                                                        }
                                                                    } else {
                                                                        if ($result < $newVar) {
                                                                            echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;font-weight: bold; vertical-align: top;">' . $result . '</td>';
                                                                        } else {
                                                                            echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;">' . $result . '</td>';
                                                                        }
                                                                    }
                                                                }
                                                            } else if ($item_results[$i['LaboItem']['id']] > $max || $item_results[$i['LaboItem']['id']] < $min) {
                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;font-weight: bold; vertical-align: top;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                            } else {
                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                            }
                                                            echo '<td style="white-space: nowrap;font-family:monospace;width: 200px; vertical-align: top;">' . $type . '</td>';
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
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;font-weight: bold; vertical-align: top;">' . $result . '</td>';
                                                                    } else {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;">' . $result . '</td>';
                                                                    }
                                                                } else if ($var == ">=") {
                                                                    if ($result < $newVar) {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;font-weight: bold; vertical-align: top;">' . $result . '</td>';
                                                                    } else {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;">' . $result . '</td>';
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
                                                                            echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;font-weight: bold; vertical-align: top;">' . $result . '</td>';
                                                                        } else {
                                                                            echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;">' . $result . '</td>';
                                                                        }
                                                                    } else {
                                                                        if ($result < $newVar) {
                                                                            echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;font-weight: bold; vertical-align: top;">' . $result . '</td>';
                                                                        } else {
                                                                            echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;">' . $result . '</td>';
                                                                        }
                                                                    }
                                                                }
                                                            } else if ($item_results[$i['LaboItem']['id']] > $max || $item_results[$i['LaboItem']['id']] < $min) {
                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;font-weight: bold; vertical-align: top;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                            } else {
                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                            }
                                                            echo '<td style="white-space: nowrap;font-family:monospace;width: 200px; vertical-align: top;">' . $i['LaboItem']['item_unit'] . '</td>';
                                                        }
                                                    }
                                                    if (trim($min) == '') {
                                                        echo ' <td style="white-space: nowrap;font-family:monospace;width: 190px; vertical-align: top;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' && $i['LaboItem']['normal_value_type'] != 'Free Style' ? '(' .$max . ')' : '') . '</td>';
                                                    } else if (trim($max) == '') {
                                                        echo ' <td style="white-space: nowrap;font-family:monospace;width: 190px; vertical-align: top;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' && $i['LaboItem']['normal_value_type'] != 'Free Style' ? '('. $min. ')' : '') . '</td>';
                                                    } else {
                                                        echo ' <td style="white-space: nowrap;font-family:monospace;width: 190px; vertical-align: top;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' && $i['LaboItem']['normal_value_type'] != 'Free Style' ? ' (' . $min . ' - ' . $max . ')' : '') . '</td>';
                                                    }
                                                    echo '</tr>';
                                                    $oldLaboTitle = $i['LaboItem']['title_item'];
                                                } else {
                                                    echo '<tr>';
                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 450px;padding-left:15px">' . ($i['LaboItem']['parent_id'] != NULL ? '&nbsp;&nbsp;&nbsp;&nbsp;' : '') . mb_str_pad($i['LaboItem']['name'], 72, '.', STR_PAD_RIGHT) . ':';
//                                                            if($i['LaboItem']['description']!=""){
//                                                                echo '<p>' . nl2br($i['LaboItem']['description']) . '</p>';
//                                                            }
                                                    echo '</td>';
                                                    if ($i['LaboItem']['normal_value_type'] == "Positive / Negative") {
                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 400px;font-weight: bold">' . ($item_results[$i['LaboItem']['id']][0] == 'Positive' ? '<b>Positive</b> &nbsp;' . $item_results[$i['LaboItem']['id']][1] : "<b>" . $item_results[$i['LaboItem']['id']][0] . "</b>" ) . '</td>';
                                                    } else if ($i['LaboItem']['normal_value_type'] == "Free Style") {
                                                        if (isset($item_results[$i['LaboItem']['id']])) {
                                                            $test = wordwrap($item_results[$i['LaboItem']['id']], 16, "<br />\n");
                                                        } else {
                                                            $test = "";
                                                        }
                                                        echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 194px;font-size: 14px">' . $test . '</td>';
                                                        echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 200px; vertical-align: top;font-size: 14px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
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
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;font-weight: bold; vertical-align: top;">' . $result . '</td>';
                                                                    } else {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;">' . $result . '</td>';
                                                                    }
                                                                } else if ($var == ">=") {
                                                                    if ($result < $newVar) {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;font-weight: bold; vertical-align: top;">' . $result . '</td>';
                                                                    } else {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;">' . $result . '</td>';
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
                                                                            echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;font-weight: bold; vertical-align: top;">' . $result . '</td>';
                                                                        } else {
                                                                            echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;">' . $result . '</td>';
                                                                        }
                                                                    } else {
                                                                        if ($result < $newVar) {
                                                                            echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;font-weight: bold; vertical-align: top;">' . $result . '</td>';
                                                                        } else {
                                                                            echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;">' . $result . '</td>';
                                                                        }
                                                                    }
                                                                }
                                                            } else if ($item_results[$i['LaboItem']['id']] > $max || $item_results[$i['LaboItem']['id']] < $min) {
                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;font-weight: bold; vertical-align: top;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                            } else {
                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                            }
                                                            echo '<td style="white-space: nowrap;font-family:monospace;width: 200px; vertical-align: top;">' . $type . '</td>';
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
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;font-weight: bold; vertical-align: top;">' . $result . '</td>';
                                                                    } else {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;">' . $result . '</td>';
                                                                    }
                                                                } else if ($var == ">=") {
                                                                    if ($result < $newVar) {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;font-weight: bold; vertical-align: top;">' . $result . '</td>';
                                                                    } else {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;">' . $result . '</td>';
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
                                                                            echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;font-weight: bold; vertical-align: top;">' . $result . '</td>';
                                                                        } else {
                                                                            echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;">' . $result . '</td>';
                                                                        }
                                                                    } else {
                                                                        if ($result < $newVar) {
                                                                            echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;font-weight: bold; vertical-align: top;">' . $result . '</td>';
                                                                        } else {
                                                                            echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;">' . $result . '</td>';
                                                                        }
                                                                    }
                                                                }
                                                            } else if ($item_results[$i['LaboItem']['id']] > $max || $item_results[$i['LaboItem']['id']] < $min) {
                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;font-weight: bold; vertical-align: top;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                            } else {
                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 194px;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                            }
                                                            echo '<td style="white-space: nowrap;font-family:monospace;width: 200px; vertical-align: top;">' . $i['LaboItem']['item_unit'] . '</td>';
                                                        }
                                                    }
                                                    if (trim($min) == '') {
                                                        echo ' <td style="white-space: nowrap;font-family:monospace;width: 190px; vertical-align: top;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' && $i['LaboItem']['normal_value_type'] != 'Free Style' ? '('. $max . ')' : '') . '</td>';
                                                    } else if (trim($max) == '') {
                                                        echo ' <td style="white-space: nowrap;font-family:monospace;width: 190px; vertical-align: top;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' && $i['LaboItem']['normal_value_type'] != 'Free Style' ? '('. $min. ')' : '') . '</td>';
                                                    } else {
                                                        echo ' <td style="white-space: nowrap;font-family:monospace;width: 190px; vertical-align: top;">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' && $i['LaboItem']['normal_value_type'] != 'Free Style' ? ' (' . $min . ' - ' . $max . ')' : '') . '</td>';
                                                    }
                                                    echo '</tr>';
                                                    if($i['LaboItem']['description']!=""){
                                                            echo '<tr>'
                                                                    . '<td></td><td></td><td></td><td>'
                                                                        . '<p>' . nl2br($i['LaboItem']['description']) . '</p>'
                                                                    . '</td>'
                                                                . '</tr>';
                                                        }
                                                    $oldLaboTitle = $i['LaboItem']['title_item'];
                                                }
//                                            }
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
                                    <td style="text-align: right;width: 140px;">Comment :</td>
                                    <td><span style="font-family:'Times New Roman;', Times, serif;white-space: nowrap;font-size: 14px">
                                            <?php
                                            echo wordwrap($comment, 180, "<br />\n");
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>                    
                            <?php
                        }
                    }
                }
                ?>         
            </div>
        </div>
    </fieldset>
</div>
