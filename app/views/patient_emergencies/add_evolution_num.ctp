<?php 
echo $this->element('prevent_multiple_submit'); 
$absolute_url = FULL_BASE_URL . Router::url("/", false);
?>
<?php $tblName = "tbl" . rand(); ?>
<script type="text/javascript">
    $(document).ready(function(){ 
        $(".back").click(function(){            
            $("#tabs1").tabs("select", 1);
            $("#tabEvolutionNum").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabEvolutionNum/<?php echo $this->params['pass'][0]; ?>");
        });    
        $(".btnSave").click(function(){
            var i = 1;
            var formName = "#PatientEmergencyAddEvolutionNumForm";
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
                var values = $( "#PatientEmergencyAddEvolutionNumForm" ).serialize();
                $.ajax({
                    type: "POST",
                    url: "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/addEvolutionNum",
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
        $('#PatientEmergencyDateEvolution').datetimepicker(
        {
            changeMonth: true,
            changeYear: true,
            timeFormat: 'hh:mm',
            showSecond: false,
            dateFormat:'yy-mm-dd'            
        });
    });        
        
</script>
<?php echo $this->Form->create('PatientEmergency'); ?>
<input id="PatientEmergencyId" name="data[PatientEmergency][id]" type="hidden" value="<?php echo $id;?>"/>

<div class="legend">
    <div class="legend_title"><label for="PatientEmergencyMedical"><b><?php echo 'Evolution Clinic'; ?></b></label></div>
    <div class="legend_content">
        <table style="width: 100%">
            <tr>
                <td style="width: 20%;"><labe for="PatientEmergencyDateEvolution"><?php echo 'Date'; ?> :</label></td>
                <td>
                    <?php echo $this->Form->input('date_evolution', array('class' => 'PatientEmergencyDateEvolution', 'label' => false, 'type' => 'text', 'readonly' => true)); ?>
                </td>
            </tr>
            <tr>
                <td style="width: 20%;"><labe for="PatientEmergencyEvolutionDescription"><?php echo 'Evolution Description'; ?> :</label></td>
                <td>
                    <?php echo $this->Form->input('evolution_description', array('label' => false, 'type' => 'textarea')); ?>                    
                </td>
            </tr>           
        </table>        
    </div>
</div>  
<br/>
<div class="legend">
    <div class="legend_title"><label for="PatientEmergencyPrescription"><b><?php echo 'Prescription'; ?></b></label></div>
    <div class="legend_content"><?php echo $this->Form->input('prescription', array('label' => false, 'type' => 'textarea')); ?></div>
</div>
<br/>
<div class="clear"></div>
<?php echo $this->Form->end(); ?>
<div class="buttons">    
    <button type="submit" class="positive back">
        <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
        <?php echo ACTION_BACK; ?>
    </button> 
    
    <button type="submit" class="positive btnSave" id="submit">
        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
        <span class="txtSave"><?php echo ACTION_SAVE; ?></span>
    </button>    
</div>
<div class="clear"></div>

