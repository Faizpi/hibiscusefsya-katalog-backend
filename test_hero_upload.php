<?php
require_once __DIR__ . '/config/config.php';

$message = '';
$uploadedFile = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadDir = __DIR__ . '/uploads/hero/';
    
    $message .= "POST received<br>";
    $message .= "FILES: " . print_r($_FILES, true) . "<br>";
    
    if (!empty($_FILES['test_image']['tmp_name'])) {
        $tmpName = $_FILES['test_image']['tmp_name'];
        $originalName = $_FILES['test_image']['name'];
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $filename = 'test_' . time() . '.' . $ext;
        $filepath = $uploadDir . $filename;
        
        $message .= "Trying to move: $tmpName to $filepath<br>";
        
        if (move_uploaded_file($tmpName, $filepath)) {
            $message .= "SUCCESS! File saved to: $filepath<br>";
            $uploadedFile = UPLOAD_URL . 'hero/' . $filename;
        } else {
            $message .= "FAILED to move file<br>";
            $message .= "Error: " . error_get_last()['message'] . "<br>";
        }
    } else {
        $message .= "No file uploaded<br>";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Test Upload</title></head>
<body>
<h1>Test Hero Image Upload</h1>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="test_image" accept="image/*"><br><br>
    <button type="submit">Upload</button>
</form>
<hr>
<h2>Result:</h2>
<pre><?= htmlspecialchars($message) ?></pre>
<?php if ($uploadedFile): ?>
<h3>Uploaded Image:</h3>
<img src="<?= $uploadedFile ?>" style="max-width: 300px;">
<?php endif; ?>
</body>
</html>
