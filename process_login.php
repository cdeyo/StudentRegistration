<?php
session_start();
include 'databasehandler.php';

$dbHandler = new DatabaseHandler();

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM students WHERE email = :email";
$params = [':email' => $email];
$result = $dbHandler->executeSelectQuery($sql, $params);

if ($result && count($result) === 1) {
    $user = $result[0];

    if (password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'student_id' => $user['student_id'],
            'email' => $user['email'],
            'name' => $user['name']
        ];
        header("Location: home.php");
        exit();
    } else {
        echo "Invalid password.";
    }
} else {
    echo "No user found with that email.";
}
?>
