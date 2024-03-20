<?php
class UserController extends BaseController
{
    private $user;

    public function __construct()
    {
        $this->user = $this->model('User');
    }

    public function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public function base64url_decode($str)
    {
        // Replace non-url-safe characters with their base64 counterparts
        $str = strtr($str, '-_', '+/');

        // Decode the base64url encoded string
        return base64_decode($str, true);
    }

    public function create_jwt($payload)
    {
        // karna gak ngerti pake composer di docker maka pake ini
        $headers = ['alg' => 'HS256', 'typ' => 'JWT'];
        $headers_encoded = $this->base64url_encode(json_encode($headers));

        $payload = ['email' => $payload];
        $payload_encoded = $this->base64url_encode(json_encode($payload));

        $key = 'secret';
        $signature = hash_hmac('sha256', "$headers_encoded.$payload_encoded", $key, true);
        $signature_encoded = $this->base64url_encode($signature);

        $token = "$headers_encoded.$payload_encoded.$signature_encoded";
        return $token;
    }
    public function verify_jwt($jwt)
    {
        // Split the JWT into parts
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            throw new Exception('Invalid JWT format');
        }

        // Decode header and payload (Base64url encoding)
        $header = json_decode($this->base64url_decode($parts[0]), true);
        $payload = json_decode($this->base64url_decode($parts[1]), true);

        // Validate header algorithm (only supports HS256 for simplicity)
        if (!isset($header['alg']) || $header['alg'] !== 'HS256') {
            throw new Exception('Unsupported signing algorithm');
        }

        // Validate expiration time (exp claim)
        if (isset($payload['exp']) && time() > $payload['exp']) {
            throw new Exception('JWT expired');
        }

        // Recreate signature (HMAC SHA-256)
        $signature = hash_hmac('sha256', implode('.', [$parts[0], $parts[1]]), 'secret', true);
        $encoded_signature = $this->base64url_encode($signature);

        // Verify signature
        if ($encoded_signature !== $parts[2]) {
            throw new Exception('Invalid signature');
        }

        // JWT is valid, return payload data
        return $payload;
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

                $token = $this->create_jwt($isExist['email']);


                $data = [
                    'status' => 200,
                    'message' => 'Success',
                    'access_token' => $token,
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
