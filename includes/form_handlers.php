
<?php
require_once 'config.php';
require_once 'includes/content_functions.php';

/**
 * Handle add content form submission
 * @return string Status message
 */
function handleAddContent() {
    $content = getCarouselContent();
    
    // Check if we already have maximum slides
    if (count($content) >= MAX_SLIDES) {
        return '<div class="alert warning">Maximum number of slides reached. Please delete an existing slide before adding a new one.</div>';
    }
    
    // Handle image upload
    $image_path = handleImageUpload();
    if (!$image_path) {
        return '<div class="alert warning">Image upload failed.</div>';
    }
    
    // Add new content
    $new_content = [
        'id' => uniqid(),
        'image' => $image_path,
        'link' => isset($_POST['link']) ? $_POST['link'] : '',
        'title' => isset($_POST['title']) ? $_POST['title'] : '',
        'description' => isset($_POST['description']) ? $_POST['description'] : '',
        'timestamp' => time()
    ];
    
    $content[] = $new_content;
    
    // If we now have more than max_slides, remove the oldest one
    if (count($content) > MAX_SLIDES) {
        // Sort by timestamp to find the oldest
        usort($content, function($a, $b) {
            return $a['timestamp'] - $b['timestamp'];
        });
        
        // Delete the associated image
        deleteImage($content[0]['image']);
        
        // Remove the oldest item
        array_shift($content);
    }
    
    saveCarouselContent($content);
    
    // Redirect to prevent form resubmission
    header('Location: content_manager.php?status=success');
    exit;
}

/**
 * Handle delete content form submission
 * @return void
 */
function handleDeleteContent() {
    $content = getCarouselContent();
    $id = $_POST['id'];
    
    foreach ($content as $key => $item) {
        if ($item['id'] === $id) {
            // Delete the associated image
            deleteImage($item['image']);
            unset($content[$key]);
            break;
        }
    }
    
    saveCarouselContent(array_values($content)); // Reindex array after deletion
    
    header('Location: content_manager.php?status=deleted');
    exit;
}

/**
 * Handle update content form submission
 * @return void
 */
function handleUpdateContent() {
    $content = getCarouselContent();
    $id = $_POST['id'];
    
    foreach ($content as $key => $item) {
        if ($item['id'] === $id) {
            // Update fields
            $content[$key]['title'] = isset($_POST['title']) ? $_POST['title'] : $item['title'];
            $content[$key]['description'] = isset($_POST['description']) ? $_POST['description'] : $item['description'];
            $content[$key]['link'] = isset($_POST['link']) ? $_POST['link'] : $item['link'];
            
            // Handle image update if provided
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image_path = handleImageUpload();
                if ($image_path) {
                    // Delete the old image
                    deleteImage($item['image']);
                    $content[$key]['image'] = $image_path;
                }
            }
            
            break;
        }
    }
    
    saveCarouselContent($content);
    
    header('Location: content_manager.php?status=updated');
    exit;
}
?>
