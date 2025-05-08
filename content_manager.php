
<?php
// Configuration
$json_file = 'carousel_content.json';

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
            saveCarouselContent($content);
            
            // Redirect to prevent form resubmission
            header('Location: ' . $_SERVER['PHP_SELF'] . '?status=success');
            exit;
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
    <title>Carousel Content Manager</title>
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
        .delete-form {
            display: inline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Carousel Content Manager</h1>
        
        <?php echo $status_message; ?>
        
        <div class="form-container">
            <h2>Add New Content</h2>
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
        </div>
        
        <div class="content-list">
            <h2>Existing Content</h2>
            <?php if (empty($all_content)): ?>
                <p>No content available.</p>
            <?php else: ?>
                <?php foreach ($all_content as $item): ?>
                    <div class="content-item">
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="content-image">
                        <div class="content-details">
                            <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                            <p><?php echo htmlspecialchars($item['description']); ?></p>
                            <?php if (!empty($item['link'])): ?>
                                <p><a href="<?php echo htmlspecialchars($item['link']); ?>" target="_blank">View Article</a></p>
                            <?php endif; ?>
                        </div>
                        <form class="delete-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this item?');">Delete</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="integration-guide" style="margin-top: 30px; background: #f9f9f9; padding: 20px; border-radius: 4px;">
            <h2>Integration Guide</h2>
            <p>To integrate this carousel content with your HTML website, add the following JavaScript code to your website:</p>
            <pre style="background: #eee; padding: 15px; border-radius: 4px; overflow-x: auto;">
&lt;script src="carousel_content.js"&gt;&lt;/script&gt;
            </pre>
            <p>Make sure the carousel_content.js file is in the same directory as your HTML file or adjust the path accordingly.</p>
        </div>
    </div>
</body>
</html>
