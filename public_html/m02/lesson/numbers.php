<?php
$x = 1;
var_dump($x);
echo "<br>";
$x++;      // same as $x = $x + 1
$x--;      // same as $x = $x - 1
$x += 50;  // same as $x = $x + 50
var_export($x);
echo "<br>";

echo "<br>";
$test = 0.2 + 0.2;
var_dump($test); // floating point precision errors