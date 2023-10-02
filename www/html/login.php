<?php
    declare(strict_types=1);

    use App\Database;

    $is_invalid = false;
    $email = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require dirname(__DIR__) . '/vendor/autoload.php';
        $mysqli = (new Database())->connect();

        $stmt = $mysqli->stmt_init();
        if (!$stmt->prepare('SELECT * FROM users WHERE email = ?')) {
            http_response_code(500);
            die('Could not prepare statement: ' . $mysqli->error);
        }

        $email = htmlspecialchars($_POST['email']);
        $stmt->bind_param('s', $email);
        if (!$stmt->execute()) {
            http_response_code(500);
            die('Could not execute statement: '. $mysqli->error);
        }

        if (!$result = $stmt->get_result()) {
            http_response_code(500);
            die('Could not get result: ' . $mysqli->error);
        }

        $user = $result->fetch_assoc();

        if ($user) {
            if (password_verify($_POST['password'], $user['password_hash'])) {
                session_start();
                session_regenerate_id();

                $_SESSION['user_id'] = $user['id'];

                header('Location: index.php');
                exit();
            }
        }

        $is_invalid = true;

    }
?>

<?php require __DIR__ . '/header.php'; ?>
  <h1>Login</h1>
  <?php if ($is_invalid): ?>
    <em>Invalid credentials</em>
  <?php endif; ?>
  <form method="post">
    <div>
      <label for="email">Email</label>
      <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?? '' ?>" />
    </div>
    <div>
      <label for="password">Password</label>
      <input type="password" id="password" name="password" />
    </div>
    <button type="submit" value="Login">Login</button>
  </form>

  <a href="forgot-password.php">Forgot password?</a>
<?php require __DIR__. '/footer.php'; ?>
