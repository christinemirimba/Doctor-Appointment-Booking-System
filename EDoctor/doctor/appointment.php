<?php
session_start();
include '../connection.php';

// Check if the user is authenticated and is a doctor
if (!isset($_SESSION['authenticated']) || $_SESSION['usertype'] !== 'd') {
    header('Location: ../login.php');
    exit();
}

// Debugging: Verify session values
$doctor_email = $_SESSION['email'];
error_log("Doctor Email: " . $doctor_email); // Log the doctor's email for debugging

// Get the logged-in doctor's ID from the session
$query = "SELECT did FROM doctor WHERE demail = ?";
$stmt = $database->prepare($query);
$stmt->bind_param("s", $doctor_email);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($doctor_id);
$stmt->fetch();
$stmt->close();

// Debugging: Verify the doctor ID
error_log("Doctor ID: " . $doctor_id); // Log the doctor ID for debugging

// Fetch appointments for the logged-in doctor from the database
$query = "SELECT a.appid, a.appdate, a.apptime, p.first_name, p.last_name 
          FROM appointment a 
          JOIN patients p ON a.pid = p.id 
          WHERE a.did = ?";
$stmt = $database->prepare($query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}
$stmt->close();
$database->close();

// Debugging: Verify appointments
error_log("Appointments: " . print_r($appointments, true)); // Log the appointments for debugging
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments</title>
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
                <p class="profile-email"><?php echo htmlspecialchars($doctor_email); ?></p>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
            <nav>
                <ul>
                    <li><a href="doctor.php">Home</a></li>
                    <li><a href="appointment.php">Appointments</a></li>
                    <li><a href="mypatients.php">My Patients</a></li>
                    <li><a href="settings.php" class="settings-link">Settings</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h2>Appointments</h2>
        <?php if (count($appointments) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Patient</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($appointment['appid']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['appdate']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['apptime']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?></td>
                            <td>
                                <a href="reschedule.php?appid=<?php echo htmlspecialchars($appointment['appid']); ?>">Reschedule</a>
                                <a href="cancel_appointment.php?appid=<?php echo htmlspecialchars($appointment['appid']); ?>">Cancel</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No appointments found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
