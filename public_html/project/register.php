<?php
require_once(__DIR__ . "/../../lib/app.php");
$errors = [];
$email = "";

if (isset($_POST["email"], $_POST["password"], $_POST["confirm_password"])) {
    $email = <?php
// File: public_html/project/register.php
// Existing require_once, message setup, and earlier page variables stay above this block.
// Replace the earlier POST validation block with this helper-based version.
$errors = [];
$email = "";

if (isset($_POST["email"], $_POST["password"], $_POST["confirm_password"])) {
    $email = sanitize_email($_POST["email"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];

    validate_email($email, $errors);
    validate_password($password, $errors);
    validate_passwords_match($password, $confirmPassword, $errors);

    if (empty($errors)) {
        // Existing password_hash and INSERT code continues here.
    }
}
// Existing HTML form stays below this block.
?>
;
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];

     validate_email($email, $errors);
    validate_password($password, $errors);
    validate_passwords_match($password, $confirmPassword, $errors);

   
    
        // TODO: connect to the database, hash the password, and insert the user.
        if (empty($errors)) {
    try {
        $db = getDB();
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $db->prepare(
            "INSERT INTO Users (email, password_hash)
             VALUES (:email, :password_hash)"
        );
        $stmt->execute([
            ":email" => $email,
            ":password_hash" => $hash,
        ]);

        error_log("Registration insert succeeded for user id " . $db->lastInsertId());
        echo "Registration saved. This temporary message can be replaced later.";
        $email = "";
    } catch (PDOException $e) {
         if ($e->getCode() === "23000") {
            $errors[] = "That email is already registered.";
        } else {
            error_log("Registration failed: " . $e->getMessage());
            $errors[] = "Registration failed. Please try again.";
        }
    }
    }
    }
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <?php render_nav(); ?>
    <h1>Register</h1>
    <form method="post" action="register.php">
    <label for="email">Email</label>
    <input id="email" name="email" type="email"
           required autocomplete="email"
           value="<?php echo htmlspecialchars($email); ?>">

    <label for="password">Password</label>
    <input id="password" name="password" type="password"
           required minlength="8" autocomplete="new-password">

    <label for="confirm_password">Confirm Password</label>
    <input id="confirm_password" name="confirm_password" type="password"
           required minlength="8" autocomplete="new-password">

    <button type="submit">Register</button>

    
    <p id="form-message"></p>

    <label for="email">Email</label>
    <input id="email" name="email" type="email"
           required autocomplete="email"
           value="<?php echo htmlspecialchars($email); ?>">

    <label for="password">Password</label>
    <input id="password" name="password" type="password"
           required minlength="8" autocomplete="new-password">

    <label for="confirm_password">Confirm Password</label>
    <input id="confirm_password" name="confirm_password" type="password"
           required minlength="8" autocomplete="new-password">

    <button type="submit">Register</button>
</form>

<script>
function validate(form) {
    const message = document.querySelector("#form-message");
    const errors = [];

    validate_email(form.email, errors);
    validate_password(form.password, errors);
    validate_passwords_match(form.password, form.confirm_password, errors);

     return show_validation_errors(message, errors);
}
</script>

</body>
</html>
