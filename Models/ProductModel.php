<?php
namespace Models;

use Core\Model;

class ProductModel extends Model {
    
    public function getProducts($params = []) {
        $records_per_page = $params['limit'] ?? 20;
        $page = $params['page'] ?? 1;
        $offset = ($page - 1) * $records_per_page;
        $search = $params['search'] ?? '';
        $category_param = $params['category'] ?? '';

        $jewellery_search = '';
        $garments_search = '';
        
        if (!empty($search)) {
            $search = mysqli_real_escape_string($this->db, $search);
            $jewellery_search = " AND (product_name LIKE '%$search%' OR product_code LIKE '%$search%')";
            $garments_search = " AND (gproduct_name LIKE '%$search%' OR gproduct_code LIKE '%$search%')";
        }
        
        $featured = $params['featured'] ?? '';
        if ($featured !== '') {
            $featured = (int)$featured;
            $jewellery_search .= " AND featured = $featured";
            $garments_search .= " AND featured = $featured";
        }

        if (!empty($category_param)) {
            if (strpos($category_param, ':') !== false) {
                list($type, $id) = explode(':', $category_param);
                $id = (int)$id;
                
                if ($type === 'garment') {
                    $garments_search .= " AND (garment_id = $id OR product_for = $id)";
                    $jewellery_search .= " AND 1=0";
                } elseif ($type === 'jewel_parent') {
                    $jewellery_search .= " AND categories_id = $id";
                    $garments_search .= " AND 1=0";
                } elseif ($type === 'jewel_child') {
                    $jewellery_search .= " AND subcat_id = $id";
                    $garments_search .= " AND 1=0";
                }
            }
        }

        $query = "
            (SELECT 
                product_id as id,
                product_name as name,
                product_code as code,
                'jewellery' as type,
                discount,
                featured,
                categories_id as category_id,
                subcat_id as subcategory_id,
                sales_price as original_sales_price,
                rent_price as db_rent_price,
                deposit as db_deposit,
                price_source,
                availability
            FROM product 
            WHERE 1=1 $jewellery_search)
            UNION ALL
            (SELECT 
                gproduct_id as id,
                gproduct_name as name,
                gproduct_code as code,
                'garments' as type,
                discount,
                featured,
                garment_id as category_id,
                0 as subcategory_id,
                sales_price as original_sales_price,
                rent_price as db_rent_price,
                deposit as db_deposit,
                price_source,
                availability
            FROM garment_product 
            WHERE 1=1 $garments_search)
            ORDER BY id DESC 
            LIMIT $offset, $records_per_page";

        $result = $this->query($this->db, $query);
        $products = $this->fetchAll($result);
        $skipDetails = $params['skip_details'] ?? false;

        if (!$skipDetails) {
            foreach ($products as &$product) {
                $product['details'] = $this->getProductDetails($product);
            }
        }

        return $products;
    }

    public function getTotalCount($params = []) {
        $search = $params['search'] ?? '';
        $category_param = $params['category'] ?? '';

        $jewellery_search = '';
        $garments_search = '';
        
        if (!empty($search)) {
            $search = mysqli_real_escape_string($this->db, $search);
            $jewellery_search = " AND (product_name LIKE '%$search%' OR product_code LIKE '%$search%')";
            $garments_search = " AND (gproduct_name LIKE '%$search%' OR gproduct_code LIKE '%$search%')";
        }

        $featured = $params['featured'] ?? '';
        if ($featured !== '') {
            $featured = (int)$featured;
            $jewellery_search .= " AND featured = $featured";
            $garments_search .= " AND featured = $featured";
        }

        if (!empty($category_param)) {
            if (strpos($category_param, ':') !== false) {
                list($type, $id) = explode(':', $category_param);
                $id = (int)$id;
                
                if ($type === 'garment') {
                    $garments_search .= " AND (garment_id = $id OR product_for = $id)";
                    $jewellery_search .= " AND 1=0";
                } elseif ($type === 'jewel_parent') {
                    $jewellery_search .= " AND categories_id = $id";
                    $garments_search .= " AND 1=0";
                } elseif ($type === 'jewel_child') {
                    $jewellery_search .= " AND subcat_id = $id";
                    $garments_search .= " AND 1=0";
                }
            }
        }

        $total_query = "
            SELECT SUM(count) as total FROM (
                SELECT COUNT(*) as count FROM product WHERE 1=1 $jewellery_search
                UNION ALL
                SELECT COUNT(*) as count FROM garment_product WHERE 1=1 $garments_search
            ) as combined_products";

        $result = $this->query($this->db, $total_query);
        return $this->fetchOne($result)['total'] ?? 0;
    }

