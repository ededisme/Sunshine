<?php if(empty($labo)){echo GENERAL_NO_RECORD;exit();} ?>
<?php $absolute_url  = FULL_BASE_URL . Router::url("/", false); ?>
<?php include('includes/function.php'); ?>
<?php $tblName = "tbl123"; ?>
<script type="text/javascript">
    $(document).ready(function(){
        $('.legend').hide();           
        $("#labo<?php echo $tblName;?>").accordion({
            collapsible: true,
            autoHeight: false,
            navigation: false,
            active: false
        });  
        
        setTimeout(function(){
            equalHeight($(".column"));
        },20000);
        
        $(".btnView").click(function(event){
            event.preventDefault();
            event.stopPropagation(); 
            var id = $(this).attr('rel');
            var name = $(this).attr('title');
            $.ajax({
                type: "GET",
                url: "<?php echo $absolute_url.$this->params['controller']; ?>/viewLaboResult/" + id,
                data: "",
                beforeSend: function(){
                    $("#dialogPreviewLaboResult").html('<p style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                },
                success: function(msg){
                    $("#dialogPreviewLaboResult").html(msg);
                }
            });
            $("#dialogPreviewLaboResult").dialog({
                title: name + ' Information',
                resizable: false,
                modal: true,
                width: '90%',
                height: 500,
                buttons: {
                    Ok: function() {
                        $( this ).dialog( "close" );
                    }
                }
            });
        });
        
        $(".btnPrint").click(function(event){
            event.preventDefault();
            $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            event.stopPropagation(); 
            var btnPatientLaboFormFirst=$("#dialogLaboPrint").html();
            var queueId = $(this).attr('queueId');
            var laboId = $(this).attr('laboId');
            var name = $(this).attr('name');
            $("#patientLaboForm").load("<?php echo $absolute_url . $this->params['controller']; ?>/printLab/" + queueId + "/" + laboId);
            $("#dialogLaboPrint").html(btnPatientLaboFormFirst);
            $("#dialogLaboPrint").dialog({
                title: '<?php echo ACTION_PRINT_DOCTOR_LABO_FROM; ?>',
                resizable: false,
                modal: true,      
                width: '250',
                height: '150',
                buttons: {
                    Ok: function() {
                        $( this ).dialog( "close" );
                    }
                }
            });
            $("#btnPatientLaboForm").click(function(){   
                w=window.open();
                w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                w.document.write('<style type="text/css">.info th{font-size: 10px;}.info td{font-size: 10px;}.table th{font-size: 10px;}.table td{font-size: 10px;}</style>');
                w.document.write($("#patientLaboForm").html());
                w.document.close();
//                try
//                {
//                    //Run some code here                                                                                                       
//                    jsPrintSetup.setSilentPrint(1);
//                    jsPrintSetup.printWindow(w);
//                }
//                catch(err)
//                {
//                    //Handle errors here                                    
//                    w.print();                                     
//                } 
//                w.close();
            });
        });
    });
    function equalHeight(group) {
        var tallest = 0;
        group.each(function() {
            var thisHeight = $(this).height();
            if(thisHeight > tallest) {
                tallest = thisHeight;
            }
        });
        group.height(tallest);
    } 
    
</script>

<div id="labo<?php echo $tblName;?>">
<?php
    $ind = 1;
    foreach ($labo as $historyLabo):                        
    ?>   
    <h3>
        <a href="#">
            <?php echo "# : "; ?>
            <?php echo date('d/m/Y H:i:s', strtotime($historyLabo['Labo']['created'])); ?>
            <div style="float:right;">
                <img alt="" src="<?php echo $this->webroot; ?>img/button/view.png" class="btnView" rel="<?php echo $historyLabo['QueuedLabo']['id'];?>" title="<?php echo $historyLabo['Labo']['queued_id']; ?>" onmouseover="Tip('<?php echo ACTION_VIEW; ?>')" />
            </div>
            <div style="float:right; padding-left: 7px; padding-right: 7px;">
                <img alt="" src="<?php echo $this->webroot; ?>img/button/printer.png" class="btnPrint" queueId="<?php echo $historyLabo['QueuedLabo']['queue_id'];?>" laboId="<?php echo $historyLabo['Labo']['id'];?>" name="<?php echo $historyLabo['Labo']['queued_id']; ?>" onmouseover="Tip('<?php echo ACTION_PRINT; ?>')" />
            </div>
        </a>
    </h3>
    <div class="<?php echo $historyLabo['Labo']['id']; ?>">
        <table style="width: 100%; display: none;" cellpadding="3" cellspacing="0">
            <tr>
                <td style="width: 99.6%;"><labe for="LaboChiefComplain"><?php echo TABLE_CHIEF_COMPLAIN; ?>: </label><?php echo $historyLabo['Labo']['chief_complain']; ?></td>
            </tr>
            <tr>
                <td style="width: 99.6%;"><labe for="LaboDiagonist"><?php echo TABLE_DAIGNOSTIC; ?>: </label><?php echo $historyLabo['Labo']['diagonist']; ?></td>
            </tr>    
        </table>
        <table style="width: 100%;">
            <?php
            $laboSelected = array();
            $queryLaboRequest = mysql_query("SELECT * FROM labo_requests WHERE is_active!=2 AND labo_id=".$historyLabo['Labo']['id']);
            while ($resultLaboRequest = mysql_fetch_array($queryLaboRequest)) {
                $laboSelected[] = $resultLaboRequest['labo_item_group_id'];
            }
            if(!empty($laboSelected)){
                $laboItem =  implode(',', $laboSelected);
                $queryLaboRequest = mysql_query("SELECT labg.name FROM labo_item_groups As labg "                                       
                                            . "WHERE labg.id IN ({$laboItem})");
                while ($rowLaboRequest = mysql_fetch_array($queryLaboRequest)) {
                    ?>
                    <tr>
                        <td style="width: 30px;"><input type="checkbox" checked="checked" disabled="disabled" /></td>
                        <td><label><?php echo $rowLaboRequest['name'];?></label></td>
                    </tr>
                    <?php
                }
            }
            ?>                                                                   
        </table>           
    </div>    
    <?php $ind++; endforeach; ?>
    
</div>
<div class="clear"></div>
<div id="dialogPreviewLaboResult" title=""></div>
<div id="dialogLaboPrint" title="" style="display: none;">
    <br />
    <center>
        <div class="buttons" style="display: inline-block;">
            <button type="button" id="btnPatientLaboForm" class="positive">
                <img src="<?php echo $this->webroot; ?>img/button/print.png" alt=""/>
                <?php echo ACTION_PRINT; ?>
            </button>
        </div>
    </center>
</div>
<div id="patientLaboForm" style="display: none;"></div>