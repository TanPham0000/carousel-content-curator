
<?php
// Set the content type to JSON
header('Content-Type: application/json');

// CORS headers to allow access from any domain
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Path to the JSON file
$json_file = 'carousel_content.json';

// Check if the file exists
if (file_exists($json_file)) {
    // Read and output the JSON content
    echo file_get_contents($json_file);
} else {
    // Return an empty array if the file doesn't exist
    echo '[]';
}
?>
