<?php
// public_html/project/esports.php
require_once(__DIR__ . "/../../lib/app.php");

$filters = [
    "name" => "",
    "category_name" => "",
    "score" => "",
    "type" => "",
];

// Read only the expected text filters from the query string.
foreach ($filters as $name => $value) {
    if (isset($_GET[$name]) && is_string($_GET[$name])) {
        $filters[$name] = trim($_GET[$name]);
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

if (!empty($filters["name"])) {
    $where_parts[] = "name LIKE :name";
    $params["name"] = "%" . $filters["name"] . "%";
}

if (!empty($filters["category_name"])) {
    $where_parts[] = "category_name LIKE :category_name";
    $params["category_name"] = "%" . $filters["category_name"] . "%";
}

if ($filters["score"] !== "" && is_numeric($filters["score"])) {
    $where_parts[] = "score = :score";
    $params["score"] = $filters["score"];
}

if (!empty($filters["type"])) {
    $where_parts[] = "type LIKE :type";
    $params["type"] = "%" . $filters["type"] . "%";
}

$where = "";

if ($where_parts) {
    // Every populated field narrows the results.
    $where = "WHERE " . implode(" AND ", $where_parts);
}

$esports_content = [];

try {
    $esports_content = selectAll(
        "SELECT id, api_id, name, category_name, score, type,
                IF(api_id IS NULL, 'Manual', 'API') AS source
         FROM Esports
         $where
         ORDER BY modified DESC
         LIMIT $limit",
        $params
    );
} catch (Throwable $e) {
    error_log("Esports content list failed: " . $e->getMessage());
    flash("Esports content could not be loaded.", "danger");
}

// Define the visible table columns before the page renders them.
$esports_columns = [
    "name" => "Name",
    "category_name" => "Category",
    "score" => "Score",
    "type" => "Type",
    "source" => "Source",
];

// View is public. Only an Admin receives management actions.
$esports_actions = [
    [
        "label" => "View",
        "url" => "esports_content.php",
        "variant" => "primary"
    ]
];

if (has_role("Admin")) {
    $esports_actions[] = [
        "label" => "Edit",
        "url" => "admin/edit_esports_content.php",
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

        <?php //render_esports_content($filters, $limit); ?>

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