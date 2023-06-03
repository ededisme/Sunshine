<script type="text/javascript">
    $(document).ready(function(){
        $(".btnBackCustomer").click(function(event){
            event.preventDefault();
            oCache.iCacheLower = -1;
            oTableCustomer.fnDraw(false);
            var rightPanel=$(this).parent().parent().parent();
            var leftPanel=rightPanel.parent().find(".leftPanel");
            rightPanel.hide();rightPanel.html("");
            leftPanel.show("slide", { direction: "left" }, 500);
        });
    });
</script>
<div style="padding: 5px;border: 1px dashed #bbbbbb;">
    <div class="buttons">
        <a href="" class="positive btnBackCustomer">
            <img src="<?php echo $this->webroot; ?>img/button/left.png" alt=""/>
            <?php echo ACTION_BACK; ?>
        </a>
    </div>
    <div style="clear: both;"></div>
</div>
<br />
<fieldset>
    <legend><?php __(MENU_CUSTOMER_MANAGEMENT_INFO); ?></legend>
    <div style="width: 44%; vertical-align: top; float: left;">
        <table cellpadding="5" style="width: 100%;">
            <tr>
                <td style="width: 40%;"><?php echo TABLE_CUSTOMER_NUMBER; ?> :</td>
                <td>
                    <div class="inputContainer" style="width: 100%;">
                        <?php echo $customer['Customer']['customer_code']; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td><?php echo TABLE_NAME; ?> :</td>
                <td>
                    <div class="inputContainer" style="width: 100%;">
                        <?php echo $customer['Customer']['name']; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <fieldset>
                        <legend><?php echo TABLE_ADDRESS; ?></legend>
                        <table cellpadding="3" cellspacing="0" style="width: 100%;">
                            <tr>
                                <td style="width: 18%;"><?php echo TABLE_NO; ?></td>
                                <td>
                                    <div class="inputContainer" style="width: 100%;">
                                        <?php echo $customer['Customer']['house_no']; ?>
                                    </div>
                                </td>
                                <td style="width: 12%;"><?php echo TABLE_STREET; ?></td>
                                <td>
                                    <div class="inputContainer" style="width: 100%;">
                                        <?php 
                                        echo $customer['Street']['name']; 
                                        ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 18%;"><?php echo TABLE_PROVINCE; ?></td>
                                <td>
                                    <div class="inputContainer" style="width: 100%;">
                                        <?php 
                                        if($customer['Customer']['province_id'] > 0){
                                            $provinceId = $customer['Customer']['province_id'];
                                            $districtId = $customer['Customer']['district_id']>0?$customer['Customer']['district_id']:'0';
                                            $communeId  = $customer['Customer']['commune_id']>0?$customer['Customer']['commune_id']:'0';
                                            $villageId  = $customer['Customer']['village_id']>0?$customer['Customer']['village_id']:'0';
                                            $sqlAddress = mysql_query("SELECT p.name AS p_name, d.name AS d_name, c.name AS c_name, v.name AS v_name FROM provinces AS p LEFT JOIN districts AS d ON d.province_id = p.id AND d.id = {$districtId} LEFT JOIN communes AS c ON c.district_id = d.id AND c.id = {$communeId} LEFT JOIN villages AS v ON v.commune_id = c.id AND v.id = {$villageId} WHERE p.id = {$customer['Customer']['province_id']}");    
                                            $rowAddress = mysql_fetch_array($sqlAddress);
                                        }else{
                                            $rowAddress['p_name'] = '';
                                            $rowAddress['d_name'] = '';
                                            $rowAddress['c_name'] = '';
                                            $rowAddress['v_name'] = '';
                                        }
                                        echo $rowAddress['p_name'];
                                        ?>
                                    </div>
                                </td>
                                <td style="width: 12%;"><?php echo TABLE_DISTRICT; ?></td>
                                <td>
                                    <div class="inputContainer" style="width: 100%;">
                                        <?php echo $rowAddress['d_name']; ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 18%;"><?php echo TABLE_COMMUNE; ?></td>
                                <td>
                                    <div class="inputContainer" style="width: 100%;">
                                        <?php echo $rowAddress['c_name']; ?>
                                    </div>
                                </td>
                                <td style="width: 12%;"><?php echo TABLE_VILLAGE; ?></td>
                                <td>
                                    <div class="inputContainer" style="width: 100%;">
                                        <?php echo $rowAddress['v_name']; ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td><?php echo TABLE_TELEPHONE; ?> :</td>
                <td>
                    <div class="inputContainer" style="width: 100%;">
                        <?php echo $customer['Customer']['main_number']; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td><?php echo TABLE_MOBILE; ?> :</td>
                <td>
                    <div class="inputContainer" style="width: 100%;">
                        <?php echo $customer['Customer']['mobile_number']; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td><?php echo TABLE_TELEPHONE_ALT; ?> :</td>
                <td>
                    <div class="inputContainer" style="width: 100%;">
                        <?php echo $customer['Customer']['other_number']; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td><?php echo TABLE_EMAIL; ?> :</td>
                <td>
                    <div class="inputContainer" style="width: 100%;">
                        <?php echo $customer['Customer']['email']; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td><?php echo TABLE_FAX; ?> :</td>
                <td>
                    <div class="inputContainer" style="width: 100%;">
                        <?php echo $customer['Customer']['fax']; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td><?php echo TABLE_PAYMENT_TERMS; ?> :</td>
                <td>
                    <div class="inputContainer" style="width: 100%;">
                        <?php echo $customer['PaymentTerm']['name']; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td><?php echo TABLE_LIMIT_CREDIT; ?> :</td>
                <td>
                    <div class="inputContainer" style="width: 100%;">
                        <?php echo $customer['Customer']['limit_balance']; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td><?php echo TABLE_LIMIT_NUMBER_INVOICE; ?> :</td>
                <td>
                    <div class="inputContainer" style="width: 100%;">
                        <?php echo $customer['Customer']['limit_total_invoice']; ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div style="width: 30%; vertical-align: top; float: left;">
        <table style="width: 100%;" cellpadding="5">
            <tr>
                <td style="width: 40%;"><?php echo TABLE_GROUP; ?> :</td>
                <td>
                    <div class="inputContainer" style="width: 100%;">
                        <?php  
                        $sqlGroup = mysql_query("SELECT GROUP_CONCAT(name) FROM cgroups WHERE id IN (SELECT cgroup_id FROM customer_cgroups WHERE customer_id = ".$customer['Customer']['id'].")");
                        $rowGroup = mysql_fetch_array($sqlGroup);
                        echo $rowGroup[0];
                        ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 30%;"><?php echo TABLE_NAME_IN_KHMER; ?> :</td>
                <td>
                    <div class="inputContainer" style="width: 100%;">
                        <?php echo $customer['Customer']['name_kh']; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td><?php echo TABLE_VAT; ?> :</td>
                <td>
                    <div class="inputContainer" style="width: 100%;">
                        <?php echo $customer['Customer']['vat']; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td><?php echo TABLE_LOCATION_GROUP; ?> :</td>
                <td>
                    <div class="inputContainer" style="width: 100%;">
                        <?php 
                        $sqlWarehouse = mysql_query("SELECT name FROM location_groups WHERE customer_id = ".$customer['Customer']['id']." LIMIT 1;");
                        $rowWarehouse = mysql_fetch_array($sqlWarehouse);
                        echo $rowWarehouse[0]; 
                        ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top;"><?php echo TABLE_NOTE; ?> :</td>
                <td>
                    <div class="inputContainer" style="width: 100%;">
                        <?php echo $customer['Customer']['note']; ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div style="width: 25%; vertical-align: top; float: right;">
        <table style="width: 100%">
            <tr>
                <td colspan="2" style="text-align: center;">
                    <?php 
                    if($customer['Customer']['photo'] != ''){
                        $photo = "public/customer_photo/".$customer['Customer']['photo'];
                    }else{
                        $photo = "img/button/no-images.png";
                    }
                    ?>
                    <img id="photoDisplayCustomer" alt="" src="<?php echo $this->webroot; ?><?php echo $photo; ?>" style=" max-width: 250px; max-height: 250px;" />
                </td>
            </tr>
        </table>
    </div>
    <div style="clear: both;"></div>
</fieldset>