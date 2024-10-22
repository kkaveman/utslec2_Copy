<?php
//admin/user_events.php
session_start();

require_once('../db.php');

// Initialize variables
if(!isset($_SESSION['username']) || !isset($_SESSION['user_id']) || !isset($_SESSION['is_admin'])){
    echo "You don't have access to this page";
    echo "<a href=\"../login.php\">Login</a>";
    exit();
}

if($_SESSION['is_admin'] == 0){
    echo "You don't have access to this page<br>";
    echo "<a href='../dashboard.php'>to Home page</a>";
    exit();
}

// Check if user_id is provided
if(!isset($_GET['user_id'])) {
    echo "No user specified";
    exit();
}

$user_id = $_GET['user_id'];

// Get user details
$user_sql = "SELECT first_name, last_name, email FROM user WHERE user_id = :user_id";
$user_stmt = $db->prepare($user_sql);
$user_stmt->execute(['user_id' => $user_id]);
$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

if(!$user) {
    echo "User not found";
    exit();
}

require("admin_nav.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Events - Event Manager (ADMIN)</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <main class="container mx-auto mt-8 p-6 bg-white rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-semibold text-blue-600">Events for <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
                <p class="text-gray-600 mt-2">Email: <?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            <a href="manage_user.php" class="text-blue-600 hover:underline">← Back to User Management</a>
        </div>

        <?php
        // Fetch events this user has registered for
        $events_sql = "SELECT e.title, e.start_date, e.end_date, e.location
                      FROM event e
                      INNER JOIN user_event ue ON e.event_id = ue.event_id
                      WHERE ue.user_id = :user_id
                      ORDER BY e.start_date DESC";
        
        $events_stmt = $db->prepare($events_sql);
        $events_stmt->execute(['user_id' => $user_id]);
        $events = $events_stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($events) > 0) {
            echo '<div class="overflow-x-auto shadow-md sm:rounded-lg">';
            echo '<table class="w-full text-sm text-left text-gray-500">';
            echo '<thead class="text-xs text-gray-700 uppercase bg-gray-50">';
            echo '<tr>';
            echo '<th scope="col" class="px-6 py-3">Event Title</th>';
            echo '<th scope="col" class="px-6 py-3">Start Date</th>';
            echo '<th scope="col" class="px-6 py-3">End Date</th>';
            echo '<th scope="col" class="px-6 py-3">Location</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            
            foreach ($events as $event) {
                echo '<tr class="bg-white border-b hover:bg-gray-50">';
                echo '<td class="px-6 py-4 font-medium text-gray-900">' . htmlspecialchars($event['title']) . '</td>';
                echo '<td class="px-6 py-4">' . date('F j, Y g:i A', strtotime($event['start_date'])) . '</td>';
                echo '<td class="px-6 py-4">' . date('F j, Y g:i A', strtotime($event['end_date'])) . '</td>';
                echo '<td class="px-6 py-4">' . htmlspecialchars($event['location']) . '</td>';
                echo '</tr>';
            }
            
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        } else {
            echo '<div class="text-center py-8">';
            echo '<p class="text-gray-600 text-lg">This user has not registered for any events.</p>';
            echo '</div>';
        }
        ?>
    </main>
</body>
</html>
