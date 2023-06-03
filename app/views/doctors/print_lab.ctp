<style type="text/css">
    table tr td {
        font-size: 12px !important;
    }
    #labo {
        color: #03bbc8
    }
</style>
<?php 
include('includes/function.php'); 
?>

<div id="labo">
    <table style="width: 100%;">
        <tr>
             <td valign="top" style="text-align: center; width: 20%;">  
                 <img style="width: 100%" src= '<?php echo $this->webroot;?>img/logo_s.png' alt=''>
             </td>
             <td style="text-align: center; width: 60%;" valign="top">      
                     <center> 
                         <h2 style="color: #083181; text-align: center; font-size: 24px; line-height:18px; font-family: 'Khmer OS Muol ';"><?php echo $this->data['Branch']['name_other'] ?></h2>
                         <h2 style="color: #083181; text-align: center; font-size: 18px; line-height:18px; font-family: 'Khmer OS Muol ';"><?php echo $this->data['Branch']['name'] ?></h2>
                         <h2 style="color: #03bbc8; text-align: center; font-size: 12px; line-height:18px; font-family: 'Khmer OS Borkor';"><?php echo $this->data['Branch']['address'];  ?> </h2>
                         <h2 style="color: #03bbc8; text-align: center; font-size: 12px; line-height:18px; font-family: 'Khmer OS Borkor';"><?php echo $this->data['Branch']['telephone'] != '' ? 'Tel : ' . $this->data['Branch']['telephone']: '';  ?><?php echo $this->data['Branch']['email_address'] != '' ? ' , Email : ' . $this->data['Branch']['email_address']: '';  ?> </h2>
                     </center>
                 </td>
             <td valign="top" style="text-align: center; width: 20%;">                
             </td>
         </tr>
     </table>
    <fieldset>
    <table style="width:100%;font-size:12px;">       
        <tr>
            <td style="width:50%"><span style="white-space: nowrap;"><?php echo PATIENT_NAME . " : " .$patient['Patient']['patient_name']; ?></span></td>
            <td><span style="width:50% ; text-align: left"><?php echo REQUEST_BY_DOCTOR . " : ".$user['User']['first_name'].' '.$user['User']['last_name']; ?></span></td>
            
        </tr>
        <tr>
            <td><span style="white-space: nowrap;"><?php echo TABLE_AGE . " : "; ?>
                    <?php
                    echo getAgePatient($patient['Patient']['dob']);                    
                    ?>
                </span>
            </td> 
            <td style=""><span style="white-space: nowrap;"><?php echo OTHER_REQUESTED_DATE; ?> : <?php echo date("d/m/Y H:i:s", strtotime($labo[0]['Labo']['created'])); ?></span><span style="white-space: nowrap;margin-left: 10px">Phone :<?php echo $user['User']['telephone'] ?> </span></td>
        </tr>
    </table>
</fieldset>
    <?php
    $ind = 1;
    foreach ($labo as $historyLabo):
        ?>
        <div class="<?php echo $historyLabo['Labo']['id']; ?>">
            <?php
            $laboSelected = array();
            $queryLaboRequest = mysql_query("SELECT * FROM labo_requests WHERE is_active!=2 AND labo_id=" . $historyLabo['Labo']['id']);
            while ($resultLaboRequest = mysql_fetch_array($queryLaboRequest)) {
                $laboSelected[] = $resultLaboRequest['labo_item_group_id'];
            }
            ?>
            <fieldset>
                <?php
                $count = 0;
                $index = 1;
                echo '<div class="column" style="float: left;padding: 11px;width: 30%;">';
                $query = mysql_query("SELECT * FROM labo_title_groups WHERE is_active!=2");
                while ($laboTitleGroups = mysql_fetch_array($query)) {
                    $subTitle = "";
                    if ($count == 3 || $count == 5) {
                        echo '</div><div class="column" style="float: left;padding: 11px;width: 30%;border-left: 1px solid #aaa;">';
                    }
                    $titleName = $laboTitleGroups['name'];
                    $titleId = $laboTitleGroups['id'];
                    $itemId = $laboTitleGroups['labo_item_group_id'];
                    $queryLaboGroup = mysql_query("SELECT LaboItemGroup.id, LaboItemGroup.labo_sub_title_group_id,LaboSubTitleGroup.name AS LaboSubTitleGroupName , LaboItemGroup.name, LaboItemGroup.price
                                                    FROM labo_title_groups AS LaboTitleGroup
                                                    INNER JOIN labo_item_groups AS LaboItemGroup ON LaboItemGroup.id
                                                    IN ($itemId)   
                                                    LEFT JOIN labo_sub_title_groups AS LaboSubTitleGroup ON LaboSubTitleGroup.id = LaboItemGroup.labo_sub_title_group_id
                                                    WHERE LaboTitleGroup.id = $titleId
                                                    ORDER BY LaboItemGroup.labo_sub_title_group_id
                                                ");

                    echo '<b style="font-size:12px"><u>' . $titleName . '</u></b>';
                    echo '<br />';
                    while ($result = mysql_fetch_array($queryLaboGroup)) {
                        if ($subTitle != $result['labo_sub_title_group_id']) {                            
                            echo '<span style="font-size:12px">'.$result['LaboSubTitleGroupName'].'</span>';
                        }
                        $checked = false;
                        if (in_array($result['id'], $laboSelected)) {
                            $checked = true;
                        }
                        if ($index < 10) {
                            $index = '0' . $index;
                        }
                        echo '<table class="defaultTable" width="100%"><tr><td style="font-size: 11px;">' . $index . '. </td><td style="font-size: 11px;">' . $this->Form->checkbox('', array('name' => 'data[LaboItemGroup][]', "value" => $result['id'], 'hiddenField' => false, 'checked' => $checked, 'id' => 'laboItemGroup_' . $result['id'])) . '</td><td style="white-space: nowrap;">' . '<label style="font-size:11px" for="laboItemGroup_' . $result['id'] . '">' . $result['name'] . '&nbsp;&nbsp;&nbsp;&nbsp;</label>' . '</td><td style="width: 80%;background: url(' . $this->webroot . 'img/bg_grey_dotted_h-line_3x1.png) repeat-x; background-position: 0 20px;"></td></table>';
                        $subTitle = $result['labo_sub_title_group_id'];
                        $index++;
                    }

                    $count++;
                }
                // FOR OTHER EXAMENS                
                echo '<b style=";font-size:12px"><u>AUTRES EXAMENS</u></b>';
                echo '<br />';
                for ($row = 0; $row < 4; $row++) {
                    echo '<table class="defaultTable" width="100%"><tr><td style="font-size: 11px;">' . $index++ . '. </td><td style="font-size: 11px;">' . $this->Form->checkbox('', array('name' => 'data[LaboItemGroup][]', 'hiddenField' => false)) . '</td><td style="white-space: nowrap;">' . '<label style="font-size:11px" for="laboItemGroup_' . $result['id'] . '">'.'.........................................</label>' . '</td><td style="width: 80%;background: url(' . $this->webroot . 'img/bg_grey_dotted_h-line_3x1.png) repeat-x; background-position: 0 20px;"></td></table>';
                }
                echo '</div>';
                ?>   
            </fieldset>                        
        </div>
        <?php $ind++;
    endforeach;
    ?>   
</div>