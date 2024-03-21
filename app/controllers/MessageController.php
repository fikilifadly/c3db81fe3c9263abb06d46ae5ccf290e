<?php

class MessageController extends BaseController
{

    public function index2()
    {
        try {
            $data = $this->model('Message')->getAll();

            $data = [
                'status' => 200,
                'message' => 'Success 123',
                'data' => $data
            ];

            header('Content-Type: application/json');
            echo json_encode($data);
        } catch (\Throwable $th) {
            $data = [
                'message' => 'Internal Server Error',
            ];

            header('Content-Type: application/json');
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode($data);
            //throw $th;
        }
    }


    public function index()
    {
        $data = json_decode(file_get_contents('php://input'), true);


        if (!$data) {
            echo json_encode(['status' => 400, 'message' => 'Bad Request']);
            exit();
        }

        $token = $this->getToken();

        if (!$token) {
            header('Content-Type: application/json');
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['status' => 401, 'message' => 'Invalid Token']);
            exit();
        }

        $fields = [
            'email' => 'required|email|min:1',
            'message' => 'required|min:1',
        ];

        $messages = [
            'email.required' => 'Email harus diisi!',
            'email.email' => 'Email harus berupa email valid!',
            'email.min' => 'Email harus diisi!',
            'message.required' => 'Message harus diisi!',
            'message.min' => 'Message harus diisi!',
        ];

        [$inputs, $errors] = $this->filter($data, $fields, $messages);


        if ($errors) {
            echo json_encode(['status' => 400, 'message' => $errors]);
            exit();
        } else {
            $data = [
                'UserId' => $token['id'],
                'to' => $inputs['email'],
                'message' => $inputs['message'],
            ];


            $this->model('Message')->addMessage($data);
            $this->sendEmail($data, $token['email']);

            header('Content-Type: application/json');
            header('HTTP/1.1 201 Created');

            echo json_encode(['status' => 201, 'message' => 'Success Sent Message']);
        }
    }
}
