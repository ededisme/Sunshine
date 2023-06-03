<?php $absolute_url  = FULL_BASE_URL . Router::url("/", false); ?>
<style type="text/css">
    form input[type=text] {
        width: 120px;
    }
    form input[type=password] {
        width: 120px;
    }
    form select {
        width: 120px;
    }
</style>
<script type="text/javascript">
    $(document).ready(function(){
        var dates = $("#date_from, #date_to").datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            onSelect: function( selectedDate ) {
                if(($("#date_from").val() != "") || ($("#date_to").val() != "")){
                    $("#due_date").val("");
                }
                var option = this.id == "date_from" ? "minDate" : "maxDate",
                    instance = $( this ).data( "datepicker" );
                    date = $.datepicker.parseDate(
                        instance.settings.dateFormat ||
                        $.datepicker._defaults.dateFormat,
                        selectedDate, instance.settings );
                dates.not( this ).datepicker( "option", option, date );
            }
        });
        $("#due_date").change(function(){
            if($("#due_date").val()!=""){
                $("#date_from").val("");
                $("#date_to").val("");
            }
        });
        $("#btnSubmit").click(function(){
            $.ajax({
                type: "POST",
                url: "<?php echo $absolute_url.$this->params['controller']; ?>/reportResult",
                data: $("#frmReport").serialize(),
                beforeSend: function(){
                    $("#loading").show();
                },
                success: function(msg){
                    $("#result").html(msg);
                }
            });
        });
    });
</script>
<div class="legend">
    <div class="legend_title"><?php echo MENU_REPORT; ?></div>
    <div class="legend_content">
        <form id="frmReport" action="" method="post">
        <table style="width: 100%;">
            <tr>
                <td><?php echo REPORT_DUE_DATE; ?>:</td>
                <td>
                    <select id="due_date" name="due_date">
                        <option value="">Please Select</option>
                        <option value="Today">Today</option>
                        <option value="This Week">This Week</option>
                        <option value="This Month">This Month</option>
                    </select>
                </td>
                <td><?php echo REPORT_FROM; ?>:</td>
                <td><input type="text" id="date_from" name="date_from" /></td>
                <td><?php echo REPORT_TO; ?>:</td>
                <td><input type="text" id="date_to" name="date_to" /></td>
                <td><?php echo PATIENT_TYPE; ?>:</td>
                <td>
                    <select name="patient_type">
                        <option value="">Please Select</option>
                        <option value="new_patient">New Patient</option>
                        <option value="old_patient">Old Patient</option>
                    </select>
                </td>
                <td>
                    <div class="buttons">
                        <button type="button" id="btnSubmit" class="positive">
                            <img src="<?php echo $this->webroot; ?>img/button/search.png" alt=""/>
                            <?php echo GENERAL_SEARCH; ?>
                        </button>
                    </div>
                </td>
            </tr>
        </table>
        </form>
    </div>
</div>
<br />
<div class="legend">
    <div class="legend_title"><?php echo GENERAL_RESULT; ?></div>
    <div id="result" class="legend_content">
        <p style="text-align: center;"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" id="loading" style="display: none;" /></p>
    </div>
</div>