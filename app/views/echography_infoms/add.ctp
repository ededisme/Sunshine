<?php echo $this->element('prevent_multiple_submit'); ?>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/jquery.form.js"></script>
<script type="text/javascript">
    $(document).ready(function() {        
        // Prevent Key Enter
        preventKeyEnter();
        $("#EchographyInfomAddForm").validationEngine();
        $("#EchographyInfomAddForm").ajaxForm({
            beforeSubmit: function(arr, $form, options) {
                $(".txtSave").html("<?php echo ACTION_LOADING; ?>");
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner.gif");
            },
            success: function(result) {
                $(".loader").attr("src", "<?php echo $this->webroot; ?>img/layout/spinner-placeholder.gif");
                $(".btnBackEchographyInfom").click();
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
        $(".btnBackEchographyInfom").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableEchographyInfom.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });                        
    });
    
    // Editor English
    CKEDITOR.replace( 'EchographyInfomDescription', {
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
            on: {
                    pluginsLoaded: configureTransformations,
                    loaded: configureHtmlWriter
            }
    });
</script>
<div style="padding: 5px;border: 1px dashed #3C69AD;">
    <div class="buttons">
        <a href="" class="positive btnBackEchographyInfom">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<?php echo $this->Form->create('EchographyInfom', array('enctype' => 'multipart/form-data')); ?>
<fieldset>
    <legend><?php __(MENU_ECHOGRAPHY_MANAGEMENT_INFO); ?></legend>
    <table>
        <tr>
            <td><label for="EchographyInfomName"><?php echo TABLE_NAME; ?> <span class="red">*</span> :</label></td>
            <td>                
                <?php echo $this->Form->text('name', array('class' => 'validate[required]')); ?>
            </td>
        </tr>
        <tr>
            <td><label for="EchographyInfomDescription"><?php echo TABLE_DESCRIPTION; ?> <span class="red">*</span> :</label></td>
            <td>   
                <?php echo $this->Form->textarea('description',array('name'=>'data[EchographyInfom][description]','class'=>'mceEditor','style'=>'height:100px;width:999px;')); ?>
            </td>
        </tr>
    </table>
</fieldset>
<br/>
<div class="buttons">
    <button type="submit" class="positive">
        <img src="<?php echo $this->webroot; ?>img/button/save.png" alt=""/>
        <span class="txtSave"><?php echo ACTION_SAVE; ?></span>
    </button>
</div>
<?php echo $this->Form->end(); ?>
    

