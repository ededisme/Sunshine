<?php
require_once("includes/function.php");
?>
<?php $absolute_url = FULL_BASE_URL . Router::url("/", false); ?>
<?php echo $javascript->link('jquery.form'); ?>
<style>
    .productPhoto {                
        cursor: pointer;
        transition: 0.7s;
    }

    .productPhoto:hover {opacity: 0.6;}

    /* The Modal (background) */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        padding-top: 100px; /* Location of the box */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }

    /* Modal Content (image) */
    .modal-content {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 700px;
    }

    /* Caption of Modal Image */
    .captionTxt {
        margin: auto;
        display: block;
        text-align: center;
        color: #fff;
        background: #597abf;
        padding: 5px;
        height: 20px;
    }

    /* Add Animation */
    .modal-content, .captionTxt {    
        -webkit-animation-name: zoom;
        -webkit-animation-duration: 0.6s;
        animation-name: zoom;
        animation-duration: 0.6s;
    }

    @-webkit-keyframes zoom {
        from {-webkit-transform:scale(0)} 
        to {-webkit-transform:scale(1)}
    }

    @keyframes zoom {
        from {transform:scale(0)} 
        to {transform:scale(1)}
    }

    /* The Close Button */
    .close {
        position: absolute;
        top: 100px;
        right: 35px;
        color: #f1f1f1;
        font-size: 40px;
        font-weight: bold;
        transition: 0.3s;
    }
    .close:hover,
    .close:focus {
        color: #bbb;
        text-decoration: none;
        cursor: pointer;
    }

    /* 100% Image Width on Smaller Screens */
    @media only screen and (max-width: 900px){
        .modal-content {
            width: 100%;
        }
    }
</style>
<?php $tblName = "tbl123"; ?>
<script>
    $(document).ready(function(){
        $("#PatientConsultationTabAttachFileForm").validationEngine();
        $("#PatientConsultationTabAttachFileForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".loading").show();
            },
            success: function(result) {
                $("#tabs3").tabs("select", <?php echo $viewId;?>);
                var viewId = <?php echo $viewId;?>;
                if(viewId == 10){
                    $("#tabAttachFiles<?php echo $tblName;?>").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabAttachFile/<?php echo $patientId;?>");
                }else if (viewId == 5){
                    $("#tabAttachFiles").load("<?php echo $absolute_url . $this->params['controller']; ?>/tabAttachFile/<?php echo $patientId; ?>");
                }
            }
        });
        
        $(".productPhoto").click(function(event){
            var rel = $(this).attr('rel');
            var modal = document.getElementById('myModal'+rel);
            var modalImg = document.getElementById("imgEcho"+rel);
            var captionText = document.getElementById("caption"+rel);

            modal.style.display = "block";
            modalImg.src = $(this).attr('src'); 
            captionText.innerHTML = $(this).attr('alt');

            // Get the <span> element that closes the modal
            var span = document.getElementsByClassName("close")[rel];

            // When the user clicks on <span> (x), close the modal
            span.onclick = function() {
                modal.style.display = "none";
            }

        });
        
        $('.btnDeleteImage').click(function(event){                                                
            var id = $(this).attr('rel');
            var name = $(this).attr('name');            
            $("#dialog").dialog('option', 'title', '<?php echo DIALOG_CONFIRMATION; ?>');
            $("#dialog").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo MESSAGE_CONFIRM_DELETE; ?> <b>' + name + '</b>?</p>');
            $("#dialog").dialog({
                title: '<?php echo DIALOG_CONFIRMATION; ?>',
                resizable: false,
                modal: true,
                width: 'auto',
                height: 'auto',
                open: function(event, ui){
                    $(".ui-dialog-buttonpane").show();
                },
                buttons: {
                    '<?php echo ACTION_DELETE; ?>': function() {
                        $.ajax({
                            type: "GET",
                            url: "<?php echo $this->base.'/patient_ipds'; ?>/deleteImageAttachFile/" + id +"/"+ name,
                            data: "",
                            beforeSend: function(){
                                $("#dialog").dialog("close");
                                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
                            },
                            success: function(result){
                                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                                $('#Image_Echo_History_' + id).fadeOut(800, function() {
                                    $(this).remove();
                                });
                            }
                        });
                        $(this).dialog("close")
                    },
                    '<?php echo ACTION_CANCEL; ?>': function() {
                        $(this).dialog("close");
                    }
                }
            });
        });
        
    });
            
    var nbRow = 1;
    $(function()
    {
        $('.btn-add').click(function(e)
        {            
            e.preventDefault();
            var controlForm = $('.controls'),
            currentEntry = $(this).parents('.entry:first'),
            newEntry = $(currentEntry.clone()).appendTo(controlForm);
            newEntry.find('img').each(function(){
            var id   = $(this).attr("data")+"_"+nbRow;
            $(this).attr("id",id);
            $(this).css("display","none");
        });
          
            newEntry.find('input').each(function(){
                  $(this).val('');
                var name = $(this).attr("data_name")+"[]";
                var id   = $(this).attr("data")+"_"+nbRow;
                var cls = $(this).attr("data");
                $(this).attr('rel', nbRow);
                $(this).attr('name', name);
                $(this).attr("id",id);
                $(this).attr("class",cls);
                controlForm.find('.entry:not(:first) .btn-add')
            .removeClass('btn-add').addClass('btn-remove')
            .html('<img style="width: 20px; height: 20px; cursor: pointer;" src="<?php echo $this->webroot; ?>img/button/trash.png" alt=""/>');
            remove();
            });
            
            
            nbRow++;
        });
        
    }); 
    function remove(){
        $(".btn-remove").unbind("click").click(function(){
           $(this).parents('.entry:last').remove();
            e.preventDefault();
            return false;
        });     
    }
    
    function PreviewImage(clicked_id) {
        var id = $("#"+clicked_id).attr('rel');
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById(clicked_id).files[0]);
        oFReader.onload = function(oFREvent) {            
            if(id!=""){
                document.getElementById("uploadPreview_"+id).src = oFREvent.target.result;
                $("#uploadPreview_"+id).css("display","block");
            }else{
                document.getElementById("uploadPreview").src = oFREvent.target.result;
                $("#uploadPreview").css("display","block");
            }
        };
    };
