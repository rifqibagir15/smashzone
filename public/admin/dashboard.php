<?php

session_name('ADMIN_SESSION');
session_start();

require '../../config/database.php';


/*
|--------------------------------------------------------------------------
| LOGIN ADMIN
|--------------------------------------------------------------------------
*/

$adminUsername = "admin";
$adminPassword = "admin123";


if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $username = $_POST['username'];
    $password = $_POST['password'];

    if(
        $username == $adminUsername &&
        $password == $adminPassword
    )
    {
        $_SESSION['role'] = 'admin';

        header("Location: dashboard.php");
        exit;
    }

    $_SESSION['error'] = "Username atau password salah";

    header("Location: index.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| VALIDASI ADMIN
|--------------------------------------------------------------------------
*/

if(
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
)
{
    header("Location: index.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| AMBIL DATA BOOKING
|--------------------------------------------------------------------------
*/

$bookings = mysqli_query($conn, "
    SELECT
        bookings.*,
        courts.court_name
    FROM bookings
    JOIN courts
        ON courts.id = bookings.court_id
    ORDER BY bookings.id DESC
");

?>

<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-[#f5f7fb]">


<!-- ALERT SUCCESS -->
<?php if(isset($_SESSION['success'])): ?>

    <div class="fixed top-5 right-5 bg-green-500 text-white px-6 py-4 rounded-2xl shadow-xl z-50">

        <?= $_SESSION['success']; ?>

    </div>

    <?php unset($_SESSION['success']); ?>

<?php endif; ?>


<!-- ALERT ERROR -->
<?php if(isset($_SESSION['error'])): ?>

    <div class="fixed top-5 right-5 bg-red-500 text-white px-6 py-4 rounded-2xl shadow-xl z-50">

        <?= $_SESSION['error']; ?>

    </div>

    <?php unset($_SESSION['error']); ?>

<?php endif; ?>


<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-72 bg-green-900 text-white p-6 hidden lg:block">

        <h1 class="text-3xl font-bold mb-10">
            ADMIN PANEL
        </h1>

        <div class="space-y-4">

            <a
                href="dashboard.php"
                class="block bg-green-700 px-5 py-4 rounded-2xl font-semibold"
            >
                Dashboard
            </a>

            <a
                href="logout.php"
                class="block hover:bg-red-500 px-5 py-4 rounded-2xl"
            >
                Logout
            </a>

        </div>

    </aside>


    <!-- CONTENT -->
    <main class="flex-1 p-6">

        <!-- HEADER -->
        <div class="bg-white rounded-3xl p-6 shadow-sm mb-8">

            <h2 class="text-4xl font-bold text-green-700">
                Dashboard Booking
            </h2>

            <p class="text-gray-500 mt-2">
                Kelola semua booking user
            </p>

        </div>


        <!-- TABLE -->
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden">

            <div class="overflow-x-auto">

                <table class="w-full min-w-[1200px]">

                    <thead class="bg-green-600 text-white">

                        <tr>

                            <th class="p-5 text-left">
                                User
                            </th>

                            <th class="p-5 text-left">
                                Lapangan
                            </th>

                            <th class="p-5 text-left">
                                Tanggal
                            </th>

                            <th class="p-5 text-left">
                                Jam
                            </th>

                            <th class="p-5 text-left">
                                Durasi
                            </th>

                            <th class="p-5 text-left">
                                Total
                            </th>

                            <th class="p-5 text-left">
                                Bukti
                            </th>

                            <th class="p-5 text-left">
                                Status
                            </th>

                            <th class="p-5 text-center">
                                Aksi
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php while($booking = mysqli_fetch_assoc($bookings)): ?>

                        <tr class="border-b hover:bg-gray-50">

                            <!-- USER -->
                            <td class="p-5">

                                <div class="font-bold">
                                    <?= htmlspecialchars($booking['booking_name']) ?>
                                </div>

                                <div class="text-sm text-gray-500">
                                    <?= htmlspecialchars($booking['whatsapp_number']) ?>
                                </div>

                            </td>


                            <!-- LAPANGAN -->
                            <td class="p-5 font-semibold">

                                <?= htmlspecialchars($booking['court_name']) ?>

                            </td>


                            <!-- TANGGAL -->
                            <td class="p-5">

                                <?= $booking['booking_date'] ?>

                            </td>


                            <!-- JAM -->
                            <td class="p-5">

                                <?= substr($booking['start_time'],0,5) ?>
                                -
                                <?= substr($booking['end_time'],0,5) ?>

                            </td>


                            <!-- DURASI -->
                            <td class="p-5">

                                <?= $booking['duration'] ?> Jam

                            </td>


                            <!-- TOTAL -->
                            <td class="p-5 font-bold text-green-600">

                                Rp <?= number_format($booking['total_price']) ?>

                            </td>


                            <!-- BUKTI -->
                            <td class="p-5">

                                <?php if($booking['payment_proof']): ?>

                                    <a
                                        href="/badminton-booking/public/uploads/payments/<?= $booking['payment_proof'] ?>"
                                        target="_blank"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-xl"
                                    >

                                        Lihat

                                    </a>

                                <?php else: ?>

                                    <span class="text-gray-400">
                                        Belum Upload
                                    </span>

                                <?php endif; ?>

                            </td>


                            <!-- STATUS -->
                            <td class="p-5">

                                <?php

                                $badge = "bg-gray-500";

                                if($booking['status'] == 'waiting_verification')
                                {
                                    $badge = "bg-yellow-500";
                                }

                                if($booking['status'] == 'confirmed')
                                {
                                    $badge = "bg-green-500";
                                }

                                if($booking['status'] == 'cancelled')
                                {
                                    $badge = "bg-red-500";
                                }

                                ?>

                                <div class="<?= $badge ?> text-white px-4 py-2 rounded-xl inline-block">

                                    <?= strtoupper($booking['status']) ?>

                                </div>

                            </td>


                            <!-- AKSI -->
                            <td class="p-5">

                                <div class="flex flex-wrap gap-2 justify-center">

                                    <?php if($booking['status'] != 'confirmed'): ?>

                                        <a
                                            href="booking/approve.php?id=<?= $booking['id'] ?>"
                                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-xl"
                                        >

                                            Approve

                                        </a>

                                    <?php endif; ?>


                                    <a
                                        href="booking/cancel.php?id=<?= $booking['id'] ?>"
                                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl"
                                    >

                                        Cancel

                                    </a>


                                    <?php if($booking['status'] != 'cancelled'): ?>

                                        <a
                                            href="booking/reschedule.php?id=<?= $booking['id'] ?>"
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-xl"
                                        >

                                            Pindah Jadwal

                                        </a>

                                    <?php endif; ?>

                                </div>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </main>

</div>

</body>
</html>