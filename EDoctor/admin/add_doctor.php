<?php
session_start();
include '../connection.php'; // Include the connection file to establish a database connection

// Check if the user is authenticated and is an admin
if (!isset($_SESSION['authenticated']) || $_SESSION['usertype'] !== 'a') {
    header('Location: ../login.php');
    exit();
}

// Process the form submission to add a doctor
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $specialization = $_POST['specialization'];
    $tel = $_POST['tel'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security

    $query = "INSERT INTO doctor (dname, demail, specialization, dtel, dpassword) VALUES (?, ?, ?, ?, ?)";
    $stmt = $database->prepare($query);
    $stmt->bind_param("sssss", $name, $email, $specialization, $tel, $password);
    if ($stmt->execute()) {
        // Doctor added successfully
        header('Location: add_doctor.php'); // Redirect to refresh the page and show the new list of doctors
        exit();
    } else {
        echo "Error adding doctor";
    }
}

// Process removal of doctor if ID is provided in URL
if (isset($_GET['id'])) {
    $doctor_id = $_GET['id'];
    $query = "DELETE FROM doctor WHERE did = ?";
    $stmt = $database->prepare($query);
    $stmt->bind_param("i", $doctor_id);
    if ($stmt->execute()) {
        // Doctor removed successfully
        header('Location: add_doctor.php'); // Redirect back to the same page after removal
        exit();
    } else {
        echo "Error removing doctor";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Doctor | Admin Dashboard</title>
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
        <h2>Add Doctor</h2>
        <!-- Form to add a new doctor -->
        <form action="add_doctor.php" method="POST">
            <!-- Doctor details input fields -->
            <label for="id">ID:</label>
            <input type="text" name="id" id="id" required><br>

            <label for="name">Name:</label>
            <input type="text" name="name" id="name" required><br>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required><br>

            <label for="specialization">Specialization:</label>
            <input type="text" name="specialization" id="specialization" required><br>

            <label for="tel">Telephone:</label>
            <input type="text" name="tel" id="tel" required><br>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required><br>

            <!-- Submit button -->
            <button type="submit">Add Doctor</button>
        </form>
    </div>

    <div class="container">
        <h2>Available Doctors</h2>
        <?php
        // Fetch all doctors from the database
        $query = "SELECT * FROM doctor";
        $result = $database->query($query);

        // Display doctors in a table
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Specialization</th><th>Telephone</th><th>Action</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['did']) . "</td>";
                echo "<td>" . htmlspecialchars($row['dname']) . "</td>";
                echo "<td>" . htmlspecialchars($row['demail']) . "</td>";
                echo "<td>" . htmlspecialchars($row['specialization']) . "</td>";
                echo "<td>" . htmlspecialchars($row['dtel']) . "</td>";
                echo "<td><a href='add_doctor.php?id=" . $row['did'] . "'>Remove</a></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No doctors found.";
        }
        ?>
</body>
</html>
