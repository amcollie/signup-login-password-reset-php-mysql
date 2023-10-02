<?php require __DIR__ . '/header.php'; ?>
    <h1>Forgot Password</h1>

    <form action="send-password-reset.php" method="post">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>

        <button type="submit">Send</button>
    </form>
<?php require __DIR__ . '/footer.php'; ?>