<?php
//event_details.php (updated)
session_start();
require_once('db.php');

if (!isset($_GET['event_id'])) {
    die("Event ID not provided");
}

$event_id = $_GET['event_id'];

// Fetch event details
$event_sql = "SELECT * FROM event WHERE event_id = ?";
$event_stmt = $db->prepare($event_sql);
$event_stmt->execute([$event_id]);
$event = $event_stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    die("Event not found");
}

// Fetch event images
$images_sql = "SELECT event_img FROM event_img WHERE event_id = ?";
$images_stmt = $db->prepare($images_sql);
$images_stmt->execute([$event_id]);
$images = $images_stmt->fetchAll(PDO::FETCH_COLUMN);

$is_registered = false;
$registration_message = '';
$can_register = ($event['status'] !== 'completed');

// Check registration status
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $check_registration_sql = "SELECT * FROM user_event WHERE event_id = ? AND user_id = ?";
    $check_registration_stmt = $db->prepare($check_registration_sql);
    $check_registration_stmt->execute([$event_id, $user_id]);
    $existing_registration = $check_registration_stmt->fetch();
    
    if ($existing_registration) {
        $is_registered = true;
        $registration_message = "You are already registered for this event.";
    }
}

// Handle registration/cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register'])) {
        if (!isset($_SESSION['user_id'])) {
            $registration_message = "Please log in to register for this event.";
        } elseif ($is_registered) {
            $registration_message = "You are already registered for this event.";
        } elseif (!$can_register) {
            $registration_message = "Registration is closed for this event.";
        } else {
            $register_sql = "INSERT INTO user_event (event_id, user_id) VALUES (?, ?)";
            try {
                $db->prepare($register_sql)->execute([$event_id, $user_id]);
                $registration_message = "Successfully registered!";
                $is_registered = true;
            } catch (PDOException $e) {
                $registration_message = "Registration failed. Please try again.";
            }
        }
    } elseif (isset($_POST['cancel'])) {
        if (!$is_registered) {
            $registration_message = "You are not registered for this event.";
        } else {
            $cancel_sql = "DELETE FROM user_event WHERE event_id = ? AND user_id = ?";
            try {
                $db->prepare($cancel_sql)->execute([$event_id, $user_id]);
                $registration_message = "Registration cancelled successfully.";
                $is_registered = false;
            } catch (PDOException $e) {
                $registration_message = "Cancellation failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($event['title']) ?> - Event Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Header Section */
        .header {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            transition: transform 0.3s ease;
        }

        .header:hover {
            transform: translateY(-5px);
        }

        .header h2 {
            color: #333;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: #666;
        }

        /* Search and Filters */
        .filters {
            background: rgba(255, 255, 255, 0.95);
            padding: 1.5rem;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            transition: transform 0.3s ease;
        }

        .filters:hover {
            transform: translateY(-5px);
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 2.5rem;
            border: 2px solid #e1e1e1;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 10px rgba(102, 126, 234, 0.2);
        }

        .filter-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 0.25rem;
        }

        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .filter-btn.active {
            background: linear-gradient(to right, #667eea, #764ba2);
            color: white;
        }

        /* Event Cards */
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            padding: 1rem 0;
        }

        .event-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .event-image {
            height: 200px;
            position: relative;
        }

        .event-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .event-content {
            padding: 1.5rem;
        }

        .event-title {
            color: #333;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .event-info {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .view-details:hover {
            color: #764ba2;
        }

        .badge-registered {
            right: 1rem;
            background: rgba(72, 187, 120, 0.9);
            color: white;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .header, .filters {
                padding: 1rem;
            }

            .events-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include("navbar.php"); ?>

    <div class="container mx-auto px-4 py-8">
        <!-- Back Button -->
        <a href="dashboard.php" class="mx-3 inline-flex items-center text-white text-lg hover:text-gray-200 mb-6 transition duration-300">
            <i class="fas fa-arrow-left mr-2"></i>
            <b>
                Back to Dashboard
            </b>
        </a>

        <!-- Main Content -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden mb-8">
            <!-- Event Banner -->
            <div class="relative h-96">
                <img src="admin/<?= htmlspecialchars($event['banner']) ?>" 
                     alt="<?= htmlspecialchars($event['title']) ?>" 
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent"></div>
                
                <!-- Event Status Badge -->
                <div class="absolute top-6 left-6">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold 
                        <?= $event['status'] === 'completed' ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800' ?>">
                        <span class="w-2 h-2 rounded-full 
                            <?= $event['status'] === 'completed' ? 'bg-gray-400' : 'bg-green-400' ?> mr-2"></span>
                        <?= ucfirst(htmlspecialchars($event['status'])) ?>
                    </span>
                </div>

                <!-- Event Title & Basic Info -->
                <div class="absolute bottom-0 left-0 right-0 p-8">
                    <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                        <?= htmlspecialchars($event['title']) ?>
                    </h1>
                    <div class="flex flex-wrap gap-6 text-white/90">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar-alt"></i>
                            <?php 
                                $start_date = new DateTime($event['start_date']);
                                $end_date = new DateTime($event['end_date']);
                            ?>
                            <span><?= $start_date->format('M d, Y') ?> - <?= $end_date->format('M d, Y') ?></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?= htmlspecialchars($event['location']) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 p-8">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Registration Message -->
                    <?php if ($registration_message): ?>
                        <div class="rounded-xl p-4 <?= $is_registered ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200' ?>">
                            <div class="flex items-center">
                                <i class="fas <?= $is_registered ? 'fa-check-circle text-green-400' : 'fa-exclamation-circle text-yellow-400' ?> mr-3"></i>
                                <p class="<?= $is_registered ? 'text-green-800' : 'text-yellow-800' ?>">
                                    <?= $registration_message ?>
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- About Section -->
                    <div class="event-card rounded-2xl p-8">
                        <h2 class="text-2xl font-semibold mb-6">About This Event</h2>
                        <div class="prose max-w-none text-gray-600">
                            <?= nl2br(htmlspecialchars($event['description'])) ?>
                        </div>
                    </div>

                    <!-- Gallery Section -->
                    <?php if (!empty($images)): ?>
                        <div class="event-card rounded-2xl p-8">
                            <h2 class="text-2xl font-semibold mb-6">Event Gallery</h2>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <?php foreach ($images as $image): ?>
                                    <div class="relative group rounded-xl overflow-hidden bg-gray-100 aspect-square">
                                        <img src="admin/event_images/<?= htmlspecialchars($image) ?>" 
                                             alt="Event Image" 
                                             class="w-full h-full object-cover transition duration-300 group-hover:scale-110">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                            <button onclick="openImage('admin/event_images/<?= htmlspecialchars($image) ?>')" 
                                                    class="text-white hover:scale-110 transition duration-300">
                                                <i class="fas fa-expand-alt fa-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Registration Card -->
                    <div class="event-card rounded-2xl p-8 text-center">
                        <?php if ($can_register): ?>
                            <?php if ($is_registered): ?>
                                <div class="mb-6">
                                    <i class="fas fa-check-circle text-5xl text-green-500"></i>
                                    <h3 class="text-2xl font-semibold mt-4 mb-2">You're Registered!</h3>
                                    <p class="text-gray-600 mb-6">We look forward to seeing you</p>
                                    <form method="POST" onsubmit="return confirm('Cancel your registration?');">
                                        <button type="submit" name="cancel" 
                                                class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-300">
                                            Cancel Registration
                                        </button>
                                    </form>
                                </div>
                            <?php elseif (isset($_SESSION['user_id'])): ?>
                                <div class="mb-6">
                                    <i class="fas fa-ticket-alt text-5xl text-blue-500"></i>
                                    <h3 class="text-2xl font-semibold mt-4 mb-2">Ready to Join?</h3>
                                    <p class="text-gray-600 mb-6">Secure your spot now</p>
                                    <form method="POST">
                                        <button type="submit" name="register" 
                                                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-300">
                                            Register Now
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div class="mb-6">
                                    <i class="fas fa-user-lock text-5xl text-gray-400"></i>
                                    <h3 class="text-2xl font-semibold mt-4 mb-2">Want to Join?</h3>
                                    <p class="text-gray-600 mb-6">Please log in first</p>
                                    <a href="login.php" 
                                       class="block w-full bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-300">
                                        Log In to Register
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="mb-6">
                                <i class="fas fa-clock text-5xl text-gray-400"></i>
                                <h3 class="text-2xl font-semibold mt-4 mb-2">Event Completed</h3>
                                <p class="text-gray-600">Registration is closed</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Event Details Card -->
                    <div class="event-card rounded-2xl p-8">
                        <h3 class="text-xl font-semibold mb-6">Event Details</h3>
                        <div class="space-y-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                                    <i class="fas fa-calendar text-blue-500"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Date</p>
                                    <p class="font-medium">
                                        <?= $start_date->format('M d, Y') ?>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                                    <i class="fas fa-clock text-blue-500"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Duration</p>
                                    <p class="font-medium">
                                        <?php
                                            $duration = $start_date->diff($end_date);
                                            echo $duration->days + 1 . ' days';
                                        ?>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                                    <i class="fas fa-map-marker-alt text-blue-500"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Location</p>
                                    <p class="font-medium">
                                        <?= htmlspecialchars($event['location']) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- Social Share Section -->
                        <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                                    <i class="fas fa-share-alt text-blue-500"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Share Event</p>
                                    <div class="flex gap-3 mt-1">
                                        <button onclick="shareEvent('twitter')" class="text-gray-600 hover:text-blue-400 transition">
                                            <i class="fab fa-twitter fa-lg"></i>
                                        </button>
                                        <button onclick="shareEvent('facebook')" class="text-gray-600 hover:text-blue-600 transition">
                                            <i class="fab fa-facebook fa-lg"></i>
                                        </button>
                                        <button onclick="shareEvent('linkedin')" class="text-gray-600 hover:text-blue-700 transition">
                                            <i class="fab fa-linkedin fa-lg"></i>
                                        </button>
                                        <button onclick="copyEventLink()" class="text-gray-600 hover:text-gray-800 transition">
                                            <i class="fas fa-link fa-lg"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black/95 hidden z-50">
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <!-- Close Button -->
            <button onclick="closeModal()" 
                    class="absolute top-4 right-4 text-white/70 hover:text-white transition-colors duration-200">
                <i class="fas fa-times text-2xl"></i>
            </button>
            
            <!-- Navigation Buttons -->
            <button onclick="changeImage(-1)" 
                    class="absolute left-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-white transition-colors duration-200">
                <i class="fas fa-chevron-left text-2xl"></i>
            </button>
            <button onclick="changeImage(1)" 
                    class="absolute right-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-white transition-colors duration-200">
                <i class="fas fa-chevron-right text-2xl"></i>
            </button>
            
            <!-- Image Container -->
            <img id="modalImage" src="" alt="Event Image" 
                 class="max-w-[90vw] max-h-[90vh] object-contain rounded-lg">
        </div>
    </div>

    <script>
        // Global variables for image gallery
        const images = <?= json_encode($images) ?>;
        let currentImageIndex = 0;

        function openImage(src) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            modal.classList.remove('hidden');
            modalImage.src = src;
            
            // Find current image index
            currentImageIndex = images.findIndex(img => src.includes(img));
            
            // Add keyboard event listeners
            document.addEventListener('keydown', handleKeyPress);
        }

        function closeModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.add('hidden');
            document.removeEventListener('keydown', handleKeyPress);
        }

        function changeImage(direction) {
            currentImageIndex = (currentImageIndex + direction + images.length) % images.length;
            const modalImage = document.getElementById('modalImage');
            modalImage.src = `admin/event_images/${images[currentImageIndex]}`;
        }

        function handleKeyPress(e) {
            if (e.key === 'Escape') closeModal();
            if (e.key === 'ArrowLeft') changeImage(-1);
            if (e.key === 'ArrowRight') changeImage(1);
        }

        function shareEvent(platform) {
            const eventUrl = window.location.href;
            const eventTitle = <?= json_encode($event['title']) ?>;
            const shareText = `Check out this event: ${eventTitle}`;
            
            const shareUrls = {
                twitter: `https://twitter.com/intent/tweet?text=${encodeURIComponent(shareText)}&url=${encodeURIComponent(eventUrl)}`,
                facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(eventUrl)}`,
                linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(eventUrl)}`
            };

            if (shareUrls[platform]) {
                window.open(shareUrls[platform], '_blank', 'width=600,height=400');
            }
        }

        function copyEventLink() {
            navigator.clipboard.writeText(window.location.href)
                .then(() => {
                    // Create and show a temporary tooltip
                    const tooltip = document.createElement('div');
                    tooltip.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg';
                    tooltip.textContent = 'Link copied to clipboard!';
                    document.body.appendChild(tooltip);
                    
                    // Remove tooltip after 2 seconds
                    setTimeout(() => tooltip.remove(), 2000);
                })
                .catch(() => alert('Failed to copy link'));
        }

        // Close modal when clicking outside the image
        document.getElementById('imageModal').addEventListener('click', (e) => {
            if (e.target === e.currentTarget) closeModal();
        });
    </script>
</body>
</html>