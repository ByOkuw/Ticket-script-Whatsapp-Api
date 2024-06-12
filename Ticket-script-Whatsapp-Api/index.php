<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$errors = [];
$subject = '';
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    $user_id = $_SESSION['user_id'];

    if (empty($subject) || empty($message)) {
        $errors[] = "All fields are required.";
    }

    if (empty($errors)) {
        $sql = "INSERT INTO tickets (user_id, subject, message, status) VALUES (?, ?, ?, 'Open')";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$user_id, $subject, $message])) {
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Error creating ticket.";
        }
    }
}

$sql = "SELECT * FROM tickets WHERE user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$tickets = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Create Ticket</h2>
        <form method="post">
            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" class="form-control" id="subject" name="subject" value="<?= htmlspecialchars($subject) ?>" required>
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea class="form-control" id="message" name="message" rows="4" required><?= htmlspecialchars($message) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
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

        <h2 class="mt-5">Your Tickets</h2>
        <?php if (count($tickets) > 0): ?>
            <ul class="list-group">
                <?php foreach ($tickets as $ticket): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="ticket.php?id=<?= $ticket['id'] ?>">
                            <?= htmlspecialchars($ticket['subject']) ?>
                        </a>
                        <span class="badge badge-primary"><?= htmlspecialchars($ticket['status']) ?></span>
                        <a href="https://wa.me/90XXXXXXXXXX?text=Ticket%20Subject:%20<?= urlencode($ticket['subject']) ?>%0ATicket%20Message:%20<?= urlencode($ticket['message']) ?>" class="btn btn-success btn-sm" target="_blank">Notify via WhatsApp</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>You have no tickets.</p>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>