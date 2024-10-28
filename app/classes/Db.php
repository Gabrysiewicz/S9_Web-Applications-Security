<?php
class Db {
    private $mysqli; //Database variable
    private $select_result; //result
    
    public function __construct($serwer, $user, $pass, $baza) {
        $this->mysqli = new mysqli($serwer, $user, $pass, $baza);
        if ($this->mysqli->connect_errno) {
            printf("Connection to server failed: %s \n", $this->mysqli->connect_error);
            exit();
        }
        if ($this->mysqli->set_charset("utf8")) { 
            //charset changed 
        }
        function __destruct() {
            $this->mysqli->close();
        }
    }
    public function select($sql) {
        $results=array();
        if ($result = $this->mysqli->query($sql)) {
            while ($row = $result->fetch_object()) {
                $results[]=$row;
            }
            $result->close();
        }
        $this->select_result=$results;
        return $results;
    }
    public function addMessage($name, $type, $content){
        $sql = "INSERT INTO message (`name`,`type`, `message`,`deleted`) VALUES ('" . $name . "','" . $type . "','" . $content . "',0)";
        echo $sql;
        echo "<BR\>";
        return $this->mysqli->query($sql);
    } 
    public function getMessage($message_id){
        foreach ($this->select_result as $message):
            if($message->id==$message_id)
                return $message->message;
        endforeach;
    } 
    public function updateMessage($id, $name, $type, $content) {
        $stmt = $this->mysqli->prepare("UPDATE message SET name = ?, type = ?, message = ? WHERE id = ?");
        
        if ($stmt) {
            $stmt->bind_param("sssi", $name, $type, $content, $id);
            $stmt->execute();
            $stmt->close();
            return true;
        } else {
            printf("Error updating message: %s\n", $this->mysqli->error);
            return false;
        }
    }
}
?>
