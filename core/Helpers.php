<?php

// core/Helpers.php

class Helpers
{
    public static function url_redirect(string $url, int $status_code = 302): void
    {
        if (headers_sent()) {
            http_response_code(500);
            exit();
        } else {
            header("Location: $url", true, $status_code);
            exit();
        }
    }

    public static function url_builder(string $base_url, array $params, array $exclude_keys = []): string
    {
        $filtered_params = array_filter($params, function ($value, $key) use ($exclude_keys) {
            return isset($value) && $value !== '' && !in_array($key, $exclude_keys);
        }, ARRAY_FILTER_USE_BOTH);

        $query_string = http_build_query($filtered_params);

        return $query_string ? $base_url . '?' . $query_string : $base_url;
    }


    public static function fetch_json(string $cache_path): ?array
    {
        if (!file_exists($cache_path)) {
            return null;
        }

        $json_content = file_get_contents($cache_path);
        if ($json_content === false) {
            return null;
        }

        $data = json_decode($json_content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        return $data;
    }

}