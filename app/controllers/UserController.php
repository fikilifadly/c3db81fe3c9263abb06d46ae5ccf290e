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
            'email' => 'required|email',
            'password' => 'required'
        ];

        $messages = [
            'email.required' => 'Email harus diisi!',
            'email.email' => 'Email harus berupa email valid!',
            'password.required' => 'Password harus diisi!',
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
        } else {
            $isExist = $this->user->getUserByEmail($inputs['email']);
            if (!$isExist) {
                $data = [
                    'status' => 401,
                    'message' => 'Invalid Email or Password'
                ];
                header('HTTP/1.1 400 Bad Request');
                header('Content-Type: application/json');
                echo json_encode($data);
                exit();
            } else {
                if (!password_verify($inputs['password'], $isExist['password'])) {
                    $data = [
                        'status' => 401,
                        'message' => 'Invalid Email or Password'
                    ];
                    header('HTTP/1.1 401 Bad Request');
                    header('Content-Type: application/json');
                    echo json_encode($data);
                    exit();
                }

                // karna gak ngerti pake composer di docker maka pake ini
                $access_token = password_hash($inputs['email'], PASSWORD_BCRYPT);

                $data = [
                    'status' => 200,
                    'message' => 'Success',
                    '$access_token' => $access_token
                ];
                header('Content-Type: application/json');
                header('HTTP/1.1 200 OK');
                echo json_encode($data);
            }
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
            'name' => 'required|min:1',
            'email' => 'required|email|min:1',
            'password' => 'required|min:6'
        ];

        $messages = [
            'name.required' => 'Name harus diisi!',
            'name.min' => 'Name harus diisi!',
            'email.required' => 'Email harus diisi!',
            'email.email' => 'Email harus berupa email valid!',
            'email.min' => 'Email harus diisi!',
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
        } else {

            $isExist = $this->user->getUserByEmail($inputs['email']);
            if ($isExist) {
                $data = [
                    'status' => 400,
                    'message' => 'Email already exist'
                ];
                header('HTTP/1.1 400 Bad Request');
                header('Content-Type: application/json');
                echo json_encode($data);
                exit();
            }

            $this->user->addUser($inputs);

            $data = [
                'status' => 201,
                'message' => 'Register Success'
            ];
            header('HTTP/1.1 201 Created');
            header('Content-Type: application/json');
            echo json_encode($data);
        }
    }
}
