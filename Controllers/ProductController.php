<?php
namespace Controllers;

use Core\Controller;
use Controllers\Products\ProductCrudTrait;
use Controllers\Products\ProductAiTrait;
use Controllers\Products\ProductImportExportTrait;
use Controllers\Products\ProductBulkTrait;

/**
 * ProductController
 *
 * Modularized catalog controller composed of specialized domain traits:
 * - ProductCrudTrait: Core catalog CRUD, single product details & gallery weight management
 * - ProductAiTrait: AI content generation, image vision analysis & model generation
 * - ProductImportExportTrait: Bulk Excel/Zip imports, templates, and CSV/Excel exports
 * - ProductBulkTrait: Bulk price adjustments, bulk deletions, and bulk SKU image handling
 */
class ProductController extends Controller {
    use ProductCrudTrait;
    use ProductAiTrait;
    use ProductImportExportTrait;
    use ProductBulkTrait;
}
