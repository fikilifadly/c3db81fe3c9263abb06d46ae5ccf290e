<?php
class MessageController extends BaseController
{

    public function index()
    {
        try {
            $data = $this->model('Message')->getAll();
            // var_dump()
            $data = [
                'status' => 200,
                'message' => 'Success',
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

    public function sendMessage()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $this->model('Message')->addMessage($data);
        echo json_encode(['status' => 200, 'message' => 'Success']);
    }
}
