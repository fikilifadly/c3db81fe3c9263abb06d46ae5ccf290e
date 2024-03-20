<?php
class UserController extends BaseController
{
    private $user;

    public function __construct()
    {
        $this->user = $this->model('User');
    }

    public function login()
    {

        $data = json_decode(file_get_contents('php://input'), true); # untuk mengambil data


        $fields = [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ];

        $messages = [
            'email.required' => 'Email harus diisi!',
            'email.email' => 'Email harus berupa email valid!',
            'password.required' => 'Password harus diisi!',
            'password.min' => 'Password minimal 6 karakter!'
        ];

        [$inputs, $errors] = $this->filter($data, $fields, $messages);



        if ($errors) {
            $data = [
                'status' => 400,
                'message' => $errors
            ];
            header('HTTP/1.1 400 Bad Request');
            header('Content-Type: application/json');
            echo json_encode($data);
            exit();
        }




        $user = $this->user->getUserByEmail($inputs['email']);
        if (!$user) {
            $data = [
                'status' => 401,
                'message' => 'Invalid email or password'
            ];
            header('HTTP/1.1 404 Not Found');
            header('Content-Type: application/json');
            echo json_encode($data);
            exit();
        }
    }



    public function register()
    {

        $data = json_decode(file_get_contents('php://input'), true); # untuk mengambil data

        if (!$data) {
            $data = [
                'status' => 400,
                'message' => 'Bad Request'
            ];
            header('HTTP/1.1 400 Bad Request');
            header('Content-Type: application/json');
            echo json_encode($data);
            exit();
        }



        $fields = [
            'name' => 'required',
            'email' => 'string|required|email',
            'password' => 'required|min:6'
        ];

        $messages = [
            'name.required' => 'Name harus diisi!',
            'email.required' => 'Email harus diisi!',
            'email.email' => 'Email harus berupa email valid!',
            'password.required' => 'Password harus diisi!',
            'password.min' => 'Password minimal 6 karakter!'
        ];

        [$inputs, $errors] = $this->filter($data, $fields, $messages);

        if ($errors) {
            $data = [
                'status' => 400,
                'message' => $errors
            ];
            header('HTTP/1.1 400 Bad Request');
            header('Content-Type: application/json');
            echo json_encode($data);
            exit();
        }
    }
}
