<?php

session_start();
require '../../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

$userId         = $_SESSION['user']['id'];
$courtId        = mysqli_real_escape_string($conn, $_POST['court_id']);
$bookingDate    = mysqli_real_escape_string($conn, $_POST['booking_date']);
$startTime      = mysqli_real_escape_string($conn, $_POST['start_time']);
$duration       = (int) $_POST['duration'];
$bookingName    = mysqli_real_escape_string($conn, $_POST['booking_name']);
$whatsappNumber = mysqli_real_escape_string($conn, $_POST['whatsapp_number']);


// ========================
// VALIDASI
// ========================
if (
    empty($courtId) ||
    empty($bookingDate) ||
    empty($startTime) ||
    empty($duration) ||
    empty($bookingName) ||
    empty($whatsappNumber)
) {
    $_SESSION['error'] = "Semua field wajib diisi!";
    header("Location: ../index.php");
    exit;
}

if ($duration < 1 || $duration > 3) {
    $_SESSION['error'] = "Durasi tidak valid!";
    header("Location: ../index.php");
    exit;
}


// ========================
// HITUNG JAM
// ========================
$startTimestamp = strtotime($startTime);
$endTimestamp   = strtotime("+{$duration} hour", $startTimestamp);
$endTime        = date('H:i:s', $endTimestamp);


// ========================
// HARGA
// ========================
$pricePerHour = 50000;
$totalPrice   = $pricePerHour * $duration;


// ========================
// PAYMENT DEADLINE (3 JAM)
// ========================
$paymentDeadline = date('Y-m-d H:i:s', strtotime('+3 hours'));


// ========================
// CEK BENTROK SLOT (CORE LOGIC)
// ========================
$checkBooking = mysqli_query($conn, "

    SELECT id 
    FROM bookings
    WHERE court_id = '$courtId'
    AND booking_date = '$bookingDate'
    AND status IN ('pending','waiting_verification','confirmed')
    AND (
        payment_deadline IS NULL
        OR payment_deadline > NOW()
    )
    AND (
        start_time < '$endTime'
        AND end_time > '$startTime'
    )

");

if (mysqli_num_rows($checkBooking) > 0) {
    $_SESSION['error'] = "Slot sudah dibooking user lain!";
    header("Location: ../index.php?date=$bookingDate");
    exit;
}


// ========================
// INSERT BOOKING
// ========================
$insert = mysqli_query($conn, "

    INSERT INTO bookings (
        user_id,
        court_id,
        booking_name,
        whatsapp_number,
        booking_date,
        start_time,
        end_time,
        duration,
        total_price,
        status,
        payment_deadline
    )
    VALUES (
        '$userId',
        '$courtId',
        '$bookingName',
        '$whatsappNumber',
        '$bookingDate',
        '$startTime',
        '$endTime',
        '$duration',
        '$totalPrice',
        'pending',
        '$paymentDeadline'
    )

");

if (!$insert) {
    $_SESSION['error'] = "Gagal membuat booking!";
    header("Location: ../index.php");
    exit;
}

$bookingId = mysqli_insert_id($conn);


// ========================
// INSERT PAYMENT
// ========================
mysqli_query($conn, "

    INSERT INTO payments (
        booking_id,
        payment_status,
        created_at
    )
    VALUES (
        '$bookingId',
        'unpaid',
        NOW()
    )

");


// ========================
// SUCCESS
// ========================
$_SESSION['success'] = "Booking berhasil dibuat! Segera lakukan pembayaran dalam 3 jam.";
header("Location: ../user/profile.php");
exit;