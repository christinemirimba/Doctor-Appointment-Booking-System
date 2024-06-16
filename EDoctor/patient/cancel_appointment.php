<?php
session_start();
include '../connection.php';

// Check if the user is authenticated and is a patient
if (!isset($_SESSION['authenticated']) || $_SESSION['usertype'] !== 'p') {
    header('Location: ../login.php');
    exit();
}

// Check if the appointment ID is provided
if(isset($_POST['appointment_id'])) {
    $appointment_id = $_POST['appointment_id'];

    // Prepare SQL statement to delete the appointment
    $delete_query = "DELETE FROM appointment WHERE appid = ?";
    $delete_statement = $database->prepare($delete_query);
    $delete_statement->bind_param("i", $appointment_id);

    // Execute the delete statement
    if ($delete_statement->execute()) {
        // Appointment successfully cancelled
        header('Location: mybookings.php');
        exit();
    } else {
        // Failed to cancel appointment
        echo "Failed to cancel appointment.";
    }

    // Close statement
    $delete_statement->close();
} else {
    // Redirect if appointment ID is not provided
    header('Location: mybookings.php');
    exit();
}

// Close the database connection
$database->close();
?>
