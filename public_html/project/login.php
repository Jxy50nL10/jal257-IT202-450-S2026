<?php
require_once(__DIR__ . "/../../lib/app.php");

$errors = [];
$email = "";
$message = implode("<br>", array_map("htmlspecialchars", $errors));
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <p id="message"><?php echo $message; ?></p>
    <form method="post" action="login.php" onsubmit="return validate(this)">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required
               autocomplete="email"
               value="<?php echo htmlspecialchars($email); ?>">

        <label for="password">Password</label>
        <input id="password" name="password" type="password"
               required minlength="8"
               autocomplete="current-password">

        <button type="submit">Login</button>
    </form>
    <script>
    function validate(form) {
       const message = document.getElementById("message");
    const errors = [];

    validate_email(form.email, errors);
    validate_password(form.password, errors);

    return show_validation_errors(message, errors);
    }
    </script>
</body>
</html>

