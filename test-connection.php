<?php
require_once('db-connect.php');

echo "<h2>Database Connection Test</h2>";
echo "<p><strong>Status:</strong> Connected successfully!</p>";
echo "<p><strong>Database:</strong> " . DB_NAME . "</p>";
echo "<p><strong>Host:</strong> " . DB_HOST . "</p>";

// Test query to show all tables
$result = $conn->query("SHOW TABLES");

if ($result) {
    echo "<h3>Tables in database:</h3>";
    echo "<ul>";
    while($row = $result->fetch_array()) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>Error: " . $conn->error . "</p>";
}

$conn->close();
?>