<?php

declare(strict_types=1);

use App\Database;

require_once dirname(__DIR__) . '/vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $token = htmlspecialchars($_GET['token']);  

    $token_hash = hash('sha256', $token);
    $mysqli = (new Database())->connect();

    $stmt = $mysqli->stmt_init();
    if (!$stmt->prepare(
        'SELECT * 
        FROM users 
        WHERE reset_token_hash =?'
    )) {
        http_response_code(500);
        die('Unable to retrieve data: '. $mysqli->error);
    }

    $stmt->bind_param('s', $token_hash);
    if (!$stmt->execute()) {
        http_response_code(500);
        die('Unable to execute statement: '. $mysqli->error);
    }

    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        http_response_code(404);
        die('No user found: '. $mysqli->error);
    }
    $user = $result->fetch_assoc();

    if ($user['reset_token_expires_at'] < date('Y-m-d H:i:s')) {
        http_response_code(400);
        die('Token has expired');
    }

    // die('token has not expired');

} else {
    http_response_code(405);
    die('Method not allowed. Allowed methods are GET');
}
?>
<?php require __DIR__ . '/header.php'; ?>

    <h1>Reset Password</h1>

    <form id="password-reset-form" action="process-reset-password.php" method="post">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <div>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" />
        </div>
        <div>
            <label for="confirm-password">Confirm Password</label>
            <input type="password" id="confirm-password" name="confirm-password" />
        </div>
        <button type="submit" value="Reset">Reset Password</button>
    </form>
    <script>
        'use strict'

        const validation = new JustValidate('#password-reset-form')

        validation
        .addField('#password', [
            {
            rule: 'required',
            },
            {
            rule: 'password',
            },
        ])
        .addField('#confirm-password', [
            {
            validator: (value, fields) => {
                return value === fields['#password'].elem.value
            },
            errorMessage: 'Passwords should match',
            },
        ])
        .onSuccess(() => {
            document.querySelector('#password-reset-form').submit()
        })

    </script>
<?php require __DIR__ . '/footer.php'; ?>
    
</body>
</html>