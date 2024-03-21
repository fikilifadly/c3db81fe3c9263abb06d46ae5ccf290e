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
        $router->post('/messages/add', ['MessageController', 'sendMessage']);
        $router->get('/messages', ['MessageController', 'index']);


        $router->run();
    }
}
