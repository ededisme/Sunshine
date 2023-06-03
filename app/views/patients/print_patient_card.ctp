<style type="text/css" media="screen">
    div.print-footer {display: none; line-height:35px;}
        /* Create a custom checkbox */
</style>
<style type="text/css" media="print">
    div.print_doc { width:350px;}
    #btnDisappearPrint { display: none;}
    table tr td{ font-size: 18px !important; font-weight: bold; }
    @page
    {
        /*this affects the margin in the printer settings*/  
        margin: 5mm;
        
    }
    th{ font-weight: normal; }   
    h2{ font-size: 18px;}
</style>
<?php 
include('includes/function.php');
?>
<div id="printQuotationPatient" class="print_doc" style="text-align: center; margin-top: 5px; width:350px;">
    <table cellspacing="0" celpadding="0" style="width: 350px; border: 0px solid #000; text-align: center;">
        <tr>
            <td style="font-weight: bold; font-size: 18px; text-align: left; line-height:35px">
                <?php echo PATIENT_CODE;?> : <?php echo $patient['Patient']['patient_code']; ?>
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; font-size: 18px; text-align: left; line-height:35px">
                <?php echo "Name";?> : <?php echo $patient['Patient']['patient_name']; ?>
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; font-size: 18px; text-align: left; line-height:35px">
            <?php echo 'Gender';?>:
                <?php if($patient['Patient']['sex']=='M'){?>
                    <img alt="" src="<?php echo $this->webroot; ?>/img/button/checkbox.png" style="max-width: 18px; max-height:18px" />
                    <?php
                        echo GENERAL_MALE;
                    ?>
                    <img alt="" src="<?php echo $this->webroot; ?>/img/button/frame.png" style="max-width: 18px; max-height:18px" />
                    <?php
                        echo GENERAL_FEMALE;
                    }else{
                    ?>
                        <img alt="" src="<?php echo $this->webroot; ?>/img/button/frame.png" style="max-width: 18px; max-height:18px" />
                        <?php
                            echo GENERAL_MALE;
                        ?>
                        <img alt="" src="<?php echo $this->webroot; ?>/img/button/checkbox.png" style="max-width: 18px; max-height:18px" />
                        <?php
                            echo GENERAL_FEMALE;
                        }
                    ?>
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; font-size: 18px; text-align: left;line-height:35px">
                <?php echo TABLE_DOB;?>: <?php echo date("d / M / Y", strtotime($patient['Patient']['dob'])); ?>
            </td>
        </tr>
        <tr style="display:none">
            <td style="font-weight: bold; font-size: 18px; text-align: left;">
                <?php echo TABLE_PLACE_OF_BIRTH;?>: <?php echo $patient['Patient']['address']; ?>
            </td>
        </tr>
        <tr style="display:none">
            <td style="font-weight: bold; font-size: 18px; text-align: left;">
                <?php echo TABLE_NATIONALITY;?>: 
                <?php                 
                    if($patient['Patient']['patient_group_id']!=""){
                        $query = mysql_query("SELECT name FROM patient_groups WHERE id=".$patient['Patient']['patient_group_id']);
                        while ($row = mysql_fetch_array($query)) {                        
                            if($patient['Patient']['patient_group_id']==1){
                                echo $row['name'];
                            }else{
                                $sqlNationality = mysql_query("SELECT name FROM nationalities WHERE id = {$patient['Patient']['nationality']}");
                                while($rowNationality = mysql_fetch_array($sqlNationality))
                                {
                                    echo $rowNationality['name'];
                                }
                            }
                        }
                    }else{
                        $sqlNationality = mysql_query("SELECT name FROM nationalities WHERE id = {$patient['Patient']['nationality']}");
                        while($rowNationality = mysql_fetch_array($sqlNationality))
                        {
                            echo $rowNationality['name'];
                        }
                    }
                ?>
            </td>
        </tr>
    </table>    
    <!-- <div style="clear:both"></div> -->
    <div>
        <input type="button" value="<?php echo ACTION_PRINT; ?>" id='btnDisappearPrint' class='noprint'>                
    </div>
</div>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/jquery-qrcode-master/jquery.qrcode.min.js"></script>
<script>
    jQuery(function () {
        jQuery('.output').qrcode({width: 60, height: 60, text: '<?php echo $patient['Patient']['patient_code']; ?>'});
    })
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $(document).dblclick(function() {
            window.close();
        });
        $("#btnDisappearPrint").click(function() {
            $("#footerPrint").show();           
            try
            {
                jsPrintSetup.setOption('scaling', 100);
                jsPrintSetup.clearSilentPrint();
                jsPrintSetup.setOption('printBGImages', 1);
                jsPrintSetup.setOption('printBGColors', 1);
                jsPrintSetup.setSilentPrint(1);

                // Choose printer using one or more of the following functions
                // jsPrintSetup.getPrintersList...
                // jsPrintSetup.setPrinter...
                // we add douplicate \\ for it working, if user use share printer
                jsPrintSetup.setPrinter('Udaya-Sticker');

                jsPrintSetup.print();
                window.close();
            }
            catch (err)
            {
                //Default printing if jsPrintsetup is not available
                window.print();
                window.close();
            }
        });
    });
</script>