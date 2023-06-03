<?php $absolute_url = FULL_BASE_URL . Router::url("/", false); ?>
<?php echo $javascript->link('jquery.form'); ?>
<style type="text/css">
    textarea {
        height: 100px;
        width: 100%;
    }
</style>
<script type="text/javascript">
    $(document).ready(function(){
        $("#PatientEmergencyEditForm").validationEngine();
        $("#PatientEmergencyEditForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".loading").show();
            },
            success: function(result) {
                $("#tabs1").tabs("select", 0);
                $("#tabObservation").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabObservation/<?php echo $this->params['pass'][0]; ?>");
            }
        });
        
        $("#consultationObservation").accordion({
            collapsible: true,
            autoHeight: false,
            navigation: false,
            active: false
        });
        
        $(".btnPrint").click(function(event) {
            event.preventDefault(); 
            var PatientEmergencyObservationId = $(this).attr('PatientEmergencyObservationId');                       
            var url = "<?php echo $this->base . '/' . $this->params['controller']; ?>/printObservationMedical/" + PatientEmergencyObservationId;
            $.ajax({
                type: "POST",
                url: url,
                beforeSend: function() {
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner.gif');
                },
                success: function(printResult) {
                    w = window.open();
                    w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                    w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                    w.document.write(printResult);
                    w.document.close();
                    $(".loader").attr('src', '<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif');
                }
            });
        });
        
        
        $(".btnDisable").click(function(event){
            event.preventDefault();
            $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            event.stopPropagation();
            var id = $(this).attr("rel");
            var name = $(this).attr("name");
            var className = $(this).attr("class");
            var options = "";
            options = (className=='btnActive')?"inactive":"active";
            
            $("#dialog").dialog('option', 'title', '<?php echo DIALOG_CONFIRMATION; ?>');
            $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Are you sure you want to delete <b>'+name+'</b>?</p>');
            $("#dialog").dialog({
                title: '<?php echo DIALOG_CONFIRMATION; ?>',
                resizable: false,
                modal: true,
                width: 'auto',
                height: 'auto',
                open: function(event, ui){
                    $(".ui-dialog-buttonpane").show();
                },
                buttons: {
                    '<?php echo ACTION_OK; ?>': function() {
                        $.ajax({
                            url:"<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/deleteObservation/"+id,
                            type: "post",
                            data: 'data[PatientEmergencyObservation][id]='+id,
                            success:function(sms){
                                if(sms == 'success'){
                                    $.ajax({
                                        url:"<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/tabObservation/"+<?php echo $this->params['pass'][0];?>,
                                        data:"",
                                        success:function(html){
                                            $("#tabObservation").html(html);
                                            $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                        }
                                    });
                                }else{
                                    $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                    alert("Data can't save. Please try again!");
                                }
                            }
                        });
                        $(this).dialog("close");
                    },
                    '<?php echo ACTION_CANCEL; ?>': function() {
                        $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                        $(this).dialog("close");
                    }
                }
            });
            
        });
        
        $(".btnAddObservation").click(function(){
            $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            $.ajax({
                url:"<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/addObservation/<?php echo $this->params['pass'][0];?>",
                data:"",
                success:function(html){
                    $("#tabObservation").html(html);
                    $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                }
            });
        });
    });
</script>

<div class="buttons">
    <button type="submit" class="positive btnAddObservation">
        <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
        <?php echo 'Add New Observation Medical'; ?>
    </button>    
</div>

