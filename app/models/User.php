<?php

class User extends Database
{
    public function __construct()
    {
        parent::__construct();
        $this->setTableName('Users');
        $this->setColumn([
            'name',
            'email',
            'password'
        ]);
    }


    public function getAll()
    {
        $query = 'SELECT *
        FROM ' . '"Users"';
        var_dump($query);
        return $this->query($query)->fetchAll();
    }

    public function ifUserExist($email)
    {
        $query = 'SELECT *
        FROM ' . '"Users"
        WHERE email = ' . $email;
        return $this->query($query)->fetch();
    }

    public function getUserByEmail($email)
    {
        $query = 'SELECT * FROM ' . '"Users" WHERE email = ' . "'" . $email . "'";
        return $this->query($query)->fetch();
    }

    public function addUser($data)
    {
        $newdate = new DateTime();
        $query = 'INSERT INTO ' . '"Users" ' . '( name, email, password, "createdAt", "updatedAt" ) VALUES (' . "'" . $data['name'] . "', '"  . $data['email'] . "', '" . password_hash($data['password'], PASSWORD_BCRYPT) . "', '" . $newdate->format('Y-m-d H:i:s') . "', '" . $newdate->format('Y-m-d H:i:s') . "')";
        return $this->query($query);
    }
}
