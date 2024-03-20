<?php

class Routes
{
    public function run()
    {
        $router = new App();
        $router->setDefaultController('DefaultApp');
        $router->setDefaultMethod('index');

        #user
        $router->get('/user', ['UserController', 'index']);
        $router->get('/user/login', ['UserController', 'login']);
        $router->post('/user/register', ['UserController', 'register']);
        $router->post('/user/login', ['UserController', 'login']);

        #message
        $router->get('/message', ['MessageController', 'index']);
        $router->post('/message/add', ['MessageController', 'add']);


        $router->run();
    }
}
