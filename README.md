<table align='center'>
  <tr> <td colspan='3' align='center' width='884px'> Web Applications Security </td> </tr>
  <tr> <td colspan="3" align='center'> <img src='https://github.com/Gabrysiewicz/Programowanie-aplikacji-w-chmurze-obliczeniowe/blob/main/logo_politechniki_lubelskiej.jpg' width="400px" height="400px"></td> </tr>
  <tr> <td> Kamil Gabrysiewicz </td> <td> Index: 95400 </td> <td> Grupa: 2.1 </td> </tr>  
  <tr> <td> Wtorek 11:45-13:15 </td> <td> Semestr 2 </td> <td>Laboratorium 7</td></tr>  
</table>


# Task 7.1.
Add functionality to record user login and logout times in the application developed during previous labs.

Structure of the table responsible for holding login-related data.
```
+------------+------------------------+------+-----+-------------------+-------------------+
| Field      | Type                   | Null | Key | Default           | Extra             |
+------------+------------------------+------+-----+-------------------+-------------------+
| id         | int                    | NO   | PRI | NULL              | auto_increment    |
| user_id    | int                    | NO   | MUL | NULL              |                   |
| event_type | enum('login','logout') | NO   |     | NULL              |                   |
| event_time | timestamp              | YES  |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| ip_address | varchar(45)            | YES  |     | NULL              |                   |
+------------+------------------------+------+-----+-------------------+-------------------+
```

Possible contents.
```
+----+---------+------------+---------------------+------------+
| id | user_id | event_type | event_time          | ip_address |
+----+---------+------------+---------------------+------------+
| 17 |       1 | login      | 2024-11-29 14:20:16 | 172.19.0.1 |
| 18 |       1 | logout     | 2024-11-29 14:23:22 | 172.19.0.1 |
| 19 |       1 | login      | 2024-11-29 14:27:32 | 172.19.0.1 |
| 20 |       3 | login      | 2024-11-29 14:29:25 | 172.19.0.1 |
| 21 |       3 | logout     | 2024-11-29 14:49:29 | 172.19.0.1 |
| 22 |       3 | login      | 2024-11-29 14:49:37 | 172.19.0.1 |
| 23 |       3 | logout     | 2024-11-29 21:06:42 | 172.19.0.1 |
| 24 |       3 | login      | 2024-11-29 21:07:01 | 172.19.0.1 |
| 25 |       1 | login      | 2024-12-03 11:06:41 | 172.19.0.1 |
| 26 |       1 | logout     | 2024-12-03 11:12:32 | 172.19.0.1 |
| 27 |       3 | login      | 2024-12-03 11:13:09 | 172.19.0.1 |
| 28 |       3 | logout     | 2024-12-03 11:19:14 | 172.19.0.1 |
| 29 |       1 | login      | 2024-12-03 11:19:30 | 172.19.0.1 |
| 30 |       1 | logout     | 2024-12-03 11:30:03 | 172.19.0.1 |
| 31 |       1 | login      | 2024-12-03 11:30:20 | 172.19.0.1 |
| 32 |       1 | logout     | 2024-12-03 11:39:39 | 172.19.0.1 |
| 33 |       3 | login      | 2024-12-03 11:39:49 | 172.19.0.1 |
| 34 |       3 | logout     | 2024-12-03 11:53:02 | 172.19.0.1 |
| 35 |       3 | login      | 2024-12-03 11:53:31 | 172.19.0.1 |
+----+---------+------------+---------------------+------------+
```

Function in `Pdo_.php` responsible for recording user session log activity, called during login or logout.
```
public function log_user_event($user_id, $event_type) {
        $ip_address = $_SERVER['REMOTE_ADDR'];
        
        $query = "INSERT INTO user_session_log (user_id, event_type, ip_address) VALUES (:user_id, :event_type, :ip_address)";
        $stmt = $this->pdo->prepare($query);
        
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':event_type', $event_type, PDO::PARAM_STR);
        $stmt->bindParam(':ip_address', $ip_address, PDO::PARAM_STR);
        
        $stmt->execute();
    }
```

