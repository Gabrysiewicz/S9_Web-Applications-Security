<?php
require './htmlpurifier-4.15.0/library/HTMLPurifier.auto.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Pdo_{
    private $pdo;
    private $purifier;
    private $select_result; // result

    public function __construct($server, $user, $pass, $db) {
        $config = HTMLPurifier_Config::createDefault();
        $this->purifier = new HTMLPurifier($config);
        try {
            $this->pdo = new PDO("mysql:host=$server;dbname=$db;charset=utf8", $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            die();
        }
    }
    // Assuming you have a PDO connection already set up
    public function log_user_event($user_id, $event_type) {
        $ip_address = $_SERVER['REMOTE_ADDR'];  // Get the user's IP address
        
        $query = "INSERT INTO user_session_log (user_id, event_type, ip_address) VALUES (:user_id, :event_type, :ip_address)";
        $stmt = $this->pdo->prepare($query);
        
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':event_type', $event_type, PDO::PARAM_STR);
        $stmt->bindParam(':ip_address', $ip_address, PDO::PARAM_STR);
        
        $stmt->execute();
    }
    public function add_user($login, $email, $password) {
        $login = $this->purifier->purify($login);
        $email = $this->purifier->purify($email);
        $salt = bin2hex(random_bytes(8)); // 16 characters
    
        try {
            // Start transaction
            $this->pdo->beginTransaction();
    
            // Insert user into the `user` table
            $sql = "INSERT INTO `user` (
                `login`, `email`, `hash`, `salt`, `id_status`, `password_form`, `name`, `surname`
            ) VALUES (
                :login, :email, :hash, :salt, :id_status, :password_form, :name, :surname
            )";
    
            $hashedPassword = hash('sha512', $salt . $password);
    
            $data = [
                'login' => $login,
                'email' => $email,
                'hash' => $hashedPassword,
                'salt' => $salt,
                'id_status' => '1',
                'password_form' => '1',
                'name' => 'changeLater',
                'surname' => 'changeLater'
            ];
    
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);
    
            // Get the newly created user's ID
            $userId = $this->pdo->lastInsertId();
    
            // Insert the default role into the `user_role` table
            $roleSql = "INSERT INTO `user_role` (`id_role`, `id_user`, `issue_time`) VALUES (:id_role, :id_user, :issue_time)";
            $roleData = [
                'id_role' => 1, // Default role ID
                'id_user' => $userId
            ];
    
            $this->pdo->prepare($roleSql)->execute($roleData);
    
            // Commit transaction
            $this->pdo->commit();
            $pdo->log_user_activity($userId, 'add', 'user', $this->pdo->lastInsertId());

    
        } catch (Exception $e) {
            // Rollback transaction in case of error
            $this->pdo->rollBack();
            echo "Adding new user failed: " . $e->getMessage();
        }
    }

    public function log_user_in($login, $password){
        $login = $this->purifier->purify($login);

        try {
            $sql = "SELECT id, hash, salt, login FROM user WHERE login = :login";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['login' => $login]);
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user_data) {
                // Retrieve the stored salt and hash
                $storedSalt = $user_data['salt'];
                $storedHash = $user_data['hash'];

                // Hash the entered password with the stored salt
                $hashedPassword = hash('sha512', $storedSalt . $password);

                // Compare the hashed password to the stored hash
                if ($hashedPassword === $storedHash) {
                    // Set session variables for successful login
                    session_start();
                    $_SESSION['logged_in'] = true;
                    $_SESSION['login'] = $user_data['login'];
                    $_SESSION['session_expiration'] = time() + 300; // 5 minutes from now

                    echo 'Login successful! <br/>';
                    echo 'You are logged in as: ' . htmlspecialchars($user_data['login']) . '<br/>';
                } else {
                    echo 'Login FAILED<br/>';
                }
            } else {
                echo 'Login FAILED: User not found<br/>';
            }
            
        } catch (Exception $e) {
            echo "Login attempt failed: " . $e->getMessage();
        }
    }

    // Method to change the user's password
    public function change_password($login, $old_password, $new_password) {
        $login = $this->purifier->purify($login);

        try {
            // Verify old password
            $sql = "SELECT id, hash, salt FROM user WHERE login = :login";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['login' => $login]);
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user_data) {
                $storedHash = $user_data['hash'];
                $storedSalt = $user_data['salt'];
                
                // Hash the old password with the stored salt
                $hashedOldPassword = hash('sha512', $storedSalt . $old_password);

                // Verify the old password
                if ($hashedOldPassword === $storedHash) {
                    // Generate new salt and hash for the new password
                    $newSalt = bin2hex(random_bytes(8)); // Generate a new salt
                    $newHash = hash('sha512', $newSalt . $new_password);

                    // Update the database with the new hash and salt
                    $updateSql = "UPDATE user SET hash = :newHash, salt = :newSalt WHERE id = :id";
                    $updateStmt = $this->pdo->prepare($updateSql);
                    $updateStmt->execute([
                        'newHash' => $newHash,
                        'newSalt' => $newSalt,
                        'id' => $user_data['id']
                    ]);

                    echo 'Password changed successfully!';
                } else {
                    echo 'Password change FAILED: Incorrect current password.';
                }
            } else {
                echo 'Password change FAILED: User not found.';
            }

        } catch (Exception $e) {
            echo "Password change attempt failed: " . $e->getMessage();
        }
    }

    public function log_2F_step1($login, $password){
        $login=$this->purifier->purify($login);
        try {
            $sql="SELECT id, hash, login, salt, email FROM user WHERE login=:login";
            $stmt= $this->pdo->prepare($sql);
            $stmt->execute(['login'=>$login]);
            $user_data=$stmt->fetch();
            $hashedPassword = hash('sha512', $user_data['salt'].$password);
            
            if($hashedPassword == $user_data['hash']){
                //generate and send OTP
                $otp = random_int(100000, 999999);
                $code_lifetime = date('Y-m-d H:i:s', time()+300);
                try{
                    $sql="UPDATE `user` SET `sms_code`=:code, `code_timelife`=:lifetime WHERE login=:login";
                    $data= [
                        'login' => $login,
                        'code' => $otp,
                        'lifetime' => $code_lifetime
                    ];
                    $this->pdo->prepare($sql)->execute($data);
                    // Send OTP via email
                    // $emailSent = $this->sendOtpEmail($user_data['email'], $otp);
                    // if (!$emailSent) {
                    //     return ['result' => 'failed', 'message' => 'OTP email failed to send.'];
                    // } 
                    print "code: ".$otp;                   
                    $result= [
                        'result'=>'success'
                    ];
                    $_SESSION['login'] = $login;
                    return $result;
                } catch (Exception $e) {
                    print 'Exception' . $e->getMessage();
                    return ['result' => 'failed', 'message' => 'Database error: ' . $e->getMessage()];
                }
            }else{
                echo 'login FAILED<BR/>';
                $result= [
                    'result'=>'failed'
                ];
                return ['result' => 'failed', 'message' => 'Invalid login credentials.'];
            }
        } catch (Exception $e) {
            print 'Exception' . $e->getMessage();
            return ['result' => 'failed', 'message' => 'Database error: ' . $e->getMessage()];
        }
    }    
    private function sendOtpEmail($email, $otp) {
        $subject = "Your OTP Code";
        $message = "Your OTP code is: $otp. It is valid for 5 minutes.";
        $headers = "From: no-reply@yourdomain.com\r\n" .
                   "Reply-To: s95400@pollub.edu.pl\r\n" .
                   "X-Mailer: PHP/" . phpversion();
    
        if (mail($email, $subject, $message, $headers)) {
            return true;
        } else {
            error_log("Failed to send OTP email to $email");
            return false;
        }
    }   
    public function log_2F_step2($login,$code){
        $login=$this->purifier->purify($login);
        $code=$this->purifier->purify($code);
        try {
            $sql="SELECT user.id as user_id,login,sms_code,code_timelife,role_name FROM user INNER JOIN user_role on user.id = user_role.id_user INNER JOIN role ON user_role.id_role = role.id WHERE login=:login";
            $stmt= $this->pdo->prepare($sql);
            $stmt->execute(['login'=>$login]);
            $user_data=$stmt->fetch();
            if($code==$user_data['sms_code'] && time()< strtotime($user_data['code_timelife'])){
                // Set session variables for successful login
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $user_data['user_id'];
                $_SESSION['login'] = $user_data['login'];
                $_SESSION['role'] = $user_data['role_name'];
                $_SESSION['session_expiration'] = time() + 300; // 5 minutes from now
                echo 'Login successful! <br/>';
                echo 'You are logged in as: ' . htmlspecialchars($user_data['login']) . '<br/>';
                return true;
            } else {
                echo 'login FAILED<BR/>';
                return false;
            }
        } catch (Exception $e) {
            print 'Exception' . $e->getMessage();
        }
    }
    public function check_session_expiration() {
        // Check if the session is active
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            // Check for session expiration
            if (isset($_SESSION['session_expiration']) && time() > $_SESSION['session_expiration']) {
                // Log the user event and expire the session
                $this->log_user_event($_SESSION['user_id'], 'logout');
                
                session_unset();  // Unset session variables
                session_destroy();  // Destroy the session
                header("Location: login.php");  // Redirect to login page after session expiration
                exit();
            } else {
                // Update session expiration time (e.g., extend session for another 5 minutes)
                $_SESSION['session_expiration'] = time() + 300; 
            }
        }
    }
    
    public function refresh_session_expiration(){
        if(isset($_SESSION["login"]) && isset($_SESSION["code"])){
            $login=$this->purifier->purify($_SESSION["login"]);
            $code=$this->purifier->purify($_SESSION["code"]);
            try {
                $sql="SELECT id,login,sms_code,code_timelife FROM user WHERE login=:login";
                $stmt= $this->pdo->prepare($sql);
                $stmt->execute(['login'=>$login]);
                $user_data=$stmt->fetch();
                if($code==$user_data['sms_code'] && time()< strtotime($user_data['code_timelife'])){
                    // Set session variables for successful login
                    $_SESSION['logged_in'] = true;
                    $_SESSION['login'] = $user_data['login'];
                    $_SESSION['session_expiration'] = time() + 300; // 5 minutes from now
                    return true;
                }
            } catch (Exception $e) {
                print 'Exception' . $e->getMessage();
            }
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
    public function session_check(){
        if( isset($_SESSION['session_expiration'])){
            echo "session_expiration: ".$_SESSION['session_expiration']."<br/>";
        }else{
            echo "session_expiration: null <br/>";
        }
        
        if( isset($_SESSION['logged_in'])){
            echo "Logged in: ".$_SESSION['logged_in']."<br/>";
        }else{
            echo "Logged in: null <br/>";
        }

        if( isset($_SESSION['role'])){
            echo "Role: ".$_SESSION['role']."<br/>";
        }else{
            echo "Role: unknown <br/>";
        }

        $this->refresh_session_expiration();
        $this->check_session_expiration();
    }

    public function get_permissions($sql){
        $results = [];
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                $results[] = $row;
            }
            $_SESSION["permissions"] = $results;  // Store results in session variable
            return true;
        } catch (PDOException $e) {
            echo "Select failed: " . $e->getMessage();
            return false;
        }
    }
    public function prepare($sql) {
        return $this->pdo->prepare($sql);
    }
    public function addMessage($name, $type, $content, $user_id) {
        $filtered_name = Filter::filter_name($name);
        $filtered_type = Filter::filter_type($type);
        $filtered_content = Filter::filter_general($content);
    
        $sql = "INSERT INTO message (`name`, `type`, `message`, `deleted`, `user_id`) VALUES (:name, :type, :content, 0, :user_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':name', $filtered_name);
        $stmt->bindParam(':type', $filtered_type);
        $stmt->bindParam(':content', $filtered_content);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }
    public function updateMessage($id, $name, $type, $content) {
        $filtered_name = Filter::filter_name($name);
        $filtered_type = Filter::filter_type($type);
        $filtered_content = Filter::filter_general($content);
    
        $sql = "UPDATE message SET name = ?, type = ?, message = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(1, $filtered_name);
        $stmt->bindParam(2, $filtered_type);
        $stmt->bindParam(3, $filtered_content);
        $stmt->bindParam(4, $id);
        return $stmt->execute(); // Return success/failure
    }
    

    public function __destruct() {
        $this->pdo = null; // Close the PDO connection
    }
    public function log_user_activity($user_id, $action_type, $table_name, $record_id, $previous_data = null, $new_data = null) {
        $sql = "INSERT INTO user_activity_log (user_id, action_type, table_name, record_id, previous_data, new_data) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id, $action_type, $table_name, $record_id, $previous_data, $new_data]);
    }

    public function get_user_activity($user_id = null) {
        // SQL query to get all activity logs (or filter by user if a user_id is passed)
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
        return $stmt->fetchAll(PDO::FETCH_OBJ); // Return results as an object
    }
    

}