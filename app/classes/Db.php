<?php
require './htmlpurifier-4.15.0/library/HTMLPurifier.auto.php';

class Db {
    private $pdo; // PDO instance
    private $select_result; // result

    public function __construct($server, $user, $pass, $db) {
        try {
            // Create PDO instance
            $this->pdo = new PDO("mysql:host=$server;dbname=$db;charset=utf8", $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            exit();
        }
    }


    public function select($sql, $params = []) {
        $results = [];
        try {
            $stmt = $this->pdo->prepare($sql); // Prepare the SQL statement
            $stmt->execute($params); // Execute with parameters
            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                $results[] = $row;
            }
            $this->select_result = $results;
            return $results;
        } catch (PDOException $e) {
            echo "Select failed: " . $e->getMessage();
            return false;
        }
    }
    public function addMessage($name, $type, $content, $user_id) {
        $filtered_name = Filter::filter_name($name);
        $filtered_type = Filter::filter_type($type);
        $filtered_content = Filter::filter_general($content);

        $sql = "INSERT INTO message (`name`, `type`, `message`, `deleted`, `user_id`) VALUES (:name, :type, :content, 0, :user_id)";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':name', $filtered_name);
            $stmt->bindParam(':type', $filtered_type);
            $stmt->bindParam(':content', $filtered_content);
            $stmt->bindParam(':user_id', $user_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Add message failed: " . $e->getMessage();
            return false;
        }
    }
    public function addMessageBasic($name, $type, $content) {
        $name = addslashes($_REQUEST['name']);
        $type = addslashes($_REQUEST['type']);
        $content = addslashes($_REQUEST['content']);

        $sql = "INSERT INTO message (`name`, `type`, `message`, `deleted`) VALUES (:name, :type, :content, 0)";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':content', $content);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Add message failed: " . $e->getMessage();
            return false;
        }
    }

    public function updateMessage($id, $name, $type, $content) {
        $filtered_name = Filter::filter_name($name);
        $filtered_type = Filter::filter_type($type);
        $filtered_content = Filter::filter_general($content);

        $sql = "UPDATE message SET name = ?, type = ?, message = ? WHERE id = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(1, $filtered_name);
            $stmt->bindParam(2, $filtered_type);
            $stmt->bindParam(3, $filtered_content);
            $stmt->bindParam(4, $id);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo "Error updating message: " . $e->getMessage();
            return false;
        }
    }

    public function __destruct() {
        $this->pdo = null; // Close the PDO connection
    }
}
?>
