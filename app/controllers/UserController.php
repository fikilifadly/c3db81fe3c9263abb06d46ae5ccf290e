<?php
class UserController extends BaseController
{
    private $user;

    public function __construct()
    {
        $this->user = $this->model('User');
    }

    public function index()
    {
        echo "Hello World From User";
    }

    public function login()
    {
        $data = [
            'title' => 'Login',
            'users' => $this->user->getAll()
        ];
        $this->view('user/login', $data);
    }

    public function register()
    {
        $this->view('user/register');
    }
}
