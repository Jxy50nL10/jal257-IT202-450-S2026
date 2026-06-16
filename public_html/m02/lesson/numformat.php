<?php
$walletBalance = 0.12345678;
$transactionAmount = 0.005;

$newBalance = $walletBalance + $transactionAmount;

echo number_format($newBalance, 8);
