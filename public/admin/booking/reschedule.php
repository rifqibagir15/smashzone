<?php

session_name('ADMIN_SESSION');
session_start();

require '../../../config/database.php';


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
    header("Location: ../index.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| AMBIL ID BOOKING
|--------------------------------------------------------------------------
*/

$id = $_GET['id'] ?? 0;


/*
|--------------------------------------------------------------------------
| AMBIL DATA BOOKING
|--------------------------------------------------------------------------
*/

$query = mysqli_query($conn, "
    SELECT bookings.*, courts.court_name
    FROM bookings
    JOIN courts ON courts.id = bookings.court_id
    WHERE bookings.id='$id'
    LIMIT 1
");

if(mysqli_num_rows($query) == 0)
{
    die("Booking tidak ditemukan");
}

$booking = mysqli_fetch_assoc($query);


/*
|--------------------------------------------------------------------------
| DEFAULT FORM VALUE
|--------------------------------------------------------------------------
*/

$selectedCourtId = $_POST['court_id'] ?? $booking['court_id'];

$selectedDate = $_POST['booking_date'] ?? $booking['booking_date'];

$selectedStartTime = $_POST['start_time'] ?? $booking['start_time'];


/*
|--------------------------------------------------------------------------
| AMBIL SEMUA LAPANGAN
|--------------------------------------------------------------------------
*/

$courtsQuery = mysqli_query($conn, "
    SELECT *
    FROM courts
    ORDER BY id ASC
");


/*
|--------------------------------------------------------------------------
| SUBMIT RESCHEDULE
|--------------------------------------------------------------------------
*/

if(isset($_POST['save_reschedule']))
{
    $bookingDate = mysqli_real_escape_string(
        $conn,
        $_POST['booking_date']
    );

    $startTime = date(
        'H:i:s',
        strtotime($_POST['start_time'])
    );

    $courtId = mysqli_real_escape_string(
        $conn,
        $_POST['court_id']
    );

    $duration = $booking['duration'];


    /*
    |--------------------------------------------------------------------------
    | VALIDASI TANGGAL
    |--------------------------------------------------------------------------
    */

    if($bookingDate < date('Y-m-d'))
    {
        $_SESSION['error'] = 'Tidak bisa memilih tanggal yang sudah lewat';

        header("Location: ../dashboard.php");
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | HITUNG END TIME
    |--------------------------------------------------------------------------
    */

    $endTime = date(
        'H:i:s',
        strtotime($startTime . ' +' . $duration . ' hour')
    );


    /*
    |--------------------------------------------------------------------------
    | VALIDASI JAM OPERASIONAL
    |--------------------------------------------------------------------------
    */

    if($endTime > '23:00:00')
    {
        $_SESSION['error'] = 'Melewati jam operasional';

        header("Location: ../dashboard.php");
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDASI STATUS
    |--------------------------------------------------------------------------
    */

    if($booking['status'] == 'cancelled')
    {
        $_SESSION['error'] = 'Booking cancelled tidak bisa dipindahkan';

        header("Location: ../dashboard.php");
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | CEK BENTROK
    |--------------------------------------------------------------------------
    */

    $check = mysqli_query($conn, "
        SELECT *
        FROM bookings
        WHERE court_id = '$courtId'
        AND booking_date = '$bookingDate'
        AND id != '$id'
        AND status != 'cancelled'
        AND (
            start_time < '$endTime'
            AND end_time > '$startTime'
        )
    ");

    if(!$check)
    {
        die(mysqli_error($conn));
    }

    if(mysqli_num_rows($check) > 0)
    {
        $_SESSION['error'] = 'Jadwal bentrok dengan booking lain';

        header("Location: ../dashboard.php");
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE BOOKING
    |--------------------------------------------------------------------------
    */

    $update = mysqli_query($conn, "
        UPDATE bookings
        SET
            court_id = '$courtId',
            booking_date = '$bookingDate',
            start_time = '$startTime',
            end_time = '$endTime'
        WHERE id='$id'
    ");

    if(!$update)
    {
        $_SESSION['error'] = 'Gagal memindahkan jadwal';

        header("Location: ../dashboard.php");
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | SUCCESS
    |--------------------------------------------------------------------------
    */

    $_SESSION['success'] = 'Jadwal berhasil dipindahkan';

    header("Location: ../dashboard.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Pindah Jadwal</title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-6">

<div class="bg-white w-full max-w-xl rounded-3xl p-8 shadow-xl">

    <h1 class="text-3xl font-bold mb-6 text-center">
        Pindah Jadwal Booking
    </h1>


    <!-- INFO BOOKING -->
    <div class="mb-6 bg-gray-100 rounded-2xl p-5">

        <div class="mb-2">
            <b>User:</b>
            <?= htmlspecialchars($booking['booking_name']) ?>
        </div>

        <div class="mb-2">
            <b>Lapangan:</b>
            <?= htmlspecialchars($booking['court_name']) ?>
        </div>

        <div class="mb-2">
            <b>Durasi:</b>
            <?= $booking['duration'] ?> Jam
        </div>

        <div>
            <b>Total:</b>
            Rp <?= number_format($booking['total_price']) ?>
        </div>

    </div>


    <!-- FORM -->
    <form method="POST" class="space-y-5">


        <!-- PILIH LAPANGAN -->
        <div>

            <label class="font-semibold">
                Pilih Lapangan
            </label>

            <select
                name="court_id"
                required
                class="w-full border rounded-2xl px-4 py-4 mt-2"
            >

                <?php while($court = mysqli_fetch_assoc($courtsQuery)): ?>

                    <option
                        value="<?= $court['id'] ?>"
                        <?= $selectedCourtId == $court['id']
                            ? 'selected'
                            : '' ?>
                    >

                        <?= htmlspecialchars($court['court_name']) ?>

                    </option>

                <?php endwhile; ?>

            </select>

        </div>


        <!-- TANGGAL -->
        <div>

            <label class="font-semibold">
                Tanggal Baru
            </label>

            <input
                type="date"
                name="booking_date"
                required
                min="<?= date('Y-m-d') ?>"
                value="<?= $selectedDate ?>"
                class="w-full border rounded-2xl px-4 py-4 mt-2"
            >

        </div>


        <!-- JAM -->
        <div>

            <label class="font-semibold">
                Jam Mulai Baru
            </label>

            <select
                name="start_time"
                required
                class="w-full border rounded-2xl px-4 py-4 mt-2"
            >

                <?php for($i = 8; $i <= 22; $i++): ?>

                    <?php

                    $time = sprintf('%02d:00:00', $i);

                    $tempEnd = date(
                        'H:i:s',
                        strtotime($time . ' +' . $booking['duration'] . ' hour')
                    );

                    $conflict = mysqli_query($conn, "
                        SELECT *
                        FROM bookings
                        WHERE court_id = '$selectedCourtId'
                        AND booking_date = '$selectedDate'
                        AND id != '$id'
                        AND status != 'cancelled'
                        AND (
                            start_time < '$tempEnd'
                            AND end_time > '$time'
                        )
                    ");

                    $isConflict = mysqli_num_rows($conflict) > 0;

                    ?>

                    <option
                        value="<?= $time ?>"
                        <?= $selectedStartTime == $time
                            ? 'selected'
                            : '' ?>
                        <?= $isConflict ? 'disabled' : '' ?>
                    >

                        <?= sprintf('%02d:00', $i) ?>

                        <?= $isConflict ? ' (Bentrok)' : '' ?>

                    </option>

                <?php endfor; ?>

            </select>

        </div>


        <!-- DURASI -->
        <div>

            <label class="font-semibold">
                Durasi Booking
            </label>

            <input
                type="text"
                disabled
                value="<?= $booking['duration'] ?> Jam"
                class="w-full border rounded-2xl px-4 py-4 mt-2 bg-gray-100"
            >

        </div>


        <!-- BUTTON -->
        <div class="flex gap-4 pt-4">

            <a
                href="../dashboard.php"
                class="w-full bg-gray-300 hover:bg-gray-400 py-4 rounded-2xl text-center font-bold"
            >

                Batal

            </a>

            <button
                type="submit"
                name="save_reschedule"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white py-4 rounded-2xl font-bold"
            >

                Simpan Perubahan

            </button>

        </div>

    </form>

</div>

</body>
</html>