    public function getCategories() {
        $categories = [];

        // 1. Apparel Categories
        $apparel_qry = "SELECT garment_id, name FROM garments WHERE Main_id=1 OR Main_id=3 ORDER BY name";
        $apparel_res = $this->query($this->db, $apparel_qry);
        $apparel_data = ['children' => [], 'count' => 0];
        
        while ($row = $this->fetchOne($apparel_res)) {
            $id = $row['garment_id'];
            $name = ucwords(strtolower($row['name']));
            
            $count_qry = "SELECT COUNT(*) as cnt FROM garment_product WHERE garment_id = $id OR product_for = $id";
            $count_res = $this->query($this->db, $count_qry);
            $count_row = $this->fetchOne($count_res);
            $count = (int)$count_row['cnt'];

            if ($count > 0) {
                $apparel_data['children']["garment:$id"] = [
                    'name' => $name,
                    'count' => $count
                ];
                $apparel_data['count'] += $count;
            }
        }
        if ($apparel_data['count'] > 0) {
            $categories['Apparel'] = $apparel_data;
        }

        // 2. Jewellery Categories
        $jewel_qry = "SELECT subcat_id, categories_name FROM jewel_subcat WHERE mcat_id=1 OR mcat_id=3 ORDER BY categories_name";
        $jewel_res = $this->query($this->db, $jewel_qry);
        $jewel_data = ['children' => [], 'count' => 0];

        while ($row = $this->fetchOne($jewel_res)) {
            $parent_id = $row['subcat_id'];
            $parent_name = ucwords(strtolower($row['categories_name']));

            // Count products for this parent
            $parent_count_qry = "SELECT COUNT(*) as cnt FROM product WHERE categories_id = $parent_id";
            $parent_count_res = $this->query($this->db, $parent_count_qry);
            $parent_count_row = $this->fetchOne($parent_count_res);
            $parent_count = (int)$parent_count_row['cnt'];

            if ($parent_count > 0) {
                // Add parent option
                $jewel_data['children']["jewel_parent:$parent_id"] = [
                    'name' => $parent_name,
                    'count' => $parent_count
                ];

                // Get subcategories
                $sub_qry = "SELECT subcat_id, name FROM subcat1 WHERE maincat_id = $parent_id AND status=1 ORDER BY name";
                $sub_res = $this->query($this->db, $sub_qry);
                
                while ($sub_row = $this->fetchOne($sub_res)) {
                    $sub_id = $sub_row['subcat_id'];
                    $sub_name = ucwords(strtolower($sub_row['name']));

                    $sub_count_qry = "SELECT COUNT(*) as cnt FROM product WHERE subcat_id = $sub_id";
                    $sub_count_res = $this->query($this->db, $sub_count_qry);
                    $sub_count_row = $this->fetchOne($sub_count_res);
                    $sub_count = (int)$sub_count_row['cnt'];

                    if ($sub_count > 0 && $sub_name !== $parent_name) {
                        $jewel_data['children']["jewel_child:$sub_id"] = [
                            'name' => "— $sub_name",
                            'count' => $sub_count
                        ];
                    }
                }
                $jewel_data['count'] += $parent_count;
            }
        }
        if ($jewel_data['count'] > 0) {
            $categories['Jewellery'] = $jewel_data;
        }

        return $categories;
    }

