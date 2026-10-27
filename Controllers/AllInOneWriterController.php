<?php
namespace Controllers;

use Core\Controller;
use Core\Database;
use Models\ProductModel;

class AllInOneWriterController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection('con');
    }

    /**
     * Get AI Writer image analysis settings (default vs multi-image)
     */
    public static function getAiWriterSettings()
    {
        $file = __DIR__ . '/../Config/ai_writer_settings.json';
        $defaults = [
            'image_analysis_mode' => 'default', // 'default' (single image) or 'multi' (multiple images up to 10)
            'max_images' => 10,
            'include_content_analysis' => true,
            'updated_at' => null
        ];
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            if (is_array($data)) {
                return array_merge($defaults, $data);
            }
        }
        return $defaults;
    }

    /**
     * Persist AI Writer image analysis settings
     */
    public static function saveAiWriterSettings($data)
    {
        $file = __DIR__ . '/../Config/ai_writer_settings.json';
        $current = self::getAiWriterSettings();
        $merged = array_merge($current, $data);
        $merged['max_images'] = max(1, min(10, (int)($merged['max_images'] ?? 10)));
        if (!in_array($merged['image_analysis_mode'] ?? '', ['default', 'multi'])) {
            $merged['image_analysis_mode'] = 'default';
        }
        $merged['include_content_analysis'] = !empty($merged['include_content_analysis']);
        $merged['updated_at'] = date('Y-m-d H:i:s');

        $dir = dirname($file);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $res = @file_put_contents($file, json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return $res !== false ? $merged : false;
    }

    /**
     * Category definitions with specialized anatomical placement and photoshoot styling rules
     */
    public static function getCategoryDefinitions()
    {
        return [
            'kamar_patta' => [
                'name' => 'Kamar Patta (Waist Belt / Belly Chain)',
                'type' => 'jewellery',
                'keywords' => ['kamar', 'patta', 'kamarbandh', 'waist', 'belly', 'kamarband'],
                'sku_prefixes' => ['KP'],
                'model_instruction' => "CRITICAL ANATOMICAL PLACEMENT & STYLING RULE: This product is a KAMAR PATTA / KAMARBANDH (Indian bridal waist belt / belly chain / saree waist chain). It MUST be worn strictly around the WAIST, NAVEL, and HIPS over an Indian bridal lehenga or saree. It is ABSOLUTELY NOT a necklace, NOT an epaulette, NOT a shoulder piece, and NOT for the chest/neck! The model must wear it securely wrapped around her waist/hip curve. The camera framing must be a mid-body portrait or waist-level shot highlighting her midriff, waistline, and lehenga waist.",
                'studio_instruction' => "CRITICAL STILL-LIFE DISPLAY: The Kamar Patta is gracefully undulating horizontally across the plush angled micro-velvet jewelry cushion and rich Banarasi brocade fabric folds, displaying the entire ornate central medallion, side chain segments, and hanging pearl/bead latkans in crisp macro focus. ABSOLUTELY NO mannequin neck or chest!",
                'copy_context' => "Indian Bridal Kamarbandh / Kamar Patta (Waist Belt & Saree Belly Chain)"
            ],
            'necklace_set' => [
                'name' => 'Necklace Sets & Chokers',
                'type' => 'jewellery',
                'keywords' => ['neck', 'necklace', 'choker', 'collar', 'long set', 'marathi set', 'south indian set'],
                'sku_prefixes' => ['N', 'NL', 'CH', 'J'],
                'model_instruction' => "CRITICAL ANATOMICAL PLACEMENT: Worn gracefully around the neck and collarbone. Close-up portrait shot focusing on the neckline, décolletage, and matching earrings.",
                'studio_instruction' => "CRITICAL STILL-LIFE DISPLAY: The necklace is elegantly arranged in a natural crescent collar curve resting on the tilted plush royal micro-velvet cushion, with the matching earrings nestled symmetrically alongside it on the velvet.",
                'copy_context' => "Indian Bridal Necklace Set & Choker"
            ],
            'earrings' => [
                'name' => 'Earrings / Jhumkas / Chandbalis',
                'type' => 'jewellery',
                'keywords' => ['ear', 'earring', 'jhumk', 'chandbali', 'stud', 'bugadi', 'bali'],
                'sku_prefixes' => ['ER', 'E'],
                'model_instruction' => "CRITICAL ANATOMICAL PLACEMENT: Worn on the earlobes. Side-profile or three-quarter close-up portrait of the model with hair tucked behind ear, showing the earring hanging gracefully.",
                'studio_instruction' => "CRITICAL STILL-LIFE DISPLAY: Both earrings rest side-by-side at a dynamic complementary angle on the plush micro-velvet jewelry cushion, with studs and dangling jhumka domes / drops angled towards the light.",
                'copy_context' => "Indian Bridal Earrings, Jhumkas & Chandbalis"
            ],
            'borla_with_sari' => [
                'name' => 'Borla with Sari (Head Chain)',
                'type' => 'jewellery',
                'keywords' => ['borla with sari', 'borla with chain', 'borla with stones chain', 'borla chain', 'borla damini', 'borla mathapatti', 'borla head chain', 'borla sari', 'borla saari'],
                'sku_prefixes' => ['BO', 'B'],
                'model_instruction' => "CRITICAL ANATOMICAL PLACEMENT & 3D DOME STRUCTURE RULE (BORLA WITH SARI): This product is an authentic traditional Rajasthani BORLA WITH SARI (3D Spherical Forehead Dome with Side Head Chains / Sheeshphool).
- 3D DOME ELEVATION (MUST BE RAISED / 'THODA UTHA HUA'): The central Borla is a distinct THREE-DIMENSIONAL SPHERICAL / BELL-SHAPED HEMISPHERICAL DOME with substantial physical height and volume (~1.5 to 2 cm protruding outward in 3D from the hair), encrusted with stones on its rounded convex surface. It must be visibly elevated and projecting outward in 3D ('thoda utha hua'), with rounded dome curvature and a subtle natural cast shadow beneath its base.
- STRICT PROHIBITION AGAINST FLAT TIKKA / NO FLAT FOREHEAD STICKER: The Borla is ABSOLUTELY NOT a flat Maang Tikka! It must NEVER be drawn flat or pasted flat against the forehead skin ('ek dum mathe se chipka hua bilkul nahi hona chahiye').
- HAIRLINE PERCH PLACEMENT: The 3D spherical Borla dome rests perched right at the crest of the central hair parting (maang) at the top hairline (where the hair meets the forehead), nestled into the roots of the styled hair. It is NOT hanging down low in the middle of the bare forehead skin.
- SIDE CHAINS ('SARI'): The 'sari' (or 'sar') refers STRICTLY to the ornate, symmetrical gold/pearl/beaded hair chains that extend from the sides of the elevated central Borla, framing along the hairline over the styled hair across the temples to just above each ear where they pin into the hair.
- NO FABRIC DRAPE: 'Sari' here is HEAD CHAIN JEWELRY, ABSOLUTELY NOT a cloth saree, veil, or head covering. Do NOT wrap cloth around the head.
- The camera framing must be a high-end beauty portrait / close-up of the model's face, forehead, and hair. Model's hair is neatly styled in an elegant festive updo or center-parted bridal hairstyle, clearly and crisply showcasing both the elevated 3D round Borla dome nestled at the hairline parting and the side head chains running over the temples to above the ears.",
                'studio_instruction' => "CRITICAL STILL-LIFE DISPLAY: The Borla with Sari rests on the plush royal micro-velvet jewelry cushion. The round central 3D spherical Borla dome is elevated and prominent in the center, displaying its convex 3D dome volume, intricate Kundan floral stone setting, and dangling pearl drop. The two symmetrical decorative side hair chains ('sari' / mathapatti chains) are arranged in an elegant crescent arc spreading outwards across the velvet to the left and right, and the central anchoring chain trails upwards along the cushion. STRICTLY pure jewelry still-life, NO human model, NO clothing or saree fabric drape.",
                'copy_context' => "Traditional Rajasthani Borla with Sari (Elevated 3D Forehead Dome with Side Head Chains)"
            ],
            'borla' => [
                'name' => 'Borla (Without Sari / Standalone)',
                'type' => 'jewellery',
                'keywords' => ['borla', 'borlas', 'rajputi borla', 'marwari borla', 'kundan borla', 'vilandi borla', 'borla tikka'],
                'sku_prefixes' => ['BO', 'B'],
                'model_instruction' => "CRITICAL ANATOMICAL PLACEMENT & 3D DOME STRUCTURE RULE (STANDALONE BORLA): This product is a standalone traditional Rajasthani BORLA (WITHOUT SARI / WITHOUT SIDE CHAINS).
- 3D DOME ELEVATION (MUST BE RAISED / 'THODA UTHA HUA'): The single central Borla is a distinct THREE-DIMENSIONAL SPHERICAL / BELL-SHAPED HEMISPHERICAL DOME with substantial physical height and volume (~1.5 to 2 cm protruding outward in 3D from the hair), encrusted with stones on its rounded convex surface. It must be visibly elevated and projecting outward in 3D ('thoda utha hua'), with rounded dome curvature and a subtle natural cast shadow beneath its base.
- STRICT PROHIBITION AGAINST FLAT TIKKA / NO FLAT FOREHEAD STICKER: The Borla is ABSOLUTELY NOT a flat Maang Tikka! It must NEVER be drawn flat or pasted flat against the forehead skin ('ek dum mathe se chipka hua bilkul nahi hona chahiye').
- HAIRLINE PERCH PLACEMENT: The 3D spherical Borla dome rests perched right at the crest of the central hair parting (maang) at the top hairline (where the hair meets the forehead), nestled into the roots of the styled hair. It is secured solely by a single delicate center chain running straight back into the maang (central hair parting) and pinned into the hair.
- STRICT NEGATIVE CONSTRAINT: STRICTLY NO side chains, NO mathapatti side bands, NO temple chains, NO ear-to-ear strings! Pure standalone Borla with ONLY the single center parting chain.
- The camera framing must be a frontal beauty close-up portrait of the model's face with hair neatly parted down the center, highlighting the elevated spherical 3D Borla dome resting at the forehead hairline.",
                'studio_instruction' => "CRITICAL STILL-LIFE DISPLAY: The standalone round Borla dome rests centered on the plush royal micro-velvet jewelry cushion, with its single delicate center chain trailing straight upwards along the cushion's pile. The 3D spherical dome is angled towards the camera to showcase the convex 3D dome volume, central gemstone, surrounding Kundan/pearl petals, and hanging bead. STRICTLY NO side chains or side straps. Pure product-only still life.",
                'copy_context' => "Traditional Rajasthani Standalone Borla (Elevated 3D Bridal Forehead Dome Ornament without Side Chains)"
            ],
            'damini_mathapatti' => [
                'name' => 'Damini / Mathapatti / Maang Tikka',
                'type' => 'jewellery',
                'keywords' => ['damini', 'mathapatti', 'matha', 'tikka', 'tikkas', 'hair'],
                'sku_prefixes' => ['DP', 'T', 'TI'],
                'model_instruction' => "CRITICAL ANATOMICAL PLACEMENT: Worn on the model's forehead and along the central hairline / hair parting (maang). Face close-up portrait highlighting the forehead jewelry.",
                'studio_instruction' => "CRITICAL STILL-LIFE DISPLAY: The Tikka / Mathapatti rests centered on the plush royal micro-velvet jewelry cushion. The ornate central pendant lies flat and prominent on the velvet, while the delicate hanging chain trails gracefully upwards along the cushion's crest, catching warm directional specular highlights.",
                'copy_context' => "Indian Bridal Damini, Mathapatti & Maang Tikka"
            ],
            'nath' => [
                'name' => 'Nath (Bridal Nose Ring)',
                'type' => 'jewellery',
                'keywords' => ['nath', 'nose'],
                'sku_prefixes' => ['NATH'],
                'model_instruction' => "CRITICAL ANATOMICAL PLACEMENT: Worn on the left nostril of the nose, with the delicate pearl/stone chain pinned into the hair. Intimate beauty close-up shot.",
                'studio_instruction' => "CRITICAL STILL-LIFE DISPLAY: The ornate bridal nath rests delicately on the plush micro-velvet jewelry cushion with its delicate pearl chain trailing softly across the velvet pile.",
                'copy_context' => "Indian Bridal Traditional Nath (Nose Ring)"
            ],
            'hath_phool' => [
                'name' => 'Hath Phool / Hath Panja (Hand Chain)',
                'type' => 'jewellery',
                'keywords' => ['hath', 'panja', 'phool', 'hand ornament'],
                'sku_prefixes' => ['HP'],
                'model_instruction' => "CRITICAL ANATOMICAL PLACEMENT: Worn on the back of the hand, with the bracelet around the wrist and rings attached to the fingers. Close-up of graceful mehendi-adorned hand.",
                'studio_instruction' => "CRITICAL STILL-LIFE DISPLAY: Laid flat and elegant across the plush micro-velvet cushion, clearly displaying the wrist bracelet, ornate central floral medallion, and connecting finger rings.",
                'copy_context' => "Indian Bridal Hath Phool & Hath Panja"
            ],
            'bangles_bracelets' => [
                'name' => 'Bangles, Bracelets & Kadas',
                'type' => 'jewellery',
                'keywords' => ['bangle', 'bangles', 'bracelet', 'kada', 'kadas', 'chuda'],
                'sku_prefixes' => ['B', 'BA', 'BR'],
                'model_instruction' => "CRITICAL ANATOMICAL PLACEMENT: Worn around the wrists and forearms. Hand and wrist pose highlighting the bangles.",
                'studio_instruction' => "CRITICAL STILL-LIFE DISPLAY: The bangles / kadas rest at an elegant 45-degree angle on the plush micro-velvet cushion or nestled amidst dark zari silk fabric folds, displaying the intricate carving and stone setting.",
                'copy_context' => "Indian Bridal Bangles, Bracelets & Kadas"
            ],
            'baju_bandh' => [
                'name' => 'Baju Bandh (Armlet)',
                'type' => 'jewellery',
                'keywords' => ['baju', 'bandh', 'armlet', 'vanki'],
                'sku_prefixes' => ['BB'],
                'model_instruction' => "CRITICAL ANATOMICAL PLACEMENT: Worn around the upper arm / bicep over blouse sleeve or bare arm.",
                'studio_instruction' => "CRITICAL STILL-LIFE DISPLAY: Laid in an elegant curved contour across the plush micro-velvet cushion, showcasing the ornate armlet centerpiece and dangling latkans.",
                'copy_context' => "Indian Traditional Baju Bandh (Armlet)"
            ],
            'payal_pagpan' => [
                'name' => 'Payal / Pag Pan (Anklets)',
                'type' => 'jewellery',
                'keywords' => ['payal', 'pag', 'pan', 'anklet', 'anklets'],
                'sku_prefixes' => ['P', 'PAYAL'],
                'model_instruction' => "CRITICAL ANATOMICAL PLACEMENT: Worn around the ankles and feet over elegant traditional juttis/heels and lehenga border.",
                'studio_instruction' => "CRITICAL STILL-LIFE DISPLAY: The pair of payals / anklets undulate gracefully across the plush micro-velvet cushion, highlighting the delicate links and ghungroo bells.",
                'copy_context' => "Indian Bridal Payal & Pag Pan (Anklet)"
            ],
            'rings' => [
                'name' => 'Finger Rings',
                'type' => 'jewellery',
                'keywords' => ['ring', 'rings'],
                'sku_prefixes' => ['R'],
                'model_instruction' => "CRITICAL ANATOMICAL PLACEMENT: Worn on the model's fingers. Close-up hand gesture showing the ring setting.",
                'studio_instruction' => "CRITICAL STILL-LIFE DISPLAY: Positioned at an angled orientation atop the plush micro-velvet cushion, catching brilliant specular glints on the faceted stone setting.",
                'copy_context' => "Statement Finger Ring"
            ],
            'mala' => [
                'name' => 'Mala / Long Chains',
                'type' => 'jewellery',
                'keywords' => ['mala', 'long chain', 'pearl mala'],
                'sku_prefixes' => ['ML'],
                'model_instruction' => "CRITICAL ANATOMICAL PLACEMENT: Worn as a long layered mala draped around the neck down to the chest or torso.",
                'studio_instruction' => "CRITICAL STILL-LIFE DISPLAY: Draped in elegant concentric flowing loops across the plush micro-velvet cushion and rich dark brocade silk drapery.",
                'copy_context' => "Indian Royal Mala & Long Necklace"
            ],
            'pendant_set' => [
                'name' => 'Pendant Sets',
                'type' => 'jewellery',
                'keywords' => ['pendant'],
                'sku_prefixes' => ['PS'],
                'model_instruction' => "CRITICAL ANATOMICAL PLACEMENT: Worn on a fine chain around the neck with the pendant resting on the collarbone or upper chest.",
                'studio_instruction' => "CRITICAL STILL-LIFE DISPLAY: The pendant is centered prominently on the plush micro-velvet cushion with its chain looping gracefully, flanked symmetrically by the matching earrings.",
                'copy_context' => "Pendant Set with Matching Earrings"
            ],
            'bridal_set' => [
                'name' => 'Bridal Jewellery Complete Set',
                'type' => 'jewellery',
                'keywords' => ['bridal jewellery'],
                'sku_prefixes' => ['BS', 'SET'],
                'model_instruction' => "CRITICAL STYLING: Complete bridal look wearing the choker, long necklace, earrings, and mathapatti.",
                'studio_instruction' => "CRITICAL STILL-LIFE DISPLAY: Arranged in an opulent royal wedding trousseau layout across coordinated plush micro-velvet display cushions, showcasing the choker, long necklace, earrings, and tikka harmoniously.",
                'copy_context' => "Complete Luxury Bridal Jewellery Set"
            ],
            'lehenga_choli' => [
                'name' => 'Lehenga Choli',
                'type' => 'garment',
                'keywords' => ['lehenga', 'choli', 'ghagra'],
                'sku_prefixes' => ['YNL', 'L'],
                'model_instruction' => "Full body or 3/4 fashion portrait of the model wearing the flared lehenga skirt, tailored choli blouse, and draped dupatta.",
                'studio_instruction' => "Displayed as an exquisite couture bridal ensemble artfully draped on an invisible ghost mannequin against a dark royal palace backdrop, highlighting the zardozi borders and flared skirt.",
                'copy_context' => "Indian Designer Bridal Lehenga Choli"
            ],
            'evening_gowns' => [
                'name' => 'Evening & Trail Gowns',
                'type' => 'garment',
                'keywords' => ['gown', 'trail', 'infinity'],
                'sku_prefixes' => ['GOWN', 'YNG'],
                'model_instruction' => "Full length fashion portrait showing the silhouette, gown flair, and neckline in a luxury hall.",
                'studio_instruction' => "Displayed as a high-fashion designer gown with elegant flowing silhouette against a dark moody boutique setting.",
                'copy_context' => "Luxury Evening & Trail Gown"
            ],
            'designer_blouse' => [
                'name' => 'Designer Blouses',
                'type' => 'garment',
                'keywords' => ['blouse'],
                'sku_prefixes' => ['BL'],
                'model_instruction' => "Close-up or mid-torso portrait showing the front or back neckline embroidery and sleeves of the blouse.",
                'studio_instruction' => "Displayed on a couture bust form against a rich textured silk background, highlighting the neckline zari embroidery and sleeve work.",
                'copy_context' => "Designer Embroidered Saree / Lehenga Blouse"
            ],
            'indo_western' => [
                'name' => 'Indo Western Outfits',
                'type' => 'garment',
                'keywords' => ['indo', 'western', 'fusion', 'co-ord'],
                'sku_prefixes' => ['IW'],
                'model_instruction' => "Chic fashion editorial portrait showing modern Indo-western silhouette and drapes.",
                'studio_instruction' => "Displayed as a contemporary designer fusion outfit with crisp architectural drapes.",
                'copy_context' => "Contemporary Indo Western Fusion Designer Outfit"
            ],
            'kurtis' => [
                'name' => 'Kurtis & Tunics',
                'type' => 'garment',
                'keywords' => ['kurti', 'tunic'],
                'sku_prefixes' => ['KT'],
                'model_instruction' => "Graceful fashion portrait showing the kurti cut, embroidery, and pairing.",
                'studio_instruction' => "Displayed as an elegant designer ethnic kurti with crisp drape against a warm boutique setting.",
                'copy_context' => "Designer Ethnic Kurti & Tunic"
            ]
        ];
    }

    /**
     * Build strict e-commerce isolation rules to prevent AI from hallucinating unlisted jewelry items
     * (e.g. preventing necklaces/earrings from appearing when selling a Borla, or vice versa).
     */
    public static function getStrictJewelryIsolationRule($catKey)
    {
        $header = "\n==================== CRITICAL STRICT HERO PRODUCT ISOLATION (ZERO EXTRA JEWELRY) ====================\n" .
                  "STRICT E-COMMERCE CATALOG MANDATE: This listing is strictly to sell ONE SINGLE JEWELRY PRODUCT.\n" .
                  "DO NOT GENERATE ANY ARTIFICIAL AI SUGGESTIONS, STYLED ACCESSORIES, OR UNLISTED JEWELRY PIECES ON THE MODEL!\n" .
                  "THE MODEL MUST ONLY WEAR THE HERO PRODUCT PROVIDED IN THE ATTACHED PRODUCT PHOTOGRAPHS.\n";

        switch ($catKey) {
            case 'borla_with_sari':
            case 'borla':
                return $header .
                    "- ZERO NECKLACE / ZERO CHOKER / BARE THROAT: The model's neck, throat, collarbones, and upper chest MUST BE 100% COMPLETELY BARE SKIN WITH ABSOLUTELY ZERO JEWELRY! STRICTLY NO necklace, NO choker, NO collar, NO haar, NO pendant, NO chains around the neck! Do NOT draw or suggest any jewelry on the neck!\n" .
                    "- ZERO EARRINGS / BARE EARS: The model's ears and earlobes MUST BE 100% COMPLETELY BARE WITH ZERO JEWELRY! STRICTLY NO earrings, NO studs, NO jhumkas, NO chandbalis, NO danglers, NO ear cuffs! Do NOT draw any earrings on the ears!\n" .
                    "- ZERO NOSE JEWELRY: The model's nose MUST BE 100% COMPLETELY BARE. STRICTLY NO nath, NO nose ring, NO nose pin.\n" .
                    "- ZERO HAND/WRIST JEWELRY: STRICTLY NO bangles, NO bracelets, NO rings.\n" .
                    "- THE ONLY JEWELRY ITEM ON THE ENTIRE MODEL MUST BE THE TARGET BORLA HEADPIECE (and its side sari hair chains if Borla with Sari). All other body parts (neck, ears, nose, wrists, fingers) MUST BE COMPLETELY BARE.\n";

            case 'kamar_patta':
                return $header .
                    "- ZERO NECKLACE / ZERO CHOKER: The model's neck and chest MUST BE 100% COMPLETELY BARE. STRICTLY NO necklace, NO choker, NO collar!\n" .
                    "- ZERO EARRINGS: The model's ears MUST BE 100% COMPLETELY BARE with ZERO earrings.\n" .
                    "- ZERO FOREHEAD JEWELRY: STRICTLY NO maang tikka, NO mathapatti, NO borla.\n" .
                    "- ZERO NOSE JEWELRY: STRICTLY NO nath.\n" .
                    "- THE ONLY JEWELRY ITEM ON THE ENTIRE MODEL MUST BE THE KAMAR PATTA (WAIST BELT) worn around the waist and hips!\n";

            case 'earrings':
                return $header .
                    "- ZERO NECKLACE / ZERO CHOKER / BARE NECK: The model's neck, throat, and collarbones MUST BE 100% COMPLETELY BARE SKIN with zero jewelry. STRICTLY NO necklace, NO choker, NO pendant!\n" .
                    "- ZERO FOREHEAD JEWELRY: STRICTLY NO maang tikka, NO mathapatti, NO borla.\n" .
                    "- ZERO NOSE JEWELRY: STRICTLY NO nath.\n" .
                    "- THE ONLY JEWELRY ITEM ON THE ENTIRE MODEL MUST BE THE EARRINGS worn on the earlobes!\n";

            case 'necklace_set':
                return $header .
                    "- ZERO FOREHEAD JEWELRY: STRICTLY NO maang tikka, NO mathapatti, NO borla.\n" .
                    "- ZERO NOSE JEWELRY: STRICTLY NO nath.\n" .
                    "- ZERO WAIST JEWELRY: STRICTLY NO kamar patta.\n" .
                    "- Only the necklace around the neck (and matching earrings on earlobes IF and only if matching earrings are included in the product's reference photos).\n";

            case 'damini_mathapatti':
                return $header .
                    "- ZERO NECKLACE / ZERO CHOKER: The model's neck and chest MUST BE 100% COMPLETELY BARE SKIN. STRICTLY NO necklace, NO choker, NO pendant.\n" .
                    "- ZERO EARRINGS: The model's ears MUST BE 100% COMPLETELY BARE with zero earrings.\n" .
                    "- ZERO NOSE JEWELRY: STRICTLY NO nath.\n" .
                    "- THE ONLY JEWELRY ITEM ON THE ENTIRE MODEL MUST BE THE DAMINI / MATHAPATTI on the forehead and hairline!\n";

            case 'nath':
                return $header .
                    "- ZERO NECKLACE: The model's neck MUST BE 100% COMPLETELY BARE SKIN with zero necklace.\n" .
                    "- ZERO EARRINGS: The model's ears MUST BE 100% COMPLETELY BARE with zero earrings.\n" .
                    "- ZERO HEAD/FOREHEAD JEWELRY: STRICTLY NO tikka, NO mathapatti, NO borla.\n" .
                    "- THE ONLY JEWELRY ITEM ON THE ENTIRE MODEL MUST BE THE NATH on the nose!\n";

            case 'hath_phool':
                return $header .
                    "- ZERO NECKLACE, ZERO EARRINGS, ZERO HEAD JEWELRY: The neck, ears, and forehead MUST BE 100% COMPLETELY BARE of jewelry.\n" .
                    "- THE ONLY JEWELRY ITEM ON THE ENTIRE MODEL MUST BE THE HATH PHOOL on the back of the hand and wrist!\n";

            case 'baju_bandh':
                return $header .
                    "- ZERO NECKLACE, ZERO EARRINGS, ZERO HEAD JEWELRY: The neck, ears, and forehead MUST BE 100% COMPLETELY BARE of jewelry.\n" .
                    "- THE ONLY JEWELRY ITEM ON THE ENTIRE MODEL MUST BE THE BAJU BANDH on the upper arm!\n";

            case 'bangles_bracelets':
                return $header .
                    "- ZERO NECKLACE, ZERO EARRINGS, ZERO HEAD JEWELRY: The neck, ears, and forehead MUST BE 100% COMPLETELY BARE of jewelry.\n" .
                    "- THE ONLY JEWELRY ITEM ON THE ENTIRE MODEL MUST BE THE BANGLES / BRACELETS on the wrist!\n";

            case 'payal_pagpan':
                return $header .
                    "- ZERO NECKLACE, ZERO EARRINGS, ZERO HEAD JEWELRY.\n" .
                    "- THE ONLY JEWELRY ITEM ON THE ENTIRE MODEL MUST BE THE ANKLETS on the ankles and feet!\n";

            case 'rings':
                return $header .
                    "- ZERO NECKLACE, ZERO EARRINGS, ZERO HEAD JEWELRY.\n" .
                    "- THE ONLY JEWELRY ITEM ON THE ENTIRE MODEL MUST BE THE RING on the finger!\n";

            default:
                return $header .
                    "- ONLY the exact target hero product from the reference photos is worn. All other areas of the body MUST be completely bare of unlisted jewelry items.\n";
        }
    }

    /**
     * Auto-detect category key for a product
     */
    public function detectCategoryKey($product)
    {
        $code = strtoupper(trim($product['code'] ?? ($product['product_code'] ?? ($product['gproduct_code'] ?? ''))));
        $name = strtolower($product['name'] ?? ($product['product_name'] ?? ($product['gproduct_name'] ?? '')));
        $catName = strtolower($product['category_name'] ?? '');
        $subcatName = strtolower($product['subcategory_name'] ?? '');
        $type = $product['type'] ?? 'jewellery';

        $catId = (int)($product['category_id'] ?? ($product['categories_id'] ?? 0));
        $subcatId = (int)($product['subcategory_id'] ?? ($product['subcat_id'] ?? 0));

        // 1. Check Borla (Category 11 / Subcat 53, SKU BO, or Borla keywords)
        $isBorla = ($catId === 11 || $subcatId === 53 || str_starts_with($code, 'BO') || str_contains($name, 'borla') || str_contains($catName, 'borla') || str_contains($subcatName, 'borla'));
        if ($isBorla) {
            if (str_contains($name, 'chain') || str_contains($name, 'sari') || str_contains($name, 'saari') || str_contains($name, 'sar') || str_contains($name, 'damini') || str_contains($name, 'mathapatti') || str_contains($name, 'sheeshphool') || str_contains($name, 'head chain') || str_contains($name, 'side')) {
                return 'borla_with_sari';
            }
            return 'borla';
        }

        // 2. Check SKU prefixes
        if (str_starts_with($code, 'KP')) return 'kamar_patta';
        if (str_starts_with($code, 'NATH')) return 'nath';
        if (str_starts_with($code, 'HP')) return 'hath_phool';
        if (str_starts_with($code, 'BB')) return 'baju_bandh';
        if (str_starts_with($code, 'DP')) return 'damini_mathapatti';
        if (str_starts_with($code, 'ER') || str_starts_with($code, 'E')) return 'earrings';
        if (str_starts_with($code, 'YNL')) return 'lehenga_choli';
        if (str_starts_with($code, 'YNG') || str_starts_with($code, 'GOWN')) return 'evening_gowns';

        // 3. Check Kamar Patta keywords
        if (str_contains($catName, 'kamar') || str_contains($subcatName, 'kamar') || str_contains($name, 'kamar') || str_contains($name, 'waist belt')) {
            return 'kamar_patta';
        }

        // 3. Match against category definitions
        $defs = self::getCategoryDefinitions();
        foreach ($defs as $key => $def) {
            foreach ($def['keywords'] as $kw) {
                if (str_contains($catName, $kw) || str_contains($subcatName, $kw) || str_contains($name, $kw)) {
                    return $key;
                }
            }
        }

        return ($type === 'garment') ? 'lehenga_choli' : 'necklace_set';
    }

    /**
     * Render the All in One AI Writer dashboard
     */
    public function index()
    {
        $secretsFile = __DIR__ . '/../Config/secrets.php';
        $hasApiKey = false;
        $hasGemini = false;
        $hasOpenAi = false;
        if (file_exists($secretsFile)) {
            $sec = include($secretsFile);
            $hasGemini = !empty($sec['GEMINI_API_KEY']);
            $hasOpenAi = !empty($sec['OPENAI_API_KEY']);
            $hasApiKey = $hasGemini || $hasOpenAi;
        }

        // Available reference model faces
        $availableModels = [];
        for ($i = 1; $i <= 10; $i++) {
            $rel = "assets/models/model_$i.png";
            $full = __DIR__ . '/../' . $rel;
            if (file_exists($full)) {
                $availableModels[] = [
                    'id' => $i,
                    'file' => "model_$i.png",
                    'title' => "Model $i",
                    'url' => $rel . '?v=' . filemtime($full)
                ];
            }
        }

        $productModel = new ProductModel();
        $categories = $productModel->getCategories();
        $categoryDefinitions = self::getCategoryDefinitions();
        $aiWriterSettings = self::getAiWriterSettings();

        $this->view('ai_writer/index', [
            'hasApiKey' => $hasApiKey,
            'hasGemini' => $hasGemini,
            'hasOpenAi' => $hasOpenAi,
            'availableModels' => $availableModels,
            'categories' => $categories,
            'categoryDefinitions' => $categoryDefinitions,
            'aiWriterSettings' => $aiWriterSettings
        ]);
    }

    /**
     * Parse and lookup products by SKUs (space, comma, or newline separated) or Category Filter
     */
    public function lookup()
    {
        $rawInput = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $skuInput = trim($rawInput['skus'] ?? ($_GET['skus'] ?? ''));
        $catFilter = trim($rawInput['category_filter'] ?? ($_GET['category_filter'] ?? ''));
        $uploadDate = trim($rawInput['upload_date'] ?? ($_GET['upload_date'] ?? ''));

        if ($uploadDate === 'saturday') {
            $uploadDate = '2026-10-10';
        }

        if (empty($skuInput) && (empty($catFilter) || $catFilter === 'all') && empty($uploadDate)) {
            $this->json(['success' => false, 'error' => 'Please enter SKUs, select a category, or choose an upload date.'], 400);
            return;
        }

        if (!$this->db) {
            $this->json(['success' => false, 'error' => 'Database connection failed.'], 500);
            return;
        }

        $jewelWhere = ["1=1"];
        $garmentWhere = ["1=1"];
        $skus = [];

        // Filter by Upload Date if provided
        if (!empty($uploadDate)) {
            $escDate = mysqli_real_escape_string($this->db, $uploadDate);
            $jewelWhere[] = "DATE(date_added) = '$escDate'";
            $garmentWhere[] = "DATE(date_added) = '$escDate'";
        }

        // Parse SKUs if provided
        if (!empty($skuInput)) {
            $rawSkus = preg_split('/[\r\n,\s]+/', $skuInput);
            $skus = array_values(array_unique(array_filter(array_map('trim', $rawSkus))));

            if (!empty($skus)) {
                $escapedSkus = array_map(function($s) {
                    return "'" . mysqli_real_escape_string($this->db, $s) . "'";
                }, $skus);
                $inList = implode(',', $escapedSkus);

                $jewelWhere[] = "(product_code IN ($inList) OR TRIM(product_code) IN ($inList))";
                $garmentWhere[] = "(gproduct_code IN ($inList) OR TRIM(gproduct_code) IN ($inList))";
            }
        }

        // Apply Category Filter if selected
        if (!empty($catFilter) && $catFilter !== 'all') {
            $clause = $this->getCategorySqlClause($catFilter);
            if ($clause) {
                $jewelWhere[] = $clause['jewel'];
                $garmentWhere[] = $clause['garment'];
            }
        }

        $jewelWhereSql = implode(" AND ", $jewelWhere);
        $garmentWhereSql = implode(" AND ", $garmentWhere);

        $customLimit = (int)($rawInput['limit'] ?? ($_GET['limit'] ?? 0));
        if ($customLimit > 0) {
            $limitClause = "LIMIT " . min(300, $customLimit);
        } else {
            $limitClause = (empty($skus) && empty($uploadDate)) ? "LIMIT 25" : "LIMIT 100";
        }

        $jewelQuery = "
            SELECT 
                product_id as id,
                product_name as name,
                product_code as code,
                'jewellery' as type,
                short_desc,
                product_desc as description,
                categories_id as category_id,
                subcat_id as subcategory_id,
                product_image as main_image
            FROM product 
            WHERE $jewelWhereSql
            ORDER BY product_id DESC
            $limitClause
        ";

        $garmentQuery = "
            SELECT 
                gproduct_id as id,
                gproduct_name as name,
                gproduct_code as code,
                'garment' as type,
                short_desc,
                gproduct_desc as description,
                garment_id as category_id,
                product_for as subcategory_id,
                gproduct_image as main_image
            FROM garment_product 
            WHERE $garmentWhereSql
            ORDER BY gproduct_id DESC
            $limitClause
        ";

        $products = [];
        $foundSkusMap = [];
        $productModel = new ProductModel();
        $defs = self::getCategoryDefinitions();

        // Fetch Jewellery
        $jRes = mysqli_query($this->db, $jewelQuery);
        if ($jRes) {
            while ($row = mysqli_fetch_assoc($jRes)) {
                $row['category_name'] = $this->resolveCategoryName('jewellery', $row['category_id'], $row['subcategory_id']);
                $detectedKey = $this->detectCategoryKey($row);
                $row['category_key'] = $detectedKey;
                $row['category_label'] = $defs[$detectedKey]['name'] ?? 'Jewellery';
                $row['images'] = $this->resolveProductImages((int)$row['id'], 'jewellery', $row['code'], $row['main_image'], $productModel);
                $row['primary_image_url'] = !empty($row['images'][0]['url']) ? $row['images'][0]['url'] : '';
                $products[] = $row;
                $foundSkusMap[strtoupper(trim($row['code']))] = true;
            }
        }

        // Fetch Garments
        $gRes = mysqli_query($this->db, $garmentQuery);
        if ($gRes) {
            while ($row = mysqli_fetch_assoc($gRes)) {
                $row['category_name'] = $this->resolveCategoryName('garment', $row['category_id'], $row['subcategory_id']);
                $detectedKey = $this->detectCategoryKey($row);
                $row['category_key'] = $detectedKey;
                $row['category_label'] = $defs[$detectedKey]['name'] ?? 'Apparel';
                $row['images'] = $this->resolveProductImages((int)$row['id'], 'garment', $row['code'], $row['main_image'], $productModel);
                $row['primary_image_url'] = !empty($row['images'][0]['url']) ? $row['images'][0]['url'] : '';
                $products[] = $row;
                $foundSkusMap[strtoupper(trim($row['code']))] = true;
            }
        }

        // Identify missing SKUs
        $missingSkus = [];
        foreach ($skus as $s) {
            if (!isset($foundSkusMap[strtoupper($s)])) {
                $missingSkus[] = $s;
            }
        }

        $this->json([
            'success' => true,
            'total_found' => count($products),
            'products' => $products,
            'missing_skus' => $missingSkus,
            'searched_count' => count($skus)
        ]);
    }

    /**
     * Generate Content (Title, Short Description, Long Description)
     */
    public function generateContent()
    {
        $rawInput = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($rawInput['id'] ?? ($_GET['id'] ?? 0));
        $type = ($rawInput['type'] ?? ($_GET['type'] ?? 'jewellery')) === 'garment' ? 'garment' : 'jewellery';
        $provider = strtolower($rawInput['provider'] ?? ($_GET['provider'] ?? ''));
        $catKeyParam = trim($rawInput['category_key'] ?? '');

        if (!$id) {
            $this->json(['error' => 'Product ID is required.'], 400);
            return;
        }

        $secrets = include(__DIR__ . '/../Config/secrets.php');
        $geminiApiKey = $secrets['GEMINI_API_KEY'] ?? '';
        $openAiApiKey = $secrets['OPENAI_API_KEY'] ?? '';

        if (empty($provider)) {
            $provider = !empty($geminiApiKey) ? 'gemini' : 'openai';
        }

        if ($provider === 'openai' && empty($openAiApiKey)) {
            if (!empty($geminiApiKey)) $provider = 'gemini';
            else {
                $this->json(['error' => 'OpenAI API key is missing in Config/secrets.php.'], 400);
                return;
            }
        } elseif ($provider === 'gemini' && empty($geminiApiKey)) {
            if (!empty($openAiApiKey)) $provider = 'openai';
            else {
                $this->json(['error' => 'Gemini API key is missing in Config/secrets.php.'], 400);
                return;
            }
        }

        // Fetch product & images
        $productModel = new ProductModel();
        $product = $productModel->getProductById($id, $type);
        if (!$product) {
            $this->json(['error' => 'Product not found.'], 404);
            return;
        }

        $defs = self::getCategoryDefinitions();
        $catKey = (!empty($catKeyParam) && isset($defs[$catKeyParam])) ? $catKeyParam : $this->detectCategoryKey($product);
        $catInfo = $defs[$catKey] ?? $defs['necklace_set'];

        $code = $product['code'] ?? ($product['product_code'] ?? ($product['gproduct_code'] ?? ''));
        $currentName = $product['name'] ?? ($product['product_name'] ?? ($product['gproduct_name'] ?? ''));

        // Load image binary for Vision analysis (honoring AI Writer Settings)
        $images = $productModel->getProductImages($id, $type);
        $aiSettings = self::getAiWriterSettings();
        $analysisMode = $rawInput['analysis_mode'] ?? ($_GET['analysis_mode'] ?? ($aiSettings['image_analysis_mode'] ?? 'default'));
        $maxImages = (int)($rawInput['max_images'] ?? ($_GET['max_images'] ?? ($aiSettings['max_images'] ?? 10)));
        $maxImages = max(1, min(10, $maxImages));
        $includeContentMulti = !empty($aiSettings['include_content_analysis']);

        $productImageParts = [];
        if ($analysisMode === 'multi' && $includeContentMulti) {
            // Prioritize authentic real product photos over previous AI generated images
            usort($images, function($a, $b) {
                $aAi = (strpos($a['img_name'] ?? '', '_ai_') !== false);
                $bAi = (strpos($b['img_name'] ?? '', '_ai_') !== false);
                if ($aAi === $bAi) return 0;
                return $aAi ? 1 : -1;
            });

            $loadedCount = 0;
            foreach ($images as $img) {
                if (empty($img['img_name'])) continue;
                if ($loadedCount >= $maxImages) break;

                $mType = 'image/jpeg';
                $bin = $this->loadBinaryImage($img['img_name'], $mType);
                if (!empty($bin)) {
                    $bin = $this->optimizeImageBinary($bin, $mType);
                    $productImageParts[] = [
                        'inlineData' => [
                            'mimeType' => $mType,
                            'data' => base64_encode($bin)
                        ]
                    ];
                    $loadedCount++;
                }
            }
        } else {
            $imgBinary = null;
            $mimeType = 'image/jpeg';
            if (!empty($images[0]['img_name'])) {
                $imgBinary = $this->loadBinaryImage($images[0]['img_name'], $mimeType);
            }
            if (!empty($imgBinary)) {
                $productImageParts[] = [
                    'inlineData' => [
                        'mimeType' => $mimeType,
                        'data' => base64_encode($imgBinary)
                    ]
                ];
            }
        }

        $hasImage = !empty($productImageParts);
        $photoNotice = "";
        if (count($productImageParts) > 1) {
            $photoNotice = " and the " . count($productImageParts) . " attached visual product photographs showing different angles and craftsmanship details";
        } elseif ($hasImage) {
            $photoNotice = " and attached visual photograph";
        }

        // Crafted prompt adhering strictly to character boundaries & specific category
        $prompt = "You are an expert luxury Indian fashion and bridal jewellery copywriter for Srishringarr Fashion Studio, Mumbai.\n" .
                  "Analyze the product details" . $photoNotice . ".\n\n" .
                  "Exact Category & Type: " . $catInfo['copy_context'] . "\n" .
                  "SKU Code: " . $code . "\n" .
                  "Current Working Title: " . $currentName . "\n\n" .
                  "Generate high-converting e-commerce copy formatted as a valid JSON object with EXACTLY three keys:\n" .
                  "1. \"name\": A clear, descriptive, and elegant title (10 to 14 words, MAXIMUM 95 characters). Use simple, natural English. Accurately state color, material/stones, and specify the product type (" . $catInfo['name'] . "). Do NOT confuse it with another jewelry type.\n" .
                  "2. \"short_description\": A concise highlight summary (20 to 30 words, MAXIMUM 240 characters) ideal for search cards and quick previews.\n" .
                  "3. \"description\": A rich e-commerce description (75 to 100 words, MAXIMUM 850 characters). Begin with a warm paragraph celebrating artisanal craftsmanship and styling versatility (weddings, receptions, sangeet, festive parties). Follow with 'Key Features:' and 3 to 4 concise bullet points starting with '• ' (e.g., '• Fabric/Material: ...', '• Work/Embroidery: ...', '• Occasion: ...'). Do NOT use markdown asterisks (no '**').\n\n" .
                  "Return ONLY raw JSON with keys \"name\", \"short_description\", and \"description\".";

        $resultJson = null;
        $promptTokens = 0;
        $candidateTokens = 0;
        $totalTokens = 0;

        if ($provider === 'openai') {
            $userContent = [['type' => 'text', 'text' => $prompt]];
            foreach ($productImageParts as $pPart) {
                $userContent[] = [
                    'type' => 'image_url',
                    'image_url' => [
                        'url' => "data:{$pPart['inlineData']['mimeType']};base64,{$pPart['inlineData']['data']}",
                        'detail' => 'low'
                    ]
                ];
            }

            $payload = json_encode([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert Indian luxury bridal fashion and jewelry copywriter for Srishringarr. Always output valid parseable JSON.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $userContent
                    ]
                ],
                'response_format' => ['type' => 'json_object'],
                'temperature' => 0.4,
                'max_tokens' => 1200
            ]);

            $ch = curl_init('https://api.openai.com/v1/chat/completions');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $openAiApiKey
                ],
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_TIMEOUT => 40,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            @curl_close($ch);

            if ($httpCode !== 200) {
                $this->json(['error' => 'OpenAI API request failed (HTTP ' . $httpCode . '): ' . $response], 500);
                return;
            }

            $decoded = json_decode($response, true);
            $rawText = $decoded['choices'][0]['message']['content'] ?? '';
            $resultJson = json_decode($rawText, true);

            $promptTokens = (int)($decoded['usage']['prompt_tokens'] ?? 0);
            $candidateTokens = (int)($decoded['usage']['completion_tokens'] ?? 0);
            $totalTokens = (int)($decoded['usage']['total_tokens'] ?? 0);

        } else {
            // Google Gemini (with zero-latency thinkingBudget: 0 and 4096 output tokens)
            $parts = [['text' => $prompt]];
            foreach ($productImageParts as $pPart) {
                $parts[] = $pPart;
            }

            $models = ['gemini-flash-latest', 'gemini-2.5-flash'];
            $success = false;
            $lastError = 'Gemini content generation failed.';

            foreach ($models as $modelName) {
                $configsToTry = [
                    [
                        'responseMimeType' => 'application/json',
                        'temperature' => 0.4,
                        'maxOutputTokens' => 4096,
                        'thinkingConfig' => ['thinkingBudget' => 0]
                    ],
                    [
                        'responseMimeType' => 'application/json',
                        'temperature' => 0.4,
                        'maxOutputTokens' => 4096
                    ]
                ];

                foreach ($configsToTry as $genConfig) {
                    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key=" . $geminiApiKey;
                    $payload = json_encode([
                        'contents' => [
                            ['parts' => $parts]
                        ],
                        'generationConfig' => $genConfig
                    ]);

                    $ch = curl_init($url);
                    curl_setopt_array($ch, [
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_POST => true,
                        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                        CURLOPT_POSTFIELDS => $payload,
                        CURLOPT_TIMEOUT => 35,
                        CURLOPT_SSL_VERIFYPEER => false,
                    ]);

                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    @curl_close($ch);

                    if ($httpCode === 200) {
                        $decoded = json_decode($response, true);
                        $rawText = '';
                        if (isset($decoded['candidates'][0]['content']['parts'])) {
                            foreach ($decoded['candidates'][0]['content']['parts'] as $p) {
                                if (!empty($p['text'])) {
                                    $rawText .= $p['text'];
                                }
                            }
                        }

                        $cleanText = trim(preg_replace('/^```json|```$/', '', trim($rawText)));
                        $parsed = json_decode($cleanText, true);
                        if (!is_array($parsed) || empty($parsed['name'])) {
                            if (preg_match('/\{[\s\S]*\}/', $cleanText, $matches)) {
                                $parsed = json_decode($matches[0], true);
                            }
                        }

                        if (is_array($parsed) && !empty($parsed['name'])) {
                            $resultJson = $parsed;
                            $promptTokens = (int)($decoded['usageMetadata']['promptTokenCount'] ?? 0);
                            $candidateTokens = (int)($decoded['usageMetadata']['candidatesTokenCount'] ?? 0);
                            $totalTokens = (int)($decoded['usageMetadata']['totalTokenCount'] ?? 0);
                            $success = true;
                            break 2;
                        }
                    } else {
                        $errObj = json_decode($response, true);
                        $lastError = $errObj['error']['message'] ?? "HTTP $httpCode: $response";
                    }
                }
            }

            if (!$success && empty($resultJson)) {
                $this->json(['error' => 'Gemini generation error: ' . $lastError], 500);
                return;
            }
        }

        if (!is_array($resultJson) || empty($resultJson['name'])) {
            $this->json(['error' => 'Could not parse AI JSON output.'], 500);
            return;
        }

        $generatedName = trim($resultJson['name'] ?? '');
        $generatedShort = trim($resultJson['short_description'] ?? '');
        $generatedDesc = trim($resultJson['description'] ?? '');

        // Safe truncation according to DB schema
        if (mb_strlen($generatedName) > 95) {
            $generatedName = mb_substr($generatedName, 0, 95);
        }
        if (mb_strlen($generatedShort) > 245) {
            $generatedShort = mb_substr($generatedShort, 0, 245);
        }

        // Log to ai_analytics
        if ($this->db) {
            $costEstimate = max(0.01, (($promptTokens * 0.000000075) + ($candidateTokens * 0.0000003)) * 86);
            $genOutput = json_encode(['name' => $generatedName, 'short_desc' => $generatedShort, 'desc' => $generatedDesc]);
            $opType = 'all_in_one_content';
            $numImg = $hasImage ? 1 : 0;
            $website = 'srishringarr';

            @mysqli_query($this->db, "ALTER TABLE ai_analytics ADD COLUMN operation_type VARCHAR(50) DEFAULT 'image'");
            @mysqli_query($this->db, "ALTER TABLE ai_analytics ADD COLUMN generated_output TEXT NULL");
            @mysqli_query($this->db, "ALTER TABLE ai_analytics ADD COLUMN website VARCHAR(100) DEFAULT 'srishringarr'");

            $stmt = $this->db->prepare("INSERT INTO ai_analytics (product_id, product_type, operation_type, prompt_text, generated_output, num_images, prompt_tokens, candidate_tokens, total_tokens, cost_estimate, website) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("issssiiiids", $id, $type, $opType, $prompt, $genOutput, $numImg, $promptTokens, $candidateTokens, $totalTokens, $costEstimate, $website);
                $stmt->execute();
                $stmt->close();
            }
        }

        $this->json([
            'success' => true,
            'id' => $id,
            'type' => $type,
            'category_key' => $catKey,
            'name' => $generatedName,
            'short_description' => $generatedShort,
            'description' => $generatedDesc,
            'analysis_mode' => $analysisMode,
            'images_analyzed_count' => count($productImageParts),
            'tokens' => $totalTokens
        ]);
    }

    /**
     * Generate Photoshoot Images (With Model and Without Model) with Category-Specific Placement
     */
    public function generateImage()
    {
        $rawInput = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($rawInput['id'] ?? ($_GET['id'] ?? 0));
        $type = ($rawInput['type'] ?? ($_GET['type'] ?? 'jewellery')) === 'garment' ? 'garment' : 'jewellery';
        $mode = $rawInput['mode'] ?? ($_GET['mode'] ?? 'both'); // 'with_model', 'without_model', or 'both'
        $faceRef = trim($rawInput['face_reference'] ?? '');
        $customNote = trim($rawInput['custom_note'] ?? '');
        $catKeyParam = trim($rawInput['category_key'] ?? '');

        if (!$id) {
            $this->json(['error' => 'Product ID is required.'], 400);
            return;
        }

        $secrets = include(__DIR__ . '/../Config/secrets.php');
        $apiKey = $secrets['GEMINI_API_KEY'] ?? '';
        if (empty($apiKey)) {
            $this->json(['error' => 'Gemini API key is not configured in Config/secrets.php.'], 400);
            return;
        }

        $productModel = new ProductModel();
        $product = $productModel->getProductById($id, $type);
        if (!$product) {
            $this->json(['error' => 'Product not found.'], 404);
            return;
        }

        $defs = self::getCategoryDefinitions();
        $catKey = (!empty($catKeyParam) && isset($defs[$catKeyParam])) ? $catKeyParam : $this->detectCategoryKey($product);
        $catInfo = $defs[$catKey] ?? $defs['necklace_set'];

        $images = $productModel->getProductImages($id, $type);
        $prodName = $product['name'] ?? ($product['product_name'] ?? ($product['gproduct_name'] ?? ''));
        $prodCode = $product['code'] ?? ($product['product_code'] ?? ($product['gproduct_code'] ?? ''));

        // Load reference product photo(s) based on AI Writer settings
        $aiSettings = self::getAiWriterSettings();
        $analysisMode = $rawInput['analysis_mode'] ?? ($_GET['analysis_mode'] ?? ($aiSettings['image_analysis_mode'] ?? 'default'));
        $maxImages = (int)($rawInput['max_images'] ?? ($_GET['max_images'] ?? ($aiSettings['max_images'] ?? 10)));
        $maxImages = max(1, min(10, $maxImages));

        $productImageParts = [];
        if ($analysisMode === 'multi') {
            // Option 2: Multiple images analysis (prioritize authentic photos over previous AI images)
            usort($images, function($a, $b) {
                $aAi = (strpos($a['img_name'] ?? '', '_ai_') !== false);
                $bAi = (strpos($b['img_name'] ?? '', '_ai_') !== false);
                if ($aAi === $bAi) return 0;
                return $aAi ? 1 : -1;
            });

            $loadedCount = 0;
            foreach ($images as $img) {
                if (empty($img['img_name'])) continue;
                if ($loadedCount >= $maxImages) break;

                $mType = 'image/jpeg';
                $bin = $this->loadBinaryImage($img['img_name'], $mType);
                if (!empty($bin)) {
                    $bin = $this->optimizeImageBinary($bin, $mType);
                    $productImageParts[] = [
                        'inlineData' => [
                            'mimeType' => $mType,
                            'data' => base64_encode($bin)
                        ]
                    ];
                    $loadedCount++;
                }
            }
        } else {
            // Option 1 (Default): Single image analysis (use the default we have)
            $imgBinary = null;
            $mimeType = 'image/jpeg';
            if (!empty($images[0]['img_name'])) {
                $imgBinary = $this->loadBinaryImage($images[0]['img_name'], $mimeType);
            }
            if (!empty($imgBinary)) {
                $productImageParts[] = [
                    'inlineData' => [
                        'mimeType' => $mimeType,
                        'data' => base64_encode($imgBinary)
                    ]
                ];
            }
        }

        // Face Reference Model
        $facePart = null;
        if (!empty($faceRef)) {
            $facePath = __DIR__ . '/../assets/models/' . basename($faceRef);
            if (file_exists($facePath)) {
                $faceContent = file_get_contents($facePath);
                $faceMime = @mime_content_type($facePath) ?: 'image/png';
                $facePart = [
                    'inlineData' => [
                        'mimeType' => $faceMime,
                        'data' => base64_encode($faceContent)
                    ]
                ];
            }
        }

        $multiPhotoGuidance = "";
        if (count($productImageParts) > 1) {
            $borlaNote = ($catKey === 'borla_with_sari' || $catKey === 'borla') 
                ? " (IMPORTANT: In the attached flat reference photos, the Borla is laid flat on a surface from above. Synthesize it into its true physical THREE-DIMENSIONAL SPHERICAL DOME volume on the model's head — standing 20mm tall off the head — DO NOT flatten it into a 2D disc or brooch)" 
                : "";
            $multiPhotoGuidance = "\nCRITICAL MULTI-IMAGE SYNTHESIS: Attached are " . count($productImageParts) . " authentic visual photographs of this exact product showing its different angles, details, clasp, stones, and craftsmanship. Carefully analyze all " . count($productImageParts) . " attached photos together to faithfully reproduce this exact item{$borlaNote} without altering its authentic craftsmanship, gemstone colors, or design motifs.\n";
        }

        $results = [];
        $errors = [];

        // 1. Generate "With Model" with Category-Specific Anatomical Placement
        if ($mode === 'with_model' || $mode === 'both') {
            $isolationRule = self::getStrictJewelryIsolationRule($catKey);
            $priorityHeader = "";
            $negativeConstraints = "";
            $categoryExtraGuard = "";

            if ($catKey === 'borla_with_sari') {
                $priorityHeader = "MANDATORY TOP PRIORITY — 3D SCULPTURAL ELEVATION OF BORLA (MUST NOT BE FLAT!):\n" .
                    "The central forehead piece is an authentic traditional Rajasthani BORLA with Sari head chains.\n" .
                    "IMPORTANT VISUAL DISTINCTION: In the attached product reference photos, the Borla is laid flat on a surface from a top-down angle, making it appear circular. On the living model, however, a Borla is fundamentally a THREE-DIMENSIONAL SPHERICAL DOME / BULBOUS CONVEX HALF-SPHERE (like a miniature 3D architectural dome, half-ball, or bell ornament standing ~18-20mm tall and projecting OUTWARD from the head into 3D space).\n" .
                    "STRICT 3D PHYSICAL FORM REQUIREMENTS:\n" .
                    "1. HIGH 3D RELIEF & BULBOUS VOLUME: The central circular Borla MUST NOT be drawn flush or flat on the hair like a flat hair brooch, flat coin, flat medallion, or sticker! It MUST stand prominently proud of the scalp ('thoda utha hua') with substantial 3D spherical dome thickness and outward convex curvature.\n" .
                    "2. PRECISE HAIRLINE PERCH PLACEMENT: The 3D Borla dome sits perched right at the VERY FRONT APEX of the central hair parting (maang) at the top boundary of the forehead hairline, nestled into the roots of the hair (NOT placed flat halfway back on top of the hair like a hair barrette, and NOT pasted down on the bare skin of the forehead like a flat tikka).\n" .
                    "3. 3D LIGHTING & CONTACT DROP-SHADOW: Directional studio lighting must create bright specular highlights on the curved dome apex and a distinct, dark 3D contact drop-shadow under the lower rim of the dome where it meets the hair roots, clearly demonstrating that the ornament is elevated and three-dimensional.\n" .
                    "4. CAMERA PERSPECTIVE: 3/4 beauty portrait angle or gentle head tilt, clearly capturing the 3D outward profile, thickness, and elevation of the round dome.\n\n";

                $categoryExtraGuard = "\n==================== CRITICAL ANATOMICAL & 3D STRUCTURE RULES FOR BORLA WITH SARI ====================\n" .
                                      "- HERO PRODUCT: Authentic traditional Rajasthani Borla with Sari (3D Forehead Dome with Side Head Chains / Sheeshphool).\n" .
                                      "- 3D SPHERICAL DOME ELEVATION (MUST BE RAISED / 'THODA UTHA HUA'):\n" .
                                      "  * The central Borla is a solid THREE-DIMENSIONAL SPHERICAL / BELL-SHAPED HEMISPHERICAL DOME (convex 3D half-sphere with ~1.5 to 2 cm physical height protruding OUTWARD from the hair), encrusted with stones on its rounded convex surface.\n" .
                                      "  * IT IS ABSOLUTELY NOT A FLAT DISK, NOT A 2D CIRCLE, NOT A FLAT MEDALLION, AND NOT A STICKER!\n" .
                                      "  * Under directional studio lighting, the 3D dome must exhibit realistic spherical convex curvature: bright specular highlights on the curved dome apex, rounded curvature along its perimeter, and a distinct 3D drop shadow beneath its lower curved rim where it meets the hairline.\n" .
                                      "- STRICT PROHIBITION AGAINST FLAT MAANG TIKKA / NO FOREHEAD SKIN STICKER:\n" .
                                      "  * The Borla must NEVER be painted flat against the model's bare forehead skin ('ek dum mathe se chipka hua bilkul nahi hona chahiye')!\n" .
                                      "  * Do NOT place it down on the forehead like a flat maang tikka pendant. A maang tikka is flat; a Borla is a 3D raised spherical dome.\n" .
                                      "- PRECISE HAIRLINE PERCH PLACEMENT:\n" .
                                      "  * The 3D spherical Borla dome rests perched right at the crest of the central hair parting (maang) AT THE VERY TOP HAIRLINE (where the hair roots begin), nestled into the roots of the hair—NOT dangling low in the center of the bare forehead skin!\n" .
                                      "- SARI SIDE CHAINS:\n" .
                                      "  * 'Sari' (or 'sar') refers EXCLUSIVELY to the decorative side hair chains (sheeshphool / mathapatti chains) running along the hairline from the central elevated 3D Borla dome across the temples to above both ears where they pin into the hair!\n" .
                                      "  * ABSOLUTELY NO CLOTH SAREE OR VEIL OVER THE HEAD! 'Sari' here is head chain jewelry only.\n" .
                                      "- POSE & CAMERA ANGLE:\n" .
                                      "  * Pose the model at a subtle three-quarter (3/4) face angle or slight head tilt (15-20 degrees). This angle perfectly showcases the 3D outward convex volume of the elevated Borla dome protruding forward from the hairline and the side chains framing the hair.\n";

                $negativeConstraints = "\nNEGATIVE PROMPT CONSTRAINTS:\n" .
                    "- DO NOT draw a flat circular medallion, flat disk, flat coin, flat hair brooch, or flat sticker pressed flush against the hair.\n" .
                    "- DO NOT draw any flat maang tikka pasted flat on forehead skin.\n" .
                    "- DO NOT draw any necklace, choker, or neck jewelry (the model's neck and chest must be 100% completely bare skin).\n" .
                    "- DO NOT draw any earrings on earlobes (ears must be 100% bare).\n" .
                    "- The central Borla must be a prominent, elevated 3D spherical dome with obvious physical depth and drop shadow.\n";

            } elseif ($catKey === 'borla') {
                $priorityHeader = "MANDATORY TOP PRIORITY — 3D SCULPTURAL ELEVATION OF STANDALONE BORLA (MUST NOT BE FLAT!):\n" .
                    "The central forehead piece is a standalone traditional Rajasthani BORLA (without sari / without side chains).\n" .
                    "IMPORTANT VISUAL DISTINCTION: While the attached reference photos show the Borla photographed lying flat on a surface from a top-down angle, on the actual living model's head a Borla is fundamentally a THREE-DIMENSIONAL SPHERICAL DOME / BULBOUS CONVEX HALF-SPHERE (standing ~18-20mm tall and protruding outward from the head into 3D space).\n" .
                    "STRICT 3D PHYSICAL FORM REQUIREMENTS:\n" .
                    "1. HIGH 3D RELIEF & BULBOUS VOLUME: The central circular Borla MUST NOT be drawn flush or flat on the hair like a flat brooch, coin, flat medallion, or sticker! It MUST stand prominently proud of the scalp ('thoda utha hua') with substantial 3D spherical dome thickness and outward convex curvature.\n" .
                    "2. PRECISE HAIRLINE PERCH PLACEMENT: Perched right at the VERY FRONT APEX of the central hair parting (maang) at the forehead hairline roots.\n" .
                    "3. 3D SHADING: Specular highlight on dome apex with a distinct contact drop-shadow under the lower rim.\n" .
                    "4. STANDALONE ONLY: STRICTLY NO side chains, NO mathapatti bands, NO temple chains. Only the single center chain running back into the hair parting.\n\n";

                $categoryExtraGuard = "\n==================== CRITICAL ANATOMICAL & 3D STRUCTURE RULES FOR STANDALONE BORLA ====================\n" .
                                      "- HERO PRODUCT: Standalone round Rajasthani Borla ONLY (WITHOUT sari / without side chains).\n" .
                                      "- 3D SPHERICAL DOME ELEVATION (MUST BE RAISED / 'THODA UTHA HUA'):\n" .
                                      "  * The Borla is a prominent THREE-DIMENSIONAL SPHERICAL / BELL-SHAPED HEMISPHERICAL DOME with substantial physical height and volume (~1.5 to 2 cm protruding outward in 3D depth from the hair), encrusted with stones on its rounded convex surface. It MUST be visibly elevated and projecting outward in 3D ('thoda utha hua'), with rounded dome curvature and a subtle natural cast shadow beneath its base.\n" .
                                      "- STRICT PROHIBITION AGAINST FLAT MAANG TIKKA / NO FLAT FOREHEAD STICKER:\n" .
                                      "  * The Borla must NEVER be drawn flat or pasted flat against the model's bare forehead skin ('ek dum mathe se chipka hua bilkul nahi hona chahiye')! It must NOT resemble a flat dangling maang tikka pendant pasted onto the forehead.\n" .
                                      "- PRECISE HAIRLINE PERCH PLACEMENT:\n" .
                                      "  * The 3D spherical Borla dome rests perched right at the crest of the central hair parting (maang) at the TOP HAIRLINE (where the hair meets the forehead), nestled into the roots of the hair—NOT dangling low in the center of the bare forehead skin!\n" .
                                      "- STRICTLY NO side chains, NO mathapatti side bands, NO temple chains, NO ear-to-ear strings.\n" .
                                      "- The single spherical 3D Borla dome is secured solely by a single central chain running straight back into the center hair parting.\n" .
                                      "- Hair is parted cleanly down the center, highlighting only the elevated 3D Borla dome.\n" .
                                      "- Pose the model at a subtle three-quarter (3/4) face angle or slight head tilt showcasing the 3D dome's outward projection from the hairline.\n";

                $negativeConstraints = "\nNEGATIVE PROMPT CONSTRAINTS:\n" .
                    "- DO NOT draw a flat circular medallion, flat disk, flat coin, flat hair brooch, or flat sticker pressed flush against the hair.\n" .
                    "- DO NOT draw any side chains or mathapatti bands.\n" .
                    "- DO NOT draw any necklace or earrings (neck, ears, and chest must be 100% bare skin).\n" .
                    "- The central Borla must be a prominent, elevated 3D spherical dome with obvious physical depth and drop shadow.\n";
            }

            $modelPrompt = $priorityHeader .
                           "High-end commercial fashion magazine catalog photography of an elegant Indian woman model displaying EXCLUSIVELY this single " . $catInfo['copy_context'] . " (" . $prodName . ", SKU: " . $prodCode . ").\n" .
                           $isolationRule . "\n" .
                           $catInfo['model_instruction'] . "\n" .
                           $categoryExtraGuard . "\n" .
                           $multiPhotoGuidance . "\n" .
                           "The model has elegant styled hair and natural makeup. She is posed gracefully in a luxury Indian royal heritage palace corridor or warm festive studio setting with soft cinematic lighting.\n" .
                           "CRITICAL HERO PRODUCT FIDELITY: Ensure the single product is shown in crisp, sharp detail and is the EXCLUSIVE hero. Do not alter its colors, craftsmanship, or design motifs. ABSOLUTELY DO NOT generate or suggest any other jewelry pieces on the model.\n" .
                           $negativeConstraints .
                           ($customNote ? " Note: " . $customNote : "");

            $parts = [['text' => $modelPrompt]];
            foreach ($productImageParts as $pPart) {
                $parts[] = $pPart;
            }
            if ($facePart) $parts[] = $facePart;

            $withModelRes = $this->callGeminiImageGen($apiKey, $parts);
            if (!empty($withModelRes['success'])) {
                $saved = $this->cacheGeneratedImage($withModelRes['base64'], $prodCode, 'with_model');
                $results['with_model'] = [
                    'success' => true,
                    'url' => $saved['url'],
                    'base64' => $withModelRes['base64'],
                    'file_path' => $saved['file_path']
                ];
            } else {
                $errors['with_model'] = $withModelRes['error'] ?? 'Failed to generate model image.';
            }
        }

        $studioTheme = trim($rawInput['studio_surface'] ?? ($rawInput['studio_theme'] ?? ($_GET['studio_surface'] ?? ($_GET['studio_theme'] ?? 'dark_marble'))));
        $studioBgCustom = trim($rawInput['studio_bg_custom'] ?? ($rawInput['studio_custom_bg'] ?? ($_GET['studio_bg_custom'] ?? '')));
        $studioShotType = trim($rawInput['studio_shot_type'] ?? ($_GET['studio_shot_type'] ?? 'macro'));
        $customStudioPrompt = trim($rawInput['custom_studio_prompt'] ?? '');

        // 2. Generate "Without Model" (Studio Photo / Still Life)
        if ($mode === 'without_model' || $mode === 'both') {
            $studioPrompt = $this->buildStudioStillLifePrompt(
                $catInfo, 
                $prodName, 
                $prodCode, 
                $customNote, 
                $studioTheme, 
                $studioShotType, 
                $studioBgCustom, 
                $customStudioPrompt
            );

            if (!empty($multiPhotoGuidance)) {
                $studioPrompt = $multiPhotoGuidance . $studioPrompt;
            }

            $parts = [['text' => $studioPrompt]];
            foreach ($productImageParts as $pPart) {
                $parts[] = $pPart;
            }

            $withoutModelRes = $this->callGeminiImageGen($apiKey, $parts);
            if (!empty($withoutModelRes['success'])) {
                $saved = $this->cacheGeneratedImage($withoutModelRes['base64'], $prodCode, 'without_model');
                $results['without_model'] = [
                    'success' => true,
                    'url' => $saved['url'],
                    'base64' => $withoutModelRes['base64'],
                    'file_path' => $saved['file_path']
                ];
            } else {
                $errors['without_model'] = $withoutModelRes['error'] ?? 'Failed to generate studio image.';
            }
        }

        if (empty($results)) {
            $this->json(['error' => implode(' | ', $errors)], 500);
            return;
        }

        // Log operation to ai_analytics
        if ($this->db) {
            $count = count($results);
            $costEstimate = $count * 0.03 * 86; // ~₹2.58 per image
            $opType = 'all_in_one_image';
            $genOutput = "$count photoshoot image(s) generated";
            $numRefImages = count($productImageParts);
            $promptLog = ($mode === 'both') 
                ? "Generated both With & Without Model ({$catInfo['name']}, Mode: $analysisMode, $numRefImages images analyzed)" 
                : "Generated $mode ({$catInfo['name']}, Mode: $analysisMode, $numRefImages images analyzed)";
            $website = 'srishringarr';

            $stmt = $this->db->prepare("INSERT INTO ai_analytics (product_id, product_type, operation_type, prompt_text, generated_output, num_images, prompt_tokens, candidate_tokens, total_tokens, cost_estimate, website) VALUES (?, ?, ?, ?, ?, ?, 0, 0, 0, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("isssisds", $id, $type, $opType, $promptLog, $genOutput, $count, $costEstimate, $website);
                $stmt->execute();
                $stmt->close();
            }
        }

        $this->json([
            'success' => true,
            'id' => $id,
            'type' => $type,
            'category_key' => $catKey,
            'category_label' => $catInfo['name'],
            'analysis_mode' => $analysisMode,
            'images_analyzed_count' => count($productImageParts),
            'max_allowed' => $maxImages,
            'images' => $results,
            'errors' => $errors
        ]);
    }

    /**
     * Build "Without Model" (Studio Product Still Life) photoshoot prompt
     * supporting all Product Edit presets (Dark Marble, Velvet Cushion, Wooden Stand, Reflective Glass, Champagne Silk, White Studio)
     * shot types (Macro Close-Up, Flat Lay, 3/4 Display Angle, Floating Display), and anatomical placement rules.
     */
    public function buildStudioStillLifePrompt($catInfo, $prodName, $prodCode, $customNote = '', $studioTheme = 'dark_marble', $studioShotType = 'macro', $customBg = '', $customStudioPrompt = '')
    {
        if (!empty($customStudioPrompt)) {
            return $customStudioPrompt;
        }

        // Display Surface / Backdrop Presets matching Product Edit Page
        $surfacePresets = [
            'dark_marble' => 'luxury black marble surface with soft warm directional studio spotlighting',
            'velvet_cushion' => 'royal velvet jewelry display cushion with subtle warm accent lighting',
            'wooden_stand' => 'minimalist rustic wooden jewelry stand with soft natural shadows',
            'reflective_glass' => 'reflective dark mirror glass surface with sharp luxury reflections',
            'champagne_silk' => 'draped champagne silk fabric backdrop with soft diffuse studio lighting',
            'white_studio' => 'pristine white minimalist studio display backdrop',
            'royal_velvet' => 'plush angled royal wine-burgundy / deep maroon micro-velvet jewelry cushion nestled amidst banarasi silk drapery',
            'velvet_rose_petals' => 'luxurious deep maroon velvet jewelry pillow with vibrant fresh crimson rose petals delicately scattered',
            'royal_navy_velvet' => 'plush royal midnight-navy blue velvet jewelry cushion with dark raw silk folds',
            'dark_emerald_velvet' => 'plush deep emerald green royal micro-velvet jewelry cushion with warm golden zari accents',
            'black_velvet' => 'premium pitch-black micro-velvet jewelry display pad with ultra-high contrast chiaroscuro aesthetics'
        ];

        // Shot Types matching Product Edit Page
        $shotTypeDescriptions = [
            'macro' => 'ultra-sharp macro close-up product photography highlighting fine details, gemstone sparkle, and metal texture',
            'flat_lay' => 'professional flat lay top-down product photography layout',
            'angle_3_4' => 'elegant 3/4 perspective product display shot showing side and front angles',
            'floating' => 'dramatic floating product display with soft realistic shadow underneath'
        ];

        $bgDesc = !empty($customBg) ? $customBg : ($surfacePresets[$studioTheme] ?? $surfacePresets['dark_marble']);
        $shotDesc = $shotTypeDescriptions[$studioShotType] ?? ($studioShotType ?: $shotTypeDescriptions['macro']);

        $categoryContext = $catInfo['copy_context'] ?? 'Jewellery';
        $studioInstruction = $catInfo['studio_instruction'] ?? '';

        $prompt = "Ultra-high resolution professional studio product photography of this exact " . $categoryContext . " (" . $prodName . ", SKU: " . $prodCode . ") placed on " . $bgDesc . ".\n\n" .
            "SHOT & COMPOSITION:\n" .
            "- Shot type: " . $shotDesc . ".\n" .
            ($studioInstruction ? "- Placement & Layout: " . $studioInstruction . "\n" : "") .
            "- Needle-sharp focus on the jewelry piece, showcasing the brilliant metallic luster of 22k gold/rhodium plating, crystalline sparkle of Kundan/Polki/zircon stones, and fine metal craftsmanship.\n" .
            "- Soft diffuse studio lighting with natural gentle shadows, 8k resolution, razor-sharp textures.\n\n" .
            "STRICT RULES & CONSTRAINTS:\n" .
            "- STRICTLY NO human model, NO person, NO face, NO lips, NO ears, NO hands, NO fingers anywhere in the image. Pure product-only still-life photography.\n" .
            "- STRICTLY NO plastic mannequin heads, necks, or cheap acrylic showroom stands.\n" .
            "- The product piece is the absolute centerpiece hero. Do not alter its authentic design, colors, gemstones, or craftsmanship.\n" .
            "- STRICT SINGLE PRODUCT RULE: The surface must ONLY feature this exact single item from the reference photos. STRICTLY NO extra matching necklaces, earrings, rings, or unlisted AI jewelry items.\n" .
            "- Aspect ratio: 2:3 vertical format.\n" .
            ($customNote ? "- Custom Styling Note: " . $customNote . "\n" : "");

        return $prompt;
    }

    /**
     * Save Product Content directly to database
     */
    public function saveContent()
    {
        $rawInput = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($rawInput['id'] ?? 0);
        $type = ($rawInput['type'] ?? 'jewellery') === 'garment' ? 'garment' : 'jewellery';
        $name = trim($rawInput['name'] ?? '');
        $shortDesc = trim($rawInput['short_description'] ?? '');
        $desc = trim($rawInput['description'] ?? '');

        if (!$id || empty($name)) {
            $this->json(['error' => 'Product ID and Title cannot be empty.'], 400);
            return;
        }

        if (!$this->db) {
            $this->json(['error' => 'Database connection failed.'], 500);
            return;
        }

        // Enforce column maximums
        if (mb_strlen($name) > 95) $name = mb_substr($name, 0, 95);
        if (mb_strlen($shortDesc) > 245) $shortDesc = mb_substr($shortDesc, 0, 245);
        if ($type === 'garment' && mb_strlen($desc) > 950) $desc = mb_substr($desc, 0, 950);

        $escName = mysqli_real_escape_string($this->db, $name);
        $escShort = mysqli_real_escape_string($this->db, $shortDesc);
        $escDesc = mysqli_real_escape_string($this->db, $desc);

        if ($type === 'jewellery') {
            $sql = "UPDATE product SET product_name = '$escName', short_desc = '$escShort', product_desc = '$escDesc' WHERE product_id = $id";
        } else {
            $sql = "UPDATE garment_product SET gproduct_name = '$escName', short_desc = '$escShort', gproduct_desc = '$escDesc' WHERE gproduct_id = $id";
        }

        if (mysqli_query($this->db, $sql)) {
            $this->json([
                'success' => true,
                'id' => $id,
                'type' => $type,
                'message' => 'Product title & descriptions saved successfully!'
            ]);
        } else {
            $this->json(['error' => 'Database error: ' . mysqli_error($this->db)], 500);
        }
    }

    /**
     * Save Generated Image to Product Gallery & File System
     */
    public function saveImage()
    {
        $rawInput = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($rawInput['id'] ?? 0);
        $type = ($rawInput['type'] ?? 'jewellery') === 'garment' ? 'garment' : 'jewellery';
        $base64Data = $rawInput['base64'] ?? '';
        $mode = $rawInput['mode'] ?? 'with_model';
        $setAsMain = !empty($rawInput['set_as_main']);

        if (!$id || empty($base64Data)) {
            $this->json(['error' => 'Product ID and Image data are required.'], 400);
            return;
        }

        $productModel = new ProductModel();
        $product = $productModel->getProductById($id, $type);
        if (!$product) {
            $this->json(['error' => 'Product not found.'], 404);
            return;
        }

        $code = $product['code'] ?? ($product['product_code'] ?? ($product['gproduct_code'] ?? ''));
        $name = $product['name'] ?? ($product['product_name'] ?? ($product['gproduct_name'] ?? ''));
        $subcatId = (int)($product['subcategory_id'] ?? ($product['subcat_id'] ?? ($product['product_for'] ?? 0)));

        // Clean base64
        if (strpos($base64Data, ',') !== false) {
            $base64Data = explode(',', $base64Data)[1];
        }
        $imgBinary = base64_decode($base64Data);
        if (!$imgBinary) {
            $this->json(['error' => 'Invalid image payload.'], 400);
            return;
        }

        // Target directory: yn/uploads/YYYY/MM/
        $year = date('Y');
        $month = date('m');
        $uploadDir = __DIR__ . "/../../yn/uploads/{$year}/{$month}";
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }

        $sanitizedSku = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $code ?: 'prod');
        $filename = "{$sanitizedSku}_ai_{$mode}_" . time() . '_' . rand(100, 999) . '.jpg';
        $fullPath = $uploadDir . '/' . $filename;

        if (file_put_contents($fullPath, $imgBinary) === false) {
            $this->json(['error' => 'Failed to write image file to uploads directory.'], 500);
            return;
        }

        $relativePath = "/{$year}/{$month}/{$filename}";
        $dateAdded = date('Y-m-d H:i:s');
        $imgField = ($type === 'jewellery') ? 'product_id' : 'gproduct_id';
        $prodNameShort = mb_substr($name, 0, 24);

        $insertSql = "INSERT INTO product_images_new (
            prod_name, prod_image, pro_code, img_name, 
            subcat_id, $imgField, date_added
        ) VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($this->db, $insertSql);
        mysqli_stmt_bind_param($stmt, "ssssiis", $prodNameShort, $relativePath, $code, $relativePath, $subcatId, $id, $dateAdded);
        $inserted = mysqli_stmt_execute($stmt);
        $insertId = mysqli_insert_id($this->db);
        mysqli_stmt_close($stmt);

        if (!$inserted) {
            $this->json(['error' => 'Failed to insert image record into database: ' . mysqli_error($this->db)], 500);
            return;
        }

        // Set as main if requested or if product currently has no main image
        $currentMain = $type === 'jewellery' ? ($product['product_image'] ?? '') : ($product['gproduct_image'] ?? '');
        if ($setAsMain || empty($currentMain)) {
            $escRel = mysqli_real_escape_string($this->db, $relativePath);
            if ($type === 'jewellery') {
                mysqli_query($this->db, "UPDATE product SET product_image = '$escRel' WHERE product_id = $id");
            } else {
                mysqli_query($this->db, "UPDATE garment_product SET gproduct_image = '$escRel' WHERE gproduct_id = $id");
            }
        }

        $this->json([
            'success' => true,
            'id' => $id,
            'image_id' => $insertId,
            'relative_path' => $relativePath,
            'url' => '../yn/uploads' . $relativePath,
            'message' => 'Image saved to product gallery successfully!'
        ]);
    }

    /**
     * Save All Content and Images in a single combined call
     */
    public function saveAll()
    {
        $rawInput = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($rawInput['id'] ?? 0);
        $type = ($rawInput['type'] ?? 'jewellery') === 'garment' ? 'garment' : 'jewellery';
        $name = trim($rawInput['name'] ?? '');
        $shortDesc = trim($rawInput['short_description'] ?? '');
        $desc = trim($rawInput['description'] ?? '');
        $withModelB64 = $rawInput['with_model_base64'] ?? '';
        $withoutModelB64 = $rawInput['without_model_base64'] ?? '';

        if (!$id) {
            $this->json(['error' => 'Product ID is required.'], 400);
            return;
        }

        // 1. Save Content
        if (!empty($name)) {
            if (mb_strlen($name) > 95) $name = mb_substr($name, 0, 95);
            if (mb_strlen($shortDesc) > 245) $shortDesc = mb_substr($shortDesc, 0, 245);
            if ($type === 'garment' && mb_strlen($desc) > 950) $desc = mb_substr($desc, 0, 950);

            $escName = mysqli_real_escape_string($this->db, $name);
            $escShort = mysqli_real_escape_string($this->db, $shortDesc);
            $escDesc = mysqli_real_escape_string($this->db, $desc);

            if ($type === 'jewellery') {
                mysqli_query($this->db, "UPDATE product SET product_name = '$escName', short_desc = '$escShort', product_desc = '$escDesc' WHERE product_id = $id");
            } else {
                mysqli_query($this->db, "UPDATE garment_product SET gproduct_name = '$escName', short_desc = '$escShort', gproduct_desc = '$escDesc' WHERE gproduct_id = $id");
            }
        }

        $mainChoice = $rawInput['main_image_choice'] ?? 'without_model'; // 'without_model', 'with_model', or 'keep_existing'

        // 2. Save Images if present
        $savedImages = [];
        if (!empty($withModelB64)) {
            $isMain = ($mainChoice === 'with_model');
            $savedImages['with_model'] = $this->saveImageRecord($id, $type, $withModelB64, 'with_model', $isMain);
        }
        if (!empty($withoutModelB64)) {
            $isMain = ($mainChoice === 'without_model');
            $savedImages['without_model'] = $this->saveImageRecord($id, $type, $withoutModelB64, 'without_model', $isMain);
        }

        $this->json([
            'success' => true,
            'id' => $id,
            'type' => $type,
            'main_image_choice' => $mainChoice,
            'saved_images' => $savedImages,
            'message' => 'Product content and photoshoot photos updated successfully!'
        ]);
    }

    /**
     * Helper to save image record internally
     */
    private function saveImageRecord($id, $type, $base64Data, $mode, $setAsMain = false)
    {
        $productModel = new ProductModel();
        $product = $productModel->getProductById($id, $type);
        if (!$product) return null;

        $code = $product['code'] ?? ($product['product_code'] ?? ($product['gproduct_code'] ?? ''));
        $name = $product['name'] ?? ($product['product_name'] ?? ($product['gproduct_name'] ?? ''));
        $subcatId = (int)($product['subcategory_id'] ?? ($product['subcat_id'] ?? ($product['product_for'] ?? 0)));

        if (strpos($base64Data, ',') !== false) {
            $base64Data = explode(',', $base64Data)[1];
        }
        $imgBinary = base64_decode($base64Data);
        if (!$imgBinary) return null;

        $year = date('Y');
        $month = date('m');
        $uploadDir = __DIR__ . "/../../yn/uploads/{$year}/{$month}";
        if (!is_dir($uploadDir)) @mkdir($uploadDir, 0777, true);

        $sanitizedSku = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $code ?: 'prod');
        $filename = "{$sanitizedSku}_ai_{$mode}_" . time() . '_' . rand(100, 999) . '.jpg';
        $fullPath = $uploadDir . '/' . $filename;

        if (file_put_contents($fullPath, $imgBinary) === false) return null;

        $relativePath = "/{$year}/{$month}/{$filename}";
        $dateAdded = date('Y-m-d H:i:s');
        $imgField = ($type === 'jewellery') ? 'product_id' : 'gproduct_id';
        $prodNameShort = mb_substr($name, 0, 24);

        $insertSql = "INSERT INTO product_images_new (
            prod_name, prod_image, pro_code, img_name, 
            subcat_id, $imgField, date_added
        ) VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($this->db, $insertSql);
        mysqli_stmt_bind_param($stmt, "ssssiis", $prodNameShort, $relativePath, $code, $relativePath, $subcatId, $id, $dateAdded);
        mysqli_stmt_execute($stmt);
        $insertId = mysqli_insert_id($this->db);
        mysqli_stmt_close($stmt);

        // Update main catalog image if designated
        if ($setAsMain && $insertId) {
            $productModel->setMainProductImage($insertId, $id, $type);
        }

        return [
            'id' => $insertId,
            'url' => '../yn/uploads' . $relativePath,
            'path' => $relativePath,
            'is_main' => $setAsMain
        ];
    }

    /**
     * Set either generated photo or existing photo as the main catalog image
     */
    public function setMainImage()
    {
        $rawInput = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($rawInput['id'] ?? 0);
        $type = ($rawInput['type'] ?? 'jewellery') === 'garment' ? 'garment' : 'jewellery';
        $imagePath = trim($rawInput['image_path'] ?? '');
        $mode = $rawInput['mode'] ?? '';
        $base64 = $rawInput['base64'] ?? '';

        if (!$id) {
            $this->json(['error' => 'Product ID is required.'], 400);
            return;
        }

        if (!$this->db) {
            $this->json(['error' => 'Database connection failed.'], 500);
            return;
        }

        // If base64 provided and not yet written to disk
        if (!empty($base64)) {
            $saved = $this->saveImageRecord($id, $type, $base64, $mode ?: 'ai', true);
            if ($saved) {
                $this->json([
                    'success' => true,
                    'message' => 'Image saved and set as primary main product image!',
                    'image' => $saved
                ]);
                return;
            }
        }

        if (!empty($imagePath)) {
            $clean = preg_replace('#^https?://[^/]+(/yn/uploads)?#', '', $imagePath);
            $clean = preg_replace('#^\.\./yn/uploads#', '', $clean);
            $clean = '/' . ltrim($clean, '/');

            $escRel = mysqli_real_escape_string($this->db, $clean);
            $imgField = ($type === 'jewellery') ? 'product_id' : 'gproduct_id';
            $findImg = mysqli_query($this->db, "SELECT id FROM product_images_new WHERE (img_name = '$escRel' OR img_name = '" . ltrim($escRel, '/') . "') AND $imgField = $id LIMIT 1");
            $imgRow = mysqli_fetch_assoc($findImg);

            $productModel = new ProductModel();
            if ($imgRow && !empty($imgRow['id'])) {
                $productModel->setMainProductImage((int)$imgRow['id'], $id, $type);
            } else {
                if ($type === 'jewellery') {
                    mysqli_query($this->db, "UPDATE product SET product_image = '$escRel' WHERE product_id = $id");
                } else {
                    mysqli_query($this->db, "UPDATE garment_product SET gproduct_image = '$escRel' WHERE gproduct_id = $id");
                }
            }

            $this->json([
                'success' => true,
                'message' => 'Main product image updated successfully in database!'
            ]);
            return;
        }

        $this->json(['error' => 'Either image_path or base64 image data is required.'], 400);
    }

    /**
     * Call Gemini image generation API (supports gemini-2.5-flash-image and fallback)
     */
    private function callGeminiImageGen($apiKey, $parts)
    {
        $models = [
            'gemini-2.5-flash-image',
            'gemini-3.1-flash-image'
        ];

        $payload = json_encode([
            'contents' => [
                ['parts' => $parts]
            ],
            'generationConfig' => [
                'imageConfig' => [
                    'aspectRatio' => '2:3'
                ]
            ]
        ]);

        $lastError = 'Image generation failed.';

        foreach ($models as $model) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $apiKey;

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_TIMEOUT => 45,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            @curl_close($ch);

            if ($httpCode === 200) {
                $decoded = json_decode($response, true);
                $b64 = null;
                // Search all candidate parts for inlineData (in case part[0] contains text greeting)
                if (isset($decoded['candidates'][0]['content']['parts'])) {
                    foreach ($decoded['candidates'][0]['content']['parts'] as $part) {
                        if (!empty($part['inlineData']['data'])) {
                            $b64 = $part['inlineData']['data'];
                            break;
                        }
                    }
                }
                if ($b64) {
                    return [
                        'success' => true,
                        'base64' => $b64
                    ];
                }
            } else {
                $errObj = json_decode($response, true);
                $lastError = $errObj['error']['message'] ?? "HTTP $httpCode: $response";
            }
        }

        return ['success' => false, 'error' => $lastError];
    }

    /**
     * Cache generated image to preview directory
     */
    private function cacheGeneratedImage($base64Data, $sku, $mode)
    {
        $previewDir = __DIR__ . '/../assets/ai_preview';
        if (!is_dir($previewDir)) {
            @mkdir($previewDir, 0777, true);
        }

        $sanitizedSku = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $sku ?: 'item');
        $filename = "{$sanitizedSku}_{$mode}_" . time() . '_' . rand(10, 99) . '.jpg';
        $fullPath = $previewDir . '/' . $filename;

        file_put_contents($fullPath, base64_decode($base64Data));

        return [
            'file_path' => $fullPath,
            'url' => 'assets/ai_preview/' . $filename . '?v=' . time()
        ];
    }

    /**
     * Load image binary from local filesystem or remote CDN
     */
    private function loadBinaryImage($imgPath, &$mimeType)
    {
        $clean = ltrim(str_replace(['../../yn/uploads', '../yn/uploads', '/yn/uploads', 'yn/uploads/', 'uploads/'], '', $imgPath), '/');

        $localPaths = [
            __DIR__ . '/../../yn/uploads/' . $clean,
            __DIR__ . '/../../uploads/' . $clean,
            __DIR__ . '/../uploads/' . $clean,
            'C:/xampp/htdocs/ss/yn/uploads/' . $clean,
            'C:/xampp/htdocs/yn/uploads/' . $clean,
            'C:/xampp/htdocs/ss/' . $clean
        ];

        foreach ($localPaths as $lp) {
            if (file_exists($lp) && is_file($lp) && filesize($lp) > 0) {
                $mime = @mime_content_type($lp);
                if ($mime) $mimeType = $mime;
                return file_get_contents($lp);
            }
        }

        // Remote fallback
        $remoteUrls = [
            'https://srishringarr.com/yn/uploads/' . $clean,
            'https://srishringarr.com/uploads/' . $clean
        ];

        foreach ($remoteUrls as $url) {
            $content = @file_get_contents($url);
            if (!empty($content)) {
                $ext = strtolower(pathinfo($clean, PATHINFO_EXTENSION));
                if ($ext === 'png') $mimeType = 'image/png';
                elseif ($ext === 'webp') $mimeType = 'image/webp';
                return $content;
            }
        }

        return null;
    }

    /**
     * Safely optimize/resize image binary for Gemini API payload efficiency
     */
    private function optimizeImageBinary($binary, &$mimeType)
    {
        if (empty($binary) || !extension_loaded('gd')) {
            return $binary;
        }

        // If under 450KB, keep original
        if (strlen($binary) <= 450 * 1024) {
            return $binary;
        }

        $srcImg = @imagecreatefromstring($binary);
        if (!$srcImg) {
            return $binary;
        }

        $origW = imagesx($srcImg);
        $origH = imagesy($srcImg);

        $maxDim = 1024;
        if ($origW <= $maxDim && $origH <= $maxDim && strlen($binary) < 800 * 1024) {
            imagedestroy($srcImg);
            return $binary;
        }

        $ratio = min($maxDim / max($origW, 1), $maxDim / max($origH, 1));
        $newW = max(1, (int)round($origW * $ratio));
        $newH = max(1, (int)round($origH * $ratio));

        $dstImg = imagecreatetruecolor($newW, $newH);
        imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

        ob_start();
        imagejpeg($dstImg, null, 82);
        $compressed = ob_get_clean();

        imagedestroy($srcImg);
        imagedestroy($dstImg);

        if (!empty($compressed) && strlen($compressed) < strlen($binary)) {
            $mimeType = 'image/jpeg';
            return $compressed;
        }

        return $binary;
    }

    /**
     * Resolve category hierarchy display name
     */
    private function resolveCategoryName($type, $catId, $subcatId)
    {
        $catName = 'Catalog';
        if (!$this->db) return $catName;

        if ($type === 'jewellery') {
            if (!empty($subcatId)) {
                $subQ = mysqli_query($this->db, "SELECT name FROM subcat1 WHERE subcat_id = " . (int)$subcatId);
                if ($subR = mysqli_fetch_assoc($subQ)) return 'Jewellery > ' . ucwords(strtolower($subR['name']));
            } elseif (!empty($catId)) {
                $mQ = mysqli_query($this->db, "SELECT categories_name FROM jewel_subcat WHERE subcat_id = " . (int)$catId);
                if ($mR = mysqli_fetch_assoc($mQ)) return 'Jewellery > ' . ucwords(strtolower($mR['categories_name']));
            }
            return 'Jewellery';
        } else {
            if (!empty($catId)) {
                $gQ = mysqli_query($this->db, "SELECT name FROM garments WHERE garment_id = " . (int)$catId);
                if ($gR = mysqli_fetch_assoc($gQ)) return 'Apparel > ' . ucwords(strtolower($gR['name']));
            }
            return 'Apparel';
        }
    }

    /**
     * Resolve product images list with direct web URLs
     */
    private function resolveProductImages($id, $type, $sku, $mainImg, $productModel)
    {
        $rawImages = $productModel->getProductImages($id, $type);
        $resolved = [];

        foreach ($rawImages as $img) {
            $raw = $img['img_name'] ?? '';
            if (empty($raw)) continue;

            $url = '';
            if (str_starts_with($raw, 'http')) {
                $url = $raw;
            } else {
                $clean = ltrim(str_replace(['../../yn/uploads', '../yn/uploads', '/yn/uploads', 'yn/uploads/', 'uploads/'], '', $raw), '/');
                $localPath = 'C:/xampp/htdocs/ss/yn/uploads/' . $clean;
                if (file_exists($localPath)) {
                    $url = '../yn/uploads/' . $clean;
                } else {
                    $url = 'https://srishringarr.com/yn/uploads/' . $clean;
                }
            }

            $isAi = (strpos($raw, '_ai_with_model_') !== false || strpos($raw, '_ai_without_model_') !== false || strpos($raw, '_ai_') !== false);

            $resolved[] = [
                'id' => $img['id'] ?? 0,
                'raw_name' => $raw,
                'url' => $url,
                'is_main' => ($raw === $mainImg),
                'is_ai' => $isAi
            ];
        }

        return $resolved;
    }

    /**
     * Helper for SQL where clause based on category filter key
     */
    private function getCategorySqlClause($catKey)
    {
        if (strpos($catKey, ':') !== false) {
            list($type, $id) = explode(':', $catKey);
            $id = (int)$id;

            if ($type === 'garment') {
                return [
                    'jewel' => "1=0",
                    'garment' => "(garment_id = $id OR product_for = $id OR EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = garment_product.gproduct_id AND pc.product_type = 'garments' AND (pc.legacy_category_id = $id OR pc.legacy_subcategory_id = $id)))"
                ];
            } elseif ($type === 'jewel_parent' || $type === 'jewel_main') {
                return [
                    'jewel' => "(categories_id = $id OR subcat_id IN (SELECT subcat_id FROM subcat1 WHERE maincat_id = $id) OR EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = product.product_id AND pc.product_type = 'jewellery' AND (pc.legacy_category_id = $id OR pc.legacy_subcategory_id IN (SELECT subcat_id FROM subcat1 WHERE maincat_id = $id))))",
                    'garment' => "1=0"
                ];
            } elseif ($type === 'jewel_child' || $type === 'jewel_sub') {
                return [
                    'jewel' => "(subcat_id = $id OR EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = product.product_id AND pc.product_type = 'jewellery' AND pc.legacy_subcategory_id = $id))",
                    'garment' => "1=0"
                ];
            }
        }

        switch ($catKey) {
            case 'kamar_patta':
                return [
                    'jewel' => "(categories_id = 15 OR subcat_id = 56 OR product_name LIKE '%kamar%' OR product_code LIKE 'KP%')",
                    'garment' => "1=0"
                ];
            case 'necklace_set':
                return [
                    'jewel' => "(categories_id = 1 OR subcat_id IN (1,2,3,4,5,6,68,83,84) OR product_name LIKE '%necklace%' OR product_name LIKE '%choker%')",
                    'garment' => "1=0"
                ];
            case 'earrings':
                return [
                    'jewel' => "(categories_id = 17 OR subcat_id IN (59,74,75,76,77,78,79,80) OR product_name LIKE '%earring%' OR product_code LIKE 'ER%')",
                    'garment' => "1=0"
                ];
            case 'borla_with_sari':
                return [
                    'jewel' => "((categories_id = 11 OR subcat_id = 53 OR product_name LIKE '%borla%' OR product_code LIKE 'BO%') AND (product_name LIKE '%chain%' OR product_name LIKE '%sari%' OR product_name LIKE '%saari%' OR product_name LIKE '%sar%' OR product_name LIKE '%damini%' OR product_name LIKE '%mathapatti%' OR product_name LIKE '%sheeshphool%'))",
                    'garment' => "1=0"
                ];
            case 'borla':
                return [
                    'jewel' => "((categories_id = 11 OR subcat_id = 53 OR product_name LIKE '%borla%' OR product_code LIKE 'BO%') AND (product_name NOT LIKE '%chain%' AND product_name NOT LIKE '%sari%' AND product_name NOT LIKE '%saari%' AND product_name NOT LIKE '%damini%' AND product_name NOT LIKE '%mathapatti%' AND product_name NOT LIKE '%sheeshphool%'))",
                    'garment' => "1=0"
                ];
            case 'damini_mathapatti':
                return [
                    'jewel' => "(categories_id IN (14,19,27) OR subcat_id IN (57,63,73) OR product_name LIKE '%damini%' OR product_name LIKE '%mathapatti%' OR product_name LIKE '%tikka%') AND (categories_id != 11 AND subcat_id != 53 AND product_name NOT LIKE '%borla%' AND product_code NOT LIKE 'BO%')",
                    'garment' => "1=0"
                ];
            case 'nath':
                return [
                    'jewel' => "(categories_id = 12 OR subcat_id = 52 OR product_name LIKE '%nath%')",
                    'garment' => "1=0"
                ];
            case 'hath_phool':
                return [
                    'jewel' => "(categories_id = 23 OR subcat_id = 69 OR product_name LIKE '%hath%' OR product_code LIKE 'HP%')",
                    'garment' => "1=0"
                ];
            case 'bangles_bracelets':
                return [
                    'jewel' => "(categories_id IN (21,22) OR subcat_id IN (66,67) OR product_name LIKE '%bangle%' OR product_name LIKE '%bracelet%')",
                    'garment' => "1=0"
                ];
            case 'baju_bandh':
                return [
                    'jewel' => "(categories_id = 25 OR subcat_id = 71 OR product_name LIKE '%baju%' OR product_code LIKE 'BB%')",
                    'garment' => "1=0"
                ];
            case 'payal_pagpan':
                return [
                    'jewel' => "(categories_id = 20 OR subcat_id = 65 OR product_name LIKE '%payal%' OR product_name LIKE '%pag pan%')",
                    'garment' => "1=0"
                ];
            case 'rings':
                return [
                    'jewel' => "(categories_id = 18 OR subcat_id = 60 OR product_name LIKE '%ring%')",
                    'garment' => "1=0"
                ];
            case 'mala':
                return [
                    'jewel' => "(categories_id = 26 OR subcat_id = 72 OR product_name LIKE '%mala%')",
                    'garment' => "1=0"
                ];
            case 'pendant_set':
                return [
                    'jewel' => "(categories_id = 24 OR subcat_id = 70 OR product_name LIKE '%pendant%')",
                    'garment' => "1=0"
                ];
            case 'bridal_set':
                return [
                    'jewel' => "(categories_id = 29 OR subcat_id = 82 OR product_name LIKE '%bridal%')",
                    'garment' => "1=0"
                ];
            case 'lehenga_choli':
                return [
                    'jewel' => "1=0",
                    'garment' => "(garment_id = 10 OR product_for = 10 OR gproduct_name LIKE '%lehenga%')"
                ];
            case 'evening_gowns':
                return [
                    'jewel' => "1=0",
                    'garment' => "(garment_id IN (22, 29) OR product_for IN (22, 29) OR gproduct_name LIKE '%gown%')"
                ];
            case 'designer_blouse':
                return [
                    'jewel' => "1=0",
                    'garment' => "(garment_id = 5 OR product_for = 5 OR gproduct_name LIKE '%blouse%')"
                ];
            case 'indo_western':
                return [
                    'jewel' => "1=0",
                    'garment' => "(garment_id = 28 OR product_for = 28 OR gproduct_name LIKE '%indo%')"
                ];
            case 'kurtis':
                return [
                    'jewel' => "1=0",
                    'garment' => "(garment_id = 8 OR product_for = 8 OR gproduct_name LIKE '%kurti%')"
                ];
            default:
                return null;
        }
    }

    /**
     * Retrieve products uploaded on Saturday (or specified date) with category grouping
     */
    public function getSaturdayProducts()
    {
        if (!$this->db) {
            $this->json(['error' => 'Database connection failed.'], 500);
            return;
        }

        $date = trim($_GET['date'] ?? '2026-10-10');
        $escDate = mysqli_real_escape_string($this->db, $date);

        // Fetch Jewellery
        $jSql = "
            SELECT 
                p.product_id as id,
                p.product_code as code,
                p.product_name as name,
                'jewellery' as type,
                p.categories_id,
                p.subcat_id,
                p.sales_price,
                p.rent_price,
                p.product_image as main_image,
                p.date_added,
                COALESCE(s.name, j.categories_name, 'Jewellery') as category_name
            FROM product p
            LEFT JOIN subcat1 s ON p.subcat_id = s.subcat_id
            LEFT JOIN jewel_subcat j ON p.categories_id = j.subcat_id
            WHERE DATE(p.date_added) = '$escDate'
            ORDER BY p.product_id ASC
        ";

        // Fetch Garments
        $gSql = "
            SELECT 
                g.gproduct_id as id,
                g.gproduct_code as code,
                g.gproduct_name as name,
                'garment' as type,
                g.garment_id as categories_id,
                g.product_for as subcat_id,
                g.sales_price,
                g.rent_price,
                g.gproduct_image as main_image,
                g.date_added,
                COALESCE(gm.name, 'Apparel') as category_name
            FROM garment_product g
            LEFT JOIN garments gm ON g.garment_id = gm.garment_id
            WHERE DATE(g.date_added) = '$escDate'
            ORDER BY g.gproduct_id ASC
        ";

        $products = [];
        $groupCounts = [
            'all' => 0,
            'tikka' => 0,
            'damini' => 0,
            'mala' => 0,
            'kamar_patta' => 0,
            'tikka_damini' => 0,
            'apparel' => 0,
            'other' => 0
        ];

        $jRes = mysqli_query($this->db, $jSql);
        if ($jRes) {
            while ($r = mysqli_fetch_assoc($jRes)) {
                $code = strtoupper(trim($r['code']));
                if (str_starts_with($code, 'TD')) {
                    $grp = 'tikka_damini';
                    $grpName = 'Tikka Damini';
                } elseif (str_starts_with($code, 'T')) {
                    $grp = 'tikka';
                    $grpName = 'Maang Tikka';
                } elseif (str_starts_with($code, 'DP') || str_starts_with($code, 'D')) {
                    $grp = 'damini';
                    $grpName = 'Damini / Mathapatti';
                } elseif (str_starts_with($code, 'MALA')) {
                    $grp = 'mala';
                    $grpName = 'Mala / Necklace';
                } elseif (str_starts_with($code, 'KP') || str_starts_with($code, 'IKP')) {
                    $grp = 'kamar_patta';
                    $grpName = 'Kamar Patta';
                } else {
                    $grp = 'other';
                    $grpName = 'Other Jewellery';
                }

                $r['group_key'] = $grp;
                $r['group_name'] = $grpName;
                $imgName = $r['main_image'];
                $r['image_url'] = !empty($imgName) ? 'https://srishringarr.com/yn/uploads' . (str_starts_with($imgName, '/') ? $imgName : '/' . $imgName) : '';

                $products[] = $r;
                $groupCounts['all']++;
                if (isset($groupCounts[$grp])) {
                    $groupCounts[$grp]++;
                } else {
                    $groupCounts['other']++;
                }
            }
        }

        $gRes = mysqli_query($this->db, $gSql);
        if ($gRes) {
            while ($r = mysqli_fetch_assoc($gRes)) {
                $r['group_key'] = 'apparel';
                $r['group_name'] = 'Apparel / Garments';
                $imgName = $r['main_image'];
                $r['image_url'] = !empty($imgName) ? 'https://srishringarr.com/yn/uploads' . (str_starts_with($imgName, '/') ? $imgName : '/' . $imgName) : '';

                $products[] = $r;
                $groupCounts['all']++;
                $groupCounts['apparel']++;
            }
        }

        $this->json([
            'success' => true,
            'date' => $date,
            'total_count' => count($products),
            'group_counts' => $groupCounts,
            'products' => $products
        ]);
    }
}
