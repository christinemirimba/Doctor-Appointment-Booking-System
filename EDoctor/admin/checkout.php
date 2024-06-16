<?php
// Include connection to database
include '../connection.php';

// Check if patient ID is provided in URL
if(isset($_GET['id'])) {
    // Sanitize the input to prevent SQL injection
    $patient_id = mysqli_real_escape_string($database, $_GET['id']);
    
    // Retrieve patient details from the database
    $query = "SELECT * FROM patients WHERE id = '$patient_id'";
    $result = mysqli_query($database, $query);

    if($result) {
        if(mysqli_num_rows($result) > 0) {
            // Display patient details
            echo "<h2>Checked-out Patient Details</h2>";
            echo "<table>";
            echo "<tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Date of Birth</th><th>Email</th><th>Mobile Number</th><th>Admission Number</th></tr>";
            while($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>".$row['id']."</td>";
                echo "<td>".$row['first_name']."</td>";
                echo "<td>".$row['last_name']."</td>";
                echo "<td>".$row['dob']."</td>";
                echo "<td>".$row['email']."</td>";
                echo "<td>".$row['mobile_number']."</td>";
                echo "<td>".$row['admission_number']."</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No patient found.";
        }
    } else {
        echo "Error: " . mysqli_error($database);
    }
} else {
    echo "Patient ID not provided.";
}
?>
