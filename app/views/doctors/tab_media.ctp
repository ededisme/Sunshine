<?php echo $this->Form->create('Media', array ('id'=>'file_upload', 'url'=>'/doctors/upload/'.$this->params['pass'][0], 'enctype' => 'multipart/form-data'));?>
    <input type="file" name="file" multiple>
    <button>Upload</button>
    <div>Upload files</div>
<?php echo $this->Form->end(); ?>
<table id="files">
    <?php
    include('includes/function.php');
    $query=mysql_query("SELECT * FROM medias WHERE patient_id=".getPatientIdByQueueId($this->params['pass'][0]));
    while($data=mysql_fetch_array($query)){?>
    <tr>
        <td><a href="<?php echo $this->webroot; ?>public/media/<?php echo $data['src']; ?>" target="_blank"><?php echo $data['src']; ?></a></td>
        <td><?php echo dateShort($data['created']); ?></td>
    </tr>
    <?php } ?>
</table>
<script src="<?php echo $this->webroot; ?>js/blueimp-jQuery-File-Upload-2c5aec1/jquery.fileupload.js"></script>
<script src="<?php echo $this->webroot; ?>js/blueimp-jQuery-File-Upload-2c5aec1/jquery.fileupload-ui.js"></script>
<script type="text/javascript">
$(function (){
    $('#file_upload').fileUploadUI({
        uploadTable: $('#files'),
        downloadTable: $('#files'),
        buildUploadRow: function (files, index) {
            return $('<tr><td>' + files[index].name + '<\/td>' +
                    '<td class="file_upload_progress"><div><\/div><\/td>' +
                    '<td class="file_upload_cancel">' +
                    '<button class="ui-state-default ui-corner-all" title="Cancel">' +
                    '<span class="ui-icon ui-icon-cancel">Cancel<\/span>' +
                    '<\/button><\/td><\/tr>');
        },
        buildDownloadRow: function (file) {
            return $('<tr><td>' + file.name + '<\/td><\/tr>');
        }
    });
});
</script>