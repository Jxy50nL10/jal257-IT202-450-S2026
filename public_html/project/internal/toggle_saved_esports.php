<?php
// public_html/project/internal/toggle_saved_esports.php

/**
 * Usage contract:
 * - Accepts POST requests from the Save/Remove form in the esports card partial.
 * - Requires a logged-in user.
 * - Expects esports_id as a positive integer and new_is_saved as 0 or 1.
 * - Accepts an optional local return_to path and query string for an approved page.
 * - Updates only the current session user's relationship, then flashes and redirects.
 */

require_once(__DIR__ . "/../../../lib/app.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . project_url("esports.php"));
    exit;
}

if (!is_logged_in()) {
    flash("Log in before saving esports content.", "warning");
    header("Location: " . project_url("login.php"));
    exit;
}

$return_url = project_url("esports.php");

$requested_return = $_POST["return_to"] ?? "";

$allowed_return_paths = [
    project_url("esports.php"),
    project_url("my_esports.php"),
    project_url("esport.php"),
];

// Reject line breaks that could alter the redirect header,
// then allow only known local pages.
if (
    is_string($requested_return)
    && !str_contains($requested_return, "\r")
    && !str_contains($requested_return, "\n")
) {
    foreach ($allowed_return_paths as $allowed_path) {
        if (
            $requested_return === $allowed_path
            || str_starts_with(
                $requested_return,
                $allowed_path . "?"
            )
        ) {
            $return_url = $requested_return;
            break;
        }
    }
}

$esports_id = filter_input(
    INPUT_POST,
    "esports_id",
    FILTER_VALIDATE_INT
);

if (!$esports_id) {
    flash("Choose valid esports content.", "warning");
    header("Location: " . $return_url);
    exit;
}

$new_is_saved = filter_input(
    INPUT_POST,
    "new_is_saved",
    FILTER_VALIDATE_INT
);

if (!in_array($new_is_saved, [0, 1], true)) {
    flash("Choose a valid saved-esports action.", "warning");
    header("Location: " . $return_url);
    exit;
}

$user_id = get_user_id();

try {
    insert(
        "UserEsports",
        [
            "user_id" => $user_id,
            "esports_id" => $esports_id,
            "is_active" => $new_is_saved,
        ],
        [
            "update_duplicate" => true,
            "columns_to_update" => [
                "is_active",
            ],
        ]
    );

    if ($new_is_saved === 1) {
        flash("Esports content saved.", "success");
    } else {
        flash(
            "Esports content removed from your saved list.",
            "success"
        );
    }

} catch (Throwable $e) {
    error_log(
        "Saved esports toggle failed: " .
        $e->getMessage()
    );

    flash(
        "The saved esports content could not be updated.",
        "danger"
    );
}

header("Location: " . $return_url);
exit;