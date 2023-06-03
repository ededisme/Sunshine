<?php

class PointOfSalesController extends AppController {
    var $name = 'PointOfSales';
    var $components = array('Helper', 'Inventory');
    var $uses = array("SalesOrder");

    function getProductByCode($barcode = null, $locationGroupId = null, $company_id = null, $branchId = null, $priceTypeId = null) {
        $this->layout = 'ajax';
        if ($barcode != null && $barcode != '') {
            $user       = $this->getCurrentUser();
            $orderDate  = date('Y-m-d');
            $locSetting = ClassRegistry::init('LocationSetting')->findById(4);
            $locCon     = '';
            $result     = "";
            if($locSetting['LocationSetting']['location_status'] == 1){
                $locCon = ' AND is_for_sale = 1';
            }
            // Check Warehouse Option Allow Negative
            $warehouseOption    = ClassRegistry::init('LocationGroup')->findById($locationGroupId);
            $joinInventory      = array('table' => $locationGroupId.'_group_totals', 'type' => 'LEFT', 'alias' => 'InventoryTotal', 'conditions' => array('InventoryTotal.product_id = Product.id', 'InventoryTotal.location_group_id' => $locationGroupId, 'InventoryTotal.total_qty > 0', 'InventoryTotal.location_id IN (SELECT id FROM locations WHERE location_group_id = '.$locationGroupId.' AND is_active = 1'.$locCon.' AND ((is_not_for_sale = 0 AND period_from IS NULL AND period_to IS NULL) OR (is_not_for_sale = 0 AND period_from <= "'.$orderDate.'" AND period_to >= "'.$orderDate.'") OR (is_not_for_sale = 1 AND period_from IS NOT NULL AND period_to IS NOT NULL AND "'.$orderDate.'" NOT BETWEEN period_from AND period_to)) AND Product.company_id = '.$company_id.')'));
            $joinProductgroup   = array('table' => 'product_pgroups', 'type' => 'INNER', 'alias' => 'ProductPgroup', 'conditions' => array('ProductPgroup.product_id = Product.id'));
            $joinPgroup         = array('table' => 'pgroups', 'type' => 'INNER', 'alias' => 'Pgroup', 'conditions' => array('Pgroup.id = ProductPgroup.pgroup_id','(Pgroup.user_apply = 0 OR (Pgroup.user_apply = 1 AND Pgroup.id IN (SELECT pgroup_id FROM user_pgroups WHERE user_id = '.$user['User']['id'].')))'));
            $joinProductBranch  = array('table' => 'product_branches','type' => 'INNER','alias' => 'ProductBranch','conditions' => array('ProductBranch.product_id = Product.id','ProductBranch.branch_id' => $branchId));
            $joins = array($joinInventory, $joinProductgroup, $joinPgroup, $joinProductBranch, array('table' => 'product_with_skus','type' => 'LEFT','alias' => 'ProductWithSku','conditions' => array('ProductWithSku.product_id = Product.id')));
            $product = ClassRegistry::init('Product')->find('first', array(
                    'conditions' => array('Product.is_active' => 1, 'Product.company_id' => $company_id,
                    "OR" => array(
                        'trim(Product.code) = ("' . mysql_real_escape_string(trim($barcode)) . '")',
                        'trim(ProductWithSku.sku) = ("' . mysql_real_escape_string(trim($barcode)) . '")'
                    )), 'joins' => $joins, 'fields' => array('Product.*', 'IF(COUNT(`ProductWithSku`.id) = 1,`ProductWithSku`.uom_id,"") as sku_uom_id', 'InventoryTotal.total_qty', 'InventoryTotal.total_order'), 'group' => array('Product.id', 'Product.code')));
            if (empty($product)) {
                $product['Product']['id'] = '';
            } else {
                $product['Product']['barcode'] = $barcode;
                $product['Product']['uom_id']  = $product[0]['sku_uom_id'];
                // Check Packet
                if($product['Product']['is_packet'] == 1){
                    $sqlPacket = mysql_query("SELECT group_concat(CONCAT(IFNULL((SELECT sku FROM product_with_skus WHERE product_id= product_with_packets.packet_product_id AND uom_id = product_with_packets.qty_uom_id), (SELECT code FROM products WHERE id = product_with_packets.packet_product_id)),'||',qty_uom_id,'||',qty,'||',conversion)) FROM product_with_packets WHERE main_product_id =".$product['Product']['id']);
                    $rowPacket = mysql_fetch_array($sqlPacket);
                    $product['Product']['packet'] = $rowPacket[0];
                    $product['InventoryTotal']['total_qty'] = ($product['InventoryTotal']['total_qty'] - $product['InventoryTotal']['total_order']);
                }else{
                    $product['Product']['packet'] = '';
                }
                // Check Allow Negative Stock
                if($warehouseOption['LocationGroup']['allow_negative_stock'] == 1){ // Allow Negative Stock
                    $product['InventoryTotal']['total_qty'] = 1000000;
                    $product['InventoryTotal']['total_order'] = 0;
                }
                // Check Total Qty for get UoM
                if($product['InventoryTotal']['total_qty'] > 0){
                    $query    = mysql_query("SELECT id,name,abbr,1 AS conversion FROM uoms WHERE id=".$product['Product']['price_uom_id']."
                                             UNION
                                             SELECT id,name,abbr,(SELECT value FROM uom_conversions WHERE is_active=1 AND from_uom_id=".$product['Product']['price_uom_id']." AND to_uom_id=uoms.id) AS conversion FROM uoms WHERE id IN (SELECT to_uom_id FROM uom_conversions WHERE is_active=1 AND from_uom_id=".$product['Product']['price_uom_id'].")
                                             ORDER BY conversion ASC");
                    $i = 1;
                    $length = mysql_num_rows($query);
                    while($data=mysql_fetch_array($query)){
                        $priceLbl   = "";
                        $dataItem   = "other";
                        $conversion = $data['conversion'];
                        if($length == $i){ $uom_sm = 1; } else { $uom_sm = 0; }
                        if($data['id'] == $product['Product']['price_uom_id']){ $dataItem = "first"; }
                        $sqlPrice = mysql_query("SELECT price_type_id, amount, percent, add_on, set_type FROM product_prices WHERE product_id =".$product['Product']['id']." AND branch_id =".$branchId." AND uom_id =".$data['id']." AND price_type_id = ".$priceTypeId);
                        if(mysql_num_rows($sqlPrice)){
                            $price = 0;
                            while($rowPrice = mysql_fetch_array($sqlPrice)){
                                $unitCost = $product['Product']['unit_cost'] /  $data['conversion'];
                                if($rowPrice['set_type'] == 1){
                                    $price = $rowPrice['amount'];
                                }else if($rowPrice['set_type'] == 2){
                                    $percent = ($unitCost * $rowPrice['percent']) / 100;
                                    $price = $unitCost + $percent;
                                }else if($rowPrice['set_type'] == 3){
                                    $price = $unitCost + $rowPrice['add_on'];
                                }
                                $priceLbl  = "price='".$price."'";
                            }
                        }else{
                            $priceLbl = "price='0'";
                        }
                        $result .= "<option data-item='{$dataItem}' {$priceLbl} uom-sm='{$uom_sm}' conversion='{$conversion}' value='{$data['id']}'>" . $data['abbr'] . "</option>";
                        $i++;
                    }  
                }
                $product['Product']['uom_list'] = $result;
            }
            echo json_encode($product);
            exit;
        }
    }
    
    function getTotalQtyByLotExp($productId = null, $locationGroupId = null, $lotNumber = null, $expiry = null){
        $this->layout = 'ajax';
        $totalQty = array();
        if(empty($productId) || empty($locationGroupId)){
            $totalQty['total'] = 0;
            echo json_encode($totalQty);
            exit;
        }
        if(empty($lotNumber)){
            $lotNumber = 0;
        }
        if(empty($expiry)){
            $expiry = '0000-00-00';
        }
        // Check Warehouse Option Allow Negative
        $warehouseOption    = ClassRegistry::init('LocationGroup')->findById($locationGroupId);
        // Check Allow Negative Stock
        if($warehouseOption['LocationGroup']['allow_negative_stock'] == 1){ // Allow Negative Stock
            $totalQty['total'] = 1000000;
        } else {
            $locSetting = ClassRegistry::init('LocationSetting')->findById(4);
            $locCon     = '';
            if($locSetting['LocationSetting']['location_status'] == 1){
                $locCon = ' AND is_for_sale = 1';
            }
            $sqlStock = mysql_query("SELECT IFNULL(SUM(total_qty - total_order), 0) AS total FROM ".$locationGroupId."_group_totals WHERE product_id = ".$productId." AND lots_number = '".$lotNumber."' AND expired_date = '".$expiry."' AND location_id IN (SELECT id FROM locations WHERE location_group_id = ".$locationGroupId." AND is_active = 1".$locCon.")");
            if(mysql_num_rows($sqlStock)){
                $rowStock = mysql_fetch_array($sqlStock);
                $totalQty['total'] = $rowStock[0];
            } else {
                $totalQty['total'] = $rowStock[0];
            }
        }
        echo json_encode($totalQty);
        exit;
    }

    function changeDiscount() {
        $this->layout = 'ajax';
    }

    function productDiscount() {
        $this->layout = 'ajax';
    }

    function printReceipt($salesOrderId = null) {
        $this->layout = 'ajax';
        if (!empty($salesOrderId)) {
            $salesOrderReceipt = ClassRegistry::init('SalesOrderReceipt')->find("first", array('conditions' => array('SalesOrderReceipt.sales_order_id' => $salesOrderId, 'SalesOrderReceipt.is_void' => 0)));

            $salesOrder = ClassRegistry::init('SalesOrder')->find("first", array('conditions' => array('SalesOrder.id' => $salesOrderId)));
            $lastExchangeRate = ClassRegistry::init('ExchangeRate')->find("first", array(
                "conditions" => array("ExchangeRate.is_active" => 1),
                "order" => array("ExchangeRate.created desc")
                    )
            );
            $salesOrderDetails = ClassRegistry::init('SalesOrderDetail')->find("all", array('conditions' => array('SalesOrderDetail.sales_order_id' => $salesOrderId)));
            $salesOrderServices = ClassRegistry::init('SalesOrderService')->find("all", array('conditions' => array('SalesOrderService.sales_order_id' => $salesOrder['SalesOrder']['id'])));
            // Currency Other
            $otherSymbolCur = '';
            if($salesOrderReceipt['SalesOrderReceipt']['exchange_rate_id'] != ''){
                $sqlOtherCur   = mysql_query("SELECT currency_centers.symbol FROM exchange_rates INNER JOIN currency_centers ON currency_centers.id = exchange_rates.currency_center_id WHERE exchange_rates.id = ".$salesOrderReceipt['SalesOrderReceipt']['exchange_rate_id']." LIMIT 1");
                if(mysql_num_rows($sqlOtherCur)){
                    $rowOtherCur    = mysql_fetch_array($sqlOtherCur);
                    $otherSymbolCur = $rowOtherCur[0];
                }
            }
            $this->set(compact('otherSymbolCur', 'salesOrder', 'salesOrderDetails', 'salesOrderReceipt', 'lastExchangeRate', 'salesOrderServices'));
        } else {
            exit;
        }
    }

    function void($id = null) {
        if (!$id) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $r = 0;
        $restCode = array();
        $dateNow  = date("Y-m-d H:i:s");
        $user = $this->getCurrentUser();
        $this->loadModel('SalesOrder');
        $this->loadModel('GeneralLedger');
        $this->loadModel('InventoryValuation');
        $salesOrder = ClassRegistry::init('SalesOrder')->find("first", array('conditions' => array('SalesOrder.id' => $id)));
        if($salesOrder['SalesOrder']['status'] == 2){
            $salesOrderDetails = ClassRegistry::init('SalesOrderDetail')->find("all", array('conditions' => array('SalesOrderDetail.sales_order_id' => $id)));
            $posPickDetails    = ClassRegistry::init('PosPickDetail')->find("all", array('conditions' => array('PosPickDetail.sales_order_id' => $id)));
            $dateSale = $salesOrder['SalesOrder']['order_date'];
            // Update Product In Location Group
            foreach ($salesOrderDetails as $salesOrderDetail) {
                $totalQtyOrder = (($salesOrderDetail['SalesOrderDetail']['qty'] + $salesOrderDetail['SalesOrderDetail']['qty_free']) * $salesOrderDetail['SalesOrderDetail']['conversion']);
                $qtyOrder      = ($salesOrderDetail['SalesOrderDetail']['qty'] * $salesOrderDetail['SalesOrderDetail']['conversion']);
                $qtyFree       = ($salesOrderDetail['SalesOrderDetail']['qty_free'] * $salesOrderDetail['SalesOrderDetail']['conversion']);
                // Update Inventory
                $dataGroup = array();
                $dataGroup['module_type']       = 9;
                $dataGroup['point_of_sales_id'] = $id;
                $dataGroup['product_id']        = $salesOrderDetail['SalesOrderDetail']['product_id'];
                $dataGroup['location_group_id'] = $salesOrder['SalesOrder']['location_group_id'];
                $dataGroup['date']         = $dateSale;
                $dataGroup['total_qty']    = $totalQtyOrder;
                $dataGroup['total_order']  = $qtyOrder;
                $dataGroup['total_free']   = $qtyFree;
                // Update Inventory Group
                $this->Inventory->saveGroupTotalDetail($dataGroup);
                // Convert to REST
                $restCode[$r]['module_type']       = 9;
                $restCode[$r]['point_of_sales_id'] = $this->Helper->getSQLSyncCode("sales_orders", $id);
                $restCode[$r]['date']              = $dateSale;
                $restCode[$r]['total_qty']         = $totalQtyOrder;
                $restCode[$r]['total_order']       = $qtyOrder;
                $restCode[$r]['total_free']        = $qtyFree;
                $restCode[$r]['product_id']        = $this->Helper->getSQLSyncCode("products", $salesOrderDetail['SalesOrderDetail']['product_id']);
                $restCode[$r]['location_group_id'] = $this->Helper->getSQLSyncCode("location_groups", $salesOrder['SalesOrder']['location_group_id']);
                $restCode[$r]['dbtype']  = 'GroupDetail';
                $restCode[$r]['actodo']  = 'inv';
                $r++;

            }
            // Update Product By Lot & Exp Date
            foreach($posPickDetails AS $posPickDetail){
                $totalOrder = $posPickDetail['PosPickDetail']['total_qty'];
                // Update Inventory (Void POS)
                $data = array();
                $data['module_type']       = 9;
                $data['point_of_sales_id'] = $id;
                $data['product_id']        = $posPickDetail['PosPickDetail']['product_id'];
                $data['location_id']       = $posPickDetail['PosPickDetail']['location_id'];
                $data['location_group_id'] = $salesOrder['SalesOrder']['location_group_id'];
                $data['lots_number']  = $posPickDetail['PosPickDetail']['lots_number'];
                $data['expired_date'] = $posPickDetail['PosPickDetail']['expired_date'];
                $data['date']         = $dateSale;
                $data['total_qty']    = $totalOrder;
                $data['total_order']  = $totalOrder;
                $data['total_free']   = 0;
                $data['user_id']      = $user['User']['id'];
                $data['customer_id']  = $salesOrder['SalesOrder']['customer_id'];
                $data['vendor_id']    = "";
                $data['unit_price']   = 0;
                $data['unit_cost']    = 0;
                // Update Invetory Location
                $this->Inventory->saveInventory($data);
                // Convert to REST
                $restCode[$r] = $this->Helper->convertToDataSync($data, 'inventories');
                $restCode[$r]['module_type']  = 9;
                $restCode[$r]['total_qty']    = $totalOrder;
                $restCode[$r]['total_order']  = $totalOrder;
                $restCode[$r]['total_free']   = 0;
                $restCode[$r]['expired_date'] = $posPickDetail['PosPickDetail']['expired_date'];
                $restCode[$r]['vendor_id']    = "";
                $restCode[$r]['unit_cost']    = 0;
                $restCode[$r]['unit_price']   = 0;
                $restCode[$r]['customer_id']  = $this->Helper->getSQLSyncCode("products", $salesOrder['SalesOrder']['customer_id']);
                $restCode[$r]['point_of_sales_id'] = $this->Helper->getSQLSyncCode("sales_orders", $id);
                $restCode[$r]['product_id']        = $this->Helper->getSQLSyncCode("products", $posPickDetail['PosPickDetail']['product_id']);
                $restCode[$r]['location_id']       = $this->Helper->getSQLSyncCode("locations", $posPickDetail['PosPickDetail']['location_id']);
                $restCode[$r]['location_group_id'] = $this->Helper->getSQLSyncCode("location_groups", $salesOrder['SalesOrder']['location_group_id']);
                $restCode[$r]['user_id']           = $this->Helper->getSQLSyncCode("users", $user['User']['id']);
                $restCode[$r]['dbtype']  = 'saveInv';
                $restCode[$r]['actodo']  = 'inv';
                $r++;
            }

            $this->SalesOrder->updateAll(
                    array('SalesOrder.status' => 0, 'SalesOrder.modified_by' => $user['User']['id']), array('SalesOrder.id' => $id)
            );
            // Convert to REST
            $restCode[$r]['status']      = 0;
            $restCode[$r]['modified']    = $dateNow;
            $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
            $restCode[$r]['dbtodo'] = 'sales_orders';
            $restCode[$r]['actodo'] = 'ut';
            $restCode[$r]['con']    = "sys_code = '".$salesOrder['SalesOrder']['sys_code']."'";
            $r++;
            $this->InventoryValuation->updateAll(
                    array('InventoryValuation.is_active' => "2"), array('InventoryValuation.point_of_sales_id' => $salesOrder['SalesOrder']['id'])
            );
            // Convert to REST
            $restCode[$r]['is_active']   = 2;
            $restCode[$r]['dbtodo'] = 'inventory_valuations';
            $restCode[$r]['actodo'] = 'ut';
            $restCode[$r]['con']    = "sales_order_id = (SELECT id FROM sales_orders WHERE sys_code = '".$salesOrder['SalesOrder']['sys_code']."' LIMIT 1)";
            $r++;
            $this->GeneralLedger->updateAll(
                    array('GeneralLedger.is_active' => 2), array('GeneralLedger.sales_order_id' => $id)
            );
            // Convert to REST
            $restCode[$r]['is_active']   = 2;
            $restCode[$r]['modified']    = $dateNow;
            $restCode[$r]['modified_by'] = $this->Helper->getSQLSysCode("users", $user['User']['id']);
            $restCode[$r]['dbtodo'] = 'general_ledgers';
            $restCode[$r]['actodo'] = 'ut';
            $restCode[$r]['con']    = "sales_order_id = (SELECT id FROM sales_orders WHERE sys_code = '".$salesOrder['SalesOrder']['sys_code']."' LIMIT 1)";
            
            // Save File Send
            $this->Helper->sendFileToSync($restCode, 0, 0);
            // Recalculate Average Cost
            $sqlTrack = mysql_query("SELECT val FROM tracks WHERE id = 1");
            $track    = mysql_fetch_array($sqlTrack);
            $dateReca = $salesOrder['SalesOrder']['order_date'];
            $dateRun  = date("Y-m-d", strtotime(date("Y-m-d", strtotime($dateReca)) . " -1 day"));
            if($track[0] == "0000-00-00" || (strtotime($track[0]) >= strtotime($dateRun))){
                mysql_query("UPDATE tracks SET val='".$dateRun."', is_recalculate = 1 WHERE id=1");
            }
            $this->Helper->saveUserActivity($user['User']['id'], 'Point Of Sales', 'Void', $id);
            echo MESSAGE_DATA_HAS_BEEN_DELETED;
            exit;
        } else {
            $this->Helper->saveUserActivity($user['User']['id'], 'Point Of Sales (Error)', 'Void', $id);
            echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
            exit;
        }
    }

    function add(){
        $this->layout = 'pos';
        $user  = $this->getCurrentUser();
        if (!empty($this->data)) {
            $this->layout = 'ajax';
            $this->loadModel('SalesOrderReceipt');
            $this->loadModel('GeneralLedger');
            $this->loadModel('GeneralLedgerDetail');
            $this->loadModel('Company');
            $this->loadModel('Branch');
            $this->loadModel('AccountType');

            //  Find Chart Account
            $cashBankAccount  = $this->AccountType->findById(6);
            $arAccount        = $this->AccountType->findById(7);
            $salesDiscAccount = $this->AccountType->findById(11);
            $result          = array();
            $checkError      = 1;
            $listOutStock    = "";
            $totalPriceSales = 0;
            $jsonArray  = array();
            $branch     = $this->Branch->read(null, $this->data['PointOfSale']['branch_id']);
            $company    = $this->Company->read(null, $this->data['PointOfSale']['company_id']);
            $classId    = $this->Helper->getClassId($company['Company']['id'], $company['Company']['classes'], $this->data['PointOfSale']['location_group_id']);
            $productOrder = array();
            for ($i = 0; $i < sizeof($this->data['SalesOrderDetail']['product_id']); $i++) {
                if ($this->data['SalesOrderDetail']['product_id'][$i] != '') {
                    $expDate  = $this->data['SalesOrderDetail']['expired_date'][$i]!=''?$this->data['SalesOrderDetail']['expired_date'][$i]:'0000-00-00';
                    $keyIndex = $this->data['SalesOrderDetail']['product_id'][$i]."+".$expDate;
                    if (array_key_exists($keyIndex, $productOrder)){
                        $productOrder[$keyIndex]['qty'] += ($this->Helper->replaceThousand($this->data['SalesOrderDetail']['qty'][$i]) + $this->Helper->replaceThousand($this->data['SalesOrderDetail']['qty_free'][$i])) * $this->Helper->replaceThousand($this->data['SalesOrderDetail']['conversion'][$i]);
                    } else {
                        $productOrder[$keyIndex]['qty'] = ($this->Helper->replaceThousand($this->data['SalesOrderDetail']['qty'][$i]) + $this->Helper->replaceThousand($this->data['SalesOrderDetail']['qty_free'][$i])) * $this->Helper->replaceThousand($this->data['SalesOrderDetail']['conversion'][$i]);
                    }
                }
                // Make Sales Detail As Array
                $totalPriceSales                  += $this->Helper->replaceThousand($this->data['SalesOrderDetail']['total_price'][$i]) -  $this->Helper->replaceThousand($this->data['SalesOrderDetail']['discount_amount'][$i]);
                $jsonArray[$i]['discount_id']      = $this->Helper->replaceThousand($this->data['SalesOrderDetail']['discount_id'][$i]);
                $jsonArray[$i]['discount_amount']  = $this->Helper->replaceThousand($this->data['SalesOrderDetail']['discount_amount'][$i]);
                $jsonArray[$i]['discount_percent'] = $this->Helper->replaceThousand($this->data['SalesOrderDetail']['discount_percent'][$i]);
                $jsonArray[$i]['product_id']       = $this->data['SalesOrderDetail']['product_id'][$i];
                $jsonArray[$i]['service_id']       = $this->data['SalesOrderDetail']['service_id'][$i];
                $jsonArray[$i]['qty_uom_id']       = $this->data['SalesOrderDetail']['qty_uom_id'][$i];
                $jsonArray[$i]['qty']              = $this->Helper->replaceThousand($this->data['SalesOrderDetail']['qty'][$i]);
                $jsonArray[$i]['qty_free']         = $this->Helper->replaceThousand($this->data['SalesOrderDetail']['qty_free'][$i]);
                $jsonArray[$i]['unit_price']       = $this->Helper->replaceThousand($this->data['SalesOrderDetail']['unit_price'][$i]);
                $jsonArray[$i]['total_price']      = $this->Helper->replaceThousand($this->data['SalesOrderDetail']['total_price'][$i]);
                $jsonArray[$i]['qty_order']        = ($this->Helper->replaceThousand($this->data['SalesOrderDetail']['qty'][$i]) + $this->Helper->replaceThousand($this->data['SalesOrderDetail']['qty_free'][$i])) * $this->Helper->replaceThousand($this->data['SalesOrderDetail']['conversion'][$i]);
                $jsonArray[$i]['conversion']       = $this->Helper->replaceThousand($this->data['SalesOrderDetail']['conversion'][$i]);
                $jsonArray[$i]['lots_number']      = $this->data['SalesOrderDetail']['lots_number'][$i]!=''?$this->data['SalesOrderDetail']['lots_number'][$i]:0;
                $jsonArray[$i]['expired_date']     = $this->data['SalesOrderDetail']['expired_date'][$i]!=''?$this->data['SalesOrderDetail']['expired_date'][$i]:'0000-00-00';
                $jsonArray[$i]['class_id']         = $classId;
            }
            // Check Warehouse Option Allow Negative
            $warehouseOption = ClassRegistry::init('LocationGroup')->findById($this->data['PointOfSale']['location_group_id']);
            if($warehouseOption['LocationGroup']['allow_negative_stock'] == 0){ // Not Allow Negative Stock
                // Check Qty in Stock Before Save
                foreach($productOrder AS $key => $order){
                    $check = explode("+", $key);
                    $productId = $check[0];
                    $expDate   = $check[1];
                    if($check[1] == ''){
                        $expDate = '0000-00-00';
                    }
                    // Get Loction Setting
                    $locSetting = ClassRegistry::init('LocationSetting')->findById(4);
                    $locCon     = '';
                    if($locSetting['LocationSetting']['location_status'] == 1){
                        $locCon = ' AND is_for_sale = 1';
                    }
                    $sqlStock = mysql_query("SELECT SUM(total_qty - total_order) FROM `".$this->data['PointOfSale']['location_group_id'] . "_group_totals` WHERE product_id = ".$productId." AND expired_date = '".$expDate."' AND location_id IN (SELECT id FROM locations WHERE location_group_id = ".$this->data['PointOfSale']['location_group_id'].$locCon.") GROUP BY product_id");
                    if (mysql_num_rows($sqlStock)) {
                        $stock = mysql_fetch_array($sqlStock);
                        $totalQtyStock = $this->Helper->replaceThousand($stock[0]); // Total Qty Stock
                        if ($totalQtyStock < $order['qty']) {
                            $checkError = 2;
                            $listOutStock .= $productId."-";
                        }
                    } else {
                        $checkError = 2;
                        $listOutStock .= $productId."-";
                    }
                }
            }
            // Check Error Qty in Stock
            if ($checkError == 1) {
                if (@$this->data['PointOfSale']['location_group_id'] != "" && @$this->data['PointOfSales']['total_amount'] != "" && @$this->data['PointOfSale']['balance_us'] != "") {
                    $r = 0;
                    $restCode = array();
                    $dateNow  = date("Y-m-d H:i:s");
                    // Update Code & Change SO Generate Code
                    $modComCode = ClassRegistry::init('ModuleCodeBranch')->find('first', array('conditions' => array("ModuleCodeBranch.branch_id" => $this->data['PointOfSale']['branch_id'])));
                    $posCode    = date("y").$modComCode['ModuleCodeBranch']['pos_code'];
                    $salesOrder = array();
                    if($this->data['PointOfSale']['order_date'] == ""){
                        $this->data['PointOfSale']['order_date'] = $this->Helper->checkDateTransaction($this->data['PointOfSale']['branch_id']);
                    }
                    $this->SalesOrder->create();
                    $salesOrder['SalesOrder']['sys_code']    = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                    $salesOrder['SalesOrder']['customer_id'] = $this->data['PointOfSale']['customer_id']!=''?$this->data['PointOfSale']['customer_id']:1;
                    $salesOrder['SalesOrder']['patient_id'] = $this->data['PointOfSale']['customer_id']!=''?$this->data['PointOfSale']['customer_id']:1;
                    $salesOrder['SalesOrder']['queue_id'] = $this->data['PointOfSale']['queu_id']!=''?$this->data['PointOfSale']['queu_id']:1;
                    $salesOrder['SalesOrder']['company_id']  = $this->data['PointOfSale']['company_id'];
                    $salesOrder['SalesOrder']['branch_id']   = $this->data['PointOfSale']['branch_id'];
                    $salesOrder['SalesOrder']['location_group_id']    = $this->data['PointOfSale']['location_group_id'];
                    $salesOrder['SalesOrder']['currency_center_id']   = $company['Company']['currency_center_id'];
                    $salesOrder['SalesOrder']['vat_chart_account_id'] = $this->data['PointOfSale']['vat_chart_account_id'];
                    $salesOrder['SalesOrder']['ar_id']           = $this->data['PointOfSale']['chart_account_id'];
                    $salesOrder['SalesOrder']['so_code']         = $posCode;
                    $salesOrder['SalesOrder']['total_amount']    = $totalPriceSales;
                    $salesOrder['SalesOrder']['balance']         = $this->Helper->replaceThousand($this->data['PointOfSale']['balance_us']);
                    $salesOrder['SalesOrder']['discount']        = $this->Helper->replaceThousand($this->data['PointOfSale']['discount']);
                    $salesOrder['SalesOrder']['discount_percent']  = $this->Helper->replaceThousand($this->data['PointOfSale']['discount_percent']);
                    $salesOrder['SalesOrder']['order_date']      = $this->data['PointOfSale']['order_date'];
                    $salesOrder['SalesOrder']['total_vat']       = $this->data['PointOfSale']['total_vat'];
                    $salesOrder['SalesOrder']['vat_percent']     = $this->data['PointOfSale']['vat_percent'];
                    $salesOrder['SalesOrder']['vat_setting_id']  = $this->data['PointOfSale']['vat_setting_id'];
                    $salesOrder['SalesOrder']['vat_calculate']   = $this->data['PointOfSale']['vat_calculate'];
                    $salesOrder['SalesOrder']['price_type_id']   = $this->data['PointOfSale']['price_type_id'];
                    $salesOrder['SalesOrder']['sales_rep_id']    = 0;
                    $salesOrder['SalesOrder']['shift_id']        = $this->data['PointOfSale']['shift_id'];
                    $salesOrder['SalesOrder']['is_pos']          = 1;
                    $salesOrder['SalesOrder']['status']          = 2;
                    $salesOrder['SalesOrder']['created']         = $dateNow;
                    $salesOrder['SalesOrder']['created_by']      = $user['User']['id'];
                    if ($this->SalesOrder->save($salesOrder)) {
                        $salesOrderId = $this->SalesOrder->id;
                        // Get Module Code
                        $modCode    = $this->Helper->getModuleCode($posCode, $salesOrderId, 'so_code', 'sales_orders', 'status >= 0 AND branch_id = '.$this->data['PointOfSale']['branch_id']);
                        // Updaet Module Code
                        $invPOSCode = $modCode;
                        mysql_query("UPDATE sales_orders SET so_code = '".$modCode."' WHERE id = ".$salesOrderId);
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($salesOrder['SalesOrder'], 'sales_orders');
                        $restCode[$r]['so_code']  = $modCode;
                        $restCode[$r]['modified'] = $dateNow;
                        $restCode[$r]['dbtodo']   = 'sales_orders';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                        // Create General Ledger
                        $this->GeneralLedger->create();
                        $generalLedger = array();
                        $generalLedger['GeneralLedger']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                        $generalLedger['GeneralLedger']['sales_order_id'] = $salesOrderId;
                        $generalLedger['GeneralLedger']['date']       = $this->data['PointOfSale']['order_date'];
                        $generalLedger['GeneralLedger']['reference']  = $modCode;
                        $generalLedger['GeneralLedger']['created_by'] = $user['User']['id'];
                        $generalLedger['GeneralLedger']['is_sys'] = 1;
                        $generalLedger['GeneralLedger']['is_adj'] = 0;
                        $generalLedger['GeneralLedger']['is_active'] = 1;
                        $this->GeneralLedger->save($generalLedger);
                        $glId = $this->GeneralLedger->id;
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($generalLedger['GeneralLedger'], 'general_ledgers');
                        $restCode[$r]['dbtodo']   = 'general_ledgers';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                        $chartAccountPOS = $cashBankAccount['AccountType']['chart_account_id'];
                        if($salesOrder['SalesOrder']['balance'] > 0){
                            $chartAccountPOS = $arAccount['AccountType']['chart_account_id'];
                        }
                        // General Ledger Detail Cash
                        if (($salesOrder['SalesOrder']['total_amount'] - $this->Helper->replaceThousand($this->data['PointOfSale']['discount'])) > 0) {
                            $this->GeneralLedgerDetail->create();
                            $generalLedgerDetail = array();
                            $generalLedgerDetail['GeneralLedgerDetail']['general_ledger_id'] = $glId;
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id']  = $chartAccountPOS;
                            $generalLedgerDetail['GeneralLedgerDetail']['company_id']  = $this->data['PointOfSale']['company_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['branch_id']   = $this->data['PointOfSale']['branch_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['location_group_id'] = $this->data['PointOfSale']['location_group_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['customer_id'] = $this->data['PointOfSale']['customer_id']!=''?$this->data['PointOfSale']['customer_id']:1;
                            $generalLedgerDetail['GeneralLedgerDetail']['type']   = 'POS';
                            $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $salesOrder['SalesOrder']['total_amount'] - $this->Helper->replaceThousand($this->data['PointOfSale']['discount']) + $this->data['PointOfSale']['total_vat'];
                            $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: POS # ' . $modCode;
                            $generalLedgerDetail['GeneralLedgerDetail']['class_id'] = $classId;
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                        }
                        // General Ledger Detail (General Discount)
                        if ($this->Helper->replaceThousand($this->data['PointOfSale']['discount']) > 0) {
                            $this->GeneralLedgerDetail->create();
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $salesDiscAccount['AccountType']['chart_account_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['debit']  = $this->Helper->replaceThousand($this->data['PointOfSale']['discount']);
                            $generalLedgerDetail['GeneralLedgerDetail']['credit'] = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: POS # ' . $modCode . ' Total Discount';
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                        }
                        // General Ledger Detail Total VAT
                        if (($salesOrder['SalesOrder']['total_vat']) > 0) {
                            $this->GeneralLedgerDetail->create();
                            $generalLedgerDetail['GeneralLedgerDetail']['chart_account_id'] = $salesOrder['SalesOrder']['vat_chart_account_id'];
                            $generalLedgerDetail['GeneralLedgerDetail']['debit']  = 0;
                            $generalLedgerDetail['GeneralLedgerDetail']['credit'] = $salesOrder['SalesOrder']['total_vat'];
                            $generalLedgerDetail['GeneralLedgerDetail']['memo']   = 'ICS: POS # ' . $modCode . ' Total VAT';
                            $this->GeneralLedgerDetail->save($generalLedgerDetail);
                            // Convert to REST
                            $restCode[$r] = $this->Helper->convertToDataSync($generalLedgerDetail['GeneralLedgerDetail'], 'general_ledger_details');
                            $restCode[$r]['dbtodo']   = 'general_ledger_details';
                            $restCode[$r]['actodo']   = 'is';
                            $r++;
                        }
                        // Send File to Process Second
                        $rand = rand();
                        $name = (isset($_SESSION['sPosDb']) ? $_SESSION['sPosDb'] : $rand) . "_pos_" . $salesOrderId;
                        $filename = "public/pos/" . $name;
                        shell_exec("touch " . $filename);
                        if (file_exists($filename)) {
                            $json = json_encode($jsonArray);
                            $file = fopen($filename, "w");
                            fwrite($file, $json);
                            fclose($file);
                            $url = LINK_URL . "deliveryPos?user=" . $user['User']['id'] . "&sales_order_id=" . $salesOrderId . "&location_group_id=" . $this->data['PointOfSale']['location_group_id'] . "&total_amount_us=" . $salesOrder['SalesOrder']['total_amount'] . "&gl=" . $glId . "&company_id= ".$this->data['PointOfSale']['company_id']."&calculate_cogs=".$this->data['PointOfSale']['calculate_cogs']." &json=" . $name;
                            $url = "wget -b -q -P public/pos/logs/ '" . $url . "' " . LINK_URL_SSL;
                            shell_exec($url);
                            // Save Sales Receipt
                            if($salesOrder['SalesOrder']['balance'] == 0){
                                $this->SalesOrderReceipt->create();
                                $salesOrderReceipt = array();
                                $salesOrderReceipt['SalesOrderReceipt']['sys_code']           = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                                $salesOrderReceipt['SalesOrderReceipt']['sales_order_id']     = $salesOrderId;
                                $salesOrderReceipt['SalesOrderReceipt']['branch_id']          = $this->data['PointOfSale']['branch_id'];
                                $salesOrderReceipt['SalesOrderReceipt']['receipt_code']       = '';
                                $salesOrderReceipt['SalesOrderReceipt']['pay_date']           = $this->data['PointOfSale']['order_date'];
                                $salesOrderReceipt['SalesOrderReceipt']['currency_center_id'] = $this->data['PointOfSale']['currency_center_id'];
                                $salesOrderReceipt['SalesOrderReceipt']['exchange_rate_id']   = $this->data['PointOfSale']['exchange_rate_id'];
                                $salesOrderReceipt['SalesOrderReceipt']['amount_us']          = $this->Helper->replaceThousand($this->data['PointOfSale']['paid_us']);
                                $salesOrderReceipt['SalesOrderReceipt']['amount_other']       = $this->Helper->replaceThousand($this->data['PointOfSale']['paid_kh']);
                                $salesOrderReceipt['SalesOrderReceipt']['total_amount']       = $this->Helper->replaceThousand($this->data['PointOfSale']['total_be_paid']);
                                $salesOrderReceipt['SalesOrderReceipt']['total_amount_other'] = $this->Helper->replaceThousand($this->data['PointOfSale']['total_be_paid_kh']);
                                $salesOrderReceipt['SalesOrderReceipt']['balance']      = 0;
                                $salesOrderReceipt['SalesOrderReceipt']['change']       = $this->Helper->replaceThousand($this->data['PointOfSale']['change_us']);
                                $salesOrderReceipt['SalesOrderReceipt']['change_other'] = $this->Helper->replaceThousand($this->data['PointOfSale']['change_kh']);
                                $salesOrderReceipt['SalesOrderReceipt']['created']      = $dateNow;
                                $salesOrderReceipt['SalesOrderReceipt']['created_by']   = $user['User']['id'];
                                $this->SalesOrderReceipt->save($salesOrderReceipt);
                                $saleReceiptId = $this->SalesOrderReceipt->id;
                                // Get Module Code
                                $posRepCode = date("y").$modComCode['ModuleCodeBranch']['pos_rep_code'];
                                $modCode    = $this->Helper->getModuleCode($posRepCode, $saleReceiptId, 'receipt_code', 'sales_order_receipts', 'is_void = 0 AND branch_id = '.$this->data['PointOfSale']['branch_id']);
                                // Updaet Module Code
                                mysql_query("UPDATE sales_order_receipts SET receipt_code = '".$modCode."' WHERE id = ".$saleReceiptId);
                                // Convert to REST
                                $restCode[$r] = $this->Helper->convertToDataSync($salesOrderReceipt['SalesOrderReceipt'], 'sales_order_receipts');
                                $restCode[$r]['receipt_code'] = $modCode;
                                $restCode[$r]['modified'] = $dateNow;
                                $restCode[$r]['dbtodo']   = 'sales_order_receipts';
                                $restCode[$r]['actodo']   = 'is';
                                $r++;
                            }
                            // Save File Send
                            $this->Helper->sendFileToSync($restCode, 0, 0);
                            // Recalculate Average Cost
                            $sqlTrack = mysql_query("SELECT val, is_recalculate FROM tracks WHERE id = 1");
                            $track    = mysql_fetch_array($sqlTrack);
                            $dateReca = $salesOrder['SalesOrder']['order_date'];
                            $dateRun  = date("Y-m-d", strtotime(date("Y-m-d", strtotime($dateReca)) . " -1 day"));
                            if($track['val'] == "0000-00-00" || (strtotime($track['val']) >= strtotime($dateRun))){
                                mysql_query("UPDATE tracks SET val='".$dateRun."', is_recalculate = 1 WHERE id=1");
                            }
                            $this->Helper->saveUserActivity($user['User']['id'], 'Point Of Sales', 'Save Add New', $salesOrderId);
                            // Assign Value to Layout Print
                            $result['error']      = 0;
                            $result['inv_code']   = $invPOSCode;
                            $result['inv_date']   = $this->Helper->dateShort($this->data['PointOfSale']['order_date']);
                            $result['print_date'] = date("d/m/Y H:i:s");
                            $result['com_photo']  = $company['Company']['photo'];
                            $result['branch_add'] = nl2br($branch['Branch']['address']);
                            $result['username']   = $user['User']['first_name']." ".$user['User']['last_name'];
                            echo json_encode($result);
                            exit;
                        } else {
                            $this->data['SalesOrder']['id'] = $salesOrderId;
                            $this->data['SalesOrder']['is_pos'] = 1;
                            $this->data['SalesOrder']['status'] = -1;
                            $this->SalesOrder->save($this->data);
                            $result['error'] = 1;
                            echo json_encode($result);
                            exit;
                        }
                    } else {
                        $result['error'] = 2;
                        echo json_encode($result);
                        exit;
                    }
                } else {
                    $result['error'] = 3;
                    echo json_encode($result);
                    exit;
                }
            } else {
                $result['error'] = 4;
                $result['stock'] = $listOutStock;
                echo json_encode($result);
                exit;
            }
        }

        if (empty($this->data)) {
            $this->Helper->saveUserActivity($user['User']['id'], 'Point Of Sales', 'Add New');
            $companies = ClassRegistry::init('Company')->find('all',
                            array(
                                'joins' => array(
                                    array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))
                                ),
                                'fields' => array('Company.id', 'Company.name', 'Company.vat_calculate', 'Company.currency_center_id'),
                                'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])
                            ));
            $branches = ClassRegistry::init('Branch')->find('all',
                            array(
                                'joins' => array(
                                    array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id')),
                                    array('table' => 'module_code_branches AS ModuleCodeBranch', 'type' => 'left', 'conditions' => array('ModuleCodeBranch.branch_id=Branch.id'))
                                ),
                                'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id', 'ModuleCodeBranch.inv_code', 'Branch.currency_center_id', 'Branch.pos_currency_id', 'Branch.telephone', 'CurrencyCenter.symbol'),
                                'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])
                            ));
            $arAccount   = ClassRegistry::init('AccountType')->findById(7);
            $arAccountId = $arAccount['AccountType']['chart_account_id'];
            // Get Loction Setting
            $locSetting = ClassRegistry::init('LocationSetting')->findById(3);
            $locCon     = '';
            if($locSetting['LocationSetting']['location_status'] == 1){
                $locCon = ' AND Location.is_for_sale = 1';
            }
            if($user['User']['id'] == 1){
                $conditionUser = "";
            }else{
                $conditionUser = "id IN (SELECT cgroup_id FROM cgroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))";
            }
            $joinUsers = array('table' => 'user_location_groups', 'type' => 'INNER', 'conditions' => array('user_location_groups.location_group_id=LocationGroup.id'));
            $joinLocation = array('table' => 'locations', 'type' => 'INNER', 'conditions' => array('locations.location_group_id=LocationGroup.id', $locCon));
            $locationGroups = ClassRegistry::init('LocationGroup')->find('list', array('fields' => array('LocationGroup.id', 'LocationGroup.name'),'joins' => array($joinUsers, $joinLocation),'conditions' => array('user_location_groups.user_id=' . $user['User']['id'], 'LocationGroup.is_active' => '1', 'LocationGroup.location_group_type_id != 1'), 'group' => 'LocationGroup.id'));
            $cgroups = ClassRegistry::init('Cgroup')->find('list', array('order' => 'id', 'conditions' => array('is_active' => 1, $conditionUser)));
            $pgroups = ClassRegistry::init('Pgroup')->find('list', array('order' => 'id', 'conditions' => array('is_active' => 1, 'id IN (SELECT pgroup_id FROM pgroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].'))')));
            $uoms = ClassRegistry::init('Uom')->find("list", array("conditions" => array("Uom.is_active = 1")));
            $this->set(compact('companies', "branches", 'locationGroups', "arAccountId", "cgroups", "pgroups", "uoms"));
        }
    }
    
    function reprintReceiptSm($code = null, $branchId = null) {
        $this->layout = 'ajax';
        if (!empty($code)) {
            $salesOrder = ClassRegistry::init('SalesOrder')->find("first", array('conditions' => array('SalesOrder.so_code' => $code, 'SalesOrder.branch_id' => $branchId, 'SalesOrder.status' => 2, 'SalesOrder.is_pos' => 1)));
            if(!empty($salesOrder)){
                $salesOrderId = $salesOrder['SalesOrder']['id'];
                $salesOrderReceipt = ClassRegistry::init('SalesOrderReceipt')->find("first", array('conditions' => array('SalesOrderReceipt.sales_order_id' => $salesOrderId, 'SalesOrderReceipt.is_void' => 0)));
                $lastExchangeRate = ClassRegistry::init('ExchangeRate')->find("first", array(
                    "conditions" => array("ExchangeRate.is_active" => 1),
                    "order" => array("ExchangeRate.created desc")
                        )
                );
                $salesOrderDetails = ClassRegistry::init('SalesOrderDetail')->find("all", array('conditions' => array('SalesOrderDetail.sales_order_id' => $salesOrderId)));
                $salesOrderServices = ClassRegistry::init('SalesOrderService')->find("all", array('conditions' => array('SalesOrderService.sales_order_id' => $salesOrder['SalesOrder']['id'])));
                // Currency Other
                $otherSymbolCur = '';
                @$sqlOtherCur   = mysql_query("SELECT currency_centers.symbol FROM exchange_rates INNER JOIN currency_centers ON currency_centers.id = exchange_rates.currency_center_id WHERE exchange_rates.id = ".$salesOrderReceipt['SalesOrderReceipt']['exchange_rate_id']." LIMIT 1");
                if(mysql_num_rows($sqlOtherCur)){
                    $rowOtherCur    = mysql_fetch_array($sqlOtherCur);
                    $otherSymbolCur = $rowOtherCur[0];
                }
                $this->set(compact('otherSymbolCur', 'salesOrder', 'salesOrderDetails', 'salesOrderReceipt', 'lastExchangeRate', 'salesOrderServices'));
            } else {
                echo "error";
                exit;
            }
        } else {
            echo "error";
            exit;
        }
    }

    function service($companyId, $branchId) {
        $this->layout = 'ajax';
        $sections = ClassRegistry::init('Section')->find("list", array("conditions" => array("Section.is_active = 1", "Section.id IN (SELECT section_id FROM section_companies WHERE company_id = ".$companyId.")")));
        $services = $this->serviceCombo($companyId, $branchId);
        $this->set(compact('sections', 'services'));
    }

    function serviceCombo($companyId, $branchId) {
        $array = array();
        $services = ClassRegistry::init('Service')->find("all", array("conditions" => array("Service.company_id=" . $companyId. " AND Service.is_active = 1", "Service.id IN (SELECT service_id FROM service_branches WHERE branch_id = ".$branchId.")")));
        foreach ($services as $service) {
            $queryUomName = mysql_query("SELECT name FROM uoms WHERE id = '".$service['Service']['uom_id']."'");
            $dataUomName  = mysql_fetch_array($queryUomName);            
            array_push($array, array('value' => $service['Service']['id'], 'name' => $service['Service']['name'], 'class' => $service['Section']['id'], 'code' => $service['Service']['code'], 'uom-name' => $dataUomName[0], 'uom-id' => $service['Service']['uom_id'], 'price' => $service['Service']['unit_price']));
        }
        return $array;
    }
    
    function viewPosDaily(){
        $this->layout = 'ajax';
    }
    
    function customer($companyId) {
        $this->layout = 'ajax';
        if(!empty($companyId)){
            $this->set('companyId', $companyId);
        }else{
            exit;
        }
    }

    function customerAjax($companyId, $group = null) {
        $this->layout = 'ajax';
        if(!empty($companyId)){
            $this->set('companyId', $companyId);
            $this->set('group', $group);
        }else{
            exit;
        }
    }
    
    function discount($companyId = null) {
        $this->layout = 'ajax';
        if (!$companyId) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $discounts = ClassRegistry::init('Discount')->find("all", array('conditions' => array('Discount.is_active' => 1, 'Discount.company_id' => $companyId), 'order' => array('id DESC')));
        $this->set(compact('discounts'));
    }
    
    function checkStartShift($companyId = null, $branchId = null) {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!$companyId && !$branchId) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        $queryStarShift = mysql_query("SELECT id, shift_code, created, total_register, total_register_other, status FROM shifts WHERE created_by = '".$user['User']['id']."' AND company_id = '".$companyId."' AND branch_id = '".$branchId."' ORDER BY id DESC LIMIT 01");
        if(mysql_num_rows($queryStarShift)){
            $dataStarShift = mysql_fetch_array($queryStarShift);
            if($dataStarShift[5] == 3){
                $result['status_shift'] = 0;
                $result['not_collect']  = 0;
                echo json_encode($result);
                exit;
            } else if($dataStarShift[5] == 2){
                $result['status_shift'] = 0;
                $result['not_collect']  = 1;
                echo json_encode($result);
                exit;
            } else {
                $totalAdj      = 0;
                $totalAdjOther = 0;
                $queryAdj = mysql_query("SELECT SUM(total_adj), SUM(total_adj_other) FROM shift_adjusts WHERE shift_id = '".$dataStarShift[0]."' GROUP BY shift_id");
                if(mysql_num_rows($queryAdj)){
                    $dataAdj = mysql_fetch_array($queryAdj);
                    $totalAdj      = $dataAdj[0];
                    $totalAdjOther = $dataAdj[1];
                }
                
                $result['status_shift'] = $dataStarShift[0];
                $result['shift_code'] = $dataStarShift[1];
                $result['shift_created'] = date('d/m/Y H:i:s', strtotime($dataStarShift[2]));
                $result['total_register'] = $dataStarShift[3];
                $result['total_register_other'] = $dataStarShift[4];
                $result['total_adj'] = $totalAdj;
                $result['total_adj_other'] = number_format($totalAdjOther, 0);
                echo json_encode($result);
                exit;
            }
        }else{
            //Don't Have Data
            $result['status_shift'] = 0;
            echo json_encode($result);
            exit;
        }
    }
    
    function addShiftRegister($companyId = null){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!$companyId) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }        
        
