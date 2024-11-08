<?php
require './htmlpurifier-4.15.0/library/HTMLPurifier.auto.php';
class Pdo_
{
    private $pdo;
    private $purifier;

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
            $sql = "INSERT INTO `user`( `login`, `email`, `hash`, `salt`, `id_status`, `password_form`) VALUES (:login, :email, :hash, :salt, :id_status, :password_form)";
            //hash password
            $hashedPassword = hash('sha512', $salt . $password);

            $data = [
                'login' => $login,
                'email' => $email,
                'hash' => $hashedPassword,
                'salt' => $salt,
                'id_status'=>'1',
                'password_form'=>'1'
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
}