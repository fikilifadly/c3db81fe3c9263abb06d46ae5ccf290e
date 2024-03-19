<?php

class DefaultApp extends BaseController
{
    public function index($url)
    {
        if (empty($url)) {
            $data = ['title' => 'Home'];
            return $this->view('home/index', $data); #passing variable ke view
        }
        $data = ['title' => '404 ' . $url . ' NOT FOUND'];
        $this->view('404', $data);
    }
}
