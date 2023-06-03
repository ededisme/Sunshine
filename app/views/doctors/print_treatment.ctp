<?php $absolute_url = FULL_BASE_URL . Router::url("/", false); ?>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Print Patient Treatment</title>
    <link rel="stylesheet" type="text/css" href="../stylesheets/print.css"/>    
    <?php echo $javascript->link('jquery-1.4.4.min'); ?>
    <script type="text/javascript">
        $(document).ready(function () {
            window.print();
            setTimeout("closePrintView()", 1000);
        });
        function closePrintView() {
            document.location.href = '<?php echo $absolute_url . $this->params['controller']; ?>/consultation/<?php echo $qId; ?>';
        }
    </script>
</head>
<style type="text/css">
    .history td{
        font-size: 18px;

    }    
    .medicine td{
        text-align: center;
        font-size: 18px;
        border: 1px solid;
    }
</style>
<body>
    <div class="print">
        <table cellspacing='0' cellpadding='0' border='0' width='100%'>
            <tr>
                <td>&nbsp;</td>
                <td style="text-align: center">
                    <span style="text-align: center;font-weight: bold;font-size: 22px;" class="unicod_wordart">ព្រះរាជាណាចក្រកម្ពុជា</span>
                    <br/>
                    <span style="text-align: left ;font-size: 18px;" class="unicod_wordart">ជាតិ សាសនា ព្រះមហាក្សត្រ</span> 
                </td>
            </tr>
            <tr>                        
                <td align='left'></td>
                <td align='center'>&nbsp;</td>
                <td align='center'>

                </td>
            </tr>     
            <tr>
                <td align='left'>
                    <span class="unicod_wordart" style="font-size: 18px;">លឹម តាំង គ្លីនិក</span>                    
                </td>                        
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td align='left'>
                    <span  style="font-size: 16px;">លេខ​ <?php echo $treatments['Treatment']['treatment_code'] ?> ម ម ខ ស</span>                    
                </td>                        
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
        <br/>
        <table class="history" cellspacing='0' cellpadding='0' border='0' width='100%'>                
            <tr>            
                <td style="width: 5%">- <?php echo PATIENT_NAME;?>:</td>
                <td style="width: 10%"><?php echo $patients['Patient']['patient_name']; ?></td>
                <td style="width: 5%"><?php echo TABLE_SEX; ?>: 
                    <?php
                    echo $patients['Patient']['sex'];
                    ?>
                </td>
                <td style="width: 5%"><?php echo TABLE_AGE; ?>: <?php echo $patients['Patient']['dob'].' '.GENERAL_YEAR_OLD?></td>
                <td style="width: 20%"><?php echo PATIENT_CODE;?>: <?php echo $patients['Patient']['patient_code'] ?></td>                     
            </tr>
            <tr>                         
                <td colspan="5">- រោគវិនិច័្ឆយ:
                    <?php
                    if ($treatments['Treatment']['diagnostic'] != "") {
                        echo $treatments['Treatment']['diagnostic'];
                    } else {
                        echo '.....................................................................................................................................................................................';
                    }
                    ?>

                </td>                            
            </tr>        
        </table>
        <table cellspacing='0' cellpadding='0' border='0' width='100%'>
            <tr>
                <td style="width: 30%"></td>
                <td style="text-align: center;width: 30%"><span style="font-weight: bold;font-size: 28px">វេជ្ជបញ្ជា</span></td>
                <td></td>
            </tr>
        </table>
        <br/>            
        <table class="medicine" cellspacing='0' cellpadding='0' border='1px solid' width='100%'>
            <tr>
                <th class="first"><?php echo TABLE_NO; ?></th>
                <th><?php echo DRUG_COMMERCIAL_NAME; ?></th>                    
                <th><?php echo GENERAL_TYPE; ?></th>                    
                <th><?php echo GENERAL_QTY; ?></th>
                <th  style="text-align: center"><?php __(GENERAL_NUMBER); ?></th>
                <th style="text-align: center"><?php __(GENERAL_MORNING); ?></th>
                <th style="text-align: center"><?php __(GENERAL_AFTERNOON); ?></th>
                <th style="text-align: center"><?php __(GENERAL_EVENING); ?></th>
                <th style="text-align: center"><?php __(GENERAL_NIGHT); ?></th>
                <th><?php echo DRUG_NOTE; ?></th>                    
            </tr>
            <?php
            $index = 1;
            foreach ($treatmentDetails as $record):
                $query_drug = mysql_query(" SELECT commercial_name,mu.*
                                                FROM grand_stocks g
                                                INNER JOIN sale_stocks s ON g.id=s.grand_stock_id                                                                                                
                                                INNER JOIN medicine_units mu ON g.medicine_unit_id=mu.id
                                                WHERE s.id=" . $record['TreatmentDetail']['sale_stock_id']);
                while ($data = mysql_fetch_array($query_drug)) {
                    if ($data['medicine_type'] == "Injection") {
                        if ($data['flacont'] == 0) {
                            $type = MEASURE_AMPOULE;
                        } else {
                            $type = MEASURE_FLACON;
                        }
                    } else if ($data['medicine_type'] == "Tablet") {
                        $type = MEASURE_TABLET;
                    } else if ($data['medicine_type'] == "Capsule") {
                        $type = MEASURE_CAPSULE;
                    } else if ($data['medicine_type'] == "Powder") {
                        $type = MEASURE_POWDER;
                    } else if ($data['medicine_type'] == "Syrup") {
                        $type = MEASURE_WATER;
                    } else if ($data['medicine_type'] == "Cream") {
                        $type = "tube";
                    } else if ($data['medicine_type'] == "jel") {
                        $type = "tube";
                    } else if ($data['medicine_type'] == "form") {
                        $type = "tube";
                    } else if ($data['medicine_type'] == "cleaningBar") {
                        $type = "tube";
                    } else if ($data['medicine_type'] == "sprite") {
                        $type = "tube";
                    } else if ($data['medicine_type'] == "ointment") {
                        $type = "tube";
                    } else if ($data['medicine_type'] == "shampoo") {
                        $type = "tube";
                    } else if ($data['medicine_type'] == "lotion") {
                        $type = "tube";
                    } else if ($data['medicine_type'] == "stick") {
                        $type = "tube";
                    } else if ($data['medicine_type'] == "Liquid") {
                        $type = " amp";
                    } else if ($data['medicine_type'] == "Other") {
                        if ($data['flacont'] == 0) {
                            $type = " no";
                        } else {
                            $type = " fla";
                        }
                    } else if ($data['medicine_type'] == "Suppository") {
                        if ($data['suppository'] == 0) {
                            $type = " unidoses";
                        } else {
                            $type = " sup";
                        }
                    }
                    ?>
                    <tr>
                        <td><?php echo $index++; ?></td>                                                
                        <td style="text-align: left"><?php echo $data['commercial_name']; ?></td>                                        
                        <td><?php echo $data['medicine_type']; ?></td>                        
                        <td><?php echo $record['TreatmentDetail']['amount'] . ' ' . $type; ?></td>
                        <td><?php echo $record['TreatmentDetail']['num_day']; ?></td>
                        <td><?php echo $record['TreatmentDetail']['morning']; ?></td>
                        <td><?php echo $record['TreatmentDetail']['afternoon']; ?></td>
                        <td><?php echo $record['TreatmentDetail']['evening']; ?></td>
                        <td><?php echo $record['TreatmentDetail']['night']; ?></td>
                        <td style="width: 30%;text-align: left;"><?php echo $record['TreatmentDetail']['note']; ?></td>
                    </tr>
                <?php } ?>
            <?php endforeach; ?>
        </table>

        <br/>
        <br/>
        <br/>

        <table cellspacing='0' cellpadding='0' border='0' width='100%'>
            <tr style="padding-right: 15px">
                <td valign='bottom' align="left">&nbsp;</td>
                <td style="text-align: center">&nbsp;</td>
                <td style="text-align: right" >
                    <?php $date = $treatments['Treatment']['created'] ?>
                    <?php list($year, $month, $day) = explode('-', substr($date, 0, 10)); ?>                            
                    <span  style="text-align: center">ភ្នំពេញ, ថ្ងៃ​ទី <?php echo $day ?> ខែ <?php echo $month ?> ឆ្នាំ <?php echo $year ?></span>                    
                </td>   
            </tr>
            <tr>                        
                <td align="left"></td>
                <td style="text-align: center">&nbsp;</td>
                <td style="text-align: right">
                    <span>គ្រូពេទ្យព្យាបាល&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>                    
                </td>
            </tr>                                        

            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr>                        
                <td align="left">សូមយកវេជ្ជបញ្ជាមក​វិញ ពេលពិនិត្យលើក្រោយ</td>
                <td style="text-align: center">&nbsp;</td>
                <td style="text-align: right">
                    <span>
                        <?php //echo $doctor_nme; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>                    
                </td>
            </tr>
        </table> 
    </div>
</body>