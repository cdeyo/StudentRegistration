<?php
include 'databasehandler.php';

$dbHandler = new DatabaseHandler();

$email = $_POST['email'];
$password = $_POST['password'];
$name = $_POST['name'];
$phone = $_POST['phone'];

$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

$sql = "INSERT INTO students (email, password, name, phone) 
        VALUES (:email, :password, :name, :phone)";

$params = [
    ':email' => $email,
    ':password' => $hashedPassword,
    ':name' => $name,
    ':phone' => $phone,
];

$result = $dbHandler->executeQuery($sql, $params);

if ($result === true) {
    echo "Registration successful";
} else {
    echo "Error: " . $result;
}
?>
