<?php
class Message extends Database
{
    public function getAll()
    {
        $query = 'SELECT *
        FROM ' . '"Messages"';
        return $this->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addMessage($data)
    {


        $query = 'INSERT INTO "Messages" (name, email, message)
        VALUES (:name, :email, :message)';

        return $this->query($query, $data);
    }
}