    private function getProductDetails($product) {
        $sku = $product['code'];
        $type = $product['type'];
        $priceSource = $product['price_source'] ?? 'pos';
        
        // POS Data
        $pos_query = "SELECT category, category_type, unit_price, quantity, cost_price FROM phppos_items WHERE name LIKE '$sku'";
        $pos_result = $this->query($this->db3, $pos_query);
        $pos_item = $this->fetchOne($pos_result);

        // Default values if POS data is missing
        $category_name = 'N/A';
        $product_type_label = ($type == 'jewellery') ? 'Jewellery' : 'Apparel';
        $quantity = 0;
        $mrp = 0;
        $cost_price = 0;
        $product_type_id = ($type == 'jewellery') ? 1 : 2;

        if ($pos_item) {
            $category_name = $pos_item['category'] ?? 'N/A';
            $product_type_id = $pos_item['category_type'] ?? $product_type_id;
            $product_type_label = ($product_type_id == 1) ? 'Jewellery' : 'Apparel';
            $quantity = $pos_item['quantity'] ?? 0;
            $mrp = $pos_item['unit_price'] ?? 0;
            $cost_price = $pos_item['cost_price'] ?? 0;
        }

        // --- Manual price source: use DB values directly ---
        if ($priceSource === 'manual') {
            $lastSellingPrice = (float)($product['original_sales_price'] ?? 0);
            $addedRentPrice = (float)($product['db_rent_price'] ?? 0);
            $deposit = (float)($product['db_deposit'] ?? 0);

            // Booking Status (still useful for manual products)
            $todaysdt = date('Y-m-d');
            $order_query = "SELECT b.pick_date, b.delivery_date, b.booking_status 
                            FROM order_detail a 
                            JOIN phppos_rent b ON a.bill_id = b.bill_id 
                            WHERE a.item_id='$sku' 
                            AND (b.pick_date >= '$todaysdt' OR b.delivery_date >= '$todaysdt') 
                            AND b.booking_status != 'Returned' 
                            ORDER BY b.pick_date ASC";
            $order_result = $this->query($this->db3, $order_query);
            $bookings = $this->fetchAll($order_result);

            // Image
            $img_field = ($type == 'jewellery') ? "product_id" : "gproduct_id";
            $pid = $product['id'];
            $img_query = "SELECT img_name FROM product_images_new WHERE $img_field = '$pid' ORDER BY rank LIMIT 1";
            $img_result = $this->query($this->db, $img_query);
            $img_row = $this->fetchOne($img_result);
            
            $img_name = $img_row['img_name'] ?? '';
            if (!empty($img_name)) {
                $clean_img_name = ltrim(str_replace(['../../yn/uploads', '../yn/uploads', '/yn/uploads'], '', $img_name), '/');
                $image_path = "https://srishringarr.com/yn/uploads/" . $clean_img_name;
            } else {
                $image_path = 'https://srishringarr.com/static/images/default.jpg';
            }

            return [
                'category_name' => $category_name,
                'product_type_label' => $product_type_label,
                'quantity' => $quantity,
                'sale_price' => $lastSellingPrice,
                'rent_price' => $addedRentPrice,
                'deposit' => $deposit,
                'bookings' => $bookings,
                'image_path' => $image_path,
                'price_source' => 'manual',
                'availability' => $product['availability'] ?? 'both'
            ];
        }

        // --- POS price source: existing formula logic ---
        // Commission
        $comm_query = "SELECT SUM(CAST(REPLACE(commission_amt, ',', '') AS DECIMAL(10,2))) 
                       FROM order_detail 
                       WHERE item_id='$sku' 
                       AND bill_id IN (SELECT bill_id FROM phppos_rent WHERE booking_status != 'Booked')";
        $comm_result = $this->query($this->db3, $comm_query);
        $comm_row = mysqli_fetch_row($comm_result);
        $commissionAmount = (float)($comm_row[0] ?? 0);

        $currentsp = $mrp - $commissionAmount;

        // Price Calculations
        $lastSellingPrice = 0;
        $addedRentPrice = 0;
        $deposit = 0;

        if ($product_type_id == 1) { // Jewellery
            $courier = ($mrp <= 2000) ? 100 : (($mrp <= 5000) ? 250 : (($mrp <= 10000) ? 500 : 1000));
            
            $sellingCalc = $mrp - $commissionAmount;
            $sellingCalc = $sellingCalc - ($sellingCalc * 0.4);

            if ($mrp >= 10000) {
                $lastSellingPrice = ($sellingCalc < 5000) ? 5000 : $sellingCalc;
            } else {
                $lastSellingPrice = $mrp - ($mrp * 0.5);
            }

            if ($currentsp > 0) {
                if ($mrp <= 10000) {
                    $rentprice = $mrp * 0.20;
                    $addedRentPrice = $courier + $rentprice;
                    $deposit = $mrp * 0.35;
                } else {
                    $rentprice = ($currentsp <= 40000) ? ($currentsp * 0.20) : (($currentsp <= 60000) ? ($currentsp * 0.17) : ($currentsp * 0.15));
                    $addedRentPrice = max(3000, $courier + $rentprice);
                    $deposit = max(3000, $currentsp * 0.35);
                }
            } else {
                if ($mrp <= 10000) {
                    $addedRentPrice = $courier + ($mrp * 0.20);
                    $deposit = $mrp * 0.35;
                } else {
                    $deposit = 3000;
                    $addedRentPrice = 3000;
                }
            }
        } else { // Garments
            $sellingCalc = $mrp - $commissionAmount;
            $sellingCalc = $sellingCalc - ($sellingCalc * 0.4);

            if ($mrp >= 10000) {
                $lastSellingPrice = ($sellingCalc < 5000) ? 5000 : $sellingCalc;
            } else {
                $lastSellingPrice = $mrp - ($mrp * 0.5);
            }

            if ($currentsp > 0) {
                if ($mrp <= 10000) {
                    $courier = 1000;
                    $addedRentPrice = $courier + ($mrp * 0.20);
                    $deposit = $mrp * 0.35;
                } else {
                    $courier = 2000;
                    $rentprice = ($currentsp <= 40000) ? ($currentsp * 0.20) : (($currentsp <= 60000) ? ($currentsp * 0.17) : ($currentsp * 0.15));
                    $addedRentPrice = max(3000, $courier + $rentprice);
                    $deposit = max(3000, $currentsp * 0.35);
                }
            } else {
                if ($mrp <= 10000) {
                    $courier = 1000;
                    $addedRentPrice = $courier + ($mrp * 0.20);
                    $deposit = $mrp * 0.35;
                } else {
                    $deposit = 3000;
                    $addedRentPrice = 3000;
                }
            }
        }

        if (isset($product['original_sales_price']) && $product['original_sales_price'] > 0) {
            $lastSellingPrice = $product['original_sales_price'];
        }

        // Booking Status
        $todaysdt = date('Y-m-d');
        $order_query = "SELECT b.pick_date, b.delivery_date, b.booking_status 
                        FROM order_detail a 
                        JOIN phppos_rent b ON a.bill_id = b.bill_id 
                        WHERE a.item_id='$sku' 
                        AND (b.pick_date >= '$todaysdt' OR b.delivery_date >= '$todaysdt') 
                        AND b.booking_status != 'Returned' 
                        ORDER BY b.pick_date ASC";
        $order_result = $this->query($this->db3, $order_query);
        $bookings = $this->fetchAll($order_result);

        // Image
        $img_field = ($type == 'jewellery') ? "product_id" : "gproduct_id";
        $pid = $product['id'];
        $img_query = "SELECT img_name FROM product_images_new WHERE $img_field = '$pid' ORDER BY rank LIMIT 1";
        $img_result = $this->query($this->db, $img_query);
        $img_row = $this->fetchOne($img_result);
        
        $img_name = $img_row['img_name'] ?? '';
        if (!empty($img_name)) {
            // Remove leading slashes/paths and ensure absolute URL
            $clean_img_name = ltrim(str_replace(['../../yn/uploads', '../yn/uploads', '/yn/uploads'], '', $img_name), '/');
            $image_path = "https://srishringarr.com/yn/uploads/" . $clean_img_name;
        } else {
            $image_path = 'https://srishringarr.com/static/images/default.jpg';
        }

        return [
            'category_name' => $category_name,
            'product_type_label' => $product_type_label,
            'quantity' => $quantity,
            'sale_price' => $lastSellingPrice,
            'rent_price' => ceil($addedRentPrice / 100) * 100,
            'deposit' => ceil($deposit / 100) * 100,
            'bookings' => $bookings,
            'image_path' => $image_path,
            'price_source' => 'pos',
            'availability' => $product['availability'] ?? 'both'
        ];
    }
    public function validateSkuInPos($sku) {
        $sku = mysqli_real_escape_string($this->db3, $sku);
        $query = "SELECT item_id, name FROM phppos_items WHERE name = '$sku' LIMIT 1";
        $result = $this->query($this->db3, $query);
        return $this->fetchOne($result);
    }

