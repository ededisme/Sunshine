<?php echo $this->element('prevent_multiple_submit'); ?>
<script type="text/javascript">
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        $("#ReferralAddForm").validationEngine('attach', {
            isOverflown: true,
            overflownDIV: ".ui-tabs-panel"
        });
        $("#ReferralAddForm").ajaxForm({
            beforeSerialize: function($form, options) {
                
            },
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveReferral").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $(".btnBackReferral").click();
                // alert message
                if(result != '<?php echo MESSAGE_DATA_HAS_BEEN_SAVED; ?>' && result != '<?php echo MESSAGE_DATA_COULD_NOT_BE_SAVED; ?>' && result != '<?php echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM; ?>'){
                    createSysAct('Referral', 'Add', 2, result);
                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_PROBLEM; ?></p>');
                }else {
                    createSysAct('Referral', 'Add', 1, '');
                    // alert message
                    $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result+'</p>');
                }
                $("#dialog").dialog({
                    title: '<?php echo DIALOG_INFORMATION; ?>',
                    resizable: false,
                    modal: true,
                    width: 'auto',
                    height: 'auto',
                    open: function(event, ui){
                        $(".ui-dialog-buttonpane").show();
                    },
                    buttons: {
                        '<?php echo ACTION_CLOSE; ?>': function() {
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
        
        $(".btnBackReferral").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableReferral.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
        
        $("#ReferralDob" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
            yearRange: '-100:-0',
            maxDate: 0,
            beforeShow: function(){
                setTimeout(function(){
                    $("#ui-datepicker-div").css("z-index", 1000);
                }, 10);
            }
        }).unbind("blur");
        
        $("#ReferralDob").change(function(){
            var dob = $("#ReferralDob").val();
                dob = dob.substr(0, 10).split("-");
                dob = dob[1] + "/" + dob[2] + "/" + dob[0];
            var age = getAge(dob);
                age = age.substr(0, 10).split(",");
                $('#ReferralAge').val(age[0]);
                $('#ReferralAgeMonth').val(age[1]);
                if(age[2]>0){
                    $('#ReferralAgeDay').val(age[2]-1);
                }else{
                    $('#ReferralAgeDay').val(age[2]);
                }
        });        
        
        $('#ReferralAge').attr('autocomplete', 'off');
        
        $("#ReferralAge").keyup(function(){
            var now = (new Date()).getFullYear();
            var age = parseUniInt($("#ReferralAge").val());
            var year = now - age;
            var dob = year + '-01-01';
            $('#ReferralDob').val(dob);
            $('#ReferralAgeMonth').val('');
            $('#ReferralAgeDay').val('');            
            getAgeReferral(dob);                        
        });
        
    });
    function getAgeReferral(dob = null){            
        dob = dob.substr(0, 10).split("-");
        dob = dob[1] + "/" + dob[2] + "/" + dob[0];
        var age = getAge(dob);
            age = age.substr(0, 10).split(",");
            $('#ReferralAge').val(age[0]);
            $('#ReferralAgeMonth').val(age[1]);
            if(age[2]>0){
                $('#ReferralAgeDay').val(age[2]-1);
            }else{
                $('#ReferralAgeDay').val(age[2]);
            }
    }

    function isNumberKey(event){
        var charCode = (event.which)?event.which : event.keyCode;
        if ((charCode > 31 && (charCode < 46 || charCode > 57))|| charCode === 47){
            return false;
        }
        return true;
    }

    function getAge(dateString) {
        var now = new Date();
        var today = new Date(now.getYear(),now.getMonth(),now.getDate());

        var yearNow = now.getYear();
        var monthNow = now.getMonth();
        var dateNow = now.getDate();

        var dob = new Date(dateString.substring(6,10),
                           dateString.substring(0,2)-1,                   
                           dateString.substring(3,5)                  
                           );

        var yearDob = dob.getYear();
        var monthDob = dob.getMonth();
        var dateDob = dob.getDate();
        var age = {};
        var ageString = "";
        var yearString = "";
        var monthString = "";
        var dayString = "";
        yearAge = yearNow - yearDob;

        if (monthNow >= monthDob)
          var monthAge = monthNow - monthDob;
        else {
          yearAge--;
          var monthAge = 12 + monthNow -monthDob;
        }

        if (dateNow >= dateDob)
          var dateAge = dateNow - dateDob;
        else {
          monthAge--;
          var dateAge = 31 + dateNow - dateDob;

          if (monthAge < 0) {
            monthAge = 11;
            yearAge--;
          }
        }

        age = {
            years: yearAge,
            months: monthAge,
            days: dateAge
        };

        if ( age.years > 1 ) yearString = " years";
        else yearString = " year";
        if ( age.months> 1 ) monthString = " months";
        else monthString = " month";
        if ( age.days > 1 ) dayString = " days";
        else dayString = " day";

        if ( (age.years > 0) && (age.months > 0) && (age.days > 0) )
            ageString = age.years + ", " + age.months + ", " + age.days ;
//          ageString = age.years + yearString + ", " + age.months + monthString + ", and " + age.days + dayString + " old.";
        else if ( (age.years == 0) && (age.months == 0) && (age.days > 0) )
          ageString = "0,0, " + age.days;
        else if ( (age.years > 0) && (age.months == 0) && (age.days == 0) )
          ageString = age.years  + ",0,0";
        else if ( (age.years > 0) && (age.months > 0) && (age.days == 0) )
          ageString = age.years + "," + age.months +",0";
        else if ( (age.years == 0) && (age.months > 0) && (age.days > 0) )
          ageString = "0, " + age.months + ", " + age.days ;
        else if ( (age.years > 0) && (age.months == 0) && (age.days > 0) )
          ageString = age.years + " ,0, " + age.days ;
        else if ( (age.years == 0) && (age.months > 0) && (age.days == 0) )
          ageString = "0,"+age.months + ",0";
        else ageString = "0,0,0";

        return ageString;
    }
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="" class="positive btnBackReferral">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php 
echo $this->Form->create('Referral'); ?>
<div>
    <fieldset>
        <legend><?php __(MENU_REFERRAL_MANAGEMENT_INFO); ?></legend>
        <table style="height: 100px;">
            <tr>
                <td><label for="ReferralName"><?php echo TABLE_NAME; ?> <span class="red">*</span> :</label></td>
                <td>
                    <div class="inputContainer">
                        <?php echo $this->Form->text('name', array('class' => 'validate[required]')); ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td><label for="ReferralSex"><?php echo TABLE_SEX; ?> <span class="red">*</span> :</label></td>
                <td>
                    <div class="inputContainer">
                        <?php echo $this->Form->input('sex', array('empty' => SELECT_OPTION, 'label' => false, 'class' => 'validate[required]', 'style' => 'width: 110px;')); ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td><label for="ReferralTelephone"><?php echo TABLE_TELEPHONE; ?> <span class="red">*</span> :</label></td>
                <td>
                    <div class="inputContainer">
                        <?php echo $this->Form->text('telephone',array('class' => 'validate[required]')); ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td><label for="ReferralDob"><?php echo TABLE_DOB; ?> <span class="red">*</span> :</label></td>
                <td>
                    <div class="inputContainer">
                    <?php echo $this->Form->text('dob', array('style'=>'width: 25%;', 'readonly' => true, 'class' => 'validate[required]','onkeypress'=>'return isNumberKey(event)')); ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <label for="ReferralAge" style="margin-left: 5px;"><?php echo TABLE_AGE; ?>:</label>
                    <?php echo $this->Form->text('age',array('style'=>'width: 39px;', 'class' => 'number validate[required]', 'maxlength' => '3')); ?>
                    &nbsp;&nbsp;&nbsp;
                    <?php echo TABLE_AGE_MONTH;?>
                    <?php echo $this->Form->text('age_month',array('style'=>'width:30px;', 'readonly'=>'readonly','disabled'=> true)); ?> 
                    &nbsp;&nbsp;
                    <?php echo TABLE_AGE_DAY;?>
                    <?php echo $this->Form->text('age_day',array('style'=>'width:30px;', 'readonly'=>'readonly', 'disabled'=> true)); ?>         
                    </div>
                </td>
            </tr>
        </table>
    </fieldset>
</div>
<div style="clear: both;"></div>
<br />
<div class="buttons">
    <button type="submit" class="positive btnSaveReferral">
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <span class="txtSaveReferral"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<div style="clear: both;"></div>
<?php echo $this->Form->end(); ?>