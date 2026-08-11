<?php
// lib/esports_api.php

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

function map_esports_content(array $result, array &$errors): array
{
    return [
        "api_id" => $result["entity"]["id"] ?? null,
        "name" => sc_nullable_string($result["entity"]["name"] ?? null),
        "category_name" => sc_nullable_string(
            $result["entity"]["category"]["name"] ?? null
        ),
        "score" => $result["score"] ?? null,
        "type" => sc_nullable_string($result["type"] ?? null),
    ];
}

function fetch_esports_content(string $q, array &$errors): array
{
    $q = strtolower(trim($q));

    $result = api_get(
        "https://esportapi1.p.rapidapi.com/api/search/all",
        ["q" => $q],
        [
            "key_name" => "ESPORTS_API_KEY",
            "host_name" => "ESPORTS_API_HOST",
        ]
    );

    $decoded = decode_api_response(
        $result,
        "results",
        $errors
    );

    if ($decoded === null) {
        return [];
    }

    $results = [];

    foreach ($decoded["results"] as $result) {
        if (!is_array($result)) {
            $errors[] = "The API returned an invalid esports item.";
            continue;
        }

        $mapped = map_esports_content($result, $errors);

        $results[] = $mapped;
    }

    return $results;
}