
<?php
require_once 'config.php';
require_once 'includes/content_functions.php';
require_once 'includes/form_handlers.php';
require_once 'includes/ui_functions.php';

// Handle form submission
$status_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    switch ($action) {
        case 'add':
            $status_message = handleAddContent();
            break;
        case 'delete':
            if (isset($_POST['id'])) {
                handleDeleteContent();
            }
            break;
        case 'update':
            if (isset($_POST['id'])) {
                handleUpdateContent();
            }
            break;
    }
}

// Get all content for display
$all_content = getCarouselContent();

// Get status message if redirected
if (empty($status_message)) {
    $status_message = getStatusMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carousel Content Manager - Slides 4 & 5</title>
    <?php echo renderStyles(); ?>
</head>
<body>
    <div class="container">
        <h1>Carousel Content Manager - Slides 4 & 5</h1>
        <p>Manage the dynamic content for slides 4 and 5 of your carousel. The first 3 slides are static and not managed here.</p>
        
        <?php echo $status_message; ?>
        
        <div class="form-container">
            <h2>Add New Slide Content</h2>
            <?php echo renderAddContentForm(count($all_content)); ?>
        </div>
        
        <div class="content-list">
            <h2>Existing Slide Content</h2>
            <?php echo renderContentList($all_content); ?>
        </div>
        
        <div class="integration-guide" style="margin-top: 30px; background: #f9f9f9; padding: 20px; border-radius: 4px;">
            <h2>Integration Guide</h2>
            <p>Your carousel is already set up to automatically load the dynamic content for slides 4 and 5.</p>
            <p>The first 3 slides remain static as defined in your HTML, and these 2 additional slides will be loaded dynamically from the content you manage here.</p>
        </div>
    </div>
</body>
</html>
