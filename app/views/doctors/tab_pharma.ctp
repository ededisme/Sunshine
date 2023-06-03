<script type="text/javascript">
    var index = 0;
    $(document).ready(function(){
        $('#loading').hide();
        $('#SearchMedicine').keypress(function(e){
            if((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)){
                $.ajax({
                    type: "POST",
                    url: '<?php e($this->base);?>/doctors/getMedicineStock/<?php echo $this->params['pass'][0]; ?>',
                    data: "medicine=" + $('#SearchMedicine').val(),
                    beforeSend: function(){
                        $('#loading').show();
                    },
                    success: function(msg){
                        $('#loading').hide();
                        $("#resultList").html(msg);
                        $(".addToList").each(function(){
                            if(idList.indexOf($(this).attr("rel"))!=-1){
                                $(this).hide();
                            }
                        });
                    }
                });
                return false;
            }
	});
        $('#btnSearch').click(function(){
            $.ajax({
                type: "POST",
                url: '<?php e($this->base);?>/doctors/getMedicineStock/<?php echo $this->params['pass'][0]; ?>',
                data: "medicine=" + $('#SearchMedicine').val(),
                beforeSend: function(){
                    $('#loading').show();
                },
                success: function(msg){
                    $('#loading').hide();
                    $("#resultList").html(msg);
                    $(".addToList").each(function(){
                        if(idList.indexOf($(this).attr("rel"))!=-1){
                            $(this).hide();
                        }
                    });
                }
            });
        });        
    });
</script>
<?php echo $this->Form->create('Treatment', array ('id'=>'TreatmentAddForm','url'=>'/doctors/tabPharma/'.$this->params['pass'][0], 'enctype' => 'multipart/form-data'));?>
<table>
    <tr>
        <td><label for="DiagnosticDiagnostic"><?php echo TABLE_DIAGNOSIS; ?>:</label></td>
        <td><?php echo $this->Form->text('Diagnostic.diagnostic'); ?></td>
    </tr>
    <tr>
        <td><label for="SearchMedicine"><?php echo DRUG_SEARCH_DRUG; ?>:</label></td>
        <td><?php echo $this->Form->text('Search.medicine'); ?></td>
        <td>
            <input type="button" value="<?php echo GENERAL_SEARCH; ?>" id="btnSearch"/>
            <?php echo $html->image('loading.gif',array('alt'=>'','id'=>'loading','height'=>16));?>
        </td>
    </tr>            
</table>
<p style="margin:10px 0px 0px 0px; font-size: 14px; font-weight: bold;"><?php echo DRUG_DRUG_IN_STOCK; ?></p>
<hr />
<div id="resultList">
    <table class="table" cellspacing="0">
        <tr>
            <th class="first"><?php echo TABLE_NO; ?></th>
            <th><?php echo DRUG_COMMERCIAL_NAME; ?></th>
            <th><?php echo GENERAL_TYPE; ?></th>            
            <th><?php echo DRUG_MADE_IN; ?></th>            
            <th><?php echo DRUG_ORIGIN; ?></th>
            <th><?php echo GENERAL_DISCOUNT; ?></th>
            <th><?php echo DRUG_EXPIRED_DATE; ?></th>
            <th><?php echo DRUG_AMOUNT_IN_STOCK; ?></th>
            <th><?php echo ACTION_ACTION; ?></th>
        </tr>
        <tr>
            <td class="first" colspan="9" style="text-align: center;"><?php echo TABLE_NO_RECORD; ?></td>
        </tr>
    </table>
</div>
<p style="margin:10px 0px 0px 0px; font-size: 14px; font-weight: bold;"><?php echo MENU_REQUEST_LIST; ?></p>
<hr />
<div>
    <table class="table" cellspacing="0" id="medicineRequest">
        <tr>
            <th class="first"><?php echo TABLE_NO; ?></th>
            <th><?php echo DRUG_COMMERCIAL_NAME; ?></th>
            <th><?php echo DRUG_AMOUNT_IN_STOCK; ?></th>
            <th><?php echo GENERAL_TYPE; ?></th>
            <th><?php echo DRUG_ORIGIN; ?></th>
            <th><?php echo GENERAL_QTY; ?></th>
            <th  style="text-align: center"><?php __(GENERAL_NUMBER); ?></th>
            <th style="text-align: center"><?php __(GENERAL_MORNING); ?></th>
            <th style="text-align: center"><?php __(GENERAL_AFTERNOON); ?></th>
            <th style="text-align: center"><?php __(GENERAL_EVENING); ?></th>
            <th style="text-align: center"><?php __(GENERAL_NIGHT); ?></th>
            <th><?php echo DRUG_NOTE; ?></th>
            <th><?php echo ACTION_ACTION; ?></th>
        </tr>        
        <tr id="noData">
            <td class="first" colspan="13" style="text-align: center;"><?php echo TABLE_NO_RECORD; ?></td>
        </tr>
    </table>
</div>
<br />
<div class="buttons">
    <button type="submit" id="btnSubmit" class="positive" style="display: none;">
        <img src="<?php echo $this->webroot; ?>img/button/tick.png" alt=""/>
        <?php echo ACTION_SAVE; ?>
    </button>
    <img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" class="loading" style="display: none;" />
</div>
<div style="clear: both;"></div>
<?php echo $this->Form->end(); ?>