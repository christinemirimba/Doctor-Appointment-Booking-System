<?php
session_start();
include '../connection.php';

// Check if the user is authenticated and is a patient
if (!isset($_SESSION['authenticated']) || $_SESSION['usertype'] !== 'p') {
    header('Location: ../login.php');
    exit();
}

// Fetch available doctors and their specializations from the database
$query = "SELECT d.did, d.dname, s.sname 
          FROM doctor d
          INNER JOIN specialization s ON d.specialization = s.sid";
$result = $database->query($query);

// Check if there are available doctors
if ($result->num_rows > 0) {
    // Display the form to book an appointment
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Book Appointment</title>
        <link rel="stylesheet" href="patient.css">
    </head>
    <body>
        <header>
            <div class="container">
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
            <h2>Book an Appointment</h2>
            <form action="submit_appointment.php" method="POST">
                <label for="doctor">Doctor:</label>
                <select name="doctor" id="doctor" required>
                    <?php 
                    // Display each available doctor and their specialization as options
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . $row['did'] . '">' . $row['dname'] . ' - ' . $row['sname'] . '</option>';
                    }
                    ?>
                </select>

                <label for="date">Date:</label>
                <input type="date" name="date" id="date" required>

                <label for="time">Time:</label>
                <input type="time" name="time" id="time" required>

                <button type="submit">Submit</button>
            </form>
        </div>
    </body>
    </html>
    <?php
} else {
    // No available doctors found
    echo "No doctors available for appointment.";
}

// Close the database connection
$database->close();
?>