    public function getJewelCategories() {
        $sql = "SELECT subcat_id, categories_name FROM jewel_subcat WHERE mcat_id=1 OR mcat_id=3 ORDER BY categories_name ASC";
        $result = $this->query($this->db, $sql);
        return $this->fetchAll($result);
    }

    public function getJewelSubcategories($categoryId) {
        $categoryId = (int)$categoryId;
        $sql = "SELECT subcat_id, name FROM subcat1 WHERE maincat_id = $categoryId AND status = 1 ORDER BY name ASC";
        $result = $this->query($this->db, $sql);
        return $this->fetchAll($result);
    }

    public function getGarmentSubcategories($garmentId) {
        $garmentId = (int)$garmentId;
        $sql = "SELECT sub_id, sub_name FROM garment_subcat WHERE gmain_id = $garmentId ORDER BY sub_name ASC";
        $result = $this->query($this->db, $sql);
        return $this->fetchAll($result);
    }

    public function getGarments() {
        $sql = "SELECT garment_id, name FROM garments WHERE Main_id=1 OR Main_id=3 ORDER BY name";
        $result = $this->query($this->db, $sql);
        return $this->fetchAll($result);
    }

    public function getSubcategoriesByParent($parentId) {
        $parentId = (int)$parentId;
        $query = "SELECT subcat_id, name FROM jewel_subcat WHERE parent_id = $parentId ORDER BY name";
        $result = $this->query($this->db, $query);
        return $this->fetchAll($result);
    }

