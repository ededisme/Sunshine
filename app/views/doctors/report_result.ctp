<?php $absolute_url  = FULL_BASE_URL . Router::url("/", false); ?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/pipeline.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(".table td:first-child").addClass('first');
        $("#example").dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo $absolute_url.$this->params['controller']; ?>/reportAjax/<?php echo implode(',', $_POST); ?>",
            "fnServerData": fnDataTablesPipeline,
            "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
                $(".table td:first-child").addClass('first');
                return sPre;
            },
            "aoColumnDefs": [
                { "sType": "numeric", "aTargets": [ 0 ] }
            ]
        });
        $("#btnPrint").click(function(){
            $(".dataTables_length").hide();
            $(".dataTables_filter").hide();
            $(".dataTables_paginate").hide();
            w=window.open();
            w.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
            w.document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/style.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/table.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/button.css" /><link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>css/print.css" media="print" />');
            w.document.write($(".print_area").html());
            w.document.close();
            w.print();
            w.close();
            $(".dataTables_length").show();
            $(".dataTables_filter").show();
            $(".dataTables_paginate").show();
        });

    });
</script>
<p style="text-align: center;"><img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" id="loading" style="display: none;" /></p>
<div class="print_area">
    <?php
    $msg = '<b style="font-size: 18px;">' . MENU_REPORT . '</b><br /><br />';
    if($_POST['due_date']=='') {
        if($_POST['date_from']!='') {
            $msg .= REPORT_FROM.': '.$_POST['date_from'];
        }
        if($_POST['date_to']!='') {
            $msg .= ' '.REPORT_TO.': '.$_POST['date_to'];
        }
    }else {
        $msg .= $_POST['due_date'];
    }
    echo $this->element('/prints/header',array('msg'=>$msg));
    ?>
    <br />
    <div id="dynamic">
        <table id="example" class="table" cellspacing="0">
            <thead>
                <tr>
                    <th class="first"><?php echo TABLE_NO; ?></th>
                    <th><?php echo TABLE_DATE; ?></th>
                    <th><?php echo PATIENT_CODE; ?></th>
                    <th><?php echo PATIENT_NAME; ?></th>
                    <th><?php echo TABLE_SEX; ?></th>
                    <th><?php echo TABLE_TELEPHONE; ?></th>                    
                    <th><?php echo PATIENT_TYPE; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="7" class="dataTables_empty"><?php echo TABLE_LOADING; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div style="clear: both;"></div>
<br />
<div class="buttons">
    <button type="button" id="btnPrint" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/printer.png" alt=""/>
        <?php echo ACTION_PRINT; ?>
    </button>
</div>
<div style="clear: both;"></div>