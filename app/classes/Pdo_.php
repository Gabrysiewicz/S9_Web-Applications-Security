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
    public function add_user($login, $email, $password){
        $login=$this->purifier->purify($login);
        $email=$this->purifier->purify($email);
        $salt = bin2hex(random_bytes(8)); // 16 characters
        try {
            $sql = "INSERT INTO `user`( 
            `login`, `email`, `hash`, `salt`, `id_status`, `password_form`, `name`, `surname`) 
            VALUES (
            :login, :email, :hash, :salt, :id_status, :password_form, :name, :surname)";
            //hash password
            $hashedPassword = hash('sha512', $salt . $password);

            $data = [
                'login' => $login,
                'email' => $email,
                'hash' => $hashedPassword,
                'salt' => $salt,
                'id_status'=>'1',
                'password_form'=>'1',
                'name' => 'changeLater',
                'surname' => 'changeLater'
            ];
            $this->pdo->prepare($sql)->execute($data);
        } catch (Exception $e) {
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
            $sql="SELECT user.id,login,sms_code,code_timelife,role_name FROM user INNER JOIN user_role on user.id = user_role.id_user INNER JOIN role ON user_role.id_role = role.id WHERE login=:login";
            $stmt= $this->pdo->prepare($sql);
            $stmt->execute(['login'=>$login]);
            $user_data=$stmt->fetch();
            if($code==$user_data['sms_code'] && time()< strtotime($user_data['code_timelife'])){
                // Set session variables for successful login
                $_SESSION['logged_in'] = true;
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
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            if (isset($_SESSION['session_expiration']) && time() > $_SESSION['session_expiration']) {
                session_unset();
                session_destroy();
                exit();
            } else {
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
    
    
}