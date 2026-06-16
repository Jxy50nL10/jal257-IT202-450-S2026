<?php
$role = "admin";

switch (strtolower($role)) {
    case "admin":
        echo "Can manage users";
        break;
    case "editor":
        echo "Can update content";
        break;
    default:
        echo "Can view content";
}
