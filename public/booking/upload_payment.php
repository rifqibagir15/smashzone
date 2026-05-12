<?php
session_start();

require '../../config/database.php';

/*
|--------------------------------------------------------------------------
| VALIDASI LOGIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user'])) {

    $_SESSION['error'] = 'Silakan login terlebih dahulu.';
    header('Location: ../index.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| VALIDASI METHOD
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: ../index.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| AMBIL DATA
|--------------------------------------------------------------------------
*/

$bookingId = $_POST['booking_id'] ?? null;

if (!$bookingId) {

    $_SESSION['error'] = 'Booking tidak ditemukan.';
    header('Location: ../index.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| CEK BOOKING
|--------------------------------------------------------------------------
*/

$query = mysqli_query($conn, "
    SELECT *
    FROM bookings
    WHERE id = '$bookingId'
    LIMIT 1
");

if (mysqli_num_rows($query) == 0) {

    $_SESSION['error'] = 'Booking tidak ditemukan.';
    header('Location: ../index.php');
    exit;
}

$booking = mysqli_fetch_assoc($query);


/*
|--------------------------------------------------------------------------
| VALIDASI FILE
|--------------------------------------------------------------------------
*/

if (!isset($_FILES['payment_proof'])) {

    $_SESSION['error'] = 'Bukti pembayaran wajib diupload.';
    header('Location: ../user/profile.php');
    exit;
}

$file = $_FILES['payment_proof'];

if ($file['error'] !== 0) {

    $_SESSION['error'] = 'Gagal upload file.';
    header('Location: ../user/profile.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| VALIDASI EXTENSION
|--------------------------------------------------------------------------
*/

$allowed = ['jpg', 'jpeg', 'png', 'webp'];

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {

    $_SESSION['error'] = 'Format file harus JPG, PNG, atau WEBP.';
    header('Location: ../user/profile.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| VALIDASI SIZE
|--------------------------------------------------------------------------
*/

$maxSize = 5 * 1024 * 1024; // 5MB

if ($file['size'] > $maxSize) {

    $_SESSION['error'] = 'Ukuran file maksimal 5MB.';
    header('Location: ../user/profile.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| BUAT FOLDER
|--------------------------------------------------------------------------
*/

$uploadDir = '../uploads/payments/';

if (!file_exists($uploadDir)) {

    mkdir($uploadDir, 0777, true);
}


/*
|--------------------------------------------------------------------------
| GENERATE NAMA FILE
|--------------------------------------------------------------------------
*/

$fileName =
    'PAYMENT_' .
    time() .
    '_' .
    rand(1000, 9999) .
    '.' .
    $ext;

$target = $uploadDir . $fileName;


/*
|--------------------------------------------------------------------------
| UPLOAD FILE
|--------------------------------------------------------------------------
*/

if (!move_uploaded_file($file['tmp_name'], $target)) {

    $_SESSION['error'] = 'Gagal menyimpan file.';
    header('Location: ../user/profile.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| UPDATE BOOKING
|--------------------------------------------------------------------------
*/

mysqli_query($conn, "
    UPDATE bookings
    SET
        payment_proof = '$fileName',
        status = 'waiting_verification'
    WHERE id = '$bookingId'
");


/*
|--------------------------------------------------------------------------
| SUCCESS
|--------------------------------------------------------------------------
*/

$_SESSION['success'] = 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.';

header('Location: ../user/profile.php');
exit;
?>