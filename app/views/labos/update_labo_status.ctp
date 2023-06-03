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
</style>
<script type="text/javascript">   
    try
    {
        jsPrintSetup.setOption('scaling', 100);
        jsPrintSetup.clearSilentPrint();
        jsPrintSetup.setOption('printBGImages', 1);
        jsPrintSetup.setOption('printBGColors', 1);
        jsPrintSetup.setSilentPrint(1);         
        jsPrintSetup.print();
        window.close();
    }
    catch (err)
    {
        //Default printing if jsPrintsetup is not available
        window.print();
        window.close();
    }
</script>
<?php
$absolute_url = FULL_BASE_URL . Router::url("/", false);
require_once("includes/function.php");
echo $this->element('labo_item_category');
$sex = @$patient['Patient']['sex'];
?>
<div class="print_doc">
    <?php echo $this->element('print_header_labo'); ?>
    <?php echo $this->element('print/print_patient_info_labo', array('patient' => $patient, 'requestDate' => $labo['Labo']['created'], 'code' => $labo['Labo']['id'])); ?>
    <div>        
        <table class="table_print"  style="width:100%; float: left; margin:0px;border: none;" align='center' id="print" class="print_labo">
            <tr>
                <td valign="top">
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
                                                                         echo '<p>' . $i['LaboItem']['description'] . '</p>';
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
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                            } else {
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                            }
                                                                        } else if ($var == ">=") {
                                                                            if ($result < $newVar) {
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
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
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                } else {
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                }
                                                                            } else {
                                                                                if ($result < $newVar) {
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                } else {
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                }
                                                                            }
                                                                        }
                                                                    } else if ($item_results[$i['LaboItem']['id']] > $max || $item_results[$i['LaboItem']['id']] < $min) {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                                    } else {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                                    }
                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 300px;font-size: 14px;">' . $type . '</td>';
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
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                            } else {
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                            }
                                                                        } else if ($var == ">=") {
                                                                            if ($result < $newVar) {
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
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
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                } else {
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                }
                                                                            } else {
                                                                                if ($result < $newVar) {
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                } else {
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                }
                                                                            }
                                                                        }
                                                                    } else if ($item_results[$i['LaboItem']['id']] > $max || $item_results[$i['LaboItem']['id']] < $min) {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                                    } else {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                                    }
                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 300px;font-size: 14px;">' . $i['LaboItem']['item_unit'] . '</td>';
                                                                }
                                                            }
                                                            if (trim($min) == '') {
                                                                echo ' <td style="color:#000;white-space: nowrap;font-family:monospace;width: 550px;font-size: 14px">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' ? $max : '') . '</td>';
                                                            } else if (trim($max) == '') {
                                                                echo ' <td style="color:#000;white-space: nowrap;font-family:monospace;width: 550px;font-size: 14px">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' ? $min : '') . '</td>';
                                                            } else {
                                                                echo ' <td style="color:#000;white-space: nowrap;font-family:monospace;width: 550px;font-size: 14px">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' ? ' (' . $min . ' - ' . $max . ')' : '') . '</td>';
                                                            }
                                                            echo '</tr>';
                                                            $oldLaboTitle = $i['LaboItem']['title_item'];
                                                        } else {

                                                            echo '<tr>';
                                                            echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 600px;padding-left:15px;font-size: 14px">'
                                                                     . ($i['LaboItem']['parent_id'] != NULL ? '&nbsp;&nbsp;&nbsp;&nbsp;' : '') . mb_str_pad($i['LaboItem']['name'], 32, '.', STR_PAD_RIGHT) . ':';
                                                                     if($i['LaboItem']['description']!=""){
                                                                         echo '<p>' . $i['LaboItem']['description'] . '</p>';
                                                                     }
                                                            echo '</td>';
//                                                            echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 600px;padding-left:15px;font-size: 14px">' . ($i['LaboItem']['parent_id'] != NULL ? '&nbsp;&nbsp;&nbsp;&nbsp;' : '') . mb_str_pad($i['LaboItem']['name'], 32, '.', STR_PAD_RIGHT) . ':' . '<p>' . $i['LaboItem']['description'] . '</p></td>';
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
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                            } else {
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                            }
                                                                        } else if ($var == ">=") {
                                                                            if ($result < $newVar) {
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
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
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                } else {
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                }
                                                                            } else {
                                                                                if ($result < $newVar) {
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                } else {
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                }
                                                                            }
                                                                        }
                                                                    } else if ($item_results[$i['LaboItem']['id']] > $max || $item_results[$i['LaboItem']['id']] < $min) {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                                    } else {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                                    }
                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 300px;font-size: 14px;">' . $type . '</td>';
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
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                            } else {
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                            }
                                                                        } else if ($var == ">=") {
                                                                            if ($result < $newVar) {
                                                                                echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
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
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                } else {
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                }
                                                                            } else {
                                                                                if ($result < $newVar) {
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                } else {
                                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px;">' . wordwrap($result, 16, "<br />\n") . '</td>';
                                                                                }
                                                                            }
                                                                        }
                                                                    } else if ($item_results[$i['LaboItem']['id']] > $max || $item_results[$i['LaboItem']['id']] < $min) {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-weight: bold; color: red; font-size: 14px">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                                    } else {
                                                                        echo '<td style="white-space: nowrap;font-family:monospace;width: 394px;font-size: 14px;">' . (isset($item_results[$i['LaboItem']['id']]) ? $item_results[$i['LaboItem']['id']] : '') . '</td>';
                                                                    }
                                                                    echo '<td style="white-space: nowrap;font-family:monospace;width: 300px;font-size: 14px;">' . $i['LaboItem']['item_unit'] . '</td>';
                                                                }
                                                            }
                                                            if (trim($min) == '') {
                                                                echo ' <td style="color:#000;white-space: nowrap;font-family:monospace;width: 550px;font-size: 14px">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' ? $max : '') . '</td>';
                                                            } else if (trim($max) == '') {
                                                                echo ' <td style="color:#000;white-space: nowrap;font-family:monospace;width: 550px;font-size: 14px">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' ? $min : '') . '</td>';
                                                            } else {
                                                                echo ' <td style="color:#000;white-space: nowrap;font-family:monospace;width: 550px;font-size: 14px">' . ($i['LaboItem']['normal_value_type'] != 'Positive / Negative' ? ' (' . $min . ' - ' . $max . ')' : '') . '</td>';
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
                                                                    echo '<td style="color:#000;white-space: nowrap;font-family:monospace;width: 600px;padding-left:15px;font-size: 14px">' . ($i['LaboItem']['parent_id'] != NULL ? '&nbsp;&nbsp;&nbsp;&nbsp;' : '') . $i['LaboItem']['name'] . '</td>';
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
                                                                                echo '<td style="font-size: 14px;text-align:left;padding-left:10px;">' . $result['name'] . '</td>';
                                                                                echo '<td style="font-size: 14px;text-align:center;">' . $result['resistance'] . '</td>';
                                                                                echo '<td style="font-size: 14px;text-align:center;">' . $result['intermidiate'] . '</td>';
                                                                                echo '<td style="border-right:1px solid;font-size: 14px;text-align:center">' . $result['sensible'] . '</td>';
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
        </table>
        <div style="float:right; margin-right:154px; margin-top: 200px;">
            <table>
                <tr>
                    <td>
                        <?php $time = substr($labo['Labo']['modified'], 11, 10);?>
                        <?php $date = $labo['Labo']['modified']; ?>
                        <?php list($year, $month, $day) = explode('-', substr($date, 0, 10)); ?>                            
                        <span style="font-size: 16px">ថ្ងៃ​ទី <?php echo $day ?> ខែ <?php echo $month ?> ឆ្នាំ <?php echo $year ?> &nbsp; <?php echo $time ?></span>        
                    </td>                    
                </tr>
                <tr>
                    <td style="text-align: center;">
                        <span style="color:#000;white-space: nowrap;font-family:monospace;font-size: 16px;font-style: italic;"><?php echo 'Lab Manager' ?></span>
                    </td>
                </tr>
            </table>                             
            <br/>            
        </div>
    </div>
</div>