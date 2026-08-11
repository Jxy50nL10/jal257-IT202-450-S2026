<?php
// public_html/project/admin/create_esports.php

require_once(__DIR__ . "/../../../lib/app.php");
require_once(__DIR__ . "/../../../lib/esports_api.php");

require_role("Admin");

$errors = [];

$q = "";
$api_id = "";
$name = "";
$category_name = "";
$score = "";
$type = "";
$action = "";

if (isset($_POST["action"]) && is_string($_POST["action"])) {
    $action = $_POST["action"];
}

$active_form = "api";

if ($action === "create_esports_content") {
    $active_form = "manual";
}

if (isset($_POST["q"]) && is_string($_POST["q"])) {
    $q = trim($_POST["q"]);
}


/*
 * API IMPORT
 */
if ($action === "import_esports_content") {
    try {
        $esports_content = fetch_esports_content($q, $errors);

        if (empty($errors) && !empty($esports_content)) {
            $result = insert(
                "esports",
                $esports_content,
                [
                    "update_duplicate" => true,
                    "columns_to_update" => [
                        "name",
                        "category_name",
                        "score",
                        "type",
                    ],
                ]
            );

            flash(
                sprintf(
                    "%s API esports row(s) saved.",
                    $result["rowCount"]
                ),
                "success"
            );

            header(
                "Location: " .
                project_url("admin/create_esports.php")
            );
            exit;
        }
    } catch (Throwable $e) {
        error_log(
            "Esports import failed: " .
            $e->getMessage()
        );

        $errors[] =
            "The esports content could not be imported.";
    }
}


/*
 * MANUAL CREATE
 */
if ($action === "create_esports_content") {

    if (
        isset($_POST["api_id"]) &&
        is_string($_POST["api_id"])
    ) {
        $api_id = trim($_POST["api_id"]);
    }

    if (
        isset($_POST["name"]) &&
        is_string($_POST["name"])
    ) {
        $name = trim($_POST["name"]);
    }

    if (
        isset($_POST["category_name"]) &&
        is_string($_POST["category_name"])
    ) {
        $category_name =
            trim($_POST["category_name"]);
    }

    if (
        isset($_POST["score"]) &&
        is_string($_POST["score"])
    ) {
        $score = trim($_POST["score"]);
    }

    if (
        isset($_POST["type"]) &&
        is_string($_POST["type"])
    ) {
        $type = trim($_POST["type"]);
    }


    /*
     * VALIDATION
     */
    if ($name === "" || strlen($name) > 150) {
        $errors[] =
            "Name is required and must be 150 characters or less.";
    }

    if (
        $category_name !== "" &&
        strlen($category_name) > 100
    ) {
        $errors[] =
            "Category name must be 100 characters or less.";
    }

    if (
        $api_id !== "" &&
        !ctype_digit($api_id)
    ) {
        $errors[] =
            "API ID must be a valid number.";
    }

    if (
        $score !== "" &&
        !is_numeric($score)
    ) {
        $errors[] =
            "Score must be a valid number.";
    }

    if (
        $type !== "" &&
        strlen($type) > 50
    ) {
        $errors[] =
            "Type must be 50 characters or less.";
    }


    /*
     * INSERT MANUAL RECORD
     */
    if (empty($errors)) {
        try {
            $manual_esports = [
                [
                    "api_id" =>
                        $api_id === ""
                            ? null
                            : (int)$api_id,

                    "name" => $name,

                    "category_name" =>
                        $category_name === ""
                            ? null
                            : $category_name,

                    "score" =>
                        $score === ""
                            ? null
                            : $score,

                    "type" =>
                        $type === ""
                            ? null
                            : $type,
                ],
            ];

            $result = insert(
                "esports",
                $manual_esports
            );

            flash(
                "Manual esports content created.",
                "success"
            );

            header(
                "Location: " .
                project_url("admin/create_esports.php")
            );
            exit;
        } catch (Throwable $e) {
            error_log(
                "Manual esports creation failed: " .
                $e->getMessage()
            );

            $errors[] =
                "The esports content could not be created.";
        }
    }
}

