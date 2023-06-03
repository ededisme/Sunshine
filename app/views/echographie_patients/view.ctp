<?php
header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
$this->element('check_access');
$allowPrint = checkAccess($user['User']['id'], $this->params['controller'], 'printObstetniquePatient');
$rand = rand();
require_once("includes/function.php");
?>
<script type="text/javascript">
    $(document).ready(function() {
        $(".print<?php echo $rand; ?>, .btnPrint<?php echo $rand; ?>").click(function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/printObstetniquePatient/" + $(this).attr("rel"),
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

        $(".btnBackEchographiePatient").click(function(event) {
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
    .text{
        width: 100%;
    }
</style>
<div style="padding: 5px;border: 1px dashed #3C69AD;">
    <div class="buttons">
        <a href="" class="positive btnBackEchographiePatient">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php foreach ($dataService as $dataService): ?>
    <fieldset style="border: 1px dashed #3C69AD;">
        <legend style="background: #CCCCCC; font-weight: bold;"><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?></legend>
        <div>
            <table class="info" style="width: 100%;">
                <tr>
                    <th><?php echo PATIENT_CODE; ?></th>
                    <td><?php echo $dataService['Patient']['patient_code']; ?></td>
                    <th><?php echo PATIENT_NAME; ?></th>
                    <td><?php echo $dataService['Patient']['patient_name']; ?></td>  
                    <th><?php echo TABLE_AGE . '/' . TABLE_DOB; ?> </th>
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
                        if ($dataService['Patient']['sex'] == "M") {
                            echo 'Male';
                        } else {
                            echo 'Female';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo TABLE_NATIONALITY; ?> </th>
                    <td>
                        <?php
                        if ($dataService['Patient']['patient_group_id'] != "") {
                            $query = mysql_query("SELECT name FROM patient_groups WHERE id=" . $dataService['Patient']['patient_group_id']);
                            while ($row = mysql_fetch_array($query)) {
                                if ($dataService['Patient']['patient_group_id'] == 1) {
                                    echo $row['name'];
                                } else {
                                    $queryNationality = mysql_query("SELECT name FROM nationalities WHERE id=" . $dataService['Patient']['nationality']);
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
                    <th><?php echo TABLE_TELEPHONE; ?> </th>
                    <td colspan="5"><?php echo $dataService['Patient']['telephone']; ?></td>
                </tr>
                <tr>
                    <th><?php echo TABLE_ADDRESS; ?> </th>
                    <td colspan="7">
                        <?php
                        if ($dataService['Patient']['address'] != "") {
                            echo $dataService['Patient']['address'];
                        }
                        if ($dataService['Patient']['location_id'] != "") {
                            $query = mysql_query("SELECT name FROM patient_locations WHERE id=" . $dataService['Patient']['location_id']);
                            while ($row = mysql_fetch_array($query)) {
                                if ($dataService['Patient']['address'] != "") {
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
        <legend><?php __(MENU_ECHO_SERVICE_INFO); ?></legend>
        <div style="float: right; width:30px;">
            <?php
            if ($allowPrint) {
                echo "<a href='#' class='btnPrint$rand' rel='{$dataService['EchographiePatient']['id']}' ><img alt='Print'  onmouseover='Tip(\"" . ACTION_PRINT . "\")'  src='{$this->webroot}img/button/printer.png' /></a>";
            }
            ?>
        </div>
        <div>
            <table class="defaultTable">
                <tr>
                    <td style="width: 40%; vertical-align: top;">
                        <fieldset>
                            <table class="defualtTable" style="width: 100%;">
                                <tr>
                                    <td style="width: 30%;">Echographie :</td>
                                    <td>    
                                        <?php echo $dataService['EchographyInfom']['name']; ?>                                                
                                    </td> 
                                </tr>
                                <tr>
                                    <td>Examen par :</td>
                                    <td>
                                        <?php echo $dataService['EchographiePatient']['doctor_name']; ?>
                                    </td>                                            
                                </tr>  
                                <tr>
                                    <td>Indication :</td>
                                    <td>
                                        <?php echo $dataService['Indication']['name']; ?>
                                    </td>                                            
                                </tr>
                                <tr>
                                    <td>D.D.R :</td>                                
                                    <td>       
                                        <?php echo $dataService['EchographiePatient']['ddr']; ?>
                                    </td>                                            
                                </tr> 
                            </table>                                                                      
                        </fieldset>
                    </td>
                    <td style="width: 65%; vertical-align: top;" rowspan="2">                            
                        <fieldset class="description" style="min-height: 362px; vertical-align: top;">
                            <div class="box" style="width:95%;height: 95%;">
                                <?php echo $dataService['EchographiePatient']['description']; ?>
                            </div>
                        </fieldset>
                    </td>
                    <!--For big child-->
                </tr>
                <tr>
                    <td valign="top">
                        <fieldset>   
                            <table class="defualtTable" style="width: 100%;">
                                <tr>
                                    <td style="width: 30%;">ទំរង់កូន :</td>
                                    <td><?php echo $dataService['EchographiePatient']['form_child']; ?></td>                                                                     
                                </tr>
                                <tr>
                                    <td>ចំនួនកូន :</td>
                                    <td><?php echo $dataService['EchographiePatient']['num_child']; ?></td>                                                                         
                                </tr>  
                                <tr>
                                    <td>សុខភាពកូន :</td>
                                    <td><?php echo $dataService['EchographiePatient']['healthy_child']; ?></td>                                                                      
                                </tr>
                                <tr>
                                    <td>ភេទកូន :</td>
                                    <td><?php echo $dataService['EchographiePatient']['sex_child']; ?></td>                                                                      
                                </tr>
                                <tr>
                                    <td>ទឹកភ្លោះ :</td>
                                    <td><?php echo $dataService['EchographiePatient']['teok_plos']; ?></td>                                                                      
                                </tr>
                                <tr>
                                    <td>ទីតាំងសុក :</td>
                                    <td><?php echo $dataService['EchographiePatient']['location_sok']; ?></td>                              
                                </tr>
                                <tr>
                                    <td>ទំងន់កូន :</td>
                                    <td>
                                        <?php echo $dataService['EchographiePatient']['weight_child']; ?>
                                        &nbsp;&nbsp;
                                        ក្រាម
                                    </td>                                                    
                                </tr>
                                <tr>
                                    <td>អាយុកូន :</td>
                                    <td>
                                        <?php echo $dataService['EchographiePatient']['week_child']; ?>
                                        &nbsp;&nbsp;
                                        សបា្តហ៍
                                        &nbsp;&nbsp;
                                        <?php echo $dataService['EchographiePatient']['day_child']; ?>
                                        &nbsp;&nbsp;
                                        ថ្ងៃ
                                    </td>                            
                                </tr>
                                <tr>
                                    <td>គ្រប់ខែថ្ងៃ :</td>
                                    <td>
                                        <?php echo $dataService['EchographiePatient']['born_date']; ?>
                                        &nbsp;&nbsp;
                                        ក្រាម
                                    </td>                                                      
                                </tr>
                            </table>
                        </fieldset>
                    </td>                            
                </tr>
            </table> 
        </div>
    </fieldset>    
<?php endforeach; ?>