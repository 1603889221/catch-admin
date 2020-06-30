<?php
namespace catcher\generate\factory;

use catcher\generate\template\Content;

class Route extends Factory
{
    use Content;

    protected $controllerName;

    protected $controller;

    protected $restful;

    protected $methods = [];

    public function done($params = [])
    {
        $route = [];

        if ($this->restful) {
            $route[] = sprintf("\$router->resource('%s', '\%s')->middleware('auth')", $this->controllerName, $this->controller);
        }

        if (!empty($this->methods)) {
            foreach ($this->methods as $method) {
                $route[] = sprintf("\$router->%s('%s/%s', '\%s@%s')->middleware('auth')", $method[1], $this->controllerName, $method[0], $this->controller, $method[0] );
            }
        }

        $router = $this->getModulePath($this->controller) . DIRECTORY_SEPARATOR . 'route.php';

        $comment = PHP_EOL . '//' . $this->controllerName . '路由' . PHP_EOL;

        if (file_exists($router)) {
            return file_put_contents($router, PHP_EOL . $comment . implode(';'. PHP_EOL , $route) . ';', FILE_APPEND);
        }

        return file_put_contents($router, $this->header() . $comment. implode(';'. PHP_EOL , $route) . ';');
    }

    /**
     * set class
     *
     * @time 2020年04月28日
     * @param $class
     * @return $this
     */
    public function controller($class)
    {
        $this->controller = $class;

        $class = explode('\\', $class);

        $this->controllerName = lcfirst(array_pop($class));

        return $this;
    }

    /**
     * set restful
     *
     * @time 2020年04月28日
     * @param $restful
     * @return $this
     */
    public function restful($restful)
    {
        $this->restful = $restful;

        return $this;
    }

    /**
     * set methods
     *
     * @time 2020年04月28日
     * @param $methods
     * @return $this
     */
    public function methods($methods)
    {
        $this->methods = $methods;

        return $this;
    }
}