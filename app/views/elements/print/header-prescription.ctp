<?php
$img = '';
if(!empty($logo)){
    $img = $logo;
}
?>
<table style="width: 100%; margin-top: 40px">
    <tr>
        <td valign="top" style="text-align: center; width: 20%;">                
            <img style="width: 100%;" alt="" src="<?php echo $this->webroot; ?>public/company_photo/<?php echo $img; ?>" /> 
            <p style="text-align: left; font-size: 12px;<?php if(empty($telephone)){ echo 'display:none;';}?>"><?php echo $telephone; ?></p>
        </td>
        <td valign="top" style="text-align: center; width: 60%;">       
            <center style="display: none;"> 
                <h2 style="text-align: center; font-size: 12pt; line-height: 0px; font-family: 'Khmer OS Muol'; padding-top: 25px;"> 
                    <?php
                        if(!empty($titleKH)){ 
                            echo $titleKH;
                        }
                    ?>
                </h2>
                <h2 style="margin-top:20px;  text-align: center; font-size: 12pt; line-height: 12px; font-family: 'Khmer Unicode R1';"><?php
                        if(!empty($title)){ 
                            echo $title;
                        }
                    ?>
                </h2>
            </center>
        </td>
        <td valign="top" style="text-align: center; width: 20%;"></td>
    </tr>
    <tr style="<?php if(empty($msg)) { echo 'display:none;';}?>">
        <td colspan="3"><p style="color: #03bbc8; text-align: center; font-size: 16px; line-height:18px; font-family: 'Khmer OS Bokor';"><?php echo $msg; ?> </p></td>
    </tr>
</table>