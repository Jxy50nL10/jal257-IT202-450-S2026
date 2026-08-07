<?php
// lib/stock_api.php
// lib/app.php imports api_helper.php before this wrapper.

/**
 * Converts Alpha Vantage JSON keys and values into a cleaner project-friendly shape.
 *
 * @param array $api_record One entity from an Alpha Vantage API response.
 * @return array Record with normalized field names and numeric values.
 */
function transform_alpha_vantage_record(array $api_record): array
{
    $transformed_record = [];

    foreach ($api_record as $json_key => $value) {
        // Alpha Vantage uses names like "01. symbol" and "07. latest trading day".
        $key_parts = explode(" ", $json_key, 2);
        $field_name = $json_key;
        if (count($key_parts) === 2) {
            $field_name = $key_parts[1];
        }
        $field_name = str_replace(" ", "_", $field_name);

        if (is_string($value)) {
            $value = str_replace("%", "", $value);
        }
        if (is_numeric($value)) {
            if (strpos((string)$value, ".") !== false) {
                $value = (float)$value;
            } else {
                $value = (int)$value;
            }
        }

        $transformed_record[$field_name] = $value;
    }

    return $transformed_record;
}

/**
 * Fetches one stock quote and maps the API response to the Stocks table shape.
 *
 * @param string $symbol Stock symbol accepted by the external API.
 * @param array $errors Validation and API response errors for the page.
 * @return array|null One project-shaped row, or null when no valid quote is available.
 */
function fetch_quote(string $symbol, array &$errors): ?array
{
    $result = api_get(
        "https://alpha-vantage.p.rapidapi.com/query",
        ["function" => "GLOBAL_QUOTE", "symbol" => $symbol],
        ["key_name" => "STOCK_API_KEY", "host_name" => "STOCK_API_HOST"]
    );

    $json_key = "Global Quote";
    $decoded = decode_api_response($result, $json_key, $errors);
    if ($decoded === null) {
        return null;
    }
    $quote_json = $decoded[$json_key];

    $quote = transform_alpha_vantage_record($quote_json);
    if (empty($quote["symbol"])) {
        error_log("fetch_quote received a quote without a symbol");
        return null;
    }

    return [
        "symbol" => $quote["symbol"],
        "open" => $quote["open"] ?? 0,
        "high" => $quote["high"] ?? 0,
        "low" => $quote["low"] ?? 0,
        "price" => $quote["price"] ?? 0,
        "volume" => $quote["volume"] ?? 0,
        "latest_trading_day" => $quote["latest_trading_day"] ?? null,
        "change_percent" => $quote["change_percent"] ?? 0,
        "is_api" => 1,
    ];
}

/**
 * Searches companies and maps each API match to the Companies table shape.
 *
 * @param string $keywords Search text accepted by the external API.
 * @param array $errors Validation and API response errors for the page.
 * @return array List of project-shaped company rows. An empty list means no usable matches.
 */
function search_companies(string $keywords, array &$errors): array
{
    $result = api_get(
        "https://alpha-vantage.p.rapidapi.com/query",
        ["function" => "SYMBOL_SEARCH", "keywords" => $keywords],
        ["key_name" => "STOCK_API_KEY", "host_name" => "STOCK_API_HOST"]
    );

    $json_key = "bestMatches";
    $decoded = decode_api_response($result, $json_key, $errors);
    if ($decoded === null) {
        return [];
    }
    $matches_json = $decoded[$json_key];

    $companies = [];
    foreach ($matches_json as $match_json) {
        if (!is_array($match_json)) {
            continue;
        }

        $company = transform_alpha_vantage_record($match_json);
        $max_symbol_length = 10;
        if (empty($company["symbol"]) || strlen($company["symbol"]) > $max_symbol_length) {
            continue;
        }

        $companies[] = [
            "symbol" => $company["symbol"],
            "name" => $company["name"] ?? "",
            "type" => $company["type"] ?? "",
            "region" => $company["region"] ?? "",
            "currency" => $company["currency"] ?? "",
            "is_api" => 1,
        ];
    }

    return $companies;
}
