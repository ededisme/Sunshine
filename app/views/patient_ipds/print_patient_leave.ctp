<style type="text/css" media="screen">
    span {
        font-size: 15px;
        font-family: 'Khmer OS Battambang';
        padding-right: 15px;
        display: inline-block;
        line-height: 2.5;
    }
</style>
<style type="text/css" media="print">
    div.print_doc {
        width: 100%;
        margin-top: 10px;
    }

    span {
        font-size: 17px;
        font-family: 'Khmer OS Battambang';
        padding-right: 10px;
        display: inline-block;
        line-height: 2.5;
    }

    @page {
        /*this affects the margin in the printer settings*/
        margin: 3mm 3mm 3mm 3mm;
    }
</style>
<?php
require_once("includes/function.php");
?>
<div id="printQuotationPatient" class="print_doc">
    <table style="width: 100%;">
        <tr>
            <td>
                <img class="logo" src="<?php echo $this->webroot; ?>img/logo.png" style="width:20%;">
            </td>
        </tr>
        <tr>
            <td>
                <h1 style="text-align: center; font-family: 'Khmer OS Battambang'; font-weight: bold; line-height: 18px; font-size: 25px; margin-top: 1px;">លិខិតចេញពីមន្ទីរពេទ្យ</h1>
            </td>
        </tr>
    </table>
    <table cellpadding="0" cellspacing="0" style="width: 100%;">
        <tr>
            <td style="text-align: center;">
                <span> គ្រូពេទ្យព្យាបាលនៃ​​ មន្ទីរសម្រាលព្យាបាលពន្លឺកុមារ </span>
            </td>
        </tr>
        <tr>
            <td>
                <span > បានអនុញ្ញាតឲ្យអ្នកជំងឺឈ្មោះ </span>
                <span style="width: 25%; text-align: center" > <?php echo $patient['Patient']['patient_name']; ?> </span>
                <span > លេខចុះបញ្ជី </span>
                <span > <?php echo $patient['Patient']['patient_code']; ?> </span>
                <span > ភេទ </span>
                <span > <?php echo $patient['Patient']['sex'] == 'F' ? 'ស្រី' : 'ប្រុស'; ?> </span>
            </td>
        </tr>
        <tr>
            <td>
                <span > អាយុ </span>
                <span style="width: 15%; text-align: center" >
                    <?php $then_ts = strtotime($patient['Patient']['dob']);
                    $then_year = date('Y', $then_ts);
                    $age = date('Y') - $then_year;
                    if (strtotime('+' . $age . ' years', $then_ts) > time()) $age--;
                    if ($age == 0) {
                        $then_year = date('m', $then_ts);
                        $month = date('m') - $then_year;
                        if (strtotime('+' . $month . ' month', $then_ts) > time()) $month--;
                        echo $month . ' ' . 'ខែ';
                    } else {
                        echo $age . ' ' . 'ឆ្នាំ';
                    }
                    ?>
                </span>
                <span > សញ្ជាតិ </span>&nbsp;&nbsp;
                <span style="width: 15%; text-align: center" > <?php echo $patient['Patient']['nationality'] == 36 ? 'ខ្មែរ' : 'បរទេស'; ?></span>&nbsp;&nbsp;
                <span > អ្នកជំងឺជា </span>&nbsp;&nbsp;
                <span style="width: 15%; text-align: center" > <?php echo $patient['Patient']['occupation']; ?>&nbsp;&nbsp;
        </tr>
        <tr>
            <td>
                <span style="width: 15%;" > ឈ្មោះឪពុក </span>&nbsp;&nbsp;
                <span style="width: 25%;" > <?php echo $patient['Patient']['father_name']; ?></span>&nbsp;&nbsp;
                <span style="width: 15%;" > មុខរបរ </span>&nbsp;&nbsp;
                <span style="width: 20%;" > <?php echo $patient['Patient']['father_occupation']; ?></span>&nbsp;&nbsp;
            </td>
        </tr>
        <tr>
            <td>
                <span style="width: 15%;" > ឈ្មោះម្ដាយ </span>&nbsp;&nbsp;
                <span style="width: 25%;" > <?php echo $patient['Patient']['mother_name']; ?></span>&nbsp;&nbsp;
                <span style="width: 15%;" > មុខរបរ </span>&nbsp;&nbsp;
                <span style="width: 20%;" > <?php echo $patient['Patient']['mother_occupation']; ?></span>&nbsp;&nbsp;
            </td>
        </tr>
        <tr>
            <td>
                <span > អាសយដ្ឋាន : <?php echo $patient['Patient']['address']; ?></span>&nbsp;&nbsp;
            </td>
        </tr>
        <tr>
            <td>
                <?php
                $day   = date("d", strtotime($patient['PatientIpd']['date_ipd']));
                $month = date("m", strtotime($patient['PatientIpd']['date_ipd']));
                $year  = date("Y", strtotime($patient['PatientIpd']['date_ipd']));
                ?>
                <span style="width: 30%;" > ចូលសម្រាកព្យាបាលនៅថ្ងៃទី </span>
                <span > : <?php echo $day; ?> ខែ <?php echo $month; ?> ឆ្នាំ <?php echo $year; ?></span>
            </td>
        </tr>
        <tr>
            <td>
                <?php
                $day = '';
                $month = '';
                $year = '';
                if ($patient['PatientLeave']['end_date'] != "0000-00-00"  && $patient['PatientLeave']['end_date'] != NULL) {
                    $day   = date("d", strtotime($patient['PatientLeave']['end_date']));
                    $month = date("m", strtotime($patient['PatientLeave']['end_date']));
                    $year  = date("Y", strtotime($patient['PatientLeave']['end_date']));
                }

                ?>
                <span style="width: 30%;" > ចេញពីមន្ទីរពេទ្យថ្ងៃទី </span>
                <span > : <?php echo $day; ?> ខែ​ <?php echo $month; ?> ឆ្នាំ <?php echo $year; ?> </span>
            </td>
        </tr>
        <tr>
            <td>
                <span > រោគវិនិច្ឆ័យចេញពីមន្ទីរពេទ្យ : <?php echo $patient['PatientLeave']['diagnotist_after']; ?> </span>
            </td>
        </tr>
        <tr>
            <td>
                <span > សភាពអ្នកជំងឺចេញពីមន្ទីរពេទ្យ </span>
                <span > : <?php echo $patient['PatientLeave']['note']; ?></span>
            </td>
        </tr>
        <tr>
            <td>
                <span > ដំបូន្មានរបស់គ្រូពេទ្យ : <?php echo $patient['PatientLeave']['doctor_nme']; ?></span>
            </td>
        </tr>
    </table>
    <table style="width:100%">
        <tr>
            <td>
                <?php
                $day   = date("d");
                $month = date("m");
                $year  = date("Y");
                ?>
                <p style="text-align: right;"><span >ថ្ងៃទី <?php echo $day;  ?> ខែ <?php echo $month;  ?> ឆ្នាំ <?php echo $year;  ?></span></p>
            </td>
        </tr>
        <tr>
            <td style="width:50%;">
                <p style="text-align: right; margin-right: 20px;margin-top: 20px;"><span >គ្រូពេទ្យព្យាបាល</span></p>
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