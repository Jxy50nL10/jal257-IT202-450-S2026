<?php
// Do not run as-is: the array keeps growing.
$a = [0];

for ($i = 0; $i < count($a); $i++) {
    array_push($a, $i);
}
?>
