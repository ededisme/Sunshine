<div id="content_wrapper">
    <div class="child">
        <div class="title">
            <div class="inner_title"><?php __(GENERAL_QUEUE); ?></div>
        </div>
        <div class="body">
            <?php echo $this->element('queue_rec'); ?>
            <div id="update_panel"></div>
        </div>
    </div>
    
    
<!--    <div class="child">
        <div class="title">
            <div class="inner_title"><?php __(MENU_APPOINTMENT); ?></div>
        </div>
        <div class="body">
            <table cellpadding="0" cellspacing="0" class="defualtTable2">
                <?php
                if (!empty($appointments)):
                    ?>
                    <tr>
                        <th><?php echo TABLE_NO; ?></th>
                        <th><?php echo (DOCTOR_NAME); ?></th>
                        <th><?php echo (PATIENT_NAME); ?></th>
                        <th><?php echo (APPOINTMENT_DATE); ?></th>
                        <th><?php echo (APPOINTMENT_START_TIME); ?></th>
                        <th><?php echo (APPOINTMENT_END_TIME); ?></th>
                        <th><?php echo (GENERAL_NOTE); ?></th>
                    </tr>


                    <?php
                    $i = 0;
                    foreach ($appointments as $appointment):
                        $class = null;
                        if ($i++ % 2 == 0) {
                            $class = ' class="altrow"';
                        }
                        ?>
                        <tr<?php echo $class; ?>>
                            <td><?php echo $appointment['Appointment']['id']; ?>&nbsp;</td>
                            <td><?php echo $appointment['User']['login']; ?>&nbsp;</td>
                            <td><?php echo $appointment['Patient']['patient_name'] . ' ' . $appointment['Patient']['last_name']; ?>&nbsp;</td>
                            <td><?php echo $appointment['Appointment']['app_date']; ?>&nbsp;</td>
                            <td><?php echo $appointment['Appointment']['start_time']; ?>&nbsp;</td>
                            <td><?php echo $appointment['Appointment']['end_time']; ?>&nbsp;</td>
                            <td><?php echo $appointment['Appointment']['note']; ?>&nbsp;</td>
                        </tr>
                    <?php endforeach; ?>

                    <?php
                else:
                    echo GENERAL_NO_RECORD;
                endif;
                ?>
            </table>
        </div>
    </div>-->
</div>