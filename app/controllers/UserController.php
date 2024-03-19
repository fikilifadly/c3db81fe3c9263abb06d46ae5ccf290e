<?php
class UserController extends BaseController
{
    public function index()
    {
        echo "Hello World From User";
    }

    public function login()
    {
        $this->view('user/login');
    }

    public function register()
    {
        $this->view('user/register');
    }
}
