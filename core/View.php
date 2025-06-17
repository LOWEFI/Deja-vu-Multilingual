<?php

// core/View.php

class View
{
    public static function render($view, $data = [], $is_partial = false)
    {
        $view = preg_replace('/[^a-zA-Z0-9_\/]/', '', $view);

        if (strpos($view, '..') !== false) {
            Helpers::url_redirect('/');
        }

        $view_file = 'views/' . $view . '.php';

        if (!file_exists($view_file)) {
            Helpers::url_redirect('/');
        }

        if (!$is_partial) { require 'views/partials/Header.php'; }

        require $view_file;

        if (!$is_partial) { require 'views/partials/Footer.php'; }

    }
}