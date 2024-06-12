<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$ticket_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = trim($_POST['message']);

    if (!empty($message)) {
        $sql = "INSERT INTO ticket_responses (ticket_id, user_id, message) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$ticket_id, $user_id, $message])) {
            header("Location: ticket.php?id=$ticket_id");
            exit;
        } else {
            $errors[] = "Error submitting response.";
        }
    } else {
        $errors[] = "Message cannot be empty.";
    }
}

$sql = "SELECT * FROM tickets WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$ticket_id]);
$ticket = $stmt->fetch();

$sql = "SELECT * FROM ticket_responses WHERE ticket_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$ticket_id]);
$responses = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Details</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Ticket Details</h2>
        <div class="card mb-4">
            <div class="card-header">
                Subject: <?= htmlspecialchars($ticket['subject']) ?>
            </div>
            <div class="card-body">
                <p class="card-text"><?= nl2br(htmlspecialchars($ticket['message'])) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($ticket['status']) ?></p>
            </div>
        </div>

        <h3>Responses</h3>
        <?php if (count($responses) > 0): ?>
            <ul class="list-group mb-4">
                <?php foreach ($responses as $response): ?>
                    <li class="list-group-item"><?= nl2br(htmlspecialchars($response['message'])) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No responses yet.</p>
        <?php endif; ?>

        <h3>Reply</h3>
        <form method="post">
            <div class="form-group">
                <label for="message">Message</label>
                <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
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
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
