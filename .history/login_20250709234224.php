<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nik = $_POST['nik'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE nik = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nik, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header("Location: dashboard_{$user['role']}.php");
    } else {
        $error = "NIK atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - e-Desa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-6 rounded shadow w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4 text-center">Login</h2>
        <?php if (isset($error)): ?>
            <p class="text-red-500 mb-4"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700">NIK</label>
                <input type="text" name="nik" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" class="w-full p-2 border rounded" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700">Login</button>
        </form>
        <p class="mt-4 text-center">Belum punya akun? <a href="register.php" class="text-blue-600">Daftar</a></p>
    </div>
</body>
</html>