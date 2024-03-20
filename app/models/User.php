<?php

class User extends Database
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {

        $query = 'SELECT *
        FROM ' . '"Users"';
        var_dump($query);
        return $this->query($query)->fetchAll();
    }
}
