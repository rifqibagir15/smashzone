<?php

session_name('ADMIN_SESSION');
session_start();
require '../../../config/database.php';

if(
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
)
{
    header("Location: ../index.php");
    exit;
}
$id = $_GET['id'];

mysqli_query($conn, "
    UPDATE bookings
    SET status='confirmed'
    WHERE id='$id'
");

header("Location: ../dashboard.php");
exit;