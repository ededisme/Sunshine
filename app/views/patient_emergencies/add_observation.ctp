<?php 
echo $this->element('prevent_multiple_submit'); 
$absolute_url = FULL_BASE_URL . Router::url("/", false);
echo $javascript->link('uninums.min'); 
?>
<?php $tblName = "tbl" . rand(); ?>

<script type="text/javascript">
    $(document).ready(function(){
        $(".back").click(function(){
            $("#tabs1").tabs("select", 0);
            $("#tabObservation").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabObservation/<?php echo $this->params['pass'][0]; ?>");
        });
        
        $(".btnSave").click(function(){
            var i = 1;
            var formName = "#PatientEmergencyAddObservationForm";
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
                var values = $( "#PatientEmergencyAddObservationForm" ).serialize();
                $.ajax({
                    type: "POST",
                    url: "<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/addObservation",
                    data: values,
                    success: function(sms){
                        if(sms == 'success'){
                            $.ajax({
                                url:"<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/tabObservation/"+<?php echo $this->params['pass'][0];?>,
                                data:"",
                                success:function(html){
                                    $("#tabObservation").html(html);
                                    $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                    $("#tabs1").tabs("select", 0);
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
    });    
    
    function isNumberKey(event){
        var charCode = (event.which)?event.which : event.keyCode;
        if ((charCode > 31 && (charCode < 46 || charCode > 57))|| charCode === 47){
            return false;
        }
        return true;
    }
        
</script>
<?php echo $this->Form->create('PatientEmergency'); ?>
<input id="PatientEmergencyId" name="data[PatientEmergency][id]" type="hidden" value="<?php echo $id;?>"/>

<div class="legend">
    <div class="legend_title"><label for="PatientEmergencyEntryMotif"><b><?php echo 'Entry Motif'; ?></b></label></div>
    <div class="legend_content"><?php echo $this->Form->input('entry_motif', array('label' => false, 'type' => 'textarea')); ?></div>
</div>
<br/>
<div class="legend">
    <div class="legend_title"><label for="PatientEmergencyPresentMedicalCondition"><b><?php echo 'Present Medical Condition'; ?></b></label></div>
    <div class="legend_content"><?php echo $this->Form->input('present_medical_condition', array('label' => false, 'type' => 'textarea')); ?></div>
</div>
<br/>
<div class="legend">
    <div class="legend_title"><label for="PatientEmergencyMedical"><b><?php echo 'Past Medical History'; ?></b></label></div>
    <div class="legend_content">
        <table style="width: 100%">
            <tr>
                <td style="width: 20%;"><labe for="PatientEmergencyMedical"><?php echo 'Medical'; ?> :</label></td>
                <td>
                    <?php echo $this->Form->input('medical', array('label' => false, 'type' => 'textarea')); ?>
                </td>
            </tr>
            <tr>
                <td style="width: 20%;"><labe for="PatientEmergencySurfical"><?php echo 'Surfical'; ?> :</label></td>
                <td>
                    <?php echo $this->Form->input('surfical', array('label' => false, 'type' => 'textarea')); ?>                    
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
                    <?php echo $this->Form->input('general_sign', array('label' => false, 'type' => 'textarea')); ?>
                </td>
            </tr>
            <tr>
                <td style="width: 20%;"><labe for="PatientEmergencyCadiovascular"><?php echo 'Cardiovascular'; ?> :</label></td>
                <td>
                    <?php echo $this->Form->input('cadiovascular', array('label' => false, 'type' => 'textarea')); ?>                    
                </td>
            </tr>
            <tr>
                <td style="width: 20%;"><labe for="PatientEmergencyRespiratory"><?php echo 'Respiratory'; ?> :</label></td>
                <td>
                    <?php echo $this->Form->input('respiratory', array('label' => false, 'type' => 'textarea')); ?>                    
                </td>
            </tr>
            <tr>
                <td style="width: 20%;"><labe for="PatientEmergencyDigestifs"><?php echo 'Digestifs'; ?> :</label></td>
                <td>
                    <?php echo $this->Form->input('digestifs', array('label' => false, 'type' => 'textarea')); ?>                    
                </td>
            </tr>
            <tr>
                <td style="width: 20%;"><labe for="PatientEmergencyUroGenital"><?php echo 'Uro-Genital'; ?> :</label></td>
                <td>
                    <?php echo $this->Form->input('uro_genital', array('label' => false, 'type' => 'textarea')); ?>                    
                </td>
            </tr>
        </table>        
    </div>
</div> 
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

