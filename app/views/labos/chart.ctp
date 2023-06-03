
<script type="text/javascript">
    function getCheckedValue(radioObj) {
        if(!radioObj)
            return "";
        var radioLength = radioObj.length;
        if(radioLength == undefined)
            if(radioObj.checked)
                return radioObj.value;
        else
            return "";
        for(var i = 0; i < radioLength; i++) {
            if(radioObj[i].checked) {
                return radioObj[i].value;
            }
        }
        return "";
    }
    $(document).ready(function(){
        $('#loading').hide();
        var dates = $( "#date_from, #date_to" ).datepicker({
            changeMonth: true,
            changeYear: true,
            showWeek: true,
            numberOfMonths: 1,
            dateFormat: "dd/mm/yy",
            onSelect: function( selectedDate ) {
                var option = this.id == "date_from" ? "minDate" : "maxDate",
                instance = $( this ).data( "datepicker" );
                date = $.datepicker.parseDate(
                instance.settings.dateFormat ||
                $.datepicker._defaults.dateFormat,
                selectedDate, instance.settings );
                dates.not( this ).datepicker( "option", option, date );
            }
        });
        $('#due_date').change(function(e){
            if($('#due_date').val()){
                $('#date_from').val('');
                $('#date_to').val('');
            }
        });
        $('#btn_go').click(function(e){
            var labo = $('#by_labo').val();
            if(labo != ''){
                $.ajax({
                    type: "POST",
                    cache: true,
                    async: true,
                    url: "chart_show/",
                    data:   "due_date=" + document.getElementById('due_date').value +
                        "&date_from=" + document.getElementById('date_from').value +
                        "&date_to=" + document.getElementById('date_to').value +
                        "&by_labo=" + document.getElementById('by_labo').value ,
                    beforeSend: function(){
                        $('#loading').show();
                    },
                    success: function(msg){
                        $('#loading').hide();
                        $("#update_panel").html(msg);
                    }
                });
            }else{
                $('<div class="opened-dialogs">').html("<p style='font-weight: bold;color:red;'>Please select labo !</p>").dialog({
                        autoOpen: true,
                        title: "Chart",
                        modal: true,
                        width: 300,
                        height: 150,
                        resizable: false,
                        buttons: {Ok: function() {
                                $(this).dialog("close");
                        }}
                });
            }
        });
    });
</script>
<div class="legend inner_title">
    <div class="legend_title"><?php echo MENU_CHART; ?></div>
    <div class="legend_content">
	<table class="defaultTable search" style="width:100%;">
		<tr>
			<td><?php echo 'Date'; ?></td>
			<td><?php echo 'From'; ?></td>
			<td><?php echo 'To'; ?></td>
			 <td><?php echo 'Labo'; ?></td>
			<td rowspan="4" style="vertical-align: middle"><img id="btn_go" alt="" src="<?php echo $this->webroot; ?>img/search.png" style="cursor:pointer" onmouseover="Tip('Click to view the report')" /></td>
		</tr>
		<tr>
			<td>
				<select id="due_date">
					<option value="" style="padding: 3px">All</option>
					<option value="Today" style="padding: 3px">Today</option>
					<option value="This Week" style="padding: 3px">This Week</option>
					<option value="This Month" style="padding: 3px">This Month</option>
				</select>
			</td>
			<td><input type="text" id="date_from" /></td>
			<td><input type="text" id="date_to" /></td>
			
			 <td>
				<select id="by_labo">
					<option value="" style="padding: 3px">Select Labo</option>
					<?php
					$selectLabo = mysql_query("SELECT * FROM labo_title_groups");
					while($rows = mysql_fetch_array($selectLabo)){
					?>
						<option value="<?php echo $rows['id']; ?>" style="padding: 3px"><?php echo $rows['name']; ?></option>
					<?php
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			
		</tr>
	</table>
        </div>
</div>
<div class="child" style="border:none">
	<div style="text-align:center;padding:20px 0 20px 0">
		<div id="loading"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" align="texttop" /></div>
		<div id="update_panel" style="padding:20px"><?php echo 'Your search result will display here...'; ?></div>
            </div>
	</div>
        
</div>
