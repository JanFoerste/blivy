<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Manager\Http\Router;

use Manager\Exception\ClassNotFoundException;
use Manager\Exception\Exception;
use Manager\Exception\HttpMethodNotAllowedException;
use Manager\Exception\HttpNotFoundException;
use Manager\Exception\MethodNotFoundException;
use Manager\Request\Guard;

class Router
{
    /**
     * @var string
     */
    protected $uri;

    /**
     * ### Sets the formatted request uri
     *
     * Router constructor.
     */
    public function __construct()
    {
        $this->uri = $this->formatURI($_SERVER['REQUEST_URI']);
    }

    /**
     * ### Formats the uri to remove GET data
     *
     * @param string $uri
     * @return string
     */
    public static function formatURI($uri)
    {
        if (strpos($uri, '?')) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }
        return $uri;
    }

    /**
     * ### Searches for the requested route in the route config
     *
     * @param string $uri
     * @return mixed
     * @throws Exception
     * @throws HttpMethodNotAllowedException
     * @throws HttpNotFoundException
     */
    public function findRoute($uri)
    {
        $parts = explode('/', $uri);
        pr($parts);
        die();

        $routes = file_get_contents(httpdir() . 'routes.json');
        $routes = json_decode($routes);

        $get = array_key_exists($uri, $routes->GET);
        $post = array_key_exists($uri, $routes->POST);

        if ($get && $post) {
            $allowed = 3;
        } elseif ($get) {
            $allowed = 1;
        } elseif ($post) {
            $allowed = 2;
        } else {
            throw new HttpNotFoundException;
        }

        return $this->checkRouteMethod($allowed, $routes, $uri);
    }

    /**
     * ### Checks and verifies the used and allowed route methods
     *
     * @param int $allowed
     * @param mixed $routes
     * @param string $uri
     * @return string
     * @throws Exception
     * @throws HttpMethodNotAllowedException
     */
    private function checkRouteMethod($allowed, $routes, $uri)
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'GET') {
            if ($allowed === 3 || $allowed === 1) {
                return $routes->GET->$uri->controller;
            } else {
                throw new HttpMethodNotAllowedException;
            }
        } elseif ($method === 'POST') {
            if ($allowed === 3 || $allowed === 2) {

                if (!Guard::verifyCSRF()) {
                    throw new Exception('CSRF-Token missing or invalid');
                }

                return $routes->POST->$uri->controller;
            } else {
                throw new HttpMethodNotAllowedException;
            }
        } else {
            throw new HttpMethodNotAllowedException;
        }
    }

    /**
     * ### Does the actual routing and runs the requested method
     *
     * @return mixed
     * @throws ClassNotFoundException
     * @throws Exception
     * @throws HttpMethodNotAllowedException
     * @throws HttpNotFoundException
     * @throws MethodNotFoundException
     */
    public function route()
    {
        $route = $this->findRoute($this->uri);
        $data = $this->explodeRoute($route);
        $class = 'Manager\Http\Controllers\\' . $data[0];

        if (!class_exists($class)) {
            throw new ClassNotFoundException($class);
        }
        $init = new $class();

        if (!method_exists($init, $data[1])) {
            throw new MethodNotFoundException($class . ':' . $data[1]);
        }

        return $init->$data[1]();
    }

    /**
     * ### Explodes the route to it's parts
     *
     * @param string $str
     * @return array
     */
    private function explodeRoute($str)
    {
        $explode = explode(':', $str);
        return $explode;
    }
}