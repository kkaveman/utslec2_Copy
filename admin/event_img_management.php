<?php
//event_img_management.php
session_start();
require_once('../db.php');

if(!isset($_SESSION['username']) || !isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] == 0){
    echo "You don't have access to this page";
    echo "<a href=\"../login.php\">Login</a>";
    exit();
}

$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

if ($event_id == 0) {
    echo "Invalid event ID";
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'upload':
                handleImageUpload($db, $event_id);
                break;
            case 'delete':
                handleImageDelete($db, $event_id);
                break;
        }
    }
}

// Fetch existing images for this event
$sql = "SELECT * FROM event_img WHERE event_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$event_id]);
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch event details
$sql = "SELECT title FROM event WHERE event_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

function handleImageUpload($db, $event_id) {
    global $message;
    
    if (!isset($_FILES['event_images'])) {
        $message = "No files were uploaded.";
        return;
    }

    $upload_dir = "event_images/";
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB

    // Get the current highest iteration for this event
    $sql = "SELECT MAX(CAST(SUBSTRING_INDEX(event_img, '-', -1) AS UNSIGNED)) as max_iteration 
            FROM event_img 
            WHERE event_id = ? AND event_img REGEXP '^{$event_id}-[0-9]+\\.';";
    $stmt = $db->prepare($sql);
    $stmt->execute([$event_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $iteration = $result['max_iteration'] ? $result['max_iteration'] + 1 : 1;

    foreach ($_FILES['event_images']['tmp_name'] as $key => $tmp_name) {
        $file_name = $_FILES['event_images']['name'][$key];
        $file_size = $_FILES['event_images']['size'][$key];
        $file_type = $_FILES['event_images']['type'][$key];

        if (!in_array($file_type, $allowed_types)) {
            $message .= "Error: {$file_name} is not an allowed image type.<br>";
            continue;
        }

        if ($file_size > $max_size) {
            $message .= "Error: {$file_name} exceeds the maximum file size of 5MB.<br>";
            continue;
        }

        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $new_file_name = "{$event_id}-{$iteration}.{$file_ext}";
        $upload_path = $upload_dir . $new_file_name;

        if (move_uploaded_file($tmp_name, $upload_path)) {
            // Insert into database
            $sql = "INSERT INTO event_img (event_id, event_img) VALUES (?, ?)";
            $stmt = $db->prepare($sql);
            if ($stmt->execute([$event_id, $new_file_name])) {
                $message .= "Success: {$file_name} uploaded successfully.<br>";
                $iteration++;
            } else {
                $message .= "Error: Failed to insert {$file_name} into database.<br>";
            }
        } else {
            $message .= "Error: Failed to upload {$file_name}.<br>";
        }
    }
}

function handleImageDelete($db, $event_id) {
    global $message;

    if (!isset($_POST['image_id'])) {
        $message = "No image selected for deletion.";
        return;
    }

    $image_id = intval($_POST['image_id']);

    // Fetch the image filename
    $sql = "SELECT event_img FROM event_img WHERE event_img_id = ? AND event_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$image_id, $event_id]);
    $image = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($image) {
        // Delete the file
        $file_path = 'event_images/' . $image['event_img'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // Delete from database
        $sql = "DELETE FROM event_img WHERE event_img_id = ?";
        $stmt = $db->prepare($sql);
        if ($stmt->execute([$image_id])) {
            $message = "Image deleted successfully.";
        } else {
            $message = "Error: Failed to delete image from database.";
        }
    } else {
        $message = "Error: Image not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Image Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php require("admin_nav.php"); ?>

    <main class="container mx-auto mt-8 p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold text-blue-600 mb-4">Image Management for Event: <?php echo htmlspecialchars($event['title']); ?></h2>

        <?php if (!empty($message)): ?>
            <div class="mb-6 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data" class="space-y-4 mb-8">
            <input type="hidden" name="action" value="upload">
            <div>
                <label for="event-images" class="block text-sm font-medium text-gray-700">Upload Images:</label>
                <input type="file" id="event-images" name="event_images[]" accept="image/*" multiple required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Upload Images</button>
        </form>

        <?php if (count($images) > 0): ?>
            <h3 class="text-xl font-semibold text-blue-600 mb-4">Event Images</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php foreach ($images as $image): ?>
                    <div class="relative group">
                        <img src="<?php echo htmlspecialchars('event_images/' . $image['event_img']); ?>" alt="Event Image" class="w-full h-48 object-cover rounded">
                        <form method="POST" action="" class="absolute top-0 right-0 hidden group-hover:block">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="image_id" value="<?php echo $image['event_img_id']; ?>">
                            <button type="submit" class="bg-red-500 text-white p-2 rounded" onclick="return confirm('Are you sure you want to delete this image?')">Delete</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-600">No images uploaded for this event yet.</p>
        <?php endif; ?>
    </main>
</body>
</html>