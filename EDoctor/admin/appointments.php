<?php
session_start();
include '../connection.php'; // Include the connection file to establish a database connection

// Check if the user is authenticated and is an admin
if (!isset($_SESSION['authenticated']) || $_SESSION['usertype'] !== 'a') {
    header('Location: ../login.php');
    exit();
}

// Process checkout if appointment ID is provided in URL
if (isset($_GET['checkout_id'])) {
    $checkout_id = $_GET['checkout_id'];
    
    // Get patient ID and doctor ID for notification
    $select_query = "SELECT pid, did FROM appointment WHERE appid = ?";
    $stmt = $database->prepare($select_query);
    $stmt->bind_param("i", $checkout_id);
    $stmt->execute();
    $stmt->bind_result($pid, $did);
    $stmt->fetch();
    $stmt->close();

    // Update the checked_out column to mark the appointment as checked out
    $update_query = "UPDATE appointment SET checked_out = 1 WHERE appid = ?";
    $stmt = $database->prepare($update_query);
    $stmt->bind_param("i", $checkout_id);
    $stmt->execute();
    $stmt->close();

    // Insert notifications for patient and doctor about the checkout
    $message = "Your appointment with ID $checkout_id has been checked out.";
    $notify_query = "INSERT INTO notifications (pid, message) VALUES (?, ?)";

    $stmt = $database->prepare($notify_query);
    $stmt->bind_param("is", $pid, $message);
    $stmt->execute();

    // Notify the doctor
    $stmt->bind_param("is", $did, $message);
    $stmt->execute();
    $stmt->close();

    // Redirect back to the same page after checkout
    header('Location: appointments.php');
    exit();
}

// Process removal if appointment ID is provided in URL
if (isset($_GET['id'])) {
    $appointment_id = $_GET['id'];

    // Get patient ID and doctor ID for notification
    $select_query = "SELECT pid, did FROM appointment WHERE appid = ?";
    $stmt = $database->prepare($select_query);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $stmt->bind_result($pid, $did);
    $stmt->fetch();
    $stmt->close();

    // Delete the appointment
    $delete_query = "DELETE FROM appointment WHERE appid = ?";
    $stmt = $database->prepare($delete_query);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $stmt->close();

    // Insert notifications for patient and doctor about the removal
    $message = "Your appointment with ID $appointment_id has been removed.";
    $notify_query = "INSERT INTO notifications (pid, message) VALUES (?, ?)";

    $stmt = $database->prepare($notify_query);
    $stmt->bind_param("is", $pid, $message);
    $stmt->execute();

    // Notify the doctor
    $stmt->bind_param("is", $did, $message);
    $stmt->execute();
    $stmt->close();

    // Redirect back to the same page after removal
    header('Location: appointments.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments | Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="date">
                <?php echo date("F j, Y"); ?>
            </div>
            <div class="profile-container">
                <img src="../images/user.png" alt="User" class="user-icon">
                <p class="profile-email">admin@cuea.edu</p>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
            <nav>
                <ul>
                    <li><a href="admin.php">Home</a></li>
                    <li><a href="add_doctor.php">Doctors</a></li>
                    <li><a href="appointments.php">Appointments</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h2>Appointments</h2>
        <!-- Display appointments -->
        <?php
        // Fetch all appointments from the database
        $query = "SELECT * FROM appointment";
        $result = $database->query($query);

        // Display appointments in a table
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Date</th><th>Time</th><th>Patient ID</th><th>Doctor ID</th><th>Action</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['appdate'] . "</td>";
                echo "<td>" . $row['apptime'] . "</td>";
                echo "<td>" . $row['appid'] . "</td>";
                echo "<td>" . $row['pid'] . "</td>";
                echo "<td>" . $row['did'] . "</td>";
                if ($row['checked_out'] == 0) {
                    // Show checkout link if the appointment is not checked out
                    echo "<td><a href='appointments.php?checkout_id=" . $row['appid'] . "'>Checkout</a> | <a href='appointments.php?id=" . $row['appid'] . "'>Remove</a></td>";
                } else {
                    // Show checked out status if the appointment is checked out
                    echo "<td>Checked Out</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No appointments found.";
        }
        ?>
    </div>

    <div class="container">
        <h2>Checked Out Patients</h2>
        <!-- Display checked out patients -->
        <?php
        // Fetch checked out patients from the database
        $checked_out_query = "SELECT * FROM appointment WHERE checked_out = 1";
        $checked_out_result = $database->query($checked_out_query);

        // Display checked out patients in a table
        if ($checked_out_result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Date</th><th>Time</th><th>Patient ID</th><th>Doctor ID</th></tr>";
            while ($row = $checked_out_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['appdate'] . "</td>";
                echo "<td>" . $row['apptime'] . "</td>";
                echo "<td>" . $row['appid'] . "</td>";
                echo "<td>" . $row['pid'] . "</td>";
                echo "<td>" . $row['did'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No checked out patients.";
        }
        ?>
    </div>
</body>
</html>
