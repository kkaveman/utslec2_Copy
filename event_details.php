<?php
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

// Check if user is logged in and registration status
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

// Handle registration
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
            $register_stmt = $db->prepare($register_sql);
            
            try {
                $register_stmt->execute([$event_id, $user_id]);
                $registration_message = "You have successfully registered for this event!";
                $is_registered = true;
            } catch (PDOException $e) {
                $registration_message = "An error occurred. Please try again later.";
            }
        }
    }
    // Handle cancellation
    elseif (isset($_POST['cancel'])) {
        if (!isset($_SESSION['user_id'])) {
            $registration_message = "Please log in to cancel your registration.";
        } elseif (!$is_registered) {
            $registration_message = "You are not registered for this event.";
        } elseif (!$can_register) {
            $registration_message = "Cannot cancel registration for completed events.";
        } else {
            $cancel_sql = "DELETE FROM user_event WHERE event_id = ? AND user_id = ?";
            $cancel_stmt = $db->prepare($cancel_sql);
            
            try {
                $cancel_stmt->execute([$event_id, $user_id]);
                $registration_message = "Your registration has been cancelled successfully.";
                $is_registered = false;
            } catch (PDOException $e) {
                $registration_message = "An error occurred while cancelling your registration.";
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
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include("navbar.php"); ?>

    <!-- Hero Section -->
    <div class="relative h-[500px] overflow-hidden">
        <img src="admin/<?= htmlspecialchars($event['banner']) ?>" 
             alt="<?= htmlspecialchars($event['title']) ?>" 
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 p-8">
            <div class="container mx-auto">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                    <?= $event['status'] === 'completed' ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800' ?> mb-4">
                    <span class="w-2 h-2 rounded-full 
                        <?= $event['status'] === 'completed' ? 'bg-gray-400' : 'bg-green-400' ?> mr-2"></span>
                    <?= ucfirst(htmlspecialchars($event['status'])) ?>
                </span>
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4"><?= htmlspecialchars($event['title']) ?></h1>
                <div class="flex flex-wrap gap-6 text-white/90">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-calendar-alt"></i>
                        <span><?= htmlspecialchars($event['start_date']) ?> - <?= htmlspecialchars($event['end_date']) ?></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?= htmlspecialchars($event['location']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="mx-5 mb-5">
            <a href="dashboard.php" class="text-blue-600 hover:underline">← Back to Dashboard</a>  
        </div>
        
        <?php if ($registration_message): ?>
            <div class="mb-6 p-4 rounded-lg <?= $is_registered ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200' ?>">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas <?= $is_registered ? 'fa-check-circle text-green-400' : 'fa-exclamation-circle text-yellow-400' ?>"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm <?= $is_registered ? 'text-green-800' : 'text-yellow-800' ?>"><?= $registration_message ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-8">
                <!-- About Section -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-2xl font-semibold mb-4">About This Event</h2>
                    <p class="text-gray-600 leading-relaxed whitespace-pre-line">
                        <?= nl2br(htmlspecialchars($event['description'])) ?>
                    </p>
                </div>

                <!-- Image Gallery -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-2xl font-semibold mb-4">Event Gallery</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <?php foreach ($images as $image): ?>
                            <div class="relative group rounded-lg overflow-hidden">
                                <img src="admin/event_images/<?= htmlspecialchars($image) ?>" 
                                     alt="Event Image" 
                                     class="w-full h-48 object-cover transition duration-300 group-hover:scale-110">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                    <button onclick="openImage('admin/event_images/<?= htmlspecialchars($image) ?>')" 
                                            class="text-white">
                                        <i class="fas fa-expand-alt"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Registration Card -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="text-center">
                        <?php if ($can_register): ?>
                            <?php if ($is_registered): ?>
                                <div class="mb-4">
                                    <i class="fas fa-check-circle text-4xl text-green-500"></i>
                                </div>
                                <h3 class="text-2xl font-semibold mb-2">You're Registered!</h3>
                                <p class="text-gray-600 mb-6">We look forward to seeing you at the event</p>
                                <form method="POST" action="" onsubmit="return confirm('Are you sure you want to cancel your registration?');">
                                    <button type="submit" name="cancel" 
                                            class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 flex items-center justify-center gap-2">
                                        <i class="fas fa-times-circle"></i>
                                        Cancel Registration
                                    </button>
                                </form>
                            <?php elseif (isset($_SESSION['user_id'])): ?>
                                <div class="mb-4">
                                    <i class="fas fa-ticket-alt text-4xl text-blue-500"></i>
                                </div>
                                <h3 class="text-2xl font-semibold mb-2">Ready to Join?</h3>
                                <p class="text-gray-600 mb-6">Secure your spot at this amazing event</p>
                                <form method="POST" action="">
                                    <button type="submit" name="register" 
                                            class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 flex items-center justify-center gap-2">
                                        <i class="fas fa-check-circle"></i>
                                        Register Now
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="mb-4">
                                    <i class="fas fa-user-lock text-4xl text-gray-400"></i>
                                </div>
                                <h3 class="text-xl font-semibold mb-2">Want to Join?</h3>
                                <p class="text-gray-600 mb-6">Please log in to register for this event</p>
                                <a href="login.php" 
                                class="inline-block w-full bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-300">
                                    Log In to Register
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="mb-4">
                                <i class="fas fa-clock text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="text-xl font-semibold mb-2">Registration Closed</h3>
                            <p class="text-gray-600">This event has been completed</p>
                        <?php endif; ?>
                    </div>
                </div>


                <!-- Event Details Card -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-xl font-semibold mb-4">Event Details</h3>
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                                <i class="fas fa-calendar text-blue-500"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Date</p>
                                <p class="font-medium"><?= htmlspecialchars($event['start_date']) ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                                <i class="fas fa-map-marker-alt text-blue-500"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Location</p>
                                <p class="font-medium"><?= htmlspecialchars($event['location']) ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                                <i class="fas fa-clock text-blue-500"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Duration</p>
                                <p class="font-medium">
                                    <?php
                                        $start = new DateTime($event['start_date']);
                                        $end = new DateTime($event['end_date']);
                                        $duration = $start->diff($end);
                                        echo $duration->days + 1 . ' days';
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Share Button -->
                <button onclick="shareEvent()" 
                        class="w-full bg-white hover:bg-gray-50 text-gray-700 font-semibold py-3 px-6 rounded-lg border transition duration-300 flex items-center justify-center gap-2">
                    <i class="fas fa-share-alt"></i>
                    Share Event
                </button>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black/90 hidden items-center justify-center z-50">
        <button onclick="closeModal()" class="absolute top-4 right-4 text-white text-xl">
            <i class="fas fa-times"></i>
        </button>
        <img id="modalImage" src="" alt="Event Image" class="max-w-[90%] max-h-[90vh] object-contain">
    </div>

    <script>
        function openImage(src) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modalImage.src = src;
        }

        function closeModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function shareEvent() {
            if (navigator.share) {
                navigator.share({
                    title: '<?= htmlspecialchars($event['title']) ?>',
                    text: 'Check out this event: <?= htmlspecialchars($event['title']) ?>',
                    url: window.location.href
                });
            } else {
                // Fallback: Copy URL to clipboard
                navigator.clipboard.writeText(window.location.href)
                    .then(() => alert('Event URL copied to clipboard!'))
                    .catch(() => alert('Unable to copy URL'));
            }
        }
    </script>
</body>
</html>