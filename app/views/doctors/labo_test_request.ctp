<style type="tetext/css">
@font-face {
    font-family: "Kh-Battambang";
    font-style: normal;
    font-weight: normal;
    font-stretch: normal; src:  url('../fonts/Kh-Battambang.eot'), url("../fonts/Kh-Battambang.ttf") format("truetype"), url('../fonts/Kh-Battambang.woff') format('woff'),url('../fonts/Kh-Battambang.svg#webfonttEfFltbI') format('svg');
}


@font-face {
    font-family: 'LimerickLightRegular';
    src: url('<?php echo $this->webroot; ?>/fonts/limericklight/limerick-light-webfont.eot');
    src: url('<?php echo $this->webroot; ?>/fonts/limericklight/limerick-light-webfont.eot?#iefix') format('embedded-opentype'),
         url('<?php echo $this->webroot; ?>/fonts/limericklight/limerick-light-webfont.woff') format('woff'),
         url('<?php echo $this->webroot; ?>/fonts/limericklight/limerick-light-webfont.ttf') format('truetype'),
         url('<?php echo $this->webroot; ?>/fonts/limericklight/limerick-light-webfont.svg#LimerickLightRegular') format('svg');
    font-weight: normal;
    font-style: normal;

}
    /*
    ColorBox Core Style
    The following rules are the styles that are consistant between themes.
    Avoid changing this area to maintain compatability with future versions of ColorBox.
*/
#colorbox, #cboxOverlay, #cboxWrapper{position:absolute; top:0; left:0; z-index:9999; overflow:hidden;}
#cboxOverlay{position:fixed; width:100%; height:100%;}
#cboxMiddleLeft, #cboxBottomLeft{clear:left;}
#cboxContent{position:relative;}
#cboxLoadedContent{overflow:auto;}
#cboxLoadedContent iframe{display:block; width:100%; height:100%; border:0;}
#cboxTitle{margin:0;}
#cboxLoadingOverlay, #cboxLoadingGraphic{position:absolute; top:0; left:0; width:100%;}
#cboxPrevious, #cboxNext, #cboxClose, #cboxSlideshow{cursor:pointer;}

/*
    ColorBox example user style
    The following rules are ordered and tabbed in a way that represents the
    order/nesting of the generated HTML, so that the structure easier to understand.
*/
#cboxOverlay{background:#000;}

#colorbox{}
    #cboxContent{margin-top:20px;}
        #cboxLoadedContent{background:#000; padding:5px;}
        #cboxTitle{position:absolute; top:-20px; left:0; color:#ccc;}
        #cboxCurrent{position:absolute; top:-20px; right:0px; color:#ccc;}
        #cboxSlideshow{position:absolute; top:-20px; right:90px; color:#fff;}
        #cboxPrevious{position:absolute; top:50%; left:5px; margin-top:-32px; background:url(images/controls.png) top left no-repeat; width:28px; height:65px; text-indent:-9999px;}
        #cboxPrevious.hover{background-position:bottom left;}
        #cboxNext{position:absolute; top:50%; right:5px; margin-top:-32px; background:url(images/controls.png) top right no-repeat; width:28px; height:65px; text-indent:-9999px;}
        #cboxNext.hover{background-position:bottom right;}
        #cboxLoadingOverlay{background:#000;}
        #cboxLoadingGraphic{background:url(images/loading.gif) center center no-repeat;}
        #cboxClose{position:absolute; top:5px; right:5px; display:block; background:url(images/controls.png) top center no-repeat; width:38px; height:19px; text-indent:-9999px;}
        #cboxClose.hover{background-position:bottom center;}
