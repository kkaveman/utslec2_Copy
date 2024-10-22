<?php
require_once('functions.php');
require_once('db.php');
session_start();

if(!isset($_SESSION['username']) && !isset($_SESSION['user_id'])){
    echo "You don't have access to this page";
    echo "<a href=\"login.php\">Login</a>";
    exit;
} 

if($_SESSION['is_admin'] == 1 || $_SESSION['is_admin'] == 0){
    require("navbar.php");
}

// Fetch events from the database
$sql = "SELECT e.*, COUNT(ue.user_id) as registered_users 
        FROM event e 
        LEFT JOIN user_event ue ON e.event_id = ue.event_id 
        GROUP BY e.event_id 
        ORDER BY e.start_date DESC";
$stmt = $db->prepare($sql);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get user's registered events
$user_events_sql = "SELECT event_id FROM user_event WHERE user_id = ?";
$user_events_stmt = $db->prepare($user_events_sql);
$user_events_stmt->execute([$_SESSION['user_id']]);
$user_registered_events = $user_events_stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Dashboard</title>
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
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>! 👋</h2>
            <p class="text-gray-600">Here's what's happening with your events</p>
        </div>

        <!-- Search and Filters Section -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-4 md:space-y-0">
                <!-- Search Bar -->
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" 
                               id="searchEvents" 
                               placeholder="Search events by title or location..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Filter Buttons -->
                <div class="flex flex-wrap gap-2">
                    <button class="filter-btn active px-4 py-2 rounded-lg bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors"
                            data-filter="all">
                        All Events
                    </button>
                    <button class="filter-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200 transition-colors"
                            data-filter="upcoming">
                        Upcoming
                    </button>
                    <button class="filter-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200 transition-colors"
                            data-filter="active">
                        Active
                    </button>
                    <button class="filter-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200 transition-colors"
                            data-filter="completed">
                        Completed
                    </button>
                    <button class="filter-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200 transition-colors"
                            data-filter="registered">
                        <i class="fas fa-check-circle mr-1"></i> Registered
                    </button>
                    
                </div>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Events Grid -->
        <?php if (count($events) > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($events as $event): ?>
                    <div class="event-card bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300" 
                         data-status="<?php echo htmlspecialchars($event['status']); ?>"
                         data-registered="<?php echo in_array($event['event_id'], $user_registered_events) ? 'true' : 'false'; ?>">

                         <!-- Event Image -->
                        <div class="relative h-48">
                            <?php if (!empty($event['banner'])): ?>
                                <img src="admin/<?php echo htmlspecialchars($event['banner']); ?>" 
                                     alt="<?php echo htmlspecialchars($event['title']); ?>" 
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400 text-4xl"></i>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Status Badge -->
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 rounded-full text-sm font-medium 
                                    <?php 
                                    switch($event['status']) {
                                        case 'upcoming':
                                            echo 'bg-blue-100 text-blue-800';
                                            break;
                                        case 'ongoing':
                                            echo 'bg-green-100 text-green-800';
                                            break;
                                        case 'completed':
                                            echo 'bg-gray-100 text-gray-800';
                                            break;
                                        default:
                                            echo 'bg-gray-100 text-gray-800';
                                    }
                                    ?>">
                                    <?php echo ucfirst(htmlspecialchars($event['status'])); ?>
                                </span>
                            </div>

                            <!-- Registration Badge -->
                            <?php if (in_array($event['event_id'], $user_registered_events)): ?>
                                <div class="absolute top-4 right-4">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Registered
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Event Content -->
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($event['title']); ?></h3>
                            
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-map-marker-alt w-5"></i>
                                    <span class="ml-2 text-sm"><?php echo htmlspecialchars($event['location']); ?></span>
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-calendar-alt w-5"></i>
                                    <span class="ml-2 text-sm">
                                        <?php 
                                            $start_date = new DateTime($event['start_date']);
                                            echo $start_date->format('M d, Y');
                                        ?>
                                    </span>
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-users w-5"></i>
                                    <span class="ml-2 text-sm"><?php echo $event['registered_users']; ?> registered</span>
                                </div>
                            </div>

                            <div class="flex justify-between items-center">
                                <a href="event_details.php?event_id=<?php echo $event['event_id']; ?>" 
                                   class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors">
                                    View Details
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                <div class="mb-4">
                    <i class="fas fa-calendar-times text-gray-400 text-5xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No Events Found</h3>
                <p class="text-gray-600">Check back later for upcoming events!</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Search and Filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchEvents');
            const filterButtons = document.querySelectorAll('.filter-btn');
            const eventCards = document.querySelectorAll('.event-card');
            let currentFilter = 'all';

            // Filter function
            function filterEvents() {
                const searchTerm = searchInput.value.toLowerCase();
                
                eventCards.forEach(card => {
                    const title = card.querySelector('h3').textContent.toLowerCase();
                    const location = card.querySelector('.fa-map-marker-alt').nextElementSibling.textContent.toLowerCase();
                    const status = card.dataset.status;
                    const isRegistered = card.dataset.registered === 'true';
                    
                    const matchesSearch = title.includes(searchTerm) || location.includes(searchTerm);
                    const matchesFilter = 
                        currentFilter === 'all' || 
                        (currentFilter === 'registered' && isRegistered) ||
                        (currentFilter !== 'registered' && status === currentFilter);
                    
                    card.style.display = matchesSearch && matchesFilter ? '' : 'none';
                });

                // Show no results message if needed
                const visibleCards = document.querySelectorAll('.event-card[style=""]').length;
                const noResultsDiv = document.querySelector('.no-results');
                
                if (visibleCards === 0) {
                    if (!noResultsDiv) {
                        const message = document.createElement('div');
                        message.className = 'no-results bg-white rounded-xl shadow-sm p-8 text-center mt-6';
                        message.innerHTML = `
                            <div class="mb-4">
                                <i class="fas fa-search text-gray-400 text-5xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">No Events Found</h3>
                            <p class="text-gray-600">Try adjusting your search or filter criteria</p>
                        `;
                        document.querySelector('.grid').after(message);
                    }
                } else {
                    if (noResultsDiv) {
                        noResultsDiv.remove();
                    }
                }
            }

            // Search event listener
            searchInput.addEventListener('input', filterEvents);

            // Filter button event listeners
            filterButtons.forEach(button => {
                button.addEventListener('click', () => {
                    // Update active button styles
                    filterButtons.forEach(btn => {
                        btn.classList.remove('active', 'bg-blue-100', 'text-blue-800');
                        btn.classList.add('bg-gray-100', 'text-gray-800');
                    });
                    button.classList.remove('bg-gray-100', 'text-gray-800');
                    button.classList.add('active', 'bg-blue-100', 'text-blue-800');
                    
                    // Update current filter and apply filtering
                    currentFilter = button.dataset.filter;
                    filterEvents();
                });
            });
        });
    </script>
</body>
</html>