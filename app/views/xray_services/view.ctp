<?php
header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
$this->element('check_access');
$allowPrint = checkAccess($user['User']['id'], $this->params['controller'], 'printXrayService');
$rand = rand();
$tblNameRadom = "tbl" . rand();
require_once("includes/function.php");
?>
<script type="text/javascript">
    $(document).ready(function() {
        $(".print<?php echo $rand; ?>, .btnPrint<?php echo $rand; ?>").click(function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printXrayService/" + $(this).attr("rel"),
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

        $(".btnBackXray").click(function(event) {
            event.preventDefault();
            var rightPanel = $(this).parent().parent().parent();
            var leftPanel = rightPanel.parent().find(".leftPanel");
            rightPanel.hide("slide", {direction: "right"}, 500, function() {
                leftPanel.show();
                rightPanel.html('');
            });
        });
        
        //Hide Patinen Info
        $("#btnHidePatientInfo<?php echo $tblNameRadom;?>").click(function(){
            $("#patientInfo<?php echo $tblNameRadom;?>").hide(900);
            $("#showPatientInfo<?php echo $tblNameRadom;?>").show();
        });
        //Show Patinen Info
        $("#btnShowPatientInfo<?php echo $tblNameRadom;?>").click(function(){
            $("#patientInfo<?php echo $tblNameRadom;?>").show(900);
            $("#showPatientInfo<?php echo $tblNameRadom;?>").hide();
        });
        
    });
</script>
<style type="text/css">
    .table th{
        text-align: center;
    }
</style>
<div style="padding: 5px;border: 1px dashed #3C69AD;">
    <div class="buttons">
        <a href="" class="positive btnBackXray">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php
foreach ($dataService as $dataService):  ?>
<legend id="showPatientInfo<?php echo $tblNameRadom;?>" style="display:none;"><a href="#" id="btnShowPatientInfo<?php echo $tblNameRadom;?>" style="background: #CCCCCC; font-weight: bold;"><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?> [ Show ] </a> </legend>
<fieldset id="patientInfo<?php echo $tblNameRadom;?>" style="border: 1px dashed #3C69AD;">
    <legend><a href="#" id="btnHidePatientInfo<?php echo $tblNameRadom;?>" style="background: #CCCCCC; font-weight: bold;"> <?php echo MENU_PATIENT_MANAGEMENT_INFO; ?> [ Hide ] </a></legend>
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
<fieldset style="border: 1px dashed #3C69AD;">
    <legend><?php __(MENU_XRAY_SERVICE_INFO); ?></legend>
    <div style="float: right; width:30px;">
        <?php
        if ($allowPrint) {
            echo "<a href='#' class='btnPrint$rand' rel='{$dataService['XrayService']['id']}' ><img alt='Print'  onmouseover='Tip(\"" . ACTION_PRINT . "\")'  src='{$this->webroot}img/button/printer.png' /></a>";
        }
        ?>
    </div>
    <div>
        <table class="table" >
            <tr>
                <td class="first" style="width:20%;border-top:1px solid #C1DAD7;">
                    <span class="title-report"><?php echo TABLE_DATE;?> </span>
                </td>
                <td valign="top" style="border-top:1px solid #C1DAD7;"><?php echo dateShort($dataService['XrayService']['xray_date']); ?></td>
            </tr>
            <tr>
                <td class="first">
                    <span class="title-report"><?php echo TABLE_DESCRIPTION;?> </span>
                </td>
                <td valign="top"><?php echo $dataService['XrayService']['description']; ?></td>
            </tr>
            <tr>
                <td class="first">
                    <span class="title-report"><?php echo TABLE_CONCLUSION;?> </span>
                </td>
                <td><?php echo $dataService['XrayService']['conclusion']; ?></td>
            </tr>
            <tr>
                <td class="first">
                    <span class="title-report"><?php echo TABLE_IMAGE;?> </span>
                </td>
                <td>
                    <?php
                    $queryImage=  mysql_query("SELECT * FROM xray_service_images as xsim WHERE is_active=1 AND xray_srv_id=".$dataService['XrayService']['id']);
                    if(@mysql_num_rows($queryImage)){
                        while ($dataImage=  mysql_fetch_array($queryImage)){ ?>
                        <img src="<?php echo $this->webroot; ?>/img/x-ray/<?php echo $dataImage['src_name']; ?>" alt="<?php echo $dataImage['src_name']; ?>" width="150px" height="150px" vspace='2px' style="margin-left:5px;">
                    <?php } 
                    }?>
                </td>
            </tr>
        </table>
    </div>
</fieldset>    
<?php endforeach; ?>