</style>
<div style="background-color: #FFFFFF;padding:15px;font-size:11px;">
    <?php echo $form->create('Doctor',array('id'=>'form_labo_request','action'=>'labo_request_save'));?>
    <?php echo $form->hidden('queued_patient_id',array('name'=>'data[QueuedPatient][id]','value'=>$queuedPatientId));?>
    <?php echo $form->hidden('labo_id',array('name'=>'data[Labo][id]','value'=>$labo['Labo']['id']));?>
    <fieldset style="background-color: #FFFFFF;">
        
        <?php 
        $count=0;
        echo '<div class="column" style="float: left;padding: 10px;width: 30%;">';
        foreach ($laboTitleGroup as $laboTitleGroups){
            if($count==3 || $count==5){
                echo '</div><div class="column" style="float: left;padding: 10px;width: 30%;border-left: 1px solid #aaa;">';
            }
            $titleName = $laboTitleGroups['LaboTitleGroup']['name'];
            $titleId = $laboTitleGroups['LaboTitleGroup']['id'];
            $itemId = $laboTitleGroups['LaboTitleGroup']['labo_item_group_id'];
            $query = mysql_query("SELECT LaboItemGroup.id, LaboItemGroup.name,LaboItemGroup.price FROM labo_title_groups AS LaboTitleGroup
                                           INNER JOIN  labo_item_groups AS LaboItemGroup ON LaboItemGroup.id in ($itemId)                    
                                           WHERE LaboTitleGroup.id = $titleId ORDER BY LaboItemGroup.id");          
            
            echo '<b style="color:#000;font-size:16px"><u>'.$titleName.'</u></b>';
            echo '<br />';
            while ($result = mysql_fetch_array($query)) {     
                $checked = false;
                if(in_array($result['id'], $laboSelected)) {
                    $checked = true;
                }
                echo '<table class="defaultTable" width="100%"><tr><td>' . $this->Form->checkbox('', array('name' => 'data[LaboItemGroup][]', "value"=>$result['id'], 'hiddenField' => false, 'checked'=>$checked, 'id' => 'laboItemGroup_'.$result['id'])) . '</td><td style="white-space: nowrap;">'.'<label for="laboItemGroup_'.$result['id'].'">'.$result['name'].'</label>'.'</td><td style="width: 80%;background: url(' . $this->webroot . 'img/bg_grey_dotted_h-line_3x1.png) repeat-x; background-position: 0 20px;"></td><td style="white-space: nowrap;">$ '.$result['price'].'</td></table>';
            }
                                                              
            $count++;
        }        
        echo '</div>';
        ?>                                       
        

    </fieldset>
    <div class="buttons">
        <a href="<?php echo $this->base; ?>/labos/index" class="negative">
            <img src="<?php echo $this->webroot; ?>img/button/cross.png" alt=""/>
            <?php echo ACTION_CANCEL; ?>
        </a>
    </div>
<!--
    <?php echo $form->submit(__(ACTION_SAVE,true));?>-->
    <?php echo $form->end();?>
</div>
<script type="text/javascript">
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
    $(document).ready(function(){
        var f=$('#form_labo_request');
        f.submit(function(){
            var data = f.serialize();
            f.find('input:submit').attr('disabled','disabled').val('<?php __('Saving...');?>');
            $.post(f.attr('action'),data,function(){
                f.find('input:submit').attr('disabled','').val('<?php __('Done');?>');
                $.colorbox.close();
                window.setTimeout(function(){
                    f.find('input:submit').val('<?php __('Save');?>');
                   // window.location.href = '<?php echo $this->base; ?>/doctors/newConsultation/'+<?php echo $queuedPatientId; ?> +'';
                    window.location.href = "<?php echo $this->base; ?>/labos/index";
                },1000);
            })
            return false;
        });
        setTimeout(function(){
            equalHeight($(".column"));
        },500);
    });
</script>
<!--
<script type="text/javascript">
    $(document).ready(function(){
        var f=$('#form_labo_request');
        f.submit(function(){
            var data = f.serialize();
            f.find('input:submit').attr('disabled','disabled').val('<?php __('Saving...');?>');
            $.post(f.attr('action'),data,function(){
                f.find('input:submit').attr('disabled','').val('<?php __('Done');?>');
                $.colorbox.close();
                window.location = window.location
                window.setTimeout(function(){
                    f.find('input:submit').val('<?php __('Save');?>');
                },2000);
            })
            return false;
        });
    });
</script>-->