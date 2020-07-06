<?php

namespace app\core;

/**
 * Routes requests to the controllers.
 */
class Router {
	
	private $routes = [];

    /**
     * Router constructor. Loads existing routes info.
     */
    function __construct() {
	    $this->routes = include '../config/routes.php';
	}

    /**
     * Routes requests to the controllers, calls actions accordingly.
     */
	public function run() {
        $route = [];
        // Comparing request with existing routes
        foreach ($this->routes as $tempRoute) {
            if (preg_match($tempRoute['uriPattern'], $_SERVER['REQUEST_URI'])) {
                $route = $tempRoute;
            }
        }
        unset($tempRoute);

        // Parsing uri
        $parsedUrl = parse_url($_SERVER['REQUEST_URI']);
        $path = explode('/', trim($parsedUrl['path'], '/'));
        $route['path'] = $path;
        // ... adding parameters
        if (array_key_exists('query', $parsedUrl)) {
            $parameters = [];
            parse_str($parsedUrl['query'], $parameters);
            $route['parameters'] = $parameters;
        }

        // Getting controller
        $controllerName = $route['controller'];
        $controllerPath = 'app\controllers\\' . $controllerName . 'Controller';
        if (class_exists($controllerPath)) {
            $controller = new $controllerPath($route);
            // Getting action
            $action = $route['defaultAction'];
            if (key_exists(1, $path)) {
                $action = $path[1];
            }
            if (method_exists($controller, $action)) {
                $controller->$action();
            } else {
                $action = $route['defaultAction'];
                if (method_exists($controller, $action)) {
                    $controller->$action();
                } else {
                    http_response_code(400);
                    echo json_encode(array('error' => 'bad request'));
                }
            }
        }
	}
}