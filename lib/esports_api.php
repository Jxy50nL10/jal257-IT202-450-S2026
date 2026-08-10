<?php
// lib/starcraft_api.php

/**
 * Returns a trimmed string or null for a missing, empty, or non-string value.
 *
 * @param mixed $value API value to normalize.
 * @return string|null Clean string or null.
 */
function sc_nullable_string(mixed $value): ?string
{
    if (!is_string($value)) {
        return null;
    }

    $value = trim($value);
    if ($value === "") {
        return null;
    }

    return $value;
}

/**
 * Returns a safe HTTP(S) URL or null.
 *
 * @param mixed $value API value to validate.
 * @return string|null Valid URL or null.
 */
function sc_nullable_url(mixed $value): ?string
{
    $url = sc_nullable_string($value);
    if ($url === null || filter_var($url, FILTER_VALIDATE_URL) === false) {
        return null;
    }

    $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
    if (!in_array($scheme, ["http", "https"], true)) {
        return null;
    }

    return $url;
}

/**
 * Maps one getGameGuides() item to the stable columns used by the project.
 *
 * @param array $guide One provider guide item.
 * @param array $errors Shared validation-style error list.
 * @return array|null Mapped guide or null when required data is invalid.
 */
function map_sc_guide(array $guide, array &$errors): ?array
{
    $required_json_keys = ["id", "game", "title"];
    foreach ($required_json_keys as $json_key) {
        if (sc_nullable_string($guide[$json_key] ?? null) === null) {
            $errors[] = "One guide was missing required API data.";
            error_log("StarCraft guide is missing JSON key: $json_key");
            return null;
        }
    }

    $source_url = sc_nullable_url($guide["source"]["url"] ?? null);
    if ($source_url === null) {
        $source_url = sc_nullable_url($guide["source"]["canonicalUrl"] ?? null);
    }

    return [
        "api_id" => sc_nullable_string($guide["id"]),
        "excerpt" => sc_nullable_string($guide["excerpt"]["text"] ?? null),
        "game" => sc_nullable_string($guide["game"]),
        "primary_category" => sc_nullable_string($guide["primaryCategory"] ?? null),
        "slug" => sc_nullable_string($guide["slug"] ?? null),
        "source_author" => sc_nullable_string($guide["source"]["author"] ?? null),
        "source_url" => $source_url,
        "status" => sc_nullable_string($guide["status"] ?? null),
        "summary" => sc_nullable_string($guide["summary"]["text"] ?? null),
        "title" => sc_nullable_string($guide["title"]),
        "video" => sc_nullable_string($guide["video"] ?? null),
        "opponent_race" => sc_nullable_string($guide["opponentRace"] ?? null),
        "player_race" => sc_nullable_string($guide["playerRace"] ?? null),
        "matchup" => sc_nullable_string($guide["matchup"] ?? null),
    ];
}

/**
 * Calls RapidAPI's getGameGuides() endpoint for one supported StarCraft game.
 *
 * @param string $game Requested game slug: sc1 or sc2.
 * @param array $errors Shared validation-style error list.
 * @return array Project-shaped guide rows.
 */
function map_esports_content(array $result, array &$errors): array
{
    return [
        "api_id" => $result["entity"]["id"] ?? null,
        "name" => sc_nullable_string($result["entity"]["name"] ?? null),
        "category_name" => sc_nullable_string($result["entity"]["category"]["name"] ?? null),
        "score" => $result["score"] ?? null,
        "type" => sc_nullable_string($result["type"] ?? null),
    ];
}
function fetch_esports_content(string $q, array &$errors): array
{
    $q = strtolower(trim($q));
    $result = api_get(
        "https://esportapi1.p.rapidapi.com/api/search/all",
        ["q"=>$q],
        ["key_name" => "ESPORTS_API_KEY", "host_name" => "ESPORTS_API_HOST"]
    );
    $decoded = decode_api_response($result, "results", $errors);
    if ($decoded === null) {
        return [];
    }

    $results = [];
    foreach ($decoded["results"] as $result) {
        if (!is_array($result)) {
            $errors[] = "The API returned an invalid guide item.";
            continue;
        }

        $mapped = map_esports_content($result, $errors);
        if ($mapped !== null) {
            $results[] = $mapped;
        }
    }

    return $results;
}
