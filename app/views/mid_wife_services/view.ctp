<?php
header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
$this->element('check_access');
$allowPrint = checkAccess($user['User']['id'], $this->params['controller'], 'printMidWifeService');
$rand = rand();
require_once("includes/function.php");
?>
<script type="text/javascript">
    $(document).ready(function() {
        $(".print<?php echo $rand; ?>, .btnPrint<?php echo $rand; ?>").click(function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printMidWifeService/" + $(this).attr("rel"),
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

        $(".btnBackMidWife").click(function(event) {
            event.preventDefault();
            var rightPanel = $(this).parent().parent().parent();
            var leftPanel = rightPanel.parent().find(".leftPanel");
            rightPanel.hide("slide", {direction: "right"}, 500, function() {
                leftPanel.show();
                rightPanel.html('');
            });
        });
    });
</script>
<style type="text/css">
    .table th{
        text-align: center;
    }
</style>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="" class="positive btnBackMidWife">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php
foreach ($dataService as $dataService):  ?>
<fieldset>
    <legend><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?></legend>
    <div>
        <table class="info" style="width: 100%;">
            <tr>
                <th><?php echo PATIENT_CODE; ?></th>
                <td><?php echo $dataService['Patient']['patient_code']; ?></td>
                <th><?php echo PATIENT_NAME; ?></th>
                <td><?php echo $dataService['Patient']['patient_name']; ?></td>  
                <th><?php echo TABLE_AGE.'/'.TABLE_DOB;?> </th>
                <td>
                    <?php 
                    $then_ts = strtotime($dataService['Patient']['dob']);
                    $then_year = date('Y', $then_ts);
                    $age = date('Y') - $then_year;
                    if (strtotime('+' . $age . ' years', $then_ts) > time())
                        $age--;

                    if ($age == 0) {
                        $then_year = date('m', $then_ts);
                        $month = date('m') - $then_year;
                        if (strtotime('+' . $month . ' month', $then_ts) > time())
                            $month--;
                        echo $month . ' ' . GENERAL_MONTH;
                    }else {
                        echo $age . ' ' . GENERAL_YEAR_OLD;
                    }
                    ?>
                </td>
                <th><?php echo TABLE_SEX; ?></th>
                <td>
                    <?php 
                        if($dataService['Patient']['sex']=="M"){
                            echo 'Male';
                        }else{
                            echo 'Female';
                        }                        
                    ?>
                </td>
            </tr>
            <tr>
                <th><?php echo TABLE_NATIONALITY;?> </th>
                <td>
                    <?php
                    if ($dataService['Patient']['patient_group_id'] != "") {
                        $query = mysql_query("SELECT name FROM patient_groups WHERE id=" . $dataService['Patient']['patient_group_id']);
                        while ($row = mysql_fetch_array($query)) {
                            if ($dataService['Patient']['patient_group_id'] == 1) {
                                echo $row['name'];
                            } else {
                                $queryNationality = mysql_query("SELECT name FROM nationalities WHERE id=".$dataService['Patient']['nationality']);
                                while ($result = mysql_fetch_array($queryNationality)) {
                                    echo $row['name'] . '&nbsp;&nbsp;(' . $result['name'] . ')';
                                }
                            }
                        }
                    } else {
                        echo $dataService['Nationality']['name'];
                    }
                    ?>
                </td>
                <th><?php echo TABLE_TELEPHONE;?> </th>
                <td><?php echo $dataService['Patient']['telephone']; ?></td>
            </tr>
            <tr>
                <th><?php echo TABLE_ADDRESS;?> </th>
                <td colspan="5">
                    <?php 
                    if($dataService['Patient']['address']!=""){
                        echo $dataService['Patient']['address'];
                    }
                    if($dataService['Patient']['location_id']!=""){
                        $query = mysql_query("SELECT name FROM patient_locations WHERE id=".$dataService['Patient']['location_id']);
                        while ($row = mysql_fetch_array($query)) {
                            if($dataService['Patient']['address']!=""){
                                echo ', ';
                            }
                            echo $row['name'];                
                        }
                    }
                    ?>
                </td>
            </tr>
            <tr>
                
            </tr>
        </table>
    </div>
</fieldset><br> 
<fieldset>
    <legend><?php __(MENU_ECHO_SERVICE_INFO); ?></legend>
    <div style="float: right; width:30px;">
        <?php
        if ($allowPrint) {
            echo "<a href='#' class='btnPrint$rand' rel='{$dataService['MidWifeService']['id']}' ><img alt='Print'  onmouseover='Tip(\"" . ACTION_PRINT . "\")'  src='{$this->webroot}img/button/printer.png' /></a>";
        }
        ?>
    </div>
    <div>
        <fieldset>
    <legend><?php __(GENERAL_REQUEST); ?></legend>
    <table style="width: 100%;" cellspacing="0">
        <tr>
            <td>
                <?php 
                    $queryDataFromDoctor=  mysql_query("SELECT mwsreq.*,mwsreq.id as id FROM mid_wife_service_requests as mwsreq "
                            . "INNER JOIN other_service_requests as osreq ON osreq.id=mwsreq.other_service_request_id "
                            . "INNER JOIN queued_doctors as qd ON qd.id=osreq.queued_doctor_id "
                            . "INNER JOIN queues as q ON q.id=qd.queue_id WHERE osreq.is_active=1 AND queue_id=".$dataService['Queue']['id']);
                    $dataRequest=  mysql_fetch_array($queryDataFromDoctor);
                    echo $dataRequest['mid_wife_description'];
                ?>
                <input type="hidden" value="<?php echo $dataRequest['id']; ?>" name="data[MidWifeServiceRequest][id]">
            </td>
        </tr>
    </table>      
</fieldset>
<br/>
<fieldset>
    <legend><?php __(MENU_MID_WIFE_SERVICE_INFO); ?></legend>
    <fieldset>
        <legend><?php __(MENU_STORY_PATIENTS); ?></legend>
        <table style="width: 100%;">
            <tr>
                <td style="width:10%;"><?php echo TABLE_LAST_MENSTRUATION_PERIOD; ?></td>
                <td style="width:3%;">:</td>
                <td style="width:20%;"><?php echo dateShort($dataService['MidWifeService']['last_mentstruation_period']); ?></td>
                <td></td>
            </tr>
            <tr>
                <td><?php echo TABLE_ESTIMATE_DELIVERY_DATE; ?></td>
                <td style="width:3%;">:</td>
                <td><?php echo dateShort($dataService['MidWifeService']['estimate_delivery_date']); ?></td>
                <td></td>
            </tr>
            <tr>
                <td><?php echo TABLE_ECHO; ?></td>
                <td>:</td>
                <td><?php echo dateShort($dataService['MidWifeService']['echo']); ?></td>
                <td></td>
            </tr>
            <tr>
                <td style="width:10%;"><?php echo TABLE_WEIGHT; ?></td>
                <td>:</td>
                <td style="width:20%;"><?php echo $dataService['MidWifeService']['weight']; ?></td>
                <td style="width:10%;"><?php echo TABLE_HEIGHT; ?></td>
                <td style="width:3%;">:</td>
                <td style="width:20%;"><?php echo $dataService['MidWifeService']['height']; ?></td>
                <td></td>
            </tr>
            <tr>
                <td><?php echo TABLE_GESTRATION; ?></td>
                <td>:</td>
                <td><?php echo $dataService['MidWifeService']['gestation']; ?></td>
                <td><?php echo TABLE_BABY; ?></td>
                <td>:</td>
                <td><?php echo $dataService['MidWifeService']['baby']; ?></td>
                <td></td>
            </tr>
        </table>
    </fieldset>
    <fieldset>
        <legend><?php __(MENU_PATIENT_STORY_SEE); ?></legend>
        <table style="width: 100%;">
            <tr>
                <td style="width:8%;"><?php __(MENU_PATIENT_STORY_SEE); ?></td>
            </tr>
            <tr>
                <td style="width:8%;"></td>
                <td style="width:8%;"><?php echo TABLE_ABORTION; ?></td>
                <td style="width:2%;">:</td>
                <td style="width:10%;"><?php echo $dataService['MidWifeService']['abortion']; ?></td>
                <td style="width:10%;"><?php echo TABLE_INTERUPTION_VOLONTAIN; ?></td>
                <td style="width:2%;">:</td>
                <td style="width:10%;"><?php echo $dataService['MidWifeService']['interuption_volontain']; ?></td>
                <td style="width:52%;" colspan="4"></td>
            </tr>
            <tr>
                <td><?php __(MENU_ACCON_CHEMENT); ?></td>    
            </tr>
            <tr>
                <td style="width:8%;"></td>
                <td style="width:10%;"><?php echo TABLE_BIRTH; ?></td>
                <td style="width:2%;">:</td>
                <td style="width:15%;"><?php echo $dataService['MidWifeService']['birth']; ?></td>
                <td style="width:15%;"><?php echo MENU_NEE_MOIT; ?></td>
                <td style="width:2%;">:</td>
                <td style="width:15%;"><?php echo $dataService['MidWifeService']['nee_moit']; ?></td>
                <td style="width:15%;"><?php echo MENU_MOIT_NEE; ?></td>
                <td style="width:2%;">:</td>
                <td style="width:10%;"><?php echo $dataService['MidWifeService']['mort_nee']; ?></td>
                <td style=""></td>
            </tr>
            <tr>
                <td><?php __(MENU_ACCONCHEMENT_RERME); ?></td>    									
            </tr> 
            <tr>
                <td></td>
                <td><?php echo TABLE_ACCONCHEM_NORMAL; ?></td>
                <td style="width:2%;">:</td>
                <td style="width:10%;"><?php echo $dataService['MidWifeService']['acconchement_normal']; ?></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td><?php echo MENU_ANORMAT; ?></td>
            </tr>
            <tr>
                <td></td>
                <td><?php echo TABLE_CAESAREAN; ?></td>
                <td style="width:2%;">:</td>
                <td style="width:10%;"><?php echo $dataService['MidWifeService']['caesarean']; ?></td>
                <td><?php echo TABLE_ACC_PAR_VENTONSE; ?></td>
                <td style="width:2%;">:</td>
                <td style="width:10%;"><?php echo $dataService['MidWifeService']['acc_par_ventonse']; ?></td>
                <td></td>
            </tr>
        </table>
    </fieldset>
    <fieldset>
        <legend><?php __(PATIENT_STORY_BEFORE); ?></legend>
        <table style="width: 100%;">
            <tr>
                <td style="width:15%;"><?php __(PATIENT_MADIE_DES_REINS); ?></td>
                <td style="width:2%;">:</td>
                <td style="width:10%;">
                    <?php 
                    if($dataService['MidWifeService']['edema']==1){
                        echo $this->Form->input('edema',array('type'=>'checkbox','name'=>'data[MidWifeService][edema]','value'=>'1','label'=>FALSE,'checked'=>TRUE,'disabled' => "disabled"));
                    }else{
                        echo $this->Form->input('edema',array('type'=>'checkbox','name'=>'data[MidWifeService][edema]','value'=>'1','label'=>FALSE,'disabled' => "disabled"));
                    }
                    ?>
                    <?php echo TABLE_EDEMA; ?>
                </td>
                
                <td style="width:10%;">
                    <?php 
                    if($dataService['MidWifeService']['albuminuria']==1){
                        echo $this->Form->input('albuminuria',array('type'=>'checkbox','name'=>'data[MidWifeService][albuminuria]','value'=>'1','label'=>FALSE,'checked'=>TRUE,'disabled' => "disabled"));
                    }else{
                        echo $this->Form->input('albuminuria',array('type'=>'checkbox','name'=>'data[MidWifeService][albuminuria]','value'=>'1','label'=>FALSE,'disabled' => "disabled"));
                    }
                    ?>
                    <?php echo TABLE_ALBUMINURIA; ?>
                </td>
                <td style="width:51%;"></td>
            </tr>
            <tr>
                <td>
                    <?php 
                    if($dataService['MidWifeService']['cadiojathie']==1){
                        echo $this->Form->input('cadiojathie',array('type'=>'checkbox','name'=>'data[MidWifeService][cadiojathie]','value'=>'1','label'=>FALSE,'checked'=>TRUE,'disabled' => "disabled"));
                    }else{
                        echo $this->Form->input('cadiojathie',array('type'=>'checkbox','name'=>'data[MidWifeService][cadiojathie]','value'=>'1','label'=>FALSE,'disabled' => "disabled"));
                    }
                    ?>
                    <?php echo TABLE_CADIOJATHIE; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php 
                    if($dataService['MidWifeService']['asthma']==1){
                        echo $this->Form->input('asthma',array('type'=>'checkbox','name'=>'data[MidWifeService][asthma]','value'=>'1','label'=>FALSE,'checked'=>TRUE,'disabled' => "disabled"));
                    }else{
                        echo $this->Form->input('asthma',array('type'=>'checkbox','name'=>'data[MidWifeService][asthma]','value'=>'1','label'=>FALSE,'disabled' => "disabled"));
                    }
                    ?>
                    <?php echo TABLE_ASTHMA; ?>
                </td>
            </tr>
            <tr>
                <td><?php echo TABLE_OTHER; ?></td>
                <td>:</td>
                <td colspan="5"><?php echo $dataService['MidWifeService']['other']; ?></td>
            </tr>
        </table>
    </fieldset>
</fieldset>
    </div>
</fieldset>    
<?php endforeach; ?>