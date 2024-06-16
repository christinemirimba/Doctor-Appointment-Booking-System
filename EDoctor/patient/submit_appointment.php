<?php
session_start();
include '../connection.php';

// Check if the user is authenticated and is a patient
if (!isset($_SESSION['authenticated']) || $_SESSION['usertype'] !== 'p') {
    header('Location: ../login.php');
    exit();
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extract form data
    $doctorId = $_POST['doctor'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $patientId = $_SESSION['patient_id']; // Assuming you have stored patient id in session

    // Insert appointment into database
    $stmt = $database->prepare("INSERT INTO appointment (appdate, apptime, pid, did) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $date, $time, $patientId, $doctorId);
    $stmt->execute();
    $stmt->close();

    // Fetch appointment details
    $result = $database->query("SELECT appointment.appid, appointment.appdate, appointment.apptime, doctor.dname, specialization.sname FROM appointment
    JOIN doctor ON appointment.did = doctor.did
    JOIN specialization ON doctor.specialization = specialization.sname
    WHERE appointment.pid = $patientId ORDER BY appointment.appdate DESC, appointment.apptime DESC LIMIT 1");
    $appointment = $result->fetch_assoc();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Details</title>
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
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <?php if (isset($appointment)) : ?>
            <h2>Appointment Details</h2>
            <p>Appointment ID: <?php echo $appointment['appid']; ?></p>
            <p>Doctor: <?php echo $appointment['dname']; ?></p>
            <p>Specialization: <?php echo $appointment['sname']; ?></p>
            <p>Date: <?php echo $appointment['appdate']; ?></p>
            <p>Time: <?php echo $appointment['apptime']; ?></p>
        <?php else : ?>
            <p>Appointment details not available.</p>
        <?php endif; ?>
    
</body>
</html>