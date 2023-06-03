<?php

include('includes/function.php');

$query_credit_memo=mysql_query("SELECT * FROM credit_memos WHERE id=".$this->params['pass'][0]);
$data_credit_memo=mysql_fetch_array($query_credit_memo);

$cityProvince = array(
    0 => 'Banteay Meanchey',
    1 => 'Battambang',
    2 => 'Kampong Cham',
    3 => 'Kampong Chhnang',
    4 => 'Kampong Speu',
    5 => 'Kampong Thom',
    6 => 'Kampot',
    7 => 'Kandal',
    8 => 'Kep',
    9 => 'Koh Kong',
    10 => 'Kratie',
    11 => 'Mondul Kiri',
    12 => 'Otdar Meanchey',
    13 => 'Pailin',
    14 => 'Phnom Penh',
    15 => 'Preah Vihear',
    16 => 'Prey Veng',
    17 => 'Pursat',
    18 => 'Ratanak Kiri',
    19 => 'Siemreap',
    20 => 'Sihanoukville',
    21 => 'Stueng Treng',
    22 => 'Svay Rieng',
    23 => 'Takeo'
);

?>
<style type="text/css">
    .info th {
        padding: 2px;
        font-size: 11px;
    }
    .info td {
        padding: 2px;
        font-size: 11px;
    }
    .table_solid th {
        font-size: 11px;
    }
    .table td {
        font-size: 11px;
    }
</style>
<table style="width: 100%;">
    <tr>
        <td colspan="3"></td>
    </tr>
    <tr>
        <td style="font-size: 10px;vertical-align: bottom;text-align: left;width: 30%;white-space: nowrap;">
            <img alt="" src="<?php echo $this->webroot; ?>img/logo.gif" style="width: 150px;" />
            <?php echo GENERAL_COMPANY_ADDRESS; ?>
            <br />
            <?php echo GENERAL_COMPANY_TEL; ?>
        </td>
        <td style="font-size: 13px;vertical-align: middle;text-align: center;">
            <?php echo GENERAL_COMPANY_NAME_KH; ?>
            <br />
            <?php echo GENERAL_COMPANY_NAME_EN; ?>
            <br />
            <br />
            <b style="font-size: 18px;"><?php echo mb_strtoupper(GENERAL_CREDIT_MEMO,  'UTF-8'); ?></b>
        </td>
        <td style="vertical-align: top;text-align: right;">
            <div style="font-size: 13px;text-align: center;">
                <?php echo GENERAL_KINGDOM_OF_CAMBODIA; ?>
                <br />
                <?php echo GENERAL_NATION_RELIGION_KING; ?>
                <br />
                <br />
                <br />
                <?php list($year,$month,$day) = split('-',  substr($data_credit_memo['created'],0,10)); ?>
                ថ្ងៃទី <?php echo $day; ?> ខែ <?php echo $month; ?> ឆ្នាំ <?php echo $year; ?>
                <br />
                <br />
                <?php echo REPORT_CREDIT_MEMO_CODE; ?>: <?php echo $data_credit_memo['credit_memo_code']; ?>
            </div>
        </td>
    </tr>
</table>
<table class="info">
    <tr>
        <th><?php echo PATIENT_CODE; ?></th>
        <td colspan="5"><?php echo $patient['Patient']['patient_code']; ?></td>
    </tr>
    <tr>
        <th><?php echo PATIENT_NAME; ?></th>
        <td><?php echo $patient['Patient']['patient_name']; ?></td>
        <th><?php echo TABLE_SEX; ?></th>
        <td><?php echo $patient['Patient']['sex']; ?></td>
        <th><?php echo TABLE_AGE; ?></th>
        <td>
            <?php
            list($curYear,$curMonth,$curDay) = split('-',date('Y-m-d'));
            list($year,$month,$day) = split('-',$patient['Patient']['dob']);
            echo $curYear-$year . ' ' . GENERAL_YEAR_OLD;
            ?>
        </td>
    </tr>
    <tr>
        <th><?php echo TABLE_ADDRESS; ?></th>
        <td colspan="5"><?php echo $patient['Patient']['address']; ?><?php echo $patient['Patient']['address']!=''?', ':''; ?><?php echo $patient['Patient']['city_province']!=''?$cityProvince[$patient['Patient']['city_province']]:''; ?></td>
    </tr>
</table>
<?php $index=1; ?>
<table class="table" cellspacing="0">
    <tr>
        <th class="first"><?php echo TABLE_NO; ?></th>
        <th><?php echo SECTION_SECTION; ?></th>
        <th><?php echo SERVICE_SERVICE; ?></th>
        <th><?php echo GENERAL_QTY; ?></th>
        <th><?php echo GENERAL_UNIT_PRICE; ?> ($)</th>
        <th><?php echo GENERAL_TOTAL_PRICE; ?> ($)</th>
    </tr>
    <?php
    $total=0;
    $query=mysql_query("SELECT * FROM credit_memo_details WHERE credit_memo_id=".$data_credit_memo['id']);
    while($data=mysql_fetch_array($query)){?>
    <tr>
        <?php
        $total+=$data['total_price'];
        $query_service=mysql_query("SELECT sec.name, ser.name FROM sections sec INNER JOIN services ser ON ser.section_id=sec.id WHERE ser.id=".$data['service_id']);
        $data_service=mysql_fetch_array($query_service);?>
        <td class="first"><?php echo $index++; ?></td>
        <td><?php echo $data_service[0]; ?></td>
        <td><?php echo $data_service[1]; ?></td>
        <td><?php echo $data['qty']; ?></td>
        <td><?php echo number_format($data['unit_price'],2); ?></td>
        <td><?php echo number_format($data['total_price'],2); ?></td>
    </tr>
    <?php } ?>
    <tr>
        <td colspan="5" style="text-align: right;border-bottom: 0px;"><b><?php echo TABLE_TOTAL_AMOUNT; ?>:</b></td>
        <td><?php echo number_format($total,2); ?></td>
    </tr>
    <tr>
        <td colspan="4" style="border-bottom: 0px;border-right: 0px;">
            <?php echo GENERAL_PAID_BY; ?>: <b><?php echo getCreditMemoCreator($data_credit_memo['id']); ?></b>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php echo GENERAL_RECEIVED_BY; ?>: <b><?php echo $patient['Patient']['first_name'].' '.$patient['Patient']['last_name']; ?></b>
        </td>
    </tr>
</table>