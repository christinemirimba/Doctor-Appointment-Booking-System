<?php
session_start();
include '../connection.php';

// Check if the user is authenticated and is a doctor
if (!isset($_SESSION['authenticated']) || $_SESSION['usertype'] !== 'd') {
    header('Location: ../login.php');
    exit();
}

// Get the logged-in doctor's ID from the session
$doctor_email = $_SESSION['email'];
$query = "SELECT did FROM doctor WHERE demail = ?";
$stmt = $database->prepare($query);
$stmt->bind_param("s", $doctor_email);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($doctor_id);
$stmt->fetch();
$stmt->close();

// Fetch distinct notifications for the patients of the logged-in doctor
$query = "SELECT DISTINCT n.id, n.message, n.status, n.created_at 
          FROM notifications n 
          JOIN appointment a ON n.pid = a.pid 
          WHERE a.did = ? 
          ORDER BY n.created_at DESC";
$stmt = $database->prepare($query);
$stmt->bind_param("i", $doctor_id);
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
    <link rel="stylesheet" href="doctor.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="date">
                <?php echo date("F j, Y"); ?>
            </div>
            <div class="profile-container">
                <img src="../images/user.png" alt="User" class="user-icon">
                <p class="profile-email"><?php echo htmlspecialchars($_SESSION['email']); ?></p>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
            <nav>
                <ul>
                    <li><a href="doctor.php">Home</a></li>
                    <li><a href="mypatients.php">My Patients</a></li>
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
