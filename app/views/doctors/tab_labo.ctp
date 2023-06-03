<?php 
echo $this->element('prevent_multiple_submit');
$absolute_url = FULL_BASE_URL . Router::url("/", false); 
$tblName = "tbl123"; 
echo $javascript->link('jquery.form'); 
?>
<script type="text/javascript">
    $(document).ready(function(){
        $('.legend').hide();
        $("#LaboForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".loading").show();
            },
            success: function(result) {                
                $("#tabs3").tabs("select", 3);
                $("#tabLaboNum<?php echo $tblName;?>").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabLaboNum/<?php echo $this->params['pass'][0].'/'.$this->params['pass'][1]; ?>");
            }
        });
        setTimeout(function(){
            equalHeight($(".column"));
        },20000); 
    });
    
    function equalHeight(group) {
        var tallest = 0;
        group.each(function() {
            var thisHeight = $(this).height();
            if(thisHeight > tallest) {
                tallest = thisHeight;
            }
        });
        group.height(tallest);
    }   
</script>
<?php 

if($labo['Labo']['status'] == 2){
    echo GENERAL_DONE;exit();
} else if(empty($labo) || $labo['Labo']['status']==1){
?>
<?php echo $this->Form->create('Labo', array('id' => 'LaboForm', 'url' => '/doctors/laboRequestSave/' . $queueId)); ?>
<?php echo $form->hidden('queued_patient_id',array('name'=>'data[Queue][id]', 'value' => $queueId));?>
<?php echo $form->hidden('labo_id',array('name'=>'data[Labo][id]', 'value' => $labo['Labo']['id']));?>
<?php echo $form->hidden('queued_doctor_id',array('name'=>'data[QueueDoctor][queued_doctor_id]', 'value' => $queueDoctorId));?>
<table style="width: 100%; display: none;">
    <tr>
        <td style="width: 10%;"><label for="LaboChiefComplain"><?php echo TABLE_CHIEF_COMPLAIN; ?> :</label></td>
        <td>
            <?php echo $this->Form->input('chief_complain', array('label' => false, 'value' => $patientConsultations['PatientConsultation']['chief_complain'], 'type' => 'textarea', 'style' => 'width:99%; height:60px;')); ?>            
        </td>
    </tr>
    <tr>
        <td style="width: 10%;"><labe for="LaboDiagonist"><?php echo TABLE_DAIGNOSTIC; ?> :</label></td>
        <td>
            <?php echo $this->Form->input('daignostic', array('label' => false, 'value' => $patientConsultations['PatientConsultation']['daignostic'], 'type' => 'textarea', 'style' => 'width:99%; height:60px;')); ?>            
        </td>
    </tr>          
</table>
<br/>
<fieldset>  
    <?php 
        $count=0;
        $index=1;
        $index1 = 0 ; 
        echo '<div class="column" style="background-color: #FFFFFF; float: left;padding: 10px;width: 30%;">';
        foreach ($laboTitleGroup as $laboTitleGroups){
//            if($count==3 || $count==5){ 
//                echo '</div><div class="column" style="background-color: #FFFFFF; float: left;padding: 10px;width: 30%;border-left: 1px solid #aaa;">';
//                 
//            }
            $titleName = $laboTitleGroups['LaboTitleGroup']['name'];
            $titleId = $laboTitleGroups['LaboTitleGroup']['id'];
            $itemId = $laboTitleGroups['LaboTitleGroup']['labo_item_group_id'];
            $subTitle = "";
            $query = mysql_query("SELECT LaboItemGroup.id, LaboItemGroup.labo_sub_title_group_id,LaboSubTitleGroup.name AS LaboSubTitleGroupName , LaboItemGroup.name, LaboItemGroup.price
                                    FROM labo_title_groups AS LaboTitleGroup
                                    INNER JOIN labo_item_groups AS LaboItemGroup ON LaboItemGroup.id
                                    IN ($itemId)   
                                    LEFT JOIN labo_sub_title_groups AS LaboSubTitleGroup ON LaboSubTitleGroup.id = LaboItemGroup.labo_sub_title_group_id
                                    WHERE LaboTitleGroup.id = $titleId
                                    ORDER BY LaboItemGroup.labo_sub_title_group_id, LaboItemGroup.code
                                   ");
            
            echo '<b style="background-color: #FFFFFF; color:#000;font-size:16px"><u>'.$titleName.'</u></b>';
            echo '<br />';
            while ($result = mysql_fetch_array($query)) {
                if($subTitle != $result['labo_sub_title_group_id']){
                   echo  $result['LaboSubTitleGroupName'];
                }
                $checked = false;
                if (in_array($result['id'], $laboSelected)) {
                    $checked = true;
                }
                if($index<10){
                    $index = '0'.$index;
                }
                 echo '<table style="background-color: #FFFFFF;" class="defaultTable" width="100%">
                           <tr>
                                <td style="width:25px;">'.$index.'. </td>
                                <td style="width:10px;">' . $this->Form->checkbox('', array('name' => 'data[LaboItemGroup][]', 'checked' => $checked, "value"=>$result['id'], 'hiddenField' => false, 'id' => 'laboItemGroup_'.$result['id'])) . '</td>
                                <td style="white-space: nowrap;">'.'<label for="laboItemGroup_'.$result['id'].'">'.$result['name'].'&nbsp;&nbsp;&nbsp;&nbsp;</label>'.'</td>
                           </tr>
                       </table>';
                $subTitle = $result['labo_sub_title_group_id'];
                if($index == 40 || $index == 80){
                      echo '</div><div class="column" style="background-color: #FFFFFF; float: left;padding: 10px;width: 30%;border-left: 1px solid #aaa;">';
                }
                $index++;
                $index1++ ; 
            }
            $count++;
        }        
        echo '</div>';
    ?>   

    
</fieldset>
<br />
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <?php echo ACTION_SAVE; ?>
    </button>
    <img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" class="loading" style="display: none;" />
</div>
<div style="clear: both;"></div>

<?php 
}else {
    echo GENERAL_NO_RECORD;exit();
} 
?>
