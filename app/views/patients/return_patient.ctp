<?php 
echo $this->element('prevent_multiple_submit'); 
$absolute_url = FULL_BASE_URL . Router::url("/", false);
echo $javascript->link('uninums.min'); 
$rnd = rand();
$dialogPrintWaitingNumber = "dialogPrintWaitingNumber" . $rnd;

?>
<style type="text/css">
    .chzn-select {
        z-index: 23423423423;
    }
</style>
<script type="text/javascript">
    var type = "<?php echo $type;?>";
    $(document).ready(function() {
        //$(".chzn-select").css("z-index", 2000);
        $(".chzn-select").chosen();        
        $("#submit").click(function(){                        
            if($("#PatientReturnPatientForm").validationEngine('validate')){
                var doctorId = $("#patientDoctorId").val();
                var btnWatingNumberPatient=$(".<?php echo $dialogPrintWaitingNumber; ?>").html();                
                if(doctorId!=""){
                    $("#dialog").dialog( "close" );
                    var url = '<?php echo $absolute_url . $this->params['controller']; ?>/addPatientWaitingNumber/<?php echo $module.'/'.$queueDocId.'/'.$queueId;?>';                                               
                    var post = $('#PatientReturnPatientForm').serialize();                    
                    $.post(url,post,function(queueId){                        
                        $(".<?php echo $dialogPrintWaitingNumber; ?>").html(btnWatingNumberPatient);
                        $(".<?php echo $dialogPrintWaitingNumber; ?>").dialog({
                            title: '<?php echo ACTION_PRINT_WAITING_NUMBER; ?>',
                            resizable: false,
                            modal: true,
                            close: function() {        
                                $( this ).dialog( "close" );
                            },
                            buttons: {
                                Ok: function() {
                                    $( this ).dialog( "close" );
                                    oCache.iCacheLower = -1;
                                    if(type == 'patient'){
                                        oTablePatient.fnDraw(false);
                                    }else if(type == 'appointment'){
                                        oTablePatientAppointment.fnDraw(false);
                                    }else if(type == 'appDashboard'){
                                        oTablePatientAppDashboard.fnDraw(false);
                                    }
                                }
                            }
                        });                    
                        $(".watingNumberPatient").load("<?php echo $absolute_url . $this->params['controller']; ?>/printDoctorWaiting/" + queueId);
                        $(".btnWatingNumberPatient").click(function(){
                            w=window.open();
                            w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                            w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                            w.document.write('<style type="text/css">.info th{font-size: 12px;}.info td{font-size: 12px;}.table th{font-size: 12px;background: none;}.table td{font-size: 12px;font-weight: bold;}</style>');
                            w.document.write($(".watingNumberPatient").html());
                            w.document.close();
                            try
                            {
                                //Run some code here                                                                                                       
                                jsPrintSetup.setSilentPrint(1);
                                jsPrintSetup.printWindow(w);
                            }
                            catch(err)
                            {
                                //Handle errors here                                    
                                w.print();                                     
                            } 
                            w.close();
                            oCache.iCacheLower = -1;
                            if(type == 'patient'){
                                oTablePatient.fnDraw(false);
                            }else if(type == 'appointment'){
                                oTablePatientAppointment.fnDraw(false);
                            }else if(type == 'appDashboard'){
                                oTablePatientAppDashboard.fnDraw(false);
                            }
                        });
                    });
                    
                    $(".<?php echo $dialogPrintWaitingNumber; ?>").unbind('click').unbind('keyup').unbind('keypress').unbind('change').unbind('blur');
                }else{
                    alert('Please select doctor first.');
                    return false;                    
                }
            }else{
                return false;
            }               
        });
    });
</script>
<?php echo $this->Form->create('Patient'); ?>
<input name="data[Patient][id]" type="hidden" value="<?php echo $_GET['id'];?>"/>
<input id="patientType" name="data[Patient][type]" type="hidden" value="<?php echo $type;?>"/>

<table cellpadding="10" width="100%" id="tblReturnPatient">
    <tr>    
        <td><label for="patientDoctorId"><?php echo DOCTOR_DOCTOR; ?> :</label></td>
        <td>
            <select id="patientDoctorId" name="data[Patient][doctor_id]" calss='chzn-select validate[required]'>
                <option value=""><?php echo SELECT_OPTION;?></option>
                <?php 
                foreach ($doctors as $doctor) {                    
                    if($doctorId == $doctor['User']['id']){                        
                        echo '<option selected="selected" value="'.$doctor['User']['id'].'">'.$doctor['Employee']['name'].'</option>';
                    }else{
                        echo '<option value="'.$doctor['User']['id'].'">'.$doctor['Employee']['name'].'</option>';
                    }
                    
                }
                ?>
            </select>                
        </td>
    </tr> 
</table>
<br />
<?php echo $this->Form->end(); ?>
<div class="buttons">
    <button type="submit" class="positive" id="submit">
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <?php echo ACTION_SAVE; ?>
    </button>
</div>       
<div class="<?php echo $dialogPrintWaitingNumber; ?>" title="" style="display: none;">
    <br />
    <center>
        <div class="buttons" style="display: inline-block;">
            <button type="button" class="btnWatingNumberPatient" class="positive">
                <img src="<?php echo $this->webroot; ?>img/button/print.png" alt=""/>
                <?php echo ACTION_PRINT_WAITING_NUMBER; ?>
            </button>
        </div>
    </center>
</div>
<div class="watingNumberPatient" style="display: none;"></div>