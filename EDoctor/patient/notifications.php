notifications.php                                                                                                                                                 <?php
session_start();
include '../connection.php';

// Check if the user is authenticated and is a patient
if (!isset($_SESSION['authenticated']) || $_SESSION['usertype'] !== 'p') {
    header('Location: ../login.php');
    exit();
}

// Fetch the patient's ID from the session
$patient_id = $_SESSION['patient_id'];

// Fetch notifications for the logged-in patient from the database
$query = "SELECT * FROM notifications WHERE pid = ? ORDER BY created_at DESC";
$stmt = $database->prepare($query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}
$stmt->close();
$database->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="patient.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="date">
                <?php echo date("F j, Y"); ?>
            </div>
            <div class="profile-container">
                <img src="../images/user.png" alt="User" class="user-icon">
                <p class="profile-email"><?php echo $_SESSION['email']; ?></p>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
            <nav>
                <ul>
                    <li><a href="patient.php">Home</a></li>
                    <li><a href="mybookings.php">My Bookings</a></li>
                    <li><a href="appointment.php">Make Appointment</a></li>
                    <li><a href="settings.php">Settings</a></li>
                    <li><a href="notifications.php">Notifications</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h2>Notifications</h2>
        <?php if (count($notifications) > 0): ?>
            <ul>
                <?php foreach ($notifications as $notification): ?>
                    <li>
                        <p><?php echo htmlspecialchars($notification['message']); ?></p>
                        <small><?php echo htmlspecialchars($notification['created_at']); ?></small>
                        <hr>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No notifications found.</p>
        <?php endif; ?>
    </div>
</body>
</html>        