<?php
// partials/esports_guide.php
// Usage: call render_esports_search($filters, $limit) from the esports list page.
?>

<form class="row g-3 align-items-end mb-4" method="get">

    <div class="col-md-6 col-xl-3">
        <?php
        render_input([
            "label" => "Name",
            "name" => "name",
            "value" => $filters["name"] ?? "",
            "attributes" => [
                "maxlength" => 150,
            ],
        ]);
        ?>
    </div>

    <div class="col-md-6 col-xl-3">
        <?php
        render_input([
            "label" => "Category",
            "name" => "category_name",
            "value" => $filters["category_name"] ?? "",
            "attributes" => [
                "maxlength" => 100,
            ],
        ]);
        ?>
    </div>

    <div class="col-md-4 col-xl-2">
        <?php
        render_input([
            "label" => "Type",
            "name" => "type",
            "value" => $filters["type"] ?? "",
            "attributes" => [
                "maxlength" => 50,
            ],
        ]);
        ?>
    </div>

        
    </div>
<div class="col-md-4 col-xl-3">
    <?php
    render_input([
        "label" => "Sort By",
        "name" => "sort",
        "type" => "select",
        "value" => $sort ?? "",
        "options" => $sort_options ?? [],
    ]);
    ?>
</div>

<div class="col-md-4 col-xl-2">
    <?php
    render_input([
        "label" => "Direction",
        "name" => "direction",
        "type" => "select",
        "value" => $direction ??" ",
        "options" => [
            "asc" => "Ascending",
            "desc" => "Descending",
        ],
    ]);
    ?>
</div>
    


    <div class="col-md-3 col-xl-2">
        <?php
        render_input([
            "label" => "Limit",
            "name" => "limit",
            "type" => "number",
            "value" => $limit ?? "",
            "attributes" => [
                "min" => 1,
                "max" => 100,
                "required" => true,
            ],
        ]);
        ?>
    </div>

    <div class="col-md-3 col-xl-1">
        <?php
        render_button([
            "text" => "Search",
            "attributes" => [
                "class" => "w-100",
            ],
        ]);
        ?>
    </div>

    <div class="col-md-3 col-xl-1">
        <a href="?" class="btn btn-secondary w-100">
            Reset
        </a>
    </div>

</form>