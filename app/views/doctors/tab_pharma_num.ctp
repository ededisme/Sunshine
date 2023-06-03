<?php
if (empty($treatment)) {
    echo GENERAL_NO_RECORD;
    exit();
}
?>
<?php $absolute_url = FULL_BASE_URL . Router::url("/", false); ?>
<script type="text/javascript">
    $(document).ready(function() {
        $("#pharma").accordion({
            collapsible: true,
            autoHeight: false,
            navigation: true,
            active: false
        });
        $(".btnDelete").click(function() {
            var id = $(this).attr('name');
            $("#dialog").dialog('option', 'title', 'Delete');
            $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Are you sure you want to delete prescription?</p>');
            $("#dialog").dialog({
                title: 'Delete',
                resizable: false,
                modal: true,
                width: 'auto',
                height: 'auto',
                buttons: {
                    Delete: function() {
                        $.ajax({
                            type: "GET",
                            url: "<?php echo $absolute_url . $this->params['controller']; ?>/removePharma/" + id,
                            data: "",
                            beforeSend: function() {
                                $("#dialog").dialog("close");
                            },
                            success: function(msg) {
                                idList = new Array();
                                $tabPharma = false;
                                $("#tabPharmaNum").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabPharmaNum/<?php echo $this->params['pass'][0]; ?>");
                            }
                        });
                    },
                    Cancel: function() {
                        $(this).dialog("close");
                    }
                }
            });
        });
    });
</script>
<div id="pharma">

    <?php foreach ($treatment as $record): ?>
        <h3>                
            <a href="#">                        
                <?php echo date('d/m/Y H:i:s', strtotime($record['Treatment']['created'])); ?>
                <?php
                $queryQueueStatus = mysql_query("SELECT status FROM queues WHERE id=" . $record['Treatment']['queue_id']);
                $dataQueueStatus = mysql_fetch_array($queryQueueStatus);
                if ($dataQueueStatus['status'] != 3) {?>
                    <div style="float:right;"><img alt="" src="<?php echo $this->webroot; ?>img/action/delete.png" class="btnDelete" name="<?php echo $record['Treatment']['id']; ?>" onmouseover="Tip('<?php echo ACTION_DELETE; ?>')" /></div>
                 <?php } ?>            
                <div style="float:right;padding-right: 10px"><img alt="" src="<?php echo $this->webroot; ?>img/button/printer.png" class="btnPrint" onclick="location.href = '<?php echo $this->base; ?>/doctors/print_treatment/<?php echo $this->params['pass'][0] . '/' . $record['Treatment']['id'] ?>';"  name="<?php echo $record['Treatment']['id']; ?>" onmouseover="Tip('<?php echo ACTION_PRINT; ?>')" /></div>
            </a>                        
        </h3>    
        <div>
            <table class="table" cellspacing="0">
                <tr>
                    <th class="first"><?php echo TABLE_NO; ?></th>
                    <th><?php echo DRUG_COMMERCIAL_NAME; ?></th>                    
                    <th><?php echo GENERAL_TYPE; ?></th>                    
                    <th><?php echo GENERAL_QTY; ?></th>
                    <th  style="text-align: center"><?php __(GENERAL_NUMBER); ?></th>
                    <th style="text-align: center"><?php __(GENERAL_MORNING); ?></th>
                    <th style="text-align: center"><?php __(GENERAL_AFTERNOON); ?></th>
                    <th style="text-align: center"><?php __(GENERAL_EVENING); ?></th>
                    <th style="text-align: center"><?php __(GENERAL_NIGHT); ?></th>
                    <th><?php echo DRUG_NOTE; ?></th>                    
                </tr>
                <?php
                $index = 1;
                $type = "";
                $query = mysql_query("SELECT * FROM treatment_details WHERE treatment_id=" . $record['Treatment']['id']);
                while ($data = mysql_fetch_array($query)) {
                    $query_drug = mysql_query("   SELECT commercial_name,mu.*
                                            FROM grand_stocks g
                                                INNER JOIN sale_stocks s ON g.id=s.grand_stock_id                                                                                                
                                                INNER JOIN medicine_units mu ON g.medicine_unit_id=mu.id
                                            WHERE s.id=" . $data['sale_stock_id']);
                    $data_drug = mysql_fetch_array($query_drug);

                    if ($data_drug['medicine_type'] == "Injection") {
                        if ($data_drug['flacont'] == 0) {
                            $type = " " . MEASURE_AMPOULE;
                        } else {
                            $type = " " . MEASURE_FLACON;
                        }
                    } else if ($data_drug['medicine_type'] == "Tablet") {
                        $type = " " . MEASURE_TABLET;
                    } else if ($data_drug['medicine_type'] == "Capsule") {
                        $type = " " . MEASURE_CAPSULE;
                    } else if ($data_drug['medicine_type'] == "Powder") {
                        $type = " " . MEASURE_POWDER;
                    } else if ($data_drug['medicine_type'] == "Syrup") {
                        $type = " " . MEASURE_WATER;
                    } else if ($data_drug['medicine_type'] == "Cream") {
                        $type = " tubes";
                    } else if ($data_drug['medicine_type'] == "jel") {
                        $type = " tubes";
                    } else if ($data_drug['medicine_type'] == "form") {
                        $type = " tubes";
                    } else if ($data_drug['medicine_type'] == "cleaningBar") {
                        $type = " tubes";
                    } else if ($data_drug['medicine_type'] == "sprite") {
                        $type = " tubes";
                    } else if ($data_drug['medicine_type'] == "ointment") {
                        $type = " tubes";
                    } else if ($data_drug['medicine_type'] == "shampoo") {
                        $type = " tubes";
                    } else if ($data_drug['medicine_type'] == "lotion") {
                        $type = " tubes";
                    } else if ($data_drug['medicine_type'] == "stick") {
                        $type = " tubes";
                    } else if ($data_drug['medicine_type'] == "Liquid") {
                        $type = " amp";
                    } else if ($data_drug['medicine_type'] == "Other") {
                        if ($data_drug['flacont'] == 0) {
                            $type = " no";
                        } else {
                            $type = " fla";
                        }
                    } else if ($data_drug['medicine_type'] == "Suppository") {
                        if ($data_drug['suppository'] == 0) {
                            $type = " unidoses";
                        } else {
                            $type = " sup";
                        }
                    }
                    ?>
                    <tr>
                        <td class="first"><?php echo $index++; ?></td>
                        <td><?php echo $data_drug['commercial_name']; ?></td>                        
                        <td><?php echo $data_drug['medicine_type']; ?></td>                        
                        <td><?php echo $data['amount'] . ' ' . $type; ?></td>
                        <td><?php echo $data['num_day']; ?></td>
                        <td><?php echo $data['morning']; ?></td>
                        <td><?php echo $data['afternoon']; ?></td>
                        <td><?php echo $data['evening']; ?></td>
                        <td><?php echo $data['night']; ?></td>
                        <td><?php echo $data['note']; ?></td>
                    </tr>
    <?php } ?>
            </table>
        </div>
<?php endforeach; ?>
</div>
<div id="dialog" title=""></div>