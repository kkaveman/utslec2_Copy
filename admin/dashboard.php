<?php
//admin/dashboard.php
session_start();

require_once('../db.php');


// Initialize variables

if(!isset($_SESSION['username'])&&
!isset($_SESSION['user_id'])&&
!isset($_SESSION['is_admin'])){
    echo "You don't have access to this page";
    echo "<a href=\"..\login.php\">Login</a>"; //change style
    
}

elseif($_SESSION['is_admin'] == 0){
    echo "You don't have access to this page<br>";
    echo "<a href='..\dashboard.php'>to Home page</a>"; //change style
       
}else{
    require("admin_nav.php");

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
        <?php 
                echo "<h2 class=\"text-2xl font-semibold text-blue-600 mb-4\">Welcome ".$_SESSION['username']."</h2>";
        ?>
        <p class="text-gray-600 mb-6">Admin dashboard</p>
        <div class="flex space-x-4">
            <a href="management_event.php" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Event Management
            </a>
            <a href="manage_user.php" class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-75 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                User Management
            </a>
        </div>

<?php
// Fetch events from the database with participant count
$sql = "SELECT e.*, COUNT(ue.user_id) as registered_users 
        FROM event e 
        LEFT JOIN user_event ue ON e.event_id = ue.event_id 
        GROUP BY e.event_id 
        ORDER BY e.start_date DESC";
$stmt = $db->prepare($sql);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Display events in a table
if (count($events) > 0) {
    echo '<div class="m-6">';
    echo '<h3 class="text-2xl font-semibold text-blue-600 mb-4">Event List</h3>';
    echo '<div class="overflow-x-auto shadow-md sm:rounded-lg">';
    echo '<table class="w-full text-sm text-left text-gray-500">';
    echo '<thead class="text-xs text-gray-700 uppercase bg-gray-50">';
    echo '<tr>';
    echo '<th scope="col" class="px-6 py-3">Title</th>';
    echo '<th scope="col" class="px-6 py-3">Start Date</th>';
    echo '<th scope="col" class="px-6 py-3">End Date</th>';
    echo '<th scope="col" class="px-6 py-3">Location</th>';
    echo '<th scope="col" class="px-6 py-3">Description</th>';
    echo '<th scope="col" class="px-6 py-3">Banner</th>';
    echo '<th scope="col" class="px-6 py-3">Status</th>';
    echo '<th scope="col" class="px-6 py-3">Participants</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    foreach ($events as $event) {
        echo '<tr class="bg-white border-b hover:bg-gray-50">';
        echo '<td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">' . htmlspecialchars($event['title']) . '</td>';
        echo '<td class="px-6 py-4">' . htmlspecialchars($event['start_date']) . '</td>';
        echo '<td class="px-6 py-4">' . htmlspecialchars($event['end_date']) . '</td>';
        echo '<td class="px-6 py-4">' . htmlspecialchars($event['location']) . '</td>';
        echo '<td class="px-6 py-4">' . htmlspecialchars(substr($event['description'], 0, 100)) . (strlen($event['description']) > 100 ? '...' : '') . '</td>';
        echo '<td class="px-6 py-4">';
        if (!empty($event['banner'])) {
            echo '<img src="' . htmlspecialchars($event['banner']) . '" alt="Event Banner" class="w-20 h-20 object-cover rounded">';
        } else {
            echo '<span class="text-gray-400">No banner</span>';
        }
        echo '</td>';
        echo '<td class="px-6 py-4">' . htmlspecialchars($event['status']) . '</td>';
        echo '<td class="px-6 py-4 text-center font-medium">' . htmlspecialchars($event['registered_users']) . '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
    echo '</div>';
} else {
    echo '<div class="mt-12 text-center">';
    echo '<p class="text-gray-600 text-lg">No events found.</p>';
    echo '</div>';
    }
}