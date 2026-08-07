<?php
// File: public_html/project/index.php
require_once(__DIR__ . "/../../lib/app.php");
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Home</title>
</head>
<body>
    <?php render_nav(); ?>
    <div class="jumbotron">
        <!-- TODO replace your name -->
        <h1 class="display-4">Welcome to Jayson's project</h1>
        <!-- TODO change Date/Semester accordingly -->
        <p class="lead">Register or login in to the website.</p>
    </div>
</body>
</html>
