<?php
    include("../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);  
    
    if(is_uploaded_file($_FILES['itemCsv']['tmp_name'])){
        $handle = fopen($_FILES['itemCsv']['tmp_name'], "r");
        $data = fgetcsv($handle, 100000, ",");
        while (($data = fgetcsv($handle, 100000, ",")) !== FALSE) {
            
            /* MANUFACTURERS */
            $manufacturerTable = "manufacturers";
            $manufacturerColumn = "code";
            $manufacturerCode = getAvailableId($manufacturerTable, $manufacturerColumn);
            $manufacturer = mres($data[0]);
            $manufacturerCode = setManufacturer($manufacturerCode, $manufacturer);
            /* ---------------------------------------------- */
            
            /* BRANDS */
            $brandTable = "brands";
            $brandColumn = "code";
            $brandCode = getAvailableId($brandTable, $brandColumn);
            $brand = mres($data[1]);
            $brandCode = setBrand($brandCode, $manufacturerCode, $brand);
            
            /* ---------------------------------------------- */
            
            /* TYPES */
            $typeTable = "types";
            $typeColumn = "code";
            $typeCode = getAvailableId($typeTable, $typeColumn);
            $type = mres($data[2]);
            $typeCode = setItemType($typeCode, $brandCode, $type);
            /* ---------------------------------------------- */
            
            /* MEASUREMENT */
            $measurement = mres($data[4]);
            addMeasurement($measurement);
            $measurementName = mres($data[3]) . $measurement;
            /* ---------------------------------------------- */
            
            /* SET MANUFACTURER BRAND CODE */
            $manufacturerBrandTable = "manufacturer_brand";
            $manufacturerBrandColumn = "code";
            $manufacturerBrandCode = getAvailableId($manufacturerBrandTable, $manufacturerBrandColumn);
            $manufacturerBrandCode = setManufacturerBrandCode($manufacturerBrandCode, $manufacturerCode, $brandCode);
            /* ---------------------------------------------- */
            
            /* SET MEASUREMENT CODE */
            $measurementCode = getAvailableMeasurementCode($manufacturerCode, $brandCode, $typeCode, $measurementName);
            $measurementCode = setMeasurementCode($measurementCode, $manufacturerCode, $brandCode, $typeCode, $measurementName);
            /* ---------------------------------------------- */
            
            /* ITEM CODE */
            $itemCode = $manufacturerBrandCode . $typeCode . $measurementCode;
            /* ---------------------------------------------- */
            
            /* PACKAGINGS */
            $packagingTable = "packagings";
            $packagingColumn = "code";
            $packagingCode = getAvailableId($packagingTable, $packagingColumn);
            $packaging = mres($data[5]);
            $packagingQuantity = mres($data[6]);
            $packagingDescription = $packaging . " of " . $packagingQuantity;
            $packagingCode = setPackaging($packagingCode, $packaging, $packagingQuantity, $packagingDescription);
            /* ---------------------------------------------- */
            
            /* DEPARTMENTS */
            $departmentTable = "departments";
            $departmentColumn = "code";
            $departmentCode = getAvailableId($departmentTable, $departmentColumn);
            $department = mres($data[7]);
            $departmentCode = setDepartment($departmentCode, $department);
            /* ---------------------------------------------- */
            
            /* CATEGORIES */
            $categoryTable = "categories";
            $categoryColumn = "code";
            $categoryCode = getAvailableId($categoryTable, $categoryColumn);
            $category = mres($data[8]);
            $categoryCode = setCategory($categoryCode, $departmentCode, $category);
            /* ---------------------------------------------- */
            
            /* DESCRIPTIONS */
            $description1 = $brand . " " . $type . " " . $measurementName ." (" . $packagingDescription . ")";
            $rawDescription2 = $data[9];
            $blackSpaced = str_replace(" ", "", $rawDescription2);
            $firstChar = substr($blackSpaced, 0, 1);
            $nextChars = substr($blackSpaced, 1, 19);
            $vowels = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U");
            $trimmed = str_replace($vowels, "", $nextChars);
//            $description2 = strtoupper($firstChar . preg_replace('/[^a-zA-Z0-9\']/', '', $trimmed));
            $description2 = $rawDescription2;
            /* ---------------------------------------------- */
            
            /* GENERIC NAMES */
            $genericName = mres($data[10]);
            /* ---------------------------------------------- */
            
            /* PRICES */
            $price = mres($data[11]);
            /* ---------------------------------------------- */
            
            $checkItemExistence = checkItemPackDescExistence($packagingCode, $description1);
            if($checkItemExistence < 1){
                /* SKU */
                $sku = getAvailableSku();
                newSku($sku);
                /* ---------------------------------------------- */

                /* TAXES */
                $taxTable = "taxes";
                $taxColumn = "code";
                $taxCode = getAvailableId($taxTable, $taxColumn);
                $tax = mres($data[12]);
                $taxDescription = $tax . " Tax";
                $taxRate = preg_replace("/[^0-9]/", '', $tax);
                $taxCode = setTax($taxCode, $tax, $taxDescription, $taxRate);
                applyTax($sku, $taxCode);
                /* ---------------------------------------------- */

                /* SUPPLIERS */
                $supplierTable = "suppliers";
                $supplierColumn = "code";
                $supplierCode = getAvailableId($supplierTable, $supplierColumn);
                $supplier = mres($data[13]);
                $supplierCode = setSupplier($supplierCode, $supplier);
                assignItemToSupplier($supplierCode, $sku);
                /* ---------------------------------------------- */
                
                /* UPDATE INVENTORY DETAILS */
                updateInventoryCount($sku, mres($data[14]), mres($data[15]), mres($data[16]), date("Y-m-d h:i:s"));
                /* ---------------------------------------------- */

                /* ADD THE ITEM */
                addItem($itemCode, $sku, $packagingCode, $departmentCode, $categoryCode,  $description1, $description2, $genericName, $price);
            }
            /*-----------------------------------------------*/
            
            header("Location: http://" . ROOT . "?module=manage&page=itemList");
        }
    }
    else{
        die("What are you doing here??");
    }
?>