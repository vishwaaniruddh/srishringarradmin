<?php
namespace Controllers;

class AimodelsController {
    public function index() {
        // Load the view
        include __DIR__ . '/../Views/ai_models/index.php';
    }

    public function upload() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?controller=aimodels");
            exit;
        }

        $modelId = (int)($_POST['model_id'] ?? 0);
        
        if ($modelId < 1 || $modelId > 5) {
            header("Location: index.php?controller=aimodels&error=Invalid model ID");
            exit;
        }

        if (isset($_FILES['model_image']) && $_FILES['model_image']['error'] === UPLOAD_ERR_OK) {
            $tmpPath = $_FILES['model_image']['tmp_name'];
            $fileName = "model_$modelId.png";
            $destPath = __DIR__ . '/../assets/models/' . $fileName;

            // Ensure the models directory exists
            $modelsDir = dirname($destPath);
            if (!is_dir($modelsDir)) {
                mkdir($modelsDir, 0777, true);
            }

            // Convert and save as PNG
            $imageInfo = getimagesize($tmpPath);
            if ($imageInfo !== false) {
                $type = $imageInfo[2];
                $image = null;

                if ($type == IMAGETYPE_JPEG) {
                    $image = imagecreatefromjpeg($tmpPath);
                } elseif ($type == IMAGETYPE_PNG) {
                    $image = imagecreatefrompng($tmpPath);
                } elseif ($type == IMAGETYPE_WEBP) {
                    $image = imagecreatefromwebp($tmpPath);
                }

                if ($image) {
                    // Resize/Crop to 1:1 square ratio for consistency (optional but recommended)
                    $width = imagesx($image);
                    $height = imagesy($image);
                    $size = min($width, $height);
                    
                    $x = ($width - $size) / 2;
                    $y = ($height - $size) / 2;

                    $squareImage = imagecrop($image, ['x' => $x, 'y' => $y, 'width' => $size, 'height' => $size]);
                    
                    if ($squareImage !== false) {
                        imagepng($squareImage, $destPath);
                        header("Location: index.php?controller=aimodels&success=Model updated successfully");
                        exit;
                    }
                }
            }
            
            header("Location: index.php?controller=aimodels&error=Invalid image format or failed to process");
            exit;
        }

        header("Location: index.php?controller=aimodels&error=No image uploaded");
        exit;
    }
}
