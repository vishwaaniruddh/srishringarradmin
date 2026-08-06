<?php
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

function inferCategoryFromSku($sku, $type = 'jewellery', $existingCat = 0, $existingSub = 0) {
    $cleanSku = strtoupper(trim($sku));
    $cat = (int)$existingCat;
    $sub = (int)$existingSub;

    if ($type === 'jewellery') {
        if ($cat <= 0) {
            if (str_starts_with($cleanSku, 'SET')) {
                $cat = 1; // Necklace Sets
                $sub = 2; // American Diamond
            } elseif (str_starts_with($cleanSku, 'K')) {
                $cat = 1; // Necklace Sets
                $sub = 3; // Kundan
            } elseif (str_starts_with($cleanSku, 'BR')) {
                $cat = 22; // BRACELET
            } elseif (str_starts_with($cleanSku, 'JU')) {
                $cat = 15; // KAMAR PATTA
            } elseif (str_starts_with($cleanSku, 'EAR')) {
                $cat = 17; // Earrings
                $sub = 59; // EARRINGS
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
    } else { // Garments
        if ($cat <= 0) {
            if (str_starts_with($cleanSku, 'LEH')) {
                $cat = 10; // LEHENGA CHOLI
            } elseif (str_starts_with($cleanSku, 'GW') || str_starts_with($cleanSku, 'GOWN')) {
                $cat = 22; // Evening Gowns
            } elseif (str_starts_with($cleanSku, 'INDO')) {
                $cat = 28; // Indo Western Outfits
            } else {
                $cat = 10; // Default LEHENGA CHOLI
            }
        }
    }

    return ['category_id' => $cat, 'subcategory_id' => $sub];
}

// Test cases
$testSkus = ['set824', 'set500on', 'set449', 'k196', 'K2049', 'BR41CS', 'JU29', 'EAR940C', 'LEH101'];
foreach ($testSkus as $s) {
    $res = inferCategoryFromSku($s, 'jewellery', 0, 0);
    echo "SKU: $s -> Cat: {$res['category_id']}, Sub: {$res['subcategory_id']}\n";
}
