<?php

session_start();
require '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

$email    = trim($_POST['email']);
$password = trim($_POST['password']);


// VALIDASI
if (
    empty($email) ||
    empty($password)
) {

    $_SESSION['error'] = "Email dan password wajib diisi!";

    header("Location: ../index.php");
    exit;
}


// GET USER
$query = mysqli_query($conn, "
    SELECT * FROM users
    WHERE email = '$email'
    LIMIT 1
");

$user = mysqli_fetch_assoc($query);


// CHECK USER
if (!$user) {

    $_SESSION['error'] = "Email tidak ditemukan!";

    header("Location: ../index.php");
    exit;
}


// VERIFY PASSWORD
if (!password_verify($password, $user['password'])) {

    $_SESSION['error'] = "Password salah!";

    header("Location: ../index.php");
    exit;
}


// SESSION LOGIN
$_SESSION['user'] = [
    'id' => $user['id'],
    'username' => $user['username'],
    'email' => $user['email'],
    'phone' => $user['phone'],
    'role' => $user['role']
];


// REDIRECT
if ($user['role'] == 'admin') {

    header("Location: ../admin/dashboard.php");
    exit;

} else {

    header("Location: ../index.php");
    exit;
}