<div class="clear"></div>
<div id="consultationObservation">    
    <?php
    $ind = 0;    
    if(!empty($consultation)){
        foreach ($consultation as $consultation):
            ?>
            <h3>
                <a href="#">
                    <?php echo $consultation['PatientEmergency']['emergency_code'] . '-' . date('d/m/Y H:i:s', strtotime($consultation['PatientEmergencyObservation']['created'])); ?>
                    <div style="float:right;">
                        <img alt="" PatientEmergencyObservationId="<?php echo $consultation['PatientEmergencyObservation']['id']?>" src="<?php echo $this->webroot; ?>img/button/printer.png" class="btnPrint"  name="<?php echo $consultation['PatientEmergency']['emergency_code']; ?>" onmouseover="Tip('<?php echo ACTION_PRINT; ?>')" />
                    </div>                    
                    <div style="float:right;">
                        <img alt="" src="<?php echo $this->webroot; ?>img/button/inactive.png" class="btnDisable" rel="<?php echo $consultation['PatientEmergencyObservation']['id']; ?>" name="<?php echo $consultation['PatientEmergencyObservation']['created']; ?>" onmouseover="Tip('<?php echo 'Disable'; ?>')" />
                    </div>
                </a>
            </h3>
            <div class="<?php echo $consultation['PatientEmergencyObservation']['id']; ?>">
                <?php echo $this->Form->create('PatientEmergency', array('id' => 'PatientEmergencyEditForm', 'url' => '/patient_emergencies/editObservation/' . $consultation['PatientEmergencyObservation']['id'] .'/'. $id, 'enctype' => 'multipart/form-data')); ?>
                <div class="legend">
                    <div class="legend_title"><label for="PatientEmergencyEntryMotif"><b><?php echo 'Entry Motif'; ?></b></label></div>
                    <div class="legend_content"><?php echo $this->Form->input('entry_motif', array('label' => false, 'type' => 'textarea', 'value' => $consultation['PatientEmergencyObservation']['entry_motif'])); ?></div>
                </div>
                <br/>
                <div class="legend">
                    <div class="legend_title"><label for="PatientEmergencyPresentMedicalCondition"><b><?php echo 'Present Medical Condition'; ?></b></label></div>
                    <div class="legend_content"><?php echo $this->Form->input('present_medical_condition', array('label' => false, 'type' => 'textarea', 'value' => $consultation['PatientEmergencyObservation']['present_medical_condition'])); ?></div>
                </div>
                <br/>
                <div class="legend">
                    <div class="legend_title"><label for="PatientEmergencyMedical"><b><?php echo 'Past Medical History'; ?></b></label></div>
                    <div class="legend_content">
                        <table style="width: 100%">
                            <tr>
                                <td style="width: 20%;"><labe for="PatientEmergencyMedical"><?php echo 'Medical'; ?> :</label></td>
                                <td>
                                    <?php echo $this->Form->input('medical', array('label' => false, 'type' => 'textarea', 'value' => $consultation['PatientEmergencyObservation']['medical'])); ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 20%;"><labe for="PatientEmergencySurfical"><?php echo 'Surfical'; ?> :</label></td>
                                <td>
                                    <?php echo $this->Form->input('surfical', array('label' => false, 'type' => 'textarea', 'value' => $consultation['PatientEmergencyObservation']['surfical'])); ?>                    
                                </td>
                            </tr>           
                        </table>        
                    </div>
                </div>  
                <br/>
                <div class="legend">
                    <div class="legend_title"><label for="PatientEmergencyMedical"><b><?php echo 'Clinical Examination'; ?></b></label></div>
                    <div class="legend_content">
                        <table style="width: 100%">
                            <tr>
                                <td style="width: 20%;"><labe for="PatientEmergencyGeneralSign"><?php echo 'General Sign'; ?> :</label></td>
                                <td>
                                    <?php echo $this->Form->input('general_sign', array('label' => false, 'type' => 'textarea', 'value' => $consultation['PatientEmergencyObservation']['general_sign'])); ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 20%;"><labe for="PatientEmergencyCadiovascular"><?php echo 'Cardiovascular'; ?> :</label></td>
                                <td>
                                    <?php echo $this->Form->input('cadiovascular', array('label' => false, 'type' => 'textarea', 'value' => $consultation['PatientEmergencyObservation']['cadiovascular'])); ?>                    
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 20%;"><labe for="PatientEmergencyRespiratory"><?php echo 'Respiratory'; ?> :</label></td>
                                <td>
                                    <?php echo $this->Form->input('respiratory', array('label' => false, 'type' => 'textarea', 'value' => $consultation['PatientEmergencyObservation']['respiratory'])); ?>                    
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 20%;"><labe for="PatientEmergencyDigestifs"><?php echo 'Digestifs'; ?> :</label></td>
                                <td>
                                    <?php echo $this->Form->input('digestifs', array('label' => false, 'type' => 'textarea', 'value' => $consultation['PatientEmergencyObservation']['digestifs'])); ?>                    
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 20%;"><labe for="PatientEmergencyUroGenital"><?php echo 'Uro-Genital'; ?> :</label></td>
                                <td>
                                    <?php echo $this->Form->input('uro_genital', array('label' => false, 'type' => 'textarea', 'value' => $consultation['PatientEmergencyObservation']['uro_genital'])); ?>                    
                                </td>
                            </tr>
                        </table>        
                    </div>
                </div>
                <br/>
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
            <?php
            $ind++;
        endforeach;
    }
    ?>
</div>
<div id="dialog" title=""></div>
<div id="dialogPrint" title="" style="display: none;">
    <br />
    <center>
        <div class="buttons" style="display: inline-block;">
            <button type="button" class="btnPatientEmergencyObservation" class="positive">
                <img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/>
                <?php echo ACTION_PRINT; ?>
            </button>
        </div>
    </center>
</div>
<div id="patientConsultation" style="display: none;"></div>