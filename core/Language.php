<?php

// core/Language.php

class Language
{
    private static $data;

    public static function load($language_code)
    {
        $file = 'languages/' . $language_code . '.php';
        if (file_exists($file)) {
            self::$data = include($file);
        } else {
            self::$data = include('languages/en.php');
        }
    }

    public static function get($key)
    {
        return isset(self::$data[$key]) ? self::$data[$key] : $key;
    }

}