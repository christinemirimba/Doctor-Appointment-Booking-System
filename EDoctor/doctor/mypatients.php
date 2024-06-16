<?php
session_start();
include '../connection.php';

// Check if the user is authenticated and is a doctor
if (!isset($_SESSION['authenticated']) || $_SESSION['usertype'] !== 'd') {
    header('Location: ../login.php');
    exit();
}

// Fetch the doctor's ID from the session
$doctor_email = $_SESSION['email'];
$doctor_query = "SELECT did FROM doctor WHERE demail = ?";
$stmt = $database->prepare($doctor_query);
$stmt->bind_param("s", $doctor_email);
$stmt->execute();
$stmt->bind_result($doctor_id);
$stmt->fetch();
$stmt->close();

// Fetch patients who have appointments with the doctor
$query = "
    SELECT p.first_name, p.last_name, p.dob, p.email, p.mobile_number, p.admission_number
    FROM appointment a
    JOIN patients p ON a.pid = p.id
    WHERE a.did = ?
    GROUP BY p.id
";
$stmt = $database->prepare($query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

$patients = [];
while ($row = $result->fetch_assoc()) {
    $patients[] = $row;
}
$stmt->close();
$database->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Patients</title>
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
                <p class="profile-email"><?php echo $_SESSION['email']; ?></p>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
            <nav>
                <ul>
                    <li><a href="doctor.php">Home</a></li>
                    <li><a href="appointment.php">Appointment</a></li>
                    <li><a href="my_patients.php">My Patients</a></li>
                    <li><a href="settings.php" class="settings-link">Settings</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h2>My Patients</h2>
        <?php if (count($patients) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Date of Birth</th>
                        <th>Email</th>
                        <th>Mobile Number</th>
                        <th>Admission Number</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patients as $patient): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($patient['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($patient['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($patient['dob']); ?></td>
                            <td><?php echo htmlspecialchars($patient['email']); ?></td>
                            <td><?php echo htmlspecialchars($patient['mobile_number']); ?></td>
                            <td><?php echo htmlspecialchars($patient['admission_number']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No patients found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
