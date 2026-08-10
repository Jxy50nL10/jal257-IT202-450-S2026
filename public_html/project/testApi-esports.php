<?php
// public_html/project/testApi-r6siege.php

require_once(__DIR__ . "/../../lib/app.php");

$q = "";
if (isset($_POST["q"]) && is_string($_POST["q"])) {
    $q = trim($_POST["q"]);
}

$decoded = null;
$errors = [];

if (!empty($q)) {
   if (empty($errors)) {
        $result = api_get(
            "https://esportapi1.p.rapidapi.com/api/search/all",
            ["q" => $q],
            ["key_name" => "ESPORTS_API_KEY", "host_name" => "ESPORTS_API_HOST"]
        );

        $decoded = decode_api_response($result, "results", $errors);
    }
}

flash_errors($errors);
?>

<!doctype html>
<html lang="en">

<head>
    <?php render_head("Test Esports API"); ?>
</head>

<body>
    <?php render_nav(); ?>

    <main>
        <h1>Test R6 Siege API</h1>

        <form method="post">
            <label for="entityType">Search</label>
            <input type="text" id="q" name="q" value="<?php echo htmlspecialchars($q); ?>">
            <button name="action" value="test" type="submit">Test Live API</button>
        </form>

        <pre><?php var_dump($decoded); ?></pre>
    </main>

    <?php render_flash_messages(); ?>
</body>

</html>