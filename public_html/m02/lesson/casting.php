<?php
$rawCount = "5";
$priceText = "12.75";
$ready = true;
$tagsText = "php,js,sql";

$count = (int) $rawCount;
$price = (float) $priceText;
$readyText = (string) $ready;
$tags = explode(",", $tagsText);

var_dump($count);
var_dump($price);
var_dump($readyText);
var_dump($tags);
?>
<script>
const rawCount = "5";
const priceText = "12.75";
const readyNumber = 1;
const tagsText = "php,js,sql";

const count = Number(rawCount);
const price = parseFloat(priceText);
const ready = Boolean(readyNumber);
const tags = tagsText.split(",");

console.log(count, typeof count);
console.log(price, typeof price);
console.log(ready, typeof ready);
console.log(tags);
<script>