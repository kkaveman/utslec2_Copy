<?php
session_start();
require_once('../db.php');
require '../vendor/autoload.php'; // Make sure you have PhpSpreadsheet installed via Composer

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if(!isset($_SESSION['username']) && !isset($_SESSION['user_id']) && !isset($_SESSION['is_admin'])){
    echo "You don't have access to this page";
    echo "<a href=\"..\login.php\">Login</a>";
    exit;
} elseif($_SESSION['is_admin'] == 0){
    echo "You don't have access to this page<br>";
    echo "<a href='..\dashboard.php'>to Home page</a>";
    exit;
}

require("admin_nav.php");

// Get event_id from URL parameter
$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

if ($event_id === 0) {
    echo "Invalid event ID";
    exit;
}

// Get event details
$event_sql = "SELECT title FROM event WHERE event_id = ?";
$event_stmt = $db->prepare($event_sql);
$event_stmt->execute([$event_id]);
$event = $event_stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    echo "Event not found";
    exit;
}

// Handle Excel export
if (isset($_POST['export_excel'])) {
    // Get participants for export
    $sql = "SELECT u.user_id, u.first_name, u.last_name, u.email 
            FROM user u 
            JOIN user_event ue ON u.user_id = ue.user_id 
            WHERE ue.event_id = ?
            ORDER BY u.user_id";
    $stmt = $db->prepare($sql);
    $stmt->execute([$event_id]);
    $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Create new Spreadsheet object
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set headers
    $sheet->setCellValue('A1', 'User ID');
    $sheet->setCellValue('B1', 'First Name');
    $sheet->setCellValue('C1', 'Last Name');
    $sheet->setCellValue('D1', 'Email');

    // Style the header row
    $sheet->getStyle('A1:D1')->getFont()->setBold(true);
    
    // Add data
    $row = 2;
    foreach ($participants as $participant) {
        $sheet->setCellValue('A' . $row, $participant['user_id']);
        $sheet->setCellValue('B' . $row, $participant['first_name']);
        $sheet->setCellValue('C' . $row, $participant['last_name']);
        $sheet->setCellValue('D' . $row, $participant['email']);
        $row++;
    }

    // Auto-size columns
    foreach(range('A','D') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Create writer and output file
    $writer = new Xlsx($spreadsheet);
    
    // Sanitize filename more thoroughly
    $trimmedtitle = preg_replace('/[^a-zA-Z0-9-_]/', '', trim($event['title'])); 
        
    // Set headers for download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $trimmedtitle . '_participants.xlsx"');
    header('Cache-Control: max-age=0');

    // Make sure no output has been sent before
    ob_clean();
    flush();

    // Save file to PHP output stream
    $writer->save('php://output');
    exit;
}

// Get participants for display
$sql = "SELECT u.user_id, u.first_name, u.last_name, u.email 
        FROM user u 
        JOIN user_event ue ON u.user_id = ue.user_id 
        WHERE ue.event_id = ?
        ORDER BY u.user_id";
$stmt = $db->prepare($sql);
$stmt->execute([$event_id]);
$participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Participants - <?php echo htmlspecialchars($event['title']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-8 p-6 bg-white rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-blue-600">
                Participants for: <?php echo htmlspecialchars($event['title']); ?>
            </h1>
            <div class="space-x-4">
                <form method="POST" class="inline">
                    <button type="submit" name="export_excel" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">
                        Export to Excel
                    </button>
                </form>
                <a href="management_event.php" class="text-blue-600 hover:underline">← Back to Event Management</a>
            </div>
        </div>

        <?php if (count($participants) > 0): ?>
            <div class="overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3">User ID</th>
                            <th scope="col" class="px-6 py-3">First Name</th>
                            <th scope="col" class="px-6 py-3">Last Name</th>
                            <th scope="col" class="px-6 py-3">Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($participants as $participant): ?>
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4"><?php echo htmlspecialchars($participant['user_id']); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($participant['first_name']); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($participant['last_name']); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($participant['email']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-4">
                <p class="text-gray-600">No participants found for this event.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>