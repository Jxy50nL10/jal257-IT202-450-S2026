<?php
// File: public_html/project/index.php
require_once(__DIR__ . "/../../lib/app.php");
?>
<!doctype html>
<html lang="en">
<head>
    <?php render_head("Home"); ?>
</head>
<body>
    <?php render_nav(); ?>
    <main class="container py-4">
    <div class="jumbotron">
        <!-- TODO replace your name -->
        <h1 class="display-4">Welcome to Jayson's project</h1>
        <!-- TODO change Date/Semester accordingly -->
        <p class="lead">Register or login in to the website.</p>
    </div>
</main>
</body>
</html>
