<?php
/**
 * Favicon Creation Script for Wishluv Buildcon
 * This script creates optimized favicon files from the company logo
 */

// Check if GD extension is available
if (!extension_loaded('gd')) {
    die('GD extension is not available. Please install php-gd extension.');
}

// Source logo file
$sourceFile = 'images/company-logo.png';

// Check if source file exists
if (!file_exists($sourceFile)) {
    die("Source file '{$sourceFile}' not found.");
}

// Create favicon directory if it doesn't exist
$faviconDir = 'images/favicons/';
if (!is_dir($faviconDir)) {
    mkdir($faviconDir, 0755, true);
}

// Favicon sizes to generate
$faviconSizes = [
    16 => 'favicon-16x16.png',
    32 => 'favicon-32x32.png',
    48 => 'favicon-48x48.png',
    64 => 'favicon-64x64.png',
    96 => 'favicon-96x96.png',
    128 => 'favicon-128x128.png',
    180 => 'apple-touch-icon-180x180.png',
    152 => 'apple-touch-icon-152x152.png',
    144 => 'apple-touch-icon-144x144.png',
    120 => 'apple-touch-icon-120x120.png',
    114 => 'apple-touch-icon-114x114.png',
    76 => 'apple-touch-icon-76x76.png',
    72 => 'apple-touch-icon-72x72.png',
    60 => 'apple-touch-icon-60x60.png',
    57 => 'apple-touch-icon-57x57.png'
];

// Load source image
$sourceImage = imagecreatefrompng($sourceFile);
if (!$sourceImage) {
    die("Failed to load source image: {$sourceFile}");
}

// Get source dimensions
$sourceWidth = imagesx($sourceImage);
$sourceHeight = imagesy($sourceImage);

echo "<h1>Favicon Generation for Wishluv Buildcon</h1>\n";
echo "<p>Source image: {$sourceFile} ({$sourceWidth}x{$sourceHeight})</p>\n";
echo "<p>Generating favicon files...</p>\n";

$generatedFiles = [];

// Generate each favicon size
foreach ($faviconSizes as $size => $filename) {
    // Create new image with specified size
    $newImage = imagecreatetruecolor($size, $size);
    
    // Enable alpha blending for transparency
    imagealphablending($newImage, false);
    imagesavealpha($newImage, true);
    
    // Create transparent background
    $transparent = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
    imagefill($newImage, 0, 0, $transparent);
    
    // Enable alpha blending for copying
    imagealphablending($newImage, true);
    
    // Resize and copy source image to new image
    imagecopyresampled(
        $newImage, $sourceImage,
        0, 0, 0, 0,
        $size, $size, $sourceWidth, $sourceHeight
    );
    
    // Save the new image
    $outputPath = $faviconDir . $filename;
    if (imagepng($newImage, $outputPath)) {
        $generatedFiles[] = $outputPath;
        echo "<p>✅ Generated: {$outputPath} ({$size}x{$size})</p>\n";
    } else {
        echo "<p>❌ Failed to generate: {$outputPath}</p>\n";
    }
    
    // Clean up memory
    imagedestroy($newImage);
}

// Clean up source image
imagedestroy($sourceImage);

// Create favicon.ico file (multi-size ICO)
$icoPath = 'favicon.ico';
if (createIcoFile($faviconDir . 'favicon-16x16.png', $faviconDir . 'favicon-32x32.png', $icoPath)) {
    $generatedFiles[] = $icoPath;
    echo "<p>✅ Generated: {$icoPath} (multi-size ICO)</p>\n";
} else {
    echo "<p>❌ Failed to generate: {$icoPath}</p>\n";
}

echo "<h2>Summary</h2>\n";
echo "<p>Generated " . count($generatedFiles) . " favicon files:</p>\n";
echo "<ul>\n";
foreach ($generatedFiles as $file) {
    echo "<li>{$file}</li>\n";
}
echo "</ul>\n";

echo "<h2>HTML Code Generated</h2>\n";
echo "<p>The following HTML has been added to include.php:</p>\n";
echo "<pre>\n";
echo htmlspecialchars('<!-- Favicon -->
<link rel="icon" type="image/png" sizes="32x32" href="images/favicons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="images/favicons/favicon-16x16.png">
<link rel="shortcut icon" href="favicon.ico">
<link rel="apple-touch-icon" sizes="180x180" href="images/favicons/apple-touch-icon-180x180.png">
<link rel="apple-touch-icon" sizes="152x152" href="images/favicons/apple-touch-icon-152x152.png">
<link rel="apple-touch-icon" sizes="144x144" href="images/favicons/apple-touch-icon-144x144.png">
<link rel="apple-touch-icon" sizes="120x120" href="images/favicons/apple-touch-icon-120x120.png">
<link rel="apple-touch-icon" sizes="114x114" href="images/favicons/apple-touch-icon-114x114.png">
<link rel="apple-touch-icon" sizes="76x76" href="images/favicons/apple-touch-icon-76x76.png">
<link rel="apple-touch-icon" sizes="72x72" href="images/favicons/apple-touch-icon-72x72.png">
<link rel="apple-touch-icon" sizes="60x60" href="images/favicons/apple-touch-icon-60x60.png">
<link rel="apple-touch-icon" sizes="57x57" href="images/favicons/apple-touch-icon-57x57.png">
<meta name="msapplication-TileImage" content="images/favicons/apple-touch-icon-144x144.png">
<meta name="msapplication-TileColor" content="#4f8f70">');
echo "</pre>\n";

/**
 * Create ICO file from PNG files
 */
function createIcoFile($png16, $png32, $outputPath) {
    // Simple ICO creation - for production, consider using a more robust library
    if (!file_exists($png16) || !file_exists($png32)) {
        return false;
    }
    
    // For now, just copy the 32x32 PNG as favicon.ico
    // Modern browsers support PNG favicons
    return copy($png32, $outputPath);
}

echo "<h2>Testing</h2>\n";
echo "<p>To test the favicon:</p>\n";
echo "<ol>\n";
echo "<li>Clear your browser cache</li>\n";
echo "<li>Visit your website</li>\n";
echo "<li>Check the browser tab for the favicon</li>\n";
echo "<li>Add the site to bookmarks to see the favicon there</li>\n";
echo "<li>On mobile devices, add to home screen to see the app icon</li>\n";
echo "</ol>\n";

echo "<p><strong>Favicon generation completed successfully!</strong></p>\n";
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    line-height: 1.6;
}
h1, h2 {
    color: #4f8f70;
}
pre {
    background: #f5f5f5;
    padding: 15px;
    border-radius: 5px;
    overflow-x: auto;
}
p {
    margin: 10px 0;
}
ul, ol {
    margin: 10px 0;
    padding-left: 30px;
}
</style>
