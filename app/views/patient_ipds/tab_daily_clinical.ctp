
<?php
    // Authentication
    $this->element('check_access');
    $allowAdmin = checkAccess($user['User']['id'], 'users', 'editProfile');
    $absolute_url = FULL_BASE_URL . Router::url("/", false);
    require_once("includes/function.php");
?>

<script type="text/javascript">
    function addOrEditFollowUp(followUpId = '', type = '') {
        var patientConsultationId = $("#consultationId").val();
        var queuedDoctorId = $("#queueDoctorId").val();
        var queueId = $("#queueId").val();
        $.ajax({
            type: "GET",
            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/followup/" + followUpId + "/" + type,
            data: "",
            beforeSend: function () {
                $("#dialog9").html('<p id="loading" style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
            },
            success: function (msg) {
                $("#loading").hide();
                $("#dialog9").html(msg);
                $("#dialog9").dialog({
                    title: '<?php echo MENU_FOLLOW_UP_LABEL_ADD; ?>',
                    resizable: false,
                    modal: true,
                    width: '70%',
                    height: 500,
                    position: 'center',
                    buttons: {
                        "<?php echo ACTION_EDIT; ?>": function () {
                            var isFormValidated = $("#PatientFollowupIPDAddForm").validationEngine('validate');
                            if (!isFormValidated) {
                                return false;
                            } else {
                                var url = "<?php echo $this->base . '/' . $this->params['controller']; ?>/addNewFollowUp/" + patientConsultationId + '/' + queuedDoctorId + '/' + queueId;
                                var post = $('#PatientFollowupIPDAddForm').serialize();
                                $.post(url, post, function (rs) {
                                    $.ajax({
                                        type: "POST",
                                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/getFollowUp/" + patientConsultationId + '/' + queuedDoctorId,
                                        data: "",
                                        beforeSend: function () {
                                            $(".tbFollowUpPntIPDResult").html('<p id="loading" style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                                        },
                                        success: function (msg) {
                                            $("#tbFollowUpPntIPDResult").html(msg);
                                            $("#btnTabDailyClinical").click()
                                        }
                                    });
                                });
                                $(this).dialog("close");
                            }
                        }
                    }
                });
            }
        });
    }
    $(document).ready(function() {
        // Modify Follow up
        $(".btnEditFollowUpIPD").click(function () {
            $("#dialog9").html('');
            $("#PatientFollowupIPDAddForm").remove();
            var type = $(this).attr('type');
            var followUpId = $(this).attr('rel');
            addOrEditFollowUp(followUpId, type)
        });

        $(".btnIPDFollowUp").click(function () {
            $("#dialog9").html('');
            $("#PatientFollowupIPDAddForm").remove();
            addOrEditFollowUp()
        });
    })
</script>
    <?php
        $date1 = date("Y-m-d");
        foreach($consultation as $consultation):
            $display = "";
            $disabled = "";
            $date2 = date('Y-m-d', strtotime($consultation['PatientConsultation']['created']));        
            if(strtotime($date1) > strtotime($date2)){
                $display = "display:none;";
                $disabled = "disabled";
            }
    ?>
          <div class="legend" style="width: 50%; float: left; padding: 2px;">
            <input name="data[PatientConsultation][id]" type="hidden" id="consultationId" value="<?php echo $consultation['PatientConsultation']['id']; ?>"/>
            <input name="data[QeuedDoctor][id]" type="hidden" id="queueDoctorId" value="<?php echo $consultation['QeuedDoctor']['id']; ?>"/>
            <input name="data[Queue][id]" type="hidden" id="queueId" value="<?php echo $consultation['Queue']['id']; ?>"/>
            <div class="legend_title"><label for="PatientConsultationFollowUp"><b><?php echo MENU_FOLLOW_UP; ?></b></label></div>
            <div class="legend_content followUpPntIPDHistory" style="height: 240px; overflow-y: scroll; cursor: pointer;">                        
                <table class="table" width="100%" style="border: medium none; padding-top: 6px;">
                    <thead style="<?php
                    if ($consultation['PatientConsultation']['follow_up'] == "") {
                        echo 'display:none;';
                    }
                    ?>">
                        <tr>
                            <td style="border: none; font-weight: bold; background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo date('d/m/Y H:i:s', strtotime($consultation['PatientConsultation']['created'])); ?></td>
                            <td style="border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo getDoctor($consultation['PatientConsultation']['created_by']); ?></td>
                            <td style="width: 5%; border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);">
                                <?php
                                if ($consultation['PatientConsultation']['created_by'] == $user['User']['id'] || $allowAdmin) {
                                    echo '<a href="#" class="btnEditFollowUpIPD" type="consult" rel="' . $consultation['PatientConsultation']['id'] . '" ><img alt="Edit" onmouseover="Tip(\'' . ACTION_EDIT . '\')" src="' . $this->webroot . 'img/action/edit.png" /></a>';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: none; padding-left: 5%; color: rgb(0, 0, 0);" colspan="3"><?php echo nl2br($consultation['PatientConsultation']['follow_up']); ?></td>
                        </tr>
                    </thead>
                    <tbody id="tbFollowUpPntIPDResult" class="tbFollowUpPntIPDResult">
                        <?php
                        $index = 1;
                        $query_followup = mysql_query("SELECT * FROM patient_followups WHERE is_active=1 AND patient_consultation_id=" . $consultation['PatientConsultation']['id'] . " ORDER BY created ASC");
                        while ($data_followup = mysql_fetch_array($query_followup)) {
                            ?>
                            <tr>
                                <td style="border: none; font-weight: bold; background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo date('d/m/Y H:i:s', strtotime($data_followup['created'])); ?></td>
                                <td style="border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo getDoctor($data_followup['created_by']); ?></td>
                                <td style="width: 5%; border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);">
                                    <?php
                                    if ($data_followup['created_by'] == $user['User']['id'] || $allowAdmin) {
                                        echo '<a href="#" class="btnEditFollowUpIPD" type="followup" rel="' . $data_followup['id'] . '" ><img alt="Edit" onmouseover="Tip(\'' . ACTION_EDIT . '\')" src="' . $this->webroot . 'img/action/edit.png" /></a>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="border: none; padding-left: 5%; color: rgb(0, 0, 0);" colspan="3">
                                    <?php echo nl2br($data_followup['followup']); ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>                
            <div class="legend_content">
                <div class="buttons">
                    <a href="#" queuedDoctorId="<?php echo $consultation['PatientConsultation']['queued_doctor_id']; ?>" name="<?php echo $consultation['PatientConsultation']['id']; ?>" title="<?php echo $consultation['Queue']['id']; ?>" class="positive btnIPDFollowUp" >
                        <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
                        <?php echo MENU_FOLLOW_UP_LABEL_ADD; ?>
                    </a>
                </div>
                <div style="clear: both;"></div>
            </div>
        </div>
    <?php endforeach; ?>