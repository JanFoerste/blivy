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
use Manager\Exception\RoutingException;
use Manager\Request\Guard;

class Router
{
    /**
     * @var string
     */
    protected $uri;

    /**
     * @var array
     */
    protected $routes;

    /**
     * @var array
     */
    protected $route;

    /**
     * ### Sets the formatted request uri
     *
     * Router constructor.
     */
    public function __construct()
    {
        // ### @TODO: Set input parameter instead of /
        $request = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
        $_SESSION['REQUEST_URI'] = $request;

        $this->uri = $this->formatURI($request);
        $this->routes = json_decode(file_get_contents(httpdir() . 'routes.json'), true);
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
     * @return mixed
     * @throws Exception
     * @throws HttpMethodNotAllowedException
     * @throws HttpNotFoundException
     */
    private function findRoute()
    {
        $request_parts = explode('/', $this->uri);
        array_shift($request_parts);

        foreach ($this->routes as $route => $data) {
            $parts = explode('/', $route);
            array_shift($parts);

            if (!isset($data['params'])) {

                // ### No route parameters configured

                if ($this->checkWithoutParams()) {

                    // ### Found the route :)

                    $this->routes[$this->uri]['uri'] = $this->uri;
                    $this->routes[$this->uri]['pattern'] = $route;
                    $this->route = $this->routes[$this->uri];
                    return $this->route;
                }
            } else {

                // ### Route parameters are required

                $parts = $this->generateParamRules($data, $parts);
                if ($this->checkArray($request_parts, $parts)) {

                    // ### Found the route :)

                    $data['uri'] = $this->uri;
                    $data['pattern'] = $route;
                    $this->route = $data;
                    return $this->route;
                }
            }
        }

        // ### Couldn't find requested route
        throw new HttpNotFoundException;
    }

    /**
     * ### Checks if requested route (without parameters) exists
     *
     * @return bool
     */
    private function checkWithoutParams()
    {
        return array_key_exists($this->uri, $this->routes);
    }

    /**
     * ### Generates the rule set for parameters
     *
     * @param array $data
     * @param array $parts
     * @return array
     * @throws RoutingException
     */
    private function generateParamRules($data, $parts)
    {
        foreach ($data['params'] as $r => $param) {
            $pos = array_search($r, $parts);
            if (!$pos) throw new RoutingException('Configured parameter not in route pattern.');

            $parts[$pos] = 'type:' . $data['params'][$parts[$pos]];
        }

        return $parts;
    }

    /**
     * ### Checks if the array fits the rule set
     *
     * @param array $check
     * @param array $rules
     * @return bool
     * @throws RoutingException
     */
    private function checkArray($check, $rules)
    {
        $datatypes = [
            'integer' => 'num',
            'string' => 'str'
        ];
        $passed = 0;

        foreach ($rules as $key => $rule) {
            if (substr($rule, 0, 5) === 'type:') {
                $type = substr($rule, 5);
                if (!array_key_exists($type, $datatypes)) {
                    throw new RoutingException('Invalid datatype set in route configuration.');
                }
                if (!isset($check[$key])) continue;
                if (empty($check[$key])) continue;
                $try = $this->{$datatypes[$type]}($check[$key]);

                if ($try) $passed++;
            } else {
                if (!isset($check[$key])) continue;

                if ($rules[$key] === $check[$key]) $passed++;
            }
        }
        return $passed === count($rules);
    }

    /**
     * ### Alias function for array checker
     * ### Checks if given value is numeric
     *
     * @param $val
     * @return bool
     */
    private function num($val)
    {
        return is_numeric($val);
    }

    /**
     * ### Alias function for array checker
     * ### Checks if given value is a string
     *
     * @param $val
     * @return bool
     */
    private function str($val)
    {
        return is_string($val);
    }

    /**
     * ### Checks and verifies the used and allowed route methods
     *
     * @return void
     * @throws HttpMethodNotAllowedException
     */
    private function verifyMethod()
    {
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        if ($method !== $this->route['method']) {
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
        $this->findRoute();
        $this->verifyMethod();

        $data = $this->explodeRoute($this->route['controller']);
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

    /**
     * ### Gets the route parameter defined in the URI
     *
     * @param string $key
     * @return mixed
     * @throws RoutingException
     */
    public function getRouteParameter($key)
    {
        if (!isset($this->route)) throw new RoutingException('No route has been initialized yet.');

        $search = '(' . $key . ')';
        if (!isset($this->route['params'][$search])) {
            return null;
        }

        $parts_p = explode('/', $this->route['pattern']);
        $parts_u = explode('/', $this->route['uri']);
        array_shift($parts_p);
        array_shift($parts_u);

        $id = array_search($search, $parts_p);
        return $parts_u[$id];
    }
}