<?php

class Router
{
    private static $instance = null;
    private $routes = [];
    private $prefix = '';
    private $basePath = '';

    // Private constructor to prevent direct instantiation
    private function __construct($basePath)
    {
        $this->basePath = $basePath;
    }

    // Prevent cloning of the instance
    private function __clone()
    {
    }

    // Static method to get the single instance of the class
    public static function getInstance($basePath)
    {
        if (self::$instance === null) {
            self::$instance = new self($basePath);
        }
        return self::$instance;
    }

    public function get($path, $callback)
    {
        $this->addRoute('GET', $path, $callback);
    }

    public function post($path, $callback)
    {
        $this->addRoute('POST', $path, $callback);
    }

    public function put($path, $callback)
    {
        $this->addRoute('PUT', $path, $callback);
    }

    public function delete($path, $callback)
    {
        $this->addRoute('DELETE', $path, $callback);
    }

    public function patch($path, $callback)
    {
        $this->addRoute('PATCH', $path, $callback);
    }

    public function options($path, $callback)
    {
        $this->addRoute('OPTIONS', $path, $callback);
    }

    public function head($path, $callback)
    {
        $this->addRoute('HEAD', $path, $callback);
    }

    public function addRoute($method, $path, $callback)
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback,
        ];
    }

    public function setPrefix($prefix) {
        $this->prefix = rtrim($prefix, '/');
    }

    public function run()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
    
        // Remove the base path and prefix
        if (strpos($requestUri, $this->basePath) === 0) {
            $requestUri = substr(substr($requestUri, strlen($this->basePath)), strlen($this->prefix));
            $requestUri = '/' . ltrim($requestUri, '/');
        }
    
        // Match the request URI and method with the defined routes
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod) {
                // Check for exact match or dynamic route
                $pattern = preg_replace('/:\w+/', '(\w+)', $route['path']); // Replace :param with regex
                $pattern = str_replace('/', '\/', $pattern); // Escape slashes for regex
                $pattern = '/^' . $pattern . '$/'; // Add start and end delimiters

                if (preg_match($pattern, $requestUri, $matches)) {
                    array_shift($matches); // Remove the full match from the matches array

                    if (is_callable($route['callback'])) {
                        // Pass dynamic parameters to the callback
                        call_user_func_array($route['callback'], $matches);
                    } elseif (is_array($route['callback']) && count($route['callback']) === 2) {
                        // Handle static methods as ['ClassName', 'methodName']
                        [$class, $method] = $route['callback'];
                        if (class_exists($class) && method_exists($class, $method)) {
                            call_user_func_array([$class, $method], $matches);
                        } else {
                            echo "Callback for route {$route['path']} is not callable.";
                        }
                    } else {
                        echo "Callback for route {$route['path']} is not callable.";
                    }
                    return;
                }
            }
        }

        // If no route matches, return a 404 response
        http_response_code(404);
        echo json_encode(['error' => 'Route not found : '. $requestUri]);
    }
}
?>