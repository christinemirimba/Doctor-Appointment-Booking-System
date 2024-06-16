<?php
session_start();
include '../connection.php';

// Check if the user is authenticated and is a doctor
if (!isset($_SESSION['authenticated']) || $_SESSION['usertype'] !== 'd') {
    header('Location: ../login.php');
    exit();
}

// Fetch the appointment ID from the URL
$appid = $_GET['appid'];

// Retrieve the appointment details
$query = "SELECT * FROM appointment WHERE appid = ?";
$stmt = $database->prepare($query);
$stmt->bind_param("i", $appid);
$stmt->execute();
$result = $stmt->get_result();
$appointment = $result->fetch_assoc();
$stmt->close();

// Handle the form submission to reschedule the appointment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_date = $_POST['new_date'];
    $new_time = $_POST['new_time'];

    // Update the appointment in the database
    $update_query = "UPDATE appointment SET appdate = ?, apptime = ? WHERE appid = ?";
    $stmt = $database->prepare($update_query);
    $stmt->bind_param("ssi", $new_date, $new_time, $appid);
    $stmt->execute();
    $stmt->close();

    // Insert a notification for the patient
    $pid = $appointment['pid'];
    $message = "Your appointment on " . $appointment['appdate'] . " at " . $appointment['apptime'] . " has been rescheduled to $new_date at $new_time.";
    $notif_query = "INSERT INTO notifications (pid, message) VALUES (?, ?)";
    $stmt = $database->prepare($notif_query);
    $stmt->bind_param("is", $pid, $message);
    $stmt->execute();
    $stmt->close();

    header('Location: appointment.php');
    exit();
}
?>

<!-- HTML form to reschedule the appointment -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reschedule Appointment</title>
    <link rel="stylesheet" href="doctor.css">
</head>
<body>
    <div class="container">
        <h2>Reschedule Appointment</h2>
        <form method="post">
            <label for="new_date">New Date:</label>
            <input type="date" id="new_date" name="new_date" required>
            <label for="new_time">New Time:</label>
            <input type="time" id="new_time" name="new_time" required>
            <button type="submit">Reschedule</button>
        </form>
    </div>
</body>
</html>
