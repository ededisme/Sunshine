<?php
header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
$this->element('check_access');
$allowPrint = checkAccess($user['User']['id'], $this->params['controller'], 'printEchoService');
$rand = rand();
require_once("includes/function.php");
?>
<script type="text/javascript">
    $(document).ready(function() {
        $(".print<?php echo $rand; ?>, .btnPrint<?php echo $rand; ?>").click(function(event) {
            event.preventDefault();
            var id= $('#echoServiceCardiaId').val();
            $.ajax({
                type: "POST",
                url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printEchoServiceCardia/" + id,
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

        $(".btnBackEchoCardia").click(function(event) {
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
<div style="padding: 5px;border: 1px dashed #3C69AD;">
    <div class="buttons">
        <a href="" class="positive btnBackEchoCardia">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="float:right;" class="buttons">
        <a href="" class="positive btnPrint<?php echo $rand; ?>">
            <img src="<?php echo $this->webroot; ?>img/action/printer1.png" alt=""/>
            <?php echo ACTION_PRINT; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php
foreach ($dataService as $dataService):  ?>
<?php echo $this->Form->hidden('echo_service_cardia_id',array('value'=>$dataService['EchoServiceCardia']['id'],'id'=>'echoServiceCardiaId')); ?>
    <table cellspacing='0' cellpadding='0' width="100%">
        <tr>
            <td class="first patient-info" style="width: 50%; padding-top: 10px;"><?php echo TABLE_EFFECTUE;?> : <?php echo $dataService['EchoServiceCardia']['effecture']; ?></td>
            <td class="patient-info" style="width: 50%; padding-top: 10px;" colspan="2"><?php echo TABLE_PAR_DOCTOR;?> : 
                <?php 
                $doctor = "";
                $query = mysql_query("SELECT emp.name FROM users As u INNER JOIN user_employees As useremployee ON useremployee.user_id = u.id INNER JOIN employees As emp ON emp.id = useremployee.employee_id WHERE u.id=".$dataService['EchoServiceCardia']['created_by']);
                while ($result = mysql_fetch_array($query)) {
                    echo $doctor = $result['name'];
                }
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="padding-top: 10px;"><?php echo TABLE_MOTIF;?> : <?php echo $dataService['EchoServiceCardia']['effecture']; ?></td>
        </tr>
        <tr>
            <td colspan="3" style="padding-top: 10px;"><?php echo TABLE_NOM_ET_PRENOM;?> : <?php echo $dataService['Patient']['patient_name']; ?></td>
        </tr>
        <tr>
            <td style="padding-top: 10px;"><?php echo TABLE_DATE_DE_NAISSANCE;?> : <?php echo dateShort($dataService['Patient']['dob'],'d/m/Y'); ?></td>
            <td style="padding-top: 10px;"><?php echo TABLE_AGE;?> : 
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
            <td style="padding-top: 10px;"><?php echo TABLE_SEXE;?> : 
                <?php
                if ($dataService['Patient']['sex'] == "F") {
                    echo GENERAL_FEMALE;
                } else {
                    echo GENERAL_MALE;
                }
                ?>
            </td>
        </tr>     
    </table>  
    <div class="clear"></div>
    <table cellspacing='0' cellpadding='0' width="100%">
        <tr>
            <td valign="top" style="padding-top:10px;padding-bottom: 10px;">
                <table style="width: 100%;" cellspacing="0">
                    <tr>
                        <td style="border:1px solid #aaaaaa;border-right: none;padding: 5px;">VD DTD</td>
                        <td style="border:1px solid #aaaaaa;border-left:none;padding: 5px;">(mm)</td>
                        <td colspan="2" style="border:1px solid #aaaaaa;border-left:none;padding: 5px;text-align: center;"><?php echo $dataService['EchoServiceCardia']['vd_dtd']; ?></td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #aaaaaa;border-top: none;border-right: none;padding: 5px;">Ao ascend</td>
                        <td style="border-left:none;border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;">(mm)</td>
                        <td colspan="2" style="border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;text-align: center;"><?php echo $dataService['EchoServiceCardia']['ao_ascend']; ?></td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #aaaaaa;border-top: none;border-right: none;padding: 5px;">OG</td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;width:25%;padding: 5px;">(mm et cm<sup style="font-size:9px">2</sup>)</td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;width:25%;padding: 5px;text-align: center;"><?php echo $dataService['EchoServiceCardia']['og_1']; ?></td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;width:25%;padding: 5px;text-align: center;"><?php echo $dataService['EchoServiceCardia']['og_2']; ?></td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #aaaaaa;border-top: none;border-right: none;padding: 5px;">SIV<sub style="font-size:9px">D-S</sub></td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;">(mm)</td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;text-align: center;"><?php echo $dataService['EchoServiceCardia']['siv_1']; ?></td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;text-align: center;"><?php echo $dataService['EchoServiceCardia']['siv_2']; ?></td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #aaaaaa;border-top: none;border-right: none;padding: 5px;">VGDTD-DTS</td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;">(mm)</td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;text-align: center;"><?php echo $dataService['EchoServiceCardia']['vgdtd_dts_1']; ?></td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;text-align: center;"><?php echo $dataService['EchoServiceCardia']['vgdtd_dts_2']; ?></td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #aaaaaa;border-top: none;border-right: none;padding: 5px;">PP VG<sub style="font-size:9px">D-S</sub></td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;">(mm)</td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;text-align: center;"><?php echo $dataService['EchoServiceCardia']['pp_vg_1']; ?></td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;text-align: center;"><?php echo $dataService['EchoServiceCardia']['pp_vg_2']; ?></td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #aaaaaa;border-top: none;border-right: none;padding: 5px;">FRVG-FEVG</td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;">%</td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;text-align: center;"><?php echo $dataService['EchoServiceCardia']['frvg_fevg_1']; ?></td>
                        <td style="border:1px solid #aaaaaa;border-left:none;border-top: none;padding: 5px;text-align: center;"><?php echo $dataService['EchoServiceCardia']['frvg_fevg_2']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="text-align: center;padding: 10px;text-decoration: underline;font-weight:bold;font-size: 16px;">
                ECHO CARDIOGRAPHY
            </td>
        </tr>
        <tr>
            <td valign="top">
                <?php echo $dataService['EchoServiceCardia']['description']; ?>
            </td>
        </tr>
        <tr>
            <td style="font-weight:bold;font-size: 15px;padding-bottom: 10px;padding-top: 10px;"><?php echo 'CONCLUSION';?></td>
        </tr>
        <tr>
            <td style="padding-left:5%;padding-bottom: 5px;"><?php echo $dataService['EchoServiceCardia']['conclusion']; ?></td>
        </tr>
        <tr>
            <td>
                <?php
                $queryImage=  mysql_query("SELECT * FROM echo_service_cardia_images as esim WHERE is_active=1 AND echo_srv_cardia_id=".$dataService['EchoServiceCardia']['id']);
                if(@mysql_num_rows($queryImage)){
                    while ($dataImage=  mysql_fetch_array($queryImage)){ ?>
                    <img src="<?php echo $this->webroot; ?>/img/echo_cardia/<?php echo $dataImage['src_name']; ?>" alt="<?php echo $dataImage['src_name']; ?>" width="150px" height="150px" vspace='2px' style="margin-left:5px;">
                    <?php 
                    }
                } ?>
            </td>
        </tr>
    </table>    
<?php endforeach; ?>