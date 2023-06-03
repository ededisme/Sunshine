<?php

/**
 * Description of Helper
 *
 * @author UDAYA
 */
date_default_timezone_set('Asia/Phnom_Penh');

class InventoryComponent extends Object {
    
    function saveInventory($data){
        // Insert Inventory
        $sqlInventory = $this->insertInventory($data, 1, $data['module_type'], "inventories");
        $this->querySql($sqlInventory);
        
        // Insert Inventory Location
        $tblInventory = $data['location_id']."_inventories";
        $sqlInventoryLoc = $this->insertInventory($data, 1, $data['module_type'], $tblInventory);
        $this->querySql($sqlInventoryLoc);
        
        // Insert Inventory Total
        $sqlInventoryTotal = $this->insertInventory($data, 2, $data['module_type'], "inventory_totals");
        $this->querySql($sqlInventoryTotal);
        
        // Insert Inventory Total Location
        $tblTotal = $data['location_id']."_inventory_totals";
        $sqlInventoryTotalLoc = $this->insertInventory($data, 3, $data['module_type'], $tblTotal);
        $this->querySql($sqlInventoryTotalLoc);
        
        // Insert Inventory Total Detail Location
        $tblTotalDetail = $data['location_id']."_inventory_total_details";
        $sqlInventoryTotalDetail = $this->insertInventory($data, 4, $data['module_type'], $tblTotalDetail);
        $this->querySql($sqlInventoryTotalDetail);
        
        // Insert Group Total
        $tblGroupTotal = $data['location_group_id']."_group_totals";
        $sqlGroupTotal = $this->insertInventory($data, 5, $data['module_type'], $tblGroupTotal);
        $this->querySql($sqlGroupTotal);
    }
    
    function saveGroupTotalDetail($data){
        // Insert Group Total
        $sqlGroupTotalDetail = $this->insertGroupTotalDetail($data);
        $this->querySql($sqlGroupTotalDetail);
    }
    
