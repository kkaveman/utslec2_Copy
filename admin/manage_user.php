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
        <div class="flex justify-between items-center mb-6">
            <div>
            <p class="text-gray-600 mb-6">Admin dashboard</p>
            </div>
            <a href="dashboard.php" class="text-blue-600 hover:underline">← Back to Dashboard</a>
        </div>
        

<?php
// Handle delete action if submitted
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    try {
        $delete_sql = "DELETE FROM user WHERE user_id = :user_id";
        $delete_stmt = $db->prepare($delete_sql);
        $delete_stmt->execute(['user_id' => $user_id]);
        echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mt-4" role="alert">';
        echo '<span class="block sm:inline">User successfully deleted.</span>';
        echo '</div>';
    } catch (PDOException $e) {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">';
        echo '<span class="block sm:inline">Error deleting user: ' . htmlspecialchars($e->getMessage()) . '</span>';
        echo '</div>';
    }
}

// Fetch events from the database with participant count
$sql = "SELECT user_id,first_name,last_name,email
        FROM user ORDER BY first_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$user = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Display events in a table
if (count($user) > 0) {
    echo '<div class="m-6">';
    echo '<h3 class="text-2xl font-semibold text-blue-600 mb-4">User List</h3>';
    echo '<div class="overflow-x-auto shadow-md sm:rounded-lg">';
    echo '<table class="w-full text-sm text-left text-gray-500">';
    echo '<thead class="text-xs text-gray-700 uppercase bg-gray-50">';
    echo '<tr>';
    echo '<th scope="col" class="px-6 py-3">User ID</th>';
    echo '<th scope="col" class="px-6 py-3">First Name</th>';
    echo '<th scope="col" class="px-6 py-3">Last Name</th>';
    echo '<th scope="col" class="px-6 py-3">Email</th>';
    echo '<th scope="col" class="px-6 py-3">Action</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    foreach ($user as $event) {
        echo '<tr class="bg-white border-b hover:bg-gray-50">';
        echo '<td class="px-6 py-4">' . htmlspecialchars($event['user_id']) . '</td>';
        echo '<td class="px-6 py-4">' . htmlspecialchars($event['first_name']) . '</td>';
        echo '<td class="px-6 py-4">' . htmlspecialchars($event['last_name']) . '</td>';
        echo '<td class="px-6 py-4">' . htmlspecialchars($event['email']) . '</td>';
        echo '<td class="px-6 py-4 flex space-x-3">';
        // User Events link
        echo '<a href="user_events.php?user_id=' . htmlspecialchars($event['user_id']) . '" class="font-medium text-purple-600 hover:underline">User events</a>';
        // Delete form
        echo '<form method="POST" class="inline" onsubmit="return confirm(\'Are you sure you want to delete this user?\')">';
        echo '<input type="hidden" name="user_id" value="' . htmlspecialchars($event['user_id']) . '">';
        echo '<button type="submit" name="delete_user" class="font-medium text-red-600 hover:underline">Delete</button>';
        echo '</form>';
        echo '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
    echo '</div>';
} else {
    echo '<div class="mt-12 text-center">';
    echo '<p class="text-gray-600 text-lg">No users found.</p>';
    echo '</div>';
    }
}