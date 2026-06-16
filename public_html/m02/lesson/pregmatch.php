<?php
$displayName = "internet applications";
$slug = preg_replace("/\s+/", "-", $displayName);

$routeText = "internet-applications";
$readableName = preg_replace("/-+/", " ", $routeText);

echo $slug . "<br>";
echo $readableName;
