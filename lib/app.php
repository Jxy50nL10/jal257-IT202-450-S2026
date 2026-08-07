<?php
session_start();

require_once(__DIR__ . "/db.php");
// lib/app.php
// Add this immediately after require_once(__DIR__ . "/db.php");
require_once(__DIR__ . "/db_helpers.php");
require_once(__DIR__ . "/render_functions.php");
// url_helpers.php must load before helpers or partials that call project_url().
require_once(__DIR__ . "/url_helpers.php");
require_once(__DIR__ . "/validations.php");
// Keep user_helpers.php before role_helpers.php.
// has_role() depends on is_logged_in().
require_once(__DIR__ . "/user_helpers.php");
require_once(__DIR__ . "/flash_messages.php");
require_once(__DIR__ . "/duplicate_user_details.php");
// require_role() depends on flash() and project_url().
require_once(__DIR__ . "/role_helpers.php");
// lib/app.php
// Add this at the end of the existing helper-import list.
// api_helper.php loads load_api_keys.php before declaring API functions.
require_once(__DIR__ . "/api_helper.php");
// lib/app.php
// Keep all existing imports, including api_helper.php.
// Add this at the end of the helper-import list, after api_helper.php.
require_once(__DIR__ . "/stock_api.php");
// lib/app.php
// Keep api_helper.php and any earlier API-specific imports in place.
// Add the new wrapper alongside the other API wrappers.
require_once(__DIR__ . "/starcraft_api.php");
