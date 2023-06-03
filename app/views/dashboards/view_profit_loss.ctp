<?php
include("includes/function.php");
$symbol = '$';
$datas = array();
$xAxis = '';
for($i = 1; $i <= 12; $i++){
    if($i > 1 && $i <= 12){
        $xAxis .= ",";
    }
    $month    = str_pad($i, 2, '0', STR_PAD_LEFT);
    $dateList = date("Y")."-".$month."-01";
    $name   = date("M", strtotime($dateList));
    $xAxis .= "'{$name}'";
    $datas[$month] = 0;
}

$tableName = "dashboard_profit_loss";
mysql_query("SET max_heap_table_size = 1024*1024*1024");
mysql_query("CREATE TABLE `{$tableName}` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`date` DATE NULL DEFAULT NULL,
	`company_id` INT(11) NULL DEFAULT NULL,
	`branch_id` INT(11) NULL DEFAULT NULL,
	`chart_account_id` INT(11) NULL DEFAULT NULL,
	`debit` DECIMAL(20,9) NULL DEFAULT NULL,
	`credit` DECIMAL(20,9) NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	INDEX `date` (`date`),
	INDEX `company` (`company_id`, `branch_id`),
	INDEX `chart_account_id` (`chart_account_id`))
        ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;;");
mysql_query("TRUNCATE $tableName");

$queryCoa = mysql_query("SELECT SUM(IFNULL(debit, 0)) AS debit,SUM(IFNULL(credit, 0)) AS credit, date, chart_account_id, company_id, branch_id
                         FROM general_ledgers gl INNER JOIN general_ledger_details gld ON gl.id=gld.general_ledger_id
                         WHERE gl.is_approve=1 AND gl.is_active=1 AND gld.company_id IN (SELECT company_id FROM user_companies WHERE user_id = {$user['User']['id']}) AND gld.branch_id IN (SELECT branch_id FROM user_branches WHERE user_id = {$user['User']['id']}) AND YEAR(date) = '" . date("Y") . "'
                         GROUP BY date,chart_account_id,company_id,branch_id");
while ($dataCoa = mysql_fetch_array($queryCoa)) {
    mysql_query("INSERT INTO $tableName (
                            date,
                            chart_account_id,
                            company_id,
                            branch_id,
                            debit,
                            credit
                        ) VALUES (
                            '" . $dataCoa['date'] . "',
                            " . (!is_null($dataCoa['chart_account_id']) ? $dataCoa['chart_account_id'] : "NULL") . ",
                            " . (!is_null($dataCoa['company_id']) ? $dataCoa['company_id'] : "NULL") . ",
                            " . (!is_null($dataCoa['branch_id']) ? $dataCoa['branch_id'] : "NULL") . ",
                            " . $dataCoa['debit'] . ",
                            " . $dataCoa['credit'] . ")");
}

$isEmpty = 0;
$totalProfitLoss = 0;
$sqlRevenue = mysql_query("SELECT SUM(IFNULL(g.credit, 0) - IFNULL(g.debit, 0)) AS income, MONTH(g.date) AS month FROM {$tableName} AS g INNER JOIN chart_accounts ON chart_accounts.id = g.chart_account_id AND chart_accounts.chart_account_type_id = 11 WHERE 1 GROUP BY MONTH(date)");
while($rowRevenue = mysql_fetch_array($sqlRevenue)){
    $month = str_pad($rowRevenue['month'], 2, '0', STR_PAD_LEFT);
    $datas[$month] += $rowRevenue['income'];
    $totalProfitLoss += $rowRevenue['income'];
}
$sqlCogs = mysql_query("SELECT SUM(IFNULL(g.debit, 0) - IFNULL(g.credit, 0)) AS cogs, MONTH(g.date) AS month FROM {$tableName} AS g INNER JOIN chart_accounts ON chart_accounts.id = g.chart_account_id AND chart_accounts.chart_account_type_id = 12 WHERE 1 GROUP BY MONTH(date)");
while($rowCogs = mysql_fetch_array($sqlCogs)){
    $month = str_pad($rowCogs['month'], 2, '0', STR_PAD_LEFT);
    $datas[$month] -= $rowCogs['cogs'];
    $totalProfitLoss -= $rowCogs['cogs'];
}
$sqlExpense = mysql_query("SELECT SUM(IFNULL(g.debit, 0) - IFNULL(g.credit, 0)) AS expense, MONTH(g.date) AS month FROM {$tableName} AS g INNER JOIN chart_accounts ON chart_accounts.id = g.chart_account_id AND chart_accounts.chart_account_type_id = 13 WHERE 1 GROUP BY MONTH(date)");
while($rowExpense = mysql_fetch_array($sqlExpense)){
    $month = str_pad($rowExpense['month'], 2, '0', STR_PAD_LEFT);
    $datas[$month] -= $rowExpense['expense'];
    $totalProfitLoss -= $rowExpense['expense'];
}
?>
<script type="text/javascript">
    $(document).ready(function(){
        <?php
        if(!empty($datas)){
        ?>
        Highcharts.chart('dvViewProfitLoss', {
            chart: {
                type: 'column'
            },
            title: {
                text: '<b style="font-size: 15px;"><?php echo MENU_PROFIT_AND_LOSS." ".date("Y"); ?></b>'
            },
            subtitle: {
                text: false
            },
            xAxis: {
                categories: [<?php echo $xAxis; ?>]
            },
            yAxis: {
                title: {
                    text: '<?php echo GENERAL_AMOUNT; ?>'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>'+'<td style="padding:0"><b>{point.y:.2f} <?php echo $symbol; ?></b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            legend: {
                enabled: false
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{
                name: 'Profit & Loss',
                data: [<?php
                    $j = 1;
                    ksort($datas);
                    $lengthVal = count($datas);
                    foreach($datas AS $val){
                        if($j > 1 && $j <= $lengthVal){
                            echo ",";
                        }
                        echo $val;
                        $j++;
                    }
                    ?>]
            }]
        });
        <?php
        }
        ?>
        $("#filterProfitLoss").unbind("change").change(function(){
            $.ajax({
                type: "GET",
                url: "<?php echo $this->base.'/'.$this->params['controller']; ?>/viewProfitLoss/",
                beforeSend: function(){
                    $("#refreshProfitLoss").hide();
                    $("#loadingProfitLoss").show();
                    $("#profitLossView").html("Loading....");
                },
                success: function(result){
                    $("#refreshProfitLoss").show();
                    $("#loadingProfitLoss").hide();
                    $("#profitLossView").html(result);
                }
            });
        });
        
        $("#refreshProfitLoss").unbind("click").click(function(){
            $.ajax({
                type: "GET",
                url: "<?php echo $this->base.'/'.$this->params['controller']; ?>/viewProfitLoss/",
                beforeSend: function(){
                    $("#refreshProfitLoss").hide();
                    $("#loadingProfitLoss").show();
                    $("#profitLossView").html("Loading....");
                },
                success: function(result){
                    $("#refreshProfitLoss").show();
                    $("#loadingProfitLoss").hide();
                    $("#profitLossView").html(result);
                }
            });
        });
    });
</script>
<div id="dvViewProfitLoss" style="width: 100%; height: 300px; margin: 0 auto"></div>