flash_errors($errors);
?>

<!doctype html>
<html lang="en">

<head>
    <?php render_head("Create Esports Content"); ?>
</head>

<body>

<?php render_nav(); ?>

<main class="container py-4">

    <h1>Create Esports Content</h1>

    <div class="mb-3">
        <button
            type="button"
            class="btn btn-primary"
            data-guide-form="api">
            API Import
        </button>

        <button
            type="button"
            class="btn btn-outline-primary"
            data-guide-form="manual">
            Manual
        </button>
    </div>

    <div class="row g-4">

        <!-- API IMPORT FORM -->
        <section
            id="guideApiForm"
            class="col-12"
            <?php if ($active_form !== "api"): ?>
                hidden
            <?php endif; ?>
        >

            <h2>Import API Esports Content</h2>

            <form method="post">

                <?php

                render_input([
                    "label" => "Search",
                    "name" => "q",
                    "value" => $q,
                ]);

                render_button([
                    "text" =>
                        "Import API Esports Content",

                    "variant" => "success",

                    "attributes" => [
                        "name" => "action",
                        "value" =>
                            "import_esports_content",
                    ],
                ]);

                ?>

            </form>

        </section>


        <!-- MANUAL CREATE FORM -->
        <section
            id="guideManualForm"
            class="col-12"
            <?php if ($active_form !== "manual"): ?>
                hidden
            <?php endif; ?>
        >

            <h2>Create Manual Esports Content</h2>

            <form method="post">

                <?php

                render_input([
                    "label" => "API ID",
                    "type" => "number",
                    "name" => "api_id",
                    "value" => $api_id,
                ]);

                render_input([
                    "label" => "Name",
                    "name" => "name",
                    "value" => $name,

                    "attributes" => [
                        "required" => true,
                        "maxlength" => 150,
                    ],
                ]);

                render_input([
                    "label" => "Category Name",
                    "name" => "category_name",
                    "value" => $category_name,

                    "attributes" => [
                        "maxlength" => 100,
                    ],
                ]);

                render_input([
                    "label" => "Score",
                    "type" => "number",
                    "name" => "score",
                    "value" => $score,

                    "attributes" => [
                        "step" => "0.01",
                    ],
                ]);

                render_input([
                    "label" => "Type",
                    "name" => "type",
                    "value" => $type,

                    "attributes" => [
                        "maxlength" => 50,
                    ],
                ]);

                render_button([
                    "text" =>
                        "Create Manual Esports Content",

                    "attributes" => [
                        "name" => "action",
                        "value" =>
                            "create_esports_content",
                    ],
                ]);

                ?>

            </form>

        </section>

    </div>

</main>


<script>
const apiGuideForm =
    document.querySelector("#guideApiForm");

const manualGuideForm =
    document.querySelector("#guideManualForm");

const guideFormButtons =
    document.querySelectorAll("[data-guide-form]");


function showGuideForm(formName) {

    const showApiForm =
        formName === "api";

    apiGuideForm.hidden =
        !showApiForm;

    manualGuideForm.hidden =
        showApiForm;


    guideFormButtons.forEach((button) => {

        const isSelected =
            button.dataset.guideForm === formName;

        button.classList.toggle(
            "btn-primary",
            isSelected
        );

        button.classList.toggle(
            "btn-outline-primary",
            !isSelected
        );

        button.setAttribute(
            "aria-pressed",
            String(isSelected)
        );

    });
}


guideFormButtons.forEach((button) => {

    button.addEventListener(
        "click",
        () => {
            showGuideForm(
                button.dataset.guideForm
            );
        }
    );

});


showGuideForm(
    "<?php echo htmlspecialchars($active_form); ?>"
);
</script>

</body>
</html>