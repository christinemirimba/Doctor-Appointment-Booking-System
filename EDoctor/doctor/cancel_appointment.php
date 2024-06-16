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

// Delete the appointment from the database
$delete_query = "DELETE FROM appointment WHERE appid = ?";
$stmt = $database->prepare($delete_query);
$stmt->bind_param("i", $appid);
$stmt->execute();
$stmt->close();

// Insert a notification for the patient
$pid = $appointment['pid'];
$message = "Your appointment on " . $appointment['appdate'] . " at " . $appointment['apptime'] . " has been canceled.";
$notif_query = "INSERT INTO notifications (pid, message) VALUES (?, ?)";
$stmt = $database->prepare($notif_query);
$stmt->bind_param("is", $pid, $message);
$stmt->execute();
$stmt->close();

header('Location: appointment.php');
exit();
?>
