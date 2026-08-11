<?php
// public_html/project/esports.php
require_once(__DIR__ . "/../../lib/app.php");

$sort_options = [
    "modified" => "Recently Updated",
    "name" => "Name",
    "category_name" => "Category",
    "score" => "Score",
    "type" => "Type",
    "api_id" => "API ID",
];

$allowed_sort_columns = array_keys($sort_options);

$list_config = [
    "filters" => esports_filter_rules(),
    // Each submitted sort key matches its trusted SQL column on these pages.
    "sort_columns" => $allowed_sort_columns,
];

$list_state = build_list_query_state($_GET, $list_config);

$filters = $list_state["filters"];
$sort = $list_state["sort"];
$direction = $list_state["direction"];
$order_by = $list_state["order_by"];
$limit = $list_state["limit"];

$filter_query = build_esports_filter_query($filters);

$where = "";

if (!empty($filter_query["sql"])) {
    $where = "WHERE " . $filter_query["sql"];
}

$params = $filter_query["params"];

$esports_content = [];

// In esports.php and admin/list_esports.php, replace the existing $esports query
// block with this code. The prior block created $where, $params, and $order_by.

$matching_count = 0;
$esports = [];

try {
    // The count uses the same filters but no ORDER BY or LIMIT.
    $count_row = select(
        "SELECT COUNT(*) AS total
         FROM esports
         $where
         LIMIT 1",
        $params
    );

    $matching_count = (int) ($count_row["total"] ?? 0);

    $esports = selectAll(
        "SELECT id, api_id, name, category_name, score, type,
                IF(api_id IS NULL, 'Manual', 'API') AS source
         FROM Esports
         $where
         ORDER BY $order_by, id ASC
         LIMIT $limit",
        $params
    );
} catch (Throwable $e) {
    error_log("Esports list failed: " . $e->getMessage());
    flash("Esports content could not be loaded.", "danger");
}
$user_id = get_user_id(); // returns 0 when not logged in

$list_params = array_merge(
    $params,
    [
        "user_id" => $user_id,
    ]
);

$esports = selectAll(
    "SELECT e.id,
            e.api_id,
            e.name,
            e.category_name,
            e.score,
            e.type,
            IF(e.api_id IS NULL, 'Manual', 'API') AS source,
            EXISTS (
                SELECT 1
                FROM UserEsports ue
                WHERE ue.esports_id = e.id
                  AND ue.user_id = :user_id
                  AND ue.is_active = 1
            ) AS is_saved
     FROM Esports e
     $where
     ORDER BY $order_by, e.id ASC
     LIMIT $limit",
    $list_params
);
$shown_count = count($esports);


// Keep each page's existing column/action setup and HTML page shell below.



if (has_role("Admin")) {
    $esports_actions[] = [
        "label" => "Edit",
        "url" => "admin/edit_esports.php",
        "variant" => "warning"
    ];

    $esports_actions[] = [
        "label" => "Delete",
        "url" => "admin/delete_esports_content.php",
        "method" => "POST",
        "include_parameter_in_url" => true,
        "query_parameters" => ["return_to" => "esports.php"],
        "variant" => "danger",
    ];
}
?>

<!doctype html>
<html lang="en">

<head>
    <?php render_head("Esports Content"); ?>
</head>

<body>
    <?php render_nav(); ?>

    <main class="container py-4">
        <h1>Esports Content</h1>

        <?php render_esports_search(
            $filters,
            $limit,
            $sort,
            $direction,
            $sort_options
        ); ?>

        <?php
        // In guides.php, remove the now-unused $guide_columns and $guide_actions arrays.
        // Keep the updated render_guide_search() call above. Replace only the old
        // render_table() call inside <main> with this block.
        render_result_summary($shown_count, $matching_count);
        render_grid($esports);

        // Keep </main>, flash messages, scripts, and the page-shell closing tags below.
        ?>
    </main>

    <?php render_flash_messages(); ?>
    <?php render_scripts(); ?>
</body>

</html>