        if(!empty($this->data)){
            $dateNow  = date("Y-m-d H:i:s");
            $this->loadModel("Shift");
            $this->Shift->create();            
            $code  = $this->Helper->getAutoGenerateShiftCode();
            $this->data['Shift']['shift_code'] = $code;
            $this->data['Shift']['date_start'] = $dateNow;
            $this->data['Shift']['date_end']   = $dateNow;
            $this->data['Shift']['created']    = $dateNow;
            $this->data['Shift']['created_by'] = $user['User']['id'];
            $this->data['Shift']['status']     = 1;
            if ($this->Shift->save($this->data)) {    
                $lastInsertId = $this->Shift->getLastInsertId();
                // Save User Activity
                $this->Helper->saveUserActivity($user['User']['id'], 'Shift', 'Save Add New', $lastInsertId);
                echo $lastInsertId."|*|".$code."|*|".date("d/m/Y H:i:s", strtotime($dateNow));
                exit;
            } else {
                $this->Helper->saveUserActivity($user['User']['id'], 'Shift', 'Save Add New (Error)');
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                exit;
            }
        }
    }    
    
    function saveAdjShiftRegister($shiftId = null, $type){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!$shiftId) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }        
        
        if(!empty($this->data)){
            $dateNow  = date("Y-m-d H:i:s");
            $this->loadModel("ShiftAdjust");
            $this->ShiftAdjust->create();
            
            if($type == 2){
                $totalAdj      = $this->Helper->replaceThousand($this->data['ShiftAdjust']['total_adj']) * -1;
                $totalAdjOther = $this->Helper->replaceThousand($this->data['ShiftAdjust']['total_adj_other']) * -1;
            }else{
                $totalAdj      = $this->Helper->replaceThousand($this->data['ShiftAdjust']['total_adj']);
                $totalAdjOther = $this->Helper->replaceThousand($this->data['ShiftAdjust']['total_adj_other']);
            }
            
            $this->data['ShiftAdjust']['total_adj']         = $totalAdj;
            $this->data['ShiftAdjust']['total_adj_other']   = $totalAdjOther;
            $this->data['ShiftAdjust']['created']           = $dateNow;
            $this->data['ShiftAdjust']['created_by']        = $user['User']['id'];
            if ($this->ShiftAdjust->save($this->data)) {    
                $lastInsertId = $this->ShiftAdjust->getLastInsertId();
                // Save User Activity
                $this->Helper->saveUserActivity($user['User']['id'], 'Adjust Shift', 'Save Add New', $lastInsertId);
                // Sum Adj Shift
                $totalAdj      = 0;
                $totalAdjOther = 0;
                $queryAdj = mysql_query("SELECT SUM(total_adj), SUM(total_adj_other) FROM shift_adjusts WHERE shift_id = '".$shiftId."' GROUP BY shift_id");
                if(mysql_num_rows($queryAdj)){
                    $dataAdj = mysql_fetch_array($queryAdj);
                    $totalAdj      = $dataAdj[0];
                    $totalAdjOther = $dataAdj[1];
                }
                
                echo $lastInsertId."|*|".$totalAdj."|*|".$totalAdjOther;
                exit;
            } else {
                $this->Helper->saveUserActivity($user['User']['id'], 'Adjust Shift', 'Save Add New (Error)');
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                exit;
            }
        }
    }   
    
    function endShiftRegister($companyId = null, $shiftId = null){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!$companyId && !$shiftId) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }
        if(!empty($this->data)){ 
            $this->loadModel("Shift");
            $dateNow    = date("Y-m-d H:i:s");
            $totalSales = 0;
            $querySales = mysql_query("SELECT SUM(total_amount + total_vat - discount) totalSales FROM sales_orders WHERE shift_id = ".$shiftId." AND status = 2");
            if(mysql_num_rows($querySales)){
                $dataSales  = mysql_fetch_array($querySales);
                $totalSales = $dataSales[0];
            }
            $this->data['Shift']['id']     = $shiftId;
            $this->data['Shift']['status'] = 2;
            $this->data['Shift']['total_acture']       = $this->data['Shift']['total_acture'];
            $this->data['Shift']['total_acture_other'] = $this->data['Shift']['total_acture_other'];
            $this->data['Shift']['total_sales']        = $totalSales;
            $this->data['Shift']['close_shift_memo']   = $this->data['Shift']['close_shift_memo'];
            $this->data['Shift']['date_end']           = $dateNow;
            if($this->Shift->save($this->data)){
                // Save User Activity
                $this->Helper->saveUserActivity($user['User']['id'], 'Shift', 'Save End Shift', $shiftId);
                echo MESSAGE_DATA_HAS_BEEN_SAVED;
                exit;
            } else {
                // Save User Activity
                $this->Helper->saveUserActivity($user['User']['id'], 'Shift', 'Save End Shift (Error)', $shiftId);
                echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                exit;
            }
        }
    }
    
    function checkAdjShiftRegister($shiftId = null){
        $this->layout = 'ajax';       
        if (!$shiftId) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }  
        $adjShift      = "0.00";
        $adjShiftOther = "0.00";
        $queryAdjShift = mysql_query("SELECT SUM(`total_adj`), SUM(`total_adj_other`) FROM `shift_adjusts` WHERE `shift_id` = '".$shiftId."' GROUP BY `shift_id`");
        if(mysql_num_rows($queryAdjShift)){
            $dataAdjShift  = mysql_fetch_array($queryAdjShift);
            $adjShift      = $dataAdjShift[0];
            $adjShiftOther = $dataAdjShift[1];
        }
        $result['adjShift'] = $adjShift;
        $result['adjShiftOther'] = $adjShiftOther;
        echo json_encode($result);
        exit;
    }
    
    function getDataAdjShiftRegister($shiftId = null){ 
        $this->layout = 'ajax';       
        $user = $this->getCurrentUser();
        if (!$shiftId) {
            echo MESSAGE_DATA_INVALID;
            exit;
        } 
        $this->loadModel("Shift");
        $shifts = $this->Shift->read(null, $shiftId);
        if (!empty($shifts)){  
            $branch = ClassRegistry::init('Branch')->find('first',
                            array(
                                'joins' => array(
                                    array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id')),
                                    array('table' => 'module_code_branches AS ModuleCodeBranch', 'type' => 'left', 'conditions' => array('ModuleCodeBranch.branch_id=Branch.id'))
                                ),
                                'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id', 'ModuleCodeBranch.inv_code', 'Branch.currency_center_id', 'Branch.pos_currency_id', 'CurrencyCenter.symbol', 'Branch.address', 'Branch.telephone'),
                                'conditions' => array('Branch.is_active = 1', 'Branch.id = '.$shifts['Shift']['branch_id'].'', 'user_branches.user_id=' . $user['User']['id'])
                            ));
            
            $this->set(compact('shiftId', 'branch'));         
        } else {
            $this->Helper->saveUserActivity($user['User']['id'], 'Shift', 'Print Shift (Error)');
            echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
            exit;
        }
    }
    function printShift($shiftId = null) {
        $this->layout = 'ajax';       
        if (!$shiftId) {
            echo MESSAGE_DATA_INVALID;
            exit;
        }        
        $user = $this->getCurrentUser();
        $this->loadModel("Shift");
        $shifts = $this->Shift->read(null, $shiftId);
        if (!empty($shifts)){    
            $company = ClassRegistry::init('Company')->find('first',
                            array(
                                'joins' => array(
                                    array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))
                                ),
                                'fields' => array('Company.photo'),
                                'conditions' => array('Company.is_active = 1', 'Company.id = '.$shifts['Shift']['company_id'].'', 'user_companies.user_id=' . $user['User']['id'])
                            ));
            $branch = ClassRegistry::init('Branch')->find('first',
                            array(
                                'joins' => array(
                                    array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id')),
                                    array('table' => 'module_code_branches AS ModuleCodeBranch', 'type' => 'left', 'conditions' => array('ModuleCodeBranch.branch_id=Branch.id'))
                                ),
                                'fields' => array('Branch.id', 'Branch.name', 'Branch.company_id', 'ModuleCodeBranch.inv_code', 'Branch.currency_center_id', 'Branch.pos_currency_id', 'CurrencyCenter.symbol', 'Branch.address', 'Branch.telephone'),
                                'conditions' => array('Branch.is_active = 1', 'Branch.id = '.$shifts['Shift']['branch_id'].'', 'user_branches.user_id=' . $user['User']['id'])
                            ));
            
            $totalAdj      = 0;
            $totalAdjOther = 0;
            $queryAdj = mysql_query("SELECT SUM(total_adj), SUM(total_adj_other) FROM shift_adjusts WHERE shift_id = '".$shiftId."' GROUP BY shift_id");
            if(mysql_num_rows($queryAdj)){
                $dataAdj = mysql_fetch_array($queryAdj);
                $totalAdj      = $dataAdj[0];
                $totalAdjOther = $dataAdj[1];
            }
            
            $this->set(compact('shifts', 'branch', 'company', 'totalAdj', 'totalAdjOther'));            
        } else {
            $this->Helper->saveUserActivity($user['User']['id'], 'Shift', 'Print Shift (Error)');
            echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
            exit;
        }
    }  
    
    function addPgroup(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $this->loadModel('Pgroup');
            $result   = array();
            $comCheck = 0;
            if(!empty($this->data['Pgroup']['company_id'])){
                if(is_array($this->data['Pgroup']['company_id'])){
                    $comCheck = implode(",", $this->data['Pgroup']['company_id']);
                } else {
                    $comCheck = $this->data['Pgroup']['company_id'];
                }
            }
            if ($this->Helper->checkDouplicate('name', 'pgroups', $this->data['Pgroup']['name'], 'is_active = 1 AND id IN (SELECT pgroup_id FROM pgroup_companies WHERE company_id IN ('.$comCheck.'))')) {
                $this->Helper->saveUserActivity($user['User']['id'], 'Product Group', 'Save Quick Add New (Name ready existed)');
                $result['error'] = 2;
                echo json_encode($result);
                exit;
            } else {
                $r = 0;
                $e = 0;
                $syncEco   = array();
                $restCode  = array();
                $dateNow   = date("Y-m-d H:i:s");
                $this->Pgroup->create();
                $this->data['Pgroup']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $this->data['Pgroup']['created']    = $dateNow;
                $this->data['Pgroup']['created_by'] = $user['User']['id'];
                $this->data['Pgroup']['is_active']  = 1;
                if ($this->Pgroup->save($this->data)) {
                    $pgroupId = $this->Pgroup->id;
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($this->data['Pgroup'], 'pgroups');
                    $restCode[$r]['modified']   = $dateNow;
                    $restCode[$r]['dbtodo']     = 'pgroups';
                    $restCode[$r]['actodo']     = 'is';
                    $r++;
                    // Pgroup Company
                    if (!empty($this->data['Pgroup']['company_id'])) {
                        for ($i = 0; $i < sizeof($this->data['Pgroup']['company_id']); $i++) {
                            mysql_query("INSERT INTO pgroup_companies (pgroup_id, company_id) VALUES ('" . $pgroupId . "','" . $this->data['Pgroup']['company_id'][$i] . "')");
                            // Convert to REST
                            $restCode[$r]['pgroup_id']  = $this->data['Pgroup']['sys_code'];
                            $restCode[$r]['company_id'] = $this->Helper->getSQLSysCode("companies", $this->data['Pgroup']['company_id'][$i]);
                            $restCode[$r]['dbtodo']     = 'pgroup_companies';
                            $restCode[$r]['actodo']     = 'is';
                            $r++;
                        }
                    }
                    // Send to E-Commerce
                    // Convert to REST
                    $syncEco[$e]['sys_code']  = $this->data['Pgroup']['sys_code'];
                    $syncEco[$e]['name']      = $this->data['Pgroup']['name'];
                    $syncEco[$e]['status']    = 2;
                    $syncEco[$e]['created']   = $dateNow;
                    $syncEco[$e]['dbtodo']    = 'pgroups';
                    $syncEco[$e]['actodo']    = 'is';
                    $e++;
                    // Save File Send
                    $this->Helper->sendFileToSync($restCode, 0, 0);
                    // Save File Send to E-Commerce
                    $this->Helper->sendFileToSyncPublic($syncEco);
                    // Save User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'Product Group', 'Save Quick Add New', $pgroupId);
                    $result['error']  = 0;
                    $result['option'] = '<option value="">'.INPUT_SELECT.'</option>';
                    $pgroups = ClassRegistry::init('Pgroup')->find('all', array('order' => 'name', 'conditions' => array('is_active' => 1)));
                    foreach($pgroups AS $pgroup){
                        $selected = '';
                        if($pgroup['Pgroup']['id'] == $pgroupId){
                            $selected = 'selected="selected"';
                        }
                        $result['option'] .= '<option value="'.$pgroup['Pgroup']['id'].'" '.$selected.'>'.$pgroup['Pgroup']['name'].'</option>';
                    }
                    echo json_encode($result);
                    exit;
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'Product Group', 'Save Quick Add New (Error)');
                    $result['error'] = 1;
                    echo json_encode($result);
                    exit;
                }
            }
        }
        $this->Helper->saveUserActivity($user['User']['id'], 'Product Group', 'Quick Add New');
        $companies = ClassRegistry::init('Company')->find('list', array('order' => 'id', 'conditions' => array('is_active' => 1, 'id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')')));
        $this->set(compact("companies"));
    }
    
    function addUom(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $this->loadModel('Uom');
            $result = array();
            if ($this->Helper->checkDouplicate('name', 'uoms', $this->data['Uom']['name'])) {
                $this->Helper->saveUserActivity($user['User']['id'], 'UoM', 'Save Quick Add New (Name has existed)');
                $result['error'] = 2;
                echo json_encode($result);
                exit;
            } else {
                Configure::write('debug', 0);
                $r = 0;
                $restCode = array();
                $dateNow  = date("Y-m-d H:i:s");
                $this->Uom->create();
                $this->data['Uom']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $this->data['Uom']['created']    = $dateNow;
                $this->data['Uom']['created_by'] = $user['User']['id'];
                $this->data['Uom']['is_active'] = 1;
                if ($this->Uom->save($this->data)) {
                    $error = mysql_error();
                    if($error != 'Invalid Data'){
                        $uomId = $this->Uom->id;
                        // Convert to REST
                        $restCode[$r] = $this->Helper->convertToDataSync($this->data['Uom'], 'uoms');
                        $restCode[$r]['modified'] = $dateNow;
                        $restCode[$r]['dbtodo']   = 'uoms';
                        $restCode[$r]['actodo']   = 'is';
                        $r++;
                        // Send to E-Commerce
                        $e = 0;
                        $syncEco = array();
                        // Convert to REST
                        $syncEco[$e]['sys_code']  = $this->data['Uom']['sys_code'];
                        $syncEco[$e]['name']      = $this->data['Uom']['name'];
                        $syncEco[$e]['abbr']      = $this->data['Uom']['abbr'];
                        $syncEco[$e]['created']   = $dateNow;
                        $syncEco[$e]['dbtodo']    = 'uoms';
                        $syncEco[$e]['actodo']    = 'is';
                        // Save File Send to E-Commerce
                        $this->Helper->sendFileToSyncPublic($syncEco);
                        // UoM Conversion
                        if(!empty($this->data['UomConversion']['to_uom_id'])){
                            $this->loadModel('UomConversion');
                            $this->UomConversion->create();
                            $this->data['UomConversion']['from_uom_id'] = $uomId;
                            $this->data['UomConversion']['to_uom_id']   = $this->data['UomConversion']['to_uom_id'];
                            $this->data['UomConversion']['value']       = $this->Helper->replaceThousand($this->data['UomConversion']['value']);
                            $this->data['UomConversion']['created']     = $dateNow;
                            $this->data['UomConversion']['created_by']  = $user['User']['id'];
                            $this->data['UomConversion']['is_active']   = 1;
                            $this->data['UomConversion']['is_small_uom'] = 1;
                            if ($this->UomConversion->save($this->data)) {
                                $error = mysql_error();
                                if($error != 'Invalid Data'){
                                    // Convert to REST
                                    $restCode[$r] = $this->Helper->convertToDataSync($this->data['UomConversion'], 'uom_conversions');
                                    $restCode[$r]['modified']   = $dateNow;
                                    $restCode[$r]['dbtodo']     = 'uom_conversions';
                                    $restCode[$r]['actodo']     = 'is';
                                    $r++;
                                    if(!empty($this->data['other_uom'])){
                                        for($i = 0; $i < sizeof($this->data['other_uom']); $i++){
                                            $checkVal = abs($this->data['UomConversion']['value'] % $this->data['other_value'][$i]);
                                            if($this->data['other_value'][$i] > 0 && $this->data['other_value'][$i] != '' && $checkVal == 0 && ($this->data['other_value'][$i] <= $this->data['UomConversion']['value'])){
                                                $this->UomConversion->create();
                                                $otherUom = array();
                                                $otherUom['UomConversion']['from_uom_id'] = $uomId;
                                                $otherUom['UomConversion']['to_uom_id']   = $this->data['other_uom'][$i];
                                                $otherUom['UomConversion']['value']       = $this->Helper->replaceThousand($this->data['other_value'][$i]);
                                                $otherUom['UomConversion']['created']     = $dateNow;
                                                $otherUom['UomConversion']['created_by']  = $user['User']['id'];
                                                $otherUom['UomConversion']['is_active']   = 1;
                                                $this->UomConversion->saveAll($otherUom);
                                                // Convert to REST
                                                $restCode[$r] = $this->Helper->convertToDataSync($otherUom['UomConversion'], 'uom_conversions');
                                                $restCode[$r]['modified']   = $dateNow;
                                                $restCode[$r]['dbtodo']     = 'uom_conversions';
                                                $restCode[$r]['actodo']     = 'is';
                                                $r++;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        // Save File Send
                        $this->Helper->sendFileToSync($restCode, 0, 0);
                        // Save User Activity
                        $this->Helper->saveUserActivity($user['User']['id'], 'UoM', 'Save Quick Add New', $uomId);
                        $result['error']  = 0;
                        $result['option'] = '<option value="">'.INPUT_SELECT.'</option>';
                        $uoms = ClassRegistry::init('Uom')->find('all', array('order' => 'name', 'conditions' => array('is_active' => 1)));
                        foreach($uoms AS $uom){
                            $selected = '';
                            if($uom['Uom']['id'] == $uomId){
                                $selected = 'selected="selected"';
                            }
                            $result['option'] .= '<option value="'.$uom['Uom']['id'].'" '.$selected.'>'.$uom['Uom']['name'].'</option>';
                        }
                        echo json_encode($result);
                        exit;
                    } else {
                        $this->Helper->saveUserActivity($user['User']['id'], 'UoM', 'Save Quick Add New (Error '.$error.')');
                        $result['error'] = 1;
                        echo json_encode($result);
                        exit;
                    }
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'UoM', 'Save Quick Add New (Error)');
                    $result['error'] = 1;
                    echo json_encode($result);
                    exit;
                }
            }
        }
        $this->Helper->saveUserActivity($user['User']['id'], 'UoM', 'Quick Add New');
        $types = array(
            'Count' => 'Count',
            'Weight' => 'Weight',
            'Length' => 'Length',
            'Area' => 'Area',
            'Volume' => 'Volume',
            'Time' => 'Time'
        );
        $uomList = ClassRegistry::init('Uom')->find('list', array('conditions' => array('is_active != 2', 'Uom.id NOT IN (SELECT from_uom_id FROM `uom_conversions` WHERE is_active = 1)')));
        $this->set(compact("types", "uomList"));
    }
    
    function quickAddProduct() {
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $this->loadModel('Product');
            $this->Product->create();
            if ($this->Helper->checkDouplicate('code', 'products', $this->data['Product']['code'], "company_id=".$this->data['Product']['company_id']." AND is_active = 1")) {
                // User Activity
                $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Save Quick Add New (Name ready existed)');
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $r = 0;
                $e = 0;
                $syncEco  = array();
                $restCode = array();
                $dateNow  = date("Y-m-d H:i:s");
                $smValUom = ClassRegistry::init('UomConversion')->find('first', array('fileds' => array('value'), 'order' => 'id', 'conditions' => array('from_uom_id' => $this->data['Product']['price_uom_id'], 'is_small_uom = 1', 'is_active' => 1)));
                if (!empty($smValUom)) {
                    $this->data['Product']['small_val_uom'] = $smValUom['UomConversion']['value'];
                } else {
                    $this->data['Product']['small_val_uom'] = 1;
                }
                if($this->data['Product']['code'] == ""){
                    $this->data['Product']['code'] = $this->data['Product']['barcode'];
                }
                $unitCost = $this->data['Product']['unit_cost'] != "" ? str_replace(",", "", $this->data['Product']['unit_cost']) : 0;
                $this->data['Product']['sys_code']        = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $this->data['Product']['default_cost']    = $unitCost;
                $this->data['Product']['unit_cost']       = $unitCost;
                $this->data['Product']['code']            = $this->data['Product']['barcode'];
                $this->data['Product']['reorder_level']   = 0;
                $this->data['Product']['created']         = $dateNow;
                $this->data['Product']['created_by']      = $user['User']['id'];
                $this->data['Product']['is_active']       = 1;
                if ($this->Product->save($this->data)) {
                    $lastInsertId = $this->Product->id;
                    // product main photo
                    if ($this->data['Product']['photo'] != '') {
                        $ext = pathinfo($this->data['Product']['photo'], PATHINFO_EXTENSION);
                        $photoName =  $lastInsertId . '_' . md5($this->data['Product']['photo']).".".$ext;
                        rename('public/product_photo/tmp/' . $this->data['Product']['photo'], 'public/product_photo/' . $photoName);
                        rename('public/product_photo/tmp/thumbnail/' . $this->data['Product']['photo'], 'public/product_photo/tmp/thumbnail/' . $photoName);
                        mysql_query("UPDATE products SET photo='" . $photoName . "' WHERE id=" . $lastInsertId);
                        $this->data['Product']['photo'] = $photoName;
                    }
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($this->data['Product'], 'products');
                    $restCode[$r]['modified'] = $dateNow;
                    $restCode[$r]['dbtodo']   = 'products';
                    $restCode[$r]['actodo']   = 'is';
                    $r++;
                    // Check Product Group Share
                    $checkShare = 2;
                    if (!empty($this->data['Product']['pgroup_id'])) {
                        $sqlShare = mysql_query("SELECT id FROM e_pgroup_shares WHERE pgroup_id = ".$this->data['Product']['pgroup_id']);
                        if(mysql_num_rows($sqlShare)){
                            $checkShare = 1;
                        }
                    }
                    // Send to E-Commerce
                    // Convert to REST
                    $shopSys = $this->Helper->getSQLSysCode("companies", $this->data['Product']['company_id']);
                    $syncEco[$e]['shop_id']   = $shopSys;
                    $syncEco[$e]['uom_id']    = $this->Helper->getSQLSysCode("uoms", $this->data['Product']['price_uom_id']);
                    $syncEco[$e]['sys_code']  = $this->data['Product']['sys_code'];
                    $syncEco[$e]['code']      = $this->data['Product']['code'];
                    $syncEco[$e]['barcode']   = $this->data['Product']['barcode'];
                    $syncEco[$e]['name']      = $this->data['Product']['name'];
                    $syncEco[$e]['description'] = $this->data['Product']['description'];
                    $syncEco[$e]['status']    = $checkShare;
                    $syncEco[$e]['created']   = $dateNow;
                    $syncEco[$e]['dbtodo']    = 'products';
                    $syncEco[$e]['actodo']    = 'is';
                    $e++;
                    if($checkShare == 1){
                        mysql_query("INSERT INTO `e_product_shares` (`company_id`, `product_id`, `created`, `created_by`) VALUES (".$this->data['Product']['company_id'].", ".$lastInsertId.", '".$dateNow."', ".$user['User']['id'].");");
                    }
                    // product group
                    if (!empty($this->data['Product']['pgroup_id'])) {
                        mysql_query("INSERT INTO product_pgroups (product_id, pgroup_id) VALUES ('".$lastInsertId."', '".$this->data['Product']['pgroup_id']."')");
                        // Convert to REST
                        $restCode[$r]['product_id'] = $this->data['Product']['sys_code'];
                        $restCode[$r]['pgroup_id']  = $this->Helper->getSQLSysCode("pgroups", $this->data['Product']['pgroup_id']);
                        $restCode[$r]['dbtodo']     = 'product_pgroups';
                        $restCode[$r]['actodo']     = 'is';
                        $r++;
                        // Convert to REST
                        $syncEco[$e]['product_id'] = $this->data['Product']['sys_code'];
                        $syncEco[$e]['pgroup_id']  = $this->Helper->getSQLSysCode("pgroups", $this->data['Product']['pgroup_id']);
                        $syncEco[$e]['dbtodo']     = 'product_pgroups';
                        $syncEco[$e]['actodo']     = 'is';
                        $e++;
                    }
                    // SKU of each UOM
                    if (!empty($this->data['sku_uom_value'])) {
                        for ($i = 0; $i < sizeof($this->data['sku_uom_value']); $i++) {
                            if ($this->data['sku_uom_value'][$i] != '' && $this->data['sku_uom'][$i] != '') {
                                mysql_query("INSERT INTO product_with_skus (product_id, sku, uom_id) VALUES ('" . $lastInsertId . "', '" . $this->data['sku_uom_value'][$i] . "', '" . $this->data['sku_uom'][$i] . "')");
                                // Convert to REST
                                $restCode[$r]['product_id'] = $this->data['Product']['sys_code'];
                                $restCode[$r]['sku']        = $this->data['sku_uom_value'][$i];
                                $restCode[$r]['uom_id']     = $this->Helper->getSQLSysCode("uoms", $this->data['sku_uom'][$i]);
                                $restCode[$r]['dbtodo']     = 'product_with_skus';
                                $restCode[$r]['actodo']     = 'is';
                                $r++;
                            }
                        }
                    }
                    if (!empty($this->data['Product']['branch_id'])) {
                        for ($i = 0; $i < sizeof($this->data['Product']['branch_id']); $i++) {
                            mysql_query("INSERT INTO product_branches (product_id,branch_id) VALUES ('" . $lastInsertId . "','" . $this->data['Product']['branch_id'][$i] . "')");
                            // Convert to REST
                            $restCode[$r]['product_id'] = $this->data['Product']['sys_code'];
                            $restCode[$r]['branch_id']  = $this->Helper->getSQLSysCode("branches", $this->data['Product']['branch_id'][$i]);
                            $restCode[$r]['dbtodo']     = 'product_branches';
                            $restCode[$r]['actodo']     = 'is';
                            $r++;
                        }
                    }
                    // Save File Send
                    $this->Helper->sendFileToSync($restCode, 0, 0);
                    // Save File Send to E-Commerce
                    $this->Helper->sendFileToSyncPublic($syncEco);
                    // Save User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Save Quick Add New', $lastInsertId);
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    // User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Save Quick Add New (Error)');
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        // User Activity
        $this->Helper->saveUserActivity($user['User']['id'], 'Product', 'Quick Add New');
        $companies = ClassRegistry::init('Company')->find('list', array('joins' => array(array('table' => 'user_companies', 'type' => 'inner', 'conditions' => array('user_companies.company_id=Company.id'))), 'conditions' => array('Company.is_active = 1', 'user_companies.user_id=' . $user['User']['id'])));
        $branches  = ClassRegistry::init('Branch')->find('list', array('joins' => array(array('table' => 'user_branches', 'type' => 'inner', 'conditions' => array('user_branches.branch_id=Branch.id'))), 'conditions' => array('Branch.is_active = 1', 'user_branches.user_id=' . $user['User']['id'])));
        $pgroups   = ClassRegistry::init('Pgroup')->find('list', array('order' => 'Pgroup.name', 'conditions' => array('Pgroup.is_active' => 1, 'Pgroup.id IN (SELECT pgroup_id FROM pgroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].'))')));
        $uoms      = ClassRegistry::init('Uom')->find("list", array("conditions" => array("Uom.is_active = 1"), "order" => "Uom.name"));
        $this->set(compact("companies", "branches", "uoms", "pgroups"));
    }
    
    function getSkuUom($uomId = null) {
        $this->layout = 'ajax';
        if ($uomId != null) {
            $this->set('uomId', $uomId);
        } else {
            echo "Error Select Uom";
        }
    }
    
    function addCgroup(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $this->loadModel('Cgroup');
            $result = array();
            $comCheck = $this->data['Cgroup']['company_id'];
            if ($this->Helper->checkDouplicate('name', 'cgroups', $this->data['Cgroup']['name'], 'is_active = 1 AND id IN (SELECT cgroup_id FROM cgroup_companies WHERE company_id IN ('.$comCheck.'))')) {
                $this->Helper->saveUserActivity($user['User']['id'], 'Customer Group', 'Save Quick Add New (Name ready existed)');
                $result['error'] = 2;
                echo json_encode($result);
                exit;
            } else {
                $r = 0;
                $restCode  = array();
                $dateNow   = date("Y-m-d H:i:s");
                $this->Cgroup->create();
                $user = $this->getCurrentUser();
                $this->data['Cgroup']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $this->data['Cgroup']['created']    = $dateNow;
                $this->data['Cgroup']['created_by'] = $user['User']['id'];
                $this->data['Cgroup']['is_active']  = 1;
                if ($this->Cgroup->save($this->data)) {
                    $lastInsertId = $this->Cgroup->getLastInsertId();
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($this->data['Cgroup'], 'cgroups');
                    $restCode[$r]['modified'] = $dateNow;
                    $restCode[$r]['dbtodo']   = 'cgroups';
                    $restCode[$r]['actodo']   = 'is';
                    $r++;
                    // Cgroup company
                    if (!empty($this->data['Cgroup']['company_id'])) {
                        mysql_query("INSERT INTO cgroup_companies (cgroup_id, company_id) VALUES ('" . $lastInsertId . "','" . $this->data['Cgroup']['company_id'] . "')");
                        // Convert to REST
                        $restCode[$r]['cgroup_id']   = $this->data['Cgroup']['sys_code'];
                        $restCode[$r]['company_id']  = $this->Helper->getSQLSysCode("companies", $this->data['Cgroup']['company_id']);
                        $restCode[$r]['modified']    = $dateNow;
                        $restCode[$r]['dbtodo']      = 'cgroup_companies';
                        $restCode[$r]['actodo']      = 'is';
                        $r++;
                    }
                    // Save File Send
                    $this->Helper->sendFileToSync($restCode, 0, 0);
                    // Save User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'Customer Group', 'Save Quick Add New', $lastInsertId);
                    $result['error']  = 0;
                    $result['option'] = '<option value="">'.INPUT_SELECT.'</option>';
                    $cgroups = ClassRegistry::init('Cgroup')->find('all', array('order' => 'name', 'conditions' => array('is_active' => 1)));
                    foreach($cgroups AS $cgroup){
                        $selected = '';
                        if($cgroup['Cgroup']['id'] == $lastInsertId){
                            $selected = 'selected="selected"';
                        }
                        $result['option'] .= '<option value="'.$cgroup['Cgroup']['id'].'" '.$selected.'>'.$cgroup['Cgroup']['name'].'</option>';
                    }
                    echo json_encode($result);
                    exit;
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'Customer Group', 'Save Quick Add New (Error)');
                    $result['error'] = 1;
                    echo json_encode($result);
                    exit;
                }
            }
        }
        $this->Helper->saveUserActivity($user['User']['id'], 'Customer Group', 'Quick Add New');
        $companies = ClassRegistry::init('Company')->find('list', array('order' => 'id', 'conditions' => array('is_active' => 1, 'id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')')));
        $this->set(compact("companies"));
    }
    
    function addTerm(){
        $this->layout = 'ajax';
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $this->loadModel('PaymentTerm');
            $result = array();
            if ($this->Helper->checkDouplicate('name', 'payment_terms', $this->data['PaymentTerm']['name'])) {
                $this->Helper->saveUserActivity($user['User']['id'], 'Payment Term', 'Save Quick Add New (Name ready existed)');
                $result['error'] = 2;
                echo json_encode($result);
                exit;
            } else {
                $r = 0;
                $restCode = array();
                $dateNow  = date("Y-m-d H:i:s");
                $this->PaymentTerm->create();
                $this->data['PaymentTerm']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $this->data['PaymentTerm']['created']    = $dateNow;
                $this->data['PaymentTerm']['created_by'] = $user['User']['id'];
                $this->data['PaymentTerm']['is_active'] = 1;
                if ($this->PaymentTerm->save($this->data)) {
                    $termId = $this->PaymentTerm->id;
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($this->data['PaymentTerm'], 'payment_terms');
                    $restCode[$r]['modified']   = $dateNow;
                    $restCode[$r]['dbtodo']     = 'payment_terms';
                    $restCode[$r]['actodo']     = 'is';
                    // Save File Send
                    $this->Helper->sendFileToSync($restCode, 0, 0);
                    // Save User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'Payment Term', 'Save Quick Add New', $termId);
                    $result['error']  = 0;
                    $result['option'] = '<option value="">'.INPUT_SELECT.'</option>';
                    $terms = ClassRegistry::init('PaymentTerm')->find('all', array('order' => 'name', 'conditions' => array('is_active' => 1)));
                    foreach($terms AS $term){
                        $selected = '';
                        if($term['PaymentTerm']['id'] == $termId){
                            $selected = 'selected="selected"';
                        }
                        $result['option'] .= '<option value="'.$term['PaymentTerm']['id'].'" '.$selected.'>'.$term['PaymentTerm']['name'].'</option>';
                    }
                    echo json_encode($result);
                    exit;
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'Payment Term', 'Save Quick Add New (Error)');
                    $result['error'] = 1;
                    echo json_encode($result);
                    exit;
                }
            }
        }
        $this->Helper->saveUserActivity($user['User']['id'], 'Payment Term', 'Quick Add New');
    }
    
    function quickAddCustomer1(){
        $this->layout = "ajax";
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $this->loadModel('Customer');
            if ($this->Helper->checkDouplicate('name', 'customers', $this->data['Customer']['name'])) {
                $this->Helper->saveUserActivity($user['User']['id'], 'Vendor', 'Save Quick Add New (Name ready existed)');
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $r = 0;
                $restCode  = array();
                $dateNow   = date("Y-m-d H:i:s");
                $this->data['Customer']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $this->data['Customer']['type']       = 2;
                $this->data['Customer']['created']    = $dateNow;
                $this->data['Customer']['created_by'] = $user['User']['id'];
                $this->data['Customer']['is_active']  = 1;
                if ($this->Customer->saveAll($this->data)) {
                    $lastInsertId = $this->Customer->getLastInsertId();
                    // Convert to REST
                    $restCode[$r] = $this->Helper->convertToDataSync($this->data['Customer'], 'customers');
                    $restCode[$r]['modified']   = $dateNow;
                    $restCode[$r]['dbtodo']     = 'customers';
                    $restCode[$r]['actodo']     = 'is';
                    $r++;
                    // Customer group
                    if (!empty($this->data['Customer']['cgroup_id'])) {
                        mysql_query("INSERT INTO customer_cgroups (customer_id,cgroup_id) VALUES ('" . $lastInsertId . "','" . $this->data['Customer']['cgroup_id'] . "')");
                        // Convert to REST
                        $restCode[$r]['customer_id'] = $this->data['Customer']['sys_code'];
                        $restCode[$r]['cgroup_id']   = $this->Helper->getSQLSysCode("cgroups", $this->data['Customer']['cgroup_id']);
                        $restCode[$r]['dbtodo']      = 'customer_cgroups';
                        $restCode[$r]['actodo']      =  'is';
                        $r++;
                    }
                    // Customer Company
                    if (isset($this->data['Customer']['company_id'])) {
                        mysql_query("INSERT INTO customer_companies (customer_id, company_id) VALUES ('" . $lastInsertId . "','" . $this->data['Customer']['company_id'] . "')");
                        // Convert to REST
                        $restCode[$r]['customer_id'] = $this->data['Customer']['sys_code'];
                        $restCode[$r]['company_id']  = $this->Helper->getSQLSysCode("companies", $this->data['Customer']['company_id']);
                        $restCode[$r]['dbtodo']      = 'customer_companies';
                        $restCode[$r]['actodo']      = 'is';
                        $r++;
                    }
                    // Save File Send
                    $this->Helper->sendFileToSync($restCode, 0, 0);
                    // Save User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'Customer', 'Save Quick Add New', $lastInsertId);
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'Customer', 'Save Quick Add New (Error)');
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        if(empty($this->data)){
            $this->Helper->saveUserActivity($user['User']['id'], 'Customer', 'Quick Add New');
            $conditionUser = "id IN (SELECT cgroup_id FROM cgroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))";
            $companies = ClassRegistry::init('Company')->find('list', array('order' => 'id', 'conditions' => array('is_active' => 1, 'id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')')));
            $cgroups   = ClassRegistry::init('Cgroup')->find('list', array('order' => 'id', 'conditions' => array('is_active' => 1, $conditionUser)));
            $paymentTerms = ClassRegistry::init('PaymentTerm')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'name'));
            $code = $this->Helper->getAutoGenerateCustomerCode();
            $this->set(compact('paymentTerms', 'cgroups', "companies", "code"));
        }
    }
    
    
    function quickAddCustomer(){
        $this->layout = "ajax";
        $user = $this->getCurrentUser();
        if (!empty($this->data)) {
            $patientCode = $this->Helper->getAutoGeneratePatientCode();
            //$this->loadModel('Customer');
            $this->loadModel('Patient');
            if ($this->Helper->checkDouplicate('patient_name', 'patients', $this->data['Customer']['name'])) {
                $this->Helper->saveUserActivity($user['User']['id'], 'Vendor', 'Save Quick Add New (Name ready existed)');
                echo MESSAGE_DATA_ALREADY_EXISTS_IN_THE_SYSTEM;
                exit;
            } else {
                $r = 0;
                $restCode  = array();
                $dateNow   = date("Y-m-d H:i:s");
                //$this->data['Customer']['sys_code']   = md5(rand().strtotime(date("Y-m-d H:i:s")).$user['User']['id']);
                $this->data['Patient']['patient_code']  = $patientCode;
                $this->data['Patient']['patient_name']  = $this->data['Customer']['name'];
                $this->data['Patient']['sex']           = $this->data['Customer']['sex'];
                $this->data['Patient']['telephone']     = $this->data['Customer']['main_number'];
                $this->data['Patient']['email']         = $this->data['Customer']['email'];
                $this->data['Patient']['location_id']   = 1;
                $this->data['Patient']['patient_bill_type_id'] = 1;
                $this->data['Patient']['patient_group_id'] = 1;
                $this->data['Patient']['dob']           = $this->data['Customer']['dob'];
                $this->data['Patient']['nationality']   = 36;
                $this->data['Patient']['created']       = $dateNow;
                $this->data['Patient']['created_by']    = $user['User']['id'];
                $this->data['Patient']['is_active']     = 1;
                if ($this->Patient->saveAll($this->data)) {
                    $lastInsertId = $this->Patient->getLastInsertId();
                    $r++;
                    // Save User Activity
                    $this->Helper->saveUserActivity($user['User']['id'], 'Customer', 'Save Quick Add New', $lastInsertId);
                    echo MESSAGE_DATA_HAS_BEEN_SAVED;
                    exit;
                } else {
                    $this->Helper->saveUserActivity($user['User']['id'], 'Customer', 'Save Quick Add New (Error)');
                    echo MESSAGE_DATA_COULD_NOT_BE_SAVED;
                    exit;
                }
            }
        }
        if(empty($this->data)){
            $this->Helper->saveUserActivity($user['User']['id'], 'Customer', 'Quick Add New');
            $conditionUser = "id IN (SELECT cgroup_id FROM cgroup_companies WHERE company_id IN (SELECT company_id FROM user_companies WHERE user_id = ".$user['User']['id']."))";
            $companies = ClassRegistry::init('Company')->find('list', array('order' => 'id', 'conditions' => array('is_active' => 1, 'id IN (SELECT company_id FROM user_companies WHERE user_id = '.$user['User']['id'].')')));
            $cgroups   = ClassRegistry::init('Cgroup')->find('list', array('order' => 'id', 'conditions' => array('is_active' => 1, $conditionUser)));
            $paymentTerms = ClassRegistry::init('PaymentTerm')->find('list', array('conditions' => array('is_active = 1'), 'order' => 'name'));
            $this->set('code', '');
            $sexes = array('M' => GENERAL_MALE, 'F' => GENERAL_FEMALE);
            $this->set(compact('paymentTerms', 'cgroups', "companies", "sexes"));
        }
    }
    
    
    function disByCard() {
        $this->layout = 'ajax';
    }
    
    function discountByItem(){
        $this->layout = 'ajax';
    }
}

?>