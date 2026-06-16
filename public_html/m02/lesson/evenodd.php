<?php
$numbers = [1, 2, 3, 4];

foreach ($numbers as $number) {
    // % gives the remainder after division.
    if ($number % 2 === 0) {
        echo "$number is even<br>";
    } else {
        echo "$number is odd<br>";
    }
}
?>
