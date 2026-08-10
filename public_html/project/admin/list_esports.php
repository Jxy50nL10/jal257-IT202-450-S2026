<?php
// public_html/project/admin/list_esports_content.php

require_once(__DIR__ . "/../../../lib/app.php");
require_role("Admin");

$filters = [
    "name" => "",              // CHANGED
    "category_name" => "",     // CHANGED
    "score" => "",             // CHANGED
    "type" => "",              // CHANGED
];

// Read only the expected filters from the query string.
foreach ($filters as $filter_name => $value) {
    if (isset($_GET[$filter_name]) && is_string($_GET[$filter_name])) {
        $filters[$filter_name] = trim($_GET[$filter_name]);
    }
}

$limit = 10;

if (isset($_GET["limit"]) && is_string($_GET["limit"])) {
    $requested_limit = filter_var(
        $_GET["limit"],
        FILTER_VALIDATE_INT,
        ["options" => ["min_range" => 1, "max_range" => 100]]
    );

    if ($requested_limit !== false) {
        $limit = $requested_limit;
    }
}

$where_parts = [];
$params = [];

if (!empty($filters["name"])) {                         // CHANGED
    $where_parts[] = "name LIKE :name";
    $params["name"] = "%" . $filters["name"] . "%";
}

if (!empty($filters["category_name"])) {                // CHANGED
    $where_parts[] = "category_name LIKE :category_name";
    $params["category_name"] = "%" . $filters["category_name"] . "%";
}

if ($filters["score"] !== "") {                         // CHANGED
    if (is_numeric($filters["score"])) {
        $where_parts[] = "score = :score";
        $params["score"] = $filters["score"];
    }
}

if (!empty($filters["type"])) {                         // CHANGED
    $where_parts[] = "type LIKE :type";
    $params["type"] = "%" . $filters["type"] . "%";
}

$where = "";

if ($where_parts) {
    $where = "WHERE " . implode(" AND ", $where_parts);
}

$esports_content = [];                                  // CHANGED

try {
    $esports_content = selectAll(                       // CHANGED
        "SELECT id, name, category_name, score, type,
                IF(api_id IS NULL, 'Manual', 'API') AS source
         FROM Esports
         $where
         ORDER BY modified DESC
         LIMIT $limit",
        $params
    );
} catch (Throwable $e) {
    error_log("Admin esports content list failed: " . $e->getMessage());
    flash("Esports content could not be loaded.", "danger");
}

$esports_columns = [                                    // CHANGED
    "name" => "Name",
    "category_name" => "Category",
    "score" => "Score",
    "type" => "Type",
    "source" => "Source",
];

$esports_actions = [                                    // CHANGED
    ["label" => "View", "url" => "esports.php", "variant" => "primary"],
    ["label" => "Edit", "url" => "admin/edit_esport.php", "variant" => "warning"],
    [
        "label" => "Delete",
        "url" => "admin/delete_esports.php",
        "method" => "POST",
        "include_parameter_in_url" => true,
        "query_parameters" => ["return_to" => "admin/list_esports.php"],
        "variant" => "danger",
    ],
];
?>

<!doctype html>
<html lang="en">

<head>
    <?php render_head("Manage Esports Content"); ?>
</head>

<body>
    <?php render_nav(); ?>

    <main class="container py-4">
        <h1>Manage Esports Content</h1>

        <?php //render_esports_search($filters, $limit); ?>

        <?php
        render_table(
            $esports_content,
            $esports_columns,
            $esports_actions,
            "No esports content has been saved yet."
        );
        ?>
    </main>

    <?php render_flash_messages(); ?>
    <?php render_scripts(); ?>
</body>

</html>