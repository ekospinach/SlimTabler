<?php
/**
 * Script to download Tabler UI assets from CDN to local project.
 * 
 * Run: php public/download-tabler.php
 */

$baseUrl = 'https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/';
$targetDir = __DIR__ . '/assets/tabler';

// Create directories
$dirs = [
    $targetDir,
    $targetDir . '/css',
    $targetDir . '/js',
    $targetDir . '/fonts',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "Created: $dir\n";
    }
}

// Files to download
$files = [
    'css/tabler.min.css',
    'css/tabler-flags.min.css',
    'css/tabler-payments.min.css',
    'css/tabler-vendors.min.css',
    'js/tabler.min.js',
];

// Also download Tabler Icons
$iconsUrl = 'https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.31.0/dist/';
$iconFiles = [
    'tabler-icons.min.css',
];

$success = true;
$errors = [];

// Download Tabler Core files
foreach ($files as $file) {
    $url = $baseUrl . $file;
    $localPath = $targetDir . '/' . $file;
    $localDir = dirname($localPath);
    
    if (!is_dir($localDir)) {
        mkdir($localDir, 0755, true);
    }
    
    echo "Downloading: $url\n";
    $content = @file_get_contents($url);
    
    if ($content === false) {
        // Try curl as fallback
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($content === false || $httpCode !== 200) {
            $errors[] = "Failed to download: $url (HTTP $httpCode)";
            $success = false;
            continue;
        }
    }
    
    if (file_put_contents($localPath, $content) !== false) {
        echo "  -> Saved to: $localPath (" . round(strlen($content) / 1024) . " KB)\n";
    } else {
        $errors[] = "Failed to write: $localPath";
        $success = false;
    }
}

// Download Tabler Icons
foreach ($iconFiles as $file) {
    $url = $iconsUrl . $file;
    $localPath = $targetDir . '/css/' . $file;
    
    echo "Downloading: $url\n";
    $content = @file_get_contents($url);
    
    if ($content === false) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($content === false || $httpCode !== 200) {
            $errors[] = "Failed to download: $url (HTTP $httpCode)";
            $success = false;
            continue;
        }
    }
    
    if (file_put_contents($localPath, $content) !== false) {
        echo "  -> Saved to: $localPath (" . round(strlen($content) / 1024) . " KB)\n";
    } else {
        $errors[] = "Failed to write: $localPath";
        $success = false;
    }
}

echo "\n" . str_repeat('=', 60) . "\n";
if ($success && empty($errors)) {
    echo "✅ All Tabler assets downloaded successfully!\n";
    echo "Assets are located in: public/assets/tabler/\n";
} else {
    echo "⚠️  Completed with " . count($errors) . " error(s):\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
    if ($success) {
        echo "\nMain assets downloaded successfully!\n";
    }
}
echo str_repeat('=', 60) . "\n";
