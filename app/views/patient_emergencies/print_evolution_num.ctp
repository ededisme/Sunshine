<style type="text/css" media="screen">
    div.print-footer {display: none;}   
    table tr th{
        text-align: center !important;
    }
</style>

<style type="text/css" media="print">
    div.print_doc { width:100%; }
    div.print-footer { display: block; width: 100%;}
    #btnDisappearPrint { display: none;}
    table tr td{ font-size: 13px; }
    @page
    {
        /*this affects the margin in the printer settings*/  
        margin: 5mm 7mm 5mm 10mm;
    }
    p{
        padding: 0 10px;
        margin: 0;
        font-size: 14px;
    }
    th{ font-weight: normal; }
</style>
<div id="printPatientEmergencyEvolution" class="print_doc">       
    <table style="width: 100%;">
        <tr>
            <td style="text-align: right;">
                <?php echo TABLE_EMERGENCY_CODE?>: <?php echo $result['PatientEmergency']['emergency_code']; ?>
            </td>
        </tr>
        <tr>
            <td style="text-align: right;">
                <?php 
                echo GENEARL_DATE.': '.date('d/m/Y H:i:s', strtotime($result['PatientEmergencyEvolution']['created']));
                ?>
            </td>
        </tr> 
    </table>
    <table style="width: 100%;height: 1000px;">           
        <tr>
            <td valign='top' style="width: 50%;height: 100%;border-right: 1px solid #000;text-align: top;">
                <h2>Evolution Clinic</h2>
                <?php 
                echo GENEARL_DATE.': '.date('d/m/Y H:i:s', strtotime($result['PatientEmergencyEvolution']['date_evolution']));
                ?>
                <p><?php echo nl2br($result['PatientEmergencyEvolution']['evolution_description']);?></p>
            </td>
            <td valign='top' style="width: 50%;height: 100%;">
                <h2 style="text-align: center;">Prescription</h2>
                <p style="padding-left: 10px;"><?php echo nl2br($result['PatientEmergencyEvolution']['prescription']);?></p>
            </td>
        </tr>
    </table>
    <div style="clear:both"></div>
    <br />        
    <div style="float:left;width: 450px;">
        <div>
            <input type="button" value="<?php echo ACTION_PRINT; ?>" id='btnDisappearPrint' class='noprint'>                
        </div>
    </div>
</div>

<div id="footerInfoFix" class="print-footer">
    <?php echo $this->element('print_footer_address_fix'); ?>
</div> 
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(document).dblclick(function() {
            window.close();
        });
        $("#btnDisappearPrint").click(function() {
            $("#footerPrint").show();
            window.print();
            window.close();
        });
    });
</script>