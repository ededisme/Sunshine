<?php $absolute_url  = FULL_BASE_URL . Router::url("/", false); ?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(".table td:first-child").addClass('first');
        $("#example").dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo $absolute_url.$this->params['controller']; ?>/ajax/",
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                $(".table td:first-child").addClass('first');
                $(".btnReturn").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var name = $(this).attr('title');
                    $.ajax({
                        type: "GET",
                        url: "<?php echo $absolute_url.$this->params['controller']; ?>/returnPatient/",
                        data: "id=" + id,
                        beforeSend: function(){
                            $("#dialog").html('<p style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                        },
                        success: function(msg){
                            $("#dialog").html(msg);
                        }
                    });
                    $("#dialog").dialog({
                        title: name,
                        resizable: false,
                        modal: true,
                        width: '400',
                        height: '215',
                        buttons: {
                            Ok: function() {
                                $( this ).dialog( "close" );
                            }
                        }
                    });
                });
                $(".btnView").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var name = $(this).attr('title');                    
                    $.ajax({
                        type: "GET",
                        url: "<?php echo $absolute_url.$this->params['controller']; ?>/view/" + id,
                        data: "",
                        beforeSend: function(){
                            $("#dialog").html('<p style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                        },
                        success: function(msg){
                            $("#dialog").html(msg);
                        }
                    });
                    $("#dialog").dialog({
                        title: name + ' Information',
                        resizable: false,
                        modal: true,
                        width: '90%',
                        height: 400,
                        buttons: {
                            Ok: function() {
                                $( this ).dialog( "close" );
                            }
                        }
                    });
                });
                $(".btnDelete").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var name = $(this).attr('title');
                    $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Are you sure you want to delete ' + name + '?</p>');
                    $("#dialog").dialog({
                        title: 'Delete',
			resizable: false,
			modal: true,
                        width: 'auto',
                        height: 'auto',
			buttons: {
                            Delete: function() {
                                window.open("<?php echo $absolute_url.$this->params['controller']; ?>/delete/" + id,"_self");
                            },
                            Cancel: function() {
                                $( this ).dialog( "close" );
                            }
			}
                    });
                });
                $(".btnPrintCertificate").click(function(event){
                    event.preventDefault();
                    var id = $(this).attr('rel');
                    var name = $(this).attr('title');
                    var lang = $(this).attr('id');
                    $.ajax({
                        type: "GET",
                        url: "<?php echo $absolute_url.$this->params['controller']; ?>/printCertificate"+lang+"/" + id,
                        data: "",
                        beforeSend: function(){
                            $("#dialog").html('<p style="text-align:center"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" /></p>');
                        },
                        success: function(msg){
                             $("#dialog").html(msg);
                        }
                    });
                    $("#dialog").dialog({
                        title: name + ' Information',
                        resizable: false,
                        modal: true,
                        width: '90%',
                        height: 400,
                        buttons: {
                            "<?php echo ACTION_PRINT; ?>": function() {
                                w=window.open();
                                w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
                                w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
                                w.document.write($("#dialog").html());
                                w.document.close();
                                w.print();
                                w.close();
                                $( this ).dialog( "close" );
                            }
                        }
                    });   
                });                               
                return sPre;
            },
            "aoColumnDefs": [
                { "sType": "numeric", "aTargets": [ 0 ] }
            ]
        });
    });
</script>
<h1 class="title"><?php __(MENU_PATIENT_MANAGEMENT_LIST);?></h1>
<div id="dynamic">
    <table id="example" class="table" cellspacing="0">
        <thead>
            <tr>
                <th class="first"><?php echo TABLE_NO; ?></th>
                <th><?php echo PATIENT_CODE; ?></th>
                <th><?php echo TABLE_NAME; ?></th>
                <th><?php echo TABLE_SEX; ?></th>
                <th><?php echo TABLE_AGE; ?></th>
                <th><?php echo TABLE_TELEPHONE; ?></th>                
                <th><?php echo ACTION_ACTION; ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="7" class="dataTables_empty"><?php echo TABLE_LOADING; ?></td>
            </tr>
        </tbody>
    </table>
</div>
<br />
<br />
<div class="buttons">
    <a href="<?php echo $this->base; ?>/<?php echo $this->params['controller']; ?>/add" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
        <?php echo MENU_PATIENT_MANAGEMENT_ADD; ?>
    </a>
</div>
<div id="dialog" title=""></div>