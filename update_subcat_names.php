<?php
require_once __DIR__ . '/Core/Database.php';
$db = \Core\Database::getConnection('con');

if (!$db) {
    die("Database connection failed.\n");
}

echo "=== Current State in subcat1 ===\n";
$res = $db->query("SELECT subcat_id, maincat_id, name FROM subcat1 WHERE subcat_id IN (83, 84) OR name LIKE '%Marathi%' OR name LIKE '%Long%'");
while ($r = $res->fetch_assoc()) {
    echo "ID {$r['subcat_id']} (maincat {$r['maincat_id']}): '{$r['name']}'\n";
}

// 1. Update subcat1 table
$db->query("UPDATE subcat1 SET name = 'Long Set' WHERE subcat_id = 83 OR name = 'Long Necklace Sets' OR name = 'Long necklace sets'");
$db->query("UPDATE subcat1 SET name = 'Marathi Set' WHERE subcat_id = 84 OR name = 'Marathi Long Necklace' OR name = 'marathi long necklace'");

// 2. Update categories table if it exists
$checkCategories = $db->query("SHOW TABLES LIKE 'categories'");
if ($checkCategories && $checkCategories->num_rows > 0) {
    $db->query("UPDATE categories SET name = 'Long Set' WHERE name LIKE '%Long Necklace%'");
    $db->query("UPDATE categories SET name = 'Marathi Set' WHERE name LIKE '%Marathi Long%'");
}

// 3. Update category_mappings table if it exists
$checkMappings = $db->query("SHOW TABLES LIKE 'category_mappings'");
if ($checkMappings && $checkMappings->num_rows > 0) {
    $db->query("UPDATE category_mappings SET category_name = 'Long Set' WHERE category_name LIKE '%Long Necklace%'");
    $db->query("UPDATE category_mappings SET category_name = 'Marathi Set' WHERE category_name LIKE '%Marathi Long%'");
    $db->query("UPDATE category_mappings SET legacy_name = 'Long Set' WHERE legacy_name LIKE '%Long Necklace%'");
    $db->query("UPDATE category_mappings SET legacy_name = 'Marathi Set' WHERE legacy_name LIKE '%Marathi Long%'");
}

echo "\n=== Updated State in subcat1 ===\n";
$res2 = $db->query("SELECT subcat_id, maincat_id, name FROM subcat1 WHERE subcat_id IN (83, 84) OR name LIKE '%Marathi%' OR name LIKE '%Long%'");
while ($r = $res2->fetch_assoc()) {
    echo "ID {$r['subcat_id']} (maincat {$r['maincat_id']}): '{$r['name']}'\n";
}

echo "\nUpdate completed successfully!\n";
