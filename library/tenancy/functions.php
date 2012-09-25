<?php

    function newTenant($code, $secret, $client, $address){
         $query = mysql_query("SELECT * FROM tenants WHERE code = '$code'");
         if(mysql_num_rows($query) == 0){
             mysql_query("INSERT INTO tenants (code, secret_key, client_name, address) VALUES ('$code', '$secret', '$client', '$address')");
         }
    }
    
    function nextTenantUserId(){
        $lastUserData = mysql_query("SELECT user_id FROM user_accounts ORDER BY user_id DESC LIMIT 1") or die(mysql_error());
        $lastUser = mysql_fetch_assoc($lastUserData);
        if(mysql_num_rows($lastUserData) == 0){
            $freeUserId = 100000;
        }
        else{
            $freeUserId = $lastUser["user_id"] + 1;
        }
        return (int) $freeUserId;
    }
    
    function createTenantDb($code, $secret, $client, $address){
        $adminId = nextTenantUserId();
        $cashierId = $adminId + 1;
        $adminUn = "admin" . substr(sha1(md5($adminId)), 0, 4);
        $cashierUn = "cashier" . substr(sha1(md5($cashierId)), 0, 4);
        $adminPw = sha1($adminUn);
        $cashierPw = sha1($cashierUn);
        mysql_query("INSERT INTO user_accounts (tenant_code, secret_key, user_id, username, password) VALUES ('$code', '$secret', '$adminId', '$adminUn', '$adminPw')");
        mysql_query("INSERT INTO user_accounts (tenant_code, secret_key, user_id, username, password) VALUES ('$code', '$secret', '$cashierId', '$cashierUn', '$cashierPw')");
        
        $tenantDb = "CREATE DATABASE " . $code;
        
        mysql_query($tenantDb) or die(" CREATING TENANT DATABASE FAILED - " . mysql_error());
        mysql_select_db($code) or die(" SELECTING TENANT DATABASE FAILED - " . mysql_error());
        
        $db = mysql_query("SELECT DATABASE()") or die(" UNABLE TO FETCH CURRRENT WORKING DB - " . mysql_error());
        if(mysql_result($db, 0) == $code){
        
            $measurements = mysql_query("CREATE TABLE measurements(id int(11) NOT NULL AUTO_INCREMENT, measurement_name varchar(256) NOT NULL,PRIMARY KEY (id))");
            $stores = mysql_query("CREATE TABLE stores ( id int(11) NOT NULL AUTO_INCREMENT, code char(4) NOT NULL, branch varchar(512) NOT NULL, address_1 text, address_2 text, date_created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id))") or die(mysql_error());
            $userGroupingTable = mysql_query("CREATE TABLE user_grouping_table (id int(11) NOT NULL AUTO_INCREMENT,user_group_id int(11) NOT NULL,user_id char(4) NOT NULL,date_assigned timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (id))");
            $deliveries = mysql_query("CREATE TABLE deliveries (id int(11) NOT NULL AUTO_INCREMENT,purchase_no int(11) NOT NULL,supplier_code char(4) NOT NULL,sku char(6) NOT NULL,quantity int(11) NOT NULL,amount decimal(19,2) NOT NULL,unit_price decimal(19,2) DEFAULT NULL,vatable decimal(19,2) DEFAULT NULL,vat_amount decimal(19,2) DEFAULT NULL,line_vat decimal(19,2) DEFAULT NULL,remaining int(11) NOT NULL,discrepancy int(11) NOT NULL,status varchar(256) NOT NULL,received_by char(4) NOT NULL,date_received datetime NOT NULL,delivery_receipt_no varchar(45) DEFAULT NULL,sales_invoice_no varchar(45) DEFAULT NULL,batch_no char(10) NOT NULL,item_record_no char(3) NOT NULL,expiration_date date NOT NULL,expenses decimal(19,2) DEFAULT NULL,doc_date date NOT NULL,doc_time time NOT NULL,PRIMARY KEY (id))");
            $transactionHeader = mysql_query("CREATE TABLE transaction_header (  id int(11) NOT NULL AUTO_INCREMENT,  transaction_no char(12) NOT NULL,  store_code char(4) DEFAULT NULL,  register_code char(4) DEFAULT NULL,  user_id char(4) NOT NULL,  sku char(12) NOT NULL,  description text NOT NULL,  quantity int(11) NOT NULL,  price decimal(19,2) NOT NULL,  subtotal decimal(19,2) NOT NULL,  total_amount decimal(19,2) NOT NULL,  discount_code char(4) DEFAULT NULL,  discount_amount decimal(19,2) DEFAULT NULL,  tax_code char(4) DEFAULT NULL,  tax_amount decimal(19,2) DEFAULT NULL,  system_date date NOT NULL,  system_time time NOT NULL,  remarks varchar(45) NOT NULL DEFAULT 'Pending',  PRIMARY KEY (id))");
//            $stores = mysql_query("CREATE TABLE stores ( id int(11) NOT NULL AUTO_INCREMENT, code char(4) NOT NULL, branch varchar(1024) NOT NULL, address_1 text, address_2 text, date_created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id))");
            $registers = mysql_query("CREATE TABLE registers (  id int(11) NOT NULL AUTO_INCREMENT,  code char(4) NOT NULL,  store_code char(4) NOT NULL,  description varchar(1024) NOT NULL,  date_created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  PRIMARY KEY (id))");
            $departments = mysql_query("CREATE TABLE departments (  id int(11) NOT NULL AUTO_INCREMENT,  code int(4) unsigned zerofill NOT NULL,  department_name varchar(128) NOT NULL,  PRIMARY KEY (id))");
            $inventoryCount = mysql_query("CREATE TABLE inventory_count (  id int(11) NOT NULL AUTO_INCREMENT,  sku char(6) NOT NULL,  stock_count int(11) NOT NULL,  reorder_min_count int(11) NOT NULL,  reorder_level int(11) NOT NULL,  last_updated datetime DEFAULT NULL,  PRIMARY KEY (id))");        
            $registryAssignments = mysql_query("CREATE TABLE register_assignments (  id int(11) NOT NULL AUTO_INCREMENT,  store_code char(4) NOT NULL,  register_code char(4) NOT NULL,  user_code char(4) NOT NULL,  last_updated datetime NOT NULL,  date_created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  active int(11) NOT NULL DEFAULT '1',  PRIMARY KEY (id))");
            $sku = mysql_query("CREATE TABLE sku (  id int(11) NOT NULL AUTO_INCREMENT,  sku char(6) NOT NULL,  date_created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  active int(1) NOT NULL DEFAULT '1',  PRIMARY KEY (id))");
            $supplierItems = mysql_query("CREATE TABLE supplier_items (  id int(11) NOT NULL AUTO_INCREMENT,  supplier_code char(4) NOT NULL,  sku char(6) NOT NULL,  PRIMARY KEY (id))");
            $customerTransactions = mysql_query("CREATE TABLE customer_transactions (  id int(11) NOT NULL AUTO_INCREMENT,  transaction_no char(12) NOT NULL,  customer_code char(6) NOT NULL,  PRIMARY KEY (id))");
            $transactionPayment = mysql_query("CREATE TABLE transaction_payment (  id int(11) NOT NULL AUTO_INCREMENT,  transaction_no char(12) NOT NULL,  store_code char(4) DEFAULT NULL,  register_code char(4) DEFAULT NULL,  user_id int(4) unsigned zerofill NOT NULL,  subtotal decimal(19,2) NOT NULL,  tax_code int(4) unsigned zerofill DEFAULT NULL,  disc_amount decimal(19,2) DEFAULT NULL,  total_amount decimal(19,2) NOT NULL,  total_tendered decimal(19,2) NOT NULL,  balance decimal(19,2) NOT NULL,  receipt_no char(12) DEFAULT NULL,  system_date date NOT NULL,  system_time time NOT NULL,  PRIMARY KEY (id))");
            $transactionCancel = mysql_query("CREATE TABLE transaction_cancel (  id int(11) NOT NULL AUTO_INCREMENT,  transaction_no char(12) NOT NULL,  store_code char(4) DEFAULT NULL,  register_code char(4) DEFAULT NULL,  user_id char(4) NOT NULL,  sub_total decimal(19,2) NOT NULL,  tax_amount decimal(19,2) DEFAULT NULL,  discount_amount decimal(19,2) NOT NULL,  total_amount decimal(19,2) NOT NULL,  system_date date NOT NULL,  system_time time NOT NULL,  PRIMARY KEY (id))");
            $userGroups = mysql_query("CREATE TABLE user_groups (  id int(11) NOT NULL AUTO_INCREMENT,  group_name varchar(45) NOT NULL,  active int(1) NOT NULL DEFAULT '1',  PRIMARY KEY (id))");
            $discounts = mysql_query("CREATE TABLE discounts (  id int(11) NOT NULL AUTO_INCREMENT,  code int(4) NOT NULL,  type varchar(256) NOT NULL,  description text,  rate decimal(6,2) NOT NULL,  active int(1) NOT NULL DEFAULT '1',  PRIMARY KEY (id))");
            $returns = mysql_query("CREATE TABLE returns (  id int(11) NOT NULL AUTO_INCREMENT,  transaction_origin char(12) NOT NULL,  transaction_no char(12) NOT NULL,  date_created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  PRIMARY KEY (id))");
            $categories = mysql_query("CREATE TABLE categories (  id int(11) NOT NULL AUTO_INCREMENT,  code int(4) unsigned zerofill NOT NULL,  department_code int(4) unsigned zerofill NOT NULL,  category_name varchar(256) NOT NULL,  PRIMARY KEY (id))");
            $salesEntry = mysql_query("CREATE TABLE sales_entry (  id int(11) NOT NULL AUTO_INCREMENT,  transaction_no char(12) NOT NULL,  store_code char(4) DEFAULT NULL,  register_code char(4) DEFAULT NULL,  user_id char(4) NOT NULL,  sub_total decimal(19,2) NOT NULL,  tax_amount decimal(19,2) DEFAULT NULL,  discount_amount decimal(19,2) NOT NULL,  total_amount decimal(19,2) NOT NULL,  system_date date NOT NULL,  system_time time NOT NULL,  status varchar(45) NOT NULL,  PRIMARY KEY (id))");
            $batchMapping = mysql_query("CREATE TABLE batch_mapping (  id int(11) NOT NULL AUTO_INCREMENT,  transaction_no char(12) NOT NULL,  sku char(6) NOT NULL,  batch_no char(10) NOT NULL,  item_record_no char(3) NOT NULL,  quantity int(11) NOT NULL,  status varchar(45) NOT NULL DEFAULT 'Pending',  date_created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  PRIMARY KEY (id))");
            $customers = mysql_query("CREATE TABLE customers (  id int(11) NOT NULL AUTO_INCREMENT,  code char(6) NOT NULL,  customer_name varchar(512) NOT NULL,  address text,  email_address varchar(512) CHARACTER SET big5 COLLATE big5_bin DEFAULT NULL,  contact_no varchar(45) DEFAULT NULL,  active int(11) NOT NULL DEFAULT '1',  date_created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  PRIMARY KEY (id))");
            $manufacturers = mysql_query("CREATE TABLE manufacturers (  id int(11) NOT NULL AUTO_INCREMENT,  code int(4) unsigned zerofill NOT NULL,  manufacturer_name varchar(256) NOT NULL,  PRIMARY KEY (id))");
            $company = mysql_query("CREATE TABLE company (  id int(11) NOT NULL AUTO_INCREMENT,  code char(4) NOT NULL,  company_name varchar(2048) NOT NULL,  address_1 text NOT NULL,  address_2 text NOT NULL,  accreditation varchar(512) NOT NULL,  permit_no varchar(512) NOT NULL,  tin varchar(512) NOT NULL,  serial_no varchar(512) NOT NULL,  date_created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  PRIMARY KEY (id))");
            $itemOrders = mysql_query("CREATE TABLE item_orders (  id_io int(11) NOT NULL AUTO_INCREMENT,  purchase_no int(11) NOT NULL,  type varchar(45) DEFAULT NULL,  supplier_code char(4) NOT NULL,  attention_to varchar(512) NOT NULL,  prepared_by char(4) NOT NULL,  status varchar(45) DEFAULT NULL,  total_amount decimal(19,2) NOT NULL,  remaining_balance decimal(19,2) DEFAULT NULL,  date_received datetime DEFAULT NULL,  delivery_status int(11) DEFAULT NULL,  system_date date NOT NULL,  system_time time NOT NULL,  PRIMARY KEY (id_io))");
            $itemIn = mysql_query("CREATE TABLE item_in (  id int(11) NOT NULL AUTO_INCREMENT,  purchase_no int(11) NOT NULL,  sku char(6) NOT NULL,  quantity int(11) NOT NULL,  unit_price decimal(19,2) NOT NULL,  total_amount decimal(19,2) NOT NULL,  delivery_no int(11) DEFAULT NULL,  waiting int(11) NOT NULL,  delivery_status int(11) DEFAULT NULL,  system_date date NOT NULL,  system_time time NOT NULL,  PRIMARY KEY (id))");
            $itemOut = mysql_query("CREATE TABLE item_out (  id int(11) NOT NULL AUTO_INCREMENT,  transaction_no char(12) NOT NULL,  item_code char(12) NOT NULL,  quantity int(11) NOT NULL,  unit_price decimal(19,2) NOT NULL,  total_amount decimal(19,2) NOT NULL,  system_date date NOT NULL,  system_time time NOT NULL,  PRIMARY KEY (id))");
            $itemImages = mysql_query("CREATE TABLE item_images (  id int(11) NOT NULL AUTO_INCREMENT,  sku char(6) NOT NULL,  filename varchar(1024) NOT NULL,  mime_type varchar(128) NOT NULL,  size int(11) DEFAULT NULL,  image longblob NOT NULL,  date_created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  PRIMARY KEY (id))");
            $brands = mysql_query("CREATE TABLE brands (  id int(11) NOT NULL AUTO_INCREMENT,  code int(4) unsigned zerofill NOT NULL,  manufacturer_code int(4) unsigned zerofill NOT NULL,  brand_name varchar(256) NOT NULL,  PRIMARY KEY (id))");
            $types = mysql_query("CREATE TABLE types (  id int(11) NOT NULL AUTO_INCREMENT,  code int(4) NOT NULL,  brand_code int(4) NOT NULL,  type_name varchar(512) NOT NULL,  date_created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  active int(1) NOT NULL DEFAULT '1',  PRIMARY KEY (id))");
            $userAccounts = mysql_query("CREATE TABLE user_accounts (  id int(11) NOT NULL AUTO_INCREMENT,  tenant_code char(40) NOT NULL, tenant_secret char(8) NOT NULL, tenant_auth_active int(1) NOT NULL DEFAULT '0',  user_id int(4) unsigned zerofill NOT NULL,  username varchar(45) NOT NULL,  password char(40) NOT NULL,  firstname varchar(128) NOT NULL,  lastname varchar(128) NOT NULL,  email_address varchar(128) NOT NULL,  contact_no varchar(64) DEFAULT NULL,  address_1 varchar(512) DEFAULT NULL,  address_2 varchar(512) DEFAULT NULL,  city varchar(128) DEFAULT NULL,  province varchar(128) DEFAULT NULL,  country varchar(128) DEFAULT NULL,  postal_code varchar(10) DEFAULT NULL,  date_created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  created_by varchar(45) DEFAULT NULL,  last_updated datetime DEFAULT NULL,  updated_by varchar(45) DEFAULT NULL,  active int(1) NOT NULL DEFAULT '1',  PRIMARY KEY (id))");
            $manufacturerBrand = mysql_query("CREATE TABLE manufacturer_brand (  id int(11) NOT NULL AUTO_INCREMENT,  code char(4) NOT NULL,  manufacturer_code char(4) NOT NULL,  brand_code char(4) NOT NULL,  PRIMARY KEY (id))");
            $measurementCode = mysql_query("CREATE TABLE measurement_code (  id int(11) NOT NULL AUTO_INCREMENT,  code char(4) NOT NULL,  manufacturer char(4) NOT NULL,  brand char(4) NOT NULL,  type char(4) NOT NULL,  measurement_name varchar(512) NOT NULL,  PRIMARY KEY (id))");
            $items = mysql_query("CREATE TABLE items (  id int(11) NOT NULL AUTO_INCREMENT,  item_code char(12) NOT NULL,  sku char(12) NOT NULL,  packaging_code char(4) NOT NULL,  department_code int(4) NOT NULL,  category_code int(4) NOT NULL,  description_1 text NOT NULL,  description_2 text NOT NULL,  generic_name varchar(512) DEFAULT NULL,  price decimal(19,2) NOT NULL,  date_created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  active int(1) NOT NULL DEFAULT '1',  PRIMARY KEY (id))");
            $suppliers = mysql_query("CREATE TABLE suppliers (  id int(11) NOT NULL AUTO_INCREMENT,  code int(4) unsigned zerofill NOT NULL,  supplier_name varchar(256) NOT NULL,  contact_no varchar(512) DEFAULT NULL,  address_1 varchar(512) DEFAULT NULL,  address_2 varchar(512) DEFAULT NULL,  accreditation text,  tin_no varchar(45) DEFAULT NULL,  terms varchar(512) DEFAULT NULL,  contract varchar(512) DEFAULT NULL,  date_created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  active int(1) NOT NULL DEFAULT '1',  PRIMARY KEY (id))");
            $taxes = mysql_query("CREATE TABLE taxes (  id int(11) NOT NULL AUTO_INCREMENT,  code int(4) NOT NULL,  type varchar(256) NOT NULL,  description text,  rate decimal(6,2) NOT NULL,  date_created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  active int(1) NOT NULL DEFAULT '1',  PRIMARY KEY (id))");
            $packagings = mysql_query("CREATE TABLE packagings (  id int(11) NOT NULL AUTO_INCREMENT,  code char(4) NOT NULL,  packaging varchar(256) NOT NULL,  quantity int(11) NOT NULL,  description varchar(512) NOT NULL,  PRIMARY KEY (id))");
            $transactions = mysql_query("CREATE TABLE transactions (  id int(11) NOT NULL AUTO_INCREMENT,  transaction_no char(12) NOT NULL,  transaction_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  PRIMARY KEY (id))");
            $itemTax = mysql_query("CREATE TABLE item_tax (  id int(11) NOT NULL AUTO_INCREMENT,  item_sku char(12) NOT NULL,  tax_code char(4) NOT NULL,  active int(1) NOT NULL DEFAULT '1',  PRIMARY KEY (id))");
            mysql_query("INSERT INTO user_groups (group_name) VALUES ('Administrator')");
            mysql_query("INSERT INTO user_groups (group_name) VALUES ('Cashier')");
            mysql_query("INSERT INTO user_accounts (tenant_code, tenant_secret, tenant_auth_active, user_id, username, password) VALUES ('$code', '$secret', '1', '1000', '$adminUn', '$adminPw')");
            mysql_query("INSERT INTO user_accounts (tenant_code, tenant_secret, tenant_auth_active, user_id, username, password) VALUES ('$code', '$secret', '1', '1001', '$cashierUn', '$cashierPw')");
            mysql_query("INSERT INTO user_grouping_table (user_group_id, user_id) VALUES (1,'1000')");
            mysql_query("INSERT INTO user_grouping_table (user_group_id, user_id) VALUES (2,'1001')");
            mysql_query("INSERT INTO company (code, company_name, address_1, address_2, accreditation, permit_no, tin, serial_no) VALUES ('1000', '$client', '$address', '', '', '', '', '')") or die(mysql_error());
        }
        mysql_select_db("my_pharma_pos_master");
    }
    
    function newInternalAccount($tenant, $secret, $id, $username, $password){
        $query = mysql_query("SELECT * FROM user_accounts WHERE tenant_code = '$tenant' AND username = '$username'") or die(mysql_error());
        if(mysql_num_rows($query) == 0){
            mysql_query("INSERT INTO user_accounts (tenant_code, secret_key, user_id, username, password) VALUES ('$tenant', '$secret', '$id', '$username', '$password')");
        }
    }
    
    function loginTenantAccount($username, $password){
        $query = mysql_query("SELECT * FROM user_accounts WHERE username = '$username' AND password = '$password'") or die(mysql_error());
        return $query;
    }


?>
