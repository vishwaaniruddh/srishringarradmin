<?php
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

function inferCategoryFromSku($sku, $type = 'auto', $existingCat = 0, $existingSub = 0) {
    $cleanSku = strtoupper(trim((string)$sku));
    $cat = (int)$existingCat;
    $sub = (int)$existingSub;

    // 1. Main Category Determination (Outfit vs Jewellery)
    if (empty($type) || $type === 'auto') {
        if (preg_match('/^(LEH|GW|GOWN|INDO|ANAR|KURTI|SAREE|SAR|DRESS|SUIT|CHOLI)/i', $cleanSku)) {
            $type = 'outfit';
        } else {
            $type = 'jewellery';
        }
    }

    $mainCategory = ($type === 'garments' || $type === 'garment' || $type === 'outfit') ? 'outfit' : 'jewellery';

    // 2. Category and Subcategory Inference based on reference menu structure
    if ($mainCategory === 'jewellery') {
        if ($cat <= 0) {
            if (str_starts_with($cleanSku, 'SET')) {
                $cat = 1; // Necklace Sets
                $sub = 2; // American Diamond
            } elseif (str_starts_with($cleanSku, 'K')) {
                $cat = 1; // Necklace Sets
                $sub = 3; // Kundan
            } elseif (str_starts_with($cleanSku, 'EAR')) {
                $cat = 17; // Earrings
                $sub = 59; // EARRINGS
            } elseif (str_starts_with($cleanSku, 'BR')) {
                $cat = 22; // Bracelet
            } elseif (str_starts_with($cleanSku, 'JU') || str_starts_with($cleanSku, 'KAMAR')) {
                $cat = 15; // Kamar Patta / Baju Bandh
            } elseif (str_starts_with($cleanSku, 'BANG')) {
                $cat = 18; // Bangles
            } elseif (str_starts_with($cleanSku, 'DAM') || str_starts_with($cleanSku, 'MATH')) {
                $cat = 19; // Damini / Mathapatti
            } elseif (str_starts_with($cleanSku, 'TIK')) {
                $cat = 20; // Tikka
            } elseif (str_starts_with($cleanSku, 'HATH')) {
                $cat = 21; // Hath Phool
            } elseif (str_starts_with($cleanSku, 'PAY') || str_starts_with($cleanSku, 'PAG')) {
                $cat = 23; // Payal / Pag Pan
            } elseif (str_starts_with($cleanSku, 'PEND')) {
                $cat = 24; // Pendant Set
            } elseif (str_starts_with($cleanSku, 'MALA')) {
                $cat = 25; // Mala
            } elseif (str_starts_with($cleanSku, 'BOR')) {
                $cat = 26; // Borlas
            } else {
                $cat = 1; // Default Necklace Sets
                $sub = 3; // Kundan
            }
        } else {
            if ($sub <= 0 && $cat == 1) {
                if (str_starts_with($cleanSku, 'SET')) {
                    $sub = 2; // American Diamond
                } elseif (str_starts_with($cleanSku, 'K')) {
                    $sub = 3; // Kundan
                }
            }
        }
    } else { // DESIGNER OUTFITS
        if ($cat <= 0) {
            if (str_starts_with($cleanSku, 'LEH')) {
                $cat = 10; // Lehenga Choli
            } elseif (str_starts_with($cleanSku, 'GW') || str_starts_with($cleanSku, 'GOWN')) {
                $cat = 22; // Evening Gowns
            } elseif (str_starts_with($cleanSku, 'INDO')) {
                $cat = 28; // Indo Western Outfits
            } elseif (str_starts_with($cleanSku, 'ANAR') || str_starts_with($cleanSku, 'KURTI')) {
                $cat = 29; // Anarkalis / Kurtis
            } elseif (str_starts_with($cleanSku, 'SAREE') || str_starts_with($cleanSku, 'SAR')) {
                $cat = 30; // Sarees
            } else {
                $cat = 10; // Default Lehenga Choli
            }
        }
    }

    // Map category_id to human-readable name for child DB
    $catNames = [
        1 => 'Necklace Sets', 17 => 'Earrings', 22 => 'Bracelet',
        15 => 'Kamar Patta', 18 => 'Bangles', 19 => 'Damini / Mathapatti',
        20 => 'Tikka', 21 => 'Hath Phool', 23 => 'Payal / Pag Pan',
        24 => 'Pendant Set', 25 => 'Mala', 26 => 'Borlas',
        10 => 'Lehenga Choli', 28 => 'Indo Western Outfits',
        29 => 'Anarkalis / Kurtis', 30 => 'Sarees'
    ];
    if ($mainCategory === 'outfit' && $cat === 22) {
        $categoryName = 'Evening Gowns';
    } else {
        $categoryName = $catNames[$cat] ?? ($mainCategory === 'outfit' ? 'Outfit' : 'Jewellery');
    }

    return [
        'main_category' => $mainCategory,
        'type'          => $mainCategory === 'outfit' ? 'garments' : 'jewellery',
        'category_id'   => $cat,
        'subcategory_id'=> $sub,
        'category_name' => $categoryName
    ];
}

// Test cases covering DESIGNER OUTFITS and JEWELLERY categories from the reference
$testSkus = [
    'set824', 'set500on', 'k196', 'K2049', 'BR41CS', 'JU29', 'EAR940C', 
    'BANG102', 'DAM15', 'TIK44', 'HATH09', 'PAY88', 'PEND33',
    'LEH101', 'GW202', 'INDO55', 'ANAR77', 'SAREE12', 'B206ON22.4', 'D544ON'
];

echo "==========================================================================================================\n";
echo " SKU            | Main Category  | Child Category Name       | Cat ID | Sub ID \n";
echo "==========================================================================================================\n";
foreach ($testSkus as $s) {
    $res = inferCategoryFromSku($s, 'auto', 0, 0);
    echo " " . str_pad($s, 14) . " | " . str_pad($res['main_category'], 14) . " | " . str_pad($res['category_name'], 25) . " | " . str_pad($res['category_id'], 6) . " | " . str_pad($res['subcategory_id'], 6) . "\n";
}
echo "==========================================================================================================\n";

