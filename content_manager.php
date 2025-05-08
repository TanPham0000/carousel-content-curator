
<?php
// Configuration
$json_file = 'carousel_content.json';
$max_slides = 2; // Limit to 2 dynamic slides (4th and 5th slides)

// Function to read content from JSON file
function getCarouselContent() {
    global $json_file;
    if (file_exists($json_file)) {
        $content = json_decode(file_get_contents($json_file), true);
        return $content ? $content : [];
    }
    return [];
}

// Function to save content to JSON file
function saveCarouselContent($content) {
    global $json_file;
    file_put_contents($json_file, json_encode($content, JSON_PRETTY_PRINT));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action === 'add') {
        $content = getCarouselContent();
        
        // Check if we already have 2 slides and the action is add
        if (count($content) >= $max_slides && $action === 'add') {
            $status_message = '<div class="alert warning">Maximum number of slides reached. Please delete an existing slide before adding a new one.</div>';
        } else {
            // Handle image upload
            $image_path = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/';
                
                // Create directory if it doesn't exist
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $filename = uniqid() . '_' . basename($_FILES['image']['name']);
                $target_file = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $image_path = $target_file;
                }
            }
            
            if ($image_path) {
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
                if (count($content) > $max_slides) {
                    // Sort by timestamp to find the oldest
                    usort($content, function($a, $b) {
                        return $a['timestamp'] - $b['timestamp'];
                    });
                    
                    // Delete the associated image if it exists
                    if (file_exists($content[0]['image'])) {
                        unlink($content[0]['image']);
                    }
                    
                    // Remove the oldest item
                    array_shift($content);
                }
                
                saveCarouselContent($content);
                
                // Redirect to prevent form resubmission
                header('Location: ' . $_SERVER['PHP_SELF'] . '?status=success');
                exit;
            }
        }
    } elseif ($action === 'delete' && isset($_POST['id'])) {
        $content = getCarouselContent();
        $id = $_POST['id'];
        
        foreach ($content as $key => $item) {
            if ($item['id'] === $id) {
                // Delete the associated image if it exists
                if (file_exists($item['image'])) {
                    unlink($item['image']);
                }
                unset($content[$key]);
                break;
            }
        }
        
        saveCarouselContent(array_values($content)); // Reindex array after deletion
        
        header('Location: ' . $_SERVER['PHP_SELF'] . '?status=deleted');
        exit;
    } elseif ($action === 'update' && isset($_POST['id'])) {
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
                    $upload_dir = 'uploads/';
                    
                    // Create directory if it doesn't exist
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $filename = uniqid() . '_' . basename($_FILES['image']['name']);
                    $target_file = $upload_dir . $filename;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                        // Delete the old image if it exists
                        if (file_exists($item['image'])) {
                            unlink($item['image']);
                        }
                        $content[$key]['image'] = $target_file;
                    }
                }
                
                break;
            }
        }
        
        saveCarouselContent($content);
        
        header('Location: ' . $_SERVER['PHP_SELF'] . '?status=updated');
        exit;
    }
}

// Display status messages
$status_message = '';
if (isset($_GET['status'])) {
    switch ($_GET['status']) {
        case 'success':
            $status_message = '<div class="alert success">Content added successfully!</div>';
            break;
        case 'deleted':
            $status_message = '<div class="alert success">Content deleted successfully!</div>';
            break;
        case 'updated':
            $status_message = '<div class="alert success">Content updated successfully!</div>';
            break;
        case 'max_reached':
            $status_message = '<div class="alert warning">Maximum number of slides reached (2).</div>';
            break;
    }
}

// Get all content for display
$all_content = getCarouselContent();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carousel Content Manager - Slides 4 & 5</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1, h2 {
            color: #444;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button, .button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover, .button:hover {
            background: #45a049;
        }
        .alert {
            padding: 10px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .warning {
            background-color: #fff3cd;
            color: #856404;
        }
        .content-list {
            margin-top: 30px;
        }
        .content-item {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
            display: flex;
            align-items: center;
        }
        .content-image {
            width: 100px;
            height: 70px;
            object-fit: cover;
            margin-right: 15px;
        }
        .content-details {
            flex: 1;
        }
        .delete-form, .edit-form {
            display: inline;
        }
        .slide-number {
            font-weight: bold;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Carousel Content Manager - Slides 4 & 5</h1>
        <p>Manage the dynamic content for slides 4 and 5 of your carousel. The first 3 slides are static and not managed here.</p>
        
        <?php echo $status_message; ?>
        
        <div class="form-container">
            <h2>Add New Slide Content</h2>
            <?php if (count($all_content) >= $max_slides): ?>
                <div class="alert warning">You have reached the maximum number of slides (<?php echo $max_slides; ?>). Please delete an existing slide before adding a new one.</div>
            <?php else: ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label for="image">Image:</label>
                    <input type="file" id="image" name="image" required accept="image/*">
                </div>
                
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="3" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="link">Link (Optional):</label>
                    <input type="text" id="link" name="link" placeholder="https://example.com/article">
                </div>
                
                <button type="submit">Add Content</button>
            </form>
            <?php endif; ?>
        </div>
        
        <div class="content-list">
            <h2>Existing Slide Content</h2>
            <?php if (empty($all_content)): ?>
                <p>No dynamic content available. Add content to be shown in slides 4 and 5.</p>
            <?php else: ?>
                <?php 
                // Sort by timestamp to display in order
                usort($all_content, function($a, $b) {
                    return $a['timestamp'] - $b['timestamp'];
                });
                
                foreach ($all_content as $index => $item): 
                    $slide_number = $index + 4; // Starting from slide 4
                ?>
                    <div class="content-item">
                        <span class="slide-number">Slide <?php echo $slide_number; ?>:</span>
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="content-image">
                        <div class="content-details">
                            <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                            <p><?php echo htmlspecialchars($item['description']); ?></p>
                            <?php if (!empty($item['link'])): ?>
                                <p><a href="<?php echo htmlspecialchars($item['link']); ?>" target="_blank">View Link</a></p>
                            <?php endif; ?>
                        </div>
                        <div class="actions">
                            <form class="delete-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this slide content?');">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="integration-guide" style="margin-top: 30px; background: #f9f9f9; padding: 20px; border-radius: 4px;">
            <h2>Integration Guide</h2>
            <p>Your carousel is already set up to automatically load the dynamic content for slides 4 and 5.</p>
            <p>The first 3 slides remain static as defined in your HTML, and these 2 additional slides will be loaded dynamically from the content you manage here.</p>
        </div>
    </div>
</body>
</html>
