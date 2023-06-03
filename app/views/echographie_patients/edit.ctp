<?php
echo $this->element('prevent_multiple_submit');
$absolute_url = FULL_BASE_URL . Router::url("/", false);
echo $javascript->link('uninums.min');
?>
<script type="text/javascript">
    $(document).ready(function(){
        // Prevent Key Enter
        preventKeyEnter();
        $("#EchographiePatientEditForm").validationEngine();
        $("#EchographiePatientEditForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSaveEchographiePatient").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $(".btnBackEchographiePatient").click();
                // alert message
                $("#dialog").html('<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>'+result+'</p>');
                $("#dialog").dialog({
                    title: '<?php echo DIALOG_INFORMATION; ?>',
                    resizable: false,
                    modal: true,
                    width: 'auto',
                    height: 'auto',
                    open: function(event, ui){
                        $(".ui-dialog-buttonpane").show();
                    },
                    buttons: {
                        '<?php echo ACTION_CLOSE; ?>': function() {
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
        
        $("#EchographiePatientEchographieId").change(function(){
            var category = $(this).val();
            var desc=$("#EchographiePatientEchographieId").find(':selected').attr('description');
            if(category==""){
                $(".box").hide();            
            }else{
                $(".box").hide();
                $(".box[rel=" + category + "]").show();
            }            
        });
        
        $(".btnBackEchographiePatient").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableEchographiePatient.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });
    // end document
    
    // Editor English
    CKEDITOR.replace( 'des', {
            allowedContent:
                    'h1 h2 h3 p pre[align]; ' +
                    'blockquote code kbd samp var del ins cite q b i u strike ul ol li hr table tbody tr td th caption; ' +
                    'img[!src,alt,align,width,height]; font[!face]; font[!family]; font[!color]; font[!size]; font{!background-color}; a[!href]; a[!name]',
            coreStyles_bold: { element: 'b' },
            coreStyles_italic: { element: 'i' },
            coreStyles_underline: { element: 'u' },
            coreStyles_strike: { element: 'strike' },
            font_style: {
                    element: 'font',
                    attributes: { 'face': '#(family)' }
            },
            fontSize_sizes: 'xx-small/1;x-small/2;small/3;medium/4;large/5;x-large/6;xx-large/7',
            fontSize_style: {
                    element: 'font',
                    attributes: { 'size': '#(size)' }
            },
            colorButton_foreStyle: {
                    element: 'font',
                    attributes: { 'color': '#(color)' }
            },

            colorButton_backStyle: {
                    element: 'font',
                    styles: { 'background-color': '#(color)' }
            },
            stylesSet: [
                    { name: 'Computer Code', element: 'code' },
                    { name: 'Keyboard Phrase', element: 'kbd' },
                    { name: 'Sample Text', element: 'samp' },
                    { name: 'Variable', element: 'var' },
                    { name: 'Deleted Text', element: 'del' },
                    { name: 'Inserted Text', element: 'ins' },
                    { name: 'Cited Work', element: 'cite' },
                    { name: 'Inline Quotation', element: 'q' }
            ],
            uiColor: '#CCEAEE',
            on: {
                    pluginsLoaded: configureTransformations,
                    loaded: configureHtmlWriter
            }
    });
</script>
<div style="padding: 5px;border: 1px dashed #3C69AD;">
    <div class="buttons">
        <a href="" class="positive btnBackEchographiePatient">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('EchographiePatient',array('enctype' => 'multipart/form-data')); ?>
<?php echo $this->Form->input('id'); ?>
<?php
foreach ($patient as $patient):  ?>
<fieldset style="border: 1px dashed #3C69AD;">
    <legend style="background: #CCCCCC; font-weight: bold;"><?php __(MENU_PATIENT_MANAGEMENT_INFO); ?></legend>
    <div>
        <table class="info" style="width: 100%;">
            <tr>
                <th><?php echo PATIENT_CODE; ?></th>
                <td><?php echo $patient['Patient']['patient_code']; ?></td>
                <th><?php echo PATIENT_NAME; ?></th>
                <td><?php echo $patient['Patient']['patient_name']; ?></td>  
                <th><?php echo TABLE_AGE.'/'.TABLE_DOB;?> </th>
                <td>
                    <?php 
                    $then_ts = strtotime($patient['Patient']['dob']);
                    $then_year = date('Y', $then_ts);
                    $age = date('Y') - $then_year;
                    if (strtotime('+' . $age . ' years', $then_ts) > time())
                        $age--;

                    if ($age == 0) {
                        $then_year = date('m', $then_ts);
                        $month = date('m') - $then_year;
                        if (strtotime('+' . $month . ' month', $then_ts) > time())
                            $month--;
                        echo $month . ' ' . GENERAL_MONTH;
                    }else {
                        echo $age . ' ' . GENERAL_YEAR_OLD;
                    }
                    ?>
                </td>
                <th><?php echo TABLE_SEX; ?></th>
                <td>
                    <?php 
                        if($patient['Patient']['sex']=="M"){
                            echo 'Male';
                        }else{
                            echo 'Female';
                        }                        
                    ?>
                </td>
            </tr>
            <tr>
                <th><?php echo TABLE_NATIONALITY;?> </th>
                <td>
                    <?php
                    if ($patient['Patient']['patient_group_id'] != "") {
                        $query = mysql_query("SELECT name FROM patient_groups WHERE id=" . $patient['Patient']['patient_group_id']);
                        while ($row = mysql_fetch_array($query)) {
                            if ($patient['Patient']['patient_group_id'] == 1) {
                                echo $row['name'];
                            } else {
                                $queryNationality = mysql_query("SELECT name FROM nationalities WHERE id=".$patient['Patient']['nationality']);
                                while ($result = mysql_fetch_array($queryNationality)) {
                                    echo $row['name'] . '&nbsp;&nbsp;(' . $result['name'] . ')';
                                }
                            }
                        }
                    } else {
                        echo $patient['Nationality']['name'];
                    }
                    ?>
                </td>
                <th><?php echo TABLE_TELEPHONE;?> </th>
                <td><?php echo $patient['Patient']['telephone']; ?></td>
            </tr>
            <tr>
                <th><?php echo TABLE_ADDRESS;?> </th>
                <td colspan="5">
                    <?php 
                    if($patient['Patient']['address']!=""){
                        echo $patient['Patient']['address'];
                    }
                    if($patient['Patient']['location_id']!=""){
                        $query = mysql_query("SELECT name FROM patient_locations WHERE id=".$patient['Patient']['location_id']);
                        while ($row = mysql_fetch_array($query)) {
                            if($patient['Patient']['address']!=""){
                                echo ', ';
                            }
                            echo $row['name'];                
                        }
                    }
                    ?>
                </td>
            </tr>
            <tr>
                
            </tr>
        </table>
    </div>
</fieldset><br> 

<fieldset style="border: 1px dashed #3C69AD;">
    <legend style="background: #EF0931; font-weight: bold;"><?php __(GENERAL_REQUEST); ?></legend>
    <table style="width: 100%;" cellspacing="0">
        <tr>
            <td>
                <?php 
                    $queryDataFromDoctor=  mysql_query("SELECT esreq.*,esreq.id as id FROM echo_service_requests as esreq "
                            . "INNER JOIN other_service_requests as osreq ON osreq.id=esreq.other_service_request_id "
                            . "INNER JOIN queued_doctors as qd ON qd.id=osreq.queued_doctor_id "
                            . "INNER JOIN queues as q ON q.id=qd.queue_id WHERE osreq.is_active=1 AND queue_id=".$patient['EchographiePatient']['queue_id']);
                    $dataRequest=  mysql_fetch_array($queryDataFromDoctor);
                    echo $dataRequest['echo_description'];
                ?>
                <input type="hidden" value="<?php echo $dataRequest['id']; ?>" name="data[EchographiePatientRequest][id]">
                <input type="hidden" value="<?php echo $patient['Queue']['id']; ?>" name="data[Queue][id]">
            </td>
        </tr>
    </table>      
</fieldset>
<br/>
<fieldset style="border: 1px dashed #3C69AD;">
    <legend><?php __(MENU_ECHO_SERVICE_INFO); ?></legend>
    <table class="defaultTable">
        <input type="hidden" value="<?php echo $patient['Patient']['id']?>" name="patient_id"/>  
        <tr>
            <td style="width: 35%; vertical-align: top;">
                <fieldset>
                    <table class="defualtTable">
                        <tr>
                            <td>    
                                <?php
                                foreach ($echographiePatients as $echographiePatient):
                                    $echographieId = $echographiePatient['EchographiePatient']['id'];
                                    $echographieCatId = $echographiePatient['EchographiePatient']['echography_infom_id'];
                                    $inicationId = $echographiePatient['EchographiePatient']['indication_id'];
                                    $description = $echographiePatient['EchographiePatient']['description'];
                                    $doctor_name = $echographiePatient['EchographiePatient']['doctor_name'];
                                    $ddr = $echographiePatient['EchographiePatient']['ddr'];
                                    $form_child = $echographiePatient['EchographiePatient']['form_child'];
                                    $num_child = $echographiePatient['EchographiePatient']['num_child'];
                                    $healthy_child = $echographiePatient['EchographiePatient']['healthy_child'];
                                    $sex_child = $echographiePatient['EchographiePatient']['sex_child'];
                                    $location_sok = $echographiePatient['EchographiePatient']['location_sok'];
                                    $teok_plos = $echographiePatient['EchographiePatient']['teok_plos'];
                                    $weight_child = $echographiePatient['EchographiePatient']['weight_child'];
                                    $week_child = $echographiePatient['EchographiePatient']['week_child'];
                                    $day_child = $echographiePatient['EchographiePatient']['day_child'];
                                    $born_date = $echographiePatient['EchographiePatient']['born_date'];
                                endforeach;
                                ?>      
                                <div class="input select">
                                    <input type="hidden" value="<?php echo $echographiePatient['EchographiePatient']['id'];?>" name="data[EchographiePatient][id]">
                                    <label for="EchographiePatientEchographieId">Echographie<span class="red">&nbsp;&nbsp;*</span>:&nbsp;&nbsp;</label>
                                    <select id="EchographiePatientEchographieId" class="validate[required]" name="data[EchographiePatient][echography_infom_id]" style="width:307px;">                            
                                        <option description="" value="0" <?php echo ($echographieCatId == 0) ? 'selected="selected"' : ''; ?>>Please Select</option>
                                        <?php
                                        foreach ($echographies as $echographie):
                                            ?>
                                            <option description="<?php echo $echographie['EchographyInfom']['description']; ?>" value="<?php echo $echographie['EchographyInfom']['id']; ?>" <?php echo ($echographie['EchographyInfom']['id'] == $echographieCatId) ? 'selected="selected"' : ''; ?>>
                                                <?php echo $echographie['EchographyInfom']['name']; ?>
                                            </option>                                                                                   
                                            <?php
                                        endforeach;
                                        ?>
                                    </select>                                            
                                </div>
                            </td>
                        <input type="hidden" value="<?php echo $echographieId ?>" name="echographiePatientId"/>
                        </tr>
                        <tr>
                            <td>
                                <?php echo $this->Form->input('doctor_name', array('label' => 'Examen par' . '<span class="red">&nbsp;&nbsp;*</span>:&nbsp;&nbsp;', 'class' => 'validate[required]', 'value' => $doctor_name,'style'=>'width: 295px; margin-left:6px;')); ?>
                            </td>                                            
                        </tr>  
                        <tr>
                            <td>                                            
                                <div class="input select">
                                    <label for="EchographiePatientEchographieId">Indication<span class="red">&nbsp;&nbsp;*</span>:&nbsp;&nbsp;</label>
                                    <select id="ImageryIndication" class="validate[required]" name="data[EchographiePatient][Indication_id]" style='width:307px;margin-left:17px;'>                            
                                        <option value="0" <?php echo ($inicationId == 0) ? 'selected="selected"' : ''; ?>>Please Select</option>
                                        <?php
                                        foreach ($indications as $indication):
                                            ?>
                                            <option value="<?php echo $indication['Indication']['id']; ?>" <?php echo ($indication['Indication']['id'] == $inicationId) ? 'selected="selected"' : ''; ?>>
                                                <?php echo $indication['Indication']['name']; ?>
                                            </option>                                                                                   
                                            <?php
                                        endforeach;
                                        ?>
                                    </select>                                            
                                </div>

                            </td>                                            
                        </tr>
                        <tr>
                            <td>                                                
                                <?php echo $this->Form->input('ddr', array('label' => 'D.D.R' . '<span>&nbsp;&nbsp;:&nbsp;&nbsp;</span>', 'value' => $ddr,'style'=>'margin-left:44px; width: 295px;')); ?>
                            </td>                                            
                        </tr> 
                    </table>                                                                      
                </fieldset>
            </td>  
            <td style="width: 65%; vertical-align: top;" rowspan="2">      
                <fieldset class="description" style="min-height: 480px; vertical-align: top;">
                    <div class="box" rel="<?php echo $echographie['EchographyInfom']['id'] ?>" style="width:95%;height: 458px;">
                        <?php
                            $id = $echographie['EchographyInfom']['id'];
                            echo $this->Form->input('description', array('name'=>"data[EchographiePatient][description]",'value' => $description, 'id' => 'des', 'class' => '', 'type' => 'textarea', 'label' => 'R E S U L T A T', 'style' => 'width:730px; height: 448px;'));                          
                        ?> 
                    </div>
                </fieldset>
            </td>
            <!--For big child-->
        </tr>                         
        <td valign="top">
            <fieldset>   
                <table class="defualtTable">
                    <tr>
                        <td colspan="3">    
                            <?php echo $this->Form->input('form_child', array('label' => 'ទំរង់កូន' . '<span>&nbsp;&nbsp;:&nbsp;&nbsp;</span>', 'value' => $form_child,'style'=>'width: 295px; margin-left:24px;')); ?>
                        </td>                                            
                    </tr>
                    <tr>
                        <td colspan="3">
                            <label for="EchographiePatientNumChild">ចំនួនកូន<span>&nbsp;&nbsp;:&nbsp;&nbsp;</span></label>
                            <select id="ImageryNumChild" name="data[EchographiePatient][num_child]" style='width:307px;margin-left:16px;'>
                                <option value="" <?php if ($num_child == "")
                                echo "selected"; ?>>Please Select</option>
                                <option value="០១" <?php if ($num_child == "០១")
                                echo "selected"; ?>>០១</option>
                                <option value="០២" <?php if ($num_child == "០២")
                                echo "selected"; ?>>០២</option>
                                <option value="០៣" <?php if ($num_child == "០៣")
                                echo "selected"; ?>>០៣</option>
                                <option value="០៤" <?php if ($num_child == "០៤")
                                echo "selected"; ?>>០៤</option>
                                <option value="០៥" <?php if ($num_child == "០៥")
                                echo "selected"; ?>>០៥</option>
                                <option value="០៦" <?php if ($num_child == "០៦")
                                echo "selected"; ?>>០៦</option>
                            </select>
                        </td>                                            
                    </tr>  
                    <tr>
                        <td colspan="3">
                            <?php echo $this->Form->input('healthy_child', array('label' => 'សុខភាពកូន' . '<span>&nbsp;&nbsp;:&nbsp;&nbsp;</span>', 'value' => $healthy_child, 'style' => 'width: 295px;')); ?>
                        </td>                                            
                    </tr>
                    <tr>
                        <td colspan="3">                                        
                            <label for="EchographiePatientSexChild">ភេទកូន<span>&nbsp;&nbsp;:&nbsp;&nbsp;</span></label>
                            <select id="ImagerySexChild" name="data[EchographiePatient][sex_child]" style='width:307px;margin-left:20px;'>
                                <option value="" <?php if ($sex_child == "")
                                    echo "selected"; ?>>Please Select</option>
                                    <option value="M" <?php if ($sex_child == "M")
                                    echo "selected"; ?>>Male</option>
                                    <option value="F" <?php if ($sex_child == "F")
                                    echo "selected"; ?>>Female</option>
                                     <option value="M+F" <?php if ($sex_child == "M+F")
                                    echo "selected"; ?>>Male+Female</option>
                                    <option value="F+F" <?php if ($sex_child == "F+F")
                                    echo "selected"; ?>>Female+Female</option>                                               
                                    <option value="M+M" <?php if ($sex_child == "M+M")
                                    echo "selected"; ?>>Male+Male</option>
                            </select>
                        </td>                                            
                    </tr>
                    <tr>
                        <td  colspan="3">
                            <?php echo $this->Form->input('teok_plos', array('label' => 'ទឹកភ្លោះ' . '<span>&nbsp;&nbsp;:&nbsp;&nbsp;</span>', 'value' => $teok_plos,'style'=>'width: 295px; margin-left:21px;')); ?>
                        </td>                                            
                    </tr>
                    <tr>
                        <td colspan="3"> 
                            <?php echo $this->Form->input('location_sok', array('label' => 'ទីតាំងសុក' . '<span>&nbsp;&nbsp;:&nbsp;&nbsp;</span>', 'value' => $location_sok,'style'=>'width: 295px; margin-left:9px;')); ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 80%" colspan="2">
                            <?php echo $this->Form->input('weight_child', array('style' => 'width:30%;margin-left:18px;', 'label' => 'ទំងន់កូន' . '<span>&nbsp;&nbsp;:&nbsp;&nbsp;</span>', 'value' => $weight_child)); ?>
                        </td>     
                        <td style="width: 80%">ក្រាម</td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo $this->Form->input('year_child', array('style' => 'width:41%;margin-left:18px;', 'label' => 'អាយុកូន' . '<span>&nbsp;&nbsp;:&nbsp;&nbsp;</span>', 'value' => $week_child)); ?>
                        </td>     
                        <td style="width: 20%">សបា្តហ៍</td>
                        <td style="width: 40%"><?php echo $this->Form->input('day_child', array('style' => 'width:87%;', 'label' => false, 'value' => $day_child)); ?></td>
                        <td style="width: 0%">ថ្ងៃ</td>
                    </tr>

                    <tr>
                        <td colspan="3">
                            <?php echo $this->Form->input('born_date', array('label' => 'គ្រប់ខែថ្ងៃ' . '<span>&nbsp;&nbsp;:&nbsp;&nbsp;</span>', 'value' => $born_date,'style'=>'width: 295px; margin-left:14px;')); ?>
                        </td>                                   
                    </tr>
                </table>
            </fieldset>
        </td>                            
        </tr>
    </table>  
</fieldset>
<br />
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <span class="txtSaveEchographiePatient"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<div style="clear: both;"></div>
<?php endforeach; ?>
<?php echo $this->Form->end(); ?>