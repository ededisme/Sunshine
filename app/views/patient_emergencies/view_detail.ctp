<?php
include('includes/function.php');
$absolute_url  = FULL_BASE_URL . Router::url("/", false);
?>
<script type="text/javascript">
    $(document).ready(function(){
        $("#tabs1").tabs();
        $("#tabObservation").load("<?php echo $absolute_url.$this->params['controller']; ?>/tabObservation/<?php echo $this->params['pass'][0]; ?>/view");
        $("#btntabObservation").click(function(){
            $("#tabObservation").load("<?php echo $absolute_url.$this->params['controller']; ?>/tabObservation/<?php echo $this->params['pass'][0]; ?>/view");
        });
        $("#btnTabEvolution").click(function(){
            $("#tabEvolutionNum").load("<?php echo $absolute_url.$this->params['controller']; ?>/tabEvolutionNum/<?php echo $this->params['pass'][0]; ?>/view");
        });  
        $(".btnBackPatientEmergency").click(function(event){
            event.preventDefault();
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide( "slide", { direction: "right" }, 500, function() {
                leftPanel.show();
                rightPanel.html('');
            });
        });
        $("#consultationObservation").accordion({
            collapsible: true,
            autoHeight: false,
            navigation: false,
            active: false
        });
    });
</script>
<style>
    form textarea{
        width: 98% !important;
        height: 50px !important;
    }
    .ui-tabs-panel{
        overflow: auto !important;
        height: auto !important;
    }
</style>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="#" class="positive btnBackPatientEmergency">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<fieldset>
    <legend><?php __(PATIENT_INFO); ?></legend>
    <div id="profile">
        <table class="info">
            <tr>
                <th style="width: 15%;"><?php echo PATIENT_CODE; ?></th>
                <td style="width: 35%;">: <?php echo $patient['Patient']['patient_code']; ?></td>
                <th style="width: 15%;"><?php echo PATIENT_NAME; ?></th>
                <td style="width: 35%;">: <?php echo $patient['Patient']['patient_name']; ?></td>            
            </tr>        
            <tr>
                <th><?php echo TABLE_SEX; ?></th>
                <td>: 
                    <?php
                    if ($patient['Patient']['sex'] == "F") {
                        echo GENERAL_FEMALE;
                    } else {
                        echo GENERAL_MALE;
                    }
                    ?>
                </td>
                <th><?php echo TABLE_AGE; ?></th>            
                <td>: 
                    <?php
                    $then_ts = strtotime($patient['Patient']['dob']);
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
            </tr>
            <tr>
                <th><?php __(TABLE_OCCUPATION); ?></th>
                <td>: <?php echo $patient['Patient']['occupation']; ?></td>
                <th><?php __(TABLE_NATIONALITY); ?></th>
                <td>: 
                    <?php
                    if ($patient['Patient']['patient_group_id'] != "") {
                        $query = mysql_query("SELECT name FROM patient_groups WHERE id=" . $patient['Patient']['patient_group_id']);
                        while ($row = mysql_fetch_array($query)) {
                            if ($patient['Patient']['patient_group_id'] == 1) {
                                echo $row['name'];
                            } else {
                                $queryNationality = mysql_query("SELECT name FROM nationalities WHERE id=".$patient['Patient']['nationality']);
                                while ($result = mysql_fetch_array($queryNationality)) {
                                    echo $row['name'] . '&nbsp;&nbsp;(' . $result['name'] . ')';
                                }

                            }
                        }
                    } else {
                        echo $patient['Nationality']['name'];
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th><?php echo TABLE_EMAIL; ?></th>            
                <td>
                    : <?php echo $patient['Patient']['email']; ?>
                </td>
                <th><?php echo TABLE_TELEPHONE; ?></th>
                <td>: <?php echo $patient['Patient']['telephone']; ?></td>            
            </tr>                  
            <tr>
                <th><?php __(TABLE_ADDRESS); ?></th>
                <td>: <?php echo $patient['Patient']['address']; ?></td>        
                <th><?php __(TABLE_CITY_PROVINCE); ?></th>
                <td>: 
                    <?php
                    if ($patient['Patient']['location_id'] != "") {
                        $query = mysql_query("SELECT name FROM patient_locations WHERE id=" . $patient['Patient']['location_id']);
                        if (mysql_num_rows($query)) {
                            while ($row = mysql_fetch_array($query)) {
                                echo $row['name'];
                            }
                        }
                    }
                    ?>
                </td>
            </tr>       
        </table>
    </div>
</fieldset>
<br />
<div id="tabs1">
    <ul>
        <li><a id="btntabObservation" href="#tabObservation">Observation Medical</a></li>
        <li><a id="btnTabEvolution" href="#tabEvolutionNum">Evolution Clinic</a></li>       
    </ul>
    <div id="tabObservation"></div>
    <div id="tabEvolutionNum"></div>
</div>