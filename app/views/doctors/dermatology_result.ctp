<?php
$absolute_url = FULL_BASE_URL . Router::url("/", false);
?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(".table td:first-child").addClass('first');        
        $("#btnPrint").click(function(){
            $(".dataTables_length").hide();
            $(".dataTables_filter").hide();
            $(".dataTables_paginate").hide();
            w=window.open();
            w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
            w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
            w.document.write($(".print_area").html());
            w.document.close();
            w.print();
            w.close();
            $(".dataTables_length").show();
            $(".dataTables_filter").show();
            $(".dataTables_paginate").show();
        });

    });
</script>
<style type="text/css">

    .table1 td,th{
        border: 1px solid;
        font-size: 14px;
    }
</style>    

<p style="text-align: center;"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" id="loading" style="display: none;" /></p>
<div class="print_area">
    <?php
    $msg = '<b style="font-size: 18px;">' . MENU_REPORT_DERMATOLOGY . '</b><br /><br />';
    if ($_POST['due_date'] == '') {
        if ($_POST['date_from'] != '') {
            $msg .= REPORT_FROM . ': ' . $_POST['date_from'];
        }
        if ($_POST['date_to'] != '') {
            $msg .= ' ' . REPORT_TO . ': ' . $_POST['date_to'];
        }
    } else {
        $msg .= $_POST['due_date'];
    }
    echo $this->element('/prints/header', array('msg' => $msg));
    ?>
    <br />    
    <?php
    $condition = '';
    if (!empty($_POST['due_date'])) {
        if ($_POST['due_date'] == 'Today') {
            $condition = 'DATE(ItemRequest.created)=CURDATE()';
        } elseif ($_POST['due_date'] == 'This Week') {
            $condition = 'WEEK(ItemRequest.created)=WEEK(CURDATE()) && YEAR(ItemRequest.created)=YEAR(CURDATE())';
        } elseif ($_POST['due_date'] == 'This Month') {
            $condition = 'MONTH(ItemRequest.created)=MONTH(CURDATE()) && YEAR(ItemRequest.created)=YEAR(CURDATE())';
        } else {
            $condition = '';
        }
    }
    if (!empty($_POST['date_from'])) {
        $condition != '' ? $condition.=' AND ' : '';
        $condition.='"' . $_POST['date_from'] . '"<=DATE(ItemRequest.created)';
    }

    if (!empty($_POST['date_to'])) {
        $condition != '' ? $condition.=' AND ' : '';
        $condition.='"' . $_POST['date_to'] . '">=DATE(ItemRequest.created)';
    }



    $sql = "SELECT sex FROM diagnosis_item_requests AS ItemRequest
                    INNER JOIN queues AS Qpnt ON ItemRequest.queue_id = Qpnt.id
                    INNER JOIN patients AS Patient ON Patient.id = Qpnt.patient_id                     
                    WHERE ItemRequest.status > 0";   

    $query = mysql_query($sql . ($condition != '' ? ' AND ' . $condition : '') . "");
    $M = 0;
    $F = 0;
    $totalSex = 0;
    if (mysql_num_rows($query) > 0) {
        while ($data = mysql_fetch_array($query)) {
            if ($data['sex'] == "M") {
                $M = $M + 1;
            } else {
                $F = $F + 1;
            }
        }

        $totalSex = $M + $F;
    }
    
    
    $sql_next = "SELECT gdi.id, count(pnt.sex) As amount, pnt.sex, gdi.name AS item_name
                        FROM diagnosis_item_request_details AS dird INNER JOIN  group_dermato_items AS gdi ON gdi.id = dird.group_dermato_item_id
                        INNER JOIN diagnosis_item_requests AS ItemRequest ON ItemRequest.id=dird.diagnosis_item_request_id
                        INNER JOIN queues AS qpnt ON qpnt.id=ItemRequest.queue_id 
                        INNER JOIN patients As pnt ON pnt.id=qpnt.patient_id
                        WHERE dird.status = 1";
    $query_next = mysql_query($sql_next . ($condition != '' ? ' AND ' . $condition : '') . "                        
                 GROUP BY item_name,sex ORDER BY item_name ASC ");
    
    ?>

    <table class="table1" cellspacing="0" style="width: 100%; border: 1px solid">            
        <tr>                    
            <th style="width: 60%" rowspan="2"><?php echo 'Number By AgeGroup'; ?></th>
            <th style="text-align: center"><?php echo 'Male'; ?></th>
            <th style="text-align: center"><?php echo 'Female'; ?></th>
            <th style="text-align: center"><?php echo 'Total'; ?></th>
        </tr>                   
        <tr>                    
            <td style="text-align: center"><?php echo $M; ?></td>
            <td style="text-align: center"><?php echo $F; ?></td>
            <td style="text-align: center"><?php echo $totalSex; ?></td>
        </tr>            
    </table> 
    <br/>
    <div style="text-align: left">
        <span style="color: red;text-align: left;font-size: 18px;font-family:'Times New Roman;', Times, serif;">Total Patient Group By Item Dermatology :</span>
    </div>

    <table class="table1" cellspacing="0" style="width: 100%; border: 1px solid">            
        <tr>                    
            <th style="width: 60%"><?php echo 'Number By Item'; ?></th>
            <th style="text-align: center"><?php echo 'Male'; ?></th>
            <th style="text-align: center"><?php echo 'Female'; ?></th>
            <th style="text-align: center"><?php echo 'Total'; ?></th>
        </tr>                   
        <?php
        $countM = 0;
        $countF = 0;
        if (mysql_num_rows($query_next) > 0) {
            $all_array = array();
            $resultAll = array();
            while ($rowAll = mysql_fetch_array($query_next)) {
                if (!in_array($rowAll['id'], $all_array)) {
                    array_push($all_array, $rowAll['id']);
                    $resultAll[$rowAll['id']]['id'] = $rowAll['id'];
                    $resultAll[$rowAll['id']]['item_name'] = $rowAll['item_name'];
                    if ($rowAll['sex'] == "M") {
                        $resultAll[$rowAll['id']]['male'] = $rowAll['amount'];
                    } else {
                        $resultAll[$rowAll['id']]['female'] = $rowAll['amount'];
                    }
                } else {
                    $resultAll[$rowAll['id']]['id'] = $rowAll['id'];
                    $resultAll[$rowAll['id']]['item_name'] = $rowAll['item_name'];
                    if ($rowAll['sex'] == "M") {
                        $resultAll[$rowAll['id']]['male'] = $rowAll['amount'];
                    } else {
                        $resultAll[$rowAll['id']]['female'] = $rowAll['amount'];
                    }
                }
            }

            foreach ($resultAll as $key => $value) {
                echo '<tr>';
                echo '<td style="text-align:left;padding-left:40px;border-bottom: 1px solid;">' . $value['item_name'] . '</td>';
                echo '<td style="border-bottom: 1px solid;text-align:center">';
                if (!empty($value['male'])) {
                    echo $male = $value['male'];
                } else {
                    echo $male = 0;
                }
                echo '</td>';
                echo '<td style="border-bottom: 1px solid;text-align:center;">';
                if (!empty($value['female'])) {
                    echo $female = $value['female'];
                } else {
                    echo $female = 0;
                }
                echo '</td>';
                echo '<td style="border-right: 1px solid;border-bottom: 1px solid;text-align:center">';
                echo $totalAll = $male + $female;
                echo '</td>';
                echo '</tr>';
                $countM += $male;
                $countF += $female;
            }
        }
        ?>             
        <tr>
            <td style="font-size: 18px;padding-right: 15px;text-align: right;border-bottom: 1px solid;font-weight: bold;font-style: italic;">Total Item</td>                
            <td style="text-align:center;font-size: 18px;border-bottom: 1px solid;font-weight: bold;font-style: italic;"><?php  echo $countM;?></td>
            <td style="text-align:center;font-size: 18px;border-bottom: 1px solid;font-weight: bold;font-style: italic;"><?php  echo $countF;?></td>
            <td style="text-align:center;font-size: 18px;border-right: 1px solid;border-bottom: 1px solid;font-weight: bold;font-style: italic;"><?php echo $countM+$countF; ?></td>
        </tr>
    </table> 
</div>
<div style="clear: both;"></div>
<br />
<div class="buttons">
    <button type="button" id="btnPrint" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/>
        <?php echo ACTION_PRINT; ?>
    </button>
</div>
<div style="clear: both;"></div>