Use of log_user_event()
```
// Log user in with 2FA
if (isset($_REQUEST['verify_user'])) {
    $code = $_REQUEST['code'];
    $login = $_SESSION['login'];
    if ($pdo->log_2F_step2($login, $code)) {
        echo 'You are logged in as: ' . $_SESSION['login'];
        $_SESSION['logged'] = 'YES';
        $pdo->log_user_event($_SESSION['user_id'], 'login');  // Log user event for login
    }    
}
// Logout
if (isset($_POST['logout'])) {
    // Log user event for logout
    if (isset($_SESSION['user_id'])) {
        $pdo->log_user_event($_SESSION['user_id'], 'logout');
    }
    // Unset session variables and destroy the session
    session_unset();
    session_destroy();
}
```


# Task 7.2.
### Extend the application with the functionality of recording user activity.
### Register what data was modified && Record what data the user displayed (just write down the record numbers and table name)

<hr/>
My application stores the history of what the user views, adds, edits, and deletes in the same table, `user_activity_log`.

```
+---------------+--------------------------------------+------+-----+-------------------+-------------------+
| Field         | Type                                 | Null | Key | Default           | Extra             |
+---------------+--------------------------------------+------+-----+-------------------+-------------------+
| id            | int                                  | NO   | PRI | NULL              | auto_increment    |
| user_id       | int                                  | NO   | MUL | NULL              |                   |
| action_type   | enum('view','add','delete','update') | NO   |     | NULL              |                   |
| table_name    | varchar(50)                          | NO   |     | NULL              |                   |
| record_id     | int                                  | YES  |     | NULL              |                   |
| previous_data | text                                 | YES  |     | NULL              |                   |
| new_data      | text                                 | YES  |     | NULL              |                   |
| timestamp     | timestamp                            | YES  |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
+---------------+--------------------------------------+------+-----+-------------------+-------------------+
```

Example content
```
+----+---------+-------------+------------+-----------+---------------+----------+---------------------+
| id | user_id | action_type | table_name | record_id | previous_data | new_data | timestamp           |
+----+---------+-------------+------------+-----------+---------------+----------+---------------------+
|  1 |       1 | view        | message    |      NULL | NULL          | NULL     | 2024-11-29 11:19:11 |
|  2 |       1 | view        | message    |      NULL | NULL          | NULL     | 2024-11-29 11:19:47 |
|  3 |       1 | view        | message    |      NULL | NULL          | NULL     | 2024-11-29 12:21:36 |
|  4 |       1 | view        | message    |      NULL | NULL          | NULL     | 2024-11-29 13:20:00 |
|  5 |       1 | add         | message    |        15 | NULL          | NULL     | 2024-11-29 13:20:06 |
+----+---------+-------------+------------+-----------+---------------+----------+---------------------+
```

Function responsible for adding user activity to the table.
```
public function log_user_activity($user_id, $action_type, $table_name, $record_id, $previous_data = null, $new_data = null) {
    $sql = "INSERT INTO user_activity_log (user_id, action_type, table_name, record_id, previous_data, new_data) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$user_id, $action_type, $table_name, $record_id, $previous_data, $new_data]);
}
```

For example, `log_user_activity()` is called during message activities such as adding, updating, and deleting a message.
```
// Adding a new message
if (isset($_POST['add_message'])) {
    /* Some code */
        try {
            $message_id = $pdo->addMessage($name, $type, $content, $user_id);
            if ($message_id) {
                $pdo->log_user_activity($user_id, 'add', 'message', $message_id);
            }
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Failed to add message: " . htmlspecialchars($e->getMessage()) . "</p>";
        }        
    /* Some code */
}

// Editing an existing message
if (isset($_POST['update_message'])) {
    /* Some code */
            // Update the message
            if ($pdo->updateMessage($id, $name, $type, $content)) {
                echo "<p>Message updated successfully.</p>";

                // Log the user activity
                $pdo->log_user_activity($_SESSION["user_id"], 'update', 'message', $id, $existing_message, $content);
            } else {
                echo "<p style='color:red;'>Updating message failed.</p>";
            }
  /* Some code */
}

// Delete an existing message
if (isset($_GET['delete_message'])) {
    /* Some code */
    if ($stmt->execute([$id])) {
      echo "<p style='color:green;'>Message deleted successfully.</p>";
      $pdo->log_user_activity($user_id, 'delete', 'message', $id, $existing_message, 'deleted');
    } else {
      echo "<p style='color:red;'>Failed to delete message.</p>";
    }
    /* Some code */
}
```

