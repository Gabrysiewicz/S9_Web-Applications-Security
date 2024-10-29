<?php

// class Db {
//     private $mysqli; //Database variable
//     private $select_result; //result
    
//     public function __construct($serwer, $user, $pass, $baza) {
//         $this->mysqli = new mysqli($serwer, $user, $pass, $baza);
//         if ($this->mysqli->connect_errno) {
//             printf("Connection to server failed: %s \n", $this->mysqli->connect_error);
//             exit();
//         }
//         if ($this->mysqli->set_charset("utf8")) { 
//             //charset changed 
//         }
//         function __destruct() {
//             $this->mysqli->close();
//         }
//     }
//     public function select($sql) {
//         $results=array();
//         if ($result = $this->mysqli->query($sql)) {
//             while ($row = $result->fetch_object()) {
//                 $results[]=$row;
//             }
//             $result->close();
//         }
//         $this->select_result=$results;
//         return $results;
//     }
//     public function addMessage($name, $type, $content){
//         $sql = "INSERT INTO message (`name`,`type`, `message`,`deleted`) VALUES ('" . $name . "','" . $type . "','" . $content . "',0)";
//         echo $sql;
//         echo "<BR\>";
//         return $this->mysqli->query($sql);
//     } 
//     public function getMessage($message_id){
//         foreach ($this->select_result as $message):
//             if($message->id==$message_id)
//                 return $message->message;
//         endforeach;
//     } 
//     public function updateMessage($id, $name, $type, $content) {
//         $stmt = $this->mysqli->prepare("UPDATE message SET name = ?, type = ?, message = ? WHERE id = ?");
        
//         if ($stmt) {
//             $stmt->bind_param("sssi", $name, $type, $content, $id);
//             $stmt->execute();
//             $stmt->close();
//             return true;
//         } else {
//             printf("Error updating message: %s\n", $this->mysqli->error);
//             return false;
//         }
//     }
// }
class Db {
    private $pdo; // PDO instance
    private $select_result; // result

    public function __construct($server, $user, $pass, $db) {
        try {
            // Create a new PDO instance
            $this->pdo = new PDO("mysql:host=$server;dbname=$db;charset=utf8", $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            exit();
        }
    }

    public function select($sql) {
        $results = [];
        try {
            $stmt = $this->pdo->query($sql);
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

    public function addMessage($name, $type, $content) {
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

    public function getMessage($message_id) {
        foreach ($this->select_result as $message) {
            if ($message->id == $message_id) {
                return $message->message;
            }
        }
    }

    public function updateMessage($id, $name, $type, $content) {
        $sql = "UPDATE message SET name = ?, type = ?, message = ? WHERE id = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(1, $name);
            $stmt->bindParam(2, $type);
            $stmt->bindParam(3, $content);
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
