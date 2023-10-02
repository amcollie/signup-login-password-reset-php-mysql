<?php

    declare(strict_types=1);

    session_start();

    use App\Database;

    $user = null;

    if (isset($_SESSION['user_id'])) {
        require dirname(__DIR__) . '/vendor/autoload.php';

        $mysqli = (new Database())->connect();

        $stmt = $mysqli->stmt_init();
        if (!$stmt->prepare('SELECT * FROM users WHERE id = ?')) {
            http_response_code(500);
            die('Could not prepare statement');
        }

        $stmt->bind_param('i', $_SESSION['user_id']);
        if (!$stmt->execute()) {
            http_response_code(500);
            die('Could not retrieve user');
        }

        if (!$result = $stmt->get_result()) {
            http_response_code(404);
            die('user not found');
        }

        $user = $result->fetch_assoc();
    }
?>
<?php require __DIR__ . '/header.php'; ?>
    <h1>Home</h1>

    <?php if (isset($_SESSION['user_id']) && isset($user)): ?>
        <p>Hello, <?= htmlspecialchars($user['name']) ?>.</p>
        <p><a href="logout.php">Log out</a></p>
    <?php else: ?>
        <p><a href="login.php">Login</a> or <a href="register.html">Register</a></p>
    <?php endif; ?>
<?php require __DIR__ . '/footer.php'; ?>