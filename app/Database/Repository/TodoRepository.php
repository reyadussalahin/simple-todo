<?php declare(strict_types=1);

namespace App\Database\Repository;

use App\Database\Database;


class TodoRepository
{
    private $db;
    private $conn;

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->conn = $db->connection();
    }

    public function add(string $content, string $status)
    {
        $sql = "INSERT INTO todo (content, status) VALUES (:content, :status)";
        if(!($stmt = $this->conn->prepare($sql))) {
            throw new \Exception(
                "Couldn't prepare statement for table \"todo\" while inserting data",
                1
            );
        }
        $data = [
            ":content" => $content,
            ":status" => $status
        ];
        if(!$stmt->execute($data)) {
            throw new \Exception(
                "Couldn't execute statement for table \"todo\" while inserting data",
                1
            );
        }
        $id = $this->conn->lastInsertId();
        return [
            "id" => $id,
            "content" => htmlspecialchars($content),
            "status" => htmlspecialchars($status)
        ];
    }

    public function all()
    {
        $sql = "SELECT id, content, status FROM todo";
        if(!($stmt = $this->conn->prepare($sql))) {
            throw new \Exception(
                "Couldn't prepare statement for table \"todo\" while retrieving all todos",
                1
            );
        }
        if(!$stmt->execute()) {
            throw new \Exception(
                "Couldn't execute statement for table \"todo\" while retrieving all todos",
                1
            );
        }
        $todos = [];
        while($row = $stmt->fetch()) {
            $todos[$row["id"]] = [
                "id" => $row["id"],
                "content" => htmlspecialchars($row["content"]),
                "status" => htmlspecialchars($row["status"])
            ];
        }
        return $todos;
    }

    public function update($id, string $content, string $status)
    {
        $sql = "UPDATE todo
            SET content = :content,
                status = :status
            WHERE id = :id";
        if(!($stmt = $this->conn->prepare($sql))) {
            throw new \Exception(
                "Couldn't prepare statement for table \"todo\" while updating todo",
                1
            );
        }
        $data = [
            ":id" => $id,
            ":content" => $content,
            ":status" => $status
        ];
        if(!$stmt->execute($data)) {
            throw new \Exception(
                "Couldn't execute statement for table \"todo\" while updating todo",
                1
            );
        }
        return [
            "id" => $id,
            "content" => htmlspecialchars($content),
            "status" => htmlspecialchars($status)
        ];
    }

    public function remove($id)
    {
        $sql = "DELETE FROM todo WHERE id = :id";
        if(!($stmt = $this->conn->prepare($sql))) {
            throw new \Exception(
                "Couldn't prepare statement for table \"todo\" while deleting todo",
                1
            );
        }
        $data = [
            ":id" => $id,
        ];
        if(!$stmt->execute($data)) {
            throw new \Exception(
                "Couldn't execute statement for table \"todo\" while deleting todo",
                1
            );
        }
        return $stmt->rowCount() === 1;
    }

    public function removeMultiple(array $ids)
    {
        $n = count($ids);
        if($n === 0) {
            return true;
        }
        $placeholder = "(?";
        for($i = 1; $i < $n; $i++) {
            $placeholder .= ", ?";
        }
        $sql = "DELETE FROM todo WHERE id in " . $placeholder . ")";
        if(!($stmt = $this->conn->prepare($sql))) {
            throw new \Exception(
                "Couldn't prepare statement for table \"todo\" while deleting todo",
                1
            );
        }
        if(!$stmt->execute($ids)) {
            throw new \Exception(
                "Couldn't execute statement for table \"todo\" while deleting todo",
                1
            );
        }
        return $stmt->rowCount() === $n;
    }
}