</script>
<?php echo $this->Form->create('PatientConsultation', array('id' => 'PatientConsultationTabAttachFileForm', 'url' => '/patient_ipds/tabAttachFile/'.$patientId.'/'.$viewId, 'enctype' => 'multipart/form-data')); ?>
<fieldset style="padding: 5px; border: 1px dashed #3C69AD;">
    <legend><?php __('Upload Document File Here'); ?></legend>
    <table style="width: 100%;">
        <tr>
            <td style="width: 20%;">
                <img style="margin-left: 15px;" src="<?php echo $this->webroot; ?>img/button/image_upload.png" alt=""/>
                <br><br>
                <span><?php echo TABLE_IMAGE; ?> / PDF File</span>
            </td>
            <td><div style="padding-top: 10px;">:</div></td>
            <td>
                <div class="controls">                                                                                                                                         
                    <div class="entry" style="padding-top: 10px;">
                        <img id="uploadPreview" class="uploadPreview" style="width: 120px; height: 120px;" data="uploadPreview"/>
                        <input id="uploadImage" type="file" name="photos[]" onchange="PreviewImage(this.id);" rel="" class="uploadImage" data_name="photos" data="uploadImage"/> 
                        <a class="buttons positive btn-add" type="button" data="btn" id="btn">
                            <img style="width: 20px; height: 20px; cursor: pointer;" src="<?php echo $this->webroot; ?>img/button/plus.png" alt=""/>
                        </a> 
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <div class="clear"></div>
    <div class="buttons" style="padding-top: 10px;">
        <button type="submit" class="positive">
            <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
            <span class="txtSavePatient"><?php echo ACTION_SAVE; ?></span>
        </button>
        <img alt="" src="<?php echo $this->webroot; ?>img/loading.gif" class="loading" style="display: none;" />
    </div>
</fieldset>
<?php echo $this->Form->end(); ?>
<div class="clear"></div>

<div id="picture">
    <?php
    $queryImage = mysql_query("SELECT * FROM patient_documents WHERE is_active=1 AND patient_id= {$patientId}");
    if (@mysql_num_rows($queryImage)) {
        $ind = 0;
        while ($dataImage = mysql_fetch_array($queryImage)) {
            if($dataImage['extension']=="pdf"){
                ?>
                <div style="float: left; width: 230px;" id="Image_Echo_History_<?php echo $dataImage['id']; ?>">
                    <a target="_blank" href="<?php echo $this->webroot; ?>public/patient_document/<?php echo $dataImage['src_name']; ?>">
                        <?php echo $dataImage['src_name']; ?>
                    </a>
                    <br/><br/>
                    <span href="#" style="text-align: center; padding-left: 5px;">
                        <?php echo date('d/m/Y H:i:s', strtotime($dataImage['created'])); ?>
                    </span>
                    <a style="float: right; cursor: pointer;" class="buttons positive btnDeleteImage btn-remove" type="button" data="btn" id="btn" rel="<?php echo $dataImage["id"];?>" name="<?php echo $dataImage["src_name"];?>">
                        <img atr="Delete Img" onmouseover="Tip('Delete')" style="width: 14px; height: 14px;" src="<?php echo $this->webroot; ?>img/button/trash.png" alt=""/>
                    </a>
                </div>   
                <?php
            }else {
                ?>
                <div style="float: left; width: 230px;" id="Image_Echo_History_<?php echo $dataImage['id']; ?>">
                    <a href="#">
                        <img class="productPhoto"​​​ rel="<?php echo $ind;?>" id="photoDisplay<?php echo $ind;?>" src="<?php echo $this->webroot; ?>public/patient_document/<?php echo $dataImage['src_name']; ?>" alt="<?php echo $dataImage['src_name']; ?>" width="220px" height="200px" vspace='2px' style="margin-left:5px;">
                    </a>    
                    <br/>
                    <span href="#" style="text-align: center; padding-left: 5px;">
                        <?php echo date('d/m/Y H:i:s', strtotime($dataImage['created'])); ?>
                    </span>
                    <a style="float: right; cursor: pointer;" class="buttons positive btnDeleteImage btn-remove" type="button" data="btn" id="btn" rel="<?php echo $dataImage["id"];?>" name="<?php echo $dataImage["src_name"];?>">
                        <img atr="Delete Img" onmouseover="Tip('Delete')" style="width: 14px; height: 14px;" src="<?php echo $this->webroot; ?>img/button/trash.png" alt=""/>
                    </a>
                    <!-- The Modal -->
                    <div id="myModal<?php echo $ind;?>" class="modal">;
                        <span class="close">&times;</span>
                        <img class="modal-content" id="imgEcho<?php echo $ind;?>" style="width: auto; height: 74%;">
                        <div class="captionTxt" id="caption<?php echo $ind;?>"></div>
                    </div>
                </div>                    
                <?php
            }
            $ind++;
        }
    }
    ?>
</div>