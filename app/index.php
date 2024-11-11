<?php
session_start();  // Initialize session at the beginning of the script
include_once "classes/Page.php";
include_once "classes/Pdo_.php";
Page::display_header("Main page");
$pdo=new Pdo_("mysql-db", "root", "rootpass", "mydb");
// Check if the session is active
// $sessionValid = $pdo->check_session_expiration();
// if (!$sessionValid) {
//     echo "<p>Your session has expired or you are not logged in. Please log in below:</p>";
//     // Display the login and registration forms since the session is either invalid or expired
// }

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

$pdo->check_session_expiration();

// Adding new user
if (isset($_REQUEST['add_user'])) {
    $login = $_REQUEST['login'];
    $email = $_REQUEST['email'];
    $password = $_REQUEST['password'];
    $password2 = $_REQUEST['password2'];
    if ($password==$password2){
        $pdo->add_user($login, $email, $password);
    } else {
        echo 'Passwords doesn\'t match';
    }
}
// Login
if (isset($_REQUEST['log_user_in'])) {
    $login = $_REQUEST['login'];
    $password = $_REQUEST['password'];
    $pdo->log_user_in($login, $password);
}
// Change passowrd
if (isset($_REQUEST['change_password'])){
    $login = $_REQUEST['login'];
    $old_password = $_REQUEST['old_password'];
    $new_password = $_REQUEST['new_password'];
    $new_password2 = $_REQUEST['new_password2'];
    if( $new_password != $new_password2){
        echo "Passwords doesn\'t match";
    }else{
        $pdo->change_password($login, $old_password, $new_password);
    }
}
// Login 2FA
if (isset($_REQUEST['log_user_in_2FA'])) {
    $login = $_REQUEST['login'];
    $password = $_REQUEST['password'];
    $pdo->log_2F_step1($login, $password);
}
// Log user in with 2FA
if (isset($_REQUEST['verify_user'])) {
    $code = $_REQUEST['code'];
    $login = $_SESSION['login'];
    if( $pdo->log_2F_step2($login, $code) ){
        echo 'You are logged in as: '.$_SESSION['login'];
        $_SESSION['logged'] = 'YES';
    }
}
?>
    <h2> Main page</h2>
    <hr/>
    <p> Register new user</p>
    <form method="post" action="index.php">
        <table>
            <tr>
                <td>login</td>
                <td>
                    <label for="name"></label>
                    <input required type="text" name="login" id="login" size="40"/>
                </td>
            </tr>
            <tr>
                <td>email</td>
                <td>
                    <label for="name"></label>
                    <input required type="text" name="email" id="email" size="40"/>
                </td>
            </tr>
            <tr>
                <td>password</td>
                <td>
                    <label for="name"></label>
                    <input required type="text" name="password" id="password" size="40"/>
                </td>
            </tr>
            <tr>
                <td>repeat password</td>
                <td>
                    <label for="name"></label>
                    <input required type="text" name="password2" id="password2" size="40"/>
                </td>
            </tr>
        </table>
        <input type="submit" id= "submit" value="Create account" name="add_user">
    </form>
    <hr>
    <p> Log in</p>
    <form method="post" action="index.php">
    <table>
        <tr>
            <td>login</td>
            <td>
                <label for="name"></label>
                <input required type="text" name="login" id="login" size="40"/>
            </td>
        </tr>
        <tr>
            <td>password</td>
            <td>
                <label for="name"></label>
                <input required type="text" name="password" id="password" size="40" />
            </td>
        </tr>
    </table>
    <input type="submit" id= "submit" value="Log in" name="log_user_in_2FA">
    </form>
    <form method="post" action="index.php">
    <table>
        <tr>
            <td>Code</td>
            <td>
                <label for="code"></label>
                <input required type="text" name="code" id="code" size="40" value=""/>
            </td>
        </tr>
    </table>
    <input type="submit" id="submit" value="Verify" name="verify_user">
    </form>


    <hr>
    <p>Change Password</p>
    <form method="post" action="index.php">
        <table>
            <tr>
                <td>Login</td>
                <td><input required type="text" name="login" size="40"/></td>
            </tr>
            <tr>
                <td>Current Password</td>
                <td><input required type="password" name="old_password" size="40"/></td>
            </tr>
            <tr>
                <td>New Password</td>
                <td><input required type="password" name="new_password" size="40"/></td>
            </tr>
            <tr>
                <td>Repeat New Password</td>
                <td><input required type="password" name="new_password2" size="40"/></td>
            </tr>
        </table>
        <input type="submit" value="Change Password" name="change_password">
    </form>
    <?php
        Page::display_navigation();
    ?>
    </body>
</html>