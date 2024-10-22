<?php
//admin/management_event.php
session_start();

require_once('../db.php');

// Initialize variables
$form_submitted = false;
$event_title = '';
$event_start = '';
$event_start_time = '';
$event_end_time = '';
$event_end = '';
$event_location = '';
$event_desc = '';
$message = '';

if(!isset($_SESSION['username']) && !isset($_SESSION['user_id']) && !isset($_SESSION['is_admin'])){
    echo "You don't have access to this page";
    echo "<a href=\"..\login.php\">Login</a>";
} elseif($_SESSION['is_admin'] == 0){
    echo "You don't have access to this page<br>";
    echo "<a href='..\dashboard.php'>to Home page</a>";
} else {
    require("admin_nav.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add':
                    $event_title = $_POST['event_title'];
                    $event_start = $_POST['event_start']." ".$_POST['event_start_time'].":00";
                    
                    $event_end = $_POST['event_end']." ".$_POST['event_end_time'].":00";
                    $event_location = $_POST['event_location'];
                    $event_desc = $_POST['event_desc'];
                    $upload_file = null;
            
                    $filename = $_FILES['event_banner']['name'];
                    $temp_file = $_FILES['event_banner']['tmp_name'];
            
                    $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    $allowed_exts = array('jpg', 'jpeg', 'png');
                    if (in_array($file_ext, $allowed_exts)) {
                        $upload_dir = "event_banner/";
                        $upload_file = $upload_dir . $event_title . "." . $file_ext;
                        echo "Temp file: $temp_file<br>";
                        echo "Upload file: $upload_file<br>";
                        
                        if (move_uploaded_file($temp_file, $upload_file)) {
                            // File successfully uploaded
                        } else {
                            echo "Failed to move the uploaded file.";
                        }
                        
                           
                         //else {
                        //     $message = "Gagal mengupload file.";
                        // }
                    } //else {
                    //     $message = "Hanya file JPG, JPEG, atau PNG yang diizinkan."; 
                    // }
                    $sql = "INSERT INTO event(title,start_date,end_date,location,description,banner)
                            values(?,?,?,?,?,?)";
                            $stmt = $db->prepare($sql);
                            $stmt->execute([$event_title,$event_start,$event_end,$event_location,$event_desc,$upload_file]);
                    
            
                   
                    $form_submitted = true;
                    break;

                case 'edit':

                        $event_id = $_POST['event_id'];
                        $event_title = $_POST['event_title'];
                        $event_start = $_POST['event_start'] . " " . $_POST['event_start_time'] . ":00";
                        $event_end = $_POST['event_end'] . " " . $_POST['event_end_time'] . ":00";
                        $event_location = $_POST['event_location'];
                        $event_desc = $_POST['event_desc'];

                        $sql = "UPDATE event SET title = ?, start_date = ?, end_date = ?, location = ?, description = ? WHERE event_id = ?";
                        $stmt = $db->prepare($sql);
                        $result = $stmt->execute([$event_title, $event_start, $event_end, $event_location, $event_desc, $event_id]);

                        if ($_FILES['event_banner']['size'] > 0) {
                            $filename = $_FILES['event_banner']['name'];
                            $temp_file = $_FILES['event_banner']['tmp_name'];
                            $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                            $allowed_exts = array('jpg', 'jpeg', 'png');
                            
                            if (in_array($file_ext, $allowed_exts)) {
                                $upload_dir = "event_banner/";
                                $upload_file = $upload_dir . $event_title . "." . $file_ext;
                                
                                if (move_uploaded_file($temp_file, $upload_file)) {
                                    $sql = "UPDATE event SET banner = ? WHERE event_id = ?";
                                    $stmt = $db->prepare($sql);
                                    $result = $stmt->execute([$upload_file, $event_id]);
                                    
                                    if (!$result) {
                                        $errorInfo = $stmt->errorInfo();
                                        throw new Exception("Failed to update the event banner. Database error: " . $errorInfo[2]);
                                    }
                                } else {
                                    throw new Exception("Failed to move the uploaded file.");
                                }
                            }
                        }
                        

                        $message = "Event updated successfully.";
                    
                    break;

                    case 'delete':
                        $event_id = $_POST['event_id'];
                        
                        // Fetch the event details to get the banner filename
                        $fetch_sql = "SELECT banner FROM event WHERE event_id = ?";
                        $fetch_stmt = $db->prepare($fetch_sql);
                        $fetch_stmt->execute([$event_id]);
                        $event = $fetch_stmt->fetch(PDO::FETCH_ASSOC);
                        
                        // Delete the banner file if it exists
                        if ($event && $event['banner'] && file_exists($event['banner'])) {
                            unlink($event['banner']);
                        }
                        
                        // Fetch and delete all associated event images
                        $fetch_images_sql = "SELECT event_img FROM event_img WHERE event_id = ?";
                        $fetch_images_stmt = $db->prepare($fetch_images_sql);
                        $fetch_images_stmt->execute([$event_id]);
                        $event_images = $fetch_images_stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach ($event_images as $image) {
                            $image_path = 'event_images/' . $image['event_img'];
                            if (file_exists($image_path)) {
                                unlink($image_path);
                            }
                        }
                        
                        // Delete the event images from the database
                        $delete_images_sql = "DELETE FROM event_img WHERE event_id = ?";
                        $delete_images_stmt = $db->prepare($delete_images_sql);
                        $delete_images_stmt->execute([$event_id]);
                        
                        // Delete the event from the database
                        $delete_sql = "DELETE FROM event WHERE event_id = ?";
                        $delete_stmt = $db->prepare($delete_sql);
                        $delete_stmt->execute([$event_id]);
                        
                        $message = "Event and all associated images deleted successfully.";
                        break;
        
                    case 'end':
                        $event_id = $_POST['event_id'];
                        
                        // Fetch and delete all associated event images
                        $fetch_images_sql = "SELECT event_img FROM event_img WHERE event_id = ?";
                        $fetch_images_stmt = $db->prepare($fetch_images_sql);
                        $fetch_images_stmt->execute([$event_id]);
                        $event_images = $fetch_images_stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach ($event_images as $image) {
                            $image_path = 'event_images/' . $image['event_img'];
                            if (file_exists($image_path)) {
                                unlink($image_path);
                            }
                        }
                        
                        // Delete the event images from the database
                        $delete_images_sql = "DELETE FROM event_img WHERE event_id = ?";
                        $delete_images_stmt = $db->prepare($delete_images_sql);
                        $delete_images_stmt->execute([$event_id]);
                        
                        // Update the event status to 'completed'
                        $sql = "UPDATE event SET status = 'completed' WHERE event_id = ?";
                        $stmt = $db->prepare($sql);
                        $stmt->execute([$event_id]);
                        
                        $message = "Event ended and all associated images deleted successfully.";
                        break;

                        case 'activate':
                            $event_id = $_POST['event_id'];
                            
                            // Update the event status to 'active'
                            $sql = "UPDATE event SET status = 'active' WHERE event_id = ?";
                            $stmt = $db->prepare($sql);
                            $stmt->execute([$event_id]);
                            
                            $message = "Event activated successfully.";
                        break;
                }
            }
        }
    // Fetch events from the database
    $sql = "SELECT * FROM event ORDER BY start_date DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Manager (ADMIN)</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <main class="container mx-auto mt-8 p-6 bg-white rounded-lg shadow-md">
    
    
    <?php if ($form_submitted): ?>
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            <h3 class="font-semibold">Saved Successfully</h3>
        </div>
            <?php endif; ?>


        <?php if (!empty($message)): ?>
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 id="formTitle" class="text-xl font-semibold text-blue-600 mb-4">Create event</h3>
            </div>
            <a href="dashboard.php" class="text-blue-600 hover:underline">← Back to Dashboard</a>
        </div>
        <form id="eventForm" action="" method="post" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" id="formAction" name="action" value="add">
            <input type="hidden" id="eventId" name="event_id" value="">

            <div>
                <label for="event-title" class="block text-sm font-medium text-gray-700">Event title:</label>
                <input type="text" id="event-title" name="event_title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>

            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label for="event-start" class="block text-sm font-medium text-gray-700">Event start:</label>
                    <input type="date" id="event-start" name="event_start" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="event-start-time" class="block text-sm font-medium text-gray-700">Start time:</label>
                    <input type="time" id="event-start-time" name="event_start_time" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="event-end-time" class="block text-sm font-medium text-gray-700">End time:</label>
                    <input type="time" id="event-end-time" name="event_end_time" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>
            </div>

            <div>
                <label for="event-end" class="block text-sm font-medium text-gray-700">Event end:</label>
                <input type="date" id="event-end" name="event_end" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>

            <div>
                <label for="event-location" class="block text-sm font-medium text-gray-700">Event location:</label>
                <input type="text" id="event-location" name="event_location" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>

            <div>
                <label for="event-description" class="block text-sm font-medium text-gray-700">Event description:</label>
                <textarea id="event-description" name="event_desc" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></textarea>
            </div>

            <div>
                <label for="event-banner" class="block text-sm font-medium text-gray-700">Event banner:</label>
                <input type="file" id="event-banner" name="event_banner" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Save</button>
        
        </form>
    </main>

    <?php if (count($events) > 0): ?>
    <div class="container mx-auto mt-8 p-6 bg-white rounded-lg shadow-md">
        <h3 class="text-2xl font-semibold text-blue-600 mb-4">Event List</h3>
        <div class="overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">Title</th>
                        <th scope="col" class="px-6 py-3">Start Date</th>
                        <th scope="col" class="px-6 py-3">End Date</th>
                        <th scope="col" class="px-6 py-3">Location</th>
                        <th scope="col" class="px-6 py-3">Description</th>
                        <th scope="col" class="px-6 py-3">Banner</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event): ?>
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"><?php echo htmlspecialchars($event['title']); ?></td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($event['start_date']); ?></td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($event['end_date']); ?></td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($event['location']); ?></td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars(substr($event['description'], 0, 100)) . (strlen($event['description']) > 100 ? '...' : ''); ?></td>
                        
                        <td class="px-6 py-4">
                            <?php if (!empty($event['banner'])): ?>
                                <img src="<?php echo htmlspecialchars($event['banner']); ?>" alt="Event Banner" class="w-20 h-20 object-cover rounded">
                            <?php else: ?>
                                <span class="text-gray-400">No banner</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($event['status']); ?></td>
                        <td class="px-6 py-4">
                            <button onclick="editEvent(<?php echo htmlspecialchars(json_encode($event)); ?>)" class="font-medium text-blue-600 hover:underline">Edit</button>
                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this event?')" class="inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                                <button type="submit" class="font-medium text-red-600 hover:underline ml-2">Delete</button>
                            </form>
                            <?php if ($event['status'] !== 'completed'): ?>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to end this event?')" class="inline">
                                    <input type="hidden" name="action" value="end">
                                    <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                                    <button type="submit" class="font-medium text-orange-600 hover:underline ml-2">End</button>
                                </form>
                            <?php endif; ?>
                            <?php if ($event['status'] !== 'active'): ?>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to activate this event?')" class="inline">
                                    <input type="hidden" name="action" value="activate">
                                    <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                                    <button type="submit" class="font-medium text-green-600 hover:underline ml-2">Activate</button>
                                </form>
                            <?php endif; ?>
                            <?php if ($event['status'] !== 'completed'): ?>
                                <a href="event_img_management.php?event_id=<?php echo $event['event_id']; ?>" class="font-medium text-purple-600 hover:underline ml-2">Image management</a>
                            <?php endif; ?>
                            <a href="show_participants.php?event_id=<?php echo $event['event_id']; ?>" class="font-medium text-yellow-600 hover:underline ml-2">Participants</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
    <div class="container mx-auto mt-8 p-6 bg-white rounded-lg shadow-md">
        <p class="text-gray-600 text-lg text-center">No events found.</p>
    </div>
    <?php endif; ?>
    

    <script>
    function editEvent(event) {
    document.getElementById('formTitle').innerText = 'Edit Event';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('eventId').value = event.event_id;
    document.getElementById('event-title').value = event.title;
    
    let startDate = new Date(event.start_date);
    document.getElementById('event-start').value = startDate.toISOString().split('T')[0];
    document.getElementById('event-start-time').value = startDate.toTimeString().split(' ')[0].substr(0, 5);
    
    let endDate = new Date(event.end_date);
    document.getElementById('event-end').value = endDate.toISOString().split('T')[0];
    document.getElementById('event-end-time').value = endDate.toTimeString().split(' ')[0].substr(0, 5);
    
    document.getElementById('event-location').value = event.location;
    document.getElementById('event-description').value = event.description;
    
    // Scroll to the form
    document.getElementById('eventForm').scrollIntoView({behavior: 'smooth'});
}
    </script>

</body>
</html>