### Add the ability to display user activity (only the administrator should have access to this function)

<p align='center'>
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab7/img/Task7_2a.png" >
</p>
<hr/>
<p align='center'>
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab7/img/Task7_2b.png" >
</p>
<hr/>

### Add the ability to restore the previous version of a selected record – display the history of changes made for the selected record and enable the restoration of the selected version of data.

To store updates for each record, I decided to create a table `message_history`, and with the use of triggers, user actions will be automatically stored in the new table.

Table for record history
```
CREATE TABLE message_history (
    history_id INT AUTO_INCREMENT PRIMARY KEY,
    message_id INT NOT NULL,
    name VARCHAR(255),
    type VARCHAR(50),
    message TEXT,
    deleted TINYINT(1),
    user_id INT,
    action_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (message_id) REFERENCES message(id)
);
```

Trigger for Update of message
```
DELIMITER $$

CREATE TRIGGER before_message_update
BEFORE UPDATE ON message
FOR EACH ROW
BEGIN
    INSERT INTO message_history (message_id, name, type, message, deleted, user_id, action_date)
    VALUES (OLD.id, OLD.name, OLD.type, OLD.message, OLD.deleted, OLD.user_id, NOW());
END$$

DELIMITER ;

```

Trigger for Delete of message
```
DELIMITER $$

CREATE TRIGGER before_message_delete
BEFORE DELETE ON message
FOR EACH ROW
BEGIN
    INSERT INTO message_history (message_id, name, type, message, deleted, user_id, action_date)
    VALUES (OLD.id, OLD.name, OLD.type, OLD.message, OLD.deleted, OLD.user_id, NOW());
END$$

DELIMITER ;

```
### View for messages, all public and undeleted messages are displayed. Logged in as admin I will delete the last message

<p align='center'>
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab7/img/Task7_2c.png" >
</p>
<hr/>

### Message was successfully deleted

<p align='center'>
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab7/img/Task7_2d.png" >
</p>
<hr/>

### Now, in the privileges view, accessible only to admins, I can see updates in the message_history. The form allows me to insert the ID of the history record that I want to revert for a message. In this example, I will use the last record, which is 11, and restore an earlier deleted message.

<p align='center'>
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab7/img/Task7_2e.png" >
</p>
<hr/>

### After that, I get a message informing me about the status of the operation, which is obviously successful.

<p align='center'>
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab7/img/Task7_2f.png" >
</p>
<hr/>

### In `Messages` view once again I can see the message that was deleted earlier but was restored by an admin

<p align='center'>
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab7/img/Task7_2g.png" >
</p>
<hr/>

# Code Snippet

The main logic in `Pdo_.php`:
```
  public function log_user_activity($user_id, $action_type, $table_name, $record_id, $previous_data = null, $new_data = null) {
        $sql = "INSERT INTO user_activity_log (user_id, action_type, table_name, record_id, previous_data, new_data) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id, $action_type, $table_name, $record_id, $previous_data, $new_data]);
    }

    public function get_user_activity($user_id = null) {
        $sql = "SELECT * FROM user_activity_log ORDER BY timestamp DESC";
        if ($user_id) {
            $sql = "SELECT * FROM user_activity_log WHERE user_id = ? ORDER BY timestamp DESC";
        }
        
        $stmt = $this->pdo->prepare($sql);
        if ($user_id) {
            $stmt->execute([$user_id]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    public function get_message_history() {
        $sql = "SELECT * FROM message_history ORDER BY message_id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    public function revert_message($history_id) {
        try {
            // Fetch the record from the history table
            $sql = "SELECT * FROM message_history WHERE history_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$history_id]);
            $history = $stmt->fetch(PDO::FETCH_OBJ);
    
            if (!$history) {
                throw new Exception("History record not found.");
            }
    
            // Update the message table to match the historical record
            $update_sql = "UPDATE message 
                           SET name = ?, type = ?, message = ?, deleted = ? 
                           WHERE id = ?";
            $update_stmt = $this->pdo->prepare($update_sql);
            $update_stmt->execute([
                $history->name,
                $history->type,
                $history->message,
                $history->deleted,
                $history->message_id
            ]);
    
            return true;
        } catch (Exception $e) {
            echo "Error reverting message: " . $e->getMessage();
            return false;
        }
    }
```
