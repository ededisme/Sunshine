<?php
require("includes/function.php");
?>
<script type="text/javascript">  
    var _gaq = _gaq || [];  _gaq.push(['_setAccount', 'UA-22897853-1']);  _gaq.push(['_trackPageview']);  
    (function() { 
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';    
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);  });
</script> 
<div style="width: 100%;">
<span style="font-size: 20px;font-family: tahoma;color:#3139AF; font-weight:bold;">CENTRAL HOSPITAL</span></b></p>
<span style="font-size: 20px;font-family: tahoma;color:#3139AF; font-weight:bold;"><?php echo MENU_GRAPH?></span></b></p>

<?php
$condition = '';
if (!empty($_POST['due_date'])) {
    if ($_POST['due_date'] == 'Today') {
        $condition = 'DATE(Labo.created)=CURDATE()';
    } elseif ($_POST['due_date'] == 'This Week') {
        $condition = 'WEEK(Labo.created)=WEEK(CURDATE()) && YEAR(Labo.created)=YEAR(CURDATE())';
    } elseif ($_POST['due_date'] == 'This Month') {
        $condition = 'MONTH(Labo.created)=MONTH(CURDATE()) && YEAR(Labo.created)=YEAR(CURDATE())';
    } else {
        $condition = '';
    }
}
if ($_POST['date_from'] != '') {
    $condition != '' ? $condition.=' AND ' : '';
    $condition.='"' . dateConvert($_POST['date_from']) . '"<=DATE(Labo.created)';
}

if ($_POST['date_to'] != '') {
    $condition != '' ? $condition.=' AND ' : '';
    $condition.='"' . dateConvert($_POST['date_to']) . '">=DATE(Labo.created)';
}

$queryLaboTitleGroup = mysql_query('SELECT id,labo_item_group_id AS laboItemGroupId,name FROM labo_title_groups WHERE id =' . $_POST['by_labo']);
while ($Data = mysql_fetch_array($queryLaboTitleGroup)) {
    $laboItemGroupItemName = $Data['laboItemGroupId'];
    $laboItemGroupItemTitle = $Data['name'];
}
$queryLaboItemGroup = mysql_query('SELECT id, name AS ItemGroupName FROM labo_item_groups WHERE id IN(' . $laboItemGroupItemName . ')');
$i = 0;
while ($dataLaboItemGroup = mysql_fetch_array($queryLaboItemGroup)) {
    $j = 0;
    $data_x[$i] = $dataLaboItemGroup['ItemGroupName'];
    $Male = 0 ;
    $Female = 0;
    $sql = ("SELECT sex AS Sex, labo_item_group_id AS LaboItemGrId, queued_patient_id AS QuePatientId, patient_id AS PatId FROM  patients AS PatientId 
			INNER JOIN queued_patients AS QueuePatient ON PatientId.id = QueuePatient.patient_id
			INNER JOIN labos AS Labo ON QueuePatient.id = Labo.queued_patient_id 
			INNER JOIN labo_requests AS LaboRequest ON Labo.id = LaboRequest.labo_id 			
			WHERE Labo.status >0 AND LaboRequest.labo_item_group_id='".$dataLaboItemGroup['id']."'");    
    
    
    $queryNumberPatient = mysql_query($sql . ($condition != '' ? ' AND ' . $condition : '') . "");
    
    while ($dataNumberPatient = mysql_fetch_array($queryNumberPatient)) {
        if($dataNumberPatient['Sex']=="F"){
            $Female++;            
        }else{
            $Male++;
        }                        
        $j++;
    }
    
    $data_y[$i] = $j;    
    $data_male[$i] = $Male;
    $data_female[$i] = $Female;
            
    $i++;
}

?>
<span style="font-size: 16px;font-family: tahoma;color:#0000; font-weight:bold;"><?php echo "$laboItemGroupItemTitle";?></span></b>

<script type="text/javascript" src="<?php echo $this->webroot; ?>js/jquery-1.7.2.min.js"></script>
<script src="<?php echo $this->webroot; ?>js/highcharts.js"></script>
<script src="<?php echo $this->webroot; ?>js/exporting.js"></script>
<script type="text/javascript">
    
    
    $(document).ready(function(){
        myChart = new Highcharts.Chart(
        {
        
        
        // your code
        
            chart: {
                renderTo: 'con',
                type: 'column',
                margin: [ 60, 60, 100, 80]
            },
            title: {
                text: ''
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: [
                                <?php
                                foreach ($data_x as $datax) {
                                    echo "'" . $datax . "',";
                                }
                                ?>
                            ],
                            labels: {
                                rotation: -25,
                                align: 'right',
                                style: {
                                    fontSize: '12px',
                                    fontFamily: 'Verdana, sans-serif'
                                }
                            }
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Financail Report'
                            }
                        },
                        legend: {
                            enabled: true
                        },
                        
                        tooltip: {
                            headerFormat: '<span style="font-size:14px">{point.key}</span><table>',
                            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                                '<td style="padding:0"><b>{point.y:.2f} per</b></td></tr>',
                            footerFormat: '</table>',
                            shared: true,
                            useHTML: true
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.2,
                                borderWidth: 0
                            }
                        },
                        
                        series: [{
                                    name: 'Total Patient:',
                                    data: [<?php
                                            foreach ($data_y as $datay) {
                                                echo " $datay" . ",";
                                            }
                                                                                        
                                            ?>
                                    ]
                                }, {
                                    name: 'Total Male:',
                                    data: [<?php
                                            foreach ($data_male as $datamale) {
                                                echo " $datamale" . ",";
                                            }
                                                                                        
                                            ?>
                                    ]
                                 }, {
                                    name: 'Total Female:',
                                    data: [<?php
                                            foreach ($data_female as $datafemale) {
                                                echo " $datafemale" . ",";
                                            }
                                                                                        
                                            ?>
                                    ]
                        }]                                
         }); 
         
         
         
         
        
    });
    
    
</script>

<div id="con" style="min-width: 500px; height: 400px; margin: 0 auto"></div>
</div>

