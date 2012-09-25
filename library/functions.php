<?php

    function helloWorld(){
        $greeting = "Hello World!";
        $name = "Juan";
        return $greeting . " " . $name;
    }

    function mres($unescaped){
        return mysql_real_escape_string($unescaped);
    }
    
    function backup_tables($host,$user,$pass,$name,$tables = '*'){
        $link = mysql_connect($host,$user,$pass);
        mysql_select_db($name,$link);

        //get all of the tables
        if($tables == '*')
        {
            $tables = array();
            $result = mysql_query('SHOW TABLES');
            while($row = mysql_fetch_row($result))
            {
            $tables[] = $row[0];
            }
        }
        else
        {
            $tables = is_array($tables) ? $tables : explode(',',$tables);
        }

        //cycle through
        foreach($tables as $table)
        {
            $result = mysql_query('SELECT * FROM '.$table);
            $num_fields = mysql_num_fields($result);

            $return.= 'DROP TABLE '.$table.';';
            $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
            $return.= "\n\n".$row2[1].";\n\n";

            for ($i = 0; $i < $num_fields; $i++) 
            {
            while($row = mysql_fetch_row($result))
            {
                $return.= 'INSERT INTO '.$table.' VALUES(';
                for($j=0; $j<$num_fields; $j++) 
                {
                $row[$j] = addslashes($row[$j]);
                $row[$j] = ereg_replace("\n","\\n",$row[$j]);
                if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                if ($j<($num_fields-1)) { $return.= ','; }
                }
                $return.= ");\n";
            }
            }
            $return.="\n\n\n";
        }

        //save file
        $handle = fopen('db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql','w+');
        fwrite($handle,$return);
        fclose($handle);
    }

    function getLastUserId(){
        $query = mysql_query("SELECT * FROM user_accounts ORDER BY user_id DESC LIMIT 1") or die(mysql_error());
        return $query;
    }
    
    function nextUserId(){
        $lastUserData = getLastUserId();
        $lastUser = mysql_fetch_assoc($lastUserData);
        if(mysql_num_rows($lastUserData) < 1){
            $freeUserId = "0000" . "1";
        }
        else{
            $freeUserId = $lastUser["user_id"] + 1;
        }
        return $freeUserId;
    }

    function currentUrl() {
        $page = 'http';
       if (!empty($_SERVER['HTTPS'])) {$page .= "s";}  
            $page .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $page .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $page .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        return $page;
    }
    
    function getUserId($tenant, $username){
        $query = mysql_query("SELECT user_id FROm user_accounts WHERE tenant_code = '$tenant' AND username = '$username'") or die(mysql_error());
        $u = mysql_fetch_assoc($query);
        return $u["user_id"];
    }

    function registerAccount($tenant, $secret, $userId, $username, $password, $firstname, $lastname, $emailAddress, $contactNo, $address1, $address2, $city, $province, $country, $postalCode){
         $query = mysql_query("SELECT username FROM user_accounts WHERE username = '$username'") or die(mysql_error());
         if(mysql_num_rows($query) == 0){
             mysql_query("INSERT INTO user_accounts (tenant_code, tenant_secret, user_id, username, password, firstname, lastname, email_address, contact_no, address_1, address_2, city, province, country, postal_code) VALUES ('$tenant', '$secret', '$userId', '$username', '$password', '$firstname', '$lastname', '$emailAddress', '$contactNo', '$address1', '$address2', '$city', '$province', '$country', '$postalCode')") or die(mysql_error());
         }
    }
    
    function authenticate(){
        if(empty($_SESSION["userId"])){
            header("Location: http://" . ROOT .  "/app/users/login.php?e=1&r=" . currentUrl());
        }
    }

    function loginAccount($username, $password){
        $query = mysql_query("SELECT * FROM user_accounts WHERE username = '$username' AND password = '$password'") or die(mysql_error());
        return $query;
    }
    
    function logout(){
        session_start();
        session_unset();
        session_destroy();
        header("Location: /app/users/login.php");
    }
    
    function updateAccount($userId, $firstname, $lastname, $emailAddress, $contactNo, $address1, $address2, $city, $province, $country, $postalCode, $dateUpdated, $updatedBy){
        
         mysql_query("UPDATE user_accounts SET firstname = '$firstname', lastname = '$lastname', email_address = '$emailAddress', contact_no = '$contactNo', address_1 = '$address1', address_2 = '$address2', city = '$city', province = '$province', country = '$country', postal_code = '$postalCode', last_updated = '$dateUpdated', updated_by = '$updatedBy' WHERE user_id = '$userId'") or die(mysql_error());
    }
    
    function checkErrorMessage($e){
        switch($e){
            case 1:
                echo "Please login with your account";
                break;
            case 2:
                echo "Invalid Username/Password";
                break;
            case 3:
                echo "You don't have enough privileges to access that page";
                break;
        }
    }
    
    function getUsers(){
        $query = mysql_query("SELECT tenant_auth_active, user_id, username, password, firstname, lastname, date_created FROM user_accounts") or die(mysql_error());
        return $query;
    }
    
    function getUserCompleteName($code){
        $query = mysql_query("SELECT firstname, lastname FROM user_accounts WHERE user_id = '$code'") or die(mysql_error());
        $u = mysql_fetch_assoc($query);
        return $u["firstname"] . " " . $u["lastname"];
    }
    
    function getUserDataById($userId){
        $query = mysql_query("SELECT username, firstname, lastname, email_address, contact_no, address_1, address_2, city, province, country, postal_code FROM user_accounts WHERE user_id = '$userId'") or die(mysql_error());
        return mysql_fetch_array($query);
    }
    
    function getUserGroups(){
        $query = mysql_query("SELECT * FROM user_groups") or die(mysql_error());
        return $query;
    }
    
    function getUserGroupSelectStatus(){
        $query = mysql_query("SELECT * FROM user_group_selection WHERE status = '1'") or die(mysql_error());
        $userGroupSelect = mysql_fetch_assoc($query);
        return $userGroupSelect["status"];
    }
    
    function checkUserGroup($userId){
        $query = mysql_query("SELECT ugt.*, ug.group_name FROM user_grouping_table ugt INNER JOIN user_groups ug ON ugt.user_group_id = ug.id WHERE ugt.user_id = '$userId'") or die(mysql_error());
        $userGroupingData = mysql_fetch_assoc($query);
        return $userGroupingData["group_name"];
    }
    
    function checkIfAdministrator($group){
        if($group != "Administrator"){
            echo "<script> window.location.href='http://". ROOT ."?module=cashier'; </script>";
            //header("Location: /?e=3");
        }
    }
    
    function addNewUserGroup($group){
         mysql_query("INSERT INTO user_groups (group_name) VALUES ('$group')") or die(mysql_error());
    }
    
    function getLastDepartmentId(){
        $query = mysql_query("SELECT * FROM departments ORDER BY code DESC LIMIT 1") or die(mysql_error());
        return $query;
    }
    
    function nextDepartmentId(){
        $lastDepartmentData = getLastDepartmentId();
        $lastDepartment = mysql_fetch_assoc($lastDepartmentData);
        if(mysql_num_rows($lastDepartmentData) < 1){
            $freeDepartmentId = 1000;
        }
        else{
            $freeDepartmentId = $lastDepartment["code"] + 1;
        }
        return $freeDepartmentId;
    }
    
    function addDepartment($code, $department){
		$query = mysql_query("SELECT * FROM departments WHERE department_name = '$department'") or die(mysql_error());	
		if(mysql_num_rows($query) == 0){
			mysql_query("INSERT INTO departments (code, department_name) VALUES ('$code', '$department')") or die(mysql_error());
		}
    }
    
    function staticPageDenial(){
        header("Location: /?e=3");
    }

    function getAvailableId($table, $column){
        $query = mysql_query("SELECT * FROM $table ORDER BY $column DESC LIMIT 1") or die(mysql_error());
        $lastColumn = mysql_fetch_assoc($query);
         if(mysql_num_rows($query) < 1){
            $freeColumnId = 1000;
        }
        else{
            $freeColumnId = $lastColumn[$column] + 1;
        }
        return $freeColumnId;
    }

    function addSupplier($code, $supplierName, $contactNo, $address1, $address2, $accreditation, $tinNo, $terms, $contract){
         mysql_query("INSERT INTO suppliers (code, supplier_name, contact_no, address_1, address_2, accreditation, tin_no, terms, contract) VALUES ('$code', '$supplierName', '$contactNo', '$address1', '$address2', '$accreditation', '$tinNo', '$terms', '$contract')") or die(mysql_error());
    }
    
    function assignItemToSupplier($supplier, $sku){
        mysql_query("INSERT INTO supplier_items (supplier_code, sku) VALUES ('$supplier', '$sku')") or die(mysql_error());
    }

    function getDepartments(){
        $query = mysql_query("SELECT * FROM departments ORDER BY department_name") or die(mysql_error());
        return $query;
    } 
    
    function getDepartmentNameByCode($code){
        $query = mysql_query("SELECT department_name FROM departments WHERE code = '$code'") or die(mysql_error());
        $d = mysql_fetch_assoc($query);
        return $d["department_name"];
    }

    function addCategory($code, $departmentCode, $categoryName){
        $query = mysql_query("SELECT * FROM categories WHERE department_code = '$departmentCode' AND category_name = '$categoryName'") or die(mysql_error());
		if(mysql_num_rows($query) == 0){
			mysql_query("INSERT INTO categories (code, department_code, category_name) VALUES ('$code', '$departmentCode', '$categoryName')") or die(mysql_error());
		}
    }
    
    function getCategories(){
        $query = mysql_query("SELECT * FROM categories ORDER BY category_name") or die(mysql_error());
        return $query;
    }
    
    function getCategoryByDepartment($code){
        $query = mysql_query("SELECT * FROM categories WHERE department_code = '$code' ORDER BY category_name") or die(mysql_error());
        return $query;
    }
    
    function getCategoryNameByCode($code){
        $query = mysql_query("SELECT category_name FROM categories WHERE code = '$code'") or die(mysql_error());
        $c = mysql_fetch_assoc($query);
        return $c["category_name"]; 
    }
    
    function addManufacturer($code, $manufacturerName){
		$query = mysql_query("SELECT * FROM manufacturers WHERE manufacturer_name = '$manufacturerName'") or die(mysql_error());
		if(mysql_num_rows($query) == 0){
			mysql_query("INSERT INTO manufacturers (code, manufacturer_name) VALUES ('$code', '$manufacturerName')") or die(mysql_error());
		}
    }
    
    function getManufacturers(){
        $query = mysql_query("SELECT * FROM manufacturers ORDER BY manufacturer_name") or die(mysql_error());
        return $query;
    }
    
    function getManufacturerCodeFromBrand($code){
        $query = mysql_query("SELECT * FROM brands WHERE code = '$code'") or die(mysql_error());
        $mftr = mysql_fetch_assoc($query);
        return $mftr["manufacturer_code"];
    }
    
    function getManufacturerNameByCode($code){
        $query = mysql_query("SELECT manufacturer_name FROM manufacturers WHERE code = '$code'") or die(mysql_error());
        $mftr = mysql_fetch_assoc($query);
        return $mftr["manufacturer_name"];
    }
    
    function getManufacturerCodeFromMBCode($code){
        $query = mysql_query("SELECT manufacturer_code FROM manufacturer_brand WHERE code = '$code'") or die(mysql_error());
        $m = mysql_fetch_assoc($query);
        return $m["manufacturer_code"];
    }
    
    function getBrandCodeFromMBCode($code){
        $query = mysql_query("SELECT brand_code FROM manufacturer_brand WHERE code = '$code'") or die(mysql_error());
        $m = mysql_fetch_assoc($query);
        return $m["brand_code"];
    }
    
    function addBrand($code, $manufacturer, $brandName){
		$query = mysql_query("SELECT * FROM brands WHERE manufacturer_code = '$manufacturer' AND brand_name = '$brandName'") or die(mysql_error());
		if(mysql_num_rows($query) == 0){
			mysql_query("INSERT INTO brands (code, manufacturer_code, brand_name) VALUES ('$code', '$manufacturer', '$brandName')") or die(mysql_error());
		}
    }
    
    function getBrands(){
        $query = mysql_query("SELECT * FROM brands ORDER BY brand_name") or die(mysql_error());
        return $query;
    }
    
    function getBrandByManufacturer($code){
        $query = mysql_query("SELECT * FROM brands WHERE manufacturer_code = '$code' ORDER BY brand_name") or die(mysql_error());
        return $query;
    }
    
    function getBrandNameByCode($manufacturerCode, $brandCode){
        $query = mysql_query("SELECT brand_name FROM brands WHERE manufacturer_code = '$manufacturerCode' AND code = '$brandCode'") or die(mysql_error());
        $brand = mysql_fetch_assoc($query);
        return $brand["brand_name"];
    }
    
    function addType($code, $brand, $type){
		$query = mysql_query("SELECT * FROM types WHERE brand_code = '$brand' AND type_name = '$type'") or die(mysql_error());
		if(mysql_num_rows($query) == 0){
			mysql_query("INSERT INTO types (code, brand_code, type_name) VALUES ('$code', '$brand', '$type')") or die(mysql_error());
		}
    }
    
    function getTypes(){
        $query = mysql_query("SELECT * FROM types ORDER BY type_name") or die(mysql_error());
        return $query;
    }
    
    function getTypeByBrand($code){
        $query = mysql_query("SELECT * FROM types WHERE brand_code = '$code'") or die(mysql_error());
        return $query;
    }
    
    function getTypeByManufacturer($code){
        $query = mysql_query("SELECT m.code AS mftr, b.code AS brnd, t.code AS type, t.type_name FROM manufacturers m INNER JOIN brands b ON b.manufacturer_code = m.code INNER JOIN types t ON t.brand_code = b.code WHERE m.code = '$code'") or die(mysql_error());
        return $query;
    }
    
    function getTypeNameByCode($brandCode, $typeCode){
        $query = mysql_query("SELECT type_name FROM types WHERE brand_code = '$brandCode' AND code = '$typeCode'") or die(mysql_error());
        $type = mysql_fetch_assoc($query);
        return $type["type_name"];
    }
    
    function addItem($itemCode, $sku, $packaging, $department, $category, $description1, $description2, $genericName, $price){
         mysql_query("INSERT INTO items (item_code, sku, packaging_code, department_code, category_code, description_1, description_2, generic_name, price) VALUES ('$itemCode', '$sku', '$packaging', '$department', '$category', '$description1', '$description2', '$genericName', '$price')") or die(mysql_error());
    }
    
    function getManufacturerByCode($code){
        $query = mysql_query("SELECT * FROM manufacturers WHERE code = '$code'") or die(mysql_error());
        return mysql_fetch_assoc($query);
    }
    
    function getBrandByCode($manufacturerCode, $brandCode){
        $query = mysql_query("SELECT * FROM brands WHERE manufacturer_code = '$manufacturerCode' AND code = '$brandCode'") or die(mysql_error());
        return mysql_fetch_assoc($query);
    }
    
    function getTypeByCode($brandCode, $typeCode){
        $query = mysql_query("SELECT * FROM types WHERE brand_code = '$brandCode' AND code = '$typeCode'") or die(mysql_error());
        return mysql_fetch_assoc($query);
    }
    
    function getItemDetailsBySku($sku){
        $query = mysql_query("SELECT i.id, i.item_code, i.sku, i.department_code, i.generic_name, i.packaging_code, i.category_code, i.description_1, description_2, i.price, i.date_created, i.active, it.tax_code, t.rate FROM items i INNER JOIN item_tax it ON it.item_sku = i.sku INNER JOIN taxes t ON t.code = it.tax_code WHERE i.sku = '$sku'") or die(mysql_error());
        return $query;
    }
    
    function getShortItemDescriptionBySku($sku){
        $query = mysql_query("SELECT description_2 FROM items WHERE sku = '$sku'") or die(mysql_error());
        $i = mysql_fetch_assoc($query);
        return $i["description_2"];
    }
    
    function addMeasurement($measurement){
        $query = mysql_query("SELECT measurement_name FROM measurements WHERE measurement_name = '$measurement'") or die(mysql_error());
        if(mysql_num_rows($query) == 0){
            mysql_query("INSERT INTO measurements (measurement_name) VALUES ('$measurement')") or die(mysql_error());
        }
    }
    
    function getMeasurements(){
        $query = mysql_query("SELECT * FROM measurements ORDER BY measurement_name") or die(mysql_error());
        return $query;
    }
    
    function checkItemCodeExistence($code){
        $query = mysql_query("SELECT * FROM items WHERE item_code = '$code'") or die(mysql_error());
        return $query;
    }
    
    function checkItemPackDescExistence($packageCode, $desc){
        $query = mysql_query("SELECT packaging_code, description_1 FROM items WHERE packaging_code = '$packageCode' AND description_1 = '$desc'") or die(mysql_error());
        return mysql_num_rows($query);
    }
    
    function getAvailableSku(){
        $query = mysql_query("SELECT * FROM sku ORDER BY sku DESC LIMIT 1") or die(mysql_error());
        $lastSku = mysql_fetch_assoc($query);
         if(mysql_num_rows($query) < 1){
            $freeSku = "100000";
            $checkSkuExistenceQuery = mysql_query("SELECT sku FROM sku WHERE sku = '$freeSku'") or die(mysql_error());
            if(mysql_num_rows($checkSkuExistenceQuery) == 1){
                $freeSku = "100000";
            }
        }
        else{
            $skuInt = preg_replace("/[^0-9]/", '', $lastSku["sku"]);
            $freeSku = $skuInt + 1;
        }
        return $freeSku;
    }
    
    function getDepartmentAndCategoryBySku($sku){
        $query = mysql_query("SELECT i.*, c.*, d.* FROM items i INNER JOIN categories c ON i.category_code = c.code INNER JOIN departments d ON i.department_code = d.code WHERE i.sku = '$code'") or die(mysql_error());
        return $query;
    }
    
    function addName($name){
         mysql_query("INSERT INTO names (name) VALUES ('$name')") or die(mysql_error());
    }
    
    function getDataByName($name){
        $query = mysql_query("SELECT * FROM names WHERE name = '$name'") or die(mysql_error());
        return $query;
    }
    
    function newTransactionNo(){
        $query = mysql_query("SELECT DISTINCT transaction_no FROM transactions ORDER BY transaction_no DESC LIMIT 1") or die(mysql_error());
        $lastTrans = mysql_fetch_assoc($query);
        if(mysql_num_rows($query) < 1){
            $freeTransNo = "100000000000";
        }
        else{
            $lastTransNo = preg_replace("/[^0-9]/", '', $lastTrans["transaction_no"]);
            $freeTransNo = $lastTransNo + 1;
        }
        
        return $freeTransNo;
    }
    
    function newTransaction($transNo){
         mysql_query("INSERT INTO transactions (transaction_no) VALUES ('$transNo')") or die(mysql_error());
    }
    
    function checkTransactionNoExistence($transNo){
        $query = mysql_query("SELECT * FROM transactions WHERE transaction_no = '$transNo'") or die(mysql_error());
        return mysql_num_rows($query);
    }
    
    function newTransactionHeader($transactionNo, $storeCode, $registerCode, $userId, $sku, $description, $quantity, $price, $subtotal, $totalAmount, $discountCode, $discountAmount, $taxCode, $taxAmount, $systemDate, $systemTime){
         mysql_query("INSERT INTO transaction_header (transaction_no, store_code, register_code, user_id, sku, description, quantity, price, subtotal, total_amount, discount_code, discount_amount, tax_code, tax_amount, system_date, system_time) VALUES ('$transactionNo', '$storeCode', '$registerCode', '$userId', '$sku', '$description', '$quantity', '$price', '$subtotal', '$totalAmount', '$discountCode', '$discountAmount', '$taxCode', '$taxAmount', '$systemDate', '$systemTime') ") or die(mysql_error());
    }
    
    function getHeaderByTransNo($transNo){
        $query = mysql_query("SELECT * FROM transaction_header WHERE transaction_no = '$transNo'") or die(mysql_error());
        return $query;
    }
    
    function getHeaderByTransNoEntry($transNo){
        $query = mysql_query("SELECT * FROM transaction_header WHERE transaction_no = '$transNo' AND remarks IN('Pending', 'OnHold') ORDER BY id DESC") or die(mysql_error());
        return $query;
    }
    
    function getOnHoldHeaderByTransNoEntry($transNo){
        $query = mysql_query("SELECT * FROM transaction_header WHERE transaction_no = '$transNo' AND remarks = 'OnHold'") or die(mysql_error());
        return $query;
    }
    
    function getHeaderByTransNoReceipt($transNo){
        $query = mysql_query("SELECT * FROM transaction_header WHERE transaction_no = '$transNo' AND remarks = 'Completed'") or die(mysql_error());
        return $query;
    }
    
    function getTransactions(){
        $query = mysql_query("SELECT * FROM transactions") or die(mysql_error());
        return $query;
    }
    
    function getTransactionDataByTransNo($transNo){
        $query = mysql_query("SELECT DISTINCT th.remarks, tp.* FROM transaction_payment tp INNER JOIN transaction_header th ON tp.transaction_no = th.transaction_no WHERE tp.transaction_no = '$transNo'") or die(mysql_error());
        return $query;
    }
    
    function viewTransactionItems($transNo){
        $query = mysql_query("SELECT * FROM transaction_header WHERE transaction_no = '$transNo'") or die(mysql_error());
        return $query;
    }
    
    function viewCompletedTransactionItems($transNo){
        $query = mysql_query("SELECT * FROM transaction_header WHERE transaction_no = '$transNo' AND remarks = 'Completed'") or die(mysql_error());
        return $query;
    }
    
    function viewCompletedTotalAmountsByDate($date){
        $query = mysql_query("SELECT SUM(quantity) AS total_quantity, price, SUM(total_amount) AS total_sales_amount, SUM(discount_amount) AS total_discount, SUM(tax_amount) AS total_tax FROM transaction_header WHERE system_date = '$date' AND remarks IN ('Completed', 'Returned', 'Completed (Return)')") or die(mysql_error());
        return mysql_fetch_assoc($query);
    }
    
    function viewCompletedTransactionItemsByDate($date){
        $query = mysql_query("SELECT * FROM transaction_header WHERE system_date = '$date' AND remarks = 'Completed'") or die(mysql_error());
        return $query;
    }
    
    function viewCompletedTotalAmountsByDateRange($start, $end){
        $query = mysql_query("SELECT SUM(quantity) AS total_quantity, price, SUM(total_amount) AS total_sales_amount, SUM(discount_amount) AS total_discount, SUM(tax_amount) AS total_tax FROM transaction_header WHERE system_date BETWEEN DATE('$start') AND DATE('$end') AND remarks = 'Completed'") or die(mysql_error());
        return mysql_fetch_assoc($query);
    }
    
    function viewCompletedTransactionItemsByDateRange($start, $end){
        $query = mysql_query("SELECT * FROM transaction_header WHERE system_date BETWEEN DATE('$start') AND DATE('$end') AND remarks = 'Completed'") or die(mysql_error());
        return $query;
    }
    
    function getTransactionTotalAmount($transNo){
        $query = mysql_query("SELECT SUM(total_amount) AS transaction_total_amount FROM transaction_header WHERE transaction_no = '$transNo' AND NOT remarks = 'Removed'") or die(mysql_error());
        $total = mysql_fetch_assoc($query);
        return $total["transaction_total_amount"];
    }
    
    function getTransactionSubtotal($transNo){
        $query = mysql_query("SELECT SUM(subtotal) AS transaction_subtotal FROM transaction_header WHERE transaction_no = '$transNo' AND NOT remarks = 'Removed'") or die(mysql_error());
        $total = mysql_fetch_assoc($query);
        return $total["transaction_subtotal"];
    }
    
    function transactionPayment($transNo, $storeCode, $registerCode, $userId, $subTotal, $discountAmount, $totalAmount, $totalTendered, $balance, $systemDate, $systemTime){
         mysql_query("INSERT INTO transaction_payment (transaction_no, store_code, register_code, user_id, subtotal, disc_amount, total_amount, total_tendered, balance, system_date, system_time) VALUES ('$transNo', '$storeCode', '$registerCode', '$userId', '$subTotal', '$discountAmount', '$totalAmount', '$totalTendered', '$balance', '$systemDate', '$systemTime')") or die(mysql_error());
    }
    
    function getTransactionPaymentDataByTransNo($transNo){
        $query = mysql_query("SELECT * FROM transaction_payment WHERE transaction_no = '$transNo'") or die(mysql_error());
        return $query;
    }
    
    function updatePaidTransaction($transNo){
         mysql_query("UPDATE transaction_header SET remarks = 'Completed' WHERE transaction_no = '$transNo' AND remarks = 'Pending'") or die(mysql_error());
    }
    
    function getTransactionCompletedEntries($transNo){
        $query = mysql_query("SELECT remarks FROM transaction_header WHERE transaction_no = '$transNo' AND remarks = 'Completed'") or die(mysql_error());
        return mysql_num_rows($query);
    }
    
    function holdTransaction($transNo){
         mysql_query("UPDATE transaction_header SET remarks = 'OnHold' WHERE transaction_no = '$transNo'") or die(mysql_error());
    }
    
    function cancelAllItems($transNo){
         mysql_query("UPDATE transaction_header SET remarks = 'Cancelled' WHERE transaction_no = '$transNo'") or die(mysql_error());
    }
    
    function getTotalSalesPerItem($sku){
        $query = mysql_query("SELECT sku, description, SUM(quantity) AS total_quantity, price, SUM(total_amount) AS total_sales_amount, SUM(discount_amount) AS total_discount, SUM(tax_amount) AS total_tax FROM transaction_header WHERE sku = '$sku' AND remarks = 'Completed'") or die(mysql_error());
        return mysql_fetch_assoc($query);
    }
    
    function getSalesPerItem($sku){
        $query = mysql_query("SELECT * FROM transaction_header WHERE sku = '$sku' AND remarks = 'Completed'") or die(mysql_error());
        return $query;
    }

    function getSalesByDate($date){
        $query = mysql_query("SELECT SUM(quantity) AS total_quantity,SUM(total_amount) as trans_total_amount, SUM(discount_amount) AS total_discount, SUM(tax_amount) AS total_tax FROM transaction_header WHERE remarks = 'Completed' AND system_date = '$date'") or die(mysql_error());
        return $query;
    }
    
    function getTransactionSaleDates($start, $end){
        $query = mysql_query("SELECT DISTINCT system_date FROM sales_entry WHERE system_date BETWEEN DATE('$start') AND DATE('$end') AND status = 'Completed'") or die(mysql_error());
        return $query;
    }
    
    function getTransactionDataByDateRange($start, $end){
        $query = mysql_query("SELECT SUM(total_amount) as trans_total_amount, SUM(discount_amount) AS total_discount, SUM(tax_amount) AS total_tax FROM sales_entry WHERE system_date BETWEEN DATE('$start') AND DATE('$end') AND status = 'Completed'") or die(mysql_error());
        return $query;
    }
    
    function searchItem($description){
        $query = mysql_query("SELECT * FROM items WHERE description_1 LIKE '%$description%'") or die(mysql_error());
        return $query;
    }
    
    function getItems(){
        $query = mysql_query("SELECT * FROM items") or die(mysql_error());
        return $query;
    }
    
    function addSalesTax($code, $type, $description, $rate){
        $query = mysql_query("SELECT * FROM taxes WHERE description = '$description' AND rate = '$rate'") or die(mysql_error());
        if(mysql_num_rows($query) == 0){
            mysql_query("INSERT INTO taxes (code, type, description, rate) VALUES ('$code', '$type', '$description', '$rate')") or die(mysql_error());
        }
    }
    
    function getTaxes(){
        $query = mysql_query("SELECT * FROM taxes") or die(mysql_error());
        return $query;
    }
    
    function getTaxCodeBySku($sku){
        $query = mysql_query("SELECT tax_code FROM item_tax WHERE item_sku = '$sku'") or die(mysql_error());
        $t = mysql_fetch_assoc($query);
        return $t["tax_code"];
    }
    
    function applyTax($sku, $tax){
         mysql_query("INSERT INTO item_tax (item_sku, tax_code) VALUES ('$sku', '$tax')") or die(mysql_error());
    }
    
    function addDiscount($code, $type, $description, $rate){
         mysql_query("INSERT INTO discounts (code, type, description, rate) VALUES ('$code', '$type', '$description', '$rate')") or die(mysql_error());
    }
    
    function getDiscounts(){
        $query = mysql_query("SELECT * FROM discounts") or die(mysql_error());
        return $query;
    }
    
    function getTransactionTotalDiscount($transNo){
        $query = mysql_query("SELECT SUM(discount_amount) AS total_discount FROM transaction_header WHERE transaction_no = '$transNo' AND NOT remarks = 'Removed'") or die(mysql_error());
        $disc = mysql_fetch_assoc($query);
        return $disc["total_discount"];
    }
    
    function removeItemEntry($id){
         mysql_query("UPDATE transaction_header SET remarks = 'Removed' WHERE id = '$id'") or die(mysql_error());
    }
    
    function getCompletedItemTransaction($transNo){
        $query = mysql_query("SELECT * FROM transaction_header WHERE transaction_no = '$transNo' AND remarks = 'Completed'") or die(mysql_error());
        return $query;
    }
    
    function getCurrentItemCount($sku){
        $query = mysql_query("SELECT stock_count FROM inventory_count WHERE sku = '$sku'") or die(mysql_error());
        $count = mysql_fetch_assoc($query);
        return $count["stock_count"];
    }
    
    function updateItemCount($sku, $updatedCount, $lastUpdate){
         mysql_query("UPDATE inventory_count SET stock_count = '$updatedCount', last_updated = '$lastUpdate' WHERE sku = '$sku'") or die(mysql_error());
    }
    
    function checkItemAvailability($sku){
        $query = mysql_query("SELECT stock_count FROM inventory_count WHERE sku  = '$sku'") or die(mysql_error());
        $item = mysql_fetch_assoc($query);
        $available = false;
        if($item["stock_count"] >= 1){
            $available = true;
        }
        return $available;
    }
    
    function processTransaction($transNo, $storeCode, $registerCode, $userId, $subTotal, $taxAmount, $discountAmount, $totalAmount, $systemDate, $systemTime, $status){
        $query = mysql_query("SELECT transaction_no FROM sales_entry WHERE transaction_no = '$transNo'") or die(mysql_error());
        if(mysql_num_rows($query) == 0){
            mysql_query("INSERT INTO sales_entry (transaction_no, store_code, register_code, user_id, sub_total, tax_amount, discount_amount, total_amount, system_date, system_time, status) VALUES ('$transNo', '$storeCode', '$registerCode', '$userId', '$subTotal', '$taxAmount', '$discountAmount', '$totalAmount', '$systemDate', '$systemTime', '$status')") or die(mysql_error());
        }
        else{
            mysql_query("UPDATE sales_entry SET store_code = '$storeCode', register_code = '$registerCode', user_id = '$userId', sub_total = '$subTotal', tax_amount = '$taxAmount', discount_amount = '$discountAmount', total_amount = '$totalAmount', system_date = '$systemDate', system_time = '$systemTime', status = '$status' WHERE transaction_no = '$transNo'") or die(mysql_error());
        }
    }
    
    function processHoldTransaction($transNo, $status){
         mysql_query("UPDATE sales_entry SET status = '$status' WHERE transaction_no = '$transNo'") or die(mysql_error());
    }
    
    function getCompletedTransactionData($transNo){
        $query = mysql_query("SELECT * FROM sales_entry WHERE transaction_no = '$transNo' AND status = 'Completed'") or die(mysql_error());
        return $query;
    }
    
    function applyTaxAmount($sku){
        $query = mysql_query("SELECT i_t.*, t.* FROM item_tax i_t INNER JOIN taxes t ON i_t.tax_code = t.code WHERE i_t.item_sku = '$sku'") or die(mysql_error());
        return $query;
    }
    
    function getTaxRate($code){
        $query = mysql_query("SELECT rate FROM taxes WHERE code = '$code'") or die(mysql_error());
        $tax = mysql_fetch_assoc($query);
        return $tax["rate"];
    }
    
    function getTransactionTotalTax($transNo){
        $query = mysql_query("SELECT SUM(tax_amount) AS total_tax FROM transaction_header WHERE transaction_no = '$transNo' AND NOT remarks = 'Removed'") or die(mysql_error());
        $tax = mysql_fetch_assoc($query);
        return $tax["total_tax"];
    }
    
    function getOnHoldTransactionData($transNo){
        $query = mysql_query("SELECT * FROM sales_entry WHERE transaction_no = '$transNo' AND status = 'OnHold'") or die(mysql_error());
        return $query;
    }
    
    function getCancelledTransactionData($transNo){
        $query = mysql_query("SELECT * FROM sales_entry WHERE transaction_no = '$transNo' AND status = 'Cancelled'") or die(mysql_error());
        return $query;
    }
    
    function getReturnedTransactionData($transNo){
        $query = mysql_query("SELECT * FROM sales_entry WHERE transaction_no = '$transNo' AND status = 'Returned'") or die(mysql_error());
        return $query;
    }
    
    function getItemDescriptionBySku($sku){
        $query = mysql_query("SELECT description_1 FROM items WHERE sku = '$sku'") or die(mysql_error());
        $item = mysql_fetch_assoc($query);
        return $item["description_1"];
    }
    
    function getItemCodes(){
        $query = mysql_query("SELECT item_code FROM items") or die(mysql_error()); 
        return $query;
    }
    
    function getSkus(){
        $query = mysql_query("SELECT sku FROM items") or die(mysql_error()); 
        return $query;
    }
    
    function getItemInventoryCount($sku){
        $query = mysql_query("SELECT * FROM inventory_count WHERE sku = '$sku'") or die(mysql_error());
        return $query;
    }
    
    function updateInventoryCount($sku, $stockCount, $reorderMin, $reorderLevel, $lastUpdate){
        $query = mysql_query("SELECT sku FROM inventory_count WHERE sku = '$sku'") or die(mysql_error());
        if(mysql_num_rows($query) >= 1){
            mysql_query("UPDATE inventory_count SET stock_count = '$stockCount', reorder_min_count = '$reorderMin', reorder_level = '$reorderLevel', last_updated = '$lastUpdate' WHERE sku = '$sku'") or die(mysql_error());
        }
        else{
            mysql_query("INSERT INTO inventory_count (sku, stock_count, reorder_min_count, reorder_level, last_updated) VALUES ('$sku', '$stockCount', '$reorderMin', '$reorderLevel', '$lastUpdate')") or die(mysql_error());
        }
    }
    
    function newInventoryCount($sku, $stockCount, $reorderMin, $reorderLevel, $lastUpdate){
        mysql_query("INSERT INTO inventory_count (sku, stock_count, reorder_min_count, reorder_level, last_updated) VALUES ('$sku', '$stockCount', '$reorderMin', '$reorderLevel', '$lastUpdate')") or die(mysql_error());
    }
   
    function processCancelledTransaction($transNo, $storeCode, $registerCode, $userId, $subTotal, $taxAmount, $discountAmount, $totalAmount, $systemDate, $systemTime){
        mysql_query("INSERT INTO transaction_cancel (transaction_no, store_code, register_code, user_id, sub_total, tax_amount, discount_amount, total_amount, system_date, system_time) VALUES ('$transNo', '$storeCode', '$registerCode', '$userId', '$subTotal', '$taxAmount', '$discountAmount', '$totalAmount', '$systemDate', '$systemTime')") or die(mysql_error());
    }
    
    function cancelSalesEntry($transNo){
        mysql_query("UPDATE sales_entry SET status = 'Cancelled' WHERE transaction_no = '$transNo'") or die(mysql_error());
    }
    
    function getCancelledTransactions(){
        $query = mysql_query("SELECT * FROM transaction_cancel") or die(mysql_error());
        return $query;
    }
    
    function checkEntryTax($id){
        $query = mysql_query("SELECT tax_amount FROM transaction_header WHERE id = '$id'") or die(mysql_error());
        $tax = mysql_fetch_assoc($query);
        return $tax["tax_amount"];
    }
    
    function updateEntryQuantity($id, $quantity, $subtotal, $amount, $taxAmount){
        mysql_query("UPDATE transaction_header SET quantity = '$quantity', subtotal = '$subtotal', total_amount = '$amount', tax_amount = '$taxAmount' WHERE id = '$id'") or die(mysql_error());
    }
    
    function updateEntryTax($id, $finalAmount, $tax){
        mysql_query("UPDATE transaction_header SET total_amount = '$finalAmount', tax_amount = '$tax' WHERE id = '$id'") or die(mysql_error());
    }
    
    function checkSku($sku){
        $query = mysql_query("SELECT sku FROM items WHERE sku = '$sku'") or die(mysql_error());
        return $query;
    }
    
    function newManufacturerBrandCode($code, $manufacturer, $brand){
        mysql_query("INSERT INTO manufacturer_brand (code, manufacturer_code, brand_code) VALUES ('$code', '$manufacturer', '$brand')") or die(mysql_error());
    }
    
    function getManufacturerBrandCode($manufacturer, $brand){
        $query = mysql_query("SELECT code FROM manufacturer_brand WHERE manufacturer_code = '$manufacturer' AND brand_code = '$brand'") or die(mysql_error());
        $mb = mysql_fetch_assoc($query);
        return $mb["code"];
    }
    
    function getAvailableMeasurementCode($manufacturer, $brand, $type, $measurement){
        $query = mysql_query("SELECT code FROM measurement_code WHERE manufacturer = '$manufacturer' AND brand = '$brand' AND type = '$type' AND measurement_name = '$measurement' ORDER BY code DESC LIMIT 1") or die(mysql_error());
        $result = mysql_num_rows($query);
        $mea = mysql_fetch_assoc($query);
        $lastMeasurementCode = $mea["code"];
        if($result >= 1){
            $freeMCode = $lastMeasurementCode;
        }
        else{
            $checkMeasurement = mysql_query("SELECT code FROM measurement_code ORDER BY code DESC LIMIT 1") or die(mysql_error());
            $m = mysql_fetch_assoc($checkMeasurement);
            if(mysql_num_rows($checkMeasurement)>= 1){
                $freeMCode = $m["code"] + 1;
            }
            else{
                $freeMCode = 1000;
            }
        }
        return $freeMCode;
    }
    
    function newMeasurementCode($measurementCode, $manufacturer, $brand, $type, $measurement){
        mysql_query("INSERT INTO measurement_code (code, manufacturer, brand, type, measurement_name) VALUES ('$measurementCode', '$manufacturer', '$brand', '$type', '$measurement')") or die(mysql_error());
    }
    
    function addPackaging($code, $packaging, $quantity, $description){
		$query = mysql_query("SELECT * FROM packagings WHERE description = '$description'") or die(mysql_error());
		if(mysql_num_rows($query) == 0){
			mysql_query("INSERT INTO packagings (code, packaging, quantity, description) VALUES ('$code', '$packaging', '$quantity', '$description')") or die(mysql_error());
		}
    }
    
    function getPackagings(){
        $query = mysql_query("SELECT * FROM packagings ORDER BY description") or die(mysql_error());
        return $query;
    }
    
    function getPackagingName($code){
        $query = mysql_query("SELECT * FROM packagings WHERE code = '$code'") or die(mysql_error());
        $t = mysql_fetch_assoc($query);
        return $t["packaging"] . " of " . $t["quantity"];
    }
    
    function newSku($sku){
        mysql_query("INSERT INTO sku (sku) VALUES ('$sku')") or die(mysql_error());
    }
    
    function getBrandNameByItemCode($code){
        $query = mysql_query("SELECT b.brand_name FROM manufacturer_brand mb INNER JOIN brands b ON mb.brand_code = b.code WHERE mb.code = '$code'") or die(mysql_error());
        $mb = mysql_fetch_assoc($query);
        return $mb["brand_name"];
    }
    
    function getBrandCodeFromItemCode($code){
        $query = mysql_query("SELECT brand_code FROM manufacturer_brand WHERE code = '$code'") or die(mysql_error());
        $b = mysql_fetch_assoc($query);
        return $b["brand_code"];
    }
    
    function getPackagingFromItemCode($code){
        $query = mysql_query("SELECT description FROM packagings WHERE code = '$code'") or die(mysql_error());
        $p = mysql_fetch_assoc($query);
        return $p["description"];
    }
    
    function getPackagingCodeFromItemCode($code){
        $query = mysql_query("SELECT packaging_code FROM items WHERE item_code = '$code'") or die(mysql_error());
        $p = mysql_fetch_assoc($query);
        return $p["packaging_code"];
    }
    
    function getSku(){
        $query = mysql_query("SELECT sku FROM items");
        return $query;
    }
    
    function getSkuByDescription($description){
        $query = mysql_query("SELECT sku FROm items WHERE description_1 = '$description'") or die(mysql_error());
        $i = mysql_fetch_assoc($query);
        return $i["sku"];
    }
    
    function getDescriptionBySku($sku){
        $query = mysql_query("SELECT description_1 FROm items WHERE sku = '$sku'") or die(mysql_error());
        $i = mysql_fetch_assoc($query);
        return $i["description_1"];
    }
    
    function getItemCodeBySku($sku){
        $query = mysql_query("SELECT item_code FROM items WHERE sku = '$sku'") or die(mysql_error());
        $i = mysql_fetch_assoc($query);
        return $i["item_code"];
    }
    
    function getStockOnHandBySku($sku){
        $query = mysql_query("SELECT stock_count FROM inventory_count WHERE sku = '$sku'") or die(mysql_error());
        $i = mysql_fetch_assoc($query);
        return $i["stock_count"];
    }
    
    function getStockReorderMinBySku($sku){
        $query = mysql_query("SELECT reorder_min_count FROM inventory_count WHERE sku = '$sku'") or die(mysql_error());
        $i = mysql_fetch_assoc($query);
        return $i["reorder_min_count"];
    }
    
    function getPackagingCodeFromSku($sku){
        $query = mysql_query("SELECT packaging_code FROM items WHERE sku = '$sku'") or die(mysql_error());
        $i = mysql_fetch_assoc($query);
        return $i["packaging_code"];
    }
    
    function getQuantityFromPackagingCode($packagingCode){
        $query = mysql_query("SELECT quantity FROM packagings WHERE code = '$packagingCode'") or die(mysql_error());
        $p = mysql_fetch_assoc($query);
        return $p["quantity"];
    }
    
    function getSuppliers(){
        $query = mysql_query("SELECT code, supplier_name FROM suppliers") or die(mysql_error());
        return $query;
    }
    
    function getSupplierNameByCode($code){
        $query = mysql_query("SELECT supplier_name FROM suppliers WHERE code = '$code'") or die(mysql_error());
        $s = mysql_fetch_assoc($query);
        return $s["supplier_name"];
    }
    
    function getPurchaseOrders(){
        $query = mysql_query("SELECT * FROM item_orders") or die(mysql_error());
        return $query;
    }
    
    function newPurchaseOrder($purchaseNo, $sku, $quantity, $price, $totalAmount, $waiting, $deliveryStatus, $systemDate, $systemTime){
        mysql_query("INSERT INTO item_in (purchase_no, sku, quantity, unit_price, total_amount, waiting, delivery_status, system_date, system_time) VALUES ('$purchaseNo', '$sku', '$quantity', '$price', '$totalAmount', '$waiting', '$deliveryStatus', '$systemDate', '$systemTime')") or die(mysql_error());
    }
    
    function getPurchaseOrderByPONo($purchaseNo){
        $query = mysql_query("SELECT * FROM item_in WHERE purchase_no = '$purchaseNo'") or die(mysql_error());
        return $query;
    }
    
    function getItemsBySupplier($code){
        $query = mysql_query("SELECT i.sku, i.description_1 FROM items i INNER JOIN supplier_items s ON i.sku = s.sku WHERE s.supplier_code = '$code'") or die(mysql_error());
        return $query;
    }
    
    function getAvailablePONo(){
        $query = mysql_query("SELECT purchase_no FROM item_orders ORDER BY purchase_no DESC LIMIT 1") or die(mysql_error());
        $lastPO = mysql_fetch_assoc($query);
         if(mysql_num_rows($query) < 1){
            $freePO = 1;
        }
        else{
            $freePO = $lastPO["purchase_no"] + 1;
        }
        return $freePO;
    }
    
    function getPriceBySku($sku){
        $query = mysql_query("SELECT price FROM items WHERE sku = 'sku'") or die(mysql_error());
        $p = mysql_fetch_assoc($query);
        return $p["price"];
    }
    
    function removeAllPOEntries($purchaseNo){
        mysql_query("DELETE FROM item_in WHERE purchase_no = '$purchaseNo'") or die(mysql_error());
    }

    function getPOTotalAmount($purchaseNo){
        $query = mysql_query("SELECT SUM(total_amount) AS po_total_amount FROM item_in WHERE purchase_no = '$purchaseNo'") or die(mysql_error());
        $p = mysql_fetch_assoc($query);
        return $p["po_total_amount"];
    }
    
    function filePurchaseOrder($purchaseNo, $supplier, $attentionTo, $preparedBy, $status, $totalAmount, $systemDate, $systemTime){
        mysql_query("INSERT INTO item_orders (purchase_no, supplier_code, attention_to, prepared_by, status, total_amount, system_date, system_time) VALUES ('$purchaseNo', '$supplier', '$attentionTo', '$preparedBy', '$status', '$totalAmount', '$systemDate', '$systemTime')") or die(mysql_error());
    }
    
    function getPurchaseOrderEntries($purchaseNo){
        $query = mysql_query("SELECT ii.*, io.* FROM item_in ii INNER JOIN item_orders io ON ii.purchase_no = io.purchase_no WHERE ii.purchase_no = '$purchaseNo'") or die(mysql_error());
        return $query;
    }
    
    function getPOEntriesDeliveryStatus($purchaseNo){
        $query = mysql_query("SELECT SUM(delivery_status) AS total_delivery_status FROM item_in WHERE purchase_no = '$purchaseNo'") or die(mysql_error());
        $p = mysql_fetch_assoc($query);
        return $p["total_delivery_status"];
    }
    
    function getTotalPurchaseQuantity($purchaseNo){
        $query = mysql_query("SELECT SUM(quantity) AS total_quantity FROM item_in WHERE purchase_no = '$purchaseNo'") or die(mysql_error());
        $p = mysql_fetch_assoc($query);
        return $p["total_quantity"];
    }
    
    function getPOEntryDeliveryStatus($po, $entry){
        $query = mysql_query("SELECT delivery_status FROM item_in WHERE purchase_no = '$po' AND id = '$entry'") or die(mysql_error());
        $p = mysql_fetch_assoc($query);
        return $p["delivery_status"];
    }
    
    function checkBatchExistence($batchNo){
        $query = mysql_query("SELECT batch_no FROM deliveries WHERE batch_no = '$batchNo'") or die(mysql_error());
        return mysql_num_rows($query);
    }
    
    function checkPODeliveryExistence($purchaseNo){
        $query = mysql_query("SELECT purchase_no FROM deliveries WHERE purchase_no = '$purchaseNo'") or die(mysql_error());
        return mysql_num_rows($query);
    }
    
    function checkNosOfDeliveries(){
        $query = mysql_query("SELECT * FROM deliveries") or die(mysql_error());
        return mysql_num_rows($query);
    }
    
    function checkSamePOBatchExistence($purchaseNo, $batchNo){
        $query = mysql_query("SELECT batch_no FROM deliveries WHERE purchase_no = '$purchaseNo' AND batch_no = '$batchNo'") or die(mysql_error());
        return mysql_num_rows($query);
    }
    
    function assignBatchNo($purchaseNo){
        $query = mysql_query("SELECT batch_no FROM deliveries ORDER BY batch_no DESC LIMIT 1") or die(mysql_error());
        $b = mysql_fetch_assoc($query);
        $batchNoSamePOExists = checkSamePOBatchExistence($purchaseNo, $b["batch_no"]);
        $noOfPODeliveries = checkNosOfDeliveries();
        $poDeliveryExists = checkPODeliveryExistence($purchaseNo);
        $batchNo = 0;
        if($noOfPODeliveries == 0){
            $batchNo = date("ymd") . "-" . "001";
        }
        elseif($poDeliveryExists == 0){
            $lastBatchSuffix = substr($b["batch_no"], -3);
            $batchNoSuffix = sprintf('%03u', $lastBatchSuffix + 1);
            $batchNo = date("ymd") . "-" . $batchNoSuffix;
        }
        elseif($poDeliveryExists == 1 && $batchNoSamePOExists == 0){
            $lastBatchSuffix = substr($b["batch_no"], -3);
            $batchNoSuffix = sprintf('%03u', $lastBatchSuffix + 1);
            $batchNo = date("ymd") . "-" . $batchNoSuffix;
        }
        elseif($batchNoSamePOExists >= 1){
            $batchNo = $b["batch_no"];
        }
        return $batchNo;
    }
    
    function assignItemRecordNo($batchNo){
        $query = mysql_query("SELECT item_record_no FROM deliveries WHERE batch_no = '$batchNo' ORDER BY item_record_no DESC LIMIT 1") or die(mysql_error());
        $r = mysql_fetch_assoc($query);
        $result = mysql_num_rows($query);
        $itemRecordNo = 0;
        if($result < 1){
            $itemRecordNo = sprintf('%03u', 1);
        }
        else{
            $itemRecordNo = sprintf('%03u', $r["item_record_no"] + 1);
        }
        return $itemRecordNo;
    }
    
    function receiveDelivery($purchaseNo, $supplier, $sku, $receivedQuantity, $amount, $unitPrice, $vatable, $vatAmount, $remaining, $discrepancy, $receivedBy, $status, $dateReceived, $deliveryNo, $salesInvoice, $batchNo, $itemRecordNo, $expiration, $expense, $docDate, $docTime){
        mysql_query("INSERT INTO deliveries (purchase_no, supplier_code, sku, quantity, amount, unit_price, vatable, vat_amount, remaining, discrepancy, received_by, status, date_received, delivery_receipt_no, sales_invoice_no,  batch_no, item_record_no, expiration_date, expenses, doc_date, doc_time) VALUES ('$purchaseNo', '$supplier', '$sku', '$receivedQuantity', '$amount', '$unitPrice', '$vatable', '$vatAmount', '$remaining', '$discrepancy', '$receivedBy', '$status', '$dateReceived', '$deliveryNo', '$salesInvoice', '$batchNo', '$itemRecordNo', '$expiration', '$expense', '$docDate', '$docTime')") or die(mysql_error());
    }
    
    function updateOrderStatus($purchaseNo, $status, $remainingBalance, $dateReceived, $deliveryStatus){
        mysql_query("UPDATE item_orders SET status = '$status', remaining_balance = '$remainingBalance', date_received = '$dateReceived', delivery_status = '$deliveryStatus' WHERE purchase_no = '$purchaseNo'") or die(mysql_error());
    }
    
    function getPOReceivedQuantity($purchaseNo){
        $query = mysql_query("SELECT SUM(quantity) AS total_received_quantity FROM item_in WHERE purchase_no = '$purchaseNo'") or die(mysql_error());
        $po = mysql_fetch_assoc($query);
        return $po["total_received_quantity"];
    }
    
    function updateItemIn($entryId, $purchaseNo, $sku, $deliveryNo, $waiting, $receivedQuantity){
         mysql_query("UPDATE item_in SET delivery_no = '$deliveryNo', waiting = '$waiting', delivery_status = '$receivedQuantity' WHERE id = '$entryId' AND purchase_no = '$purchaseNo' AND sku = '$sku'") or die(mysql_error());
    }
    
    function getReorderLevelBySku($sku){
        $query = mysql_query("SELECT reorder_level FROM inventory_count WHERE sku = '$sku'") or die(mysql_error());
        $i = mysql_fetch_assoc($query);
        return $i["reorder_level"];
    }
    
    function updateInventoryUponDelivery($sku, $receivedQuantity, $lastUpdated){
        mysql_query("UPDATE inventory_count SET stock_count = '$receivedQuantity', last_updated = '$lastUpdated' WHERE sku = '$sku'") or die(mysql_error());
    }
    
    function getTransactionPayment($transNo){
        $query = mysql_query("SELECT total_tendered FROM transaction_payment WHERE transaction_no = '$transNo'") or die(mysql_error());
        $t = mysql_fetch_assoc($query);
        return $t["total_tendered"];
    }
    
    function applyTransactionHeaderDiscount($entry, $discountedAmount, $discountCode, $discountAmount){
        mysql_query("UPDATE transaction_header SET total_amount = '$discountedAmount', discount_code = '$discountCode', discount_amount = '$discountAmount' WHERE id = '$entry'") or die(mysql_error());
    }
    
    function removeTransactionHeaderDiscount($transNo, $entry, $unDiscountedAmount, $discountCode, $discountAmount){
        mysql_query("UPDATE transaction_header SET total_amount = '$unDiscountedAmount', discount_code = '$discountCode', discount_amount = '$discountAmount' WHERE transaction_no = '$transNo' AND id = '$entry'") or die(mysql_error());
    }
    
    function getTransactionHeaders($transNo){
        $query = mysql_query("SELECT * FROM transaction_header WHERE transaction_no = '$transNo'") or die(mysql_error());
        return $query;
    }
    
    function getInventoryCount(){
        $query =  mysql_query("SELECT ic.*, i.description_1 FROM inventory_count ic INNER JOIN items i ON ic.sku = i.sku") or die(mysql_error());
        return $query;
    }
    
    function setCompany($code, $company, $address1, $address2, $accrediatation, $permitNo, $tin, $serialNo){
//        mysql_query("INSERT INTO company (code, company_name, address_1, address_2, accreditation, permit_no, tin, serial_no) VALUES ('$code', '$company', '$address1', '$address2', '$accrediatation', '$permitNo', '$tin', '$serialNo')") or die(mysql_error());
        mysql_query("UPDATE company SET company_name = '$company', address_1 = '$address1', address_2 = '$address2', accreditation = '$accrediatation', permit_no = '$permitNo', tin = '$tin', serial_no = '$serialNo'") or die(mysql_error());
    }
    
    function getCompanyDetails(){
        $query = mysql_query("SELECT * FROM company") or die(mysql_error());
        return $query;
    }
    
    function addStore($code, $branch, $address1, $address2){
        mysql_query("INSERT INTO stores (code, branch, address_1, address_2) VALUES ('$code', '$branch', '$address1', '$address2')") or die(mysql_error());
    }
    
    function getStores(){
        $query = mysql_query("SELECT * FROM stores") or die(mysql_error());
        return $query;
    }
    
    function addStoreRegister($code, $storeCode, $registerDescription){
        mysql_query("INSERT INTO registers (code, store_code, description) VALUES ('$code', '$storeCode', '$registerDescription')") or die(mysql_error());
    }
    
    function getRegistersByStore($code){
        $query = mysql_query("SELECT code, description FROM registers WHERE store_code =  '$code'") or die(mysql_error());
        return $query;
    }
    
    function suggestUser($term){
        $query = mysql_query("SELECT user_id, username FROM user_accounts WHERE username LIKE '%$term%'") or die(mysql_error());
        return $query;
    }
    
    function getUserIdByUsername($username){
        $query = mysql_query("SELECT user_id FROM user_accounts WHERE username = '$username'") or die(mysql_error());
        $u = mysql_fetch_assoc($query);
        return $u["user_id"];
    }
    
    function assignUserToRegister($store, $register, $user, $lastUpdated){
        $query = mysql_query("SELECT user_code FROM register_assignments WHERE user_code = '$user'") or die(mysql_error());
        if(mysql_num_rows($query) != 0){
            mysql_query("UPDATE register_assignments SET active = '0' WHERE user_code = '$user'") or die(mysql_error());
        }
        mysql_query("INSERT INTO register_assignments (store_code, register_code, user_code, last_updated) VALUES ('$store', '$register', '$user', '$lastUpdated')") or die(mysql_error());
    }
    
    function getAssignedStore($user){
        $query = mysql_query("SELECT store_code FROM register_assignments WHERE user_code = '$user' AND active = '1'") or die(mysql_error());
        $s = mysql_fetch_assoc($query);
        return $s["store_code"];
    }
    
    function getAssignedRegister($user){
        $query = mysql_query("SELECT register_code FROM register_assignments WHERE user_code = '$user' AND active = '1'") or die(mysql_error());
        $s = mysql_fetch_assoc($query);
        return $s["register_code"];
    }
    
    function storeDesignations(){
        $query = mysql_query("SELECT * FROM register_assignments ORDER BY store_code") or die(mysql_error());
        return $query;
    }
    
    function getStoreName($store){
        $query = mysql_query("SELECT branch FROM stores WHERE code = '$store'") or die(mysql_error());
        $s = mysql_fetch_assoc($query);
        return $s["branch"];
    }
    
    function getRegisterDescription($store, $register){
        $query = mysql_query("SELECT description FROM registers WHERE store_code = '$store' AND code = '$register'") or die(mysql_error());
        $r = mysql_fetch_assoc($query);
        return $r["description"];
    }
    
    function assignReturnTransactionNo($origin){
        $query = mysql_query("SELECT transaction_no FROM returns WHERE transaction_origin = '$origin'") or die(mysql_error());
        $t = mysql_fetch_assoc($query);
        $transNo = null;
        if(mysql_num_rows($query) >= 1){
            $transNo = $t["transaction_no"];
        }
        else{
            if(isset($_SESSION["transNo"])){
                $transNo = $_SESSION["transNo"];
            }
            else{
                $transNo = newTransactionNo();
            }
        }
        return $transNo;
    }
    
    function getTransactionHeaderById($id){
        $query = mysql_query("SELECT * FROM transaction_header WHERE id = '$id'") or die(mysql_error());
        return $query;
    }
    
    function updateReturnedItem($id, $status){
        mysql_query("UPDATE transaction_header SET status = '$status' WHERE id = '$id'") or die(mysql_error());
    }
    
    function fileReturnTransactionNo($originTransNo, $returnTransNo){
        $query = mysql_query("SELECT transaction_no FROM returns WHERE transaction_no = '$returnTransNo'") or die(mysql_error());
        if(mysql_num_rows($query) < 1){
            mysql_query("INSERT INTO returns (transaction_origin, transaction_no) VALUES ('$originTransNo', '$returnTransNo')") or die(mysql_error());
            mysql_query("INSERT INTO transactions (transaction_no) VALUES ('$returnTransNo')") or die(mysql_error());
        }
    }
    
    function returnTransaction($transNo){
         mysql_query("UPDATE transaction_header SET remarks = 'Completed (Return)' WHERE transaction_no = '$transNo'") or die(mysql_error());
    }
    
    function viewUnreturnedTransactionItems($transNo){
        $query = mysql_query("SELECT * FROM transaction_header WHERE transaction_no = '$transNo' AND remarks != 'Returned' AND remarks != 'OnHold' AND remarks != 'Cancelled'") or die(mysql_error());
        return $query;
    }
    
    function updateReturnedHeader($item){
        mysql_query("UPDATE transaction_header SET remarks = 'Returned' WHERE id = '$item'") or die(mysql_error());
    }
    
    function checkReturnSalesEntry($transNo){
        $query = mysql_query("SELECT transaction_no FROM sales_entry WHERE transaction_no = '$transNo'") or die(mysql_error());
        return mysql_num_rows($query);
    }
    
    function processReturnTransaction($transNo, $storeCode, $registerCode, $userId, $subTotal, $taxAmount, $discountAmount, $totalAmount, $systemDate, $systemTime, $status){
         mysql_query("INSERT INTO sales_entry (transaction_no, store_code, register_code, user_id, sub_total, tax_amount, discount_amount, total_amount, system_date, system_time, status) VALUES ('$transNo', '$storeCode', '$registerCode', '$userId', '$subTotal', '$taxAmount', '$discountAmount', '$totalAmount', '$systemDate', '$systemTime', '$status')") or die(mysql_error());
    }
    
    function updateReturnTransaction($transNo, $subTotal, $taxAmount, $discountAmount, $totalAmount, $systemDate, $systemTime){
        mysql_query("UPDATE sales_entry SET sub_total = '$subTotal', tax_amount = '$taxAmount', discount_amount = '$discountAmount', total_amount = '$totalAmount', system_date = '$systemDate', system_time = '$systemTime' WHERE transaction_no = '$transNo'") or die(mysql_error());
    }
    
    function checkReturnPayment($transNo){
        $query = mysql_query("SELECT transaction_no FROM transaction_payment WHERE transaction_no = '$transNo'") or die(mysql_error());
        return mysql_num_rows($query);
    }
    
    function updateReturnPayment($transNo, $subTotal, $discountAmount, $totalAmount, $totalTendered, $balance, $systemDate, $systemTime){
        mysql_query("UPDATE transaction_payment SET sub_total = '$subTotal', discount_amount = '$discountAmount', total_amount = '$totalAmount', total_tendered = '$totalTendered', balance = '$balance', system_date = '$systemDate', system_time = '$systemTime' WHERE transaction_no = '$transNo'") or die(mysql_error());
    }
    
    function suggestItem($supplier, $term){
        $query = mysql_query("SELECT DISTINCT i.description_1 FROM items i INNER JOIN supplier_items s ON i.sku = s.sku WHERE s.supplier_code = '$supplier' AND i.description_1 LIKE '%$term%'") or die(mysql_error());
        return $query;
    }
    
    function suggestItemName($term){
        $query = mysql_query("SELECT description_1 FROM items WHERE description_1 LIKE '%$term%'") or die(mysql_error());
        return $query;
    }
    
    /* IMPORT */
    
    function setManufacturer($code, $manufacturerName){
        $query = mysql_query("SELECT code FROM manufacturers WHERE manufacturer_name = '$manufacturerName'") or die(mysql_error());
        $m = mysql_fetch_assoc($query);
        if(mysql_num_rows($query) < 1){
            mysql_query("INSERT INTO manufacturers (code, manufacturer_name) VALUES ('$code', '$manufacturerName')") or die(mysql_error());
        }
        else{
            $code = $m["code"];
        }
        return $code;
    }
    
    function setBrand($code, $manufacturer, $brandName){
        $query = mysql_query("SELECT code FROM brands WHERE manufacturer_code = '$manufacturer' AND brand_name = '$brandName'") or die(mysql_error());
        $b = mysql_fetch_assoc($query);
        if(mysql_num_rows($query) < 1){
            mysql_query("INSERT INTO brands (code, manufacturer_code, brand_name) VALUES ('$code', '$manufacturer', '$brandName')") or die(mysql_error());
        }
        else{
            $code = $b["code"];
        }
        return $code;
    }
    
    function setItemType($code, $brand, $type){
        $query = mysql_query("SELECT code FROM types WHERE brand_code = '$brand' AND type_name = '$type'") or die(mysql_error());
        $t = mysql_fetch_assoc($query);
        if(mysql_num_rows($query) < 1){
            mysql_query("INSERT INTO types (code, brand_code, type_name) VALUES ('$code', '$brand', '$type')") or die(mysql_error());
        }
        else{
            $code = $t["code"];
        }
        return $code;
    }
    
    function setManufacturerBrandCode($code, $manufacturer, $brand){
        $query = mysql_query("SELECT code FROM manufacturer_brand WHERE manufacturer_code = '$manufacturer' AND brand_code = '$brand'") or die(mysql_error());
        $mb = mysql_fetch_assoc($query);
        if(mysql_num_rows($query) < 1){
            mysql_query("INSERT INTO manufacturer_brand (code, manufacturer_code, brand_code) VALUES ('$code', '$manufacturer', '$brand')") or die(mysql_error());
        }
        else{
            $code = $mb["code"];
        }
        return $code;
    }
    
    function setMeasurementCode($code, $manufacturer, $brand, $type, $measurement){
        $query = mysql_query("SELECT code FROM measurement_code WHERE manufacturer = '$manufacturer' AND brand = '$brand' AND type = '$type' AND measurement_name = '$measurement'") or die(mysql_error());
        $m = mysql_fetch_assoc($query);
        if(mysql_num_rows($query) < 1){
            mysql_query("INSERT INTO measurement_code (code, manufacturer, brand, type, measurement_name) VALUES ('$code', '$manufacturer', '$brand', '$type', '$measurement')") or die(mysql_error());
        }
        else{
            $code = $m["code"];
        }
        return $code;
    }
    
    function setPackaging($code, $packaging, $quantity, $description){
        $query = mysql_query("SELECT code FROM packagings WHERE packaging = '$packaging' AND quantity = '$quantity'") or die(mysql_error());
        $p = mysql_fetch_assoc($query);
        if(mysql_num_rows($query) < 1){
            mysql_query("INSERT INTO packagings (code, packaging, quantity, description) VALUES ('$code', '$packaging', '$quantity', '$description')") or die(mysql_error());
        }
        else{
            $code = $p["code"];
        }
        return $code;
    }
    
    function setDepartment($code, $department){
        $query = mysql_query("SELECT code FROM departments WHERE department_name = '$department'") or die(mysql_error());
        $d = mysql_fetch_assoc($query);
        if(mysql_num_rows($query) < 1){
            mysql_query("INSERT INTO departments (code, department_name) VALUES ('$code', '$department')") or die(mysql_error());
        }
        else{
            $code = $d["code"];
        }
        return $code;
    }
    
    function setCategory($code, $departmentCode, $categoryName){
        $query = mysql_query("SELECT code FROM categories WHERE department_code = '$departmentCode' AND category_name = '$categoryName'") or die(mysql_error());
        $c = mysql_fetch_assoc($query);
        if(mysql_num_rows($query) < 1){
            mysql_query("INSERT INTO categories (code, department_code, category_name) VALUES ('$code', '$departmentCode', '$categoryName')") or die(mysql_error());
        }
        else{
            $code = $c["code"];
        } 
        return $code;
    }
    
    function setTax($code, $type, $description, $rate){
        $query = mysql_query("SELECT code FROM taxes WHERE type = '$type' AND description = '$description' AND rate = '$rate'") or die(mysql_error());
        $t = mysql_fetch_assoc($query);
        if(mysql_num_rows($query) < 1){
            mysql_query("INSERT INTO taxes (code, type, description, rate) VALUES ('$code', '$type', '$description', '$rate')") or die(mysql_error());
        }
        else{
            $code = $t["code"];
        }
        return $code;
    }
    
    function setSupplier($code, $supplierName){
        $query = mysql_query("SELECT code FROM suppliers WHERE supplier_name = '$supplierName'") or die(mysql_error());
        $s = mysql_fetch_assoc($query);
        if(mysql_num_rows($query) < 1){
            mysql_query("INSERT INTO suppliers (code, supplier_name) VALUES ('$code', '$supplierName')") or die(mysql_error());
        }
        else{
            $code = $s["code"];
        }
        return $code;
    }
    
    /* -------------------------------------------- */
    
    function getTransactionHeaderAmount($entry){
        $query = mysql_query("SELECT total_amount FROM transaction_header WHERE id = '$entry'") or die(mysql_error());
        $t = mysql_fetch_assoc($query);
        return $t["total_amount"];
    }
    
    function getTransactionHeaderSubtotal($entry){
        $query = mysql_query("SELECT subtotal FROM transaction_header WHERE id = '$entry'") or die(mysql_error());
        $t = mysql_fetch_assoc($query);
        return $t["subtotal"];
    }
    
    function getTransactionHeaderDiscount($entry){
        $query = mysql_query("SELECT discount_amount FROM transaction_header WHERE id = '$entry'") or die(mysql_error());
        $t = mysql_fetch_assoc($query);
        return $t["discount_amount"];
    }
    
    function applyItemDiscount($transNo, $entry, $discountCode, $discountAmount, $totalAmount){
        mysql_query("UPDATE transaction_header SET discount_code = '$discountCode', discount_amount = '$discountAmount', total_amount = '$totalAmount' WHERE transaction_no = '$transNo' AND id = '$entry'") or die(mysql_error());
    }
    
    function changeItemImage($sku, $filename, $mimeType, $size, $image){
        $query = mysql_query("SELECT sku FROM item_images WHERE sku = '$sku'") or die(mysql_error());
        if(mysql_num_rows($query) == 1){
            mysql_query("UPDATE item_images SET filename = '$filename', mime_type = '$mimeType', size = '$size', image = '$image' WHERE sku = '$sku'") or die(mysql_error());
        }
        else{
            mysql_query("INSERT INTO item_images (sku, filename, mime_type, size, image) VALUES ('$sku', '$filename', '$mimeType', '$size', '$image')") or die(mysql_error());
        }
    }
    
    function getItemImage($sku){
        $query = mysql_query("SELECT * FROM item_images WHERE sku = '$sku'") or die(mysql_error());
        return $query;
    }
    
    function getlastItemEntry($transNo){
        $query = mysql_query("SELECT sku FROM transaction_header WHERE transaction_no = '$transNo' ORDER BY system_time DESC LIMIT 1") or die(mysql_error());
        $t = mysql_fetch_assoc($query);
        return $t["sku"];
    }
    
    function getCustomers(){
        $query = mysql_query("SELECT * FROM customers") or die(mysql_error());
        return $query;
    }
    
    function getAvailableCustomerCode(){
        $query = mysql_query("SELECT * FROM customers ORDER BY code DESC LIMIT 1") or die(mysql_error());
        $lastCustomer = mysql_fetch_assoc($query);
         if(mysql_num_rows($query) == 0){
            $freeCode= 100000;
        }
        else{
            $freeCode = $lastCustomer["code"] + 1;
        }
        return $freeCode;
    }
    
    function newCustomer($code, $customerName, $address, $email, $contactNo){
        $query = mysql_query("SELECT code FROM customers WHERE customer_name = '$customerName'") or die(mysql_error());
        if(mysql_num_rows($query) == 0){
            mysql_query("INSERT INTO customers (code, customer_name, address, email_address, contact_no) VALUES ('$code', '$customerName', '$address', '$email', '$contactNo')") or die(mysql_error());
        }
        else{
            mysql_query("UPDATE customers SET address = '$address', email_address = '$email', contact_no = '$contactNo' WHERE customer_name = '$customerName'") or die(mysql_error());
        }
    }
    
    function getCustomerTransactionCount($code){
        $query = mysql_query("SELECT * FROM customer_transactions WHERE customer_code = '$code'") or die(mysql_error());
        return mysql_num_rows($query);
    }
    
    function getCustomerDetails($code){
        $query = mysql_query("SELECT * FROM customers WHERE code = '$code'") or die(mysql_error());
        return $query;
    }
    
    function trackCustomerTransaction($transNo, $customer){
        $query = mysql_query("SELECT * FROM customer_transactions WHERE transaction_no = '$transNo'") or die(mysql_error());
        if(mysql_num_rows($query) == 0){
            mysql_query("INSERT INTO customer_transactions (transaction_no, customer_code) VALUES ('$transNo', '$customer')") or die(mysql_error());
        }
        else{
            mysql_query("UPDATE customer_transactions SET customer_code = '$customer' WHERE transaction_no = '$transNo'") or die(mysql_error());
        }
    }
    
    function getCustomerNameByCode($code){
        $query = mysql_query("SELECT customer_name FROM customers WHERE code = '$code'") or die(mysql_error());
        $c = mysql_fetch_assoc($query);
        return $c["customer_name"];
    }
    
    function getCustomerFromTransaction($transNo){
        $query = mysql_query("SELECT customer_code FROM customer_transactions WHERE transaction_no = '$transNo'") or die(mysql_error());
        $c = mysql_fetch_assoc($query);
        return $c["customer_code"];
    }
    
    function checkTransactionCustomer($transNo){
        $query = mysql_query("SELECT * FROM customer_transactions WHERE transaction_no = '$transNo'") or die(mysql_error());
        return mysql_num_rows($query);
    }
    
     function batchLookup($sku){
        $query = mysql_query("SELECT * FROM deliveries WHERE sku = '$sku' AND remaining <> '0' ORDER BY batch_no") or die(mysql_error());
        return $query;
    }
    
    function reserveFromBatch($batchNo, $itemRecordNo, $reserve){
        mysql_query("UPDATE deliveries SET remaining = '$reserve' WHERE batch_no = '$batchNo' AND item_record_no = '$itemRecordNo'") or die(mysql_error());
    }
    
    function mapBatch($transNo, $sku, $batchNo, $itemRecordNo, $reserve){
        $query = mysql_query("SELECT * FROM batch_mapping WHERE transaction_no = '$transNo' AND sku = '$sku' AND batch_no = '$batchNo' AND item_record_no = '$itemRecordNo'") or die(mysql_error());
        if(mysql_num_rows($query) == 0){
            mysql_query("INSERT INTO batch_mapping (transaction_no, sku, batch_no, item_record_no, quantity) VALUES ('$transNo', '$sku', '$batchNo', '$itemRecordNo', '$reserve')") or die(mysql_error());
        }
        else{
            mysql_query("UPDATE batch_mapping SET quantity = '$reserve', status = 'Pending' WHERE transaction_no = '$transNo' AND sku = '$sku' AND batch_no = '$batchNo' AND item_record_no = '$itemRecordNo'") or die(mysql_error());
        }
    }
    
    function getBatchReservedQuantity($transNo, $sku, $batchNo, $itemRecordNo){
        $query = mysql_query("SELECT quantity FROM batch_mapping WHERE transaction_no = '$transNo' AND sku = '$sku' AND batch_no = '$batchNo' AND item_record_no = '$itemRecordNo'") or die(mysql_error());
        $b = mysql_fetch_assoc($query);
        return $b["quantity"];
    }
    
    function completeBatchMapping($transNo, $sku){
        mysql_query("UPDATE batch_mapping SET status = 'Completed' WHERE transaction_no = '$transNo' AND sku = '$sku' AND status = 'Pending'") or die(mysql_error());
    }
    
    function cancelBatchReserves($transNo){
        mysql_query("UPDATE batch_mapping SET status = 'Cancelled' WHERE transaction_no = '$transNo'") or die(mysql_error());
    }
    
    function removeBatchReserves($transNo, $sku, $batchNo, $itemRecordNo){
        mysql_query("UPDATE batch_mapping SET status = 'Removed' WHERE transaction_no = '$transNo' AND sku = '$sku' AND batch_no = '$batchNo' AND item_record_no = '$itemRecordNo' AND status = 'Pending'") or die(mysql_error());
    }
    
    function getBatchAssigned($transNo, $sku){
        $query = mysql_query("SELECT * FROM batch_mapping WHERE transaction_no = '$transNo' AND sku = '$sku'") or die(mysql_error());
        return $query;
    }
    
    function getDeliveryEntryRemaining($batchNo, $itemRecordNo){
        $query = mysql_query("SELECT remaining FROM deliveries WHERE batch_no = '$batchNo' AND item_record_no = '$itemRecordNo'") or die(mysql_error());
        $b = mysql_fetch_assoc($query);
        return $b["remaining"];
    }
    
    function updateDeliveryEntryRemaining($batchNo, $itemRecordNo, $quantity){
        mysql_query("UPDATE deliveries SET remaining = '$quantity' WHERE batch_no = '$batchNo' AND item_record_no = '$itemRecordNo'") or die(mysql_error());
    }
    
    function getBatchMapEntries($transNo){
        $query = mysql_query("SELECT * FROM batch_mapping WHERE transaction_no = '$transNo'") or die(mysql_error());
        return $query;
    }
    
    function rotatingBatchReserve($transNo, $sku, $quantity){
        
        $freeBatch = batchLookup($e["sku"]);
        $b = mysql_fetch_assoc($freeBatch);
        $batchNo = $b["batch_no"];
        $itemRecordNo = $b["item_record_no"];
        $remaining = $b["remaining"];
        $reserve = ($remaining + getBatchReservedQuantity($transNo, $sku, $batchNo, $itemRecordNo)) - $quantity;
        $quantity = $quantity - $reserve;
        if($quantity > $remaining){
            $reserve = 0;
        }
        reserveFromBatch($batchNo, $itemRecordNo, $reserve);
        mapBatch($transNo, $sku, $batchNo, $itemRecordNo, $quantity);
        return $quantity;
    }
    
    function getTaxRateBySku($sku){
        $query = mysql_query("SELECT tax_code FROM item_tax WHERE item_sku = '$sku'") or die(mysql_error());
        $t = mysql_fetch_assoc($query);
        return getTaxRate($t["tax_code"]);
    }
    
    function getSkuByCashierEntryId($id){
        $query = mysql_query("SELECT sku FROM transaction_header WHERE id = '$id'") or die(mysql_error());
        $t = mysql_fetch_assoc($query);
        return $t["sku"];
    }
    
    function getUnitPriceBySku($sku){
        $query = mysql_query("SELECT price FROM items WHERE sku = '$sku'") or die(mysql_error());
        $i = mysql_fetch_assoc($query);
        return $i["price"];
    }
    
    function activateUser($id){
        mysql_query("UPDATE user_accounts SET tenant_auth_active = '1' WHERE user_id = '$id'") or die(mysql_error());
    }
    
    function assignToGroup($group, $user){
        $query = mysql_query("SELECT * FROM user_grouping_table WHERE user_id = '$user'") or die(mysql_error());
        if(mysql_num_rows($query) == 0){
            mysql_query("INSERT INTO user_grouping_table (user_group_id, user_id) VALUES ('$group', '$user')") or die(mysql_error());
        }
        else{
            mysql_query("UPDATE user_grouping_table SET user_group_id = '$group' WHERE user_id = '$user'") or die(mysql_error());
        }
    }

?>