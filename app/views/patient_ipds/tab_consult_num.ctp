<?php
// Authentication
$this->element('check_access');
$allowAdmin = checkAccess($user['User']['id'], 'users', 'editProfile');

if (empty($consultation)) {
    echo GENERAL_NO_RECORD;
    exit();
}
require_once("includes/function.php");
?>
<?php
$absolute_url = FULL_BASE_URL . Router::url("/", false);
$tblName = "tbl123";
?>
<?php echo $javascript->link('jquery.form'); ?>
<style type="text/css" media="screen">
    div.checkbox{
        width: 180px;
    }
    .table_print_labo td{ 
        border: none !important;
        line-height: 20px;
        padding: 0;
        margin: 0;
    }
    .legend_title{
        background: #3C69AD !important;
    }
    div.legend div.legend_content {
        border-left: 1px solid #3C69AD !important;
        border-right: 1px solid #3C69AD !important;
        border-bottom: 1px solid #3C69AD !important;
    }
</style>
<?php
$tblName = "tbl123";
$tblRand = "tbl" . rand();
?>
<script type="text/javascript">
    function eventKeyFollowUp() {
        $(".btnEditVitalSignIPD").click(function () {
            $("#dialog9").html('');
            $("#PatientIpdVitalSignAddForm").remove();
            var vitalSignId = $(this).attr('rel');
            var patientIPDId = $(this).attr('patientIPDId');
            $.ajax({
                type: "GET",
                url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/vitalSign/" + patientIPDId + "/" + vitalSignId,
                data: "",
                beforeSend: function () {
                    $("#dialog9").html('<p id="loading" style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                },
                success: function (msg) {
                    $("#loading").hide();
                    $("#dialog9").html(msg);
                    $("#dialog9").dialog({
                        title: '<?php echo MENU_VITAL_SING_INFO; ?>',
                        resizable: false,
                        modal: true,
                        width: '70%',
                        height: 500,
                        position: 'center',
                        buttons: {
                            "<?php echo ACTION_SAVE; ?>": function () {
                                var isFormValidated = $("#PatientIpdVitalSignAddForm").validationEngine('validate');
                                if (!isFormValidated) {
                                    return false;
                                } else {
                                    var url = "<?php echo $this->base . '/' . $this->params['controller']; ?>/addVitalSign/" + patientIPDId;
                                    var post = $('#PatientIpdVitalSignAddForm').serialize();
                                    $.post(url, post, function (rs) {
                                        $.ajax({
                                            type: "POST",
                                            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/getVitalSign/" + patientIPDId,
                                            data: "",
                                            beforeSend: function () {
                                                $("#tbVitalSignPntIPDResult").html('<p id="loading" style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                                            },
                                            success: function (msg) {
                                                $("#tbVitalSignPntIPDResult").html(msg);
                                                eventKeyFollowUp();
                                            }
                                        });
                                    });
                                    $(this).dialog("close");
                                }
                            },
                            "<?php echo ACTION_CLOSE; ?>": function () {
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            });
        });
        // Modify Follow up

        $(".btnEditFollowUpIPD").click(function () {
            $("#dialog9").html('');
            $("#PatientFollowupIPDAddForm").remove();
            var type = $(this).attr('type');
            var followUpId = $(this).attr('rel');
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
                                                if (type == "followup") {
                                                    $("#tbFollowUpPntIPDResult").html('<p id="loading" style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                                                }
                                            },
                                            success: function (msg) {
                                                if (type == "followup") {
                                                    $("#tbFollowUpPntIPDResult").html(msg);
                                                    eventKeyFollowUp();
                                                } else {
                                                    $("#btnTabConsultNum").click();
                                                }
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
        });
    }

    function eventKeyDoctorComment() {
        // Modify Follow up
        $(".btnEditDoctorCommentIPD").click(function () {
            $("#dialog9").html('');
            $("#DoctorCommentIPDAddForm").remove();
            var type = $(this).attr('type');
            var doctorCommentId = $(this).attr('rel');
            var patientConsultationId = $("#consultationId").val();
            var queuedDoctorId = $("#queueDoctorId").val();
            var queueId = $("#queueId").val();
            $.ajax({
                type: "GET",
                url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/doctorComment/" + doctorCommentId + "/" + type,
                data: "",
                beforeSend: function () {
                    $("#dialog9").html('<p id="loading" style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                },
                success: function (msg) {
                    $("#loading").hide();
                    $("#dialog9").html(msg);
                    $("#dialog9").dialog({
                        title: '<?php echo MENU_REMARK_LABEL_ADD; ?>',
                        resizable: false,
                        modal: true,
                        width: '70%',
                        height: 500,
                        position: 'center',
                        buttons: {
                            "<?php echo ACTION_EDIT; ?>": function () {
                                var isFormValidated = $("#DoctorCommentIPDAddForm").validationEngine('validate');
                                if (!isFormValidated) {
                                    return false;
                                } else {
                                    var url = "<?php echo $this->base . '/' . $this->params['controller']; ?>/addNewDoctorComment/" + patientConsultationId + '/' + queuedDoctorId + '/' + queueId;
                                    var post = $('#DoctorCommentIPDAddForm').serialize();
                                    $.post(url, post, function (rs) {
                                        $.ajax({
                                            type: "POST",
                                            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/getDoctorComment/" + patientConsultationId + '/' + queuedDoctorId,
                                            data: "",
                                            beforeSend: function () {
                                                if (type == "doctor_comment") {
                                                    $("#tbDoctorCommentPntIPDResult").html('<p id="loading" style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                                                }
                                            },
                                            success: function (msg) {
                                                if (type == "doctor_comment") {
                                                    $("#tbDoctorCommentPntIPDResult").html(msg);
                                                    eventKeyDoctorComment();
                                                } else {
                                                    $("#btnTabConsultNum").click();
                                                }
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
        });
    }

    function eventKeyDoctorDaignostic() {
        // Modify Follow up
        $(".btnEditDoctorDaignosticIPD").click(function () {
            $("#dialog9").html('');
            $("#DoctorDaignosticIPDAddForm").remove();
            var type = $(this).attr('type');
            var doctorDaignosticId = $(this).attr('rel');
            var patientConsultationId = $("#consultationId").val();
            var queuedDoctorId = $("#queueDoctorId").val();
            var queueId = $("#queueId").val();
            $.ajax({
                type: "GET",
                url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/doctorDaignostic/" + doctorDaignosticId + "/" + type,
                data: "",
                beforeSend: function () {
                    $("#dialog9").html('<p id="loading" style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                },
                success: function (msg) {
                    $("#loading").hide();
                    $("#dialog9").html(msg);
                    $("#dialog9").dialog({
                        title: '<?php echo MENU_DAIGNOSTIC_LABEL_ADD; ?>',
                        resizable: false,
                        modal: true,
                        width: '70%',
                        height: 500,
                        position: 'center',
                        buttons: {
                            "<?php echo ACTION_EDIT; ?>": function () {
                                var isFormValidated = $("#DoctorDaignosticIPDAddForm").validationEngine('validate');
                                if (!isFormValidated) {
                                    return false;
                                } else {
                                    var url = "<?php echo $this->base . '/' . $this->params['controller']; ?>/addNewDoctorDaignostic/" + patientConsultationId + '/' + queuedDoctorId + '/' + queueId;
                                    var post = $('#DoctorDaignosticIPDAddForm').serialize();
                                    $.post(url, post, function (rs) {
                                        $.ajax({
                                            type: "POST",
                                            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/getDoctorDaignostic/" + patientConsultationId + '/' + queuedDoctorId,
                                            data: "",
                                            beforeSend: function () {
                                                if (type == "doctor_daignostic") {
                                                    $("#tbDoctorDaignosticPntIPDResult").html('<p id="loading" style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                                                }
                                            },
                                            success: function (msg) {
                                                if (type == "doctor_daignostic") {
                                                    $("#tbDoctorDaignosticPntIPDResult").html(msg);
                                                    eventKeyDoctorDaignostic();
                                                } else {
                                                    $("#btnTabConsultNum").click();
                                                }
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
        });
    }


    function eventKeyMedicalHistory() {
        $(".btnEditMedicalHistory").click(function () {
            var type = $(this).attr('type');
            var medicalHisotryId = $(this).attr('rel');
            var patientConsultationId = $("#consultationId").val();
            var queuedDoctorId = $("#queueDoctorId").val();
            var queueId = $("#queueId").val();
            $.ajax({
                type: "GET",
                url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/medicalHistory/" + medicalHisotryId + "/" + type,
                data: "",
                beforeSend: function () {
                    $("#dialog9").html('<p id="loading" style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                },
                success: function (msg) {
                    $("#loading").hide();
                    $("#dialog9").html(msg);
                    $("#dialog9").dialog({
                        title: '<?php echo MENU_MEDICAL_HISTORY_ADD; ?>',
                        resizable: false,
                        modal: true,
                        width: '70%',
                        height: 500,
                        position: 'center',
                        buttons: {
                            "<?php echo ACTION_EDIT; ?>": function () {
                                var url = "<?php echo $this->base . '/' . $this->params['controller']; ?>/addNewMedicalHistory/" + patientConsultationId + '/' + queuedDoctorId + '/' + queueId;
                                var post = $('#DoctorMedicalHistoryIPDAddForm').serialize();
                                $.post(url, post, function (rs) {
                                    $.ajax({
                                        type: "POST",
                                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/getMedicalHistory/" + patientConsultationId + '/' + queuedDoctorId,
                                        data: "",
                                        beforeSend: function () {
                                            if (type == "medical_history") {
                                                $("#tbDoctorMedicalHistory").html('<p id="loading" style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                                            }
                                        },
                                        success: function (msg) {
                                            if (type == "medical_history") {
                                                $("#tbDoctorMedicalHistory").html(msg);
                                                eventKeyMedicalHistory();
                                            } else {
                                                $("#btnTabConsultNum").click();
                                            }
                                        }
                                    });
                                });
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            });
        });
    }


    function eventKeyDoctorChiefComplain() {
        $(".btnEditChiefComplain").click(function () {
            var type = $(this).attr('type');
            var chiefCompalinId = $(this).attr('rel');
            var patientConsultationId = $("#consultationId").val();
            var queuedDoctorId = $("#queueDoctorId").val();
            var queueId = $("#queueId").val();
            $.ajax({
                type: "GET",
                url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/chiefComplain/" + chiefCompalinId + "/" + type,
                data: "",
                beforeSend: function () {
                    $("#dialog9").html('<p id="loading" style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                },
                success: function (msg) {
                    $("#loading").hide();
                    $("#dialog9").html(msg);
                    $("#dialog9").dialog({
                        title: '<?php echo MENU_DAIGNOSTIC_LABEL_ADD; ?>',
                        resizable: false,
                        modal: true,
                        width: '70%',
                        height: 500,
                        position: 'center',
                        buttons: {
                            "<?php echo ACTION_EDIT; ?>": function () {
                                var url = "<?php echo $this->base . '/' . $this->params['controller']; ?>/addNewChiefComplain/" + patientConsultationId + '/' + queuedDoctorId + '/' + queueId;
                                var post = $('#DoctorChiefComplainIPDAddForm').serialize();
                                $.post(url, post, function (rs) {
                                    $.ajax({
                                        type: "POST",
                                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/getChiefComplain/" + patientConsultationId + '/' + queuedDoctorId,
                                        data: "",
                                        beforeSend: function () {
                                            if (type == "chief_complain") {
                                                $("#tbDoctorChiefComplain").html('<p id="loading" style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                                            }
                                        },
                                        success: function (msg) {
                                            if (type == "chief_complain") {
                                                $("#tbDoctorChiefComplain").html(msg);
                                                eventKeyDoctorChiefComplain();
                                            } else {
                                                $("#btnTabConsultNum").click();
                                            }
                                        }
                                    });
                                });
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            });
        });
    }

    $(document).ready(function () {
        $(".legend_content").show();
        $(".legend_title").click(function () {
            $(this).siblings(".legend_content").slideToggle();
        });
        $("#divPatientIPD").accordion({
            collapsible: true,
            autoHeight: false,
            navigation: true,
            active: false
        });
        $(".followup").accordion({
            collapsible: true,
            autoHeight: false,
            navigation: true,
            active: false
        });
        // reload function modify followup
        eventKeyFollowUp();
        // reload function modify doctor comment
        eventKeyDoctorComment();
        // reload function modify doctor daignostic
        eventKeyDoctorDaignostic();

        eventKeyDoctorChiefComplain();

        eventKeyMedicalHistory();

        $(".btnIPDVitalSign").click(function () {
            showVitalSign($(this).attr('patientIPDId'));
        });

        $(".vitalSignPntIPDHistory").dblclick(function (event) {
            var resultVitalSign = $(this).html();
            if (resultVitalSign != "") {
                $("#dialog9").html(resultVitalSign);
                $("#dialog9").dialog({
                    title: '<?php echo MENU_VITAL_SING_INFO; ?>',
                    resizable: false,
                    modal: true,
                    width: '50%',
                    height: 650,
                    position: 'center',
                    buttons: {
                        Close: function () {
                            $(this).dialog("close");
                        }
                    }
                });
                // reload function modify followup
                eventKeyFollowUp();
            }
        });


        /**
         * This script use for add new follow up using ajax to patient history by patient_history_id
         */
        $(".btnIPDFollowUp").click(function () {
            $("#dialog9").html('');
            $("#PatientFollowupIPDAddForm").remove();
            var patientConsultationId = this.name;
            var queueId = $(this).attr('title');
            var queuedDoctorId = $(this).attr('queuedDoctorId');
            $.ajax({
                type: "GET",
                url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/followup/",
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
                            "<?php echo ACTION_SAVE; ?>": function () {
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
                                                $("#tbFollowUpPntIPDResult").html('<p id="loading" style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                                            },
                                            success: function (msg) {
                                                $("#tbFollowUpPntIPDResult").html(msg);
                                                eventKeyFollowUp();
                                            }
                                        });
                                    });
                                    $(this).dialog("close");
                                }
                            },
                            "<?php echo ACTION_CLOSE; ?>": function () {
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            });
        });
        $(".followUpPntIPDHistory").dblclick(function (event) {
            var resultFollowUp = $(this).html();
            if (resultFollowUp != "") {
                $("#dialog9").html(resultFollowUp);
                $("#dialog9").dialog({
                    title: '<?php echo MENU_FOLLOW_UP; ?>',
                    resizable: false,
                    modal: true,
                    width: '50%',
                    height: 650,
                    position: 'center',
                    buttons: {
                        Close: function () {
                            $(this).dialog("close");
                        }
                    }
                });
                // reload function modify followup
                eventKeyFollowUp();
            }
        });




        /**
         * This script use for add new doctor comment using ajax to patient history by patient_history_id
         */
        $(".btnIPDDoctorComment").click(function () {
            $("#dialog9").html('');
            $("#DoctorCommentIPDAddForm").remove();
            var patientConsultationId = this.name;
            var queueId = $(this).attr('title');
            var queuedDoctorId = $(this).attr('queuedDoctorId');
            $.ajax({
                type: "GET",
                url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/doctorComment/",
                data: "",
                beforeSend: function () {
                    $("#dialog9").html('<p id="loading" style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                },
                success: function (msg) {
                    $("#loading").hide();
                    $("#dialog9").html(msg);
                    $("#dialog9").dialog({
                        title: '<?php echo MENU_REMARK_LABEL_ADD; ?>',
                        resizable: false,
                        modal: true,
                        width: '70%',
                        height: 500,
                        position: 'center',
                        buttons: {
                            "<?php echo ACTION_SAVE; ?>": function () {
                                var isFormValidated = $("#DoctorCommentIPDAddForm").validationEngine('validate');
                                if (!isFormValidated) {
                                    return false;
                                } else {
                                    var url = "<?php echo $this->base . '/' . $this->params['controller']; ?>/addNewDoctorComment/" + patientConsultationId + '/' + queuedDoctorId + '/' + queueId;
                                    var post = $('#DoctorCommentIPDAddForm').serialize();
                                    $.post(url, post, function (rs) {
                                        $.ajax({
                                            type: "POST",
                                            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/getDoctorComment/" + patientConsultationId + '/' + queuedDoctorId,
                                            data: "",
                                            beforeSend: function () {
                                                $("#tbDoctorCommentPntIPDResult").html('<p id="loading" style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                                            },
                                            success: function (msg) {
                                                $("#tbDoctorCommentPntIPDResult").html(msg);
                                                eventKeyDoctorComment();
                                            }
                                        });
                                    });
                                    $(this).dialog("close");
                                }
                            },
                            "<?php echo ACTION_CLOSE; ?>": function () {
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            });
        });
        $(".doctorCommentPntIPDHistory").dblclick(function (event) {
            var resultDocCommentIPDHistory = $(this).html();
            if (resultDocCommentIPDHistory != "") {
                $("#dialog9").html(resultDocCommentIPDHistory);
                $("#dialog9").dialog({
                    title: "<?php echo MENU_REMARKS; ?>",
                    resizable: false,
                    modal: true,
                    width: '50%',
                    height: 650,
                    position: 'center',
                    buttons: {
                        Close: function () {
                            $(this).dialog("close");
                        }
                    }
                });
                // reload function doctor comment
                eventKeyDoctorComment();
            }
        });


        $(".btnIPDMedicalHistory").click(function () {
            $("#dialog9").html('');
            $("#DoctorMedicalHistoryIPDAddForm").remove();
            var patientConsultationId = this.name;
            var queueId = $(this).attr('title');
            var queuedDoctorId = $(this).attr('queuedDoctorId');
            $.ajax({
                type: "GET",
                url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/medicalHistory/",
                data: "",
                beforeSend: function () {
                    $("#dialog9").html('<p id="loading" style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                },
                success: function (msg) {
                    $("#loading").hide();
                    $("#dialog9").html(msg);
                    $("#dialog9").dialog({
                        title: '<?php echo 'Add New Medical Hisotry'; ?>',
                        resizable: false,
                        modal: true,
                        width: '70%',
                        height: 500,
                        position: 'center',
                        buttons: {
                            "<?php echo ACTION_SAVE; ?>": function () {
                                var url = "<?php echo $this->base . '/' . $this->params['controller']; ?>/addNewMedicalHistory/" + patientConsultationId + '/' + queuedDoctorId + '/' + queueId;
                                var post = $('#DoctorMedicalHistoryIPDAddForm').serialize();
                                $.post(url, post, function (rs) {
                                    $.ajax({
                                        type: "POST",
                                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/getMedicalHistory/" + patientConsultationId + '/' + queuedDoctorId,
                                        data: "",
                                        beforeSend: function () {
                                            $("#tbDoctorMedicalHistory").html('<p id="loading" style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                                        },
                                        success: function (msg) {
                                            $("#tbDoctorMedicalHistory").html(msg);
                                            eventKeyMedicalHistory();
                                        }
                                    });
                                });
                                $(this).dialog("close");
                            },
                            "<?php echo ACTION_CLOSE; ?>": function () {
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            });
        });

        $(".btnIPDChiefComplain").click(function () {
            $("#dialog9").html('');
            $("#DoctorChiefComplainIPDAddForm").remove();
            var patientConsultationId = this.name;
            var queueId = $(this).attr('title');
            var queuedDoctorId = $(this).attr('queuedDoctorId');
            $.ajax({
                type: "GET",
                url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/chiefComplain/",
                data: "",
                beforeSend: function () {
                    $("#dialog9").html('<p id="loading" style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                },
                success: function (msg) {
                    $("#loading").hide();
                    $("#dialog9").html(msg);
                    $("#dialog9").dialog({
                        title: '<?php echo 'Add New Chief Complain'; ?>',
                        resizable: false,
                        modal: true,
                        width: '70%',
                        height: 500,
                        position: 'center',
                        buttons: {
                            "<?php echo ACTION_SAVE; ?>": function () {
                                var url = "<?php echo $this->base . '/' . $this->params['controller']; ?>/addNewChiefComplain/" + patientConsultationId + '/' + queuedDoctorId + '/' + queueId;
                                var post = $('#DoctorChiefComplainIPDAddForm').serialize();
                                $.post(url, post, function (rs) {
                                    $.ajax({
                                        type: "POST",
                                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/getChiefComplain/" + patientConsultationId + '/' + queuedDoctorId,
                                        data: "",
                                        beforeSend: function () {
                                            $("#tbDoctorChiefComplain").html('<p id="loading" style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                                        },
                                        success: function (msg) {
                                            $("#tbDoctorChiefComplain").html(msg);
                                            eventKeyDoctorChiefComplain();
                                        }
                                    });
                                });
                                $(this).dialog("close");
                            },
                            "<?php echo ACTION_CLOSE; ?>": function () {
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            });
        });
        /**
         * This script use for add new doctor daignostic using ajax to patient history by patient_history_id
         */
        $(".btnIPDDoctorDaignostic").click(function () {
            $("#dialog9").html('');
            $("#DoctorDaignosticIPDAddForm").remove();
            var patientConsultationId = this.name;
            var queueId = $(this).attr('title');
            var queuedDoctorId = $(this).attr('queuedDoctorId');
            $.ajax({
                type: "GET",
                url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/doctorDaignostic/",
                data: "",
                beforeSend: function () {
                    $("#dialog9").html('<p id="loading" style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                },
                success: function (msg) {
                    $("#loading").hide();
                    $("#dialog9").html(msg);
                    $("#dialog9").dialog({
                        title: '<?php echo MENU_DAIGNOSTIC_LABEL_ADD; ?>',
                        resizable: false,
                        modal: true,
                        width: '70%',
                        height: 500,
                        position: 'center',
                        buttons: {
                            "<?php echo ACTION_SAVE; ?>": function () {
                                var isFormValidated = $("#DoctorDaignosticIPDAddForm").validationEngine('validate');
                                if (!isFormValidated) {
                                    return false;
                                } else {
                                    var url = "<?php echo $this->base . '/' . $this->params['controller']; ?>/addNewDoctorDaignostic/" + patientConsultationId + '/' + queuedDoctorId + '/' + queueId;
                                    var post = $('#DoctorDaignosticIPDAddForm').serialize();
                                    $.post(url, post, function (rs) {
                                        $.ajax({
                                            type: "POST",
                                            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/getDoctorDaignostic/" + patientConsultationId + '/' + queuedDoctorId,
                                            data: "",
                                            beforeSend: function () {
                                                $("#tbDoctorDaignosticPntIPDResult").html('<p id="loading" style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                                            },
                                            success: function (msg) {
                                                $("#tbDoctorDaignosticPntIPDResult").html(msg);
                                                eventKeyDoctorDaignostic();
                                            }
                                        });
                                    });
                                    $(this).dialog("close");
                                }
                            },
                            "<?php echo ACTION_CLOSE; ?>": function () {
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            });
        });
        $(".doctorDaignosticPntIPDHistory").dblclick(function (event) {
            var resultDocDaignosticIPDHistory = $(this).html();
            if (resultDocDaignosticIPDHistory != "") {
                $("#dialog9").html(resultDocDaignosticIPDHistory);
                $("#dialog9").dialog({
                    title: '<?php echo TABLE_DAIGNOSTIC; ?>',
                    resizable: false,
                    modal: true,
                    width: '50%',
                    height: 650,
                    position: 'center',
                    buttons: {
                        Close: function () {
                            $(this).dialog("close");
                        }
                    }
                });
                // reload function doctor daignostic
                eventKeyDoctorDaignostic();
            }
        });
        $(".prescriptionIPDHistory").dblclick(function (event) {
            var resultPrescriptionIPDHistory = $(this).html();
            if (resultPrescriptionIPDHistory != "") {
                $("#dialog9").html(resultPrescriptionIPDHistory);
                $("#dialog9").dialog({
                    title: '<?php echo MENU_PRESCRIPTION; ?>',
                    resizable: false,
                    modal: true,
                    width: '50%',
                    height: 650,
                    position: 'center',
                    buttons: {
                        Close: function () {
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
        $(".vaccineIPDHistory").dblclick(function (event) {
            var resultVaccineIPDHistory = $(this).html();
            if (resultVaccineIPDHistory != "") {
                $("#dialog9").html(resultVaccineIPDHistory);
                $("#dialog9").dialog({
                    title: '<?php echo MENU_PRESCRIPTION; ?>',
                    resizable: false,
                    modal: true,
                    width: '50%',
                    height: 650,
                    position: 'center',
                    buttons: {
                        Close: function () {
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
        $(".viewLaboResult").dblclick(function (event) {

            var resultLaboIPDHistory = $(this).html();
            if (resultLaboIPDHistory != "") {
                $("#dialog9").html(resultLaboIPDHistory);
                $("#dialog9").dialog({
                    title: '<?php echo MENU_LABO_MANAGEMENT; ?>',
                    resizable: false,
                    modal: true,
                    width: '50%',
                    height: 650,
                    position: 'center',
                    buttons: {
                        Close: function () {
                            $(this).dialog("close");
                        }
                    }
                });
            }
//            event.preventDefault();
//            event.stopPropagation(); 
//            var id = $(this).attr('rel');
//            var name = $(this).attr('title');
//            if(id!=""){
//                $.ajax({
//                    type: "GET",
//                    url: "<?php echo $absolute_url; ?>/doctors/viewLaboResult/" + id,
//                    data: "",
//                    beforeSend: function(){
//                        $("#dialog9").html('<p style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
//                    },
//                    success: function(msg){
//                        $("#dialog9").html(msg);
//                    }
//                });
//                $("#dialog9").dialog({
//                    title: name + ' Information',
//                    resizable: false,
//                    modal: true,
//                    width: '90%',
//                    height: 500,
//                    position:'center',
//                    buttons: {
//                        Close: function() {
//                            $( this ).dialog( "close" );
//                        }
//                    }
//                });
//            } 
        });
    });

    function showVitalSign(patientIPDId) {
        $("#dialog9").html('');
        $("#PatientIpdVitalSignAddForm").remove();
        $.ajax({
            type: "GET",
            url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/vitalSign/" + patientIPDId,
            data: "",
            beforeSend: function () {

            },
            success: function (msg) {
                $("#loading").hide();
                $("#dialog9").html(msg);
                $("#dialog9").dialog({
                    title: '<?php echo MENU_VITAL_SING_INFO; ?>',
                    resizable: false,
                    modal: true,
                    width: '70%',
                    height: 500,
                    position: 'center',
                    buttons: {
                        "<?php echo ACTION_SAVE; ?>": function () {
                            var isFormValidated = $("#PatientIpdVitalSignAddForm").validationEngine('validate');
                            if (!isFormValidated) {
                                return false;
                            } else {
                                var url = "<?php echo $this->base . '/' . $this->params['controller']; ?>/addVitalSign/" + patientIPDId;
                                var post = $('#PatientIpdVitalSignAddForm').serialize();
                                $.post(url, post, function (rs) {
                                    $.ajax({
                                        type: "POST",
                                        url: "<?php echo $this->base . '/' . $this->params['controller']; ?>/getVitalSign/" + patientIPDId,
                                        data: "",
                                        beforeSend: function () {
                                            $("#tbVitalSignPntIPDResult").html('<p id="loading" style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                                        },
                                        success: function (msg) {
                                            $("#tbVitalSignPntIPDResult").html(msg);
                                            eventKeyFollowUp();
                                        }
                                    });
                                });
                                $(this).dialog("close");
                            }
                        },
                        "<?php echo ACTION_CLOSE; ?>": function () {
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });

    }
</script>
<?php
$resultImmuzation = array();
$queryPrscription = mysql_query("SELECT * FROM orders WHERE orders.status > 0 AND orders.patient_id = {$patientId}");
if (mysql_num_rows($queryPrscription)) {
    while ($resultPrescription = mysql_fetch_array($queryPrscription)) {
        $queryPrscriptionDetail = mysql_query("SELECT order_details.order_id FROM order_details "
                . "INNER JOIN products ON products.id = order_details.product_id "
                . "INNER JOIN product_pgroups ON product_pgroups.product_id = products.id "
                . "INNER JOIN uoms ON uoms.id = order_details.qty_uom_id "
                . "WHERE order_details.order_id = {$resultPrescription['id']} AND pgroup_id = 6 GROUP BY order_details.order_id");
        while ($orderDetail = mysql_fetch_array($queryPrscriptionDetail)) {
            $resultImmuzation[] = $orderDetail['order_id'];
        }
    }
}
?>
<div id="divPatientIPD">
    <?php
    $ind = 1;
    $date1 = date("Y-m-d");
    foreach ($consultation as $consultation):
        $display = "";
        $disabled = "";
        $date2 = date('Y-m-d', strtotime($consultation['PatientConsultation']['created']));
        if (strtotime($date1) >= strtotime($date2)) {
            $display = "display:none;";
            $disabled = "disabled";
        }
        ?>    
        <h3>
            <a href="#">
                <?php echo "# : "; ?>
                <?php echo $consultation['PatientConsultation']['consultation_code'] . ' - ' . date('d/m/Y H:i:s', strtotime($consultation['PatientConsultation']['created'])); ?>                
            </a>
        </h3>    
        <div class="<?php echo $consultation['PatientConsultation']['id']; ?>">
            <?php echo $this->Form->create('PatientConsultation', array('id' => 'PatientConsultationEditForm' . $consultation['PatientConsultation']['id'], 'class' => 'PatientConsultationEditForm', 'rel' => $consultation['PatientConsultation']['id'], 'url' => '/doctors/editConsult/' . $consultation['PatientConsultation']['id'] . '/' . $consultation['PatientConsultation']['queued_doctor_id'] . '/' . $consultation['Queue']['id'], 'enctype' => 'multipart/form-data')); ?>
            <input type="hidden" type="text" id="patient_id" name="patient_id" value="<?php echo $consultation['Patient']['id']; ?>" />
            <input name="data[PatientConsultation][consultation_code]" type="hidden" value="<?php echo $consultation['PatientConsultation']['consultation_code']; ?>"/>
            <input name="data[PatientConsultation][id]" type="hidden" id="consultationId" value="<?php echo $consultation['PatientConsultation']['id']; ?>"/>
            <input name="data[QeuedDoctor][id]" type="hidden" id="queueDoctorId" value="<?php echo $consultation['QeuedDoctor']['id']; ?>"/>
            <input name="data[Queue][id]" type="hidden" id="queueId" value="<?php echo $consultation['Queue']['id']; ?>"/>
            <input id="link_url" type="hidden" value="<?php echo $absolute_url . $this->params['controller']; ?>"/>                        
            <div style="width: 100%;">
                <!-- Vital Sign  -->
                <div class="legend" style="width: 32.5%; float: left; padding: 2px;">
                    <div class="legend_title"><label><b><?php echo MENU_PATIENT_IPD; ?></b></label></div>
                    <div class="legend_content" style="height: 215px;">
                        <table style="width: 100%">
                            <tr>
                                <td style="width: 30%;"><label for="PatientConsultationRoomId"><?php echo TABLE_ROOM_NUMBER; ?> :</label></td>
                                <td>
                                    <input type="hidden" name="data[PatientIpd][id]" value="<?php echo $patientIpd['PatientIpd']['id']; ?>"/>
                                    <?php
                                    $roomDisabled = "";
                                    if ($disabled != "") {
                                        $roomDisabled = "disabled='disabled'";
                                    }
                                    ?>
                                    <select <?php echo $disabled; ?> id="PatientConsultationRoomId" name="data[PatientIpd][room_id]" class="classRoom validate[required]">
                                        <option value=""><?php echo SELECT_OPTION; ?></option>
                                        <?php
                                        foreach ($rooms as $room) {
                                            if ($room['Room']['id'] == $patientIpd['Room']['id']) {
                                                echo '<option selected="selected" value="' . $room['Room']['id'] . '">' . $room['Room']['room_name'] . '-' . $room['RoomType']['name'] . '</option>';
                                            } else {
                                                echo '<option value="' . $room['Room']['id'] . '">' . $room['Room']['room_name'] . '-' . $room['RoomType']['name'] . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>  
                                </td>
                            </tr>
                            <tr>
                                <td><label for="PatientConsultationAllergies"><?php echo TABLE_ALLERGIC; ?> :</label></td>
                                <td><?php echo $this->Form->textarea('allergies', array('name' => 'data[PatientIpd][allergies]', 'disabled' => $disabled, 'style' => 'width: 97% !important; height: 70px ! important;', 'value' => $patientIpd['PatientIpd']['allergies'])); ?></td>
                            </tr>
                        </table>        
                    </div>  
                </div>
                <!-- Chief Complain -->

                <div class="legend" style="width: 32.5%; float: left; padding: 2px;">
                    <div class="legend_title"><label for="PatientConsultationComplain"><b><?php echo TABLE_CHIEF_COMPLAIN; ?></b></label></div>
                    <div class="legend_content" style="height: 215px; overflow-y: scroll;">    
                        <table class="table" width="100%" style="border:none;">
                            <tbody id="tbDoctorChiefComplain">
                                <?php
                                $index = 1;
                                $query_doctor_chief_complains = mysql_query("SELECT * FROM doctor_chief_complains WHERE status=1 AND patient_consultation_id=" . $consultation['PatientConsultation']['id'] . " ORDER BY created ASC");
                                while ($data_doctor_chief_complains = mysql_fetch_array($query_doctor_chief_complains)) {
                                    ?>
                                    <tr>
                                        <td style="border: none; font-weight: bold; background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo date('d/m/Y H:i:s', strtotime($data_doctor_chief_complains['created'])); ?></td>
                                        <td style="border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo getDoctor($data_doctor_chief_complains['created_by']); ?></td>
                                        <td style="width: 5%; border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);">
                                            <?php
                                            if ($data_doctor_chief_complains['created_by'] == $user['User']['id']) {
                                                echo '<a href="#" class="btnEditChiefComplain" type="chief_complain" rel="' . $data_doctor_chief_complains['id'] . '" ><img alt="Edit" onmouseover="Tip(\'' . ACTION_EDIT . '\')" src="' . $this->webroot . 'img/action/edit.png" /></a>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border: none; padding-left: 5%; color: rgb(0, 0, 0);" colspan="3">
                                            <?php echo nl2br($data_doctor_chief_complains['chief_complain']); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tr>
                                <td style="border: none; font-weight: bold; background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo date('d/m/Y H:i:s', strtotime($consultation['PatientConsultation']['created'])); ?></td>
                                <td style="border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo getDoctor($consultation['PatientConsultation']['created_by']); ?></td>
                                <td style="width: 5%; border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);">
                                    <?php
                                    if ($consultation['PatientConsultation']['created_by'] == $user['User']['id'] || $allowAdmin) {
                                        echo '<a href="#" class="btnEditChiefComplain" type="consult" rel="' . $consultation['PatientConsultation']['id'] . '" ><img alt="Edit" onmouseover="Tip(\'' . ACTION_EDIT . '\')" src="' . $this->webroot . 'img/action/edit.png" /></a>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="border: none; padding-left: 5%; color: rgb(0, 0, 0);" colspan="3"><?php echo nl2br($consultation['PatientConsultation']['chief_complain']); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="legend_content">
                        <div class="buttons">
                            <a style="margin-left: 2px;" href="#" queuedDoctorId="<?php echo $consultation['PatientConsultation']['queued_doctor_id']; ?>" name="<?php echo $consultation['PatientConsultation']['id']; ?>" title="<?php echo $consultation['Queue']['id']; ?>" class="positive btnIPDChiefComplain" >
                                <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
                                <?php echo MENU_CHIEF_COMPLAIN_ADD; ?>
                            </a>
                        </div>
                        <div style="clear: both;"></div>
                    </div>
                </div>       
                <!-- Medical History -->
                <div class="legend" style="width: 32.5%; float: left; padding: 2px;">
                    <div class="legend_title"><label for="PatientConsultationMedicalHistory"><b><?php echo MENU_MEDICAL_HISTORY; ?></b></label></div>
                    <div class="legend_content" style="height: 215px; overflow-y: scroll;">    
                        <table class="table" width="100%" style="border:none;">
                            <tbody id="tbDoctorMedicalHistory">
                                <?php
                                $index = 1;
                                $query_doctor_medical_histories = mysql_query("SELECT * FROM doctor_medical_histories WHERE is_active=1 AND patient_consultation_id=" . $consultation['PatientConsultation']['id'] . " ORDER BY created ASC");
                                while ($data_doctor_medical_histories = mysql_fetch_array($query_doctor_medical_histories)) {
                                    ?>
                                    <tr>
                                        <td style="border: none; font-weight: bold; background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo date('d/m/Y H:i:s', strtotime($data_doctor_medical_histories['created'])); ?></td>
                                        <td style="border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo getDoctor($data_doctor_medical_histories['created_by']); ?></td>
                                        <td style="width: 5%; border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);">
                                            <?php
                                            if ($data_doctor_medical_histories['created_by'] == $user['User']['id']) {
                                                echo '<a href="#" class="btnEditMedicalHistory" type="medical_history" rel="' . $data_doctor_medical_histories['id'] . '" ><img alt="Edit" onmouseover="Tip(\'' . ACTION_EDIT . '\')" src="' . $this->webroot . 'img/action/edit.png" /></a>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border: none; padding-left: 5%; color: rgb(0, 0, 0);" colspan="3">
                                            <?php echo nl2br($data_doctor_medical_histories['medical_history']); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tr>
                                <td style="border: none; font-weight: bold; background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo date('d/m/Y H:i:s', strtotime($consultation['PatientConsultation']['created'])); ?></td>
                                <td style="border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo getDoctor($consultation['PatientConsultation']['created_by']); ?></td>
                                <td style="width: 5%; border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);">
                                    <?php
                                    if ($consultation['PatientConsultation']['created_by'] == $user['User']['id'] || $allowAdmin) {
                                        echo '<a href="#" class="btnEditMedicalHistory" type="consult" rel="' . $consultation['PatientConsultation']['id'] . '" ><img alt="Edit" onmouseover="Tip(\'' . ACTION_EDIT . '\')" src="' . $this->webroot . 'img/action/edit.png" /></a>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="border: none; padding-left: 5%; color: rgb(0, 0, 0);" colspan="3"><?php echo nl2br($consultation['PatientConsultation']['medical_history']); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="legend_content">
                        <div class="buttons">
                            <a style="margin-left: 2px;" href="#" queuedDoctorId="<?php echo $consultation['PatientConsultation']['queued_doctor_id']; ?>" name="<?php echo $consultation['PatientConsultation']['id']; ?>" title="<?php echo $consultation['Queue']['id']; ?>" class="positive btnIPDMedicalHistory" >
                                <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
                                <?php echo MENU_MEDICAL_HISTORY_ADD; ?>
                            </a>
                        </div>
                        <div style="clear: both;"></div>
                    </div>
                </div>
                <div class="clear"></div>
                <div style="width: 100%;">
                    <!-- Physical Examination -->
                    <div class="legend" style="width: 32.5%; float: left; padding: 2px;">
                        <div class="legend_title"><label for="PatientConsultationDateFirstComplaint"><b><?php echo PHYSICAL_EXAMINATION; ?></b></label></div>
                        <div class="legend_content" style="height: 150px; overflow-y: scroll;">
                            <div style="display: none;"><?php echo $this->Form->input('examination', array('empty' => SELECT_OPTION, 'label' => false, 'style' => 'width: 200px;', 'class' => 'validate[require]', 'selected' => $consultation['PatientConsultation']['physical_examination_id'])); ?> </div>
                            <?php echo nl2br($consultation['PatientConsultation']['physical_examination']); ?>                      
                        </div>
                    </div>   
                    <!-- Laboratory -->
                    <div class="legend" style="width: 32.5%; float: left; padding: 2px;">
                        <div class="legend_title"><label for="PatientConsultationPrescription"><b><?php echo MENU_LABO_MANAGEMENT; ?></b></label></div>
                        <div class="legend_content viewLaboResult" rel="<?php echo $consultation['QeuedLabo']['id']; ?>" title="<?php echo MENU_LABO_MANAGEMENT; ?>" style="height: 150px; overflow-y: scroll; cursor: pointer;">
                            <table class="table" width="100%" style="border:none;">
                                <?php
                                $resultQueue = array();
                                $grouped = array();
                                if (!empty($consultation['Queue']["id"])) {
                                    array_push($resultQueue, $consultation['Queue']["id"]);
                                }
                                if (!empty($resultQueue)) {
                                    array_multisort($resultQueue);
                                    $oldValue = "";
                                    foreach ($resultQueue as $value) {
                                        if ($value != $oldValue) {
                                            array_push($grouped, $value);
                                        }
                                        $oldValue = $value;
                                    }
                                    $queueId = implode(',', $grouped);
                                    $queryLabo = mysql_query("SELECT * FROM labos WHERE status > 0 AND queued_id IN (SELECT id FROM  queued_labos WHERE queue_id IN ({$queueId}))");
                                    while ($rowLabo = mysql_fetch_array($queryLabo)) {
                                        ?>
                                        <tr>
                                            <td style="border: none; font-weight: bold; background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo date('d/m/Y H:i:s', strtotime($rowLabo['created'])); ?></td>
                                            <td style="border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo getDoctor($rowLabo['created_by']); ?></td>
                                        </tr>
                                        <?php
                                        $queryLaboRequest = mysql_query("SELECT labg.name, labg.code FROM labos As la "
                                                . "INNER JOIN labo_requests As lar ON la.id = lar.labo_id "
                                                . "INNER JOIN  labo_item_groups As labg ON labg.id = lar.labo_item_group_id "
                                                . "WHERE lar.is_active = 1 AND la.status > 0 AND la.id = {$rowLabo['id']}");
                                        while ($rowLaboRequest = mysql_fetch_array($queryLaboRequest)) {
                                            ?>
                                            <tr>
                                                <td style="text-align: left;border: none;" colspan="2">
                                                    <input style="width: 20px;" type="checkbox" checked="checked" disabled="disabled" />&nbsp;&nbsp;&nbsp;
                                                    <label><?php echo $rowLaboRequest['name']; ?></label>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                }

                                if (!empty($consultation['QeuedLabo']['id'])) {
                                    
                                }
                                ?>
                            </table>
                        </div>                    
                    </div>
                    <!-- Diagnostic -->
                    <div class="legend" style="width: 32.5%; float: left; padding: 2px;">
                        <div class="legend_title"><label for="PatientConsultationDaignostic"><b><?php echo TABLE_DAIGNOSTIC; ?></b></label></div>  
                        <div class="legend_content doctorDaignosticPntIPDHistory" style="height: 120px; overflow-y: scroll; cursor: pointer;">
                            <table class="table" width="100%" style="border:none;">
                                <tbody id="tbDoctorDaignosticPntIPDResult">
                                    <?php
                                    $index = 1;
                                    $query_doctor_daignostic = mysql_query("SELECT * FROM doctor_daignostics WHERE is_active=1 AND patient_consultation_id=" . $consultation['PatientConsultation']['id'] . " ORDER BY created ASC");
                                    while ($data_doctor_daignostic = mysql_fetch_array($query_doctor_daignostic)) {
                                        ?>
                                        <tr>
                                            <td style="border: none; font-weight: bold; background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo date('d/m/Y H:i:s', strtotime($data_doctor_daignostic['created'])); ?></td>
                                            <td style="border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo getDoctor($data_doctor_daignostic['created_by']); ?></td>
                                            <td style="width: 5%; border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);">
                                                <?php
                                                if ($data_doctor_daignostic['created_by'] == $user['User']['id']) {
                                                    echo '<a href="#" class="btnEditDoctorDaignosticIPD" type="doctor_daignostic" rel="' . $data_doctor_daignostic['id'] . '" ><img alt="Edit" onmouseover="Tip(\'' . ACTION_EDIT . '\')" src="' . $this->webroot . 'img/action/edit.png" /></a>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="border: none; padding-left: 5%; color: rgb(0, 0, 0);" colspan="3">
                                                <?php echo nl2br($data_doctor_daignostic['doctor_daignostic']); ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                                <tr>
                                    <td style="border: none; font-weight: bold; background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo date('d/m/Y H:i:s', strtotime($consultation['PatientConsultation']['created'])); ?></td>
                                    <td style="border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo getDoctor($consultation['PatientConsultation']['created_by']); ?></td>
                                    <td style="width: 5%; border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);">
                                        <?php
                                        if ($consultation['PatientConsultation']['created_by'] == $user['User']['id'] || $allowAdmin) {
                                            echo '<a href="#" class="btnEditDoctorDaignosticIPD" type="consult" rel="' . $consultation['PatientConsultation']['id'] . '" ><img alt="Edit" onmouseover="Tip(\'' . ACTION_EDIT . '\')" src="' . $this->webroot . 'img/action/edit.png" /></a>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border: none; padding-left: 5%; color: rgb(0, 0, 0);" colspan="3"><?php echo nl2br($consultation['PatientConsultation']['daignostic']); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="legend_content">
                        <div class="buttons">
                            <a style="margin-left: 2px;" href="#" queuedDoctorId="<?php echo $consultation['PatientConsultation']['queued_doctor_id']; ?>" name="<?php echo $consultation['PatientConsultation']['id']; ?>" title="<?php echo $consultation['Queue']['id']; ?>" class="positive btnIPDDoctorDaignostic" >
                                <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
                                <?php echo MENU_DAIGNOSTIC_LABEL_ADD; ?>
                            </a>
                        </div>
                        <div style="clear: both;"></div>
                    </div>
                </div>
                <div class="clear"></div>
                <div style="width: 100%;">
                    <!-- Prescription -->
                    <div class="legend" style="width: 32.5%; float: left; padding: 2px;">
                        <div class="legend_title"><label for="PatientConsultationPrescription"><b><?php echo MENU_PRESCRIPTION; ?></b></label></div>
                        <div class="legend_content prescriptionIPDHistory" style="height: 120px; overflow-y: scroll; cursor: pointer;">
                            <table class="table" style="border: none;">
                                <?php
                                $conditionVaccine = "";
                                if (!empty($resultImmuzation)) {
                                    $resultVaccine = implode(',', $resultImmuzation);
                                    $conditionVaccine = "AND orders.id NOT IN ({$resultVaccine})";
                                }
                                $queryPrscription = mysql_query("SELECT * FROM orders WHERE orders.status > 0 AND orders.queue_doctor_id = {$consultation['QeuedDoctor']['id']} {$conditionVaccine}  ORDER BY created ASC");
                                if (mysql_num_rows($queryPrscription)) {
                                    while ($resultPrescription = mysql_fetch_array($queryPrscription)) {
                                        ?>                        
                                        <tr>
                                            <td style="border: none; font-weight: bold; background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo date('d/m/Y H:i:s', strtotime($resultPrescription['created'])); ?></td>
                                            <td style="border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo getDoctor($resultPrescription['created_by']); ?></td>
                                        </tr>
                                        <?php
                                        $index = 0;
                                        $queryPrscriptionDetail = mysql_query("SELECT *, products.name, products.code, uoms.abbr FROM order_details INNER JOIN products ON products.id = order_details.product_id INNER JOIN uoms ON uoms.id = order_details.qty_uom_id WHERE order_details.order_id = {$resultPrescription['id']}");
                                        while ($orderDetail = mysql_fetch_array($queryPrscriptionDetail)) {
                                            $productName = $orderDetail['name'];
                                            ?>
                                            <tr>
                                                <td style="border: none;" colspan="2">
                                                    <?php echo ++$index; ?> - 
                                                    <?php
                                                    echo $productName;
                                                    if (trim($orderDetail['note']) != "") {
                                                        echo '&nbsp;&nbsp;>&nbsp;&nbsp;' . $orderDetail['note'];
                                                    }
                                                    if ($orderDetail['qty'] != "") {
                                                        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $orderDetail['qty'];
                                                    }
                                                    if ($orderDetail['abbr'] != "") {
                                                        echo '&nbsp;&nbsp;&nbsp; ' . $orderDetail['abbr'];
                                                    }
                                                    if ($orderDetail['num_days'] != "") {
                                                        echo ',&nbsp;&nbsp;&nbsp;' . $orderDetail['num_days'];
                                                    }
                                                    $medicinUseMorning = "";
                                                    if ($orderDetail['morning_use_id'] != "") {
                                                        $queryMedicineUse = mysql_query("SELECT name FROM treatment_uses WHERE id = {$orderDetail['morning_use_id']}");
                                                        while ($resultMedicineUse = mysql_fetch_array($queryMedicineUse)) {
                                                            $medicinUseMorning = $resultMedicineUse['name'];
                                                        }
                                                    }
                                                    echo ',&nbsp;&nbsp;&nbsp;' . $medicinUseMorning;
                                                    ?>
                                                </td>
                                            </tr>                                        
                                            <?php
                                        }

                                        $queryPrscriptionMisc = mysql_query("SELECT order_miscs.*, uoms.abbr FROM order_miscs INNER JOIN uoms ON uoms.id =  order_miscs.qty_uom_id WHERE order_miscs.order_id = {$resultPrescription['id']}");
                                        while ($orderMisc = mysql_fetch_array($queryPrscriptionMisc)) {
                                            ?>
                                            <tr>
                                                <td style="border: none;" colspan="2">
                                                    <?php echo ++$index; ?> -  
                                                    <?php
                                                    echo $productName = $orderMisc['description'];
                                                    if (trim($orderMisc['note']) != "") {
                                                        echo '&nbsp;&nbsp;&nbsp;&nbsp;' . $orderMisc['note'];
                                                    }
                                                    if ($orderMisc['qty'] != "") {
                                                        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $orderMisc['qty'];
                                                    }
                                                    if ($orderMisc['num_days'] != "") {
                                                        echo ',&nbsp;&nbsp;&nbsp; ' . TABLE_NUM_DAYS . ': ' . $orderMisc['num_days'];
                                                    }
                                                    $medicinUseMorning = "";
                                                    if ($orderMisc['morning_use_id'] != "") {
                                                        $queryMedicineUse = mysql_query("SELECT name FROM treatment_uses WHERE id = {$orderMisc['morning_use_id']}");
                                                        while ($resultMedicineUse = mysql_fetch_array($queryMedicineUse)) {
                                                            $medicinUseMorning = $resultMedicineUse['name'];
                                                        }
                                                    }
                                                    echo ',&nbsp;&nbsp;&nbsp;' . $medicinUseMorning;
                                                    ?>
                                                </td>                                            
                                            </tr>
                                            <?php
                                        }
                                    }
                                }
                                ?>           
                            </table>
                        </div>                    
                    </div>
                    <!-- Doctor Appointment -->
                    <div class="legend" style="width: 32.5%; float: left; padding: 2px;">
                        <div class="legend_title"><label for="ConsultationDateFirstComplaint"><b><?php echo MENU_APPOINTMENT_MANAGEMENT; ?></b></label></div>
                        <div class="legend_content" style="height: 120px;">
                            <table style="width: 100%">
                                <?php
                                $appointmentId = "";
                                $appointmentDate = "";
                                $appointmentDesc = "";
                                $queryAppointment = mysql_query("SELECT id, app_date, description FROM appointments WHERE queue_doctor_id = {$consultation['QeuedDoctor']['id']}");
                                while ($rowAppointment = mysql_fetch_array($queryAppointment)) {
                                    $appointmentId = $rowAppointment['id'];
                                    $appointmentDate = date('d/m/Y H:i', strtotime($rowAppointment['app_date']));
                                    $appointmentDesc = $rowAppointment['description'];
                                }
                                $patientConsultationAppDate = "PatientConsultationNumAppDate";
                                if (!empty($display)) {
                                    $patientConsultationAppDate = "PatientConsultationNumAppDate" . $consultation['PatientConsultation']['id'];
                                }
                                ?>
                                <tr>
                                    <td style="width: 30%;">
                                        <input type="hidden" name="data[Appointment][id]" value="<?php echo $appointmentId; ?>" />
                                        <label for="<?php echo $patientConsultationAppDate; ?>"><?php echo APPOINTMENT_DATE; ?> :</label>
                                    </td>
                                    <td><?php echo $this->Form->text('app_date', array('id' => $patientConsultationAppDate, 'disabled' => $disabled, 'value' => $appointmentDate, 'readonly' => true, "style" => "width: 97% ! important;")); ?></td>
                                </tr>
                                <tr>
                                    <td><label for="PatientConsultationDescription"><?php echo TABLE_FOR; ?> :</label></td>
                                    <td><?php echo $this->Form->textarea('description', array('disabled' => $disabled, 'type' => 'textarea', 'value' => $appointmentDesc, 'style' => 'width: 97% ! important; height: 72px ! important;')); ?></td>
                                </tr>
                            </table>        
                        </div>
                    </div> 
                    <!-- Immunization -->
                    <div class="legend" style="width: 32.5%; float: left; padding: 2px;">
                        <div class="legend_title"><label for="PatientConsultationImmunization"><b><?php echo 'Immunization'; ?></b></label></div>
                        <div class="legend_content vaccineIPDHistory" style="height: 120px; overflow-y: scroll; cursor: pointer;">
                            <table class="table" style="border: none;">
                                <?php
                                if (!empty($resultImmuzation)) {
                                    $resultVaccine = implode(',', $resultImmuzation);
                                    $queryPrscription = mysql_query("SELECT * FROM orders WHERE orders.status > 0 AND orders.queue_doctor_id = {$consultation['QeuedDoctor']['id']} AND orders.id IN ({$resultVaccine}) ORDER BY created ASC");
                                    if (mysql_num_rows($queryPrscription)) {
                                        while ($resultPrescription = mysql_fetch_array($queryPrscription)) {
                                            ?>                            
                                            <tr>
                                                <td style="border: none; font-weight: bold; background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo date('d/m/Y H:i:s', strtotime($resultPrescription['created'])); ?></td>
                                                <td style="border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo getDoctor($resultPrescription['created_by']); ?></td>
                                            </tr>
                                            <?php
                                            $index = 0;
                                            $queryPrscriptionDetail = mysql_query("SELECT *, products.name, products.code, uoms.abbr FROM order_details "
                                                    . "INNER JOIN products ON products.id = order_details.product_id "
                                                    . "INNER JOIN product_pgroups ON product_pgroups.product_id = products.id "
                                                    . "INNER JOIN uoms ON uoms.id = order_details.qty_uom_id "
                                                    . "WHERE order_details.order_id = {$resultPrescription['id']} AND pgroup_id = 27");
                                            ?>

                                            <?php
                                            while ($orderDetail = mysql_fetch_array($queryPrscriptionDetail)) {
                                                $productName = $orderDetail['name'];
                                                ?>
                                                <tr>
                                                    <td style="border: none;" colspan="2">
                                                        <?php echo ++$index; ?> - 
                                                        <?php
                                                        echo $productName;
                                                        if (trim($orderDetail['note']) != "") {
                                                            echo '&nbsp;&nbsp;>&nbsp;&nbsp;' . $orderDetail['note'];
                                                        }
                                                        if ($orderDetail['qty'] != "") {
                                                            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $orderDetail['qty'];
                                                        }
                                                        if ($orderDetail['abbr'] != "") {
                                                            echo '&nbsp;&nbsp;&nbsp; ' . $orderDetail['abbr'];
                                                        }
                                                        if ($orderDetail['num_days'] != "") {
                                                            echo ',&nbsp;&nbsp;&nbsp;' . $orderDetail['num_days'];
                                                        }
                                                        $medicinUseMorning = "";
                                                        if ($orderDetail['morning_use_id'] != "") {
                                                            $queryMedicineUse = mysql_query("SELECT name FROM treatment_uses WHERE id = {$orderDetail['morning_use_id']}");
                                                            while ($resultMedicineUse = mysql_fetch_array($queryMedicineUse)) {
                                                                $medicinUseMorning = $resultMedicineUse['name'];
                                                            }
                                                        }
                                                        echo ',&nbsp;&nbsp;&nbsp;' . $medicinUseMorning;
                                                        ?>
                                                    </td>
                                                </tr>                                        
                                                <?php
                                            }
                                            $queryPrscriptionMisc = mysql_query("SELECT order_miscs.*, uoms.abbr FROM order_miscs INNER JOIN uoms ON uoms.id =  order_miscs.qty_uom_id WHERE order_miscs.order_id = {$resultPrescription['id']}");
                                            while ($orderMisc = mysql_fetch_array($queryPrscriptionMisc)) {
                                                ?>
                                                <tr>
                                                    <td style="border: none;" colspan="2">
                                                        <?php echo ++$index; ?> -  
                                                        <?php
                                                        echo $productName = $orderMisc['description'];
                                                        if (trim($orderMisc['note']) != "") {
                                                            echo '&nbsp;&nbsp;&nbsp;&nbsp;' . $orderMisc['note'];
                                                        }
                                                        if ($orderMisc['qty'] != "") {
                                                            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $orderMisc['qty'];
                                                        }
                                                        if ($orderMisc['num_days'] != "") {
                                                            echo ',&nbsp;&nbsp;&nbsp; ' . TABLE_NUM_DAYS . ': ' . $orderMisc['num_days'];
                                                        }
                                                        $medicinUseMorning = "";
                                                        if ($orderMisc['morning_use_id'] != "") {
                                                            $queryMedicineUse = mysql_query("SELECT name FROM treatment_uses WHERE id = {$orderMisc['morning_use_id']}");
                                                            while ($resultMedicineUse = mysql_fetch_array($queryMedicineUse)) {
                                                                $medicinUseMorning = $resultMedicineUse['name'];
                                                            }
                                                        }
                                                        echo ',&nbsp;&nbsp;&nbsp;' . $medicinUseMorning;
                                                        ?>
                                                    </td>                                            
                                                </tr>
                                                <?php
                                            }
                                        }
                                    }
                                }
                                ?>
                            </table>
                            <div class="clear"></div>
                        </div>                    
                    </div>
                </div>
                <div class="clear"></div>  
                <div style="width: 100%;">
                    <div class="legend" style="width: 32.5%; float: left; padding: 2px;">
                        <div class="legend_title"><label for="ConsultationDaignostic"><b><?php echo MENU_VITAL_SING; ?></b></label></div>
                        <div class="legend_content vitalSignPntIPDHistory" style="height: 240px; overflow-y: scroll;">
                            <table style="border: medium none; width: 100%; padding-top: 7px; padding-bottom: 7px;" cellpadding="5" cellspacing="0">
                                <tbody>
                                    <tr>
                                        <td style="border: none; font-weight: bold; background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo date('d/m/Y H:i:s', strtotime($consultation['PatientVitalSign']['created'])); ?></td>
                                        <td style="border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo getDoctor($consultation['PatientVitalSign']['created_by']); ?></td>
                                        <td style="width: 5%; border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <fieldset>
                                <legend><?php __(MENU_VITAL_SING); ?></legend>
                                <table style="width: 100%;" cellspacing="3">
                                    <tr>
                                        <td style="width: 15%;"><label for="PatientVitalSignHeight"><?php echo TABLE_HEIGHT; ?></label></td>
                                        <td style="width: 20%;">: <?php echo $consultation['PatientVitalSign']['height']; ?> cm</td>
                                        <td style="width: 15%;"><label for="PatientVitalSignWeight"><?php echo TABLE_WEIGHT; ?></label></td>
                                        <td style="width: 15%;">: <?php echo $consultation['PatientVitalSign']['weight']; ?> kg</td>
                                        <td style="width: 25%;"><label for="PatientVitalSignBMI"><?php echo TABLE_BMI; ?>: <span class="BMI"><?php echo $consultation['PatientVitalSign']['BMI'] ?></span></label></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%;"><label for="PatientVitalSignPulse"><?php echo TABLE_PULSE; ?></label></td>
                                        <td style="width: 20%;">: <?php echo $consultation['PatientVitalSign']['pulse']; ?> /m</td>
                                        <td style="width: 15%;"><label for="PatientVitalSignRespiratory"><?php echo TABLE_RESPIRATORY; ?></label></td>
                                        <td style="width: 20%;" colspan="2">: <?php echo $consultation['PatientVitalSign']['respiratory']; ?> /m</td>                     
                                    </tr>
                                    <tr>
                                        <td style="width: 15%;"><label for="PatientVitalSignTemperature"><?php echo TABLE_TEMPERATURE; ?></label></td>
                                        <td style="width: 20%;">: <?php echo $consultation['PatientVitalSign']['temperature']; ?> °C</td>
                                        <td style="width: 15%;"><label for="PatientVitalSignSop2"><?php echo "SPO<sub>2</sub>"; ?></label></td>
                                        <td style="width: 20%;" colspan="2">: <?php echo $consultation['PatientVitalSign']['sop2']; ?></td> 
                                    </tr>
                                    <tr style="display: none;">
                                        <td style="width: 15%;"><label for="PatientVitalSignTemperature">Description</label></td>
                                        <td style="width: 20%;" colspan="4">: <?php echo $consultation['PatientVitalSign']['other_info']; ?> </td>
                                    </tr>
                                </table>      
                            </fieldset>
                            <fieldset>
                                <legend><?php __(MENU_BLOOD_PRESSURE); ?></legend>
                                <table class="table" style="width: 100%;">
                                    <tr>
                                        <th class="first" style="width: 10%"></th>
                                        <th style="font-size: 10px !important;">1st</th>
                                        <th style="font-size: 10px !important;">2nd</th>
                                        <th style="font-size: 10px !important;">3rd</th>
                                    </tr>
                                    <tr>
                                        <td class="first">Systolic</td>            
                                        <td><?php echo $consultation['PatientVitalSignBloodPressure']['result_systolic_1']; ?> mmHg</td>
                                        <td><?php echo $consultation['PatientVitalSignBloodPressure']['result_systolic_2']; ?> mmHg</td>
                                        <td><?php echo $consultation['PatientVitalSignBloodPressure']['result_systolic_3']; ?> mmHg</td>
                                    </tr>
                                    <tr>
                                        <td class="first">Diastolic</td>            
                                        <td><?php echo $consultation['PatientVitalSignBloodPressure']['result_diastolic_1']; ?> mmHg</td>
                                        <td><?php echo $consultation['PatientVitalSignBloodPressure']['result_diastolic_2']; ?> mmHg</td>
                                        <td><?php echo $consultation['PatientVitalSignBloodPressure']['result_diastolic_3']; ?> mmHg</td>
                                    </tr>
                                </table>
                            </fieldset>
                            <table width="100%" style="border:none; padding-top: 6px;" cellpadding="5" cellspacing="0">
                                <tbody id="tbVitalSignPntIPDResult">
                                    <?php
                                    if (!empty($patientIpd)) {
                                        $query_vital_sign = mysql_query("SELECT * FROM patient_ipd_vital_signs WHERE is_active=1 AND patient_ipd_id=" . $patientIpd['PatientIpd']['id'] . " ORDER BY created ASC");
                                        while ($data_vital_sign = mysql_fetch_array($query_vital_sign)) {
                                            ?>
                                            <tr>
                                                <td style="border: none; font-weight: bold; background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo date('d/m/Y H:i:s', strtotime($data_vital_sign['created'])); ?></td>
                                                <td style="border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo getDoctor($data_vital_sign['created_by']); ?></td>
                                                <td style="width: 5%; border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);">
                                                    <?php
                                                    if (($data_vital_sign['created_by'] == $user['User']['id'] && $patientIpd['PatientIpd']['is_active'] == 1) || $allowAdmin) {
                                                        echo '<a href="#" class="btnEditVitalSignIPD" patientIPDId="' . $data_vital_sign['patient_ipd_id'] . '" rel="' . $data_vital_sign['id'] . '" ><img alt="Edit" onmouseover="Tip(\'' . ACTION_EDIT . '\')" src="' . $this->webroot . 'img/action/edit.png" /></a>';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <table style="width: 100%;" cellpadding="3" cellspacing="0">
                                                        <tr>
                                                            <td><label for="PatientIPDVitalSignBp"><?php echo 'BP'; ?></label> = <?php echo $data_vital_sign['bp']; ?> mmHg</td>
                                                        </tr>
                                                        <tr>
                                                            <td><label for="PatientIPDVitalSignHr"><?php echo 'HR'; ?></label> = <?php echo $data_vital_sign['hr']; ?> bpm</td>
                                                        </tr>
                                                        <tr>
                                                            <td><label for="PatientIPDVitalSignTemperature"><?php echo 'T<sup>o</sup>'; ?></label> = <?php echo $data_vital_sign['temperature']; ?> °C</td>
                                                        </tr>
                                                        <tr>     
                                                            <td><label for="PatientIPDVitalSignRr"><?php echo 'RR'; ?></label> = <?php echo $data_vital_sign['rr']; ?> / min</td>
                                                        </tr>
                                                        <tr style="display: none;">
                                                            <td><label for="PatientIPDVitalSignUrine"><?php echo 'Urine'; ?></label> = <?php echo $data_vital_sign['urine']; ?> ml/24h</td>
                                                        </tr>
                                                        <tr style="display: none;">
                                                            <td><label for="PatientIPDVitalSignGasFecal"><?php echo 'Gas and Fecal'; ?> : </label> <?php echo $data_vital_sign['gas_fecal']; ?></td>
                                                        </tr>
                                                        <tr style="display: none;">
                                                            <td><label for="PatientIPDVitalSignDrainage"><?php echo 'Drainage'; ?></label> : <?php echo $data_vital_sign['drainage']; ?> mL</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="vertical-align: top;"><label for="PatientIPDVitalSignNote"><?php echo 'Note'; ?></label> : <?php echo nl2br($data_vital_sign['note']); ?></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <div class="clear"></div>
                        </div>
                        <div class="legend_content">
                            <div class="buttons">
                                <a href="#" patientIPDId="<?php echo $patientIpd['PatientIpd']['id']; ?>" name="" title="<?php echo MENU_VITAL_SING_ADD; ?>" class="positive btnIPDVitalSign">
                                    <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
                                    <?php echo MENU_VITAL_SING_ADD; ?>
                                </a>
                            </div>
                            <div style="clear: both;"></div>
                        </div>
                    </div>
                    <!-- Daily Clinical Report -->
                    <div class="legend" style="width: 32.5%; float: left; padding: 2px;">
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
                                <tbody id="tbFollowUpPntIPDResult">
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
                    <!-- Doctor Comment -->
                    <div class="legend" style="width: 32.5%; float: left; padding: 2px;">
                        <div class="legend_title"><label for="PatientConsultationRemark"><b><?php echo MENU_REMARKS; ?></b></label></div>
                        <div class="legend_content doctorCommentPntIPDHistory" style="height: 240px; overflow-y: scroll; cursor: pointer;">
                            <table class="table" width="100%" style="border:none;">
                                <thead style="<?php
                                if ($consultation['PatientConsultation']['remark'] == "") {
                                    echo 'display:none;';
                                }
                                ?>">
                                    <tr>
                                        <td style="border: none; font-weight: bold; background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo date('d/m/Y H:i:s', strtotime($consultation['PatientConsultation']['created'])); ?></td>
                                        <td style="border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo getDoctor($consultation['PatientConsultation']['created_by']); ?></td>
                                        <td style="width: 5%; border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);">
                                            <?php
                                            if ($consultation['PatientConsultation']['created_by'] == $user['User']['id'] || $allowAdmin) {
                                                echo '<a href="#" class="btnEditDoctorCommentIPD" type="consult" rel="' . $consultation['PatientConsultation']['id'] . '" ><img alt="Edit" onmouseover="Tip(\'' . ACTION_EDIT . '\')" src="' . $this->webroot . 'img/action/edit.png" /></a>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border: none; padding-left: 5%; color: rgb(0, 0, 0);" colspan="3"><?php echo nl2br($consultation['PatientConsultation']['remark']); ?></td>
                                    </tr>
                                </thead>
                                <tbody id="tbDoctorCommentPntIPDResult">
                                    <?php
                                    $index = 1;
                                    $query_doctor_comment = mysql_query("SELECT * FROM doctor_comments WHERE is_active=1 AND patient_consultation_id=" . $consultation['PatientConsultation']['id'] . " ORDER BY created ASC");
                                    while ($data_doctor_comment = mysql_fetch_array($query_doctor_comment)) {
                                        ?>
                                        <tr>
                                            <td style="border: none; font-weight: bold; background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo date('d/m/Y H:i:s', strtotime($data_doctor_comment['created'])); ?></td>
                                            <td style="border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);"><?php echo getDoctor($data_doctor_comment['created_by']); ?></td>
                                            <td style="width: 5%; border: none; font-weight: bold; text-align: right; font-size: 11px !important;background-color: rgb(204, 204, 204); color: rgb(0, 0, 0);">
                                                <?php
                                                if ($data_doctor_comment['created_by'] == $user['User']['id'] || $allowAdmin) {
                                                    echo '<a href="#" class="btnEditDoctorCommentIPD" type="doctor_comment" rel="' . $data_doctor_comment['id'] . '" ><img alt="Edit" onmouseover="Tip(\'' . ACTION_EDIT . '\')" src="' . $this->webroot . 'img/action/edit.png" /></a>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="border: none; padding-left: 5%; color: rgb(0, 0, 0);" colspan="3">
                                                <?php echo nl2br($data_doctor_comment['doctor_comment']); ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>                                                                       
                        </div>
                        <div class="legend_content">
                            <div class="buttons">
                                <div class="buttons">
                                    <a href="#" queuedDoctorId="<?php echo $consultation['PatientConsultation']['queued_doctor_id']; ?>" name="<?php echo $consultation['PatientConsultation']['id']; ?>" title="<?php echo $consultation['Queue']['id']; ?>" class="positive btnIPDDoctorComment" >
                                        <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
                                        <?php echo MENU_REMARK_LABEL_ADD; ?>
                                    </a>
                                </div>
                            </div>
                            <div style="clear: both;"></div>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
                <div class="buttons" style='<?php echo $display; ?>' >
                    <button type="submit" class="positive">
                        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
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
        ?>
    </div>
    <div id="dialog" title=""></div>
    <div id="dialog9" title=""></div>
    <div id="dialogPrint<?php echo $tblName; ?>" title="" style="display: none;">
        <br />
        <center>
            <div class="buttons" style="display: inline-block;">
                <button type="button" id="btnPatientConsultation<?php echo $tblName; ?>" class="positive">
                    <img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/>
                    <?php echo ACTION_PRINT; ?>
                </button>
            </div>
        </center>
    </div>