    function saveGroupQtyOrder($locationGroupId, $locationId, $productId, $lotsNum, $expDate, $qtyOrder, $date, $symbol){
        $sym = '';
        if($symbol == "-"){
            $sym = '-';
        }
        if($lotsNum == ''){
            $lotsNum = 0;
        }
        if($expDate == ''){
            $expDate = '0000-00-00';
        }
        // Group Totals
        mysql_query("INSERT INTO ".$locationGroupId."_group_totals (product_id, lots_number, expired_date, location_id, location_group_id, total_order) 
                     VALUES (".$productId.", '".$lotsNum."', '".$expDate."', ".$locationId.", ".$locationGroupId.", ".$sym.$qtyOrder.") 
                     ON DUPLICATE KEY UPDATE total_order = (total_order ".$symbol." ".$qtyOrder.")");
        // Group Total Details
        mysql_query("INSERT INTO ".$locationGroupId."_group_total_details (product_id, location_group_id, date, total_order) 
                     VALUES (".$productId.", ".$locationGroupId.", '".$date."', ".$sym.$qtyOrder.") 
                     ON DUPLICATE KEY UPDATE total_order = (total_order ".$symbol." ".$qtyOrder.")");
        // Inventory Totals
        mysql_query("INSERT INTO ".$locationId."_inventory_totals (product_id, lots_number, expired_date, location_id, total_order) 
                     VALUES (".$productId.", '".$lotsNum."', '".$expDate."', ".$locationId.", ".$sym.$qtyOrder.")
                     ON DUPLICATE KEY UPDATE total_order = (total_order ".$symbol." ".$qtyOrder.")");
        // Inventory Total Details
        mysql_query("INSERT INTO ".$locationId."_inventory_total_details (product_id, lots_number, expired_date, location_id, date, total_order) 
                     VALUES (".$productId.", '".$lotsNum."', '".$expDate."', ".$locationId.", '".$date."', ".$sym.$qtyOrder.") 
                     ON DUPLICATE KEY UPDATE total_order = (total_order ".$symbol." ".$qtyOrder.")");
        // Inventory Totals (All)
        mysql_query("INSERT INTO inventory_totals (product_id, lots_number, expired_date, total_order) 
                     VALUES (".$productId.", '".$lotsNum."', '".$expDate."', ".$sym.$qtyOrder.")
                     ON DUPLICATE KEY UPDATE total_order = (total_order ".$symbol." ".$qtyOrder.")");
    }
    
    function querySql($sql){
        mysql_query($sql) or die(mysql_error());
    }
    
    function insertGroupTotalDetail($data){
        $sqlQuery   = "";
        $tableName  = $data['location_group_id']."_group_total_details";
        $moduleType = $data['module_type'];
        $fieldList  = $this->checkModule($moduleType);
        $field      = "";
        $values     = "";
        $doplicate  = "";
        $calType    = $fieldList['module_operator']=="-"?$fieldList['module_operator']:"";
        
        // List Field, Value, Doplicate
        $field .= "(`product_id`, `location_group_id`,";
        $field .= " `".$fieldList['filed']."`";
        $doplicate .= "`".$fieldList['filed']."` = (`".$fieldList['filed']."` ".$fieldList['module_operator']." ".$data['total_order'].")";
        if($fieldList['filed_free'] != ""){
            $field .= ", `".$fieldList['filed_free']."`";
            $doplicate .= ", `".$fieldList['filed_free']."` = (`".$fieldList['filed_free']."` ".$fieldList['module_operator']." ".$data['total_free'].")";
        }
        $field .= ", `date`)";
        $values .= "(";
        $values .= $data['product_id'];
        $values .= ",".$data['location_group_id'];
        $values .= ",".$calType.$data['total_order'];
        if($fieldList['filed_free'] != ""){
            $values .= ",".$calType.$data['total_free'];
        }
        $values .= ",'".$data['date']."'";
        $values .= ")";
        // Get SQL
        $sqlQuery .= $this->getInsert();
        $sqlQuery .= $tableName." ";
        $sqlQuery .= $field;
        $sqlQuery .= $this->getValue();
        $sqlQuery .= $values;
        $sqlQuery .= $this->getDoplicate();
        $sqlQuery .= $doplicate;
        $sqlQuery .= ";";
        return $sqlQuery;
    }
    
    function insertInventory($data, $type, $modueType, $tableName){
        $sqlQuery    = "";
        $module      = $this->checkModule($modueType);
        $qtyOperator = $module['qty_operator'];
        $modOperator = $module['module_operator'];
        $field   = "(";
        $fileds  = $this->getFieldByType($type);
        $values  = "(";
        // Get Field & Value IF Inventory
        if($type == 1){
            $field .= "`".$module['module']."`,";
            // Value
            $values .= "'".$data[$module['module']]."'";
            $values .= ",'".$module['type']."'";
            $values .= ",'".$data['product_id']."'";
            $values .= ",'".$data['location_id']."'";
            $values .= ",'".$data['location_group_id']."'";
            $values .= ",'".($qtyOperator=="-"?$qtyOperator:"").$data['total_qty']."'";
            $values .= ",'".$data['unit_cost']."'";
            $values .= ",'".$data['unit_price']."'";
            $values .= ",'".$data['date']."'";
            $values .= ",'".$data['lots_number']."'";
            $values .= ",'".$data['expired_date']."'";
            $values .= ",".$this->checkValueNull($data['customer_id']);
            $values .= ",".$this->checkValueNull($data['vendor_id']);
            $values .= ",'".date("Y-m-d H:i:s")."'";
            $values .= ",'".$data['user_id']."'";
            $values .= ",'".date("Y-m-d H:i:s")."'";
            $values .= ",'".$data['user_id']."'";
        }
        // List Field & Value Default
        $length = count($fileds);
        $index  = 1;
        foreach($fileds AS $filedList){
            if($index != $length){
                $symbol = ",";
            }else{
                $symbol = "";
            }
            $field .= "`".$filedList."`".$symbol;
            if($type != 1){
                if($filedList == 'total_qty'){
                    $operator = $qtyOperator=="-"?$qtyOperator:"";
                }else{
                    $operator = "";
                }
                $values .= "'".$operator.$data[$filedList]."'".$symbol;
            }
            $index++;
        }
        // Filed & Value of Module
        if($type == 4){
                $operator = $modOperator=="-"?$modOperator:"";
                $field   .= ",`".$module['filed']."`";
                $values  .= ",'".$operator.$data['total_qty']."'";
        }else if($type == 2){
            $operator = $modOperator=="-"?$modOperator:"";
            $field   .= ",`".$module['filed']."`";
            $values  .= ",'".$operator.$data['total_order']."'";
            if($module['filed_free'] != ""){
                $field   .= ",`".$module['filed_free']."`";
                $values  .= ",'".$operator.$data['total_free']."'";
            }
        }
        
        $field .= ")";
        $values .= ")";
        $doplicate  = "";
        // Get Doplicate Insert
        if($type == 2){
            $doplicate .= "`total_qty` = (`total_qty`".$qtyOperator.$data['total_qty']."), `".$module['filed']."` = (`".$module['filed']."`".$modOperator.$data['total_order'].")";
            if($module['filed_free'] != ""){
                $doplicate .= ", `".$module['filed_free']."` = (`".$module['filed_free']."` ".$modOperator." ".$data['total_free'].")";
            }
        }else if($type == 3 || $type == 5){
            $doplicate .= "`total_qty` = (`total_qty`".$qtyOperator.$data['total_qty'].")";
        }else if($type == 4){
            $doplicate .= "`".$module['filed']."` = (`".$module['filed']."`".$modOperator.$data['total_qty'].")";
        }
        // Get SQL
        $sqlQuery .= $this->getInsert();
        $sqlQuery .= $tableName." ";
        $sqlQuery .= $field;
        $sqlQuery .= $this->getValue();
        $sqlQuery .= $values;
        if($doplicate != ""){
            $sqlQuery .= $this->getDoplicate();
            $sqlQuery .= $doplicate;
        }
        $sqlQuery .= ";";
        return $sqlQuery;
    }
    
    function getFieldByType($type){
        $result = array();
        switch ($type) {
            // Inventory
            case '1':
                $result[] = "type";
                $result[] = "product_id";
                $result[] = "location_id";
                $result[] = "location_group_id";
                $result[] = "qty";
                $result[] = "unit_cost";
                $result[] = "unit_price";
                $result[] = "date";
                $result[] = "lots_number";
                $result[] = "date_expired";
                $result[] = "customer_id";
                $result[] = "vendor_id";
                $result[] = "created";
                $result[] = "created_by";
                $result[] = "modified";
                $result[] = "modified_by";
                break;
            // Inventory Total
            case '2':
                $result[] = "product_id";
                $result[] = "lots_number";
                $result[] = "expired_date";
                $result[] = "total_qty";
                break;
            // Inventory Total SELF
            case '3':
                $result[] = "product_id";
                $result[] = "lots_number";
                $result[] = "expired_date";
                $result[] = "location_id";
                $result[] = "total_qty";
                break;
            // Inventory Total Detail SELF
            case '4':
                $result[] = "product_id";
                $result[] = "lots_number";
                $result[] = "expired_date";
                $result[] = "location_id";
                $result[] = "date";
                break;
            // Group Total Detail SELF
            case '5':
                $result[] = "product_id";
                $result[] = "lots_number";
                $result[] = "expired_date";
                $result[] = "location_id";
                $result[] = "location_group_id";
                $result[] = "total_qty";
                break;
        }
        return $result;
    }
    
    function checkModule($moduleType = null){
        $result = array();
        if(!empty($moduleType)){
            $result['error'] = 0;
            switch ($moduleType){
                case '1':
                    $result['type']   = "Inv Adj";
                    $result['module'] = "cycle_product_id";
                    $result['filed']  = "total_cycle";
                    $result['filed_free'] = "";
                    $result['qty_operator']    = "+";
                    $result['module_operator'] = "+";
                    break;
                case '2':
                    $result['type']   = "Transfer In";
                    $result['module'] = "transfer_order_id";
                    $result['filed']  = "total_to_in";
                    $result['filed_free'] = "";
                    $result['qty_operator']    = "+";
                    $result['module_operator'] = "+";
                    break;
                case '3':
                    $result['type']   = "Transfer Out";
                    $result['module'] = "transfer_order_id";
                    $result['filed']  = "total_to_out";
                    $result['filed_free'] = "";
                    $result['qty_operator']    = "-";
                    $result['module_operator'] = "+";
                    break;
                case '4':
                    $result['type']   = "Void Transfer Out";
                    $result['module'] = "transfer_order_id";
                    $result['filed']  = "total_to_in";
                    $result['filed_free'] = "";
                    $result['qty_operator']    = "+";
                    $result['module_operator'] = "-";
                    break;
                case '5':
                    $result['type']   = "Void Transfer In";
                    $result['module'] = "transfer_order_id";
                    $result['filed']  = "total_to_out";
                    $result['filed_free'] = "";
                    $result['qty_operator']    = "-";
                    $result['module_operator'] = "-";
                    break;
                case '6':
                    $result['type']   = "Purchase";
                    $result['module'] = "purchase_order_id";
                    $result['filed']  = "total_pb";
                    $result['filed_free'] = "";
                    $result['qty_operator']    = "+";
                    $result['module_operator'] = "+";
                    break;
                case '7':
                    $result['type']   = "Purchase Return";
                    $result['module'] = "purchase_return_id";
                    $result['filed']  = "total_pbc";
                    $result['filed_free'] = "";
                    $result['qty_operator']    = "-";
                    $result['module_operator'] = "+";
                    break;
                case '8':
                    $result['type']   = "POS";
                    $result['module'] = "point_of_sales_id";
                    $result['filed']  = "total_pos";
                    $result['filed_free'] = "total_pos_free";
                    $result['qty_operator']    = "-";
                    $result['module_operator'] = "+";
                    break;
                case '9':
                    $result['type']   = "Void POS";
                    $result['module'] = "point_of_sales_id";
                    $result['filed']  = "total_pos";
                    $result['filed_free'] = "total_pos_free";
                    $result['qty_operator']    = "+";
                    $result['module_operator'] = "-";
                    break;
                case '10':
                    $result['type']   = "Sale";
                    $result['module'] = "sales_order_id";
                    $result['filed']  = "total_so";
                    $result['filed_free'] = "total_so_free";
                    $result['qty_operator']    = "-";
                    $result['module_operator'] = "+";
                    break;
                case '11':
                    $result['type']   = "Sales Return";
                    $result['module'] = "credit_memo_id";
                    $result['filed']  = "total_cm";
                    $result['filed_free'] = "total_cm_free";
                    $result['qty_operator']    = "+";
                    $result['module_operator'] = "+";
                    break;
                case '12':
                    $result['type']   = "Customer Consignment In";
                    $result['module'] = "consignment_id";
                    $result['filed']  = "total_cus_consign_in";
                    $result['filed_free'] = "";
                    $result['qty_operator']    = "+";
                    $result['module_operator'] = "+";
                    break;
                case '13':
                    $result['type']   = "Customer Consignment Out";
                    $result['module'] = "consignment_id";
                    $result['filed']  = "total_cus_consign_out";
                    $result['filed_free'] = "";
                    $result['qty_operator']    = "-";
                    $result['module_operator'] = "+";
                    break;
                case '14':
                    $result['type']   = "Customer Return Consignment In";
                    $result['module'] = "consignment_return_id";
                    $result['filed']  = "total_cus_consign_in";
                    $result['filed_free'] = "";
                    $result['qty_operator']    = "+";
                    $result['module_operator'] = "+";
                    break;
                case '15':
                    $result['type']   = "Customer Return Consignment Out";
                    $result['module'] = "consignment_return_id";
                    $result['filed']  = "total_cus_consign_out";
                    $result['filed_free'] = "";
                    $result['qty_operator']    = "-";
                    $result['module_operator'] = "+";
                    break;
                case '16':
                    $result['type']   = "Vendor Consignment";
                    $result['module'] = "vendor_consignment_id";
                    $result['filed']  = "total_ven_consign_in";
                    $result['filed_free'] = "";
                    $result['qty_operator']    = "+";
                    $result['module_operator'] = "+";
                    break;
                case '17':
                    $result['type']   = "Vendor Return Consignment";
                    $result['module'] = "vendor_consignment_return_id";
                    $result['filed']  = "total_ven_consign_out";
                    $result['filed_free'] = "";
                    $result['qty_operator']    = "-";
                    $result['module_operator'] = "+";
                    break;
                case '18':
                    $result['type']   = "Void Purchase";
                    $result['module'] = "purchase_order_id";
                    $result['filed']  = "total_pb";
                    $result['filed_free'] = "";
                    $result['qty_operator']    = "-";
                    $result['module_operator'] = "-";
                    break;
                case '19':
                    $result['type']   = "Void Sales Return";
                    $result['module'] = "credit_memo_id";
                    $result['filed']  = "total_cm";
                    $result['filed_free'] = "total_cm_free";
                    $result['qty_operator']    = "-";
                    $result['module_operator'] = "-";
                    break;
                case '20':
                    $result['type']   = "Void Purchase Return";
                    $result['module'] = "purchase_return_id";
                    $result['filed']  = "total_pbc";
                    $result['filed_free'] = "";
                    $result['qty_operator']    = "+";
                    $result['module_operator'] = "-";
                    break;
                case '21':
                    $result['type']   = "Void Sale";
                    $result['module'] = "sales_order_id";
                    $result['filed']  = "total_so";
                    $result['filed_free'] = "total_so_free";
                    $result['qty_operator']    = "+";
                    $result['module_operator'] = "-";
                    break;
            }
        }else{
            $result['error'] = 1;
        }
        return $result;
    }
    
    function getInsert(){
        return "INSERT INTO ";
    }
    
    function getValue(){
        return " VALUES ";
    }
    
    function getDoplicate(){
        return " ON DUPLICATE KEY UPDATE ";
    }
    
    function checkValueNull($value){
        if($value == ""){
            $value = "NULL";
        }else{
            $value = "'".$value."'";
        }
        return $value;
    }
    
}

?>