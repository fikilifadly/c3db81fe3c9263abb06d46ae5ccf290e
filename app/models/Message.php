<?php
class Message extends Database
{
    public function __construct()
    {
        parent::__construct();
        $this->setTableName('Messages');
        $this->setColumn([
            'UserId',
            'to',
            'message'
        ]);
    }
    public function getAll()
    {
        $query = 'SELECT *
        FROM ' . '"Messages"';
        return $this->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addMessage($data)
    {
        $query = 'INSERT INTO "Messages" ("UserId", "to", "message", "createdAt", "updatedAt")
    VALUES (:UserId, :to, :message, :createdAt, :updatedAt)';

        $params = [
            ':UserId' => $data['UserId'],
            ':to' => $data['to'],
            ':message' => $data['message'],
            ':createdAt' => date('Y-m-d H:i:s'),
            ':updatedAt' => date('Y-m-d H:i:s'),
        ];
        return $this->query($query, $params);
    }
}
