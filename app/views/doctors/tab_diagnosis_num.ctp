<?php if(empty($diagnosisRequests)){echo GENERAL_NO_RECORD;exit();} ?>
<?php $absolute_url = FULL_BASE_URL . Router::url("/", false); ?>
<?php echo $javascript->link('jquery.form'); ?>
<script type="text/javascript">
    var index = 0;
    $(document).ready(function(){
        $("#DiagnosisForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".loading").show();
            },
            success: function(result) {
                $("#tabs").tabs("select", 5);
                $("#tabDiagnosisNum").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabDiagnosisNum/<?php echo $this->params['pass'][0]; ?>");
            }
        });
        
        $("#DiagnosisItemRequest").accordion({
            collapsible: true,
            autoHeight: false,
            navigation: true,
            active: false
        });  
          
        setTimeout(function(){
            equalHeight($(".column"));
        },2000); 
    });
    
    function equalHeight(group) {
        var tallest = 0;
        group.each(function() {
            var thisHeight = $(this).height();
            if(thisHeight > tallest) {
                tallest = thisHeight;
            }
        });
        group.height(tallest);
    }   
</script>


<div id="DiagnosisItemRequest">
    <?php
    $ind = 1;
    foreach ($diagnosisRequests as $historyDiagnosisRequest):
        ?>            
        <h3><a href="#"><?php echo date('d/m/Y H:i:s', strtotime($historyDiagnosisRequest['DiagnosisItemRequest']['created'])); ?>
                <div style="float:right;">
<!--                    <img alt="" src="<?php echo $this->webroot; ?>img/button/printer.png" class="btnPrint" onclick="location.href = '<?php echo $this->base; ?>/doctors/print_lab/<?php echo $this->params['pass'][0] . '/' . $historyDiagnosisRequest['DiagnosisItemRequest']['id'] ?>';" name="<?php echo $historyDiagnosisRequest['DiagnosisItemRequest']['queue_id']; ?>" onmouseover="Tip('<?php echo ACTION_PRINT; ?>')" />-->
                </div>
            </a>
        </h3>
        <div class="<?php echo $historyDiagnosisRequest['DiagnosisItemRequest']['id']; ?>">       

            <?php echo $this->Form->create('Diagnosis', array('id' => 'DiagnosisForm', 'url' => '/doctors/tabDiagnosisNum/' . $this->params['pass'][0])); ?>
            <?php echo $form->hidden('DiagnosisQueueId', array('name' => 'data[Queue][id]', 'value' => $qPatient['Queue']['id'])); ?>

            <?php
            $arrayDiagnosis = array();
            $queryDiagnosisRequest = mysql_query("SELECT group_dermato_item_id FROM diagnosis_item_request_details WHERE status=1 AND diagnosis_item_request_id=" . $historyDiagnosisRequest['DiagnosisItemRequest']['id']);
            while ($rowDiagnosisRequest = mysql_fetch_array($queryDiagnosisRequest)) {
                $arrayDiagnosis[] = $rowDiagnosisRequest['group_dermato_item_id'];
            }            
            ?>
            <fieldset>


                <legend style="font-size: 14px"><?php __('រោគវិនិច្ឆ័យនៃជំងឺសើស្បែក បែងចែកទៅតាមក្រុមនៃរោគសញ្ញាស្បែក'); ?></legend>
                <?php
                $count = 0;
                echo '<div class="column" style="float: left;padding: 0px;width: 35%;">';
                $queryGroup = mysql_query("SELECT id,name FROM group_dermato_groups WHERE sts=1 ORDER BY id asc");
                $index = 1;
                while ($rowGroup = mysql_fetch_array($queryGroup)) {

                    $checked = false;
                    if ($count == 4) {
                        echo '</div><div class="column" style="float: left;padding: 0px;width: 30%;">';
                        $count = 0;
                    }
                    echo '<p><b style="font-size:16px">Group ' . $index++ . ': ' . $rowGroup['name'] . '</b></p>';

                    $queryType = mysql_query("SELECT id,name FROM group_dermato_types WHERE sts=1 AND group_dermato_group_id = " . $rowGroup['id']);
                    while ($rowType = mysql_fetch_array($queryType)) {
                        echo '<p><b style="font-size:14px">' . $rowType['name'] . '</b></p>';

                        $checked = false;
                        $queryItem = mysql_query("SELECT id,name FROM group_dermato_items WHERE sts=1 AND group_dermato_type_id = " . $rowType['id']);
                        echo '<ul>';
                        while ($rowItem = mysql_fetch_array($queryItem)) {
                            if (!in_array($rowItem['id'], $arrayDiagnosis)) {
                                $checked = false;
                                echo '<li  type="1" style="font-size:12px">' . $this->Form->checkbox('', array('class' => 'checkBox', 'name' => 'data[GroupDermatoItemId][]', "value" => $rowItem['id'], 'hiddenField' => false, 'checked' => $checked, 'id' => 'groupDermatoItem_' . $rowItem['id'])) .
                                '<label style="text-align: center" for="groupDermatoItem_' . $rowItem['id'] . '">' . $rowItem['name'] . '</label>' .
                                '</li>';
                            }else{
                                $checked = true;
                                echo '<li  type="1" style="font-size:12px">' . $this->Form->checkbox('', array('class' => 'checkBox', 'name' => 'data[GroupDermatoItemId][]', "value" => $rowItem['id'], 'hiddenField' => false, 'checked' => $checked, 'id' => 'groupDermatoItem_' . $rowItem['id'])) .
                                '<label style="text-align: center;color:red" for="groupDermatoItem_' . $rowItem['id'] . '">' . $rowItem['name'] . '</label>' .
                                '</li>';
                            }
                        }
                        echo '</ul>';
                    }
                    $count++;
                }
                ?>
            </fieldset>                
            <br />



            <div class="legend">
                <div class="legend_title"><label for="LaboDiagnosisLocation"><b>Diagnosis</b></label></div>
                <div class="legend_content"><?php echo $this->Form->input('diagnosis_location', array('label' => false, 'type' => 'textarea', 'value' => $historyDiagnosisRequest['DiagnosisItemRequest']['diagnosis_location'])); ?></div>
            </div>
            <br />
            <div class="legend">
                <div class="legend_title"><label for="LaboDifferentialDiagnosis"><b>Differential Diagnosis</b></label></div>
                <div class="legend_content"><?php echo $this->Form->input('differential_diagnosis', array('label' => false, 'type' => 'textarea', 'value' => $historyDiagnosisRequest['DiagnosisItemRequest']['differential_diagnosis'])); ?></div>
            </div>
            <input type="hidden" type="text" value="<?php echo $qId; ?>" name="data[Queue][id]"/>
            <input type="hidden" type="text" value="<?php echo $historyDiagnosisRequest['DiagnosisItemRequest']['id']; ?>" name="data[DiagnosisItemRequest][id]"/>
            <br />      
            <div class="buttons">
                <button type="submit" class="positive">
                    <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
                    <?php echo ACTION_SAVE; ?>
                </button>
                <img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" class="loading" style="display: none;" />
            </div>
            <div style="clear: both;"></div>
            <?php echo $this->Form->end(); ?>




        </div>
        <?php $ind++;
    endforeach; ?>
</div>