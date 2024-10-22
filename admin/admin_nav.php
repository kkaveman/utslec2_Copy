<?php
session_start();
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <nav class="bg-blue-600 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <a href="#" class="text-white text-2xl font-bold">Event Manager (ADMIN)</a>
            <div class="space-x-4">
                <a href="dashboard.php" class="text-white hover:text-blue-200">Dashboard</a>
                <a href="profile.php" class="text-white hover:text-blue-200">Profile</a>
                <a href="../dashboard.php" class="text-white hover:text-blue-200">User Dashboard</a>
                <a href="../logout.php" class="text-white hover:text-blue-200">Logout</a>
            </div>
        </div>
    </nav>
</body>
</html>