<?php
require 'config.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (empty($username) || empty($password) || empty($password_confirm)) {
        $errors[] = "All fields are required.";
    }

    if ($password !== $password_confirm) {
        $errors[] = "Passwords do not match.";
    }

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    if ($stmt->rowCount() > 0) {
        $errors[] = "Username is already taken.";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$username, $hashed_password])) {
            header("Location: login.php");
            exit;
        } else {
            $errors[] = "Error creating user.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Register</h2>
        <form method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="password_confirm">Confirm Password</label>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger mt-3">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>