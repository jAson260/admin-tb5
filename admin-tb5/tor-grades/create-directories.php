<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\tor-grades\create-directories.php

$baseDir = 'C:/laragon/www/admin-tb5';
$uploadsDir = $baseDir . '/uploads';
$torDir = $uploadsDir . '/tor';

echo "<h2>Creating Directories...</h2>";

// Create uploads directory
if (!is_dir($uploadsDir)) {
    if (mkdir($uploadsDir, 0777, true)) {
        echo "<p style='color:green;'>✓ Created: $uploadsDir</p>";
        chmod($uploadsDir, 0777);
    } else {
        echo "<p style='color:red;'>✗ Failed to create: $uploadsDir</p>";
        echo "<p>Error: " . print_r(error_get_last(), true) . "</p>";
    }
} else {
    echo "<p style='color:blue;'>• Already exists: $uploadsDir</p>";
    chmod($uploadsDir, 0777);
}

// Create tor directory
if (!is_dir($torDir)) {
    if (mkdir($torDir, 0777, true)) {
        echo "<p style='color:green;'>✓ Created: $torDir</p>";
        chmod($torDir, 0777);
    } else {
        echo "<p style='color:red;'>✗ Failed to create: $torDir</p>";
        echo "<p>Error: " . print_r(error_get_last(), true) . "</p>";
    }
} else {
    echo "<p style='color:blue;'>• Already exists: $torDir</p>";
    chmod($torDir, 0777);
}

// Check permissions
echo "<h3>Permission Check:</h3>";
echo "<p>Uploads directory writable: " . (is_writable($uploadsDir) ? '<span style="color:green;">✓ Yes</span>' : '<span style="color:red;">✗ No</span>') . "</p>";
echo "<p>TOR directory writable: " . (is_writable($torDir) ? '<span style="color:green;">✓ Yes</span>' : '<span style="color:red;">✗ No</span>') . "</p>";

// Try creating a test file
echo "<h3>File Creation Test:</h3>";
$testFile = $torDir . '/test_' . time() . '.txt';
if (file_put_contents($testFile, 'This is a test file')) {
    echo "<p style='color:green;'>✓ Test file created successfully at: $testFile</p>";
    echo "<p>File size: " . filesize($testFile) . " bytes</p>";
    unlink($testFile);
    echo "<p style='color:green;'>✓ Test file deleted successfully</p>";
} else {
    echo "<p style='color:red;'>✗ Failed to create test file</p>";
    echo "<p>Error: " . print_r(error_get_last(), true) . "</p>";
}

echo "<hr>";
echo "<h3>Current PHP User:</h3>";
echo "<pre>";
echo "PHP running as: " . get_current_user() . "\n";
echo "Process ID: " . getmypid() . "\n";
echo "</pre>";

echo "<h3>Directory Info:</h3>";
echo "<pre>";
if (file_exists($torDir)) {
    echo "TOR Directory exists: Yes\n";
    echo "TOR Directory perms: " . substr(sprintf('%o', fileperms($torDir)), -4) . "\n";
    echo "TOR Directory owner: " . fileowner($torDir) . "\n";
} else {
    echo "TOR Directory exists: No\n";
}
echo "</pre>";
?>