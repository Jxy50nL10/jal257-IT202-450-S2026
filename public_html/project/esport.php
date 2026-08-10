
<?php
// public_html/project/guide.php
require_once(__DIR__ . "/../../lib/app.php");

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) {
    flash("Missing guide id.", "danger");
    header("Location: " . project_url("esports.php"));
    exit;
}

$user_id = get_user_id(); // returns 0 when not logged in

$esport = null;

try {
    $esport = select(
        "SELECT e.id, e.api_id, e.name, e.category_name,
                e.score, e.type, e.created, e.modified,
                EXISTS (
                    SELECT 1
                    FROM UserEsports ue
                    WHERE ue.esports_id = e.id
                      AND ue.user_id = :user_id
                      AND ue.is_active = 1
                ) AS is_saved
         FROM Esports e
         WHERE e.id = :id
         LIMIT 1",
        [
            "id" => $id,
            "user_id" => $user_id
        ]
    );
} catch (Throwable $e) {
    error_log("Esports lookup failed: " . $e->getMessage());

    flash(
        "The esports content could not be loaded.",
        "danger"
    );

    header(
        "Location: " .
        project_url("esports.php")
    );

    exit;
}

if ($esport === null) {
    flash(
        "Esports content not found.",
        "warning"
    );

    header(
        "Location: " .
        project_url("esports.php")
    );

    exit;
}
$video_url = sc_nullable_url($esport["video"] ?? null);
?>
<!doctype html>
<html lang="en">
<head><?php render_head($esport["title"]); ?></head>
<?php
// In esport.php, keep the page shell and <main> element.
// Replace the existing <article class="card">...</article> block with this code.
$card_options = [
    "show_detail_view" => true,
];
render_esports_card($esport, $card_options);
?>

</main>

<?php render_flash_messages(); ?>
<?php render_scripts(); ?>
</body>
</html>