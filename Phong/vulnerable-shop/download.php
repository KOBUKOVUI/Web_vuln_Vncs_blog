<?php
ob_start();
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

$error = '';
if (isset($_GET['file']) && !empty($_GET['file'])) {
    $file = $_GET['file'];
    // Vulnerable: Path Traversal, no sanitization
    $file_path = 'files/documents/' . $file;

    if (file_exists($file_path)) {
        ob_clean();
        $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        $content_types = [
            'pdf' => 'application/pdf',
            'txt' => 'text/plain',
        ];
        $content_type = isset($content_types[$extension]) ? $content_types[$extension] : 'application/octet-stream';

        header('Content-Type: ' . $content_type);
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        ob_end_flush();
        exit;
    } else {
        $error = 'File not found: ' . htmlspecialchars($file, ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="en">        
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<main>
    <h2>Download File</h2>
    <?php if ($error): ?>
        <p class="flash-message flash-error"><?php echo $error; ?></p>
    <?php endif; ?>
    <p>Select a file to download:</p>
    <ul>
        <li><a href="<?php echo BASE_URL; ?>download.php?file=manual.pdf">Manual (PDF)</a></li>
        <li><a href="<?php echo BASE_URL; ?>download.php?file=guide.txt">Guide (TXT)</a></li>
    </ul>
</main>
</html>

<?php
require_once 'includes/footer.php';
?>