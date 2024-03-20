<?php

class DefaultApp extends BaseController
{
    public function index($url)
    {
        $data = [
            'message' => 'NOT FOUND',
        ];
        $this->view('template/header', $data);
        header('Content-Type: application/json');
        header("HTTP/1.0 404 Not Found");
        echo json_encode($data);
    }
}
