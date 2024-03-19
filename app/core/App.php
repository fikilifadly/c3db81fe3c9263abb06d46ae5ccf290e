<?php
class App
{

    private $controllerFile = 'DefaultApp'; # untuk default agar ketika tidak terdapat route akan mengambil controller DefaultApp
    private $controllerMethod = 'index'; # untuk default agar ketika tidak terdapat route akan mengambil controller DefaultAppController

    private $parameters = [];

    private const DEFAULT_GET = 'GET';
    private const DEFAULT_POST = 'POST';
    private const DEFAULT_DELETE = 'DELETE';
    private const DEFAULT_UPDATE = 'UPDATE';

    private $handlers = [];

    public function  setDefaultController($controller)
    {
        $this->controllerFile = $controller;
    }

    public function setDefaultMethod($method)
    {
        $this->controllerMethod = $method;
    }

    public function get($uri, $cb)
    {
        $this->setHandler(self::DEFAULT_GET, $uri, $cb);
    }
    public function post($uri, $cb)
    {
        $this->setHandler(self::DEFAULT_GET, $uri, $cb);
    }
    public function update($uri, $cb)
    {
        $this->setHandler(self::DEFAULT_GET, $uri, $cb);
    }
    public function delete($uri, $cb)
    {
        $this->setHandler(self::DEFAULT_GET, $uri, $cb);
    }

    public function setHandler(string $method, string $path, $handler)
    {
        $this->handlers[$method . $path] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function run()
    {
        $executed = 0;


        $url = $this->getUrl();
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // check apakah ada route yg sesuai dengan url dan method
        foreach ($this->handlers as $handler) {
            $path = explode('/', ltrim($handler['path'], '/'));
            $kurl = (isset($url[0]) ? $url[0] : '') . (isset($url[1]) ? $url[1] : '');
            $kpath = (isset($path[0]) ? $path[0] : '') . (isset($path[1]) ? $path[1] : '');

            // validasi jika path dan url sama dan url tidak kosong 
            if ($kurl && $kpath == $kurl) {
                if (isset($handler['handler'][0]) && file_exists(__DIR__ . '/../controllers/' . $handler['handler'][0] . '.php')) {
                    $this->controllerFile = $handler['handler'][0];
                    unset($url[0]);
                }
                require_once __DIR__ . '/../controllers/' . $this->controllerFile . '.php';
                $this->controllerFile = new $this->controllerFile;
                $executed = 1;
                if (isset($handler['handler'][1]) && isset($url[1]) && $handler['handler'][1] == $url[1]) {
                    if (method_exists($this->controllerFile, $url[1])) {

                        $this->controllerMethod = $handler['handler'][1];
                        unset($url[1]);
                    }
                }
            }
        }

        if ($executed == 0) {
            require_once __DIR__ . '/../controllers/' . $this->controllerFile . '.php';
            $this->controllerFile = new $this->controllerFile; #ubah menjadi objecct
        }

        if (!empty($url)) {
            $this->parameters = array_values($url);
        }

        call_user_func_array([$this->controllerFile, $this->controllerMethod], $this->parameters);
    }

    private function getUrl()
    {
        $url = rtrim($_SERVER['QUERY_STRING'], '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = explode('/', $url);
        return $url;
    }
}
