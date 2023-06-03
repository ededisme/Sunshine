<?php
$img = '';
if(!empty($logo)){
    $img = $logo;
}
?>
<table style="width: 100%;">
    <tr>
        <td style="text-align: center;">     
            <img alt="" src="<?php echo $this->webroot; ?>public/company_photo/<?php echo $img; ?>" style="width: 100%;" />
            <h2 style="font-size: 18px;line-height: 25px">
                <?php 
                    if(!empty($title)) {
                        echo $title;
                    } 
                ?>
            </h2>
            <p style="text-align: center;">
                <span style="font-size:14px; font-weight: bold;">
                    <?php
                        if(!empty($address)){
                            echo nl2br($address);
                        }
                    ?>
                    <br/>
                    <?php
                         if(!empty($address)){
                             echo nl2br($address);
                         }
                    ?>
                    <br/>
                    <?php
                         if(!empty($telephone)){
                             echo $telephone;
                         }
                    ?>
                    <br/>
                    <?php
                         if(!empty($mail)){
                              echo "Email:". $mail;
                         }
                    ?>
                </span>
            </p>                
        </td>
    </tr>
</table>