<?php $absolute_url = FULL_BASE_URL . Router::url("/", false); ?>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Print Patient Treatment Sticker </title>
    <link rel="stylesheet" type="text/css" href="../stylesheets/print.css"/>    
    <?php echo $javascript->link('jquery-1.4.4.min'); ?>
</head>
<style type="text/css" media="screen">
    div#print_treatment_sticker { 
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        margin: auto;
        height: 250px;
        width: 350px; 
    } 
    
    #btnDisappearPrintSticker { display: block;}
    #disappearPrint { display: block;}
    #trPrinter {display: block;}
</style>
<style type="text/css" media="print">
    div#print_treatment_sticker { 
        height: 250px;
        width: 350px;  
    }       
    table tr td{ font-size: 10px; }
    @page
    {
        margin: 0mm 4mm 0mm 4mm;
    }
    
    #btnDisappearPrintSticker { display: none;}
    #disappearPrint { display: none;}
    #trPrinter {display: none;}
</style>
<body>
    <div class="print" id="print_treatment_sticker">
        <table style="width: 100%;">
            <tr>
                <td style="text-align: center; font-family: 'Khmer OS Muol';font-size: 12px;" valign='top'>
                    <p style="font-family: 'Khmer OS Muol'; line-height: 0px; font-size: 12px;text-align: center; font-weight: bold;">
                        <?php echo GENERAL_COMPANY_NAME_KH; ?><br>
                    </p>
                </td>
            </tr>
       </table>
       <table style="width: 100%; font-size: 10px; font-weight: bold;">
            <tr>
                <td style="width:100%; padding-left: 2px;line-height: 15px;">  
                   Patient: <?php echo $patientName; ?>
                   <span style="margin-left: 2px;">
                        <?php echo $dob; ?>
                   </span>
                   <span style="margin-left: 5px;">
                        <?php echo $sex; ?>
                   </span>
                </td>
            </tr>
            <tr style="display: none">
                <td style="width:100%; padding-left: 2px;line-height: 15px;">
                    Age: 
                   <span style="margin-left: 2px;">
                        <?php echo $dob; ?>
                   </span>
                    <span style="margin-left: 5px;">
                        Weight: 
                        <?php echo $weight; ?> Kg
                   </span>
                </td>
            </tr>
            <tr>
                <td style="width: 100%; padding-left: 2px;line-height: 15px;">  
                    <?php echo $medicineName; ?>
                    <?php if($qty!=NULL){ ?>
                        <span style="margin-left: 15px; display: none;">
                            <?php echo $qty.' '.$uom; ?> 
                        </span> 
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td style="width:100%; text-align: left; padding-left: 2px;line-height: 15px;">
                    <?php if($numDay!=NULL){ ?>
                        <span>
                            <?php __(TABLE_USED); ?>:
                            <?php echo $numDay; ?>
                        </span>
                    <?php } ?>    
                    <?php if($frequency!=""){ ?>
                         <span>
                            <?php echo ' '.$frequency; ?>
                         </span> 
                    <?php } ?>   
                </td>
            </tr>            
            <tr>
                <td style="width:100%;line-height: 15px;">
                   <span style="font-weight: bold;">Date: <?php echo date("d/m/Y H:i"); ?></span>
                   <span style="font-weight: bold;"><?php echo $doctorName;?></span>
                </td>
            </tr>
            <tr id="trPrinter">
                <td style="width:100%; padding-left: 10px;">  
                    <div id="btnPrint" style="float:left; margin-left: 10px;">
                        <input style="width: 200px; height: 50px;" type="button" value="<?php echo ACTION_PRINT; ?>" id="btnDisappearPrintSticker">
                    </div>
                </td>
            </tr>
        </table>  
    </div>
</body>

<script type="text/javascript" src="<?php echo $this->webroot; ?>js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#btnDisappearPrintSticker").click(function() {
            var ws = window;
            try {
                jsPrintSetup.refreshOptions();
                var printer = 'Udaya-Sticker';
                var silent  = 1;
                jsPrintSetup.setPrinter(printer);
                jsPrintSetup.setOption('marginTop', 0);
                jsPrintSetup.setOption('marginBottom', 0);
                jsPrintSetup.setOption('marginLeft', 0);
                jsPrintSetup.setOption('marginRight', 0);
                jsPrintSetup.setSilentPrint(silent);
                jsPrintSetup.printWindow(ws);
                ws.close();
            } catch (e) {
                ws.print();
                ws.close();
            }
        });
    });
</script>