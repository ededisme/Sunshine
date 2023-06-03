<?php
$img = '';
if(!empty($logo)){
    $img = $logo;
}
?>
<table style="width: 100%;">
    <tr>
        <td style="text-align: center; width: 20%;">                
            <img style="width: 135px; height: 70px;" alt="" src="<?php echo $this->webroot; ?>public/company_photo/<?php echo $img; ?>" /> 
        </td>
        <td style="text-align: center; width: 80%;">      
            <center style="padding-right: 30px;"> 
                <h2 style="text-align: center; font-size: 18px; line-height:15px;">
                    <?php
                        if(!empty($title)){ 
                            echo $title;
                        }
                    ?>
                </h2>
                <h2 style="text-align: center; font-size: 18px; line-height:15px;">
                    <?php
                        if(!empty($titleKH)){ 
                            echo $titleKH;
                        }
                    ?>
                </h2>
                <p style="text-align: center; line-height: 20px;">
                    <span style="font-size:14px;">
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
        <td style="text-align: center; width: 20%;">                
            <img style="width: 135px; height: 70px;" alt="" src="<?php echo $this->webroot; ?>public/company_photo/<?php echo $img; ?>" /> 
        </td>
    </tr>
</table>