    public function saveProduct($type, $data, $images = []) {
        mysqli_begin_transaction($this->db);
        try {
            $product_id = 0;
            $date_added = date('Y-m-d H:i:s');
            $name = $data['name'];
            $desc = $data['description'] ?? '';
            $code = $data['code'];
            $cat = (int)($data['category'] ?? 0);
            $sub = (int)($data['sub_category'] ?? 0);
            $price = (float)($data['s_price'] ?? 0);
            $rent = (float)($data['rental_price'] ?? 0);
            $dep = (float)($data['deposit'] ?? 0);
            $featured = (int)($data['featured'] ?? 0);
            $priceSource = ($data['price_source'] ?? 'pos') === 'manual' ? 'manual' : 'pos';
            $availability = in_array($data['availability'] ?? 'both', ['rent', 'sell', 'both']) ? $data['availability'] : 'both';
            
            if ($type === 'jewellery') {
                $sql = "INSERT INTO product (
                    product_code, product_name, product_desc, date_added, 
                    categories_id, subcat_id, sales_price, rent_price, deposit, featured, price_source, availability
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = mysqli_prepare($this->db, $sql);
                mysqli_stmt_bind_param($stmt, "ssssiidddiss", $code, $name, $desc, $date_added, $cat, $sub, $price, $rent, $dep, $featured, $priceSource, $availability);
                mysqli_stmt_execute($stmt);
                $product_id = mysqli_insert_id($this->db);
                mysqli_stmt_close($stmt);
            } else {
                $sql = "INSERT INTO garment_product (
                    gproduct_code, gproduct_name, gproduct_desc, date_added, 
                    garment_id, product_for, sales_price, rent_price, deposit, featured, price_source, availability
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = mysqli_prepare($this->db, $sql);
                mysqli_stmt_bind_param($stmt, "ssssiidddiss", $code, $name, $desc, $date_added, $cat, $cat, $price, $rent, $dep, $featured, $priceSource, $availability);
                mysqli_stmt_execute($stmt);
                $product_id = mysqli_insert_id($this->db);
                mysqli_stmt_close($stmt);
            }

            // Save Images
            $main_image = '';
            foreach ($images as $index => $path) {
                if ($index === 0) $main_image = $path;
                
                $img_field = ($type === 'jewellery') ? 'product_id' : 'gproduct_id';
                $subcat_val = ($type === 'jewellery') ? $sub : 0;
                $full_path = '/' . $path;
                
                $img_sql = "INSERT INTO product_images_new (
                    prod_name, prod_image, pro_code, img_name, 
                    subcat_id, $img_field, date_added
                ) VALUES (?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = mysqli_prepare($this->db, $img_sql);
                mysqli_stmt_bind_param($stmt, "ssssiis", $name, $full_path, $code, $full_path, $subcat_val, $product_id, $date_added);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }

            // Update main image
            if ($main_image) {
                $update_field = ($type === 'jewellery') ? 'product_image' : 'gproduct_image';
                $table = ($type === 'jewellery') ? 'product' : 'garment_product';
                $pk = ($type === 'jewellery') ? 'product_id' : 'gproduct_id';
                
                $update_sql = "UPDATE $table SET $update_field = ? WHERE $pk = ?";
                $stmt = mysqli_prepare($this->db, $update_sql);
                mysqli_stmt_bind_param($stmt, "si", $main_image, $product_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }

            mysqli_commit($this->db);
            return true;
        } catch (\Exception $e) {
            mysqli_rollback($this->db);
            throw $e;
        }
    }
    public function checkProductExists($code, $type) {
        $code = mysqli_real_escape_string($this->db, $code);
        if ($type === 'jewellery') {
            $sql = "SELECT product_id FROM product WHERE product_code = '$code' LIMIT 1";
        } else {
            $sql = "SELECT gproduct_id FROM garment_product WHERE gproduct_code = '$code' LIMIT 1";
        }
        $result = $this->query($this->db, $sql);
        return $this->fetchOne($result);
    }

    public function getProductById($id, $type) {
        $id = (int)$id;
        if ($type === 'jewellery') {
            $sql = "SELECT p.product_id as id, p.product_code as code, p.product_name as name, p.product_desc as description, 
                           p.categories_id as category, p.subcat_id as sub_category, p.sales_price as s_price, 
                           p.rent_price as rental_price, p.deposit, p.discount, p.featured, p.price_source, p.availability,
                           c.categories_name as category_name, s.name as subcategory_name
                    FROM product p
                    LEFT JOIN jewel_subcat c ON p.categories_id = c.subcat_id
                    LEFT JOIN subcat1 s ON p.subcat_id = s.subcat_id
                    WHERE p.product_id = $id";
        } else {
            $sql = "SELECT p.gproduct_id as id, p.gproduct_code as code, p.gproduct_name as name, p.gproduct_desc as description, 
                           p.garment_id as category, p.product_for as sub_category, p.sales_price as s_price, 
                           p.rent_price as rental_price, p.deposit, p.discount, p.featured, p.price_source, p.availability,
                           c.name as category_name, s.name as subcategory_name
                    FROM garment_product p
                    LEFT JOIN garments c ON p.garment_id = c.garment_id
                    LEFT JOIN garments s ON p.product_for = s.garment_id
                    WHERE p.gproduct_id = $id";
        }
        $result = $this->query($this->db, $sql);
        return $this->fetchOne($result);
    }

    public function getProductImages($id, $type) {
        $id = (int)$id;
        $table = ($type === 'jewellery') ? 'product' : 'garment_product';
        $id_field = ($type === 'jewellery') ? 'product_id' : 'gproduct_id';
        $code_field = ($type === 'jewellery') ? 'product_code' : 'gproduct_code';
        
        $code_query = "SELECT $code_field as code FROM $table WHERE $id_field = $id LIMIT 1";
        $code_result = $this->query($this->db, $code_query);
        $product = $this->fetchOne($code_result);
        
        if ($product && !empty($product['code'])) {
            $sku = mysqli_real_escape_string($this->db, $product['code']);
            $sql = "SELECT id, img_name, rank FROM product_images_new WHERE pro_code = '$sku' ORDER BY rank ASC";
            $result = $this->query($this->db, $sql);
            return $this->fetchAll($result);
        }
        
        $img_field = ($type === 'jewellery') ? 'product_id' : 'gproduct_id';
        $sql = "SELECT id, img_name, rank FROM product_images_new WHERE $img_field = $id ORDER BY rank ASC";
        $result = $this->query($this->db, $sql);
        return $this->fetchAll($result);
    }

    public function updateProduct($type, $id, $data, $images = []) {
        mysqli_begin_transaction($this->db);
        try {
            $id = (int)$id;
            $featured = (int)($data['featured'] ?? 0);
            $priceSource = ($data['price_source'] ?? 'pos') === 'manual' ? 'manual' : 'pos';
            $availability = in_array($data['availability'] ?? 'both', ['rent', 'sell', 'both']) ? $data['availability'] : 'both';
            $name = $data['name'];
            $desc = $data['description'] ?? '';
            $cat = (int)($data['category'] ?? 0);
            $sub = (int)($data['sub_category'] ?? 0);
            $price = (float)($data['s_price'] ?? 0);
            $rent = (float)($data['rental_price'] ?? 0);
            $dep = (float)($data['deposit'] ?? 0);

            if ($type === 'jewellery') {
                $sql = "UPDATE product SET 
                    product_name = ?, 
                    product_desc = ?, 
                    categories_id = ?, 
                    subcat_id = ?, 
                    sales_price = ?, 
                    rent_price = ?, 
                    deposit = ?,
                    featured = ?,
                    price_source = ?,
                    availability = ?
                    WHERE product_id = ?";
                $stmt = mysqli_prepare($this->db, $sql);
                if (!$stmt) throw new \Exception(mysqli_error($this->db));
                mysqli_stmt_bind_param($stmt, "ssiidddissi", $name, $desc, $cat, $sub, $price, $rent, $dep, $featured, $priceSource, $availability, $id);
                if (!mysqli_stmt_execute($stmt)) throw new \Exception(mysqli_error($this->db));
                mysqli_stmt_close($stmt);
            } else {
                $sql = "UPDATE garment_product SET 
                    gproduct_name = ?, 
                    gproduct_desc = ?, 
                    garment_id = ?, 
                    product_for = ?, 
                    sales_price = ?, 
                    rent_price = ?, 
                    deposit = ?,
                    featured = ?,
                    price_source = ?,
                    availability = ?
                    WHERE gproduct_id = ?";
                $stmt = mysqli_prepare($this->db, $sql);
                if (!$stmt) throw new \Exception(mysqli_error($this->db));
                mysqli_stmt_bind_param($stmt, "ssiidddissi", $name, $desc, $cat, $cat, $price, $rent, $dep, $featured, $priceSource, $availability, $id);
                if (!mysqli_stmt_execute($stmt)) throw new \Exception(mysqli_error($this->db));
                mysqli_stmt_close($stmt);
            }

            // Save New Images
            $date_added = date('Y-m-d H:i:s');
            foreach ($images as $path) {
                $img_field = ($type === 'jewellery') ? 'product_id' : 'gproduct_id';
                $subcat_val = ($type === 'jewellery') ? $sub : 0;
                $full_path = '/' . $path;
                $code = $data['code'];
                
                $img_sql = "INSERT INTO product_images_new (
                    prod_name, prod_image, pro_code, img_name, 
                    subcat_id, $img_field, date_added
                ) VALUES (?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = mysqli_prepare($this->db, $img_sql);
                if (!$stmt) throw new \Exception(mysqli_error($this->db));
                mysqli_stmt_bind_param($stmt, "ssssiis", $name, $full_path, $code, $full_path, $subcat_val, $id, $date_added);
                if (!mysqli_stmt_execute($stmt)) throw new \Exception(mysqli_error($this->db));
                mysqli_stmt_close($stmt);
            }

            mysqli_commit($this->db);
            return true;
        } catch (\Exception $e) {
            mysqli_rollback($this->db);
            throw $e;
        }
    }
    public function deleteProduct($id, $type) {
        mysqli_begin_transaction($this->db);
        try {
            $id = (int)$id;
            if ($type === 'jewellery') {
                $sql = "DELETE FROM product WHERE product_id = $id";
                $img_field = 'product_id';
            } else {
                $sql = "DELETE FROM garment_product WHERE gproduct_id = $id";
                $img_field = 'gproduct_id';
            }
            
            if (!$this->query($this->db, $sql)) throw new \Exception(mysqli_error($this->db));
            
            // Delete images from database (files remain for now to avoid accidental data loss)
            $img_del_sql = "DELETE FROM product_images_new WHERE $img_field = $id";
            $this->query($this->db, $img_del_sql);

            mysqli_commit($this->db);
            return true;
        } catch (\Exception $e) {
            mysqli_rollback($this->db);
            throw $e;
        }
    }

    public function deleteImage($imageId) {
        $imageId = (int)$imageId;
        $sql = "DELETE FROM product_images_new WHERE id = $imageId";
        return $this->query($this->db, $sql);
    }

    public function syncProductBySku($type, $data, $images = []) {
        $sku = $data['code'];
        $name = $data['name'];
        $desc = $data['description'] ?? '';
        $cat = (int)($data['category'] ?? 0);
        $sub = (int)($data['sub_category'] ?? 0);
        $price = (float)($data['s_price'] ?? 0);
        $rent = (float)($data['rental_price'] ?? 0);
        $dep = (float)($data['deposit'] ?? 0);

        if ($type === 'jewellery') {
            $sql = "UPDATE product SET 
                    product_name = ?, 
                    product_desc = ?, 
                    categories_id = ?, 
                    subcat_id = ?, 
                    sales_price = ?, 
                    rent_price = ?, 
                    deposit = ?
                    WHERE product_code = ?";
            
            $stmt = mysqli_prepare($this->db, $sql);
            mysqli_stmt_bind_param($stmt, "ssiiddds", $name, $desc, $cat, $sub, $price, $rent, $dep, $sku);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            
            if (!empty($images)) {
                $date_added = date('Y-m-d H:i:s');
                foreach ($images as $img) {
                    $imgSql = "INSERT INTO product_images_new (product_id, pro_code, img_name, prod_name, prod_image, date_added) 
                               VALUES ((SELECT product_id FROM product WHERE product_code = ? LIMIT 1), ?, ?, ?, ?, ?)";
                    $stmt = mysqli_prepare($this->db, $imgSql);
                    mysqli_stmt_bind_param($stmt, "ssssss", $sku, $sku, $img, $name, $img, $date_added);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            }
        } else {
            $sql = "UPDATE garment_product SET 
                    gproduct_name = ?, 
                    gproduct_desc = ?, 
                    garment_id = ?, 
                    sales_price = ?, 
                    rent_price = ?, 
                    deposit = ?
                    WHERE gproduct_code = ?";
            
            $stmt = mysqli_prepare($this->db, $sql);
            mysqli_stmt_bind_param($stmt, "ssiddds", $name, $desc, $cat, $price, $rent, $dep, $sku);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            if (!empty($images)) {
                $date_added = date('Y-m-d H:i:s');
                foreach ($images as $img) {
                    $imgSql = "INSERT INTO product_images_new (gproduct_id, pro_code, img_name, prod_name, prod_image, date_added) 
                               VALUES ((SELECT gproduct_id FROM garment_product WHERE gproduct_code = ? LIMIT 1), ?, ?, ?, ?, ?)";
                    $stmt = mysqli_prepare($this->db, $imgSql);
                    mysqli_stmt_bind_param($stmt, "ssssss", $sku, $sku, $img, $name, $img, $date_added);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            }
        }
        return true;
    }

    public function deleteBySku($sku) {
        $sku = mysqli_real_escape_string($this->db, $sku);
        
        // Try deleting from Jewelry table
        $sql1 = "DELETE FROM product WHERE product_code = '$sku'";
        $res1 = mysqli_query($this->db, $sql1);
        $affected1 = mysqli_affected_rows($this->db);

        // Try deleting from Garments table
        $sql2 = "DELETE FROM garment_product WHERE gproduct_code = '$sku'";
        $res2 = mysqli_query($this->db, $sql2);
        $affected2 = mysqli_affected_rows($this->db);

        return ($affected1 > 0 || $affected2 > 0);
    }

    public function toggleFeatured($id, $type, $status) {
        $id = (int)$id;
        $status = (int)$status;
        if ($type === 'jewellery') {
            $sql = "UPDATE product SET featured = $status WHERE product_id = $id";
        } else {
            $sql = "UPDATE garment_product SET featured = $status WHERE gproduct_id = $id";
        }
        return $this->query($this->db, $sql);
    }

    public function togglePriceSource($id, $type, $priceSource) {
        $id = (int)$id;
        $priceSource = $priceSource === 'manual' ? 'manual' : 'pos';
        if ($type === 'jewellery') {
            $sql = "UPDATE product SET price_source = '$priceSource' WHERE product_id = $id";
        } else {
            $sql = "UPDATE garment_product SET price_source = '$priceSource' WHERE gproduct_id = $id";
        }
        return $this->query($this->db, $sql);
    }

    public function toggleAvailability($id, $type, $availability) {
        $id = (int)$id;
        $availability = in_array($availability, ['rent', 'sell', 'both']) ? $availability : 'both';
        if ($type === 'jewellery') {
            $sql = "UPDATE product SET availability = '$availability' WHERE product_id = $id";
        } else {
            $sql = "UPDATE garment_product SET availability = '$availability' WHERE gproduct_id = $id";
        }
        return $this->query($this->db, $sql);
    }

    public function addImagesToProduct($sku, $images) {
        $existsJewel = $this->checkProductExists($sku, 'jewellery');
        $existsGarment = $this->checkProductExists($sku, 'garments');

        if (!$existsJewel && !$existsGarment) {
            return false;
        }

        $type = $existsJewel ? 'jewellery' : 'garments';
        $product = $this->getProductById($existsJewel ? $existsJewel['product_id'] : $existsGarment['gproduct_id'], $type);
        if (!$product) return false;

        $id = $product['id'];
        $name = $product['name'];
        $sub = $product['sub_category'] ?? 0;
        
        $img_field = ($type === 'jewellery') ? 'product_id' : 'gproduct_id';
        $subcat_val = ($type === 'jewellery') ? $sub : 0;
        $date_added = date('Y-m-d H:i:s');

        foreach ($images as $path) {
            $full_path = '/' . $path;
            
            $img_sql = "INSERT INTO product_images_new (
                prod_name, prod_image, pro_code, img_name, 
                subcat_id, $img_field, date_added
            ) VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($this->db, $img_sql);
            if (!$stmt) throw new \Exception(mysqli_error($this->db));
            mysqli_stmt_bind_param($stmt, "ssssiis", $name, $full_path, $sku, $full_path, $subcat_val, $id, $date_added);
            if (!mysqli_stmt_execute($stmt)) throw new \Exception(mysqli_error($this->db));
            mysqli_stmt_close($stmt);
        }

        return true;
    }

    public function setMainProductImage($imageId, $productId, $type) {
        $imageId = (int)$imageId;
        $productId = (int)$productId;
        $img_field = ($type === 'jewellery') ? 'product_id' : 'gproduct_id';

        $sql1 = "UPDATE product_images_new SET rank = 1 WHERE $img_field = $productId";
        $this->query($this->db, $sql1);

        $sql2 = "UPDATE product_images_new SET rank = 0 WHERE id = $imageId";
        $this->query($this->db, $sql2);

        $imgQ = "SELECT img_name FROM product_images_new WHERE id = $imageId LIMIT 1";
        $res = $this->query($this->db, $imgQ);
        $row = $this->fetchOne($res);
        if ($row) {
            $main_image = $row['img_name'];
            $table = ($type === 'jewellery') ? 'product' : 'garment_product';
            $field = ($type === 'jewellery') ? 'product_image' : 'gproduct_image';
            $pk = ($type === 'jewellery') ? 'product_id' : 'gproduct_id';
            
            $sql3 = "UPDATE $table SET $field = ? WHERE $pk = ?";
            $stmt = mysqli_prepare($this->db, $sql3);
            mysqli_stmt_bind_param($stmt, "si", $main_image, $productId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        return true;
    }

    public function getPosQuantity($sku) {
        $sku = mysqli_real_escape_string($this->db3, $sku);
        $query = "SELECT quantity FROM phppos_items WHERE name = '$sku' LIMIT 1";
        $result = $this->query($this->db3, $query);
        $row = $this->fetchOne($result);
        return (int)($row['quantity'] ?? 0);
    }

    public function getDbConnection() {
        return $this->db;
    }
}
