<?php

class Routes
{
    public function run()
    {
        $router = new App();
        $router->setDefaultController('DefaultApp');
        $router->setDefaultMethod('index');

        #list router
        $router->get('/user', ['UserController', 'index']);
        $router->post('/user/register', ['UserController', 'register']);
        $router->post('/user/login', ['UserController', 'login']);



        $router->run();
    }
}
