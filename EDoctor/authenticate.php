<?php
session_start();
include 'connection.php';

// Fetching data from POST
$email = $_POST['email'];
$password = $_POST['password'];

// Prepare and bind for patient table
$stmt = $database->prepare("SELECT password, 'p' as usertype, id as patient_id FROM patients WHERE email = ?
                           UNION
                           SELECT dpassword as password, 'd' as usertype, did as patient_id FROM doctor WHERE demail = ?
                           UNION
                           SELECT password, 'a' as usertype, NULL as patient_id FROM admin WHERE email = ?");
$stmt->bind_param("sss", $email, $email, $email);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($db_password, $usertype, $patient_id);
$stmt->fetch();


if ($stmt->num_rows > 0) {
    if ($password === $db_password) { // Compare passwords directly
        $_SESSION['authenticated'] = true;
        $_SESSION['usertype'] = $usertype;
        $_SESSION['email'] = $email;
        if ($usertype === 'p') {
            $_SESSION['patient_id'] = $patient_id;
            header('Location: patient/patient.php');
        } else if ($usertype === 'd') {
            header('Location: doctor/doctor.php');
        } else if ($usertype === 'a') {
            header('Location: admin/admin.php');
        }
    } else {
        $_SESSION['authenticated'] = false;
        header('Location: login.php');
    }
} else {
    $_SESSION['authenticated'] = false;
    header('Location: login.php');
}

$stmt->close();
$database->close();
?>