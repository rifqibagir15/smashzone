<?php

session_start();
require '../../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit;
}

$userId = $_SESSION['user']['id'];


// GET USER
$userQuery = mysqli_query($conn, "
    SELECT * FROM users
    WHERE id = '$userId'
    LIMIT 1
");

$user = mysqli_fetch_assoc($userQuery);


// GET HISTORY
$historyQuery = mysqli_query($conn, "
    SELECT
        bookings.*,
        courts.court_name,
        payments.payment_proof,
        payments.payment_status

    FROM bookings

    LEFT JOIN courts
    ON bookings.court_id = courts.id

    LEFT JOIN payments
    ON bookings.id = payments.booking_id

    WHERE bookings.user_id = '$userId'

    ORDER BY bookings.id DESC
");

?>
<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Profile</title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100 min-h-screen pt-20">


<!-- NAVBAR -->
<nav class="bg-white shadow-md fixed w-full top-0 left-0 z-50">

    <div class="max-w-7xl mx-auto px-4">

        <div class="h-16 flex items-center justify-between">

            <!-- LOGO -->
            <a href="../index.php"
               class="text-2xl font-bold text-green-600">
                Badminton Booking
            </a>

            <!-- DESKTOP MENU -->
            <div class="hidden md:flex items-center gap-4 font-medium">

                <a href="../index.php#home"
                   class="hover:text-green-600 transition">
                    Home
                </a>

                <a href="../index.php#tentang"
                   class="hover:text-green-600 transition">
                    Tentang Kami
                </a>

                <a href="../index.php#prosedur"
                   class="hover:text-green-600 transition">
                    Prosedur Booking
                </a>

                <a href="https://wa.me/6287729359927"
                   target="_blank"
                   class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-xl transition">
                    Kontak Admin
                </a>

                <span class="font-semibold">
                    <?= htmlspecialchars($user['username']) ?>
                </span>

                <a href="profile.php"
                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-xl">
                    Profile
                </a>

                <a href="../auth/logout.php"
                   class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl">
                    Logout
                </a>

            </div>

            <!-- MOBILE BUTTON -->
            <button id="menuButton"
                    class="md:hidden text-3xl">
                ☰
            </button>

        </div>

    </div>

    <!-- MOBILE MENU -->
    <div id="mobileMenu"
         class="hidden md:hidden bg-white border-t">

        <div class="flex flex-col p-4 gap-4 font-medium">

            <a href="../index.php#home">
                Home
            </a>

            <a href="../index.php#tentang">
                Tentang Kami
            </a>

            <a href="../index.php#prosedur">
                Prosedur Booking
            </a>

            <a href="https://wa.me/628123456789"
               target="_blank"
               class="bg-green-500 text-center text-white py-2 rounded-xl">
                Kontak Admin
            </a>

            <a href="profile.php"
               class="bg-blue-500 text-center text-white py-2 rounded-xl">
                Profile
            </a>

            <a href="../auth/logout.php"
               class="bg-red-500 text-center text-white py-2 rounded-xl">
                Logout
            </a>

        </div>

    </div>

</nav>


<!-- PROFILE -->
<section class="py-10">

    <div class="max-w-7xl mx-auto px-4">

        <div class="grid lg:grid-cols-3 gap-8">


            <!-- LEFT -->
            <div class="bg-white rounded-3xl shadow-lg p-8 h-fit">

                <div class="text-center">

                    <div class="w-32 h-32 rounded-full bg-green-500 text-white text-5xl font-bold flex items-center justify-center mx-auto mb-6">
                        <?= strtoupper(substr($user['username'], 0, 1)) ?>
                    </div>

                    <h2 class="text-3xl font-bold mb-2">
                        <?= htmlspecialchars($user['username']) ?>
                    </h2>

                    <p class="text-gray-500">
                        <?= htmlspecialchars($user['email']) ?>
                    </p>

                </div>


                <div class="mt-10 space-y-5">

                    <div>
                        <label class="text-sm text-gray-500">
                            Username
                        </label>

                        <div class="mt-1 border rounded-2xl px-4 py-3 bg-gray-50">
                            <?= htmlspecialchars($user['username']) ?>
                        </div>
                    </div>


                    <div>
                        <label class="text-sm text-gray-500">
                            Email
                        </label>

                        <div class="mt-1 border rounded-2xl px-4 py-3 bg-gray-50">
                            <?= htmlspecialchars($user['email']) ?>
                        </div>
                    </div>


                    <div>
                        <label class="text-sm text-gray-500">
                            Nomor WhatsApp
                        </label>

                        <div class="mt-1 border rounded-2xl px-4 py-3 bg-gray-50">
                            <?= htmlspecialchars($user['phone']) ?>
                        </div>
                    </div>

                </div>

            </div>


            <!-- RIGHT -->
            <div class="lg:col-span-2">

                <div class="bg-white rounded-3xl shadow-lg p-8">

                    <div class="flex items-center justify-between mb-8">

                        <div>

                            <h2 class="text-3xl font-bold">
                                History Booking
                            </h2>

                            <p class="text-gray-500 mt-1">
                                Semua riwayat booking Anda
                            </p>

                        </div>

                    </div>


                    <?php if(mysqli_num_rows($historyQuery) > 0): ?>

                        <div class="space-y-6">

                            <?php while($history = mysqli_fetch_assoc($historyQuery)): ?>

                                <?php

                                $statusColor = 'bg-gray-500';

                                if($history['status'] == 'confirmed') {
                                    $statusColor = 'bg-green-500';
                                }

                                if($history['status'] == 'pending') {
                                    $statusColor = 'bg-yellow-500';
                                }

                                if($history['status'] == 'waiting_verification') {
                                    $statusColor = 'bg-blue-500';
                                }

                                if($history['status'] == 'cancelled') {
                                    $statusColor = 'bg-red-500';
                                }

                                ?>

                                <div class="border rounded-3xl p-6 hover:shadow-lg transition">

                                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">

                                        <!-- LEFT -->
                                        <div class="space-y-3">

                                            <div class="flex items-center gap-3 flex-wrap">

                                                <h3 class="text-2xl font-bold">
                                                    <?= htmlspecialchars($history['court_name']) ?>
                                                </h3>

                                                <div class="<?= $statusColor ?> text-white px-4 py-1 rounded-full text-sm font-semibold">
                                                    <?= strtoupper($history['status']) ?>
                                                </div>

                                            </div>


                                            <div class="grid md:grid-cols-2 gap-4 text-gray-600">

                                                <div>
                                                    <span class="font-semibold">
                                                        Atas Nama:
                                                    </span>

                                                    <?= htmlspecialchars($history['booking_name']) ?>
                                                </div>


                                                <div>
                                                    <span class="font-semibold">
                                                        WhatsApp:
                                                    </span>

                                                    <?= htmlspecialchars($history['whatsapp_number']) ?>
                                                </div>


                                                <div>
                                                    <span class="font-semibold">
                                                        Tanggal:
                                                    </span>

                                                    <?= date('d F Y', strtotime($history['booking_date'])) ?>
                                                </div>


                                                <div>
                                                    <span class="font-semibold">
                                                        Jam:
                                                    </span>

                                                    <?= substr($history['start_time'], 0, 5) ?>
                                                    -
                                                    <?= substr($history['end_time'], 0, 5) ?>
                                                </div>


                                                <div>
                                                    <span class="font-semibold">
                                                        Durasi:
                                                    </span>

                                                    <?= $history['duration'] ?> Jam
                                                </div>


                                                <div>
                                                    <span class="font-semibold">
                                                        Total:
                                                    </span>

                                                    Rp <?= number_format($history['total_price'], 0, ',', '.') ?>
                                                </div>

                                            </div>

                                        </div>


                                        <!-- RIGHT -->
                                        <div class="flex flex-col gap-3 min-w-[180px]">

                                            <?php if(!empty($history['payment_proof'])): ?>

                                                <a href="../uploads/payments/<?= $history['payment_proof'] ?>"
                                                   target="_blank"
                                                   class="bg-blue-500 hover:bg-blue-600 text-white text-center px-4 py-3 rounded-2xl font-semibold">
                                                    Lihat Bukti
                                                </a>

                                            <?php endif; ?>


                                            <?php if($history['status'] == 'pending'): ?>

                                                <button
                                                    onclick="openPaymentModal(
                                                        '<?= $history['id'] ?>'
                                                    )"
                                                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-3 rounded-2xl font-semibold">

                                                    Upload Pembayaran

                                                </button>

                                            <?php endif; ?>

                                        </div>

                                    </div>

                                </div>

                            <?php endwhile; ?>

                        </div>

                    <?php else: ?>

                        <div class="text-center py-20">

                            <div class="text-7xl mb-5">
                                🏸
                            </div>

                            <h3 class="text-3xl font-bold mb-3">
                                Belum Ada Booking
                            </h3>

                            <p class="text-gray-500 mb-8">
                                Anda belum pernah booking lapangan.
                            </p>

                            <a href="../index.php"
                               class="bg-green-500 hover:bg-green-600 text-white px-8 py-4 rounded-2xl font-semibold">
                                Booking Sekarang
                            </a>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- PAYMENT MODAL -->
<div id="paymentModal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 px-4">

    <div class="bg-white rounded-3xl p-8 w-full max-w-lg relative">

        <button onclick="closePaymentModal()"
                class="absolute top-5 right-5 text-3xl text-gray-400 hover:text-black">
            ×
        </button>

        <div class="text-center">

            <h2 class="text-3xl font-bold mb-3">
                Upload Bukti Pembayaran
            </h2>

            <p class="text-gray-500 mb-8">
                Upload bukti transfer / QRIS pembayaran
            </p>

        </div>


        <!-- QRIS -->
        <div class="bg-gray-100 rounded-3xl p-6 mb-8">

            <img src="../assets/images/qris.jpeg"
                 class="w-full rounded-2xl"
                 alt="QRIS">

        </div>


        <!-- FORM -->
        <form action="../booking/upload_payment.php"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-5">

            <input type="hidden"
                   name="booking_id"
                   id="booking_id">


            <div>

                <label class="font-semibold">
                    Upload Bukti
                </label>

                <input type="file"
                       name="payment_proof"
                       required
                       accept="image/*"
                       class="w-full border rounded-2xl px-4 py-3 mt-2">

            </div>


            <button class="w-full bg-green-500 hover:bg-green-600 text-white py-4 rounded-2xl font-bold text-lg">

                Upload Sekarang

            </button>

        </form>

    </div>

</div>



<script>

const menuButton = document.getElementById('menuButton');
const mobileMenu = document.getElementById('mobileMenu');

menuButton.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
});


function openPaymentModal(bookingId)
{
    document.getElementById('paymentModal').classList.remove('hidden');
    document.getElementById('paymentModal').classList.add('flex');

    document.getElementById('booking_id').value = bookingId;
}


function closePaymentModal()
{
    document.getElementById('paymentModal').classList.add('hidden');
    document.getElementById('paymentModal').classList.remove('flex');
}

</script>

</body>
</html>