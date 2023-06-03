<?php
$img = '';
if(!empty($logo)){
    $img = $logo;
}
?>
<table style="width: 100%;">
    <tr>
        <td style="vertical-align: top; text-align: center; width: 20%;">                
            <img style="width: 100%;" alt="" src="<?php echo $this->webroot; ?>public/company_photo/<?php echo $img; ?>" /> 
        </td>
        <td style="text-align: center; width: 60%;">      
            <center> 
                 <h2 style="text-align: center; font-size: 18px; line-height:15px; font-family: 'Khmer OS Muol';">
                    <?php
                        if(!empty($titleKH)){ 
                            echo $titleKH;
                        }
                    ?>
                </h2>
                <h2 style="text-align: center; font-size: 18px; line-height:15px; font-family: 'Khmer OS Muol';">
                    <?php
                        if(!empty($title)){ 
                            echo $title;
                        }
                    ?>
                </h2>
                <p style="text-align: center; line-height: 20px;">
                    <span style="font-size: 12px;">
                        <?php
                            if(!empty($address)){
                                echo $address;
                            }
                        ?>
                        <?php
                            if(!empty($telephone)){
                                echo '<br>';
                                echo $telephone;
                            }
                        ?>
                        <?php
                             if(!empty($mail)){
                                 echo '<br>';
                                 echo $mail;
                             }
                         ?>
                    </span>
                </p>      
            </center>
        </td>
        <td style="width: 20%; vertical-align: top; text-align: right; white-space: nowrap; font-size: 30px; font-weight: bold;">
            <?php echo !empty($msg) ? $msg : ''; ?>
        </td>
    </tr>
</table>
