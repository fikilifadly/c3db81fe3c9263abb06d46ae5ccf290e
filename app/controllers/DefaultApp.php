<?php

class DefaultApp extends BaseController
{
    public function index($url)
    {
        $data = [
            'message' => 'Internal Server Error',
        ];
        $this->view('template/header', $data);
        header('Content-Type: application/json');
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
    }
}
