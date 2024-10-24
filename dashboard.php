<?php
//dasboard.php
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
else{
    require('admin/admin_nav.php');
} 

$sql = "SELECT e.*, COUNT(ue.user_id) as registered_users 
        FROM event e 
        LEFT JOIN user_event ue ON e.event_id = ue.event_id 
        GROUP BY e.event_id 
        ORDER BY e.start_date DESC";
$stmt = $db->prepare($sql);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

        .view-details {
            display: inline-block;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            margin-top: 1rem;
            transition: color 0.3s ease;
        }

        .view-details:hover {
            color: #764ba2;
        }

        .badge {
            position: absolute;
            top: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-status {
            left: 1rem;
            background: rgba(255, 255, 255, 0.9);
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
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>! 👋</h2>
            <p>Here's what's happening with your events</p>
        </div>

        <!-- Search and Filters Section -->
        <div class="filters">
            <div class="search-container">
                <input type="text" 
                       id="searchEvents" 
                       class="search-input"
                       placeholder="Search events by title or location...">
            </div>
            
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="all">All Events</button>
                <button class="filter-btn" data-filter="upcoming">Upcoming</button>
                <button class="filter-btn" data-filter="active">Active</button>
                <button class="filter-btn" data-filter="completed">Completed</button>
                <button class="filter-btn" data-filter="registered">
                    <i class="fas fa-check-circle"></i> Registered
                </button>
            </div>
        </div>

        <!-- Events Grid -->
        <?php if (count($events) > 0): ?>
            <div class="events-grid">
                <?php foreach ($events as $event): ?>
                    <div class="event-card" 
                         data-status="<?php echo htmlspecialchars($event['status']); ?>"
                         data-registered="<?php echo in_array($event['event_id'], $user_registered_events) ? 'true' : 'false'; ?>">
                        
                        <div class="event-image">
                            <?php if (!empty($event['banner'])): ?>
                                <img src="admin/<?php echo htmlspecialchars($event['banner']); ?>" 
                                     alt="<?php echo htmlspecialchars($event['title']); ?>">
                            <?php else: ?>
                                <div class="placeholder-image">
                                    <i class="fas fa-image"></i>
                                </div>
                            <?php endif; ?>
                            
                            <span class="badge badge-status">
                                <?php echo ucfirst(htmlspecialchars($event['status'])); ?>
                            </span>
                            
                            <?php if (in_array($event['event_id'], $user_registered_events)): ?>
                                <span class="badge badge-registered">
                                    <i class="fas fa-check-circle"></i> Registered
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="event-content">
                            <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                            
                            <div class="event-info">
                                <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['location']); ?></p>
                                <p><i class="fas fa-calendar-alt"></i> <?php 
                                    $start_date = new DateTime($event['start_date']);
                                    echo $start_date->format('M d, Y');
                                ?></p>
                                <p><i class="fas fa-users"></i> <?php echo $event['registered_users']; ?> registered</p>
                            </div>

                            <a href="event_details.php?event_id=<?php echo $event['event_id']; ?>" 
                               class="view-details">
                                View Details <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="header">
                <div class="text-center">
                    <i class="fas fa-calendar-times fa-3x" style="color: #667eea;"></i>
                    <h3 class="event-title">No Events Found</h3>
                    <p>Check back later for upcoming events!</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchEvents');
            const filterButtons = document.querySelectorAll('.filter-btn');
            const eventCards = document.querySelectorAll('.event-card');
            let currentFilter = 'all';

            function filterEvents() {
                const searchTerm = searchInput.value.toLowerCase();
                
                eventCards.forEach(card => {
                    const title = card.querySelector('.event-title').textContent.toLowerCase();
                    const location = card.querySelector('.fa-map-marker-alt').parentElement.textContent.toLowerCase();
                    const status = card.dataset.status;
                    const isRegistered = card.dataset.registered === 'true';
                    
                    const matchesSearch = title.includes(searchTerm) || location.includes(searchTerm);
                    const matchesFilter = 
                        currentFilter === 'all' || 
                        (currentFilter === 'registered' && isRegistered) ||
                        (currentFilter !== 'registered' && status === currentFilter);
                    
                    card.style.display = matchesSearch && matchesFilter ? '' : 'none';
                });

                const visibleCards = document.querySelectorAll('.event-card[style=""]').length;
                const noResultsDiv = document.querySelector('.no-results');
                
                if (visibleCards === 0) {
                    if (!noResultsDiv) {
                        const message = document.createElement('div');
                        message.className = 'no-results header';
                        message.innerHTML = `
                            <div class="text-center">
                                <i class="fas fa-search fa-3x" style="color: #667eea;"></i>
                                <h3 class="event-title">No Events Found</h3>
                                <p>Try adjusting your search or filter criteria</p>
                            </div>
                        `;
                        document.querySelector('.events-grid').after(message);
                    }
                } else {
                    if (noResultsDiv) {
                        noResultsDiv.remove();
                    }
                }
            }

            searchInput.addEventListener('input', filterEvents);

            filterButtons.forEach(button => {
                button.addEventListener('click', () => {
                    filterButtons.forEach(btn => {
                        btn.classList.remove('active');
                    });
                    button.classList.add('active');
                    
                    currentFilter = button.dataset.filter;
                    filterEvents();
                });
            });
        });
    </script>
</body>
</html>