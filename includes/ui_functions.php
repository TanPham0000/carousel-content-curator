
<?php
require_once 'config.php';

/**
 * Get status message based on query parameter
 * @return string HTML for status message
 */
function getStatusMessage() {
    if (!isset($_GET['status'])) {
        return '';
    }
    
    switch ($_GET['status']) {
        case 'success':
            return '<div class="alert success">Content added successfully!</div>';
        case 'deleted':
            return '<div class="alert success">Content deleted successfully!</div>';
        case 'updated':
            return '<div class="alert success">Content updated successfully!</div>';
        case 'max_reached':
            return '<div class="alert warning">Maximum number of slides reached (2).</div>';
        default:
            return '';
    }
}

/**
 * Render form for adding new content
 * @param int $current_count Current count of slides
 * @return string HTML for add content form
 */
function renderAddContentForm($current_count) {
    if ($current_count >= MAX_SLIDES) {
        return '<div class="alert warning">You have reached the maximum number of slides (' . MAX_SLIDES . '). Please delete an existing slide before adding a new one.</div>';
    }
    
    $form = '<form action="content_manager.php" method="post" enctype="multipart/form-data">
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
            </form>';
            
    return $form;
}

/**
 * Render list of existing content
 * @param array $content Array of content items
 * @return string HTML for content list
 */
function renderContentList($content) {
    if (empty($content)) {
        return '<p>No dynamic content available. Add content to be shown in slides 4 and 5.</p>';
    }
    
    // Sort by timestamp to display in order
    usort($content, function($a, $b) {
        return $a['timestamp'] - $b['timestamp'];
    });
    
    $output = '';
    foreach ($content as $index => $item) {
        $slide_number = $index + 4; // Starting from slide 4
        
        $output .= '<div class="content-item">
                        <span class="slide-number">Slide ' . $slide_number . ':</span>
                        <img src="' . htmlspecialchars($item['image']) . '" alt="' . htmlspecialchars($item['title']) . '" class="content-image">
                        <div class="content-details">
                            <h3>' . htmlspecialchars($item['title']) . '</h3>
                            <p>' . htmlspecialchars($item['description']) . '</p>';
                            
        if (!empty($item['link'])) {
            $output .= '<p><a href="' . htmlspecialchars($item['link']) . '" target="_blank">View Link</a></p>';
        }
        
        $output .= '</div>
                    <div class="actions">
                        <form class="delete-form" action="content_manager.php" method="post">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="' . $item['id'] . '">
                            <button type="submit" onclick="return confirm(\'Are you sure you want to delete this slide content?\');">Delete</button>
                        </form>
                    </div>
                </div>';
    }
    
    return $output;
}

/**
 * Render page styles
 * @return string CSS styles
 */
function renderStyles() {
    return '<style>
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
    </style>';
}
?>
