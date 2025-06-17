<?php

// core/Controller.php

class Controller
{
    public function model($model)
    {
        $model = preg_replace('/[^a-zA-Z0-9_]/', '', $model);
        $model_file = 'models/' . $model . '.php';
        if (!file_exists($model_file)) {
            Helpers::url_redirect('/');
        }
        require_once $model_file;
        return new $model();
    }

    public function view($view, $data = [], $is_partial = false)
    {
        View::render($view, $data, $is_partial);
    }
}