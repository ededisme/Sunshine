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
        
        $(".btnSave").click(function(){
            var patientEmergencyId = $(".PatientEmergencyId").val();
            var patientEmergencyEvolutionId = $(".PatientEmergencyEvolutionId").val();            
            var i = 1;
            var formName = "#PatientEmergencyEvolutionEditForm";
            var validateBack =$(formName).validationEngine("validate");
            if(!validateBack){
                return false;
            }else{
                if(i>1){
                    return false;
                }else{
                    $(".txtSave").html("<?php echo ACTION_LOADING; ?>");
                }
                i++;
                var values = $("#PatientEmergencyEvolutionEditForm").serialize();                
                $.ajax({
                    type: "POST",
                    url: "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/editEvolutionNum/"+patientEmergencyEvolutionId+'/'+patientEmergencyId,
                    data: values,
                    success: function(sms){
                        if(sms == 'success'){
                            $.ajax({
                                url:"<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/tabEvolutionNum/"+<?php echo $this->params['pass'][0];?>,
                                data:"",
                                success:function(html){
                                    $("#tabEvolutionNum").html(html);
                                    $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                }
                            });
                        }else{
                            $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                            alert("Data can't save. Please try again!");
                        }
                    }
                });
            }
        });        
        
        $('.editPatientEmergencyDateEvolution').datetimepicker(
        {
            changeMonth: true,
            changeYear: true,
            timeFormat: 'hh:mm',
            showSecond: false,
            dateFormat:'yy-mm-dd'            
        });
        
        $("#consultationEvolution").accordion({
            collapsible: true,
            autoHeight: false,
            navigation: false,
            active: false
        });
             
        $(".btnPrint").click(function(event) {
            event.preventDefault(); 
            var PatientEmergencyEvolutionId = $(this).attr('PatientEmergencyEvolutionId');                  
            var url = "<?php echo $this->base . '/' . $this->params['controller']; ?>/printEvolutionNum/" + PatientEmergencyEvolutionId;
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
                            url:"<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/deleteEvolutionNum/"+id,
                            type: "post",
                            data: 'data[PatientEmergencyEvolution][id]='+id,
                            success:function(sms){
                                if(sms == 'success'){
                                    $.ajax({
                                        url:"<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/tabEvolutionNum/"+<?php echo $this->params['pass'][0];?>,
                                        data:"",
                                        success:function(html){
                                            $("#tabEvolutionNum").html(html);
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
        
        $(".btnAddEvolutionNum").click(function(){
            $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            $.ajax({
                url:"<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/addEvolutionNum/<?php echo $this->params['pass'][0];?>",
                data:"",
                success:function(html){
                    $("#tabEvolutionNum").html(html);
                    $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                }
            });
        });
        
    });
</script>

<div class="buttons">
    <button type="submit" class="positive btnAddEvolutionNum">
        <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
        <?php echo 'Add New Evolution Clinic'; ?>
    </button>    
</div>

<div class="clear"></div>
<div id="consultationEvolution">    
    <?php
    $ind = 0;    
    if(!empty($consultation)){
        foreach ($consultation as $consultation):
            ?>
            <h3>
                <a href="#">
                    <?php echo $consultation['PatientEmergency']['emergency_code'] . '-' . date('d/m/Y H:i:s', strtotime($consultation['PatientEmergencyEvolution']['created'])); ?>
                    <div style="float:right;">
                        <img alt="" PatientEmergencyEvolutionId="<?php echo $consultation['PatientEmergencyEvolution']['id']?>" src="<?php echo $this->webroot; ?>img/button/printer.png" class="btnPrint"  name="<?php echo $consultation['PatientEmergency']['emergency_code']; ?>" onmouseover="Tip('<?php echo ACTION_PRINT; ?>')" />
                    </div>                    
                    <div style="float:right;">
                        <img alt="" src="<?php echo $this->webroot; ?>img/button/inactive.png" class="btnDisable" rel="<?php echo $consultation['PatientEmergencyEvolution']['id']; ?>" name="<?php echo $consultation['PatientEmergencyEvolution']['created']; ?>" onmouseover="Tip('<?php echo 'Disable'; ?>')" />
                    </div>
                </a>
            </h3>
            <div class="<?php echo $consultation['PatientEmergencyEvolution']['id']; ?>">
                <?php echo $this->Form->create('PatientEmergency', array('id' => 'PatientEmergencyEvolutionEditForm', 'url' => '/patient_emergencies/editEvolutionNum/' . $consultation['PatientEmergencyEvolution']['id'] .'/'. $id, 'enctype' => 'multipart/form-data')); ?>
                
                <input class="PatientEmergencyId" name="data[PatientEmergency][id]" type="hidden" value="<?php echo $id;?>"/>
                <input class="PatientEmergencyEvolutionId" name="data[atientEmergencyEvolution][id]" type="hidden" value="<?php echo $consultation['PatientEmergencyEvolution']['id'];?>"/>
                <div class="legend">
                    <div class="legend_title"><label for="PatientEmergencyEditDateEvolution"><b><?php echo 'Evolution Clinic'; ?></b></label></div>
                    <div class="legend_content">
                        <table style="width: 100%">
                            <tr>
                                <td style="width: 20%;"><labe for="PatientEmergencyEditDateEvolution"><?php echo 'Date'; ?> :</label></td>
                                <td>
                                    <?php echo $this->Form->input('edit_date_evolution', array('id' => 'PatientEmergencyEditDateEvolution'.$consultation['PatientEmergencyEvolution']['id'], 'class' => 'editPatientEmergencyDateEvolution', 'label' => false, 'type' => 'text', 'readonly' => true, 'value' => $consultation['PatientEmergencyEvolution']['date_evolution'])); ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 20%;"><labe for="PatientEmergencyEditEvolutionDescription"><?php echo 'Evolution Description'; ?> :</label></td>
                                <td>
                                    <?php echo $this->Form->input('edit_evolution_description', array('label' => false, 'type' => 'textarea', 'value' => $consultation['PatientEmergencyEvolution']['evolution_description'])); ?>                    
                                </td>
                            </tr>           
                        </table>        
                    </div>
                </div> 
                <br/>
                <div class="legend">
                    <div class="legend_title"><label for="PatientEmergencyPrescription"><b><?php echo 'Prescription'; ?></b></label></div>
                    <div class="legend_content"><?php echo $this->Form->input('prescription', array('label' => false, 'type' => 'textarea', 'value' => $consultation['PatientEmergencyEvolution']['prescription'])); ?></div>
                </div>
                <br/>
                <?php echo $this->Form->end(); ?>  
                <div class="buttons"> 
                    <button type="submit" class="positive btnSave" id="submit">
                        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
                        <span class="txtSave"><?php echo ACTION_SAVE; ?></span>
                    </button>  
                </div>
                <div style="clear: both;"></div>
               
            </div>
            <?php
            $ind++;
        endforeach;
    }
    ?>
</div>
<div id="dialogEvolution" title=""></div>
<div id="dialogPrintEvolution" title="" style="display: none;">
    <br />
    <center>
        <div class="buttons" style="display: inline-block;">
            <button type="button" class="btnPatientEmergencyEvolution" class="positive">
                <img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/>
                <?php echo ACTION_PRINT; ?>
            </button>
        </div>
    </center>
</div>
<div id="patientConsultationEvolution" style="display: none;"></div>