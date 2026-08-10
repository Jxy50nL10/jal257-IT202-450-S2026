<?php
// public_html/project/internal/clear_saved_esports.php

require_once(__DIR__ . "/../../../lib/app.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    flash(
        "That saved-esports action is not available.",
        "warning"
    );

    header(
        "Location: " .
        project_url("my_esports.php")
    );

    exit;
}

if (!is_logged_in()) {
    flash(
        "Log in to update saved esports content.",
        "warning"
    );

    header(
        "Location: " .
        project_url("login.php")
    );

    exit;
}

try {
    $db = getDB();

    $stmt = $db->prepare(
        "UPDATE UserEsports
         SET is_active = 0
         WHERE user_id = :user_id
           AND is_active = 1"
    );

    $stmt->execute([
        ":user_id" => get_user_id(),
    ]);

    flash(
        "All saved esports content was removed.",
        "success"
    );

} catch (Throwable $e) {
    error_log(
        "Clear saved esports failed: " .
        $e->getMessage()
    );

    flash(
        "Saved esports content could not be cleared.",
        "danger"
    );
}

header(
    "Location: " .
    project_url("my_esports.php")
);

exit;