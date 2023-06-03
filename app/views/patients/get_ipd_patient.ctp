<?php if(!empty($patients)){ ?>
    <script type="text/javascript">
        $("tr").click(function(){
            $(this).find(":radio").attr('checked', true);
        });
    </script>
    <table class="table">
        <tr>
            <th class="first"></th>
            <th><?php echo TABLE_NO; ?></th>
            <th><?php echo PATIENT_CODE; ?></th>
            <th><?php echo PATIENT_NAME; ?></th>            
            <th><?php echo TABLE_TELEPHONE; ?></th>
        </tr>
        <?php
        $i = 1;
        foreach($patients as $row){?>
        <tr>
            <td class="first">
                <input type="radio" name="status" class="status" value="<?php echo $row['patients']['id'].','.$row['patients']['patient_code'].','.$row['patients']['patient_name'].','.$row['patients']['telephone'];?>">
            </td>
            <td>
                <?php echo $i++; ?>
            </td>
            <td>
                <?php echo $row['patients']['patient_code']; ?>
            </td>
            <td>
                <?php echo $row['patients']['patient_name']; ?>
            </td>            
            <td>
                <?php echo $row['patients']['telephone']; ?>
            </td>
        </tr>
        <?php } ?>
    </table>
<?php }else{
    echo GENERAL_NO_RECORD;
} ?>