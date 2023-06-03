<style type="text/css">
    .none-border{
        border: none !important;
    }
    .none-border tr td{
        border: none !important;
        font-size: 12px !important;
    }
    .table{
        font-size: 12px !important;
    }   
    .table_print th{
        background: none;
    }
    .table_print_labo td{ 
        border: none !important;
        line-height: 20px;
        padding: 0;
        margin: 0;
    }
</style>
<style type="text/css" media="print"> 
    .table_print th{
        background: none;
    }
    .table_print_labo td{ 
        border: none !important;
        line-height: 20px;
        padding: 0;
        margin: 0;
    }
    .none-border{
        border: none !important;
        font-size: 12px !important;
    }
    .none-border tr td{
        border: none !important;
        font-size: 12px !important;
    }
    tr{
         page-break-inside:avoid;
    }
    .table{
        font-size: 12px !important;
    } 
    p{
        font-size: 12px !important;
    } 
    @page
    {
        /*this affects the margin in the printer settings*/  
        margin: 5mm 7mm 5mm 10mm;
    }    
</style>
<div class="print_doc">
    <?php
    require_once("includes/function.php");
    foreach ($consultation as $consultation):
        ?>
        <div style="width: 45%;float: left;margin-top: 20px;">
            <table border="0" cellspacing='0' cellpadding='2' style="padding: 0; margin: 0;">
                <tr>
                    <td style="text-align: left">
                        <h2 style="font-size: 16px; line-height: 0px; text-transform: uppercase;"><?php echo $this->data['Branch']['name'] ?></h2>         
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $this->data['Branch']['address'] != '' ? 'Address : '.$this->data['Branch']['address'] :''; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $this->data['Branch']['telephone'] != '' ? 'Tel : '.$this->data['Branch']['telephone'] :''; ?>
                    </td>
                </tr>
            </table>    
        </div>
        <div class="clear"></div>
        <div style="width: 100%;">
            <h2 style="width:100% ; text-align : center ; font-size: 14px ;"><?php echo TITLE_MEDICAL_CERTIFICATE; ?></h2>
            <table border="0" cellspacing='0' cellpadding='3' style="padding: 0; margin: 0;">
                <tr>
                    <td style="border: none; width: 15%;">
                        <?php echo TABLE_NAME; ?> :
                        <?php echo $consultation['Patient']['patient_name']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="border: none;">
                        <?php echo TABLE_SEX; ?> :
                        <?php
                        if ($consultation['Patient']['sex'] == "F") {
                            echo GENERAL_FEMALE;
                        } else {
                            echo GENERAL_MALE;
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td style="border: none;">
                        <?php echo TABLE_DOB; ?> :                    
                        <?php echo date("d/m/Y", strtotime($consultation['Patient']['dob'])); ?>
                    </td>
                </tr>
                <tr>
                    <td style="border: none;">
                        <?php echo TABLE_CHIEF_COMPLAIN; ?>
                        <?php
                        $text = "";
                        $queryComplain = mysql_query("SELECT c.name FROM doctor_chief_complains dc INNER JOIN chief_complains c ON c.id = dc.chief_complain_id WHERE dc.status = 1 AND dc.queued_id = ".$consultation['Queue']['id']);
                        $num_rows = mysql_num_rows($queryComplain);
                        if($num_rows != 0 ){  
                            while ($result = mysql_fetch_array($queryComplain)) {
                                $text .= $result['name'] ."       ,";
                            }
                            echo rtrim($text,",");
                        }  
                        if(!empty($consultation['PatientConsultation']['chief_complain'])) { 
                            echo nl2br($consultation['PatientConsultation']['chief_complain']); 
                        }                             
                        ?>
                    </td>
                </tr>  
                <tr>
                    <td style="border: none;">
                        <?php echo TABLE_DAIGNOSTIC; ?> :
                        <?php echo $consultation['PatientConsultation']['daignostic']." <br/>".$consultation['PatientConsultation']['daignostic_other_info']; ?></td>
                </tr>          
            </table>    
        </div>
        <div class="clear"></div>
        
        <p><b style="font-size: 14px; font-style: italic;"><?php echo MENU_REMARKS; ?>: </b><?php echo $consultation['PatientConsultation']['remark']; ?></p>
        
        <div style="width: 50%; float: right; padding-top: 20%;">
            <table class="table" cellspacing='0' cellpadding='0' style="padding: 0; margin: 0;">
                <tr>
                    <td style="border: none; text-align: center;">Phnom Penh <?php echo date("d/m/Y", strtotime($consultation['PatientConsultation']['created'])); ?></td>
                </tr>
                <tr>
                    <td style="text-align: center; border: none;">........................................................</td>
                </tr>
                <tr>
                    <td style="text-align: center; border: none;"><?php echo $user['User']['first_name'].' '.$user['User']['last_name'];?></td>
                </tr>
                <tr style="display: none;">
                    <td style="text-align: center; border: none;">Medical license 046</td>
                </tr>
            </table>
        </div>
    <?php endforeach; ?>

    <br />
    <div style="clear:both;"></div>
    <div style="float:left;width: 450px">
        <div>
            <input type="button" value="<?php echo ACTION_PRINT; ?>" id='btnDisappearPrint' class='noprint'>
        </div>
    </div>
    <div style="clear:both"></div>
</div>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(document).dblclick(function() {
            window.close();
        });
        $("#btnDisappearPrint").click(function() {
            $("#footerTablePrint").show();
            $("#footerTablePrint").css("width", "100%");
            window.print();
            window.close();
        });
    });
</script>