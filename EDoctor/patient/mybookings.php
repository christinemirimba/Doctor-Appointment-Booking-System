<?php
session_start();
include '../connection.php';

// Check if the user is authenticated and is a patient
if (!isset($_SESSION['authenticated']) || $_SESSION['usertype'] !== 'p') {
    header('Location: ../login.php');
    exit();
}

// Fetch appointments for the logged-in patient from the database
$patient_id = $_SESSION['patient_id'];
$query = "SELECT * FROM appointment WHERE pid = $patient_id";
$result = $database->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link rel="stylesheet" href="patient.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="date">
                <!-- Display the current date here -->
                <?php echo date("F j, Y"); ?>
            </div>
            <div class="profile-container">
            <img src="../images/user.png" alt="User" style="width: 32px; height: 32px; border-radius: 50%;">

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
        <h2>My Bookings</h2>
        <?php
        // Display fetched appointments
        if ($result->num_rows > 0) {
            // Display appointment details
            while ($row = $result->fetch_assoc()) {
                echo "<div>";
                echo "<p>Appointment ID: " . $row['appid'] . "</p>";
                echo "<p>Date: " . $row['appdate'] . "</p>";
                echo "<p>Time: " . $row['apptime'] . "</p>";
                // Include additional appointment details as needed
                echo "<form action='cancel_appointment.php' method='POST'>";
                echo "<input type='hidden' name='appointment_id' value='" . $row['appid'] . "'>";
                echo "<button type='submit'>Cancel Appointment</button>";
                echo "</form>";
                echo "</div>";
            }
        } else {
            echo "<p>No appointments found.</p>";
        }
        ?>
</body>
</html>