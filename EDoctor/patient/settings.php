<?php
session_start();
include '../connection.php';

// Check if the user is authenticated and is a patient
if (!isset($_SESSION['authenticated']) || $_SESSION['usertype'] !== 'p') {
    header('Location: ../login.php');
    exit();
}

$patient_id = $_SESSION['patient_id'];
$patient_query = $database->query("SELECT * FROM patients WHERE id = $patient_id");

if ($patient_query->num_rows > 0) {
    $patient = $patient_query->fetch_assoc();
} else {
    echo "Patient not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $dob = $_POST['dob'];
    $mobile_number = $_POST['mobile_number'];
    $admission_number = $_POST['admission_number'];
    $password = $_POST['password'];

    // Update patient details
    $stmt = $database->prepare("UPDATE patients SET first_name = ?, last_name = ?, dob = ?, mobile_number = ?, admission_number = ?, password = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $first_name, $last_name, $dob, $mobile_number, $admission_number, $password, $patient_id);
    
    if ($stmt->execute()) {
        echo "Details updated successfully.";
        header('Location: settings.php');
        exit();
    } else {
        echo "Error updating details.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
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
        <h2>Settings</h2>
        <form method="POST" action="">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" value="<?= $patient['first_name'] ?>" required>

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" value="<?= $patient['last_name'] ?>" required>

            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" value="<?= $patient['dob'] ?>" required>

            <label for="mobile_number">Mobile Number:</label>
            <input type="text" id="mobile_number" name="mobile_number" value="<?= $patient['mobile_number'] ?>" required>

            <label for="admission_number">Admission Number:</label>
            <input type="text" id="admission_number" name="admission_number" value="<?= $patient['admission_number'] ?>" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" value="<?= $patient['password'] ?>" required>

            <button type="submit">Update Details</button>
        </form>
    </div>

    <footer>
        <div class="container">
            <a href="#" class="settings-link">Settings</a>
        </div>
    </footer>
</body>
</html>