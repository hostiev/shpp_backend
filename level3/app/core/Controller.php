<?php

namespace app\core;
/**
 * An abstract controller class.
 */
abstract class Controller {

    protected $route;
    protected $model;
    protected $view;

    /**
     * Constructs controller with references to model and view specified in the route.
     * @param $route
     */
	function __construct($route) {
        $this->route = $route;
	    $modelPath = 'app\models\\' . $this->route['model'] . 'Model';
        $this->model = new $modelPath();
        $this->view = new View($this->route);
	}
}