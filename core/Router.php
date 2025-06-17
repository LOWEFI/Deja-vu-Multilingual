<?php

// core/Router.php

class Router
{
    public function run()
    {
        $controller_name = 'Home';
        $action_name = 'index';
        $params = [];

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = trim($uri, '/');
        $segments = explode('/', $uri);

        if (!empty($segments[0]) && ctype_alnum($segments[0])) {
            $controller_name = ucfirst($segments[0]);
        }

        if (!empty($segments[1])) {
            if (ctype_alnum($segments[1])) {

                $action_candidate = $segments[1];
                $controller_file = 'controllers/' . $controller_name . 'Controller.php';

                if (file_exists($controller_file)) {
                    require_once $controller_file;
                    $controller_class = $controller_name . 'Controller';
                    if (class_exists($controller_class)) {
                        $controller = new $controller_class();
                        if (method_exists($controller, $action_candidate)) {
                            $action_name = $action_candidate;
                            $params = array_slice($segments, 2);
                        } else {
                            $action_name = 'index';
                            $params = array_slice($segments, 1);
                        }
                    }
                }
            }
        }

        $controller_file = 'controllers/' . $controller_name . 'Controller.php';

        if (file_exists($controller_file)) {
            require_once $controller_file;
            $controller_class = $controller_name . 'Controller';
            if (class_exists($controller_class)) {
                $controller = new $controller_class();

                if (method_exists($controller, $action_name)) {
                    call_user_func_array([$controller, $action_name], $params);
                } else {
                    Helpers::url_redirect('/');
                }
            } else {
                Helpers::url_redirect('/');
            }
        } else {
            Helpers::url_redirect('/');
        }
    }
}
