<?php
session_start();
require '../config/database.php';

$date = $_GET['date'] ?? date('Y-m-d');

$courtsQuery = mysqli_query($conn, "SELECT * FROM courts ORDER BY id ASC");
$courts = [];

while ($court = mysqli_fetch_assoc($courtsQuery)) {
    $courts[] = $court;
}

$bookingsData = [];

$getAllBookings = mysqli_query($conn, "
    SELECT court_id, booking_date, start_time, end_time
    FROM bookings
    WHERE status IN ('pending','waiting_verification','confirmed')
");

while($b = mysqli_fetch_assoc($getAllBookings)) {

    $bookingsData[] = [
        'court_id' => $b['court_id'],
        'booking_date' => $b['booking_date'],
        'start_time' => substr($b['start_time'], 0, 5),
        'end_time' => substr($b['end_time'], 0, 5),
    ];
}

$hours = [];
for ($i = 8; $i <= 23; $i++) {
    $hours[] = sprintf('%02d:00:00', $i);
}

function getBookingStatus($conn, $courtId, $date, $time)
{
    $query = mysqli_query($conn, "
        SELECT * FROM bookings
        WHERE court_id = '$courtId'
        AND booking_date = '$date'
        AND start_time = '$time'
        AND status IN ('pending', 'waiting_verification', 'confirmed')
        LIMIT 1
    ");

    return mysqli_fetch_assoc($query);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SmashZone</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

<?php if(isset($_SESSION['success'])): ?>

    <div class="fixed top-24 right-5 bg-green-500 text-white px-6 py-4 rounded-xl shadow-lg z-50">
        <?= $_SESSION['success']; ?>
    </div>

    <?php unset($_SESSION['success']); ?>

<?php endif; ?>


<?php if(isset($_SESSION['error'])): ?>

    <div class="fixed top-24 right-5 bg-red-500 text-white px-6 py-4 rounded-xl shadow-lg z-50">
        <?= $_SESSION['error']; ?>
    </div>

    <?php unset($_SESSION['error']); ?>

<?php endif; ?>

<!-- NAVBAR -->
<nav class="bg-white shadow-md fixed w-full top-0 left-0 z-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">

            <div class="font-bold text-2xl text-green-600">
                SmashZone
            </div>

            <div class="hidden md:flex items-center gap-6 font-medium">
                <a href="#home" class="hover:text-green-600 transition">Home</a>
                <a href="#tentang" class="hover:text-green-600 transition">Tentang Kami</a>
                <a href="#prosedur" class="hover:text-green-600 transition">Prosedur Booking</a>

                <a href="https://wa.me/6287729359927"
                   target="_blank"
                   class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition">
                    Kontak Admin
                </a>

                <?php if(isset($_SESSION['user'])): ?>
                    <a href="user/profile.php"
                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                        Profile
                    </a>
                <?php else: ?>
                    <button onclick="openLoginModal()"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                        Login / Register
                    </button>
                <?php endif; ?>
            </div>

            <button id="menuButton" class="md:hidden text-3xl">
                ☰
            </button>
        </div>
    </div>

    <!-- MOBILE MENU -->
    <div id="mobileMenu" class="hidden md:hidden bg-white border-t">
        <div class="flex flex-col p-4 gap-4 font-medium">
            <a href="#home">Home</a>
            <a href="#tentang">Tentang Kami</a>
            <a href="#prosedur">Prosedur Booking</a>

            <a href="https://wa.me/628123456789"
               target="_blank"
               class="bg-green-500 text-center text-white py-2 rounded-lg">
                Kontak Admin
            </a>

            <?php if(isset($_SESSION['user'])): ?>
                <a href="user/profile.php"
                   class="bg-blue-500 text-center text-white py-2 rounded-lg">
                    Profile
                </a>
            <?php else: ?>
                <button onclick="openLoginModal()"
                        class="bg-blue-500 text-white py-2 rounded-lg">
                    Login / Register
                </button>
            <?php endif; ?>
        </div>
    </div>
</nav>


<!-- HERO -->
<section id="home"
         class="h-screen bg-cover bg-center flex items-center justify-center text-center px-4"
         style="background-image: url('assets/images/lapangan1.jpeg');">

    <div class="bg-black/60 p-8 rounded-2xl max-w-2xl text-white">
        <h1 class="text-4xl md:text-6xl font-bold mb-4">
            Selamat Datang
        </h1>

        <p class="text-lg md:text-xl mb-6 text-gray-200">
            Booking lapangan badminton jadi lebih cepat, simpel, dan modern.
        </p>

        <a href="#prosedur"
           class="bg-green-500 hover:bg-green-600 px-6 py-3 rounded-xl text-lg font-semibold inline-block transition">
            Baca Prosedur Booking
        </a>
    </div>
</section>


<!-- KEUNGGULAN -->
<section id="tentang" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">

        <div class="text-center mb-14">
            <h2 class="text-4xl font-bold mb-4">
                Keunggulan Kami
            </h2>

            <p class="text-gray-500 max-w-2xl mx-auto">
                Sistem booking modern yang memudahkan pemain badminton untuk booking lapangan kapan saja.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">

            <div class="bg-gray-100 p-8 rounded-2xl shadow hover:-translate-y-2 transition">
                <div class="text-5xl mb-4">⚡</div>
                <h3 class="text-2xl font-bold mb-3">Booking Cepat</h3>
                <p class="text-gray-600">
                    Cuma beberapa klik dan lapangan langsung terbooking.
                </p>
            </div>

            <div class="bg-gray-100 p-8 rounded-2xl shadow hover:-translate-y-2 transition">
                <div class="text-5xl mb-4">📱</div>
                <h3 class="text-2xl font-bold mb-3">Responsive</h3>
                <p class="text-gray-600">
                    Bisa digunakan dengan nyaman di HP maupun laptop.
                </p>
            </div>

            <div class="bg-gray-100 p-8 rounded-2xl shadow hover:-translate-y-2 transition">
                <div class="text-5xl mb-4">🔒</div>
                <h3 class="text-2xl font-bold mb-3">Anti Double Booking</h3>
                <p class="text-gray-600">
                    Slot otomatis terkunci saat user melakukan pembayaran.
                </p>
            </div>

        </div>
    </div>
</section>


<!-- PROSEDUR -->
<section id="prosedur" class="py-20 bg-gray-100">
    <div class="max-w-5xl mx-auto px-4">

        <div class="text-center mb-14">
            <h2 class="text-4xl font-bold mb-4">
                Prosedur Booking
            </h2>
        </div>

        <div class="space-y-6">

            <div class="bg-white p-6 rounded-2xl shadow">
                <h3 class="font-bold text-xl mb-2">1. Pilih Slot Lapangan</h3>
                <p class="text-gray-600">
                    Klik jam dan lapangan yang masih kosong.
                </p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow">
                <h3 class="font-bold text-xl mb-2">2. Isi Form Booking</h3>
                <p class="text-gray-600">
                    Isi data booking seperti nama dan nomor WhatsApp.
                </p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow">
                <h3 class="font-bold text-xl mb-2">3. Upload Bukti Pembayaran</h3>
                <p class="text-gray-600">
                    Upload bukti pembayaran QRIS agar booking bisa diverifikasi admin.
                </p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow">
                <h3 class="font-bold text-xl mb-2">4. Booking Dikonfirmasi</h3>
                <p class="text-gray-600">
                    Setelah diverifikasi admin, slot booking akan aktif.
                </p>
            </div>

        </div>
    </div>
</section>

<!-- BOOKING SCHEDULE -->
<section class="py-20 bg-[#f5f7fb]">

    <div class="max-w-[1400px] mx-auto px-4">

        <!-- TOP BAR -->
        <div class="bg-white border rounded-2xl p-5 mb-6 shadow-sm">

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">

                <!-- LEFT -->
                <div class="flex items-center gap-3">

                    <?php
                    $prevDate = date('Y-m-d', strtotime($date . ' -1 day'));
                    $nextDate = date('Y-m-d', strtotime($date . ' +1 day'));
                    ?>

                    <a href="?date=<?= date('Y-m-d') ?>"
                       class="bg-green-600 hover:bg-green-700 text-white px-5 py-3 rounded-xl font-medium">
                        Hari Ini
                    </a>

                    <a href="?date=<?= $prevDate ?>"
                       class="bg-green-600 hover:bg-green-700 text-white w-12 h-12 rounded-xl flex items-center justify-center text-2xl">
                        ‹
                    </a>

                    <a href="?date=<?= $nextDate ?>"
                       class="bg-green-600 hover:bg-green-700 text-white w-12 h-12 rounded-xl flex items-center justify-center text-2xl">
                        ›
                    </a>
                </div>

                <!-- CENTER -->
                <div class="text-center">
                    <h2 class="text-4xl font-bold text-green-800">
                        <?= date('d F Y', strtotime($date)) ?>
                    </h2>
                </div>

                <!-- RIGHT -->
                <div class="flex gap-3">

                    <div class="bg-green-700 text-white px-5 py-3 rounded-xl font-medium">
                        Booking
                    </div>

                </div>

            </div>

        </div>


        <!-- TABLE -->
        <div class="overflow-x-auto">

            <div class="min-w-[1200px]">

                <!-- HEADER -->
                <div class="grid"
                     style="grid-template-columns: 80px repeat(<?= count($courts) ?>, 1fr);">

                    <div class="bg-white border h-14"></div>

                    <?php foreach($courts as $court): ?>

                        <div class="bg-green-900 text-white font-bold text-center flex items-center justify-center border h-14 text-xl">
                            <?= strtoupper($court['court_name']) ?>
                        </div>

                    <?php endforeach; ?>

                </div>


                <!-- BODY -->
                <div class="relative">

                    <!-- JAM -->
                    <div class="absolute left-0 top-0 w-[80px] z-10"> 
                        <?php
                        for($i = 8; $i <= 22; $i++):
                        ?>
                            <div class="h-[80px] border bg-white flex items-start justify-center pt-2 font-bold">
                                <?= sprintf('%02d.00', $i) ?>
                            </div>
                        <?php endfor; ?>
                    </div>

                    <!-- GRID -->
                    <div class="ml-[80px] grid relative"
                         style="grid-template-columns: repeat(<?= count($courts) ?>, 1fr);">
                        <?php foreach($courts as $court): ?>
                            <div class="relative border-r bg-white">
                                <!-- ROWS -->
                                <?php for($i = 8; $i <= 22; $i++): ?>
                                    <div class="h-[80px] border-b"></div>
                                <?php endfor; ?>

                                <!-- BOOKINGS -->
                                <?php
                                $courtId = $court['id'];
                                $bookingQuery = mysqli_query($conn, "
                                    SELECT *
                                    FROM bookings
                                    WHERE court_id = '$courtId'
                                    AND booking_date = '$date'
                                    AND status IN ('pending', 'waiting_verification', 'confirmed')
                                    ORDER BY start_time ASC
                                ");

                                while($booking = mysqli_fetch_assoc($bookingQuery)):
                                
                                    $startHour = (int) date('H', strtotime($booking['start_time']));
                                    $duration  = (int) $booking['duration'];
                                
                                    $top    = ($startHour - 8) * 80 + 4;
                                    $height = ($duration * 80) - 8;
                                
                                    // WARNA STATUS
                                    $bg = 'bg-gray-700';
                                
                                    if($booking['status'] == 'pending') {
                                        $bg = 'bg-yellow-500';
                                    }
                                
                                    if($booking['status'] == 'waiting_verification') {
                                        $bg = 'bg-red-500';
                                    }
                                
                                    if($booking['status'] == 'confirmed') {
                                        $bg = 'bg-green-600';
                                    }
                                
                                ?>

                                    <div class="<?= $bg ?> absolute left-1 right-1 rounded-2xl shadow-xl text-white p-4 overflow-hidden z-20"
                                         style="
                                            top: <?= $top ?>px;
                                            height: <?= $height ?>px;
                                         ">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="font-bold text-lg">
                                                <?= substr($booking['start_time'],0,5) ?>
                                                -
                                                <?= substr($booking['end_time'],0,5) ?>
                                            </div>
                                            <div class="text-xs bg-white/20 px-3 py-1 rounded-full">
                                                <?= strtoupper($booking['status']) ?>
                                            </div>
                                        </div>
                                        <div class="text-2xl font-bold leading-tight">
                                            <?= htmlspecialchars($booking['booking_name']) ?>
                                        </div>
                                        <div class="mt-3 text-sm text-white/90">
                                            Durasi <?= $booking['duration'] ?> jam
                                        </div>
                                    </div>
                                <?php endwhile; ?>

                                <!-- SLOT KOSONG -->
                                <?php for($i = 8; $i <= 22; $i++): ?>
                                    <?php
                                    $time = sprintf('%02d:00:00', $i);
                                    // CEK APAKAH JAM INI MASUK KE RENTANG BOOKING
                                    $checkBooking = mysqli_query($conn, "
                                        SELECT *
                                        FROM bookings
                                        WHERE court_id = '$courtId'
                                        AND booking_date = '$date'
                                        AND status IN ('pending','waiting_verification','confirmed')
                                        AND (
                                            '$time' >= start_time
                                            AND '$time' < end_time
                                        )
                                        LIMIT 1
                                    ");
                                    $isBooked = mysqli_num_rows($checkBooking) > 0;
                                    ?>

                                    <?php if(!$isBooked): ?>
                                    
                                        <button
                                            onclick="openBookingModal(
                                                '<?= $court['id'] ?>',
                                                '<?= $court['court_name'] ?>',
                                                '<?= $date ?>',
                                                '<?= substr($time,0,5) ?>'
                                            )"
                                            class="absolute left-0 right-0 h-[80px] hover:bg-green-100/40 transition z-10"
                                            style="top: <?= ($i - 8) * 80 ?>px;">
                                        </button>
                                    
                                    <?php endif; ?>
                                    
                                <?php endfor; ?>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>

            </div>

        </div>

    </div>
</section>

<!-- GALERI -->
<section class="py-24 relative overflow-hidden">

    <!-- BACKGROUND -->
    <div class="absolute inset-0">
        <img src="assets/images/lapangan1.jpg"
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/75 backdrop-blur-sm"></div>
    </div>

    <div class="relative z-10 max-w-6xl mx-auto px-4">

        <!-- TITLE -->
        <div class="text-center mb-14">
            <h2 class="text-5xl font-bold text-white mb-4">
                GALERI
            </h2>

            <div class="flex justify-center items-center gap-3">
                <div class="w-16 h-[2px] bg-white/50"></div>
                <div class="w-4 h-[2px] bg-green-500"></div>
                <div class="w-16 h-[2px] bg-white/50"></div>
            </div>
        </div>

        <!-- GRID -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            <?php
            $galleryImages = [
                'assets/images/lapangan1.jpeg',
                'assets/images/lapangan2.jpeg',
                'assets/images/lapangan3.jpeg',
                'assets/images/lapangan4.jpeg',
                'assets/images/lapangan5.jpeg',
                'assets/images/kantin.jpeg',
            ];
            ?>

                        <?php foreach($galleryImages as $index => $image): ?>
                <div class="group relative overflow-hidden rounded-3xl cursor-pointer"
                     onclick="openGallery(<?= $index ?>)">
                    <img src="<?= $image ?>"
                         class="w-full h-[260px] object-cover group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition"></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="bg-black text-white pt-16 pb-8">

    <div class="max-w-7xl mx-auto px-4">

        <div class="grid md:grid-cols-3 gap-10">

            <!-- BRAND -->
            <div>
                <h2 class="text-3xl font-bold mb-4 text-green-500">
                    Badminton Booking
                </h2>

                <p class="text-gray-400 leading-relaxed">
                    Tempat booking lapangan badminton modern dengan fasilitas nyaman,
                    harga terjangkau, dan sistem booking online yang cepat.
                </p>
            </div>

            <!-- ALAMAT -->
            <div>
                <h3 class="text-2xl font-bold mb-5">
                    📍 Lokasi Kami
                </h3>

                <p class="text-gray-300 leading-relaxed mb-5">
                    SMKN 8 Semarang<br>
                    Jl. Pandanaran II No.12<br>
                    Mugassari, Kec. Semarang Sel., Kota Semarang
                </p>

                <!-- BUTTON MAP -->
                <a href="https://maps.google.com/?q=SMKN+8+Semarang"
                   target="_blank"
                   class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 px-5 py-3 rounded-xl font-semibold transition">

                    <span>📌</span>
                    <span>Buka Google Maps</span>

                </a>
            </div>

            <!-- KONTAK -->
            <div>
                <h3 class="text-2xl font-bold mb-5">
                    📞 Kontak
                </h3>

                <div class="space-y-3 text-gray-300">

                    <p>
                        WhatsApp: 0877-2935-9927
                    </p>

                    <p>
                        Email: rifqibagir15@email.com
                    </p>

                    <p>
                        Jam Operasional:
                        <br>
                        08.00 - 22.00
                    </p>

                </div>
            </div>

        </div>

        <!-- MAP EMBED -->
        <div class="mt-14 overflow-hidden rounded-3xl shadow-2xl border border-white/10">

            <iframe
                src="https://maps.google.com/maps?q=SMKN%208%20Semarang&t=&z=15&ie=UTF8&iwloc=&output=embed"
                width="100%"
                height="350"
                style="border:0;"
                allowfullscreen=""
                loading="lazy">
            </iframe>

        </div>

        <!-- COPYRIGHT -->
        <div class="border-t border-white/10 mt-10 pt-6 text-center text-gray-500">

            © <?= date('Y') ?> Badminton Booking.
            All Rights Reserved.

        </div>

    </div>

</footer>


<!-- LOGIN MODAL -->
<div id="loginModal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 px-4">

    <div class="bg-white w-full max-w-md rounded-2xl p-8 relative">

        <button onclick="closeLoginModal()"
                class="absolute top-4 right-4 text-2xl">
            ×
        </button>

        <h2 class="text-3xl font-bold mb-6 text-center">
            Login
        </h2>

        <form action="auth/login.php" method="POST" class="space-y-4">

            <div>
                <label>Email</label>
                <input type="email"
                       name="email"
                       required
                       class="w-full border rounded-xl px-4 py-3 mt-1">
            </div>

            <div>
                <label>Password</label>
                <input type="password"
                       name="password"
                       required
                       class="w-full border rounded-xl px-4 py-3 mt-1">
            </div>

            <button class="w-full bg-blue-500 hover:bg-blue-600 text-white py-3 rounded-xl font-semibold">
                Login
            </button>
        </form>

        <div class="text-center mt-6">
            <button onclick="showRegister()"
                    class="text-blue-500 hover:underline">
                Belum punya akun? Register
            </button>
        </div>
    </div>
</div>


<!-- REGISTER MODAL -->
<div id="registerModal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 px-4 overflow-y-auto py-10">

    <div class="bg-white w-full max-w-md rounded-2xl p-8 relative">

        <button onclick="closeRegisterModal()"
                class="absolute top-4 right-4 text-2xl">
            ×
        </button>

        <h2 class="text-3xl font-bold mb-6 text-center">
            Register
        </h2>

        <form action="auth/register.php" method="POST" class="space-y-4">

            <div>
                <label>Username</label>
                <input type="text"
                       name="username"
                       required
                       class="w-full border rounded-xl px-4 py-3 mt-1">
            </div>

            <div>
                <label>Email</label>
                <input type="email"
                       name="email"
                       required
                       class="w-full border rounded-xl px-4 py-3 mt-1">
            </div>

            <div>
                <label>No WhatsApp</label>
                <input type="text"
                       name="phone"
                       required
                       class="w-full border rounded-xl px-4 py-3 mt-1">
            </div>

            <div>
                <label>Password</label>
                <input type="password"
                       name="password"
                       required
                       class="w-full border rounded-xl px-4 py-3 mt-1">
            </div>

            <button class="w-full bg-green-500 hover:bg-green-600 text-white py-3 rounded-xl font-semibold">
                Register
            </button>
        </form>

        <div class="text-center mt-6">
            <button onclick="showLogin()"
                    class="text-blue-500 hover:underline">
                Sudah punya akun? Login
            </button>
        </div>
    </div>
</div>

<!-- BOOKING MODAL -->
<div id="bookingModal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 px-4 overflow-y-auto py-10">

    <div class="bg-white w-full max-w-lg rounded-3xl p-8 relative">

        <button onclick="closeBookingModal()"
                class="absolute top-4 right-4 text-3xl text-gray-400 hover:text-black">
            ×
        </button>
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold">
                Booking Lapangan
            </h2>
            <p class="text-gray-500 mt-2">
                Lengkapi data booking Anda
            </p>
        </div>
        <form action="booking/process_booking.php"
              method="POST"
              class="space-y-5">
            <input type="hidden"
                   name="court_id"
                   id="court_id">

            <!-- LAPANGAN -->
            <div>
                <label class="font-semibold">
                    Nomor Lapangan
                </label>
                <input type="text"
                       id="court_name"
                       readonly
                       class="w-full border rounded-2xl px-4 py-4 bg-gray-100 mt-2">
            </div>

            <!-- TANGGAL -->
            <div>
                <label class="font-semibold">
                    Tanggal Booking
                </label>
                <input type="text"
                       name="booking_date"
                       id="booking_date"
                       readonly
                       class="w-full border rounded-2xl px-4 py-4 bg-gray-100 mt-2">
            </div>

            <!-- JAM -->
            <div>
                <label class="font-semibold">
                    Jam Mulai
                </label>
                <input type="text"
                       name="start_time"
                       id="start_time"
                       readonly
                       class="w-full border rounded-2xl px-4 py-4 bg-gray-100 mt-2">
            </div>

            <!-- DURASI -->
            <div>
                <div>
        <label class="font-semibold">
            Durasi Booking
        </label>
    <div class="flex items-center justify-between border rounded-2xl px-4 py-4 mt-2">
        <!-- MINUS -->
        <button
            type="button"
            onclick="decreaseDuration()"
            class="w-14 h-14 rounded-2xl bg-red-500 hover:bg-red-600 text-white text-4xl font-bold transition flex items-center justify-center">
            −
        </button>

        <!-- TEXT -->
        <div class="text-center">
            <div id="duration_text"
                 class="text-4xl font-bold text-green-700">
                1 Jam
            </div>
            <div class="text-sm text-gray-500 mt-2">
                Tambah atau kurangi durasi
            </div>
        </div>


        <!-- PLUS -->
        <button
            type="button"
            onclick="increaseDuration()"
            class="w-14 h-14 rounded-2xl bg-green-500 hover:bg-green-600 text-white text-4xl font-bold transition flex items-center justify-center">
            +
        </button>
    </div>

    <!-- INPUT HIDDEN -->
    <input
        type="hidden"
        name="duration"
        id="duration"
        value="1">
</div>

            <!-- ATAS NAMA -->
            <div>
                <label class="font-semibold">
                    Atas Nama
                </label>
                <input type="text"
                       name="booking_name"
                       required
                       class="w-full border rounded-2xl px-4 py-4 mt-2">
            </div>

            <!-- WA -->
            <div>
                <label class="font-semibold">
                    Nomor WhatsApp
                </label>
                <input type="text"
                       name="whatsapp_number"
                       required
                       class="w-full border rounded-2xl px-4 py-4 mt-2">
            </div>

            <!-- TOTAL -->
            <div>
                <label class="font-semibold">
                    Total Biaya
                </label>
                <input type="text"
                       id="total_price"
                       readonly
                       value="Rp 50.000"
                       class="w-full border rounded-2xl px-4 py-4 bg-gray-100 mt-2 text-2xl font-bold text-green-700">
            </div>

            <!-- BUTTON -->
            <div class="flex gap-4 pt-3">
                <button type="button"
                        onclick="closeBookingModal()"
                        class="w-full bg-gray-300 hover:bg-gray-400 py-4 rounded-2xl font-bold">
                    Batal
                </button>
                <button
                    class="w-full bg-green-500 hover:bg-green-600 text-white py-4 rounded-2xl font-bold">
                    Booking Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<!-- GALLERY MODAL -->
<div id="galleryModal"
     class="fixed inset-0 bg-black/95 hidden items-center justify-center z-[9999]">

    <!-- CLOSE -->
    <button onclick="closeGallery()"
            class="absolute top-5 right-6 text-white text-5xl z-50 hover:scale-110 transition">
        ×
    </button>

    <!-- LEFT -->
    <button onclick="prevImage()"
            class="absolute left-5 text-white text-7xl z-50 hover:scale-110 transition">
        ‹
    </button>

    <!-- IMAGE -->
    <img id="galleryImage"
         src=""
         class="max-w-[90%] max-h-[85vh] rounded-xl shadow-2xl object-contain">

    <!-- RIGHT -->
    <button onclick="nextImage()"
            class="absolute right-5 text-white text-7xl z-50 hover:scale-110 transition">
        ›
    </button>

</div>

<script>

const menuButton = document.getElementById('menuButton');
const mobileMenu = document.getElementById('mobileMenu');

menuButton.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
});

const bookings = <?= json_encode($bookingsData) ?>;

let selectedCourtId = null;
let selectedDate = null;
let selectedStartTime = null;

function openLoginModal() {
    document.getElementById('loginModal').classList.remove('hidden');
    document.getElementById('loginModal').classList.add('flex');
}

function closeLoginModal() {
    document.getElementById('loginModal').classList.add('hidden');
    document.getElementById('loginModal').classList.remove('flex');
}

function openRegisterModal() {
    document.getElementById('registerModal').classList.remove('hidden');
    document.getElementById('registerModal').classList.add('flex');
}

function closeRegisterModal() {
    document.getElementById('registerModal').classList.add('hidden');
    document.getElementById('registerModal').classList.remove('flex');
}

function showRegister() {
    closeLoginModal();
    openRegisterModal();
}

function showLogin() {
    closeRegisterModal();
    openLoginModal();
}

function openBookingModal(courtId, courtName, bookingDate, startTime) {

    <?php if(!isset($_SESSION['user'])): ?>
        openLoginModal();
        return;
    <?php endif; ?>

    selectedCourtId = courtId;
    selectedDate = bookingDate;
    selectedStartTime = startTime;

    document.getElementById('bookingModal').classList.remove('hidden');
    document.getElementById('bookingModal').classList.add('flex');

    document.getElementById('court_id').value = courtId;
    document.getElementById('court_name').value = courtName;
    document.getElementById('booking_date').value = bookingDate;
    document.getElementById('start_time').value = startTime;

    // RESET DURASI
    document.getElementById('duration').value = 1;

    updateDurationUI();
}

function closeBookingModal() {
    document.getElementById('bookingModal').classList.add('hidden');
    document.getElementById('bookingModal').classList.remove('flex');
}

function timeToHour(time) {

    return parseInt(time.split(':')[0]);
}

function getMaxDuration() {

    let startHour = timeToHour(selectedStartTime);

    let maxDuration = 15;

    bookings.forEach(booking => {

        if(
            booking.court_id == selectedCourtId &&
            booking.booking_date == selectedDate
        ) {

            let bookedStart = timeToHour(booking.start_time);

            if(bookedStart > startHour) {

                let available = bookedStart - startHour;

                if(available < maxDuration) {
                    maxDuration = available;
                }
            }
        }
    });

    let endLimit = 23 - startHour;

    if(maxDuration > endLimit) {
        maxDuration = endLimit;
    }

    return maxDuration;
}

function updateDurationUI() {

    let duration = parseInt(document.getElementById('duration').value);

    document.getElementById('duration_text').innerText =
        duration + ' Jam';

    let total = duration * 50000;

    document.getElementById('total_price').value =
        'Rp ' + total.toLocaleString('id-ID');
}

function increaseDuration() {

    let durationInput = document.getElementById('duration');

    let current = parseInt(durationInput.value);

    let maxDuration = getMaxDuration();

    if(current >= maxDuration) {

        alert(
            'Durasi tidak bisa ditambah karena akan bertabrakan dengan booking lain.'
        );

        return;
    }

    current++;

    durationInput.value = current;

    updateDurationUI();
}

function decreaseDuration() {

    let durationInput = document.getElementById('duration');

    let current = parseInt(durationInput.value);

    if(current > 1) {

        current--;

        durationInput.value = current;

        updateDurationUI();
    }
}

updateDurationUI();

</script>

<script>

const galleryImages = <?= json_encode($galleryImages) ?>;

let currentGalleryIndex = 0;

function openGallery(index) {

    currentGalleryIndex = index;

    document.getElementById('galleryModal').classList.remove('hidden');
    document.getElementById('galleryModal').classList.add('flex');

    updateGalleryImage();
}

function closeGallery() {

    document.getElementById('galleryModal').classList.add('hidden');
    document.getElementById('galleryModal').classList.remove('flex');
}

function updateGalleryImage() {

    document.getElementById('galleryImage').src =
        galleryImages[currentGalleryIndex];
}

function nextImage() {

    currentGalleryIndex++;

    if(currentGalleryIndex >= galleryImages.length) {
        currentGalleryIndex = 0;
    }

    updateGalleryImage();
}

function prevImage() {

    currentGalleryIndex--;

    if(currentGalleryIndex < 0) {
        currentGalleryIndex = galleryImages.length - 1;
    }

    updateGalleryImage();
}

// KEYBOARD SUPPORT
document.addEventListener('keydown', function(e) {

    const modal = document.getElementById('galleryModal');

    if(modal.classList.contains('hidden')) return;

    if(e.key === 'Escape') {
        closeGallery();
    }

    if(e.key === 'ArrowRight') {
        nextImage();
    }

    if(e.key === 'ArrowLeft') {
        prevImage();
    }
});

// CLOSE KLIK BACKGROUND
document.getElementById('galleryModal').addEventListener('click', function(e){

    if(e.target.id === 'galleryModal') {
        closeGallery();
    }
});

</script>
</body>
</html>
