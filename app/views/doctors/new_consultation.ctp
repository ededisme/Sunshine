<div id="content_wrapper">
    <?php echo $form->create('Exam', array('action' => 'doctor_save')); ?>
    <?php echo $form->hidden('patient_id', array('value' => $patient['QueuedPatient']['id'])); ?>
    <div class="child">
        <div class="title">
            <div class="inner_title"><?php __(PATIENT_INFO); ?></div>
        </div>
        <div class="body">
            <?php echo $this->element('patient_info', array('qpid' => $patient['QueuedPatient']['id'])); ?>
        </div>
    </div>
    <table>
        <tr>
            <td class="actions" colspan="3">
                <a href="<?php echo $this->base; ?>/doctors/consultation/<?php echo $patient['QueuedPatient']['id']; ?>"><?php __((!empty($isConsult) ? ACTION_EDIT . ' ' : '') . 'Consultation'); ?></a>
                <a href="<?php echo $this->base; ?>/doctors/paraclinic/<?php echo $patient['QueuedPatient']['id']; ?>"><?php __((!empty($isPara) || !empty($isLabo) ? ACTION_EDIT . ' ' : '') . 'Paraclinic'); ?></a>
                <a href="<?php echo $this->base; ?>/doctors/protocol/<?php echo $patient['QueuedPatient']['id']; ?>"><?php __((!empty($isProtocol) ? ACTION_EDIT . ' ' : '') . 'Protocol'); ?></a>
                <a href="<?php echo $this->base; ?>/doctors/treatment/<?php echo $patient['QueuedPatient']['id']; ?>"><?php __((!empty($isTreatment) ? ACTION_EDIT . ' ' : '') . 'Treatment'); ?></a>
                <a href="<?php echo $this->base; ?>/doctors/patientHistory/<?php echo $patient['Patient']['id']; ?>" ><?php __(GENERAL_HISTORY); ?></a>
            </td>
        </tr>
    </table>
    <?php echo $this->Form->end(); ?>
</div>