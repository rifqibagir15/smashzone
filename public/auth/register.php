<?php

session_start();
require '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

$username = trim($_POST['username']);
$email    = trim($_POST['email']);
$phone    = trim($_POST['phone']);
$password = trim($_POST['password']);


// VALIDASI
if (
    empty($username) ||
    empty($email) ||
    empty($phone) ||
    empty($password)
) {
    $_SESSION['error'] = "Semua field wajib diisi!";
    header("Location: ../index.php");
    exit;
}


// CHECK EMAIL
$checkEmail = mysqli_query($conn, "
    SELECT id FROM users
    WHERE email = '$email'
");

if (mysqli_num_rows($checkEmail) > 0) {

    $_SESSION['error'] = "Email sudah digunakan!";
    header("Location: ../index.php");
    exit;
}


// HASH PASSWORD
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);


// INSERT USER
$insert = mysqli_query($conn, "
    INSERT INTO users (
        username,
        email,
        password,
        phone,
        role
    ) VALUES (
        '$username',
        '$email',
        '$hashedPassword',
        '$phone',
        'user'
    )
");


if ($insert) {

    $userId = mysqli_insert_id($conn);

    $_SESSION['user'] = [
        'id' => $userId,
        'username' => $username,
        'email' => $email,
        'phone' => $phone,
        'role' => 'user'
    ];

    $_SESSION['success'] = "Register berhasil!";

    header("Location: ../index.php");
    exit;

} else {

    $_SESSION['error'] = "Terjadi kesalahan!";

    header("Location: ../index.php");
    exit;
}