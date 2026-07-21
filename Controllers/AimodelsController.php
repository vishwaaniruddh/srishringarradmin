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

        $rawInput = json_decode(file_get_contents('php://input'), true);
        $modelId = (int)($_POST['model_id'] ?? ($rawInput['model_id'] ?? 0));
        $b64 = $_POST['cropped_image'] ?? ($rawInput['cropped_image'] ?? '');

        if ($modelId < 1 || $modelId > 10) {
            if (!empty($b64)) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Invalid model ID']);
                exit;
            }
            header("Location: index.php?controller=aimodels&error=Invalid model ID");
            exit;
        }

        $fileName = "model_$modelId.png";
        $destPath = __DIR__ . '/../assets/models/' . $fileName;

        // Ensure directory exists
        $modelsDir = dirname($destPath);
        if (!is_dir($modelsDir)) {
            mkdir($modelsDir, 0777, true);
        }

        // Handle Base64 cropped image payload (from Cropper modal)
        if (!empty($b64)) {
            $b64Data = preg_replace('#^data:image/\w+;base64,#i', '', $b64);
            $imageData = base64_decode($b64Data);
            if ($imageData && file_put_contents($destPath, $imageData) !== false) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Model framing saved successfully']);
                exit;
            } else {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Failed to save cropped image']);
                exit;
            }
        }

        // Handle standard file upload
        if (isset($_FILES['model_image']) && $_FILES['model_image']['error'] === UPLOAD_ERR_OK) {
            $tmpPath = $_FILES['model_image']['tmp_name'];
            move_uploaded_file($tmpPath, $destPath);
            header("Location: index.php?controller=aimodels&success=Model uploaded successfully");
            exit;
        }

        header("Location: index.php?controller=aimodels&error=No image uploaded");
        exit;
    }
}
