<script type="text/javascript">
    $(document).ready(function(){
        $(".btnBackVendor").click(function(event){
            event.preventDefault();
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
            oCache.iCacheLower = -1;
            oTableVendor.fnDraw(false);
        });
    });
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="" class="positive btnBackVendor">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<fieldset>
    <legend><?php __(MENU_VENDOR_MANAGEMENT_INFO); ?></legend>
    <div style="width: 49%; vertical-align: top; float: left;">
        <table style="width: 100%;" cellpadding="5">
            <tr>
                <td><label for="VendorVgroupId"><?php echo USER_GROUP; ?> :</td>
                <td>
                    <div class="inputContainer">
                        <?php 
                        $sqlGroup = mysql_query("SELECT GROUP_CONCAT(name) AS name FROM vgroups WHERE id IN (SELECT vgroup_id FROM vendor_vgroups WHERE vendor_id = {$this->data['Vendor']['id']})");
                        $rowGroup = mysql_fetch_array($sqlGroup);
                        echo $sqlGroup[0];
                        ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td><?php echo TABLE_CODE; ?> :</td>
                <td>
                    <div class="inputContainer">
                        <?php echo $this->data['Vendor']['vendor_code']; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td width="30%"><?php echo TABLE_NAME; ?> :</td>
                <td>
                    <div class="inputContainer">
                        <?php echo $this->data['Vendor']['name']; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td width="30%"><?php echo TABLE_COUNTRY; ?> :</td>
                <td>
                    <div class="inputContainer">
                        <?php 
                        if(!empty($this->data['Vendor']['country_id'])){
                            $sqlContry = mysql_query("SELECT name FROM countries WHERE id  = {$this->data['Vendor']['country_id']}");
                            $rowContry = mysql_fetch_array($sqlContry);
                            echo $rowContry[0];
                        }
                        ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td><?php echo TABLE_TELEPHONE_WORK; ?> :</td>
                <td>
                    <div class="inputContainer">
                        <?php echo $this->data['Vendor']['work_telephone']; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td><?php echo TABLE_TELEPHONE_OTHER; ?> :</td>
                <td>
                    <div class="inputContainer">
                        <?php echo $this->data['Vendor']['other_number']; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td><?php echo TABLE_FAX; ?> :</td>
                <td>
                    <div class="inputContainer">
                        <?php echo $this->data['Vendor']['fax_number']; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td><?php echo TABLE_EMAIL; ?> :</td>
                <td>
                    <div class="inputContainer">
                        <?php echo $this->data['Vendor']['email_address']; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top;"><?php echo TABLE_ADDRESS; ?> :</td>
                <td>
                    <div class="inputContainer">
                        <?php 
                        echo $this->data['Vendor']['address'];
                        ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td><?php echo TABLE_PAYMENT_TERMS; ?> :</td>
                <td>
                    <div class="inputContainer">
                        <?php 
                        if(!empty($this->data['Vendor']['payment_term_id'])){
                            $sqlTerm = mysql_query("SELECT name FROM payment_terms WHERE id  = {$this->data['Vendor']['payment_term_id']}");
                            $rowTerm = mysql_fetch_array($sqlTerm);
                            echo $rowTerm[0];
                        }
                        ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div style="vertical-align: top; float: right; width: 49%;">
        <table style="width: 100%" cellpadding="5">
            <tr>
                <td colspan="2" style="text-align: center;">
                    <?php 
                    if($this->data['Vendor']['photo'] != ''){
                        $photo = "public/vendor_photo/".$this->data['Vendor']['photo'];
                    }else{
                        $photo = "img/button/no-images.png";
                    }
                    ?>
                    <img id="photoDisplay" alt="" src="<?php echo $this->webroot; ?><?php echo $photo; ?>" />
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top;"><?php echo TABLE_NOTE; ?> :</td>
                <td>
                    <div class="inputContainer">
                        <?php echo $this->data['Vendor']['note']; ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div style="clear: both;"></div>
</fieldset>