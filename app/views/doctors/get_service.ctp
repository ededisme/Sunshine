<option value=""><?php echo SELECT_OPTION; ?></option>
<?php
foreach ($services as $service) {
    ?>
    <option value="<?php echo $service['Service']['id']; ?>"><?php echo $service['Service']['name']; ?></option>
<?php
}?>