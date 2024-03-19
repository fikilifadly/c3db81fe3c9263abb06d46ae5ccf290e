<?php

class Routes
{
    private $controllerFile = 'DefaultApp'; # untuk default agar ketika tidak terdapat route akan mengambil controller DefaultApp
    private $controllerMethod = 'index'; # untuk default agar ketika tidak terdapat route akan mengambil controller DefaultAppController

    private $parameters = [];

    public function run()
    {
        $url = $this->getUrl();

        if (isset($url[0]) && file_exists(__DIR__ . '/../controllers/' . $url[0] . '.php')) {
            $this->controllerFile = $url[0];
            unset($url[0]); #untuk menghilangkan index 0
        }

        require_once __DIR__ . '/../controllers/' . $this->controllerFile . '.php';


        if (isset($url[1])) {
            if (method_exists($this->controllerFile, $url[1])) {
                $this->controllerMethod = $url[1];
                unset($url[1]); #untuk menghilangkan index 1
            }
        }



        $this->controllerFile = new $this->controllerFile; #ubah menjadi objecct

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
