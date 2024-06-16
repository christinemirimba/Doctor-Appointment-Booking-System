<?php
session_start();
include '../connection.php';

// Check if the user is authenticated and is a doctor
if (!isset($_SESSION['authenticated']) || $_SESSION['usertype'] !== 'd') {
    header('Location: ../login.php');
    exit();
}

// Get the logged-in doctor's email
$doctor_email = $_SESSION['email'];

// Fetch the doctor's current details from the database
$query = "SELECT * FROM doctor WHERE demail = ?";
$stmt = $database->prepare($query);
$stmt->bind_param("s", $doctor_email);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

// Handle form submission to update doctor details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dname = $_POST['dname'];
    $specialization = $_POST['specialization'];
    $dtel = $_POST['dtel'];
    $dpassword = $_POST['dpassword'];

    // Update doctor details in the database
    $update_query = "UPDATE doctor SET dname = ?, specialization = ?, dtel = ?, dpassword = ? WHERE demail = ?";
    $stmt = $database->prepare($update_query);
    $stmt->bind_param("sssss", $dname, $specialization, $dtel, $dpassword, $doctor_email);

    if ($stmt->execute()) {
        echo "<p>Details updated successfully.</p>";
        // Refresh the page to display updated details
        header('Refresh: 2; URL=settings.php');
    } else {
        echo "<p>Error updating details: " . $database->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Settings</title>
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
                <p class="profile-email"><?php echo $doctor_email; ?></p>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
            <nav>
                <ul>
                    <li><a href="doctor.php">Home</a></li>
                    <li><a href="appointment.php">Appointments</a></li>
                    <li><a href="mypatients.php">My patients</a></li>
                    <li><a href="settings.php">Settings</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h2>Update Your Details</h2>
        <form action="settings.php" method="POST">
            <label for="dname">Name:</label>
            <input type="text" id="dname" name="dname" value="<?php echo htmlspecialchars($doctor['dname']); ?>" required>

            <label for="specialization">Specialization:</label>
            <input type="text" id="specialization" name="specialization" value="<?php echo htmlspecialchars($doctor['specialization']); ?>" required>

            <label for="dtel">Telephone:</label>
            <input type="text" id="dtel" name="dtel" value="<?php echo htmlspecialchars($doctor['dtel']); ?>" required>

            <label for="dpassword">Password:</label>
            <input type="password" id="dpassword" name="dpassword" value="<?php echo htmlspecialchars($doctor['dpassword']); ?>" required>

            <button type="submit" class="btn">Update</button>
        </form>
    </div>
</body>
</html>
