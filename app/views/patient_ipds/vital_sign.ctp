<script type="text/javascript">
    $(document).ready(function(){
        document.getElementById("PatientIpdVitalSignAddForm").reset();
        $("#PatientIpdVitalSignAddForm").validationEngine();
    });
</script>
<?php 
echo $this->Form->create('PatientIpdVitalSign', array ('id'=>'PatientIpdVitalSignAddForm'));
?>
<input type='hidden' name="data[PatientIpdVitalSign][patient_ipd_id]" value='<?php echo $patientIPDId;?>'/>
<input type='hidden' name="data[PatientIpdVitalSign][id]" value='<?php echo $this->data['PatientIpdVitalSign']['id'];?>'/>
<fieldset style="border: 1px dashed #3C69AD;">
    <legend><?php __(MENU_VITAL_SING_INFO); ?></legend>
    <table style="width: 70%;" cellspacing="0">
        <tr>
            <td style="width: 20%;"><label for="PatientIPDVitalSignBp"><?php echo 'BP'; ?> :</label></td>
            <td><?php echo $this->Form->text('bp', array('style' => 'width: 150px;', 'autocomplete' => "off")); ?> mmHg</td>
        </tr>
        <tr>
            <td><label for="PatientIPDVitalSignHr"><?php echo 'HR'; ?> :</label></td>
            <td><?php echo $this->Form->text('hr', array('style' => 'width: 150px;', 'autocomplete' => "off")); ?> bpm</td>
        </tr>
        <tr>
            <td><label for="PatientIPDVitalSignTemperature"><?php echo TABLE_TEMPERATURE; ?> :</label></td>
            <td><?php echo $this->Form->text('temperature', array('style' => 'width: 150px;')); ?> °C</td>      
        </tr>
        <tr>     
            <td><label for="PatientIPDVitalSignRr"><?php echo 'RR'; ?> :</label></td>
            <td><?php echo $this->Form->text('rr', array('style' => 'width: 150px;')); ?> / min</td>
        </tr>
		<tr>     
            <td><label for="PatientIPDVitalSignSpo2"><?php echo "SpO<sub>2</sub>"; ?> :</label></td>
            <td><?php echo $this->Form->text('sop2', array('style' => 'width: 150px;')); ?></td>
        </tr>
        <tr style="display: none;">
            <td><label for="PatientIPDVitalSignUrine"><?php echo 'Urine'; ?> :</label></td>
            <td><?php echo $this->Form->text('urine', array('style' => 'width: 150px;')); ?> ml/24h</td>               
        </tr>
        <tr style="display: none;">
            <td><label for="PatientIPDVitalSignGasFecal"><?php echo 'Gas and Fecal'; ?> : +/-</label></td>
            <td><?php echo $this->Form->text('gas_fecal', array('style' => 'width: 150px;')); ?></td>      
        </tr>
        <tr style="display: none;">
            <td><label for="PatientIPDVitalSignDrainage"><?php echo 'Drainage'; ?> :</label></td>
            <td><?php echo $this->Form->text('drainage', array('style' => 'width: 150px;')); ?> mL</td>      
        </tr>
        <tr>
            <td><label for="PatientIPDVitalSignNote"><?php echo 'Note'; ?> :</label></td>
            <td><?php echo $this->Form->textarea('note', array('style' => 'width: 80%;')); ?></td>    
        </tr>
    </table>      
</fieldset>