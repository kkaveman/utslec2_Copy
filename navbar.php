<?php
// Only start session if one hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
    <!-- Fixed position and backdrop-blur for the navbar -->
    <nav class="fixed w-full bg-blue-600/30 backdrop-blur-md shadow-lg z-10">
        <div class="container mx-auto">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <a href="#" class="text-white text-2xl font-bold">Event Manager</a>
                
                <!-- Mobile menu button -->
                <button id="mobile-menu-button" class="md:hidden text-white hover:text-blue-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <!-- Desktop menu -->
                <div class="hidden md:flex space-x-4">
                    <a href="dashboard.php" class="text-white hover:text-blue-200">Dashboard</a>
                    <a href="profile.php" class="text-white hover:text-blue-200">Profile</a>
                    <?php if ($is_admin): ?>
                        <a href="admin/dashboard.php" class="bg-white/20 backdrop-blur-sm text-white px-3 py-1 rounded hover:bg-white/30 transition duration-200">Admin</a>
                    <?php endif; ?>
                    <a href="logout.php" class="text-white hover:text-blue-200">Logout</a>
                </div>
            </div>

            <!-- Mobile menu -->
            <div id="mobile-menu" class="hidden md:hidden mt-4">
                <div class="flex flex-col space-y-3">
                    <a href="dashboard.php" class="text-white hover:text-blue-200">Dashboard</a>
                    <a href="profile.php" class="text-white hover:text-blue-200">Profile</a>
                    <?php if ($is_admin): ?>
                        <a href="admin/dashboard.php" class="bg-white/20 backdrop-blur-sm text-white px-3 py-1 rounded hover:bg-white/30 transition duration-200 w-fit">Admin</a>
                    <?php endif; ?>
                    <a href="logout.php" class="text-white hover:text-blue-200">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Add padding to the body to prevent content from hiding under fixed navbar -->
    <div class="pt-20">
        <!-- Your page content goes here -->
    </div>

    <script>
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>