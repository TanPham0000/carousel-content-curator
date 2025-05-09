
<?php
require_once 'config.php';

/**
 * Function to read content from JSON file
 * @return array Array of content items
 */
function getCarouselContent() {
    if (file_exists(JSON_FILE)) {
        $content = json_decode(file_get_contents(JSON_FILE), true);
        return $content ? $content : [];
    }
    return [];
}

/**
 * Function to save content to JSON file
 * @param array $content Array of content items to save
 * @return void
 */
function saveCarouselContent($content) {
    file_put_contents(JSON_FILE, json_encode($content, JSON_PRETTY_PRINT));
}

/**
 * Handle image upload
 * @return string|bool Path to uploaded image or false if upload failed
 */
function handleImageUpload() {
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    // Create directory if it doesn't exist
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
    
    $filename = uniqid() . '_' . basename($_FILES['image']['name']);
    $target_file = UPLOAD_DIR . $filename;
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        return $target_file;
    }
    
    return false;
}

/**
 * Delete image file
 * @param string $path Path to the image file
 * @return bool True if deleted successfully or file doesn't exist
 */
function deleteImage($path) {
    if (file_exists($path)) {
        return unlink($path);
    }
    return true;
}
?>
