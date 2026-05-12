<?php
session_start();

if(isset($_SESSION['admin'])) {
    header("Location: dashboard.php");
    exit;
}

$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">

    <div class="bg-white w-full max-w-md rounded-3xl shadow-xl p-8">

        <div class="text-center mb-8">

            <h1 class="text-4xl font-bold text-green-600">
                ADMIN
            </h1>

            <p class="text-gray-500 mt-2">
                Login Dashboard
            </p>

        </div>

        <?php if($error): ?>

            <div class="bg-red-500 text-white p-4 rounded-2xl mb-5">
                <?= $error ?>
            </div>

        <?php endif; ?>

        <form action="dashboard.php" method="POST" class="space-y-5">

            <div>
                <label class="font-semibold">
                    Username
                </label>

                <input
                    type="text"
                    name="username"
                    required
                    class="w-full border rounded-2xl px-4 py-4 mt-2">
            </div>

            <div>
                <label class="font-semibold">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    required
                    class="w-full border rounded-2xl px-4 py-4 mt-2">
            </div>

            <button
                class="w-full bg-green-500 hover:bg-green-600 text-white py-4 rounded-2xl font-bold text-lg">

                Login

            </button>

        </form>

    </div>

</body>
</html>